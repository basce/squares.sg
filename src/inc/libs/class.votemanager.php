<?php
require_once 'class.ncapputil.php';
class votemanager{
	
	private $conn = NULL;
	private $v_entry = NULL;
	private $i_dailylimit = -1;
	
	function __construct($votetable,$conn, $dailyvotes){
		$this->v_entry = $votetable;
		$this->conn = $conn;
		$this->i_dailylimit = $dailyvotes;
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

	
	public function getSummary($fbid){
		$conn = $this->_getConnection();
		$query = "SELECT COUNT(*) FROM `".$this->v_entry."` WHERE fbid = ?";
		$totalVotes = $conn->GetOne($query, array($fbid));
		
		$query = "SELECT COUNT(*) FROM `".$this->v_entry."` WHERE fbid = ? AND DATE(tt) = DATE(NOW())";
		$todayVotes = $conn->GetOne($query, array($fbid));
		
		if($this->i_dailylimit != -1){
			//votes with daily limit
			return array(
				"total"=>$totalVotes,
				"available"=>max(0, $this->i_dailylimit - $todayVotes)
			);
		}else{
			//always available
			return array(
				"total"=>$totalVotes,
				"available"=>1
			);
		}
	}
	
	public function insert($fbid,$eid){
		$conn = $this->_getConnection();
		$query = "INSERT INTO `".$this->v_entry."` ( fbid , eid ) VALUES ( ? , ? )";
		$conn->Execute($query, array($fbid, $eid));
		
		//get total vote
		$query = "SELECT COUNT(*) FROM `".$this->v_entry."` WHERE eid = ?";
		return $conn->GetOne($query, array($eid));
	}
}