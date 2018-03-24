<?php

/**
 * @name IndexController
 * @author
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class ArtController extends Yaf_Controller_Abstract {
	public function indexAction() {
		return $this->listAction();
	}

	public function addAction( $artId = 0 ) {
		if ( ! $this->_isAdmin() ) {
			echo json_encode( [
				'errno'  => - 2000,
				"errmsg" => "需要管理员才可以操作"
			] );

			return false;
		}
		$submit = $this->getRequest()->getQuery( 'submit', 0 );
		if ( $submit != '1' ) {
			echo json_encode( [
				'errno'  => - 2001,
				'errmsg' => '请通过正确渠道提交'
			] );

			return false;
		}
		$title    = $this->getRequest()->getPost( 'title', false );
		$contents = $this->getRequest()->getPost( 'contents', false );
		$author   = $this->getRequest()->getPost( 'author', false );
		$cate     = $this->getRequest()->getPost( 'cate', false );

		if ( ! $title || ! $contents || ! $author || ! $cate ) {
			echo json_encode( [
				'errno'  => - 2002,
				'errmsg' => '标题、内容、作者、分类信息，不能为空'
			] );

			return false;
		}
		// 调用Model ,做登录验证
		$model = new ArtModel();
		if ( $lastId = $model->add( trim( $title ), trim( $contents ), trim( $author ), trim( $cate ), $artId ) ) {
			echo json_encode( [
				'errno'  => 0,
				'errmsg' => '',
				'data'   => [ 'lastId' => $lastId ]
			] );
		} else {
			echo json_encode( [
				'errno'  => $model->errno,
				'errmsg' => $model->errmsg,
			] );
		}

		return true;
	}

	public function editAction() {
		if ( !$this->_isAdmin() ) {
			echo json_encode( [
				'errno'  => - 2000,
				"errmsg" => "需要管理员才可以操作"
			] );

			return false;
		}
		$artId = $this->getRequest()->getQuery( 'artId', '0' );
		if ( is_numeric( $artId ) && $artId ) {
			$this->addAction( $artId );
		} else {
			echo json_encode( [
				'errno'  => - 2003,
				"errmsg" => "缺少必要的文章ID参数"
			] );

			return false;
		}

		return true;
	}

	public function delAction() {

	}

	public function statusAction() {

	}

	public function getAction() {

	}

	public function listAction() {

	}

	private function _isAdmin() {
		return true;
	}
}