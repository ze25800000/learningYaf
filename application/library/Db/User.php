<?php

class Db_User extends Db_Base {
	public function find( $uname ) {
		$query = self::getDb()->prepare( "select `pwd`,`id` from `user` where `name`=?" );
		$query->execute( [ $uname ] );
		$ret = $query->fetchAll();
		if ( ! $ret || count( $ret ) != 1 ) {
			self::$errno  = Err_Map::get( 1003 )['errno'];
			self::$errmsg = Err_Map::get( 1003 )['errmsg'];

			return false;
		}

		return $ret[0];
	}

	public function checkExist( $uname ) {
		$query = self::getDb()->prepare( "select count(*) as c from user where name=?" );
		$query->execute( [ $uname ] );
		$count = $query->fetchAll();
		if ( $count[0]['c'] > 0 ) {
			self::$errno  = Err_Map::get( 1005 )['errno'];
			self::$errmsg = Err_Map::get( 1005 )['errmsg'];

			return false;
		}

		return true;
	}

	public function addUser( $uname, $pwd ) {
		$query = self::getDb()->prepare( "insert into user (`id`,`name`,`pwd`,`reg_time`) VALUES (null,?,?,?)" );
		$ret   = $query->execute( [ $uname, $pwd, date( "Y-m-d H:i:s" ) ] );
		if ( ! $ret ) {
			self::$errno  = Err_Map::get( 1006 )['errno'];
			self::$errmsg = Err_Map::get( 1006 )['errmsg'];

			return false;
		}

		return true;
	}
}