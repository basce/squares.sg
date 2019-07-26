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
	$publishtime = isset($_POST["publishtime"])?$_POST["publishtime"]:date("Y-m-d H:i:s");
	$msg = isset($_POST["msg"])?$_POST["msg"]:"";
	$target = isset($_POST["target"])?$_POST["target"]:"all";
	if($msg){
		$adminManager->addTask($publishtime,$msg, "?frm=app",$target);
	}
	return array(
		"success"=>1,
		"msg"=>"Task added. It will take sometime to sent out all notification, please come back again and check on the detail column."
	);
}

function editData($get){
	return array(
		"success"=>0,
		"msg"=>"sent notification cannot be edited"
	);
}

function deleteData($get){
	return array(
		"success"=>0,
		"msg"=>"sent notification cannot be deleted"
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
