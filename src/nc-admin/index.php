<?php
include("inc/main.php");

$renderManager = new newreport_render();
$conn = ncapputil::getConnection();

$querypath = $renderManager->getQueryPath();
$adminManager = new admin(array(
	"conn"=>$conn,
	"table"=>DB_ADMIN
));

$main = new main();
$user = $adminManager->validToken();
switch($querypath[0]){
		case "resetpassword":
		case "forgetpassword":
			$adminManager->logout();
			$user = null;
		break;
}
if(!$user){
	switch($querypath[0]){
		case "resetpassword":
			$page = array(
					"label"=>"Reset Password",
					"name"=>"resetpassword",
					"folder"=>"resetpassword"
				);
			break;
		case "forgetpassword":
			$page = array(
					"label"=>"Forget Password",
					"name"=>"forgetpassword",
					"folder"=>"forgetpassword"
				);
			break;
		case "":
			//login page
			$page = array(
					"label"=>"Login",
					"name"=>"login",
					"folder"=>"login"
				);
			break;
		default:
			header('Location: '.SERVER_PATH.ADMIN_FOLDER);
		break;
	}
}else{
	$renderManager->setAdmin($user);
	$page = $renderManager->getPageData($querypath[0]);

	if(!$page){
		//cannot find page, redirect to the available page
		$available_page = $adminManager->getAvailablePage($user["level"]);
		if($available_page == ""){
			print_r("no page is allowed to access. Most likely is misconfiguration.");exit();
		}else{
			header('Location: '.SERVER_PATH.ADMIN_FOLDER.$available_page."/");
		}
	}

	if($page["name"] == "logout"){
		//remove cookie
		$adminManager->logout();
		//redirect to login page
		header('Location: '.SERVER_PATH.ADMIN_FOLDER);
	}else{

	}
}

//global variable
$commonfolder = SERVER_PATH.ADMIN_FOLDER."pages/common/";
$ownfolder = SERVER_PATH.ADMIN_FOLDER."pages/".$page["folder"]."/";

if($page["name"] == "login"){
	//load login page
	
	$errors = "";
	if(isset($_POST["username"]) && isset($_POST["password"])){

		$query = "SELECT aid FROM `".$adminManager->getAdminTable()."` WHERE email = ?";
		$check_uid = $conn->GetOne($query, array($_POST["username"]));
		if(!$check_uid){
			$query = "SELECT aid FROM `".$adminManager->getAdminTable()."` WHERE username = ?";
			$check_uid = $conn->GetOne($query, array($_POST["username"]));
		}
		if($check_uid){
			$result = $adminManager->login($check_uid, $_POST["password"], isset($_POST["remme"]) ? $_POST["remme"] : null );
			if($result["status"]){
				//login
				$user = $adminManager->validToken();
				$adminManager->adminlog($user["username"]." login at ".date("Y-m-d H:i:s"));	
				header('Location: '.SERVER_PATH.ADMIN_FOLDER."dashboard");	
			}else{
				$errors .=$result["msg"];
			}
		}else{
			$errors .= 'Sorry the email that you entered is not allowed to access the system.<br>
	    Please contact the service administrator.';
		}
	}

	include_once("pages/common/header_start.php");
	include_once("pages/".$page["folder"]."/header.php");
	include_once("pages/common/header_end.php");
	echo $renderManager->createMenu();
	include_once("pages/".$page["folder"]."/content.php");
	include_once("pages/common/footer_start.php");
	include_once("pages/".$page["folder"]."/footer.php");
	include_once("pages/common/footer_end.php");
}else if($page["name"] == "forgetpassword"){
	
	if(isset($_POST["email"])){
		$query = "SELECT aid, username FROM `".$adminManager->getAdminTable()."` WHERE email = ?";
		$check_uid = $conn->GetRow($query, array($_POST["email"]));		
		if(sizeof($check_uid)){
			$auth_token = $adminManager->getAuthToken($check_uid["aid"]);
			$content = array(
					"username"=>$check_uid["username"],
					"resetlink"=>SERVER_PATH.ADMIN_FOLDER."resetpassword/".$auth_token
				);

			$receiver = array(
				"email"=>$_POST["email"],
				"name"=>$check_uid["username"]
				);

			$main->sendResetEmail($receiver, $content);

			$success = "Email sent to ".$_POST["email"];
		}else{
			$errors .= 'Sorry the email that you entered is not allowed to access the system.<br>
	    Please contact the service administrator.';
		}	
	}

	include_once("pages/common/header_start.php");
	include_once("pages/".$page["folder"]."/header.php");
	include_once("pages/common/header_end.php");
	echo $renderManager->createMenu();
	include_once("pages/".$page["folder"]."/content.php");
	include_once("pages/common/footer_start.php");
	include_once("pages/".$page["folder"]."/footer.php");
	include_once("pages/common/footer_end.php");

}else if($page["name"] == "resetpassword"){
	$auth_token = $querypath[1];

	$result = $adminManager->validAuthToken($auth_token);
	if($result["status"]){
		if(isset($_POST["password"]) && isset($_POST["cpassword"])){
			if($_POST["password"] == $_POST["cpassword"]){
				$adminManager->updatePassword($result["uid"], $_POST["password"]);
				$success = "Password updated";
			}else{
				$errors = "Password does not match the confirm password";
			}
		}
	}else{
		$errors = "Authentication Token Expired.";
	}

	include_once("pages/common/header_start.php");
	include_once("pages/".$page["folder"]."/header.php");
	include_once("pages/common/header_end.php");
	echo $renderManager->createMenu();
	include_once("pages/".$page["folder"]."/content.php");
	include_once("pages/common/footer_start.php");
	include_once("pages/".$page["folder"]."/footer.php");
	include_once("pages/common/footer_end.php");
}else{	
	//load page
	
	//if got method ( use for table )
	if(isset($_GET["method"])){
		header('Content-Type: application/json');
		include_once("pages/".$page["folder"]."/json.php");
	}else if(sizeof($_POST)){
		header('Content-Type: application/json');
		include_once("pages/".$page["folder"]."/post.php");
	}else{
		// normal page
		
		include_once("pages/common/header_start.php");
		include_once("pages/".$page["folder"]."/header.php");
		include_once("pages/common/header_end.php");
		echo $renderManager->createMenu($page);
		include_once("pages/".$page["folder"]."/content.php");
		include_once("pages/common/footer_start.php");
		include_once("pages/".$page["folder"]."/footer.php");
		include_once("pages/common/footer_end.php");		
	}
}



