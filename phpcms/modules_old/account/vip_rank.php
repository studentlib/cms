<?php
ini_set("max_execution_time",1800);
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
ini_set('html_errors','1');
error_reporting(E_ALL);
class vip_rank extends admin {
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
    
//    }
    public  function admin()
    {
        $servers=$this->get_server_config();
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        $keys=$redis->keys('RoleInfo:*');
        sort($keys);
        $count=count($keys);
        for($i=0;$i<$count;$i++){
        $data=$redis->get($keys[$i]);
        $data=(unpack($this->ufmt, $data)); 
            $num[]=$data['viplevel'];
        }
            $num[0]=$server["text"];
            $num[1]=$server["id"];
        //$level[]=$num;
        //    var_dump($num);
        // echo $redis->dbsize();//返回当前数据库的总记录条数
        
        include   $this->admin_tpl('vip','account');
    }
   
    public  function search()
    {
        $post=$_POST;//var_dump($post);die;
        $servers=$this->get_server_config();
        if(empty($post)){$post['1']="on";}
        foreach($post as $k=>$v){
            $redis=$this->getRedis($servers[$k]['RIP'], $servers[$k]['RIndex'],$servers[$k]['RPort']);
            $keys=$redis->keys('RoleInfo:*');
            sort($keys);
            $count=count($keys);
            $num=array();
            $num[0]=$servers[$k]["text"];
            $num[1]=$servers[$k]["id"];
            for($i=0;$i<$count;$i++){
                $data=$redis->get($keys[$i]);
                $data=(unpack($this->ufmt, $data));
                $num[]=$data['viplevel'];
            }
            $level[]=$num;
        }
        //var_dump($level);
        include   $this->admin_tpl('vip','account');
    }


}