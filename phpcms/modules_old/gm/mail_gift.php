<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
ini_set('html_errors','1');
error_reporting(E_ALL);

class mail_gift extends admin {
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
    
    public function  compensate()
    {
        $servers=$this->get_server_config();
        $keys=$this->getRedisKeys(50);
        $config=$this->get_config();
         $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        $mails=$redis->hgetall('GlobalRewardEmail');
       $ufmt='Iid/Iminlv/Imaxlv/Istart/Iend/a24name/a32title/a128msg';
#        print_r(json_encode($config));
//        $ret=array();
//        foreach ($config as $k=>$v) {
//            $ret[$v['id']]=array('type'=>$v['ItemType'],'name'=>$v['ItemName']);
//        }
//        print_r($mails);
//        foreach ($mails as $k=>$v)
//        {
//            $dt=unpack($ufmt, $v);
//            print_r($dt);
//            $len=unpack('I', substr($v, 204));
//            print_r($len);
//            $str=substr($v,208);
//            for($i=0;$i<$len[1];$i++)
//            {
//                $dt=unpack('Iid/Icount', $str);
//                print_r($dt);
//                $str=substr($str,8);
//            }
//        }
        include $this->admin_tpl('mail_gift','gm');
    }
   
    /**发多人邮件**/
    public function  many_com()
    {
        $servers=$this->get_server_config();
        $keys=$this->getRedisKeys(50);
        $config=$this->get_config();
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        $mails=$redis->hgetall('GlobalRewardEmail');
        $lang_config=$this->getlanguage();
        include $this->admin_tpl('many_gift','gm');
    }

    /**发多人邮件**/
    public function  many_mails()
    {
	//关掉浏览器，PHP脚本也可以继续执行.
	ignore_user_abort();
        // 通过set_time_limit(0)可以让程序无限制的执行下去
        set_time_limit(0);
        $ret=file_get_contents(PHPCMS_PATH.'uploadfile/code.txt');
//        $data=preg_replace('/\n|\r\n/','&',$ret);
	$data=preg_replace('/\D{1,}/','&',$ret);
        $data=explode('&',$data);
        $ret=array('code'=>0,'msg'=>'','test'=>$data);
        if(!file_exists(PHPCMS_PATH.'uploadfile/code.txt')){$ret['msg']='ID文件为空';exit(json_encode($ret));}
        if(empty($_REQUEST['title'])||strlen($_REQUEST['title'])>32){echo json_encode(array('code'=>101,msg=>'字节数'.strlen($_REQUEST['title']).',标题不能超过32个字节'));exit;}
        if(empty($_REQUEST['content'])||strlen($_REQUEST['content'])>512){echo json_encode(array('code'=>102,msg=>'字节数'.strlen($_REQUEST['content']).',内容不能超过512个字节'));exit;}
        if(isset($_REQUEST['s'])&&isset($_REQUEST['items'])&&isset($_REQUEST['content'])&&isset($_REQUEST['sender'])&&isset($_REQUEST['title']) )
        {
            $servers=$this->get_server_config();
            if(isset($servers[$_REQUEST['s']]))
            {
             $server=$servers[$_REQUEST['s']];
//                foreach($data as $k=>$v){
//                 usleep(1000);
//                 $sg=array();
//                 $sg[]=$v;
		$m=0;
		for($i=0;$i<count($data);$i++){
		   $uids[$m][$i]=$data[$i];
		   if($i%1000==0&&$i!=0){$m++;}
		   }
		foreach($uids as $k=>$v){
		   $sg=$v;
file_put_contents(PHPCMS_PATH.'uploadfile/mail_code.log',date('Y-m-d H:i:s',time()).'--uid:'.json_encode($sg).'--标题：'.$_REQUEST['title'].'--补偿物品：'.json_encode($_REQUEST['items']).PHP_EOL, FILE_APPEND);
                   $this->__sendPlayersMail($server['GIP'],$server['GPort'],$_REQUEST['sender'],$_REQUEST['title'],$_REQUEST['content'],$sg,$_REQUEST['items']);
                   self::manage_log();
		   usleep(1000);
		 }  
//        	}
                $ret['msg']='success';
//		rename(PHPCMS_PATH.'uploadfile/code.txt',PHPCMS_PATH.'uploadfile/'.date('Y-m-d H:i:s').'code.txt');
		unlink(PHPCMS_PATH.'uploadfile/code.txt');
            }
        }else{
            $ret['code']=1;
            $ret['msg']='lost_param';
        }
        echo json_encode($ret);
    }

