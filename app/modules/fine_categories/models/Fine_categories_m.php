<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Fine_categories_m extends MY_Model {

	protected $_table = 'fine_categories';

	function __construct(){
		$this->load->dbforge();
		//$this->install();
	}

	/**
		**
		expense categories
		active description
		1. The category is active
		''. The category is inactive

		is_hidden
		1. the category is hidden from selection
		''. the category is not hidden

	**/

	function install()
	{
		$this->db->query("
		create table if not exists fine_categories(
					id int not null auto_increment primary key,
					`name` BLOB,
					`slug` BLOB,
					`description` BLOB,
					`amount` BLOB,
					`group_id` BLOB,
					`active` BLOB,
					`is_hidden` BLOB,
					created_by BLOB,
					created_on BLOB,
					modified_on BLOB,
					modified_by BLOB
				)");
	}

	function insert($input,$skip_validation=FALSE)
	{
		return $this->insert_secure_data('fine_categories',$input);
	}

	function insert_batch($input,$skip_validation=FALSE)
	{
		return $this->insert_batch_secure_data('fine_categories',$input);
	}

	function get_all($group_id = 0)
	{
		$this->select_all_secure('fine_categories');
		if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		$this->db->order_by($this->dx('name'), 'DESC',FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->get('fine_categories')->result();
	}


	function count_all_group_expense_categories($group_id = 0)
	{
		if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->count_all_results('fine_categories')?:0;
	}
	
	
	function get($id = 0){
		$this->select_all_secure('fine_categories');
		$this->db->where('id',$id);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->get('fine_categories')->row();
	}

	function get_group_fine_category($id = 0,$group_id = 0){
		$this->select_all_secure('fine_categories');
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('fine_categories')->row();
	}

	function check_group_fine_category($id = 0,$group_id = 0){
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->count_all_results('fine_categories')?:0;
	}

	function get_by_slug($slug,$id='',$group_id=''){
		$this->select_all_secure('fine_categories');
		$this->db->where($this->dx('slug').'="'.$slug.'"',NULL,FALSE);
		if($id){
			$this->db->where('id !=',$id);
		}
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->get('fine_categories')->row();
	}

	function get_group_options($show_fine_amount = TRUE,$group_id=0){
		$arr = array();
		$this->db->select(array(
			'id',
			$this->dx('name').' as name',
			$this->dx('amount').' as amount',
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$fine_categories = $this->db->get('fine_categories')->result();
		foreach($fine_categories as $fine_category){
			if($show_fine_amount){
				$arr[$fine_category->id] = $fine_category->name;
			}else{
				$arr[$fine_category->id] = $fine_category->name;
			}
		}
		return $arr;
	}


	function count_all($params = array())
	{
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->count_all_results('fine_categories');
	}



	function update($id, $input,$skip_validation = false)
	{
		return $this->update_secure_data($id,'fine_categories',$input);
	}

	function get_group_fine_category_options($disable_option_groups = FALSE){
    	$contribution_options = array();
    	$fine_category_options = array();

    	$this->db->select(array(
			'id',
			$this->dx('amount').' as amount',
			$this->dx('name').' as name',
			)
		);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$fine_categories = $this->db->get('fine_categories')->result();
		foreach($fine_categories as $fine_category){
			$fine_category_options['fine_category-'.$fine_category->id] = $fine_category->name.' - '.$this->group_currency.' '.number_to_currency($fine_category->amount);
		}

		$this->db->select(array(
			'id',
			$this->dx('name').' as name',
			)
		);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$contributions = $this->db->get('contributions')->result();
		foreach($contributions as $contribution){
			$contribution_options['contribution-'.$contribution->id] = $contribution->name.' fine ';
		}
		if($disable_option_groups){
			return $fine_category_options + $contribution_options;
		}else{
			return array(
	            'Fine Categories' => $fine_category_options,
	            'Contributions'   => $contribution_options,
	        );
		}
	}


	function safe_delete($id=0,$group_id=0){
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->update_secure_data($id,'fine_categories',array('is_deleted'=>1,'modified_on'=>time()));
	}

	function get_group_back_dating_fine_category(){
		$this->select_all_secure('fine_categories');
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "0" ',NULL,FALSE);
		$this->db->where($this->dx('is_system').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('slug')." = 'back-dated-fines' ",NULL,FALSE);
		$this->db->limit(1);
		if($fine_category = $this->db->get('fine_categories')->row()){
			return $fine_category;
		}else{
			$input = array(
				'name' => 'Back-dated Fines',
				'slug' => 'back-dated-fines',
				'group_id' => $this->group->id,
				'is_system' => 1,
				'active' => 0,
				'created_on' => time(),
				'created_by' => $this->user->id,
			);
			if($id = $this->insert($input)){
				if($fine_category = $this->get_group_fine_category($id)){
					return $fine_category;
				}else{
					return FALSE;
				}
			}else{
				return FALSE;
			}
		}
	}

	function get_group_fine_categories($group_id = 0,$field_names = array()){
		if(empty($field_names)){
			$this->select_all_secure('fine_categories');
		}else{
			$arr = array();
			foreach($field_names as $field_name):
				if($field_name == 'id'){
					$arr[] = 'CONCAT("fine_category-",id) as id ';
				}else{
					$arr[] = $this->dx($field_name)." as ".$field_name." ";
				}
			endforeach;
			$this->db->select($arr);
		}
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->get('fine_categories')->result();
	}

	function get_group_fine_categories_options_array($group_id = 0,$field_names = array()){
		if(empty($field_names)){
			$this->select_all_secure('fine_categories');
		}else{
			$arr = array();
			foreach($field_names as $field_name):
				if($field_name == 'id'){
					$arr[] = 'CONCAT("fine_category-",id) as id ';
				}else{
					$arr[] = $this->dx($field_name)." as ".$field_name." ";
				}
			endforeach;
			$this->db->select($arr);
		}
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$results = $this->db->get('fine_categories')->result();
		$fine_arr = array();
		foreach ($results as $key => $result):
			$fine_arr[$result->id] = $result;
		endforeach;
		return $fine_arr;
	}

	function get_group_active_fine_categories($group_id = 0,$field_names = array()){
		if(empty($field_names)){
			$this->select_all_secure('fine_categories');
		}else{
			$arr = array();
			foreach($field_names as $field_name):
				if($field_name == 'id'){
					$arr[] = 'CONCAT("fine_category-",id) as id ';
				}else{
					$arr[] = $this->dx($field_name)." as ".$field_name." ";
				}
			endforeach;
			$this->db->select($arr);
		}
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$this->db->where($this->dx('active').' ="1"',NULL,FALSE);
		return $this->db->get('fine_categories')->result();
	}

}
