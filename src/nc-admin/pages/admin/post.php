<?php
$conn = ncapputil::getConnection();

$adminManager = new admin(array(
	"conn"=>$conn,
	"table"=>DB_ADMIN
));
$user = $adminManager->validToken();
if(!$user){
	exit("require admin login");
}

function addData($get){
	global $user, $adminManager;
	$conn = ncapputil::getConnection();
	switch($get["nctable"]){
		case "admin":
			if($user["level"] == 4){
				if($conn->GetOne("SELECT COUNT(*) FROM `".DB_ADMIN."` WHERE LOWER(username) = LOWER(?)",array($get["username"]))){
					return array(
						"success"=>0,
						"msg"=>"A similar username is found in the db, please choose another username"
					);	
				}
				if(isset($get["email"]) && $get["email"] && $conn->GetOne("SELECT COUNT(*) FROM `".DB_ADMIN."` WHERE LOWER(email) = LOWER(?)",array($get["email"]))){
					return array(
						"success"=>0,
						"msg"=>"A similar email is found in the db, please choose another email"
					);
				}
				if($get["level"] == 1){
					//regional admin
					if(isset($get["password"]) && $get["password"]){
						
					}else{
						return array(
							"success"=>0,
							"msg"=>"Initial password is needed for regional admin."
						);
					}

					if($get["level"] == 0){
						return array(
							"success"=>0,
							"msg"=>"Please select a country for regional admin."
						);
					}
					$adminid = $adminManager->insertAdmin($get["username"], $get["email"], $get["level"]);
				}else{
					$adminid = $adminManager->insertAdmin($get["username"], $get["email"], $get["level"]);
				}
				if(isset($get["password"]) && $get["password"]){
					$adminManager->updatePassword($adminid["uid"], $get["password"]);
				}

				$conn->Execute("INSERT INTO `".DB_ADMIN."_location` (locationid, aid) VALUES ( ? ,? )", array($get["location"], $adminid["uid"]));
				
				return array(
					"success"=>1,
					"msg"=>"admin added"
				);
			}else if($user["level"] == 2){
				if($conn->GetOne("SELECT COUNT(*) FROM `".DB_ADMIN."` WHERE LOWER(username) = LOWER(?)",array($get["username"]))){
					return array(
						"success"=>0,
						"msg"=>"A similar username is found in the db, please choose another username"
					);	
				}
				if(isset($get["email"]) && $get["email"] && $conn->GetOne("SELECT COUNT(*) FROM `".DB_ADMIN."` WHERE LOWER(email) = LOWER(?)",array($get["email"]))){
					return array(
						"success"=>0,
						"msg"=>"A similar email is found in the db, please choose another email"
					);
				}
				if($get["level"] == 1){
					//regional admin
					if(isset($get["password"]) && $get["password"]){
						
					}else{
						return array(
							"success"=>0,
							"msg"=>"Initial password is needed for regional admin."
						);
					}

					if($get["level"] == 0){
						return array(
							"success"=>0,
							"msg"=>"Please select a country for regional admin."
						);
					}
					$adminid = $adminManager->insertAdmin($get["username"], $get["email"], $get["level"]);
				}else{
					$adminid = $adminManager->insertAdmin($get["username"], $get["email"], $get["level"]);
				}
				//if have password
				if(isset($get["password"]) && $get["password"]){
					$adminManager->updatePassword($adminid["uid"], $get["password"]);
				}

				$conn->Execute("INSERT INTO `".DB_ADMIN."_location` (locationid, aid) VALUES ( ? ,? )", array($get["location"], $adminid["uid"]));
				
				return array(
					"success"=>1,
					"msg"=>"admin added"
				);
			}else{
				return array(
					"success"=>0,
					"msg"=>"You don't have permission to create new admin user."
				);
			}
		break;
	}
}

