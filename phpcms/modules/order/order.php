<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('form', '', 0);

class order extends admin
{
    /**
     * @var Redis
     */
    private $_redis;

    /**
     * @var orders_list_model
     */
    private $_order_list;
    /**
     * @var orders_model
     */
    private $_orders;
    /**
     * @var account_model
     */
    private $_account;

    public function __construct()
    {
        $this->_orders = pc_base::load_model('orders_model');
        $this->_order_list = pc_base::load_model('orders_list_model');
        $this->_account = pc_base::load_model('account_model');
        $this->db = pc_base::load_model('admin_role_model');
    }

    public function index()
    {
        include $this->admin_tpl('order', 'order');
    }

    public function account()
    {
        include $this->admin_tpl('account', 'order');
    }

    public function loadChannels()
    {
        $pay = pc_base::load_config('pay');
        $roleid=$_SESSION['roleid'];
        $platform=$this->db->get_one('`roleid`='.$roleid,'`platform`');
        $platform['platform']=str_replace(' ','',$platform['platform']);
        $platform['platform']=str_replace('，',',',$platform['platform']);
        $platformArray=explode(",",$platform['platform']);
        if(isset($roleid) && !empty($roleid) && $roleid!=1 && $platform['platform']!=''){
            foreach ($pay as $k=>$v){
                if(!in_array($k,$platformArray)){
                    unset($pay[$k]);
                }
            }
        }
        echo json_encode($pay,JSON_UNESCAPED_UNICODE);
    }

    public function pay_order_list()
    {
        $platform = pc_base::load_config('ltv');
        unset($platform['A']);
        $servers = $this->get_server_config();
        include $this->admin_tpl('pay_list', 'order');
    }

    /*充值排行榜查询*/
    public function search_pay_list()
    {
        $st = isset($_POST['st']) ? $_POST['st'] : 0;
        $ed = isset($_POST['ed']) ? $_POST['ed'] : 0;
        $ch = isset($_POST['ch']) ? $_POST['ch'] : 0;
        $sid = isset($_POST['sid']) ? $_POST['sid'] : 0;
        $ret = array('ret' => 0, 'msg' => '查询为空', 'list' => array());
        $servers = array();
        if ($sid == 0) {
            $servers = $this->get_server_config();
        } else {
            $servers['0']['ServerType'] = $sid;
        }
        foreach ($servers as $value) {
            $sid = $value['ServerType'];
            $host = pc_base::load_config('database');
            $roleinfo = $host['roleinfo'];
            $port = $roleinfo['port'] + $sid;
            $database = $roleinfo['database'] . $sid;
            $sql = sprintf("select t1.area,t1.roleid,t2.AccountName,t2.PlayerName,sum(t1.count) as money,t3.level,t3.viplevel,t3.diamond,t3.gold,t3.time from db_charge_s%s.orders as t1 left join GameDB_s%s.TAccountData as t2 on t1.roleid=t2.ID left join GameDB_s%s.Roleinfo as t3 on t1.roleid=t3.roleid where t1.time > '%s' and t1.time < '%s' and t2.AccountName like '%s' group by t1.roleid order by money desc limit 30", $sid, $sid, $sid, $st, $ed, $ch . '%');
            $conn = mysql_connect($roleinfo['hostname'] . ':' . $port, $roleinfo['username'], $roleinfo['password']); //连接数据库
            if (!$conn) {
                continue;
            }
            mysql_query("set names 'utf8'"); //数据库输出编码 .
            //mysql_select_db($database);
            $result = mysql_query($sql); //查询
            if ($result) {
                $ret['ret'] = 1;
            }
            while ($row = mysql_fetch_array($result)) {
                $row['serverid'] = $sid;
                $ret['list'][] = $row;
            }
        }
        foreach ($ret['list'] as $k => $v) {
            $money[$k] = $v['money'];
        }
        array_multisort($money, SORT_DESC, $ret['list']);
        echo json_encode($ret);
    }

