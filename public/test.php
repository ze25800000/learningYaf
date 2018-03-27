<?php

class father {
	public static $a = 0;
	public static $b = '';
}

class son extends father {
	public static function test() {
		list( self::$a, self::$b ) = [ 'hello', 'world' ];

		return self::$a . self::$b;
	}
}

echo son::test();