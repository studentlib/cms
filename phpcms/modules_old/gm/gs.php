<?php
defined ( 'IN_PHPCMS' ) or exit ( 'No permission resources.' );
pc_base::load_app_class ( 'admin', 'admin', 0 );
pc_base::load_sys_class ( 'form', '', 0 );
ini_set ( 'html_errors', '1' );
error_reporting ( E_ALL );
class gs extends admin {
	/**
	 *
	 * @var Redis
	 */
	private $_redis;
	public function __construct() {
	}
	public function index() {
		$files = glob ( "F:/work/sanguo/src/configs/gameserver/configs/*" );
		$configs = array ();
		foreach ( $files as $v ) {
			$configs [] = pathinfo ( $v, PATHINFO_FILENAME );
		}
		$servers = $this->get_server_config ();
		include $this->admin_tpl ( 'gs', 'gm' );
	}
	public function maintain() {
		$files = glob ( "F:/work/sanguo/src/configs/gameserver/configs/*" );
		$configs = array ();
		foreach ( $files as $v ) {
			$configs [] = pathinfo ( $v, PATHINFO_FILENAME );
		}
		$servers = $this->get_server_config ();
		include $this->admin_tpl ( 'mt', 'gm' );
	}
	public function sendNotice() {
		$ret = array (
				'code' => 0,
				'msg' => '' 
		);
		if (isset ( $_GET ['sid'] ) && isset ( $_GET ['msg'] )) {
			$sids = explode ( ',', $_GET ['sid'] );
			$servers = $this->get_server_config ();
			foreach ( $sids as $value ) {
				if (isset ( $servers [$value] )) {
					$server = $servers [$value];
					$this->_sendNotice ( $server ['GIP'], $server ['GPort'], urldecode ( $_GET ['msg'] ) );
					$ret ['msg'] = '发送成功';
					self::manage_log ();
				} else {
					$ret ['code'] = 1;
					$ret ['msg'] = 'no_such_server';
				}
			}
		} else {
			$ret ['code'] = 2;
			$ret ['msg'] = 'lost_param';
		}
		echo json_encode ( $ret );
	}
	public function kickAll() {
		$ret = array (
				'code' => 0,
				'msg' => '' 
		);
		if (isset ( $_GET ['sid'] )) {
			$servers = $this->get_server_config ();
			$sids = explode ( ',', $_GET ['sid'] );
			foreach ( $sids as $value ) {
				if (isset ( $servers [$value] )) {
					$server = $servers [$value];
					$this->_kickAll ( $server ['GIP'], $server ['GPort'] );
					$ret ['msg'] = 'Thư Kick-người đã được gửi thành công';
					self::manage_log ();
				} else {
					$ret ['code'] = 1;
					$ret ['msg'] = 'no_such_server';
				}
			}
		} else {
			$ret ['code'] = 2;
			$ret ['msg'] = 'lost_param';
		}
		echo json_encode ( $ret );
	}
	public function shutDownNotice() {
		$ret = array (
				'code' => 0,
				'msg' => '' 
		);
		if (isset ( $_GET ['sid'] ) && isset ( $_GET ['msg'] )) {
			$servers = $this->get_server_config ();
			$sids = explode ( ',', $_GET ['sid'] );
			foreach ( $sids as $value ) {
				if (isset ( $servers [$value] )) {
					$server = $servers [$value];
					$this->_sendShutDown ( $server ['GIP'], $server ['GPort'], urldecode ( $_GET ['msg'] ) );
					$ret ['msg'] = 'Đã gửi thành công';
					self::manage_log ();
				} else {
					$ret ['code'] = 1;
					$ret ['msg'] = 'no_such_server';
				}
			}
		} else {
			$ret ['code'] = 2;
			$ret ['msg'] = 'lost_param';
		}
		echo json_encode ( $ret );
	}
	public function updateAll() {
		if (isset ( $_GET ['sid'] )) {
			$servers = $this->get_server_config ();
			if (isset ( $servers [$_GET ['sid']] )) {
				$server = $servers [$_GET ['sid']];
				if (isset ( $server ['GIP'] ) && isset ( $server ['GPort'] )) {
					$this->reload ( $server ['GIP'], $server ['GPort'], 1, '' );
					self::manage_log ();
				}
			}
		}
	}
	
	// reload
	private function reload($ip, $port, $flag, $file) {
		$pkfmt = 'IISSIIICa32';
		$errno = 0;
		$errstr = '';
		$timeout = 1;
		$dt = pack ( $pkfmt, 55, 0xF1E2D3C4, 0x01, 0xDDD5, 0, 0, 0, $flag, $file );
		$sock = fsockopen ( $ip, ( int ) $port, $errno, $errstr, $timeout );
		if ($sock) {
			fwrite ( $sock, $dt, 55 );
		}
	}
	
	// _sendShutDown
	private function _sendShutDown($ip, $port, $msg) {
		$pkfmt = 'IISSIIISa128';
		$errno = 0;
		$errstr = '';
		$timeout = 1;
		$dt = pack ( $pkfmt, 154, 0xF1E2D3C4, 0x01, 0xDDF8, 0, 0, 0, strlen ( $msg ), $msg );
		$sock = fsockopen ( $ip, ( int ) $port, $errno, $errstr, $timeout );
		if ($sock) {
			fwrite ( $sock, $dt, 154 );
		}
	}
	// _sendNotice
	private function _sendNotice($ip, $port, $msg) {
		$pkfmt = 'IISSIIISa128';
		$errno = 0;
		$errstr = '';
		$timeout = 1;
		$dt = pack ( $pkfmt, 154, 0xF1E2D3C4, 0x01, 0xDDF7, 0, 0, 0, strlen ( $msg ), $msg );
		$sock = fsockopen ( $ip, ( int ) $port, $errno, $errstr, $timeout );
		if ($sock) {
			fwrite ( $sock, $dt, 154 );
		}
	}
	
	// _kickAll
	private function _kickAll($ip, $port) {
		$pkfmt = 'IISSIII';
		$errno = 0;
		$errstr = '';
		$timeout = 1;
		$dt = pack ( $pkfmt, 24, 0xF1E2D3C4, 0x01, 0xDDF9, 0, 0, 0 );
		$sock = fsockopen ( $ip, ( int ) $port, $errno, $errstr, $timeout );
		if ($sock) {
			fwrite ( $sock, $dt, 24 );
		}
	}
}
