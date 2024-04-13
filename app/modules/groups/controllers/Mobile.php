<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mobile extends Mobile_Controller{

	function __construct(){
        parent::__construct();
        $this->load->model('members/members_m');
        $this->load->model('themes/themes_m');
        $this->load->model('groups_m');
        $this->load->library('files_uploader');
    }

    public function _remap($method, $params = array()){
       if(method_exists($this, $method)){
           return call_user_func_array(array($this, $method), $params);
       }
       $this->output->set_status_header('404');
       header('Content-Type: application/json');
       $file = file_get_contents('php://input')?(array)json_decode(file_get_contents('php://input')):array();
       $request = $_REQUEST+$file;
       echo json_encode(
       	array(
       		'response' => array(
		       		'status'	=>	404,
		       		'message'		=>	'404 Method Not Found for URI: '.$this->uri->uri_string(),
       			),

       	));
	}

    protected $validation_rules = array(
        array(
            'field' => 'member_listing_order_by',
            'label' => 'Group Members Listing Order',
            'rules' => 'trim|callback__valid_order_by',
        ),
        array(
            'field' => 'order_members_by',
            'label' => 'Group Members Listing Order By',
            'rules' => 'trim|callback__valid_order_member_by',
        ),
        array(
            'field' => 'enable_member_information_privacy',
            'label' => 'Enforce Member Information Privacy',
            'rules' => 'trim|numeric',
        ),
        array(
            'field' => 'enable_send_monthly_email_statements',
            'label' => 'Enable Send Monthly Email Statements',
            'rules' => 'trim|numeric',
        ),
        array(
            'field' => 'disable_arrears',
            'label' => 'Disable Arrears',
            'rules' => 'trim|numeric',
        ),
        array(
            'field' => 'disable_ignore_contribution_transfers',
            'label' => 'Disable Ignore Contribution Transfers',
            'rules' => 'trim|numeric',
        ),
        array(
            'field' => 'enable_bulk_transaction_alerts_reconciliation',
            'label' => 'Enable Bulk Transaction Alerts Reconciliation',
            'rules' => 'trim|numeric',
        ),
        array(
            'field' => 'disable_member_directory',
            'label' => 'Disable Member Directory',
            'rules' => 'trim|numeric',
        ),
    );

    function _valid_order_by(){
        $member_listing_order_by = $this->input->post('member_listing_order_by');
        if($member_listing_order_by){
            if(array_key_exists($member_listing_order_by, $this->member_listing_order_by_options)){
                return TRUE;
            }else{
                $this->form_validation->set_message('_valid_order_by','Select a valid order by method');
                return FALSE;
            }
        }
    }

    function _valid_order_member_by(){
        $order_members_by = $this->input->post('order_members_by');
        if($order_members_by){
            if(array_key_exists($order_members_by, $this->order_by_options)){
                return TRUE;
            }else{
                $this->form_validation->set_message('_valid_order_member_by','Select a valid member order by');
                return FALSE;
            }
        }
    }

    function edit_profile_photo(){
        if($_FILES){
            $user_id = $this->input->get_post('user_id');
            $group_id = $this->input->get_post('group_id');
            $request = array(
                'file' => $_FILES,
                'user_id' => $user_id,
                'group_id' => $group_id,
            );
            $this->user = $this->ion_auth->get_user($user_id);
            if($this->user){
                if($this->group = $this->groups_m->get($group_id)){
                    if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                        $groups_directory = './uploads/groups';
                        if(!is_dir($groups_directory)){
                            mkdir($groups_directory,0777,TRUE);
                        }
                        $avatar['file_name'] = ''; 
                        if($_FILES['avatar']['name']){
                            $avatar = $this->files_uploader->upload('avatar',$groups_directory);
                            if($avatar){
                                if(is_file(FCPATH.$groups_directory.'/'.$this->group->avatar)){
                                    if(unlink(FCPATH.$groups_directory.'/'.$this->group->avatar)){
                                        
                                    }
                                }
                                if($avatar['file_name']){
                                    $update = array(
                                        'avatar' => $avatar['file_name']?:$this->group->avatar,
                                        'modified_on' => time(),
                                        'modified_by' => $this->user->id,
                                    );
                                    if($this->groups_m->update($this->group->id,$update)){
                                        $response = array(
                                            'status'    =>  1,
                                            'message' => 'successful',
                                            'avatar' => $avatar['file_name'],
                                        );
                                    }else{
                                        $response = array(
                                            'status' =>  0,
                                            'message' =>  'Error updating group profile picture',
                                        );
                                    }
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Error uploading profile picture',
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
                            'message' => 'Could not find member details',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find group details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status'    =>  4,
                    'time'  =>  time(),
                );
            }
        }else{
            $response = array(
                'status'    =>  2,
                'error'     =>  'No files sent',
            );
        }
        echo json_encode(array('response'=>$response));
    }

    function update_name(){
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
                    $name = strtoupper($this->input->post('name'));
                    if($name){
                        $update = array(
                            'name' => $name,
                            'modified_by' => $this->user->id,
                            'modified_on' => time(),
                        );
                        if($this->groups_m->update($this->group->id,$update)){
                            $response = array(
                                'status' => 1,
                                'message' => 'Successful',
                                'name' => $name,
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Failed to update Group name',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Group name not found',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }            
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
    }

    function update_phone(){
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
                    $phone = valid_phone($this->input->post('phone'));
                    if(valid_phone($phone)){
                        $update = array(
                            'phone' => $phone,
                            'modified_by' => $this->user->id,
                            'modified_on' => time(),
                        );
                        if($this->groups_m->update($this->group->id,$update)){
                            $response = array(
                                'status' => 1,
                                'message' => 'Successful',
                                'phone' => $phone,
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Failed to update Group phone number',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Group phone number is invalid',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }  
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
    }

    function update_email(){
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
                    $email = $this->input->post('email');
                    if(valid_email($email)){
                        $update = array(
                            'email' => $email,
                            'modified_by' => $this->user->id,
                            'modified_on' => time(),
                        );
                        if($this->groups_m->update($this->group->id,$update)){
                            $response = array(
                                'status' => 1,
                                'message' => 'Successful',
                                'email' => $email,
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Failed to update Group email address',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Group Email is invalid',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }   
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
    }

    function update_country(){
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
                    $country_id = $this->input->post('country_id');
                    if(is_numeric($country_id)){
                        $country = $this->countries_m->get($country_id);
                        if($country){
                            $update = array(
                                'country_id' => $country->id,
                                'modified_by' => $this->user->id,
                                'modified_on' => time(),
                            );
                            if($this->groups_m->update($this->group->id,$update)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Successful',
                                    'country_id' => $country_id,
                                    'country_name' => $country->name,
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Failed to update Group email address',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Country selected not found'
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Group Email is invalid',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }   
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
    }

    function update_currency(){
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
                    $currency_id = $this->input->post('currency_id');
                    if(is_numeric($currency_id)){
                        $country = $this->countries_m->get($currency_id);
                        if($country){
                            $update = array(
                                'currency_id' => $country->id,
                                'modified_by' => $this->user->id,
                                'modified_on' => time(),
                            );
                            if($this->groups_m->update($this->group->id,$update)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Successful',
                                    'currency_id' => $currency_id,
                                    'currency' => $country->currency_code,
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Failed to update Group email address',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Country selected not found'
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Group Email is invalid',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }   
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
    }

    function update_group_settings(){
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
                    $this->form_validation->set_rules($this->validation_rules);
                    if($this->form_validation->run()){
                        $input = array(
                            'member_listing_order_by' => $this->input->post('member_listing_order_by')?:$this->group->member_listing_order_by,
                            'order_members_by' => $this->input->post('order_members_by')?:$this->group->order_members_by,
                            'enable_send_monthly_email_statements' => $this->input->post('enable_send_monthly_email_statements')?1:0,
                            'enable_bulk_transaction_alerts_reconciliation' => $this->input->post('enable_bulk_transaction_alerts_reconciliation')?1:0,
                            'enable_member_information_privacy' => $this->input->post('enable_member_information_privacy')?1:0,
                            'disable_arrears' => $this->input->post('disable_arrears')?1:0,
                            'disable_ignore_contribution_transfers' => $this->input->post('disable_ignore_contribution_transfers')?1:0,
                            'disable_member_directory' => $this->input->post('disable_member_directory')?1:0,
                            'disable_member_edit_profile' => $this->input->post('disable_member_edit_profile')?1:0,
                            'enable_absolute_loan_recalculation' => $this->input->post('enable_absolute_loan_recalculation')?1:0,
                            'modified_by' => $this->user->id,
                            'modified_on' => time(),
                        );
                        $result = $this->groups_m->update($this->group->id,$input);
                        if($result){
                            $response = array(
                                'status' => 1,
                                'message' => 'Changes saved successfully',
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not update group settings',
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
                            'time' => time(),
                            'error' => 'Form validation failed',
                            'validation_errors' => $post,
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }   
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
    }
}
?>
