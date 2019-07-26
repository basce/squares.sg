<?php
/*
------------------------------------------------------------------------------
Usage
------------------------------------------------------------------------------
getGoodieIndex($uid, $goodieId=0, $conditionIndex=0, $priority=true , $numberOfAllowWon=1, $repeatable=false, $premiumPrizeIndex="1", $fbid=0)
$uid = user id in DB_USER
$goodieId : use when limited to certain prizes, accept int or string, etc, use "1,2,3" if multiple value is accepted.
$conditionIndex : str or int, etc, use "1,2,3" if multiple value is accepted. The difference between goodieId and conditionIndex where goodieId is unique in db, however conditionIndex isn't.
$priority : distribute prizes according to priorityIndex
$numberOfAllowWon : number of prizes user allow to win
$repeatable : can same prize won by same user ?
$premiumPrizeIndex : str or int, goodieid of the premium prize, user for prevent certain users to win premium prize
$fbid : facebook user id (optional, use for global black list checking)

------------------------------------------------------------------------------
Changelog
------------------------------------------------------------------------------
6/12/2013 - Cheewei
- using uid rather than fbid now, fbid now become optional and only user for global black list check

28/11/2013 - Wei Li
- auto create drawtry and prizes tables if don't exist
- added alternate prize distribution algorithm: nextwin
- to use nextwin method, in DB_PRIZE, set drawmethod=nextwin and nextwin=0
	
7/11/2013 - cheewei
- added numberofplay in prize table.
- removed the orginal first date, and use the startdate for probability calculation
- inserted a line for updating numberofplay in database when the probability calculation of the specified prize is trigger
- changing the multiplier of the formula as acting as a "buffer solution" to prevent dramatically change on probability number
- reversed the priority order, and remove the break in the hittest loop to make sure every items will calculate the new prob rate.
- add probability log, which can monitor the probability changes over time.
- changed syntax (int) to ceil()

8/6/2014 - cheewei
- nextwin mode is not yet complete.

------------------------------------------------------------------------------
Custom codes
------------------------------------------------------------------------------	
- added force win codes in getGoodieIndex() for development use
- CheckPastUnfinishedTry() set to 5 minutes
*/
require_once 'class.bcw_clientip.php';
require_once 'class.ncapputil.php';
class drawtry{
	
	private $conn;
	private $drawtrytable;
	private $goodielogtable;
	private $goodietable;
	
	function getDrawTryTable(){
		return $this->drawtrytable;
	}
	function __construct(){
		$this->conn = ncapputil::getConnection();
		
		if(defined("DB_DRAW")){
			$this->drawtrytable = DB_DRAW;
		}else{
			//generate table if DB_PREFIX is exist
			if(defined("DB_PREFIX")){
				$this->drawtrytable = DB_PREFIX."__drawtry";
			}else{
				die("either DB_PREFIX or DB_DRAW need to be defined");
			}
		}
		
		if(!defined("DB_PRIZE")) die('Missing Prize Table, DB_PRIZE need to be defined');
		$this->goodietable = DB_PRIZE;
		
		if(defined("DB_PRIZE_LOG")){
			$this->goodielogtable = DB_PRIZE_LOG;
		}else{
			//generate table if DB_PREFIX is exist
			if(defined("DB_PREFIX")){
				$this->goodielogtable = DB_PREFIX."__prizes_prob_log";
			}else{
				die("either DB_PREFIX or DB_PRIZE_LOG need to be defined");
			}
		}
		
		$this->tablegenerate();
	}
	
