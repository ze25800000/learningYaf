<?php

class UserModel {
	public $errno = 0;
	public $errmsg = "";
	private $_dao = null;

	public function __construct() {
		$this->_dao = new Db_User();
	}

	public function login( $uname, $pwd ) {
		$userInfo = $this->_dao->find( $uname );
		if ( ! $userInfo ) {
			$this->errno  = $this->_dao->errno();
			$this->errmsg = $this->_dao->errmsg();

			return false;
		}
		if ( Common_Password::pwdEncode( $pwd ) != $userInfo['pwd'] ) {
			$this->errno  = Err_Map::get( 1004 )['errno'];
			$this->errmsg = Err_Map::get( 1004 )['errmsg'];

			return false;
		}

		return $userInfo[1];
	}

	public function register( $uname, $pwd ) {
		if ( ! $this->_dao->checkExist( $uname ) ) {
			$this->errno  = $this->_dao->errno();
			$this->errmsg = $this->_dao->errmsg();

			return false;
		}

		if ( strlen( $pwd ) < 8 ) {
			list( $this->errno, $this->errmsg ) = Err_Map::get( 1006 );

			return false;
		} else {
			$password = Common_Password::pwdEncode( $pwd );
		}
		if ( ! $this->_dao->addUser( $uname, $password ) ) {
			$this->errno  = $this->_dao->errno();
			$this->errmsg = $this->_dao->errmsg();

			return false;
		}

		return true;
	}
}