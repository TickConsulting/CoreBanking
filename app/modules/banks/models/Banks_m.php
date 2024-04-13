<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Banks_m extends MY_Model {

	protected $_table = 'banks';

	function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->install();
	}

	public function install(){
		$this->db->query("
		create table if not exists banks(
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

		$this->db->query("
		create table if not exists user_bank_pairings(
			id int not null auto_increment primary key,
			`user_id` blob,
			`bank_id` blob,
			created_by blob,
			created_on blob
		)");
	}

	public function insert($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('banks',$input);
	}

	public function insert_user_bank_pairing($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('user_bank_pairings',$input);
	}

	public function get_user_bank_pairings_array($user_id = 0){
		$arr = array();
		$this->select_all_secure('user_bank_pairings');
		$this->db->where($this->dx('user_id').' = "'.$user_id.'"',NULL,FALSE);
		$user_bank_pairings = $this->db->get('user_bank_pairings')->result();
		foreach($user_bank_pairings as $user_bank_pairing):
			$arr[] = $user_bank_pairing->bank_id;
		endforeach;
		return $arr;
	}

	function check_user_bank_pairing($user_id = 0,$bank_id = 0){
		$this->db->where($this->dx('user_id').' = "'.$user_id.'"',NULL,FALSE);
		$this->db->where($this->dx('bank_id').' = "'.$bank_id.'"',NULL,FALSE);
		return $this->count_all_results('user_bank_pairings');
	}

	function delete_user_bank_pairings($user_id = 0){
		$this->db->where($this->dx('user_id').' = "'.$user_id.'"',NULL,FALSE);
		return $this->db->delete('user_bank_pairings');
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'banks',$input);
    }

	public function count_all(){
		return $this->count_all_results('banks');
	}
	
	public function get_all(){	
		$this->select_all_secure('banks');
		//$this->db->order_by($this->dx('name'), 'ASC', FALSE);
		$this->db->order_by('id','ASC');
		return $this->db->get('banks')->result();
	}


	public function get($id = 0){	
		$this->select_all_secure('banks');
		$this->db->where('id',$id);
		return $this->db->get('banks')->row();
	}

	function get_bank_and_country($id=0){
		$this->select_all_secure('banks');
		$this->select_all_secure('countries');
		$this->db->where('banks.id',$id);
		$this->db->join('countries',$this->dx('banks.country_id').' = countries.id');
		return $this->db->get('banks')->row();
	}

	public function is_partner($id = 0){
		$this->db->select(
			array($this->dx('partner').' as partner')
		);
		$this->db->where('id',$id);
		$bank = $this->db->get('banks')->row();
		if($bank){
			if($bank->partner){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	public function get_by_slug($slug = ''){	
		$this->select_all_secure('banks');
		$this->db->where($this->dx('slug').' = "'.$slug.'"',NULL,FALSE);
		return $this->db->get('banks')->row();
	}

	public function get_admin_bank_options(){
		$arr = array();
		$this->select_all_secure('banks');
		$banks = $this->db->get('banks')->result();
		foreach($banks as $bank){
			if($bank->active){
				$status = '';
			}else{
				$status = "- ( Hidden )";
			}
			$arr[$bank->id] = $bank->name.' '.$status;
		}
		return $arr;
	}

	public function get_partner_banks(){
		$this->select_all_secure('banks');
		$this->db->where($this->dx('partner').' = "1"',NULL,FALSE);
		return $this->db->get('banks')->result();
	}

	public function get_partner_bank_options(){
		$arr = array();
		$banks = $this->get_partner_banks();
		foreach($banks as $bank){
			if($bank->active){
				$status = '';
			}else{
				$status = "- ( Hidden )";
			}
			$arr[$bank->id] = $bank->name.' '.$status;
		}
		return $arr;
	}

	function get_group_bank_options($get_wallet=FALSE,$country_id=0)
	{
		$this->select_all_secure('banks');
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		if($get_wallet){
			$this->db->where($this->dx('wallet').' = 1',NULL,FALSE);
		}else{
			$this->db->where('('.$this->dx('wallet').' IS NULL  OR '.$this->dx('wallet').' = "0")',NULL,FALSE);
		}
		if($country_id){
			$this->db->where($this->dx('country_id').' = "'.$country_id.'"',NULL,FALSE);
		}
		$banks = $this->db->get('banks')->result();
		$arr = array();
		if($banks)
		{
			foreach ($banks as $bank) 
			{
				$arr[$bank->id] = $bank->name;
			}
		}
		
		return $arr;
	}

	function get_bank_id_by_slug($slug = ''){
		if($bank = $this->get_by_slug($slug)){
			return $bank->id;
		}else{
			return 0;
		}
	}

	public function get_default_bank(){
		$this->select_all_secure('banks');
		$this->db->where($this->dx('default_bank').' = 1',NULL,FALSE);
		return $this->db->get('banks')->row();
	}
}