	function tablegenerate(){
		
		$query = "CREATE TABLE IF NOT EXISTS `".$this->drawtrytable."` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `uid` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `goodieIndex` smallint(6) NOT NULL,
  `status` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `overflow` int(1) NOT NULL DEFAULT '0',
  `ip` varchar(20) NOT NULL,
  `tt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `claim_tt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `goodieIndex` (`goodieIndex`),
  KEY `tt` (`tt`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$this->conn->execute($query);
		
		$query = "
CREATE TABLE IF NOT EXISTS `".$this->goodielogtable."` (
  `lid` int(20) NOT NULL AUTO_INCREMENT,
  `goodieid` int(20) NOT NULL DEFAULT '0',
  `prob` int(20) NOT NULL DEFAULT '0',
  `mod_prob` int(20) NOT NULL DEFAULT '0',
  `user_roll` int(20) NOT NULL DEFAULT '1',
  `tt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`lid`),
  KEY `goodieid` (`goodieid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
	$this->conn->execute($query);
	}
	
	function filterByGoodieID($goodieid){
		return function($test) use($goodieid) {
			$goodieids = explode(",",$goodieid);
			foreach($goodieids as $key=>$value){
				if($test["goodieid"] == trim($value)){
					return true;
				}
			}
			return false;						
		};
	}
	
	function filterByConditionIndex($conditionIndex){
		return function($test) use($conditionIndex) {
			$testconditionIndexs = explode(",",$test["conditionIndex"]);
			foreach($testconditionIndexs as $key=>$value){
				if($conditionIndex == trim($value)){
					return true;
				}
			}
			return false;				
		}; 	
	}
	
	function sortByPriority($a, $b){
		if($a["priorityIndex"] == $b["priorityIndex"]){
			return 0;
		}
		return $a["priorityIndex"] > $b["priorityIndex"] ? -1 : 1;
	}
	
	function generateGoodieIndex($goodieid="", $priority=false){
		//get all prize data
		$query = "SELECT goodieid, maxamount, oneover, amount, numberofplay, startdatetime, enddatetime, conditionIndex, priorityIndex, multiplier FROM ".$this->goodietable." WHERE oneover <> -1";
		$data = $this->conn->GetArray($query);
		
		if($goodieid != "" ){
			$filterfunc = $this->filterByGoodieID($goodieid);
			$data = array_filter($data, $filterfunc);
		}
		
		//shuffle, also apply to same priority index
		shuffle($data);
		
		if($priority){
			//arrange by priority index, lower index have the higher priority
			usort($data, array($this, "sortByPriority"));
		}
		
		//$this->getTotalGamePlay();
		
		$goodieIndex = 0;
		foreach( $data as $key=>$value){
			// update each goodie index
			$gameplaycount = $value["numberofplay"];
			$id = $value["goodieid"];
			$st = strtotime($value["startdatetime"]);
			$et = strtotime($value["enddatetime"]);
			$oo = $value["oneover"];
			$am = $value["amount"];
			$ma = $value["maxamount"];
			$mp = isset($value["multiplier"]) && $value["multiplier"] ? $value["multiplier"] : 1;
			
			if(time() > $st && time() < $et){
				//gameplay for this index increase by 1;
				$query = "UPDATE `".$this->goodietable."` SET numberofplay = numberofplay + 1 WHERE goodieid = ?";
				$this->conn->Execute($query, array($value["goodieid"]));
				
				switch($prob_type){
					default:
						$newProbability = $this->calProbability($ma,$am,$gameplaycount,$oo,$st,$et,time());
						$query = "UPDATE ".$this->goodietable." SET oneover = ? WHERE goodieid = ?";
						$this->conn->Execute($query, array($newProbability, $id));
					
						if($gameplaycount < 100 && $newProbability < 100 && $am < 100){
							$newProbability = 100;
						}
						
						if($newProbability != -1){
							$modProbability = ceil($newProbability/$mp);
						}else{
							$modProbability = $newProbability;
							//-1, record as zero in the log
						}
						$rollnumber = $this->getRollNumber($modProbability);
						if(!$rollnumber){ // only when roll number is 0
							$goodieIndex = $id;
						}
						$this->conn->Execute("INSERT INTO `".$this->goodielogtable."` ( goodieid , prob  , mod_prob, user_roll ) VALUES ( ? , ? , ?, ? )",array($id,$newProbability, $modProbability, $rollnumber));
					break;
				}
			}
		}
		//prevent over distribute
		if($goodieIndex != 0){
			if(!$this->CheckNumberOfPrizeClaimed($goodieIndex)){
				$goodieIndex = 0;
			}
		}
		return $goodieIndex;	
		
	}
	
	
	/*
	getGoodieIndex
	$uid : user_id
	$goodieId : (optional) prizes user allow to win in a number with comma, etc 1,2,3,5 
	$priority : (default: true) lower priority index set in DB have a higher priority to win, etc if user won 2 prizes together, higher priority will be the one won
	$numberOfAllowWon: (default: 1) number of prize user allow to win
	$repeatable: (default: false) whether user can win the same prize
	*/
	function getGoodieIndex($uid, $goodieId="", $priority=true , $numberOfAllowWon=1, $repeatable=false){
		$this->CheckPastUnfinishedTry();
		
		$query = "SELECT goodieIndex, id FROM ".$this->drawtrytable." WHERE uid = ? AND status = 'waiting' ORDER BY tt DESC LIMIT 1";
		$data = $this->conn->GetRow($query, array($uid));
		if(!isset($data["id"])){
			$overflow = 0;
			$prizesWon = $this->getWonPrize($uid);
			if(sizeof($prizesWon) < $numberOfAllowWon ){
				//user still can win other prizes
				
				if($repeatable){
					$filterPrize = array();
				}else{
					//if repeatable prize not allow, add won prizes to filter list					
					$filterPrize = $prizesWon;
				}

				//get goodieId that's allow to win
				//get all goodieid
				if($goodieId){
					$goodieIds = explode(",",$goodieId);
					$goodieids = array_diff($goodieIds, $filterPrize);
					if(sizeof($goodieids)){
						$newTry = $this->generateGoodieIndex(implode(",",$goodieids), $priority);
					}else{
						//goodieid in filterprize. 
						$overflow = 2;
						$newTry = 0;
					}
				}else{
					//not defined by goodieId
					$query = "SELECT goodieid FROM `".$this->goodietable."`";
					$allgoodieid = $this->conn->GetCol($query);
					$goodieids = array_diff($allgoodieid, $filterPrize);
					if(sizeof($goodieids)){
						$newTry = $this->generateGoodieIndex(implode(",",$goodieids), $priority);
					}else{
						//goodieid in filterprize. 
						$overflow = 2;
						$newTry = 0;
					}
				}
				
			}else{
				//won prize more than allow
				$overflow = 2;
				$newTry = 0;
			}
			$id = $this->RecordTry($uid,$newTry, $overflow);
			if($newTry != 0){
				$this->StockReduce($newTry);
			}
			return array("goodieIndex"=>$newTry, "id"=>$id); 
		}else{
			return $data;
		}
		
	}
	
	function RecordTry($uid, $goodieIndex, $overflow=0){
		$query = "INSERT INTO ".$this->drawtrytable." (uid, goodieIndex, status, overflow, ip) VALUES (?,?,?,?,?)";
		$this->conn->Execute($query, array($uid, $goodieIndex, 'waiting', $overflow, bcw_clientip::ip_address()));
		return $this->conn->Insert_ID();
	}
	
	function UpdateTry($uid, $id){
		$query = "SELECT COUNT(*) FROM ".$this->drawtrytable." WHERE uid = ? AND id = ? AND status='waiting'";
		if($this->conn->GetOne($query, array($uid, $id))){
			$query = "UPDATE ".$this->drawtrytable." SET status='claimed', claim_tt = NOW() WHERE id = ?";
			$this->conn->Execute($query, array($id));
			
			$query = "SELECT goodieIndex FROM ".$this->drawtrytable." WHERE id = ?";
			$prizeThatUserWin = $this->conn->GetOne($query, array($id));
			return $prizeThatUserWin;
		}else{
			//record not exist or already been claim.
			return 0;
		}
	}
	
	function isPastWinner($uid){
		$query = "SELECT COUNT(*) FROM ".$this->drawtrytable." WHERE uid = ? AND goodieIndex != 0 AND status = 'claimed'";
		return $this->conn->GetOne($query, array($uid)); 
	}
	
	function getWonPrize($uid){
		$query = "SELECT goodieIndex FROM `".$this->drawtrytable."` WHERE uid = ? AND goodieIndex != 0 AND status = 'claimed'";
		return $this->conn->GetCol($query, array($uid));
	}
	
	function getRollNumber($amount){
		if($amount == -1){
			return 1;
		}else{
			//$amount is oneover (probability)
			$amount = $amount ? $amount : 1; // prevent zero
			return rand(0, $amount-1);
		}
	}
	
	function getTotalGamePlay(){
		$query = "SELECT COUNT(*) FROM ".$this->drawtrytable." WHERE overflow = 0";
		$this->totalgameplay = $this->conn->GetOne($query);
		return $this->totalgameplay;
	}
	
	function calProbability($maxstock, $total_number_prize, $total_game_play, $lastProbability, $gameStartDate, $gameEndDate, $currentTime){
		if($lastProbability == -1){
			return -1;
		}
		$total_time = $gameEndDate - $gameStartDate;
		$total_time_left = $gameEndDate - $currentTime;		
		$total_time_pass = $currentTime - $gameStartDate;
		if($total_time_left <= 0){
			return -1;
		}
		if($total_number_prize == 0){
			return -1;
		}		
		if($total_game_play == 0 || $total_time_pass <= 0){
			return $lastProbability;
		}
		//$modifier = $total_number_prize - $maxstock*$total_time_left/ $total_time;
		//$modifier = $modifier <= 0 ? (1 + abs($modifier)) : (1 / (1 + $modifier));
		$prob = (($total_time_left * $total_game_play)/($total_time_pass * $total_number_prize)*3/4 + $lastProbability/4);
		return $prob;
	}
		
	function CheckNumberOfPrizeClaimed($goodieid){
		$query = "SELECT COUNT(*) FROM ".$this->drawtrytable." WHERE goodieIndex = ? AND status != 'forfeit'";
		$claimedAmount = $this->conn->GetOne($query, array($goodieid));
		
		$query = "SELECT maxamount FROM ".$this->goodietable." WHERE goodieid = ?";
		$maxAmount = $this->conn->GetOne($query, array($goodieid));
				
		return $claimedAmount < $maxAmount;
	}
	
	
	function StockReduce($goodieid){
		$query = "UPDATE ".$this->goodietable." SET amount = amount-1 WHERE goodieid = ?";
		$this->conn->Execute($query, array($goodieid));
	}
	
	function CheckPastUnfinishedTry(){
		$query = "SELECT goodieIndex, id FROM ".$this->drawtrytable." WHERE status = 'waiting' AND tt < ADDDATE(NOW(), INTERVAL -5 MINUTE)";
		$data = $this->conn->GetArray($query);
		foreach($data as $key=>$value){
			$this->conn->StartTrans();
			$query = "UPDATE ".$this->drawtrytable." SET status = 'forfeit' WHERE id = ?";
			$this->conn->Execute($query, array($value['id']));
			if($value['goodieIndex']){	//skip if scratchItem = 0, reduce load
				$query = "UPDATE ".$this->goodietable." SET amount = amount + 1 WHERE goodieid = ?";
				$this->conn->Execute($query, array($value['goodieIndex']));
			}
			$this->conn->CompleteTrans();
		}
	}
}
?>