<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
ini_set('html_errors','1');
error_reporting(E_ALL);
class gm extends admin {
    /**
     * @var Redis
     */
    private $_redis;
    private $ufmt;
    private $pfmt;
    private $filter;
    
    public function __construct() {
        $this->ufmt="Iuid/a65acount/a65name/Ipic_id/Csex/Iexp/Clevel/Ivipexp/Cviplevel/Ilogout_time/igold/idiamond/".
        "Sap/Sapnum/Sapbuynum/Sapmax/Sstamina/Sstamina_max/Igong/Ihonor/Smapid/crace/Icreate_time/a348card/cgmlevel/".
        "Ijade/Ifirstapretime/Isecondapretime/Ifirststaminaretime/Isecondstaminaretime/Iapfrompillvalue/Istaminafrompillvalue/".
        "Isoul/Iapfriend/Ilastchattime/Irelive/Iexploit/Ibattlepower/Ifodder/Inationcontribute/Ileagueid/Inationid/Iscore/Icar/Iflag".
        "/IstaminaBuyCount/IapBuy/IstaminaBuy/IapBuyToday/IstaminaBuyToday/ImainHeroId";
        $this->pfmt="Ia65a65ICICICIii".
        "SSSSSSIIScIa348c".
        "IIIIIII".
        "IIIIIIII";
        $this->filter=array(
         'uid'=>1,'pic_id'=>2,'diamond'=>3,'gold'=>4,
         'sex'=>5,'exp'=>6,'level'=>7,'logout_time'=>8,
         'vipexp'=>9,
//         'viplevel'=>10,
         'ap'=>11,'apnum'=>12,
         'apbuynum'=>13,'apmax'=>14,'stamina'=>15,'stamina_max'=>16,
         'gong'=>17,'honor'=>18,'create_time'=>19,'jade'=>20,
         'firstapretime'=>21,'secondapretime'=>22,'firststaminaretime'=>23,'secondstaminaretime'=>24,
         'apfrompillvalue'=>25,'staminafrompillvalue'=>26,'soul'=>27,'apfriend'=>28,'lastchattime'=>29,
         'exploit'=>30,'relive'=>31,'battlepower'=>32,'fodder'=>33,'nationcontribute'=>34,'leagueid'=>35,
         'nationid'=>36,'score'=>37,'car'=>38,
          );
          $admin_group=array(1);
          $priv=in_array($_SESSION['roleid'],$admin_group);
          
    }   
    
    public  function admin()
    {
        $servers=$this->get_server_config();
        $keys=$this->getRedisKeys(50);
        include   $this->admin_tpl('gm','gm');
    }
    
    public function updateSid()
    {
          if($_POST['sid']>0)
          {
              $servers=$this->get_server_config();
              if(isset($servers[$_POST['sid']]))
              {
                   $_SESSION['sid']=$_POST['sid'];
              }
          }
    }
    
