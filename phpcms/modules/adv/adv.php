<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
class adv  {
    /**
     * @var Redis
     */
    private $_redis;
    
    /**
     * @var advertise_model
     */
    private $_adv;
    
    const MAX_TABLE=10;
    
    public function __construct() {
        $this->_adv=pc_base::load_model('advertise_model');
    }   
    
    private function __adv_get_table($ifa)
    {
        $hash=self::times33($ifa)%self::MAX_TABLE;
        $table=$this->_adv->getTable().'_'.$hash;
        if(!$this->_adv->table_exists($table))
        {
            return false;
        }
        return $table;
    }
    private function __adv_create_table($ifa)
    {
        $hash=self::times33($ifa)%self::MAX_TABLE;
        $table=$this->_adv->getTable().'_'.$hash;
        if(!$this->_adv->table_exists($table))
        {
            $create=pc_base::load_config('create','adv');
            $sql=str_replace('#TABLE', $table, $create);
            $this->_adv->query($sql);
        }
        return $table;
    }
    
    public function duomeng()
    {
        $url=pc_base::load_config('system','app_download_url');
        $ifa=isset($_REQUEST['ifa'])?$_REQUEST['ifa']:'';
        $appkey=isset($_REQUEST['appkey'])?$_REQUEST['appkey']:'';
        $mac=isset($_REQUEST['mac'])?$_REQUEST['mac']:'';
        $source=isset($_REQUEST['source'])?$_REQUEST['source']:'';
        $macmd5=isset($_REQUEST['macmd5'])?$_REQUEST['macmd5']:'';
        $ifamd5=isset($_REQUEST['ifamd5'])?$_REQUEST['ifamd5']:'';
        $imei=isset($_REQUEST['imei'])?$_REQUEST['imei']:'';
        if($ifa)
        {
             $table=$this->__adv_create_table($ifa);
             $this->_adv->setTable($table);
             $data=$this->_adv->get_one(array('ifa'=>$ifa));
             if(!$data)
             {
                $data=array('ifa'=>$ifa,'appkey'=>$appkey,'mac'=>$mac,'macmd5'=>$macmd5,'ifamd5'=>$ifamd5,'source'=>$source,'imei'=>$imei);
                $this->_adv->insert($data);
             }
             header('Location:'.$url);
        }
    }
    public function adv_callback()
    {
        $ifa=isset($_REQUEST['ifa'])?$_REQUEST['ifa']:'';
        $appkey=isset($_REQUEST['appkey'])?$_REQUEST['appkey']:'';
        $mac=isset($_REQUEST['mac'])?$_REQUEST['mac']:'';
        $sign=isset($_REQUEST['sign'])?$_REQUEST['sign']:'';
        $macmd5=isset($_REQUEST['macmd5'])?$_REQUEST['macmd5']:'';
        $ifamd5=isset($_REQUEST['ifamd5'])?$_REQUEST['ifamd5']:'';
        $imei=isset($_REQUEST['imei'])?$_REQUEST['imei']:'';
        $acttime=isset($_REQUEST['acttime'])?$_REQUEST['acttime']:'';
        $acctype=isset($_REQUEST['acctype'])?$_REQUEST['acctype']:'';
        $idfv=isset($_REQUEST['idfv'])?$_REQUEST['idfv']:'';
        $aaid=isset($_REQUEST['aaid'])?$_REQUEST['aaid']:'';
        $aid=isset($_REQUEST['aid'])?$_REQUEST['aid']:'';
        if($ifa)
        {
             $table=$this->__adv_create_table($ifa);
             $this->_adv->setTable($table);
             $data=$this->_adv->get_one(array('ifa'=>$ifa));
             if(isset($data['acttime'])&&$data['acttime']==0)
             {
                 $update=array(
                 'acttime'=>$acttime,'acctype'=>$acctype,
                 'macmd5'=>$macmd5,'mac'=>$mac,'imei'=>$imei,'sign'=>$sign,'appkey'=>$appkey,
                 'aaid'=>$aaid,'aid'=>$aid,'idfv'=>$idfv,
                 );
                 $this->_adv->update($update,array('ifa'=>$ifa));
             }else{
                 $data=array(
                 'acttime'=>$acttime,'acctype'=>$acctype,
                 'macmd5'=>$macmd5,'mac'=>$mac,'imei'=>$imei,'sign'=>$sign,'appkey'=>$appkey,
                 'aaid'=>$aaid,'aid'=>$aid,'idfv'=>$idfv,'ifa'=>$ifa
                 );
                 $this->_adv->insert($data);
             }
        }
    }
    
    
    public function juzhang()
    {
        $idfa=isset($_REQUEST['idfa'])?$_REQUEST['idfa']:'';
        $mac=isset($_REQUEST['mac'])?$_REQUEST['mac']:'';
        $source=isset($_REQUEST['source'])?$_REQUEST['source']:'juzhang';
        $ip=isset($_REQUEST['ip'])?$_REQUEST['ip']:'';
        if($idfa)
        {
             $table=$this->__adv_create_table($idfa);
             $this->_adv->setTable($table);
             $data=$this->_adv->get_one(array('ifa'=>$idfa));
             if(!$data)
             {
                $data=array('ifa'=>$idfa,'mac'=>$mac,'ip'=>$ip,'source'=>$source,);
                $this->_adv->insert($data);
             }
        }
    }
    
    public function check_ifa()
    {
        $idfa=isset($_POST['idfa'])?$_POST['idfa']:'';
        $idfas=explode('',$idfa);
        $ret=array();
        foreach ($idfas as $k=>$v)
        {
            $table=$this->__adv_get_table($v);
            if($table)
            {
                $this->_adv->setTable($table);
                $data=$this->_adv->get_one(array('ifa'=>$v));
                if(isset($data['acttime'])&&$data['acttime']!=0)
                {
                    $ret[$v]=1;
                }else{
                    $ret[$v]=1;
                }
            }else{
                $ret[$v]=0;
            }
        }
        echo json_encode($ret);
    }
    
}