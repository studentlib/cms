<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
ini_set('html_errors','1');
error_reporting(E_ALL);
class gift extends admin {
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
     /**
     * @var used
     */
    private $_used;
    
    public function __construct() {
        $this->_channel=pc_base::load_model('channel_model');
        $this->_gift=pc_base::load_model('gift_model');
        $this->_item=pc_base::load_model('item_model');
        $this->_used=pc_base::load_model('used_model');
    }   
   
    public function  index()
    {
        $config=$this->get_config();
        $items=$this->_item->listinfo('','',1,100);
        $list=$this->_channel->listinfo('','',1,100);
        //通用礼包使用次数
        $sql[]="select b.no,count(a.key) as usedcount from gift as a left join item as b on a.no=b.no and a.uid!='' GROUP BY b.no";
        $this->_gift->muti_query($sql);
        $str=$this->_gift->muti_results();
        foreach($str['0'] as $k=>$v){
                $arr[$v['no']]=$v['usedcount'];
        }
        foreach($items as $k=>$v){
         if($v['total_used']>1){
           $gift_num[$v['no']]['number']=$v['number'];
           $usedcount=$this->_gift->get_one("`no`='".$v['no']."'","usedcount");
           $gift_num[$v['no']]['usedcount']=$usedcount['usedcount'];
           $gift_num[$v['no']]['no']=$v['no'];
           continue;
         }
         $gift_num[$v['no']]['number']=$v['number'];
         $gift_num[$v['no']]['usedcount']=$arr[$v['no']];
         $gift_num[$v['no']]['no']=$v['no'];
        }
        $cconfig=array();
        foreach ($list as &$value)
        {
            $cconfig[$value['code']]=$value;
        }
        foreach ($items as &$value)
        {
            $value['cname']=$cconfig[$value['channel']]['name'];
            $value['item']=explode(',', $value['item']);
            foreach ($value['item'] as &$v)
            {
                $lv=explode(':', $v);
                $v=$config[$lv[0]]['name']."(".$lv[1]."个)";
            }
             $value['item']=join(', ', $value['item']);
        }
        include $this->admin_tpl('gift','gift');
    }
 
    public function  indexOld()
    {
        $config=$this->get_config();
        $items=$this->_item->listinfo('','',1,100);
        $list=$this->_channel->listinfo('','',1,100);
	//通用礼包使用次数
	foreach($items as $k=>$v){
	 if($v['total_used']>1){
	   //$str=$this->_item->get_one("`no`='".$v['no']."'","`total_used` as number");
	   //$gift_num[$v['no']]['number']=$str['number'];
	   $gift_num[$v['no']]['number']=$v['number'];
	   $usedcount=$this->_gift->get_one("`no`='".$v['no']."'","usedcount");
	   $gift_num[$v['no']]['usedcount']=$usedcount['usedcount'];
	   $gift_num[$v['no']]['no']=$v['no'];
	   continue;
	 }
//	 $sql1="select count(*) from gift where `no`='".$v['no']."'";
//	 $this->_gift->muti_query($sql1);
//	 $str=$this->_gift->muti_results();print_r($str);
//       $str=$this->_gift->select("`no`='".$v['no']."' and `usedcount`=1",'no,usedcount');
	 //$str=$this->_gift->get_one("`no`='".$v['no']."'","count('key') as number");
	 //$gift_num[$v['no']]['number']=$str['number'];
	 $gift_num[$v['no']]['number']=$v['number'];
	 $usedcount=$this->_gift->get_one("`usedcount`='1' and `no`='".$v['no']."'","count('key') as usedcount");
	 $gift_num[$v['no']]['usedcount']=$usedcount['usedcount'];
	 $gift_num[$v['no']]['no']=$v['no'];
	}
        $cconfig=array();
        foreach ($list as &$value) 
        {
            $cconfig[$value['code']]=$value;
        }
        foreach ($items as &$value) 
        {
            $value['cname']=$cconfig[$value['channel']]['name'];
            $value['item']=explode(',', $value['item']);
            foreach ($value['item'] as &$v)
            {
                $lv=explode(':', $v);
                $v=$config[$lv[0]]['name']."(".$lv[1]."个)";
            }
             $value['item']=join(', ', $value['item']);
        }
        include $this->admin_tpl('gift','gift');
    }
   
