<?php
class regionmanager{
	private $conn;
	private $regionTable;
	
	function __construct($dbconn, $regionTable){
		$this->conn = $dbconn;
		$this->regionTable = $regionTable;
	}
	
	function increaseRegionCount($region, $count){
		if(!$this->isRegionExist($region)){
			$this->createRegion($region);
		}
		$conn = $this->conn;
		$query = "UPDATE `".$this->regionTable."` SET amount = amount + "+$count+" WHERE region = ?";
		$conn->Execute($query, array($count, $region));
	}
	
	function isRegionExist($region){
		$conn = $this->conn;
		$query = "SELECT COUNT(*) FROM `".$this->regionTable."` WHERE name = ?";
		return $conn->GetOne($query, array($region));
	}
	
	function createRegion($region){
		$conn = $this->conn;
		$query = "INSERT INTO `".$this->regionTable."` ( name , amount ) VALUES ( ? , ? )";
		$conn->Execute($query, array($region, 0));
	}
	
	function getCountries(){
		$conn = $this->conn;
		return $conn->GetArray("SELECT * FROM `".$this->regionTable."`");
	}
	
	function getRegion(){
		$data = $this->getCountries();
	}
	
	function getRegionFromCountry($countryCode){
		/*
			AFRICA
			EUROPE
			MIDDLE EAST
			NORTH AMERICA
			NORTH ASIA
			OCEANIA
			SOUTH AMERICA
			SOUTH EAST ASIA
			SOUTH ASIA
		*/
		$regionData = $this->getRegionArray();
		$region = "SOUTH EAST ASIA";
		foreach($regionData as $key=>$value){
			if(array_search($countryCode, $value) !== false){
				$region = $key;
				break;
			}
		}
		return $region;
	}
	
	function getCountriesFromRegion($region){
		$data = $this->getRegionArray();
		return $data[$region];
	}
	
	private function getRegionArray(){
		$arr = array(
				"AFRICA"=>array(
							'DZ',
							'AO',
							'BW',
							'BF',
							'BI',
							'CV',
							'CF',
							'TD',
							'KM',
							'CD',
							'CG',
							'CI',
							'DJ',
							'DM',
							'DO',
							'ER',
							'ET',
							'GA',
							'GM',
							'GH',
							'GN',
							'GY',
							'KR',
							'LR',
							'MG',
							'MW',
							'ML',
							'MR',
							'YT',
							'MA',
							'MZ',
							'NA',
							'NI',
							'NE',
							'NG',
							'NU',
							'RE',
							'RW',
							'SH',
							'PM',
							'WS',
							'ST',
							'SN',
							'SC',
							'SL',
							'SO',
							'ZA',
							'SD',
							'SZ',
							'TZ',
							'TG',
							'TT',
							'TN',
							'UG',
							'EH',
							'ZM',
							'ZW'
								),
				"EUROPE"=>array(
							'AX',
							'AL',
							'AD',
							'AM',
							'AT',
							'AZ',
							'BY',
							'BE',
							'BJ',
							'BM',
							'BA',
							'BV',
							'BG',
							'HR',
							'CY',
							'CZ',
							'DK',
							'EE',
							'FO',
							'FI',
							'FR',
							'PF',
							'TF',
							'GE',
							'DE',
							'GI',
							'GR',
							'GL',
							'HU',
							'IS',
							'IE',
							'IT',
							'LV',
							'LI',
							'LT',
							'LU',
							'MK',
							'MT',
							'MD',
							'MC',
							'NL',
							'NC',
							'NF',
							'NO',
							'PL',
							'PT',
							'RO',
							'RU',
							'SM',
							'CS',
							'SK',
							'SI',
							'ES',
							'SJ',
							'SE',
							'CH',
							'TR',
							'UA',
							'GB',
							'VA',
							'WF'
								),
				"MIDDLE EAST"=>array(
							'AF',
							'BH',
							'EG',
							'IR',
							'IQ',
							'IL',
							'JO',
							'KW',
							'LB',
							'LY',
							'OM',
							'PS',
							'QA',
							'SA',
							'SY',
							'AE',
							'YE'
									),
				"NORTH AMERICA"=>array(
							'AS',
							'AW',
							'BS',
							'BB',
							'BZ',
							'CM',
							'CA',
							'GU',
							'GT',
							'GW',
							'MQ',
							'AN',
							'PN',
							'TC',
							'US',
							'UM',
							'VG',
							'VI'
									),
				"NORTH ASIA"=>array(
							'CN',
							'HK',
							'JP',
							'KZ',
							'KP',
							'KR',
							'KG',
							'MO',
							'MN',
							'PW',
							'TW',
							'TJ',
							'TM',
							'UZ'
									),
				"OCEANIA"=>array(
							'AU',
							'CX',
							'CC',
							'CK',
							'GQ',
							'FJ',
							'HM',
							'KI',
							'MH',
							'FM',
							'NR',
							'NZ',
							'MP',
							'PG',
							'SB',
							'TK',
							'TO',
							'TV',
							'VU'
								),
				"SOUTH AMERICA"=>array(
							'AI',
							'AG',
							'AR',
							'BO',
							'BR',
							'KY',
							'CL',
							'CO',
							'CR',
							'CU',
							'EC',
							'SV',
							'FK',
							'GF',
							'GD',
							'GP',
							'HT',
							'HN',
							'JM',
							'LS',
							'MS',
							'PA',
							'PY',
							'PE',
							'PR',
							'KN',
							'LC',
							'VC',
							'GS',
							'SR',
							'UY',
							'VE',
							'MX'
										),
				"SOUTH EAST ASIA"=>array(
									'BN',
									'KH',
									'ID',
									'LA',
									'MY',
									'MM',
									'PK',
									'PH',
									'SG',
									'TH',
									'TL',
									'VN'
										),
				"SOUTH ASIA"=>array(
									'BD',
									'BT',
									'IO',
									'IN',
									'MV',
									'MU',
									'NP',
									'LK'
									)																		
					);
		return $arr;
	}
}
?>