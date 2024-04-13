<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{

    protected $data = array();
    
    function __construct(){
        parent::__construct();
        $this->load->model('setup_tasks_m');
        $this->load->library('setup_tasks_tracker');
        $this->load->library('contribution_invoices');
    }

    public function get_members_listing($group_id=''){
        $members = $this->members_m->get_group_members($group_id);
        $disabled = $this->input->get('disabled')?:'';
        $html = '';
        if(empty($members)){
            $html.= '
            <div class="alert alert-info">
                <h4 class="block">Information! No records to display</h4>
                <p>
                    No accepted members to display.
                </p>
            </div>';
        }else{
            $group_role_options = $this->group_roles_m->get_group_role_options();
            $html.= '
            <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                <thead>
                    <tr>
                        <th width="8px">
                            #
                        </th>
                        <th>
                            '.translate('Member Name').'
                        </th>
                        <th>
                            '.translate('Phone Number').'
                        </th>
                         <th>
                            '.translate('Email Address').'
                        </th>
                        <th>
                            '.translate('Role').'
                        </th>
                    </tr>
                </thead>
                <tbody>
            ';
            $i = 1;
            foreach($members as $member):
                $group_role = isset($group_role_options[$member->group_role_id])?$group_role_options[$member->group_role_id]:'Member';
                $html.= '
                <tr>
                    <td>'.($i++).'.</td>
                    <td>'.$member->first_name." ".$member->middle_name." ".$member->last_name.'</td>
                    <td>'.$member->phone.'</td>
                    <td>'.$member->email.'</td>
                    <td><a data-content="#assign_role_form_holder" data-title="'.translate('Assign Role to').''.$member->first_name." ".$member->middle_name." ".$member->last_name.'" class="member_group_role inline'.($disabled?('hidden'):'').'" data-toggle="modal" data-member-id="'.$member->id.'" data-user-id="'.$member->user_id.'" data-group-role-id="'.$member->group_role_id.'" href="#">'.translate($group_role).'</a></td>';
                    $html.= '
                </tr>
                ';
            endforeach;
            $html.= '
                </tbody>
            </table>
            ';
        }
        if($members){
            $status = 1;
        }else{
            $status = 2;
        }
        echo json_encode(array(
            'status' => $status,
            'html' => $html,
        ));
    }

    public function get_declined_members_listing(){
        $members = $this->members_m->get_declined_group_members();
        $html='';
        if(empty($members)){
            $html.= '
            <div class="alert alert-info">
                <h4 class="block">Information! No records to display</h4>
                <p>
                    No members have declined invitation.
                </p>
            </div>';
        }else{
            $group_role_options = $this->group_roles_m->get_group_role_options();
            $html.= '
            <br/>
            <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                <thead>
                    <tr>
                        <th width="8px">
                            #
                        </th>
                        <th>
                            Member Name
                        </th>
                        <th>
                            Phone
                        </th>
                        <th>
                            Email
                        </th>
                        <th>
                            Role
                        </th>
                        <th>
                            Declined On 
                        </th>
                    </tr>
                </thead>
                <tbody>
            ';
            $i = 1;
            foreach($members as $member):
                $group_role = isset($group_role_options[$member->group_role_id])?$group_role_options[$member->group_role_id]:'Member';
                $html.= '
                <tr>
                    <td>'.($i++).'.</td>
                    <td>'.$member->first_name." ".$member->middle_name." ".$member->last_name.'</td>
                    <td>'.$member->phone.'</td>
                    <td>'.$member->email.'</td>
                    <td>'.$group_role.'</td>
                    <td>';
                        $html.= timestamp_to_datetime($member->invitation_declined_on);
                    $html.= '
                    </td>
                </tr>
                ';
            endforeach;
            $html.= '
                </tbody>
            </table>
            ';
        }
        if($members){
            $status = 1;
        }else{
            $status = 2;
        }
        echo json_encode(array(
            'status' => $status,
            'html' => $html,
        ));

    }

    public function check_role_assignment(){
        $group_role_options = $this->group_roles_m->get_group_role_options();
        $group_roles_assigned = TRUE;
        $unassigned_roles_array = array();
        foreach($group_role_options as $group_role_id => $name):
            if($this->members_m->get_member_by_group_role_id($group_role_id)){

            }else{
                $unassigned_roles_array[] = $name;
                $group_roles_assigned = FALSE;
            }
        endforeach;
        if($group_roles_assigned){
            $response = array(
                'code' => 1,
                'message' => "All roles have been assigned.",
            );
            echo json_encode($response);
        }else{
            if(count($unassigned_roles_array)==1){
                $roles_place_holder = 'role' ;
            }else{
                $roles_place_holder = 'roles' ;
            }
            $message = " You need to assign the following ".$roles_place_holder." to your invited members or members who have accepted your invitation before you can proceed: ".implode(',',$unassigned_roles_array);
            $response = array(
                'code' => 400,
                'message' => $message,
            );
            echo json_encode($response);
        }
    }

    public function invite_members(){
        $group_role_options = $this->group_roles_m->get_group_role_options();
        $data = array();
        $posts = $_POST;
        $errors = array();
        $error_messages = array();
        $successes = array();
        $phones = array();
        $emails = array();
        $calling_codes = array();
        $group_roles = array();
        $entries_are_valid = TRUE;
        $add_member_error_results = array();
        if($this->input->post('submit')){
            if(!empty($posts)){ 
                if(isset($posts['first_names'])){
                    $count = count($posts['first_names']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['first_names'][$i])&&isset($posts['last_names'][$i])&&isset($posts['phones'][$i])&&isset($posts['calling_codes'][$i])):
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
                                    $phone = trim($posts['calling_codes'][$i].$posts['phones'][$i]);
                                    if(valid_phone($phone)){
                                        if(in_array($phone,$phones)){
                                            $successes['phones'][$i] = 0;
                                            $errors['phones'][$i] = 1;
                                            $error_messages['phones'][$i] = 'Please enter another phone number, you cannot have duplicated phone numbers';
                                            $entries_are_valid = FALSE;   
                                        }else{      
                                            $successes['phones'][$i] = 1;
                                            $errors['phones'][$i] = 0;
                                            $phones[] = $phone;
                                        }                                   
                                    }else{  
                                        $successes['phones'][$i] = 0;
                                        $errors['phones'][$i] = 1;
                                        $error_messages['phones'][$i] = 'Please enter a valid phone number';
                                        $entries_are_valid = FALSE;
                                    }
                                }
                                //Group roles
                                if($posts['group_roles'][$i]==''){
                                    $successes['group_roles'][$i] = 1;
                                    $errors['group_roles'][$i] = 0;
                                }else{
                                    if(in_array($posts['group_roles'][$i],$group_roles)){
                                        $successes['group_roles'][$i] = 0;
                                        $errors['group_roles'][$i] = 1;
                                        $error_messages['group_roles'][$i] = 'You have already assigned this role to another member. Two members cannot share the same role.';
                                        $entries_are_valid = FALSE;
                                    }else{      
                                        $successes['group_roles'][$i] = 1;
                                        $errors['group_roles'][$i] = 0;
                                        $group_roles[] = $posts['group_roles'][$i];
                                    }
                                }
                                 //emails
                                if($posts['emails'][$i]==''){
                                    $successes['emails'][$i] = 1;
                                    $errors['emails'][$i] = 0;
                                }else{
                                    $email = $posts['emails'][$i];
                                    if(valid_email($email)){
                                        if(in_array($email,$emails)){
                                            $successes['emails'][$i] = 0;
                                            $errors['emails'][$i] = 1;
                                            $error_messages['emails'][$i] = 'Please enter another email address, you cannot have duplicated email addresses';
                                            $entries_are_valid = FALSE;   
                                        }else{      
                                            $successes['emails'][$i] = 1;
                                            $errors['emails'][$i] = 0;
                                            $emails[] = $email;
                                        } 
                                    }else{
                                        $successes['emails'][$i] = 0;
                                        $errors['emails'][$i] = 1;
                                        $error_messages['emails'][$i] = 'Please enter valid email address';
                                        $entries_are_valid = FALSE;  
                                    }
                                }
                        endif;
                    endfor;
                }
            }
            if($entries_are_valid){
                $member_id_array = array();
                if(isset($posts['first_names'])){
                    $count = count($posts['first_names']);
                    $successful_invitations_count = 0;
                    $unsuccessful_invitations_count = 0;
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['first_names'][$i])&&isset($posts['last_names'][$i])&&isset($posts['phones'][$i])&&isset($posts['calling_codes'][$i])):
                            $send_invitation_sms = TRUE;
                            $send_invitation_email = FALSE;
                            $first_name = strip_tags($posts['first_names'][$i]);
                            $last_name = strip_tags($posts['last_names'][$i]);
                            $phone = trim($posts['calling_codes'][$i].$posts['phones'][$i]);
                            $email = $posts['emails'][$i];
                            if($member_id = $this->group_members->add_member_to_group($this->group,$first_name,$last_name,$phone,$email,$send_invitation_sms,$send_invitation_email,$this->user,$this->member->id,$posts['group_roles'][$i])){
                                $member_id_array[] = $member_id;
                                $successful_invitations_count++;
                            }else{
                                $add_member_error_results[] = $this->session->userdata('warning_feedback');
                                $unsuccessful_invitations_count++;
                            }
                        endif;
                    endfor;
                    if($successful_invitations_count){
                        if($successful_invitations_count==1){
                            //$this->session->set_flashdata('success',$successful_invitations_count.' member successfully added to your group.');
                        }else{
                            //$this->session->set_flashdata('success',$successful_invitations_count.' members successfully added to your group.');
                        }
                    }
                    if($unsuccessful_invitations_count){
                        if($unsuccessful_invitations_count==1){
                            //$this->session->set_flashdata('warning',$unsuccessful_invitations_count.' member was not added to your group.');
                        }else{
                            //$this->session->set_flashdata('warning',$unsuccessful_invitations_count.' members were not added to your group.');
                        }
                    }
                    $this->group_members->set_active_group_size($this->group->id,TRUE);
                    $this->setup_tasks_tracker->set_completion_status('add-group-members',$this->group->id,$this->user->id);
                    if($members = $this->members_m->get_group_members_by_member_id_array($this->group->id,$member_id_array)){
                        $group_members = array();
                        foreach ($members as $member) {
                            # code...
                            if(isset($group_role_options[$member->group_role_id])){
                                $member->group_role_name = $group_role_options[$member->group_role_id];
                            }else{
                                $member->group_role_name = "Member";
                            }
                            $group_members[] = $member;
                        }
                        echo json_encode($group_members);
                        $this->session->set_userdata('success_feedback',"");
                        $this->session->set_flashdata('success',"");
                        $this->session->set_flashdata('info',"");
    
                    }else{
                        if($add_member_error_results){
                            foreach ($add_member_error_results as $key => $value) {
                                echo ($value[$key]).'<br/>';
                            }
                        }else{
                            echo 'Unable to add member(s) to group';
                        }
                    }
                }
            }else{
                echo 'There are some errors on the form. Please review and try again.';
            }

            if($this->members_m->count_invited_group_role_holders($this->group->id) == 3){
                if($this->group->setup_tasks_position<2){
                     $update = array(
                        'setup_tasks_position' => 2,
                        'modified_by' => $this->user->id,
                        'modified_on' => time(),
                    );
                    $this->groups_m->update($this->group->id,$update);
                }

            }
        }else{
            //print_r($_POST);
            //echo 'Form not submitted.';
        }
    }

    public function assign_group_role(){
        $validation_rules = array(
            array(
                'field' =>  'member_id',
                'label' =>  'Member ID',
                'rules' =>  'trim|required|numeric',
            ),array(
                'field' =>  'user_id',
                'label' =>  'User ID',
                'rules' =>  'trim|numeric|required',
            ),array(
                'field' =>  'group_role_id',
                'label' =>  'Group Role ID',
                'rules' =>  'trim|callback_group_role_assignment_is_unique|numeric',
            ),
        );
        $this->form_validation->set_rules($validation_rules);
        if($this->form_validation->run()){
            $member_id = $this->input->post('member_id');
            $group_role_id = $this->input->post('group_role_id');
            $input = array(
                'group_role_id' => $group_role_id,
                'modified_on' => time(),
                'modified_by' => $this->user->id,
            );
            if($this->members_m->update($member_id,$input)){
                $response =  array(
                    'code' => 1,
                    'message' => "Role assigned successfully"
                );
                echo json_encode($response);
            }else{
                echo "Could not update member role.";
            }
        }else{
            echo validation_errors();
        }
    }


    public function group_role_assignment_is_unique(){
        $group_role_id = $this->input->post('group_role_id');
        if($group_role_id==''){
            return TRUE;
        }else{
            if($member = $this->members_m->get_member_by_group_role_id($group_role_id)){
                if($this->input->post('user_id')==$member->user_id){
                    return TRUE;
                }else{
                    $this->form_validation->set_message('group_role_assignment_is_unique', 'The group role is already assigned to another member.');
                    return FALSE;
                }
            }else{
                return TRUE;
            }
        }
    }

    function get_group_setup_complete_data(){
        $members = $this->members_m->get_group_members($this->group->id);
        $group_roles = $this->group_roles_m->get_group_role_options($this->group->id);
        $contributions = $this->contributions_m->get_group_contributions($this->group->id);
        $contributions_arr='';
        if($contributions){
            $contribution_type_options = $this->contribution_invoices->contribution_type_options;
            $contribution_frequency_options = $this->contribution_invoices->contribution_frequency_options;
            $month_days = $this->contribution_invoices->month_days;
            $week_days = $this->contribution_invoices->week_days;
            $days_of_the_month = $this->contribution_invoices->days_of_the_month;
            $every_two_week_days = $this->contribution_invoices->every_two_week_days;
            $months = $this->contribution_invoices->months;
            $starting_months = $this->contribution_invoices->starting_months;
            $week_numbers = $this->contribution_invoices->week_numbers;
            $contribution_frequency_options = $this->contribution_invoices->contribution_frequency_options;
            foreach ($contributions as $key=>$post) {
                $frequency = '';
                $category = $post->category?$this->contribution_invoices->contribution_category_options[$post->category]:'';
                $list_members = '';
                if($post->type == 1){
                    $regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($post->id);
                    if($regular_contribution_setting){
                        if($regular_contribution_setting->contribution_frequency==1){
                            //Once a month
                            $frequency = $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].',
                                                                                '.$days_of_the_month[$regular_contribution_setting->month_day_monthly].'
                                                                                '.$month_days[$regular_contribution_setting->week_day_monthly?$regular_contribution_setting->week_day_monthly:0];
                        }else if($regular_contribution_setting->contribution_frequency==6){
                            $frequency = $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$week_days[$regular_contribution_setting->week_day_weekly];
                        }else if($regular_contribution_setting->contribution_frequency==7){
                            $frequency = $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$every_two_week_days[$regular_contribution_setting->week_day_fortnight].' '.$week_numbers[$regular_contribution_setting->week_number_fortnight];
                        }else if($regular_contribution_setting->contribution_frequency==2||$regular_contribution_setting->contribution_frequency==3||$regular_contribution_setting->contribution_frequency==4||$regular_contribution_setting->contribution_frequency==5){
                            $frequency = $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].',
                            '.$days_of_the_month[$regular_contribution_setting->month_day_multiple].'
                            '.$month_days[$regular_contribution_setting->week_day_multiple?$regular_contribution_setting->week_day_multiple:0].',
                            '.$starting_months[$regular_contribution_setting->start_month_multiple];
                        }else if($regular_contribution_setting->contribution_frequency==8){
                            $frequency = $contribution_frequency_options[$regular_contribution_setting->contribution_frequency];
                        }
                        if($regular_contribution_setting->enable_contribution_member_list){
                            $list_members.='<strong>For specific members<strong>';
                        }else{
                            $list_members.= '<strong>For all members</strong>';
                        }
                    }
                }
                if($key >0){
                    $contributions_arr.='<div class="m-separator m-separator--dashed m-separator--lg"></div>';
                }
                $contributions_arr.= 
                '<div class="m-form__section m-form__section--first">
                    <div class="m-form__heading">
                        <h4 class="m-form__heading-title">'.$post->name.' contribution</h4>
                    </div>
                    <div class="form-group m-form__group m-form__group--sm row">
                        <div class="col-sm-12"><strong>'.translate('Category').': </strong>'.$category.'</div>
                        <label class="col-xl-4 col-lg-4 col-form-label"> <p>'.$contribution_type_options[$post->type].':</p></label>
                        <div class="col-xl-8 col-lg-8">
                            <span class="m-form__control-static">'.$this->group_currency.' '.number_to_currency($post->amount).' - '.$frequency.'<br/>'.$list_members.'</span>
                        </div>
                    </div>
                </div>';
            }
        }
        $loan_types = $this->loan_types_m->get_all();
        $loan_types_arr = '';
        if($loan_types){
            foreach ($loan_types as $key => $post) {
                if($key >0){
                    $loan_types_arr.='<div class="m-separator m-separator--dashed m-separator--lg"></div>';
                }
                if($post->loan_amount_type == 1){
                    $loan_title='<strong>'.translate('Amount Range').'</strong>: '.$this->group_currency.'. '.number_to_currency($post->minimum_loan_amount).' - '.$this->group_currency.'. '.number_to_currency($post->maximum_loan_amount).'<br/>';
                }else if($post->loan_amount_type == 2){
                    $loan_title='<strong>'.translate('Multiplier').'</strong>: '.$post->loan_times_number.' '.translate('times member savings').'<br/>';
                }
                if($post->interest_type == 1 || $post->interest_type==2){
                    $calc = '';
                    if($post->enable_reducing_balance_installment_recalculation){
                        $calc = ' - Recalculations on early payments enabled';
                    }
                    $loan_details= '<strong>'.translate('Interest Rate').':</strong> '.$post->interest_rate.'% per '.$this->loan->loan_interest_rate_per[$post->loan_interest_rate_per].' on '.$this->loan->interest_types[$post->interest_type].$calc;
                }else{
                    $loan_details= '<strong>'.translate('Interest Rate').':</strong> Custom';
                }
                $loan_types_arr.= 
                '<div class="m-form__section m-form__section--first">
                    <div class="m-form__heading">
                        <h4 class="m-form__heading-title">'.$post->name.'</h4>
                    </div>
                    <div class="form-group m-form__group m-form__group--sm row">
                        <label class="col-xl-4 col-lg-4 col-form-label"> '.$post->name.'</label>
                        <div class="col-xl-8 col-lg-8">
                            <span class="m-form__control-static">'.$loan_title.' <br/>'.$loan_details.'</span>
                        </div>
                    </div>
                </div>';
            }
        }
        $bank_accounts_arr = '';
        $bank_key =0;
        $bank_accounts = $this->bank_accounts_m->get_group_bank_accounts();
        if($bank_accounts){
            foreach ($bank_accounts as $key => $post) {
                if($bank_key >0){
                    $bank_accounts_arr.='<div class="m-separator m-separator--dashed m-separator--lg"></div>';
                }
                $bank_key++;
                $bank_accounts_arr.= 
                '<div class="m-form__section m-form__section--first">
                    <div class="m-form__heading">
                        <h4 class="m-form__heading-title">'.$post->bank_name.'</h4>
                    </div>
                    <div class="form-group m-form__group m-form__group--sm row">
                        <label class="col-xl-4 col-lg-4 col-form-label"><strong> Branch: </strong>&nbsp;'.$post->bank_branch.'</label>
                        <div class="col-xl-8 col-lg-8">
                            <span class="m-form__control-static"> 
                                <strong>'.translate('Name').' :</strong>  &nbsp;'.$post->account_name.'<br/>
                                <strong>'.translate('Number').' :</strong>  &nbsp;'.$post->account_number.'
                            </span>
                        </div>
                    </div>
                </div>';
            }
        }
        $sacco_accounts = $this->sacco_accounts_m->get_group_sacco_accounts();
        if($sacco_accounts){
            foreach ($sacco_accounts as $key => $post) {
                if($bank_key >0){
                    $bank_accounts_arr.='<div class="m-separator m-separator--dashed m-separator--lg"></div>';
                }
                $bank_key++;
                $bank_accounts_arr.= 
                '<div class="m-form__section m-form__section--first">
                    <div class="m-form__heading">
                        <h4 class="m-form__heading-title">'.$post->sacco_name.'</h4>
                    </div>
                    <div class="form-group m-form__group m-form__group--sm row">
                        <label class="col-xl-4 col-lg-4 col-form-label"><strong> Branch: </strong> &nbsp;'.$post->sacco_branch.'</label>
                        <div class="col-xl-8 col-lg-8">
                            <span class="m-form__control-static"> 
                                <strong>'.translate('Name').' :</strong>  &nbsp;'.$post->account_name.'<br/>
                                <strong>'.translate('Number').' :</strong>  &nbsp;'.$post->account_number.'
                            </span>
                        </div>
                    </div>
                </div>';
            }
        }
        $mobile_money_accounts =$this->mobile_money_accounts_m->get_group_mobile_money_accounts();
        if($mobile_money_accounts){
            foreach ($mobile_money_accounts as $key => $post) {
                if($bank_key >0){
                    $bank_accounts_arr.='<div class="m-separator m-separator--dashed m-separator--lg"></div>';
                }
                $bank_key++;
                $bank_accounts_arr.= 
                '<div class="m-form__section m-form__section--first">
                    <div class="m-form__heading">
                        <h4 class="m-form__heading-title">'.$post->mobile_money_provider_name.'</h4>
                    </div>
                    <div class="form-group m-form__group m-form__group--sm row">
                        <label class="col-xl-4 col-lg-4 col-form-label"><strong>Name :</strong>  &nbsp;'.$post->account_name.'</label>
                        <div class="col-xl-8 col-lg-8">
                            <span class="m-form__control-static"> 
                                <strong>'.translate('Number').' :</strong>  &nbsp;'.$post->account_number.'
                            </span>
                        </div>
                    </div>
                </div>';
            }
        }
        $petty_cash_accounts = $this->petty_cash_accounts_m->get_group_petty_cash_accounts();
        if($petty_cash_accounts){
            foreach ($petty_cash_accounts as $key => $post) {
                if($bank_key >0){
                    $bank_accounts_arr.='<div class="m-separator m-separator--dashed m-separator--lg"></div>';
                }
                $bank_key++;
                $bank_accounts_arr.= 
                '<div class="m-form__section m-form__section--first">
                    <div class="m-form__heading">
                        <h4 class="m-form__heading-title">Petty Cash Account</h4>
                    </div>
                    <div class="form-group m-form__group m-form__group--sm row">
                        <label class="col-xl-4 col-lg-4 col-form-label"><strong>'.translate('Name').' :</strong>  &nbsp;'.$post->account_name.'</label>
                        <div class="col-xl-8 col-lg-8">
                            <span class="m-form__control-static"> 
                            </span>
                        </div>
                    </div>
                </div>';
            }
        }
        echo json_encode(array(
            'members' => $members,
            'group_roles' => $group_roles,
            'contributions' => $contributions_arr,
            'loan_types' => $loan_types_arr,
            'bank_accounts' => $bank_accounts_arr,
        ));
    }
}