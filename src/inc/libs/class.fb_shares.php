<?php
/* Class file create by Kenny on 2015-04-13 */
require_once 'class.ncapputil.php';
class fb_shares
{
	private $mytable;
	
	function __construct(){
		$this->_prepareDB();
	}
	
	public function addFBShare($uid, $post_id, $periodIndex=0){
		$conn = ncAppUtil::getConnection();
		$query = "INSERT INTO `".$this->mytable."` ( uid, post_id, periodIndex ) VALUES ( ? , ? ,?)";
		$conn->Execute($query, array($uid, $post_id, $periodIndex));
	}
	
	public function getFBShares($uid, $periodIndex=0){
		$conn = ncAppUtil::getConnection();
		$condition = ($periodIndex > 0) ? " AND periodIndex = ?" : "";
		$query = "SELECT * FROM `".$this->mytable."` WHERE uid = ?".$condition;
		return $conn->GetArray($query, array($uid,$periodIndex));
	}
	
	private function _prepareDB(){
		if(defined("DB_FBSHARE")){
			$this->mytable = DB_FBSHARE;
		}else{
			//generate table if DB_PREFIX is exist
			if(defined("DB_PREFIX")){
				$this->mytable = DB_PREFIX."__fbshares";
			}else{
				die("either DB_PREFIX or DB_FBSHARE need to be defined");
			}
		}
		$this->_tablegenerate();
	}
	
	private function _tablegenerate(){
		$conn = ncAppUtil::getConnection();
		$query = "CREATE TABLE IF NOT EXISTS `".$this->mytable."` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `uid` int(11) NOT NULL,
			  `post_id` text COLLATE utf8_bin NOT NULL,
			  `periodIndex` int(11) NOT NULL,
			  `tt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
			";
		$conn->execute($query);
	}
}
?>