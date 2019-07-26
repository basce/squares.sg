<?php
header('Content-Type: application/json');
require_once(__dir__."../../../inc/main.php");

$conn = ncapputil::getConnection();

$adminManager = new admin(array(
	"conn"=>$conn,
	"table"=>DB_ADMIN
));
$user = $adminManager->validToken();
if(!$user){
	exit("require admin login");
}

$method = isset($_REQUEST["method"]) ? $_REQUEST["method"] : "";
switch($method){
	case "add":
		$uid = isset($_REQUEST["uid"]) ? $_REQUEST["uid"] : 0;
		$gid = isset($_REQUEST["gid"]) ? $_REQUEST["gid"] : 0;
		echo json_encode(setWinner($uid, $gid));
	break;
	case "getUniqueNumber":
		$conn = ncapputil::getConnection();
		$table_user  = DB_PREFIX."__users";
		$num = 0;
		$query = "SELECT COUNT(*) FROM `".$table_user."`";
		$num = $conn->GetOne($query);
		echo json_encode(array(
			"num"=>$num
		));
	break;
	case "getChart":
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

		$genderQuery = 'SELECT COUNT(*) as `amount`, CONCAT(UCASE(LEFT(sex, 1)), LCASE(SUBSTRING(sex, 2))) as `name`, sex as `label` FROM `'.$table_user.'` GROUP BY sex ORDER BY amount DESC';
		$gendersdata = $conn->GetArray($genderQuery);

		if(strpos(APP_PERM,"user_birthday") === FALSE && USE_AGE == "0"){
		  $ageQuery = 'SELECT COUNT(*) as `amount`, name FROM ( SELECT uid as `id` ,(
		                        CASE
		                          WHEN age = 0 THEN 
		                            CASE 
		                              WHEN age_range = "{\"min\":21}" THEN "> 21"
		                              WHEN age_range = "{\"max\":20,\"min\":18}" THEN "18 - 21"
		                              WHEN age_range = "{\"max\":17,\"min\":13}" THEN "< 18"
		                              ELSE age_range
		                            END
		                          WHEN age > 21 THEN "> 21"
		                          WHEN age > 18 THEN "18 - 21"
		                          ELSE "< 18"
		                        END
		                        ) as `name` FROM `'.$table_user.'`

		                    ) z GROUP BY name ORDER BY amount DESC';
		  $agesdata = $conn->GetArray($ageQuery);
		  
		}else{
		  $ageQuery = 'SELECT COUNT(*) as `amount`, name FROM ( SELECT uid as `id` ,(
		                        CASE
		                          WHEN age > 35 THEN "> 35"
		                          WHEN age > 20 THEN "21 - 35"
		                          ELSE "< 21"
		                        END
		                        ) as `name` FROM `'.$table_user.'`
		) z GROUP BY name ORDER BY amount DESC';
		  $agesdata = $conn->GetArray($ageQuery);
		}

		$countryQuery = 'SELECT COUNT(*) as `amount`, UCASE(country) as `name`, country as `label`  FROM `'.$table_user.'` GROUP BY country ORDER BY amount DESC';
		$countriesdata = $conn->GetArray($countryQuery);
		
		echo json_encode(array(
			"gender"=>$gendersdata,
			"age"=>$agesdata,
			"country"=>$countriesdata,
			"genderQ"=>$genderQuery,
			"ageQ"=>$ageQuery,
			"countryQ"=>$countryQuery
		));
	break;
}