    public function sel_code()
    {
	include $this->admin_tpl('sel_code','gift');
    }

    public function sel_code_id(){
	$data=$_POST;
	$ret=array('code'=>0,'msg'=>'');
	if(empty($data['uid'])){
	  $str=$this->_used->listinfo("`code`='".$data['code']."'",'',1,100);
	  
	  $ret['str']=$str;
	}elseif(empty($data['code'])){
	  $str=$this->_used->listinfo("`uid`='".$data['uid']."'",'',1,100);
	  $ret['str']=$str;
	}else{
	  $str=$this->_used->get_one("`uid`='".$data['uid']."' and `code`='".$data['code']."'");
	  if(empty($str)){$ret['code']=110;$ret['msg']='该用户没有用过该兑换码';}
	     else{ echo json_encode(array('code'=>110,'msg'=>'该用户已用过该兑换码'),JSON_UNESCAPED_UNICODE);exit;}
	}
	foreach($ret['str'] as $k=>$v){
	  $ret['str'][$k]=implode("--",$v);
	}
    	$ret['str']=implode("<br>",$ret['str']);
	echo json_encode($ret);
    }
 
    public function delCode()
    {
         $ret=array('code'=>1,'msg'=>'');
        if(isset($_REQUEST['batch'])&&isset($_REQUEST['channel']))
        {
            $result=$this->_item->delete("`no`='".$_REQUEST['batch']."' and `channel`='".$_REQUEST['channel']."'"); 
            $result&$result=$this->_gift->delete("`no`='".$_REQUEST['batch']."' and `channel`='".$_REQUEST['channel']."'"); 
            if($result)
            {
                $ret['code']=0;
                self::manage_log();
            }     
        }else{
            $ret['msg']='lost_param';
        }
        echo json_encode($ret);
    }
    public function exportCode()
    {
        $batch=$_REQUEST['batch']?$_REQUEST['batch']:0;
        $channel=$_REQUEST['channel']?$_REQUEST['channel']:0;
        if(!empty($batch)&&!empty($channel))
        {
	    
            $batch='`no`='."'$batch'";
            $channel='`channel`='."'$channel'";
            $condition=$batch;
            $condition.=" and ".$channel;
            $condition.=" and `uid` is null ";
            $list=($this->_gift->select($condition,'key'));
            $str='';
            
            foreach ($list as $v)
            {
                $str.=$v['key']."\r\n";
            }
            $dir="code/".$_REQUEST['batch']."/".$_REQUEST['channel'];
            if(!file_exists($dir)){
                mkdir($dir,0777,true);
            }
            $filename="code/".$_REQUEST['batch']."/".$_REQUEST['channel'].'/code.txt';
            file_put_contents($filename,$str);
	    header("Cache-Control: public"); 
            header("Content-Description: File Transfer"); 
            header('Content-disposition: attachment; filename='.basename($filename)); //文件名   
            header("Content-Type: application/text");  
            header("Content-Transfer-Encoding: text");  
            header('Content-Length: '. filesize($filename));  
            @readfile($filename);
        }else{
            echo 'lost_param';
        }
    }
    
    
    public function giftList()
    {
	$page=$_REQUEST['page']?$_REQUEST['page']:0;
        $batch=$_REQUEST['batch']?$_REQUEST['batch']:'1';
        $channel=$_REQUEST['channel']?$_REQUEST['channel']:'a001';
        $batch=empty($batch)?'':'`no`='."'$batch'";
        $channel=empty($channel)?'':'`channel`='."'$channel'";
        $condition=$batch;
        if($condition)
        {
           if($channel)
           {
               $condition.=" and ".$channel;
           }
        }else{
           $condition=$channel;
        }
        $list=($this->_gift->listinfo($condition,'',$page,50));
        $batchs=$this->_gift->query('select distinct no from gift');
        $batchs=$this->_gift->fetch_array();
        $channels=$this->_gift->query('select distinct channel from gift');
        $channels=$this->_gift->fetch_array();
        $pages=$this->_gift->pages;
	if($pages!=''){
	  $pages.='&nbsp&nbsp第<input type="text" id="page" value="" style="width:30px;"/>页';
	}
        include $this->admin_tpl('giftList','gift');
    }
    
