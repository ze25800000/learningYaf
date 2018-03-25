<?php

class SmsModel {
	public $errno = 0;
	public $errmsg = "";
	private $_db = null;

	public function __construct() {
		$this->_db = new PDO( 'mysql:host=127.0.0.1;dbname=yaf;', 'root', 'Yangze@1234' );
	}

	public function send( $uid, $templateId ) {
		$query = $this->_db->prepare( "select `mobile` from `user` WHERE `id`=?" );
		$query->execute( [ intval( $uid ) ] );
		$ret = $query->fetchAll();
		if ( ! $ret || count( $ret ) != 1 ) {
			$this->errno  = - 4003;
			$this->errmsg = '用户手机号信息查找失败';

			return false;
		}
		$userMobile = $ret[0]['mobile'];
		if ( ! $userMobile || ! is_numeric( $userMobile ) || strlen( $userMobile ) != 11 ) {
			$this->errno  = - 4004;
			$this->errmsg = '用户手机号信息不符合标准，手机号为：' . ( ! $userMobile ? "空" : $userMobile );

			return false;
		}
		$smsUid       = 'ze25800000';
		$smsPwd       = 'yangze1234';
		$sms          = new ThirdParty_Sms( $smsUid, $smsPwd );
		$contentParam = [ 'code' => rand( 1000, 9999 ) ];
		$template     = $templateId;
		$result       = $sms->send( $userMobile, $contentParam, $template );
		if ( $result['stat'] == 100 ) {
			return true;
		} else {
			$this->errno  = - 4005;
			$this->errmsg = '发送失败：' . $result['stat'] . '(' . $result['message'] . ')';

			return false;
		}
	}
}
