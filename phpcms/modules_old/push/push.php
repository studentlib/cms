<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class push extends admin {
    
    const MAX_ROLEID_MAP_TABLE=10000;
    
    public function __construct() {
    }
    
    
    public function index() {
        $ret=array('error'=>0,'msg'=>'');
        if(isset($_REQUEST['s'])&&isset($_REQUEST['uid'])&&isset($_REQUEST['dev'])&&isset($_REQUEST['channel']))
        {
                $redis=$this->getPushRedis();
                $data=array(
                'uid'=>$_REQUEST['uid'],
                'channel'=>$_REQUEST['channel'],
                'dev'=>$_REQUEST['dev'],
                'time'=>time(),
                'date'=>date('Y-m-d'),
                );
                $mapData=array(
                'uid'=>$_REQUEST['uid'],
                'channel'=>$_REQUEST['channel'],
                );
                $redis->hset($_REQUEST['channel'].":".$_REQUEST['s'],$_REQUEST['uid'],json_encode($data));
                $redis->hset('RoleIdMap:'.(self::times33($_REQUEST['uid'])%self::MAX_ROLEID_MAP_TABLE).":".$_REQUEST['uid'],$_REQUEST['uid'],json_encode($mapData));
        }else{
            $ret['error']=1;
            $ret['msg']='lost_params';
        }
        echo json_encode($ret);
    }
    
    public function push()
    {
         $ret=array('error'=>0,'msg'=>'');
         if(isset($_REQUEST['uid'])&&isset($_REQUEST['type'])&&isset($_REQUEST['sid']))
         {
                $redis=$this->getPushRedis();
                
              
                
                if(is_array($_REQUEST['uid']))
                {
                    $ret['msg']=array();
                    foreach ($_REQUEST['uid'] as $v) 
                    {
                        $channel=$redis->hget('RoleIdMap:'.(self::times33($v)%self::MAX_ROLEID_MAP_TABLE).":".$v,$v);
                        if($channel)
                        {
                            $channel=json_decode($channel,TRUE);
                            $data=array(
                            'type'=>$_REQUEST['type'],
                            'channel'=>$channel['channel'],
                            );
                            $dev=$redis->hget($channel['channel'].":".$_REQUEST['sid'],$v);
                            if($dev)
                            {
                                $json=json_decode($dev,TRUE);
                                $data['dev']=$json['dev'];
                                $redis->rPush('PushQueque',json_encode($data));
                            }else{
                                 $ret['msg'][$v]='no_dev_report';
                            }
                        }
                    }
                }else{
                    $ret['error']=2;
                    $ret['msg']='uid_not_array';
                }
         }else{
            $ret['error']=1;
            $ret['msg']='lost_params';
         }   
        
         echo json_encode($ret);   
    }

   
    
    public function daemon()
    {
        $sid=1;
        $servers=$this->get_server_config();
        $server=$servers[$sid];
        $redis=$this->getRedis($server['RIP'],$server['RIndex']);
        $data=$redis->lRange('PushQueque',0,-1);
        
    }
}
?>