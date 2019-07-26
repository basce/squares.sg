<?php
require_once 'class.logger.php';
require_once 'class.ncapputil.php';
require_once 'class.bcw_clientip.php';
class usermanager{
	
	private $conn;
	private $usertable;
	private $userFBIDtable;
	
	function __construct(){
		$this->_prepareDB();
	}
	
	public function getUserTable(){
		return $this->usertable;
	}
	
	public function getUserFBIDTable(){
		return $this->userFBIDtable;
	}
	
	/*backward compatible please use getUserObjByUid or getUserObjByFBID for new project*/
	public function getUserObj($fbid){
		return $this->getUserObjByFBID($fbid);
	}
	
	public function getUIDByAccessKey($access_key){
		$conn = $this->getConnection();
		$query = "SELECT uid FROM `".$this->usertable."` WHERE access_key = ?";
		$result =  $conn->GetOne($query, array($access_key));
		return $result && sizeof($result) ? $result : NULL;
	}
	
	public function getUserObjByUid($uid){
		$query = "SELECT * FROM `".$this->usertable."` WHERE uid = ?";
		$result = $this->getConnection()->GetRow($query, array($uid));
		if($result && sizeof($result)){
			$result["fbid"] = $this->getUserFBID($uid);
		}
		
		return $result && sizeof($result) ? $result : NULL;		
	}
	
	public function getUserObjByFBID($fbid){
		$query = "SELECT uid FROM `".$this->userFBIDtable."` WHERE fbid = ?";
		$uid = $this->getConnection()->GetOne($query, array($fbid));
		if($uid){
			return $this->getUserObjByUid($uid);
		}else{
			return NULL;
		}
	}
	
	public function getUserFBID($uid){
		$query = "SELECT fbid From `".$this->userFBIDtable."` WHERE uid = ?";
		return $this->getConnection()->GetOne($query, array($uid));
	}
	
	public function getSocialDiscoveryUsersByFBIDs($fbids, $amount){
		$query = "SELECT b.uid, a.fbid, b.name FROM `".$this->userFBIDtable."` a join `".$this->usertable."` b on a.uid = b.uid WHERE a.fbid IN (".$fbids.") ORDER BY a.uid DESC LIMIT ?";
		return $this->getConnection()->GetArray($query, array($amount));
	}
	
	public function getSocialDiscoveryUsers($amount){
		$query = "SELECT b.uid, a.fbid, b.name FROM `".$this->usertable."` b join `".$this->userFBIDtable."` a on a.uid = b.uid ORDER BY a.uid DESC LIMIT ?";
		return $this->getConnection()->GetArray($query, array($amount));
	}
	
	public function getTotalUsers(){
		$query = "SELECT COUNT(*) FROM `".$this->usertable."`";
		return $this->getConnection()->GetOne($query);
	}
	
	public function bindFBID($user, $fbid){
		$query = "SELECT COUNT(*) FROM `".$this->userFBIDtable."` WHERE uid = ? AND fbid = ?";
		if(!$this->getConnection()->GetOne($query, array($user, $fbid))){
			$query = "INSERT INTO `".$this->userFBIDtable."` ( uid, fbid ) VALUES ( ? , ? )";
			$this->getConnection()->Execute($query, array($user, $fbid));
		}
	}
	
	public function unbindFBID($user){
		$query = "DELETE FROM `".$this->userFBIDtable."` WHERE uid = ?";
		$this->getConnection()->Execute($query, array($user));
	}
	
	public function getUIDByKeyValue($field, $value){
		$query = "SELECT uid FROM `".$this->usertable."` WHERE ? = ?";
		return $this->getConnection()->GetCol($query, array($field, $value));
	}
	
	public function updateUser($data){
		if(!isset($data["uid"])){
			die("uid is undefined for updateUser function.");
		}
		$id = $data["uid"];
		unset($data["uid"]);
		if(sizeof($data)){
			$this->getConnection()->AutoExecute($this->usertable,$data,'UPDATE', "uid = '".$id."'");
		}
	}
	
	public function insertUser($data){
		$this->getConnection()->AutoExecute($this->usertable,$data);
		return $this->getConnection()->Insert_ID();
	}
	
