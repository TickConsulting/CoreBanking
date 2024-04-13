<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Sacco_branches_m extends MY_Model {

	protected $_table = 'sacco_branches';

	function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->install();
	}

	public function install()
	{
		$this->db->query("
		create table if not exists sacco_branches(
			id int not null auto_increment primary key,
			`sacco_id` blob,
			`name` blob,
			`code` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	}

	public function insert($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('sacco_branches',$input);
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'sacco_branches',$input);
    }

	public function count_all($sacco_id = 0){
		if($sacco_id){
			$this->db->where($this->dx('sacco_id').' = "'.$sacco_id.'"',NULL,FALSE);
		}
		return $this->count_all_results('sacco_branches');
	}
	
	public function get_all($sacco_id = 0){	
		$this->select_all_secure('sacco_branches');
		if($sacco_id){
			$this->db->where($this->dx('sacco_id').' = "'.$sacco_id.'"',NULL,FALSE);
		}
        $this->db->order_by($this->dx('sacco_id'), 'ASC', FALSE);
        $this->db->order_by($this->dx('code').'+0', 'ASC', FALSE);
		return $this->db->get('sacco_branches')->result();
	}

	public function get($id = 0){	
		$this->select_all_secure('sacco_branches');
		$this->db->where('id',$id);
		return $this->db->get('sacco_branches')->row();
	}

	public function get_sacco_branch_options_by_sacco_id($sacco_id = 0){
		$arr = array();
		$this->select_all_secure('sacco_branches');
		$this->db->where($this->dx('sacco_id').' = '.$sacco_id,NULL,FALSE);
		$sacco_branches = $this->db->get('sacco_branches')->result();
		foreach($sacco_branches as $sacco_branch){
			$arr[$sacco_branch->id] = $sacco_branch->name;
		}
		return $arr;
	}

	function get_by_sacco($id=0,$sacco_id=0)
	{
		$this->select_all_secure('sacco_branches');
		$thi->db->where('id',$id);
		$this->db->where($this->dx('sacco_id').'="'.$sacco_id.'"');

		return $this->db->get('sacco_branches')->row();
	}

	
}