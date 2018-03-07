<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
class active extends admin {
    /**
     * @var Redis
     */
    private $_redis;
    
     /**
     * @var model
     */
    private $_active;
    /**
     * @var model
     */
    private $_language;
    /**
     * @var model
     */
    private $_activeArr;
    /**
     * @var model
     */
    private $_activeEndTime;
    
    public function __construct() {
        $this->_active=pc_base::load_model('active_model');
        $this->_language=pc_base::load_model('language_model');
        $this->_activeArr=pc_base::load_model('activeArr_model');
        $this->_activeEndTime=$this->active_time();
//	    $priv=self::check_priv_admin();
//        if(!empty($priv)&&$priv['code']===0){
//          exit(json_encode($priv)); 
//        }
    }   
    
    public function updateChannel()
    {
        $_SESSION['channel']=isset($_POST['channel'])?$_POST['channel']:1;
    }
    /*
     * 活动中心
     */
    public function  index()
    {
        $servers=$this->get_server_config();
        $activeEndTime=json_encode($this->_activeEndTime);
        $sid=isset($_SESSION['sid'])?$_SESSION['sid']:0;
        $default=$this->_active->get_one('`server_id`='.$sid);
        if(!$default)
        {
            $default=$this->_active->get_one('`server_id`=0');
        }
        $xml=simplexml_load_string($default['content']);
        $sp=strpos($default['content'],'<!--');
        $ep=strpos($default['content'],'-->');
        $comment=substr($default['content'],$sp+4, $ep-$sp-4);
        $langCN_config=array();
        $langCN=simplexml_load_file(PHPCMS_PATH.'/statics/config/LanguageCN.xml');
        foreach ($langCN as $vl) {
            $langCN_config[(string)$vl['Key']]=(string)$vl['Value'];
        }
        include $this->admin_tpl('active','active');
    }
    /*
     * 累计充值
     */
    public function acc_charge()
    {
        $config=$this->get_config();
        $activeEndTime=$this->_activeEndTime;
        $activeId='201';//ToolResult表中的累计充值活动id
        foreach($activeEndTime as $k=>$v){
            $activeTypeTime[$k]=$v[$activeId];
        }
        $server=$activeTypeTime[$_SESSION['sid']];//做活动结束时间对比匹配
        $activeTypeTime=json_encode($activeTypeTime);//做同步时间对比匹配
        $servers=$this->get_server_config();
        $items=array('Items','ItemReward');//物品加数量 特殊处理
        $array=$this->findXml_array('30000',$items);
        $comment=$array['comment'];
        $xml=$array['xml_arr'];
        include $this->admin_tpl('acc_charge','active');
    }
    /*
     * 累计消费
     */
    public function cost_sum()
    {
        $config=$this->get_config();
        $activeEndTime=$this->_activeEndTime;
        $activeId='401';//ToolResult表中的累计充值活动id
        foreach($activeEndTime as $k=>$v){
            $activeTypeTime[$k]=$v[$activeId];
        }
        $server=$activeTypeTime[$_SESSION['sid']];//做活动结束时间对比匹配
        $activeTypeTime=json_encode($activeTypeTime);//做同步时间对比匹配
        $servers=$this->get_server_config();
        $items=array('ItemReward');//物品加数量 特殊处理
        $array=$this->findXml_array('40000',$items);
        $comment=$array['comment'];
        $xml=$array['xml_arr'];
        include $this->admin_tpl('cost_sum','active');
    }
    /*
     * 排行双倍
     */
    public function rank()
    {
        $config=$this->get_config();
        $servers=$this->get_server_config();
        $items=array('ItemReward');//物品加数量 特殊处理
        $array=$this->findXml_array('50000',$items);
        $comment=$array['comment'];
        $xml=$array['xml_arr'];
        include $this->admin_tpl('rank','active');
    }
    /*
     * 充值特惠
     */
    public function discount()
    {
        $config=$this->get_config();
        $activeEndTime=$this->_activeEndTime;
        $activeId='301';//ToolResult表中的累计充值活动id
        foreach($activeEndTime as $k=>$v){
            $activeTypeTime[$k]=$v[$activeId];
        }
        $server=$activeTypeTime[$_SESSION['sid']];//做活动结束时间对比匹配
        $activeTypeTime=json_encode($activeTypeTime);//做同步时间对比匹配
        $servers=$this->get_server_config();
        $items=array('ReturnItem');//物品加数量 特殊处理
        $array=$this->findXml_array('10000',$items);
        $comment=$array['comment'];
        $xml=$array['xml_arr'];
        include $this->admin_tpl('discount','active');
    }
    /*
     * 累计登陆
     */
    public function acc_login()
    {
        $config=$this->get_config();
        $activeEndTime=$this->_activeEndTime;
        $activeId='101';//ToolResult表中的累计充值活动id
        foreach($activeEndTime as $k=>$v){
            $activeTypeTime[$k]=$v[$activeId];
        }
        $server=$activeTypeTime[$_SESSION['sid']];//做活动结束时间对比匹配
        $activeTypeTime=json_encode($activeTypeTime);//做同步时间对比匹配
        $servers=$this->get_server_config();
        $items=array('ItemReward');//物品加数量 特殊处理
        $array=$this->findXml_array('20000',$items);
        $comment=$array['comment'];
        $xml=$array['xml_arr'];
        include $this->admin_tpl('acc_login','active');
        
    }
    /*
     * 累计招募
     */
    public function recruit()
    {
        $config=$this->get_config();
        $activeEndTime=$this->_activeEndTime;
        $activeId='5001';//ToolResult表中的累计充值活动id
        foreach($activeEndTime as $k=>$v){
            $activeTypeTime[$k]=$v[$activeId];
        }
        $server=$activeTypeTime[$_SESSION['sid']];//做活动结束时间对比匹配
        $activeTypeTime=json_encode($activeTypeTime);//做同步时间对比匹配
        $servers=$this->get_server_config();
        $items=array('ItemReward');//物品加数量 特殊处理
        $array=$this->findXml_array('60000',$items);
        $comment=$array['comment'];
        $xml=$array['xml_arr'];
        include $this->admin_tpl('recruit','active');
    }
    /*
     * 每日消费
     */
    public function daily_cost()
    {
        $config=$this->get_config();
        $activeEndTime=$this->_activeEndTime;
        $activeId='501';//ToolResult表中的累计充值活动id
        foreach($activeEndTime as $k=>$v){
            $activeTypeTime[$k]=$v[$activeId];
        }
        $server=$activeTypeTime[$_SESSION['sid']];//做活动结束时间对比匹配
        $activeTypeTime=json_encode($activeTypeTime);//做同步时间对比匹配
        $servers=$this->get_server_config();
        $items=array('ItemReward');//物品加数量 特殊处理
        $array=$this->findXml_array('70000',$items);
        $comment=$array['comment'];
        $xml=$array['xml_arr'];
        include $this->admin_tpl('daily_cost','active');
    }

    /*
     * 每日充值
     */
    public function daily_pay()
    {
        $config=$this->get_config();
        $activeEndTime=$this->_activeEndTime;
        $activeId='601';//ToolResult表中的累计充值活动id
        foreach($activeEndTime as $k=>$v){
            $activeTypeTime[$k]=$v[$activeId];
        }
        $server=$activeTypeTime[$_SESSION['sid']];//做活动结束时间对比匹配
        $activeTypeTime=json_encode($activeTypeTime);//做同步时间对比匹配
        $servers=$this->get_server_config();
        $heros=$this->__get_active_heros();//活动武将
        $heroid=$this->__get_hero_id();
        $items=array('ItemReward');//物品加数量 特殊处理
        $array=$this->findXml_array('80000',$items);
        $comment=$array['comment'];
        $xml=$array['xml_arr'];
        include $this->admin_tpl('daily_pay','active');
    }
    // /*
    //  * 活动招募
    //  */
    // public function active_recruit()
    // {
    //     $servers=$this->get_server_config();
    //     $heros=$this->__get_active_heros();
    //     $heroid=$this->__get_hero_id();
    //     include $this->admin_tpl('active_recruit','active');
    // }
    
