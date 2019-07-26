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

function setWinner($uid, $gid){
	global $user, $adminManager;
	$conn = ncapputil::getConnection();
	if($user["level"] == 1 || $user["level"] == 2){
		if(!$gid || !$uid){
			return array(
				"success"=>0,
				"msg"=>"Set Winner success. new wid :".$wid
			);
		}else{
			$main = new main();
			$wid = $main->setWinner($uid, $gid);
			if($wid){
				return array(
					"success"=>1,
					"msg"=>"Set Winner success. new wid :".$wid
				);
			}else{
				return array(
					"success"=>0,
					"msg"=>"Set winner failed."
				);
			}
			return array(
				"success"=>0,
				"msg"=>"test"
			);
		}
	}else{
		return array(
			"success"=>0,
			"msg"=>"You don't have permission to edit winner."
			);
	}
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
		$ar = array();

		$locationid = isset($_REQUEST["locationid"])?$_REQUEST["locationid"]:0;

		$query = "SELECT COUNT(*) FROM `".DB_WINNER."` WHERE redeem = 1";
		$totalcount = $conn->GetOne($query);

		$query = "SELECT COUNT(*) FROM ( SELECT uid FROM `".DB_WINNER."` WHERE redeem = 1 GROUP BY uid) a";
		$totalunqiuecount = $conn->GetOne($query);
		$totalunqiuecount = $totalunqiuecount?$totalunqiuecount:0;

		//all location
		$ar[] = array(
			"id"=>0,
			"name"=>"All",
			"count"=>$totalcount,
			"uniquecount"=>$totalunqiuecount
		);

		$num = $totalunqiuecount;

		$query = "SELECT id, name FROM `".DB_LOCATION."` ORDER BY name";
    	$result = $conn->GetArray($query);
    	foreach($result as $key=>$value){
        	$query = "SELECT COUNT(*) FROM `".DB_WINNER."` WHERE redeem = 1 AND locationid = ?";
        	$numcount = $conn->GetOne($query, array($value["id"]));

        	$query = "SELECT COUNT(*) FROM ( SELECT uid FROM `".DB_WINNER."` WHERE redeem = 1 AND locationid = ? GROUP BY uid) a";
        	$uniquecount = $conn->GetOne($query, array($value["id"]));
        	$uniquecount = $uniquecount? $uniquecount : 0;

        	$ar[] = array(
				"id"=>$value["id"],
				"name"=>$value["name"],
				"count"=>$numcount,
				"uniquecount"=>$uniquecount
			);

        	if((int)$locationid && $value["id"] == $locationid){
        		$num = $uniquecount;
        	}
        }

		echo json_encode(array(
			"num"=>$num,
			"ar"=>$ar
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

		$locationid = isset($_REQUEST["locationid"])?$_REQUEST["locationid"]:0;

		if((int)$locationid){
			$genderQuery = 'SELECT COUNT(*) as `amount`, CONCAT(UCASE(LEFT(sex, 1)), LCASE(SUBSTRING(sex, 2))) as `name`, sex as `label` FROM (
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
						SELECT d.uid as `id`, d.name, d.first_name, d.last_name, d.sex, d.age, d.country, d.email, d.phone, d.ic FROM `'.$table_user.'` d
					) h ON g.uid = h.id
				) n LEFT JOIN
				(
					SELECT p1.* FROM ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p1 LEFT JOIN ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p2 ON ( p1.wid = p2.wid AND p1.tt < p2.tt ) WHERE p2.tt is NULL
				) o ON n.wid = o.wid
				) z GROUP BY sex ORDER BY amount DESC';
			$gendersdata = $conn->GetArray($genderQuery, array($locationid));

				if(strpos(APP_PERM,"user_birthday") === FALSE && USE_AGE == "0"){
				  $ageQuery = 'SELECT COUNT(*) as `amount`, name FROM ( SELECT n.id ,(
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
				                        ) as `name` FROM SELECT n.*, o.tt as `redeemed_time` FROM
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
						SELECT d.uid as `id`, d.name, d.first_name, d.last_name, d.sex, d.age, d.country, d.email, d.phone, d.ic FROM `'.$table_user.'` d
					) h ON g.uid = h.id
				) n LEFT JOIN
				(
					SELECT p1.* FROM ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p1 LEFT JOIN ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p2 ON ( p1.wid = p2.wid AND p1.tt < p2.tt ) WHERE p2.tt is NULL
				) o ON n.wid = o.wid
				) a GROUP BY name ORDER BY amount DESC';
				  $agesdata = $conn->GetArray($ageQuery, array($locationid));
				  
				}else{
				  $ageQuery = 'SELECT COUNT(*) as `amount`, name FROM ( SELECT n.id ,(
				                        CASE
				                          WHEN age > 35 THEN "> 35"
				                          WHEN age > 20 THEN "21 - 35"
				                          ELSE "< 21"
				                        END
				                        ) as `name` FROM (
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
						SELECT d.uid as `id`, d.name, d.first_name, d.last_name, d.sex, d.age, d.country, d.email, d.phone, d.ic FROM `'.$table_user.'` d
					) h ON g.uid = h.id
				) n LEFT JOIN
				(
					SELECT p1.* FROM ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p1 LEFT JOIN ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p2 ON ( p1.wid = p2.wid AND p1.tt < p2.tt ) WHERE p2.tt is NULL
				) o ON n.wid = o.wid
				) a GROUP BY name ORDER BY amount DESC';
				  $agesdata = $conn->GetArray($ageQuery, array($locationid));
				}

				$countryQuery = 'SELECT COUNT(*) as `amount`, UCASE(country) as `name`, country as `label`  FROM (
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
						SELECT d.uid as `id`, d.name, d.first_name, d.last_name, d.sex, d.age, d.country, d.email, d.phone, d.ic FROM `'.$table_user.'` d
					) h ON g.uid = h.id
				) n LEFT JOIN
				(
					SELECT p1.* FROM ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p1 LEFT JOIN ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p2 ON ( p1.wid = p2.wid AND p1.tt < p2.tt ) WHERE p2.tt is NULL
				) o ON n.wid = o.wid
				) z  GROUP BY country ORDER BY amount DESC';
				$countriesdata = $conn->GetArray($countryQuery, array($locationid));
		}else{
			$genderQuery = 'SELECT COUNT(*) as `amount`, CONCAT(UCASE(LEFT(sex, 1)), LCASE(SUBSTRING(sex, 2))) as `name`, sex as `label` FROM (
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
						SELECT d.uid as `id`, d.name, d.first_name, d.last_name, d.sex, d.age, d.country, d.email, d.phone, d.ic FROM `'.$table_user.'` d
					) h ON g.uid = h.id
				) n LEFT JOIN
				(
					SELECT p1.* FROM ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p1 LEFT JOIN ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p2 ON ( p1.wid = p2.wid AND p1.tt < p2.tt ) WHERE p2.tt is NULL
				) o ON n.wid = o.wid GROUP BY n.id
				) z GROUP BY sex ORDER BY amount DESC';
			$gendersdata = $conn->GetArray($genderQuery);

				if(strpos(APP_PERM,"user_birthday") === FALSE && USE_AGE == "0"){
				  $ageQuery = 'SELECT COUNT(*) as `amount`, name FROM ( SELECT n.id ,(
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
				                        ) as `name` FROM SELECT n.*, o.tt as `redeemed_time` FROM
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
						SELECT d.uid as `id`, d.name, d.first_name, d.last_name, d.sex, d.age, d.country, d.email, d.phone, d.ic FROM `'.$table_user.'` d
					) h ON g.uid = h.id
				) n LEFT JOIN
				(
					SELECT p1.* FROM ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p1 LEFT JOIN ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p2 ON ( p1.wid = p2.wid AND p1.tt < p2.tt ) WHERE p2.tt is NULL
				) o ON n.wid = o.wid GROUP BY n.id
				) a GROUP BY name ORDER BY amount DESC';
				  $agesdata = $conn->GetArray($ageQuery);
				  
				}else{
				  $ageQuery = 'SELECT COUNT(*) as `amount`, name FROM ( SELECT n.id ,(
				                        CASE
				                          WHEN age > 35 THEN "> 35"
				                          WHEN age > 20 THEN "21 - 35"
				                          ELSE "< 21"
				                        END
				                        ) as `name` FROM (
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
						SELECT d.uid as `id`, d.name, d.first_name, d.last_name, d.sex, d.age, d.country, d.email, d.phone, d.ic FROM `'.$table_user.'` d
					) h ON g.uid = h.id
				) n LEFT JOIN
				(
					SELECT p1.* FROM ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p1 LEFT JOIN ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p2 ON ( p1.wid = p2.wid AND p1.tt < p2.tt ) WHERE p2.tt is NULL
				) o ON n.wid = o.wid GROUP BY n.id
				) a GROUP BY name ORDER BY amount DESC';
				  $agesdata = $conn->GetArray($ageQuery);
				}

				$countryQuery = 'SELECT COUNT(*) as `amount`, UCASE(country) as `name`, country as `label`  FROM (
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
						SELECT d.uid as `id`, d.name, d.first_name, d.last_name, d.sex, d.age, d.country, d.email, d.phone, d.ic FROM `'.$table_user.'` d
					) h ON g.uid = h.id
				) n LEFT JOIN
				(
					SELECT p1.* FROM ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p1 LEFT JOIN ( SELECT * FROM `'.DB_REDEEMLOG.'` WHERE action = "redeem" ) p2 ON ( p1.wid = p2.wid AND p1.tt < p2.tt ) WHERE p2.tt is NULL
				) o ON n.wid = o.wid GROUP BY n.id
				) z  GROUP BY country ORDER BY amount DESC';
				$countriesdata = $conn->GetArray($countryQuery);
		}
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
