<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
Class Permissions_m extends MY_Model{

	protected $_table = 'permissions';

	function __construct()
	{
		parent::__construct();
		//$this->install();
	}

	function install()
	{
		$this->db->query("
			create table if not exists permissions(
				id int not null auto_increment primary key,
				`menu_id` blob,
				`role_id` blob,
				`group_id` blob,
				`active` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_on` blob,
				`modified_by` blob
		)");
	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE)
	{
		return $this->insert_secure_data('permissions',$input);
	}

	function insert_batch($input = array(),$key=FALSE){
        return $this->insert_batch_secure_data('permissions', $input);
	}

	function update($id,$input=array(),$SKIP_VALIDATION=FALSE)
	{
		return $this->update_secure_data($id,'permissions',$input);
	}

	function get($id=0)
	{
		$this->select_all_secure('permissions');
		$this->db->where('id'.$id.'"',NULL,FALSE);
		return $this->db->get('permissions')->row();
	}

	function get_group_permissions($group_id=0){
		$this->select_all_secure('permissions');
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->get('permissions')->result();
	}

	function get_group_permissions_array(){
		$arr = array();
		$this->select_all_secure('permissions');
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$permissions = $this->db->get('permissions')->result();
		foreach ($permissions as $permission) {
			$arr[$permission->role_id][$permission->menu_id] = $permission->menu_id;
		}
		return $arr;
	}

	function delete_group_permissions($group_id=0){
		$permissions = $this->get_group_permissions($group_id);
		if($permissions){
			foreach ($permissions as $permission) {
				$this->db->where('id',$permission->id);
				$this->db->delete('permissions');
			}
			return TRUE;
		}else{
			return TRUE;
		}
	}

	function get_group_member_role_permissions($role_id=0){
		$this->select_all_secure('permissions');
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('role_id').'="'.$role_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		return $this->db->get('permissions')->result();
	}
}