    /*
     * 新名将阁
     */
    public function active_recruit()
    {
        $config=$this->get_config();
        $activeEndTime=$this->_activeEndTime;
        $activeId='7001';//ToolResult表中的累计充值活动id
        foreach($activeEndTime as $k=>$v){
            $activeTypeTime[$k]=$v[$activeId];
        }
        $server=$activeTypeTime[$_SESSION['sid']];//做活动结束时间对比匹配
        $activeTypeTime=json_encode($activeTypeTime);//做同步时间对比匹配
        $servers=$this->get_server_config();
        $heros=$this->__get_active_heros();//活动武将
        $heroid=$this->__get_hero_id();
        $items=array('Reward','Award');//物品加数量 特殊处理
        $array=$this->findXml_array('150000',$items);
	    $array_rank=$this->findXml_array('155000',$items);
        $array_ba=$this->findXml_array('160000',$items);
        $comment=$array['comment'];
        $xml_arr=$array['xml_arr'];
        $comment_ba=$array_ba['comment'];
        $xml_ba_arr=$array_ba['xml_arr'];//var_dump($xml_arr);
	    $comment_rank=$array_rank['comment'];
        $xml_rank_arr=$array_rank['xml_arr'];//var_dump($xml_rank_arr);
        include $this->admin_tpl('active_recruit','active');
    }
    /*
     * 节日活动
     */
    public function newyear()
    {
        $config=$this->get_config();
        $activeEndTime=$this->_activeEndTime;
        $activeId='8001';//ToolResult表中的累计充值活动id
        foreach($activeEndTime as $k=>$v){
            $activeTypeTime[$k]=$v[$activeId];
        }
        $server=$activeTypeTime[$_SESSION['sid']];//做活动结束时间对比匹配
        $activeTypeTime=json_encode($activeTypeTime);//做同步时间对比匹配
        $servers=$this->get_server_config();
        $items=array('ItemReward','ItemDrop','CostItem','GetItem');//物品加数量 特殊处理
        $array=$this->findXml_array('90000',$items);
        $array_get=$this->findXml_array('91000',$items);
        $comment=$array['comment'];
        $xml_arr=$array['xml_arr'];
        $comment_get=$array_get['comment'];
        $xml_get_arr=$array_get['xml_arr'];
        include $this->admin_tpl('newyear','active');
    }
    /*
     * 神兵排行
     */
    public function leaderboard()
    {
        $config=$this->get_config();
        $activeEndTime=$this->_activeEndTime;
        $activeId='30001';//ToolResult表中的累计充值活动id
        foreach($activeEndTime as $k=>$v){
            $activeTypeTime[$k]=$v[$activeId];
        }
        $server=$activeTypeTime[$_SESSION['sid']];//做活动结束时间对比匹配
        $activeTypeTime=json_encode($activeTypeTime);//做同步时间对比匹配
        $servers=$this->get_server_config();
        $items=array('ItemReward');//物品加数量 特殊处理
        $array=$this->findXml_array('110000',$items);
        $comment=$array['comment'];
        $xml_arr=$array['xml_arr'];

				$array_ti=$this->findXml_array('115000',$items);
        $comment_ti=$array_ti['comment'];
        $xml_arr_ti=$array_ti['xml_arr'];
        include $this->admin_tpl('leaderboard','active');
    }
    /*
     * 神兵招募
     */
    public function magicDraw()
    {
        $config=$this->get_config();
        $activeEndTime=$this->_activeEndTime;
        $activeId='20001';//ToolResult表中的累计充值活动id
        foreach($activeEndTime as $k=>$v){
            $activeTypeTime[$k]=$v[$activeId];
        }
        $server=$activeTypeTime[$_SESSION['sid']];//做活动结束时间对比匹配
        $activeTypeTime=json_encode($activeTypeTime);//做同步时间对比匹配
        $servers=$this->get_server_config();
        $items=array('ItemReward');//物品加数量 特殊处理
        $array=$this->findXml_array('100000',$items);
        $comment=$array['comment'];
        $xml_arr=$array['xml_arr'];
        include $this->admin_tpl('magicDraw','active');
    }
    /*
     * 限时商店
     */
    public function limitshop()
    {
        $config=$this->get_config();
        $activeEndTime=$this->_activeEndTime;
        $activeId='40001';//ToolResult表中的累计充值活动id
        foreach($activeEndTime as $k=>$v){
            $activeTypeTime[$k]=$v[$activeId];
        }
        $server=$activeTypeTime[$_SESSION['sid']];//做活动结束时间对比匹配
        $activeTypeTime=json_encode($activeTypeTime);//做同步时间对比匹配
        $servers=$this->get_server_config();
        $active=$this->findXml_array('120000',$items);
        foreach($active['xml_arr'] as $k=>$v){
            if($v['ID']==$activeId){$attr=$v;break;}
        }
        $array=$this->findXml_array('120000',$items);
        $comment=$array['comment'];
        $xml_arr=$array['xml_arr'];
        $limititem=$this->__get_limit_item();
				//var_dump($limititem);	
        include $this->admin_tpl('limitshop','active');
    }
    /*
     * 大转盘
     */
    public function turntable(){
        $config=$this->get_config();
        $activeEndTime=$this->_activeEndTime;
        $activeId='10001';//ToolResult表中的累计充值活动id
        foreach($activeEndTime as $k=>$v){
            $activeTypeTime[$k]=$v[$activeId];
        }
        $server=$activeTypeTime[$_SESSION['sid']];//做活动结束时间对比匹配
        $activeTypeTime=json_encode($activeTypeTime);//做同步时间对比匹配
        $servers=$this->get_server_config();
        $items=array('ItemReward');//物品加数量 特殊处理
        $array=$this->findXml_array('130000',$items);
        $array_lu=$this->findXml_array('140000',$items);
        $comment=$array['comment'];
        $xml_arr=$array['xml_arr'];
        $comment_lu=$array_lu['comment'];
        $xml_lu_arr=$array_lu['xml_arr'];
        include $this->admin_tpl('turntable','active');
        
    }   

