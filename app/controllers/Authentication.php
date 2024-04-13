<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Authentication extends Authentication_Controller{
    protected $signup_rules = array(
        array(
            'field' =>  'first_name',
            'label' =>  'First Name',
            'rules' =>  'required|trim|callback__first_name_has_illegal_characters',
        ),
        array(
            'field' =>  'last_name',
            'label' =>  'Last Name',
            'rules' =>  'required|trim|callback__last_name_has_illegal_characters',
        ),
        array(
            'field' =>  'identity',
            'label' =>  'Phone Number / Email Address',
            'rules' =>  'trim|required|callback__valid_identity',
        ),
        array(
            'field' =>  'password',
            'label' =>  'Password',
            'rules' =>  'required|trim|min_length[8]',
        ),
        array(
            'field' =>  'group_name',
            'label' =>  'Group Name',
            'rules' =>  'required|trim|callback__group_name_has_illegal_characters',
        ),  
        array(
            'field' =>  'group_size',
            'label' =>  'Group Size',
            'rules' =>  'required|trim|numeric|min_length[1]|max_length[10000]',
        ),  
        array(
            'field' =>  'referrer_id',
            'label' =>  'Referrer',
            'rules' =>  'trim|numeric',
        ),  
        array(
            'field' =>  'referrer_information',
            'label' =>  'Referrer Information',
            'rules' =>  'trim',
        ), 
        array(
            'field' =>  'language_id',
            'label' =>  'Language',
            'rules' =>  'trim|numeric',
        ),
    );

    function __construct()
    {
        parent::__construct();
        $this->load->model('groups/groups_m');
        $this->load->model('banks/banks_m');
        $this->load->model('users/users_m');
        $this->load->model('loans/loans_m');
        $this->load->model('withdrawals/withdrawals_m');
        $this->load->model('partners/partners_m');
        $this->load->model('deposits/deposits_m');
        $this->load->model('referrers/referrers_m');
        $this->load->library('investment_groups');
        $this->load->library('group_members');
    }

    function index(){
        $groups = $this->investment_groups->current_user_groups($this->user->id);
        $groups_managed = $this->investment_groups->current_user_groups_managed($this->user->id);
        $count = count($groups);
        $group_id = $this->session->userdata('group_id');
        if($group_id){

        }else if($count==1&&!$this->ion_auth->is_admin()&&!$this->ion_auth->is_bank_admin()){
            //if user only has one group and is not an admin
            foreach($groups as $group){
                $group_id = $group->id;
                break;
            }

        }else if($this->ion_auth->is_admin()&&$count>0){
        }
        if($group_id){
            $this->login_to_group($group_id);
        }else{
            redirect('checkin');
        }
    }

    function verify_otp(){
        if($this->application_settings->enable_two_factor_auth){
            $this->template->set_layout('authentication.html')->title('Verify Otp')->build('authentication/verify_otp');
        }else{
            redirect('checkin');
        }
    }

    public function new_password(){
        $code = $this->input->post_get('code');
        $this->template->set_layout('authentication.html')->title('new_password')->build('authentication/new_password');
    }

    function checkin(){
        $data = array();
        // if($this->user->is_validated == 2){
        //     redirect('verify_otp','refresh');
        // }else{
            $data['groups_managed'] = $this->investment_groups->current_user_groups_managed($this->user->id);
            $groups = $this->investment_groups->current_user_groups($this->user->id);
            $data['groups'] = $groups;
            $data['group_role_options_array'] = $this->group_roles_m->get_group_role_options_array($groups);
            $data['partner_banks'] = $this->banks_m->get_partner_banks();
            $data['partner_accounts'] = $this->partners_m->get_accounts_managed_by_user($this->user->id);
            $this->template->set_layout('authentication.html')->title('Check into '.$this->application_settings->application_name)->build('authentication/checkin',$data);
        //}
    }
    
    function demo_login(){
        //remove_subdomain_from_url($this->application_settings->url,$this->application_settings->protocol);
        if($this->ion_auth->logged_in()){ 
            redirect('authentication');
        }
        $this->data['refer'] = $this->input->get_post('refer');

        $slug = '';
        $url_segments = explode(".",$_SERVER['HTTP_HOST']);
        $url_segments_dots = count($url_segments);

        $title = 'Login into '.$this->application_settings->application_name;
        $avatar = '';

        $demo_email = 'innovations@chamasoft.com';
        $demo_phone = '+2547398712777';
        $demo_password = 'innovations1234';

        $this->data['title'] = $title;
        $this->data['demo_email'] = $demo_email;
        $this->data['demo_phone'] = $demo_phone;
        $this->data['demo_password'] = $demo_password;
        $this->data['avatar'] = '';
        $this->template->set_layout('authentication.html')->title('Online Demo Instructions')->build('authentication/demo_login',$this->data);
    }

    function _valid_identity(){
        $identity = $this->input->post('identity');
        if(!valid_email($identity)){
            if(!valid_phone($identity)){
                $this->form_validation->set_message('_valid_identity','Enter a valid Email or Phone Number');
                return FALSE;
            }
            return TRUE;
        }else{
            return TRUE;
        }
    }

    function _first_name_has_illegal_characters()
    {
        $first_name = $this->input->post('first_name');
        if(is_character_allowed($first_name)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_first_name_has_illegal_characters','You have entered illegal characters in the First Name field, avoid using the following: % $ - - & * ? < > ');
            return FALSE;
        }
    }

    function _last_name_has_illegal_characters()
    {
        $last_name = $this->input->post('last_name');
        if(is_character_allowed($last_name)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_last_name_has_illegal_characters','You have entered illegal characters in the Last Name field, avoid using the following: % $ - - & * ? < > ');
            return FALSE;
        }
    }

    function _group_name_has_illegal_characters()
    {
        $group_name = $this->input->post('group_name');
        if(is_character_allowed($group_name)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_group_name_has_illegal_characters','You have entered illegal characters in the Group Name field, avoid using the following: % $ - - & * ? < > ');
            return FALSE;
        }
    }

    public function self_member_registration($group = 0){
        // get the group id
        $group_id = $this->input->get('group');
        // get the group name,
        $group = $this->groups_m->get($group_id);
        $this->data['group_name'] = $group->name;
        // send it to the form
        $this->template->set_layout('authentication.html')->title('Self Member Registration')->build('authentication/self_member_registration',$this->data);
    }


    public function login(){
        //remove_subdomain_from_url($this->application_settings->url,$this->application_settings->protocol);
        if($this->ion_auth->logged_in()){ 
            redirect('logout');
        }
        $em = $this->input->get('l');
        $action = $this->input->post('action');
        //validate form input
        $this->form_validation->set_rules('identity', 'Email / Phone Number', 'required|trim|callback__valid_identity');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');
        $messages = '';
        $this->session->set_userdata('pass_key',random_string('alnum', 32));
        if($this->form_validation->run() == true){

            //check to see if the user is logging in
            //check for "remember me"
            $identity = $this->input->post('identity');
            $remember = false;
            if($this->ion_auth->login($identity, $this->input->post('password'), $remember)){ 
                $refer = $this->input->post('refer');
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                //redirect them to their default chama
                $this->user = $this->ion_auth->get_user();
                $user_id = $this->user->id;
                if($action=='signup'){
                    $this->session->set_flashdata('message', $this->ion_auth->messages().'You can now continue Signing Up for Chamasoft.');
                    redirect('signup?action=create_group_account');
                }else if($refer){
                    redirect($refer);
                }else if($this->user->prompt_to_change_password == 1){
                    redirect('change_password');
                }else{
                    redirect('authentication');
                }
            }else{
                $i_exist_in_me = $this->users_m->check_if_identity_exists($identity);
                if(valid_email($identity) && !$i_exist_in_me && preg_match('/(chamasoft\.com)/',$_SERVER['HTTP_HOST'])){
                    $url = 'https://www.chamasoft.co.ke/member_groups/user_exists_in_me/';
                    $data = array('email'=>$identity);
                    $ch = curl_init();  
                    curl_setopt($ch,CURLOPT_URL,$url);
                    curl_setopt($ch,CURLOPT_POST,true );
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
                    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
                    curl_setopt($ch,CURLOPT_POSTFIELDS,$data);    
                    $output = curl_exec($ch);
                    curl_close($ch);
                    $result = json_decode($output);
                    if(!$result){
                        $this->session->set_flashdata('error', 'You are not registered. Kindly fill in the form below to signup'); 
                        redirect('signup','refresh');
                    }else{
                        $group = (object)$result;
                        header('Location: http://'.$group->slug.'.chamasoft.co.ke/chama/login');
                        exit;
                    }
                }else{
                    $this->session->set_flashdata('error', $this->ion_auth->errors()); 
                }
                $this->session->set_flashdata('error',$this->ion_auth->errors());
                $this->session->set_flashdata('name',$identity);
                redirect('login','redirect'); //use redirects instead of loading views for compatibility with MY_Controller libraries
            }
        }else{  
            //the user is not logging in so display the login page
            //set the flash data error message if there is one
            $this->data['messages'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            $this->data['identity'] = array('name' => 'identity',
                'id' => 'identity',
                'type' => 'text',
                'value' => $this->form_validation->set_value('identity'),
            );
            $this->data['password'] = array('name' => 'password',
                'id' => 'password',
                'type' => 'password',
            );
        }        
        $this->data['refer'] = $this->input->get_post('refer');
        $slug = '';
        $url_segments = explode(".",$_SERVER['HTTP_HOST']);
        $url_segments_dots = count($url_segments);
        if($url_segments_dots>2){
            $slug = $url_segments[0];
            $group = $this->groups_m->get_by_slug($slug);
        }else{
            $group = '';
        }
        if($group){
            $title = 'Login into '.$group->name;
            $avatar = $group->avatar;
        }else{
            $title = 'Login into '.$this->application_settings->application_name;
            $avatar = '';
        }
        $demo_email = 'innovations@chamasoft.com';
        $demo_phone = '+2547398712777';
        $demo_password = 'innovations1234';

        $this->data['title'] = $title;
        $this->data['phone'] = '';
        $this->data['password'] = '';
        if($this->data['refer'] == '/demo'){

            $this->data['phone'] = $demo_phone;
             $this->data['password'] = $demo_password;
           
            }
        $this->data['avatar'] = $avatar;
        $this->template->set_layout('authentication.html')->title($title)->build('authentication/login',$this->data);
    }

    function forgot_password(){
        if($this->ion_auth->logged_in())
        {      
            redirect('authentication');
        }

        $this->form_validation->set_rules('identity', 'Email / Phone Number', 'required|callback__valid_identity');
        if($this->form_validation->run()){
            $identity = $this->input->post('identity');
            $forgotten = $this->ion_auth_model->forgotten_password($identity,true);
            if($forgotten){ 
                //if there were no errors
                $forgotten = (object)$forgotten;
                if(valid_email($identity)){
                    $this->session->set_flashdata('success','Password recovery mail sent to <strong>'.$identity.'</strong>, please check your email inbox, if you don\'t find it there check your spam or junk folder.');
                    redirect('login','refresh');
                }else{
                    $this->session->set_flashdata('success', "Confirmation code sent to ".$identity);
                    redirect("confirm_code?identity=".$identity, 'refresh'); //we should display a confirmation page here instead of the login page
                }
            }else{
                $this->session->set_flashdata('error', $this->ion_auth->errors().' We could not find any account identified by <strong>'.$identity.'</strong>');
                redirect("forgot_password", 'refresh');
            }
        }else{
            $this->data['messages'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            $this->data['identity'] = array('name' => 'identity',
                'id' => 'identity',
                'type' => 'text',
                'value' => $this->form_validation->set_value('identity'),
            );
        }
        $this->template->set_layout('authentication.html')->title('Forgot Password')->build('authentication/forgot_password');
    }

    function confirm_code()
    {
        if($this->ion_auth->logged_in())
        {      
            redirect('authentication');
        }

        $this->form_validation->set_rules('identity', 'Email / Phone Number', 'required|callback__valid_identity');
        $this->form_validation->set_rules('confirmation_code', 'Confirmation Code', 'required|numeric');
        if($this->form_validation->run())
        {
            $this->load->library('ion_auth');
            $confirmation_code = $this->input->post('confirmation_code');
            $identity = $this->input->post('identity');
            $forgot_password_code = $this->ion_auth->confirm_code($identity,$confirmation_code);

            if($forgot_password_code)
            {
                $this->session->set_flashdata('success',' Confirm code Successfully verified. Set new Password');
                redirect('reset_password?code='.$forgot_password_code,'refresh');
            }
            else
            {
                $this->session->set_flashdata('error', $this->ion_auth->errors());
                redirect("confirm_code", 'refresh');
            }

        }
        else
        {
            $this->data['messages'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            $this->data['identity'] = array('name' => 'identity',
                'id' => 'identity',
                'type' => 'text',
                'value' => $this->form_validation->set_value('identity'),
            );
            $this->data['confirmation_code'] = array('name' => 'confirmation_code',
                'id' => 'confirmation_code',
                'type' => 'text',
                'value' => $this->form_validation->set_value('confirmation_code'),
            );
        }
        $this->template->set_layout('authentication.html')->title('Confirm Code')->build('authentication/confirm_code');
    }

    public function reset_password(){
        if($this->ion_auth->logged_in()){      
            redirect('authentication');
        }
        $code = $this->input->post_get('code');
        $user = $this->ion_auth->forgotten_password_check($code);
        if($user){
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
            if($this->form_validation->run()){
                $password = $this->input->post('password');
                $object = $this->ion_auth->forgotten_password_complete($code,$password);
                if($object){
                    $object = (object)$object;
                    if($this->ion_auth->reset_password($object->identity,$password)){
                        $this->ion_auth->clear_forgotten_password_code($code);
                        if($this->ion_auth->login($object->identity,$object->new_password,1)){
                            $this->session->set_flashdata('success', $this->ion_auth->messages());
                            redirect('authentication');
                        }else{
                            $this->session->set_flashdata('error', $this->ion_auth->errors());
                            redirect('login');
                        }  
                    }else{
                        $this->session->set_flashdata('error', $this->ion_auth->errors());
                        redirect('login','refresh');
                    }
                }else{
                    $this->session->set_flashdata('error', $this->ion_auth->errors());
                    redirect('login','refresh');
                }
            }else{
                $this->data['messages'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
                $this->data['password'] = array('name' => 'password',
                    'id' => 'password',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('password'),
                );
                $this->data['conf_password'] = array('name' => 'conf_password',
                    'id' => 'conf_password',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('conf_password'),
                );
           }
           $data['user'] = $user;
           $this->template->set_layout('authentication.html')->title('Reset Password')->build('authentication/reset_password',$data);
       }else{
            $this->session->set_flashdata('error', 'sorry the code does not exist');
            redirect("forgot_password", 'refresh');
            return false;
       }
    }

    public function change_password(){
        if(!$this->ion_auth->logged_in()){      
            redirect('authentication');
        }
        $user = $this->user;
        $this->form_validation->set_rules('current_password', 'Current Password', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
        if($this->form_validation->run()){
            $password = $this->input->post('password');
            $current_password = $this->input->post('current_password');
            if($this->ion_auth->change_password($user->phone,$current_password,$password)){
                $this->ion_auth->update($user->id,array('prompt_to_change_password' => 0));
                $this->user = $this->ion_auth->get_user();
                $this->session->set_flashdata('info','Please use the new password to login');
                $this->session->set_flashdata('success','PAssword Change Successful');
                redirect('logout');  
            }else{
                $this->session->set_flashdata('error',$this->ion_auth->errors());
                redirect('change_password');
            }
            
        }else{
            $this->data['messages'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            $this->data['password'] = array('name' => 'password',
                'id' => 'password',
                'type' => 'text',
                'value' => $this->form_validation->set_value('password'),
            );
            $this->data['conf_password'] = array('name' => 'conf_password',
                'id' => 'conf_password',
                'type' => 'text',
                'value' => $this->form_validation->set_value('conf_password'),
            );
       }
       $data['user'] = $user;
       $this->template->set_layout('authentication.html')->title('Change Password')->build('authentication/change_password',$data);
       
    }

    public function set_new_password(){
        $this->template->set_layout('authentication.html')->title('Change Password')->build('authentication/set_new_password'); 
    }

    function signup($partner_slug = ""){
        //remove_subdomain_from_url($this->application_settings->url,$this->application_settings->protocol);
        $post = new stdClass();
        if($this->input->post('referrer_information_required')){
            $this->signup_rules[] = array(
                'field' =>  'referrer_information',
                'label' =>  'Referrer Information',
                'rules' =>  'trim|required',
            );
        }

        $this->form_validation->set_rules($this->signup_rules);
        $action = $this->input->get('action');
        if($this->ion_auth->logged_in()){  
            if($action=='create_group_account'){
                $group = $this->session->userdata('group');
                $data['group_name'] = $group['group_name'];
                $data['group_size'] = $group['group_size'];
                $this->form_validation->set_rules('password','Password','trim');
            }else{
                redirect('authentication');
            }
        }else{
            if($action=='create_group_account'){
                redirect('login');
            }else{
                //do nothing
                $data['group_name'] = '';
                $data['group_size'] = '';
            }
        };

        if($this->form_validation->run()){
            $identity = strtolower($this->input->post('identity'));
            $password = $this->input->post('password');
            if(valid_phone($identity)){
                $identity = valid_phone($identity);
            }
            if($this->ion_auth->logged_in()){
                if($group_id = $this->investment_groups->create_group($this->user->id,$this->input->post('group_name'),$this->input->post('group_size'),"","",TRUE,$partner_slug)){
                    $this->session->set_flashdata('success','Group account created successfully');
                    redirect('authentication?group_id='.$group_id,'refresh');
                }else{
                    $this->session->set_flashdata('error','Something went wrong during group creation');
                    redirect('signup?action=create_group_account','refresh');
                }
            }else if($this->ion_auth->identity_check($identity)){
                $this->session->set_flashdata('info','We have detected you already have an account with us registered under '.$identity.', please login to proceed with your group sign up. ');
                $group_data = array(
                    'group_name' => $this->input->post('group_name'),
                    'group_size' => $this->input->post('group_size'),
                );
                $this->session->set_userdata("group",$group_data);
                redirect('login?identity='.$identity.'&action=signup','refresh');    
            }else{
                if(is_character_allowed($this->input->post('first_name'))==FALSE || is_character_allowed($this->input->post('last_name'))==FALSE || $this->input->post('group_size')>100000 || is_character_allowed($this->input->post('group_name'))==FALSE || preg_match('/mailinator/',$identity)){
                    echo '';
                    redirect('');
                    exit;
                }
                $first_name = ucwords(strtolower(strip_tags($this->input->post('first_name'))));
                $last_name =  ucwords(strtolower(strip_tags($this->input->post('last_name'))));
                $language_id = $this->input->post('language_id');
                $group_id = $this->ion_auth->get_group_by_name('member');
                if($group_id){
                    $groups = array($group_id->id);
                }else{
                    $groups = array(2);
                }
                $additional_data = array(
                    'created_on'=>time(),
                    'active'=>1, 
                    'ussd_pin'=>rand(1000,9999),
                    'first_name'=>ucfirst($first_name), 
                    'last_name'=>ucfirst($last_name), 
                    'language_id'=>$language_id, 
                    'is_validated'=>0,
                );
                //redefine identity.....
                if(valid_phone($this->input->post('identity'))){
                    //get countries prefix code
                    $identity = valid_phone($identity,'',TRUE);
                }
                $user_id = $this->ion_auth->register($identity,$password,'',$additional_data,$groups,TRUE);
                $activate_group = $this->application_settings->sms_delivery_enabled?FALSE:TRUE;
                if($group_id = $this->investment_groups->create_group($user_id,strip_tags($this->input->post('group_name')),$this->input->post('group_size'),$this->input->post('referrer_id'),$this->input->post('referrer_information'),$activate_group,$partner_slug)){
                    if($this->ion_auth->login($identity,$password,1)){

                        $this->session->set_flashdata('success','User,member and group account created successfully');
                        redirect('authentication?group_id='.$group_id,'refresh');
                        
                    }else{
                        $this->session->set_flashdata('error','Something went wrong while we were logging you in');
                        redirect('signup','refresh');
                    }
                }else{
                    $this->session->set_flashdata('error','Something went wrong during group creation');
                    redirect('signup','refresh');
                }
            }
        }else{
            //do nothing
        }

        $country_region = $this->investment_groups->get_group_country_region();
        if($country_region && isset($country_region->calling_code)){
            $data['country_help_block'] = 'Recommended phone format '.$country_region->calling_code.'xxxxxxxxx in '.$country_region->name;
        }else{
            $data['country_help_block'] = '';
        }
        $data['action'] = $action;
        $data['language_options'] = $this->languages_m->get_language_options();
        $data['referrers'] = $this->referrers_m->get_all_active();
        $data['referrer_options'] = $this->referrers_m->get_options();
        $this->template->set_layout('authentication.html')->title('Sign Up for '.$this->application_settings->application_name)->build('authentication/signup',$data);
    }

    public function logout()
    {
        $cookie_prefix = '';
        $CookieInfo = session_get_cookie_params();
        $cookie_domain = isset($CookieInfo['domain'])?$CookieInfo['domain']:'';
        $cookie_path = isset($CookieInfo['path'])?$CookieInfo['path']:'';
        delete_cookie('ci_session', $cookie_domain, $cookie_path, $cookie_prefix);
        unset($_COOKIE);
        $this->ion_auth->logout();
        $this->session->set_flashdata('success', 'You have Successfully Logged Out');
        //redirect them back to the page they came from
        // if(preg_match('/app\./',$this->application_settings->url)){
        //    $url = $this->application_settings->protocol.$this->application_settings->url.'/login';
        // }else{
        //    $url = $this->application_settings->protocol.'app.'.$this->application_settings->url.'/login'; 
        // }
        $url = $this->application_settings->protocol.$this->application_settings->url.'/login';
        $this->session->unset_userdata('application_settings');
        redirect($url,'refresh');
    }

    public function join($code=''){
        if($code){
            $user = $this->users_m->get_user_by_join_code($code);
            if($user){
                $join_rules = array(
                    array(
                            'field' =>  'password',
                            'label' =>  'Password',
                            'rules' =>  'required|trim|min_length[8]',
                        )
                );
                $data = array();
                $data['user'] = $user;
                $this->form_validation->set_rules($join_rules);
                if($this->form_validation->run()){
                    $password = $this->input->post('password');
                    $additional_data = array(
                        'password' => $password,
                        'join_code' => '',
                    );
                    if($this->ion_auth->update($user->id,$additional_data)){
                        if($this->ion_auth->login($user->phone,$password,1)){
                            $this->session->set_flashdata('success','You have successfully set your password on Chamasoft, use it during your subsequent logins.');
                            redirect('checkin','refresh');
                        }else{
                            $this->session->set_flashdata('error','Something went wrong while we were logging you in');
                        }
                    }
                }
                $this->template->set_layout('authentication.html')->title('Join Chamasoft')->build('authentication/join',$data);
            }else{
                redirect(site_url());
            }
        }else{
            redirect(site_url());
        }
    }

    function extend_session(){
        
    }

    function get_numbers($date){
        if($date){
            $date = strtotime($date);
        }else{
            $date =  time();
        }
        $from = $date;
        $to = time();
        $paying_group_ids = $this->billing_m->get_paying_group_id_array($from,$to);        
        $registered_users = $this->users_m->count_all_active_users();
        $registered_partners = $this->partners_m->count_all();
        $total_deposits = $this->deposits_m->get_total_deposits($date ,$paying_group_ids);
        //$number_of_transactions = $this->transactions_m
        $number_of_loans = $this->loans_m->count_all();
        $number_of_active_loans = $this->loans_m->count_all_active_loans();
        $total_amount_loans = $this->loans_m->get_total_amount_loans($date,$paying_group_ids);
        $total_withdrawals = $this->withdrawals_m->get_total_withdrawals_amount($date,$paying_group_ids);
        $user_countries = $this->groups_m->get_group_countries_no();
        $currency_option = $this->countries_m->get_currency_options();
        foreach ($user_countries as $key => $country_id) {
          $currency_code[] = $currency_option[$key];
           
        }
        print_r($total_withdrawals); die();
        print_r($get_total_deposits_by_month_array_tests); die();
        print_r($currency_code);
        print_r($user_countries); die();
        echo count($paying_group_ids ) .' Paying groups <br>';
        echo $registered_users .' registered users<br>';
        echo $registered_partners .' registered partners<br>';
        echo number_to_currency($total_deposits->amount) .' Total deposits <br>';
        echo $number_of_loans .' number of loans <br>';
        echo $number_of_active_loans .' number of active loans <br>';  
        echo number_to_currency($total_amount_loans->amount) .' Total amount of loans <br>';  
        echo number_to_currency($total_withdrawals->amount) .' Total amount withdrawals <br>';        
        echo $user_countries->country_id .' number of countries <br>';
    }

    function change_language($language_id = 0){
        if($language_id){
            if($this->ion_auth->change_language($language_id)){
                $this->session->set_flashdata('success',"Language Change Successful");
            }else{
                $this->session->set_flashdata('error',"Language Change Failed");
            }
            if($this->agent->referrer()){
                redirect($this->agent->referrer());
            }else{
                redirect('authentication');
            }
        }else{
            redirect('authentication');
        }
    }

    function create_group(){
        if($this->application_settings->allow_self_onboarding){
            $this->data['system_group_roles'] = $this->investment_groups->system_group_roles;
            $this->data['type_of_groups'] = $this->investment_groups->type_of_groups;
            $this->data['currencies'] = $this->countries_m->get_currency_options();
            $this->data['countries'] = $this->countries_m->get_country_options(1);
            $this->template->set_layout('setup_tasks.html')->title('Group Setup Tasks')->build('authentication/group_setup',$this->data);
        }else{
            redirect('checkin');
        }
    }

    function login_to_group($id=0,$group=''){
        $id OR redirect('checkin');
        $this->session->set_userdata('group_id',$id);
        $refer_session = $this->session->userdata('refer');
        if($refer_session && filter_var(site_url($refer_session), FILTER_VALIDATE_URL)){
            $this->session->unset_userdata('refer');
            $url = site_url($refer_session);
        }
        // if(preg_match('/app\./',$this->application_settings->url)){
        //    $url = $this->application_settings->protocol.$this->application_settings->url.'/group';
        // }else{
        //    $url = $this->application_settings->protocol.'app.'.$this->application_settings->url.'/group'; 
        // }
        $url = $this->application_settings->protocol.$this->application_settings->url.'/group';
        redirect($url,'refresh');
    }

}