    /**
     * 上传TXT文件
     * @param  [type] $_SESSION [description]
     * @return [type]           [description]
     */
    public function file_upload(){
//        if($_SESSION['roleid']!=1){exit('没有权限');}
        if($_FILES["upload"]["size"] < 1024000&&$_FILES["upload"]['type']='text/plain')
          {
              if ($_FILES["upload"]["error"] > 0)
                {
                echo "Return Code: " . $_FILES["upload"]["error"] . "<br />";
              }else{
               // echo "Upload: " . $_FILES["upload"]["name"] . "<br />";
               // echo "Type: " . $_FILES["upload"]["type"] . "<br />";
               // echo "Size: " . ($_FILES["upload"]["size"] / 1024) . " Kb<br />";
               // echo "Temp file: " . $_FILES["upload"]["tmp_name"] . "<br />";

                if (file_exists(PHPCMS_PATH."uploadfile/code.txt"))
                  {
		  unlink(PHPCMS_PATH.'uploadfile/code.txt');
		  move_uploaded_file($_FILES["upload"]["tmp_name"],"uploadfile/code.txt");
		  echo '文件存在,已覆盖';
                  }
                else
                  {
                 // move_uploaded_file($_FILES["upload"]["tmp_name"],"uploadfile/" . $_FILES["upload"]["name"]);
		  move_uploaded_file($_FILES["upload"]["tmp_name"],"uploadfile/code.txt");
                  echo "上传成功";
                  }
                }
          }else{
          echo "文件超过1000kb或格式不正确";
          }
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
    
    public function string(){
        $post = $_POST['string'];
        $str = str_replace('u', '\u', $post);
        $json = "{\"str\":\"$str\"}";
        //$json = str_replace('str:');
        $string=json_decode($json, true);
        echo json_encode($string);
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
        if(empty($_REQUEST['title'])||strlen($_REQUEST['title'])>32){echo json_encode(array('code'=>101,msg=>'字节数'.strlen($_REQUEST['title']).',标题不能超过32个字节'));exit;}
        if(empty($_REQUEST['content'])||strlen($_REQUEST['content'])>512){echo json_encode(array('code'=>102,msg=>'字节数'.strlen($_REQUEST['content']).',内容不能超过512个字节'));exit;}
        if(isset($_REQUEST['s'])&&isset($_REQUEST['uids'])&&
        isset($_REQUEST['items'])&&isset($_REQUEST['content'])&&
        isset($_REQUEST['sender'])&&isset($_REQUEST['title'])
        )
        {
            $servers=$this->get_server_config();
            if(isset($servers[$_REQUEST['s']]))
            {
              
                $server=$servers[$_REQUEST['s']];
                $this->__sendPlayersMail($server['GIP'],$server['GPort'],$_REQUEST['sender'],$_REQUEST['title'],$_REQUEST['content'],$_REQUEST['uids'],$_REQUEST['items']);
                $ret['msg']='success';
		$ret['test']=array($server['GIP'],$server['GPort'],$_REQUEST['sender'],$_REQUEST['title'],$_REQUEST['content'],$_REQUEST['uids'],$_REQUEST['items']);
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
        if(empty($_REQUEST['title'])||strlen($_REQUEST['title'])>32){echo json_encode(array('code'=>101,msg=>'字节数'.strlen($_REQUEST['title']).',标题不能超过32个字节'));exit;}
        if(empty($_REQUEST['content'])||strlen($_REQUEST['content'])>512){echo json_encode(array('code'=>102,msg=>'字节数'.strlen($_REQUEST['content']).',内容不能超过512个字节'));exit;}
        if(isset($_REQUEST['s'])&&isset($_REQUEST['st'])&&isset($_REQUEST['sender'])&&
        isset($_REQUEST['items'])&&isset($_REQUEST['ed'])&&
        isset($_REQUEST['minlv'])&&isset($_REQUEST['maxlv'])&&
        isset($_REQUEST['content'])&&isset($_REQUEST['title'])&&
        isset($_REQUEST['rstart'])&&isset($_REQUEST['rend'])&&
        isset($_REQUEST['sviplv'])&&isset($_REQUEST['eviplv'])
        )
        {
                $servers=$this->get_server_config();
//                if(isset($servers[$_REQUEST['s']]))
//                {
//                  
//                    $server=$servers[$_REQUEST['s']];
//                    $this->__sendAllPlayersMail($server['GIP'],$server['GPort'],$_REQUEST['sender'],$_REQUEST['title'],
//                    $_REQUEST['content'],$_REQUEST['items'],$_REQUEST['st'],$_REQUEST['ed'],$_REQUEST['minlv'],$_REQUEST['maxlv'],
//                    $_REQUEST['rstart'],$_REQUEST['rend'],$_REQUEST['sviplv'],$_REQUEST['eviplv']
//                    );
//                   $ret['msg']='success';
//                    self::manage_log();
//                }
                foreach($_REQUEST['s'] as $k=>$v){
                        if(isset($v))
                        {
                        $server=$servers[$v];
                        $this->__sendAllPlayersMail($server['GIP'],$server['GPort'],$_REQUEST['sender'],$_REQUEST['title'],
                        $_REQUEST['content'],$_REQUEST['items'],$_REQUEST['st'],$_REQUEST['ed'],$_REQUEST['minlv'],$_REQUEST['maxlv'],
                        $_REQUEST['rstart'],$_REQUEST['rend'],$_REQUEST['sviplv'],$_REQUEST['eviplv']
                        );
                        self::manage_log();
                        }
                }
                $ret['msg']='success';
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