    /*
     * 消费排行
     */
    public function CostRank(){
        $config=$this->get_config();
        $activeEndTime=$this->_activeEndTime;
        $activeId='40004';//ToolResult表中的累计充值活动id
        foreach($activeEndTime as $k=>$v){
            $activeTypeTime[$k]=$v[$activeId];
        }
        $server=$activeTypeTime[$_SESSION['sid']];//做活动结束时间对比匹配
        $activeTypeTime=json_encode($activeTypeTime);//做同步时间对比匹配
        $servers=$this->get_server_config();
        $items=array('ItemReward');//物品加数量 特殊处理
        $array=$this->findXml_array('170000',$items);
        $array_lu=$this->findXml_array('175000');
        $comment=$array['comment'];
        $xml_arr=$array['xml_arr'];
        $comment_lu=$array_lu['comment'];
        $xml_lu_arr=$array_lu['xml_arr'];
        
        $active=$this->findXml_array('0',$items);
        foreach($active['xml_arr'] as $k=>$v){
            if($v['ID']==$activeId){$active=$v;break;}
        }
        $RankOverDay=(strtotime($active['CloseTime'])-strtotime($active['OpenTime']))/86400;
        include $this->admin_tpl('CostRank','active');
    }
    /*
     * 游戏内充值排行
     */
    public function PayRank(){
        $config=$this->get_config();
        $activeEndTime=$this->_activeEndTime;
        $activeId='40003';//ToolResult表中的累计充值活动id
        foreach($activeEndTime as $k=>$v){
            $activeTypeTime[$k]=$v[$activeId];
        }
        $server=$activeTypeTime[$_SESSION['sid']];//做活动结束时间对比匹配
        $activeTypeTime=json_encode($activeTypeTime);//做同步时间对比匹配
        $servers=$this->get_server_config();
        $items=array('ItemReward');//物品加数量 特殊处理
        $array=$this->findXml_array('180000',$items);
        $array_lu=$this->findXml_array('185000');
        $comment=$array['comment'];
        $xml_arr=$array['xml_arr'];
        $comment_lu=$array_lu['comment'];
        $xml_lu_arr=$array_lu['xml_arr'];
        
        $active=$this->findXml_array('0',$items);
        foreach($active['xml_arr'] as $k=>$v){
            if($v['ID']==$activeId){$active=$v;break;}
        }
        $RankOverDay=(strtotime($active['CloseTime'])-strtotime($active['OpenTime']))/86400;
        //var_dump($RankOverDay);
        include $this->admin_tpl('PayRank','active');
    }
    /*
     * 连续充值活动
     */
    public function PaySumDay(){
        $config=$this->get_config();
        $activeEndTime=$this->_activeEndTime;
        $activeId='40002';//ToolResult表中的累计充值活动id
        foreach($activeEndTime as $k=>$v){
            $activeTypeTime[$k]=$v[$activeId];
        }
        $server=$activeTypeTime[$_SESSION['sid']];//做活动结束时间对比匹配
        $activeTypeTime=json_encode($activeTypeTime);//做同步时间对比匹配
        $servers=$this->get_server_config();var_dump($_GET);
        $items=array('ItemReward');//物品加数量 特殊处理
        $array=$this->findXml_array('190000',$items,$_GET['renovate']);
        $comment=$array['comment'];
        $xml_arr=$array['xml_arr'];

        include $this->admin_tpl('PaySumDay','active');
    }

    /*
     * 七日活动
     */
    public function sevenDayActive(){
        $config=$this->get_config();
        $activeEndTime=$this->_activeEndTime;
        $activeId='50001';//ToolResult表中的累计充值活动id
        foreach($activeEndTime as $k=>$v){
            $activeTypeTime[$k]=$v[$activeId];
        }
        $server=$activeTypeTime[$_SESSION['sid']];//做活动结束时间对比匹配
        $activeTypeTime=json_encode($activeTypeTime);//做同步时间对比匹配
        $servers=$this->get_server_config();
        $items=array('');//物品:数量 特殊处理
        $array=$this->findXml_array('200000',$items);
        $comment=$array['comment'];
        $xml_arr=$array['xml_arr'];
	include $this->admin_tpl('sevenDayActive','active');
    } 

    /*
     * 点赞评论
     */
    public function comment(){
        $config=$this->get_config();
        $activeEndTime=$this->_activeEndTime;
        $activeId='60001';//ToolResult表中的累计充值活动id
        foreach($activeEndTime as $k=>$v){
            $activeTypeTime[$k]=$v[$activeId];
        }
        $server=$activeTypeTime[$_SESSION['sid']];//做活动结束时间对比匹配
        $activeTypeTime=json_encode($activeTypeTime);//做同步时间对比匹配
        $servers=$this->get_server_config();
        $items=array('');//物品:数量 特殊处理
        $array=$this->findXml_array('210000',$items);
        $comment=$array['comment'];
        $xml_arr=$array['xml_arr'];
        include $this->admin_tpl('comment','active');
    }
    
    /*
     * 导出配表
     */
    public function exportConfig()
    {
//        $ret=array('ret'=>0,'msg'=>'');
        $sid=$_REQUEST['sid']?$_REQUEST['sid']:0;
        if(!empty($sid))
        {
            
            $default=$this->_active->get_one(array('server_id'=>$sid));
            if($default)
            {
                $dir="xml/".$sid;
                if(!file_exists($dir)){
                    mkdir($dir,0777,true);
                }
                
                $filename=$dir.'/ToolResult.xml';
                file_put_contents($filename, $default['content']);
                header("Cache-Control: public"); 
                header("Content-Description: File Transfer"); 
                header('Content-disposition: attachment; filename='.basename($filename)); //文件名   
                header("Content-Type: application/xml");  
                header("Content-Transfer-Encoding: xml");  
                header('Content-Length: '. filesize($filename));  
                @readfile($filename);
            }else{
                echo '该区没有配置';
            }
        }else{
            echo '缺少参数';
        }
    }
    
    
    
    public function syncAll()
    {
       $ret=array('code'=>0,'msg'=>'');
       if(isset($_POST['sid'])&&isset($_POST['servers']))
       {
           if(is_array($_POST['servers']))
           {
               $sid=isset($_POST['sid'])?$_POST['sid']:0;
               $content=$this->_active->get_one('`server_id`='.$sid);
               if($content)
               {
                   $this->sync_xml($sid,$_POST['servers'],'ToolResult','0');
                   $ret['msg']='同步成功';
               }else{
                   $ret['code']=2;
                   $ret['msg']='请先保存';
               }
           }else{
               $ret['code']=3;
               $ret['msg']='请选择要同步的服务器';
           }
       }else{
           $ret['code']=1;
           $ret['msg']='缺少参数';
       }
       echo json_encode($ret);
    }
    public function sync_login()
    {
       $ret=array('code'=>0,'msg'=>'');
       if(isset($_POST['sid'])&&isset($_POST['servers']))
       {
           if(is_array($_POST['servers']))
           {
               $sid=isset($_POST['sid'])?$_POST['sid']+20000:20000;
               $content=$this->_active->get_one('`server_id`='.$sid);
               if($content)
               {
                   $this->sync_xml($sid,$_POST['servers'],'DailyLogin','20000');
                   $ret['msg']='同步成功';
               }else{
                   $ret['code']=2;
                   $ret['msg']='请先保存';
               }
           }else{
               $ret['code']=3;
               $ret['msg']='请选择要同步的服务器';
           }
       }else{
           $ret['code']=1;
           $ret['msg']='缺少参数';
       }
       echo json_encode($ret);
    }
    
    public function sync_discount()
    {
       $ret=array('code'=>0,'msg'=>'');
       if(isset($_POST['sid'])&&isset($_POST['servers']))
       {
           if(is_array($_POST['servers']))
           {
               $sid=isset($_POST['sid'])?$_POST['sid']+10000:10000;
               $content=$this->_active->get_one('`server_id`='.$sid);
               if($content)
               {
                   $this->sync_xml($sid,$_POST['servers'],'PayRebate','10000');
                   $ret['msg']='同步成功';
               }else{
                   $ret['code']=2;
                   $ret['msg']='请先保存';
               }
           }else{
               $ret['code']=3;
               $ret['msg']='请选择要同步的服务器';
           }
       }else{
           $ret['code']=1;
           $ret['msg']='缺少参数';
       }
       echo json_encode($ret);
    }
    public function sync_acc_charge()
    {
       $ret=array('code'=>0,'msg'=>'');
       if(isset($_POST['sid'])&&isset($_POST['servers']))
       {
           if(is_array($_POST['servers']))
           {
               $sid=isset($_POST['sid'])?$_POST['sid']+30000:30000;
               $content=$this->_active->get_one('`server_id`='.$sid);
               if($content)
               {
                   $this->sync_xml($sid,$_POST['servers'],'PaySum','30000');
                   $ret['msg']='同步成功';
               }else{
                   $ret['code']=2;
                   $ret['msg']='请先保存';
               }
           }else{
               $ret['code']=3;
               $ret['msg']='请选择要同步的服务器';
           }
       }else{
           $ret['code']=1;
           $ret['msg']='缺少参数';
       }
       echo json_encode($ret);
    }
    