	public function updateUserByFBdata($userinfo, $fbid, $user){
		$parseData = array();
		$parseData["name"] = isset($userinfo[0]["name"])?$userinfo[0]["name"]:"";		
		$parseData["first_name"] = isset($userinfo[0]["first_name"])?$userinfo[0]["first_name"]:"";
		$parseData["last_name"] = isset($userinfo[0]["last_name"])?$userinfo[0]["last_name"]:"";
		$parseData["birthday"] = isset($userinfo[0]["birthday_date"])?$userinfo[0]["birthday_date"]:"";
		$tempage = "";
		if($parseData["birthday"] != ""){
			$birth  = explode("/",$parseData["birthday"]);
			if(sizeof($birth) == 3){
				$tempage = date('Y') - $birth[2];
			}
		}
		$parseData["ip"] = bcw_clientip::ip_address();
		$parseData["age"] = $tempage;
		$parseData["sex"] = isset($userinfo[0]["sex"])?$userinfo[0]["sex"]:"male";
		
		$parseData["fb_email"] = isset($userinfo[0]["email"])?$userinfo[0]["email"]:""; /*have to remove in future*/
		$parseData["email"] = strlen($parseData["fb_email"]) < 50 ? $parseData["fb_email"] : "";
		
		$parseData["access_key"] = $this->getUniqueKey();
		
		if($user){
			//userid exist, update
			$parseData["uid"] = $user;
			$this->updateUser($parseData);
			$this->bindFBID($user, $fbid);
		}else{
			//insert
			$user = $this->insertUser($parseData);
			$this->bindFBID($user, $fbid);
		}
		return $user;
	}
	
	private function getUniqueKey(){
		$searchUnique = true;
		$key = "";
		$conn = getConnection();	
		while($searchUnique){
			$key = ncapputil::getRandomString(50);
			
			if(!$conn->GetOne("SELECT COUNT(*) FROM `".$this->usertable."` WHERE access_key = ?", array($key))){
				$searchUnique = false;
			}
		}
		return $key;
	}
	
	private function getConnection(){
		if(!$this->conn){
			$this->conn = ncapputil::getConnection();
		}
		return $this->conn;
	}
	
	private function _prepareDB(){
		if(defined("DB_USER")){
			$this->usertable = DB_USER;
		}else{
			//generate table if DB_PREFIX is exist
			if(defined("DB_PREFIX")){
				$this->usertable = DB_PREFIX."__users";
			}else{
				die("either DB_PREFIX or DB_USER need to be defined");
			}
		}
		
		if(defined("DB_USERFBID")){
			$this->userFBIDtable = DB_USERFBID;
		}else{
			//generate table if DB_PREFIX is exist
			if(defined("DB_PREFIX")){
				$this->userFBIDtable = DB_PREFIX."__users_fbid";
			}else{
				die("either DB_PREFIX or DB_USERFBID need to be defined");
			}
		}
		
		$this->_tablegenerate();
	}
	
	private function _tablegenerate(){
		$conn = $this->getConnection();		
		$query = "CREATE TABLE IF NOT EXISTS `".$this->usertable."` (
				  `uid` int(20) NOT NULL AUTO_INCREMENT,
				  `fbid` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
				  `access_key` varchar(50) NOT NULL,
				  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
				  `first_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
				  `last_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
				  `sex` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
				  `birthday` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
				  `age` int(11) DEFAULT NULL,
				  `fb_email` varchar(255) NOT NULL,
				  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
				  `phone` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
				  `ic` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
				  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
				  `postal` varchar(10) NOT NULL DEFAULT '',
				  `ip` varchar(50) NOT NULL,
				  `lastc` int(11) NOT NULL DEFAULT '0',
				  `try_count` int(11) NOT NULL DEFAULT '0',
				  `status` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
				  `email_notify` int(1) NOT NULL DEFAULT '1',
				  `tt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				  `first_registed_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  PRIMARY KEY (`uid`),
				  KEY `fbid` (`fbid`),
				  KEY `status` (`status`),
				  KEY `access_key` (`access_key`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
			$conn->execute($query);
			
			$query = "CREATE TABLE IF NOT EXISTS `".$this->userFBIDtable."` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `uid` int(11) NOT NULL,
					  `fbid` varchar(50) COLLATE utf8_bin NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `uid` (`uid`,`fbid`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
			$conn->execute($query);
	}
	
}