<?php

class Common_Request {
	public static function request( $key, $default = null, $type = null ) {
		if ( $type == 'get' ) {
			$result = isset( $_GET[ $key ] ) ? $_GET[ $key ] : null;
		} elseif ( $type == 'post' ) {
			$result = isset( $_POST[ $key ] ) ? $_POST[ $key ] : null;
		} else {
			$result = isset( $_REQUEST[ $key ] ) ? $_REQUEST[ $key ] : null;
		}

		return $result;
	}

	public static function getRequest( $key, $default = null ) {
		return self::request( $key, $default, 'get' );
	}

	public static function postRequest( $key, $default = null ) {
		return self::request( $key, $default, 'post' );
	}

	public static function response( $errno = 0, $errmsg = "", $data = [] ) {
		$arr = [
			"errmsg" => $errmsg,
			"errno"  => $errno
		];
		if ( ! empty( $data ) ) {
			$arr['data'] = $data;
		}

		return json_encode( $arr );
	}
}