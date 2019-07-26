<?php
class nc_analytic{
	private static $analytic_url = "http://campaigns.sg/report/track/";
	public static function http_build_query_for_curl($arrays, &$new = array(), $prefix = null){
		if ( is_object( $arrays ) ) { $arrays = get_object_vars( $arrays ); }
		foreach ( $arrays AS $key => $value ) {
			$k = isset( $prefix ) ? $prefix . '[' . $key . ']' : $key;
			if ( is_array( $value ) OR is_object( $value )  ) {
				self::http_build_query_for_curl( $value, $new, $k );
			} else {
				$new[$k] = $value;
			}
		}
	}
	
	/**
	 *	There are 3 methods: userData, userRef, userAction
	 *	
	 *	to insert or update userData, you need to define either fbid or email or both
	 *	Input Sample for userData
	 *	array(
	 *		"appname":"uniqueappname",
	 *		"method":"userData",
	 *		"param":array(
	 *					name:string,
	 *					email:string,
	 *					fbid:string,
	 *					gender:string,
	 *					age:number,
	 *					phone:string,
	 *					ic:string,
	 *					country:"countrycode",
	 *					pdpa:"accept" || "deny",
	 *					ip:string
	 *				)
	 *	)
	 *
	 *	to insert the referral into the system, you can just define the fbid or email, and the via parameter can be set as any anonymous string,etc ( email, phone, fb )
	 *	Input Sample for userRef
	 *	array(
	 *		"appname":"uniqueappname",
	 *		"method":"userRef",
	 *		"param":array(
	 *					user1:array(
	 *							"fbid"=>String,
	 *							"email"=>String
	 *							),
	 *					user2:array(
	 *							"fbid"=>String,
	 *							"email"=>String
	 *							),
	 *					via:string
	 *				)
	 *	)
	 *
	 *  to record the user action in the app, etc read, draw, play
	 *	Input Sample for userAction
	 *	array(
	 *		"appname":"uniqueappname",
	 *		"method":"userAction",
	 *		"param":array(
	 *					email:string,
	 *					fbid:string,
	 *					action:string
	 *				)
	 *	)
	 @var params = array(
	                     "appname" : string ( a unique name for the app ) *require
						 "method" : userData | userRef | userAction
						 "param" : obj ( different for all 3 methods.
	                        )
		
	 */
	public static function send($params){
		self::http_build_query_for_curl( $params, $post );
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, self::$analytic_url);
		curl_setopt($curl, CURLOPT_POST, TRUE);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
		curl_setopt($curl, CURLOPT_TIMEOUT, 1);
		curl_setopt($curl, CURLOPT_HEADER, FALSE);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($curl, CURLOPT_VERBOSE, TRUE);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 0);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
		$result = curl_exec($curl);
		curl_close($curl);
		return $result;
	}
}