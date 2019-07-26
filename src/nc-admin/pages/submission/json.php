<?php
header('Content-Type: application/json');

$conn = ncapputil::getConnection();

$table_email = DB_PREFIX."__email";

$limit = isset($_REQUEST["limit"])?$_REQUEST["limit"]:10;
		$offset = isset($_REQUEST["offset"])?$_REQUEST["offset"]:0;
		$order = isset($_REQUEST["order"])?$_REQUEST["order"]:'asc';
		$sort = isset($_REQUEST["sort"])?$_REQUEST["sort"]:'uid';
		$search = isset($_REQUEST["search"])?$_REQUEST["search"]:'';
		$all = isset($_REQUEST["all"])?$_REQUEST["all"]:0;
		
		$queryAll = 'SELECT COUNT(*) FROM `'.$table_email.'`';
		$query = "SELECT * FROM `".$table_email."`";
	
		if($all){
			//for report
			$total = $conn->GetOne($queryAll);
			$rawdata = $conn->GetArray($query);

			$data = array(
				"total"=>$total,
				"rows"=>$rawdata,
				"query"=>$query
			);
		}else{
			$insertQ = array();
			if($search){
				 $query .=" WHERE LOWER(email) LIKE LOWER(?) OR LOWER(landingpage) LIKE LOWER(?)";
				 $queryAll .=" WHERE LOWER(email) LIKE LOWER(?) OR LOWER(landingpage) LIKE LOWER(?)";
				 $insertQ[] = '%'.$search.'%';
				 $insertQ[] = '%'.$search.'%';
			}
			$total = $conn->GetOne($queryAll, $insertQ);
			
			if(!$specialSort){
				$query .=" ORDER BY `".$sort."` ".$order;
				$query .=" LIMIT ".$offset.", ".$limit;
			}
			
			$rawdata = $conn->GetArray($query);
			
			$data = array(
				"total"=>$total,
				"rows"=>$rawdata,
				"query"=>$query
			);
		}
		echo json_encode($data);