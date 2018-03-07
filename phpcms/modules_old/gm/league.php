<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class league extends admin {
    /**
     * @var model
     */
    private $_db;
    
	public function __construct() {
	   $this->_db=pc_base::load_model('errors_model');
	}
	
	public function index()
	{
	    $servers=$this->get_server_config();
	    $leagues=$this->_getLeagues();
	    include $this->admin_tpl('league','gm');
	}
	
	
	public function getMembers()
	{
	    if(isset($_POST['id']))
	    {
	        $members=$this->_getLeagueMembers($_POST['id']);
	        echo json_encode($members);
	    }
	}
	
	public function setLeader()
	{
	    $ret=array('code'=>0,'msg'=>'');
	    if(isset($_POST['sid'])&&isset($_POST['lid'])&&isset($_POST['mid']))
	    {
	        $servers=$this->get_server_config();
            if(isset($servers[$_POST['sid']]))
            {
                $server=$servers[$_POST['sid']];
                if(isset($server['GIP'])&&isset($server['GPort']))
                {
                    $this->__setLeader($server['GIP'], $server['GPort'], $_POST['lid'], $_POST['mid']);
                    $ret['msg']='设置成功';
                    self::manage_log();
                }
            }else{
                $ret['code']=2;
                $ret['msg']='no_such_server';
            }
	    }else{
	        $ret['code']=1;
	        $ret['msg']='wrong_param';
	    }
	    echo json_encode($ret);
	}
	
	
	public function modify()
	{
	    $ret=array('code'=>0,'msg'=>'');
	    if(isset($_POST['sid'])&&isset($_POST['lid'])&&isset($_POST['content']))
	    {
	        $servers=$this->get_server_config();
	        if(isset($servers[$_POST['sid']]))
	        {
	            $server=$servers[$_POST['sid']];
	            if(isset($server['GIP'])&&isset($server['GPort']))
	            {
	                $league=$this->_getLeague($_POST['lid']);
	                $nleague=$_POST['content'];
	                foreach ($league as $k=>&$v)
	                {
	                    if(isset($nleague[$k])&&$v!=$nleague[$k])
	                    {
	                        $v=$nleague[$k];
	                    }
	                }
	                $this->__modifyLeague($server['GIP'], $server['GPort'], $league);
	                $ret['msg']='修改成功';
	                self::manage_log();
	            }
	        }else{
	            $ret['code']=2;
	            $ret['msg']='no_such_server';
	        }
	    }else{
	        $ret['code']=1;
	        $ret['msg']='wrong_param';
	    }
	    echo json_encode($ret);
	}
	public function delete()
	{
	    $ret=array('code'=>0,'msg'=>'');
	    if(isset($_POST['sid'])&&isset($_POST['lid']))
	    {
	        $servers=$this->get_server_config();
	        if(isset($servers[$_POST['sid']]))
	        {
	            $server=$servers[$_POST['sid']];
	            if(isset($server['GIP'])&&isset($server['GPort']))
	            {
	                $this->__deleteLeague($server['GIP'], $server['GPort'], $_POST['lid']);
	                $ret['msg']='删除成功';
	                self::manage_log();
	            }
	        }else{
	            $ret['code']=2;
	            $ret['msg']='no_such_server';
	        }
	    }else{
	        $ret['code']=1;
	        $ret['msg']='wrong_param';
	    }
	    echo json_encode($ret);
	}
	
	private function __modifyLeague($ip,$port,$league)
	{
	   $pklgfmt='IIIIISSIIIIa32a256I5I3I8IIII';
	   $pkfmt='IISSIII';
        $errno=0;
        $errstr='';
        $timeout=1;
       
        $lgdt=pack($pklgfmt,
                   $league['id'],$league['rank'],$league['level'],$league['exp'],$league['auto'],$league['mcount'],
                   $league['maxcount'],$league['money'],$league['leader'],$league['tleader'],$league['ctime'],$league['name'],
                   $league['broad'],$league['flag1'],$league['flag2'],$league['flag3'],$league['flag4'],$league['flag5'],
                   $league['cond1'],$league['cond2'],$league['cond3'],$league['boss1'],$league['boss2'],$league['boss3'],
                   $league['boss4'],$league['boss5'],$league['boss6'],$league['boss7'],$league['boss8'],$league['date'],
                   $league['active'],$league['logout'],0
        );
        $len=strlen($lgdt);
        $dt=pack($pkfmt,24+$len,0xF1E2D3C4,0x01,0xDE00,0,0,0);
        $rdata=$dt.$lgdt;
        $rlen=strlen($rdata);
        $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
        if($sock)
        {
             fwrite($sock, $rdata,$rlen);
             return TRUE;
        }
        return false;
	}
	private function __deleteLeague($ip,$port,$lid)
	{
	   $pkfmt='IISSIIII';
        $errno=0;
        $errstr='';
        $timeout=1;
        $dt=pack($pkfmt,28,0xF1E2D3C4,0x01,0xDE01,0,$lid,0,$lid);
        $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
        if($sock)
        {
            fwrite($sock, $dt,28);
        }
	}
	private function __setLeader($ip,$port,$lid,$mid)
	{
	   $pkfmt='IISSIII';
        $errno=0;
        $errstr='';
        $timeout=1;
        $dt=pack($pkfmt,24,0xF1E2D3C4,0x01,0xDDFE,0,$lid,$mid);
        $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
        if($sock)
        {
            fwrite($sock, $dt,24);
        }
	}
	
	private function _getLeague($lid)
	{
	    $config=$this->getRedisConfig();
	   // $fmt='Iid/Irank/Ilevel/Iexp/Iauto/Smcount/Smaxcount/Imoney/Ileader/Itleader/Ictime/a32name/a256broad/I5flag/I3cond/I8boss/Idate/Iactive/Ilogout';
	    $fmt='Iid/Irank/Ilevel/Sexp/Sauto/Smcount/Smaxcount/Imoney/Ileader/Itleader/Ictime/a32name/a256broad/I5flag/I3cond/I8boss/Idate/Iactive/Ilogout';
	    $redis=$this->getRedis($config['RIP'], $config['RIndex'],$config['RPort']);
	    $league=$redis->hget('League',pack('I',$lid));
	    $data=array();
	    if($league)
	    {
	        $data=unpack($fmt, $league);
	    }
	    return $data;
	    
	}
	
	private function _getLeagues()
	{
	    $config=$this->getRedisConfig();
	    $redis=$this->getRedis($config['RIP'], $config['RIndex'],$config['RPort']);
	    $leagues=$redis->hgetall('League');
	    $fmt='Iid/Irank/Ilevel/Iexp/Iauto/Smcount/Smaxcount/Ileader/Itleader/Ictime/a32name/a256broad/I5flag/I3cond/I8boss/Idate/Iactive/Ilogout'; 
	    //$fmt='Iid/Irank/Ilevel/Iexp/Iauto/Smcount/Smaxcount/Imoney/Ileader/Itleader/Ictime/a32name/a256broad/I5flag/I3cond/I8boss/Idate/Iactive/Ilogout';
	    $dt=array();
	    foreach ($leagues as $value) 
	    {
	        $dt[]=unpack($fmt, $value);
	        
	    }
	    return $dt;
	}
	
	private function _getLeagueMembers($id)
	{
	    $config=$this->getRedisConfig();
	    $redis=$this->getRedis($config['RIP'], $config['RIndex'],$config['RPort']);
	    $members=$redis->hgetall('League:'.$id.':1');
	    $fmt='Imid/Snlen/a64mname/Imlevel/Imviplevel/Imop/Imbp/Imtdc/Imgexp/Imfcont/Immainid/Imcampid/Imcpos/Imlegong/Imjtime/Imofftime/Cmstatus';
	    $dt=array();
	    foreach ($members as $value) 
	    {
	        $dt[]=unpack($fmt, $value);
	    }
	    return $dt;
	}
	
	
	
}
?>
