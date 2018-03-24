<?php

class ArtModel {
	public $errno = 0;
	public $errmsg = "";
	private $_db = null;

	public function __construct() {
		$this->_db = new PDO( 'mysql:host=127.0.0.1;dbname=yaf;', 'root', 'Yangze@1234' );
		/**
		 * 不设置下面这行的话，PDO会在拼SQL的时候，把int 0转成 string 0
		 */
		$this->_db->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
	}

	public function add( $title, $contents, $author, $cate, $artId = 0 ) {
		$isEdit = false;
		if ( $artId != 0 && is_numeric( $artId ) ) {
			/* edit */
			$query = $this->_db->prepare( "select count(*) from `art` WHERE `id`=?" );
			$query->execute( [ $artId ] );
			$ret = $query->fetchAll();
			if ( ! $ret || count( $ret ) != 1 ) {
				$this->errno  = - 2003;
				$this->errmsg = "找不到你要的编辑文章";

				return false;
			}
			$isEdit = true;
		} else {
			/* add */
			$query = $this->_db->prepare( "select count(*) from `cate` WHERE `id`=?" );
			$query->execute( [ $cate ] );
			$ret = $query->fetchAll();
			if ( ! $ret || $ret [0][0] == 0 ) {
				$this->errno  = - 2005;
				$this->errmsg = "找不到你要的分类信息，cate id：" . $cate . "，请先创建该分类";

				return false;
			}
		}
		/**
		 * 插入或者更新文章
		 */
		$data = [ $title, $contents, $author, intval( $cate ) ];
		if ( ! $isEdit ) {
			$query = $this->_db->prepare( 'insert into `art` (`title`,`content`,`author`,`cate`) VALUES (?,?,?,?)' );
		} else {
			$query  = $this->_db->prepare( "UPDATE `art` SET title=?,content=?,author=?,cate=? WHERE id=?" );
			$data[] = $artId;
		}
		$ret = $query->execute( $data );
		if ( ! $ret ) {
			$this->errno  = - 2006;
			$this->errmsg = '操作文章数据表失败，ErrInfo:' . end( $query->errorInfo() );

			return false;
		}
		/**
		 * 返回文章最后的ID值
		 */
		if ( ! $isEdit ) {
			return intval( $this->_db->lastInsertId() );
		} else {
			return intval( $artId );
		}
	}
}
