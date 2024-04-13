<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Archive_m extends MY_Model{
    
    /**
     * The constructor
     * @access public
     * @return void
    */
    // protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('members/members_m');
        $this ->load->dbforge();
        $this->db = $this->load->database('archive', TRUE);
       // $this->install();
    }
 
    public function install(){
        $this->db->query("
            create table if not exists voided_statements(
                id int not null auto_increment primary key,
                `transaction_type` blob,
                `transaction_date` blob,
                `contribution_id` blob,
                `refund_id` blob,
                `fine_id` blob,
                `user_id` blob,
                `group_id` blob,
                `application_profile_id` blob,
                `invoice_id` blob,
                `payment_id` blob,
                `amount` blob,
                `contribution_balance` blob,
                `contribution_invoice_due_date` blob,
                `is_a_back_dating_record` blob,
                `balance` blob,
                `active` blob,
                `amount_payable` blob,
                `amount_paid` blob,
                `cumulative_balance` blob,
                `void_id` blob,
                `fine_invoice_due_date` blob,
                `deposit_id` blob,
                `last_invoice_date` blob,
                `income_category_id` blob,
                `loan_id` blob,
                `withdrawal_id` blob,
                `fine_category_balance` blob,
                `contribution_fine_balance` blob,
                `member_from_id` blob,
                `transfer_from` blob,
                `contribution_from_id` blob,
                `transfer_to` blob,
                `member_to_id` blob,
                `loan_to_id` blob,
                `loan_from_id` blob,
                `fine_category_to_id` blob,
                `transfer_date` blob,
                `member_transfer_to` blob,
                `contribution_transfer_id` blob,
                `description` blob,
                `transaction_alert_id` blob,
                `account_id` blob,              
                `created_by` blob,
                `created_on` blob,
                `modified_on` blob,
                `void_created_by` blob,
                `void_created_on` blob,
                `parent_statement_id` blob,
                `member_id` blob,
                `fine_category_id` blob,
                `contribution_to_id` blob,
                `loan_transfer_invoice_id` blob,
                `contribution_paid` blob,
                `cumulative_paid` blob,
                `contribution_minus_contribution_transfers_balance` blob,
                `cumulative_minus_contribution_transfers_balance` blob,
                `fine_balance` blob,
                `fine_paid` blob,
                `old_statement_id` blob,
                `contribution_fine_paid` blob,
                `modified_by` blob
            )"
        );

        $this->db->query("
            create table if not exists activity_log(
                id int not null auto_increment primary key,
                `user_id` blob,
                `group_id` blob,
                `member_id` blob,
                `url` blob,
                action blob,
                description blob,
                `ip_address` blob,
                `request_method` blob,
                created_by blob,
                created_on blob,
                modified_on blob,
                modified_by blob
            )"
        );
    }

    function batch_insert_voided_statements($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_chunked_batch_secure_data('voided_statements',$input);
    }

    function batch_insert_activity_logs_archive($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_chunked_batch_secure_data('activity_log',$input);
    }

    function get_voided_statements_parent_ids_options_array($voided_statements_ids=array()){
        $this->select_all_secure('voided_statements');
        if(empty($voided_statements_ids)){
            $parent_id = 0;
        }else{
            $parent_id = implode(',', $voided_statements_ids);
        }
        $arr = array();
        $this->db->where($this->dx('parent_statement_id')." = '".$parent_id."' ",NULL,FALSE);
        $this->db->order_by('id','DESC',FALSE);
        $results = $this->db->get('voided_statements')->result();
        foreach($results as $statement):
            $arr[$statement->parent_statement_id] = $statement->id;
        endforeach;
        return $arr;
    }

    function get_group_archived_activity_logs($group_id = 0,$date = 0){
        if($date){

        }else{
            $date = time();
        }
        $this->select_all_secure('activity_log');
        $this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')."),'%m%d%Y') = '" . date('mdY',$date) . "'", NULL, FALSE);
        // $this->db->where($this->dx('')." = '".$date."' ",NULL,FALSE);
        return $this->db->get('activity_log')->result();
    }

}