<?php
header('Content-Type: application/json');
require_once(__dir__."../../../inc/main.php");

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
			if($user["level"] == 2){
				if($conn->GetOne("SELECT COUNT(*) FROM `".DB_ADMIN."` WHERE LOWER(username) = LOWER(?)",array($get["username"]))){
					return array(
						"success"=>0,
						"msg"=>"A similar name is found in the db, please choose another username"
					);
				}else{
					if($get["password"] != $get["confirmpassword"]){
						return array(
							"success"=>0,
							"msg"=>"confirm password doesn't match, please key in again"
						);
					}else{
						$adminManager->addAdmin($get["username"], $get["password"], $get["level"]);
						return array(
							"success"=>1,
							"msg"=>"admin added"
						);
					}
				}
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
			if($user["level"] != 2){
				return array(
					"success"=>0,
					"msg"=>"You don't have permission to add/edit/delete"
				);
			}
			if($conn->GetOne("SELECT COUNT(*) FROM `".DB_ADMIN."` WHERE LOWER(username) = LOWER(?) AND aid != ?",array($get["username"]))){
				return array(
					"success"=>0,
					"msg"=>"A similar name is found in the db, please choose another username"
				);
			}else{
				if($get["password"] != $get["confirmpassword"]){
					return array(
						"success"=>0,
						"msg"=>"confirm password doesn't match, please key in again"
					);
				}else{
					$query = "UPDATE `".DB_ADMIN."` SET username = ? , password = ?, level = ? WHERE aid = ?";
					$conn->Execute($query, array($get["username"], $get["password"], $get["level"], $get["aid"]));
					$adminManager->adminlog("updated admin : ".$get["aid"], array($get["username"], $get["password"], $get["level"], $get["aid"]));
					return array(
						"success"=>1,
						"msg"=>"admin edited"
					);
				}
			}
		break;
	}
}

function deleteData($get){
	global $user, $adminManager;
	$conn = ncapputil::getConnection();
	switch($get["nctable"]){
		case "admin":
			if($user["level"] != 2){
				return array(
					"success"=>0,
					"msg"=>"You don't have permission to add/edit/delete"
				);
			}
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
}