    public function sync_cost_sum()
    {
       $ret=array('code'=>0,'msg'=>'');
       if(isset($_POST['sid'])&&isset($_POST['servers']))
       {
           if(is_array($_POST['servers']))
           {
               $sid=isset($_POST['sid'])?$_POST['sid']+40000:40000;
               $content=$this->_active->get_one('`server_id`='.$sid);
               if($content)
               {
                   $this->sync_xml($sid,$_POST['servers'],'CostSum','40000');
                   $ret['msg']='同步成功';
               }else{
                   $ret['code']=2;
                   $ret['msg']='请先保存';
               }
           }else{
               $ret['code']=3;
               $ret['msg']='请选择要同步的服务器';
           }
       }else{
           $ret['code']=1;
           $ret['msg']='缺少参数';
       }
       echo json_encode($ret);
    }
    public function sync_rank()
    {
       $ret=array('code'=>0,'msg'=>'');
       if(isset($_POST['sid'])&&isset($_POST['servers']))
       {
           if(is_array($_POST['servers']))
           {
               $sid=isset($_POST['sid'])?$_POST['sid']+50000:50000;
               $content=$this->_active->get_one('`server_id`='.$sid);
               if($content)
               {
                   $this->sync_xml($sid,$_POST['servers'],'MailDouble','50000');
                   $ret['msg']='同步成功';
               }else{
                   $ret['code']=2;
                   $ret['msg']='请先保存';
               }
           }else{
               $ret['code']=3;
               $ret['msg']='请选择要同步的服务器';
           }
       }else{
           $ret['code']=1;
           $ret['msg']='缺少参数';
       }
       echo json_encode($ret);
    }
    
    public function sync_recruit()
    {
       $ret=array('code'=>0,'msg'=>'');
       if(isset($_POST['sid'])&&isset($_POST['servers']))
       {
           if(is_array($_POST['servers']))
           {
               $sid=isset($_POST['sid'])?$_POST['sid']+60000:60000;
               $content=$this->_active->get_one('`server_id`='.$sid);
               if($content)
               {
                   $this->sync_xml($sid,$_POST['servers'],'AddRecruit','60000');
                   $ret['msg']='同步成功';
               }else{
                   $ret['code']=2;
                   $ret['msg']='请先保存';
               }
           }else{
               $ret['code']=3;
               $ret['msg']='请选择要同步的服务器';
           }
       }else{
           $ret['code']=1;
           $ret['msg']='缺少参数';
       }
       echo json_encode($ret);
    }
    public function sync_dailycost()
    {
       $ret=array('code'=>0,'msg'=>'');
       if(isset($_POST['sid'])&&isset($_POST['servers']))
       {
           if(is_array($_POST['servers']))
           {
               $sid=isset($_POST['sid'])?$_POST['sid']+70000:70000;
               $content=$this->_active->get_one('`server_id`='.$sid);
               if($content)
               {
                   $this->sync_xml($sid,$_POST['servers'],'DailyCostSum','70000');
                   $ret['msg']='同步成功';
               }else{
                   $ret['code']=2;
                   $ret['msg']='请先保存';
               }
           }else{
               $ret['code']=3;
               $ret['msg']='请选择要同步的服务器';
           }
       }else{
           $ret['code']=1;
           $ret['msg']='缺少参数';
       }
       echo json_encode($ret);
    }
    public function sync_dailypay()
    {
       $ret=array('code'=>0,'msg'=>'');
       if(isset($_POST['sid'])&&isset($_POST['servers']))
       {
           if(is_array($_POST['servers']))
           {
               $sid=isset($_POST['sid'])?$_POST['sid']+80000:80000;
               $content=$this->_active->get_one('`server_id`='.$sid);
               if($content)
               {
                   $this->sync_xml($sid,$_POST['servers'],'DailyPaySum','80000');
                   $ret['msg']='同步成功';
               }else{
                   $ret['code']=2;
                   $ret['msg']='请先保存';
               }
           }else{
               $ret['code']=3;
               $ret['msg']='请选择要同步的服务器';
           }
       }else{
           $ret['code']=1;
           $ret['msg']='缺少参数';
       }
       echo json_encode($ret);
    }
    public function sync_newyear()
    {
       $ret=array('code'=>0,'msg'=>'');
       if(isset($_POST['sid'])&&isset($_POST['servers']))
       {
           if(is_array($_POST['servers']))
           {
               $sid=isset($_POST['sid'])?$_POST['sid']+90000:90000;
               $sid_ba=isset($_POST['sid'])?$_POST['sid']+91000:91000;
               $content=$this->_active->get_one('`server_id`='.$sid);
               $content_ba=$this->_active->get_one('`server_id`='.$sid);
               if($content&&$content_ba)
               {
                   $this->sync_xml($sid,$_POST['servers'],'NewYearRecycle','90000');
                   $this->sync_xml($sid_ba,$_POST['servers'],'NewYearActivity','91000');
                   $ret['msg']='同步成功';
               }else{
                   $ret['code']=2;
                   $ret['msg']='请先保存';
               }
           }else{
               $ret['code']=3;
               $ret['msg']='请选择要同步的服务器';
           }
       }else{
           $ret['code']=1;
           $ret['msg']='缺少参数';
       }
       echo json_encode($ret);
    }
    
    public function sync_leaderboard()
    {
        $ret=array('code'=>0,'msg'=>'');
        if(isset($_POST['sid'])&&isset($_POST['servers']))
        {
            if(is_array($_POST['servers']))
            {
                $sid=isset($_POST['sid'])?$_POST['sid']+110000:110000;
		$sid_ti=isset($_POST['sid'])?$_POST['sid']+115000:115000;
                $content=$this->_active->get_one('`server_id`='.$sid);
		$content_ti=$this->_active->get_one('`server_id`='.$sid_ti);
                if($content && $content_ti)
                {
                    $this->sync_xml($sid,$_POST['servers'],'ArtifactRank','110000');
		    $this->sync_xml($sid_ti,$_POST['servers'],'ArtifacRankBase','115000');
                    $ret['msg']='同步成功';
                }else{
                    $ret['code']=2;
                    $ret['msg']='请先保存';
                }
            }else{
                $ret['code']=3;
                $ret['msg']='请选择要同步的服务器';
            }
        }else{
            $ret['code']=1;
            $ret['msg']='缺少参数';
        }
        echo json_encode($ret);
    }
    
    public function sync_magicDraw()
    {
        $ret=array('code'=>0,'msg'=>'');
        if(isset($_POST['sid'])&&isset($_POST['servers']))
        {
            if(is_array($_POST['servers']))
            {
                $sid=isset($_POST['sid'])?$_POST['sid']+100000:100000;
                $content=$this->_active->get_one('`server_id`='.$sid);
                if($content)
                {
                    $this->sync_xml($sid,$_POST['servers'],'AddArtifact','100000');
                    $ret['msg']='同步成功';
                }else{
                    $ret['code']=2;
                    $ret['msg']='请先保存';
                }
            }else{
                $ret['code']=3;
                $ret['msg']='请选择要同步的服务器';
            }
        }else{
            $ret['code']=1;
            $ret['msg']='缺少参数';
        }
        echo json_encode($ret);
    }
    
    public function sync_limitshop()
    {
        $ret=array('code'=>0,'msg'=>'');
        if(isset($_POST['sid'])&&isset($_POST['servers']))
        {
            if(is_array($_POST['servers']))
            {
                $sid=isset($_POST['sid'])?$_POST['sid']+120000:120000;
                $content=$this->_active->get_one('`server_id`='.$sid);
                if($content)
                {
                    $this->sync_xml($sid,$_POST['servers'],'ActivityShopItemSlot','120000');
                    $ret['msg']='同步成功';
                }else{
                    $ret['code']=2;
                    $ret['msg']='请先保存';
                }
            }else{
                $ret['code']=3;
                $ret['msg']='请选择要同步的服务器';
            }
        }else{
            $ret['code']=1;
            $ret['msg']='缺少参数';
        }
        echo json_encode($ret);
    }
    
