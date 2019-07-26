<?php
//configuration file
//SERVER RELATED
error_reporting(0);

date_default_timezone_set('Asia/Singapore');

require_once(__dir__.'../../../_libs/adodb5/adodb.inc.php');			
require_once(__dir__.'../../../_libs/adodb5/adodb-exceptions.inc.php'); 	
//require_once(__dir__.'../../../_libs/facebook/v3_2_3/facebook.php');
require_once(__dir__.'../../../_libs/facebook/v5_5_0/autoload.php');

//facebook key and secret
define("APP_ID","[[APP_ID]]");
define("APP_SECRET","[[APP_SECRET]]");
define("FB_PATH", "");
define("FOLDER_TO_ROOT","");
define("SERVER_PATH", "[[SERVER_PATH]]");
define("APP_PERM",""); /* insight report will user age_range rather than age column if user_birthday is missing */
define("APP_NAME", "[[APP NAME]]");
define("APP_NAMESPACE", "[[NAME SPACE]]");

define("SENDER_EMAIL","no-reply@campaigns.sg");
define("SENDER_NAME","no-reply");
define("CLIENT_NAME", "[[CLIENT NAME]]");
define("CAMPAIGN_STARTDATE","2019-01-01");
define("CAMPAIGN_ENDDATE","2200-01-01");
define("USE_AGE", "0"); /* insight report will read age column even no user_birthday permission */
define("GAMEMODE", "NUMBEROFWIN"); // game type, NUMBEROFDRAW or NUMBEROFWIN
define("NUMBEROFDRAW", 1); //number of draw allow
define("NUMBEROFWIN", 6); //number of win allow, choose gamemode
define("REDEMPTIONCODESTRING", "QAZWSXEDCRFVTGBYHNUJMIKOLP1234567890");
define("DEFAULT_LANGUAGE","en_GB");

if(time() > strtotime(CAMPAIGN_STARTDATE) + 604800){ //if currently time is more than a week from the campaign start date
	define('REPORT_INTERVAL','daily');
}else{
	define('REPORT_INTERVAL','hourly');
}

//settings depending on app status
define('STATUS', 'staging');
if(STATUS=='live'):
	define("App_Version", '');
	define("GOOGLE_ANALYTIC",'');
	
	define("FANPAGE_ID","");
	define("FANPAGE_URL","");
	
	define("FANPAGE_NAME","");
	define("FANPAGE_TITLE", "");	

	define("SKIPREG", TRUE);

	define("DEBUG", FALSE);
	define("DB_DEBUG", FALSE);
	
	define("DAILY_CHANCE", 1);
		
	define("INCLUDE_EMAIL","");
	define("INCLUDE_EMAIL_2","");
	define("CACHE_ENABLED", true);
	
elseif(STATUS=='staging'):
	define("App_Version", date('ymdHis'));
	define("GOOGLE_ANALYTIC","");
	
	define("FANPAGE_ID",""); 
	define("FANPAGE_URL","");
	
	define("FANPAGE_NAME","");
	define("FANPAGE_TITLE","");

	define("SKIPREG", FALSE);

	define("DEBUG", FALSE);
	define("DB_DEBUG", FALSE);
	
	define("DAILY_CHANCE", 1); 

	define("INCLUDE_EMAIL",""); //put your email here for inserting to bcc list
	define("INCLUDE_EMAIL_2","");
	define("CACHE_ENABLED", FALSE);

endif;

define("FANPAGE_TAB_MOBILE",""); //require ref=profile for mobile hack
define("FANPAGE_TAB","");

define("ForceLike", false);
define("PAGE_TITLE","");
define("PAGE_DESCRIPTION","[[PAGE DESCRIPTION]]");

define('DBHOST', '[[MYSQL HOST]]');
define('DBUSERNAME','[[USERNAME]]');
define('DBPASSWORD','[[PASSWORD]]');
define('DBNAME','[[DB NAME]]');
define("DB_DRIVER", "mysqli://".DBUSERNAME.":".DBPASSWORD."@".DBHOST."/".DBNAME);

