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
				'app_id' => APP_ID,
				'app_secret'=> APP_SECRET,
				'default_graph_version'=> 'v2.9'
			),
			$options
		);	
	}
	
	public function getFB(){
		if($this->FB == NULL){
			$this->FB = new \Facebook\Facebook([
			  'app_id' => $this->FB_O["app_id"],
			  'app_secret' => $this->FB_O["app_secret"],
			  'default_graph_version' => $this->FB_O["default_graph_version"]
			  //'default_access_token' => '{access-token}', // optional
			]);
		}
		return $this->FB;
	}

	public function getAccessToken(){
		$helperresult = $this->_getAccessToken();
		if($helperresult["accesstoken"]){
			//with access token
			$this->getFB()->setDefaultAccessToken($helperresult["accesstoken"]);
		}

		return $helperresult;
	}

	private function _getAccessToken(){
		$obj = array(
				"accesstoken"=>NULL,
				"error"=>1,
				"msg"=>"",
				"msgAr"=>array()
			);
		//try getting from session
		if(isset($_SESSION["fbToken_exp_".APP_ID]) && time() > $_SESSION["fbToken_exp_".APP_ID]){
			unset($_SESSION["fbToken_exp_".APP_ID]);
			unset($_SESSION["fbToken_".APP_ID]);
		}
		if(isset($_SESSION["fbToken_".APP_ID])){
			$obj["accesstoken"] = (string) $_SESSION["fbToken_".APP_ID];
			$obj["error"] = 0;
			$obj["msg"] = "JsHelper";
			return $obj;
		}

		//js
		try{
			$JSHelper = $this->getFB()->getJavaScriptHelper();
			$accessToken = $JSHelper->getAccessToken();

			if($accessToken){
				$_SESSION["fbToken_".APP_ID] = (string) $accessToken;
				$_SESSION["fbToken_exp_".APP_ID] = $accessToken->getExpiresAt()->getTimeStamp();
			}
		} catch(Facebook\Exceptions\FacebookResponseException $e){
			$obj["msgAr"][] = 'JsHelper Graph returned an error: ' . $e->getMessage();
		} catch(Facebook\Exceptions\FacebookSDKException $e){
			$obj["msgAr"][] = 'JsHelper Facebook SDK returned an error: ' . $e->getMessage();
		}

		//if with access token 
		if(isset($accessToken)){
			$obj["accesstoken"] = (string) $accessToken;
			$obj["error"] = 0;
			$obj["msg"] = "JsHelper";
			return $obj;
		}

		//page tab
		try{
			$pageTabHelper = $this->getFB()->getPageTabHelper();
			$accessToken = $pageTabHelper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e){
			$obj["msgAr"][] = 'pageTabHelper Graph returned an error: ' . $e->getMessage();
		} catch(Facebook\Exceptions\FacebookSDKException $e){
			$obj["msgAr"][] = 'pageTabHelper Facebook SDK returned an error: ' . $e->getMessage();
		}

		//if with access token 
		if(isset($accessToken)){
			$obj["accesstoken"] = (string) $accessToken;
			$obj["error"] = 0;
			$obj["msg"] = "pageTabHelper";
			return $obj;
		}

		//canvas
		try{
			$canvasHelper = $this->getFB()->getCanvasHelper();
			$accessToken = $canvasHelper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e){
			$obj["msgAr"][] = 'canvasHelper Graph returned an error: ' . $e->getMessage();
		} catch(Facebook\Exceptions\FacebookSDKException $e){
			$obj["msgAr"][] = 'canvasHelper Facebook SDK returned an error: ' . $e->getMessage();
		}

		//if with access token 
		if(isset($accessToken)){
			$obj["accesstoken"] = (string) $accessToken;
			$obj["error"] = 0;
			$obj["msg"] = "pageTabHelper";
			return $obj;
		}

		//redirect
		try{
			$redirectHelper = $this->getFB()->getRedirectLoginHelper();
			$accessToken = $redirectHelper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e){
			$obj["msgAr"][] = 'redirectHelper Graph returned an error: ' . $e->getMessage();
		} catch(Facebook\Exceptions\FacebookSDKException $e){
			$obj["msgAr"][] = 'redirectHelper Facebook SDK returned an error: ' . $e->getMessage();
		}

		//if with access token 
		if(isset($accessToken)){
			$obj["accesstoken"] = (string) $accessToken;
			$obj["error"] = 0;
			$obj["msg"] = "pageTabHelper";
			return $obj;
		}

		return $obj;
	}

	public function getSignedRequest(){
		//try get signedRequest from different source
		$jsHelper = $this->getFB()->getJavaScriptHelper();
		$signedRequest = $jsHelper->getSignedRequest();

		if($signedRequest) return $signedRequest;

		$pageTabHelper = $this->getFB()->getPageTabHelper();
		$signedRequest = $pageTabHelper->getSignedRequest();

		if($signedRequest) return $signedRequest;

		$canvasHelper = $this->getFB()->getCanvasHelper();
		$signedRequest = $canvasHelper->getSignedRequest();

		if($signedRequest) return $signedRequest;

		return NULL;
	}
	
	public function getFbid(){ //from signed request
		if($this->fbid == NULL){
			$signedRequestObj = $this->getSignedRequest();
			if($signedRequestObj){
				$this->fbid = $signedRequestObj->getUserId();
			}
		}
		return $this->fbid;
	}
	
	public function getLoginURL($redirecturi, $scope = APP_PERM){
		$redirectHelper = $this->getFB()->getRedirectLoginHelper();
		return $redirectHelper->getLoginUrl($redirecturi, explode($scope,","));
	}
	
	public function publishAction($fbid, $actionname, $objecttype, $ogurl){
		try{
			$token_url = "https://graph.facebook.com/oauth/access_token?" .
						"client_id=" . APP_ID .
						"&client_secret=" . APP_SECRET .
						"&grant_type=client_credentials";
						
			$app_access_token = file_get_contents($token_url);
			$at = explode("=",$app_access_token);

			if(sizeof($at) > 1){
				$appAccessToken = $at[1];
			}else{
				$tempar = json_decode($app_access_token, true);
				$appAccessToken = $tempar["access_token"];
			}

			$obj = array();
			$obj[$objecttype] = $ogurl;
			$result = $this->getFB()->post($fbid."/".APP_NAMESPACE.":".$actionname, $obj, $appAccessToken);
			
			return array("error"=>0, "msg"=>json_encode($result));
		}catch(Exception $e){
			return array("error"=>1, "msg"=>json_encode(array($fbid, $actionname, $objecttype, $ogurl))." ".$e->getMessage());
		}
	}

	public function api($path, $method, $data){
		$accesstoken_check = $this->getAccessToken();
		$accesstoken = isset($data["access_token"]) ? $data["access_token"] : $accesstoken_check["accesstoken"];
		if($accesstoken){
			if($method == "GET"){
				$query = http_build_query($data);
				$response = $this->getFB()->get($path."?".$query, $accesstoken);
				return json_decode($response->getBody(), true);
			}else{
				$response = $this->getFB()->post($path, $data, $accesstoken);
				return json_decode($response->getBody(),true);
			}
		}else{
			return NULL;
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

		if(sizeof($at) > 1){
			$appAccessToken = $at[1];
		}else{
			$tempar = json_decode($app_access_token, true);
			$appAccessToken = $tempar["access_token"];
		}
						   
		$result = $this->getFB()->post($fbid."/notifications", 
							array(
								'href'=>$url,
								'template'=>$msg
							),$appAccessToken
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
		$accesstoken_check = $this->getAccessToken();
		if($accesstoken_check["accesstoken"]){
			try {
				// Proceed knowing you have a logged in user who's authenticated.
				
				$user_request = $this->getFB()->request('GET', '/me', array(
					"fields"=>"id,birthday,email,first_name,locale,gender,last_name,link,name,verified,age_range"
				));

				$afriend_request = $this->getFB()->request('GET', "/me/friends", array("limit"=>5000));

				$batchResponse = $this->getFB()->sendBatchRequest(array("user"=>$user_request, "friends"=>$afriend_request));
				/*
				$me = $this->getFB()->api("me","GET",array(
					"fields"=>"id,birthday,email,first_name,locale,gender,last_name,link,name,verified,age_range"
				));
				*/
				
				foreach($batchResponse->getResponses() as $key=>$response){
					if($response->isError()){
						$error = $response->getThrownException();
						throw new Exception($error->getMessage());
					}else{
						switch($key){
							case "user":
								$me = json_decode($response->getBody(), true);
							break;
							case "friends":
								$afriend_results =json_decode($response->getBody(), true);
							break;
						}
					}
				}
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
				return "";
			  } catch (Exception $e) {
					  logger::trace('fbAuth Error: '.$e->getMessage());
					return array('status'=>2, 'msg'=>$e->getMessage());
			  }
		}else{
			return array(
					"status"=>0,
					"msg"=>"access token missing"
				);
		}
	}
}