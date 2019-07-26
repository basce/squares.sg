<?php
$conn = ncapputil::getConnection();
$reportManager = new reporting(array("conn"=>ncapputil::getConnection()));

$startingdate = CAMPAIGN_STARTDATE;
$endingdate = CAMPAIGN_ENDDATE;

$startingdatelabel = date("j F",strtotime($startingdate));
$endingdatelabel = date("j F",strtotime($endingdate." -1 days"));
$weekdurationsecond = 60*60*24*7;
$numbersofweek = ceil( ( strtotime($endingdate) - strtotime($startingdate) ) / $weekdurationsecond );
if(time() < strtotime($startingdate)){
  $currentWeek = 0;
}else if(time() < strtotime($endingdate)){
  $currentWeek = ceil( (time() - strtotime($startingdate)) / $weekdurationsecond );
}else{
  $currentWeek = $numbersofweek;
}
$table_user  = DB_PREFIX."__users";
$table_user_fbid  = DB_PREFIX."__users_fbid";
$table_fbinvite = DB_PREFIX."__fbinvitations";
$table_invite = DB_PREFIX."__invite";
$table_referral = DB_PREFIX."__referral";
$table_prize = DB_PREFIX."__prizes";
$table_social = DB_PREFIX."__socialleaderboard";
//$table_quiz = DB_PREFIX."__quiz";
$table_draw = gameplay_table_count_for_insight; //DB_PREFIX."__drawtry";

/*get statistic */
$num_participant = $conn->GetOne("SELECT COUNT(*) FROM `".$table_user."`");
//$num_quiz_done = $conn->GetOne("SELECT COUNT(*) FROM `".$table_quiz."`");
$num_drawtry = $conn->GetOne("SELECT COUNT(*) FROM `".$table_draw."`");

