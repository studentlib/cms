<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
ini_set('html_errors','1');
error_reporting(E_ALL);
class black extends admin {
    /**
     * @var Redis
     */
    private $_redis;
    public function __construct() {
    }   
    
    public function  index()
    {
        $files=glob("F:/work/sanguo/src/config/*");
        $configs=array();
        foreach ($files as $v) 
        {
            $configs[]=pathinfo($v, PATHINFO_FILENAME);    
        }
        $servers=$this->get_server_config();
        $config=$this->get_config();
        $keys=$this->getRedisKeys(500);
        $black=$this->getBlackList(100);
        $ban=$this->getBanList(100);
        include $this->admin_tpl('black','gm');
    }
    
    public function forbbiden()
    {
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex']);
        $ret=array('error'=>0,'code'=>0,'msg'=>'');
        if(!empty($_REQUEST['uid'])&&!empty($_REQUEST['sid'])&&isset($_REQUEST['ch']))
        {
            $servers=$this->get_server_config();
            $server=$servers[$_REQUEST['sid']];
            if(isset($server['GIP'])&&isset($server['GPort']))
            {
                $this->_forbidden($server['GIP'], $server['GPort'], $_REQUEST['uid'], $_REQUEST['ch']);
                self::manage_log();
            }
        }else{
            $ret['error']=1;
            $ret['msg']='参数错误';
        }
        echo json_encode($ret);
    }
    public function ban()
    {
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex']);
        $ret=array('error'=>0,'code'=>0,'msg'=>'');
        if(!empty($_REQUEST['uid'])&&!empty($_REQUEST['sid'])&&isset($_REQUEST['ch'])&&isset($_REQUEST['time']))
        {
            $servers=$this->get_server_config();
            $server=$servers[$_REQUEST['sid']];
            if(isset($server['GIP'])&&isset($server['GPort']))
            {
                $this->_ban($server['GIP'], $server['GPort'], $_REQUEST['uid'], time(),$_REQUEST['time']);
                self::manage_log();
            }
        }else{
            $ret['error']=1;
            $ret['msg']='参数错误';
        }
        echo json_encode($ret);
    }
    
    public function delban()
    {
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex']);
        $ret=array('error'=>0,'code'=>0,'msg'=>'');
        if(!empty($_REQUEST['uid'])&&!empty($_REQUEST['sid'])&&isset($_REQUEST['ch'])&&isset($_REQUEST['time']))
        {
            $servers=$this->get_server_config();
            $server=$servers[$_REQUEST['sid']];
            if(isset($server['GIP'])&&isset($server['GPort']))
            {
		file_put_contents('/data/www/sg/po/phpcms/modules/gm'," content: \n".print_r($_REQUEST,1) ,FILE_APPEND);
                $this->_ban($server['GIP'], $server['GPort'], $_REQUEST['uid'], 1,0);
                self::manage_log();
		$ret['msg']='success';
            }
        }else{
            $ret['error']=1;
            $ret['msg']='参数错误';
        }
        echo json_encode($ret);
    }
    
    private function getBlackList($count)
    {
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        $keys=$redis->hgetall('Forbidden');
        if(count($keys)>$count)
        {
             $keys=array_slice($keys,0,$count);
        }
      
        $ret=array();
        foreach ($keys as $k=>$v) {
            $lret=unpack('Iuid/Ich', $k.$v);
            $ret[$lret['uid']]=$lret['ch'];
        }
        return $ret;
    }
    private function getBanList($count)
    {
        $server=$this->getRedisConfig();
         $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        $keys=$redis->hget('GlobalBanList',0);
        $ret=array();
         if(strlen($keys)>=4)
         {
             $sz=unpack('Isz', $keys);
             $keys=substr($keys, 4);
             for($i=0;$i<$sz['sz'];++$i)
             {
                 $data=unpack('Iuid/Ist/Ied/Ireason/Iparam',$keys);
                 
                 $ret[$data['uid']]=$data;
                 $keys=substr($keys, 20);
             }
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
    
    
    //forbidden
    private function _forbidden($ip,$port,$uid,$ch)
    {
        $pkfmt='IISSIII';
        $errno=0;
        $errstr='';
        $timeout=5;
        $dt=pack($pkfmt,24,0xF1E2D3C4,0x01,0xDDF5,$uid,$ch,0);
        $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
        if($sock)
        {
            fwrite($sock, $dt,24);
        }
    }
    //_ban
    private function _ban($ip,$port,$uid,$ch,$time)
    {
        $pkfmt='IISSIII';
        $errno=0;
        $errstr='';
        $timeout=5;
        $dt=pack($pkfmt,24,0xF1E2D3C4,0x01,0xDDFC,$uid,$ch,$ch+$time);
        $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
        if($sock)
        {
            fwrite($sock, $dt,24);
        }
    }
    
}
