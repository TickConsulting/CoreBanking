<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bank extends Bank_Controller{

    protected $validation_rules = array(
        array(
            'field' => 'loan_type',
            'label' => 'Loan Type',
            'rules' => 'trim|required|numeric',
        ),
        array(
            'field' => 'loan_type_id',
            'label' => 'Loan Type id',
            'rules' => 'trim|required|numeric',
        ),
        array(
            'field' =>  'member_id',
            'label' =>  'Member Name',
            'rules' =>  'trim|required|numeric'
        ),
        array(
            'field' =>  'disbursement_date',
            'label' =>  'Loan Disbursement Date',
            'rules' =>  'trim|required'
        ),
        array(
            'field' =>  'loan_amount',
            'label' =>  'Loan Amount',
            'rules' =>  'trim|required|currency|greater_than[0]'
        ),
        array(
            'field' =>  'repayment_period',
            'label' =>  'Loan Repayment Period',
            'rules' =>  'trim|required|numeric'
        ),
        array(
            'field' =>  'interest_rate',
            'label' =>  'Loan Interest Rate',
            'rules' =>  'trim|numeric'
        ),
        array(
            'field' =>  'interest_type',
            'label' =>  'Loan Interest Type',
            'rules' =>  'trim|numeric'
        ),
        array(
            'field' =>  'account_id',
            'label' =>  'Loan Disbursing Account',
            'rules' =>  'trim|required'
        ),
        array(
            'field' =>  'grace_period',
            'label' =>  'Loan Grace Period',
            'rules' =>  'trim|required'
        ),
        array(
            'field' =>  'enable_loan_fines',
            'label' =>  'Enable Late Loan Payment Fines',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'loan_fine_type',
            'label' =>  'Loan Fine Type',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'enable_outstanding_loan_balance_fines',
            'label' =>  'Enable Fines for Outstanding Balances',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'enable_loan_processing_fee',
            'label' =>  'Enable Loan Processing',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'enable_loan_guarantors',
            'label' =>  "Enable Loan Guarantors",
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'enable_loan_fine_deferment',
            'label' =>  'Enable Loan Deferment',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'sms_notifications_enabled',
            'label' =>  'Enable SMS Notifications',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'email_notifications_enabled',
            'label' =>  'Enable Email Notifications',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'fixed_fine_amount',
            'label' =>  'Fixed Fine Amount',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'fixed_amount_fine_frequency',
            'label' =>  'Fixed Amount Fine Frequecy',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'fixed_amount_fine_frequency_on',
            'label' =>  'Fixed Amount Fine Frequecy On',
            'rules' =>  'trim'  
        ),
        array(
            'field' =>  'percentage_fine_rate',
            'label' =>  'Percentage Fine Rate',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'percentage_fine_frequency',
            'label' =>  'Percentage Fine Frequecy',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'percentage_fine_on',
            'label' =>  'Percentage Fine On',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'one_off_fine_type',
            'label' =>  'One Off Fine Type',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'one_off_fixed_amount',
            'label' =>  'One Off Fine Amount',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'one_off_percentage_rate',
            'label' =>  'One Off Percentage Rate',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'one_off_percentage_rate_on',
            'label' =>  'One Off Percentage Rate On',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_fine_type',
            'label' =>  'Outstanding Loan Balance Fine Type',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_fine_fixed_amount',
            'label' =>  'Outstanding Loan Balance Fine Fixed Amount',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_fixed_fine_frequency',
            'label' =>  'Outstanding Loan Balance Fine Fixed Amount Frequecy',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_percentage_fine_on',
            'label' =>  'Outstanding Loan Balance Percentage Fine On',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_percentage_fine_frequency',
            'label' =>  'Outstanding Loan Balance Percentage Fine Frequency',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_percentage_fine_rate',
            'label' =>  'Outstanding Loan Balance Percentage Fine Rate',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_fine_one_off_amount',
            'label' =>  'Outstanding Loan Balance Fine One Off Amount',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'loan_processing_fee_type',
            'label' =>  'Loan Processing Fee Type',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'loan_processing_fee_fixed_amount',
            'label' =>  'Loan Processing Fee Fixed Amount',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'loan_processing_fee_percentage_rate',
            'label' =>  'Loan Processing Fee Fixed Percentage Rate',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'disable_automatic_loan_processing_income',
            'label' =>  'Disable Automatic Loan Processing Income',
            'rules' =>  'trim|numeric'
        ),
        array(
            'field' =>  'loan_processing_fee_type',
            'label' =>  'Loan Processing Fee Type',
            'rules' =>  'trim'
        ),
        array(
            'field' => 'loan_interest_rate_per',
            'label' => 'Loan Interest Rate Per',
            'rules' => 'required|numeric|trim',
        ),
        array(
            'field' =>  'custom_interest_procedure',
            'label' =>  'Custom Procedure',
            'rules' => '',
        ),
        array(
            'field' => 'enable_reducing_balance_installment_recalculation',
            'label' => 'Enable Reducing Balance Recalulation on Early Installment Repayment',
            'rules' => 'trim|numeric',
        ), 
        array(
            'field' => 'grace_period_date',
            'label' => 'Grace Period Date',
            'rules' => 'trim',
        ),  
        array(
            'field' => 'loan_processing_fee_percentage_charged_on',
            'label' => 'Loan Processing Fee Charged On',
            'rules' => 'trim',
        ),
        array(
            'field' => 'loan_to',
            'label' => 'Loan to ',
            'rules' => 'trim|numeric|required',
        ), 
         
    );
    
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


    protected $transfer_options = array(
            1 => 'To contribution share',
            2 => 'To fines ',
            3 => 'To another loan',
            4 => 'To another member',
        );

    protected $loan_types = array(
            1   =>  'Automated Group Loan',
            //2   =>  'Custom Group Loan', used upon adding a custom interest rate
            3   =>  'Group Bank Loan'
        );

    protected $interest_types = array(
            1   =>  'Fixed Balance',
            2   =>  'Reducing Balance',
            3   =>  'Custom Interest Type'
        );

    protected $loan_grace_periods = array(
        1   =>  'One Month',
        2   =>  'Two Months',
        3   =>  'Three Months',
        4   =>  'Four Months',
        5   =>  'Five Months',
        6   =>  'Six Months',
        7   =>  'Seven Months',
        8   =>  'Eight Months',
        9   =>  'Nine Months',
        10  =>  'Ten Months',
        11  =>  'Eleven Months',
        12  =>  'One Year',
    );

    protected $accounts = array();

    protected $late_loan_payment_fine_types = array(
        3   =>  'A One off Fine Amount per Installment',
        1   =>  'A Fixed Fine Amount',
        2   =>  'A Percentage (%) Fine',
    );

    protected $late_payments_fine_frequency = array(
        1   =>  'per day',
        2   =>  'per week',
        3   =>  'per month',
        4   =>  'per year'
    );

    public $fixed_amount_fine_frequency_on = array(
        1 => 'For each outstanding installment',
        2 => 'On total outstanding balance',
    );

    protected $percentage_fine_on = array(
        1   =>  'Loan Installment Balance',
        2   =>  'Loan Amount',
        3   =>  'Total Unpaid Loan Amount',
    );

    protected $one_off_fine_types = array(
        1   =>  'Fixed Fine',
        2   =>  'Percentage Fine'
    );

    protected $one_off_percentage_rate_on = array(
        1   =>  'Loan Installment Balance',
        2   =>  'Loan Amount',
        3   =>  'Total Unpaid Loan Amount',
    );

    protected $loan_processing_fee_types = array(
        1   =>  'A Fixed Amount',
        2   =>  'A Percentage (%) Value'
    );

    protected $loan_processing_fee_percentage_charged_on = array(
        1   =>  'Total Loan Amount',
        2   =>  'Total Loan Principle plus Interest',
    );

    protected $loan_interest_rate_per = array(
        1   =>  'Per Day',
        2   =>  'Per Week',
        3   =>  'Per Month',
        4   =>  'Per Annum',
        5   =>  'For the whole loan repayment period'
    );

    protected $month_options = array(
        1 => "January",
        2 => "Febraury",
        3 => "March",
        4 => "April",
        5 => "May",
        6 => "June",
        7 => "July",
        8=> "August",
        9 => "September",
        10 => "October",
        11 => "November",
        12 => "December",
    );

    protected $loan_days = array(
        1=>'Day 1 after loan disbursement',
        2=>'Day 2 after loan disbursement',
        3=>'Day 3 after loan disbursement',
        4=>'Day 4 after loan disbursement',
        5=>'Day 5 after loan disbursement',
        6=>'Day 6 after loan disbursement',
        7=>'1 Week after loan disbursement',
        14=>'2 Weeks after loan disbursement',
        21=>'3 Weeks after loan disbursement',
        30=>'1 Month after loan disbursement',
        60=>'2 Months after loan disbursement',
        90=>'3 Months after loan disbursement',
        120=>'4 Months after loan disbursement',
        150=>'5 Months after loan disbursement',
        180=>'6 Months after loan disbursement',
        210=>'7 Months after loan disbursement',
        240=>'8 Months after loan disbursement',
        270=>'9 Months after loan disbursement',
        300=>'10 Months after loan disbursement',
        330=>'11 Months after loan disbursement',
        360=>'1 year after loan disbursement',
        10000=>'To the last date of loan repayment', 
    );

    protected $sms_template_default = 'Hi [FIRST_NAME], you have been invoiced [GROUP_CURRENCY] [INVOICED_AMOUNT], for your group loan payable before [DUE_DATE]. Total Loan balance is [GROUP_CURRENCY] [LOAN_BALANCE].';


    protected $data = array();

    protected $repayment_status_options = array(
        1 => "Fully Paid",
        0 => "In Progress",
    );

    protected $loan_repayment_period_type = array(
        1=>  'Fixed Repayment Period',
        2=>  'Varying Repayment Period',
    );
    protected $loan_amount_type = array(
        1=>'Based on Amount Range',
        2=>'Based On Member Savings',
    );

    protected $loan_to_options = array(
        ' ' => '--Select members option--',
        '1' => 'All Members',
        '2' => 'Individual Members',
    );

    protected $disbursement_options = array(
        ' ' => '--Select disbursment option--',
        '1' => 'Mobile Money Wallet Account',
        '2' => 'Equity Bank Account',
    );

    function __construct(){

        parent::__construct();

        $this->load->model('loans_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('loan_repayments/loan_repayments_m');
        $this->load->model('loan_invoices/loan_invoices_m');
        $this->load->model('members/members_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->model('withdrawals/withdrawals_m');
        $this->load->model('loan_types/loan_types_m');
        $this->load->model('users/users_m');
        $this->load->model('recipients/recipients_m');
        $this->load->library('transactions');
        $this->load->library('loan');
        $this->load->library('notifications');
        $this->load->library('pdf_library');

        $this->data['loan_types'] = $this->loan_types;
        $this->data['interest_types'] = $this->interest_types;
        $this->data['late_loan_payment_fine_types'] = $this->late_loan_payment_fine_types;
        $this->data['late_payments_fine_frequency'] = $this->late_payments_fine_frequency;
        $this->data['percentage_fine_on'] = $this->percentage_fine_on;
        $this->data['one_off_fine_types'] = $this->one_off_fine_types;
        $this->data['one_off_percentage_rate_on'] = $this->one_off_percentage_rate_on;
        $this->data['loan_processing_fee_types'] =  $this->loan_processing_fee_types;
        $this->data['sms_template_default'] = $this->sms_template_default;
        $this->data['loan_grace_periods'] = $this->loan_grace_periods;
        $this->data['loan_processing_fee_percentage_charged_on'] = $this->loan_processing_fee_percentage_charged_on;
        $this->data['loan_days'] = $this->loan_days;
        $this->data['loan_interest_rate_per'] = $this->loan_interest_rate_per;
        $this->data['fixed_amount_fine_frequency_on'] = $this->fixed_amount_fine_frequency_on;

        $this->accounts = $this->accounts_m->get_group_account_options();
        $this->active_accounts = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        $this->data['accounts'] = $this->accounts;
        $this->data['active_accounts'] = $this->active_accounts;

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


    function _check_joint_loan_members(){
        $joint_loan_members = $this->input->post('joint_loan_members');
        if(count($joint_loan_members)>=1){
            $counter = 0;
            foreach ($joint_loan_members as $joint_loan_member){
               if($joint_loan_member == $this->input->post('member_id')){
                    $counter++;
               }
            }
            if($counter>=1){
                $this->form_validation->set_message('_check_joint_loan_members','Member cannot take a joint loan with self');
                return FALSE;
            }else{
                return TRUE;
            }
        }else{
            $this->form_validation->set_message('_check_joint_loan_members','Select atleast one member to process the joint loan with');
            return FALSE;
        }
    }


    function create_old(){
        $post = new stdClass();
        if($this->input->post('sms_notifications_enabled')){
            $this->validation_rules[] = array(
                'field' =>  'sms_template',
                'label' =>  'Loan Installment SMS Template',
                'rules' =>  'required|trim'
            );
        }

        if($this->input->post('is_a_joint_loan')){

            $this->validation_rules[] = array(
                'field' => 'joint_loan_members',
                'label' => 'Members',
                'rules' => 'callback__check_joint_loan_members',
            );
        }

        if($this->input->post('enable_loan_fines')){

            $this->validation_rules[] = array(
                'field' =>  'loan_fine_type',
                'label' =>  'Late Loan Fine Type',
                'rules' =>  'required|trim'
            );

            if($this->input->post('loan_fine_type') == 1){

                $this->validation_rules[] = array(
                    'field' =>  'fixed_fine_amount',
                    'label' =>  'Fixed Fine Amount',
                    'rules' =>  'required|trim|currency'
                );

                $this->validation_rules[] = array(
                    'field' =>  'fixed_amount_fine_frequency',
                    'label' =>  'Fixed Amount Fine Frequency',
                    'rules' =>  'required|trim'
                );

                $this->validation_rules[] = array(
                    'field' =>  'fixed_amount_fine_frequency_on',
                    'label' =>  'Fixed Amount Fine Frequency On',
                    'rules' =>  'required|trim'
                );

            }

            if($this->input->post('loan_fine_type') == 2){

                $this->validation_rules[] = array(
                    'field' =>  'percentage_fine_rate',
                    'label' =>  'Percentage(%) Fine Rate',
                    'rules' =>  'required|trim|numeric'
                );

                $this->validation_rules[] = array(
                    'field' =>  'percentage_fine_frequency',
                    'label' =>  'Percentage Fine Frequency',
                    'rules' =>  'required|trim'
                );

                $this->validation_rules[] = array(
                    'field' =>  'percentage_fine_on',
                    'label' =>  'Percentage Fine Frequency on',
                    'rules' =>  'required|trim'
                );

            }

            if($this->input->post('loan_fine_type') == 3){

                $this->validation_rules[] = array(
                    'field' =>  'one_off_fine_type',
                    'label' =>  'One off Fine Type',
                    'rules' =>  'required|trim'
                );

                if($this->input->post('one_off_fine_type') == 1){
                    $this->validation_rules[] = array(
                        'field' =>  'one_off_fixed_amount',
                        'label' =>  'One off Fine Fixed Amount',
                        'rules' =>  'required|trim|currency'
                    );
                }

                if($this->input->post('one_off_fine_type') == 2){

                    $this->validation_rules[] = array(
                        'field' =>  'one_off_percentage_rate',
                        'label' =>  'One off Percentage (%) Rate',
                        'rules' =>  'required|trim|numeric'
                    );

                    $this->validation_rules[] = array(
                        'field' =>  'one_off_percentage_rate_on',
                        'label' =>  'One off Percentage Rate On',
                        'rules' =>  'required|trim'
                    );

                }
            }
        }

        if($this->input->post('enable_outstanding_loan_balance_fines')){

            $this->validation_rules[] = array(
                'field' =>  'outstanding_loan_balance_fine_type',
                'label' =>  'Outstanding Loan FIne Types',
                'rules' =>  'required|trim'
            );

            if($this->input->post('outstanding_loan_balance_fine_type') == 1){
                
                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fine_fixed_amount',
                    'label' =>  'Outstanding Loan Balance FIne Fixed Amount',
                    'rules' =>  'required|trim|currency'
                );
                
                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fixed_fine_frequency',
                    'label' =>  'Outstanding Loan Balance FIne Fixed Frequency Rate',
                    'rules' =>  'required|trim'
                );

            }

            if($this->input->post('outstanding_loan_balance_fine_type') == 2){

                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_percentage_fine_rate',
                    'label' =>  'Outstanding Loan Balance Percentage Rate',
                    'rules' =>  'required|trim|numeric'
                );

                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_percentage_fine_frequency',
                    'label' =>  'Outstanding Loan Balance Percentage Fine Frequency',
                    'rules' =>  'required|trim'
                );

                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_percentage_fine_on',
                    'label' =>  'Outstanding Loan Balance Percentage Rate on',
                    'rules' =>  'required|trim'
                );

            }

            if($this->input->post('outstanding_loan_balance_fine_type') == 3){

                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fine_one_off_amount',
                    'label' =>  'Outstanding Loan Balance One Off Amount',
                    'rules' =>  'required|trim|currency'
                );

            }
        }

        if($this->input->post('enable_loan_processing_fee')){

            $this->validation_rules[] = array(
                'field' =>  'loan_processing_fee_type',
                'label' =>  'Loan Processing Fee Type',
                'rules' =>  'required|trim'
            );

            if($this->input->post('loan_processing_fee_type') == 1){

                $this->validation_rules[] = array(
                    'field' =>  'loan_processing_fee_fixed_amount',
                    'label' =>  'Loan Processing Fee Fixed Amount',
                    'rules' =>  'required|trim|currency'
                );

            }else if($this->input->post('loan_processing_fee_type') == 2){

                $this->validation_rules[] = array(
                    'field' =>  'loan_processing_fee_percentage_rate',
                    'label' =>  'Loan Processing Percentage Value',
                    'rules' =>  'required|trim|numeric'
                ); 

                $this->validation_rules[] = array(
                    'field' =>  'loan_processing_fee_percentage_charged_on',
                    'label' =>  'Loan Processing Fee Charged On',
                    'rules' =>  'required|trim'
                );

            }
        }

        if($this->input->post('enable_loan_guarantors')){

            $this->validation_rules[] = array(
                'field' =>  'guarantor_id[]',
                'label' =>  'Guarontor Name',
                'rules' =>  'callback__verify_guarantor_name'
            );
        }

        if($this->input->post('interest_type') == 3){

            $custom_interest_procedure = $this->input->post('custom_interest_procedure');

            if($this->input->post('custom_interest_procedure') == 1){

                $this->validation_rules[] = array(
                    'field' =>  'interest_rate_date_from',
                    'label' =>  'Interest Rate Date From',
                    'rules' =>  'callback__interest_rate_breakdown'
                );

            }else if($this->input->post('custom_interest_procedure') == 2){
                $this->validation_rules[] = array(
                    'field' =>  'custom_payment_date',
                    'label' =>  'Loan installment payment date',
                    'rules' =>  'callback__installment_breakdown'
                );
            }
        }else{

            $custom_interest_procedure = '';

            $this->validation_rules[] = array(
                'field' =>  'interest_type',
                'label' =>  'Interest Type ',
                'rules' =>  'required|trim|numeric'
            );

            $this->validation_rules[] = array(
                'field' =>  'interest_rate',
                'label' =>  'Interest Rate ',
                'rules' =>  'required|trim|numeric'
            );
        }

        if($this->input->post('grace_period') == "date"){

            $this->validation_rules[] = array(
                'field' =>  'grace_period_date',
                'label' =>  'Grace Period Date',
                'rules' =>  'required|trim'
            );
        }

        $this->form_validation->set_rules($this->validation_rules);

        if($this->form_validation->run()){

            $loan_type =  $this->input->post('loan_type');
            $member_id =  $this->input->post('member_id');
            $group_id  =  $this->group->id;

            $enable_loan_fines = $this->input->post('enable_loan_fines');
            $enable_outstanding_loan_balance_fines = $this->input->post('enable_outstanding_loan_balance_fines');
            $enable_loan_processing_fee = $this->input->post('enable_loan_processing_fee');

            $loan_details = array(
                'disbursement_date' =>  strtotime($this->input->post('disbursement_date')),
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
                'enable_loan_fines' =>  $enable_loan_fines,
                'enable_outstanding_loan_balance_fines'=>$enable_outstanding_loan_balance_fines,
                'enable_loan_processing_fee' => $enable_loan_processing_fee,
                'enable_loan_fine_deferment' => $this->input->post('enable_loan_fine_deferment'),
                'enable_loan_guarantors' => $this->input->post('enable_loan_guarantors'),
                'enable_reducing_balance_installment_recalculation' => $this->input->post('enable_reducing_balance_installment_recalculation')?1:0,
                'active'    =>  1,
                'created_by'    =>  $this->user->id,
                'created_on'    =>  time(),
            );

            if($enable_loan_fines){
                $loan_fine_type = $this->input->post('loan_fine_type');
                $loan_details = $loan_details+array('loan_fine_type'=>$loan_fine_type);
                if($loan_fine_type == 1){

                    $loan_details += array(
                        'fixed_fine_amount' =>  $this->input->post('fixed_fine_amount'),
                        'fixed_amount_fine_frequency'   =>  $this->input->post('fixed_amount_fine_frequency'),
                        'fixed_amount_fine_frequency_on' => $this->input->post('fixed_amount_fine_frequency_on'),
                    );

                }else if($loan_fine_type == 2){

                    $loan_details += array(
                        'percentage_fine_rate'  =>  $this->input->post('percentage_fine_rate'),
                        'percentage_fine_frequency' =>  $this->input->post('percentage_fine_frequency'),
                        'percentage_fine_on'    =>  $this->input->post('percentage_fine_on'),
                    );

                }else if($loan_fine_type == 3){

                    $one_off_fine_type = $this->input->post('one_off_fine_type');

                    $loan_details += array(
                        'one_off_fine_type' => $one_off_fine_type
                    );

                    if($one_off_fine_type == 1){

                        $loan_details += array(
                            'one_off_fixed_amount'  =>  $this->input->post('one_off_fixed_amount')
                        );

                    }else if($one_off_fine_type == 2){

                        $loan_details += array(
                            'one_off_percentage_rate'   =>  $this->input->post('one_off_percentage_rate'),
                            'one_off_percentage_rate_on'    =>  $this->input->post('one_off_percentage_rate_on'),
                        );

                    }
                }
            }

            if($enable_outstanding_loan_balance_fines){

                $outstanding_loan_balance_fine_type = $this->input->post('outstanding_loan_balance_fine_type');

                $loan_details += array(
                    'outstanding_loan_balance_fine_type' => $outstanding_loan_balance_fine_type
                );

                if($outstanding_loan_balance_fine_type == 1){

                    $loan_details += array(
                        'outstanding_loan_balance_fine_type' => $this->input->post('outstanding_loan_balance_fine_type'),
                        'outstanding_loan_balance_fine_fixed_amount' => $this->input->post('outstanding_loan_balance_fine_fixed_amount'),
                        'outstanding_loan_balance_fixed_fine_frequency' => $this->input->post('outstanding_loan_balance_fixed_fine_frequency'),
                    );

                }else if($outstanding_loan_balance_fine_type == 2){

                    $loan_details += array(
                        'outstanding_loan_balance_percentage_fine_rate' => $this->input->post('outstanding_loan_balance_percentage_fine_rate'),
                        'outstanding_loan_balance_percentage_fine_frequency' => $this->input->post('outstanding_loan_balance_percentage_fine_frequency'),
                        'outstanding_loan_balance_percentage_fine_on' => $this->input->post('outstanding_loan_balance_percentage_fine_on'),
                    );

                }else if($outstanding_loan_balance_fine_type == 3){

                    $loan_details += array(
                        'outstanding_loan_balance_fine_one_off_amount' => $this->input->post('outstanding_loan_balance_fine_one_off_amount'), 
                    );

                }
            }

            if($enable_loan_processing_fee){

                $loan_processing_fee_type  =  $this->input->post('loan_processing_fee_type');
                $disable_automatic_loan_processing_income  =  $this->input->post('disable_automatic_loan_processing_income');

                $loan_details += array(
                    'loan_processing_fee_type' => $loan_processing_fee_type,
                    'disable_automatic_loan_processing_income' => $disable_automatic_loan_processing_income
                );

                if($loan_processing_fee_type == 1){

                    $loan_details += array(
                        'loan_processing_fee_fixed_amount' => $this->input->post('loan_processing_fee_fixed_amount')
                    );

                }else if($loan_processing_fee_type == 2){

                    $loan_details += array(
                        'loan_processing_fee_percentage_rate'=>$this->input->post('loan_processing_fee_percentage_rate'),
                        'loan_processing_fee_percentage_charged_on' =>  $this->input->post('loan_processing_fee_percentage_charged_on')
                    );

                }
            }

                $guarantor_id = $this->input->post('guarantor_id');
                $guaranteed_amount = $this->input->post('guaranteed_amount');
                $guarantor_comment = $this->input->post('guarantor_comment');

                $guarantors = array(
                    'guarantor_id' => $guarantor_id,
                    'guaranteed_amount' => $guaranteed_amount,
                    'guarantor_comment' => $guarantor_comment
                );

                $custom_loan_values = array();

                if($this->input->post('custom_interest_procedure') == 1){

                    $custom_loan_values = array(
                        'date_from' =>  $this->input->post('interest_rate_date_from'),
                        'date_to' =>  $this->input->post('interest_rate_date_to'),
                        'rate' =>  $this->input->post('custom_interest_rate'),
                    );

                }else if($this->input->post('custom_interest_procedure') == 2){

                    $custom_loan_values = array(
                        'payment_date' =>  $this->input->post('custom_payment_date'),
                        'amount_payable' =>  $this->input->post('custom_amount_payable'),
                    );

                }
                
                $id = $this->loan->create_automated_group_loan(
                    $loan_type,
                    $member_id,
                    $group_id,
                    $loan_details,
                    $custom_loan_values,
                    $this->input->post('custom_interest_procedure'),
                    $guarantors
                );

                if($this->input->post('is_a_joint_loan')){

                    $joint_loan_members_id = $this->input->post('joint_loan_members');

                    $could_not_create_joint_loan = array();

                    $is_a_joint_loan = array(
                        'is_a_joint_loan' => 1,
                    );

                    $this->loans_m->update($id,$is_a_joint_loan,FALSE);

                    foreach ($joint_loan_members_id as $joint_loan_member_id){

                        $input = array(
                            'member_id' => $joint_loan_member_id,
                            'loan_id' => $id,
                            'group_id' => $this->group->id,
                            'created_on' => time(),
                            'created_by' => $this->user->id,
                            'is_deleted' => '',
                        );

                        if($joint_loan_members_pairing_id = $this->loans_m->insert_joint_loan_members_pairing($input,FALSE)){

                        }else{
                            $could_not_create_joint_loan[] = true;
                        }

                    }

                    if(count($could_not_create_joint_loan) >= 1){

                        $this->session->set_flashdata('error','Could not join '.count($could_not_create_joint_loan).' members');

                    }
                               
                }
                if($id){
                    //notify the member and guarontors
                    $member = $this->members_m->get($this->input->post('member_id'));
                    $user = $this->members_m->get_group_member_by_user_id('',$this->user->id);
                    $members  = $this->members_m->get_group_member_options();

                    $this->notifications->create(
                        'Loan successfully recorded.',
                        'Your loan of  '.$this->group_currency.' '.number_to_currency($this->input->post('loan_amount')).' disbursed on '.timestamp_to_receipt(strtotime($this->input->post('disbursement_date'))).' has been successfully recorded.',
                        $this->user,
                        $this->user->id,
                        $member->user_id,
                        $this->input->post('member_id'),
                        $this->group->id,
                        'View loan installments',
                        'bank/loans/view_installments/'.$id,
                        3
                    );

                //for guarantors 
                if($this->input->post('enable_loan_guarantors')){
                    foreach ($guarantor_id as $key => $value) {
                        $guarantor_member = $this->members_m->get($value);
                        $this->notifications->create(
                            'Loan Created',
                            'Dear '.$members[$value].', '.$members[$member->id].' has choosen you to guarantee him '.$this->group_currency.' '.number_to_currency($guaranteed_amount[$key]).' for his loan of '.$this->group_currency.' '.number_to_currency($this->input->post('loan_amount')),
                            $this->user,
                            $this->user->id,
                            $guarantor_member->user_id,
                            $value,
                            $this->group->id,
                            'none',
                            'none'.$id,
                            3
                        );
                    }
                }
                

                if($this->input->post('new_item')){
                    redirect('bank/loans/create');
                }else{
                    redirect('bank/loans/view_installments/'.$id,'refresh');
                }
            }else{
                redirect('bank/loans/listing');
            }
        }

        foreach ($this->validation_rules as $key => $field) {
            $field_value = $field['field'];
            $post->$field_value = set_value($field['field']);
        }
        preg_match_all("/\[[^\]]*\]/", $this->sms_template_default,$placeholders);
        $this->data['placeholders'] = isset($placeholders[0])?$placeholders[0]:array();
        $this->data['post'] = $post;
        $this->data['sms_template_default'] = $this->sms_template_default;
        //$this->data['banks'] = $this->banks_m->get_group_bank_options();
        $this->data['bank_account_options'] = $this->bank_accounts_m->get_group_verified_partner_bank_account_options_ids($this->group->id,true);
        $this->data['group_members'] = $this->members_m->get_group_member_options($this->group->id);
        $this->data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $loan_types = $this->loan_types_m->get_options($this->group->id);
        $this->data['loan_repayment_period_type'] = $this->loan->loan_repayment_period_type;
        $this->data['loan_amount_type'] = $this->loan->loan_amount_type;
        $this->data['loan_id'] = '';
        $this->data['loan_to_options'] = $this->loan_to_options;
        $this->data['disbursement_options'] = $this->disbursement_options;
        $this->data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $this->data['post']->account_id = '';
        $this->data['post']->disbursement_option_id = '';
        $this->data['post']->gurantors_id = '';
        $this->data['post']->recipient_account_id = '';
        $this->data['post']->mobile_money_wallet_id = '';
        $this->data['post']->equity_bank_account_id = '';
        $this->data['id']= 0;
        $this->data['bank_account_recipients'] = $this->recipients_m->get_group_bank_account_recipient_options();
        $this->data['mobile_money_account_recipients'] = $this->recipients_m->get_group_mobile_money_account_recipient_options();
        $this->data['loan_types'] = $loan_types;
        if($loan_types){
            if($this->application_settings->enable_online_disbursement){
                $this->template->title(translate('Create  Loan'))->build('bank/form',$this->data);
            }else{
                
                $this->template->title(translate('Create  Loan'))->build('bank/form_loan',$this->data);
            }
        }else{
            $this->template->title(translate('Create  Loan'))->build('bank/no_loan_type_form',$this->data);
        }
    }
    function create(){
        $post = new stdClass();
        if($this->input->post('sms_notifications_enabled')){
            $this->validation_rules[] = array(
                'field' =>  'sms_template',
                'label' =>  'Loan Installment SMS Template',
                'rules' =>  'required|trim'
            );
        }

        if($this->input->post('is_a_joint_loan')){

            $this->validation_rules[] = array(
                'field' => 'joint_loan_members',
                'label' => 'Members',
                'rules' => 'callback__check_joint_loan_members',
            );
        }

        if($this->input->post('enable_loan_fines')){

            $this->validation_rules[] = array(
                'field' =>  'loan_fine_type',
                'label' =>  'Late Loan Fine Type',
                'rules' =>  'required|trim'
            );

            if($this->input->post('loan_fine_type') == 1){

                $this->validation_rules[] = array(
                    'field' =>  'fixed_fine_amount',
                    'label' =>  'Fixed Fine Amount',
                    'rules' =>  'required|trim|currency'
                );

                $this->validation_rules[] = array(
                    'field' =>  'fixed_amount_fine_frequency',
                    'label' =>  'Fixed Amount Fine Frequency',
                    'rules' =>  'required|trim'
                );

                $this->validation_rules[] = array(
                    'field' =>  'fixed_amount_fine_frequency_on',
                    'label' =>  'Fixed Amount Fine Frequency On',
                    'rules' =>  'required|trim'
                );

            }

            if($this->input->post('loan_fine_type') == 2){

                $this->validation_rules[] = array(
                    'field' =>  'percentage_fine_rate',
                    'label' =>  'Percentage(%) Fine Rate',
                    'rules' =>  'required|trim|numeric'
                );

                $this->validation_rules[] = array(
                    'field' =>  'percentage_fine_frequency',
                    'label' =>  'Percentage Fine Frequency',
                    'rules' =>  'required|trim'
                );

                $this->validation_rules[] = array(
                    'field' =>  'percentage_fine_on',
                    'label' =>  'Percentage Fine Frequency on',
                    'rules' =>  'required|trim'
                );

            }

            if($this->input->post('loan_fine_type') == 3){

                $this->validation_rules[] = array(
                    'field' =>  'one_off_fine_type',
                    'label' =>  'One off Fine Type',
                    'rules' =>  'required|trim'
                );

                if($this->input->post('one_off_fine_type') == 1){
                    $this->validation_rules[] = array(
                        'field' =>  'one_off_fixed_amount',
                        'label' =>  'One off Fine Fixed Amount',
                        'rules' =>  'required|trim|currency'
                    );
                }

                if($this->input->post('one_off_fine_type') == 2){

                    $this->validation_rules[] = array(
                        'field' =>  'one_off_percentage_rate',
                        'label' =>  'One off Percentage (%) Rate',
                        'rules' =>  'required|trim|numeric'
                    );

                    $this->validation_rules[] = array(
                        'field' =>  'one_off_percentage_rate_on',
                        'label' =>  'One off Percentage Rate On',
                        'rules' =>  'required|trim'
                    );

                }
            }
        }

        if($this->input->post('enable_outstanding_loan_balance_fines')){

            $this->validation_rules[] = array(
                'field' =>  'outstanding_loan_balance_fine_type',
                'label' =>  'Outstanding Loan FIne Types',
                'rules' =>  'required|trim'
            );

            if($this->input->post('outstanding_loan_balance_fine_type') == 1){
                
                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fine_fixed_amount',
                    'label' =>  'Outstanding Loan Balance FIne Fixed Amount',
                    'rules' =>  'required|trim|currency'
                );
                
                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fixed_fine_frequency',
                    'label' =>  'Outstanding Loan Balance FIne Fixed Frequency Rate',
                    'rules' =>  'required|trim'
                );

            }

            if($this->input->post('outstanding_loan_balance_fine_type') == 2){

                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_percentage_fine_rate',
                    'label' =>  'Outstanding Loan Balance Percentage Rate',
                    'rules' =>  'required|trim|numeric'
                );

                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_percentage_fine_frequency',
                    'label' =>  'Outstanding Loan Balance Percentage Fine Frequency',
                    'rules' =>  'required|trim'
                );

                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_percentage_fine_on',
                    'label' =>  'Outstanding Loan Balance Percentage Rate on',
                    'rules' =>  'required|trim'
                );

            }

            if($this->input->post('outstanding_loan_balance_fine_type') == 3){

                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fine_one_off_amount',
                    'label' =>  'Outstanding Loan Balance One Off Amount',
                    'rules' =>  'required|trim|currency'
                );

            }
        }

        if($this->input->post('enable_loan_processing_fee')){

            $this->validation_rules[] = array(
                'field' =>  'loan_processing_fee_type',
                'label' =>  'Loan Processing Fee Type',
                'rules' =>  'required|trim'
            );

            if($this->input->post('loan_processing_fee_type') == 1){

                $this->validation_rules[] = array(
                    'field' =>  'loan_processing_fee_fixed_amount',
                    'label' =>  'Loan Processing Fee Fixed Amount',
                    'rules' =>  'required|trim|currency'
                );

            }else if($this->input->post('loan_processing_fee_type') == 2){

                $this->validation_rules[] = array(
                    'field' =>  'loan_processing_fee_percentage_rate',
                    'label' =>  'Loan Processing Percentage Value',
                    'rules' =>  'required|trim|numeric'
                ); 

                $this->validation_rules[] = array(
                    'field' =>  'loan_processing_fee_percentage_charged_on',
                    'label' =>  'Loan Processing Fee Charged On',
                    'rules' =>  'required|trim'
                );

            }
        }

        if($this->input->post('enable_loan_guarantors')){

            $this->validation_rules[] = array(
                'field' =>  'guarantor_id[]',
                'label' =>  'Guarontor Name',
                'rules' =>  'callback__verify_guarantor_name'
            );
        }

        if($this->input->post('interest_type') == 3){

            $custom_interest_procedure = $this->input->post('custom_interest_procedure');

            if($this->input->post('custom_interest_procedure') == 1){

                $this->validation_rules[] = array(
                    'field' =>  'interest_rate_date_from',
                    'label' =>  'Interest Rate Date From',
                    'rules' =>  'callback__interest_rate_breakdown'
                );

            }else if($this->input->post('custom_interest_procedure') == 2){
                $this->validation_rules[] = array(
                    'field' =>  'custom_payment_date',
                    'label' =>  'Loan installment payment date',
                    'rules' =>  'callback__installment_breakdown'
                );
            }
        }else{

            $custom_interest_procedure = '';

            $this->validation_rules[] = array(
                'field' =>  'interest_type',
                'label' =>  'Interest Type ',
                'rules' =>  'required|trim|numeric'
            );

            $this->validation_rules[] = array(
                'field' =>  'interest_rate',
                'label' =>  'Interest Rate ',
                'rules' =>  'required|trim|numeric'
            );
        }

        if($this->input->post('grace_period') == "date"){

            $this->validation_rules[] = array(
                'field' =>  'grace_period_date',
                'label' =>  'Grace Period Date',
                'rules' =>  'required|trim'
            );
        }

        $this->form_validation->set_rules($this->validation_rules);

        if($this->form_validation->run()){

            $loan_type =  $this->input->post('loan_type');
            $member_id =  $this->input->post('member_id');
            $group_id  =  $this->group->id;

            $enable_loan_fines = $this->input->post('enable_loan_fines');
            $enable_outstanding_loan_balance_fines = $this->input->post('enable_outstanding_loan_balance_fines');
            $enable_loan_processing_fee = $this->input->post('enable_loan_processing_fee');

            $loan_details = array(
                'disbursement_date' =>  strtotime($this->input->post('disbursement_date')),
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
                'enable_loan_fines' =>  $enable_loan_fines,
                'enable_outstanding_loan_balance_fines'=>$enable_outstanding_loan_balance_fines,
                'enable_loan_processing_fee' => $enable_loan_processing_fee,
                'enable_loan_fine_deferment' => $this->input->post('enable_loan_fine_deferment'),
                'enable_loan_guarantors' => $this->input->post('enable_loan_guarantors'),
                'enable_reducing_balance_installment_recalculation' => $this->input->post('enable_reducing_balance_installment_recalculation')?1:0,
                'active'    =>  1,
                'created_by'    =>  $this->user->id,
                'created_on'    =>  time(),
            );

            if($enable_loan_fines){
                $loan_fine_type = $this->input->post('loan_fine_type');
                $loan_details = $loan_details+array('loan_fine_type'=>$loan_fine_type);
                if($loan_fine_type == 1){

                    $loan_details += array(
                        'fixed_fine_amount' =>  $this->input->post('fixed_fine_amount'),
                        'fixed_amount_fine_frequency'   =>  $this->input->post('fixed_amount_fine_frequency'),
                        'fixed_amount_fine_frequency_on' => $this->input->post('fixed_amount_fine_frequency_on'),
                    );

                }else if($loan_fine_type == 2){

                    $loan_details += array(
                        'percentage_fine_rate'  =>  $this->input->post('percentage_fine_rate'),
                        'percentage_fine_frequency' =>  $this->input->post('percentage_fine_frequency'),
                        'percentage_fine_on'    =>  $this->input->post('percentage_fine_on'),
                    );

                }else if($loan_fine_type == 3){

                    $one_off_fine_type = $this->input->post('one_off_fine_type');

                    $loan_details += array(
                        'one_off_fine_type' => $one_off_fine_type
                    );

                    if($one_off_fine_type == 1){

                        $loan_details += array(
                            'one_off_fixed_amount'  =>  $this->input->post('one_off_fixed_amount')
                        );

                    }else if($one_off_fine_type == 2){

                        $loan_details += array(
                            'one_off_percentage_rate'   =>  $this->input->post('one_off_percentage_rate'),
                            'one_off_percentage_rate_on'    =>  $this->input->post('one_off_percentage_rate_on'),
                        );

                    }
                }
            }

            if($enable_outstanding_loan_balance_fines){

                $outstanding_loan_balance_fine_type = $this->input->post('outstanding_loan_balance_fine_type');

                $loan_details += array(
                    'outstanding_loan_balance_fine_type' => $outstanding_loan_balance_fine_type
                );

                if($outstanding_loan_balance_fine_type == 1){

                    $loan_details += array(
                        'outstanding_loan_balance_fine_type' => $this->input->post('outstanding_loan_balance_fine_type'),
                        'outstanding_loan_balance_fine_fixed_amount' => $this->input->post('outstanding_loan_balance_fine_fixed_amount'),
                        'outstanding_loan_balance_fixed_fine_frequency' => $this->input->post('outstanding_loan_balance_fixed_fine_frequency'),
                    );

                }else if($outstanding_loan_balance_fine_type == 2){

                    $loan_details += array(
                        'outstanding_loan_balance_percentage_fine_rate' => $this->input->post('outstanding_loan_balance_percentage_fine_rate'),
                        'outstanding_loan_balance_percentage_fine_frequency' => $this->input->post('outstanding_loan_balance_percentage_fine_frequency'),
                        'outstanding_loan_balance_percentage_fine_on' => $this->input->post('outstanding_loan_balance_percentage_fine_on'),
                    );

                }else if($outstanding_loan_balance_fine_type == 3){

                    $loan_details += array(
                        'outstanding_loan_balance_fine_one_off_amount' => $this->input->post('outstanding_loan_balance_fine_one_off_amount'), 
                    );

                }
            }

            if($enable_loan_processing_fee){

                $loan_processing_fee_type  =  $this->input->post('loan_processing_fee_type');
                $disable_automatic_loan_processing_income  =  $this->input->post('disable_automatic_loan_processing_income');

                $loan_details += array(
                    'loan_processing_fee_type' => $loan_processing_fee_type,
                    'disable_automatic_loan_processing_income' => $disable_automatic_loan_processing_income
                );

                if($loan_processing_fee_type == 1){

                    $loan_details += array(
                        'loan_processing_fee_fixed_amount' => $this->input->post('loan_processing_fee_fixed_amount')
                    );

                }else if($loan_processing_fee_type == 2){

                    $loan_details += array(
                        'loan_processing_fee_percentage_rate'=>$this->input->post('loan_processing_fee_percentage_rate'),
                        'loan_processing_fee_percentage_charged_on' =>  $this->input->post('loan_processing_fee_percentage_charged_on')
                    );

                }
            }

                $guarantor_id = $this->input->post('guarantor_id');
                $guaranteed_amount = $this->input->post('guaranteed_amount');
                $guarantor_comment = $this->input->post('guarantor_comment');

                $guarantors = array(
                    'guarantor_id' => $guarantor_id,
                    'guaranteed_amount' => $guaranteed_amount,
                    'guarantor_comment' => $guarantor_comment
                );

                $custom_loan_values = array();

                if($this->input->post('custom_interest_procedure') == 1){

                    $custom_loan_values = array(
                        'date_from' =>  $this->input->post('interest_rate_date_from'),
                        'date_to' =>  $this->input->post('interest_rate_date_to'),
                        'rate' =>  $this->input->post('custom_interest_rate'),
                    );

                }else if($this->input->post('custom_interest_procedure') == 2){

                    $custom_loan_values = array(
                        'payment_date' =>  $this->input->post('custom_payment_date'),
                        'amount_payable' =>  $this->input->post('custom_amount_payable'),
                    );

                }
                
                $id = $this->loan->create_automated_group_loan(
                    $loan_type,
                    $member_id,
                    $group_id,
                    $loan_details,
                    $custom_loan_values,
                    $this->input->post('custom_interest_procedure'),
                    $guarantors
                );

                if($this->input->post('is_a_joint_loan')){

                    $joint_loan_members_id = $this->input->post('joint_loan_members');

                    $could_not_create_joint_loan = array();

                    $is_a_joint_loan = array(
                        'is_a_joint_loan' => 1,
                    );

                    $this->loans_m->update($id,$is_a_joint_loan,FALSE);

                    foreach ($joint_loan_members_id as $joint_loan_member_id){

                        $input = array(
                            'member_id' => $joint_loan_member_id,
                            'loan_id' => $id,
                            'group_id' => $this->group->id,
                            'created_on' => time(),
                            'created_by' => $this->user->id,
                            'is_deleted' => '',
                        );

                        if($joint_loan_members_pairing_id = $this->loans_m->insert_joint_loan_members_pairing($input,FALSE)){

                        }else{
                            $could_not_create_joint_loan[] = true;
                        }

                    }

                    if(count($could_not_create_joint_loan) >= 1){

                        $this->session->set_flashdata('error','Could not join '.count($could_not_create_joint_loan).' members');

                    }
                               
                }
                if($id){
                    //notify the member and guarontors
                    $member = $this->members_m->get($this->input->post('member_id'));
                    $user = $this->members_m->get_group_member_by_user_id('',$this->user->id);
                    $members  = $this->members_m->get_group_member_options();

                    $this->notifications->create(
                        'Loan successfully recorded.',
                        'Your loan of  '.$this->group_currency.' '.number_to_currency($this->input->post('loan_amount')).' disbursed on '.timestamp_to_receipt(strtotime($this->input->post('disbursement_date'))).' has been successfully recorded.',
                        $this->user,
                        $this->user->id,
                        $member->user_id,
                        $this->input->post('member_id'),
                        $this->group->id,
                        'View loan installments',
                        'bank/loans/view_installments/'.$id,
                        3
                    );

                //for guarantors 
                if($this->input->post('enable_loan_guarantors')){
                    foreach ($guarantor_id as $key => $value) {
                        $guarantor_member = $this->members_m->get($value);
                        $this->notifications->create(
                            'Loan Created',
                            'Dear '.$members[$value].', '.$members[$member->id].' has choosen you to guarantee him '.$this->group_currency.' '.number_to_currency($guaranteed_amount[$key]).' for his loan of '.$this->group_currency.' '.number_to_currency($this->input->post('loan_amount')),
                            $this->user,
                            $this->user->id,
                            $guarantor_member->user_id,
                            $value,
                            $this->group->id,
                            'none',
                            'none'.$id,
                            3
                        );
                    }
                }
                

                if($this->input->post('new_item')){
                    redirect('bank/loans/create');
                }else{
                    redirect('bank/loans/view_installments/'.$id,'refresh');
                }
            }else{
                redirect('bank/loans/listing');
            }
        }

        foreach ($this->validation_rules as $key => $field) {
            $field_value = $field['field'];
            $post->$field_value = set_value($field['field']);
        }
        preg_match_all("/\[[^\]]*\]/", $this->sms_template_default,$placeholders);
        $this->data['placeholders'] = isset($placeholders[0])?$placeholders[0]:array();
        $this->data['post'] = $post;
        $this->data['sms_template_default'] = $this->sms_template_default;
        //$this->data['banks'] = $this->banks_m->get_group_bank_options();
        $this->data['bank_account_options'] = $this->bank_accounts_m->get_group_verified_partner_bank_account_options_ids(0,true);
        $this->data['group_members'] = $this->members_m->get_group_member_options();
        $this->data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $loan_types = $this->loan_types_m->get_options();
        $this->data['loan_repayment_period_type'] = $this->loan->loan_repayment_period_type;
        $this->data['loan_amount_type'] = $this->loan->loan_amount_type;
        $this->data['loan_id'] = '';
        $this->data['active_group_member_options'] = $this->members_m->get_active_group_member_options();
        $this->data['loan_to_options'] = $this->loan_to_options;
        $this->data['disbursement_options'] = $this->disbursement_options;
        $this->data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $this->data['post']->account_id = '';
        $this->data['post']->disbursement_option_id = '';
        $this->data['post']->gurantors_id = '';
        $this->data['post']->recipient_account_id = '';
        $this->data['post']->mobile_money_wallet_id = '';
        $this->data['post']->equity_bank_account_id = '';
        $this->data['id']= 0;
        $this->data['bank_account_recipients'] = $this->recipients_m->get_group_bank_account_recipient_options();
        $this->data['mobile_money_account_recipients'] = $this->recipients_m->get_group_mobile_money_account_recipient_options();
        $this->data['loan_types'] = $loan_types;
        if($loan_types){
            if($this->application_settings->enable_online_disbursement){
                $this->template->title(translate('Create  Loan'))->build('bank/form',$this->data);
            }else{
                
                $this->template->title(translate('Create  Loan'))->build('bank/form_loan',$this->data);
            }
        }else{
            $this->template->title(translate('Create  Loan'))->build('bank/no_loan_type_form',$this->data);
        }
    }
    function edit_loan($id=0){
        //id= loan_application_id
        $id OR redirect('member/loans/pending_loan_request_listing');
        $loan_application_id = $id;
        $post =  new StdClass();
        $group_id = $this->group->id; 
        $loan_applicant_id = $this->user->id; 
        $currency = $this->group_currency;       
        $get_loan_application_details = $this->loan_applications_m->get($loan_application_id);
        $member_id = $get_loan_application_details->member_id; 
        $get_loan_application_requests_details = $this->loans_m->get_loan_application_guarantorship_requests_by_loan_application_id($loan_application_id,$group_id); 
        foreach ($get_loan_application_requests_details as $key => $loan_request_details)
        { 
            $loan_requests_loan_application_id[] = $loan_request_details->id;
            $data['loan_requests_loan_application_id'][] = $loan_request_details->id;
            $data['guarantor_comment'][] = $loan_request_details->comment;
            $data['guarantor_id'][] = $loan_request_details->guarantor_member_id;
            $data['guaranteed_amount'][] = $loan_request_details->amount;        
           
        }
        $guaranteed_amounts = $this->input->post('guaranteed_amount');
        $guarantor_comment = $this->input->post('guarantor_comment');
        $this->form_validation->set_rules($this->loan_application_rules);
        if($this->form_validation->run()){         
           
           $loan_type_id = $this->input->post('loan_type_id');
           $loan_application_amount =  currency($this->input->post('loan_application_amount'));
           $loan_applicant_agree_to_rules = $this->input->post('loan_rules_check_box');                    
           $get_loan_type_options = $this->loan_types_m->get($loan_type_id,$group_id);
            $guarantor_id = $this->input->post('guarantor_id');
            $get_duplicate_member_id = array_unique( array_diff_assoc( $guarantor_id, array_unique( $guarantor_id)));
            if(!$get_duplicate_member_id){
                if($get_loan_type_options){
                    $minimum_loan_amount_to_borrow = $get_loan_type_options->minimum_loan_amount;
                    $maximum_loan_amount_to_borrow = $get_loan_type_options->maximum_loan_amount; 
                    if($minimum_loan_amount_to_borrow <= $loan_application_amount && $maximum_loan_amount_to_borrow >= $loan_application_amount ){
                        for ($i=0; $i < count($loan_requests_loan_application_id) ; $i++) {                           
                           $data = array(
                            'member_id'=>$member_id,
                            'group_id'=>$group_id,
                            'loan_type_id'=>$loan_type_id,
                            'loan_amount'=>$loan_application_amount,
                            'active'=>1,
                            'agree_to_rules'=>$loan_applicant_agree_to_rules,
                            'created_on'=>time(),
                            'created_by'=>$this->user->id
                        );
                          $loan_application_success = $this->loan_applications_m->update($loan_application_id,$data); //3 // works
                          if($loan_application_success){
                                if($guarantor_id[$i] && $guaranteed_amounts[$i]){
                                    $guarantor_details =  array(
                                        'loan_type_id'=>$loan_type_id,
                                        'loan_request_applicant_user_id'=>$this->user->id,
                                        'loan_request_applicant_member_id'=>$member_id,
                                        'guarantor_member_id'=>$guarantor_id[$i],
                                        'group_id'=>$group_id,
                                        'amount'=>$guaranteed_amounts[$i],                                        
                                        'loan_request_progress_status'=>1,
                                        'comment'=>$guarantor_comment[$i],
                                        'created_on'=>time(),
                                        'created_by'=>$this->user->id
                                    );                                    
                                   $loan_application_request_id = $this->loans_m->update_loan_request_application($loan_requests_loan_application_id[$i],$guarantor_details);
                                   if($loan_application_request_id){
                                    //send message to guarantors                                  
                                    $messaging = $this->messaging->notify_guarantor_about_loan_edit_application_request($group_id,$loan_applicant_id,$loan_type_id ,$guarantor_id[$i] ,$guaranteed_amounts[$i] ,$currency,$loan_application_amount,$member_id,$loan_application_id);                                                                                                         
                                   }else{
                                    $this->session->set_flashdata('error','Loan application failed: could not create loan request details');
                                   }                               
                                }else{

                                }
                          }else{
                             $this->session->set_flashdata('error','Loan application failed: Could not update loan application details');
                          }                           
                        }//end loop
                        redirect('member/loans/pending_loan_request_listing');
                    }else{
                        $this->session->set_flashdata('info','Loan application failed: Ensure your loan application amount is of between '.number_to_currency($minimum_loan_amount_to_borrow).' and '.number_to_currency($maximum_loan_amount_to_borrow));
                    }
                }else{
                     $this->session->set_flashdata('info','Loan application failed: Loan type does not exist');
                } 
            }else{
              $this->session->set_flashdata('error','Loan application failed: Name of a guarntor appears more than once');  
            }        
          
        }else{
            foreach ($this->loan_application_rules as $key => $field) {
               $field_value = $field['field'];
               $post->$field_value = set_value($field['field']);
            }
        }
        $data['loan_application_amount'] = $get_loan_application_details->loan_amount;
        $data['loan_type_id']= $get_loan_application_details->loan_type_id;
        $data['loan_rules_check_box'] = $get_loan_application_details->agree_to_rules;
        $data['group_loan_types_options'] = $this->loan_types_m->get_options($group_id); 
        $post = (object)$data; 
        $data['post'] = $post; 
        //print_r($loan_application_id); die();
        $this->template->title('Edit Loan Request ')->build('bank/apply',$data);
     }

    function void_loan_application($id=0){
        $id OR redirect('member/loans/pending_loan_request_listing');
        $post = new StdClass();
    }

    function _verify_guarantor_name()
    {
        $guarantors = $this->input->post('guarantor_id');
        $member_id = $this->input->post('member_id');
        $guaranteed_amounts = $this->input->post('guaranteed_amount');
        if(count($guarantors)>= 1)
        {
            for($i=0;$i<count($guarantors);$i++)
            {
                if(empty($guarantors[$i]))
                {
                  $this->form_validation->set_message('_verify_guarantor_name','The Guarantor Name field is required');
                    return FALSE;   
                }
                if($guarantors[$i]==$member_id)
                {
                    $this->form_validation->set_message('_verify_guarantor_name','Guarantor number '.++$i.' should not be the same as the member taking the Loan');
                    return FALSE; 
                }
                if(!currency($guaranteed_amounts[$i]) && !empty($guaranteed_amounts[$i]))
                {
                    $this->form_validation->set_message('_verify_guarantor_name','The Guaranteed amount row '.++$i.' must be a valid currency');
                    return FALSE;  
                }
                else{
                    return TRUE;
                }
            }
        }else
        {
            $this->form_validation->set_message('_verify_guarantor_name','Add atleast one guarantor');
            return FALSE;
        }
    }

     function _verify_guarantor_amount_on_edit(){
        $loan_requests_loan_application_id = $this->input->post('loan_requests_loan_application_id');
        die($loan_requests_loan_application_id);
     }

    function _verify_guarantor_amount(){
        $guaranteed_amounts = $this->input->post('guaranteed_amount');
        $loan_application_amount =  currency($this->input->post('loan_application_amount')); 
        $guarantor_id = $this->input->post('guarantor_id');
        if(!isset($guarantor_id)){
            $this->form_validation->set_message('_verify_guarantor_amount','Add atleast one guarantor');
              return FALSE;
        }else{ 
        $bad_symbols = array(",", ".");
        $value = str_replace($bad_symbols, "", $guaranteed_amounts);
        $total_amount_from_guarantors = array_sum($value);
        if($loan_application_amount == $total_amount_from_guarantors){

        }else{
            $this->form_validation->set_message('_verify_guarantor_amount','Loan application failed: amount from guarantors should be equal to the amount of loan applied. You applied for a loan of '.$this->group_currency.' '.$loan_application_amount.' and gurantors amount total to '.$this->group_currency.$total_amount_from_guarantors);
            return FALSE;
        } 
     }      
        
    }

    function _verify_guarantor_details(){
        $guarantor_id = $this->input->post('guarantor_id');       
        $member_id = $this->member->id;
        $loan_type_id = $this->input->post('loan_type_id');
        $group_id = $this->group->id;
        $get_loan_type_options = $this->loan_types_m->get($loan_type_id,$group_id);       
        $guaranteed_amounts = $this->input->post('guaranteed_amount');

        if(!isset($guarantor_id)){
            //
        }else{     
            if($get_loan_type_options){
                if(count($guarantor_id) >= $get_loan_type_options->minimum_guarantors && count($guarantor_id) <= $get_loan_type_options->maximum_guarantors ){                               
                  if(in_array($member_id, $guarantor_id)){                
                    $this->form_validation->set_message('_verify_guarantor_details','Loan application failed: You are not allowed to be your own guarantor');
                    return FALSE;
                  }
                }else{
                   $this->form_validation->set_message('_verify_guarantor_details','Loan application failed: You are allowed to have atleast '.$get_loan_type_options->minimum_guarantors.' and a maximum of '.$get_loan_type_options->maximum_guarantors.' guarantors you have '.count($guarantor_id) .' guarantors');
                   return FALSE;
                }
            }else{
                $this->form_validation->set_message('_verify_guarantor_details','Loan application failed: loan type does not exist');
                return FALSE;
            } 
       } 
    }

    function ajax_create(){
        $post = new stdClass();
        if($this->input->post('sms_notifications_enabled'))
        {
            $this->validation_rules[] = array(
                'field' =>  'sms_template',
                'label' =>  'Loan Installment SMS Template',
                'rules' =>  'required|trim'
            );
        }

        if($this->input->post('enable_loan_fines'))
        {
            $this->validation_rules[] = array(
                    'field' =>  'loan_fine_type',
                    'label' =>  'Late Loan Fine Type',
                    'rules' =>  'required|trim'
                );

            if($this->input->post('loan_fine_type')==1)
            {
                $this->validation_rules[] = array(
                        'field' =>  'fixed_fine_amount',
                        'label' =>  'Fixed Fine Amount',
                        'rules' =>  'required|trim|currency'
                    );
                $this->validation_rules[] = array(
                        'field' =>  'fixed_amount_fine_frequency',
                        'label' =>  'Fixed Amount Fine Frequency',
                        'rules' =>  'required|trim'
                    );
                $this->validation_rules[] = array(
                        'field' =>  'fixed_amount_fine_frequency_on',
                        'label' =>  'Fixed Amount Fine Frequency On',
                        'rules' =>  'required|trim'
                    );
            }
            if($this->input->post('loan_fine_type')==2)
            {
                $this->validation_rules[] = array(
                    'field' =>  'percentage_fine_rate',
                    'label' =>  'Percentage(%) Fine Rate',
                    'rules' =>  'required|trim|numeric'
                );

                $this->validation_rules[] = array(
                    'field' =>  'percentage_fine_frequency',
                    'label' =>  'Percentage Fine Frequency',
                    'rules' =>  'required|trim'
                );

                $this->validation_rules[] = array(
                    'field' =>  'percentage_fine_on',
                    'label' =>  'Percentage Fine Frequency on',
                    'rules' =>  'required|trim'
                );
            }

            if($this->input->post('loan_fine_type')==3)
            {
                $this->validation_rules[] = array(
                    'field' =>  'one_off_fine_type',
                    'label' =>  'One off Fine Type',
                    'rules' =>  'required|trim'
                );

                if($this->input->post('one_off_fine_type')==1)
                {
                    $this->validation_rules[] = array(
                        'field' =>  'one_off_fixed_amount',
                        'label' =>  'One off Fine Fixed Amount',
                        'rules' =>  'required|trim|currency'
                    );
                }
                if($this->input->post('one_off_fine_type')==2)
                {
                    $this->validation_rules[] = array(
                        'field' =>  'one_off_percentage_rate',
                        'label' =>  'One off Percentage (%) Rate',
                        'rules' =>  'required|trim|numeric'
                    );

                    $this->validation_rules[] = array(
                        'field' =>  'one_off_percentage_rate_on',
                        'label' =>  'One off Percentage Rate On',
                        'rules' =>  'required|trim'
                    );
                }
            }
        }

        if($this->input->post('enable_outstanding_loan_balance_fines'))
        {
            $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fine_type',
                    'label' =>  'Outstanding Loan FIne Types',
                    'rules' =>  'required|trim'
                );
            if($this->input->post('outstanding_loan_balance_fine_type')==1){
                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fine_fixed_amount',
                    'label' =>  'Outstanding Loan Balance FIne Fixed Amount',
                    'rules' =>  'required|trim|currency'
                );
                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fixed_fine_frequency',
                    'label' =>  'Outstanding Loan Balance FIne Fixed Frequency Rate',
                    'rules' =>  'required|trim'
                );
            }
            if($this->input->post('outstanding_loan_balance_fine_type')==2){
                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_percentage_fine_rate',
                    'label' =>  'Outstanding Loan Balance Percentage Rate',
                    'rules' =>  'required|trim|numeric'
                );
                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_percentage_fine_frequency',
                    'label' =>  'Outstanding Loan Balance Percentage Fine Frequency',
                    'rules' =>  'required|trim'
                );
                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_percentage_fine_on',
                    'label' =>  'Outstanding Loan Balance Percentage Rate on',
                    'rules' =>  'required|trim'
                );
            }
            if($this->input->post('outstanding_loan_balance_fine_type')==3){
                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fine_one_off_amount',
                    'label' =>  'Outstanding Loan Balance One Off Amount',
                    'rules' =>  'required|trim|currency'
                );
            }
        }

        if($this->input->post('enable_loan_processing_fee'))
        {
            $this->validation_rules[] = array(
                    'field' =>  'loan_processing_fee_type',
                    'label' =>  'Loan Processing Fee Type',
                    'rules' =>  'required|trim'
                );
            if($this->input->post('loan_processing_fee_type')==1){
                $this->validation_rules[] = array(
                    'field' =>  'loan_processing_fee_fixed_amount',
                    'label' =>  'Loan Processing Fee Fixed Amount',
                    'rules' =>  'required|trim|currency'
                );
            }else if($this->input->post('loan_processing_fee_type')==2){
                 $this->validation_rules[] = array(
                    'field' =>  'loan_processing_fee_percentage_rate',
                    'label' =>  'Loan Processing Percentage Value',
                    'rules' =>  'required|trim|numeric'
                );
                 $this->validation_rules[] = array(
                    'field' =>  'loan_processing_fee_percentage_charged_on',
                    'label' =>  'Loan Processing Fee Charged On',
                    'rules' =>  'required|trim'
                );
            }
        }

        if($this->input->post('enable_loan_guarantors'))
        {
            $this->validation_rules[] = array(
                    'field' =>  'guarantor_id[]',
                    'label' =>  'Guarontor Name',
                    'rules' =>  'callback__verify_guarantor_name'
                );
        }

        if($this->input->post('interest_type')==3){
            $custom_interest_procedure = $this->input->post('custom_interest_procedure');
            if($this->input->post('custom_interest_procedure')==1){
                $this->validation_rules[] = array(
                    'field' =>  'interest_rate_date_from',
                    'label' =>  'Interest Rate Date From',
                    'rules' =>  'callback__interest_rate_breakdown'
                );
            }
            else if($this->input->post('custom_interest_procedure')==2){
                $this->validation_rules[] = array(
                    'field' =>  'custom_payment_date',
                    'label' =>  'Loan installment payment date',
                    'rules' =>  'callback__installment_breakdown'
                );
            }
        }else{
            $custom_interest_procedure='';
            $this->validation_rules[] = array(
                    'field' =>  'interest_type',
                    'label' =>  'Interest Type ',
                    'rules' =>  'required|trim|numeric'
                );
             $this->validation_rules[] = array(
                    'field' =>  'interest_rate',
                    'label' =>  'Interest Rate ',
                    'rules' =>  'required|trim|numeric'
                );
        }

        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run())
        {
            $loan_type =  $this->input->post('loan_type');
            $member_id =  $this->input->post('member_id');
            $group_id  =  $this->group->id;

            $enable_loan_fines = $this->input->post('enable_loan_fines');
            $enable_outstanding_loan_balance_fines = $this->input->post('enable_outstanding_loan_balance_fines');
            $enable_loan_processing_fee = $this->input->post('enable_loan_processing_fee');

            $loan_details = array(
                'disbursement_date' =>  strtotime($this->input->post('disbursement_date')),
                'loan_amount'   =>  $this->input->post('loan_amount'),
                'account_id'    =>  $this->input->post('account_id'),
                'repayment_period'  =>  $this->input->post('repayment_period'),
                'interest_rate' =>  $this->input->post('interest_rate'),
                'loan_interest_rate_per' =>  $this->input->post('loan_interest_rate_per'),
                'interest_type' =>  $this->input->post('interest_type'),
                'custom_interest_procedure'=>$custom_interest_procedure,
                'grace_period'  =>  $this->input->post('grace_period'),
                'grace_period_date' => '',
                'sms_notifications_enabled' =>  $this->input->post('sms_notifications_enabled'),
                'sms_template'  =>  $this->input->post('sms_template'),
                'email_notifications_enabled' =>  $this->input->post('email_notifications_enabled'),
                'enable_loan_fines' =>  $enable_loan_fines,
                'enable_outstanding_loan_balance_fines'=>$enable_outstanding_loan_balance_fines,
                'enable_loan_processing_fee' => $enable_loan_processing_fee,
                'enable_loan_fine_deferment' => $this->input->post('enable_loan_fine_deferment'),
                'enable_loan_guarantors' => $this->input->post('enable_loan_guarantors'),
                'enable_reducing_balance_installment_recalculation' => $this->input->post('enable_reducing_balance_installment_recalculation')?1:0,
                'active'    =>  1,
                'created_by'    =>  $this->user->id,
                'created_on'    =>  time(),
            );
                if($enable_loan_fines)
                {
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
                if($enable_outstanding_loan_balance_fines)
                {
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
                if($enable_loan_processing_fee)
                {
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


            $id = $this->loan->create_automated_group_loan($loan_type,$member_id,$group_id,$loan_details,$custom_loan_values,
                $this->input->post('custom_interest_procedure'),
                $guarantors);

            if($id)
            {
                //notify the member and guarontors
                $member = $this->members_m->get($this->input->post('member_id'));
                $user = $this->members_m->get_group_member_by_user_id('',$this->user->id);
                $members  = $this->members_m->get_group_member_options();

                $this->notifications->create(
                    'Loan successfully recorded.',
                    'Your loan of  '.$this->group_currency.' '.number_to_currency($this->input->post('loan_amount')).' disbursed on '.timestamp_to_receipt(strtotime($this->input->post('disbursement_date'))).' has been successfully recorded.',
                    $this->user,
                    $this->user->id,
                    $member->user_id,
                    $this->input->post('member_id'),
                    $this->group->id,
                    'View loan installments',
                    'bank/loans/view_installments/'.$id,
                    3
                );


                //for guarantors
                if($this->input->post('enable_loan_guarantors')){
                    foreach ($guarantor_id as $key => $value) {
                        $guarantor_member = $this->members_m->get($value);
                        $this->notifications->create('Loan Created',
                            'Dear '.$members[$value].', '.$members[$member->id].' has choosen you to guarantee him '.$this->group_currency.' '.number_to_currency($guaranteed_amount[$key]).' for his loan of '.$this->group_currency.' '.number_to_currency($this->input->post('loan_amount')),
                            $this->user,
                            $this->user->id,
                            $guarantor_member->user_id,
                            $value,
                            $this->group->id,
                            'none',
                            'none'.$id,3);
                    }
                }
                if($loan = $this->loans_m->get_group_loan($id)){
                    $this->session->set_flashdata('success',"");
                    $loan->details = $this->group_currency.' '.number_to_currency($loan->loan_amount).' - Disbursed '.timestamp_to_date($loan->disbursement_date,TRUE);
                    echo json_encode($loan);
                }else{
                    echo "Could not find loan";
                }
            }
            else
            {
                echo "Could not create loan";
            }
        }else{
            echo validation_errors();
        }
    }

    function edit($id = 0){ 
        $id or redirect('bank/loans/listing');
        $post = $this->loans_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','The loan does not exist');
            redirect('bank/loans/listing','refresh');
            die;
        }

        if(!$this->loans_m->is_loan_editable($id)){
            $this->session->set_flashdata('error','Sorry you cannot edit this loan. Custom Loans or loans that have been paid for are not editable. Kindly void and recreat the loan');
            redirect('bank/loans/listing','refresh');
            die;
        }

        if($this->input->post('sms_notifications_enabled')){
            $this->validation_rules[] = array(
                'field' =>  'sms_template',
                'label' =>  'Loan Installment SMS Template',
                'rules' =>  'required|trim'
            );
        }

        if($this->input->post('is_a_joint_loan')){
            $this->validation_rules[] = array(
                'field' => 'joint_loan_members',
                'label' => 'Members',
                'rules' => 'callback__check_joint_loan_members',
            );
        }

        if($this->input->post('enable_loan_fines')){

            $this->validation_rules[] = array(
                'field' =>  'loan_fine_type',
                'label' =>  'Late Loan Fine Type',
                'rules' =>  'required|trim'
            );

            if($this->input->post('loan_fine_type') == 1){

                $this->validation_rules[] = array(
                    'field' =>  'fixed_fine_amount',
                    'label' =>  'Fixed Fine Amount',
                    'rules' =>  'required|trim|currency'
                );

                $this->validation_rules[] = array(
                    'field' =>  'fixed_amount_fine_frequency',
                    'label' =>  'Fixed Amount Fine Frequency',
                    'rules' =>  'required|trim'
                );

                $this->validation_rules[] = array(
                    'field' =>  'fixed_amount_fine_frequency_on',
                    'label' =>  'Fixed Amount Fine Frequency On',
                    'rules' =>  'required|trim'
                );

            }

            if($this->input->post('loan_fine_type') == 2){

                $this->validation_rules[] = array(
                    'field' =>  'percentage_fine_rate',
                    'label' =>  'Percentage(%) Fine Rate',
                    'rules' =>  'required|trim|numeric'
                );

                $this->validation_rules[] = array(
                    'field' =>  'percentage_fine_frequency',
                    'label' =>  'Percentage Fine Frequency',
                    'rules' =>  'required|trim'
                );

                $this->validation_rules[] = array(
                    'field' =>  'percentage_fine_on',
                    'label' =>  'Percentage Fine Frequency on',
                    'rules' =>  'required|trim'
                );

            }

            if($this->input->post('loan_fine_type') == 3){

                $this->validation_rules[] = array(
                    'field' =>  'one_off_fine_type',
                    'label' =>  'One off Fine Type',
                    'rules' =>  'required|trim'
                );

                if($this->input->post('one_off_fine_type') == 1){

                    $this->validation_rules[] = array(
                        'field' =>  'one_off_fixed_amount',
                        'label' =>  'One off Fine Fixed Amount',
                        'rules' =>  'required|trim|currency'
                    );

                }

                if($this->input->post('one_off_fine_type') == 2){

                    $this->validation_rules[] = array(
                        'field' =>  'one_off_percentage_rate',
                        'label' =>  'One off Percentage (%) Rate',
                        'rules' =>  'required|trim|numeric'
                    );

                    $this->validation_rules[] = array(
                        'field' =>  'one_off_percentage_rate_on',
                        'label' =>  'One off Percentage Rate On',
                        'rules' =>  'required|trim'
                    );

                }
            }
        }

        if($this->input->post('enable_outstanding_loan_balance_fines')){

            $this->validation_rules[] = array(
                'field' =>  'outstanding_loan_balance_fine_type',
                'label' =>  'Outstanding Loan FIne Types',
                'rules' =>  'required|trim'
            );

            if($this->input->post('outstanding_loan_balance_fine_type') == 1){

                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fine_fixed_amount',
                    'label' =>  'Outstanding Loan Balance FIne Fixed Amount',
                    'rules' =>  'required|trim|currency'
                );

                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fixed_fine_frequency',
                    'label' =>  'Outstanding Loan Balance FIne Fixed Frequency Rate',
                    'rules' =>  'required|trim'
                );

            }

            if($this->input->post('outstanding_loan_balance_fine_type') == 2){

                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_percentage_fine_rate',
                    'label' =>  'Outstanding Loan Balance Percentage Rate',
                    'rules' =>  'required|trim|numeric'
                );

                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_percentage_fine_frequency',
                    'label' =>  'Outstanding Loan Balance Percentage Fine Frequency',
                    'rules' =>  'required|trim'
                );

                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_percentage_fine_on',
                    'label' =>  'Outstanding Loan Balance Percentage Rate on',
                    'rules' =>  'required|trim'
                );

            }

            if($this->input->post('outstanding_loan_balance_fine_type') == 3){

                $this->validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fine_one_off_amount',
                    'label' =>  'Outstanding Loan Balance One Off Amount',
                    'rules' =>  'required|trim|currency'
                );

            }
        }

        if($this->input->post('enable_loan_processing_fee')){

            $this->validation_rules[] = array(
                'field' =>  'loan_processing_fee_type',
                'label' =>  'Loan Processing Fee Type',
                'rules' =>  'required|trim'
            );

            if($this->input->post('loan_processing_fee_type') == 1){

                $this->validation_rules[] = array(
                    'field' =>  'loan_processing_fee_fixed_amount',
                    'label' =>  'Loan Processing Fee Fixed Amount',
                    'rules' =>  'required|trim|currency'
                );

            }else if($this->input->post('loan_processing_fee_type') == 2){

                 $this->validation_rules[] = array(
                    'field' =>  'loan_processing_fee_percentage_rate',
                    'label' =>  'Loan Processing Percentage Value',
                    'rules' =>  'required|trim|numeric'
                ); 
 
                $this->validation_rules[] = array(
                    'field' =>  'loan_processing_fee_percentage_charged_on',
                    'label' =>  'Loan Processing Fee Charged On',
                    'rules' =>  'required|trim'
                );

            }
        }

        if($this->input->post('enable_loan_guarantors')){

            $this->validation_rules[] = array(
                'field' =>  'guarantor_id[]',
                'label' =>  'Guarontor Name',
                'rules' =>  'callback__verify_guarantor_name'
            );

        }

        if($post->enable_loan_guarantors){

            $guarantors_details = $this->loans_m->get_loan_guarantors($id);

            if($guarantors_details){

                foreach ($guarantors_details as $key => $details) {
                    $guarantor_id[] = $details ->guarantor_id;
                    $guaranteed_amount[] = $details->guaranteed_amount;
                    $guarantor_comment[] = $details->guarantor_comment;
                }

                $this->data['guarantors_details'] = array('guarantor_id'=>$guarantor_id,'guaranteed_amount'=>$guaranteed_amount,'guarantor_comment'=>$guarantor_comment);
            }else{
                $this->data['guarantors_details'] = '';
            }
        
        }

        $this->form_validation->set_rules($this->validation_rules);

        if($this->form_validation->run()){

            $loan_type =  $this->input->post('loan_type');
            $member_id =  $this->input->post('member_id');
            $group_id  =  $this->group->id;

            $enable_loan_fines = $this->input->post('enable_loan_fines');
            $enable_outstanding_loan_balance_fines = $this->input->post('enable_outstanding_loan_balance_fines');
            $enable_loan_processing_fee = $this->input->post('enable_loan_processing_fee');

            $loan_details = array(
                'disbursement_date' =>  strtotime($this->input->post('disbursement_date')),
                'loan_amount'   =>  $this->input->post('loan_amount'),
                'account_id'    =>  $this->input->post('account_id'),
                'repayment_period'  =>  $this->input->post('repayment_period'),
                'interest_rate' =>  $this->input->post('interest_rate'),
                'interest_type' =>  $this->input->post('interest_type'),
                'loan_interest_rate_per' =>  $this->input->post('loan_interest_rate_per'),
                'grace_period'  =>  $this->input->post('grace_period'),
                'grace_period_date'  =>  $this->input->post('grace_period_date')?strtotime($this->input->post('grace_period_date')):"",
                'sms_notifications_enabled' =>  $this->input->post('sms_notifications_enabled'),
                'sms_template'  =>  $this->input->post('sms_template'),
                'email_notifications_enabled' =>  $this->input->post('email_notifications_enabled'),
                'enable_loan_fines' =>  $enable_loan_fines,
                'enable_outstanding_loan_balance_fines'=>$enable_outstanding_loan_balance_fines,
                'enable_loan_processing_fee' => $enable_loan_processing_fee,
                'enable_loan_fine_deferment' => $this->input->post('enable_loan_fine_deferment'),
                'enable_loan_guarantors' => $this->input->post('enable_loan_guarantors'),
                'enable_reducing_balance_installment_recalculation' => $this->input->post('enable_reducing_balance_installment_recalculation')?1:0,
                'active'    =>  1,
                'created_by'    =>  $this->user->id,
                'created_on'    =>  time(),
            );

            if($enable_loan_fines){

                $loan_fine_type = $this->input->post('loan_fine_type');

                $loan_details += array(
                    'loan_fine_type'=>$loan_fine_type
                );

                if($loan_fine_type == 1){

                    $loan_details += array(
                        'fixed_fine_amount' =>  $this->input->post('fixed_fine_amount'),
                        'fixed_amount_fine_frequency'   =>  $this->input->post('fixed_amount_fine_frequency'),
                        'fixed_amount_fine_frequency_on' => $this->input->post('fixed_amount_fine_frequency_on'),
                    );

                }else if($loan_fine_type == 2){

                    $loan_details += array(
                        'percentage_fine_rate'  =>  $this->input->post('percentage_fine_rate'),
                        'percentage_fine_frequency' =>  $this->input->post('percentage_fine_frequency'),
                        'percentage_fine_on'    =>  $this->input->post('percentage_fine_on'),
                    );

                }else if($loan_fine_type == 3){

                    $one_off_fine_type = $this->input->post('one_off_fine_type');

                    $loan_details += array(
                        'one_off_fine_type' => $one_off_fine_type
                    );

                    if($one_off_fine_type == 1){

                        $loan_details += array(
                            'one_off_fixed_amount' => $this->input->post('one_off_fixed_amount')
                        );

                    }else if($one_off_fine_type == 2){
                        $loan_details += array(
                            'one_off_percentage_rate' => $this->input->post('one_off_percentage_rate'),
                            'one_off_percentage_rate_on' => $this->input->post('one_off_percentage_rate_on'),
                        );
                    }
                }

            }

            if($enable_outstanding_loan_balance_fines){

                $outstanding_loan_balance_fine_type = $this->input->post('outstanding_loan_balance_fine_type');

                $loan_details += array(
                    'outstanding_loan_balance_fine_type' => $outstanding_loan_balance_fine_type
                );

                if($outstanding_loan_balance_fine_type == 1){

                    $loan_details += array(
                        'outstanding_loan_balance_fine_type' => $this->input->post('outstanding_loan_balance_fine_type'),
                        'outstanding_loan_balance_fine_fixed_amount' => $this->input->post('outstanding_loan_balance_fine_fixed_amount'),
                        'outstanding_loan_balance_fixed_fine_frequency' => $this->input->post('outstanding_loan_balance_fixed_fine_frequency'),
                    );

                }else if($outstanding_loan_balance_fine_type == 2){

                    $loan_details += array(
                        'outstanding_loan_balance_percentage_fine_rate' => $this->input->post('outstanding_loan_balance_percentage_fine_rate'),
                        'outstanding_loan_balance_percentage_fine_frequency' => $this->input->post('outstanding_loan_balance_percentage_fine_frequency'),
                        'outstanding_loan_balance_percentage_fine_on' => $this->input->post('outstanding_loan_balance_percentage_fine_on'),
                    );

                }else if($outstanding_loan_balance_fine_type == 3){

                    $loan_details += array(
                        'outstanding_loan_balance_fine_one_off_amount' => $this->input->post('outstanding_loan_balance_fine_one_off_amount'), 
                    );

                }
            }

            if($enable_loan_processing_fee){

                $loan_processing_fee_type  =  $this->input->post('loan_processing_fee_type');
                $disable_automatic_loan_processing_income  =  $this->input->post('disable_automatic_loan_processing_income');

                $loan_details += array(
                    'loan_processing_fee_type' => $loan_processing_fee_type,
                    'disable_automatic_loan_processing_income' => $disable_automatic_loan_processing_income,
                );

                if($loan_processing_fee_type == 1){

                    $loan_details += array(
                        'loan_processing_fee_fixed_amount' => $this->input->post('loan_processing_fee_fixed_amount'),
                    );

                }else if($loan_processing_fee_type == 2){

                    $loan_details += array(
                        'loan_processing_fee_percentage_rate' => $this->input->post('loan_processing_fee_percentage_rate'),
                        'loan_processing_fee_percentage_charged_on' =>  $this->input->post('loan_processing_fee_percentage_charged_on'),
                    );

                }
            }

            $guarantors = array(
                'guarantor_id' => $this->input->post('guarantor_id'),
                'guaranteed_amount' => $this->input->post('guaranteed_amount'),
                'guarantor_comment' => $this->input->post('guarantor_comment')
            );

            $modified_by = $this->user->id;

            $custom_loan_values = array();

            $custom_interest_procedure = $this->input->post('custom_interest_procedure');

            if($this->input->post('custom_interest_procedure') == 1){

                $custom_loan_values = array(
                    'date_from' =>  $this->input->post('interest_rate_date_from'),
                    'date_to' =>  $this->input->post('interest_rate_date_to'),
                    'rate' =>  $this->input->post('custom_interest_rate'),
                );

            }else if($this->input->post('custom_interest_procedure') == 2){

                $custom_loan_values = array(
                    'payment_date' =>  $this->input->post('custom_payment_date'),
                    'amount_payable' =>  $this->input->post('custom_amount_payable'),
                );

            }

            $loan_details += array('modified_by' => $modified_by); 

            $update = $this->loan->modify_automated_group_loan(
                $id,
                $loan_type,
                $member_id,
                $group_id,
                $loan_details,
                $custom_loan_values,
                $custom_interest_procedure,
                $guarantors,
                FALSE
            );
               
            if($update){
                if($this->input->post('is_a_joint_loan')){
                    $this->loans_m->delete_joint_loan_members_pairing($id);
                    $joint_loan_members_id = $this->input->post('joint_loan_members');
                    $could_not_create_joint_loan = array();
                    $is_a_joint_loan = array(
                        'is_a_joint_loan' => 1,
                    );
                    $this->loans_m->update($update,$is_a_joint_loan,FALSE);
                    foreach ($joint_loan_members_id as $joint_loan_member_id) {
                        $input = array(
                            'member_id' => $joint_loan_member_id,
                            'loan_id' => $update,
                            'group_id' => $this->group->id,
                            'created_on' => time(),
                            'created_by' => $this->user,
                            'is_deleted' => '',
                        );
                        if($joint_loan_members_pairing_id = $this->loans_m->insert_joint_loan_members_pairing($input,FALSE)){

                        }else{
                            $could_not_create_joint_loan[] = true;
                        }
                    }
                    if(count($could_not_create_joint_loan)>=1){
                        $this->session->set_flashdata('error','Could not join '.count($could_not_create_joint_loan).' members');
                    }           
                }else{

                    $is_a_joint_loan = array(
                        'is_a_joint_loan' => 0,
                    );
                    $this->loans_m->update($id,$is_a_joint_loan,FALSE);
                }

                if($this->input->post('new_item')){
                    redirect('bank/loans/create');
                }else{
                    redirect('bank/loans/view_installments/'.$update);
                }
            }else{
                redirect('bank/loans/edit/'.$id);
            }
        }else{
            foreach (array_keys($this->validation_rules) as $field){
                if(isset($_POST[$field])){
                    $post->$field = $this->form_validation->$field;
                }
            }
        }
        $this->data['members'] = $this->members_m->get_group_member_options();
        preg_match_all("/\[[^\]]*\]/", $this->sms_template_default,$placeholders);
        $this->data['placeholders'] = isset($placeholders[0])?$placeholders[0]:array();
        $this->data['group_members'] = $this->members_m->get_group_member_options($this->group->id);; 
        if($post->is_a_joint_loan == 1){
            $post->enable_joint_loan = TRUE;
            $post->joint_loan_members = $this->members_m->get_joint_loan_member_pairings($id);
            //print_r($post->joint_loan_members); die;
        }else{
            $post->enable_joint_loan = FALSE;
        }
        $this->data['post'] = $post;
        //$this->data['loan_types'] = $this->loan_types;
        $this->data['loan_types'] = $this->loan_types_m->get_options($this->group->id);
        $this->data['interest_types'] = $this->interest_types;
        $this->data['loan_grace_periods'] = $this->loan_grace_periods;
        $this->data['accounts'] = $this->accounts;
        $this->data['late_loan_payment_fine_types'] = $this->late_loan_payment_fine_types;
        $this->data['late_payments_fine_frequency'] = $this->late_payments_fine_frequency;
        $this->data['percentage_fine_on'] = $this->percentage_fine_on;
        $this->data['one_off_fine_types'] = $this->one_off_fine_types;
        $this->data['one_off_percentage_rate_on'] = $this->one_off_percentage_rate_on;
        $this->data['loan_processing_fee_types'] =  $this->loan_processing_fee_types;
        $this->data['loan_processing_fee_percentage_charged_on'] = $this->loan_processing_fee_percentage_charged_on;
        $this->data['banks'] = $this->banks_m->get_group_bank_options();
        $this->data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $this->data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $this->data['loan_repayment_period_type'] = $this->loan->loan_repayment_period_type;
        $this->data['loan_amount_type'] = $this->loan->loan_amount_type;
        $this->data['loan_id'] = $id;
        $this->data['bank_account_options'] = $this->bank_accounts_m->get_group_verified_partner_bank_account_options_ids($this->group->id,true);

        $this->data['disbursement_options'] = $this->disbursement_options;
        // $this->data['post']->disbursement_option_id = '';
        // $this->data['post']->gurantors_id = '';
        // $this->data['post']->recipient_account_id = '';
        // $this->data['post']->mobile_money_wallet_id = '';
        // $this->data['post']->equity_bank_account_id = '';
        $this->data['id']= $post->id;
        $this->data['bank_account_recipients'] = $this->recipients_m->get_group_bank_account_recipient_options();
        $this->data['mobile_money_account_recipients'] = $this->recipients_m->get_group_mobile_money_account_recipient_options();
        if($post->interest_type==3){
            $loan_custom_invoices = $this->loan_invoices_m->get_custom_invoices($id);
            $this->data['loan_custom_invoices'] = $loan_custom_invoices;
        }
        $this->data['loan_to_options'] = $this->loan_to_options;
        $this->data['loan_to'] = 2;
        $loan_types = $this->loan_types_m->get_options($this->group->id);
        $this->data['loan_types'] = $loan_types;
        if($loan_types){
            if($this->application_settings->enable_online_disbursement){
                $this->template->title(translate('Edit Member Loan'))->build('bank/form',$this->data);
            }else{
                $this->template->title(translate('Edit Member Loan'))->build('bank/form_loan',$this->data);
            }
        }else{
            $this->template->title(translate('Edit Member Loan'))->build('bank/no_loan_type_form',$this->data);
        }
        // if($this->application_settings->enable_online_disbursement){
        //     $this->template->title(translate('Edit Member Loan'))->build('bank/form',$this->data);
        // }else{
        //     $this->template->title(translate('Edit Member Loan'))->build('bank/form_loan',$this->data);
        // }
        // }else{
        //     $this->template->title(translate('Edit Member Loan'))->build('bank/no_loan_type_form',$this->data);
        // }
    }

    function index(){
        $this->data['loaned_amount'] = $this->loans_m->get_total_member_loan_amount($this->group->id);
        $this->data['total_repayments'] = $this->loans_m->get_total_member_loan_amount_paid($this->group->id);
        $this->data['loan_balances'] = $this->loans_m->get_total_loan_balances($this->group->id);
        $this->data['loan_types_count'] = $this->loan_types_m->count_group_loan_types($this->group->id);
        $from = strtotime('-2 year');
        $to = strtotime('tomorrow');
        $this->data['average_loan_applications_per_month'] = $this->loan_applications_m->count_average_loan_application_per_month($this->group->id,$from,$to);
        $this->data['average_loan_application_amounts'] = $this->loan_applications_m->get_average_loan_application_amounts_per_month($this->group->id,$from,$to);
        $this->data['average_loan_disbursements_per_month_count'] = $this->loans_m->count_average_loan_disbursements_per_month($this->group->id,$from,$to);
        $this->data['average_loan_disbursement_amounts_per_month_count'] = $this->loans_m->get_average_loan_disbursement_amounts_per_month($this->group->id,$from,$to);
        $this->data['total_defaulted_loan_amount'] = $this->loans_m->get_total_defaulted_loan_amount($this->group->id);


        $this->template->title(translate('Group Loans'))->build('bank/index',$this->data);
    }

    function listing(){
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-6 months');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('-3 months');
        // $aging_loan_applications = [];

        // $loans = $this->loans_m->get_group_loans($this->group->id); // 10mins
        $loan_type_options = $this->loan_types_m->get_options();
        // foreach ($loans as $loan) {
        //     if(!$loan->is_fully_paid){
        //         $this->loan_repayments_m->last_loan_repayment_date($loan->id);
        //         $loan->last_loan_repayment_date = $this->loan_repayments_m->last_loan_repayment_date($loan->id);
        //         array_push($aging_loan_applications, $loan);
        //     }
        // }
        // print_r($aging_loan_applications[1]);
        // print_r( $this->loan_repayments_m->last_loan_repayment_date(46));
        
        // die;
        
     

        // $this->data['aging_loan_applications'] = $aging_loan_applications;
        $this->data['loan_type_options'] = $loan_type_options;
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $this->data['additional_member_details'] =  $this->members_m->get_member_additional_fields_data();
        if($this->input->get('generate_excel') == 1){
            $filter_parameters = array(
                'member_id' => $this->input->get('member_id')?:'',
                'accounts' => $this->input->get('accounts')?:'',
                'is_fully_paid' => $this->input->get('is_fully_paid')?:'',
                'from' => $from,
                'to' => $to,
            );
            $this->data['members'] = $this->members_m->get_group_member_options();
            $this->data['posts'] = $this->loans_m->get_group_loans($filter_parameters);
            $this->data['group'] = $this->group;
            $this->data['group_currency'] = $this->group_currency;
            $json_file = json_encode($this->data);
            /*if($this->group->id == 4){
                //print_r($json_file);die;
            }*/
            print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/loans/listing',$this->group->name.' List of Member Loans'));
            die;
        }
        $loan_type_options = $this->loan_types_m->get_options();
         
        $this->data['repayment_status_options'] = $this->repayment_status_options;
        $this->data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
        $this->data['month_options'] = $this->month_options;
        $this->data['loan_type_options'] = $loan_type_options;
        $this->data['additional_member_details'] =  $this->members_m->get_member_additional_fields_data();
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $this->template->title(translate('List User Loans'))->build('bank/listing',$this->data);
    }

    function loan_repayments(){
        $total_rows = $this->loan_repayments_m->count_all_group_loan_repayments();
        $pagination = create_pagination('bank/loans/loan_repayments/pages', $total_rows,50,5,TRUE);
        $posts = $this->loan_repayments_m->limit($pagination['limit'])->get_all_group_loan_repayments();

        $this->accounts = $this->accounts_m->get_group_account_options(FALSE);
        $this->data['accounts'] = $this->accounts;
        $this->data['deposit_method_options'] = $this->transactions->deposit_method_options;
        $this->data['posts'] = $posts;
        $this->data['members'] = $this->members_m->get_group_member_options();

        $this->template->title('List Member Loans')->build('bank/loan_repayments_listing',$this->data);
    }

    function pending_member_loan(){
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $this->data['members'] = $this->members_m->get_group_member_options();
        $this->data['loan_types'] = $this->loan_types_m->get_options($this->group->id);
        $this->data['group'] = $this->group;
        $this->data['group_currency'] = $this->group_currency;        
        $this->data['repayment_status_options'] = $this->repayment_status_options;
        $this->data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);       
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $this->data['roles_holders'] = $this->members_m->get_active_group_role_holder_options($this->group->id);
        $loan_applications = $this->loan_applications_m->get_pending_group_loan_applications($this->group->id);
        foreach ($loan_applications as $key => $loan_application) {
            $loan_application_ids[] = $loan_application->id;
            $loan_type_ids[] = $loan_application->loan_type_id;
            $group_id = $loan_application->group_id;
        }
        $this->data['loan_applications'] = $loan_applications;
        $this->data['guarantor_requests'] = $this->loans_m->get_loan_guarantorship_request_array($loan_application_ids ,$loan_type_ids ,$group_id);
        $this->data['signatory_requests'] = $this->loans_m->get_loan_signatory_request_array($loan_application_ids ,$loan_type_ids ,$group_id);
        $this->template->title('List Pending Member Loans')->build('bank/pending_loan_listing',$this->data);  
    }

    function pending_eazzyclub_member_loan(){
            
        $this->template->title('View Pending Loan requests ')->build('shared/eazzy_club_pending_loan_request', $this->data); 
    }

    function view_installments($id=0,$generate_pdf=FALSE)
    {
        $id or redirect('bank/loans/listing');
        $posts = $this->loan_invoices_m->get_loan_installments($id);
        if(!$posts)
        {
            $this->session->set_flashdata('error','The loan has no installments to display');
            redirect('bank/loans/listing');
            die;
        }
        $loan = $this->loans_m->get_loan_and_member($id);
        if($loan->is_a_joint_loan == 1){
            $loan->joint_loan_members = $this->loans_m->get_joint_loan_joint_members($id);
        }
        $this->data['loan'] = $loan;
        $this->data['posts'] = $posts;
        $this->data['loan_types'] = $this->loan_types;
        $this->data['interest_types'] = $this->interest_types;
        $this->data['members'] = $this->members_m->get_group_member_options();
        $this->data['late_loan_payment_fine_types'] = $this->late_loan_payment_fine_types;
        $this->data['late_payments_fine_frequency'] = $this->late_payments_fine_frequency;
        $this->data['percentage_fine_on'] = $this->percentage_fine_on;
        $this->data['one_off_fine_types'] = $this->one_off_fine_types;
        $this->data['one_off_percentage_rate_on'] = $this->one_off_percentage_rate_on;
        $this->data['loan_processing_fee_types'] =  $this->loan_processing_fee_types;
        $this->data['loan_processing_fee_percentage_charged_on'] = $this->loan_processing_fee_percentage_charged_on;
        $this->data['loan_guarantors'] = $this->loans_m->get_loan_guarantors($id);
        $this->data['accounts'] = $this->accounts;
        $this->data['group'] = $this->group;
        $this->data['group_currency'] = $this->group_currency;
        $this->data['application_settings'] = $this->application_settings;
        $this->data['transfer_options'] = $this->transfer_options;
        $loan_type_options = $this->loan_types_m->get_options();
        $this->data['loan_type_options'] = $loan_type_options;
        $type = isset($loan_type_options[$loan->loan_type_id])?$loan_type_options[$loan->loan_type_id]:translate('Normal Loan');
        if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
            $this->data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
        }else{
            $this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
        }
        if($generate_pdf==TRUE){
            $this->data['pdf_true'] = TRUE;
            $html = $this->load->view('bank/view_installments',$this->data,TRUE);
            $this->pdf_library->generate_landscape_report($html);
            die;
            /*$response = $this->curl_post_data->curl_post_json_pdf((json_encode($this->data)),'https://pdfs.chamasoft.com/loan_installment',$loan->first_name.' '.$type.' Installment Breakdown - '.$this->group->name);
            print_r($response);die;*/
        }else{
            $this->template->title($loan->first_name.' '.$loan->last_name.' '.$type.' Installments')->build('bank/view_installments',$this->data);
        }
    }

    function loan_statement($id=0,$generate_pdf=FALSE){
        $id or redirect('bank/loans/listing');
        $loan = $this->loans_m->get_loan_and_member($id);
        if(!$loan){
            $this->session->set_flashdata('info','Sorry the loan does not exist');
            redirect('bank/loans/listing');
        }

        $total_installment_payable = $this->loan_invoices_m->get_total_installment_loan_payable($id);
        $total_fines = $this->loan_invoices_m->get_total_loan_fines_payable($id);
        $total_transfers_out = $this->loan_invoices_m->get_total_loan_transfers_out($id);
        $total_paid = $this->loan_repayments_m->get_loan_total_payments($id);
        $loan_balance =$this->loans_m->get_loan_balance($id);


        $posts = $this->loans_m->get_loan_statement($id);
        $this->data['loan'] = $loan;
        $this->data['posts'] = $posts;
        $this->data['total_installment_payable'] = $total_installment_payable;
        $this->data['total_fines'] = $total_fines;
        $this->data['total_transfers_out'] = $total_transfers_out;
        $this->data['total_paid'] = $total_paid;
        $this->data['lump_sum_remaining'] = $this->loan_invoices_m->get_loan_lump_sum_as_date($id);
        $this->accounts = $this->accounts_m->get_group_account_options(FALSE);
        $this->data['accounts'] = $this->accounts;
        $this->data['loan_grace_periods'] = $this->loan_grace_periods;
        $this->data['deposit_options']=$this->transactions->deposit_method_options;
        $this->data['group'] = $this->group;
        $this->data['group_currency'] = $this->group_currency;
        $this->data['application_settings'] = $this->application_settings;
        $this->data['transfer_options'] = $this->transfer_options;
       // print_r($this->data['posts']); die();
        if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
            $this->data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
        }else{
            $this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
        }
        if($generate_pdf==TRUE){
            $this->data['pdf_true'] = TRUE;
            $html = $this->load->view('shared/loan_statement',$this->data,TRUE);
            $this->pdf_library->generate_landscape_report($html);
            die;
            $json_file = (json_encode($this->data));
            $response = $this->curl_post_data->curl_post_json_pdf($json_file,'https://pdfs.chamasoft.com/loan_statement',$loan->first_name.' Loan Statement - '.$this->group->name);
            print_r($response);die;
        }else{
            $this->template->title($loan->first_name.' '.$loan->last_name.' Loan Statement')->build('shared/loan_statement',$this->data); 
        }
    }

    function loan_invoices_sent_list(){
        $posts= $this->loan_invoices_m->get_all_group_sent_loan_invoices();

        $this->data['posts'] = $posts;
        $this->template->title('Sent Member Loan Invoices')->build('bank/sent_invoices',$this->data);
    }

    function calculator(){
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
                die('am in');
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
            $field_value = $field['field'];
            $post->$field_value = set_value($field['field']);
        }

        $this->data['post'] = $post;
        $this->data['total_amount_payable']=$total_amount_payable;
        $this->data['total_principle_amount'] = $total_principle_amount;
        $this->data['total_interest'] = $total_interest;
        $this->data['loan_types'] = $this->loan_types_m->get_options($this->group->id);
        $this->data['loan_values'] = $loan_values;
        $this->data['monthly_payment'] = $monthly_payment;
        $this->data['loan_repayment_period_type'] = $this->loan->loan_repayment_period_type;
        $this->data['loan_amount_type'] = $this->loan->loan_amount_type;
        $this->template->title('Loan Calculator')->build('shared/calculator',$this->data);
    }

    function calculator_old(){
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

    function _interest_rate_breakdown()
    {
        $i=0;
        $interest_rate_date_from = $this->input->post('interest_rate_date_from');
        $interest_rate_date_to = $this->input->post('interest_rate_date_to');
        $custom_interest_rate = $this->input->post('custom_interest_rate');
        foreach ($interest_rate_date_from as $key => $interest) 
        {++$i;
           if(empty($interest)){
                $this->form_validation->set_message('_interest_rate_breakdown','`Apply Rate From date` at line '.$i.' is required');
                return FALSE;
           }
           else if(empty($interest_rate_date_to[$key]))
           {
                $this->form_validation->set_message('_interest_rate_breakdown','`Apply Rate To Date at line` '.$i.' is required');
                return FALSE;
           }
           else if(empty($custom_interest_rate[$key])){
                if(is_numeric($custom_interest_rate[$key])){}
                else{
                    $this->form_validation->set_message('_interest_rate_breakdown','The custom loan interest rate field at line '.$i.' is required');
                    return FALSE;
                }
           }
           else{
                if($interest>=$interest_rate_date_to[$key]){
                    $this->form_validation->set_message('_interest_rate_breakdown','Apply Rate From date from at line '.$i.' can not be bigger than `Apply Rate To Date`');
                    return FALSE;
                }
                if(isset($interest_rate_date_to[$key-1]))
                    if($interest!=$interest_rate_date_to[$key-1]){
                    $this->form_validation->set_message('_interest_rate_breakdown','`Apply Rate From date`, at line '.++$key.', should be equal to `Apply Rate To Date` at line '.--$i);
                    return FALSE;
                }
           }
        }

        return TRUE;
    }

    function _installment_breakdown()
    {
        $i=0;
        $max=0;
        $min = '31-12-2035';
        $sum_amount_payable = 0;
        $date_payable = $this->input->post('custom_payment_date');
        $amount_payable = $this->input->post('custom_amount_payable');
        $loan_amount = $this->input->post('loan_amount');
        $repayment_period = $this->input->post('repayment_period');
        $disbursement_date = $this->input->post('disbursement_date');

        foreach ($date_payable as $key => $value) {
            ++$i;
            if(empty($value)){
                $this->form_validation->set_message('_installment_breakdown','`Installment expected payment date` field at line '.$i.' is required');
                return FALSE;
            }
            if(empty($amount_payable[$key])){
                $this->form_validation->set_message('_installment_breakdown','`Amount payable` field at line '.$i.' is required');
                return FALSE;
            }
            if(strtotime($value)>strtotime($max))
            {
                $max = $value;
            }
            if(strtotime($value)<strtotime($min))
            {
                $min = $value;
            }
            $sum_amount_payable += currency($amount_payable[$key]);
        }

        if($max && $min){
            $disbursement_date = $disbursement_date?:time();
            $diff = round((strtotime($max)-strtotime($min)+2419000)/(604800*4));
            if($diff<$repayment_period)
            {
                $this->form_validation->set_message('_installment_breakdown','The installment breakdown payment period is less than the loan repayment period as stated');
                return FALSE;
            }
        }
        
        else if($loan_amount > $sum_amount_payable){
            $this->form_validation->set_message('_installment_breakdown','Amount Payable is less than the loan amount');
                return FALSE;
        }
        else if($strtotime($min)<strtotime($disbursement_date))
        {
            $this->form_validation->set_message('_installment_breakdown','Loan repayment date cannot be before the loan disbursement date');
                return FALSE;
        }
        else
        {
            return TRUE;
        }

    }


    function void($id=0,$redirect=TRUE)
    {
        
        $withdrawal = $this->withdrawals_m->get_group_withdrawal_by_loan_id($id,$this->group->id);
        
        if($withdrawal&&$id){
            
            if($this->transactions->void_group_withdrawal($withdrawal->id,$withdrawal,TRUE,$this->group->id)){
                if($redirect){
                    redirect('bank/loans/listing');
                }
                return TRUE;
            }else{
                if($redirect){
                    redirect('bank/loans/listing');
                }
                return FALSE;
            }
        }else{
            $this->session->set_flashdata('error','Kindly pass all parameters');
            if($redirect){
                redirect('bank/loans/listing');
            }
            return FALSE;
        }
    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_void'){
            for($i=0;$i<count($action_to);$i++){
                $this->void($action_to[$i],FALSE);
            }
            redirect($this->agent->referrer());
        }else{
            redirect('bank/loans/listing');
        }
    }

    function ajax_get_active_member_loans(){
        $member_id = $this->input->post('member_id');
        $attribute = $this->input->post('attribute');
        $loan_id = $this->input->post('loan_id')?:'';
        $no_add_loan = $this->input->post('no_add_loan');
        if(preg_match('/members/',$attribute)){
            $value =explode('members[', $attribute);
            $value = explode(']', $value[1]);

            $att = $value[0];
        }
        else{
            $att = $attribute;
        }
        if($member_id){
            $loans = $this->loans_m->get_active_member_loans_option($member_id);
            if($loans){
                if($no_add_loan){
                    echo '<i class="" ></i>'.form_dropdown('loans['.$att.']',array(''=>'Select a loan')+$loans,$loan_id,' class="form-control  loan new_loan"');
                }else{
                    echo '<i class="" ></i>'.form_dropdown('loans['.$att.']',array(''=>'Select a loan')+$loans,$loan_id,' class="form-control  loan new_loan"');
                    // echo '<i class="" ></i>'.form_dropdown('loans['.$att.']',array(''=>'Select a loan')+$loans+array('0'=>"Add Loan"),'',' class="form-control  loan new_loan"');
                }
            }else{
                if($no_add_loan){
                    echo  '<i class="" ></i>'.form_dropdown('loans['.$att.']',array(''=>'Member has no active loans'),'',' class="form-control  loan new_loan"');
                }else{
                    echo  '<i class="" ></i>'.form_dropdown('loans['.$att.']',array(''=>'Member has no active loans'),'',' class="form-control  loan new_loan"');
                    // echo  '<i class="" ></i>'.form_dropdown('loans['.$att.']',array(''=>'Member has no active loans')+array('0'=>"Add Loan"),'',' class="form-control  loan new_loan"');
                }
            }
        }else{
            echo '<i class="" ></i>'.form_dropdown('loans['.$att.']',array(''=>'Select Member first'),'',' class="form-control  loan new_loan"');
        }
    }

    function ajax_get_active_debtor_loans(){
        $debtor_id = $this->input->post('debtor_id');
        $attribute = $this->input->post('attribute');
        $no_add_loan = 1;
        if(preg_match('/debtors/',$attribute)){
            $value =explode('debtors[', $attribute);
            $value = explode(']', $value[1]);

            $att = $value[0];
        }
        else{
            $att = $attribute;
        }
        if($debtor_id){
            $loans = $this->loans_m->get_active_debtor_loans_option($debtor_id);
            if($loans){
                if($no_add_loan){
                    echo '<i class="" ></i>'.form_dropdown('external_loans['.$att.']',array(''=>'Select a loan')+$loans,'',' class="form-control  loan new_loan"');
                }else{
                    echo '<i class="" ></i>'.form_dropdown('external_loans['.$att.']',array(''=>'Select a loan')+$loans+array('0'=>"Add Loan"),'',' class="form-control  loan new_loan"');
                }
            }else{
                if($no_add_loan){
                    echo  '<i class="" ></i>'.form_dropdown('external_loans['.$att.']',array(''=>'Borrower has no active loans'),'',' class="form-control  loan new_loan"');
                }else{
                    echo  '<i class="" ></i>'.form_dropdown('external_loans['.$att.']',array(''=>'Borrower has no active loans')+array('0'=>"Add Loan"),'',' class="form-control  loan new_loan"');
                }
            }
        }else{
            echo '<i class="" ></i>'.form_dropdown('external_loans['.$att.']',array(''=>'Select Borrower first'),'',' class="form-control  loan new_loan"');
        }
    }

    function ajax_get_active_external_loans(){
        $debtor_id = $this->input->post('debtor_id');
        $attribute = $this->input->post('attribute');
        $no_add_loan = 1;
        if(preg_match('/debtors/',$attribute)){
            $value =explode('debtors[', $attribute);
            $value = explode(']', $value[1]);

            $att = $value[0];
        }
        else{
            $att = $attribute;
        }
        if($debtor_id){
            $loans = $this->loans_m->get_active_debtor_loans_option($debtor_id);
            if($loans){
                if($no_add_loan){
                    echo '<i class="" ></i>'.form_dropdown('loans['.$att.']',array(''=>'Select a loan')+$loans,'',' class="form-control  loan new_loan"');
                }else{
                    echo '<i class="" ></i>'.form_dropdown('loans['.$att.']',array(''=>'Select a loan')+$loans+array('0'=>"Add Loan"),'',' class="form-control  loan new_loan"');
                }
            }else{
                if($no_add_loan){
                    echo  '<i class="" ></i>'.form_dropdown('loans['.$att.']',array(''=>'Borrower has no active loans'),'',' class="form-control  loan new_loan"');
                }else{
                    echo  '<i class="" ></i>'.form_dropdown('loans['.$att.']',array(''=>'Borrower has no active loans')+array('0'=>"Add Loan"),'',' class="form-control  loan new_loan"');
                }
            }
        }else{
            echo '<i class="" ></i>'.form_dropdown('loans['.$att.']',array(''=>'Select Borrower first'),'',' class="form-control  loan new_loan"');
        }
    }


    function ajax_get_active_member_loans_for_transfer(){
        $member_id = $this->input->post('member_id');
        $loan_from_id = $this->input->post('loan_from_id');
        $loan_to_id = $this->input->post('loan_to_id');
        $arr = array();
        if($member_id){
            $loans = $this->loans_m->get_member_loans_option($member_id,TRUE);
            if($loans){
                $arr = array(
                    'loan_from'=>form_dropdown('loan_from_id',array(''=>'Select a loan')+$loans,$loan_from_id,'class="form-control m-select2" id="loan_from_id"'),
                    'loan_to'=>form_dropdown('loan_to_id',array(''=>'Select a loan')+$loans,$loan_to_id,'class="form-control m-select2" id="loan_to_id"')
                    );
                echo json_encode($arr);
            }else{
                $arr = array(
                        'loan_from'=>form_dropdown('loan_from_id',array(''=>'Member has no active loans to transfer from'),'','class="form-control m-select2" id="loan_from_id"'),
                        'loan_to'=>form_dropdown('loan_to_id',array(''=>'Member has no active loans to transfer from'),'','class="form-control m-select2" id="loan_to_id"'),
                        );
                echo json_encode($arr);
            }
        }else{
            $arr = array(
                    'loan_from'=>form_dropdown('loan_from_id',array(''=>'Select Member first'),'',' class="form-control m-select2" id="loan_from_id"'),
                    'loan_to'=>form_dropdown('loan_to_id',array(''=>'Select Member first'),'',' class="form-control m-select2" id="loan_to_id"'),
                    );
            echo json_encode($arr);
        }
    }

    function ajax_get_active_member_loans_to_transfer(){
        $member_id = $this->input->post('member_id');
        $member_loan_to_id = $this->input->post('member_loan_to_id');
        $arr = array();
        if($member_id){
            $loans = $this->loans_m->get_member_loans_option($member_id,TRUE);
            if($loans){
                $arr = array(
                    'member_loan_to_id'=>form_dropdown('member_loan_to_id',array(''=>'Select a loan')+$loans,$member_loan_to_id,'class="form-control m-select2" id="member_loan_to_id_select"')
                    );
                echo json_encode($arr);
            }else{
                $arr = array(
                        'member_loan_to_id'=>form_dropdown('member_loan_to_id',array(''=>'Member has no active loans to transfer to'),'','class="form-control m-select2" id="member_loan_to_id_select"'),
                        );
                echo json_encode($arr);
            }
        }else{
            $arr = array(
                'member_loan_to_id'=>form_dropdown('member_loan_to_id',array(''=>'Select Member first'),'',' class="form-control m-select2" id="member_loan_to_id_select"'),
            );
            echo json_encode($arr);
        }
    }

    protected $review_rules = array(
            array(
                'field' => 'review_report',
                'label' => 'Review Report',
                'rules' => 'trim|required|numeric'
            ),
            array(
                'field' => 'account_id',
                'label' => 'Account To Disburse',
                'rules' => 'trim'
            ),
            array(
                'field' => 'decline_message',
                'label' => 'Review Report',
                'rules' => 'trim'
            ),
        );

    function _additional_review_rules(){
        if($this->input->post('review_report')==1){
            $this->review_rules[] = array(
                'field' => 'account_id',
                'label' => 'Account To Disburse',
                'rules' => 'trim|required'
            );
        }else if($this->input->post('review_report')==2){
            $this->review_rules[] =  array(
                'field' => 'decline_message',
                'label' => 'Reason for declining',
                'rules' => 'trim|required|max_length[200]'
            );
        }
    }

    function reveiw_applicaion($id=0){
        $id OR redirect('bank/loans/loan_applications');
        $post = $this->loan_applications_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry the loan application does not exist');
            redirect('bank/loans/loan_applications');
        }
        if($post->status==3){
            $this->session->set_flashdata('error','Sorry the loan has already been processed and rolled out');
            redirect('bank/loans/loan_applications');
        }

        $this->loan_applications_m->update($post->id,array('status'=>2));
        $post = $this->loan_applications_m->get($id);
        $this->_additional_review_rules();

        $this->form_validation->set_rules($this->review_rules);

        if($this->form_validation->run()){
            if($this->_account_balance_available_for_disburse($post->loan_amount)){
                $data = array(
                        'review_report' => $this->input->post('review_report'),
                        'modified_by' => $this->user->id,
                        'modified_on' => time(),
                    );
                if($this->input->post('review_report')==1){
                    $data = $data + array(
                        'account_id' => $this->input->post('account_id'),
                        'is_approved' => 1,
                        'status' => 3,
                    );
                }else if($this->input->post('review_report')==2){
                    $data = $data + array(
                        'decline_message' => $this->input->post('decline_message'),
                        'is_declined' => 1,
                        'status' => 4,
                    );
                    $this->session->set_flashdata('success','Loan Application denie.;');
                }
                if($this->loan_applications_m->update($post->id,$data)){
                    $this->session->set_flashdata('success','Loan successfully reviewed');
                    $this->messaging->notify_member_about_loan_application($post->id,$this->group_currency);
                    if($this->input->post('review_report')==1){
                        if($this->loan->api_disburse_loan($id,$this->group_currency)){
                            $this->session->set_flashdata('success','Awaiting loan disbursement');
                        }else{
                            $this->session->set_flashdata('error','Loan not disbursed');
                        }
                    }
                }else{
                    $this->session->set_flashdata('error','Error reviewing loan');
                }
                redirect('bank/loans/loan_applications');
            }else{
                $this->session->set_flashdata('error','This loan cannot be disbursed. Not enough money in the account');
            }
            
        }
        foreach (array_keys($this->review_rules) as $field)
        {
             if (isset($_POST[$field])){
                $post->$field = $this->form_validation->$field;
            }
        }

        $loan_type= $this->loan_types_m->get($post->loan_type_id);
        $this->data['loan_type'] = $loan_type;
        $this->data['accounts'] = $this->accounts_m->get_group_account_options('',TRUE);
        $this->data['post'] = $post;
        $this->data['application_review_reports'] = $this->loan->application_review_reports;
        $this->template->set_layout('default_full_width.html')->title('Review Loan')->build('bank/review_form',$this->data);
    }

    function _account_balance_available_for_disburse($amount=0){
        $account_id = $this->input->post('account_id');
        if($account_id && $this->input->post('review_report')==1){
           $amount_available = $this->accounts_m->get_group_account_balance($account_id);
           if(currency($amount_available) > currency($amount)){
                return true;
           }else{
                return FALSE;
           }
        }else if($this->input->post('review_report')==2){
            return true;
        }
    }

    function redisburse_application($id = 0){
        $id OR redirect('bank/loans/loan_applications');
        if($this->loan->api_disburse_loan($id,$this->group_currency)){
            $this->session->set_flashdata('success','Awaiting loan disbursement');
        }else{
            $this->session->set_flashdata('error','Loan not disbursed');
        }
        redirect('bank/loans/loan_applications');
    }

    function mark_as_a_bad_loan($id = 0){
        $id OR redirect('bank/loans/listing');
        $loan = $this->loans_m->get_group_loan($id);
        $loan OR redirect('bank/loans/listing');
        $input = array(
            'is_a_bad_loan' => 1,
            'modified_by' => $this->user->id,
            'modified_on' => time(),
        );
        if($this->loans_m->update($loan->id,$input)){
            $this->session->set_flashdata('success','Loan marked as a bad loan');
        }else{
            $this->session->set_flashdata('error','Loan could not be marked as a bad loan');
        }
        redirect('bank/loans/listing');
    }

    function unmark_as_a_bad_loan($id = 0){
        $id OR redirect('bank/loans/listing');
        $loan = $this->loans_m->get_group_loan($id);
        $loan OR redirect('bank/loans/listing');
        
        $input = array(
            'is_a_bad_loan' => '',
            'modified_by' => $this->user->id,
            'modified_on' => time(),
        );
        if($this->loans_m->update($loan->id,$input)){
            $this->session->set_flashdata('success','Loan marked as a bad loan');
        }else{
            $this->session->set_flashdata('error','Loan could not be marked as a bad loan');
        }
        redirect('bank/loans/listing');

    }

    function export_latest_loan_installments($month_count = 0,$year = ""){
        //$this->data = array();
        $month_count = $this->input->get('month_count');
        $year = $this->input->get('year');
        $this->data['active_group_member_options'] = $this->active_group_member_options;
        //$this->data['loans'] = $this->loans_m->get_active_group_loans();
        $this->data['group_currency'] = $this->group_currency;
        $this->data['interest_types'] = $this->loan->interest_types;
        $this->data['loan_interest_rate_per'] = $this->loan->loan_interest_rate_per;
        $this->data['group'] = $this->group;
        $this->data['group_currency'] = $this->group_currency;
        $this->data['loan_installments'] = $this->loan_invoices_m->get_months_loan_installments($this->group->id,$month_count,$year);
        $json_file = json_encode($this->data);
        print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/loans/months_installments_summary',$this->group->name.' monthly installments template'));
        die;
    }

    function test_loan_application($id = 0){

        $id OR redirect('member/loans/pending_loan_request_listing');
        $loan_application = $this->loan_applications_m->get($id);

        $loan_type = $this->loan_types_m->get($loan_application->loan_type_id);

        $this->data['guarantor_progress'] = $this->loans_m->get_group_loan_gurantorship_progress_status_request_array($loan_application->group_id , $loan_application->loan_type_id , $id);
        $members = $this->members_m->get_user('13524');
        print_r($members);
        print_r($this->data);
    }

    function check_loan_for_group($id=0){
        $id OR redirect('bank/loans/'); 
        $loans = $this->loans_m->get_specific_active_group_loans($id);
        print_r($loans);
    }


    function get_group_loan_overpayment_total(){
        $this->loans_m->get_group_loan_overpayments($this->group->id);
    }

    function delete_we_kif_up_loans(){
        die();
        $count = 0;
        $loans = $this->loans_m->get_active_group_loans('3735');
        if($loans){
            foreach ($loans as $key => $loan):
                $input = array(
                    'active'=>0,
                    //'modified_by'=>,
                    'modified_on'=>time(),
                );
                $count++;
                $this->loans_m->update($loan->id,$input);
            endforeach;
        }
        
        echo $count ." voided";
    }

    

   
}