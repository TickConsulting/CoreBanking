<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Coupons_m extends MY_Model{

	protected $_table = 'coupons';

	function __construct(){
		parent::__construct();
		//$this->install();
	}

	function install(){
		$this->db->query("
			create table if not exists coupons(
				id int not null auto_increment primary key,
				`type` blob,
				`name` blob,
				`percentage_value` blob,
				`fixed_amount` blob,
				`coupon_waiver_type` blob,
				`partial_waiver_period` blob,
				`partial_waiver_start_date` blob,
				`expiry_date` blob,
				`distribution_limit` blob,
				`limited_users` blob,
				`active` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_by` blob,
				`modified_on` blob
			)"
		);

		$this->db->query("
			create table if not exists coupon_usages(
				id int not null auto_increment primary key,
				`coupon_id` blob,
				`group_id` blob,
				`user_id` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_by` blob,
				`modified_on` blob
			)"
		);
	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('coupons',$input);
	}

	function update($id,$input=array(),$SKIP_VALIDATION = FALSE){
    	return $this->update_secure_data($id,'coupons',$input);
	}

	function get($id=0){
		$this->select_all_secure('coupons');
		$this->db->where('id',$id);
		return $this->db->get('coupons')->row();
	}

	function get_all(){
		$this->select_all_secure('coupons');
		return $this->db->get('coupons')->result();
	}

	function count_all(){
		return $this->db->count_all_results('coupons');
	}

	function get_all_active(){
		$this->select_all_secure('coupons');
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('coupons')->result();
	}

	function get_coupon($coupon = ''){
		$this->select_all_secure('coupons');
		$this->db->where("CONVERT(".$this->dx('coupon')." using 'latin1') = '".$this->db->escape_str($coupon)."'",NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('coupons')->row();
	}


	/********************coupon_usages****************************/

	function insert_coupon_usages($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('coupon_usages',$input);
	}

	function insert_unique_coupon_usages($coupon_id=0,$group_id=0,$input){
		if($this->get_group_inactive_usage_coupon($coupon_id,$group_id)){
			if($this->delete_coupon_usage($coupon_id,$group_id)){
				return $this->insert_coupon_usages($input);
			}
		}else{
			return $this->insert_coupon_usages($input);
		}
	}

	function get_group_active_usage_coupon($coupon_id=0,$group_id=0){
		$this->select_all_secure('coupon_usages');
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('coupon_id').'= "'.$coupon_id.'"',NULL,FALSE);
		$this->db->where($this->dx('group_id').'= "'.$group_id.'"',NULL,FALSE);
		return $this->db->get('coupon_usages')->row();
	}

	function get_group_inactive_usage_coupon_by_coupon($coupon_id=0,$group_id=0){
		$this->select_all_secure('coupon_usages');
		$this->db->where($this->dx('active').' = "0"',NULL,FALSE);
		$this->db->where($this->dx('coupon_id').'= "'.$coupon_id.'"',NULL,FALSE);
		$this->db->where($this->dx('group_id').'= "'.$group_id.'"',NULL,FALSE);
		return $this->db->get('coupon_usages')->row();
	}

	function get_group_inactive_usage_coupon($group_id=0){
		$this->select_all_secure('coupon_usages');
		$this->db->where($this->dx('active').' = "0"',NULL,FALSE);
		$this->db->where($this->dx('group_id').'= "'.$group_id.'"',NULL,FALSE);
		return $this->db->get('coupon_usages')->row();
	}

	function delete_coupon_usage($coupon_id=0,$group_id=0){
		$this->db->where($this->dx('coupon_id').'= "'.$coupon_id.'"',NULL,FALSE);
		$this->db->where($this->dx('group_id').'= "'.$group_id.'"',NULL,FALSE);
		$this->db->delete('coupon_usages');
	}

	function update_coupon_usages($id,$input=array(),$SKIP_VALIDATION = FALSE){
    	return $this->update_secure_data($id,'coupon_usages',$input);
	}


	function count_used_coupons($coupon_id=0){
		$this->db->where($this->dx('coupon_id').' = "'.$coupon_id.'"',NULL,FALSE);
		return $this->db->count_all_results('coupon_usages')?:0;
	}
}