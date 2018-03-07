<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
class account extends admin {
    /**
     * @var Redis
     */
    private $_redis;
    
     /**
     * @var model
     */
    private $_online;
    private $_number;
    
    public function __construct() {
        $this->_online=pc_base::load_model('online_model');
	$this->_daily_ltv=pc_base::load_model('daily_ltv_model'); 
        $this->_daily_base=pc_base::load_model('daily_base_model');
        //ltv 内容
        $this->_ltvContent=pc_base::load_config('information','ltv');
        $this->_bases=pc_base::load_config('information','base');
    }

    public function ltv(){
        $servers=$this->_ltvContent;
	$ltvtime=pc_base::load_config('information','ltvtime');
        $st=date('Y-m-d',time()-864000);//$_POST['st'];
        $ed=date('Y-m-d');//$_POST['et'];
        $time=sprintf("`time` between '%s' AND '%s'",$st,$ed);
        $result=$this->_daily_ltv->select($time);
	$channel=pc_base::load_config('ltv');//混服渠道
        include   $this->admin_tpl('ltv','account');
    }
    
    public function base(){
        $servers=$this->_bases;
	$basetime=pc_base::load_config('information','basetime');
	$channel=pc_base::load_config('ltv');//混服渠道
	$server=$this->get_server_config();//区服
        $st=date('Y-m-d');//,time()-864000);//$_POST['st'];
        $ed=date('Y-m-d');//$_POST['et'];
        $time=sprintf("`time` between '%s' AND '%s' and `game`!='all'",$st,$ed);
        $result=$this->_daily_base->select($time);
	$arr=array('pr','one_liucun','two_liucun','three_liucun','four_liucun','five_liucun','six_liucun','seven_liucun','ten_liucun','fifteen_liucun','twentyone_liucun','thirty_liucun');
        foreach($result as $k=>$v){
                $result[$k]['channel']=$channel[$v['channel']];
                foreach($v as $k1=>$v1){
                        if(in_array($k1,$arr)&& !empty($v1)) $result[$k][$k1]=$v1.'%';
                }
        }
        include   $this->admin_tpl('base','account');
    }
      
    public function findLtv(){
        $ret=array('code'=>0,'msg'=>'Truy vấn trống','content'=>'');
        $condition='*';//`time`,`num`,`one_amount`,`one_ltv`';//查询内容
        $st=$_POST['st'];
        $ed=$_POST['ed'];
        $sql=sprintf("SELECT %s FROM GMdaily_ltv where `time` BETWEEN '%s' AND '%s'",$condition,$st,$ed);
        $this->_daily_ltv->query($sql);
        $result=$this->_daily_ltv->fetch_array();
        if($result){$ret['code']=1;}
        $ret['content']=$result;
	$ret['ltvtime']=$basetime=pc_base::load_config('information','ltvtime');
        echo json_encode($ret);
    }
    
    public function findbase(){
        $ret=array('code'=>0,'msg'=>'Truy vấn trống','content'=>'');
        $condition='*';//`time`,`num`,`one_amount`,`one_ltv`';//查询内容
        $st=$_POST['st'];
        $ed=$_POST['ed'];
	$ch=empty($_POST['ch']) ? 'AND `channel`!="A"' : 'AND `channel`='."'".$_POST['ch']."'";
	$sid=empty($_POST['sid']) ? 'AND `game`!="all"' : 'AND `game`='."'".$_POST['sid']."'";
        $sql=sprintf("SELECT %s FROM GMdaily_base where `time` BETWEEN '%s' AND '%s' %s %s",$condition,$st,$ed,$ch,$sid);
        $this->_daily_base->query($sql);
        $result=$this->_daily_base->fetch_array();
        if($result){$ret['code']=1;}
	$channel=pc_base::load_config('ltv');
	$arr=array('pr','one_liucun','two_liucun','three_liucun','four_liucun','five_liucun','six_liucun','seven_liucun','ten_liucun','fifteen_liucun','twentyone_liucun','thirty_liucun');
	foreach($result as $k=>$v){
		$result[$k]['channel']=$channel[$v['channel']];
		foreach($v as $k1=>$v1){
			if(in_array($k1,$arr)&& !empty($v1)) $result[$k][$k1]=$v1.'%';
		}
	//	if(!empty($v['pr']))$result[$k]['pr']=$v['pr'].'%';
	}
        $ret['content']=$result;
	$ret['basetime']=$basetime=pc_base::load_config('information','basetime');
        echo json_encode($ret);
    }