//demographic
$gendersdata = $conn->GetArray("SELECT COUNT(*) as `amount`, CONCAT(UCASE(LEFT(sex, 1)), LCASE(SUBSTRING(sex, 2))) as `name`, sex as `label` FROM `".$table_user."` GROUP BY sex ORDER BY amount DESC");
// for using fb_age_range
/*
$agesdata = $conn->GetArray("SELECT COUNT(*) as `amount`, (
                    CASE
                      WHEN age_range = '{\"min\":21}' THEN '> 21 y/o'
                      WHEN age_range = '{\"max\":17,\"min\":13}' THEN '13 - 17 y/o'
                      WHEN age_range = '{\"max\":20,\"min\":18}' THEN '18 - 20 y/o'
                      ELSE 'unknown'
                    END
                    ) as `name`, age_range as `label` FROM `".$table_user."` GROUP BY age_range ORDER BY amount DESC");
*/
// using age column
if(strpos(APP_PERM,"user_birthday") === FALSE && USE_AGE == "0"){
  $agesdata = $conn->GetArray("SELECT COUNT(*) as `amount`, name FROM ( SELECT uid ,(
                        CASE
                          WHEN age = 0 THEN 
                            CASE 
                              WHEN age_range = '{\"min\":21}' THEN \"> 21\"
                              WHEN age_range = '{\"max\":20,\"min\":18}' THEN \"18 - 21\"
                              WHEN age_range = '{\"max\":17,\"min\":13}' THEN \"< 18\"
                              ELSE age_range
                            END
                          WHEN age > 21 THEN \"> 21\"
                          WHEN age > 18 THEN \"18 - 21\"
                          ELSE \"< 18\"
                        END
                        ) as `name` FROM `".$table_user."` ) a GROUP BY name ORDER BY amount DESC");
  
  foreach($agesdata as $index=>$value){
    /* when using fb_age_range
    $agesdata[$index]["data"] = $reportManager->getRawData($table_user, "tt", true, " age_range = '".$value["label"]."'","",false);
    */
    switch($value["name"]){
      case "> 21":
        $agesdata[$index]["data"] = $reportManager->getRawData($table_user, "tt", true, " age_range = '{\"min\":21}","",false);
      break;
      case "18 - 21":
        $agesdata[$index]["data"] = $reportManager->getRawData($table_user, "tt", true, " age_range = '{\"max\":20,\"min\":18}'","",false);
      break;
      case "< 18":
        $agesdata[$index]["data"] = $reportManager->getRawData($table_user, "tt", true, " age_range = '{\"max\":17,\"min\":13}'","",false);
      break;
      default:
        $agesdata[$index]["data"] = $reportManager->getRawData($table_user, "tt", true, " age_range = '".$value["name"]."'","",false);
      break;
    }
  }
}else{
  $agesdata = $conn->GetArray("SELECT COUNT(*) as `amount`, name FROM ( SELECT uid ,(
                        CASE
                          WHEN age > 35 THEN \"> 35\"
                          WHEN age > 20 THEN \"21 - 35\"
                          ELSE \"< 21\"
                        END
                        ) as `name` FROM `".$table_user."` ) a GROUP BY name ORDER BY amount DESC");
                        
  foreach($agesdata as $index=>$value){
    /* when using fb_age_range
    $agesdata[$index]["data"] = $reportManager->getRawData($table_user, "tt", true, " age_range = '".$value["label"]."'","",false);
    */
    switch($value["name"]){
      case "> 35":
        $agesdata[$index]["data"] = $reportManager->getRawData($table_user, "tt", true, " age > 35","",false);
      break;
      case "21 - 35":
        $agesdata[$index]["data"] = $reportManager->getRawData($table_user, "tt", true, " age > 20 && age < 36","",false);
      break;
      default:
        $agesdata[$index]["data"] = $reportManager->getRawData($table_user, "tt", true, " age < 21","",false);
      break;
    }
  }

}
$countriesdata = $conn->GetArray("SELECT COUNT(*) as `amount`, UCASE(country) as `name`, country as `label`  FROM `".$table_user."` GROUP BY country ORDER BY amount DESC");
$languages = $conn->GetArray("SELECT COUNT(*) as `cnt`, locale as `value`, locale as `label` FROM `".$table_user."` GROUP BY locale");

$referral = $conn->GetOne("SELECT COUNT(*) FROM (SELECT COUNT(*) FROM `".$table_referral."` GROUP BY invitee) a");

  
foreach($gendersdata as $index=>$value){
  $gendersdata[$index]["data"] = $reportManager->getRawData($table_user, "tt", true, " sex = '".$value["label"]."'","",false);
}

foreach($countriesdata as $index=>$value){
  $countriesdata[$index]["data"] = $reportManager->getRawData($table_user, "tt", true, " country = '".$value["label"]."'","",false);
}
/*
foreach($languages as $index=>$value){
  $languages[$index]["data"] = $reportManager->getRawData($table_user, "tt", true, " locale = '".$value["name"]."'");
}
*/

$referrals = $reportManager->getRawData($table_user, "tt", true, "uid IN ( SELECT invitee FROM `".$table_referral."` GROUP BY invitee)","", false);
$totalusers = $reportManager->getRawData($table_user, "tt", true, "","", false);
$nonreferrals = array();
$tempkeycounter = 0;
foreach($totalusers as $key=>$value){
  if($value["tt"] == $referrals[$tempkeycounter]["tt"]){
    //date match, get value and go next item
    $substract = $referrals[$tempkeycounter]["amount"];
    $tempkeycounter++;
  }else{
    //data doesn't match
    $substract = 0;
  }
  $nonreferrals[] = array(
    "tt"=>$value["tt"],
    "amount"=>$value["amount"] - $substract
  );
}
function createDateRange($rawsdatas){
  //get the earliest date
  //get the last date
  $firsttt = "3000-12-31 00:00:00";
  $lasttt = "1000-12-31 00:00:00";
  foreach($rawsdatas as $key=>$value){
    if(sizeof($value["data"])){
      if($firsttt > $value["data"][0]["tt"]){
        $firsttt = $value["data"][0]["tt"];
      }
      if($lasttt < $value["data"][sizeof($value["data"])-1]["tt"]){
        $lasttt = $value["data"][sizeof($value["data"])-1]["tt"];
      }
    }
  }
  if($firsttt == "3000-12-31 00:00:00"){
    //no data found
    return array();
  }else{
    $returnAr = array();
    $ctt = $firsttt;
    while($ctt <= $lasttt){
      $returnAr[] = $ctt;
      $ctt = date('Y-m-d H:i:s', strtotime($ctt." +1 days"));
    }
    return $returnAr;
  }
}

