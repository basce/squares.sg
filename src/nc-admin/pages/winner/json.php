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
		
		$queryAll = 'SELECT COUNT(*) FROM `'.$table_winners.'`';
		$query = 'SELECT h.*, g.code, g.wid, ( CASE WHEN g.redeem = 1 THEN \'yes\' ELSE \'no\' END ) as `redeem`, g.gid, g.won_tt, g.prizename FROM ( SELECT j.*, k.code FROM ( SELECT e.wid, e.redeem, e.uid, e.gid, e.tt as `won_tt`, f.name as `prizename` FROM `'.$table_winners.'` e LEFT JOIN `'.$table_prize.'` f on e.gid = f.goodieid ) j LEFT JOIN `'.$table_code.'` k on j.wid = k.wid ) g
		LEFT JOIN 
		(
		SELECT d.uid as `id`, 
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
		d.ip,
		d.tt,
		Coalesce(d.fbverified, 0) as `fbverified`,
		Coalesce(d.fblikeshown, 0) as `fblikeshown`,
		(Coalesce(c.viashare, 0) + Coalesce(c.viaemail, 0)) as `share`,
		(Coalesce(c.total, 0) - Coalesce(c.viashare, 0) - Coalesce(c.viaemail, 0)) as `apprequest`,
		Coalesce(c.total, 0) as `referral`
		FROM ( SELECT b.*, a.fbid FROM `'.$table_user_fbid.'` a join `'.$table_user.'` b on a.uid = b.uid ) d
		LEFT JOIN ( SELECT uid, SUM(total_success) as `total`, SUM(success_fbshares) as `viashare`, SUM(success_fbinvitations) as `viaapprequest`, SUM(success_email) as `viaemail` FROM `'.$table_social.'` GROUP BY uid ) c ON c.uid = d.uid ) h ON g.uid = h.id';

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
	$insertQ = array();
	if($search){
		 $query .=" WHERE LOWER(email) LIKE LOWER(?) OR LOWER(name) LIKE LOWER(?) OR LOWER(phone) LIKE LOWER(?)";
		 $queryAll .=" WHERE LOWER(email) LIKE LOWER(?) OR LOWER(name) LIKE LOWER(?) OR LOWER(phone) LIKE LOWER(?)";
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