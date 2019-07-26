<?php
class bcw_useragents{
	public static function isIOS(){
		$agents = bcw_useragents::get_agent_type();
		return isset($agents["mobile"]) && array_search($agents["mobile"],array("Apple iPhone","iPad","Apple iPod Touch"));
	}
	
	public static function isMobile(){
		$agents = bcw_useragents::get_agent_type();
		return isset($agents["mobile"])&&($agents["mobile"] != NULL);
	}
	public static function isIOSChrome(){
		$agents = bcw_useragents::get_agent_type();
		return (strpos($_SERVER['HTTP_USER_AGENT'], 'CriOS'))? 1 : 0;
	}
	
	public static function get_agent_type(){
		$agent_types = array();
		$agent_types["browser"] = array(
					'Flock'				=> 'Flock',
					'Chrome'			=> 'Chrome',
					'Opera'				=> 'Opera',
					'MSIE'				=> 'Internet Explorer',
					'Internet Explorer'	=> 'Internet Explorer',
					'Shiira'			=> 'Shiira',
					'Firefox'			=> 'Firefox',
					'Chimera'			=> 'Chimera',
					'Phoenix'			=> 'Phoenix',
					'Firebird'			=> 'Firebird',
					'Camino'			=> 'Camino',
					'Netscape'			=> 'Netscape',
					'OmniWeb'			=> 'OmniWeb',
					'Safari'			=> 'Safari',
					'Mozilla'			=> 'Mozilla',
					'Konqueror'			=> 'Konqueror',
					'icab'				=> 'iCab',
					'Lynx'				=> 'Lynx',
					'Links'				=> 'Links',
					'hotjava'			=> 'HotJava',
					'amaya'				=> 'Amaya',
					'IBrowse'			=> 'IBrowse'
				);
				
		$agent_types["mobile"] = array(
					// legacy array, old values commented out
					'mobileexplorer'	=> 'Mobile Explorer',
					'palmsource'		=> 'Palm',
					'palmscape'			=> 'Palmscape',

					// Phones and Manufacturers
					'motorola'			=> "Motorola",
					'nokia'				=> "Nokia",
					'palm'				=> "Palm",
					'iphone'			=> "Apple iPhone",
					'ipad'				=> "iPad",
					'ipod'				=> "Apple iPod Touch",
					'sony'				=> "Sony Ericsson",
					'ericsson'			=> "Sony Ericsson",
					'blackberry'		=> "BlackBerry",
					'cocoon'			=> "O2 Cocoon",
					'blazer'			=> "Treo",
					'lg'				=> "LG",
					'amoi'				=> "Amoi",
					'xda'				=> "XDA",
					'mda'				=> "MDA",
					'vario'				=> "Vario",
					'htc'				=> "HTC",
					'samsung'			=> "Samsung",
					'sharp'				=> "Sharp",
					'sie-'				=> "Siemens",
					'alcatel'			=> "Alcatel",
					'benq'				=> "BenQ",
					'ipaq'				=> "HP iPaq",
					'mot-'				=> "Motorola",
					'playstation portable'	=> "PlayStation Portable",
					'hiptop'			=> "Danger Hiptop",
					'nec-'				=> "NEC",
					'panasonic'			=> "Panasonic",
					'philips'			=> "Philips",
					'sagem'				=> "Sagem",
					'sanyo'				=> "Sanyo",
					'spv'				=> "SPV",
					'zte'				=> "ZTE",
					'sendo'				=> "Sendo",

					//Nexus
					'nexus'				=> "Nexus",
		
					// Operating Systems
					'symbian'				=> "Symbian",
					'SymbianOS'				=> "SymbianOS",
					'elaine'				=> "Palm",
					'palm'					=> "Palm",
					'series60'				=> "Symbian S60",
					'windows ce'			=> "Windows CE",

					// Browsers
					'obigo'					=> "Obigo",
					'netfront'				=> "Netfront Browser",
					'openwave'				=> "Openwave Browser",
					'mobilexplorer'			=> "Mobile Explorer",
					'operamini'				=> "Opera Mini",
					'opera mini'			=> "Opera Mini",

					// Other
					'digital paths'			=> "Digital Paths",
					'avantgo'				=> "AvantGo",
					'xiino'					=> "Xiino",
					'novarra'				=> "Novarra Transcoder",
					'vodafone'				=> "Vodafone",
					'docomo'				=> "NTT DoCoMo",
					'o2'					=> "O2",

					// Fallback
					'tablet'				=> "Generic Tablet",
					'mobile'				=> "Generic Mobile",
					'wireless'				=> "Generic Mobile",
					'j2me'					=> "Generic Mobile",
					'midp'					=> "Generic Mobile",
					'cldc'					=> "Generic Mobile",
					'up.link'				=> "Generic Mobile",
					'up.browser'			=> "Generic Mobile",
					'smartphone'			=> "Generic Mobile",
					'cellphone'				=> "Generic Mobile"
				);
				
			$agent_types["robot"] = array(
					'googlebot'			=> 'Googlebot',
					'msnbot'			=> 'MSNBot',
					'slurp'				=> 'Inktomi Slurp',
					'yahoo'				=> 'Yahoo',
					'askjeeves'			=> 'AskJeeves',
					'fastcrawler'		=> 'FastCrawler',
					'infoseek'			=> 'InfoSeek Robot 1.0',
					'lycos'				=> 'Lycos'
				);
			
			$agent = trim($_SERVER['HTTP_USER_AGENT']);
			
			$result = array(
								"robot"=>NULL,
								"browser"=>NULL,
								"mobile"=>NULL,
								"version"=>NULL
							);
			foreach($agent_types as $keytype=>$value){
				switch($keytype){
					case "robot":
						foreach ($value as $key => $val)
						{
							if (preg_match("|".preg_quote($key)."|i", $agent))
							{
								$result["robot"] = $val;
								return $result;
							}
						}
					break;
					case "browser":
						foreach ($value as $key => $val)
						{
							if (preg_match("|".preg_quote($key).".*?([0-9\.]+)|i", $agent, $match))
							{
								$result["browser"] = $val;
								$result["version"] = $match[1];
							}
						}
					break;
					case "mobile":
						foreach ($value as $key => $val)
						{
							if (FALSE !== (strpos(strtolower($agent), $key)))
							{
								$result["mobile"] = $val;
							}
						}
					break;
				}
			}
			
			return $result;
	}
}
?>