<?php
require_once 'class.logger.php';
require_once 'class.ncapputil.php';
class quizmanager{
	
	private $conn;
	private $table_response_log;
	private $table_quiz_result;
	private $table_quiz_log;
	
	function __construct(){
		$this->_prepareDB();
	}
	
	public function getResponseLogTable(){
		return $this->table_response_log;
	}
	
	public function getQuizResultTable(){
		return $this->table_quiz_result;
	}
	
	public function getTypeByQLID($qlid){
		$conn = $this->getConnection();
		$query = "SELECT type FROM `".$this->table_quiz_log."` WHERE qlid = ?";
		return $conn->GetOne($query, array($qlid));
	}
	
	public function getUserPersonality($uid){
		$conn = $this->getConnection();
		$query = "SELECT * FROM `".$this->table_quiz_result."` WHERE uid = ?";
		return $conn->GetArray($query, array($uid));
	}
	
	public function getUserPersonalityByqrid($qrid){
		$conn =$this->getConnection();
		$query = "SELECT * FROM `".$this->table_quiz_result."` WHERE qrid = ?";
		return $conn->GetRow($query, array($qrid));
	}
	
	public function setUserPersonality($uid, $type, $qlid){
		$conn = $this->getConnection();
		$query = "SELECT qrid FROM `".$this->table_quiz_result."` WHERE uid = ? AND type= ?";
		$existingID = $conn->GetOne($query, array($uid, $type));
		if($existingID){
			return $existingID;
		}else{
			$query = "INSERT INTO `".$this->table_quiz_result."` ( uid, type, qlid ) VALUES ( ? , ? , ?)";
			$conn->Execute($query, array($uid, $type, $qlid));
			return $this->conn->Insert_ID();
		}
	}
	
	public function setUserPersonalityResponse($qrid, $agree, $uid){
		$conn = $this->getConnection();
		if($agree == 1){
			$query = "UPDATE `".$this->table_quiz_result."` SET agree = agree+1 WHERE qrid = ?";
		}else{
			$query = "UPDATE `".$this->table_quiz_result."` SET disagree = disagree+1 WHERE qrid = ?";
		}
		$conn->Execute($query, array($qrid));
		$query = "INSERT INTO `".$this->table_response_log."` ( qrid, uid, agree ) VALUES ( ? , ? , ? )";
		$conn->Execute($query, array($qrid, $uid, $agree));
	}
	
	private function getConnection(){
		if(!$this->conn){
			$this->conn = ncapputil::getConnection();
		}
		return $this->conn;
	}
	
	public function getQuizResult($selections, $uid=0){
		/* algorithmn to get the personality */
		$score = 0;
		foreach($selections as $key=>$value){
			if($value == 1){
				$score++;
			}else{
				$score--;
			}
		}
		
		$type = 1;
		if($score < -3 ){
			$type = 1;
		}else if($score < -1){
			$type = 2;
		}else if($score < 2){
			$type = 3;
		}else if($score < 4){
			$type = 4;
		}else{
			$type = 5;
		}
		
		$qlid = $this->insertQuizLog($uid, $type, json_encode($selections));
		return array(
			"qlid"=>$qlid,
			"type"=>$type
		);
	}
	
	public function updateQuizLogUID($qlid, $uid){
		$conn = $this->getConnection();
		$query = "UPDATE `".$this->table_quiz_log."` SET uid = ? WHERE qlid = ?";
		$conn->Execute($query, array($uid, $qlid));
	}
	
	private function insertQuizLog($uid, $type, $selection){
		$conn = $this->getConnection();
		$query = "INSERT INTO `".$this->table_quiz_log."` ( uid, type, selection ) VALUES ( ? , ? , ?)";
		$conn->Execute($query, array($uid ? $uid : 0, $type, $selection));
		return $this->conn->Insert_ID();
	}
	
	private function _prepareDB(){
		if(defined("DB_QUIZ_RESULT")){
			$this->table_quiz_result = DB_QUIZ_RESULT;
		}else{
			//generate table if DB_PREFIX is exist
			if(defined("DB_PREFIX")){
				$this->table_quiz_result = DB_PREFIX."__quiz_result";
			}else{
				die("either DB_PREFIX or DB_QUIZ_RESULT need to be defined");
			}
		}
		
		if(defined("DB_RESPONSE_LOG")){
			$this->table_response_log = DB_RESPONSE_LOG;
		}else{
			//generate table if DB_PREFIX is exist
			if(defined("DB_PREFIX")){
				$this->table_response_log = DB_PREFIX."__response_log";
			}else{
				die("either DB_PREFIX or DB_USERFBID need to be defined");
			}
		}
		
		if(defined("DB_QUIZ_LOG")){
			$this->table_quiz_log = DB_QUIZ_LOG;
		}else{
			//generate table if DB_PREFIX is exist
			if(defined("DB_PREFIX")){
				$this->table_quiz_log = DB_PREFIX."__quiz_log";
			}else{
				die("either DB_PREFIX or DB_QUIZ_LOG need to be defined");
			}
		}
		
		$this->_tablegenerate();
	}
	
	private function _tablegenerate(){
		/*
		$conn = $this->getConnection();		
		$query = "
SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
SET time_zone = \"+00:00\";

--
-- Database: `production`
--

-- --------------------------------------------------------

--
-- Table structure for table `fairmont__quiz_log`
--

CREATE TABLE IF NOT EXISTS `fairmont__quiz_log` (
  `qlid` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `selection` text NOT NULL,
  `tt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fairmont__quiz_result`
--

CREATE TABLE IF NOT EXISTS `fairmont__quiz_result` (
  `qrid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `agree` int(11) NOT NULL,
  `disagree` int(11) NOT NULL,
  `qlid` int(11) NOT NULL,
  `tt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fairmont__response_log`
--

CREATE TABLE IF NOT EXISTS `fairmont__response_log` (
  `rlid` int(11) NOT NULL,
  `qrid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `agree` int(1) NOT NULL DEFAULT '0',
  `tt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fairmont__quiz_log`
--
ALTER TABLE `fairmont__quiz_log`
  ADD PRIMARY KEY (`qlid`);

--
-- Indexes for table `fairmont__quiz_result`
--
ALTER TABLE `fairmont__quiz_result`
  ADD PRIMARY KEY (`qrid`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `fairmont__response_log`
--
ALTER TABLE `fairmont__response_log`
  ADD PRIMARY KEY (`rlid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fairmont__quiz_log`
--
ALTER TABLE `fairmont__quiz_log`
  MODIFY `qlid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fairmont__quiz_result`
--
ALTER TABLE `fairmont__quiz_result`
  MODIFY `qrid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fairmont__response_log`
--
ALTER TABLE `fairmont__response_log`
  MODIFY `rlid` int(11) NOT NULL AUTO_INCREMENT;
		
		"; */
	}
	
}