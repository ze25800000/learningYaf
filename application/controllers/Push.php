<?php


class PushController extends Yaf_Controller_Abstract {
	public function singleAction() {
		if ( ! Admin_Object::isAdmin()) {
			echo json_encode( [
				'errno'  => - 2000,
				"errmsg" => "需要管理员才可以操作"
			] );

			return false;
		}

		$cid = $this->getRequest()->getQuery( 'cid', "" );
		$msg = $this->getRequest()->getQuery( 'msg', "" );
		if ( ! $cid || ! $msg ) {
			echo json_encode( [
				'errno'  => - 7002,
				"errmsg" => "请输入推送用户的设备ID与要推送的内容"
			] );

			return false;
		}

		//调用Model
		$model = new PushModel();
		if ( $model->single( $cid, $msg ) ) {
			echo json_encode( [
				'errno'  => 0,
				"errmsg" => ""
			] );
		} else {
			echo json_encode( [
				'errno'  => $model->errno,
				"errmsg" => $model->errmsg
			] );
		}

		return false;
	}

	public function toallAction() {
		if ( ! Admin_Object::isAdmin()) {
			echo json_encode( [
				'errno'  => - 2000,
				"errmsg" => "需要管理员才可以操作"
			] );

			return false;
		}

		$msg = $this->getRequest()->getQuery( 'msg', "" );
		if ( ! $msg ) {
			echo json_encode( [
				'errno'  => - 7002,
				"errmsg" => "请输入推送用户要推送的内容"
			] );

			return false;
		}

		//调用Model
		$model = new PushModel();
		if ( $model->toAll( $msg ) ) {
			echo json_encode( [
				'errno'  => 0,
				"errmsg" => ""
			] );
		} else {
			echo json_encode( [
				'errno'  => $model->errno,
				"errmsg" => $model->errmsg
			] );
		}

		return false;
	}
}
