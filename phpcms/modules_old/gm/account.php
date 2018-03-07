<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
ini_set('html_errors','1');
error_reporting(E_ALL);
// function __autoload($class)
// {
//    pc_base::load_app_class($class);
// }
class account extends admin {
    /**
    * @var account_model
    */
    protected $_account;
    
    public function __construct() {
        $this->_account=pc_base::load_model('account_model');
    }   
    
    public function  index()
    {
         $servers=$this->get_server_config();
       include $this->admin_tpl('account','gm');
    }
    public function search()
    {
        $ret=array('ret'=>0,'msg'=>'','uid'=>0);
        $sid=$_POST['sid'];
        $account=$_POST['account'];
        $uname=$_POST['uname'];
	$servers=$this->get_server_config();
	/*99 102服开启关闭经常改不记在查找范围内*/
	unset($servers['99']);
	unset($servers['999']);
        if(!empty($account))
        {
	foreach($servers as $k=>$v){
	    $sid=$v['ServerType'];
            if($this->_account->changeConnection($sid))
            {
                $cond='';
                $cond=sprintf("`AccountName`='%s'",$account);
		$array=$this->_account->get_one($cond,'`ID`,`AccountName`,`PlayerName`');
		if($array)$user[$sid]=$array;
            }
	}
	}
	if(!empty($uname)||empty($account)){
            if($this->_account->changeConnection($sid))
            {
                $cond=sprintf("`PlayerName`='%s'",$uname);
                $array=$this->_account->get_one($cond,'`ID`,`AccountName`,`PlayerName`');
                if($array)$user[$sid]=$array;
            }
	}
		if($user)
                {
                    $ret['uid']=$user;//$user['ID'];
                }else{
                    $ret['ret']=2;
                    $ret['msg']='Vai trò không tồn tại';
                }
        echo json_encode($ret);
    }

    public function role_switch(){
        $servers=$this->get_server_config();
	include $this->admin_tpl('switch','gm');
    }

    public function findRole(){
	$data=$_POST;
        if(isset($_SESSION['sid'])){
            $data['sid']=!isset($data['sid'])? $_SESSION['sid'] : $data['sid'];
        }else{
            $data['sid']=102;
        }
        if($this->_account->changeConnection($data['sid']))
        {
            $cond=sprintf("`ID`='%s' OR `ID`='%s'",$data['oldUid'],$data['newUid']);
            $array=$this->_account->select($cond,'`ID`,`AccountName`,`PlayerName`');
	    $ret['ret']=1;
            $ret['msg']=$array;
        }else{
            $ret['ret']=0;
            $ret['msg']='服务器未开启';
        }
	echo json_encode($ret);
    }

    public function switchRole(){
        $data=$_POST;
        if(isset($_SESSION['sid'])){
            $data['sid']=!isset($data['sid'])? $_SESSION['sid'] : $data['sid'];
        }else{
            $data['sid']=102;
        }
	if(empty($data['oldUid']) || empty($data['newUid'])){
            $ret['ret']=11;
            $ret['msg']='uid缺失';
            exit(json_encode($ret));
        }
        if($this->_account->changeConnection($data['sid']))
        {
	    //$this->kickOut($data);
            $old=$this->_account->get_one('`ID`='.$data['oldUid'],'`AccountName`');
	    $new=$this->_account->get_one('`ID`='.$data['newUid'],'`AccountName`');
	    $sqls[]=$this->_account->update($old,'`ID` ='.$data['newUid']);
            $sqls[]=$this->_account->update($new,'`ID` ='.$data['oldUid']);
            if($sqls['0'] && $sqls['1']){
                $cond=sprintf("`ID`='%s' OR `ID`='%s'",$data['oldUid'],$data['newUid']);
                $array=$this->_account->select($cond,'`ID`,`AccountName`,`PlayerName`');
 file_put_contents('switch.txt',date('Y-m-d H:i:s').'转换成功:'.'old:'.$data['oldUid'].json_encode($old).'---'.'new:'.$data['newUid'].json_encode($new).PHP_EOL,FILE_APPEND);
                $ret['ret']=1;
                $ret['msg']=$array;
            }else{
file_put_contents('switch.txt',date('Y-m-d H:i:s').'转换失败:'.'old:'.$data['oldUid'].json_encode($old).'---'.'new:'.$data['newUid'].json_encode($new).PHP_EOL,FILE_APPEND);
                $ret['ret']=4;
                $ret['msg']='转换失败';
            }
	}else{
            $ret['ret']=0;
            $ret['msg']='Máy chủ không được bật';
        }
        echo json_encode($ret);
    }
        
    public function kickOut($data)
    {
        $ret=array('error'=>0,'code'=>0,'msg'=>'');
            $servers=$this->get_server_config();
            $server=$servers[$data['sid']];
            if(isset($server['GIP'])&&isset($server['GPort']))
            {
                 $this->kick_out($server['GIP'], $server['GPort'], $data['oldUid']);
		 $this->kick_out($server['GIP'], $server['GPort'], $data['newUid']);                 
                 self::manage_log();
            }else {
                  $ret['msg']='没有服务器配置信息无法更新';
            }
          
    }
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

}
