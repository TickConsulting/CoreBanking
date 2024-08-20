<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Loans_m extends MY_Model{

    protected $_table = 'loans';

    function __construct(){
        parent::__construct();

        $this->install();
    }

    /***
        active 
        1. A loan is active
        '' . A loan is no longer active, has been voided.

        is_fully_paid
        1 - yes the loan is fully paid
        '' or anything else - not fully paid

        Guarantor
        active
        1. if the guarantor is still active guaranting that loan
            else the guarantor no longer guarantees any loan

        Loan Type
        1 . Invoice
        2. Fine Invoice
        3. outstanding loan balances fines
        4. Loan Payment
    ***/

    /****loan statement
        1. Loan Installment Invoice
        2. fine to unpaid installment invoice
        3. fine to outstanding loan balances
        4. loan repayment entry
        5. Loan Transfer
        6. Other Fines
    ****/

    function install(){
        $this->db->query('
            create table if not exists loans(
                id int not null auto_increment primary key,
                `loan_type` blob,
                `member_id` blob,
                `group_id` blob,
                `disbursement_date` blob,
                `loan_end_date` blob,
                `loan_amount` blob,
                `account_type` blob,
                `account_id` blob,
                `repayment_period` blob,
                `interest_rate` blob,
                `loan_interest_rate_per` blob,
                `interest_type` blob,
                `grace_period` blob,
                `grace_period_end_date` blob,
                `custom_interest_procedure` blob,
                `sms_notifications_enabled` blob,
                `sms_template` blob,
                `email_notifications_enabled` blob,
                `enable_loan_fines` blob,
                `loan_fine_type` blob,
                `fixed_fine_amount` blob,
                `fixed_amount_fine_frequency` blob,
                `percentage_fine_rate` blob,
                `percentage_fine_frequency` blob,
                `percentage_fine_on` blob,
                `one_off_fine_type` blob,
                `one_off_fixed_amount` blob,
                `one_off_percentage_rate` blob,
                `one_off_percentage_rate_on` blob,
                `enable_outstanding_loan_balance_fines` blob,
                `outstanding_loan_balance_fine_type` blob,
                `outstanding_loan_balance_fine_fixed_amount` blob,
                `outstanding_loan_balance_fixed_fine_frequency` blob,
                `outstanding_loan_balance_percentage_fine_rate` blob,
                `outstanding_loan_balance_percentage_fine_frequency` blob,
                `outstanding_loan_balance_percentage_fine_on` blob,
                `outstanding_loan_balance_fine_one_off_amount` blob,
                `outstanding_loan_balance_fine_date` blob,
                `enable_loan_processing_fee` blob,
                `loan_processing_fee_type` blob,
                `loan_processing_fee_fixed_amount` blob,
                `loan_processing_fee_percentage_rate` blob,
                `loan_processing_fee_percentage_charged_on` blob,
                `enable_loan_fine_deferment` blob,
                `enable_loan_guarantors` blob,
                `active` blob,
                `is_fully_paid` blob,
                `created_by` blob,
                `created_on` blob,
                `modified_by` blob,
                `modified_on` blob
            )'
        );

        $this->db->query('
            create table if not exists loan_guarantors(
                id int not null auto_increment primary key,
                `loan_id` blob,
                `group_id` blob,
                `member_id` blob,
                `guaranteed_amount` blob,
                `comment` blob,
                `active` blob,
                `created_by` blob,
                `created_on` blob,
                `modified_on` blob,
                `modified_by` blob
        )');

        $this->db->query('
            create table if not exists loan_statements(
                id int not null auto_increment primary key,
                `member_id` blob,
                `group_id` blob,
                `transaction_date` blob,
                `payment_method` blob,
                `transaction_type` blob,
                `loan_id` blob,
                `loan_invoice_id` blob,
                `loan_payment_id` blob,
                `account_type` blob,
                `account_id` blob,
                `amount` blob,
                `balance` blob,
                `active` blob,
                `status` blob,
                `created_by` blob,
                `created_on` blob,
                `modified_on` blob,
                `modified_by` blob
        )');

        $this->db->query('

            create table if not exists joint_loan_members_pairing(
                id int not null auto_increment primary key,
                `member_id` blob,
                `loan_id` blob,
                `group_id` blob,
                `status` blob,
                `created_by` blob,
                `created_on` blob,
                `modified_on` blob,
                `modified_by` blob
            )');
            
        $this->db->query('
            create table if not exists loan_guarantorship_requests(
            id int not null auto_increment primary key,
            `loan_application_id` blob,
            `loan_applicant_member_id` blob,    
            `guarantor_member_id` blob,
            `group_id` blob,
            `amount` blob,
            `comment` blob,
            `is_approved` blob,
            `is_declined` blob,
            `active` blob,
            `approved_on` blob,
            `declined_on` blob,
            `approve_comment` blob,
            `decline_comment` blob,
            `created_by` blob,
            `created_on` blob,
            `modified_on` blob,
            `modified_by` blob
        )');
        $this->db->query('
            create table if not exists loan_signatory_requests(
            id int not null auto_increment primary key,
            `loan_application_id` blob,
            `loan_applicant_member_id` blob,    
            `signatory_member_id` blob,
            `group_id` blob,    
            `loan_amount` blob,
            `active` blob,
            `is_approved` blob,
            `is_declined` blob, 
            `approve_comment` blob,
            `decline_comment` blob,
            `created_on` blob,  
            `created_by` blob,  
            `modified_on` blob,
            `modified_by` blob
        )');
        $this->db->query('
            create table if not exists supervisor_recommendations(
             id int not null auto_increment primary key,
            `loan_type_id` blob,
            `loan_application_id` blob,
            `loan_request_applicant_user_id` blob,
            `loan_request_member_id` blob,  
            `supervisor_user_id` blob,
            `supervisor_member_id` blob,
            `group_id` blob,    
            `loan_amount` blob,
            `active` blob,
            `performance_management` blob,
            `recommendation_date` blob,
            `disciplinary_case` blob,
            `comment` blob,
            `created_on` blob,  
            `created_by` blob,  
            `modified_on` blob,
            `modified_by` blob
        )');
        $this->db->query('
            create table if not exists hr_appraisals(
             id int not null auto_increment primary key,
            `loan_type_id` blob,
            `loan_application_id` blob,
            `loan_applicant_user_id` blob,
            `loan_member_id` blob,  
            `hr_user_id` blob,
            `hr_member_id` blob,
            `group_id` blob,    
            `loan_amount` blob,
            `is_decline` blob,
            `is_approve` blob,
            `active` blob,
            `terms_of_employment` blob,
            `contract_end_date` blob,
            `created_on` blob,  
            `created_by` blob,  
            `modified_on` blob,
            `modified_by` blob
        )');

        $this->db->query('
            create table if not exists sacco_officer(
             id int not null auto_increment primary key,
            `loan_type_id` blob,
            `loan_application_id` blob,
            `loan_applicant_user_id` blob,
            `loan_member_id` blob,  
            `officer_user_id` blob,
            `officer_member_id` blob,
            `group_id` blob,    
            `loan_amount` blob,
            `is_decline` blob,
            `is_approve` blob,
            `active` blob,
            `terms_of_employment` blob,
            `contract_end_date` blob,
            `created_on` blob,  
            `created_by` blob,  
            `modified_on` blob,
            `modified_by` blob
        )');
    }

    function insert($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('loans',$input);
    }

    function insert_batch_statements($input,$skip_validation=FALSE){
        return $this->insert_batch_secure_data('loan_statements',$input);
    }

    function insert_to_guarantor($input=array(),$SKIP_VALIDATION = FALSE)
    {
        return $this->insert_secure_data('loan_guarantors',$input);
    }

    function batch_insert_guarantors($input,$skip_validation=FALSE){
        return $this->insert_batch_secure_data('loan_guarantors',$input);
    }

    function insert_loan_guarantorship_requests($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('loan_guarantorship_requests',$input);
    }

     function insert_batch_loan_guarantorship_requests($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_chunked_batch_secure_data('loan_guarantorship_requests',$input);
    }

    function insert_loan_signatory_requests($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('loan_signatory_requests',$input);
    }

    function insert_batch_loan_signatory_requests($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_chunked_batch_secure_data('loan_signatory_requests',$input);
    }

    function insert_hr_appraisals ($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('hr_appraisals',$input);
    }

    function insert_supervisor($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('supervisor_recommendations',$input);
    }

    function insert_sacco_officer($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('sacco_officer',$input);
    }

    function insert_loan_statements_batch($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_chunked_batch_secure_data('loan_statements',$input);
    }

    function filter_data($table,$data)
    {
        $filtered_data = array();
        $columns = $this->db->list_fields($table);

        if (is_array($data))
        {
            foreach ($columns as $column)
            {
                if (array_key_exists($column, $data))
                    $filtered_data[$column] = $data[$column];
            }
        }

        return $filtered_data;
    }


    function update($id,$input,$val=FALSE){
        return $this->update_secure_data($id,'loans',$input);
    }


    function update_where($where = "",$input = array()){
        return $this->update_secure_where($where,'loans',$input);
    }

    function update_loan_statements_where($where = "",$input = array()){
        return $this->update_secure_where($where,'loan_statements',$input);
    }


    function update_loan_guarantors($id,$input,$val=FALSE){
        return $this->update_secure_data($id,'loan_guarantors',$input);
    }

    function update_loan_signatories($id,$input,$val=FALSE){
        return $this->update_secure_data($id,'loan_signatory_requests',$input);
    }

    function update_loan_guarantorship_request($loan_guarantorship_request_id,$input,$val=FALSE){
        return $this->update_secure_data($loan_guarantorship_request_id,'loan_guarantorship_requests',$input);
    }


    function update_loan_signatory_request($loan_signatory_request_id,$input = array(),$val=FALSE){
        return $this->update_secure_data($loan_signatory_request_id,'loan_signatory_requests',$input);
    }

    function get($id=0){
        $this->select_all_secure('loans');
        $this->db->where('id',$id);
        return $this->db->get('loans')->row();
    }
    function insert_joint_loan_members_pairing($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('joint_loan_members_pairing',$input);
    }

    function delete_joint_loan_members_pairing($loan_id = 0,$group_id=0){
        if($group_id){
            
        }else{
            $group_id = $this->group->id;
        }
        $where = " ".$this->dx('loan_id')." = '".$loan_id."' AND ".$this->dx('group_id')." = '".$group_id."' ;";
        $input = array(
            'is_deleted'=>1,
            'modified_on'=>time(),
        );
        return $this->update_secure_where($where,'joint_loan_members_pairing',$input);

    }

    function get_loan_details($id=0){
        $this->select_all_secure('loans');
        $this->db->where('id',$id);
        $loan = $this->db->get('loans')->row();
        if($loan){
            $amount = $this->loan_repayments_m->get_loan_total_payments($loan->id);
            return ' disbursed on '.timestamp_to_report_time($loan->disbursement_date,TRUE);
        }else{
            return FALSE;
        }
    }

    function get_loan_signatory_request($id= 0,$group_id= 0){        
        $this->select_all_secure('loan_signatory_requests');        
        $this->db->where('id',$id);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        return $this->db->get('loan_signatory_requests')->row();        
    }
    function get_loans_summation_for_ivoices_by_loan_ids($loan_ids=''){
        $this->db->select(array(
            'SUM('.$this->dx('interest_amount_payable').') as total_interest_payable',
            'SUM('.$this->dx('principle_amount_payable').') as total_principle_payable',
            'SUM('.$this->dx('amount_payable').') as total_amount_payable',
            'SUM('.$this->dx('amount_paid').') as total_amount_paid',
            $this->dx('loan_id').' as loan_id',
        ));
        $this->db->where($this->dx('loan_invoices.loan_id').' IN ('.$loan_ids.')',NULL,FALSE);
        
        $this->db->where($this->dx('loan_invoices.active').'="1"',NULL,FALSE);
        $this->db->group_by(array(
            $this->dx('loan_id'),
        ));
        $results = $this->db->get('loan_invoices')->result();
        $arr = [];
        foreach($results as $result){
            $arr[$result->loan_id] = (object)array(
                'total_interest_payable' => $result->total_interest_payable,
                'total_principle_payable' => $result->total_principle_payable,
                'total_amount_payable' => $result->total_amount_payable,
            );
        }
        return $arr;
    }
    function loan_payable_and_principle_todate_by_loan_ids($loan_ids = ''){
        $this->db->select(array(
                'sum('.$this->dx('loan_invoices.amount_payable').') as todate_amount_payable',
                'sum('.$this->dx('loan_invoices.principle_amount_payable').') as todate_principle_payable',
                $this->dx('loan_id').' as loan_id'
            ));
        $this->db->where($this->dx('loan_invoices.loan_id').' IN ('.$loan_ids.')',NULL,FALSE);
        $this->db->where($this->dx('loan_invoices.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('due_date').' <"'.time().'"',NULL,FALSE);
        $this->db->group_by('loan_id');
        $results =  $this->db->get('loan_invoices')->result();
        $arr = array();
        foreach($results as $result){
           $arr[$result->loan_id] = (object)array(
                'todate_amount_payable' => $result->todate_amount_payable,
                'todate_principle_payable' => $result->todate_principle_payable,
           ); 
        }
        return $arr;
    }
    
    function get_loan_borrowed($id){
        $this->db->select(array($this->dx('loan_amount').' as amount_borrowed'));
        $this->db->where('id',$id);
        $loan_amount = $this->db->get('loans')->row();
        if($loan_amount){
            return $loan_amount->amount_borrowed;
        }else{
            return 0;
        }
    }
    function get_loan_guarantorship_request($id= 0,$group_id= 0){        
        $this->select_all_secure('loan_guarantorship_requests');        
        $this->db->where('id',$id);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        return $this->db->get('loan_guarantorship_requests')->row();        
    }

    function get_pending_member_loan_guarantorship_requests($member_id= 0){        
        $this->select_all_secure('loan_guarantorship_requests');        
        $this->db->where($this->dx('guarantor_member_id').'="'.$member_id.'"',Null,FALSE);
        $this->db->where($this->dx('loan_guarantorship_requests.is_approved').'="0"',Null,FALSE);
        $this->db->where($this->dx('loan_guarantorship_requests.is_declined').'="0"',Null,FALSE);
        $this->db->where($this->dx('loan_applications.is_approved').'="0"',Null,FALSE);
        $this->db->where($this->dx('loan_applications.is_declined').'="0"',Null,FALSE);
        $this->db->join('loan_applications','loan_applications.id = '.$this->dx('loan_guarantorship_requests.loan_application_id'),'LEFT');
        return $this->db->get('loan_guarantorship_requests')->result();        
    }

    function get_approved_member_loan_guarantorship_requests($member_id= 0){        
        $this->select_all_secure('loan_guarantorship_requests');        
        $this->db->where($this->dx('guarantor_member_id').'="'.$member_id.'"',Null,FALSE);
        $this->db->where($this->dx('loan_guarantorship_requests.is_approved').'="1"',Null,FALSE);
        $this->db->join('loan_applications','loan_applications.id = '.$this->dx('loan_guarantorship_requests.loan_application_id'),'LEFT');
        return $this->db->get('loan_guarantorship_requests')->result(); 
    }

    function get_declined_member_loan_guarantorship_requests($member_id= 0){        
        $this->select_all_secure('loan_guarantorship_requests');        
        $this->db->where($this->dx('guarantor_member_id').'="'.$member_id.'"',Null,FALSE);
        $this->db->where($this->dx('loan_guarantorship_requests.is_declined').'="1"',Null,FALSE);
        $this->db->join('loan_applications','loan_applications.id = '.$this->dx('loan_guarantorship_requests.loan_application_id'),'LEFT');
        return $this->db->get('loan_guarantorship_requests')->result();    
    }

    function get_pending_member_guarantorship_requests($group_id= 0, $guarantor_member_id= 0){
        $this->select_all_secure('loan_guarantorship_requests');
        if($guarantor_member_id){
            $this->db->where($this->dx('guarantor_member_id').'="'.$guarantor_member_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('guarantor_member_id').'="'.$this->member->id.'"',Null,FALSE);
        } 
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        $this->db->where($this->dx('loan_request_progress_status').'="1"',Null,FALSE);
        $this->db->order_by($this->dx('modified_on'),'DESC',FALSE);        
        return $this->db->get('loan_guarantorship_requests')->result();
    }

    function get_approved_member_guarantorship_requests($group_id= 0, $guarantor_member_id= 0){
        $this->select_all_secure('loan_guarantorship_requests');
        if($guarantor_member_id){
            $this->db->where($this->dx('guarantor_member_id').'="'.$guarantor_member_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('guarantor_member_id').'="'.$this->member->id.'"',Null,FALSE);
        }
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        $this->db->where($this->dx('loan_request_progress_status').'="3"',Null,FALSE);
        $this->db->order_by($this->dx('modified_on'),'DESC',FALSE);        
        return $this->db->get('loan_guarantorship_requests')->result();
    }

    function get_declined_member_guarantorship_requests($group_id= 0, $guarantor_member_id= 0){
        $this->select_all_secure('loan_guarantorship_requests');
        if($guarantor_member_id){
            $this->db->where($this->dx('guarantor_member_id').'="'.$guarantor_member_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('guarantor_member_id').'="'.$this->member->id.'"',Null,FALSE);
        }
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        $this->db->where($this->dx('loan_request_progress_status').'="2"',Null,FALSE);
        $this->db->order_by($this->dx('modified_on'),'DESC',FALSE);        
        return $this->db->get('loan_guarantorship_requests')->result();
    }

    function get_loan_application_guarantorship_requests_by_member_id($guarantor_member_id= 0,$group_id= 0){
        $this->select_all_secure('loan_guarantorship_requests');
        if($guarantor_member_id){
            $this->db->where($this->dx('guarantor_member_id').'="'.$guarantor_member_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('guarantor_member_id').'="'.$this->member->id.'"',Null,FALSE);
        } 
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        $this->db->order_by($this->dx('modified_on'),'DESC',FALSE);        
        return $this->db->get('loan_guarantorship_requests')->result();
    }

    function get_loan_application_guarantorship_requests_by_loan_application_id($loan_application_id=0,$group_id= 0){  

        $this->select_all_secure('loan_guarantorship_requests');     
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        $this->db->where($this->dx('loan_application_id'),$loan_application_id);
        return $this->db->get('loan_guarantorship_requests')->row();        
    }

    function get_loan_application_guarantorship_requests_by_loan_application_id_array($loan_application_id= 0,$group_id= 0 ){
        $arr = array();
        $this->select_all_secure('loan_guarantorship_requests');     
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',Null,FALSE);
        $results =  $this->db->get('loan_guarantorship_requests')->result();
        foreach ($results as $key => $result) {
            $arr[$result->guarantor_member_id] = $result;
        }
        return $arr;
    }

    // function get_loan_application_guarantorship_requests($id= 0,$group_id= 0){
    //     //echo $id .'<br>'. $group_id; die();
    //     $this->select_all_secure('loan_guarantorship_requests');
    //     if($group_id){
    //         $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
    //     }else{
    //         $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
    //     }
    //     $this->db->where($this->dx('active').'="1"',NULL,FALSE);
    //     return $this->db->get('loan_guarantorship_requests')->result();        
    // } 

    function get_loan_signatories($id=0,$group_id=0){
        $this->select_all_secure('loan_signatory_requests');
        $this->db->where('id',$id);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);            
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('loan_signatory_requests')->row();
    }

    function get_all_signatory_requests($loan_application_id=0){
        $this->select_all_secure('loan_signatory_requests');
        $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('loan_signatory_requests')->result();
    }

    function get_pending_member_signatory_requests($group_id = 0 , $member_id =0){
        $this->select_all_secure('loan_signatory_requests');
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);            
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        if($member_id){
            $this->db->where($this->dx('signatory_member_id').'="'.$member_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('signatory_member_id').'="'.$this->member->id.'"',Null,FALSE);
        }
        $this->db->where($this->dx('loan_signatory_progress_status').'="1"',Null,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('loan_signatory_requests')->result();
    } 

    function get_approved_member_signatory_requests($group_id = 0 , $member_id =0){
        $this->select_all_secure('loan_signatory_requests');
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);            
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        if($member_id){
            $this->db->where($this->dx('signatory_member_id').'="'.$member_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('signatory_member_id').'="'.$this->member->id.'"',Null,FALSE);
        }
        $this->db->where($this->dx('loan_signatory_progress_status').'="3"',Null,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('loan_signatory_requests')->result();
    }

    function get_declined_member_signatory_requests($group_id = 0 , $member_id =0){
        $this->select_all_secure('loan_signatory_requests');
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);            
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
       if($member_id){
            $this->db->where($this->dx('signatory_member_id').'="'.$member_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('signatory_member_id').'="'.$this->member->id.'"',Null,FALSE);
        }
        $this->db->where($this->dx('loan_signatory_progress_status').'="2"',Null,FALSE);
        //$this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('loan_signatory_requests')->result();
    }   

    function get_group_member_signatories_array ($loan_application_id = 0 ,$group_id = 0){
        $this->select_all_secure('loan_signatory_requests');
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);            
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',NULL,FALSE);
        //$this->db->where($this->dx('active').'="1"',NULL,FALSE);        
        $arr = array();
        $sig_array = array();
        $result_arr = array();
        $results = $this->db->get('loan_signatory_requests')->result();
        foreach ($results as $key => $result) {
            if($result->signatory_member_id){
                $sig_array[$result->signatory_member_id] = $result;
            }            
        }
        $result_arr = array_merge($arr ,$sig_array);
        return $result_arr;
    }  

    function get_group_signatories_array ($group_id = 0 , $loan_application_id = 0){
        $this->select_all_secure('loan_signatory_requests');
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);            
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);        
        $arr = array();
        $sig_array = array();
        $result_arr = array();
        $results = $this->db->get('loan_signatory_requests')->result();
        foreach ($results as $key => $result) {
            if($result->signatory_member_id){
                $sig_array[$result->signatory_member_id] = $result->loan_signatory_progress_status;
            }            
        }
        $result_arr = array_merge($arr ,$sig_array);
        return $result_arr;
    }

    function get_member_loan_signatory_options($group_id = 0,$member_id = 0,$user_id = 0){
        $this->select_all_secure('loan_signatory_requests');
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);            
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        $this->db->order_by($this->dx('modified_on'),'DESC',FALSE);
        $this->db->where($this->dx('signatory_member_id').'="'.$member_id.'"',Null,FALSE); 
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('loan_signatory_requests')->result();
    }

    function get_member_loan_signatory_options_array($group_id = 0){
        $arr = array();
        $this->select_all_secure('loan_signatory_requests');
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);            
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        $this->db->order_by($this->dx('modified_on'),'DESC',FALSE); 
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $results =  $this->db->get('loan_signatory_requests')->result();        
        return $results;
    }

    function get_member_loan_signatory_options_per_member_array($group_id = 0 , $member_id = 0){
        $arr = array();
        $this->select_all_secure('loan_signatory_requests');
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);            
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        if($member_id){
            $this->db->where($this->dx('signatory_member_id').'="'.$member_id.'"',Null,FALSE); 
        }else{
           $this->db->where($this->dx('signatory_member_id').'="'.$this->member->id.'"',Null,FALSE);   
        }
        $this->db->order_by($this->dx('modified_on'),'DESC',FALSE); 
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $results =  $this->db->get('loan_signatory_requests')->result();        
        return $results;
    }

    function get_signatory_member_request_loans_application($loan_type_id=0,$loan_application_id=0,$group_id=0,$member_id=0){
         $this->select_all_secure('loan_signatory_requests');       
        if($loan_type_id && $loan_application_id && $group_id && $member_id){            
            $this->db->where($this->dx('loan_type_id').'="'.$loan_type_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        $this->db->where($this->dx('loan_application_id'),$loan_application_id);
         $this->db->where($this->dx('loan_signatory_progress_status').'="1"',NULL,FALSE);
        $this->db->where($this->dx('loan_request_member_id'),$member_id,NULL,FALSE);
        return $this->db->get('loan_signatory_requests')->result();
    }

    function get_group_signatory_per_loan_application($loan_application_id=0){
        $this->select_all_secure('loan_signatory_requests');
        $this->db->where($this->dx('loan_application_id'),$loan_application_id);
        //$this->db->where($this->dx('loan_signatory_progress_status').'="1"',NULL,FALSE);
        //$this->db->where($this->dx('loan_request_member_id'),$member_id,NULL,FALSE);
        return $this->db->get('loan_signatory_requests')->result();

    }

    function delete_signatory($ids_to_delete = array()){
        $delete_count = 0;
        //$this->db->where($this->dx('active').'="0"',NULL,FALSE);
        foreach ($ids_to_delete as $key => $id) {
            $delete_count++;
            $this->db->where('id',$id);
            $this->db->delete('loan_signatory_requests');            
        }
        return $delete_count;
    }

    function get_member_request_loans_application($loan_type_id=0,$loan_application_id=0,$group_id=0,$member_id=0){
        //echo $loan_type_id .'<br>'. $loan_application_id  .'<br>'. $group_id  .'<br>'. $member_id; die();
       $this->select_all_secure('loan_guarantorship_requests');       
        if($loan_type_id && $loan_application_id && $group_id && $member_id){            
            $this->db->where($this->dx('loan_type_id').'="'.$loan_type_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        $this->db->where($this->dx('loan_application_id'),$loan_application_id);
         $this->db->where($this->dx('loan_request_progress_status').'="1"',NULL,FALSE);
        $this->db->where($this->dx('loan_request_applicant_member_id'),$member_id,NULL,FALSE);
        return $this->db->get('loan_guarantorship_requests')->result();   
    }

    function get_guarantor_request($id=0){
        $this->select_all_secure('loan_guarantorship_requests');
        $this->db->where('id',$id);
        return $this->db->get('loan_guarantorship_requests')->row();   
    }   

    function get_loan_applications_member_requests($loan_type_id=0,$loan_application_id=0,$group_id=0,$member_id=0){

        $this->select_all_secure('loan_guarantorship_requests');       
        if($loan_type_id && $loan_application_id && $group_id && $member_id){            
            $this->db->where($this->dx('loan_type_id').'="'.$loan_type_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        $this->db->where($this->dx('loan_application_id'),$loan_application_id);
        $this->db->where($this->dx('loan_request_progress_status').'="1"',NULL,FALSE);
        $this->db->where($this->dx('guarantor_member_id'),$member_id,NULL,FALSE);
        return $this->db->get('loan_guarantorship_requests')->result();      
    }

    function get_loan_request_status($loan_type_id,$loan_application_id,$group_id){
        //$this->select_all_secure('loan_guarantorship_requests');
        $this->db->select(array(
                   $this->dx('loan_guarantorship_requests.loan_request_progress_status').' as loan_request_progress_status'
                   ));
        if($loan_type_id && $loan_application_id &&$group_id){            
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        $this->db->where($this->dx('loan_type_id').'="'.$loan_type_id.'"',NULL,FALSE);
        $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',Null,FALSE);
        return $this->db->get('loan_guarantorship_requests')->result();

    }

    function get_group_loan_gurantorship_request_array($group_id = 0 , $loan_type_id = 0 , $loan_application_id = 0){
        $arr = array();
        $this->select_all_secure('loan_guarantorship_requests');
        if($group_id){            
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        if($loan_type_id){            
            $this->db->where($this->dx('loan_type_id').'="'.$loan_type_id.'"',Null,FALSE);
        }
        if($loan_application_id ){            
            $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',Null,FALSE);
        }
        $results =  $this->db->get('loan_guarantorship_requests')->result();
        foreach ($results as $key => $loan_guarantor_request) {
           $arr[$loan_guarantor_request->guarantor_member_id] = $loan_guarantor_request;
        }
        return $arr;
    }

    function get_group_loan_gurantorship_progress_status_request_array($group_id = 0 , $loan_type_id = 0 , $loan_application_id = 0){
        $arr = array();
        $this->select_all_secure('loan_guarantorship_requests');
        if($group_id){            
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        if($loan_type_id){            
            $this->db->where($this->dx('loan_type_id').'="'.$loan_type_id.'"',Null,FALSE);
        }
        if($loan_application_id ){            
            $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',Null,FALSE);
        }
        $results =  $this->db->get('loan_guarantorship_requests')->result();
        foreach ($results as $key => $loan_guarantor_request) {
           $arr[$loan_guarantor_request->guarantor_member_id] = $loan_guarantor_request->loan_request_progress_status;
        }
        return $arr;
    }

    function get_group_loan_hr_appraisal_request_array($group_id = 0 , $loan_type_id = 0 , $loan_application_id = 0){
        $arr = array();
        $this->select_all_secure('hr_appraisals');
        if($group_id){            
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }

        if($loan_type_id){            
            $this->db->where($this->dx('loan_type_id').'="'.$loan_type_id.'"',Null,FALSE);
        }
        if($loan_application_id ){
            //$this->db->where($this->dx('loan_application_id').'="1"',Null,FALSE);                
            $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',Null,FALSE);
        }
        $results =  $this->db->get('hr_appraisals')->result();
        foreach ($results as $key => $result) {
           $arr[$result->loan_member_id] = $result;
        }
        return $arr;
    }

    function get_group_loan_hr_appraisal_requests($group_id = 0 , $loan_type_id = 0 , $loan_application_id = 0){
        $arr = array();
        $this->select_all_secure('hr_appraisals');
        if($group_id){            
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        if($loan_type_id){            
            $this->db->where($this->dx('loan_type_id').'="'.$loan_type_id.'"',Null,FALSE);
        }
        if($loan_application_id ){            
            $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',Null,FALSE);
        }
        $this->db->where($this->dx('is_loan_exisiting').'="1"',Null,FALSE);
        $results =  $this->db->get('hr_appraisals')->result();
        return $results;
    }

    function get_group_loan_sacco_officer_appraisal_request_array($group_id = 0 , $loan_type_id = 0 , $loan_application_id = 0){
        $arr = array();
        $this->select_all_secure('sacco_officer');
        if($group_id){            
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        if($loan_type_id){            
            $this->db->where($this->dx('loan_type_id').'="'.$loan_type_id.'"',Null,FALSE);
        }
        if($loan_application_id ){            
            $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',Null,FALSE);
        }
        $results =  $this->db->get('sacco_officer')->result();
        foreach ($results as $key => $result) {
           $arr[$result->loan_member_id] = $result;
        }
        return $arr;
    }

    function get_group_loan_committe_decision_request_array($group_id = 0 , $loan_type_id = 0 , $loan_application_id = 0){
        $arr = array();
        $this->select_all_secure('loan_signatory_requests');
        if($group_id){            
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        if($loan_type_id){            
            $this->db->where($this->dx('loan_type_id').'="'.$loan_type_id.'"',Null,FALSE);
        }
        if($loan_application_id ){            
            $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',Null,FALSE);
        }
        $results =  $this->db->get('loan_signatory_requests')->result();
        /*foreach ($results as $key => $result) {
           $arr[$result->loan_application_id] = $result;
        }*/
        return $results;    
    }

    function get_member_supervisor_recommendations($group_id = 0 ,$member_id =0 ){
        $arr = array();
        $this->select_all_secure('supervisor_recommendations');
        if($group_id){            
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        if($member_id){            
            $this->db->where($this->dx('supervisor_member_id').'="'.$member_id.'"',Null,FALSE);
        }
        $results =  $this->db->get('supervisor_recommendations')->result();
        return $results;
    }

    function get_member_hr_recommendations($group_id = 0 ,$member_id =0 ){
        $arr = array();
        $this->select_all_secure('hr_appraisals');
        if($group_id){            
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        if($member_id){            
            $this->db->where($this->dx('hr_member_id').'="'.$member_id.'"',Null,FALSE);
        }
        $results =  $this->db->get('hr_appraisals')->result();
        return $results;
    }

    function get_member_hr_recommendations_array($group_id = 0 ,$member_id =0 ){
        $arr = array();
        $this->select_all_secure('hr_appraisals');
        if($group_id){            
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        if($member_id){            
            $this->db->where($this->dx('hr_member_id').'="'.$member_id.'"',Null,FALSE);
        }
        $results =  $this->db->get('hr_appraisals')->result();
        foreach ($results as $key => $result) {
           $arr[$result->loan_application_id] = $result;
        }
        return $arr;
    }

    function get_member_sacco_officer_appraisals($group_id = 0 ,$member_id =0){
        $arr = array();
        $this->select_all_secure('sacco_officer');
        if($group_id){            
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        if($member_id){            
            $this->db->where($this->dx('officer_member_id').'="'.$member_id.'"',Null,FALSE);
        }
        $results =  $this->db->get('sacco_officer')->result();
        foreach ($results as $key => $result):
        endforeach;
        return $results;
    }

    function get_supervisor_recommendations($id = 0 ){
        $this->select_all_secure('supervisor_recommendations');
        $this->db->where('id',$id);
        return $this->db->get('supervisor_recommendations')->row();
    }

    function update_supervisory($id,$input,$val=FALSE){
        return $this->update_secure_data($id,'supervisor_recommendations',$input);
    }

    function if_supervisor_recommendation_exists($loan_application_id = 0 ){
        $this->select_all_secure('supervisor_recommendations');
        $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',NULL,FALSE);
        return $this->db->count_all_results('supervisor_recommendations');
    }

    function if_payroll_accountant_exist($loan_application_id = 0){       
        $this->select_all_secure('hr_appraisals');
        $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',NULL,FALSE);
        return $this->db->count_all_results('hr_appraisals');
    }

    function if_sacco_appraisal_exist( $loan_application_id = 0){
        $this->select_all_secure('sacco_officer');
        $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',NULL,FALSE);
        return $this->db->count_all_results('sacco_officer');
    }

    function if_committee_decision_exist($loan_application_id = 0){
        $this->select_all_secure('loan_signatory_requests');
        $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',NULL,FALSE);
        return $this->db->count_all_results('loan_signatory_requests');  
    }

    function get_group_loan_supervisor_request_array($group_id = 0 , $loan_type_id = 0 , $loan_application_id = 0){
        $arr = array();
        $this->select_all_secure('supervisor_recommendations');
        if($group_id){            
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',Null,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',Null,FALSE);
        }
        if($loan_type_id){            
            $this->db->where($this->dx('loan_type_id').'="'.$loan_type_id.'"',Null,FALSE);
        }
        if($loan_application_id ){            
            $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',Null,FALSE);
        }
        $results =  $this->db->get('supervisor_recommendations')->result();
        foreach ($results as $key => $loan_supervisor_recommendation) {
           $arr[$loan_supervisor_recommendation->supervisor_member_id] = $loan_supervisor_recommendation;
        }
        return $arr;
    }
    

    function get_group_loan($id = 0,$group_id = 0){
        $this->select_all_secure('loans');
        $this->db->where('id',$id);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('loans')->row();
    }

    function check_group_loan($id = 0,$group_id = 0){
        $this->db->where('id',$id);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->count_all_results('loans')?:0; 
    }

     function get_joint_loan_joint_members($loan_id=0)
    {
        $this->select_all_secure('joint_loan_members_pairing');
        $this->db->select(array(
                $this->dx('users.first_name').' as first_name',
                $this->dx('users.last_name').' as last_name',
                $this->dx('users.email').' as email',
                $this->dx('users.phone').' as phone',
            ));
        $this->db->join('members',$this->dx('joint_loan_members_pairing.member_id').' = members.id');
        $this->db->join('users',$this->dx('members.user_id').' = users.id');
        $this->db->where($this->dx('joint_loan_members_pairing.is_deleted').' != 1');
        $this->db->where($this->dx('joint_loan_members_pairing.loan_id'),$loan_id);
        return $this->db->get('joint_loan_members_pairing')->result();
    }

    function get_loan_and_member($id=0)
    {
        $this->select_all_secure('loans');
        $this->db->select(array(
                $this->dx('users.first_name').' as first_name',
                $this->dx('users.last_name').' as last_name',
                $this->dx('users.email').' as email',
                $this->dx('users.id_number').' as id_number',
                $this->dx('users.phone').' as phone',
                $this->dx('users.avatar').' as avatar',
                $this->dx('members.group_role_id').' as group_role_id',
                $this->dx('members.membership_number').' as membership_number',
            ));
        $this->db->join('members',$this->dx('loans.member_id').' = members.id');
        $this->db->join('users',$this->dx('members.user_id').' = users.id');
        $this->db->where('loans.id',$id);
        return $this->db->get('loans')->row();
    }

    function get_active_member_loans_option($member_id=0){
        $this->select_all_secure('loans');
        $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE);

        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where('('.$this->dx('is_fully_paid').'="" OR '.$this->dx('is_fully_paid').' IS NULL OR '.$this->dx('is_fully_paid').'="0")',NULL,FALSE);
        $loans = $this->db->get('loans')->result(); 
        $arr=array();
        foreach ($loans as $value){
            $arr[$value->id] = $this->group_currency.' '.number_to_currency($value->loan_amount).' - Disbursed '.timestamp_to_date($value->disbursement_date,TRUE);
        }
        return $arr;
    }

    function get_active_debtor_loans_option($debtor_id=0){
        $this->select_all_secure('debtor_loans');
        $this->db->where($this->dx('debtor_id').'="'.$debtor_id.'"',NULL,FALSE);
        $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where('('.$this->dx('is_fully_paid').'="" OR '.$this->dx('is_fully_paid').' IS NULL OR '.$this->dx('is_fully_paid').'="0")',NULL,FALSE);
        $loans = $this->db->get('debtor_loans')->result(); 
        $arr=array();
        foreach ($loans as $value){
            $arr[$value->id] = $this->group_currency.' '.number_to_currency($value->loan_amount).' - Disbursed '.timestamp_to_date($value->disbursement_date,TRUE);
        }
        return $arr;
    }

    function count_active_group_loans($group_id = 0,$from=0,$to=0,$check_fully_paid= TRUE){
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if($from && $to){
            $this->db->where($this->dx('disbursement_date').' >= "'.$from.'"',NULL,FALSE);
            $this->db->where($this->dx('disbursement_date').' <= "'.$to.'"',NULL,FALSE);
        }
        if($check_fully_paid){
            $this->db->where('('.$this->dx('is_fully_paid').'="" OR '.$this->dx('is_fully_paid').' IS NULL OR '.$this->dx('is_fully_paid').'="0")',NULL,FALSE);
        }else{
        }
        return $this->db->count_all_results('loans'); 
    }

    function count_active_loans($check_fully_paid = TRUE){
        $this->db->where('('.$this->dx('interest_type').' ="1" OR '.$this->dx('interest_type').' ="2" )',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if($check_fully_paid){
            $this->db->where('('.$this->dx('is_fully_paid').'="" OR '.$this->dx('is_fully_paid').' IS NULL OR '.$this->dx('is_fully_paid').'="0")',NULL,FALSE);
        }else{
        }
        return $this->db->count_all_results('loans'); 
    }

    function get_active_group_loans($group_id = 0,$from=0,$to=0,$check_fully_paid= TRUE){
        $this->select_all_secure('loans');
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        if($from && $to){
            $this->db->where($this->dx('disbursement_date').' >= "'.$from.'"',NULL,FALSE);
            $this->db->where($this->dx('disbursement_date').' <= "'.$to.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if($check_fully_paid){
            $this->db->where('('.$this->dx('is_fully_paid').'="" OR '.$this->dx('is_fully_paid').' IS NULL OR '.$this->dx('is_fully_paid').'="0")',NULL,FALSE);
        }else{
            
        }
        return $this->db->get('loans')->result(); 

    }

    function get_active_loans($check_fully_paid= TRUE){
        $this->select_all_secure('loans');
        $this->db->where('('.$this->dx('interest_type').' ="1" OR '.$this->dx('interest_type').' ="2" )',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if($check_fully_paid){
            $this->db->where('('.$this->dx('is_fully_paid').'="" OR '.$this->dx('is_fully_paid').' IS NULL OR '.$this->dx('is_fully_paid').'="0")',NULL,FALSE);
        }else{
            
        }
        return $this->db->get('loans')->result(); 
    }

    function get_specific_active_group_loans($group_id = 0){
        $this->select_all_secure('loans');
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        return $this->db->get('loans')->result(); 
    }

    function count_all_group_active_member_loan_option($group_id ,$member_id = 0){
        $this->select_all_secure('loans');
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);    
        }
        if($member_id){
            $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $loans = $this->db->get('loans')->result();
        $arr=array();
        foreach ($loans as $value) 
        {
            $arr[$value->member_id] = count($loans);
        }
        
        return $arr;
    }

    function get_member_loans_option($member_id=0){
        $this->select_all_secure('loans');
        $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE);
        $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $loans = $this->db->get('loans')->result(); 
        $arr=array();
        foreach ($loans as $value) 
        {
            $amount = $this->loan_repayments_m->get_loan_total_payments($value->id);
            $transfer_out = $this->loan_invoices_m->get_total_loan_transfers_out($value->id)?:0;
            $amount = $amount-$transfer_out;
            $arr[$value->id] = 'Disbursed '.$this->group_currency.' '.number_to_currency($value->loan_amount).' on '.timestamp_to_report_time($value->disbursement_date,TRUE).'. So far paid '.$this->group_currency.' '.number_to_currency($amount);
        }
        return $arr;
    }

    function get_group_loan_options($group_id = 0){
        $this->select_all_secure('loans');
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $loans = $this->db->get('loans')->result(); 
        $arr=array();
        foreach ($loans as $value) {
            $arr[$value->id] = $this->group_currency.' '.number_to_currency($value->loan_amount).' - Disbursed '.timestamp_to_date($value->disbursement_date,TRUE);
        }
        return $arr;
    }

    function get_group_mobile_loan_options($group_id = 0){
        $this->select_all_secure('loans');
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $loans = $this->db->get('loans')->result(); 
        $arr=array();
        foreach ($loans as $value) {
            $arr[$value->id] = number_to_currency($value->loan_amount).' - Disbursed '.timestamp_to_date($value->disbursement_date,TRUE);
        }
        return $arr;
    }


    function get_all()
    {
        $this->select_all_secure('loans');
        $this->db->select(array(
                $this->dx('loan_guarantors.member_id').' as guarantor_member_id',
            ));
        $this->db->join('loan_guarantors','loans.id = '.$this->dx('loan_guarantors.loan_id'),'INNER');
        return $this->db->get('loans')->result();
    }

    function get_group_loans($filter_parameters = array(),$group_id = 0){
        $this->select_all_secure('loans');
         
        if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
            if($filter_parameters['from'] && $filter_parameters['to']){
                $this->db->where($this->dx('disbursement_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
                $this->db->where($this->dx('disbursement_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
            }
        }

        if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){

            if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
                $member_list = '0';
                $members = $filter_parameters['member_id'];
                $count = 1;
                foreach($members as $member_id){
                    if($member_id){
                        if($count==1){
                            $member_list = $member_id;
                        }else{
                            $member_list .= ','.$member_id;
                        }
                        $count++;
                    }
                }

                if($member_list){
                    $this->db->where($this->dx('member_id').' IN ('.$member_list.')',NULL,FALSE);
                }
            }
        }

        if(isset($filter_parameters['accounts']) && $filter_parameters['accounts']){
            $account_list = '0';
            $accounts = $filter_parameters['accounts'];
            $count = 1;
            foreach($accounts as $account_id){
                if($account_id){
                    if($count==1){
                        $account_list = '"'.$account_id.'"';
                    }else{
                        $account_list .= ',"'.$account_id.'"';
                    }
                    $count++;
                }
            }
            if($account_list){
                $this->db->where($this->dx('account_id').' IN ('.$account_list.')',NULL,FALSE);
            }
        }

        if(isset($filter_parameters['is_fully_paid']) && $filter_parameters['is_fully_paid']){
            if($filter_parameters['is_fully_paid']==1){
                $this->db->where($this->dx('is_fully_paid').' = "1" ',NULL,FALSE);
            }else if($filter_parameters['is_fully_paid']==0){
                $this->db->where($this->dx('is_fully_paid').' = "0" ',NULL,FALSE);
            }
        }
        // if($group_id){
        //     $this->db->where($this->dx('loans.group_id').'="'.$group_id.'"',NULL,FALSE);
        // }else{
        //     $this->db->where($this->dx('loans.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        // }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->order_by($this->dx('disbursement_date'),'DESC',FALSE);
        $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
        return $this->db->get('loans')->result();
    }

    function get_member_loans($group_id=0,$member_id=0,$is_fully_paid = FALSE){
        $this->select_all_secure('loans');
        if($group_id){
            $this->db->where($this->dx('loans.group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
          $this->db->where($this->dx('loans.group_id').'="'.$this->group->id.'"',NULL,FALSE);  
        }
        if($member_id){
            $this->db->where($this->dx('loans.member_id').'="'.$member_id.'"',NULL,FALSE);
        }else{
          $this->db->where($this->dx('loans.member_id').'="'.$this->member->id.'"',NULL,FALSE);  
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if($is_fully_paid){
            $this->db->where($this->dx('loans.is_fully_paid').' ="1"',NULL,FALSE);
        }
        $this->db->order_by($this->dx('disbursement_date'),'DESC',FALSE);
        $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
        return $this->db->get('loans')->result();
    }

    function get_member_group_loan($id=0,$group_id=0,$member_id=0){
        $this->select_all_secure('loans');
        $this->db->where('id',$id);
        if($group_id){
            $this->db->where($this->dx('loans.group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
          $this->db->where($this->dx('loans.group_id').'="'.$this->group->id.'"',NULL,FALSE);  
        }
        if($member_id){
            $this->db->where($this->dx('loans.member_id').'="'.$member_id.'"',NULL,FALSE);
        }else{
          $this->db->where($this->dx('loans.member_id').'="'.$this->member->id.'"',NULL,FALSE);  
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('loans')->row();
    }
    function count_all(){
        return $this->count_all_results('loans');
    }

    function count_all_paying_groups_loans($from ,$to , $paying_group_ids){
        $arr = array();
        $this->db->select(
            array(
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%Y') as year ",
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%c') as month "
            )
        );
        $this->db->where($this->dx('created_on')." >= ".$from,NULL,FALSE);
        $this->db->where($this->dx('created_on')." <= ".$to,NULL,FALSE);
        $this->db->where($this->dx('active')."= '1'",NULL,FALSE);
        //$this->db->where($this->dx('type').' = "2"',NULL,FALSE);
        if(empty($paying_group_ids)){
            $this->db->where($this->dx('group_id ').' IN ( 0 ) ',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id ').' IN ('.implode(',',$paying_group_ids).') ',NULL,FALSE);
        }
        $this->db->group_by(
            array(
                'year',
                'month'
            )
        );
        $result = $this->db->get('loans')->result();
        foreach($result as $row){
            $arr[$row->year][$row->month] = count($result);
        }
        return $arr;
    }

    function count_all_active_loans($from ,$to , $paying_group_ids){
        $arr = array();
        $this->db->select(
            array(
                'COUNT(DISTINCT(id)) as count',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%Y') as year ",
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%c') as month "
            )
        );
        $this->db->where($this->dx('created_on')." >= ".$from,NULL,FALSE);
        $this->db->where($this->dx('created_on')." <= ".$to,NULL,FALSE);
        $this->db->where($this->dx('active')."= '1'",NULL,FALSE);
        if(empty($paying_group_ids)){
            $this->db->where($this->dx('group_id ').' IN ( 0 ) ',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id ').' IN ('.implode(',',$paying_group_ids).') ',NULL,FALSE);
        }
        $this->db->group_by(
            array(
                'year',
                'month'
            )
        );
        $result = $this->db->get('loans')->result();
        foreach($result as $row){
            $arr[$row->year][$row->month] = $row->count;
        }
        return $arr;
    }
    function get_total_amount_loans($date ,$paying_group_ids){
        $this->db->select(
            array(
                'sum('.$this->dx('loan_amount ').') as amount'
        ));
        if($date){
            $this->db->where($this->dx('created_on').' >= "'.$date.'"',NULL,FALSE);
        }
        if(empty($paying_group_ids)){
            $this->db->where($this->dx('group_id ').' IN ( 0 ) ',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id ').' IN ('.implode(',',$paying_group_ids).') ',NULL,FALSE);
        }
        $this->db->where($this->dx('active')."= '1'",NULL,FALSE);
        return $this->db->get('loans')->row();
    }
    function get_total_amount_by_month_array_tests($from = 0 ,$to ,$paying_group_ids){
        $arr = array();
        $this->db->select(
            array(
                'sum('.$this->dx('loan_amount').') as amount',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%Y') as year ",
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%c') as month "
            )
        );
        $this->db->where($this->dx('created_on')." >= ".$from,NULL,FALSE);
        $this->db->where($this->dx('created_on')." <= ".$to,NULL,FALSE);
        $this->db->where($this->dx('active')."= '1'",NULL,FALSE);
        //$this->db->where($this->dx('type').' = "2"',NULL,FALSE);
        if(empty($paying_group_ids)){
            $this->db->where($this->dx('group_id ').' IN ( 0 ) ',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id ').' IN ('.implode(',',$paying_group_ids).') ',NULL,FALSE);
        }
        $this->db->group_by(
            array(
                'year',
                'month'
            )
        );
        $result = $this->db->get('loans')->result();
        foreach($result as $row){
            $arr[$row->year][$row->month] = $row->amount;
        }
        return $arr;
    }

    function count_all_group_loans($filter_parameters=array(),$group_id=0){
        
        if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
            if($filter_parameters['from'] && $filter_parameters['to']){
                $this->db->where($this->dx('disbursement_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
                $this->db->where($this->dx('disbursement_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
            }
        }

        if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){

            if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
                $member_list = '0';
                $members = $filter_parameters['member_id'];
                $count = 1;
                foreach($members as $member_id){
                    if($member_id){
                        if($count==1){
                            $member_list = $member_id;
                        }else{
                            $member_list .= ','.$member_id;
                        }
                        $count++;
                    }
                }

                if($member_list){
                    $this->db->where($this->dx('member_id').' IN ('.$member_list.')',NULL,FALSE);
                }
            }
        }

        if(isset($filter_parameters['accounts']) && $filter_parameters['accounts']){
            $account_list = '0';
            $accounts = $filter_parameters['accounts'];
            $count = 1;
            foreach($accounts as $account_id){
                if($account_id){
                    if($count==1){
                        $account_list = '"'.$account_id.'"';
                    }else{
                        $account_list .= ',"'.$account_id.'"';
                    }
                    $count++;
                }
            }
            if($account_list){
                $this->db->where($this->dx('account_id').' IN ('.$account_list.')',NULL,FALSE);
            }
        }

        if(isset($filter_parameters['is_fully_paid']) && $filter_parameters['is_fully_paid']){
            if($filter_parameters['is_fully_paid']==1){
                $this->db->where($this->dx('is_fully_paid').' = "1" ',NULL,FALSE);
            }else if($filter_parameters['is_fully_paid']==0){
                $this->db->where("(".$this->dx('is_fully_paid').' = "0" OR '.$this->dx('is_fully_paid').' IS NULL OR '.$this->dx('is_fully_paid').' = "" )',NULL,FALSE);
            }
        }
        if($group_id){
            $this->db->where($this->dx('loans.group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loans.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);    
        return $this->count_all_results('loans');
    }

    function count_all_member_loans($group_id=0,$member_id=0){
        if($group_id){
            $this->db->where($this->dx('loans.group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loans.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        if($member_id){
            $this->db->where($this->dx('loans.member_id').'="'.$member_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loans.member_id').'="'.$this->member->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);    
        return $this->count_all_results('loans');
    }

    function get_loans_interest_and_principle_amount($loan_id=0)
    {

        $this->db->select('sum('.$this->dx('interest_amount_payable').') as amount');
        $this->db->where($this->dx('loan_id').'= "'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);

        $interest_amount = $this->db->get('loan_invoices')->row()->amount;

        $this->db->select('sum('.$this->dx('principle_amount_payable').') as amount');
        $this->db->where($this->dx('loan_id').'= "'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);

        $principle_amount = $this->db->get('loan_invoices')->row()->amount;

        return $interest_amount + $principle_amount;

    }

    function is_loan_editable($id)
    {
        /**
            1. check if the loan has any payments made.
            2. Check the day the loan was created
            3. if a custom loan 

        **/
        $this->db->where($this->dx('loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $payments = $this->db->count_all_results('loan_repayments');

         $this->db->where($this->dx('interest_type').'="3"',NULL,FALSE);
         $custom = $this->db->count_all_results('loans');



        if($payments || $custom)
        {
            return TRUE;
        }
        else
        {
            return TRUE;
        }

    }


    function loan_exists_in_group($id=0,$group_id=0,$member_id=0){
        $this->db->where('id',$id);
        if($group_id){
            $this->db->where($this->dx('group_id')." = '".$group_id."'",NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id')." = '".$this->group->id."'",NULL,FALSE);
        }
        if($member_id){
            $this->db->where($this->dx('member_id')." = '".$member_id."'",NULL,FALSE);
        }else{
            $this->db->where($this->dx('member_id')." = '".$this->member->id."'",NULL,FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->count_all_results('loans')?:0;        
    }


    
    function get_loan_installments($id=0)
    {
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_invoices.loan_id').'="'.$id.'"',NULL,FALSE);
        return $this->db->get('loan_invoices')->result();
    }

    function get_total_defaulted_loan_amount($group_id=0){
        $this->db->select('sum('.$this->dx('loans.loan_amount').') as amount');
        $this->db->where($this->dx("loans.active").'="1"',NULL,FALSE);
        $this->db->where($this->dx("loans.created_on").'<="'.time().'"',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('is_a_bad_loan').' = "1"',NULL,FALSE);
        $amount = $this->db->get('loans')->row();
        if($amount){
            return $amount->amount?:0;
        }else{
            return 0;
        }
    }

    function count_total_defaulted_loans($group_id=0){
        $this->select_all_secure('loans');
        $this->db->where($this->dx("loans.active").'="1"',NULL,FALSE);
        $this->db->where($this->dx("loans.created_on").'<="'.time().'"',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('is_a_bad_loan').' = "1"',NULL,FALSE);
        return $this->db->count_all_results('loans');
    }

    /*****Loans Statements***/

    function insert_loan_statement($input=array(),$SKIP_VALIDATION=FALSE)
    {
        return $this->insert_secure_data('loan_statements',$input);
    }

    function update_statement($id,$input,$val=FALSE){
        return $this->update_secure_data($id,'loan_statements',$input);
    }

    function update_statement_payment($loan_payment_id=0){
        return $this -> db -> query("update loan_statements set 
                active=".$this->exa('0').",
                status = ".$this->exa('0')." ,
                modified_on = ".$this->exa(time())." 
                where ".$this->dx("loan_payment_id")." ='".$loan_payment_id."'");  
    }

    function update_statement_invoice_void($loan_invoice_id=0){
        return $this -> db -> query("update loan_statements set 
                active=".$this->exa('0').",
                status = ".$this->exa('0')." 
                where ".$this->dx("loan_invoice_id")." ='".$loan_invoice_id."'"); 
    }

    function get_loan_statement($id=0)
    {
        $this->select_all_secure('loan_statements');
        $this->db->where($this->dx('loan_statements.loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where('('.$this->dx('transaction_type').'="4" OR '.$this->dx('transaction_type').'="5")',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->order_by($this->dx('transaction_date'),'ASC',FALSE);
        return $this->db->get('loan_statements')->result();
    }

    function get_loan_statement_for_library($id=0)
    {
        $this->select_all_secure('loan_statements');
        $this->db->where($this->dx('loan_statements.loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx('loan_statements.active').'="1"',NULL,FALSE);
        $this->db->order_by($this->dx('transaction_date'),'ASC',FALSE);
        return $this->db->get('loan_statements')->result();
    }

    function get_all_member_loan_statement($loan_id=0){
       $this->select_all_secure('loan_statements');
        $this->db->where($this->dx('loan_statements.loan_id').'="'.$loan_id.'"',NULL,FALSE);
        //$this->db->where($this->dx('loan_statements.active').'="1"',NULL,FALSE);
        $this->db->order_by($this->dx('transaction_date'),'ASC',FALSE);
        return $this->db->get('loan_statements')->result(); 
    }

    function get_loan_statement_for_library_for_payments($loan_id=0){
        $this->select_all_secure('loan_statements');
        $this->db->where($this->dx('loan_statements.loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('loan_statements.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('loan_statements.transaction_type').'="4"',NULL,FALSE);
        $this->db->order_by($this->dx('transaction_date'),'ASC',FALSE);
        $this->db->order_by($this->dx('created_on'),'ASC',FALSE);
        return $this->db->get('loan_statements')->result();
    }

    function get_transfer_out_invoice($loan_id=0){
        $this->select_all_secure('loan_statements');
        $this->db->where($this->dx('loan_statements.loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('loan_statements.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('loan_statements.transaction_type').'="5"',NULL,FALSE);
        return $this->db->get('loan_statements')->result();
    }

    function get_loan_balance($id=0){
        $this->db->select(array($this->dx('loan_statements.balance').' as balance'));
        $this->select_all_secure('loan_statements');
        $this->db->where($this->dx('loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->order_by($this->dx('id'),'DESC',FALSE);
        $this->db->order_by($this->dx('transaction_date').'+0','DESC',FALSE);
        $this->db->order_by($this->dx('created_on').'+0','DESC',FALSE);
        $this->db->order_by($this->dx('loan_invoice_id').'+0','DESC',FALSE);
        $this->db->limit(1);
        $query = $this->db->get('loan_statements');
        $result = $query->row();
        $query->free_result();
        if($result){
            if($result->balance<=0){
                $this->db->select('sum('.$this->dx('amount_payable').') - sum('.$this->dx('amount_paid').') as balance');
                $this->db->where($this->dx('active').'="1"',NULL,FALSE);
                $this->db->where($this->dx('loan_id').'="'.$id.'"',NULL,FALSE);
                $balance = $this->db->get('loan_invoices')->row()->balance;
                if($balance){
                    return $balance;
                }else{
                    return 0;
                }
            }else{
                return $result->balance;
            }
        }else{
            return 0;
        }
    }

    function get_unpaid_loan_balance($id){
        $this->db->select('sum('.$this->dx('amount_payable').') - sum('.$this->dx('amount_paid').') as balance');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('loan_id').'="'.$id.'"',NULL,FALSE);
        $balance = $this->db->get('loan_invoices')->row()->balance;
        if($balance){
            return $balance;
        }else{
            return 0;
        }
    }

    function delete_all_loan_invoices_and_fines_entries($loan_id=0){
        $this->select_all_secure('loan_statements');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where('('.$this->dx('transaction_type').'="1" OR '.$this->dx('transaction_type').' ="2" OR '.$this->dx('transaction_type').' ="3")',NULL,FALSE);
        $result = $this->db->get('loan_statements')->result();
        if($result)
        {
            foreach ($result as $res) 
            {
                $this->db->where('id',$res->id);
                $this->db->delete('loan_statements');
            }
            return TRUE;
        }
        else
        {
            return TRUE;
        }
    }

    function void_statement_entries($loan_id=0){
        $this->select_all_secure('loan_statements');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $result = $this->db->get('loan_statements')->result();

        if($result)
        {
            foreach ($result as $res) 
            {
                $this->update_statement($res->id,array('active'=>NULL,'status'=>NULL));
            }
            return TRUE;
        }
        else
        {
            return TRUE;
        }
    }

    function void_loan_statement($loan_id = 0){
       $this->select_all_secure('loan_statements'); 
       $input = array(
            'active'=>0,
            'modified_on'=>time()
        );
       return $this->update_secure_data($loan_id,'loan_statements',$input);
    }


    function void_loan_payment_statement($loan_id = 0){
        return $this ->db->query("update loan_statements set 
                active=".$this->exa('0').",
                status = ".$this->exa('0')." 
                where ".$this->dx("loan_id")." ='".$loan_id."' AND ".$this->dx('transaction_type')." = '4'");  
    }

    /****************/

    function get_amount_payable_for_parent($loan_invoice_id=0)
    {
        $this->select_all_secure('loan_invoices');
        $this->db->where('id',$loan_invoice_id);
        $loan_invoice = $this->db->get('loan_invoices')->row();

        if($loan_invoice)
        {
            $this->select_all_secure('loan_invoices');
             $this->db->select(array(
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loan_invoices.due_date')." ),'%Y %D %M') as due_date2 ",
            ));
            $this->db->where('id',$loan_invoice->fine_parent_loan_invoice_id);
            $parent_loan_invoice = $this->db->get('loan_invoices')->row();

            //print_r($parent_loan_invoice);

            if($parent_loan_invoice)
            {
                $this->db->select('sum('.$this->dx('amount').') as amount');
                //$this->select_all_secure('loan_statements');
                /*$this->db->select(array(
                    "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loan_invoices.transaction_date')." ),'%Y %D %M') as transaction_date2 ",
                ));*/
                $this->db->where($this->dx('loan_id').'="'.$loan_invoice->loan_id.'"',NULL,FALSE);
                $this->db->where($this->dx('active').'="1"',NULL,FALSE); 
                $this->db->where('('.$this->dx('transaction_type').'= "1" OR '.$this->dx('transaction_type').'="2" OR '.$this->dx('transaction_type').' = "3" ) '); 
               // $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %D %M') < '" . date('Y jS F',$parent_loan_invoice->due_date) . "'", NULL, FALSE);
                $this->db->where($this->dx('transaction_date').' <="'.$parent_loan_invoice->due_date.'"',NULL,FALSE); 
                $amount = $this->db->get('loan_statements')->row();
                /*echo $parent_loan_invoice->due_date;
                print_r($amount);die;*/
                if($amount)
                {
                    return $amount->amount;
                }
                else
                {
                    return 0;
                }
            }
            else
            {
                return 0;
            }
        }
        else
        {
            return 0;
        }
        $this->db->select('sum('.$this->dx('amount').') as amount');
        $this->db->where("loan_id",$id);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE); 
        $this->db->where('('.$this->dx('transaction_type').' = "1" OR '.$this->dx('transaction_type').' = "2" OR '.$this->dx('transaction_type').' = "3" '); 
        $this->db->where($this->dx('transaction_date').' <="'.$date.'"',NULL,FALSE); 
        $amount = $this->db->get('loan_statements')->row();
        if($amount)
        {
            return $amount->amount;
        }
        else
        {
            return 0;
        }
    }

    function get_current_amount_paid($loan_id=0,$date=0)
    {
        $this->db->select('sum('.$this->dx('amount').') as amount');
        $this->db->where("loan_id",$loan_id);
        $this->db->where($this->dx('active').'= "1"',NULL,FALSE); 
        $this->db->where($this->dx('transaction_type').'= "4"',NULL,FALSE); 
        $this->db->where($this->dx('transaction_date').'<="'.$date.'"',NULL,FALSE); 
        $amount = $this->db->get('loan_statements')->row();
        if($amount)
        {
            return $amount->amount;
        }
        else
        {
            return 0;
        }
    }

     function delete_all_loan_statement_entries($loan_id=0)
    {
        $this->select_all_secure('loan_statements');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $result = $this->db->get('loan_statements')->result();

        if($result)
        {
            foreach ($result as $res) 
            {
                $this->db->where('id',$res->id);
                $this->db->delete('loan_statements');
            }
            return TRUE;
        }
        else
        {
            return TRUE;
        }

    }

    /****guarantors***/

    function insert_loan_guarontors($input=array(),$SKIP_VALIDATION=FALSE)
    {
        return $this->insert_secure_data('loan_guarantors',$input);
    }


    function get_loan_guarantors($loan_id=0)
    {
        $this->db->select(array(
                $this->dx('member_id').' as guarantor_id',
                $this->dx('guaranteed_amount').' as guaranteed_amount',
                $this->dx('comment').' as guarantor_comment',
                $this->dx('users.first_name').' as guarantor_first_name',
                $this->dx('users.last_name').' as guarantor_last_name',
                $this->dx('users.email').' as guarantor_email',
                $this->dx('users.phone').' as guarantor_phone',

            ));
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('loan_guarantors.active').'="1"',NULL,FALSE);
        $this->db->join('members',$this->dx('loan_guarantors.member_id').' = members.id');
        $this->db->join('users',$this->dx('members.user_id').' = users.id');
        return $this->db->get('loan_guarantors')->result();
    }

    function get_loan_application_guarantorship_requests($loan_application_id = 0){
        $this->db->select(array(
                'loan_guarantorship_requests.id as id',
                $this->dx('loan_application_id').' as loan_application_id',
                $this->dx('guarantor_member_id').' as guarantor_member_id',
                $this->dx('decline_reason').' as decline_reason',
                $this->dx('comment').' as comment',
                $this->dx('loan_guarantorship_requests.active').' as active',
                $this->dx('loan_guarantorship_requests.modified_by').' as modified_by',
                $this->dx('amount').' as amount',
                $this->dx('comment').' as guarantor_comment',
                $this->dx('is_declined').' as is_declined',
                $this->dx('is_approved').' as is_approved',
                $this->dx('users.first_name').' as guarantor_first_name',
                $this->dx('users.last_name').' as guarantor_last_name',
                $this->dx('users.email').' as guarantor_email',
                $this->dx('users.phone').' as guarantor_phone',
                $this->dx('loan_guarantorship_requests.created_on').' as created_on',
            ));
        $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',NULL,FALSE);
        $this->db->join('members',$this->dx('loan_guarantorship_requests.guarantor_member_id').' = members.id');
        $this->db->join('users',$this->dx('members.user_id').' = users.id');
        return $this->db->get('loan_guarantorship_requests')->result();
    }

    function get_loan_application_signatory_requests($loan_application_id = 0){
        $this->db->select(array(
            'loan_signatory_requests.id as id',
            $this->dx('loan_signatory_requests.created_on').' as created_on',
            $this->dx('signatory_member_id').' as signatory_member_id',
            $this->dx('is_declined').' as is_declined',
            $this->dx('loan_signatory_requests.active').' as active',
            $this->dx('decline_reason').' as decline_reason',
            $this->dx('loan_signatory_requests.modified_by').' as modified_by',
            $this->dx('is_approved').' as is_approved',
            $this->dx('users.first_name').' as signatory_first_name',
            $this->dx('users.last_name').' as signatory_last_name',
            $this->dx('users.email').' as signatory_email',
            $this->dx('users.phone').' as signatory_phone',
        ));
        $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',NULL,FALSE);
        $this->db->join('members',$this->dx('loan_signatory_requests.signatory_member_id').' = members.id');
        $this->db->join('users',$this->dx('members.user_id').' = users.id');
        return $this->db->get('loan_signatory_requests')->result();
    }
    function get_applicant_loan_by_phone($phone=''){
        $result=array();
        $member=$this->members_m->get_applicant_by_phone_number(valid_phone($phone));
        if($member){
            $this->select_all_secure('loans');
            $this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
            $this->db->where($this->dx('member_id')." = '".$member->id."' ",NULL,FALSE);
            $this->db->where('('.$this->dx('is_fully_paid').'="" OR '.$this->dx('is_fully_paid').' IS NULL OR '.$this->dx('is_fully_paid').'="0")',NULL,FALSE);
            $this->db->limit(1);
            return $this->db->get('loans')->row();
        }
        else{
            return $result;
        }
      
    }
    function get_loan_application_signatory_request($loan_application_id = 0,$signatory_member_id = 0){
        $this->select_all_secure('loan_signatory_requests');
        $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',NULL,FALSE);
        $this->db->where($this->dx('signatory_member_id').'="'.$signatory_member_id.'"',NULL,FALSE);
        return $this->db->get('loan_signatory_requests')->row();
    }

    function check_if_loan_guarantors_exist($loan_application_id = 0 ){
        $this->select_all_secure('loan_guarantorship_requests');
        $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',NULL,FALSE);
        return $this->db->count_all_results('loan_guarantorship_requests');
    }

    function get_loan_guarantorship_request_array($loan_application_ids = array() ,$loan_type_ids = array(),$group_id =0){
        $this->select_all_secure('loan_guarantorship_requests');
       // $this->db->where($this->dx('active').'="1"',Null,FALSE);
        $this->db->where($this->dx('group_id').'='.$group_id.'',Null,FALSE);
        if(empty($loan_application_ids)){
            $this->db->where($this->dx('loan_application_id').' IN ( 0 ) ',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loan_application_id').' IN ( '.implode(",",$loan_application_ids)." ) ",NULL,FALSE);
        }
        if(empty($loan_type_ids)){
            $this->db->where($this->dx('loan_type_id').' IN ( 0 ) ',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loan_type_id').' IN ( '.implode(",",$loan_type_ids)." ) ",NULL,FALSE);
        }
        $this->db->order_by($this->dx('loan_application_id'));
        return $this->db->get('loan_guarantorship_requests')->result();
    }

    function get_loan_guarantorship_request_per_member_array($loan_application_ids = array() ,$loan_type_ids = array(),$group_id =0 , $member_id =0 ){
        $this->select_all_secure('loan_guarantorship_requests');
       // $this->db->where($this->dx('active').'="1"',Null,FALSE);
        $this->db->where($this->dx('group_id').'='.$group_id.'',Null,FALSE);
        if(empty($loan_application_ids)){
            $this->db->where($this->dx('loan_application_id').' IN ( 0 ) ',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loan_application_id').' IN ( '.implode(",",$loan_application_ids)." ) ",NULL,FALSE);
        }
        if(empty($loan_type_ids)){
            $this->db->where($this->dx('loan_type_id').' IN ( 0 ) ',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loan_type_id').' IN ( '.implode(",",$loan_type_ids)." ) ",NULL,FALSE);
        }
        if($member_id){
            $this->db->where($this->dx('guarantor_member_id').'="'.$member_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('guarantor_member_id').'="'.$this->member->id.'"',NULL,FALSE);
        }        
        $this->db->order_by($this->dx('loan_application_id'));
        return $this->db->get('loan_guarantorship_requests')->result();
    }

    function get_loan_signatory_request_array($loan_application_ids = array() ,$loan_type_ids = array(),$group_id =0){
        $this->select_all_secure('loan_signatory_requests');
        $this->db->where($this->dx('active').'="1"',Null,FALSE);
        $this->db->where($this->dx('group_id').'='.$group_id.'',Null,FALSE);
        if(empty($loan_application_ids)){
            $this->db->where($this->dx('loan_application_id').' IN ( 0 ) ',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loan_application_id').' IN ( '.implode(",",$loan_application_ids)." ) ",NULL,FALSE);
        }
        if(empty($loan_type_ids)){
            $this->db->where($this->dx('loan_type_id').' IN ( 0 ) ',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loan_type_id').' IN ( '.implode(",",$loan_type_ids)." ) ",NULL,FALSE);
        }
        return $this->db->get('loan_signatory_requests')->result();
    }

    function get_loan_signatory_request_per_member_array($loan_application_ids = array() ,$loan_type_ids = array(),$group_id =0 , $member_id = 0){
        $this->select_all_secure('loan_signatory_requests');
        $this->db->where($this->dx('active').'="1"',Null,FALSE);
        $this->db->where($this->dx('group_id').'='.$group_id.'',Null,FALSE);
        if(empty($loan_application_ids)){
            $this->db->where($this->dx('loan_application_id').' IN ( 0 ) ',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loan_application_id').' IN ( '.implode(",",$loan_application_ids)." ) ",NULL,FALSE);
        }
        if(empty($loan_type_ids)){
            $this->db->where($this->dx('loan_type_id').' IN ( 0 ) ',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loan_type_id').' IN ( '.implode(",",$loan_type_ids)." ) ",NULL,FALSE);
        }
        if($member_id){
            $this->db->where($this->dx('signatory_member_id').'="'.$member_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('signatory_member_id').'="'.$this->member->id.'"',NULL,FALSE);
        } 
        return $this->db->get('loan_signatory_requests')->result();
    }

    function delete_loan_guarantors($loan_id=0)
    {
        $this->select_all_secure('loan_guarantors');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $result = $this->db->get('loan_guarantors')->result();

        if($result)
        {
            foreach ($result as $res) 
            {
                $this->db->where('id',$res->id);
                $this->db->delete('loan_guarantors');
            }
            return TRUE;
        }
        else
        {
            return TRUE;
        }

    }
    function void_loan_guarantors($loan_id=0)
    {
        $this->select_all_secure('loan_guarantors');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $result = $this->db->get('loan_guarantors')->result();

        if($result)
        {
            foreach ($result as $res) 
            {
                $this->update_secure_data($res->id,'loan_guarantors',array('active'=>NULL));
            }
            return TRUE;
        }
        else
        {
            return TRUE;
        }

    }

    function get_this_group_currency($group_id=0)
    {
       $this->db->select(array(
                $this->dx('countries.currency_code').' as currency_code'
            ));
        $this->db->where('investment_groups.id',$group_id?:$this->group_id);
        $this->db->join('countries','countries.id = '.$this->dx('currency_id'));
        return $this->db->get('investment_groups')->row()->currency_code;
    }


    function get_loans_to_fine_outstanding_balance($date=0){  
        if($date){
        }else{
            $date=time();
        }
        $this->select_all_secure('loans');
        $this->db->select(array(
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loans.outstanding_loan_balance_fine_date')." ),'%Y %D %M') as outstanding_balance_fine_date ",
            ));
        $this->db->where($this->dx('loans.active').'="1"',NULL,FALSE);
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loans.outstanding_loan_balance_fine_date')."),'%Y %D %M') = '". date('Y jS F',$date) . "'", NULL, FALSE);
        $this->db->where('('.$this->dx('is_fully_paid').' IS NULL OR '.$this->dx('is_fully_paid').' ="" OR '.$this->dx('is_fully_paid').' ="0" OR '.$this->dx('is_fully_paid').' = " " )',NULL,FALSE);
        $this->db->where($this->dx('enable_outstanding_loan_balance_fines').'="1"',NULL,FALSE);
        return $this->db->get('loans')->result();
    } 

    function get_total_loaned_amount($group_id=0,$from=0,$to=0){
        $this->db->select('sum('.$this->dx('loans.loan_amount').') as amount');
        $this->db->where($this->dx("loans.active").'="1"',NULL,FALSE);
        $this->db->where($this->dx("loans.created_on").'<="'.time().'"',NULL,FALSE);
       
        if($from&&$to){
            $this->db->where($this->dx('disbursement_date').' >= "'.$from.'"',NULL,FALSE);
            $this->db->where($this->dx('disbursement_date').' <= "'.$to.'"',NULL,FALSE);
        }
        $amount = $this->db->get('loans')->row();
        if($amount){
            return $amount->amount?:0;
        }else{
            return 0;
        }
    }

    function get_total_member_loaned_amount($group_id = 0,$member_id = 0){
        $this->db->select('sum('.$this->dx('loans.loan_amount').') as amount');
        $this->db->where($this->dx("loans.active").'="1"',NULL,FALSE);
        $this->db->where($this->dx("loans.created_on").'<="'.time().'"',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE);
        $amount = $this->db->get('loans')->row();
        if($amount){
            return $amount->amount?:0;
        }else{
            return 0;
        }
    }

    function get_many_by($params = array(),$group_id = 0){   
        $this->select_all_secure('loans');
        foreach($params as $column_name => $value){
            $column_name = trim($column_name);
            if($column_name=='id'){
                $this->db->where('id',$value);
            }elseif($column_name == 'from' || $column_name == 'to'){
                if($column_name=='from'){
                    $this->db->where($this->dx('disbursement_date').'>="'.$value.'"',NULL,FALSE);
                }elseif($column_name=='to'){
                    $this->db->where($this->dx('disbursement_date').'<="'.$value.'"',NULL,FALSE);
                }
            }elseif($column_name == 'is_fully_paid'){
                if($value=='0'){
                     
                    $this->db->where('('.$this->dx('is_fully_paid').' = "" OR '.$this->dx('is_fully_paid').' IS NULL OR '.$this->dx('is_fully_paid').' = "0" )',NULL,FALSE);
                }   
            }
            else{
                if($value){
                    if(is_array($value)){
                        $list = '';
                        if(!empty($value)){
                            foreach ($value as $value_list) {
                                if($list){
                                    $list.=','.$value_list;
                                }else{
                                    $list = $value_list;
                                }
                            }
                            if($list){
                                $this->db->where($this->dx($column_name).' IN('.$value_list.')',NULL,FALSE);
                            }
                        }
                    }else{
                        $this->db->where($this->dx($column_name).'="'.$value.'"',NULL,FALSE);
                    }
                }else{
                    //$this->db->where("(".$this->dx($column_name).'="0" OR '.$this->dx($column_name).'="" OR '.$this->dx($column_name).' IS NULL OR '.$this->dx($column_name).' =" " )',NULL,FALSE);
                }
            }
        } 
      
        $this->db->where($this->dx('active').' ="1"',NULL,FALSE);
        $this->db->order_by($this->dx('disbursement_date'),'DESC',FALSE);
        return $this->db->get('loans')->result();
    }

    function get_summation_for_invoice($loan_id=0)
    {
        $this->db->select(array(
                'sum('.$this->dx('interest_amount_payable').') as total_interest_payable',
                'sum('.$this->dx('principle_amount_payable').') as total_principle_payable',
                'sum('.$this->dx('amount_payable').') as total_amount_payable',
                'sum('.$this->dx('amount_paid').') as total_amount_paid',
            ));
        $this->select_all_secure('loans');
        $this->db->where($this->dx('loan_invoices.loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('loan_invoices.active').'="1"',NULL,FALSE);
        $this->db->join('loans',$this->dx('loan_invoices.loan_id').'= loans.id');
        return $this->db->get('loan_invoices')->row();
    }
    function _get_group_aging_loans_for_all_classes($filter_parameters = array(),$group_id = 0,$include_invoice_details=FALSE){
        $this->select_all_secure('loans');
        if($include_invoice_details){
            $this->db->select(
                array(
                    $this->dx('loan_invoices.amount_payable').' as amount_payable',
                    $this->dx('loan_invoices.principle_amount_payable').' as principle_payable',
                    $this->dx('loan_invoices.interest_amount_payable').' as interest_payable',
                    $this->dx('loan_invoices.amount_paid').' as amount_paid',
                    $this->dx('loan_repayments.receipt_date').' as receipt_date',
                    $this->dx('users.first_name').' as first_name',
                    $this->dx('users.last_name').' as last_name',
                    $this->dx('users.phone').' as phone',
                    $this->dx('users.email').' as email',
                )
            );
        }
        if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){

            if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
                $member_list = '0';
                $members = $filter_parameters['member_id'];
                $count = 1;
                foreach($members as $member_id){
                    if($member_id){
                        if($count==1){
                            $member_list = $member_id;
                        }else{
                            $member_list .= ','.$member_id;
                        }
                        $count++;
                    }
                }

                if($member_list){
                    $this->db->where($this->dx('member_id').' IN ('.$member_list.')',NULL,FALSE);
                }
            }
        }

        if(isset($filter_parameters['created_by']) && $filter_parameters['created_by']){

            if(isset($filter_parameters['created_by']) && $filter_parameters['created_by']){
                $created_by_list = '0';
                $created_bys = $filter_parameters['created_by'];
                $count = 1;
                foreach($created_bys as $created_by){
                    if($created_by){
                        if($count==1){
                            $created_by_list = $created_by;
                        }else{
                            $created_by_list .= ','.$created_by;
                        }
                        $count++;
                    }
                }

                if($created_by_list){
                    $this->db->where($this->dx('created_by').' IN ('.$created_by_list.')',NULL,FALSE);
                }
            }
        }

        if(isset($filter_parameters['accounts']) && $filter_parameters['accounts']){
            $account_list = '0';
            $accounts = $filter_parameters['accounts'];
            $count = 1;
            foreach($accounts as $account_id){
                if($account_id){
                    if($count==1){
                        $account_list = '"'.$account_id.'"';
                    }else{
                        $account_list .= ',"'.$account_id.'"';
                    }
                    $count++;
                }
            }
            if($account_list){
                $this->db->where($this->dx('account_id').' IN ('.$account_list.')',NULL,FALSE);
            }
        }

        if(isset($filter_parameters['is_fully_paid']) && $filter_parameters['is_fully_paid']){
            if($filter_parameters['is_fully_paid']==1){
                $this->db->where($this->dx('is_fully_paid').' = "1" ',NULL,FALSE);
            }else if($filter_parameters['is_fully_paid']==0){
                $this->db->where($this->dx('is_fully_paid').' = "0" ',NULL,FALSE);
                
                $this->db->or_where($this->dx('is_fully_paid').' = "IS NULL" ',NULL,FALSE);
            }
        }
        
        //check for loan type.
        if(isset($filter_parameters['loan_type']) && $filter_parameters['loan_type']){
			$loan_types_id_list = '0';
			$loan_types = $filter_parameters['loan_type'];
			$count = 1;
			foreach($loan_types as $loan_type_id){
				if($loan_type_id){
					if($count==1){
						$loan_types_id_list = $loan_type_id;
					}else{
						$loan_types_id_list .= ','.$loan_type_id;
					}
					$count++;
				}
			}
			if($loan_types_id_list){
        		$this->db->where($this->dx('loan_type_id').' IN ('.$loan_types_id_list.')',NULL,FALSE);
			}
		}

        // // if($include_invoice_details){
            $this->db->join('loan_invoices',$this->dx('loan_invoices.loan_id').'= loans.id');
            // $this->db->join('loan_repayments',$this->dx('loan_repayments.loan_id').'= loans.id');
            $this->db->join('members','members.id = '.$this->dx('loan_invoices.member_id'));
            $this->db->join('users','users.id = '.$this->dx('members.user_id'));
            $this->db->group_by('loan_invoices.loan_id');
             
        // }

        $this->db->where($this->dx('loans.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('loans.is_fully_paid')." IS NULL", NULL, FALSE);
        $this->db->order_by($this->dx('loans.disbursement_date'),'DESC',FALSE);
        $this->db->order_by($this->dx('loans.created_on'),'DESC',FALSE);
        // $this->db->limit(100);
        return $this->db->get('loans')->result();
    }
    function loan_payable_and_principle_todate($loan_id=0)
    {
        $this->db->select(array(
                'sum('.$this->dx('loan_invoices.amount_payable').') as todate_amount_payable',
                'sum('.$this->dx('loan_invoices.principle_amount_payable').') as todate_principle_payable'
            ));
        $this->db->where($this->dx('loan_invoices.loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('loan_invoices.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('due_date').' <"'.time().'"',NULL,FALSE);
        $result =  $this->db->get('loan_invoices')->row();

        return $result;

    }


    function get_projected_interest($loan_id = 0,$amount_paid = 0,$group_id=0){
        if($amount_paid){
            $this->select_all_secure('loan_invoices');
            $this->db->where($this->dx("loans.active").'="1"',NULL,FALSE);
            $this->db->where($this->dx('loan_invoices.active').'="1"',NULL,FALSE);
            $this->db->where($this->dx('loan_invoices.loan_id').'="'.$loan_id.'"',NULL,FALSE);
         
            
            $this->db->join('loans',$this->dx('loan_invoices.loan_id').'= loans.id');
            $this->db->order_by($this->dx('loan_invoices.invoice_date'),'ASC',FALSE);
            $loan_invoices = $this->db->get('loan_invoices')->result();

            if(!empty($loan_invoices)){
                $projected_interest = 0;
                foreach($loan_invoices as $loan_invoice){
                    if($amount_paid>0){
                        if($loan_invoice->type==1){
                            if($loan_invoice->amount_payable<=$amount_paid){
                                $projected_interest+=$loan_invoice->interest_amount_payable;
                            }else{
                                if($loan_invoice->principle_amount_payable<=$amount_paid){
                                    $projected_interest+=$amount_paid - $loan_invoice->principle_amount_payable;
                                }else{
                                    //do nothing
                                }
                            }
                        }else if($loan_invoice->type==2||$loan_invoice->type==3){
                            if($loan_invoice->amount_payable<=$amount_paid){
                                $projected_interest+=$loan_invoice->amount_payable;
                            }else{
                                $projected_interest+=$amount_paid;
                            }
                        }
                        $amount_paid-=$loan_invoice->amount_payable;
                    }
                }
                return $projected_interest;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
        
    }



    /***loan processing***/


    function void_loan_processing($loan_id=0){
        return TRUE;
    }

    function get_group_member_back_dated_loans(){
        $this->select_all_secure('loans');
        
        $this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
        $this->db->where($this->dx('is_a_back_dating_record')." = '1' ",NULL,FALSE);
        return $this->db->get('loans')->result();
    }

    function update_group_back_dating_loan_statements_cut_off_date($group_id = 0,$input = array()){
        $where = " ".$this->dx('is_a_back_dating_record')." = '1' AND ".$this->dx('group_id')." = '".$group_id."' AND ".$this->dx('active')." = '1' ;";
        if($this->update_loan_statements_where($where,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function update_group_back_dating_loans_cut_off_date($group_id = 0,$input = array()){
        $where = " ".$this->dx('is_a_back_dating_record')." = '1' AND ".$this->dx('group_id')." = '".$group_id."' AND ".$this->dx('active')." = '1' ;";
        if($this->update_where($where,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function get_fully_paid_loans_id($group_id=0,$from=0,$to=0){
        $this->db->select('id');
        $this->db->where($this->dx('is_fully_paid').' ="1"',NULL,FALSE);
      
        if($from){
            $this->db->where($this->dx('disbursement_date').'>="'.$from.'"',NULL,FALSE);
        }
        if($to){
            $this->db->where($this->dx('disbursement_date').'<="'.$to.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
        $ids = $this->db->get('loans')->result();
        $arr = array();
        foreach ($ids as $value) {
            $arr[] = $value->id;
        }
        return $arr;
    }

    function get_voided_loans(){
        $this->select_all_secure('loans');
        $this->db->where($this->dx('active')." = 0 ",NULL,FALSE);
        return $this->db->get('loans')->result();
    }

    function get_group_total_outstanding_loans_per_member_array($group_id=0,$group_member_options=array()){ 
    print_r($group_id);
    print_r($group_member_options)     ; die(); 


    }


    function count_group_loan_types($id = 0,$group_id=0){
        $this->db->where($this->dx('loans.loan_type_id'). ' = "'.$id.'"',NULL,FALSE);
       
        return $this->db->count_all_results('loans');
    }

    function get_total_loans_of_that_month($from ='' ,$to ='' ,$paying_group_ids = array()){
        $arr = array(); 
        //$this->select_all_secure('withdrawals');  
        $this->db->select(
            array(
                $this->dx('loan_amount').' as amount',
                $this->dx('group_id').' as group_id',
                $this->dx('member_id  ').' as member_id',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%Y') as year ",
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%c') as month "
            )
        );
        //$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')."),'%Y  %M') = '" . date('Y  F',$from) . "'", NULL, FALSE);
        $this->db->where($this->dx('created_on')." >= ".$from,NULL,FALSE);
        $this->db->where($this->dx('created_on')." <= ".$to,NULL,FALSE);
        $this->db->where($this->dx('active')."= '1'",NULL,FALSE);
        if(empty($paying_group_ids)){
            $this->db->where($this->dx('group_id ').' IN ( 0 ) ',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id ').' IN ('.implode(',',$paying_group_ids).') ',NULL,FALSE);
        }
        $result = $this->db->get('loans')->result();
        foreach($result as $row){
            $arr[$row->year][$row->month] = $row->amount;
        }
        return $result;
        //return $arr;
    }

    function get_group_member_loan_guarantors(){
        $this->db->select(
            array(
                $this->dx('loans.member_id')." as guaranteed_member_id ",
                $this->dx('loans.loan_amount')." as loan_amount ",
                $this->dx('loans.disbursement_date')." as disbursement_date ",
                $this->dx('loans.interest_rate')." as interest_rate ",
                $this->dx('loans.loan_interest_rate_per')." as loan_interest_rate_per ",
                $this->dx('loans.repayment_period')." as repayment_period ",
                $this->dx('loans.interest_type')." as interest_type ",
            )
        );
        $this->select_all_secure('loan_guarantors');
        $this->db->where($this->dx('loan_guarantors.group_id')." = '".$this->group->id."' ",NULL,FALSE);
        $this->db->where($this->dx('loan_guarantors.active')." = '1' ",NULL,FALSE);
        $this->db->where($this->dx('loans.active')." = '1' ",NULL,FALSE);
        $this->db->where('('.$this->dx('loans.is_fully_paid').' = "" OR '.$this->dx('loans.is_fully_paid').' IS NULL OR '.$this->dx('loans.is_fully_paid').' = "0" )',NULL,FALSE);
        $this->db->join('loans',$this->dx('loan_guarantors.loan_id')." = loans.id ");
        $this->db->join('members',$this->dx('loan_guarantors.member_id')." = members.id ");
        $this->db->join('users',$this->dx('members.user_id')." = users.id ");
        $this->db->order_by($this->dx('users.first_name'),'ASC',FALSE);
        /**
        $this->db->group_by(
            array(
                $this->dx('loan_guarantors.member_id')
            )
        );
        **/
        return $this->db->get('loan_guarantors')->result();
    }

    function get_group_total_principal_loans_out_per_year_array($group_id = 0){
        $arr = array();

        $this->db->select(
            array(
                'SUM('.$this->dx('loan_amount').') as amount ',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('disbursement_date')." ),'%Y') as year ",
            )
        );
    
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->order_by($this->dx('disbursement_date'),'ASC',FALSE);
        $this->db->group_by(
            array(
                'year',
            )
        );
        $loans = $this->db->get('loans')->result();

        foreach($loans as $loan):
            if(isset($arr[$loan->year])){
                $arr[$loan->year] += $loan->amount;
            }else{
                $arr[$loan->year] = $loan->amount;
            }
        endforeach;

        // foreach($arr as $key => $value):
        //     if(isset($arr[($key - 1)])){
        //         $arr[$key] += $arr[($key - 1)];
        //     }
        // endforeach;

        ksort($arr);

        $current_year = date('Y');

        foreach($loans as $loan):
            $year = $loan->year + 1;
            for($i = $year; $i <= $current_year; $i++):
                if(isset($arr[$i])){
                    $arr[$i] += $loan->amount;
                }else{
                    $arr[$i] = $loan->amount;
                }
            endfor;
        endforeach;

        ksort($arr);


        $total_loan_principal_paid_per_year_array = $this->reports_m->get_group_total_loan_principal_paid_per_year_array($group_id);

        ksort($total_loan_principal_paid_per_year_array);

        foreach($total_loan_principal_paid_per_year_array as $year => $principal_paid):
            for($i = $year; $i <= $current_year; $i++):
                $arr[$i] -= $principal_paid;
            endfor;
        endforeach;
        
        return $arr;
    }

    function get_group_loan_overpayments($group_id = 0){
        $this->select_all_secure('loans');
        $this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
        $this->db->where($this->dx('is_fully_paid')." = '1' ",NULL,FALSE);
        $this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
        $loans = $this->db->get('loans')->result();
        $total_over_payments = 0;
        foreach($loans as $loan):
            $loan_balance = $this->get_loan_balance($loan->id);
            $total_over_payments += $loan_balance;
        endforeach;
        echo number_to_currency($total_over_payments)."<br/>";
    }

    function get_all_loans(){
        $this->db->select('id');
        $this->db->where('('.$this->dx('active').' = "" OR '.$this->dx('active').' IS NULL OR '.$this->dx('active').' ="" OR '.$this->dx('active').' ="0" )',NULL,FALSE);
        return $this->db->get('loans')->result();
    }

    function get_member_active_loan($member_id=0,$amount=0,$group_id=0,$disbursement_date=''){
        $this->select_all_secure('loans');
        $this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
        $this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
        $this->db->where($this->dx('member_id')." = '".$member_id."' ",NULL,FALSE);
        $this->db->where($this->dx('loan_amount')." = '".$amount."' ",NULL,FALSE);
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('disbursement_date')."),'%Y%m%d') ='" . date('Ymd',$disbursement_date) . "'", NULL, FALSE);
        return $this->db->get('loans')->result();
    }
    function get_member_unpaid_loan($member_id=0){
        $this->select_all_secure('loans');
        $this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
        $this->db->where($this->dx('member_id')." = '".$member_id."' ",NULL,FALSE);
        $this->db->where($this->dx('loan_amount')." = '".$amount."' ",NULL,FALSE);
        $this->db->where('('.$this->dx('is_fully_paid').'="" OR '.$this->dx('is_fully_paid').' IS NULL OR '.$this->dx('is_fully_paid').'="0")',NULL,FALSE);
        $this->db->limit(1);
        return $this->db->get('loans')->row();
    }

    function get_voided_loan($id=0,$group_id=0){
        $this->select_all_secure('loans');
        $this->db->where('id',$id);
        $this->db->where('('.$this->dx('active').' = "" OR '.$this->dx('active').' ="0" OR '.$this->dx('active').' =" " OR '.$this->dx('active').' IS NULL )',NULL,FALSE);
        $this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
        return $this->db->get('loans')->row();
    }

    function get_old_loan_repayment_statements_array($loan_repayment_ids = array()){
        $this->select_all_secure('loan_statements');
        if(empty($loan_repayment_ids)){
            $this->db->where($this->dx('loan_payment_id').' IN ( 0 ) ',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loan_payment_id').' IN ( '.implode(",",$loan_repayment_ids)." ) ",NULL,FALSE);
        }
        $this->db->where($this->dx('active').' = "1"',NULL,FALSE);  
        $this->db->order_by('id','ASC',FALSE);
        return $this->db->get('loan_statements')->result();
    }

    function void_group_member_loan_statements($loan_statement_ids = array()){
        $input = array(
            'active' => 0,
            'modified_on' => time()
        );
        if(empty($loan_statement_ids)){
            $loan_statement_id_list = "0";
        }else{
            $loan_statement_id_list = implode(",",$loan_statement_ids);
        }
        $where = " ".'id'." IN (".$loan_statement_id_list.") ";
        return $this->update_secure_where($where,'loan_statements',$input);
    }

    function get_total_loan_balances($group_id=0,$member_id=0){
        $this->db->select(array('id'));
      
        if($member_id){
            $this->db->where($this->dx('loans.member_id').'="'.$member_id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').' ="1"',NULL,FALSE);
        $this->db->order_by($this->dx('disbursement_date'),'DESC',FALSE);
        $this->db->where('('.$this->dx('loans.is_fully_paid').' = "" OR '.$this->dx('loans.is_fully_paid').' IS NULL OR '.$this->dx('loans.is_fully_paid').' = "0" )',NULL,FALSE);
        $loans = $this->db->get('loans')->result();
        $loan_list_ids = 0;
        if($loans){
            foreach ($loans as $loan) {
                if($loan_list_ids){
                    $loan_list_ids.=','.$loan->id;
                }else{
                    $loan_list_ids=$loan->id;
                }
            }
        }
        $this->db->select('sum('.$this->dx('amount_payable').') as amount_payable');
        $this->db->where($this->dx('loan_id').' IN('.$loan_list_ids.')',NULL,FALSE);
        //$this->db->where($this->dx('type').'IN(1,2,3,4,5)',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $payable = $this->db->get('loan_invoices')->row()->amount_payable?:0;
        
        $this->db->select('sum('.$this->dx('amount').') as amount_paid');
        $this->db->where($this->dx('loan_id').' IN('.$loan_list_ids.')',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $paid =  $this->db->get('loan_repayments')->row()->amount_paid?:0;

        return($payable-$paid);
    }

    function get_total_member_loan_amount($group_id=0,$member_id=0){
        $this->db->select(array(
            "SUM(".$this->dx('loan_amount').") as total_amount"
        ));
      
        if($member_id){
            $this->db->where($this->dx('loans.member_id').'="'.$member_id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').' ="1"',NULL,FALSE);
        $loan = $this->db->get('loans')->row();
        if($loan){
            return $loan->total_amount;
        }else{
            return 0;
        }
    }

    function get_total_member_loan_amount_paid($group_id=0,$member_id=0){
        $this->db->select(array(
            "SUM(".$this->dx('amount').") as total_amount"
        ));
      
        if($member_id){
            $this->db->where($this->dx('loan_repayments.member_id').'="'.$member_id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').' ="1"',NULL,FALSE);
        $loan = $this->db->get('loan_repayments')->row();
        if($loan){
            return $loan->total_amount;
        }else{
            return 0;
        }
    }

     function get_total_loan_lump_sum_as_date($group_id=0,$member_id=0,$date=''){
        if($date){
        }else{
            $date = time();
        }
        $this->db->select(array(
            'id',
            $this->dxa('interest_type'),
            $this->dxa('enable_reducing_balance_installment_recalculation')
        ));
      
        if($member_id){
            $this->db->where($this->dx('loans.member_id').'="'.$member_id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').' ="1"',NULL,FALSE);
        $loans = $this->db->get('loans')->result();

        $loan_list_recalculate_ids = 0;
        $loan_list_ids = 0;
        if($loans){
            foreach ($loans as $loan) {
                if($loan->interest_type==2 && $loan->enable_reducing_balance_installment_recalculation){
                    if($loan_list_recalculate_ids){
                        $loan_list_recalculate_ids.=','.$loan->id;
                    }else{
                        $loan_list_recalculate_ids=$loan->id;
                    }
                }else{
                    if($loan_list_ids){
                        $loan_list_ids.=','.$loan->id;
                    }else{
                        $loan_list_ids=$loan->id;
                    }
                }
            }
        }
        $recalculated_balance = 0;
        if($loan_list_recalculate_ids){
            $this->db->select('sum('.$this->dx('amount_payable').') - sum('.$this->dx('amount_paid').') as balance ');
            $this->db->where($this->dx('loan_id').' IN('.$loan_list_recalculate_ids.')',NULL,FALSE);
            $this->db->where($this->dx('active').'="1"',NULL,FALsE);
            $this->db->where($this->dx('due_date').'<"'.$date.'"',NULL,FALSE);
            $balance = $this->db->get("loan_invoices")->row()->balance;
            $this->db->select('sum('.$this->dx('principle_amount_payable').') - sum('.$this->dx('amount_paid').') as balance ');
            $this->db->where($this->dx('loan_id').' IN('.$loan_list_recalculate_ids.')',NULL,FALSE);
            $this->db->where($this->dx('active').'="1"',NULL,FALsE);
            $this->db->where($this->dx('due_date').'>="'.$date.'"',NULL,FALSE);
            $principle = $this->db->get("loan_invoices")->row()->balance;
            $recalculated_balance =  ($balance+$principle)?:0;
        }
        if($loan_list_ids){
            $this->db->select('sum('.$this->dx('amount_payable').') as amount_payable');
            $this->db->where($this->dx('loan_id').' IN('.$loan_list_ids.')',NULL,FALSE);
            $this->db->where($this->dx('active').'="1"',NULL,FALSE);
            $payable = $this->db->get('loan_invoices')->row()->amount_payable?:0;
            $this->db->select('sum('.$this->dx('amount').') as amount_paid');
            $this->db->where($this->dx('loan_id').' IN('.$loan_list_ids.')',NULL,FALSE);
            $this->db->where($this->dx('active').'="1"',NULL,FALSE);
            $paid=  $this->db->get('loan_repayments')->row()->amount_paid?:0;
            $recalculated_balance+=($payable-$paid);
        }
        return $recalculated_balance;
    }


    function count_loan_applications_by_loan_type($id=0,$group_id=0){
        $this->db->where($this->dx('loan_applications.loan_type_id'). ' = "'.$id.'"',NULL,FALSE);
      
        $counts = $this->db->count_all_results('loan_applications');

        $this->db->where($this->dx('loans.loan_type_id'). ' = "'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx('loans.active'). ' = "1"',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('loans.group_id'). ' = "'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loans.group_id'). ' = "'.$this->group->id.'"',NULL,FALSE);
        }
        $counts+= $this->db->count_all_results('loans');

    }

    function get_member_loans_distribution($group_id = 0){
        $this->db->select(array(
            'SUM('.$this->dx('loan_amount').') as total_amount',
            $this->dx('member_id').' as member_id',
        ));
        $this->db->where($this->dx('loans.active'). ' = "1"',NULL,FALSE);
       
        $this->db->group_by(array(
            'member_id',
        ));
        $this->db->order_by('total_amount','DESC');
        $results = $this->db->get('loans')->result();
        $arr = array();
        if($results){
            foreach ($results as $result) {
                $arr[$result->member_id] = $result->total_amount;
            }
        }
        return $arr;
    }

    function get_group_loan_types_distribution($group_id = 0){
        $this->db->select(array(
            'SUM('.$this->dx('loan_amount').') as total_amount',
            $this->dx('loan_type_id').' as loan_type_id',
        ));
        $this->db->where($this->dx('loans.active'). ' = "1"',NULL,FALSE);
       
        $this->db->group_by(array(
            'loan_type_id',
        ));
        $this->db->order_by('total_amount','DESC');
        $results = $this->db->get('loans')->result();
        $arr = array();
        if($results){
            foreach ($results as $result) {
                $arr[$result->loan_type_id] = $result->total_amount;
            }
        }
        return $arr;
    }

    function count_average_loan_disbursements_per_month($group_id = 0,$from = '',$to = '',$loan_type_id = 0){
        $this->db->select(array(
          'COUNT('.$this->dx('loan_amount').') as loan_disbursements',
          "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('disbursement_date')." ),'%c') as month "
        ));
        $this->db->where($this->dx('active'). ' = "1"',NULL,FALSE);
       

        if($from){
          $this->db->where($this->dx('disbursement_date'). ' >= "'.$from.'"',NULL,FALSE);
        }

        if($to){
          $this->db->where($this->dx('disbursement_date'). ' <= "'.$to.'"',NULL,FALSE);
        }

        $this->db->group_by(
          array(
            'month',
          )
        );
        $result = $this->db->get('loans')->result();
        $count = $result?array_sum(array_column($result, 'loan_disbursements'))/count($result):0;
        return $count;
    }

    function get_average_loan_disbursement_amounts_per_month($group_id = 0,$from = '',$to = '',$loan_type_id = 0){
        $this->db->select(array(
          'SUM('.$this->dx('loan_amount').') as loan_amount',
          "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('disbursement_date')." ),'%c') as month "
        ));
        $this->db->where($this->dx('active'). ' = "1"',NULL,FALSE);
      

        if($from){
          $this->db->where($this->dx('disbursement_date'). ' >= "'.$from.'"',NULL,FALSE);
        }

        if($to){
          $this->db->where($this->dx('disbursement_date'). ' <= "'.$to.'"',NULL,FALSE);
        }

        $this->db->group_by(
          array(
            'month',
          )
        );
        $result = $this->db->get('loans')->result();
        $average = $result?array_sum(array_column($result, 'loan_amount'))/count($result):0;
        return $average;
      }

     function get_group_total_principal_loans_out_per_month_array($group_id = 0){
        $arr = array();

        $this->db->select(
            array(
                'SUM('.$this->dx('loan_amount').') as amount ',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('disbursement_date')." ),'%Y') as year ",
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('disbursement_date')." ),'%b') as month ",
            )
        );
      
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->order_by($this->dx('disbursement_date'),'ASC',FALSE);
        $this->db->group_by(
            array(
                'year',
                'month',
            )
        );
        $loans = $this->db->get('loans')->result();

        $first_month = date('M Y');
        foreach($loans as $loan):
            $first_month = $loan->month.' '.$loan->year;
            break;
        endforeach;

        $current_month = date('M Y');
        $months_array = generate_months_array(strtotime($first_month),strtotime($current_month));

        foreach($months_array as $month):
            $arr[$month] = 0;
        endforeach;

        foreach($loans as $loan):
            if(isset($arr[$loan->month.' '.$loan->year])){
                $arr[$loan->month.' '.$loan->year] += $loan->amount;
            }else{
                $arr[$loan->month.' '.$loan->year] = $loan->amount;
            }
        endforeach;

        foreach($months_array as $month):
            if($month == $first_month){

            }else{
                $previous_month = date('M Y',strtotime('-1 month',strtotime($month)));
                if(isset($arr[($previous_month)])){
                    $arr[$month] += $arr[($previous_month)];
                }
            }
        endforeach;

        $total_loan_principal_paid_per_month_array = $this->reports_m->get_group_total_loan_principal_paid_per_month_array($group_id);


        foreach($total_loan_principal_paid_per_month_array as $month => $principal_paid):
            foreach($months_array as $m):
                $arr[$m] -= $principal_paid;
            endforeach;
        endforeach;
        
        return $arr;
    }

    function get_transfer_statement_and_other_fines($loan_id=0){
        $this->select_all_secure('loan_statements');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('transaction_type').'="6"',NULL,FALSE);
        return $this->db->get('loan_statements')->result();
    }

    function get_table_fields(){
        return $this->db->list_fields('loans');
    }

    function get_summation_for_ivoices_by_loan_ids($loan_ids=''){
        $this->db->select(array(
            'sum('.$this->dx('interest_amount_payable').') as total_interest_payable',
            'sum('.$this->dx('principle_amount_payable').') as total_principle_payable',
            'sum('.$this->dx('amount_payable').') as total_amount_payable',
            'sum('.$this->dx('amount_paid').') as total_amount_paid',
        ));
        $this->db->where($this->dx('loan_invoices.loan_id').' IN ('.$loan_ids.')',NULL,FALSE);
        $this->db->where($this->dx("loan_invoices.group_id").'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('loan_invoices.active').'="1"',NULL,FALSE);
        // $this->db->group_by(array(
        //     $this->dx('loan_id'),
        // ));
        $results = $this->db->get('loan_invoices')->row();
        return $results;
    }

    function get_projected_interest_by_loan_ids($loan_ids='',$amount_paids='',$group_id=0){
        $this->db->select(array(
            'id',
            $this->dx('type').' as type',
            $this->dx('amount_payable').' as amount_payable',
            $this->dx('loan_id').' as loan_id',
            $this->dx('principle_amount_payable').' as principle_amount_payable',
            $this->dx('interest_amount_payable').' as interest_amount_payable',  
        ));
        $this->db->where($this->dx('loan_invoices.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('loan_invoices.loan_id').' IN('.$loan_ids.')',NULL,FALSE);
      
        $this->db->order_by($this->dx('loan_invoices.invoice_date'),'ASC',FALSE);
        $results = $this->db->get('loan_invoices')->result();
        $loan_invoices = array();
        foreach($results as $result){
           $loan_invoices[$result->loan_id][] = array(
                'type' => $result->type,
                'amount_payable' => $result->amount_payable,
                'principle_amount_payable' => $result->principle_amount_payable,
                'interest_amount_payable' => $result->interest_amount_payable,
           );
        }
        $projected_profits = [];

        if($loan_invoices){ 
            foreach($loan_invoices as $key=>$loan_loan_invoices){
                $projected_profits[$key] = 0;
                $amount_paid = isset($amount_paids[$key])?$amount_paids[$key]:0;
                if($amount_paid){
                    foreach($loan_loan_invoices as $loan_invoice){
                        $loan_invoice = (object)$loan_invoice;
                        if($loan_invoice->type==1){
                            if($loan_invoice->amount_payable<=$amount_paid){
                                $projected_profits[$key]+=$loan_invoice->interest_amount_payable;
                            }else{
                                if($loan_invoice->principle_amount_payable<=$amount_paid){
                                    $projected_profits[$key] +=$amount_paid - $loan_invoice->principle_amount_payable;
                                }else{
                                    //do nothing
                                }
                            }
                        }else if($loan_invoice->type==2||$loan_invoice->type==3){
                            if($loan_invoice->amount_payable<=$amount_paid){
                                $projected_profits[$key]+=$loan_invoice->amount_payable;
                            }else{
                                $projected_profits[$key]+=$amount_paid;
                            }
                        }
                        $amount_paid-=$loan_invoice->amount_payable;
                    }
                }
            }
        }

        return $projected_profits;
    }
}

