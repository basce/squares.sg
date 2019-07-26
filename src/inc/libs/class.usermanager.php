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
		$query = "SELECT uid FROM `".$this->usertable."` WHERE ".$field." = ?";
		return $this->getConnection()->GetCol($query, array($value));
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
	
	public function updateUserByFBdata($storeUserData, $userinfo, $fbid, $user){
		$parseData = array();
		if($storeUserData && $storeUserData["name"]){
			//don't do anything
		}else{
			$parseData["name"] = isset($userinfo["name"])?$userinfo["name"]:"";
		}
		if(isset($userinfo["first_name"]) && $userinfo["first_name"]){
			if($storeUserData && $storeUserData["first_name"]){
				//don't do anything
			}else{
				$parseData["first_name"] = $userinfo["first_name"];
			}
		}
		if(isset($userinfo["last_name"]) && $userinfo["last_name"]){
			if($storeUserData && $storeUserData["last_name"]){
				//don't do anything
			}else{
				$parseData["last_name"] = $userinfo["last_name"];
			}
		}
		if(isset($userinfo["birthday"]) && $userinfo["birthday"]){
			if($storeUserData && $storeUserData["birthday"]){
				//don't do anything
			}else{
				$parseData["birthday"] = $userinfo["birthday"];
			}
		}
		if(isset($userinfo["locale"]) && $userinfo["locale"]){
			$parseData["locale"] = $userinfo["locale"];
		}
		$tempage = "";
		if(isset($parseData["birthday"])){
			if($parseData["birthday"] != ""){
				$birth  = explode("/",$parseData["birthday"]);
				if(sizeof($birth) == 3){
					$tempage = date('Y') - $birth[2];
				}
			}
			$parseData["age"] = $tempage;
		}
		$parseData["ip"] = bcw_clientip::ip_address();
		$tempcountry = ncapputil::getIPInfo($parseData["ip"], "Country Code");	//added by Kenny
		if($tempcountry){ //only update country if got value
			$parseData["country"] = $tempcountry;
		}
		
		if(isset($userinfo["locale"]) && $userinfo["locale"]){
			$parseData["age_range"] = $userinfo["age_range"];
		}
		if(isset($userinfo["sex"]) && $userinfo["sex"]){
			if($storeUserData && $storeUserData["sex"]){
				//don't do anything
			}else{
				$parseData["sex"] = $userinfo["sex"];
			}
		}
		if(isset($userinfo["fbverified"]) && $userinfo["fbverified"]){
			$parseData["fbverified"] = $userinfo["fbverified"];
		}
		if(isset($userinfo["email"]) && $userinfo["email"]){
			$parseData["fb_email"] = $userinfo["email"];
			if($storeUserData && $storeUserData["email"]){
				//don't do anything
			}else{
				$parseData["email"] = strlen($parseData["fb_email"]) < 50 ? $parseData["fb_email"] : "";
			}
		}
		$parseData["access_key"] = $this->getUniqueKey();
		
		if($user){
			//userid exist, update
			$parseData["uid"] = $user;
			$this->updateUser($parseData);
			$this->bindFBID($user, $fbid);
		}else{
			//insert
			$parseData["status"] = "installed";
			$parseData["tt"] = date("Y-m-d H:i:s");
			$user = $this->insertUser($parseData);
			$this->bindFBID($user, $fbid);
		}
		return $user;
	}
	
	private function getUniqueKey(){
		$searchUnique = true;
		$key = "";
		$conn = $this->getConnection();	
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
		/*
		$conn = $this->getConnection();		
		$query = "CREATE TABLE IF NOT EXISTS `".$this->usertable."` (
				  `uid` int(20) NOT NULL,
				  `access_key` varchar(50) COLLATE utf8mb4_bin NOT NULL,
				  `name` varchar(255) COLLATE utf8mb4_bin NOT NULL,
				  `first_name` varchar(255) COLLATE utf8mb4_bin NOT NULL,
				  `last_name` varchar(255) COLLATE utf8mb4_bin NOT NULL,
				  `sex` varchar(10) COLLATE utf8mb4_bin NOT NULL,
				  `birthday` varchar(20) COLLATE utf8mb4_bin DEFAULT NULL,
				  `age` int(11) DEFAULT NULL,
				  `age_range` varchar(255) COLLATE utf8mb4_bin NOT NULL,
				  `fb_email` varchar(255) COLLATE utf8mb4_bin NOT NULL,
				  `email` varchar(255) COLLATE utf8mb4_bin NOT NULL,
				  `phone` varchar(255) COLLATE utf8mb4_bin NOT NULL,
				  `ic` varchar(100) COLLATE utf8mb4_bin NOT NULL,
				  `address` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
				  `postal` varchar(10) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
				  `locale` varchar(255) COLLATE utf8mb4_bin NOT NULL,
				  `country` varchar(255) COLLATE utf8mb4_bin NOT NULL,
				  `initial_ip` varchar(255) COLLATE utf8mb4_bin NOT NULL,
				  `ip` varchar(50) COLLATE utf8mb4_bin NOT NULL,
				  `lastc` int(11) NOT NULL DEFAULT '0',
				  `try_count` int(11) NOT NULL DEFAULT '0',
				  `status` varchar(50) COLLATE utf8mb4_bin NOT NULL,
				  `fbverified` varchar(50) COLLATE utf8mb4_bin NOT NULL,
				  `fblikeshown` int(1) NOT NULL DEFAULT '1',
				  `email_notify` int(1) NOT NULL DEFAULT '1',
				  `pdpa` int(11) NOT NULL DEFAULT '2' COMMENT '0 deny | 1 accept | 2 na',
				  `tt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `update_tt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
					
					ALTER TABLE `rafflesstories__users`
				  ADD PRIMARY KEY (`uid`),
				  ADD KEY `status` (`status`),
				  ADD KEY `access_key` (`access_key`);
				 
				  ALTER TABLE `rafflesstories__users`
 					 MODIFY `uid` int(20) NOT NULL AUTO_INCREMENT;
				"; */
	}
	
}