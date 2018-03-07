<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
ini_set('html_errors','1');
error_reporting(E_ALL);

class dny_mail extends admin {
        /**
     * @var Redis
     */
    private $_redis;
    
     /**
     * @var model
     */
    private $_channel;
     /**
     * @var model
     */
    private $_gift;
     /**
     * @var model
     */
    private $_item;
    public function __construct() {
        $this->_channel=pc_base::load_model('channel_model');
        $this->_gift=pc_base::load_model('gift_model');
        $this->_item=pc_base::load_model('item_model');
    }   
    
    public function  dny_compensate()
    {
        $servers=$this->get_server_config();
        $keys=$this->getRedisKeys(50);
        $config=$this->get_config();
         $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        $mails=$redis->hgetall('GlobalRewardEmail');
        $lang_config=$this->getlanguage();
        $emailgm=simplexml_load_file(PHPCMS_PATH.'/statics/config/EmailGM.xml');
        foreach($emailgm->row as $k=>$v){
            $email[(string)$v['ID']]=(string)$v['Titile'];
            
        }
        include $this->admin_tpl('dny_mail','gm');
    }
    
    public function  mail_content()
    {
        $post=$_POST['content'];
        $post=str_split($post,5);
        $post=$post[1];
        $lang_config=$this->getlanguage();
        $emailgm=simplexml_load_file(PHPCMS_PATH.'/statics/config/EmailGM.xml');
        foreach($emailgm->row as $k=>$v){
            $email[(string)$v['ID']]=(string)$v['Content'];
            $email[(string)$v['EMailID']]=(string)$v['Content'];
        }
        $ret['content']=$lang_config[$email[$post]];
//         $ret['EMailID']=$_POST['content'];
//         $ret['ID']=$email[1];
        echo json_encode($ret);
    }
    
    public function mails()
    {
        $servers=$this->get_server_config();
        $server=$this->getRedisConfig();
        $config=$this->get_config();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        $mails=$redis->hgetall('GlobalRewardEmail');
        $ufmt='Iid/Iminlv/Imaxlv/Istart/Iend/Irstart/Irend/Isvip/Ievip/a24name/a32title/a512msg';
        $gMails=array();
        foreach ($mails as $k=>$v)
        {
            $mail=unpack($ufmt, $v);
            $len=unpack('Iilen', substr($v, 604));
            $str=substr($v,608);
            $items=array();
            for($i=0;$i<$len['ilen'];$i++)
            {
                $dt=unpack('Iid/Icount', $str);
                $str=substr($str,8);
                $items[]=$dt;
            }
            $mail['items']=$items;
            $gMails[$k]=$mail;
//            break;
        }
        include $this->admin_tpl('mails','gm');
    }
    
    public function remove()
    {
        $ret=array('code'=>0,'msg'=>'ok');
        if(isset($_POST['sid'])&&isset($_POST['mid']))
        {
            $servers=$this->get_server_config();
            if(isset($servers[$_POST['sid']]))
            {
                $server=$servers[$_POST['sid']];
                $this->__remove_mail($server['GIP'], $server['GPort'], $_POST['mid']);
            }else{
                $ret['code']=2;
                $ret['msg']='no_such_server';
            }
        }else{
            $ret['code']=1;
            $ret['msg']='lost_param';
        }
        echo json_encode($ret);
    }
    
    public function sendUsersMail()
    {
        $ret=array('code'=>0,'msg'=>'');
        if(isset($_REQUEST['title'])
        ){
            $servers=$this->get_server_config();
            if(isset($servers[$_REQUEST['s']]))
            {
              
                $server=$servers[$_REQUEST['s']];
                $this->__sendPlayersMail($server['GIP'],$server['GPort'],$_REQUEST['sender'],$_REQUEST['title'],$_REQUEST['content'],$_REQUEST['uids'],$_REQUEST['items']);
                $ret['msg']='success';
                self::manage_log();
            }
        }else{
            $ret['code']=1;
            $ret['msg']='lost_param';
        }
        echo json_encode($ret);
    }
    
