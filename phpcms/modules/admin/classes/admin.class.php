<?php
defined('IN_PHPCMS') or exit('No permission resources.');
$session_storage = 'session_'.pc_base::load_config('system','session_storage');
pc_base::load_sys_class($session_storage);
if(param::get_cookie('sys_lang')) {
	define('SYS_STYLE',param::get_cookie('sys_lang'));
} else {
	define('SYS_STYLE','zh-cn');
}
//定义在后台
define('IN_ADMIN',true);
class admin {
	public $userid;
	public $username;
	
	public function __construct() {
		self::check_admin();
		self::check_priv();
		pc_base::load_app_func('global','admin');
		if (!module_exists(ROUTE_M)) showmessage(L('module_not_exists'));
		self::check_ip();
		self::lock_screen();
		self::check_hash();
		if(pc_base::load_config('system','admin_url') && $_SERVER["HTTP_HOST"]!= pc_base::load_config('system','admin_url')) {
			Header("http/1.1 403 Forbidden");
			exit('No permission resources.');
		}
	}
	
	/**
	 * 判断用户是否已经登陆
	 */
	final public function check_admin() {
		if(ROUTE_M =='admin' && ROUTE_C =='index' && in_array(ROUTE_A, array('login', 'public_card'))) {
			return true;
		} else {
			$userid = param::get_cookie('userid');
			if(!isset($_SESSION['userid']) || !isset($_SESSION['roleid']) || !$_SESSION['userid'] || !$_SESSION['roleid'] || $userid != $_SESSION['userid']) showmessage(L('admin_login'),'?m=admin&c=index&a=login');
		}
	}

	/**
	 * 加载后台模板
	 * @param string $file 文件名
	 * @param string $m 模型名
	 */
	final public static function admin_tpl($file, $m = '') {
		$m = empty($m) ? ROUTE_M : $m;
		if(empty($m)) return false;
		return PC_PATH.'modules'.DIRECTORY_SEPARATOR.$m.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$file.'.tpl.php';
	}
	
	/**
	 * 按父ID查找菜单子项
	 * @param integer $parentid   父菜单ID  
	 * @param integer $with_self  是否包括他自己
	 */
	final public static function admin_menu($parentid, $with_self = 0) {
		$parentid = intval($parentid);
		$menudb = pc_base::load_model('menu_model');
		$site_model = param::get_cookie('site_model');
		$where = array('parentid'=>$parentid,'display'=>1);
		if ($site_model && $parentid) {
			$where[$site_model] = 1;
 		}
		$result =$menudb->select($where,'*',1000,'listorder ASC');
		if($with_self) {
			$result2[] = $menudb->get_one(array('id'=>$parentid));
			$result = array_merge($result2,$result);
		}
		//权限检查
		if($_SESSION['roleid'] == 1) return $result;
		$array = array();
		$privdb = pc_base::load_model('admin_role_priv_model');
		$siteid = param::get_cookie('siteid');
		foreach($result as $v) {
			$action = $v['a'];
			if(preg_match('/^public_/',$action)) {
				$array[] = $v;
			} else {
				if(preg_match('/^ajax_([a-z]+)_/',$action,$_match)) $action = $_match[1];
				$r = $privdb->get_one(array('m'=>$v['m'],'c'=>$v['c'],'a'=>$action,'roleid'=>$_SESSION['roleid'],'siteid'=>$siteid));
				if($r) $array[] = $v;
			}
		}
		return $array;
	}
	/**
	 * 获取菜单 头部菜单导航
	 * 
	 * @param $parentid 菜单id
	 */
	final public static function submenu($parentid = '', $big_menu = false) {
		if(empty($parentid)) {
			$menudb = pc_base::load_model('menu_model');
			$r = $menudb->get_one(array('m'=>ROUTE_M,'c'=>ROUTE_C,'a'=>ROUTE_A));
			$parentid = $_GET['menuid'] = $r['id'];
		}
		$array = self::admin_menu($parentid,1);
		
		$numbers = count($array);
		if($numbers==1 && !$big_menu) return '';
		$string = '';
		$pc_hash = $_SESSION['pc_hash'];
		foreach($array as $_value) {
			if (!isset($_GET['s'])) {
				$classname = ROUTE_M == $_value['m'] && ROUTE_C == $_value['c'] && ROUTE_A == $_value['a'] ? 'class="on"' : '';
			} else {
				$_s = !empty($_value['data']) ? str_replace('=', '', strstr($_value['data'], '=')) : '';
				$classname = ROUTE_M == $_value['m'] && ROUTE_C == $_value['c'] && ROUTE_A == $_value['a'] && $_GET['s'] == $_s ? 'class="on"' : '';
			}
			if($_value['parentid'] == 0 || $_value['m']=='') continue;
			if($classname) {
				$string .= "<a href='javascript:;' $classname><em>".L($_value['name'])."</em></a><span>|</span>";
			} else {
				$string .= "<a href='?m=".$_value['m']."&c=".$_value['c']."&a=".$_value['a']."&menuid=$parentid&pc_hash=$pc_hash".'&'.$_value['data']."' $classname><em>".L($_value['name'])."</em></a><span>|</span>";
			}
		}
		$string = substr($string,0,-14);
		return $string;
	}
	/**
	 * 当前位置
	 * 
	 * @param $id 菜单id
	 */
	final public static function current_pos($id) {
		$menudb = pc_base::load_model('menu_model');
		$r =$menudb->get_one(array('id'=>$id),'id,name,parentid');
		$str = '';
		if($r['parentid']) {
			$str = self::current_pos($r['parentid']);
		}
		return $str.L($r['name']).' > ';
	}
	
