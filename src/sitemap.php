<?php
header("Content-type: text/xml");
?><?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"> 
<?php  
$debugTime = microtime(true);
function getDebugTime(){
    global $debugTime;
    return microtime(true) - $debugTime;
}

function createLoc($link){
?>	<url>
		<loc><?=$link?></loc>
	</url>
<?php
}

/*
//home page
createLoc("https://storify.me/");
//sign up
createLoc("https://storify.me/signup");
//submit creator
createLoc("https://storify.me/submitcreator");
//singapore
createLoc("https://storify.me/listing?country%5B%5D=4");
//malaysia
createLoc("https://storify.me/listing?country%5B%5D=14");
//hong kong
createLoc("https://storify.me/listing?country%5B%5D=42");

//link for every creator
$query = "SELECT igusername FROM `".$wpdb->prefix."pods_instagrammer_fast` WHERE hidden = %d ORDER BY follows_by_count DESC";
$igusernames = $wpdb->get_col($wpdb->prepare($query, 0));

foreach($igusernames as $key=>$value){
	createLoc("https://storify.me/".$value."/");
}
*/
?></urlset>