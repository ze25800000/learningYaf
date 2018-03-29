<?php

class Common_Password {
	private static $salt = 'salt-yangze';

	public static function pwdEncode( $pwd ) {
		return md5( self::$salt . $pwd );
	}
}