function checkIFObjExist($dataArs, $name){
  foreach($dataArs as $key=>$value){
    if($value["name"] == $name){
      return true;
    }
  }
  return false;
}

function dataFill($rawdatas, $maxitem, $accumulate){
  $timeindexes = createDateRange($rawdatas);
  $indexdatas = array();
  foreach($rawdatas as $key=>$value){
    $indexdatas[$key] = array();
    foreach($value["data"] as $key2=>$value2){
      $indexdatas[$key][$value2["tt"]] = $value2["amount"];
    }
  }
  $finaldatas = array();
  foreach($rawdatas as $key=>$data){
    $dataAr = array();
    $vamount = 0;
    foreach($timeindexes as $key2=>$value2){
      $datetime = explode(" ",$value2);
      $date = explode("-",$datetime[0]);
      $time = explode(":",$datetime[1]);
      if(isset($indexdatas[$key][$value2])){
        if($accumulate){
          $vamount += $indexdatas[$key][$value2];
        }else{
          $vamount = $indexdatas[$key][$value2];
        } 
      }else{
        if(!$accumulate){
          $vamount = 0;
        }
      }
      $dataAr[] = $vamount;
    }
    $rawdatas[$key]["chartdata"] = $dataAr;
  }
  return $rawdatas;
}

//get influencer top 6 
// long fbid will have number issue, add a f infront to force it to be string.
$query = " SELECT c.uid, CONCAT('f',d.fbid) as fbid, d.name, (Coalesce(c.viashare, 0) + Coalesce(c.viaemail, 0)) as `share`,
    (Coalesce(c.total, 0) - Coalesce(c.viashare, 0) - Coalesce(c.viaemail, 0)) as `apprequest`,
    Coalesce(c.total, 0) as `referral` FROM ( SELECT uid, SUM(total_success) as `total`, SUM(success_fbshares) as `viashare`, SUM(success_fbinvitations) as `viaapprequest`, SUM(success_email) as `viaemail` FROM `".$table_social."` GROUP BY uid ) c
       LEFT JOIN ( SELECT b.uid, a.fbid, b.name FROM `".$table_user_fbid."` a join `".$table_user."` b on a.uid = b.uid ) d ON c.uid = d.uid
       ORDER BY c.total DESC LIMIT 6";

$influencer = $conn->GetArray($query);

//get fan number
/*
$cache = new Memcached;
$cache->addServer('localhost', 11211) or die ("Cannot connect to Memcache");

$fannumber = $cache->get("FB_".FANPAGE_ID);
if($fannumber === FALSE){
  try{
    $fbm = new fbmanager(array());

    $token_url = "https://graph.facebook.com/oauth/access_token?" .
          "client_id=" . APP_ID .
          "&client_secret=" . APP_SECRET .
          "&grant_type=client_credentials";
    $app_access_token = file_get_contents($token_url);
    $at = explode("=",$app_access_token);

    if(sizeof($at) > 1){
      $appAccessToken = $at[1];
    }else{
      $tempar = json_decode($app_access_token, true);
      $appAccessToken = $tempar["access_token"];
    }

    $obj = array();
    $obj['access_token'] = $appAccessToken;
    $fb = $fbm->getFB();
    $query = http_build_query(array("fields"=>"fan_count"));
    $response = $fb->get(FANPAGE_ID."?".$query, $appAccessToken);
    $result = json_decode($response->getBody(), true);
    $fannumber = ($result && $result["fan_count"] )? $result["fan_count"] : FALSE;
    
  }catch(Exception $e){
    $fannumber = FALSE;
  }
  if($fannumber !== FALSE){
    $cache->set("FB_".FANPAGE_ID, $fannumber, 86400 ); // cache for 1 day
  }
}
*/
/*
change the structure so we can use the function
*/
$referralsar = array(
  array(
    "name"=>"Referred",
    "data"=>$referrals
  ),
  array(
    "name"=>"Organic",
    "data"=>$nonreferrals
  ));

