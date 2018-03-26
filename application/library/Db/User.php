<?php

class Db_User extends Db_Base {
	public function find( $uname ) {
		$query = self::getDb()->prepare( "select `pwd`,`id` from `user` where `name`=?" );
		$query->execute( [ $uname ] );
		$ret = $query->fetchAll();
		if ( ! $ret || count( $ret ) != 1 ) {
			self::$errno  = - 1003;
			self::$errmsg = '用户查找失败';

			return false;
		}

		return $ret[0];
	}

	public function checkExist( $uname ) {
		$query = self::getDb()->prepare( "select count(*) as c from user where name=?" );
		$query->execute( [ $uname ] );
		$count = $query->fetchAll();
		if ( $count[0]['c'] > 0 ) {
			self::$errno  = - 1005;
			self::$errmsg = '用户名已存在';

			return false;
		}

		return true;
	}

	public function addUser( $uname, $pwd ) {
		$query = self::getDb()->prepare( "insert into user (`id`,`name`,`pwd`,`reg_time`) VALUES (null,?,?,?)" );
		$ret   = $query->execute( [ $uname, $pwd, date( "Y-m-d H:i:s" ) ] );
		if ( ! $ret ) {
			self::$errno  = - 1006;
			self::$errmsg = '注册失败，写入数据失败';

			return false;
		}

		return true;
	}
}