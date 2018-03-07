<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
ini_set('html_errors','1');
class index extends  admin
{
    private $charge_params;
    private $query_params;
    private $skey='godlike';
    public function __construct ()
    {
        $this->need_params=array('roleid','ticket','count','area');
        $this->query_params=array('roleid','ticket','orderid','count');
    }
    
    public function init ()
    {
        
    }
    
    
    private function _check_param($params)
    {
//        $_REQUEST['roleid']=100013034;
//        $_REQUEST['area']=1;
//        $_REQUEST['ticket']=md5($_REQUEST['roleid'].$this->skey);
//        $_REQUEST['count']=1;
//        $_REQUEST['orderid']=uniqid();
        $lost=array();
        $params=array();
        foreach ($params as $v)
        {
            if(!isset($_REQUEST[$v]))
            {
                $lost[]=$v;
            }else{
                $params[$v]=$_REQUEST[$v];
            }
        }
        if(count($lost))
        {
           echo 'LOST PARAMS [ '.join(', ', $lost).' ]';
           return 1;
        }
        $cticket=md5($_REQUEST['roleid'].$this->skey);
        if($cticket!= $_REQUEST['ticket'])
        {
            echo 'WRONG TICKET';
            return 1; 
        }
        return 0;
    }
    
    public function index ()
    {
        if($this->_check_param($this->charge_params))
        {
            return ;
        }
        $_REQUEST['orderid']=uniqid();
         extract($_REQUEST);
        include $this->admin_tpl("index",'charge');
    }
    
    public function query ()
    {
        if($this->_check_param($this->query_params))
        {
            return ;
        }
         extract($_REQUEST);
         $servers=$this->get_server_config();
         if(isset($servers[$area]))
         {
             $server=$servers[$area];
             $redis=$this->getRedis($server['RIP'],$server['RIndex']);
             $result=$redis->hget('orders',$orderid);
           
         }
           $ret=array('ret'=>0,'msg'=>'');
         if($result)
         {
              $ret['ret']=0;
         }else{
             $ret['ret']=1;
             $ret['msg']='order_not_exists';
         }
         echo pack('Ia32',$ret['ret'],$ret['msg']);
    }
    
    public function charge ()
    {
        if($this->_check_param($this->charge_params))
        {
            return ;
        }
        $servers=$this->get_server_config();
         extract($_REQUEST);
         if(isset($servers[$area]))
         {
           
            $server=$servers[$area];
            $redis=$this->getRedis($server['RIP'],$server['RIndex']);
            $redis->hset('orders',$orderid,array($roleid, $orderid, $area, $count));
//            echo $server['CIP'], $server['CPort'], $roleid, $orderid, $area, $count;
            $this->sendtocharge($server['CIP'], $server['CPort'], $roleid, $orderid, $area, $count);
         }
    }
    
    public function success ()
    {
        extract($_REQUEST);
        include $this->admin_tpl("success",'charge');
    }
    
    //modify attr
    private function sendtocharge($ip,$port,$uid,$orderid,$area,$count)
    {
        $pkfmt='IISSIIIa64III';
        $errno=0;
        $errstr='';
        $timeout=5;
        $dt=pack($pkfmt,100,0xF1E2D3C4,0,0xD801,0,0,0,$orderid,$uid,$area,$count);
        $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
        if($sock)
        {
            fwrite($sock, $dt,100);
        }
        sleep(1);
    }
}