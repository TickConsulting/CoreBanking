<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Partners_m extends MY_Model {

	function __construct(){
		parent::__construct();
		$this->load->dbforge();
		$this->install();
	}

	function install(){
		$this->db->query("
			create table if not exists partners(
				id int not null auto_increment primary key,
				`name` blob,
				`slug` blob,
				`active` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_by` blob,
				`modified_on` blob
			)"
		);

		$this->db->query("
			create table if not exists user_partner_pairings(
				id int not null auto_increment primary key,
				`partner_id` blob,
				`user_id` blob,
				`created_by` blob,
				`created_on` blob
			)"
		);

		$this->db->query("
			create table if not exists partner_commission_matrices(
				id int not null auto_increment primary key,
				`partner_id` blob,
				`commission_type` blob,
				`minimum_group_number` blob,
				`maximum_group_number` blob,
				`percentage` blob,
				`active` blob,
				`fixed_amount` blob,
				`modified_on` blob,
				`modified_by` blob,
				`created_by` blob,
				`created_on` blob
			)"
		);

		$this->db->query("
			create table if not exists partner_commission_types(
				id int not null auto_increment primary key,
				`partner_id` blob,
				`commission_type` blob,
				`active` blob,
				`modified_on` blob,
				`modified_by` blob,
				`created_by` blob,
				`created_on` blob
			)"
		);
	}

	public function insert($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('partners',$input);
	}

	public function insert_user_partner_pairing($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('user_partner_pairings',$input);
	}

	public function insert_partner_commission_matrix($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('partner_commission_matrices',$input);
	}

	public function insert_partner_commission_type($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('partner_commission_types',$input);
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'partners',$input);
    }

    function get_partner_commission_matrices($partner_id = 0){
    	$this->select_all_secure('partner_commission_matrices');
		$this->db->where($this->dx('partner_id')." = '".$partner_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		return $this->db->get('partner_commission_matrices')->result();
    }

    function get_partner_commission_type($partner_id = 0){
    	$this->select_all_secure('partner_commission_types');
		$this->db->where($this->dx('partner_id')." = '".$partner_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		return $this->db->get('partner_commission_types')->row();
    }

	public function get($id = 0){
		$this->select_all_secure('partners');
		$this->db->where('id',$id);
		$this->db->limit(1);
		return $this->db->get('partners')->row();
	}

	public function delete_user_partner_pairings($partner_id = 0){
		$this->db->where($this->dx('partner_id')." = '".$partner_id."' ",NULL,FALSE);
		return $this->db->delete('user_partner_pairings');
	}

	public function delete_partner_commission_matrices($partner_id = 0){
		$this->db->where($this->dx('partner_id')." = '".$partner_id."' ",NULL,FALSE);
		return $this->db->delete('partner_commission_matrices');
	}

	public function delete_partner_commission_type($partner_id = 0){
		$this->db->where($this->dx('partner_id')." = '".$partner_id."' ",NULL,FALSE);
		return $this->db->delete('partner_commission_types');
	}

	function get_user_partner_pairings_array($partner_id = 0){
		$arr = array();
		$this->select_all_secure('user_partner_pairings');
		$this->db->where($this->dx('partner_id')." = '".$partner_id."' ",NULL,FALSE);
		$user_partner_pairings = $this->db->get('user_partner_pairings')->result();
		foreach($user_partner_pairings as $user_partner_pairing):
			$arr[] = $user_partner_pairing->user_id;
		endforeach;
		return $arr;
	}


	public function get_all(){
		$this->select_all_secure('partners');
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		return $this->db->get('partners')->result();
	}

	public function count_all(){
		return $this->db->count_all_results('partners');
	}

	function get_by_slug($slug = "",$id = ''){
		$this->select_all_secure('partners');
		$this->db->where($this->dx('slug').'="'.$slug.'"',NULL,FALSE);
		if($id){
			$this->db->where('id !=',$id);
		}
		return $this->db->get('partners')->row();
	}

	function get_accounts_managed_by_user($user_id = 0){
		$this->select_all_secure('partners');
		if($user_id){
			$this->db->where($this->dx('user_partner_pairings.user_id').' = "'.$user_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('user_partner_pairings.user_id').' = "'.$this->user->id.'"',NULL,FALSE);
		}		
		$this->db->join('user_partner_pairings',$this->dx('user_partner_pairings.partner_id')." = partners.id");
		return $this->db->get('partners')->result();
	}

}

	