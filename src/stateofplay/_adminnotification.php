<?php
include_once(__dir__."../../inc/config.php");
include_once(__dir__."../../inc/libs/class.admin.php");
include_once(__dir__."../../inc/libs/class.reporting.php");
include_once(__dir__."../../inc/libs/class.bcw_useragent.php");
include_once(__dir__."../../inc/libs/class.ncapputil.php");
include_once(__dir__."../../inc/libs/claass.logger.php");

$adminManager = new admin(array(
	"conn"=>ncapputil::getConnection(),
	"table"=>DB_ADMIN
));

$table_user  = DB_PREFIX."__users";
$table_winner  = DB_PREFIX."__winners";
$table_user_fbid  = DB_PREFIX."__users_fbid";

$atid = isset($argv[1])?$argv[1]:(isset($_REQUEST["atid"])?$_REQUEST["atid"]:0);
$conn = ncapputil::getConnection();
//$conn->Execute('INSERT INTO winwithit__tracelog (log) VALUES(?)', array('testtest'));
if($atid){
	//get notification information;
	logger::trace("task start :".$atid);
	$data = $adminManager->getTaskDetail($atid);
	if($data && sizeof($data)){
		//check target type
		switch($data["target"]){
			case "notwinner":
				$query = "SELECT d.fbid as fbid FROM ( SELECT DISTINCT a.uid as uid, b.gid as gid FROM `".$table_user."` a LEFT JOIN `".$table_winner."` b ON a.uid = b.uid WHERE b.gid IS NULL ) c JOIN `".$table_user_fbid."` d ON c.uid = d.uid";
				$fbids = $conn->GetCol($query);
			break;
			case "notredeem":
				$query = "SELECT fbid FROM ( SELECT DISTINCT uid FROM `".$table_winner."` WHERE redeem = 0 ) a LEFT JOIN `".$table_user_fbid."` b ON a.uid = b.uid";
				$fbids = $conn->GetCol($query);
			break;
			case "all":
				$query = "SELECT fbid FROM `".$table_user_fbid ."` WHERE fbid <> ''";
				$fbids = $conn->GetCol($query);
			break;
			default:
				$fbids = array($data["target"]);
			break;
		}		
		$publishtime = strtotime($data["publishtime"]);
		
		$sleeptime = $publishtime - time();
		if($sleeptime > 0 ){
			sleep($sleeptime);
		}
		
		$result = $adminManager->publishNotification($data["msg"], $data["href"], $fbids,$atid);
		
		
		if($result["error"]){
			$msg = $result["description"];
			$adminManager->adminlog($msg, $result);
		}else{
			//successfully sent all
			//update data
			$adminManager->updateTaskDetail($result["description"], $result["total"],$atid);
			$msg = "";
		}
	}else{
		$msg = "data is empty, or error occur on mysql";
		$adminManager->adminlog($msg);
	}
}
?>