    public function exportLtv(){
        $ret=array('code'=>0,'msg'=>'导出成功');
        $st="'".$_GET['st']."'";
        $ed="'".$_GET['ed']."'";
        $file=$_GET['file'];
	$ch=empty($_GET['ch']) ? '' : 'AND channel='."'".$_GET['ch']."'";
        $sid=empty($_GET['sid']) ? '' : 'AND game='."'".$_GET['sid']."'";
	$address=PHPCMS_PATH.'uploadfile/'.$file.'.xls';
        $sql=sprintf('mysql -uroot GM_af -e "select * from GMdaily_%s where time between %s and %s  %s %s;" > %s',$file,$st,$ed,$ch,$sid,$address);
        //$sql=sprintf('mysql -uroot GM_af -e "select * from GMdaily_ltv;" > %s',$address);
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

    public function tabTime()
    {
        $sid=isset($_SESSION['sid'])?$_SESSION['sid']:1;
        $st=isset($_SESSION['st'])?$_SESSION['st']:date('Ymd',time()).'0000';
        $ed=isset($_SESSION['ed'])?$_SESSION['ed']:date('Ymd',time()+86400).'0000';
        $sql='SELECT * FROM GMonline where  `Time`="'.$st.'" ';
        $this->_online->query($sql);
        $list=$this->_online->fetch_array();
        $date='';
        $online='';
        $i=0;
        $range=substr($st,0,8).'-'.substr($ed,0,8);
        $servers=$this->get_server_config();
        $table=array();
        foreach ($list as $k=>$v)
        {
            if($i++%10==0)
            {
                $table[$k]=$v;
            }
        }
        include $this->admin_tpl('table','account');
    }
    
    public function table()
    {
        $number=$this->tab();
        $servers=array();
        $servers=$this->get_server_config();
        $sid=isset($_SESSION['sid'])?$_SESSION['sid']:1;
        $st=isset($_SESSION['st'])?$_SESSION['st']:date('Ymd',time()).'0000';
        $ed=isset($_SESSION['ed'])?$_SESSION['ed']:date('Ymd',time()+86400).'0000';
        $server=array();
        foreach($servers as $k=>$v){
            $server[]=$v;
        }
        $count=count($server);
         foreach($server as $x=>$y){
            //$bb=$server["$aa"]['ServerType'];echo $bb;
            $bb=$y['ServerType'];
            $sql='SELECT * FROM GMonline where `ServerType`='.$bb." AND `Time`>='".$st."' AND `Time`<='".$ed."'";
            $this->_online->query($sql);
            $list=$this->_online->fetch_array();
            $date='';
            $online='';
            $i=0;
            $range=substr($st,0,8).'-'.substr($ed,0,8);
            $table=array();$tt=array();
            foreach ($list as $k=>$v) 
            {
                    $v['server']=$y['text'];
                    if($i++%10==0)
                    {
                        $table[$k]=$v;
                   }  
            }
            $data[$x]=$table;
         }
         
         foreach ($data as $k=>$v)
         {
             foreach($v as $kk=>$vv){
                 $unit[]=$vv['OnlinePlayers'];
             }
         }
         $max=max($unit);
         
        include $this->admin_tpl('table','account');
    }
    
    public function tab()
    {
        $servers=array();
        $servers=$this->get_server_config();
        $st=isset($_SESSION['st'])?$_SESSION['st']:date('Ymd',time()).'0000';
        $server=array();
        foreach($servers as $k=>$v){
            $server[]=$v;
        }
        $count=count($server);
        foreach($server as $x=>$y){
            $bb=$y['ServerType'];
            $sql='SELECT * FROM GMonline where `ServerType`='.$bb." AND `Time`='".$st."' ";
            $this->_online->query($sql);
            $list=$this->_online->fetch_array();
            foreach ($list as $k=>$v)
            {
                $number=$number+$v['OnlinePlayers'];
            }
        }
        return $number;
    }
    
    public function  all()
    {
        
        $sid=isset($_SESSION['sid'])?$_SESSION['sid']:1;
        $st=isset($_SESSION['st'])?$_SESSION['st']:date('Ymd',time()).'0000';
        $ed=isset($_SESSION['ed'])?$_SESSION['ed']:date('Ymd',time()+86400).'0000';
        $sql='select a.* from GMonline as a right join (select ServerType,max(TIME) as TIME from GMonline group by ServerType) as b on a.ServerType=b.ServerType and a.TIME=b.TIME order by a.ServerType';
	//$sql='SELECT Id,ServerType,TIME,OnlinePlayers FROM GMonline WHERE TIME IN (SELECT MAX(TIME) FROM GMonline GROUP BY ServerType) GROUP BY ServerType';
        $this->_online->query($sql);
        $list=$this->_online->fetch_array();
        $date='';
        $online='';
        $i=0;
        $range=substr($st,0,8).'-'.substr($ed,0,8);
        $servers=$this->get_server_config();
        foreach ($list as $k=>$v) 
        {
            if($i++%1==0)
            {
                $online.='["'.$v['ServerType'].'区",'.$v['OnlinePlayers'].'],';
            }   
        }
        
        include $this->admin_tpl('all','account');
    }
    
    public function  index()
    {
        
        $sid=isset($_SESSION['sid'])?$_SESSION['sid']:1;
        $st=isset($_SESSION['st'])?$_SESSION['st']:date('Ymd',time()).'0000';
        $ed=isset($_SESSION['ed'])?$_SESSION['ed']:date('Ymd',time()+86400).'0000';
        $sql='SELECT * FROM GMonline where `ServerType`='.$sid." AND `Time`>='".$st."' AND `Time`<='".$ed."'";
        $this->_online->query($sql);
        $list=$this->_online->fetch_array();
        $date='';
        $online='';
        $i=0;
        $range=substr($st,0,8).'-'.substr($ed,0,8);
        $servers=$this->get_server_config();
        foreach ($list as $k=>$v) 
        {
            if($i++%1==0)
            {
                $tm=substr($v['Time'], 8,2).':'.substr($v['Time'], 10,2);
                $date.="'".$tm."',";
                $online.=$v['OnlinePlayers'].',';
            }   
        }
        
        include $this->admin_tpl('online','account');
    }
    
    public function updateSessionTime()
    {
        if(isset($_POST['st']))
        {
            $_SESSION['st']=$_POST['st'];
            $_SESSION['ed']=$_POST['ed'];
        }
    }
    
    
}