function editData($get){
	global $user, $adminManager;
	$conn = ncapputil::getConnection();
	switch($get["nctable"]){
		case "admin":
			if($user["level"] != 2 AND $user["level"] != 4 AND $user["level"] != 1){
				return array(
					"success"=>0,
					"msg"=>"You don't have permission to add/edit/delete"
				);
			}
			if($get["aid"] != $user["aid"]){
				$query = "SELECT level FROM `".DB_ADMIN."` WHERE aid = ?";
				if($conn->GetOne($query, array($get["aid"])) == 2 && $user["level"] != 4){
					return array(
						"success"=>0,
						"msg"=>"You are not allow to edit other super admin account. You may only edit your own account"
					);
				}

				if($conn->GetOne($query, array($get["aid"])) == 1 && $user["level"] == 1){
					return array(
						"success"=>0,
						"msg"=>"You are not allow to edit other admin account. You may only edit your own account"
					);
				}
			}
			if(isset($get["email"]) && $get["email"] && $conn->GetOne("SELECT COUNT(*) FROM `".DB_ADMIN."` WHERE LOWER(email) = LOWER(?) AND aid != ?",array($get["email"], $get["aid"]))){
				return array(
					"success"=>0,
					"msg"=>"A similar email is found in the db, please choose another email"
				);
			}
			if($conn->GetOne("SELECT COUNT(*) FROM `".DB_ADMIN."` WHERE LOWER(username) = LOWER(?) AND aid != ?",array($get["username"], $get["aid"]))){
				return array(
					"success"=>0,
					"msg"=>"A similar username is found in the db, please choose another username"
				);
			}
			$query = "UPDATE `".DB_ADMIN."` SET username = ? , email = ?, level = ? WHERE aid = ?";
			$conn->Execute($query, array($get["username"], $get["email"], $get["level"], $get["aid"]));
			$adminManager->adminlog("updated admin : ".$get["aid"], array($get["username"], $get["email"], $get["level"], $get["aid"]));
			$query = "SELECT COUNT(*) FROM `".DB_ADMIN."_location` WHERE aid = ?";
			if($conn->GetOne($query, array($get["aid"]))){
				$conn->Execute("UPDATE `".DB_ADMIN."_location` SET locationid = ? WHERE aid = ?", array($get["location"], $get["aid"]));
			}else{
				$conn->Execute("INSERT INTO `".DB_ADMIN."_location` (locationid, aid) VALUES ( ? ,? )", array($get["location"], $get["aid"]));
			}

			if(isset($get["password"])){
				if($get["level"] == 1 || $user["aid"] == 1){
					//store manager update password
					$adminManager->updatePassword($get["aid"], $get["password"]);
					return array(
						"success"=>1,
						"msg"=>"admin edited. Password updated."
					);
				}else{
					return array(
						"success"=>1,
						"msg"=>"admin edited. Please note that reset password only available via email."
					);
				}
			}else{
				return array(
					"success"=>1,
					"msg"=>"admin edited"
				);
			}
			
			
		break;
	}
}

function deleteData($get){
	global $user, $adminManager;
	$conn = ncapputil::getConnection();
	switch($get["nctable"]){
		case "admin":
			$query = "DELETE FROM `".DB_ADMIN."` WHERE aid = ?";
			$conn->Execute($query, array($get["id"]));
			$adminManager->adminlog("deleted admin : ".$get["id"]);
			return array(
				"success"=>1,
				"msg"=>"admin account deleted"
			);
		break;
	}
}

function sendEmail($get){
	global $adminManager;
	global $conn;
	global $main;
	$query = "SELECT email FROM `".DB_ADMIN."` WHERE aid = ?";
	$email = $conn->GetOne($query, array($get["id"]));
	if($email){
		$auth_token = $adminManager->getAuthToken($get["id"]);
		$content = array(
				"username"=>$email,
				"resetlink"=>SERVER_PATH.ADMIN_FOLDER."resetpassword/".$auth_token
			);

		$receiver = array(
			"email"=>$email
			);

		$main->sendResetEmail($receiver, $content);

		return array(
				"success"=>1,
				"Email Sent"
			);
	}else{
		return array(
				"success"=>0,
				"msg"=>"invalid admin id"
			);
	}
}

$method = isset($_REQUEST["method"]) ? $_REQUEST["method"] : "";
switch($method){
	case "add":
		echo json_encode(addData($_REQUEST));
	break;
	case "edit":
		echo json_encode(editData($_REQUEST));
	break;
	case "del":
		echo json_encode(deleteData($_REQUEST));
	break;
	case "retrievepw":
		echo json_encode(sendEmail($_REQUEST));
	break;
}
