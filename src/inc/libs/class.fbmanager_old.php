<?php
require_once 'class.logger.php';
class fbmanager{
	
	private $conn;
	private $FB = NULL;
	private $FB_O = NULL;
	private $fbid = NULL;
	
	public function __construct($options){
		$options = $options ? $options : array();
		$this->FB_O = array_merge(
			/* default */
			array(
				'appId' => APP_ID,
				'secret'=> APP_SECRET,
				'cookie'=> true
			),
			$options
		);	
	}
	
	public function getFB(){
		if($this->FB == NULL){
			$this->FB = new Facebook(array(
			'appId' => $this->FB_O["appId"],
			'secret'=> $this->FB_O["secret"],
			'cookie'=> $this->FB_O["cookie"]
			));
		}
		return $this->FB;
	}
	
	public function getFbid(){
		if($this->fbid == NULL){
			$this->fbid = $this->getFB()->getUser();
		}
		return $this->fbid;
	}
	
	public function getLoginURL($redirecturi, $scope = APP_PERM){
		return $this->getFB()->getLoginUrl(array('scope'=>$scope,'redirect_uri'=>$redirecturi));
	}
	
	public function publishAction($fbid, $actionname, $objecttype, $ogurl){
		try{
			$token_url = "https://graph.facebook.com/oauth/access_token?" .
						"client_id=" . APP_ID .
						"&client_secret=" . APP_SECRET .
						"&grant_type=client_credentials";
						
			$app_access_token = file_get_contents($token_url);
			$at = explode("=",$app_access_token);
			$obj = array();
			$obj['access_token'] = $at[1];
			$obj[$objecttype] = $ogurl;
			$result = $this->getFB()->api($fbid."/".APP_NAMESPACE.":".$actionname, 'POST',$obj);
			
			return array("error"=>0, "msg"=>json_encode($result));
		}catch(Exception $e){
			return array("error"=>1, "msg"=>json_encode(array($fbid, $actionname, $objecttype, $ogurl))." ".$e->getMessage());
		}
	}
	
	public function sendAppNotification($fbid, $msg, $url){
		try{
		$token_url = "https://graph.facebook.com/oauth/access_token?" .
					"client_id=" . APP_ID .
					"&client_secret=" . APP_SECRET .
					"&grant_type=client_credentials";	
					
		$app_access_token = file_get_contents($token_url);
		$at = explode("=",$app_access_token);
						   
		$result = $this->getFB()->api($fbid."/notifications", 'POST',
							array(
								'access_token' => $at[1],
								'href'=>$url,
								'template'=>$msg
							)
						);

			return array("error"=>0, "msg"=>json_encode($result));
		}catch(Exception $e){
			return array("error"=>1, "msg"=>$e->getMessage());
		}
	}
	
	public function fbAuth(){
		$user = $this->getFbid();
		if(!$user){
			return array('status'=>0, 'msg'=>'invalid user, missing accesstoken');
		}
	  	try {
			// Proceed knowing you have a logged in user who's authenticated.
			
			$me = $this->getFB()->api("me","GET",array(
				"fields"=>"id,birthday,email,first_name,locale,gender,last_name,link,name,verified,age_range"
			));
			
			$meObj = array();
			$meObj["name"] = isset($me["name"]) ? $me["name"]:"";
			$meObj["first_name"] = isset($me["first_name"]) ? $me["first_name"]:"";
			$meObj["last_name"] = isset($me["last_name"]) ? $me["last_name"]:"";
			$meObj["birthday"] = isset($me["birthday"]) ? $me["birthday"]:"";	//edit by kenny from "birthday_date" to "birthday"
			$meObj["locale"] = isset($me["locale"]) ? $me["locale"]:"";	//add by kenny
			$meObj["email"] = isset($me["email"]) ? $me["email"]:"";
			$meObj["sex"] = isset($me["gender"]) ? $me["gender"]:"male";
			$meObj["fbverified"] = isset($me["verified"]) ? $me["verified"]:0;
			$meObj["age_range"] = isset($me["age_range"]) ? json_encode($me["age_range"]):"";			
			
			$afriend_results = $this->getFB()->api("me/friends/?limit=5000");
			//$friend_results = $this->getFB()->api("me/invitable_friends/?limit=5000");
			$global_allfriendinfo = $global_friendinfo = isset($afriend_results["data"])?$afriend_results["data"]:array();
			
			return array('status'=>1, 
						'msg'=>'',
						'fbid'=>$user,
						'data'=>array(

								"isFan"=>0,
								"allFriends"=>$global_allfriendinfo,
								"appFriends"=>$global_friendinfo,
								"userInfo"=>$meObj
							)
						);
		  } catch (FacebookApiException $e) {
				  logger::trace('fbAuth Error: '.$e->getMessage());
				return array('status'=>2, 'msg'=>$e->getMessage());
		  }
	}
}