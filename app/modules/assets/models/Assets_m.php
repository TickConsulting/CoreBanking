<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Assets_m extends MY_Model {

	protected $_table = 'assets';

	public function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->install();
		$this->load->model('asset_categories/asset_categories_m');
	}

	public function install(){
		$this->db->query("
		create table if not exists assets(
			id int not null auto_increment primary key,
			`name` blob,
			`asset_category_id` blob,
			`cost` blob,
			`group_id` blob,
			`description` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	}

	function get($id=0,$group_id=0){
		$this->select_all_secure('assets');
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where('id',$id);
		return $this->db->get('assets')->row();
	}

	function insert($input,$skip_validation=FALSE){
		return $this->insert_secure_data('assets',$input);
	}

	function update($id,$input=array(),$SKIP_VALIDATION=FALSE)
	{
		return $this->update_secure_data($id,'assets',$input);
	}


	public function get_group_asset_options($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'id',
				$this->dx('name').' as name ',
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$assets = $this->db->get('assets')->result();
		foreach($assets as $asset){
			$arr[$asset->id] = $asset->name;
		}
		return $arr;
	}

	function get_group_asset($id = 0,$group_id = 0){
		$this->select_all_secure('assets');
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('assets')->row();
	}

	function get_group_assets($group_id = 0){
		$this->select_all_secure('assets');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('assets')->result();
	}

	function count_group_assets($group_id =0){
		$this->select_all_secure('assets');
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->count_all_results('assets');
	}

	function get_group_asset_value(){
		$asset_categories = $this->asset_categories_m->get_group_asset_category_options();
		$asset_value=array();
		$total_cost = 0;
		foreach ($asset_categories as $asset_category_id => $asset_category_name) {
			$this->db->select('sum('.$this->dx('cost').') as cost');
			$this->db->where($this->dx('asset_category_id').'="'.$asset_category_id.'"',NULL,FALSE);
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
			$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
			$cost = $this->db->get('assets')->row()->cost?:0;
			$asset_value[]=array('cost'=>$cost,'asset_category_name'=>$asset_category_name);
			$total_cost+=$cost;
		}

		$result = array('category'=>$asset_value,'asset_total_cost'=>$total_cost);

		//print_r($result);die;
		return($result);
	}

	function get_group_back_dating_assets(){
		$this->select_all_secure('assets');
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1"',NULL,FALSE);
		return $this->db->get('assets')->result();
	}

	function get_group_back_dating_asset_objects_array(){
		$this->select_all_secure('assets');
		$this->db->where($this->dx('assets.group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('assets.active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('assets.is_a_back_dating_record').'="1"',NULL,FALSE);
		$assets = $this->db->get('assets')->result();
		$arr = array();
		foreach($assets as $asset):
			$arr[$asset->id] = $asset;
		endforeach;
		return $arr;
	}
}