    public function findInfo($uid, $server)
    {
        $servers = $this->get_server_config();
        $host = pc_base::load_config('database');
        $roleinfo = $host['roleinfo'];
        $port = $roleinfo['port'] + $server;
        $database = $roleinfo['database'] . $server;
        $conn = mysql_connect($roleinfo['hostname'] . ':' . $port, $roleinfo['username'], $roleinfo['password']); //连接数据库
        if ($conn) {
            $conn = array();
            return $conn;
        }
        mysql_query("set names 'utf8'"); //数据库输出编码 .
        mysql_select_db($database);
        $sql = sprintf("select *  from Roleinfo where roleid=%s", $uid); //SQL语句
        $result = mysql_query($sql); //查询
        $row = mysql_fetch_array($result);
        return $row;
    }

    public function server_account()
    {
        $st = isset($_POST['st']) ? $_POST['st'] : 0;
        $ed = isset($_POST['ed']) ? $_POST['ed'] : 0;
        $ch = isset($_POST['ch']) ? $_POST['ch'] : 0;
        $sid = isset($_POST['sid']) ? $_POST['sid'] : array();
        $ret = array('code' => 0, 'msg' => 'ok', 'result' => array(), 'total' => array());
        $pay = pc_base::load_config('pay');
        if ($st && $ed && $ch && is_array($sid) && count($sid)) {
            $this->_orders->changeConnection($ch);
            $sqls = array();
            $exists = array();
            foreach ($sid as $k => $v) {
                $otable = $this->_orders->getTable();
                $ch_1 = $pay[$ch]['table'];
                $ntable = $otable . '_' . $ch_1 . '_s' . $v;
                !isset($ret['result'][$v]) && $ret['result'][$v] = array('sid' => $v, 'c_amount' => 0, 'c_count' => 0, 'r_count' => 0);
                if (!$this->_orders->table_exists($ntable)) {
                    continue;
                }
                $exists[$v] = 1;
                $sql_count = sprintf("select sum(amount) as c_amount ,count(id) as c_count from %s where time between '%s' and '%s'", $ntable, $st, $ed);
                $sql_roles = sprintf("select count(uid) as r_count from (select uid  from %s where time between '%s' and '%s' group by uid) as xx", $ntable, $st, $ed);
                $sqls[] = $sql_count;
                $sqls[] = $sql_roles;
            }
            $this->_orders->muti_query($sqls);
            $results = $this->_orders->muti_results();
            $index = 0;
            foreach ($sid as $k => $v) {
                if (isset($exists[$v])) {
                    $ret['result'][$v] = array_merge($ret['result'][$v], $results[$index++][0]);
                    $ret['result'][$v]['r_count'] = $results[$index++][0]['r_count'];
                } else {
                    $ret['result'][$v] = array('sid' => $v, 'r_count' => 0, 'c_amount' => 0, 'c_count' => 0);
                }
            }
            if (count($ret['result'])) {
                foreach ($ret['result'] as $k => &$vx) {
                    foreach ($vx as $k1 => &$v1) {
                        if (is_null($v1)) {
                            $v1 = 0;
                        }
                        $ret['total'][$k1] += $v1;
                    }

                }
            }
        } else {
            $ret['code'] = 1;
            $ret['lost_params'] = 1;
        }
        echo json_encode($ret);
    }

