<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
ini_set('html_errors','1');
error_reporting(E_ALL);

class broad extends admin {

    /**
     * @var account_model
     */
    private $_platformArray;
    
    public function __construct() {

        $this->db = pc_base::load_model('admin_role_model');
        /**
         * 添加管理员 分管平台权限
         */
        $roleid=$_SESSION['roleid'];
        $platform=$this->db->get_one('`roleid`='.$roleid,'`platform`');
        $platform['platform']=str_replace(' ','',$platform['platform']);
        $platform['platform']=str_replace('，',',',$platform['platform']);
        $this->_platformArray=explode(",",$platform['platform']);
        
    }   
    
    public function  index()
    {
        $broad=pc_base::load_config('system','broad_url');
        $files_name=pc_base::load_config('notice','files_name');
        $file_name=array();
        $platformArray=$this->_platformArray;
        foreach ($files_name as $k=>$v)
        {
            if(in_array($k , $platformArray) || empty($platformArray[0]))
            {
                $file_name=array_merge($file_name,$v);
            }
        }
        $html=array_keys($file_name);
        $content=file_get_contents($broad.$html[0]);
        include $this->admin_tpl('broad','gm');

    }

    public function  file_content(){
        $post=$_POST;
        $ret=array('ret'=>1,'msg'=>'','content'=>'');
        $broad=pc_base::load_config('system','broad_url');
        $ret['content']=file_get_contents($broad.$post['file']);
        if(empty($ret['content'])){return array('ret'=>0,'msg'=>'内容为空');}
        echo json_encode($ret) ;
    }

    public function  save() 
    {
        $ret=array('msg'=>'');
        if(isset($_POST['content']))
        {
            $broad=pc_base::load_config('system','broad_url');
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $broad.'upBroad.php');
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('content'=>stripslashes($_POST['content']),'file'=>$_POST['file']));
            curl_exec($ch);
            curl_close($ch);
            $ret['msg']='保存成功';
            self::manage_log();
        }else{
            $ret['msg']='缺少参数';
        }
        echo json_encode($ret);
    }
    
    public function update()
    {
//        if(isset($_POST['content']))
//        {
//            file_put_contents('gg.html', $_POST['content']);
//        }
    }
}
