<?php

include_once( dirname( __FILE__ ) . '/../../vendor/autoload.php' );

class ArtController extends Yaf_Controller_Abstract {
	public function indexAction() {
		return $this->listAction();
	}

	public function addAction( $artId = 0 ) {
		if ( ! Admin_Object::isAdmin() ) {
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

		return false;
	}

	public function editAction() {
		if ( ! Admin_Object::isAdmin() ) {
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

		return false;
	}

	public function delAction() {
		if ( ! Admin_Object::isAdmin() ) {
			echo json_encode( [
				'errno'  => - 2000,
				"errmsg" => "需要管理员才可以操作"
			] );

			return false;
		}
		$artId = $this->getRequest()->getQuery( 'artId', 0 );
		if ( $artId && is_numeric( $artId ) ) {
			$model = new ArtModel();
			if ( $model->del( $artId ) ) {
				echo json_encode( [
					'errno'  => 0,
					"errmsg" => ""
				] );
			} else {
				echo json_encode( [
					'errno'  => $model->errno,
					"errmsg" => $model->errmsg,
				] );

				return false;
			}
		} else {
			echo json_encode( [
				'errno'  => - 2003,
				"errmsg" => "缺少必要的文章ID参数"
			] );

			return false;
		}

		return false;
	}

	public function statusAction() {
		if ( ! Admin_Object::isAdmin() ) {
			echo json_encode( [
				'errno'  => - 2000,
				"errmsg" => "需要管理员才可以操作"
			] );

			return false;
		}
		$artId  = $this->getRequest()->getQuery( 'artId', 0 );
		$status = $this->getRequest()->getQuery( 'status', 'offline' );
		if ( $artId && is_numeric( $artId ) ) {
			$model = new ArtModel();
			if ( $model->status( $artId, $status ) ) {
				echo json_encode( [
					'errno'  => 0,
					"errmsg" => ""
				] );
			} else {
				echo json_encode( [
					'errno'  => $model->errno,
					"errmsg" => $model->errmsg,
				] );

				return false;
			}
		} else {
			echo json_encode( [
				'errno'  => - 2003,
				"errmsg" => "缺少必要的文章ID参数"
			] );

			return false;
		}

		return false;
	}

	public function getAction() {
		$artId = $this->getRequest()->getQuery( 'artId', 0 );
		if ( $artId && is_numeric( $artId ) ) {
			$model = new ArtModel();
			if ( $data = $model->get( $artId ) ) {
				echo json_encode( [
					'errno'  => 0,
					"errmsg" => "",
					"data"   => $data
				] );
			} else {
				echo json_encode( [
					'errno'  => - 2009,
					"errmsg" => "获取文章信息失败",
				] );
			}
		} else {
			echo json_encode( [
				'errno'  => - 2003,
				"errmsg" => "缺少必要的文章ID参数"
			] );
		}

		return false;
	}

	public function listAction() {
		$pageNo   = $this->getRequest()->getQuery( 'pageNo', 0 );
		$pageSize = $this->getRequest()->getQuery( 'pageSize', 10 );
		$cate     = $this->getRequest()->getQuery( 'cate', 0 );
		$status   = $this->getRequest()->getQuery( 'status', 'offline' );

		$model = new ArtModel();
		if ( $data = $model->list( $pageNo, $pageSize, $cate, $status ) ) {
			echo json_encode( [
				'errno'  => 0,
				"errmsg" => "",
				"data"   => $data
			] );
		} else {
			echo json_encode( [
				'errno'  => - 2012,
				"errmsg" => "获取文章列表失败"
			] );
		}

		return false;
	}
}
