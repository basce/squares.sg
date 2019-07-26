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
		case "adminlog":
			return array(
				"fields"=>array(
					array(
						'title'=>'admin',
						'field'=>'aname',
						'align'=>'center',
						'valign'=>'middle',
						'sortable'=>true
					),
					array(
						'title'=>'Log',
						'field'=>'action',
						'align'=>'left',
						'valign'=>'top',
						'sortable'=>false
					),
					array(
						'title'=>'IP',
						'field'=>'ip',
						'align'=>'center',
						'valign'=>'middle',
						'sortable'=>true
					),
					array(
						'title'=>'datetime',
						'field'=>'tt',
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
		case "adminlog":
			$workingtable = DB_ADMIN."_log";
			if($user["level"] == 2){
				if($search){
					$query = "SELECT * FROM `".$workingtable."` WHERE LOWER(action) LIKE ?";
					$queryparam[] = '%'.strtolower($search).'%';
					
					$queryall = "SELECT COUNT(*) FROM `".$workingtable."` WHERE LOWER(action) LIKE ?";
					$total = $conn->getOne($queryall, $queryparam);
				}else{
					$query = "SELECT * FROM `".$workingtable."`";	
					
					$queryall = "SELECT COUNT(*) FROM `".$workingtable."`";
					$total = $conn->getOne($queryall);
				}
			}else{
				return array(
					"total"=>0,
					"rows"=>array()
				);
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