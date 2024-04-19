<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends Mobile_Controller{

    protected $validation_rules = array(
        /*array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'required'
        ),*/
        array(
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'required|trim|valid_email'
        ),
    );
    protected $validation_rules_user_details = array(
        array(
            'field' => 'loan_limit',
            'label' => 'Loan Limit',
            'rules' => 'required|valid_currency'
        ),
        array(
            'field' => 'id_number',
            'label' => 'ID number',
            'rules' => 'required|trim'
        ),
    );
    protected $validation_rules_check_limit = array(
  
        array(
            'field' => 'id_number',
            'label' => 'ID number',
            'rules' => 'required|trim'
        ),
    );
    protected $change_number_validation_rules =array(
        array(
            'field' => 'phone',
            'label' => 'Old Phone Number',
            'rules' => 'trim'
        ),
        array(
            'field' => 'new_phone',
            'label' => 'New Phone Number',
            'rules' => 'required|trim|valid_phone'
        ),
        /*array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'required'
        ),*/
    );

    protected $user_registration_rules =array(
        array(
            'field' => 'id_number',
            'label' => 'ID Number',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'phone_number',
            'label' => 'Phone Number',
            'rules' => 'required|trim|valid_phone'
        ),
        array(
            'field' => 'last_name',
            'label' => 'Last Name',
            'rules' => 'required|trim'
        ),
        array(
            'field' => 'calling_code',
            'label' => 'Calling Code',
            'rules' => 'required|trim'
        ),
        array(
            'field' => 'first_name',
            'label' => 'First Name',
            'rules' => 'required|trim'
        ),
        array(
            'field' => 'middle_name',
            'label' => 'Middle Name',
            'rules' => 'trim'
        ),
        array(
            'field' =>  'email',
            'label' =>  'Email address',
            'rules' =>  'trim|valid_email',
        ),
        array(
            'field' => 'loan_limit',
            'label' => 'Loan  Limit',
            'rules' => 'trim|required|valid_currency'
        ),
        /*array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'required'
        ),*/
    );
	function __construct(){
        parent::__construct();
        $this->load->model('users/users_m');
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
       echo json_encode(
       	array(
       		'response' => array(
		       		'status'	=>	404,
		       		'message'		=>	'404 Method Not Found for URI: '.$this->uri->uri_string(),
       			),

       	));
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
            $name = $this->input->post('name');
            $password = $this->input->post('password');
            $identity = valid_phone($this->user->phone)?$this->user->phone:(valid_email($this->user->email)?$this->user->email:'');
            if($name){
                $first_name = '';
                $last_name = '';
                $middle_name = '';
                $names = explode(' ',$name);
                if(count($names)>1){
                    if(count($names) == 2){
                        $first_name = ucfirst($names[0]);
                        $last_name = ucfirst($names[ 1]);
                    }elseif(count($names) == 3){
                        $first_name = ucfirst($names[0].' '.$names[1]);
                        $last_name = ucfirst($names[ 2]);
                    }elseif(count($names) == 4){
                        $first_name = ucfirst($names[0].' '.$names[1]);
                        $last_name = ucfirst($names[2].' '.$names[3]);
                    }else{
                        $first_name = ucfirst($name);
                    }
                    $update = array(
                        'first_name' =>$first_name,
                        'last_name' => $last_name,
                        'modified_by' => $this->user->id,
                        'modified_on' => time(),
                    );
                    if($this->ion_auth->update($this->user->id,$update)){
                        $response = array(
                            'status' => 1,
                            'message' => 'Successful',
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Failed to update your names',
                        );
                    }
                }else{
                   $response = array(
                        'status' => 0,
                        'message' => 'Kindly enter all your usernames. You can not use one name',
                    ); 
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'User name is not found',
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
        $user_id = $this->input->post('user_id')?:0;
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $this->form_validation->set_rules($this->validation_rules);
            if($this->form_validation->run()){
                $password = $this->input->post('password');
                $email = $this->input->post('email');
                $identity = valid_phone($this->user->phone)?$this->user->phone:(valid_email($this->user->email)?$this->user->email:'');
                if($this->ion_auth->identity_check($email)){
                    $response = array(
                        'status' => 0,
                        'message' => 'Email update failed: Email already in use with a different account',
                    );
                }else{
                    $update = array(
                        'email' => $email,                       
                        'modified_on' => time(),
                        'modified_by' => $this->user->id,
                    );
                    if($this->ion_auth->update($this->user->id,$update)){
                        $response = array(
                            'status' => 1,
                            'email' => $email,
                            'message' => 'Success',
                            'time' => time(),
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Email update failed: Error occured. Try again',
                            'time' => time(),
                        );
                    }
                }
            }else{
                $post = array();
                $form_errors = $this->form_validation->error_array();
                foreach ($form_errors as $key => $value) {
                    $post[$key] = $value;
                }
                $response = array(
                    'status' => 0,
                    'message' => 'Form validation failed',
                    'validation_errors' => $post,
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
        echo json_encode(array('response'=>$response));
    }
    function update_user_details(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $this->form_validation->set_rules($this->validation_rules_user_details);
        if($this->form_validation->run()){
            $user_id = $this->input->post('id_number')?:0;
        if($this->user = $this->users_m->get_user_by_id_number($user_id)){
             
            $this->ion_auth->update_last_login($this->user->id);
            $loan_limit =($this->input->post('loan_limit')) ??$this->user->limit;
            $update=array(
                "loan_limit"=>$loan_limit
            );
            if($this->users_m->update_user($this->user->id,$update)){
                $response = array(
                    'status' => 0,
                    'message' => 'User details updated successfully',
                    'time' => time(),
                );
            }
            else{
                $response = array(
                    'status' => 1,
                    'message' => 'Something went wrong when updating user Details',
                    'time' => time(),
                );
            }
               
        
            
        }else{
            $response = array(
                'status' => 1,
                'message' => 'Could not find user details',
                'time' => time(),
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
            'message' => 'Form validation failed',
            'validation_errors' => $post,
            'time' => time(),
        );
    }
        echo json_encode(array('response'=>$response));
    }
    function check_user_loan_limit(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $this->form_validation->set_rules($this->validation_rules_check_limit);
        if($this->form_validation->run()){
            $user_id = $this->input->post('id_number')?:0;
        if($this->user = $this->users_m->get_user_by_id_number($user_id)){
            print_r($this->user);
            die;
            $this->ion_auth->update_last_login($this->user->id);  
            $response = array(
                'status' => 0,
                'message' => 'User details Found',
                'limit'=>$this->user->loan_limit,
                'time' => time(),
            );  
        }else{
            $response = array(
                'status' => 1,
                'message' => 'Could not find user details',
                'time' => time(),
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
            'message' => 'Form validation failed',
            'validation_errors' => $post,
            'time' => time(),
        );
    }
        echo json_encode(array('response'=>$response));
    }
    function change_user_phone_number(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id')?:0;
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $this->form_validation->set_rules($this->change_number_validation_rules);
            if($this->form_validation->run()){
                $password = $this->input->post('password');
                $identity = valid_phone($this->user->phone)?$this->user->phone:(valid_email($this->user->email)?$this->user->email:'');
                $new_number = valid_phone($this->input->post('new_phone'));
                if($this->ion_auth->identity_check($new_number)){
                    $response = array(
                        'status' => 0,
                        'message' => 'Phone change failed: Phone number already in use with a different account',
                    );
                }else{
                    $one_time_pin = rand(1111,9999);
                    $data = array(
                        "old_number" => $this->user->phone,
                        "new_number" => $new_number,
                        "user_id" => $this->user->id,
                        "document_number" => $this->user->document_number,
                        "document_type" => $this->user->document_type,
                        "one_time_pin" => $one_time_pin,
                        "status" => 1,
                        "old_first_name" => $this->user->first_name,
                        "old_last_name" => $this->user->last_name,
                        "active" => 1,
                        "created_on" => time(),
                        "created_by" => $this->user->id,
                        
                    );
                    if($this->users_m->insert_change_phone_request($data)){
                        if($this->messaging->send_change_phone_activation_code($this->user,$one_time_pin,$new_number)){
                            $response = array(
                                'status' => 1,
                                'message' => 'successful',
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Phone number update failed: Failed to send OTP code. Try again',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Phone number update failed: Could not initiate request. Try again',
                        );
                    }
                }
            }else{
                $post = array();
                $form_errors = $this->form_validation->error_array();
                foreach ($form_errors as $key => $value) {
                    $post[$key] = $value;
                }
                $response = array(
                    'status' => 0,
                    'message' => 'Form validation failed',
                    'validation_errors' => $post,
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
        echo json_encode(array('response'=>$response));
    }
    function register_user(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $id_number=$this->input->post("id_number");
        $phone_number=$this->input->post("phone_number");
        $email_address=($this->input->post("email"))?$this->input->post("email"):'';
        $first_name=$this->input->post("first_name");
        $send_invitation_sms=0;
        $send_invitation_email=0;
        $middle_name=$this->input->post("middle_name");
        $last_name=$this->input->post("last_name");
        $calling_code=$this->input->post("calling_code");
        $loan_limit=$this->input->post("loan_limit");
        $response=array();
        $this->form_validation->set_rules($this->user_registration_rules);
        if($this->form_validation->run()){
        if(!$this->user = $this->users_m->get_user_by_phone_or_id_number($phone_number,$id_number)){
           
            $this->form_validation->set_rules($this->user_registration_rules);
            $this->group=array(
                'id'=>1
             );
             $this->user=array(
                'id'=>1
             );
             if ($member_id=$this->group_members->add_member_to_group(
                $this->group,
                $first_name,
                $last_name,
                $phone_number,
                $email_address,
                FALSE,
                FALSE,
                $this->user,
                '',
                1,
                '',
                $calling_code,
                $phone_number,
                FALSE,
                $id_number,
                $loan_limit,
            )) {
                         
                    $response = array(
                        'status' => 0,
                        'message' => 'A user Registered successfully'
                    );  
                }
                else{
                    $response = array(
                        'status' => 1,
                        'message' => 'A user Not registered'
                    );
                }
                
           
        }else{
            
            $response = array(
                'status' => 1,
                'message' => 'A user is already registered to that phone number or ID'
            );
        }
    }
    else{
        $post = array();
        $form_errors = $this->form_validation->error_array();
        foreach ($form_errors as $key => $value) {
            $post[$key] = $value;
        }
        $response = array(
            'status' => 0,
            'message' => 'Form validation failed',
            'validation_errors' => $post,
            'time' => time(),
        );
    }

        echo json_encode(array('response'=>$response));
    }

    function resend_code(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id')?:0;
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $phone = $this->input->post('phone');
            $new_phone = valid_phone($this->input->post('new_phone'));
            $change_number_request = $this->users_m->get_user_change_phone_number_request($this->user->id,$phone,$new_phone);
            if($change_number_request){
                $one_time_pin = rand(1111,9999);
                $update = array(
                    "one_time_pin" => $one_time_pin,
                    "modified_by" => $this->user->id,
                    "modified_on" => time(),
                );
                if($this->users_m->update_change_number_request($change_number_request->id,$update)){
                    $response = array(
                        'status' => 1,
                        'message' => 'successful',
                    );
                    if($this->messaging->send_change_phone_activation_code($this->user,$one_time_pin,$new_phone)){
                        $response = array(
                            'status' => 1,
                            'message' => 'successful',
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Phone number update failed: Failed to send OTP code. Try again',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not resend code: update failed. Try again later',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not resend code. Kindly start change start over again',
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
            );
        }
        echo json_encode(array('response'=>$response));
    }

    function verify_code(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id')?:0;
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $code = $this->input->post('code');
            $phone = $this->input->post('phone');
            $new_phone = $this->input->post('new_phone');
            if($code){
                $change_number_request = $this->users_m->get_user_change_phone_number_request($this->user->id,$phone,$new_phone);
                if($change_number_request){
                    if($code == $change_number_request->one_time_pin){
                        $update = array(
                            "status" => 2,
                            "modified_by" => $this->user->id,
                            "modified_on" => time(),
                        );
                        if($this->users_m->update_change_number_request($change_number_request->id,$update)){
                            $new_phone = valid_phone($change_number_request->new_number);
                            $user_update = array(
                                'phone' => $new_phone,
                                'modified_on' => time(),
                                'modified_by' => $this->user->id,
                            );
                            if($this->ion_auth->update($this->user->id,$user_update)){
                                $auth = $this->users_m->get_user_authentication_by_identity($this->user->phone);
                                if($this->users_m->update_user_pin_access_token($change_number_request->id,$user_update)){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Successfully updated user phone number',
                                        'phone' => $new_phone,
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Failed to update user profile. Try again',
                                    );
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => strip_tags($this->ion_auth->errors()).' Kindly contact support for account merger',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not verify code: update failed. Try again later',
                                'time' => time(),
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Invalid code submitted. Resend to receive correct activation code',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not verify code. Try again later',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Form validation failed',
                    'validation_errors' => 'Enter code sent to your phone number',
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
        echo json_encode(array('response'=>$response));
    }

    function edit_profile_photo(){
        if($_FILES){
            $user_id = $this->input->get_post('user_id');
            $request = array(
                'file' => $_FILES,
                'user_id' => $user_id,
            );
            $this->user = $this->ion_auth->get_user($user_id);
            if($this->user){
                $groups_directory = './uploads/groups';
                if(!is_dir($groups_directory)){
                    mkdir($groups_directory,0777,TRUE);
                }
                $avatar['file_name'] = ''; 
                if($_FILES['avatar']['name']){
                    $avatar = $this->files_uploader->upload('avatar',$groups_directory);
                    if($avatar){
                        if(is_file(FCPATH.$groups_directory.'/'.$this->user->avatar)){
                            if(unlink(FCPATH.$groups_directory.'/'.$this->user->avatar)){
                                
                            }
                        }
                        if($avatar['file_name']){
                            $update = array(
                                'avatar' => $avatar['file_name'],
                                'modified_on' => time(),
                                'modified_by' => $this->user->id,
                            );
                            if($this->ion_auth->update($this->user->id,$update)){
                                $response = array(
                                    'status'    =>  1,
                                    'message' => 'successful',
                                    'avatar' => $avatar['file_name'],
                                );
                            }else{
                                $response = array(
                                    'status'    =>  0,
                                    'message'       =>  'Error updating your profile picture',
                                );
                            }
                        }else{
                            $response = array(
                                'status'    =>  0,
                                'message'       =>  'Error uploading profile picture',
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

}
?>