	/**
	 * 获取当前的站点ID
	 */
	final public static function get_siteid() {
		return get_siteid();
	}
	
	/**
	 * 获取当前站点信息
	 * @param integer $siteid 站点ID号，为空时取当前站点的信息
	 * @return array
	 */
	final public static function get_siteinfo($siteid = '') {
		if ($siteid == '') $siteid = self::get_siteid();
		if (empty($siteid)) return false;
		$sites = pc_base::load_app_class('sites', 'admin');
		return $sites->get_by_id($siteid);
	}
	
	final public static function return_siteid() {
		$sites = pc_base::load_app_class('sites', 'admin');
		$siteid = explode(',',$sites->get_role_siteid($_SESSION['roleid']));
		return current($siteid);
	}
	/**
	 * 权限判断
	 */
	final public function check_priv() {
		if(ROUTE_M =='admin' && ROUTE_C =='index' && in_array(ROUTE_A, array('login', 'init', 'public_card'))) return true;
		if($_SESSION['roleid'] == 1) return true;
		$siteid = param::get_cookie('siteid');
		$action = ROUTE_A;
		$privdb = pc_base::load_model('admin_role_priv_model');
		if(preg_match('/^public_/',ROUTE_A)) return true;
		if(preg_match('/^ajax_([a-z]+)_/',ROUTE_A,$_match)) {
			$action = $_match[1];
		}
		$r =$privdb->get_one(array('m'=>ROUTE_M,'c'=>ROUTE_C,'a'=>$action,'roleid'=>$_SESSION['roleid'],'siteid'=>$siteid));
		if(!$r) showmessage('您没有权限操作该项','blank');
	}

	/**
	 * 
	 * 记录日志 
	 */
	final protected  function manage_log($user='') {
		//判断是否记录
		$setconfig = pc_base::load_config('system');
		extract($setconfig);
 		if($admin_log==1){
 			$action = ROUTE_A;
 			if($action == '' || strchr($action,'public') || $action == 'init' || $action=='public_current_pos') {
				return false;
			}else {
			    $arr=debug_backtrace();
			    $file='';
			    if(isset($arr[1]))
			    {
			        if(isset($arr[1]['class'])&&isset($arr[1]['function']))
			        {
    			        $file=$arr[1]['class'].":".$arr[1]['function'];
			        }
			    }
				$ip = ip();
				$log = pc_base::load_model('log_model');
				$username = param::get_cookie('admin_username');
				$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : '';
				if($user)
				{
				    $username=$user;
				}
				$time = date('Y-m-d H-i-s',SYS_TIME);
				$url = '?m='.ROUTE_M.'&c='.ROUTE_C.'&a='.ROUTE_A;
				$log->insert(array('module'=>ROUTE_M,'file'=>$file,'username'=>$username,'data'=>json_encode($_REQUEST,JSON_UNESCAPED_UNICODE),'userid'=>$userid,'action'=>$action, 'querystring'=>$url,'time'=>$time,'ip'=>$ip));
			}
	  	}
	}
	
	/**
	 * 
	 * 后台IP禁止判断 ...
	 */
	final private function check_ip(){
		$this->ipbanned = pc_base::load_model('ipbanned_model');
		$this->ipbanned->check_ip();
 	}
 	/**
 	 * 检查锁屏状态
 	 */
	final private function lock_screen() {
		if(isset($_SESSION['lock_screen']) && $_SESSION['lock_screen']==1) {
			if(preg_match('/^public_/', ROUTE_A) || (ROUTE_M == 'content' && ROUTE_C == 'create_html') || (ROUTE_M == 'release') || (ROUTE_A == 'login') || (ROUTE_M == 'search' && ROUTE_C == 'search_admin' && ROUTE_A=='createindex')) return true;
			showmessage(L('admin_login'),'?m=admin&c=index&a=login');
		}
	}

	/**
 	 * 检查hash值，验证用户数据安全性
 	 */
	final private function check_hash() {
		if(preg_match('/^public_/', ROUTE_A) || ROUTE_M =='admin' && ROUTE_C =='index' || in_array(ROUTE_A, array('login'))) {
			return true;
		}
		if(isset($_GET['pc_hash']) && $_SESSION['pc_hash'] != '' && ($_SESSION['pc_hash'] == $_GET['pc_hash'])) {
			return true;
		} elseif(isset($_POST['pc_hash']) && $_SESSION['pc_hash'] != '' && ($_SESSION['pc_hash'] == $_POST['pc_hash'])) {
			return true;
		} else {
			showmessage(L('hash_check_false'),HTTP_REFERER);
		}
	}

