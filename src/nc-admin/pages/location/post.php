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

function editData($get){
	global $user, $adminManager;
	$conn = ncapputil::getConnection();
	switch($get["nctable"]){
		case "location":
			$query = "UPDATE `".DB_LOCATION."` SET name = ? WHERE id = ?";
			$conn->Execute($query, array($get["name"], $get["id"]));
			$adminManager->adminlog("updated location : ".$get["id"], array($get["name"]));
			return array(
				"success"=>1,
				"msg"=>"updated location"
			);
		break;
	}
}

function addData($get){
	global $user, $adminManager;
	$conn = ncapputil::getConnection();
	switch($get["nctable"]){
		case "location":
			if($user["level"] == 2){
				if($conn->GetOne("SELECT COUNT(*) FROM `".DB_LOCATION."` WHERE LOWER(name) = LOWER(?)",array($get["name"]))){
					return array(
						"success"=>0,
						"msg"=>"A similar name is found in the db, please choose another location name"
					);
				}else{
					$query = "INSERT INTO `".DB_LOCATION."` ( name ) VALUES ( ? )";
					$conn->Execute($query, array($get["name"]));
					$adminManager->adminlog("added new location ", array($get["name"]));
					return array(
						"success"=>1,
						"msg"=>"location added"
					);
				}
			}else{
				return array(
					"success"=>0,
					"msg"=>"You don't have permission to create new location."
				);
			}
		break;
		default:
			return array(
				"success"=>0,
				"msg"=>"You don't have permission to create new location."
			);
		break;
	}
}

function deleteData($get){
	global $user, $adminManager;
	$conn = ncapputil::getConnection();
	switch($get["nctable"]){
		case "location":
			if($user["level"] != 2){
				return array(
					"success"=>0,
					"msg"=>"You don't have permission to delete"
				);
			}
			$query = "DELETE FROM `".DB_LOCATION."` WHERE id = ?";
			$conn->Execute($query, array($get["id"]));
			$adminManager->adminlog("deleted location : ".$get["id"]);
			return array(
				"success"=>1,
				"msg"=>"location deleted"
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
