<?php
require_once 'class.ncapputil.php';
class fbfriendmanager{

	private $table;
	
	function __construct(){
		$this->_prepareDB();
	}
	
	public function updateFriends($uid,$friends){
		$conn = ncAppUtil::getConnection();
		$query = "INSERT INTO `".$this->table."` ( uid , friends ) VALUES ( ? , ? )  ON DUPLICATE KEY UPDATE friends = ?";
		$conn->Execute($query, array($uid, json_encode($friends, true), json_encode($friends, true)));
	}
	
	public function getFriends($uid){
		$conn = ncAppUtil::getConnection();
		$query = "SELECT friends FROM `".$this->table."` WHERE uid = ?";
		$data = $conn->getOne($query, array($uid));
		return $data ? json_decode($data, true) : array();
	}
	
	public function getFriendsFBIDStr($obj){
		$fbids = array();
		if(sizeof($obj)){
			foreach($obj as $value){
				$fbids[] = $value["id"];
			}
		}
		return implode(",",$fbids);
	}
	
	private function _prepareDB(){
		if(defined("DB_FRIEND")){
			$this->table = DB_FRIEND;
		}else{
			//generate table if DB_PREFIX is exist
			if(defined("DB_PREFIX")){
				$this->table = DB_PREFIX."__friend";
			}else{
				die("either DB_PREFIX or DB_FRIEND need to be defined");
			}
		}
		$this->_tablegenerate();
	}
	
	private function _tablegenerate(){
		$conn = ncAppUtil::getConnection();
		$query = "
				CREATE TABLE IF NOT EXISTS `".$this->table."` (
				  `uid` int(11) NOT NULL,
				  `friends` text COLLATE utf8_bin NOT NULL,
				  `tt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				  UNIQUE KEY `uid` (`uid`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
		$conn->execute($query);
	}
}
?>