<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Asset_categories_m extends MY_Model {

	protected $_table = 'asset_categories';

	function __construct(){
		$this->load->dbforge();
		//$this->install();
	}

	function install(){
		$this->db->query("
		create table if not exists asset_categories(
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
		return $this->insert_secure_data('asset_categories',$input);
	}

	function insert_batch($input,$skip_validation=FALSE){
		return $this->insert_batch_secure_data('asset_categories',$input);
	}

	function get_all(){
		$this->select_all_secure('asset_categories');
		$this->db->order_by($this->dx('name'), 'DESC',FALSE);
		return $this->db->get('asset_categories')->result();
	}

	function get_group_asset_categories($group_id = 0){
		$this->select_all_secure('asset_categories');
		if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		$this->db->order_by($this->dx('name'), 'DESC',FALSE);
		return $this->db->get('asset_categories')->result();
	}

	function count_group_asset_categories($group_id=0){
		if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		return $this->db->count_all_results('asset_categories')?:0;
	}
	
	function get($id = 0,$group_id=0){
		$this->select_all_secure('asset_categories');
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		return $this->db->get('asset_categories')->row();
	}

	function get_group_asset_category($id = 0,$group_id = 0){
		$this->select_all_secure('asset_categories');
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->get('asset_categories')->row();
	}

	function get_by_slug($slug,$id='',$group_id=''){
		$this->select_all_secure('asset_categories');
		$this->db->where($this->dx('slug').'="'.$slug.'"',NULL,FALSE);
		if($id){
			$this->db->where('id !=',$id);
		}
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->get('asset_categories')->row();
	}

	function get_group_asset_category_options($group_id=0){
		$arr = array();
		$this->db->select(array(
			'id',
			$this->dx('name').' as name',
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		$asset_categories = $this->db->get('asset_categories')->result();
		foreach($asset_categories as $asset_category){
			$arr[$asset_category->id] = $asset_category->name;
		}
		return $arr;
	}

	function count_all($params = array()){
		return $this->db->count_all_results('asset_categories');
	}

	function update($id, $input,$skip_validation = false){
		return $this->update_secure_data($id,'asset_categories',$input);
	}

}
