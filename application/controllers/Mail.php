<?php


class MailController extends Yaf_Controller_Abstract {
	public function indexAction() {
		return $this->loginAction();
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

		// 获取参数
		$uid     = $this->getRequest()->getPost( 'uid', false );
		$title   = $this->getRequest()->getPost( 'title', false );
		$content = $this->getRequest()->getPost( 'content', false );

		if ( ! $uid || ! $title || !$content ) {
			echo json_encode( [
				'errno'  => - 3002,
				'errmsg' => '用户ID、邮件标题、邮件内容均不能为空。'
			] );

			return false;
		}

		//调用Model，发邮件
		$model = new MailModel();
		if ( $model->send( intval( $uid ), trim( $title ), trim( $content ) ) ) {
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

		return true;
	}
}
