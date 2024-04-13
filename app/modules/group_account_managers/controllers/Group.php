<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{
	
    protected $validation_rules = array(
        array(
            'field' =>  'first_name',
            'label' =>  'First Name',
            'rules' =>  'xss_clean|trim|required',
        ),array(
            'field' =>  'last_name',
            'label' =>  'Last Name',
            'rules' =>  'xss_clean|trim|required',
        ),array(
            'field' =>  'middle_name',
            'label' =>  'Middle Name',
            'rules' =>  'xss_clean|trim',
        ),array(
            'field' =>  'phone',
            'label' =>  'Phone Number',
            'rules' =>  'xss_clean|trim|required|callback_phone_is_unique',
        ),array(
            'field' =>  'email',
            'label' =>  'Email address',
            'rules' =>  'xss_clean|trim|valid_email|callback_email_is_unique',
        ),array(
            'field' =>  'id_number',
            'label' =>  'ID Number',
            'rules' =>  'xss_clean|trim|callback_id_number_is_unique',
        ),
    );

	function __construct(){
        parent::__construct();
        $this->load->model('group_account_managers_m');
    }

    // public function index(){
    //     $data = array();
    //     $this->template->title('Group Account Managers')->build('group/index',$data);
    // }

    public function add_group_account_managers(){
    	$data = array();
    	$posts = $_POST;
    	$errors = array();
    	$error_messages = array();
    	$successes = array();
    	$phones = array();
        $emails = array();
    	$group_roles = array();
        $entries_are_valid = TRUE;
    	if($this->input->post('submit')){
    		if(!empty($posts)){ 
                if(isset($posts['first_names'])){
                    $count = count($posts['first_names']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['first_names'][$i])&&isset($posts['last_names'][$i])&&isset($posts['phones'][$i])&&isset($posts['emails'][$i])):
        					//first names
                        		if($posts['first_names'][$i]==''){
                        			$successes['first_names'][$i] = 0;
                        			$errors['first_names'][$i] = 1;
                        			$error_messages['first_names'][$i] = 'Please enter a first name';
                                    $entries_are_valid = FALSE;
                        		}else{
                        			$successes['first_names'][$i] = 1;
                        			$errors['first_names'][$i] = 0;
                        		}
        					//last names
    							if($posts['last_names'][$i]==''){
                        			$successes['last_names'][$i] = 0;
                        			$errors['last_names'][$i] = 1;
                        			$error_messages['last_names'][$i] = 'Please enter a last name';
                                    $entries_are_valid = FALSE;
                        		}else{
                        			$successes['last_names'][$i] = 1;
                        			$errors['last_names'][$i] = 0;
                        		}
        					//phones
                        		if($posts['phones'][$i]==''){
                        			$successes['phones'][$i] = 0;
                        			$errors['phones'][$i] = 1;
                        			$error_messages['phones'][$i] = 'Please enter a phone number';
                                    $entries_are_valid = FALSE;
                        		}else{
                        			if(valid_phone($posts['phones'][$i])){
    	                    			if(in_array($posts['phones'][$i],$phones)){
    		                    			$successes['phones'][$i] = 0;
    		                    			$errors['phones'][$i] = 1;
                        					$error_messages['phones'][$i] = 'Please enter another phone number, you cannot have duplicated phone numbers';
                                            $entries_are_valid = FALSE;   
                                        }else{		
    		                    			$successes['phones'][$i] = 1;
    		                    			$errors['phones'][$i] = 0;
    		                    			$phones[] = $posts['phones'][$i];
                        				}	                    			
                        			}else{	
    	                    			$successes['phones'][$i] = 0;
    	                    			$errors['phones'][$i] = 1;
                        				$error_messages['phones'][$i] = 'Please enter a valid phone number';
                                        $entries_are_valid = FALSE;
                        			}
                        		}
        					   //emails
                        		if($posts['emails'][$i]==''){
                        			$successes['emails'][$i] = 1;
                        			$errors['emails'][$i] = 0;
                        		}else{
                        			if(valid_email($posts['emails'][$i])){
                        				if(in_array($posts['emails'][$i],$emails)){
    		                    			$successes['emails'][$i] = 0;
    		                    			$errors['emails'][$i] = 1;
                        					$error_messages['emails'][$i] = 'Please enter another email address, you cannot have duplicated email addresses';
                                            $entries_are_valid = FALSE;
                                        }else{		
    		                    			$successes['emails'][$i] = 1;
    		                    			$errors['emails'][$i] = 0;
    		                    			$emails[] = $posts['emails'][$i];
                        				}
                        			}else{	
    	                    			$successes['emails'][$i] = 0;
    	                    			$errors['emails'][$i] = 1;
                        				$error_messages['emails'][$i] = 'Please enter a valid email addresses';
                                        $entries_are_valid = FALSE;
                        			}
                        		}
        				endif;
        			endfor;
                }
        	}

            if($entries_are_valid){
                if(isset($posts['first_names'])){
                    $count = count($posts['first_names']);
                    $successful_invitations_count = 0;
                    $unsuccessful_invitations_count = 0;
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['first_names'][$i])&&isset($posts['last_names'][$i])&&isset($posts['phones'][$i])&&isset($posts['emails'][$i])):
                            $send_invitation_sms = isset($posts['send_invitation_sms'][$i])?$posts['send_invitation_sms'][$i]:0;
                            $send_invitation_email = isset($posts['send_invitation_email'][$i])?$posts['send_invitation_email'][$i]:0;
                            $first_name = strip_tags($posts['first_names'][$i]);
                            $last_name = strip_tags($posts['last_names'][$i]);
                            if($this->group_members->add_group_account_manager_to_group($this->group,$first_name,$last_name,$posts['phones'][$i],$posts['emails'][$i],$send_invitation_sms,$send_invitation_email,$this->user,$this->member->id)){
                                $successful_invitations_count++;
                            }else{
                                $unsuccessful_invitations_count++;
                            }
                        endif;
                    endfor;
                    if($successful_invitations_count){
                        if($successful_invitations_count==1){
                            $this->session->set_flashdata('success',$successful_invitations_count.' group account manager successfully added to your group.');
                        }else{
                            $this->session->set_flashdata('success',$successful_invitations_count.' group account managers successfully added to your group.');
                        }
                    }
                    if($unsuccessful_invitations_count){
                        if($unsuccessful_invitations_count==1){
                            $this->session->set_flashdata('warning',$unsuccessful_invitations_count.' group account manager was not added to your group.');
                        }else{
                            $this->session->set_flashdata('warning',$unsuccessful_invitations_count.' group account managers were not added to your group.');
                        }
                    }
                    redirect('group/group_account_managers/listing');
                }
            }else{
                $this->session->set_flashdata('error','There are some errors on the form. Please review and try again.');
            }
    	}
        $data['posts'] = $posts;
    	$data['errors'] = $errors;
    	$data['error_messages'] = $error_messages;
    	$data['successes'] = $successes;
        $this->template->title('Add Group Account Managers')->build('group/add_group_account_managers',$data);
    }

    public function index(){
        $data = array();    
        $this->template->title(translate('List Group Account Managers'))->build('group/listing',$data);
    } 

    public function ajax_add_group_account_managers(){
        $response = array();
        $data = array();
        $posts = $_POST;
        $errors = array();
        $error_messages = array();
        $successes = array();
        $phones = array();
        $emails = array();
        $group_roles = array();
        $entries_are_valid = TRUE;
        if(!empty($posts)){ 
            if(isset($posts['first_names'])){
                $count = count($posts['first_names']);
                for($i=0;$i<=$count;$i++):
                    if(isset($posts['first_names'][$i])&&isset($posts['last_names'][$i])&&isset($posts['phones'][$i])&&isset($posts['emails'][$i])):
                        //first names
                            if($posts['first_names'][$i]==''){
                                $successes['first_names'][$i] = 0;
                                $errors['first_names'][$i] = 1;
                                $error_messages['first_names'][$i] = 'Please enter a first name';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['first_names'][$i] = 1;
                                $errors['first_names'][$i] = 0;
                            }
                        //last names
                            if($posts['last_names'][$i]==''){
                                $successes['last_names'][$i] = 0;
                                $errors['last_names'][$i] = 1;
                                $error_messages['last_names'][$i] = 'Please enter a last name';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['last_names'][$i] = 1;
                                $errors['last_names'][$i] = 0;
                            }
                        //phones
                            if($posts['phones'][$i]==''){
                                $successes['phones'][$i] = 0;
                                $errors['phones'][$i] = 1;
                                $error_messages['phones'][$i] = 'Please enter a phone number';
                                $entries_are_valid = FALSE;
                            }else{
                                if(valid_phone($posts['phones'][$i])){
                                    if(in_array($posts['phones'][$i],$phones)){
                                        $successes['phones'][$i] = 0;
                                        $errors['phones'][$i] = 1;
                                        $error_messages['phones'][$i] = 'Please enter another phone number, you cannot have duplicated phone numbers';
                                        $entries_are_valid = FALSE;   
                                    }else{      
                                        $successes['phones'][$i] = 1;
                                        $errors['phones'][$i] = 0;
                                        $phones[] = $posts['phones'][$i];
                                    }                                   
                                }else{  
                                    $successes['phones'][$i] = 0;
                                    $errors['phones'][$i] = 1;
                                    $error_messages['phones'][$i] = 'Please enter a valid phone number';
                                    $entries_are_valid = FALSE;
                                }
                            }
                           //emails
                            if($posts['emails'][$i]==''){
                                $successes['emails'][$i] = 1;
                                $errors['emails'][$i] = 0;
                            }else{
                                if(valid_email($posts['emails'][$i])){
                                    if(in_array($posts['emails'][$i],$emails)){
                                        $successes['emails'][$i] = 0;
                                        $errors['emails'][$i] = 1;
                                        $error_messages['emails'][$i] = 'Please enter another email address, you cannot have duplicated email addresses';
                                        $entries_are_valid = FALSE;
                                    }else{      
                                        $successes['emails'][$i] = 1;
                                        $errors['emails'][$i] = 0;
                                        $emails[] = $posts['emails'][$i];
                                    }
                                }else{  
                                    $successes['emails'][$i] = 0;
                                    $errors['emails'][$i] = 1;
                                    $error_messages['emails'][$i] = 'Please enter a valid email addresses';
                                    $entries_are_valid = FALSE;
                                }
                            }
                    endif;
                endfor;
            }
        }
        if($entries_are_valid){
            if(isset($posts['first_names'])){
                $count = count($posts['first_names']);
                $successful_invitations_count = 0;
                $unsuccessful_invitations_count = 0;
                for($i=0;$i<=$count;$i++):
                    if(isset($posts['first_names'][$i])&&isset($posts['last_names'][$i])&&isset($posts['phones'][$i])&&isset($posts['emails'][$i])):
                        $send_invitation_sms = isset($posts['send_invitation_sms'][$i])?$posts['send_invitation_sms'][$i]:0;
                        $send_invitation_email = isset($posts['send_invitation_email'][$i])?$posts['send_invitation_email'][$i]:0;
                        $first_name = strip_tags($posts['first_names'][$i]);
                        $last_name = strip_tags($posts['last_names'][$i]);
                        if($this->group_members->add_group_account_manager_to_group($this->group,$first_name,$last_name,$posts['phones'][$i],$posts['emails'][$i],$send_invitation_sms,$send_invitation_email,$this->user,$this->member->id)){
                            $successful_invitations_count++;
                        }else{
                            $unsuccessful_invitations_count++;
                        }
                    endif;
                endfor;
                if($successful_invitations_count){
                    if($successful_invitations_count==1){
                        $response = array(
                            'status' => 1,
                            'message' => $successful_invitations_count.' group account manager successfully added to your group.',
                            'refer'=>site_url('group/group_account_managers/listing'),
                        );

                    }else{
                        $response = array(
                            'status' => 1,
                            'message' => $successful_invitations_count.' group account managers successfully added to your group.',
                            'refer'=>site_url('group/group_account_managers/listing'),
                        );
                    }
                    
                }
                if($unsuccessful_invitations_count){
                    $response = array(
                        'status' => 0,
                        'message' => $unsuccessful_invitations_count.' group account manager was not added to your group.',
                        'refer'=>site_url('group/group_account_managers/listing'),
                    );
                }
            }
        }else{
            $response = array(
                'status' => 0,
                'validation_errors'=>$error_messages,
                'message' => 'There are some errors on the form. Please review and try again.',
            );
        }
        echo json_encode($response);
    }

    public function ajax_edit_group_account_managers(){
        $response = array();
        $id = $this->input->post('id');
        if($id){
            $post = $this->group_account_managers_m->get_group_account_manager($id);
            $this->form_validation->set_rules($this->validation_rules);
            if($this->form_validation->run()){
                $user_input = array(
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'middle_name' => $this->input->post('middle_name'),
                    'email' => $this->input->post('email'),
                    'phone' => valid_phone($this->input->post('phone')),
                    'id_number' => $this->input->post('id_number'),
                    //'avatar' => $avatar['file_name']?:$post->avatar,
                    'modified_on' => time(),
                    'modified_by' => $this->user->id
                );
                $group_account_manager_input = array(
                    'modified_on' => time(),
                    'modified_by' => $this->user->id
                );
                if($user_update_result = $this->ion_auth->update($post->user_id,$user_input)){
                    if($group_account_manager_update_result = $this->group_account_managers_m->update($post->id,$group_account_manager_input)){
                        $response = array(
                            'status'=>1,
                            'refer'=>site_url('group/group_account_managers/listing'),
                            'message'=>'Account manager details activated successfully',
                        );
                    }else{
                        $response = array(
                            'status'=>0,
                            'message'=>'Could not update group account manager profile',
                        );
                    }
                }else{
                    $response = array(
                        'status'=>0,
                        'message'=>'Could not update user profile',
                    );
                }
            }else{
                $post = array();
                $form_errors = $this->form_validation->error_array();
                foreach ($form_errors as $key => $value) {
                    $post[$key] = $value;
                }
                $response = array(
                    'status' => 0,
                    'message' => 'There are some errors on the form. Please review and try again.',
                    'validation_errors' => $post,
                );
            }
        }else{
            $response = array(
                'status'=>0,
                'message'=>'User id variable is required',
            );
        }
        echo json_encode($response);
    }

    function ajax_get_group_account_managers_listing(){
        $total_rows = $this->group_account_managers_m->count_group_account_managers();
        $pagination = create_pagination('group/group_account_managers/listing/pages', $total_rows,50,5,TRUE);
        $posts = $this->group_account_managers_m->limit($pagination['limit'])->get_group_account_managers();
        if(!empty($posts)){
            echo form_open('group/members/action', ' id="form"  class="form-horizontal"');
                if ( ! empty($pagination['links'])):
                echo '
                <div class="row col-md-12">
                    <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Group Account Managers</p>';
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links']; 
                    echo '</div></div>';
                endif; 
                echo '  
                <table class="table m-table m-table--head-separator-primary">
                    <thead>
                        <tr>
                            <th width=\'2%\'>
                                <label class="m-checkbox">
                                    <input type="checkbox" name="check" value="all" class="check_all">
                                    <span></span>
                                </label>
                            </th>
                            <th>
                                #
                            </th>
                            <th>
                                Name
                            </th>
                            <th>
                                Contacts
                            </th>
                            <th>
                                Last Seen
                            </th>
                            <th width="30%">
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                        $i = $this->uri->segment(5, 0); foreach($posts as $post):
                            echo '
                            <tr>
                                <td>
                                    <label class="m-checkbox">
                                        <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" />
                                        <span></span>
                                    </label>
                                </td>
                                <td>'.($i+1).'</td>
                                <td>'.$post->first_name.' '.$post->last_name.'</td>
                                <td>'.$post->phone.'</br> '.$post->email.'</td>
                                <td>';
                                    if($post->last_login){
                                        echo timestamp_to_datetime($post->last_login); 
                                    }else{
                                        echo "Never Logged in";
                                    }
                                
                            echo '
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="'.site_url('group/group_account_managers/edit/'.$post->id).'" class="btn btn-sm btn-primary">
                                            <i class="la la-pencil"></i>
                                            Edit Profile
                                        </a>
                                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a href="'.site_url('group/group_account_managers/send_invitation/'.$post->id).'" class="dropdown-item"><i class="fa fa-ship"></i> Send Invitation</a>
                            ';
                                    if($this->user->id==$post->user_id){ 

                                    }else{
                                        if($post->active){
                                            echo '
                                                <a href="'.site_url('group/group_account_managers/suspend/'.$post->id).'" class="dropdown-item"><i class="la la-remove"></i>Suspend</a>
                                            ';
                                        }else{
                                            echo '
                                                <a href="'.site_url('group/group_account_managers/activate/'.$post->id).'" class="dropdown-item confirmation_link"><i class="la la-check-square-o"></i>Activate</a>
                                            ';
                                        }
                                    }
                                echo '
                                        </div>
                                    </div>
                                </td>
                            </tr>';
                            $i++;
                            endforeach;
                        echo '
                    </tbody>
                </table>
                <div class="clearfix"></div>
                <div class="row col-md-12">'; 
                if( ! empty($pagination['links'])): 
                    echo $pagination['links']; 
                endif; 
                echo ' 
                </div>
                <div class="clearfix"></div>';
                if($posts):
                    echo '
                    <button class="btn btn-sm btn-success confirmation_bulk_action" name=\'btnAction\' value=\'bulk_send_invitation\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-ship\'></i> Bulk Send Invitation </button>';
                endif;
            echo form_close();
        }else{
            echo '
                <div class="m-alert m-alert--icon m-alert--outline alert alert-primary" role="alert">
                    <div class="m-alert__icon">
                        <i class="la la-warning"></i>
                    </div>
                    <div class="m-alert__text">
                        <strong>'. translate('Oops') .'! </strong>'. translate('Looks like you have not added a group account manager yet') .'.
                    </div>  
                    <div class="m-alert__actions" style="width: 200px;">
                        <a href="'.site_url('group/group_account_managers/add_group_account_managers').'" class="btn btn-primary btn-sm m-btn m-btn--pill m-btn--wide">
                            '. translate('Add Group Account Manager Now') .'
                        </a>   
                    </div>              
                </div>
            ';
        }
    }

    public function edit($id = 0){
        $id OR redirect('group/group_account_managers');
        $post = $this->group_account_managers_m->get_group_account_manager($id);
        $post OR redirect('group/group_account_managers');
        $errors = array();
        $error_messages = array();
        $successes = array();
        $data['post'] = $post;
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $groups_directory = './uploads/groups';
            if(!is_dir($groups_directory)){
                mkdir($groups_directory,0777,TRUE);
            }
            $avatar['file_name'] = ''; 
            if($_FILES['avatar']['name']){
                 $avatar = $this->files_uploader->upload('avatar',$groups_directory);
                 if($avatar){
                    if(is_file(FCPATH.$groups_directory.'/'.$post->avatar)){
                        if(unlink(FCPATH.$groups_directory.'/'.$post->avatar)){
                            $this->session->set_flashdata('info','Group manager profile picture successfully replaced');
                        }
                    }

                 }
            }
            $user_input = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'middle_name' => $this->input->post('middle_name'),
                'email' => $this->input->post('email'),
                'phone' => valid_phone($this->input->post('phone')),
                'id_number' => $this->input->post('id_number'),
                'avatar' => $avatar['file_name']?:$post->avatar,
                'modified_on' => time(),
                'modified_by' => $this->user->id
            );
            $group_account_manager_input = array(
                'modified_on' => time(),
                'modified_by' => $this->user->id
            );
            if($user_update_result = $this->ion_auth->update($post->user_id,$user_input)){
                //do nothing for now
            }else{
                $this->session->set_flashdata('error','Could not update user profile');
            }
            if($group_account_manager_update_result = $this->group_account_managers_m->update($post->id,$group_account_manager_input)){
                //do nothing for now
            }else{
                $this->session->set_flashdata('error','Could not update group account manager profile');
            }
            redirect('group/group_account_managers/listing');
        }else{
            //do nothing for now
        }
       $this->template->title('Edit Group Account Manager',$post->first_name.' '.$post->last_name)->build('group/form',$data);
    }

    public function send_invitation($id = 0,$redirect = TRUE){
        $id OR redirect('group/group_account_managers/listing');
        $post = $this->group_account_managers_m->get_group_account_manager($id);
        $post OR redirect('group/group_account_managers/listing');
        $user = $this->ion_auth->get_user($post->user_id);
        $user OR redirect('group/group_account_managers/listing');
        if($this->messaging->send_group_invitation_to_user($this->group,$user,$post->id,$this->user,$this->member->id,TRUE,TRUE)){
            $this->session->set_flashdata('success','Invitation sent successfully');
        }else{
            $this->session->set_flashdata('error','Invitation not sent');
        }
        if($redirect){
            if($this->agent->referrer()){
                redirect($this->agent->referrer());
            }else{
                redirect('group/group_account_managers/listing');
            }
        }
    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_send_invitation'){
            for($i=0;$i<count($action_to);$i++){
                $this->send_invitation($action_to[$i],FALSE);
            }
        }
        if($this->agent->referrer()){
            redirect($this->agent->referrer());
        }else{
            redirect('group/members/listing');
        }
    }

    public function ajax_valid_phone(){
    	$phone = $this->input->phone('phone');
    	if(valid_phone($phone)){
    		echo 'valid';
    	}else{
    		echo 'invalid';
    	}
    }

    public function phone_is_unique(){
        $phone = valid_phone($this->input->post('phone'));
        if($user = $this->ion_auth->identity_check($phone)){
            if($this->input->post('user_id')==$user->id){
                return TRUE;
            }else{
                $this->form_validation->set_message('phone_is_unique', 'The phone number is already registered to another member.');
                return FALSE;
            }
        }else{
            return TRUE;
        }
    }

    public function email_is_unique(){
        $email = $this->input->post('email');
        if($email==''){
            return TRUE;
        }else{
            if($user = $this->ion_auth->identity_check($email)){
                if($this->input->post('user_id')==$user->id){
                    return TRUE;
                }else{
                    $this->form_validation->set_message('email_is_unique', 'The email address is already registered to another member.');
                    return FALSE;
                }
            }else{
                return TRUE;
            }
        }
    }

    public function id_number_is_unique(){
        $id_number = $this->input->post('id_number');
        if($id_number==''){
            return TRUE;
        }else{
            if($user = $this->ion_auth->id_number_check($id_number)){
                if($this->input->post('user_id')==$user->id){
                    return TRUE;
                }else{
                    $this->form_validation->set_message('id_number_is_unique', 'The id number is already registered to another member.');
                    return FALSE;
                }
            }else{
                return TRUE;
            }
        }
    }

    public function suspend($id = 0){
        $id OR redirect('group/group_account_managers/listing');
        $post = $this->group_account_managers_m->get_group_account_manager($id);
        $post OR redirect('group/group_account_managers/listing');
        if($this->user->id==$post->user_id||$post->user_id==$this->group->owner){
            $this->session->set_flashdata('warning','You cannot suspend this group account manager.');
        }else{
            $input = array(
                'active' => 0,
                'modified_on' => time(),
                'modified_by' => $this->user->id
            );
            if($this->group_account_managers_m->update($id,$input)){
                $this->session->set_flashdata('success',$post->first_name.' '.$post->last_name.' suspended successfully. ');
            }else{
                $this->session->set_flashdata('error',$post->first_name.' '.$post->last_name.' could not be suspended. ');
            }
        }
        redirect('group/group_account_managers/listing');
    }

    public function activate($id = 0){
        $id OR redirect('group/group_account_managers/listing');
        $post = $this->group_account_managers_m->get_group_account_manager($id);
        $post OR redirect('group/group_account_managers/listing');
        $input = array(
            'active' => 1,
            'modified_on' => time(),
            'modified_by' => $this->user->id
        );
        if($this->group_account_managers_m->update($id,$input)){
            $this->session->set_flashdata('success',$post->first_name.' '.$post->last_name.' activated successfully. ');
        }else{
            $this->session->set_flashdata('error',$post->first_name.' '.$post->last_name.' could not be activated. ');
        }
        redirect('group/group_account_managers/listing');
    }

}