<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Setup_tasks_m extends MY_Model {

	protected $_table = 'setup_tasks';

	public $setup_task_options = array();

	function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->install();
	}

	public function install(){
		$this->db->query("
		create table if not exists setup_tasks(
			id int not null auto_increment primary key,
			`name` blob,
			`icon` blob,
			`slug` blob,
			`parent_id` blob,
			`description` blob,
			`call_to_action_name` blob,
			`call_to_action_link` blob,
			`active` blob,
			`position` blob,
			`created_by` blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");


		$this->db->query("
			create table if not exists setup_tasks_tracker(
				id int not null auto_increment primary key,
				`setup_task_slug` blob,
				`group_id` blob,
				`created_by` blob,
				`created_on` blob
			)"
		);
	}

	public function insert($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('setup_tasks',$input);
	}

	public function insert_setup_task_tracker($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('setup_tasks_tracker',$input);
	}

	function get_ordered_setup_tasks(){
		$this->select_all_secure('setup_tasks');
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('setup_tasks')->result();
	}

	public function delete_setup_task_trackers_by_slug($slug = ''){
		$this->db->select('id as id');
		$this->db->where($this->dx('setup_task_slug').'="'.$slug.'"',NULL,FALSE);
		$res = $this->db->get('setup_tasks_tracker')->row();
		if($res){
			return $this->update_secure_data($res->id,'setup_tasks_tracker',array('is_deleted'=>1,'modified_on'=>time()));
		}
		
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'setup_tasks',$input);
    }

	public function count_all(){
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->count_all_results('setup_tasks');
	}
	
	public function get_all(){	
		$this->select_all_secure('setup_tasks');
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->get('setup_tasks')->result();
	}

	public function get($id = 0){	
		$this->select_all_secure('setup_tasks');
		$this->db->where('id',$id);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->get('setup_tasks')->row();
	}

	function get_by_slug($slug = ''){
		$this->select_all_secure('setup_tasks');
		$this->db->where($this->dx('slug').'="'.$slug.'"',NULL,FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->get('setup_tasks')->row();
	}

	function get_group_setup_tasks(){
		$arr = array();
		$setup_tasks = $this->get_parent_setup_tasks();
		$completed_setup_tasks = $this->get_completed_setup_tasks_array();
		foreach($setup_tasks as $setup_task){
			if(in_array($setup_task->slug,$completed_setup_tasks)){
				$setup_task->completed = 1;
			}else{
				$setup_task->completed = 0;
			}
			$arr[] = $setup_task;
		}
		return $arr;
	}

	function get_completed_setup_tasks_array(){
		$arr = array();
		$this->db->select(
			array(
				'id',
				$this->dx('setup_task_slug').' as setup_task_slug ',
			)
		);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$completed_tasks = $this->db->get('setup_tasks_tracker')->result();
		foreach($completed_tasks as $complete_task){
			$arr[$complete_task->id] = $complete_task->setup_task_slug;
		}
		return $arr;
	}

	function count_complete_setup_tasks(){
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->count_all_results('setup_tasks_tracker');
	}

	function check_if_setup_tracker_tasks_exists($setup_task_slug = '',$group_id = 0){
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$this->db->where($this->dx('setup_task_slug').' = "'.$setup_task_slug.'"',NULL,FALSE);
		return $this->db->count_all_results('setup_tasks_tracker')?TRUE:FALSE;
	}

	function get_options(){
		$arr = array();
		$parent_setup_tasks = $this->get_parent_setup_tasks();
		foreach($parent_setup_tasks as $parent_setup_task){
			$this->setup_task_options[$parent_setup_task->id] = $parent_setup_task->name;
			$this->get_children_options($parent_setup_task->name,$parent_setup_task->id);
		}
		return $this->setup_task_options;
	}


	function get_children_options($parent_setup_task_name='',$parent_id = 0){
		$this->select_all_secure('setup_tasks');
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
        $this->db->order_by($this->dx('position'),'ASC', FALSE);
		$children_setup_tasks = $this->db->get('setup_tasks')->result();
		if(!empty($children_setup_tasks)){
			foreach($children_setup_tasks as $children_setup_task){
				$this->setup_task_options[$children_setup_task->id] = $parent_setup_task_name.' >> '.$children_setup_task->name;
				$this->get_children_options($parent_setup_task_name.' >> '.$children_setup_task->name,$children_setup_task->id);
			}

		}
	}

	function get_parent_setup_tasks(){
		$this->select_all_secure('setup_tasks');
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$this->db->where('( '.$this->dx('parent_id').' = 0 OR '.$this->dx('parent_id').' = "" OR '.$this->dx('parent_id').' IS NULL )',NULL,FALSE);
        $this->db->order_by($this->dx('position'),'ASC', FALSE);
		return $this->db->get('setup_tasks')->result();
	}


	function get_children_setup_tasks($parent_id = 0){
		$this->select_all_secure('setup_tasks');
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('setup_tasks')->result();	
	}

	function display_children($parent_id = 0){
		$links = $this->get_children_setup_tasks($parent_id);	
		if(!empty($links)){
			echo '<ol class="dd-list">';
			foreach($links as $link):
				echo '<li class="dd-item" data-id="'.$link->id.'">
	                        <div class="dd-handle">
	                             '.$link->name.' ';
                                  echo $link->active==1?' - Active':' - Hidden'; 
	                        echo '</div>';
	                        $this->display_children($link->id); 
	            echo '</li>';
	        endforeach;
			echo '</ol>';
		}	
	}

	function delete_setup_task_tracker_by_setup_task_slug($setup_task_slug = '',$group_id = 0){
		$this->db->select('id as id');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('setup_task_slug').' = "'.$setup_task_slug.'"',NULL,FALSE);
		$res = $this->db->get('setup_tasks_tracker')->row();
		if($res){
			return $this->update_secure_data($res->id,'setup_tasks_tracker',array('is_deleted'=>1,'modified_on'=>time()));
		}
	}

	function safe_delete($id=0){
		$this->db->where('id',$id);
		return $this->update_secure_data($id,'setup_tasks',array('is_deleted'=>1,'modified_on'=>time()));
	}

}