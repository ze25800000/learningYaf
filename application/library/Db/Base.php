<?php

class Db_Base {
	public static $errno = 0;
	public static $errmsg = "";
	public static $db = null;

	public static function getDb() {
		if ( self::$db == null ) {
			self::$db = new PDO( 'mysql:host=127.0.0.1;dbname=yaf;', 'root', 'Yangze@1234' );
		}

		return self::$db;
	}

	public static function errno() {
		return self::$errno;
	}

	public static function errmsg() {
		return self::$errmsg;
	}
}