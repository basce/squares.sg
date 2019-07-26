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
$table_scores = DB_PREFIX."__scores";
$table_winners = DB_PREFIX."__winners";
$table_code = DB_PREFIX."__code";

$limit = isset($_REQUEST["limit"])?$_REQUEST["limit"]:10;
		$offset = isset($_REQUEST["offset"])?$_REQUEST["offset"]:0;
		$order = isset($_REQUEST["order"])?$_REQUEST["order"]:'asc';
		$sort = isset($_REQUEST["sort"])?$_REQUEST["sort"]:'uid';
		$search = isset($_REQUEST["search"])?$_REQUEST["search"]:'';
		$all = isset($_REQUEST["all"])?$_REQUEST["all"]:0;

$insertQ = array();

if($_REQUEST["method"] && (INT)$_REQUEST["method"]){
		$queryAll = 'SELECT COUNT(*) FROM `'.$table_winners.'` WHERE redeem = 1 AND locationid = ?';
		$query = '
		SELECT n.*, o.tt as `redeemed_time` FROM
		(
			SELECT h.*, g.code, g.wid, g.gid, g.won_tt, g.prizename, g.location_name, g.locationid FROM
			(
				SELECT l.*, m.name as `location_name` FROM 
				(
					SELECT j.*, k.code FROM
					(
						SELECT e.wid, e.locationid, e.uid, e.gid, e.tt as `won_tt`, f.name as `prizename` FROM `'.$table_winners.'` e LEFT JOIN
						`'.$table_prize.'` f on e.gid = f.goodieid WHERE e.redeem = 1 AND e.locationid = ?
					) j LEFT JOIN  `'.$table_code.'` k ON j.gid = k.wid
				) l LEFT JOIN `'.DB_LOCATION.'` m ON l.locationid = m.id
			) g LEFT JOIN 
			(
				SELECT d.uid as `id`, d.name, d.first_name, d.last_name, d.sex, d.age, d.email, d.phone, d.ic FROM `'.$table_user.'` d
			) h ON g.uid = h.id
		) n LEFT JOIN
		(
			SELECT p1.* FROM ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p1 LEFT JOIN ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p2 ON ( p1.wid = p2.wid AND p1.tt < p2.tt ) WHERE p2.tt is NULL
		) o ON n.wid = o.wid
		';

		$insertQ[] = $_REQUEST["method"];

}else{

		$queryAll = 'SELECT COUNT(*) FROM `'.$table_winners.'` WHERE redeem = 1';
		$query = '
		SELECT n.*, o.tt as `redeemed_time` FROM
		(
			SELECT h.*, g.code, g.wid, g.gid, g.won_tt, g.prizename, g.location_name, g.locationid FROM
			(
				SELECT l.*, m.name as `location_name` FROM 
				(
					SELECT j.*, k.code FROM
					(
						SELECT e.wid, e.locationid, e.uid, e.gid, e.tt as `won_tt`, f.name as `prizename` FROM `'.$table_winners.'` e LEFT JOIN
						`'.$table_prize.'` f on e.gid = f.goodieid WHERE e.redeem = 1
					) j LEFT JOIN  `'.$table_code.'` k ON j.gid = k.wid
				) l LEFT JOIN `'.DB_LOCATION.'` m ON l.locationid = m.id
			) g LEFT JOIN 
			(
				SELECT d.uid as `id`, d.name, d.first_name, d.last_name, d.sex, d.age, d.email, d.phone, d.ic FROM `'.$table_user.'` d
			) h ON g.uid = h.id
		) n LEFT JOIN
		(
			SELECT p1.* FROM ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p1 LEFT JOIN ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p2 ON ( p1.wid = p2.wid AND p1.tt < p2.tt ) WHERE p2.tt is NULL
		) o ON n.wid = o.wid
		';
}


//get all winners

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
	
	if($search){
		 $query .="WHERE LOWER(email) LIKE LOWER(?) OR LOWER(name) LIKE LOWER(?) OR LOWER(location_name) LIKE LOWER(?)";
		 $queryAll .=" WHERE LOWER(email) LIKE LOWER(?) OR LOWER(name) LIKE LOWER(?) OR LOWER(location_name) LIKE LOWER(?)";
		 $insertQ[] = '%'.$search.'%';
		 $insertQ[] = '%'.$search.'%';
		 $insertQ[] = '%'.$search.'%';
	}
	$total = $conn->GetOne($queryAll, $insertQ);
	
	$query .=" ORDER BY `".$sort."` ".$order;
	$query .=" LIMIT ".$offset.", ".$limit;
	
	$rawdata = $conn->GetArray($query, $insertQ);
	
	$data = array(
		"total"=>$total,
		"rows"=>$rawdata,
		"query"=>$query
	);
}
echo json_encode($data);