    public function getGift()
    {
        $ret=array('code'=>0,'item'=>array());
        $fmt=isset($_REQUEST['type'])?$_REQUEST['type']:'binary';
        if(isset($_REQUEST['code'])&&isset($_REQUEST['uid']))
        {
             $uid=$_REQUEST['uid'];
             $kcode=($_REQUEST['code']);
             $code=$this->_gift->get_one("`key`='".$kcode."'");
	    
             file_put_contents('getGift.txt', print_r($code,1),FILE_APPEND);
             if($code)
             {
                $batch=sprintf('%d',hexdec(substr($code['code'],0,2)));
                $channel=sprintf('%s',substr($code['code'],2,4));
                $item=$this->_item->get_one("`channel`='".$channel."' and `no`='".$batch."'");
             file_put_contents('getGift.txt', print_r($item,1),FILE_APPEND);
             file_put_contents('getGift.txt', "at 001",FILE_APPEND);
                if($item['total_used']>1)
                {
             file_put_contents('getGift.txt', "at 002",FILE_APPEND);
                     if($code['usedcount']>=$item['total_used'])
                     {
                         //使用次数已经到达上限
                         $ret['code']=5;
             file_put_contents('getGift.txt', "at 003",FILE_APPEND);
                     }else{
                         $sql=sprintf("select uid from used_user where `uid`='%d' and `code`='%s'",$uid,$kcode);
                         $this->_gift->query($sql);
                         $user=$this->_gift->fetch_array();
             file_put_contents('getGift.txt', "at 004",FILE_APPEND);
                         if(!$user)
                         {
             file_put_contents('getGift.txt', "at 005",FILE_APPEND);
                             $code['usedcount']++;
                             if($item)
                             {
                                 $newItem=array();
                                 $item=explode(',',$item['item']);
                                 foreach ($item as $value) 
                                 {
                                     $lv=explode(':', $value);
                                     $newItem[]=array($lv[0],$lv[1]);
                                 }
                                 $ret['item']=$newItem;
                                 $ret['code']=0;
             file_put_contents('getGift.txt', "at 006",FILE_APPEND);
                             }
                             //print_r($item);
             file_put_contents('getGift.txt', print_r($item,1),FILE_APPEND);
             file_put_contents('getGift.txt', print_r($ret,1),FILE_APPEND);
			    // unset($code['uid']);
                            // $this->_gift->update($code,"`key`='".$kcode."'");
                             $insert=sprintf("insert into used_user (`code`,`uid`) values ('%s','%d')",$kcode,$uid);
                             $this->_gift->query($insert);
			     unset($code['uid']);
                             $this->_gift->update($code,"`key`='".$kcode."'");
             file_put_contents('getGift.txt', "at 007",FILE_APPEND);
                             self::manage_log();
                         }else{
                             //该角色已经使用过一次了
                             $ret['code']=6;
//                             //@todo 临时使用
             file_put_contents('getGift.txt', "at 008",FILE_APPEND);
//                             $ret['code']=4;
                         }
                         
                     }
                }else{
             file_put_contents('getGift.txt', "at 009 \n",FILE_APPEND);
                    if($code["repeat"] == 1)
                    {
             file_put_contents('getGift.txt', "at 00a",FILE_APPEND);
                        if(is_null($code['uid']))
                        {
                            $code['uid']=$uid;
                            $code['usedcount']++;
                            if($item)
                            {
                                $newItem=array();
                                $item=explode(',',$item['item']);
                                foreach ($item as $value)
                                {
                                    $lv=explode(':', $value);
                                    $newItem[]=array($lv[0],$lv[1]);
                                }
                                $ret['item']=$newItem;
                                $ret['code']=0;
                            }
                            //print_r($item);
                            $this->_gift->update($code,"`key`='".$kcode."'");
                        }
                        else 
                        {
                            $ret['code']=4;
                        }
                    }
                    else
                    {
             file_put_contents('getGift.txt', "at 00a001 \n",FILE_APPEND);
                        $batch_code=$this->_gift->get_one("`uid`='".$uid."' and `no`='".$batch."'");
             file_put_contents('getGift.txt',  $batch_code,FILE_APPEND);
                        if(!$batch_code)
                        {
             file_put_contents('getGift.txt', "at 00a002 \n",FILE_APPEND);
                            if(is_null($code['uid']))
                            {
             file_put_contents('getGift.txt', "at 00a003 \n",FILE_APPEND);
             file_put_contents('getGift.txt', print_r($code,1),FILE_APPEND);
                                $code['uid']=$uid;
                                $code['usedcount']++;
             file_put_contents('getGift.txt', print_r($item,1),FILE_APPEND);
             file_put_contents('getGift.txt', print_r($code,1),FILE_APPEND);
                                if($item)
                                {
                                    $newItem=array();
                                    $item=explode(',',$item['item']);
                                    foreach ($item as $value)
                                    {
                                        $lv=explode(':', $value);
                                        $newItem[]=array($lv[0],$lv[1]);
                                    }
                                    $ret['item']=$newItem;
                                    $ret['code']=0;
             file_put_contents('getGift.txt', "at 00a004 \n",FILE_APPEND);
             file_put_contents('getGift.txt', print_r($ret,1),FILE_APPEND);
                                }
                                //print_r($item);
                                $this->_gift->update($code,"`key`='".$kcode."'");
                                //                             self::manage_log();
                            }else{
                                //已经被使用过了
             file_put_contents('getGift.txt', "at 00a005 \n",FILE_APPEND);
                                $ret['code']=4;
                            }
                        }else{
                            //本批次已经使用过一次了不能重复使用
             file_put_contents('getGift.txt', "at 00a006 \n",FILE_APPEND);
                            $ret['code']=3;
                        }
                    }
                    
                }
             }else{
                 if(isset($_REQUEST['idn'])&&$_REQUEST['idn']!='')
                 {
             file_put_contents('getGift.txt', "at 00a005001",FILE_APPEND);
                     $url=pc_base::load_config('system','gv_gift');
                     if($url)
                     {
             file_put_contents('getGift.txt', "at 00a005002",FILE_APPEND);
                         $gv_ret=$this->gv_gift($_REQUEST['uid'],$_REQUEST['code'],$_REQUEST['idn']);
                         if(isset($gv_ret['code'])&&$gv_ret['code']==1000&&isset($gv_ret['data']['value']))
                         {
             file_put_contents('getGift.txt', "at 00a005003",FILE_APPEND);
                             $channel=pc_base::load_config('system','gv_channel');
                             $item=$this->_item->get_one(array('channel'=>$channel,'no'=>intval($gv_ret['data']['value'])));
                             if(isset($item['item']))
                             {
                                 $newItem=array();
                                 $item=explode(',',$item['item']);
                                 foreach ($item as $value)
                                 {
                                     $lv=explode(':', $value);
                                     $newItem[]=array($lv[0],$lv[1]);
                                 }
                                 $ret['item']=$newItem;
                                 $ret['code']=0;
                             }
             file_put_contents('getGift.txt', "at 00a005004",FILE_APPEND);
                             file_put_contents('gv.txt', print_r($ret,1),FILE_APPEND);
                         }else{
                             
                             $ret['code']=2;
                             if(isset($gv_ret['code'])&&$gv_ret['code']==-8100)
                             {
                                 $ret['code']=4;
             file_put_contents('getGift.txt', "at 00a005005",FILE_APPEND);
                             }
                         }
                     }else{
                         $ret['code']=2;
             file_put_contents('getGift.txt', "at 00a005006",FILE_APPEND);
                     }
                 }else{
                     $ret['code']=2;
             file_put_contents('getGift.txt', "at 00a005007",FILE_APPEND);
                 }
			 
              
             }
               
        }else{
             //缺少参数
             $ret['code']=1;
             file_put_contents('getGift.txt', "at 0011",FILE_APPEND);
        }
        if($fmt=='json')
        {
            echo json_encode($ret);
             file_put_contents('getGift.txt', "at 0012",FILE_APPEND);
             file_put_contents('getGift.txt', print_r($ret,1),FILE_APPEND);
        }else{
            $str=pack('I',$ret['code']);
            $str.=pack('S',count($ret['item']));
            foreach ($ret['item'] as $value) {
                $str.=pack('II',$value[0],$value[1]);
            }
            echo $str;
//            print_r(unpack('Icode/Slen',$str));
             file_put_contents('getGift.txt', "at 0013",FILE_APPEND);
             file_put_contents('getGift.txt', print_r($ret,1),FILE_APPEND);
             file_put_contents('getGift.txt', "at retcode:",FILE_APPEND);
             file_put_contents('getGift.txt', $ret['code'],FILE_APPEND);

             file_put_contents('getGift.txt', "at end \n",FILE_APPEND);
             file_put_contents('getGift.txt', print_r(unpack('Icode/Slen',$str),1),FILE_APPEND);
             //file_put_contents('getGift.txt', $str,FILE_APPEND);
        }
    }
    
