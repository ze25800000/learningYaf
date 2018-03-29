<?php

class UserController extends Yaf_Controller_Abstract {
	public function indexAction() {
		return $this->loginAction();
	}

	public function loginAction() {

		//防止爬虫
		$submit = Common_Request::getRequest( 'submit', 0 );
		if ( $submit != 1 ) {
			echo json_encode( Err_Map::get( 1001 ) );
			return false;
		}

		//获取参数，并验证参数
		$uname = Common_Request::postRequest( 'uname', '' );
		$pwd   = Common_Request::postRequest( 'pwd', '' );

		if ( ! $uname || ! $pwd ) {
			echo json_encode( Err_Map::get( 1002 ) );

			return false;
		}
		try {
			//调用数据库
			$model = new UserModel();
			$uid   = $model->login( trim( $uname ), trim( $pwd ) );
		} catch ( Exception $e ) {
			echo json_encode( Err_Map::get( 1000 ) );

			return false;
		}
		if ( $uid ) {

			session_start();
			$_SESSION['user_token']      = md5( 'salt' . $_SERVER['REQUEST_TIME'] . $uid );
			$_SESSION['user_token_time'] = $_SERVER['REQUEST_TIME'];
			$_SESSION['user_id']         = $uid;

			echo Common_Request::response( 0, "", [ 'name' => $uname ] );
		} else {
			echo Common_Request::response( $model->errno, $model->errmsg );
		}

		return false;
	}

	public function registerAction() {
		//防止爬虫
		$submit = Common_Request::getRequest( 'submit', 0 );
		if ( $submit != 1 ) {
			echo json_encode( Err_Map::get( 1001 ) );

			return false;
		}

		//获取参数，并验证参数
		$uname = Common_Request::postRequest( 'uname', '' );
		$pwd   = Common_Request::postRequest( 'pwd', '' );
		if ( ! $uname || ! $pwd ) {
			echo json_encode( Err_Map::get( 1002 ) );
		}

		//调用model,做登录验证
		$model = new UserModel();
		if ( $model->register( trim( $uname ), trim( $pwd ) ) ) {
			echo Common_Request::response( $data = [ 'name' => $uname ] );
		} else {
			echo Common_Request::response( $model->errno, $model->errmsg );
		}

		return false;
	}
}
