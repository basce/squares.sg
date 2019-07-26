<?php
require_once 'class.ncapputil.php';
class logger{
	protected static $instance = null;
	
	public static function trace($str){
		$conn = ncAppUtil::getConnection();
		if(defined("DB_TRACELOG")){
			$table = DB_TRACELOG;
		}else{
			//generate table if DB_PREFIX is exist
			if(defined("DB_PREFIX")){
				$table = DB_PREFIX."__tracelog";
			}else{
				die("either DB_PREFIX or DB_TRACELOG need to be defined");
			}
		}
		if (!isset(static::$instance)) {
            static::$instance = "initialized";
			$query = "
				CREATE TABLE IF NOT EXISTS `".$table."` (
				  `id` int(20) NOT NULL AUTO_INCREMENT,
				  `log` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
				  `tt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
			$conn->Execute($query);
        }
		
		$query = "INSERT INTO `".$table."` (log) VALUES ( ? )";
		$str = is_string($str)?$str : json_encode($str);
		$conn->Execute($query, array($str));
	}
	
}