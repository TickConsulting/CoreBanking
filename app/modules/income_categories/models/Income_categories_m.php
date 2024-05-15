<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Income_categories_m extends MY_Model {

	protected $_table = 'income_categories';

	function __construct(){
		$this->load->dbforge();
		//$this->install();
	}

	function install(){
		$this->db->query("
		create table if not exists income_categories(
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

	function insert($input,$skip_validation=FALSE){
		return $this->insert_secure_data('income_categories',$input);
	}

	function insert_batch($input,$skip_validation=FALSE){
		return $this->insert_batch_secure_data('income_categories',$input);
	}

	function get_all(){
		$this->select_all_secure('income_categories');
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$this->db->order_by($this->dx('name'), 'DESC',FALSE);
		return $this->db->get('income_categories')->result();
	}

	function get_by_slug($slug,$id='',$group_id=''){
		$this->select_all_secure('income_categories');
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$this->db->where($this->dx('slug').'="'.$slug.'"',NULL,FALSE);
		if($id)
		{
			$this->db->where('id !=',$id);
		}
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->get('income_categories')->row();
	}


	function safe_delete($id=0,$group_id=0){
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->update_secure_data($id,'income_categories',array('is_deleted'=>1,'modified_on'=>time()));
	}


	function get_all_group_income_categories($group_id = 0){
		$this->select_all_secure('income_categories');
		if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		
		$this->db->order_by($this->dx('name'), 'DESC',FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->get('income_categories')->result();
	}

	function get_all_active_group_income_categories($group_id = 0){
		$this->select_all_secure('income_categories');
		if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		$this->db->order_by($this->dx('name'), 'DESC',FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('income_categories')->result();
	}

	function count_all_group_income_categories($group_id = 0){
		if ($group_id) {
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->count_all_results('income_categories')?:0;
	}
	
	function get($id){
		$this->select_all_secure('income_categories');
		$this->db->where('id',$id);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->get('income_categories')->row();
	}

	function income_category_exists($id=0,$group_id=0){
		$this->db->where('id',$id);
		if ($group_id) {
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->count_all_results('income_categories')?:0;
	}

	function get_group_income_category($id,$group_id=0){
		$this->select_all_secure('income_categories');
		$this->db->where('id',$id);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		if ($group_id) {
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		return $this->db->get('income_categories')->row();
	}

	function get_group_income_category_options($group_id=0){
		$arr = array();
		$this->db->select(array(
			'id',
			$this->dx('name').' as name',
			)
		);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		 
		$income_categories = $this->db->get('income_categories')->result();
		foreach($income_categories as $income_category){
			$arr[$income_category->id] = $income_category->name;
		}
		return $arr;
	}

	function count_all($params = array()){
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->count_all_results('income_categories');
	}

	function update($id, $input,$skip_validation = false){
		return $this->update_secure_data($id,'income_categories',$input);
	}

}
