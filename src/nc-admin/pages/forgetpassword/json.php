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
		case "admin":
			return array(
				"fields"=>array(
					array(
						'title'=>'Username',
						'field'=>'username',
						'align'=>'center',
						'valign'=>'middle',
						'sortable'=>true
					),
					array(
						'title'=>'Email',
						'field'=>'email',
						'align'=>'center',
						'valign'=>'middle',
						'sortable'=>false
					),
					array(
						'title'=>'Access Level',
						'field'=>'level_label',
						'align'=>'center',
						'valign'=>'middle',
						'sortable'=>true
					),
					array(
						'title'=>'Default Location',
						'field'=>"name",
						'align'=>'center',
						'valign'=>'middle',
						'sortable'=>true
					),
				),
				"actions"=>checkPermission(
					array("add","edit","delete"),
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
		case "admin":
			$workingtable = DB_ADMIN;
			$workingtable2 = DB_ADMIN."_location";
			if($user["level"] == 2){
				if($search){
					$query = "SELECT c.*, d.name FROM ( SELECT a.*, b.locationid FROM ( SELECT aid, username, password, level, (
		CASE
			WHEN level = 0 THEN 'Read Only'
			WHEN level = 1 THEN 'Full Access ( without admin management )'
			WHEN level = 2 THEN 'Full Access ( with admin management )'
			WHEN level = 3 THEN 'Store Manager (Only can access redemption)'
			ELSE 'unknown'
		END
		) as `level_label` FROM `".$workingtable."` WHERE LOWER(username) LIKE ? ) a LEFT JOIN `".$workingtable2."` b ON a.aid = b.aid ) c LEFT JOIN `".DB_LOCATION."` d ON c.locationid = d.id";
					$queryparam[] = '%'.strtolower($search).'%';
					
					$queryall = "SELECT COUNT(*) FROM `".$workingtable."` WHERE LOWER(username) LIKE ?";
					$total = $conn->getOne($queryall, $queryparam);
				}else{
					$query = "SELECT c.*, d.name FROM ( SELECT a.*, b.locationid FROM ( SELECT aid, username, password, level, (
		CASE
			WHEN level = 0 THEN 'Read Only'
			WHEN level = 1 THEN 'Full Access ( without admin management )'
			WHEN level = 2 THEN 'Full Access ( with admin management )'
			WHEN level = 3 THEN 'Store Manager (Only can access redemption)'
			ELSE 'unknown'
		END
		) as `level_label` FROM `".$workingtable."` ) a LEFT JOIN `".$workingtable2."` b on a.aid = b.aid ) c LEFT JOIN `".DB_LOCATION."` d on c.locationid = d.id";	
					
					$queryall = "SELECT COUNT(*) FROM `".$workingtable."`";
					$total = $conn->getOne($queryall);
				}

			}else{
				if($search){
					$query = "SELECT c.*, d.name FROM ( SELECT a.*, b.locationid FROM ( SELECT aid, username, '****' as `password`, level, (
		CASE
			WHEN level = 0 THEN 'Read Only'
			WHEN level = 1 THEN 'Full Access ( without admin management )'
			WHEN level = 2 THEN 'Full Access ( with admin management )'
			WHEN level = 3 THEN 'Store Manager (Only can access redemption)'
			ELSE 'unknown'
		END
		) as `level_label` FROM `".$workingtable."` WHERE LOWER(username) LIKE ? ) a LEFT JOIN `".$workingtable2."` b on a.aid = b.aid ) c LEFT JOIN `".DB_LOCATION."` d ON c.locationid = d.id";
					$queryparam[] = '%'.strtolower($search).'%';
					
					$queryall = "SELECT COUNT(*) FROM `".$workingtable."` WHERE LOWER(username) LIKE ?";
					$total = $conn->getOne($queryall, $queryparam);
				}else{
					$query = "SELECT c.*, d.name FROM ( SELECT a.*, b.name FROM ( SELECT aid, username, '****' as `password`, level, (
		CASE
			WHEN level = 0 THEN 'Read Only'
			WHEN level = 1 THEN 'Full Access ( without admin management )'
			WHEN level = 2 THEN 'Full Access ( with admin management )'
			WHEN level = 3 THEN 'Store Manager (Only can access redemption)'
			ELSE 'unknown'
		END
		) as `level_label` FROM `".$workingtable."` ) a LEFT JOIN `".$workingtable2."` b on a.aid = b.aid ) c LEFT JOIN `".DB_LOCATION."` d on c.locationid = d.id";	
					
					$queryall = "SELECT COUNT(*) FROM `".$workingtable."`";
					$total = $conn->getOne($queryall);
				}
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