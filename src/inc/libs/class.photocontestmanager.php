<?php
require_once 'class.ncapputil.php';
class photocontestmanager{
	
	private $conn = NULL;
	private $t_entry = NULL;
	private $s_order = "";
	private $s_filter = "";
	
	function __construct($entrytable,$conn){
		$this->t_entry = $entrytable;
		$this->conn = $conn;
	}
	
	private function _getConnection(){
		if(!$this->conn){
			try{
				$this->conn = ncapputil::getConnection();
			}catch(Exception $e){
				print_r("DB getConnection not exist :".$e->getMessage());
				exit();
			}
		}
		return $this->conn;
	}
	
	public function order_set($SQLOrderStr){
		$this->s_order = $SQLOrderStr;
	}
	
	public function filter_set($SQLFilterStr){
		$this->s_filter = $SQLFilterStr;
	}
	
	public function entry_insert($fbid, $creator_name, $image, $title, $caption, $initialvotes, $hidden, $fbregion, $fblanguage, $ip){
		$conn = $this->_getConnection();
		$query = "INSERT INTO `".$this->t_entry."` ( fbid, name, photo, title, caption, votes, hidden, region, language, ip ) VALUES ( ? , ? , ? , ? , ? , ? , ? , ? , ? , ?)";
		$conn->Execute($query, array($fbid, $creator_name, $image, $title, $caption, $initialvotes, $hidden, $fbregion, $fblanguage, $ip));
		
		return $conn->Insert_ID();
	}
	
	public function entry_hide($eid, $hide=true){
		$conn = $this->_getConnection();
		$query = "UPDATE `".$this->t_entry."` SET hidden = ? WHERE eid = ?";
		$conn->Execute($query, array($hide?1:0,$eid));
	}
	
	public function entry_voteUpdate($eid, $totalNumberVote){
		$conn = $this->_getConnection();
		$query = "UPDATE `".$this->t_entry."` SET votes = ? WHERE eid = ?";
		$conn->Execute($query, array($totalNumberVote, $eid));
	}
	
	public function entry_viewIncrease($eid){
		$conn = $this->_getConnection();
		$query = "UPDATE `".$this->t_entry."` SET view = view + 1 WHERE eid = ?";
		$conn->Execute($query, array($eid));
	}
	
	public function entry_get($eid){
		$conn = $this->_getConnection();
		$query = "SELECT *, DATE_FORMAT(tt, '%b %d %Y %h:%i%p') AS tt2 FROM `".$this->t_entry."` WHERE eid = ? AND hidden = 0";
		return $conn->GetRow($query, array($eid));
	}
	
	public function entry_getWithHidden($eid){
		$conn = $this->_getConnection();
		$query = "SELECT * FROM `".$this->t_entry."` WHERE eid = ?";
		return $conn->GetRow($query, array($eid));
	}
	
	public function entry_getRankIndex($eid){
		$conn = $this->_getConnection();
		$query = "SELECT rank FROM ( SELECT e.eid as eid, @rank:=@rank+1 as rank FROM `".$this->t_entry."` e, (SELECT @rank:= 0 ) r ".$this->s_filter." ".$this->s_order.") a WHERE eid = ?";
		return $conn->GetOne($query, array($eid));
	}
	
	public function entry_nextId($eid){
		$current_eid_rank = max(0,$this->entry_getRankIndex($eid));
		$totalItems = $this->entries_getTotal();
		$nextRank = $current_eid_rank + 1 > $totalItems ? 1:$current_eid_rank + 1; //go to the first item if it's the last item

		$conn = $this->_getConnection();
		$query = "SELECT eid FROM ( SELECT e.eid as eid, @rank:=@rank+1 as rank FROM `".$this->t_entry."` e, (SELECT @rank:= 0 ) r ".$this->s_filter." ".$this->s_order.") a WHERE rank = ?";
		return $conn->GetOne($query,array($nextRank));
	}
	
	public function entry_previousId($eid){
		$current_eid_rank = max(0,$this->entry_getRankIndex($eid));
		$totalItems = $this->entries_getTotal();
		$previousRank = $current_eid_rank - 1 < 1 ?$totalItems :  $current_eid_rank - 1; //go to the last item if it's the first item

		$conn = $this->_getConnection();
		$query = "SELECT eid FROM ( SELECT e.eid as eid, @rank:=@rank+1 as rank FROM `".$this->t_entry."` e, (SELECT @rank:= 0 ) r ".$this->s_filter." ".$this->s_order.") a WHERE rank = ?";
		return $conn->GetOne($query,array($previousRank));
	}
	
	public function entries_getTotal(){
		$conn = $this->_getConnection();
		$query = "SELECT COUNT(*) FROM `".$this->t_entry."` ".$this->s_filter;
		return $conn->GetOne($query);
	}
	
	public function entries_getPage($pageIndex, $pageSize){
		$conn = $this->_getConnection();
		$query = "SELECT *, DATE_FORMAT(tt, '%b %d %Y %h:%i%p') AS tt2 FROM `".$this->t_entry."` ".$this->s_filter." ".$this->s_order." LIMIT ?, ?";
		return $conn->GetArray($query, array(($pageIndex-1)*$pageSize, $pageSize));
	}
	
}