<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
	Class Members_m extends MY_Model{

		protected $_table = 'members';

		function __construct()
		{
			parent::__construct();

			$this->install();
		}

		function install()
		{
			$this->db->query("
				create table if not exists members(
					id int not null auto_increment primary key,
					user_id blob,
					group_id blob,
					group_role_id blob,
					is_admin blob,
					`active` blob,
					`created_by` blob,
					`created_on` blob,
					`modified_by` blob,
					`modified_on` blob
				)"
			);
			$this->db->query("
			create table if not exists member_additional_fields(
				id int not null auto_increment primary key,
				group_id blob,
				field_name blob,
				`created_by` blob,
				`created_on` blob
			)
		");
			$this->db->query("
				create table if not exists next_of_kin(
					id int not null auto_increment primary key,
					full_name blob,
					id_number blob,
					phone blob,
					email blob,
					relationship blob,
					allocation blob,
					member_id blob,
					group_id blob,
					`created_by` blob,
					`created_on` blob
				)"
			);
			$this->db->query("
			create table if not exists member_additional_field_mapping(
				id int not null auto_increment primary key,
				group_id blob,
				member_additional_field_id blob,
				member_id blob,
				value blob,
				`created_by` blob,
				`created_on` blob
			)
		");
			$this->db->query("
				create table if not exists member_deletion_data(
					id int not null auto_increment primary key,
					user_id blob,
					member_id blob,
					group_id blob,
					group_name blob,
					restore_status blob,
					`restored_on` blob,
					`created_by` blob,
					`created_on` blob
				)"
			);

			$this->db->query("
				create table if not exists group_membership_requests(
					id int not null auto_increment primary key,
					group_id blob,
					first_name blob,
					last_name blob,
					phone blob,
					email blob,
					id_number blob,
					location blob,
					avatar blob,
					next_of_kin_full_name blob,
					next_of_kin_phone blob,
					next_of_kin_id_number blob,
					next_of_kin_relationship blob,
					`created_by` blob,
					`created_on` blob
				)"
			);
		}

		function insert($input=array(),$SKIP_VALIDATION = FALSE)
		{
			return $this->insert_secure_data('members',$input);
		}

		function insert_member_deletion_data($input=array(),$SKIP_VALIDATION = FALSE){
			return $this->insert_secure_data('member_deletion_data',$input);
		}

		function insert_group_membership_request_data($input=array(),$SKIP_VALIDATION = FALSE){
			return $this->insert_secure_data('group_membership_requests',$input);
		}

		function get_archived_groups($user_id=0){
			$this->select_all_secure('member_deletion_data');
			$this->db->where($this->dx('user_id').' = "'.$user_id.'"',NULL,FALSE);
			$this->db->where($this->dx('restore_status').' = "0"',NULL,FALSE);
			$this->db->where('('.$this->dx('is_hidden').' = "0" OR '.$this->dx('is_hidden').' IS NULL)',NULL,FALSE);
			return $result =  $this->db->get('member_deletion_data')->result();
		}

		function get_delete_member_data($id=0){
			$this->select_all_secure('member_deletion_data');
			$this->db->where('id',$id);
			return $this->db->get('member_deletion_data')->row();
		}

		function update($id,$input=array(),$SKIP_VALIDATION=FALSE)
		{
			return $this->update_secure_data($id,'members',$input);
		}

		function update_group_member_deletion_data($id=0,$input=array()){
			return $this->update_secure_data($id,'member_deletion_data',$input);
		}
		function get_member_additional_fields_mapping_data_using_field_slug($member_id=0,$additional_field_slug='',$group_id=0){
			$member_additional_fields_options = $this->cache->file->get('additional_member_fields_mapping_'.($group_id ? $group_id : $this->group->id));
			// if(isset($member_additional_fields_options->$member_id)){
				$member_mapping_data = isset($member_additional_fields_options[$member_id]) ? $member_additional_fields_options[$member_id] : "";
				$value = $member_mapping_data && isset($member_mapping_data[$additional_field_slug]) ? $member_mapping_data[$additional_field_slug] : "";
				return $value;
			// }else{
				// $this->select_all_secure('member_additional_field_mapping');
				// $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE);
				// $this->db->where($this->dx('member_additional_field_slug').'="'.$additional_field_slug.'"',NULL,FALSE);
				// if($group_id){
				// 	$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
				// } else {
				// 	$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
				// }
				// $result =  $this->db->get('member_additional_field_mapping')->row();
				// if($result){
				// 	return $result->value;
				// }else{
				// 	return '';
				// }
				// $arr = array();
				// $arr[$result->member_id]
				// $this->cache->redis->save('additional_member_fields_mapping_'.($group_id ? $group_id : $this->group->id),$result,(12*60*24));
				// return $result;
			
		}
		function update_group_membership_request($id=0,$input=array()){
			return $this->update_secure_data($id,'group_membership_requests',$input);
		}
		function get_member_additional_fields_data($group_id=0,$include_hidden=FALSE){
			$this->select_all_secure('member_additional_fields');
			
			if(!$include_hidden){
				$this->db->where($this->dx('is_hidden').'=0',NULL,FALSE);
			}
			return $result =  $this->db->get('member_additional_fields')->result();
		}

		function get_member_group_deletion_data($group_deletion_id=0){
			$this->db->select(array('id'));
			$this->db->where($this->dx('group_deletion_id').' = "'.$group_deletion_id.'"',NULL,FALSE);
			$res = $this->db->get('member_deletion_data')->result();
			return $res;
		}

		function group_member_deletion_data($group_deletion_id=0){
			$results = $this->get_member_group_deletion_data($group_deletion_id);
			if($results){
				foreach ($results as $result) {
					$this->db->where('id',$result->id);
					$this->db->delete('member_deletion_data');
				}
				return TRUE;
			}
		}

		function update_group_member_deletion_data_by_group_id($group_deletion_id=0,$update=array()){
			$results = $this->get_member_group_deletion_data($group_deletion_id);
			if($results){
				foreach ($results as $result) {
					$this->update_group_member_deletion_data($result->id,$update);
				}
				return TRUE;
			}
		}


	function insert_next_of_kin($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('next_of_kin',$input);
	}

	function get($id=0){
		$this->select_all_secure('members');
		$this->db->where('id',$id);
		return $this->db->get('members')->row();
	}

	function get_group_membership_request($id=0){
		$this->select_all_secure('group_membership_requests');
		$this->db->where('id',$id);
		return $this->db->get('group_membership_requests')->row();
	}

	function get_group_old_member($id=0){
		$this->select_all_secure('members');
		$this->db->where($this->dx('old_id').' = "'.$id.'"',NULL,FALSE);
		return $this->db->get('members')->row();
	}
	function get_ajax_active_group_member_options_using_name($group_id=0){
		$result = new stdClass();
		$query = $this->input->get('q');
		$this->select_all_secure('members');		
		$this->db->select(array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.language_id').' as language_id',
		));
		
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$search_query = '';
		$search_query.=" ( ";
		$search_query.=" CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$query."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$query."%' ";
		$search_query.=" ) ";
		$this->db->where($search_query,NULL,FALSE);
		$this->db->join('users','users.id ='.$this->dx('members.user_id'));
		if(isset($this->group->id)){
			$this->db->order_by($this->dx($this->group->member_listing_order_by?:'first_name'), $this->group->order_members_by?:'ASC', FALSE);
		}else{
			$this->db->order_by($this->dx('first_name'),'ASC',FALSE);
		}
		$members = $this->db->get('members')->result();
		// $arr = array();
		// if($members)
		// {
		// 	foreach ($members as $key => $member) 
		// 	{
		// 		$arr[$member->id] = $member->first_name.' '.$member->last_name;
		// 	}
		// }
		$result->items = $members;
		echo json_encode($result);
	}
	function get_group_member($id = 0,$group_id = 0){
		$this->db->select(
			array(
				'members.id as id',
				$this->dx('members.user_id').' as user_id',
				$this->dx('members.group_id').' as group_id',
				// $this->dx('investment_groups.name').' as group_name',
				$this->dx('members.group_role_id').' as group_role_id',
				$this->dx('members.is_admin').' as is_admin',
				$this->dx('members.membership_number').' as membership_number',
				$this->dx('members.date_of_birth').' as date_of_birth',
				$this->dx('members.physical_address').' as physical_address',
				$this->dx('members.postal_address').' as postal_address',
				$this->dx('members.active').' as active',
				$this->dx('members.suspension_initiated').' as suspension_initiated',
			)
		);
		$this->db->select(
			array(
				$this->dx('users.avatar').' as avatar',
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.middle_name').' as middle_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.phone').' as phone',
				$this->dx('users.loan_limit').' as loan_limit',
				$this->dx('users.email').' as email',
				$this->dx('users.id_number').' as id_number',
				$this->dx('users.language_id').' as language_id',
			)
		);
		$this->db->where('members.id',$id);
		
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		// $this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		return $this->db->get('members')->row();
	}

	function get_group_member_by_email($email='',$group_id = 0){
		$this->db->select(
			array(
				'members.id as id',
				$this->dx('members.user_id').' as user_id',
				$this->dx('members.group_id').' as group_id',
				$this->dx('members.group_role_id').' as group_role_id',
				$this->dx('members.is_admin').' as is_admin',
				$this->dx('members.membership_number').' as membership_number',
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
				$this->dx('users.language_id').' as language_id',
			)
		);
		$this->db->where($this->dx('users.email').'="'.$email.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('members.group_id').' = '.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('members.group_id').' = '.$this->group->id,NULL,FALSE);
		}
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		return $this->db->get('members')->row();
	}
	function get_group_member_by_id_number($id_number=''){
		$this->db->select(
			array(
				'members.id as id',
				$this->dx('members.user_id').' as user_id',
				$this->dx('members.group_id').' as group_id',
				$this->dx('members.active').' as active',
				$this->dx('members.group_role_id').' as group_role_id',
				$this->dx('members.is_admin').' as is_admin',
				$this->dx('members.membership_number').' as membership_number',
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
				$this->dx('users.loan_limit').' as loan_limit',
				$this->dx('users.id_number').' as id_number',
				$this->dx('users.language_id').' as language_id',
			)
		);
		$this->db->where($this->dx('users.id_number').'="'.$id_number.'"',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		return $this->db->get('members')->row();
	}
	function get_all_member_options_only($group_id = 0){
		$this->db->select('id');
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		if($group_id){
			$this->db->where($this->dx('members.group_id').' = '.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('members.group_id').' = '.$this->group->id,NULL,FALSE);
		}
   		$members =  $this->db->get('members')->result();
   		$arr = array();
   		foreach ($members as $member) {
   			$arr[$member->id] = $member->id;
   		}
   		return $arr;
	}

	function get_group_member_options($group_id = 0,$show_other_details = FALSE){
		$this->select_all_secure('members');
		$this->db->select(array(
				'users.id'.' as user_id',
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.phone').' as phone',
				$this->dx('users.email').' as email',
				$this->dx('users.language_id').' as language_id',
			));
		 
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('users','users.id ='.$this->dx('members.user_id'),'INNER');
      
        	$this->db->order_by($this->dx('first_name'),'ASC',FALSE);
        
		$members = $this->db->get('members')->result();
		$arr = array();
		if($members)
		{
			foreach ($members as $key => $member) {
				if($show_other_details){
					$arr[$member->id] = $member->first_name.' '.$member->last_name.' ('.$member->phone.')';
				}else{
					$arr[$member->id] = $member->first_name.' '.$member->last_name;
				}
			}
		}
		return $arr;
	}



	function get_group_members_phone_options($group_id = 0){
		$this->select_all_secure('members');
		$this->db->select(array(
				'users.id'.' as user_id',
				$this->dx('users.phone').' as phone',
			));
		 
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('users','users.id ='.$this->dx('members.user_id'),'INNER');
		$members = $this->db->get('members')->result();
		$arr = array();
		if($members)
		{
			foreach ($members as $key => $member) {
				$arr[$member->id] = $member->phone;
			}
		}
		return $arr;
	}

	function get_group_member_user_id_options($group_id = 0){
		$this->select_all_secure('members');
		
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
       	
		$members = $this->db->get('members')->result();
		$arr = array();
		if($members)
		{
			foreach ($members as $key => $member) {
				$arr[$member->id] = $member->user_id;
			}
		}
		return $arr;
	}


	function get_group_member_user_id($id = 0,$group_id = 0){
		$this->select_all_secure('members');
		$this->db->where('id',$id,NULL,FALSE);
		
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$member = $this->db->get('members')->row();
		return $member->user_id;
	}

	function get_group_member_with_membership_number_options($group_id = 0,$show_other_details = FALSE){
		$this->select_all_secure('members');
		$this->db->select(array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.phone').' as phone',
				$this->dx('users.email').' as email',
				$this->dx('users.language_id').' as language_id',
			));
		
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('users','users.id ='.$this->dx('members.user_id'),'INNER');
       	if(isset($this->group->id)){
        	$this->db->order_by($this->dx($this->group->member_listing_order_by?:'first_name'), $this->group->order_members_by?:'ASC', FALSE);
        }else{
        	$this->db->order_by($this->dx('first_name'),'ASC',FALSE);
        }
		$members = $this->db->get('members')->result();
		$arr = array();
		if($members){
			foreach ($members as $key => $member) {
				$member_name = $member->first_name.' '.$member->last_name;
				if($member->membership_number){
					$member_name .= " - ".$member->membership_number."";
				}
				$arr[$member->id] = $member_name;
			}
		}
		return $arr;
	}

	function get_joint_loan_member_pairings($loan_id = 0){
		$this->select_all_secure('joint_loan_members_pairing');
        // $this->db->select(array(
        //         $this->dx('users.first_name').' as first_name',
        //         $this->dx('users.last_name').' as last_name',
        //         $this->dx('users.email').' as email',
        //         $this->dx('users.phone').' as phone',
        //     ));
        // $this->db->join('members',$this->dx('joint_loan_members_pairing.member_id').' = members.id');
        // $this->db->join('users',$this->dx('members.user_id').' = users.id');
        $this->db->where($this->dx('joint_loan_members_pairing.loan_id'),$loan_id);
        $this->db->where($this->dx('joint_loan_members_pairing.is_deleted').' != 1');
        $joint_loan_members_pairings = $this->db->get('joint_loan_members_pairing')->result();

		$arr = array();

		if($joint_loan_members_pairings){
			foreach ($joint_loan_members_pairings as $joint_loan_members_pairing) 
			{
				$arr[] = $joint_loan_members_pairing->member_id;
			}
		}
		
		return $arr;
	}

	function get_membership_number(){
		$this->db->select(array(
				'id as id',
	    		'IFNULL('.$this->dx('membership_number').',"-") as membership_number',
			));
		$this->db->where($this->dx('members.group_id').'='.$this->group->id,NULL,FALSE);
		$members = $this->db->get('members')->result();
		$arr = array();
		if($members)
		{
			foreach ($members as $key => $member) 
			{
				$arr[$member->id] = $member->membership_number;
			}
		}
		
		return $arr;
	}

	function get_active_group_role_holder_options($group_id = 0,$limit = 0){
		$arr = array();
		$this->db->select(
			array(
				'members.id as id',
				$this->dx('users.first_name')." as first_name ",
				$this->dx('users.middle_name')." as middle_name ",
				$this->dx('users.last_name')." as last_name ",
				$this->dx('members.group_role_id')."as group_role_id",
				$this->dx('users.language_id').' as language_id',
			)
		);
		
		$this->db->where($this->dx('members.group_role_id').' > 0 ',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		if($limit){
			$this->db->limit($limit);
		}
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		$members = $this->db->get('members')->result();
		foreach($members as $member):
			$arr[$member->id] = $member->first_name." ".$member->middle_name." ".$member->last_name;
		endforeach;
		return $arr;
	}

	function get_active_group_role_holder_member_details($group_id = 0,$limit = 0){
		$arr = array();
		$this->db->select(
			array(
				'members.id as id',
				'users.id as user_id',
				$this->dx('users.first_name')." as first_name ",
				//$this->dx('users.id')." as user_id ",
				$this->dx('users.middle_name')." as middle_name ",
				$this->dx('users.last_name')." as last_name ",
				$this->dx('users.phone')." as phone ",
				$this->dx('users.language_id').' as language_id',
				$this->dx('members.group_role_id')."as group_role_id"
			)
		);
		
		$this->db->where($this->dx('members.group_role_id').' > 0 ',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		if($limit){
			$this->db->limit($limit);
		}
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		$members = $this->db->get('members')->result();
		foreach($members as $member):
			$arr[$member->id] = $member;
		endforeach;
		return $arr;
	}

	function get_eazzy_sacco_active_group_role_holder_options($group_id = 0,$limit = 0){
		$arr = array();
		$this->db->select(
			array(
				'members.id as id',
				'users.id as user_id',
				$this->dx('users.first_name')." as first_name ",
				$this->dx('users.middle_name')." as middle_name ",
				$this->dx('users.phone')." as phone ",
				$this->dx('users.email')." as email ",
				$this->dx('users.last_name')." as last_name ",
				$this->dx('users.language_id').' as language_id',
				$this->dx('members.group_role_id')."as group_role_id"
			)
		);
		
		$this->db->where($this->dx('members.group_role_id').' > 0 ',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		if($limit){
			$this->db->limit($limit);
		}
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		$members = $this->db->get('members')->result();
		foreach($members as $member):
			$arr[$member->id] = $member;
		endforeach;
		return $arr;
	}

	function get_active_organizational_role_holder_options($group_id = 0,$limit = 0){
		$arr = array();
		$this->db->select(
			array(
				'users.id as user_id',
				'members.id as id',
				$this->dx('users.first_name')." as first_name ",
				$this->dx('users.middle_name')." as middle_name ",
				$this->dx('users.last_name')." as last_name ",
				$this->dx('users.phone')." as phone ",
				$this->dx('members.group_role_id')."as group_role_id",
				$this->dx('users.language_id').' as language_id',
				$this->dx('members.organization_role_id')."as organization_role_id"
			)
		);
		
		$this->db->where($this->dx('members.organization_role_id').' > 0 ',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		if($limit){
			$this->db->limit($limit);
		}
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		$members = $this->db->get('members')->result();
		foreach($members as $member):
			$arr[$member->organization_role_id] = $member;
		endforeach;
		return $arr;
	}

	function get_active_group_member_options($group_id=0){
		$this->select_all_secure('members');
		$this->db->select(array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.language_id').' as language_id',
			));
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$this->db->join('users','users.id ='.$this->dx('members.user_id'),'INNER');
		if(isset($this->group->id)){
        	$this->db->order_by($this->dx($this->group->member_listing_order_by?:'first_name'), $this->group->order_members_by?:'ASC', FALSE);
        }else{
        	$this->db->order_by($this->dx('first_name'),'ASC',FALSE);
        }
		$members = $this->db->get('members')->result();

		$arr = array();

		if($members)
		{
			foreach ($members as $key => $member) 
			{
				$arr[$member->id] = $member->first_name.' '.$member->last_name;
			}
		}
		
		return $arr;
	}

	function get_active_group_membership_number_options($group_id=0){
		$this->select_all_secure('members');
		/*$this->db->select(array(
			'id as id',
    		'IFNULL('.$this->dx('membership_number').',"-") as membership_number',
		));*/
 
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$this->db->join('users','users.id ='.$this->dx('members.user_id'),'INNER');
		if(isset($this->group->id)){
        	$this->db->order_by($this->dx($this->group->member_listing_order_by?:'first_name'), $this->group->order_members_by?:'ASC', FALSE);
        }else{
        	$this->db->order_by($this->dx('first_name'),'ASC',FALSE);
        }
		$members = $this->db->get('members')->result();

		$arr = array();

		if($members)
		{
			foreach ($members as $key => $member) 
			{
				if($member->membership_number){
					$arr[$member->id] = $member->membership_number;
				}
			}
		}
		
		return $arr;
	}



	

	function get_active_group_member_options_by_user_id($group_id=0){
		$this->select_all_secure('members');
		$this->db->select(array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.language_id').' as language_id',
			));
		 
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$this->db->join('users','users.id ='.$this->dx('members.user_id'),'INNER');
		if(isset($this->group->id)){
        	$this->db->order_by($this->dx($this->group->member_listing_order_by?:'first_name'), $this->group->order_members_by?:'ASC', FALSE);
        }else{
        	$this->db->order_by($this->dx('first_name'),'ASC',FALSE);
        }
		$members = $this->db->get('members')->result();

		$arr = array();

		if($members)
		{
			foreach ($members as $key => $member) 
			{
				$arr[$member->user_id] = $member->first_name.' '.$member->last_name;
			}
		}
		
		return $arr;
	}

	function get_options(){
		$this->select_all_secure('members');
		$this->db->select(array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.language_id').' as language_id',
			));
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('users','users.id ='.$this->dx('members.user_id'),'INNER');
        $this->db->order_by($this->dx('first_name'), 'ASC', FALSE);
		$members = $this->db->get('members')->result();

		$arr = array();

		if($members)
		{
			foreach ($members as $key => $member) 
			{
				$arr[$member->id] = $member->first_name.' '.$member->last_name;
			}
		}
		
		return $arr;
	}

	function get_group_member_options_for_messaging(){
		$this->select_all_secure('members');
		$this->db->select(array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.phone').' as phone',
				$this->dx('users.language_id').' as language_id',
			));
		// $this->db->where($this->dx('members.group_id').'='.$this->group->id,NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$this->db->join('users','users.id ='.$this->dx('members.user_id'),'INNER');

		$members = $this->db->get('members')->result();

		$arr = array();

		if($members)
		{
			foreach ($members as $key => $member) 
			{
				$arr[$member->id] = $member->first_name.' '.$member->last_name.' - ('.$member->phone.')';
			}
		}
		
		return $arr;
	}

	function get_group_member_options_for_emailing(){
		$this->select_all_secure('members');
		$this->db->select(array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.email').' as email',
				$this->dx('users.language_id').' as language_id',
			));
		// $this->db->where($this->dx('members.group_id').'='.$this->group->id,NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$this->db->join('users','users.id ='.$this->dx('members.user_id'),'INNER');
		$members = $this->db->get('members')->result();

		$arr = array();

		if($members)
		{
			foreach ($members as $key => $member) 
			{
				if($member->email){
					if($member->membership_number){

					}else{
						$member->membership_number = 'membership number not set';
					}
					$arr[$member->id] = $member->first_name.' '.$member->last_name.' - ('.$member->membership_number.')';
				}
			}
		}
		
		return $arr;
	}

	function get_member_by_membership_number($membership_number = 0){
		$this->select_all_secure('members');
		// 
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('membership_number').' = "'.$membership_number.'"',NULL,FALSE);
		return $this->db->get('members')->row();
	}

	function get_member_by_group_role_id($group_role_id = 0){
		if($group_role_id){
			$this->select_all_secure('members');
			// 
			$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
			$this->db->where($this->dx('group_role_id').' = "'.$group_role_id.'"',NULL,FALSE);
			return $this->db->get('members')->row();
		}else{
			return FALSE;
		}
	}

	function get_member_by_organization_role_id($organization_role_id = 0){
		if($organization_role_id){
			$this->select_all_secure('members');
		
			$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
			$this->db->where($this->dx('organization_role_id').' = "'.$organization_role_id.'"',NULL,FALSE);
			return $this->db->get('members')->row();
		}else{
			return FALSE;
		}
	}

	function get_all()
	{
		$this->select_all_secure('members');
		return $this->db->get('members')->result();
	}

	function delete($id=0){
		$this->db->where('id',$id);
		return $this->db->delete('members');
	}

	function get_group_member_next_of_kin_entries($group_id = 0,$member_id = 0){
		$this->select_all_secure('next_of_kin');
		// $this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		return $this->db->get('next_of_kin')->result();
	}

	function delete_next_of_kin($group_id = 0,$member_id = 0){
		// $this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		return $this->db->delete('next_of_kin');
	}

	function check_if_user_is_in_group($group_id = 0,$user_id = 0){
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('user_id').' = "'.$user_id.'"',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		if($this->db->count_all_results('members')>0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	function get_group_member_by_user_id($group_id = 0, $user_id = 0){
		$this->select_all_secure('members');
		$this->db->select(
			array(
				$this->dx('users.avatar').' as avatar',
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.middle_name').' as middle_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.phone').' as phone',
				$this->dx('users.email').' as email',
				$this->dx('users.id_number').' as id_number',
				$this->dx('users.language_id').' as language_id',
			)
		);
		
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('user_id').' = "'.$user_id.'"',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		return $this->db->get('members')->row();
	}

	function get_group_members($group_id = 0,$filter_parameters = array(),$order_dir=''){
		$name = trim($this->input->get('name'));
		$names_array = explode(' ',$name);
		$search_query = '';
		$this->db->select(
			array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.middle_name').' as middle_name',
				$this->dx('users.phone').' as phone',
				$this->dx('users.email').' as email',
				$this->dx('users.loan_limit').' as loan_limit',
				$this->dx('users.id_number').' as id_number',
				$this->dx('users.avatar').' as avatar',
				$this->dx('users.join_code').' as join_code',
				$this->dx('users.email_join_code').' as email_join_code',
				$this->dx('users.last_login').' as last_login',
				$this->dx('users.language_id').' as language_id',
			)
		);
		$this->select_all_secure('members');
		
		if(count($names_array)>1){
			$search_query.=" ( ";
			$count = 1;
			foreach($names_array as $name){
				$name = $this->escape_str($name);
				if($count==1){
					$search_query.=" CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$name."%' ";
				}else{
					$search_query.=" OR CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$name."%' ";
				}
				$count++;
			}
			$search_query.=" ) ";
			$this->db->where($search_query,NULL,FALSE);
		}else{
			if($name){
				$phone = valid_phone($name);
				if($phone){
					$txt = "OR
					CONVERT(" . $this->dx('phone') . " USING 'latin1')  like '%" . $this->escape_str($phone) . "%'";
				}else{
					$txt = "";
				}
				$this->db->where(" ( 
					CONVERT(" . $this->dx('first_name') . " USING 'latin1')  like '%" . $this->escape_str($name) . "%' OR 
					CONVERT(" . $this->dx('last_name') . " USING 'latin1')  like '%" . $this->escape_str($name) . "%' OR
					CONVERT(" . $this->dx('phone') . " USING 'latin1')  like '%" . $this->escape_str($name) . "%' ".$txt."
					)", NULL, FALSE);
			}
		}
		if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
			$member_list = '0';
			$members = $filter_parameters['member_id'];
			$count = 1;
			foreach($members as $member_id){
				if($member_id){
					if($count==1){
						$member_list = $member_id;
					}else{
						$member_list .= ','.$member_id;
					}
					$count++;
				}
			}
			if($member_list){
        		$this->db->where('members.id IN ('.$member_list.')',NULL,FALSE);
			}
		}
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
        $this->db->order_by($this->dx('members.active'), 'DESC', FALSE);
        if(isset($this->group->id)){
        	$this->db->order_by($this->dx($this->group->member_listing_order_by?:'first_name'), $this->group->order_members_by?:'ASC', FALSE);
        }else{
        	$this->db->order_by($this->dx('first_name'),$order_dir,FALSE);
        }
        return $this->db->get('members')->result();
	}

	function get_group_membership_requests($group_id = 0,$filter_parameters = array(),$order_dir=''){
		$name = trim($this->input->get('name'));
		$names_array = explode(' ',$name);
		$search_query = '';
		$this->select_all_secure('group_membership_requests');
		
		if(count($names_array)>1){
			$search_query.=" ( ";
			$count = 1;
			foreach($names_array as $name){
				$name = $this->escape_str($name);
				if($count==1){
					$search_query.=" CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$name."%' ";
				}else{
					$search_query.=" OR CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$name."%' ";
				}
				$count++;
			}
			$search_query.=" ) ";
			$this->db->where($search_query,NULL,FALSE);
		}else{
			if($name){
				$phone = valid_phone($name);
				if($phone){
					$txt = "OR
					CONVERT(" . $this->dx('group_membership_requests.phone') . " USING 'latin1')  like '%" . $this->escape_str($phone) . "%'";
				}else{
					$txt = "";
				}
				$this->db->where(" ( 
					CONVERT(" . $this->dx('group_membership_requests.first_name') . " USING 'latin1')  like '%" . $this->escape_str($name) . "%' OR 
					CONVERT(" . $this->dx('group_membership_requests.last_name') . " USING 'latin1')  like '%" . $this->escape_str($name) . "%' OR
					CONVERT(" . $this->dx('group_membership_requests.phone') . " USING 'latin1')  like '%" . $this->escape_str($name) . "%' ".$txt."
					)", NULL, FALSE);
			}
		}
		if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
			$member_list = '0';
			$members = $filter_parameters['member_id'];
			$count = 1;
			foreach($members as $member_id){
				if($member_id){
					if($count==1){
						$member_list = $member_id;
					}else{
						$member_list .= ','.$member_id;
					}
					$count++;
				}
			}
			if($member_list){
        		$this->db->where('group_membership_requests.id IN ('.$member_list.')',NULL,FALSE);
			}
		}
		$this->db->where($this->dx('group_membership_requests.is_deleted').' IS NULL ',NULL,FALSE);
        if(isset($this->group->id)){
        	$this->db->order_by($this->dx('group_membership_requests.first_name'), 'ASC', FALSE);
        }else{
        	$this->db->order_by($this->dx('group_membership_requests.first_name'),$order_dir,FALSE);
        }
        return $this->db->get('group_membership_requests')->result();
	}

	function get_group_members_with_next_of_kin_details($group_id = 0){
		$this->db->select(
			array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.phone').' as phone',
				$this->dx('users.email').' as email',
				$this->dx('users.avatar').' as avatar',
				$this->dx('users.join_code').' as join_code',
				$this->dx('users.language_id').' as language_id',
				$this->dx('users.email_join_code').' as email_join_code',
				$this->dx('users.last_login').' as last_login',
				$this->dx('next_of_kin.full_name').' as next_of_kin_full_name',
				$this->dx('next_of_kin.email').' as next_of_kin_email',
				$this->dx('next_of_kin.phone').' as next_of_kin_phone',
				$this->dx('next_of_kin.relationship').' as next_of_kin_relationship',
			)
		);
		$this->select_all_secure('members');
		
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		$this->db->join('next_of_kin','members.id = '.$this->dx('next_of_kin.member_id'),'LEFT');
		if(isset($this->group)){
        	$this->db->order_by($this->dx($this->group->member_listing_order_by?:'first_name'), $this->group->order_members_by?:'ASC', FALSE);
        }else{
        	$this->db->order_by($this->dx('first_name'),'ASC',FALSE);
        }
		return $this->db->get('members')->result();
	}

	function get_active_group_members($group_id = 0,$filter_parameters = array()){
		$name = trim($this->input->get('name'));
		$names_array = explode(' ',$name);
		$search_query = '';
		$this->db->select(
			array(
				$this->dx('users.id').' as user_id',
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.phone').' as phone',
				$this->dx('users.email').' as email',
				$this->dx('users.avatar').' as avatar',
				$this->dx('users.join_code').' as join_code',
				$this->dx('users.email_join_code').' as email_join_code',
				$this->dx('users.last_login').' as last_login',
				$this->dx('users.language_id').' as language_id',
				
			)
		);
		$this->select_all_secure('members');
		
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
		if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
			$member_list = '0';
			$members = $filter_parameters['member_id'];
			$count = 1;
			foreach($members as $member_id){
				if($member_id){
					if($count==1){
						$member_list = $member_id;
					}else{
						$member_list .= ','.$member_id;
					}
					$count++;
				}
			}
			if($member_list){
        		$this->db->where('members.id IN ('.$member_list.')',NULL,FALSE);
			}
		}
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').' = "1"',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		if(isset($this->group->id)){
			//echo $this->group->member_listing_order_by; die;
        	$this->db->order_by($this->dx($this->group->member_listing_order_by?:'first_name'), $this->group->order_members_by?:'ASC', FALSE);
        }else{
        	$this->db->order_by($this->dx('first_name'),'ASC',FALSE);
        }
		return $this->db->get('members')->result();
	}

	function get_group_members_array($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'members.id as id',
				$this->dx('users.id').'as user_id',
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.phone').' as phone',
				$this->dx('users.email').' as email',
				$this->dx('users.avatar').' as avatar',
				$this->dx('users.language_id').' as language_id',
			)
		);
		$this->select_all_secure('members');
		
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		if(isset($this->group->id)){
        	$this->db->order_by($this->dx($this->group->member_listing_order_by?:'first_name'), $this->group->order_members_by?:'ASC', FALSE);
        }else{
        	$this->db->order_by($this->dx('first_name'),'ASC',FALSE);
        }
		$members = $this->db->get('members')->result();
		foreach($members as $member){
			$arr[$member->id] = $member;
		}
		return $arr;
	}

	function count_group_members($group_id = 0,$filter_parameters = array()){
		$name = strip_tags(trim($this->input->get('name')));
		$names_array = explode(' ',$name);
		$search_query = '';
		if($name){
			$this->db->where("CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%'",NULL,FALSE);
		}
		if(count($names_array)>1){
			$search_query.=" ( ";
			$count = 1;
			foreach($names_array as $name){
				$name = $this->escape_str($name);
				if($count==1){
					$search_query.=" CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$name."%' ";
				}else{
					$search_query.=" OR CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$name."%' ";
				}
				$count++;
			}
			$search_query.=" ) ";
			$this->db->where($search_query,NULL,FALSE);
		}else{
			if($name){
				$phone = valid_phone($name);
				if($phone){
					$txt = "OR
					CONVERT(" . $this->dx('phone') . " USING 'latin1')  like '%" . $this->escape_str($phone) . "%'";
				}else{
					$txt = "";
				}
				$this->db->where(" ( 
					CONVERT(" . $this->dx('first_name') . " USING 'latin1')  like '%" . $this->escape_str($name) . "%' OR 
					CONVERT(" . $this->dx('last_name') . " USING 'latin1')  like '%" . $this->escape_str($name) . "%' OR
					CONVERT(" . $this->dx('phone') . " USING 'latin1')  like '%" . $this->escape_str($name) . "%' ".$txt."
					)", NULL, FALSE);
			}
		}
		
		if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
			$member_list = '0';
			$members = $filter_parameters['member_id'];
			$count = 1;
			foreach($members as $member_id){
				if($member_id){
					if($count==1){
						$member_list = $member_id;
					}else{
						$member_list .= ','.$member_id;
					}
					$count++;
				}
			}
			if($member_list){
        		$this->db->where('members.id IN ('.$member_list.')',NULL,FALSE);
			}
		}
		
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		return $this->db->count_all_results('members');
	}

	function count_group_membership_requests($group_id = 0,$filter_parameters = array()){
		$name = strip_tags(trim($this->input->get('name')));
		$names_array = explode(' ',$name);
		$search_query = '';
		if($name){
			$this->db->where("CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%'",NULL,FALSE);
		}
		if(count($names_array)>1){
			$search_query.=" ( ";
			$count = 1;
			foreach($names_array as $name){
				$name = $this->escape_str($name);
				if($count==1){
					$search_query.=" CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$name."%' ";
				}else{
					$search_query.=" OR CONVERT(".$this->dx('first_name')." USING 'latin1') LIKE '%".$name."%' OR CONVERT(".$this->dx('last_name')." USING 'latin1') LIKE '%".$name."%' ";
				}
				$count++;
			}
			$search_query.=" ) ";
			$this->db->where($search_query,NULL,FALSE);
		}else{
			if($name){
				$phone = valid_phone($name);
				if($phone){
					$txt = "OR
					CONVERT(" . $this->dx('phone') . " USING 'latin1')  like '%" . $this->escape_str($phone) . "%'";
				}else{
					$txt = "";
				}
				$this->db->where(" ( 
					CONVERT(" . $this->dx('first_name') . " USING 'latin1')  like '%" . $this->escape_str($name) . "%' OR 
					CONVERT(" . $this->dx('last_name') . " USING 'latin1')  like '%" . $this->escape_str($name) . "%' OR
					CONVERT(" . $this->dx('phone') . " USING 'latin1')  like '%" . $this->escape_str($name) . "%' ".$txt."
					)", NULL, FALSE);
			}
		}
		
		if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
			$member_list = '0';
			$members = $filter_parameters['member_id'];
			$count = 1;
			foreach($members as $member_id){
				if($member_id){
					if($count==1){
						$member_list = $member_id;
					}else{
						$member_list .= ','.$member_id;
					}
					$count++;
				}
			}
			if($member_list){
        		$this->db->where('group_membership_requests.id IN ('.$member_list.')',NULL,FALSE);
			}
		}
		if($group_id){
			$this->db->where($this->dx('group_membership_requests.group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_membership_requests.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('group_membership_requests.is_deleted').' IS NULL ',NULL,FALSE);
		return $this->db->count_all_results('group_membership_requests');
	}

	function count_active_group_members($group_id = 0,$filter_parameters = array()){
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
		if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
			$member_list = '0';
			$members = $filter_parameters['member_id'];
			$count = 1;
			foreach($members as $member_id){
				if($member_id){
					if($count==1){
						$member_list = $member_id;
					}else{
						$member_list .= ','.$member_id;
					}
					$count++;
				}
			}
			if($member_list){
        		$this->db->where('members.id IN ('.$member_list.')',NULL,FALSE);
			}
		}
		
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').' = "1"',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		return $this->db->count_all_results('members');
	}

	function count_all(){
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').' = "1"',NULL,FALSE);
		return $this->db->count_all_results('members');
	}

	function count_members_in_groups($group_ids=array()){
		$group_list = '';
		/*if($group_ids){
			foreach ($group_ids as $id) {
				if($group_list){
					$group_list.=','.$id;
				}else{
					$group_list=$id;
				}
			}
		}*/
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id').' = 0 ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' IN('.implode(',',$group_ids).') ',NULL,FALSE);
		}
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').' = "1"',NULL,FALSE);
		//$this->db->where($this->dx('group_id').' IN ('.$group_list.')',NULL,FALSE);
		return $this->db->count_all_results('members')?:0;
	}

	function get_group_members_by_id($group_id=''){
		$this->db->select(array($this->dx('users.first_name').' as first_name',$this->dx('users.last_name').' as last_name',$this->dx('users.phone').' as phone',$this->dx('users.email').' as email',$this->dx('users.last_login').' as last_login'));
		$this->select_all_secure('members');
		$this->db->where($this->dx('members.group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		$this->db->order_by($this->dx('users.first_name'),'ASC',FALSE);
		return $this->db->get('members')->result();
	}

	function get_group_members_by_id_for_admin($group_id=''){
		$this->db->select(array($this->dx('users.first_name').' as first_name',$this->dx('users.last_name').' as last_name',$this->dx('users.phone').' as phone',$this->dx('users.email').' as email',$this->dx('users.last_login').' as last_login'));
		$this->select_all_secure('members');
		$this->db->where($this->dx('members.group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		$this->db->order_by($this->dx('users.first_name'),'ASC',FALSE);
		return $this->db->get('members')->result();
	}

	function get_member_group_role_ids($group_id = 0){
		$arr = array();
		$this->db->select(array('id',$this->dx('group_role_id').' as group_role_id '));
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		$members = $this->db->get('members')->result();
		foreach ($members as $member) {
			$arr[$member->id] = $member->group_role_id;
		}
		return $arr;
	}

	function get_assigned_group_role_options($group_id = 0){
		$this->db->select(array(
			$this->dx('group_role_id'). ' as group_role_id',
		));
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		$results = $this->db->get('members')->result();
		$arr = array();
		foreach ($results as $result) {
			$arr[$result->group_role_id] = $result->group_role_id;
		}
		return $arr;
	}

	function get_member_oranization_role_ids($group_id = 0){
		$arr = array();
		$this->db->select(array('id',$this->dx('organization_role_id').' as organization_role_id '));
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$members = $this->db->get('members')->result();
		foreach ($members as $member) {
			$arr[$member->id] = $member->organization_role_id;
		}
		return $arr;
	}

	function count_all_active_group_members($group_id=0){
   		$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
   		return $this->db->count_all_results('members')?:0;
   	}

   	function get_group_member_ids($group_id=0){
   		$arr = array();
   		$this->db->select(array('id'));
   		$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
   		$members = $this->db->get('members')->result();
   		foreach($members as $member){
   			$arr[] = $member->id;
   		}
   		return $arr;
   	}

   	function get_group_member_by_group_user_id($group_id = 0,$user_id = 0){
   		$this->db->select(array('id'));
   		$this->db->where($this->dx('user_id').'='.$user_id,NULL,FALSE);
		//$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
   		$member = $this->db->get('members')->row();
   		return $member;
   	}

   	function get_member_by_user_id($user_id = 0){
   		$this->select_all_secure('members');
   		$this->db->where($this->dx('user_id').'='.$user_id,NULL,FALSE);
		//$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
   		$member = $this->db->get('members')->result();
   		return $member;
   	}

   	function get_member_group_ids_list_where_user_id($user_id = 0){
   		$member_group_id_list = "";
   		$this->db->select(
   			array(
   				$this->dx('group_id').' as group_id '
   			)
   		);
   		$this->db->where($this->dx('user_id').'="'.$user_id.'"',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$result = $this->db->get('members')->result();
		$count = 1;
		foreach($result as $row){
			if($count==1){
				$member_group_id_list.=$row->group_id;
			}else{
				$member_group_id_list.=','.$row->group_id;
			}
			$count++;
		}
   		return $member_group_id_list;
   	}

   	function get_member_user_ids_list_by_group_id_list($member_group_id_list = "0"){
   		$member_user_ids_list = "";
   		$this->db->select(
   			array(
   				$this->dx('user_id').' as user_id ',
   				'members.id as member_id'
   			)
   		);
   		if($member_group_id_list){
   			$this->db->where($this->dx('group_id').'IN('.$member_group_id_list.')',NULL,FALSE);
   		}else{
   			$this->db->where($this->dx('group_id').'IN(0)',NULL,FALSE);
   		}
		//$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$result = $this->db->get('members')->result();
   		foreach($result as $row):
   			$count = 1;
			foreach($result as $row){
				if($count==1){
					$member_user_ids_list.=$row->user_id;
				}else{
					$member_user_ids_list.=','.$row->user_id;
				}
				$count++;
			}
   		endforeach;
   		return $member_user_ids_list;
   	}

   	function get_member_where_user_id($user_id=0,$group_id=0){
   		$this->db->select(
			array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.phone').' as phone',
				$this->dx('users.email').' as email',
				$this->dx('users.avatar').' as avatar',
				$this->dx('users.language_id').' as language_id',
			)
		);
   		$this->select_all_secure('members');
   		$this->db->where($this->dx('user_id').'="'.$user_id.'"',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
	
		$this->db->join('users',$this->dx('members.user_id').' = users.id',NULL,FALSE);
   		return $this->db->get('members')->row();
   	}


   	function get_member_where_member_id($id=0,$group_id=0){
   		$this->select_all_secure('members');
   		$this->db->where('id',$id);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		
   		return $this->db->get('members')->row();
   	}

   	function get_suspended_members_ids_array(){
   		$arr = array();
   		$this->db->select(array('id'));
   		$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->where($this->dx('active').'="0"',NULL,FALSE);
   		$members = $this->db->get('members')->result();
   		foreach($members as $member){
   			$arr[] = $member->id;
   		}
   		return $arr;
   	}

   	function get_paying_group_owners_and_administrators(){
   		$this->db->select(
   			array(
   				$this->dx('users.first_name').' as first_name ',
				$this->dx('users.last_name').' as last_name ',
   				$this->dx('users.phone').' as phone ',
   				$this->dx('users.email').' as email ',
   				$this->dx('members.group_id').' as group_id ',
   				$this->dx('users.language_id').' as language_id',
   			)
   		);
   		$this->db->where($this->dx('members.is_admin').'="1"',NULL,FALSE);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('investment_groups.status').' = 1 ',NULL,FALSE);
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
   		return $this->db->get('members')->result();
   	}

   	function get_member_options_by_group_id_list($group_id_list = "0"){
   		$this->db->select(
   			array(
   				$this->dx('users.first_name').' as first_name ',
				$this->dx('users.last_name').' as last_name ',
   				$this->dx('users.phone').' as phone ',
   				$this->dx('users.email').' as email ',
   				$this->dx('members.group_id').' as group_id ',
   				$this->dx('users.language_id').' as language_id',
   			)
   		);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where('investment_groups.id IN ('.$group_id_list.')',NULL,FALSE);
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
   		return $this->db->get('members')->result();
   	}

   	function count_paying_group_owners_and_administrators(){
   		$this->db->where($this->dx('members.is_admin').'="1"',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('investment_groups.status').' = 1 ',NULL,FALSE);
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
   		return $this->db->count_all_results('members');
   	}

   	function get_paying_group_owners_administrators_and_members(){
   		$this->db->select(
   			array(
   				$this->dx('users.first_name').' as first_name ',
				$this->dx('users.last_name').' as last_name ',
   				$this->dx('users.phone').' as phone ',
   				$this->dx('users.email').' as email ',
   				$this->dx('members.group_id').' as group_id ',
   				$this->dx('users.language_id').' as language_id',
   			)
   		);
   		//$this->db->where($this->dx('members.is_admin').'="1"',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('investment_groups.status').' = 1 ',NULL,FALSE);
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
   		return $this->db->get('members')->result();
   	}

   	function count_paying_group_owners_administrators_and_members(){
   		//$this->db->where($this->dx('members.is_admin').'="1"',NULL,FALSE);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('investment_groups.status').' = 1 ',NULL,FALSE);
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
   		return $this->db->count_all_results('members');
   	}

   	function get_in_arrears_paying_group_owners_and_administrators(){
   		$arr = array();
   		$this->db->select(
   			array(
   				$this->dx('users.first_name').' as first_name ',
				$this->dx('users.last_name').' as last_name ',
   				$this->dx('users.phone').' as phone ',
   				$this->dx('users.email').' as email ',
   				$this->dx('users.language_id').' as language_id',
				'sum('.$this->dx('billing_payments.amount').') - sum('.$this->dx('billing_invoices.amount').') as arrears'
   			)
   		);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->where($this->dx('members.is_admin').'="1"',NULL,FALSE);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('investment_groups.status').' = 1 ',NULL,FALSE);
		//$this->db->where('arrears > ',0);
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		$this->db->join('billing_payments',$this->dx('members.group_id').' = '.$this->dx('billing_payments.group_id'));
		$this->db->join('billing_invoices',$this->dx('members.group_id').' = '.$this->dx('billing_invoices.group_id'));
   		$members = $this->db->get('members')->result();
   		foreach($members as $member):
   			if($member->arrears>0):
   				$arr[] = $member;
   			endif;
   		endforeach;
   		return $arr;
   	}

   	function count_in_arrears_paying_group_owners_and_administrators(){
   		$this->db->select(
   			array(
				'sum('.$this->dx('billing_payments.amount').') - sum('.$this->dx('billing_invoices.amount').') as arrears'
   			)
   		);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->where($this->dx('members.is_admin').'="1"',NULL,FALSE);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('investment_groups.status').' = 1 ',NULL,FALSE);
		//$this->db->where('arrears > ',0);
		$this->db->join('billing_payments',$this->dx('members.group_id').' = '.$this->dx('billing_payments.group_id'));
		$this->db->join('billing_invoices',$this->dx('members.group_id').' = '.$this->dx('billing_invoices.group_id'));
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
   		return $this->db->count_all_results('members');
   	}

   	function get_in_arrears_paying_group_owners_administrators_and_members(){
   		$arr = array();
   		$this->db->select(
   			array(
   				$this->dx('users.first_name').' as first_name ',
				$this->dx('users.last_name').' as last_name ',
   				$this->dx('users.phone').' as phone ',
   				$this->dx('users.email').' as email ',
   				$this->dx('users.language_id').' as language_id',
				'sum('.$this->dx('billing_payments.amount').') - sum('.$this->dx('billing_invoices.amount').') as arrears'
   			)
   		);
   		//$this->db->where($this->dx('members.is_admin').'="1"',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('investment_groups.status').' = 1 ',NULL,FALSE);
		//$this->db->where('arrears > ',0);
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		$this->db->join('billing_payments','investment_groups.id = '.$this->dx('billing_payments.group_id'));
		$this->db->join('billing_invoices','investment_groups.id = '.$this->dx('billing_invoices.group_id'));
   		$members = $this->db->get('members')->result();
   		foreach($members as $member):
   			if($member->arrears>0):
   				$arr[] = $member;
   			endif;
   		endforeach;
   		return $arr;
   	}

   	function count_in_arrears_paying_group_owners_administrators_and_members(){
   		//$this->db->where($this->dx('members.is_admin').'="1"',NULL,FALSE);
   		$this->db->select(
   			array(
				'sum('.$this->dx('billing_payments.amount').') - sum('.$this->dx('billing_invoices.amount').') as arrears'
   			)
   		);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('investment_groups.status').' = 1 ',NULL,FALSE);
		$this->db->where('arrears > ',0);
		$this->db->join('billing_payments','investment_groups.id = '.$this->dx('billing_payments.group_id'));
		$this->db->join('billing_invoices','investment_groups.id = '.$this->dx('billing_invoices.group_id'));
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
   		return $this->db->count_all_results('members');
   	}

   	function get_on_trial_group_owners_and_administrators(){
   		$this->db->select(
   			array(
   				$this->dx('users.first_name').' as first_name ',
				$this->dx('users.last_name').' as last_name ',
   				$this->dx('users.phone').' as phone ',
   				$this->dx('users.email').' as email ',
   				$this->dx('users.language_id').' as language_id',
   			)
   		);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->where($this->dx('members.is_admin').'="1"',NULL,FALSE);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('trial_days').' > 0',NULL,FALSE);
   		$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL )',NULL,FALSE);
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
   		return $this->db->get('members')->result();
   	}

   	function count_on_trial_group_owners_and_administrators(){
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->where($this->dx('members.is_admin').'="1"',NULL,FALSE);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('trial_days').' > 0',NULL,FALSE);
   		$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL )',NULL,FALSE);
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
   		return $this->db->count_all_results('members');
   	}

   	function get_on_trial_group_owners_administrators_and_members(){
   		$this->db->select(
   			array(
   				$this->dx('users.first_name').' as first_name ',
				$this->dx('users.last_name').' as last_name ',
   				$this->dx('users.phone').' as phone ',
   				$this->dx('users.email').' as email ',
   				$this->dx('users.language_id').' as language_id',
   			)
   		);
		$this->db->where($this->dx('trial_days').' > 0',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		//$this->db->where($this->dx('members.is_admin').'="1"',NULL,FALSE);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
   		$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL )',NULL,FALSE);
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
   		return $this->db->get('members')->result();
   	}

   	function count_on_trial_group_owners_administrators_and_members(){
   		//$this->db->where($this->dx('members.is_admin').'="1"',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('trial_days').' > 0',NULL,FALSE);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
   		$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL )',NULL,FALSE);
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
   		return $this->db->count_all_results('members');
   	}

   	function get_trial_expired_group_owners_administrators_and_members(){
   		$this->db->select(
   			array(
   				$this->dx('users.first_name').' as first_name ',
				$this->dx('users.last_name').' as last_name ',
   				$this->dx('users.phone').' as phone ',
   				$this->dx('users.language_id').' as language_id',
   				$this->dx('users.email').' as email ',
   			)
   		);

		$this->db->where($this->dx('trial_days').' < 1',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		//$this->db->where($this->dx('members.is_admin').'="1"',NULL,FALSE);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
   		$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL )',NULL,FALSE);
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
   		return $this->db->get('members')->result();
   	}

   	function count_trial_expired_group_owners_administrators_and_members(){
		$this->db->where($this->dx('trial_days').' < 1',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		//$this->db->where($this->dx('members.is_admin').'="1"',NULL,FALSE);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
   		$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL )',NULL,FALSE);
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
   		return $this->db->count_all_results('members');
   	}


   	function get_trial_expired_group_owners_and_administrators(){
   		$this->db->select(
   			array(
   				$this->dx('users.first_name').' as first_name ',
				$this->dx('users.last_name').' as last_name ',
   				$this->dx('users.phone').' as phone ',
   				$this->dx('users.email').' as email ',
   				$this->dx('users.language_id').' as language_id',
   			)
   		);

		$this->db->where($this->dx('trial_days').' < 1',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->where($this->dx('members.is_admin').'="1"',NULL,FALSE);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
   		$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL )',NULL,FALSE);
		// $this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
   		return $this->db->get('members')->result();
   	}
	   function get_applicant_by_id_number($id_number=''){
		$this->db->select(
			array(
				'members.id as id',
				$this->dx('members.user_id').' as user_id',
				$this->dx('members.group_id').' as group_id',
				$this->dx('members.group_role_id').' as group_role_id',
				$this->dx('members.is_admin').' as is_admin',
				$this->dx('members.membership_number').' as membership_number',
			)
		);
		$this->db->select(
			array(
				$this->dx('users.avatar').' as avatar',
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.middle_name').' as middle_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.phone').' as phone',
				$this->dx('users.loan_limit').' as loan_limit',
				$this->dx('users.email').' as email',
				$this->dx('users.id_number').' as id_number',
			)
		);
		$this->db->where($this->dx('users.id_number').'="'.$id_number.'"',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		$this->db->limit(1);
		return $this->db->get('members')->row();
	}
	function get_applicant_by_phone_number($phone=''){
		$this->db->select(
			array(
				'members.id as id',
				$this->dx('members.user_id').' as user_id',
				$this->dx('members.group_id').' as group_id',
				$this->dx('members.group_role_id').' as group_role_id',
				$this->dx('members.is_admin').' as is_admin',
				$this->dx('members.membership_number').' as membership_number',
			)
		);
		$this->db->select(
			array(
				$this->dx('users.avatar').' as avatar',
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.middle_name').' as middle_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.phone').' as phone',
				$this->dx('users.loan_limit').' as loan_limit',
				$this->dx('users.email').' as email',
				$this->dx('users.id_number').' as id_number',
			)
		);
		$this->db->where($this->dx('users.phone').'="'.$phone.'"',NULL,FALSE);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		$this->db->limit(1);
		return $this->db->get('members')->row();
	}

   	function count_trial_expired_group_owners_and_administrators(){
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('trial_days').' < 1',NULL,FALSE);
   		$this->db->where($this->dx('members.is_admin').'="1"',NULL,FALSE);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
   		$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL )',NULL,FALSE);
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
   		return $this->db->count_all_results('members');
   	}


   	function get_all_members(){
   		$this->db->select(
   			array(
   				$this->dx('users.first_name').' as first_name ',
				$this->dx('users.last_name').' as last_name ',
   				$this->dx('users.phone').' as phone ',
   				$this->dx('users.email').' as email ',
   				$this->dx('users.language_id').' as language_id',
   			)
   		);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
   		return $this->db->get('members')->result();
   	}

   	function count_all_members($group_id = 0){
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   	
   		return $this->db->count_all_results('members');
   	}

   	function get_group_member_email_address_list_by_member_id_array($member_ids = array(),$group_id = 0){
   		$this->db->select(
   			array(
   				$this->dx('users.email').' as email ',
   			)
   		);
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
   		$this->db->where_in('members.id',$member_ids);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		$members = $this->db->get('members')->result();
		$email_list = '';

		$count = 1;
		foreach ($members as $member) {
			# code...
			if($count==1){
				$email_list = $member->email;
				
			}else{
				$email_list .= ','.$member->email;
			}
			$count++;
		}
		return $email_list;
   	}

   	function member_exists_in_group($phone,$group_id=0){
   		$user = $this->ion_auth->get_user_by_phone($phone);
   		if($user){
   			$this->db->select('id as id');
	   		$this->db->where($this->dx('group_id')." = '".$group_id."'",NULL,FALSE);
	   		$this->db->where($this->dx('user_id')." = '".$user->id."'",NULL,FALSE);
	   		$member = $this->db->get('members')->row();
	   		if($member){
	   			return $member->id;
	   		}else{
	   			return false;
	   		}
   		}else{
   			return false;
   		}
   		
   	}

   	function get_group_members_by_member_id_array($group_id = 0,$member_id_array = array()){
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
				$this->dx('users.language_id').' as language_id',
			)
		);
		$this->select_all_secure('members');
		
		if(empty($member_id_array)){
			$this->db->where_in('members.id',0);
		}else{
			$this->db->where_in('members.id',$member_id_array);
		}
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
        $this->db->order_by($this->dx('members.active'), 'DESC', FALSE);
        if(isset($this->group->id)){
        	$this->db->order_by($this->dx($this->group->member_listing_order_by?:'first_name'), $this->group->order_members_by?:'ASC', FALSE);
        }else{
        	$this->db->order_by($this->dx('first_name'),'ASC',FALSE);
        }

		return $this->db->get('members')->result();
	}

	function get_group_member_logged_in_counts_array(){
		$this->db->select(
			array(
				$this->dx('members.group_id').' as group_id',
				$this->dx('users.last_login').' as last_login',
				" COUNT(".$this->dx('last_login').") as logged_in_count ",
			)
		);
		$this->db->where($this->dx('last_login')." IS NOT NULL ",NULL,FALSE);
		$this->db->where($this->dx('is_deleted')." IS NULL ",NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
        $this->db->group_by(array($this->dx("group_id")));
		$group_members_logged_in_counts =  $this->db->get('members')->result();
		$arr = array();
		foreach($group_members_logged_in_counts as $group_members_logged_in_count):
			$arr[$group_members_logged_in_count->group_id] = $group_members_logged_in_count->logged_in_count;
		endforeach;
		return $arr;
	}

	function get_group_member_logged_in_percentages_array(){
		$group_member_logged_in_counts_array = $this->get_group_member_logged_in_counts_array();
		$group_active_sizes_array = $this->groups_m->get_group_sizes_array();
		$arr = array();
		foreach($group_member_logged_in_counts_array as $group_id => $logged_in_count):
			$arr[$group_id] = round($logged_in_count/$group_active_sizes_array[$group_id] * 100,2);
		endforeach;
		return $arr;
	}

	function get_user_by_member_id($member_id=0,$group_id=0){
		$this->select_all_secure('members');
		$this->db->where('id',$member_id);
		
		$member = $this->db->get('members')->row();

		if($member){
			$user_id = $member->user_id;
			return $this->ion_auth->get_user($user_id);
		}else{
			return FALSE;
		}
	}

	function get_group_user_ids_as_key_and_member_ids_as_values($group_id = 0){
		$this->select_all_secure('members');
		$this->db->where('id',$member_id);
		
		$arr = array();
		$members = $this->db->get('members')->result();
		foreach ($members as $key => $member):
			$arra[$member->user_id] = $member->id;
		endforeach;
		return $arr;
	}

	function get_member_user_id($member_id = 0,$group_id = 0){
		$this->db->select(array(
			$this->dx('user_id').' as user_id')
		);
		$this->db->where('id',$member_id);
		
		$member = $this->db->get('members')->row();
		return $member->user_id;
	}

	function get_user_ids_by_group_ids($group_ids= array()){
		$list = '';
		if($group_ids):
			foreach ($group_ids as $key => $group_id) {
				if($list){
					$list.=','.$group_id;
				}else{
					$list=$group_id;
				}
			}
		else:
			$list = '0';
		endif;

		$this->db->select(array($this->dx('user_id').' as user_id'));
		$this->db->where($this->dx('group_id').' IN ('.$list.')',NULL,FALSE);
		$result = $this->db->get('members')->result();
		$arr = array();
		if($result){
			foreach ($result as $key => $value) {
				$arr[$value->user_id] = $value->user_id;
			}
		}
		return $arr;
	}

	function get_paying_group_members($group_ids_array = array()){
		$this->db->select(
			array(
				'members.id as id',
				$this->dx('members.user_id').' as user_id',
				$this->dx('members.group_id').' as group_id',
				$this->dx('investment_groups.name').' as group_name',
				$this->dx('members.group_role_id').' as group_role_id',
				$this->dx('members.is_admin').' as is_admin',
				$this->dx('members.membership_number').' as membership_number',
				$this->dx('members.date_of_birth').' as date_of_birth',
				$this->dx('members.physical_address').' as physical_address',
				$this->dx('members.postal_address').' as postal_address',
				$this->dx('members.created_on').' as created_on',
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
				$this->dx('users.last_login').' as last_login',
				$this->dx('users.language_id').' as language_id',
			)
		);
		if(empty($group_ids_array)){
			$this->db->where($this->dx('group_id')." IN (0)",NULL,FALSE);

		}else{
			$this->db->where($this->dx('group_id')." IN (".implode(',',$group_ids_array).")",NULL,FALSE);

		}
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('members.group_id'));
		$this->db->order_by($this->dx('investment_groups.created_on'),'DESC',FALSE);
		$members = $this->db->get('members')->result();
		return $members;
	}

	function check_if_group_role_id_is_assigned($group_role_id = 0,$group_id = 0){
   		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
   		$this->db->where($this->dx('group_role_id').' = "'.$group_role_id.'"',NULL,FALSE);
   		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->where($this->dx('members.active').' ="1" ',NULL,FALSE);
   		return $this->db->count_all_results('members');
   	}

   	function get_active_group_members_member_ids($group_id = 0){
		$this->db->select('members.id');
		
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').' = "1"',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		$results =  $this->db->get('members')->result();
		$arr = array();
		if($results){
			foreach ($results as $result) {
				$arr[] = $result->id;
			}
		}
		return $arr;
	}

	function delete_group_member_next_of_kin($group_id = 0,$member_id = 0){
		$this->db->where($this->dx('group_id')." = '".$group_id."'",NULL,FALSE);
		$this->db->where($this->dx('member_id')." = '".$member_id."'",NULL,FALSE);
		return $this->db->delete('next_of_kin');
	}

	function get_group_member_objects_array($group_ids = array(),$member_ids = array()){
		$this->db->select(
			array(
				'members.id as id',
				$this->dx('members.user_id').' as user_id',
				$this->dx('members.group_id').' as group_id',
				$this->dx('members.group_role_id').' as group_role_id',
				$this->dx('members.is_admin').' as is_admin',
				$this->dx('members.membership_number').' as membership_number',
				$this->dx('members.date_of_birth').' as date_of_birth',
				$this->dx('members.physical_address').' as physical_address',
				$this->dx('members.postal_address').' as postal_address',
				$this->dx('members.organization_role_id').' as organization_role_id',
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
				$this->dx('users.language_id').' as language_id',
				$this->dx('users.last_login').' as last_login',
			)
		);
		if(empty($member_ids)){
			$this->db->where_in('members.id',0);
		}else{
			$this->db->where_in('members.id',$member_ids);
		}
		if(empty($group_ids)){
			$this->db->where($this->dx('members.group_id').' = 0 ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('members.group_id').' IN('.implode(',',$group_ids).') ',NULL,FALSE);
		}
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		$members = $this->db->get('members')->result();
		foreach($members as $member):
			$arr[$member->id] = $member;
		endforeach;
		return $arr;

	}

	function get_active_group_member_recipient_options($group_id=0){
		$this->select_all_secure('members');
		$this->db->select(array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.language_id').' as language_id',
				$this->dx('users.phone').' as phone',
			));
		
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').' ="1" ',NULL,FALSE);
		$this->db->join('users','users.id ='.$this->dx('members.user_id'),'INNER');
		// if(!$group_id){
        // 	$this->db->order_by($this->dx($this->group->member_listing_order_by?:'first_name'), $this->group->order_members_by?:'ASC', FALSE);
        // }
		$members = $this->db->get('members')->result();
		$arr = array();
		if($members){
			foreach ($members as $key => $member) {
				$arr["member-".$member->id] = $member->first_name.' '.$member->last_name." - ".$member->phone;
			}
		}

		return $arr;
	}

	function get_active_group_officials($group_id = 0,$filter_parameters = array()){
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
				$this->dx('users.language_id').' as language_id',
				$this->dx('users.email_join_code').' as email_join_code',
			)
		);
		$this->select_all_secure('members');
		
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
		if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
			$member_list = '0';
			$members = $filter_parameters['member_id'];
			$count = 1;
			foreach($members as $member_id){
				if($member_id){
					if($count==1){
						$member_list = $member_id;
					}else{
						$member_list .= ','.$member_id;
					}
					$count++;
				}
			}
			if($member_list){
        		$this->db->where('members.id IN ('.$member_list.')',NULL,FALSE);
			}
		}
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('members.group_role_id').' >= "1"',NULL,FALSE);
		$this->db->where($this->dx('users.is_validated').' = "1"',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		if($group_id){
			$this->db->order_by($this->dx('first_name'),'ASC',FALSE);
		}else{
			$this->db->order_by($this->dx($this->group->member_listing_order_by?:'first_name'), $this->group->order_members_by?:'ASC', FALSE);
		}
		return $this->db->get('members')->result();
	}

	function count_member_suspension_requests($group_id = 0){
   		if($group_id){
   			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
   		}else{
   			
   		}
   		$this->db->where($this->dx('is_declined'). ' = "0"',NULL,FALSE);
   		$this->db->where($this->dx('is_approved'). ' = "0"',NULL,FALSE);
   		$this->db->where($this->dx('active'). ' = "1"',NULL,FALSE);
   		return $this->db->count_all_results('member_suspension_requests')?:0;
   	}

   	function get_member_suspension_requests($group_id = 0){
   		$this->select_all_secure('member_suspension_requests');
   		if($group_id){
   			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
   		}else{
   			
   		}
   		$this->db->where($this->dx('is_declined'). ' = "0"',NULL,FALSE);
   		$this->db->where($this->dx('is_approved'). ' = "0"',NULL,FALSE);
   		$this->db->where($this->dx('active'). ' = "1"',NULL,FALSE);
   		return $this->db->get('member_suspension_requests')->result();
   	}

   	function get_member_suspension_request($id=0,$group_id = 0){
   		$this->select_all_secure('member_suspension_requests');
   		if($group_id){
   			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
   		}else{
   			
   		}
   		$this->db->where($this->dx('active'). ' = "1"',NULL,FALSE);
   		$this->db->where('id',$id);
   		return $this->db->get('member_suspension_requests')->row();
   	}

   	function get_member_suspension_request_by_member_id($member_id=0,$group_id = 0){
   		$this->select_all_secure('member_suspension_requests');
   		if($group_id){
   			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
   		}else{
   			
   		}
   		$this->db->where($this->dx('active'). ' = "1"',NULL,FALSE);
   		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
   		return $this->db->get('member_suspension_requests')->row();
   	}

   	function get_member_suspension_request_id_by_member_id($member_id=0,$group_id = 0){
   		$this->db->select('id');
   		if($group_id){
   			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
   		}else{
   			
   		}
   		$this->db->where($this->dx('active'). ' = "1"',NULL,FALSE);
   		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
   		$this->db->where($this->dx('is_approved'). ' <= "0"',NULL,FALSE);
		$this->db->where($this->dx('is_declined'). ' <= "0"',NULL,FALSE);
   		$return  = $this->db->get('member_suspension_requests')->row();
   		if($return){
   			return $return->id;
   		}else{
   			return FALSE;
   		}
   	}


	function get_member_suspension_appoval_request($member_suspension_request_id=0,$member_id = 0,$group_id = 0){
   		$this->select_all_secure('member_suspension_approval_requests');
   		if($group_id){
   			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
   		}else{
   			
   		}
   		if($member_id){
   			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
   		}else{
   			$this->db->where($this->dx('member_id').' = "'.$this->member->id.'"',NULL,FALSE);
   		}
   		$this->db->where($this->dx('active'). ' = "1"',NULL,FALSE);
   		$this->db->where($this->dx('member_suspension_request_id').' = "'.$member_suspension_request_id.'"',NULL,FALSE);
   		return $this->db->get('member_suspension_approval_requests')->row();
   	}

   	function get_all_member_approval_suspension_requests($member_suspension_request_id=0,$group_id=0){
		$this->select_all_secure('member_suspension_approval_requests');
		if($group_id){
   			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
   		}else{
   			
   		}
		$this->db->where($this->dx('member_suspension_request_id').'= "'.$member_suspension_request_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'= "1"',NULL,FALSE);
		return $this->db->get('member_suspension_approval_requests')->result();
	}

	function count_all_member_approval_suspension_requests($member_suspension_request_id=0,$group_id = 0){
		if($group_id){
   			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
   		}else{
   			
   		}
		$this->db->where($this->dx('member_suspension_request_id').'= "'.$member_suspension_request_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'= "1"',NULL,FALSE);
		return $this->db->count_all_results('member_suspension_approval_requests');
	}


	function get_active_group_member_with_arears_options($group_id=0){
		$this->select_all_secure('members');
		$this->db->select(array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
				$this->dx('users.language_id').' as language_id',
			));
		
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		// $this->db->where($this->dx('members.arrears').' > "0"',NULL,FALSE);


		$this->db->join('users','users.id ='.$this->dx('members.user_id'),'INNER');
		if(isset($this->group->id)){
        	$this->db->order_by($this->dx($this->group->member_listing_order_by?:'first_name'), $this->group->order_members_by?:'ASC', FALSE);
        }else{
        	$this->db->order_by($this->dx('first_name'),'ASC',FALSE);
        }
		$members = $this->db->get('members')->result();

		$arr = array();

		if($members)
		{
			foreach ($members as $key => $member) 
			{
				$arr[$member->id] = $member->first_name.' '.$member->last_name;
			}
		}
		
		return $arr;
	}

}