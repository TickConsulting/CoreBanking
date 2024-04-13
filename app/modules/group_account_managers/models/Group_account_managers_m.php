<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
Class group_account_managers_m extends MY_Model{

	protected $_table = 'group_account_managers';

	function __construct(){
		parent::__construct();
		//$this->install();
	}

	function install(){
		$this->db->query("
			create table if not exists group_account_managers(
				id int not null auto_increment primary key,
				user_id blob,
				group_id blob,
				`active` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_by` blob,
				`modified_on` blob
			)"
		);

	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('group_account_managers',$input);
	}

	function update($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'group_account_managers',$input);
	}

	function get($id=0){
		$this->select_all_secure('group_account_managers');
		$this->db->where('id',$id);
		return $this->db->get('group_account_managers')->row();
	}

	function get_group_account_manager($id = 0,$group_id = 0){
		$this->db->select(
			array(
				'group_account_managers.id as id',
				$this->dx('group_account_managers.user_id').' as user_id',
				$this->dx('group_account_managers.group_id').' as group_id',
			)
		);
		$this->db->select(
			array(
				$this->dx('users.avatar').' as avatar',
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.middle_name').' as middle_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.phone').' as phone',
				$this->dx('users.email').' as email',
				$this->dx('users.id_number').' as id_number',
			)
		);
		$this->db->where('group_account_managers.id',$id);
		if($group_id){
			$this->db->where($this->dx('group_account_managers.group_id').' = '.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_account_managers.group_id').' = '.$this->group->id,NULL,FALSE);
		}
		$this->db->join('users','users.id = '.$this->dx('group_account_managers.user_id'));
		return $this->db->get('group_account_managers')->row();
	}

	function get_group_group_account_manager_by_email($email='',$group_id = 0){
		$this->db->select(
			array(
				'group_account_managers.id as id',
				$this->dx('group_account_managers.user_id').' as user_id',
				$this->dx('group_account_managers.group_id').' as group_id',
			)
		);
		$this->db->select(
			array(
				$this->dx('users.avatar').' as avatar',
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.middle_name').' as middle_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.phone').' as phone',
				$this->dx('users.email').' as email',
				$this->dx('users.id_number').' as id_number',
			)
		);
		$this->db->where($this->dx('users.email').'="'.$email.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_account_managers.group_id').' = '.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_account_managers.group_id').' = '.$this->group->id,NULL,FALSE);
		}
		$this->db->join('users','users.id = '.$this->dx('group_account_managers.user_id'));
		return $this->db->get('group_account_managers')->row();
	}

	function get_group_group_account_manager_options(){
		$this->select_all_secure('group_account_managers');
		$this->db->select(array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
			));
		$this->db->where($this->dx('group_account_managers.group_id').'='.$this->group->id,NULL,FALSE);
		$this->db->join('users','users.id ='.$this->dx('group_account_managers.user_id'),'INNER');
        $this->db->order_by($this->dx('first_name'), 'ASC', FALSE);
		$group_account_managers = $this->db->get('group_account_managers')->result();

		$arr = array();

		if($group_account_managers){
			foreach ($group_account_managers as $key => $group_account_manager){
				$arr[$group_account_manager->id] = $group_account_manager->first_name.' '.$group_account_manager->last_name;
			}
		}
		
		return $arr;
	}

	function get_active_group_group_account_manager_options(){
		$this->select_all_secure('group_account_managers');
		$this->db->select(array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
			));
		$this->db->where($this->dx('group_account_managers.group_id').'='.$this->group->id,NULL,FALSE);
		$this->db->where($this->dx('group_account_managers.active').'="1"',NULL,FALSE);
		$this->db->join('users','users.id ='.$this->dx('group_account_managers.user_id'),'INNER');
        $this->db->order_by($this->dx('first_name'), 'ASC', FALSE);
		$group_account_managers = $this->db->get('group_account_managers')->result();

		$arr = array();

		if($group_account_managers){
			foreach ($group_account_managers as $key => $group_account_manager) 
			{
				$arr[$group_account_manager->id] = $group_account_manager->first_name.' '.$group_account_manager->last_name;
			}
		}
		
		return $arr;
	}

	function get_options(){
		$this->select_all_secure('group_account_managers');
		$this->db->select(array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
			));
		$this->db->join('users','users.id ='.$this->dx('group_account_managers.user_id'),'INNER');
        $this->db->order_by($this->dx('first_name'), 'ASC', FALSE);
		$group_account_managers = $this->db->get('group_account_managers')->result();

		$arr = array();

		if($group_account_managers)
		{
			foreach ($group_account_managers as $key => $group_account_manager) 
			{
				$arr[$group_account_manager->id] = $group_account_manager->first_name.' '.$group_account_manager->last_name;
			}
		}
		
		return $arr;
	}

	function get_all(){
		$this->select_all_secure('group_account_managers');
		return $this->db->get('group_account_managers')->result();
	}

	function delete($id=0){
		$this->db->where('id',$id);
		return $this->db->delete('group_account_managers');
	}

	function check_if_user_is_in_group($group_id = 0,$user_id = 0){
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('user_id').' = "'.$user_id.'"',NULL,FALSE);
		if($this->db->count_all_results('group_account_managers')>0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	function get_group_account_manager_by_user_id($group_id = 0, $user_id = 0){
		$this->select_all_secure('group_account_managers');
		$this->db->select(
			array(
				$this->dx('users.avatar').' as avatar',
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.middle_name').' as middle_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.phone').' as phone',
				$this->dx('users.email').' as email',
				$this->dx('users.id_number').' as id_number',
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('user_id').' = "'.$user_id.'"',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('group_account_managers.user_id'));
		return $this->db->get('group_account_managers')->row();
	}

	function get_group_account_managers($group_id = 0){
		$name = trim($this->input->get('name'));
		$names_array = explode(' ',$name);
		$search_query = '';
		$this->db->select(
			array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.phone').' as phone',
				$this->dx('users.email').' as email',
				$this->dx('users.avatar').' as avatar',
				$this->dx('users.join_code').' as join_code',
				$this->dx('users.email_join_code').' as email_join_code',
				$this->dx('users.last_login').' as last_login',
			)
		);
		$this->select_all_secure('group_account_managers');
		if($group_id){
			$this->db->where($this->dx('group_account_managers.group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_account_managers.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($names_array){
			$search_query.=" ( ";
			$count = 1;
			foreach($names_array as $name){
				if($count==1){
					$search_query.=" CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$name."%' ";
				}else{
					$search_query.=" OR CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$name."%' ";
				}
				$count++;
			}
			$search_query.=" ) ";
			$this->db->where($search_query,NULL,FALSE);
		}
		$this->db->join('users','users.id = '.$this->dx('group_account_managers.user_id'));
        $this->db->order_by($this->dx('group_account_managers.active'), 'DESC', FALSE);
		$this->db->order_by($this->dx('users.first_name'),'ASC',FALSE);
		return $this->db->get('group_account_managers')->result();
	}

	function count_group_account_managers($group_id = 0){
		$name = trim($this->input->get('name'));
		$names_array = explode(' ',$name);
		$search_query = '';
		if($group_id){
			$this->db->where($this->dx('group_account_managers.group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_account_managers.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($names_array){
			$search_query.=" ( ";
			$count = 1;
			foreach($names_array as $name){
				if($count==1){
					$search_query.=" CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$name."%' ";
				}else{
					$search_query.=" OR CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$name."%' ";
				}
				$count++;
			}
			$search_query.=" ) ";
			$this->db->where($search_query,NULL,FALSE);
		}
		$this->db->join('users','users.id = '.$this->dx('group_account_managers.user_id'));
		return $this->db->count_all_results('group_account_managers');
	}


	function get_active_group_group_account_managers($group_id = 0){
		$name = trim($this->input->get('name'));
		$names_array = explode(' ',$name);
		$search_query = '';
		$this->db->select(
			array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.phone').' as phone',
				$this->dx('users.email').' as email',
				$this->dx('users.avatar').' as avatar',
				$this->dx('users.join_code').' as join_code',
				$this->dx('users.email_join_code').' as email_join_code',
			)
		);
		$this->select_all_secure('group_account_managers');
		if($group_id){
			$this->db->where($this->dx('group_account_managers.group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_account_managers.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($names_array){
			$search_query.=" ( ";
			$count = 1;
			foreach($names_array as $name){
				if($count==1){
					$search_query.=" CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$name."%' ";
				}else{
					$search_query.=" OR CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$name."%' ";
				}
				$count++;
			}
			$search_query.=" ) ";
			$this->db->where($search_query,NULL,FALSE);
		}
		$this->db->where($this->dx('group_account_managers.active').' = "1"',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('group_account_managers.user_id'));
		$this->db->order_by($this->dx('users.first_name'),'ASC',FALSE);
		return $this->db->get('group_account_managers')->result();
	}

	function get_group_group_account_managers_array($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.phone').' as phone',
				$this->dx('users.email').' as email',
				$this->dx('users.avatar').' as avatar'
			)
		);
		$this->select_all_secure('group_account_managers');
		if($group_id){
			$this->db->where($this->dx('group_account_managers.group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_account_managers.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->join('users','users.id = '.$this->dx('group_account_managers.user_id'));
		$this->db->order_by($this->dx('users.first_name'),'ASC',FALSE);
		$group_account_managers = $this->db->get('group_account_managers')->result();
		foreach($group_account_managers as $group_account_manager){
			$arr[$group_account_manager->id] = $group_account_manager;
		}
		return $arr;
	}

	function count_group_group_account_managers($group_id = 0){
		$name = $this->input->get('name');
		$names_array = explode(' ',$name);
		$search_query = '';
		if($name){
			$this->db->where("CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%'",NULL,FALSE);
		}
		if($names_array){
			$search_query.=" ( ";
			$count = 1;
			foreach($names_array as $name){
				if($count==1){
					$search_query.=" CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$name."%' ";
				}else{
					$search_query.=" OR CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$name."%' ";
				}
				$count++;
			}
			$search_query.=" ) ";
			$this->db->where($search_query,NULL,FALSE);
		}
		if($group_id){
			$this->db->where($this->dx('group_account_managers.group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_account_managers.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->join('users','users.id = '.$this->dx('group_account_managers.user_id'));
		return $this->db->count_all_results('group_account_managers');
	}

	function count_active_group_group_account_managers($group_id = 0){
		$name = $this->input->get('name');
		$names_array = explode(' ',$name);
		$search_query = '';
		if($name){
			$this->db->where("CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%'",NULL,FALSE);
		}
		if($names_array){
			$search_query.=" ( ";
			$count = 1;
			foreach($names_array as $name){
				if($count==1){
					$search_query.=" CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$name."%' ";
				}else{
					$search_query.=" OR CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$name."%' ";
				}
				$count++;
			}
			$search_query.=" ) ";
			$this->db->where($search_query,NULL,FALSE);
		}
		if($group_id){
			$this->db->where($this->dx('group_account_managers.group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_account_managers.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('group_account_managers.active').' = "1"',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('group_account_managers.user_id'));
		return $this->db->count_all_results('group_account_managers');
	}

	function count_all(){
		$this->db->where($this->dx('group_account_managers.active').' = "1"',NULL,FALSE);
		return $this->db->count_all_results('group_account_managers');
	}

	function get_group_group_account_managers_by_id($group_id=''){
		$this->db->select(array($this->dx('users.first_name').' as first_name',$this->dx('users.last_name').' as last_name',$this->dx('users.phone').' as phone',$this->dx('users.email').' as email',$this->dx('users.last_login').' as last_login'));
		$this->select_all_secure('group_account_managers');
		$this->db->where($this->dx('group_account_managers.group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('group_account_managers.user_id'));
		$this->db->order_by($this->dx('users.first_name'),'ASC',FALSE);
		return $this->db->get('group_account_managers')->result();
	}

	function get_group_group_account_managers_by_id_for_admin($group_id=''){
		$this->db->select(array($this->dx('users.first_name').' as first_name',$this->dx('users.last_name').' as last_name',$this->dx('users.phone').' as phone',$this->dx('users.email').' as email',$this->dx('users.last_login').' as last_login'));
		$this->select_all_secure('group_account_managers');
		$this->db->where($this->dx('group_account_managers.group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('group_account_managers.user_id'));
		$this->db->order_by($this->dx('users.first_name'),'ASC',FALSE);
		return $this->db->get('group_account_managers')->result();
	}

	function count_all_active_group_group_account_managers($group_id=0){
   		$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
   		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
   		return $this->db->count_all_results('group_account_managers')?:0;
   	}

   	function get_group_group_account_manager_ids($group_id=0){
   		$arr = array();
   		$this->db->select(array('id'));
   		$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
   		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
   		$group_account_managers = $this->db->get('group_account_managers')->result();
   		foreach($group_account_managers as $group_account_manager){
   			$arr[] = $group_account_manager->id;
   		}
   		return $arr;
   	}

   	function get_group_account_manager_where_user_id($user_id=0){
   		$this->select_all_secure('group_account_managers');
   		$this->db->where($this->dx('user_id').'="'.$user_id.'"',NULL,FALSE);
   		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
   		return $this->db->get('group_account_managers')->row();
   	}

   	function get_suspended_group_account_managers_ids_array(){
   		$arr = array();
   		$this->db->select(array('id'));
   		$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
   		$this->db->where($this->dx('active').'="0"',NULL,FALSE);
   		$group_account_managers = $this->db->get('group_account_managers')->result();
   		foreach($group_account_managers as $group_account_manager){
   			$arr[] = $group_account_manager->id;
   		}
   		return $arr;
   	}

   	function get_all_group_account_managers(){
   		$this->db->select(
   			array(
   				$this->dx('users.first_name').' as first_name ',
				$this->dx('users.last_name').' as last_name ',
   				$this->dx('users.phone').' as phone ',
   				$this->dx('users.email').' as email ',
   			)
   		);
   		$this->db->where($this->dx('group_account_managers.active').'="1"',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('group_account_managers.user_id'));
   		return $this->db->get('group_account_managers')->result();
   	}

   	function count_all_group_account_managers(){
   		$this->db->where($this->dx('group_account_managers.active').'="1"',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('group_account_managers.user_id'));
   		return $this->db->count_all_results('group_account_managers');
   	}

   	function get_group_group_account_manager_email_address_list_by_group_account_manager_id_array($group_account_manager_ids = array(),$group_id = 0){
   		$this->db->select(
   			array(
   				$this->dx('users.email').' as email ',
   			)
   		);
		$this->db->where($this->dx('group_account_managers.group_id').' = "'.$group_id.'" ',NULL,FALSE);
   		$this->db->where($this->dx('group_account_managers.active').'="1"',NULL,FALSE);
   		$this->db->where_in('group_account_managers.id',$group_account_manager_ids);
		$this->db->join('users','users.id = '.$this->dx('group_account_managers.user_id'));
		$group_account_managers = $this->db->get('group_account_managers')->result();
		$email_list = '';

		$count = 1;
		foreach ($group_account_managers as $group_account_manager) {
			# code...
			if($count==1){
				$email_list = $group_account_manager->email;
				
			}else{
				$email_list .= ','.$group_account_manager->email;
			}
			$count++;
		}
		return $email_list;
   	}

}