<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends Ajax_Controller{

	protected $validation_rules = array(
        array(
            'field' => 'name',
            'label' => 'Contribution Name',
            'rules' => 'xss_clean|trim|required',
        ),
        array(
            'field' => 'category',
            'label' => 'Contribution category',
            'rules' => 'xss_clean|trim|numeric|required',
        ),array(
            'field' => 'type',
            'label' => 'Contribution Type',
            'rules' => 'xss_clean|trim|numeric|required',
        ),array(
            'field' => 'regular_invoicing_active',
            'label' => 'Activate Invoicing',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'one_time_invoicing_active',
            'label' => 'Activate Invoicing',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'invoice_date',
            'label' => 'Invoice Date',
            'rules' => 'xss_clean|trim',
        ),array(
            'field' => 'contribution_date',
            'label' => 'Contribution Date/Due Date',
            'rules' => 'xss_clean|trim',
        ),array(
            'field' => 'contribution_frequency',
            'label' => 'How often do members contribute',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'month_day_monthly',
            'label' => 'Day of the Month',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'week_day_monthly',
            'label' => 'Day of the Week',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'week_day_weekly',
            'label' => 'Day of the Week',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'week_day_fortnight',
            'label' => 'Day of the Week',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'week_day_multiple',
            'label' => 'Day of the Week',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'week_number_fortnight',
            'label' => 'Day of the Week',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'month_day_multiple',
            'label' => 'Day of the Month',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'start_month_multiple',
            'label' => 'Staring Month',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'invoice_days',
            'label' => 'Invoice days',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'sms_notifications_enabled',
            'label' => 'Enable SMS Notifications',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'email_notifications_enabled',
            'label' => 'Enable Email Notifications',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'sms_template',
            'label' => 'SMS template',
            'rules' => 'xss_clean|trim',
        ),array(
            'field' => 'enable_fines',
            'label' => 'Enable Fines',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'enable_contribution_member_list',
            'label' => 'Enable Contribution Member List',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'disable_overpayments',
            'label' => 'Disable Overpayments',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'enable_contribution_summary_display_configuration',
            'label' => 'Enable contribution summary display configuration',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'display_contribution_arrears_cumulatively',
            'label' => 'Enable display of contribution arrears as a cumulative',
            'rules' => 'xss_clean|trim|numeric',
        ),
        array(
            'field' => 'is_non_refundable',
            'label' => 'Contribution is non refundable',
            'rules' => 'xss_clean|trim|numeric',
        ),
        array(
            'field' => 'enable_checkoff',
            'label' => 'Enable Checkoff',
            'rules' => 'xss_clean|trim|numeric',
        ),
        
        array(
            'field' => 'is_equity',
            'label' => 'Contribution considered as Equity',
            'rules' => 'xss_clean|trim|numeric',
        )
    );
	function __construct(){
        parent::__construct();
        $this->load->model('contributions_m');
        $this->load->model('members/members_m');
        $this->load->library('contribution_invoices');
        $this->load->library('setup_tasks_tracker');
        $this->load->library('bank');
        $this->sms_template_default = $this->contribution_invoices->sms_template_default;
        $this->group_member_options = $this->members_m->get_group_member_options();
    }

    function listing(){
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
        $disabled = $this->input->get_post('disabled');
        $html = '';
        if(empty($posts)){
            $html.='<div class="alert alert-info">
                <h4 class="block">Information! No records to display</h4>
                <p>
                    No contributions to display.
                </p>
            </div>';
        }else{
            $i = 1;
            $html.='
            <table class="table m-table m-table--head-separator-primary">
                <thead>
                    <tr>
                        <th width="2%">
                            #
                        </th>
                        <th width="15%">
                            '.translate('Name').'
                        </th>
                        <th>
                            '.translate('Contribution Particulars').'
                        </th>
                        <th width="20%">
                            '.translate('Contribution Fines').'
                        </th>
                        <th width="15%" class="text-right">
                            '.translate('Amount').' ('.$this->group_currency.')
                        </th>
                        <th width="15%" '.($disabled?('class="hidden"'):'').'>
                            '.translate('Actions').'
                        </th>
                    </tr>
                </thead>
                <tbody>';
                    foreach($posts as $post):
                        if($post->type==1){
                            $html.= '
                            <tr data-id="'.$post->id.'">
                                <td>'.$i++.'</td>
                                <td class="name" data-name="'.$post->name.'">'.$post->name.'</td>
                                <td class="type" data-type="'.translate($post->type).'" data-regular-invoicing-active="'.$post->regular_invoicing_active
                                .'"  >';
                                $html.= '<strong>Contribution Type: </strong>'.translate($contribution_type_options[$post->type]).'<br/>';
                                        if($post->regular_invoicing_active){
                                            $regular_contribution_setting = isset($regular_contribution_settings_array[$post->id])?$regular_contribution_settings_array[$post->id]:'';
                                            if($regular_contribution_setting){
                                                $html.= ' '.$contribution_type_options[$post->type];
                                                $week_day_monthly = $regular_contribution_setting->week_day_monthly?$regular_contribution_setting->week_day_monthly:0;
                                                $week_day_multiple = $regular_contribution_setting->week_day_multiple?$regular_contribution_setting->week_day_multiple:0;
                                                if($regular_contribution_setting->contribution_frequency==1){
                                                    //Once a month
                                                    $html.= ' '.$contribution_frequency_options[$regular_contribution_setting->contribution_frequency].',
																										'.$days_of_the_month[$regular_contribution_setting->month_day_monthly].'
																										'.$month_days[$regular_contribution_setting->week_day_monthly?$regular_contribution_setting->week_day_monthly:0];
                                                }else if($regular_contribution_setting->contribution_frequency==6){
                                                    //Weekly
                                                    $html.= $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$week_days[$regular_contribution_setting->week_day_weekly];
                                                }else if($regular_contribution_setting->contribution_frequency==7){
                                                    //Fortnight or every two weeks
                                                    $html.= $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$every_two_week_days[$regular_contribution_setting->week_day_fortnight].' '.$week_numbers[$regular_contribution_setting->week_number_fortnight];
                                                }else if($regular_contribution_setting->contribution_frequency==2||$regular_contribution_setting->contribution_frequency==3||$regular_contribution_setting->contribution_frequency==4||$regular_contribution_setting->contribution_frequency==5){
                                                    //Multiple months
                                                    $html.= $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].',
                                                    '.$days_of_the_month[$regular_contribution_setting->month_day_multiple].'
                                                    '.$month_days[$regular_contribution_setting->week_day_multiple?$regular_contribution_setting->week_day_multiple:0].',
                                                    '.$starting_months[$regular_contribution_setting->start_month_multiple];
                                                }else if($regular_contribution_setting->contribution_frequency==8){
                                                   $html.=$contribution_frequency_options[$regular_contribution_setting->contribution_frequency];
                                                }
                                                $html.= '<br/><strong>Invoice Date: </strong>'.timestamp_to_date($regular_contribution_setting->invoice_date);
                                                $html.= '<br/><strong>Contribution Date: </strong>'.timestamp_to_date($regular_contribution_setting->contribution_date);
                                                $html.= '<br/><strong>SMS Notifications: </strong>'; $html.= $regular_contribution_setting->sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                                $html.= '<br/><strong>Email Notifications: </strong>';$html.= $regular_contribution_setting->email_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                                $html.= '<br/>';
                                                if($regular_contribution_setting->enable_contribution_member_list){
                                                    $html.= '<hr/>';
                                                    if(isset($selected_group_members_array[$post->id])){
                                                        $group_members = $selected_group_members_array[$post->id];
                                                        $count = 1;
                                                        $html.= '<strong>Members to be invoiced: </strong><br/>';
                                                        foreach($group_members as $member_id){
                                                            if(isset($group_member_options[$member_id])){
                                                                if($count==1){
                                                                    $html.= $group_member_options[$member_id];
                                                                }else{
                                                                    $html.= ','.$group_member_options[$member_id];
                                                                }
                                                                $count++;
                                                            }
                                                        }
                                                    }
                                                }else{
                                                    $html.= '<strong>All members to be invoiced </strong><br/>';
                                                }
                                            }else{
                                                $html.= "<span class='label label-default'>Regular Contribution Setting not Available</span>";
                                            }

                                        }else{
                                            $html.= "<span class='label label-default'>Invoicing Disabled</span>";
                                        }
                                $html.= '
                                <td>';
                                if($post->regular_invoicing_active){
                                    $html.= '<br/><strong>Fines: </strong>';$html.= $regular_contribution_setting->enable_fines?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                    if($regular_contribution_setting->enable_fines){
                                        if(isset($contribution_fine_settings_array[$post->id])){
                                            $html.= '<hr/>';
                                            $html.= '<strong>Contribution fine settings: </strong><br/>';
                                            $contribution_fine_settings = $contribution_fine_settings_array[$post->id];
                                            $count = 1;
                                            foreach ($contribution_fine_settings as $contribution_fine_setting) {
                                                if($count>1){
                                                    $html.= '<br/>';
                                                }
                                                $html.= '<strong>Fine setting #'.$count.' - '.$contribution_fine_setting->id.'<br/></strong>';
                                                $html.= '<strong>Fine Date</strong> '.timestamp_to_date($contribution_fine_setting->fine_date).'<br/>';
                                                if($contribution_fine_setting->fine_type==1){
                                                    $html.= $fine_types[$contribution_fine_setting->fine_type];
                                                    $html.= ' '.$this->group_currency.' '.number_to_currency($contribution_fine_setting->fixed_amount);
                                                    $html.= ' '.$fine_mode_options[$contribution_fine_setting->fixed_fine_mode];
                                                    $html.= ' '.$fine_chargeable_on_options[$contribution_fine_setting->fixed_fine_chargeable_on];
                                                    $html.= ' '.$fine_frequency_options[$contribution_fine_setting->fixed_fine_frequency];
                                                    $html.= ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
                                                }else if($contribution_fine_setting->fine_type==2){
                                                    $html.= $fine_types[$contribution_fine_setting->fine_type];
                                                    $html.= ' '.$contribution_fine_setting->percentage_rate.' % ';
                                                    $html.= ' '.$percentage_fine_on_options[$contribution_fine_setting->percentage_fine_on];
                                                    $html.= ' '.$fine_chargeable_on_options[$contribution_fine_setting->percentage_fine_chargeable_on];
                                                    $html.= ' '.$fine_mode_options[$contribution_fine_setting->percentage_fine_mode];
                                                    $html.= ' '.$fine_frequency_options[$contribution_fine_setting->percentage_fine_frequency];
                                                    $html.= ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
                                                }
                                                $html.= '<br/><strong>SMS Notifications: </strong>'; $html.= $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                                $html.= '<br/><strong>Email Notifications: </strong>';$html.= $contribution_fine_setting->fine_email_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span><br/>";

                                                $count++;
                                            }
                                        }
                                    }
                                }else{

                                }
                                $html.= '
                                </td>
                                </td>';
                        }else if($post->type==2){
                            $html.= '
                            <tr data-id="'.$post->id.'">
                                <td>'.$i++.'</td>
                                <td class="name" data-name="'.$post->name.'">
                                    '.$post->name.'
                                </td>
                                <td class="type" data-type="'.$post->type.'">';
                                    $html.= '<strong>Contribution Type: </strong>'.translate($contribution_type_options[$post->type]).'<br/>';
                                    if($post->one_time_invoicing_active){
                                        $one_time_contribution_setting = isset($one_time_contribution_settings_array[$post->id])?$one_time_contribution_settings_array[$post->id]:'';
                                        if($one_time_contribution_setting){
                                            $html.= '<br/><strong>Invoice Date: </strong>'.timestamp_to_date($one_time_contribution_setting->invoice_date);
                                            $html.= '<br/><strong>Contribution Date: </strong>'.timestamp_to_date($one_time_contribution_setting->contribution_date);
                                            $html.= '<br/><strong>SMS Notifications: </strong>'; $html.= $one_time_contribution_setting->sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                            $html.= '<br/><strong>Email Notifications: </strong>';$html.= $one_time_contribution_setting->email_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                            $html.= '<br/><strong>Fines: </strong>';$html.= $one_time_contribution_setting->enable_fines?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                            $html.= '<br/>';
                                            if($one_time_contribution_setting->enable_contribution_member_list){
                                                if(isset($selected_group_members_array[$post->id])){
                                                    $html.= '<hr/>';
                                                    $group_members = $selected_group_members_array[$post->id];
                                                    $count = 1;
                                                    $html.= '<strong>Members to be invoiced: </strong><br/>';
                                                    foreach($group_members as $member_id){
                                                        if($count==1){
                                                            $html.= $group_member_options[$member_id];
                                                        }else{
                                                            $html.= ','.$group_member_options[$member_id];
                                                        }
                                                        $count++;
                                                    }
                                                }
                                            }else{
                                                $html.= '<strong>All members to be invoiced </strong><br/>';
                                            }
                                        }else{
                                            $html.= "<span class='label label-default'>One Time Contribution Setting not Available</span>";
                                        }

                                    }else{
                                        $html.= "<span class='label label-default'>Invoicing Disabled</span>";
                                    }

                                    if($post->enable_contribution_summary_display_configuration){
                                        $html.= '<br/><strong> Display contribution arrears cumulatively:</strong>';
                                        $html.= $post->display_contribution_arrears_cumulatively?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                    }
                                $html.='
                                <td>';
                                    if($post->one_time_invoicing_active){
                                        $html.= '<br/><strong>Fines: </strong>';$html.= $one_time_contribution_setting->enable_fines?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                        if($one_time_contribution_setting->enable_fines){
                                            if(isset($contribution_fine_settings_array[$post->id])){
                                                $html.= '<hr/>';
                                                $html.= '<strong>Contribution fine settings: </strong><br/>';
                                                $contribution_fine_settings = $contribution_fine_settings_array[$post->id];
                                                $count = 1;
                                                foreach ($contribution_fine_settings as $contribution_fine_setting) {
                                                    if($count>1){
                                                        $html.= '<br/>';
                                                    }
                                                    $html.= '<strong>Fine setting #'.$count.' - '.$contribution_fine_setting->id.'<br/></strong>';

                                                    if($contribution_fine_setting->fine_type==1){
                                                        $html.= $fine_types[$contribution_fine_setting->fine_type];
                                                        $html.= ' '.$this->group_currency.' '.number_to_currency($contribution_fine_setting->fixed_amount);
                                                        $html.= ' '.$fine_mode_options[$contribution_fine_setting->fixed_fine_mode];
                                                        $html.= ' '.$fine_chargeable_on_options[$contribution_fine_setting->fixed_fine_chargeable_on];
                                                        $html.= ' '.$fine_frequency_options[$contribution_fine_setting->fixed_fine_frequency];
                                                        $html.= ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
                                                    }else if($contribution_fine_setting->fine_type==2){
                                                        $html.= $fine_types[$contribution_fine_setting->fine_type];
                                                        $html.= ' '.$contribution_fine_setting->percentage_rate.' % ';
                                                        $html.= ' '.$fine_chargeable_on_options[$contribution_fine_setting->percentage_fine_on];
                                                        $html.= ' '.$fine_chargeable_on_options[$contribution_fine_setting->percentage_fine_chargeable_on];
                                                        $html.= ' '.$fine_mode_options[$contribution_fine_setting->percentage_fine_mode];
                                                        $html.= ' '.$fine_frequency_options[$contribution_fine_setting->percentage_fine_frequency];
                                                        $html.= ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
                                                    }
                                                    $html.= '<br/><strong>SMS Notifications: </strong>'; $html.= $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                                    $html.= '<br/><strong>Email Notifications: </strong>';$html.= $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span><br/>";

                                                    $count++;
                                                }
                                            }
                                        }
                                    }else{
                                        $html.= "<span class='label label-default'>Fines: Disabled</span>";
                                    }
                                $html.= 
                                '';
                        }else if($post->type==3){
                            $html.= '
                            <tr data-id="'.$post->id.'">
                                <td>'.$i++.'</td>
                                <td class="name" data-name="'.$post->name.'">
                                    '.$post->name.'
                                </td>
                                <td class="type" data-type="'.$post->type.'">';
                                        $html.= '<strong>Contribution Type: </strong>'.translate($contribution_type_options[$post->type]);

                                        if($post->enable_contribution_summary_display_configuration){
                                            $html.= '<br/><strong> Display contribution arrears cumulatively:</strong>';
                                            $html.= $post->display_contribution_arrears_cumulatively?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                        }
                                $html.= '
                                </td>
                                <td><span class="label label-default">Disabled</span></td>
                                ';
                        }
                        $html.= '
                                <td class="amount" data-amount="'.$post->amount.'" class=\'text-right\'> <strong>'.number_to_currency($post->amount).'</strong></td>
                                <td '.($disabled?('class="hidden"'):'').'>
                                    <a href="#" class="btn btn-xs default edit_contribution full_width_inline" data-content="#contribution_form_holder" data-title="Edit Group Contribution" data-id="edit_contribution" id="">
                                        <i class="icon-pencil"></i> '.translate('Edit').' &nbsp;&nbsp;
                                    </a>
                                    <a data-title="Enter your password to delete the contribution"  data-content="This will delete the contribution permanently. Are you sure you want to proceed?" href='.site_url('ajax/contributions/delete/'.$post->id).' class="tooltips btn btn-xs red prompt_confirmation_message_link">
                                        <i class="fa fa-trash"></i> '.translate('Delete').' &nbsp;&nbsp; 
                                    </a>
                                </td>
                            </tr>';
                        endforeach;
                        $html.= '
                </tbody>
            </table>';
        }
        if($posts){
            $status = 1;
        }else{
            $status = 2;
        }
        echo json_encode(array(
            "status" => $status,
            "html" => $html,
        ));
    }

    function setup_listing(){
        $this->data = array();
        $per_page = ($this->input->post('length'))>1?$this->input->post('length'):0;
        $start_number = $this->input->post('start');
        $order = $this->input->post('order');
        $order = $this->input->post('order');
        if($order){
            $dir = strtoupper($order[0]['dir']);
        }else{
            $dir = 'ASC';
        }
        $search = $this->input->post('search');
        $name ='';
        if($search){
            $name = $search['value'];
        }
        $filter_parameters = array(
            'name' => $name,
        );
        $total_rows = $this->contributions_m->count_group_contributions('',$filter_parameters,$dir);
        $pagination = create_custom_pagination('group/contributions/listing/pages', $total_rows,$per_page,$start_number,TRUE);
        $posts = $this->contributions_m->limit($pagination['limit'])->get_group_contributions('',$filter_parameters,$dir);
        $contribution_type_options = $this->contribution_invoices->contribution_type_options;
        $regular_contribution_settings_array = $this->contributions_m->get_group_regular_contribution_settings_array();
        $contribution_frequency_options = $this->contribution_invoices->contribution_frequency_options;
        $month_days = $this->contribution_invoices->month_days;
        $week_days = $this->contribution_invoices->week_days;
        $days_of_the_month = $this->contribution_invoices->days_of_the_month;
        $every_two_week_days = $this->contribution_invoices->every_two_week_days;
        $months = $this->contribution_invoices->months;
        $starting_months = $this->contribution_invoices->starting_months;
        $week_numbers = $this->contribution_invoices->week_numbers;
        $contribution_fine_settings_array = $this->contributions_m->get_all_contribution_fine_settings_array();
        $one_time_contribution_settings_array = $this->contributions_m->get_group_one_time_contribution_settings_array();
        $fine_types = $this->contribution_invoices->fine_types;
        $fine_chargeable_on_options = $this->contribution_invoices->fine_chargeable_on_options['Frequently used options']+$this->contribution_invoices->fine_chargeable_on_options['Other options'];
        $fine_frequency_options = $this->contribution_invoices->fine_frequency_options;
        $fine_mode_options = $this->contribution_invoices->fine_mode_options;
        $fine_limit_options = $this->contribution_invoices->fine_limit_options;
        $percentage_fine_on_options = $this->contribution_invoices->percentage_fine_on_options;
        foreach ($posts as $key => $post) {
            $contribution_details ='<strong>'.translate('Name').': </strong>'.translate($contribution_type_options[$post->type]).'<br/>';
            $frequency = '';
            $fine_datails = '';
            $invoice_date = '';
            $contribution_date = '';
            $notifications = '';
            $member_notification = '';
            if($post->type==1){
                if($post->regular_invoicing_active){
                    $regular_contribution_setting = isset($regular_contribution_settings_array[$post->id])?$regular_contribution_settings_array[$post->id]:'';
                    if($regular_contribution_setting){
                        $week_day_monthly = $regular_contribution_setting->week_day_monthly?$regular_contribution_setting->week_day_monthly:0;
                        $week_day_multiple = $regular_contribution_setting->week_day_multiple?$regular_contribution_setting->week_day_multiple:0;
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
                        $invoice_date = '<br/><strong>'.translate('Invoice Date').': </strong>'.timestamp_to_date($regular_contribution_setting->invoice_date);
                        $contribution_date = '<br/><strong>'.translate('Contribution Date').': </strong>'.timestamp_to_date($regular_contribution_setting->contribution_date);
                        $fine_datails='<strong>'.translate('SMS Notifications').': </strong>'; 
                        $fine_datails.=$regular_contribution_setting->sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                        $fine_datails.='<br/><strong>'.translate('Email Notifications').': </strong>';
                        $fine_datails.=$regular_contribution_setting->email_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                        
                        $member_notification ="<br/><br/>";
                        if($regular_contribution_setting->enable_contribution_member_list){
                            $member_notification.='<strong>'.translate('Invoicing').': </strong> For specific members';
                        }else{
                            $member_notification.= '<strong>'.translate('Invoicing').': </strong> All members';
                        }
                    }else{
                        $frequency = "Regular Contribution Setting not Available";
                    }

                    $fine_datails.= '<br/><strong>Fines: </strong>';
                    $fine_datails.= $regular_contribution_setting->enable_fines?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                }
            }elseif($post->type == 2){
                if($post->one_time_invoicing_active){
                    $one_time_contribution_setting = isset($one_time_contribution_settings_array[$post->id])?$one_time_contribution_settings_array[$post->id]:'';
                    if($one_time_contribution_setting){
                        $invoice_date= '<strong>'.translate('Invoice Date').': </strong>'.timestamp_to_date($one_time_contribution_setting->invoice_date);
                        $contribution_date= '<br/><strong>'.translate('Contribution Date').': </strong>'.timestamp_to_date($one_time_contribution_setting->contribution_date);
                        $fine_datails= '<br/><strong>'.translate('SMS Notifications').': </strong>'; 
                        $fine_datails.= $one_time_contribution_setting->sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";

                        $fine_datails.= '<br/><strong>'.translate('Email Notifications').': </strong>';
                        $fine_datails.= $one_time_contribution_setting->email_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                        $fine_datails.= '<br/><strong>'.translate('Fines').': </strong>';
                        $fine_datails.= $one_time_contribution_setting->enable_fines?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                    }else{
                        $frequency= "<span class='label label-default'>One Time Contribution Setting not Available</span>";
                    }

                }else{
                    $frequency= "<span class='label label-default'>Invoicing Disabled</span>";
                }
            }
            $contribution_details.=$frequency.'<br/>';
            $contribution_details.=$invoice_date;
            $contribution_details.=$contribution_date;
            $contribution_details.=$notifications;
            $contribution_details.=$member_notification;
            $category = $post->category?$this->contribution_invoices->contribution_category_options[$post->category]:'';
            $this->data[] = array(
                ($key+1+$start_number).'.',
                $post->name.' - '.$category,
                $contribution_details,
                $fine_datails,
                number_to_currency($post->amount),
                $post->id,
            );
        } 
        echo json_encode(array(
            "data" => $this->data,
            "iTotalDisplayRecords" => $total_rows,
            "iTotalRecords" => $this->contributions_m->count_group_contributions(),
        ));
    }

    function listing_complete(){
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
                    No contributions to display.
                </p>
            </div>';
        }else{
            $i = 1;
            echo'
            <table class="table table-condensed table-striped margin-top-10 table-bordered">
                <thead>
                    <tr>
                        <th width="2%">
                            #
                        </th>
                        <th width="25%">
                            Name
                        </th>
                        <th>
                            Contribution Particulars
                        </th>
                        <th width="20%">
                            Contribution Fines
                        </th>
                        <th width="15%" class="text-right">
                            Amount ('.$this->group_currency.')
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
                                                echo ' '.$contribution_type_options[$post->type];
                                                $week_day_monthly = $regular_contribution_setting->week_day_monthly?$regular_contribution_setting->week_day_monthly:0;
                                                $week_day_multiple = $regular_contribution_setting->week_day_multiple?$regular_contribution_setting->week_day_multiple:0;
                                                if($regular_contribution_setting->contribution_frequency==1){
                                                    //Once a month
                                                    echo ' '.$contribution_frequency_options[$regular_contribution_setting->contribution_frequency].',
                                                                                                        '.$days_of_the_month[$regular_contribution_setting->month_day_monthly].'
                                                                                                        '.$month_days[$regular_contribution_setting->week_day_monthly?$regular_contribution_setting->week_day_monthly:0];
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
                                            }else{
                                                echo "<span class='label label-default'>Regular Contribution Setting not Available</span>";
                                            }

                                        }else{
                                            echo "<span class='label label-default'>Invoicing Disabled</span>";
                                        }
                                echo '
                                <td>';
                                    echo '<br/><strong>Fines: </strong>';echo $regular_contribution_setting->enable_fines?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
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
                                echo '
                                </td>
                                </td>
                                <td class="amount" data-amount="'.$post->amount.'" class=\'text-right\'> <strong>'.number_to_currency($post->amount).'</strong></td>
                                <td>
                                    <a href="#" class="btn btn-xs default edit_contribution full_width_inline" data-content="#contribution_form_holder" data-title="Edit Group Contribution" data-id="edit_contribution" id="">
                                        <i class="icon-pencil"></i> Edit &nbsp;&nbsp;
                                    </a>Edit &nbsp;&nbsp;
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
                                <td>';
                                    if($post->one_time_invoicing_active){
                                        echo '<br/><strong>Fines: </strong>';echo $one_time_contribution_setting->enable_fines?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
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
                                        echo "<span class='label label-default'>Fines: Disabled/span>";
                                    }
                                echo 
                                '
                                </td>
                                <td class="amount" data-amount="'.$post->amount.'" class=\'text-right\'>'.number_to_currency($post->amount).'</td>
                                
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
                                    <a href="#" class="btn btn-xs default edit_contribution full_width_inline" data-content="#contribution_form_holder" data-title="Edit Group Contribution" data-id="edit_contribution" id="">
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

    function get(){
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

    function create(){
        $data = array();
        $response = array();
        $post = new stdClass();
        $posts = $_POST;
        $this->_conditional_validation_rules();
        $this->form_validation->set_rules($this->validation_rules);
        $fine_entries_are_valid = TRUE;
        $fine_errors = array();
        if($this->input->post('enable_fines')){
            if(isset($posts['fine_type'])){
                $count = 0; 
                foreach($posts['fine_type'] as $fine_type):
                    if($fine_type){
                        if($fine_type==1){
                            if($posts['fixed_amount'][$count]&&is_numeric(currency($posts['fixed_amount'][$count]))){
                                if($posts['fixed_fine_mode'][$count] && is_numeric($posts['fixed_fine_mode'][$count])){
                                    if($posts['fixed_fine_chargeable_on'][$count]){
                                        if(is_numeric($posts['fixed_fine_frequency'][$count])){
                                            if(is_numeric($posts['fine_limit'][$count])){

                                            }else{
                                                $fine_entries_are_valid = FALSE;
                                                $fine_errors[$count]['fine_limit'] = 'This field must be numeric';
                                            }
                                        }else{
                                            $fine_entries_are_valid = FALSE;
                                            $fine_errors[$count]['fixed_fine_frequency'] = 'This field must be numeric';
                                        }
                                    }else{
                                        $fine_entries_are_valid = FALSE;
                                        $fine_errors[$count]['fixed_fine_chargeable_on'] = 'This field is required'; 
                                    }
                                }else{
                                    $fine_entries_are_valid = FALSE;
                                    $fine_errors[$count]['fixed_fine_mode'] = 'Entry is invalid';
                                }
                            }else{
                                $fine_entries_are_valid = FALSE;
                                $fine_errors[$count]['fixed_amount'] = 'Entry is invalid';
                            }
                        }else if($fine_type==2){
                            if($posts['percentage_rate'][$count]&&is_numeric($posts['percentage_rate'][$count])){
                                if($posts['percentage_fine_on'][$count] && is_numeric($posts['percentage_fine_on'][$count])){
                                    if($posts['percentage_fine_chargeable_on'][$count]){
                                        if($posts['percentage_fine_mode'][$count] && is_numeric($posts['percentage_fine_mode'][$count])){
                                            if(is_numeric($posts['fine_limit'][$count])){
                                                if(is_numeric($posts['percentage_fine_frequency'][$count])){

                                                }else{
                                                    $fine_entries_are_valid = FALSE;
                                                    $fine_errors[$count]['percentage_fine_frequency'] = 'Field must be numeric';
                                                }
                                            }else{
                                                $fine_entries_are_valid = FALSE;
                                                $fine_errors[$count]['fine_limit'] = 'Field must be numeric';
                                            }
                                        }else{
                                            $fine_entries_are_valid = FALSE;
                                            $fine_errors[$count]['percentage_fine_mode'] = 'Entry is not valid';
                                        }
                                    }else{
                                        $fine_entries_are_valid = FALSE;
                                        $fine_errors[$count]['percentage_fine_chargeable_on'] = 'This field is required';
                                    }
                                }else{
                                    $fine_entries_are_valid = FALSE;
                                    $fine_errors[$count]['percentage_fine_on'] = 'Entry is invalid';
                                }
                            }else{
                                $fine_entries_are_valid = FALSE;
                                $fine_errors[$count]['percentage_rate'] = 'Entry is invalid';
                            }
                        }else{
                            $fine_entries_are_valid = FALSE;
                            $fine_errors[$count]['fine_type'] = 'Invalid Fine Type';
                        }
                    }else{
                        $fine_entries_are_valid = FALSE;
                        $fine_errors[$count]['fine_type'] = 'Fine type is required';
                    }
                    $count++;
                endforeach;
            }
        }
        if($this->form_validation->run()&&$fine_entries_are_valid){
            $invoice_days = $this->input->post('invoice_days')?:3;
            $sms_template = $this->contribution_invoices->sms_template_default;
            $name = $this->input->post('name');
            $input = array(
                'name' => $name,
                'amount' => $this->input->post('amount'),
                'category' => $this->input->post('category'),
                'type' => $this->input->post('type'),
                'regular_invoicing_active' => $this->input->post('regular_invoicing_active')?1:0,
                'one_time_invoicing_active' => $this->input->post('one_time_invoicing_active')?1:0,
                'enable_contribution_summary_display_configuration' => $this->input->post('enable_contribution_summary_display_configuration')?1:0,
                'display_contribution_arrears_cumulatively' => $this->input->post('display_contribution_arrears_cumulatively')?1:0,
                'active' => 1,
                'group_id' => $this->group->id,
                'is_hidden' => 0,
                'created_by' => $this->user->id,
                'is_non_refundable' => $this->input->post('is_non_refundable'),
                'enable_checkoff' => $this->input->post('enable_checkoff'),
                'enable_deposit_statement_display' => $this->input->post('enable_deposit_statement_display'),
                'is_equity' => $this->input->post('is_equity'),
                'created_on' => time(),
            );
            if($contribution_id = $this->contributions_m->insert($input)){
                if($this->input->post('type')==1){
                    $contribution_date = $this->_contribution_date();
                    $invoice_date = $contribution_date - (24*60*60*$invoice_days);
                    $regular_contribution_settings_input = array(
                        'contribution_id'=>$contribution_id,
                        'group_id'=>$this->group->id,
                        'invoice_date'=>$invoice_date,
                        'contribution_date'=>$contribution_date,
                        'contribution_frequency'=>$this->input->post('contribution_frequency'),
                        'invoice_days'=>$invoice_days,
                        'sms_notifications_enabled'=>$this->input->post('sms_notifications_enabled')?1:0,
                        'email_notifications_enabled'=>$this->input->post('email_notifications_enabled')?1:0,
                        'sms_template'=>$sms_template,
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
                        'sms_template'=>$sms_template,
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
                                        'fine_sms_notifications_enabled'=>$this->input->post('sms_notifications_enabled')?1:0,
                                        'fine_email_notifications_enabled'=>$this->input->post('email_notifications_enabled')?1:0,                                        
                                        'created_on'=>time(),
                                        'created_by'=>$this->user->id
                                    );
                                    if($contribution_fine_setting_id = $this->contributions_m->insert_contribution_fine_setting($input)){
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
                    $response = array(
                        'status' => 1,
                        'id' => $contribution_id,
                        'name' => $name,
                        'message' => 'Contribution successfully created',
                        'refer'=>site_url('group/contributions/listing')
                    );
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
            $post = array();
            $form_errors = $this->form_validation->error_array();
            foreach ($form_errors as $key => $value) {
                $post[$key] = $value;
            }
            if($error_message){
                $post['fine_errors'] = $error_message;
            }

            $response = array(
                'status' => 0,
                'message' => 'There are some errors on the form. Please review and try again.',
                'validation_errors' => $post,
                'fine_validation_errors' => $fine_errors,
            );
        }

        echo json_encode($response);
    }

    function edit($id = 0){
        $response = array();
        $fine_errors = array();
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
                    $invoice_days = $this->input->post('invoice_days')?:3;
                    $input = array(
                        'name' => $this->input->post('name'),
                        'amount' => $this->input->post('amount'),
                        'category' => $this->input->post('category'),
                        'type' => $this->input->post('type'),
                        'regular_invoicing_active' => $this->input->post('regular_invoicing_active')?1:0,
                        'one_time_invoicing_active' => $this->input->post('one_time_invoicing_active')?1:0,
                        'enable_contribution_summary_display_configuration' => $this->input->post('enable_contribution_summary_display_configuration')?1:0,
                        'display_contribution_arrears_cumulatively' => $this->input->post('display_contribution_arrears_cumulatively')?1:0,
                        'active' => 1,
                        'group_id' => $this->group->id,
                        'is_non_refundable' => $this->input->post('is_non_refundable'),
                        'enable_checkoff' => $this->input->post('enable_checkoff'),
                        'enable_deposit_statement_display' => $this->input->post('enable_deposit_statement_display'),
                        'is_equity' => $this->input->post('is_equity'),
                        'is_hidden' => 0,
                        'modified_by' => $this->user->id,
                        'modified_on' => time(),
                    );

                    if($result = $this->contributions_m->update($id,$input)){
                        if($this->input->post('type')==1){
                            $contribution_date = $this->_contribution_date();
                            $invoice_date = $contribution_date - (24*60*60*$invoice_days);
                            $regular_contribution_settings_input = array(
                                'contribution_id'=>$id,
                                'group_id'=>$this->group->id,
                                'invoice_date'=>$invoice_date,
                                'contribution_date'=>$contribution_date,
                                'contribution_frequency'=>$this->input->post('contribution_frequency'),
                                'invoice_days'=>$invoice_days,
                                'sms_notifications_enabled'=>$this->input->post('sms_notifications_enabled')?1:0,
                                'email_notifications_enabled'=>$this->input->post('email_notifications_enabled')?1:0,
                                'sms_template'=>$this->input->post('sms_template'),
                                'month_day_monthly'=>$this->input->post('month_day_monthly'),
                                'week_day_monthly'=>$this->input->post('week_day_monthly')?$this->input->post('week_day_monthly'):0,
                                'week_day_weekly'=>$this->input->post('week_day_weekly'),
                                'week_day_fortnight'=>$this->input->post('week_day_fortnight'),
                                'week_number_fortnight'=>$this->input->post('week_number_fortnight'),
                                'month_day_multiple'=>$this->input->post('month_day_multiple'),
                                'week_day_multiple'=>$this->input->post('week_day_multiple'),
                                'start_month_multiple'=>$this->input->post('start_month_multiple'),
                                'disable_overpayments'=>$this->input->post('disable_overpayments'),
                                'enable_contribution_member_list'=>$this->input->post('enable_contribution_member_list')?1:0,
                                'enable_fines'=>$this->input->post('enable_fines'),
                                'active'=>1,
                                'modified_by'=>$this->user->id,
                                'modified_on'=>time(),
                            );
                            if($regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($id)){
                                if($result = $this->contributions_m->update_regular_contribution_setting($regular_contribution_setting->id,$regular_contribution_settings_input)){
                                    //do nothing for now
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Could not save changes to regular contribution setting.',
                                        'validation_errors' => '',
                                        'fine_validation_errors' => '',
                                    );
                                }
                            }else{
                                if($regular_contribution_setting_id = $this->contributions_m->insert_regular_contribution_setting($regular_contribution_settings_input)){
                                    //do nothing for now
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Could create regular contribution setting.',
                                        'validation_errors' => '',
                                        'fine_validation_errors' => '',
                                    );
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
                                'sms_template'=>$this->input->post('sms_template'),
                                'enable_contribution_member_list'=>$this->input->post('enable_contribution_member_list')?1:0,
                                'active'=>1,
                                'enable_fines'=>$this->input->post('enable_fines'),
                                'invoices_queued'=>0,
                                'modified_by'=>$this->user->id,
                                'modified_on'=>time(),
                            );
                            if($one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($id)){
                                if($result = $this->contributions_m->update_one_time_contribution_setting($one_time_contribution_setting->id,$one_time_contribution_settings_input)){
                                    //do nothing for now
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Could not save changes to one time contribution setting.',
                                        'validation_errors' => '',
                                        'fine_validation_errors' => '',
                                    );
                                }
                            }else{
                                if($one_time_contribution_setting_id = $this->contributions_m->insert_one_time_contribution_setting($one_time_contribution_settings_input)){
                                    //do nothing for now
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Could create one time contribution setting.',
                                        'validation_errors' => '',
                                        'fine_validation_errors' => '',
                                    );
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
                                        $response = array(
                                            'status' => 0,
                                            'message' => 'Could not insert contribution member pairing.',
                                            'validation_errors' => '',
                                            'fine_validation_errors' => '',
                                        );
                                       // $this->session->set_flashdata('error','Could not insert contribution member pairing');
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
                                                $response = array(
                                                    'status' => 0,
                                                    'message' => 'Could not insert contribution fine setting.',
                                                    'validation_errors' => '',
                                                    'fine_validation_errors' => '',
                                                );
                                            }
                                        }
                                        $count++;
                                    endforeach;
                                }
                            }
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Changes could not be saved.',
                            'validation_errors' => '',
                            'fine_validation_errors' => '',
                        );
                    }
                    if($post = $this->contributions_m->get_group_contribution($id)){
                        $response = array(
                            'status' => 1,
                            'data'=>$post,
                            'message' => 'Contribution successfully updated',
                            'refer' => site_url('group/contributions/listing'),
                            'fine_validation_errors' => '',
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not find contribution.',
                            'validation_errors' => '',
                            'fine_validation_errors' => '',
                        );
                    }
                }else{

                    if($fine_entries_are_valid==FALSE){
                        $error_message = '<p> Please enter all the fields in the fine settings category, there are some entries missing.</p>';
                    }else{
                        $error_message = "";
                    }
                    $post = array();
                    $form_errors = $this->form_validation->error_array();
                    foreach ($form_errors as $key => $value) {
                        $post[$key] = $value;
                    }
                    if($error_message){
                        $post['fine_errors'] = $error_message;
                    }
                    $response = array(
                        'status' => 0,
                        'message' => 'There are some errors on the form. Please review and try again.',
                        'validation_errors' => $post,
                        'fine_validation_errors' => $fine_errors,
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find contribution.',
                    'validation_errors' => '',
                    'fine_validation_errors' => '',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find contribution id.',
                'validation_errors' => '',
                'fine_validation_errors' => '',
            );            
        }
        echo json_encode($response);
    }

    function create_contribution_fines($id = 0){
        $id = $this->input->post('id');
        if($id){
            $post = new stdClass();
            $post = $this->contributions_m->get_group_contribution($id);
            if($post){
                $posts = $_POST;
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
                    if($fine_entries_are_valid){
                        $contribution_date = 0;
                        if($post->type == 1){
                            $regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($post->id);
                            $contribution_date = $regular_contribution_setting->contribution_date;
                        }elseif ($post->type==2) {
                            $one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($post->id);
                            $contribution_date = $one_time_contribution_setting->contribution_date;
                        }
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
                                        'fine_sms_notifications_enabled'=>1,
                                        'fine_email_notifications_enabled'=>1,
                                        'created_on'=>time(),
                                        'created_by'=>$this->user->id
                                    );
                                    if($contrbution_fine_setting_id = $this->contributions_m->insert_contribution_fine_setting($input)){
                                        $update = array(
                                            'enable_fines'=>$this->input->post('enable_fines'),
                                            'modified_on' => time(),
                                            'modified_by' => $this->user->id,
                                        );
                                        if($post->type == 1){
                                            if($regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($post->id)){
                                                if($result = $this->contributions_m->update_regular_contribution_setting($regular_contribution_setting->id,$update)){
                                                        //do nothing for now
                                                }else{
                                                    $this->session->set_flashdata('error','Could not save changes to regular contribution setting');
                                                }
                                            }else{
                                                echo "Could not update group fines";
                                            }
                                        }elseif ($post->type==2) {
                                            if($one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($post->id)){
                                                if($result = $this->contributions_m->update_one_time_contribution_setting($one_time_contribution_setting->id,$update)){
                                                    
                                                }else{
                                                    $this->session->set_flashdata('error','Could not save changes to one time contribution setting');
                                                }
                                            }else{
                                                echo "Could not update group fines";
                                            }
                                        }else{
                                            echo "Could not update group fines";
                                        }
                                    }else{
                                        $this->session->set_flashdata('error','Could not insert contribution fine setting');
                                    }
                                }
                                $count++;
                            endforeach;

                            if($post = $this->contributions_m->get_group_contribution($id)){
                                echo json_encode($post);
                            }else{
                                echo "Could not find contribution";
                            }
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
                    $update = array(
                        'enable_fines'=>$this->input->post('enable_fines'),
                        'modified_on' => time(),
                        'modified_by' => $this->user->id,
                    );
                    if($post->type == 1){
                        if($regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($post->id)){
                            if($result = $this->contributions_m->update_regular_contribution_setting($regular_contribution_setting->id,$update)){
                                    //do nothing for now
                                if($post = $this->contributions_m->get_group_contribution($id)){
                                    echo json_encode($post);
                                }else{
                                    echo "Could not find contribution";
                                }
                            }else{
                                $this->session->set_flashdata('error','Could not save changes to regular contribution setting');
                            }
                        }else{
                            echo "Could not update group fines";
                        }
                    }elseif ($post->type==2) {
                        if($one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($post->id)){
                            if($result = $this->contributions_m->update_one_time_contribution_setting($one_time_contribution_setting->id,$update)){
                                if($post = $this->contributions_m->get_group_contribution($id)){
                                    echo json_encode($post);
                                }else{
                                    echo "Could not find contribution";
                                }
                            }else{
                                $this->session->set_flashdata('error','Could not save changes to one time contribution setting');
                            }
                        }else{
                            echo "Could not update group fines";
                        }
                    }else{
                        echo "Could not update group fines";
                    }
                }
            }else{
                echo "Could not find contribution";
            }
        }else{
            echo "Contribution ID not supplied";
        }
    }

    function _conditional_validation_rules(){
        if($this->input->post('type') == 1 || $this->input->post('type') == 2){
            $this->validation_rules[] = array(
                'field' => 'amount',
                'label' => 'Contribution Amount',
                'rules' => 'trim|required|currency',
            );
        }
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

    function check_contribution_member_list(){
        $contribution_list_members = $this->input->post('contribution_member_list');
        $contribution_list_members = (array)$contribution_list_members;
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

    function _fine_date($contribution_date = 0,$fine_type = 0,$fixed_fine_chargeable_on = 0,$percentage_fine_chargeable_on = 0){
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

    function delete(){
        $response = array();
        $id = $this->input->post('id');
        if($id){
            $post = $this->contributions_m->get_group_contribution($id);
            if($this->user->id==$this->group->owner||$this->ion_auth->is_admin()){
                $password = $this->input->post('password');
                $identity = valid_phone($this->user->phone)?:$this->user->email;
                if($this->ion_auth->login($identity,$password)){
                    if($this->transaction_statements_m->check_if_contribution_has_transactions($post->id,$post->group_id)||$this->statements_m->check_if_contribution_has_transactions($post->id,$post->group_id)){
                        $response = array(
                            'status'=>0,
                            'message'=>'The contribution has transactions associated to it, void all transactions associated to this account before deleting it'
                        );
                    }else{
                        if($this->contributions_m->safe_delete($post->id,$post->group_id)){
                            $response = array(
                                'status'=>1,
                                'message'=>'Contribution deleted successfully'
                            );
                        }else{
                            $response = array(
                                'status'=>0,
                                'message'=>'Contribution could not be deleted'
                            );
                        }
                    }
                }else{
                    $response = array(
                        'status'=>0,
                        'message'=>'You entered the wrong password'
                    );
                }
            }else{
                $response = array(
                    'status'=>0,
                    'message'=>'You do not have sufficient permissions to delete a contribution'
                );
            }
        }else{
            $response = array(
                'status'=>0,
                'message'=>'Contribution id is required'
            );

        }
        echo json_encode($response);
    }

    function get_contribution_view($id=0){
        $response = array();
        if($id){
            $post = $this->contributions_m->get_group_contribution($id);
            if($post){
                $contribution_type_options = $this->contribution_invoices->contribution_type_options;
                $contribution_frequency_options = $this->contribution_invoices->contribution_frequency_options;
                $month_days = $this->contribution_invoices->month_days;
                $week_days = $this->contribution_invoices->week_days;
                $days_of_the_month = $this->contribution_invoices->days_of_the_month;
                $every_two_week_days = $this->contribution_invoices->every_two_week_days;
                $months = $this->contribution_invoices->months;
                $starting_months = $this->contribution_invoices->starting_months;
                $week_numbers = $this->contribution_invoices->week_numbers;
                $fine_types = $this->contribution_invoices->fine_types;
                $fine_chargeable_on_options = $this->contribution_invoices->fine_chargeable_on_options['Frequently used options']+$this->contribution_invoices->fine_chargeable_on_options['Other options'];
                $fine_frequency_options = $this->contribution_invoices->fine_frequency_options;
                $fine_mode_options = $this->contribution_invoices->fine_mode_options;
                $fine_limit_options = $this->contribution_invoices->fine_limit_options;
                $percentage_fine_on_options = $this->contribution_invoices->percentage_fine_on_options;
                $html='
                <div class="row invoice-body">
                    <div class="col-xs-12 table-responsive ">
                        <table class="table table-sm m-table m-table--head-separator-primary table table--hover table-borderless table-condensed contributions-listing-table">
                            <thead>
                                <tr>
                                    <th width="30%">'.translate('Contribution details').'</th>
                                    <th class="m--align-right">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="'.$post->id.'._active_row">
                                    <td class="m--align-right" nowrap>
                                        <strong>'.translate('Contribution Type').'</strong>
                                    </td>
                                    <td>: '.($contribution_type_options[$post->type]).'</td>
                                </tr>
                                <tr class="'.$post->id.'_active_row">
                                    <td class="m--align-right" nowrap>
                                        <strong>'.translate('Contribution Amount').'</strong>
                                    </td>
                                    <td>: '.$this->group_currency.' '.number_to_currency($post->amount).'</td>
                                </tr>';
                                if($post->type==1){
                                    if($post->regular_invoicing_active){
                                        $regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($post->id);
                                        if($regular_contribution_setting){
                                            $html.='
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong>'.
                                                            translate('Contribution Frequency').
                                                        '</strong>
                                                    </td>
                                                    <td>: ';
                                                       if($regular_contribution_setting->contribution_frequency==1){
                                                            $html.= $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$days_of_the_month[$regular_contribution_setting->month_day_monthly].' '.$month_days[$regular_contribution_setting->week_day_monthly?$regular_contribution_setting->week_day_monthly:0];
                                                        }else if($regular_contribution_setting->contribution_frequency==6){
                                                            $html.= $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$week_days[$regular_contribution_setting->week_day_weekly];
                                                        }else if($regular_contribution_setting->contribution_frequency==7){
                                                            $html.= $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$every_two_week_days[$regular_contribution_setting->week_day_fortnight].' '.$week_numbers[$regular_contribution_setting->week_number_fortnight];
                                                        }else if($regular_contribution_setting->contribution_frequency==2||$regular_contribution_setting->contribution_frequency==3||$regular_contribution_setting->contribution_frequency==4||$regular_contribution_setting->contribution_frequency==5){
                                                            $html.=$contribution_frequency_options[$regular_contribution_setting->contribution_frequency].',
                                                            '.$days_of_the_month[$regular_contribution_setting->month_day_multiple].'
                                                            '.$month_days[$regular_contribution_setting->week_day_multiple?$regular_contribution_setting->week_day_multiple:0].', 
                                                            '.$starting_months[$regular_contribution_setting->start_month_multiple];
                                                        }else if($regular_contribution_setting->contribution_frequency==8){
                                                            $html.= $contribution_frequency_options[$regular_contribution_setting->contribution_frequency];
                                                        }else if($regular_contribution_setting->contribution_frequency == 9){
                                                            $html.= $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].''. $contribution_days_option[$regular_contribution_setting->after_first_contribution_day_option].'&nbsp;'. $month_days[$regular_contribution_setting->after_first_day_week_multiple] .'&nbsp;'.$starting_days[$regular_contribution_setting->after_first_starting_day] .'&nbsp; and &nbsp;'. $contribution_days_option[$regular_contribution_setting->after_second_contribution_day_option].'&nbsp;'. $month_days[$regular_contribution_setting->after_second_day_week_multiple] .'&nbsp;'.$starting_days[$regular_contribution_setting->after_second_starting_day]; 
                                                        }
                                                        $html.= '
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong>'.translate('Invoice Date').'</strong>
                                                    </td>
                                                    <td>: '.timestamp_to_date($regular_contribution_setting->invoice_date).'</td>
                                                </tr>
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong>'.translate('Contribution Date').'</strong>
                                                    </td>
                                                    <td>: '.timestamp_to_date($regular_contribution_setting->contribution_date).'</td>
                                                </tr>
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong>'.translate('SMS Notifications').'</strong>
                                                    </td>
                                                    <td>: ';
                                                        if($regular_contribution_setting->sms_notifications_enabled){
                                                            $html.='<span class="m-badge m-badge--success m-badge--wide">'.translate('Enabled').'</span>';
                                                        }else{
                                                            $html.='<span class="m-badge m-badge--danger m-badge--wide">'.translate('Disabled').'</span>';
                                                        }
                                                $html.= '
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong>'.translate('Email Notifications').'</strong>
                                                    </td>
                                                    <td>: ';
                                                        if($regular_contribution_setting->email_notifications_enabled){
                                                            $html.= '<span class="m-badge m-badge--success m-badge--wide">'.translate('Enabled').'</span>';
                                                        }else{
                                                            $html.= '<span class="m-badge m-badge--danger m-badge--wide">'.translate('Disabled').'</span>';
                                                        }
                                                    $html.= '</td>
                                                </tr>
                                            ';
                                        }else{
                                            $html.= '
                                                <tr>
                                                    <td class="text-right">
                                                        <i class="la la-exclamation-triangle m--font-danger"></i>
                                                    </td>
                                                    <td colspan>
                                                        : <span class="m--font-danger">
                                                           Regular Contribution Settings not Available.
                                                        </span>
                                                    </td>
                                                </tr>
                                            ';
                                        }
                                    }else{
                                        $html.= '
                                            <tr>
                                                <td class="text-right">
                                                    <strong>'.
                                                        translate('Invoicing').
                                                    '</strong>
                                                </td>
                                                <td colspan>
                                                    : <span class="m-badge m-badge--danger m-badge--wide">Disabled</span>
                                                </td>
                                            </tr>
                                        ';
                                    }
                                }elseif($post->type==2){
                                    if($post->one_time_invoicing_active){
                                        $one_time_contribution_setting = isset($one_time_contribution_settings_array[$post->id])?$one_time_contribution_settings_array[$post->id]:'';
                                        if($one_time_contribution_setting){
                                            $html.= '
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong>'.
                                                            translate('Contribution Frequency').
                                                        '</strong>
                                                    </td>
                                                    <td>: ';
                                                       if($regular_contribution_setting->contribution_frequency==1){
                                                            //Once a month
                                                            $html.= $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$days_of_the_month[$regular_contribution_setting->month_day_monthly].' '.$month_days[$regular_contribution_setting->week_day_monthly?$regular_contribution_setting->week_day_monthly:0];
                                                        }else if($regular_contribution_setting->contribution_frequency==6){
                                                            //Weekly
                                                            $html.= $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$week_days[$regular_contribution_setting->week_day_weekly];
                                                        }else if($regular_contribution_setting->contribution_frequency==7){
                                                            //Fortnight or every two weeks
                                                            $html.= $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$every_two_week_days[$regular_contribution_setting->week_day_fortnight].' '.$week_numbers[$regular_contribution_setting->week_number_fortnight];
                                                        }else if($regular_contribution_setting->contribution_frequency==2||$regular_contribution_setting->contribution_frequency==3||$regular_contribution_setting->contribution_frequency==4||$regular_contribution_setting->contribution_frequency==5){
                                                            //Multiple months
                                                            $html.= $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].',
                                                            '.$days_of_the_month[$regular_contribution_setting->month_day_multiple].'
                                                            '.$month_days[$regular_contribution_setting->week_day_multiple?$regular_contribution_setting->week_day_multiple:0].', 
                                                            '.$starting_months[$regular_contribution_setting->start_month_multiple];
                                                        }else if($regular_contribution_setting->contribution_frequency==8){
                                                            $html.= $contribution_frequency_options[$regular_contribution_setting->contribution_frequency];
                                                        }else if($regular_contribution_setting->contribution_frequency == 9){
                                                            $html.= $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].''. $contribution_days_option[$regular_contribution_setting->after_first_contribution_day_option].'&nbsp;'. $month_days[$regular_contribution_setting->after_first_day_week_multiple] .'&nbsp;'.$starting_days[$regular_contribution_setting->after_first_starting_day] .'&nbsp; and &nbsp;'. $contribution_days_option[$regular_contribution_setting->after_second_contribution_day_option].'&nbsp;'. $month_days[$regular_contribution_setting->after_second_day_week_multiple] .'&nbsp;'.$starting_days[$regular_contribution_setting->after_second_starting_day]; 
                                                        }
                                                        $html.= '
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong>'.
                                                            translate('Invoice Date').'
                                                        </strong>
                                                    </td>
                                                    <td>: '.timestamp_to_date($one_time_contribution_setting->invoice_date).
                                                    '</td>
                                                </tr>
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong>'.
                                                            translate('Contribution Date').'
                                                        </strong>
                                                    </td>
                                                    <td>: '.timestamp_to_date($one_time_contribution_setting->contribution_date).
                                                    '</td>
                                                </tr>
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong>'.
                                                            translate('SMS Notifications').'
                                                        </strong>
                                                    </td>
                                                    <td>: ';
                                                        if($one_time_contribution_setting->sms_notifications_enabled){
                                                            $html.= '<span class="m-badge m-badge--success m-badge--wide">'.translate('Enabled').'</span>';
                                                        }else{
                                                            $html.= '<span class="m-badge m-badge--danger m-badge--wide">'.translate('Disabled').'</span>';
                                                        }
                                                    $html.= '</td>
                                                </tr>
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong>'.
                                                            translate('Email Notifications').'
                                                        </strong>
                                                    </td>
                                                    <td>: ';
                                                        if($one_time_contribution_setting->email_notifications_enabled){
                                                            $html.= '<span class="m-badge m-badge--success m-badge--wide">'.translate('Enabled').'</span>';
                                                        }else{
                                                            $html.= '<span class="m-badge m-badge--danger m-badge--wide">'.translate('Disabled').'</span>';
                                                        }
                                                    $html.= '</td>
                                                </tr>
                                            ';
                                        }else{
                                            $html.= '
                                                <tr>
                                                    <td class="text-right">
                                                        <i class="la la-exclamation-triangle m--font-danger"></i>
                                                    </td>
                                                    <td colspan>
                                                        : <span class="m--font-danger">
                                                           One Time Contribution Setting not Available.
                                                        </span>
                                                    </td>
                                                </tr>
                                            ';
                                        }
                                    }else{
                                        $html.= '
                                            <tr>
                                                <td class="text-right">
                                                    <strong>'.
                                                        translate('Invoicing').
                                                    '</strong>
                                                </td>
                                                <td colspan>
                                                    : <span class="m-badge m-badge--danger m-badge--wide">Disabled</span>
                                                </td>
                                            </tr>
                                        ';
                                    }

                                }elseif($post->type==3){
                                }
                            $html.='
                            </tbody>
                        </table>';
                        if($post->type==1 || $post->type==2){
                            if($post->type==1){
                                if($post->regular_invoicing_active){
                                    if($regular_contribution_setting){
                                        $html.='
                                            <table class="table m-table table-sm m-table--head-separator-primary contributions-listing-table">
                                                <thead>
                                                    <tr>
                                                        <th colspan="2">'.translate('Fine Settings').'</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
                                                    if($regular_contribution_setting->enable_fines){
                                                        $contribution_fine_settings = $this->contributions_m->get_contribution_fine_settings($post->id);
                                                        $count = 1;
                                                        foreach ($contribution_fine_settings as $contribution_fine_setting) {
                                                            $html.= '
                                                                <tr>
                                                                    <th scope="row" nowrap>'.
                                                                        $count.'.
                                                                    </th>
                                                                    <td>';
                                                                    if($contribution_fine_setting->fine_type==1){
                                                                        $html.= $fine_types[$contribution_fine_setting->fine_type];
                                                                        $html.= ' '.$this->group_currency.' '.number_to_currency($contribution_fine_setting->fixed_amount);
                                                                        $html.= ' '.$fine_mode_options[$contribution_fine_setting->fixed_fine_mode];
                                                                        $html.= ' '.$fine_chargeable_on_options[$contribution_fine_setting->fixed_fine_chargeable_on];
                                                                        $html.= ' '.$fine_frequency_options[$contribution_fine_setting->fixed_fine_frequency];
                                                                        $html.= ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
                                                                    }else if($contribution_fine_setting->fine_type==2){
                                                                        $html.= $fine_types[$contribution_fine_setting->fine_type];
                                                                        $html.= ' '.$contribution_fine_setting->percentage_rate.' % ';
                                                                        $html.= ' '.$percentage_fine_on_options[$contribution_fine_setting->percentage_fine_on];
                                                                        $html.= ' '.$fine_chargeable_on_options[$contribution_fine_setting->percentage_fine_chargeable_on];
                                                                        $html.= ' '.$fine_mode_options[$contribution_fine_setting->percentage_fine_mode];
                                                                        $html.= ' '.$fine_frequency_options[$contribution_fine_setting->percentage_fine_frequency];
                                                                        $html.= ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
                                                                    }
                                                                $html.='
                                                                    <br>'.
                                                                    translate('Fine date').' :'.timestamp_to_date($contribution_fine_setting->fine_date).'
                                                                    <br/>
                                                                    SMS Notifications: '; 

                                                                    $html.= $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='m-badge m-badge--success m-badge--wide'>Enabled</span>":"<span class='m-badge m-badge--info m-badge--wide'>Disabled</span>";
                                                                    $html.= '<br/>Email Notifications: ';

                                                                    $html.= $contribution_fine_setting->fine_email_notifications_enabled?"<span class='m-badge m-badge--success m-badge--wide'>Enabled</span>":"<span class='m-badge m-badge--info m-badge--wide'>Disabled</span>";
                                                                $html.='
                                                                    </td>
                                                                </tr>
                                                            ';
                                                            $count++;
                                                        }
                                                    }else{
                                                        $html.= '
                                                            <td colspan="2">
                                                                <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
                                                                    <strong>Heads up! </strong>No fine settings configured.
                                                                </div>
                                                            </td>
                                                        ';
                                                    }
                                                    $html.='
                                                </tbody>
                                            </table>
                                        ';
                                        $html.= '
                                            <table class="table m-table table-sm m-table--head-separator-primary contributions-listing-table">
                                                <thead>
                                                    <tr>
                                                        <th colspan="2">'.translate('Members to be invoiced').'</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
                                                    if($regular_contribution_setting->enable_contribution_member_list){
                                                        
                                                        if(isset($selected_group_members_array[$post->id])){
                                                            $group_members = $selected_group_members_array[$post->id];
                                                            $count = 1;
                                                            foreach($group_members as $member_id){
                                                        $html.= '<tr><th scope="row" width="1%">'.$count.'.</th><td>'.$group_member_options[$member_id].'</td></tr>';
                                                        $count++;
                                                    }
                                                    $html.='
                                                </tbody>
                                            </table>';
                                                            
                                                        }
                                                    }else{
                                                        $html.= '
                                                            <tr>
                                                                <td colspan="2">
                                                                    <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
                                                                        <strong>Heads up! </strong>All members to be invoiced.
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        ';
                                                    $html.='
                                                </tbody>
                                            </table>
                                        ';
                                                   } 
                                    }
                                }
                            }else{
                                if($post->one_time_invoicing_active){
                                    if($one_time_contribution_setting){
                                        echo'
                                            <table class="table m-table table-sm m-table--head-separator-primary contributions-listing-table">
                                                <thead>
                                                    <tr>
                                                        <th colspan="2">'.translate('Fine Settings').'</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
                                                    if($regular_contribution_setting->enable_fines){
                                                        $contribution_fine_settings = $this->contributions_m->get_contribution_fine_settings($post->id);
                                                        $count = 1;
                                                        foreach ($contribution_fine_settings as $contribution_fine_setting) {
                                                            echo '
                                                                <tr>
                                                                    <th scope="row" nowrap>'.
                                                                        $count.'.
                                                                    </th>
                                                                    <td>';
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
                                                                echo'
                                                                    <br>'.
                                                                    translate('Fine date').' :'.timestamp_to_date($contribution_fine_setting->fine_date).'
                                                                    <br/>
                                                                    SMS Notifications: '; 

                                                                    echo $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='m-badge m-badge--success m-badge--wide'>Enabled</span>":"<span class='m-badge m-badge--info m-badge--wide'>Disabled</span>";
                                                                    echo '<br/>Email Notifications: ';

                                                                    echo $contribution_fine_setting->fine_email_notifications_enabled?"<span class='m-badge m-badge--success m-badge--wide'>Enabled</span>":"<span class='m-badge m-badge--info m-badge--wide'>Disabled</span>";
                                                                echo'
                                                                    </td>
                                                                </tr>
                                                            ';
                                                            $count++;
                                                        }
                                                    }else{


                                                        echo '
                                                            <td colspan="2">
                                                                <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
                                                                    <strong>Heads up! </strong>No fine settings configured.
                                                                </div>
                                                            </td>
                                                        ';
                                                    }
                                                    echo'
                                                </tbody>
                                            </table>
                                        ';

                                        echo '
                                            <table class="table table-sm m-table m-table--head-separator-primary contributions-listing-table">
                                                <thead>
                                                    <tr>
                                                        <th colspan="2">'.translate('Members to be invoiced').'</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
                                                    if($one_time_contribution_setting->enable_contribution_member_list){
                                                        if(isset($selected_group_members_array[$post->id])){
                                                            $group_members = $selected_group_members_array[$post->id];
                                                            $count = 1;
                                                            foreach($group_members as $member_id){
                                                        echo '<tr><th scope="row" width="1%">'.$count.'.</th><td>'.$group_member_options[$member_id].'</td></tr>';
                                                        $count++;
                                                    }
                                                    echo'
                                                </tbody>
                                            </table>';
                                                            
                                                        }
                                                    }else{
                                                        echo '
                                                            <tr>
                                                                <td colspan="2">
                                                                    <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
                                                                        <strong>Heads up! </strong>All members to be invoiced.
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        ';
                                                    echo'
                                                </tbody>
                                            </table>
                                        ';
                                                    }
                                    }
                                }
                            }
                        }
                $html.='
                    </div>
                </div>';
                $response = array(
                    'status' => 1,
                    'message' => $html,
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find the requested contribution',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find the requested contribution',
            );
        }
        echo json_encode($response);
    }

}