    public function server_account_all()
    {
        $st = isset($_POST['st']) ? $_POST['st'] : 0;
        $ed = isset($_POST['ed']) ? $_POST['ed'] : 0;
        $ch = isset($_POST['ch']) ? $_POST['ch'] : 0;
        $sid = isset($_POST['sid']) ? $_POST['sid'] : array();
        $ret = array('code' => 0, 'msg' => 'ok', 'result' => array(), 'total' => array());
        $pay = pc_base::load_config('pay');
        if ($st && $ed && $ch && is_array($sid) && count($sid) && is_array($ch) && count($ch)) {
            foreach ($ch as $kc => $vc) {
                $this->_orders->changeConnection($vc);
                $sqls = array();
                $exists = array();
                foreach ($sid as $k => $v) {
                    !isset($ret['result'][$v]) && $ret['result'][$v] = array('sid' => $v, 'c_amount' => 0, 'c_count' => 0, 'r_count' => 0);
                    $otable = $this->_orders->getTable();
                    //fix bug 极品百度与极品华为
                    if (preg_match('/^jp/', $vc)) {
                        $vc = str_replace('jp', '', $vc);
                    }
                    $vc_1 = $pay[$vc]['table'];
                    //$vc=$pay[$vc]['table'];
                    $ntable = $otable . '_' . $vc_1 . '_s' . $v;
                    if (!$this->_orders->table_exists($ntable)) {
                        continue;
                    }
                    $exists[$v] = 1;
                    $sql_count = sprintf("select round(sum(amount),2) as c_amount ,count(id) as c_count from %s where time between '%s' and '%s'", $ntable, $st, $ed);
                    $sql_roles = sprintf("select count(uid) as r_count from (select uid  from %s where time between '%s' and '%s' group by uid) as xx", $ntable, $st, $ed);
                    $sqls[] = $sql_count;
                    $sqls[] = $sql_roles;

//                      if($this->_orders->query($sql_count))
//                      {
//                          $result=$this->_orders->fetch_array();

//                          if(isset($result[0])&&is_array($result[0]))
//                          {
//                              $ret['result'][$v]['c_amount']+=$result[0]['c_amount'];
//                              $ret['result'][$v]['c_count']+=$result[0]['c_count'];
//                          }
//                      }
//                      if($this->_orders->query($sql_roles))
//                      {
//                          $result=$this->_orders->fetch_array();
//                          if(isset($result[0])&&is_array($result[0]))
//                          {
//                              $ret['result'][$v]['r_count']+=$result[0]['r_count'];
//                          }
//                      }

                }
                $this->_orders->muti_query($sqls);
                $results = $this->_orders->muti_results();
                $index = 0;
                foreach ($sid as $k => $v) {
                    if (isset($exists[$v])) {
                        $ret['result'][$v]['c_amount'] += $results[$index][0]['c_amount'];
                        $ret['result'][$v]['c_count'] += $results[$index++][0]['c_count'];
                        $ret['result'][$v]['r_count'] += $results[$index++][0]['r_count'];
                    }
                }
                /*各渠道分开统计*/
                $i = 0;
                foreach ($sid as $k => $v) {
                    if (isset($exists[$v])) {
                        $ret['result_all'][$vc]['platform'] = $vc;
                        $ret['result_all'][$vc]['c_amount'] += $results[$i][0]['c_amount'];
                        $ret['result_all'][$vc]['c_count'] += $results[$i++][0]['c_count'];
                        $ret['result_all'][$vc]['r_count'] += $results[$i++][0]['r_count'];
                    }
                    if ($ret['result_all'][$vc][$v]['c_amount'] == 'null') {
                        unset($ret['result_all'][$vc][$v]);
                    }
                }
#		 if(empty($ret['result_all'][$vc])){
#                        unset($ret['result_all'][$vc]);
#                 }
            }
            if (count($ret['result'])) {
                foreach ($ret['result'] as $k => &$vx) {
                    foreach ($vx as $k1 => &$v1) {
                        if (is_null($v1)) {
                            $v1 = 0;
                        }
                        $ret['total'][$k1] += $v1;
                    }

                }
            }
            unset($ret['total']['sid']);
        } else {
            $ret['code'] = 1;
            $ret['lost_params'] = 1;
        }
        echo json_encode($ret);
    }

