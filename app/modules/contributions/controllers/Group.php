<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Group extends Group_Controller{

	protected $validation_rules = array(
        array(
            'field' => 'name',
            'label' => 'Contribution Name',
            'rules' => 'trim|required',
        ),
        array(
            'field' => 'category',
            'label' => 'Contribution category',
            'rules' => 'trim|numeric|required',
        ),array(
            'field' => 'type',
            'label' => 'Contribution Type',
            'rules' => 'trim|numeric|required',
        ),array(
            'field' => 'amount',
            'label' => 'Contribution Amount',
            'rules' => 'trim|required|currency',
        ),array(
            'field' => 'regular_invoicing_active',
            'label' => 'Activate Invoicing',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'one_time_invoicing_active',
            'label' => 'Activate Invoicing',
            'rules' => 'trim|numeric',
        ), array(
            'field' => 'contribution_days_option',
            'label' => 'Contribution Days Option',
            'rules' => 'trim|numeric',
        ), array(
            'field' => 'first_contribution_day_option',
            'label' => 'First Contribution Days Option',
            'rules' => 'trim|numeric',
        ), array(
            'field' => 'invoice_date',
            'label' => 'Invoice Date',
            'rules' => 'trim|required',
        ),array(
            'field' => 'contribution_date',
            'label' => 'Contribution Date/Due Date',
            'rules' => 'trim|required',
        ),array(
            'field' => 'contribution_frequency',
            'label' => 'How often do members contribute',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'month_day_monthly',
            'label' => 'Day of the Month',
            'rules' => 'trim|numeric',
        ),
         array(
            'field' => 'twice_every_one_month',
            'label' => 'Contribution Date Frequency',
            'rules' => 'trim|numeric',
        ),
         array(
            'field' => 'after_first_day_week_multiple',
            'label' => 'First Day of the Week',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'after_first_starting_day',
            'label' => 'First Date  of  Contribution',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'after_first_contribution_day_option',
            'label' => 'First Contribution Day Option',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'after_second_contribution_day_option',
            'label' => 'Second Contribution Day Option',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'after_second_day_week_multiple',
            'label' => 'Second Day of the Week',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'after_second_starting_day',
            'label' => 'The Second Date  of  Contribution',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'starting_days',
            'label' => 'Contribution Start Date Frequency',
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
            'field' => 'sms_notifications_enabled',
            'label' => 'Enable SMS Notifications',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'email_notifications_enabled',
            'label' => 'Enable Email Notifications',
            'rules' => 'trim|numeric',
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
        ),array(
            'field' => 'enable_contribution_summary_display_configuration',
            'label' => 'Enable contribution summary display configuration',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'display_contribution_arrears_cumulatively',
            'label' => 'Enable display of contribution arrears as a cumulative',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'is_non_refundable',
            'label' => 'Is this contribution non refundable',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'is_equity',
            'label' => 'Is equity',
            'rules' => 'trim|numeric',
        ),
        array(
            'field' => 'enable_checkoff',
            'label' => 'Enable Checkoff',
            'rules' => 'trim|numeric',
        ),
        array(
            'field' => 'enable_deposit_statement_display',
            'label' => 'Enable Deposit Statement Display',
            'rules' => 'trim|numeric',
        ),
        
    );

    function _conditional_validation_rules(){
        if($this->input->post('regular_invoicing_active')){
            if($this->input->post('type') == 1){
                $this->validation_rules[] = array(
                    'field' => 'contribution_frequency',
                    'label' => 'How often do members contribute',
                    'rules' => 'trim|numeric|required',
                );
                $this->validation_rules[] = array(
                    'field' => 'invoice_days',
                    'label' => 'When do want to send invoices to members',
                    'rules' => 'trim|numeric',
                );
            }
            
            if($this->input->post('contribution_frequency')==1){
                //monthly
                $this->validation_rules[] =
                     array(
                        'field' => 'month_day_monthly',
                        'label' => 'When do members contribute',
                        'rules' => 'trim|required|numeric'
                    );

                $this->validation_rules[] =
                     array(
                        'field' => 'week_day_monthly',
                        'label' => 'Week Day',
                        'rules' => 'trim|numeric'
                    );
            }else if($this->input->post('contribution_frequency')==6){
               //once a week
                $this->validation_rules[] =
                     array(
                        'field' => 'week_day_weekly',
                        'label' => 'When do members contribute',
                        'rules' => 'trim|required'
                    );
            }else if($this->input->post('contribution_frequency')==7){
                //once in two weeks
                $this->validation_rules[] =
                    array(
                        'field' => 'week_day_fortnight',
                        'label' => 'When do members contribute',
                        'rules' => 'trim|required'
                    );
                $this->validation_rules[] =
                    array(
                        'field' => 'week_number_fortnight',
                        'label' => 'When do members contribute Week Number',
                        'rules' => 'trim|required'
                    );
            }else if($this->input->post('contribution_frequency')==2||$this->input->post('contribution_frequency')==3||$this->input->post('contribution_frequency')==4||$this->input->post('contribution_frequency')==5){
                //multiple months
                 $this->validation_rules[] =
                     array(
                        'field' => 'month_day_multiple',
                        'label' => 'When do members contribute',
                        'rules' => 'trim|required|numeric'
                    );

                 $this->validation_rules[] =
                     array(
                        'field' => 'week_day_multiple',
                        'label' => 'Week Day',
                        'rules' => 'trim|numeric'
                    );

                $this->validation_rules[] =
                     array(
                        'field' => 'start_month_multiple',
                        'label' => 'Starting Month',
                        'rules' => 'trim|required|numeric'
                    );
            }else if($this->input->post('contribution_frequency')==8){

            }else if($this->input->post('contribution_frequency') ==9){
                $this->validation_rules[] = array(
                    'field' => 'after_first_contribution_day_option',
                    'label' => 'First contribution day option',
                    'rules' => 'trim|required|numeric',
                );
                $this->validation_rules[] = array(
                    'field' => 'after_first_day_week_multiple',
                    'label' => 'First Day of the Week',
                    'rules' => 'trim|required|numeric',
                );
                $this->validation_rules[] = array(
                    'field' => 'after_first_starting_day',
                    'label' => 'First Date  of  Contribution',
                    'rules' => 'trim|numeric|required',
                );
                $this->validation_rules[] =array(
                    'field' => 'after_second_contribution_day_option',
                    'label' => 'Second Contribution Day Option',
                    'rules' => 'trim|numeric|required',
                );
                $this->validation_rules[] = array(
                    'field' => 'after_second_day_week_multiple',
                    'label' => 'Second Day of the Week',
                    'rules' => 'trim|numeric|required',
                );
                $this->validation_rules[] = array(
                    'field' => 'after_second_starting_day',
                    'label' => 'The Second Date  of  Contribution',
                    'rules' => 'trim|numeric|required',
                );
            }

        }

        if($this->input->post('one_time_invoicing_active')){
            $this->validation_rules[] = array(
                'field' => 'invoice_date',
                'label' => 'Invoice Date',
                'rules' => 'trim|required|callback_check_if_invoice_date_is_less_than_contribution_date',
            );
            $this->validation_rules[] = array(
                'field' => 'contribution_date',
                'label' => 'Contribution Date',
                'rules' => 'trim|required',
            );
        }


        if($this->input->post('sms_notifications_enabled')){
            $this->validation_rules[] = array(
                'field' => 'sms_template',
                'label' => 'SMS Template',
                'rules' => 'trim',
            );

            $this->validation_rules[] = array(
                'field' => 'sms_template',
                'label' => 'SMS Template',
                'rules' => 'trim',
            );
        }

        if($this->input->post('enable_contribution_member_list')){
            $this->validation_rules[] = array(
                'field' => 'contribution_member_list',
                'label' => 'Contribution member list',
                'rules' => 'callback_check_contribution_member_list',
            );
        }
    }

    

    protected $sms_template_default = '';

	function __construct(){
        parent::__construct();
        $this->load->model('contributions_m');
        $this->load->model('members/members_m');
        $this->load->library('contribution_invoices');
        $this->sms_template_default = $this->contribution_invoices->sms_template_default;
        $this->group_member_options = $this->members_m->get_group_member_options();
        $data['contribution_type_options'] = $this->contribution_invoices->contribution_type_options;
        $data['contribution_category_options'] = $this->contribution_invoices->contribution_category_options;
    }
    
    function create(){
    	$data = array();
    	$post = new stdClass();
        $posts = $_POST;
        $this->_conditional_validation_rules();
        $this->form_validation->set_rules($this->validation_rules);
        $fine_entries_are_valid = TRUE;
        //print_r($_POST);  
        if($this->input->post('enable_fines')){
            if(isset($posts['fine_type'])){ 
                $count = 0; foreach($posts['fine_type'] as $fine_type):
                    if($fine_type){
                        if($fine_type==1){
                            if($posts['fixed_amount'][$count]&&$posts['fixed_fine_mode'][$count]&&$posts['fixed_fine_chargeable_on'][$count]){
                                if(is_numeric(currency($posts['fixed_amount'][$count]))&&is_numeric($posts['fixed_fine_mode'][$count])&&is_numeric($posts['fixed_fine_frequency'][$count])&&is_numeric($posts['fine_limit'][$count])){
                                    //do for nothing now
                                }else{
                                    $fine_entries_are_valid = FALSE;
                                }
                            }else{
                                $fine_entries_are_valid = FALSE;
                            }
                        }else if($fine_type==2){
                            if($posts['percentage_rate'][$count]&&$posts['percentage_fine_on'][$count]&&$posts['percentage_fine_chargeable_on'][$count]&&$posts['percentage_fine_mode'][$count]){
                                if(is_numeric($posts['percentage_rate'][$count])&&is_numeric($posts['percentage_fine_on'][$count])&&is_numeric($posts['percentage_fine_mode'][$count])&&is_numeric($posts['fine_limit'][$count])&&is_numeric($posts['percentage_fine_frequency'][$count])){
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
        }
        
        if($this->form_validation->run()&&$fine_entries_are_valid){
            $input = array(
                'name' => $this->input->post('name'),
                'amount' => $this->input->post('amount'),
                'type' => $this->input->post('type'),
                'regular_invoicing_active' => $this->input->post('regular_invoicing_active')?1:0,
                'one_time_invoicing_active' => $this->input->post('one_time_invoicing_active')?1:0,
                'enable_contribution_summary_display_configuration' => $this->input->post('enable_contribution_summary_display_configuration')?1:0,
                'display_contribution_arrears_cumulatively' => $this->input->post('display_contribution_arrears_cumulatively')?1:0,
                'is_non_refundable' => $this->input->post('is_non_refundable')?1:0,
                'is_equity' => $this->input->post('is_equity')?1:0,
                'active' => 1,
                'group_id' => $this->group->id,
                'is_hidden' => 0,
                'created_by' => $this->user->id,
                'created_on' => time(),
            );     
            if($contribution_id = $this->contributions_m->insert($input)){
                $_POST['invoice_days'] = '3';
                if($this->input->post('type')==1){
                    $contribution_date = $this->_contribution_date();
                    $second_contribution_date = $this->_second_contribution_date();
                    //$invoice_date = $contribution_date - (24*60*60*$this->input->post('invoice_days'));
                    if($this->session->flashdata('error')){//delete inserted id
                        //echo $this->session->flashdata('error'); die();
                        redirect('group/contributions/create');
                    }else{
                         //print_r($contribution_date); die();                       
                        $invoice_date = $contribution_date - (24*60*60*$this->input->post('invoice_days'));
                        $regular_contribution_settings_input = array(
                            'contribution_id'=>$contribution_id,
                            'group_id'=>$this->group->id,
                            'invoice_date'=>$invoice_date,
                            'contribution_date'=>$contribution_date,
                            'after_second_contribution_date'=>$second_contribution_date,
                            'contribution_frequency'=>$this->input->post('contribution_frequency'),
                            'invoice_days'=>$this->input->post('invoice_days'),
                            'sms_notifications_enabled'=>$this->input->post('sms_notifications_enabled')?1:0,
                            'email_notifications_enabled'=>$this->input->post('email_notifications_enabled')?1:0,
                            'sms_template'=>$this->sms_template_default,//start                      
                            'after_first_contribution_day_option'=>$this->input->post('after_first_contribution_day_option'),
                            'after_first_day_week_multiple'=>$this->input->post('after_first_day_week_multiple'),
                            'after_first_starting_day'=>$this->input->post('after_first_starting_day'),
                            'after_second_contribution_day_option'=>$this->input->post('after_second_contribution_day_option'),
                            'after_second_day_week_multiple'=>$this->input->post('after_second_day_week_multiple'),
                            'after_second_starting_day'=>$this->input->post('after_second_starting_day'),//end
                            'month_day_monthly'=>$this->input->post('month_day_monthly'),
                            'week_day_monthly'=>$this->input->post('week_day_monthly'),
                            'week_day_weekly'=>$this->input->post('week_day_weekly'),
                            'week_day_fortnight'=>$this->input->post('week_day_fortnight'),
                            'week_number_fortnight'=>$this->input->post('week_number_fortnight'),
                            'month_day_multiple'=>$this->input->post('month_day_multiple'),
                            'week_day_multiple'=>$this->input->post('week_day_multiple'),
                            'start_month_multiple'=>$this->input->post('start_month_multiple'),
                            'disable_overpayments'=>$this->input->post('disable_overpayments'),
                            'enable_fines'=>$this->input->post('enable_fines'),
                            'enable_contribution_member_list'=>$this->input->post('enable_contribution_member_list')?1:0,
                            'active'=>1,
                            'created_by'=>$this->user->id,
                            'created_on'=>time(),
                        );
                            //print_r($regular_contribution_settings_input); die();
                        if($regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($contribution_id)){
                            if($result = $this->contributions_m->update_regular_contribution_setting($regular_contribution_setting->id,$regular_contribution_settings_input)){
                                //do nothing for now
                            }else{
                                $this->session->set_flashdata('error','Could not save changes to regular contribution setting');
                            }
                        }else{
                            if($regular_contribution_setting_id = $this->contributions_m->insert_regular_contribution_setting($regular_contribution_settings_input)){
                                //do nothing for now
                            }else{
                                $this->session->set_flashdata('error','Could create regular contribution setting');
                            }
                        }
                 }                   
                    
                }else if($this->input->post('type')==2){
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
                        'enable_fines'=>$this->input->post('enable_fines'),
                        'enable_contribution_member_list'=>$this->input->post('enable_contribution_member_list')?1:0,
                        'active'=>1,
                        'invoices_queued'=>0,
                        'created_by'=>$this->user->id,
                        'created_on'=>time(),
                    );
                    if($one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($contribution_id)){
                        if($result = $this->contributions_m->update_one_time_contribution_setting($one_time_contribution_setting->id,$one_time_contribution_settings_input)){
                            //do nothing for now
                        }else{
                            $this->session->set_flashdata('error','Could not save changes to one time contribution setting');
                        }
                    }else{
                        if($one_time_contribution_setting_id = $this->contributions_m->insert_one_time_contribution_setting($one_time_contribution_settings_input)){
                            //do nothing for now
                        }else{
                            $this->session->set_flashdata('error','Could create one time contribution setting');
                        }
                    }
                }

                if($this->input->post('type')==1||$this->input->post('type')==2){
                    if($this->input->post('enable_fines')){
                        if(isset($posts['fine_type'])){ 
                            $count = 0; foreach($posts['fine_type'] as $fine_type):
                                if($fine_type){
                                    $input = array(
                                        'contribution_id'=>$contribution_id,
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
                                        'fine_date'=>$this->_fine_date($contribution_date,$fine_type,$posts['fixed_fine_chargeable_on'][$count],$posts['percentage_fine_chargeable_on'][$count]),
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
                    }

                    if($this->input->post('enable_contribution_member_list')){
                        $group_member_ids = $this->input->post('contribution_member_list');
                        foreach($group_member_ids as $member_id){
                            $input = array(
                                'member_id'=>$member_id,
                                'group_id'=>$this->group->id,
                                'contribution_id'=>$contribution_id,
                                'created_on'=>time(),
                                'created_by'=>$this->user->id,
                            );
                            if($contribution_member_pairing_id = $this->contributions_m->insert_contribution_member_pairing($input)){

                            }else{
                                $this->session->set_flashdata('error','Could not insert contribution member pairing');
                            }
                        }
                    }
                }
                $this->setup_tasks_tracker->set_completion_status('create-contribution',$this->group->id,$this->user->id);
                $this->session->set_flashdata('success','Contribution created successfully.');
            }else{
                $this->session->set_flashdata('error','Contribution could not be created.');
            }
            redirect('group/contributions/listing');
        }else{
            if($fine_entries_are_valid==FALSE){
                $this->session->set_flashdata('error','Please enter all the fields in the fine settings category, there are some entries missing.');
            }else{
                $this->session->set_flashdata('error','');
            }
        	foreach ($this->validation_rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        preg_match_all("/\[[^\]]*\]/", $this->sms_template_default,$placeholders);
        $data['placeholders'] = isset($placeholders[0])?$placeholders[0]:array();
        $data['invoice_days'] = $this->contribution_invoices->invoice_days;
        $data['month_days'] = $this->contribution_invoices->month_days;
        $data['week_days'] = $this->contribution_invoices->week_days;
        $data['days_of_the_month'] = $this->contribution_invoices->days_of_the_month;
        $data['every_two_week_days'] = $this->contribution_invoices->every_two_week_days;
        $data['months'] = $this->contribution_invoices->months;
        $data['contribution_days_option']=$this->contribution_invoices->contribution_days_option;        
        $data['starting_days'] = $this->contribution_invoices->starting_days;
        $data['twice_every_one_month'] = $this->contribution_invoices->twice_every_one_month;
        $data['starting_months'] = $this->contribution_invoices->starting_months;
        $data['week_numbers'] = $this->contribution_invoices->week_numbers;
        $data['contribution_frequency_options'] = $this->contribution_invoices->contribution_frequency_options;
        $data['contribution_type_options'] = $this->contribution_invoices->contribution_type_options;
        $data['sms_template_default'] = $this->sms_template_default;
        $data['fine_types'] = $this->contribution_invoices->fine_types;
        $data['fine_chargeable_on_options'] = $this->contribution_invoices->fine_chargeable_on_options;
        $data['fine_frequency_options'] = $this->contribution_invoices->fine_frequency_options;
        $data['fine_mode_options'] = $this->contribution_invoices->fine_mode_options;
        $data['fine_limit_options'] = $this->contribution_invoices->fine_limit_options;
        $data['percentage_fine_on_options'] = $this->contribution_invoices->percentage_fine_on_options;
        $data['contribution_type_options'] = $this->contribution_invoices->contribution_type_options;
        $data['contribution_category_options'] = $this->contribution_invoices->contribution_category_options;
        $data['selected_group_members'] = array();
        $data['contribution_fine_settings'] = array();
        $data['post'] = $post;
        $data['posts'] = $posts;
        //print_r($data['twice_every_one_month']); 
        $this->template->title('Create Contribution')->build('group/form',$data);
    }

    function ajax_create(){
        $data = array();
        $post = new stdClass();
        $posts = $_POST;
        $this->_conditional_validation_rules();
        $this->form_validation->set_rules($this->validation_rules);
        $fine_entries_are_valid = TRUE;
        if($this->input->post('enable_fines')){
            if(isset($posts['fine_type'])){ 
                $count = 0; foreach($posts['fine_type'] as $fine_type):
                    if($fine_type){
                        if($fine_type==1){
                            if($posts['fixed_amount'][$count]&&$posts['fixed_fine_mode'][$count]&&$posts['fixed_fine_chargeable_on'][$count]){
                                if(is_numeric(currency($posts['fixed_amount'][$count]))&&is_numeric($posts['fixed_fine_mode'][$count])&&is_numeric($posts['fixed_fine_frequency'][$count])&&is_numeric($posts['fine_limit'][$count])){
                                    //do for nothing now
                                }else{
                                    $fine_entries_are_valid = FALSE;
                                }
                            }else{
                                $fine_entries_are_valid = FALSE;
                            }
                        }else if($fine_type==2){
                            if($posts['percentage_rate'][$count]&&$posts['percentage_fine_on'][$count]&&$posts['percentage_fine_chargeable_on'][$count]&&$posts['percentage_fine_mode'][$count]){
                                if(is_numeric($posts['percentage_rate'][$count])&&is_numeric($posts['percentage_fine_on'][$count])&&is_numeric($posts['percentage_fine_mode'][$count])&&is_numeric($posts['fine_limit'][$count])&&is_numeric($posts['percentage_fine_frequency'][$count])){
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
        }      
        if($this->form_validation->run()&&$fine_entries_are_valid){
            $input = array(
                'name' => $this->input->post('name'),
                'amount' => $this->input->post('amount'),
                'type' => $this->input->post('type'),
                'regular_invoicing_active' => $this->input->post('regular_invoicing_active')?1:0,
                'one_time_invoicing_active' => $this->input->post('one_time_invoicing_active')?1:0,
                'enable_contribution_summary_display_configuration' => $this->input->post('enable_contribution_summary_display_configuration')?1:0,
                'display_contribution_arrears_cumulatively' => $this->input->post('display_contribution_arrears_cumulatively')?1:0,
                'active' => 1,
                'group_id' => $this->group->id,
                'is_hidden' => 0,
                'created_by' => $this->user->id,
                'created_on' => time(),
            );     
            if($contribution_id = $this->contributions_m->insert($input)){
                $_POST['invoice_days'] = '3';
                if($this->input->post('type')==1){
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
                        'enable_fines'=>$this->input->post('enable_fines'),
                        'enable_contribution_member_list'=>$this->input->post('enable_contribution_member_list')?1:0,
                        'active'=>1,
                        'created_by'=>$this->user->id,
                        'created_on'=>time(),
                    );
                    if($regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($contribution_id)){
                        if($result = $this->contributions_m->update_regular_contribution_setting($regular_contribution_setting->id,$regular_contribution_settings_input)){
                            //do nothing for now
                        }else{
                            $this->session->set_flashdata('error','Could not save changes to regular contribution setting');
                        }
                    }else{
                        if($regular_contribution_setting_id = $this->contributions_m->insert_regular_contribution_setting($regular_contribution_settings_input)){
                            //do nothing for now
                        }else{
                            $this->session->set_flashdata('error','Could create regular contribution setting');
                        }
                    }
                }else if($this->input->post('type')==2){
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
                        'enable_fines'=>$this->input->post('enable_fines'),
                        'enable_contribution_member_list'=>$this->input->post('enable_contribution_member_list')?1:0,
                        'active'=>1,
                        'invoices_queued'=>0,
                        'created_by'=>$this->user->id,
                        'created_on'=>time(),
                    );
                    if($one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($contribution_id)){
                        if($result = $this->contributions_m->update_one_time_contribution_setting($one_time_contribution_setting->id,$one_time_contribution_settings_input)){
                            //do nothing for now
                        }else{
                            $this->session->set_flashdata('error','Could not save changes to one time contribution setting');
                        }
                    }else{
                        if($one_time_contribution_setting_id = $this->contributions_m->insert_one_time_contribution_setting($one_time_contribution_settings_input)){
                            //do nothing for now
                        }else{
                            $this->session->set_flashdata('error','Could create one time contribution setting');
                        }
                    }
                }
                if($this->input->post('type')==1||$this->input->post('type')==2){
                    if($this->input->post('enable_fines')){
                        if(isset($posts['fine_type'])){ 
                            $count = 0; foreach($posts['fine_type'] as $fine_type):
                                if($fine_type){
                                    $input = array(
                                        'contribution_id'=>$contribution_id,
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
                                        'fine_date'=>$this->_fine_date($contribution_date,$fine_type,$posts['fixed_fine_chargeable_on'][$count],$posts['percentage_fine_chargeable_on'][$count]),
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
                    }

                    if($this->input->post('enable_contribution_member_list')){
                        $group_member_ids = $this->input->post('contribution_member_list');
                        foreach($group_member_ids as $member_id){
                            $input = array(
                                'member_id'=>$member_id,
                                'group_id'=>$this->group->id,
                                'contribution_id'=>$contribution_id,
                                'created_on'=>time(),
                                'created_by'=>$this->user->id,
                            );
                            if($contribution_member_pairing_id = $this->contributions_m->insert_contribution_member_pairing($input)){

                            }else{
                                $this->session->set_flashdata('error','Could not insert contribution member pairing');
                            }
                        }
                    }
                }
                $this->setup_tasks_tracker->set_completion_status('create-contribution',$this->group->id,$this->user->id);
                if($contribution = $this->contributions_m->get_group_contribution($contribution_id)){
                    echo json_encode($contribution);
                }else{
                    echo "Could not find contribution";
                }
            }else{
                echo 'Contribution could not be created.';
            }
        }else{
            if($fine_entries_are_valid==FALSE){
                $error_message = '<p> Please enter all the fields in the fine settings category, there are some entries missing.</p>';
            }else{
                $error_message = "";
            }
            echo validation_errors().$error_message;
        }
    }

    function index(){
        $data = array();
        $total_rows = $this->contributions_m->count_group_contributions();
        $pagination = create_pagination('group/contributions/listing/pages', $total_rows,50,5,TRUE);
        $data['posts'] = $this->contributions_m->limit($pagination['limit'])->get_group_contributions();
        $data['pagination'] = $pagination;
        $data['contribution_type_options'] = $this->contribution_invoices->contribution_type_options;
        $data['month_days'] = $this->contribution_invoices->month_days;
        $data['week_days'] = $this->contribution_invoices->week_days;
        $data['days_of_the_month'] = $this->contribution_invoices->days_of_the_month;
        $data['every_two_week_days'] = $this->contribution_invoices->every_two_week_days;
        $data['months'] = $this->contribution_invoices->months;
        $data['starting_months'] = $this->contribution_invoices->starting_months;
        $data['starting_days'] = $this->contribution_invoices->starting_days;
        $data['week_numbers'] = $this->contribution_invoices->week_numbers;
        $data['contribution_days_option'] = $this->contribution_invoices->contribution_days_option;
        $data['regular_contribution_settings_array'] = $this->contributions_m->get_group_regular_contribution_settings_array();
        $data['one_time_contribution_settings_array'] = $this->contributions_m->get_group_one_time_contribution_settings_array();
        $data['selected_group_members_array'] = $this->contributions_m->get_all_contribution_member_pairings_array();
        $data['contribution_fine_settings_array'] = $this->contributions_m->get_all_contribution_fine_settings_array();
        $data['contribution_frequency_options'] = $this->contribution_invoices->contribution_frequency_options;
        $data['contribution_type_options'] = $this->contribution_invoices->contribution_type_options;
        $data['fine_types'] = $this->contribution_invoices->fine_types;
        $data['fine_chargeable_on_options'] = $this->contribution_invoices->fine_chargeable_on_options['Frequently used options']+$this->contribution_invoices->fine_chargeable_on_options['Other options'];
        $data['fine_frequency_options'] = $this->contribution_invoices->fine_frequency_options;
        $data['fine_mode_options'] = $this->contribution_invoices->fine_mode_options;
        $data['fine_limit_options'] = $this->contribution_invoices->fine_limit_options;
        $data['percentage_fine_on_options'] = $this->contribution_invoices->percentage_fine_on_options;
        $data['group_member_options'] = $this->group_member_options;
        //print_r($data); die();
        $this->template->title(translate('List Contributions'))->build('group/listing',$data);
    }

    function listing(){
        redirect('group/contributions/');
    }

    function ajax_listing(){
        $posts = $this->contributions_m->get_group_contributions();
        $contribution_type_options = $this->contribution_invoices->contribution_type_options;
        $month_days = $this->contribution_invoices->month_days;
        $week_days = $this->contribution_invoices->week_days;
        $days_of_the_month = $this->contribution_invoices->days_of_the_month;
        $every_two_week_days = $this->contribution_invoices->every_two_week_days;
        $months = $this->contribution_invoices->months;
        $starting_months = $this->contribution_invoices->starting_months;
        $week_numbers = $this->contribution_invoices->week_numbers;
        $contribution_frequency_options = $this->contribution_invoices->contribution_frequency_options;
        $regular_contribution_settings_array = $this->contributions_m->get_group_regular_contribution_settings_array();
        $one_time_contribution_settings_array = $this->contributions_m->get_group_one_time_contribution_settings_array();
        $selected_group_members_array = $this->contributions_m->get_all_contribution_member_pairings_array();
        $contribution_fine_settings_array = $this->contributions_m->get_all_contribution_fine_settings_array();
        $contribution_frequency_options = $this->contribution_invoices->contribution_frequency_options;
        $contribution_type_options = $this->contribution_invoices->contribution_type_options;
        $fine_types = $this->contribution_invoices->fine_types;
        $fine_chargeable_on_options = $this->contribution_invoices->fine_chargeable_on_options['Frequently used options']+$this->contribution_invoices->fine_chargeable_on_options['Other options'];
        $fine_frequency_options = $this->contribution_invoices->fine_frequency_options;
        $fine_mode_options = $this->contribution_invoices->fine_mode_options;
        $fine_limit_options = $this->contribution_invoices->fine_limit_options;
        $percentage_fine_on_options = $this->contribution_invoices->percentage_fine_on_options;
        $group_member_options = $this->group_member_options;
        if(empty($posts)){
            echo '<div class="alert alert-info">
                <h4 class="block">Information! No records to display</h4>
                <p>
                    No Contributions to display.
                </p>
            </div>';
        }else{
            $i = 1;
            echo'
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th width="2%">
                            #
                        </th>
                        <th width="20%">
                            Name
                        </th>
                        <th>
                            Contribution Particulars
                        </th>
                        <th width="20%" class="text-right">
                            Amount ('.$this->group_currency.')
                        </th>  
                        <th>
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>';
                    foreach($posts as $post): 
                        if($post->type==1){
                            echo '
                            <tr data-id="'.$post->id.'">
                                <td>'.$i++.'</td>
                                <td class="name" data-name="'.$post->name.'">'.$post->name.'</td>
                                <td class="type" data-type="'.$post->type.'" data-regular-invoicing-active="'.$post->regular_invoicing_active
                                .'"  >';
                                        if($post->regular_invoicing_active){
                                            $regular_contribution_setting = isset($regular_contribution_settings_array[$post->id])?$regular_contribution_settings_array[$post->id]:'';
                                            if($regular_contribution_setting){ 
                                                echo '<strong>Contribution Type: </strong>'.$contribution_type_options[$post->type];
                                                $week_day_monthly = $regular_contribution_setting->week_day_monthly?$regular_contribution_setting->week_day_monthly:0;
                                                $week_day_multiple = $regular_contribution_setting->week_day_multiple?$regular_contribution_setting->week_day_multiple:0;
                                                echo '<br/><strong class="contribution_frequency month_day_monthly week_day_monthly week_day_weekly week_day_fortnight week_number_fortnight month_day_multiple week_day_multiple start_month_multiple" data-start-month-multiple="'.$regular_contribution_setting->start_month_multiple.'" data-week-day-multiple="'.$regular_contribution_setting->week_day_multiple.'" data-month-day-multiple="'.$regular_contribution_setting->month_day_multiple.'" data-week-number-fortnight="'.$regular_contribution_setting->week_number_fortnight.'" data-week-day-fortnight="'.$regular_contribution_setting->week_day_fortnight.'" data-week-day-weekly="'.$regular_contribution_setting->week_day_weekly.'"  data-contribution-frequency="'.$regular_contribution_setting->contribution_frequency.'" data-month-day-monthly="'.$regular_contribution_setting->month_day_monthly.'" data-week-day-monthly="'.$week_day_monthly.'">Contribution Details: </strong>'; 
                                                if($regular_contribution_setting->contribution_frequency==1){
                                                    //Once a month
                                                    echo $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$days_of_the_month[$regular_contribution_setting->month_day_monthly].' '.$month_days[$regular_contribution_setting->week_day_monthly?$regular_contribution_setting->week_day_monthly:0];
                                                }else if($regular_contribution_setting->contribution_frequency==6){
                                                    //Weekly
                                                    echo $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$week_days[$regular_contribution_setting->week_day_weekly];
                                                }else if($regular_contribution_setting->contribution_frequency==7){
                                                    //Fortnight or every two weeks
                                                    echo $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$every_two_week_days[$regular_contribution_setting->week_day_fortnight].' '.$week_numbers[$regular_contribution_setting->week_number_fortnight];
                                                }else if($regular_contribution_setting->contribution_frequency==2||$regular_contribution_setting->contribution_frequency==3||$regular_contribution_setting->contribution_frequency==4||$regular_contribution_setting->contribution_frequency==5){
                                                    //Multiple months
                                                    echo $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].',
                                                    '.$days_of_the_month[$regular_contribution_setting->month_day_multiple].'
                                                    '.$month_days[$regular_contribution_setting->week_day_multiple?$regular_contribution_setting->week_day_multiple:0].', 
                                                    '.$starting_months[$regular_contribution_setting->start_month_multiple];
                                                }else if($regular_contribution_setting->contribution_frequency==8){
                                                    echo $contribution_frequency_options[$regular_contribution_setting->contribution_frequency];
                                                }
                                                echo '<br/><strong>Invoice Date: </strong>'.timestamp_to_date($regular_contribution_setting->invoice_date);
                                                echo '<br/><strong>Contribution Date: </strong>'.timestamp_to_date($regular_contribution_setting->contribution_date);
                                                echo '<br/><strong>SMS Notifications: </strong>'; echo $regular_contribution_setting->sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>"; 
                                                echo '<br/><strong>Email Notifications: </strong>';echo $regular_contribution_setting->email_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                                echo '<br/><strong>Fines: </strong>';echo $regular_contribution_setting->enable_fines?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                                echo '<br/>';
                                                if($regular_contribution_setting->enable_contribution_member_list){
                                                    echo '<hr/>';
                                                    if(isset($selected_group_members_array[$post->id])){
                                                        $group_members = $selected_group_members_array[$post->id];
                                                        $count = 1;
                                                        echo '<strong>Members to be invoiced: </strong><br/>';
                                                        foreach($group_members as $member_id){
                                                            if(isset($group_member_options[$member_id])){
                                                                if($count==1){
                                                                    echo $group_member_options[$member_id];
                                                                }else{
                                                                    echo ','.$group_member_options[$member_id];
                                                                }
                                                                $count++;
                                                            }
                                                        }
                                                    }
                                                }else{
                                                    echo '<strong>All members to be invoiced </strong><br/>';
                                                }
                                                if($regular_contribution_setting->enable_fines){
                                                    if(isset($contribution_fine_settings_array[$post->id])){
                                                        echo '<hr/>';
                                                        echo '<strong>Contribution fine settings: </strong><br/>';
                                                        $contribution_fine_settings = $contribution_fine_settings_array[$post->id];
                                                        $count = 1;
                                                        foreach ($contribution_fine_settings as $contribution_fine_setting) {
                                                            if($count>1){
                                                                echo '<br/>';
                                                            }
                                                            echo '<strong>Fine setting #'.$count.' - '.$contribution_fine_setting->id.'<br/></strong>';
                                                            echo '<strong>Fine Date</strong> '.timestamp_to_date($contribution_fine_setting->fine_date).'<br/>';
                                                            if($contribution_fine_setting->fine_type==1){
                                                                echo $fine_types[$contribution_fine_setting->fine_type];
                                                                echo ' '.$this->group_currency.' '.number_to_currency($contribution_fine_setting->fixed_amount);
                                                                echo ' '.$fine_mode_options[$contribution_fine_setting->fixed_fine_mode];
                                                                echo ' '.$fine_chargeable_on_options[$contribution_fine_setting->fixed_fine_chargeable_on];
                                                                echo ' '.$fine_frequency_options[$contribution_fine_setting->fixed_fine_frequency];
                                                                echo ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
                                                            }else if($contribution_fine_setting->fine_type==2){
                                                                echo $fine_types[$contribution_fine_setting->fine_type];
                                                                echo ' '.$contribution_fine_setting->percentage_rate.' % ';
                                                                echo ' '.$percentage_fine_on_options[$contribution_fine_setting->percentage_fine_on];
                                                                echo ' '.$fine_chargeable_on_options[$contribution_fine_setting->percentage_fine_chargeable_on];
                                                                echo ' '.$fine_mode_options[$contribution_fine_setting->percentage_fine_mode];
                                                                echo ' '.$fine_frequency_options[$contribution_fine_setting->percentage_fine_frequency];
                                                                echo ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
                                                            }
                                                            echo '<br/><strong>SMS Notifications: </strong>'; echo $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>"; 
                                                            echo '<br/><strong>Email Notifications: </strong>';echo $contribution_fine_setting->fine_email_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span><br/>";
                                    
                                                            $count++;
                                                        }
                                                    }
                                                }
                                            }else{
                                                echo "<span class='label label-default'>Regular Contribution Setting not Available</span>";
                                            }
                                        }else{
                                            echo "<span class='label label-default'>Invoicing Disabled</span>";
                                        }
                                        if($post->enable_contribution_summary_display_configuration){
                                            echo '<br/><strong> Arrears Display :</strong>';
                                            echo $post->display_contribution_arrears_cumulatively?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                        }
                                echo '
                                </td>
                                <td class="amount" data-amount="'.$post->amount.'" class=\'text-right\'>'.number_to_currency($post->amount).'</td>
                                <td>
                                    <a href="#" class="btn btn-xs default edit_contribution full_width_inline" data-content="#contributions_form" data-title="Edit Group Contribution" data-id="edit_contribution" id="">
                                        <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
                                    </a>
                                </td>
                            </tr>';
                        }else if($post->type==2){
                            echo '
                            <tr data-id="'.$post->id.'">
                                <td>'.$i++.'</td>
                                <td class="name" data-name="'.$post->name.'">
                                    '.$post->name.'
                                </td>
                                <td class="type" data-type="'.$post->type.'">';
                                    if($post->one_time_invoicing_active){
                                        $one_time_contribution_setting = isset($one_time_contribution_settings_array[$post->id])?$one_time_contribution_settings_array[$post->id]:'';
                                        if($one_time_contribution_setting){ 
                                            echo '<strong>Contribution Type: </strong>'.$contribution_type_options[$post->type];
                                            echo '<br/><strong>Invoice Date: </strong>'.timestamp_to_date($one_time_contribution_setting->invoice_date);
                                            echo '<br/><strong>Contribution Date: </strong>'.timestamp_to_date($one_time_contribution_setting->contribution_date);
                                            echo '<br/><strong>SMS Notifications: </strong>'; echo $one_time_contribution_setting->sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>"; 
                                            echo '<br/><strong>Email Notifications: </strong>';echo $one_time_contribution_setting->email_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                            echo '<br/><strong>Fines: </strong>';echo $one_time_contribution_setting->enable_fines?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                            echo '<br/>';
                                            if($one_time_contribution_setting->enable_contribution_member_list){
                                                if(isset($selected_group_members_array[$post->id])){
                                                    echo '<hr/>';
                                                    $group_members = $selected_group_members_array[$post->id];
                                                    $count = 1;
                                                    echo '<strong>Members to be invoiced: </strong><br/>';
                                                    foreach($group_members as $member_id){
                                                        if($count==1){
                                                            echo $group_member_options[$member_id];
                                                        }else{
                                                            echo ','.$group_member_options[$member_id];
                                                        }
                                                        $count++;
                                                    }
                                                }
                                            }else{
                                                echo '<strong>All members to be invoiced </strong><br/>';
                                            }

                                            if($one_time_contribution_setting->enable_fines){
                                                if(isset($contribution_fine_settings_array[$post->id])){
                                                    echo '<hr/>';
                                                    echo '<strong>Contribution fine settings: </strong><br/>';
                                                    $contribution_fine_settings = $contribution_fine_settings_array[$post->id];
                                                    $count = 1;
                                                    foreach ($contribution_fine_settings as $contribution_fine_setting) {
                                                        if($count>1){
                                                            echo '<br/>';
                                                        }
                                                        echo '<strong>Fine setting #'.$count.' - '.$contribution_fine_setting->id.'<br/></strong>';

                                                        if($contribution_fine_setting->fine_type==1){
                                                            echo $fine_types[$contribution_fine_setting->fine_type];
                                                            echo ' '.$this->group_currency.' '.number_to_currency($contribution_fine_setting->fixed_amount);
                                                            echo ' '.$fine_mode_options[$contribution_fine_setting->fixed_fine_mode];
                                                            echo ' '.$fine_chargeable_on_options[$contribution_fine_setting->fixed_fine_chargeable_on];
                                                            echo ' '.$fine_frequency_options[$contribution_fine_setting->fixed_fine_frequency];
                                                            echo ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
                                                        }else if($contribution_fine_setting->fine_type==2){
                                                            echo $fine_types[$contribution_fine_setting->fine_type];
                                                            echo ' '.$contribution_fine_setting->percentage_rate.' % ';
                                                            echo ' '.$fine_chargeable_on_options[$contribution_fine_setting->percentage_fine_on];
                                                            echo ' '.$fine_chargeable_on_options[$contribution_fine_setting->percentage_fine_chargeable_on];
                                                            echo ' '.$fine_mode_options[$contribution_fine_setting->percentage_fine_mode];
                                                            echo ' '.$fine_frequency_options[$contribution_fine_setting->percentage_fine_frequency];
                                                            echo ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
                                                        }
                                                        echo '<br/><strong>SMS Notifications: </strong>'; echo $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>"; 
                                                        echo '<br/><strong>Email Notifications: </strong>';echo $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span><br/>";
                                
                                                        $count++;
                                                    }
                                                }
                                            }
                                        }else{
                                            echo "<span class='label label-default'>One Time Contribution Setting not Available</span>";
                                        }

                                    }else{
                                        echo "<span class='label label-default'>Invoicing Disabled</span>";
                                    }

                                    if($post->enable_contribution_summary_display_configuration){
                                        echo '<br/><strong> Display contribution arrears cumulatively:</strong>';
                                        echo $post->display_contribution_arrears_cumulatively?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                    }
                                echo'
                                </td>
                                <td class="amount" data-amount="'.$post->amount.'" class=\'text-right\'>'.number_to_currency($post->amount).'</td>
                                <td>
                                    <a href="#" class="btn btn-xs default edit_contribution full_width_inline" data-content="#contributions_form" data-title="Edit Group Contribution" data-id="edit_contribution" id="">
                                        <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
                                    </a>
                                </td>
                            </tr>';
                        }else if($post->type==3){
                            echo '
                            <tr data-id="'.$post->id.'">
                                <td>'.$i++.'</td>
                                <td class="name" data-name="'.$post->name.'">
                                    '.$post->name.'
                                </td>
                                <td class="type" data-type="'.$post->type.'">';
                                        echo '<strong>Contribution Type: </strong>'.$contribution_type_options[$post->type];
                                        
                                        if($post->enable_contribution_summary_display_configuration){
                                            echo '<br/><strong> Display contribution arrears cumulatively:</strong>';
                                            echo $post->display_contribution_arrears_cumulatively?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                        }
                                echo '
                                </td>
                                <td class="amount" data-amount="'.$post->amount.'" class=\'text-right\'>'.number_to_currency($post->amount).'</td>
                                <td>
                                    <a href="#" class="btn btn-xs default edit_contribution full_width_inline" data-content="#contributions_form" data-title="Edit Group Contribution" data-id="edit_contribution" id="">
                                        <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
                                    </a>
                                </td>
                            </tr>';
                        } 
                        endforeach;
                        echo '
                </tbody>
            </table>';
        }
    }

    function ajax_get(){
        $id = $this->input->post('id');
        if($id){
            if($post = $this->contributions_m->get_group_contribution($id)){

                if($post->type==1){
                    $regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($id);
                    $post = (object) array_merge((array) $regular_contribution_setting, (array) $post);
                }else if($post->type==2){
                    $one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($id);
                    $post = (object) array_merge((array) $one_time_contribution_setting, (array) $post);
                }else{

                }

                $selected_group_members = $this->contributions_m->get_contribution_member_pairings_array($id);

                $contribution_fine_settings = $this->contributions_m->get_contribution_fine_settings($id);
                
                $selected_group_members = array(
                    'selected_group_members' => $selected_group_members
                );

                $contribution_fine_settings = array(
                    'contribution_fine_settings' => $contribution_fine_settings
                );

                $post = (object) array_merge($contribution_fine_settings, (array) $post);

                $post = (object) array_merge($selected_group_members, (array) $post);

                echo json_encode($post);

            }else{
                echo "Could not find contribution";
            }
        }else{
            echo "Contribution ID not provided";
        }
    }

    function edit($id = 0){
        $id OR redirect('group/contributions/listing');
        $post = new stdClass();
        $post = $this->contributions_m->get_group_contribution($id);
        $post OR redirect('group/contributions/listing');
        $posts = $_POST;
        $this->_conditional_validation_rules();
        $this->form_validation->set_rules($this->validation_rules);
        if($post->type==1){
            $regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($id);
            $post = (object) array_merge((array) $regular_contribution_setting, (array) $post);
            //print_r($post); die();
        }else if($post->type==2){
            $one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($id);
            $post = (object) array_merge((array) $one_time_contribution_setting, (array) $post);
        }else{

        }
        foreach ($this->validation_rules as $key => $field) {
            $field_value = $field['field'];
            if(isset($post->$field_value)){
                //do nothing for now
            }else{
                $post->$field_value = set_value($field['field']);
            }
        }

        $fine_entries_are_valid = TRUE;
        if($this->input->post('enable_fines')){
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
        }
        if($this->form_validation->run()&&$fine_entries_are_valid){
            $input = array(
                'name' => $this->input->post('name'),
                'amount' => $this->input->post('amount'),
                'type' => $this->input->post('type'),
                'regular_invoicing_active' => $this->input->post('regular_invoicing_active')?1:0,
                'one_time_invoicing_active' => $this->input->post('one_time_invoicing_active')?1:0,
                'enable_contribution_summary_display_configuration' => $this->input->post('enable_contribution_summary_display_configuration')?1:0,
                'display_contribution_arrears_cumulatively' => $this->input->post('display_contribution_arrears_cumulatively')?1:0,
                'is_non_refundable' => $this->input->post('is_non_refundable')?1:0,
                'is_equity' => $this->input->post('is_equity')?1:0,
                'active' => 1,
                'group_id' => $this->group->id,
                'is_hidden' => 0,
                'modified_by' => $this->user->id,
                'modified_on' => time(),
            );
            if($result = $this->contributions_m->update($id,$input)){
                $_POST['invoice_days'] = '3';
                if($this->input->post('type')==1){
                        //$contribution_date = $post->contribution_date>$this->_contribution_date()?$post->contribution_date:$this->_contribution_date();
                        $contribution_date = $this->_contribution_date();
                        $second_contribution_date = $this->_second_contribution_date();
                        if($this->session->flashdata('error')){
                            die($this->session->flashdata('error'));
                            redirect('group/contributions/edit/'.$id);
                        }else{
                             //print_r($contribution_date); die();                       
                            $invoice_date = $contribution_date - (24*60*60*$this->input->post('invoice_days'));
                            echo $invoice_date;die;
                            $regular_contribution_settings_input = array(
                                'contribution_id'=>$id,
                                'group_id'=>$this->group->id,
                                'invoice_date'=>$invoice_date,
                                'contribution_date'=>$contribution_date,
                                'after_second_contribution_date'=>$second_contribution_date,
                                'contribution_frequency'=>$this->input->post('contribution_frequency'),
                                'invoice_days'=>$this->input->post('invoice_days'),
                                'sms_notifications_enabled'=>$this->input->post('sms_notifications_enabled')?1:0,
                                'email_notifications_enabled'=>$this->input->post('email_notifications_enabled')?1:0,
                                'sms_template'=>$this->sms_template_default,//start                      
                                'after_first_contribution_day_option'=>$this->input->post('after_first_contribution_day_option'),
                                'after_first_day_week_multiple'=>$this->input->post('after_first_day_week_multiple'),
                                'after_first_starting_day'=>$this->input->post('after_first_starting_day'),
                                'after_second_contribution_day_option'=>$this->input->post('after_second_contribution_day_option'),
                                'after_second_day_week_multiple'=>$this->input->post('after_second_day_week_multiple'),
                                'after_second_starting_day'=>$this->input->post('after_second_starting_day'),//end
                                'month_day_monthly'=>$this->input->post('month_day_monthly'),
                                'week_day_monthly'=>$this->input->post('week_day_monthly'),
                                'week_day_weekly'=>$this->input->post('week_day_weekly'),
                                'week_day_fortnight'=>$this->input->post('week_day_fortnight'),
                                'week_number_fortnight'=>$this->input->post('week_number_fortnight'),
                                'month_day_multiple'=>$this->input->post('month_day_multiple'),
                                'week_day_multiple'=>$this->input->post('week_day_multiple'),
                                'start_month_multiple'=>$this->input->post('start_month_multiple'),
                                'disable_overpayments'=>$this->input->post('disable_overpayments'),
                                'enable_fines'=>$this->input->post('enable_fines'),
                                'enable_contribution_member_list'=>$this->input->post('enable_contribution_member_list')?1:0,
                                'active'=>1,
                                'created_by'=>$this->user->id,
                                'created_on'=>time(),
                            );
                        } 
                       
                    //print_r($regular_contribution_settings_input); die();
                    if($regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($id)){
                        if($result = $this->contributions_m->update_regular_contribution_setting($regular_contribution_setting->id,$regular_contribution_settings_input)){
                            //do nothing for now
                        }else{
                            $this->session->set_flashdata('error','Could not save changes to regular contribution setting');
                        }
                    }else{
                        if($regular_contribution_setting_id = $this->contributions_m->insert_regular_contribution_setting($regular_contribution_settings_input)){
                            //do nothing for now
                        }else{
                            $this->session->set_flashdata('error','Could create regular contribution setting');
                        }
                    }
                }else if($this->input->post('type')==2){
                    $invoice_date = strtotime($this->input->post('invoice_date'));
                    $contribution_date = strtotime($this->input->post('contribution_date'));
                    $one_time_contribution_settings_input = array(
                        'contribution_id'=>$id,
                        'group_id'=>$this->group->id,
                        'invoice_date'=>$invoice_date,
                        'contribution_date'=>$contribution_date,
                        'disable_overpayments'=>$this->input->post('disable_overpayments'),
                        'sms_notifications_enabled'=>$this->input->post('sms_notifications_enabled')?1:0,
                        'email_notifications_enabled'=>$this->input->post('email_notifications_enabled')?1:0,
                        'sms_template'=>$this->sms_template_default,
                        'enable_fines'=>$this->input->post('enable_fines'),
                        'enable_contribution_member_list'=>$this->input->post('enable_contribution_member_list')?1:0,
                        'active'=>1,
                        'invoices_queued'=>0,
                        'modified_by'=>$this->user->id,
                        'modified_on'=>time(),
                    );
                    if($one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($id)){
                        if($result = $this->contributions_m->update_one_time_contribution_setting($one_time_contribution_setting->id,$one_time_contribution_settings_input)){
                            //do nothing for now
                            $group_ids[] = $this->group->id;
                            $member_ids = array_flip($this->active_group_member_options);
                            if($this->transactions->update_group_member_contribution_statement_balances($group_ids,$member_ids)){

                            }else{
                                $this->session->set_flashdata('error','Could not update contribution balances');
                            }
                        }else{
                            $this->session->set_flashdata('error','Could not save changes to one time contribution setting');
                        }
                    }else{
                        if($one_time_contribution_setting_id = $this->contributions_m->insert_one_time_contribution_setting($one_time_contribution_settings_input)){
                            //do nothing for now
                        }else{
                            $this->session->set_flashdata('error','Could create one time contribution setting');
                        }
                    }
                }

                if($this->input->post('type')==1||$this->input->post('type')==2){
                    if($this->input->post('enable_contribution_member_list')){
                        $this->contributions_m->delete_contribution_member_pairings($id);
                        $group_member_ids = $this->input->post('contribution_member_list');
                        foreach($group_member_ids as $member_id){
                            $input = array(
                                'member_id'=>$member_id,
                                'group_id'=>$this->group->id,
                                'contribution_id'=>$id,
                                'created_on'=>time(),
                                'created_by'=>$this->user->id,
                            );
                            if($contribution_member_pairing_id = $this->contributions_m->insert_contribution_member_pairing($input)){

                            }else{
                                $this->session->set_flashdata('error','Could not insert contribution member pairing');
                            }
                        }
                    }
                    if($this->input->post('enable_fines')){
                        $this->contributions_m->delete_contribution_fine_settings($id);
                        if(isset($posts['fine_type'])){ 
                            $count = 0; foreach($posts['fine_type'] as $fine_type):
                                if($fine_type){
                                    $contribution_date = $post->contribution_date>$this->_contribution_date()?$post->contribution_date:$this->_contribution_date();

                                    if($post->contribution_date>$this->_contribution_date()){
                                        $contribution_date = $this->_contribution_date();
                                    }

                                    $fine_date = $this->_fine_date($contribution_date,$fine_type,$posts['fixed_fine_chargeable_on'][$count],$posts['percentage_fine_chargeable_on'][$count]);
                                    $input = array(
                                        'contribution_id'=>$id,
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
                                        //'fine_date'=>isset($posts['fine_date'][$count])?(($posts['fine_date'][$count]>=strtotime('today'))?$posts['fine_date'][$count]:$fine_date):$fine_date,
                                        'fine_date'=>$fine_date>strtotime('today')?$fine_date:(isset($posts['fine_date'][$count])?(($posts['fine_date'][$count]>=strtotime('today'))?$posts['fine_date'][$count]:$fine_date):$fine_date),
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
                    }
                }

                if($this->input->post('display_contribution_arrears_cumulatively') == $post->display_contribution_arrears_cumulatively){
                    //die("Am in");
                }else{
                    $group_ids[] = $this->group->id;
                    $member_ids = array_flip($this->active_group_member_options);
                    if($this->transactions->update_group_member_contribution_statement_balances($group_ids,$member_ids)){

                    }else{
                        $this->session->set_flashdata('error','Could not update contribution balances');
                    } 
                }  

                $this->session->set_flashdata('success','Contribution changes saved successfully.');
            }else{
                $this->session->set_flashdata('error','Changes could not be saved.');
            }
            redirect('group/contributions/listing');
        }else{
            if($fine_entries_are_valid==FALSE){
                $this->session->set_flashdata('error','Please enter all the fields in the fine settings category, there are some entries missing.');
            }else{
                $this->session->set_flashdata('error','');
            }
        }
        preg_match_all("/\[[^\]]*\]/", $this->sms_template_default,$placeholders);
        $data['placeholders'] = isset($placeholders[0])?$placeholders[0]:array();
        $data['invoice_days'] = $this->contribution_invoices->invoice_days;
        $data['month_days'] = $this->contribution_invoices->month_days;
        $data['week_days'] = $this->contribution_invoices->week_days;
        $data['days_of_the_month'] = $this->contribution_invoices->days_of_the_month;
        $data['starting_days'] = $this->contribution_invoices->starting_days;
        $data['every_two_week_days'] = $this->contribution_invoices->every_two_week_days;
        $data['months'] = $this->contribution_invoices->months;
        $data['starting_months'] = $this->contribution_invoices->starting_months;
        $data['week_numbers'] = $this->contribution_invoices->week_numbers;
        $data['contribution_frequency_options'] = $this->contribution_invoices->contribution_frequency_options;        
        $data['contribution_days_option']=$this->contribution_invoices->contribution_days_option;   
        $data['contribution_type_options'] = $this->contribution_invoices->contribution_type_options;
        $data['sms_template_default'] = $this->sms_template_default;
        $data['fine_types'] = $this->contribution_invoices->fine_types;
        $data['fine_chargeable_on_options'] = $this->contribution_invoices->fine_chargeable_on_options;
        $data['fine_frequency_options'] = $this->contribution_invoices->fine_frequency_options;
        $data['fine_mode_options'] = $this->contribution_invoices->fine_mode_options;
        $data['fine_limit_options'] = $this->contribution_invoices->fine_limit_options;
        $data['percentage_fine_on_options'] = $this->contribution_invoices->percentage_fine_on_options;
        $data['selected_group_members'] = $this->contributions_m->get_contribution_member_pairings_array($id);
        $data['contribution_fine_settings'] = $this->contributions_m->get_contribution_fine_settings($id);
        $data['contribution_type_options'] = $this->contribution_invoices->contribution_type_options;
        $data['contribution_category_options'] = $this->contribution_invoices->contribution_category_options;
        $data['post'] = $post;
        $data['posts'] = $posts;
        $this->template->title('Edit Contribution')->build('group/form',$data);
    }

    function ajax_edit($id = 0){
        $id = $this->input->post('id');
        if($id){
            $post = new stdClass();
            $post = $this->contributions_m->get_group_contribution($id);

            if($post){
                $posts = $_POST;
                $this->_conditional_validation_rules();
                $this->form_validation->set_rules($this->validation_rules);
                if($post->type==1){
                    $regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($id);
                    $post = (object) array_merge((array) $regular_contribution_setting, (array) $post);
                }else if($post->type==2){
                    $one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($id);
                    $post = (object) array_merge((array) $one_time_contribution_setting, (array) $post);
                }else{

                }
                foreach ($this->validation_rules as $key => $field) {
                    if(isset($post->$field['field'])){
                        //do nothing for now
                    }else{
                        $post->$field['field'] = set_value($field['field']);
                    }
                }

                $fine_entries_are_valid = TRUE;
                if($this->input->post('enable_fines')){
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
                }
                if($this->form_validation->run()&&$fine_entries_are_valid){
                    $input = array(
                        'name' => $this->input->post('name'),
                        'amount' => $this->input->post('amount'),
                        'type' => $this->input->post('type'),
                        'regular_invoicing_active' => $this->input->post('regular_invoicing_active')?1:0,
                        'one_time_invoicing_active' => $this->input->post('one_time_invoicing_active')?1:0,
                        'enable_contribution_summary_display_configuration' => $this->input->post('enable_contribution_summary_display_configuration')?1:0,
                        'display_contribution_arrears_cumulatively' => $this->input->post('display_contribution_arrears_cumulatively')?1:0,
                        'active' => 1,
                        'group_id' => $this->group->id,
                        'is_hidden' => 0,
                        'modified_by' => $this->user->id,
                        'modified_on' => time(),
                    );
                    if($result = $this->contributions_m->update($id,$input)){
                        $_POST['invoice_days'] = '3';
                        if($this->input->post('type')==1){
                            $contribution_date = $this->_contribution_date();
                            $invoice_date = $contribution_date - (24*60*60*$this->input->post('invoice_days'));                            
                            $regular_contribution_settings_input = array(
                                'contribution_id'=>$id,
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
                                'enable_fines'=>$this->input->post('enable_fines'),
                                'enable_contribution_member_list'=>$this->input->post('enable_contribution_member_list')?1:0,
                                'active'=>1,
                                'modified_by'=>$this->user->id,
                                'modified_on'=>time(),
                            );
                            if($regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($id)){
                                if($result = $this->contributions_m->update_regular_contribution_setting($regular_contribution_setting->id,$regular_contribution_settings_input)){
                                    //do nothing for now
                                }else{
                                    $this->session->set_flashdata('error','Could not save changes to regular contribution setting');
                                }
                            }else{
                                if($regular_contribution_setting_id = $this->contributions_m->insert_regular_contribution_setting($regular_contribution_settings_input)){
                                    //do nothing for now
                                }else{
                                    $this->session->set_flashdata('error','Could create regular contribution setting');
                                }
                            }
                        }else if($this->input->post('type')==2){
                            $invoice_date = strtotime($this->input->post('invoice_date'));
                            $contribution_date = strtotime($this->input->post('contribution_date'));
                            $one_time_contribution_settings_input = array(
                                'contribution_id'=>$id,
                                'group_id'=>$this->group->id,
                                'invoice_date'=>$invoice_date,
                                'contribution_date'=>$contribution_date,
                                'disable_overpayments'=>$this->input->post('disable_overpayments'),
                                'sms_notifications_enabled'=>$this->input->post('sms_notifications_enabled')?1:0,
                                'email_notifications_enabled'=>$this->input->post('email_notifications_enabled')?1:0,
                                'sms_template'=>$this->sms_template_default,
                                'enable_fines'=>$this->input->post('enable_fines'),
                                'enable_contribution_member_list'=>$this->input->post('enable_contribution_member_list')?1:0,
                                'active'=>1,
                                'invoices_queued'=>0,
                                'modified_by'=>$this->user->id,
                                'modified_on'=>time(),
                            );
                            if($one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($id)){
                                if($result = $this->contributions_m->update_one_time_contribution_setting($one_time_contribution_setting->id,$one_time_contribution_settings_input)){
                                    //do nothing for now
                                }else{
                                    $this->session->set_flashdata('error','Could not save changes to one time contribution setting');
                                }
                            }else{
                                if($one_time_contribution_setting_id = $this->contributions_m->insert_one_time_contribution_setting($one_time_contribution_settings_input)){
                                    //do nothing for now
                                }else{
                                    $this->session->set_flashdata('error','Could create one time contribution setting');
                                }
                            }
                        }


                        if($this->input->post('type')==1||$this->input->post('type')==2){
                            if($this->input->post('enable_contribution_member_list')){
                                $this->contributions_m->delete_contribution_member_pairings($id);
                                $group_member_ids = $this->input->post('contribution_member_list');
                                foreach($group_member_ids as $member_id){
                                    $input = array(
                                        'member_id'=>$member_id,
                                        'group_id'=>$this->group->id,
                                        'contribution_id'=>$id,
                                        'created_on'=>time(),
                                        'created_by'=>$this->user->id,
                                    );
                                    if($contribution_member_pairing_id = $this->contributions_m->insert_contribution_member_pairing($input)){

                                    }else{
                                        $this->session->set_flashdata('error','Could not insert contribution member pairing');
                                    }
                                }
                            }

                            if($this->input->post('enable_fines')){
                                $this->contributions_m->delete_contribution_fine_settings($id);
                                if(isset($posts['fine_type'])){ 
                                    $count = 0; foreach($posts['fine_type'] as $fine_type):
                                        if($fine_type){
                                            $fine_date = $this->_fine_date($contribution_date,$fine_type,$posts['fixed_fine_chargeable_on'][$count],$posts['percentage_fine_chargeable_on'][$count]);
                                            $input = array(
                                                'contribution_id'=>$id,
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
                            }
                        }

                         
                        //$this->session->set_flashdata('success','Contribution changes saved successfully.');
                    }else{
                        echo 'Changes could not be saved.';
                    }
                    if($post = $this->contributions_m->get_group_contribution($id)){
                        echo json_encode($post);
                    }else{
                        echo "Could not find contribution";
                    }
                }else{
                    if($fine_entries_are_valid==FALSE){
                        $messsage = '<p>Please enter all the fields in the fine settings category, there are some entries missing.</p>';
                    }else{
                        $messsage = '';
                    }
                    echo validation_errors().$messsage;
                }
            }else{
                echo "Could not find contribution";
            }
        }else{
            echo "Contribution ID not supplied";
        }
    }

    function hide($id=0,$redirect=TRUE){
        $id OR redirect('group/contributions/listing');
        $post = $this->contributions_m->get_group_contribution($id);    
        $post OR redirect('group/contributions/listing');
        if($post->is_hidden){
            $this->session->set_flashdata('error','Sorry, the Contribution is already hidden');
            redirect('group/contributions/listing');
        }
        $input = array(
            'is_hidden'=>1,
            'modified_by'=>$this->user->id,
            'modified_on'=>time(),
        );
        if($result = $this->contributions_m->update($post->id,$input)){
            $this->session->set_flashdata('success',$post->name.' was successfully hidden');
        }else{
            $this->session->set_flashdata('error','Unable to hide '.$post->name);
        }
        if($redirect){
            redirect('group/contributions/listing');
        }
        return TRUE;
    }

    function unhide($id=0,$redirect=TRUE){
        $id OR redirect('group/contributions/listing');
        $post = $this->contributions_m->get_group_contribution($id);    
        $post OR redirect('group/contributions/listing');
        if($post->is_hidden==0){
            $this->session->set_flashdata('error','Sorry, the Contribution is already displayed');
            redirect('group/contributions/listing');
        }
        $input = array(
            'is_hidden'=>0,
            'modified_by'=>$this->user->id,
            'modified_on'=>time(),
        );
        if($result = $this->contributions_m->update($post->id,$input)){
            $this->session->set_flashdata('success',$post->name.' was successfully displayed');
        }else{
            $this->session->set_flashdata('error','Unable to display '.$post->name);
        }
        if($redirect){
            redirect('group/contributions/listing');
        }
        return TRUE;
    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_hide'){
            for($i=0;$i<count($action_to);$i++){
                $this->hide($action_to[$i],FALSE);
            }
        }else if($action == 'bulk_display'){
            for($i=0;$i<count($action_to);$i++){
                $this->unhide($action_to[$i],FALSE);
            }
        }
        redirect('group/contributions/listing');
    }

    function delete($id = 0){
        $id OR redirect('group/contributions/listing');
        $post = new stdClass();
        $post = $this->contributions_m->get_group_contribution($id);
        $post OR redirect('group/contributions/listing');
        if($this->user->id==$this->group->owner||$this->ion_auth->is_admin()){
            $password = $this->input->get('confirmation_string');
            $identity = valid_phone($this->user->phone)?:$this->user->email;
            if($this->ion_auth->login($identity,$password)){
                if($this->transaction_statements_m->check_if_contribution_has_transactions(
                    $post->id,
                    $post->group_id)||$this->statements_m->check_if_contribution_has_transactions(
                        $post->id,
                        $post->group_id)){
                    $this->session->set_flashdata('warning','The contribution has transactions associated to it, void all transactions associated to this account before deleting it');
                }else{
                    if($this->contributions_m->safe_delete($post->id,$post->group_id)){
                        $this->session->set_flashdata('success','Contribution deleted successfully');
                    }else{
                        $this->session->set_flashdata('error','Contribution could not be deleted');
                    }
                }
            }else{
                $this->session->set_flashdata('warning','You entered the wrong password.');
            }
        }else{
            $this->session->set_flashdata('warning','You do not have sufficient permissions to delete a contribution.');
        }
        redirect('group/contributions/listing');
    }

    

    function check_contribution_member_list(){
        $contribution_list_members = $this->input->post('contribution_member_list');
        $count = count($contribution_list_members);
        if($count>0){
            return TRUE;
        }else{
            $this->form_validation->set_message('check_contribution_member_list', 'At least one member should be selected under limit invoicing for this contribution to specific members');
            return FALSE;
        }
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
            $this->input->post('start_month_multiple'),
            $this->input->post('after_first_contribution_day_option'),
            $this->input->post('after_first_day_week_multiple'),
            $this->input->post('after_first_starting_day'),
            $this->input->post('after_second_contribution_day_option'),
            $this->input->post('after_second_day_week_multiple'),
            $this->input->post('after_second_starting_day')
        );         

         //print_r(date("F j, Y, g:i a",$contribution_date)); die();
    }

    function _second_contribution_date(){
        return $second_contribution_date = $this->contribution_invoices->get_second_regular_contribution_contribution_date(
            $this->input->post('contribution_frequency'),
            $this->input->post('month_day_monthly'),
            $this->input->post('week_day_monthly'),
            $this->input->post('week_day_weekly'),
            $this->input->post('week_day_fortnight'),
            $this->input->post('week_number_fortnight'),
            $this->input->post('month_day_multiple'),
            $this->input->post('week_day_multiple'),
            $this->input->post('start_month_multiple'),
            $this->input->post('after_first_contribution_day_option'),
            $this->input->post('after_first_day_week_multiple'),
            $this->input->post('after_first_starting_day'),
            $this->input->post('after_second_contribution_day_option'),
            $this->input->post('after_second_day_week_multiple'),
            $this->input->post('after_second_starting_day')    
           
        );
    }
 

    function _fine_date($contribution_date = 0,$fine_type = 0,$fixed_fine_chargeable_on = 0,$percentage_fine_chargeable_on = 0
    ){
        return $this->contribution_invoices->get_contribution_fine_date($contribution_date,$fine_type,$fixed_fine_chargeable_on,$percentage_fine_chargeable_on,0,0,0,$contribution_date);
    }

    function check_if_invoice_date_is_less_than_contribution_date(){
        $invoice_date = strtotime($this->input->post('invoice_date'));
        $contribution_date = strtotime($this->input->post('contribution_date'));
        if($invoice_date<=$contribution_date){
            return TRUE;
        }else{
            $this->form_validation->set_message('check_if_invoice_date_is_less_than_contribution_date', 'The invoice date has to be earlier than the contribution date');
            return FALSE;
        }
    }
    
   /* function check_past_dates(){
        $today = time();
        $get_contribution_dates = $this->contributions_m->get_regular_contributions();
          foreach ($get_contribution_dates as $key => $contribution_dates)
         {
            $cont_date = $contribution_dates->contribution_date;
            if(date('Y-m-d',$cont_date) > date('Y-m-d',$today)){
                echo 'day in future';
            }else{
                echo 'day in past';
                $change_date = $this->invoices->queue_contribution_invoices($today);
            }
            //print_r($cont_date);
          }

         

    }*/

}