define('DB_PREFIX', '[[PREFIX]]');
define('DB_VAR',DB_PREFIX.'__settings');
define('DB_PRIZE',DB_PREFIX.'__prizes');
define('DB_CODE', DB_PREFIX.'__code');
define('DB_SOCIALLEAD',DB_PREFIX.'__socialleaderboard');
define('DB_JOINNOTIFY', DB_PREFIX.'__join_notification');
define('DB_REV_JOINNOTIFY', DB_PREFIX.'__join_rev_notification');
define('DB_ADMIN', DB_PREFIX.'__admin');
define('DB_DRAW',DB_PREFIX.'__drawtry');
define('DB_USER',DB_PREFIX.'__users');
define('DB_USERFBID',DB_PREFIX.'__users_fbid');
define('DB_FBINVITE',DB_PREFIX.'__fbinvitations');
define('DB_FBSHARE',DB_PREFIX.'__fbshares');
define('DB_REFERRAL',DB_PREFIX.'__referral');
define('DB_WINNER',DB_PREFIX.'__winners');
define('DB_REDEEMLOG', DB_PREFIX.'__redeemlog');

define("DB_TOKEN", DB_PREFIX."__tokens");
define("DB_PW", DB_PREFIX."__password");
define("DB_PW_SALT", DB_PREFIX."__password_salt");
define("DB_PW_REQUEST", DB_PREFIX."__password_request");

define('DB_VOTE', DB_PREFIX.'__vote');
define('DB_DESIGNER', DB_PREFIX.'__designer');
define('DB_SUBMISSION', DB_PREFIX.'__submission');
define('DB_SUBMISSION_ITEM', DB_PREFIX.'__submission_item');
define('DB_SE_WINNER_TOP3', DB_PREFIX.'__se_top3_winners');
define('DB_SE_WINNER', DB_PREFIX.'__se_winners');

//define('DB_ANSWER', DB_PREFIX.'__answers');
//define('DB_SCORELEADERBOARD', DB_PREFIX.'__scoreleaderboard');
define('DB_LOCATION', DB_PREFIX.'__location');
/*
define('DB_GAME', DB_PREFIX.'__game');
define('DB_GAMEDATA', DB_PREFIX.'__gamedata');
*/
define('gameplay_table_count_for_insight', DB_VOTE);

define('SOCIAL_SHARING_PERIOD', false);

//DKIM
define('USE_LOCAL_DKIM', false);
define('DKIM_DOMAIN', ''); //'pizzaexpress.life');
define('DKIM_PRIVATE_FILENAME', ''); //'1540778959.pizzaexpress.pem');
define('DKIM_SELECTOR', ''); //'1540778959.pizzaexpress');

define('USE_AWS_SMTP', true);
define('AWS_PORT', 587);
define('AWS_SMTP_ENDPOINT', '[[AWS END POINT]]');
define('AWS_SES_LABEL', '[[AWS LABEL]]');
define('AWS_SMTP_USERNAME', '[[AWS USERNAME]]');
define('AWS_SMTP_PASSWORD', '[[AWS PASSWORD]]');

define('VOTE_START', '2019-07-15 00:00:00');
define('VOTE_CLOSE', '2019-07-30 00:00:00');
define('WINNER_ANNOUNCED', false);

//campaing phase
function perioddata(){
	return array(
		array(
			"id"=>1,
			"start"=>"2017-01-01 00:00:00",
			"end"=>"2117-01-01 00:00:00"
		)
	);
}

//GetCurrentTimeSID
function getSID(){
	$period = perioddata();
	
	$currentTime = time();
	$sid = 0; //default is 0;
	$timeleft = 0;
	foreach($period as $key=>$value){
		if($currentTime >= strtotime($value["start"]) && $currentTime < strtotime($value["end"])){
			$sid = $value["id"];
			$timeleft = strtotime($value["end"]) - $currentTime;
		}
	}
	
	return array(
		"period"=>$period,
		"current"=>$sid,
		"left"=>$timeleft
	);
}


//for preload images.
$preload_images =array();
if ($dh = opendir('images/')) {
	while (($file = readdir($dh)) !== false) {
		if(preg_match('/(png|jpg|jpeg|bmp|gif)/i',$file)){
			$preload_images[] = "images/".$file;
		}
	}
	closedir($dh);
}
if ($dh = opendir('images/apps/')) {
	while (($file = readdir($dh)) !== false) {
		if(preg_match('/(png|jpg|jpeg|bmp|gif)/i',$file)){
			$preload_images[] = "images/apps/".$file;
		}
	}
	closedir($dh);
}
if(sizeof($preload_images)){
	$onloadscript = "preload(['".implode("','",$preload_images)."']);";
	$manifest_images = json_encode($preload_images);
}else{
	$onloadscript = "";
	$manifest_images = "[]";
}
?>