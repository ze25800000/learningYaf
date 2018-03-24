<?php

class ArtModel {
	public $errno = 0;
	public $errmsg = "";
	private $_db = null;

	public function __construct() {
		$this->_db = new PDO( 'mysql:host=127.0.0.1;dbname=yaf;', 'root', 'Yangze@1234' );
	}

}
