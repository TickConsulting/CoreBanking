<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Saccos_m extends MY_Model {

	protected $_table = 'saccos';

	function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->install();
	}

	public function install()
	{
		$this->db->query("
		create table if not exists saccos(
			id int not null auto_increment primary key,
			`name` blob,
			`slug` blob,
			`partner` blob,
			`logo` blob,
			`primary_color` blob,
			`secondary_color` blob,
			`tertiary_color` blob,
			`text_color` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	}

	public function insert($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('saccos',$input);
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'saccos',$input);
    }

	public function count_all(){
		return $this->count_all_results('saccos');
	}
	
	public function get_all(){	
		$this->select_all_secure('saccos');
		$this->db->order_by($this->dx('name'), 'ASC', FALSE);
		return $this->db->get('saccos')->result();
	}

	public function get($id = 0){	
		$this->select_all_secure('saccos');
		$this->db->where('id',$id);
		return $this->db->get('saccos')->row();
	}

	public function get_by_slug($slug = ''){	
		$this->select_all_secure('saccos');
		$this->db->where($this->dx('slug').' = "'.$slug.'"',NULL,FALSE);
		return $this->db->get('saccos')->row();
	}

	public function get_admin_sacco_options(){
		$arr = array();
		$this->select_all_secure('saccos');
		$saccos = $this->db->get('saccos')->result();
		foreach($saccos as $sacco){
			if($sacco->active){
				$status = '';
			}else{
				$status = "- ( Hidden )";
			}
			$arr[$sacco->id] = $sacco->name.' '.$status;
		}
		return $arr;
	}

	function get_active_saccos(){
		$this->select_all_secure('saccos');
		$this->db->where($this->dx('active'), ' = "1"',NULL,FALSE);
		$this->db->order_by($this->dx('name'), 'ASC', FALSE);
		return $this->db->get('saccos')->result();
	}

	function get_group_sacco_options($country_id=0){
		$this->select_all_secure('saccos');
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		if($country_id){
			$this->db->where($this->dx('country_id').' = "'.$country_id.'"',NULL,FALSE);
		}
		$saccos = $this->db->get('saccos')->result();

		$arr = array();
		if($saccos)
		{
			foreach ($saccos as $sacco) 
			{
				$arr[$sacco->id] = $sacco->name;
			}
		}
		
		return $arr;
	}
}