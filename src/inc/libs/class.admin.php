<?php
require_once 'class.bcw_clientip.php';
require_once 'class.ncapputil.php';
require_once 'class.logger.php';
require_once 'class.fbmanager.php';
require_once 'class.passwordmanager.php';
class admin{
	private $conn;
	private $adminTable;
	private $fbm;
	
	function __construct(){
		$this->conn = ncapputil::getConnection();
		
		if(defined("DB_ADMIN")){
			$this->adminTable = DB_ADMIN;
		}else{
			//generate table if DB_PREFIX is exist
			if(defined("DB_PREFIX")){
				$this->adminTable = DB_PREFIX."__admin";
			}
		}
		/* $this->adminTable = isset($obj["table"])?$obj["table"]:$this->adminTable;  */
		$this->generateAdminTable();
		
		if(!$this->conn){
			throw new ErrorException("conn cannot be empty");
		}
		session_start();
	}

	public function getAdminTable(){
		return $this->adminTable;
	}
	
	private function generateAdminTable(){
		//create table if table not exist
		$query = "
		  CREATE TABLE IF NOT EXISTS `".$this->adminTable."` (
			`aid` int(20) NOT NULL AUTO_INCREMENT,
			`username` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
			`email` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
			`password` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
			`token` varchar(20) CHARACTER set utf8 COLLATE utf8_bin NOT NULL,
			`level` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
			PRIMARY KEY (`aid`),
			KEY `username` (`username`)
		  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		 ";
		$query2 = "
		  CREATE TABLE IF NOT EXISTS `".$this->adminTable."_notificationtask` (
			`atid` int(20) NOT NULL AUTO_INCREMENT,
			`aid` int(20) NOT NULL,
			`aname` varchar(255) NOT NULL,
			`number_sent` int(20) NOT NULL DEFAULT 0,
			`msg` varchar(255) NOT NULL,
			`href` varchar(255) NOT NULL,
			`target` varchar(255) NOT NULL,
			`status` varchar(100) NOT NULL,
			`description` varchar(255) NOT NULL DEFAULT '',
			`publishtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
			`starttime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
			`endtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
			`tt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`atid`),
			KEY `aid` (`aid`)
		  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		";
		$query3 = "
		  CREATE TABLE IF NOT EXISTS `".$this->adminTable."_log` (
			`id` int(20) NOT NULL AUTO_INCREMENT,
			`aid` int(20) NOT NULL,
			`aname` varchar(255) NOT NULL,
			`ip` varchar(255) NOT NULL,
			`action` varchar(255) NOT NULL,
			`detail` text,
			`tt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`),
			KEY `aid` (`aid`)
		  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		 ";
		$this->conn->Execute($query);
		$this->conn->Execute("INSERT IGNORE INTO `".$this->adminTable."` (`aid`, `username`, `email`, `password`, `token`, `level`) VALUES
(1, 'cheewei', 'cheewei.yong@noisycrayons.com','ilovedomokun', '0000000000', '2')");
		$this->conn->Execute($query2);
		$this->conn->Execute($query3);		
	}
	
	function getFB(){
		if($this->fbm == NULL){
			$this->fbm = new fbmanager(array());
		}
		return $this->fbm;
	}
	
	function tracelog($str){
		logger::trace($str);
	}
	
	function adminlog($description, $data){
		$query = "INSERT INTO `".$this->adminTable."_log` ( aid, aname, ip, action, detail ) VALUES (? , ? , ?, ? , ?)";
		$currentuser = $this->validToken();
		$this->conn->Execute($query, array($currentuser["aid"], $currentuser["username"], bcw_clientip::ip_address(), $description, json_encode($data)));
	}
	
	function getLast3TaskStatus(){
		$query = "SELECT * FROM `".$this->adminTable."_notificationtask` ORDER BY atid DESC LIMIT 3";
		return $this->conn->GetArray($query);
	}
	
	function addTask($publishtime, $msg, $href, $target){
		$currentuser = $this->validToken();
		if($currentuser){
			//insert class
			$sleeptime = strtotime($publishtime) - time();
			if($sleeptime > 0 ){
				$description = "Task will be executed on ".$publishtime;				
			}else{
				$description = "Task will be executed now";
			}
			
			$query = "INSERT INTO `".$this->adminTable."_notificationtask` ( aid, aname, msg, href, target, description, publishtime ) VALUES ( ? , ? , ? , ? , ? , ?, ?)";
			$this->conn->Execute($query, array($currentuser["aid"], $currentuser["username"], $msg, $href, $target, $description,$publishtime));
			$taskID = $this->conn->Insert_ID();
			//execute at backend
			//
			
			try{
				exec('php '.dirname(dirname( dirname(__FILE__))).'/stateofplay/_adminnotification.php '.$taskID.' 2>&1', $outputanderror, $return_value);
				$this->tracelog($outputanderror);
			}catch(Exception $e){
				$this->tracelog($e->getMessage());
			}
		}
	}
	
	function getTaskDetail($atid){
		$query = "SELECT * FROM `".$this->adminTable."_notificationtask` WHERE atid = ?";
		return $this->conn->GetRow($query, array($atid));
	}
	
	function updateTaskDetail($description, $total, $atid){
		$query = "UPDATE `".$this->adminTable."_notificationtask` SET number_sent = ? , status = ?, description = ?, endtime = UTC_TIMESTAMP() WHERE atid = ?";
		$this->conn->Execute($query, array($total,"complete",$description, $atid));			
	}
	
	function publishNotification($msg, $href, $fbids,$atid){
		
		$this->conn->Execute("UPDATE `".$this->adminTable."_notificationtask` SET starttime = UTC_TIMESTAMP() WHERE atid =?",array($atid));
		
		if(sizeof($fbids)){
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


			}catch(Exception $e){
				$this->tracelog($e->getMessage());
				$at = array();	
				$appAccessToken = "";
			}
			
			if($appAccessToken){
				$counter = 0;
				foreach($fbids as $key=>$value){
					try{
						$result = $this->getFB()->api($value."/notifications", 'POST',
								array(
									'access_token' => $appAccessToken,
									'href'=>$href,
									'template'=>$msg
								)
							);
						$counter++;
						$this->updateTaskDetail($counter."/".sizeof($fbids), $counter, $atid);
						if($counter%20){
							sleep(1);
						}
					}catch(Exception $e){
						$this->tracelog($e->getMessage());
					}
				}
				$error = 0;
				$totalaffected = sizeof($fbids);
				$description = "Total :".sizeof($fbids)." users sent";
			}else{
				$error = 1;
				$totalaffected = 0;
				$description = "cannot retrieve access token, please check your APP_ID and APP_SECRET VALUE.";
			}
			
		}else{
			//no data task end
			$error = 0;
			$totalaffected = 0;
			$description = "no user match the criteria";
			$this->tracelog("no user match the criteria");
		}
		return array(
					"error"=>$error,
					"total"=>$totalaffected,
					"description"=>$description			
					);
	}

	public function checkEmailUnique($email, $uid){
		$conn = ncapputil::getConnection();
		$query = "SELECT uid FROM `".$this->adminTable."` WHERE UPPER(email) = UPPER(?)";
		$exist_uid = $conn->GetOne($query, array(trim($email)));
		if($exist_uid && $exist_uid != $uid){
			return false;
		}else{
			return true;
		}
	}

	public function getAuthToken($uid){
		return passwordmanager::getPasswordRequestLink($uid);
	}

	public function insertAdmin($name, $email, $role=0){
		if($this->checkEmailUnique($email, 0)){
			$conn = ncapputil::getConnection();
			$query = "INSERT INTO `".$this->adminTable."` ( username , email, level ) VALUES (?, ?, ?)";
			$conn->Execute($query, array(trim($name), trim($email), $role));
			$uid = $conn->Insert_ID();

			$currentuser = $this->validToken();

			$this->adminlog($currentuser["username"]." added new user :".$username, func_get_args());
					
			return array(
					"status"=>1,
					"msg"=>"",
					"uid"=>$uid
				);
		}else{
			return array(
					"status"=>0,
					"msg"=>"Email is in used by other account"
				);
		}
	}
	
	function validToken(){
		$prefix = str_replace(array("/","\\"," "),"",dirname(__FILE__));
		$token = isset($_COOKIE[$prefix ."_report_token"])? $_COOKIE[$prefix ."_report_token"] : "";
		if(!$token){
			$token = isset($_SESSION[$prefix ."_report_token"]) ? $_SESSION[$prefix ."_report_token"] : ""; 
		}
		if($token){
			$result = passwordmanager::validToken($token);

			if($result && sizeof($result)){
				$query = "SELECT * FROM `".$this->adminTable."` WHERE aid = ?";
				$data = $this->conn->GetRow($query, array($result["uid"]));
				return array(
					"username"=>$data["username"],
					"aid"=>$data["aid"],
					"level"=>$data["level"]
				);
			}
			$query = "SELECT * FROM ".$this->adminTable." WHERE token = ?";
			$data = $this->conn->GetRow($query, array($token));
			if(sizeof($data)){
				return array(
								"username"=>$data["username"],
								"aid"=>$data["aid"],
								"level"=>$data["level"]
							);
			}else{
				return NULL;
			}
		}else{
			return NULL;
		}
	}
	
	function insertAdminMenuHeader(){
		?>
        <link href='https://fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>
        <!--@Stylesheets-->
		<link rel="stylesheet" type="text/css" href="/common/css/menus.css">
        <link rel="stylesheet" type="text/css" href="/common/css/overall.css">
        <?php
	}
	
	function insertAdminMenuScript(){
		?>
        <!--@Javascripts-->
        <script type="text/javascript" src="/common/js/adminsidemenu.js"></script> 
        <?php
	}
	
	function getAdminmenu($setting, $currentpage){
		/*
		<!-- PUSH MENU LEFT -->
        <div id="push_left" class="push_left_close prl_menu">
            <ul class="list_menu">
                <h2>Push Menu Left</h2>
                <a href="#"><li>Mac & Cheese</li></a>
                <a href="#"><li>Peanut Butter & Jelly</li></a>
                <a href="#"><li>Cookie Jars</li></a>
                <a href="#"><li>Banana Bread</li></a>
                <a href="#"><li>Gummy Bears</li></a>
                <a href="#"><li>Nuttela Cake</li></a>
            </ul>
            <button id="button_push_left" class="button pbutton">
                <img id="bpleft" src="img/menu_icon.png"/>
            </button>
        </div>
        <!-- END PUSH MENU LEFT -->
		*/
		$currentuser = $this->validToken();
		$currentuserlevel = isset($currentuser["level"]) ? $currentuser["level"] : 0;
		?>
        <!-- PUSH MENU LEFT -->
        <div id="push_left" class="push_left_close prl_menu">
            <ul class="list_menu">
                <h2>Menu</h2>
        <?php
		foreach($setting as $key=>$value){
			$allowLevels = explode(",",$value["visible"]);
			if(in_array($currentuserlevel, $allowLevels)){
				?>
                <a href="<?=$value["url"]?>"<?=($value["label"] == $currentpage)?"class=\"active\"":""?>><li><?=$value["label"]?></li></a>
                <?php
			}
		}
		?>
        </ul>
            <button id="button_push_left" class="button pbutton">
                <img id="bpleft" src="/common/img/menu_icon.png"/>
            </button>
        </div>
        <!-- END PUSH MENU LEFT -->
		<?php
	}

	function tableForm($fields){
		foreach($fields as $index=>$field){
			switch($field["type"]){
				case "text":
					?>
  <!-- text -->
  <div class="form-group">
    <label for="<?=$field["id"]?>" class="col-sm-3 control-label"><?=$field["label"]?></label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="<?=$field["id"]?>" name="<?=$field["name"]?>" placeholder="<?=$field["placeholder"]?>" value="<?=$field["default"]?>" <?=$field["required"]?"required":""?>>
    </div>
  </div>
					<?php
				break;
				case "textarea":
				?>
  <!-- text -->
  <div class="form-group">
    <label for="<?=$field["id"]?>" class="col-sm-3 control-label"><?=$field["label"]?></label>
    <div class="col-sm-9">
      <textarea class="form-control" id="<?=$field["id"]?>" name="<?=$field["name"]?>" placeholder="<?=$field["placeholder"]?>" <?=$field["required"]?"required":""?>><?=$field["default"]?></textarea>
    </div>
  </div>
  				<?php
				break;
				case "password":
					?>
  <!-- password -->
  <div class="form-group">
    <label for="<?=$field["id"]?>" class="col-sm-3 control-label"><?=$field["label"]?></label>
    <div class="col-sm-9">
      <input type="password" class="form-control" id="<?=$field["id"]?>" name="<?=$field["name"]?>" placeholder="<?=$field["placeholder"]?>" value="<?=$field["default"]?>" <?=$field["required"]?"required":""?>>
    </div>
  </div>
					<?php
				break;
				case "datetime":
					?>
  <!-- datetime -->
  <div class="form-group">
    <label for="<?=$field["id"]?>" class="col-sm-3 control-label"><?=$field["label"]?></label>
    <div class="col-sm-9">
      <input type="datetime" class="form-control" id="<?=$field["id"]?>" name="<?=$field["name"]?>" placeholder="<?=$field["placeholder"]?>" value="<?=$field["default"]?>" <?=$field["required"]?"required":""?>>
    </div>
  </div>
					<?php
				break;
				case "number":
					?>
  <!-- number -->
  <div class="form-group">
    <label for="<?=$field["id"]?>" class="col-sm-3 control-label"><?=$field["label"]?></label>
    <div class="col-sm-9">
      <input type="number" class="form-control" id="<?=$field["id"]?>" name="<?=$field["name"]?>" placeholder="<?=$field["placeholder"]?>" value="<?=$field["default"]?>" <?=$field["required"]?"required":""?>>
    </div>
  </div>
					<?php
				break;
				case "image":
					?>
  <!-- number -->
  <div class="form-group">
    <label for="<?=$field["id"]?>" class="col-sm-3 control-label"><?=$field["label"]?></label>
    <div class="col-sm-9">
      <input type="file" accept=".jpg,.jpeg,.png" class="form-control" id="<?=$field["id"]?>" name="<?=$field["name"]?>" <?=$field["required"]?"required":""?>>
    </div>
  </div>
					<?php
				break;
			}
		}
	}
	
	function login($uid, $password, $setCookie){
		$prefix = str_replace(array("/","\\"," "),"",dirname(__FILE__));

		$result = passwordmanager::validPassword($uid, $password);
		if($result["status"]){
			$token = passwordmanager::getSessionToken($uid);
			$_SESSION[$prefix ."_report_token"] = $token;
			if($setCookie){
				setcookie($prefix."_report_token", $token, time()+86400*31);
			}
			return array(
				"status"=>1,
				"msg"=>""
				);
		}else{
			return array(
				"status"=>0,
				"msg"=>'Sorry the password that you entered is incorrect.<br>
	    Please contact the service administrator.'
				);
		}
	}
	
	public function validAuthToken($auth_token){
		return passwordmanager::validPasswordRequestLink($auth_token);
	}

	public function updatePassword($uid, $password){
		passwordmanager::insertPassword($uid, $password);
	}
	
	function logout(){
		$prefix = str_replace(array("/","\\"," "),"",dirname(__FILE__));
		$_SESSION[$prefix ."_report_token"] = "";
		$_SESSION[$prefix ."_report_user"] = "";
		setcookie($prefix ."_report_token", "", time()-3600);
	}
	
	function getRandomString($len){
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		while(strlen($str) < $len){
			$str .= substr($chars, mt_rand(0, strlen($chars)),1);
		}
		return $str;
	}
	
	function insertRedeemHistory($wid, $action, $extra){
		$currentuser = $this->validToken();
		$msg = "";
		switch($action){
			case "redeem":
				$msg = "status updated to redeem by ".$currentuser["username"]." ".$extra;
			break;
			case "unredeem":
				$msg = "status updated to unredeem by ".$currentuser["username"]." ".$extra;
			break;
			case "update note":
				$msg = $currentuser["username"]." updated note : ".$extra;
			break;
		}
		$this->conn->Execute('INSERT INTO '.DB_REDEEMLOG.' ( aid, wid, adminname, action, msg, ip ) VALUES ( ? , ? , ? , ? , ? , ? )', array($currentuser["aid"], $wid, $currentuser["username"], $action, $msg, bcw_clientip::ip_address()));
	}
	
	function getRedeemHistory($wid){
		return $this->conn->GetArray("SELECT * FROM `".DB_REDEEMLOG."` WHERE wid = ? ORDER BY tt DESC", array($wid));
	}

	function getAvailablePage($currentuserlevel){
		global $admin_menu;
		$available_page = "";

		foreach($admin_menu as $key=>$value){
          $allowLevels = explode(",",$value["visible"]);
          if(in_array($currentuserlevel, $allowLevels)){
          	$available_page = $value["folder"];
          	break;
          }
        }
        return $available_page;
	}
}
?>