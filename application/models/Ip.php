<?php

class IpModel {
	public $errno = 0;
	public $errmsg = "";


	public function get( $ip ) {
		$rep = ThirdParty_Ip::find( $ip );

		return $rep;
	}
}
