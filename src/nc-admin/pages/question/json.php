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
		case "question":
			return array(
				"fields"=>array(
					array(
						"title"=>"Question ID",
						"field"=>"question_id",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
						),
					array(
						"title"=>"Answer",
						"field"=>"name",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
						),
					array(
						"title"=>"Hints",
						"field"=>"hints",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
						),
					array(
						"title"=>"Image",
						"field"=>"image",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
						)
				),
				"actions"=>checkPermission(
					array("edit"),
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
		case "question":
			$workingtable = DB_QUESTION;
			if($search){
				$query = "SELECT question_id as `id`, name, hints, image FROM `".$workingtable."` WHERE LOWER(name) LIKE ? OR LOWER(hints) LIKE ?";
				$queryparam[] = '%'.strtolower($search).'%';
				$queryparam[] = '%'.strtolower($search).'%';

				$queryall = "SELECT COUNT(*) FROM `".$workingtable."` WHERE LOWER(name) LIKE ? OR LOWER(hints) LIKE ?";
				$total = $conn->GetOne($queryall, $queryparam);
			}else{
				$query = "SELECT question_id as `id`, name, hints, image FROM `".$workingtable."`";

				$queryall = "SELECT COUNT(*) FROM `".$workingtable."`";
				$total = $conn->GetOne($queryall);
			}
		break;
		case "prize":
			$workingtable = DB_PRIZE;
			if($search){
				$query = "SELECT goodieid as `id`, name, ashortname, maxamount, amount, startdatetime, enddatetime, oneover, tnc, multiplier, numberofplay FROM `".$workingtable."` WHERE LOWER(name) LIKE ?";
				$queryparam[] = '%'.strtolower($search).'%';

				$queryall = "SELECT COUNT(*) FROM `".$workingtable."` WHERE LOWER(name) LIKE ?";
				$total = $conn->GetOne($queryall, $queryparam);
			}else{
				$query = "SELECT goodieid as `id`, name, ashortname, maxamount, amount, startdatetime, enddatetime, oneover, tnc, multiplier, numberofplay FROM `".$workingtable."`";	
				
				$queryall = "SELECT COUNT(*) FROM `".$workingtable."`";
				$total = $conn->getOne($queryall);
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