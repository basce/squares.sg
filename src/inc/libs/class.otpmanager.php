<?php
require_once 'class.ncapputil.php';
require_once 'class.logger.php';
class otpmanager {
	private static $instance = null;
	private static $table_otp = "";
	private static function checkTable(){
		self::$table_otp = DB_OTP;
        self::$instance = "initialized";
	}

	public static function createOTP($phone){
		self::checkTable();
		$conn = ncapputil::getConnection();
		$query ="SELECT COUNT(*) FROM `".self::$table_otp."` WHERE phone = ? AND tt >= NOW() - INTERVAL 2 MINUTE";
		if(!$conn->GetOne($query, array($phone))){
			//no otp created in the last 2 mins, create OTP
			$OTP = ncapputil::getRandomString(6, "0123456789");
			$query = "INSERT INTO `".self::$table_otp."` ( phone, otp ) VALUES ( ? , ? )";
			$conn->Execute($query, array($phone, $OTP));
			return array(
				"status"=>1,
				"msg"=>"OTP created",
				"otp"=>$OTP
			);
		}else{
			//an otp is created within 2 mins, not going to create again.
			return array(
				"status"=>0,
				"msg"=>"please wait 2 minutes for generating a new OTP"
			);
		}
	}

	public static function checkOTP($phone, $otp){
		self::checkTable();
		$conn = ncapputil::getConnection();
		$query = "SELECT COUNT(*) FROM `".self::$table_otp."` WHERE phone = ? AND otp = ? AND tt >= NOW() - INTERVAL 2 MINUTE";
		if($conn->GetOne($query, array($phone, $otp))){
			//otp valid
			return array(
				"status"=>1,
				"msg"=>""
			);
		}else{
			$query = "SELECT COUNT(*) FROM `".self::$table_otp."` WHERE phone = ? AND otp = ?";
			if($conn->GetOne($query, array($phone, $otp))){
				//otp expired
				return array(
					"status"=>0,
					"msg"=>"OTP expired"
				);
			}else{
				//wrong otp
				return array(
					"status"=>0,
					"msg"=>"invalid OTP"
				);
			}
		}
	}
}