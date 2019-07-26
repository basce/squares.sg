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
		case "location":
			return array(
					"fields"=>array(
						array(
							"title"=>"Location ID",
							"field"=>"id",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>true
							),
						array(
							"title"=>"Name",
							"field"=>"name",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>true
							),
						array(
							"title"=>"# claimed",
							"field"=>"noc",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>true
							)
					),
					"actions"=>checkPermission(
						array("add","edit","delete"),
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
	
	$workingtable = "";
	$queryparam = array();
	switch($table){
		case "location":
			$workingtable = DB_LOCATION;
			if($search){
				$query = "SELECT a.*, b.cnt as `noc` FROM ( SELECT id, name FROM `".$workingtable."` WHERE LOWER(name) LIKE ? ) a LEFT JOIN ( SELECT locationid, COUNT(*) as cnt FROM `".DB_WINNER."` GROUP BY locationid ) b ON a.id = b.locationid";
				$queryparam[] = '%'.strtolower($search).'%';

				$queryall = "SELECT COUNT(*) FROM `".$workingtable."` WHERE LOWER(name) LIKE ?";
				$total = $conn->GetOne($queryall, $queryparam);
			}else{
				$query = "SELECT a.*, b.cnt as `noc` FROM ( SELECT id, name FROM `".$workingtable."`) a LEFT JOIN ( SELECT locationid, COUNT(*) as cnt FROM `".DB_WINNER."` GROUP BY locationid ) b ON a.id = b.locationid";

				$queryall = "SELECT COUNT(*) FROM `".$workingtable."`";
				$total = $conn->GetOne($queryall);
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