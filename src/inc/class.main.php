<?php
require_once 'libs/class.bcw_clientip.php';
require_once 'libs/class.bcw_useragent.php';
require_once 'libs/class.ncapputil.php';
require_once 'libs/class.logger.php';
require_once 'config.php';
require_once 'libs/class.usermanager.php';
require_once 'libs/class.drawtry.php';
require_once 'libs/class.fbmanager.php';
require_once 'libs/class.fbfriendmanager.php';
require_once 'libs/class.referralmanager.php';
require_once 'libs/class.winnermanager.php';
require_once 'libs/class.phpmailer.php';
require_once 'libs/class.nc_analytic.php';
require_once 'libs/class.passwordmanager.php';
class main{

	function __construct(){

	}

	private $cache = NULL;

	public function getCache(){
		if(!$this->cache){
			$this->cache = new Memcached;
			$this->cache->addServer('localhost', 11211) or die ("Cannot connect to Memcache");
		}
		return $this->cache;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////MANAGER CLASS
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	private function getUM(){	if(!$this->UM){		$this->UM = new usermanager();}			return $this->UM;}
	private function getRM(){	if(!$this->RM){		$this->RM = new referralmanager();}		return $this->RM;}
	private function getFB(){	if(!$this->FB){		$this->FB = new fbmanager(array());}			return $this->FB;}
	private function getFM(){	if(!$this->FM){		$this->FM = new fbfriendmanager();}		return $this->FM;}
	private function getDrawManager(){	if(!$this->drawManager){	$this->drawManager = new drawtry();}	return $this->drawManager; }
	private function getWinnerManager(){	if(!$this->winnerManager){	$this->winnerManager = new winnermanager();}	return $this->winnerManager;}

	public function getQueryPath(){
		$url_parts = parse_url($_SERVER['REQUEST_URI']);
	    $request = $url_parts["path"];
	    return explode("/", trim($request, "/"));
	}

	public function getCurrentStage(){
		$cur_ts = time();
		$vote_start_ts = strtotime(VOTE_START);
		$vote_end_ts = strtotime(VOTE_CLOSE);

		$status = "";
		if($cur_ts < $vote_start_ts){
			//before vote
			$status = "before_vote";
		}else if($cur_ts >= $vote_end_ts){
			//after vote
			$status = "after_vote";
		}else{
			//during vote
			$status = "in_vote";
		}

		return array(
			"status"=>$status,
			"cur_ts"=>$cur_ts,
			"vote_start_ts"=>$vote_start_ts,
			"vote_end_ts"=>$vote_end_ts,
			"winner_announced"=>WINNER_ANNOUNCED
		);
	}

	public function getSubmission($pagesize=12, $pageindex=1, $sort="popular"){
		$conn = ncapputil::getConnection();

		$query = "SELECT a.*, b.name as `designer_name`, b.first_name, b.last_name, b.student_id, b.age, b.ig_handle, b.faculty, b.course, b.year, b.profile_image FROM `".DB_SUBMISSION."` a LEFT JOIN `".DB_DESIGNER."` b ON a.designer_id = b.id WHERE hidden = 0";

		switch($sort){
			case "recent":
				$query .= " ORDER BY a.tt DESC, a.id ASC";
			break;
			case "designer":
				$query .= " ORDER BY b.name, a.artwork_name ASC";
			break;
			case "title":
				$query .= " ORDER BY a.artwork_name ASC";
			break;
			case "popular":
			default:
				$query .= " ORDER BY a.number_of_vote DESC, a.id ASC";
			break;
		}

		$query .= " LIMIT ".($pageindex-1)*$pagesize.", ".$pagesize;
		$data = $conn->GetArray($query);

		//get total page
		$query = "SELECT COUNT(*) FROM `".DB_SUBMISSION."` WHERE hidden = 0";
		$total = $conn->GetOne($query);

		$totalpage = (int) ceil($total/$pagesize);

		//get submission item
		foreach($data as $key=>$value){
			$data[$key]["items"] = $this->getSubmissionItem($value["id"]);
		}

		return array(
			"data"=>$data,
			"pages"=>$totalpage,
			"cpage"=>(int)$pageindex
		);
	}

	public function getVoted($user_id, $type="today"){
		$conn = ncapputil::getConnection();

		$query = "SELECT submission_id FROM `".DB_VOTE."` WHERE user_id = ?";
		switch($type){
			case "overall":
			break;
			case "today":
			default:
				$query .= " AND DATE(tt) = DATE(NOW())";
			break;
		}

		return $conn->GetCol($query, array($user_id));
	}

	public function checkIsVotedToday($user_id, $submission_id){
		$conn = ncapputil::getConnection();

		$query = "SELECT COUNT(*) FROM `".DB_VOTE."` WHERE user_id = ? AND submission_id = ? AND DATE(tt) = DATE(NOW())";
		return $conn->GetOne($query, array($user_id, $submission_id)) ? true : false;
	}

	public function vote($user_id, $submission_id){
		$conn = ncapputil::getConnection();

		$query = "INSERT INTO `".DB_VOTE."` ( user_id, submission_id ) VALUES ( ?, ? )";
		$conn->Execute($query, array($user_id, $submission_id));

		$this->updateNumberOfVote($submission_id);
	}

	public function updateNumberOfVote($submission_id){
		$conn = ncapputil::getConnection();

		$query = "SELECT COUNT(*) FROM `".DB_VOTE."` WHERE submission_id = ?";
		$total = $conn->GetOne($query, array($submission_id));

		//update
		$query = "UPDATE `".DB_SUBMISSION."` SET number_of_vote = ? WHERE id = ?";
		$conn->Execute($query, array($total, $submission_id));
	}

	public function submission_view($submission_id){
		$conn = ncapputil::getConnection();

		$query = "UPDATE `".DB_SUBMISSION."` SET number_of_view = number_of_view + 1 WHERE id = ?";
		$conn->Execute($query, array($submission_id));
	}

	public function getSubmissionItem($submission_id){
		$conn = ncapputil::getConnection();

		$query = "SELECT id, image_url FROM `".DB_SUBMISSION_ITEM."` WHERE submission_id = ? ORDER BY id ASC";
		return $conn->GetArray($query, array($submission_id));
	}

	public function getSubmissionDetail($submission_id){
		$conn = ncapputil::getConnection();

		$query = "SELECT a.*, b.name as `designer_name`, b.faculty, b.first_name, b.last_name, b.student_id, b.age, b.ig_handle, b.course, b.year, b.profile_image FROM `".DB_SUBMISSION."` a LEFT JOIN `".DB_DESIGNER."` b ON a.designer_id = b.id WHERE a.id = ?";
		$data = $conn->GetRow($query, array($submission_id));

		$data["items"] = $this->getSubmissionItem($submission_id);

		return $data;
	}

	public function getSubmissionByUniqueCode($unique_code){
		$conn = ncapputil::getConnection();

		$query = "SELECT id FROM `".DB_SUBMISSION."` WHERE unique_code = ?";
		return $conn->GetOne($query, array($unique_code));
	}

	public function GotWinners(){
		$conn = ncapputil::getConnection();

		$query = "SELECT COUNT(*) FROM `".DB_SE_WINNER_TOP3."`";
		return $conn->GetOne($query);
	}

	public function get_se_winners(){
		$conn = ncapputil::getConnection();

		$query = "SELECT `index`, unique_code, submission_id FROM `".DB_SE_WINNER_TOP3."` ORDER BY `index` DESC";
		$top3 = $conn->GetArray($query);

		$top1_obj = NULL;
		$top2_obj = NULL;
		$top3_obj = NULL;

		foreach($top3 as $key=>$value){
		  if($value["index"] == 1){
		  	$top1_obj = $this->getSubmissionDetail($value["submission_id"]);
		  }else if($value["index"] == 2){
		  	$top2_obj = $this->getSubmissionDetail($value["submission_id"]);
		  }else if($value["index"] == 3){
		    $top3_obj = $this->getSubmissionDetail($value["submission_id"]);
		  }
		}

		$query = "SELECT submission_id FROM `".DB_SE_WINNER."`";
		$winners = $conn->GetCol($query);

		$winners_obj = array();
		foreach($winners as $key=>$value){
			$winners_obj[] = $this->getSubmissionDetail($value);
		}

		return array(
			"top1"=>$top1_obj,
			"top2"=>$top2_obj,
			"top3"=>$top3_obj,
			"others"=>$winners_obj
		);
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////FRIEND MANAGER
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function setFriends($uid, $friends){	$this->getFM()->updateFriends($uid, $friends);}
	public function getFriends($uid){			$this->getFM()->getFriends($uid);}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////USER & AUTHENTICATION
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function getUserObj($refresh = false){	
		if(!$this->userObj || $refresh){
			$this->userObj = $this->getUM()->getUserObjByUid($this->getUID());
		}	
		return $this->userObj;
	}
	//============================================================================================================
	public function getFBID(){	if($this->fbid == NULL){	$this->fbid = $this->getFB()->getFbid();}	return $this->fbid;	}
	public function clearFBID(){ $this->fbid = 0; }
	//============================================================================================================
	public function getUID(){
		if(!$this->uid){
			if($this->getFBID()){
				//if facebook signed_request exist, get uid by fbid
				$this->userObj = $this->getUM()->getUserObjByFBID($this->getFBID());
				$this->uid = ( $this->userObj && isset($this->userObj["uid"]) )? $this->userObj["uid"] : NULL;
			}else if(isset($_POST["access_key"]) && $_POST["access_key"]){
				//if access_key exist, get uid by nc_access_key
				$this->uid= $this->getUM()->getUIDByAccessKey($_POST["access_key"]);
				$this->userObj = $this->getUM()->getUserObjByUid($this->uid); 
			}
		}
		return $this->uid;
	}

	function getUIDByKeyValue($field, $value){
		return $this->getUM()->getUIDByKeyValue($field, $value);
	}
	//============================================================================================================
	/*
	authenticate user; if not in DB, mines user data
	returns
		status <int>
			0: invalid user
			1: OK
			2: FB error
		msg <string>
	*/
	public function fbAuth(){
	  	$result = $this->getFB()->fbAuth();
		//$this->isFan = isset($result["data"]["isFan"]) && $result["data"]["isFan"] ? 1 : 0 ;
		//all friend data 
		if($result["status"] == 1){
			$this->allfriendlist = $result["data"]["allFriends"];
		}
		return $result;
	}
	//============================================================================================================
	public function getLoginURL($redirecturi){
		return $this->getFB()->getLoginURL($redirecturi,APP_PERM);
	}
	
	public function setInvite($invitor, $invitee, $periodIndex, $mode){
		$this->getRM()->insertFBInvitation($invitor, $invitee, $periodIndex);
	}
	
	public function setShare($uid, $postid, $periodIndex){
		$this->getRM()->insertFBShare($uid, $postid, $periodIndex);
	}
	
	public function successReferral($invitee, $invitee_fbid = 0, $referral=0, $channel='', $periodIndex = 0){
		$invitor = $this->getUM()->getUserObjByUid($referral);
		if(!$invitor){
			$referral = 0;
		}
		$conn = ncapputil::getConnection();
		$affected_uids = $this->getRM()->successReferral($invitee, $invitee_fbid, $referral, $channel, $periodIndex); //return structure changed.
		$user2 = $this->getUM()->getUserObjByUid($invitee);
		foreach($affected_uids as $key=>$value){
			//tracking code in
			$user1 = $this->getUM()->getUserObjByUid($value["uid"]);
			nc_analytic::send(array(
				"appname"=>STATUS == "live" ? APP_NAMESPACE : APP_NAMESPACE."_staging",
				"method"=>"userRef",
				"param"=>array(
					"user1"=>array(
								"fbid"=>$user1["fbid"],
								"email"=>$user1["email"]
							),
					"user2"=>array(
								"fbid"=>$user2["fbid"],
								"email"=>$user2["email"]
							),
					"via"=>$value["channel"]
				)
			));
			$this->setNotification($invitee,$value["uid"], $periodIndex);
			$this->setReverseNotification($invitee,$value["uid"], $periodIndex);
			//send app notification
			$this->sendAppNotificationAsync($user1["fbid"],"Jolly good! You have earned an additional chance from @[".$user2["fbid"]." to juggle Doughby!", "?via=appnotification");
			//$this->sendAppNotification($user1["fbid"],"@[".$user2["fbid"]."] has just earned you an additional chance to unlock Fortune’s Favour!", "?via=appnotification");
			$this->sendEmailAsync($value["uid"],$invitee);
		}

	}
	
	public function updateUserByFBdata($userinfo, $fbid, $uid){
		return $this->getUM()->updateUserByFBdata($this->getUserObj(), $userinfo, $fbid, $uid);
	}
	public function updateUser($data){
		$this->getUM()->updateUser($data);
	}
	public function trackUserData(){ /* only send tracking data when user particular update, etc first login / update particular  */
		$userObj = $this->getUserObj();
		if($userObj){
			nc_analytic::send(array(
				"appname"=>STATUS == "live" ? APP_NAMESPACE : APP_NAMESPACE."_staging",
				"method"=>"userData",
				"param"=>array(
					"name"=>$userObj["name"],
					"email"=>$userObj["email"],
					"fbid"=>$userObj["fbid"],
					"phone"=>$userObj["phone"],
					"ic"=>$userObj["ic"],
					"gender"=>$userObj["sex"],
					"age"=>$userObj["age"],
					"country"=>$userObj["country"],
					"pdpa"=>$userObj["pdpa"] == 1 ? "accept":"deny",
					"ip"=>$userObj["ip"]
				)
			));
		}
	}
	public function trackUserAction($action){ /* for tracking current user activity */
		$userObj = $this->getUserObj();
		if($userObj){
			nc_analytic::send(array(
				"appname"=>STATUS == "live" ? APP_NAMESPACE : APP_NAMESPACE."_staging",
				"method"=>"userAction",
				"param"=>array(
					"fbid"=>$userObj["fbid"],
					"email"=>$userObj["email"],
					"action"=>$action
				)
			));
		}
	}
	public function getCurrentPeriodIndexExact(){
		$cur_index = 0;
		$weekprizedata = perioddata();
		foreach($weekprizedata as $key=>$value){
			if(time() >= strtotime($value["start"]) && time() < strtotime($value["end"])){
				$cur_index = $value["id"];
			}
		}
		return $cur_index;
	}
	public function getCurrentPeriodIndex(){
		$cur_index = 0;
		if(SOCIAL_SHARING_PERIOD){
			$weekprizedata = perioddata();
			foreach($weekprizedata as $key=>$value){
				if(time() >= strtotime($value["start"]) && time() < strtotime($value["end"])){
					$cur_index = $value["id"];
				}
			}
		}
		return $cur_index;
	}

	public function sendResetEmail($receiver, $content){
		
		return $this->sendEmail($receiver, "Password Reset", $content, __dir__."/email/passwordreset.html",'',false);
	}

	//============================================================================================================
	public function sendEmail($receiver, $title, $content, $emailtemplate, $bcc='', $withIncludeEmail=false, $attachment=false){
		$mail = new PHPMailer();
		$mail->CharSet = 'UTF-8';
		$mail->Encoding = 'quoted-printable';
		if(!USE_LOCAL_DKIM){
			$mail->IsSMTP(); 
			$mail->SMTPAuth = true;
			$mail->Host = AWS_SMTP_ENDPOINT;
			$mail->Port = AWS_PORT;
			$mail->SMTPSecure = 'tls';
			$mail->Username = AWS_SMTP_USERNAME;
			$mail->Password = AWS_SMTP_PASSWORD;
		}
		$mail->From = SENDER_EMAIL;
		$mail->FromName = SENDER_NAME;
		$trackingEmail = array();
		if($withIncludeEmail && INCLUDE_EMAIL){
			$default_emails = explode(",",INCLUDE_EMAIL);
			foreach($default_emails as $value){
				$trackingEmail[] = $value;
				$mail->AddBCC($value);	
			}
		}
		if($bcc!=''){
			$additional_emails = explode(",",$bcc);
			foreach($additional_emails as $value){
				$trackingEmail[] = $value;
				$mail->AddBCC($value);				
			}
		}
		$mail->AddAddress($receiver['email'], $receiver['name']);
		$mail->Subject = $mail->Subject = '=?utf-8?B?'.base64_encode($title).'?=';
		
		$mail->isHTML(true);
		$mail->MsgHTML($this->parse_email_template($emailtemplate, $content, true));
		if($attachment){
			if(is_array($attachment)){
				foreach($attachment as $key=>$value){
					$mail->AddAttachment($value);	
				}
			}else{
				$mail->AddAttachment($attachment);
			}
		}
		if(USE_LOCAL_DKIM){
			$mail->DKIM_domain = DKIM_DOMAIN;
			$mail->DKIM_private = __dir__."/".DKIM_PRIVATE_FILENAME; //path to file on the disk.
			$mail->DKIM_selector = DKIM_SELECTOR;// change this to whatever you set during step 2
			$mail->DKIM_passphrase = "";
			$mail->DKIM_identifier = $mail->From;
		}
				
		if($mail->Send()) {
			return array("error"=>0, "msg"=>"email sent to ".$receiver["email"]." bcc :".implode(",",$trackingEmail));
		}else{
			return array("error"=>1, "msg"=>$mail->ErrorInfo);
		}
	}

	//============================================================================================================
	private function parse_email_template($t_file, $replace, $return_output = false){
		$fd = @fopen ($t_file, "r") or die(__FILE__." , ". __LINE__. " Can't open file $t_file");
		$content = @fread ($fd, filesize ($t_file)) or 
		die(__FILE__." , ". __LINE__. " Can't open file $t_file");
		@fclose ($fd);
	
		$content = preg_replace_callback("/%%([A-Za-z0-9_ ]+)%%/", function($matches) use ($replace){
			return isset($replace[$matches[1]])?$replace[$matches[1]]:'';
		},$content);

		$content = preg_replace_callback("/%%([A-Za-z0-9_ ]+)%%/", function($matches) use ($replace){
			return isset($replace[$matches[1]])?$replace[$matches[1]]:'';
		},$content);
	
		if ($return_output) {
			return $content;
		}
		else {
			echo $content;
			exit();
		}
	}
}