<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
ini_set('html_errors','1');
error_reporting(E_ALL);
class hero extends admin {
    /**
     * @var Redis
     */
    private $_redis;
    private $ufmt;
    private $pfmt;
    private $filter;
    private $pnames;
    public function __construct() {
        $this->pnames=array(
            4=>'生命',
            5=>'怒气',
            6=>'物理攻击',
            7=>'物理防御',
            8=>'法术攻击',
            9=>'法术防御',
            10=>'命中',
            11=>'闪避',
            12=>'暴击',
            13=>'抗暴',
            14=>'反击',
            15=>'破挡',
            16=>'治愈加成',
            17=>'吸血',
            18=>'伤害附加',
            19=>'暴击伤害提高',
            20=>'反弹伤害',
            21=>'伤害减免',
            22=>'暴击伤害减免',
        );
    }   
    public function index()
    {
        $servers=$this->get_server_config();
        $keys=$this->getRedisKeys(50);
        $config=$this->get_config();
        include   $this->admin_tpl('hero','hero');
    }
    public function getHeros()
    {
        if(isset($_REQUEST['id']))
        {
            $server=$this->getRedisConfig();
            $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
            $id=$_REQUEST['id'];
            $key="role:$id:heros"; 
            $list=$redis->hgetall($key);
            $heroConfig=$this->_getHeroConfig();
            foreach ($list as $k=>&$v)
            {
                $v=$heroConfig[$k];
            }
            echo json_encode($list,JSON_FORCE_OBJECT);
        }
    }
    public function getHeroInfo()
    {
        if(isset($_REQUEST['id'])&&isset($_REQUEST['hid'])&&$_REQUEST['hid']!='null')
        {
            $id=$_REQUEST['id'];
            $hid=$_REQUEST['hid'];
	    $server=$this->getRedisConfig();
            $hero=$this->__decode_hero($id,$hid);//var_dump($hero);
            $props=$this->getHeroProps($server['GIP'], $server['GPort'], $id, $hid);
            if(count($props))
            {
                foreach ($props as $k=>&$value) 
                {
                    if(isset($this->pnames[$value['id']]))
                    {
                        $value['name']=$this->pnames[$value['id']];
                    }else{
                        $pid=intval($value['id']/100);
                        if($value['id']%2!=0)
                        {
                            $props[$pid]['value']+=$value['value'];
                        }else{
                            
                        }
                        unset($props[$k]);
                    }
                }
            }
            $hero['props']=$props;
            echo json_encode($hero);
        }
    }
    
    
    
    private function __decode_hero($id,$hid)
    {
        //$hfmt='Itid/Iexp/Igift_id/IquenchCost/Slevel/Sgrade/Selen';
        $hfmt='Itid/Iexp/IquenchCost/Slevel/Ievolveid/Italentlv/Italentexp/Iawakelv';//talentlv  天赋等级  Iawakelv：觉醒等级 
	$hfmt0='Itid/Sstar/Ifid/Scount/S*4para';//tid 武将id  star 星级  fid 消耗卡片id count 消耗卡片数量
	$efmt='Ipart/Itid/Ilevel/Cqlen';
        $qfmt='Iid/Ipercent';
        $slenfmt='Sslen';

	$server=$this->getRedisConfig();//var_dump($server);
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
	$key="role:$id:heros";
        $buf=$redis->hget($key,$hid);
        $hero=unpack($hfmt,$buf);//武将基础信息
	
	$hid0=pack('I',$hid);
        $key="Module:$id:hero:0";
        $buf0=$redis->hGet($key,$hid0);
        $hero0=unpack($hfmt0,$buf0);

	$hero['star']=$hero0['star'];
	$hero['fid']=$hero0['fid'];
	$hero['count']=$hero0['count'];
        $econfig=$this->_getEquipConfig();
        return $hero;
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
    
    
    private function _getEquipConfig()
    {
        $equips=simplexml_load_file(PHPCMS_PATH.'/statics/config/Equip.xml');
        $lang=simplexml_load_file(PHPCMS_PATH.'/statics/config/Language.xml');
        $config=array();
        $lang_config=array();
        foreach ($lang as $vl) {
            $lang_config[(string)$vl['Key']]=(string)$vl['Value'];
        }
        foreach ($equips as $k=>$v) 
        {
            if(isset($v['ID'])&&isset($v['NameID']))
            {
                $config[(string)$v['ID']]=array('id'=>(string)$v['ID'],'name'=>$lang_config[(string)$v['NameID']]);
            }
        }
        return $config;
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
    
    //getHeroProps
    private function getHeroProps($ip,$port,$uid,$tid)
    {
        $pkfmt='IISSIII';
        $upkfmt='Isize/Imagic/Stype/Scmd/Ieno/Ipf/Ips/Slen';
        $errno=0;
        $errstr='';
        $timeout=5;
        $data=array('props'=>array());
        $dt=pack($pkfmt,24,0xF1E2D3C4,0x01,0xDDF1,$uid,$tid,0);
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
                    $data=unpack($upkfmt, $rdata);//var_dump($data);
                    if($data['len']>0)
                    {
                        $rdata=substr($rdata, 26);
                        $upfmt='Sid/Ivalue';
                        for($i=0;$i<$data['len'];++$i)
                        {
                            $prop=unpack($upfmt, $rdata);
                            $rdata=substr($rdata, 6);
                            if(count($prop))
                            {
                                $data['props'][$prop['id']]=$prop;
                            }
                        }
                    }
                    break;
                }
            }
        }
        return $data['props'];
    }
    
  
}
