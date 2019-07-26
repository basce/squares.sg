<?php
/* Notes of Kenny 2015-04-13
 * - $this->uniqueInvite is useless, going to remove this paramter in future 
 * - added $fbshareManager
 * - $inviteManager is extra work, should completely remove in future
 * - setMode not going to use anymore
 * - unuse functions : updateConfirmInvite() , getAffectedUser(), setMode() for PHG project
 *
 *
 * - need class.social_leaderboard.php , currently do in here first.
 */
	
require_once 'class.ncapputil.php';
require_once 'class.fb_invitation.php';
require_once 'class.fb_shares.php';
//require_once 'class.invitation.php';
require_once 'class.logger.php';
class referralmanager{
	
	//private $mode = "FB";
	private $successreferraltable;
	//private $inviteManager;
	private $fbinviteManager;
	private $fbshareManager;
	//private $notuniqueInvite;
	
	function __construct($uniqueInvite = true){
		$this->uniqueInvite = $uniqueInvite;
		$this->_prepareDB();
	}
	
	public function getSuccessReferralTable(){
		return $this->successreferraltable;
	}
	
	/*
	public function setMode($mode){
		$this->mode = $mode;
	}*/
	
	public function insertFBInvitation($invitor, $invitee, $periodIndex=0){
		$this->getFBInvitation()->addFBInvite($invitor, $invitee, $periodIndex);
		if (!$this->isExistedLeaderboard($invitor, $periodIndex)) $this->updateLeaderboard($invitor, $periodIndex);
	}
	
	public function insertFBShare($uid, $postid, $periodIndex=0)
	{
		$this->getFBShares()->addFBShare($uid, $postid, $periodIndex);
		if (!$this->isExistedLeaderboard($uid, $periodIndex)) $this->updateLeaderboard($uid, $periodIndex);
	}
	
	public function successReferral($invitee, $invitee_fbid=0, $referral=0, $channel='', $periodIndex=0)
	{
		$affected_invitor = array();
		if($invitee_fbid)
		{
			//with fbid , check fb_invitation table
			$temp = $this->getFBInvitation()->getInviteByInvitee($invitee_fbid, $periodIndex);
			foreach($temp as $key=>$value):
				if (!$this->isExisted($value["invitor"], $invitee, 'fbinvite', $periodIndex))
				{
					$success = $this->successfulInvite($value["invitor"], $invitee,'fbinvite', $periodIndex);
					if($success){
						$affected_invitor[] = array("uid"=>$value["invitor"], "channel"=>"fbinvite"); /* edited for return the channel */
					}
				}
			endforeach;
		}
		if ($referral)
		{
			//user refer from a link   eg. $channel = email | fbshare | twitter
			if (!$this->isExisted($referral, $invitee, $channel, $periodIndex))
			{
				$success = $this->successfulInvite($referral, $invitee, $channel, $periodIndex);
				if($success){
					if (!in_array($referral, $affected_invitor)) $affected_invitor[] = array("uid"=>$referral, "channel"=>$channel); /* edited for return the channel */
				}
			}
		}
		return $affected_invitor;
	}
	
	private function successfulInvite($invitor, $invitee, $channel, $periodIndex=0){
		$conn = ncAppUtil::getConnection();
		if($invitor == $invitee){
			//invite by himself
			return false;
		}else{
			$query = "INSERT INTO `".$this->successreferraltable."` ( invitor, invitee, channel, periodIndex ) VALUES ( ?, ?, ?, ?)";
			$conn->Execute($query, array($invitor, $invitee, $channel, $periodIndex));
			$this->updateLeaderboard($invitor, $periodIndex);
			return true;
		}
	}
	
	private function isExisted($invitor, $invitee, $channel, $periodIndex=0)
	{
		$conn = ncAppUtil::getConnection();
		//$query = "SELECT COUNT(*) FROM `".$this->successreferraltable."` WHERE invitor = ? AND invitee = ? AND channel = ? AND periodIndex = ?";
		//return $conn->GetOne($query, array($invitor, $invitee, $channel, $periodIndex));
		$query = "SELECT COUNT(*) FROM `".$this->successreferraltable."` WHERE invitor = ? AND invitee = ? AND periodIndex = ?";
		return $conn->GetOne($query, array($invitor, $invitee, $periodIndex));
	}
	
	public function getSuccessfulReferral($invitor){
		$conn = ncAppUtil::getConnection();
		//question: If periodIndex is 2 now, those successful referral in periodIndex 1 still available as "bonus chance earn"?
		$query = "SELECT * FROM `".$this->successreferraltable."` WHERE invitor = ? GROUP BY invitee, periodIndex";
		$result = $conn->GetArray($query, array($invitor));
		return $result;
	}

	public function getSuccessfulReferree($invitee){
		$conn = ncAppUtil::getConnection();
		//question: If periodIndex is 2 now, those successful referral in periodIndex 1 still available as "bonus chance earn"?
		$query = "SELECT * FROM `".$this->successreferraltable."` WHERE invitee = ? GROUP BY invitor, periodIndex";
		$result = $conn->GetArray($query, array($invitee));
		return $result;
	}
	
