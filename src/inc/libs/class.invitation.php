<?php
require_once 'class.ncapputil.php';
class invitation{

	private $invitetable;
	
	private $notuniqueInvite;
	
	function __construct($uniqueInvite = true){
		$this->uniqueInvite = $uniqueInvite;
		$this->_prepareDB();
	}
	
	public function addInvite($invitor, $invitee, $periodIndex=0){
		if(!$this->uniqueInvite || !$this->isInvited($invitor, $invitee, $periodIndex) ){
			$conn = ncAppUtil::getConnection();
			$query = "INSERT INTO `".$this->invitetable."` ( invitor, invitee, periodIndex ) VALUES ( ? , ? ,?)";
			$conn->Execute($query, array($invitor, $invitee, $periodIndex));
		}
	}
	
	private function isInvited($invitor, $invitee, $periodIndex=0){
		$conn = ncAppUtil::getConnection();
		$query = "SELECT COUNT(*) FROM `".$this->invitetable."` WHERE invitor = ? AND invitee = ? AND periodIndex = ?";
		return $conn->getOne($query, array($invitor, $invitee, $periodIndex));
	}

	public function getInviteByInvitor($invitor, $periodIndex=0){
		$conn = ncAppUtil::getConnection();
		$query = "SELECT * FORM `".$this->invitetable."` WHERE invitor = ? AND periodIndex = ?";
		return $conn->GetArray($query, array($invitor,$periodIndex));
	}
	
	public function getInviteByInvitee($invitee,$periodIndex=0){
		$conn = ncAppUtil::getConnection();
		$query = "SELECT * FROM `".$this->invitetable."` WHERE invitee = ? AND periodIndex = ?";
		return $conn->GetArray($query, array($invitee,$periodIndex));		
	}
	
	public function getInviteUnique($invitee,$periodIndex=0){
		$conn = ncAppUtil::getConnection();
		$query = "SELECT invitor FROM `".$this->invitetable."` WHERE invitee = ? AND periodIndex =? GROUP BY invitor";
		return $conn->GetArray($query, array($invitee,$periodIndex));
	}
	
	private function _prepareDB(){
		if(defined("DB_INVITE")){
			$this->invitetable = DB_INVITE;
		}else{
			//generate table if DB_PREFIX is exist
			if(defined("DB_PREFIX")){
				$this->invitetable = DB_PREFIX."__invite";
			}else{
				die("either DB_PREFIX or DB_FBINVITE need to be defined");
			}
		}
		$this->_tablegenerate();
	}
	
	private function _tablegenerate(){
		$conn = ncAppUtil::getConnection();
		$query = "CREATE TABLE IF NOT EXISTS `".$this->invitetable."` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `invitor` int(11) NOT NULL,
			  `invitee` int(11) NOT NULL,
			  `periodIndex` int(11) NOT NULL,
			  `tt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`),
			  KEY `invitor` (`invitor`,`invitee`,`periodIndex`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
		$conn->execute($query);
	}
}
?>