    public  function item()
    {
        $servers=$this->get_server_config();
        $keys=$this->getRedisKeys(50);
        $config=$this->get_config();
//        print_r($config);
        include   $this->admin_tpl('item','gm');
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
    

    public  function getItem()
    {
        $upk='Itid/Icount';
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        $config=$this->get_config();
        $id=$_GET['id'];
        $id=str_replace('RoleInfo:', '', $id);
        $id='Module:'.$id.":bag:0";
        $data=$redis->hgetall($id);
        $ret=array();
        if(count($data))
        {
            foreach ($data as $value) {
                $a=unpack($upk, $value);
                $a['name']=$config[$a['tid']]['name'];
                $ret[]=$a;
            }
        }
        if(count($ret))
        {
            echo json_encode($ret);
        }else{
             echo json_encode(array('code'=>1,'msg'=>'角色不存在或背包道具为空'));
        }
    }
    
    public  function addItem()
    {
       $id=isset($_GET['id'])?$_GET['id']:'';
       $sid=isset($_GET['sid'])?$_GET['sid']:'';
       $tid=isset($_GET['tid'])?$_GET['tid']:'';
       $count=isset($_GET['count'])?$_GET['count']:'';
       $ret=array('code'=>0,'msg'=>'');
       if($id&&tid&&$count&&$sid)
       {
           $servers=$this->get_server_config();
           $server=$servers[$sid];
           if(isset($server['GIP'])&&isset($server['GPort']))
           {
                $this->modify_item($server['GIP'],$server['GPort'],$id, 0, $tid, $count);
           }
           self::manage_log();
           $ret['msg']='添加成功';
       }else{
           $ret['code']=1;
           $ret['msg']='必要字段不全,不能提交';
       }
       echo json_encode($ret);
    }
    public  function addGift()
    {
       $id=isset($_GET['id'])?$_GET['id']:'';
       $sid=isset($_GET['sid'])?$_GET['sid']:'';
       $tid=isset($_GET['tid'])?$_GET['tid']:'';
       $count=isset($_GET['count'])?$_GET['count']:'';
       $ret=array('code'=>0,'msg'=>'');
       if($id&&tid&&$count&&$sid)
       {
           $servers=$this->get_server_config();
           $server=$servers[$sid];
           if(isset($server['GIP'])&&isset($server['GPort']))
           {
                $this->add_gift($server['GIP'],$server['GPort'],$id, 0, $tid, $count);
           }
           self::manage_log();
           $ret['msg']='添加礼包成功';
       }else{
           $ret['code']=1;
           $ret['msg']='必要字段不全,不能提交';
       }
       echo json_encode($ret);
    }
    
    public  function delItem()
    {
       $id=isset($_GET['id'])?$_GET['id']:'';
       $tid=isset($_GET['tid'])?$_GET['tid']:'';
       $count=isset($_GET['count'])?$_GET['count']:'';
        $sid=isset($_GET['sid'])?$_GET['sid']:'';
       $ret=array('code'=>0,'msg'=>'');
       if($id&&tid&&$count&&$sid)
       {
           $servers=$this->get_server_config();
           $server=$servers[$sid];
           if(isset($server['GIP'])&&isset($server['GPort']))
           {
                $this->modify_item($server['GIP'],$server['GPort'],$id, 1, $tid, $count);
           }
          self::manage_log();
           $ret['msg']='删除成功';
       }else{
           $ret['code']=1;
           $ret['msg']='必要字段不全,不能提交';
       }
       echo json_encode($ret);
    }
    
    public  function getExtraInfo()
    {
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        $id=$_GET['id'];
        if(!strstr($id, 'RoleInfo:'))
        {
            $id='RoleInfo:'.$id;
        }
        $id=substr($id,strpos($id, ':')+1);
        $data=array('trial'=>array(),'carbon'=>array(),'mount'=>array(),'form'=>array(),'skill'=>array(),'arena'=>array());
        $tdata=$this->_getTrialInfo($id);
        $cdata=$this->_getCarbon($id);
        $mdata=$this->_getMount($id);
        $fdata=$this->_getFormation($id);
        $sdata=$this->_getSkill($id);
        $counts=$this->_getArenaCount($id);
        $servers=$this->get_server_config();
        $server=$servers[$_GET['sid']];
        $adata=$this->_getArena($server['GIP'],$server['GPort'],$id);
        $adata['count']=isset($counts[7])?$counts[7]:0;
        $tdata&&$data['trial']=$tdata;
        $cdata&&$data['carbon']=$cdata;
        $mdata&&$data['mount']=$mdata;
        $fdata&&$data['form']=$fdata;
        $sdata&&$data['skill']=$sdata;
        $adata&&$data['arena']=$adata;
        if(is_array($data))
        {
            echo json_encode($data,JSON_FORCE_OBJECT);
        }else{
            echo json_encode(array('code'=>1,'msg'=>'该角色不存在'));
        }
    }
    public  function getInfo()
    {
	$time=array('logout_time','create_time','firstapretime','firststaminaretime','lastchattime','secondapretime','secondstaminaretime');
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        $id=$_GET['id'];
	$nationid=$redis->hGet('camp:roles',$id);
	if(empty($nationid)||!isset($nationid)){$nationid=0;}
        if(!strstr($id, 'RoleInfo:'))
        {
            $id='RoleInfo:'.$id;
        }
        $data=$redis->get($id);
        $data=(unpack($this->ufmt, $data));
        //print_r($data);
	foreach($data as $k=>$v){
	  if(in_array($k,$time)&&$v!=0){
		$data[$k]=date('Y-m-d H:i:s',$v);
	  }
	}
	$data['nationid']=$nationid;
        if(is_array($data))
        {
            echo json_encode($data);
        }else{
            echo json_encode(array('code'=>1,'msg'=>'该角色不存在'));
        }
    }
    
    public  function updateInfo()
    {
        $time=array('logout_time','create_time','firstapretime','firststaminaretime','lastchattime','secondapretime','secondstaminaretime');
	foreach($_POST as $kk=>$vv){
          if(in_array($kk,$time)&&$vv!=0){
                $_POST[$kk]=strtotime($v);
          }
        } 
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
         $ret=array('error'=>0,'code'=>0,'msg'=>'');
         $up_field=array();
        if(!empty($_POST['uid'])&&!empty($_GET['sid']))
        {
            $key='RoleInfo:'.$_POST['uid'];
            $data=$redis->get($key);
            $data=unpack($this->ufmt, $data);
            $alter=false;
            
            foreach ($data as $k=>&$v) 
            {
                if($k=='card')continue;
                if(!isset($this->filter[$k]))continue;
                if(isset($_POST[$k])&&$v!=$_POST[$k])
                {
                    $up_field[]=array('k'=>$k,'t'=>$this->filter[$k],'v'=>array($v,$_POST[$k]));
                    $v=$_POST[$k];
                    $alter=true;
                   
                }
            }
            
            if($alter)
            {
                $servers=$this->get_server_config();
                $server=$servers[$_GET['sid']];
                if(isset($server['GIP'])&&isset($server['GPort']))
                {
//                     $data=array_values($data);
//                    array_unshift($data, $this->pfmt);
//                    $dt=call_user_func_array('pack',$data);
                    
                    foreach ($up_field as $k1=>$v1) 
                    {
                        $this->modify_attr($server['GIP'],$server['GPort'],$_POST['uid'], $this->filter[$v1['k']], $v1['v'][1]-$v1['v'][0]);
                    }
                    self::manage_log();
                    $ret['msg']='更新成功';
                }else{
                    $ret['msg']='没有服务器配置信息无法更新';
                }
            }else{
                $ret['msg']='不需要更新';
                $ret['error']=1;
                $ret['code']=1;
            }
            
        }else{
             $ret['msg']='没有提交数据';
        }
        $ret[]=$up_field;
//        $this->send_cmd($_POST['uid']);
        echo json_encode($ret);
    }
    
    public function kickOut()
    {
        $ret=array('error'=>0,'code'=>0,'msg'=>'');
        if($_POST['uid'])
        {
            $servers=$this->get_server_config();
            $server=$servers[$_POST['sid']];
            if(isset($server['GIP'])&&isset($server['GPort']))
            {
                 $this->kick_out($server['GIP'], $server['GPort'], $_POST['uid']);
                 $ret['msg']='踢人成功';
                 self::manage_log();
            }else {
                  $ret['msg']='没有服务器配置信息无法更新';
            }
          
        }else{
            $ret['error']=1;
            $ret['code']=1;
            $ret['msg']='没有提交数据';
        }
         
    }
    
    
    public function updateCarbon()
    {
        $ret=array('error'=>0,'code'=>0,'msg'=>'');
        if($_REQUEST['data']&&isset($_REQUEST['id']))
        {
            $servers=$this->get_server_config();
            $server=$servers[$_REQUEST['sid']];
            if(isset($server['GIP'])&&isset($server['GPort']))
            {
                foreach ($_REQUEST['data'] as $k=>$v)
                {
                    $arr=explode('-', $v['chapter']);
                    $chapter=$arr[0];
                    $scene=$arr[1];
                    $this->_updateCarbon($server['GIP'], $server['GPort'], $_REQUEST['id'], $v['line'], $chapter, $scene);
                    //break;
                }
                 $ret['msg']='修改关卡成功';
                 self::manage_log();
            }else{
                $ret['code']=1;
                $ret['msg']='没有服务器配置信息无法更新';
            }
        }  else{
            $ret['error']=1;
            $ret['code']=1;
            $ret['msg']='没有提交数据';
        }
        
        echo json_encode($ret);
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
    //modify attr
    private function add_gift($ip,$port,$uid,$type,$tid,$count)
    {
        $pkfmt='IISSIII';
        $errno=0;
        $errstr='';
        $timeout=5;
        $dt=pack($pkfmt,24,0xF1E2D3C4,0x01,0xDDF0,$uid,$tid,$count);
        $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
        if($sock)
        {
            fwrite($sock, $dt,24);
        }
    }
    // _updateCarbon
    private function _updateCarbon($ip,$port,$uid,$line,$chapter,$scene)
    {
        $pkfmt='IISSIIIIII';
        $errno=0;
        $errstr='';
        $timeout=5;
        $dt=pack($pkfmt,36,0xF1E2D3C4,0x01,0xDDF6,$uid,0,0,$line,$chapter,$scene);
        $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
        if($sock)
        {
            fwrite($sock, $dt,36);
        }
    }
    
    //_getArena
    private function _getArena($ip,$port,$uid)
    {
        $pkfmt='IISSIII';
         $upkfmt='Isize/Imagic/Stype/Scmd/Ieno/Ipf/Ips/Izone/Irank';
        $errno=0;
        $errstr='';
        $timeout=5;
        $dt=pack($pkfmt,24,0xF1E2D3C4,0x01,0xDDF3,$uid,0,0);
        $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
        if($sock)
        {
            fwrite($sock, $dt,24);
            $count=50;
            $i=0;
            while($i++<$count)
            {
                $rdata=fread($sock, 1024);
                if($rdata)
                {
                    $data=unpack($upkfmt, $rdata);
                    if($data['size']>0)
                    {
                        return array('zone'=>$data['zone'],'rank'=>$data['rank']);
                    }
                    break;
                }
            }
        }
        return array('zone'=>0,'rank'=>0);
    }
    
    
    //_getTrialInfo
    private function _getTrialInfo($id)
    {
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        $upkfmt='Inum/IunCurrentPoint/IunProcessPointCount/IunLevel/IunTrialsTime/IunCurrentBoxID/IunBoxState';
        $idtrial='RoleDataBuf:'.$id;;
        $rtriadata=$redis->hget($idtrial,28);
        $triadata=array();
        if($rtriadata)
        {
            $triadata=unpack($upkfmt, $rtriadata);
        }
        
        return $triadata;
    }
    
    //_getArenaCount
    private function _getArenaCount($id)
    {
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        $upkfmt='It1/It2/Slen';
        $upfmt='Itype/Ivalue';
        $idcount='RoleDataBuf:'.$id;;
        $rcdata=$redis->hget($idcount,31);
        $cdata=unpack($upkfmt, $rcdata);
        $counts=array();
        if(isset($cdata['len'])&&$cdata['len']>0)
        {
            $rcdata=substr($rcdata, 10);
            for($i=0;$i<$cdata['len'];$i++)
            { 
                 $count=unpack($upfmt,$rcdata);
                 if(is_array($count))
                 {
                     $counts[$count['type']]=$count['value'];
                 }
                 $rcdata=substr($rcdata, 8);
                 
            }
        }

        return $counts;
    }
    //_getCarbon
    private function _getCarbon($id)
    {
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        $carbons=array();
        $upkfmt='Ichapter_id/Iscene_id/Cplot';
        $upklinefmt='Iline';
        $idcarbon='Module:'.$id.':carbon:0';
        $rcarbondata=$redis->hgetall($idcarbon);
        foreach ($rcarbondata as $k=>$value) {
              $carbon=unpack($upkfmt, $value);
              $line=unpack($upklinefmt, $k);
              if(is_array($carbon)&&is_array($line))
              {
                  $carbons[$line['line']]=$carbon;
              }
        }
        return $carbons;
    }

    //_getMount
    private function _getMount($id)
    {
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
      
        $upkfmt='Itid/Igrow/C6';
        $idmount='Module:'.$id.':mount:0';
        $id=pack('I',$id);
        $rmount=$redis->hget($idmount,$id);
        $mount=unpack($upkfmt, $rmount);
        return $mount;
    }
    //_getFormation
    private function _getFormation($id)
    {
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        $heroConfig=$this->_getHeroConfig();
        $upkfmt='I6';
        $idform='role:'.$id.':forms';
        $rform=$redis->hgetall($idform);
        $forms=array();
       
        foreach ($rform  as $k=>$v)
        {
            $form=unpack($upkfmt, $v);
            if(is_array($form))
            {
                foreach ($form as $k=>&$v) 
                {
                    if(isset($heroConfig[$v]))
                    {
                        $v.='['.$heroConfig[$v].']';
                    }
                }
                $forms[$k]=$form;
             
            }
        }
        return $forms;
    }
    //_getSkill
    private function _getSkill($id)
    {
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        $idskill='Module:'.$id.':skill:0';
        $rskill=$redis->hgetall($idskill);
        $skills=array();
        foreach ($rskill  as $k=>$v)
        {
            $skill=unpack('Cpos/Clevel', $v);
            $k=ord($k);
            if(is_array($skill))
            {
                $skills[$k]=$skill;
            }
        }
        $equipeds=array();
        $idequiped='Module:'.$id.':skill:1';
        $requiped=$redis->hgetall($idequiped);
        foreach ($requiped as $k=>$v) 
        {
            $equiped=unpack('Csid', $v);
              $k=ord($k);
            if(is_array($equiped))
            {
                $equipeds[$k]=$equiped;
            }
        }
        return array('skills'=>$skills,'equiped'=>$equipeds);
    }
    
    private function _getHeroConfig()
    {
         $hero=simplexml_load_file(PHPCMS_PATH.'/statics/config/Hero.xml');
         $lang=simplexml_load_file(PHPCMS_PATH.'/statics/config/Language.xml');
         $config=array();
         $lang_config=array();
        foreach ($lang as $vl) {
            $lang_config[(string)$vl['Key']]=(string)$vl['Value'];
        }
        foreach ($hero as $vl) { 
            
            $config[(string)$vl['HeroID']]=$lang_config[(string)$vl['NameID']];//(string)$vl['Name'];
        }
        return $config;
    }
    
    
}
