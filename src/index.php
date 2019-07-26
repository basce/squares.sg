<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

$debugTime = microtime(true);
function getDebugTime(){
    global $debugTime;
    return microtime(true) - $debugTime;
}

include_once("inc/class.main.php");
$main = new main();

$current_stage = $main->getCurrentStage();

$pathquery = $main->getQueryPath();
if(sizeof($pathquery) == 0){
    if($current_stage["status"] == "before_vote"){
        include_once("pages/index-beforevoting.php");
        exit();
    }else{
        if(isset($_POST)){
            include_once("pages/index_ajax.php");
            exit();
        }else{
            include_once("pages/index.php");
            exit();
        }
    }
}else{
    if($pathquery[0] == ""){
        if($current_stage["status"] == "before_vote"){
            include_once("pages/index-beforevoting.php");
            exit();
        }else{
            if(isset($_POST) && sizeof($_POST)){
                include_once("pages/index_ajax.php");
                exit();
            }else{
                include_once("pages/index.php");
                exit();
            }
        }
    }else if($pathquery[0] == "rules"){
        include_once("pages/rules.php");
        exit();
    }else if($pathquery[0] == "terms"){
        include_once("pages/terms.php");
        exit();
    }else if($pathquery[0] == "about"){
        include_once("pages/about.php");
        exit();
    }else if($pathquery[0] == "winners"){
        include_once("pages/winners.php");
        exit();
    }

    $submission_id = $main->getSubmissionByUniqueCode($pathquery[0]);
    if($submission_id){
        //uniquecode exist
        if(isset($_POST) && sizeof($_POST)){
            include_once("pages/index_ajax.php");
            exit();
        }else{
            include_once("pages/portfolio-details.php");
            exit();
        }
    }

    header("Location: /");
    exit();
}
?>