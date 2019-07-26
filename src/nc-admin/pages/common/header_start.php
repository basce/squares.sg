<?php
$pagetitle = $page && isset($page["title"]) ? $page["title"] : PAGE_TITLE;
$pagedescription  = $page && isset($page["description"]) ? $page["description"] : PAGE_DESCRIPTION;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <meta name="description" content="<?=htmlspecialchars($pagedescription)?>">
  <meta name="robots" content="noindex,nofollow" />

  <title><?=htmlspecialchars($pagetitle)?></title>

  <!-- Bootstrap Core CSS -->
  <link href="<?=$commonfolder?>assets/css/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="<?=$commonfolder?>css/bootstrap.min.css">

  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
    <script src="assets/js/html5shiv.js"></script>
    <script src="assets/js/respond.min.js"></script>
  <![endif]-->
	<link rel="stylesheet/less" type="text/css" href="<?=$commonfolder?>less/main.less" />
	<script src="<?=$commonfolder?>js/less-1.7.1.min.js" type="text/javascript"></script>
  <!-- <link rel="stylesheet" href="css/main.css"> -->
  <link href='https://fonts.googleapis.com/css?family=Lato:300,400,600,700' rel='stylesheet' type='text/css'>
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="<?=$commonfolder?>css/animate.css" />
  <link rel="stylesheet" type="text/css" href="<?=$commonfolder?>css/hover.css" />
  <link rel="stylesheet" type="text/css" href="<?=$commonfolder?>css/keyframe.css" />
  <link rel="stylesheet" type="text/css" href="<?=$commonfolder?>webfonts/font.css" />
  <link rel="stylesheet" type="text/css" href="<?=$commonfolder?>webfonts/font-awesome.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    