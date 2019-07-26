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
		case "distributor":
			if($user["level"] == 4 || $user["level"] == 2){
				return array(
					"fields"=>array(
						array(
							"title"=>"Distributor ID",
							"field"=>"distributor_id",
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
							"title"=>"Contact No",
							"field"=>"contact_no",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>true
						),
						array(
							"title"=>"Email",
							"field"=>"email",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>true
						),
						array(
							"title"=>"Country",
							"field"=>"country",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>true
						),
						array(
							"title"=>"Link",
							"field"=>"link",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>false
						),
						array(
							"title"=>"Exclusion",
							"field"=>"exclusion",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>true
						),
						array(
							"title"=>"Lead Sent Count",
							"field"=>"lead_sent_count",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>true
						),
						array(
							"title"=>"No Follow Up Count",
							"field"=>"no_follow_up_count",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>true
						),
						array(
							"title"=>"No Follow Up %",
							"field"=>"no_follow_up_per",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>true
						),
						array(
							"title"=>"Unique Code",
							"field"=>"uniquecode",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>false
						)
					),
					"actions"=>checkPermission(
						array("add","edit","delete"),
						array("add","edit"),
						array()
					),
					"bulkactions"=>array(
					)
				);
			}else if($user["level"] == 1){
				return array(
					"fields"=>array(
						array(
							"title"=>"Distributor ID",
							"field"=>"distributor_id",
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
							"title"=>"Contact No",
							"field"=>"contact_no",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>true
						),
						array(
							"title"=>"Email",
							"field"=>"email",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>true
						),
						array(
							"title"=>"Link",
							"field"=>"link",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>false
						),
						array(
							"title"=>"Exclusion",
							"field"=>"exclusion",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>true
						),
						array(
							"title"=>"Lead Sent Count",
							"field"=>"lead_sent_count",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>true
						),
						array(
							"title"=>"No Follow Up Count",
							"field"=>"no_follow_up_count",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>true
						),
						array(
							"title"=>"No Follow Up %",
							"field"=>"no_follow_up_per",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>true
						),
						array(
							"title"=>"Unique Code",
							"field"=>"uniquecode",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>true
						),
						array(
							"title"=>"Link",
							"field"=>"link",
							"align"=>"center",
							"valign"=>"middle",
							"sortable"=>true
						)
					),
					"actions"=>checkPermission(
						array("add","edit","delete"),
						array("add","edit","delete"),
						array()
					),
					"bulkactions"=>array(
					)
				);
			}
		break;
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
						"title"=>"ActiveCampaign Domain",
						"field"=>"domain",
						"align"=>"center",
						"valign"=>"middle",
						"sortable"=>true
					),
					array(
						"title"=>"ActiveCampaign Key",
						"field"=>"key",
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
				"bulkactions"=>array(
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

function getData($get, $all = false){
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
		case "distributor":
			$workingtable = DB_DISTRIBUTOR;
			if($search){
				if($user["level"] == 4 || $user["level"] == 2){
					$query = "SELECT * FROM ( SELECT a.distributor_id, a.name, a.contact_no, a.email, a.countryid, a.uniquecode, a.exclusion_flag, ( CASE WHEN a.exclusion_flag = 0 THEN 'false' ELSE 'true' END ) as `exclusion`, a.lead_sent_count, a.no_follow_up_count, CONCAT('".SERVER_PATH."', b.code, '/', b.default_language, '/', a.uniquecode ) as `link`, ( CASE WHEN a.lead_sent_count = 0 THEN 0 ELSE a.no_follow_up_count / a.lead_sent_count END) as `no_follow_up_per`, a.id, b.name as `country_name`, b.code as `country`, b.default_language FROM `".$workingtable."` a LEFT JOIN `".DB_COUNTRY."` b on a.countryid = b.id WHERE LOWER(a.name) LIKE ? OR LOWER(a.email) LIKE ? OR LOWER(a.distributor_id) LIKE ? OR LOWER(a.uniquecode) LIKE ? OR a.contact_no LIKE ? ) c";
					/*$query = "SELECT * FROM ( SELECT a.name, a.email, a.countryid, a.id, b.name as `country_name`, b.code as `country`, CONCAT('".SERVER_PATH."', b.code, '/[Language]/', uniquecode ) as `link` FROM `".$workingtable."` a LEFT JOIN `".DB_COUNTRY."` b on a.countryid = b.id WHERE LOWER(a.name) LIKE ? OR LOWER(a.email) LIKE ? ) c ";*/
					$queryparam[] = '%'.strtolower($search).'%';
					$queryparam[] = '%'.strtolower($search).'%';
					$queryparam[] = '%'.strtolower($search).'%';
					$queryparam[] = '%'.strtolower($search).'%';
					$queryparam[] = '%'.strtolower($search).'%';

					$queryall = "SELECT COUNT(*) FROM `".$workingtable."` a WHERE LOWER(a.name) LIKE ? OR LOWER(a.email) LIKE ? OR LOWER(a.distributor_id) LIKE ? OR LOWER(a.uniquecode) LIKE ? OR a.contact_no LIKE ? ";
					$total = $conn->GetOne($queryall, $queryparam);
				}else{
					//regional admin, 
					$query = "SELECT locationid FROM `".DB_PREFIX."__admin_location` WHERE aid = ?";
					$locationid = $conn->GetOne($query, array($user["aid"]));

					$query = "SELECT * FROM ( SELECT a.distributor_id, a.name, a.contact_no, a.email, a.countryid, a.uniquecode, a.exclusion_flag, ( CASE WHEN a.exclusion_flag = 0 THEN 'false' ELSE 'true' END ) as `exclusion`, a.lead_sent_count, a.no_follow_up_count, CONCAT('".SERVER_PATH."', b.code, '/', b.default_language, '/', a.uniquecode ) as `link`, ( CASE WHEN a.lead_sent_count = 0 THEN 0 ELSE a.no_follow_up_count / a.lead_sent_count END) as `no_follow_up_per`, a.id, b.name as `country_name`, b.code as `country`, b.default_language FROM `".$workingtable."` a LEFT JOIN `".DB_COUNTRY."` b on a.countryid = b.id WHERE a.countryid = ? AND ( LOWER(a.name) LIKE ? OR LOWER(a.email) LIKE ? OR LOWER(a.distributor_id) LIKE ? OR LOWER(a.uniquecode) LIKE ? OR a.contact_no LIKE ? ) ) c";
					/*$query = "SELECT * FROM ( SELECT a.name, a.email, a.countryid, a.id, b.name as `country_name`, b.code as `country`, CONCAT('".SERVER_PATH."', b.code, '/[Language]/', uniquecode ) as `link` FROM `".$workingtable."` a LEFT JOIN `".DB_COUNTRY."` b on a.countryid = b.id WHERE LOWER(a.name) LIKE ? OR LOWER(a.email) LIKE ? ) c ";*/
					$queryparam[] = $locationid;
					$queryparam[] = '%'.strtolower($search).'%';
					$queryparam[] = '%'.strtolower($search).'%';
					$queryparam[] = '%'.strtolower($search).'%';
					$queryparam[] = '%'.strtolower($search).'%';
					$queryparam[] = '%'.strtolower($search).'%';

					$queryall = "SELECT COUNT(*) FROM `".$workingtable."` a WHERE a.countryid = ? AND ( LOWER(a.name) LIKE ? OR LOWER(a.email) LIKE ? OR LOWER(a.distributor_id) LIKE ? OR LOWER(a.uniquecode) LIKE ? OR a.contact_no LIKE ? )";
					$total = $conn->GetOne($queryall, $queryparam);
				}
			}else{
				if($user["level"] == 4 || $user["level"] == 2){
					$query = "SELECT * FROM ( SELECT a.distributor_id, a.name, a.contact_no, a.email, a.countryid, a.uniquecode, a.exclusion_flag, ( CASE WHEN a.exclusion_flag = 0 THEN 'false' ELSE 'true' END ) as `exclusion`, a.lead_sent_count, a.no_follow_up_count, CONCAT('".SERVER_PATH."', b.code, '/', b.default_language, '/', a.uniquecode ) as `link`, ( CASE WHEN a.lead_sent_count = 0 THEN 0 ELSE a.no_follow_up_count / a.lead_sent_count END) as `no_follow_up_per`, a.id, b.name as `country_name`, b.code as `country`, b.default_language FROM `".$workingtable."` a LEFT JOIN `".DB_COUNTRY."` b on a.countryid = b.id ) c";
					/*$query = "SELECT * FROM ( SELECT a.name, a.email, a.countryid, a.id, b.name as `country_name`, b.code as `country`, CONCAT('".SERVER_PATH."', b.code, '/[Language]/', uniquecode ) as `link` FROM `".$workingtable."` a LEFT JOIN `".DB_COUNTRY."` b on a.countryid = b.id WHERE LOWER(a.name) LIKE ? OR LOWER(a.email) LIKE ? ) c ";*/

					$queryall = "SELECT COUNT(*) FROM `".$workingtable."` a";
					$total = $conn->GetOne($queryall, $queryparam);
				}else{
					//regional admin, 
					$query = "SELECT locationid FROM `".DB_PREFIX."__admin_location` WHERE aid = ?";
					$locationid = $conn->GetOne($query, array($user["aid"]));

					$query = "SELECT * FROM ( SELECT a.distributor_id, a.name, a.contact_no, a.email, a.countryid, a.uniquecode, a.exclusion_flag, ( CASE WHEN a.exclusion_flag = 0 THEN 'false' ELSE 'true' END ) as `exclusion`, a.lead_sent_count, a.no_follow_up_count, CONCAT('".SERVER_PATH."', b.code, '/', b.default_language, '/', a.uniquecode ) as `link`, ( CASE WHEN a.lead_sent_count = 0 THEN 0 ELSE a.no_follow_up_count / a.lead_sent_count END) as `no_follow_up_per`, a.id, b.name as `country_name`, b.code as `country`, b.default_language FROM `".$workingtable."` a LEFT JOIN `".DB_COUNTRY."` b on a.countryid = b.id WHERE a.countryid = ? ) c";
					/*$query = "SELECT * FROM ( SELECT a.name, a.email, a.countryid, a.id, b.name as `country_name`, b.code as `country`, CONCAT('".SERVER_PATH."', b.code, '/[Language]/', uniquecode ) as `link` FROM `".$workingtable."` a LEFT JOIN `".DB_COUNTRY."` b on a.countryid = b.id WHERE LOWER(a.name) LIKE ? OR LOWER(a.email) LIKE ? ) c ";*/
					$queryparam[] = $locationid;

					$queryall = "SELECT COUNT(*) FROM `".$workingtable."` a WHERE a.countryid = ?";
					$total = $conn->GetOne($queryall, $queryparam);
				}
			}
		break;
		case "country":
			$workingtable = DB_COUNTRY;
			if($search){
				$query = "SELECT a.*, b.cnt as `noc` FROM ( SELECT id, name, code, domain, `key` FROM `".$workingtable."` WHERE LOWER(name) LIKE ? OR LOWER(code) LIKE ? ) a LEFT JOIN ( SELECT countryid, COUNT(*) as cnt FROM `".DB_SUBMISSION."` GROUP BY countryid ) b ON a.id = b.countryid";
				$queryparam[] = '%'.strtolower($search).'%';
				$queryparam[] = '%'.strtolower($search).'%';

				$queryall = "SELECT COUNT(*) FROM `".$workingtable."` WHERE LOWER(name) LIKE ? OR LOWER(code) LIKE ?";
				$total = $conn->GetOne($queryall, $queryparam);
			}else{
				$query = "SELECT a.*, b.cnt as `noc` FROM ( SELECT id, name, code, domain, `key` FROM `".$workingtable."` ) a LEFT JOIN ( SELECT countryid, COUNT(*) as cnt FROM `".DB_SUBMISSION."` GROUP BY countryid ) b ON a.id = b.countryid";
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

	if($all){
		if($user["level"] == 4 || $user["level"] == 2){
			$query = "SELECT distributor_id, name, contact_no, email, country, exclusion_flag, uniquecode, link, lead_sent_count, no_follow_up_count, no_follow_up_per FROM ( SELECT a.distributor_id, a.name, a.contact_no, a.email, a.countryid, a.uniquecode, a.exclusion_flag, ( CASE WHEN a.exclusion_flag = 0 THEN 'false' ELSE 'true' END ) as `exclusion`, a.lead_sent_count, a.no_follow_up_count, CONCAT('".SERVER_PATH."', b.code, '/', b.default_language, '/', a.uniquecode ) as `link`, ( CASE WHEN a.lead_sent_count = 0 THEN 0 ELSE a.no_follow_up_count / a.lead_sent_count END) as `no_follow_up_per`, a.id, b.name as `country_name`, b.code as `country`, b.default_language FROM `".$workingtable."` a LEFT JOIN `".DB_COUNTRY."` b on a.countryid = b.id ) c";
			/*$query = "SELECT * FROM ( SELECT a.name, a.email, a.countryid, a.id, b.name as `country_name`, b.code as `country`, CONCAT('".SERVER_PATH."', b.code, '/[Language]/', uniquecode ) as `link` FROM `".$workingtable."` a LEFT JOIN `".DB_COUNTRY."` b on a.countryid = b.id WHERE LOWER(a.name) LIKE ? OR LOWER(a.email) LIKE ? ) c ";*/
		}else{
			//regional admin, 
			$query = "SELECT locationid FROM `".DB_PREFIX."__admin_location` WHERE aid = ?";
			$locationid = $conn->GetOne($query, array($user["aid"]));

			$query = "SELECT distributor_id, name, contact_no, email, country, exclusion_flag, uniquecode, link, lead_sent_count, no_follow_up_count, no_follow_up_per FROM ( SELECT a.distributor_id, a.name, a.contact_no, a.email, a.countryid, a.uniquecode, a.exclusion_flag, ( CASE WHEN a.exclusion_flag = 0 THEN 'false' ELSE 'true' END ) as `exclusion`, a.lead_sent_count, a.no_follow_up_count, CONCAT('".SERVER_PATH."', b.code, '/', b.default_language, '/', a.uniquecode ) as `link`, ( CASE WHEN a.lead_sent_count = 0 THEN 0 ELSE a.no_follow_up_count / a.lead_sent_count END) as `no_follow_up_per`, a.id, b.name as `country_name`, b.code as `country`, b.default_language FROM `".$workingtable."` a LEFT JOIN `".DB_COUNTRY."` b on a.countryid = b.id WHERE a.countryid = ? ) c";
			/*$query = "SELECT * FROM ( SELECT a.name, a.email, a.countryid, a.id, b.name as `country_name`, b.code as `country`, CONCAT('".SERVER_PATH."', b.code, '/[Language]/', uniquecode ) as `link` FROM `".$workingtable."` a LEFT JOIN `".DB_COUNTRY."` b on a.countryid = b.id WHERE LOWER(a.name) LIKE ? OR LOWER(a.email) LIKE ? ) c ";*/
			$queryparam[] = $locationid;
		}
		$rows = $conn->getArray($query, $queryparam);
	}else{
		$query .= " LIMIT ?, ?";
		$queryparam[] = (int)$offset;
		$queryparam[] = (int)$limit;
		
		$rows = $conn->getArray($query, $queryparam);
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
	case "csvreport":
		$params = array(
			"nctable"=>"distributor"
		);
		$data = getData($params,true);
		$reportManager = new reporting(array("conn"=>ncapputil::getConnection()));
		$reportManager->exportMysqlToCsv($data["rows"], date("Y_m_d_").time().".csv");
	break;
	default:
		echo json_encode(getData($_REQUEST));
	break;
}