<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Ajax extends Ajax_Controller{

	function __construct(){
		parent::__construct();
        $this->load->library('cryptojs');
        $this->load->library('curl');
        $this->load->model('settings/settings_m');
        $this->load->model('members/members_m');
        $this->load->model('notifications/notifications_m');
	}

    protected $expiry_time = "10 minutes";

    protected $signup_rules = array(
        array(
                'field' =>  'full_name',
                'label' =>  'Full Name',
                'rules' =>  'required|trim|xss_clean|callback__first_name_has_illegal_characters',
            ),
        array(
                'field' =>  'phone',
                'label' =>  'Phone Number',
                'rules' =>  'trim|required|xss_clean|callback__valid_phone',
            ),
        array(
                'field' =>  'email',
                'label' =>  'Email Address',
                'rules' =>  'trim|xss_clean|valid_email',
            ),
        array(
                'field' =>  'password',
                'label' =>  'Password',
                'rules' =>  'required|trim|min_length[8]|callback__check_password_strength',
            ),
        array(
                'field' =>  'calling_code',
                'label' =>  'Calling Code',
                'rules' =>  'required|trim|required',
            ),
    );


    protected $create_group_rules = array(
        array(
                'field' =>  'group_name',
                'label' =>  'Group Name',
                'rules' =>  'required|trim|xss_clean|callback__group_name_has_illegal_characters',
            ),  
        array(
                'field' =>  'group_size',
                'label' =>  'Group Size',
                'rules' =>  'required|currency|trim|xss_clean|min_length[1]|max_length[10000]',
            ),  
        array(
                'field' =>  'group_type',
                'label' =>  'Group Type',
                'rules' =>  'required|trim|numeric|xss_clean',
            ),  
        array(
                'field' =>  'group_role_key',
                'label' =>  'Group Role',
                'rules' =>  'trim|xss_clean',
            ), 
        array(
                'field' =>  'group_is_registered',
                'label' =>  'Group Registered',
                'rules' =>  'trim|xss_clean',
            ),
        array(
                'field' =>  'country_id',
                'label' =>  'Country of Operation',
                'rules' =>  'trim|numeric|required|xss_clean',
            ),
        array(
                'field' =>  'currency_id',
                'label' =>  'Group Currency',
                'rules' =>  'trim|numeric|required|xss_clean',
            ),
    );

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

    protected $demo_validation_rules = array(
        array(
            'field' =>  'question_1',
            'label' =>  'solution',
            'rules' =>  'required|trim',
        ),
        array(
            'field' =>  'question_2',
            'label' =>  'product type are you intrested in',
            'rules' =>  'required|trim',
        ),
        array(
            'field' =>  'enable_phone_contact',
            'label' =>  'phone',
            'rules' =>  'trim|callback__check_if_contact_method_is_checked',
        ),
        array(
            'field' =>  'full_name',
            'label' =>  'Your Name',
            'rules' =>  'trim|required',
        ),
        array(
            'field' =>  'email',
            'label' =>  'Email Address',
            'rules' =>  'trim|required',
        ),
        array(
            'field' =>  "phone",
            'label' =>  'Phone Number',
            'rules' =>  'trim|required',
        ),
    );    

    protected $exempt_otp = [
        '2547398712777',
        '254707158577',
        '254791569999',
        '254723085151',
        '254727440464',
        '256702379997',
        '256703659391',
        'innovations@chamasoft.com'
    ];

    function _register_group_rules(){
        if($this->input->post('group_is_registered')){
            $this->create_group_rules[] = array(
                'field' =>  'group_registration_certificate_number',
                'label' =>  'Group Registration Number',
                'rules' =>  'trim|required|xss_clean',
            );
        }
    }

    function _valid_phone(){
        $phone = $this->input->post('phone');
        if(!valid_phone($phone)){
            $this->form_validation->set_message('_valid_phone','Phone number entered is not a valid Phone Number');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    function _valid_identity(){
        $identity = $this->input->post('identity');
        if(!valid_email($identity)){
            if(!valid_phone($identity)){
                $this->form_validation->set_message('_valid_identity','The identity entered is not a valid Email Address or Phone Number');
                return FALSE;
            }
            return TRUE;
        }else{
            return TRUE;
        }
    }

    function _first_name_has_illegal_characters(){
        $first_name = $this->input->post('first_name');
        if(is_character_allowed($first_name)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_first_name_has_illegal_characters','You have entered illegal characters in the Full Name field, avoid using the following: % $ - - & * ? < > ');
            return FALSE;
        }
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

    function _check_admin_member_id(){
        $admin_member_id = $this->input->post('admin_member_id');
        if($admin_member_id&&is_numeric($admin_member_id)){
            return true;
        }else{
            $this->form_validation->set_message('_check_admin_member_id','Select a member to be the admin of the group');
            return FALSE;
        }
    }

    function _group_name_has_illegal_characters(){
        $group_name = $this->input->post('group_name');
        if(is_character_allowed($group_name)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_group_name_has_illegal_characters','You have entered illegal characters in the Group Name field, avoid using the following: % $ - - & * ? < > ');
            return FALSE;
        }
    }

	function login(){
        $response = array();
        if($_POST){
            foreach ($_POST as $key => $value) {
                $_POST[$key] = $this->cryptojs->cryptoJsAesDecrypt($this->session->userdata("pass_key"),$value);
            }
            if(!$this->input->post('identity')){
                $response = array(
                    'status'=>0,
                    'refer'=>site_url('login'),
                    'message' => 'Login failed due to invalid passphrase',
                );
            }
        }
        if(empty($response)){
    		$this->form_validation->set_rules('identity', 'Email / Phone Number', 'required|trim|callback__valid_identity');
            $this->form_validation->set_rules('password', 'Password', 'required|trim');
            $language_id = isset($_COOKIE['language_id'])?($_COOKIE['language_id']?:''):'';
            if($this->form_validation->run()){
                // check for google recaptcha if enabled.
                if($this->application_settings->enable_google_recaptcha){
                    $secret = $this->config->item('google_secret');
                    $recaptcha_response =  $this->input->post('g-recaptcha-response');
                    $check = array(
                        'secret' => $secret,
                        'response' => $recaptcha_response,
                    );
                    
                    $status = $this->curl->recaptcha_verify("https://www.google.com/recaptcha/api/siteverify",$check);
                                    
                    if (!$status['success']) {
                        $response = array(
                            'status' => 0,
                            'refer' => site_url('login'),
                            'message' => 'Verification failed, please try again',
                        );
                    }
                }
                
                if(empty($response)){

                    $identity = $this->input->post('identity');
                    $password = $this->input->post('password');
                    $remember = false;                                

                    if($this->ion_auth->login($identity, $password, $remember)){
                        $initial_page = $this->input->post('refer');

                        $this->session->set_userdata('refer', $initial_page);
                        $this->session->set_userdata('identity',$identity);
				        $this->session->set_userdata('phone',$identity);
				        $this->session->set_userdata('phone_number',$identity);
                        $refer = '';

                        $user = $this->ion_auth->get_user_by_identity($identity);
                        if($user->first_time_login_status == 1){
                            $this->ion_auth_model->forgotten_password($identity,'');
                            $user_data = $this->ion_auth->get_user_by_identity($identity); 
                            $code = $user_data->forgotten_password_code;
                            $response = array(
                                'status'=>1,
                                'refer'=>site_url('new_password?code='.$code),
                                'initial_page' => http_build_query(array('refer'=>$initial_page)),
                                'message' => strip_tags($this->ion_auth->messages()),
                            );
                        }else{
                                $confirmation_code = rand(100000,999999);
                                $otp_expiry_time = strtotime("+".$this->expiry_time,time());
                                if(valid_phone($identity) && in_array($identity, $this->exempt_otp)){
                                    $input = array(
                                        'otp_expiry_time'=>$otp_expiry_time,
                                        'login_validated' => 1,
                                        'modified_on' => time(),
                                        'confirmation_code'=>$confirmation_code,
                                    );
                                }else{
                                    $input = array(
                                        'otp_expiry_time'=>$otp_expiry_time,
                                        'login_validated' => $this->ion_auth->is_admin()?1:($this->application_settings->enable_two_factor_auth?0:1),
                                        'modified_on' => time(),
                                        'confirmation_code'=>$confirmation_code,
                                    );
                                }

                                // Update user on login.

                                if($this->users_m->update_user($user->id,$input)){
                                    $otp_array = array(
                                        'pin'=>$confirmation_code,
                                        'phone'=>$user->phone,
                                        'email'=>$user->email,
                                        'first_name'=>ucwords($user->first_name),
                                        'last_name'=>ucwords($user->last_name),
                                        'user_id' => $user->id,
                                        'language_id' => $user->language_id,
                                        'expiry_time' => $this->expiry_time,
                                    );
                                    // If not admin
                                    if(!$this->ion_auth->is_admin()){      
                                        // Check if the identity is not exempted.
                                        if(!in_array($identity, $this->exempt_otp)){
                                            if($this->application_settings->enable_two_factor_auth){
                                                $sent_otp = $this->messaging->send_user_otp($otp_array);
                                            }
                                        }                                
                                    }
                                    // Validate the refer URL
                                    if(filter_var($refer, FILTER_VALIDATE_URL)){
                                    }else{
                                        $refer = site_url('dashboard');
                                        if($this->ion_auth->is_admin()){ 
                                            $refer = site_url('dashboard');
                                         }

                                       
                                    }

                                    // Change the language based on the language_id

                                    if($language_id){
                                        $this->ion_auth->change_language($language_id);
                                    }

                                    // Send response

                                    $response = array(
                                        'status'=>1,
                                        'refer'=>$refer,
                                        'initial_page' => http_build_query(array('refer'=>$initial_page)),
                                        'message' => strip_tags($this->ion_auth->messages()),
                                    );

                                }else{

                                    // Cannot update user on login

                                    $response = array(
                                        'status'=>0,
                                        'message'=>strip_tags($this->ion_auth->errors()),
                                    );
                                }
                            }               
                        }else{
                            // Invalid credentials
                            $response = array(
                                'status'=>0,
                                'message'=>strip_tags($this->ion_auth->errors()),
                            );                                                  
                    }
                
            }
            }else{
                // Form validation errors 
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
        }
        echo json_encode($response);
	}

    function set_new_password(){
        $response = array();
        if($_POST){
            foreach ($_POST as $key => $value) {
                $_POST[$key] = $this->cryptojs->cryptoJsAesDecrypt($this->session->userdata("pass_key"),$value);
            }
            if(!$this->input->post('password')){
                $response = array(
                    'status'=>0,
                    'refer'=>site_url('set_new_password'),
                    'message' => 'Setting new password failed due to invalid passphrase',
                );
            }
        }
        if(empty($response)){
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]|callback__check_password_strength');
            if($this->form_validation->run()){
                $password = $this->input->post('password');
                if($this->ion_auth->reset_password($this->user->phone,$password)){
                    $user_input = array(
                        'password_check'=>$this->ion_auth->hash_password($password,'','',1),
                        'prompt_to_change_password'=>0,
                        'modified_on'=>time(),
                        'modified_by'=>$this->user->id,
                    );
                    if($this->users_m->update_user($this->user->id,$user_input)){
                        $response = array(
                            'status' => 1,
                            'message' => 'Password successfully set',
                            'refer' => site_url('dashboard'),
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not set the password at the moment. Try again',
                        );
                    }
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
        }
        echo json_encode($response);
    }

	function signup(){
        $response = array();
        if($this->ion_auth->logged_in()){  
            $response = array(
                'status' => 200,
                'refer' => 'authentication',
                'message' => 'user already logged in',
            );
        }else{
            if($_POST){
                foreach ($_POST as $key => $value) {
                    $_POST[$key] = $this->cryptojs->cryptoJsAesDecrypt($this->session->userdata("pass_key"),$value);
                }
                if(!$this->input->post('full_name')){
                    $response = array(
                        'status'=>0,
                        'refer'=>site_url('signup'),
                        'message' => 'Login failed due to invalid passphrase',
                    );
                }
            }
            if(empty($response)){
                $this->form_validation->set_rules($this->signup_rules);
                if($this->form_validation->run()){

                    if($this->application_settings->enable_google_recaptcha){
                        // check for google recaptcha 
                        $secret = $this->config->item('google_secret');
                        $recaptcha_response =  $this->input->post('g-recaptcha-response');
                        $check = array(
                            'secret' => $secret,
                            'response' => $recaptcha_response,
                        );
                        
                        $status = $this->curl->recaptcha_verify("https://www.google.com/recaptcha/api/siteverify",$check);
                                        
                        if (!$status['success']) {
                            $response = array(
                                'status' => 0,
                                'refer' => site_url('login'),
                                'message' => 'Verification failed, please try again',
                            );
                        }
                    }                    

                    if(empty($response)){
                        $phone_number = (int)strtolower($this->input->post('phone'));
                        $full_name = $this->input->post('full_name');
                        $email = $this->input->post('email');
                        $password = $this->input->post('password');
                        $calling_code = $this->input->post('calling_code');
                        $phone = $calling_code.$phone_number;
                        if(valid_phone($phone)){
                            if($email){
                                if(valid_email($email)){
                                    if($this->ion_auth->get_user_by_identity($email)){
                                        $response = array(
                                            'status' => 1,
                                            'refer' =>site_url('login'),
                                            'message' => 'You already have an account, please login to continue.',
                                        );
                                        echo json_encode($response);
                                        die;
                                    }
                                    // continue
                                }else{
                                    //exit here
                                    $response = array(
                                        'status' => 0,
                                        'refer' => '',
                                        'message' => 'Please enter a valid email address',
                                    );
                                    echo json_encode($response);
                                    die;
                                }
                            }
                            if($this->ion_auth->get_user_by_identity($phone)){
                                $response = array(
                                    'status' => 1,
                                    'refer' => site_url('login'),
                                    'message' => 'You already have an account, please login to continue.',
                                );
                            }else{
                                $group_id = $this->ion_auth->get_group_by_name('member');                            
                                if($group_id){
                                    $groups = array($group_id->id);
                                }else{
                                    $groups = array(2);
                                }
                                if($groups){
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
                                        if($first_name && $last_name && strtolower($first_name) != "customer"){
                                            $confirmation_code = rand(100000,999999);
                                            $language_id = isset($_COOKIE['language_id'])?$_COOKIE['language_id']:'';
                                            $otp_expiry_time = strtotime("+".$this->expiry_time,time());
                                            $additional_data = array(
                                                'created_on'=>time(),
                                                'active'=>1, 
                                                'password_check'=>$this->ion_auth->hash_password($password,'','',1),
                                                'ussd_pin'=>rand(1000,9999),
                                                'first_name'=>ucwords($first_name), 
                                                'last_name'=>ucwords($last_name), 
                                                'language_id'=>$language_id, 
                                                'is_validated'=>2,
                                                'confirmation_code'=>$confirmation_code,
                                                'otp_expiry_time' => $otp_expiry_time,
                                                'login_validated' => 0,
                                            );
                                            if($user_id = $this->ion_auth->register($phone,$password,$email, $additional_data,$groups,TRUE)){
                                                $message = strip_tags($this->ion_auth->messages());                                    
                                                if($this->ion_auth->login($phone, $password,1)){
                                                    $otp_array = array(
                                                        'pin'=>$confirmation_code,
                                                        'phone'=>$phone,
                                                        'email'=>$email,
                                                        'first_name'=>ucwords($first_name),
                                                        'last_name'=>ucwords($last_name),
                                                        'user_id' => $user_id,
                                                        'expiry_time' => $this->expiry_time,
                                                        'language_id' => $language_id,
                                                    );
                                                    $sent_otp = 0;
                                                    if($this->application_settings->enable_two_factor_auth){
                                                        $sent_otp = $this->messaging->send_user_otp($otp_array);

                                                    }
                                                    if($sent_otp){
                                                        $refer = $this->input->post('refer')?:'verify_otp';
                                                        if($language_id){
                                                            $this->ion_auth->change_language($language_id);
                                                        }
                                                        $response = array(
                                                            'status'=>1,
                                                            'refer'=>site_url('verify_otp'),
                                                            'message' => 'One Time Password sms sent to your phone number <strong>'.$phone.'</strong>.',
                                                        );
                                                    }else{
                                                        $response = array(
                                                            'status'=>0,
                                                            'message'=>'Could not send one time password',
                                                        );
                                                    }
                                                }else{
                                                    $response = array(
                                                        'status'=>1,
                                                        'message'=>'Error occured after signup. Kindly refresh this page',
                                                    );
                                                }
                                            }else{
                                                $response = array(
                                                    'status' => 0,
                                                    'message' => strip_tags($this->ion_auth->errors()),
                                                );
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
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Error occured on signup. Kindly contact admin',
                                    );
                                }
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Invalid phone number '.$phone,
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
                        'message' => 'Form validation errors',
                        'validation_errors' => $post,
                    );
                }
            }
        }
        echo json_encode($response);
	}

    public function create_demo_users(){
        $data = array();
        $response = array();
        $this->form_validation->set_rules($this->demo_validation_rules);
        if($this->form_validation->run()){
            $phone_number = strtolower($this->input->post('phone'));
            $full_name = $this->input->post('full_name');
            $email = $this->input->post('email');
            $solution = $this->input->post('question_1');
            $product = $this->input->post('question_2');
            $enable_phone_contact = $this->input->post('enable_phone_contact');
            $enable_email_contact = $this->input->post('enable_email_contact');
            $full_names = explode(' ', $full_name);
            if(sizeof($full_names) > 1){
                $count = sizeof($full_names);
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
                    $group_id = $this->ion_auth->get_group_by_name('member');
                    if($group_id){
                        $groups = array($group_id->id);
                    }else{
                        $groups = array(2);
                    }
                    $confirmation_code = rand(1000,9999);
                    $language_id = isset($_COOKIE['language_id'])?$_COOKIE['language_id']:'';
                    $additional_data = array(
                        'created_on'=>time(),
                        'active'=>1, 
                        'ussd_pin'=>rand(1000,9999),
                        'first_name'=>ucwords($first_name), 
                        'last_name'=>ucwords($last_name), 
                        'language_id'=>$language_id, 
                        'is_validated'=>2,
                        'confirmation_code'=>$confirmation_code,
                    );
                    $password = random_string();
                    if($user = $this->ion_auth->get_user_by_identity($phone_number)){
                        if($user_request = $this->users_m->check_if_demo_request_exist($user->id)){
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
                                'name' => $user_request->first_name,
                                'phone'=>$user_request->phone,
                                'email'=>$user_request->email,
                                'solution'=>$this->solutions[$user_request->solution],
                                'product'=>$this->products[$user_request->product],
                                'preffered_contact'=>$preffered_contact,
                            );
                            $this->messaging->send_demo_request_email($email_array);
                            $identity = 'innovations@chamasoft.com';
                            $password = 'innovations1234';
                            $remember = 1;
                            $demo_email = 'innovations@chamasoft.com';
                            $demo_phone = '+2547398712777';
                            $demo_password = 'innovations1234';
                            if($this->ion_auth->login($identity, $password, $remember)){
                                //print_r($demo_password); die();
                                
                                if(preg_match('/(local)/',$_SERVER['HTTP_HOST'])){
                                    $response = array(
                                        'status'=>1,
                                        'refer'=>'http://app.websacco.local/checkin',
                                        'message'=>'success',
                                    );
                                }else{
                                   $response = array(
                                        'status'=>1,
                                        'refer'=>'https://app.websacco.com/checkin',
                                        'message'=>'success',
                                    );
                                }
                            }else{
                                $response = array(
                                    'status'=>0,
                                    'message'=>strip_tags($this->ion_auth->errors()),
                                );
                            }                            
                        }else{
                            $input_data = array(
                                'created_on'=>time(),
                                'created_by'=>$this->user->id,
                                'user_id'=>$user->id,
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
                            if($demo_user_id = $this->users_m->insert_demo_requests($input_data)){
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
                                $identity = 'innovations@chamasoft.com';
                                $password = 'innovations1234';
                                $remember = 1;
                                $demo_email = 'innovations@chamasoft.com';
                                $demo_phone = '+2547398712777';
                                $demo_password = 'innovations1234';
                                if($this->ion_auth->login($identity, $password, $remember)){
                                    //print_r($demo_password); die();                                    
                                    if(preg_match('/(local)/',$_SERVER['HTTP_HOST'])){
                                        $response = array(
                                            'status'=>1,
                                            'refer'=>'websacco-demo.websacco.local',
                                            'message'=>'success',
                                        );
                                    }else{
                                       $response = array(
                                            'status'=>1,
                                            'refer'=>'websacco-demo.websacco.com',
                                            'message'=>'success',
                                        );
                                    }
                                }else{
                                    $response = array(
                                        'status'=>0,
                                        'message'=>strip_tags($this->ion_auth->errors()),
                                    );
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Could not create user demo requests',
                                );
                            }
                        }
                    }else{
                        if($user_id = $this->ion_auth->register($phone_number,$password,$email, $additional_data,$groups,TRUE)){        
                            $input_data = array(
                                'created_on'=>time(),
                                'created_by'=>$this->user->id,
                                'user_id'=>$user_id,
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
                                $identity = 'innovations@chamasoft.com';
                                $password = 'innovations1234';
                                $remember = 1;
                                $demo_email = 'innovations@chamasoft.com';
                                $demo_phone = '+2547398712777';
                                $demo_password = 'innovations1234';
                                if($this->ion_auth->login($identity, $password, $remember)){
                                    //print_r($demo_password); die();
                                    
                                    if(preg_match('/(local)/',$_SERVER['HTTP_HOST'])){
                                        $response = array(
                                            'status'=>1,
                                            'refer'=>'websacco-demo.websacco.local',
                                            'message'=>'success',
                                        );
                                    }else{
                                       $response = array(
                                            'status'=>1,
                                            'refer'=>'websacco-demo.websacco.com',
                                            'message'=>'success',
                                        );
                                    }
                                }else{
                                    $response = array(
                                        'status'=>0,
                                        'message'=>strip_tags($this->ion_auth->errors()),
                                    );
                                }
                            }else{
                                if($demo_user_id = $this->users_m->insert_demo_requests($input_data)){
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
                                    $identity = 'innovations@chamasoft.com';
                                    $password = 'innovations1234';
                                    $remember = 1;
                                    $demo_email = 'innovations@chamasoft.com';
                                    $demo_phone = '+2547398712777';
                                    $demo_password = 'innovations1234';
                                    if($this->ion_auth->login($identity, $password, $remember)){
                                        //print_r($demo_password); die();                                        
                                        if(preg_match('/(local)/',$_SERVER['HTTP_HOST'])){
                                            $response = array(
                                                'status'=>1,
                                                'refer'=>'websacco-demo.websacco.local',
                                                'message'=>'success',
                                            );
                                        }else{
                                           $response = array(
                                                'status'=>1,
                                                'refer'=>'websacco-demo.websacco.com',
                                                'message'=>'success',
                                            );
                                        }
                                    }else{
                                        $response = array(
                                            'status'=>0,
                                            'message'=>strip_tags($this->ion_auth->errors()),
                                        );
                                    }
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
                                'message' => strip_tags($this->ion_auth->errors()),
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

    function verify_otp_code(){
        $response = array();
        if($this->ion_auth->logged_in()){
            if($_POST){
                foreach ($_POST as $key => $value) {
                    $_POST[$key] = $this->cryptojs->cryptoJsAesDecrypt($this->session->userdata("pass_key"),$value);
                }
            }
            $this->form_validation->set_rules('code', 'One time password', 'required');
            if($this->form_validation->run()){
                if($this->user){
                    $code = $this->input->post('code');
                    if($this->user->otp_expiry_time > time()){
                        if($this->user->confirmation_code == $code){
                            $input = array(
                                'is_validated'=>1,
                                'login_validated' => 1,
                                'modified_on'=>time(),
                                'modified_by'=>$this->user->id,
                            );
                            if($this->users_m->update_user($this->user->id ,$input)){
                                $response = array(
                                'status'=>1,
                                'refer'=>site_url('checkin'),
                                'message' =>translate('OTP confirmed  successfully'),
                            );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => translate('Could not update user details'),
                                );  
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => translate('OTP code does not match'),
                            );  
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => translate('Your one time password has expired kindly generate a new code by clicking on Resend OTP button.'),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => translate('Could not find user details'),
                        'refer' => site_url('login'),
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
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Not logged in',
                'refer' => site_url('login'),
            );
        }
        echo json_encode($response);
    }

    function resend_otp_code(){
        if($this->ion_auth->logged_in()){
            $identity = $this->session->userdata['identity'];
            if($identity){
                $user = $this->ion_auth->get_user_by_identity($identity);
                if($user){
                    if($user->is_validated === 1){
                        $response = array(
                            'status' => 1,
                            'message' => 'User already verified proceed to check in',
                            'refer' => site_url('checkin'),
                        );
                    }else{
                        $otp_expiry_time = strtotime("+".$this->expiry_time,time());
                        $confirmation_code = rand(100000,999999);
                        $input = array(
                            'otp_expiry_time'=>$otp_expiry_time,
                            'modified_on' => time(),
                            'confirmation_code'=>$confirmation_code,
                        );
                        if($this->users_m->update_user($user->id,$input)){
                            $otp_array = array(
                                'pin'=>$confirmation_code,
                                'phone'=>$user->phone,
                                'email'=>$user->email,
                                'first_name'=>ucfirst($user->first_name),
                                'last_name'=> ucfirst($user->last_name),
                                'user_id' => $user->id,
                                'language_id' => $user->language_id,
                                'expiry_time' => $this->expiry_time,
                            );
                            $sent_otp = $this->messaging->send_user_otp($otp_array);
                            if($sent_otp){
                                $response = array(
                                    'status'=>1,
                                    //'refer'=>site_url('verify_otp'),
                                    
                                    // 'code' => $confirmation_code,
                                    'message' =>'One time password sms sent to your phone number <strong>'.$user->phone.'</strong> and email address <strong>'.$user->email.'</strong>.',
                                );
                            }else{
                                $response = array(
                                    'status'=>0,
                                    // 'code' => $confirmation_code,
                                    'message'=>'Could not send one time password',
                                );   
                            }
                        }else{
                            $confirmation_code = rand(1000,9999);
                            $input_array = array(
                                'confirmation_code'=>$confirmation_code,
                                'is_validated'=>2,
                                'modified_on'=>time(),
                                'modified_by'=>$user->id,
                            );
                            if($this->users_m->update_user($user->id ,$input_array)){
                                $otp_array = array(
                                    'pin'=>$confirmation_code,
                                    'phone'=>$user->phone,
                                    'email'=>$user->email,
                                    'first_name'=>ucfirst($user->first_name),
                                    'last_name'=>ucfirst($user->last_name),
                                    'user_id' => $user->id,
                                    'language_id' => $user->language_id,
                                    'expiry_time' => $this->expiry_time,
                                );
                                $sent_otp = $this->messaging->send_user_otp($otp_array);
                                if($sent_otp){
                                    $response = array(
                                        'status'=>1,
                                        //'refer'=>site_url('verify_otp'),
                                        'message' =>'One time password sms sent to your phone number <strong>'.$user->phone.'</strong> and email address <strong>'.$user->email.'</strong>.',
                                    );
                                }else{
                                    $response = array(
                                        'status'=>0,
                                        'message'=>'Could not send one time password',
                                    );   
                                }
                            }else{
                                $response = array(
                                    'status'=>0,
                                    'message'=>'Could not update user details',
                                );
                            }
                        }
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not user details',
                        'refer' => site_url('login'),
                    );   
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not get identity details',
                    'refer' => site_url('login'),
                );    
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Not logged in',
                'refer' => site_url('login'),
            );
        }
        echo json_encode($response);
    }

    function forgot_password(){
        $response = array();
        $message ='';
        if($_POST){
            foreach ($_POST as $key => $value) {
                $_POST[$key] = $this->cryptojs->cryptoJsAesDecrypt($this->session->userdata("pass_key"),$value);
            }
        }        

        $this->form_validation->set_rules('identity', 'Email / Phone Number', 'required|callback__valid_identity');

        if($this->form_validation->run()){

            if($this->application_settings->enable_google_recaptcha){
                // check for google recaptcha 
                $secret = $this->config->item('google_secret');
                $recaptcha_response =  $this->input->post('g-recaptcha-response');
                $check = array(
                    'secret' => $secret,
                    'response' => $recaptcha_response,
                );
                
                $status = $this->curl->recaptcha_verify("https://www.google.com/recaptcha/api/siteverify",$check);
                                
                if (!$status['success']) {
                    $response = array(
                        'status' => 0,
                        'refer' => site_url('login'),
                        'message' => 'Verification failed, please try again',
                    );
                }
            }

            if(empty($response)){
                $identity = trim(str_replace(' ', '',$this->input->post('identity')));
                $forgotten=$this->ion_auth->forgotten_password($identity,true);
                if($forgotten){ 
                    $forgotten = (object)$forgotten;
                    if(valid_email($identity)){
                        $message = 'Password recovery mail sent to <strong>'.$identity.'</strong>, please check your email inbox, if you don\'t find it there check your spam or junk folder.';
                        $response = array(
                            'status'=>1,
                            'message'=>$message,
                            'refer' => site_url("confirm_code?identity=".$identity),
                        );
                    }else{
                        $message = "Confirmation code sent to ".valid_phone($identity);
                        $response = array(
                            'status'=>1,
                            'message'=>$message,
                            'refer' => site_url("confirm_code?identity=".$identity),
                        );
                    }
                }else{
                    
                    $response = array(
                        'status'=> 0,
                        'message'=>"A user with email address/phone number entered does not exist"
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
                'message' => 'Form validation errors',
                'validation_errors' => $post,
            );
        }
        if($message){
           $this->session->set_flashdata('success',$message); 
        }
        echo json_encode($response);
       
       
    }

    function confirm_code(){
        $response = array();
        $message ='';
        if($_POST){
            foreach ($_POST as $key => $value) {
                $_POST[$key] = $this->cryptojs->cryptoJsAesDecrypt($this->session->userdata("pass_key"),$value);
            }
        }
        $this->form_validation->set_rules('identity', 'Email / Phone Number', 'required|callback__valid_identity');
        $this->form_validation->set_rules('code', 'Confirmation Code', 'required|numeric');
        if($this->form_validation->run()){
            $confirmation_code = $this->input->post('code');
            $identity = $this->input->post('identity');
            $forgot_password_code = $this->ion_auth->confirm_code($identity,$confirmation_code);
            if($forgot_password_code){
                $response = array(
                    'status' => 1,
                    'refer' => site_url('reset_password?code='.$forgot_password_code),
                    'message' => translate('Confirm code Successfully verified. Set new Password'),
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => translate($this->ion_auth->errors()),
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
                    'message' => translate('Form validation errors'),
                    'validation_errors' => $post,
                );
        }
        echo json_encode($response);
    }

    function reset_password($code=0){
        if($_POST){
            foreach ($_POST as $key => $value) {
                $_POST[$key] = $this->cryptojs->cryptoJsAesDecrypt($this->session->userdata("pass_key"),$value);
            }
        }
        $code = $this->input->post_get('code')?:$code;
        $user = $this->ion_auth->forgotten_password_check($code);
        $response = array();
        $message ='';
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]|callback__check_password_strength');
        if($this->form_validation->run()){
            $password = $this->input->post('password');
            $object = $this->ion_auth->forgotten_password_complete($code,$password);
            if($object){
                $object = (object)$object;
                if($this->ion_auth->reset_password($object->identity,$password)){
                    $this->ion_auth->clear_forgotten_password_code($code);
                    $user_input = array(
                        'password_check'=>$this->ion_auth->hash_password($password,'','',1),
                        'first_time_login_status'=>0,
                        'modified_on'=>time(),
                        'modified_by'=>$user->id,
                    );
                    $old_password_input = [
                        'user_id' =>$user->id,
                        'password' =>$user->password_check,
                        'changed_on' =>time(),
                        'is_change_success' =>1,
                    ];
                    $this->users_m->insert_password_reset_history($old_password_input);
                    $this->users_m->update_user($user->id,$user_input);
                    if($this->ion_auth->login($object->identity,$object->new_password,1)){
                        /*$response = array(
                            'status' => 1,
                            'refer' => site_url('authentication'),
                            'message' => 'Your password was reset successfully.',
                        );*/
                        $refer = $this->input->post('refer');
                        $confirmation_code = rand(100000,999999);
                        $otp_expiry_time = strtotime("+".$this->expiry_time,time());
                        $identity = $user->phone; 
                        if(valid_phone($identity) && in_array($identity, $this->exempt_otp)){
                            $input = array(
                                'otp_expiry_time'=>$otp_expiry_time,
                                'login_validated' => 1,
                                'modified_on' => time(),
                                'confirmation_code'=>$confirmation_code,
                            );
                        }else{
                            $input = array(
                                'otp_expiry_time'=>$otp_expiry_time,
                                'login_validated' => $this->ion_auth->is_admin()?1:0,
                                'modified_on' => time(),
                                'confirmation_code'=>$confirmation_code,
                            );
                        }
                        if($this->users_m->update_user($user->id,$input)){
                            $otp_array = array(
                                'pin'=>$confirmation_code,
                                'phone'=>$user->phone,
                                'email'=>$user->email,
                                'first_name'=>ucwords($user->first_name),
                                'last_name'=>ucwords($user->last_name),
                                'user_id' => $user->id,
                                'language_id' => $user->language_id,
                                'expiry_time' => $this->expiry_time,
                            );
                            if(!$this->ion_auth->is_admin()){
                                if(!in_array($identity, $this->exempt_otp)){
                                    $sent_otp = $this->messaging->send_user_otp($otp_array);
                                }                                
                            }
                            if(filter_var($refer, FILTER_VALIDATE_URL)){
                            }else{
                                $refer = site_url('checkin');
                            }
                            $response = array(
                                'status'=>1,
                                'refer'=>$refer,
                                'message' => strip_tags($this->ion_auth->messages()),
                            );
                        }else{
                            $response = array(
                                'status'=>0,
                                'message'=>strip_tags($this->ion_auth->errors()),
                            );
                        }


                    }else{
                        $response = array(
                            'status' => 1,
                            'refer' => site_url('login'),
                            'message' => 'Your password was reset successfully.',
                        );
                    }  
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => $this->ion_auth->errors(),
                    );
                }
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

    public function _check_password_strength() {
        $password = $this->input->post('password');
        if(containsDigits($password)) {
            if (containsMixedCase($password)) {
                // if(preg_match('@[^\w]@', $password)){
                    $code = $this->input->post_get('code');
                    if($code){
                        $user = $this->ion_auth->forgotten_password_check($code); 
                   }else{
                        $user = $this->user;
                   }                    
                    $salt = $this->config->item('store_salt', 'ion_auth') ? $this->config->item('store_salt', 'ion_auth') : FALSE;
                    $new_password = $this->ion_auth->hash_password($password);
                    if(!$user){
                        return TRUE;
                    }
                    $password_check = $user->password_check?$user->password_check:'';
                    /*echo $this->ion_auth->hash_password($password,'','',1);
                    echo "<br>";
                    echo $password_check; die();*/
                    if($this->ion_auth->hash_password($password,'','',1) == $password_check){
                        $this->form_validation->set_message('_check_password_strength',translate('The new password must not be the same as the old password!'));
                        return FALSE;
                    }else{
                        $rest_password_options = $this->users_m->get_password_reset_user_pairings($user->id);                        
                        if(empty($rest_password_options)){
                            return TRUE;
                        }else{
                            if(array_key_exists($this->ion_auth->hash_password($password,'','',1), $rest_password_options)){
                                $this->form_validation->set_message('_check_password_strength',translate('Looks like you already have used that password before, kindly generate a new password'));
                                return FALSE;
                            }else{
                                return TRUE;
                            }
                        }
                    }
                // }else{
                //     $this->form_validation->set_message('_check_password_strength','Password must include at least one special character!');
                //     return FALSE;  
                // }                
            }else{
                $this->form_validation->set_message('_check_password_strength',translate('Password must be a mixture of upper and lower case!'));
                return FALSE;  
            }
        }else{
            $this->form_validation->set_message('_check_password_strength',translate('Password must include at least one number!'));
            return FALSE;
        }

            

        return ($errors == $errors_init);
    }

    function new_password($code=0){
        if($_POST){
            foreach ($_POST as $key => $value) {
                $_POST[$key] = $this->cryptojs->cryptoJsAesDecrypt($this->session->userdata("pass_key"),$value);
            }
        }
        $code = $this->input->post_get('code')?:$code; 
        $user = $this->ion_auth->forgotten_password_check($code);
        $response = array();
        $message ='';
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
        if($this->form_validation->run()){
            $password = $this->input->post('password');
            $object = $this->ion_auth->forgotten_password_complete($code,$password);
            if($object){
                $object = (object)$object;
                if($this->ion_auth->reset_password($object->identity,$password)){
                    $this->ion_auth->clear_forgotten_password_code($code);
                    $user_input = array(
                        'first_time_login_status'=>0,
                        'modified_on'=>time(),
                        'modified_by'=>$this->user->id,
                    );
                    $this->users_m->update_user($this->user->id,$user_input);
                    if($this->ion_auth->login($object->identity,$object->new_password,1)){
                        $response = array(
                            'status' => 1,
                            'refer' => site_url('checkin'),
                            'message' => 'Your password was set successfully.',
                        );
                    }else{
                        $response = array(
                            'status' => 1,
                            'refer' => site_url('login'),
                            'message' => 'Your password was set successfully.',
                        );
                    }  
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => $this->ion_auth->errors(),
                    );
                }
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

    function create_group(){
        if($this->application_settings->allow_self_onboarding){
            $response = array();
            $this->_register_group_rules();
            $this->form_validation->set_rules($this->create_group_rules);
            if($this->form_validation->run()){
                $activate_group = $this->application_settings->sms_delivery_enabled?FALSE:TRUE;
                $partner_slug = "";
                $additional_data = array(
                    'group_type' => $this->input->post('group_type'),
                    'group_is_registered' => strtoupper($this->input->post('group_is_registered')),
                    'group_registration_certificate_number' => $this->input->post('group_registration_certificate_number'),
                    'country_id'  => $this->input->post('country_id')?:$this->default_country->id,
                    'currency_id' => $this->input->post('currency_id')?:$this->default_country->id,
                );
                $group_id = $this->investment_groups->create_group(
                    $this->user->id,
                    ucwords(strip_tags($this->input->post('group_name'))),
                    $this->input->post('group_size'),
                    $this->input->post('referrer_id'),
                    $this->input->post('referrer_information'),
                    $activate_group,
                    $partner_slug,
                    $this->input->post('group_role_key'),
                    $additional_data,
                    $this->input->post('banker')?FALSE:TRUE
                );
                if($group_id){
                    // die(''.$group_id);
                    // $group = $this->groups_m->get($group_id);
                    $this->session->set_userdata('group_id',''.$group_id);

                    $refer = "";

                    if(preg_match('/app\./',$this->application_settings->url)){
                        if($this->input->post('banker')){
                            $refer = $this->application_settings->protocol.'app.'.$this->application_settings->url.'/bank/setup_tasks/group_setup/'.$group_id;
                        }else{
                            $refer = $this->application_settings->protocol.'app.'.$this->application_settings->url.'/group/setup_tasks';
                        }
                    }else{
                        if($this->input->post('banker')){
                            $refer = $this->application_settings->protocol.''.$this->application_settings->url.'/bank/setup_tasks/group_setup/'.$group_id;
                        }else{
                            $refer = $this->application_settings->protocol.''.$this->application_settings->url.'/group/setup_tasks';
                        }
                    }

                    $response = array(
                        'status' => 1,
                        'message' => 'Group account created successfully',
                        'refer' => $refer,
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => $this->session->flashdata('error'),
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
                'status' => 0,
                'message' => 'Self Group Registration is currently disabled.'
            );
        }
        echo json_encode($response);
    }

    function update_group_setup_position(){
        if($this->input->post('group_id') && $this->input->post('group_setup_position')){
            $this->groups_m->update($this->input->post('group_id'),array(
                'group_setup_position' => $this->input->post('group_setup_position'),
                'modified_on' => time(),
                'modified_by' => $this->user->id,
            ));
            echo 'done';
        }
    }

    function update_group_details(){
        $allow_members_request_loan = $this->input->post('allow_members_request_loan');
        $group_setup_position = $this->input->post('group_setup_position')?:$this->group->group_setup_position;
        $disable_member_directory = $this->input->post('disable_member_directory');
        $enable_member_information_privacy = $this->input->post('enable_member_information_privacy');
        $this->groups_m->update($this->group->id,array(
            'group_setup_position' => $group_setup_position,
            'allow_members_request_loan' => $allow_members_request_loan,
            'disable_member_directory' => $disable_member_directory,
            'enable_member_information_privacy' => $enable_member_information_privacy,
            'modified_on' => time(),
            'modified_by' => $this->user->id,
        ));
        echo 'done';
    }

    protected $completed_setup_validation = array(
        array(
            'field'     =>  'accept_complete_setup', 
            'label'     =>  'Accept terms of use', 
            'rules'     =>  'required|trim|numeric|xss_clean'
        ),
        array(
            'field'     =>  'enable_referral_code', 
            'label'     =>  'Enable Refferal code', 
            'rules'     =>  'numeric|xss_clean'
        ),
        array(
            'field'     =>  'referral_code', 
            'label'     =>  'Refferal Code', 
            'rules'     =>  'trim|xss_clean|callback__valid_referral_code'
        ),
        array(
            'field'     =>  'admin_member_id', 
            'label'     =>  'Set Admin Member', 
            'rules'     =>  'required|trim|xss_clean|callback__check_admin_member_id'
        ),

    );

    function _valid_referral_code(){
        $enable_referral_code = $this->input->post('enable_referral_code');
        $referral_code = $this->input->post('referral_code');
        if($enable_referral_code  == 1){
            if($referral_code){
                return TRUE;
            }else{
                $this->form_validation->set_message('_valid_referral_code','Partner Refferal code is required');
                return FALSE;
            }

        }
    }

    function complete_setup(){
        $response = array();
        $this->form_validation->set_rules($this->completed_setup_validation);
        if($this->form_validation->run()){
            $accept_complete_setup = $this->input->post('accept_complete_setup');
            $banker = $this->input->post('banker');
            $admin_member_id = $this->input->post('admin_member_id');
            $user = $this->ion_auth->get_user($admin_member_id);
            $referral_code = $this->input->post('referral_code');
            if($admin_member_id && $user){
                $enable_referral_code = $this->input->post('enable_referral_code');
                if($this->group->group_setup_status==1){
                    $this->groups_m->update($this->group->id,array(
                        'modified_on' => time(),
                        'owner' => $user->id,
                        'modified_by' => $this->user->id,
                    ));
                    $response = array(
                        'status' => 0,
                        'message' => 'Group already completed setup',
                        'refer' => site_url('group'),
                    );
                }else{
                    $err_message = '';
                    $active_members = $this->members_m->count_active_group_members($this->group->id);
                    $err = FALSE;
                    if($active_members<3){
                        $error_message = "Ensure the group has 3 or more members";
                        $err = TRUE;
                    }
                    $contribution_settings = $this->contributions_m->count_group_contributions();
                    if($contribution_settings<1){
                        $error_message = "You must set atleast one contribution setting before proceeding";
                        $err = TRUE;
                    }
                    // $connected_bank_accounts = $this->bank_accounts_m->get_group_verified_partner_bank_account_options_ids($this->group->id);
                    // if(count($connected_bank_accounts)<1){
                    //     $error_message = "Ensure you have a connected equity bank account before you complete setup";
                    //     $err = TRUE;
                    // }
                    if($err){
                        $response = array(
                            'status' => 0,
                            'message' => $error_message,
                        );
                    }else{
                        if($this->groups_m->update($this->group->id,array(
                            'group_setup_status' => 1,
                            'referral_code'=>$referral_code,
                            'modified_on' => time(),
                            'owner' => $user->id,
                            'modified_by' => $this->user->id,
                        ))){ 
                            if($enable_referral_code){
                                $active_members = $this->members_m->count_active_group_members($this->group->id); 
                            }
                            if(!$this->group->bulk_invitation_sent){
                                if($this->messaging->send_member_first_time_login_invitation_message($this->group,1,$this->user)){
                                    $this->groups_m->update($this->group->id,array(
                                        'modified_on' => time(),
                                        'modified_by' => $this->user->id,
                                        'bulk_invitation_sent' => 1,
                                    ));
                                }
                            }
                            $response = array(
                                'status' => 1,
                                'message' => 'Setup successfully completed. Proceed to Dashboard',
                                'refer' => $banker?site_url('bank'):site_url('group'),
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Error occured submitting your request. Try again or refresh the page and submit',
                            );
                        }
                    }
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Sorry. Kindly choose a user who will be the group administrator among the members',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'There are some errors on the form. Please review and try again.',
                'validation_errors' => $this->form_validation->error_array(),
            );
        }
        echo json_encode($response);
    }
}