<?php
require_once 'class.ncapputil.php';
require_once 'class.logger.php';
class passwordmanager {
	private static $instance = null;
	private static $table = "";
	private static $table_request = "";
	private static $table_salt = "";
	private static $table_token = "";
	private static function checkTable(){
		self::$table = DB_PW;
		self::$table_salt = DB_PW_SALT;
		self::$table_request = DB_PW_REQUEST;
		self::$table_token = DB_TOKEN;
        self::$instance = "initialized";
	}
	
	private static function getSalt($uid, $usertype=1){
		self::checkTable();
		$conn = ncapputil::getConnection();
		$query = "SELECT salt FROM `".self::$table_salt."` WHERE uid = ? AND usertype =?";
		$salt = $conn->GetOne($query, array($uid,$usertype));
		if(!$salt){
			//if salt not exist, create a salt for this user.
			$salt = ncapputil::getRandomString(20);
			$query = "INSERT INTO `".self::$table_salt."` ( uid , usertype, salt ) VALUES ( ? , ?, ? )";
			$conn->Execute($query, array($uid, $usertype, $salt));
		}
		return $salt;
	}
	
	public static function validPassword($uid, $password, $usertype = 1){
		self::checkTable();
		$conn = ncapputil::getConnection();
		$query = "SELECT tt, password FROM `".self::$table."` WHERE uid = ? AND usertype = ? ORDER BY id DESC LIMIT 1";
		$result = $conn->GetRow($query, array($uid, $usertype));
		if($result){
			//password found
			$salt = self::getSalt($uid, $usertype);
			$saltedPW = $password.$salt;
			
			$hashedPW = hash('sha256', $saltedPW);
			
			$pw = $result["password"];
			if($pw === $hashedPW){
				return array(
					"error"=>0,
					"status"=>1,
					"msg"=>""
				);
			}else{
				//password not match
				$query = "SELECT COUNT(*) FROM `".self::$table."` WHERE password = ? AND uid = ? AND usertype = ?";
				$checkedRow = $conn->GetRow($query, array($hashedPW, $uid, $usertype));
				if($checkedRow){
					//password match with previous password, remind user that he had updated the password
					return array(
						"error"=>0,
						"status"=>0,
						"msg"=>"doesn't match with latest password",
						"update"=>array(
							"date"=>$result["tt"]
						)
					);
				}else{
					return array(
						"error"=>0,
						"status"=>0,
						"msg"=>"incorrect password"
					);
				}
			}
		}else{
			return array(
				"error"=>1,
				"status"=>0,
				"msg"=>"no password : ".$query
			);
		}
	}
	public static function insertPassword($uid, $password, $usertype = 1){
		self::checkTable();
		//geenrate random salt
		$salt = self::getSalt($uid, $usertype);
		$saltedPW = $password.$salt;
		
		$hashedPW = hash('sha256',$saltedPW);
		
		$conn = ncapputil::getConnection();
		$query = "INSERT INTO `".self::$table."` ( uid, usertype, password) VALUES ( ? , ? , ?)";
		$conn->Execute($query, array($uid, $usertype, $hashedPW));
	}

	public static function getPasswordRequestLink($uid, $usertype =1){
		self::checkTable();
		//insert into password reset request table
		$password_request_token = ncapputil::getRandomString("100");
		$conn = ncapputil::getConnection();
		$query = "INSERT INTO `".self::$table_request."` ( uid, usertype, token ) VALUES ( ? , ?, ? )";
		$conn->Execute($query, array($uid, $usertype, $password_request_token));
		return $password_request_token;
	}
	public static function validPasswordRequestLink($token){
		self::checkTable();
		$conn = ncapputil::getConnection();
		$query = "SELECT * FROM `".self::$table_request."` WHERE token = ?";
		$result = $conn->GetRow($query, array($token));
		if($result){
			//the request link only valid for an hour
			if(strtotime($result["tt"]) > strtotime("-1 hours")){
				return array(
					"uid"=>$result["uid"],
					"usertype"=>$result["usertype"],
					"error"=>0,
					"status"=>1,
					"msg"=>""
				);
			}else{
				return array(
					"error"=>0,
					"status"=>0,
					"msg"=>"token expired"
				);
			}
		}else{
			return array(
				"error"=>1,
				"status"=>0,
				"msg"=>"invalid token"
			);
		}
	}
	public static function getSessionToken($uid, $usertype = 1){
		self::checkTable();
		$conn = ncapputil::getConnection();
		$query = "SELECT token FROM `".self::$table_token."` WHERE uid = ? AND usertype = ?";
		$token = $conn->getOne($query, array($uid, $usertype));
		if(!$token){
			$token = ncapputil::getRandomString(50,"abcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()ABCDEFGHIJKLMNOPQRSTUVWXYZ");
			$query = "INSERT INTO `".self::$table_token."` (uid, usertype, token) VALUES ( ? , ?, ?)";
			$conn->Execute($query, array($uid, $usertype, $token));
		}
		return $token;
	}

	public static function validToken($token){
		self::checkTable();
		$conn = ncapputil::getConnection();
		$query = "SELECT uid, usertype FROM `".self::$table_token."` WHERE token = ?";
		return $conn->GetRow($query, array($token));
	}
}