	/**
	 * 后台信息列表模板
	 * @param string $id 被选中的模板名称
	 * @param string $str form表单中的属性名
	 */
	final public function admin_list_template($id = '', $str = '') {
		$templatedir = PC_PATH.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;
		$pre = 'content_list';
		$templates = glob($templatedir.$pre.'*.tpl.php');
		if(empty($templates)) return false;
		$files = @array_map('basename', $templates);
		$templates = array();
		if(is_array($files)) {
			foreach($files as $file) {
				$key = substr($file, 0, -8);
				$templates[$key] = $file;
			}
		}
		ksort($templates);
		return form::select($templates, $id, $str,L('please_select'));
	}
	
    protected  function getRedis($ip,$index,$port=6379)
    {
        $redis=new Redis();
        try {
            $redis->connect($ip,$port);
            $redis->select($index);
        } catch (Exception $e) {
            //exit($e);
	    echo 'redis已关闭';
        }

        return $redis;
    }
    
    protected  function get_config()
    {
        $items=simplexml_load_file(PHPCMS_PATH.'/statics/config/Item.xml');
        $lang=simplexml_load_file(PHPCMS_PATH.'/statics/config/LanguageCN.xml');
        $config=array();
        $lang_config=array();
        foreach ($lang as $vl) {
            $lang_config[(string)$vl['Key']]=(string)$vl['Value'];
        }
        foreach ($items as $k=>$v) 
        {
            if(isset($v['ItemID'])&&isset($v['ItemName']))
            {
                $config[(string)$v['ItemID']]=array('id'=>(string)$v['ItemID'],'name'=>$lang_config[(string)$v['ItemName']],'type'=>(string)$v['ItemType']);
            }
        }
        return $config;
    }

    protected  function get_config_item()
    {
        $items=simplexml_load_file(PHPCMS_PATH.'/statics/config/Item.xml');
        $lang=simplexml_load_file(PHPCMS_PATH.'/statics/config/LanguageCN.xml');
        $config=array();
        $lang_config=array();
        foreach ($lang as $vl) {
            $lang_config[(string)$vl['Key']]=(string)$vl['Value'];
        }
        foreach ($items as $k=>$v)
        {
            if(isset($v['ItemID'])&&isset($v['ItemName']))
            {
            $config[$lang_config[(string)$v['ItemName']]]=array('id'=>(string)$v['ItemID'],'name'=>$lang_config[(string)$v['ItemName']],'type'=>(string)$v['ItemType']);
            }
        }
        return $config;
    }
    
    protected  function getlanguage()
    {
        $lang=simplexml_load_file(PHPCMS_PATH.'/statics/config/LanguageCN.xml');
        $lang_config=array();
        foreach ($lang as $vl) {
            $lang_config[(string)$vl['Key']]=(string)$vl['Value'];
        }
        return $lang_config;
    }
    
    protected function getRedisConfig()
    {
        //$_SESSION['sid']=isset($_SESSION['sid'])?$_SESSION['sid']:1;
        $servers=$this->get_server_config();
        $server=array();
        if(isset($servers[$_SESSION['sid']]))
        {
            $server=$servers[$_SESSION['sid']];
        }else if(count($servers)){
            $skeys=array_keys($servers);
            $server=$servers[$skeys[0]];
        }
        if(!count($server))
        {
            exit('NO ACCESS');
        }
        return $server;
    }
    
    protected  function get_server_config()
    {
         $opts = array(
        'http'=>array(
        'method'=>"GET",
        'timeout'=>10,
        )
        );
        $option=stream_context_create($opts);
        $system=pc_base::load_config('system');
        $file = file_get_contents($system['server_list'],  null, $option);
        $servers=simplexml_load_string($file);
        $admin_group=array(1,8,16,17);
        $ret=array();
        foreach ($servers->row as $v)
        {
             foreach ($v->attributes() as $k1=>$v1) 
             {
                $arr[(string)$k1]=(string)$v1;
             }
             
             if(!in_array($_SESSION['roleid'],$admin_group)&&isset($arr['Admin'])&&$arr['Admin']==='yes')
             {
                continue;    
             }
              $ret[$arr['ServerType']]=$arr;
             if(!isset($_SESSION['sid']))
             {
                 $_SESSION['sid']=$arr['ServerType'];
             }
        }
        return $ret;
    }
    
    protected function getPushRedis()
    {
        $push=pc_base::load_config('push','redis');
        return $this->getRedis($push['host'],$push['index'],$push['port']);
    }
    
    public static function times33 ($string)
    {
        $string = strval($string);
        $len = strlen($string);
        $code = 0;
        for ($i = 0; $i < $len; $i ++)
        {
            $code = (int) (($code << 5) + $code) + ord($string[$i]) & 0x7fffffff;
        }
        return $code;
    }
}
