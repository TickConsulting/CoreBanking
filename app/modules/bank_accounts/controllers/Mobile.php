<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mobile extends Mobile_Controller{

    function __construct(){
        parent::__construct();
        $this->load->library('bank');
        $this->load->model('bank_accounts/bank_accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('bank_branches/bank_branches_m');
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
                'status'    =>  404,
                'message'       =>  '404 Method Not Found for URI: '.$this->uri->uri_string(),
            )

        ));
    }

    protected $validation_rules = array(
        array(
                'field' =>  'account_name',
                'label' =>  'Account Name',
                'rules' =>  'xss_clean|required|trim'
            ),
        array(
                'field' =>  'bank_id',
                'label' =>  'Bank Name',
                'rules' =>  'xss_clean|required|trim|numeric|callback__bank_exists'
            ),
        array(
                'field' =>  'bank_branch_id',
                'label' =>  'Bank Branch Name',
                'rules' =>  'xss_clean|required|trim|numeric|callback__bank_branch_exist'
            ),
        array(
                'field' =>  'account_number',
                'label' =>  'Account Number',
                'rules' =>  'xss_clean|required|trim|numeric|callback__is_unique_account|min_length[5]|max_length[20]'
            ),
        array(
                'field' =>  'initial_balance',
                'label' =>  'Initial Balance',
                'rules' =>  'xss_clean|trim|currency'
            ),
        array(
                'field' =>  'enable_email_transaction_alerts_to_members',
                'label' =>  'Enable Email Transaction Alerts to Members',
                'rules' =>  'xss_clean|trim|numeric|callback_check_email_selected_members'
            ),
        array(
                'field' =>  'enable_sms_transaction_alerts_to_members',
                'label' =>  'Enable SMS Transaction Alerts to Members',
                'rules' =>  'xss_clean|trim|numeric|callback_check_sms_selected_members'
            ),
        array(
                'field' =>  'is_verified',
                'label' =>  '',
                'rules' =>  ''
            ),
         array(
                'field' =>  'account_password',
                'label' =>  '',
                'rules' =>  'xss_clean'
            ),
    );

    function _bank_exists(){
        $bank_id = $this->input->post('bank_id');
        if($this->banks_m->get($bank_id)){
            return TRUE;
        }else{
            $this->session->set_flashdata('error','Bank does not exist. Select another bank and try again');
            return FALSE;
        }
    }

    function _bank_branch_exist(){
        $bank_id = $this->input->post('bank_id');
        $bank_branch_id = $this->input->post('bank_branch_id');
        if($this->bank_branches_m->get_by_bank($bank_branch_id,$bank_id)){
            return TRUE;
        }else{
            $this->session->set_flashdata('error','Bank does not exist. Select another bank and try again');
            return FALSE;
        }
    }

    function _is_unique_account(){
        $account_number = $this->input->post('account_number');
        $bank_id = $this->input->post('bank_id');
        $id = $this->input->post('id');
        $account_exists = $this->bank_accounts_m->check_if_account_exists($id,$account_number,$bank_id);
        if($account_exists){
            $this->form_validation->set_message('_is_unique_account','Sorry the account number '.'`'.$account_number.'`'.' is already registered and cannot allow duplicate');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    function check_email_selected_members(){
        if($this->input->post('enable_email_transaction_alerts_to_members')==1){
            $bank_account_email_transaction_alert_member_list = $this->input->post('bank_account_email_transaction_alert_member_list');
            $count = count($bank_account_email_transaction_alert_member_list);
            if($count>0){
                return TRUE;
            }else{
                $this->form_validation->set_message('check_email_selected_members', 'At least one member should be selected under enable email transaction alerts');
                return FALSE;
            }
        }else{
            return TRUE;
        }
    }

    function check_sms_selected_members(){
        if($this->input->post('enable_sms_transaction_alerts_to_members')==1){
            $bank_account_sms_transaction_alert_member_list = $this->input->post('bank_account_sms_transaction_alert_member_list');
            $count = count($bank_account_sms_transaction_alert_member_list);
            if($count>0){
                return TRUE;
            }else{
                $this->form_validation->set_message('check_sms_selected_members', 'At least one member should be selected under enable SMS transaction alerts');
                return FALSE;
            }
        }else{
            return TRUE;
        }
    }

    function create_otp(){
        $file = file_get_contents('php://input');
        $response = array();
        $request = array();
        header('Content-Type: application/json');
        $time = time();

        if($file){
            $result = json_decode($file);
            $account_number = $result->account_number;
            $phone_number = $result->phone_number;
            if($account_number&&$phone_number){
                $response = $this->bank->request_one_time_password(trim($account_number),$phone_number);
                print_r($response);
            }else{
                $response= array(
                    'time' => time(),
                    'status'    =>  "0",          
                    'error'     =>  "Parameters missing"
                );
                echo encrypt_json_encode($response);
            }
        }else{
            $response = array(
                'status' => "0",
                'time' => time(),
                'error' => 'No file sent',
            );
            echo encrypt_json_encode($response);
        }
    }

    function verify_otp(){
        $file = file_get_contents('php://input');
        $response = array();
        $request = array();
        header('Content-Type: application/json');
        $time = time();
        if($file){
            $result = json_decode($file);
            $account_number = $result->account_number;
            $phone_number = $result->phone_number;
            $verification_code = $result->verification_code;
            if($account_number&&$phone_number&&$verification_code){
                $response = $this->bank->verify_one_time_password(trim($account_number),$phone_number,$verification_code);
                print_r($response);
            }else{
                $response= array(
                    'time' => time(),
                    'status'    =>  "0",          
                    'error'     =>  "Parameters missing"
                );
                echo encrypt_json_encode($response);
            }
        }else{
             $response = array(
                'status' => "0",
                'time' => time(),
                'error' => 'No file sent',
            );
            echo encrypt_json_encode($response);
        }
    }

    function verify_account(){
        $file = file_get_contents('php://input');
        $response = array();
        $request = array();
        header('Content-Type: application/json');
        $time = time();
        if($file){
            $result = json_decode($file);
            $account_number = $result->account_number;
            $group_id = $result->group_id;
            if($account_number && $group_id){
                $id = $this->bank_accounts_m->get_group_bank_account_id_by_account_number($account_number,$group_id);
                if($id){
                    if($this->bank_accounts_m->update($id,array('is_verified'=>1,'modified_on'=>time()))){
                        $response= array(
                            'time' => '"'.time().'"',
                            'status'    =>  "1",          
                            'error'     =>  "Bank account connected successfully"
                        );
                    }else{
                        $response= array(
                            'time' => '"'.time().'"',
                            'status'    =>  "0",          
                            'error'     =>  "Could not connected bank account"
                        );
                    }
                    echo encrypt_json_encode($response);
                }else{
                    $response= array(
                        'time' => '"'.time().'"',
                        'status'    =>  "0",          
                        'error'     =>  "Could not find bank account"
                    );
                    echo encrypt_json_encode($response);
                }
            }else{

                $response= array(
                    'time' => '"'.time()."'",
                    'status'    =>  "0",          
                    'error'     =>  "Parameters missing"
                );
                echo encrypt_json_encode($response);
            }
        }else{
             $response = array(
                'status' => "0",
                'time' => '"'.time().'"',
                'error' => 'No file sent',
            );
            echo encrypt_json_encode($response);
        }
    }

    function disconnect_account(){ 
        $file = file_get_contents('php://input');
        $response = array();
        $request = array();
        header('Content-Type: application/json');
        $time = time();
        if($file){
            $result = json_decode($file);
            $account_number = $result->account_number;
            $group_id = $result->group_id;
            if($account_number && $group_id){
                $id = $this->bank_accounts_m->get_group_bank_account_id_by_account_number($account_number,$group_id);
                if($id){
                    if($this->bank_accounts_m->update($id,array('is_verified'=>0,'modified_on'=>time()))){
                        $response= array(
                            'time' => '"'.time().'"',
                            'status'    =>  "1",          
                            'error'     =>  "Bank account disconnected successfully"
                        );
                    }else{
                        $response= array(
                            'time' => '"'.time().'"',
                            'status'    =>  "0",          
                            'error'     =>  "Could not disconnect bank account"
                        );
                    }
                    echo encrypt_json_encode($response);
                }else{
                    $response= array(
                        'time' => '"'.time().'"',
                        'status'    =>  "0",          
                        'error'     =>  "Could not find bank account"
                    );
                    echo encrypt_json_encode($response);
                }
            }else{

                $response= array(
                    'time' => '"'.time()."'",
                    'status'    =>  "0",          
                    'error'     =>  "Parameters missing"
                );
                echo encrypt_json_encode($response);
            }
        }else{
             $response = array(
                'status' => "0",
                'time' => '"'.time().'"',
                'error' => 'No file sent',
            );
            echo encrypt_json_encode($response);
        }
    }

    function create(){
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
                    if($this->member->group_role_id || $this->member->is_admin){
                        $post = new stdClass();
                        $this->form_validation->set_rules($this->validation_rules);
                        if($this->form_validation->run()){
                            $id = $this->bank_accounts_m->insert(array(
                                'group_id'          =>  $this->group->id,
                                'account_number'    =>  $this->input->post('account_number'),
                                'account_name'      =>  $this->input->post('account_name'),
                                'initial_balance'   =>  $this->input->post('initial_balance')?currency($this->input->post('initial_balance')):0,
                                'bank_branch_id'    =>  $this->input->post('bank_branch_id'),
                                'bank_id'           =>  $this->input->post('bank_id'),
                                'enable_email_transaction_alerts_to_members'           =>  $this->input->post('enable_email_transaction_alerts_to_members')?1:0,
                                'created_by'        =>  $this->user->id,
                                'account_password'  =>  $this->input->post('account_password'),
                                'created_on'        =>  time(),
                                'active'            =>  1,
                            ));
                            if($id){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Successfully added',
                                    'id' => $id,
                                    'bank_id' => $this->input->post('bank_id'),
                                    'is_partner' => $this->banks_m->is_partner($this->input->post('bank_id'))?1:0,
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Failed to add bank account. Try again later',
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
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

    function edit(){
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
                    if($this->member->group_role_id || $this->member->is_admin){
                        $id = $this->input->post('id');
                        if($post = $this->bank_accounts_m->get_group_bank_account($id,$this->group->id)){
                            if($post->is_default){
                                $response = array(
                                    'status' => 0,
                                    'message' => 'No action can be performed on this account. Kindly contact support to disable E-Wallet services',
                                );
                            }else{
                                $this->form_validation->set_rules($this->validation_rules);
                                if($this->form_validation->run()){
                                    $id = $this->bank_accounts_m->update($post->id,array(
                                        'account_number'    =>  $this->input->post('account_number'),
                                        'account_name'      =>  $this->input->post('account_name'),
                                        'initial_balance'   =>  $this->input->post('initial_balance')?currency($this->input->post('initial_balance')):0,
                                        'bank_branch_id'    =>  $this->input->post('bank_branch_id'),
                                        'bank_id'           =>  $this->input->post('bank_id'),
                                        'enable_email_transaction_alerts_to_members'           =>  $this->input->post('enable_email_transaction_alerts_to_members')?1:0,
                                        'modified_by'        =>  $this->user->id,
                                        'account_password'  =>  $this->input->post('account_password'),
                                        'modified_on'        =>  time(),
                                    ));
                                    if($id){
                                        $response = array(
                                            'status' => 1,
                                            'message' => 'Successfully updated',
                                            'id' => $post->id,
                                            'bank_id' => $this->input->post('bank_id'),
                                            'is_partner' => $this->banks_m->is_partner($this->input->post('bank_id'))?1:0,
                                        );
                                    }else{
                                        $response = array(
                                            'status' => 0,
                                            'message' => 'Failed to edit bank account. Try again later',
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
                                    );
                                }
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Group bank account to edit not found',
                            );
                        }
                        $post = new stdClass();
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

    function get_group_bank_accounts_list(){
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
                    $posts = $this->bank_accounts_m->get_group_bank_accounts($this->group->id);
                    $banks=array();
                    foreach ($posts as $post) {
                        $is_partner = $this->banks_m->is_partner($post->bank_id)?1:0;
                        $banks[] = array(
                            'id' => $post->id,
                            'account_name' => $post->account_name,
                            'description' => $post->bank_name.' ('.$post->bank_branch.') - '.$post->account_number,
                            'initial_balance' => $post->initial_balance,
                            'account_balance' => $post->initial_balance? ($post->initial_balance + $post->current_balance):$post->current_balance,
                            'account_number' => $post->account_number,
                            'bank_id' => $post->bank_id,
                            'bank_branch_id' => $post->bank_branch_id,
                            'bank_name' => $post->bank_name,
                            'bank_branch' => $post->bank_branch,
                            'is_closed' => $post->is_closed?1:0,
                            'is_partner' => $is_partner,
                            'is_verified' => $is_partner?($post->is_verified?1:0):0,
                            'is_default' => $post->is_default?1:0,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'successful',
                        'banks' => $banks,
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

    function connect(){
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
                    if($this->member->group_role_id || $this->member->is_admin){
                        $post = new stdClass();
                        $bank_id = $this->input->post('bank_id');
                        $id = $this->input->post('id');
                        $signatory_phone = $this->input->post('signatory_phone');
                        if($bank = $this->banks_m->get($bank_id)){
                            if($bank->partner){
                                if($group_account = $this->bank_accounts_m->get_group_bank_account($id,$this->group->id)){
                                    if(!$group_account->is_verified){
                                        if($phone = valid_phone($signatory_phone)){
                                            $response = $this->bank->request_one_time_password($group_account->account_number,$phone,CALLING_CODE,$this->default_country->code,$bank->create_otp_url);
                                            if($response){
                                                $result = json_decode(json_decode($response));
                                                if(isset($result->rsData)){
                                                    if($result->rsData->status->code == '1' || $result->rsData->status->code == 1){
                                                        $response = array(
                                                            'status' => 1,
                                                            'message' => 'The verification code was sent to '.$phone,
                                                        );
                                                    }else{
                                                         $response = array(
                                                            'status' => 0,
                                                            'message' => 'The Account Number did not match the signatory number. Kindly try another signatory phone number',
                                                        );
                                                    }
                                                }else{
                                                    $response = array(
                                                        'status' => 0,
                                                        'message' => 'Service is temporarily down. Kindly try again later. ',
                                                    );
                                                }
                                            }else{
                                                $response = array(
                                                    'status' => 0,
                                                    'message' => 'Service is temporarily down. No response received. Kindly try again later.',
                                                );
                                            }
                                        }else{
                                            $response = array(
                                                'status' => 0,
                                                'message' => 'Signatory phone number is invalid. Try a different one',
                                            );
                                        }
                                    }else{
                                        $response = array(
                                            'status' => 0,
                                            'message' => 'Account already verified. Disconnect to start verification again',
                                        );
                                    }
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Could not group bank account. Select a different account',
                                    );
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Bank selected is not a partner bank. Kindly use a different bank',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not find bank. Try again later',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

    function verify(){
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
                    if($this->member->group_role_id || $this->member->is_admin){
                        $post = new stdClass();
                        $bank_id = $this->input->post('bank_id');
                        $id = $this->input->post('id');
                        $signatory_phone = $this->input->post('signatory_phone');
                        $verification_code = $this->input->post('verification_code');                                               
                        if($bank = $this->banks_m->get($bank_id)){
                            if($bank->partner){
                                if($group_account = $this->bank_accounts_m->get_group_bank_account($id,$this->group->id)){
                                    if($phone = valid_phone($signatory_phone)){
                                        if($verification_code){
                                            $response = $this->bank->verify_one_time_password($group_account->account_number,$phone,strtoupper($verification_code),CALLING_CODE,$this->default_country->code,$bank->verify_otp_url);
                                            $result = json_decode(json_decode($response));
                                            if($result->rsData->status->code == '1'){
                                                $input = array(
                                                    'is_verified'=>1,
                                                    'modified_on'=>time(),
                                                    'verified_on'=>time(),
                                                    'modified_by'=>$this->user->id
                                                );
                                                if($this->bank_accounts_m->update($id,$input)){
                                                    if($this->messaging->send_verification_confirmation_to_member($this->group,$this->member,$this->user,$group_account->account_number)){
                                                         if($this->messaging->send_bank_account_scheme_code_change_request($this->user,$this->group,$group_account->account_number)){
                                                            $response = array(
                                                                'status' => 1,
                                                                'message' => 'The verification request was successful. Your account can now receive transaction alerts. ',
                                                            );
                                                        }else{
                                                            $response = array(
                                                                'status' => 0,
                                                                'message' => 'The verification request was successful, but something went wrong while sending the scheme code change request. ',
                                                            );
                                                        }   
                                                    }else{
                                                        $response = array(
                                                            'status' => 0,
                                                            'message' => 'The verification request was successful, but could not send SMS and Email Confirmation to the member',
                                                        );
                                                    }
                                                }else{
                                                    $response = array(
                                                        'status' => 0,
                                                        'message' => 'Something went wrong when verifying the bank account.',
                                                    );
                                                }
                                            }else if($result->rsData->status->code == '-1'){
                                                $response = array(
                                                    'status' => 0,
                                                    'message' => 'Verification code failed. Ensure you enter correct verification code or ensure the verification code has not expired.',
                                                );
                                            }else{
                                                $response = array(
                                                    'status' => 0,
                                                    'message' => 'The verification  request failed. Kindly request another verification code',
                                                );
                                            }
                                        }else{
                                            $response = array(
                                                'status' => 0,
                                                'message' => 'Signatory phone number is invalid. Try a different one',
                                            );
                                        }
                                    }else{
                                        $response = array(
                                            'status' => 0,
                                            'message' => 'Signatory phone number is invalid. Try a different one',
                                        );
                                    }
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Could not group bank account. Select a different account',
                                    );
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Bank selected is not a partner bank. Kindly use a different bank',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not find bank. Try again later',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

    function disconnect(){
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
                    if($this->member->group_role_id || $this->member->is_admin){
                        $id = $this->input->post('id');
                        if($group_account = $this->bank_accounts_m->get_group_bank_account($id,$this->group->id)){
                            $update = array(
                                'is_verified'   =>  0,
                                'modified_on'   =>  time(),
                                'modified_by'   =>  $this->user->id,
                            );
                            if($this->bank_accounts_m->update($group_account->id,$update)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'successful',
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Could not group bank account. Select a different account',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not group bank account. Select a different account',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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


    function close(){
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
                    if($this->member->group_role_id || $this->member->is_admin){
                        $id = $this->input->post('id');
                        if($post = $this->bank_accounts_m->get_group_bank_account($id,$this->group->id)){
                            if($post->is_default){
                                $response = array(
                                    'status' => 0,
                                    'message' => 'No action can be performed on this account. Kindly contact support to disable E-Wallet services',
                                );
                            }else{
                                if($post->is_closed){
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Sorry, the bank account is already closed',
                                    );
                                }
                                $update = array(
                                    'is_closed'=>1,
                                    'modified_by'=>$this->user->id,
                                    'modified_on'=>time()
                                );
                                if($res = $this->bank_accounts_m->update($post->id,$update)){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Bank Account successfully closed',
                                    );
                                }else{
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Unable to close the bank account',
                                    );
                                }
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not group bank account. Select a different account',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

    function activate(){
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
                    if($this->member->group_role_id || $this->member->is_admin){
                        $id = $this->input->post('id');
                        if($post = $this->bank_accounts_m->get_group_bank_account($id,$this->group->id)){
                            if($post->is_default){
                                $response = array(
                                    'status' => 0,
                                    'message' => 'No action can be performed on this account. Kindly contact support to disable E-Wallet services',
                                );
                            }else{
                                if(!$post->is_closed){
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Sorry, the bank account is already activated',
                                    );
                                }
                                $update = array(
                                    'is_closed'=>0,
                                    'modified_by'=>$this->user->id,
                                    'modified_on'=>time()
                                );
                                if($res = $this->bank_accounts_m->update($post->id,$update)){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Bank Account successfully activated',
                                    );
                                }else{
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Unable to activate the bank account',
                                    );
                                }
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not group bank account. Select a different account',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

    function delete(){
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
                    if($this->member->group_role_id || $this->member->is_admin){
                        $id = $this->input->post('id');
                        $identity = $this->user->email?:$this->user->phone;
                        $password = $this->input->post('password');
                        if($this->ion_auth->login($identity,$password)){
                            if($post = $this->bank_accounts_m->get_group_bank_account($id,$this->group->id)){
                                if($post->is_default){
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Bank account can not be deleted. Kindly contact support to disable E-Wallet services',
                                    );
                                }else{
                                    if($this->transaction_statements_m->check_if_group_account_has_transactions('bank-'.$post->id,$post->group_id)){
                                        $response = array(
                                            'status' => 0,
                                            'message' => 'The bank account has transactions associated to it, void all transactions associated to this account before deleting it',
                                        );
                                    }else{
                                        if($this->bank_accounts_m->delete($post->id,$post->group_id)){
                                            $response = array(
                                                'status' => 1,
                                                'message' => 'Bank account deleted successfully',
                                            );
                                        }else{
                                            $response = array(
                                                'status' => 0,
                                                'message' => 'Bank account could not be deleted',
                                            );
                                        }
                                    }
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Could not group bank account. Select a different account',
                                );
                            }

                            
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'You entered the wrong password.',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

    function activate_wallet(){
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
                    if($this->member->group_role_id || $this->member->is_admin){
                        $accept_terms_and_conditions = $this->input->post('accept_terms_and_conditions');
                        if($accept_terms_and_conditions){
                            if($this->bank->open_online_account($this->user,$this->group,$this->member)){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'successfully opened E-Wallet',
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => $this->session->flashdata('error'),
                                    );
                                }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'You must accept terms and conditions to open a group Wallet',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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
        echo json_encode(array('response'=>$response));
    }
}