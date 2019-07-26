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
		case "prize":
			$query = "UPDATE `".DB_PRIZE."` SET name = ?, shortname = ?, ashortname = ?, tnc = ?, content= ? , multiplier =?, numberofplay =?, startdatetime = ?, enddatetime = ? WHERE goodieid = ?";
			$conn->Execute($query, array($get["name"], $get["shortname"], $get["name_in_sentence"], $get["tnc"], $get["redemption_detail"], $get["multiplier"], $get["numberofplay"], $get["startdate"], $get["enddate"], $get["id"]));
			$adminManager->adminlog("updated prize : ".$get["id"], array($get["name"], $get["shortname"], $get["name_in_sentence"], $get["tnc"], $get["redemption_detail"], $get["multiplier"], $get["numberofplay"], $get["startdate"], $get["enddate"]));
			return array(
				"success"=>1,
				"msg"=>"updated prize"
			);
		break;
	}
}

function addData($get){
	return array(
		"success"=>0,
		"msg"=>"You don't have permission to add/delete"
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
