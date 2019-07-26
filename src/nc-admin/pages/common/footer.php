<?php
//enforce HTTPS access
include_once("../../inc/config.php");
include_once("../../inc/libs/class.admin.php");
include_once("../../inc/libs/class.ncapputil.php");

$adminManager = new admin(array(
	"conn"=>ncapputil::getConnection(),
	"table"=>DB_ADMIN
));
$adminManager->logout();
$message ='';
if(isset($_POST["username"]) && isset($_POST["password"])){
	if($adminManager->login($_POST["username"], $_POST["password"], $_POST["remme"])){
		$user = $adminManager->validToken();
		$adminManager->adminlog($user["username"]." login at ".date("Y-m-d H:i:s"));		
		header('Location:index.php');
	}else{
		$errors .= 'Sorry the password that you entered is incorrect.<br>
    Please contact the service administrator.';
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="cheewei">
    <meta name="robots" content="noindex,nofollow" />

    <title>Admin Module</title>

    <!-- Bootstrap Core CSS -->
    <link href="assets/css/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css">
    
    <link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-table.css">
	<link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css">


    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="assets/js/html5shiv.js"></script>
      <script src="assets/js/respond.min.js"></script>
    <![endif]-->
	<link rel="stylesheet/less" type="text/css" href="less/main.less" />
	<script src="js/less-1.7.1.min.js" type="text/javascript"></script>
    <!-- <link rel="stylesheet" href="css/main.css"> -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700' rel='stylesheet' type='text/css'>
    <script src="js/vendor/modernizr.min.js"></script>
    <!--[if lte IE 8]><script src="js/vendor/html5.js"></script><![endif]-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="assets/js/plugins/jquery.easing/jquery.easing.1.3.js"></script>
</head>

<body id="loginpage" data-spy="scroll" data-target=".navbar">
<div class="vertical-center">
	<div class="container loginbox">
    	<div class="row">
        	<div class="col-xs-4 col-sm-4 col-md-4 bgcolor1"></div>
            <div class="col-xs-4 col-sm-4 col-md-4 bgcolor2"></div>
            <div class="col-xs-4 col-sm-4 col-md-4 bgcolor3"></div>
        </div>
        <div class="row text-center logocont">
        	<img src="images/nclogo0.png">
        </div>
        <div class="row">
        <div class="col-xs-1 col-sm-1 col-md-1"></div>
        <div class="col-xs-10 col-sm-10 col-md-10">
            <form action="" method="post" id="frm_user" class="frm_standard" enctype="multipart/form-data">
              <div class="form-group has-feedback has-feedback-left">
                <label class="control-label sr-only">Username</label>
                <input type="text"  name="username" class="form-control" placeholder="Username" tabindex="1" />
                <i class="form-control-feedback glyphicon glyphicon-user"></i>
              </div>
              <div class="form-group has-feedback has-feedback-left">
                <label class="control-label sr-only">Username</label>
                <input type="password"  name="password" class="form-control" placeholder="Password" tabindex="2" />
                <i class="form-control-feedback glyphicon glyphicon-lock"></i>
                <?php if($errors){ ?>
              	<div class="errors"><?=$errors?></div>
                <?php } ?>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="remme"> Remember me
                </label>
              </div>
              <div class="text-right">
              <button type="submit" class="btn btn-login">Login</button>
              </div>
            </form>
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1"></div>
        </div>
    </div>
</div>
  <script>$(document).ready(function(e) {$('#login input[type=text]').focus();});</script>
</body>
</html>