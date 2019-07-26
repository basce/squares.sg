<?php
header('Content-Type: application/json');

$conn = ncapputil::getConnection();

function checkPermission($permissionFullaccess, $permissionPartialAccess, $permissionReadOnly){
	global $user;
	
	switch($user["level"]){
		case 2:
			return $permissionFullaccess;
		break;
		case 1:
			return $permissionPartialAccess;
		break;
		default:
			return $permissionReadOnly;
		break;
	}
}

function getTableInfo($tablename){
	global $user;
	switch($tablename){
		case "redeemlog":
			return array(
				"fields"=>array(
					array(
						"title"=>"Admin",
						"field"=>"adminname",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
						),
					array(
						"title"=>"Message",
						"field"=>"msg",
						"align"=>"left",
						"valign"=>"middle",
						"sortable"=>true
						),
					array(
						"title"=>"IP",
						"field"=>"ip",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
						),
					array(
						"title"=>"Date Time",
						"field"=>"tt",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
						)
					),
				"actions"=>checkPermission(
					array(),
					array(),
					array()
				),
				"bulkactions"=>array(
				)
			);
		break;
		default:
		break;
	}
}

function getData($get){
	global $user;
	$conn = ncapputil::getConnection();
	$search = isset($get["search"]) ? $get["search"] : "";
	$sort = isset($get["sort"]) ? $get["sort"] : "";
	$order = isset($get["order"]) && $get["order"] == "asc" ? "asc" : "desc";
	$offset = isset($get["offset"]) ? $get["offset"] : 0;
	$limit = isset($get["limit"]) ? $get["limit"] : 10;
	$table = isset($get["nctable"]) ? $get["nctable"] : "";
	$winnerid = isset($get["wid"])? $get["wid"] : 0;
	
	$workingtable = "";
	$queryparam = array();
	switch($table){
		case "redeemlog":
			$workingtable = DB_REDEEMLOG;
			if($search){
				$query = "SELECT id, adminname, msg, ip, tt FROM `".$workingtable."` WHERE LOWER(adminname) LIKE ? OR LOWER(msg) LIKE ? AND wid = ?";
				$queryparam[] = '%'.strtolower($search).'%';
				$queryparam[] = '%'.strtolower($search).'%';
				$queryparam[] = $winnerid;

				$queryall = "SELECT COUNT(*) FROM `".$workingtable."` WHERE LOWER(adminname) LIKE ? OR LOWER(msg) LIKE ? AND wid = ?";
				$total = $conn->GetOne($queryall, $queryparam);
			}else{
				$query = "SELECT id, adminname, msg, ip, tt FROM `".$workingtable."` WHERE wid = ?";
				$queryparam[] = $winnerid;
				$queryall = "SELECT COUNT(*) FROM `".$workingtable."` WHERE wid = ?";
				$total = $conn->GetOne($queryall, $queryparam);
			}
		break;
		default:
		break;
	}
	
	if($sort){
		$query .=" ORDER BY `".$sort."`	".( $order == "asc" ? "ASC":"DESC" );
	}
	
	$query .= " LIMIT ?, ?";
	$queryparam[] = (int)$offset;
	$queryparam[] = (int)$limit;
	
	$rows = $conn->getArray($query, $queryparam);
	
	return array(
		"total"=>$total,
		"rows"=>$rows,
		"query"=>$query,
		"queryparam"=>$queryparam
	);
}

$method = isset($_REQUEST["method"]) ? $_REQUEST["method"] : "";
switch($method){
	case "init":
		echo json_encode(getTableInfo($_REQUEST["nctable"]));
	break;
	default:
		echo json_encode(getData($_REQUEST));
	break;
}