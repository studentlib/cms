<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class index extends admin {
    /**
     * @var model
     */
    private $_db;
    const MAX_USER_MAP=10000;
	public function __construct() {
	   $this->_db=pc_base::load_model('errors_model');
	}
	
	public function createUser()
	{
	    if(isset($_REQUEST['sid'])&&isset($_REQUEST['account'])&&isset($_REQUEST['uid'])
	    &&isset($_REQUEST['name'])&&isset($_REQUEST['vipLv'])&&isset($_REQUEST['lv']))
	    {
	        $rKey='Roles:'.self::times33($_REQUEST['account'])%self::MAX_USER_MAP.":".$_REQUEST['account'];
	        $redis=$this->getPushRedis();
            $data=array(
            'sid'=>$_REQUEST['sid'],
//            'account'=>$_REQUEST['account'],
            'uid'=>$_REQUEST['uid'],
            'name'=>urlencode($_REQUEST['name']),
            'vipLv'=>$_REQUEST['vipLv'],
            'lv'=>$_REQUEST['lv'],
            'account'=>$_REQUEST['account'],
            ); 
            $redis->hset($rKey,$_REQUEST['sid'],json_encode($data));
	        echo json_encode(array('ret'=>0,'msg'=>'success'));
	    }else{
	        echo json_encode(array('ret'=>1,'msg'=>'lost_params'));
	    }
	}
	
	public function getList() {
	   if(isset($_REQUEST['account']))
        {
            $rKey='Roles:'.self::times33($_REQUEST['account'])%self::MAX_USER_MAP.":".$_REQUEST['account'];
            $redis=$this->getPushRedis();
            $data=$redis->hgetall($rKey);
            $data=array_values($data);
            foreach ($data as $k=>$v)
            {
                $json=json_decode($v,TRUE);
                foreach ($json as $k1=>&$v1) {
                  
                   if(is_numeric($v1))
                   {
                       $v1=intval($v1);
                   }elseif(is_string($v1))
                   {
//                       $v1=iconv("ASCII","UTF-8",$v1);
//                       $v1=urlencode($v1);
                   }
                }
                $data[$k]=$json;
            }
	    file_put_contents('/data/www/sg/po/getlist.log',date('Y-m-d H:is').print_r($data,1).'\n\r',FILE_APPEND);
            echo urldecode(json_encode($data));
        }
	}
	
	public function logError()
	{

	     if(isset($_REQUEST['playerid'])&&isset($_REQUEST['deviceId'])
	     &&isset($_REQUEST['operatingSystem'])&&isset($_REQUEST['cacheErr'])
	     &&isset($_REQUEST['account'])&&isset($_REQUEST['area']))
	     {
//	         $msg=base64_decode($_REQUEST['cacheErr']);
             $data=array(
             'playerid'=>$_REQUEST['playerid'],
             'area'=>$_REQUEST['area'],
             'account'=>$_REQUEST['account'],
             'platform'=>$_REQUEST['platform'],
             'device'=>$_REQUEST['deviceId'],
             'system'=>$_REQUEST['operatingSystem'],
             'err'=>$_REQUEST['cacheErr'],
             'version'=>isset($_REQUEST['ver'])?$_REQUEST['ver']:'',
             );
	         $this->_db->insert($data);
	     }else{
	         echo 'lost params';
	     }
	}
	
	public function errors()
	{
	    $page=$_REQUEST['page']?$_REQUEST['page']:0;
	    $playerid=$_REQUEST['playerid']?$_REQUEST['playerid']:0;
	    $playerid=empty($playerid)?'':'`playerid`='."'$playerid'";
	    $list=($this->_db->listinfo($playerid,'logtime desc',$page,20));
	    $pages=$this->_db->pages;
	    include $this->admin_tpl('errors','gm');
	}
	public function delete()
	{
	    $ret=array('code'=>1,'msg'=>'');
	    $id=$_REQUEST['id']?$_REQUEST['id']:0;
	    $this->_db->delete(array('ID'=>$id));
	    if($this->_db->affected_rows()>0)
	    {
	        $ret['code']=0;
	    }
	    echo json_encode($ret,JSON_FORCE_OBJECT);
	}
	
}
?>
