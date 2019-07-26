<?php
require_once 'class.logger.php';
require_once 'class.ncapputil.php';
class winnermanager{
	
	private $conn;
	private $winnertable;
	
	function __construct(){
		$this->_prepareDB();
	}
	
	public function getWinnerByWid($wid){
		$conn = $this->getConnection();
		$query = "SELECT * FROM `".$this->winnertable."` WHERE wid = ?";
		return $conn->GetRow($query, array($wid));
	}
	
	public function getWinnerByUid($uid, $maxitem = 100){
		$conn = $this->getConnection();
		$query = "SELECT * FROM `".$this->winnertable."` WHERE uid = ? ORDER BY wid DESC LIMIT ?";
		return $conn->GetArray($query, array($uid, $maxitem));
	}
	
	public function setWinner($uid, $gid){
		$conn = $this->getConnection();
		$query = "INSERT INTO `".$this->winnertable."` (uid, gid) VALUES ( ? , ? )";
		$conn->Execute($query, array($uid, $gid));
		return $this->conn->Insert_ID();
	}
	
	//custom
	public function getWinners($reverse=true){
		$conn = $this->getConnection();
		$query = "SELECT c.wid, c.uid, c.gid, c.name, c.tt, d.fbid FROM ( SELECT b.wid, b.uid, b.gid, b.tt, a.name FROM `".$this->winnertable."` b JOIN `".DB_PREFIX."__users"."` a ON b.uid = a.uid ) c JOIN `".DB_PREFIX."__users_fbid"."` d ON c.uid = d.uid ORDER BY c.tt ";
		if($reverse){
			$query .="DESC";
		}else{
			$query .="ASC";
		}
		return $conn->getArray($query);
	}
	
	private function getConnection(){
		if(!$this->conn){
			$this->conn = ncapputil::getConnection();
		}
		return $this->conn;
	}
	
	private function _prepareDB(){
		if(defined("DB_WINNER")){
			$this->winnertable = DB_WINNER;
		}else{
			//generate table if DB_PREFIX is exist
			if(defined("DB_PREFIX")){
				$this->winnertable = DB_PREFIX."__winners";
			}else{
				die("either DB_PREFIX or DB_WINNER need to be defined");
			}
		}
		
		$this->_tablegenerate();
	}
	
	private function _tablegenerate(){
		$conn = $this->getConnection();		
		$query = "CREATE TABLE IF NOT EXISTS `".$this->winnertable."` (
				  `wid` int(20) NOT NULL AUTO_INCREMENT,
				  `uid` int(20) NOT NULL,
				  `gid` int(20) NOT NULL,
				  `tt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  PRIMARY KEY (`wid`),
				  KEY `uid` (`uid`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
			$conn->execute($query);
	}
	
}