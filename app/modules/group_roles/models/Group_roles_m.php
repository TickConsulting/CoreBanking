<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Group_roles_m extends MY_Model {

protected $_table = 'group_roles';

	function __construct(){
		$this->load->dbforge();
		//$this->install();
	}

	function install(){
		$this->db->query("
		create table if not exists group_roles(
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
        return $this->insert_secure_data('group_roles', $input);
	}

	function insert_batch($input = array(),$key=FALSE){
        return $this->insert_batch_secure_data('group_roles', $input);
	}

	function get($id = 0){
		$this->select_all_secure('group_roles');
		$this->db->where('id',$id);
		return $this->db->get('group_roles')->row();
	}

	function get_group_role($id = 0,$group_id=0){
		$this->select_all_secure('group_roles');
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->get('group_roles')->row();
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'group_roles',$input);
    }

    function get_all_group_roles($group_id = 0){
		$this->select_all_secure('group_roles');
		if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		return $this->db->get('group_roles')->result();
	}

	function get_group_role_options_array($groups=array()){
		$group_ids = '';
		$arr = array();
		if($groups){
			foreach ($groups as $group) {
				if($group_ids){
					$group_ids.=','.$group->id;
				}else{
					$group_ids = $group->id;
				}
			}
		}
		if($group_ids){
			$this->db->select(array('id',$this->dx('name').' as name ',$this->dx('group_id').' as group_id '));
			$this->db->where($this->dx('group_id').' IN ('.$group_ids.')',NULL,FALSE);
			$this->db->order_by($this->dx('name'), 'ASC',FALSE);
			$group_roles = $this->db->get('group_roles')->result();
			foreach ($group_roles as $group_role) {
				$arr[$group_role->group_id][$group_role->id] = $group_role->name;
			}
		}
		return $arr;
	}

	function get_all_active_group_roles(){
		$this->select_all_secure('group_roles');
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		return $this->db->get('group_roles')->result();
	}

    function count_all_group_roles($group_id = 0){
    	if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		return $this->db->count_all_results('group_roles');
	}

	function get_group_role_options($group_id=0){
		$arr = array();
		$this->db->select(array('id',$this->dx('name').' as name '));
		
		$this->db->order_by($this->dx('name'), 'ASC',FALSE);
		$group_roles = $this->db->get('group_roles')->result();
		foreach($group_roles as $group_role){
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
		return $this->db->count_all_results('group_roles');
	}

	function is_name_unique($name='',$group_id = 0){
		$this->db->where("( CONVERT(".$this->dx('name')." USING 'latin1') LIKE '%".$name."%')",NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->count_all_results('group_roles')?0:1;
	}

	


}
