<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Loan_types_m extends MY_Model{

	protected $_table = 'loan_types';

	function __construct(){
		parent::__construct();
		$this->install();
	}

	function install(){
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `loan_types` (
				`id` int not null auto_increment primary key,
			  	`name` blob,
			  	`group_id` blob,
			  	`minimum_loan_amount` blob,
			  	`maximum_loan_amount` blob,
			  	`minimum_repayment_period` blob,
			  	`maximum_repayment_period` blob,
			  	`interest_rate` blob,
			  	`interest_type` blob,
			 	`loan_interest_rate_per` blob,
			 	`grace_period` blob,
			 	`enable_loan_fines` blob,
			 	`loan_fine_type` blob,
			  	`fixed_fine_amount` blob,
			  	`fixed_amount_fine_frequency` blob,
			  	`fixed_amount_fine_frequency_on` blob,
			  	`percentage_fine_rate` blob,
			  	`percentage_fine_frequency` blob,
			  	`percentage_fine_on` blob,
			  	`one_off_fine_type` blob,
			  	`one_off_fixed_amount` blob,
			  	`one_off_percentage_rate` blob,
			  	`one_off_percentage_rate_on` blob,
			  	`enable_outstanding_loan_balance_fines` blob,
			  	`outstanding_loan_balance_fine_type` blob,
			  	`outstanding_loan_balance_fine_fixed_amount` blob,
			  	`outstanding_loan_balance_fixed_fine_frequency` blob,
			  	`outstanding_loan_balance_percentage_fine_rate` blob,
			  	`outstanding_loan_balance_percentage_fine_frequency` blob,
			  	`outstanding_loan_balance_percentage_fine_on` blob,
			  	`outstanding_loan_balance_fine_one_off_amount` blob,
			  	`outstanding_loan_balance_fine_date` blob,
			  	`enable_loan_processing_fee` blob,
			  	`loan_processing_fee_type` blob,
			  	`loan_processing_fee_fixed_amount` blob,
			  	`loan_processing_fee_percentage_rate` blob,
			 	`loan_processing_fee_percentage_charged_on` blob,
			  	`enable_loan_fine_deferment` blob,
			  	`enable_loan_guarantors` blob,
			  	`minimum_guarantors` blob,
			  	`maximum_guarantors` blob,
			  	`active` blob,
			  	`is_hidden` blob,
			  	`created_by` blob,
			  	`created_on` blob,
			  	`modified_by` blob,
			  	`modified_on` blob
		 	);
		");

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `loan_types_groups_match` (
				`id` int not null auto_increment primary key,
				`loan_type_id` BLOB,
				`group_id` BLOB,
				`active` blob,
			  	`created_by` blob,
			  	`created_on` blob,
			  	`modified_by` blob,
			  	`modified_on` blob
		 	);
		");

	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('loan_types',$input);
	}

	function update($id,$input,$val=FALSE){
	    return $this->update_secure_data($id,'loan_types',$input);
	}

	function insert_match($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('loan_types_groups_match',$input);
	}

	function insert_batch_match($input,$skip_validation=FALSE){
        return $this->insert_batch_secure_data('loan_types_groups_match',$input);
    }

    function get_loan_types_match($loan_type_id=0){
    	$this->select_all_secure('loan_types_groups_match');
    	if($loan_type_id){
    		$this->db->where($this->dx('loan_type_id').' = "'.$loan_type_id.'"',NULL,FALSE);
    	}else{
    		$this->db->select(array(
    			$this->dx('investment_groups.name').' name'
    		));
    		$this->db->join('investment_groups',$this->dx('loan_types_groups_match.group_id').' = investment_groups.id');
    	}
    	$matches = $this->db->get('loan_types_groups_match')->result();
    	$arr = array();
    	if($loan_type_id){
    		foreach ($matches as $match) {
    			$arr[$match->id] = $match->group_id;
    		}
    	}else{
    		foreach ($matches as $match) {
    			$arr[$match->loan_type_id][$match->id] = $match->name;
    		}
    	}
    	return $arr;
    }

    function get_admin_loan_types_for_group($group_id=0){
    	$this->db->select(array(
    		$this->dx('loan_type_id').' as id',
    	));
    	$results = $this->db->get('loan_types_groups_match')->result();
    	$loan_type_ids = '';
    	foreach ($results as $result) {
    		$loan_type_ids = $loan_type_ids?','.$result->id:$result->id;
    	}
    	if(!$loan_type_ids){
    		$this->select_all_secure('loan_types');
    		$this->db->where_in('id',$loan_type_ids);
			$this->db->where($this->dx('is_admin').'="1"',NULL,FALSE);

    		return $this->db->get('loan_types')->result();
    	}

    }

    function get_admin_loan_types_options_for_group($group_id=0){
    	$this->db->select(array(
    		$this->dx('loan_type_id').' as loan_type_id',
    		$this->dx('loan_types.name').' as loan_type_name',
    	));
    	$this->db->where($this->dx('loan_types_groups_match.group_id').' = "'.$group_id.'"');
    	$this->db->join('loan_types','loan_types.id = '.$this->dx('loan_type_id'));
    	$results = $this->db->get('loan_types_groups_match')->result();
    	if($results){
    		$options = array();
    		foreach ($results as $result) {
	    		$options[$result->loan_type_id] = $result->loan_type_name;
	    	}
    		return $options;
    	}else{
    		return $results;
    	}
    }

	function delete_loan_types_match($loan_type_id=0){
		$this->db->where($this->dx('loan_type_id').' = "'.$loan_type_id.'"',NULL,FALSE);
		return $this->db->delete('loan_types_groups_match');
	}

	function get($id=0,$group_id=0){
	    $this->select_all_secure('loan_types');
	    $this->db->where('id',$id);
	  
	    $this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
	    return $this->db->get('loan_types')->row();
	}

	function get_all($filter_params=array()){
    	$this->select_all_secure('loan_types');
    	$this->db->where($this->dx('active').'="1"',NULL,FALSE);	
    	$this->db->where($this->dx('is_admin').'="1"',NULL,FALSE);	
    	if($filter_params){
			foreach ($filter_params as $key => $value) {
				if($value && $this->db->field_exists($key, 'loan_types')){
					$this->db->where("
					CONVERT(" . $this->dx($key) . " USING 'latin1')  like '%" . $this->escape_str($value) . "%'
					",NULL,FALSE);
				}
			}
		}
    	$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
    	return $this->db->get('loan_types')->result();
	}

	function count_all(){
    	$this->db->where($this->dx('active').'="1"',NULL,FALSE);	
    	$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
    	return $this->db->count_all_results('loan_types');
    }

    function count_group_loan_types($group_id = 0,$filter_params = array()){
    	
    	if($filter_params){
			foreach ($filter_params as $key => $value) {
				if($value && $this->db->field_exists($key, 'loan_types')){
					$this->db->where("
					CONVERT(" . $this->dx($key) . " USING 'latin1')  like '%" . $this->escape_str($value) . "%'
					",NULL,FALSE);
				}
			}
		}
    	$this->db->where($this->dx('active').'="1"',NULL,FALSE);	
    	$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
    	return $this->db->count_all_results('loan_types');
    }

    function get_group_loan_types($group_id = 0){
    	$this->select_all_secure('loan_types');
    	
    	$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
    	return $this->db->get('loan_types')->result();
    }

    function get_admin_loan_types(){
    	$this->select_all_secure('loan_types');
    	$this->db->where($this->dx('is_admin').'="1"',NULL,FALSE);	
    	$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
    	return $this->db->get('loan_types')->result();
    }

    function get_active_group_loan_types($group_id = 0){
    	$this->select_all_secure('loan_types');
    	
    	$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
    	$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
    	return $this->db->get('loan_types')->result();
    }

    function get_options($group_id=0){
    	$this->select_all_secure('loan_types');
    	
    	$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
	    $loan_types =  $this->db->get('loan_types')->result();
	    $arr = array();
	    foreach ($loan_types as $loan_type) {
	    	$arr[$loan_type->id] = $loan_type->name;
	    }
	    $loan_types = $this->get_admin_loan_types_options_for_group();
	    return $loan_types?$arr+$loan_types:$arr;
    }

    function get_specific_options($id,$group_id){
    	$this->select_all_secure('loan_types');
    	
    	$this->where('id',$id);
    	$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
	    $loan_types =  $this->db->get('loan_types')->result();

	    $arr = array();

	    foreach ($loan_types as $loan_type) {
	    	$arr[$loan_type->id] = $loan_type->name;
	    }

	    return $arr;

    }

    function safe_delete($id=0,$group_id=0){
		$this->db->where('id',$id);
		return $this->update_secure_data($id,'loan_types',array('is_deleted'=>1,'modified_on'=>time()));
	}
   
   	function get_search_options(){
   		$result = new stdClass();
   		$query = trim($this->input->get('q'));
   		$this->select_all_secure('loan_types');	
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);	
		$this->db->where(" ( 
					CONVERT(" . $this->dx('loan_types.name') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%'
			)", NULL, FALSE);
		$this->db->order_by($this->dx('loan_types.created_on'),'DESC',FALSE);
		$this->db->order_by($this->dx('loan_types.name'),'DESC',FALSE);
		$loan_types = $this->db->get('loan_types')->result();
		$result->total_count = count($loan_types);
		$result->incomplete_results = false;
		$result->items = $loan_types;
		echo json_encode($result);   	
   	}

   	function get_group_loan_type($id = 0, $group_id = 0){
   		$this->select_all_secure('loan_types');
   		
    	$this->where('id',$id);
    	$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
	    return $this->db->get('loan_types')->row();
   	}
   	
   	function get_option_objects_array($group_id = 0){
    	$this->select_all_secure('loan_types');
    	if($group_id){
    		$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
    	}else{
    		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
    	}
    	$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
	    $loan_types =  $this->db->get('loan_types')->result();
	    $arr = array();
	    foreach ($loan_types as $loan_type) {
	    	$arr[$loan_type->id] = $loan_type;
	    }
	    return $arr;
    }
}?>