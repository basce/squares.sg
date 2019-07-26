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
		case "tracking":
			return array(
				"fields"=>array(
					array(
						'title'=>'Ads Label',
						'field'=>'ads_label',
						'align'=>'left',
						'valign'=>'middle',
						'sortable'=>true
					),
					array(
						'title'=>'Impression',
						'field'=>'impression',
						'align'=>'center',
						'valign'=>'middle',
						'sortable'=>false
					),array(
						'title'=>'Estimate Usage (GB)',
						'field'=>'estimate_usage',
						'align'=>'center',
						'valign'=>'middle',
						'sortable'=>true
					),
					array(
						'title'=>'Estimate Cost (USD)',
						'field'=>'estimate_cost',
						'align'=>'center',
						'valign'=>'middle',
						'sortable'=>true
					),
					array(
						'title'=>'Last Active Date',
						'field'=>'last_date',
						'align'=>'center',
						'valign'=>'middle',
						'sortable'=>true
					)
				),
				"actions"=>checkPermission(
					array(),
					array(),
					array()
				)
				,
				"bulkactions"=>array(
					/*"delete"*/
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
		case "tracking":
			$workingtable = DB_TRACK;
			$query0 = "SELECT a.* FROM ( SELECT ads_label, COUNT(*) as `impression`, FORMAT(0.12 * COUNT(*) / 100, 2) as `estimate_cost`, FORMAT(COUNT(*) / 100, 2) as `estimate_usage`,  max(tt) as `last_date` FROM `".$workingtable."` GROUP BY ads_label ) a";
			if($search){
				$query = $query0." WHERE LOWER(ads_label) LIKE ?";
				$queryparam[] = '%'.strtolower($search).'%';

				$queryall = "SELECT COUNT(*) FROM ( SELECT ads_label FROM `".$workingtable."` GROUP BY ads_label ) a WHERE LOWER(ads_label) LIKE ?";
				$total = $conn->getOne($queryall, $queryparam);
			}else{
				$query = $query0;

				$queryall = "SELECT COUNT(*) FROM ( SELECT ads_label FROM `".$workingtable."` GROUP BY ads_label ) a";
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