<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Group_members{

	protected $ci;

	public $application_settings;
	public $success_feedback = array();
	public $warning_feedback = array();
	
	public $organization_roles = array(
        '1' => 'Payroll Accountant',
        '2' => 'Sacco Officer ',
        '3' => 'Credit committee',
        '4' => 'Sacco Manager'
    );
    

	public function __construct(){
		$this->ci= & get_instance();
		$this->ci->load->library('ion_auth');
		$this->ci->load->library('messaging');
		$this->ci->load->model('members/members_m');
		$this->ci->load->model('group_account_managers/group_account_managers_m');
		$this->ci->load->model('groups/groups_m');
		$this->application_settings = $this->ci->settings_m->get_settings()?:'';
	}

	public function create(
		$group_id = 1,
		$user = array(),
		$created_by = 0,
		$is_admin = FALSE,
		$group_role_id=0,
		$calling_code = "",
		$original_phone = "",
		$group_admin = FALSE,
		$membership_number = "",
		$date_of_birth = "",
		$physical_address = "",
        $next_of_kin_full_name = "",
        $next_of_kin_id_number = "",
        $next_of_kin_phone = "",
        $next_of_kin_relationship = ""
	){
		$group_id=10;
		 
		if($group_id&&$user&&$created_by){
			if($this->ci->members_m->check_if_user_is_in_group($group_id,$user->id) == FALSE){
				$data = array(
					'membership_number' => $membership_number,
					'date_of_birth' => $date_of_birth,
					'physical_address' => $physical_address,
					'group_id' => $group_id,
					'user_id' => $user->id,
					'is_admin' => $is_admin?1:($group_admin?1:0),
					'group_role_id' => $group_role_id,
					'created_by' => $created_by,
					'active' => 1,
					'created_on' => time(),
				);
				if($member_id = $this->ci->members_m->insert($data)){
					if($next_of_kin_full_name){
						$input = array(
							'full_name'=>$next_of_kin_full_name,
		                    'id_number'=>$next_of_kin_id_number,
		                    'phone'=>valid_phone($next_of_kin_phone),
		                    'email'=>'',
		                    'relationship'=> $next_of_kin_relationship,
		                    'allocation'=>100,
		                    'member_id'=>$member_id,
		                    'group_id'=>$group_id,
		                    'created_by'=>$created_by,
		                    'created_on'=>time(),
						);
						if($next_of_kin_id = $this->ci->members_m->insert_next_of_kin($input)){

						}else{
							$this->ci->session->set_flashdata('error','Could not create member next of kin');
						}
					}
					$this->success_feedback[] =  $user->first_name.' '.$user->last_name.' - '.$user->phone.' was successfully added to the group.';
					return $member_id;
				}else{
					$this->ci->session->set_flashdata('error','Could not create member');
					return FALSE;
				}
			}else{
				$update = array(
					'membership_number' => $membership_number,
					'date_of_birth' => $date_of_birth,
					'physical_address' => $physical_address,
					'is_admin' => $is_admin?1:($group_admin?1:0),
					'group_role_id' => $group_role_id,
					'modified_by' => $created_by,
					'modified_on' => time(),
				);
				//if($group_admin){
					if($member = $this->ci->members_m->get_group_member_by_user_id($group_id,$user->id)){
						if($this->ci->members_m->update($member->id,$update)){
							if($next_of_kin_full_name){

								$this->ci->members_m->delete_group_member_next_of_kin($group_id,$member->id);

								$input = array(
									'full_name'=>$next_of_kin_full_name,
				                    'id_number'=>$next_of_kin_id_number,
				                    'phone'=>valid_phone($next_of_kin_phone),
				                    'email'=>'',
				                    'relationship'=> $next_of_kin_relationship,
				                    'allocation'=>100,
				                    'member_id'=>$member->id,
				                    'group_id'=>$group_id,
				                    'created_by'=>$created_by,
				                    'created_on'=>time(),
								);
								if($next_of_kin_id = $this->ci->members_m->insert_next_of_kin($input)){

								}else{
									$this->ci->session->set_flashdata('error','Could not create member next of kin');
								}
							}
							return $member->id;
						}else{
							$this->warning_feedback[] =  $user->first_name.' '.$user->last_name.' - '.$user->phone.' already exists within the group and could not update role';
							return FALSE;
						}
					}else{
						$this->warning_feedback[] =  $user->first_name.' '.$user->last_name.' - '.$user->phone.' already exists within the group.';
						return FALSE;
					}
				// }else{
				// 	$this->warning_feedback[] =  $user->first_name.' '.$user->last_name.' - '.$user->phone.' already exists within the group.';
				// 	return FALSE;
				// }
			}
		}else{
			$this->ci->session->set_flashdata('error','Member properties missing');
			$this->warning_feedback[] = 'Member properties missing';
			return FALSE;
		}
	}

	public function create_group_account_manager($group_id = 0,$user = array(),$created_by = 0){
		if($group_id&&$user&&$created_by){
			if($this->ci->group_account_managers_m->check_if_user_is_in_group($group_id,$user->id)==FALSE){
				$user_group_id = $this->ci->ion_auth->get_group_by_name('group-account-manager');
                if($user_group_id){
                    $groups = array($user_group_id->id);
                }else{
                    $groups = array(3);
                }
                if($this->ci->ion_auth->is_in_group(
                	$user->id,
                	isset($user_group_id->id)?$user_group_id->id:'')){

                }else{
	                if($this->ci->ion_auth->add_to_group($groups,$user->id)){

	                }else{
						$this->ci->session->set_flashdata('error','Could not create group account manager');
						return FALSE;
					}
				}

				$data = array(
					'group_id' => $group_id,
					'user_id' => $user->id,
					'created_by' => $created_by,
					'active' => 1,
					'created_on' => time(),
				);
				if($member_id = $this->ci->group_account_managers_m->insert($data)){
					$this->success_feedback[] =  $user->first_name.' '.$user->last_name.' - '.$user->phone.' was successfully added to the group.';
					return $member_id;
				}else{
					$this->ci->session->set_flashdata('error','Could not create group account manager');
					return FALSE;
				}
				
			}else{
				$this->warning_feedback[] =  $user->first_name.' '.$user->last_name.' - '.$user->phone.' already exists within the group.';
				return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Group account manager properties missing');
			return FALSE;
		}
	}

	public function add_member_to_group(
		$group = array(),
		$first_name = '',
		$last_name='',
		$phone='',
		$email='',
		$send_invitation_sms=FALSE,
		$send_invitation_email=FALSE,
		$current_user,
		$current_member_id = 0,
		$group_role_id = 0,
		$middle_name = "",
		$calling_code = "",
		$original_phone = "",
		$group_admin = FALSE,
        $id_number = "",
		$loan_limit="",
        $membership_number = "",
        $date_of_birth = "",
        $physical_address = "",
        $next_of_kin_full_name = "",
        $next_of_kin_id_number = "",
        $next_of_kin_phone = "",
        $next_of_kin_relationship = ""
	){
		if($group){
			 
			 
			//check if user is already registered or not
			if($this->ci->ion_auth->identity_check($phone)){
				//phone number user is found
				$phone = valid_phone($phone);
				$user = $this->ci->ion_auth->get_user_by_phone($phone);
				if($user->id_number){
					//$id_number = $user->id_number;
				}
				$input = array(
                    'first_name' => $user->first_name?$user->first_name:ucfirst($first_name), 
                    'last_name' => $user->last_name?$user->last_name:ucfirst($last_name), 
                    'middle_name' => $user->middle_name?$user->middle_name:ucfirst($middle_name),
					'id_number' => $user->id_number?$user->id_number:$id_number,
					'email' => $user->email?$user->email:$email,
					'loan_limit' => currency($loan_limit),
					'modified_on' => time(),
				);	
						
				if($this->ci->ion_auth->update($user->id,$input)){
					if($member_id = $this->create($group->id,$user,$current_user->id,'',$group_role_id,'','',$group_admin,$membership_number,$date_of_birth,$physical_address,$next_of_kin_full_name,$next_of_kin_id_number,$next_of_kin_phone,$next_of_kin_relationship)){
						//send the user an invitation if notification requests set and add notifications
						$member = $this->ci->members_m->get_group_member($member_id,$group->id);
						if($user->id == $current_user->id){
							return $member_id;
						}
						if($this->ci->messaging->send_group_invitation_to_user($group,$user,$member,$current_user,$current_member_id,$send_invitation_sms,$send_invitation_email)){
							$this->ci->session->set_userdata('success_feedback',$this->success_feedback);
							$this->ci->session->set_userdata('warning_feedback',$this->warning_feedback);
							return $member_id;
						}else{
							$this->ci->session->set_flashdata('error','Something went wrong when sending the group invitation ');
							$this->ci->session->set_userdata('success_feedback',$this->success_feedback);
							$this->ci->session->set_userdata('warning_feedback',$this->warning_feedback);
							return FALSE;
						}
					}else{
						//add tally of lack of registry
						$this->ci->session->set_userdata('success_feedback',$this->success_feedback);
						$this->ci->session->set_userdata('warning_feedback',$this->warning_feedback);
						return FALSE;
					}
				}else{
					return FALSE;
				}
			}else if($this->ci->ion_auth->identity_check($email)&&valid_email($email)){
				//email address user is found
				$user = $this->ci->ion_auth->get_user_by_email($email);
				if($user->id_number){
					//$id_number = $user->id_number;
				}
				$input = array(
                    'first_name' => $user->first_name?$user->first_name:ucfirst($first_name), 
                    'last_name' => $user->last_name?$user->last_name:ucfirst($last_name), 
                    'middle_name' => $user->middle_name?$user->middle_name:ucfirst($middle_name),
					'id_number' => $user->id_number?$user->id_number:$id_number,
					'loan_limit' =>  currency($loan_limit),
					'email' => $user->email?$user->email:$email,
					'modified_on' => time(),
				);
				 
				if($this->ci->ion_auth->update($user->id,$input)){	
					if($member_id = $this->create($group->id,$user,$current_user->id,FALSE,$group_role_id,'','',$group_admin,$membership_number,$date_of_birth,$physical_address,$next_of_kin_full_name,$next_of_kin_id_number,$next_of_kin_phone,$next_of_kin_relationship)){
						//send the user an invitation if notification requests set and add notifications
						if($user->id == $current_user->id){
							return $member_id;
						}
						$member = $this->ci->members_m->get_group_member($member_id,$group->id);
						if($this->ci->messaging->send_group_invitation_to_user($group,$user,$member,$current_user,$current_member_id,$send_invitation_sms,$send_invitation_email)){
							$this->ci->session->set_userdata('success_feedback',$this->success_feedback);
							$this->ci->session->set_userdata('warning_feedback',$this->warning_feedback);
							return $member_id;
						}else{
							$this->ci->session->set_flashdata('error','Something went wrong when sending the group invitation ');
							$this->ci->session->set_userdata('success_feedback',$this->success_feedback);
							$this->ci->session->set_userdata('warning_feedback',$this->warning_feedback);
							return FALSE;
						}
					}else{
						//add tally of lack of registry
						$this->ci->session->set_userdata('success_feedback',$this->success_feedback);
						$this->ci->session->set_userdata('warning_feedback',$this->warning_feedback);
						return FALSE;
					}
				}else{
					return FALSE;
				}
			}else{
				//user not found now we go HAM
                $user_group_id = $this->ci->ion_auth->get_group_by_name('member');
                if($user_group_id){
                    $groups = array($user_group_id->id);
                }else{
                    $groups = array(2);
                }
                $password = rand(1000,9999);
                $additional_data = array(
                    'created_on'=>time(),
                    'active'=>1, 
                    'ussd_pin'=>rand(1000,9999),
                    'first_name'=>ucfirst($first_name), 
                    'last_name'=>ucfirst($last_name), 
                    'middle_name'=>ucfirst($middle_name),
                    'is_validated'=> 1,
                    'calling_code' => $calling_code,
                    'original_phone' => $original_phone,
                    'date_of_birth' => $date_of_birth,
					'id_number' => $id_number,
					'loan_limit' => $loan_limit?currency($loan_limit):$user->loan_limit,
					'prompt_to_change_password' => 1,
					'password_check'=>$this->ci->ion_auth->hash_password($password,'','',1),
                );                
                $user_id = $this->ci->ion_auth->register($phone,$password,$email,$additional_data,$groups);
                if($user_id){
                	$user = $this->ci->ion_auth->get_user($user_id);
					if($member_id = $this->create($group->id,$user,$current_user->id,FALSE,$group_role_id,'','','',$membership_number,$date_of_birth,$physical_address,$next_of_kin_full_name,$next_of_kin_id_number,$next_of_kin_phone,$next_of_kin_relationship)){
						//send the user an invitation if notification requests set and add notifications
						if($user->id == $current_user->id){
							return $member_id;
						}
						$member = $this->ci->members_m->get_group_member($member_id,$group->id);
						if($this->ci->messaging->send_group_invitation_to_user($group,$user,$member,$current_user,$current_member_id,$send_invitation_sms,$send_invitation_email)){
							$this->ci->session->set_userdata('success_feedback',$this->success_feedback);
							$this->ci->session->set_userdata('warning_feedback',$this->warning_feedback);
							return $member_id;
						}else{
							$this->ci->session->set_flashdata('error','Something went wrong when sending the group invitation ');
							$this->ci->session->set_userdata('success_feedback',$this->success_feedback);
							$this->ci->session->set_userdata('warning_feedback',$this->warning_feedback);
							return FALSE;
						}
					}else{
						//add tally of lack of registry
						$this->ci->session->set_flashdata('error','Could not create member profile');
						$this->ci->session->set_userdata('success_feedback',$this->success_feedback);
						$this->ci->session->set_userdata('warning_feedback',$this->warning_feedback);
						return FALSE;
					}
                }else{
                	$this->ci->session->set_flashdata('error','Could not create user profile');
					$this->ci->session->set_userdata('warning_feedback','Error creating user');
					return FALSE;
                }
			}
		}else{
			$this->ci->session->set_flashdata('error','The group id is not set');
			return FALSE;
		}
	}

	public function add_group_account_manager_to_group($group = array(),$first_name = '',$last_name='',$phone='',$email='',$send_invitation_sms=FALSE,$send_invitation_email=FALSE,$current_user,$current_member_id=0){
		if($group){
			//check if user is already registered or not
			if($this->ci->ion_auth->identity_check($phone)){
				//phone number user is found
				$phone = valid_phone($phone);
				$user = $this->ci->ion_auth->get_user_by_phone($phone);
				if($group_account_manager_id = $this->create_group_account_manager($group->id,$user,$current_user->id)){
					//send the user an invitation if notification requests set and add notifications
					if($this->ci->messaging->send_group_invitation_to_user($group,$user,$group_account_manager_id,$current_user,$current_member_id,$send_invitation_sms,$send_invitation_email)){
						$this->ci->session->set_userdata('success_feedback',$this->success_feedback);
						$this->ci->session->set_userdata('warning_feedback',$this->warning_feedback);
						return $group_account_manager_id;
					}else{
						$this->ci->session->set_flashdata('error','Something went wrong when sending the group invitation ');
						$this->ci->session->set_userdata('success_feedback',$this->success_feedback);
						$this->ci->session->set_userdata('warning_feedback',$this->warning_feedback);
						return FALSE;
					}
				}else{
					//add tally of lack of registry
					$this->ci->session->set_userdata('success_feedback',$this->success_feedback);
					$this->ci->session->set_userdata('warning_feedback',$this->warning_feedback);
					return FALSE;
				}
			}else if($this->ci->ion_auth->identity_check($email)&&valid_email($email)){
				//email address user is found
				$user = $this->ci->ion_auth->get_user_by_email($email);
				if($group_account_manager_id = $this->create_group_account_manager($group->id,$user,$current_user->id)){
					//send the user an invitation if notification requests set and add notifications
					if($this->ci->messaging->send_group_invitation_to_user($group,$user,$group_account_manager_id,$current_user,$current_member_id,$send_invitation_sms,$send_invitation_email)){
						$this->ci->session->set_userdata('success_feedback',$this->success_feedback);
						$this->ci->session->set_userdata('warning_feedback',$this->warning_feedback);
						return $group_account_manager_id;
					}else{
						$this->ci->session->set_flashdata('error','Something went wrong when sending the group invitation ');
						$this->ci->session->set_userdata('success_feedback',$this->success_feedback);
						$this->ci->session->set_userdata('warning_feedback',$this->warning_feedback);
						return FALSE;
					}
				}else{
					//add tally of lack of registry
					$this->ci->session->set_userdata('success_feedback',$this->success_feedback);
					$this->ci->session->set_userdata('warning_feedback',$this->warning_feedback);
					return FALSE;
				}
			}else{
				//user not found now we go HAM
                $user_group_id = $this->ci->ion_auth->get_group_by_name('group-account-manager');
                if($user_group_id){
                    $groups = array($user_group_id->id);
                }else{
                    $groups = array(3);
                }
                $additional_data = array(
                    'created_on'=>time(),
                    'active'=>1, 
                    'ussd_pin'=>rand(1000,9999),
                    'first_name'=>ucfirst($first_name), 
                    'last_name'=>ucfirst($last_name), 
                );
                $password = rand(1000,9999);
                $user_id = $this->ci->ion_auth->register($phone,$password,$email,$additional_data,$groups);
                $user = $this->ci->ion_auth->get_user($user_id);
				if($group_account_manager_id = $this->create_group_account_manager($group->id,$user,$current_user->id)){
					//send the user an invitation if notification requests set and add notifications
					if($this->ci->messaging->send_group_invitation_to_user($group,$user,$group_account_manager_id,$current_user,$current_member_id,$send_invitation_sms,$send_invitation_email)){
						$this->ci->session->set_userdata('success_feedback',$this->success_feedback);
						$this->ci->session->set_userdata('warning_feedback',$this->warning_feedback);
						return $group_account_manager_id;
					}else{
						$this->ci->session->set_flashdata('error','Something went wrong when sending the group invitation ');
						$this->ci->session->set_userdata('success_feedback',$this->success_feedback);
						$this->ci->session->set_userdata('warning_feedback',$this->warning_feedback);
						return FALSE;
					}
				}else{
					//add tally of lack of registry
					$this->ci->session->set_flashdata('error','Could not create group account manager profile');
					$this->ci->session->set_userdata('success_feedback',$this->success_feedback);
					$this->ci->session->set_userdata('warning_feedback',$this->warning_feedback);
					return FALSE;
				}
			}
		}else{
			$this->ci->session->set_flashdata('error','The group id is not set');
			return FALSE;
		}
	}

	function set_active_group_size($group_id='',$alert=FALSE){
		if($group_id){
			$group = $this->ci->groups_m->get($group_id);
			$previous_active_size = $group->active_size;
			$set_group_size_by_group_admin = $group->size;

			$current_active_size = $this->ci->members_m->count_all_active_group_members($group_id);
			if($current_active_size)
			{
				if($current_active_size == $previous_active_size && $set_group_size_by_group_admin>$current_active_size)
				{
					//do nothing as the size does not change.
				}
				else
				{
					if($current_active_size >$set_group_size_by_group_admin)
					{
						$set_group_size_by_group_admin = $current_active_size;
					}

					$update = $this->ci->groups_m->update($group_id,array(
							'size'=>$set_group_size_by_group_admin,
							'active_size'=>$current_active_size
							));
					if($current_active_size>$previous_active_size && $update)
					{
						if($alert){
							$this->ci->session->set_flashdata('info','Active Group Members size has increased to '.$current_active_size);
						}
					}
					else if($current_active_size<$previous_active_size && $update)
					{
						if($alert){
							$this->ci->session->set_flashdata('info','Active Group Members size has been reduced to '.$current_active_size);
						}
					}
					else if($current_active_size==$previous_active_size)
					{
						
					}
					else
					{
						if($alert){
							$this->ci->session->set_flashdata('error','Error updating group size');
						}
					}
				}
			}
			else
			{
				if($alert){
					$this->ci->session->set_flashdata('error','Group active size not set. Pass an id');
				}
			}
		}
		else
		{
			if($alert){
				$this->ci->session->set_flashdata('error','Group active size not set. Pass an id');
			}
		}
	}

	/* member suspension */

	function initiate_member_suspension($group=array(),$user=array(),$member=array(),$suspend_user = array(),$suspend_member = array(),$suspesion_reason='',$group_role_options = array()){
		if($group&&$user&&$member&&$suspesion_reason&&$suspend_user&&$suspend_member){
			$suspension_request_input = array(
				'request_date' => time(),
				'member_id' => $suspend_member->id,
				'user_id' => $suspend_user->id,
				'suspension_reason' => $suspesion_reason,
				'group_id' => $group->id,
				'active' => 1,
				'is_declined' => 0,
				'is_approved' => 0,
				'created_by' => $user->id,
				'created_on' => time(),
			);
			if($suspension_request_id = $this->ci->members_m->insert_member_suspension_request($suspension_request_input)){
				$notify_members = array();
				$role = '';
				if(array_key_exists($suspend_member->group_role_id, $group_role_options)){//all members - suspend official
					$role = $group_role_options[$suspend_member->group_role_id];
					$group_members = $this->ci->members_m->get_active_group_members($group->id);
					foreach ($group_members as $group_member) {
						$approval_input = array();
						$send_notification = TRUE; 
						if($group_member->id == $suspend_member->id){
							//ignore this
						}elseif($group_member->id == $member->id){
							$send_notification = FALSE;
							$approval_input = array(
								'member_suspension_request_id' => $suspension_request_id,
								'active' => 1,
								'group_id' => $group->id,
								'member_id' => $group_member->id,
								'is_approved' => 1,
								'is_declined' => 0,
								'approved_on' => time(),
								'comment' => $suspesion_reason,
								'created_by' => $user->id,
								'created_on' => time(),
							);
						}else{
							$approval_input = array(
								'member_suspension_request_id' => $suspension_request_id,
								'active' => 1,
								'group_id' => $group->id,
								'member_id' => $group_member->id,
								'is_approved' => 0,
								'is_declined' => 0,
								'approved_on' => time(),
								'created_by' => $user->id,
								'created_on' => time(),
							);
						}
						if($approval_input){
							if($this->ci->members_m->insert_member_suspension_approval_request($approval_input)){
								if($send_notification){
									$notify_members[] = $group_member;
								}
							}
						}
					}
				}else{
					$group_officials = $this->ci->members_m->get_active_group_officials($group->id);
					foreach ($group_officials as $group_official) {
						$approval_input = array();
						$send_notification = TRUE; 
						if($group_official->id == $member->id){
							$approval_input = array(
								'member_suspension_request_id' => $suspension_request_id,
								'active' => 1,
								'group_id' => $group->id,
								'member_id' => $group_official->id,
								'is_approved' => 1,
								'is_declined' => 0,
								'approved_on' => time(),
								'comment' => $suspesion_reason,
								'created_by' => $user->id,
								'created_on' => time(),
							);
							$send_notification = FALSE; 
						}else{
							$approval_input = array(
								'member_suspension_request_id' => $suspension_request_id,
								'active' => 1,
								'group_id' => $group->id,
								'member_id' => $group_official->id,
								'is_approved' => 0,
								'is_declined' => 0,
								'approved_on' => time(),
								'comment' => $suspesion_reason,
								'created_by' => $user->id,
								'created_on' => time(),
							);
						}
						if($approval_input){
							if($this->ci->members_m->insert_member_suspension_approval_request($approval_input)){
								if($send_notification){
									$notify_members[] = $group_official;
								}
							}
						}
					}
				}
				if($notify_members){
					if($this->ci->messaging->notify_members_member_suspension_request($group,$user,$member,$suspesion_reason,$role,$notify_members,$suspend_member,$suspension_request_id)){
						return $suspension_request_id;
					}else{
						return $suspension_request_id;
					}
				}

			}else{
				$this->ci->session->set_flashdata('error','Could not insert member suspension request');
			}
		}else{
			$this->ci->session->set_flashdata('error','Missing parameters');
			return FALSE;
		}
	}

	function approve_member_suspension($group=array(),$user=array(),$member=array(),$member_approval_request=array()){
		if($group&&$user&&$member&&$member_approval_request){
			$update = array(
				'is_approved' => 1,
				'approved_on' => time(),
				'modified_on' => time(),
				'modified_by' => $user->id,
			);
			if($this->ci->members_m->update_member_suspension_approval_request($member_approval_request->id,$update)){
				if($this->update_suspension_request($member_approval_request->member_suspension_request_id,$group)){
					return TRUE;
				}else{
					return FALSE;
				}
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	function decline_member_suspension($group = array(),$user = array(),$member = array(),$member_approval_request=array(),$reason=''){
		if($group&&$user&&$member&&$member_approval_request&&$reason){
			$update = array(
				'is_declined' => 1,
				'declined_on' => time(),
				'comment' => $reason,
				'modified_on' => time(),
				'modified_by' => $user->id,
			);
			if($this->ci->members_m->update_member_suspension_approval_request($member_approval_request->id,$update)){
				if($this->update_suspension_request($member_approval_request->member_suspension_request_id,$group)){
					return TRUE;
				}else{
					return FALSE;
				}
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	function update_suspension_request($request_id = 0,$group=array()){
		if($request_id){
			$all_suspension_requests = $this->ci->members_m->get_all_member_approval_suspension_requests($request_id,$group->id);
			if($all_suspension_requests){
				$approved = 0;
				$declined = 0;
				foreach ($all_suspension_requests as $suspension_request) {
					if($suspension_request->is_approved){
						++$approved;
					}elseif ($suspension_request->is_declined) {
						++$declined;
					}
				}
				$total_request = count($all_suspension_requests);
				$percentage_approved = ($approved/$total_request)*100;
				$percentage_declined = ($declined/$total_request)*100;
				if(round($percentage_approved)>=66){
					$update = array(
						'is_approved' => 1,
						'approved_on' => time(),
						'modified_on' => time(),
					);
					if($this->ci->members_m->update_suspension_request($request_id,$update)){
						$member_suspension_request = $this->ci->members_m->get_member_suspension_request($request_id,$group->id);
						if($member_suspension_request){
							$member_update = array(
								'active' => 0,
								'modified_on' => time(),
							);
							if($this->ci->members_m->update($member_suspension_request->member_id,$member_update)){
								$this->ci->messaging->notify_initiator_suspension_status($group,$member_suspension_request,1);
								return TRUE;
							}else{
								return FALSE;
							}
						}else{
							return FALSE;
						}
					}else{
						return FALSE;
					}
				}elseif (round($percentage_declined)>=66) {
					$update = array(
						'is_declined' => 1,
						'declined_on' => time(),
						'modified_on' => time(),
					);
					if($this->ci->members_m->update_suspension_request($request_id,$update)){
						$member_suspension_request = $this->ci->members_m->get_member_suspension_request($request_id,$group->id);
						if($member_suspension_request){
							$member_update = array(
								'active' => 1,
								'modified_on' => time(),
								'suspension_initiated' => '',
								'suspension_initiated_by' => '',
								'suspension_initiated_on'=> '',
								'suspension_reason'=> '',
							);
							if($this->ci->members_m->update($member_suspension_request->member_id,$member_update)){
								$this->ci->messaging->notify_initiator_suspension_status($group,$member_suspension_request,2);
								return TRUE;
							}else{
								return FALSE;
							}
						}else{
							return FALSE;
						}
					}else{
						return FALSE;
					}
				}elseif (round($percentage_declined+$percentage_approved)>=100){
					$update = array(
						'is_declined' => 1,
						'declined_on' => time(),
						'modified_on' => time(),
					);
					if($this->ci->members_m->update_suspension_request($request_id,$update)){
						$member_suspension_request = $this->ci->members_m->get_member_suspension_request($request_id,$group->id);
						if($member_suspension_request){
							$member_update = array(
								'active' => 1,
								'modified_on' => time(),
								'suspension_initiated' => '',
								'suspension_initiated_by' => '',
								'suspension_initiated_on'=> '',
								'suspension_reason'=> '',
							);
							if($this->ci->members_m->update($member_suspension_request->member_id,$member_update)){
								$this->ci->messaging->notify_initiator_suspension_status($group,$member_suspension_request,2);
								return TRUE;
							}else{
								return FALSE;
							}
						}else{
							return FALSE;
						}
					}else{
						return FALSE;
					}
				}else{
					return TRUE;
				}
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}
	

}