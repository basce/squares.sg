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
				if($conn->GetOne("SELECT COUNT(*) FROM `".DB_ADMIN."` WHERE LOWER(email) = LOWER(?)",array($get["email"]))){
					return array(
						"success"=>0,
						"msg"=>"A similar email is found in the db, please choose another email"
					);
				}else{
					$adminid = $adminManager->insertAdmin($get["username"], $get["email"], $get["level"]);
					$query = "SELECT COUNT(*) FROM `".DB_ADMIN."_location` WHERE aid = ?";
					if($conn->GetOne($query, array($get["aid"]))){
						$conn->Execute("UPDATE `".DB_ADMIN."_location` SET locationid = ? WHERE aid = ?", array($get["location"], $adminid));
					}else{
						$conn->Execute("INSERT INTO `".DB_ADMIN."_location` (locationid, aid) VALUES ( ? ,? )", array($get["location"], $adminid));
					}
					return array(
						"success"=>1,
						"msg"=>"admin added"
					);
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
			if($conn->GetOne("SELECT COUNT(*) FROM `".DB_ADMIN."` WHERE LOWER(email) = LOWER(?) AND aid != ?",array($get["email"]))){
				return array(
					"success"=>0,
					"msg"=>"A similar email is found in the db, please choose another email"
				);
			}else{
				$query = "UPDATE `".DB_ADMIN."` SET username = ? , email = ?, level = ? WHERE aid = ?";
				$conn->Execute($query, array($get["username"], $get["email"], $get["level"], $get["aid"]));
				$adminManager->adminlog("updated admin : ".$get["aid"], array($get["username"], $get["email"], $get["level"], $get["aid"]));
				$query = "SELECT COUNT(*) FROM `".DB_ADMIN."_location` WHERE aid = ?";
				if($conn->GetOne($query, array($get["aid"]))){
					$conn->Execute("UPDATE `".DB_ADMIN."_location` SET locationid = ? WHERE aid = ?", array($get["location"], $get["aid"]));
				}else{
					$conn->Execute("INSERT INTO `".DB_ADMIN."_location` (locationid, aid) VALUES ( ? ,? )", array($get["location"], $get["aid"]));
				}
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
