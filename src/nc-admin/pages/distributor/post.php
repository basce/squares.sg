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
	$main = new main();
	switch($get["nctable"]){
		case "distributor":
			if($user["level"] == 4 || $user["level"] == 2){
				$query = "SELECT COUNT(*) FROM `".DB_DISTRIBUTOR."` WHERE distributor_id = ? AND id != ?";
				if($conn->GetOne($query, array($get["distributor_id"], $get["id"]))){
					return array(
						"success"=>0,
						"msg"=>"Distributor ID is used with other distributor"
					);
				}

				$query = "SELECT email, countryid FROM `".DB_DISTRIBUTOR."` WHERE id = ?";
				$original_distrbutor = $conn->GetRow($query, array($get["id"]));

				$query = "UPDATE `".DB_DISTRIBUTOR."` SET distributor_id = ?, name = ?, contact_no = ?, email = ?, countryid = ?, exclusion_flag = ? WHERE id = ?";
				$conn->Execute($query, array($get["distributor_id"], $get["name"], $get["contact_no"], $get["email"], $get["country"], $get["exclusion"], $get["id"]));

				$adminManager->adminlog("update distributor : ".$get["id"], array($get["distributor_id"], $get["name"], $get["contact_no"], $get["email"], $get["countryid"], $get["exclusion"], $get["id"]));

				if( (strtolower($original_distrbutor["email"]) != strtolower($get["email"])) || ($original_distrbutor["countryid"] != $get["country"]) ){
					//change on email or country
					$main->triggerDistributorChangeNotification($get["id"], "both");
					$msg = "distributor updated. Share link changed, a new email had been sent to distributor.";
				}else{
					$msg = "distributor updated";
				}

				return array(
					"success"=>1,
					"msg"=>$msg
				);

			}else{
				
				$query = "SELECT COUNT(*) FROM `".DB_DISTRIBUTOR."` WHERE distributor_id = ? AND id != ?";
				if($conn->GetOne($query, array($get["distributor_id"], $get["id"]))){
					return array(
						"success"=>0,
						"msg"=>"Distributor ID is used with other distributor"
					);
				}

				$query = "SELECT email, countryid FROM `".DB_DISTRIBUTOR."` WHERE id = ?";
				$original_distrbutor = $conn->GetRow($query, array($get["id"]));

				$query = "UPDATE `".DB_DISTRIBUTOR."` SET distributor_id = ?, name = ?, contact_no = ?, email = ?, exclusion_flag = ? WHERE id = ?";
				$conn->Execute($query, array($get["distributor_id"], $get["name"], $get["contact_no"], $get["email"], $get["exclusion"], $get["id"]));

				$adminManager->adminlog("update distributor : ".$get["id"], array($get["distributor_id"], $get["name"], $get["contact_no"], $get["email"], $get["countryid"], $get["exclusion"], $get["id"]));

				if( (strtolower($original_distrbutor["email"]) != strtolower($get["email"])) ){
					//change on email or country
					$main->triggerDistributorChangeNotification($get["id"]);
					$msg = "distributor updated. Share link changed, a new email had been sent to distributor.";
				}else{
					$msg = "distributor updated";	
				}

				return array(
					"success"=>1,
					"msg"=>$msg
				);
			}
		break;
		case "country":
			$query = "UPDATE `".DB_COUNTRY."` SET name = ?, code = ?, domain = ?, `key` = ? WHERE id = ?";
			$conn->Execute($query, array($get["name"], $get["code"], $get["domain"], $get["key"], $get["id"]));
			$adminManager->adminlog("update country : ".$get["id"], array($get["name"], $get["code"], $get["domain"], $get["key"]));
			return array(
				"success"=>1,
				"msg"=>"updated country"
			);
		break;
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
	$main = new main();
	switch($get["nctable"]){
		case "distributor":
			if($user["level"] == 4 || $user["level"] == 2){
				$query = "SELECT COUNT(*) FROM `".DB_DISTRIBUTOR."` WHERE distributor_id = ?";
				if($conn->GetOne($query, array($get["distributor_id"]))){
					return array(
						"success"=>0,
						"msg"=>"Distributor ID is used with other distributor"
					);
				}

				//create unique code
				$isUniqueCode = false;
				do{
					$randomString = ncapputil::getRandomString(20,"abcdefghijklmnopqrstuvswxyz0123456789_");
					$query = "SELECT COUNT(*) FROM `".DB_DISTRIBUTOR."` WHERE uniquecode = ?";
					if(!$conn->GetOne($query, array($randomString))){
						$isUniqueCode = true;
					}
				}while(!$isUniqueCode);

				$query = "INSERT INTO `".DB_DISTRIBUTOR."` ( distributor_id, name, contact_no, email, countryid, exclusion_flag, uniquecode ) VALUES ( ?, ?, ?, ?, ?, ?, ? )";
				$conn->Execute($query, array($get["distributor_id"], $get["name"], $get["contact_no"], $get["email"], $get["country"], $get["exclusion"], $randomString));
				$distributor_id = $conn->insert_ID();
				$adminManager->adminlog("added new distributor", array($get["distributor_id"], $get["name"], $get["contact_no"], $get["email"], $get["country"], $get["exclusion"], $randomString));

				$main->triggerNewDistributorNotification($distributor_id);

				return array(
					"success"=>1,
					"msg"=>"distributor added. An Email had been sent out to distributor."
				);
			}else{
				//only insert to only the country
				$query = "SELECT COUNT(*) FROM `".DB_DISTRIBUTOR."` WHERE distributor_id = ?";
				if($conn->GetOne($query, array($get["distributor_id"]))){
					return array(
						"success"=>0,
						"msg"=>"Distributor ID is used with other distributor"
					);
				}

				$query = "SELECT locationid FROM `".DB_ADMIN."_location` WHERE aid = ?";
  				$locationid = $conn->GetOne($query, array($user["aid"]));

				//create unique code
				$isUniqueCode = false;
				do{
					$randomString = ncapputil::getRandomString(20,"abcdefghijklmnopqrstuvswxyz0123456789_");
					$query = "SELECT COUNT(*) FROM `".DB_DISTRIBUTOR."` WHERE uniquecode = ?";
					if(!$conn->GetOne($query, array($randomString))){
						$isUniqueCode = true;
					}
				}while(!$isUniqueCode);

				$query = "INSERT INTO `".DB_DISTRIBUTOR."` ( distributor_id, name, contact_no, email, countryid, exclusion_flag, uniquecode ) VALUES ( ?, ?, ?, ?, ?, ?, ? )";
				$conn->Execute($query, array($get["distributor_id"], $get["name"], $get["contact_no"], $get["email"], $locationid, $get["exclusion"], $randomString));
				$distributor_id = $conn->insert_ID();
				$adminManager->adminlog("added new distributor", array($get["distributor_id"], $get["name"], $get["contact_no"], $get["email"], $locationid, $get["exclusion"]));

				$main->triggerNewDistributorNotification($distributor_id);

				return array(
					"success"=>1,
					"msg"=>"distributor added. An Email had been sent out to distributor."
				);

			}
		break;
		default:
			return array(
				"success"=>0,
				"msg"=>"Missing parameters."
			);
		break;
	}
}

function deleteData($get){
	global $user, $adminManager;
	$conn = ncapputil::getConnection();
	switch($get["nctable"]){
		case "distributor":
			if($user["level"] == 4 || $user["level"] == 2){
				$query = "DELETE FROM `".DB_DISTRIBUTOR."` WHERE id = ?";
				$conn->Execute($query, array($get["id"]));
				$adminManager->adminlog("deleted distributor : ".$get["id"]);
				return array(
					"success"=>1,
					"msg"=>"distributor deleted"
				);
			}else{
				return array(
					"success"=>0,
					"msg"=>"You don't have permission to delete"
				);
			}
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
