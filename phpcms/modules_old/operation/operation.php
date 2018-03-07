<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
ini_get($varname);
class operation extends admin {
     /**
     * @var model
     */
    private $_language;
    public function __construct() {
        
    }   
    
    public function  itemProp()
    {
        $items=$this->get_config();
        include $this->admin_tpl('prop','operation');
    }
    
    public function  file_upload(){
        if($_SESSION['roleid']!=1){exit('没有权限');}
        $ret = '';
        if($_FILES["upload"]['type']='text/plain')
        {
            if ($_FILES["upload"]["error"] > 0)
            {
                echo "Return Code: " . $_FILES["upload"]["error"] . "<br />";
            }else{
        
                if (file_exists(PHPCMS_PATH."statics/config/".$_FILES['upload']['name'])||file_exists(PHPCMS_PATH."statics/config/".$_FILES['upload2']['name']))
                {
                    unlink(PHPCMS_PATH.'statics/config/'.$_FILES['upload']['name']);
                    unlink(PHPCMS_PATH.'statics/config/'.$_FILES['upload2']['name']);
                    move_uploaded_file($_FILES["upload"]["tmp_name"],"statics/config/".$_FILES['upload']['name']);
                    move_uploaded_file($_FILES["upload2"]["tmp_name"],"statics/config/".$_FILES['upload2']['name']);
                    $ret = '文件已更新';
                }
                else
                {
                    // move_uploaded_file($_FILES["upload"]["tmp_name"],"uploadfile/" . $_FILES["upload"]["name"]);
                    move_uploaded_file($_FILES["upload"]["tmp_name"],"statics/config/".$_FILES['upload']['name']);
                    move_uploaded_file($_FILES["upload2"]["tmp_name"],"statics/config/".$_FILES['upload2']['name']);
                    $ret = "上传成功";
                }
            }
        }else{
            $ret =  "文件格式不正确";
        }
        $items="var items= ".json_encode($this->get_config());
        $types="var types=[{'type':1,'name':'消耗类'},{'type':2,'name':'使用类'},{'type':3,'name':'武将'},{'type':4,'name':'武将碎片'},{'type':5,'name':'宝石'},{'type':6,'name':'装备坐骑'},{'type':7,'name':'新装备坐骑'},{'type':11,'name':'神兵'},{'type':12,'name':'天赋材料类'},{'type':14,'name':'宝箱'},{'type':15,'name':'碎片'},{'type':16,'name':'图纸'}];
";
        file_put_contents(PHPCMS_PATH.'statics/config/config.js',$items.PHP_EOL.$types);
        echo $ret;
    }
   
}
