<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
class log extends admin {
    /**
     * @var MongoClient
     */
    private $_mongo;
    
    public function __construct() {
        $mongo=pc_base::load_config('mongo','mongo');
        $this->_mongo=new MongoClient($mongo['host']);
    }   
    
    public function report()
    {
//         ini_set('max_input_vars', 1000000);
//         print_r($_POST);
        if(isset($_POST['sid'])&&isset($_POST['data']))
        {
            $month=date('Y-m');
            try {
                if($this->_mongo->selectDB($_POST['sid'])->selectCollection($month)->count()==0)
                {
                    $this->_mongo->selectDB($_POST['sid'])->selectCollection($month)->createIndex(array('UID'=>1),array('background'=>1));
                    $this->_mongo->selectDB($_POST['sid'])->selectCollection($month)->createIndex(array('MODULE'=>1),array('background'=>1));
                    $this->_mongo->selectDB($_POST['sid'])->selectCollection($month)->createIndex(array('ACTION'=>1),array('background'=>1));
                }
                $this->_mongo->selectDB($_POST['sid'])->selectCollection($month)->batchInsert($_POST['data']);
            } catch (Exception $e) {
                print_r($e->getMessage());
            }
        }
    }
    
}