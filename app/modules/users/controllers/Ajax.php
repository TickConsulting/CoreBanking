<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
	
	protected $data = array();

    protected $solutions = array(
        1 => 'I am in a Sacco',
        2 => 'I am a Sacco official/owner',
        3 => 'I am into Digital Lending',
        4 => 'I am in a Microfinance',
        5 => 'I am a Microfinance owner',
        6 => 'Other',
    );

    protected $products = array(
        1 => 'Websacco OnPremise Platform',
        2 => 'Websacco Cloud Platform',
    );

    protected $preffered_contact_method = array(
        1 =>'Phone',
        2 => 'E-mail',
    );

    protected $validation_rules = array(
        array(
            'field' =>  'question_1',
            'label' =>  'solution',
            'rules' =>  'xss_clean|required|trim',
        ),
        array(
            'field' =>  'question_2',
            'label' =>  'product type are you intrested in',
            'rules' =>  'xss_clean|required|trim',
        ),
        array(
            'field' =>  'enable_phone_contact',
            'label' =>  'phone',
            'rules' =>  'xss_clean|trim|callback__check_if_contact_method_is_checked',
        ),
    );

    protected  $change_password_validation_rules =  array(
        array(
            'field' =>  'old_password',
            'label' =>  'Current Password',
            'rules' =>  'xss_clean|trim|required|min_length[8]|max_length[20]|callback__check_password_matching',
        ),
        array(
            'field' =>  'new_password',
            'label' =>  'New Password',
            'rules' =>  'xss_clean|trim|required|min_length[8]|max_length[20]|callback__check_password_strenght',
        ),
        array(
            'field' =>  'conf_password',
            'label' =>  'Confirm Password',
            'rules' =>  'xss_clean|trim|required|matches[new_password]',
        )
    );
    
    function __construct(){
        parent::__construct();        
        $this->load->library('cryptojs');
    }

    function _check_if_contact_method_is_checked(){
        $enable_phone_contact = $this->input->post('enable_phone_contact');
        $enable_email_contact = $this->input->post('enable_email_contact');
        if($enable_phone_contact || $enable_email_contact){
            return true;
        }else{
            $this->form_validation->set_messsage('_check_if_contact_method_is_checked','Choose one contact method');
            return FALSE;
        }
    }

    function update_email_address(){
        $email = $this->input->post('email');
        if($email){
            if(valid_email($email)){
                $input = array(
                    'email'         => $this->input->post('email'),
                    'modified_on'   => time(),
                    'modified_by'   => $this->user->id,
                );
                $update = $this->ion_auth->update($this->user->id, $input);
                if($this->ion_auth->update($this->user->id, $input)){
                    $response = array(
                        'status' => 1,
                        'message' => 'Email successfully updated'
                    );
                }else{
                    $response = array(
                        'status'=> 0,
                        'message'=> $this->ion_auth->errors(),
                    );
                }
            }else{
                $response = array(
                    'status'=>0,
                    'message'=>'Email address submitted is not valid.',
                );
            }
        }else{
            $response = array(
                'status'=>0,
                'message'=>'Email Address field is required.',
            );
        }

        echo json_encode($response);
    }

    public function create_demo_users(){
        $data = array();
        $response = array();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $phone_number = strtolower($this->input->post('phone'));
            $full_name = $this->input->post('full_name');
            $email = $this->input->post('email');
            $solution = $this->input->post('question_1');
            $product = $this->input->post('question_2');
            $enable_phone_contact = $this->input->post('enable_phone_contact');
            $enable_email_contact = $this->input->post('enable_email_contact');
            $full_names =explode(' ', $full_name);
            if(count($full_names) > 1){
                $count = count($full_names);
                if($count == 2){
                    $first_name = $full_names[0];
                    $last_name = $full_names[1];
                }else if($count == 3){
                    $first_name = $full_names[0];
                    $last_name = $full_names[1].' '.$full_names[2];
                }else if($count == 4){
                    $first_name = $full_names[0];
                    $last_name = $full_names[1].' '.$full_names[2].' '.$full_names[3];
                }
                if($first_name && $last_name){
                    $input_data = array(
                        'created_on'=>time(),
                        'created_by'=>$this->user->id,
                        'user_id'=>$this->user->id,
                        'active'=>1, 
                        'first_name'=>ucwords($first_name), 
                        'last_name'=>ucwords($last_name), 
                        'phone'=>$phone_number, 
                        'email'=>$email, 
                        'solution'=>$solution,
                        'product'=>$product,
                        'enable_phone_contact'=>$enable_phone_contact,
                        'enable_email_contact'=>$enable_email_contact,
                    );
                    if($user_request = $this->users_m->check_if_demo_request_exist($this->user->id)){
                        $preffered_contact = '';
                        if($user_request->enable_phone_contact && $user_request->enable_email_contact){
                            $preffered_contact = 'Phone or email';
                        }else if($user_request->enable_phone_contact){
                            $preffered_contact = 'Phone';
                        }else if($user_request->enable_email_contact){
                            $preffered_contact = 'Email';
                        }

                        $email_array = array(
                            'full_name'=>$user_request->first_name . ' '.$user_request->last_name,
                            'phone'=>$user_request->phone,
                            'email'=>$user_request->email,
                            'solution'=>$this->solutions[$user_request->solution],
                            'product'=>$this->products[$user_request->product],
                            'preffered_contact'=>$preffered_contact,
                        );
                        $this->messaging->send_demo_request_email($email_array);                        
                        $response = array(
                            'status'=>1,
                            'refer'=>'https://websacco-demo.websacco.com',
                            'message' => strip_tags($this->ion_auth->messages()),
                        );
                        //print_r($response); die();
                    }else{
                        if($user_id = $this->users_m->insert_demo_requests($input_data)){
                            $preffered_contact = '';
                            if($enable_phone_contact && $enable_email_contact){
                                $preffered_contact = 'Phone or email';
                            }else if($enable_phone_contact){
                                $preffered_contact = 'Phone';
                            }else if($enable_email_contact){
                                $preffered_contact = 'Email';
                            }
                            $email_array = array(
                                'full_name'=>$full_name,
                                'phone'=>$phone_number,
                                'email'=>$email,
                                'solution'=>$this->solutions[$solution],
                                'product'=>$this->products[$product],
                                'preffered_contact'=>$preffered_contact,
                            );
                            $this->messaging->send_demo_request_email($email_array);
                            $response = array(
                                'status'=>1,
                                //'refer'=>site_url('verify_otp'),
                                'message' => strip_tags($this->ion_auth->messages()),
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not create user demo requests',
                            );
                        }
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Full name entered is not valid. Enter first name and last name',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Full name entered is not valid. Enter first and last name',
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
        echo json_encode($response);
    }

    public function change_password(){
        $response = array();
        if($_POST){
            foreach ($_POST as $key => $value) {
                $_POST[$key] = $this->cryptojs->cryptoJsAesDecrypt($this->session->userdata("pass_key"),$value);
            }
        }
        $this->form_validation->set_rules($this->change_password_validation_rules);
        if($this->form_validation->run()){
            $password = $this->input->post('new_password');
            $input = array(
                'password' => $this->input->post('new_password'),
                'password_check'=>$this->ion_auth->hash_password($password,'','',1),
                'modified_on'=>time(),
                'modified_by'=>$this->user->id,
            );
            if($this->ion_auth->update($this->user->id, $input)){                
                $response = array(
                    'status' => 1,
                    'message' => 'Password successfully changed',
                    'refer' => site_url('logout'),
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => $this->ion_auth->errors(),
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
                'message' => 'Form validation errors',
                'validation_errors' => $post,
            ); 
        }
        echo json_encode($response);
    }

    function _check_password_matching(){
        if($this->input->post('old_password') == $this->input->post('new_password')){
            $this->form_validation->set_message('_check_password_matching','Old Password must not be the same as the new password!');
            return FALSE;
        }else{
            if($this->ion_auth->login($this->user->phone,$this->input->post('old_password'))){
                return TRUE;
            }else{
                $this->form_validation->set_message('_check_password_matching','Old Password is incorrect!');
                return FALSE; 
            }
        }
    }

    public function _check_password_strenght() {
        $password = $this->input->post('new_password');
        //print_r(checkPassword( $password)); die();
        if(containsDigits($password)) {
            if (containsMixedCase($password)) {
                if(containsSpecialChars($password)){
                    //print_r('pass'); die();
                    $code = $this->input->post_get('code');
                    if($code){
                        $user = $this->ion_auth->forgotten_password_check($code); 
                   }else{
                        $user = $this->user;
                   }                    
                    $salt = $this->config->item('store_salt', 'ion_auth') ? $this->config->item('store_salt', 'ion_auth') : FALSE;
                    $new_password = $this->ion_auth->hash_password($password);
                  
                    $password_check = $user->password_check?$user->password_check:'';
                    if($this->ion_auth->hash_password($password,'','',1) == $password_check){
                        $this->form_validation->set_message('_check_password_strenght','The new password must not be the same as the old password!');
                        return FALSE;
                    }else{
                        $rest_password_options = $this->users_m->get_password_reset_user_pairings($user->id);                        
                        if(empty($rest_password_options)){
                            return TRUE;
                        }else{
                            if(array_key_exists($this->ion_auth->hash_password($password,'','',1), $rest_password_options)){
                                $this->form_validation->set_message('_check_password_strenght','Looks like you already have used that password before, kindly generate a new password');
                                return FALSE;
                            }else{
                                return TRUE;
                            }
                        }
                    }
                }else{
                    $this->form_validation->set_message('_check_password_strenght','Password must include at least one character!');
                    return FALSE;  
                }                
            }else{
                $this->form_validation->set_message('_check_password_strenght','Password must be a mixture of upper and lower case !');
                return FALSE;  
            }
        }else{
            $this->form_validation->set_message('_check_password_strenght','Password must include at least one digit!');
            return FALSE;
        }   
    }
}
