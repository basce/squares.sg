<?php
header('Content-Type: application/json');

$conn = ncapputil::getConnection();

$table_user  = DB_PREFIX."__users";
$table_user_fbid  = DB_PREFIX."__users_fbid";
$table_fbinvite = DB_PREFIX."__fbinvitations";
$table_invite = DB_PREFIX."__invite";
$table_referral = DB_PREFIX."__referral";
$table_prize = DB_PREFIX."__prizes";
$table_social = DB_PREFIX."__socialleaderboard";
$table_scores = DB_PREFIX."__scoreleaderboard";

$limit = isset($_REQUEST["limit"])?$_REQUEST["limit"]:10;
		$offset = isset($_REQUEST["offset"])?$_REQUEST["offset"]:0;
		$order = isset($_REQUEST["order"])?$_REQUEST["order"]:'asc';
		$sort = isset($_REQUEST["sort"])?$_REQUEST["sort"]:'uid';
		$search = isset($_REQUEST["search"])?$_REQUEST["search"]:'';
		$all = isset($_REQUEST["all"])?$_REQUEST["all"]:0;
		
		$queryAll = 'SELECT COUNT(*) FROM `'.$table_user.'`';
		$query = 'SELECT e.*, j.total_score as `overall_total_score`, j.highest_score as `overall_highest_score`, j.duration as `duration`
		FROM ( SELECT d.uid as `id`, 
		d.fbid as `fbid`, 
		CONCAT(\'f\',d.fbid) as `avatar`,
		d.name,
		d.first_name,
		d.last_name,
		d.sex,
		(
		CASE
			WHEN d.age_range = \'{"min":21}\' THEN \'> 21\'
			WHEN d.age_range = \'{"max":17,"min":13}\' THEN \'13 - 17\'
			WHEN d.age_range = \'{"max":20,"min":18}\' THEN \'18 - 20\'
			ELSE \'unknown\'
		END
		) as `fb_age_range`,
		(
		CASE
			WHEN d.age > 35 THEN \'> 35\'
			WHEN d.age > 20 THEN \'21 - 35\'
			ELSE \'< 21\'
		END
		) as `age_range`,
		d.age,
		d.email,
		d.phone,
		d.ic,
		d.locale,
		d.country,
		d.pdpa,
		d.ip,
		d.tt,
		Coalesce(d.fbverified, 0) as `fbverified`,
		Coalesce(d.fblikeshown, 0) as `fblikeshown`,
		(Coalesce(c.viashare, 0) + Coalesce(c.viaemail, 0)) as `share`,
		(Coalesce(c.total, 0) - Coalesce(c.viashare, 0) - Coalesce(c.viaemail, 0)) as `apprequest`,
		Coalesce(c.total, 0) as `referral`
		FROM ( SELECT b.*, a.fbid FROM `'.$table_user_fbid.'` a join `'.$table_user.'` b on a.uid = b.uid ) d
		LEFT JOIN ( SELECT uid, SUM(total_success) as `total`, SUM(success_fbshares) as `viashare`, SUM(success_fbinvitations) as `viaapprequest`, SUM(success_email) as `viaemail` FROM `'.$table_social.'` GROUP BY uid ) c ON c.uid = d.uid ) e
		LEFT JOIN ( SELECT uid, total_score, highest_score, duration FROM `'.$table_scores .'` WHERE sid = 1 ) f ON e.id = f.uid
		LEFT JOIN ( SELECT uid, total_score, highest_score, duration FROM `'.$table_scores .'` WHERE sid = 2 ) g ON e.id = g.uid
		LEFT JOIN ( SELECT uid, total_score, highest_score, duration FROM `'.$table_scores .'` WHERE sid = 3 ) h ON e.id = h.uid
		LEFT JOIN ( SELECT uid, total_score, highest_score, duration FROM `'.$table_scores .'` WHERE sid = 4 ) i ON e.id = i.uid
		LEFT JOIN ( SELECT uid, total_score, highest_score, duration FROM `'.$table_scores .'` WHERE sid = 0 ) j ON e.id = j.uid';

//build score array

/*
$queryScore = "SELECT sid FROM `".$table_scores."` GROUP BY sid";
$weekIndexs = $conn->GetRow($queryScore);

$scoresArray = array();
if(sizeof($weekIndexs)){
	//with score data
	$allScores = $conn->GetArray("SELECT * FROM `".$table_scores."`");
	foreach($allScores as $key=>$value){
		if(!isset($scoresArray[$value["uid"]])){
			$scoresArray[$value["uid"]] = array();
			foreach($weekIndexs as $key2=>$value2){
				if($value2 == 0){
					$scoresArray[$value["uid"]]["total_score"] = 0;
				}else{
					$scoresArray[$value["uid"]]["week_".$value2] = 0;
				}
				
			}
		}
		if($value["sid"] == 0){
			$scoresArray[$value["uid"]]["total_score"] = $value["score"];
		}else{
			$scoresArray[$value["uid"]]["week_".$value["sid"]] = $value["score"];
		}
	}
}
*/

function build_sorter($param){
	return function($a, $b) use ($param){
		if($a[$param["key"]] == $b[$param["key"]]){
			return 0;
		}else{
			if($param["asc"]){
				return ( $a[$param["key"]] > $b[$param["key"]] ) ? +1 : -1;
			}else{
				return ( $a[$param["key"]] < $b[$param["key"]] ) ? +1 : -1;
			}
		}
	};
}
	
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
				 $query .=" WHERE LOWER(email) LIKE LOWER(?) OR LOWER(name) LIKE LOWER(?) OR LOWER(phone) LIKE LOWER(?)";
				 $queryAll .=" WHERE LOWER(email) LIKE LOWER(?) OR LOWER(name) LIKE LOWER(?) OR LOWER(phone) LIKE LOWER(?)";
				 $insertQ[] = '%'.$search.'%';
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