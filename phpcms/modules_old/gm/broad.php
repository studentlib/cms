<?php
defined ( 'IN_PHPCMS' ) or exit ( 'No permission resources.' );
pc_base::load_app_class ( 'admin', 'admin', 0 );
pc_base::load_sys_class ( 'form', '', 0 );
ini_set ( 'html_errors', '1' );
error_reporting ( E_ALL );
class broad extends admin {
	public function __construct() {
	}
	public function index() {
		$broad = pc_base::load_config ( 'system', 'broad_url' );
		$files_name = pc_base::load_config ( 'notice', 'files_name' );
		$html = array_keys ( $files_name );
		$content = file_get_contents ( $broad . $html [0] );
		// var_dump($content);
		include $this->admin_tpl ( 'broad', 'gm' );
	}
	public function file_content() {
		$post = $_POST;
		$ret = array (
				'ret' => 1,
				'msg' => '',
				'content' => '' 
		);
		$broad = pc_base::load_config ( 'system', 'broad_url' );
		$ret ['content'] = file_get_contents ( $broad . $post ['file'] );
		if (empty ( $ret ['content'] )) {
			return array (
					'ret' => 0,
					'msg' => 'Nội dung trống' 
			);
		}
		echo json_encode ( $ret );
	}
	public function save() {
		$ret = array (
				'msg' => '' 
		);
		if (isset ( $_POST ['content'] )) {
			$broad = pc_base::load_config ( 'system', 'broad_url' );
			$ch = curl_init ();
			curl_setopt ( $ch, CURLOPT_URL, $broad . 'upBroad.php' );
			curl_setopt ( $ch, CURLOPT_POST, TRUE );
			curl_setopt ( $ch, CURLOPT_TIMEOUT, 60 );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, array (
					'content' => stripslashes ( $_POST ['content'] ),
					'file' => $_POST ['file'] 
			) );
			curl_exec ( $ch );
			curl_close ( $ch );
			$ret ['msg'] = 'Lưu thành công';
			self::manage_log ();
		} else {
			$ret ['msg'] = 'Thiếu thông số';
		}
		echo json_encode ( $ret );
	}
	public function update() {
		// if(isset($_POST['content']))
		// {
		// file_put_contents('gg.html', $_POST['content']);
		// }
	}
}
