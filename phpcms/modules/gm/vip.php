<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class vip extends admin {
    /**
     * @var model
     */
    private $_db;
    
	public function __construct() {
	   $this->_db=pc_base::load_model('errors_model');
	   $this->_dummy=pc_base::load_model('dummy_model');
	}
    public function index()
    {
        $servers=$this->get_server_config();
        $keys=$this->getRedisKeys(50);
        $pays=$this->_getPayConfig();
        include $this->admin_tpl('vip','gm');
    }
    
    public function buy()
    {
        $ret=array('ret'=>0,'msg'=>'购买成功');
    	$username=param::get_cookie('admin_username');
    	$email=param::get_cookie('admin_email');
    	$pays=$this->_getPayConfig();
    	$str=array('ip'=>$_SERVER['REMOTE_ADDR'],'username'=>$username,'email'=>$email,'sid'=>$_REQUEST['sid'],'uid'=>$_REQUEST['uid'],'itemID'=>$_REQUEST['itemid'],'time'=>date('Y-m-d H:i:s'),'gold'=>$pays[$_REQUEST['itemid']]['Des']);
        if(isset($_REQUEST['sid'])&&isset($_REQUEST['uid'])&&isset($_REQUEST['itemid']))
        {
           $servers=$this->get_server_config();
           $server=$servers[$_REQUEST['sid']];
           if(isset($server['GIP'])&&isset($server['GPort']))
           {
                  $this->_sendBuyItem($server['GIP'],$server['GPort'],$_REQUEST['uid'],$_REQUEST['itemid']);
		  $this->_dummy->insert($str);
                  self::manage_log();
           }else{
               $ret['msg']='服务器配置不存在';
           }
        }else{
            $ret['msg']='缺少参数';
        }
        echo json_encode($ret);
    }

    public function dummy(){
      $post=$_POST;
      $ret=array('ret'=>0,'msg'=>''); 
      foreach($post as $k=>$v){trim($v);}
      if(!empty($post['uid'])){$str.="`uid`='".$post['uid']."'";}
      if(!empty($post['usern'])){
        if(empty($post['uid'])){
            $str.="`username`='".$post['usern']."'";
        }else{
            $str.=" and `username`='".$post['usern']."'";
        }
      }
      if(!empty($post['st'])){ 
        if(!empty($post['uid']) || !empty($post['usern'])){
            $str.=" and `time` like '%".$post['st']."%'";
        }else{
            $str.="`time` like '%".$post['st']."%'";
        }
      }
      $order=$this->_dummy->select($str,'*','','id DESC'); 
      if(empty($order)){
              $ret=array('ret'=>1,'msg'=>'查询为空');
      }else{
              $ret['msg']=$order;
      }
      echo json_encode($ret);
    }
    
    protected function _getPayConfig ()
    {
        $xml=simplexml_load_file(CACHE_PATH."configs".DIRECTORY_SEPARATOR."Pay.xml");
        $ret = array();
        foreach ($xml->row as $v) {
            foreach ($v->attributes() as $k1 => $v1) {
                $arr[(string) $k1] = (string) $v1;
            }
            $ret[$arr['ID']] = $arr;
        }
        return $ret;
    }
    
    private function getRedisKeys($count)
    {
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        $keys=$redis->keys('RoleInfo:*');
        sort($keys);
        $config=$this->get_config();
        if(count($keys)>$count)
        {
             $keys=array_slice($keys,0,$count);
        }
        return $keys;
    }
    
    //_sendBuyItem
    private function _sendBuyItem($ip,$port,$uid,$itemid)
    {
        $pkfmt='IISSIII';
        $errno=0;
        $errstr='';
        $timeout=5;
        $dt=pack($pkfmt,24,0xF1E2D3C4,0x01,0xDDFD,$uid,$itemid,0);
        $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
        if($sock)
        {
             fwrite($sock, $dt,24);
        }
    }
}
?>
