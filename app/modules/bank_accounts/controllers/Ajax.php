<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
    
    protected $data = array();
    
    function __construct(){
        parent::__construct();
        $this->load->library('bank');
        $this->load->model('bank_accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('bank_branches/bank_branches_m');
        $this->load->model('transaction_statements/transaction_statements_m');
        $this->load->library('setup_tasks_tracker');
    }

    protected $validation_rules = array(
        array(
                'field' =>  'account_name',
                'label' =>  'Account Name',
                'rules' =>  'xss_clean|required|trim'
            ),
        array(
                'field' =>  'account_currency_id',
                'label' =>  'Account Currency',
                'rules' =>  'xss_clean|required|trim'
            ),
        array(
                'field' =>  'bank_id',
                'label' =>  'Bank Name',
                'rules' =>  'xss_clean|required|trim|numeric'
            ),
        array(
                'field' =>  'bank_branch_id',
                'label' =>  'Bank Branch Name',
                'rules' =>  'xss_clean|required|trim|numeric'
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
                'field' => 'account_signatories',
                'label' => 'Account Signatories',
                'rules' => 'callback__signatory_rules'
            )

        );

    function _is_unique_account(){
        $account_number = $this->input->post('account_number');
        $bank_id = $this->input->post('bank_id');
        $id = $this->input->post('id');
        if($bank_id){
            return TRUE;
        }

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

    function ajax_get_bank_branches(){
        $bank_id = $this->input->post('bank_id');
        $post = $this->banks_m->get($bank_id);
        $branch_id = $this->input->post('branch_id');
        if($bank_id)
        {
            $branches = $this->bank_branches_m->get_bank_branch_options_by_bank_id($bank_id);
            echo form_dropdown('bank_branch_id',array(''=>'--Select '.$post->name.' branch--')+$branches,$branch_id?:'','class="form-control select2" id="bank_branch_id"');
        }
    }

    public function ajax_connect(){
        $validation_rules = array(
            array(
                    'field' =>  'account_number',
                    'label' =>  'Account Number',
                    'rules' =>  'required|trim'
                ),
            array(
                    'field' =>  'phone',
                    'label' =>  'Signatory Phone Number',
                    'rules' =>  'required|trim|callback__valid_phone'
                ),
            array(
                    'field' =>  'id',
                    'label' =>  'Bank Account Id',
                    'rules' =>  'required|trim|numeric'
                ),
        );
        $this->form_validation->set_rules($validation_rules);
        if($this->form_validation->run()){
            $id = $this->input->post('id');
            if($bank_account = $this->bank_accounts_m->get_group_bank_account($id)){
                if($bank = $this->banks_m->get($bank_account->bank_id)){
                    $response = $this->bank->request_one_time_password($this->input->post('account_number'),$this->input->post('phone'),CALLING_CODE,$this->default_country->code,$bank->create_otp_url);
                    // if($response){
                    //     $result = json_decode(json_decode($response));
                    //     if(isset($result->rsData)){
                    //         if($result->rsData->status->code == '1' || $result->rsData->status->code == 1){
                    //             //$this->session->set_flashdata('success','The verification code was sent to '.$this->input->post('phone').'. Kindly proceed to enter the verification code');
                    //             $response = array(
                    //                 'message' => 'The verification code was sent to '.$this->input->post('phone').'. Kindly proceed to enter the verification code that was sent to you',
                    //                 'phone' => $this->input->post('phone'),
                    //                 'account_number' => $this->input->post('account_number'),
                    //             );
                    //             echo json_encode($response);
                    //         }else{
                    //             echo 'The Account Number did not match the signatory number. Kindly try another signatory phone number.';
                    //         }
                    //     }else{
                    //         echo 'Service is temporarily down. Kindly try again later.';
                    //         /*$response = array(
                    //                 'message' => 'The verification code was sent to '.$this->input->post('phone').'. Kindly proceed to enter the verification code that was sent to you',
                    //                 'phone' => $this->input->post('phone'),
                    //                 'account_number' => $this->input->post('account_number'),
                    //             );
                    //             echo json_encode($response);*/
                    //     }
                    // }else{
                        
                    //     /*$response = array(
                    //         'message' => 'The verification code was sent to '.$this->input->post('phone').'. Kindly proceed to enter the verification code',
                    //         'phone' => $this->input->post('phone'),
                    //         'account_number' => $this->input->post('account_number'),
                    //     );
                    //     echo json_encode($response);*/
                    
                    //     echo 'Service is temporarily down. Kindly try again later.';
                    // }
                    $response = array(
                        'message' => 'The verification code was sent to '.$this->input->post('phone').'. Kindly proceed to enter the verification code that was sent to you',
                        'phone' => $this->input->post('phone'),
                        'account_number' => $this->input->post('account_number'),
                    );
                    echo json_encode($response);
                }else{
                    echo 'Could not find group bank';
                }
            }else{
                echo 'Could not find group bank account';
            }
        }else{
            echo validation_errors();
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

    public function ajax_verify_ownership($id = ''){
        $validation_rules = array(
            array(
                    'field' =>  'account_number',
                    'label' =>  'Account Number',
                    'rules' =>  'required|trim'
                ),
            array(
                    'field' =>  'phone',
                    'label' =>  'Signatory Phone Number',
                    'rules' =>  'required|trim|callback__valid_phone'
                ),
            array(
                    'field' =>  'verification_code',
                    'label' =>  'Verification Code',
                    'rules' =>  'required|trim'
                ),
            array(
                    'field' =>  'id',
                    'label' =>  'Bank Account Id',
                    'rules' =>  'required|trim|numeric'
                ),
        );
        $this->form_validation->set_rules($validation_rules);
        if($this->form_validation->run()){
            $id = $this->input->post('id');
            if($bank_account = $this->bank_accounts_m->get_group_bank_account($id)){
                if($bank = $this->banks_m->get($bank_account->bank_id)){
                    $response = $this->bank->verify_one_time_password($this->input->post('account_number'),$this->input->post('phone'),strtoupper($this->input->post('verification_code')),CALLING_CODE,$this->default_country->code,$bank->verify_otp_url);
                    if($response){    
                        $result = json_decode(json_decode($response));
                        $input = array(
                            'actual_balance' => rand(1000,10000),
                            'is_verified'=>1,
                            'modified_on'=>time(),
                            'modified_by'=>$this->user->id
                        );
                        if($this->bank_accounts_m->update($id,$input)){
                            $response = array(
                                'message' => $message,
                                'phone' => $this->input->post('phone'),
                                'account_number' => $this->input->post('account_number'),
                            );
                            echo json_encode($response);
                        }else{
                            echo "Something went wrong when verifying the bank account.";
                        }
                        
                        // if($result->rsData->status->code == '1'){
                        //     $input = array(
                        //         'actual_balance' => rand(1000,10000),
                        //         'is_verified'=>1,
                        //         'modified_on'=>time(),
                        //         'modified_by'=>$this->user->id
                        //     );
                            
                        //     if($this->bank_accounts_m->update($id,$input)){
                        //         if($this->messaging->send_verification_confirmation_to_member($this->group,$this->member,$this->user,$this->input->post('account_number'))){
                        //             //if($this->group->status==1){
                        //                 if($this->messaging->send_bank_account_scheme_code_change_request($this->user,$this->group,$this->input->post('account_number'))){
                        //                     $message = 'The verification request was successful. Your account can now receive transaction alerts. ';
                        //                 }else{
                        //                     $message = 'The verification request was successful, but something went wrong while sending the scheme code change request. ';
                        //                 }
                        //                 $response = array(
                        //                     'message' => $message,
                        //                     'phone' => $this->input->post('phone'),
                        //                     'account_number' => $this->input->post('account_number'),
                        //                 );
                        //                 echo json_encode($response);
                        //             //}else{
                        //                 //$this->session->set_flashdata('success','The verification request was successful. Your account can now receive transaction alerts. ');
                        //             //}     
                        //         }else{
                        //             echo 'The verification request was successful, but could not send SMS and Email Confirmation to the member'; 
                        //         }
                        //     }else{
                        //         echo "Something went wrong when verifying the bank account.";
                        //     }
                        // }else if($result->rsData->status->code == '-1'){
                        //     echo 'The verification  request failed. Due to one of the following reasons;<br/><br/> <ol><li>The verification code has expired. Kindly request another one.</li><li>or the verfication code entered is incorrect. Kindly reenter the verification code carefully.</li></ol>';
                        // }else{
                        //     echo 'The verification  request failed. Kindly request another verification code';
                        // }
                    }else{
                        //echo 'System temporarily down. Try again later.'; 
                        $input = array(
                            'actual_balance' => rand(1000,10000),
                            'is_verified'=>1,
                            'modified_on'=>time(),
                            'modified_by'=>$this->user->id
                        );
                        if($this->bank_accounts_m->update($id,$input)){
                            $message = 'The verification request was successful. Your account can now receive transaction alerts. ';
                            $response = array(
                                'message' => $message,
                                'phone' => $this->input->post('phone'),
                                'account_number' => $this->input->post('account_number'),
                            );
                            echo json_encode($response);
                        }else{
                            echo "Something went wrong when verifying the bank account.";
                        }
                    }
                }else{
                    echo 'Could not find group bank';
                }
            }else{
                echo 'Could not find group bank account';
            }
        }else{
            echo validation_errors(); 
        }
    }

    public function ajax_edit(){
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $id = $this->input->post('id');
            $result = $this->bank_accounts_m->update($id,array(
                'group_id'          =>  $this->group->id,
                'account_number'    =>  $this->input->post('account_number'),
                'account_currency_id'    =>  $this->input->post('account_currency_id'),
                'account_name'      =>  $this->input->post('account_name'),
                'bank_branch_id'    =>  $this->input->post('bank_branch_id'),
                'bank_id'           =>  $this->input->post('bank_id'),
                //'enable_email_transaction_alerts_to_members'           =>  $this->input->post('enable_email_transaction_alerts_to_members')?1:0,
                'modified_by'        =>  $this->user->id,
                'modified_on'        =>  time(),
            ));
            if($result){
                if($bank_account = $this->bank_accounts_m->get_group_bank_account($id)){
                    $banks = $this->banks_m->get_group_bank_options();
                    $bank = $this->banks_m->get($bank_account->bank_id);
                    $bank_branches = $this->bank_branches_m->get_bank_branch_options_by_bank_id($bank_account->bank_id);
                    $bank_account->bank_details = $banks[$bank_account->bank_id].' ('.$bank_branches[$bank_account->bank_branch_id].')';
                    $bank_account->bank_name = $banks[$bank_account->bank_id];
                    $bank_account->bank_branch = $bank_branches[$bank_account->bank_branch_id];
                    $bank_account->bank_is_partner = $bank->partner;
                    $bank_account->bank_account_id = $bank_account->id;
                    $bank_account->account_currency_id = $bank->account_currency_id;
                    if($this->group->setup_tasks_position<4){
                         $update = array(
                            'setup_tasks_position' => 4,
                            'modified_by' => $this->user->id,
                            'modified_on' => time(),
                        );
                        $this->groups_m->update($this->group->id,$update);
                    }
                    echo json_encode($bank_account);
                }else{
                    echo 'Could not add find any bank account';
                }
            }else{
                echo 'Could not add bank account';
            }
        }else{
            echo validation_errors();
        }
    }

    function ajax_get_bank_accounts_listing(){
        $total_rows = $this->bank_accounts_m->count_all();
        $pagination = create_pagination('group/bank_accounts/listing/pages', $total_rows,50,5,TRUE);
        $posts = $this->bank_accounts_m->limit($pagination['limit'])->get_group_bank_accounts();
        $bank_account_email_transaction_alert_member_pairings_array = $this->bank_accounts_m->get_all_bank_account_email_transaction_alert_member_pairings_array();
        $bank_account_sms_transaction_alert_member_pairings_array = $this->bank_accounts_m->get_all_bank_account_sms_transaction_alert_member_pairings_array();
        $disabled = $this->input->get_post('disabled');
        $html = '';
        if(!empty($posts)){
            $html.= form_open('admin/banks/action', ' id="form"  class="form-horizontal"');
            if ( ! empty($pagination['links'])):
                $html.= '
                <div class="row col-md-12">
                    <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Bank Accounts</p>';
                 
                    $html.= '<div class ="top-bar-pagination">';
                    $html.= $pagination['links']; 
                    $html.= '</div></div>';
                    endif; 
                
                $html.= '
                <table class="table table-striped table-hover table-condensed table-searchable">
                    <thead>
                        <tr>
                            <th>
                                #
                            </th>
                            <th width="40%">
                                '.translate('Bank Account Details').'
                            </th>
                            <th class="text-right" width="20%">
                                '.translate('Balances').' ('.($this->currency_code_options[$this->group->currency_id]?:$this->default_country->currency_code).')
                            </th>
                            <th width="20%">
                                '.translate('Status').'
                            </th>
                            <th width="20%"'.($disabled?('class="hidden"'):'').'>
                                '.translate('Actions').'
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                        $i = $this->uri->segment(5, 0); foreach($posts as $post):
                        $html.= '
                            <tr class="'.$post->id.'_active_row">
                                <td>'.($i+1).'</td>
                                <td>
                                    <strong> '.translate('Account Name').': </strong>'.$post->account_name.'<br/>
                                    <strong> '.translate('Bank Name').': </strong>'.$post->bank_name.'<br/>
                                    <strong> '.translate('Branch Name').': </strong>'.$post->bank_branch.'<br/>
                                    <strong> '.translate('Account Number').': </strong>'.$post->account_number;
                                    if($post->is_verified):
                                        if(isset($bank_account_sms_transaction_alert_member_pairings_array[$post->id])):
                                            $html.= "<hr/>";
                                            $member_ids = $bank_account_sms_transaction_alert_member_pairings_array[$post->id];
                                            if($member_ids):
                                                $html.= "<strong>Members to be alerted via SMS on deposits and withdrawals: </strong>";
                                                $count = 1;
                                                foreach($member_ids as $member_id):
                                                    if($count==1){
                                                        $html.= $this->group_member_options[$member_id];
                                                    }else{
                                                        $html.= ",".$this->group_member_options[$member_id];
                                                    }
                                                    $count++;
                                                endforeach;
                                            endif;
                                        endif;
                                        if(isset($bank_account_email_transaction_alert_member_pairings_array[$post->id])):
                                            $html.= "<hr/>";
                                            $member_ids = $bank_account_email_transaction_alert_member_pairings_array[$post->id];
                                            if($member_ids):
                                                $html.= "<strong>".translate('Members to be alerted via E-mail on deposits and withdrawals').": </strong>";
                                                $count = 1;
                                                foreach($member_ids as $member_id):
                                                    if($count==1){
                                                        $html.= $this->group_member_options[$member_id];
                                                    }else{
                                                        $html.= ",".$this->group_member_options[$member_id];
                                                    }
                                                    $count++;
                                                endforeach;
                                            endif;
                                        endif;
                                    endif;
                                $html.= '
                                    <br/>
                                    <br/>
                                    <Strong>Signatories:</strong>'.($post->id).'
                                    <br/>';
                                    $signatories = $this->bank_accounts_m->get_group_bank_account_signatories($post->id);
                                    foreach ($signatories as $key => $signatory) {
                                        $html.='<strong>Name : </strong>'.$this->group_member_options[$signatory].'<br/>';
                                    }
                                $html.= 
                                '</td>
                                <td class="text-right">
                                    '.number_to_currency(floatval($post->initial_balance)+floatval($post->current_balance)).'
                                </td>
                                <td>';
                                    if($post->is_closed){
                                        $html.= "<span class='m-badge m-badge--warning m-badge--wide'>".translate('Closed')."</span>";
                                    }else{
                                        if($post->active){
                                            $html.= "<span class='m-badge m-badge--success m-badge--wide'>".translate('Active')."</span>";
                                        }else{
                                            $html.= "<span class='m-badge m-badge--default m-badge--wide'>".translate('Hidden')."</span>";
                                        }
                                    }
                                    if($post->partner){
                                        if($post->is_verified && $post->is_linked){
                                            $html.="&nbsp;&nbsp; <span class='m-badge m-badge--primary m-badge--wide'>".translate('Connected')."</span>";
                                        }else{
                                            $html.="&nbsp;&nbsp; <span class='m-badge m-badge--danger m-badge--wide'>".translate('Disconnected')."</span>";
                                        }
                                    }
                                $html.= '
                                </td>
                                <td class="actions">';
                                    // if($post->is_verified){
                                    //     $html.=  '
                                    //     <div class="btn-group">
                                    //         <a href="'.site_url('group/bank_accounts/edit/'.$post->id).'" class="btn btn-sm btn-primary disabled" aria-disabled="true">
                                    //             <i class="fa fa-edit"></i>
                                    //             Edit
                                    //         </a>
                                    //         <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split disabled" aria-disabled="true" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    //             <span class="sr-only">Toggle Dropdown</span>
                                    //         </button>
                                    //         <div class="dropdown-menu">
                                    //     ';
                                    //     if($post->is_closed){ 
                                    //         $html.=  '
                                    //             <a href="'.site_url('group/bank_accounts/reopen/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-play"></i>Reopen</a>
                                    //         ';
                                    //     }else{
                                    //         if($post->active){
                                    //             $html.=  '
                                    //                 <a href="'.site_url('group/bank_accounts/hide/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-eye-slash"></i>Hide</a>
                                    //             ';
                                    //             if($post->partner){
                                    //                 if($post->is_verified){ 
                                    //                     $html.=  '
                                    //                         <a href="'.site_url('group/bank_accounts/disconnect/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-unlock"></i>Disconnect</a>';
                                    //                 }else{
                                    //                     $html.=  '
                                    //                     <a href="'.site_url('group/bank_accounts/connect/'.$post->id).'" class="dropdown-item"><i class="fa fa-check-square"></i>Connect</a>';
                                    //                 }
                                    //             }
                                    //         }else{
                                    //             $html.=  '
                                    //                 <a data-original-title="Activate Bank Account" href="'.site_url('group/bank_accounts/activate/'.$post->id).'" class="dropdown-item confirmation_link"><i class="la la-check-square-o"></i>Unhide</a>
                                    //             ';
                                    //         }
                                    //         $html.=  '
                                    //             <a data-original-title="Close Bank Account" href="'.site_url('group/bank_accounts/close/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-pause"></i>Close</a>
                                    //         ';
                                    //     }
                                    //     $html.=  '
                                    //         <a data-original-title="Delete Bank Account" href="'.site_url('group/bank_accounts/delete/'.$post->id).'" class="dropdown-item prompt_confirmation_message_link" data-content="'.translate("This will erase all data associated to this account. Are you sure you want to proceed?").'" data-title="'.translate('Enter your password to confirm').'" id="'.$post->id.'"><i class="fa fa-trash"></i>Delete</a>
                                    //         </div>
                                    //     </div>';
                                    // }else{
                                        $html.=  '
                                        <div class="btn-group">
                                            <a href="'.site_url('group/bank_accounts/edit/'.$post->id).'" class="btn btn-sm btn-primary">
                                                <i class="fa fa-edit"></i>
                                                Edit
                                            </a>
                                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu">
                                        ';
                                        if($post->is_closed){ 
                                            $html.=  '
                                                <a href="'.site_url('group/bank_accounts/reopen/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-play"></i>Reopen</a>
                                            ';
                                        }else{
                                            if($post->active){
                                                $html.=  '
                                                    <a href="'.site_url('group/bank_accounts/hide/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-eye-slash"></i>Hide</a>
                                                ';
                                                if($post->partner){
                                                    if($post->is_verified && $post->is_linked){ 
                                                    }else{
                                                        $html.=  '
                                                        <a href="'.site_url('group/bank_accounts/connect/'.$post->id).'" class="dropdown-item"><i class="fa fa-check-square"></i>Connect</a>';
                                                    }
                                                }
                                            }else{
                                                $html.=  '
                                                    <a data-original-title="Activate Bank Account" href="'.site_url('group/bank_accounts/activate/'.$post->id).'" class="dropdown-item confirmation_link"><i class="la la-check-square-o"></i>Unhide</a>
                                                ';
                                            }
                                            $html.=  '
                                                <a data-original-title="Close Bank Account" href="'.site_url('group/bank_accounts/close/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-pause"></i>Close</a>
                                            ';
                                        }
                                        $html.=  '
                                            <a data-original-title="Delete Bank Account" href="'.site_url('group/bank_accounts/delete/'.$post->id).'" class="dropdown-item prompt_confirmation_message_link" data-content="'.translate("This will erase all data associated to this account. Are you sure you want to proceed?").'" data-title="'.translate('Enter your password to confirm').'" id="'.$post->id.'"><i class="fa fa-trash"></i>Delete</a>
                                            </div>
                                        </div>';
                                    // }
                                // }
                                $html.=  '
                                </td>
                            </tr>';
                            $i++;
                            endforeach; 
                        $html.= '
                    </tbody>
                </table>
                <div class="clearfix"></div>
                <div class="row col-md-12">';
                    if( ! empty($pagination['links'])): 
                    $html.= $pagination['links']; 
                    endif; 
                $html.= '
                </div>
                <div class="clearfix"></div>';    
            $html.= form_close(); 
        }else{
            $html.= '
            <div class="alert alert-info">
                <h4 class="block">'.translate('Information! No records to display').'</h4>
                <p>
                    '.translate('No Bank Accounts to display').'.
                </p>
            </div>';
        }

        if($posts){
            $status = 1;
        }else{
            $status = 1;
        }

        echo json_encode(array(
            "status" => $status,
            "html" => $html,
        ));
    }

    function ajax_get_bank_accounts_listing_complete(){
        $total_rows = $this->bank_accounts_m->count_all();
        $pagination = create_pagination('group/bank_accounts/listing/pages', $total_rows,50,5,TRUE);
        $posts = $this->bank_accounts_m->limit($pagination['limit'])->get_group_bank_accounts();
        $bank_account_email_transaction_alert_member_pairings_array = $this->bank_accounts_m->get_all_bank_account_email_transaction_alert_member_pairings_array();
        $bank_account_sms_transaction_alert_member_pairings_array = $this->bank_accounts_m->get_all_bank_account_sms_transaction_alert_member_pairings_array();
        if(!empty($posts)){
            echo form_open('admin/banks/action', ' id="form"  class="form-horizontal"');
            if ( ! empty($pagination['links'])):
                echo '
                <div class="row col-md-12">
                    <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Bank Accounts</p>';
                 
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links']; 
                    echo '</div></div>';
                    endif; 
                
                echo '
                <table class="table table-striped table-hover table-condensed table-searchable">
                    <thead>
                        <tr>
                            <th>
                                #
                            </th>
                            <th>
                                Bank Account Details
                            </th>
                            <th class="text-right">
                                Balances ('.($this->currency_code_options[$this->group->currency_id]?:$this->default_country->currency_code).')
                            </th>
                            <th>
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                        $i = $this->uri->segment(5, 0); foreach($posts as $post):
                        echo '
                            <tr>
                                <td>'.($i+1).'</td>
                                <td>
                                    <strong> Account Name: </strong>'.$post->account_name.'<br/>
                                    <strong> Bank Name: </strong>'.$post->bank_name.'<br/>
                                    <strong> Branch Name: </strong>'.$post->bank_branch.'<br/>
                                    <strong> Account Number: </strong>'.$post->account_number;
                                    if($post->is_verified):
                                        if(isset($bank_account_sms_transaction_alert_member_pairings_array[$post->id])):
                                            echo "<hr/>";
                                            $member_ids = $bank_account_sms_transaction_alert_member_pairings_array[$post->id];
                                            if($member_ids):
                                                echo "<strong>Members to be alerted via SMS on deposits and withdrawals: </strong>";
                                                $count = 1;
                                                foreach($member_ids as $member_id):
                                                    if($count==1){
                                                        echo $this->group_member_options[$member_id];
                                                    }else{
                                                        echo ",".$this->group_member_options[$member_id];
                                                    }
                                                    $count++;
                                                endforeach;
                                            endif;
                                        endif;
                                        if(isset($bank_account_email_transaction_alert_member_pairings_array[$post->id])):
                                            echo "<hr/>";
                                            $member_ids = $bank_account_email_transaction_alert_member_pairings_array[$post->id];
                                            if($member_ids):
                                                echo "<strong>Members to be alerted via E-mail on deposits and withdrawals: </strong>";
                                                $count = 1;
                                                foreach($member_ids as $member_id):
                                                    if($count==1){
                                                        echo $this->group_member_options[$member_id];
                                                    }else{
                                                        echo ",".$this->group_member_options[$member_id];
                                                    }
                                                    $count++;
                                                endforeach;
                                            endif;
                                        endif;
                                    endif;
                                echo '
                                </td>
                                <td class="text-right">
                                    '.number_to_currency(floatval($post->initial_balance)+floatval($post->current_balance)).'
                                </td>
                                <td>';
                                    if($post->is_closed){
                                        echo "<span class='label label-warning'>Closed</span>";
                                    }else{
                                        if($post->active){
                                            echo "<span class='label label-success'>Active</span>";
                                        }else{
                                            echo "<span class='label label-default'>Hidden</span>";
                                        }
                                    }
                                echo '
                                </td>
                                
                            </tr>';
                            $i++;
                            endforeach; 
                        echo '
                    </tbody>
                </table>
                <div class="clearfix"></div>
                <div class="row col-md-12">';
                    if( ! empty($pagination['links'])): 
                    echo $pagination['links']; 
                    endif; 
                echo '
                </div>
                <div class="clearfix"></div>';    
            echo form_close(); 
        }else{
            echo '
            <div class="alert alert-info">
                <h4 class="block">Information! No records to display</h4>
                <p>
                    No Bank Accounts to display.
                </p>
            </div>';
        }  
    }

    function hide($id=0,$redirect = TRUE){
        $id OR redirect('group/setup_tasks/link_bank_account');

        $post = $this->bank_accounts_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the bank account does not exist');
            redirect('group/setup_tasks/link_bank_account');
            return FALSE; 
        }

        if(!$post->active)
        {
            $this->session->set_flashdata('Error','Sorry, the bank account is already hidden');
            redirect('group/setup_tasks/link_bank_account');
            return FALSE; 
        }

        $res = $this->bank_accounts_m->update($post->id,array('active'=>NULL,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Bank Account successfully hidden');
            if($redirect)
            {
                redirect('group/setup_tasks/link_bank_account');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to hide the bank account');
            if($redirect)
            {
                redirect('group/setup_tasks/link_bank_account');
            }
        }
    }

    function activate($id=0,$redirect = TRUE){
        $id OR redirect('group/setup_tasks/link_bank_account');

        $post = $this->bank_accounts_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the bank account does not exist');
            redirect('group/setup_tasks/link_bank_account');
            return FALSE; 
        }

        if($post->active)
        {
            $this->session->set_flashdata('Error','Sorry, the bank account is already active');
            redirect('group/setup_tasks/link_bank_account');
            return FALSE; 
        }

        $res = $this->bank_accounts_m->update($post->id,array('active'=>1,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Bank Account successfully activated');
            if($redirect)
            {
                redirect('group/setup_tasks/link_bank_account');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to actvate the bank account');
            if($redirect)
            {
                redirect('group/setup_tasks/link_bank_account');
            }
        }
    }

    function close($id=0 , $redirect = TRUE){
        $id OR redirect('group/setup_tasks/link_bank_account');

        $post = $this->bank_accounts_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the bank account does not exist');
            redirect('group/setup_tasks/link_bank_account');
            return FALSE; 
        }

        if($post->is_closed)
        {
            $this->session->set_flashdata('Error','Sorry, the bank account is already closed');
            redirect('group/setup_tasks/link_bank_account');
            return FALSE; 
        }

        $res = $this->bank_accounts_m->update($post->id,array('is_closed'=>1,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Bank Account successfully closed');
            if($redirect)
            {
                redirect('group/setup_tasks/link_bank_account');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to close the bank account');
            if($redirect)
            {
                redirect('group/setup_tasks/link_bank_account');
            }
        }
    }

    function reopen($id=0 , $redirect = TRUE){
        $id OR redirect('group/setup_tasks/link_bank_account');

        $post = $this->bank_accounts_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the bank account does not exist');
            redirect('group/setup_tasks/link_bank_account');
            return FALSE; 
        }

        if(!$post->is_closed)
        {
            $this->session->set_flashdata('Error','Sorry, the bank account is already open');
            redirect('group/setup_tasks/link_bank_account');
            return FALSE; 
        }

        $res = $this->bank_accounts_m->update($post->id,array('is_closed'=>NULL,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Bank Account successfully re-opened');
            if($redirect)
            {
                redirect('group/setup_tasks/link_bank_account');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to re-open the bank account');
            if($redirect)
            {
                redirect('group/setup_tasks/link_bank_account');
            }
        }
    }

    function delete($id = 0){
        $response = array();
        $id = abs((int) filter_var($this->input->post('id'), FILTER_SANITIZE_NUMBER_INT));
        if($id){
            $post = $this->bank_accounts_m->get($id);
            if($post){
                if($this->user->id==$this->group->owner||$this->ion_auth->is_admin()||$this->user->id==$this->group->created_by){
                    $password = $this->input->post('password');
                    $identity = valid_phone($this->user->phone)?:$this->user->email;
                    if($this->ion_auth->login($identity,$password)){
                        if($this->transaction_statements_m->check_if_group_account_has_transactions('bank-'.$post->id,$post->group_id)){
                            $response = array(
                                'status'=> 0,
                                'message'=> 'The bank account has transactions associated to it, void all transactions associated to this account before deleting it',
                            );
                        }else{
                            if($this->bank_accounts_m->delete($post->id,$post->group_id)){
                                $response = array(
                                    'status'=> 1,
                                    'message'=> 'Bank account deleted successfully',
                                );
                            }else{
                                $response = array(
                                    'status'=> 0,
                                    'message'=> 'Bank account could not be deleted',
                                );
                            }
                        }
                    }else{
                        $response = array(
                            'status'=> 0,
                            'message'=> 'You entered the wrong password',
                        );
                    }
                }else{
                    $response = array(
                        'status'=> 0,
                        'message'=> 'You do not have sufficient permissions to delete a bank account.',
                    );
                }
            }else{
                $response = array(
                    'status'=>0,
                    'message'=>'Could not find group bank account selected'
                );
            }
        }else{
            $response = array(
                'status'=>0,
                'message'=>'Could not find group bank account selected'
            );
        }
        echo json_encode($response);
    }

    function create(){
        //check if account exists
        $response = array();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $bank_id = $this->input->post('bank_id');
            $default_bank = $this->banks_m->get_default_bank();
            $is_verified = 0;
            if($default_bank){
                if($default_bank->id == $bank_id){
                    $is_verified = ($this->ion_auth->is_bank_admin() || $this->ion_auth->is_admin())?1:0;
                }
            }
            $bank = $this->banks_m->get($bank_id);
            $account_number = $this->input->post('account_number');
            $bank_account = array(
                'group_id'          =>  $this->group->id,
                'account_number'    =>  $account_number,
                'account_currency_id'      =>  $this->input->post('account_currency_id')?:$bank->country_id,
                'account_name'      =>  $this->input->post('account_name'),
                'initial_balance'   =>  currency($this->input->post('initial_balance')),
                'bank_branch_id'    =>  $this->input->post('bank_branch_id'),
                'bank_id'           =>  $bank_id,
                'is_verified'       =>  $is_verified,
                'is_linked'       =>  0,
                'enable_email_transaction_alerts_to_members' =>  $this->input->post('enable_email_transaction_alerts_to_members')?1:0,
                'created_by'        =>  $this->user->id,
                'created_on'        =>  time(),
                'active'            =>  1,
            );
            $id = $this->bank_accounts_m->insert($bank_account);
            if($id){
                if(($this->ion_auth->is_bank_admin() || $this->ion_auth->is_admin())){
                    $this->_get_account_balance($id);
                }
                $bank_account['id'] = $id;
                $account_signatories = $this->input->post('account_signatories');
                if($account_signatories){
                    foreach ($account_signatories as $account_signatory) {
                        $group_ids[] =  $this->group->id;
                        $member_ids[] = $account_signatory;
                        $account_ids[] = $id;
                        $created_bys[] =  $this->user->id;
                        $created_ons[] =  time();
                    }
                    $input = array(
                        'group_id' => $group_ids,
                        'member_id' => $member_ids,
                        'bank_account_id' => $account_ids,
                        'created_by' => $created_bys,
                        'created_on' => $created_ons,
                    );
                    $this->bank_accounts_m->insert_batch_account_signatories($input);
                }
                $banks = $this->banks_m->get_group_bank_options();
                $bank_branches = $this->bank_branches_m->get_bank_branch_options_by_bank_id($bank_account['bank_id']);
                $bank_account['bank_details'] = $banks[$bank_account['bank_id']].' ('.$bank_branches[$bank_account['bank_branch_id']].')';
                $bank_account['bank_name'] = $banks[$bank_account['bank_id']];
                $bank_account['bank_branch'] = $bank_branches[$bank_account['bank_branch_id']];
                $response = array(
                    'status' => 1,
                    'id' => 'bank-'.$id,
                    'refer'=>site_url('group/bank_accounts/listing'),
                    'message' => 'Group Bank Account was successfully updated',
                );
                if($default_bank->id == $bank_id){
                    // $this->initiate_account_linkage($id,$account_number,$bank_id);
                    $response = array(
                        'bank_account' => $bank_account,
                        'status' => 1,
                        'is_default' => $this->banks_m->is_partner($bank_id),
                        'id' => 'bank-'.$id,
                        'refer' => site_url('group/bank_accounts/connect/'.$id),
                        'message' => 'Bank account successfully added',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not add bank account. Please try again later',
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

    function edit(){
        $response = array();
        $id = $this->input->post('id');
        if($id){
            $post = $this->bank_accounts_m->get($id);
            if($post){
                $this->form_validation->set_rules($this->validation_rules);
                if($this->form_validation->run()){
                    $bank_id = $this->input->post('bank_id');
                    $default_bank = $this->banks_m->get_default_bank();
                    $is_verified = 0;
                    if($default_bank){
                        if($default_bank->id == $bank_id){
                            $is_verified = ($this->ion_auth->is_bank_admin() || $this->ion_auth->is_admin())?1:0;
                        }
                    }
                    $initial_balance = floatval(currency($this->input->post('initial_balance')));
                    $account_number = $this->input->post('account_number');
                    $data = array(
                        'group_id'          =>  $this->group->id,
                        'account_number'    =>  $account_number,
                        'account_name'      =>  $this->input->post('account_name'),
                        'initial_balance'   =>  $initial_balance,
                        'bank_branch_id'    =>  $this->input->post('bank_branch_id'),
                        'bank_id'           =>  $bank_id,
                        'enable_email_transaction_alerts_to_members'=>  $this->input->post('enable_email_transaction_alerts_to_members')?1:0,
                        'enable_sms_transaction_alerts_to_members'=>$this->input->post('enable_sms_transaction_alerts_to_members')?1:0,
                        'account_password'  =>  $this->input->post('account_password'),
                        'modified_by'       =>  $this->user->id,
                        'modified_on'       =>  time(),
                        'is_verified'       =>  $is_verified,
                        'account_currency_id' => $this->input->post('account_currency_id'),
                    );
                    $update = $this->bank_accounts_m->update($post->id,$data);
                    if(($this->ion_auth->is_bank_admin() || $this->ion_auth->is_admin())){
                        $this->_get_account_balance($post->id);
                    }
                    $this->bank_accounts_m->delete_bank_account_email_transaction_alert_member_pairings($id);
                    if($this->input->post('bank_account_email_transaction_alert_member_list')){
                        $group_member_ids = $this->input->post('bank_account_email_transaction_alert_member_list');
                        foreach($group_member_ids as $member_id){
                            $input = array(
                                'member_id'=>$member_id,
                                'group_id'=>$this->group->id,
                                'bank_account_id'=>$post->id,
                                'created_on'=>time(),
                                'created_by'=>$this->user->id,
                            );
                            if($this->bank_accounts_m->insert_bank_account_email_transaction_alert_member_pairing($input)){

                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Could not insert bank account email transaction alert pairing',
                                );
                            }
                        }
                    }
                    if($this->input->post('bank_account_sms_transaction_alert_member_list')){
                        $this->bank_accounts_m->delete_bank_account_sms_transaction_alert_member_pairings($id);
                        $group_member_ids = $this->input->post('bank_account_sms_transaction_alert_member_list');
                        foreach($group_member_ids as $member_id){
                            $input = array(
                                'member_id'=>$member_id,
                                'group_id'=>$this->group->id,
                                'bank_account_id'=>$post->id,
                                'created_on'=>time(),
                                'created_by'=>$this->user->id,
                            );
                            if($this->bank_accounts_m->insert_bank_account_sms_transaction_alert_member_pairing($input)){

                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Could not insert bank account SMS transaction alert pairing',
                                );
                            }
                        }
                    }

                    $this->bank_accounts_m->delete_bank_account_signatories($id);
                    if($this->input->post('account_signatories')){
                        $account_signatories = $this->input->post('account_signatories');
                        if($account_signatories){
                            foreach ($account_signatories as $account_signatory) {
                                    $group_ids[] =  $this->group->id;
                                    $member_ids[] = $account_signatory;
                                    $account_ids[] = $id;
                                    $created_bys[] =  $this->user->id;
                                    $created_ons[] =  time();
                            }
                            $input = array(
                                'group_id' => $group_ids,
                                'member_id' => $member_ids,
                                'bank_account_id' => $account_ids,
                                'created_by' => $created_bys,
                                'created_on' => $created_ons,
                            );
                        }
                        $this->bank_accounts_m->insert_batch_account_signatories($input);                        
                    }
                    if($update){
                        $response = array(
                            'status' => 1,
                            'id' => 'bank-'.$id,
                            'refer'=>site_url('group/bank_accounts/listing'),
                            'message' => 'Group Bank Account was successfully updated',
                        );
                        $banks = $this->banks_m->get_group_bank_options();
                        $bank_branches = $this->bank_branches_m->get_bank_branch_options_by_bank_id($post->bank_id);
                        $bank_account['bank_details'] = $banks[$post->bank_id].' ('.$bank_branches[$post->bank_branch_id].')';
                        $bank_account['bank_name'] = $banks[$post->bank_id];
                        $bank_account['bank_branch'] = $bank_branches[$post->bank_id];
                        if($default_bank){
                            if($default_bank->id == $bank_id){
                                $url = site_url('group/bank_accounts/listing');
                                if(!$post->is_linked){
                                    $url = site_url('group/bank_accounts/connect/'.$post->id);
                                }
                                $response = array(
                                    'bank_account' => $bank_account,
                                    'status' => 1,
                                    'is_default' => $this->banks_m->is_partner($bank_id),
                                    'id' => 'bank-'.$id,
                                    'refer' => $url,
                                    'message' => 'Bank account successfully edited',
                                );
                            }
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'There was an error updating Group Bank Account',
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
                    'message' => 'The bank account does not exist',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Form validation failed',
            );
        }
        echo json_encode($response);
    }

    function initiate_account_linkage($id=0,$bank_id=0,$account_number=0){
        $response = array();
        $bank = $this->banks_m->get_bank_and_country($bank_id);
        $linkage_type = 0;
        if($bank){
            $country_code = strtoupper($bank->code);
            $account = $this->bank_accounts_m->get_group_bank_account($id);
            if($account){
                $response = $this->curl->equityBankRequests->account_linkage_status($account->account_number,$country_code);
                $notification_keys = '';
                if($response){
                    if($response->responseObject){
                        $linkage_type = 1;
                        $response = array(
                            'status' => 1,
                            'message' => 'Proceed to OTP generation',
                        );
                    }else{
                        $response = $this->curl->equityBankRequests->initiate_account_linkage($account->account_number,$country_code);
                        if($response){
                            if($response->statusCode=='00'){
                                $notification_keys = json_encode($response->responseObject);
                                $linkage_type = 2;
                                $response = array(
                                    'status' => 2,
                                    'message' => 'Proceed to OTP generation',
                                    'notification_keys' => $response->responseObject,
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'We could not complete the process at the moment, kindly try again later or contact support',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => $this->session->flashdata('error'),
                            );
                        }
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => $this->session->flashdata('error'),
                    );
                }
                $update = array(
                    'notification_keys' => $notification_keys,
                    'linkage_type' => $linkage_type,
                    'modified_on' => time(),
                    'modified_by' => $this->user->id,
                );
                if(!$this->bank_accounts_m->update($id,$update)){
                    $response = array(
                        'status' => 0,
                        'message' => 'We could not complete account linkage process at the moment, kindly try again later or contact support',
                    );              
                }else{
                    
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Account linkage failure. Account to link not found',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Account linkage failure. Default bank not found'
            );
        }
        echo json_encode($response+array('linkage_type'=>$linkage_type));
    }

    function setup_listing(){
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
            'account_name' => $name,
        );
        $total_rows = $this->bank_accounts_m->count_group_bank_accounts('',$filter_parameters);
        $pagination = create_custom_pagination('group/bank_accounts/listing/pages', $total_rows,$per_page,$start_number,TRUE);
        $posts = $this->bank_accounts_m->limit($pagination['limit'])->get_group_bank_accounts('',$filter_parameters);
        $num = $start_number+1;
        if($posts){
            $signatories = $this->bank_accounts_m->get_group_bank_accounts_signatories_array($posts,$this->group->id,$this->group_member_options);
            foreach ($posts as $key=>$post) {
                $account_signatories = isset($signatories[$post->id])?$signatories[$post->id]:array();
                $arr = '';
                foreach ($account_signatories as $account_signatory) {
                    $arr.=$account_signatory.'<br>';
                }
                $this->data[] = array(
                    ($num++).'.',
                    $post->account_name.' <br/> <strong>'.$post->bank_name.'</strong> at '.$post->bank_branch.' <br><br/> <strong> Account Number: </strong>  '.$post->account_number,
                    $arr,
                    number_to_currency(floatval($post->initial_balance)+floatval($post->current_balance)),
                    'bank-'.$post->id,
                    $post->is_default?1:0,
                    $post->is_verified?1:0,
                    $post->account_number,
                    $post->account_name,
                    $post->notification_keys,
                    $post->bank_id,
                    $post->wallet?:0,
                );
            }
        }

        $total_rows_saccos = $this->sacco_accounts_m->count_group_sacco_accounts('',$filter_parameters);
        $pagination = create_custom_pagination('group/bank_accounts/listing/pages', $total_rows_saccos,$per_page,$start_number,TRUE);
        $posts = $this->sacco_accounts_m->limit($pagination['limit'])->get_group_sacco_accounts('',$filter_parameters);
        if($posts){
            foreach ($posts as $key=>$post) {
                $this->data[] = array(
                    ($num++).'.',
                    $post->account_name.' <br/> <strong>'.$post->sacco_name.'</strong> at '.$post->sacco_branch.' <br><br/> <strong> Account Number: </strong>  '.$post->account_number,
                    '',
                    number_to_currency(floatval($post->initial_balance)+floatval($post->current_balance)),
                    'sacco-'.$post->id,
                );
            }
        }

        $total_rows_pettys = $this->petty_cash_accounts_m->count_group_petty_cash_accounts('',$filter_parameters);
        $pagination = create_custom_pagination('group/bank_accounts/listing/pages', $total_rows_pettys,$per_page,$start_number,TRUE);
        $posts = $this->petty_cash_accounts_m->limit($pagination['limit'])->get_group_petty_cash_accounts('',$filter_parameters);
        if($posts){
            foreach ($posts as $key=>$post) {
                $this->data[] = array(
                    ($num++).'.',
                    $post->account_name.' <br/> <strong>Petty Cash Account</strong>',
                    '',
                    number_to_currency(floatval($post->initial_balance)+floatval($post->current_balance)),
                    'petty-'.$post->id,
                );
            }
        }

        $total_rows_mobiles = $this->mobile_money_accounts_m->count_group_mobile_money_accounts('',$filter_parameters);
        $pagination = create_custom_pagination('group/bank_accounts/listing/pages', $total_rows_mobiles,$per_page,$start_number,TRUE);
        $posts = $this->mobile_money_accounts_m->limit($pagination['limit'])->get_group_mobile_money_accounts('',$filter_parameters);
        if($posts){
            foreach ($posts as $key=>$post) {
                $this->data[] = array(
                    ($num++).'.',
                    $post->account_name.' <br/> <strong>Mobile Account</strong> for '.$post->mobile_money_provider_name.' <br><br/> <strong> Account Number: </strong>  '.$post->account_number,
                    '',
                    number_to_currency(floatval($post->initial_balance)+floatval($post->current_balance)),
                    'mobile-'.$post->id,
                );
            }
        }
        $all_records = $this->bank_accounts_m->count_group_bank_accounts()+ $this->mobile_money_accounts_m->count_group_mobile_money_accounts()+$this->petty_cash_accounts_m->count_group_petty_cash_accounts()+$this->sacco_accounts_m->count_group_sacco_accounts();

        echo json_encode(array(
            "data" => $this->data,
            "iTotalDisplayRecords" => $total_rows+$total_rows_saccos+$total_rows_pettys+$total_rows_mobiles,
            "iTotalRecords" => $all_records,
        ));
    }

    function get(){
        $response = array();
        $id = abs((int) filter_var($this->input->post('id'), FILTER_SANITIZE_NUMBER_INT));
        if($id){
            $post = $this->bank_accounts_m->get_group_bank_account($id);
            if($post){
                $signatories = $this->bank_accounts_m->get_group_bank_account_signatories($post->id);
                $post = array_merge((array)$post,array('signatories'=>$signatories));
                echo json_encode($post);die;
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find the requested bank account',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find the requested bank account',
            );
        }
        echo json_encode($response);
    }

    function _signatory_rules(){
        $default_bank = $this->banks_m->get_default_bank();
        if($default_bank){
            if($this->input->post('bank_id') == $default_bank->id){
                $account_signatories = $this->input->post('account_signatories');
                if($account_signatories){
                    if(count($account_signatories)>=2){
                        return TRUE;
                    }else{
                        $this->form_validation->set_message('_signatory_rules','Kindly select atleast 2 account signatories before proceeding');
                        return FALSE;
                    }
                }else{
                    $this->form_validation->set_message('_signatory_rules','Kindly select atleast 2 account signatories');
                    return FALSE;
                }
            }
        }
    }

    function connect_bank_account(){
        $response = array();
        $validation_rules = array(
            array(
                    'field' =>  'id',
                    'label' =>  'Account ID',
                    'rules' =>  'required|trim|numeric'
                ),
                array(
                    'field' =>  'bank_id',
                    'label' =>  'Bank Account ID',
                    'rules' =>  'required|trim|numeric'
                ),
            array(
                    'field' =>  'linkage_type',
                    'label' =>  'Account Linkage Type',
                    'rules' =>  'required|trim|numeric'
                ),
        );

        if($this->input->post('linkage_type') == 1){
            $this->validation_rules[] = array(
                'field' =>  'phone',
                'label' =>  'Signatory Phone Number',
                'rules' =>  'required|trim|valid_phone'
            );
        }else if($this->input->post('linkage_type') == 2){
            $this->validation_rules[] = array(
                'field' =>  'notification_channel',
                'label' =>  'Account Notification Channel',
                'rules' =>  'required|trim'
            );
        }
        $this->form_validation->set_rules($validation_rules);
        if($this->form_validation->run()){
            $linkage_type = $this->input->post('linkage_type');
            $bank_id = $this->input->post('bank_id');
            $id = $this->input->post('id');
            $bank = $this->banks_m->get_bank_and_country($bank_id);
            if($bank){
                $country_code = strtoupper($bank->code); 
                $account = $this->bank_accounts_m->get_group_bank_account($id);
                if($account){
                    if($linkage_type == 1){
                        $phone_number = valid_phone($this->input->post('phone'));
                        $update = array(
                            'signatory_phone' => $phone_number,
                            'modified_on' => time(),
                            'modified_by' => $this->user->id,
                        );
                        $this->bank_accounts_m->update($id,$update);
                        $response = $this->bank->request_one_time_password($account->account_number,$phone_number,CALLING_CODE,$country_code);
                        $file = json_decode(json_decode($response));
                        if($file){
                            if(isset($file->rsData)){
                                if($file->rsData->status->code == '1' || $file->rsData->status->code == 1){
                                    $response = array(
                                        'message' => 'The verification code was sent to '.$this->input->post('phone').'. Kindly proceed to enter the verification code that was sent to you',
                                        'phone' => $this->input->post('phone'),
                                        'account_number' => $this->input->post('account_number'),
                                        'status' => 1,
                                        'refer' => site_url('group/bank_accounts/verify_ownership/'.$id),
                                    );
                                }else{
                                    $response = array(
                                        'message' => 'The Account Number did not match the signatory number. Kindly try another signatory phone number.',
                                        'status' => 0,
                                    );
                                }
                            }else{
                                $response = array(
                                    'message' => 'Service is temporarily down. Kindly try again later.',
                                    'status' => 0,
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Try again later. We could not complete the transaction at the moment',
                            ); 
                        }
                    }else{
                        $notication_channel =  $this->input->post('notification_channel');
                        $response = $this->curl->equityBankRequests->account_linkage_send_otp($account->account_number,$notication_channel,$country_code);
                        if($response){
                            $response = array(
                                'status' => 1,
                                'message' => 'Success',
                                'refer' => site_url('group/bank_accounts/verify_ownership/'.$id),
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => $this->session->flashdata('error'),
                            );
                        }
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'We could not link account. Bank account details not found',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'We could not link account. Bank details not found',
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

    function connect_bank_account_old(){
        $response = array();
        $validation_rules = array(
            array(
                    'field' =>  'account_number',
                    'label' =>  'Account Number',
                    'rules' =>  'required|trim'
                ),
            array(
                    'field' =>  'notification_channel',
                    'label' =>  'Notification Channel',
                    'rules' =>  'required|trim'
                ),
            array(
                    'field' =>  'id',
                    'label' =>  'Bank Account Id',
                    'rules' =>  'required|trim|numeric'
                ),
        );
        $this->form_validation->set_rules($validation_rules);
        if($this->form_validation->run()){
            $account_number = $this->input->post('account_number');
            $recipient_key = $this->input->post('notification_channel');
            $id = $this->input->post('id');
            $bank_id = $this->input->post('bank_id');
            $bank = $this->banks_m->get_bank_and_country($bank_id);
            if($bank){
                $country_code = strtoupper($bank->code);
                if($res = $this->curl->equityBankRequests->account_linkage_send_otp($account_number,$recipient_key,$country_code)){
                    // print_r($res);
                    if(isset($res->statusCode)){
                        if($res->statusCode == 99){
                            $res2 = $this->initiate_account_linkage($id,$bank_id,$account_number);
                            if($res2->statusCode=='00'){
                                $response = array(
                                    'status' => 2,
                                    'message'=>'Kindly select option and submit again',
                                    'id' => $id,
                                    'updated_keys' => json_encode($res2->responseObject),
                                );
                            }else if($res2->statusCode=='94'){
                                $res3 = $this->curl->equityBankRequests->account_linkage_status($account_number,$country_code);
                                if($res3){
                                    $input = array(
                                        'is_verified'=>1,
                                        'modified_on'=>time(),
                                        'verified_on'=>time(),
                                        'modified_by'=>$this->user->id
                                    );
                                    if($this->bank_accounts_m->update($id,$input)){
                                        $response = array(
                                            'status' => 3,
                                            'refer' => site_url('group/bank_accounts/listing'),
                                            'message' => 'Account already connected. Proceed.',
                                        );
                                    }else{
                                        $response = array(
                                            'status' => 3,
                                            'refer' => site_url('group/bank_accounts/listing'),
                                            'message' => 'We could not complete this process at the moment. Kindly contact the system admin for assistance.',
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
                                    'message' => $this->session->flashdata('error'),
                                );
                            }
                        }else if($res->statusCode == '00'){
                            $response = array(
                                'status' => 1,
                                'message' => 'successful. Enter the verification code received via channel selected',
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
                            'message' => $this->session->flashdata('error'),
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
                    'message' => 'Could not find bank to connect',
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

    function verify_ownership(){
        $response = array();
        $validation_rules = array(
            array(
                    'field' =>  'bank_id',
                    'label' =>  'Bank ID',
                    'rules' =>  'required|trim|numeric'
                ),
            array(
                    'field' =>  'verification_code',
                    'label' =>  'Verification Code',
                    'rules' =>  'required|trim'
                ),
            array(
                    'field' =>  'id',
                    'label' =>  'Bank Account Id',
                    'rules' =>  'required|trim|numeric'
                ),
            array(
                    'field' =>  'linkage_type',
                    'label' =>  'Bank Account Linkage Type',
                    'rules' =>  'required|trim|numeric'
                ),
        );
        if($this->input->post('linkage_type') == 1){
            $this->validation_rules[] = array(
                'field' =>  'phone',
                'label' =>  'Signatory Phone Number',
                'rules' =>  'required|trim|valid_phone'
            );
        }
        $this->form_validation->set_rules($validation_rules);
        if($this->form_validation->run()){
            $linkage_type = $this->input->post('linkage_type');
            $bank_id = $this->input->post('bank_id');
            $id = $this->input->post('id');
            $bank = $this->banks_m->get_bank_and_country($bank_id);
            if($bank){
                $country_code = strtoupper($bank->code); 
                $account = $this->bank_accounts_m->get_group_bank_account($id);
                if($account){
                    $verification_code = $this->input->post('verification_code');
                    if($linkage_type == 1){
                        $phone_number = valid_phone($this->input->post('phone'));
                        $response = $this->bank->verify_one_time_password($account->account_number,$phone_number,$verification_code,CALLING_CODE,$country_code);
                        $file = json_decode(json_decode($response));
                        if($file->rsData->status->code == '1'){
                            $this->_get_account_balance($id);
                            $input = array(
                                'is_verified'=>1,
                                'is_linked' => 1,
                                'modified_on'=>time(),
                                'verified_on'=>time(),
                                'modified_by'=>$this->user->id
                            );
                            if($this->bank_accounts_m->update($id,$input)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Account successfully linked',
                                    'refer' => site_url('group/bank_accounts/listing'),
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => "Something went wrong when verifying the bank account.",
                                );
                            }
                        }else if($result->rsData->status->code == '-1'){
                            $response = array(
                                'status' => 0,
                                'message' => 'The verification  request failed. Due to one of the following reasons;<br/><br/> <ol><li>The verification code has expired. Kindly request another one.</li><li>or the verfication code entered is incorrect. Kindly reenter the verification code carefully.</li></ol>',
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'The verification  request failed. Kindly request another verification code',
                            );
                        }
                    }else{
                        if($this->curl->equityBankRequests->account_linkage_verify_otp($account->account_number,$verification_code,$country_code)){
                            $this->_get_account_balance($id);
                            $input = array(
                                'is_verified'=>1,
                                'is_linked' => 1,
                                'modified_on'=>time(),
                                'verified_on'=>time(),
                                'modified_by'=>$this->user->id
                            );
                            if($this->bank_accounts_m->update($id,$input)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Account successfully linked',
                                    'refer' => site_url('group/bank_accounts/listing'),
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'We could not complete account linkage. Kindly refresh and try again',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => $this->session->flashdata('error'),
                            );
                        }
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'We could not link account. Bank account details not found',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'We could not link account. Bank details not found',
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

    function get_group_account_balances(){
        $response = array();
        $verified_bank_accounts = $this->bank_accounts_m->get_group_verified_partner_bank_accounts();
        foreach ($verified_bank_accounts as $account) {
            $calling_code = substr($account->calling_code,-2) ;
            $balance = $this->curl->equityBankRequests->get_account_balance($account->account_number,$calling_code);
            //$balance = $this->curl->equityBankRequests->get_account_balance($account->account_number);
            if($balance){
                $this->bank_accounts_m->update($account->id,array(
                    'actual_balance' => currency($balance),
                    //'initial_balance' => 0,
                ));
            }else{
                $this->bank_accounts_m->update($account->id,array(
                    'actual_balance' => 0,
                    //'initial_balance' => 0,
                ));
            }
        }
        $total_cash_at_bank= $this->accounts_m->get_group_total_actual_bank_balance($this->group->id);
        $response = array(
            'status' => 1,
            'currency' => $this->group_currency,
            'amount' => number_to_currency($total_cash_at_bank),
        );
        echo json_encode($response);
    }

    function _get_account_balance($id=0){
        $verified_bank_account = $this->bank_accounts_m->get_group_verified_bank_account($id,$this->group->id);
        if($verified_bank_account){
            $calling_code = substr($verified_bank_account->calling_code,-2) ;
            $balance = $this->curl->equityBankRequests->get_account_balance($verified_bank_account->account_number,$calling_code);
            if($balance){
                $this->bank_accounts_m->update($id,array(
                    'actual_balance' => currency($balance),
                ));
                return currency($balance);
            }else{
                return 0;
            }
        }
        return 0;
    }

    function disconnect_account(){
        $response = array();
        $id = abs((int) filter_var($this->input->post('id'), FILTER_SANITIZE_NUMBER_INT));
        if($id){
            $post = $this->bank_accounts_m->get($id);
            if($post){
                if($this->user->id==$this->group->owner||$this->ion_auth->is_admin()||$this->member->group_role_id){
                    $password = $this->input->post('password');
                    $identity = valid_phone($this->user->phone)?:$this->user->email;
                    if($this->ion_auth->login($identity,$password)){
                        $input = array(
                            'is_verified'=>0,
                            'modified_on'=>time(),
                            'verified_on'=>time(),
                            'modified_by'=>$this->user->id
                        );
                        if($this->bank_accounts_m->update($post->id,$input)){
                            $response = array(
                                'status'=> 1,
                                'message'=> 'Bank account disconnected successfully',
                            );
                        }else{
                            $response = array(
                                'status'=> 0,
                                'message'=> 'Bank account could not be disconnected',
                            );
                        }
                    }else{
                        $response = array(
                            'status'=> 0,
                            'message'=> 'You entered the wrong password',
                        );
                    }
                }else{
                    $response = array(
                        'status'=> 0,
                        'message'=> 'You do not have sufficient permissions to delete a bank account.',
                    );
                }
            }else{
                $response = array(
                    'status'=>0,
                    'message'=>'Could not find group bank account selected'
                );
            }
        }else{
            $response = array(
                'status'=>0,
                'message'=>'Could not find group bank account selected'
            );
        }
        echo json_encode($response);
    }
    

}