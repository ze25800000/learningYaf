<?php

class Err_Map {
	private static $err_map = [
		1000 => 'Exception error',
		1001 => '请通过正确渠道提交',
		1002 => "用户名和密码必须传递",
		1003 => "用户查找失败",
		1004 => "密码错误",
		1005 => "用户名已存在",
		1006 => '密码太短，请设置至少8位密码'
	];

	public static function get( $code ) {
		$var = self::$err_map[ $code ];
		if ( isset( $var ) ) {
			return [
				'errno'  => 0 - $code,
				'errmsg' => $var,
			];
		}

		return [
			'errno'  => 0 - $code,
			'errmsg' => 'undefined this error number.',
		];
	}
}