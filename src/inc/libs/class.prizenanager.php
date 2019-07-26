<?php
require_once 'class.logger.php';
require_once 'class.ncapputil.php';
class prizemanager{
	
	private $conn;
	private $goodietable;
	
	function __construct(){
		if(!defined("DB_PRIZE")) die('Missing Prize Table, DB_PRIZE need to be defined');
		$this->goodietable = DB_PRIZE;
	}
	
	private function getConnection(){
		if(!$this->conn){
			$this->conn = ncapputil::getConnection();
		}
		return $this->conn;
	}
}