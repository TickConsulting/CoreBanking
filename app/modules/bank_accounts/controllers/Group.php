<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{

    protected $validation_rules = array(
        array(
                'field' =>  'account_name',
                'label' =>  'Account Name',
                'rules' =>  'required|trim'
            ),
        array(
                'field' =>  'bank_id',
                'label' =>  'Bank Name',
                'rules' =>  'required|trim|numeric'
            ),
        array(
                'field' =>  'id',
                'label' =>  'Bank Account ID',
                'rules' =>  'trim|numeric'
            ),
        array(
                'field' =>  'bank_branch_id',
                'label' =>  'Bank Branch Name',
                'rules' =>  'required|trim|numeric'
            ),
        array(
                'field' =>  'account_number',
                'label' =>  'Account Number',
                'rules' =>  'required|trim|numeric|callback__is_unique_account|min_length[5]|max_length[20]'
            ),
        array(
                'field' =>  'initial_balance',
                'label' =>  'Initial Balance',
                'rules' =>  'trim|currency'
            ),
        array(
                'field' =>  'enable_email_transaction_alerts_to_members',
                'label' =>  'Enable Email Transaction Alerts to Members',
                'rules' =>  'trim|numeric|callback_check_email_selected_members'
            ),
        array(
                'field' =>  'enable_sms_transaction_alerts_to_members',
                'label' =>  'Enable SMS Transaction Alerts to Members',
                'rules' =>  'trim|numeric|callback_check_sms_selected_members'
            ),
        array(
                'field' =>  'is_verified',
                'label' =>  '',
                'rules' =>  ''
            ),
         array(
                'field' =>  'account_password',
                'label' =>  '',
                'rules' =>  ''
            ),
         array(
                'field' =>  'bank_account_sms_transaction_alert_member_list',
                'label' =>  '',
                'rules' =>  ''
            ),

         
        );

    protected $data = array();
	
	function __construct(){
        parent::__construct();
        $this->load->library('bank');
        $this->load->model('bank_accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('bank_branches/bank_branches_m');
        $this->load->model('countries/countries_m');
        $this->load->model('transaction_statements/transaction_statements_m');
    }

    function index(){
        $this->template->title('Bank Accounts List')->build('group/listing',$this->data);
    }

    function _is_unique_account()
    {
        $account_number = $this->input->post('account_number');
        $bank_id = $this->input->post('bank_id');
        $id = $this->input->post('id');

        $account_exists = $this->bank_accounts_m->check_if_account_exists($id,$account_number,$bank_id);
        if($account_exists)
        {
            $this->form_validation->set_message('_is_unique_account','Sorry the account number '.'`'.$account_number.'`'.' is already registered and cannot allow duplicate');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    
    public function create(){
        $post = new stdClass();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run())
        {
            $id = $this->bank_accounts_m->insert(array(
                'group_id'          =>  $this->group->id,
                'account_currency_id' =>  $this->input->post('account_currency_id'),
                'account_number'    =>  $this->input->post('account_number'),
                'account_name'      =>  $this->input->post('account_name'),
                'initial_balance'   =>  currency($this->input->post('initial_balance')),
                'bank_branch_id'    =>  $this->input->post('bank_branch_id'),
                'bank_id'           =>  $this->input->post('bank_id'),
                'enable_email_transaction_alerts_to_members' =>  $this->input->post('enable_email_transaction_alerts_to_members')?1:0,
                'created_by'        =>  $this->user->id,
                'account_password'  =>  $this->input->post('account_password'),
                'created_on'        =>  time(),
                'active'            =>  1,
            ));

            if($id){
                $this->setup_tasks_tracker->set_completion_status('create-group-bank-account',$this->group->id,$this->user->id);
                $this->session->set_flashdata('success', 'Group Bank Account was successfully added');
                if($this->input->post('new_item')){
                    redirect('group/bank_accounts/create','refresh');
                }else{
                    if($this->banks_m->is_partner($this->input->post('bank_id'))){
                        redirect('group/bank_accounts/connect/'.$id.'/'.$this->input->post('account_number'),'refresh');
                    }else{
                        redirect('group/bank_accounts/listing','refresh');
                    }
                }
            }else{
                $this->session->set_flashdata('error', 'There was an error adding new Group Bank Account');
                redirect('group/bank_accounts/create','refresh');
            }
        }
        else
        {
            foreach ($this->validation_rules as $key => $field) {
                $field_value = $field['field'];
                 $post->$field_value= set_value($field['field']);
            }
        }
        $this->data['post'] = $post;
        $this->data['id'] = '';
        $default_bank = $this->banks_m->get_default_bank();
        $this->data['default_bank'] = $default_bank;
        $this->data['bank_account_signatories'] = array();
        $this->data['banks'] = $this->banks_m->get_group_bank_options();
        $this->data['bank_branches'] = array();
        $this->data['currencies'] = $this->countries_m->get_currency_options();
        if($default_bank){
            $this->data['bank_branches'] = $this->bank_branches_m->get_bank_branch_options_by_bank_id($default_bank->id);
        }
        $this->data['default_bank'] = $default_bank?:(object)array('id'=>0);
        if($default_bank){
            $this->template->title('Create '.$default_bank->name.' Group Bank Account')->build('group/form',$this->data);
        }else{
            $this->template->title('Create Group Bank Account')->build('group/form',$this->data);
        }
    }

    public function connect($id = 0,$account_number = ''){
        $id OR redirect('group/bank_accounts/listing');
        $post = $this->bank_accounts_m->get_group_bank_account($id);        
        $post OR redirect('group/bank_accounts/listing');
        $this->data['account'] = $post;
        $post->is_linked != 1 OR redirect('group/bank_accounts/listing');
        $bank = $this->banks_m->get($post->bank_id); 
        $bank OR redirect('group/bank_accounts/listing');
        $post = new stdClass();
        $this->data['bank_account_options'] = $this->bank_accounts_m->get_group_unlinked_partner_bank_account_options();
        $this->data['id'] = $id;
        $this->template->title('Connect '.$this->application_settings->application_name.' to your Group Bank Account')->build('group/connect',$this->data);
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
                    'rules' =>  'required|trim|valid_phone'
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
                    //     }
                    // }else{
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

    public function verify_ownership($id = ''){
        $id OR redirect('group/bank_accounts/listing');
        $post = $this->bank_accounts_m->get_group_bank_account($id);
        $post->is_linked != 1 OR redirect('group/bank_accounts/listing');
        $bank = $this->banks_m->get($post->bank_id); 
        $bank OR redirect('group/bank_accounts/listing');
        $this->data['bank_account_options'] = $this->bank_accounts_m->get_group_unlinked_partner_bank_account_options();
        $this->data['id'] = $id;
        $this->data['account'] = $post;
        $this->template->title('Connect Chamasoft to your Group Bank Account')->build('group/verify_ownership',$this->data);
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
                    'rules' =>  'required|trim|valid_phone'
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
                    // if($response){    
                    //     $result = json_decode(json_decode($response));
                    //     if($result->rsData->status->code == '1'){
                    //         $input = array(
                    //              'actual_account_balance' => rand(1000,1000000),
                    //             'is_verified'=>1,
                    //             'modified_on'=>time(),
                    //             'verified_on'=>time(),
                    //             'modified_by'=>$this->user->id
                    //         );
                            
                    //         if($this->bank_accounts_m->update($id,$input)){
                    //             if($this->messaging->send_verification_confirmation_to_member($this->group,$this->member,$this->user,$this->input->post('account_number'))){
                    //                     if($this->messaging->send_bank_account_scheme_code_change_request($this->user,$this->group,$this->input->post('account_number'))){
                    //                         $message = 'The verification request was successful. Your account can now receive transaction alerts. ';
                    //                     }else{
                    //                         $message = 'The verification request was successful, but something went wrong while sending the scheme code change request. ';
                    //                     }
                    //                     $response = array(
                    //                         'message' => $message,
                    //                         'phone' => $this->input->post('phone'),
                    //                         'account_number' => $this->input->post('account_number'),
                    //                     );
                    //                     echo json_encode($response);  
                    //             }else{
                    //                 echo 'The verification request was successful, but could not send SMS and Email Confirmation to the member'; 
                    //             }
                    //         }else{
                    //             echo "Something went wrong when verifying the bank account.";
                    //         }
                    //     }else if($result->rsData->status->code == '-1'){
                    //         echo 'The verification  request failed. Due to one of the following reasons;<br/><br/> <ol><li>The verification code has expired. Kindly request another one.</li><li>or the verfication code entered is incorrect. Kindly reenter the verification code carefully.</li></ol>';
                    //     }else{
                    //         echo 'The verification  request failed. Kindly request another verification code';
                    //     }
                    // }else{
                    //     echo 'System temporarily down. Try again later.'; 
                    // }

                    $input = array(
                        'actual_balance' => rand(1000,1000000),
                        'is_verified'=>1,
                        'modified_on'=>time(),
                        'verified_on'=>time(),
                        'modified_by'=>$this->user->id
                    );
                    $this->bank_accounts_m->update($id,$input);
                    if($this->messaging->send_bank_account_scheme_code_change_request($this->user,$this->group,$this->input->post('account_number'))){
                                            $message = 'The verification request was successful. Your account can now receive transaction alerts. ';
                                        }else{
                                            $message = 'The verification request was successful, but something went wrong while sending the scheme code change request. ';
                                        }
                                        $response = array(
                                            'message' => $message,
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

    public function disconnect($id = ''){
        $id OR redirect('group/bank_accounts/listing');
        $post = $this->bank_accounts_m->get_group_bank_account($id);
        $post->is_linked == 1 OR redirect('group/bank_accounts/listing');
        $input = array(
            'is_linked' => 0,
            // 'is_verified'=>0,
            'modified_on'=>time(),
            'modified_by'=>$this->user->id
        );
        if($this->bank_accounts_m->update($id,$input)){
            $this->session->set_flashdata('success','Your account was successfully disconnected. Your account will not receive transaction alerts. ');
        }else{
            $this->session->set_flashdata('error','Something went wrong when disconnecting the bank account.');
        }
        redirect('group/bank_accounts/listing');
    }

    public function ajax_create(){
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $id = $this->bank_accounts_m->insert(array(
                'group_id'          =>  $this->group->id,
                'account_number'    =>  $this->input->post('account_number'),
                'account_name'      =>  $this->input->post('account_name'),
                'initial_balance'   =>  currency($this->input->post('initial_balance')),
                'bank_branch_id'    =>  $this->input->post('bank_branch_id'),
                'bank_id'           =>  $this->input->post('bank_id'),
                'enable_email_transaction_alerts_to_members'           =>  $this->input->post('enable_email_transaction_alerts_to_members')?1:0,
                'created_by'        =>  $this->user->id,
                'created_on'        =>  time(),
                'active'            =>  1,
            ));
            if($id){
                if($bank_account = $this->bank_accounts_m->get_group_bank_account($id)){
                    $this->setup_tasks_tracker->set_completion_status('create-group-bank-account',$this->group->id,$this->user->id);
                    $banks = $this->banks_m->get_group_bank_options();
                    $bank = $this->banks_m->get($bank_account->bank_id);
                    $bank_branches = $this->bank_branches_m->get_bank_branch_options_by_bank_id($bank_account->bank_id);
                    $bank_account->bank_details = $banks[$bank_account->bank_id].' ('.$bank_branches[$bank_account->bank_branch_id].')';
                    $bank_account->bank_name = $banks[$bank_account->bank_id];
                    $bank_account->bank_branch = $bank_branches[$bank_account->bank_branch_id];
                    $bank_account->bank_is_partner = $bank->partner;
                    $bank_account->bank_account_id = $bank_account->id;
                    echo json_encode($bank_account);
                }else{
                    echo 'Could not add find any bank account';
                }
            }else{
                echo 'Could not add bank account';
            }
        }else{
            print_r($this->form_validation->error_array());
        }
    }

    public function ajax_edit(){
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $id = $this->input->post('id');
            $result = $this->bank_accounts_m->update($id,array(
                'group_id'          =>  $this->group->id,
                'account_number'    =>  $this->input->post('account_number'),
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


    function ajax_get_bank_branches()
    {
        $bank_id = $this->input->post('bank_id');
        $post = $this->banks_m->get($bank_id);
        $branch_id = $this->input->post('branch_id');
        if($bank_id)
        {
            $branches = $this->bank_branches_m->get_bank_branch_options_by_bank_id($bank_id);
            echo form_dropdown('bank_branch_id',array(''=>'--Select '.$post->name.' branch--')+$branches,$branch_id?:'','class="form-control select2-append" id="bank_branch_id"');
        }
    }

    function edit($id=0){
        $id OR redirect('group/bank_accounts/listing');
        $post = new stdClass();
        $post = $this->bank_accounts_m->get($id);
        if(empty($post)){
            $this->session->set_flashdata('Error','Sorry, the bank account does not exist');
            redirect('group/bank_accounts/listing');
            return FALSE;
        }
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $update = $this->bank_accounts_m->update($post->id,array(
                'group_id'          =>  $this->group->id,
                'account_number'    =>  $this->input->post('account_number'),
                'account_name'      =>  $this->input->post('account_name'),
                'initial_balance'   =>  currency($this->input->post('initial_balance')),
                'bank_branch_id'    =>  $this->input->post('bank_branch_id'),
                'bank_id'           =>  $this->input->post('bank_id'),
                'enable_email_transaction_alerts_to_members'            =>  $this->input->post('enable_email_transaction_alerts_to_members')?1:0,
                'enable_sms_transaction_alerts_to_members'              =>  $this->input->post('enable_sms_transaction_alerts_to_members')?1:0,
                'account_password'  =>  $this->input->post('account_password'),
                'modified_by'       =>  $this->user->id,
                'modified_on'       =>  time(),
            ));

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
                        $this->session->set_flashdata('error','Could not insert bank account email transaction alert pairing');
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
                        $this->session->set_flashdata('error','Could not insert bank account SMS transaction alert pairing');
                    }
                }
            }

            if($update){
                $this->session->set_flashdata('success', 'Group Bank Account was successfully updates');
                if($this->input->post('new_item')){
                    redirect('group/bank_accounts/create','refresh');
                }else{
                    redirect('group/bank_accounts/listing','refresh');
                }
            }else{
                $this->session->set_flashdata('error', 'There was an error updating Group Bank Account');
                redirect('group/bank_accounts/edit/'.$id,'refresh');
            }

        }
        else
        {
            // Go through all the known fields and get the post values
            foreach (array_keys($this->validation_rules) as $field)
            {
                 if (isset($_POST[$field]))
                {
                    $post->$field = $this->form_validation->$field;
                }
            }
        }
        
        $default_bank = $this->banks_m->get_default_bank();
        $this->data['default_bank'] = $default_bank;
        $this->data['post'] = $post;
        $this->data['id'] = $id;
        $this->data['banks'] = $this->banks_m->get_group_bank_options();
        $this->data['currencies'] = $this->countries_m->get_currency_options();
        $this->data['bank_branches'] = $this->bank_branches_m->get_bank_branch_options_by_bank_id($post->bank_id);
        $this->data['bank_account_signatories'] = $this->bank_accounts_m->get_group_bank_account_signatories($id);
        $this->data['email_selected_group_members'] = $this->bank_accounts_m->get_bank_account_email_transaction_alert_member_pairings_array($id,$this->group->id);
        $this->data['sms_selected_group_members'] = $this->bank_accounts_m->get_bank_account_sms_transaction_alert_member_pairings_array($id,$this->group->id);
        $this->template->title('Edit Group Bank Account')->build('group/form',$this->data);
    }

    function delete($id = 0){
        $id OR redirect('group/bank_accounts/listing');
        $post = new stdClass();
        $post = $this->bank_accounts_m->get($id);
        if($this->user->id==$this->group->owner || $this->ion_auth->is_admin()){
            $password = $this->input->get('confirmation_string');
            $identity = valid_phone($this->user->phone)?:$this->user->email;
            if($this->ion_auth->login($identity,$password)){
                if($this->transaction_statements_m->check_if_group_account_has_transactions('bank-'.$post->id,$post->group_id)){
                    $this->session->set_flashdata('warning','The bank account has transactions associated to it, void all transactions associated to this account before deleting it');
                }else{
                    if($this->bank_accounts_m->delete($post->id,$post->group_id)){
                        $this->session->set_flashdata('success','Bank account deleted successfully');
                    }else{
                        $this->session->set_flashdata('error','Bank account could not be deleted');
                    }
                }
            }else{
                $this->session->set_flashdata('warning','You entered the wrong password.');
            }
        }else{
            $this->session->set_flashdata('warning','You do not have sufficient permissions to delete a bank account.');
        }
        redirect('group/bank_accounts/listing');
    }


    function listing(){
        $this->template->title('Bank Accounts List')->build('group/listing',$this->data);
    }

    function ajax_get_bank_accounts_listing(){
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
                <table class="table m-table m-table--head-separator-primary">
                    <thead>
                        <tr>
                            <th nowrap>
                                #
                            </th>
                            <th nowrap>
                                Account Name
                            </th>
                            <th nowrap>
                                Bank
                            </th>
                            <th nowrap>
                                Branch
                            </th>
                            <th nowrap>
                                Account Number
                            </th>
                            <th class="text-right" nowrap>
                                Balances ('.($this->group_currency).')
                            </th>
                            <th nowrap>
                                Status
                            </th>
                            <th nowrap>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                        $i = $this->uri->segment(5, 0); foreach($posts as $post):
                        $initial_balance = is_numeric($post->initial_balance)?$post->initial_balance:0;
                        $current_balance = is_numeric($post->current_balance)?$post->current_balance:0;
                        echo '
                            <tr>
                                <td>'.($i+1).'</td>
                                <td>'.$post->account_name.'</td>
                                <td>'.$post->bank_name.'</td>
                                <td>'.$post->bank_branch.'</td>
                                <td>'.$post->account_number;
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
                                    '.number_to_currency($initial_balance + $current_balance).'
                                </td>
                                <td>';
                                    if($post->is_closed){
                                        if($post->is_default){
                                            echo "<span class='m-badge m-badge--warning m-badge--wide'>Active</span>";
                                        }else{

                                        }
                                    }else{
                                        if($post->active){
                                            echo "<span class='m-badge m-badge--success m-badge--wide'>Active</span>";
                                        }else{
                                            echo "<span class='m-badge m-badge--info m-badge--wide'>Hidden</span>";
                                        }
                                    }
                                echo '
                                </td>                                
                                <td class="actions">';
                                if($post->is_default){
                                    echo "<span class='m-badge m-badge--primary m-badge--wide'>E-Wallet - No Actions</span>";
                                }else{
                                    echo '
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
                                        echo '
                                            <a href="'.site_url('group/bank_accounts/reopen/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-play"></i>Reopen</a>
                                        ';
                                    }else{
                                        if($post->active){
                                            echo '
                                                <a href="'.site_url('group/bank_accounts/hide/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-eye-slash"></i>Hide</a>
                                            ';
                                            if($post->partner){
                                                if($post->is_verified){ 
                                                    echo '
                                                        <a href="'.site_url('group/bank_accounts/disconnect/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-unlock"></i>Disconnect</a>';
                                                }else{
                                                    echo '
                                                    <a href="'.site_url('group/bank_accounts/connect/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-check-square"></i>Connect</a>';
                                                }
                                            }
                                        }else{
                                            echo '
                                                <a data-original-title="Activate Bank Account" href="'.site_url('group/bank_accounts/activate/'.$post->id).'" class="dropdown-item confirmation_link"><i class="la la-check-square-o"></i>Unhide</a>
                                            ';
                                        }
                                        echo '
                                            <a data-original-title="Close Bank Account" href="'.site_url('group/bank_accounts/close/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-pause"></i>Close</a>
                                        ';
                                    }
                                    echo '
                                        <a data-original-title="Delete Bank Account" href="'.site_url('group/sacco_accounts/delete/'.$post->id).'" class="dropdown-item prompt_confirmation_message_link" id="'.$post->id.'"><i class="fa fa-trash"></i>Delete</a>
                                        </div>
                                    </div>';
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

    function hide($id=0,$redirect = TRUE)
    {
        $id OR redirect('group/bank_accounts/listing');

        $post = $this->bank_accounts_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the bank account does not exist');
            redirect('group/bank_accounts/listing');
            return FALSE; 
        }

        if(!$post->active)
        {
            $this->session->set_flashdata('Error','Sorry, the bank account is already hidden');
            redirect('group/bank_accounts/listing');
            return FALSE; 
        }

        $res = $this->bank_accounts_m->update($post->id,array('active'=>NULL,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Bank Account successfully hidden');
            if($redirect)
            {
                redirect('group/bank_accounts/listing');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to hide the bank account');
            if($redirect)
            {
                redirect('group/bank_accounts/listing');
            }
        }
    }

    function activate($id=0,$redirect = TRUE)
    {
        $id OR redirect('group/bank_accounts/listing');

        $post = $this->bank_accounts_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the bank account does not exist');
            redirect('group/bank_accounts/listing');
            return FALSE; 
        }

        if($post->active)
        {
            $this->session->set_flashdata('Error','Sorry, the bank account is already active');
            redirect('group/bank_accounts/listing');
            return FALSE; 
        }

        $res = $this->bank_accounts_m->update($post->id,array('active'=>1,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Bank Account successfully activated');
            if($redirect)
            {
                redirect('group/bank_accounts/listing');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to actvate the bank account');
            if($redirect)
            {
                redirect('group/bank_accounts/listing');
            }
        }
    }


    function close($id=0 , $redirect = TRUE)
    {
        $id OR redirect('group/bank_accounts/listing');

        $post = $this->bank_accounts_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the bank account does not exist');
            redirect('group/bank_accounts/listing');
            return FALSE; 
        }

        if($post->is_closed)
        {
            $this->session->set_flashdata('Error','Sorry, the bank account is already closed');
            redirect('group/bank_accounts/listing');
            return FALSE; 
        }

        $res = $this->bank_accounts_m->update($post->id,array('is_closed'=>1,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Bank Account successfully closed');
            if($redirect)
            {
                redirect('group/bank_accounts/listing');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to close the bank account');
            if($redirect)
            {
                redirect('group/bank_accounts/listing');
            }
        }
    }

    function reopen($id=0 , $redirect = TRUE)
    {
        $id OR redirect('group/bank_accounts/listing');

        $post = $this->bank_accounts_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the bank account does not exist');
            redirect('group/bank_accounts/listing');
            return FALSE; 
        }

        if(!$post->is_closed)
        {
            $this->session->set_flashdata('Error','Sorry, the bank account is already open');
            redirect('group/bank_accounts/listing');
            return FALSE; 
        }

        $res = $this->bank_accounts_m->update($post->id,array('is_closed'=>NULL,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Bank Account successfully re-opened');
            if($redirect)
            {
                redirect('group/bank_accounts/listing');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to re-open the bank account');
            if($redirect)
            {
                redirect('group/bank_accounts/listing');
            }
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

    function open_online_account(){
        if($this->bank->open_online_account($this->user,$this->group,$this->member)){
            echo 'done';
        }else{
            echo $this->session->flashdata('error');
        }
    }

}