    public function gv_gift($uid,$code,$idn)
    {
        $url=pc_base::load_config('system','gv_gift');
        $secret=pc_base::load_config('system','gv_secret');
        $appid=pc_base::load_config('system','app_id');
        $params=array(
           'app_id'=>$appid,
           'pin_no'=>$code,
           'time'=>time(),
           'idn'=>$idn,
        );
        $params['Sign']=md5($appid.$code.$params['time'].$idn.$secret);
        $data=http_build_query($params);
        $url=$url.'?'.$data;
        $ret=$this->__send_request($url);
        file_put_contents('/data/www/sg/po/phpcms/modules/gift/gv.txt', print_r($ret,1),FILE_APPEND);
        
        return $ret;
    }
    

    public function  create()
    {
        $batch=range(1, 255);
        $list=$this->_channel->listinfo('','',1,100);
        $config=$this->get_config();
        include $this->admin_tpl('create','gift');
    }
    
    public function updateState()
    {
        $ret=array('code'=>1,'msg'=>'');
        if(isset($_REQUEST['batch'])&&isset($_REQUEST['channel'])
         &&isset($_REQUEST['codecount'])&&isset($_REQUEST['codeunit'])&&isset($_REQUEST['oldcount'])
        )
        {
            $ncount=$_REQUEST['codecount']*$_REQUEST['codeunit'];
            $rcount=$this->_gift->count("`no`='".$_REQUEST['batch']."' and `channel`='".$_REQUEST['channel']."'");
            $ret['current']=$rcount;
            $ret['total']=$ncount+$_REQUEST['oldcount'];
            if($ret['current']>=$ret['total'])
            {
                $ret['code']=0;
//                self::manage_log();
            }
        }else{
            $ret['code']=2;
            $ret['msg']='lost_param';
        }
        echo json_encode($ret);
    }
    public function  getItem()
    {
        $ret=array('code'=>1,'item'=>array());
        $config=$this->get_config();
        if(isset($_REQUEST['batch'])&&isset($_REQUEST['channel']))
        {
           $item=$this->_item->get_one("`no`='".$_REQUEST['batch']."' and `channel`='".$_REQUEST['channel']."'");      
            if(is_array($item))        
            {
                if(isset($item['item']))
                {
                    $item['item']=explode(',', $item['item']);
                    if(is_array($item['item']))
                    {
                        foreach ($item['item'] as $k=>&$v) 
                        {
                            $lv=explode(':', $v);
                            $v=array('id'=>$lv[0],'count'=>$lv[1],'name'=>$config[$lv[0]]['name']);
                        }
                    }
                }
                $ret['code']=0;
                $ret['item']=$item;
            }  
            $count=$this->_gift->count("`no`='".$_REQUEST['batch']."' and `channel`='".$_REQUEST['channel']."'");
            $ret['count']=$count;
        }else{
             $ret['code']=2;
        }
        echo json_encode($ret);
    }
    
