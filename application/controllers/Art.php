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
		if ( $this->_isAdmin() ) {
			echo json_encode( [
				'errno'  => - 2000,
				"errmsg" => "需要管理员才可以操作"
			] );
		}
	}

	public function editAction() {

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

	}
}
