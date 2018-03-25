<?php


class SmsController extends Yaf_Controller_Abstract {
	public function indexAction() {
	}

	public function sendAction() {
		$submit = $this->getRequest()->getQuery( 'submit', 0 );
		if ( $submit != '1' ) {
			echo json_encode( [
				'errno'  => - 2001,
				'errmsg' => '请通过正确渠道提交'
			] );

			return false;
		}

		/**
		 * 参数获取
		 */
		$uid        = $this->getRequest()->getPost( 'uid', false );
		$templateId = $this->getRequest()->getPost( 'templateId', false );
		if ( ! $uid || ! $templateId ) {
			echo json_encode( [
				'errno'  => - 4002,
				'errmsg' => '用户ID、templateId均不能为空'
			] );

			return false;
		}

		//调用Model，发邮件
		$model = new SmsModel();
		if ( $model->send( intval( $uid ), intval( $templateId ) ) ) {
			echo json_encode( [
				'errno'  => 0,
				'errmsg' => ''
			] );

		} else {
			echo json_encode( [
				'errno'  => $model->errno,
				'errmsg' => $model->errmsg
			] );
		}

		return false;
	}
}