    public function query()
    {
        $ret = array('ret' => 0, 'msg' => '', 'test' => '');
        if (isset($_POST['channel']) && isset($_POST['server'])) {
            $this->_orders->changeConnection($_POST['channel']);
            $ch = $_POST['channel'];
            //fix bug 极品百度与极品华为
            if (preg_match('/^jp/', $ch)) {
                $ch = str_replace('jp', '', $ch);
            }
            if (isset($_POST['uid'])) {
                $_POST['server'] = (int)substr($_POST['uid'], 1, 2);
            }

            $pay = pc_base::load_config('pay');
            $ch = $pay[$ch]['table'];
            $table = 'orders_' . $ch . '_s' . $_POST['server'];
            if ($this->_orders->table_exists($table)) {
                $otable = $this->_orders->getTable();
                $this->_orders->setTable($table);
                $condition = array();
                if (isset($_POST['account'])) {
                    $condition['account'] = $_POST['account'];
                }
                if (isset($_POST['uid'])) {
                    $condition['uid'] = $_POST['uid'];
                }
                if (isset($_POST['order_id'])) {
                    $condition['orderid'] = $_POST['order_id'];
                }

                $res = $this->_orders->select($condition);
                $ret['result'] = $res;
                if (!count($res)) {
                    //查询失败的订单
//                      $otable=$this->_orders->getTable();
                    $this->_orders->setTable($otable);
                    $res = $this->_orders->select($condition);
                    $ret['result'] = $res;
                    if (!count($res)) {
                        $ret = array('ret' => 3, 'msg' => '查询记录为空');
                    }
                }
                $keys = array('id', 'channel', 'serverid', 'uid', 'orderid', 'billno', 'amount', 'time', 'status', 'ts', 'product_id', 'itemid');
                if (isset($ret['result']) && count($ret['result'])) {
                    foreach ($ret['result'] as $k => &$v) {
                        foreach ($v as $k1 => &$v1) {
                            if (!in_array($k1, $keys)) {
                                unset($v[$k1]);
                            }
                            if ($k1 == 'ts') {
                                $v1 = date('Y-m-d H:i:s', $v1);
                            }
                        }
                    }
                }
                $this->_order_list->changeConnection($_POST['channel']);
                $his_orders = $this->_order_list->select($condition, '*', 10000, 'ctime');
                $ret['his_orders'] = $his_orders;
            } else {
                $ret = array('ret' => 2, 'msg' => '该渠道还没有玩家充值');
            }
        } else {
            $ret = array('ret' => 1, 'msg' => '参数错误');
        }
        echo json_encode($ret);
    }

    public function getInfo($id, $sid)
    {
        $server = $this->getRedisConfig_1($sid);
        $redis = $this->getRedis($server['RIP'], $server['RIndex'], $server['RPort']);
        if (!strstr($id, 'RoleInfo:')) {
            $id = 'RoleInfo:' . $id;
        }
        $data = $redis->get($id);
        $ufmt = "Iuid/a65acount/a65name/Ipic_id/Csex/Iexp/Clevel/Ivipexp/Cviplevel/Ilogout_time/igold/idiamond/" .
            "Sap/Sapnum/Sapbuynum/Sapmax/Sstamina/Sstamina_max/Igong/Ihonor/Smapid/crace/Icreate_time/a348card/cgmlevel/" .
            "Ijade/Ifirstapretime/Isecondapretime/Ifirststaminaretime/Isecondstaminaretime/Iapfrompillvalue/Istaminafrompillvalue/" .
            "Isoul/Iapfriend/Ilastchattime/Irelive/Iexploit/Ibattlepower/Ifodder/Inationcontribute/Ileagueid/Inationid/Iscore/Icar/Iflag" .
            "/IstaminaBuyCount/IapBuy/IstaminaBuy/IapBuyToday/IstaminaBuyToday/ImainHeroId";
        $data = (unpack($ufmt, $data));
        return $data;
    }

    protected function getRedisConfig_1($sid)
    {
        //$_SESSION['sid']=isset($_SESSION['sid'])?$_SESSION['sid']:1;
        $sid = empty($sid) ? $_SESSION['sid'] : $sid;
        $servers = $this->get_server_config();
        $server = array();
        if (isset($servers[$sid])) {
            $server = $servers[$sid];
        } else if (count($servers)) {
            $skeys = array_keys($servers);
            $server = $servers[$skeys[0]];
        }
        if (!count($server)) {
            exit('NO ACCESS');
        }
        return $server;
    }


}
