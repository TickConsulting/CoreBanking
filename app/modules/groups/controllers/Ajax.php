<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
    function __construct(){
        parent::__construct();
        $this->load->library('contribution_invoices');
        $this->load->model('countries/countries_m');
        $this->load->library('files_uploader');
        $this->load->helper('string');
    }

    function _check_if_valid_phone(){
        $phone = $this->input->post('phone');
        if($phone){
            if(valid_phone($phone)){
                return TRUE;
            }else{
                $this->form_validation->set_message('_check_if_valid_phone', 'The Group Phone number format you entered is invalid');
                return FALSE;
            }
        }else{
            return TRUE;
        }
    }

    function edit_settings(){
        if($_POST){
            $validation_rules = array();
            $validation_rules[] = array(
                'field' => 'id',
                'label' => 'ID',
                'rules' => 'trim|numeric',
            );
            if($this->input->post('enable_send_monthly_email_statements')){
                $validation_rules[] = array(
                    'field' => 'statement_send_date',
                    'label' => 'Statement sending date',
                    'rules' => 'trim|numeric',
                );
            }

            if($this->input->post('owner')){
                $validation_rules[] = array(
                    'field' => 'name',
                    'label' => 'Group Name',
                    'rules' => 'trim|required',
                );
                $validation_rules[] = array(
                    'field' => 'email',
                    'label' => 'Group Email Address',
                    'rules' => 'trim|valid_email',
                );
                $validation_rules[] = array(
                    'field' => 'phone',
                    'label' => 'Group Phone Number',
                    'rules' => 'trim|callback__check_if_valid_phone',
                );
                $validation_rules[] = array(
                    'field' => 'owner',
                    'label' => 'Group Owner',
                    'rules' => 'trim|required|numeric',
                );
                $validation_rules[] = array(
                    'field' => 'member_listing_order_by',
                    'label' => 'Group Members Listing Order',
                    'rules' => 'trim',
                );
                $validation_rules[] = array(
                    'field' => 'order_members_by',
                    'label' => 'Group Members Listing Order By',
                    'rules' => 'trim',
                );
                $validation_rules[] = array(
                    'field' => 'group_type',
                    'label' => 'Investment type',
                    'rules' => 'trim',
                );
                $validation_rules[] = array(
                    'field' => 'address',
                    'label' => 'Group Address',
                    'rules' => 'trim',
                );
            }
           
            $this->form_validation->set_rules($validation_rules);
            if($this->form_validation->run()){
                $settings_array = array(
                    'name',
                    'member_listing_order_by',
                    'order_members_by',
                    'enable_send_monthly_email_statements',
                    'enable_bulk_transaction_alerts_reconciliation',
                    'enable_member_information_privacy',
                    'disable_arrears',
                    'disable_ignore_contribution_transfers',
                    'disable_member_directory',
                    'enable_self_registration',
                    'disable_member_edit_profile',
                    'enable_absolute_loan_recalculation',
                    'enable_merge_transaction_alerts',
                    'group_offer_loans',
                    'allow_members_request_loan',
                    'owner',
                    'email',
                    'phone',
                    'address'
                );

                $input = array();
                foreach ($settings_array as $setting) {
                    if(isset($_POST[$setting])){
                        $input[$setting] = ($this->input->post($setting) == 'true'?1:($this->input->post($setting) == 'false'?0:$this->input->post($setting)));
                    }
                }
                if($this->input->post('enable_send_monthly_email_statements')){
                    $statement_send_date = $this->input->post('statement_send_date');
                    $next_monthly_contribution_statement_send_date  = $this->get_next_monthly_contribution_statement_send_date($statement_send_date);
                    $input['statement_send_date'] = $statement_send_date;
                    $input['next_monthly_contribution_statement_send_date'] = $next_monthly_contribution_statement_send_date;

                }
                $input['group_type'] = $this->input->post('group_type');
                if($this->groups_m->update($this->group->id,$input)){
                    $response = array(
                        'status' => 1,
                        'message' => 'Setting applied successfully',
                        'validation_errors' => '',
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not update group settings',
                        'validation_errors' => '',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'There are some errors on the phone, please review and try again',
                    'validation_errors' => $this->form_validation->error_array(),
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'No data submitted for processing',
                'validation_errors' => '',
            );
        }

        
        echo json_encode($response );
    }

    function get_next_monthly_contribution_statement_send_date($statement_send_date = 0){
        $today = time();
        if($this->group->next_monthly_contribution_statement_send_date){
            if($statement_send_date){
                if(date('m',$this->group->next_monthly_contribution_statement_send_date) <= date('m',$today)
                    && mktime(0, 0, 0, date('m',$today), $statement_send_date, date('Y',$today)) > $today
                ){
                    $next_monthly_contribution_statement_send_date = mktime(0, 0, 0, date('m',$today), $statement_send_date, date('Y',$today));
                }else{
                    $next_monthly_contribution_statement_send_date = mktime(0, 0, 0, date('m',$today)+1,$statement_send_date, date('Y',$today));
                }
            }else{
                if(date('m',$this->group->next_monthly_contribution_statement_send_date) <= date('m',$today)){
                    $next_monthly_contribution_statement_send_date = mktime(0, 0, 0, date('m',$today), 28, date('Y',$today));
                }else{
                    $next_monthly_contribution_statement_send_date = mktime(0, 0, 0, date('m',$today)+1, 28, date('Y',$today));
                }
            }
        }else{
            if($statement_send_date){
                $next_monthly_contribution_statement_send_date = mktime(0, 0, 0, date('m',$today), $statement_send_date, date('Y',$today));
            }else{
                $next_monthly_contribution_statement_send_date = mktime(0, 0, 0, date('m',$today), 28, date('Y',$today));
            }
        }
        return $next_monthly_contribution_statement_send_date;
    }

    function listing(){
    	$total_rows = $this->groups_m->count_all('','','','','','',($_POST['query']['search']??''));

    	echo json_encode(
    		array(
    			'data' => $this->groups_m->limit(
                    $_POST['pagination']['perpage'],
                    $_POST['pagination']['page']==1?0:(($_POST['pagination']['page']-1)*$_POST['pagination']['perpage'])
                )->get_all('','','','','','',($_POST['query']['search']??'')),
	    		'meta' => array(
			    	'field' => "id",
					'page' => $_POST['pagination']['page'],
					'pages' => ceil($total_rows/$_POST['pagination']['perpage']),
					'perpage' => $_POST['pagination']['perpage'],
					'sort' => "asc",
					'total' => $total_rows,
			    ),
    		)
    	);
    }

    function get_group_information($group_id=''){
        $bank_accounts_arr = '';
        $bank_key =0;
        $bank_accounts = $this->bank_accounts_m->get_group_bank_accounts($group_id);
        $group = $this->groups_m->get($group_id);
        $group_currency = $this->countries_m->get_currency_code($group->currency_id);
        $created_by = '';
        if($group->owner){
            $created_by = $this->ion_auth->get_user($group->owner);
        }
        $group_data = '
            <div>
                <a target="_blank" href="'.site_url('bank/groups/login_as_admin/'.$group->id).'" class="btn btn-xs btn-default">
                        <i class="fa fa-user-secret"></i> '.translate('Login as Admin').' 
                </a>&nbsp;&nbsp;
            </div>
            <table class="table m-table m-table--head-no-border">
                <thead>
                    <tr>
                        <th colspan="2" class="text-center">
                            '.$group->name.'
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th width="20%" nowrap>
                            '.translate('Group Account Number').'
                        </th>
                        <td align="left">
                            : '.$group->account_number.'
                        </td>
                    </tr>
                    ';
                    if($created_by){
                        $group_data.='
                        <tr>
                            <th width="20%" nowrap>
                                 '.translate('Registered By').'
                            </th>
                            <td align="left">
                                : '.$created_by->first_name.' '.$created_by->last_name.'
                            </td>
                        </tr>
                        <tr>
                            <th width="20%" nowrap>
                                 '.translate('Email').'
                            </th>
                            <td align="left">
                                : '.$created_by->email.'
                            </td>
                        </tr>
                        <tr>
                            <th width="20%" nowrap>
                                 '.translate('Phone').'
                            </th>
                            <td align="left">
                                : '.$created_by->phone.'
                            </td>
                        </tr>';
                    }
                    $group_data.='
                    <tr>
                        <th width="20%" nowrap>
                             '.translate('Group Phone Number').'
                        </th>
                        <td align="left">
                            : '.($group->phone?:'-').'
                        </td>
                    </tr>
                    <tr>
                        <th width="20%" nowrap>
                            '.translate('Group Email').'
                        </th>
                        <td align="left">
                            : '.($group->email?:'-').'
                        </td>
                    </tr>
                </tbody>
            </table>
        ';
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
                                <strong>Name :</strong>  &nbsp;'.$post->account_name.'<br/>
                                <strong>Number :</strong>  &nbsp;'.$post->account_number.'
                            </span>
                        </div>
                    </div>
                </div>';
            }
        }
        $sacco_accounts = $this->sacco_accounts_m->get_group_sacco_accounts($group_id);
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
                                <strong>Name :</strong>  &nbsp;'.$post->account_name.'<br/>
                                <strong>Number :</strong>  &nbsp;'.$post->account_number.'
                            </span>
                        </div>
                    </div>
                </div>';
            }
        }
        $mobile_money_accounts =$this->mobile_money_accounts_m->get_group_mobile_money_accounts($group_id);
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
                                <strong>Number :</strong>  &nbsp;'.$post->account_number.'
                            </span>
                        </div>
                    </div>
                </div>';
            }
        }
        $petty_cash_accounts = $this->petty_cash_accounts_m->get_group_petty_cash_accounts($group_id);
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
                        <label class="col-xl-4 col-lg-4 col-form-label"><strong>Name :</strong>  &nbsp;'.$post->account_name.'</label>
                        <div class="col-xl-8 col-lg-8">
                            <span class="m-form__control-static"> 
                            </span>
                        </div>
                    </div>
                </div>';
            }
        }
   
       	$contributions = $this->contributions_m->get_group_contributions($group_id);
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
                    $regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($post->id,$post->group_id);
                    if($regular_contribution_setting){
                        if($regular_contribution_setting->contribution_frequency==1){
                            //Once a month
                            $frequency = $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$days_of_the_month[$regular_contribution_setting->month_day_monthly].' '.$month_days[$regular_contribution_setting->week_day_monthly?$regular_contribution_setting->week_day_monthly:0];
                        }else if($regular_contribution_setting->contribution_frequency==6){
                            $frequency = $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$week_days[$regular_contribution_setting->week_day_weekly];
                        }else if($regular_contribution_setting->contribution_frequency==7){
                            $frequency = $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$every_two_week_days[$regular_contribution_setting->week_day_fortnight].' '.$week_numbers[$regular_contribution_setting->week_number_fortnight];
                        }else if($regular_contribution_setting->contribution_frequency==2||$regular_contribution_setting->contribution_frequency==3||$regular_contribution_setting->contribution_frequency==4||$regular_contribution_setting->contribution_frequency==5){
                            $frequency = $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$days_of_the_month[$regular_contribution_setting->month_day_multiple].' '.$month_days[$regular_contribution_setting->week_day_multiple?$regular_contribution_setting->week_day_multiple:0].', '.$starting_months[$regular_contribution_setting->start_month_multiple];
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
                $contributions_arr.= '
                    <div class="m-form__section m-form__section--first">
                        <div class="m-form__heading">
                            <h4 class="m-form__heading-title">'.$post->name.' contribution</h4>
                        </div>
                        <div class="form-group m-form__group m-form__group--sm row">
                            <div class="col-sm-12"><strong>Category: </strong>'.$category.'</div>
                        </div>
                        <div class="form-group m-form__group m-form__group--sm row">
                            <label class="col-xl-4 col-lg-4 col-form-label__">
                                <p>'.$contribution_type_options[$post->type].':</p>
                            </label>
                            <div class="col-xl-8 col-lg-8">
                                <span class="m-form__control-static">'.$group_currency.' '.number_to_currency($post->amount).' - '.$frequency.'<br/>'.$list_members.'</span>
                            </div>
                        </div>
                    </div>
                ';
            }
        }
   
        $group_members = $this->members_m->get_group_members($group_id);
        $disabled = $this->input->get('disabled')?:'';
        $members = '';
        if(empty($group_members)){
            $members.= '
            <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                <tbody>
                    <tr>
                        <td colspan="5">
                           <div class="alert alert-info" role="alert">
                                <strong>Sorry!</strong> There are no active member records to display.
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            ';
        }else{
            $group_role_options = $this->group_roles_m->get_group_role_options($group_id);
            $members.= '
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
                            '.translate('Group Role').'
                        </th>
                    </tr>
                </thead>
                <tbody>
            ';
            $i = 1;
            foreach($group_members as $member):
                $group_role = isset($group_role_options[$member->group_role_id])?$group_role_options[$member->group_role_id]:'Member';
                $members.= '
                <tr>
                    <td>'.($i++).'.</td>
                    <td>'.$member->first_name." ".$member->middle_name." ".$member->last_name.'</td>
                    <td>'.$member->phone.'</td>
                    <td>'.$member->email.'</td>
                    <td><a data-content="#assign_role_form_holder" data-title="'.translate('Assign Role to').''.$member->first_name." ".$member->middle_name." ".$member->last_name.'" class="member_group_role inline'.($disabled?('hidden'):'').'" data-toggle="modal" data-member-id="'.$member->id.'" data-user-id="'.$member->user_id.'" data-group-role-id="'.$member->group_role_id.'" href="#">'.translate($group_role).'</a></td>';
                    $members.= '
                </tr>
                ';
            endforeach;
            $members.= '
                </tbody>
            </table>
            ';
        }
        echo json_encode(array(
            'contributions' => $contributions_arr,
            'members' => $members,
            'bank_accounts' => $bank_accounts_arr,
            'group_data' => $group_data,
        ));
    }


    function upload_logo(){
        if(empty($_FILES)){
            $this->response = array(
                'result_code' => 400,
                'result_description' => 'File not sent',
            );
        }else{
            $groups_directory = './uploads/groups';
            if(!is_dir($groups_directory)){
                mkdir($groups_directory,0777,TRUE);
            }
            $file = $_FILES['file'];
            $name = $file['name'];
            $type = $file['type'];
            $size = $file['size'];
            $config['upload_path'] = $groups_directory;
            $config['allowed_types']        = 'gif|jpg|png|pdf|jpeg';
            $config['max_size'] = 10000000;
            $new_name = strtolower($name);
            $config['encrypt_name'] = FAlSE;
            $config['remove_spaces'] = TRUE;
            $config['detect_mime'] = TRUE;            
            $config['file_name'] = preg_replace("#[^A-Za-z0-9-./]#",'',$new_name);
            $config['file_name'] = preg_replace("/[\-_]/","",$config['file_name']);
            $config['file_name'] = preg_replace('/\.(?=.*\.)/','',$config['file_name']);            
            $this->load->library('upload', $config);
            if($this->upload->do_upload('file')){
                $files_ext =  explode('.', $config['file_name']);
                $upload_data = $this->upload->data();
                $input = array(
                    'avatar' => $upload_data['raw_name'].''.$upload_data['file_ext'],
                    'modified_by' => $this->user->id,
                    'modified_on' => time(),
                );
                $result = $this->groups_m->update($this->group->id,$input);
                if($result = $this->groups_m->update($this->group->id,$input)){
                    $this->response = array(
                        'result_code' => 200,
                        'result_description' => 'Success',
                        'data' => array(
                            'result' => $result
                        )
                    );
                }else{
                    $this->response = array(
                        'result_code' => 400,
                        'result_description' => 'Could not insert file details',
                    );
                }
            }else{
                $this->response = array(
                    'result_code' => 400,
                    'result_description' => 'Could not upload file',
                );
            }
        }
        echo json_encode($this->response);
    }

    public function get_group_avatar(){
        $groups_directory = '/uploads/groups/';
        $arr = array(
            'id'=>$this->group->id,
            'size'=>'',
            'name'=>$this->group->avatar,
            'path'=>site_url($groups_directory.''.$this->group->avatar)
        );
        $this->response = array(
            'result_code' => 1,
            'result_description' => 'Success',
            'data'=>$arr
        );
        echo json_encode($this->response);
    }

    function update_group_join_code(){        
        $join_code = strtolower(random_string('alnum',6)).'-'.$this->group->id;
        $input = array(
            'join_code' => $join_code,
            'modified_on' => time(),
            'modified_by' => $this->user->id,
        );
        if($result = $this->groups_m->update($this->group->id,$input)){
            $this->response = array(
                'result_code' => 1,
                'result_description' => 'Success',
                'data' => array(
                    'join_code' => $join_code
                )
            );
        }else{
            $this->response = array(
                'result_code' => 0,
                'result_description' => 'Could not update group join code',
            );
        }
        echo json_encode($this->response);
    }

}