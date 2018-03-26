<?php

class Common_Password {
	const SALT = 'HELLO WORLD';

	public static function pwdEncode( $pwd ) {
		return md5( self::SALT . $pwd );
	}
}