    public function sync_turntable()
    {
        $ret=array('code'=>0,'msg'=>'');
        if(isset($_POST['sid'])&&isset($_POST['servers']))
        {
            if(is_array($_POST['servers']))
            {
                $sid=isset($_POST['sid'])?$_POST['sid']+130000:130000;
                $sid_ba=isset($_POST['sid'])?$_POST['sid']+140000:140000;
                $content=$this->_active->get_one('`server_id`='.$sid);
                $content_ba=$this->_active->get_one('`server_id`='.$sid);
                if($content&&$content_ba)
                {
                    $this->sync_xml($sid,$_POST['servers'],'LuckyCircleItems','130000');
                    $this->sync_xml($sid_ba,$_POST['servers'],'LuckyCircleBase','140000');
                    $ret['msg']='同步成功';
                }else{
                    $ret['code']=2;
                    $ret['msg']='请先保存';
                }
            }else{
                $ret['code']=3;
                $ret['msg']='请选择要同步的服务器';
            }
        }else{
            $ret['code']=1;
            $ret['msg']='缺少参数';
        }
        echo json_encode($ret);
    }
    
    // public function sync_active_recruit()
    // {
    //     $ret=array();
    //     if(isset($_POST['sid'])&&isset($_POST['servers'])&&isset($_POST['hid']))
    //     {
    //         $servers=$this->get_server_config();
    //         foreach ($_POST['servers'] as $k=>$v)
    //         {
    //             if($v!=$_POST['sid'])
    //             {
    //                 if(isset($servers[$v]))
    //                 {
    //                     $server=$servers[$v];
    //                     $this->__set_active_hero($server['GIP'], $server['GPort'], $_POST['hid']);
    //                 }
    //             }
    //         }
    //         self::manage_log();
    //         $ret['msg']='同步活动将成功';
    //     }
    //     echo json_encode($ret);
    // }

    public function sync_active_recruit(){
        $ret=array('code'=>0,'msg'=>'');
        if(isset($_POST['sid'])&&isset($_POST['servers']))
        {
            if(is_array($_POST['servers']))
            {
                $sid=isset($_POST['sid'])?$_POST['sid']+150000:150000;
                $sid_ba=isset($_POST['sid'])?$_POST['sid']+160000:160000;
		$sid_rank=isset($_POST['sid'])?$_POST['sid']+155000:155000;
                $content=$this->_active->get_one('`server_id`='.$sid);
                $content_ba=$this->_active->get_one('`server_id`='.$sid);
		$content_rank=$this->_active->get_one('`server_id`='.$sid);
                if($content&&$content_ba)
                {
                    $this->sync_xml($sid,$_POST['servers'],'LegendRecruitTimes','150000');
                    $this->sync_xml($sid_ba,$_POST['servers'],'LegendRecruitbase','160000');
		    $this->sync_xml($sid_rank,$_POST['servers'],'LegendRecruitRank','155000');
                    $ret['msg']='同步成功';
                }else{
                    $ret['code']=2;
                    $ret['msg']='请先保存';
                }
            }else{
                $ret['code']=3;
                $ret['msg']='请选择要同步的服务器';
            }
        }else{
            $ret['code']=1;
            $ret['msg']='缺少参数';
        }
        echo json_encode($ret);
    }

    /*
     * 同步消费排行
     */
    public function sync_CostRank(){
        $ret=array('code'=>0,'msg'=>'');
        if(isset($_POST['sid'])&&isset($_POST['servers']))
        {
            if(is_array($_POST['servers']))
            {
                $sid=isset($_POST['sid'])?$_POST['sid']+170000:170000;
                $sid_ba=isset($_POST['sid'])?$_POST['sid']+175000:175000;
                $content=$this->_active->get_one('`server_id`='.$sid);
                $content_ba=$this->_active->get_one('`server_id`='.$sid);
                if($content&&$content_ba)
                {
                    $this->sync_xml($sid,$_POST['servers'],'CostRank','170000');
                    $this->sync_xml($sid_ba,$_POST['servers'],'CostRankBase','175000');
                    $ret['msg']='同步成功';
                }else{
                    $ret['code']=2;
                    $ret['msg']='请先保存';
                }
            }else{
                $ret['code']=3;
                $ret['msg']='请选择要同步的服务器';
            }
        }else{
            $ret['code']=1;
            $ret['msg']='缺少参数';
        }
        echo json_encode($ret);
    }
    /*
     * 同步充值排行
     */
    public function sync_PayRank(){
        $ret=array('code'=>0,'msg'=>'');
        if(isset($_POST['sid'])&&isset($_POST['servers']))
        {
            if(is_array($_POST['servers']))
            {
                $sid=isset($_POST['sid'])?$_POST['sid']+180000:180000;
                $sid_ba=isset($_POST['sid'])?$_POST['sid']+185000:185000;
                $content=$this->_active->get_one('`server_id`='.$sid);
                $content_ba=$this->_active->get_one('`server_id`='.$sid);
                if($content&&$content_ba)
                {
                    $this->sync_xml($sid,$_POST['servers'],'CostRank','180000');
                    $this->sync_xml($sid_ba,$_POST['servers'],'CostRankBase','185000');
                    $ret['msg']='同步成功';
                }else{
                    $ret['code']=2;
                    $ret['msg']='请先保存';
                }
            }else{
                $ret['code']=3;
                $ret['msg']='请选择要同步的服务器';
            }
        }else{
            $ret['code']=1;
            $ret['msg']='缺少参数';
        }
        echo json_encode($ret);
    }
    /*
     * 同步连续充值
     * 
     */
    public function sync_PaySumDay(){
        $ret=array('code'=>0,'msg'=>'');
        if(isset($_POST['sid'])&&isset($_POST['servers']))
        {
            if(is_array($_POST['servers']))
            {
                $sid=isset($_POST['sid'])?$_POST['sid']+190000:190000;
                $content=$this->_active->get_one('`server_id`='.$sid);
                if($content)
                {
                    $this->sync_xml($sid,$_POST['servers'],'PaySumDay','190000');
                    $ret['msg']='同步成功';
                }else{
                    $ret['code']=2;
                    $ret['msg']='请先保存';
                }
            }else{
                $ret['code']=3;
                $ret['msg']='请选择要同步的服务器';
            }
        }else{
            $ret['code']=1;
            $ret['msg']='缺少参数';
        }
        echo json_encode($ret);
    }

    /*
     * 同步七日活动
     * 
     */
    public function sync_sevenDayActive(){
        $ret=array('code'=>0,'msg'=>'');
        if(isset($_POST['sid'])&&isset($_POST['servers']))
        {
            if(is_array($_POST['servers']))
            {
                $sid=isset($_POST['sid'])?$_POST['sid']+200000:200000;
                $content=$this->_active->get_one('`server_id`='.$sid);
                if($content)
                {
                    $this->sync_xml($sid,$_POST['servers'],'ASevenDayBase','200000');
                    $ret['msg']='同步成功';
                }else{
                    $ret['code']=2;
                    $ret['msg']='请先保存';
                }
            }else{
                $ret['code']=3;
                $ret['msg']='请选择要同步的服务器';
            }
        }else{
            $ret['code']=1;
            $ret['msg']='缺少参数';
        }
        echo json_encode($ret);
    }