	public function isExistedLeaderboard($uid, $periodIndex)
	{
		$conn = ncAppUtil::getConnection();
		return $conn->GetOne("SELECT count(*) FROM `".DB_SOCIALLEAD."` WHERE uid = ? AND periodIndex = ?", array($uid, $periodIndex));
	}
	
	public function updateLeaderboard($uid, $periodIndex)
	{
		//check if user data exist, if a unique key is defined, we can use if duplicate mysql statement
		$conn = ncAppUtil::getConnection();
		$query_total = "SELECT COUNT(*) FROM (SELECT id FROM `".$this->getSuccessReferralTable()."` WHERE invitor = ? AND periodIndex = ? GROUP BY invitee) As a";
		$query = "SELECT COUNT(*) FROM `".$this->getSuccessReferralTable()."` WHERE invitor = ? AND periodIndex = ?";
		$total_success = $conn->GetOne($query_total, array($uid, $periodIndex));
		$total_fbinvitations = $conn->GetOne($query." AND channel = 'fbinvite'", array($uid, $periodIndex));
		$total_fbshares = $conn->GetOne($query." AND channel = 'fbshare'", array($uid, $periodIndex));
		$total_email = $conn->GetOne($query." AND channel = 'email'", array($uid, $periodIndex));

		
				//logger::trace($query." GROUP BY invitee");
		if($this->isExistedLeaderboard($uid, $periodIndex))
		{ 
			//data exist update
			$query = "UPDATE `".DB_SOCIALLEAD."` SET total_success = ?, tt = NOW(), success_fbinvitations = ?, success_fbshares = ? , success_email = ? WHERE uid = ? AND periodIndex = ?";
			$conn->Execute($query, array($total_success, $total_fbinvitations, $total_fbshares, $total_email, $uid, $periodIndex));
		}else{
			$query = "INSERT INTO `".DB_SOCIALLEAD."` (total_success, tt, success_fbinvitations, success_fbshares, success_email, uid, periodIndex ) VALUES (?, NOW(), ?, ?, ?, ?,?)";
			$conn->Execute($query, array($total_success, $total_fbinvitations, $total_fbshares, $total_email, $uid, $periodIndex));
		}
	}
	
	/*
	 * These 2 functions seem like same as confirmInsert(), dunno what is the actual purpose, temporary remarked
	 *
	public function updateConfirmInvite($invitee, $invitors, $periodIndex=0){
		$affected_invitor = array();
		foreach($invitors as $key=>$value){
			if($value["invitor"]!=$invitee && !$this->isInvited($value["invitor"], $invitee, $periodIndex)){
				$this->successfulInvite($value["invitor"], $invitee,$value["tt"], $periodIndex);
				$affected_invitor[] = $value["invitor"];
			}
		}	
		return $affected_invitor;
	}
	public function getAffectedUser($invitee, $invitee_fbid=0, $periodIndex=0){
		$invitors_from_fb = array();
		if($invitee_fbid){
			//with fbid , check fb_invitation table
			$temp = $this->getFBInvitation()->getInviteByInvitee($invitee_fbid);
			foreach($temp as $key=>$value){
				$invitors_from_fb[$value["invitor"]] = $value;
			}
		}
		$temp = $this->getInvitation()->getInviteByInvitee($invitee);
		$invitors = array();
		foreach($temp as $key=>$value){
			$invitors[$value["invitor"]] = $value;
		}	
		$invitors = array_merge($invitors, $invitors_from_fb);
		foreach($invitors as $key=>$value){
			if($value["invitor"]!=$invitee && !$this->isInvited($value["invitor"], $invitee, $periodIndex)){
				$affected_invitor[] = $value["invitor"];
			}
		}	
		return $affected_invitor;
	}
	*/
	
	/*private function getInvitation(){
		if(!$this->inviteManager){
			$this->inviteManager = new invitation($this->uniqueInvite);
		}
		return $this->inviteManager;
	}*/
	
	private function getFBInvitation(){
		if(!$this->fbinviteManager){
			$this->fbinviteManager = new fb_invitation($this->uniqueInvite);
		}
		return $this->fbinviteManager;
	}
	
	//Added by Kenny on 13-4-2015
	private function getFBShares(){
		if(!$this->fbshareManager){
			$this->fbshareManager = new fb_shares();	//maybe need pass parameter $this->uniqueInvite in future
		}
		return $this->fbshareManager;
	}
	
	private function _prepareDB(){
		if(defined("DB_REFERAL")){
			$this->successreferraltable = DB_REFERAL;
		}else{
			//generate table if DB_PREFIX is exist
			if(defined("DB_PREFIX")){
				$this->successreferraltable = DB_PREFIX."__referral";
			}else{
				die("either DB_PREFIX or DB_REFERAL need to be defined");
			}
		}
		
		$this->_tablegenerate();
	}
	
	private function _tablegenerate(){
		$conn = ncAppUtil::getConnection();
		$query = "CREATE TABLE IF NOT EXISTS `".$this->successreferraltable."` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `invitor` int(11) NOT NULL,
				  `invitee` int(11) NOT NULL,
				  `channel` varchar(20) NOT NULL,
				  `periodIndex` int(11) NOT NULL,
				  `tt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  PRIMARY KEY (`id`),
				  KEY `invitor` (`invitor`,`invitee`, `periodIndex`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
		$conn->execute($query);
	}
}
	