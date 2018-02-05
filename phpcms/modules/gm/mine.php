<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
ini_set('html_errors','1');
error_reporting(E_ALL);
class mine extends admin {
    /**
     * @var Redis
     */
    private $_redis;
    private $ufmtinfo;
    private $ufmtmine;
    private $ufdefrecord;
    private $filter;
    
    public function __construct() {
          $this->ufmtinfo='Iuid/Ilv/Iscore/Imatch/Iend/Istate/Imines';
          $this->ufmtmine='Imid/Iid/Iuid/Itype/Ilast_item_time/Ispeed_end_time/Idefend_form_id/Idefend_skill_id/Ilast_reward_time/Ibe_rob_item_count/Ibe_rob_amount';
          $this->ufdefrecord='Iaid/Iauid/Imid/Iresult/Icampid/Irtime/SlLen/a32lname/Salen/a32aname/I6currency/I6item';
    }   
    
    public function index()
    {
        $servers=$this->get_server_config();
        $keys=$this->getRedisKeys(50);
        include   $this->admin_tpl('mine','gm');
    }
    
    public function getMineInfo()
    {
        $data=array();
        $uid=isset($_REQUEST['id'])?$_REQUEST['id']:0;
        $uid=str_replace('RoleInfo:', '', $uid);
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        $info=$redis->hget('GlobalUserMine',pack('I',$uid));
        if($info)
        {
            $data=unpack($this->ufmtinfo, $info);
            if($data['end'])
            {
                $data['end']=date('Y-m-d H:i:s',$data['end']);
            }
            if(isset($data['mines']))
            {
                $data['detail']=array();
                $data['mines']=$this->count_bit($data['mines']);
                foreach (range(1,$data['mines']) as $k=>$v)
                {
                    $vdetail=$redis->hget('GlobalMines',pack('I',($uid<<2)+$v));
                    if($vdetail)
                    {
                        $detail=unpack($this->ufmtmine, $vdetail);
                        $detail['last_item_time']=date('Y-m-d H:i:s',$detail['last_item_time']);
                        $detail['speed_end_time']=date('Y-m-d H:i:s',$detail['speed_end_time']);
                        $detail['last_reward_time']=date('Y-m-d H:i:s',$detail['last_reward_time']);
                        $data['detail'][]=$detail;
                        
                    }
                }
            }
            $data['record']=array();
            $records=$redis->hgetall("Module:$uid:mine:2");
            if($records)
            {
                foreach ($records as $kr=>$vr)
                {
                    $record=unpack($this->ufdefrecord, $vr);
                    if($record)
                    {
                        $items=array();
                        $currency=array();
                        foreach ($record as $ki=>$vi)
                        {
                            if(strncasecmp($ki, 'item',4)==0)
                            {
                                $items[]=$vi;
                            }
                            if(strncasecmp($ki, 'currency',8)==0)
                            {
                                $currency[]=$vi;
                            }
                        }
                        $record['lname']=trim($record['lname']);
                        $record['aname']=trim($record['aname']);
                        $record['currency']=join(',', $currency);
                        $record['item']=join(',', $items);
                        $record['aname']=trim($record['aname']);
                        $data['record'][]=$record;
                    }
                }
            }
        }
        echo json_encode($data,JSON_FORCE_OBJECT);
    }
    
    private function count_bit($n)
    {
        $count=0;
        while($n)
        {
            if($n&1)
            {
                $count++;
            }
            $n>>=1;
        }
        return $count;
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
    
}