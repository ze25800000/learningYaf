<?php

/**
 * @name SampleModel
 * @desc sample数据获取类, 可以访问数据库，文件，其它系统等
 * @author
 */
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
			list( $this->errno, $this->errmsg ) = Err_Map::get( 1003 );

			return false;
		}

		return intval( $userInfo[1] );
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
