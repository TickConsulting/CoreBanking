<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends Member_Controller{
    
    protected $accounts = array();
    
    protected $loan_repayment_period_type = array(1 => 'Fixed Repayment Period', 2 => 'Varying Repayment Period');
    
    protected $loan_application_rules = array(
        array(
            'field' => 'loan_type_id', 
            'label' => 'Loan Type', 
            'rules' => 'trim|required|numeric'
        ),array(
            'field' => 'loan_application_amount', 
            'label' => 'Loan Application Amount', 
            'rules' => 'trim|required|currency'
        ), array(
            'field' => 'loan_rules_check_box', 
            'label' => 'Agree to loan rules', 
            'rules' => 'trim|required|numeric'
        ), array(
            'field' => 'guaranteed_amount[]', 
            'label' => 'Guarantor Amount', 
            'rules' => ''
        ), array(
            'field' => 'guarantor_id[]', 
            'label' => 'Guranter Name', 
            'rules' => 'callback__valid_guarantor_details'
        ), array(
            'field' => 'repayment_period', 
            'label' => 'Loan Repayment Period', 
            'rules' => 'trim'
        )
    );

    protected $eazzy_club_loan_application_rules = array(
        array(
            'field' => 'loan_type_id', 
            'label' => 'Loan Type', 
            'rules' => 'trim|required|numeric'
        ),array(
            'field' => 'loan_application_amount', 
            'label' => 'Loan Application Amount', 
            'rules' => 'trim|required|currency'
        ), array(
            'field' => 'loan_rules_check_box', 
            'label' => 'Agree to loan rules', 
            'rules' => 'trim|required|numeric'
        ), array(
            'field' => 'guaranteed_amount[]', 
            'label' => 'Guarantor Amount', 
            'rules' => ''
        ),array(
            'field' => 'supervisor_id', 
            'label' => 'Supervisor Name', 
            'rules' => 'trim|required'
        ), array(
            'field' => 'guarantor_id[]', 
            'label' => 'Guranter Name', 
            'rules' => 'callback__valid_loan_application_details'
        ), array(
            'field' => 'repayment_period', 
            'label' => 'Loan Repayment Period', 
            'rules' => 'trim'
        ));
   
    protected $supervisor_recommendation = array(
        array(
            'field'=>'comment',
            'label'=>'Supervisor Comment',
            'rules'=>'trim|required'
        ),
        array(
            'field'=>'performance_id',
            'label'=>'Performance Rating',
            'rules'=>'trim|required|numeric'
        ),
        array(
            'field'=>'performance_id',
            'label'=>'Performance Rating',
            'rules'=>'trim|required|numeric'
        ),
        array(
            'field'=>'disciplinary_id',
            'label'=>'Discplinary/pending cases',
            'rules'=>'trim|required|numeric|callback__check_if_oranisational_roles_exist'
        ),
        array(
            'field'=>'stamp_date',
            'label'=>'Recommendation date',
            'rules'=>'trim|required'
        ),
    );

    protected $hr_appraisal_rules = array(
        array(
            'field'=>'contract_type_id',
            'label'=>'Choose type of contract',
            'rules'=>'trim|required|numeric'
        ),
        array(
            'field'=>'contract_end_date',
            'label'=>'Contract End Date',
            'rules'=>'trim|required|callback__check_if_sacco_officer_exist'
        ),
        array(
            'field'=>'loan_amount[]',
            'label'=>'Loan Amount',
            'rules'=>'trim|currency'
        ),
        array(
            'field'=>'loan_amount_installments[]',
            'label'=>'Loan Amount installments',
            'rules'=>'trim|currency'
        ),
        array(
            'field'=>'repayment_period[]',
            'label'=>'Loan Term',
            'rules'=>'trim'
        ),
        array(
            'field'=>'loan_balance[]',
            'label'=>'Loan Balance',
            'rules'=>'trim|currency'
        ),
        array(
            'field'=>'net_pay',
            'label'=>'Net Pay',
            'rules'=>'trim|currency'
        ),
        array(
            'field'=>'percentage_net_pay',
            'label'=>'Percentage net pay',
            'rules'=>'trim'
        ),
        array(
            'field'=>'is_loan_exisiting',
            'label'=>'Check if loan exist or not',
            'rules'=>'trim|required|callback__check_loan_values_are_correct'
        ),
        
        
    );

    protected $sacco_appraisal_rules = array(
        array(
            'field'=>'stamp_date',
            'label'=>'Choose stamp date',
            'rules'=>'trim'
        ),
         array(
            'field'=>'percentage_net_pay',
            'label'=>'Percentage net pay',
            'rules'=>'trim|required'
        ),
    );

    protected $applicant_affirmation_rules = array(
        array(
            'field'=>'stamp_date',
            'label'=>'Choose stamp date',
            'rules'=>'trim|callback__check_if_credit_committee_member_exist'
        ),
    );

    protected $committe_validation = array(
        array(
            'field'=>'action_id',
            'label'=>'committee Action is required',
            'rules'=>'trim|numeric|required'
        ),
        array(
            'field'=>'comment',
            'label'=>'Comment for rejecting / deffering',
            'rules'=>'callback__check_action_option'
        ),
        array(
            'field'=>'account_id',
            'label'=>'Account no',
            'rules'=>'trim|required'
        )
    );

    protected $signatory_validation = array(
        array(
            'field'=>'comment',
            'label'=>'Comment',
            'rules'=>'trim'
        ),
        array(
            'field'=>'account_id',
            'label'=>'Account no',
            'rules'=>'trim|required'
        )
    );

    protected $sacco_manager = array(
        array(
                'field'=>'account_id',
                'label'=>'Account no',
                'rules'=>'trim|required'
            )
    );


    protected $data = array();
    
    function __construct()
    {
        parent::__construct();        
        $this->load->model('loans_m');
        $this->load->model('loan_applications/loan_applications_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('loan_repayments/loan_repayments_m');
        $this->load->model('loan_invoices/loan_invoices_m');
        $this->load->library('transactions');
        $this->load->library('loan');
        $this->load->library('notifications');
        $this->load->model('members/members_m');
        $this->load->model('contributions/contributions_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->model('withdrawals/withdrawals_m');
        $this->load->model('loan_types/loan_types_m');
        $this->load->model('reports/reports_m');
        $this->load->model('group_roles/group_roles_m');
        $this->data['interest_types']                            = $this->loan->interest_types;
        $this->data['interest_types']                            = $this->loan->interest_types;
        $this->data['late_loan_payment_fine_types']              = $this->loan->late_loan_payment_fine_types;
        $this->data['late_payments_fine_frequency']              = $this->loan->late_payments_fine_frequency;
        $this->data['percentage_fine_on']                        = $this->loan->percentage_fine_on;
        $this->data['one_off_fine_types']                        = $this->loan->one_off_fine_types;
        $this->data['one_off_percentage_rate_on']                = $this->loan->one_off_percentage_rate_on;
        $this->data['loan_processing_fee_types']                 = $this->loan->loan_processing_fee_types;
        $this->data['sms_template_default']                      = $this->loan->sms_template_default;
        $this->data['loan_grace_periods']                        = $this->loan->loan_grace_periods;
        $this->data['loan_processing_fee_percentage_charged_on'] = $this->loan->loan_processing_fee_percentage_charged_on;
        $this->data['loan_days']                                 = $this->loan->loan_days;
        $this->data['loan_interest_rate_per']                    = $this->loan->loan_interest_rate_per;
        $this->data['loan_repayment_period_type']                = $this->loan->loan_repayment_period_type;
        $this->accounts         = $this->accounts_m->get_group_account_options();
        $this->data['accounts'] = $this->accounts;
    }

    function _valid_guarantor_details(){
        $loan_type_id  = $this->input->post('loan_type_id');
        $guarantor_ids  = $this->input->post('guarantor_id');
        $guarantor_amounts  = $this->input->post('guaranteed_amount');
        $loan_application_amount = currency($this->input->post('loan_application_amount'));
        $repayment_period = $this->input->post('repayment_period');
        if($loan_type_id){            
            if($loan_type = $this->loan_types_m->get($loan_type_id)){ 
              //print_r($loan_type); die();               
                $selected_guarantors = 0;
                $selected_guarantors_array = array();
                $selected_guaranteed_amount = 0;
                if($guarantor_ids){
                    foreach ($guarantor_ids as $key => $guarantor_id) {
                        if($guarantor_id){
                            if($guarantor_id == $this->member->id){
                                $this->form_validation->set_message('_valid_guarantor_details','You cannot select yourself as a guarantor');
                                return FALSE;
                            }else{
                                if($guarantor_savings = $this->transactions->get_group_member_savings($this->group->id,$guarantor_id)){
                                    $maximum_amount_to_grant = currency($guarantor_savings*($loan_type->loan_times_number?:2));
                                    if($guarantor_amount = currency(isset($guarantor_amounts[$key])?currency($guarantor_amounts[$key]):0)){
                                        if($guarantor_amount > $maximum_amount_to_grant){
                                            $this->form_validation->set_message('_valid_guarantor_details',$this->active_group_member_options[$guarantor_id].' can not guarantee that much ');
                                            return FALSE;
                                        }else{
                                            if(in_array($guarantor_id, $selected_guarantors_array)){
                                                $this->form_validation->set_message('_valid_guarantor_details',$this->active_group_member_options[$guarantor_id].' has been selected more than once ');
                                                return FALSE;
                                            }                                            
                                            $selected_guaranteed_amount+=$guarantor_amount;
                                            ++$selected_guarantors;
                                            $selected_guarantors_array[]=$guarantor_id; 
                                        }
                                    }else{
                                        $this->form_validation->set_message('_valid_guarantor_details','You cannot select a guarantor without selecting amount');
                                        return FALSE;
                                    }
                                }
                            }
                        }else{

                        }
                    }
                }
                if($loan_type->loan_amount_type == 1){//range
                    if($loan_application_amount < $loan_type->minimum_loan_amount){
                         $this->form_validation->set_message('_valid_guarantor_details','Amount applied is below the required minimum amount');
                         return FALSE;
                    }elseif($loan_application_amount > $loan_type->maximum_loan_amount){

                        if($loan_type->enable_loan_guarantors){

                            if($selected_guarantors < $loan_type->minimum_guarantors){
                                $this->form_validation->set_message('_valid_guarantor_details','Selected less guarantors than the required');
                                return FALSE;
                            }else{
                                if($loan_type->loan_guarantors_type == 1){//every time 
                                    $this->form_validation->set_message('_valid_guarantor_details','Amount applied is above the required maximum amount');
                                    return FALSE;
                                }elseif ($loan_type->loan_guarantors_type == 2) {//when exceeds savings
                                    $amount_above_maximum = currency($loan_application_amount - $loan_type->maximum_loan_amount);
                                    if($selected_guaranteed_amount < $amount_above_maximum){
                                        $this->form_validation->set_message('_valid_guarantor_details','Guaranteed amount is less than allowed amount to be guaranteed');
                                        return FALSE;
                                    }
                                }
                            }
                        }
                        
                    }else{
                        if($loan_type->enable_loan_guarantors){

                            if($selected_guarantors < $loan_type->minimum_guarantors){
                                $this->form_validation->set_message('_valid_guarantor_details','Guarantors selected are less than the required guarantors');
                                return FALSE;
                            }else{
                                if($loan_type->loan_guarantors_type == 1){//every time  
                                }elseif ($loan_type->loan_guarantors_type == 2) {//when exceeds savings
                                    $amount_above_maximum = currency($loan_application_amount - $loan_type->maximum_loan_amount);
                                    if($selected_guaranteed_amount < $amount_above_maximum){
                                        $this->form_validation->set_message('_valid_guarantor_details','Guaranteed amount is less than allowed amount to be guaranteed');
                                        return FALSE;
                                    }
                                }
                            }

                        }else{
                            if($loan_application_amount > $loan_type->maximum_loan_amount){
                                $this->form_validation->set_message('_valid_guarantor_details','Amount applied is above the required maximum amount');
                                return FALSE;
                            }
                        }
                    }
                }else if($loan_type->loan_amount_type == 2){//member savings
                    $member_savings = $this->transactions->get_group_member_savings($this->group->id,$this->member->id);
                    $maximum_allowed_loan = currency($member_savings * $loan_type->loan_times_number);
                    $amount_above_savings = currency($loan_application_amount - $maximum_allowed_loan);
                    if($loan_type->enable_loan_guarantors){
                        if($loan_type->loan_guarantors_type == 1){//every time
                            if($selected_guarantors < $loan_type->minimum_guarantors){
                                $this->form_validation->set_message('_valid_guarantor_details','Guarantors selected is less than the minimum guarantors required');
                                return FALSE;
                            }else{

                            }
                        }elseif ($loan_type->loan_guarantors_type == 2) {//when exceeds savings
                            if($loan_application_amount > $maximum_allowed_loan){
                                if($selected_guarantors < $loan_type->minimum_guarantors){
                                    $this->form_validation->set_message('_valid_guarantor_details','Guarantors selected are less than the required');
                                    return FALSE;
                                }else{
                                    if($selected_guaranteed_amount < $amount_above_savings){
                                        $this->form_validation->set_message('_valid_guarantor_details','Guaranteed amount is less than allowed amount to be guaranteed');
                                        return FALSE;
                                    }
                                }
                            }else{

                            }                          
                        }
                    }else{
                        if($loan_application_amount > $maximum_allowed_loan){
                            $this->form_validation->set_message('_valid_guarantor_details','Loan applied is above '.$loan_type->loan_times_number.' times your savings');
                            return FALSE;
                        }else{
                        }
                    }

                }else{
                    $this->form_validation->set_message('_valid_guarantor_details','Invalid loan type selected');
                    return FALSE;
                }
                //check repayment period here
                if($loan_type->loan_repayment_period_type == 1){//fixed

                }elseif($loan_type->loan_repayment_period_type == 2){
                    if($repayment_period < $loan_type->minimum_repayment_period){
                        $this->form_validation->set_message('_valid_guarantor_details','Loan repayment period is less than the allowed repayment period');
                        return FALSE;
                    }else if($repayment_period > $loan_type->maximum_repayment_period){
                        $this->form_validation->set_message('_valid_guarantor_details','Loan repayment period is above than the allowed repayment period');
                        return FALSE;
                    }
                }

                return TRUE;
            }else{
                $this->form_validation->set_message('_valid_guarantor_details','Invalid loan type selected');
                return FALSE;
            }
        }else{
            $this->form_validation->set_message('_valid_guarantor_details','Kindly select loan type');
            return FALSE;
        }
    }
    
    
    function index(){
        $this->data['loan_limit'] = 0;
        $this->data['loan_balances'] = $this->loans_m->get_total_loan_balances($this->group->id,$this->member->id);
        $this->data['tota_loan_amount'] = $this->loans_m->get_total_member_loan_amount($this->group->id,$this->member->id);
        $this->data['total_loan_repaid'] = $this->loans_m->get_total_member_loan_amount_paid($this->group->id,$this->member->id);
        $this->template->title(translate('Your Loans Summary'))->build('member/index',$this->data);
    }
    
    function your_loans(){
        $total_rows   = $this->loans_m->count_all_member_loans();
        $pagination               = create_pagination('group/loans/listing/pages', $total_rows, 50, 5, TRUE);
        $this->data['posts']      = $this->loans_m->limit($pagination['limit'])->get_member_loans();
        $this->data['loan_type_options'] = $this->loan_types_m->get_options();
        $this->data['pagination'] = $pagination;
        $this->data['member']     = $this->member;
        $this->template->title(translate('Your Loans'))->build('member/listing', $this->data);
    }
    
    function declined_loans(){
        $group_id                 = $this->group->id;
        $member_id                = $this->member->id;
        $total_rows               = $this->loans_m->count_all_member_loans();
        $pagination               = create_pagination('group/loans/listing/pages', $total_rows, 50, 5, TRUE);
        $loan_application_details = $this->loan_applications_m->limit($pagination['limit'])->get_member_loans($group_id, $member_id);
        
        foreach ($loan_application_details as $key => $loan_details) {
            $loan_type_id          = $loan_details->loan_type_id;
            $get_loan_type_details = $this->loan_types_m->get($loan_type_id);
        }
        $this->data['posts']             = $loan_application_details;
        $this->data['loan_type_details'] = $get_loan_type_details;
        $this->data['interest_types']    = $this->loan->interest_types;
        $this->data['pagination']        = $pagination;
        $this->data['member']            = $this->member;
        $this->template->title('My Declined Loans')->build('member/declined_listing', $this->data);   
    }
    
    
    function view_installments($id = 0)
    {
        $id or redirect('group/loans/listing');
        $posts = $this->loan_invoices_m->get_loan_installments($id);
        
        if (!$posts) {
            $this->session->set_flashdata('error', 'The loan has no installments to display');
            redirect('group/loans/listing');
            
        }
        
        $loan  = $this->loans_m->get_loan_and_member($id);
        $this->data['loan']                                      = $loan;
        $this->data['posts']                                     = $posts;
        $this->data['members']                                   = $this->members_m->get_group_member_options();
        $this->data['loan_guarantors']                           = $this->loans_m->get_loan_guarantors($id);
        $this->data['accounts']                                  = $this->accounts;
        $this->template->title($loan->first_name . ' ' . $loan->last_name . ' Loan Installments')->build('member/view_installments', $this->data);
        
    }
    
    function loan_statement($id = 0)
    {
        $id or redirect('group/loans/listing');
        $loan = $this->loans_m->get_loan_and_member($id);
        if (!$loan) {
            $this->session->set_flashdata('info', 'Sorry the loan does not exist');
            redirect('group/loans/listing');
        }        
        $total_installment_payable = $this->loan_invoices_m->get_total_installment_loan_payable($id);
        $total_fines               = $this->loan_invoices_m->get_total_loan_fines_payable($id);
        $total_paid                = $this->loan_repayments_m->get_loan_total_payments($id);
        $loan_balance              = $this->loans_m->get_loan_balance($id);
        $total_transfers_out       = $this->loan_invoices_m->get_total_loan_transfers_out($id);
        
        
        $posts = $this->loans_m->get_loan_statement($id);
        
        $this->data['loan']                      = $loan;
        $this->data['posts']                     = $posts;
        $this->data['total_installment_payable'] = $total_installment_payable;
        $this->data['total_fines']               = $total_fines;
        $this->data['total_transfers_out']       = $total_transfers_out;
        $this->data['total_paid']                = $total_paid;
        $this->data['lump_sum_remaining']        = $this->loan_invoices_m->get_loan_lump_sum_as_date($id);
        $this->accounts                          = $this->accounts_m->get_group_account_options(FALSE);
        $this->data['accounts']                  = $this->accounts;
        $this->data['deposit_options']           = $this->transactions->deposit_method_options;
        $this->template->title($loan->first_name . ' ' . $loan->last_name . ' Loan Statement')->build('shared/loan_statement', $this->data);
    }

    function calculator()
    {
        $post = new StdClass();
        $loan_values = array();
        $total_amount_payable = 0;
        $total_principle_amount = 0;
        $total_interest = 0;
        $monthly_payment = 0;
        $this->validation_rules = array(
            array(
                'field' => 'loan_amount',
                'label' => 'Loan Amount',
                'rules' => 'required|currency|trim',)
            ,array(
                'field' => 'interest_type',
                'label' => 'Loan Interest Type',
                'rules' => 'required|numeric|trim',)
            ,array(
                'field' => 'repayment_period',
                'label' => 'Loan Repayment Period',
                'rules' => 'required|numeric|trim',
                ),
            array(
                'field' => 'loan_interest_rate_per',
                'label' => 'Loan Interest Rate Per',
                'rules' => 'required|numeric|trim',
                ),array(
                'field' => 'interest_rate',
                'label' => 'Loan Interest Rate',
                'rules' => 'numeric|trim',)
        );
        if($this->input->post('interest_type')==1 || $this->input->post('interest_type')==2)
        {
            $this->validation_rules[] = array(
                        'field' => 'interest_rate',
                        'label' => 'Loan Interest Rate',
                        'rules' => 'required|numeric|trim',);
            $this->form_validation->set_rules($this->validation_rules);

            if($this->form_validation->run()){
                $loan_values = $this->loan->calculate_loan_balance_invoice(
                    $this->input->post('loan_amount'),
                    $this->input->post('interest_type'),
                    $this->input->post('interest_rate'),
                    $this->input->post('repayment_period'),'',time(),$this->input->post('loan_interest_rate_per'));

                foreach ($loan_values as $key => $value) {
                    $value = (object)$value;
                    $total_amount_payable +=$value->amount_payable;
                    $total_principle_amount+=$value->principle_amount_payable;
                    $total_interest+=$value->interest_amount_payable;
                    $monthly_payment=$value->amount_payable;
                }
            }
        }
        else if($this->input->post('interest_type')==3)
        {
           if($this->input->post('custom_interest_procedure')==1){
                $this->validation_rules[] = array(
                    'field' =>  'interest_rate_date_from',
                    'label' =>  'Interest Rate Date From',
                    'rules' =>  'callback__interest_rate_breakdown'
                );

                $this->form_validation->set_rules($this->validation_rules);
                if($this->form_validation->run())
                {
                    $custom_interest_rate = array(
                            'date_from' =>  $this->input->post('interest_rate_date_from'),
                            'date_to' =>  $this->input->post('interest_rate_date_to'),
                            'rate' =>  $this->input->post('custom_interest_rate'),
                        );
                    $loan_values = $this->loan->calculate_loan_balance_invoice_for_custom(
                        $this->input->post('loan_amount'),
                        $this->input->post('custom_interest_procedure'),
                        $this->input->post('repayment_period'),'',$custom_interest_rate);
                    
                    foreach ($loan_values as $key => $value) {
                        $value = (object)$value;
                        $total_amount_payable +=$value->amount_payable;
                        $total_principle_amount+=$value->principle_amount_payable;
                        $total_interest+=$value->interest_amount_payable;
                        $monthly_payment=$value->amount_payable;
                    }
                }
           }

           else if($this->input->post('custom_interest_procedure')==2){
                $this->validation_rules[] = array(
                    'field' =>  'custom_payment_date',
                    'label' =>  'Loan installment payment date',
                    'rules' =>  'callback__installment_breakdown'
                );

                $this->form_validation->set_rules($this->validation_rules);
                if($this->form_validation->run()){
                    $custom_istallments = array(
                            'payment_date' =>  $this->input->post('custom_payment_date'),
                            'amount_payable' =>  $this->input->post('custom_amount_payable'),
                        );
                    $loan_values = $this->loan->calculate_loan_balance_invoice_for_custom(
                        $this->input->post('loan_amount'),
                        $this->input->post('custom_interest_procedure'),
                        $this->input->post('repayment_period'),'',$custom_istallments);

                    foreach ($loan_values as $key => $value) {
                        $value = (object)$value;
                        $total_amount_payable +=$value->amount_payable;
                        $total_principle_amount+=$value->principle_amount_payable;
                        $total_interest+=$value->interest_amount_payable;
                        $monthly_payment=$value->amount_payable;
                    }
                }
           }
        }
        
        foreach ($this->validation_rules as $key => $field) 
        {
            $field_value        = $field['field'];
            $post->$field_value = set_value($field['field']);
        }

        $this->data['post'] = $post;
        $this->data['total_amount_payable']=$total_amount_payable;
        $this->data['total_principle_amount'] = $total_principle_amount;
        $this->data['total_interest'] = $total_interest;
        $this->data['loan_values'] = $loan_values;
        $this->data['monthly_payment'] = $monthly_payment;
        $this->data['loan_types'] = $this->loan_types_m->get_options($this->group->id);
        $this->template->title('Loan Calculator')->build('shared/calculator',$this->data);
    }

    function calculator_loan_type(){
        $post = new StdClass();
        $loan_values = array();
        $total_amount_payable = 0;
        $total_principle_amount = 0;
        $total_interest    = 0;
        $monthly_payment  = 0;
        $group_id     = $this->group->id;
        $this->validate_rules   = array(
            array(
                'field' => 'loan_type_id',
                'label' => 'Loan Type',
                'rules' => 'trim|required|numeric'
            ),
            array(
                'field' => 'loan_application_amount',
                'label' => 'Loan Amount',
                'rules' => 'currency|trim'
            ),
            array(
                'field' => 'repayment_period',
                'label' => 'Loan Repayment Period',
                'rules' => 'trim'
            )
        );
        $loan_type_id  = $this->input->post('loan_type_id');
        $loan_amount =  currency($this->input->post('loan_application_amount'));
        $repayment_period  = $this->input->post('repayment_period');
        $maximum_loan_amount_from_savings  = $this->input->post('maximum_loan_amount_from_savings');
        $get_loan_type_details  = $this->loan_types_m->get($loan_type_id, $group_id);
        $this->form_validation->set_rules($this->validate_rules);
        if ($this->form_validation->run()) {
            if (!empty($get_loan_type_details)) {
                $loan_clacultor_entries = TRUE;

                if($get_loan_type_details->loan_amount_type == 1){
                    $maximum_amount = $get_loan_type_details->maximum_loan_amount;
                    $minimum_loan_amount = $get_loan_type_details->minimum_loan_amount;                    
                    if($loan_amount > $maximum_amount || $loan_amount < $minimum_loan_amount){
                        $this->session->set_flashdata('error','Loan amount applied is not within  ranges allowed the minimum amount allowed is '. $this->group_currency .' ' .number_to_currency($minimum_loan_amount 
                        ).' and the maximum amount allowed is '. $this->group_currency .' ' . number_to_currency($maximum_amount ).' ');
                        $loan_clacultor_entries = FALSE;
                    }
                }else if($get_loan_type_details->loan_amount_type == 2){
                    if($loan_amount > $maximum_loan_amount_from_savings ){
                        $this->session->set_flashdata('error','Loan amount aplied is greater than the amount allowed the highest amount allowed  is '. $this->group_currency .' ' . number_to_currency($maximum_loan_amount_from_savings ) .' ');
                        $loan_clacultor_entries = FALSE;
                    }
                }

                if($get_loan_type_details->loan_repayment_period_type == 2){
                    $minimum_repayment_period = $get_loan_type_details->minimum_repayment_period;
                    $maximum_repayment_period =  $get_loan_type_details->maximum_repayment_period;
                        if($repayment_period >= $minimum_repayment_period && $repayment_period <= $maximum_repayment_period){
                            $loan_repayment_period = $repayment_period;
                        }else{
                            $this->session->set_flashdata('error','Repayment period is not within the date ranges allowed the minimum repayment period is '. $minimum_repayment_period .' and the maximum repayment period is '.  $maximum_repayment_period .' ');
                        }
                }else{
                    $loan_repayment_period = $get_loan_type_details->fixed_repayment_period;
                } 
                if($loan_clacultor_entries){                 
                    if($get_loan_type_details->interest_type == 1 || $get_loan_type_details->interest_type == 2){
                        $loan_values = $this->loan->calculate_loan_balance_invoice(
                                $loan_amount,
                                $get_loan_type_details->interest_type,
                                $get_loan_type_details->interest_rate,
                                $loan_repayment_period,
                                '',time(),
                                $get_loan_type_details->loan_interest_rate_per);
                        foreach ($loan_values as $key => $value) {
                            $value = (object)$value;
                            $total_amount_payable +=$value->amount_payable;
                            $total_principle_amount+=$value->principle_amount_payable;
                            $total_interest+=$value->interest_amount_payable;
                            $monthly_payment=$value->amount_payable;
                        }
                    }
                }

            } else {
                $this->session->set_flashdata('error', "Loan type details is missing");
            }
        } else {
            foreach ($this->validate_rules as $key => $field) {
                $field_value        = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $this->data['post']                     = $post;
        $this->data['total_amount_payable']     = $total_amount_payable;
        $this->data['total_principle_amount']   = $total_principle_amount;
        $this->data['total_interest']           = $total_interest;
        $this->data['loan_values']              = $loan_values;
        $this->data['monthly_payment']          = $monthly_payment;
        $this->data['group_loan_types_options'] = $this->loan_types_m->get_options($group_id);
        $this->data['get_loan_type_options']    = $get_loan_type_details;
        $this->data['loan_interest_rate_per']   = $this->loan->loan_interest_rate_per;
        $this->data['interest_types'] = $this->loan->interest_types;
        $this->template->title('Loan Calculator')->build('shared/calculator', $this->data);
        
    }

    function get_loan_repayment_type()
    {
        $loan_type_id               = $this->input->post('loan_type_id');
        $get_loan_type_details      = $this->loan_types_m->get($loan_type_id);
        $loan_repayment_period_type = $get_loan_type_details->loan_repayment_period_type;
        if ($loan_repayment_period_type == 1) {
            echo json_encode(1);
        } elseif ($loan_repayment_period_type == 2) {
            echo json_encode(2);
        }
    }
    
    function _interest_rate_breakdown()
    {
        $i = 0;
        $interest_rate_date_from = $this->input->post('interest_rate_date_from');
        $interest_rate_date_to   = $this->input->post('interest_rate_date_to');
        $custom_interest_rate    = $this->input->post('custom_interest_rate');
        foreach ($interest_rate_date_from as $key => $interest) {
            ++$i;
            if (empty($interest)) {
                $this->form_validation->set_message('_interest_rate_breakdown', '`Apply Rate From date` at line ' . $i . ' is required');
                return FALSE;
            } else if (empty($interest_rate_date_to[$key])) {
                $this->form_validation->set_message('_interest_rate_breakdown', '`Apply Rate To Date at line` ' . $i . ' is required');
                return FALSE;
            } else if (empty($custom_interest_rate[$key])) {
                if (is_numeric($custom_interest_rate[$key])) {
                } else {
                    $this->form_validation->set_message('_interest_rate_breakdown', 'The custom loan interest rate field at line ' . $i . ' is required');
                    return FALSE;
                }
            } else {
                if ($interest >= $interest_rate_date_to[$key]) {
                    $this->form_validation->set_message('_interest_rate_breakdown', 'Apply Rate From date from at line ' . $i . ' can not be bigger than `Apply Rate To Date`');
                    return FALSE;
                }
                if (isset($interest_rate_date_to[$key - 1]))
                    if ($interest != $interest_rate_date_to[$key - 1]) {
                        $this->form_validation->set_message('_interest_rate_breakdown', '`Apply Rate From date`, at line ' . ++$key . ', should be equal to `Apply Rate To Date` at line ' . --$i);
                        return FALSE;
                    }
            }
        }
        
        return TRUE;
    }
    
    function _installment_breakdown()
    {
        $i                  = 0;
        $max                = 0;
        $min                = '31-12-2035';
        $sum_amount_payable = 0;
        $date_payable       = $this->input->post('custom_payment_date');
        $amount_payable     = $this->input->post('custom_amount_payable');
        $loan_amount        = $this->input->post('loan_amount');
        $repayment_period   = $this->input->post('repayment_period');
        $disbursement_date  = $this->input->post('disbursement_date');
        
        foreach ($date_payable as $key => $value) {
            ++$i;
            if (empty($value)) {
                $this->form_validation->set_message('_installment_breakdown', '`Installment expected payment date` field at line ' . $i . ' is required');
                return FALSE;
            }
            if (empty($amount_payable[$key])) {
                $this->form_validation->set_message('_installment_breakdown', '`Amount payable` field at line ' . $i . ' is required');
                return FALSE;
            }
            if (strtotime($value) > strtotime($max)) {
                $max = $value;
            }
            if (strtotime($value) < strtotime($min)) {
                $min = $value;
            }
            $sum_amount_payable += currency($amount_payable[$key]);
        }
        
        if ($max && $min) {
            $disbursement_date = $disbursement_date ?: time();
            $diff              = round((strtotime($max) - strtotime($min) + 2419000) / (604800 * 4));
            if ($diff < $repayment_period) {
                $this->form_validation->set_message('_installment_breakdown', 'The installment breakdown payment period is less than the loan repayment period as stated');
                return FALSE;
            }
        }
        
        else if ($loan_amount > $sum_amount_payable) {
            $this->form_validation->set_message('_installment_breakdown', 'Amount Payable is less than the loan amount');
            return FALSE;
        } else if ($strtotime($min) < strtotime($disbursement_date)) {
            $this->form_validation->set_message('_installment_breakdown', 'Loan repayment date cannot be before the loan disbursement date');
            return FALSE;
        } else {
            return TRUE;
        }
        
    }
    
    // function loan_applications()
    // {
    //     $total_rows               = $this->loan_applications_m->count_all_member_loan_applications();
    //     $pagination               = create_pagination('member/loans/loan_applications/pages', $total_rows, 50, 5, TRUE);
    //     $this->data['posts']      = $this->loan_applications_m->limit($pagination['limit'])->get_member_loan_applications();
    //     $this->data['pagination'] = $pagination;
    //     $this->data['member']     = $this->member;
    //     $this->data['loan_types'] = $this->loan_types_m->get_options();
    //     $this->template->title('Your Loan Applications')->build('member/loan_application_listing', $this->data);
    // }
    
    function signatory_approvals_listing()
    {
        $this->data['member']     = $this->member;
        $this->data['loan_types'] = $this->loan_types_m->get_options();
        $this->template->title('Your Loan Signatory Approvals')->build('member/loan_signatory_listing', $this->data);
    }
    
    function loan_request_listing()
    {
        $data = array();
        $this->template->title('Pending Loan Requests')->build('member/loan_requests', $data);
        
    }

    function signatory_approval($id = 0){
        $post  = new stdClass();
        $comment = $this->input->post('comment');
        $group_id = $this->group->id;
        $signatory_details = $this->loans_m->get_loan_signatories($id,$group_id);
        if($signatory_details){
            $loan_application = $this->loan_applications_m->get($signatory_details->loan_application_id);
            $account_id = $this->input->post('account_id');
            $this->form_validation->set_rules($this->signatory_validation);
            if($this->form_validation->run()){
                if($signatory_details){
                    if(isset($_POST['approve'])){
                        $data = array(
                            'is_approved'=>1,
                            'approve_comment'=>$comment,
                            'loan_signatory_progress_status'=>3,
                            'modified_on'=>time(),
                            'modified_by'=>$this->user->id
                        ); 
                        $input = array(
                            'account_id'=>$account_id,
                            'status'=>7,
                            'modified_by'=>$this->user->id,
                            'modified_on'=>time()
                        );
                        $action = 'approve';
                    }elseif (isset($_POST['decline'])) {
                        $data = array(
                            'is_declined'=>1,
                            'decline_comment'=>$comment,
                            'loan_signatory_progress_status'=>2,
                            'active'=>0,
                            'modified_on'=>time(),
                            'modified_by'=>$this->user->id
                        );
                        $input = array(
                            'account_id'=>$account_id,
                            'status'=>8,
                            'modified_by'=>$this->user->id,
                            'modified_on'=>time()
                        );
                        $action = 'decline';
                    }
                    if($this->loan_applications_m->update($signatory_details->loan_application_id,$input)){
                        if($this->loans_m->update_loan_signatories($id,$data)){
                            $progress_status = $this->loans_m->get_group_signatories_array($group_id , $signatory_details->loan_application_id);                           
                            $group_memebers = $this->members_m->get_group_members_array($group_id);
                            $signatory_array =   array(
                                'signatory_user_id'=>$group_memebers[$signatory_details->signatory_member_id]->user_id,
                                'signatory_member_id' => $group_memebers[$signatory_details->signatory_member_id]->id,
                                'first_name'=>$group_memebers[$signatory_details->signatory_member_id]->first_name,
                                'last_name'=>$group_memebers[$signatory_details->signatory_member_id]->last_name,
                                'signatory_phone_no'=>$group_memebers[$signatory_details->signatory_member_id]->phone,
                            );
                            $loan_applicant_array =  array(
                                'loan_applicant_user_id'=>$signatory_details->loan_request_applicant_user_id,
                                'loan_applicant_member_id' => $signatory_details->loan_request_member_id,
                                'first_name'=>$group_memebers[$signatory_details->loan_request_member_id]->first_name,
                                'last_name'=>$group_memebers[$signatory_details->loan_request_member_id]->last_name,
                                'phone_no' =>$group_memebers[$signatory_details->loan_request_member_id]->phone,
                            );
                            $loan_details_array =   array(
                                'loan_type_id'=>$loan_application->loan_type_id,
                                'loan_application_id' => $loan_application->id,
                                'loan_amount' => $loan_application->loan_amount,
                                'loan_request_status'=>$progress_status,
                                'group_id'=>$group_id,
                                'currency'=>$this->group_currency,
                                'loan_name'=>$loan_application->name,
                                'action'=>$action,
                                'repayment_period'=>$loan_application->repayment_period,
                                'loan_application_date'=>$loan_application->created_on,
                                'account_id'=>$account_id,
                            );
                            if($this->messaging->notify_loan_applicant_of_signatory_action($signatory_array,$loan_applicant_array,$loan_details_array)){
                                $this->session->set_flashdata('success','You have agreed to '.$action.'ed '.$group_memebers[$signatory_details->loan_request_member_id]->first_name.' '.$group_memebers[$signatory_details->loan_request_member_id]->last_name.' loan( '.$loan_application->name.') of  '.$this->group_currency.' '.number_to_currency($loan_application->loan_amount).' as a signatory of the group');
                                redirect('member/loans/signatory_approvals_listing');
                            }else{
                                $this->session->set_flashdata('error','could not notify loan applicant ');
                            }
                        }else{
                           $this->session->set_flashdata('error','could not update loan signatory details'); 
                       }
                    }else{
                        $this->session->set_flashdata('error','Loan application could not be edited');
                    }
                }else{
                    $this->session->set_flashdata('error','Loan application failed: Siganatory details missing');
                }
            }else{
                foreach ($this->signatory_validation as $key => $field) {
                    $field_value        = $field['field'];
                    $post->$field_value = set_value($field['field']);
                }
            }
            $this->data['post'] = $loan_application;
            $this->data['id'] = $id; 
            $this->data['account_numbers'] = $this->accounts_m->get_active_group_account_options($group_id);   
            $this->template->title('Loan Requests  ')->build('member/signatory_action',$this->data);
        }else{
            $this->session->set_flashdata('error','Loan application failed: Siganatory details missing');
            redirect('member');
        }
    }
    function signatory_approved_loans($id){
        //if signatory not available redirect to pending loans
        $post = new stdClass();
        $this->data['post'] = $post;        
        $this->data['loan_application_id'] = $loan_application_id;
        $this->template->title('Loan Listing  Requests')->build('member/signatory_action',$this->data);


    }
    
    function ajax_loan_requests_listing()
    {
        $loan_type_options           = $this->loan_types_m->get_all();
        $user_id                     = $this->user->id;
        $member_id                   = $this->member->id;
        $group_id                    = $this->group->id;
        $count                       = 0;
        $get_loan_application_option = $this->loan_applications_m->get_member_loan_applications($group_id, $member_id);        
        if (!empty($get_loan_application_option)){    
            echo '
            <table class="table table-condensed table-striped table-hover table-header-fixed ">
                <thead>
                    <tr>
                        <th width="2%">
                             <span><input name="check" value="all" class="check_all" type="checkbox"></span>
                        </th>
                        <th width="2%">
                            #
                        </th>
                        <th>
                            Loan Details
                        </th>
                        <th class="text-right">
                            Amount ('.$this->group_currency.')
                        </th>';
                        if($this->group->id == 3912){ 
                           echo '
                            <th>
                                Actions
                            </th>';
                        } 
                    echo '</tr>
                </thead>
                <tbody>';
                foreach ($get_loan_application_option as $key => $get_loan_application_details) {
                    $loan_type_id = $get_loan_application_details->loan_type_id;
                    $loan_type_options = $this->loan_types_m->get($loan_type_id);
                    $count++;
                    $loan_type_name                     = $loan_type_options->name;
                    $loan_application_id                = $get_loan_application_details->id;
                    $member_id                          = $get_loan_application_details->member_id;
                    $active                             = $get_loan_application_details->active;
                    $loan_amount                        = $get_loan_application_details->loan_amount;
                    $get_loan_applicant_user_details    = $this->users_m->get($user_id);
                    $get_loan_type_application_requests = $this->loans_m->get_member_request_loans_application($loan_type_id, $loan_application_id, $group_id, $member_id);
                    echo '
                    <tr> 
                        <td><span>
                            <input type="checkbox" name="check" value="all" class="check_all"></span>
                        </td>
                        <td>'.$count.' '.$get_loan_application_details->id.'</td>
                        <td>
                            <strong> Loan Name : </strong>'.$loan_type_name.'<br>
                            <strong> Member Name : </strong>'.$get_loan_applicant_user_details->first_name . ' ' . $get_loan_applicant_user_details->last_name.'<br>
                            <strong> Loan Duration : </strong> <span>';
                            if($loan_type_options->loan_repayment_period_type == 1){
                                echo $loan_type_options->fixed_repayment_period.' Months <br>';
                            }else if ($loan_type_options->loan_repayment_period_type == 2) {
                                echo $loan_type_options->minimum_repayment_period.' - '.$loan_type_options->maximum_repayment_period.' Months <br>';
                            }
                            echo '</span>
                            <strong> Loan Request  Status: </strong>';
                            if ($active == 1) {
                                echo '<span class="label label-success">In Progress</span>';
                            } else {
                                echo '<span class="label label-danger"> loan Declined</span>';
                            }
                        echo '
                        </td>
                        <td class="text-right">
                            '.number_to_currency($loan_amount).'
                        </td>';

                        if($this->group->id == 3912){ 
                           echo '
                            <td class="text-right">
                                <a href="'.site_url('member/loans/view_loan_application/'.$get_loan_application_details->id).'" class="btn btn-xs btn-success">
                                    <i class="icon-eye"></i> View &nbsp;&nbsp; 
                                </a>
                            </td>';
                        }                        
                    echo '</tr>';
                }
                echo '
                </tbody>
            </table>';
        }else{
            echo '
            <div class="alert alert-info">
                <h4 class="block">Information! No records to display</h4>
                <p>
                    No Loan Requests to display.
                </p>
            </div>';
        }
        
    }

    function ajax_pending_loan_requests_listing(){

        $loan_type_options           = $this->loan_types_m->get_all();
        $user_id                     = $this->user->id;
        $member_id                   = $this->member->id;
        $group_id                    = $this->group->id;
        $count                       = 0;
        $get_loan_application_option = $this->loan_applications_m->get_member_loan_applications($group_id, $member_id);        
        if (!empty($get_loan_application_option)){    
            echo '
            <table class="table table-condensed table-striped table-hover table-header-fixed ">
                <thead>
                    <tr>
                        <th width="2%">
                             <span><input name="check" value="all" class="check_all" type="checkbox"></span>
                        </th>
                        <th width="2%">
                            #
                        </th>
                        <th>
                            Loan Details
                        </th>
                        <th class="text-right">
                            Amount ('.$this->group_currency.')
                        </th>
                    </tr>
                </thead>
                <tbody>';
                foreach ($get_loan_application_option as $key => $get_loan_application_details) {
                    $loan_type_id = $get_loan_application_details->loan_type_id;
                    $loan_type_options = $this->loan_types_m->get($loan_type_id);
                    $count++;
                    $loan_type_name                     = $loan_type_options->name;
                    $loan_application_id                = $get_loan_application_details->id;
                    $member_id                          = $get_loan_application_details->member_id;
                    $active                             = $get_loan_application_details->active;
                    $loan_amount                        = $get_loan_application_details->loan_amount;
                    $get_loan_applicant_user_details    = $this->users_m->get($user_id);
                    $get_loan_type_application_requests = $this->loans_m->get_member_request_loans_application($loan_type_id, $loan_application_id, $group_id, $member_id);
                    echo '
                    <tr> 
                        <td><span>
                            <input type="checkbox" name="check" value="all" class="check_all"></span>
                        </td>
                        <td>'.$count.'</td>
                        <td>
                            <strong> Loan Name : </strong>'.$loan_type_name.'<br>
                            <strong> Member Name : </strong>'.$get_loan_applicant_user_details->first_name . ' ' . $get_loan_applicant_user_details->last_name.'<br>
                            <strong> Loan Duration : </strong> <span>';
                            if($loan_type_options->loan_repayment_period_type == 1){
                                echo $loan_type_options->fixed_repayment_period.' Months <br>';
                            }else if ($loan_type_options->loan_repayment_period_type == 2) {
                                echo $loan_type_options->minimum_repayment_period.' - '.$loan_type_options->maximum_repayment_period.' Months <br>';
                            }
                            echo '</span>
                            <strong> Loan Request  Status: </strong>';
                            if ($active == 1) {
                                echo '<span class="label label-success">In Progress</span>';
                            } else {
                                echo '<span class="label label-danger"> loan Declined</span>';
                            }
                        echo '
                        </td>
                        <td class="text-right">
                            '.number_to_currency($loan_amount).'
                        </td>
                    </tr>';
                }
                echo '
                </tbody>
            </table>';
        }else{
            echo '
            <div class="alert alert-info">
                <h4 class="block">Information! No records to display</h4>
                <p>
                    No Loan Requests to display.
                </p>
            </div>';
        }
    }
    
    function ajax_loan_signatory_listing()
    {
        $loan_type_options         = $this->loan_types_m->get_all();
        $user_id                   = $this->user->id;
        $member_id                 = $this->member->id;
        $group_id                  = $this->group->id;
        $count                     = 0;
        $signatory_details = $this->loans_m->get_member_loan_signatory_options_array($group_id);  
        $group_members = $this->members_m->get_group_members_array($this->group->id);     
        if (!empty($signatory_details)) {
        echo '<table class="table table-condensed table-striped table-hover table-header-fixed ">
                <thead>
                    <tr>
                        <th width="2%">
                            #
                        </th>
                        <th>
                            Loan Details
                        </th>
                        <th class="text-right">
                            Amount (KES)
                        </th>
                    </tr>
                </thead>';
                foreach ($signatory_details as $key => $signatory_detail) {
                    if($signatory_detail->signatory_member_id){
                        $commitee_member_id = $signatory_detail->signatory_member_id;
                        $progress_status = $signatory_detail->loan_signatory_progress_status;
                    }else if($signatory_detail->commitee_member_id){
                        $commitee_member_id = $signatory_detail->commitee_member_id;
                        $progress_status = $signatory_detail->committee_progress_status; 
                    }
                    $signatory_id      = $signatory_detail->id;
                    $loan_type_id      = $signatory_detail->loan_type_id;
                    $loan_type_options = $this->loan_types_m->get($loan_type_id);                   
                    $count++;
                    $loan_type_name  = $loan_type_options->name;
                    $loan_application_id                = $signatory_detail->id;
                    $loan_applicant_user_id             = $signatory_detail->loan_request_applicant_user_id;
                    $member_id                          = $signatory_detail->loan_request_member_id;
                    $active                             = $signatory_detail->active;
                    $loan_amount                        = $signatory_detail->loan_amount;
                    //$loan_signatory_progress_status     = $progress_status;
                    //$get_loan_applicant_user_details    = $this->users_m->get($loan_applicant_user_id);
                    $get_loan_type_application_requests = $this->loans_m->get_member_request_loans_application($loan_type_id, $loan_application_id, $group_id, $member_id);
                    echo '<tr>
                           <td>';
                               $count;
                       echo '</td>
                           <td>
                           <strong> Loan name : </strong>';
                               echo $loan_type_name;
                        echo '<br>
                            <strong>  Applicant name : </strong> ';
                               echo $group_members[$member_id]->first_name . ' ' . $group_members[$member_id]->last_name;
                         echo'<br>
                            <strong> Your response to group signatory request: </strong>';
                            if ($progress_status == 3) {
                               echo '<span class="label label-success">Approved</span>';
                            } else if ($progress_status == 2) { 
                                echo '<span class="label label-danger">Declined</span>';
                            } else if ($progress_status == 1) { 
                                echo '<span class="label label-warning">Pending</span>';
                            } else if($progress_status == 4) { 
                                echo '<span class="label label-danger"> Deffered</span>';
                            }                               
                        echo '</td>
                        <td class="text-right">';
                          number_to_currency($loan_amount);
                        echo'</td>
                      </tr>';
                }
                echo '<tbody>';
            } else {
                echo '
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No Loan Requests to display.
                        </p>
                    </div>';
                }
    }
    
    function apply(){
        $post = new stdClass();
        $loan_applicant_id = $this->user->id;
        $guaranteed_amounts = $this->input->post('guaranteed_amount');
        $loan_type_id= $this->input->post('loan_type_id');
        $loan_application_amount= currency($this->input->post('loan_application_amount'));
        $guarantor_ids= $this->input->post('guarantor_id');
        $repayment_period_from_form= $this->input->post('repayment_period');
        $guarantor_comment = $this->input->post('guarantor_comment');
        $loan_applicant_agree_to_rules = $this->input->post('loan_rules_check_box');
        $member_id = $this->member->id;
        $user_id = $this->user->id;
        $currency = $this->group_currency;
        $this->form_validation->set_rules($this->loan_application_rules);
        if ($this->form_validation->run()){
            $loan_type = $this->loan_types_m->get($loan_type_id,$group_id);
            if($loan_type){
                $data = array(
                    'member_id'=>$member_id,
                    'group_id'=>$group_id,
                    'loan_type_id'=>$loan_type_id,
                    'loan_amount'=>$loan_application_amount,
                    'active'=>1,
                    'status'=>2,
                    'agree_to_rules'=>$loan_applicant_agree_to_rules,
                    'created_on'=>time(),
                    'created_by'=>$this->user->id
                );
                if($loan_type->loan_repayment_period_type == 1){
                    $data = $data + array(
                        'repayment_period'=>$loan_type->fixed_repayment_period,
                    );
                }else if($loan_type->loan_repayment_period_type == 1){
                   $data = $data + array(
                        'repayment_period'=>$repayment_period_from_form,
                    );  
                }
                $loan_application_id = $this->loan_applications_m->insert($data);
                if($loan_application_id){
                    if($loan_type->enable_loan_guarantors == 1){ 
                        foreach ($guarantor_ids as $key => $guarantor_id) {
                            if($guarantor_id && currency($guaranteed_amounts[$key])){
                                $guarantor_details =  array(
                                    'loan_type_id'=>$loan_type_id,
                                    'loan_application_id'=>$loan_application_id,
                                    'loan_request_applicant_user_id'=>$this->user->id,
                                    'loan_request_applicant_member_id'=>$member_id,
                                    'guarantor_member_id'=>$guarantor_id,
                                    'group_id'=>$group_id,
                                    'active'=>1,
                                    'amount'=>$guaranteed_amounts[$key],                                        
                                    'loan_request_progress_status'=>1,
                                    'comment'=>$guarantor_comment[$key],
                                    'created_on'=>time(),
                                    'created_by'=>$this->user->id
                                );
                                $loan_application_guarantors_request_id = $this->loans_m->insert_loan_guarantorship_requests($guarantor_details);
                                if($loan_application_guarantors_request_id){
                                    $guarantor_member_details = $this->members_m->get($guarantor_id);
                                    $guarantor_user_details = $this->users_m->get($guarantor_member_details->user_id);
                                    if($guarantor_user_details){
                                        $guarantor_array =   array(
                                            'guarantor_user_id'=>$guarantor_member_details->user_id,
                                            'guarantor_member_id' => $guarantor_id,
                                            'first_name'=>$guarantor_user_details->first_name,
                                            'last_name'=>$guarantor_user_details->last_name,
                                            'guarantor_amount' => $guaranteed_amounts[$key],
                                            'guarantor_phone_no'=>$guarantor_user_details->phone,
                                        );
                                    }
                                    $get_loan_applicant_details = $this->users_m->get($user_id);
                                    if($get_loan_applicant_details){
                                        $applicant_array =  array(
                                            'loan_applicant_user_id'=>$user_id,
                                            'loan_applicant_member_id' => $member_id,
                                            'first_name'=>$get_loan_applicant_details->first_name,
                                            'last_name'=>$get_loan_applicant_details->last_name,
                                        );
                                    }
                                    $loan_details_array =   array(
                                        'loan_type_id'=>$loan_type_id,
                                        'loan_application_id' => $loan_application_id,
                                        'loan_amount' => $loan_application_amount,
                                        'group_id'=>$group_id,
                                        'currency'=>$currency,
                                        'loan_name'=>$loan_type->name,
                                    );
                                    $messaging = $this->messaging->notify_guarantor_about_loan_application_request($loan_details_array,$applicant_array,$guarantor_array);                              
                                }else{
                                    $this->session->set_flashdata('error','Loan application failed: could not create loan request details for guarantor');
                                }                               
                            }
                        } 
                        $this->session->set_flashdata('success',"Loan application submitted. An approval request has been sent to all your guarantors, kindly await their responses to this loan application.");
                                redirect('member/loans/pending_loan_request_listing');
                    }else{
                        $loan_application_particulars = array(
                            'loan_type_id'=>$loan_type_id,
                            'loan_application_id'=>$loan_application_id,
                            'group_id'=>$group_id,
                            'loan_applicant_member_id'=>$member_id,
                            'loan_type_name'=>$loan_type->name,
                            'currency'=>$currency,
                            'loan_application_amount'=>$loan_application_amount,
                            'loan_applicant_user_id'=>$this->user->id,
                        );
                        if($this->messaging->notify_signatories_of_loan_request_without_guarantors($loan_application_particulars)){
                            $this->session->set_flashdata('success',"Loan application submitted. An approval request has been sent to all group signatories, kindly await their responses to this loan application.");
                            redirect('member/loans/pending_loan_request_listing');
                        }else{
                          $this->session->set_flashdata('error','Loan application could not be saved');  
                        }
                    }
                }else{
                    $this->session->set_flashdata('error','Loan application could not be saved');
                }                
            }else{
                $this->session->set_flashdata('error',"Could not get loan type details");
            }  
        }else{
            foreach ($this->loan_application_rules as $key => $field) {
                $field_value        = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $group_loan_types_options = $this->loan_types_m->get_options($this->group->id);
        $admin_loan_types = $this->loan_types_m->get_admin_loan_types_for_group($this->group->id);
        $this->data['group_loan_types_options'] = $group_loan_types_options;
        $loan_types = $this->loan_types_m->get_all();
        $loan_types = $admin_loan_types?array_merge($loan_types,$admin_loan_types):$loan_types;
        $this->data['loan_types'] = $loan_types;
        $this->data['interest_types'] = $this->loan->interest_types;
        $this->data['loan_interest_rate_per'] = $this->loan->loan_interest_rate_per;
        $this->data['active_accounts'] = $this->accounts_m->get_active_group_account_options();
        $this->data['post'] = $post;
        $this->template->title(translate('Apply For Loan'))->build('member/apply',$this->data);
    }
    

    
    function loan_guarantorship_requests($id = 1){
        $this->data['loan_guarantorship_request'] = new stdClass();
        $this->template->title('Loan Guarantorship Requests')->build('member/loan_guarantorship_requests', $this->data);
    }

    function pending_loan_request_listing(){
        $data = array();
        $this->template->title('Pending Loan Application Requests')->build('member/pending_loan_application_listing.php', $data);
    }


    /* -- Eazzy Club loan Apllication --*/
    /*
        loan application status 
        loan application table 
        status 
            2 = application in progress
            3 = loan approved  
            4 = loan declined
        // ---------- //
        loan guarantors progress status
            1 = loan approval pending
            2 = Guarantor decline loan request
            3 = Guarantor approve loan request
        loan supervisor  options
            Performance 
               1 => Yes
               2 => NO,
               3 => ANY
            Disciplinary 
               1 => Yes
               2 =>  NO
        HR appraisals
            terms_of_employment 
            1 => permanent
            2 => contract
        Siganatory / sacco committee
            1 => pending
            2 => decline
            3 => approve
            4 => deffered

    */

    function _valid_loan_application_details(){
        $loan_type_id  = $this->input->post('loan_type_id');
        $guarantor_ids  = $this->input->post('guarantor_id');
        $guarantor_amounts  = $this->input->post('guaranteed_amount');
        $loan_application_amount = currency($this->input->post('loan_application_amount'));
        $repayment_period = $this->input->post('repayment_period');
        if($loan_type_id){
            if($loan_type = $this->loan_types_m->get($loan_type_id)){
                $selected_guarantors = 0;
                $selected_guarantors_array = array();
                $selected_guaranteed_amount = 0;
                $guarantor_input_entries_valid = TRUE;
                $data['contribution_options'] = $this->contributions_m->get_group_refundable_contribution_options();        
                $member_savings = $this->reports_m->get_group_member_total_contributions($this->member->id,$data['contribution_options']);
                if($guarantor_ids){                    
                    foreach ($guarantor_ids as $key => $guarantor_id) {
                        if($guarantor_id){
                            $group_active_loans = $this->loans_m->count_all_group_active_member_loan_option($this->group->id ,$guarantor_id);    
                            if($guarantor_id == $this->member->id){
                                $this->form_validation->set_message('_valid_loan_application_details','You cannot select yourself as a guarantor');
                                return FALSE;
                                break;
                            }else{        
                                $guarantor_savings = $this->reports_m->get_group_member_total_contributions($guarantor_id,$data['contribution_options']);
                                if($guarantor_savings){
                                    if(array_key_exists($guarantor_id, $group_active_loans)){
                                        $guarantor_active_loans = $group_active_loans[$guarantor_id];
                                    }else{
                                        $guarantor_active_loans = 0;
                                    }
                                    if($guarantor_active_loans > 3){
                                        $this->form_validation->set_message('_valid_loan_application_details',$this->active_group_member_options[$guarantor_id].' has three active loans');
                                        return FALSE;
                                        break;
                                    }else{
                                        $maximum_amount_to_grant = currency($guarantor_savings*($loan_type->loan_times_number?:3));
                                        if($guarantor_amount = currency(isset($guarantor_amounts[$key])?currency($guarantor_amounts[$key]):0)){
                                            if($guarantor_amount > $maximum_amount_to_grant){
                                                $this->form_validation->set_message('_valid_loan_application_details',$this->active_group_member_options[$guarantor_id].' can not guarantee that much ');
                                                return FALSE;
                                                break;
                                            }else{
                                                if(in_array($guarantor_id, $selected_guarantors_array)){
                                                    $this->form_validation->set_message('_valid_loan_application_details',$this->active_group_member_options[$guarantor_id].' has been selected more than once ');
                                                    return FALSE;
                                                    break;
                                                } 
                                                $selected_guaranteed_amount+=$guarantor_amount;
                                                ++$selected_guarantors;
                                                $selected_guarantors_array[]=$guarantor_id; 
                                            }
                                        }else{
                                            $this->form_validation->set_message('_valid_loan_application_details','You cannot select a guarantor without selecting amount');
                                            return FALSE;
                                            break;
                                        }
                                    }
                                }
                            }
                        }else{
                            //$this->form_validation->set_message('_valid_loan_application_details','kindly select atleast one guarantor');
                            //return FALSE;
                        }
                    }
                    if($selected_guarantors  >= 2){
                        $supervisor_id = $this->input->post('supervisor_id');
                        if($supervisor_id){
                            if($supervisor_id == $this->member->id){
                                $this->form_validation->set_message('_valid_loan_application_details','You cannot select yourself as a supervisor');
                                return FALSE;
                            }else{
                                if($loan_type->loan_amount_type == 1){//range
                                    if($loan_application_amount < $loan_type->minimum_loan_amount){
                                        $this->form_validation->set_message('_valid_loan_application_details','Amount applied is below the required minimum amount');
                                        return FALSE;
                                    }elseif($loan_application_amount > $loan_type->maximum_loan_amount){
                                        $this->form_validation->set_message('_valid_loan_application_details','Amount applied is above the required maximum amount');
                                        return FALSE;
                                    }
                                }else if($loan_type->loan_amount_type == 2){
                                    //member savings
                                   // $data['contribution_options'] = $this->contributions_m->get_group_refundable_contribution_options();        
                                    $member_savings = $this->reports_m->get_group_member_total_contributions($this->member->id,$data['contribution_options']);
                                    //$member_savings = $this->transactions->get_group_member_savings($this->group->id,$this->member->id);
                                    $maximum_allowed_loan = currency($member_savings * $loan_type->loan_times_number);
                                    $amount_above_savings = currency($loan_application_amount - $maximum_allowed_loan);
                                    if($loan_application_amount > $maximum_allowed_loan){
                                        $this->form_validation->set_message('_valid_loan_application_details','Loan applied is above '.$loan_type->loan_times_number.' times your savings and your savings is '.$this->group_currency.' '. number_to_currency($member_savings));
                                        return FALSE;
                                    }else{
                                        //nothing for now
                                    }
                                }else{
                                    $this->form_validation->set_message('_valid_loan_application_details','Invalid loan type selected');
                                    return FALSE;
                                }                              
                            }
                        }else{
                            $this->form_validation->set_message('_valid_loan_application_details','Kindly select a supervisor for your loan ');
                            return FALSE; 
                        }
                    }else{
                        $this->form_validation->set_message('_valid_loan_application_details','Kindly select atleast 2 guarantors you have selected '.$selected_guarantors.' guarantors');
                        return FALSE; 
                    }
                }
            }else{
                $this->form_validation->set_message('_valid_loan_application_details','Invalid loan type selected');
                return FALSE;
            }
        }else{
            $this->form_validation->set_message('_valid_loan_application_details','Kindly select a loan type');
            return FALSE;
        }
    }

    function _check_if_oranisational_roles_exist(){
        $member_role_holders = $this->members_m->get_active_organizational_role_holder_options($this->group->id);
        if($member_role_holders){ 
            return TRUE;
        }else{
            $this->form_validation->set_message('_check_if_oranisational_roles_exist','Group does not have organizational  role holders contact group admin to assign them'); 
            return FALSE;
        }
    }

    function _check_if_sacco_officer_exist(){
        $member_role_holders = $this->members_m->get_active_organizational_role_holder_options($this->group->id);
        if($member_role_holders){ 
            if(array_key_exists(2, $member_role_holders)){
                return TRUE;
            }else{
                $this->form_validation->set_message('_check_if_sacco_officer_exist','Group does not have sacco officer contact group admin to assign the role'); 
                return FALSE;
            }            
        }else{
            $this->form_validation->set_message('_check_if_sacco_officer_exist','Group does not have organizational  role holders contact group admin to assign the role'); 
            return FALSE;
        }
    }

    function _check_if_credit_committee_member_exist(){
        $member_role_holders = $this->members_m->get_active_organizational_role_holder_options($this->group->id);
        if($member_role_holders){ 
            if(array_key_exists(3, $member_role_holders)){
                return TRUE;
            }else{
                $this->form_validation->set_message('_check_if_credit_committee_member_exist','Group does not have credit committee member contact group admin to assign the role'); 
                return FALSE;
            }            
        }else{
            $this->form_validation->set_message('_check_if_credit_committee_member_exist','Group does not have organizational role holders contact group admin to assign the role'); 
            return FALSE;
        }
    }

    function _check_action_option(){
        $action_id = $this->input->post('action_id');
        $comment = $this->input->post('comment');
        if($action_id  == 2){
            if($comment){
                return TRUE;
            }else{
               $this->form_validation->set_message('_check_action_option','Please indicate  a comment for deferring this loan application '); 
                return FALSE; 
            }
        }else if($action_id  == 3){
           if($comment){
                return TRUE;
            }else{
               $this->form_validation->set_message('_check_action_option','Please indicate  a comment for rejecting this loan application '); 
                return FALSE; 
            } 
        }
        $member_role_holders = $this->members_m->get_active_organizational_role_holder_options($this->group->id);
        if($member_role_holders){ 
            if(array_key_exists(4, $member_role_holders)){
                return TRUE;
            }else{
                $this->form_validation->set_message('_check_action_option','Group does not have sacco manager please contact group admin to assign the role'); 
                return FALSE;
            }            
        }
    }

    function _check_loan_values_are_correct(){
        $is_loan_exisiting = $this->input->post('is_loan_exisiting');
        $loan_amounts = $this->input->post('loan_amount');
        $loan_amount_installments = $this->input->post('loan_amount_installments');
        $percentage_net_pay = $this->input->post('percentage_net_pay');
        $net_pay = $this->input->post('net_pay');
        $repayment_period = $this->input->post('repayment_period');
        $loan_balance = $this->input->post('loan_balance');  
        if($is_loan_exisiting == 1){ //exist
            $loan_values = TRUE;
            foreach ($loan_amounts as $key => $loan_amount):
                if($loan_amount){
                    if($loan_amount_installments[$key]){
                        if($repayment_period[$key]){
                            if($loan_balance[$key]){
                               //
                            }else{
                                $loan_values = FALSE;
                                $this->form_validation->set_message('_check_loan_values_are_correct','Loan balance amount is required'); 
                                return FALSE;
                                break;
                            }   
                        }else{
                            $loan_values = FALSE;
                            $this->form_validation->set_message('_check_loan_values_are_correct','Loan repayment period is required'); 
                            return FALSE;
                            break;
                        }
                    }else{
                        $loan_values = FALSE;
                        $this->form_validation->set_message('_check_loan_values_are_correct','Loan installments amount is required'); 
                        return FALSE;
                        break;
                    }
                }else{
                    $this->form_validation->set_message('_check_loan_values_are_correct','Loan amount is required'); 
                    return FALSE;
                    break;
                }
            endforeach;
            if($loan_values){
                if($net_pay){
                    if($percentage_net_pay){
                        return TRUE;
                    }else{
                        $this->form_validation->set_message('_check_loan_values_are_correct','Percentage net pay is required'); 
                        return FALSE;    
                    }
                }else{
                    $this->form_validation->set_message('_check_loan_values_are_correct','Net pay is required'); 
                    return FALSE;  
                } 
            }else{

            }

        }else if($is_loan_exisiting == 2){
            //save as is 
        }
    }

    function eazzy_club_apply_loan(){
        $post = new stdClass();
        $group_id= $this->group->id;
        $loan_applicant_id = $this->user->id;
        $guaranteed_amounts = $this->input->post('guaranteed_amount');
        $loan_type_id= $this->input->post('loan_type_id');
        $loan_application_amount= currency($this->input->post('loan_application_amount'));
        $guarantor_ids= $this->input->post('guarantor_id');
        $repayment_period_from_form= $this->input->post('repayment_period');
        $guarantor_comment = $this->input->post('guarantor_comment');
        $loan_applicant_agree_to_rules = $this->input->post('loan_rules_check_box');
        $supervisor_id = $this->input->post('supervisor_id');
        $member_id = $this->member->id;
        $user_id = $this->user->id;
        $currency = $this->group_currency;
        $this->form_validation->set_rules($this->eazzy_club_loan_application_rules);
        if($this->form_validation->run()){
            $loan_type = $this->loan_types_m->get($loan_type_id,$group_id);
            if($loan_type){
                $data = array(
                    'member_id'=>$member_id,
                    'group_id'=>$group_id,
                    'loan_type_id'=>$loan_type_id,
                    'loan_amount'=>$loan_application_amount,
                    'member_supervisor_id'=>$supervisor_id,
                    'active'=>1,
                    'status'=>2,
                    'agree_to_rules'=>$loan_applicant_agree_to_rules,
                    'created_on'=>time(),
                    'created_by'=>$this->user->id
                );
                if($loan_type->loan_repayment_period_type == 1){
                    $data = $data + array(
                        'repayment_period'=>$loan_type->fixed_repayment_period,
                    );
                }else if($loan_type->loan_repayment_period_type == 1){
                   $data = $data + array(
                        'repayment_period'=>$repayment_period_from_form,
                    );  
                }
                $loan_application_id = $this->loan_applications_m->insert($data);
                if($loan_application_id){
                    if($loan_type->enable_loan_guarantors == 1){ 
                        foreach ($guarantor_ids as $key => $guarantor_id) {
                            if($guarantor_id && currency($guaranteed_amounts[$key])){
                                $guarantor_details =  array(
                                    'loan_type_id'=>$loan_type_id,
                                    'loan_application_id'=>$loan_application_id,
                                    'loan_request_applicant_user_id'=>$this->user->id,
                                    'loan_request_applicant_member_id'=>$member_id,
                                    'guarantor_member_id'=>$guarantor_id,
                                    'group_id'=>$group_id,
                                    'active'=>1,
                                    'amount'=>$guaranteed_amounts[$key],                                        
                                    'loan_request_progress_status'=>1,
                                    'comment'=>$guarantor_comment[$key],
                                    'created_on'=>time(),
                                    'created_by'=>$this->user->id
                                );
                                $loan_application_guarantors_request_id = $this->loans_m->insert_loan_guarantorship_requests($guarantor_details);
                                if($loan_application_guarantors_request_id){
                                    $guarantor_member_details = $this->members_m->get($guarantor_id);
                                    $guarantor_user_details = $this->users_m->get($guarantor_member_details->user_id);
                                    if($guarantor_user_details){
                                        $guarantor_array =   array(
                                            'guarantor_user_id'=>$guarantor_member_details->user_id,
                                            'guarantor_member_id' => $guarantor_id,
                                            'first_name'=>$guarantor_user_details->first_name,
                                            'last_name'=>$guarantor_user_details->last_name,
                                            'guarantor_amount' => $guaranteed_amounts[$key],
                                            'guarantor_phone_no'=>$guarantor_user_details->phone,
                                        );
                                    }
                                    $get_loan_applicant_details = $this->users_m->get($user_id);
                                    if($get_loan_applicant_details){
                                        $applicant_array =  array(
                                            'loan_applicant_user_id'=>$user_id,
                                            'loan_applicant_member_id' => $member_id,
                                            'first_name'=>$get_loan_applicant_details->first_name,
                                            'last_name'=>$get_loan_applicant_details->last_name,
                                        );
                                    }
                                    $loan_details_array =   array(
                                        'loan_type_id'=>$loan_type_id,
                                        'loan_application_id' => $loan_application_id,
                                        'loan_amount' => $loan_application_amount,
                                        'group_id'=>$group_id,
                                        'currency'=>$currency,
                                        'loan_name'=>$loan_type->name,
                                    );
                                    $messaging = $this->messaging->eazzy_club_notify_guarantor_about_loan_application_request($loan_details_array,$applicant_array,$guarantor_array);                              
                                }else{
                                    $this->session->set_flashdata('error','Loan application failed: could not create loan request details for guarantor');
                                }                               
                            }
                        } 
                        $this->session->set_flashdata('success',"Loan application submitted. An approval request has been sent to all your guarantors, kindly await their responses to this loan application.");
                                redirect('member/loans/loan_request_listing');
                    }else{
                        // else notify supervisor
                        $this->session->set_flashdata('error',"Loan application requires atleast 2 guarantors.");                    
                    }
                }else{
                    $this->session->set_flashdata('error','Loan application could not be saved');
                } 
            }else{
                $this->session->set_flashdata('error',"Could not get loan type details");
            }
        }else{
            foreach ($this->eazzy_club_loan_application_rules as $key => $field) {
                $field_value        = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $this->data['group_loan_types_options'] = $this->loan_types_m->get_options($group_id);
        $this->data['active_accounts'] = $this->accounts_m->get_active_group_account_options();
        $this->data['post'] = $post;
        $this->template->title('Apply For Loan')->build('shared/equity_sacco_apply',$this->data);

    }    

    function view_loan_application($id = 0, $generate_pdf = FALSE){
        $id OR redirect('member/loans/pending_loan_request_listing');
        $loan_application = $this->loan_applications_m->get($id);

        $loan_type = $this->loan_types_m->get($loan_application->loan_type_id); 

        $posts = $this->loans_m->get_group_loan_gurantorship_request_array($loan_application->group_id,$loan_type->id,$id);

        $this->data['supervisor_recommendations'] = $this->loans_m->get_group_loan_supervisor_request_array($loan_application->group_id,$loan_type->id,$id);

        $this->data['guarantor_progress'] = $this->loans_m->get_group_loan_gurantorship_progress_status_request_array($loan_application->group_id , $loan_application->loan_type_id , $id); 

        $hr_appraisals = $this->loans_m->get_group_loan_hr_appraisal_request_array($loan_application->group_id , $loan_application->loan_type_id , $id); 
            foreach ($hr_appraisals as $key => $post) {
                
                $this->data['existing_loans'][$post->loan_application_id] = $this->loans_m->get_group_loan_hr_appraisal_requests($this->group->id ,$post->loan_type_id, $post->loan_application_id);
            } 

        $this->data['hr_appraisals'] = $hr_appraisals; 

        $this->data['sacco_appraisals'] = $this->loans_m->get_group_loan_sacco_officer_appraisal_request_array($loan_application->group_id , $loan_application->loan_type_id , $id);

        $this->data['committee_decisions'] = $this->loans_m->get_group_loan_committe_decision_request_array($loan_application->group_id , $loan_application->loan_type_id , $id);

        $this->data['contribution_options'] = $this->contributions_m->get_group_refundable_contribution_options();        
        $this->data['savings'] = $this->reports_m->get_group_member_total_contributions($this->member->id , $this->data['contribution_options']);

        $this->data['loan_values'] = $this->loan->calculate_loan_balance_invoice($loan_application->loan_amount,$loan_type->interest_type,$loan_type->interest_rate, $loan_type->fixed_repayment_period,'',time(),$loan_type->loan_interest_rate_per); 
        $installments_many = array();
        $loans  = $this->loans_m->get_member_loans($this->group->id,$loan_application->member_id);
        $total_installment = 0;
        $total_amount_payable= 0;
        $total_amount_paid =0;
        foreach ($loans as $key => $loan) {
            $installments = $this->loan_invoices_m->get_loan_installments($loan->id);
            foreach ($installments as $key => $installment) {
                //print_r($installment); 
                $get_loan[] = $this->loans_m->get($installment->loan_id);
                $monthly_installment[$installment->loan_id] = $installment->amount_payable;
                $total_amount_payable += $installment->amount_payable;
                $total_amount_paid += $installment->amount_paid;
            } 
            $balance[$loan->id] = $total_amount_payable - $total_amount_paid;
            $installments_many[$loan->id] = $installments;           
        }
        $this->data['balance'] = $balance;
        $this->data['member_loans'] = $loans;
        $this->data['posts'] = $posts;
        $this->data['monthly_installment'] = $monthly_installment;
        $this->data['installments_many'] = $installments_many;
        $this->data['loan_application'] = $loan_application;
        $this->data['loan_type'] = $loan_type; 
        $this->data['active_accounts'] = $this->accounts_m->get_active_group_account_options();
        $this->data['active_group_member_options'] = $this->active_group_member_options;
        $this->data['group_member_options'] = $this->group_member_options;
        $this->data['group_members_array'] = $this->members_m->get_group_members_array($loan_application->group_id);
        $this->data['get_eazzy_sacco_active_group_role_holder_options'] = $this->members_m->get_eazzy_sacco_active_group_role_holder_options($loan_application->group_id);
        $this->data['group_role_options'] = $this->group_roles_m->get_group_role_options($loan_application->group_id);
        if($generate_pdf==TRUE){
            //print_r($this->data);
           //print_r((array)json_encode($this->data['installments_many'])); die();
            $this->data['member'] = array(
                'first_name'=>$this->member->first_name,
                'last_name' =>$this->member->last_name,
                'phone'=>$this->member->phone,
                'email'=>$this->member->email,
                'id'=> $this->member->id,
            );
           
            $this->data['group_currency'] = $this->group_currency;
            if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
                $this->data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
            }else{
                $this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
            }
            $response = $this->curl_post_data->curl_post_json_pdf((json_encode($this->data)),'https://pdfs.chamasoft.com/loan_form',$this->group->name.'Loan Application Form');
            print_r($response);die;
        }     
        $this->template->title('View Loan requests ')->build('shared/view_pending_loan_request', $this->data);
    }

    function view_member_loan_application($id = 0, $generate_pdf = FALSE){
        $id OR redirect('group/loans/pending_eazzyclub_member_loan');
        $loan_application = $this->loan_applications_m->get($id);

        $loan_type = $this->loan_types_m->get($loan_application->loan_type_id);
        $posts = $this->loans_m->get_group_loan_gurantorship_request_array($loan_application->group_id,$loan_type->id,$id);

        $this->data['supervisor_recommendations'] = $this->loans_m->get_group_loan_supervisor_request_array($loan_application->group_id,$loan_type->id,$id);

        $this->data['guarantor_progress'] = $this->loans_m->get_group_loan_gurantorship_progress_status_request_array($loan_application->group_id , $loan_application->loan_type_id , $id); 

        $hr_appraisals = $this->loans_m->get_group_loan_hr_appraisal_request_array($loan_application->group_id , $loan_application->loan_type_id , $id); 

            foreach ($hr_appraisals as $key => $post) {
                $this->data['existing_loans'][$post->loan_application_id] = $this->loans_m->get_group_loan_hr_appraisal_requests($this->group->id ,$post->loan_type_id, $post->loan_application_id);
            } 

            $sacco_appraisals_details = $this->loans_m->get_group_loan_sacco_officer_appraisal_request_array($loan_application->group_id , $loan_application->loan_type_id , $id);

            //$sacco_appraisals_details = $this->loans_m->get_member_sacco_officer_appraisals($this->group->id ,$loan_application->member_id);
            foreach ($sacco_appraisals_details as $key => $post) {
                $this->data['loan_values'][$post->loan_application_id] = $this->loan->calculate_loan_balance_invoice($loan_application->loan_amount,$loan_type->interest_type,$loan_type->interest_rate,$loan_type->fixed_repayment_period,'',time(),$loan_type->loan_interest_rate_per);
            }

            $this->data['sacco_appraisals'] =  $sacco_appraisals_details;
            //print_r($sacco_appraisals_details); die(); 

            

        $this->data['hr_appraisals'] = $hr_appraisals;  

        $this->data['sacco_appraisals_details'] = $sacco_appraisals_details;   

       

        $this->data['committee_decisions'] = $this->loans_m->get_group_loan_committe_decision_request_array($loan_application->group_id , $loan_application->loan_type_id , $id);

        $this->data['contribution_options'] = $this->contributions_m->get_group_refundable_contribution_options();        
        $this->data['savings'] = $this->reports_m->get_group_member_total_contributions($this->member->id , $this->data['contribution_options']);

       // $this->data['loan_values'] = $this->loan->calculate_loan_balance_invoice($loan_application->loan_amount,$loan_type->interest_type,$loan_type->interest_rate, $loan_type->fixed_repayment_period,'',time(),$loan_type->loan_interest_rate_per); 
        $installments = array();
        //$loans  = $this->loans_m->get_member_loans($this->group->id,$loan_application->member_id);
        $balance = array();
        $total_installment = 0;

        $this->data['balance'] = $balance;
       // $this->data['member_loans'] = $loans;
        $this->data['posts'] = $posts;
       // $this->data['get_loan'] = $get_loan;
        //$this->data['installments'] = $installments_many;
        $this->data['loan_application'] = $loan_application;
        $this->data['loan_type'] = $this->loan_types_m->get_option_objects_array($this->group->id);
        $this->data['loan_type_details'] = $loan_type;
        $this->data['active_accounts'] = $this->accounts_m->get_active_group_account_options();
        $this->data['active_group_member_options'] = $this->active_group_member_options;
        $this->data['group_member_options'] = $this->group_member_options;
        $this->data['group_loan_applications'] = $this->loan_applications_m->get_option_objects_array();
        $this->data['group_members_array'] = $this->members_m->get_group_members_array($loan_application->group_id);
        $this->data['get_eazzy_sacco_active_group_role_holder_options'] = $this->members_m->get_eazzy_sacco_active_group_role_holder_options($loan_application->group_id);
        $this->data['group_role_options'] = $this->group_roles_m->get_group_role_options($loan_application->group_id);
        if($generate_pdf==TRUE){
            $this->data['member'] = array(
                'first_name'=>$this->member->first_name,
                'last_name' =>$this->member->last_name,
                'phone'=>$this->member->phone,
                'email'=>$this->member->email,
                'id'=> $this->member->id,
            );
           
            $this->data['group_currency'] = $this->group_currency;
            if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
                $this->data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
            }else{
                $this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
            }
            $response = $this->curl_post_data->curl_post_json_pdf((json_encode($this->data)),'https://pdfs.chamasoft.com/loan_form',$this->group->name.'Loan Application Form');
            print_r($response);die;
        }     
        $this->template->title('View Loan requests ')->build('shared/view_group_pending_loan_request', $this->data);
    }

    function supervisor_recommendation($id = 0){
        $post = new stdClass();
        $loan_applications = $this->loan_applications_m->get($id);       
        $this->data['member_supervisor_id'] = $loan_applications->member_supervisor_id;
        $comment  = $this->input->post('comment');
        $performance_id  = $this->input->post('performance_id');
        $disciplinary_id  = $this->input->post('disciplinary_id');
        $stamp_date = $this->input->post('stamp_date');
        $is_approve = 0 ;
        $is_decline = 0;
        $this->form_validation->set_rules($this->supervisor_recommendation);
        if($this->form_validation->run()){
            if(isset($_POST['approve'])){
                $is_approve = 1;
            }else if(isset($_POST['decline'])){
                $is_decline = 1;
            }            
            $supervisor_enries =  array(
                'loan_type_id'=>$loan_applications->loan_type_id,
                'loan_application_id'=>$loan_applications->id,
                'loan_request_member_id'=>$loan_applications->member_id,
                'supervisor_member_id'=>$loan_applications->member_supervisor_id,
                'group_id'=>$loan_applications->group_id,
                'is_decline'=>$is_decline,
                'is_approve'=>$is_approve,
                'loan_amount'=>$loan_applications->loan_amount,
                'performance_management'=>$performance_id,
                'recommendation_date'=>strtotime($stamp_date),
                'disciplinary_case'=>$disciplinary_id,
                'comment'=>$comment,
                'created_on'=>time(),
                'created_by'=>$this->user->id,
            );
            //get group organisation role ids 
            $member_role_holders = $this->members_m->get_active_organizational_role_holder_options($loan_applications->group_id);
            if($this->loans_m->insert_supervisor($supervisor_enries)){
                //notify loan applicant of loan approval or decline
                if($this->messaging->notify_loan_applicant($supervisor_enries)){
                    if(isset($_POST['approve'])){
                        $action = "approved";
                    }else if(isset($_POST['decline'])){
                        $action = "declined"; 
                    }
                    $this->session->set_flashdata('info',' Loan request '.$action.' successfully');
                    redirect('member/loans/view_supervisor_recommendatios');
                }else{
                   $this->session->set_flashdata('warning','Could not notify loan applicant about supervisor recommendation'); 
                }
            }else{
                $this->session->set_flashdata('warning','Could not create supervisor records');
            }
        }else{
            foreach ($this->supervisor_recommendation as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $this->data['post'] = $post;        
        $this->data['loan_applications'] = $loan_applications;
        $this->data['readonly'] = "readonly='readonly'";
        $this->template->title('Supervisor Recommendations ')->build('shared/supervisor_recommendation', $this->data);  
    }

    function edit_supervisor_recommendation($id = 0){
        $supervisor_recommendations = $this->loans_m->get_supervisor_recommendations($id);
        $post = $this->loan_applications_m->get($supervisor_recommendations->loan_application_id);       
        $this->data['member_supervisor_id'] = $post->member_supervisor_id;
        $comment  = $this->input->post('comment');
        $performance_id  = $this->input->post('performance_id');
        $disciplinary_id  = $this->input->post('disciplinary_id');
        $stamp_date = $this->input->post('stamp_date');
        $this->form_validation->set_rules($this->supervisor_recommendation);
        if($this->form_validation->run()){
            $supervisor_enries =  array(
                'loan_type_id'=>$post->loan_type_id,
                'loan_application_id'=>$post->id,
                'loan_request_member_id'=>$post->member_id,
                'supervisor_member_id'=>$post->member_supervisor_id,
                'group_id'=>$post->group_id,
                'loan_amount'=>$post->loan_amount,
                'performance_management'=>$performance_id,
                'recommendation_date'=>strtotime($stamp_date),
                'disciplinary_case'=>$disciplinary_id,
                'comment'=>$comment,
            );
            if($this->loans_m->update_supervisory($id,$supervisor_enries)){
                $this->session->set_flashdata('success','Supervisor recomendations details edited succesfully');
                redirect('member/loans/view_supervisor_recommendatios');
            }else{
                $this->session->set_flashdata('warning','Could not create supervisor records');
            }
        }else{
            foreach ($this->supervisor_recommendation as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $this->data['post'] = $post; 
        $this->data['readonly'] = "readonly='readonly'";
        $this->template->title('Supervisor Recommendations ')->build('shared/supervisor_recommendation', $this->data);  

    }

    function view_supervisor_recommendatios(){
        $posts = $this->loans_m->get_member_supervisor_recommendations($this->group->id ,$this->member->id);
        $this->data['loan_application'] = $this->loan_applications_m->get_option_objects_array();
        $this->data['loan_type'] = $this->loan_types_m->get_option_objects_array($this->group->id);   
        $this->data['posts'] = $posts;
        $this->template->title('Supervisor Recommendations ')->build('shared/view_supervisor_recommendation', $this->data);
    }

    function view_hr_appraisals($id = 0){
        $post = new stdClass();
        $loan_applications = $this->loan_applications_m->get($id);
        $contract_type_id = $this->input->post('contract_type_id');
        $contract_end_date = $this->input->post('contract_end_date');
        $loan_amounts = $this->input->post('loan_amount');
        $loan_amount_installments = $this->input->post('loan_amount_installments');
        $percentage_net_pay = $this->input->post('percentage_net_pay');
        $net_pay = $this->input->post('net_pay');
        $repayment_period = $this->input->post('repayment_period');
        $loan_balance = $this->input->post('loan_balance');
        $is_loan_exisiting = $this->input->post('is_loan_exisiting');
        $is_approve = 0;
        $is_decline = 0;
        $this->form_validation->set_rules($this->hr_appraisal_rules);
        if($this->form_validation->run()){
            if(isset($_POST['approve'])){
                $is_approve = 1;
            }else if(isset($_POST['decline'])){
                $is_decline = 1;
            }
            foreach ($loan_amounts as $key => $loan_amount):

                $input =  array(
                    'loan_type_id'=>$loan_applications->loan_type_id,
                    'loan_application_id'=>$loan_applications->id,
                    'loan_member_id'=>$loan_applications->member_id,
                    'hr_member_id'=>$this->member->id,
                    'hr_user_id'=>$this->user->id,
                    'existing_loan_amount' => $loan_amount,
                    'loan_amount_installments'=> $loan_amount_installments[$key],
                    'repayment_period'=> $repayment_period[$key],
                    'loan_balance'=> $loan_balance[$key],
                    'percentage_net_pay'=>$percentage_net_pay,
                    'net_pay' =>$net_pay,
                    'is_loan_exisiting'=>$is_loan_exisiting,
                    'is_approve' => $is_approve,
                    'is_decline'=> $is_decline,
                    'loan_amount'=>$loan_applications->loan_amount,
                    'terms_of_employment'=> $contract_type_id,
                    'contract_end_date'=>strtotime($contract_end_date),
                    'group_id'=>$loan_applications->group_id,
                    'active'=>1,
                    'created_by'=>$this->user->id,
                    'created_on'=>time()
                );
                if($this->loans_m->insert_hr_appraisals($input)){
                    //notify loan applicant of loan approval or decline
                    
                }else{
                    $this->session->set_flashdata('warning','Could not create supervisor records');
                }
                
            endforeach;

            if($this->messaging->notify_sacco_officer_applicant($input)){
                if(isset($_POST['approve'])){
                    $action = "approved";
                }else if(isset($_POST['decline'])){
                    $action = "declined"; 
                }
                $this->session->set_flashdata('info',' Loan request '.$action.' successfully');
                redirect('member/loans/my_hr_appraisals');
            }else{
               $this->session->set_flashdata('warning','Could not notify loan applicant about supervisor recommendation'); 
            }

        }else{
            foreach ($this->hr_appraisal_rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            } 
        }
        $this->data['post'] = $post;        
        $this->data['loan_applications'] = $loan_applications; 
        //$loans = $this->loans_m->get_member_loans($this->group->id,$loan_applications->member_id);
        //$this->data['member_loans'] = $loans;
        $this->data['readonly'] = "readonly='readonly'";
        $this->template->title('Human Resource Appraisal ')->build('shared/hr_appraisals', $this->data);
    }

    function my_hr_appraisals(){
        $posts = $this->loans_m->get_member_hr_recommendations_array($this->group->id ,$this->member->id);
        $this->data['loan_application'] = $this->loan_applications_m->get_option_objects_array();
        $this->data['loan_type'] = $this->loan_types_m->get_option_objects_array($this->group->id);
        foreach ($posts as $key => $post) {
            $this->data['existing_loans'][$post->loan_application_id] = $this->loans_m->get_group_loan_hr_appraisal_requests($this->group->id ,$post->loan_type_id, $post->loan_application_id);
        } 
        $this->data['posts'] = $posts;
        $this->data['hr_appraisals'] = $posts;
        $this->template->title('Human Resource Appraisals')->build('shared/view_hr_appraisals', $this->data); 
    }

    function view_sacco_appraisals($id = 0 ){
        $post = new stdClass();
        $loan_applications = $this->loan_applications_m->get($id);
        $contract_type_id = $this->input->post('contract_type_id');
        $contract_end_date = $this->input->post('contract_end_date');
        $existing_loans = $this->loans_m->get_group_loan_hr_appraisal_requests($this->group->id ,$loan_applications->loan_type_id, $id);
        $is_approve = 0;
        $is_decline = 0;
        $this->form_validation->set_rules($this->sacco_appraisal_rules);
        if($this->form_validation->run()){
            if(isset($_POST['approve'])){
                $is_approve = 1;
            }else if(isset($_POST['decline'])){
                $is_decline = 1;
            }
            $input =  array(
                'loan_type_id'=>$loan_applications->loan_type_id,
                'loan_application_id'=>$loan_applications->id,
                'loan_member_id'=>$loan_applications->member_id,
                'officer_member_id'=>$this->member->id,
                'officer_user_id'=>$this->user->id,
                'percentage_net_pay'=>$this->input->post('percentage_net_pay'),
                'is_approve' => $is_approve,
                'is_decline'=> $is_decline,
                'loan_amount'=>$loan_applications->loan_amount,
                'group_id'=>$loan_applications->group_id,
                'active'=>1,
                'created_by'=>$this->user->id,
                'created_on'=>time()
            );
            //print_r($input); die();
            if($this->loans_m->insert_sacco_officer($input)){
                //notify loan applicant of loan approval or decline
                if($this->messaging->notify_loan_applicant_about_sacco_action($input)){
                    if(isset($_POST['approve'])){
                        $action = "approved";
                    }else if(isset($_POST['decline'])){
                        $action = "declined"; 
                    }
                    $this->session->set_flashdata('info',' Loan request '.$action.' successfully');
                    redirect('member/loans/view_sacco_recommendations');
                }else{
                   $this->session->set_flashdata('warning','Could not notify loan applicant about supervisor recommendation'); 
                }
            }else{
                $this->session->set_flashdata('warning','Could not create supervisor records');
            }
        }else{
            foreach ($this->sacco_appraisal_rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $loan_type = $this->loan_types_m->get($loan_applications->loan_type_id);  
        $this->data['loan_values'] = $this->loan->calculate_loan_balance_invoice($loan_applications->loan_amount,$loan_type->interest_type,$loan_type->interest_rate,$loan_type->fixed_repayment_period,'',time(),$loan_type->loan_interest_rate_per);
        $data['contribution_options'] = $this->contributions_m->get_group_refundable_contribution_options();        
        $this->data['member_savings'] = $this->reports_m->get_group_member_total_contributions($loan_applications->member_id,$data['contribution_options']);
        $this->data['post'] = $post;
        $this->data['loan_type'] =  $loan_type;     
        $this->data['loan_applications'] = $loan_applications; 
        $loans = $this->loans_m->get_member_loans($this->group->id,$loan_applications->member_id);
        $this->data['member_loans'] = $loans;
        $this->data['readonly'] = "readonly='readonly'";
        $this->data['existing_loans'] = $existing_loans;
        $this->template->title('Sacco Officer Approval ')->build('shared/sacco_appraisals', $this->data);    
    }

    function eazzy_club_sacco_loan_calculator(){
       $loan_values = array();
       $total_amount_payable = 0;
       $total_principle_amount = 0;
       $total_interest = 0;
       $monthly_payment = 0;
       $loan_application_id = $this->input->post('loan_application_id');
        if($loan_application_id){
           $loan_application = $this->loan_applications_m->get($loan_application_id);
           $loan_type_id = $loan_application->loan_type_id;
           $get_loan_type_details = $this->loan_types_m->get($loan_type_id);
           $loan_amount =  currency($loan_application->loan_amount);
           $today = time();        
            if($get_loan_type_details->interest_type ==1 || $get_loan_type_details->interest_type ==2){
             if($get_loan_type_details->loan_repayment_period_type == 1){ 
               $loan_values = $this->loan->calculate_loan_balance_invoice(
                        $loan_amount,
                        $get_loan_type_details->interest_type,
                        $get_loan_type_details->interest_rate,
                        $get_loan_type_details->fixed_repayment_period,
                        '',time(),
                        $get_loan_type_details->loan_interest_rate_per);
                foreach ($loan_values as $key => $value) {
                    $value = (object)$value;
                    $total_amount_payable +=$value->amount_payable;
                    $total_principle_amount+=$value->principle_amount_payable;
                    $total_interest+=$value->interest_amount_payable;
                    $monthly_payment=$value->amount_payable;
                } 
             }else if($get_loan_type_details->loan_repayment_period_type == 2){
                if(empty($repayment_period)){?>
                      <div class="alert alert-danger"><button class="close"data-dismiss="alert"></button><p>Loan repayment period is required</p></div>
                   <?php 
                }else{                 
                    $minimum_repayment_period = $get_loan_type_details->minimum_repayment_period;
                    $maximum_repayment_period =  $get_loan_type_details->maximum_repayment_period;
                        if($repayment_period >= $minimum_repayment_period && $repayment_period <= $maximum_repayment_period){
                            $loan_values = $this->loan->calculate_loan_balance_invoice(
                            $loan_amount,
                            $get_loan_type_details->interest_type,
                            $get_loan_type_details->interest_rate,
                            $repayment_period,
                            '',time(),
                            $get_loan_type_details->loan_interest_rate_per);
                        foreach ($loan_values as $key => $value) {
                            $value = (object)$value;
                            $total_amount_payable +=$value->amount_payable;
                            $total_principle_amount+=$value->principle_amount_payable;
                            $total_interest+=$value->interest_amount_payable;
                            $monthly_payment=$value->amount_payable;
                        }
                    }else{?>
                      <div class="alert alert-danger"><button class="close"data-dismiss="alert"></button><p>Repayment period is not within the date ranges allowed the minimum repayment period is <?php echo  $minimum_repayment_period ?> and the maximum repayment period is <?php echo $maximum_repayment_period ?> </p></div>
                   <?php }  
                }
                
                          
             }
            }else if ($get_loan_type_details->interest_type==3) {
                echo 'varying repayment periods2'; 
            }   ?>
            <div class="amortized_schedule">
                <div class="clearfix table_details"></div>
                    <?php if($loan_values):?>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Total Loan Payable:</strong> <?php echo $this->group_currency.' '.number_to_currency($total_amount_payable);?><br/>
                            <strong>Total Interest :</strong> <?php echo $this->group_currency.' '.number_to_currency($total_interest);?><br/>
                            <strong>Repayment Period:</strong> <?php if($get_loan_type_details->loan_repayment_period_type == 1){
                                echo  $get_loan_type_details->fixed_repayment_period;
                            }else if($get_loan_type_details->loan_repayment_period_type == 2){
                               echo isset($repayment_period)?$repayment_period:''; 
                            } ?> Months<br/>       
                        </div>

                        <div class="col-md-6 loan_details_calc">                   
                           
                            <strong>Monthly Payments :</strong> <?php echo $this->group_currency.' '.number_to_currency($monthly_payment);?><br/> 
                            <strong>Interest Rate :</strong> <?php echo $get_loan_type_details->interest_rate?>% <?php $this->loan->loan_interest_rate_per[$get_loan_type_details->loan_interest_rate_per]?>
                                <?php if($get_loan_type_details->loan_interest_rate_per!=3){
                                        if($get_loan_type_details->loan_interest_rate_per==1){
                                            echo 'at '.number_format($get_loan_type_details->interest_rate*30,1).' % Monthly rate';
                                        }
                                        else if($get_loan_type_details->loan_interest_rate_per==2){
                                            echo 'at '.number_format($get_loan_type_details->interest_rate*4,1).' % Monthly rate';
                                        }
                                        else if($get_loan_type_details->loan_interest_rate_per==4){
                                            echo 'at '.number_format($get_loan_type_details->interest_rate/12,1).' % Monthly rate';
                                        }else if($get_loan_type_details->loan_interest_rate_per==5){
                                            $interest_rate = $get_loan_type_details->interest_rate;
                                            $repayment_period = isset($repayment_period)?$repayment_period:$get_loan_type_details->fixed_repayment_period;

                                            echo 'at '.number_format($interest_rate/isset($repayment_period)?$repayment_period:$get_loan_type_details->fixed_repayment_period,1).' % Monthly rate';
                                        }
                                    }?>

                                <br/>
                            <strong>Interest Type :</strong> <?php echo $this->loan->interest_types[$get_loan_type_details->interest_type];?><br/>
                        </div>
                    </div><br><hr>
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-hover table-striped table-condensed table-statement">
                            <thead>
                                <tr>
                                    <th class="invoice-title" width="2%">#</th>
                                    <th class="invoice-title" >Date Payment</th>
                                    <th class="invoice-title text-right">Monthly Payments</th>
                                    <th class="invoice-title text-right">Principal Payable</th>
                                    <th class="invoice-title text-right">Interest Payable</th>
                                    <th class="invoice-title text-right">Total Interest</th>
                                    <th class="invoice-title  text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total_payable =0; $total_principle=0;$balance=$total_amount_payable;$i=0;$total_interest=0; foreach($loan_values as $key=>$value):  $value = (object)$value;
                                        $total_payable+=$value->amount_payable;
                                    ?>
                                        <tr>
                                            <td><?php echo ++$i?></td>
                                            <td><?php echo timestamp_to_date($value->due_date);?></td>
                                            <td class="text-right"><?php echo number_to_currency($value->amount_payable);?></td>
                                            <td class="text-right"><?php echo number_to_currency($principle=$value->principle_amount_payable);?></td>
                                            <td class="text-right"><?php echo number_to_currency($value->interest_amount_payable);?></td>
                                            <td class="text-right"><?php echo number_to_currency($total_interest+=$value->interest_amount_payable);?></td>
                                            <td class="text-right"><?php echo number_to_currency($balance-$total_payable);?></td>
                                        </tr>
                                <?php $total_principle+=$principle; endforeach;?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2">Totals</th>
                                    <th class="text-right"><?php echo number_to_currency($total_payable);?></th>
                                    <th class="text-right"><?php echo number_to_currency($total_principle);?></th>
                                    <th class="text-right"><?php echo number_to_currency($total_interest);?></th>
                                    <th class="text-right"></th>
                                    <th class="text-right"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif;?>
            </div><br><?php
        }else{ ?>
            <div class="alert alert-danger"><button class="close"data-dismiss="alert"></button><p>Loan application details is missing</p></div>
            <?php
        }
    } 


    /* affirmation 
        1 => loan accept 
        2 => loan decline
    */ 

    function applicant_affirmation($id = 0){
        $id OR redirect('group/members');
        $post = new stdClass();
        $loan_applications = $this->loan_applications_m->get($id);
        $is_approve = 0;
        $is_decline = 0;
        $this->form_validation->set_rules($this->applicant_affirmation_rules);
        if($this->form_validation->run()){
            if(isset($_POST['approve'])){
                $affirmation = 1;
            }else if(isset($_POST['decline'])){
                $affirmation = 2;
            }
            $affirmation_entries =  array(
                'affirmation'=>$affirmation,
                'modified_on' => time(),
                'modified_by' => $this->user->id
            );
            $input =  array(
                'loan_type_id'=>$loan_applications->loan_type_id,
                'loan_application_id'=>$loan_applications->id,
                'loan_member_id'=>$loan_applications->member_id,
                'affirmation' => $affirmation,
                'loan_amount'=>$loan_applications->loan_amount,
                'group_id'=>$loan_applications->group_id
            );
            if($this->loan_applications_m->update($id , $affirmation_entries)){
                if($this->messaging->notify_sacco_commitee_members($input)){
                    if(isset($_POST['approve'])){
                        $action = "approved";
                    }else if(isset($_POST['decline'])){
                        $action = "declined"; 
                    }
                    $this->session->set_flashdata('info',' Loan request '.$action.' successfully');
                    redirect('member/loans/view_loan_application/'.$loan_applications->id);
                }else{
                   $this->session->set_flashdata('warning','Could not notify loan applicant about supervisor recommendation'); 
                }
            }else{
                $this->session->set_flashdata('warning','Could not create supervisor records');
            }
        }else{
            foreach ($this->applicant_affirmation_rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $this->data['post'] = $post;
        $this->data['loan_type'] = $this->loan_types_m->get($loan_applications->loan_type_id);        
        $this->data['loan_applications'] = $loan_applications;
        $this->data['readonly'] = "readonly='readonly'";
        $this->template->title('APPLICANT AFFIRMATION')->build('shared/applicant_affirmation', $this->data);
    }

    function view_sacco_recommendations(){
        $posts = $this->loans_m->get_member_sacco_officer_appraisals($this->group->id ,$this->member->id);       
        $loan_values = array();
        if($posts){
            $loan_type = $this->loan_types_m->get_option_objects_array($this->group->id); 
            //print_r($loan_type); die();
            foreach ($posts as $key => $post) {
                $loan_applications = $this->loan_applications_m->get($post->loan_application_id);
                $this->data['existing_loans'][$post->loan_application_id] = $this->loans_m->get_group_loan_hr_appraisal_requests($this->group->id ,$post->loan_type_id, $post->loan_application_id);

                $this->data['loan_values'][$post->loan_application_id] = $this->loan->calculate_loan_balance_invoice($loan_applications->loan_amount,$loan_type[$post->loan_type_id]->interest_type,$loan_type[$post->loan_type_id]->interest_rate,$loan_type[$post->loan_type_id]->fixed_repayment_period,'',time(),$loan_type[$post->loan_type_id]->loan_interest_rate_per);

                $loans = $this->loans_m->get_member_loans($this->group->id,$loan_applications->member_id);
                $this->data['member_loans'][$post->loan_application_id] = $loans;
            }             
        } 

        $data['contribution_options'] = $this->contributions_m->get_group_refundable_contribution_options();        
        $this->data['member_savings'] = $this->reports_m->get_group_member_total_contributions($loan_applications->member_id,$data['contribution_options']);
        $this->data['post'] = $post;
        $this->data['loan_type'] =  $loan_type;     
        $this->data['loan_applications'] = $loan_applications; 
        $this->data['group_loan_applications'] = $this->loan_applications_m->get_option_objects_array();
        //$this->data['loan_values'] = $loan_values;
        $this->data['posts'] = $posts;
        
        $this->template->title('Level 2 Appraisal & Processing By Sacco')->build('shared/view_sacco_officer_appraisals', $this->data); 
    }  

    function pending_loan_request($id = 0){
        $id OR redirect('member'); 
        $post =  new stdClass();
        $is_approve = 0;
        $is_decline = 0;
        $comment = $this->input->post('comment');
        $action_id = $this->input->post('action_id');
        $account_id = $this->input->post('account_id');
        $commitee_details = $this->loans_m->get_loan_signatories($id,$this->group->id);  
        $loan_application = $this->loan_applications_m->get($commitee_details->loan_application_id); 

        $this->form_validation->set_rules($this->committe_validation);
        if($this->form_validation->run()){
            $group_members = $this->members_m->get_group_members_array($this->group->id);
            $loan_type = $this->loan_types_m->get($commitee_details->loan_type_id);
            if($commitee_details->signatory_member_id){
                $saccom_committe = 'signatory';
                $member_id = $commitee_details->signatory_member_id;
            }else if($commitee_details->commitee_member_id){
                $saccom_committe = 'committee member';
                $member_id = $commitee_details->commitee_member_id;
            }
            $member_particulars = array(
                'signatory_id' => $id,
                'loan_type_id' =>$commitee_details->loan_type_id,
                'loan_member_id'=>$commitee_details->loan_request_member_id,
                'loan_application_id'=>$commitee_details->loan_application_id,
                'signatory_member_id'=>$commitee_details->signatory_member_id,
                'committee_member_id'=>$commitee_details->commitee_member_id,
                'group_id'=>$commitee_details->group_id,
                'loan_amount'=>$commitee_details->loan_amount,
                'repayment_period'=> $loan_application->repayment_period,
                'created_on' => $loan_application->created_on,
                'loan_name' => $loan_application->name,
                'loan_application_date'=> $loan_application->created_on,
                'account_id'=>$account_id
            );
            if($action_id == 1){
                $action = 'approved';
                if($commitee_details->commitee_member_id){
                    $data = array(
                        'is_approved'=>1,
                        'approve_comment'=>$comment,
                        'committee_progress_status'=>3,
                        'modified_on'=>time(),
                        'modified_by'=>$this->user->id
                    );
                }else if($commitee_details->signatory_member_id){
                    $action = 'approved';
                    $data = array(
                        'is_approved'=>1,
                        'approve_comment'=>$comment,
                        'loan_signatory_progress_status'=>3,
                        'modified_on'=>time(),
                        'modified_by'=>$this->user->id
                    );   
                }
            }else if($action_id == 2){
                $action = 'deffered';
                if($commitee_details->commitee_member_id){
                    $data = array(
                        'is_approved'=>2,
                        'approve_comment'=>$comment,
                        'committee_progress_status'=>4,
                        'active'=>1,
                        'modified_on'=>time(),
                        'modified_by'=>$this->user->id
                    );
                }else if($commitee_details->signatory_member_id){
                    $data = array(
                        'is_approved'=>2,
                        'approve_comment'=>$comment,
                        'loan_signatory_progress_status'=>4,
                        'active'=>1,
                        'modified_on'=>time(),
                        'modified_by'=>$this->user->id
                    );   
                }
            }else if($action_id == 3){
                $action = 'decline';
                if($commitee_details->commitee_member_id){
                    $data = array(
                        'is_declined'=>1,
                        'decline_comment'=>$comment,
                        'committee_progress_status'=>2,
                        'active'=>0,
                        'modified_on'=>time(),
                        'modified_by'=>$this->user->id
                    );
                }else if($commitee_details->signatory_member_id){
                    $data = array(
                        'is_declined'=>1,
                        'decline_comment'=>$comment,
                        'loan_signatory_progress_status'=>2,
                        'active'=>0,
                        'modified_on'=>time(),
                        'modified_by'=>$this->user->id
                    );   
                }
            }
            if($this->loans_m->update_loan_signatories($id,$data)){
                
                if($action_id == 3){
                    $decline_loan = array(
                        'is_declined'=>1,
                        'decline_message'=>$comment,
                        'status'=>4,
                        'modified_by'=>$this->user->id,
                        'modified_on'=>time()
                    );
                    if($this->loan_applications_m->update($commitee_details->loan_application_id,$decline_loan)){
                        //notify loan applicant of loan decline
                        $notification_success = $this->notifications->create(
                            'Loan Declined ',
                            ''.$group_members[$member_id]->first_name.' '.$group_members[$member_id]->last_name.' a '.$saccom_committe.' of the group has declined your loan ('.$loan_type->name.')  request of amount '.$this->group_currency.''.number_to_currency($commitee_details->loan_amount),
                                $this->ion_auth->get_user($group_members[$member_id]->user_id),                    
                                $member_id,
                                $group_members[$commitee_details->loan_request_member_id]->user_id,                               
                                $commitee_details->loan_request_member_id,
                                $commitee_details->group_id,
                            'View Loan request declined ',
                            'member/members/signatory_approved_loans/'.$commitee_details->loan_application_id,
                            10,
                            $id
                        );
                        redirect('member/loans/signatory_approvals_listing');
                    }
                }else{
                    //notify loan applicant of success
                    $input_loan_application = array(
                        'account_id'=>$account_id
                    );
                    if($this->loan_applications_m->update($commitee_details->loan_application_id,$input_loan_application)){
                        if($this->messaging->notify_loan_applicant_about_committee_action($member_particulars)){
                            $this->session->set_flashdata('success','You have '.$action.'  '.$group_members[$commitee_details->loan_request_member_id]->first_name.' '.$group_members[$commitee_details->loan_request_member_id]->last_name.' loan( '.$loan_type->name.') of  '.$this->group_currency.' '.number_to_currency($commitee_details->loan_amount).' as a '.$saccom_committe.' of the group');                       
                            redirect('member/loans/signatory_approvals_listing');
                        }
                        /*if($this->messaging->create_member_loan($member_particulars)){
                            $this->session->set_flashdata('success','Loan approved successfully');
                            redirect('member/loans/sacco_manager_listings/');
                        }else{
                           $this->session->set_flashdata('error','Could not create member loan'); 
                        }*/
                    }else{
                        $this->session->set_flashdata('error','Could not update loan details'); 
                    }
                    
                }
            }else{
                $this->session->set_flashdata('error','Loan application could not be edited');
            }             
        }else{
            foreach ($this->committe_validation as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
       } 
       $this->data['post'] = $post;
       $this->data['loan_signatory_id'] = $id;
       $this->data['active_accounts'] = $this->accounts_m->get_active_group_account_options();
       $this->template->title('Loan Approvals Requests')->build('shared/committee_approval',$this->data); 
    }

       /* saccom_manager_status
            1 => approved
            2 = >declined
        */

    function loan_request($id = 0){
        $id OR redirect('member');
        $post = new StdClass();
        $loan_application = $this->loan_applications_m->get($id);
        $account_id = $this->input->post('account_id');
        $this->form_validation->set_rules($this->sacco_manager);
        if($this->form_validation->run()){
            if(isset($_POST['approve'])){
                //create loan
                $member_particulars = array(
                    'loan_type_id' =>$loan_application->loan_type_id,
                    'loan_member_id'=>$loan_application->member_id,
                    'loan_application_id'=>$id,
                    'group_id'=>$loan_application->group_id,
                    'loan_amount'=>$loan_application->loan_amount,
                    'repayment_period'=> $loan_application->repayment_period,
                    'created_on' => $loan_application->created_on,
                    'loan_application_date'=> $loan_application->created_on,
                    'account_id'=>$account_id,
                    'loan_name' => $loan_application->name,
                );
                $input = array(
                    'account_id'=>$account_id,
                    'sacco_manager_member_id'=>$this->user->id,
                    'sacco_manager_status'=>1,
                );
                if($this->loan_applications_m->update($id,$input)){
                    if($this->messaging->create_member_loan($member_particulars)){
                        $this->session->set_flashdata('success','Loan approved successfully');
                        redirect('member/loans/sacco_manager_listings/');
                    }else{
                       $this->session->set_flashdata('error','Could not create member loan'); 
                    }
                }else{
                    $this->session->set_flashdata('error','Could not update loan details'); 
                }
            }else if(isset($_POST['decline'])){
                //don't create loan
                $member_particulars = array(
                    'loan_type_id' =>$loan_application->loan_type_id,
                    'loan_member_id'=>$loan_application->member_id,
                    'loan_application_id'=>$id,
                    'group_id'=>$loan_application->group_id,
                    'loan_amount'=>$loan_application->loan_amount,
                    'repayment_period'=> $loan_application->repayment_period,
                    'created_on' => $loan_application->created_on,
                    'loan_application_date'=> $loan_application->created_on,
                    'account_id'=>$account_id,
                    'loan_name' => $loan_application->name,
                );
                $input = array(
                    'account_id'=>$account_id,
                    'sacco_manager_member_id'=>$this->user->id,
                    'sacco_manager_status'=>2,
                    'active'=>0,
                    'is_approved'=>0,
                );
                if($this->loan_applications_m->update($id,$input)){
                    if($this->messaging->decline_member_loan($member_particulars)){
                        $this->session->set_flashdata('success','Loan declined successfully');
                        redirect('member/loans/sacco_manager_listings/');
                    }else{
                       $this->session->set_flashdata('error','Could not create member loan'); 
                    }
                }else{
                    $this->session->set_flashdata('error','Could not update loan details'); 
                }
            }
        }else{
            foreach ($this->sacco_manager as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $this->data['post'] = $post;
        $this->data['active_accounts'] = $this->accounts_m->get_active_group_account_options();
        $this->data['loan_application'] = $loan_application;
        $this->template->title('Loan  Requests')->build('shared/loan_request',$this->data);   
    }

    function change_status_credit_committe($id = 0){
        $commitee_details = $this->loans_m->get_loan_signatories($id,$this->group->id);
        $data = array(
            'is_approved'=>0,
            'is_declined'=>0,
            'decline_comment'=>'test decline',
            'loan_signatory_progress_status'=>2,
            'active'=>1,
            'modified_on'=>time(),
            'modified_by'=>$this->user->id
        );
        if($this->loans_m->update_loan_signatories($id,$data)){
             echo 'loan test  declined successfully'; 
        }else{
            echo 'not declined';  
        }   
    }

    function change_loan_amount($id =0){
        die();
        $loan_application = $this->loan_applications_m->get($id);
        $data = array(
            'loan_amount'=>1200000,
            'active'=>1,
            'modified_on'=>time(),
            'modified_by'=>$this->user->id
        );
        if($this->loan_applications_m->update($id,$data)){
            echo 'loan amount changed successfully'; 
        }else{
            echo 'loan amount not changed';  
        } 
    }

    function sacco_manager_listings(){
        $posts = $this->loan_applications_m->get_pending_group_loan_applications();
        $this->data['posts'] = $posts;
        //print_r($posts); die();
        $this->template->title('Sacco Manager Approvals')->build('shared/view_sacco_manager_approvals', $this->data);
    }


}