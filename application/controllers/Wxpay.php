<?php

$qrcodeLibPath = dirname( __FILE__ ) . '/../library/ThirdParty/Qrcode/';
include_once( $qrcodeLibPath . 'Qrcode.php' );

class WxpayController extends Yaf_Controller_Abstract {


	public function createbillAction() {
		$itemId = $this->getRequest()->getQuery( 'itemId', '' );
		if ( ! $itemId ) {
			echo json_encode( [
				'errno'  => - 6001,
				'errmsg' => "请传递正确的商品ID",
			] );

			return false;
		}

		/**
		 * 检测是否登录
		 */
		session_start();
		if ( ! isset( $_SESSION['user_id'] ) && ! isset( $_SESSION['user_token'] ) && ! isset( $_SESSION['user_id'] ) && md5( 'salt' . $_SESSION['user_token_time'] . $_SESSION['user_id'] != $_SESSION['user_token'] ) ) {
			echo json_encode( [
				'errno'  => - 6002,
				'errmsg' => "请先登录",
			] );

			return false;
		}

		/**
		 * 调用Modle
		 */
		$model = new WxpayModel();
		if ( $data = $model->createbill( $itemId, $_SESSION['user_id'] ) ) {
			echo json_encode( [
				'errno'  => 0,
				'errmsg' => "",
				'data'   => $data
			] );
		} else {
			echo json_encode( [
				'errno'  => $model->errno,
				'errmsg' => $model->errmsg
			] );
		}

		return false;
	}

	public function qrcodeAction() {
		$billId = $this->getRequest()->getQuery( 'billId', '' );
		if ( ! $billId ) {
			echo json_encode( [
				'errno'  => - 6008,
				'errmsg' => "请传递正确的订单ID",
			] );

			return false;
		}
		//调用Model
		$model = new WxpayModel();
		if ( $data = $model->qrcode( $billId ) ) {
			/**
			 * 输出二维码
			 */
			QRcode::png( $data );
		} else {
			echo json_encode( [
				'errno'  => $model->errno,
				'errmsg' => $model->errmsg
			] );
		}

		return false;
	}

	public function callbackAction() {
		$model = new WxpayModel();
		$model->callback();
		echo json_encode( [
			'errno'  => 0,
			'errmsg' => 0
		] );

		return false;
	}

}
