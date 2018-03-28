<?php

class UserController extends Yaf_Controller_Abstract {
	public function indexAction() {
		return $this->loginAction();
	}

	public function loginAction() {
		$uname = Common_Request::postRequest( 'uname', '' );
		$pwd   = Common_Request::postRequest( 'pwd', '' );

		if ( ! $uname || ! $pwd ) {
			echo json_encode( Err_Map::get( 1002 ) );
		}

		return false;
	}

	public function registerAction() {

	}
}
