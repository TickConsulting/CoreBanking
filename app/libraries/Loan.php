<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Loan{

    protected $ci;

    public $interest_types = array(
            1   =>  'Fixed Balance',
            2   =>  'Reducing Balance',
            3   =>  'Custom Interest Type'
        );
    public $loan_interest_rate_per = array(
        1   =>  'Per Day',
        2   =>  'Per Week',
        3   =>  'Per Month',
        4   =>  'Per Annum',
        5   =>  'For the whole loan repayment period'
    );
    public $late_loan_payment_fine_types = array(
            3   =>  'A One off Fine Amount per Installment',
            1   =>  'A Fixed Fine Amount',
            2   =>  'A Percentage (%) Fine',
        );
    public $late_payments_fine_frequency = array(
            1   =>  'Daily',
            2   =>  'Weekly',
            3   =>  'Monthly',
            4   =>  'Yearly'
        );
    public $fixed_amount_fine_frequency_on = array(
            1 => 'Each Outstanding Installment',
            2 => 'Total Outstanding Balance',
        );

    public $percentage_fine_on = array(
            1   =>  'Loan Installment Balance',
            2   =>  'Loan Amount',
            3   =>  'Total Unpaid Loan Amount',
            4   =>  'Installment Interest',
        );
    public $one_off_fine_types = array(
            1   =>  'Fixed Fine',
            2   =>  'Percentage Fine'
        );
    public $one_off_percentage_rate_on = array(
            1   =>  'Loan Installment Balance',
            2   =>  'Loan Amount',
            3   =>  'Total Unpaid Loan Amount',
            4   =>  'Installment Interest',
        );

    public $loan_processing_fee_types = array(
            1   =>  'A Fixed Amount',
            2   =>  'A Percentage (%) Value'
        );

    public $loan_processing_fee_percentage_charged_on = array(
            1   =>  'Total Loan Amount',
            2   =>  'Total Loan Principle plus Interest',
        );

    public $loan_grace_periods = array(
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

    public $loan_days = array(
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

    public $sms_template_default = 'Hi [FIRST_NAME], you have been invoiced [GROUP_CURRENCY] [INVOICED_AMOUNT], for your [LOAN_TYPE] group loan payable before [DUE_DATE]. Total Loan balance is [GROUP_CURRENCY] [LOAN_BALANCE].';

     public $loan_application_stages = array(
            1 => 'Awaiting Review',
            2 => 'In progress',
            3 => 'Accepted',
            4 => 'Declined',
        );

    public $application_review_reports = array(
            1 => 'Approve',
            2 => 'Decline',
        );

    public $transfer_options = array(
            1 => 'To contribution share',
            2 => 'To fines ',
            3 => 'To another loan',
            4 => 'To another member',
        );

    public $loan_repayment_period_type = array(
        1=>  'Fixed Repayment Period',
        2=>  'Varying Repayment Period',
    );
    public $loan_amount_type = array(
        1=>'Based on Amount Range',
        2=>'Based On Member Savings',
    );
    
    public function __construct(){
        $this->ci= & get_instance();
        $this->ci->load->library('ion_auth');
        $this->ci->load->model('loans/loans_m');
        $this->ci->load->model('wallets/wallets_m');
        $this->ci->load->model('loan_invoices/loan_invoices_m');
        $this->ci->load->model('loan_types/loan_types_m');
        $this->ci->load->model('loan_repayments/loan_repayments_m');
        $this->ci->load->model('emails/emails_m');
        $this->ci->load->model('sms/sms_m');
        $this->ci->load->library('notifications');
        $this->ci->load->library('transactions');
        $this->ci->load->library('messaging');
        $this->ci->load->model('countries/countries_m');
        $this->ci->load->model('withdrawals/withdrawals_m');
        $this->ci->load->model('deposits/deposits_m');
        $this->ci->load->model('bank_loans/bank_loans_m');
        $this->ci->load->model('loan_applications/loan_applications_m');
        $this->ci->load->model('loan_types/loan_types_m');
        $this->ci->load->model('debtors/debtors_m');
        $this->ci->load->model('groups/groups_m');
        $this->ci->load->model('members/members_m');

        set_time_limit(0);
        ini_set('memory_limit','2048M');
        ini_set('max_execution_time', 1200);
    }

    public function create_automated_group_loan($loan_type,$member_id,$group_id,$loan_details=array(),$custom_loan_values=array(),$custom_rate_procedure='',$guarantors=array(),$is_a_back_dating_record = FALSE){
        if(array_key_exists('loan_amount', $loan_details) && $member_id){
            //create an object of loan details
            /**
                1. Create the Loan details
                2. Create the loan invoices
                3. Penalize for the past unpaid invoices
                4. In case there isprocessing fee
                    1. Calculate the processing fee amount
                    2. insert the processing fee amount as a group contribution
                5. call the transaction to deduct money and create a statement.

            ***/

            $loan_details_object = (object)$loan_details;
            
            $id =$this->ci->loans_m->insert(
                array(
                'loan_type' => $loan_type,
                'member_id' => $member_id,
                'group_id' => $group_id,
                'loan_end_date' => strtotime('+ '.$loan_details_object->repayment_period.' months',$loan_details_object->disbursement_date),
                'grace_period_end_date' => strtotime('+ '.$loan_details_object->grace_period.' months',$loan_details_object->disbursement_date),
                        ) + $loan_details);

                if($id){
                    if(preg_match('/bank-/', $loan_details_object->account_id)){
                        $account_id = str_replace('bank-','',$loan_details_object->account_id);
                        $type = 9;
                    }else if(preg_match('/sacco-/', $loan_details_object->account_id)){
                        $type = 10;
                        $account_id = str_replace('sacco-','',$loan_details_object->account_id);
                    }else if(preg_match('/mobile-/', $loan_details_object->account_id)){
                        $type = 11;
                        $account_id = str_replace('mobile-','',$loan_details_object->account_id);
                    }else if(preg_match('/petty-/', $loan_details_object->account_id)){
                        $type = 12;
                        $account_id = str_replace('petty-','',$loan_details_object->account_id);
                    }else{
                        $type = 0;
                    }
                    if($account_id){
                        if($type){
                            $input = array(
                                'type' => $type,
                                'group_id' => $group_id,
                                'withdrawal_date' => $loan_details_object->disbursement_date,
                                'loan_type_id' => isset($loan_details_object->loan_type_id)?$loan_details_object->loan_type_id:'',
                                'member_id' => $member_id,
                                'loan_id' => $id,                            
                                'withdrawal_method' => 1,
                                'account_id' => $loan_details_object->account_id,
                                'amount' => $loan_details_object->loan_amount,
                                'description' => '',
                                'active' => 1,
                                'created_on' => time(),
                                'created_by' => $loan_details_object->created_by,
                                'member_id' => $member_id,
                                'is_a_back_dating_record' =>  $is_a_back_dating_record?1:0,
                            );
                            if($withdrawal_id = $this->ci->withdrawals_m->insert($input)){
                                $transaction_type = $type;
                                // if($this->ci->transactions->withdrawal($group_id,$withdrawal_id,$transaction_type,$loan_details_object->disbursement_date,$loan_details_object->account_id,$loan_details_object->loan_amount,'','','','','',$id,'','','',isset($loan_details_object->account_id)?$loan_details_object->account_id:'','',$member_id,0,0,0,$is_a_back_dating_record)){
                        
                                    if($loan_details_object->interest_type==1|| $loan_details_object->interest_type==2){
                                        $loan_invoices = $this->calculate_loan_balance_invoice($loan_details_object->loan_amount,$loan_details_object->interest_type,$loan_details_object->interest_rate,$loan_details_object->repayment_period,$loan_details_object->grace_period,$loan_details_object->disbursement_date,$loan_details_object->loan_interest_rate_per,$loan_details_object->grace_period_date,$loan_details_object->loan_type_id);
                                    }else if($loan_details_object->interest_type==3){
                                        $loan_invoices = $this->calculate_loan_balance_invoice_for_custom($loan_details_object->loan_amount,$custom_rate_procedure,$loan_details_object->repayment_period,$loan_details_object->disbursement_date,$custom_loan_values,$is_a_back_dating_record);
                                    }
                                    
                                    if($loan_invoices){ 
                                        $invoice_start_number = $this->ci->loan_invoices_m->calculate_invoice_no($group_id);
                                        $invoice_numbers = array();
                                        $member_ids = array();
                                        $processing_fees=array();
                                        $group_ids = array();
                                        $loan_ids = array();
                                        $types = array();
                                        $is_sents = array();
                                        $interest_amount_payables = array();
                                        $principle_amount_payable = array();
                                        $invoice_date = array();
                                        $due_date = array();
                                        $fine_date = array();
                                        $amount_payable = array();
                                        $amount_paid = array();
                                        $active = array();
                                        $created_by = array();
                                        $created_on = array();
                                        $is_a_back_dating_record = array();
                                        foreach ($loan_invoices as $key => $invoice) {
                                            
                                            $invoice = (object)$invoice;
                                            $invoice_numbers[] = $invoice_start_number+$key;
                                            $member_ids[] = $member_id;
                                            $group_ids[] = $group_id;
                                            $loan_ids[] = $id;
                                            $types[] = 1;
                                            $is_sents[] = 0;
                                            $interest_amount_payables[] = $invoice->interest_amount_payable;
                                            $principle_amount_payables[] = $invoice->principle_amount_payable;
                                            $invoice_dates[]  = $invoice->invoice_date;
                                            $processing_fees[]  = $invoice->processing_fee;
                                            $due_dates[]  = $invoice->due_date;
                                            $fine_dates[]  = $invoice->fine_date;
                                            $amount_payables[]  = ($invoice->amount_payable)+($invoice->processing_fee);
                                            $amount_paids[]  = 0;
                                            $actives[]  = 1;
                                            $created_bys[]  = $loan_details_object->created_by;
                                            $created_ons[]  = time();
                                            $is_a_back_dating_records[] = $is_a_back_dating_record?:0;
                                            /*$invoice_id[] = $this->ci->loan_invoices_m->insert();*/
                                        }
                                        $input = array(
                                                'invoice_no'    => $invoice_numbers,
                                                'member_id'     => $member_ids,
                                                'group_id'      => $group_ids,
                                                'loan_id'       =>  $loan_ids,
                                                'type'          =>  $types,
                                                'is_sent'       =>  $is_sents,
                                                'interest_amount_payable'   => $interest_amount_payables,
                                                'principle_amount_payable'  => $principle_amount_payables,
                                                'invoice_date'  =>  $invoice_dates,
                                                'processing_fee'=>$processing_fees,
                                                'due_date'      =>  $due_dates,
                                                'fine_date'     =>  $fine_dates,
                                                'amount_payable'=>  $amount_payables,
                                                'amount_paid'   =>  $amount_paids,
                                                'active'        =>  $actives,
                                                'created_by'    =>  $created_bys,
                                                'created_on'    =>  $created_ons,
                                                'is_a_back_dating_record'    =>  $is_a_back_dating_records,
                                            );
                                        if($this->ci->loan_invoices_m->insert_batch($input)){

                                        }

                                        $past_invoice_id = $this->send_past_invoices($id);
                                        if($past_invoice_id){
                                            $outstanding_balance_fine_date = $this->set_outstanding_balance_fine_date($id);
                                            if($outstanding_balance_fine_date)
                                            {
                                                $fix_loan_invoices_fine = $this->fix_loan_invoices_fine($id);
                                                if($fix_loan_invoices_fine){
                                                    $loan = $this->ci->loans_m->get($id);
                                                    $outstanding_balance_fixer = $this->outstanding_loan_balance_fine_fixer($loan);
                                                    if($outstanding_balance_fixer)
                                                    {
                                                        if($loan_details_object->enable_loan_processing_fee){
                                                            //loan processing
                                                            if($loan_details_object->disable_automatic_loan_processing_income){

                                                            }else{
                                                                $loan_processing_fee_amount = $this->calculate_and_record_loan_processing_fee($id);
                                                                if($loan_processing_fee_amount){
                                                                    $this->ci->session->set_flashdata('success','Loan amount successfully recorded');
                                                                }else{
                                                                    $this->ci->session->set_flashdata('error','Unable to record loan processing');
                                                                }
                                                            }
                                                        }
                                                        //create guarantors;
                                                        if($loan_details_object->enable_loan_guarantors)
                                                        {
                                                            if(is_array($guarantors))
                                                            {
                                                                $guarantors = (object)$guarantors;
                                                                $loan_guarantors = array();
                                                                foreach ($guarantors->guarantor_id as $key => $value) 
                                                                {
                                                                    $loan_guarantors = array(
                                                                        'member_id' =>  $value,
                                                                        'group_id'  =>  $group_id,
                                                                        'loan_id'   =>  $id,
                                                                        'guaranteed_amount' =>  $guarantors->guaranteed_amount[$key],
                                                                        'comment'   =>  $guarantors->guarantor_comment[$key],
                                                                        'active'    =>  1,
                                                                        'created_on'    => time(),
                                                                        'created_by'    => $loan_details_object->created_by,
                                                                    );
                                                                    $this->ci->loans_m->insert_loan_guarontors($loan_guarantors);

                                                                }
                                                                return $id;
                                                                // if($this->ci->loans_m->batch_insert_guarantors($loan_guarantors))
                                                                // {
                                                                //     $this->ci->session->set_flashdata('success','Loan Successfully Created and Guarantors added');
                                                                //     return $id;
                                                                // }
                                                                // else
                                                                // {
                                                                //     $this->ci->session->set_flashdata('info','Loan Successfully created but unable to add guarantors');
                                                                //     return FALSE;
                                                                // }
                                                            }
                                                            else
                                                            {
                                                                $this->ci->session->set_flashdata('info','Loan Successfully created but unable to add guarantors');
                                                                return $id;
                                                            }
                                                        }else
                                                        {
                                                            $this->ci->session->set_flashdata('success','Loan Successfully Created');
                                                            return $id;
                                                        }

                                                    }
                                                    else{
                                                        return FALSE;
                                                    }

                                                }
                                            }
                                            else
                                            {
                                                $this->ci->session->set_flashdata('error','Unable to set the outstanding balance fine date');
                                                return FALSE;
                                            }
                                        }
                                        else
                                        {
                                            $this->ci->session->set_flashdata('error','Unable to send past invoices');
                                            return FALSE;
                                        }
                                    }
                                    else
                                    {
                                        $this->ci->session->set_flashdata('error','Unable to create loan and its installments');
                                        return FALSE;
                                    }
                                // }else{
                                //     $this->ci->loans_m->update($id,array('active'=>'','modified_on'=>time()));
                                //     $this->ci->session->set_flashdata('error','Unable to create withdrawal statement');
                                //     return FALSE;
                                // }
                            }else{
                                $this->ci->loans_m->update($id,array('active'=>'','modified_on'=>time()));
                                $this->ci->session->set_flashdata('error','Unable to record withdrawal');
                                return FALSE; 
                            }
                        }else{
                            $this->ci->session->set_flashdata('error','Unable to record withdrawal, no type found');
                            return FALSE;  
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Ensure all loan details are included');
                        return FALSE;
                    }
                }
                else
                {
                    $this->ci->session->set_flashdata('error','Unable to create loan');
                    return FALSE;
                }
        }
        else
        {
            $this->ci->session->set_flashdata('error','Ensure all loan details are included');
            return FALSE;
        }
    }


    public function create_debtor_loan($debtor_id=0,$group_id=0,$loan_details=array(),$custom_loan_values=array(),$custom_rate_procedure='',$guarantors=array(),$is_a_back_dating_record=FALSE){
        if($group_id&&$debtor_id&&$loan_details){
            $loan_details_object = (object)$loan_details;
            $loan_data = array(
                    'debtor_id'=>$debtor_id,
                    'group_id'=>$group_id,
                    'loan_end_date'=>strtotime('+ '.$loan_details_object->repayment_period.' months',$loan_details_object->disbursement_date),
                    'grace_period_end_date'=>strtotime('+ '.$loan_details_object->grace_period.' months',$loan_details_object->disbursement_date),
                );
            $loan_data = array_merge($loan_data,$loan_details);
            if($id = $this->ci->debtors_m->insert_loan($loan_data)){
                if(preg_match('/bank-/', $loan_details_object->account_id)){
                    $type = 33;
                }else if(preg_match('/sacco-/', $loan_details_object->account_id)){
                    $type = 34;
                }else if(preg_match('/mobile-/', $loan_details_object->account_id)){
                    $type = 35;
                }else if(preg_match('/petty-/', $loan_details_object->account_id)){
                    $type = 36;
                }else{
                    $type = 0;
                }
                if($type){
                    $input = array(
                        'type'=>$type,
                        'group_id' => $group_id,
                        'withdrawal_date' => $loan_details_object->disbursement_date,
                        'debtor_loan_id' => $id,                            
                        'withdrawal_method' => 1,
                        'account_id' => $loan_details_object->account_id,
                        'amount' => $loan_details_object->loan_amount,
                        'description' => '',
                        'active' => 1,
                        'created_on' => time(),
                        'created_by' => $loan_details_object->created_by,
                        'debtor_id' => $debtor_id,
                        'is_a_back_dating_record' =>  $is_a_back_dating_record?1:0,
                    );
                    if($withdrawal_id = $this->ci->withdrawals_m->insert($input)){
                        $transaction_type = $type;
                            if($this->ci->transactions->withdrawal($group_id,$withdrawal_id,$transaction_type,$loan_details_object->disbursement_date,$loan_details_object->account_id,$loan_details_object->loan_amount,'','','','','','','','','','','','',0,0,0,$is_a_back_dating_record,$debtor_id,$id)){
                                $loan_invoices = array();
                                if($loan_details_object->interest_type==1|| $loan_details_object->interest_type==2){
                                    $loan_invoices = $this->calculate_loan_balance_invoice($loan_details_object->loan_amount,$loan_details_object->interest_type,$loan_details_object->interest_rate,$loan_details_object->repayment_period,$loan_details_object->grace_period,$loan_details_object->disbursement_date,$loan_details_object->loan_interest_rate_per);
                                }else if($loan_details_object->interest_type==3){
                                    $loan_invoices = $this->calculate_loan_balance_invoice_for_custom($loan_details_object->loan_amount,$custom_rate_procedure,$loan_details_object->repayment_period,$loan_details_object->disbursement_date,$custom_loan_values,$is_a_back_dating_record);
                                }
                                if($loan_invoices){
                                    foreach ($loan_invoices as $loan_invoice) {
                                        $loan_invoice = (object)$loan_invoice;
                                        $loan_invoice_data = array();
                                        $loan_invoice_data = array(
                                            'debtor_loan_id' => $id,
                                            'group_id' => $group_id,
                                            'debtor_id' => $debtor_id,
                                            'invoice_no' => $this->ci->debtors_m->calculate_invoice_no($group_id),
                                            'type' => 1,
                                            'is_sent' => 0,
                                            'interest_amount_payable' => $loan_invoice->interest_amount_payable,
                                            'principle_amount_payable' => $loan_invoice->principle_amount_payable,
                                            'invoice_date' => $loan_invoice->invoice_date,
                                            'due_date' => $loan_invoice->due_date,
                                            'fine_date' => $loan_invoice->fine_date,
                                            'amount_payable' => $loan_invoice->amount_payable,
                                            'amount_paid' => 0,
                                            'active' => 1,
                                            'created_by' => $loan_details_object->created_by,
                                            'created_on' => time(),
                                            'is_a_back_dating_record' => $is_a_back_dating_record?:0,
                                        );
                                        if($loan_invoice_data){
                                            $this->ci->debtors_m->insert_loan_invoice($loan_invoice_data);
                                        }
                                        unset($loan_invoice_data);
                                    }  
                                    if($this->send_past_invoices($id,TRUE)){
                                        if($this->set_outstanding_balance_fine_date($id,TRUE)){
                                           if($this->fix_loan_invoices_fine($id,TRUE)){
                                                $loan = $this->ci->debtors_m->get_loan($id,$group_id); 
                                                if($this->outstanding_loan_balance_fine_fixer($loan,TRUE)){
                                                    if($this->calculate_and_record_loan_processing_fee($id,'',TRUE)){
                                                        $this->ci->debtors_m->delete_guarantors($id);
                                                        if($guarantors){
                                                            $guarantor_count = count($guarantors);
                                                            $guarantors = (object)$guarantors;
                                                            $guarantor_id = $guarantors->guarantor_id;
                                                            $guaranteed_amount = $guarantors->guaranteed_amount;
                                                            $guarantor_comment = $guarantors->guarantor_comment;
                                                            for($i=0;$i<$guarantor_count;$i++){
                                                                $guarantor_data = array(
                                                                    'debtor_loan_id' => $id,
                                                                    'group_id' => $group_id,
                                                                    'member_id' => $guarantor_id[$i],
                                                                    'guaranteed_amount' => currency($guaranteed_amount[$i]),
                                                                    'comment' => $guarantor_comment[$i],
                                                                    'active' => 1 ,
                                                                    'created_by' => $loan_details_object->created_by,
                                                                    'created_on'   => time(),
                                                                );
                                                                $this->ci->debtors_m->insert_loan_guarantor($guarantor_data);
                                                            }
                                                            $this->update_loan_invoices($id,TRUE);
                                                            return $id;
                                                        }else{
                                                            $this->update_loan_invoices($id,TRUE);
                                                            return $id;
                                                        }
                                                    }else{
                                                        return FALSE;
                                                    }
                                                }else{
                                                    return FALSE;
                                                }
                                           }else{
                                                return FALSE;
                                           }
                                        }else{
                                            return FALSE;
                                        }
                                    }else{
                                        return FALSE;
                                    }
                                }else{
                                    $this->ci->debtors_m->update_loan($id,array('active'=>'','modified_on'=>time()));
                                    $this->ci->session->set_flashdata('error','Unable to create Loan invoices');
                                    return FALSE;
                                } 
                            }else{
                                $this->ci->debtors_m->update_loan($id,array('active'=>'','modified_on'=>time()));
                                $this->ci->session->set_flashdata('error','Unable to create withdrawal statement');
                                return FALSE;
                            }
                    }else{
                        //void loan
                        $this->ci->debtors_m->update_loan($id,array('active'=>'','modified_on'=>time()));
                        $this->ci->session->set_flashdata('error','Unable to create withdrawal entry');
                        return FALSE;
                    }
                }else{
                    //void loan
                    $this->ci->debtors_m->update_loan($id,array('active'=>'','modified_on'=>time()));
                    $this->ci->session->set_flashdata('error','Unable to get type');
                    return FALSE;
                }

            }else{
                $this->ci->session->set_flashdata('error','Error on creating loan');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Parameters missing');
            return FALSE;
        }
    }

    function void_loan($id=0,$modified_by=array(),$unreconcile_transaction_alerts = TRUE){
        if($id && $modified_by){
            $loan = $this->ci->loans_m->get($id);
            if($loan){
                /* 
                    1. void guarantors
                    2. void invoices
                    3. void processing contribution
                    3. void payments
                    4. void statement
                    5. void loan
                */
                if($loan->active){
                    if($this->ci->loans_m->void_loan_guarantors($loan->id)){
                        if($this->ci->loan_invoices_m->void_all_invoices($loan->id)){
                            if($this->ci->transactions->void_loan_processing_income('','',$loan->id,FALSE,'',$loan->group_id,$unreconcile_transaction_alerts)){
                                $this->void_loan_repayment('','',$loan->id,TRUE,$unreconcile_transaction_alerts,2);
                                $this->ci->loans_m->void_statement_entries($loan->id);
                                if($this->ci->loans_m->update($loan->id,array('active'=>'','modified_by'=>$modified_by->id,'modified_on'=>time()))){
                                    if($loan->loan_application_id){
                                        if($this->ci->loan_applications_m->update($loan->loan_application_id,array('status'=>0,'modified_by'=>$modified_by->id,'modified_on'=>time()))){
                                            $this->ci->session->set_flashdata('success','Loan Successfully voided');
                                            return TRUE;
                                        }
                                    }else{
                                        $this->ci->session->set_flashdata('success','Loan Successfully voided');
                                        return TRUE;
                                    }
                                }else{
                                }  
                            }else{
                            }
                        }else{
                        }
                    }else{
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Loan already voided');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Loan not found');
                return FALSE;
            }
        }else{
            return FALSE;
        }

    }

    function manage_contribution_transfer($loan_repayment=array(),$id=0){
        if($loan_repayment&&$id){
            if($loan_repayment->incoming_contribution_transfer_id){
                $statement = $this->ci->statements_m->get($loan_repayment->incoming_contribution_transfer_id);
                if($statement){
                    $update = array(
                        'loan_to_id' => $id,
                        'modified_on' => time(),
                    );
                    $this->ci->statements_m->update($statement->id,$update);
                    $this->ci->deposits_m->update_contribution_transfer($statement->contribution_transfer_id,$update);
                }
                return TRUE;
            }
        }else{
            return FALSE;
        }
    }

    function migrate_transfers_out($loan_id=0,$id=0){
        if($loan_id&&$id){
            $transfered_invoices = $this->ci->loans_m->get_transfer_out_invoice($loan_id);
            if($transfered_invoices){
                foreach ($transfered_invoices as $transfered_invoice) {
                    $statement = $this->ci->statements_m->get_group_contribution_transfer_by_loan_invoice($transfered_invoice->loan_invoice_id,$transfered_invoice->group_id);
                    if($statement){
                        $transfer_statement = $this->ci->statements_m->get_group_contribution_transfer_by_contribution_transfer_id($statement->contribution_transfer_id);
                    }
                    $contribution_transfer_id = (($statement)?$statement->contribution_transfer_id:'');
                    $invoice_id = $this->create_loan_transfer_invoice($transfered_invoice->member_id,$id,$transfered_invoice->transfer_to,$transfered_invoice->amount,$transfered_invoice->transaction_date,$transfered_invoice->group_id,$contribution_transfer_id,2);
                    if($invoice_id){
                        if($statement){
                            $this->ci->statements_m->update($statement->id,array(
                                'loan_transfer_invoice_id'=>$invoice_id,
                                'loan_from_id' => $id,
                            ));

                            $this->ci->deposits_m->update_contribution_transfer($statement->contribution_transfer_id,array(
                                'loan_from_id' => $id,
                            ));
                            $this->ci->statements_m->update($transfer_statement->id,array(
                                'loan_from_id' => $id,
                            ));
                        }
                        $this->ci->loans_m->update_statement($transfered_invoice->id,array('loan_invoice_id'=>$invoice_id));
                    }
                }
            } 
        }else{
            return FALSE;
        }
    }

    public function modify_automated_group_loan($loan_id,$loan_type,$member_id,$group_id,$loan_details=array(),$custom_loan_values=array(),$custom_rate_procedure='',$guarantors=array(),$is_a_back_dating_record = FALSE){
        
        if(array_key_exists('loan_amount', $loan_details) && $member_id){
            $loan_details_object = (object)$loan_details;
            $loan = $this->ci->loans_m->get($loan_id);
            if($loan){
                /* 
                    1. void withdrawal
                    2. void invoices
                    3. void guarantors
                    4. void processing contribution
                    5. void statement
                    6. void loan
                */ 
                if($loan->active){
                    $input = array(
                        'loan_type' => $loan_type,
                        'member_id' => $member_id,
                        'group_id' => $group_id,
                        'loan_end_date' => strtotime('+'.$loan_details_object->repayment_period.' months',$loan_details_object->disbursement_date),
                        'grace_period_end_date' => strtotime('+ '.$loan_details_object->grace_period.' months',$loan_details_object->disbursement_date),
                    ) + $loan_details;
                    if($id =$this->ci->loans_m->insert($input)){
                        $withdrawal = $this->ci->withdrawals_m->get_withdrawal_by_loan_id($loan->id,$group_id);
                        
                        if(preg_match('/bank-/', $loan_details_object->account_id)){
                            $type = 9;
                        }else if(preg_match('/sacco-/', $loan_details_object->account_id)){
                            $type = 10;
                        }else if(preg_match('/mobile-/', $loan_details_object->account_id)){
                            $type = 11;
                        }else if(preg_match('/petty-/', $loan_details_object->account_id)){
                            $type = 12;
                        }else{
                            $type = 0;
                        }
                        if($type){
                            $input = array(
                                'type' => $type,
                                'group_id' => $group_id,
                                'withdrawal_date' => $loan_details_object->disbursement_date,
                                'member_id' => $member_id,
                                'loan_id' => $id,                            
                                'withdrawal_method' => 1,
                                'account_id' => $loan_details_object->account_id,
                                'amount' => $loan_details_object->loan_amount,
                                'description' => '',
                                'active' => 1,
                                'created_on' => time(),
                                'created_by' => $loan_details_object->modified_by,
                                'member_id' => $member_id,
                                'is_a_back_dating_record' =>  $is_a_back_dating_record?1:0,
                            );
                            if($withdrawal_id = $this->ci->withdrawals_m->insert($input)){
                                $transaction_type = $type;
                                if($this->ci->transactions->withdrawal($group_id,$withdrawal_id,$transaction_type,$loan_details_object->disbursement_date,$loan_details_object->account_id,$loan_details_object->loan_amount,'','','','','',$id,'','','','','',$member_id,0,0,0,$is_a_back_dating_record)){
                                    if($loan_details_object->interest_type==1|| $loan_details_object->interest_type==2){
                                        $loan_invoices = $this->calculate_loan_balance_invoice($loan_details_object->loan_amount,$loan_details_object->interest_type,$loan_details_object->interest_rate,$loan_details_object->repayment_period,$loan_details_object->grace_period,$loan_details_object->disbursement_date,$loan_details_object->loan_interest_rate_per,$loan_details_object->grace_period_date);
                                    }else if($loan_details_object->interest_type==3){
                                        $loan_invoices = $this->calculate_loan_balance_invoice_for_custom($loan_details_object->loan_amount,$custom_rate_procedure,$loan_details_object->repayment_period,$loan_details_object->disbursement_date,$custom_loan_values,$is_a_back_dating_record);
                                    }
                                    if($withdrawal){
                                        $transaction_alert_id = $withdrawal->transaction_alert_id;
                                        if($transaction_alert_id){
                                            $this->ci->transactions->match_loan_disbursement_to_transaction_alert($group_id,$id,$transaction_alert_id);
                                            $this->ci->transactions->mark_transaction_alert_as_reconciled($transaction_alert_id,$group_id);
                                        }
                                    }
                                    if($loan_invoices){  
                                        $old_invoices = $this->ci->loan_invoices_m->get_loan_principal_installments($loan->id);
                                        $installment_array = array();
                                        if($old_invoices){
                                            foreach ($old_invoices as $old_invoice){
                                                $date = date('dmY',$old_invoice->due_date).'_'.date('dmY',$old_invoice->invoice_date);
                                                $installment_array[$date] = array(
                                                    'amount_payable' => $old_invoice->amount_payable,
                                                    'disable_fines' => $old_invoice->disable_fines,
                                                    'book_interest' => $old_invoice->book_interest,
                                                );
                                            }
                                        }
                                        $invoice_start_number = time();
                                        $invoice_numbers = array();
                                        $member_ids = array();
                                        $group_ids = array();
                                        $loan_ids = array();
                                        $types = array();
                                        $is_sents = array();
                                        $interest_amount_payables = array();
                                        $principle_amount_payable = array();
                                        $invoice_date = array();
                                        $due_date = array();
                                        $fine_date = array();
                                        $amount_payable = array();
                                        $amount_paid = array();
                                        $active = array();
                                        $created_by = array();
                                        $created_on = array();
                                        $disable_finess = array();
                                        $book_interests = array();
                                        $is_a_back_dating_record = array();
                                        foreach ($loan_invoices as $key => $invoice) {
                                            $invoice = (object)$invoice;
                                            $date_range = date('dmY',$invoice->due_date).'_'.date('dmY',$invoice->invoice_date);
                                            if(array_key_exists($date_range, $installment_array)){
                                                $disable_finess[] = $installment_array[$date_range]['disable_fines']; 
                                                $book_interests[] = $installment_array[$date_range]['book_interest']; 
                                            }else{
                                                $disable_finess[] = '';
                                                $book_interests[] = '';
                                            }
                                            $invoice_numbers[] = $invoice_start_number+$key;
                                            $member_ids[] = $member_id;
                                            $group_ids[] = $group_id;
                                            $loan_ids[] = $id;
                                            $types[] = 1;
                                            $is_sents[] = 0;
                                            $interest_amount_payables[] = $invoice->interest_amount_payable;
                                            $principle_amount_payables[] = $invoice->principle_amount_payable;
                                            $invoice_dates[]  = $invoice->invoice_date;
                                            $due_dates[]  = $invoice->due_date;
                                            $fine_dates[]  = $invoice->fine_date;
                                            $amount_payables[]  = $invoice->amount_payable;
                                            $amount_paids[]  = 0;
                                            $actives[]  = 1;
                                            $created_bys[]  = $loan_details_object->modified_by;
                                            $created_ons[]  = time();
                                            $is_a_back_dating_records[] = $is_a_back_dating_record?:0;
                                        }
                                        $input = array(
                                                'invoice_no'    => $invoice_numbers,
                                                'member_id'     => $member_ids,
                                                'group_id'      => $group_ids,
                                                'loan_id'       =>  $loan_ids,
                                                'type'          =>  $types,
                                                'is_sent'       =>  $is_sents,
                                                'interest_amount_payable'   => $interest_amount_payables,
                                                'principle_amount_payable'  => $principle_amount_payables,
                                                'invoice_date'  =>  $invoice_dates,
                                                'due_date'      =>  $due_dates,
                                                'disable_fines' =>  $disable_finess,
                                                'fine_date'     =>  $fine_dates,
                                                'amount_payable'=>  $amount_payables,
                                                'amount_paid'   =>  $amount_paids,
                                                'active'        =>  $actives,
                                                'book_interest' =>  $book_interests,
                                                'created_by'    =>  $created_bys,
                                                'created_on'    =>  $created_ons,
                                                'is_a_back_dating_record'    =>  $is_a_back_dating_records,
                                            );
                                        if($this->ci->loan_invoices_m->insert_batch($input)){
                                        }
                                        if($loan_details_object->enable_loan_processing_fee){
                                            if($loan_details_object->disable_automatic_loan_processing_income){
                                            }else{
                                                $loan_processing_fee_amount = $this->calculate_and_record_loan_processing_fee($id);
                                                if($loan_processing_fee_amount){
                                                    $this->ci->session->set_flashdata('success','Loan amount successfully recorded');
                                                }else{
                                                    $this->ci->session->set_flashdata('error','Unable to record loan processing');
                                                }
                                            }
                                        } 
                                        $this->migrate_transfers_out($loan_id,$id);
                                        $loan_repayments = $this->ci->loan_repayments_m->get_loan_repayments($loan_id);
                                        if($loan_repayments){
                                            foreach ($loan_repayments as $loan_repayment) {
                                                $member = $this->ci->members_m->get_group_member($loan_repayment->member_id,$loan_repayment->group_id);
                                                $deposit = $this->ci->deposits_m->get_deposit_by_loan_repayment_id($loan_repayment->id,$group_id);
                                                $payment_transaction_alert_id=0;
                                                if($deposit){
                                                    $payment_transaction_alert_id = $deposit->transaction_alert_id;
                                                }
                                                if($loan_repayment->transfer_from){
                                                    $this->manage_contribution_transfer($loan_repayment,$id);
                                                    $this->create_incoming_transfer_payment($id,$loan_repayment->amount,$loan_repayment->member_id,$loan_repayment->receipt_date,$loan_repayment->group_id,0,$loan_repayment->transfer_from,$loan_repayment->incoming_contribution_transfer_id,2);
                                                }else{
                                                    if($this->record_loan_repayment($loan_repayment->group_id,$loan_repayment->receipt_date,$member,$id,$loan_repayment->account_id,$loan_repayment->payment_method,0,$loan_repayment->amount,0,0,$loan_details_object->modified_by,$member->user_id,$payment_transaction_alert_id,$loan_repayment->is_a_back_dating_record,2)){
                                                        if($payment_transaction_alert_id){
                                                            $this->ci->transactions->mark_transaction_alert_as_reconciled($payment_transaction_alert_id,$group_id);
                                                        }
                                                    }else{
                                                        
                                                    }
                                                }
                                            }
                                        }
                                        $this->void_loan($loan->id,(object)array('id'=>$loan_details_object->modified_by),FALSE);
                                        $this->update_loan_invoices($id);
                                        $this->update_loan_invoices($id);
                                        if($loan_details_object->enable_loan_guarantors){
                                            if(is_array($guarantors)){
                                                $guarantors = (object)$guarantors;
                                                if($guarantors){
                                                    foreach ($guarantors->guarantor_id as $key => $value){
                                                        $data = array(
                                                                    'member_id' =>  $value,
                                                                    'group_id'  =>  $group_id,
                                                                    'loan_id'   =>  $id,
                                                                    'guaranteed_amount' =>  $guarantors->guaranteed_amount[$key],
                                                                    'comment'   =>  $guarantors->guarantor_comment[$key],
                                                                    'active'    =>  1,
                                                                    'created_on'    => time(),
                                                                    'created_by'    => $loan_details_object->modified_by,
                                                                );

                                                        $guarontor_result[] = $this->ci->loans_m->insert_loan_guarontors($data);
                                                    }
                                                    if($guarontor_result){
                                                        $this->ci->session->set_flashdata('success','Loan Successfully Created and Guarantors added');
                                                        return $id;
                                                    }else{
                                                        $this->ci->session->set_flashdata('info','Loan Successfully created but unable to add guarantors');
                                                        return FALSE;
                                                    }
                                                }
                                            }else{
                                                $this->ci->session->set_flashdata('info','Loan Successfully created but unable to add guarantors');
                                                return $id;
                                            }
                                        }else{
                                            $this->ci->session->set_flashdata('success','Loan Successfully Created');
                                            return $id;
                                        }
                                    }
                                    else
                                    {
                                        $this->ci->session->set_flashdata('error','Unable to create loan and its installments');
                                        return FALSE;
                                    }
                                }else{
                                    $this->ci->loans_m->update($id,array('active'=>'','modified_on'=>time()));
                                    $this->ci->session->set_flashdata('error','Unable to create withdrawal statement');
                                    return FALSE;
                                }
                            }else{
                                $this->ci->loans_m->update($id,array('active'=>'','modified_on'=>time()));
                                $this->ci->session->set_flashdata('error','Unable to record withdrawal');
                                return FALSE; 
                            }
                        }else{
                            $this->ci->session->set_flashdata('error','Unable to record withdrawal, no type found');
                            return FALSE;  
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Unable to record new loan after editing');
                        return FALSE;  
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Loan is not active');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Loan not found');
                return FALSE;
            }
        }
    }

    function void_external_loan($id=0,$void_payments = TRUE){
        if($id){
            $loan = $this->ci->debtors_m->get_loan($id);
            if($loan){
                /*
                    1. void guarantors
                    2. void invoices
                    3. void processing contribution
                    3. void payments
                    4. void statement
                    5. void loan
                */
                if($loan->active){
                    if($this->ci->debtors_m->void_loan_guarantors($loan->id)){
                        if($this->ci->debtors_m->void_all_invoices($loan->id)){
                            if($this->ci->transactions->void_external_lending_loan_processing_income('','',$loan->id,FALSE,'',$loan->group_id)){
                                if($void_payments){
                                    if($this->void_external_lending_loan_repayment('','',$loan->id,TRUE)){
                                        if($this->ci->debtors_m->void_statement_entries($loan->id)){
                                            if($this->ci->debtors_m->update_loan($loan->id,array('active'=>'','modified_on'=>time()))){
                                                $this->ci->session->set_flashdata('success','Loan Successfully voided');
                                                return TRUE;
                                            }
                                        }
                                    } 
                                }else{
                                    if($this->ci->debtors_m->void_loan_incoices_and_fines_statement_entries($loan->id)){
                                        if($this->ci->debtors_m->update_loan($loan->id,array('active'=>'','modified_on'=>time()))){
                                            $this->ci->session->set_flashdata('success','Loan Successfully voided');
                                            return TRUE;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Loan already voided');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Loan not found');
                return FALSE;
            }
        }
    }


    public function edit_automated_group_loan($id,$loan_type,$member_id,$group_id,$modified_by,$loan_details=array(),$guarantors=array(),$custom_loan_values=array(),$custom_rate_procedure=''){
        set_time_limit(0);
        ini_set('memory_limit','1536M');
        $loan = $this->ci->loans_m->get($id);
        if($loan){
            /***
                Delete all invoices and update them with the new loan invoices
                Delete all loan invoices/fines entries

                if both deletion are successful
                 proceed to calculate new loan
                 enter the loan invoices sent to the loans statements
                 pay the invoices from the total amount paid fot the loan
            ***/

            $loan_details_object = (object)$loan_details;

            $update_details = array(
                    'loan_type' =>  $loan_type,
                    'member_id' =>  $member_id,
                    'group_id'  =>  $group_id,
                    'is_edited' => '',
                    'loan_end_date'=>strtotime('+ '.$loan_details_object->repayment_period.' months',$loan_details_object->disbursement_date),
                    'grace_period_end_date'=>strtotime('+ '.$loan_details_object->grace_period.' months',$loan_details_object->disbursement_date),
                    'is_fully_paid' =>'',
                    'modified_by' => $modified_by,
                    'modified_on'  => time())+$loan_details;

            $update = $this->ci->loans_m->update($id,$update_details);
            if($update)
            {
                //void all invoices 
                $delete_invoices = $this->ci->loan_invoices_m->delete_all_invoices($id);

                if($delete_invoices)
                {
                    $delete_statements = $this->ci->loans_m->delete_all_loan_invoices_and_fines_entries($id,$loan->group_id);
                    $delete_guarantors = $this->ci->loans_m->delete_loan_guarantors($id,$loan->group_id);
                    $delete_loan_processing = $this->ci->transactions->void_loan_processing_income('','',$id,FALSE,'',$loan->group_id);

                    if($delete_statements && $delete_guarantors)
                    {
                        if($loan_details_object->interest_type==1|| $loan_details_object->interest_type==2){
                            $loan_invoices = $this->calculate_loan_balance_invoice($loan_details_object->loan_amount,$loan_details_object->interest_type,$loan_details_object->interest_rate,$loan_details_object->repayment_period,$loan_details_object->grace_period,$loan_details_object->disbursement_date,$loan_details_object->loan_interest_rate_per,$loan_details_object->grace_period_date);
                        }else if($loan_details_object->interest_type==3){
                            $loan_invoices = $this->calculate_loan_balance_invoice_for_custom($loan_details_object->loan_amount,$custom_rate_procedure,$loan_details_object->repayment_period,$loan_details_object->disbursement_date,$custom_loan_values);
                        }

                        if($loan_invoices)
                        {  
                            foreach ($loan_invoices as $key => $invoice) 
                            {
                                $invoice = (object)$invoice;
                                $invoice_id[] = $this->ci->loan_invoices_m->insert(array(
                                    'invoice_no'    => $this->ci->loan_invoices_m->calculate_invoice_no($group_id),
                                    'member_id'     => $member_id,
                                    'group_id'      => $group_id,
                                    'loan_id'       =>  $id,
                                    'type'          =>  1,
                                    'is_sent'       =>  0,
                                    'interest_amount_payable'   => $invoice->interest_amount_payable,
                                    'principle_amount_payable'  => $invoice->principle_amount_payable,
                                    'invoice_date'  =>  $invoice->invoice_date,
                                    'due_date'      =>  $invoice->due_date,
                                    'fine_date'     =>  $invoice->fine_date,
                                    'amount_payable'=>  $invoice->amount_payable,
                                    'amount_paid'   =>  0,
                                    'active'        =>  1,
                                    'created_by'    =>  $loan_details_object->created_by,
                                    'created_on'    =>  time(),
                                ));
                            }

                            $past_invoice_id = $this->send_past_invoices($id,'',$group_id);
                            if($past_invoice_id)
                            {
                                $outstanding_balance_fine_date = $this->set_outstanding_balance_fine_date($id,'',$group_id);
                                if($outstanding_balance_fine_date)
                                {
                                    $fix_loan_invoices_fine = $this->fix_loan_invoices_fine($id);
                                    if($fix_loan_invoices_fine)
                                    {
                                        $outstanding_balance_fixer = $this->outstanding_loan_balance_fine_fixer($loan);
                                        if($outstanding_balance_fixer)
                                        {
                                            /*if($this->pay_edited_invoices($id)){
                                                $this->loan_fixer($id);
                                                $this->update_loan_status($id);
                                                //$this->update_loan_invoices($id);
                                                $this->update_loan_invoices($id);
                                                $this->ci->loans_m->update($id,array('is_edited'=>1,'modified_on'=>time()));
                                                return $id;
                                            }else{
                                                $this->ci->session->set_flashdata('error','Just click save again');
                                                return FALSE;
                                            }*/
                                            if($loan_details_object->enable_loan_guarantors)
                                            {
                                                if(is_array($guarantors))
                                                {
                                                    $guarantors = (object)$guarantors;
                                                    foreach ($guarantors->guarantor_id as $key => $value) 
                                                    {
                                                        $data = array(
                                                                        'member_id' =>  $value,
                                                                        'group_id'  =>  $group_id,
                                                                        'loan_id'   =>  $id,
                                                                        'guaranteed_amount' =>  $guarantors->guaranteed_amount[$key],
                                                                        'comment'   =>  $guarantors->guarantor_comment[$key],
                                                                        'active'    =>  1,
                                                                        'created_on'    => time(),
                                                                        'created_by'    => $loan_details_object->created_by,
                                                                    );

                                                        $guarontor_result[] = $this->ci->loans_m->insert_loan_guarontors($data);
                                                    }

                                                    if($guarontor_result)
                                                    {
                                                        $this->ci->session->set_flashdata('success','Loan Successfully Created and Guarantors added');
                                                    }
                                                    else
                                                    {
                                                        $this->ci->session->set_flashdata('info','Loan Successfully created but unable to add guarantors');
                                                    }
                                                }
                                                else
                                                {
                                                    $this->ci->session->set_flashdata('info','Loan Successfully created but unable to add guarantors');
                                                }
                                            }else{
                                                $this->ci->session->set_flashdata('success','Loan Successfully Created');
                                            }
                                            $this->update_loan_invoices($id);
                                            $this->ci->loans_m->update($id,array('is_edited'=>1,'modified_on'=>time()));
                                            return $id; 
                                        }
                                        else{
                                            return FALSE;
                                        }

                                    }
                                    else
                                    {
                                        $this->ci->session->set_flashdata('error','Unable to fix loan fine date');
                                        return FALSE;
                                    }
                                }
                                else
                                {
                                    $this->ci->session->set_flashdata('error','Unable to set the outstanding balance fine date');
                                    return FALSE;
                                }
                            }
                            else
                            {
                                $this->ci->session->set_flashdata('error','Unable to send past invoices');
                                return FALSE;
                            }


                        }
                        else
                        {
                            $this->ci->session->set_flashdata('error','Unable to create loan and its installments');
                            return FALSE;
                        }

                    }
                    else
                    {
                        $this->ci->session->set_flashdata('error','Unable to edit loan since it cannot remove the guarantors added or statement entries');
                        return FALSE;
                    }
                }

                else
                {
                    $this->ci->session->set_flashdata('error','Unable to remove loan invoices added.');
                    return FALSE;
                }

            }
            else{
                $this->ci->session->set_flashdata('error','Unable to edit the Loan details');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Could not find loan to edit');
            return FALSE;
        }
    }


    function edit_debtor_loan($loan_id=0,$debtor_id=0,$group_id=0,$loan_details=array(),$guarantors=array(),$custom_interest_procedure=0,$custom_loan_values=array(),$is_a_back_dating_record=0){
        if($loan_id&&$debtor_id&&$group_id&&$loan_details){
            $loan = $this->ci->debtors_m->get_loan($loan_id,$group_id);
            if($loan){
                //delete all withdrawal
                //delete loan income
                //delete loan repayments
                //delete loan statements - invoices and guarantors
                if($this->void_external_loan($loan->id,FALSE) || $loan->active!=1){
                    $loan_details_object = (object)$loan_details;
                    $loan_data = array(
                            'debtor_id'=>$debtor_id,
                            'group_id'=>$group_id,
                            'loan_end_date'=>strtotime('+ '.$loan_details_object->repayment_period.' months',$loan_details_object->disbursement_date),
                            'grace_period_end_date'=>strtotime('+ '.$loan_details_object->grace_period.' months',$loan_details_object->disbursement_date),
                        );
                    $loan_data = array_merge($loan_data,$loan_details);

                    if($this->ci->debtors_m->update_loan($loan->id,$loan_data)){
                        if(preg_match('/bank-/', $loan_details_object->account_id)){
                            $type = 33;
                        }else if(preg_match('/sacco-/', $loan_details_object->account_id)){
                            $type = 34;
                        }else if(preg_match('/mobile-/', $loan_details_object->account_id)){
                            $type = 35;
                        }else if(preg_match('/petty-/', $loan_details_object->account_id)){
                            $type = 36;
                        }else{
                            $type = 0;
                        }
                        if($type){
                            $input = array(
                                'type'=>$type,
                                'group_id' => $group_id,
                                'withdrawal_date' => $loan_details_object->disbursement_date,
                                'debtor_loan_id' => $loan->id,                            
                                'withdrawal_method' => 1,
                                'account_id' => $loan_details_object->account_id,
                                'amount' => $loan_details_object->loan_amount,
                                'description' => '',
                                'active' => 1,
                                'created_on' => time(),
                                'created_by' => $loan_details_object->modified_by,
                                'debtor_id' => $debtor_id,
                                'is_a_back_dating_record' =>  $is_a_back_dating_record?1:0,
                            );
                            /*if($withdrawal_id = $this->ci->withdrawals_m->insert($input)){
                                $transaction_type = $type;
                                    if($this->ci->transactions->withdrawal($group_id,$withdrawal_id,$transaction_type,$loan_details_object->disbursement_date,$loan_details_object->account_id,$loan_details_object->loan_amount,'','','','','','','','','','','','',0,0,0,$is_a_back_dating_record,$debtor_id,$loan->id)){*/
                                        $loan_invoices = array();
                                        if($loan_details_object->interest_type==1|| $loan_details_object->interest_type==2){
                                            $loan_invoices = $this->calculate_loan_balance_invoice($loan_details_object->loan_amount,$loan_details_object->interest_type,$loan_details_object->interest_rate,$loan_details_object->repayment_period,$loan_details_object->grace_period,$loan_details_object->disbursement_date,$loan_details_object->loan_interest_rate_per);
                                        }else if($loan_details_object->interest_type==3){
                                            $loan_invoices = $this->calculate_loan_balance_invoice_for_custom($loan_details_object->loan_amount,$custom_rate_procedure,$loan_details_object->repayment_period,$loan_details_object->disbursement_date,$custom_loan_values,$is_a_back_dating_record);
                                        }
                                        if($loan_invoices){
                                            foreach ($loan_invoices as $loan_invoice) {
                                                $loan_invoice = (object)$loan_invoice;
                                                $loan_invoice_data = array();
                                                $loan_invoice_data = array(
                                                    'debtor_loan_id' => $loan->id,
                                                    'group_id' => $group_id,
                                                    'debtor_id' => $debtor_id,
                                                    'invoice_no' => $this->ci->debtors_m->calculate_invoice_no($group_id),
                                                    'type' => 1,
                                                    'is_sent' => 0,
                                                    'interest_amount_payable' => $loan_invoice->interest_amount_payable,
                                                    'principle_amount_payable' => $loan_invoice->principle_amount_payable,
                                                    'invoice_date' => $loan_invoice->invoice_date,
                                                    'due_date' => $loan_invoice->due_date,
                                                    'fine_date' => $loan_invoice->fine_date,
                                                    'amount_payable' => $loan_invoice->amount_payable,
                                                    'amount_paid' => 0,
                                                    'active' => 1,
                                                    'created_by' => $loan_details_object->modified_by,
                                                    'created_on' => time(),
                                                    'is_a_back_dating_record' => $is_a_back_dating_record?:0,
                                                );
                                                if($loan_invoice_data){
                                                    $this->ci->debtors_m->insert_loan_invoice($loan_invoice_data);
                                                }
                                                unset($loan_invoice_data);
                                            }  
                                            if($this->send_past_invoices($loan->id,TRUE)){
                                                if($this->set_outstanding_balance_fine_date($loan->id,TRUE)){
                                                   if($this->fix_loan_invoices_fine($loan->id,TRUE)){
                                                        if($this->outstanding_loan_balance_fine_fixer($loan,TRUE)){
                                                            if($this->calculate_and_record_loan_processing_fee($loan->id,'',TRUE)){
                                                                $this->ci->debtors_m->delete_guarantors($loan->id);
                                                                if($guarantors){
                                                                    $guarantor_count = count($guarantors);
                                                                    $guarantors = (object)$guarantors;
                                                                    $guarantor_id = $guarantors->guarantor_id;
                                                                    $guaranteed_amount = $guarantors->guaranteed_amount;
                                                                    $guarantor_comment = $guarantors->guarantor_comment;
                                                                    for($i=0;$i<$guarantor_count;$i++){
                                                                        $guarantor_data = array(
                                                                            'debtor_loan_id' => $loan->id,
                                                                            'group_id' => $group_id,
                                                                            'member_id' => $guarantor_id[$i],
                                                                            'guaranteed_amount' => currency($guaranteed_amount[$i]),
                                                                            'comment' => $guarantor_comment[$i],
                                                                            'active' => 1 ,
                                                                            'created_by' => $loan_details_object->modified_by,
                                                                            'created_on'   => time(),
                                                                        );
                                                                        $this->ci->debtors_m->insert_loan_guarantor($guarantor_data);
                                                                    }
                                                                    $this->update_loan_invoices($loan->id,TRUE);
                                                                    return $loan->id;
                                                                }else{
                                                                    $this->update_loan_invoices($loan->id,TRUE);
                                                                    return $loan->id;
                                                                }
                                                            }else{
                                                                return FALSE;
                                                            }
                                                        }else{
                                                            return FALSE;
                                                        }
                                                   }else{
                                                        return FALSE;
                                                   }
                                                }else{
                                                    return FALSE;
                                                }
                                            }else{
                                                return FALSE;
                                            }
                                        }else{
                                            $this->ci->debtors_m->update_loan($loan->id,array('active'=>'','modified_on'=>time()));
                                            $this->ci->session->set_flashdata('error','Unable to create Loan invoices');
                                            return FALSE;
                                        } 
                                    /*}else{
                                        $this->ci->debtors_m->update_loan($loan->id,array('active'=>'','modified_on'=>time()));
                                        $this->ci->session->set_flashdata('error','Unable to create withdrawal statement');
                                        return FALSE;
                                    }*/
                            /*}else{
                                //void loan
                                $this->ci->debtors_m->update_loan($loan->id,array('active'=>'','modified_on'=>time()));
                                $this->ci->session->set_flashdata('error','Unable to create withdrawal entry');
                                return FALSE;
                            }*/
                        }else{
                            //void loan
                            $this->ci->debtors_m->update_loan($loan->id,array('active'=>'','modified_on'=>time()));
                            $this->ci->session->set_flashdata('error','Unable to get type');
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    // function calculate_loan_balance_invoice($loan_amount,$interest_type,$interest_rate,$repayment_period,$grace_period=1,$disbursement_date='',$loan_interest_rate_per=4,$grace_period_date = 0){
    //     if($loan_amount && $interest_type){
    //         if(empty($grace_period)){
    //             $grace_period=1;
    //         }
    //         if($grace_period=="date"){
    //             $disbursement_date = $grace_period_date;
    //         }else{
    //             $grace_period = $grace_period-1;
    //             if($disbursement_date){

    //             }else{
    //                 $disbursement_date=time();
    //             }
    //             $disbursement_date  = strtotime('+ '.$grace_period.' months',$disbursement_date?:time());
    //         }
    //         if($loan_interest_rate_per==1){
    //             $interest_rate = $interest_rate*365;
    //         }else if($loan_interest_rate_per==2){
    //             $interest_rate = $interest_rate*52;
    //         }else if($loan_interest_rate_per==3){
    //             $interest_rate = $interest_rate*12;
    //         }else if($loan_interest_rate_per==5){
    //             $interest_rate = ($interest_rate*12)/$repayment_period;
    //         }
    //         $decrement_count = FALSE;
    //         if($interest_type==1)
    //         {
    //             $loan_invoices = array();
    //             if($repayment_period){
    //                 $monthly = ($loan_amount * ((100 + (($interest_rate) * ($repayment_period / 12))) / 100)) / $repayment_period;
    //                 $count = 1;
    //                 for ($i = 0; $i < $repayment_period; $i++){
    //                     $principle_amount = $loan_amount / $repayment_period;
    //                     $interest_amount = $monthly - $principle_amount;
    //                     if($grace_period === "date" && $count == 1){
    //                         $due_date = $disbursement_date;
    //                         $decrement_count = TRUE; 
    //                     }else{
    //                         $due_date = $this->_date_add(date('d M Y',$disbursement_date),$count,$decrement_count);
    //                     }
    //                     $count++;

    //                     $invoice_date = $due_date - (86400 * 7);
    //                     $loan_invoices[] = array
    //                         (
    //                             'fine_date' => $due_date + (24 * 60 * 60),
    //                             'interest_amount_payable' => (float) str_replace(',', '', number_format($interest_amount, 2)),
    //                             'principle_amount_payable' => (float) str_replace(',', '', number_format($principle_amount, 2)),
    //                             'invoice_date' => $invoice_date,
    //                             'due_date' => $due_date,
    //                             'amount_payable' => (float) str_replace(',', '', number_format($monthly, 2)),
    //                         );
    //                 }
    //             }
    //             return $loan_invoices;
    //         }
    //         else if($interest_type==2)
    //         {
    //             $loan_invoices = array();
    //             if($repayment_period){
    //                 if($interest_rate){
    //                     $annual = number_format($interest_rate / 100, 5);
    //                     $monthly_rate = number_format($annual / 12, 5);

    //                     $monthly = ($monthly_rate / (1 - (pow((1 + $monthly_rate), -($repayment_period))))) * $loan_amount;
    //                 }else{
    //                     $monthly_rate = 0;
    //                     $monthly = $loan_amount/$repayment_period;
    //                 }
                    
    //                 $count = 1;
    //                 for ($i = 0; $i < $repayment_period; $i++)
    //                 {
    //                     $new_interest = $loan_amount * $monthly_rate;
    //                     $new_principle = $monthly - $new_interest;

    //                     $loan_amount -= $new_principle;
    //                     //$grace_period = $grace_period-1;
                        
    //                     if($grace_period==="date"&&$count==1){
    //                         $due_date = $disbursement_date;
    //                         $decrement_count = TRUE; 
    //                     }else{
    //                         $due_date = $this->_date_add(date('d M Y',$disbursement_date), $count,$decrement_count);
    //                     }
    //                     $count++;
    //                     $invoice_date = $due_date - (86400 * 7);
    //                     $loan_invoices[] =array(
    //                         'interest_amount_payable' => (float) str_replace(',', '', number_format($new_interest, 2)),
    //                         'principle_amount_payable' => (float) str_replace(',', '', number_format($new_principle, 2)),
    //                         'invoice_date' => $invoice_date,
    //                         'due_date' => $due_date,
    //                         'fine_date' => $due_date + (24 * 60 * 60),
    //                         'amount_payable' => (float) str_replace(',', '', number_format($monthly, 2)),
    //                     );
    //                 }
    //             }
    //             return $loan_invoices;
    //         }
    //         else
    //         {
    //             return FALSE;
    //         }
    //     }else{
    //         $this->ci->session->set_flashdata('error','Cannot calculate invoices. Some parameters are missing');
    //         return FALSE;
    //     }
        
    // }

    function calculate_loan_balance_invoice($loan_amount,$interest_type,$interest_rate,$repayment_period,$grace_period=1,$disbursement_date='',$loan_interest_rate_per=4,$grace_period_date = 0,$loan_type_id=0){
        if($loan_amount && $interest_type){
            $processing_fee_amount=$this->calculate_loan_processing_fee($loan_type_id,$is_a_loan = FALSE,$is_a_debtor=FALSE,$loan_amount,$loan_amount);
            $loan_type=$this->ci->loan_types_m->get($loan_type_id);
            $loan_processing_recovery_on=$loan_type->loan_processing_recovery_on;
            if(empty($grace_period)){
                $grace_period=1;
            }
            if($grace_period=="date"){
                $disbursement_date = $grace_period_date;
            }else{
                $grace_period = $grace_period-1;
                if($disbursement_date){

                }else{
                    $disbursement_date=time();
                }
                $disbursement_date  = strtotime('+ '.$grace_period.' months',$disbursement_date?:time());
            }
            if($loan_interest_rate_per==1){
                $interest_rate = $interest_rate*365;
            }else if($loan_interest_rate_per==2){
                $interest_rate = $interest_rate*52;
            }else if($loan_interest_rate_per==3){
                $interest_rate = $interest_rate*12;
            }else if($loan_interest_rate_per==5){
                $interest_rate = ($interest_rate*12)/$repayment_period;
            }
            $decrement_count = FALSE;
            if($interest_type==1)
            {
                $loan_invoices = array();
                if($repayment_period){
                    $monthly = ($loan_amount * ((100 + (($interest_rate) * ($repayment_period / 12))) / 100)) / $repayment_period;
                    $count = 1;
                    
                    $processing_fee_displayed = false;
                    for ($i = 0; $i < $repayment_period; $i++){
                        $principle_amount = $loan_amount / $repayment_period;
                        $interest_amount = $monthly - $principle_amount;
                        if($grace_period === "date" && $count == 1){
                            $due_date = $disbursement_date;
                            $decrement_count = TRUE; 
                        }else{
                            $due_date = $this->_date_add(date('d M Y',$disbursement_date),$count,$decrement_count);
                        }
                        $count++;

                        if ($loan_processing_recovery_on == 2 && !$processing_fee_displayed) {
                            $processing_fees = $processing_fee_amount/$repayment_period;
                            $processing_fee_displayed = false;
                        } else {
                            $processing_fees = 0;
                        }
                        if ($loan_processing_recovery_on == 1 && !$processing_fee_displayed) {
                            $processing_fees = $processing_fee_amount;
                            $processing_fee_displayed = true;
                        } if ($loan_processing_recovery_on == 1 && !$processing_fee_displayed) {
                            $processing_fees = 0;
                           
                        }
                        $invoice_date = $due_date - (86400 * 7);
                        $loan_invoices[] = array
                            (
                                'fine_date' => $due_date + (24 * 60 * 60),
                                'interest_amount_payable' => (float) str_replace(',', '', number_format($interest_amount, 2)),
                                'principle_amount_payable' => (float) str_replace(',', '', number_format($principle_amount, 2)),
                                'invoice_date' => $invoice_date,
                                'due_date' => $due_date,
                                'processing_fee'=>$processing_fees,
                                'amount_payable' => (float) str_replace(',', '', number_format($monthly, 2)),
                            );
                    }
                }
                return $loan_invoices;
            }
            else if($interest_type==2)
            {
                $loan_invoices = array();
                if($repayment_period){
                    if($interest_rate){
                        $annual = number_format($interest_rate / 100, 5);
                        $monthly_rate = number_format($annual / 12, 5);

                        $monthly = ($monthly_rate / (1 - (pow((1 + $monthly_rate), -($repayment_period))))) * $loan_amount;
                    }else{
                        $monthly_rate = 0;
                        $monthly = $loan_amount/$repayment_period;
                    }
                    $processing_fee_displayed = false;
                    $count = 1;
                    for ($i = 0; $i < $repayment_period; $i++)
                    {
                        $new_interest = $loan_amount * $monthly_rate;
                        $new_principle = $monthly - $new_interest;
                       
                        if ($loan_processing_recovery_on == 2 && !$processing_fee_displayed) {
                            $processing_fees = $processing_fee_amount/$repayment_period;
                            $processing_fee_displayed = false;
                        } else {
                            $processing_fees = 0;
                        }
                        if ($loan_processing_recovery_on == 1 && !$processing_fee_displayed) {
                            $processing_fees = $processing_fee_amount;
                            $processing_fee_displayed = true;
                        } if ($loan_processing_recovery_on == 1 && !$processing_fee_displayed) {
                            $processing_fees = 0;
                           
                        }
                        $loan_amount -= $new_principle;
                        //$grace_period = $grace_period-1;
                        
                        if($grace_period==="date"&&$count==1){
                            $due_date = $disbursement_date;
                            $decrement_count = TRUE; 
                        }else{
                            $due_date = $this->_date_add(date('d M Y',$disbursement_date), $count,$decrement_count);
                        }
                        
                    
                        $count++;
                        $invoice_date = $due_date - (86400 * 7);
                        $loan_invoices[] =array(
                            'interest_amount_payable' => (float) str_replace(',', '', number_format($new_interest, 2)),
                            'principle_amount_payable' => (float) str_replace(',', '', number_format($new_principle, 2)),
                            'invoice_date' => $invoice_date,
                            'due_date' => $due_date,
                            'processing_fee'=>$processing_fees,
                            'fine_date' => $due_date + (24 * 60 * 60),
                            'amount_payable' => (float) str_replace(',', '', number_format($monthly, 2)),
                        );
                    }
                }
                
                return $loan_invoices;
            }
            else
            {
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Cannot calculate invoices. Some parameters are missing');
            return FALSE;
        }
        
    }

    function repay_invoices($invoices=array(),$amount = 0){
        if(is_array($invoices) && $amount>0){
            foreach ($invoices as $invoice){
                if($amount){
                    $amount_paid=0;
                    $status = 1;
                    $amount_payable = $invoice->amount_payable;
                    if(round($amount_payable) == round($amount)){
                        $amount_paid = $amount_payable;
                        $status = 2;
                    }else if(round($amount) > round($amount_payable)){
                        $amount_paid = $amount_payable;
                        $status = 2;
                    }else if(round($amount) < round($amount_payable)){
                        $amount_paid = $amount;
                    }
                    $amount -=$amount_payable;
                    $update = array(
                            'amount_paid' => $amount_paid,
                            'status' => $status,
                            'modified_on' => time(),
                        );
                    $this->ci->loan_invoices_m->update($invoice->id,$update);
                }else{
                    break;
                }
            }
        }
    }

    

    function recalculate_reducing_balance($loan = array(),$amount_paid=0,$repayment_date='',$is_a_debtor=FALSE,$ignore_ivoices = array(),$total_amount_payable = 0){
        if($loan&& $repayment_date){
            $enable_absolute_loan_recalculation = 0;
            if($is_a_debtor){
                $group = $this->ci->groups_m->get($loan->group_id);
                if($group){
                    $enable_absolute_loan_recalculation = $group->enable_absolute_loan_recalculation?:0;
                }
                if($loan->enable_reducing_balance_installment_recalculation){
                    if($amount_paid){
                        if($loan && $loan->interest_type==2){
                            $invoice  = $this->ci->debtors_m->get_newest_invoice($loan_id);
                            if($invoices){
                                $key = count($ignore_ivoices);
                                $new_invoice = isset($invoices[$key])?$invoices[$key]:array();
                                if(empty($new_invoice)){
                                    return TRUE;
                                }
                                $equates = 'Ymd';
                                if($enable_absolute_loan_recalculation){
                                    $equates = 'Ym';
                                }
                                if((date($equates,$repayment_date)<date($equates,$new_invoice->invoice_date))){
                                    $loan_balance = round($this->ci->debtors_m->get_installment_principle_balance($loan_id));
                                    if($loan_balance){
                                        $loan_amount = ($loan_balance-$amount_paid-$total_amount_payable);
                                        $annual = number_format($loan->interest_rate / 100, 5);
                                        $monthly_rate = number_format($annual / 12, 5);
                                        $interest_type = $loan->interest_type;
                                        $interest_rate = $loan->interest_rate;
                                        $repayment_period = count($invoices);
                                        $grace_period = $loan->grace_period;
                                        $disbursement_date = $loan->disbursement_date;
                                        $loan_interest_rate_per = $loan->loan_interest_rate_per;
                                        $grace_period_date = $loan->grace_period_date;
                                        $new_invoices = $this->calculate_loan_balance_invoice($loan_amount,$interest_type,$interest_rate,$repayment_period,$grace_period,$disbursement_date,$loan_interest_rate_per,$grace_period_date);
                                        foreach ($invoices as $invoice) {
                                            if(in_array($invoice->id, $ignore_ivoices)){
                                                continue;   
                                            }
                                            if($invoice->book_interest){
                                            }else{
                                                $interest = $loan_amount*$monthly_rate;
                                                $principle = $invoice->principle_amount_payable;
                                                $monthly =$principle+$interest;
                                                $loan_amount -=$principle;
                                                if($interest<0){
                                                    $interest = 0;
                                                    $monthly = $principle;
                                                    $loan_amount = 0;
                                                } 
                                                // if($interest<0 || $loan_amount<0){
                                                //     // $interest = 0;

                                                //     // $monthly = $principle;
                                                //     // $loan_amount = 0;
                                                // }

                                                $invoice->interest_amount_payable = (float) str_replace(',', '', number_format($interest, 2));
                                                $invoice->amount_payable = (float) str_replace(',', '', number_format($monthly, 2));
                                                $invoice->modified_on = time();
                                                $invoice->status = 1;
                                                if($loan_amount<0){
                                                    break;
                                                }
                                            }
                                        }
                                        $invoices = json_decode(json_encode($invoices),TRUE);
                                        $this->ci->debtors_m->update_batch($invoices);
                                    }else{
                                        return TRUE;
                                    }
                                }else{
                                    if($amount_paid > $new_invoice->amount_payable){
                                        $new_amount_paid = floor($amount_paid - ($new_invoice->amount_payable));
                                        $total_amount_payable+=$new_invoice->principle_amount_payable;
                                        $ignore_ivoices = array_merge($ignore_ivoices,array('id' =>$new_invoice->id));
                                        if($new_amount_paid){
                                            $this->recalculate_reducing_balance($loan,$new_amount_paid,$repayment_date,TRUE,$ignore_ivoices,$total_amount_payable);
                                        }
                                    }
                                }
                            }
                            return TRUE;
                        }else{
                            return FALSE;
                        }
                    }else{
                        if($loan && $loan->interest_type==2){
                            $loan_balance = round($this->ci->debtors_m->get_installment_principle_balance($loan_id));
                            if($loan_balance){
                                $loan_amount = $loan_balance-$amount_paid;
                                $annual = number_format($loan->interest_rate / 100, 5);
                                $monthly_rate = number_format($annual / 12, 5);
                                $invoices = $this->ci->debtors_m->get_newest_invoices($loan_id,$last_due_date,$enable_absolute_loan_recalculation);
                                foreach ($invoices as $inv) {
                                    $interest = $loan_amount*$monthly_rate;
                                    $principle = $inv->principle_amount_payable;
                                    $monthly =$principle+$interest;
                                    $loan_amount -=$principle;
                                    if($interest<0){
                                        $interest = 0;
                                        $monthly = $principle;
                                    }
                                    $update = array(
                                            'interest_amount_payable' => (float) str_replace(',', '', number_format($interest, 2)),
                                            'amount_payable' => (float) str_replace(',', '', number_format($monthly, 2)),
                                            'modified_on' => time(),
                                        );
                                    $this->ci->debtors_m->update_loan_invoices($inv->id,$update);
                                    unset($update);
                                }
                                return TRUE;
                            }
                        }else{
                            return true;
                        }
                    }
                }else{
                    return TRUE; 
                }
            }else{
                $group = $this->ci->groups_m->get($loan->group_id);
                if($group){
                    $enable_absolute_loan_recalculation = $group->enable_absolute_loan_recalculation?:0;
                }
                if($loan->enable_reducing_balance_installment_recalculation){
                    if($amount_paid){
                        if($loan && $loan->interest_type==2){
                           
                            $invoices  = $this->ci->loan_invoices_m->get_newest_invoices($loan->id);
                        
                            if($invoices){
                                $key = count($ignore_ivoices);
                                $new_invoice = isset($invoices[$key])?$invoices[$key]:array();
                                if(empty($new_invoice)){
                                    return TRUE;
                                }
                                $equates = 'Ymd';
                                if($enable_absolute_loan_recalculation){
                                    $equates = 'Ym';
                                }
                                if((date($equates,$repayment_date)<date($equates,$new_invoice->due_date))){
                                    $loan_balance = round($this->ci->loan_invoices_m->get_installment_principle_balance($loan->id));
                                    if($loan_balance){
                                        $loan_amount = ($loan_balance-$amount_paid-$total_amount_payable);
                                        $annual = number_format($loan->interest_rate / 100, 5);
                                        $monthly_rate = number_format($annual / 12, 5);
                                        $interest_type = $loan->interest_type;
                                        $interest_rate = $loan->interest_rate;
                                        $repayment_period = count($invoices);
                                        $grace_period = $loan->grace_period;
                                        $disbursement_date = $loan->disbursement_date;
                                        $loan_interest_rate_per = $loan->loan_interest_rate_per;
                                        $grace_period_date = $loan->grace_period_date;
                                        $new_invoices = $this->calculate_loan_balance_invoice($loan_amount,$interest_type,$interest_rate,$repayment_period,$grace_period,$disbursement_date,$loan_interest_rate_per,$grace_period_date);
                                        foreach ($invoices as $invoice) {
                                            if(in_array($invoice->id, $ignore_ivoices)){
                                                continue;   
                                            }
                                            if($invoice->book_interest){
                                            }else{
                                                $interest = $loan_amount*$monthly_rate;
                                                $principle = $invoice->principle_amount_payable;
                                                $monthly =$principle+$interest;
                                                $loan_amount -=$principle;
                                                if($interest<0 || $loan_amount<0){
                                                    $interest = 0;
                                                    $monthly = $principle;
                                                    $loan_amount = 0;
                                                }

                                                $invoice->interest_amount_payable = (float) str_replace(',', '', number_format($interest, 2));
                                                $invoice->amount_payable = (float) str_replace(',', '', number_format($monthly, 2));
                                                $invoice->modified_on = time();
                                                $invoice->status = 1;

                                                // $update = array(
                                                //     'interest_amount_payable' => (float) str_replace(',', '', number_format($interest, 2)),
                                                //     'amount_payable' => (float) str_replace(',', '', number_format($monthly, 2)),
                                                //     'modified_on' => time(),
                                                //     'status' => 1,
                                                // );
                                                // $this->ci->loan_invoices_m->update($invoice->id,$update);
                                            }
                                        }
                                        $invoices = json_decode(json_encode($invoices),TRUE);
                                        $this->ci->loan_invoices_m->update_batch($invoices);
                                    }else{
                                        return TRUE;
                                    }
                                }else{
                                    if($amount_paid > $new_invoice->amount_payable){
                                        $new_amount_paid = round($amount_paid - ($new_invoice->amount_payable));
                                        $total_amount_payable+=$new_invoice->amount_payable;
                                        $ignore_ivoices = array_merge($ignore_ivoices,array('id' =>$new_invoice->id));
                                        if($new_amount_paid){
                                            $this->recalculate_reducing_balance($loan,$new_amount_paid,$repayment_date,'',$ignore_ivoices,$total_amount_payable);
                                        }
                                    }
                                }
                            }
                            return TRUE;
                            // $loan_balance = round($this->ci->loan_invoices_m->get_installment_principle_balance($loan->id));
                            // if($loan_balance){
                            //     $invoice  = $this->ci->loan_invoices_m->get_newest_invoice($loan_id);
                            //     if($invoice){
                            //         $equates = 'Ymd';
                            //         if($enable_absolute_loan_recalculation){
                            //             $equates = 'Ym';
                            //         }
                            //         if((date($equates,$repayment_date)<date($equates,$invoice->due_date))){
                            //             if(round($invoice->amount_payable)<round($amount_paid)){
                            //                 $loan_amount = $loan_balance-$amount_paid;
                            //                 $annual = number_format($loan->interest_rate / 100, 5);
                            //                 $monthly_rate = number_format($annual / 12, 5);
                            //                 $paid_existing_invoices = $this->ci->loan_invoices_m->get_paid_existing_invoices($loan_id,$repayment_date,$enable_absolute_loan_recalculation);
                            //                 if($paid_existing_invoices){
                            //                     $previous_amount_paid = 0;
                            //                     foreach ($paid_existing_invoices as $invoice) {
                            //                         if($invoice->book_interest){
                            //                             $last_due_date = '';
                            //                         }else{
                            //                             $previous_amount_paid+=$invoice->amount_paid;
                            //                             $last_due_date = $invoice->due_date;
                            //                         }
                            //                     }
                            //                     $new_paid_existing_invoices = $paid_existing_invoices;
                            //                     if($last_due_date){
                            //                         //get invoices after this date
                            //                         $invoices = $this->ci->loan_invoices_m->get_newest_invoices($loan_id,$last_due_date,$enable_absolute_loan_recalculation);
                            //                         if($invoices){
                            //                             $new_paid_existing_invoices = array_merge($paid_existing_invoices,$invoices);
                            //                         }
                            //                     }
                            //                     $loan_amount = $loan_balance-$amount_paid-$previous_amount_paid;
                            //                     $invoice_list = array();
                            //                     foreach ($new_paid_existing_invoices as $invoice) {
                            //                         if($invoice->book_interest){

                            //                         }else{
                            //                             $interest = $loan_amount*$monthly_rate;
                            //                             $principle = $invoice->principle_amount_payable;
                            //                             $monthly =$principle+$interest;
                            //                             $loan_amount -=$principle;
                            //                             if($interest<0 || $loan_amount<0){
                            //                                 $interest = 0;
                            //                                 $monthly = $principle;
                            //                                 $loan_amount = 0;
                            //                             }
                            //                             $update = array(
                            //                                     'interest_amount_payable' => (float) str_replace(',', '', number_format($interest, 2)),
                            //                                     'amount_payable' => (float) str_replace(',', '', number_format($monthly, 2)),
                            //                                     'modified_on' => time(),
                            //                                     'status' => 1,
                            //                                 );
                            //                             $this->ci->loan_invoices_m->update($invoice->id,$update);
                            //                             unset($update);
                            //                         }
                                                    
                            //                     }
                            //                     foreach ($paid_existing_invoices as $invoice) {
                            //                         $invoice_list[] = $this->ci->loan_invoices_m->get($invoice->id);
                            //                     }
                            //                     if($previous_amount_paid){
                            //                        //$this->repay_invoices($invoice_list,$previous_amount_paid);
                            //                     }
                            //                 }else{
                            //                     $invoices = $this->ci->loan_invoices_m->get_newest_invoices($loan_id);
                            //                     foreach ($invoices as $inv) {
                            //                         $interest = $loan_amount*$monthly_rate;
                            //                         $principle = $inv->principle_amount_payable;
                            //                         $monthly =$principle+$interest;
                            //                         $loan_amount -=$principle;
                            //                         if($interest<0){
                            //                             $interest = 0;
                            //                             $monthly = $principle;
                            //                         }
                            //                         $update = array(
                            //                                 'interest_amount_payable' => (float) str_replace(',', '', number_format($interest, 2)),
                            //                                 'amount_payable' => (float) str_replace(',', '', number_format($monthly, 2)),
                            //                                 'modified_on' => time(),
                            //                             );
                            //                         $this->ci->loan_invoices_m->update($inv->id,$update);
                            //                         unset($update);
                            //                     }
                            //                 }
                            //                 return TRUE;
                            //             }else{
                            //                 return FALSE;
                            //             }
                            //         }else{
                            //             return FALSE;
                            //         }
                            //     }else{
                            //         return TRUE;
                            //     } 
                            // }else{
                            //     return TRUE;
                            // }
                        }else{
                            return FALSE;
                        }
                    }else{
                        if($loan && $loan->interest_type==2){
                            $loan_balance = $this->ci->loan_invoices_m->get_installment_principle_balance($loan_id);
                            if($loan_balance){
                                $loan_amount = $loan_balance-$amount_paid;
                                $annual = number_format($loan->interest_rate / 100, 5);
                                $monthly_rate = number_format($annual / 12, 5);
                                $invoices = $this->ci->loan_invoices_m->get_newest_invoices($loan_id);
                                foreach ($invoices as $inv) {
                                    $interest = $loan_amount*$monthly_rate;
                                    $principle = $inv->principle_amount_payable;
                                    $monthly =$principle+$interest;
                                    $loan_amount -=$principle;
                                    if($interest<0){
                                        $interest = 0;
                                        $monthly = $principle;
                                    }
                                    $update = array(
                                            'interest_amount_payable' => (float) str_replace(',', '', number_format($interest, 2)),
                                            'amount_payable' => (float) str_replace(',', '', number_format($monthly, 2)),
                                            'modified_on' => time(),
                                        );
                                    $this->ci->loan_invoices_m->update($inv->id,$update);
                                    unset($update);
                                }
                                return TRUE;
                            }
                        }else{
                            return true;
                        }
                    }
                }else{
                    return TRUE; 
                }
            }
            
        }else{
            $this->ci->session->set_flashdata('error','Include all parameters');
            return FALSE;
        }
    }


    function recalculate_reducing_balance_after_voiding($loan_id=0,$amount_paid=0,$repayment_date=''){
        if($loan_id&&$amount_paid&&$repayment_date){
            $loan = $this->ci->loans_m->get($loan_id);       
            if($loan->enable_reducing_balance_installment_recalculation){
                if($loan_id){
                    //$loan_balance = $this->ci->loan_invoices_m->get_installment_principle_balance($loan_id);
                    $lump_sum_remaining = $this->ci->loan_invoices_m->get_loan_lump_sum_as_date($loan_id);
                    $invoices = $this->ci->loan_invoices_m->get_newest_invoices($loan_id,$repayment_date);
                    $annual = number_format($loan->interest_rate / 100, 5);
                    $monthly_rate = number_format($annual / 12, 5);
                    $monthly = ($monthly_rate / (1 - (pow((1 + $monthly_rate), -(count($invoices)))))) * $lump_sum_remaining;
                    foreach ($invoices as $inv) {
                        $principle = $inv->principle_amount_payable;
                        $interest = $monthly-$principle;
                        $lump_sum_remaining-=$principle;
                        if($interest<0){
                            $interest = 0;
                            $monthly = $principle;
                        }
                        $update = array(
                                'interest_amount_payable' => (float) str_replace(',', '', number_format($interest, 2)),
                                'amount_payable' => (float) str_replace(',', '', number_format($monthly, 2)),
                                'modified_on'=>time()
                            );
                        $this->ci->loan_invoices_m->update($inv->id,$update);
                        unset($update);
                    }
                }else{
                    return FALSE;
                }
            }else{
                return TRUE;
            }
        }else{
            return FALSE;
        }
    }


    // function recalculate_loan_balance_invoice_for_reducing($loan_id=0,$is_a_debtor=FALSE){
    //     if($loan_id){
    //         if($is_a_debtor){
    //             $loan = $this->ci->debtors_m->get_loan($loan_id);
    //             if($loan){
    //                 $grace_period = $loan->grace_period;
    //                 $disbursement_date = $loan->disbursement_date;
    //                 $loan_amount = $loan->loan_amount;
    //                 $repayment_period = $loan->repayment_period;
    //                 $interest_rate = $loan->interest_rate;
    //                 $loan_interest_rate_per = $loan->loan_interest_rate_per;
    //                 $interest_type = $loan->interest_type;
    //                 //$grace_period_date = $loan->grace_period_date;
    //                 if(empty($grace_period)){
    //                     $grace_period=1;
    //                 }
    //                 // if($grace_period==="date"){
    //                 //     $disbursement_date = $grace_period_date;
    //                 // }else{
    //                     $grace_period = $grace_period-1;
    //                     if($disbursement_date){

    //                     }else{
    //                         $disbursement_date=time();
    //                     }
    //                     $disbursement_date  = strtotime('+ '.$grace_period.' months',$disbursement_date?:time());
    //                 //}
    //                 if($loan_interest_rate_per==1){
    //                     $interest_rate = $interest_rate*365;
    //                 }else if($loan_interest_rate_per==2){
    //                     $interest_rate = $interest_rate*52;
    //                 }else if($loan_interest_rate_per==3){
    //                     $interest_rate = $interest_rate*12;
    //                 }else if($loan_interest_rate_per==5){
    //                     $interest_rate = ($interest_rate*12)/$repayment_period;
    //                 }
    //                 $decrement_count = FALSE;
    //                 if($interest_type==2 || $interest_type == 1){
    //                     $loan_invoices = array();
    //                     if($repayment_period){
    //                         if($interest_type == 2){
    //                             if($interest_rate){
    //                                 $annual = number_format($interest_rate / 100, 5);
    //                                 $monthly_rate = number_format($annual / 12, 5);
    //                                 $monthly = ($monthly_rate / (1 - (pow((1 + $monthly_rate), -($repayment_period))))) * $loan_amount;
    //                             }else{
    //                                 $monthly_rate = 0;
    //                                 $monthly = $loan_amount/$repayment_period;
    //                             }
    //                             for ($i = 0; $i < $repayment_period; $i++){
    //                                 $new_interest = $loan_amount * $monthly_rate;
    //                                 $new_principle = $monthly - $new_interest;

    //                                 $loan_amount -= $new_principle;
    //                                 $count = $i + 1;
    //                                 if($grace_period==="date"&&$count==1){
    //                                     $due_date = $disbursement_date;
    //                                     $decrement_count = TRUE; 
    //                                 }else{
    //                                     $due_date = $this->_date_add(date('d M Y',$disbursement_date), $count,$decrement_count);
    //                                 }
    //                                 $invoice_date = $due_date - (86400 * 7);
    //                                 $is_sent = 0;
    //                                 if(date('Ymd',$invoice_date)<=date('Ymd')){
    //                                     $is_sent = 1;
    //                                 }  
    //                                 $loan_invoices[] =array(
    //                                     'interest_amount_payable' => (float) str_replace(',', '', number_format($new_interest, 2)),
    //                                     'principle_amount_payable' => (float) str_replace(',', '', number_format($new_principle, 2)),
    //                                     'invoice_date' => $invoice_date,
    //                                     'due_date' => $due_date,
    //                                     'fine_date' => $due_date + (24 * 60 * 60),
    //                                     'amount_payable' => (float) str_replace(',', '', number_format($monthly, 2)),
    //                                     'is_sent' => $is_sent,
    //                                 );
    //                             }
    //                         }elseif ($interest_type == 1) {
    //                             $loan_invoices = $this->calculate_loan_balance_invoice($loan_amount,$interest_type,$loan->interest_rate,$repayment_period,$grace_period,$disbursement_date,$loan_interest_rate_per,'');
    //                         }
    //                     }
    //                     if($loan_invoices){  
    //                         $old_invoices = $this->ci->debtors_m->get_loan_principal_installments($loan->id,$loan->group_id);
    //                         $installment_array = array();
    //                         if($old_invoices){
    //                             foreach ($old_invoices as $old_invoice){
    //                                 $date = date('dmY',$old_invoice->due_date).'_'.date('dmY',$old_invoice->invoice_date);
    //                                 $installment_array[$date] = array(
    //                                     'amount_payable' => $old_invoice->amount_payable,
    //                                     'interest_amount_payable' => $old_invoice->interest_amount_payable,
    //                                     'principle_amount_payable' => $old_invoice->principle_amount_payable,
    //                                     'disable_fines' => $old_invoice->disable_fines,
    //                                     //'book_interest' => $old_invoice->book_interest,
    //                                     'is_sent' => $old_invoice->is_sent,
    //                                 );
    //                             }
    //                         }
    //                         $delete_invoices = $this->ci->debtors_m->delete_all_invoices($loan->id);
    //                         $delete_statements = $this->ci->debtors_m->delete_all_loan_invoices_and_fines_entries($loan->id);
    //                         if($delete_invoices){
    //                             $invoice_start_number = $this->ci->debtors_m->calculate_invoice_no($loan->group_id);
    //                             $invoice_numbers = array();
    //                             $member_ids = array();
    //                             $group_ids = array();
    //                             $loan_ids = array();
    //                             $types = array();
    //                             $is_sents = array();
    //                             $interest_amount_payables = array();
    //                             $principle_amount_payable = array();
    //                             $invoice_date = array();
    //                             $due_date = array();
    //                             $fine_date = array();
    //                             $amount_payable = array();
    //                             $amount_paid = array();
    //                             $active = array();
    //                             $created_by = array();
    //                             $created_on = array();
    //                             $disable_finess = array();
    //                             $book_interests = array();
    //                             $is_a_back_dating_records = array();
    //                             foreach ($loan_invoices as $key => $invoice) {
    //                                 $invoice = (object)$invoice;
    //                                 $date_range = date('dmY',$invoice->due_date).'_'.date('dmY',$invoice->invoice_date);
    //                                 if(array_key_exists($date_range, $installment_array)){
    //                                     $disable_finess[] = $installment_array[$date_range]['disable_fines']; 
    //                                     //$book_interests[] = $installment_array[$date_range]['book_interest']; 
    //                                 }else{
    //                                     $disable_finess[] = '';
    //                                     $book_interests[] = '';
    //                                 }
    //                                 $is_sent = 0;
    //                                 if(isset($invoice->is_sent)){
    //                                     $is_sent = $invoice->is_sent;
    //                                 }else{
    //                                     if(date('Ymd',$invoice->invoice_date)<=date('Ymd')){
    //                                         $is_sent = 1;
    //                                     } 
    //                                 }
    //                                 $invoice_numbers[] = $invoice_start_number+$key;
    //                                 $debtor_ids[] = $loan->debtor_id;
    //                                 $group_ids[] = $loan->group_id;
    //                                 $loan_ids[] = $loan->id;
    //                                 $types[] = 1;
    //                                 $is_sents[] = $is_sent;
    //                                 $interest_amount_payables[] = $invoice->interest_amount_payable;
    //                                 $principle_amount_payables[] = $invoice->principle_amount_payable;
    //                                 $invoice_dates[]  = $invoice->invoice_date;
    //                                 $due_dates[]  = $invoice->due_date;
    //                                 $fine_dates[]  = $invoice->fine_date;
    //                                 $amount_payables[]  = $invoice->amount_payable;
    //                                 $amount_paids[]  = 0;
    //                                 $actives[]  = 1;
    //                                 $created_bys[]  = $loan->created_by;
    //                                 $created_ons[]  = time();
    //                                 $is_a_back_dating_records[] = 0;
    //                             }
    //                             $input = array(
    //                                     'invoice_no'    => $invoice_numbers,
    //                                     'debtor_id'     => $debtor_ids,
    //                                     'group_id'      => $group_ids,
    //                                     'debtor_loan_id'       =>  $loan_ids,
    //                                     'type'          =>  $types,
    //                                     'is_sent'       =>  $is_sents,
    //                                     'interest_amount_payable'   => $interest_amount_payables,
    //                                     'principle_amount_payable'  => $principle_amount_payables,
    //                                     'invoice_date'  =>  $invoice_dates,
    //                                     'due_date'      =>  $due_dates,
    //                                     'fine_date'     =>  $fine_dates,
    //                                     'disable_fines' =>  $disable_finess,
    //                                     'amount_payable'=>  $amount_payables,
    //                                     'amount_paid'   =>  $amount_paids,
    //                                     'active'        =>  $actives,
    //                                     'created_by'    =>  $created_bys,
    //                                     //'book_interest' => $book_interests,
    //                                     'created_on'    =>  $created_ons,
    //                                     'is_a_back_dating_record'    =>  $is_a_back_dating_records,
    //                                 );
    //                             if($this->ci->debtors_m->insert_batch($input)){
    //                             }  
    //                             $transfered_invoices = $this->ci->debtors_m->get_transfer_out_invoice($loan->id);
    //                             if($transfered_invoices){
    //                                 foreach ($transfered_invoices as $transfered_invoice) {
    //                                     //$statement = $this->ci->statements_m->get_group_contribution_transfer_by_loan_invoice($transfered_invoice->loan_invoice_id,$transfered_invoice->group_id);
    //                                     $new_post = array(
    //                                         'invoice_no'    => time(),
    //                                         'debtor_id'     => $transfered_invoice->debtor_id,
    //                                         'group_id'      => $transfered_invoice->group_id,
    //                                         'debtor_id'       =>  $transfered_invoice->debtor_id,
    //                                         'type'          =>  5,
    //                                         'is_sent'       =>  1,
    //                                         'interest_amount_payable'   => 0,
    //                                         'principle_amount_payable'  => $transfered_invoice->amount,
    //                                         'invoice_date'  =>  $transfered_invoice->transaction_date,
    //                                         'due_date'      =>  $transfered_invoice->transaction_date,
    //                                         'fine_date'     =>  '',
    //                                         'amount_payable'=>  $transfered_invoice->amount,
    //                                         'amount_paid'   =>  0,
    //                                         'active'        =>  1,
    //                                         'created_on'    =>  time(),
    //                                         //'transfer_to'   =>  $transfered_invoice->transfer_to,
    //                                         //'contribution_transfer_id'=> ($statement)?$statement->contribution_transfer_id:'',
    //                                     );
    //                                     if($invoice_id = $this->ci->loan_invoices_m->insert($new_post)){
    //                                         //if($statement){
    //                                             //$this->ci->statements_m->update($statement->id,array('loan_transfer_invoice_id'=>$invoice_id));
    //                                         //}
    //                                         $this->ci->debtors_m->update_statement($transfered_invoice->id,array('loan_invoice_id'=>$invoice_id));
    //                                     }
    //                                 }
    //                             }                                      
    //                             $past_invoice_id = $this->send_past_invoices($loan->id,TRUE);
    //                             if($past_invoice_id){
    //                                 $outstanding_balance_fine_date = $this->set_outstanding_balance_fine_date($loan->id,TRUE);
    //                                 if($outstanding_balance_fine_date){
    //                                     $fix_loan_invoices_fine = $this->fix_loan_invoices_fine($loan->id,TRUE);
    //                                     if($fix_loan_invoices_fine){
    //                                         $outstanding_balance_fixer = $this->outstanding_loan_balance_fine_fixer($loan,TRUE);
    //                                         if($outstanding_balance_fixer){;
    //                                             return TRUE;
    //                                         }
    //                                         else{
    //                                             return FALSE;
    //                                         }
    //                                     }
    //                                 }else{
    //                                     $this->ci->session->set_flashdata('error','Unable to set the outstanding balance fine date');
    //                                     return FALSE;
    //                                 }
    //                             }
    //                             else{
    //                                 $this->ci->session->set_flashdata('error','Unable to send past invoices');
    //                                 return FALSE;
    //                             }
    //                         }else{
    //                             return FALSE;
    //                         }
    //                     }
    //                     else{
    //                         return FALSE;
    //                     }
    //                 }else{
    //                     return FALSE;
    //                 }
    //             }
    //         }else{
    //             $loan = $this->ci->loans_m->get($loan_id);
    //             if($loan){
    //                 $grace_period = $loan->grace_period;
    //                 $disbursement_date = $loan->disbursement_date;
    //                 $loan_amount = $loan->loan_amount;
    //                 $repayment_period = $loan->repayment_period;
    //                 $interest_rate = $loan->interest_rate;
    //                 $loan_interest_rate_per = $loan->loan_interest_rate_per;
    //                 $interest_type = $loan->interest_type;
    //                 $grace_period_date = $loan->grace_period_date;
    //                 if(empty($grace_period)){
    //                     $grace_period=1;
    //                 }
    //                 if($grace_period==="date"){
    //                     $disbursement_date = $grace_period_date;
    //                 }else{
    //                     $grace_period = $grace_period-1;
    //                     if($disbursement_date){

    //                     }else{
    //                         $disbursement_date=time();
    //                     }
    //                     $disbursement_date  = strtotime('+ '.$grace_period.' months',$disbursement_date?:time());
    //                 }
    //                 if($loan_interest_rate_per==1){
    //                     $interest_rate = $interest_rate*365;
    //                 }else if($loan_interest_rate_per==2){
    //                     $interest_rate = $interest_rate*52;
    //                 }else if($loan_interest_rate_per==3){
    //                     $interest_rate = $interest_rate*12;
    //                 }else if($loan_interest_rate_per==5){
    //                     $interest_rate = ($interest_rate*12)/$repayment_period;
    //                 }
    //                 $decrement_count = FALSE;
    //                 if($interest_type==2 || $interest_type == 1){
    //                     $loan_invoices = array();
    //                     if($repayment_period){
    //                         if($interest_type == 2){
    //                             if($interest_rate){
    //                                 $annual = number_format($interest_rate / 100, 5);
    //                                 $monthly_rate = number_format($annual / 12, 5);

    //                                 $monthly = ($monthly_rate / (1 - (pow((1 + $monthly_rate), -($repayment_period))))) * $loan_amount;
    //                             }else{
    //                                 $monthly_rate = 0;
    //                                 $monthly = $loan_amount/$repayment_period;
    //                             }
    //                             for ($i = 0; $i < $repayment_period; $i++){
    //                                 $new_interest = $loan_amount * $monthly_rate;
    //                                 $new_principle = $monthly - $new_interest;

    //                                 $loan_amount -= $new_principle;
    //                                 $count = $i + 1;
    //                                 if($grace_period==="date"&&$count==1){
    //                                     $due_date = $disbursement_date;
    //                                     $decrement_count = TRUE; 
    //                                 }else{
    //                                     $due_date = $this->_date_add(date('d M Y',$disbursement_date), $count,$decrement_count);
    //                                 }
    //                                 $invoice_date = $due_date - (86400 * 7);
    //                                 $is_sent = 0;
    //                                 if(date('Ymd',$invoice_date)<=date('Ymd')){
    //                                     $is_sent = 1;
    //                                 }  
    //                                 $loan_invoices[] =array(
    //                                     'interest_amount_payable' => (float) str_replace(',', '', number_format($new_interest, 2)),
    //                                     'principle_amount_payable' => (float) str_replace(',', '', number_format($new_principle, 2)),
    //                                     'invoice_date' => $invoice_date,
    //                                     'due_date' => $due_date,
    //                                     'fine_date' => $due_date + (24 * 60 * 60),
    //                                     'amount_payable' => (float) str_replace(',', '', number_format($monthly, 2)),
    //                                     'is_sent' => $is_sent,
    //                                 );
    //                             }
    //                         }elseif ($interest_type == 1) {
    //                             $loan_invoices = $this->calculate_loan_balance_invoice($loan_amount,$interest_type,$loan->interest_rate,$repayment_period,$grace_period,$disbursement_date,$loan_interest_rate_per,$grace_period_date);
    //                         }
    //                     }
    //                     if($loan_invoices){  
    //                         $old_invoices = $this->ci->loan_invoices_m->get_loan_principal_installments($loan->id,$loan->group_id);
    //                         $installment_array = array();
    //                         if($old_invoices){
    //                             foreach ($old_invoices as $old_invoice){
    //                                 $date = date('dmY',$old_invoice->due_date).'_'.date('dmY',$old_invoice->invoice_date);
    //                                 $installment_array[$date] = array(
    //                                     'amount_payable' => $old_invoice->amount_payable,
    //                                     'interest_amount_payable' => $old_invoice->interest_amount_payable,
    //                                     'principle_amount_payable' => $old_invoice->principle_amount_payable,
    //                                     'disable_fines' => $old_invoice->disable_fines,
    //                                     'book_interest' => $old_invoice->book_interest,
    //                                     'is_sent' => $old_invoice->is_sent,
    //                                 );
    //                             }
    //                         }
    //                         $delete_invoices = $this->ci->loan_invoices_m->delete_all_invoices($loan->id);
    //                         $delete_statements = $this->ci->loans_m->delete_all_loan_invoices_and_fines_entries($loan->id);
    //                         if($delete_invoices){
    //                             $invoice_start_number = $this->ci->loan_invoices_m->calculate_invoice_no($loan->group_id);
    //                             $invoice_numbers = array();
    //                             $member_ids = array();
    //                             $group_ids = array();
    //                             $loan_ids = array();
    //                             $types = array();
    //                             $is_sents = array();
    //                             $interest_amount_payables = array();
    //                             $principle_amount_payable = array();
    //                             $invoice_date = array();
    //                             $due_date = array();
    //                             $fine_date = array();
    //                             $amount_payable = array();
    //                             $amount_paid = array();
    //                             $active = array();
    //                             $created_by = array();
    //                             $created_on = array();
    //                             $disable_finess = array();
    //                             $book_interests = array();
    //                             $is_a_back_dating_records = array();
    //                             foreach ($loan_invoices as $key => $invoice) {
    //                                 $invoice = (object)$invoice;
    //                                 $date_range = date('dmY',$invoice->due_date).'_'.date('dmY',$invoice->invoice_date);
    //                                 if(array_key_exists($date_range, $installment_array)){
    //                                     $disable_finess[] = $installment_array[$date_range]['disable_fines']; 
    //                                     $book_interests[] = $installment_array[$date_range]['book_interest']; 
    //                                 }else{
    //                                     $disable_finess[] = '';
    //                                     $book_interests[] = '';
    //                                 }
    //                                 $is_sent = 0;
    //                                 if(isset($invoice->is_sent)){
    //                                     $is_sent = $invoice->is_sent;
    //                                 }else{
    //                                     if(date('Ymd',$invoice->invoice_date)<=date('Ymd')){
    //                                         $is_sent = 1;
    //                                     } 
    //                                 }
    //                                 $invoice_numbers[] = $invoice_start_number+$key;
    //                                 $member_ids[] = $loan->member_id;
    //                                 $group_ids[] = $loan->group_id;
    //                                 $loan_ids[] = $loan->id;
    //                                 $types[] = 1;
    //                                 $is_sents[] = $is_sent;
    //                                 $interest_amount_payables[] = $invoice->interest_amount_payable;
    //                                 $principle_amount_payables[] = $invoice->principle_amount_payable;
    //                                 $invoice_dates[]  = $invoice->invoice_date;
    //                                 $due_dates[]  = $invoice->due_date;
    //                                 $fine_dates[]  = $invoice->fine_date;
    //                                 $amount_payables[]  = $invoice->amount_payable;
    //                                 $amount_paids[]  = 0;
    //                                 $actives[]  = 1;
    //                                 $created_bys[]  = $loan->created_by;
    //                                 $created_ons[]  = time();
    //                                 $is_a_back_dating_records[] = 0;
    //                             }
    //                             $input = array(
    //                                     'invoice_no'    => $invoice_numbers,
    //                                     'member_id'     => $member_ids,
    //                                     'group_id'      => $group_ids,
    //                                     'loan_id'       =>  $loan_ids,
    //                                     'type'          =>  $types,
    //                                     'is_sent'       =>  $is_sents,
    //                                     'interest_amount_payable'   => $interest_amount_payables,
    //                                     'principle_amount_payable'  => $principle_amount_payables,
    //                                     'invoice_date'  =>  $invoice_dates,
    //                                     'due_date'      =>  $due_dates,
    //                                     'fine_date'     =>  $fine_dates,
    //                                     'disable_fines' =>  $disable_finess,
    //                                     'amount_payable'=>  $amount_payables,
    //                                     'amount_paid'   =>  $amount_paids,
    //                                     'active'        =>  $actives,
    //                                     'created_by'    =>  $created_bys,
    //                                     'book_interest' => $book_interests,
    //                                     'created_on'    =>  $created_ons,
    //                                     'is_a_back_dating_record'    =>  $is_a_back_dating_records,
    //                                 );
    //                             if($this->ci->loan_invoices_m->insert_batch($input)){
    //                             }  
    //                             $transfered_invoices = $this->ci->loans_m->get_transfer_out_invoice($loan->id);
    //                             if($transfered_invoices){
    //                                 foreach ($transfered_invoices as $transfered_invoice) {
    //                                     $statement = $this->ci->statements_m->get_group_contribution_transfer_by_loan_invoice($transfered_invoice->loan_invoice_id,$transfered_invoice->group_id);
    //                                     $new_post = array(
    //                                         'invoice_no'    => time(),
    //                                         'member_id'     => $transfered_invoice->member_id,
    //                                         'group_id'      => $transfered_invoice->group_id,
    //                                         'loan_id'       =>  $transfered_invoice->loan_id,
    //                                         'type'          =>  5,
    //                                         'is_sent'       =>  1,
    //                                         'interest_amount_payable'   => 0,
    //                                         'principle_amount_payable'  => $transfered_invoice->amount,
    //                                         'invoice_date'  =>  $transfered_invoice->transaction_date,
    //                                         'due_date'      =>  $transfered_invoice->transaction_date,
    //                                         'fine_date'     =>  '',
    //                                         'amount_payable'=>  $transfered_invoice->amount,
    //                                         'amount_paid'   =>  0,
    //                                         'active'        =>  1,
    //                                         'created_on'    =>  time(),
    //                                         'transfer_to'   =>  $transfered_invoice->transfer_to,
    //                                         'contribution_transfer_id'=> ($statement)?$statement->contribution_transfer_id:'',
    //                                     );
    //                                     if($invoice_id = $this->ci->loan_invoices_m->insert($new_post)){
    //                                         if($statement){
    //                                             $this->ci->statements_m->update($statement->id,array('loan_transfer_invoice_id'=>$invoice_id));
    //                                         }
    //                                         $this->ci->loans_m->update_statement($transfered_invoice->id,array('loan_invoice_id'=>$invoice_id));
    //                                     }
    //                                 }
    //                             }                                      
    //                             $past_invoice_id = $this->send_past_invoices($loan->id);
    //                             if($past_invoice_id){
    //                                 $outstanding_balance_fine_date = $this->set_outstanding_balance_fine_date($loan->id);
    //                                 if($outstanding_balance_fine_date){
    //                                     $fix_loan_invoices_fine = $this->fix_loan_invoices_fine($loan->id);
    //                                     if($fix_loan_invoices_fine){
    //                                         $outstanding_balance_fixer = $this->outstanding_loan_balance_fine_fixer($loan);
    //                                         if($outstanding_balance_fixer){;
    //                                             return TRUE;
    //                                         }
    //                                         else{
    //                                             return FALSE;
    //                                         }
    //                                     }
    //                                 }else{
    //                                     $this->ci->session->set_flashdata('error','Unable to set the outstanding balance fine date');
    //                                     return FALSE;
    //                                 }
    //                             }
    //                             else{
    //                                 $this->ci->session->set_flashdata('error','Unable to send past invoices');
    //                                 return FALSE;
    //                             }
    //                         }else{
    //                             return FALSE;
    //                         }
    //                     }
    //                     else{
    //                         return FALSE;
    //                     }
    //                 }else{
    //                     return FALSE;
    //                 }
    //             }
    //         }
    //     }else{
    //         return FALSE;
    //     }
    // }

    function recalculate_loan_balance_invoice_for_reducing($loan='',$is_a_debtor=FALSE){
        if($is_a_debtor){
            if($loan){
                $grace_period = $loan->grace_period;
                $disbursement_date = $loan->disbursement_date;
                $loan_amount = $loan->loan_amount;
                $repayment_period = $loan->repayment_period;
                $interest_rate = $loan->interest_rate;
                $loan_interest_rate_per = $loan->loan_interest_rate_per;
                $interest_type = $loan->interest_type;
                //$grace_period_date = $loan->grace_period_date;
                if(empty($grace_period)){
                    $grace_period=1;
                }
                // if($grace_period==="date"){
                //     $disbursement_date = $grace_period_date;
                // }else{
                    $grace_period = $grace_period-1;
                    if($disbursement_date){

                    }else{
                        $disbursement_date=time();
                    }
                    $disbursement_date  = strtotime('+ '.$grace_period.' months',$disbursement_date?:time());
                //}
                if($loan_interest_rate_per==1){
                    $interest_rate = $interest_rate*365;
                }else if($loan_interest_rate_per==2){
                    $interest_rate = $interest_rate*52;
                }else if($loan_interest_rate_per==3){
                    $interest_rate = $interest_rate*12;
                }else if($loan_interest_rate_per==5){
                    $interest_rate = ($interest_rate*12)/$repayment_period;
                }
                $decrement_count = FALSE;
                if($interest_type==2 || $interest_type == 1){
                    $loan_invoices = array();
                    if($repayment_period){
                        if($interest_type == 2){
                            if($interest_rate){
                                $annual = number_format($interest_rate / 100, 5);
                                $monthly_rate = number_format($annual / 12, 5);
                                $monthly = ($monthly_rate / (1 - (pow((1 + $monthly_rate), -($repayment_period))))) * $loan_amount;
                            }else{
                                $monthly_rate = 0;
                                $monthly = $loan_amount/$repayment_period;
                            }
                            for ($i = 0; $i < $repayment_period; $i++){
                                $new_interest = $loan_amount * $monthly_rate;
                                $new_principle = $monthly - $new_interest;

                                $loan_amount -= $new_principle;
                                $count = $i + 1;
                                if($grace_period==="date"&&$count==1){
                                    $due_date = $disbursement_date;
                                    $decrement_count = TRUE; 
                                }else{
                                    $due_date = $this->_date_add(date('d M Y',$disbursement_date), $count,$decrement_count);
                                }
                                $invoice_date = $due_date - (86400 * 7);
                                $is_sent = 0;
                                if(date('Ymd',$invoice_date)<=date('Ymd')){
                                    $is_sent = 1;
                                }  
                                $loan_invoices[] =array(
                                    'interest_amount_payable' => (float) str_replace(',', '', number_format($new_interest, 2)),
                                    'principle_amount_payable' => (float) str_replace(',', '', number_format($new_principle, 2)),
                                    'invoice_date' => $invoice_date,
                                    'due_date' => $due_date,
                                    'fine_date' => $due_date + (24 * 60 * 60),
                                    'amount_payable' => (float) str_replace(',', '', number_format($monthly, 2)),
                                    'is_sent' => $is_sent,
                                );
                            }
                        }elseif ($interest_type == 1) {
                            $loan_invoices = $this->calculate_loan_balance_invoice($loan_amount,$interest_type,$loan->interest_rate,$repayment_period,$grace_period,$disbursement_date,$loan_interest_rate_per,'');
                        }
                    }
                    if($loan_invoices){  
                        $old_invoices = $this->ci->debtors_m->get_loan_principal_installments($loan->id,$loan->group_id);
                        $installment_array = array();
                        if($old_invoices){
                            foreach ($old_invoices as $old_invoice){
                                $date = date('dmY',$old_invoice->due_date).'_'.date('dmY',$old_invoice->invoice_date);
                                $installment_array[$date] = array(
                                    'amount_payable' => $old_invoice->amount_payable,
                                    'interest_amount_payable' => $old_invoice->interest_amount_payable,
                                    'principle_amount_payable' => $old_invoice->principle_amount_payable,
                                    'disable_fines' => $old_invoice->disable_fines,
                                    //'book_interest' => $old_invoice->book_interest,
                                    'is_sent' => $old_invoice->is_sent,
                                );
                            }
                        }
                        $delete_invoices = $this->ci->debtors_m->delete_all_invoices($loan->id,$loan->group_id);
                        $delete_statements = $this->ci->debtors_m->delete_all_loan_invoices_and_fines_entries($loan->id,$loan->group_id);
                        if($delete_invoices){
                            $invoice_start_number = $this->ci->debtors_m->calculate_invoice_no($loan->group_id);
                            $invoice_numbers = array();
                            $member_ids = array();
                            $group_ids = array();
                            $loan_ids = array();
                            $types = array();
                            $is_sents = array();
                            $interest_amount_payables = array();
                            $principle_amount_payable = array();
                            $invoice_date = array();
                            $due_date = array();
                            $fine_date = array();
                            $amount_payable = array();
                            $amount_paid = array();
                            $active = array();
                            $created_by = array();
                            $created_on = array();
                            $disable_finess = array();
                            $book_interests = array();
                            $is_a_back_dating_records = array();
                            foreach ($loan_invoices as $key => $invoice) {
                                $invoice = (object)$invoice;
                                $date_range = date('dmY',$invoice->due_date).'_'.date('dmY',$invoice->invoice_date);
                                if(array_key_exists($date_range, $installment_array)){
                                    $disable_finess[] = $installment_array[$date_range]['disable_fines']; 
                                    //$book_interests[] = $installment_array[$date_range]['book_interest']; 
                                }else{
                                    $disable_finess[] = '';
                                    $book_interests[] = '';
                                }
                                $is_sent = 0;
                                if(isset($invoice->is_sent)){
                                    $is_sent = $invoice->is_sent;
                                }else{
                                    if(date('Ymd',$invoice->invoice_date)<=date('Ymd')){
                                        $is_sent = 1;
                                    } 
                                }
                                $invoice_numbers[] = $invoice_start_number+$key;
                                $debtor_ids[] = $loan->debtor_id;
                                $group_ids[] = $loan->group_id;
                                $loan_ids[] = $loan->id;
                                $types[] = 1;
                                $is_sents[] = $is_sent;
                                $interest_amount_payables[] = $invoice->interest_amount_payable;
                                $principle_amount_payables[] = $invoice->principle_amount_payable;
                                $invoice_dates[]  = $invoice->invoice_date;
                                $due_dates[]  = $invoice->due_date;
                                $fine_dates[]  = $invoice->fine_date;
                                $amount_payables[]  = $invoice->amount_payable;
                                $amount_paids[]  = 0;
                                $actives[]  = 1;
                                $created_bys[]  = $loan->created_by;
                                $created_ons[]  = time();
                                $is_a_back_dating_records[] = 0;
                            }
                            $input = array(
                                    'invoice_no'    => $invoice_numbers,
                                    'debtor_id'     => $debtor_ids,
                                    'group_id'      => $group_ids,
                                    'debtor_loan_id'       =>  $loan_ids,
                                    'type'          =>  $types,
                                    'is_sent'       =>  $is_sents,
                                    'interest_amount_payable'   => $interest_amount_payables,
                                    'principle_amount_payable'  => $principle_amount_payables,
                                    'invoice_date'  =>  $invoice_dates,
                                    'due_date'      =>  $due_dates,
                                    'fine_date'     =>  $fine_dates,
                                    'disable_fines' =>  $disable_finess,
                                    'amount_payable'=>  $amount_payables,
                                    'amount_paid'   =>  $amount_paids,
                                    'active'        =>  $actives,
                                    'created_by'    =>  $created_bys,
                                    //'book_interest' => $book_interests,
                                    'created_on'    =>  $created_ons,
                                    'is_a_back_dating_record'    =>  $is_a_back_dating_records,
                                );
                            if($this->ci->debtors_m->insert_batch($input)){
                            }  
                            $transfered_invoices = $this->ci->debtors_m->get_transfer_out_invoice($loan->id,$loan->group_id);
                            if($transfered_invoices){
                                foreach ($transfered_invoices as $transfered_invoice) {
                                    //$statement = $this->ci->statements_m->get_group_contribution_transfer_by_loan_invoice($transfered_invoice->loan_invoice_id,$transfered_invoice->group_id);
                                    $new_post = array(
                                        'invoice_no'    => time(),
                                        'debtor_id'     => $transfered_invoice->debtor_id,
                                        'group_id'      => $transfered_invoice->group_id,
                                        'debtor_id'       =>  $transfered_invoice->debtor_id,
                                        'type'          =>  5,
                                        'is_sent'       =>  1,
                                        'interest_amount_payable'   => 0,
                                        'principle_amount_payable'  => $transfered_invoice->amount,
                                        'invoice_date'  =>  $transfered_invoice->transaction_date,
                                        'due_date'      =>  $transfered_invoice->transaction_date,
                                        'fine_date'     =>  '',
                                        'amount_payable'=>  $transfered_invoice->amount,
                                        'amount_paid'   =>  0,
                                        'active'        =>  1,
                                        'created_on'    =>  time(),
                                        //'transfer_to'   =>  $transfered_invoice->transfer_to,
                                        //'contribution_transfer_id'=> ($statement)?$statement->contribution_transfer_id:'',
                                    );
                                    if($invoice_id = $this->ci->loan_invoices_m->insert($new_post)){
                                        //if($statement){
                                            //$this->ci->statements_m->update($statement->id,array('loan_transfer_invoice_id'=>$invoice_id));
                                        //}
                                        $this->ci->debtors_m->update_statement($transfered_invoice->id,array('loan_invoice_id'=>$invoice_id));
                                    }
                                }
                            }                                      
                            $past_invoice_id = $this->send_past_invoices($loan->id,TRUE,$loan->group_id);
                            if($past_invoice_id){
                                $outstanding_balance_fine_date = $this->set_outstanding_balance_fine_date($loan->id,TRUE,$loan->group_id);
                                if($outstanding_balance_fine_date){
                                    $fix_loan_invoices_fine = $this->fix_loan_invoices_fine($loan->id,TRUE);
                                    if($fix_loan_invoices_fine){
                                        $outstanding_balance_fixer = $this->outstanding_loan_balance_fine_fixer($loan,TRUE);
                                        if($outstanding_balance_fixer){;
                                            return TRUE;
                                        }
                                        else{
                                            return FALSE;
                                        }
                                    }
                                }else{
                                    $this->ci->session->set_flashdata('error','Unable to set the outstanding balance fine date');
                                    return FALSE;
                                }
                            }
                            else{
                                $this->ci->session->set_flashdata('error','Unable to send past invoices');
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }
                    else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }
        }else{
            if($loan){
                $grace_period = $loan->grace_period;
                $disbursement_date = $loan->disbursement_date;
                $loan_amount = $loan->loan_amount;
                $repayment_period = $loan->repayment_period;
                $interest_rate = $loan->interest_rate;
                $loan_interest_rate_per = $loan->loan_interest_rate_per;
                $interest_type = $loan->interest_type;
                $grace_period_date = $loan->grace_period_date;
                if(empty($grace_period)){
                    $grace_period=1;
                }
                if($grace_period==="date"){
                    $disbursement_date = $grace_period_date;
                }else{
                    $grace_period = $grace_period-1;
                    if($disbursement_date){

                    }else{
                        $disbursement_date=time();
                    }
                    $disbursement_date  = strtotime('+ '.$grace_period.' months',$disbursement_date?:time());
                }
                if($loan_interest_rate_per==1){
                    $interest_rate = $interest_rate*365;
                }else if($loan_interest_rate_per==2){
                    $interest_rate = $interest_rate*52;
                }else if($loan_interest_rate_per==3){
                    $interest_rate = $interest_rate*12;
                }else if($loan_interest_rate_per==5){
                    $interest_rate = ($interest_rate*12)/$repayment_period;
                }
                $decrement_count = FALSE;
                if($interest_type==2 || $interest_type == 1){
                    $loan_invoices = array();
                    if($repayment_period){
                        if($interest_type == 2){
                            if($interest_rate){
                                $annual = number_format($interest_rate / 100, 5);
                                $monthly_rate = number_format($annual / 12, 5);

                                $monthly = ($monthly_rate / (1 - (pow((1 + $monthly_rate), -($repayment_period))))) * $loan_amount;
                            }else{
                                $monthly_rate = 0;
                                $monthly = $loan_amount/$repayment_period;
                            }
                            for ($i = 0; $i < $repayment_period; $i++){
                                $new_interest = $loan_amount * $monthly_rate;
                                $new_principle = $monthly - $new_interest;

                                $loan_amount -= $new_principle;
                                $count = $i + 1;
                                if($grace_period==="date"&&$count==1){
                                    $due_date = $disbursement_date;
                                    $decrement_count = TRUE; 
                                }else{
                                    $due_date = $this->_date_add(date('d M Y',$disbursement_date), $count,$decrement_count);
                                }
                                $invoice_date = $due_date - (86400 * 7);
                                $is_sent = 0;
                                if(date('Ymd',$invoice_date)<=date('Ymd')){
                                    $is_sent = 1;
                                }  
                                $loan_invoices[] =array(
                                    'interest_amount_payable' => (float) str_replace(',', '', number_format($new_interest, 2)),
                                    'principle_amount_payable' => (float) str_replace(',', '', number_format($new_principle, 2)),
                                    'invoice_date' => $invoice_date,
                                    'due_date' => $due_date,
                                    'fine_date' => $due_date + (24 * 60 * 60),
                                    'amount_payable' => (float) str_replace(',', '', number_format($monthly, 2)),
                                    'is_sent' => $is_sent,
                                );
                            }
                        }elseif ($interest_type == 1) {
                            $loan_invoices = $this->calculate_loan_balance_invoice($loan_amount,$interest_type,$loan->interest_rate,$repayment_period,$grace_period,$disbursement_date,$loan_interest_rate_per,$grace_period_date);
                        }
                    }
                    if($loan_invoices){  
                        $old_invoices = $this->ci->loan_invoices_m->get_loan_principal_installments($loan->id,$loan->group_id);
                        $installment_array = array();
                        if($old_invoices){
                            foreach ($old_invoices as $old_invoice){
                                $date = date('dmY',$old_invoice->due_date).'_'.date('dmY',$old_invoice->invoice_date);
                                $installment_array[$date] = array(
                                    'amount_payable' => $old_invoice->amount_payable,
                                    'interest_amount_payable' => $old_invoice->interest_amount_payable,
                                    'principle_amount_payable' => $old_invoice->principle_amount_payable,
                                    'disable_fines' => $old_invoice->disable_fines,
                                    'book_interest' => $old_invoice->book_interest,
                                    'disable_interest' => $old_invoice->disable_interest,
                                    'is_sent' => $old_invoice->is_sent,
                                );
                            }
                        }
                        $delete_invoices = $this->ci->loan_invoices_m->delete_all_invoices($loan->id,$loan->group_id);
                        $delete_statements = $this->ci->loans_m->delete_all_loan_invoices_and_fines_entries($loan->id,$loan->group_id);
                        if($delete_invoices){
                            $invoice_start_number = $this->ci->loan_invoices_m->calculate_invoice_no($loan->group_id);
                            $invoice_numbers = array();
                            $member_ids = array();
                            $group_ids = array();
                            $loan_ids = array();
                            $types = array();
                            $is_sents = array();
                            $interest_amount_payables = array();
                            $principle_amount_payable = array();
                            $invoice_date = array();
                            $due_date = array();
                            $fine_date = array();
                            $amount_payable = array();
                            $amount_paid = array();
                            $active = array();
                            $created_by = array();
                            $created_on = array();
                            $disable_finess = array();
                            $book_interests = array();
                            $disable_interests = array();
                            $is_a_back_dating_records = array();
                            foreach ($loan_invoices as $key => $invoice) {
                                $invoice = (object)$invoice;
                                $disable_int = false;
                                $date_range = date('dmY',$invoice->due_date).'_'.date('dmY',$invoice->invoice_date);
                                if(array_key_exists($date_range, $installment_array)){
                                    $disable_finess[] = $installment_array[$date_range]['disable_fines']; 
                                    $book_interests[] = $installment_array[$date_range]['book_interest']; 
                                    $disable_interests[] = $installment_array[$date_range]['disable_interest']; 
                                    if($installment_array[$date_range]['disable_interest'] == 1){
                                        $disable_int = true;
                                    }
                                }else{
                                    $disable_finess[] = '';
                                    $book_interests[] = '';
                                    $disable_interests[] = '';
                                }
                                $is_sent = 0;
                                if(isset($invoice->is_sent)){
                                    $is_sent = $invoice->is_sent;
                                }else{
                                    if(date('Ymd',$invoice->invoice_date)<=date('Ymd')){
                                        $is_sent = 1;
                                    } 
                                }

                                if($disable_int){
                                    $interest_payable = 0;
                                    $principle_payable = $invoice->principle_amount_payable;
                                    $amount_payable = $invoice->amount_payable - $invoice->interest_amount_payable;
                                }else{
                                    $interest_payable = $invoice->interest_amount_payable;
                                    $principle_payable = $invoice->principle_amount_payable;
                                    $amount_payable = $invoice->amount_payable;
                                }
                                $invoice_numbers[] = $invoice_start_number+$key;
                                $member_ids[] = $loan->member_id;
                                $group_ids[] = $loan->group_id;
                                $loan_ids[] = $loan->id;
                                $types[] = 1;
                                $is_sents[] = $is_sent;
                                $interest_amount_payables[] = $interest_payable;
                                $principle_amount_payables[] = $principle_payable;
                                $invoice_dates[]  = $invoice->invoice_date;
                                $due_dates[]  = $invoice->due_date;
                                $fine_dates[]  = $invoice->fine_date;
                                $amount_payables[]  = $amount_payable;
                                $amount_paids[]  = 0;
                                $actives[]  = 1;
                                $created_bys[]  = $loan->created_by;
                                $created_ons[]  = time();
                                $is_a_back_dating_records[] = 0;
                            }
                            $input = array(
                                    'invoice_no'    => $invoice_numbers,
                                    'member_id'     => $member_ids,
                                    'group_id'      => $group_ids,
                                    'loan_id'       =>  $loan_ids,
                                    'type'          =>  $types,
                                    'is_sent'       =>  $is_sents,
                                    'interest_amount_payable'   => $interest_amount_payables,
                                    'principle_amount_payable'  => $principle_amount_payables,
                                    'invoice_date'  =>  $invoice_dates,
                                    'due_date'      =>  $due_dates,
                                    'fine_date'     =>  $fine_dates,
                                    'disable_fines' =>  $disable_finess,
                                    'amount_payable'=>  $amount_payables,
                                    'amount_paid'   =>  $amount_paids,
                                    'active'        =>  $actives,
                                    'created_by'    =>  $created_bys,
                                    'book_interest' => $book_interests,
                                    'disable_interest' => $disable_interests,
                                    'created_on'    =>  $created_ons,
                                    'is_a_back_dating_record'    =>  $is_a_back_dating_records,
                                );
                            if($this->ci->loan_invoices_m->insert_batch($input)){
                            }  
                            $transfered_invoices = $this->ci->loans_m->get_transfer_out_invoice($loan->id,$loan->group_id);
                            if($transfered_invoices){
                                foreach ($transfered_invoices as $transfered_invoice) {
                                    $statement = $this->ci->statements_m->get_group_contribution_transfer_by_loan_invoice($transfered_invoice->loan_invoice_id,$transfered_invoice->group_id);
                                    $new_post = array(
                                        'invoice_no'    => time(),
                                        'member_id'     => $transfered_invoice->member_id,
                                        'group_id'      => $transfered_invoice->group_id,
                                        'loan_id'       =>  $transfered_invoice->loan_id,
                                        'type'          =>  5,
                                        'is_sent'       =>  1,
                                        'interest_amount_payable'   => 0,
                                        'principle_amount_payable'  => $transfered_invoice->amount,
                                        'invoice_date'  =>  $transfered_invoice->transaction_date,
                                        'due_date'      =>  $transfered_invoice->transaction_date,
                                        'fine_date'     =>  '',
                                        'amount_payable'=>  $transfered_invoice->amount,
                                        'amount_paid'   =>  0,
                                        'active'        =>  1,
                                        'created_on'    =>  time(),
                                        'transfer_to'   =>  $transfered_invoice->transfer_to,
                                        'contribution_transfer_id'=> ($statement)?$statement->contribution_transfer_id:'',
                                    );
                                    if($invoice_id = $this->ci->loan_invoices_m->insert($new_post)){
                                        if($statement){
                                            $this->ci->statements_m->update($statement->id,array('loan_transfer_invoice_id'=>$invoice_id));
                                        }
                                        $this->ci->loans_m->update_statement($transfered_invoice->id,array('loan_invoice_id'=>$invoice_id));
                                    }
                                }
                            }                                      
                            $past_invoice_id = $this->send_past_invoices($loan->id,'',$loan->group_id);
                            if($past_invoice_id){
                                $outstanding_balance_fine_date = $this->set_outstanding_balance_fine_date($loan->id,'',$loan->group_id);
                                if($outstanding_balance_fine_date){
                                    $fix_loan_invoices_fine = $this->fix_loan_invoices_fine($loan->id);
                                    if($fix_loan_invoices_fine){
                                        $outstanding_balance_fixer = $this->outstanding_loan_balance_fine_fixer($loan);
                                        if($outstanding_balance_fixer){
                                            return TRUE;
                                        }
                                        else{
                                            return FALSE;
                                        }
                                    }
                                }else{
                                    $this->ci->session->set_flashdata('error','Unable to set the outstanding balance fine date');
                                    return FALSE;
                                }
                            }
                            else{
                                $this->ci->session->set_flashdata('error','Unable to send past invoices');
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }
                    else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }
        }
    }

    function calculate_loan_balance_invoice_for_custom($loan_amount,$custom_interest_procedure,$repayment_period,$disbursement_date='',$custom_loan_values=array(),$is_a_back_dating_record = 0)
    {      
        $input = array($loan_amount,$custom_interest_procedure,$repayment_period,$disbursement_date='',$custom_loan_values);
        if($disbursement_date){
        }else{
            $disbursement_date=time();
        }

        if($custom_interest_procedure==1){
            $loan_per_month = $loan_amount/$repayment_period;
            $loan_invoices = array();
            for ($i = 0; $i < $repayment_period; $i++)
            {
                $count = $i + 1;
                $due_date = $this->_date_add(date('d M Y',$disbursement_date), $count);

                $rate = $this->_calculate_custom_rate($disbursement_date,$due_date,$custom_loan_values);
              
                $monthly_pay = $loan_per_month+(($loan_per_month * $rate)/100);

                $interest_amount = $monthly_pay - $loan_per_month;

                $invoice_date = $due_date - (86400 * 7);
                $loan_invoices[] = array
                    (
                        'fine_date' => $due_date + (24 * 60 * 60),
                        'interest_amount_payable' => (float) str_replace(',', '', number_format($interest_amount, 2)),
                        'principle_amount_payable' => (float) str_replace(',', '', number_format($loan_per_month, 2)),
                        'invoice_date' => $invoice_date,
                        'due_date' => $due_date,
                        'amount_payable' => (float) str_replace(',', '', number_format($monthly_pay, 2)),
                    );
            }

            return $loan_invoices;
        }
        else if($custom_interest_procedure==2){
                $count = count($custom_loan_values['payment_date']);
                $monthly_pay = $loan_amount/$count;
            asort($custom_loan_values['payment_date']);
            foreach ($custom_loan_values['payment_date'] as $key => $value) {
                $loan_invoices[] = array(
                        'fine_date' => strtotime($value) + (24 * 60 * 60),
                        'interest_amount_payable' => (float)currency($custom_loan_values['amount_payable'][$key]) - $monthly_pay,
                        'principle_amount_payable' => (float) str_replace(',', '', number_format($monthly_pay, 2)),
                        'invoice_date' => strtotime($value) - (86400*7),
                        'due_date' => strtotime($value),
                        'amount_payable' => (float)currency($custom_loan_values['amount_payable'][$key]),
                        'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                );
            }

            return $loan_invoices;
        }
    }

    function create_loan_transfer_invoice($member_id=0,$loan_id,$transfer_to=0,$amount=0,$invoice_date=0,$group_id=0,$contribution_transfer_id,$update_loan_invoices=1){
        if($member_id&&$transfer_to&&$amount&&$invoice_date&&$group_id&&$contribution_transfer_id){
            $invoice_id =$this->ci->loan_invoices_m->insert(array(
                'invoice_no'    => $this->ci->loan_invoices_m->calculate_invoice_no($group_id),
                'member_id'     => $member_id,
                'group_id'      => $group_id,
                'loan_id'       =>  $loan_id,
                'type'          =>  5,
                'is_sent'       =>  1,
                'interest_amount_payable'   => 0,
                'principle_amount_payable'  => $amount,
                'invoice_date'  =>  $invoice_date,
                'due_date'      =>  $invoice_date,
                'fine_date'     =>  '',
                'amount_payable'=>  $amount,
                'amount_paid'   =>  0,
                'active'        =>  1,
                'created_on'    =>  time(),
                'transfer_to'   =>  $transfer_to,
                'contribution_transfer_id'=>$contribution_transfer_id,
            ));
            if($invoice_id){
                $this->ci->loans_m->insert_loan_statement(array(
                    'member_id' =>  $member_id,
                    'group_id'  =>  $group_id,
                    'transaction_date'  =>  $invoice_date,
                    'loan_id'   =>  $loan_id,
                    'transaction_type'  =>  5,
                    'loan_invoice_id'   =>  $invoice_id,
                    'amount'    =>  $amount,
                    'balance'   =>  0,
                    'active'    =>  1,
                    'created_on'    =>  time(),
                    'transfer_to'   =>  $transfer_to,
                ));
                if($update_loan_invoices == 1){
                    $this->update_loan_invoices($loan_id);
                }
                return $invoice_id;
            }
        }else{
            $this->ci->session->set_flashdata('error','Missing parameters');
            return FALSE;
        }
    }

    function create_incoming_transfer_payment($loan_id,$amount=0,$member_id=0,$transfer_date=0,$group_id=0,$loan_transfer_invoice_id='',$transfer_from='',$contribution_transfer_entry_id=0,$update_loan_invoices = 1){
        if($amount&&$transfer_date&&$member_id&&$loan_id&&$group_id&&$transfer_from){
            if($transfer_from=='loan'){
                $input = array(
                        'loan_id'   =>  $loan_id,
                        'group_id'  =>  $group_id,
                        'member_id' =>  $member_id,
                        'receipt_date'=>$transfer_date,
                        'amount'    =>  $amount,
                        'status'    =>  1,
                        'active'    =>  1,
                        'created_on'=>  time(),
                        'transfer_from' => $transfer_from,
                        'incoming_loan_transfer_invoice_id' => $loan_transfer_invoice_id,
                        'incoming_contribution_transfer_id' => $contribution_transfer_entry_id,
                    );
                $repayment_id =$this->ci->loan_repayments_m->insert($input);
            }else{
                $input = array(
                        'loan_id'   =>  $loan_id,
                        'group_id'  =>  $group_id,
                        'member_id' =>  $member_id,
                        'receipt_date'=>$transfer_date,
                        'amount'    =>  $amount,
                        'status'    =>  1,
                        'active'    =>  1,
                        'created_on'=>  time(),
                        'transfer_from' => $transfer_from,
                        'incoming_loan_transfer_invoice_id' => '',
                        'incoming_contribution_transfer_id' => $contribution_transfer_entry_id,
                    );
                $repayment_id = $this->ci->loan_repayments_m->insert($input);
            }
            if($repayment_id){
                $statement_entry_id =$this->ci->loans_m->insert_loan_statement(array(
                        'member_id' =>  $member_id,
                        'group_id'  =>  $group_id,
                        'transaction_date' =>   $transfer_date,
                        'transaction_type'  =>  4,
                        'transfer_from' => $transfer_from,
                        'loan_id'   =>  $loan_id,
                        'loan_payment_id'   =>  $repayment_id,
                        'amount'        =>  $amount,
                        'balance'       =>  0,
                        'active'        =>  1,
                        'status'        =>  1,
                        'created_on'    =>  time(),
                    ));
                if($statement_entry_id){
                    if($update_loan_invoices == 1){
                        $this->update_loan_invoices($loan_id);
                    }
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }

    }

    function void_transfered_from_loan($loan_id=0,$transfer_id=0){
        $update = array(
                'active' => NULL,
                'status' => NULL,
                'modified_on'=>time()
            );
        if($loan_id){
            $loan_invoices = $this->ci->loan_invoices_m->get_transfered_invoice($loan_id,$transfer_id,TRUE);
            if($loan_invoices){
                $incoices_id = array();
                foreach ($loan_invoices as $loan_invoice) {
                    $invoice_id = $this->ci->loan_invoices_m->update($loan_invoice->id,$update);
                    $this->ci->loans_m->update_statement_invoice_void($loan_invoice->id);
                    $invoices_id[] = $loan_invoice->id;
                }
                $this->update_loan_invoices($loan_id);
                return $invoices_id;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    function void_transfered_to_loan($loan_id=0,$incoming_loan_transfers=array(),$contribution_entries_ids=array()){
        $update = array(
                'active' => '',
                'modified_on'=>time()
            );
        if($loan_id){
            $incoming_transfer='';
            if($incoming_loan_transfers){
                foreach ($incoming_loan_transfers as $key => $value) {
                    if($incoming_transfer){
                        $incoming_transfer=$incoming_transfer.','.$value;
                    }else{
                        $incoming_transfer=$value;
                    }
                }
            }
            $statement_entry='';
            if($contribution_entries_ids){
                foreach ($contribution_entries_ids as $contribution_entries_id) {
                    if($statement_entry){
                        $statement_entry=$statement_entry.','.$contribution_entries_id;
                    }else{
                        $statement_entry=$contribution_entries_id;
                    }
                }
            }
            $result = $this->ci->loan_repayments_m->get_loan_repayments($loan_id,$incoming_transfer,$statement_entry);
            if($result){
                foreach ($result as $res) {
                    $void_id = $this->ci->loan_repayments_m->update($res->id,$update);
                    if($void_id){
                        $statement_id =$this->ci->loans_m->update_statement_payment($res->id);
                    }else{
                        return FALSE;
                    }
                }
                $this->update_loan_invoices($loan_id);
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    function _date_add($date_str, $months,$decrement_count = FALSE)
    {
        if($decrement_count){
            $months -= 1;
        }
        $date = new DateTime($date_str);
        $start_day = $date->format('j');

        $date->modify("+{$months} month");
        $end_day = $date->format('j');

        if ($start_day != $end_day)
        {
            $date->modify('last day of last month');
        }
        return $date->getTimestamp();
    }

    function _calculate_custom_rate($disbursement_date,$date,$custom_loan_values=array()){
        foreach ($custom_loan_values['date_from'] as $key => $value) {
            $dif = $disbursement_date - $date;
            $days = intval(abs($dif/86400));
            if($days>$value && $days<=$custom_loan_values['date_to'][$key]){
                return $custom_loan_values['rate'][$key];
            }
        }
        
    }

    // function send_past_invoices($id=0,$is_a_debtor= FALSE){
    //     if($id){
    //         if($is_a_debtor){
    //             $past_unsent_invoices = $this->ci->debtors_m->get_past_unsent_invoices($id);
    //             foreach ($past_unsent_invoices as $invoice) {
    //                 $this->ci->debtors_m->insert_loan_statement(array(
    //                     'debtor_id' =>  $invoice->debtor_id,
    //                     'group_id'  =>  $invoice->group_id,
    //                     'transaction_date'  =>  $invoice->invoice_date,
    //                     'debtor_loan_id'   =>  $invoice->debtor_loan_id,
    //                     'transaction_type'  =>  1,
    //                     'debtor_loan_invoice_id'   =>  $invoice->id,
    //                     'amount'    =>  $invoice->amount_payable,
    //                     'balance'   =>  0,
    //                     'active'    =>  1,
    //                     'created_by' => $invoice->created_by,
    //                     'created_on'    =>  time(),
    //                 ));
    //                 $this->ci->debtors_m->update_loan_invoices($invoice->id, array('is_sent' => 1,'modified_on'=>time()));
    //             }
    //             return TRUE;
    //         }else{
    //            $past_unsent_invoices = $this->ci->loan_invoices_m->get_past_unsent_invoices($id);
    //             foreach ($past_unsent_invoices as $key => $invoice){
    //                 $this->ci->loans_m->insert_loan_statement(array(
    //                     'member_id' =>  $invoice->member_id,
    //                     'group_id'  =>  $invoice->group_id,
    //                     'transaction_date'  =>  $invoice->invoice_date,
    //                     'loan_id'   =>  $invoice->loan_id,
    //                     'transaction_type'  =>  1,
    //                     'loan_invoice_id'   =>  $invoice->id,
    //                     'amount'    =>  $invoice->amount_payable,
    //                     'balance'   =>  0,
    //                     'active'    =>  1,
    //                     'created_by' => $invoice->created_by,
    //                     'created_on'    =>  time(),
    //                 ));

    //                 $this->ci->loan_invoices_m->update($invoice->id, array('is_sent' => 1,'modified_on'=>time()));
    //             }
    //             return TRUE; 
    //         } 
    //     }
    //     else
    //     {
    //         return FALSE;
    //     }
    // }

    function send_past_invoices($id=0,$is_a_debtor= FALSE,$group_id=0){
        if($id){
            if($is_a_debtor){
                $past_unsent_invoices = $this->ci->debtors_m->get_past_unsent_invoices($id,$group_id);
                foreach ($past_unsent_invoices as $invoice) {
                    $this->ci->debtors_m->insert_loan_statement(array(
                        'debtor_id' =>  $invoice->debtor_id,
                        'group_id'  =>  $invoice->group_id,
                        'transaction_date'  =>  $invoice->invoice_date,
                        'debtor_loan_id'   =>  $invoice->debtor_loan_id,
                        'transaction_type'  =>  1,
                        'debtor_loan_invoice_id'   =>  $invoice->id,
                        'amount'    =>  $invoice->amount_payable,
                        'balance'   =>  0,
                        'active'    =>  1,
                        'created_by' => $invoice->created_by,
                        'created_on'    =>  time(),
                    ));
                    $this->ci->debtors_m->update_loan_invoices($invoice->id, array('is_sent' => 1,'modified_on'=>time()));
                }
                return TRUE;
            }else{
               $past_unsent_invoices = $this->ci->loan_invoices_m->get_past_unsent_invoices($id,$group_id);
                foreach ($past_unsent_invoices as $key => $invoice){
                    $this->ci->loans_m->insert_loan_statement(array(
                        'member_id' =>  $invoice->member_id,
                        'group_id'  =>  $invoice->group_id,
                        'transaction_date'  =>  $invoice->invoice_date,
                        'loan_id'   =>  $invoice->loan_id,
                        'transaction_type'  =>  1,
                        'loan_invoice_id'   =>  $invoice->id,
                        'amount'    =>  $invoice->amount_payable,
                        'balance'   =>  0,
                        'active'    =>  1,
                        'created_by' => $invoice->created_by,
                        'created_on'    =>  time(),
                    ));

                    $this->ci->loan_invoices_m->update($invoice->id, array('is_sent' => 1,'modified_on'=>time()));
                }
                return TRUE; 
            } 
        }
        else
        {
            return FALSE;
        }
    }

    // function set_outstanding_balance_fine_date($id = 0,$is_a_debtor=FALSE){
    //     if($id){
    //         if($is_a_debtor){
    //             $invoice = $this->ci->debtors_m->get_last_invoice($id);
    //             if($invoice){
    //                 $loan_end_date = $invoice->invoice_date + (7*24*60*60);
    //                 $update = $this->ci->debtors_m->update_loan($id,
    //                     array(
    //                         'loan_end_date'=>$loan_end_date,
    //                         'outstanding_loan_balance_fine_date'=>$loan_end_date,
    //                         'modified_on'=>time()
    //                         ));
    //                 if($update){
    //                     return TRUE;
    //                 }
    //                 else{
    //                     return FALSE;
    //                 }
    //             }else{
    //                 return TRUE;
    //             }
    //         }else{
    //             $invoice = $this->ci->loan_invoices_m->get_last_invoice($id);
    //             $loan_end_date = $invoice->invoice_date + (7*24*60*60);
    //             $update = $this->ci->loans_m->update($id,
    //                 array(
    //                     'loan_end_date'=>$loan_end_date,
    //                     'outstanding_loan_balance_fine_date'=>$loan_end_date,
    //                     'modified_on'=>time()
    //                     ));
    //             if($update){
    //                 return TRUE;
    //             }
    //             else{
    //                 return FALSE;
    //             }
    //         }
    //     }else{
    //         return FALSE;
    //     }
       
    // }

    function set_outstanding_balance_fine_date($id = 0,$is_a_debtor=FALSE,$group_id=0){
        if($id){
            if($is_a_debtor){
                $invoice = $this->ci->debtors_m->get_last_invoice($id,$group_id);
                if($invoice){
                    $loan_end_date = $invoice->invoice_date + (7*24*60*60);
                    $update = $this->ci->debtors_m->update_loan($id,
                        array(
                            'loan_end_date'=>$loan_end_date,
                            'outstanding_loan_balance_fine_date'=>$loan_end_date,
                            'modified_on'=>time()
                            ));
                    if($update){
                        return TRUE;
                    }
                    else{
                        return FALSE;
                    }
                }else{
                    return TRUE;
                }
            }else{
                $invoice = $this->ci->loan_invoices_m->get_last_invoice($id,$group_id);
                $loan_end_date = $invoice->invoice_date + (7*24*60*60);
                $update = $this->ci->loans_m->update($id,
                    array(
                        'loan_end_date'=>$loan_end_date,
                        'outstanding_loan_balance_fine_date'=>$loan_end_date,
                        'modified_on'=>time()
                        ));
                if($update){
                    return TRUE;
                }
                else{
                    return FALSE;
                }
            }
        }else{
            return FALSE;
        }
       
    }

    function _get_fines_less_than_due_date($fines_in_list=array(),$current_date=0,$updated_fine_list=array()){
        $fines = array();
        if($current_date&&$fines_in_list){
            if($updated_fine_list){
                foreach ($fines_in_list as$key=>$fine_list) {
                    if(array_key_exists($fine_list->id, $updated_fine_list)){
                        $next_fine_date = $updated_fine_list[$fine_list->id];
                        $new_array = array_merge((array)$fine_list,array('fine_date'=>$next_fine_date));
                        $fines_in_list[$key] = (object)$new_array;
                    }
                }
            }
            foreach ($fines_in_list as $fine_in_list) {
                if(($fine_in_list->due_date < $current_date) && (date('Ymd',$fine_in_list->fine_date) == date('Ymd',$current_date))){
                    $fines[] = $fine_in_list;
                }
            }
        }
        return $fines;
    }

    function _get_charged_fines($loan_id=0){
        $fines = array();
        if($loan_id){
            $fine_lists = $this->ci->loan_invoices_m->get_all_fines($loan_id);
            if($fine_lists){
                foreach ($fine_lists as $fine_list) {
                    $fines_charged[date('Ymd',$fine_list->invoice_date).'-'.$fine_list->fine_parent_loan_invoice_id]  = 1;
                }
            }
        }
        return $fines;
    }


    // function fix_loan_invoices_fine($loan_id=0,$is_a_debtor = FALSE,$return_queue=FALSE,$date=0){
    //     if($loan_id){
    //         if($is_a_debtor){
    //             $loan = $this->ci->debtors_m->get_loan($loan_id);

    //             if($loan->enable_loan_fines){
    //                 $grace_period = $loan->grace_period; 
    //                 $disbursement_date = $loan->disbursement_date;
    //                 if(empty($grace_period)){
    //                     $grace_period=1;
    //                 }
    //                 if($disbursement_date){
    //                 }else{
    //                     $disbursement_date=time();
    //                 }
    //                     $current_date  = strtotime('+ '.$grace_period.' months',$disbursement_date?:time());
    //                 $days = (strtotime(date('d-M-Y',time())) - strtotime(date('d-M-Y',$current_date)))/(24*60*60)+1;
                    
    //                 $fines = array();
    //                 for($i=1;$i<=$days;$i++){
    //                     $fine_list= $this->ci->debtors_m->fine_invoice_list($loan->id,$current_date);
    //                     if($fine_list){
    //                         foreach ($fine_list as $fine){
    //                             if ((date('Ymd',$current_date) == date('Ymd', $fine->fine_date))){
    //                                 if(!$this->ci->debtors_m->fine_invoice_exists_fixer($fine->id,$current_date,$fine->debtor_loan_id)){
    //                                     $amount = $this->get_fine_payable($loan->debtor_id, $loan, $loan->loan_amount, $fine->id, 0,$current_date,TRUE);
    //                                     $amount = (float) str_replace(',', '', number_format($amount, 2));
    //                                     $invoice_number = $this->ci->loan_invoices_m->calculate_invoice_no($fine->group_id);
    //                                     if($amount>0){   
    //                                         //insert int db
    //                                         $fine_details = array(
    //                                             'fine_parent_loan_invoice_id' => $fine->id,
    //                                             'invoice_no' => $this->ci->debtors_m->calculate_invoice_no($fine->group_id),
    //                                             'debtor_id' => $fine->debtor_id,
    //                                             'debtor_loan_id' => $loan_id,
    //                                             'type' => 2,
    //                                             'interest_amount_payable' => 0,
    //                                             'principle_amount_payable' => 0,
    //                                             'invoice_date' => $current_date,
    //                                             'due_date' => $current_date,
    //                                             'fine_date' => 0,
    //                                             'amount_payable' => $amount,
    //                                             'amount_paid' => 0,
    //                                             'status' => 1,
    //                                             'active' => 1,
    //                                             'is_sent' => 1,
    //                                             'group_id' => $fine->group_id,
    //                                             'created_on' => time(),
    //                                             'created_by' => 1,
    //                                         );
    //                                         $fine_id = $this->ci->debtors_m->insert_loan_invoice($fine_details);
    //                                         if ($fine_id){
    //                                             $statement = array(
    //                                                 'debtor_loan_id' => $loan_id,
    //                                                 'debtor_id' => $fine->debtor_id,
    //                                                 'transaction_type' => 2,
    //                                                 'debtor_loan_invoice_id' => $fine_id,
    //                                                 'transaction_date' => $current_date,
    //                                                 'amount' => $amount,
    //                                                 'active' => 1,
    //                                                 'balance' => 0,
    //                                                 'group_id' => $fine->group_id,
    //                                                 'created_on' => time(),
    //                                                 'created_by' => 1,
    //                                             );
    //                                             $statement_id = $this->ci->debtors_m->insert_loan_statement($statement);
    //                                             if($return_queue){
    //                                                 if(date('Ymd',$fine->fine_date) == date('Ymd',$date)){
    //                                                     $loan_balance =$this->ci->debtors_m->get_loan_balance($fine->debtor_loan_id);
    //                                                     $lump_sum_remaining = $this->ci->debtors_m->get_loan_lump_sum_as_date($fine->debtor_loan_id);
    //                                                     $fines[] = array(
    //                                                         'loan_id'   =>  $fine->debtor_loan_id,
    //                                                         'invoice_id'    =>  $fine->id,
    //                                                         'invoice_no'    =>  $invoice_number,
    //                                                         'debtor_id'     =>  $fine->debtor_id,
    //                                                         'due_date'  =>  $current_date,
    //                                                         'invoice_date'  =>  $current_date,
    //                                                         'invoice_type'  => 2,
    //                                                         'amount_payable'    =>  $amount,
    //                                                         'lump_sum_remaining'    =>  $lump_sum_remaining,
    //                                                         'loan_balance'  =>  $loan_balance,
    //                                                         'group_id'  =>  $fine->group_id,
    //                                                         'description'  =>   'Late Loan Payment Fine Invoice',
    //                                                         'created_on'    =>  time(),
    //                                                     );
    //                                                 }
    //                                             }
    //                                         }
    //                                     }     
    //                                 }
    //                                 else{
    //                                 }
    //                             }
    //                             else{
    //                             }
    //                         }
    //                         $uptodate_fine_list = $this->ci->debtors_m->get_to_update_fixer($loan->id,$current_date);
    //                         foreach($uptodate_fine_list as $fine){
    //                             $next_fine_date = $this->_next_fine_date_fixer($loan,$current_date,TRUE);
    //                             $this->ci->debtors_m->update_loan_invoices($fine->id,array('fine_date'=>$next_fine_date,'modified_on'=>time()));
    //                         }
    //                     }
    //                     else
    //                     {
    //                         //continue;
    //                     }
    //                     $current_date+=(24*60*60);
    //                 }
    //                 if($return_queue && $date){
    //                     return $fines;
    //                 }else{
    //                     return TRUE;
    //                 }
    //             }else{
    //                 return TRUE;
    //             }
    //         }else{
    //             $loan = $this->ci->loans_m->get($loan_id);
    //             if($loan->enable_loan_fines){
    //                 $grace_period = $loan->grace_period; 
    //                 $disbursement_date = $loan->disbursement_date;
    //                 $grace_period_date = $loan->grace_period_date;
    //                 if(empty($grace_period)){
    //                     $grace_period=1;
    //                 }
    //                 if($grace_period=="date"){
    //                     $current_date = $grace_period_date;
    //                 }else{
    //                     if($disbursement_date){
    //                     }else{
    //                         $disbursement_date=time();
    //                     }
    //                     $current_date  = strtotime('+ '.$grace_period.' months',$disbursement_date?:time());
    //                 }
    //                 $days = (strtotime(date('d-M-Y',time())) - strtotime(date('d-M-Y',$current_date)))/(24*60*60)+1;
                    
    //                 $fines = array();
    //                 $fines_in_list = $this->ci->loan_invoices_m->get_all_fines_for_list($loan->id);
    //                 $updated_fine_list = array();

    //                 $fines_charged = $this->_get_charged_fines($loan->id);
    //                 for($i=1;$i<=$days;$i++){
    //                     $fine_list= $this->_get_fines_less_than_due_date($fines_in_list,$current_date,$updated_fine_list);//$this->ci->loan_invoices_m->fine_invoice_list($loan->id,$current_date);
    //                     if($fine_list){
    //                         foreach ($fine_list as $fine){
    //                             if ((date('Ymd',$current_date) == date('Ymd', $fine->fine_date))){
    //                                 //if(!$this->ci->loan_invoices_m->fine_invoice_exists_fixer($fine->id,$current_date,$fine->loan_id)){
    //                                 if(array_key_exists((date('Ymd',$current_date).'-'.$fine->id),$fines_charged) ==FALSE){
    //                                     $amount = $this->get_fine_payable($loan->member_id, $loan, $loan->loan_amount, $fine->id, 0,$current_date,'',$fine->interest_amount_payable);
    //                                     $amount = (float) str_replace(',', '', number_format($amount, 2));
    //                                     if($amount>0){   
    //                                         $invoice_number = 'Invoice-'.time();
    //                                         $fine_details = array(
    //                                             'fine_parent_loan_invoice_id' => $fine->id,
    //                                             'invoice_no' => $invoice_number,
    //                                             'member_id' => $fine->member_id,
    //                                             'loan_id' => $fine->loan_id,
    //                                             'type' => 2,
    //                                             'interest_amount_payable' => 0,
    //                                             'principle_amount_payable' => 0,
    //                                             'invoice_date' => $current_date,
    //                                             'due_date' => $current_date,
    //                                             'fine_date' => 0,
    //                                             'amount_payable' => $amount,
    //                                             'amount_paid' => 0,
    //                                             'status' => 1,
    //                                             'active' => 1,
    //                                             'is_sent' => 1,
    //                                             'group_id' => $fine->group_id,
    //                                             'created_on' => time(),
    //                                             'modified_on' => time(),
    //                                             'created_by' => 1,
    //                                         );
    //                                         $fine_id =  $this->ci->loan_invoices_m->insert($fine_details);
    //                                         if ($fine_id){
    //                                             $fines_charged[date('Ymd',$current_date).'-'.$fine->id] = 1;
    //                                             $statement = array(
    //                                                 'loan_id' => $fine->loan_id,
    //                                                 'member_id' => $fine->member_id,
    //                                                 'transaction_type' => 2,
    //                                                 'loan_invoice_id' => $fine_id,
    //                                                 'transaction_date' => $current_date,
    //                                                 'amount' => $amount,
    //                                                 'active' => 1,
    //                                                 'balance' => 0,
    //                                                 'group_id' => $fine->group_id,
    //                                                 'created_on' => time(),
    //                                                 'modified_on' => time(),
    //                                                 'created_by' => 1,
    //                                             );
    //                                             $statement_id = $this->ci->loans_m->insert_loan_statement($statement);
    //                                             if($return_queue){
    //                                                 if(date('Ymd',$fine->fine_date) == date('Ymd',$date)){
    //                                                     $loan_balance =$this->ci->loans_m->get_loan_balance($fine->loan_id);
    //                                                     $lump_sum_remaining = $this->ci->loan_invoices_m->get_loan_lump_sum_as_date($fine->loan_id);
    //                                                     $fines[] = array(
    //                                                         'loan_id'   =>  $fine->loan_id,
    //                                                         'invoice_id'    =>  $fine->id,
    //                                                         'invoice_no'    =>  $invoice_number,
    //                                                         'member_id'     =>  $fine->member_id,
    //                                                         'due_date'  =>  $current_date,
    //                                                         'invoice_date'  =>  $current_date,
    //                                                         'invoice_type'  => 2,
    //                                                         'amount_payable'    =>  $amount,
    //                                                         'lump_sum_remaining'    =>  $lump_sum_remaining,
    //                                                         'loan_balance'  =>  $loan_balance,
    //                                                         'group_id'  =>  $fine->group_id,
    //                                                         'description'  =>   'Late Loan Payment Fine Invoice',
    //                                                         'created_on'    =>  time(),
    //                                                     );
    //                                                 }
    //                                             }
    //                                         }
    //                                     }     
    //                                 }
    //                                 else{
    //                                 }
    //                             }
    //                             else{
    //                             }
    //                         }
    //                         // $uptodate_fine_list = $this->ci->loan_invoices_m->get_to_update_fixer($loan->id,$current_date);
    //                         // foreach($uptodate_fine_list as $fine){
    //                         //     $next_fine_date = $this->_next_fine_date_fixer($loan,$current_date);
    //                         //     $this->ci->loan_invoices_m->update($fine->id,array('fine_date'=>$next_fine_date,'modified_on'=>time()));
    //                         //     $updated_fine_list[$fine->id] = $next_fine_date;
    //                         // }
    //                         foreach($fines_in_list as $fine){
    //                             if($fine->due_date < $current_date){
    //                                 $next_fine_date = $this->_next_fine_date_fixer($loan,$current_date);
    //                                 $updated_fine_list[$fine->id] = $next_fine_date;
    //                             }
    //                         }
    //                     }else{
    //                         //continue;
    //                     }

    //                     $current_date+=(24*60*60);
    //                 }
    //                 foreach ($updated_fine_list as $fine_id=>$fine_date){
    //                     $this->ci->loan_invoices_m->update($fine_id,array('fine_date'=>$fine_date,'modified_on'=>time()));
    //                 }
    //                 if($return_queue && $date){
    //                     return $fines;
    //                 }else{
    //                     return TRUE;
    //                 }
    //             }else{
    //                 return TRUE;
    //             }
    //         }
    //     }else{
    //         return FALSE;
    //     }
        
    // }

    // public function get_fine_payable($member_id=0, $loan=array(), $loan_amount=0, $invoice_id = 0, $type = 0,$date=0,$is_a_debtor=FALSE,$installment_amount=0){
    //     //percentage one off fine type
    //     /**
    //         1. Total loan installment balance
    //         2. Total loan amount
    //         3. Total unpaid loan amout 
    //     **/
    //     if($is_a_debtor){
    //         //debtor
    //         $debtor_id = $member_id;
    //         $amount_payable = 0;
    //         $fine = $loan;//$this->ci->debtors_m->get_loan($loan_id);
    //         if($fine->loan_fine_type==1){
    //             //fixed fine type
    //             if($fine->fixed_amount_fine_frequency_on==2){
    //                 if($this->ci->debtors_m->get_invoice_sent_today($loan->id,$date)){
    //                     $amount_payable=0;
    //                 }else{
    //                     $amount_payable = $fine->fixed_fine_amount;
    //                 }
    //             }else{
    //                 $amount_payable = $fine->fixed_fine_amount;       
    //             }
    //         }else if($fine->loan_fine_type==2){
    //             //percentage fine
    //             $rate = $fine->percentage_fine_rate;
    //             $rate_on = $fine->percentage_fine_on;
    //             if($rate_on == 1){
    //                 $installment_balance = $this->ci->debtors_m->get_total_unpaid_loan_installments($invoice_id);
    //                 $amount_payable = ($rate*$installment_balance)/100; 
    //             }
    //             else if($rate_on==2){
    //                  $amount_payable = ($rate*$loan_amount)/100;
    //             }
    //             else if($rate_on==3){
    //                 $total_unpaid_loan = $this->ci->debtors_m->get_outstanding_balance($debtor_id, $loan->id);
    //                 $amount_payable = ($rate*$total_unpaid_loan)/100; 
    //             }
    //         }else if($fine->loan_fine_type==3){
    //             //one off fine
    //             if($fine->one_off_fine_type==1){
    //                 //fixed one off fine type
    //                 $amount_payable = $one_off_fixed_amount = $fine->one_off_fixed_amount;
    //             }else if($fine->one_off_fine_type==2){
    //                 $rate = $fine->one_off_percentage_rate;
    //                 $rate_on = $fine->one_off_percentage_rate_on;
    //                 if($rate_on == 1){
    //                     $installment_balance = $this->ci->debtors_m->get_total_unpaid_loan_installments($invoice_id);
    //                     $amount_payable = ($rate*$installment_balance)/100;
    //                 }else if($rate_on==2){
    //                     $amount_payable = ($rate*$loan_amount)/100;
    //                 }else if($rate_on==3){
    //                     $total_unpaid_loan = $this->ci->debtors_m->get_outstanding_balance($debtor_id, $loan->id);
    //                     $amount_payable = ($rate*$total_unpaid_loan)/100;   
    //                 }
    //             }
    //         }
    //         return $amount_payable;
    //     }else{
    //         //member
    //         $amount_payable = 0;
    //         $fine = $loan;
    //         if($fine->loan_fine_type==1){
    //             //fixed fine type
    //             if($fine->fixed_amount_fine_frequency_on==2){
    //                 if($this->ci->loan_invoices_m->get_invoice_sent_today($loan->id,$date)){
    //                     $amount_payable=0;
    //                 }else{
    //                     $amount_payable = $fine->fixed_fine_amount;
    //                 }
    //             }else{
    //                 $amount_payable = $fine->fixed_fine_amount;       
    //             }
                
    //         }
    //         else if($fine->loan_fine_type==2){
    //             //percentage fine
    //             $rate = $fine->percentage_fine_rate;
    //             $rate_on = $fine->percentage_fine_on;
    //             if($rate_on == 1)
    //             {
    //                 $installment_balance = $this->ci->loan_invoices_m->get_total_unpaid_loan_installments($invoice_id,$date);
    //                 $amount_payable = ($rate*$installment_balance)/100; 
    //             }
    //             else if($rate_on==2)
    //             {
    //                  $amount_payable = ($rate*$loan_amount)/100;
    //             }
    //             else if($rate_on==3)
    //             {
    //                 $total_unpaid_loan = $this->ci->loan_invoices_m->get_outstanding_balance($member_id, $loan->id,$date);
    //                 $amount_payable = ($rate*$total_unpaid_loan)/100; 
    //             }elseif($rate_on == 4){
    //                 $amount_payable = ($rate*$installment_amount)/100;
    //             }
    //         }
    //         else if($fine->loan_fine_type==3){
    //             //one off fine
    //             if($fine->one_off_fine_type==1){
    //                 //fixed one off fine type
    //                 $amount_payable = $one_off_fixed_amount = $fine->one_off_fixed_amount;
    //             }
    //             else if($fine->one_off_fine_type==2){
    //                 $rate = $fine->one_off_percentage_rate;
    //                 $rate_on = $fine->one_off_percentage_rate_on;
    //                 if($rate_on == 1)
    //                 {
    //                     $installment_balance = $this->ci->loan_invoices_m->get_total_unpaid_loan_installments($invoice_id,$date);
    //                     $amount_payable = ($rate*$installment_balance)/100;
    //                 }
    //                 else if($rate_on==2)
    //                 {
    //                     $amount_payable = ($rate*$loan_amount)/100;
    //                 }else if($rate_on==3)
    //                 {
    //                     $total_unpaid_loan = $this->ci->loan_invoices_m->get_outstanding_balance($member_id, $loan->id,$date);
    //                     $amount_payable = ($rate*$total_unpaid_loan)/100;   
    //                 }elseif($rate_on == 4){
    //                     $amount_payable = ($rate*$installment_amount)/100;
    //                 }
    //             }
    //         }
    //         return $amount_payable;
    //     }
    // }

    // private function _next_fine_date_fixer($loan,$date,$is_a_debtor = FALSE){
    //     $next_date = 0;
    //     // if($is_a_debtor){
    //     //     $loan = $this->ci->debtors_m->get_loan($loan_id);
    //     // }else{
    //     //     $loan = $this->ci->loan_invoices_m->fetch_loan($loan_id);
    //     // }

    //     if ($loan->loan_fine_type ==1){
    //         //fixed amount && fixed time_ref
    //         $value = $loan->fixed_amount_fine_frequency;
    //     }
    //     elseif ($loan->loan_fine_type == 2){
    //         $value = $loan->percentage_fine_frequency;
    //     }
    //     elseif ($loan->loan_fine_type == 3){
    //         return $next_date;
    //     }
    //     else{
    //         return 0;
    //     }

    //     $weekdays = array('Day',
    //         'Sunday',
    //         'Monday',
    //         'Tuesday',
    //         'Wednesday',
    //         'Thursday',
    //         'Friday',
    //         'Saturday',
    //     );
    //     switch ($value)
    //     {
    //         case 1: //Day
    //             $next_date = strtotime('next day',$date);
    //             break;

    //         case 2: //Week
    //             $dt = "Next " . $weekdays[date('w') + 1];
    //             $next_date = strtotime($dt,$date);
    //             //$next_date = strtotime('next week',$date);
    //             break;

    //         case 3: //Month
    //             $next_date = strtotime('next month',$date);
    //             break;

    //         case 4://Year
    //             $next_date = strtotime('next year',$date);
    //             break;

    //         default:
    //             $next_date = 0;
    //             break;
    //     }
    //     return $next_date;
    // }

    // private function _next_outstanding_fine_date_fixer($loan_id,$date,$is_a_debtor=FALSE){
    //     $next_date = 0;
    //     if($is_a_debtor){
    //         $loan = $this->ci->debtors_m->get_loan($loan_id);
    //     }else{
    //         $loan = $this->ci->loan_invoices_m->fetch_loan($loan_id);
    //     }
        
    //     if ($loan->outstanding_loan_balance_fine_type ==1){
    //         $value = $loan->outstanding_loan_balance_fixed_fine_frequency;
    //     }
    //     else if ($loan->outstanding_loan_balance_fine_type == 2){
    //         $value = $loan->outstanding_loan_balance_percentage_fine_frequency;
    //     }
    //     else{
    //         $value = 0;
    //     }

    //     $weekdays = array('Day',
    //         'Sunday',
    //         'Monday',
    //         'Tuesday',
    //         'Wednesday',
    //         'Thursday',
    //         'Friday',
    //         'Saturday',
    //     );
    //     switch ($value)
    //     {
    //         case 1: //Day
    //             $next_date = strtotime('next day',$date);
    //             break;

    //         case 2: //Week
    //             $dt = "Next " . $weekdays[date('w') + 1];
    //             $next_date = strtotime($dt,$date);
    //             break;

    //         case 3: //Month
    //             $next_date = strtotime('next month',$date);
    //             break;

    //         case 4://Year
    //             $next_date = strtotime('next year',$date);
    //             break;

    //         default:
    //             $next_date = 0;
    //             break;
    //     }
    //     return $next_date;
    // }

    // function loan_fixer($loan_id=0,$is_a_debtor=FALSE){
    //     //loop through each day of the loan statement entries and correct everything accordingly
    //     if($is_a_debtor){
    //         $loan = $this->ci->debtors_m->get_loan($loan_id);
    //         if($loan){        
    //             $repayments = $this->ci->debtors_m->get_loan_repayments($loan->id);
    //             $statement_entries = $this->ci->debtors_m->get_loan_statement_for_library($loan_id);
    //             $total_paid = $this->ci->debtors_m->get_total_payment($loan_id);
    //             if($total_paid){
    //                 $total_paid = $total_paid->amount;
    //             }
    //             else{
    //                  $total_paid = 0;
    //             }
    //             $balance=0;
    //             $amount_paid=0;
    //             $amount_payable=0;
    //             $fine_balance=0;
    //             $unpaid_invoices = array();
    //             $instalment_balance = 0; 
    //             if($statement_entries){
    //                 foreach($statement_entries as $statement_entry){
    //                     if($statement_entry->transaction_type==1||$statement_entry->transaction_type==2||$statement_entry->transaction_type==3||$statement_entry->transaction_type==5){
    //                         if($statement_entry->transaction_type==1){  //installment
    //                             $balance+=$statement_entry->amount;
    //                             //find payment made for this installment.
    //                             $payment = $this->ci->debtors_m->get_payment_by_date($loan_id,($statement_entry->transaction_date+(7*24*60*60)));
    //                             $payment-=$amount_paid;
    //                             //balances         
    //                             $instalment_balance+=$statement_entry->amount;                 
    //                             $this->ci->debtors_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
    //                         }else if($statement_entry->transaction_type==2){
    //                             if(round($balance,2)<0){
    //                                 $this->ci->debtors_m->update_statement($statement_entry->id,array('active'=>0,'modified_on'=>time(),));
    //                                 $this->ci->debtors_m->update_loan_invoices($statement_entry->debtor_loan_invoice_id,array('active'=>0,'modified_on'=>time(),));
    //                             }
    //                             else{ 
    //                                 $fine = $this->ci->debtors_m->get_loan($loan_id,0);
    //                                 $amount_payable = 0;
                                   
    //                                 if ($fine->loan_fine_type == 1){
    //                                     $amount_payable = $fine->fixed_fine_amount;
    //                                 }
    //                                 else if ($fine->loan_fine_type == 2){
    //                                     if($fine->percentage_fine_on==2||$fine->percentage_fine_on==3){
    //                                         $outstanding_balance = $balance;
    //                                         $loan_balance = $balance - $fine_balance;
    //                                         $amount_payable = $this->_get_fine_percentage(
    //                                             $fine->percentage_fine_rate, 
    //                                             $fine->percentage_fine_on, 
    //                                             $outstanding_balance, 
    //                                             $loan_balance, 
    //                                             $statement_entry->debtor_loan_invoice_id, 
    //                                             $fine->loan_amount,
    //                                             '',
    //                                             '',
    //                                             $is_a_debtor);
                                           
    //                                     }else if($fine->percentage_fine_on==1){
    //                                         $outstanding_balance = $balance;
    //                                         $loan_balance = $balance - $fine_balance;
    //                                         $par_invoice = $this->ci->debtors_m->get_invoice($statement_entry->debtor_loan_invoice_id);
                                           
    //                                         $amount_payable = $this->_get_fine_percentage(
    //                                             $fine->percentage_fine_rate,
    //                                             $fine->percentage_fine_on,
    //                                             $outstanding_balance, 
    //                                             $loan_balance, 
    //                                             $par_invoice->fine_parent_loan_invoice_id,
    //                                             $fine->loan_amount,
    //                                             $balance,
    //                                             $statement_entry->transaction_date,$is_a_debtor);                   
                                          
    //                                     }

    //                                 }else if ($fine->loan_fine_type == 3){   
    //                                     if($fine->one_off_fine_type==1){     
    //                                         $amount_payable = $fine->one_off_fixed_amount;
    //                                     }else if($fine->one_off_fine_type==2){
    //                                         $par_invoice = $this->ci->debtors_m->get_invoice($statement_entry->debtor_loan_invoice_id);
    //                                         $outstanding_balance = $balance;
    //                                         $loan_balance = $balance - $fine_balance;
    //                                         $amount_payable = $this->_get_fine_percentage(
    //                                             $fine->one_off_percentage_rate, 
    //                                             $fine->one_off_percentage_rate_on, 
    //                                             $outstanding_balance, 
    //                                             $loan_balance, 
    //                                             $par_invoice->fine_parent_loan_invoice_id, 
    //                                             $fine->loan_amount,
    //                                             $balance,
    //                                             $statement_entry->transaction_date,$is_a_debtor
    //                                             );
    //                                     }
    //                                 }

    //                                 //continue from here
    //                                 $current_amount_payable = $this->ci->debtors_m->get_amount_payable_for_parent($statement_entry->debtor_loan_invoice_id);

    //                                 $current_amount_paid = $this->ci->debtors_m->get_current_amount_paid($statement_entry->debtor_loan_id,$statement_entry->transaction_date);
    //                                 $current_bal= $current_amount_payable -$current_amount_paid;

    //                                 if($amount_payable>0&&($current_bal>0)){
    //                                     $this->ci->debtors_m->update_statement($statement_entry->id,array('active'=>1,'amount'=>$amount_payable,'modified_on'=>time(),));
    //                                     $this->ci->debtors_m->update_loan_invoices($statement_entry->debtor_loan_invoice_id,array('active'=>1,'amount_payable'=>$amount_payable,'modified_on'=>time(),));                
    //                                     $balance += $amount_payable;
    //                                     $fine_balance+=$amount_payable;
    //                                 }
    //                                 else
    //                                 {
    //                                     $this->ci->debtors_m->update_statement($statement_entry->id,array('active'=>0,'modified_on'=>time(),));
    //                                     $this->ci->debtors_m->update_loan_invoices($statement_entry->debtor_loan_invoice_id,array('active'=>0,'modified_on'=>time(),));
    //                                 }
    //                                 //We may add the recalculation here
    //                             } 

                                            
    //                             $this->ci->debtors_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
    //                         }
    //                         else if($statement_entry->transaction_type==3){
    //                             if(round($balance,1)>0){

    //                             }else{
    //                                 $balance=0;
    //                             }
    //                             if($balance<=0){
    //                                 $this->ci->debtors_m->update_statement($statement_entry->id,array('active'=>0,'modified_on'=>time(),));
    //                                 $this->ci->debtors_m->update_loan_invoices($statement_entry->debtor_loan_invoice_id,array('active'=>'NULL','modified_on'=>time(),));
    //                             }
    //                             else{ 
    //                                 $fine = $this->ci->debtors_m->get_loan($statement_entry->debtor_loan_id,0);
    //                                 $amount_payable = 0;
                                   
    //                                 if ($fine->outstanding_loan_balance_fine_type == 1){
    //                                     $amount_payable = $fine->outstanding_loan_balance_fine_fixed_amount;
    //                                 }
    //                                 else if ($fine->outstanding_loan_balance_fine_type == 2){
    //                                     if($fine->outstanding_loan_balance_percentage_fine_on==2||$fine->outstanding_loan_balance_percentage_fine_on==3){
    //                                         $outstanding_balance = $balance;
    //                                         $loan_balance = $balance - $fine_balance;
    //                                         $amount_payable = $this->_get_fine_percentage(
    //                                             $fine->outstanding_loan_balance_percentage_fine_rate, 
    //                                             $fine->outstanding_loan_balance_percentage_fine_on, 
    //                                             $outstanding_balance, 
    //                                             $loan_balance, 
    //                                             $statement_entry->debtor_loan_invoice_id, 
    //                                             $fine->loan_amount,
    //                                             '',
    //                                             '',
    //                                             $is_a_debtor); 
                                           
    //                                     }
    //                                     else if($fine->outstanding_loan_balance_percentage_fine_on==1){

    //                                         $outstanding_balance = $balance;
    //                                         $loan_balance = $balance - $fine_balance;
    //                                         $par_invoice = $this->ci->debtors_m->get_invoice($statement_entry->debtor_loan_invoice_id);
                                           
    //                                         $amount_payable = $this->_get_fine_percentage(
    //                                             $fine->outstanding_loan_balance_percentage_fine_rate,
    //                                             $fine->outstanding_loan_balance_percentage_fine_on,
    //                                             $outstanding_balance, 
    //                                             $loan_balance, 
    //                                             $par_invoice->parent_id,
    //                                             $fine->loan_amount,
    //                                             $balance,
    //                                             $statement_entry->transaction_date,
    //                                             $is_a_debtor);                   
                                          
    //                                     }
    //                                 }
    //                                 else if ($fine->outstanding_loan_balance_fine_type == 3){
    //                                     $amount_payable = $fine->outstanding_loan_balance_fine_one_off_amount;
    //                                 }

    //                                 if($amount_payable>0){
    //                                     $this->ci->debtors_m->update_statement($statement_entry->id,array('active'=>1,'amount'=>$amount_payable,'modified_on'=>time(),));
    //                                     $this->ci->debtors_m->update_loan_invoices($statement_entry->debtor_loan_invoice_id,array('active'=>1,'amount_payable'=>$amount_payable,'modified_on'=>time(),));                
    //                                     $balance += $amount_payable;
    //                                     $fine_balance+=$amount_payable;
    //                                 }else{
    //                                     $this->ci->debtors_m->update_statement($statement_entry->id,array('active'=>0,'modified_on'=>time(),));
    //                                     $this->ci->debtors_m->update_loan_invoices($statement_entry->debtor_loan_invoice_id,array('active'=>0,'modified_on'=>time(),));
    //                                 }
    //                                 //We may add the recalculation here
    //                             }            
    //                             $this->ci->debtors_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
    //                         }else if($statement_entry->transaction_type==5){
    //                             //transfer installment
    //                             $balance+=$statement_entry->amount;
    //                             //find payment made for this installment.
    //                             $payment = $this->ci->debtors_m->get_payment_by_date($statement_entry->debtor_loan_id,($statement_entry->transaction_date+(7*24*60*60)));
    //                             $payment-=$amount_paid;
    //                             //balances         
    //                             $instalment_balance+=$statement_entry->amount;                 
    //                             $this->ci->debtors_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
    //                         }
    //                     }
    //                     else if($statement_entry->transaction_type==4)
    //                     {
                              
    //                             $balance -= $statement_entry->amount; 
    //                             $instalment_balance-=$statement_entry->amount;         
    //                             $this->ci->debtors_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
    //                     }
    //                 }
    //             }

    //             //2. calculate the new invoice to be sent in the future
    //         }
    //     }else{
    //         $loan = $this->ci->loans_m->get($loan_id);
    //         if($loan)
    //         {        
    //             $repayments = $this->ci->loan_repayments_m->get_loan_repayments($loan->id);
    //             $statement_entries = $this->ci->loans_m->get_loan_statement_for_library($loan_id);
    //             $total_paid = $this->ci->loan_repayments_m->get_total_payment($loan_id);
    //             if($total_paid)
    //             {
    //                 $total_paid = $total_paid->amount;
    //             }
    //             else
    //             {
    //                  $total_paid = 0;
    //             }
    //             $balance=0;
    //             $amount_paid=0;
    //             $amount_payable=0;
    //             $fine_balance=0;
    //             $unpaid_invoices = array();
    //             $instalment_balance = 0; 
    //             if($statement_entries)
    //             {
    //                 foreach($statement_entries as $statement_entry)
    //                 {
    //                     if($statement_entry->transaction_type==1||$statement_entry->transaction_type==2||$statement_entry->transaction_type==3||$statement_entry->transaction_type==5)
    //                         //invoices
    //                     {
    //                         if($statement_entry->transaction_type==1)
    //                         {  //installment
    //                             $balance+=$statement_entry->amount;
    //                             //find payment made for this installment.
    //                             $payment = $this->ci->loan_repayments_m->get_payment_by_date($statement_entry->loan_id,($statement_entry->transaction_date+(7*24*60*60)));
    //                             $payment-=$amount_paid;
    //                             //balances         
    //                             $instalment_balance+=$statement_entry->amount;                 
    //                             $this->ci->loans_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
    //                         }
    //                         else if($statement_entry->transaction_type==2)
    //                         {
    //                             if(round($balance,2)<0)
    //                             {
    //                                 $this->ci->loans_m->update_statement($statement_entry->id,array('active'=>0,'modified_on'=>time(),));
    //                                 $this->ci->loan_invoices_m->update($statement_entry->loan_invoice_id,array('active'=>0,'modified_on'=>time(),));
    //                             }
    //                             else
    //                             { 
    //                                 $fine = $this->ci->loan_invoices_m->fetch_loan($statement_entry->loan_id,0);
    //                                 $amount_payable = 0;
                                   
    //                                 if ($fine->loan_fine_type == 1)
    //                                 {
    //                                     $amount_payable = $fine->fixed_fine_amount;
    //                                 }
    //                                 else if ($fine->loan_fine_type == 2)
    //                                 {
    //                                     if($fine->percentage_fine_on==2||$fine->percentage_fine_on==3)
    //                                     {
    //                                         $outstanding_balance = $balance;
    //                                         $loan_balance = $balance - $fine_balance;
    //                                         $amount_payable = $this->_get_fine_percentage($fine->percentage_fine_rate, $fine->percentage_fine_on, $outstanding_balance, $loan_balance, $statement_entry->loan_invoice_id, $fine->loan_amount); 
                                           
    //                                     }
    //                                     else if($fine->percentage_fine_on==1)
    //                                     {

    //                                         $outstanding_balance = $balance;
    //                                         $loan_balance = $balance - $fine_balance;
    //                                         $par_invoice = $this->ci->loan_invoices_m->get($statement_entry->loan_invoice_id);
                                           
    //                                         $amount_payable = $this->_get_fine_percentage(
    //                                             $fine->percentage_fine_rate,
    //                                             $fine->percentage_fine_on,
    //                                             $outstanding_balance, 
    //                                             $loan_balance, 
    //                                             $par_invoice->fine_parent_loan_invoice_id,
    //                                             $fine->loan_amount,
    //                                             $balance,
    //                                             $statement_entry->transaction_date);                   
                                          
    //                                     }

    //                                 }
    //                                 else if ($fine->loan_fine_type == 3)
    //                                 {   
    //                                     if($fine->one_off_fine_type==1){     
    //                                         $amount_payable = $fine->one_off_fixed_amount;
    //                                     }else if($fine->one_off_fine_type==2){
    //                                         $par_invoice = $this->ci->loan_invoices_m->get($statement_entry->loan_invoice_id);
    //                                         $outstanding_balance = $balance;
    //                                         $loan_balance = $balance - $fine_balance;
    //                                         $amount_payable = $this->_get_fine_percentage(
    //                                             $fine->one_off_percentage_rate, 
    //                                             $fine->one_off_percentage_rate_on, 
    //                                             $outstanding_balance, 
    //                                             $loan_balance, 
    //                                             $par_invoice->fine_parent_loan_invoice_id, 
    //                                             $fine->loan_amount,
    //                                             $balance,
    //                                             $statement_entry->transaction_date
    //                                             );
    //                                     }
    //                                 }

    //                                 //continue from here
    //                                 $current_amount_payable = $this->ci->loans_m->get_amount_payable_for_parent($statement_entry->loan_invoice_id);
    //                                 //find payable by date then decide
    //                                 $current_amount_paid = $this->ci->loans_m->get_current_amount_paid($statement_entry->loan_id,$statement_entry->transaction_date);
    //                                 $current_bal= $current_amount_payable -$current_amount_paid;

                                

    //                                 if($amount_payable>0&&($current_bal>0)){
    //                                     $this->ci->loans_m->update_statement($statement_entry->id,array('active'=>1,'amount'=>$amount_payable,'modified_on'=>time(),));
    //                                     $this->ci->loan_invoices_m->update($statement_entry->loan_invoice_id,array('active'=>1,'amount_payable'=>$amount_payable,'modified_on'=>time(),));                
    //                                     $balance += $amount_payable;
    //                                     $fine_balance+=$amount_payable;
    //                                 }
    //                                 else
    //                                 {
    //                                     $this->ci->loans_m->update_statement($statement_entry->id,array('active'=>0,'modified_on'=>time(),));
    //                                     $this->ci->loan_invoices_m->update($statement_entry->loan_invoice_id,array('active'=>0,'modified_on'=>time(),));
    //                                 }
    //                                 //We may add the recalculation here
    //                             } 

                                            
    //                             $this->ci->loans_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
    //                         }
    //                         else if($statement_entry->transaction_type==3)
    //                         {
    //                             if(round($balance,1)>0){

    //                             }else{
    //                                 $balance=0;
    //                             }
    //                             if($balance<=0)
    //                             {
    //                                 $this->ci->loans_m->update_statement($statement_entry->id,array('active'=>0,'modified_on'=>time(),));
    //                                 $this->ci->loan_invoices_m->update($statement_entry->loan_invoice_id,array('active'=>'NULL','modified_on'=>time(),));
    //                             }
    //                             else
    //                             { 
    //                                 $fine = $this->ci->loan_invoices_m->fetch_loan($statement_entry->loan_id,0);
    //                                 $amount_payable = 0;
                                   
    //                                 if ($fine->outstanding_loan_balance_fine_type == 1)
    //                                 {
    //                                     $amount_payable = $fine->outstanding_loan_balance_fine_fixed_amount;
    //                                 }
    //                                 else if ($fine->outstanding_loan_balance_fine_type == 2)
    //                                 {
    //                                     if($fine->outstanding_loan_balance_percentage_fine_on==2||$fine->outstanding_loan_balance_percentage_fine_on==3)
    //                                     {
    //                                         $outstanding_balance = $balance;
    //                                         $loan_balance = $balance - $fine_balance;
    //                                         $amount_payable = $this->_get_fine_percentage($fine->outstanding_loan_balance_percentage_fine_rate, $fine->outstanding_loan_balance_percentage_fine_on, $outstanding_balance, $loan_balance, $statement_entry->loan_invoice_id, $fine->loan_amount); 
                                           
    //                                     }
    //                                     else if($fine->outstanding_loan_balance_percentage_fine_on==1){

    //                                         $outstanding_balance = $balance;
    //                                         $loan_balance = $balance - $fine_balance;
    //                                         $par_invoice = $this->ci->loan_invoices_m->get($statement_entry->loan_invoice_id);
                                           
    //                                         $amount_payable = $this->_get_fine_percentage(
    //                                             $fine->outstanding_loan_balance_percentage_fine_rate,
    //                                             $fine->outstanding_loan_balance_percentage_fine_on,
    //                                             $outstanding_balance, 
    //                                             $loan_balance, 
    //                                             $par_invoice->parent_id,
    //                                             $fine->loan_amount,
    //                                             $balance,
    //                                             $statement_entry->transaction_date);                   
                                          
    //                                     }
    //                                 }
    //                                 else if ($fine->outstanding_loan_balance_fine_type == 3)
    //                                 {
    //                                     $amount_payable = $fine->outstanding_loan_balance_fine_one_off_amount;
    //                                 }

    //                                 if($amount_payable>0){
    //                                     $this->ci->loans_m->update_statement($statement_entry->id,array('active'=>1,'amount'=>$amount_payable,'modified_on'=>time(),));
    //                                     $this->ci->loan_invoices_m->update($statement_entry->loan_invoice_id,array('active'=>1,'amount_payable'=>$amount_payable,'modified_on'=>time(),));                
    //                                     $balance += $amount_payable;
    //                                     $fine_balance+=$amount_payable;
    //                                 }else{
    //                                     $this->ci->loans_m->update_statement($statement_entry->id,array('active'=>0,'modified_on'=>time(),));
    //                                     $this->ci->loan_invoices_m->update($statement_entry->loan_invoice_id,array('active'=>0,'modified_on'=>time(),));
    //                                 }
    //                                 //We may add the recalculation here
    //                             }            
    //                             $this->ci->loans_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
    //                         }else if($statement_entry->transaction_type==5){
    //                             //transfer installment
    //                             $balance+=$statement_entry->amount;
    //                             //find payment made for this installment.
    //                             $payment = $this->ci->loan_repayments_m->get_payment_by_date($statement_entry->loan_id,($statement_entry->transaction_date+(7*24*60*60)));
    //                             $payment-=$amount_paid;
    //                             //balances         
    //                             $instalment_balance+=$statement_entry->amount;                 
    //                             $this->ci->loans_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
    //                         }
    //                     }
    //                     else if($statement_entry->transaction_type==4)
    //                     {
                              
    //                             $balance -= $statement_entry->amount; 
    //                             $instalment_balance-=$statement_entry->amount;         
    //                             $this->ci->loans_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
    //                     }
    //                 }
    //             }

    //             //2. calculate the new invoice to be sent in the future
    //         }
    //     } 
    // }

    // private function _get_fine_percentage($percentage_rate=0,$percentage_on=0,$outstanding_balance=0,$loan_balance=0, $invoice_id=0,$loan_amount=0,$balance=0,$transaction_date=0,$is_a_debtor=FALSE){
    //     $amount = 0;
    //     if($is_a_debtor){
    //         if ($percentage_on == 1){
    //             //On loan instalment balance 
    //             $instalment_balance = $this->ci->debtors_m->get_total_unpaid_loan_installments($invoice_id,$transaction_date);
    //             $amount = $instalment_balance * ($percentage_rate / 100);
    //         }else if ($percentage_on == 2){
    //             //On loan amount
    //             $amount = $loan_amount * ($percentage_rate / 100);
    //         }else if ($percentage_on == 3){
    //             //On outstanding balance
    //             $amount = $outstanding_balance * ($percentage_rate / 100);
    //         }
    //     }else{
    //         if ($percentage_on == 1){
    //             //On loan instalment balance 
    //             //$instalment_balance = $this->ci->loan_invoices_m->get_total_unpaid_loan_installments($invoice_id,$transaction_date);
    //             $amount = $loan_balance * ($percentage_rate / 100);
    //         }else if ($percentage_on == 2){
    //             //On loan amount
    //             $amount = $loan_amount * ($percentage_rate / 100);
    //         }else if ($percentage_on == 3){
    //             //On outstanding balance
    //             $amount = $outstanding_balance * ($percentage_rate / 100);
    //         }
    //     }
        
    //     return $amount;
    // }


     function fix_loan_invoices_fine($loan_id=0,$is_a_debtor = FALSE,$return_queue=FALSE,$date=0){
        if($loan_id){
            if($is_a_debtor){
                $loan = $this->ci->debtors_m->get_loan($loan_id);
                if($loan->enable_loan_fines){
                    $grace_period = $loan->grace_period; 
                    $disbursement_date = $loan->disbursement_date;
                    if(empty($grace_period)){
                        $grace_period=1;
                    }
                    if($disbursement_date){
                    }else{
                        $disbursement_date=time();
                    }
                        $current_date  = strtotime('+ '.$grace_period.' months',$disbursement_date?:time());
                    $days = (strtotime(date('d-M-Y',time())) - strtotime(date('d-M-Y',$current_date)))/(24*60*60)+1;
                    
                    $fines = array();
                    for($i=1;$i<=$days;$i++){
                        $fine_list= $this->ci->debtors_m->fine_invoice_list($loan->id,$current_date,$loan->group_id);
                        if($fine_list){
                            foreach ($fine_list as $fine){
                                if ((date('Ymd',$current_date) == date('Ymd', $fine->fine_date))){
                                    if(!$this->ci->debtors_m->fine_invoice_exists_fixer($fine->id,$current_date,$fine->group_id)){
                                        $amount = $this->get_fine_payable($loan->debtor_id, $loan, $loan->loan_amount, $fine->id, 0,$current_date,TRUE);
                                        $amount = (float) str_replace(',', '', number_format($amount, 2));
                                        if($amount>0){   
                                            //insert int db
                                            $fine_details = array(
                                                'fine_parent_loan_invoice_id' => $fine->id,
                                                'invoice_no' => $this->ci->debtors_m->calculate_invoice_no($fine->group_id),
                                                'debtor_id' => $fine->debtor_id,
                                                'debtor_loan_id' => $loan_id,
                                                'type' => 2,
                                                'interest_amount_payable' => 0,
                                                'principle_amount_payable' => 0,
                                                'invoice_date' => $current_date,
                                                'due_date' => $current_date,
                                                'fine_date' => 0,
                                                'amount_payable' => $amount,
                                                'amount_paid' => 0,
                                                'status' => 1,
                                                'active' => 1,
                                                'is_sent' => 1,
                                                'group_id' => $fine->group_id,
                                                'created_on' => time(),
                                                'created_by' => 1,
                                            );
                                            $fine_id = $this->ci->debtors_m->insert_loan_invoice($fine_details);
                                            if ($fine_id){
                                                $statement = array(
                                                    'debtor_loan_id' => $loan_id,
                                                    'debtor_id' => $fine->debtor_id,
                                                    'transaction_type' => 2,
                                                    'debtor_loan_invoice_id' => $fine_id,
                                                    'transaction_date' => $current_date,
                                                    'amount' => $amount,
                                                    'active' => 1,
                                                    'balance' => 0,
                                                    'group_id' => $fine->group_id,
                                                    'created_on' => time(),
                                                    'created_by' => 1,
                                                );
                                                $statement_id = $this->ci->debtors_m->insert_loan_statement($statement);
                                                if($return_queue){
                                                    if(date('Ymd',$fine->fine_date) == date('Ymd',$date)){
                                                        $loan_balance =$this->ci->debtors_m->get_loan_balance($fine->debtor_loan_id,$fine->group_id);
                                                        $lump_sum_remaining = $this->ci->debtors_m->get_loan_lump_sum_as_date($fine->debtor_loan_id,'',$fine->group_id);
                                                        $fines[] = array(
                                                            'loan_id'   =>  $fine->debtor_loan_id,
                                                            'invoice_id'    =>  $fine->id,
                                                            'invoice_no'    =>  $invoice_number,
                                                            'debtor_id'     =>  $fine->debtor_id,
                                                            'due_date'  =>  $current_date,
                                                            'invoice_date'  =>  $current_date,
                                                            'invoice_type'  => 2,
                                                            'amount_payable'    =>  $amount,
                                                            'lump_sum_remaining'    =>  $lump_sum_remaining,
                                                            'loan_balance'  =>  $loan_balance,
                                                            'group_id'  =>  $fine->group_id,
                                                            'description'  =>   'Late Loan Payment Fine Invoice',
                                                            'created_on'    =>  time(),
                                                        );
                                                    }
                                                }
                                            }
                                        }     
                                    }
                                    else{
                                    }
                                }
                                else{
                                }
                            }
                            $uptodate_fine_list = $this->ci->debtors_m->get_to_update_fixer($loan->id,$current_date,$loan->group_id);
                            foreach($uptodate_fine_list as $fine){
                                $next_fine_date = $this->_next_fine_date_fixer($loan,$current_date,TRUE);
                                $this->ci->debtors_m->update_loan_invoices($fine->id,array('fine_date'=>$next_fine_date,'modified_on'=>time()));
                            }
                        }
                        else
                        {
                            //continue;
                        }
                        $current_date+=(24*60*60);
                    }
                    if($return_queue && $date){
                        return $fines;
                    }else{
                        return TRUE;
                    }
                }else{
                    return TRUE;
                }
            }else{
                $loan = $this->ci->loans_m->get($loan_id);
                if($loan->enable_loan_fines){
                    $grace_period = $loan->grace_period; 
                    $disbursement_date = $loan->disbursement_date;
                    $grace_period_date = $loan->grace_period_date;
                    if(empty($grace_period)){
                        $grace_period=1;
                    }
                    if($grace_period=="date"){
                        $current_date = $grace_period_date;
                    }else{
                        if($disbursement_date){
                        }else{
                            $disbursement_date=time();
                        }
                        $current_date  = strtotime('+ '.$grace_period.' months',$disbursement_date?:time());
                    }
                    $days = (strtotime(date('d-M-Y',time())) - strtotime(date('d-M-Y',$current_date)))/(24*60*60)+1;
                    
                    $fines = array();
                    $fines_in_list = $this->ci->loan_invoices_m->get_all_fines_for_list($loan->id,$loan->group_id);
                    $updated_fine_list = array();

                    $fines_charged = $this->_get_charged_fines($loan->id,$loan->group_id);
                    for($i=1;$i<=$days;$i++){
                        $fine_list= $this->_get_fines_less_than_due_date($fines_in_list,$current_date,$updated_fine_list);//$this->ci->loan_invoices_m->fine_invoice_list($loan->id,$current_date);
                        if($fine_list){
                            foreach ($fine_list as $fine){
                                if ((date('Ymd',$current_date) == date('Ymd', $fine->fine_date))){
                                    //if(!$this->ci->loan_invoices_m->fine_invoice_exists_fixer($fine->id,$current_date,$fine->loan_id)){
                                    if(array_key_exists((date('Ymd',$current_date).'-'.$fine->id),$fines_charged) ==FALSE){
                                        $amount = $this->get_fine_payable($loan->member_id, $loan, $loan->loan_amount, $fine->id, 0,$current_date,'',$fine->interest_amount_payable);
                                        $amount = (float) str_replace(',', '', number_format($amount, 2));
                                        if($amount>0){   
                                            $invoice_number = 'Invoice-'.time();
                                            $fine_details = array(
                                                'fine_parent_loan_invoice_id' => $fine->id,
                                                'invoice_no' => $invoice_number,
                                                'member_id' => $fine->member_id,
                                                'loan_id' => $fine->loan_id,
                                                'type' => 2,
                                                'interest_amount_payable' => 0,
                                                'principle_amount_payable' => 0,
                                                'invoice_date' => $current_date,
                                                'due_date' => $current_date,
                                                'fine_date' => 0,
                                                'amount_payable' => $amount,
                                                'amount_paid' => 0,
                                                'status' => 1,
                                                'active' => 1,
                                                'is_sent' => 1,
                                                'group_id' => $fine->group_id,
                                                'created_on' => time(),
                                                'modified_on' => time(),
                                                'created_by' => 1,
                                            );
                                            $fine_id =  $this->ci->loan_invoices_m->insert($fine_details);
                                            if ($fine_id){
                                                $fines_charged[date('Ymd',$current_date).'-'.$fine->id] = 1;
                                                $statement = array(
                                                    'loan_id' => $fine->loan_id,
                                                    'member_id' => $fine->member_id,
                                                    'transaction_type' => 2,
                                                    'loan_invoice_id' => $fine_id,
                                                    'transaction_date' => $current_date,
                                                    'amount' => $amount,
                                                    'active' => 1,
                                                    'balance' => 0,
                                                    'group_id' => $fine->group_id,
                                                    'created_on' => time(),
                                                    'modified_on' => time(),
                                                    'created_by' => 1,
                                                );
                                                $statement_id = $this->ci->loans_m->insert_loan_statement($statement);
                                                if($return_queue){
                                                    if(date('Ymd',$fine->fine_date) == date('Ymd',$date)){
                                                        $loan_balance =$this->ci->loans_m->get_loan_balance($fine->loan_id,$fine->group_id);
                                                        $lump_sum_remaining = $this->ci->loan_invoices_m->get_loan_lump_sum_as_date($fine->loan_id,'',$fine->group_id);
                                                        $fines[] = array(
                                                            'loan_id'   =>  $fine->loan_id,
                                                            'invoice_id'    =>  $fine->id,
                                                            'invoice_no'    =>  $invoice_number,
                                                            'member_id'     =>  $fine->member_id,
                                                            'due_date'  =>  $current_date,
                                                            'invoice_date'  =>  $current_date,
                                                            'invoice_type'  => 2,
                                                            'amount_payable'    =>  $amount,
                                                            'lump_sum_remaining'    =>  $lump_sum_remaining,
                                                            'loan_balance'  =>  $loan_balance,
                                                            'group_id'  =>  $fine->group_id,
                                                            'description'  =>   'Late Loan Payment Fine Invoice',
                                                            'created_on'    =>  time(),
                                                        );
                                                    }
                                                }
                                            }
                                        }     
                                    }
                                    else{
                                    }
                                }
                                else{
                                }
                            }
                            // $uptodate_fine_list = $this->ci->loan_invoices_m->get_to_update_fixer($loan->id,$current_date);
                            // foreach($uptodate_fine_list as $fine){
                            //     $next_fine_date = $this->_next_fine_date_fixer($loan,$current_date);
                            //     $this->ci->loan_invoices_m->update($fine->id,array('fine_date'=>$next_fine_date,'modified_on'=>time()));
                            //     $updated_fine_list[$fine->id] = $next_fine_date;
                            // }
                            foreach($fines_in_list as $fine){
                                if($fine->due_date < $current_date){
                                    $next_fine_date = $this->_next_fine_date_fixer($loan,$current_date);
                                    $updated_fine_list[$fine->id] = $next_fine_date;
                                }
                            }
                        }else{
                            //continue;
                        }

                        $current_date+=(24*60*60);
                    }
                    foreach ($updated_fine_list as $fine_id=>$fine_date){
                        $this->ci->loan_invoices_m->update($fine_id,array('fine_date'=>$fine_date,'modified_on'=>time()));
                    }
                    if($return_queue && $date){
                        return $fines;
                    }else{
                        return TRUE;
                    }
                }else{
                    return TRUE;
                }
            }
        }else{
            return FALSE;
        }
        
    }

    public function get_fine_payable($member_id=0, $loan=array(), $loan_amount=0, $invoice_id = 0, $type = 0,$date=0,$is_a_debtor=FALSE,$installment_amount=0){
        //percentage one off fine type
        /**
            1. Total loan installment balance
            2. Total loan amount
            3. Total unpaid loan amout 
        **/
        if($is_a_debtor){
            //debtor
            $debtor_id = $member_id;
            $amount_payable = 0;
            $fine = $loan;//$this->ci->debtors_m->get_loan($loan_id);
            if($fine->loan_fine_type==1){
                //fixed fine type
                if($fine->fixed_amount_fine_frequency_on==2){
                    if($this->ci->debtors_m->get_invoice_sent_today($loan->id,$date,$loan->group_id)){
                        $amount_payable=0;
                    }else{
                        $amount_payable = $fine->fixed_fine_amount;
                    }
                }else{
                    $amount_payable = $fine->fixed_fine_amount;       
                }
            }else if($fine->loan_fine_type==2){
                //percentage fine
                $rate = $fine->percentage_fine_rate;
                $rate_on = $fine->percentage_fine_on;
                if($rate_on == 1){
                    $installment_balance = $this->ci->debtors_m->get_total_unpaid_loan_installments($invoice_id,'',$loan->group_id);
                    $amount_payable = ($rate*$installment_balance)/100; 
                }
                else if($rate_on==2){
                     $amount_payable = ($rate*$loan_amount)/100;
                }
                else if($rate_on==3){
                    $total_unpaid_loan = $this->ci->debtors_m->get_outstanding_balance($debtor_id, $loan->id,'',$loan->group_id);
                    $amount_payable = ($rate*$total_unpaid_loan)/100; 
                }
            }else if($fine->loan_fine_type==3){
                //one off fine
                if($fine->one_off_fine_type==1){
                    //fixed one off fine type
                    $amount_payable = $one_off_fixed_amount = $fine->one_off_fixed_amount;
                }else if($fine->one_off_fine_type==2){
                    $rate = $fine->one_off_percentage_rate;
                    $rate_on = $fine->one_off_percentage_rate_on;
                    if($rate_on == 1){
                        $installment_balance = $this->ci->debtors_m->get_total_unpaid_loan_installments($invoice_id,'',$loan->group_id);
                        $amount_payable = ($rate*$installment_balance)/100;
                    }else if($rate_on==2){
                        $amount_payable = ($rate*$loan_amount)/100;
                    }else if($rate_on==3){
                        $total_unpaid_loan = $this->ci->debtors_m->get_outstanding_balance($debtor_id, $loan->id,'',$loan->group_id);
                        $amount_payable = ($rate*$total_unpaid_loan)/100;   
                    }
                }
            }
            return $amount_payable;
        }else{
            //member
            $amount_payable = 0;
            $fine = $loan;
            if($fine->loan_fine_type==1){
                //fixed fine type
                if($fine->fixed_amount_fine_frequency_on==2){
                    if($this->ci->loan_invoices_m->get_invoice_sent_today($loan->id,$date,$loan->group_id)){
                        $amount_payable=0;
                    }else{
                        $amount_payable = $fine->fixed_fine_amount;
                    }
                }else{
                    $amount_payable = $fine->fixed_fine_amount;       
                }
                
            }
            else if($fine->loan_fine_type==2){
                //percentage fine
                $rate = $fine->percentage_fine_rate;
                $rate_on = $fine->percentage_fine_on;
                if($rate_on == 1)
                {
                    $installment_balance = $this->ci->loan_invoices_m->get_total_unpaid_loan_installments($invoice_id,$date,$loan->group_id);
                    $amount_payable = ($rate*$installment_balance)/100; 
                }
                else if($rate_on==2)
                {
                     $amount_payable = ($rate*$loan_amount)/100;
                }
                else if($rate_on==3)
                {
                    $total_unpaid_loan = $this->ci->loan_invoices_m->get_outstanding_balance($member_id, $loan->id,$date,$loan->group_id);
                    $amount_payable = ($rate*$total_unpaid_loan)/100; 
                }elseif($rate_on == 4){
                    $amount_payable = ($rate*$installment_amount)/100;
                }
            }
            else if($fine->loan_fine_type==3){
                //one off fine
                if($fine->one_off_fine_type==1){
                    //fixed one off fine type
                    $amount_payable = $one_off_fixed_amount = $fine->one_off_fixed_amount;
                }
                else if($fine->one_off_fine_type==2){
                    $rate = $fine->one_off_percentage_rate;
                    $rate_on = $fine->one_off_percentage_rate_on;
                    if($rate_on == 1)
                    {
                        $installment_balance = $this->ci->loan_invoices_m->get_total_unpaid_loan_installments($invoice_id,$date,$loan->group_id);
                        $amount_payable = ($rate*$installment_balance)/100;
                    }
                    else if($rate_on==2)
                    {
                        $amount_payable = ($rate*$loan_amount)/100;
                    }else if($rate_on==3)
                    {
                        $total_unpaid_loan = $this->ci->loan_invoices_m->get_outstanding_balance($member_id, $loan->id,$date,$loan->group_id);
                        $amount_payable = ($rate*$total_unpaid_loan)/100;   
                    }elseif($rate_on == 4){
                        $amount_payable = ($rate*$installment_amount)/100;
                    }
                }
            }
            return $amount_payable;
        }
    }

    private function _next_fine_date_fixer($loan,$date,$is_a_debtor = FALSE){
        $next_date = 0;
        // if($is_a_debtor){
        //     $loan = $this->ci->debtors_m->get_loan($loan_id);
        // }else{
        //     $loan = $this->ci->loan_invoices_m->fetch_loan($loan_id);
        // }

        if ($loan->loan_fine_type ==1){
            //fixed amount && fixed time_ref
            $value = $loan->fixed_amount_fine_frequency;
        }
        elseif ($loan->loan_fine_type == 2){
            $value = $loan->percentage_fine_frequency;
        }
        elseif ($loan->loan_fine_type == 3){
            return $next_date;
        }
        else{
            return 0;
        }

        $weekdays = array('Day',
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
        );
        switch ($value)
        {
            case 1: //Day
                $next_date = strtotime('next day',$date);
                break;

            case 2: //Week
                $dt = "Next " . $weekdays[date('w') + 1];
                $next_date = strtotime($dt,$date);
                //$next_date = strtotime('next week',$date);
                break;

            case 3: //Month
                $next_date = strtotime('next month',$date);
                break;

            case 4://Year
                $next_date = strtotime('next year',$date);
                break;

            default:
                $next_date = 0;
                break;
        }
        return $next_date;
    }

    private function _next_outstanding_fine_date_fixer($loan=array(),$date,$is_a_debtor=FALSE){
        $next_date = 0;
        // if($is_a_debtor){
        //     $loan = $this->ci->debtors_m->get_loan($loan_id);
        // }else{
        //     $loan = $this->ci->loan_invoices_m->fetch_loan($loan_id);
        // }
        
        if ($loan->outstanding_loan_balance_fine_type ==1){
            $value = $loan->outstanding_loan_balance_fixed_fine_frequency;
        }
        else if ($loan->outstanding_loan_balance_fine_type == 2){
            $value = $loan->outstanding_loan_balance_percentage_fine_frequency;
        }
        else{
            $value = 0;
        }

        $weekdays = array('Day',
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
        );
        switch ($value)
        {
            case 1: //Day
                $next_date = strtotime('next day',$date);
                break;

            case 2: //Week
                $dt = "Next " . $weekdays[date('w') + 1];
                $next_date = strtotime($dt,$date);
                break;

            case 3: //Month
                $next_date = strtotime('next month',$date);
                break;

            case 4://Year
                $next_date = strtotime('next year',$date);
                break;

            default:
                $next_date = 0;
                break;
        }
        return $next_date;
    }

    function loan_fixer($loan_id=0,$is_a_debtor=FALSE){
        //loop through each day of the loan statement entries and correct everything accordingly
        if($is_a_debtor){
            $loan = $this->ci->debtors_m->get_loan($loan_id);
            if($loan){        
                $repayments = $this->ci->debtors_m->get_loan_repayments($loan->id);
                $statement_entries = $this->ci->debtors_m->get_loan_statement_for_library($loan_id,$loan->group_id);
                $total_paid = $this->ci->debtors_m->get_total_payment($loan_id);
                if($total_paid){
                    $total_paid = $total_paid->amount;
                }
                else{
                     $total_paid = 0;
                }
                $balance=0;
                $amount_paid=0;
                $amount_payable=0;
                $fine_balance=0;
                $unpaid_invoices = array();
                $instalment_balance = 0; 
                if($statement_entries){
                    foreach($statement_entries as $statement_entry){
                        if($statement_entry->transaction_type==1||$statement_entry->transaction_type==2||$statement_entry->transaction_type==3||$statement_entry->transaction_type==5){
                            if($statement_entry->transaction_type==1){  //installment
                                $balance+=$statement_entry->amount;
                                //find payment made for this installment.
                                $payment = $this->ci->debtors_m->get_payment_by_date($loan_id,($statement_entry->transaction_date+(7*24*60*60)));
                                $payment-=$amount_paid;
                                //balances         
                                $instalment_balance+=$statement_entry->amount;                 
                                $this->ci->debtors_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
                            }else if($statement_entry->transaction_type==2){
                                if(round($balance,2)<0){
                                    $this->ci->debtors_m->update_statement($statement_entry->id,array('active'=>0,'modified_on'=>time(),));
                                    $this->ci->debtors_m->update_loan_invoices($statement_entry->debtor_loan_invoice_id,array('active'=>0,'modified_on'=>time(),));
                                }
                                else{ 
                                    $fine = $this->ci->debtors_m->get_loan($loan_id,0);
                                    $amount_payable = 0;
                                   
                                    if ($fine->loan_fine_type == 1){
                                        $amount_payable = $fine->fixed_fine_amount;
                                    }
                                    else if ($fine->loan_fine_type == 2){
                                        if($fine->percentage_fine_on==2||$fine->percentage_fine_on==3){
                                            $outstanding_balance = $balance;
                                            $loan_balance = $balance - $fine_balance;
                                            $amount_payable = $this->_get_fine_percentage(
                                                $fine->percentage_fine_rate, 
                                                $fine->percentage_fine_on, 
                                                $outstanding_balance, 
                                                $loan_balance, 
                                                $statement_entry->debtor_loan_invoice_id, 
                                                $fine->loan_amount,
                                                '',
                                                '',
                                                $is_a_debtor);
                                           
                                        }else if($fine->percentage_fine_on==1){
                                            $outstanding_balance = $balance;
                                            $loan_balance = $balance - $fine_balance;
                                            $par_invoice = $this->ci->debtors_m->get_invoice($statement_entry->debtor_loan_invoice_id);
                                           
                                            $amount_payable = $this->_get_fine_percentage(
                                                $fine->percentage_fine_rate,
                                                $fine->percentage_fine_on,
                                                $outstanding_balance, 
                                                $loan_balance, 
                                                $par_invoice->fine_parent_loan_invoice_id,
                                                $fine->loan_amount,
                                                $balance,
                                                $statement_entry->transaction_date,$is_a_debtor);                   
                                          
                                        }

                                    }else if ($fine->loan_fine_type == 3){   
                                        if($fine->one_off_fine_type==1){     
                                            $amount_payable = $fine->one_off_fixed_amount;
                                        }else if($fine->one_off_fine_type==2){
                                            $par_invoice = $this->ci->debtors_m->get_invoice($statement_entry->debtor_loan_invoice_id);
                                            $outstanding_balance = $balance;
                                            $loan_balance = $balance - $fine_balance;
                                            $amount_payable = $this->_get_fine_percentage(
                                                $fine->one_off_percentage_rate, 
                                                $fine->one_off_percentage_rate_on, 
                                                $outstanding_balance, 
                                                $loan_balance, 
                                                $par_invoice->fine_parent_loan_invoice_id, 
                                                $fine->loan_amount,
                                                $balance,
                                                $statement_entry->transaction_date,$is_a_debtor
                                                );
                                        }
                                    }

                                    //continue from here
                                    $current_amount_payable = $this->ci->debtors_m->get_amount_payable_for_parent($statement_entry->debtor_loan_invoice_id);

                                    $current_amount_paid = $this->ci->debtors_m->get_current_amount_paid($statement_entry->debtor_loan_id,$statement_entry->transaction_date);
                                    $current_bal= $current_amount_payable -$current_amount_paid;

                                    if($amount_payable>0&&($current_bal>0)){
                                        $this->ci->debtors_m->update_statement($statement_entry->id,array('active'=>1,'amount'=>$amount_payable,'modified_on'=>time(),));
                                        $this->ci->debtors_m->update_loan_invoices($statement_entry->debtor_loan_invoice_id,array('active'=>1,'amount_payable'=>$amount_payable,'modified_on'=>time(),));                
                                        $balance += $amount_payable;
                                        $fine_balance+=$amount_payable;
                                    }
                                    else
                                    {
                                        $this->ci->debtors_m->update_statement($statement_entry->id,array('active'=>0,'modified_on'=>time(),));
                                        $this->ci->debtors_m->update_loan_invoices($statement_entry->debtor_loan_invoice_id,array('active'=>0,'modified_on'=>time(),));
                                    }
                                    //We may add the recalculation here
                                } 

                                            
                                $this->ci->debtors_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
                            }
                            else if($statement_entry->transaction_type==3){
                                if(round($balance,1)>0){

                                }else{
                                    $balance=0;
                                }
                                if($balance<=0){
                                    $this->ci->debtors_m->update_statement($statement_entry->id,array('active'=>0,'modified_on'=>time(),));
                                    $this->ci->debtors_m->update_loan_invoices($statement_entry->debtor_loan_invoice_id,array('active'=>'NULL','modified_on'=>time(),));
                                }
                                else{ 
                                    $fine = $this->ci->debtors_m->get_loan($statement_entry->debtor_loan_id,0);
                                    $amount_payable = 0;
                                   
                                    if ($fine->outstanding_loan_balance_fine_type == 1){
                                        $amount_payable = $fine->outstanding_loan_balance_fine_fixed_amount;
                                    }
                                    else if ($fine->outstanding_loan_balance_fine_type == 2){
                                        if($fine->outstanding_loan_balance_percentage_fine_on==2||$fine->outstanding_loan_balance_percentage_fine_on==3){
                                            $outstanding_balance = $balance;
                                            $loan_balance = $balance - $fine_balance;
                                            $amount_payable = $this->_get_fine_percentage(
                                                $fine->outstanding_loan_balance_percentage_fine_rate, 
                                                $fine->outstanding_loan_balance_percentage_fine_on, 
                                                $outstanding_balance, 
                                                $loan_balance, 
                                                $statement_entry->debtor_loan_invoice_id, 
                                                $fine->loan_amount,
                                                '',
                                                '',
                                                $is_a_debtor); 
                                           
                                        }
                                        else if($fine->outstanding_loan_balance_percentage_fine_on==1){

                                            $outstanding_balance = $balance;
                                            $loan_balance = $balance - $fine_balance;
                                            $par_invoice = $this->ci->debtors_m->get_invoice($statement_entry->debtor_loan_invoice_id);
                                           
                                            $amount_payable = $this->_get_fine_percentage(
                                                $fine->outstanding_loan_balance_percentage_fine_rate,
                                                $fine->outstanding_loan_balance_percentage_fine_on,
                                                $outstanding_balance, 
                                                $loan_balance, 
                                                $par_invoice->parent_id,
                                                $fine->loan_amount,
                                                $balance,
                                                $statement_entry->transaction_date,
                                                $is_a_debtor);                   
                                          
                                        }
                                    }
                                    else if ($fine->outstanding_loan_balance_fine_type == 3){
                                        $amount_payable = $fine->outstanding_loan_balance_fine_one_off_amount;
                                    }

                                    if($amount_payable>0){
                                        $this->ci->debtors_m->update_statement($statement_entry->id,array('active'=>1,'amount'=>$amount_payable,'modified_on'=>time(),));
                                        $this->ci->debtors_m->update_loan_invoices($statement_entry->debtor_loan_invoice_id,array('active'=>1,'amount_payable'=>$amount_payable,'modified_on'=>time(),));                
                                        $balance += $amount_payable;
                                        $fine_balance+=$amount_payable;
                                    }else{
                                        $this->ci->debtors_m->update_statement($statement_entry->id,array('active'=>0,'modified_on'=>time(),));
                                        $this->ci->debtors_m->update_loan_invoices($statement_entry->debtor_loan_invoice_id,array('active'=>0,'modified_on'=>time(),));
                                    }
                                    //We may add the recalculation here
                                }            
                                $this->ci->debtors_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
                            }else if($statement_entry->transaction_type==5){
                                //transfer installment
                                $balance+=$statement_entry->amount;
                                //find payment made for this installment.
                                $payment = $this->ci->debtors_m->get_payment_by_date($statement_entry->debtor_loan_id,($statement_entry->transaction_date+(7*24*60*60)));
                                $payment-=$amount_paid;
                                //balances         
                                $instalment_balance+=$statement_entry->amount;                 
                                $this->ci->debtors_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
                            }
                        }
                        else if($statement_entry->transaction_type==4)
                        {
                              
                                $balance -= $statement_entry->amount; 
                                $instalment_balance-=$statement_entry->amount;         
                                $this->ci->debtors_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
                        }
                    }
                }

                //2. calculate the new invoice to be sent in the future
            }
        }else{
            $loan = $this->ci->loans_m->get($loan_id);
            if($loan)
            {        
                $repayments = $this->ci->loan_repayments_m->get_loan_repayments($loan->id);
                $statement_entries = $this->ci->loans_m->get_loan_statement_for_library($loan_id,$loan->group_id);
                $total_paid = $this->ci->loan_repayments_m->get_total_payment($loan_id);
                if($total_paid)
                {
                    $total_paid = $total_paid->amount;
                }
                else
                {
                     $total_paid = 0;
                }
                $balance=0;
                $amount_paid=0;
                $amount_payable=0;
                $fine_balance=0;
                $unpaid_invoices = array();
                $instalment_balance = 0; 
                if($statement_entries)
                {
                    foreach($statement_entries as $statement_entry)
                    {
                        if($statement_entry->transaction_type==1||$statement_entry->transaction_type==2||$statement_entry->transaction_type==3||$statement_entry->transaction_type==5)
                            //invoices
                        {
                            if($statement_entry->transaction_type==1)
                            {  //installment
                                $balance+=$statement_entry->amount;
                                //find payment made for this installment.
                                $payment = $this->ci->loan_repayments_m->get_payment_by_date($statement_entry->loan_id,($statement_entry->transaction_date+(7*24*60*60)));
                                $payment-=$amount_paid;
                                //balances         
                                $instalment_balance+=$statement_entry->amount;                 
                                $this->ci->loans_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
                            }
                            else if($statement_entry->transaction_type==2)
                            {
                                if(round($balance,2)<0)
                                {
                                    $this->ci->loans_m->update_statement($statement_entry->id,array('active'=>0,'modified_on'=>time(),));
                                    $this->ci->loan_invoices_m->update($statement_entry->loan_invoice_id,array('active'=>0,'modified_on'=>time(),));
                                }
                                else
                                { 
                                    $fine = $this->ci->loan_invoices_m->fetch_loan($statement_entry->loan_id,0);
                                    $amount_payable = 0;
                                   
                                    if ($fine->loan_fine_type == 1)
                                    {
                                        $amount_payable = $fine->fixed_fine_amount;
                                    }
                                    else if ($fine->loan_fine_type == 2)
                                    {
                                        if($fine->percentage_fine_on==2||$fine->percentage_fine_on==3)
                                        {
                                            $outstanding_balance = $balance;
                                            $loan_balance = $balance - $fine_balance;
                                            $amount_payable = $this->_get_fine_percentage($fine->percentage_fine_rate, $fine->percentage_fine_on, $outstanding_balance, $loan_balance, $statement_entry->loan_invoice_id, $fine->loan_amount); 
                                           
                                        }
                                        else if($fine->percentage_fine_on==1)
                                        {

                                            $outstanding_balance = $balance;
                                            $loan_balance = $balance - $fine_balance;
                                            $par_invoice = $this->ci->loan_invoices_m->get($statement_entry->loan_invoice_id);
                                           
                                            $amount_payable = $this->_get_fine_percentage(
                                                $fine->percentage_fine_rate,
                                                $fine->percentage_fine_on,
                                                $outstanding_balance, 
                                                $loan_balance, 
                                                $par_invoice->fine_parent_loan_invoice_id,
                                                $fine->loan_amount,
                                                $balance,
                                                $statement_entry->transaction_date);                   
                                          
                                        }

                                    }
                                    else if ($fine->loan_fine_type == 3)
                                    {   
                                        if($fine->one_off_fine_type==1){     
                                            $amount_payable = $fine->one_off_fixed_amount;
                                        }else if($fine->one_off_fine_type==2){
                                            $par_invoice = $this->ci->loan_invoices_m->get($statement_entry->loan_invoice_id);
                                            $outstanding_balance = $balance;
                                            $loan_balance = $balance - $fine_balance;
                                            $amount_payable = $this->_get_fine_percentage(
                                                $fine->one_off_percentage_rate, 
                                                $fine->one_off_percentage_rate_on, 
                                                $outstanding_balance, 
                                                $loan_balance, 
                                                $par_invoice->fine_parent_loan_invoice_id, 
                                                $fine->loan_amount,
                                                $balance,
                                                $statement_entry->transaction_date
                                                );
                                        }
                                    }

                                    //continue from here
                                    $current_amount_payable = $this->ci->loans_m->get_amount_payable_for_parent($statement_entry->loan_invoice_id);
                                    //find payable by date then decide
                                    $current_amount_paid = $this->ci->loans_m->get_current_amount_paid($statement_entry->loan_id,$statement_entry->transaction_date);
                                    $current_bal= $current_amount_payable -$current_amount_paid;

                                

                                    if($amount_payable>0&&($current_bal>0)){
                                        $this->ci->loans_m->update_statement($statement_entry->id,array('active'=>1,'amount'=>$amount_payable,'modified_on'=>time(),));
                                        $this->ci->loan_invoices_m->update($statement_entry->loan_invoice_id,array('active'=>1,'amount_payable'=>$amount_payable,'modified_on'=>time(),));                
                                        $balance += $amount_payable;
                                        $fine_balance+=$amount_payable;
                                    }
                                    else
                                    {
                                        $this->ci->loans_m->update_statement($statement_entry->id,array('active'=>0,'modified_on'=>time(),));
                                        $this->ci->loan_invoices_m->update($statement_entry->loan_invoice_id,array('active'=>0,'modified_on'=>time(),));
                                    }
                                    //We may add the recalculation here
                                } 

                                            
                                $this->ci->loans_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
                            }
                            else if($statement_entry->transaction_type==3)
                            {
                                if(round($balance,1)>0){

                                }else{
                                    $balance=0;
                                }
                                if($balance<=0)
                                {
                                    $this->ci->loans_m->update_statement($statement_entry->id,array('active'=>0,'modified_on'=>time(),));
                                    $this->ci->loan_invoices_m->update($statement_entry->loan_invoice_id,array('active'=>'NULL','modified_on'=>time(),));
                                }
                                else
                                { 
                                    $fine = $this->ci->loan_invoices_m->fetch_loan($statement_entry->loan_id,0);
                                    $amount_payable = 0;
                                   
                                    if ($fine->outstanding_loan_balance_fine_type == 1)
                                    {
                                        $amount_payable = $fine->outstanding_loan_balance_fine_fixed_amount;
                                    }
                                    else if ($fine->outstanding_loan_balance_fine_type == 2)
                                    {
                                        if($fine->outstanding_loan_balance_percentage_fine_on==2||$fine->outstanding_loan_balance_percentage_fine_on==3)
                                        {
                                            $outstanding_balance = $balance;
                                            $loan_balance = $balance - $fine_balance;
                                            $amount_payable = $this->_get_fine_percentage($fine->outstanding_loan_balance_percentage_fine_rate, $fine->outstanding_loan_balance_percentage_fine_on, $outstanding_balance, $loan_balance, $statement_entry->loan_invoice_id, $fine->loan_amount); 
                                           
                                        }
                                        else if($fine->outstanding_loan_balance_percentage_fine_on==1){

                                            $outstanding_balance = $balance;
                                            $loan_balance = $balance - $fine_balance;
                                            $par_invoice = $this->ci->loan_invoices_m->get($statement_entry->loan_invoice_id);
                                           
                                            $amount_payable = $this->_get_fine_percentage(
                                                $fine->outstanding_loan_balance_percentage_fine_rate,
                                                $fine->outstanding_loan_balance_percentage_fine_on,
                                                $outstanding_balance, 
                                                $loan_balance, 
                                                $par_invoice->parent_id,
                                                $fine->loan_amount,
                                                $balance,
                                                $statement_entry->transaction_date);                   
                                          
                                        }
                                    }
                                    else if ($fine->outstanding_loan_balance_fine_type == 3)
                                    {
                                        $amount_payable = $fine->outstanding_loan_balance_fine_one_off_amount;
                                    }

                                    if($amount_payable>0){
                                        $this->ci->loans_m->update_statement($statement_entry->id,array('active'=>1,'amount'=>$amount_payable,'modified_on'=>time(),));
                                        $this->ci->loan_invoices_m->update($statement_entry->loan_invoice_id,array('active'=>1,'amount_payable'=>$amount_payable,'modified_on'=>time(),));                
                                        $balance += $amount_payable;
                                        $fine_balance+=$amount_payable;
                                    }else{
                                        $this->ci->loans_m->update_statement($statement_entry->id,array('active'=>0,'modified_on'=>time(),));
                                        $this->ci->loan_invoices_m->update($statement_entry->loan_invoice_id,array('active'=>0,'modified_on'=>time(),));
                                    }
                                    //We may add the recalculation here
                                }            
                                $this->ci->loans_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
                            }else if($statement_entry->transaction_type==5){
                                //transfer installment
                                $balance+=$statement_entry->amount;
                                //find payment made for this installment.
                                $payment = $this->ci->loan_repayments_m->get_payment_by_date($statement_entry->loan_id,($statement_entry->transaction_date+(7*24*60*60)));
                                $payment-=$amount_paid;
                                //balances         
                                $instalment_balance+=$statement_entry->amount;                 
                                $this->ci->loans_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
                            }
                        }
                        else if($statement_entry->transaction_type==4)
                        {
                              
                                $balance -= $statement_entry->amount; 
                                $instalment_balance-=$statement_entry->amount;         
                                $this->ci->loans_m->update_statement($statement_entry->id,array('balance'=>$balance,'modified_on'=>time(),));
                        }
                    }
                }

                //2. calculate the new invoice to be sent in the future
            }
        } 
    }

    private function _get_fine_percentage($percentage_rate=0,$percentage_on=0,$outstanding_balance=0,$loan_balance=0, $invoice_id=0,$loan_amount=0,$balance=0,$transaction_date=0,$is_a_debtor=FALSE){
        $amount = 0;
        if($is_a_debtor){
            if ($percentage_on == 1){
                //On loan instalment balance 
                $instalment_balance = $this->ci->debtors_m->get_total_unpaid_loan_installments($invoice_id,$transaction_date);
                $amount = $instalment_balance * ($percentage_rate / 100);
            }else if ($percentage_on == 2){
                //On loan amount
                $amount = $loan_amount * ($percentage_rate / 100);
            }else if ($percentage_on == 3){
                //On outstanding balance
                $amount = $outstanding_balance * ($percentage_rate / 100);
            }
        }else{
            if ($percentage_on == 1){
                //On loan instalment balance 
                //$instalment_balance = $this->ci->loan_invoices_m->get_total_unpaid_loan_installments($invoice_id,$transaction_date);
                $amount = $loan_balance * ($percentage_rate / 100);
            }else if ($percentage_on == 2){
                //On loan amount
                $amount = $loan_amount * ($percentage_rate / 100);
            }else if ($percentage_on == 3){
                //On outstanding balance
                $amount = $outstanding_balance * ($percentage_rate / 100);
            }
        }
        
        return $amount;
    }

    function outstanding_loan_balance_fine_fixer($loan=array(),$is_a_debtor=FALSE,$do_return_amount = 0,$date=0){
        if($loan){
            if($is_a_debtor){
                $loan_id = $loan->id;
                if($loan->enable_outstanding_loan_balance_fines){
                    $current_date = $loan->loan_end_date; 
                    $days = (strtotime(date('d-M-Y',time())) - strtotime(date('d-M-Y',$current_date)))/(24*60*60)+1;
                    $next_fine_date = $loan->loan_end_date;
                    $x=0;
                    for($i=1;$i<=$days;$i++){
                        if(date('dmY',$current_date)==date('dmY',$next_fine_date) && $loan->outstanding_loan_balance_fine_date!=0){
                            if(!$this->ci->debtors_m->outstanding_loan_balance_fine_invoice_exists_fixer($loan->id,$current_date,$loan_id)){
                                $amt = $this->get_outstanding_loan_balance_fine_payable(
                                    $loan->debtor_id,
                                    $loan->id,
                                    $loan->loan_amount,
                                    0,
                                    0,
                                    $is_a_debtor);
                                    $amt = (float) str_replace(',', '', number_format($amt, 2));
                                    $fine = array(
                                        'fine_parent_loan_invoice_id' => 0,
                                        'invoice_no' => $this->ci->debtors_m->calculate_invoice_no($loan->group_id),
                                        'debtor_id' => $loan->debtor_id,
                                        'debtor_loan_id' => $loan_id,
                                        'type' => 3,
                                        'interest_amount_payable' => 0,
                                        'principle_amount_payable' => 0,
                                        'invoice_date' => $current_date,
                                        'due_date' => $current_date,
                                        'fine_date' => 0,
                                        'amount_payable' => $amt,
                                        'amount_paid' => 0,
                                        'status' => 1,
                                        'active' => 1,
                                        'is_sent' => 1,
                                        'group_id' => $loan->group_id,
                                        'created_on' => time(),
                                        'created_by' => 1,
                                    );
                                    if($amt>0){   
                                        //insert int db
                                        $fine_id = $this->ci->debtors_m->insert_loan_invoice($fine);
                                        if ($fine_id)
                                        {
                                            $statement = array(
                                                'debtor_loan_id' => $loan_id,
                                                'debtor_id' => $loan->debtor_id,
                                                'transaction_type' => 3,
                                                'debtor_loan_invoice_id' => $fine_id,
                                                'transaction_date' => $current_date,
                                                'amount' => $amt,
                                                'active' => 1,
                                                'balance' => 0,
                                                'group_id' => $loan->group_id,
                                                'created_on' => time(),
                                                'created_by' => 1,
                                            );
                                            $statement_id = $this->ci->debtors_m->insert_loan_statement($statement);
                                        }
                                    }
                            }
                            $next_fine_date = $this->_next_outstanding_fine_date_fixer($loan->id,$current_date,$is_a_debtor);
                        }/*else{
                            continue;
                        }*/
                        $current_date+=(24*60*60);
                    }
                    $this->ci->debtors_m->update_loan($loan_id,array('outstanding_loan_balance_fine_date'=>$next_fine_date,'modified_on'=>time(),));

                    return TRUE;
                }else{
                    return TRUE;
                }
            }else{
                $return_amount = 0;
                $return_fine_id = 0;
                $loan_id = $loan->id;
                if($loan->enable_outstanding_loan_balance_fines){
                    if($do_return_amount){
                        $current_date = $date;
                    }else{
                        $current_date = $loan->loan_end_date; 
                    }
                    $days = (strtotime(date('d-M-Y',time())) - strtotime(date('d-M-Y',$current_date)))/(24*60*60)+1;
                    $next_fine_date = $loan->loan_end_date;
                    $x=0;
                    for($i=1;$i<=$days;$i++){
                        if((date('Ymd',$current_date)==date('Ymd',$next_fine_date) && $loan->outstanding_loan_balance_fine_date!=0) || $do_return_amount == 1){
                            if($this->ci->loan_invoices_m->outstanding_loan_balance_fine_invoice_exists_fixer($loan->id,$current_date,$loan_id) == 0){
                                    $amt = $this->get_outstanding_loan_balance_fine_payable($loan->member_id,$loan->id,$loan->loan_amount, 0, 0,0,$current_date);
                                    $amt = (float) str_replace(',', '', number_format($amt, 2));
                                    if($amt>0){
                                        $fine = array(
                                            'fine_parent_loan_invoice_id' => 0,
                                            'invoice_no' => time(),
                                            'member_id' => $loan->member_id,
                                            'loan_id' => $loan->id,
                                            'type' => 3,
                                            'interest_amount_payable' => 0,
                                            'principle_amount_payable' => 0,
                                            'invoice_date' => $current_date,
                                            'due_date' => $current_date,
                                            'fine_date' => 0,
                                            'amount_payable' => $amt,
                                            'amount_paid' => 0,
                                            'status' => 1,
                                            'active' => 1,
                                            'is_sent' => 1,
                                            'group_id' => $loan->group_id,
                                            'created_on' => time(),
                                            'created_by' => 1,
                                        );
                                        $fine_id = $this->ci->loan_invoices_m->insert($fine);
                                        if ($fine_id){
                                            $statement = array(
                                                'loan_id' => $loan->id,
                                                'member_id' => $loan->member_id,
                                                'transaction_type' => 3,
                                                'loan_invoice_id' => $fine_id,
                                                'transaction_date' => $current_date,
                                                'amount' => $amt,
                                                'active' => 1,
                                                'balance' => 0,
                                                'group_id' => $loan->group_id,
                                                'created_on' => time(),
                                                'created_by' => 1,
                                            );
                                            $statement_id = $this->ci->loans_m->insert_loan_statement($statement);
                                            if(date('dmY',$date) == date('dmY',$current_date)){
                                                $return_amount = $amt;
                                                $return_fine_id = $fine_id;
                                            }
                                        }
                                    }
                            }
                            $next_fine_date = $this->_next_outstanding_fine_date_fixer($loan->id,$current_date);
                        }
                        $current_date+=(24*60*60);
                    }
                    $this->ci->loans_m->update($loan_id,array('outstanding_loan_balance_fine_date'=>$next_fine_date,'modified_on'=>time(),));
                    if($do_return_amount){
                        $post = new StdClass();
                        $post->amount = $return_amount;
                        $post->next_fine_date = $next_fine_date;
                        $post->fine_id = $return_fine_id;
                        return $post;
                    }else{
                        return TRUE;
                    }
                }else{
                    return TRUE;
                }
            }
        }else{
            $this->ci->session->set_flashdata('error','The loan Id Is empty');
            return FALSE;
        }
        
    }

    public function get_outstanding_loan_balance_fine_payable($member_id,$loan_id,$loan_amount,$invoice_id = 0,$type = 0,$is_a_debtor=FALSE,$current_date=0){
        $amount_payable = 0;
        if($is_a_debtor){
            $debtor_id = $member_id;
            $fine = $this->ci->debtors_m->get_loan($loan_id);
            if ($fine->outstanding_loan_balance_fine_type == 1){
                $amount_payable = $fine->outstanding_loan_balance_fine_fixed_amount;
            }else if ($fine->outstanding_loan_balance_fine_type == 2){
                $outstanding_balance = $this->ci->debtors_m->get_outstanding_balance($debtor_id,$loan_id);
                $loan_balance = $this->ci->debtors_m->get_outstanding_loan_balance($debtor_id, $loan_id);

                $amount_payable = $this->_get_fine_percentage(
                    $fine->outstanding_loan_balance_percentage_fine_rate, 
                    $fine->outstanding_loan_balance_percentage_fine_on, 
                    $outstanding_balance, 
                    $loan_balance, 
                    $invoice_id, 
                    $loan_amount,
                    '',
                    '',
                    $is_a_debtor);
            }
            else if ($fine->outstanding_loan_balance_fine_type == 3){
                $amount_payable = $fine->outstanding_loan_balance_fine_one_off_amount;
            }
        }else{
            $fine = $this->ci->loan_invoices_m->fetch_loan($loan_id,$type);
            $amount_payable = 0;
            if ($fine->outstanding_loan_balance_fine_type == 1){
                $amount_payable = $fine->outstanding_loan_balance_fine_fixed_amount;
            }else if ($fine->outstanding_loan_balance_fine_type == 2){
                $outstanding_balance = 0;
                $loan_balance = 0;
                if($fine->outstanding_loan_balance_percentage_fine_on == 1){
                    $loan_balance = $this->ci->loan_invoices_m->get_outstanding_loan_balance($member_id, $loan_id,$current_date);
                }elseif ($fine->outstanding_loan_balance_percentage_fine_on == 2) {
                    $loan_balance = $this->ci->loan_invoices_m->get_outstanding_balance($member_id,$loan_id,$current_date);
                    if($loan_balance > 0){

                    }else{
                        $loan_amount = 0;
                    }
                }elseif ($fine->outstanding_loan_balance_percentage_fine_on == 3) {
                    $outstanding_balance = $this->ci->loan_invoices_m->get_outstanding_balance($member_id,$loan_id,$current_date);
                }
                $amount_payable = $this->_get_fine_percentage($fine->outstanding_loan_balance_percentage_fine_rate, $fine->outstanding_loan_balance_percentage_fine_on, $outstanding_balance, $loan_balance, $invoice_id, $loan_amount,0,$current_date);
            }
            else if ($fine->outstanding_loan_balance_fine_type == 3){
                $amount_payable = $fine->outstanding_loan_balance_fine_one_off_amount;
            }
        }
        return $amount_payable;
    }

    public function calculate_and_record_loan_processing_fee($loan_id=0,$is_a_loan = FALSE,$is_a_debtor=FALSE,$transaction_alert_id = 0,$transaction_alert_amount = 0){
        if($loan_id){
            if($is_a_debtor){
                $loan = $this->ci->debtors_m->get_loan($loan_id);
                if($loan->enable_loan_processing_fee){
                    $amount = 0;
                    if($loan->loan_processing_fee_type==1){
                        $amount = $loan->loan_processing_fee_fixed_amount;
                    }else if($loan->loan_processing_fee_type==2){
                        if($loan->loan_processing_fee_percentage_charged_on==1){
                            $amount = ($loan->loan_processing_fee_percentage_rate/100)*$loan->loan_amount;
                        }
                        else if($loan->loan_processing_fee_percentage_charged_on==2){
                            $interest_principle_amount = $this->ci->debtors_m->get_loans_interest_and_principle_amount($loan_id);
                            $amount = ($loan->loan_processing_fee_percentage_rate/100)*$interest_principle_amount;                        
                        }
                    }
                    if($this->ci->transactions->record_external_lending_processing_income_deposit($loan->group_id,$loan->id,$loan->disbursement_date,$loan->debtor_id,$loan->account_id,1,'External Lending Loan Processing Income',$amount)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }
                else{
                    //$this->ci->session->set_flashdata('error','Sorry, the loan is not activated for loan processing');
                    return TRUE;
                }
            }else{
                $loan = $this->ci->loans_m->get($loan_id);
                if($loan->enable_loan_processing_fee){
                    $amount = 0;
                    $is_admin_loan_type = FALSE;
                    if($loan->loan_processing_fee_type==1)
                    {
                        $amount = $loan->loan_processing_fee_fixed_amount;
                    }
                    else if($loan->loan_processing_fee_type==2)
                    {
                        if($loan->loan_processing_fee_percentage_charged_on==1)
                        {
                            $amount = ($loan->loan_processing_fee_percentage_rate/100)*$loan->loan_amount;
                        }
                        else if($loan->loan_processing_fee_percentage_charged_on==2)
                        {
                            $interest_principle_amount = $this->ci->loans_m->get_loans_interest_and_principle_amount($loan_id);
                            $amount = ($loan->loan_processing_fee_percentage_rate/100)*$interest_principle_amount;                        
                        }
                    }
                    if($transaction_alert_amount&&$transaction_alert_id){
                        $amount = $transaction_alert_amount;
                    }
                    if($loan->loan_type_id){
                        $loan_type = $this->ci->loan_types_m->get();
                        if($loan_type){
                            if($loan_type->is_admin){
                                $is_admin_loan_type = TRUE;
                            }
                        }
                    }
                    $result = $this->ci->transactions->record_loan_processing_income_deposit($loan->group_id,$loan->id,$loan->disbursement_date,$loan->member_id,$loan->account_id,1,'Amount from loan processing',$amount,$transaction_alert_id,$is_admin_loan_type);
                    if($result){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }
                else{
                    // $this->ci->session->set_flashdata('warning','Sorry, the loan is not activated for loan processing');
                    return TRUE;
                }
            }
            
        }else{
            // $this->ci->session->set_flashdata('error','Sorry, Loan ID was not passed to calculate processing Fee');
            return FALSE;
        }
    }
    public function calculate_loan_processing_fee($loan_type_id=0,$is_a_loan = FALSE,$is_a_debtor=FALSE,$loan_amount,$interest_principle_amount){
        if($loan_type_id){
            if($is_a_debtor){
                $loan = $this->ci->debtors_m->get_loan($loan_id);
                if($loan->enable_loan_processing_fee){
                    $amount = 0;
                    if($loan->loan_processing_fee_type==1){
                        $amount = $loan->loan_processing_fee_fixed_amount;
                    }else if($loan->loan_processing_fee_type==2){
                        if($loan->loan_processing_fee_percentage_charged_on==1){
                            $amount = ($loan->loan_processing_fee_percentage_rate/100)*$loan->loan_amount;
                        }
                        else if($loan->loan_processing_fee_percentage_charged_on==2){
                            $interest_principle_amount = $this->ci->debtors_m->get_loans_interest_and_principle_amount($loan_id);
                            $amount = ($loan->loan_processing_fee_percentage_rate/100)*$interest_principle_amount;                        
                        }
                    }
                    if($this->ci->transactions->record_external_lending_processing_income_deposit($loan->group_id,$loan->id,$loan->disbursement_date,$loan->debtor_id,$loan->account_id,1,'External Lending Loan Processing Income',$amount)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }
                else{
                    //$this->ci->session->set_flashdata('error','Sorry, the loan is not activated for loan processing');
                    return TRUE;
                }
            }else{
                $loan = $this->ci->loan_types_m->get($loan_type_id);
                if($loan->enable_loan_processing_fee){
                    $amount = 0;
                    $is_admin_loan_type = FALSE;
                    if($loan->loan_processing_fee_type==1)
                    {
                        $amount = $loan->loan_processing_fee_fixed_amount;
                    }
                    else if($loan->loan_processing_fee_type==2)
                    {
                        if($loan->loan_processing_fee_percentage_charged_on==1)
                        {
                         
                            $amount = ($loan->loan_processing_fee_percentage_rate/100)*$loan_amount;
                        }
                        else if($loan->loan_processing_fee_percentage_charged_on==2)
                        {
                       
                            // $amount = ($loan->loan_processing_fee_percentage_rate/100)*$interest_principle_amount;                        
                            $amount = ($loan->loan_processing_fee_percentage_rate/100)*$loan_amount;                        
                        }
                    }
                  
                    if($amount){
                        return $amount;
                    }else{
                        return FALSE;
                    }
                }
                else{
                    // $this->ci->session->set_flashdata('error','Sorry, the loan is not activated for loan processing');
                    return TRUE;
                }
            }
            
        }else{
            // $this->ci->session->set_flashdata('error','Sorry, Loan ID was not passed to calculate processing Fee');
            return FALSE;
        }
    }
    // function pay_edited_invoices($loan_id,$reset=FALSE,$is_a_debtor=FALSE){
    //     if($loan_id){
    //         if($is_a_debtor){                
    //             $loan = $this->ci->debtors_m->get_loan($loan_id);
    //             if($this->ci->debtors_m->void_loan_payment_statement($loan->id)){
    //                 $payments = $this->ci->debtors_m->get_loan_repayments($loan->id);
    //                 if($payments){
    //                     $debtor_ids = array();
    //                     $group_ids = array();
    //                     $transaction_dates = array();
    //                     $transaction_types = array();
    //                     $payment_methods = array();
    //                     $loan_ids = array();
    //                     $loan_payment_ids = array();
    //                     $account_ids = array();
    //                     $amounts = array();
    //                     $balances = array();
    //                     $actives = array();
    //                     $statuss = array();
    //                     $created_ons = array();
    //                     $is_a_back_dating_records = array();
    //                     foreach ($payments as $payment) {
    //                         $debtor_ids[] = $payment->debtor_id;
    //                         $group_ids[] = $payment->group_id;
    //                         $transaction_dates[] = $payment->receipt_date;
    //                         $transaction_types[] = 4;
    //                         $payment_methods[] = $payment->payment_method;
    //                         $loan_ids[] = $payment->debtor_loan_id;
    //                         $loan_payment_ids[] = $payment->id;
    //                         $account_ids[] = $payment->account_id;
    //                         $amounts[] = $payment->amount;
    //                         $balances[] = 0;
    //                         $actives[] = $payment->active;
    //                         $statuss[] = $payment->status;
    //                         $created_ons[] = time();
    //                         $is_a_back_dating_records[] = $payment->is_a_back_dating_record;
    //                     }

    //                     if($debtor_ids){
    //                         $input = array(
    //                             'debtor_id' =>  $debtor_ids,
    //                             'group_id'  =>  $group_ids,
    //                             'transaction_date' =>   $transaction_dates,
    //                             'transaction_type'  =>  $transaction_types,
    //                             'payment_method'    =>  $payment_methods,
    //                             'debtor_loan_id'   =>  $loan_ids,
    //                             'debtor_loan_payment_id'   =>  $loan_payment_ids,
    //                             'account_id'    =>  $account_ids,
    //                             'amount'        =>  $amounts,
    //                             'balance'       =>  $balances,
    //                             'active'        =>  $actives,
    //                             'status'        =>  $statuss,
    //                             'created_on'    =>  $created_ons,
    //                             'is_a_back_dating_record'=>  $is_a_back_dating_records,
    //                         );
    //                         $this->ci->debtors_m->insert_batch_statements($input);
    //                     }
    //                 }
    //             }
    //             $statement_entries = $this->ci->debtors_m->get_loan_statement_for_library_for_payments($loan_id);
    //             $date_array = array();
    //             foreach ($statement_entries as $statement_entry) {
    //                 if($statement_entry->transaction_date){
    //                     $date = date('dmY',$statement_entry->transaction_date);
    //                     if(array_key_exists($date, $date_array)){
    //                         $old_amount = $date_array[$date]['amount'];
    //                         $date_array[$date] = array(
    //                             'amount' => ($statement_entry->amount+$old_amount),
    //                             'transaction_date' => strtotime(date('d-m-Y',$statement_entry->transaction_date)),
    //                             'transaction_date2' => (date('d-m-Y',$statement_entry->transaction_date)),
    //                         );
    //                     }else{
    //                         $date_array[$date] = array(
    //                             'amount' => $statement_entry->amount,
    //                             'transaction_date' => strtotime(date('d-m-Y',$statement_entry->transaction_date)),
    //                             'transaction_dat2' => (date('d-m-Y',$statement_entry->transaction_date)),
    //                         );
    //                     }
    //                 }
    //             }
    //             if($date_array){
    //                 unset($statement_entries);
    //                 foreach ($date_array as $date_key => $date_value) {
    //                     $statement_entries[] = (object)$date_value;
    //                 }
    //             }
    //             foreach ($statement_entries as $statement_entry_key=>$statement_entry) {
    //                 $statement_amount = $statement_entry->amount;
    //                 if($statement_amount>0){
    //                     $this->recalculate_reducing_balance($loan,$statement_amount,$statement_entry->transaction_date,TRUE);
    //                     $invoices = $this->ci->debtors_m->get_unpaid_loan_installments($loan_id);
    //                     $parent_id = '';
    //                     $count=0;
    //                     $amount = $statement_amount;
    //                     foreach ($invoices as $invoice){
    //                         if($amount){
    //                             if($invoice->type==1 || $invoice->type==5){
    //                                 if($invoice->status!=2){
    //                                     $amount_paid = $invoice->amount_paid;
    //                                     if($amount_paid<1){
    //                                         $amount_paid = 0;
    //                                     }
    //                                     $amount_payable = $invoice->amount_payable-$amount_paid;
    //                                     if($amount_payable<1 && $amount_payable>=0){
    //                                         $status=2;
    //                                         $amount_paid = $amount_paid;
    //                                         if(date('ymd',$statement_entry->transaction_date)){
    //                                             if($this->ci->debtors_m->void_children_invoices_fined_wrongly_after_date($invoice->id,$statement_entry->transaction_date)){
    //                                                 $parent_id = $invoice->id;
    //                                             }
    //                                         }
    //                                     }else{
    //                                         if((round($amount,1)>=round($amount_payable,0)) || (round($amount,0)>=round($amount_payable,0)) ){
    //                                             $amount = $amount-$amount_payable;
    //                                             if($amount<1){
    //                                                 $amount = 0;
    //                                             }
    //                                             $status = 2;
    //                                             $amount_paid = $invoice->amount_payable;
    //                                             if(date('ymd',$statement_entry->transaction_date)){
    //                                                 if($this->ci->debtors_m->void_children_invoices_fined_wrongly_after_date($invoice->id,$statement_entry->transaction_date)){
    //                                                     $parent_id = $invoice->id;
    //                                                 }
    //                                             }
    //                                         }else{
    //                                             $status = 1;
    //                                             $amount_paid = $amount+$amount_paid;
    //                                             $amount = 0;
    //                                         }
    //                                     }
    //                                     $update_invoice = array(
    //                                             'status'=>$status,
    //                                             'amount_paid'=>$amount_paid,
    //                                             'modified_on'=>time(),
    //                                         );
    //                                     $this->ci->debtors_m->update_loan_invoices($invoice->id,$update_invoice);
    //                                 }else{
    //                                     continue;
    //                                 }
    //                             }else if(isset($parent_id) && $invoice->fine_parent_loan_invoice_id!=$parent_id){
    //                                 if($invoice->active){
    //                                     if($loan->enable_loan_fine_deferment){
    //                                         if($this->ci->debtors_m->count_all_loan_invoices($loan_id)==0){
    //                                             //all payments were made
    //                                             if($invoice->status!=2)
    //                                             {
    //                                                 $amount_paid = $invoice->amount_paid;
    //                                                 $amount_payable = $invoice->amount_payable-$amount_paid;
    //                                                 if($amount_payable<1){
    //                                                     $status =2;
    //                                                     $amount_paid = $amount_paid;
    //                                                     if(date('ymd',$invoice->due_date)>=date('ymd',$statement_entry->transaction_date))
    //                                                     {
    //                                                         //check if this invoice has fines
    //                                                         if($this->ci->debtors_m->void_children_invoices_fined_wrongly($invoice->id)){
    //                                                             $parent_id = $invoice->id;
    //                                                         }
    //                                                     }
    //                                                 }else 
    //                                                 {
    //                                                     if(round($amount,1)>=round($amount_payable,1)){
    //                                                         $amount = $amount-$amount_payable;
    //                                                         $status =2;
    //                                                         $amount_paid = $invoice->amount_payable;
    //                                                         if(date('ymd',$invoice->due_date)>=date('ymd',$statement_entry->transaction_date))
    //                                                         {
    //                                                             //check if this invoice has fines
    //                                                             if($this->ci->debtors_m->void_children_invoices_fined_wrongly($invoice->id)){
    //                                                                $parent_id = $invoice->id; 
    //                                                             }
    //                                                         }
    //                                                     }
    //                                                     else{
    //                                                         $status = 1;
    //                                                         $amount_paid = $amount+$amount_paid;
    //                                                         $amount = 0;
    //                                                     }
    //                                                 }
    //                                                $this->ci->debtors_m->update_loan_invoices($invoice->id,array('status'=>$status,'amount_paid'=>$amount_paid,'modified_on'=>time(),));
    //                                             }else{
    //                                                 continue;
    //                                             }
    //                                         }else{
    //                                             continue;
    //                                         }
    //                                     }else{
    //                                         if($invoice->status!=2){
    //                                             $amount_paid = $invoice->amount_paid;
    //                                             $amount_payable = $invoice->amount_payable-$amount_paid;
    //                                             if($amount_payable<1){
    //                                                 $status =2;
    //                                                 $amount_paid = $amount_paid;
    //                                                 if(date('ymd',$statement_entry->transaction_date)){
    //                                                     //check if this invoice has fines
    //                                                     if($this->ci->debtors_m->void_children_invoices_fined_wrongly_after_date($invoice->id,$statement_entry->transaction_date)){
    //                                                         $parent_id = $invoice->id;
    //                                                     }
    //                                                 }
    //                                             }else{
    //                                                 if(round($amount,1)>=round($amount_payable,1)){
    //                                                     $amount = $amount-$amount_payable;
    //                                                     $status =2;
    //                                                     if(date('ymd',$statement_entry->transaction_date)){
    //                                                         //check if this invoice has fines
    //                                                         if($this->ci->debtors_m->void_children_invoices_fined_wrongly_after_date($invoice->id,$statement_entry->transaction_date)){
    //                                                             $parent_id = $invoice->id;
    //                                                         }
    //                                                     }
    //                                                     $amount_paid = $invoice->amount_payable;
    //                                                 }
    //                                                 else{
    //                                                     $status = 1;
    //                                                     $amount_paid = $amount+$amount_paid;
    //                                                      $amount = 0;
    //                                                 }
    //                                             }
    //                                             $this->ci->debtors_m->update_loan_invoices($invoice->id,array('status'=>$status,'amount_paid'=>$amount_paid));
    //                                         }else{
    //                                             continue;
    //                                         }
    //                                     }
    //                                 }
    //                                 else{
    //                                     continue;
    //                                 }
    //                             }
    //                         }else{
    //                             //break;
    //                         }
    //                         if($invoice->type == 3){
    //                             $balance = $this->ci->debtors_m->get_outstanding_balance($loan->debtor_id, $loan->id,$statement_entry->transaction_date);
    //                             if($balance<=1){
    //                                 if($this->ci->debtors_m->void_future_outstanding_loan_invoices($loan->id,$statement_entry->transaction_date)){
    //                                 }
    //                             }
    //                         }
    //                     }
    //                     $this->_update_all_invoices($loan_id,TRUE);
    //                 }else{
    //                     break;
    //                 }
    //             }
    //             if($reset ==FALSE){
    //                 $invoices = $this->ci->debtors_m->get_loan_installments($loan_id);
    //                 if($invoices){
    //                     foreach ($invoices as $invoice) {
    //                         $this->ci->debtors_m->update_loan_invoices($invoice->id,array('status'=>1,'amount_paid'=>0,'modified_on'=>time()));
    //                     }
    //                 }
    //                 $this->pay_edited_invoices($loan_id,TRUE,TRUE);
    //             }
    //             return TRUE;
    //         }else{
    //             $loan = $this->ci->loans_m->get($loan_id);
    //             if($this->ci->loans_m->void_loan_payment_statement($loan->id)){
    //                 $payments = $this->ci->loan_repayments_m->get_loan_repayments($loan->id);
    //                 if($payments){
    //                     $member_ids = array();
    //                     $group_ids = array();
    //                     $transaction_dates = array();
    //                     $transaction_types = array();
    //                     $payment_methods = array();
    //                     $loan_ids = array();
    //                     $loan_payment_ids = array();
    //                     $account_ids = array();
    //                     $amounts = array();
    //                     $balances = array();
    //                     $actives = array();
    //                     $statuss = array();
    //                     $created_ons = array();
    //                     $is_a_back_dating_records = array();
    //                     foreach ($payments as $payment) {
    //                         $member_ids[] = $payment->member_id;
    //                         $group_ids[] = $payment->group_id;
    //                         $transaction_dates[] = $payment->receipt_date;
    //                         $transaction_types[] = 4;
    //                         $payment_methods[] = $payment->payment_method;
    //                         $loan_ids[] = $payment->loan_id;
    //                         $loan_payment_ids[] = $payment->id;
    //                         $account_ids[] = $payment->account_id;
    //                         $amounts[] = $payment->amount;
    //                         $balances[] = 0;
    //                         $actives[] = $payment->active;
    //                         $statuss[] = $payment->status;
    //                         $created_ons[] = time();
    //                         $is_a_back_dating_records[] = $payment->is_a_back_dating_record;
    //                     }

    //                     if($member_ids){
    //                         $input = array(
    //                             'member_id' =>  $member_ids,
    //                             'group_id'  =>  $group_ids,
    //                             'transaction_date' =>   $transaction_dates,
    //                             'transaction_type'  =>  $transaction_types,
    //                             'payment_method'    =>  $payment_methods,
    //                             'loan_id'   =>  $loan_ids,
    //                             'loan_payment_id'   =>  $loan_payment_ids,
    //                             'account_id'    =>  $account_ids,
    //                             'amount'        =>  $amounts,
    //                             'balance'       =>  $balances,
    //                             'active'        =>  $actives,
    //                             'status'        =>  $statuss,
    //                             'created_on'    =>  $created_ons,
    //                             'is_a_back_dating_record'=>  $is_a_back_dating_records,
    //                         );
    //                         $this->ci->loans_m->insert_batch_statements($input);
    //                     }
    //                 }
    //             }
    //             $statement_entries = $this->ci->loans_m->get_loan_statement_for_library_for_payments($loan_id);
    //             $date_array = array();
    //             foreach ($statement_entries as $statement_entry) {
    //                 if($statement_entry->transaction_date){
    //                     $date = date('dmY',$statement_entry->transaction_date);
    //                     if(array_key_exists($date, $date_array)){
    //                         $old_amount = $date_array[$date]['amount'];
    //                         $date_array[$date] = array(
    //                             'amount' => ($statement_entry->amount+$old_amount),
    //                             'transaction_date' => strtotime(date('d-m-Y',$statement_entry->transaction_date)),
    //                             'transaction_date2' => (date('d-m-Y',$statement_entry->transaction_date)),
    //                         );
    //                     }else{
    //                         $date_array[$date] = array(
    //                             'amount' => $statement_entry->amount,
    //                             'transaction_date' => strtotime(date('d-m-Y',$statement_entry->transaction_date)),
    //                             'transaction_dat2' => (date('d-m-Y',$statement_entry->transaction_date)),
    //                         );
    //                     }
    //                 }
    //             }
    //             if($date_array){
    //                 unset($statement_entries);
    //                 foreach ($date_array as $date_key => $date_value) {
    //                     $statement_entries[] = (object)$date_value;
    //                 }
    //             }
    //             foreach ($statement_entries as $statement_entry_key=>$statement_entry) {
    //                 $statement_amount = $statement_entry->amount;
    //                 if($statement_amount>0){
    //                     $this->recalculate_reducing_balance($loan,$statement_amount,$statement_entry->transaction_date);
    //                     $invoices = $this->ci->loan_invoices_m->get_unpaid_loan_installments($loan_id);
    //                     $parent_id = '';
    //                     $count=0;
    //                     $amount = $statement_amount;
    //                     foreach ($invoices as $invoice){
    //                         if($amount){
    //                             if($invoice->type==1 || $invoice->type==5){
    //                                 if($invoice->status!=2){
    //                                     $amount_paid = $invoice->amount_paid;
    //                                     if($amount_paid<1){
    //                                         $amount_paid = 0;
    //                                     }
    //                                     $amount_payable = $invoice->amount_payable-$amount_paid;
    //                                     if($amount_payable<1 && $amount_payable>=0){
    //                                         $status=2;
    //                                         $amount_paid = $amount_paid;
    //                                         if(date('ymd',$statement_entry->transaction_date)){
    //                                             if($this->ci->loan_invoices_m->void_children_invoices_fined_wrongly_after_date($invoice->id,$statement_entry->transaction_date)){
    //                                                 $parent_id = $invoice->id;
    //                                             }
    //                                         }
    //                                     }else{
    //                                         if((round($amount,1)>=round($amount_payable,0)) || (round($amount,0)>=round($amount_payable,0)) ){
    //                                             $amount = $amount-$amount_payable;
    //                                             if($amount<1){
    //                                                 $amount = 0;
    //                                             }
    //                                             $status = 2;
    //                                             $amount_paid = $invoice->amount_payable;
    //                                             if(date('ymd',$statement_entry->transaction_date)){
    //                                                 if($this->ci->loan_invoices_m->void_children_invoices_fined_wrongly_after_date($invoice->id,$statement_entry->transaction_date)){
    //                                                     $parent_id = $invoice->id;
    //                                                 }
    //                                             }
    //                                         }else{
    //                                             $status = 1;
    //                                             $amount_paid = $amount+$amount_paid;
    //                                             $amount = 0;
    //                                         }
    //                                     }
    //                                     $update_invoice = array(
    //                                             'status'=>$status,
    //                                             'amount_paid'=>$amount_paid,
    //                                             'modified_on'=>time(),
    //                                         );
    //                                     $this->ci->loan_invoices_m->update($invoice->id,$update_invoice);
    //                                 }else{
    //                                     continue;
    //                                 }
    //                             }else if(isset($parent_id) && $invoice->fine_parent_loan_invoice_id!=$parent_id){
    //                                 if($invoice->active){
    //                                     if($loan->enable_loan_fine_deferment){
    //                                         if($this->ci->loan_invoices_m->count_all_loan_invoices($loan_id)==0){
    //                                             //all payments were made
    //                                             if($invoice->status!=2)
    //                                             {
    //                                                 $amount_paid = $invoice->amount_paid;
    //                                                 $amount_payable = $invoice->amount_payable-$amount_paid;
    //                                                 if($amount_payable<1){
    //                                                     $status =2;
    //                                                     $amount_paid = $amount_paid;
    //                                                     if(date('ymd',$invoice->due_date)>=date('ymd',$statement_entry->transaction_date))
    //                                                     {
    //                                                         //check if this invoice has fines
    //                                                         if($this->ci->loan_invoices_m->void_children_invoices_fined_wrongly($invoice->id)){
    //                                                             $parent_id = $invoice->id;
    //                                                         }
    //                                                     }
    //                                                 }else 
    //                                                 {
    //                                                     if(round($amount,1)>=round($amount_payable,1)){
    //                                                         $amount = $amount-$amount_payable;
    //                                                         $status =2;
    //                                                         $amount_paid = $invoice->amount_payable;
    //                                                         if(date('ymd',$invoice->due_date)>=date('ymd',$statement_entry->transaction_date))
    //                                                         {
    //                                                             //check if this invoice has fines
    //                                                             if($this->ci->loan_invoices_m->void_children_invoices_fined_wrongly($invoice->id)){
    //                                                                $parent_id = $invoice->id; 
    //                                                             }
    //                                                         }
    //                                                     }
    //                                                     else{
    //                                                         $status = 1;
    //                                                         $amount_paid = $amount+$amount_paid;
    //                                                         $amount = 0;
    //                                                     }
    //                                                 }
    //                                                $this->ci->loan_invoices_m->update($invoice->id,array('status'=>$status,'amount_paid'=>$amount_paid,'modified_on'=>time(),));
    //                                             }else{
    //                                                 continue;
    //                                             }
    //                                         }else{
    //                                             continue;
    //                                         }
    //                                     }else{
    //                                         if($invoice->status!=2){
    //                                             $amount_paid = $invoice->amount_paid;
    //                                             $amount_payable = $invoice->amount_payable-$amount_paid;
    //                                             if($amount_payable<1){
    //                                                 $status =2;
    //                                                 $amount_paid = $amount_paid;
    //                                                 if(date('ymd',$statement_entry->transaction_date)){
    //                                                     //check if this invoice has fines
    //                                                     if($this->ci->loan_invoices_m->void_children_invoices_fined_wrongly_after_date($invoice->id,$statement_entry->transaction_date)){
    //                                                         $parent_id = $invoice->id;
    //                                                     }
    //                                                 }
    //                                             }else{
    //                                                 if(round($amount,1)>=round($amount_payable,1)){
    //                                                     $amount = $amount-$amount_payable;
    //                                                     $status =2;
    //                                                     if(date('ymd',$statement_entry->transaction_date)){
    //                                                         //check if this invoice has fines
    //                                                         if($this->ci->loan_invoices_m->void_children_invoices_fined_wrongly_after_date($invoice->id,$statement_entry->transaction_date)){
    //                                                             $parent_id = $invoice->id;
    //                                                         }
    //                                                     }
    //                                                     $amount_paid = $invoice->amount_payable;
    //                                                 }
    //                                                 else{
    //                                                     $status = 1;
    //                                                     $amount_paid = $amount+$amount_paid;
    //                                                      $amount = 0;
    //                                                 }
    //                                             }
    //                                             $this->ci->loan_invoices_m->update($invoice->id,array('status'=>$status,'amount_paid'=>$amount_paid));
    //                                         }else{
    //                                             continue;
    //                                         }

    //                                     }
    //                                 }
    //                                 else{
    //                                     continue;
    //                                 }
    //                             }
    //                         }else{
    //                             //break;
    //                         }
    //                         if($invoice->type == 3){
    //                             $pending_invoices =  $this->ci->loan_invoices_m->count_unpaid_loan_invoices($loan->id);
    //                             if($pending_invoices <=0){
    //                                 $balance = $this->ci->loan_invoices_m->get_outstanding_balance($loan->member_id, $loan->id,$statement_entry->transaction_date);
    //                                 if($balance<=1){
    //                                     if($this->ci->loan_invoices_m->void_future_outstanding_loan_invoices($loan->id,$statement_entry->transaction_date)){
    //                                     }
    //                                 }
    //                             }
    //                         }
    //                     }
    //                     $this->_update_all_invoices($loan_id);
    //                 }else{
    //                     break;
    //                 }
    //             }
    //             if($reset ==FALSE){
    //                 $invoices = $this->ci->loan_invoices_m->get_loan_installments($loan_id);
    //                 if($invoices){
    //                     foreach ($invoices as $invoice) {
    //                         $this->ci->loan_invoices_m->update($invoice->id,array('status'=>1,'amount_paid'=>0,'modified_on'=>time()));
    //                     }
    //                 }
    //                 $this->pay_edited_invoices($loan_id,TRUE);
    //             }
    //             return TRUE;
    //         }
    //     }else{
    //         $this->ci->session->set_flashdata('error','There are some paramenters missing');
    //         return FALSE;
    //     }
    // }

    function pay_edited_invoices($loan='',$reset=FALSE,$is_a_debtor=FALSE){
        if($is_a_debtor){
            if($this->ci->debtors_m->void_loan_payment_statement($loan->id,$loan->group_id)){
                $payments = $this->ci->debtors_m->get_loan_repayments($loan->id,'','',$loan->group_id);
                if($payments){
                    $debtor_ids = array();
                    $group_ids = array();
                    $transaction_dates = array();
                    $transaction_types = array();
                    $payment_methods = array();
                    $loan_ids = array();
                    $loan_payment_ids = array();
                    $account_ids = array();
                    $amounts = array();
                    $balances = array();
                    $actives = array();
                    $statuss = array();
                    $created_ons = array();
                    $is_a_back_dating_records = array();
                    foreach ($payments as $payment) {
                        $debtor_ids[] = $payment->debtor_id;
                        $group_ids[] = $payment->group_id;
                        $transaction_dates[] = $payment->receipt_date;
                        $transaction_types[] = 4;
                        $payment_methods[] = $payment->payment_method;
                        $loan_ids[] = $payment->debtor_loan_id;
                        $loan_payment_ids[] = $payment->id;
                        $account_ids[] = $payment->account_id;
                        $amounts[] = $payment->amount;
                        $balances[] = 0;
                        $actives[] = $payment->active;
                        $statuss[] = $payment->status;
                        $created_ons[] = time();
                        $is_a_back_dating_records[] = $payment->is_a_back_dating_record;
                    }

                    if($debtor_ids){
                        $input = array(
                            'debtor_id' =>  $debtor_ids,
                            'group_id'  =>  $group_ids,
                            'transaction_date' =>   $transaction_dates,
                            'transaction_type'  =>  $transaction_types,
                            'payment_method'    =>  $payment_methods,
                            'debtor_loan_id'   =>  $loan_ids,
                            'debtor_loan_payment_id'   =>  $loan_payment_ids,
                            'account_id'    =>  $account_ids,
                            'amount'        =>  $amounts,
                            'balance'       =>  $balances,
                            'active'        =>  $actives,
                            'status'        =>  $statuss,
                            'created_on'    =>  $created_ons,
                            'is_a_back_dating_record'=>  $is_a_back_dating_records,
                        );
                        $this->ci->debtors_m->insert_batch_statements($input);
                    }
                }
            }
            $statement_entries = $this->ci->debtors_m->get_loan_statement_for_library_for_payments($loan->id,$loan->group_id);
            $date_array = array();
            foreach ($statement_entries as $statement_entry) {
                if($statement_entry->transaction_date){
                    $date = date('dmY',$statement_entry->transaction_date);
                    if(array_key_exists($date, $date_array)){
                        $old_amount = $date_array[$date]['amount'];
                        $date_array[$date] = array(
                            'amount' => ($statement_entry->amount+$old_amount),
                            'transaction_date' => strtotime(date('d-m-Y',$statement_entry->transaction_date)),
                            'transaction_date2' => (date('d-m-Y',$statement_entry->transaction_date)),
                        );
                    }else{
                        $date_array[$date] = array(
                            'amount' => $statement_entry->amount,
                            'transaction_date' => strtotime(date('d-m-Y',$statement_entry->transaction_date)),
                            'transaction_dat2' => (date('d-m-Y',$statement_entry->transaction_date)),
                        );
                    }
                }
            }
            if($date_array){
                unset($statement_entries);
                foreach ($date_array as $date_key => $date_value) {
                    $statement_entries[] = (object)$date_value;
                }
            }
            foreach ($statement_entries as $statement_entry_key=>$statement_entry) {
                $statement_amount = $statement_entry->amount;
                if($statement_amount>0){
                    $this->recalculate_reducing_balance($loan,$statement_amount,$statement_entry->transaction_date,TRUE);
                    $invoices = $this->ci->debtors_m->get_unpaid_loan_installments($loan->id,$loan->group_id);
                    $parent_id = '';
                    $count=0;
                    $amount = $statement_amount;
                    foreach ($invoices as $invoice){
                        if($amount){
                            if($invoice->type==1 || $invoice->type==5){
                                if($invoice->status!=2){
                                    $amount_paid = $invoice->amount_paid;
                                    if($amount_paid<1){
                                        $amount_paid = 0;
                                    }
                                    $amount_payable = $invoice->amount_payable-$amount_paid;
                                    if($amount_payable<1 && $amount_payable>=0){
                                        $status=2;
                                        $amount_paid = $amount_paid;
                                        if(date('ymd',$statement_entry->transaction_date)){
                                            if($this->ci->debtors_m->void_children_invoices_fined_wrongly_after_date($invoice->id,$statement_entry->transaction_date,$loan->group_id)){
                                                $parent_id = $invoice->id;
                                            }
                                        }
                                    }else{
                                        if((round($amount,1)>=floor($amount_payable)) || (floor($amount)>=floor($amount_payable)) ){
                                            $amount = $amount-$amount_payable;
                                            if($amount<1){
                                                $amount = 0;
                                            }
                                            $status = 2;
                                            $amount_paid = $invoice->amount_payable;
                                            if(date('ymd',$statement_entry->transaction_date)){
                                                if($this->ci->debtors_m->void_children_invoices_fined_wrongly_after_date($invoice->id,$statement_entry->transaction_date,$loan->group_id)){
                                                    $parent_id = $invoice->id;
                                                }
                                            }
                                        }else{
                                            $status = 1;
                                            $amount_paid = $amount+$amount_paid;
                                            $amount = 0;
                                        }
                                    }
                                    $update_invoice = array(
                                            'status'=>$status,
                                            'amount_paid'=>$amount_paid,
                                            'modified_on'=>time(),
                                        );
                                    $this->ci->debtors_m->update_loan_invoices($invoice->id,$update_invoice);
                                }else{
                                    continue;
                                }
                            }else if(isset($parent_id) && $invoice->fine_parent_loan_invoice_id!=$parent_id){
                                if($invoice->active){
                                    if($loan->enable_loan_fine_deferment){
                                        if($this->ci->debtors_m->count_all_loan_invoices($loan->id,$loan->group_id)==0){
                                            //all payments were made
                                            if($invoice->status!=2)
                                            {
                                                $amount_paid = $invoice->amount_paid;
                                                $amount_payable = $invoice->amount_payable-$amount_paid;
                                                if($amount_payable<1){
                                                    $status =2;
                                                    $amount_paid = $amount_paid;
                                                    if(date('ymd',$invoice->due_date)>=date('ymd',$statement_entry->transaction_date))
                                                    {
                                                        //check if this invoice has fines
                                                        if($this->ci->debtors_m->void_children_invoices_fined_wrongly($invoice->id,$loan->group_id)){
                                                            $parent_id = $invoice->id;
                                                        }
                                                    }
                                                }else 
                                                {
                                                    if(round($amount,1)>=round($amount_payable,1)){
                                                        $amount = $amount-$amount_payable;
                                                        $status =2;
                                                        $amount_paid = $invoice->amount_payable;
                                                        if(date('ymd',$invoice->due_date)>=date('ymd',$statement_entry->transaction_date))
                                                        {
                                                            //check if this invoice has fines
                                                            if($this->ci->debtors_m->void_children_invoices_fined_wrongly($invoice->id,$loan->group_id)){
                                                               $parent_id = $invoice->id; 
                                                            }
                                                        }
                                                    }
                                                    else{
                                                        $status = 1;
                                                        $amount_paid = $amount+$amount_paid;
                                                        $amount = 0;
                                                    }
                                                }
                                               $this->ci->debtors_m->update_loan_invoices($invoice->id,array('status'=>$status,'amount_paid'=>$amount_paid,'modified_on'=>time(),));
                                            }else{
                                                continue;
                                            }
                                        }else{
                                            continue;
                                        }
                                    }else{
                                        if($invoice->status!=2){
                                            $amount_paid = $invoice->amount_paid;
                                            $amount_payable = $invoice->amount_payable-$amount_paid;
                                            if($amount_payable<1){
                                                $status =2;
                                                $amount_paid = $amount_paid;
                                                if(date('ymd',$statement_entry->transaction_date)){
                                                    //check if this invoice has fines
                                                    if($this->ci->debtors_m->void_children_invoices_fined_wrongly_after_date($invoice->id,$statement_entry->transaction_date,$loan->group_id)){
                                                        $parent_id = $invoice->id;
                                                    }
                                                }
                                            }else{
                                                if(round($amount,1)>=round($amount_payable,1)){
                                                    $amount = $amount-$amount_payable;
                                                    $status =2;
                                                    if(date('ymd',$statement_entry->transaction_date)){
                                                        //check if this invoice has fines
                                                        if($this->ci->debtors_m->void_children_invoices_fined_wrongly_after_date($invoice->id,$statement_entry->transaction_date,$loan->group_id)){
                                                            $parent_id = $invoice->id;
                                                        }
                                                    }
                                                    $amount_paid = $invoice->amount_payable;
                                                }
                                                else{
                                                    $status = 1;
                                                    $amount_paid = $amount+$amount_paid;
                                                     $amount = 0;
                                                }
                                            }
                                            $this->ci->debtors_m->update_loan_invoices($invoice->id,array('status'=>$status,'amount_paid'=>$amount_paid));
                                        }else{
                                            continue;
                                        }
                                    }
                                }
                                else{
                                    continue;
                                }
                            }
                        }else{
                            //break;
                        }
                        if($invoice->type == 3){
                            $balance = $this->ci->debtors_m->get_outstanding_balance($loan->debtor_id, $loan->id,$statement_entry->transaction_date,$loan->group_id);
                            if($balance<=1){
                                if($this->ci->debtors_m->void_future_outstanding_loan_invoices($loan->id,$statement_entry->transaction_date,$loan->group_id)){
                                }
                            }
                        }
                    }
                    $this->_update_all_invoices($loan->id,TRUE,$loan->group_id);
                }else{
                    break;
                }
            }
            if($reset ==FALSE){
                $invoices = $this->ci->debtors_m->get_loan_installments($loan->id,$loan->group_id);
                if($invoices){
                    foreach ($invoices as $invoice) {
                        $this->ci->debtors_m->update_loan_invoices($invoice->id,array('status'=>1,'amount_paid'=>0,'modified_on'=>time()));
                    }
                }
                $this->pay_edited_invoices($loan,TRUE,TRUE);
            }
            return TRUE;
        }else{
            
            if($this->ci->loans_m->void_loan_payment_statement($loan->id,$loan->group_id)){
                $payments = $this->ci->loan_repayments_m->get_loan_repayments($loan->id,'','',$loan->group_id);
              
                if($payments){
                    $member_ids = array();
                    $group_ids = array();
                    $transaction_dates = array();
                    $transaction_types = array();
                    $payment_methods = array();
                    $loan_ids = array();
                    $loan_payment_ids = array();
                    $account_ids = array();
                    $amounts = array();
                    $balances = array();
                    $actives = array();
                    $statuss = array();
                    $created_ons = array();
                    $is_a_back_dating_records = array();
                    foreach ($payments as $payment) {
                        $member_ids[] = $payment->member_id;
                        $group_ids[] = $payment->group_id;
                        $transaction_dates[] = $payment->receipt_date;
                        $transaction_types[] = 4;
                        $payment_methods[] = $payment->payment_method;
                        $loan_ids[] = $payment->loan_id;
                        $loan_payment_ids[] = $payment->id;
                        $account_ids[] = $payment->account_id;
                        $amounts[] = $payment->amount;
                        $balances[] = 0;
                        $actives[] = $payment->active;
                        $statuss[] = $payment->status;
                        $created_ons[] = time();
                        $is_a_back_dating_records[] = $payment->is_a_back_dating_record;
                    }

                    if($member_ids){
                        $input = array(
                            'member_id' =>  $member_ids,
                            'group_id'  =>  $group_ids,
                            'transaction_date' =>   $transaction_dates,
                            'transaction_type'  =>  $transaction_types,
                            'payment_method'    =>  $payment_methods,
                            'loan_id'   =>  $loan_ids,
                            'loan_payment_id'   =>  $loan_payment_ids,
                            'account_id'    =>  $account_ids,
                            'amount'        =>  $amounts,
                            'balance'       =>  $balances,
                            'active'        =>  $actives,
                            'status'        =>  $statuss,
                            'created_on'    =>  $created_ons,
                            'is_a_back_dating_record'=>  $is_a_back_dating_records,
                        );
                        $this->ci->loans_m->insert_batch_statements($input);
                    }
                }
            }
            $statement_entries = $this->ci->loans_m->get_loan_statement_for_library_for_payments($loan->id,$loan->group_id);
         
            $date_array = array();
            foreach ($statement_entries as $statement_entry) {
                if($statement_entry->transaction_date){
                    $date = date('dmY',$statement_entry->transaction_date);
                    if(array_key_exists($date, $date_array)){
                        $old_amount = $date_array[$date]['amount'];
                        $date_array[$date] = array(
                            'amount' => ($statement_entry->amount+$old_amount),
                            'transaction_date' => strtotime(date('d-m-Y',$statement_entry->transaction_date)),
                            'transaction_date2' => (date('d-m-Y',$statement_entry->transaction_date)),
                        );
                    }else{
                        $date_array[$date] = array(
                            'amount' => $statement_entry->amount,
                            'transaction_date' => strtotime(date('d-m-Y',$statement_entry->transaction_date)),
                            'transaction_dat2' => (date('d-m-Y',$statement_entry->transaction_date)),
                        );
                    }
                }
            }
            if($date_array){
                unset($statement_entries);
                foreach ($date_array as $date_key => $date_value) {
                    $statement_entries[] = (object)$date_value;
                }
            }
            foreach ($statement_entries as $statement_entry_key=>$statement_entry) {
                $statement_amount = $statement_entry->amount;
                if($statement_amount>0){
                    $this->recalculate_reducing_balance($loan,$statement_amount,$statement_entry->transaction_date);
                   
                    $invoices = $this->ci->loan_invoices_m->get_unpaid_loan_installments($loan->id,$loan->group_id);
                    $parent_id = '';
                    $count=0;
                    $amount = $statement_amount;
                    foreach ($invoices as $invoice){
                        if($amount){
                            if($invoice->type==1 || $invoice->type==5){
                                if($invoice->status!=2){
                                    $amount_paid = $invoice->amount_paid;
                                    if($amount_paid<1){
                                        $amount_paid = 0;
                                    }
                                    $amount_payable = $invoice->amount_payable-$amount_paid;
                                    if($amount_payable<1 && $amount_payable>=0){
                                        $status=2;
                                        $amount_paid = $amount_paid;
                                        if(date('ymd',$statement_entry->transaction_date)){
                                            if($this->ci->loan_invoices_m->void_children_invoices_fined_wrongly_after_date($invoice->id,$statement_entry->transaction_date,$loan->group_id)){
                                                $parent_id = $invoice->id;
                                            }
                                        }
                                    }else{
                                        if((round($amount,1)>=floor($amount_payable)) || (floor($amount)>=floor($amount_payable)) ){
                                            $amount = $amount-$amount_payable;
                                            if($amount<1){
                                                $amount = 0;
                                            }
                                            $status = 2;
                                            $amount_paid = $invoice->amount_payable;
                                            if(date('ymd',$statement_entry->transaction_date)){
                                                if($this->ci->loan_invoices_m->void_children_invoices_fined_wrongly_after_date($invoice->id,$statement_entry->transaction_date,$loan->group_id)){
                                                    $parent_id = $invoice->id;
                                                }
                                            }
                                        }else{
                                            $status = 1;
                                            $amount_paid = $amount+$amount_paid;
                                            $amount = 0;
                                        }
                                    }
                                    $update_invoice = array(
                                            'status'=>$status,
                                            'amount_paid'=>$amount_paid,
                                            'modified_on'=>time(),
                                        );
                                    $this->ci->loan_invoices_m->update($invoice->id,$update_invoice);
                                }else{
                                    continue;
                                }
                            }else if(isset($parent_id) && $invoice->fine_parent_loan_invoice_id!=$parent_id){
                                if($invoice->active){
                                    if($loan->enable_loan_fine_deferment){
                                        if($this->ci->loan_invoices_m->count_all_loan_invoices($loan->id,$loan->group_id)==0){
                                            //all payments were made
                                            if($invoice->status!=2)
                                            {
                                                $amount_paid = $invoice->amount_paid;
                                                $amount_payable = $invoice->amount_payable-$amount_paid;
                                                if($amount_payable<1){
                                                    $status =2;
                                                    $amount_paid = $amount_paid;
                                                    if(date('ymd',$invoice->due_date)>=date('ymd',$statement_entry->transaction_date))
                                                    {
                                                        //check if this invoice has fines
                                                        if($this->ci->loan_invoices_m->void_children_invoices_fined_wrongly($invoice->id,$loan->group_id)){
                                                            $parent_id = $invoice->id;
                                                        }
                                                    }
                                                }else 
                                                {
                                                    if(round($amount,1)>=round($amount_payable,1)){
                                                        $amount = $amount-$amount_payable;
                                                        $status =2;
                                                        $amount_paid = $invoice->amount_payable;
                                                        if(date('ymd',$invoice->due_date)>=date('ymd',$statement_entry->transaction_date))
                                                        {
                                                            //check if this invoice has fines
                                                            if($this->ci->loan_invoices_m->void_children_invoices_fined_wrongly($invoice->id,$loan->group_id)){
                                                               $parent_id = $invoice->id; 
                                                            }
                                                        }
                                                    }
                                                    else{
                                                        $status = 1;
                                                        $amount_paid = $amount+$amount_paid;
                                                        $amount = 0;
                                                    }
                                                }
                                               $this->ci->loan_invoices_m->update($invoice->id,array('status'=>$status,'amount_paid'=>$amount_paid,'modified_on'=>time(),));
                                            }else{
                                                continue;
                                            }
                                        }else{
                                            continue;
                                        }
                                    }else{
                                        if($invoice->status!=2){
                                            $amount_paid = $invoice->amount_paid;
                                            $amount_payable = $invoice->amount_payable-$amount_paid;
                                            if($amount_payable<1){
                                                $status =2;
                                                $amount_paid = $amount_paid;
                                                if(date('ymd',$statement_entry->transaction_date)){
                                                    //check if this invoice has fines
                                                    if($this->ci->loan_invoices_m->void_children_invoices_fined_wrongly_after_date($invoice->id,$statement_entry->transaction_date,$lona->group_id)){
                                                        $parent_id = $invoice->id;
                                                    }
                                                }
                                            }else{
                                                if(round($amount,1)>=round($amount_payable,1)){
                                                    $amount = $amount-$amount_payable;
                                                    $status =2;
                                                    if(date('ymd',$statement_entry->transaction_date)){
                                                        //check if this invoice has fines
                                                        if($this->ci->loan_invoices_m->void_children_invoices_fined_wrongly_after_date($invoice->id,$statement_entry->transaction_date,$loan->group_id)){
                                                            $parent_id = $invoice->id;
                                                        }
                                                    }
                                                    $amount_paid = $invoice->amount_payable;
                                                }
                                                else{
                                                    $status = 1;
                                                    $amount_paid = $amount+$amount_paid;
                                                     $amount = 0;
                                                }
                                            }
                                            $this->ci->loan_invoices_m->update($invoice->id,array('status'=>$status,'amount_paid'=>$amount_paid));
                                        }else{
                                            continue;
                                        }

                                    }
                                }
                                else{
                                    continue;
                                }
                            }
                        }else{
                            //break;
                        }
                        if($invoice->type == 3){
                            $pending_invoices =  $this->ci->loan_invoices_m->count_unpaid_loan_invoices($loan->id,$loan->group_id);
                            if($pending_invoices <=0){
                                $balance = $this->ci->loan_invoices_m->get_outstanding_balance($loan->member_id, $loan->id,$statement_entry->transaction_date,$loan->group_id);
                                if($balance<=1){
                                    if($this->ci->loan_invoices_m->void_future_outstanding_loan_invoices($loan->id,$statement_entry->transaction_date,$loan->group_id)){
                                    }
                                }
                            }
                        }
                    }
                    $this->_update_all_invoices($loan->id,'',$loan->group_id);
                    // if(date('Ymd',$statement_entry->transaction_date) == date('Ymd',strtotime('27-02-2020'))){
                    //     echo 'for '.date('Y-m-d',$statement_entry->transaction_date).' ';
                    //     die('done');
                    // }
                }else{
                    break;
                }
            }
            // if($reset ==FALSE){
            //     $invoices = $this->ci->loan_invoices_m->get_loan_installments($loan->id,$loan->group_id);
            //     if($invoices){
            //         foreach ($invoices as $invoice) {
            //             $this->ci->loan_invoices_m->update($invoice->id,array('status'=>1,'amount_paid'=>0,'modified_on'=>time()));
            //         }
            //     }
            //     $this->pay_edited_invoices($loan,TRUE);
            // }
            return TRUE;
        }
    }

    function _update_all_invoices($loan_id = 0,$is_a_debtor=FALSE){
        if($is_a_debtor){
            $loan_invoices = $this->ci->debtors_m->get_loan_installments($loan_id);
            if($loan_invoices){
                $update = array();
                $status = array();
                $modified_on = array();
                $ids = array();
                foreach ($loan_invoices as $loan_invoice) {
                    if(($loan_invoice->amount_payable - $loan_invoice->amount_paid)<1){
                        $loan_invoice->status = 2;
                        $loan_invoice->modified_on = time();
                    }else{
                        $loan_invoice->status = 1;
                        $loan_invoice->modified_on = time();
                    }
                }
                $loan_invoices = json_decode(json_encode($loan_invoices),TRUE);
                $this->ci->debtors_m->update_batch($loan_invoices);
            }
        }else{
            $loan_invoices = $this->ci->loan_invoices_m->get_loan_installments($loan_id);
            if($loan_invoices){
                $update = array();
                $status = array();
                $modified_on = array();
                $ids = array();
                foreach ($loan_invoices as $loan_invoice) {
                    if(($loan_invoice->amount_payable - $loan_invoice->amount_paid)<1){
                        $loan_invoice->status = 2;
                        $loan_invoice->modified_on = time();
                    }else{
                        $loan_invoice->status = 1;
                        $loan_invoice->modified_on = time();
                    }
                }
                $loan_invoices = json_decode(json_encode($loan_invoices),TRUE);
                $this->ci->loan_invoices_m->update_batch($loan_invoices);
            }
        }

        
        return TRUE;
    }

    function pay_invoices($loan_id){
        $loan = $this->ci->loans_m->get($loan_id);
        $statement_entries = $this->ci->loans_m->get_loan_statement_for_library($loan_id);
        $amount_paid = $this->ci->loan_repayments_m->get_loan_total_payments($loan_id);
        if($loan->enable_loan_fine_deferment)
        {
            $instalments = $this->ci->loan_invoices_m->get_loan_instalments($loan_id);
            foreach($instalments as $i){
                    if($amount_paid>=$i->amount_payable){
                        $this->ci->loan_invoices_m->update($i->id,array('amount_paid'=>$i->amount_payable,"status"=>3,'modified_on'=>time(),));
                        $amount_paid-=$i->amount_payable;
                    }else if($amount_paid<$i->amount_payable){
                        if($amount_paid){
                            $this->ci->loan_invoices_m->update($i->id,array('amount_paid'=>$amount_paid,"status"=>1,'modified_on'=>time(),));
                        }else{
                            $this->ci->loan_invoices_m->update($i->id,array('amount_paid'=>0,"status"=>1,'modified_on'=>time(),));                       
                        }
                        $amount_paid=0;
                    }        
            }
            //fines balance
            $fines = $this->ci->loan_invoices_m->get_loan_fines($loan_id);
            foreach($fines as $fine){
                    if($amount_paid>=$fine->amount_payable){
                        $this->ci->loan_invoices_m->update($fine->id,array('amount_paid'=>$fine->amount_payable,"status"=>2,'modified_on'=>time(),));
                        $amount_paid-=$fine->amount_payable;
                    }else if($amount_paid<$fine->amount_payable){
                        if($amount_paid){
                            $this->ci->loan_invoices_m->update($fine->id,array('amount_paid'=>$amount_paid,"status"=>1,'modified_on'=>time(),));
                        }else{
                            $this->ci->loan_invoices_m->update($fine->id,array('amount_paid'=>0,"status"=>1,'modified_on'=>time(),));                       
                        }
                        $amount_paid=0;
                    }  
            }
            return TRUE;
        }else{
            foreach($statement_entries as $statement_entry){
                if($statement_entry->transaction_type==1||$statement_entry->transaction_type==2||$statement_entry->transaction_type==3||$statement_entry->transaction_type==5){
                    if($amount_paid>=$statement_entry->amount){
                        $this->ci->loan_invoices_m->update($statement_entry->loan_invoice_id,array('amount_paid'=>$statement_entry->amount,"status"=>2,'modified_on'=>time(),));
                        $amount_paid-=$statement_entry->amount;
                    }else if($amount_paid<$statement_entry->amount){
                        if($amount_paid){
                            $this->ci->loan_invoices_m->update($statement_entry->loan_invoice_id,array('amount_paid'=>$amount_paid,"status"=>1,'modified_on'=>time(),));
                        }else{
                            $this->ci->loan_invoices_m->update($statement_entry->loan_invoice_id,array('amount_paid'=>0,"status"=>1,'modified_on'=>time(),));                       
                        }
                        $amount_paid=0;
                    }else{
                        $amount_paid=0;
                    }
                }
            }
            return TRUE; 
        }
    }

    function queue_today_loan_invoices($date=0,$limit=0){
        if($date){
             if(date('ymd',$date)>date('ymd',time())){
               $date=time();
            }
            //do nothing for now
        }else{
            $date = time();
        }
        $successful_statement_entries = 0;
        $successful_invoice_queue_entry = 0;
        $unsuccessful_statement_entries = 0;
        $unsuccessful_invoice_queue_entry = 0;
        $successful_invoice_updates = 0;
        $unsuccessful_invoice_updates = 0;

        //member loans
        $today_invoices = $this->ci->loan_invoices_m->get_today_loan_invoices_to_queue($date,$limit);
        $member_ids = array();
        $group_ids = array();
        $transaction_dates = array();
        $loan_ids = array();
        $transaction_types = array();
        $loan_invoice_ids = array();
        $amounts = array();
        $balances = array();
        $actives = array();
        $statuss = array();
        $created_bys = array();
        $created_ons = array();
        $lump_sum_remainings = array();
        $loan_balances = array();
        $invoice_numbers = array();
        $due_dates = array();
        $types = array();
        $invoice_dates = array();
        $descriptions = array();
        if($today_invoices){
            foreach ($today_invoices as $invoice) {
                $member_ids[] = $invoice->member_id;
                $group_ids[] = $invoice->group_id;
                $transaction_dates[] = $date;
                $loan_ids[] = $invoice->loan_id;
                $transaction_types[] = 1;
                $loan_invoice_ids[] = $invoice->id;
                $amounts[] = $invoice->amount_payable;
                $balances[] = 0;
                $actives[] = 1;
                $statuss[] = 1;
                $created_bys[] = 1;
                $created_ons[] = time();
                $invoice_numbers[] = 'INV-'.$invoice->invoice_no;
                $due_dates[] = $invoice->due_date;
                $types[] = $invoice->type;
                $invoice_dates[] = $invoice->invoice_date;
                $descriptions[] = 'Loan Invoice';
            }

            if($member_ids&&$group_ids&&$loan_ids){
                $input = array(
                    'member_id' =>  $member_ids,
                    'group_id'  =>  $group_ids,
                    'transaction_date'  => $transaction_dates,
                    'loan_id'   =>  $loan_ids,
                    'transaction_type'  =>  $transaction_types,
                    'loan_invoice_id'   =>  $loan_invoice_ids,
                    'amount'    =>  $amounts,
                    'balance'   =>  $balances,
                    'active'    =>  $actives,
                    'status'    =>  $statuss,
                    'created_by' => $created_bys,
                    'created_on' =>  $created_ons,
                );
                if($this->ci->loans_m->insert_batch_statements($input)){
                    foreach ($loan_ids as $key=>$loan_id) {
                        if($update = $this->ci->loan_invoices_m->update($loan_invoice_ids[$key],array(
                            'is_sent' => '1',
                            'modified_on'=>time()
                        ))){
                            $successful_statement_entries = count($member_ids);
                        }else{
                             ++$unsuccessful_invoice_queue_entry;
                        }
                        $this->update_loan_invoices($loan_id);
                        $loan_balances[] = $this->ci->loans_m->get_unpaid_loan_balance($loan_id);
                        $lump_sum_remainings[] = $this->ci->loan_invoices_m->get_loan_lump_sum_as_date($loan_id);
                        
                    }
                    $invoice_queue_input = array(
                        'loan_id'   =>  $loan_ids,
                        'invoice_id'    =>  $loan_invoice_ids,
                        'invoice_no'    =>  $invoice_numbers,
                        'member_id'     =>  $member_ids,
                        'due_date'  =>  $due_dates,
                        'invoice_type'  => $types,
                        'invoice_date'  =>  $invoice_dates,
                        'amount_payable'    =>  $amounts,
                        'lump_sum_remaining'    =>  $lump_sum_remainings,
                        'loan_balance'  =>  $loan_balances,
                        'group_id'  =>  $group_ids,
                        'description'  =>   $descriptions,
                        'created_on'    =>  $created_ons,
                    );
                    if($this->ci->loan_invoices_m->insert_loan_invoicing_batch_queue($invoice_queue_input)){
                        $successful_invoice_queue_entry = count($loan_ids);
                    }else{
                       
                    }
                }
            }
            
        }else{
            echo 'No invoices to send today.<br/>';
        }

        //debtor loans
        $debtor_loan_invoices = $this->ci->debtors_m->get_today_loan_invoices_to_queue($date);
        if($debtor_loan_invoices){
            foreach ($debtor_loan_invoices as $invoice) {
               $id = $this->ci->debtors_m->insert_loan_statement(array(
                            'debtor_id' =>  $invoice->debtor_id,
                            'group_id'  =>  $invoice->group_id,
                            'transaction_date'  => $date,
                            'debtor_loan_id'   =>  $invoice->debtor_loan_id,
                            'transaction_type'  =>  1,
                            'debtor_loan_invoice_id'   =>  $invoice->id,
                            'amount'    =>  $invoice->amount_payable,
                            'balance'   =>  0,
                            'active'    =>  1,
                            'status'    =>  1,
                            'created_by' => 1,
                            'created_on' =>  time(),
                        ));
                if($id){
                    $update = $this->ci->debtors_m->update_loan_invoices($invoice->id,array('is_sent' => '1','modified_on'=>time(),));
                    if($update){
                        $this->update_loan_invoices($invoice->debtor_loan_id,TRUE);

                        $debtor_total_installment_payable = $this->ci->debtors_m->get_total_installment_loan_payable($invoice->debtor_loan_id);

                        $debtor_total_fines = $this->ci->debtors_m->get_total_loan_fines_payable($invoice->debtor_loan_id);
                        $debtor_total_paid = $this->ci->debtors_m->get_loan_total_payments($invoice->debtor_loan_id);
                        $debtor_loan_balance =$this->ci->debtors_m->get_unpaid_loan_balance($invoice->debtor_loan_id);
                        $debtor_lump_sum_remaining = $this->ci->debtors_m->get_loan_lump_sum_as_date($invoice->debtor_loan_id);;
                        //queue the invoice
                        $invoice_queue = $this->ci->loan_invoices_m->insert_loan_invoicing_queue(array(
                            'debtor_loan_id'   =>  $invoice->debtor_loan_id,
                            'invoice_id'    =>  $invoice->id,
                            'invoice_no'    =>  'INV-'.$invoice->invoice_no,
                            'debtor_id'     =>  $invoice->debtor_id,
                            'due_date'  =>  $invoice->due_date,
                            'invoice_type'  => $invoice->type,
                            'invoice_date'  =>  $invoice->invoice_date,
                            'amount_payable'    =>  $invoice->amount_payable,
                            'lump_sum_remaining'    =>  $debtor_lump_sum_remaining,
                            'loan_balance'  =>  $debtor_loan_balance,
                            'group_id'  =>  $invoice->group_id,
                            'description'  =>   'Debtor Loan Invoice',
                            'created_on'    =>  time(),
                        ));

                        if($invoice_queue){
                            ++$successful_invoice_queue_entry;
                        }else{
                            ++$unsuccessful_invoice_queue_entry;
                        }
                        ++$successful_invoice_updates;
                        unset($invoice_queue);
                    }else
                    {
                        ++$unsuccessful_invoice_updates;
                    }
                    ++$successful_statement_entries;
                    unset($update);
                }else
                {
                    ++$unsuccessful_statement_entries;
                }
                unset($id);
            }
        }else{
            echo 'No debtor invoices to send today.<br/>';
        }





        if($successful_statement_entries){
            echo  $successful_statement_entries.' Loan statements created.<br/> ';
        }
        if($successful_invoice_queue_entry){
            echo  $successful_invoice_queue_entry.' Loan invoices queued.<br/> ';
        }
        if($unsuccessful_statement_entries){
            echo  $unsuccessful_statement_entries.' loan statements could not be created.<br/> ';
        }
        if($unsuccessful_invoice_queue_entry){
            echo  $unsuccessful_invoice_queue_entry.' Loan invoices could not be queued.<br/> ';
        }
        echo  date('d-m-Y',$date).' .<br/> ';
    }

    function process_loan_invoices_queue($limit=0){
        if($limit){

        }else{
            $limit=40;
        }

        $queued_invoices = $this->ci->loan_invoices_m->get_queued_loan_invoices($limit);
        $successful_invoice_sent = 0;
        $unsuccessful_invoice_sent = 0;
        $successful_deleted_queue = 0;
        $unsuccessful_deleted_queue = 0;

        if($queued_invoices){
            /**
            1. create the email body..
            2. create the sms body..
            3. send a notification to the member..
            ***/
            foreach ($queued_invoices as $invoice) {
                $invoice_sent = array();
                $notification_id = 0;
                $sms_body = '';
                $email_body = '';
                $sms_id = '';
                $email_id = '';
                if($invoice->member_id){
                    $group_currency = $this->ci->loans_m->get_this_group_currency($invoice->group_id);
                    $group = $this->ci->groups_m->get($invoice->group_id);
                    $member = $this->ci->members_m->get_group_member($invoice->member_id,$invoice->group_id);
                    $total_installment_payable = $this->ci->loan_invoices_m->get_total_installment_loan_payable($invoice->loan_id);
                    $total_fines = $this->ci->loan_invoices_m->get_total_loan_fines_payable($invoice->loan_id);
                    $total_paid = $this->ci->loan_repayments_m->get_loan_total_payments($invoice->loan_id);
                    $loan_balance =$this->ci->loans_m->get_loan_balance($invoice->loan_id);
                    $loan_borrowed = $this->ci->loans_m->get_loan_borrowed($invoice->loan_id);

                    $loan_details = array(
                            'total_installment_payable' => $total_installment_payable,
                            'total_fines' => $total_fines,
                            'total_paid' => $total_paid,
                            'loan_balance' => $loan_balance,
                            'loan_borrowed' => $loan_borrowed,
                            'id' => $invoice->loan_id,
                        );

                    $loan = $this->ci->loans_m->get($invoice->loan_id);

                    // if($loan->sms_notifications_enabled){
                    //     $sms_id = $this->ci->messaging->send_sms_loan_installments_invoices_queued($invoice,$group,$member,$group_currency);
                    // }
                    // if($loan->email_notifications_enabled){
                    //     $email_id = $this->ci->messaging->send_email_loan_installments_invoices_queued($invoice,$group,$member,$group_currency,$loan_details);
                    // }
                    $member = $this->ci->members_m->get_group_member($invoice->member_id,$invoice->group_id);
                    $notification_id = $this->ci->notifications->create(
                        $invoice->description,
                        'You have been invoiced '.$group_currency.' '.number_to_currency($invoice->amount_payable)." for your 'Loan' due ".timestamp_to_receipt($invoice->due_date).'. Total Unpaid loan balance is '.$group_currency.' '.number_to_currency($invoice->loan_balance).' and the Lump Sum Loan amount is '.$group_currency.' '.number_to_currency($invoice->lump_sum_remaining),
                        $this->ci->ion_auth->get_user($member->user_id),$invoice->member_id,
                        $member->user_id,
                        $invoice->member_id,
                        $invoice->group_id,
                        'View Invoice',
                        'group/loans/loan_statement/'.$invoice->loan_id,3,'','','','','','','','','','',$invoice->loan_id);
                    $invoice_sent = $this->ci->loan_invoices_m->insert_loan_invoices_sent(array(
                            'loan_id' =>$invoice->loan_id,
                            'invoice_id'=>$invoice->invoice_id,
                            'invoice_no'=>$invoice->invoice_no,
                            'invoice_type'=>$invoice->invoice_type,
                            'member_id'=>$invoice->member_id,
                            'group_id'=>$invoice->group_id,
                            'invoice_date'=>$invoice->invoice_date,
                            'due_date' => $invoice->due_date,
                            'amount_payable'=>$invoice->amount_payable,
                            'loan_balance'=>$invoice->loan_balance,
                            'lump_sum_remaining'=>$invoice->lump_sum_remaining,
                            'description'=>$invoice->description,
                            'notification_created'=>$notification_id,
                            'email_sent'=>$email_id,
                            'email_body'=>$email_body,
                            'sms_sent'=>$sms_id,
                            'sms_message'=>$sms_body,
                            'created_on'=>time(),
                        ));
                }elseif ($invoice->debtor_id) {
                    $group_currency = $this->ci->loans_m->get_this_group_currency($invoice->group_id);
                    $group = $this->ci->groups_m->get($invoice->group_id);
                    $debtor = $this->ci->debtors_m->get($invoice->debtor_id,$invoice->group_id);

                    $total_installment_payable = $this->ci->debtors_m->get_total_installment_loan_payable($invoice->debtor_loan_id);
                    $total_fines = $this->ci->debtors_m->get_total_loan_fines_payable($invoice->debtor_loan_id);
                    $total_paid = $this->ci->debtors_m->get_loan_total_payments($invoice->debtor_loan_id);
                    $loan_balance =$this->ci->debtors_m->get_loan_balance($invoice->debtor_loan_id);
                    $loan_borrowed = $this->ci->debtors_m->get_loan_borrowed($invoice->debtor_loan_id);

                    $loan_details = array(
                            'total_installment_payable' => $total_installment_payable,
                            'total_fines' => $total_fines,
                            'total_paid' => $total_paid,
                            'loan_balance' => $loan_balance,
                            'loan_borrowed' => $loan_borrowed,
                            'id' => $invoice->debtor_loan_id,
                        );

                    $debtor_loan = $this->ci->debtors_m->get_loan($invoice->debtor_loan_id,$invoice->group_id);
                    // if($debtor_loan->sms_notifications_enabled){
                    //     $sms_id = $this->ci->messaging->send_sms_loan_installments_invoices_queued($invoice,$group,$debtor,$group_currency,TRUE);
                    // }
                    // if($debtor_loan->email_notifications_enabled){
                    //     $email_id = $this->ci->messaging->send_email_loan_installments_invoices_queued($invoice,$group,$debtor,$group_currency,$loan_details,TRUE);
                    // }
                }
                $delete_queue = $this->ci->loan_invoices_m->delete_queued_loan_invoices($invoice->id);
                if($delete_queue){
                    ++$successful_deleted_queue;
                }else{
                    ++$unsuccessful_deleted_queue;
                }
                ++$successful_invoice_sent;
                unset($delete_queue);
            }

        }else{
            echo 'there are no queued invoices';
        }

        if($successful_invoice_sent){
            echo $successful_invoice_sent.' invoices were processed</br>';
        }else if($unsuccessful_invoice_sent){
            echo $unsuccessful_invoice_sent.' invoices were not processed</br>';
        }

        if($successful_deleted_queue){
            echo $successful_deleted_queue.' invoices were processed and removed from queue</br>';
        }else if($unsuccessful_deleted_queue){
            echo $unsuccessful_deleted_queue.' invoices were processed and not removed from queue</br>';
        }
    }

    function queue_fine_loan_late_payment($date=0){
        if($date){
             if(date('ymd',$date)>date('ymd',time())){
               $date=time();
            }  
        }else{
            $date=time();
        }

        $successful_statement_entries = 0;
        $successful_invoice_queue_entry = 0;
        $unsuccessful_statement_entries = 0;
        $unsuccessful_invoice_queue_entry = 0;
        $successful_fines = 0;
        $unsuccessful_fines = 0;
        $fine_exits=0;
        $fine_list = $this->ci->loan_invoices_m->get_late_loan_payment_fine_list($date);
        if($fine_list){
            $loan_invoice_ids = array();
            $loan_invoice_ids = array();
            $loan_ids = array();
            $member_ids = array();
            $transaction_types = array();
            $transaction_dates = array();
            $amounts = array();
            $actives = array();
            $statuss = array();
            $balances = array();
            $group_ids = array();
            $created_ons = array();
            $created_bys = array();
            $invoice_nos = array();
            $due_dates = array();
            $invoice_dates = array();
            $invoice_types = array();
            $descriptions = array();
            $loan_balances = array();
            $lump_sum_remainings = array();
            $invoices = array();
            $invoice_ids = array();
            $loan__number_ids = array();
            foreach ($fine_list as $fine) {
                if($fine->status == '2'){

                }else{
                    ++$successful_fines;
                    $loan_ids[$fine->loan_id] = $fine->loan_id;
                }
            }
            $res = array();
            if($loan_ids){
                foreach ($loan_ids as $loan_id) {
                    $res[$loan_id] = $this->fix_loan_invoices_fine($loan_id,0,TRUE,$date);
                }
            }
            if($res){
                foreach ($res as $results) {
                    if(is_array($results)){
                        foreach ($results as $result) {
                            $result = (object)$result;
                            $loan__number_ids[] = $result->loan_id;
                            $invoice_ids[] = $result->invoice_id;
                            $invoice_nos[] = $result->invoice_no;
                            $member_ids[] = $result->member_id;
                            $due_dates[] = $result->due_date;
                            $invoice_dates[] = $result->invoice_date;
                            $invoice_types[] = $result->invoice_type;
                            $amounts[] = $result->amount_payable;
                            $lump_sum_remainings[] = $result->lump_sum_remaining;
                            $loan_balances[] = $result->loan_balance;
                            $group_ids[] = $result->group_id;
                            $descriptions[] = $result->description;
                            $created_ons[] = $result->created_on;
                        }
                        $successful_statement_entries = count($results);
                    }else{

                    }
                }
            }

            if($loan__number_ids && $member_ids && $amounts){
                $loan_invoicing_queue = array(
                    'loan_id'   =>  $loan__number_ids,
                    'invoice_id'    =>  $invoice_ids,
                    'invoice_no'    =>  $invoice_nos,
                    'member_id'     =>  $member_ids,
                    'due_date'  =>  $due_dates,
                    'invoice_date'  =>  $invoice_dates,
                    'invoice_type'  => $invoice_types,
                    'amount_payable'    =>  $amounts,
                    'lump_sum_remaining'    =>  $lump_sum_remainings,
                    'loan_balance'  =>  $loan_balances,
                    'group_id'  =>  $group_ids,
                    'description'  =>   $descriptions,
                    'created_on'    =>  $created_ons,
                );
                if($this->ci->loan_invoices_m->insert_loan_invoicing_batch_queue($loan_invoicing_queue)){
                    $successful_invoice_queue_entry = count($amounts);
                }else{
                   ++$unsuccessful_fines;
                }
            }
        }

        //debtor fine list
        $debtor_fine_list = $this->ci->debtors_m->get_late_loan_payment_fine_list($date);
        if($debtor_fine_list){
            $debtor_loan_ids = array();
            $loan_invoice_ids = array();
            $loan_invoice_ids = array();
            $loan_ids = array();
            $debtor_ids = array();
            $transaction_types = array();
            $transaction_dates = array();
            $amounts = array();
            $actives = array();
            $statuss = array();
            $balances = array();
            $group_ids = array();
            $created_ons = array();
            $created_bys = array();
            $invoice_nos = array();
            $due_dates = array();
            $invoice_dates = array();
            $invoice_types = array();
            $descriptions = array();
            $loan_balances = array();
            $lump_sum_remainings = array();
            $invoices = array();
            $invoice_ids = array();
            $loan__number_ids = array();
            foreach ($debtor_fine_list as $fine) {
                if($fine->status == '2'){
                }else{
                    ++$successful_fines;
                    $debtor_loan_ids[$fine->debtor_loan_id] = $fine->debtor_loan_id;
                }
            }

            $debtor_res = array();
            if($debtor_loan_ids){
                foreach ($debtor_loan_ids as $debtor_loan_id) {
                    $debtor_res[$debtor_loan_id] = $this->fix_loan_invoices_fine($debtor_loan_id,TRUE,TRUE,$date);
                }
            }
            if($debtor_res){
                foreach ($debtor_res as $debtor_results) {
                    if(is_array($debtor_results)){
                        foreach ($debtor_results as $debtor_result) {
                            $debtor_result = (object)$debtor_result;
                            $loan__number_ids[] = $debtor_result->loan_id;
                            $invoice_ids[] = $debtor_result->invoice_id;
                            $invoice_nos[] = $debtor_result->invoice_no;
                            $debtor_ids[] = $debtor_result->debtor_id;
                            $due_dates[] = $debtor_result->due_date;
                            $invoice_dates[] = $debtor_result->invoice_date;
                            $invoice_types[] = $debtor_result->invoice_type;
                            $amounts[] = $debtor_result->amount_payable;
                            $lump_sum_remainings[] = $debtor_result->lump_sum_remaining;
                            $loan_balances[] = $debtor_result->loan_balance;
                            $group_ids[] = $debtor_result->group_id;
                            $descriptions[] = $debtor_result->description;
                            $created_ons[] = $debtor_result->created_on;
                        }
                        $successful_statement_entries = count($debtor_results);
                    }else{

                    }
                }
            }

            if($loan__number_ids && $debtor_ids && $amounts){
                $loan_invoicing_queue = array(
                    'loan_id'   =>  $loan__number_ids,
                    'invoice_id'    =>  $invoice_ids,
                    'invoice_no'    =>  $invoice_nos,
                    'debtor_id'     =>  $debtor_ids,
                    'due_date'  =>  $due_dates,
                    'invoice_date'  =>  $invoice_dates,
                    'invoice_type'  => $invoice_types,
                    'amount_payable'    =>  $amounts,
                    'lump_sum_remaining'    =>  $lump_sum_remainings,
                    'loan_balance'  =>  $loan_balances,
                    'group_id'  =>  $group_ids,
                    'description'  =>   $descriptions,
                    'created_on'    =>  $created_ons,
                );
                if($this->ci->loan_invoices_m->insert_loan_invoicing_batch_queue($loan_invoicing_queue)){
                    $successful_invoice_queue_entry = count($amounts);
                }else{
                   ++$unsuccessful_fines;
                }
            }
        }

        if($successful_fines){
            echo  $successful_fines.' Loan fines.<br/> ';
        }
        if($unsuccessful_fines){
            echo  $unsuccessful_fines.' Unsuccessful loan fines.<br/> ';
        }
        if($successful_statement_entries){
            echo  $successful_statement_entries.' Loan statements created.<br/> ';
        }
        if($successful_invoice_queue_entry){
            echo  $successful_invoice_queue_entry.' Loan invoices queued.<br/> ';
        }
        if($unsuccessful_statement_entries){
            echo  $unsuccessful_statement_entries.' loan statements could not be created.<br/> ';
        }
        if($unsuccessful_invoice_queue_entry){
            echo  $unsuccessful_invoice_queue_entry.' Loan invoices could not be queued.<br/> ';
        }
        if($fine_exits){
            echo $fine_exits.' exits thus not processed';
        }

        echo date('d-m-Y',$date);
    }

    function process_loan_late_payment_invoices_queue($limit=0){
        if($limit){

        }else{
            $limit=40;
        }

        $fine_invoice_list = $this->ci->loan_invoices_m->get_queued_loan_invoices_fine_list($limit);
        $successful_invoice_sent = 0;
        $unsuccessful_invoice_sent = 0;
        $successful_deleted_queue = 0;
        $unsuccessful_deleted_queue = 0;
        if($fine_invoice_list){
            foreach ($fine_invoice_list as $fine_invoice){
                $sms_body = '';
                $email_body = '';
                $sms_id = '';
                $email_id = '';
                $notification_id = 0;
                $fine_invoice_sent = array();
                if($fine_invoice->member_id){
                    $group_currency = $this->ci->loans_m->get_this_group_currency($fine_invoice->group_id);
                    $group = $this->ci->groups_m->get($fine_invoice->group_id);
                    $member = $this->ci->members_m->get_group_member($fine_invoice->member_id,$fine_invoice->group_id);

                    $total_installment_payable = $this->ci->loan_invoices_m->get_total_installment_loan_payable($fine_invoice->loan_id);
                    $total_fines = $this->ci->loan_invoices_m->get_total_loan_fines_payable($fine_invoice->loan_id);
                    $total_paid = $this->ci->loan_repayments_m->get_loan_total_payments($fine_invoice->loan_id);
                    $loan_balance =$this->ci->loans_m->get_loan_balance($fine_invoice->loan_id);
                    $loan_borrowed = $this->ci->loans_m->get_loan_borrowed($fine_invoice->loan_id);
                    $loan_details = array(
                            'total_installment_payable' => $total_installment_payable,
                            'total_fines' => $total_fines,
                            'total_paid' => $total_paid,
                            'loan_balance' => $loan_balance,
                            'loan_borrowed' => $loan_borrowed,
                            'id' => $fine_invoice->loan_id
                        );

                    $loan = $this->ci->loans_m->get($fine_invoice->loan_id,$fine_invoice->group_id);
                    // if($loan->sms_notifications_enabled){
                    //     //generate email template
                    //     $sms_id = $this->ci->messaging->send_sms_late_loan_installment_payment_invoice($fine_invoice,$group,$member,$group_currency);
                    // }
                    // if($loan->email_notifications_enabled){
                    //     //generate email template
                    //      $email_id = $this->ci->messaging->send_email_late_loan_installment_payment_invoice($fine_invoice,$group,$member,$group_currency,$loan_details);
                    // }
                    $member = $this->ci->members_m->get_group_member($fine_invoice->member_id,$fine_invoice->group_id);
                    $notification_id = $this->ci->notifications->create(
                        $fine_invoice->description,
                        'You have been fined '.$group_currency.' '.number_to_currency($fine_invoice->amount_payable)." for your 'Late Loan repayment'. This fine is due ".timestamp_to_receipt($fine_invoice->due_date).'. Total Unpaid loan balance is '.$group_currency.' '.number_to_currency($fine_invoice->loan_balance).' and the Lump Sum Loan amount remaining is '.$group_currency.' '.number_to_currency($fine_invoice->lump_sum_remaining),
                        $this->ci->ion_auth->get_user($member->user_id),$fine_invoice->member_id,
                        $member->user_id,
                        $fine_invoice->member_id,
                        $fine_invoice->group_id,
                        'View Invoice',
                        'group/loans/loan_statement/'.$fine_invoice->loan_id,3,'','','','','','','','','','',$fine_invoice->loan_id);

                    $fine_invoice_sent = $this->ci->loan_invoices_m->insert_loan_invoices_sent(array(
                            'loan_id' =>$fine_invoice->loan_id,
                            'invoice_id'=>$fine_invoice->invoice_id,
                            'invoice_no'=>$fine_invoice->invoice_no,
                            'invoice_type'=>$fine_invoice->invoice_type,
                            'member_id'=>$fine_invoice->member_id,
                            'group_id'=>$fine_invoice->group_id,
                            'invoice_date'=>$fine_invoice->invoice_date,
                            'due_date' => $fine_invoice->due_date,
                            'amount_payable'=>$fine_invoice->amount_payable,
                            'loan_balance'=>$fine_invoice->loan_balance,
                            'lump_sum_remaining'=>$fine_invoice->lump_sum_remaining,
                            'description'=>$fine_invoice->description,
                            'notification_created'=>$notification_id,
                            'email_sent'=>$email_id,
                            'email_body'=>$email_body,
                            'sms_sent'=>$sms_id,
                            'sms_message'=>$sms_body,
                            'created_on'=>time(),
                        ));

                    if($fine_invoice_sent)
                    {
                        $delete_queue = $this->ci->loan_invoices_m->delete_queued_loan_invoices($fine_invoice->id);
                        if($delete_queue)
                        {
                            ++$successful_deleted_queue;
                        }else{
                            ++$unsuccessful_deleted_queue;
                        }
                        ++$successful_invoice_sent;
                        unset($delete_queue);
                    }else{
                        ++$unsuccessful_invoice_sent;
                    }
                }elseif($fine_invoice->debtor_id) {
                    $group_currency = $this->ci->loans_m->get_this_group_currency($fine_invoice->group_id);
                    $group = $this->ci->groups_m->get($fine_invoice->group_id);
                    $debtor = $this->ci->debtors_m->get($fine_invoice->debtor_id,$fine_invoice->group_id);
                    $total_installment_payable = $this->ci->debtors_m->get_total_installment_loan_payable($fine_invoice->debtor_loan_id);
                    $total_fines = $this->ci->debtors_m->get_total_loan_fines_payable($fine_invoice->debtor_loan_id);
                    $total_paid = $this->ci->debtors_m->get_loan_total_payments($fine_invoice->debtor_loan_id);
                    $loan_balance =$this->ci->debtors_m->get_loan_balance($fine_invoice->debtor_loan_id);
                    $loan_borrowed = $this->ci->debtors_m->get_loan_borrowed($fine_invoice->debtor_loan_id);

                    $loan_details = array(
                            'total_installment_payable' => $total_installment_payable,
                            'total_fines' => $total_fines,
                            'total_paid' => $total_paid,
                            'loan_balance' => $loan_balance,
                            'loan_borrowed' => $loan_borrowed,
                            'id' => $fine_invoice->debtor_loan_id,
                        );
                    $debtor_loan = $this->ci->debtors_m->get_loan($fine_invoice->debtor_loan_id,$fine_invoice->group_id);
                    // if($debtor_loan->sms_notifications_enabled){
                    //     //generate email template
                    //     $sms_id = $this->ci->messaging->send_sms_late_loan_installment_payment_invoice($fine_invoice,$group,$debtor,$group_currency,TRUE);
                    // }
                    // if($debtor_loan->email_notifications_enabled){
                    //     //generate email template
                    //      $email_id = $this->ci->messaging->send_email_late_loan_installment_payment_invoice($fine_invoice,$group,$debtor,$group_currency,$loan_details,TRUE);
                    // }
                        $delete_queue = $this->ci->loan_invoices_m->delete_queued_loan_invoices($fine_invoice->id);
                        if($delete_queue)
                        {
                            ++$successful_deleted_queue;
                        }else{
                            ++$unsuccessful_deleted_queue;
                        }
                        ++$successful_invoice_sent;
                        unset($delete_queue);
                }
                unset($notification_id);
                unset($fine_invoice_sent);
            }
        }else{
            echo 'There are no fine invoices to send today';
        }

        if($successful_invoice_sent){
            echo $successful_invoice_sent.' fine invoices were processed</br>';
        }else if($unsuccessful_invoice_sent){
            echo $unsuccessful_invoice_sent.' fine invoices were not processed</br>';
        }

        if($successful_deleted_queue){
            echo $successful_deleted_queue.' fine invoices were processed and removed from queue</br>';
        }else if($unsuccessful_deleted_queue){
            echo $unsuccessful_deleted_queue.' fine invoices were processed and not removed from queue</br>';
        }
    }


    function queue_outstanding_loan_balance_fines($date=0){
        if($date){
            if(date('ymd',$date)>date('ymd',time())){
                $date=time();
            }
        }else{
            $date=time();
        }
        $successful_loops=0;
        $unsuccessful_loops=0;
        $successful_invoice=0;
        $unsuccessful_invoice=0;
        $successful_statement_entry=0;
        $unsuccessful_statement_entry=0;
        $successful_invoice_queue_entry=0;
        $unsuccessful_invoice_queue_entry=0;
        $loan_lists = $this->ci->loans_m->get_loans_to_fine_outstanding_balance($date);
        $loan_ids = array();
        if($loan_lists){
            foreach ($loan_lists as $loan){
                if(date('Ymd',$date) == date('Ymd', $loan->outstanding_loan_balance_fine_date)){
                    $post = $outstanding_balance_fixer = $this->outstanding_loan_balance_fine_fixer($loan,'',TRUE,$date);
                    $amount = $post->amount;
                    $next_fine_date = $post->next_fine_date;
                    $fine_id = $post->fine_id;
                    if($amount>0){
                        if($fine_id){
                            $statement = array(
                                'loan_id' => $loan->id,
                                'member_id' => $loan->member_id,
                                'transaction_type' => 3,
                                'loan_invoice_id' => $fine_id,
                                'transaction_date' => $date,
                                'amount' => $amount,
                                'active' => 1,
                                'status' => 1,
                                'balance' => 0,
                                'group_id' => $loan->group_id,
                                'created_on' => time(),
                                'created_by' => 1,
                            );
                            $statement_id = $this->ci->loans_m->insert_loan_statement($statement);
                            if($statement_id){
                                $this->ci->loans_m->update($loan->id,array('outstanding_loan_balance_fine_date'=>$next_fine_date,'modified_on'=>time()));
                                $total_installment_payable = $this->ci->loan_invoices_m->get_total_installment_loan_payable($loan->id);
                                $total_fines = $this->ci->loan_invoices_m->get_total_loan_fines_payable($loan->id);
                                $total_paid = $this->ci->loan_repayments_m->get_loan_total_payments($loan->id);
                                $loan_balance =$this->ci->loans_m->get_loan_balance($loan->id);
                                $lump_sum_remaining = $this->ci->loan_invoices_m->get_loan_lump_sum_as_date($loan->id);
                                $invoice_queue = $this->ci->loan_invoices_m->insert_loan_invoicing_queue(array(
                                    'loan_id'   =>  $loan->id,
                                    'invoice_id'    =>  $fine_id,
                                    'invoice_no'    =>  'INV-'.time(),
                                    'member_id'     =>  $loan->member_id,
                                    'due_date'  =>  $date,
                                    'invoice_type'  => 3,
                                    'invoice_date'  =>  $date,
                                    'amount_payable'    =>  $amount,
                                    'lump_sum_remaining'    =>  $lump_sum_remaining,
                                    'loan_balance'  =>  $loan_balance,
                                    'group_id'  =>  $loan->group_id,
                                    'description'  =>   'Outstanding loan balance fine',
                                    'created_on'    =>  time(),
                                ));
                                if($invoice_queue){
                                    ++$successful_invoice_queue_entry;
                                }
                                else{
                                    ++$unsuccessful_invoice_queue_entry;
                                }

                                unset($invoice_queue);
                                ++$successful_statement_entry;
                            }
                            else{
                                ++$unsuccessful_statement_entry;
                            }

                            unset($statement_id);
                            ++$successful_invoice;
                        }
                        else{
                           ++$unsuccessful_invoice; 
                        }
                        unset($fine_id);
                        ++$successful_loops;
                    }else{
                        ++$unsuccessful_loops;
                    }
                }else{
                    ++$unsuccessful_loops;
                }
            }
        }


        $debtor_loan_list = $this->ci->debtors_m->get_loans_to_fine_outstanding_balance($date);
        if($debtor_loan_list){
            foreach ($debtor_loan_list as $loan) {
                if (date('Ymd',$date) == date('Ymd', $loan->outstanding_loan_balance_fine_date)){
                    $amount = $this->get_outstanding_loan_balance_fine_payable($loan->debtor_id,$loan->id,$loan->loan_amount,'','',TRUE);
                    $amount = (float) str_replace(',', '', number_format($amount, 2));
                    $next_fine_date = $this->_next_outstanding_fine_date_fixer($loan->id,$date,TRUE);
                    $invoice_number = $this->ci->debtors_m->calculate_invoice_no($loan->group_id);
                    
                     $fine = array(
                            'fine_parent_loan_invoice_id' => 0,
                            'invoice_no' => $invoice_number,
                            'debtor_id' => $loan->debtor_id,
                            'debtor_loan_id' => $loan->id,
                            'type' => 3,
                            'interest_amount_payable' => 0,
                            'principle_amount_payable' => 0,
                            'invoice_date' => $date,
                            'due_date' => $date,
                            'fine_date' => 0,
                            'amount_payable' => $amount,
                            'amount_paid' => 0,
                            'status' => 1,
                            'active' => 1,
                            'is_sent' => 1,
                            'group_id' => $loan->group_id,
                            'created_on' => time(),
                            'created_by' => 1,
                        );
                        if($amount>0){
                            $fine_id = $this->ci->debtors_m->insert_loan_invoice($fine);
                            if ($fine_id){
                                $statement = array(
                                    'debtor_loan_id' => $loan->id,
                                    'debtor_id' => $loan->debtor_id,
                                    'transaction_type' => 3,
                                    'debtor_loan_invoice_id' => $fine_id,
                                    'transaction_date' => $date,
                                    'amount' => $amount,
                                    'active' => 1,
                                    'status' => 1,
                                    'balance' => 0,
                                    'group_id' => $loan->group_id,
                                    'created_on' => time(),
                                    'created_by' => 1,
                                );
                                $statement_id = $this->ci->debtors_m->insert_loan_statement($statement);
                                if($statement_id){
                                    $this->ci->debtors_m->update_loan($loan->id,array('outstanding_loan_balance_fine_date'=>$next_fine_date,'modified_on'=>time()));
                                    
                                    $this->update_loan_invoices($loan->id,TRUE);
                                    $this->update_loan_invoices($loan->id,TRUE);

                                    $total_installment_payable = $this->ci->debtors_m->get_total_installment_loan_payable($loan->id);
                                    $total_fines = $this->ci->debtors_m->get_total_loan_fines_payable($loan->id);
                                    $total_paid = $this->ci->debtors_m->get_loan_total_payments($loan->id);
                                    $loan_balance =$this->ci->debtors_m->get_loan_balance($loan->id);
                                    $lump_sum_remaining = $this->ci->debtors_m->get_loan_lump_sum_as_date($loan->id);;

                                    //queue the invoice
                                    $invoice_queue = $this->ci->loan_invoices_m->insert_loan_invoicing_queue(array(
                                        'debtor_loan_id'   =>  $loan->id,
                                        'invoice_id'    =>  $fine_id,
                                        'invoice_no'    =>  'INV-'.$invoice_number,
                                        'debtor_id'     =>  $loan->debtor_id,
                                        'due_date'  =>  $date,
                                        'invoice_type'  => 3,
                                        'invoice_date'  =>  $date,
                                        'amount_payable'    =>  $amount,
                                        'lump_sum_remaining'    =>  $lump_sum_remaining,
                                        'loan_balance'  =>  $loan_balance,
                                        'group_id'  =>  $loan->group_id,
                                        'description'  =>   'Outstanding loan balance fine',
                                        'created_on'    =>  time(),
                                    ));

                                    if($invoice_queue){
                                        ++$successful_invoice_queue_entry;
                                    }
                                    else{
                                        ++$unsuccessful_invoice_queue_entry;
                                    }

                                    unset($invoice_queue);
                                    ++$successful_statement_entry;
                                }
                                else{
                                    ++$unsuccessful_statement_entry;
                                }

                                unset($statement_id);
                                ++$successful_invoice;
                            }
                            else{
                               ++$unsuccessful_invoice; 
                            }
                            unset($fine_id);
                        }


                    ++$successful_loops;
                }else{
                    ++$unsuccessful_loops;
                }
            }
        }



        if($successful_loops){
            echo $successful_loops.' Loan Balances loops gone throw<br/>';
        }
        if($unsuccessful_loops){
            echo $unsuccessful_loops.' Loan Balances loops failed<br/>';
        }
        if($successful_invoice){
            echo $successful_invoice.' Loan invoices created<br/>';
        }
        if($unsuccessful_invoice){
            echo $unsuccessful_invoice.' Loan invoices not created<br/>';
        }
        if($successful_statement_entry){
            echo $successful_statement_entry.' Loan statements created<br/>';
        }
        if($unsuccessful_statement_entry){
            echo $unsuccessful_statement_entry.' Loan statements not created<br/>';
        }
        if($successful_invoice_queue_entry){
            echo $successful_invoice_queue_entry.' Loan invoices queued<br/>';
        }
        if($unsuccessful_invoice_queue_entry){
            echo $unsuccessful_invoice_queue_entry.' Loan invoices not queued<br/>';
        }

         echo date('dmY',$date);
    }

    function process_outstanding_loan_balance_invoices_queue($limit=0){
        if($limit){

        }else{
            $limit=40;
        }

        $outstanding_loan_balance_fine_invoice_list = $this->ci->loan_invoices_m->get_queued_outstanding_loan_balance_invoices_fine_list($limit);
        $successful_invoice_sent = 0;
        $unsuccessful_invoice_sent = 0;
        $successful_deleted_queue = 0;
        $unsuccessful_deleted_queue = 0;
        if($outstanding_loan_balance_fine_invoice_list){
            foreach ($outstanding_loan_balance_fine_invoice_list as $fine_invoice) {
                $sms_body = '';
                $email_body = '';
                $sms_id = '';
                $email_id = '';
                $group_currency = $this->ci->loans_m->get_this_group_currency($fine_invoice->group_id);
                $group = $this->ci->groups_m->get($fine_invoice->group_id);
                $notification_id = 0;
                $fine_invoice_sent = array();
                if($fine_invoice->member_id){
                    $member = $this->ci->members_m->get_group_member($fine_invoice->member_id,$fine_invoice->group_id);
                    $total_installment_payable = $this->ci->loan_invoices_m->get_total_installment_loan_payable($fine_invoice->loan_id);
                    $total_fines = $this->ci->loan_invoices_m->get_total_loan_fines_payable($fine_invoice->loan_id);
                    $total_paid = $this->ci->loan_repayments_m->get_loan_total_payments($fine_invoice->loan_id);
                    $loan_balance =$this->ci->loans_m->get_loan_balance($fine_invoice->loan_id);
                    $loan_borrowed = $this->ci->loans_m->get_loan_borrowed($fine_invoice->loan_id);
                    $loan_details = array(
                            'total_installment_payable' => $total_installment_payable,
                            'total_fines' => $total_fines,
                            'total_paid' => $total_paid,
                            'loan_balance' => $loan_balance,
                            'loan_borrowed' => $loan_borrowed,
                        );
                    $loan = $this->ci->loans_m->get($fine_invoice->loan_id,$fine_invoice->group_id);
                    // if($loan->sms_notifications_enabled){
                    //    $sms_id = $this->ci->messaging->send_sms_loan_oustanding_balance_invoice($fine_invoice,$member,$group,$group_currency);
                    // }
                    // if($loan->email_notifications_enabled){
                    //    $email_id = $this->ci->messaging->send_email_loan_oustanding_balance_invoice($fine_invoice,$member,$group,$group_currency,$loan_details);
                    // }
                    $member = $this->ci->members_m->get_group_member($fine_invoice->member_id,$fine_invoice->group_id);
                    $notification_id = $this->ci->notifications->create(
                        $fine_invoice->description,
                        'You have been fined '.$group_currency.' '.number_to_currency($fine_invoice->amount_payable)." for your 'Unpaid Outstanding Loan Balance'. This fine is due ".timestamp_to_receipt($fine_invoice->due_date).'. Total Unpaid loan balance is '.$group_currency.' '.number_to_currency($fine_invoice->loan_balance).' and the Lump Sum Loan amount remaining is '.$group_currency.' '.number_to_currency($fine_invoice->lump_sum_remaining),
                        $this->ci->ion_auth->get_user($member->user_id),$fine_invoice->member_id,
                        $member->user_id,
                        $fine_invoice->member_id,
                        $fine_invoice->group_id,
                        'View Invoice',
                        'group/loans/loan_statement/'.$fine_invoice->loan_id,3,'','','','','','','','','','',$fine_invoice->loan_id);

                    $fine_invoice_sent = $this->ci->loan_invoices_m->insert_loan_invoices_sent(array(
                            'loan_id' =>$fine_invoice->loan_id,
                            'invoice_id'=>$fine_invoice->invoice_id,
                            'invoice_no'=>$fine_invoice->invoice_no,
                            'invoice_type'=>$fine_invoice->invoice_type,
                            'member_id'=>$fine_invoice->member_id,
                            'group_id'=>$fine_invoice->group_id,
                            'invoice_date'=>$fine_invoice->invoice_date,
                            'due_date' => $fine_invoice->due_date,
                            'amount_payable'=>$fine_invoice->amount_payable,
                            'loan_balance'=>$fine_invoice->loan_balance,
                            'lump_sum_remaining'=>$fine_invoice->lump_sum_remaining,
                            'description'=>$fine_invoice->description,
                            'notification_created'=>$notification_id,
                            'email_sent'=>$email_id,
                            'email_body'=>$email_body,
                            'sms_sent'=>$sms_id,
                            'sms_message'=>$sms_body,
                            'created_on'=>time(),
                        ));

                    if($fine_invoice_sent){
                        $delete_queue = $this->ci->loan_invoices_m->delete_queued_loan_invoices($fine_invoice->id);
                        if($delete_queue)
                        {
                            ++$successful_deleted_queue;
                        }else{
                            ++$unsuccessful_deleted_queue;
                        }
                        ++$successful_invoice_sent;
                        unset($delete_queue);
                    }else{
                        ++$unsuccessful_invoice_sent;
                    }
                }else if($fine_invoice->debtor_id){
                    $debtor = $this->ci->debtors_m->get($fine_invoice->debtor_id,$fine_invoice->group_id);
                    $total_installment_payable = $this->ci->debtors_m->get_total_installment_loan_payable($fine_invoice->debtor_loan_id);
                    $total_fines = $this->ci->debtors_m->get_total_loan_fines_payable($fine_invoice->debtor_loan_id);
                    $total_paid = $this->ci->debtors_m->get_loan_total_payments($fine_invoice->debtor_loan_id);
                    $loan_balance =$this->ci->debtors_m->get_loan_balance($fine_invoice->debtor_loan_id);
                    $loan_borrowed = $this->ci->debtors_m->get_loan_borrowed($fine_invoice->debtor_loan_id);

                    $loan_details = array(
                            'total_installment_payable' => $total_installment_payable,
                            'total_fines' => $total_fines,
                            'total_paid' => $total_paid,
                            'loan_balance' => $loan_balance,
                            'loan_borrowed' => $loan_borrowed,
                        );
                    $debtor_loan = $this->ci->debtors_m->get($fine_invoice->loan_id,$fine_invoice->group_id);
                    // if($debtor_loan->sms_notifications_enabled){
                    //    $sms_id = $this->ci->messaging->send_sms_loan_oustanding_balance_invoice($fine_invoice,$debtor,$group,$group_currency,TRUE);
                    // }
                    // if($debtor_loan->email_notifications_enabled){
                    //    $email_id = $this->ci->messaging->send_email_loan_oustanding_balance_invoice($fine_invoice,$debtor,$group,$group_currency,$loan_details,TRUE);
                    // }

                    $delete_queue = $this->ci->loan_invoices_m->delete_queued_loan_invoices($fine_invoice->id);
                    if($delete_queue){
                        ++$successful_deleted_queue;
                    }else{
                        ++$unsuccessful_deleted_queue;
                    }
                    ++$successful_invoice_sent;
                    unset($delete_queue);
                }
                unset($notification_id);

            }
        }else{
            echo 'There are no outstanding loan balance fine invoices to send today';
        }

        if($successful_invoice_sent){
            echo $successful_invoice_sent.' outstanding loan balance fine invoices were processed</br>';
        }else if($unsuccessful_invoice_sent){
            echo $unsuccessful_invoice_sent.' outstanding loan balance fine invoices were not processed</br>';
        }

        if($successful_deleted_queue){
            echo $successful_deleted_queue.' outstanding loan balance fine invoices were processed and removed from queue</br>';
        }else if($unsuccessful_deleted_queue){
            echo $unsuccessful_deleted_queue.' outstanding loan balance fine invoices were processed and not removed from queue</br>';
        }
    }

    // function update_loan_invoices($loan_id=0,$is_a_debtor=FALSE){
    //     if($is_a_debtor){
    //         $invoices = $this->ci->debtors_m->get_loan_installments($loan_id);
    //         if($invoices){
    //             foreach ($invoices as $invoice) {
    //                 $this->ci->debtors_m->update_loan_invoices($invoice->id,array('status'=>1,'amount_paid'=>0,'modified_on'=>time()));
    //             }
    //         }
    //         $this->recalculate_loan_balance_invoice_for_reducing($loan_id,$is_a_debtor);
    //         $this->pay_edited_invoices($loan_id,'',$is_a_debtor);
    //         $this->update_loan_status($loan_id,$is_a_debtor);
    //         $this->update_loan_status($loan_id,$is_a_debtor);
    //     }else{
    //         $invoices = $this->ci->loan_invoices_m->get_loan_installments($loan_id);
    //         if($invoices){
    //             foreach ($invoices as $invoice) {
    //                  $this->ci->loan_invoices_m->update($invoice->id,array('status'=>'','amount_paid'=>0,'modified_on'=>time()));
    //             }        
    //         }
    //         $this->recalculate_loan_balance_invoice_for_reducing($loan_id);
    //         $this->pay_edited_invoices($loan_id);
    //         $this->update_loan_status($loan_id);
    //         $this->update_loan_status($loan_id);
    //         return TRUE;      
    //     }
    //     return true;
    // }

    function update_loan_invoices($loan_id=0,$is_a_debtor=FALSE){
        if($is_a_debtor){
            $loan = $this->ci->debtors_m->get_loan($loan_id);
            if($loan){
                $invoices = $this->ci->debtors_m->get_loan_installments($loan->id,$loan->group_id);
                if($invoices){
                    foreach ($invoices as $invoice) {
                        $this->ci->debtors_m->update_loan_invoices($invoice->id,array('status'=>1,'amount_paid'=>0,'modified_on'=>time()));
                    }
                }
                $this->recalculate_loan_balance_invoice_for_reducing($loan,$is_a_debtor);
                $this->pay_edited_invoices($loan,'',$is_a_debtor);
                $this->update_loan_status($loan,$is_a_debtor);
                $this->update_loan_status($loan,$is_a_debtor);
            }
            return TRUE;
        }else{
            $loan = $this->ci->loans_m->get($loan_id);
            if($loan){
                $invoices = $this->ci->loan_invoices_m->get_loan_installments($loan->id,$loan->group_id);
                if($invoices){
                    foreach ($invoices as $invoice) {
                        
                        $this->ci->loan_invoices_m->update($invoice->id,array('status'=>'','amount_paid'=>0,'modified_on'=>time()));
                        
                        //$member = $this->ci->members_m->get($invoice->member_id);
                    }        
                }
                $this->recalculate_loan_balance_invoice_for_reducing($loan);
               
                $this->pay_edited_invoices($loan);
                $this->update_loan_status($loan);
                $this->update_loan_status($loan);
            }
            return TRUE;      
        }
        return true;
    }



    /*********************Loan Repayment*************************/

    function record_loan_repayment($group_id=0,$deposit_date=0,$member=array(),$loan_id,$account_id=0,$deposit_method='',$description='',$amount=0,$send_sms_notification=0,$send_email_notification=0,$created_by=array(),$member_user_id=0,$transaction_alert_id = 0,$is_a_back_dating_record = FALSE,$update_loan_invoices = 1){
        if($amount && $group_id && $deposit_date && $member && $loan_id && $account_id){
            $loan = $this->ci->loans_m->get($loan_id);
            $deposited_amount = $amount;
            if($loan){
                //$this->recalculate_reducing_balance($loan_id,$amount,$deposit_date);
                /*
                *   1. insert loan repayment
                    2. pay for invoice
                    3. insert into statement
                    4. get wrongly fined invoices
                    5. update loan status
                    6. send notifications
                        a) Chamasoft Notification
                        b) email notification
                        c) sms notification
                */
                        
                $data = array(
                        'loan_id'   =>  $loan_id,
                        'group_id'  =>  $group_id,
                        'member_id' =>  $member->id,
                        'account_id'=>  $account_id,
                        'receipt_date'=>$deposit_date,
                        'payment_method'=>$deposit_method,
                        'amount'    =>  $amount,
                        'status'    =>  1,
                        'active'    =>  1,
                        'created_on'=>  time(),
                        'is_a_back_dating_record'=>  $is_a_back_dating_record?1:0,
                    );

                $repayment_id =$this->ci->loan_repayments_m->insert($data);
                if($repayment_id){
                    if(preg_match('/bank-/', $account_id)){
                        $type = 17;
                    }else if(preg_match('/sacco-/', $account_id)){
                        $type = 18;
                    }else if(preg_match('/mobile-/', $account_id)){
                        $type = 19;
                    }else if(preg_match('/petty-/', $account_id)){
                        $type = 20;
                    }else{
                        $type = 0;
                    }
                    if($type){
                        $deposit_data = array(
                            'type'=>$type,
                            'group_id'=>$group_id,
                            'deposit_date'=>$deposit_date,
                            'member_id'=>$member->id,
                            'account_id'=>$account_id,
                            'deposit_method'=>$deposit_method,
                            'amount'=>$amount,
                            'description'=>$description,
                            'active'=>1,
                            'created_on'=>time(),
                            'loan_id' => $loan_id,
                            'loan_repayment_id' => $repayment_id,
                            'is_a_back_dating_record'=>  $is_a_back_dating_record?1:0,
                            'transaction_alert_id' => $transaction_alert_id,
                        );
                        $deposit_id = $this->ci->deposits_m->insert($deposit_data);
                        if($deposit_id){
                            if($this->ci->transactions->deposit($group_id,$deposit_id,$type,$deposit_date,$account_id,$amount,$member->id,'','','','','','','','','','','','',$loan_id,$repayment_id,$transaction_alert_id,$is_a_back_dating_record)){
                                $statement_entry_id =$this->ci->loans_m->insert_loan_statement(array(
                                                'member_id' =>  $member->id,
                                                'group_id'  =>  $group_id,
                                                'transaction_date' =>   $deposit_date,
                                                'transaction_type'  =>  4,
                                                'payment_method'    =>  $deposit_method,
                                                'loan_id'   =>  $loan_id,
                                                'loan_payment_id'   =>  $repayment_id,
                                                'account_id'    =>  $account_id,
                                                'amount'        =>  $amount,
                                                'balance'       =>  0,
                                                'active'        =>  1,
                                                'status'        =>  1,
                                                'created_on'    =>  time(),
                                                'is_a_back_dating_record'=>  $is_a_back_dating_record?1:0,

                                            ));
                                if($statement_entry_id){
                                    //get all fines before this payment date
                                    /*$this->recalculate_reducing_balance($loan_id,$amount,$deposit_date);
                                    $invoices = $this->ci->loan_invoices_m->get_unpaid_loan_installments($loan_id);
                                    $amount = $amount;
                                    $parent_id = '';
                                    $count=0;
                                    foreach ($invoices as $invoice) {
                                        if($amount){
                                            if($invoice->type==1){
                                                if($invoice->status!=2)
                                                {
                                                    $amount_paid = $invoice->amount_paid;
                                                    $amount_payable = $invoice->amount_payable-$amount_paid;
                                                    if($amount_payable<=0){
                                                        $status =2;
                                                        $amount_paid = $amount_paid;
                                                        if(date('ymd',$invoice->due_date)>=date('ymd',$deposit_date))
                                                        {
                                                            //check if this invoice has fines
                                                            if($this->ci->loan_invoices_m->void_children_invoices_fined_wrongly($invoice->id))
                                                            {
                                                                $parent_id = $invoice->id;
                                                            }
                                                        }
                                                    }else 
                                                    {
                                                        if($amount>=$amount_payable){
                                                            $amount = $amount-$amount_payable;
                                                            $status =2;
                                                            $amount_paid = $invoice->amount_payable;
                                                            if(date('ymd',$invoice->due_date)>=date('ymd',$deposit_date))
                                                            {
                                                                //check if this invoice has fines
                                                                 if($this->ci->loan_invoices_m->void_children_invoices_fined_wrongly($invoice->id)){
                                                                    $parent_id = $invoice->id;
                                                                 }
                                                            }
                                                        }
                                                        else{
                                                            $status = 1;
                                                            $amount_paid = $amount+$amount_paid;
                                                             $amount = 0;
                                                        }
                                                    }
                                                    $this->ci->loan_invoices_m->update($invoice->id,array('status'=>$status,'amount_paid'=>$amount_paid,'modified_on'=>time()));
                                                }else{
                                                    continue;
                                                }
                                            }
                                            else if(isset($parent_id) && $invoice->fine_parent_loan_invoice_id!=$parent_id){
                                                if($invoice->active){
                                                    if($loan->enable_loan_fine_deferment){
                                                        if($this->ci->loan_invoices_m->count_all_loan_invoices($loan_id)==0){
                                                            //all payments were made
                                                            if($invoice->status!=2)
                                                            {
                                                                $amount_paid = $invoice->amount_paid;
                                                                $amount_payable = $invoice->amount_payable-$amount_paid;
                                                                if($amount_payable<=0){
                                                                    $status =2;
                                                                    $amount_paid = $amount_paid;
                                                                    if(date('ymd',$invoice->due_date)>=date('ymd',$deposit_date))
                                                                    {
                                                                        //check if this invoice has fines
                                                                        if($this->ci->loan_invoices_m->void_children_invoices_fined_wrongly($invoice->id)){
                                                                            $parent_id = $invoice->id;
                                                                        }
                                                                    }
                                                                }else 
                                                                {
                                                                    if($amount>=$amount_payable){
                                                                        $amount = $amount-$amount_payable;
                                                                        $status =2;
                                                                        $amount_paid = $invoice->amount_payable;
                                                                        if(date('ymd',$invoice->due_date)>=date('ymd',$deposit_date))
                                                                        {
                                                                            //check if this invoice has fines
                                                                            if($this->ci->loan_invoices_m->void_children_invoices_fined_wrongly($invoice->id)){
                                                                               $parent_id = $invoice->id; 
                                                                            }
                                                                        }
                                                                    }
                                                                    else{
                                                                        $status = 1;
                                                                        $amount_paid = $amount+$amount_paid;
                                                                         $amount = 0;
                                                                    }
                                                                }
                                                               $this->ci->loan_invoices_m->update($invoice->id,array('status'=>$status,'amount_paid'=>$amount_paid,'modified_on'=>time()));
                                                            }else{
                                                                continue;
                                                            }
                                                        }else{
                                                            continue;
                                                        }
                                                    }else{
                                                        if($invoice->status!=2)
                                                        {
                                                            $amount_paid = $invoice->amount_paid;
                                                            $amount_payable = $invoice->amount_payable-$amount_paid;
                                                            if($amount_payable<=0){
                                                                $status =2;
                                                                $amount_paid = $amount_paid;
                                                                if(date('ymd',$invoice->due_date)>=date('ymd',$deposit_date))
                                                                {
                                                                    //check if this invoice has fines
                                                                    if($this->ci->loan_invoices_m->void_children_invoices_fined_wrongly($invoice->id)){
                                                                        $parent_id = $invoice->id;
                                                                    }
                                                                }
                                                            }else 
                                                            {
                                                                if($amount>=$amount_payable){
                                                                    $amount = $amount-$amount_payable;
                                                                    $status =2;
                                                                    if(date('ymd',$invoice->due_date)>=date('ymd',$deposit_date))
                                                                    {
                                                                        //check if this invoice has fines
                                                                        if($this->ci->loan_invoices_m->void_children_invoices_fined_wrongly($invoice->id)){
                                                                            $parent_id = $invoice->id;
                                                                        }
                                                                    }
                                                                    $amount_paid = $invoice->amount_payable;
                                                                }
                                                                else{
                                                                    $status = 1;
                                                                    $amount_paid = $amount+$amount_paid;
                                                                     $amount = 0;
                                                                }
                                                            }
                                                            $this->ci->loan_invoices_m->update($invoice->id,array('status'=>$status,'amount_paid'=>$amount_paid,'modified_on'=>time()));
                                                        }else{
                                                            continue;
                                                        }

                                                    }
                                                }
                                                else{
                                                    continue;
                                                }
                                            }
                                            if($amount>0){
                                                 $this->recalculate_reducing_balance($loan_id,$amount,$deposit_date);
                                            }
                                        }else{
                                            break;
                                        }
                                    }
                                    $this->outstanding_loan_balance_fine_fixer($loan_id);

                                    */
                                    if($update_loan_invoices == 1){
                                        $this->update_loan_invoices($loan_id);
                                        $loan_balance = $this->ci->loans_m->get_loan_balance($loan_id);

                                        $total_installment_payable = $this->ci->loan_invoices_m->get_total_installment_loan_payable($loan_id);
                                        $total_fines = $this->ci->loan_invoices_m->get_total_loan_fines_payable($loan_id);
                                        $total_paid = $this->ci->loan_repayments_m->get_loan_total_payments($loan_id);
                                        $loan_balance =$this->ci->loans_m->get_unpaid_loan_balance($loan_id);
                                        $loan_borrowed = $this->ci->loans_m->get_loan_borrowed($loan_id);

                                        $loan_details = array(
                                                'total_installment_payable' => $total_installment_payable,
                                                'total_fines' => $total_fines,
                                                'total_paid' => $total_paid,
                                                'loan_balance' => $loan_balance,
                                                'loan_borrowed' => $loan_borrowed,
                                                'id' => $loan_id,
                                            );

                                        $group = $this->ci->groups_m->get($group_id);

                                        $this->group_currency = $this->ci->loans_m->get_this_group_currency($group_id);

                                        if($send_sms_notification){
                                            $this->ci->messaging->notify_loan_repayment_sms($member,$deposited_amount,$deposit_date,$deposit_method,$group_id,$loan_balance,$created_by);
                                        }

                                        if($send_email_notification){
                                            $this->ci->messaging->notify_loan_repayment_email($member,$deposited_amount,$deposit_date,$deposit_method,$group,$loan_details,$this->group_currency,$created_by);
                                        }

                                        $this->ci->notifications->create(
                                            'Loan repayment successfully repaid.',
                                            'Your loan repayment of  '.$this->group_currency.' '.number_to_currency($deposited_amount).' paid on '.timestamp_to_date($deposit_date).' has been successfully recorded. Your loan installment reapyment balance is  '.$this->group_currency.' '.number_to_currency($loan_balance),
                                            $this->ci->ion_auth->get_user($member->user_id),
                                            $member->id,
                                            $member->user_id,
                                            $member->id,
                                            $group_id,
                                            'View Receipt',
                                            'group/loans/view_receipt/'.$repayment_id,
                                            9,
                                            0,
                                            $deposit_id,'','','','','','','','',$loan_id
                                        );
                                    }
                                    return TRUE;     
                                }else{
                                    //void the payment made

                                    $this->ci->session->set_flashdata('error','unable to create repayment statement');
                                    return FALSE; 
                                }

                            }else{
                                $this->ci->session->set_flashdata('error','unable to create transaction statement');
                                return FALSE;
                            }
                        }else{

                            $this->ci->session->set_flashdata('error','Unable to create a deposit');
                            return FALSE;
                        }
                    }
                }else{
                    $this->ci->session->set_flashdata('error','unable to add the loan repayment');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Unable to make payments. No loan found');
                return FALSE; 
            }
        }else{
            $this->ci->session->set_flashdata('error','Sorry, some parameters are missing '.$account_id);
            return FALSE;
        }
    }

    function record_debtor_loan_repayment($group_id=0,$deposit_date=0,$debtor=array(),$loan_id,$account_id=0,$deposit_method='',$description='',$amount=0,$send_sms_notification=0,$send_email_notification=0,$created_by=array(),$transaction_alert_id = 0,$is_a_back_dating_record = FALSE){
        if($amount && $group_id && $deposit_date && $debtor && $loan_id && $account_id){
            $loan = $this->ci->debtors_m->get_loan($loan_id);
            $deposited_amount = $amount;
            if($loan){
                //$this->recalculate_reducing_balance($loan_id,$amount,$deposit_date);
                /*
                *   1. insert loan repayment
                    2. pay for invoice
                    3. insert into statement
                    4. get wrongly fined invoices
                    5. update loan status
                    6. send notifications
                        a) Chamasoft Notification
                        b) email notification
                        c) sms notification
                */
                $data = array(
                        'debtor_loan_id'   =>  $loan_id,
                        'group_id'  =>  $group_id,
                        'debtor_id' =>  $debtor->id,
                        'account_id'=>  $account_id,
                        'receipt_date'=>$deposit_date,
                        'payment_method'=>$deposit_method,
                        'amount'    =>  $amount,
                        'status'    =>  1,
                        'active'    =>  1,
                        'created_on'=>  time(),
                        'is_a_back_dating_record'=>  $is_a_back_dating_record?1:0,
                    );

                $repayment_id =$this->ci->debtors_m->insert_loan_repayments($data);
                if($repayment_id){
                    if(preg_match('/bank-/', $account_id)){
                        $type = 49;
                    }else if(preg_match('/sacco-/', $account_id)){
                        $type = 50;
                    }else if(preg_match('/mobile-/', $account_id)){
                        $type = 51;
                    }else if(preg_match('/petty-/', $account_id)){
                        $type = 52;
                    }else{
                        $type = 0;
                    }
                    if($type){
                        $deposit_data = array(
                            'type'=>$type,
                            'group_id'=>$group_id,
                            'deposit_date'=>$deposit_date,
                            'debtor_id'=>$debtor->id,
                            'account_id'=>$account_id,
                            'deposit_method'=>$deposit_method,
                            'amount'=>$amount,
                            'description'=>$description,
                            'active'=>1,
                            'created_on'=>time(),
                            'debtor_loan_id' => $loan_id,
                            'debtor_loan_repayment_id' => $repayment_id,
                            'is_a_back_dating_record'=>  $is_a_back_dating_record?1:0,
                            'transaction_alert_id' => $transaction_alert_id,
                        );
                        $deposit_id = $this->ci->deposits_m->insert($deposit_data);
                        if($deposit_id){
                            if($this->ci->transactions->deposit($group_id,$deposit_id,$type,$deposit_date,$account_id,$amount,'','','','','','','','','','','','','','','',$transaction_alert_id,$is_a_back_dating_record,$loan_id,$debtor->id,$repayment_id)){
                                $statement_entry_id =$this->ci->debtors_m->insert_loan_statement(array(
                                                'debtor_id' =>  $debtor->id,
                                                'group_id'  =>  $group_id,
                                                'transaction_date' =>   $deposit_date,
                                                'transaction_type'  =>  4,
                                                'payment_method'    =>  $deposit_method,
                                                'debtor_loan_id'   =>  $loan_id,
                                                'debtor_loan_payment_id'   =>  $repayment_id,
                                                'account_id'    =>  $account_id,
                                                'amount'        =>  $amount,
                                                'balance'       =>  0,
                                                'active'        =>  1,
                                                'status'        =>  1,
                                                'created_on'    =>  time(),
                                                'is_a_back_dating_record'=>  $is_a_back_dating_record?1:0,

                                            ));
                                if($statement_entry_id){
                                    $this->update_loan_invoices($loan_id,TRUE);
                                    $this->update_loan_invoices($loan_id,TRUE);
                                    $loan_balance = $this->ci->debtors_m->get_loan_balance($loan_id);

                                    $total_installment_payable = $this->ci->debtors_m->get_total_installment_loan_payable($loan_id);
                                    $total_fines = $this->ci->debtors_m->get_total_loan_fines_payable($loan_id);
                                    $total_paid = $this->ci->debtors_m->get_loan_total_payments($loan_id);
                                    $loan_balance =$this->ci->debtors_m->get_unpaid_loan_balance($loan_id);
                                    $loan_borrowed = $this->ci->debtors_m->get_loan_borrowed($loan_id);

                                    $loan_details = array(
                                            'total_installment_payable' => $total_installment_payable,
                                            'total_fines' => $total_fines,
                                            'total_paid' => $total_paid,
                                            'loan_balance' => $loan_balance,
                                            'loan_borrowed' => $loan_borrowed,
                                        );

                                    $group = $this->ci->groups_m->get($group_id);

                                    $this->group_currency = $this->ci->loans_m->get_this_group_currency($group_id);

                                    if($send_sms_notification){
                                        //$this->ci->messaging->notify_loan_repayment_sms($member,$deposited_amount,$deposit_date,$deposit_method,$group_id,$loan_balance,$created_by);
                                    }

                                    if($send_email_notification){
                                        //$this->ci->messaging->notify_loan_repayment_email($member,$deposited_amount,$deposit_date,$deposit_method,$group,$loan_details,$this->group_currency,$created_by);
                                    }
                                    return TRUE;     
                                }else{
                                    //void the payment made

                                    $this->ci->session->set_flashdata('error','unable to create repayment statement');
                                    return FALSE; 
                                }

                            }else{
                                $this->ci->session->set_flashdata('error','unable to create transaction statement');
                                return FALSE;
                            }
                        }else{

                            $this->ci->session->set_flashdata('error','Unable to create a deposit');
                            return FALSE;
                        }
                    }
                }else{

                    $this->ci->session->set_flashdata('error','unable to add the loan repayment');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Unable to make payments. No loan found');
                return FALSE; 
            }
        }else{
            $this->ci->session->set_flashdata('error','Sorry, some parameters are missing');
            return FALSE;
        }
    }

    // function update_loan_status($loan_id=0,$is_a_debtor=FALSE){
    //     if($loan_id){
    //         if($is_a_debtor){
    //             $balance = $this->ci->debtors_m->get_unpaid_loan_balance($loan_id);
    //             if(round($balance,1)<=1){
    //                 $this->ci->debtors_m->update_loan($loan_id,array('is_fully_paid'=>1,'modified_on'=>time()));
    //             }else{
    //                 $this->ci->debtors_m->update_loan($loan_id,array('is_fully_paid'=>NULL,'modified_on'=>time()));
    //             }
    //         }else{
    //             $balance = $this->ci->loans_m->get_unpaid_loan_balance($loan_id);
    //             if(round($balance,1)<=1){
    //                 $this->ci->loans_m->update($loan_id,array('is_fully_paid'=>1,'modified_on'=>time()));
    //             }else{
    //                 $this->ci->loans_m->update($loan_id,array('is_fully_paid'=>NULL,'modified_on'=>time()));
    //             }
    //        }
    //        return TRUE;
    //     }else{
    //         return FALSE;
    //     }
    // }

    function update_loan_status($loan='',$is_a_debtor=FALSE){
        if($loan){
            if($is_a_debtor){
                $balance = $this->ci->debtors_m->get_unpaid_loan_balance($loan->id,$loan->group_id);
                if(floor($balance)<=1){
                    $this->ci->debtors_m->update_loan($loan->id,array('is_fully_paid'=>1,'modified_on'=>time()));
                }else{
                    $this->ci->debtors_m->update_loan($loan->id,array('is_fully_paid'=>NULL,'modified_on'=>time()));
                }
            }else{
                $balance = $this->ci->loans_m->get_unpaid_loan_balance($loan->id,$loan->group_id);
                if(floor($balance)<=1){
                    $this->ci->loans_m->update($loan->id,array('is_fully_paid'=>1,'modified_on'=>time()));
                }else{
                    $this->ci->loans_m->update($loan->id,array('is_fully_paid'=>NULL,'modified_on'=>time()));
                }
           }
           return TRUE;
        }else{
            return FALSE;
        }
    }

    function update_outstanding_balance_status($loan_id=0){
        if($loan_id){
            $loan = $this->ci->loans_m->get($loan_id);
            if($loan && $loan->enable_outstanding_loan_balance_fines){
               $repayment_date = $this->ci->loan_repayments_m->last_loan_repayment_date($loan_id);
               if($repayment_date<time() && $this->ci->loan_invoices_m->loan_invoices_and_fines($loan_id)==0){
                    if($this->ci->loan_invoices_m->has_outstanding_balance($loan_id)){
                        $this->ci->loan_invoices_m->void_outstanding_balance($loan_id);
                    }else{
                        return TRUE;
                    }
               }else{
                //
               }
            }else{
                return TRUE;
            }
        }else{
            return FALSE;
        }
    }

    function void_loan_repayment($repayment_id=0,$modified_by=0,$loan_id=0,$void_loan=FALSE,$unreconcile_transaction_alerts = TRUE,$update_loan_invoices=1){
        if($repayment_id){     

            $post = $this->ci->loan_repayments_m->get($repayment_id);
            if($post){


                 $paid_amount = $post->amount;
                /*
                    1. void the repayment
                    2. void the statement
                    3. subtract amount paid on the invoices
                    4. fine loan invoices again and resend
                    5. fix the loan
                */
                $void_id =$this->ci->loan_repayments_m->update($repayment_id,array('active'=>'','modified_on'=>time()));
                if($void_id){
                    $this->ci->transactions->void_loan_repayment_deposit('','',$post->id,$post->group_id,$unreconcile_transaction_alerts,$update_loan_invoices);
                    $statement_id =$this->ci->loans_m->update_statement_payment($repayment_id);
                    if($statement_id){
                        // //here
                        // $invoices = $this->ci->loan_invoices_m->get_paid_invoices($post->loan_id);
                        // if($invoices){
                        //     $amount = $paid_amount;
                        //     foreach ($invoices as $invoice) 
                        //     {
                        //         if($amount>=0){
                        //             if($invoice->amount_paid<=$amount){
                        //                 if($this->ci->loan_invoices_m->update($invoice->id,array('status'=>'','amount_paid'=>0,'modified_by'=>$modified_by,'modified_on'=>time()))){
                        //                     $amount =$amount-$invoice->amount_paid;
                        //                 }
                        //             }else{
                        //                 $amount_paid = $invoice->amount_paid-$amount;
                        //                 $amount_paid = round($amount_paid,2);
                        //                 if($this->ci->loan_invoices_m->update($invoice->id,array('status'=>'','amount_paid'=>$amount_paid,'modified_by'=>$modified_by,'modified_on'=>time()))){
                        //                     $amount =0;
                        //                 }
                        //             }
                        //         }else{
                        //             break;
                        //         }
                        //     }
                        //     if($this->fix_loan_invoices_fine($post->loan_id)){
                        //         $this->update_loan_invoices($post->loan_id);
                        //         $this->update_loan_status($post->loan_id);
                        //         $this->outstanding_loan_balance_fine_fixer($post->loan_id);
                        //         $this->loan_fixer($post->loan_id); 
                        //         $this->recalculate_loan_balance_invoice_for_reducing($post->loan_id);
                        //         $this->ci->session->set_flashdata('success','Payment successfully voided');
                        //         return TRUE;
                        //     }else{
                        //         $this->update_loan_invoices($post->loan_id);
                        //         $this->update_loan_status($post->loan_id);
                        //         $this->outstanding_loan_balance_fine_fixer($post->loan_id);
                        //         $this->loan_fixer($post->loan_id);
                        //         $this->update_loan_status($post->loan_id);
                        //         $this->recalculate_loan_balance_invoice_for_reducing($post->loan_id);
                        //         $this->ci->session->set_flashdata('success','Payment successfully voided');
                        //         return TRUE;
                        //     }
                        // }else{
                        //     return TRUE;
                        // }    
                        // $this->fix_loan_invoices_fine($post->loan_id);
                        // $this->outstanding_loan_balance_fine_fixer($post->loan_id);
                        //$this->recalculate_loan_balance_invoice_for_reducing($post->loan_id);
                        if($update_loan_invoices==1){
                            $this->update_loan_invoices($post->loan_id);  
                            $this->update_loan_invoices($post->loan_id);  
                        }
                        return TRUE;              
                    }else{
                       $this->ci->session->set_flashdata('error','unable to void the loan repayment statement');
                        return FALSE;  
                    }
                }else{
                    $this->ci->session->set_flashdata('error','unable to void the loan repayment');
                    return FALSE; 
                }
            }else{
               $this->ci->session->set_flashdata('error','Loan repayment not found');
                return FALSE; 
            }

        }else if($loan_id && $void_loan==TRUE){
             
            //void all loan repayments
            $loan = $this->ci->loans_m->get($loan_id);

            if($loan && $loan->active==1){
                if($this->ci->transactions->void_loan($loan_id,$loan->group_id)){
                    $result = $this->ci->loan_repayments_m->get_loan_repayments($loan->id);
                    if($result){
                        foreach ($result as $res) {
                            if($this->ci->transactions->void_loan_repayment_deposit('','',$res->id,$loan->group_id,$unreconcile_transaction_alerts,$update_loan_invoices)){
                                $this->ci->loan_repayments_m->update($res->id,array('active'=>NULL,'status'=>NULL,'modified_on'=>time()));
                            }
                        }
                      
                        return TRUE;
                    }
                    else
                    {
                        return TRUE;
                    }
                }else{
                }
                
            }else{
                $this->ci->session->set_flashdata('error','Loan already voided');
                return FALSE;
            }  
        }else{
            $this->ci->session->set_flashdata('error','Loan repayment can not be voided. Some parameters are missing');
            return FALSE;
        }
    }


    function void_external_lending_loan_repayment($repayment_id=0,$modified_by=0,$loan_id=0,$void_loan=FALSE){
        if($repayment_id){
            $post = $this->ci->debtors_m->get_payment($repayment_id);
            if($post){
                 $paid_amount = $post->amount;
                /*
                    1. void the repayment
                    2. void the statement
                    3. subtract amount paid on the invoices
                    4. fine loan invoices again and resend
                    5. fix the loan
                */
                $void_id =$this->ci->debtors_m->update_loan_repayment($repayment_id,array('active'=>'','modified_on'=>time()));
                if($void_id){
                    $statement_id =$this->ci->debtors_m->update_statement_payment($repayment_id);
                    if($statement_id){
                        //here
                        $invoices = $this->ci->debtors_m->get_paid_invoices($post->debtor_loan_id);
                        if($invoices){
                            $amount = $paid_amount;
                            foreach ($invoices as $invoice) 
                            {
                                if($amount>=0){
                                    if($invoice->amount_paid<=$amount){
                                        if($this->ci->debtors_m->update_loan_invoices($invoice->id,array('status'=>'','amount_paid'=>0,'modified_by'=>$modified_by,'modified_on'=>time()))){
                                            $amount =$amount-$invoice->amount_paid;
                                        }
                                    }else{
                                        $amount_paid = $invoice->amount_paid-$amount;
                                        $amount_paid = round($amount_paid,2);
                                        if($this->ci->debtors_m->update_loan_invoices($invoice->id,array('status'=>'','amount_paid'=>$amount_paid,'modified_by'=>$modified_by,'modified_on'=>time()))){
                                            $amount =0;
                                        }
                                    }
                                }else{
                                    break;
                                }
                            }
                            if($this->fix_loan_invoices_fine($post->debtor_loan_id,TRUE)){
                                $this->update_loan_invoices($post->debtor_loan_id,TRUE);
                                $this->update_loan_status($post->debtor_loan_id,TRUE);
                                $this->outstanding_loan_balance_fine_fixer($post,TRUE);
                                $this->loan_fixer($post->debtor_loan_id,TRUE); 
                                $this->recalculate_loan_balance_invoice_for_reducing($post->debtor_loan_id,TRUE);
                                $this->ci->session->set_flashdata('success','Payment successfully voided');
                                return TRUE;
                            }else{
                                $this->update_loan_invoices($post->debtor_loan_id,TRUE);
                                $this->update_loan_status($post->debtor_loan_id,TRUE);
                                $this->outstanding_loan_balance_fine_fixer($post,TRUE);
                                $this->loan_fixer($post->debtor_loan_id,TRUE);
                                $this->update_loan_status($post->debtor_loan_id,TRUE);
                                $this->recalculate_loan_balance_invoice_for_reducing($post->debtor_loan_id,TRUE);
                                $this->ci->session->set_flashdata('success','Payment successfully voided');
                                return TRUE;
                            }
                        }else{
                            return TRUE;
                        }                        
                    }else{
                       $this->ci->session->set_flashdata('error','unable to void the loan repayment statement');
                        return FALSE;  
                    }
                }else{
                    $this->ci->session->set_flashdata('error','unable to void the loan repayment');
                    return FALSE; 
                }
            }else{
               return TRUE;
            }
        }else if($loan_id && $void_loan==TRUE){
            //void all loan repayments
            $loan = $this->ci->debtors_m->get_loan($loan_id);
            if($loan){
                if($this->ci->transactions->void_external_loan($loan_id,$loan->group_id)){
                    $result = $this->ci->debtors_m->get_loan_repayments($loan->id);
                    if($result){
                        foreach ($result as $res) {
                            if($this->ci->transactions->void_external_loan_repayment_deposit('','',$res->id)){
                                $this->ci->debtors_m->update_loan_repayment($res->id,array('active'=>NULL,'status'=>NULL,'modified_on'=>time()));
                            }
                        }
                        return TRUE;
                    }
                    else
                    {
                        return TRUE;
                    }
                }
            }else{
                $this->ci->session->set_flashdata('error','Loan already voided');
                return FALSE;
            }  
        }else{
            $this->ci->session->set_flashdata('error','Loan repayment can not be voided. Some parameters are missing');
            return FALSE;
        }
    }




    /***********************************Bank Loans****************************/

    function bank_loan_repayment($bank_loan_id=0,$amount=0,$payment_date=0,$group_id=0,$account_id=0,$payment_method='',$description=0,$created_by=0,$transaction_alert_id = 0,$is_a_back_dating_record = FALSE,$is_bank_loan_interest = 0){
        if($bank_loan_id && $amount && $payment_date && $group_id && $account_id && $description){

            $loan = $this->ci->bank_loans_m->get($bank_loan_id,$group_id);
            if(!$loan){
                $this->ci->session->set_flashdata('error','Bank loan can not be identified. Try again');
                return FALSE;
            }
            else{
                $data = array(
                    'bank_loan_id' => $bank_loan_id,
                    'group_id'  =>  $group_id,
                    'description'  =>  $description,
                    'account_id' => $account_id,
                    'receipt_date' => $payment_date,
                    'payment_method' => $payment_method,
                    'amount' => $amount,
                    'active' => 1,
                    'status' => 1,
                    'created_by' => $created_by,
                    'created_on' => time(),
                    'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                    'is_bank_loan_interest' => $is_bank_loan_interest?1:0,
                );
                $payment_id = $this->ci->bank_loans_m->insert_repayment($data);
                if($payment_id){
                    $balance = $loan->loan_balance - $amount;
                    $loan_update = $this->ci->bank_loans_m->update($loan->id,array('loan_balance'=>$balance,'modified_by'=>$created_by,'modified_on'=>time()));
                    if($loan_update){
                        if($this->ci->transactions->record_bank_loan_repayment_withdrawal($group_id,$bank_loan_id,$payment_date,$account_id,$payment_method,$amount,$description,$transaction_alert_id,$payment_id,$is_a_back_dating_record,$is_bank_loan_interest,0,0,$is_bank_loan_interest)){
                            $this->bank_loan_status($loan->id,$group_id);
                            $this->ci->session->set_flashdata('success','Bank loan repayment successfully recorded');
                            return TRUE;
                        }else{
                            return FALSE;
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Unable to record bank loan repayment');
                        return FALSE;
                    }  
                }else{
                    $this->ci->session->set_flashdata('error','Bank loan repayment not recorded');
                    return FALSE;
                }
            }

        }else{
            $this->ci->session->set_flashdata('error','Bank loan cannot be recorded as some paramenters are missing');
            return FALSE;
        }
    }

    function void_bank_loan_repayment($repayment_id = 0,$created_by=0){
        if($repayment_id){
            $repayment = $this->ci->bank_loans_m->get_bank_loan_repayment($repayment_id);
            if($repayment && $repayment->active==1 && $repayment->status==1){
                $bank_loan = $this->ci->bank_loans_m->get($repayment->bank_loan_id,$repayment->group_id);
                if($bank_loan){
                    $void = $this->ci->bank_loans_m->update_bank_loan_repayment($repayment->id,array('status'=>'','active'=>'','modified_by'=>$created_by,'modified_on'=>time()));
                    if($void){
                        $amount = $repayment->amount;
                        $balance = $bank_loan->loan_balance + $amount;
                        $bank_loan_update = $this->ci->bank_loans_m->update($bank_loan->id,array('loan_balance'=>$balance,'modified_by'=>$created_by,'modified_on'=>time()));
                        if($bank_loan_update){
                            $withdrawal = $this->ci->withdrawals_m->get_group_withdrawal_by_bank_loan_repayment_id($repayment_id,$repayment->group_id);
                            if($withdrawal){
                                if($this->ci->transactions->void_bank_loan_repayment_withdrawal($withdrawal->id,$withdrawal)){
                                    $this->bank_loan_status($bank_loan->id,$bank_loan->group_id);
                                    $this->ci->session->set_flashdata('success','Loan successfully voided.');
                                    return TRUE;
                                }else{
                                    $this->ci->session->set_flashdata('warning','Unable to find withdrawal.');
                                    return FALSE;
                                }
                            }else{
                                $this->ci->session->set_flashdata('warning','Unable to find withdrawal.');
                                return FALSE;
                            }
                        }else{
                            $this->ci->session->set_flashdata('error','Unable to void bank loan and update loan.');
                            return FALSE;
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Unable to void bank loan.');
                        return FALSE;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Bank loan not found. Try again');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Repayment field not found try again');
                return FALSE;
            }

        }else{
            $this->ci->session->set_flashdata('error','Cannot void loan repayment. Some parameters are missing');
            return FALSE;
        }
    }


    function bank_loan_status($id = 0,$group_id=0){
        if($id&&$group_id){
            $loan = $this->ci->bank_loans_m->get($id,$group_id);
            $payments = $this->ci->bank_loans_m->get_sum_paid($id);
            $balance = round($loan->loan_balance-$payments);
            if($loan->loan_balance<=0 || $balance<=0){
                $this->ci->bank_loans_m->update($id,array('is_fully_paid'=>1,'modified_on'=>time()));
            }else{
                $this->ci->bank_loans_m->update($id,array('is_fully_paid'=>'','modified_on'=>time()));
            }
            return TRUE;

        }else{
            $this->ci->set_flashdata('error','Check paramenters passed and try again');
            return FALSE;
        }
    }



    /***********************Loan Application**********************/

    public function calculate_loan_processing_fee_from_application($loan_application_id=0,$loan_type_id=0){
        if($loan_application_id && $loan_type_id){
            $loan_application = $this->ci->loan_applications_m->get($loan_application_id);
            $loan_type = $this->ci->loan_types_m->get($loan_type_id);

            if($loan_type->enable_loan_processing_fee){
                $amount = 0;

                if($loan_type->loan_processing_fee_type==1){
                    $amount = $loan_type->loan_processing_fee_fixed_amount;
                }else if($loan_type->loan_processing_fee_type==2){
                    if($loan_type->loan_processing_fee_percentage_charged_on==1){
                        $amount = ($loan_type->loan_processing_fee_percentage_rate/100)*$loan_application->loan_amount;
                    }else if($loan_type->loan_processing_fee_percentage_charged_on==2){
                        //$interest_principle_amount = $this->ci->loans_m->get_loans_interest_and_principle_amount($loan_id);
                        //$amount = ($loan->loan_processing_fee_percentage_rate/100)*$interest_principle_amount;                        
                    }
                }
                return $amount;
            }else{
                // $this->ci->session->set_flashdata('error','Sorry, the loan is not activated for loan processing');
                return FALSE;
            }
        }else{
            // $this->ci->session->set_flashdata('error','Sorry, Loan ID was not passed to calculate processing Fee');
            return FALSE;
        }
    }


    function create_loan_application($application_details=array(),$user='',$currency=''){
        if(is_array($application_details) && is_object($user)){
            if(array_key_exists('group_id',$application_details) && array_key_exists('member_id',$application_details)){
                $id = $this->ci->loan_applications_m->insert($application_details);
                if($id){
                    /*
                        Notify group admins
                        Notify guarantors
                    */
                    $group_id = $application_details['group_id']?:0;
                    $group = $this->ci->groups_m->get_group_owner($group_id);
                    $group = (object)((array)$group+array('group_id'=>$group_id));
                    if($group){
                        $this->ci->messaging->notify_admin_loan_application($group,$application_details,$user,$currency);
                        return $id;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Could not create application');
                    return False; 
                }
            }else{
                $this->ci->session->set_flashdata('error','Essential Parameters missing');
                return False;
            }
        }else{
            $this->ci->session->set_flashdata('error','Essential Parameters missing');
            return False;
        }
    }

    function api_disburse_loan($loan_application_id=0){
        if($loan_application_id){
            $loan_application = $this->ci->loan_applications_m->get($loan_application_id);
            if($loan_application){
                $loan_type = $this->ci->loan_types_m->get($loan_application->loan_type_id);
                if($loan_type){
                    $amount_to_charge = 0;
                    if($loan_type->enable_loan_processing_fee){
                        $amount_to_charge = $this->calculate_loan_processing_fee_from_application($loan_application_id,$loan_application->loan_type_id);
                    }
                    $amount_to_disburse = $loan_application->loan_amount - $amount_to_charge;
                    if($amount_to_disburse<1){
                        $data = array(
                            'status' =>2,
                            'disbursement_fail_reason' => "",
                            'modified_on' => time(),
                        );
                        $this->ci->session->set_flashdata('error','Amount loaned is too low to be discharged');
                        return FALSE;
                    }

                    if($this->ci->transactions->create_api_transaction_withdrawal($amount_to_disburse,$loan_application->account_id,1,$loan_application->member_id,$loan_application->group_id)){
                        $data = array(
                            'status' =>1,
                            'modified_on' => time(),
                        );
                        $this->loan_applications_m->update($loan_application_id,$data);
                        $this->ci->session->set_flashdata('success','Loan is being disbursed. Wait a confirmation message');
                        return TURE;
                    }else{
                        $this->ci->session->set_flashdata('error','Error disbursing loan');
                        return FALSE;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Loan type not available');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Loan application not available');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Sorry some paramenters are missing');
            return FALSE;
        }
    }



    function toggle_loan_application_status($loan_application = array(),$loan_type = array()){
        /* just incase someone passes objects */
        $loan_application = (array)$loan_application;
        $loan_type = (array)$loan_type; 
        if(empty($loan_application) || empty($loan_type)){
            return FALSE;
        }else{
            // print_r($loan_type['enable_loan_guarantors']); die;
            if($loan_type['enable_loan_guarantors']){
                $loan_application_guarantorship_requests = $this->ci->loans_m->get_loan_application_guarantorship_requests($loan_application['id']);
                if($loan_application_guarantorship_requests){
                    if(count((array_keys(array_combine(array_keys(json_decode(json_encode($loan_application_guarantorship_requests), true)), array_column(json_decode(json_encode($loan_application_guarantorship_requests), true), 'is_approved')),1))) == count($loan_application_guarantorship_requests)){ //loan application has been approved by guarantors
                        $loan_application_signatory_requests = $this->ci->loans_m->get_loan_application_signatory_requests($loan_application['id']);
                        if($loan_application_signatory_requests){
                            if(count((array_keys(array_combine(array_keys(json_decode(json_encode($loan_application_signatory_requests), true)), array_column(json_decode(json_encode($loan_application_signatory_requests), true), 'is_approved')),1))) == count($loan_application_signatory_requests)){ //loan application has been approved
                                $input = array(
                                    'is_approved'=>1,
                                    'status'=>0,
                                    'is_declined'=>0,
                                    'modified_by'=>$this->ci->user->id,
                                    'modified_on'=>time()
                                );
                                if($this->ci->loan_applications_m->update($loan_application['id'],$input)){
                                    return TRUE;
                                }else{
                                    return FALSE;
                                }
                            }elseif(count((array_keys(array_combine(array_keys(json_decode(json_encode($loan_application_signatory_requests), true)), array_column(json_decode(json_encode($loan_application_signatory_requests), true), 'is_declined')),1)))){
                                $key = array_search(1, array_column(json_decode(json_encode($loan_application_signatory_requests), true), 'is_declined'));
                                $input = array(
                                    'is_approved'=>0,
                                    'is_declined'=>1,
                                    'declined_by'=>$loan_application_signatory_requests[$key]->modified_by,
                                    'decline_reason'=>$loan_application_signatory_requests[$key]->decline_reason,
                                    'modified_by'=>$this->ci->user->id,
                                    'modified_on'=>time()
                                );
                                if($this->ci->loan_applications_m->update($loan_application['id'],$input)){
                                    return TRUE;
                                }else{
                                    return FALSE;
                                }
                            }else{
                                return TRUE;
                            }
                        }else{
                            if($this->create_group_signatories_loan_application_approval_requests($loan_application,$loan_type)){
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }
                    }elseif(count((array_keys(array_combine(array_keys(json_decode(json_encode($loan_application_guarantorship_requests), true)), array_column(json_decode(json_encode($loan_application_guarantorship_requests), true), 'is_declined')),1)))){
                        $key = array_search(1, array_column(json_decode(json_encode($loan_application_guarantorship_requests), true), 'is_declined'));
                        $input = array(
                            'is_approved'=>0,
                            'is_declined'=>1,
                            'decline_reason'=>$loan_application_guarantorship_requests[$key]->decline_reason,
                            'declined_by'=>$loan_application_guarantorship_requests[$key]->modified_by,
                            'modified_by'=>$this->ci->user->id,
                            'modified_on'=>time()
                        );
                        if($this->ci->loan_applications_m->update($loan_application['id'],$input)){
                            return TRUE;
                        }else{
                            return FALSE;
                        }
                    }else{
                        return TRUE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                $loan_application_signatory_requests = $this->ci->loans_m->get_loan_application_signatory_requests($loan_application['id']);
                if($loan_application_signatory_requests){
                    if(count((array_keys(array_combine(array_keys(json_decode(json_encode($loan_application_signatory_requests), true)), array_column(json_decode(json_encode($loan_application_signatory_requests), true), 'is_approved')),1))) == count($loan_application_signatory_requests)){ //loan application has been approved
                        $input = array(
                            'is_approved'=>1,
                            'status'=>0,
                            'is_declined'=>0,
                            'modified_by'=>$this->ci->user->id,
                            'modified_on'=>time()
                        );
                        if($this->ci->loan_applications_m->update($loan_application['id'],$input)){
                            // create the automated group member loan.
                            $this->create_loan_from_loan_application($loan_application,'');
                            return TRUE;
                        }else{
                            return FALSE;
                        }
                    }elseif(count((array_keys(array_combine(array_keys(json_decode(json_encode($loan_application_signatory_requests), true)), array_column(json_decode(json_encode($loan_application_signatory_requests), true), 'is_declined')),1))) ){
                        $key = array_search(1, array_column(json_decode(json_encode($loan_application_signatory_requests), true), 'is_declined'));
                        $input = array(
                            'is_approved'=>0,
                            'is_declined'=>1,
                            'decline_reason'=>$loan_application_signatory_requests[$key]->decline_reason,
                            'declined_by'=>$loan_application_signatory_requests[$key]->modified_by,
                            'modified_by'=>$this->ci->user->id,
                            'modified_on'=>time()
                        );
                        if($this->ci->loan_applications_m->update($loan_application['id'],$input)){
                            return TRUE;
                        }else{
                            return FALSE;
                        }
                    }else{
                        return TRUE;
                    }
                }else{
                    if($this->create_group_signatories_loan_application_approval_requests($loan_application,$loan_type)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }
            }
            
        }
    }

    function create_guarantors_loan_application_approval_requests($loan_application = array(),$loan_type = array(),$guarantor_details = array()){ 
        /* guarantor_details array format 
            array('id' => array(amount => amount,comments => comments))
        */

        /* just incase someone passes objects */
        $loan_application = (array)$loan_application; 
        $loan_type = (array)$loan_type;
        $guarantor_details = (array)$guarantor_details;
        if(empty($guarantor_details) || empty($loan_application) || empty($loan_type)){
            $this->ci->session->set_flashdata('warning','Parameters(s) missing');
            return FALSE;
        }else{
            if($loan_type['enable_loan_guarantors'] == 1){
                if(isset($loan_application['id'])){
                    $sms = array();
                    $guarantor_entries = array();
                    $member_user_id_options = $this->ci->members_m->get_group_member_user_id_options();
                    $members_phone_options = $this->ci->members_m->get_group_members_phone_options();
                    $guarantors_are_valid = TRUE;
                    foreach ($guarantor_details as $guarantor_id => $value) {
                        if($this->ci->member->id != $guarantor_id){
                            if(valid_currency($value['amount'])){
                                $guarantor_entries[] =  array(
                                    'loan_type_id'=>$loan_type['id'],
                                    'loan_application_id'=>$loan_application['id'],
                                    'loan_applicant_member_id'=>$this->ci->member->id,
                                    'guarantor_member_id'=>$guarantor_id,
                                    'group_id'=>$this->ci->group->id,
                                    'is_approved'=>0,
                                    'is_declined'=>0,
                                    'active'=>1,
                                    'amount'=>$value['amount'],                                        
                                    'comment'=>$value['comment'],
                                    'created_on'=>time(),
                                    'created_by'=>$this->ci->user->id
                                );
                                $notifications[] = array(
                                    'subject' => $loan_type['name'].' Loan Guarantorship Request',
                                    'message' => $this->ci->member->first_name.' '.$this->ci->member->last_name.' is requesting for your guarantorship of '.$this->ci->group_currency.$value['amount'].' for a '.$loan_type['name'].' loan application.',
                                    'from_member_id' => $this->ci->member->id,
                                    'to_member_id' => $guarantor_id,
                                    'group_id' => $this->ci->group->id,
                                    'call_to_action' => 'Guarantorship Request',
                                    'category' => 20,
                                    'loan_application_id' => $loan_application['id'],
                                    'call_to_action_link' => '/member/loans/guarantorship_requests',
                                    'from_user' => $this->ci->user,
                                    'to_user_id' => $member_user_id_options[$guarantor_id],
                                );

                                $sms[] = array(
                                    'applicant_name' => $this->ci->member->first_name.' '.$this->ci->member->last_name,
                                    'group_currency' => $this->ci->group_currency,
                                    'group_id' => $this->ci->group->id,
                                    'user_id' => $member_user_id_options[$guarantor_id],
                                    'phone' => $members_phone_options[$guarantor_id],
                                    'requested_amount' => $value['amount'],
                                    'loan_type_name' => $loan_type['name'],
                                );
                            }
                        }else{
                            $guarantors_are_valid = FALSE;
                            break;
                        }
                    }
                    if($guarantors_are_valid){
                        if(empty($guarantor_entries)){
                            $this->ci->session->set_flashdata('warning','Invalid guarantor details');
                            return FALSE;
                        }else{
                            if($this->ci->loans_m->insert_batch_loan_guarantorship_requests($guarantor_entries)){
                                $notifications[] = array(
                                    'subject' => $loan_type['name'].' Loan Application',
                                    'message' => 'Your '.$loan_type['name'].' loan application of'.$this->ci->group_currency.$loan_application['loan_amount'].' has been submitted to your guarantors for approval.',
                                    'from_member_id' => $this->ci->member->id,
                                    'to_member_id' => $this->ci->member->id,
                                    'group_id' => $this->ci->group->id,
                                    'call_to_action' => 'Loan Application',
                                    'category' => 20,
                                    'loan_application_id' => $loan_application['id'],
                                    'call_to_action_link' => '/member/loan_applications',
                                    'from_user' => $this->ci->user,
                                    'to_user_id' => $this->ci->user->id,
                                );
                                $this->ci->notifications->create_bulk($notifications);
                                $this->ci->messaging->send_loan_guarantorship_request_sms($sms);
                                return TRUE;
                            }else{
                                $this->ci->session->set_flashdata('warning','Loan application failed: could not insert guarantor entries');
                                return FALSE;
                            }
                        }
                    }else{
                        $this->ci->session->set_flashdata('warning','You cannot guarantee yourself');
                        return FALSE;
                    }                         
                }else{
                    $this->ci->session->set_flashdata('warning','Parameters(s) missing');
                    return FALSE;
                }
            }else{
                return TRUE;
            }
        }
    }

    function create_group_signatories_loan_application_approval_requests($loan_application = array(),$loan_type = array()){
        /* just incase someone passes objects */
        $loan_application = (array)$loan_application; 
        $loan_type = (array)$loan_type;
        $active_group_role_holder_options = $this->ci->members_m->get_active_group_role_holder_options();
        if(count($active_group_role_holder_options) > 2){ //group must have atleast 3 signatories to approve loans
            if(empty($loan_application) || empty($loan_type)){
                $this->ci->session->set_flashdata('warning','Parameters(s) missing');
                return FALSE;
            }else{
                $members_phone_options = $this->ci->members_m->get_group_members_phone_options();
                $member_user_id_options = $this->ci->members_m->get_group_member_user_id_options();
                $signatory_details = array();
                $sms_id = array();
                foreach($active_group_role_holder_options as $group_member_id => $name){
                    $signatory_details[] = array(
                        'loan_type_id'=>$loan_type['id'],
                        'loan_application_id'=> $loan_application['id'],
                        'loan_applicant_member_id'=>$this->ci->member->id,
                        'signatory_member_id'=>$group_member_id,
                        'group_id'=>$this->ci->group->id,
                        'is_approved' => $this->ci->member->id == $group_member_id?1:0,
                        'is_declined'=>0,
                        'active'=>1,
                        'created_on'=>time(),
                        'created_by'=>$this->ci->user->id,
                    );
                    //if current member is signatory do not notify
                    if($this->ci->member->id != $group_member_id){
                        $notifications[] = array(
                            'subject' => $loan_type['name'].' Loan Application Request',
                            'message' => $this->ci->member->first_name.' '.$this->ci->member->last_name.' is requesting for your approval  for a '.$loan_type['name'].' loan application of '.$this->ci->group_currency.$loan_application['loan_amount'],
                            'from_member_id' => $this->ci->member->id,
                            'to_member_id' => $group_member_id,
                            'group_id' => $this->ci->group->id,
                            'call_to_action' => 'Loan Application',
                            'category' => 20,
                            'loan_application_id' => $loan_application['id'],
                            'call_to_action_link' => '/group/loan_applications',
                            'from_user' => $this->ci->user,
                            'to_user_id' => $member_user_id_options[$group_member_id],
                        );
                        $sms[] = array(
                            'applicant_name' => $this->ci->member->first_name.' '.$this->ci->member->last_name,
                            'group_name' => $this->ci->group->name,
                            'group_id' => $this->ci->group->id,
                            'user_id' => $member_user_id_options[$group_member_id],
                            'phone' => $members_phone_options[$group_member_id],
                            'requested_amount' => $loan_application['loan_amount'],
                            'loan_type_name' => $loan_type['name'],
                        );
                    }
                    
                }

                if($this->ci->loans_m->insert_batch_loan_signatory_requests($signatory_details)){
                    $notifications[] = array(
                        'subject' => $loan_type['name'].' Loan Application',
                        'message' => 'Your '.$loan_type['name'].' loan application of'.$this->ci->group_currency.$loan_application['loan_amount'].' has been submitted to your group signatories for approval.',
                        'from_member_id' => $this->ci->member->id,
                        'to_member_id' => $this->ci->member->id,
                        'group_id' => $this->ci->group->id,
                        'call_to_action' => 'Loan Application',
                        'category' => 20,
                        'loan_application_id' => $loan_application['id'],
                        'call_to_action_link' => '/member/loan_applications',
                        'from_user' => $this->ci->user,
                        'to_user_id' => $this->ci->user->id,
                    );
                    // $loan_application += array('id' => $loan_application_id);
                    $this->ci->loan->toggle_loan_application_status($loan_application,$loan_type);
                    $this->ci->messaging->send_group_signatories_loan_application_sms($sms);
                    $this->ci->notifications->create_bulk($notifications);
                    return TRUE;
                }else{
                    $this->ci->session->set_flashdata('warning','Loan application failed: could not insert signatory entries');
                    return FALSE;
                }
            }
        }else{
            $input = array(
                'is_approved'=>0,
                'is_declined'=> 1,
                'declined_by' => '',
                'decline_reason'=> 'Group does not have enough signatories to approve this application',
                'modified_on' => time(),
                'modified_by' => $this->ci->user->id,
            );
            if($this->ci->loan_applications_m->update($loan_application['id'],$input)){
                return TRUE;
            }else{
                $this->ci->session->set_flashdata('warning','Loan application could not be updated');
                return FALSE;
            }
        }
    }

    function process_approved_loan_applications(){
        $approved_loan_applications_pending_disbursements = $this->ci->loan_applications_m->get_approved_loan_applications_pending_disbursements();
        if($approved_loan_applications_pending_disbursements){
            $success = 0;
            $fail = 0;
            $insufficient_balance = 0;
            $disbursements = 0;
            $input = array();
            foreach ($approved_loan_applications_pending_disbursements as $loan_application) {
                $wallet_account = $loan_application->loan_type_is_admin?$this->ci->bank_accounts_m->get_admin_wallet_account():$this->ci->wallets_m->get_wallet_account($loan_application->group_id);
                if($wallet_account){
                    if(preg_match('/bank-/', $loan_application->account_id)){
                        $account_id = str_replace('bank-','',$loan_application->account_id);
                        if($account_id == $wallet_account->id || $loan_application->loan_type_is_admin){ //autodisburse from wallet
                            $wallet_account_balance = ($wallet_account->current_balance + $wallet_account->initial_balance);
                            $group = $this->ci->groups_m->get($loan_application->group_id);
                            $member = $this->ci->members_m->get($loan_application->member_id);
                            $user = $this->ci->ion_auth->get_user($member->user_id);
                            $account = $this->ci->accounts_m->get_group_account($loan_application->account_id,$loan_application->group_id);
                            $loan_type = $this->ci->loan_types_m->get($loan_application->loan_type_id,$loan_application->group_id);

                            $convenience_charge = $this->ci->transactions->calculate_convenience_charge($user,$group,$member,$account,$loan_application->loan_amount,2);

                            if($wallet_account_balance > ($loan_application->loan_amount+$convenience_charge)){ //disburse
                                $loan_processing_fee = 0;
                                if($loan_type->enable_loan_processing_fee){
                                    $loan_processing_fee = $this->calculate_loan_processing_fee_from_application($loan_application_id,$loan_application->loan_type_id);

                                }
                                $amount_to_disburse = $loan_application->loan_amount - $loan_processing_fee;
                                if($amount_to_disburse<1){
                                    $input = array(
                                        'status' =>2,
                                        'disbursement_fail_reason' => "Amount loaned is too low to be discharged",
                                        'modified_on' => time(),
                                    );
                                    if($this->ci->loan_applications_m->update($loan_application->id,$input)){
                                    }else{
                                        echo 'Could not update loan application '.$loan_application->id;
                                    }
                                    $fail++;
                                }else{
                                    if($this->ci->transactions->process_loan_application_disbursement($loan_application)){
                                        $input = array(
                                            'status' =>0,
                                            'modified_on' => time(),
                                        );
                                        if($this->ci->loan_applications_m->update($loan_application->id,$input)){
                                        }else{
                                            echo 'Could not update loan application '.$loan_application->id;
                                        }
                                        $success++;
                                    }else{
                                        if($this->ci->session->error){
                                            $input = array(
                                                'status' => 2,
                                                'disbursement_fail_reason' => $this->ci->session->error,
                                                'modified_on' => time(),
                                            );
                                            if($this->ci->loan_applications_m->update($loan_application->id,$input)){
                                            }else{
                                                echo 'Could not update loan application '.$loan_application->id;
                                            }
                                            $fail++;
                                        }else{
                                            $fail++;
                                        }
                                    }
                                }
                            }else{ //insufficient balance fail
                                $input = array(
                                    'status' => 2,
                                    'disbursement_fail_reason' => "Wallet account balance is insufficient to cater for loan amount and transaction charges",
                                    'modified_on' => time(),
                                );
                                if($this->ci->loan_applications_m->update($loan_application->id,$input)){
                                    $insufficient_balance++;
                                }else{
                                    echo 'Could not update loan application '.$loan_application->id;
                                }
                            }
                            
                        }else{
                            if($this->create_loan_from_loan_application($loan_application)){//just create normal loan
                                //increment counter
                                $input = array(
                                    'status' => 1,
                                    'disbursement_fail_reason' => "",
                                    'modified_on' => time(),
                                );
                                if($this->ci->loan_applications_m->update($loan_application->id,$input)){
                                }else{
                                    echo 'Could not update loan application '.$loan_application->id;
                                }
                                $success++;
                            }else{
                                //increment counter
                                $input = array(
                                    'status' => 2,
                                    'disbursement_fail_reason' => $this->ci->session->error,
                                    'modified_on' => time(),
                                );
                                if($this->ci->loan_applications_m->update($loan_application->id,$input)){
                                }else{
                                    echo 'Could not update loan application '.$loan_application->id;
                                }
                                $fail++;
                            }
                            
                        }
                    }else{
                        if($this->create_loan_from_loan_application($loan_application)){//just create normal loan
                            //increment counter
                            $input = array(
                                'status' => 1,
                                'disbursement_fail_reason' => "",
                                'modified_on' => time(),
                            );
                            if($this->ci->loan_applications_m->update($loan_application->id,$input)){
                            }else{
                                echo 'Could not update loan application '.$loan_application->id;
                            }
                            $success++;
                        }else{
                            //increment counter
                            $input = array(
                            'status' => 2,
                                'disbursement_fail_reason' => $this->ci->session->error,
                                'modified_on' => time(),
                            );
                            if($this->ci->loan_applications_m->update($loan_application->id,$input)){
                            }else{
                                echo 'Could not update loan application '.$loan_application->id;
                            }
                            $fail++;
                        }
                    }
                }else{
                    if($this->create_loan_from_loan_application($loan_application)){//just create normal loan
                        //increment counter
                        $input = array(
                            'status' => 1,
                            'disbursement_fail_reason' => "",
                            'modified_on' => time(),
                        );
                        if($this->ci->loan_applications_m->update($loan_application->id,$input)){
                        }else{
                            echo 'Could not update loan application '.$loan_application->id;
                        }
                        $success++;
                    }else{
                        //increment counter
                        $input = array(
                            'status' => 2,
                            'disbursement_fail_reason' => $this->ci->session->error,
                            'modified_on' => time(),
                        );
                        if($this->ci->loan_applications_m->update($loan_application->id,$input)){
                        }else{
                            echo 'Could not update loan application '.$loan_application->id;
                        }
                        $fail++;
                    }
                }
            }
            echo $success.' loan applications processed successfully <br>';
            echo $disbursements.' done <br>';
            echo $fail.' loan applications processing failed <br>';
            echo $insufficient_balance.' loan applications processing failed due to insufficient wallet balance <br>';
        }else{
            echo 'There are no approved loan applications pending disbursement';
        }
    }

    function create_loan_from_loan_application($loan_application = array(),$transaction_alert_id = 0){
        /* just incase someone passes an object */
        $loan_application = (array)$loan_application;
        if(empty($loan_application)){
            return FALSE;
        }else{
            $guarantors = array(); 
            $guarantor_ids = array();
            $guaranteed_amounts = array();
            $guarantor_comments = array();
            $loan_type = $this->ci->loan_types_m->get($loan_application['loan_type_id'],$loan_application['group_id']);
            if($loan_type->enable_loan_guarantors){
                $loan_application_guarantorship_requests = $this->ci->loans_m->get_loan_application_guarantorship_requests($loan_application['id']);
                foreach ($loan_application_guarantorship_requests as $loan_application_guarantorship_request) {
                    $guarantor_ids[] = $loan_application_guarantorship_request->guarantor_member_id;
                    $guaranteed_amounts[] = $loan_application_guarantorship_request->amount;
                    $guarantor_comments[] = $loan_application_guarantorship_request->comment;
                }
                $guarantors=array(
                    'guarantor_id' => $guarantor_ids,
                    'guaranteed_amount' =>$guaranteed_amounts ,
                    'guarantor_comment' => $guarantor_comments
                );
            }

            $custom_loan_values = array('date_from' =>$loan_application['created_on'],
                'date_to' => strtotime("+".$loan_application['repayment_period']."months", $loan_application['created_on']) ,
                'rate' =>  0,
            );

            $loan_details = array(
                'disbursement_date' =>time() ,
                'loan_amount'   =>  $loan_application['loan_amount'],
                'account_id'    =>$loan_application['account_id'],
                'repayment_period'  =>$loan_application['repayment_period'],
                'interest_rate' =>$loan_type->interest_rate ,
                'loan_application_id' =>$loan_application['id'] ,
                'loan_type_id' =>$loan_type->id ,
                'loan_interest_rate_per' =>$loan_type->loan_interest_rate_per ,
                'interest_type' =>$loan_type->interest_type  ,
                'custom_interest_procedure'=>0,
                'grace_period'  =>$loan_type->grace_period  ,
                'transaction_alert_id'  =>$transaction_alert_id  ,
                'grace_period_date'  =>''  ,
                'sms_notifications_enabled' =>1 ,
                // 'sms_template'  => 1,
                'email_notifications_enabled' => 1 ,
                'enable_loan_fines' =>$loan_type->enable_loan_fines  ,
                'enable_outstanding_loan_balance_fines'=>$loan_type->enable_outstanding_loan_balance_fines,
                'enable_loan_processing_fee' =>$loan_type->enable_loan_processing_fee,
                'enable_loan_fine_deferment' =>$loan_type->enable_loan_fine_deferment ,
                'enable_loan_guarantors' =>$loan_type->enable_loan_guarantors ,
                'enable_reducing_balance_installment_recalculation' => 1,
                'disable_automatic_loan_processing_income' => $loan_type->enable_loan_processing_fee,
                'active'  =>  1,
                'created_by'  =>  isset($this->ci->user)?$this->ci->user->id:0,
                'created_on'  =>  time(),
            );
            $loan_id = $this->create_automated_group_loan($loan_type->name,$loan_application['member_id'],$loan_application['group_id'],$loan_details,$custom_loan_values,0,$guarantors);
            if($loan_id){
                $notifications[] = array(
                    'subject' => $loan_type->name.' Loan Disbursement',
                    'message' => 'Your '.$loan_type->name.' loan application of '.$this->ci->countries_m->get_group_currency($loan_application['group_id']).$loan_application['loan_amount'].' has been disbursed.',
                    'to_member_id' => $loan_application['member_id'],
                    'group_id' => $loan_application['group_id'],
                    'call_to_action' => 'Loan Disbursement',
                    'category' => 22,
                    'loan_id' => $loan_id,
                    'call_to_action_link' => '/group/loans/view_installments/'.$loan_id,
                    'to_user_id' => $this->ci->members_m->get_group_member_user_id($loan_application['member_id'],$loan_application['group_id']),
                );
                $this->ci->notifications->create_bulk($notifications);
                return TRUE;
            }else{
                return FALSE;
            }
        }
    }
}