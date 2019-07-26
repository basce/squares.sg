<?php
header('Content-Type: application/json');

$conn = ncapputil::getConnection();

function checkPermission($permissionFullaccess, $permissionPartialAccess, $permissionReadOnly){
	global $user;
	
	switch($user["level"]){
		case 4:
			return $permissionFullaccess;
		break;
		case 2:
			return $permissionReadOnly;
		break;
		case 1:
			return $permissionReadOnly;
		break;
		default:
			return $permissionReadOnly;
		break;
	}
}

function getTableInfo($tablename){
	global $user;
	switch($tablename){
		case "country":
			return array(
				"fields"=>array(
					array(
						"title"=>"Country ID",
						"field"=>"id",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
					),
					array(
						"title"=>"Country Name",
						"field"=>"name",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
					),
					array(
						"title"=>"Country Code",
						"field"=>"code",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
					),
					array(
						"title"=>"Default Language",
						"field"=>"default_language",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
					),
					array(
						"title"=>"Number of Distributors",
						"field"=>"noc",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
					),
					array(
						"title"=>"ActiveCampaign List ID",
						"field"=>"list_id",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
					)
				),
				"actions"=>checkPermission(
					array("add", "edit", "delete"),
					array(),
					array()
				),
				"blukactions"=>array(
				)
			);
		break;
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
		case "country":
			$workingtable = DB_COUNTRY;
			if($search){
				$query = "SELECT a.*, b.cnt as `noc` FROM ( SELECT id, name, code, default_language, list_id FROM `".$workingtable."` WHERE LOWER(name) LIKE ? OR LOWER(code) LIKE ? ) a LEFT JOIN ( SELECT countryid, COUNT(*) as cnt FROM `".DB_DISTRIBUTOR."` GROUP BY countryid ) b ON a.id = b.countryid";
				$queryparam[] = '%'.strtolower($search).'%';
				$queryparam[] = '%'.strtolower($search).'%';

				$queryall = "SELECT COUNT(*) FROM `".$workingtable."` WHERE LOWER(name) LIKE ? OR LOWER(code) LIKE ?";
				$total = $conn->GetOne($queryall, $queryparam);
			}else{
				$query = "SELECT a.*, b.cnt as `noc` FROM ( SELECT id, name, code, default_language, list_id FROM `".$workingtable."` ) a LEFT JOIN ( SELECT countryid, COUNT(*) as cnt FROM `".DB_DISTRIBUTOR."` GROUP BY countryid ) b ON a.id = b.countryid";
				$queryall = "SELECT COUNT(*) FROM `".$workingtable."`";
				$total = $conn->GetOne($queryall);
			}
		break;
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