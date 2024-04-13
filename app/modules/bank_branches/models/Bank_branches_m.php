<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Bank_branches_m extends MY_Model {

	protected $_table = 'bank_branches';

	function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->install();
	}

	public function install(){
		$this->db->query("
		create table if not exists bank_branches(
			id int not null auto_increment primary key,
			`bank_id` blob,
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
		return $this->insert_secure_data('bank_branches',$input);
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'bank_branches',$input);
    }

	public function count_all($bank_id = 0){
		if($bank_id){
			$this->db->where($this->dx('bank_id').' = "'.$bank_id.'"',NULL,FALSE);
		}
		return $this->count_all_results('bank_branches');
	}
	
	public function get_all($bank_id = 0){	
		$this->select_all_secure('bank_branches');
		if($bank_id){
			$this->db->where($this->dx('bank_id').' = "'.$bank_id.'"',NULL,FALSE);
		}
        // $this->db->order_by($this->dx('bank_id'), 'ASC', FALSE);
        // $this->db->order_by($this->dx('code').'+0', 'ASC', FALSE);
        $this->db->order_by('id','ASC');
		return $this->db->get('bank_branches')->result();
	}

	public function get($id = 0){	
		$this->select_all_secure('bank_branches');
		$this->db->where('id',$id);
		return $this->db->get('bank_branches')->row();
	}

	public function get_bank_branch_options_by_bank_id($bank_id = 0){
		$arr = array();
		$this->select_all_secure('bank_branches');
		if($bank_id){
			$this->db->where($this->dx('bank_id').' = '.$bank_id,NULL,FALSE);
		}
		$bank_branches = $this->db->get('bank_branches')->result();
		foreach($bank_branches as $bank_branch){
			$arr[$bank_branch->id] = $bank_branch->name;
		}
		return $arr;
	}

	function get_by_bank($id=0,$bank_id=0)
	{
		$this->select_all_secure('bank_branches');
		$this->db->where('id',$id);
		$this->db->where($this->dx('bank_id').'="'.$bank_id.'"');

		return $this->db->get('bank_branches')->row();
	}

	function delete_branch_branches_by_bank_id($bank_id = 0){
		$this->db->where($this->dx('bank_id')." = '".$bank_id."' ",NULL,FALSE);
		return $this->db->delete('bank_branches');
	}

	function get_online_banking_headoffice(){
		$this->select_all_secure('bank_branches');
		$this->db->where($this->dx('banks.wallet').' = "1"',NULL,FALSE);
		$this->db->limit(1);
		$this->db->join('banks',$this->dx('bank_branches.bank_id').' = banks.id');
		return $this->db->get('bank_branches')->row();
	}

	

	
}