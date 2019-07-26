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
function getSubmissionIDByUniqueCode($unique_code){
    $conn = ncapputil::getConnection();

    $query = "SELECT id FROM `".DB_SUBMISSION."` WHERE unique_code = ?";
    return $conn->GetOne($query, array($unique_code));
}

if(isset($_POST) && sizeof($_POST)){

    $temp_unique_code = isset($_POST["winner1"]) ? trim($_POST["winner1"]) : "";
    if($temp_unique_code){
      $tempID = getSubmissionIDByUniqueCode($temp_unique_code);
      if($tempID){
        $query = "SELECT COUNT(*) FROM `".DB_SE_WINNER_TOP3."` WHERE `index` = ?";
        if($conn->GetOne($query, array(1))){
          $query = "UPDATE `".DB_SE_WINNER_TOP3."` SET unique_code = ?, submission_id = ? WHERE `index` = ?";
          $conn->Execute($query, array($temp_unique_code, $tempID, 1));
        }else{
          $query = "INSERT INTO `".DB_SE_WINNER_TOP3."` ( unique_code, submission_id, `index` ) VALUES ( ?, ?, ?)";
          $conn->Execute($query, array($temp_unique_code, $tempID, 1));
        }
      }
    }

    $temp_unique_code = isset($_POST["winner2"]) ? trim($_POST["winner2"]) : "";
    if($temp_unique_code){
      $tempID = getSubmissionIDByUniqueCode($temp_unique_code);
      if($tempID){
        $query = "SELECT COUNT(*) FROM `".DB_SE_WINNER_TOP3."` WHERE `index` = ?";
        if($conn->GetOne($query, array(2))){
          $query = "UPDATE `".DB_SE_WINNER_TOP3."` SET unique_code = ?, submission_id = ? WHERE `index` = ?";
          $conn->Execute($query, array($temp_unique_code, $tempID, 2));
        }else{
          $query = "INSERT INTO `".DB_SE_WINNER_TOP3."` ( unique_code, submission_id, `index` ) VALUES ( ?, ?, ?)";
          $conn->Execute($query, array($temp_unique_code, $tempID, 2));
        }
      }
    }

    $temp_unique_code = isset($_POST["winner3"]) ? trim($_POST["winner3"]) : "";
    if($temp_unique_code){
      $tempID = getSubmissionIDByUniqueCode($temp_unique_code);
      if($tempID){
        $query = "SELECT COUNT(*) FROM `".DB_SE_WINNER_TOP3."` WHERE `index` = ?";
        if($conn->GetOne($query, array(3))){
          $query = "UPDATE `".DB_SE_WINNER_TOP3."` SET unique_code = ?, submission_id = ? WHERE `index` = ?";
          $conn->Execute($query, array($temp_unique_code, $tempID, 3));
        }else{
          $query = "INSERT INTO `".DB_SE_WINNER_TOP3."` ( unique_code, submission_id, `index` ) VALUES ( ?, ?, ?)";
          $conn->Execute($query, array($temp_unique_code, $tempID, 3));
        }
      }
    }

    $query = "DELETE FROM `".DB_SE_WINNER."`";
    $conn->Execute($query);

    $winners = $_POST["otherwinner"];
    if($winners){
      $winners_ar = explode(",", $winners);
      foreach($winners_ar as $key=>$value){
        $tempID = getSubmissionIDByUniqueCode(trim($value));
        if($tempID){
          $query = "INSERT INTO `".DB_SE_WINNER."` ( unique_code, submission_id ) VALUES ( ?, ? )";
          $conn->Execute($query, array(trim($value), $tempID));
        }
      }
    }
}

header('Location: '.SERVER_PATH.ADMIN_FOLDER."winner/");	