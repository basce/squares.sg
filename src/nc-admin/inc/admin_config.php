<?php
/*
menu setting
 */
define("ADMIN_FOLDER","nc-admin/");

$admin_menu = array(
		array(
			"label"=>"Dashboard",
			"name"=>"dashboard",
			"folder"=>"dashboard",
			"url"=>SERVER_PATH.ADMIN_FOLDER."dashboard/",
			"visible"=>"0,1,2,3,4"
		),
		array(
			"label"=>"Voter",
			"name"=>"user",
			"folder"=>"user",
			"url"=>SERVER_PATH.ADMIN_FOLDER."user/",
			"visible"=>"0,1,2,3,4"
		),
		array(
			"label"=>"Winner",
			"name"=>"winner",
			"folder"=>"se_winner",
			"url"=>SERVER_PATH.ADMIN_FOLDER."winner/",
			"visible"=>"2,4"
		),
		array(
			"label"=>"Admin",
			"name"=>"admin",
			"folder"=>"admin",
			"url"=>SERVER_PATH.ADMIN_FOLDER."admin/",
			"visible"=>"2,4"
		),
		array(
			"label"=>"CSV Tool",
			"name"=>"csv",
			"folder"=>"batchupload",
			"url"=>SERVER_PATH.ADMIN_FOLDER."csv/",
			"visible"=>"2"
		),
		array(
			"label"=>"Log",
			"name"=>"adminlog",
			"folder"=>"log",
			"url"=>SERVER_PATH.ADMIN_FOLDER."adminlog/",
			"visible"=>"4 "
		),
		array(
			"label"=>"Logout",
			"name"=>"logout",
			"url"=>SERVER_PATH.ADMIN_FOLDER."logout",
			"visible"=>"0,1,2,3,4"
		)
	);


?>