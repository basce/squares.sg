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

function editData($get){
	global $user, $adminManager;
	$conn = ncapputil::getConnection();
	switch($get["nctable"]){
		case "question":
			$query = "UPDATE `".DB_QUESTION."` SET name = ?, hints = ?, image = ? WHERE question_id = ?";
			$conn->Execute($query, array($get["name"], $get["hints"], $get["image"], $get["id"]));
			$adminManager->adminlog("updated question : ".$get["id"], array($get["name"], $get["hints"], $get["image"]));
			return array(
				"success"=>1,
				"msg"=>"updated question"
			);
		break;
		case "prize":
			$query = "UPDATE `".DB_PRIZE."` SET name = ?, ashortname = ?, tnc = ?, multiplier =?, numberofplay =?, startdatetime = ?, enddatetime = ? WHERE goodieid = ?";
			$conn->Execute($query, array($get["name"], $get["name_in_sentence"], $get["redemption_detail"], $get["multiplier"], $get["numberofplay"], $get["startdate"], $get["enddate"], $get["id"]));
			$adminManager->adminlog("updated prize : ".$get["id"], array($get["name"], $get["name_in_sentence"], $get["redemption_detail"], $get["multiplier"], $get["numberofplay"], $get["startdate"], $get["enddate"]));
			return array(
				"success"=>1,
				"msg"=>"updated prize"
			);
		break;
	}
}

function addData($get){
	global $user, $adminManager;
	$conn = ncapputil::getConnection();
	$query = "INSERT INTO `".DB_QUESTION."` ( name, hints, image ) VALUES (?, ?, ?)";
	$conn->Execute($query, array($get["name"], $get["hints"], $get["image"]));
	$adminManager->adminlog("added new question ", array($get["name"], $get["hints"], $get["image"]));
	return array(
		"success"=>1,
		"msg"=>"added question"
	);
}

function deleteData($get){
	return array(
		"success"=>0,
		"msg"=>"You don't have permission to add/delete"
	);
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
