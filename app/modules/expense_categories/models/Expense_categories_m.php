<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Expense_categories_m extends MY_Model {

	protected $_table = 'expense_categories';

	function __construct()
	{
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
		create table if not exists expense_categories(
					id int not null auto_increment primary key,
					`name` BLOB,
					`slug` BLOB,
					`description` BLOB,
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
		return $this->insert_secure_data('expense_categories',$input);
	}

	function insert_batch($input,$skip_validation=FALSE)
	{
		return $this->insert_batch_secure_data('expense_categories',$input);
	}

	function get_group_expense_categories($group_id = 0){
		$this->select_all_secure('expense_categories');
		if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$this->db->order_by($this->dx('name'), 'DESC',FALSE);
		return $this->db->get('expense_categories')->result();
	}

	function count_group_expense_categories($group_id = 0){
		if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->count_all_results('expense_categories')?:0;
	}

	function get_group_expense_category_options($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'id',
				$this->dx('name').' as name '
			)
		);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		$expense_categories =  $this->db->get('expense_categories')->result();
		foreach ($expense_categories as $expense_category) {
			# code...
			$arr[$expense_category->id] = $expense_category->name;
		}
		return $arr;
	}

	function get_group_administrative_expense_category_options($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'id',
				$this->dx('name').' as name '
			)
		);
		$this->db->where($this->dx('is_an_administrative_expense_category').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		$expense_categories =  $this->db->get('expense_categories')->result();
		foreach ($expense_categories as $expense_category) {
			# code...
			$arr[$expense_category->id] = $expense_category->name;
		}
		return $arr;
	}
	
	function get_group_other_expense_category_options($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'id',
				$this->dx('name').' as name '
			)
		);
		$this->db->where(" ( ".$this->dx('is_an_administrative_expense_category').' = "0" OR '.$this->dx('is_an_administrative_expense_category')." IS NULL ) ",NULL,FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		$expense_categories =  $this->db->get('expense_categories')->result();
		foreach ($expense_categories as $expense_category) {
			# code...
			$arr[$expense_category->id] = $expense_category->name;
		}
		return $arr;
	}
	
	
	function get($id)
	{
		$this->select_all_secure('expense_categories');
		$this->db->where('id',$id);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->get('expense_categories')->row();
	}
	function get_group_expense_category($id = 0,$group_id = 0)
	{
		$this->select_all_secure('expense_categories');
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->get('expense_categories')->row();
	}

	function get_by_slug($slug,$id='',$group_id=''){
		$this->select_all_secure('expense_categories');
		$this->db->where($this->dx('slug').'="'.$slug.'"',NULL,FALSE);
		if($id){
			$this->db->where('id !=',$id);
		}
		if($group_id)
		{
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->get('expense_categories')->row();
	}

	function count_all($params = array())
	{
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->count_all_results('expense_categories');
	}

	function get_all($params = array())
	{
		$this->select_all_secure('expense_categories');
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->get('expense_categories')->result();
	}

	function update($id, $input,$skip_validation = false)
	{
		return $this->update_secure_data($id,'expense_categories',$input);
	}

	function expense_category_exists($id=0,$group_id=0){
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->count_all_results('expense_categories');
	}


	function safe_delete($id=0,$group_id=0){
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->update_secure_data($id,'expense_categories',array('is_deleted'=>1,'modified_on'=>time()));
	}

}
