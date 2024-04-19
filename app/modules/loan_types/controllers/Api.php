<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends Mobile_Controller{

  function __construct(){
        parent::__construct();
        $this->load->model('loan_types_m');
        $this->load->library('loan');
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
            ))
        );
    }

    protected $validation_rules = array(
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => 'required|xss_clean|trim',
        ),
        array(
            'field' => 'loan_amount_type',
            'label' => 'Loan amount type',
            'rules' => 'xss_clean|trim|required|numeric',
        ),
        array(
            'field' => 'minimum_loan_amount',
            'label' => 'Minimum Amount',
            'rules' => 'xss_clean|trim|currency',
        ),
        array(
            'field' => 'maximum_loan_amount',
            'label' => 'Maximum Amount',
            'rules' => 'xss_clean|trim|currency',
        ),
        array(
            'field' => 'loan_amount_type',
            'label' => 'Loan amount type',
            'rules' => 'xss_clean|trim|required|numeric',
        ),
        array(
            'field' => 'savings_times',
            'label' => 'Times a member savings',
            'rules' => 'xss_clean|trim|numeric',
        ),
        array(
            'field' => 'interest_type',
            'label' => 'Loan Interest Type',
            'rules' => 'required|xss_clean|trim|currency'
        ),
        array(
            'field' => 'interest_rate',
            'label' => 'Loan Interest Rate',
            'rules' => 'required|xss_clean|trim|currency'
        ),
        array(
            'field' => 'interest_rate_per',
            'label' => 'Loan Interest Rate Per',
            'rules' => 'required|xss_clean|trim|currency'
        ),
        array(
            'field'=>'repayment_period_type',
            'label'=>'Loan Repayment Period Type',
            'rules'=>'required|xss_clean|trim',
        ),
        array(
            'field' => 'minimum_repayment_period',
            'label' => 'Minimum Repayment Period',
            'rules' => 'xss_clean|trim|numeric'
        ),
        array(
            'field' => 'maximum_repayment_period',
            'label' => 'Maximum Repayment Period',
            'rules' => 'xss_clean|trim|numeric'
        ),
        array(
            'field' => 'fixed_repayment_period',
            'label' => 'Fixed Repayment Period',
            'rules' => 'xss_clean|trim|numeric'
        ),
    );

    protected $fines_validation_rules = array(
        array(
            'field' => 'enable_loan_fines',
            'label' => 'Enable Loan Fines',
            'rules' => 'numeric|xss_clean|trim',
        ),
        array(
            'field' => 'enable_outstanding_loan_balance_fines',
            'label' => 'Enable Outstanding Loan Balance Fines',
            'rules' => 'xss_clean|trim|numeric',
        ),
    );

    protected $general_validation_rules = array(
        array(
            'field' => 'enable_loan_processing_fee',
            'label' => 'Enable Loan Processing Fee',
            'rules' => 'numeric|xss_clean|trim',
        ),
        array(
            'field' => 'enable_loan_guarantors',
            'label' => 'Enable Loan Guarantors',
            'rules' => 'xss_clean|trim|numeric',
        ),
    );

    function _additional_rules(){
        $loan_amount_type = $this->input->post('loan_amount_type');
        if($loan_amount_type == 1){
            $this->validation_rules[] = array(
                'field' =>  'minimum_loan_amount',
                'label' =>  'Minimum Loan Amount',
                'rules' =>  'required|xss_clean|trim|currency|callback__is_greater_than_minimum_loan_amount'
            );
            $this->validation_rules[] = array(
                'field' =>  'maximum_loan_amount',
                'label' =>  'Maximum Loan Amount',
                'rules' =>  'required|xss_clean|trim|currency'
            );
        }elseif ($loan_amount_type == 2) {
            $this->validation_rules[] = array(
                'field' =>  'savings_times',
                'label' =>  'Member Savings',
                'rules' =>  'required|xss_clean|trim'
            );
        }
        $repayment_period_type = $this->input->post('repayment_period_type');
        if($repayment_period_type == 1){
            $this->validation_rules[] = array(
                'field' =>  'fixed_repayment_period',
                'label' =>  'Fixed repayment period',
                'rules' =>  'required|xss_clean|trim|numeric'
            );
        }elseif($repayment_period_type == 2){
            $this->validation_rules[] = array(
                'field' =>  'minimum_repayment_period',
                'label' =>  'Minimum repayment period',
                'rules' =>  'required|xss_clean|trim|numeric'
            );
            $this->validation_rules[] = array(
                'field' =>  'maximum_repayment_period',
                'label' =>  'Maximum Repayment Period',
                'rules' =>  'required|xss_clean|trim|numeric|callback__is_greater_than_minimum_repayment_period'
            );
        }
    }

    function _additional_fine_rules(){
        $enable_loan_fines = $this->input->post('enable_loan_fines');
        if($enable_loan_fines == 1){
            $this->fines_validation_rules[] = array(
                'field' => 'loan_fine_type',
                'label' => 'Loan Fine Type',
                'rules' => 'numeric|xss_clean|trim|required',
            );

            if($this->input->post('loan_fine_type')==1){
                $this->fines_validation_rules[] = array(
                        'field' =>  'fixed_fine_amount',
                        'label' =>  'Fixed Fine Amount',
                        'rules' =>  'required|xss_clean|trim|currency'
                    );
                $this->fines_validation_rules[] = array(
                        'field' =>  'fixed_amount_fine_frequency',
                        'label' =>  'Fixed Amount Fine Frequency',
                        'rules' =>  'required|xss_clean|trim'
                    );
                $this->fines_validation_rules[] = array(
                        'field' =>  'fixed_amount_fine_frequency_on',
                        'label' =>  'Fixed Amount Fine Frequency On',
                        'rules' =>  'required|xss_clean|trim'
                    );
            }
            if($this->input->post('loan_fine_type')==2){
                $this->fines_validation_rules[] = array(
                    'field' =>  'percentage_fine_rate',
                    'label' =>  'Percentage(%) Fine Rate',
                    'rules' =>  'required|xss_clean|trim|numeric'
                );
                $this->fines_validation_rules[] = array(
                    'field' =>  'percentage_fine_frequency',
                    'label' =>  'Percentage Fine Frequency',
                    'rules' =>  'required|xss_clean|trim'
                );
                $this->fines_validation_rules[] = array(
                    'field' =>  'percentage_fine_on',
                    'label' =>  'Percentage Fine Frequency on',
                    'rules' =>  'required|xss_clean|trim'
                );
            }

            if($this->input->post('loan_fine_type')==3){
                $this->fines_validation_rules[] = array(
                    'field' =>  'one_off_fine_type',
                    'label' =>  'One off Fine Type',
                    'rules' =>  'required|xss_clean|trim'
                );
                if($this->input->post('one_off_fine_type')==1){
                    $this->fines_validation_rules[] = array(
                        'field' =>  'one_off_fixed_amount',
                        'label' =>  'One off Fine Fixed Amount',
                        'rules' =>  'required|xss_clean|trim|currency'
                    );
                }
                if($this->input->post('one_off_fine_type')==2){
                    $this->fines_validation_rules[] = array(
                        'field' =>  'one_off_percentage_rate',
                        'label' =>  'One off Percentage (%) Rate',
                        'rules' =>  'required|xss_clean|trim|numeric'
                    );
                    $this->fines_validation_rules[] = array(
                        'field' =>  'one_off_percentage_rate_on',
                        'label' =>  'One off Percentage Rate On',
                        'rules' =>  'required|xss_clean|trim'
                    );
                }
            }
        }
        
        $enable_outstanding_loan_balance_fines = $this->input->post('enable_outstanding_loan_balance_fines');
        if($enable_outstanding_loan_balance_fines == 1){
            $this->fines_validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fine_type',
                    'label' =>  'Outstanding Loan FIne Types',
                    'rules' =>  'required|xss_clean|trim'
                );
            if($this->input->post('outstanding_loan_balance_fine_type')==1){
                $this->fines_validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fine_fixed_amount',
                    'label' =>  'Outstanding Loan Balance FIne Fixed Amount',
                    'rules' =>  'required|xss_clean|trim|currency'
                );
                $this->fines_validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fixed_fine_frequency',
                    'label' =>  'Outstanding Loan Balance FIne Fixed Frequency Rate',
                    'rules' =>  'required|xss_clean|trim'
                );
            }
            if($this->input->post('outstanding_loan_balance_fine_type')==2){
                $this->fines_validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_percentage_fine_rate',
                    'label' =>  'Outstanding Loan Balance Percentage Rate',
                    'rules' =>  'required|xss_clean|trim|numeric'
                );
                $this->fines_validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_percentage_fine_frequency',
                    'label' =>  'Outstanding Loan Balance Percentage Fine Frequency',
                    'rules' =>  'required|xss_clean|trim'
                );
                $this->fines_validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_percentage_fine_on',
                    'label' =>  'Outstanding Loan Balance Percentage Rate on',
                    'rules' =>  'required|xss_clean|trim'
                );
            }
            if($this->input->post('outstanding_loan_balance_fine_type')==3){
                $this->fines_validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fine_one_off_amount',
                    'label' =>  'Outstanding Loan Balance One Off Amount',
                    'rules' =>  'required|xss_clean|trim|currency'
                );
            }
        }
    }

    function _additional_general_details_rules(){
        if($this->input->post('enable_loan_processing_fee')){
            $this->general_validation_rules[] = array(
                    'field' =>  'loan_processing_fee_type',
                    'label' =>  'Loan Processing Fee Type',
                    'rules' =>  'xss_clean|trim|required'
                );
            if($this->input->post('loan_processing_fee_type')==1){
                $this->general_validation_rules[] = array(
                    'field' =>  'loan_processing_fee_fixed_amount',
                    'label' =>  'Loan Processing Fee Fixed Amount',
                    'rules' =>  'xss_clean|trim|currency|required'
                );
            }else{
                $this->general_validation_rules[] = array(
                        'field' =>  'loan_processing_fee_percentage_rate',
                        'label' =>  'Loan Processing Fee Fixed Percentage Rate',
                        'rules' =>  'xss_clean|trim|required|currency'
                    );
                $this->general_validation_rules[] = array(
                        'field' =>  'loan_processing_fee_percentage_charged_on',
                        'label' =>  'Loan Processing Fee Fixed Percentage Charged On',
                        'rules' =>  'xss_clean|trim|required|numeric'
                    );
            }
        }
        if($this->input->post('enable_loan_guarantors')){
            if($this->input->post('loan_guarantors_type') == 1){    
                $this->general_validation_rules[] = array(
                    'field' => 'minimum_guarantors',
                    'label' => 'Maximum Guarantors',
                    'rules' => 'required|xss_clean|trim|numeric|greater_than[0]'
                );
            }elseif($this->input->post('loan_guarantors_type') == 2){
                $this->general_validation_rules[] = array(
                    'field' => 'minimum_guarantors',
                    'label' => 'Minimum Guarantors',
                    'rules' => 'required|xss_clean|trim|numeric|greater_than[0]'
                );
            }else{
                $this->general_validation_rules[] = array(
                    'field' => 'loan_guarantors_type',
                    'label' => 'Loan guarantors type',
                    'rules' => 'xss_clean|trim|currency|required',
                );
            }
        }
    }

    function _is_greater_than_minimum_loan_amount(){
        if(currency($this->input->post('minimum_loan_amount'))>currency($this->input->post('maximum_loan_amount'))){
            $this->form_validation->set_message('_is_greater_than_minimum_loan_amount','Maximum Loan Amount must be greater than Minimum Loan Amount');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    function _is_greater_than_minimum_repayment_period(){
        if(currency($this->input->post('minimum_repayment_period'))>currency($this->input->post('maximum_repayment_period'))){
            $this->form_validation->set_message('_is_greater_than_minimum_repayment_period','Maximum Repayment Period must be greater than Minimum Repayment Period');
            return FALSE;
        }else{
            return TRUE;
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
                    $this->_additional_rules();
                    $this->form_validation->set_rules($this->validation_rules);
                    if($this->form_validation->run()){
                        $name = $this->input->post('name');
                        $loan_amount_type = $this->input->post('loan_amount_type');
                        $minimum_loan_amount = currency($this->input->post('minimum_loan_amount'));
                        $maximum_loan_amount = currency($this->input->post('maximum_loan_amount'));
                        $savings_times = $this->input->post('savings_times');
                        $interest_type = $this->input->post('interest_type');
                        $interest_rate = $this->input->post('interest_rate');
                        $interest_rate_per = $this->input->post('interest_rate_per');
                        $repayment_period_type = $this->input->post('repayment_period_type');
                        $fixed_repayment_period = $this->input->post('fixed_repayment_period');
                        $minimum_repayment_period = $this->input->post('minimum_repayment_period');
                        $maximum_repayment_period = $this->input->post('maximum_repayment_period');
                        $input = array(
                            'name' => strtoupper($name),
                            'loan_amount_type' => $loan_amount_type,
                            'minimum_loan_amount' => $minimum_loan_amount,
                            'maximum_loan_amount' => $maximum_loan_amount,
                            'loan_times_number' => $savings_times,
                            'interest_type' => $interest_type,
                            'interest_rate' => $interest_rate,
                            'loan_interest_rate_per' => $interest_rate_per,
                            'loan_repayment_period_type' => $repayment_period_type,
                            'enable_reducing_balance_installment_recalculation' => $this->input->post('enable_reducing_balance_installment_recalculation'),
                            'fixed_repayment_period' => $fixed_repayment_period,
                            'minimum_repayment_period' => $minimum_repayment_period,
                            'maximum_repayment_period' => $maximum_repayment_period,
                            'group_id' => $this->group->id,
                            'active' => 1,
                            'grace_period' => $this->input->post('grace_period')?:1,
                            'enable_loan_fines' => $this->input->post('enable_loan_fines')?:0,
                            'enable_outstanding_loan_balance_fines' => $this->input->post('enable_outstanding_loan_balance_fines')?:0,
                            'enable_loan_processing_fee' => $this->input->post('enable_loan_processing_fee')?:0,
                            'enable_loan_fine_deferment' => $this->input->post('enable_loan_fine_deferment')?:0,
                            'enable_loan_guarantors' => $this->input->post('enable_loan_guarantors')?:0,
                            'created_by' => $this->user->id,
                            'created_on' => time(),
                        );
                        if($id = $this->loan_types_m->insert($input)){
                            $response = array(
                                'status' => 1,
                                'message' => 'Loan type successfully added',
                                'id' => $id,
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not add loan type',
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
                    $id = $this->input->post('id');
                    if($post = $this->loan_types_m->get_group_loan_type($id)){
                        $this->_additional_rules();
                        $this->form_validation->set_rules($this->validation_rules);
                        if($this->form_validation->run()){
                            $name = $this->input->post('name');
                            $loan_amount_type = $this->input->post('loan_amount_type');
                            $minimum_loan_amount = currency($this->input->post('minimum_loan_amount'));
                            $maximum_loan_amount = currency($this->input->post('maximum_loan_amount'));
                            $savings_times = $this->input->post('savings_times');
                            $interest_type = $this->input->post('interest_type');
                            $interest_rate = $this->input->post('interest_rate');
                            $interest_rate_per = $this->input->post('interest_rate_per');
                            $repayment_period_type = $this->input->post('repayment_period_type');
                            $fixed_repayment_period = $this->input->post('fixed_repayment_period');
                            $minimum_repayment_period = $this->input->post('minimum_repayment_period');
                            $maximum_repayment_period = $this->input->post('maximum_repayment_period');
                            $update = array(
                                'name' => strtoupper($name),
                                'loan_amount_type' => $loan_amount_type,
                                'minimum_loan_amount' => $minimum_loan_amount,
                                'maximum_loan_amount' => $maximum_loan_amount,
                                'loan_times_number' => $savings_times,
                                'interest_type' => $interest_type,
                                'interest_rate' => $interest_rate,
                                'loan_interest_rate_per' => $interest_rate_per,
                                'loan_repayment_period_type' => $repayment_period_type,
                                'fixed_repayment_period' => $fixed_repayment_period,
                                'enable_reducing_balance_installment_recalculation' => $this->input->post('enable_reducing_balance_installment_recalculation'),
                                'minimum_repayment_period' => $minimum_repayment_period,
                                'maximum_repayment_period' => $maximum_repayment_period,
                                'grace_period' => $this->input->post('grace_period')?:1,
                                'modified_by' => $this->user->id,
                                'modified_on' => time(),
                            );
                            if($id = $this->loan_types_m->update($post->id,$update)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Loan type successfully added',
                                    'id' => $id,
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Could not add loan type',
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
                            'message' => 'Could not find group loan type selected',
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
        echo encrypt_json_encode(array('response'=>$response));
    }

    function update_loan_type_fines(){
        foreach ($this->request as $key => $value) {https://www.facebook.com/?ref=tn_tnmn
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
                    $id = $this->input->post('id');
                    if($post = $this->loan_types_m->get_group_loan_type($id)){
                        $this->_additional_fine_rules();
                        $this->form_validation->set_rules($this->fines_validation_rules);
                        if($this->form_validation->run()){
                            $enable_loan_fines = $this->input->post('enable_loan_fines');
                            $enable_outstanding_loan_balance_fines = $this->input->post('enable_outstanding_loan_balance_fines');
                            $update = array(
                                'enable_loan_fines' =>  $enable_loan_fines,
                                'enable_outstanding_loan_balance_fines'=>$enable_outstanding_loan_balance_fines,
                            );
                            if($enable_loan_fines){
                                $loan_fine_type    =  $this->input->post('loan_fine_type');
                                $update = $update+array('loan_fine_type'=>$loan_fine_type);
                                if($loan_fine_type==1){
                                    $update = $update + array(
                                        'fixed_fine_amount' =>  $this->input->post('fixed_fine_amount'),
                                        'fixed_amount_fine_frequency'   =>  $this->input->post('fixed_amount_fine_frequency'),
                                        'fixed_amount_fine_frequency_on' => $this->input->post('fixed_amount_fine_frequency_on'),
                                    );
                                }else if($loan_fine_type == 2){
                                    $update = $update + array(
                                        'percentage_fine_rate'  =>  $this->input->post('percentage_fine_rate'),
                                        'percentage_fine_frequency' =>  $this->input->post('percentage_fine_frequency'),
                                        'percentage_fine_on'    =>  $this->input->post('percentage_fine_on'),
                                    );
                                }else if($loan_fine_type==3){
                                    $one_off_fine_type     =  $this->input->post('one_off_fine_type');
                                    $update = $update+array('one_off_fine_type'=>$one_off_fine_type);
                                    if($one_off_fine_type==1){
                                        $update = $update + array('one_off_fixed_amount'  =>  $this->input->post('one_off_fixed_amount'));
                                    }else if($one_off_fine_type==2){
                                        $update = $update + array(
                                            'one_off_percentage_rate'   =>  $this->input->post('one_off_percentage_rate'),
                                            'one_off_percentage_rate_on'    =>  $this->input->post('one_off_percentage_rate_on'),
                                        );
                                    }
                                }
                            }
                            if($enable_outstanding_loan_balance_fines){
                                $outstanding_loan_balance_fine_type = $this->input->post('outstanding_loan_balance_fine_type');
                                $update = $update+array('outstanding_loan_balance_fine_type'=>$outstanding_loan_balance_fine_type);
                                if($outstanding_loan_balance_fine_type==1){
                                    $update = $update + array(
                                        'outstanding_loan_balance_fine_type'    =>  $this->input->post('outstanding_loan_balance_fine_type'),
                                        'outstanding_loan_balance_fine_fixed_amount'   =>$this->input->post('outstanding_loan_balance_fine_fixed_amount'),
                                        'outstanding_loan_balance_fixed_fine_frequency'=>$this->input->post('outstanding_loan_balance_fixed_fine_frequency'),
                                    );
                                }else if($outstanding_loan_balance_fine_type==2){
                                    $update = $update + array(
                                        'outstanding_loan_balance_percentage_fine_rate'=>$this->input->post('outstanding_loan_balance_percentage_fine_rate'),
                                        'outstanding_loan_balance_percentage_fine_frequency'=>$this->input->post('outstanding_loan_balance_percentage_fine_frequency'),
                                        'outstanding_loan_balance_percentage_fine_on'=>$this->input->post('outstanding_loan_balance_percentage_fine_on'),
                                    );
                                }else if($outstanding_loan_balance_fine_type==3){
                                    $update = $update + array(
                                        'outstanding_loan_balance_fine_one_off_amount'=>$this->input->post('outstanding_loan_balance_fine_one_off_amount'), 
                                    );
                                }
                            }
                            if($this->loan_types_m->update($post->id,$update)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'successful',
                                    'id' => $post->id,
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Error occured updating loan type(Product). Try again later'
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
                            'message' => 'Could not find group loan type selected',
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
        echo encrypt_json_encode(array('response'=>$response));
    }

    function update_general_details(){
        foreach ($this->request as $key => $value) {https://www.facebook.com/?ref=tn_tnmn
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
                    $id = $this->input->post('id');
                    if($post = $this->loan_types_m->get_group_loan_type($id)){
                        $this->_additional_general_details_rules();
                        $this->form_validation->set_rules($this->general_validation_rules);
                        if($this->form_validation->run()){
                            $enable_loan_processing_fee = $this->input->post('enable_loan_processing_fee')?:0;
                            $enable_loan_guarantors = $this->input->post('enable_loan_guarantors')?:0;
                            $update = array(
                                'enable_loan_processing_fee' => $enable_loan_processing_fee,
                                'enable_loan_guarantors' => $enable_loan_guarantors,
                            );
                            if($enable_loan_processing_fee){
                                $loan_processing_fee_type  =  $this->input->post('loan_processing_fee_type');
                                $update = $update + array('loan_processing_fee_type'=>$loan_processing_fee_type);
                                if($loan_processing_fee_type==1){
                                    $update = $update + array('loan_processing_fee_fixed_amount'  =>  $this->input->post('loan_processing_fee_fixed_amount'),);
                                }else if($loan_processing_fee_type==2){
                                    $update = $update + array(
                                        'loan_processing_fee_percentage_rate'=>$this->input->post('loan_processing_fee_percentage_rate'),
                                        'loan_processing_fee_percentage_charged_on' =>  $this->input->post('loan_processing_fee_percentage_charged_on'),);
                                }
                            }
                            if($enable_loan_guarantors){
                                $update = $update + array(
                                    "loan_guarantors_type" => $this->input->post('loan_guarantors_type'),
                                );
                                if($this->input->post('loan_guarantors_type') == 1){
                                    $update = $update + array(
                                        'minimum_guarantors' => $this->input->post('minimum_guarantors'),
                                        'maximum_guarantors' => $this->input->post('minimum_guarantors')+2,
                                    );
                                }else if($this->input->post('loan_guarantors_type') == 2){
                                    $update = $update + array(
                                        'minimum_guarantors' => $this->input->post('minimum_guarantors'),
                                        'maximum_guarantors' => $this->input->post('minimum_guarantors')+2,
                                    );
                                } 
                            }
                            if($this->loan_types_m->update($post->id,$update)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'successful',
                                    'id' => $post->id,
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Error occured updating loan type(Product). Try again later'
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
                            'message' => 'Could not find group loan type selected',
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
        echo encrypt_json_encode(array('response'=>$response));
    }

    function get_group_loan_type(){
        foreach ($this->request as $key => $value) {https://www.facebook.com/?ref=tn_tnmn
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
                    $id = $this->input->post('id');
                    if($post = $this->loan_types_m->get_group_loan_type($id)){
                        $loan_type = array(
                            'id' => $post->id,
                            'name' => $post->name,
                            'loan_amount_type' => $post->loan_amount_type,
                            'minimum_loan_amount' => $post->minimum_loan_amount,
                            'maximum_loan_amount' => $post->maximum_loan_amount,
                            'savings_times' => $post->loan_times_number,
                            'enable_reducing_balance_installment_recalculation' => $post->enable_reducing_balance_installment_recalculation,
                            'interest_type' => $post->interest_type,
                            'interest_rate' => $post->interest_rate,
                            'loan_interest_rate_per' => $post->loan_interest_rate_per,
                            'loan_repayment_period_type' => $post->loan_repayment_period_type,
                            'minimum_repayment_period' => $post->minimum_repayment_period,
                            'maximum_repayment_period' => $post->maximum_repayment_period,
                            'fixed_repayment_period' => $post->fixed_repayment_period,
                        );
                        $fine = array(
                            'enable_loan_fines' => $post->enable_loan_fines?:0,
                            'loan_fine_type' => $post->loan_fine_type?:0,
                            'fixed_fine_amount' => $post->fixed_fine_amount?:0,
                            'fixed_amount_fine_frequency' => $post->fixed_amount_fine_frequency?:0,
                            'fixed_amount_fine_frequency_on' => $post->fixed_amount_fine_frequency_on?:0,
                            'percentage_fine_rate' => $post->percentage_fine_rate?:0,
                            'percentage_fine_frequency' => $post->percentage_fine_frequency?:0,
                            'percentage_fine_on' => $post->percentage_fine_on?:0,
                            'one_off_fine_type' => $post->one_off_fine_type?:0,
                            'one_off_fixed_amount' => $post->one_off_fixed_amount?:0,
                            'one_off_percentage_rate' => $post->one_off_percentage_rate?:0,
                            'one_off_percentage_rate_on' => $post->one_off_percentage_rate_on?:0,
                            'enable_outstanding_loan_balance_fines' => $post->enable_outstanding_loan_balance_fines?:0,
                            'outstanding_loan_balance_fine_type' => $post->outstanding_loan_balance_fine_type?:0,
                            'outstanding_loan_balance_fine_fixed_amount' => $post->outstanding_loan_balance_fine_fixed_amount?:0,
                            'outstanding_loan_balance_fixed_fine_frequency' => $post->outstanding_loan_balance_fixed_fine_frequency?:0,
                            'outstanding_loan_balance_percentage_fine_rate' => $post->outstanding_loan_balance_percentage_fine_rate?:0,
                            'outstanding_loan_balance_percentage_fine_frequency' => $post->outstanding_loan_balance_percentage_fine_frequency?:0,
                            'outstanding_loan_balance_percentage_fine_on' => $post->outstanding_loan_balance_percentage_fine_on?:0,
                            'outstanding_loan_balance_fine_one_off_amount' => $post->outstanding_loan_balance_fine_one_off_amount?:0,
                        );
                        $general_details = array(
                            'enable_loan_processing_fee' => $post->enable_loan_processing_fee?:0,
                            'loan_processing_fee_type' => $post->loan_processing_fee_type?:0,
                            'loan_processing_fee_fixed_amount' => $post->loan_processing_fee_fixed_amount?:0,
                            'loan_processing_fee_percentage_rate' => $post->loan_processing_fee_percentage_rate?:0,
                            'loan_processing_fee_percentage_charged_on' => $post->loan_processing_fee_percentage_charged_on?:0,
                            'enable_loan_guarantors' => $post->enable_loan_guarantors?:0,
                            'loan_guarantors_type' => $post->loan_guarantors_type?:0,
                            'minimum_guarantors' => $post->minimum_guarantors?:0,
                            'loan_guarantors_type' => $post->loan_guarantors_type?:0,
                        );
                        $response = array(
                            'status' => 1,
                            'message' => 'successful',
                            'data' => array(
                               'loan_type' => $loan_type,
                               'fine' => $fine,
                               'general_details' => $general_details,
                            ),
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not find group loan type selected',
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
        echo encrypt_json_encode(array('response'=>$response));
    }

    function get_loan_types_list(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $id_number = $this->input->post('id_number')??0;
        if($this->user = $this->users_m->get_user_by_id_number($id_number)){
            $this->ion_auth->update_last_login($this->user->id);
                    $lower_limit = $this->input->post('lower_limit')?:0;
                    $upper_limit = $this->input->post('upper_limit')?:20;
                    $records_per_page = $upper_limit - $lower_limit;
                    $total_rows = $this->loan_types_m->count_group_loan_types();
                    $pagination = create_custom_pagination('group',$total_rows,$records_per_page,$lower_limit,TRUE);
                    $posts = $this->loan_types_m->limit($pagination['limit'])->get_group_loan_types();
                    $loan_types = array();
                    $group_currency = "KES";
                    foreach ($posts as $post) {
                        $repayment_period ='';
                        if($post->loan_repayment_period_type == 1){
                            $repayment_period = 'Fixed repayment of '.$post->fixed_repayment_period.' month(s)';
                        }elseif($post->loan_repayment_period_type == 2){
                            $repayment_period = 'Varying repayment between '.$post->minimum_repayment_period.' and '.$post->maximum_repayment_period.' month(s)';
                        }
                        if($post->loan_amount_type == 1){
                            $minimum_loan_amount = $post->minimum_loan_amount;
                            $maximum_loan_amount = $post->maximum_loan_amount;
                            $loan_amount = 'Between '.$group_currency.' '.number_to_currency($minimum_loan_amount).' - '.$group_currency.' '.number_to_currency($maximum_loan_amount);
                        }elseif($post->loan_amount_type == 2){  
                            $loan_amount = $post->loan_times_number.' times member savings';
                        }else{
                            $loan_amount = '';
                        }
                        if($post->enable_loan_processing_fee):
                            if($post->loan_processing_fee_type==1){
                                $loan_processing= 'Fixed Amount of '.number_to_currency($post->loan_processing_fee_fixed_amount);
                            }else{
                                $loan_processing = $post->loan_processing_fee_percentage_rate.'% of '.$this->loan->loan_processing_fee_percentage_charged_on[$post->loan_processing_fee_percentage_charged_on];
                            }
                        else:
                            $loan_processing = 'No Charge';
                        endif;

                        if($post->enable_loan_guarantors == 1){
                                if($post->loan_guarantors_type == 1){
                                    $guarantors= 'Atleast '.$post->minimum_guarantors.' guarantors every time a member is applying a loan ';   
                                }else if($post->loan_guarantors_type == 2){
                                   $guarantors= 'Atleast '.$post->minimum_guarantors.' guarantors when loan request exceeds maximum loan amount';
                                }else{
                                    $guarantors= 'Unknown value '.$post->loan_guarantors_type;
                                }
                        }else{
                            $guarantors = 'Not Required';  
                        }
                        if($post->enable_loan_fines):
                            $late_payment_fines= $this->loan->late_loan_payment_fine_types[$post->loan_fine_type].' of ';
                                if($post->loan_fine_type==1){
                                    $late_payment_fines.= $group_currency.' '.number_to_currency($post->fixed_fine_amount).' fine '.$this->loan->late_payments_fine_frequency[$post->fixed_amount_fine_frequency].' on ';
                                    $late_payment_fines.= isset($this->loan->fixed_amount_fine_frequency_on[$post->fixed_amount_fine_frequency_on])?$this->loan->fixed_amount_fine_frequency_on[$post->fixed_amount_fine_frequency_on]:'';
                                }else if($post->loan_fine_type==2){
                                    $late_payment_fines.= $post->percentage_fine_rate.'% fine '.$this->loan->late_payments_fine_frequency[$post->percentage_fine_frequency].' on '.$this->loan->percentage_fine_on[$post->percentage_fine_on];
                                }else if($post->loan_fine_type==3){
                                    if($post->one_off_fine_type==1){
                                        $late_payment_fines.= $group_currency.' '.number_to_currency($post->one_off_fixed_amount).' per Installment';
                                    }else if($post->one_off_fine_type==2){
                                        $late_payment_fines.=  $post->one_off_percentage_rate.'% on '.$this->loan->percentage_fine_on[$post->one_off_percentage_rate_on];
                                    }
                                }else{

                                }
                        else:
                            $late_payment_fines = 'Disabled';
                        endif;
                        if($post->enable_outstanding_loan_balance_fines):
                            if($post->outstanding_loan_balance_fine_type==1){
                                $outstanding_payment_fines = $group_currency.' '.number_to_currency($post->outstanding_loan_balance_fine_fixed_amount).' '.$this->loan->late_payments_fine_frequency[$post->outstanding_loan_balance_fixed_fine_frequency];
                            }else if($post->outstanding_loan_balance_fine_type==2){
                                $outstanding_payment_fines =  $post->outstanding_loan_balance_percentage_fine_rate.'% fine '.$this->loan->late_payments_fine_frequency[$post->outstanding_loan_balance_percentage_fine_frequency].' on '.$this->loan->percentage_fine_on[$post->outstanding_loan_balance_percentage_fine_on];
                            }else{
                                $outstanding_payment_fines =  'One Off Amount of '.$group_currency.' '.number_to_currency($post->outstanding_loan_balance_fine_one_off_amount);
                            }
                        else:
                            $outstanding_payment_fines = 'Disabled';
                        endif;
                        $loan_types[] = array(
                            'id' => $post->id,
                            'name' => $post->name,
                            'repayment_period' => $repayment_period,
                            'loan_amount' => $loan_amount,
                            'interest_rate' =>  $post->interest_rate.'% per '.$this->loan->loan_interest_rate_per[$post->loan_interest_rate_per].' on '.$this->loan->interest_types[$post->interest_type],
                            'loan_processing' => $loan_processing,
                            'guarantors' => $guarantors,
                            'late_payment_fines'=> $late_payment_fines,
                            'outstanding_payment_fines'=> $outstanding_payment_fines,
                            'is_hidden' => $post->active?0:1,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'time' => time(),
                        'message' => 'Loan Types list',
                        'loan_types' => $loan_types,
                    );
                
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function get_group_loan_type_options(){
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
                    $posts = $this->loan_types_m->get_active_group_loan_types($this->group->id);
                    $loan_types = array();
                    $group_currency = $this->countries_m->get_group_currency($this->group->id);
                    foreach ($posts as $post) {
                        $repayment_period ='';
                        if($post->loan_repayment_period_type == 1){
                            $repayment_period = 'Fixed repayment period of '.$post->fixed_repayment_period.' month(s)';
                        }elseif($post->loan_repayment_period_type == 2){
                            $repayment_period = 'Varying repayment period between '.$post->minimum_repayment_period.' and '.$post->maximum_repayment_period.' month(s)';
                        }
                        if($post->loan_amount_type == 1){
                            $minimum_loan_amount = $post->minimum_loan_amount;
                            $maximum_loan_amount = $post->maximum_loan_amount;
                            $loan_amount = 'Between '.$group_currency.' '.number_to_currency($minimum_loan_amount).' - '.$group_currency.' '.number_to_currency($maximum_loan_amount);
                        }elseif($post->loan_amount_type == 2){  
                            $loan_amount = $post->loan_times_number.' times member savings';
                        }
                        
                        if($post->enable_loan_guarantors == 1){
                                if($post->loan_guarantors_type == 1){
                                    $guarantors= 'Atleast '.$post->minimum_guarantors.' guarantors every time a member is applying a loan ';   
                                }else if($post->loan_guarantors_type == 2){
                                   $guarantors= 'Atleast '.$post->minimum_guarantors.' guarantors when loan request exceeds maximum loan amount';
                                }
                        }else{
                            $guarantors = 'Not Required';  
                        }
                        $loan_types[] = array(
                            'id' => $post->id,
                            'name' => $post->name,
                            'description' => $loan_amount.' for '.$repayment_period,
                            'repayment_period_type' => $post->loan_repayment_period_type,
                            'interest_type' => $post->interest_type,
                            'interest_rate' => $post->interest_rate,
                            'loan_interest_rate_per' => $post->loan_interest_rate_per,
                            'loan_repayment_period_type' => $post->loan_repayment_period_type,
                            'minimum_repayment_period' => $post->minimum_repayment_period,
                            'maximum_repayment_period' => $post->maximum_repayment_period,
                            'fixed_repayment_period' => $post->fixed_repayment_period,
                            'minimum_repayment_period' => $post->minimum_repayment_period, 
                            'maximum_repayment_period' => $post->maximum_repayment_period, 
                            'enable_loan_guarantors' => $post->enable_loan_guarantors,
                            'minimum_guarantors' => $post->minimum_guarantors?:0,
                            'enable_loan_fines' => $post->enable_loan_fines?:0,
                            'loan_fine_type' => $post->loan_fine_type?:0,
                            'fixed_fine_amount' => $post->fixed_fine_amount?:0,
                            'fixed_amount_fine_frequency' => $post->fixed_amount_fine_frequency?:0,
                            'fixed_amount_fine_frequency_on' => $post->fixed_amount_fine_frequency_on?:0,
                            'percentage_fine_rate' => $post->percentage_fine_rate?:0,
                            'percentage_fine_frequency' => $post->percentage_fine_frequency?:0,
                            'percentage_fine_on' => $post->percentage_fine_on?:0,
                            'one_off_fine_type' => $post->one_off_fine_type?:0,
                            'one_off_fixed_amount' => $post->one_off_fixed_amount?:0,
                            'one_off_percentage_rate' => $post->one_off_percentage_rate?:0,
                            'one_off_percentage_rate_on' => $post->one_off_percentage_rate_on?:0,
                            'enable_outstanding_loan_balance_fines' => $post->enable_outstanding_loan_balance_fines?:0,
                            'outstanding_loan_balance_fine_type' => $post->outstanding_loan_balance_fine_type?:0,
                            'outstanding_loan_balance_fine_fixed_amount' => $post->outstanding_loan_balance_fine_fixed_amount?:0,
                            'outstanding_loan_balance_fixed_fine_frequency' => $post->outstanding_loan_balance_fixed_fine_frequency?:0,
                            'outstanding_loan_balance_percentage_fine_rate' => $post->outstanding_loan_balance_percentage_fine_rate?:0,
                            'outstanding_loan_balance_percentage_fine_frequency' => $post->outstanding_loan_balance_percentage_fine_frequency?:0,
                            'outstanding_loan_balance_percentage_fine_on' => $post->outstanding_loan_balance_percentage_fine_on?:0,
                            'outstanding_loan_balance_fine_one_off_amount' => $post->outstanding_loan_balance_fine_one_off_amount?:0,
                            'enable_loan_processing_fee' => $post->enable_loan_processing_fee?:0,
                            'loan_processing_fee_type' => $post->loan_processing_fee_type?:0,
                            'loan_processing_fee_fixed_amount' => $post->loan_processing_fee_fixed_amount?:0,
                            'loan_processing_fee_percentage_rate' => $post->loan_processing_fee_percentage_rate?:0,
                            'loan_processing_fee_percentage_charged_on' => $post->loan_processing_fee_percentage_charged_on?:0,
                            'enable_loan_guarantors' => $post->enable_loan_guarantors?:0,
                            'loan_guarantors_type' => $post->loan_guarantors_type?:0,
                            'minimum_guarantors' => $post->minimum_guarantors?:0,
                            'loan_guarantors_type' => $post->loan_guarantors_type?:0,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'time' => time(),
                        'message' => 'deposit list',
                        'loan_types' => $loan_types,
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

    function hide(){
        foreach ($this->request as $key => $value) {https://www.facebook.com/?ref=tn_tnmn
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
                    $id = $this->input->post('id');
                    if($post = $this->loan_types_m->get_group_loan_type($id)){
                        if($post->active){
                            $update = array(
                                'active' => 0,
                                'modified_on' => time(),
                                'modified_by' => $this->user->id,
                            );
                            if($this->loan_types_m->update($post->id,$update)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'successful',
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Could not complete the process. Try again later',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Loan type already inactive',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not find group loan type selected',
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
        echo encrypt_json_encode(array('response'=>$response));
    }

    function unhide(){
        foreach ($this->request as $key => $value) {https://www.facebook.com/?ref=tn_tnmn
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
                    $id = $this->input->post('id');
                    if($post = $this->loan_types_m->get_group_loan_type($id)){
                        if($post->active){
                            $response = array(
                                'status' => 0,
                                'message' => 'Loan type already active',
                            );
                        }else{
                            $update = array(
                                'active' => 1,
                                'modified_on' => time(),
                                'modified_by' => $this->user->id,
                            );
                            if($this->loan_types_m->update($post->id,$update)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'successful',
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Could not complete the process. Try again later',
                                );
                            }
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not find group loan type selected',
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
        echo encrypt_json_encode(array('response'=>$response));
    }

    function delete(){ 
        foreach ($this->request as $key => $value) {https://www.facebook.com/?ref=tn_tnmn
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
                    $id = $this->input->post('id');
                    if($post = $this->loan_types_m->get_group_loan_type($id)){
                        if($this->loans_m->count_group_loan_types($post->id,$this->group->id)){
                            $response = array(
                                'status' => 0,
                                'message' => 'Loan type already in use. Can not be deleted. Option to hide the loan type',
                                'time' => time(),
                            );
                        }else{
                            if($this->loan_applications_m->count_group_loan_types($post->id,$this->group->id)){
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Loan type already in use. Can not be deleted. Option to hide the loan type',
                                    'time' => time(),
                                );
                            }else{
                                if($this->loan_types_m->safe_delete($post->id,$this->group->id)){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Loan type successfully removed',
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Error occured performing this action. Try again later',
                                    );
                                }
                            }
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not find group loan type selected',
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
        echo encrypt_json_encode(array('response'=>$response));
    }

    function get_group_loan_type_application_options(){
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
                    $posts = $this->loan_types_m->get_active_group_loan_types($this->group->id);
                    $loan_types = array();
                    $group_currency = $this->countries_m->get_group_currency($this->group->id);
                    foreach ($posts as $post) {
                        $loan_types[] = array(
                            'name' => $post->name,
                            'repayment_period_type' => $post->loan_repayment_period_type,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'time' => time(),
                        'message' => 'deposit list',
                        'loan_types' => $loan_types,
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
}