//setup
$client_name = CLIENT_NAME;
$app_name = APP_NAME;
//$duration = "5 weeks";
$duration = "Week ".$currentWeek." of ".$numbersofweek;
$daterange = $startingdatelabel." - ".$endingdatelabel;


$totalactions = $num_drawtry == 1 ? "1 play" : number_format($num_drawtry,0)." plays";
$useraction = "Gameplays";
$totalusers= number_format($num_participant,0);
$fannumber = $fannumber?$fannumber:0;
$fancount = ($fannumber == 1 )? "1 fan" : number_format($fannumber,0)." fans";
$genderPerData = json_encode(dataFill($gendersdata, false), JSON_NUMERIC_CHECK);
$agePerData = json_encode(dataFill($agesdata, false), JSON_NUMERIC_CHECK);
$countryPerData = json_encode(dataFill($countriesdata, false), JSON_NUMERIC_CHECK);

$dateLabelRange = array();
$tempr = createDateRange($gendersdata); //one can be use for all
foreach($tempr as $key=>$value){
  $dateLabelRange[] = date('j M',strtotime($value));
}
$dateLabelRangeJs = json_encode($dateLabelRange);

$referamount = $referral == 1 ? "1 Person":$referral." People";
$averageRefer = sizeof($tempr) > 0 ? number_format($referral/sizeof($tempr), 2):0;
$referPerc = number_format($referral / $num_participant*100,1)."%";

$referralC = json_encode(dataFill($referralsar,false), JSON_NUMERIC_CHECK);

$tempreferraldata = dataFill($referralsar,false);
//get the maximum date
$maxs = array_keys($tempreferraldata[0]["chartdata"], max($tempreferraldata[0]["chartdata"]));
$mins = array_keys($tempreferraldata[0]["chartdata"], min($tempreferraldata[0]["chartdata"]));

$highestDate = date("j/n/y",strtotime($tempr[$maxs[0]]));
$highestAmount = $tempreferraldata[0]["chartdata"][$maxs[0]];
$lowestDate = date("j/n/y",strtotime($tempr[$mins[0]]));
$lowestAmount = $tempreferraldata[0]["chartdata"][$mins[0]];

