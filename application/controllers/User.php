<?php

class UserController extends Yaf_Controller_Abstract {
	public function indexAction() {
		return $this->loginAction();
	}

	public function loginAction() {
		print_r( Common_Request::request() );

		return false;
	}

	public function registerAction() {

	}
}
