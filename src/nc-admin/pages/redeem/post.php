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
			$query = "UPDATE `".DB_PRIZE."` SET name = ?, ashortname = ?, tnc = ?, content= ? , multiplier =?, numberofplay =?, startdatetime = ?, enddatetime = ? WHERE goodieid = ?";
			$conn->Execute($query, array($get["name"], $get["name_in_sentence"], $get["tnc"], $get["redemption_detail"], $get["multiplier"], $get["numberofplay"], $get["startdate"], $get["enddate"], $get["id"]));
			$adminManager->adminlog("updated prize : ".$get["id"], array($get["name"], $get["name_in_sentence"], $get["tnc"], $get["redemption_detail"], $get["multiplier"], $get["numberofplay"], $get["startdate"], $get["enddate"]));
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

function searchByCode($get){
	$table_winners = DB_PREFIX."__winners";
	$table_user  = DB_PREFIX."__users";
	$table_user_fbid  = DB_PREFIX."__users_fbid";
	$table_prize = DB_PREFIX."__prizes";

	if(isset($get["code"])){
		$conn = ncapputil::getConnection();
		$query = "SELECT a.uid, a.gid, a.tt, a.wid, a.redeem, a.locationid, b.code FROM `".$table_winners."` a JOIN `".DB_CODE."` b ON a.wid = b.wid WHERE UPPER(b.code) = UPPER(?)";
		$winnerdata = $conn->GetRow($query, array($_REQUEST["code"]));
		
		if(sizeof($winnerdata )){
			$userdata = $conn->GetRow("SELECT b.fbid, a.name, a.sex, a.email, a.ip, a.tt FROM `".$table_user."` a JOIN `".$table_user_fbid."` b ON a.uid = b.uid WHERE a.uid = ?", array($winnerdata["uid"]));
			
			$prizedata = $conn->GetRow("SELECT name, ashortname, goodieid FROM `".DB_PRIZE."` WHERE goodieid = ?", array($winnerdata["gid"]));
			
			$location = $conn->GetOne("SELECT name FROM `".DB_LOCATION."` WHERE id = ?", array($winnerdata["locationid"]));

			$data = array(
							"wid"=>$winnerdata["wid"],
							"username"=>$userdata["name"],
							"gender"=>$userdata["sex"],
							"fbid"=>$userdata["fbid"],
							"email"=>$userdata["email"],
							"ip"=>$userdata["ip"],
							"tt"=>$winnerdata["tt"],
							"prizename"=>$prizedata["name"],
							"code"=>$winnerdata["code"],
							"goodieid"=>$prizedata["goodieid"],
							"redeem"=>$winnerdata["redeem"],
							"location"=>$location?$location:"",
							"locationid"=>$winnerdata["locationid"]
						);
			return $data;
		}else{
			return NULL;
		}
	}else{
		return NULL;
	}
}

function redeem($get){
	global $user, $adminManager;
	$table_winners = DB_PREFIX."__winners";
	$status = 1;
	$msg = "";
	if(isset($get["wid"]) && isset($get["locationid"])){
		$conn = ncapputil::getConnection();
		if($conn->GetOne("SELECT COUNT(*) FROM `".$table_winners."` WHERE wid = ?", array($get["wid"]))){
			$isRedeem = $conn->GetOne("SELECT redeem FROM `".$table_winners."` WHERE wid = ?", array($get["wid"]));
			if($isRedeem !== "0"){
				$status = 0;
				$msg = 'Error: prize had been redeemed : ('.$get["wid"].'). Refresh to check the info';
			}else{
				$query = "UPDATE `".$table_winners."` SET redeem = ?, locationid = ? WHERE wid = ?";
				$conn->Execute($query, array(1, $get["locationid"], $get["wid"]));
				$status = 1;
				$msg = "Redemption status updated";

				$locationname = $conn->GetOne("SELECT name FROM `".DB_LOCATION."` WHERE id = ?", array($get["locationid"]));

				$adminManager->insertRedeemHistory($get["wid"], "redeem", " at ".$locationname."(".$get["locationid"].")");
				$adminManager->adminlog($user["name"]." set wid:".$get["wid"]."'s redeemed to 1 and locationid to ".$get["locationid"]);	
			}
		}else{
			$status = 0;
			$msg = 'Error: invalid wid : ('.$get["wid"].').';	
		}
	}else{
		$status = 0;
		$msg = 'Error: missing wid or locationid';
	}
	return array(
			"success"=>$status,
			"msg"=>$msg
		);
}

function unredeem($get){
	global $user, $adminManager;
	$table_winners = DB_PREFIX."__winners";
	$status = 1;
	$msg = "";
	if(isset($get["wid"])){
		$conn = ncapputil::getConnection();
		if($conn->GetOne("SELECT COUNT(*) FROM `".$table_winners."` WHERE wid = ?", array($get["wid"]))){
			if($user["level"] <= 2){
				$query = "UPDATE `".$table_winners."` SET redeem = ?, locationid = ? WHERE wid = ?";
				$conn->Execute($query, array(0, 0, $get["wid"]));
				$status = 1;
				$msg = "Redemption status updated";

				$adminManager->insertRedeemHistory($get["wid"], "unredeem", "");
				$adminManager->adminlog($user["name"]." set wid:".$get["wid"]."'s redeemed to 0");	
			}else{
				$status = 0;
				$msg = 'Error: You are not authorised to change the redemption status of this item.';	
			}
		}else{
			$status = 0;
			$msg = 'Error: invalid wid : ('.$get["wid"].').';	
		}
	}else{
		$status = 0;
		$msg = 'Error: wid is not defined.';
	}
	return array(
		"success"=>$status,
		"msg"=>$msg
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
	case "searchByCode":
		echo json_encode(searchByCode($_REQUEST));
	break;
	case "redeem":
		echo json_encode(redeem($_REQUEST));
	break;
	case "unredeem":
		echo json_encode(unredeem($_REQUEST));
	break;
}
