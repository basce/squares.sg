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
		case "prize":
			return array(
				"fields"=>array(
					array(
						"title"=>"Prize ID",
						"field"=>"id",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
						),
					array(
						"title"=>"Prize Name",
						"field"=>"name",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
						),
					array(
						"title"=>"Current Amount",
						"field"=>"amount",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
						),
					array(
						"title"=>"Max Amount",
						"field"=>"maxamount",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
						),
					array(
						"title"=>"Start Date",
						"field"=>"startdatetime",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
						),
					array(
						"title"=>"End Date",
						"field"=>"enddatetime",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
						),
					array(
						"title"=>"Probability",
						"field"=>"oneover",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
						),
					array(
						"title"=>"Multiplier",
						"field"=>"multiplier",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
						),
					array(
						"title"=>"Number of plays",
						"field"=>"numberofplay",
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
		case "prize":
			$workingtable = DB_PRIZE;
			if($search){
				$query = "SELECT goodieid as `id`, name, shortname, ashortname, maxamount, amount, startdatetime, enddatetime, oneover, tnc, content, multiplier, numberofplay FROM `".$workingtable."` WHERE LOWER(name) LIKE ?";
				$queryparam[] = '%'.strtolower($search).'%';

				$queryall = "SELECT COUNT(*) FROM `".$workingtable."` WHERE LOWER(name) LIKE ?";
				$total = $conn->GetOne($queryall, $queryparam);
			}else{
				$query = "SELECT goodieid as `id`, name, shortname, ashortname, maxamount, amount, startdatetime, enddatetime, oneover, tnc, content, multiplier, numberofplay FROM `".$workingtable."`";	
				
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
	
	if($table == "code"){
		//check maximum of use
		foreach($rows as $key=>$value){
			$query = "SELECT COUNT(*) FROM `".DB_USEDCODE."` WHERE LOWER(code) = LOWER(?)";
			$usedcount = $conn->GetOne($query, array($value["code"]));
			$rows[$key]["quantity"] = $value["maxnumberofuse"]." / ".$usedcount;
		}
	}
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