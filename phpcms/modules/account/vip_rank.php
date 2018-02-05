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
    /**
     * @var account_model
     */
    protected $_roleinfo; 
    public function __construct() {
	$this->_roleinfo=pc_base::load_model('roleinfo_model');
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
            'vipexp'=>9,//'viplevel'=>10,
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
        // $server=$this->getRedisConfig();
        // $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        // $keys=$redis->keys('RoleInfo:*');
        // sort($keys);
        // $count=count($keys);
        // for($i=0;$i<$count;$i++){
        // $data=$redis->get($keys[$i]);
        // $data=(unpack($this->ufmt, $data)); 
        //     $num[]=$data['viplevel'];
        // }
        //     $num[0]=$server["text"];
        //     $num[1]=$server["id"];
        //$level[]=$num;
        //    var_dump($num);
        // echo $redis->dbsize();//返回当前数据库的总记录条数
        
        include   $this->admin_tpl('vip','account');
    }
   
    public  function search()
    {
/*        $post=$_POST;//var_dump($post);die;
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
*/	$post=$_POST;
	$servers=$this->get_server_config();
	$host = pc_base::load_config('database');
	if(empty($post)){$post['1']="on";}
	
        foreach($post as $k=>$v){
        	$port=$host['roleinfo']['port']+$k;
		$database=$host['roleinfo']['database'].$k;
        	$conn=mysql_connect($host['roleinfo']['hostname'].':'.$port,$host['roleinfo']['username'],$host['roleinfo']['password']);// or die("error connecting") ; //连接数据库
		if(!$conn){continue;}
		mysql_query("set names 'utf8'"); //数据库输出编码 .
        	mysql_select_db($database);
		$level[$k][0]=$servers[$k]["text"];
            	$level[$k][1]=$servers[$k]["id"];
		$y=0;
		$x=18;
        	for($x ;$x>=$y;$y++){
                	$sql =sprintf("select count(roleid) as count from Roleinfo where `viplevel`='%s'",$y); //SQL语句 
                	$result = mysql_query($sql); //查询
               		$row = mysql_fetch_array($result);
                	$level[$k][]=$row['count'];
        	}
	} 
        include   $this->admin_tpl('vip','account');
    }


}