    public function sendAllUsersMail()
    {
        $ret=array('code'=>0,'msg'=>'');
        
        if(isset($_REQUEST['s'])&&isset($_REQUEST['st'])&&isset($_REQUEST['sender'])&&
        isset($_REQUEST['items'])&&isset($_REQUEST['ed'])&&
        isset($_REQUEST['minlv'])&&isset($_REQUEST['maxlv'])&&
        isset($_REQUEST['content'])&&isset($_REQUEST['title'])&&
        isset($_REQUEST['rstart'])&&isset($_REQUEST['rend'])&&
        isset($_REQUEST['sviplv'])&&isset($_REQUEST['eviplv'])
        )
        {
                $servers=$this->get_server_config();
                if(isset($servers[$_REQUEST['s']]))
                {
                  
                    $server=$servers[$_REQUEST['s']];
                    $this->__sendAllPlayersMail($server['GIP'],$server['GPort'],$_REQUEST['sender'],$_REQUEST['title'],
                    $_REQUEST['content'],$_REQUEST['items'],$_REQUEST['st'],$_REQUEST['ed'],$_REQUEST['minlv'],$_REQUEST['maxlv'],
                    $_REQUEST['rstart'],$_REQUEST['rend'],$_REQUEST['sviplv'],$_REQUEST['eviplv']
                    );
                    $ret['msg']='success';
                    self::manage_log();
                }
        }else{
            $ret['code']=1;
            $ret['msg']='lost_param';
        }
        echo json_encode($ret);
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
             $keys=array_slice($keys,0,50);
             
        }
        return $keys;
    }
    
    private function __remove_mail($ip,$port,$mid)
    {
       $pkfmt='IISSIII';
        $errno=0;
        $errstr='';
        $timeout=1;
        $dt=pack($pkfmt,24,0xF1E2D3C4,0x01,0xDDFF,0,$mid,0);
        $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
        if($sock)
        {
            fwrite($sock, $dt,24);
        }
    }
    
    private function __sendPlayersMail($ip,$port,$sender,$title,$msg,$uids,$items)
    {
         $pkfmt='IISSIIIa24a32a512';
         $str=pack('S',count($uids));
         $len=2;
         foreach ($uids as $value) {
             $str.=pack('II',$value,0);
             $len+=8;
         }
         if(is_array($items))
         {
             $str.=pack('S',count($items));
             $len+=2;
             foreach ($items as $k=>$v) {
                 $idc=explode(':', $v);
                 $str.=pack('II',$idc[0],$idc[1]);
                 $len+=8;
             }
         }else{
             $str.=pack('S',0);
             $len+=2;
         }
         $errno=0;
         $errstr='';
         $timeout=5;
         $len+=24+24+32+512;
          $dt=pack($pkfmt,$len,0xF1E2D3C4,0x01,0xDDFB,0,0,0,$sender,$title,$msg);
         $dt.=$str;
         $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
         if($sock)
         {
             fwrite($sock, $dt,$len);
         }
    }
    private function __sendAllPlayersMail($ip,$port,$sender,$title,$msg,$items,$st,$ed,$minlv,$maxlv,$rstart,$rend,$sviplv,$eviplv)
    {
         $pkfmt='IISSIIIIIIIIIIIa24a32a512';
         $str='';
         if(is_array($items))
         {
             $str.=pack('S',count($items));
             $len+=2;
             foreach ($items as $k=>$v) {
                 $idc=explode(':', $v);
                 $str.=pack('II',$idc[0],$idc[1]);
                 $len+=8;
             }
         }else{
             $str.=pack('S',0);
             $len+=2;
         }
         $st=strtotime($st);
         $ed=strtotime($ed);
         $rstart=strtotime($rstart);
         $rend=strtotime($rend);
         $errno=0;
         $errstr='';
         $timeout=5;
         $len+=56+24+32+512;
          $dt=pack($pkfmt,$len,0xF1E2D3C4,0x01,0xDDFA,0,0,0,$minlv,$maxlv,$st,$ed,$rstart,$rend,$sviplv,$eviplv,$sender,$title,$msg);
         $dt.=$str;
         $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
         if($sock)
         {
             fwrite($sock, $dt,$len);
         }
    }
    
    
}