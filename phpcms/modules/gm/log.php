<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
pc_base::load_sys_class('db_factory', '', 0);
ini_set('html_errors','1');
error_reporting(E_ALL);
class log extends admin {
    /**
     * @var MongoClient
     */
    private $_mongo;
    /**
     * @var MongoClient
     */
    private $_account;
    public function __construct() {
        //mogo数据已废除
        //$mongo=pc_base::load_config('mongo','mongo');
        //$this->_mongo=new MongoClient($mongo['host']);
        $this->_account=pc_base::load_model('account_model');
    }   

    public function actionLog(){
    	$st=!empty($_REQUEST['st'])? $_REQUEST['st'] : date('Y-m-d H:i:s',time()-432000) ;
    	$ed=!empty($_REQUEST['ed'])? $_REQUEST['ed'] : date('Y-m-d H:i:s') ;
    	$page=!empty($_REQUEST['page'])? $_REQUEST['page'] : '' ;
    	if(!isset($_REQUEST['username'])){
    		$username=" and username='anfeng'";	
    	}elseif(empty($_REQUEST['username'])){
    		 $username='';
    	}else{
    		$username=' and username="'.$_REQUEST['username'].'"';
    	}
    	if(!isset($_REQUEST['action'])){
    		 $action=' and action="buy"';
            }elseif(empty($_REQUEST['action'])){
                     $action='';
            }else{
    		$action=' and action="'.$_REQUEST['action'].'"';
            }
    	$this->_account->setTable('GMlog');
    	if(!empty($page)&&$page>1){
    		$st=date('Y-m-d H:i:s',strtotime($st));
    		$ed=date('Y-m-d H:i:s',strtotime($ed));
    	}
    	$time="time between '".$st."' and '".$ed."'";
        	//$log=$this->_account->select("action='buy' and ".$time,"action,data,time,username");
    	$log=$this->_account->listinfo($time.$action.$username,"",$page,'40');
    	//$number = $this->_account->get_one("action='buy' and ".$time, "COUNT(*) AS num");
    	//$pages=pages($number['num'], '1', '30' , '' , array() ,'10');
        	$pages=$this->_account->pages;
    	$this->_account->setTable('GMadmin');
    	$username=$this->_account->select('','username');
    	include $this->admin_tpl('actionLog','gm');
    }

    public function export(){
        $ret=array('code'=>0,'msg'=>'导出成功');
        $st="'".$_GET['st']."'";
        $ed="'".$_GET['ed']."'";
        $file='log';
	    $action=empty($_GET['action']) ? '' : 'AND action='."'".$_GET['action']."'";
        $username=empty($_GET['username']) ? '' : 'AND username='."'".$_GET['username']."'";
	    $address=PHPCMS_PATH.'uploadfile/'.$file.'.xls';
	    $str='username,action,time,data';
        $sql=sprintf('mysql -uroot GM_gat -e "select %s from GMlog where time between %s and %s  %s %s ORDER BY time DESC;" > %s',$str,$st,$ed,$action,$username,$address);
        //exit($sql);
	    exec($sql);
        if(file_exists($address)){
            header("Cache-Control: public"); 
            header("Content-Description: File Transfer"); 
            header('Content-disposition: attachment; filename='.basename($address)); //文件名   
            header("Content-Type: application/vnd.ms-excel");  
            header("Content-Transfer-Encoding: xls");  
            header('Content-Length: '. filesize($address));  
            @readfile($address);
        }else{
             echo '导出失败';
        }
    }

    public function  index()
    {
        $servers=$this->get_server_config();
        include $this->admin_tpl('log','gm');
    }
    
    public function query()
    {
        ini_set('memory_limit', '512M');
        $ret=array('code'=>0,'data'=>array(),'msg'=>'');
        $condition=array();
        $db=0;
        if(!isset($_POST['sid']))
        {
            $ret['code']=1;
            $ret['msg']='参数错误';
        }else{
            $db='s'.$_POST['sid'];
            $collection=$_POST['time'];
            $limit=$_POST['limit'];
//             isset($_POST['time'])&&$condition['TIME']=$_POST['time'];
            !empty($_POST['module'])&&$condition['MODULE']=$_POST['module'];
            !empty($_POST['action'])&&$condition['ACTION']=$_POST['action'];
            !empty($_POST['uid'])&&$condition['UID']=trim($_POST['uid']);
            $cursor=$this->_mongo->selectDB($db)->selectCollection($collection)->find($condition);
            $cursor->sort(array('TIME'=>-1));
            $cursor->limit($limit);
            $data=iterator_to_array($cursor);
//             usort($data, function(&$a,&$b){
// //                 $atime=date_create_from_format('Y-m-d H:i:s', $a['TIME']);
// //                 $btime=date_create_from_format('Y-m-d H:i:s', $b['TIME']);
//                 $atime=strtotime($a['TIME']);
//                 $btime=strtotime($b['TIME']);
//                 if($atime&&$btime)
//                 {
//                     return $atime<$btime;
//                 }
//                 return false;
//             });
            $rdata=array();
            foreach ($data as $k=>&$v)
            {
                if(isset($v['_id']))
                {
                    unset($v['_id']);
                }
//                 $data[]=$v;
//                 unset($data[$k]);
//                 $rdata[]=$v;
            }
            $ret['data']=array_values($data);
            $ret['msg']='查询成功';
        }
        echo json_encode($ret);
    }
    
}
