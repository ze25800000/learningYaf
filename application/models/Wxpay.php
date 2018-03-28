<?php
$wxpayLibPath = dirname( __FILE__ ) . '/../library/ThirdParty/Wxpay/';
include_once( $wxpayLibPath . 'WxPay.Api.php' );
include_once( $wxpayLibPath . 'WxPay.Config.php' );
include_once( $wxpayLibPath . 'WxPay.Data.php' );
include_once( $wxpayLibPath . 'WxPay.Exception.php' );
include_once( $wxpayLibPath . 'WxPay.Notify.php' );
include_once( $wxpayLibPath . 'WxPay.NativePay.php' );

class WxpayModel extends WxPayNotify {
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

	public function createbill( $itemId, $uid ) {
		$query = $this->_db->prepare( "select * from `item` WHERE `id`=?" );
		$query->execute( [ $itemId ] );
		$ret = $query->fetchAll();
		if ( ! $ret || count( $ret ) != 1 ) {
			$this->errno  = - 6003;
			$this->errmsg = '找不到商品';

			return false;
		}
		$item = $ret[0];
		if ( strtotime( $item['etime'] ) <= time() ) {
			$this->errno  = - 6004;
			$this->errmsg = '商品已经过期';

			return false;
		}
		if ( intval( $item['stock'] ) <= 0 ) {
			$this->errno  = - 6005;
			$this->errmsg = '商品库存不够，不能购买';

			return false;
		}
		try {
			$this->_db->beginTransaction();

			/**
			 * 创建订单
			 */
			$query = $this->_db->prepare( "insert into `bill` (`itemid`,`uid`,`price`,`status`,`ctime`) VALUES (?,?,?,'unpaid',date( 'Y-m-d H:i:s' ))" );
			$ret   = $query->execute( [ $itemId, $uid, intval( $item['price'] ) ] );
			if ( ! $ret ) {
				$this->errno  = - 6006;
				$this->errmsg = '创建账单失败';
				throw new PDOException( '创建账单失败' );
			}
			$lastId = intval( $this->_db->lastInsertId() );

			/**
			 * 库存减1
			 */
			$query = $this->_db->prepare( "UPDATE `item` SET `stock`=`stock`-1 WHERE `id`=?" );
			$ret   = $query->execute( [ $itemId ] );
			if ( ! $ret ) {
				$this->errno  = - 6007;
				$this->errmsg = '更新库存失败';
				throw new PDOException( '更新库存失败' );
			}
			$this->_db->commit();
		} catch ( PDOException $e ) {
			$this->_db->rollback();
		}

		return $lastId;
	}

	public function qrcode( $billId ) {
		$query = $this->_db->prepare( "select * from `bill` WHERE `id`=?" );
		$query->execute( [ $billId ] );
		$ret = $query->fetchAll();
		if ( ! $ret || count( $ret ) != 1 ) {
			$this->errno  = - 6009;
			$this->errmsg = '找不到账单信息';

			return false;
		}
		$bill  = $ret[0];
		$query = $this->_db->prepare( "select * from `item` WHERE `id`=?" );
		$query->execute( [ $bill['itemid'] ] );
		$ret = $query->fetchAll();
		if ( ! $ret || count( $ret ) != 1 ) {
			$this->errno  = - 6010;
			$this->errmsg = '找不到商品信息';

			return false;
		}
		$item = $ret[0];

		$input = new WxPayUnifiedOrder();
		$input->SetBody( $item['name'] );
		$input->SetAttach( $billId );
		$input->SetOut_trade_no( WxPayConfig::MCHID . date( "YmdHis" ) );
		$input->SetTotal_fee( $bill['price'] );
		$input->SetTime_start( date( "YmdHis" ) );
		$input->SetTime_expire( date( "YmdHis", time() + 86400 * 3 ) );
		$input->SetGoods_tag( $item['name'] );
		$input->SetNotify_url( 'http://demo2.xmwsh0479.com/wxpay/callback' );
		$input->SetTrade_type( "NATIVE" );
		$input->SetProduct_id( $billId );

		$notify = new NativePay();
		$result = $notify->GetPayUrl( $input );
		$url    = $result['code_url'];

		return $url;
	}

	public function callback() {
		$xmlData = file_get_contents( 'php://input' );
		if ( substr_count( ! $xmlData, "<result_code><![CDATA[SUCCESS]></result_code>" ) == 1 &&
		     substr_count( ! $xmlData, "<return_code><![CDATA[SUCCESS]></return_code>" ) == 1
		) {
			preg_match( '/<attach>(.*)\[(\d+)\](.*)<\/attach>/i', $xmlData, $match );
			if ( isset( $match[2] ) && is_numeric( $match[2] ) ) {
				$billId = intval( $match[2] );
			}
			preg_match( '/<transaction_id>(.*)\[(\d+)\](.*)<\/transaction_id>/i', $xmlData, $match );
			if ( isset( $match[2] ) && is_numeric( $match[2] ) ) {
				$transactionId = intval( $match[2] );
			}
		}
		if ( isset( $billId ) && isset( $transactionId ) ) {
			$query = $this->_db->prepare( "UPDATE `bill` SET `transaction`=? ,`ptime`=?, `status`='paid' WHERE `id`=?" );
			$query->execute( [ $transactionId, date( "Y-m-d H:i:s" ), $billId ] );
		}
	}
}