    /*
     * 同步评论点赞
     * 
     */
    public function sync_comment(){
        $ret=array('code'=>0,'msg'=>'');
        if(isset($_POST['sid'])&&isset($_POST['servers']))
        {
            if(is_array($_POST['servers']))
            {
                $sid=isset($_POST['sid'])?$_POST['sid']+210000:210000;
                $content=$this->_active->get_one('`server_id`='.$sid);
                if($content)
                {
                    $this->sync_xml($sid,$_POST['servers'],'CommitLikeBase','210000');
                    $ret['msg']='同步成功';
                }else{
                    $ret['code']=2;
                    $ret['msg']='请先保存';
                }
            }else{
                $ret['code']=3;
                $ret['msg']='请选择要同步的服务器';
            }
        }else{
            $ret['code']=1;
            $ret['msg']='缺少参数';
        }
        echo json_encode($ret);
    }

    public function update()
    {
       $ret=array('code'=>0,'msg'=>'');
       $exclude=array('Item','Equip','Stone','Mount','Frag');
	foreach($_POST['content'] as $v=>$k){
	   $open=strtotime($k['OpenTime'])+$k['OpenSecond'];
	   $close=strtotime($k['CloseTime'])+$k['CloseSecond'];
	   if($open>$close){
	      	$ret['msg']=$k['Des'].'---开始时间大于结束时间';
		echo json_encode($ret);
		exit;
	   }
	}
       if(isset($_POST['content'])&&isset($_POST['sid']))
       {
           $root=new DOMDocument("1.0", 'UTF-8');
           $root->appendChild(new DOMComment($_POST['comment']));
           $children=$root->createElement("ToolResult");
            
           foreach ($_POST['content'] as $k=>$v)
           {
		foreach ($v as $kk => $vv) {
                   $v[$kk]=trim($vv);
                }
                $child=$root->createElement("row");
                
                foreach ($v as $k1=>$v1)
                {
                    if(in_array($k1, $exclude))
                    {
                        continue;
                    }
                    $child->setAttribute($k1,$v1);
                }
                $items='0';
                if($v['Item'])
                {
                    $items.=','.$v['Item'];
                }
                if($v['Equip'])
                {
                    $items.=','.$v['Equip'];
                }
                if($v['Stone'])
                {
                    $items.=','.$v['Stone'];
                }
                if($v['Mount'])
                {
                    $items.=','.$v['Mount'];
                }
                if($v['Frag'])
                {
                    $items.=','.$v['Frag'];
                }
                if($items!=='0')
                {
                    $items=substr($items, 2);
                }
                $child->setAttribute('DropItemSave', $items);
                $children->appendChild($child);
           }
          $root->appendChild($children);
          $root->formatOutput=TRUE;
          $xml=$root->saveXML();
          $sid=isset($_POST['sid'])?$_POST['sid']:0;
          $_SESSION['sid']=$_POST['sid'];
          $servers=$this->get_server_config();
          $default=$this->_active->get_one('`server_id`='.$sid);
          if(!$default)
          {
              $this->_active->insert(array('server_id'=>$_POST['sid'],'content'=>$xml));
          }else{
              $this->_active->update(array('server_id'=>$_POST['sid'],'content'=>$xml),'`server_id`='.$sid);
          }
            $servers=$this->get_server_config();
            if(isset($servers[$_POST['sid']]))
            {
                $server=$servers[$_POST['sid']];
                if(isset($server['GIP'])&&isset($server['GPort']))
                {
                     $this->_sendAndReload($server['GIP'], $server['GPort'], 'ToolResult.xml', $xml);
                }
            }
             self::manage_log();
            $ret['msg']='保存成功';
       }else{
           $ret['code']=1;
           $ret['msg']='缺少参数';
       }
       echo json_encode($ret);
    }
    
    
    public function update_discount()
    {
       $ret=array('code'=>0,'msg'=>'');
       $_SESSION['sid']=$_POST['sid'];
       $servers=$this->get_server_config();
       if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
       {
           $sid=isset($_POST['sid'])?$_POST['sid']+10000:10000;
           $server=$servers[$_POST['sid']];
           $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"PayRebate",$sid,$server);
       }
       echo json_encode($ret);
    }
    
    public function update_login()
    {
       $ret=array('code'=>0,'msg'=>'');
       $_SESSION['sid']=$_POST['sid'];
       $servers=$this->get_server_config();
       if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
       {
           $sid=isset($_POST['sid'])?$_POST['sid']+20000:20000;
           $server=$servers[$_POST['sid']];
           $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"DailyLogin",$sid,$server);
       }
       echo json_encode($ret);
    }
    
    public function update_acc_charge()
    {
       $ret=array('code'=>0,'msg'=>'');
       $_SESSION['sid']=$_POST['sid'];
       $servers=$this->get_server_config();
       if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
       {
           $sid=isset($_POST['sid'])?$_POST['sid']+30000:30000;
           $server=$servers[$_POST['sid']];
           $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"PaySum",$sid,$server);
       }
       echo json_encode($ret);
    }
    
    public function update_cost_sum()
    {
       $ret=array('code'=>0,'msg'=>'');
       $_SESSION['sid']=$_POST['sid'];
       $servers=$this->get_server_config();
       if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
       {
           $sid=isset($_POST['sid'])?$_POST['sid']+40000:40000;
           $server=$servers[$_POST['sid']];
           $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"CostSum",$sid,$server);
       }
       echo json_encode($ret);
    }
    
    public function update_rank()
    {
       $ret=array('code'=>0,'msg'=>'');
       $_SESSION['sid']=$_POST['sid'];
       $servers=$this->get_server_config();
       if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
       {
           $sid=isset($_POST['sid'])?$_POST['sid']+50000:50000;
           $server=$servers[$_POST['sid']];
           $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"MailDouble",$sid,$server);
       }
       echo json_encode($ret);
    }
    
    public function update_recruit()
    {
       $ret=array('code'=>0,'msg'=>'');
       $_SESSION['sid']=$_POST['sid'];
       $servers=$this->get_server_config();
       if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
       {
           $sid=isset($_POST['sid'])?$_POST['sid']+60000:60000;
           $server=$servers[$_POST['sid']];
           $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"AddRecruit",$sid,$server);
       }
       echo json_encode($ret);
    }
    public function update_dailycost()
    {
        $ret=array('code'=>0,'msg'=>'');
        $_SESSION['sid']=$_POST['sid'];
        $servers=$this->get_server_config();
	$ret['test']=$_POST['content'];
        if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+70000:70000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"DailyCostSum",$sid,$server);
        }
       echo json_encode($ret);
    }
    
    public function update_dailypay()
    {
        $ret=array('code'=>0,'msg'=>'');
        $_SESSION['sid']=$_POST['sid'];
        $servers=$this->get_server_config();
        if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+80000:80000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"DailyPaySum",$sid,$server);
        }
       echo json_encode($ret);
    }
    public function update_newyear()
    {
        $ret=array('code'=>0,'msg'=>'');
        $_SESSION['sid']=$_POST['sid'];
        $servers=$this->get_server_config();
        if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+90000:90000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"NewYearRecycle",$sid,$server);
        }
        if(isset($_POST['content_get'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+91000:91000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content_get'],$_POST['comment_get'],"NewYearActivity",$sid,$server);
        }
       echo json_encode($ret);
    }
    
    public function update_leaderboard()
    {
        $ret=array('code'=>0,'msg'=>'');
        $_SESSION['sid']=$_POST['sid'];
        $servers=$this->get_server_config();
        if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+110000:110000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"ArtifactRank",$sid,$server);
        }
	if(isset($_POST['content_ti'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
        {
	    $_POST['content_ti'][2]['Value']=$_POST['content_ti'][2]['Value']*86400;
            $sid=isset($_POST['sid'])?$_POST['sid']+115000:115000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content_ti'],$_POST['comment_ti'],"ArtifacRankBase",$sid,$server);
        }
        echo json_encode($ret);
    }
    
    public function update_magicDraw()
    {
         $ret=array('code'=>0,'msg'=>'');
        $_SESSION['sid']=$_POST['sid'];
        $servers=$this->get_server_config();
        if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+100000:100000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"AddArtifact",$sid,$server);
        }
        echo json_encode($ret);
    }
    
    public function update_limitshop()
    {
        $ret=array('code'=>0,'msg'=>'');
        $_SESSION['sid']=$_POST['sid'];
        $configItem=$this->get_config_item();
        foreach ($_POST['content'] as $k=>$v){
            $_POST['content'][$k]['ItemId']=$configItem[$v['ItemId']]['id'];
            $_POST['content'][$k]['Des']=$v['ItemId'];
        }
        $servers=$this->get_server_config();
        if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+120000:120000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"ActivityShopItemSlot",$sid,$server);
        }
        echo json_encode($ret);
    }
    
    public function update_turntable()
    {
        $ret=array('code'=>0,'msg'=>'');
        $_SESSION['sid']=$_POST['sid'];
        $configItem=$this->get_config_item();
        foreach ($_POST['content'] as $k=>$v){
            $_POST['content'][$k]['ItemID']=$configItem[$v['ItemID']]['id'];
        }
        $servers=$this->get_server_config();
        if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+130000:130000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"LuckyCircleItems",$sid,$server);
        }
        if(isset($_POST['content_lu'])&&isset($_POST['sid']))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+140000:140000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content_lu'],$_POST['comment_lu'],"LuckyCircleBase",$sid,$server);
        }
        echo json_encode($ret);
    }

    
    // public function update_active_recruit()
    // {
    //     $ret=array('code'=>0,'msg'=>'');
    //     if(isset($_POST['hid'])&&isset($_POST['sid']))
    //     {
    //         $servers=$this->get_server_config();
    //         if(isset($servers[$_POST['sid']]))
    //         {
    //             $server=$servers[$_POST['sid']];
    //             if(isset($server['GIP'])&&isset($server['GPort']))
    //             {
    //                 $this->__set_active_hero($server['GIP'], $server['GPort'], $_POST['hid']);
    //                 self::manage_log();
    //                 $ret['msg']='设置活动将成功';
    //             }
    //         }
    //     }
    //     echo json_encode($ret);
    // }
    public function update_active_recruit(){
        $ret=array('code'=>0,'msg'=>'');
        $_SESSION['sid']=$_POST['sid'];
        $servers=$this->get_server_config();
	//var_dump($_POST['content']);
        if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+150000:150000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"LegendRecruitTimes",$sid,$server);
        }
        if(isset($_POST['content_ba'])&&isset($_POST['sid']))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+160000:160000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content_ba'],$_POST['comment_ba'],"LegendRecruitbase",$sid,$server);
        }
	if(isset($_POST['content_rank'])&&isset($_POST['sid']))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+155000:155000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content_rank'],$_POST['comment_rank'],"LegendRecruitRank",$sid,$server);
        }
        echo json_encode($ret);
    }

    /*
     * 消费排行修改
     */
    public function update_CostRank()
    {
        $ret=array('code'=>0,'msg'=>'');
        $_SESSION['sid']=$_POST['sid'];
        $servers=$this->get_server_config();
        /*
         *防止结束时间和结算时间冲突
         **/
        $active=$this->findXml_array('0',$items);
        foreach($active['xml_arr'] as $k=>$v){
            if($v['ID']=='40002'){$active=$v;break;}
        }
        $RankOverDay=(strtotime($active['CloseTime'])-strtotime($active['OpenTime']))/86400;
        if($_POST['content_lu'][1]['Value']>$RankOverDay){
            $ret['msg']="活动间隔(".$RankOverDay."天)与结算时间(".$_POST['content_lu'][1]['Value']."天)冲突";
            exit(json_encode($ret));
        }
        if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+170000:170000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"CostRank",$sid,$server);
        }
        if(isset($_POST['content_lu'])&&isset($_POST['sid']))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+175000:175000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content_lu'],$_POST['comment_lu'],"CostRankBase",$sid,$server);
        }
        echo json_encode($ret);
    }
    
    /*
     * 充值排行修改
     */
    public function update_PayRank()
    {
        $ret=array('code'=>0,'msg'=>'');
        $_SESSION['sid']=$_POST['sid'];
        $servers=$this->get_server_config();
        /*
         *防止结束时间和结算时间冲突 
         **/
        $active=$this->findXml_array('0',$items);
        foreach($active['xml_arr'] as $k=>$v){
            if($v['ID']=='40003'){$active=$v;break;}
        }
        $RankOverDay=(strtotime($active['CloseTime'])-strtotime($active['OpenTime']))/86400;
        if($_POST['content_lu'][1]['Value']>$RankOverDay){
            $ret['msg']="活动间隔(".$RankOverDay."天)与结算时间(".$_POST['content_lu'][1]['Value']."天)冲突";
            exit(json_encode($ret));
        }
        
        if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+180000:180000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"PayRank",$sid,$server);
        }
        if(isset($_POST['content_lu'])&&isset($_POST['sid']))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+185000:185000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content_lu'],$_POST['comment_lu'],"PayRankBase",$sid,$server);
        }
        echo json_encode($ret);
    }
    /*
     * 连续充值修改
     */
    public function update_PaySumDay()
    {
        $ret=array('code'=>0,'msg'=>'');
        $_SESSION['sid']=$_POST['sid'];
        $servers=$this->get_server_config();
	foreach($_POST['content'] as $k=>$v){
                if(empty($v['PaySumDayLeastGold'])){
                        unset($_POST['content'][$k]);
                }
        }
        if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+190000:190000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"PaySumDay",$sid,$server);
        }
        echo json_encode($ret);
    }
    /*
     * 连续充值修改
     */
    public function update_sevenDayActive()
    {
        $ret=array('code'=>0,'msg'=>'');
        $_SESSION['sid']=$_POST['sid'];
        $servers=$this->get_server_config();
        if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+200000:200000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"ASevenDayBase",$sid,$server);
        }
        echo json_encode($ret);
    }

    /*
     * 点赞评论修改
     */
    public function update_comment()
    {
        $ret=array('code'=>0,'msg'=>'');
        $_SESSION['sid']=$_POST['sid'];
        $servers=$this->get_server_config();
        if(isset($_POST['content'])&&isset($_POST['sid'])&&isset($servers[$_POST['sid']]))
        {
            $sid=isset($_POST['sid'])?$_POST['sid']+210000:210000;
            $server=$servers[$_POST['sid']];
            $ret['msg']=$this->build_xml($_POST['content'],$_POST['comment'],"CommitLikeBase",$sid,$server);
        }
        echo json_encode($ret);
    }

    /*
     * 查询区服的xml，并且转换为数组
     * $uid                     游戏的模板活动id
     * $array['comment']        xml注释内容
     * $array['xml_arr']        xml转数组
     * $items                   物品加数量 特殊处理
     * $templet                 更新
     */
    public function findXml_array($uid,$items,$templet=0){
        $array=array();
        $sid=isset($_SESSION['sid'])?$_SESSION['sid']+$uid:$uid;
        $_SESSION['sid']=$sid-$uid;
        $servers=$this->get_server_config();
        $default=$this->_active->get_one('`server_id`='.$sid);
        if(!$default || $templet==1)
        {
            $default=$this->_active->get_one('`server_id`='.$uid);
        }
        $config=$this->get_config();
        $xml=simplexml_load_string($default['content']);
        $sp=strpos($default['content'],'<!--');
        $ep=strpos($default['content'],'-->');
        $array['comment']=substr($default['content'],$sp+4, $ep-$sp-4);
        foreach($xml->row as $k=>$v ){
            $v=$v->attributes();
            $i++;
            foreach($v as $k1=>$v1){
                if(in_array($k1,$items)){
                    $arr1=explode(',',$v1);
                    $arr=array();
                    foreach($arr1 as $k2=>$v2){
                        $arr2=explode(':',$v2);
                        /*
                         * 防止奖励相同和奖励都为空
                         */
                        $arr[][$arr2[0]]=$arr2['1'];
                    }
                    $xml_arr[$i][$k1]=$arr;
                }else{
                    $xml_arr[$i][$k1]=(string)$v1;
                }
            }
        }
        $array['xml_arr']=$xml_arr;
        return $array;
    }
    
    /*
     * 生成xml表并保存本地   发送到游戏内
     * $content     保存的内容（array）
     * $comment     xml的注释部分
     * $filename    xml的文件名
     * $sid         保存的区服ID
     * $server      保存的区服serverlist信息
     */
    public function build_xml($content,$comment,$filename,$sid,$server){
        $configItem=$this->get_config_item();//var_dump($content);
        foreach($content as $k=>$v){
            foreach($v as $k1=>$v1){//var_dump($v1);
                if(is_array($v1)){
                    $str='';
                    foreach($v1 as $k2=>$v2){//var_dump($v2);exit;
                        foreach($v2 as $k3=>$v3){
                            trim($k3);
                            if(isset($configItem[$k3]))
                            {
                                $k3=$configItem[$k3]['id'];
                            }elseif(empty($k3)){
                                $k3='0';$v3='0';
                            }else{
                                exit(json_encode(array('code'=>110,'msg'=>'<'.$k3.'>不存在')));
                            }
                            $str.=($k3.":".$v3.",");
                        }
                    }
                    $row[$k][$k1]=substr_replace($str,'',strripos($str,','));
                }else{
                    $row[$k][$k1]=$v1;
                }
            }
        }
        $root=new DOMDocument("1.0", 'UTF-8');
        $root->appendChild(new DOMComment($comment));
        $children=$root->createElement($filename);
        foreach ($row as $k=>$v){
            $child=$root->createElement("row");
            foreach($v as $k1=>$v1){
                $child->setAttribute($k1,$v1);
            }
    
            $children->appendChild($child);
        }
        $root->appendChild($children);
        $root->formatOutput=TRUE;
        $xml=$root->saveXML();
        $default=$this->_active->get_one('`server_id`='.$sid);
        if(!$default)
        {
            $this->_active->insert(array('server_id'=>$sid,'content'=>$xml));
        }else{
            $this->_active->update(array('server_id'=>$sid,'content'=>$xml),'`server_id`='.$sid);
        }
        if(isset($server['GIP'])&&isset($server['GPort']))
        {
             $this->_sendAndReload($server['GIP'], $server['GPort'], $filename.'.xml', $xml);
        }
        self::manage_log();
        return '发送成功';
    }
    
    /*
     * 同步区服的方法
     * $sid       复制模板的区服ID（string）
     * $server    要同步的区服ID（array）
     * $filename  活动的表名
     * $xml_id    模板的id
     */
    public function sync_xml($sid,$sverer,$filename,$xml_id){
        $servers=$this->get_server_config();
        $content=$this->_active->get_one('`server_id`='.$sid);
        foreach ($sverer as $k=>$v)
        {
            $server=$servers[$v];
            if($v!=$sid)
            {
                $v+=$xml_id;
                $default=$this->_active->get_one('`server_id`='.$v);
                if(!$default)
                {
                    $this->_active->insert(array('server_id'=>$v,'content'=>$content['content']));
                }else{
                    $this->_active->update(array('content'=>$content['content']),'`server_id`='.$v);
                }
                if(isset($server['GIP'])&&isset($server['GPort']))
                {
                    $this->_sendAndReload($server['GIP'], $server['GPort'], $filename.'.xml', $content['content']);
                }
            }
        }
        self::manage_log();
        return '同步成功';
    }

    public function is_item_exit(){
        $post=$_POST;
        $ret=array('code'=>'0','msg'=>'');
        $post['item']=trim($post['item']);
        $configItem=$this->get_config_item();
        if(empty($post['item'])) 
        {
            exit;
        }
        if(!isset($configItem[$post['item']])){
            $ret['code']=1;
            $ret['msg']='该物品不存在';
        }
        echo json_encode($ret);
    }
    
    private function __get_hero_id()
    {
        $server=$this->getRedisConfig();
        $redis=$this->getRedis($server['RIP'], $server['RIndex'],$server['RPort']);
        $dt=$redis->hget('GlobalCookie',105);
        return $dt;
    }

    /**
     * 活动结束时间
     * @param  [type] $active_id [description]
     * @return [type]            [description]
     */
    public function active_time(){
        $servers=$this->get_server_config();
        foreach ($servers as $sid => $v) {
            $default=$this->_active->get_one('`server_id`='.$sid);
            if(!$default)
            {
                $default=$this->_active->get_one('`server_id`=0');
            }
            $xml=simplexml_load_string($default['content']);
            foreach($xml->row as $k=>$v){
               $sv=$v->attributes();
               if (is_object($sv)) {
                   foreach ($sv as $key => $value) {
                       $array[$key] = (string)$value;
                   }
               }
               else {
                   $array = $sv;
               } 
               $sumTime[$array['ID']]=strtotime($array['CloseTime'])+$array['CloseSecond'];    
            }
            $closetime[$sid]=$sumTime;
        }
        return $closetime;
    }
    
    private function __get_active_heros()
    {
        $ret=array();
        $config=simplexml_load_file(PHPCMS_PATH.'/statics/config/LegendRecruit.xml');
        foreach ($config as $k=>$v)
        {
            if(isset($v['ID'])&&isset($v['Des']))
            {
        //    $ret[(string)$v['ID']]=array('id'=>(string)$v['ID'],'name'=>(string)$v['Des'],'heroid'=>(string)$v['HeroID'],'job'=>(string)$v['Job']);
            $ret[(string)$v['ID']]=array('id'=>(string)$v['ID'],'name'=>(string)$v['Des']); 
            }
        }
        return $ret;
    }
    
    private  function __get_limit_item()
    {
        $limititem=array();
        $limititems=simplexml_load_file(PHPCMS_PATH.'/statics/config/ActivityShopItem.xml');
        $lang=simplexml_load_file(PHPCMS_PATH.'/statics/config/LanguageCN.xml');
        $lang_config=array();
        foreach ($lang as $vl) {
            $lang_config[(string)$vl['Key']]=(string)$vl['Value'];
        }
        foreach ($limititems->row as $k=>$v) {
            $limititem[(string)$v['ID']]=array('ID'=>(string)$v['ID'],'Des'=>$lang_config[(string)$v['Name']],'ItemId'=>(string)$v['ItemId'],'PriceFormal'=>(string)$v['PriceFormal'],'PriceNow'=>(string)$v['PriceNow']);
        }
        return $limititem;
    }
    
    private function __set_active_hero($ip,$port,$hid)
    {
        $pkfmt='IISSIIII';
        $errno=0;
        $errstr='';
        $timeout=5;
        $dt=pack($pkfmt,28,0xF1E2D3C4,0x01,0xDE02,0,0,0,$hid);
        $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
        if($sock)
        {
            fwrite($sock, $dt,28);
        }
    }
    
    //_sendAndReload
    private function _sendAndReload($ip,$port,$file,$content)
    {
        $pkfmt='IISSIIIa32Sa*';
        $errno=0;
        $errstr='';
        $timeout=5;
        $len=56+2+strlen($content);
        $dt=pack($pkfmt,$len,0xF1E2D3C4,0x01,0xDDD7,0,0,0,$file,strlen($content),$content);
        $sock=fsockopen($ip,(int)$port,$errno,$errstr,$timeout);
        if($sock)
        {
            fwrite($sock, $dt,$len);
        }
    }
    
}
