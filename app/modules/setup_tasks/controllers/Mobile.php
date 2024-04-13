<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mobile extends Mobile_Controller{

    protected $data = array();

    function __construct(){
        parent::__construct();
        $this->load->model('setup_tasks_m');
        $this->load->model('bank_accounts/bank_accounts_m');
        $this->load->model('mobile_money_accounts/mobile_money_accounts_m');
        $this->load->model('sacco_accounts/sacco_accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->model('group_roles/group_roles_m');
        $this->load->library('setup_tasks_tracker');
        $this->load->library('contribution_invoices');
        $this->load->library('bank');
        $this->load->library('group_members');

        $this->sms_template_default = $this->contribution_invoices->sms_template_default;
    }

    protected $invitation_rules = array(
        array(
            'field' => 'group_id',
            'label' => 'Group id',
            'rules' => 'trim|required|numeric'
        ),
        array(
            'field' => 'user_id',
            'label' => 'User id',
            'rules' => 'trim|required|numeric'
        ),
        array(
            'field' => 'usernames',
            'label' => 'Username',
            'rules' => 'trim|callback__valid_usernames'
        ),
        array(
            'field' => 'phones',
            'label' => 'Phones',
            'rules' => 'trim'
        ),
        array(
            'field' => 'group_role_ids',
            'label' => 'Group Role',
            'rules' => 'trim'
        ),
    );

    protected $contribution_rules = array(
        array(
            'field' => 'name',
            'label' => 'Contribution Name',
            'rules' => 'trim|required',
        ),array(
            'field' => 'amount',
            'label' => 'Contribution Amount',
            'rules' => 'trim|required|currency',
        ),array(
            'field' => 'type',
            'label' => 'Contribution Type',
            'rules' => 'trim|numeric|required',
        ),array(
            'field' => 'regular_invoicing_active',
            'label' => 'Activate Invoicing',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'one_time_invoicing_active',
            'label' => 'Activate Invoicing',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'invoice_date',
            'label' => 'Invoice Date',
            'rules' => 'trim',
        ),array(
            'field' => 'contribution_date',
            'label' => 'Contribution Date/Due Date',
            'rules' => 'trim',
        ),array(
            'field' => 'contribution_frequency',
            'label' => 'How often do members contribute',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'month_day_monthly',
            'label' => 'Day of the Month',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'week_day_monthly',
            'label' => 'Day of the Week',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'week_day_weekly',
            'label' => 'Day of the Week',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'week_day_fortnight',
            'label' => 'Day of the Week',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'week_day_multiple',
            'label' => 'Day of the Week',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'week_number_fortnight',
            'label' => 'Day of the Week',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'month_day_multiple',
            'label' => 'Day of the Month',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'start_month_multiple',
            'label' => 'Staring Month',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'invoice_days',
            'label' => 'Invoice days',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'sms_template',
            'label' => 'SMS template',
            'rules' => 'trim',
        ),array(
            'field' => 'enable_fines',
            'label' => 'Enable Fines',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'enable_contribution_member_list',
            'label' => 'Enable Contribution Member List',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'disable_overpayments',
            'label' => 'Disable Overpayments',
            'rules' => 'trim|numeric',
        ),
    );

    protected $contributing_member_rules = array(
        array(
            'field' => 'member_ids',
            'label' => 'Member Id',
            'rules' => 'trim|callback__valid_member_id',
        ),
        array(
            'field' => 'group_id',
            'label' => 'Group id',
            'rules' => 'trim|required|numeric'
        ),
        array(
            'field' => 'user_id',
            'label' => 'User id',
            'rules' => 'trim|required|numeric'
        ),
    );

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
                        'status'    =>  404,
                        'message'       =>  '404 Method Not Found for URI: '.$this->uri->uri_string(),
                    ),
            )
        );
    }

    function index(){
    }

    function _valid_usernames(){
        $first_names = $this->input->post('first_names')?:array();
        foreach ($first_names as $key => $value) {
            if(!$value){
                $this->form_validation->set_message('_valid_usernames','Ensure all usernames are valid');
                return FALSE;
            }
        }
        return TRUE;
    }

    function _valid_member_id(){
        $member_ids = $this->input->post('member_ids');
        $all_members = $this->input->post('all_members');
        if($all_members){
            return TRUE;
        }else{
            if ($member_ids) {
                foreach ($member_ids as $key => $member_id) {
                    if(!is_numeric($member_id)){
                        $this->form_validation->set_message('_valid_member_id','Invalid member details');
                        return FALSE;
                    }
                }
                return TRUE;
            }else{
                $this->form_validation->set_message('_valid_member_id','Kindly select atleast one member');
                return FALSE;
            }
        }
    }

    function _valid_phones(){
        $phones = $this->input->post('phones');
        foreach ($phones as $key => $value) {
            if(valid_phone($value)){
                
            }else{
                $this->form_validation->set_message('_valid_phones','Ensure all user phone numbers are valid');
                return FALSE;
            }
        }
        return TRUE;
    }

    function _valid_group_roles(){
        $group_role_ids = $this->input->post('group_role_ids');
        $group_id = $this->input->post('group_id');
        $group_roles = $this->group_roles_m->get_group_role_options($group->id);
        $group_roles_array = array();
        foreach ($group_role_ids as $key => $value) {
            if(is_numeric($value)){
                if(!array_key_exists($value, $group_roles)){
                    $this->form_validation->set_message('_valid_group_roles','Ensure all user group roles are valid');
                    return FALSE;
                }else{
                    if(in_array($value, $group_roles_array)){
                        $this->form_validation->set_message('_valid_group_roles','Ensure a role is not assigned to more than one member');
                        return FALSE;
                    }else{
                        $group_roles_array[] = array($value);
                    }
                }
            }else{
                $this->form_validation->set_message('_valid_group_roles','Ensure all user group roles are valid');
                return FALSE;
            }
        }
        return TRUE;
    }

    function add_group_members(){
        die("disabled adding group member on mobile");
        $first_names = array();
        $last_names = array();
        $emails = array();
        $phones = array();
        $group_role_ids = array();
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                if(is_array($value)){
                    $i = 0;
                    foreach ($value as $member_key => $member_details) {
                        if($member_details->first_name){
                            $first_names[$i] = $member_details->first_name;
                        }
                        if($member_details->last_name){
                            $last_names[$i] = $member_details->last_name;
                        }
                        if($member_details->email){
                            $emails[$i] = $member_details->email;
                        }
                        if($member_details->phone){
                            $phones[$i] = valid_phone($member_details->phone);
                        }
                        if(is_numeric($member_details->group_role_id)){
                            $group_role_ids[$i] = $member_details->group_role_id;
                        }
                        ++$i;
                    }
                }else{
                    $_POST[$key] = $value;
                }
            }
        }
        $_POST['first_names'] = $first_names;
        $_POST['last_names'] = $last_names;
        $_POST['emails'] = $emails;
        $_POST['phones'] = $phones;
        $_POST['group_role_ids'] = $group_role_ids;
        $this->form_validation->set_rules($this->invitation_rules);
        if($this->form_validation->run()){
            $user_id = $this->input->post('user_id');
            $group_id = $this->input->post('group_id');
            if($this->user = $this->ion_auth->get_user($user_id)){
                $this->ion_auth->update_last_login($this->user->id);
                if($this->group = $this->groups_m->get($group_id)){
                    if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                        $successful_invitations_count = 0;
                        $unsuccessful_invitations_count = 0;
                        $error_messages = array();
                        $error_response = array();
                        foreach ($first_names as $key => $first_name) {
                            $phone = isset($phones[$key])?valid_phone($phones[$key]):0;
                            $group_role = isset($group_role_ids[$key])?$group_role_ids[$key]:$group_role_ids[$key];
                            $calling_code = substr($phone, 0,3);
                            $original_phone = substr($phone, -9);
                            $first_name = $first_name;
                            $last_name = isset($last_names[$key])?$last_names[$key]:'';
                            $email = isset($emails[$key])?$emails[$key]:'';
                            $middle_name = '';
                            $is_admin = 0;
                            if($this->user->id == $this->group->owner){
                                if($this->user->phone == valid_phone($phone) || $this->user->email == $email){
                                    if($phone){
                                        $is_admin = 1;
                                    }
                                    if($email){
                                        $is_admin = 1;
                                    }                       
                                }else{
                                    $is_admin = 0;
                                }
                            }else{
                                $is_admin = 0;
                            }
                            if(valid_phone($phone) || valid_email($email)){
                                if($this->group_members->add_member_to_group($this->group,$first_name,$last_name,$phone,$email,TRUE,TRUE,$this->user,$this->member->id,$group_role,$middle_name,$calling_code,$original_phone,$is_admin)){
                                    $successful_invitations_count++;
                                }else{
                                    $unsuccessful_invitations_count++;
                                    $error_messages[$key] = $this->session->userdata('warning_feedback');
                                    $error_response[$key] = $this->session->flashdata('error');
                                }
                            }else{
                                $unsuccessful_invitations_count++;
                                $error_response[$key] = 'user does not have a valid email address or phone number';
                            }
                        }

                        if($successful_invitations_count){
                            if($unsuccessful_invitations_count){
                                $response = array(
                                    'status' => 1,
                                    'message' => $successful_invitations_count.' member(s) successfully invited but '.$unsuccessful_invitations_count.' failed',
                                    'time' => time(),
                                    'fails' => $error_response,
                                );
                            }else{
                                $response = array(
                                    'status' => 1,
                                    'message' => $successful_invitations_count.' member(s) successfully invited',
                                    'time' => time(),
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 1,
                                'message' => $error_messages,
                                'fails' => $error_response,
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

    function create_group_contribution_setting(){
        $member_ids = array();
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                if(is_array($value)){
                    $i = 0;
                    foreach ($value as $member_key => $member_details) {
                        if($member_details->member_id){
                            $member_ids[$i] = $member_details->member_id;
                        }
                        ++$i;
                    }
                }else{
                    $_POST[$key] = $value;
                }
            }
        }
        $_POST['invoice_days'] = 2;
        $user_id = $this->input->post('user_id')?:0;
        $group_id = $this->input->post('group_id')?:0;
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $this->form_validation->set_rules($this->contribution_rules);
                    if($this->form_validation->run()){
                        $name = $this->input->post('name');
                        $amount = currency($this->input->post('amount'));
                        $type = $this->input->post('type');
                        $regular_invoicing_active = $this->input->post('regular_invoicing_active')?1:0;
                        $one_time_invoicing_active = $this->input->post('one_time_invoicing_active')?1:0;
                        $input = array(
                            'name' => $name,
                            'amount' => $amount,
                            'type' => $type,
                            'regular_invoicing_active' => $regular_invoicing_active,
                            'one_time_invoicing_active' => $one_time_invoicing_active,
                            'active' => 1,
                            'group_id' => $this->group->id,
                            'is_hidden' => 0,
                            'created_by' => $this->user->id,
                            'created_on' => time(),
                        );
                        if($contribution_id = $this->contributions_m->insert($input)){
                            if($type == 1){
                                $contribution_date = $this->_contribution_date();
                                $invoice_date = $contribution_date - (24*60*60*$this->input->post('invoice_days'));
                                $regular_contribution_settings_input = array(
                                    'contribution_id'=>$contribution_id,
                                    'group_id'=>$this->group->id,
                                    'invoice_date'=>$invoice_date,
                                    'contribution_date'=>$contribution_date,
                                    'contribution_frequency'=>$this->input->post('contribution_frequency'),
                                    'invoice_days'=>$this->input->post('invoice_days'),
                                    'sms_notifications_enabled'=>$this->input->post('sms_notifications_enabled')?1:0,
                                    'email_notifications_enabled'=>$this->input->post('email_notifications_enabled')?1:0,
                                    'sms_template'=>$this->sms_template_default,
                                    'month_day_monthly'=>$this->input->post('month_day_monthly'),
                                    'week_day_monthly'=>$this->input->post('week_day_monthly'),
                                    'week_day_weekly'=>$this->input->post('week_day_weekly'),
                                    'week_day_fortnight'=>$this->input->post('week_day_fortnight'),
                                    'week_number_fortnight'=>$this->input->post('week_number_fortnight'),
                                    'month_day_multiple'=>$this->input->post('month_day_multiple'),
                                    'week_day_multiple'=>$this->input->post('week_day_multiple'),
                                    'start_month_multiple'=>$this->input->post('start_month_multiple'),
                                    'disable_overpayments'=>$this->input->post('disable_overpayments'),
                                    'enable_fines'=> 0,
                                    'active'=>1,
                                    'created_by'=>$this->user->id,
                                    'created_on'=>time(),
                                );
                                if($regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($contribution_id,$this->group->id)){
                                    if($result = $this->contributions_m->update_regular_contribution_setting($regular_contribution_setting->id,$regular_contribution_settings_input)){
                                        $response = array(
                                            'status' => 1,
                                            'message' => 'Contribution changes saved successfully',
                                            'time' => time(),
                                            'members' => $this->members_m->get_active_group_members($this->group->id),
                                            'group_roles' => $this->group_roles_m->get_group_role_options(),
                                            'contribution_id' => $contribution_id,
                                        );
                                    }else{
                                        $response = array(
                                            'status' => 0,
                                            'message' => 'Could not save changes to regular contribution setting',
                                            'time' => time(),
                                        );
                                    }
                                }else{
                                    if($regular_contribution_setting_id = $this->contributions_m->insert_regular_contribution_setting($regular_contribution_settings_input)){
                                        $response = array(
                                            'status' => 1,
                                            'message' => 'Contribution changes saved successfully',
                                            'time' => time(),
                                            'members' => $this->members_m->get_group_members(),
                                            'group_roles' => $this->group_roles_m->get_group_role_options(),
                                            'contribution_id' => $contribution_id,
                                        );
                                    }else{
                                        $response = array(
                                            'status' => 0,
                                            'message' => 'Could create regular contribution setting',
                                            'time' => time(),
                                        );
                                    }
                                }
                            }else if($type==2){
                                $invoice_date = strtotime($this->input->post('invoice_date'));
                                $contribution_date = strtotime($this->input->post('contribution_date'));
                                $one_time_contribution_settings_input = array(
                                    'contribution_id'=>$contribution_id,
                                    'group_id'=>$this->group->id,
                                    'invoice_date'=>$invoice_date,
                                    'contribution_date'=>$contribution_date,
                                    'disable_overpayments'=>$this->input->post('disable_overpayments'),
                                    'sms_notifications_enabled'=>$this->input->post('sms_notifications_enabled')?1:0,
                                    'email_notifications_enabled'=>$this->input->post('email_notifications_enabled')?1:0,
                                    'sms_template'=>$this->sms_template_default,
                                    'enable_fines'=> 0,
                                    'active'=>1,
                                    'invoices_queued'=>0,
                                    'created_by'=>$this->user->id,
                                    'created_on'=>time(),
                                );
                                if($one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($contribution_id,$this->group->id)){
                                    if($result = $this->contributions_m->update_one_time_contribution_setting($one_time_contribution_setting->id,$one_time_contribution_settings_input)){
                                        //do nothing for now
                                        $response = array(
                                            'status' => 1,
                                            'message' => 'Contribution changes saved successfully',
                                            'time' => time(),
                                            'members' => $this->members_m->get_group_members(),
                                            'group_roles' => $this->group_roles_m->get_group_role_options(),
                                            'contribution_id' => $contribution_id,
                                        );
                                    }else{
                                        $response = array(
                                            'status' => 0,
                                            'message' => 'Could not save changes to one time contribution setting',
                                            'time' => time(),
                                        );
                                    }
                                }else{
                                    if($one_time_contribution_setting_id = $this->contributions_m->insert_one_time_contribution_setting($one_time_contribution_settings_input)){
                                        $response = array(
                                            'status' => 1,
                                            'message' => 'Contribution changes saved successfully',
                                            'time' => time(),
                                            'members' => $this->members_m->get_group_members(),
                                            'group_roles' => $this->group_roles_m->get_group_role_options(),
                                            'contribution_id' => $contribution_id,
                                        );
                                    }else{
                                        $response = array(
                                            'status' => 0,
                                            'message' => 'Could create one time contribution setting',
                                            'time' => time(),
                                        );
                                    }
                                }
                            }elseif ($type == 3) {
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Non scheduled Contribution successfully added',
                                    'time' => time(),
                                    'members' => $this->members_m->get_group_members(),
                                    'group_roles' => $this->group_roles_m->get_group_role_options(),
                                    'contribution_id' => $contribution_id,
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Invalid contribution type',
                                    'time' => time(),
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Contribution could not be created',
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
        echo json_encode(array('response'=>$response));
    }

    function contributing_members(){
        $member_ids = array();
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                if(is_array($value) && preg_match('/contributing_members/', $key)){
                    $i = 0;
                    foreach ($value as $member_key => $member_details) {
                        if($member_details->member_id){
                            $member_ids[$i] = $member_details->member_id;
                        }
                        ++$i;
                    }
                }else{
                    $_POST[$key] = $value;
                }
            }
        }
        $_POST['member_ids'] = $member_ids;
        $user_id = $this->input->post('user_id')?:0;
        $group_id = $this->input->post('group_id')?:0;
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $this->form_validation->set_rules($this->contributing_member_rules);
                    if($this->form_validation->run()){
                        $id = $this->input->post('contribution_id');
                        $contribution = $this->contributions_m->get_group_contribution($id,$this->group->id);
                        $regular_ontime_contribution_setting = array('regular_contribution_setting_id'=>0,'one_time_contribution_setting_id'=>0);
                        if($contribution){
                            if($contribution->type==1){
                                $regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($contribution->id,$this->group->id);
                                $regular_ontime_contribution_setting = array('regular_contribution_setting_id'=>$regular_contribution_setting->id);
                                $contribution = (object) array_merge((array) $regular_contribution_setting, (array) $contribution);
                            }else if($contribution->type==2){
                                $one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($contribution->id,$this->group->id);
                                $regular_ontime_contribution_setting = array('one_time_contribution_setting_id'=>$one_time_contribution_setting->id);
                                $contribution = (object) array_merge((array) $one_time_contribution_setting, (array) $contribution);
                            }else{

                            }
                            $post = $contribution;
                            $post = (object)array_merge($regular_ontime_contribution_setting,(array)$post);
                            $all_members = $this->input->post('all_members');
                            if($all_members){
                                $this->contributions_m->delete_contribution_member_pairings($contribution->id);
                                if($post->type == 1){
                                    if($regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($contribution->id,$this->group->id)){
                                        $regular_contribution_settings_input = array(
                                            'enable_contribution_member_list'=>0,
                                            'active'=>1,
                                            'modified_by'=>$this->user->id,
                                            'modified_on'=>time(),
                                        );
                                        if($result = $this->contributions_m->update_regular_contribution_setting($regular_contribution_setting->id,$regular_contribution_settings_input)){
                                            $response = array(
                                                'status' => 1,
                                                'message' => 'Contribution changes saved successfully',
                                                'contribution_id' => $contribution->id,
                                                'time' => time(),
                                            );
                                        }else{
                                            $response = array(
                                                'status' => 0,
                                                'message' => 'Could not save changes to regular contribution setting',
                                                'time' => time(),
                                            );
                                        }
                                    }else{
                                        $response = array(
                                            'status' => 0,
                                            'message' => 'Could not update regular contribution setting',
                                            'time' => time(),
                                        );
                                    }
                                }elseif ($post->type == 2) {
                                    if($one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($contribution->id,$this->group->id)){
                                        $one_time_contribution_settings_input = array(
                                            'enable_contribution_member_list'=>0,
                                            'modified_by'=>$this->user->id,
                                            'modified_on'=>time(),
                                        );
                                        if($result = $this->contributions_m->update_one_time_contribution_setting($one_time_contribution_setting->id,$one_time_contribution_settings_input)){
                                            //do nothing for now
                                            $response = array(
                                                'status' => 1,
                                                'message' => 'Contribution changes saved successfully',
                                                'contribution_id' => $contribution->id,
                                                'time' => time(),
                                            );
                                        }else{
                                            $response = array(
                                                'status' => 0,
                                                'message' => 'Could not save changes to one time contribution setting',
                                                'time' => time(),
                                            );
                                        }
                                    }else{
                                        $response = array(
                                            'status' => 0,
                                            'message' => 'Could not update one time contribution setting',
                                            'time' => time(),
                                        );
                                    }
                                }elseif ($post->type==3) {
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Contribution changes saved successfully',
                                        'contribution_id' => $contribution->id,
                                        'time' => time(),
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'You are updating invalid contribution type. Kindly start over again',
                                    );
                                }
                            }else{
                                if($member_ids){
                                    $this->contributions_m->delete_contribution_member_pairings($contribution->id);
                                    $success = 0;
                                    $fails = 0;
                                    if($post->type==1){
                                        $regular_contribution_settings_input = array(
                                            'enable_contribution_member_list'=>1,
                                            'active'=>1,
                                            'modified_by'=>$this->user->id,
                                            'modified_on'=>time(),
                                        );
                                        if($result = $this->contributions_m->update_regular_contribution_setting($post->regular_contribution_setting_id,$regular_contribution_settings_input)){
                                        }else{
                                        }
                                    }elseif($post->type == 2){
                                        $one_time_contribution_settings_input = array(
                                            'enable_contribution_member_list'=>1,
                                            'modified_by'=>$this->user->id,
                                            'modified_on'=>time(),
                                        );
                                        if($result = $this->contributions_m->update_one_time_contribution_setting($post->one_time_contribution_setting_id,$one_time_contribution_settings_input)){
                                        }else{
                                        }
                                    }
                                    foreach($member_ids as $member_id){
                                        $input = array(
                                            'member_id'=>$member_id,
                                            'group_id'=>$this->group->id,
                                            'contribution_id'=>$contribution->id,
                                            'created_on'=>time(),
                                            'created_by'=>$this->user->id,
                                        );
                                        if($contribution_member_pairing_id = $this->contributions_m->insert_contribution_member_pairing($input)){
                                            $success++;
                                        }else{
                                            $fails++;
                                            //$this->session->set_flashdata('error','Could not insert contribution member pairing');
                                        }
                                    }
                                    if($fails){
                                        $response = array(
                                            'status' => 0,
                                            'message' => 'Could not insert '.$fails.'contribution member pairing',
                                            'time' => time(),
                                        );
                                    }else if ($success) {
                                        $response = array(
                                            'status' => 1,
                                            'contribution_id' => $contribution->id,
                                            'message' => 'successfully added members',
                                            'contribution_id' => $contribution->id,
                                            'time' => time(),
                                        );
                                    }
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'You need to select at least one member',
                                        'time' => time(),
                                    );
                                }    
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Error. Kindly first create a contribution',
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
        echo json_encode(array('response'=>$response));
    }

    function contribution_fine_settings(){
        $member_ids = array();
        foreach ($this->request as $key => $value) {
            if(is_array($value) || is_object($value)){
                $data = array();
                $fine_settings= array();
                foreach ($value as $value_key => $value_value) {
                    if(isset($value_value->member_id)){
                        $data[$value_key] = $value_value->member_id;
                    }else{
                        foreach ($value_value as $new_key => $new_value) {
                            if(array_key_exists($new_key, $fine_settings)){
                                $fine_settings[$new_key] = $fine_settings[$new_key]+array( $value_key=> $new_value);
                            }else{
                                $fine_settings[$new_key] = array( $value_key=> $new_value);
                            }
                        }
                    }
                }
                $_POST[$key] = $data;
                $_POST = $_POST+$fine_settings;
            }else{
                if(preg_match('/phone/', $key)){
                    $_POST[$key] = valid_phone($value);
                }else{
                    $_POST[$key] = $value;
                }
            }
        }
        $user_id = $this->input->post('user_id')?:0;
        $group_id = $this->input->post('group_id')?:0;
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $id = $this->input->post('contribution_id');
                    $contribution = $this->contributions_m->get_group_contribution($id,$this->group->id);
                    if($contribution){
                        if($contribution->type==1){
                            $regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($contribution->id,$this->group->id);
                            $contribution = (object) array_merge((array) $regular_contribution_setting, (array) $contribution);
                        }else if($contribution->type==2){
                            $one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($contribution->id,$this->group->id);
                            $contribution = (object) array_merge((array) $one_time_contribution_setting, (array) $contribution);
                        }else{

                        }
                        $post = $contribution;
                        $result_update = TRUE;
                        $message = '';
                        if($post->type==1){
                            $regular_contribution_settings_input = array(
                                'enable_fines' => $this->input->post('enable_fines')?1:0,
                                'modified_on' => time(),
                                'modified_by' => $this->user->id,
                            );
                            if($regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($contribution->id,$this->group->id)){
                                if($result = $this->contributions_m->update_regular_contribution_setting($regular_contribution_setting->id,$regular_contribution_settings_input)){
                                    //do nothing for now
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Could not update contribution setting',
                                        'time' => time(),
                                    );
                                    $result_update = FALSE;
                                }
                            }else{
                                if($regular_contribution_setting_id = $this->contributions_m->insert_regular_contribution_setting($regular_contribution_settings_input)){
                                    //do nothing for now
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Could create regular contribution setting',
                                        'time' => time(),
                                    );
                                    $result_update = FALSE;
                                }
                            }
                        }else if($post->type==2){
                            $one_time_contribution_settings_input = array(
                                'enable_fines' => $this->input->post('enable_fines')?1:0,
                                'modified_on' => time(),
                                'modified_by' => $this->user->id,
                            );
                            if($one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($contribution->id)){
                                if($result = $this->contributions_m->update_one_time_contribution_setting($one_time_contribution_setting->id,$one_time_contribution_settings_input)){
                                    //do nothing for now
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Could not save changes to one time contribution setting',
                                        'time' => time(),
                                    );
                                    $result_update = FALSE;
                                }
                            }else{
                                if($one_time_contribution_setting_id = $this->contributions_m->insert_one_time_contribution_setting($one_time_contribution_settings_input,$this->group->id)){
                                    //do nothing for now
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Could create one time contribution setting',
                                        'time' => time(),
                                    );
                                    $result_update = FALSE;
                                }
                            }
                        }elseif ($post->type == 3) {
                            if($this->input->post('enable_fines')){
                                $message = 'This contribution type is not allowed to have fines';
                                $result_update = FALSE;
                            }else{

                            }
                        }else{
                            $message = 'Invalid contribution type';
                            $result_update = FALSE;
                        }
                        if($this->input->post('enable_fines') == 1 && $result_update == TRUE){
                            $posts = $_POST;
                            $fine_entries_are_valid = TRUE;
                            if(isset($posts['fine_type'])){ 
                                $count = 0; foreach($posts['fine_type'] as $fine_type):
                                    if($fine_type){
                                        $fine_limit = isset($posts['fine_limit'][$count])?$posts['fine_limit'][$count]:0;
                                        if($fine_type==1){
                                            if($posts['fixed_amount'][$count]&&$posts['fixed_fine_mode'][$count]&&$posts['fixed_fine_chargeable_on'][$count]){
                                                if(is_numeric(currency($posts['fixed_amount'][$count]))&&
                                                    is_numeric($posts['fixed_fine_mode'][$count])
                                                    &&is_numeric($posts['fixed_fine_frequency'][$count])
                                                    &&is_numeric($fine_limit)){
                                                    //do for nothing now
                                                }else{
                                                    $fine_entries_are_valid = FALSE;
                                                }
                                            }else{
                                                $fine_entries_are_valid = FALSE;
                                            }
                                        }else if($fine_type==2){
                                            $percentage_fine_frequency = isset($posts['percentage_fine_frequency'][$count])?$posts['percentage_fine_frequency'][$count]:0;
                                            if($posts['percentage_rate'][$count]
                                                &&$posts['percentage_fine_on'][$count]
                                                &&$posts['percentage_fine_chargeable_on'][$count]
                                                &&$posts['percentage_fine_mode'][$count]){
                                                if(is_numeric($posts['percentage_rate'][$count])
                                                    &&is_numeric($posts['percentage_fine_on'][$count])
                                                    &&is_numeric($posts['percentage_fine_mode'][$count])
                                                    &&is_numeric($fine_limit)
                                                    &&is_numeric($percentage_fine_frequency)){
                                                    //do for nothing now
                                                }else{
                                                    $fine_entries_are_valid = FALSE;
                                                }
                                            }else{
                                                $fine_entries_are_valid = FALSE;
                                            }
                                        }else{
                                            $fine_entries_are_valid = FALSE;
                                        }
                                    }else{
                                        $fine_entries_are_valid = FALSE;
                                    }
                                    $count++;
                                endforeach;
                            }
                            if($fine_entries_are_valid){
                                $this->contributions_m->delete_contribution_fine_settings($post->id,$this->group->id);
                                if(isset($posts['fine_type'])){
                                    $count = 0; foreach($posts['fine_type'] as $fine_type):
                                        if($fine_type){
                                            $fine_date = $this->_fine_date($post->contribution_date,$fine_type,$posts['fixed_fine_chargeable_on'][$count],$posts['percentage_fine_chargeable_on'][$count]);
                                            $input = array(
                                                'contribution_id'=>$post->id,
                                                'group_id'=>$this->group->id,
                                                'fine_type'=>$fine_type,
                                                'fixed_amount'=>currency($posts['fixed_amount'][$count]),
                                                'fixed_fine_mode'=>$posts['fixed_fine_mode'][$count],
                                                'fixed_fine_chargeable_on'=>$posts['fixed_fine_chargeable_on'][$count],
                                                'fixed_fine_frequency'=>isset($posts['fixed_fine_frequency'][$count])?$posts['fixed_fine_frequency'][$count]:0,
                                                'percentage_rate'=>$posts['percentage_rate'][$count],
                                                'percentage_fine_on'=>$posts['percentage_fine_on'][$count],
                                                'percentage_fine_chargeable_on'=>$posts['percentage_fine_chargeable_on'][$count],
                                                'percentage_fine_mode'=>$posts['percentage_fine_mode'][$count],
                                                'percentage_fine_frequency'=>isset($posts['percentage_fine_frequency'][$count])?$posts['percentage_fine_frequency'][$count]:0,
                                                'fine_limit'=>$posts['fine_limit'][$count],
                                                'fine_date'=>isset($posts['fine_date'][$count])?(($posts['fine_date'][$count]>=strtotime('today'))?$posts['fine_date'][$count]:$fine_date):$fine_date,
                                                //'fine_date'=>$fine_date,
                                                'active'=>1,
                                                'fine_sms_notifications_enabled'=>isset($posts['fine_sms_notifications_enabled'][$count])?1:0,
                                                'fine_email_notifications_enabled'=>isset($posts['fine_email_notifications_enabled'][$count])?1:0,
                                                'created_on'=>time(),
                                                'created_by'=>$this->user->id
                                            );
                                            if($contrbution_fine_setting_id = $this->contributions_m->insert_contribution_fine_setting($input)){
                                                //do nothing for now
                                            }else{
                                                $this->session->set_flashdata('error','Could not insert contribution fine setting');
                                            }
                                        }
                                        $count++;
                                    endforeach;
                                }
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Successful.',
                                    'time' => time(),
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Fine entries are not valid',
                                    'time' => time(),
                                );
                            }
                        }else{
                            if($result_update == TRUE){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Successful.',
                                    'time' => time(),
                                );
                            }else{
                                $response = array(
                                    'status' => 1,
                                    'message' => $message?:'Could not update contribution fines. Try over again.',
                                    'time' => time(),
                                );
                            }
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Error. Kindly create a contribution first',
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
        echo json_encode(array('response'=>$response));
    }

    function _fine_date($contribution_date = 0,$fine_type = 0,$fixed_fine_chargeable_on = 0,$percentage_fine_chargeable_on = 0){
        return $this->contribution_invoices->get_contribution_fine_date($contribution_date,$fine_type,$fixed_fine_chargeable_on,$percentage_fine_chargeable_on,0,0,0,$contribution_date);
    }

    function _contribution_date(){
        return $contribution_date = $this->contribution_invoices->get_regular_contribution_contribution_date(
            $this->input->post('contribution_frequency'),
            $this->input->post('month_day_monthly'),
            $this->input->post('week_day_monthly'),
            $this->input->post('week_day_weekly'),
            $this->input->post('week_day_fortnight'),
            $this->input->post('week_number_fortnight'),
            $this->input->post('month_day_multiple'),
            $this->input->post('week_day_multiple'),
            $this->input->post('start_month_multiple')
        );
    }

    function edit_contribution_setting(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $_POST['invoice_days'] = 2;
        $user_id = $this->input->post('user_id')?:'';
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                     $this->form_validation->set_rules($this->contribution_rules);
                    if($this->form_validation->run()){
                        $id = $this->input->post('id');
                        if($contribution = $this->contributions_m->get_group_contribution($id,$this->group->id)){
                            if($contribution){
                                if($contribution->type==1){
                                    $regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($contribution->id,$this->group->id);
                                    $contribution = (object) array_merge((array) $regular_contribution_setting, (array) $contribution);
                                }else if($contribution->type==2){
                                    $one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($contribution->id,$this->group->id);
                                    $contribution = (object) array_merge((array) $one_time_contribution_setting, (array) $contribution);
                                }else{

                                }
                            }
                            $post = $contribution;
                            $input = array(
                                'name' => $this->input->post('name'),
                                'amount' => $this->input->post('amount'),
                                'type' => $this->input->post('type'),
                                'regular_invoicing_active' => $this->input->post('regular_invoicing_active')?1:0,
                                'one_time_invoicing_active' => $this->input->post('one_time_invoicing_active')?1:0,
                                'modified_by' => $this->user->id,
                                'modified_on' => time(),
                            );
                            if($result = $this->contributions_m->update($post->id,$input)){
                                if($this->input->post('type')==1){
                                    $contribution_date = $post->contribution_date>$this->_contribution_date()?$post->contribution_date:$this->_contribution_date();
                                    $invoice_date = $contribution_date - (24*60*60*$this->input->post('invoice_days'));
                                    $regular_contribution_settings_input = array(
                                        'contribution_id'=>$post->id,
                                        'group_id'=>$this->group->id,
                                        'invoice_date'=>$invoice_date,
                                        'contribution_date'=>$contribution_date,
                                        'contribution_frequency'=>$this->input->post('contribution_frequency'),
                                        'invoice_days'=>$this->input->post('invoice_days'),
                                        'sms_notifications_enabled'=>$this->input->post('sms_notifications_enabled')?1:0,
                                        'email_notifications_enabled'=>$this->input->post('email_notifications_enabled')?1:0,
                                        'sms_template'=>$this->sms_template_default,
                                        'month_day_monthly'=>$this->input->post('month_day_monthly'),
                                        'week_day_monthly'=>$this->input->post('week_day_monthly'),
                                        'week_day_weekly'=>$this->input->post('week_day_weekly'),
                                        'week_day_fortnight'=>$this->input->post('week_day_fortnight'),
                                        'week_number_fortnight'=>$this->input->post('week_number_fortnight'),
                                        'month_day_multiple'=>$this->input->post('month_day_multiple'),
                                        'week_day_multiple'=>$this->input->post('week_day_multiple'),
                                        'start_month_multiple'=>$this->input->post('start_month_multiple'),
                                        'disable_overpayments'=>$this->input->post('disable_overpayments'),
                                        'enable_fines'=>$post->enable_fines?1:0,
                                        'active'=>1,
                                        'modified_by'=>$this->user->id,
                                        'modified_on'=>time(),
                                    );
                                    if($regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($post->id,$this->group->id)){
                                        if($result = $this->contributions_m->update_regular_contribution_setting($regular_contribution_setting->id,$regular_contribution_settings_input)){
                                            $response = array(
                                                'status' => 1,
                                                'message' => 'Contribution changes saved successfully',
                                                'time' => time(),
                                                'members' => $this->members_m->get_active_group_members($this->group->id),
                                                'group_roles' => $this->group_roles_m->get_group_role_options(),
                                                'contribution_id' => $post->id,
                                            );
                                        }else{
                                            $response = array(
                                                'status' => 0,
                                                'message' => 'Could not save changes to regular contribution setting',
                                                'time' => time(),
                                            );
                                        }
                                    }else{
                                        if($regular_contribution_setting_id = $this->contributions_m->insert_regular_contribution_setting($regular_contribution_settings_input)){
                                            $response = array(
                                                'status' => 1,
                                                'message' => 'Contribution changes saved successfully',
                                                'time' => time(),
                                                'members' => $this->members_m->get_group_members(),
                                                'group_roles' => $this->group_roles_m->get_group_role_options(),
                                                'contribution_id' => $post->id,
                                            );
                                        }else{
                                            $response = array(
                                                'status' => 0,
                                                'message' => 'Could create regular contribution setting',
                                                'time' => time(),
                                            );
                                        }
                                    }
                                }else if($this->input->post('type')==2){
                                    $invoice_date = strtotime($this->input->post('invoice_date'));
                                    $contribution_date = strtotime($this->input->post('contribution_date'));
                                    $one_time_contribution_settings_input = array(
                                        'contribution_id'=>$post->id,
                                        'group_id'=>$this->group->id,
                                        'invoice_date'=>$invoice_date,
                                        'contribution_date'=>$contribution_date,
                                        'disable_overpayments'=>$this->input->post('disable_overpayments'),
                                        'sms_notifications_enabled'=>$this->input->post('sms_notifications_enabled')?1:0,
                                        'email_notifications_enabled'=>$this->input->post('email_notifications_enabled')?1:0,
                                        'sms_template'=>$this->input->post('sms_template'),
                                        'enable_fines'=>$post->enable_fines?1:0,
                                        'active'=>1,
                                        'invoices_queued'=>0,
                                        'modified_by'=>$this->user->id,
                                        'modified_on'=>time(),
                                    );
                                    if($one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($post->id,$this->group->id)){
                                        if($result = $this->contributions_m->update_one_time_contribution_setting($one_time_contribution_setting->id,$one_time_contribution_settings_input)){
                                            $response = array(
                                                'status' => 1,
                                                'message' => 'Contribution changes saved successfully',
                                                'time' => time(),
                                                'members' => $this->members_m->get_group_members(),
                                                'group_roles' => $this->group_roles_m->get_group_role_options(),
                                                'contribution_id' => $post->id,
                                            );
                                        }else{
                                            $response = array(
                                                'status' => 0,
                                                'message' => 'Could not save changes to one time contribution setting',
                                                'time' => time(),
                                            );
                                        }
                                    }else{
                                        if($one_time_contribution_setting_id = $this->contributions_m->insert_one_time_contribution_setting($one_time_contribution_settings_input)){
                                            $response = array(
                                                'status' => 1,
                                                'message' => 'Contribution changes saved successfully',
                                                'time' => time(),
                                                'members' => $this->members_m->get_group_members(),
                                                'group_roles' => $this->group_roles_m->get_group_role_options(),
                                                'contribution_id' => $post->id,
                                            );
                                        }else{
                                            $response = array(
                                                'status' => 0,
                                                'message' => 'Could create one time contribution setting',
                                                'time' => time(),
                                            );
                                        }
                                    }
                                }else if($this->input->post('type')==3){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Non scheduled Contribution successfully added',
                                        'time' => time(),
                                        'members' => $this->members_m->get_group_members(),
                                        'group_roles' => $this->group_roles_m->get_group_role_options(),
                                        'contribution_id' => $post->id,
                                    );

                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Invalid contribution type',
                                        'time' => time(),
                                    );
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Error occured updating contribution setting, try again later',
                                    'time' => time(),
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => ' Could not find group contribution',
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
        echo json_encode(array('response'=>$response));
    }

    function resend_group_invitation(){
        $file = file_get_contents('php://input');
        $response = array();
        $request = array();
        $time = time();
        if($file){
            $file = $this->encryptdecrypt->decrypt($file);
            $request = json_decode($file);
            if($request){
                $usernames = array();
                $phones = array();
                $group_role_ids = array();
                foreach ($request as $key => $value) {
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
                            if($member_id){
                                $invited_member = $this->members_m->get_group_member($member_id,$this->group->id);
                                if($invited_member){
                                    if($invited_member->invitation_accepted){
                                        $response = array(
                                            'status' => 0,
                                            'message' => 'Member invitation failed: Member has already accepted group invitation',
                                            'time' => time(),
                                        );
                                    }else{
                                        $user = $this->ion_auth->get_user($invited_member->user_id);
                                        if($this->messaging->send_group_invitation_to_user($this->group,$user,$invited_member,$this->user,$this->member->id,1,1)){
                                            $invitation_code = rand(10000,999999);
                                            $update = array(
                                                'invitation_code' => $invited_member->is_admin?"":$invitation_code,
                                                'invitation_accepted' => 0,
                                                'invitation_declined' => 0,
                                                'invitation_declined_on' => '',
                                                'invitation_accepted_on' => '',
                                                'modified_on' => time(),
                                                'modified_by' => $this->user->id,
                                            );
                                            $this->members_m->update($invited_member->id,$update);
                                            $response = array(
                                                'status' => 1,
                                                'message' => 'success',
                                                'time' => time(),
                                            );
                                        }else{
                                            $response = array(
                                                'status' => 0,
                                                'message' => 'Member invitation failed: Something went wrong when sending the group invitation',
                                                'time' => time(),
                                            );
                                        }
                                    }
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Member invitation failed: Could not find invited member profile',
                                        'time' => time(),
                                    );
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Invalid request: Missing member_id',
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
            }else{
                $response = array(
                    'status' => 3,
                    'message' => 'File format error',
                    'time' => $time,
                );
            }           
        }else{
            $response = array(
                'status' => 2,
                'message' => 'Empty file sent',
                'time' => $time,
            );
        }
        echo json_encode(array('response'=>$response,'request'=>$request));
    }

    function cancel_group_invitation(){
        $file = file_get_contents('php://input');
        $response = array();
        $request = array();
        $time = time();
        if($file){
            $file = $this->encryptdecrypt->decrypt($file);
            $request = json_decode($file);
            if($request){
                $usernames = array();
                $phones = array();
                $group_role_ids = array();
                foreach ($request as $key => $value) {
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
                            if($member_id){
                                $invited_member = $this->members_m->get_group_member($member_id,$this->group->id);
                                if($invited_member){
                                    if($invited_member->invitation_accepted){
                                        $response = array(
                                            'status' => 0,
                                            'message' => 'Member invitation cancelation failed: Member has already accepted group invitation',
                                            'time' => time(),
                                        );
                                    }else{
                                        $user = $this->ion_auth->get_user($invited_member->user_id);
                                        $update = array(
                                            "group_role_id" => '',
                                            'is_deleted' => 1,
                                            'active' => 0,
                                            'modified_by' => $this->user->id,
                                            'modified_on' => time(),
                                        );
                                        if($this->members_m->update($invited_member->id,$update)){
                                            $response = array(
                                                'status' => 1,
                                                'message' => 'successful',
                                                'time' => time(),
                                            );
                                        }else{
                                            $response = array(
                                                'status' => 0,
                                                'message' => 'Member invitation cancelation failed: Could not find invited member profile',
                                                'time' => time(),
                                            );
                                        }
                                    }
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Member invitation cancelation failed: Could not find invited member profile',
                                        'time' => time(),
                                    );
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Invalid request: Missing member_id',
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
            }else{
                $response = array(
                    'status' => 3,
                    'message' => 'File format error',
                    'time' => $time,
                );
            }           
        }else{
            $response = array(
                'status' => 2,
                'message' => 'Empty file sent',
                'time' => $time,
            );
        }
        echo json_encode(array('response'=>$response,'request'=>$request));
    }

    function reassign_member_group_role(){
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
                    if($this->group->is_validated){
                        $response = array(
                            'status' => 0,
                            'message' => 'Member update failed. Group already active. Go to settings and initiate member signatory change request',
                            'time' => time(),
                        );
                    }else{
                        $member_id = $this->input->post('member_id');
                        $role_in_use = FALSE;
                        if($member_id){
                            $invited_member = $this->members_m->get_group_member($member_id,$this->group->id);
                            if($invited_member){
                                if($invited_member->invitation_accepted&&$invited_member->invitation_declined){
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Member invitation cancelation failed: Member has already accepted invitation',
                                        'time' => time(),
                                    );
                                }else{
                                    $group_role_id = $this->input->post('group_role_id')?:'';
                                    if($group_role_id){
                                        if($group_role_id == $invited_member->group_role_id){

                                        }else{
                                            if($this->members_m->check_if_group_role_id_is_assigned($group_role_id,$this->group->id)){
                                                $role_in_use = TRUE;
                                            }
                                        }
                                    }
                                    if($role_in_use){
                                        $response = array(
                                            'status' => 0,
                                            'message' => 'Member update failed. Role already in use',
                                            'time' => time(),
                                        );
                                    }else{
                                        $update = array(
                                            'group_role_id' => $group_role_id?:'',
                                            'modified_by' => $this->user->id,
                                            'modified_on' => time(),
                                        );
                                        if($this->members_m->update($invited_member->id,$update)){
                                            $response = array(
                                                'status' => 1,
                                                'message' => 'Group member role successfully reassigned',
                                                'time' => time(),
                                            );
                                        }else{
                                            $response = array(
                                                'status' => 0,
                                                'message' => 'Member update failed. Could not reasign role',
                                                'time' => time(),
                                            );
                                        }
                                    }
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Member invitation cancelation failed: Could not find invited member profile',
                                    'time' => time(),
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Invalid request: Missing member_id',
                                'time' => time(),
                            );
                        }
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
        echo json_encode(array('response'=>$response));        
    }
}?>