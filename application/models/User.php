<?php

/**
 * @name SampleModel
 * @desc sample数据获取类, 可以访问数据库，文件，其它系统等
 * @author
 */
class UserModel {
	public $errno = 0;
	public $errmsg = "";
	private $_db = null;

	public function __construct() {
		$this->_db = new PDO( 'mysql:host=127.0.0.1;dbname=yaf;', 'root', 'Yangze@1234' );
	}

	public function login( $uname, $pwd ) {
		$query = $this->_db->prepare( "select `pwd`,`id` from `user` where `name`=?" );
		$query->execute( [ $uname ] );
		$ret = $query->fetchAll();
		if ( ! $ret || count( $ret ) != 1 ) {
			$this->errno  = - 1003;
			$this->errmsg = '用户查找失败';

			return false;
		}
		$userInfo = $ret[0];
		if ( Common_Password::pwdEncode( $pwd ) != $userInfo['pwd'] ) {
			$this->errno  = - 1003;
			$this->errmsg = '密码错误';

			return false;
		}

		return intval( $userInfo[1] );
	}

	public function register( $uname, $pwd ) {
		$query = $this->_db->prepare( "select count(*) as c from user where name=?" );
		$query->execute( [ $uname ] );
		$count = $query->fetchAll();
		if ( $count[0]['c'] > 0 ) {
			$this->errno  = - 1005;
			$this->errmsg = '用户名已存在';

			return false;
		}
		if ( strlen( $pwd ) < 8 ) {
			$this->errmsg = "密码长度不足8位";
			$this->errno  = - 1006;

			return false;
		} else {
			$password = Common_Password::pwdEncode( $pwd );
		}

		$query = $this->_db->prepare( "insert into user (`id`,`name`,`pwd`,`reg_time`) VALUES (null,?,?,?)" );
		$ret   = $query->execute( [ $uname, $password, date( "Y-m-d H:i:s" ) ] );
		if ( ! $ret ) {
			$this->errno  = - 1006;
			$this->errmsg = '注册失败，写入数据失败';

			return false;
		}

		return true;
	}
}