//top influencer
$topinfluencer = json_encode($influencer, JSON_NUMERIC_CHECK);
?>

  
    <section id="dashboard" class="container">
      <div class="row1 row">
          <div class="col-sm-6">
              <div class="itemtype1">
                  <img src="<?=$ownfolder?>images/icon1.png" alt="calender">
                    <h2><?=$duration?></h2>
                    <label><?=$daterange?></label>
                </div>
            </div>
            <div class="col-sm-6">
              <div class="itemtype1">
                  <img src="<?=$ownfolder?>images/icon2.png" alt="people">
                    <h2><?=$totalactions?></h2>
                    <label><?=$useraction?></label>
                </div>
            </div>
            <!--
            <div class="col-sm-4">
              <div class="itemtype1">
                  <img src="<?=$ownfolder?>images/icon3.png" alt="like icon">
                    <h2><?=$fancount?></h2>
                    <label>on Facebook</label>
                </div>
            </div>
          -->
        </div>
        <div class="row2">
          <div class="col-sm-4">
              <div class="itemtype2">
                  <h1><?=$totalusers?></h1>
                    <label>Total Participants</label>
                    <a href="#demographic" type="button" class="btn btn-more page-scroll">
                      More
                      <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>
                    </a>
                </div>
            </div>
            <div class="col-sm-4">
              <div class="majorbar floatrect c2r3">
                  <div class="content">
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
              <div class="itemtype2">
                  <h1><?=$referPerc?></h1>
                    <label>Referred</label>
                    <a href="#refparticipants" type="button" class="btn btn-more page-scroll">
                      More
                      <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <section id="topinfluencers" class="container">
      <div class="row">
          <h3 class="subtitle">Top Influencers</h3>
        </div>
      <div class="row">
            <div class="col-sm-6">
              <!--
                <div class="influencer media">
                    <div class="media-left">
                        <img src="" alt="icon" width="100" height="100" class="media-object">
                    </div>
                    <div class="influencer_info media-body">
                        <h4 class="name media-heading">
                            Dian Hafiza
                        </h4>
                        <p class="social_info">
                            <span class="activated_text"><span class="emnumber">80</span> People Activated</span>
                            <span class="detail_text"><span class="emnumber">999</span> by Share <span class="emnumber">999</span> by Invite</span>
                        </p>
                    </div>
                </div>
                <div class="influencer media">
                    <div class="media-left">
                        <img src="" alt="icon" width="100" height="100" class="media-object">
                    </div>
                    <div class="influencer_info media-body">
                        <h4 class="name media-heading">
                            Dian Hafiza
                        </h4>
                        <p class="social_info">
                            <span class="activated_text"><span class="emnumber">80</span> People Activated</span>
                            <span class="detail_text"><span class="emnumber">999</span> by Share <span class="emnumber">999</span> by Invite</span>
                        </p>
                    </div>
                </div>
                <div class="influencer media">
                    <div class="media-left">
                        <img src="" alt="icon" width="100" height="100" class="media-object">
                    </div>
                    <div class="influencer_info media-body">
                        <h4 class="name media-heading">
                            Dian Hafiza
                        </h4>
                        <p class="social_info">
                            <span class="activated_text"><span class="emnumber">80</span> People Activated</span>
                            <span class="detail_text"><span class="emnumber">999</span> by Share <span class="emnumber">999</span> by Invite</span>
                        </p>
                    </div>
                </div>
                -->
            </div>
            <div class="col-sm-6">
              <!--
              <div class="influencer media">
                    <div class="media-left">
                        <img src="" alt="icon" width="100" height="100" class="media-object">
                    </div>
                    <div class="influencer_info media-body">
                        <h4 class="name media-heading">
                            Dian Hafiza
                        </h4>
                        <p class="social_info">
                            <span class="activated_text"><span class="emnumber">8220</span> People Activated</span>
                            <span class="detail_text"><span class="emnumber">999</span> by Share <span class="emnumber">999</span> by Invite</span>
                        </p>
                    </div>
                </div>
                <div class="influencer media">
                    <div class="media-left">
                        <img src="" alt="icon" width="100" height="100" class="media-object">
                    </div>
                    <div class="influencer_info media-body">
                        <h4 class="name media-heading">
                            Dian Hafiza
                        </h4>
                        <p class="social_info">
                            <span class="activated_text"><span class="emnumber">80</span> People Activated</span>
                            <span class="detail_text"><span class="emnumber">999</span> by Share <span class="emnumber">999</span> by Invite</span>
                        </p>
                    </div>
                </div>
                <div class="influencer media">
                    <div class="media-left">
                        <img src="" alt="icon" width="100" height="100" class="media-object">
                    </div>
                    <div class="influencer_info media-body">
                        <h4 class="name media-heading">
                            Dian Hafiza
                        </h4>
                        <p class="social_info">
                            <span class="activated_text"><span class="emnumber">80</span> People Activated</span>
                            <span class="detail_text"><span class="emnumber">999</span> by Share <span class="emnumber">999</span> by Invite</span>
                        </p>
                    </div>
                </div>
            -->
            </div>
        </div>
        <div class="row">
          <a href="#userdetails" type="button" class="btn btn-more pull-right page-scroll">
                More
                <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>
            </a>
        </div>
    </section>
    <section class="container-fluid" id="demographic">
      <div class="row sectiontitle2">
          <h1>Demographics</h1>
        </div>
      <div class="container">
          <div class="row row3">
                <div class="col-sm-4 demochart">
                    <div class="row row-no-padding">
                        <div class="col-xs-5 col-sm-5">
                            <div class="category">Gender</div>
                            <div class="floatrect">
                                <div id="genderdonut" class="content">
                                </div>
                            </div>
                            <div class="dominant"></div>
                        </div>
                        <div class="col-xs-7 col-sm-7">
                            <div class="legends" id="genderlegend">
                                
                            </div>
                        </div>
                    </div>
                    <div class="floatrect">
                      <div class="content" id="genderbarchart"></div>
                    </div>
                </div>
                <div class="col-sm-4 demochart">
                    <div class="row row-no-padding">
                        <div class="col-xs-5 col-sm-5">
                            <div class="category">Country</div>
                            <div class="floatrect">
                                <div id="countrydonut" class="content">
                                </div>
                            </div>
                            <div class="dominant"></div>
                        </div>
                        <div class="col-xs-7 col-sm-7">
                            <div class="legends" id="countrylegend">
                               
                            </div>
                        </div>
                    </div>
                    <div class="floatrect">
                      <div class="content" id="countrybarchart"></div>
                    </div>
                </div>
                <div class="col-sm-4 demochart">
                    <div class="row row-no-padding">
                        <div class="col-xs-5 col-sm-5">
                            <div class="category">Age</div>
                            <div class="floatrect">
                                <div id="agedonut" class="content">
                                </div>
                            </div>
                            <div class="dominant"></div>
                        </div>
                        <div class="col-xs-7 col-sm-7">
                            <div class="legends" id="agelegend">
                                
                            </div>
                        </div>
                    </div>
                    <div class="floatrect">
                      <div class="content" id="agebarchart"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="container-fluid" id="refparticipants">
      <div class="row sectiontitle2">
          <h1>Referred Participants</h1>
        </div>
      <div class="container">
            <h2 class="subtitle"><?=$referPerc?> of participants are referred</h2>
            <div class="row">
              <div class="hightlineitems col-md-8">
                  <div class="col-sm-6">
                      <div class="row">
                            <div class="col-xs-6 col-sm-6">
                                <div class="highlineitem">
                                    <h4>Referred</h4>
                                    <em><?=$referamount?></em>
                                    <label></label>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6">
                                <div class="highlineitem">
                                    <h4>Average per Day</h4>
                                    <em><?=$averageRefer?></em>
                                    <label></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="row">
                            <div class="col-xs-6 col-sm-6">
                                <div class="highlineitem">
                                    <h4>Highest on</h4>
                                    <em><?=$highestDate?></em>
                                    <label><?=($highestAmount == 1 ? "1 Person":$highestAmount." People")?></label>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6">
                                <div class="highlineitem">
                                    <h4>Lowest</h4>
                                    <em><?=$lowestDate?></em>
                                    <label><?=($lowestAmount == 1 ? "1 Person":$lowestAmount." People")?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="floatrect c3r1">
                <div id="refbarchart" class="content">
                </div>
            </div>
        </div>
    </section>
    <section class="container-fluid" id="userdetails">
      <div class="row sectiontitle2">
          <h1>User Details</h1>
        </div>
      <div class="container">
      <table id="table1"></table>
        </div>
    </section>
