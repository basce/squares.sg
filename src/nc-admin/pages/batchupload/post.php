<?php
$conn = ncapputil::getConnection();

$adminManager = new admin(array(
	"conn"=>$conn,
	"table"=>DB_ADMIN
));
$user = $adminManager->validToken();
if(!$user){
	exit("require admin login");
}

function insertSubmission($data){
	$conn = ncapputil::getConnection();
	foreach($data as $key=>$value){
		//check if designer exist by student id
		$query = "SELECT id FROM `".DB_DESIGNER."` WHERE student_id = ?";
		$id = $conn->GetOne($query, array($value["student_id"]));
		if($id){
			//update data
			$query = "UPDATE `".DB_DESIGNER."` SET name = ?, first_name = ?, last_name = ?, age = ?, ig_handle = ?, faculty = ?, course = ?, year = ? WHERE id = ?";
			$conn->Execute($query, array($value["first_name"]." ".$value["last_name"], $value["first_name"], $value["last_name"], $value["age"], $value["igusername"], $value["faculty"], $value["course"], $value["year"], $id));
		}else{
			//insert data
			$query = "INSERT INTO `".DB_DESIGNER."` ( name, first_name, last_name, age, ig_handle, faculty, course, year, student_id ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ? )";
			$conn->Execute($query, array($value["first_name"]." ".$value["last_name"], $value["first_name"], $value["last_name"], $value["age"], $value["igusername"], $value["faculty"], $value["course"], $value["year"], $value["student_id"]));
			print_r($query);
			print_r(array($value["first_name"]." ".$value["last_name"], $value["first_name"], $value["last_name"], $value["age"], $value["igusername"], $value["faculty"], $value["course"], $value["year"], $value["student_id"]));
			$id = $conn->Insert_ID();
		}

		//check if submissino exist
		$query = "SELECT id FROM `".DB_SUBMISSION."` WHERE unique_code = ?";
		$submission_id = $conn->GetOne($query, array($value["unique_code"]));

		if($submission_id){
			$query = "UPDATE `".DB_SUBMISSION."` SET artwork_name = ?, designer_id = ? WHERE id = ?";
			$conn->Execute($query, array($value["theme"], $id, $submission_id));
		}else{
			$query = "INSERT INTO `".DB_SUBMISSION."` ( artwork_name, designer_id, unique_code ) VALUES ( ?, ?, ? )";
			$conn->Execute($query, array($value["theme"], $id, $value["unique_code"]));
			$submission_id = $conn->Insert_ID();
		}

		//reset all submission items
		$query = "DELETE FROM `".DB_SUBMISSION_ITEM."` WHERE submission_id = ?";
		$conn->Execute($query, array($submission_id));

		$images = explode(",", $value["file_name"]);
		$placeholder = array();
		$items = array();
		foreach($images as $key1=>$value1){
			$placeholder[] = "(?, ?)";
			$items[] = $submission_id;
			$items[] = trim($value1);
		}

		$query = "INSERT INTO `".DB_SUBMISSION_ITEM."` ( submission_id, image_url ) VALUES ".implode(",", $placeholder);
		$conn->Execute($query, $items);
	}

	return array(
		"success"=>1,
		"msg"=>"insert complete"
	);
}

$method = isset($_REQUEST["method"]) ? $_REQUEST["method"] : "";
switch ($method) {
	case 'insert':
		echo json_encode(insertSubmission(json_decode($_REQUEST["data"], true)));
		break;
	
	default:
		echo json_encode(array(
			"success"=>0,
			"msg"=>"missing method"
		));
		break;
}