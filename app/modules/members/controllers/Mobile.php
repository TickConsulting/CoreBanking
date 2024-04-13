<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mobile extends Mobile_Controller{

	function __construct(){
        parent::__construct();
        $this->load->model('group_roles/group_roles_m');
        $this->load->model('loan_invoices/loan_invoices_m');
    		$this->load->library('image_lib');
    	$this->load->library('group_members');

    }

    public function _remap($method, $params = array()){
       if(method_exists($this, $method)){
           return call_user_func_array(array($this, $method), $params);
       }
       $this->output->set_status_header('404');
       header('Content-Type: application/json');
       $file = file_get_contents('php://input')?(array)json_decode(file_get_contents('php://input')):array();
       $request = $_REQUEST+$file;
       echo encrypt_json_encode(
       	array(
       		'response' => array(
		       		'status'	=>	404,
		       		'message'		=>	'404 Method Not Found for URI: '.$this->uri->uri_string(),
       			),
       	));
	}

    function edit_profile_photo(){
    	if($_FILES){
    		$user_id = $this->input->get_post('user_id');
    		$group_id = $this->input->get_post('group_id');
    		$member_id = $this->input->get_post('member_id');
    		$request = array(
    			'file' => $_FILES,
    			'user_id' => $user_id,
    			'group_id' => $group_id,
    			'member_id' => $member_id,
    		);
    		$this->user = $this->ion_auth->get_user($user_id);
			if($this->user){
				if($this->group = $this->groups_m->get($group_id)){
	                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
	                	$member_to_edit = is_numeric($member_id)?$this->members_m->get_group_member($member_id,$this->group->id):$this->member;
	                	if($member_to_edit){
	                		$groups_directory = './uploads/groups';
				            if(!is_dir($groups_directory)){
				                mkdir($groups_directory,0777,TRUE);
				            }
				            $avatar['file_name'] = ''; 
				            if($_FILES['avatar']['name']){
				            	$avatar = $this->files_uploader->upload('avatar',$groups_directory);
				                if($avatar){
				                    if(is_file(FCPATH.$groups_directory.'/'.$member_to_edit->avatar)){
				                        if(unlink(FCPATH.$groups_directory.'/'.$member_to_edit->avatar)){
				                            
				                        }
				                    }
					                if($avatar['file_name']){
						            	$update = array(
							                'avatar' => $avatar['file_name'],
							                'modified_on' => time(),
							                'modified_by' => $this->user->id,
							            );
					    				if($this->ion_auth->update($member_to_edit->user_id,$update)){
							                $response = array(
								    			'status'	=>	1,
												'message' => 'successful',
												'avatar' => $avatar['file_name'],
								    		);
								    		$subject = 'User profile update';
							                $message = $this->user->first_name.' '.$this->user->last_name.' updated your profile picture.';
							                $call_to_action = 'View profile';
							                $call_to_action_link = "/group/members/view/".$this->user->id;
								    		$this->member_notifications->create($subject,$message,$this->user,$this->member->id,$member_to_edit->user_id,$member_to_edit->id,$this->group->id,$call_to_action,$call_to_action_link,1);
							            }else{
							                $response = array(
								    			'status'	=>	0,
												'message'		=>	'Error updating member avatar',
								    		);
							            }
						            }else{
						            	$response = array(
							    			'status'	=>	0,
											'message'		=>	'Error uploading avatar',
							    		);
						            }
				                 }else{
				                 	$response = array(
				                 		'status' => 0,
				                 		'message' => $this->session->flashdata('error'),
				                 	);
				                 }
				            }else{
				            	$response = array(
			                 		'status' => 0,
			                 		'message' => 'Invalid file format name',
			                 	);
				            }
	                	}else{
	                		$response = array(
	                			'status' => 0,
	                			'message' => 'Could not find user to update profile picture',
	                		);
	                	}
					}else{
	                    $response = array(
	                        'status' => 6,
	                        'message' => 'Could not find member details',
	                    );
	                }
	            }else{
	                $response = array(
	                    'status' => 5,
	                    'message' => 'Could not find group details',
	                );
	            }
			}else{
				$response = array(
	    			'status'	=>	4,
					'time'	=>	time(),
	    		);
			}
    	}else{
    		$response = array(
    			'status'	=>	2,
				'error'		=>	'No files sent',
    		);
    	}
    	echo encrypt_json_encode(array('response'=>$response));
    }

    function get_members_and_details(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
        	$this->ion_auth->update_last_login($this->user->id);
        	$group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
           			$lower_limit = $this->input->post('lower_limit')?:0;
           			$upper_limit = $this->input->post('upper_limit')?:100;
           			$show_full_details = $this->input->post('show_full_details')?:FALSE;
           			$view_profile_member_id = $this->input->post('view_profile_member_id')?:FALSE;
                	$records_per_page = $upper_limit - $lower_limit;
                	$view_member_profile = 1;
                	if($this->member->group_role_id || $this->member->is_admin){

                	}else{
                		if($this->group->enable_member_information_privacy){
                			if($view_profile_member_id){
                				if($view_profile_member_id == $this->member->id){
	            				}else{
	            					$response = array(
	            						'status' => 0,
	            						'message' => 'Settings > Enable member informaton privacy is enabled within the group and thus you can not view other member profiles',
	            					);
	            				}
                			}
                			$view_profile_member_id = $this->member->id;
                			$view_member_profile = 0;
	                	}
                	}
                	if(isset($response)){
                	}else{
				        $filter_params = array("member_id"=>array($view_profile_member_id));
	                	$total_rows = $this->members_m->count_group_members($this->group->id,$filter_params);
				        $pagination = create_pagination('group/members/listing/pages', $total_rows,$records_per_page,$upper_limit
				        	,TRUE);
				        $group_role_options = $this->group_roles_m->get_group_role_options($this->group->id);
				        $posts = $this->members_m->limit($pagination['limit'])->get_group_members($this->group->id,$filter_params);
				        $member_details = array();
				        $suspended_members = array();
				        $group_details = array();
				        $additional_data = array();
				        foreach ($posts as $post) {
				        	if($post->user_id == $this->user->id){
				        		$is_editable = 0;
				        	}else{
				        		if($post->last_login){
				        			$is_editable = 0;
				        		}else{
				        			$is_editable = 1;
				        		}
				        	}
				        	$user_data = array(
				        		"name" => $post->first_name.' '.$post->last_name,
				        		"first_name" => $post->first_name,
				        		"last_name" => $post->last_name,
				        		"role" => (isset($group_role_options[$post->group_role_id])?$group_role_options[$post->group_role_id]:(
				        			$post->is_admin?'Group Admin':'Member')),
				        		"has_role" => isset($group_role_options[$post->group_role_id])?1:0,
				        		'is_editable' => $is_editable,
				        		'view_member_profile' => $view_member_profile,
				        		"phone" => valid_phone($post->phone)?:0,
				        		"email" => $post->email,
				        		"last_seen" => $post->last_login?(((time()-$post->last_login)<(60*2))?'Online':'Last seen '.(timestamp_to_time_elapsed($post->last_login).'')):'Never',
				        		"avatar" => $post->avatar,
				        		"member_id" => $post->id,
				        		"user_id" => $post->user_id,
				        		'selected' => ($post->user_id == $this->user->id)?TRUE:(($post->id ==$view_profile_member_id )?TRUE:FALSE),
				        		'filter_params' => $filter_params,
				        	);
				        	if($show_full_details){
				        		$member_data = $this->_get_member_group_dashboard_data($this->group->id,$post->id,$this->group->disable_arrears);
				        		$additional_data = array(
				        			'total_contributions' => $member_data['total_member_contributions'],
	                        		'total_fines' => $member_data['total_member_fines'],
	                        		'total_loan_balances' => $member_data['total_member_loan_balances'],
	                        		'contribution_arrears' => $member_data['total_member_contribution_arrears'],
	                        		'total_fine_arrears' => $member_data['total_member_fine_arrears'],
	                        		'lump_sum_balance' => $member_data['total_loan_lump_sum_balance'],
				        		);
				        	}
				        	if($post->active){
				        		$member_details[] = array_merge($user_data,$additional_data);
				        	}else{
				        		$suspended_members[] = array_merge($user_data,$additional_data);
				        	}
				        	
				        }
				        if($show_full_details){
				        	$group_data = $this->_get_group_dashboard_data($this->group->id);
				        	$group_details = array(
				        		'total_contributions' => $group_data['total_group_contributions'],
	                    		'total_fines' => $group_data['total_group_fines'],
	                    		'total_loan_balances' => $group_data['total_group_loan_balances'],
	                    		'total_expense_payments' => $group_data['total_group_expenses'],
				        	);
				        }
				        $response = array(
				        	'status' => 1,
				        	'time' => time(),
				        	'message' => 'Member Details',
				        	'members' => $member_details,
				        	'suspended_members' => $suspended_members,
				        	'group_details' => $group_details,
				        );
				    }
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
	}


	function suspend_member(){
		foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
        	$this->ion_auth->update_last_login($this->user->id);
        	$group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                	$member_id = $this->input->post('member_id');
                	if($member_id){
                		$member = $this->members_m->get_group_member($member_id,$this->group->id);
                		if($member){
                			if($member->id == $this->member->id){
                				$response = array(
	                                'status' => 0,
	                                'message' => 'You can not suspend yourself',
	                                'time' => time(),
	                            );
                			}else{
                				$update = array(
					                'active' => 0,
					                'modified_on' => time(),
					                'modified_by' => $this->user->id
					            );
					            if($this->members_m->update($member->id,$update)){
					            	$response = array(
		                                'status' => 1,
		                                'message' => $member->first_name.' '.$member->last_name.' suspended successfully.', 
		                                'time' => time(),
		                            );
					            }else{
					            	$response = array(
		                                'status' => 0,
		                                'message' => $member->first_name.' '.$member->last_name.' could not be suspended.', 
		                                'time' => time(),
		                            );
					            }
                			}
                		}else{
                			$response = array(
                                'status' => 0,
                                'message' => 'Member selected to suspend not found',
                                'time' => time(),
                            );
                		}
                	}else{
                		$response = array(
                            'status' => 0,
                            'message' => 'Kindly select member to suspend',
                            'time' => time(),
                        );
                	}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
	}

	function activate_member(){
    	foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
        	$this->ion_auth->update_last_login($this->user->id);
        	$group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                	$member_id = $this->input->post('member_id');
                	if($member_id){
                		$member = $this->members_m->get_group_member($member_id,$this->group->id);
                		if($member){
                			$update = array(
				                'active' => 1,
				                'modified_on' => time(),
				                'modified_by' => $this->user->id
				            );
				            if($this->members_m->update($member->id,$update)){
				            	$response = array(
	                                'status' => 1,
	                                'message' => $member->first_name.' '.$member->last_name.' unsuspended successfully.', 
	                                'time' => time(),
	                            );
				            }else{
				            	$response = array(
	                                'status' => 0,
	                                'message' => $member->first_name.' '.$member->last_name.' could not be unsuspended.', 
	                                'time' => time(),
	                            );
				            }
                		}else{
                			$response = array(
                                'status' => 0,
                                'message' => 'Member selected to unsuspended not found',
                                'time' => time(),
                            );
                		}
                	}else{
                		$response = array(
                            'status' => 0,
                            'message' => 'Kindly select member to unsuspended',
                            'time' => time(),
                        );
                	}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
	}

	function delete_member(){
    	foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
        	$this->ion_auth->update_last_login($this->user->id);
        	$group_id = $this->input->post('group_id');
        	$response = array(
                'status' => 1,
                'message' => 'This action has been disabled',
                'time' => time(),
            );
            /*if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                	$password = $this->input->post('password');
                	$identity = valid_phone($this->user->phone)?:$this->user->email;
                	if($this->ion_auth->login($identity,$password)){
                		$member_id = $this->input->post('member_id');
                    	if($member_id){
                    		$member = $this->members_m->get_group_member($member_id,$this->group->id);
                    		if($member){
                    			if($member->id == $this->member->id){
                    				$response = array(
		                                'status' => 0,
		                                'message' => 'You can not delete yourself',
		                                'time' => time(),
		                            );
                    			}else{
                    				if($this->transactions->void_all_group_member_transactions($this->group->id,$member->id)){
				                        $update = array(
				                            'active' => 0,
				                            'is_deleted' => 1,
				                            'modified_on' => time(),
				                            'modified_by' => $this->user->id
				                        );
				                        if($this->members_m->update($member->id,$update)){
				                            $this->group_members->set_active_group_size($this->group->id);
				                            $response = array(
				                                'status' => 1,
				                                'message' => $member->first_name.' deleted successfully. ',
				                                'time' => time(),
				                            );
				                        }else{
				                            $response = array(
				                                'status' => 0,
				                                'message' => $member->first_name.' '.$member->last_name.' could not be deleted.',
				                                'time' => time(),
				                            );
				                        }
				                    }else{
				                    	$response = array(
			                                'status' => 0,
			                                'message' => 'Something went wrong while voiding all member records',
			                                'time' => time(),
			                            );
				                    }
                    			}
                    		}else{
                    			$response = array(
	                                'status' => 0,
	                                'message' => 'Member selected to delete not found',
	                                'time' => time(),
	                            );
                    		}
                    	}else{
                    		$response = array(
                                'status' => 0,
                                'message' => 'Kindly select member to delete',
                                'time' => time(),
                            );
                    	}
                	}else{
                		$response = array(
                            'status' => 0,
                            'message' => 'Authentication failed. Could not delete member',
                            'time' => time(),
                        );
                	}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }*/
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
	}

	function get_group_member_options(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                	$members = $this->members_m->get_active_group_members($this->group->id);
                	if($this->member->is_admin || $this->member->group_role_id){

                    }else{
                        if($this->group->enable_member_information_privacy){
                            $members = array($this->member);

                        }else{

                        }
                    }
                    
                    $posts = array();
                    foreach ($members as $member) {
                       $posts[] = array(
                            'id' => $member->id,
                            'name' => $member->first_name.' '.$member->last_name,
                            'avatar' => $member->avatar,
                            'user_id' => $member->user_id,
                            'identity' =>	valid_email($member->email)?$member->email:$member->phone, 
                       );
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'Members',
                        'time' => time(),
                        'members' => $posts,
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }


    function edit_group_member(){
    	foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                	$member_id = $this->input->post('member_id');
                	if($member_to_edit = $this->members_m->get_group_member($member_id,$this->group->id)){
                		if($member_to_edit->user_id == $this->user->id){
                			$response = array(
                				'status' => 0,
                				'message' => 'You can not edit your user profile. Go to settings > My profile'
                			);
                		}else{
                			if($member_to_edit->last_login){
                				$response = array(
	                				'status' => 0,
	                				'message' => 'You can not edit an active member. Kindly communicate to the member to update their profile',
	                			);
                			}else{
                				$first_name = $this->input->post('first_name')?:$member_to_edit->first_name;
			                	$last_name = $this->input->post('last_name')?:$member_to_edit->last_name;
			                	$phone = $this->input->post('phone')?:$member_to_edit->phone;
			                	$email = $this->input->post('email')?:$member_to_edit->email;
		                		if($first_name&&$last_name&&(valid_email($email) || valid_phone($phone))){
		                			$update = array(
										'first_name' => $first_name,
						                'last_name' => $last_name,
						                'email' => $email,
						                'phone' => valid_phone($phone),
						                'modified_on' => time(),
						                'modified_by' => $this->user->id
						            );
						            if($this->ion_auth->update($member_to_edit->user_id,$update)){
						            	$response= array(
											'status' =>	1,
											'message' => 'successful',
										);
						            }else{
						            	$response= array(
											'status' =>	0,
											'message' =>	'Error occured updating user details. Try again',
										);
						            }
		                		}else{
		                			$response= array(
										'status' =>	0,
										'message' =>	'First name, last name and user identity are required',
									);
		                		}
                			}
                		}
                	}else{
                		$response= array(
							'status' =>	0,
							'message' =>	'Member you are trying to edit is not available in the group',
						);
                	}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function send_invitation(){
    	foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                	$member_id = $this->input->post('member_id');
                	if($member_to_invite = $this->members_m->get_group_member($member_id,$this->group->id)){
                		if($this->messaging->send_group_invitation_to_user($this->group,$member_to_invite,$member_to_invite->id,$this->user,$this->member->id,TRUE,TRUE)){
                			$response = array(
                				'status' => 1,
                				'message' => 'Invitation sent successfully',
                			);
				        }else{
				        	$response = array(
                				'status' => 0,
                				'message' => 'Could not send user invitation',
                			);
				        }
                	}else{
                		$response= array(
							'status' =>	0,
							'message' =>	'Member you are trying to edit is not available in the group',
						);
                	}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }


    function reassign_member_group_role(){
        $usernames = array();
        $phones = array();
        $group_role_ids = array();
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;                    
            }
        }
        $user_id = $this->input->post('user_id');
        $group_id = $this->input->post('group_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $member_id = $this->input->post('member_id');
                    $role_in_use = FALSE;
                    if($member_id){
                        $group_role_id = $this->input->post('group_role_id')?:'';
                        $member_to_assign = $this->members_m->get_group_member($member_id,$this->group->id);
                        if($member_to_assign){
                        	if($group_role_id){
	                            if($group_role_id == $member_to_assign->group_role_id){

	                            }else{
	                                if($this->members_m->check_if_group_role_id_is_assigned($group_role_id,$this->group->id)){
	                                    $role_in_use = TRUE;
	                                }
	                            }
	                        }
	                        if($role_in_use){
	                            $response = array(
	                                'status' => 0,
	                                'message' => 'Member update failed. Role already in use. Reassign user already with role',
	                            );
	                        }else{
	                            $update = array(
	                                'group_role_id' => $group_role_id?:'',
	                                'modified_by' => $this->user->id,
	                                'modified_on' => time(),
	                            );
	                            if($this->members_m->update($member_to_assign->id,$update)){
	                                $response = array(
	                                    'status' => 1,
	                                    'message' => 'Group member role successfully reassigned',
	                                );
	                            }else{
	                                $response = array(
	                                    'status' => 0,
	                                    'message' => 'Member update failed. Could not reasign role',
	                                );
	                            }
	                        }
                        }else{
                        	$response = array(
                        		'status' => 0,
                        		'message' => 'Member to assign role not found'
                        	);
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Invalid request: Missing member_id',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
            );
        }
        echo encrypt_json_encode(array('response'=>$response));        
    }

}
