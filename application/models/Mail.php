<?php

require __DIR__ . '/../../vendor/autoload.php';

use Nette\Mail\Message;

class MailModel {
	public $errno = 0;
	public $errmsg = "";
	private $_db = null;

	public function __construct() {
		$this->_db = new PDO( 'mysql:host=127.0.0.1;dbname=yaf;', 'root', 'Yangze@1234' );
	}

	public function send( $uid, $title, $content ) {
		$query = $this->_db->prepare( "select `email` from `user` WHERE `id`=?" );
		$query->execute( [intval( $uid )] );
		$ret = $query->fetchAll();
		if ( ! $ret || count( $ret ) != 1 ) {
			if ( ! $ret || count( $ret ) != 1 ) {
				$this->errno  = - 3003;
				$this->errmsg = '用户邮箱查找失败';

				return false;
			}
		}
		$userEmail = $ret[0]['email'];
		if ( ! filter_var( $userEmail, FILTER_VALIDATE_EMAIL ) ) {
			$this->errno  = - 3003;
			$this->errmsg = '用户邮箱不符合标准，邮箱地址为：' . $userEmail;

			return false;
		}

		$mail = new Message();
		$mail->setFrom( 'PHP是世界上最好的语言 <1726249137@qq.com>' )
		     ->addTo( $userEmail )
		     ->setSubject( $title )
		     ->setBody( $content );
		$mailer = new Nette\Mail\SmtpMailer( [
			'host'     => ' smtp.163.com',
			'username' => 'ze25800000@163.com',
			'password' => 'yangze@wangyi',
			'secure'   => 'ssl'
		] );
		$rep    = $mailer->send( $mail );

		return true;
	}

}
