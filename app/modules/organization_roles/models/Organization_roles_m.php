<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Organization_roles_m extends MY_Model {

protected $_table = 'organization_roles';

	function __construct(){
		$this->load->dbforge();
		//$this->install();
	}

	function install(){
		$this->db->query("
		create table if not exists organization_roles(
			id int not null auto_increment primary key,
		 	`name` blob,
		 	`description` blob,
		  	`group_id` blob,
		  	`is_editable` blob,
		  	`active` blob,
			created_on blob,
			created_by blob,
			modified_on blob,
			modified_by blob
		)");
	}

	function insert($input = array(),$key=FALSE){
        return $this->insert_secure_data('organization_roles', $input);
	}

	function insert_batch($input = array(),$key=FALSE){
        return $this->insert_batch_secure_data('organization_roles', $input);
	}

	function get($id = 0){
		$this->select_all_secure('organization_roles');
		$this->db->where('id',$id);
		return $this->db->get('organization_roles')->row();
	}

	function get_group_organization_role($id = 0,$group_id=0){
		$this->select_all_secure('organization_roles');
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->get('organization_roles')->row();
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'organization_roles',$input);
    }

    function get_all_group_organization_roles($group_id = 0){
		$this->select_all_secure('organization_roles');
		if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		return $this->db->get('organization_roles')->result();
	}

	function get_all_active_group_organization_roles(){
		$this->select_all_secure('organization_roles');
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		return $this->db->get('organization_roles')->result();
	}

    function count_all_group_organization_roles($group_id = 0){
    	if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		return $this->db->count_all_results('organization_roles');
	}

	function get_group_organization_role_options($group_id=0){
		$arr = array();
		$this->db->select(array('id',$this->dx('name').' as name '));
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->order_by($this->dx('name'), 'ASC',FALSE);
		$organization_roles = $this->db->get('organization_roles')->result();
		foreach($organization_roles as $group_role){
			$arr[$group_role->id] = $group_role->name;
		}
		return $arr;
	}

	function get_by_name($name='',$id=0,$group_id=0){
		if($id){
			$this->db->where('id !=',$id);
		}
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where("CONVERT(".$this->dx('name')." USING 'latin1') LIKE '%".$this->db->escape_str($name)."%' ",NULL,FALSE);
		return $this->db->count_all_results('organization_roles');
	}


}
