<?php
/*
Date 		:	2 Dec 2013
Programmer	:	Yong Chee Wei
Content		:	added static function getAppData, see the usage below,
				
				
*/
include_once("lessc.inc.php");
include_once("phpqrcode/qrlib.php");

class ncAppUtil{

	private static $conn = NULL;
	public static function getConnection($connection_string = DB_DRIVER){
		if(!self::$conn){
			if(!$connection_string) die("Connection String need to be defined, Please assign DB_DRIVER");
			self::$conn = NewADOConnection($connection_string);	
			self::$conn ->debug = false ;	
			self::$conn ->SetFetchMode(ADODB_FETCH_ASSOC);
			self::$conn->EXECUTE("set names 'utf8mb4'"); 
			self::$conn->EXECUTE("set time_zone = '+8:00'");
		}
		return self::$conn;	
	}
	
	public static $AppData = array();
	public static function getAppData($default = NULL){
		$appdataencoded = isset($_REQUEST["app_data"])?$_REQUEST["app_data"]:"";
		// for backward compability
		if($appdataencoded == "" && isset($_REQUEST["_ref"])){
			$appdataencoded = $_REQUEST["_ref"];
		}
		
		$appdata = ncAppUtil::$AppData;
		if($appdataencoded != ""){
			$appdata = array_merge(
						$appdata,	// previous data
						ncAppUtil::nc64decode($appdataencoded) //data from query string
					);	
		}
		
		$fbsignrequest = ncAppUtil::getSignRequest();
		$fbsignrequest_appdata = isset($fbsignrequest["app_data"])?$fbsignrequest["app_data"]:"";
		
		if($fbsignrequest_appdata != ""){
			$appdata = array_merge(
						$appdata,	//data from query string
						ncAppUtil::nc64decode($fbsignrequest_appdata)				//data from sign request
					);	
		}
		if($default){
			$appdata = array_merge(
							$default,
							$appdata
						);
		}
		ncAppUtil::$AppData = $appdata;
		
		return $appdata;
	}

	public static function generateQRCode($content, $correctionLevel="M", $matrixPointSize=4){
		$filename = md5($content).'.png';
		$PNG_TEMP_DIR = dirname(dirname(__DIR__))."/temp";
		if(file_exists($PNG_TEMP_DIR."/".$filename)){
			return "temp/".$filename;
		}else{
			//not exist, try to generate
			QRcode::png($content, $PNG_TEMP_DIR."/".$filename, $correctionLevel, $matrixPointSize, 2);
			if(!file_exists($PNG_TEMP_DIR."/".$filename)){
				return NULL;
			}else{
				return "temp/".$filename;
			}
		}

	}
	
	public static function getRandomString($len, $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"){
		$str = "";
		while(strlen($str) < $len){
			$str .= substr($chars, mt_rand(0, strlen($chars)),1);
		}
		return $str;
	}
	public static function returnEncodedStr($extra = NULL){
		$appdata = ncAppUtil::$AppData;
		if($extra){
			$appdata = array_merge(
							$appdata,
							$extra
								);
		}
		
		return ncAppUtil::nc64encode($appdata);
	}
	
	public static function nc64encode($obj){
		return strtr(base64_encode(json_encode($obj)),"+/","-_");
	}
	
	public static function nc64decode($str){
		return json_decode(base64_decode(strtr($str, "-_", "+/")), true);
	}
	
	public static function getSignRequest(){
		if(!isset($_REQUEST["signed_request"])){
			return array();
		}
		list($encoded_sig, $payload) = explode(".",$_REQUEST["signed_request"], 2);
		return ncAppUtil::nc64decode($payload);
	}
	
	public static function isFaninTab($signrequest){
		if(isset($signrequest["page"]["liked"])){
			if($signrequest["page"]["liked"] == 1 ){
				return 1;
			}else{
				return 0;
			}
		}else{
			return -1;
		}
	}
	
	public static function getLocationinTab($signrequest){
		if(isset($signrequest["user"]["country"])){
			return $signrequest["user"]["country"];
		}
		return '';
	}
	
	public static function getLanguageinTab($signrequest){
		if(isset($signrequest["user"]["locale"])){
			return $signrequest["user"]["locale"];
		}
		return '';
	}
	
	public static function getMinAgeinTab($signrequest){
		if(isset($signrequest["user"]["age"]["min"])){
			return $signrequest["user"]["age"]["min"];
		}
		return -1;
	}
	
	public static function getCurrentPageIninTab($signrequest){
		if(isset($signrequest["page"]["id"])){
			return $signrequest["page"]["id"];
		}
		return '';
	}
	
	public static function combinations_set($set = [], $size = 0) {
		if ($size == 0) {
			return [[]];
		}
	 
		if ($set == []) {
			return [];
		}
	 
	 
		$prefix = [array_shift($set)];
	 
		$result = [];
	 
		foreach (self::combinations_set($set, $size-1) as $suffix) {
			$result[] = array_merge($prefix, $suffix);
		}
	 
		foreach (self::combinations_set($set, $size) as $next) {
			$result[] = $next;
		}
	 
		return $result;
	}
	
	// Returns the total number of $count-length strings generatable from $letters.
	public static function getPermCount($letters, $count)
	{
	  $result = 1;
	  // k characters from a set of n has n!/(n-k)! possible combinations
	  for($i = strlen($letters) - $count + 1; $i <= strlen($letters); $i++) {
		$result *= $i;
	  }
	  return $result;
	} 
	
