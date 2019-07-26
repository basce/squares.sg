<?php
class bcw_clientip{
	
	public static function valid_ip($ip)
	{
		$ip_segments = explode('.', $ip);

		// Always 4 segments needed
		if (count($ip_segments) != 4)
		{
			return FALSE;
		}
		// IP can not start with 0
		if ($ip_segments[0][0] == '0')
		{
			return FALSE;
		}
		// Check each segment
		foreach ($ip_segments as $segment)
		{
			// IP segments must be digits and can not be
			// longer than 3 digits or greater then 255
			if ($segment == '' OR preg_match("/[^0-9]/", $segment) OR $segment > 255 OR strlen($segment) > 3)
			{
				return FALSE;
			}
		}

		return TRUE;
	}
	
	public static function ip_address(){
		$ip_address = false;
		if (isset($_SERVER['REMOTE_ADDR']) AND isset($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (isset($_SERVER['REMOTE_ADDR']))
		{
			$ip_address = $_SERVER['REMOTE_ADDR'];
		}
		elseif (isset($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif ($_SERVER('HTTP_X_FORWARDED_FOR'))
		{
			$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		if ($ip_address === FALSE)
		{
			return '0.0.0.0';
		}

		if (strpos($ip_address, ',') !== FALSE)
		{
			$x = explode(',', $ip_address);
			$ip_address = trim(end($x));
		}

		if ( ! bcw_clientip::valid_ip($ip_address))
		{
			return '0.0.0.0';
		}

		return $ip_address;
	}
}
?>