<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
ini_set('html_errors','1');
error_reporting(E_ALL);
class gs extends admin {
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
        include $this->admin_tpl('gs','gm');
    }
    
    public function  update()
    {
        if(isset($_GET['sid'])&&isset($_GET['file']))
        {
            $servers=$this->get_server_config();
            if(isset($servers[$_GET['sid']]))
            {
                $server=$servers[$_GET['sid']];
                if(isset($server['GIP'])&&isset($server['GPort']))
                {
                    $this->reload($server['GIP'], $server['GPort'], 0, $_GET['file']);
                }
            }
        }
    }
    public function  updateAll()
    {
        if(isset($_GET['sid']))
        {
            $servers=$this->get_server_config();
            if(isset($servers[$_GET['sid']]))
            {
                $server=$servers[$_GET['sid']];
                if(isset($server['GIP'])&&isset($server['GPort']))
                {
                    $this->reload($server['GIP'], $server['GPort'], 1, '');
                }
            }
        }
    }
    
    //modify attr
    private function reload($ip,$port,$flag,$file)
    {
        $pkfmt='IISSIIICa32';
        $errno=0;
        $errstr='';
        $timeout=5;
        $dt=pack($pkfmt,55,0xF1E2D3C4,0x01,0xDDD5,0,0,0,$flag,$file);
        $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
        if($sock)
        {
            fwrite($sock, $dt,55);
        }
    }
    //modify attr
    private function kick_out($ip,$port,$uid)
    {
        $pkfmt='IISSIIIII';
        $errno=0;
        $errstr='';
        $timeout=5;
        $dt=pack($pkfmt,32,0xF1E2D3C4,0x01,0xDDDD,$uid,0,0,7788521,$uid);
        $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
        if($sock)
        {
            fwrite($sock, $dt,32);
        }
    }
    //modify attr
    private function modify_attr($ip,$port,$uid,$key,$value)
    {
        $pkfmt='IISSIII';
        $errno=0;
        $errstr='';
        $timeout=5;
        $dt=pack($pkfmt,24,0xF1E2D3C4,0x01,0xDDEE,$uid,$key,$value);
        $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
        if($sock)
        {
            fwrite($sock, $dt,24);
        }
    }
    
    //modify attr
    private function modify_item($ip,$port,$uid,$type,$tid,$count)
    {
        $pkfmt='IISSIIII';
        $errno=0;
        $errstr='';
        $timeout=5;
        $dt=pack($pkfmt,28,0xF1E2D3C4,0x01,0xDDEF,$uid,$type,$tid,$count);
        $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
        if($sock)
        {
            fwrite($sock, $dt,28);
        }
       
    }
    
    
}