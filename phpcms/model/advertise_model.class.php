<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class advertise_model extends model {
	public function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'advertise';
		$this->table_name = 'advertise';
		parent::__construct();
	}
	
    public function changeConnection($serverid)
    {
        $this->db->close();
        $this->db_setting='account_s'.$serverid;
        if (!isset($this->db_config[$this->db_setting])) {
            exit(json_encode(array('ret'=>10,'msg'=>'no_such_db_config_serverid='.$serverid)));
            $this->db_setting = 'default';
        }
        try {
              $this->db = db_factory::get_instance($this->db_config)->get_database($this->db_setting);
              $this->db->connect();
              return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table_name=$table;
    }
    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table_name;
    }
}
?>