    public function  createCode()
    {
        ini_set("max_execution_time", 2400); 
        $ret=array('code'=>'0','msg'=>'');
        $array=$this->_item->get_one("`no`='".$_REQUEST['batch']."' and `channel`='".$_REQUEST['channel']."'");
        if(!empty($array)){
                $ret['code']='110';
                $ret['msg']='礼包只可生成一次';
                exit(json_encode($ret));
        }
        if(isset($_REQUEST['batch'])&&isset($_REQUEST['channel'])
        &&isset($_REQUEST['codecount'])&&isset($_REQUEST['codeunit'])&&isset($_REQUEST['items']))
        {
             $count=$_REQUEST['codecount']*$_REQUEST['codeunit'];//var_dump($count);
            if(is_numeric($count))
            {
                $gift=$this->_gift->get_one("`no`='".$_REQUEST['batch']."' and `channel`='".$_REQUEST['channel']."'","*",'code desc');
                $seq=0;
                if(isset($gift['code']))
                {
                   $seq=hexdec(substr($gift['code'], 6,6))+1;
                }
                  $array=array();
                if(is_numeric($seq))
                {
                  
                    for($i=0;$i<$count;++$i)
                    {
                        // $code=sprintf('%02x%s%06x',$_REQUEST['batch'],$_REQUEST['channel'],$seq++);
			 $code=sprintf('%02x%s%06x%s',$_REQUEST['batch'],$_REQUEST['channel'],$seq++,date(YmdHis));
			$array[]=array('no'=>$_REQUEST['batch'],'channel'=>$_REQUEST['channel'],'code'=>$code,'key'=>strtolower(substr(base64_encode(md5($code)), 20,12)).$_REQUEST['batch'],'repeat'=>$_REQUEST['repeat']);
                         if($i%1000==0)
                         {
                              $status=$this->_gift->insertArray($array);
                              $array=array();
			      if(!$status){
                                 break;
                              }
                         }
                    }
                }
                if(count($array))
                {
                    $status=$this->_gift->insertArray($array);
                }
		//礼品码冲突碰撞，删除已经存入数据库的数据
                if(!$status){
                    $this->_gift->delete('`no`='.$array['no']);
                    $ret['code']=111;
                    $ret['msg']='制造失败';
                    exit(json_encode($ret));
                }
                $item=$this->_item->get_one("`no`='".$_REQUEST['batch']."' and `channel`='".$_REQUEST['channel']."'");      
                if(!is_array($item))        
                {
                    $item=array('no'=>$_REQUEST['batch'],'channel'=>$_REQUEST['channel'],'item'=>implode(',',$_REQUEST['items']),'number'=>$_REQUEST['allNumber']);
                    if(isset($_REQUEST['common'])&&$_REQUEST['common']&&isset($_REQUEST['usecount'])&&$_REQUEST['usecount'])
                    {
                        $item['total_used']=$_REQUEST['usecount'];
                    }else{
                        $item['total_used']=1;
                    }
                    $this->_item->insert($item);
                }
		$ret['msg']='制造完成';  
                self::manage_log();
            }
        }else{
            $ret['code']=1;
            $ret['msg']='lost_param';
        }
        echo json_encode($ret);
    }
    private  function __send_request($url)
    {
        $ch=NULL;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'DJRobot');
        //         curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $str = curl_exec($ch);
        $ret=json_decode($str,true);
        return $ret;
    }
    
}