	// Decodes $index to a $count-length string from $letters, no repeat chars.
	public static function getPerm($letters, $count, $index)
	{
	  $result = '';
	  for($i = 0; $i < $count; $i++)
	  {
		$pos = $index % strlen($letters);
		$result .= $letters[$pos];
		$index = ($index-$pos)/strlen($letters);
		$letters = substr($letters, 0, $pos) . substr($letters, $pos+1);
	  }
	  return $result;
	}
	
	public static function getIPInfo($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
		try{
			$output = NULL;
			if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
				$ip = $_SERVER["REMOTE_ADDR"];
				if ($deep_detect) {
					if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
						$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
					if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
						$ip = $_SERVER['HTTP_CLIENT_IP'];
				}
			}
			
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, "http://campaigns.sg/report/ip/");
			curl_setopt($curl, CURLOPT_POST, TRUE);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
			'ip'=>$ip)));
			curl_setopt($curl, CURLOPT_TIMEOUT, 1);
			curl_setopt($curl, CURLOPT_HEADER, FALSE);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curl, CURLOPT_VERBOSE, TRUE);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
			curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 0);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
			$result = curl_exec($curl);
			curl_close($curl);
			$resultjs = json_decode($result, true);
			if($resultjs && !$resultjs["error"]){
				if(isset($resultjs["data"]) && isset($resultjs["data"]["country_code"])){
					return $resultjs["data"]["country_code"];
				}else{
					return NULL;
				}
			}else{
				return NULL;
			}
		}catch(Exception $e){
			return NULL;
		}
	}
	
	public static function minifyfiles($type, $filenameAr, $folder_to_root, $cachebooster, $useCache=true){
		$minify_f_ar = array();
		$less_f_ar = array();
		$normal_f_ar = array();
		foreach($filenameAr as $key=>$value){
			if(substr($value,-4) == "less"){ //specially for file in less folder
				$less_f_ar[] = $value;
			}else{
				$normal_f_ar[] = $value;
				if(substr($value,0,3) == "../"){
					$minify_f_ar[] = str_replace("../","",$value);
				}else{
					$minify_f_ar[] = $folder_to_root.$value;
				}
			}
		}
		
		$str = "";
		if(!$useCache){
			$cachebooster = time();
			if($type == "css"){
				foreach($normal_f_ar as $key=>$value){
					$str .= '<link href="'.$value.'?t='.$cachebooster.'" rel="stylesheet" type="text/css">';
				}
			}else{
				foreach($normal_f_ar as $key=>$value){
					$str .= '<script type="text/javascript" src="'.$value.'?t='.$cachebooster.'"></script>';
				}
			}
			$str .= "<!--";
			$str .= "?f=".implode(",",$minify_f_ar); 
			$str .=  "-->";
			
			if(sizeof($less_f_ar)){
				//with less
				//<link rel="stylesheet/less" type="text/css" href="less/main_aw810.less
				foreach($less_f_ar as $key=>$value){
					$str .= '<link rel="stylesheet/less" type="text/css" href="'.$value.'?t='.$cachebooster.'" />';
				}
				$str .= '<script src="js/less-1.7.1.min.js?t='.$cachebooster.'" type="text/javascript"></script>';
			}
			
		}else{
			//check if cache file exist
			$absolutefilename = dirname(dirname(__DIR__))."/cache/cache_".$cachebooster.".".$type;

			
			if (!file_exists($absolutefilename)) {
				//if got less, generate css file from less and put in the less folder.
				if(sizeof($less_f_ar)){
					foreach($less_f_ar as $key=>$value){
						if(substr($value,0,3) == "../"){
							$minify_f_ar[] = str_replace("../","",$value);
						}else{
							$minify_f_ar[] = $folder_to_root.$value;
						}
						
					}

				}
				$minurl = "http://campaigns.sg/min2/?f=".implode(",",$minify_f_ar); 

				$allowUrlFopen = preg_match('/1|yes|on|true/i', ini_get('allow_url_fopen'));
				if ($allowUrlFopen) {
					$contents = file_get_contents($minurl, false, stream_context_create(array(
						'http' => array(
							'method' => 'GET',
							'header' => "Content-type: application/x-www-form-urlencoded\r\nConnection: close\r\n",
							'max_redirects' => 0,
							'timeout' => 30,
						)
					)));
				} elseif (defined('CURLOPT_POST')) {
					$ch = curl_init($this->serviceUrl);
					curl_setopt($ch, CURLOPT_POST, FALSE);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
					$contents = curl_exec($ch);
					curl_close($ch);
				} else {
					die(  "Could not make HTTP request: allow_url_open is false and cURL not available"	);
				}
		
				if (false === $contents) {
					die(  "No HTTP response from server" );
				}
		
				if (! @file_put_contents($absolutefilename, trim($contents))) {
					die( "Minify_Cache_File: Write failed to '$absolutefilename'");
				}
			}
			
			if($type == "css"){
				$str .=  '<link href="cache/cache_'.$cachebooster.'.'.$type.'" rel="stylesheet" type="text/css">';
			}else{
				$str .=  '<script type="text/javascript" src="cache/cache_'.$cachebooster.'.'.$type.'"></script>';
			}
		}
		return $str;
	}
}
?>