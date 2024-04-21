<?php  defined('BASEPATH') OR exit('No direct script access allowed');
class Mobile extends Mobile_Controller{
    
    function __construct(){
        parent::__construct();

        $this->load->library('loan');
        $this->load->model('group_roles/group_roles_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('wallets/wallets_m');
        $this->interest_types_option = $this->loan->interest_types;
        $this->loan_interest_rate_per = $this->loan->loan_interest_rate_per;
        $this->interest_types = $this->loan->interest_types;
    }
    protected $validation_rules = array(
        array(
            'field' => 'loan_type_id',
            'label' => 'Loan Type',
            'rules' => 'xss_clean|trim|required|numeric',
        ),
        array(
            'field' => 'id_number',
            'label' => 'Id  Number',
            'rules' => 'xss_clean|trim|required',
        ),
        array(
            'field' =>  'loan_amount',
            'label' =>  'Loan Amount',
            'rules' =>  'xss_clean|trim|required|currency|greater_than[0]|callback__valid_application_amount'
        ),
        array(
            'field' => 'repayment_period', 
            'label' => 'Loan Repayment Period', 
            'rules' => 'trim|callback__valid_repayment_period'
        )
    );
  
    protected $application_rules = array(
        array(
            'field' => 'loan_type_id', 
            'label' => 'Loan Type', 
            'rules' => 'trim|required|numeric'
        ),array(
            'field' => 'loan_application_amount', 
            'label' => 'Loan Application Amount', 
            'rules' => 'trim|required|currency|callback__valid_application_amount'
        ),
        array(
            'field' => 'id_number', 
            'label' => 'Id Number', 
            'rules' => 'trim|required|currency|callback__valid_application_amount'
        ), array(
            'field' => 'loan_rules_check_box', 
            'label' => 'Agree to loan rules', 
            'rules' => 'trim|numeric'
        ), array(
            'field' => 'guaranteed_amount[]', 
            'label' => 'Guaranteed Amount', 
            'rules' => ''
        ),
        // array(
        //     'field' => 'guarantor_id[]', 
        //     'label' => 'Gurantor', 
        //     'rules' => 'callback__valid_guarantor_details'
        // ),
        array(
            'field' => 'repayment_period', 
            'label' => 'Loan Repayment Period', 
            'rules' => 'trim|callback__valid_repayment_period'
        )
    );

    // protected $validation_rules = array(
    //     array(
    //         'field' => 'member_id',
    //         'label' => 'Member Name',
    //         'rules' => 'required|xss_clean|trim|numeric|callback__member_exists',
    //     ),
    //     array(
    //         'field' => 'disbursement_date',
    //         'label' => 'Disbursement Date',
    //         'rules' => 'required|date|xss_clean|trim',
    //     ),
    //     array(
    //         'field' => 'loan_amount',
    //         'label' => 'Loan Amount',
    //         'rules' => 'required|xss_clean|trim|currency',
    //     ),
    //     array(
    //         'field' => 'repayment_period',
    //         'label' => 'Repayment Period',
    //         'rules' => 'required|xss_clean|trim|numeric',
    //     ),
    //     array(
    //         'field' => 'interest_rate',
    //         'label' => 'Interest Rate',
    //         'rules' => 'required|xss_clean|trim|numeric',
    //     ),
    //     array(
    //         'field' => 'loan_interest_rate_per',
    //         'label' => 'Loan Interest Rate Per',
    //         'rules' => 'required|xss_clean|trim|numeric',
    //     ),
    //     array(
    //         'field' => 'interest_type',
    //         'label' => 'Interest Type',
    //         'rules' => 'required|xss_clean|trim|numeric',
    //     ),
    //     array(
    //         'field' => 'account_id',
    //         'label' => 'Account',
    //         'rules' => 'xss_clean|trim|required|callback__valid_account_id'
    //     ),
    //     array(
    //         'field' => 'grace_period',
    //         'label' => 'Grace Period',
    //         'rules' => 'xss_clean|trim|required|numeric'
    //     ),
    //     array(
    //         'field' => 'enable_loan_fines',
    //         'label' => 'Enable Loan Fines',
    //         'rules' => 'xss_clean|trim'
    //     ),
    // );
    function _additional_validation_rules_mobile(){
        if($this->input->post('enable_loan_guarantors') == 1){
            $this->validation_rules[] = array(
                'field' => 'guarantor_id[]',
                'label' => 'Guarantor name required',
                'rules' => 'callback__verify_guarantor_name',
            );
            $this->validation_rules[] = array(
                'field' => 'guaranteed_amount[]',
                'label' => 'Guarantor Amount ',
                'rules' => 'callback__valid_application_amount',
            );
        }

    } 
    function _additional_validation_rules(){
        if($this->input->post('enable_loan_guarantors') == 1){
            $this->validation_rules[] = array(
                'field' => 'guarantor_id[]',
                'label' => 'Guarantor name required',
                'rules' => 'callback__verify_guarantor_name',
            );
            $this->validation_rules[] = array(
                'field' => 'guaranteed_amount[]',
                'label' => 'Guarantor Amount ',
                'rules' => 'callback__valid_application_amount',
            );
        }

    } 

    public $_from_loan_type_validation_rules = array(
        array(
            'field' => 'member_id',
            'label' => 'Member Name',
            'rules' => 'required|xss_clean|trim|numeric|callback__member_exists',
        ),
        array(
            'field' => 'disbursement_date',
            'label' => 'Disbursement Date',
            'rules' => 'required|date|xss_clean|trim',
        ),
        array(
            'field' => 'loan_amount',
            'label' => 'Loan Amount',
            'rules' => 'required|xss_clean|trim|currency',
        ),
        array(
            'field' => 'loan_type_id',
            'label' => 'Loan Type id',
            'rules' => 'xss_clean|trim',
        ),
    );

    protected $loan_type;

    function _member_exists(){
        $member_id = $this->input->post('member_id');
        $group_id = $this->input->post('group_id');
        if($this->members_m->get_member_where_member_id($member_id,$group_id)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_member_exists','Member selected does not exist in this group');
            return FALSE;
        }
    }

    function _valid_account_id(){
        $account_id = $this->input->post('account_id');
        $group_id = $this->input->post('group_id');
        if($this->accounts_m->check_if_group_account_exists($account_id,$group_id)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_valid_account_id','Group bank account does not exist');
            return FALSE;
        }

    }



    function _verify_guarantor_name(){
        $guarantors = $this->input->post('guarantor_id');
        $member_id = $this->input->post('member_id');
        $guaranteed_amounts = $this->input->post('guaranteed_amount');
        if(count($guarantors)>=1){
            for($i=0;$i<count($guarantors);$i++){
                if(empty($guarantors[$i])){
                  $this->form_validation->set_message('_verify_guarantor_name','The Guarantor Name field is required');
                    return FALSE;   
                }
                if($guarantors[$i]==$member_id){
                    $this->form_validation->set_message('_verify_guarantor_name','Guarantor number '.++$i.' should not be the same as the member taking the Loan');
                    return FALSE; 
                }
                if(!currency($guaranteed_amounts[$i]) && !empty($guaranteed_amounts[$i])){
                    $this->form_validation->set_message('_verify_guarantor_name','The Guaranteed amount row '.++$i.' must be a valid currency');
                    return FALSE;  
                }
                else{
                    return TRUE;
                }
            }
        }else{
            $this->form_validation->set_message('_verify_guarantor_name','Add atleast one guarantor');
            return FALSE;
        }
    }

    public function record_member_loan(){
        $guaranteed_amount= array();
        $guarantor_id = array();
        $guarantor_comment = array();
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                if(is_array($value)){
                    foreach ($value as $key_result_value => $value_result_value) {
                        $guaranteed_amount[$key_result_value] = $value_result_value->guaranteed_amount;
                        $guarantor_id[$key_result_value] = $value_result_value->member_id;
                        $guarantor_comment[$key_result_value] = $value_result_value->comment;
                    }
                }
                $_POST[$key] = $value;
            }
        }
        $_POST['guarantor_id'] = $guarantor_id;
        $_POST['guaranteed_amount'] = $guaranteed_amount;
        $_POST['guarantor_comment'] = $guarantor_comment;
        $user_id = $this->input->post('user_id')?:0;
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->group_role_id || $this->member->is_admin){

                        if($this->version_code > 77){
                            $loan_type_id = $this->input->post('loan_type_id');
                        }else{
                            $loan_type_id = $this->input->post('loan_type_id');
                        }
                        
                        if($loan_type_id){
                            $this->form_validation->set_rules($this->_from_loan_type_validation_rules);
                            if($this->form_validation->run()){
                                $loan_type_id = $this->input->post('loan_type_id');
                                $loan_type = $this->loan_types_m->get($loan_type_id);
                                if($loan_type){
                                    $custom_interest_procedure = '';
                                    $loan_details = array(
                                        'loan_type_id' => $loan_type->id,
                                        'disbursement_date' => $this->input->post('disbursement_date'),
                                        'loan_amount'   =>  $this->input->post('loan_amount'),
                                        'account_id'    =>  $this->input->post('account_id'),
                                        'repayment_period'  => isset($loan_type->repayment_period)?$loan_type->repayment_period:$this->input->post('repayment_period'),
                                        'interest_rate' =>  isset($loan_type->interest_rate)?$loan_type->interest_rate:$this->input->post('interest_rate'),
                                        'loan_interest_rate_per' =>  isset($loan_type->loan_interest_rate_per)?$loan_type->loan_interest_rate_per:$this->input->post('loan_interest_rate_per'),
                                        'interest_type' =>  isset($loan_type->interest_type)?$loan_type->interest_type:$this->input->post('interest_type'),
                                        'custom_interest_procedure'=>$custom_interest_procedure,
                                        'grace_period'  =>  $this->input->post('grace_period'),
                                        'grace_period_date'  =>  $this->input->post('grace_period_date')?strtotime($this->input->post('grace_period_date')):"",
                                        'sms_notifications_enabled' =>  isset($loan_type->sms_notifications_enabled)?$loan_type->sms_notifications_enabled:$this->input->post('sms_notifications_enabled'),
                                        'sms_template'  =>  isset($loan_type->sms_template)?$loan_type->sms_template:$this->input->post('sms_template'),
                                        'email_notifications_enabled' =>  isset($loan_type->email_notifications_enabled)?$loan_type->email_notifications_enabled:$this->input->post('email_notifications_enabled'),
                                        'enable_loan_fines' =>  isset($loan_type->enable_loan_fine)?$loan_type->enable_loan_fine:$this->input->post('enable_loan_fine'),
                                        'enable_outstanding_loan_balance_fines'=>isset($loan_type->enable_outstanding_loan_balance_fines)?$loan_type->enable_outstanding_loan_balance_fines:$this->input->post('enable_outstanding_loan_balance_fines'),
                                        'enable_loan_processing_fee' => isset($loan_type->enable_loan_processing_fee)?$loan_type->enable_loan_processing_fee:$this->input->post('enable_loan_processing_fee'),
                                        'enable_loan_fine_deferment' => isset($loan_type->enable_loan_fine_deferment)?$loan_type->enable_loan_fine_deferment:$this->input->post('enable_loan_fine_deferment'),
                                        'enable_loan_guarantors' => isset($loan_type->enable_loan_guarantors)?$loan_type->enable_loan_guarantors:$this->input->post('enable_loan_guarantors'),
                                        'enable_reducing_balance_installment_recalculation' => isset($loan_type->enable_reducing_balance_installment_recalculation)?$loan_type->enable_reducing_balance_installment_recalculation:$this->input->post('enable_reducing_balance_installment_recalculation'),
                                        'disable_automatic_loan_processing_income' => isset($loan_type->disable_automatic_loan_processing_income)?$loan_type->disable_automatic_loan_processing_income:$this->input->post('disable_automatic_loan_processing_income'),
                                        'group_id'=>  $this->group->id,
                                        'active'  =>  1,
                                        'created_by' =>  $this->user->id,
                                        'created_on'  =>  time(),
                                        'fixed_fine_amount' =>  isset($loan_type->fixed_fine_amount)?$loan_type->fixed_fine_amount:$this->input->post('fixed_fine_amount'),
                                        'fixed_amount_fine_frequency' =>  isset($loan_type->fixed_amount_fine_frequency)?$loan_type->fixed_amount_fine_frequency:$this->input->post('fixed_amount_fine_frequency'),
                                        'fixed_amount_fine_frequency_on' => isset($loan_type->fixed_amount_fine_frequency_on)?$loan_type->fixed_amount_fine_frequency_on:$this->input->post('fixed_amount_fine_frequency_on'),
                                        'percentage_fine_rate'  =>  isset($loan_type->percentage_fine_rate)?$loan_type->percentage_fine_rate:$this->input->post('percentage_fine_rate'),
                                        'percentage_fine_frequency'=>  isset($loan_type->percentage_fine_frequency)?$loan_type->percentage_fine_frequency:$this->input->post('percentage_fine_frequency'),
                                        'percentage_fine_on' =>  isset($loan_type->percentage_fine_on)?$loan_type->percentage_fine_on:$this->input->post('percentage_fine_on'),
                                        'one_off_fine_type'=>isset($loan_type->one_off_fine_type)?$loan_type->one_off_fine_type:$this->input->post('one_off_fine_type'),
                                        'one_off_fixed_amount' =>  isset($loan_type->one_off_fixed_amount)?$loan_type->one_off_fixed_amount:$this->input->post('one_off_fixed_amount'),
                                        'one_off_percentage_rate' =>  isset($loan_type->one_off_percentage_rate)?$loan_type->one_off_percentage_rate:$this->input->post('one_off_percentage_rate'),
                                        'one_off_percentage_rate_on' =>  isset($loan_type->one_off_percentage_rate_on)?$loan_type->one_off_percentage_rate_on:$this->input->post('one_off_percentage_rate_on'),
                                        'outstanding_loan_balance_fine_type'=> isset($loan_type->outstanding_loan_balance_fine_type)?$loan_type->outstanding_loan_balance_fine_type:$this->input->post('outstanding_loan_balance_fine_type'),
                                        'outstanding_loan_balance_fine_type' =>  isset($loan_type->outstanding_loan_balance_fine_type)?$loan_type->outstanding_loan_balance_fine_type:$this->input->post('outstanding_loan_balance_fine_type'),
                                        'outstanding_loan_balance_fine_fixed_amount'   => isset($loan_type->outstanding_loan_balance_fine_fixed_amount)?$loan_type->outstanding_loan_balance_fine_fixed_amount:$this->input->post('outstanding_loan_balance_fine_fixed_amount'),
                                        'outstanding_loan_balance_fixed_fine_frequency'=> isset($loan_type->outstanding_loan_balance_fixed_fine_frequency)?$loan_type->outstanding_loan_balance_fixed_fine_frequency:$this->input->post('outstanding_loan_balance_fixed_fine_frequency'),
                                        'outstanding_loan_balance_percentage_fine_rate'=> isset($loan_type->outstanding_loan_balance_percentage_fine_rate)?$loan_type->outstanding_loan_balance_percentage_fine_rate:$this->input->post('outstanding_loan_balance_percentage_fine_rate'),
                                        'outstanding_loan_balance_percentage_fine_frequency'=> isset($loan_type->outstanding_loan_balance_percentage_fine_frequency)?$loan_type->outstanding_loan_balance_percentage_fine_frequency:$this->input->post('outstanding_loan_balance_percentage_fine_frequency'),
                                        'outstanding_loan_balance_percentage_fine_on'=> isset($loan_type->outstanding_loan_balance_percentage_fine_on)?$loan_type->outstanding_loan_balance_percentage_fine_on:$this->input->post('outstanding_loan_balance_percentage_fine_on'),
                                        'outstanding_loan_balance_fine_one_off_amount'=> isset($loan_type->outstanding_loan_balance_fine_one_off_amount)?$loan_type->outstanding_loan_balance_fine_one_off_amount:$this->input->post('outstanding_loan_balance_fine_one_off_amount'),
                                        'loan_processing_fee_type'=> isset($loan_type->loan_processing_fee_type)?$loan_type->loan_processing_fee_type:$this->input->post('loan_processing_fee_type'),
                                        'loan_processing_fee_fixed_amount'  =>  isset($loan_type->loan_processing_fee_fixed_amount)?$loan_type->loan_processing_fee_fixed_amount:$this->input->post('loan_processing_fee_fixed_amount'),
                                        'loan_processing_fee_percentage_rate'=> isset($loan_type->loan_processing_fee_percentage_rate)?$loan_type->loan_processing_fee_percentage_rate:$this->input->post('loan_processing_fee_percentage_rate'),
                                        'loan_processing_fee_percentage_charged_on' =>  isset($loan_type->loan_processing_fee_percentage_charged_on)?$loan_type->loan_processing_fee_percentage_charged_on:$this->input->post('loan_processing_fee_percentage_charged_on')
                                    );
                                    $loan_type =  1;
                                    $member_id =  $this->input->post('member_id');
                                    $group_id  =  $this->group->id;
                                    $id = $this->loan->create_automated_group_loan($loan_type,$member_id,$group_id,$loan_details,'',$this->input->post('custom_interest_procedure'),'');
                                    if($id){
                                        $response = array(
                                            'status' => 1,
                                            'time' => time(),
                                            'success' => 'Successfully recorded member loan'
                                        );
                                    }else{
                                        $response = array(
                                            'status' => 0,
                                            'time' => time(),
                                            'message' => 'Error adding member loan',
                                        );
                                    }
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Loan type does not exist.',
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
                                    'time' => time(),
                                    'message' => 'Form validation failed',
                                    'validation_errors' => $post,
                                );
                            }
                        }else{
                            $this->_additional_rules();
                            $this->form_validation->set_rules($this->validation_rules);
                            if($this->form_validation->run()){
                                $loan_type =  1;
                                $member_id =  $this->input->post('member_id');
                                $group_id  =  $this->group->id;
                                $custom_interest_procedure = '';
                                $loan_details = array(
                                        'loan_type_id' => $this->input->post('loan_type_id'),
                                        'disbursement_date' => $this->input->post('disbursement_date'),
                                        'loan_amount'   =>  $this->input->post('loan_amount'),
                                        'account_id'    =>  $this->input->post('account_id'),
                                        'repayment_period'  =>  $this->input->post('repayment_period'),
                                        'interest_rate' =>  $this->input->post('interest_rate'),
                                        'loan_interest_rate_per' =>  $this->input->post('loan_interest_rate_per'),
                                        'interest_type' =>  $this->input->post('interest_type'),
                                        'custom_interest_procedure'=>$custom_interest_procedure,
                                        'grace_period'  =>  $this->input->post('grace_period'),
                                        'grace_period_date'  =>  $this->input->post('grace_period_date')?strtotime($this->input->post('grace_period_date')):"",
                                        'sms_notifications_enabled' =>  $this->input->post('sms_notifications_enabled'),
                                        'sms_template'  =>  $this->input->post('sms_template'),
                                        'email_notifications_enabled' =>  $this->input->post('email_notifications_enabled'),
                                        'enable_loan_fines' =>  $this->input->post('enable_loan_fines'),
                                        'enable_outstanding_loan_balance_fines'=>$this->input->post('enable_outstanding_loan_balance_fines'),
                                        'enable_loan_processing_fee' => $this->input->post('enable_loan_processing_fee'),
                                        'enable_loan_fine_deferment' => $this->input->post('enable_loan_fine_deferment'),
                                        'enable_loan_guarantors' => $this->input->post('enable_loan_guarantors'),
                                        'enable_reducing_balance_installment_recalculation' => $this->input->post('enable_reducing_balance_installment_recalculation'),
                                        'disable_automatic_loan_processing_income' => $this->input->post('disable_automatic_loan_processing_income'),
                                        'group_id'  =>  $this->group->id,
                                        'active'    =>  1,
                                        'created_by'    =>  $this->user->id,
                                        'created_on'    =>  time(),

                                );
                                $modified_by = $this->user->id;
                                $custom_loan_values = '';
                                if($this->input->post('enable_loan_fines')){
                                    $loan_fine_type    =  $this->input->post('loan_fine_type');
                                    $loan_details = $loan_details+array('loan_fine_type'=>$loan_fine_type);
                                    if($loan_fine_type==1)
                                    {
                                        $loan_details = $loan_details + array(
                                                'fixed_fine_amount' =>  $this->input->post('fixed_fine_amount'),
                                                'fixed_amount_fine_frequency'   =>  $this->input->post('fixed_amount_fine_frequency'),
                                                'fixed_amount_fine_frequency_on' => $this->input->post('fixed_amount_fine_frequency_on'),
                                            );
                                    }else if($loan_fine_type == 2){
                                        $loan_details = $loan_details + array(
                                                'percentage_fine_rate'  =>  $this->input->post('percentage_fine_rate'),
                                                'percentage_fine_frequency' =>  $this->input->post('percentage_fine_frequency'),
                                                'percentage_fine_on'    =>  $this->input->post('percentage_fine_on'),
                                            );
                                    }else if($loan_fine_type==3){
                                        $one_off_fine_type     =  $this->input->post('one_off_fine_type');
                                        $loan_details = $loan_details+array('one_off_fine_type'=>$one_off_fine_type);
                                        if($one_off_fine_type==1){
                                            $loan_details = $loan_details + array('one_off_fixed_amount'  =>  $this->input->post('one_off_fixed_amount'));
                                        }else if($one_off_fine_type==2){
                                            $loan_details = $loan_details + array(
                                                    'one_off_percentage_rate'   =>  $this->input->post('one_off_percentage_rate'),
                                                    'one_off_percentage_rate_on'    =>  $this->input->post('one_off_percentage_rate_on'),
                                                );
                                        }
                                    }
                                }
                                if($this->input->post('enable_outstanding_loan_balance_fines')){
                                    $outstanding_loan_balance_fine_type = $this->input->post('outstanding_loan_balance_fine_type');

                                    $loan_details = $loan_details+array('outstanding_loan_balance_fine_type'=>$outstanding_loan_balance_fine_type);
                                    if($outstanding_loan_balance_fine_type==1){
                                        $loan_details = $loan_details + array(
                                            'outstanding_loan_balance_fine_type'    =>  $this->input->post('outstanding_loan_balance_fine_type'),
                                            'outstanding_loan_balance_fine_fixed_amount'   =>$this->input->post('outstanding_loan_balance_fine_fixed_amount'),
                                            'outstanding_loan_balance_fixed_fine_frequency'=>$this->input->post('outstanding_loan_balance_fixed_fine_frequency'),
                                        );
                                    }else if($outstanding_loan_balance_fine_type==2){
                                        $loan_details = $loan_details + array(
                                                'outstanding_loan_balance_percentage_fine_rate'=>$this->input->post('outstanding_loan_balance_percentage_fine_rate'),
                                                'outstanding_loan_balance_percentage_fine_frequency'=>$this->input->post('outstanding_loan_balance_percentage_fine_frequency'),
                                                'outstanding_loan_balance_percentage_fine_on'=>$this->input->post('outstanding_loan_balance_percentage_fine_on'),
                                            );
                                    }else if($outstanding_loan_balance_fine_type==3){
                                        $loan_details = $loan_details + array(
                                                'outstanding_loan_balance_fine_one_off_amount'=>$this->input->post('outstanding_loan_balance_fine_one_off_amount'), 
                                            );
                                    }
                                }
                                if($this->input->post('enable_loan_processing_fee')){
                                    $loan_processing_fee_type  =  $this->input->post('loan_processing_fee_type');
                                    $loan_details = $loan_details + array('loan_processing_fee_type'=>$loan_processing_fee_type);
                                    if($loan_processing_fee_type==1){
                                        $loan_details = $loan_details + array('loan_processing_fee_fixed_amount'  =>  $this->input->post('loan_processing_fee_fixed_amount'),);
                                    }else if($loan_processing_fee_type==2){
                                        $loan_details = $loan_details + array(
                                            'loan_processing_fee_percentage_rate'=>$this->input->post('loan_processing_fee_percentage_rate'),
                                            'loan_processing_fee_percentage_charged_on' =>  $this->input->post('loan_processing_fee_percentage_charged_on'),);
                                    }
                                }
                                $guarantor_id = $this->input->post('guarantor_id');
                                $guaranteed_amount = $this->input->post('guaranteed_amount');
                                $guarantor_comment = $this->input->post('guarantor_comment');
                                $guarantors=array(
                                            'guarantor_id' => $guarantor_id,
                                            'guaranteed_amount' => $guaranteed_amount,
                                            'guarantor_comment' => $guarantor_comment
                                        );
                                if($this->input->post('custom_interest_procedure')==1)
                                {
                                    $custom_loan_values = array(
                                        'date_from' =>  $this->input->post('interest_rate_date_from'),
                                        'date_to' =>  $this->input->post('interest_rate_date_to'),
                                        'rate' =>  $this->input->post('custom_interest_rate'),
                                    );
                                }else if($this->input->post('custom_interest_procedure')==2){
                                    $custom_loan_values = array(
                                            'payment_date' =>  $this->input->post('custom_payment_date'),
                                            'amount_payable' =>  $this->input->post('custom_amount_payable'),
                                        );
                                }

                                $id = $this->loan->create_automated_group_loan($loan_type,$member_id,$group_id,$loan_details,$custom_loan_values,$this->input->post('custom_interest_procedure'),$guarantors);
                                if($id){
                                    $response = array(
                                            'status' => 1,
                                            'time' => time(),
                                            'success' => 'Successfully recorded member loan'
                                        );
                                }else{
                                    $response = array(
                                            'status' => 0,
                                            'time' => time(),
                                            'message' => 'Error adding member loan',
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
                                        'time' => time(),
                                        'message' => 'Form validation failed',
                                        'validation_errors' => $post,
                                    );
                            }
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
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
        echo encrypt_json_encode(array('response'=>$response));
    }

    public function edit_member_loan(){
        $guaranteed_amount= array();
        $guarantor_id = array();
        $guarantor_comment = array();
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                if(is_array($value)){
                    foreach ($value as $key_result_value => $value_result_value) {
                        $guaranteed_amount[$key_result_value] = $value_result_value->guaranteed_amount;
                        $guarantor_id[$key_result_value] = $value_result_value->member_id;
                        $guarantor_comment[$key_result_value] = $value_result_value->comment;
                    }
                }
                $_POST[$key] = $value;
            }
        }
        $_POST['guarantor_id'] = $guarantor_id;
        $_POST['guaranteed_amount'] = $guaranteed_amount;
        $_POST['guarantor_comment'] = $guarantor_comment;
        $user_id = $this->input->post('user_id')?:0;
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $id = $this->input->post('id');
                    $loan = $this->loans_m->get_member_group_loan($id,$this->group->id,$this->input->post('member_id'));
                    if($loan){
                        $this->_additional_rules();
                        $this->form_validation->set_rules($this->validation_rules);
                        if($this->form_validation->run()){
                            $loan_type =  1;
                            $member_id =  $this->input->post('member_id');
                            $group_id  =  $this->group->id;
                            $custom_interest_procedure = '';
                            $loan_details = array(
                                    'loan_type_id' => $this->input->post('loan_type_id'),
                                    'disbursement_date' => $this->input->post('disbursement_date'),
                                    'loan_amount'   =>  $this->input->post('loan_amount'),
                                    'account_id'    =>  $this->input->post('account_id'),
                                    'repayment_period'  =>  $this->input->post('repayment_period'),
                                    'interest_rate' =>  $this->input->post('interest_rate'),
                                    'loan_interest_rate_per' =>  $this->input->post('loan_interest_rate_per'),
                                    'interest_type' =>  $this->input->post('interest_type'),
                                    'custom_interest_procedure'=>$custom_interest_procedure,
                                    'grace_period'  =>  $this->input->post('grace_period'),
                                    'grace_period_date'  =>  $this->input->post('grace_period_date')?strtotime($this->input->post('grace_period_date')):"",
                                    'disable_automatic_loan_processing_income' => $this->input->post('disable_automatic_loan_processing_income'),
                                    'sms_notifications_enabled' =>  $this->input->post('sms_notifications_enabled'),
                                    'sms_template'  =>  $this->input->post('sms_template'),
                                    'email_notifications_enabled' =>  $this->input->post('email_notifications_enabled'),
                                    'enable_loan_fines' =>  $this->input->post('enable_loan_fines'),
                                    'enable_outstanding_loan_balance_fines'=>$this->input->post('enable_outstanding_loan_balance_fines'),
                                    'enable_loan_processing_fee' => $this->input->post('enable_loan_processing_fee'),
                                    'enable_loan_fine_deferment' => $this->input->post('enable_loan_fine_deferment'),
                                    'enable_loan_guarantors' => $this->input->post('enable_loan_guarantors'),
                                    'enable_reducing_balance_installment_recalculation' => $this->input->post('enable_reducing_balance_installment_recalculation'),
                                    'active'    =>  1,
                                    'modified_by'    =>  $this->user->id,
                                    'modified_on'    =>  time(),
                            );
                            $modified_by = $this->user->id;
                            $custom_loan_values = '';
                            if($this->input->post('enable_loan_fines')){
                                $loan_fine_type    =  $this->input->post('loan_fine_type');
                                $loan_details = $loan_details+array('loan_fine_type'=>$loan_fine_type);
                                if($loan_fine_type==1)
                                {
                                    $loan_details = $loan_details + array(
                                            'fixed_fine_amount' =>  $this->input->post('fixed_fine_amount'),
                                            'fixed_amount_fine_frequency'   =>  $this->input->post('fixed_amount_fine_frequency'),
                                            'fixed_amount_fine_frequency_on' => $this->input->post('fixed_amount_fine_frequency_on'),
                                        );
                                }else if($loan_fine_type == 2){
                                    $loan_details = $loan_details + array(
                                            'percentage_fine_rate'  =>  $this->input->post('percentage_fine_rate'),
                                            'percentage_fine_frequency' =>  $this->input->post('percentage_fine_frequency'),
                                            'percentage_fine_on'    =>  $this->input->post('percentage_fine_on'),
                                        );
                                }else if($loan_fine_type==3){
                                    $one_off_fine_type     =  $this->input->post('one_off_fine_type');
                                    $loan_details = $loan_details+array('one_off_fine_type'=>$one_off_fine_type);
                                    if($one_off_fine_type==1){
                                        $loan_details = $loan_details + array('one_off_fixed_amount'  =>  $this->input->post('one_off_fixed_amount'));
                                    }else if($one_off_fine_type==2){
                                        $loan_details = $loan_details + array(
                                                'one_off_percentage_rate'   =>  $this->input->post('one_off_percentage_rate'),
                                                'one_off_percentage_rate_on'    =>  $this->input->post('one_off_percentage_rate_on'),
                                            );
                                    }
                                }
                            }
                            if($this->input->post('enable_outstanding_loan_balance_fines')){
                                $outstanding_loan_balance_fine_type = $this->input->post('outstanding_loan_balance_fine_type');

                                $loan_details = $loan_details+array('outstanding_loan_balance_fine_type'=>$outstanding_loan_balance_fine_type);
                                if($outstanding_loan_balance_fine_type==1){
                                    $loan_details = $loan_details + array(
                                        'outstanding_loan_balance_fine_type'    =>  $this->input->post('outstanding_loan_balance_fine_type'),
                                        'outstanding_loan_balance_fine_fixed_amount'   =>$this->input->post('outstanding_loan_balance_fine_fixed_amount'),
                                        'outstanding_loan_balance_fixed_fine_frequency'=>$this->input->post('outstanding_loan_balance_fixed_fine_frequency'),
                                    );
                                }else if($outstanding_loan_balance_fine_type==2){
                                    $loan_details = $loan_details + array(
                                            'outstanding_loan_balance_percentage_fine_rate'=>$this->input->post('outstanding_loan_balance_percentage_fine_rate'),
                                            'outstanding_loan_balance_percentage_fine_frequency'=>$this->input->post('outstanding_loan_balance_percentage_fine_frequency'),
                                            'outstanding_loan_balance_percentage_fine_on'=>$this->input->post('outstanding_loan_balance_percentage_fine_on'),
                                        );
                                }else if($outstanding_loan_balance_fine_type==3){
                                    $loan_details = $loan_details + array(
                                            'outstanding_loan_balance_fine_one_off_amount'=>$this->input->post('outstanding_loan_balance_fine_one_off_amount'), 
                                        );
                                }
                            }
                            if($this->input->post('enable_loan_processing_fee')){
                                $loan_processing_fee_type  =  $this->input->post('loan_processing_fee_type');

                                $loan_details = $loan_details + array('loan_processing_fee_type'=>$loan_processing_fee_type);
                                if($loan_processing_fee_type==1){
                                    $loan_details = $loan_details + array('loan_processing_fee_fixed_amount'  =>  $this->input->post('loan_processing_fee_fixed_amount'),);
                                }else if($loan_processing_fee_type==2){
                                    $loan_details = $loan_details + array(
                                        'loan_processing_fee_percentage_rate'=>$this->input->post('loan_processing_fee_percentage_rate'),
                                        'loan_processing_fee_percentage_charged_on' =>  $this->input->post('loan_processing_fee_percentage_charged_on'),);
                                }
                            }
                            $guarantor_id = $this->input->post('guarantor_id');
                            $guaranteed_amount = $this->input->post('guaranteed_amount');
                            $guarantor_comment = $this->input->post('guarantor_comment');
                            $guarantors=array(
                                        'guarantor_id' => $guarantor_id,
                                        'guaranteed_amount' => $guaranteed_amount,
                                        'guarantor_comment' => $guarantor_comment
                                    );
                            if($this->input->post('custom_interest_procedure')==1)
                            {
                                $custom_loan_values = array(
                                    'date_from' =>  $this->input->post('interest_rate_date_from'),
                                    'date_to' =>  $this->input->post('interest_rate_date_to'),
                                    'rate' =>  $this->input->post('custom_interest_rate'),
                                );
                            }else if($this->input->post('custom_interest_procedure')==2){
                                $custom_loan_values = array(
                                        'payment_date' =>  $this->input->post('custom_payment_date'),
                                        'amount_payable' =>  $this->input->post('custom_amount_payable'),
                                    );
                            }
                            //$update = $this->loan->edit_automated_group_loan($loan->id,$loan_type,$member_id,$group_id,$modified_by,$loan_details,$guarantors,$custom_loan_values,$custom_interest_procedure);
                            $update = $this->loan->modify_automated_group_loan($loan->id,$loan_type,$member_id,$this->group->id,$loan_details,$custom_loan_values,$custom_interest_procedure,$guarantors,FALSE);

                            if($update){
                                $response = array(
                                        'status' => 1,
                                        'time' => time(),
                                        'success' => 'Successfully edited member loan'
                                    );
                            }else{
                                $response = array(
                                        'status' => 0,
                                        'time' => time(),
                                        'message' => 'Error adding member loan',
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
                                    'time' => time(),
                                    'message' => 'Form validation failed',
                                    'validation_errors' => $post,
                                );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'time' => time(),
                            'message' => 'Your are trying to edit a loan that does not exist',
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
        echo encrypt_json_encode(array('response'=>$response));
    }

    function void(){
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
                        $id = $this->input->post('id')?:0;
                        if($id){
                            $withdrawal = $this->withdrawals_m->get_group_withdrawal_by_loan_id($id,$this->group->id);
                            if($withdrawal&&$id){
                                if($this->transactions->void_group_withdrawal($withdrawal->id,$withdrawal,TRUE,$this->group->id)){
                                    $response = array(
                                        'status' => 1,
                                        'time' => time(),
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Error occured. Try again later',
                                    );
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Not details not found',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'time' => time(),
                                'message' => 'Loan details are missing',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
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
        echo encrypt_json_encode(array('response'=>$response));
    }

    function statement(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }

        }
        
        $user_id = $this->input->post('user_id')?:0;
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $loan_id = $this->input->post('id');
                    if($loan = $this->loans_m->get_loan_and_member($loan_id)){
                        $total_installment_payable = $this->loan_invoices_m->get_total_installment_loan_payable($loan->id);
                        $total_fines = $this->loan_invoices_m->get_total_loan_fines_payable($loan->id);
                        $total_transfers_out = $this->loan_invoices_m->get_total_loan_transfers_out($loan->id);
                        $total_paid = $this->loan_repayments_m->get_loan_total_payments($loan->id);
                        $loan_balance =$this->loans_m->get_loan_balance($loan->id);
                        $posts = $this->loans_m->get_loan_statement($loan->id,$group_id);
                        $group_role_options = $this->group_roles_m->get_group_role_options($this->group->id);
                        $user_data = array(
                            'name' => $loan->first_name.' '.$loan->last_name,
                            'email' => $loan->email,
                            'phone' => $loan->phone,
                            'avatar' => $loan->avatar,
                            'role' => $loan->group_role_id?$group_role_options[$loan->group_role_id]:'Member',
                        );
                        $amount = 0;
                        $total_amount=0;
                        $remaining_payable=0; 
                        $payable =$total_installment_payable+$total_fines+$total_transfers_out;
                        $statement_body[] = array(
                            "description" => 'Total Amount Payable',
                            'date'         =>   '',
                            'amount_paid'   =>  0,
                            'balance' => $balance = ($payable-$total_transfers_out),
                        );
                        foreach($posts as $post): 
                            if($post->transaction_type!=5){
                                $total_amount+=$post->amount;
                            }
                            if($post->transaction_type==5){
                                $description = 'Contibution Transfer';
                            }else{
                                $description = 'Payment';
                            }
                            $date = timestamp_to_datepicker($post->transaction_date);
                                
                            if($post->transaction_type==5){
                                $amount_paid = $post->amount?:0;
                            }else{
                                $amount = $post->amount?:0;
                                $amount_paid = $amount;
                            }
                            if($post->transaction_type==5){

                            }else{
                                $balance = $payable-$total_amount;
                            }

                            $statement_body[] = array(
                                "description" => $description,
                                'date' => $date,
                                "amount_paid" => $amount_paid,
                                "balance" => $balance,
                            );
                        endforeach;
                        $loan_interest_rate_per = $this->loan->loan_interest_rate_per;
                        $interest_types = $this->loan->interest_types;
                        $interest_rate = $loan->interest_rate.'% '.($loan->loan_interest_rate_per?$loan_interest_rate_per[$loan->loan_interest_rate_per]:$loan_interest_rate_per[4]).' '.$interest_types[$loan->interest_type];

                        $response = array(
                            "status" => 1,
                            "message" => "Successful",
                            "time" => time(),
                            "data" => array(
                                'is_fully_paid' => $loan->is_fully_paid?1:0,
                                "lump_sum" => $this->loan_invoices_m->get_loan_lump_sum_as_date($loan->id),
                                "description" => "Loan disbursed on ".timestamp_to_mobile_time($loan->disbursement_date).' at '.$interest_rate,
                                "user_data" => $user_data,
                                "statement_body" => $statement_body,
                                "statement_footer" => array(
                                    "type" => 'Total',
                                    "paid" => ($total_paid-$total_transfers_out),
                                    "balance" =>($payable-$total_paid),
                                ),
                                
                            ),
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not find group member loan details',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find group member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'User account not found',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function get_member_loan_options(){
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
                $member_id = $this->input->post('member_id');
                if($member_id){
                    $this->member = $this->members_m->get_group_member($member_id,$this->group->id);
                }else{
                    $this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id);
                }
                $loan_types_options = $this->loan_types_m->get_options($this->group->id);
                if($this->member){
                    $base_where = array('member_id'=>$this->member->id,'is_fully_paid'=>0,'group_id'=>$this->group->id);
                    $ongoing_member_loans = $this->loans_m->get_many_by($base_where,$group_id);
                    $loans = array();

                    foreach ($ongoing_member_loans as $ongoing_member_loan){
                        if(array_key_exists($ongoing_member_loan->loan_type_id, $loan_types_options)){
                            $loan_name =  $loan_types_options[$ongoing_member_loan->loan_type_id];
                        }else{
                            $loan_name = 'Normal Loan';
                        }
                        $ongoing_loan_amounts_payable= $this->loans_m->get_summation_for_invoice($ongoing_member_loan->id)->total_amount_payable;
                        $ongoing_loan_amounts_paid= $this->loan_repayments_m->get_loan_total_payments($ongoing_member_loan->id);

                        $balance = ($ongoing_loan_amounts_payable - $ongoing_loan_amounts_paid);
                        $loans[] = array(
                            'id' => $ongoing_member_loan->id,
                            'name' => $loan_name,
                            'amount' => $ongoing_member_loan->loan_amount,
                            'description' => 'Disbursed on '.timestamp_to_report_time($ongoing_member_loan->disbursement_date),
                            'balance' => $balance,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'Member loans',
                        'time' => time(),
                        'loans' => $loans,
                    );
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
        echo encrypt_json_encode(array('response'=>$response));
    }

    function get_group_loans_list(){
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
                    $is_member_loans = $this->input->post('is_member_loans');
                    $lower_limit = $this->input->post('lower_limit')?:0;
                    $upper_limit = $this->input->post('upper_limit')?:100;
                    $member_id = $this->input->post('member_id');
                    $records_per_page = $upper_limit - $lower_limit;
                    if($lower_limit>$upper_limit){
                        $records_per_page = 100;
                    }

                    if($is_member_loans){
                        $filter_parameters = array(
                            'member_id' => array($this->member->id),
                        );
                    }else{
                        
                        if($this->member->is_admin || $this->member->group_role_id){
                            $filter_parameters = array(   
                                'member_id' => array($member_id),
                            );
                        }else{
                            if($this->group->enable_member_information_privacy){
                                $filter_parameters = array(   
                                    'member_id' => array($this->member->id),
                                );
                            }else{
                                $filter_parameters = array(   
                                    'member_id' => array($member_id),
                                );
                            }
                        }
                    }
                    $total_rows = $this->loans_m->count_all_group_loans($filter_parameters,$this->group->id);
                    $pagination = create_custom_pagination('mobile',$total_rows,$records_per_page,$lower_limit,TRUE);
                    $posts = $this->loans_m->limit($pagination['limit'])->get_group_loans($filter_parameters,$this->group->id);
                    $account_options = $this->accounts_m->get_group_account_options('','',$this->group->id);
                    $loans = array();
                    if($posts){
                        $group_member_options = $this->members_m->get_group_member_options($this->group->id);
                        $loan_types_options = $this->loan_types_m->get_options($this->group->id);
                        foreach ($posts as $post) {
                            $interest_rate = $post->interest_rate.'% '.($post->loan_interest_rate_per?$this->loan->loan_interest_rate_per[$post->loan_interest_rate_per]:$this->loan->loan_interest_rate_per[4]).' '.$this->loan->interest_types[$post->interest_type];
                           $loans[] = array(
                                "id" => $post->id,
                                'name' => $group_member_options[$post->member_id],
                                'loan_type' => isset($loan_types_options[$post->loan_type_id])?$loan_types_options[$post->loan_type_id]:'Normal Loan',
                                'description' => 'Loan disbursed to '.$group_member_options[$post->member_id].' on '.timestamp_to_mobile_shorttime($post->disbursement_date).' at '.$interest_rate.' payable in '.$post->repayment_period.' months. Disbursed from '.$account_options[$post->account_id],
                                "member_id" => $post->member_id,
                                'amount' => $post->loan_amount,
                                'disbursement_date' => timestamp_to_mobile_shorttime($post->disbursement_date),
                                "end_date" => timestamp_to_mobile_shorttime($post->loan_end_date),
                                "is_fully_paid" => $post->is_fully_paid?:0,
                           );
                        }
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'success',
                        'loans' => $loans
                    );
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
        echo encrypt_json_encode(array('response'=>$response));
    }

    function get_group_loan(){
        $usernames = array();
        $phones = array();
        $group_role_ids = array();
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id')?:'';
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $id = $this->input->post('id');
                    if($id){
                        if($post = $this->loans_m->get_group_loan($id,$this->group->id)){
                            $account_options = $this->accounts_m->get_group_account_options('','',$this->group->id);
                            $account_name = $account_options[$post->account_id];
                            $loan_types = $this->loan_types_m->get_options($this->group->id);
                            $loan_type_name = isset($loan_types[$post->loan_type_id])?$loan_types[$post->loan_type_id]:'Normal Loan';
                            $post = array_merge((array)$post,array(
                                'account_name' => $account_name,
                                'loan_type_name' => $loan_type_name,
                            ));
                            $response = array(
                                'status' => 1,
                                'message' => 'success',
                                'loan' => (object)$post,
                            );

                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Group loan not available',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Bad request: Missing loan id',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function get_members_loans_options(){
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
                    $base_where = array('is_fully_paid'=>0,'group_id'=>$this->group->id);
                    $ongoing_member_loans = $this->loans_m->get_many_by($base_where,$group_id);
                    $loan_types_options = $this->loan_types_m->get_options($this->group->id);
                    $loans = array();
                    foreach ($ongoing_member_loans as $ongoing_member_loan){
                        if(array_key_exists($ongoing_member_loan->loan_type_id, $loan_types_options)){
                            $loan_name =  $loan_types_options[$ongoing_member_loan->loan_type_id];
                        }else{
                            $loan_name = 'Normal Loan';
                        }
                        $ongoing_loan_amounts_payable= $this->loans_m->get_summation_for_invoice($ongoing_member_loan->id)->total_amount_payable;
                        $ongoing_loan_amounts_paid= $this->loan_repayments_m->get_loan_total_payments($ongoing_member_loan->id);

                        $balance = ($ongoing_loan_amounts_payable - $ongoing_loan_amounts_paid);
                        $loans[] = array(
                            'id' => $ongoing_member_loan->id,
                            'name' => $loan_name,
                            'amount' => $ongoing_member_loan->loan_amount,
                            'description' => 'Disbursed on '.timestamp_to_report_time($ongoing_member_loan->disbursement_date),
                            'balance' => $balance,
                            "member_id" => $ongoing_member_loan->member_id,
                            "is_selected" => $ongoing_member_loan->member_id==$this->member->id?1:0,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'Member loans',
                        'loans' => $loans,
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
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
    function _get_member_loan_limit($group_id='',$member_id){
        $loan_limit = 0;
        $loan_products = $this->loan_types_m->get_group_loan_types($group_id);
        if($loan_products){
            $data['contribution_options'] = $this->contributions_m->get_group_savings_contribution_options();
            $member_savings = $this->reports_m->get_group_member_total_contributions($this->member->id,$data['contribution_options']);
            foreach ($loan_products as $key => $loan_product):
                if($loan_product->loan_amount_type == 1){//amount_range
                    $loan_limit = currency($loan_product->maximum_loan_amount);
                }else if($loan_product->loan_amount_type == 2){//member savings 3 
                    $loan_limit = $loan_product->loan_times_number * currency($member_savings);
                }else{
                  $loan_limit = $member_savings; 
                }                
            endforeach;

            return $loan_limit;
        }
    }
    function get_loan_applicant_loan_summary(){
       
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('id_number');
        if($this->user = $this->users_m->get_user_by_id_number($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            // $group_id = $this->input->post('group_id');
                if($this->member = $this->members_m->get_group_member_by_id_number($this->user->id_number)){
                    if($this->member->active){
                        $today = time();
                        $outstanding_loan = 0;
                        $amount_paid = 0;
                        $total_amount_paid = 0;
                        $total_loan_borrowed = 0;
                        $total_loan_amount_payable = 0;
                        $matured_loans = array();
                        $not_matured_loans= array();
                        $ongoing_loan_amounts_payable = array();
                        $base_where = array('member_id'=>$this->member->id,'is_fully_paid'=>0);
                        $ongoing_member_loans = $this->loans_m->get_many_by($base_where);
                        $loan_types = $this->loan_types_m->get_options();
                        foreach($ongoing_member_loans as $ongoing_member_loan):
                            $total_loan_borrowed += $ongoing_member_loan->loan_amount;
                            $ongoing_loan_amounts_payable[$ongoing_member_loan->id]
                            = $this->loans_m->get_summation_for_invoice($ongoing_member_loan->id)->total_amount_payable;
                            $ongoing_loan_amounts_paid[$ongoing_member_loan->id]
                            = $this->loan_repayments_m->get_loan_total_payments($ongoing_member_loan->id);
                            $newest_invoice = $this->loan_invoices_m->get_newest_invoice($ongoing_member_loan->id);

                            $total_paid = $this->loan_repayments_m->get_loan_total_payments($ongoing_member_loan->id);
                            $total_installment_payable = $this->loan_invoices_m->get_total_installment_loan_payable($ongoing_member_loan->id);
                            $percentage_paid = 0;
                            $percentage_paid = ($total_paid?($total_paid/$total_installment_payable) * 100:0);
                            $loan_balance = $total_installment_payable - $total_paid; 
                            if($newest_invoice){                       
                                if($today > $ongoing_member_loan->grace_period_end_date){
                                    $matured_loans[] = array(
                                        'loan_id'=>$ongoing_member_loan->id,
                                        'total_loan_amount_payable'=>$ongoing_loan_amounts_payable[$ongoing_member_loan->id],
                                        'loan_amount'=>$ongoing_member_loan->loan_amount,
                                        'loan_name'=>$loan_types[$ongoing_member_loan->loan_type_id],
                                        'loan_balance'=>$loan_balance,
                                        'percentage_paid'=>$percentage_paid,
                                        'interest_rate'=>$ongoing_member_loan->interest_rate,
                                        'repayment_period'=>$ongoing_member_loan->repayment_period,
                                        'loan_from'=>date('M Y',$ongoing_member_loan->disbursement_date),
                                        'loan_to'=>date('M Y',$ongoing_member_loan->loan_end_date),
                                        'timestamp_from'=>$ongoing_member_loan->disbursement_date,
                                        'timestamp_to'=>$ongoing_member_loan->loan_end_date,
                                        'loan_invoice_id'=>$newest_invoice->id?$newest_invoice->id:0,
                                        'next_payment_date'=>timestamp_to_datepicker($newest_invoice->due_date),
                                        'next_payment_amount'=>$newest_invoice->principle_amount_payable,
                                        'amount_paid'=>$total_paid,

                                    );
                                }else if($today < $ongoing_member_loan->grace_period_end_date){
                                    $not_matured_loans[] = array(
                                        'loan_id'=>$ongoing_member_loan->id,
                                        'total_loan_amount_payable'=>$ongoing_loan_amounts_payable[$ongoing_member_loan->id],
                                        'loan_amount'=>$ongoing_member_loan->loan_amount,
                                        'loan_from'=>date('M Y',$ongoing_member_loan->disbursement_date),
                                        'loan_to'=>date('M Y',$ongoing_member_loan->loan_end_date),
                                        'timestamp_from'=>$ongoing_member_loan->disbursement_date,
                                        'timestamp_to'=>$ongoing_member_loan->loan_end_date,
                                        'loan_invoice_id'=>$newest_invoice->id?$newest_invoice->id:0,
                                        'first_installment'=>timestamp_to_datepicker($newest_invoice->due_date),
                                        'installment_amount'=>$newest_invoice->principle_amount_payable,
                                        'loan_name'=>$loan_types[$ongoing_member_loan->loan_type_id],
                                        'loan_balance'=>$loan_balance,
                                        'percentage_paid'=>$percentage_paid,
                                        'interest_rate'=>$ongoing_member_loan->interest_rate,
                                        'repayment_period'=>$ongoing_member_loan->repayment_period,
                                        'amount_paid'=>$total_paid,
                                    );
                                }
                            }
                        endforeach;
                        if(!empty($ongoing_loan_amounts_payable)){
                            foreach($ongoing_loan_amounts_payable as $key => $amount_payable):
                                $outstanding_loan+= $amount_payable - $ongoing_loan_amounts_paid[$key];                                
                                $total_amount_paid += $ongoing_loan_amounts_paid[$key];
                            endforeach;
                        }
                        $application_details = array();                        
                        $pending_loans = $this->loan_applications_m->get_member_pending_loan_applications($this->member->id);  
                        if($pending_loans){
                            foreach ($pending_loans as $key => $pending_loan):
                                $application_details[] = array(
                                    'id'=>$pending_loan->id,
                                    'name'=>$loan_types[$pending_loan->loan_type_id],
                                    'amount'=>$pending_loan->loan_amount,
                                    'duration' =>$pending_loan->repayment_period,
                                    'status'=>$pending_loan->status,
                                    'application_date'=>timestamp_to_mobile_report_time($pending_loan->created_on),
                                    'status_flag'=>0, //pending approvals
                                );
                            endforeach;
                        }
                        $approved_loan_status = array();
                        $approved_applications = $this->loan_applications_m->get_member_approved_loan_applications($this->member->id);
                        if($approved_applications){
                            foreach($approved_applications as $key => $approved_application):
                                if($approved_application->status == 0){
                                    $approved_loan_status[] = array(
                                        'id'=>$approved_application->id,
                                        'status'=>$approved_application->status,
                                        'name'=>$loan_types[$approved_application->loan_type_id],
                                        'amount'=>$approved_application->loan_amount,
                                        'duration' =>$approved_application->repayment_period,
                                        'application_date'=>timestamp_to_mobile_report_time($approved_application->created_on),
                                        'description'=>'Disbursment in progress' ,
                                        'status_flag'=>1, //approved in progress                                  
                                    );
                                }else if($approved_application->status == 1){
                                    $approved_loan_status[] = array(
                                        'id'=>$approved_application->id,
                                        'name'=>$loan_types[$approved_application->loan_type_id],
                                        'amount'=>$approved_application->loan_amount,
                                        'duration' =>$approved_application->repayment_period,
                                        'status'=>$approved_application->status,
                                        'application_date'=>timestamp_to_mobile_report_time($approved_application->created_on),
                                        'description'=>'Loan disbursed',
                                        'status_flag'=>2, //approved disbursed 
                                    );
                                }elseif ($approved_application->status == 2){
                                    $approved_loan_status[] = array(
                                        'id'=>$approved_application->id,
                                        'name'=>$loan_types[$approved_application->loan_type_id],
                                        'amount'=>$approved_application->loan_amount,
                                        'duration' =>$approved_application->repayment_period,
                                        'status'=>$approved_application->status,
                                        'application_date'=>timestamp_to_mobile_report_time($approved_application->created_on),
                                        'description'=>'Disbursment failed',
                                        'status_flag'=>3, //approved disbursment failed 
                                    );
                                }
                            endforeach;

                        }
                        $declined_details = array();
                        $declined_applications = $this->loan_applications_m->get_member_declined_loan_applications($this->member->id);
                        if($declined_applications){
                            foreach ($declined_applications as $key => $declined_application):
                                if(array_key_exists($declined_application->loan_type_id, $loan_types)){
                                    $declined_details[] = array(
                                        'id'=>$declined_application->id,
                                        'name'=>$loan_types[$declined_application->loan_type_id],
                                        'amount'=>$declined_application->loan_amount,
                                        'duration' =>$declined_application->repayment_period,
                                        'status'=>$declined_application->status,
                                        'application_date'=>timestamp_to_mobile_report_time($declined_application->created_on),
                                        'status_flag'=>4, //loan declined
                                    );
                                }
                            endforeach;
                        }

                        $loan_limit = $this->_get_member_loan_limit('',$this->member->id);
                        $loan_details = array(
                            'loan_balance'=>$outstanding_loan,
                            'total_amount_borrowed'=>$total_loan_borrowed,
                            'total_amount_paid'=>$total_amount_paid,
                            'loan_limit'=>$loan_limit                           
                        );
                        $response = array(
                            'status'=>1,
                            'time'=>time(),
                            'loan_details'=>$loan_details,
                            'matured_loans'=>$matured_loans,
                            'not_matured_loans'=>$not_matured_loans,
                            'application_details'=>array_merge($declined_details,array_merge($approved_loan_status,$application_details)),
                           
                        );                                               
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find member details',
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
    function get_group_member_loan_types(){        
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
                    if($this->member->active){
                        $loan_products = array();
                        $loan_types = $this->loan_types_m->get_group_loan_types();
                        $data['contribution_options'] = $this->contributions_m->get_group_savings_contribution_options();
                        $member_savings = $this->reports_m->get_group_member_total_contributions($this->member->id,$data['contribution_options']);
                        $this->group_currency = $this->currency_code_options[$this->group->currency_id]; 
                        foreach ($loan_types as $key => $loan_type):
                            if($loan_type->loan_amount_type == 1){//amount_range
                                $amount_range = ''.$this->group_currency.' '.number_to_currency($loan_type->minimum_loan_amount) .' to '.$this->group_currency.' '.number_to_currency($loan_type->maximum_loan_amount); 
                                $loan_limit = currency($loan_type->maximum_loan_amount);
                            }else if($loan_type->loan_amount_type == 2){//member savings 3                                
                                $loan_limit = $loan_type->loan_times_number * currency($member_savings);
                                $amount_range = 'Upto '.$this->group_currency .' '.number_to_currency($loan_limit); 
                            }else{
                              $loan_limit = $member_savings; 
                            }
                            if($loan_type->loan_repayment_period_type == 1){
                                $repayment_period = $loan_type->fixed_repayment_period .' months';
                            }else{
                                $repayment_period = $loan_type->minimum_repayment_period.' - '.$loan_type->maximum_repayment_period.' months';
                            }
                            $loan_interest_rate_narration = $loan_type->interest_rate.'% '.$this->loan_interest_rate_per[$loan_type->loan_interest_rate_per].' on '.$this->interest_types[$loan_type->interest_type];

                            $loan_products[] = array(
                                'id'=>$loan_type->id,
                                'name'=>$loan_type->name,
                                'grace_period'=>$loan_type->grace_period .' months',
                                'interest_type'=>$this->interest_types_option[$loan_type->interest_type],
                                'maximum_amount'=>$loan_limit,
                                'amount_range'=>$amount_range,
                                'interest_rate'=>$loan_type->interest_rate,
                                'repayment_period'=>$repayment_period,
                                'loan_interest_rate_per'=>$this->loan_interest_rate_per[$loan_type->loan_interest_rate_per],
                                'loan_interest_rate_narration'=>$loan_interest_rate_narration,
                                'loan_guarantor_type'=>$loan_type->loan_guarantors_type,
                                'minimum_guarantors'=>$loan_type->minimum_guarantors,
                                'repayment_period_type'=>$loan_type->loan_repayment_period_type,
                            );
                        endforeach;
                        $response = array(
                            'status'=>1,
                            'time'=>time(),
                            'loan_products'=>$loan_products,
                        );                                      
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
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

    function get_active_member_loans(){
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
                    if($this->member->active){
                        $today = time();
                        $outstanding_loan = 0;
                        $amount_paid = 0;
                        $total_amount_paid = 0;
                        $total_loan_borrowed = 0;
                        $total_loan_amount_payable = 0;
                        $matured_loans = array();
                        $not_matured_loans= array();
                        $ongoing_loan_amounts_payable = array();
                        $ongoing_member_loans = $this->loans_m->get_member_loans($this->group->id,$this->member->id);
                        $loan_types = $this->loan_types_m->get_options();
                        foreach($ongoing_member_loans as $ongoing_member_loan):
                            $total_loan_borrowed += $ongoing_member_loan->loan_amount;
                            $ongoing_loan_amounts_payable[$ongoing_member_loan->id]
                            = $this->loans_m->get_summation_for_invoice($ongoing_member_loan->id)->total_amount_payable;
                            $ongoing_loan_amounts_paid[$ongoing_member_loan->id]
                            = $this->loan_repayments_m->get_loan_total_payments($ongoing_member_loan->id);
                            $newest_invoice = $this->loan_invoices_m->get_newest_invoice($ongoing_member_loan->id);

                            $total_paid = $this->loan_repayments_m->get_loan_total_payments($ongoing_member_loan->id);
                            $total_installment_payable = $this->loan_invoices_m->get_total_installment_loan_payable($ongoing_member_loan->id);
                            $percentage_paid = 0;
                            $percentage_paid = ($total_paid?($total_paid/$total_installment_payable) * 100:0);
                            $loan_balance = $total_installment_payable - $total_paid;                        
                            $matured_loans[] = array(
                                'loan_id'=>$ongoing_member_loan->id,
                                'total_loan_amount_payable'=>$total_installment_payable,
                                'loan_amount'=>$ongoing_member_loan->loan_amount,
                                'loan_name'=>isset($loan_types[$ongoing_member_loan->loan_type_id])?$loan_types[$ongoing_member_loan->loan_type_id]:'',
                                'loan_balance'=>$loan_balance,
                                'percentage_paid'=>$percentage_paid,
                                'interest_type_narration'=>$this->loan->interest_types[$ongoing_member_loan->interest_type],
                                'interest_rate_narration'=>$this->loan->loan_interest_rate_per[$ongoing_member_loan->loan_interest_rate_per],
                                'interest_rate'=>$ongoing_member_loan->interest_rate,
                                'repayment_period'=>$ongoing_member_loan->repayment_period,
                                'loan_from'=>date('M Y',$ongoing_member_loan->disbursement_date),
                                'loan_to'=>date('M Y',$ongoing_member_loan->loan_end_date),
                                'timestamp_from'=>$ongoing_member_loan->disbursement_date,
                                'timestamp_to'=>$ongoing_member_loan->loan_end_date,
                                'amount_paid'=>$total_paid,
                                'is_fully_paid'=>$ongoing_member_loan->is_fully_paid
                            );
                        endforeach;
                        if(!empty($ongoing_loan_amounts_payable)){
                            foreach($ongoing_loan_amounts_payable as $key => $amount_payable):
                                $outstanding_loan+= $amount_payable - $ongoing_loan_amounts_paid[$key];                                
                                $total_amount_paid += $ongoing_loan_amounts_paid[$key];
                            endforeach;
                        }
                        

                        $loan_limit = $this->_get_member_loan_limit($this->group->id,$this->member->id);
                        $loan_details = array(
                            'loan_balance'=>$outstanding_loan,
                            'total_amount_borrowed'=>$total_loan_borrowed,
                            'total_amount_paid'=>$total_amount_paid,                          
                        );
                        $response = array(
                            'status'=>1,
                            'time'=>time(),
                            'loan_details'=>$loan_details,
                            'loans'=>$matured_loans,
                           
                        );                                               
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
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
    function _valid_application_amount(){
        $loan_application_amount = currency($this->input->post('loan_amount'));
        if($this->loan_type->loan_amount_type == 1){//range
            if($loan_application_amount < $this->loan_type->minimum_loan_amount){
                $this->form_validation->set_message('_valid_application_amount','Amount applied is below the allowed minimum amount');
                return FALSE;
            }elseif($loan_application_amount > $this->loan_type->maximum_loan_amount){
                if($this->loan_type->enable_loan_guarantors){
                    return TRUE; //handled in guarantor callback
                }else{
                    $this->form_validation->set_message('_valid_application_amount','Amount applied is above the allowed maximum amount');
                    return FALSE;
                }
            }else{
                if($this->loan_type->enable_loan_guarantors){
                    return TRUE; //handled in guarantor callback
                }else{
                    if($loan_application_amount > $this->loan_type->maximum_loan_amount){
                        $this->form_validation->set_message('_valid_application_amount','Amount applied is above the allowed maximum amount');
                        return FALSE;
                    }else{
                        return TRUE;
                    }
                }
            }
        }else if($this->loan_type->loan_amount_type == 2){//member savings
            $data['contribution_options'] = $this->contributions_m->get_group_savings_contribution_options();
            $member_savings = $this->reports_m->get_group_member_total_contributions($this->member->id,$data['contribution_options']); 
            $maximum_allowed_loan = $member_savings * ($this->loan_type->loan_times_number?$this->loan_type->loan_times_number:1);
            if($this->loan_type->enable_loan_guarantors){
                return TRUE; //handled in guarantor callback
            }else{
                if($loan_application_amount > $maximum_allowed_loan){
                    $this->form_validation->set_message('_valid_application_amount','Loan applied is above '.$this->loan_type->loan_times_number.' times your savings');
                    return FALSE;
                }else{
                    return TRUE;
                }
            }
        }else{
            $this->form_validation->set_message('_valid_application_amount','Invalid loan type selected');
            return FALSE;
        }
    }
    function _valid_repayment_period(){
        //check repayment period here
        if($this->loan_type->loan_repayment_period_type == 1){//fixed
            return TRUE;
        }elseif($this->loan_type->loan_repayment_period_type == 2){
            if($this->input->post('repayment_period') < $this->loan_type->minimum_repayment_period){
                $this->form_validation->set_message('_valid_repayment_period','Loan repayment period is less than the allowed repayment period');
                return FALSE;
            }else if($this->input->post('repayment_period') > $this->loan_type->maximum_repayment_period){
                $this->form_validation->set_message('_valid_repayment_period','Loan repayment period is above than the allowed repayment period');
                return FALSE;
            }else{
                return TRUE;
            }
        }
    }

    function apply_loan(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('id_number');
        if($this->user = $this->users_m->get_user_by_id_number($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
                $this->group_currency = "KES";
                $member_id = $this->user->id_number;
                    $this->member = $this->members_m->get_group_member_by_id_number($member_id);
                if($this->member){ 
                    $loan_type_id = $this->input->post('loan_type_id');
                    if($loan_type_id){
                       $this->loan_type = $this->loan_types_m->get($loan_type_id);
                       if($this->loan_type){                   
                           $this->form_validation->set_rules($this->application_rules);
                           if($this->form_validation->run()){
                                $active_group_member_options = $this->members_m->get_active_group_member_options();
                                $loan_application_amount = $this->input->post('loan_application_amount');
                                if($this->loan_type){
                                    $validation_errors = array();
                                    $guarantor_details_are_valid = TRUE;
                                    if($this->loan_type->enable_loan_guarantors){
                                        $guarantors = $_POST['guarantors'];
                                        $guarantor_ids = array();
                                        $guarantor_count = count($guarantors);
                                        foreach ($guarantors as $key => $guarantor):
                                            $guarantor_ids[] = $guarantor->id;
                                        endforeach;
                                        if(count(array_unique($guarantor_ids)) == $guarantor_count){
                                            $total_guaranteed_amount = 0;
                                            foreach ($guarantors as $key => $guarantor):                                           
                                                if($guarantor->id == $this->member->id){
                                                    $guarantor_details_are_valid = FALSE;
                                                    $message = 'You cannot select yourself as a guarantor';
                                                    $response = array(
                                                        'status' => 0,
                                                        'time'=>time(),
                                                        'message' => $message,
                                                    ); break;
                                                }else{
                                                    $total_guaranteed_amount+= $guarantor->amount;
                                                    $data['contribution_options'] = $this->contributions_m->get_group_refundable_contribution_options();
                                                    $guarantor_savings = $this->reports_m->get_group_member_total_contributions($guarantor->id,$data['contribution_options']);

                                                    $guarantor_savings = $guarantor_savings?$guarantor_savings:0;

                                                    $maximum_amount_to_grant = $guarantor_savings *($this->loan_type->loan_times_number?currency($this->loan_type->loan_times_number):1);
                                                    $guarantor_amount = isset($guarantor->amount)?$guarantor->amount:0;
                                                    if($guarantor_amount){
                                                        if($guarantor_amount > $maximum_amount_to_grant){
                                                            $guarantor_details_are_valid = FALSE;
                                                            $response = array(
                                                                'status' => 0,
                                                                'time'=>time(),
                                                                'message' => $active_group_member_options[$guarantor->id].' can not guarantee that much',
                                                            ); break;
                                                        }
                                                    }else{
                                                        $guarantor_details_are_valid = FALSE;
                                                        $response = array(
                                                            'status' => 0,
                                                            'time'=>time(),
                                                            'message' => 'Please select a valid amount for '.$active_group_member_options[$guarantor->id].' to guarantee',
                                                        ); break;
                                                    }
                                                }
                                            endforeach;

                                            if($guarantor_details_are_valid){
                                                if($this->loan_type->loan_amount_type == 1){//range
                                                    
                                                    if($loan_application_amount > $this->loan_type->maximum_loan_amount){
                                                        if(count($guarantor_ids) < $this->loan_type->minimum_guarantors){
                                                            $guarantor_details_are_valid = FALSE;
                                                            $response = array(
                                                                'status' => 0,
                                                                'time'=>time(),
                                                                'message' => 'You have selected less guarantors than the required',
                                                            );
                                                        }else{
                                                            if($this->loan_type->loan_guarantors_type == 1){//every time 
                                                                $guarantor_details_are_valid = FALSE;
                                                                $response = array(
                                                                    'status' => 0,
                                                                    'time'=>time(),
                                                                    'message' => 'Amount applied is above the required maximum amount',
                                                                );
                                                            }elseif ($this->loan_type->loan_guarantors_type == 2) {//when exceeds savings
                                                                $amount_above_maximum = currency($loan_application_amount - $this->loan_type->maximum_loan_amount);
                                                                if($total_guaranteed_amount < $amount_above_maximum){
                                                                    $guarantor_details_are_valid = FALSE;
                                                                    $response = array(
                                                                        'status' => 0,
                                                                        'time'=>time(),
                                                                        'message' => 'Total guaranteed amount is less than allowed amount to be guaranteed',
                                                                    );
                                                                }
                                                            }
                                                        }
                                                    }else{
                                                        if(count($guarantor_ids) < $this->loan_type->minimum_guarantors){
                                                            $guarantor_details_are_valid = FALSE;
                                                            $response = array(
                                                                'status' => 0,
                                                                'time'=>time(),
                                                                'message' => 'You have selected are less guarantors than the required ',
                                                            );
                                                        }else{
                                                            if($this->loan_type->loan_guarantors_type == 1){//every time
                                                               //carry on
                                                            }elseif ($this->loan_type->loan_guarantors_type == 2) {//when exceeds savings
                                                                $amount_above_maximum = currency($loan_application_amount - $this->loan_type->maximum_loan_amount);
                                                                if($total_guaranteed_amount < $amount_above_maximum){
                                                                    $guarantor_details_are_valid = FALSE;
                                                                    $response = array(
                                                                        'status' => 0,
                                                                        'time'=>time(),
                                                                        'message' => 'Guaranteed amount is less than allowed amount to be guaranteed',
                                                                    );
                                                                }
                                                            }
                                                        }
                                                    }                                                   

                                                }else if($this->loan_type->loan_amount_type == 2){//member savings

                                                    $member_savings = $this->transactions->get_group_member_savings($this->group->id,$this->member->id);
                                                    $maximum_allowed_loan = currency($member_savings * $this->loan_type->loan_times_number);
                                                    $amount_above_savings = currency($loan_application_amount - $maximum_allowed_loan);
                                                    if($this->loan_type->enable_loan_guarantors){
                                                        if($this->loan_type->loan_guarantors_type == 1){//every time
                                                            if(count($guarantor_ids) < $this->loan_type->minimum_guarantors){
                                                                $guarantor_details_are_valid = FALSE;
                                                                $response = array(
                                                                    'status' => 0,
                                                                    'time'=>time(),
                                                                    'message' => 'You have selected guarantors than the minimum required',
                                                                );
                                                            }else{

                                                            }
                                                        }elseif ($this->loan_type->loan_guarantors_type == 2) {//when exceeds savings
                                                            if($loan_application_amount > $maximum_allowed_loan){
                                                                if(count($guarantor_ids) < $this->loan_type->minimum_guarantors){
                                                                    $guarantor_details_are_valid = FALSE;
                                                                    $response = array(
                                                                        'status' => 0,
                                                                        'time'=>time(),
                                                                        'message' => 'Guarantors selected are less than the required',
                                                                    );
                                                                }else{                                                                    
                                                                    if($total_guaranteed_amount < $amount_above_savings){
                                                                        $guarantor_details_are_valid = FALSE;
                                                                        $response = array(
                                                                            'status' => 0,
                                                                            'time'=>time(),
                                                                            'message' => 'Guaranteed amount is less than allowed amount to be guaranteed',
                                                                        );
                                                                    }
                                                                }
                                                            }                      
                                                        }
                                                    }
                                                }else{
                                                    $guarantor_details_are_valid = FALSE;
                                                    $response = array(
                                                        'status' => 0,
                                                        'time'=>time(),
                                                        'message' => 'Invalid loan type selected',
                                                    );
                                                }
                                            }

                                        }else{
                                            $guarantor_details_are_valid = FALSE;
                                            $repeated_guarantor_ids = array_flip(array_diff_key($guarantor_ids,array_unique($guarantor_ids)));
                                            foreach ($repeated_guarantor_ids as $key => $value) {
                                                $message = 'You cannot select '.$active_group_member_options[$key].' as a guarantor more than once';
                                            }                                            
                                            $response = array(
                                                'status' => 0,
                                                'time'=>time(),
                                                'message' => $message,
                                            ); 
                                        } 
                                    }

                                    if($guarantor_details_are_valid){
                                        $loan_application = array(
                                            'member_id'=>$this->member->id,
                                            'group_id'=>$this->group->id,
                                            'loan_type_id'=>$loan_type_id,
                                            'status'=>0,
                                            'is_approved'=>0,
                                            'is_declined'=>0,
                                            'loan_amount'=>currency($this->input->post('loan_application_amount')),
                                            'active'=>1,
                                            'agree_to_rules'=>$this->input->post('loan_rules_check_box'),
                                            'created_on'=>time(),
                                            'created_by'=>$this->user->id
                                        );
                                        if($this->loan_type->loan_repayment_period_type == 1){
                                            $loan_application += array(
                                                'repayment_period'=>$this->loan_type->fixed_repayment_period,
                                            );
                                        }else if($this->loan_type->loan_repayment_period_type == 2){
                                            $loan_application += array(
                                                'repayment_period'=>$this->input->post('repayment_period'),
                                            );  
                                        }
                                        $wallet_account = $this->wallets_m->get_wallet_account();
                                        if($wallet_account){
                                            $loan_application += array(
                                                'account_id' => 'bank-'.$wallet_account->id,
                                            );
                                        }                                        
                                        $loan_application_id = $this->loan_applications_m->insert($loan_application);
                                        $loan_application += array('id' => $loan_application_id);                                        
                                        if($this->loan_type->enable_loan_guarantors == 1){
                                            $guarantor_details = array();
                                            foreach ($guarantors as $key => $guarantor):
                                                $guarantor_details[$guarantor->id] = array(
                                                    'amount' => $guarantor->amount,
                                                    'comment' => '',
                                                );
                                            endforeach;
                                            if($this->loan->create_guarantors_loan_application_approval_requests($loan_application,$this->loan_type,$guarantor_details)){
                                                $this->loan->toggle_loan_application_status($loan_application,$this->loan_type);
                                                $response = array(
                                                    'status' => 1,
                                                    'time'=>time(),
                                                    'message' => 'Loan application submitted. An approval request has been sent to all your guarantors, kindly await their responses to this loan application',
                                                );
                                            }else{
                                                $response = array(
                                                    'status' => 0,
                                                    'time'=>time(),
                                                    'message' => $this->session->warning,
                                                );
                                            }
                                        }else{
                                            if($this->loan->create_group_signatories_loan_application_approval_requests($loan_application,$this->loan_type)){
                                                $this->loan->toggle_loan_application_status($loan_application,$this->loan_type);
                                                $response = array(
                                                    'status' => 1,
                                                    'time'=>time(),
                                                    'message' => 'Loan application submitted. An approval request has been sent to all your group signatories, kindly await their responses to this loan application',
                                                );
                                            }else{
                                                $response = array(
                                                    'status' => 0,
                                                    'time'=>time(),
                                                    'message' => $this->session->warning,
                                                );
                                            }
                                        }
                                    }else{
                                        //do nothing for now
                                    }
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'time'=>time(),
                                        'message' => 'Loan type details missing',
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
                                    'time' => time(),
                                    'message' => 'Form validation failed',
                                    'validation_errors' => $post,
                                );
                           }
                       }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not find Loan type details',
                                'time' => time(),
                            );
                       }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Loan type id is required',
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
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
    }
    function create_loan(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
       
        $post = new StdClass();
       
        $response = array();
        $this->_additional_validation_rules_mobile();
        $loan_type_id = $this->input->post('loan_type_id');
        $this->loan_type = $this->loan_types_m->get($loan_type_id);
       

        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){ 
            $loan_details = new StdClass();
            $custom_loan_values = array();
            
            if(!$this->user=$this->users_m->get_user_by_id_number($this->input->post('id_number'))){
                $response = array(
                    'status' => 0,
                    'message' => 'We could not get the User with this Id Number in our records',
                );
                echo json_encode($response);
                die;
            }
            $member=$this->members_m->get_applicant_by_id_number($this->input->post('id_number'));
             if(!$member){
                $response = array(
                    'status' => 0,
                    'message' => 'Applicant details not found. Please contact support',
                );
                echo json_encode($response);
                die;
             }
            $member_id = $member->id;
            $loan_amount = currency($this->input->post('loan_amount'));
            $accounts=$this->accounts_m->get_active_group_account_options('','','','',FALSE);
            $account_id='';
            foreach($accounts as $key=>$value){
                if(preg_match('/mobile/',$key)){
                    $account_id=$key;
                    break;
                }
            }
            $disbursement_date = time();
            $disbursement_option_id =1;
            $mobile_money_wallet_id = "member-".$member_id;
            $equity_bank_account_id = ($this->input->post('equity_bank_account_id'))??'';
            $repayment_period = $this->input->post('repayment_period');
            $guarantor_id = $this->input->post('guarantor_ids');
            $guaranteed_amount = $this->input->post('guaranteed_amounts');
            $guarantor_comment = $this->input->post('guarantor_comments');
            $guarantors= array();
            foreach ($guarantor_id as $key => $value) {
                if($value){
                    $guarantors['guarantor_id'][] = $value;
                    $guarantors['guaranteed_amount'][] = $guaranteed_amount[$key];
                    $guarantors['guarantor_comment'][] = $guarantor_comment[$key];
                }
            }
            $loan_type = $this->loan_types_m->get_group_loan_type($loan_type_id);
            if($loan_type){
                $loan_details->loan_type_id = $loan_type_id;
                $loan_details->disbursement_date = $disbursement_date;
                $loan_details->account_id = $account_id;
                $loan_details->loan_amount = $loan_amount;
                $loan_details->created_by = $member->user_id;
                $loan_details->created_on = time();
                if($loan_type->loan_repayment_period_type == 1){
                    $loan_details->repayment_period = $loan_type->fixed_repayment_period;
                }else{
                    $loan_details->repayment_period = $repayment_period;
                }
                $fields = $this->loans_m->get_table_fields();
                foreach ($loan_type as $key => $value) {
                    if(!isset($loan_details->$key)){
                        if(in_array($key, $fields) && $key!='id'){
                            $loan_details->$key = $value;
                        }
                    }
                }
                $verified_bank_accounts = $this->bank_accounts_m->get_group_verified_partner_bank_account_options_ids($this->group->id);
                if(preg_match('/bank-/', $account_id) && array_key_exists(trim(preg_replace('/[^0-9]/', '', $account_id)), $verified_bank_accounts)){
                    //withdrawal request
                    $bank_account_id = trim(preg_replace('/[^0-9]/','', $account_id));
                    $withdrawal = new StdClass();
                    $withdrawal->withdrawal_for = 1;
                    $withdrawal->amount = $loan_amount;
                    $withdrawal->bank_account_id = $bank_account_id;
                    $withdrawal->transfer_to = $disbursement_option_id==1?1:3;
                    $withdrawal->recipient = $disbursement_option_id==1?$mobile_money_wallet_id:$equity_bank_account_id;
                    $withdrawal->loan_type_id = $loan_type_id;
                    $withdrawal->member_id = $member_id;
                    $withdrawal->disbursement_channel = $disbursement_option_id;
                    $bank_account = $this->bank_accounts_m->get_group_verified_bank_account_by_id($bank_account_id,$this->group->id);
                    if($bank_account){
                        if(floatval($bank_account->current_balance) >= floatval($withdrawal->amount)){
                            if($this->transactions->process_batch_withdrawal_requests($withdrawal,$this->group_currency,$this->member,$this->group,$this->user)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Successfully processed withdrawal request(s)',
                                    'refer' => site_url('bank/withdrawals/withdrawal_requests'),
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
                                'message' => translate('You can not disburse more than is in the selected account. Available balance is ').$this->group_currency.' '.number_to_currency($bank_account->current_balance),
                            );
                        }
                    }else{
                         $response = array(
                            'status' => 0,
                            'message' => translate('You must select a valid disbursing bank account that is connected'),
                        );
                    }
                }else{
                    if($id = $this->loan->create_automated_group_loan(1,$member_id,$group_id,(array)$loan_details,'','',$guarantors)){
                        $withdrawal = new StdClass();
                        $withdrawal->withdrawal_for = 1;
                        $withdrawal->amount = $loan_amount;
                        $withdrawal->bank_account_id = $bank_account_id;
                        $withdrawal->transfer_to = $disbursement_option_id==1?1:3;
                        $withdrawal->recipient = $disbursement_option_id==1?$mobile_money_wallet_id:$equity_bank_account_id;
                        $withdrawal->loan_type_id = $loan_type_id;
                        $withdrawal->member_id = $member_id;
                        $withdrawal->disbursement_channel = $disbursement_option_id;
                        $this->member=$this->members_m->get($member_id);
                        if($this->transactions->process_batch_withdrawal_requests($withdrawal,"KES",$this->member,$this->group,$this->user)){
                            $response = array(
                                'status' => 1,
                                'message' => 'Successfully processed withdrawal request(s)',
                                'refer' => site_url('bank/withdrawals/withdrawal_requests'),
                            );
                        }
                    $response = array(
                        'status' => 1,
                        'message' => 'Loan Successfully created',
                    );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => $this->session->flashdata('error')
                        );
                    }
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'We could not get the loan type you trying to create',
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
    function get_member_loan_application_status(){        
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
                    if($this->member->active){
                        $loan_application_id = $this->input->post('loan_application_id');
                        if($this->loan_application = $this->loan_applications_m->get($loan_application_id)){
                            $guarantor_array = array();
                            $signatory_array = array();
                            $loan_type = $this->loan_types_m->get_options();
                            $guarantorship_requests = $this->loans_m->get_loan_application_guarantorship_requests($this->loan_application->id);
                            $signatory_requests = $this->loans_m->get_loan_application_signatory_requests($this->loan_application->id);
                            if($guarantorship_requests){                                
                                foreach($guarantorship_requests as $key => $guarantorship_request):
                                    $guarantor_array[] = array(
                                        'id'=>$guarantorship_request->id,
                                        'first_name'=>$guarantorship_request->first_name,
                                        'last_name'=>$guarantorship_request->last_name,
                                        //'member_id'=>$guarantorship_request->signatory_member_id,
                                        'is_approved'=>$guarantorship_request->is_approved,
                                        'is_declined'=>$guarantorship_request->is_declined,
                                    );
                                endforeach;
                            }
                            if($signatory_requests){
                                foreach($signatory_requests as $key => $signatory_request):
                                    $signatory_array[] = array(
                                        'id'=>$signatory_request->id,
                                        'first_name'=>$signatory_request->signatory_first_name,
                                        'last_name'=>$signatory_request->signatory_last_name,
                                        'member_id'=>$signatory_request->signatory_member_id,
                                        'is_approved'=>$signatory_request->is_approved,
                                        'is_declined'=>$signatory_request->is_declined,
                                    );
                                endforeach;
                            }
                            $loan_details = array(
                                'id'=>$this->loan_application->id,
                                'name'=>$loan_type[$this->loan_application->loan_type_id],
                                'loan_amount'=>$this->loan_application->loan_amount,
                                'application_date'=>timestamp_to_datepicker($this->loan_application->created_on),
                            );
                            $response = array(
                                'status'=>1,
                                'time'=>time(),
                                'loan_details'=>$loan_details,
                                'guarantor_array'=>$guarantor_array,
                                'signatory_array'=>$signatory_array
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not find loan application details',
                                'time' => time(),
                            );
                        }                                                                                      
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
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
    function get_member_loan_calculator(){
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
                    if($this->member->active){
                        $loan_type_id = $this->input->post('loan_type_id');
                        if($this->loan_type = $this->loan_types_m->get($loan_type_id)){
                            $loan_amount =  currency($this->input->post('loan_amount'));
                            if($loan_amount){
                                $repayment_period = $this->input->post('repayment_period')?$this->input->post('repayment_period'):$this->loan_type->fixed_repayment_period;
                                if($repayment_period){
                                    $loan_values = array();
                                    $total_amount_payable = 0;
                                    $total_principle_amount = 0;
                                    $total_interest = 0;
                                    $monthly_payment = 0;
                                    $total_payable =0;
                                    $loan_values = $this->loan->calculate_loan_balance_invoice($loan_amount,$this->loan_type->interest_type,
                                        $this->loan_type->interest_rate,
                                        $repayment_period,
                                        '',time(),
                                        $this->loan_type->loan_interest_rate_per);
                                    foreach ($loan_values as $key => $value):
                                        $value = (object)$value;
                                        $total_amount_payable +=$value->amount_payable;
                                        $total_principle_amount+=$value->principle_amount_payable;
                                        $total_interest+=$value->interest_amount_payable;
                                        $monthly_payment=$value->amount_payable;
                                    endforeach;
                                    $total_principle=0;
                                    $balance=$total_amount_payable;
                                    $total_interest=0;
                                    $breakdown = array();
                                    foreach ($loan_values as $key => $loan_value):
                                        $total_payable+=$value->amount_payable;
                                        $loan_value = (object)$loan_value;
                                        $total_interest += $loan_value->interest_amount_payable;
                                        $balance_amounts[] = $balance - $total_payable;
                                        $breakdown[] = array(
                                            'due_date'=>timestamp_to_datepicker($loan_value->due_date),
                                            'amount_payable'=>$loan_value->amount_payable,
                                            'principle_payable'=>$loan_value->principle_amount_payable,
                                            'interest_payable'=>$loan_value->interest_amount_payable,
                                            'total_intrest_payable'=>$loan_value->interest_amount_payable,
                                            'balance' =>$balance - $total_payable,
                                        );
                                    endforeach;
                                    $amortization_totals = array(
                                        'total_payable'=>$total_amount_payable,
                                        'total_principle'=>$total_principle_amount,
                                        'total_interest'=>$total_interest,                                        
                                    );
                                    $response = array(
                                        'status'=>1,
                                        'time'=>time(),
                                        'amortization_totals'=>$amortization_totals,
                                        'breakdown'=>$breakdown,
                                    ); 
                                }else{
                                    $response = array(
                                    'status' => 0,
                                        'message' => 'Loan repayment period is required',
                                        'time' => time(),
                                    );
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Loan amount is required',
                                    'time' => time(),
                                );   
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not proceed. Loan type details missing',
                                'time' => time(),
                            );
                        }                                                              
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
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
    function get_member_loan_statement(){
        foreach ($this->request as $key => $value) {
             if(preg_match('/phone/', $key)){
                 $_POST[$key] = valid_phone($value);
             }else{
                 $_POST[$key] = $value;
             }
         }
         $user_id = $this->input->post('id_number');
         if($this->user = $this->users_m->get_user_by_id_number($user_id)){
             $this->ion_auth->update_last_login($this->user->id);
                 if($this->member = $this->members_m->get_group_member_by_user_id('',$this->user->id)){
                     if($this->member->active){
                         $loan_id = $this->input->post('loan_id');
                         if($loan_id){
                             $loan = $this->loans_m->get($loan_id);
                             if($loan){
                                 $total_installment_payable = $this->loan_invoices_m->get_total_installment_loan_payable($loan_id);
                                 $total_fines = $this->loan_invoices_m->get_total_loan_fines_payable($loan_id);
                                 $total_transfers_out = $this->loan_invoices_m->get_total_loan_transfers_out($loan_id);
                                 $total_paid = $this->loan_repayments_m->get_loan_total_payments($loan_id);
                                 $loan_balance =$this->loans_m->get_loan_balance($loan_id);
                                 $posts = $this->loans_m->get_loan_statement($loan_id);
                                 $loan_types = $this->loan_types_m->get_options();
                                 //if($posts){
                                     $loan_details = array( 
                                         'loan_id'=>$loan->id,
                                         'total_loan_amount_payable'=>$total_installment_payable,
                                         'loan_amount'=>$loan->loan_amount,
                                         'loan_name'=>$loan_types[$loan->loan_type_id],
                                         'loan_balance'=>$loan_balance,
                                         'interest_rate'=>$loan->interest_rate,
                                         'interest_type'=>$loan->interest_type,
                                         'interest_type_narration'=>$this->loan->interest_types[$loan->interest_type],
                                         'interest_rate_narration'=>$this->loan->loan_interest_rate_per[$loan->loan_interest_rate_per],
                                         'repayment_period'=>$loan->repayment_period,
                                         'loan_from'=>date('M Y',$loan->disbursement_date),
                                         'loan_to'=>date('M Y',$loan->loan_end_date),
                                         'timestamp_from'=>$loan->disbursement_date,
                                         'timestamp_to'=>$loan->loan_end_date,
                                         'amount_paid'=>$total_paid,
                                         'is_fully_paid'=>$loan->is_fully_paid
                                     );
                                     $amount = 0;
                                     $total_amount=0;
                                     $remaining_payable=0; 
                                     $payable =$total_installment_payable+$total_fines+$total_transfers_out;
                                     $statement_body[] = array(
                                         "description" => 'Total Amount Payable',
                                         'date'         =>   '',
                                         'amount_paid'   =>  0,
                                         'balance' => $balance = ($payable-$total_transfers_out),
                                     );
                                     if($posts){
                                         foreach($posts as $post): 
                                             if($post->transaction_type!=5){
                                                 $total_amount+=$post->amount;
                                             }
                                             if($post->transaction_type==5){
                                                 $description = 'Contibution Transfer';
                                             }else{
                                                 $description = 'Payment';
                                             }
                                             $date = timestamp_to_datepicker($post->transaction_date);
                                                 
                                             if($post->transaction_type==5){
                                                 $amount_paid = $post->amount?:0;
                                             }else{
                                                 $amount = $post->amount?:0;
                                                 $amount_paid = $amount;
                                             }
                                             if($post->transaction_type==5){
 
                                             }else{
                                                 $balance = $payable-$total_amount;
                                             }
 
                                             $statement_body[] = array(
                                                 "description" => $description,
                                                 'date' => $date,
                                                 "amount_paid" => $amount_paid,
                                                 "balance" => $balance,
                                             );
                                         endforeach;
                                     }
                                     $loan_interest_rate_per = $this->loan->loan_interest_rate_per;
                                     $interest_types = $this->loan->interest_types;
                                     $interest_rate = $loan->interest_rate.'% '.($loan->loan_interest_rate_per?$loan_interest_rate_per[$loan->loan_interest_rate_per]:$loan_interest_rate_per[4]).' '.$interest_types[$loan->interest_type];
                                     $lump_sum_remaining = $this->loan_invoices_m->get_loan_lump_sum_as_date($loan_id);
                                     $response = array(
                                         "status" => 1,
                                         "message" => "Successful",
                                         "time" => time(),
                                         "data" => array(
                                             'is_fully_paid' => $loan->is_fully_paid?1:0,
                                             "lump_sum_remaining" => $lump_sum_remaining,
                                             "loan_details" => $loan_details,
                                             "statement_body" => $statement_body,
                                             "statement_footer" => array(
                                                 "type" => 'Total',
                                                 "paid" => ($total_paid-$total_transfers_out),
                                                 "balance" =>($payable-$total_paid),
                                             ),
                                             
                                         ),
                                     );
                                 /*}else{
                                     $response = array(
                                         'status' => 0,
                                         'message' => 'Could not find loan details',
                                         'time' => time(),
                                     );
                                 }*/
                             }else{
                                 $response = array(
                                     'status' => 0,
                                     'message' => 'Could not find loan details',
                                     'time' => time(),
                                 );   
                             }  
                         }else{
                             $response = array(
                                 'status' => 0,
                                 'message' => 'Could not proceed. Loan id is required',
                                 'time' => time(),
                             );  
                         }                                    
                     }else{
                         $response = array(
                             'status' => 0,
                             'message' => 'Could not proceed. Account is suspended',
                             'time' => time(),
                         );
                     }
                 }else{
                     $response = array(
                         'status' => 0,
                         'message' => 'Could not find member details',
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

    function get_member_loan_repayment_list(){        
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
                    if($this->member->active){
                        $loan_id = $this->input->post('loan_id');
                        if($this->loan = $this->loans_m->get($loan_id)){
                            $loan_types = $this->loan_types_m->get_options();
                            $total_paid = $this->loan_repayments_m->get_loan_total_payments($this->loan->id);
                            $total_installment_payable = $this->loan_invoices_m->get_total_installment_loan_payable($this->loan->id);
                            $loan_statments = $this->loans_m->get_loan_statement($this->loan->id);
                            $percentage_paid = 0;
                            $percentage_paid = ($total_paid/$total_installment_payable) * 100;
                            $loan_balance = $total_installment_payable - $total_paid;
                            $loan_details = array(
                                'loan_id'=>$this->loan->id,
                                'total_loan_amount_payable'=>$total_installment_payable,
                                'loan_amount'=>$this->loan->loan_amount,
                                'loan_balance'=>$loan_balance,
                                'loan_name'=>$loan_types[$this->loan->loan_type_id],
                                'percentage_paid'=>$percentage_paid,
                                'amount_paid'=>$total_paid,
                                'amount_borrowed'=>$this->loan->loan_amount,
                                'repayment_period'=>$this->loan->repayment_period,
                                'disbursement_date'=>timestamp_to_mobile_shorttime($this->loan->disbursement_date),
                                'loan_end_date'=>timestamp_to_mobile_shorttime($this->loan->loan_end_date),
                                'timestamp_from'=>$this->loan->disbursement_date,
                                'timestamp_to'=>$this->loan->loan_end_date,                                
                                'intrest_rate'=>$this->loan->interest_rate,
                            );
                            $account_options =$this->accounts_m->get_group_account_options(FALSE);
                            $deposit_options =$this->transactions->deposit_method_options;
                            $payment_details = array();
                            if($loan_statments){
                                foreach ($loan_statments as $key => $loan_statment):
                                    if($loan_statment->transaction_type==5){
                                        $type = 'Contibution Transfer';
                                    }else{
                                        $type = 'Payment';
                                    }
                                    $date = timestamp_to_mobile_shorttime($loan_statment->transaction_date);
                                    $amount = $loan_statment->amount;
                                    $balance_after_payment = $total_installment_payable - $loan_statment->amount ;
                                    $description = $deposit_options[$loan_statment->payment_method].' payment to '.$account_options[$loan_statment->account_id];
                                    $payment_details[] = array(
                                        'type'=>$type,
                                        'date'=>$date,
                                        'amount'=>$amount,
                                        'description'=>$description,
                                        'amount_payable'=>$total_installment_payable,
                                        'balance'=>$balance_after_payment
                                    );

                                endforeach;
                            }
                            $response = array(
                                'status'=>1,
                                'time'=>time(),
                                'loan_details'=>$loan_details,
                                'payment_details'=>$payment_details,
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not get loan details',
                                'time' => time(),
                            );
                        }                                                             
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
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