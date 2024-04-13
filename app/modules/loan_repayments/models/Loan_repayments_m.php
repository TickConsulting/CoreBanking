<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Loan_repayments_m extends MY_Model{

    protected $_table = 'loan_repayments';

    function __construct(){
        parent::__construct();
        //$this->install();
    }

    /***
    status 1
        voided ''
    active 1
        voided ''

    ***/


    function install(){
        $this->db->query('
            create table if not exists loan_repayments(
                id int not null auto_increment primary key,
                `loan_id` blob,
                `group_id` blob,
                `member_id` blob,
                `account_id` blob,
                `account_type` blob,
                `receipt_date` blob,
                `payment_method` blob,
                `amount` blob,
                `status` blob,
                `active` blob,
                created_by blob,
                created_on blob,
                modified_on blob,
                modified_by blob
        )');
    }

    function insert($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('loan_repayments',$input);
    }


    function insert_loan_repayments_batch($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_chunked_batch_secure_data('loan_repayments',$input);
    }

    function get($id){
        $this->select_all_secure('loan_repayments');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where('id',$id);
        return $this->db->get('loan_repayments')->row();
    }

    function get_group_active_loan_repayments($group_id = 0){

        $this->select_all_secure('loan_repayments');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
        }
        return $this->db->get('loan_repayments')->result();
    }


    function update($id,$input,$val=FALSE){
        return $this->update_secure_data($id,'loan_repayments',$input);
    }

    function update_where($where = "",$input = array()){
        return $this->update_secure_where($where,'loan_repayments',$input);
    }

    function void_group_member_loan_repayments($loan_repayment_ids = array()){
        $input = array(
            'active' => 0,
            'modified_on' => time()
        );
        if(empty($loan_repayment_ids)){
            $loan_repayment_id_list = "0";
        }else{
            $loan_repayment_id_list = implode(",",$loan_repayment_ids);
        }
        $where = " ".'id'." IN (".$loan_repayment_id_list.") ";
        return $this->update_secure_where($where,'loan_repayments',$input);
    }

    function get_loan_repayments($load_id=0,$incoming_loan_transfer_invoice_id=0,$incoming_contribution_transfer_id=0)
    {
        $this->select_all_secure('loan_repayments');
        $this->db->where($this->dx('loan_id').'="'.$load_id.'"',NULL,FALSE);
        if($incoming_loan_transfer_invoice_id){
            $this->db->where($this->dx('incoming_loan_transfer_invoice_id').' IN ('.$incoming_loan_transfer_invoice_id.')',NULL,FALSE);
        }
        if($incoming_contribution_transfer_id){
            $this->db->where($this->dx('incoming_contribution_transfer_id').' IN ('.$incoming_contribution_transfer_id.')',NULL,FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('loan_repayments')->result();
    } 

    function get_group_member_loan_repayments_array($incoming_contribution_transfer_ids = array()){
        $this->select_all_secure('loan_repayments');
        if(empty($incoming_contribution_transfer_ids)){
            $this->db->where($this->dx('incoming_contribution_transfer_id').' IN ( 0 ) ',NULL,FALSE);
        }else{
            $this->db->where($this->dx('incoming_contribution_transfer_id').' IN ( '.implode(",",$incoming_contribution_transfer_ids)." ) ",NULL,FALSE);
        }
        $this->db->where($this->dx('active').' = "1"',NULL,FALSE);  
        $this->db->order_by('id','ASC',FALSE);
        return $this->db->get('loan_repayments')->result();
    }

    function get_new_loan_repayments_array($loan_repayment_ids = array()){
        $this->select_all_secure('loan_repayments');
        if(empty($loan_repayment_ids)){
            $this->db->where($this->dx('old_loan_repayment_id').' IN ( 0 ) ',NULL,FALSE);
        }else{
            $this->db->where($this->dx('old_loan_repayment_id').' IN ( '.implode(",",$loan_repayment_ids)." ) ",NULL,FALSE);
        }
        $this->db->where($this->dx('active').' = "1"',NULL,FALSE);  
        $this->db->order_by('id','ASC',FALSE);
        return $this->db->get('loan_repayments')->result();
    }

    function get_total_payment($loan_id=0)
    {
        $this->db->select($this->dx('amount').'as amount');
        $this->db->where($this->dx("loan_id").'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx("active").'="1"',NULL,FALSE);
        $query = $this->db->get('loan_repayments');
        $query2 = $query->row();
        $query->free_result();
        return $query2;
    }

    function get_loan_payment_by_loan_id($loan_id=0)
    {
        $this->select_all_secure('loan_repayments');
        $this->db->where($this->dx("loan_id").'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx("active").'="1"',NULL,FALSE);
        return $this->db->get('loan_repayments')->result();
    }

    function get_payment_by_date($loan_id=0,$receipt_date=0)
    {
        if($receipt_date)
        {
            $this->db->select('sum('.$this->dx('amount').') as amount');
            $this->db->where($this->dx("loan_id").'="'.$loan_id.'"',NULL,FALSE);
            $this->db->where($this->dx("active").'="1"',NULL,FALSE);
            $this->db->where($this->dx("receipt_date").'<="'.$receipt_date.'"',NULL,FALSE);
            $amount = $this->db->get('loan_repayments')->row();

            if($amount)
            {
                return $amount->amount;
            }else
            {
                return 0; 
            }
        }
        else
        {
            return 0; 
        }
    }

    function get_loan_total_payments($loan_id=0){
        $this->db->select('sum('.$this->dx('amount').') as amount_paid');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('loan_repayments')->row()->amount_paid?:0;
    }

    function get_loan_total_payments_by_loan_ids($loan_ids=''){
        $this->db->select(array('sum('.$this->dx('amount').') as amount_paid',$this->dx('loan_id').' as loan_id'));
        $this->db->where($this->dx('loan_id').'IN('.$loan_ids.')',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->group_by(array(
            $this->dx('loan_id'),
        ));
        $results = $this->db->get('loan_repayments')->result();
        $arr = array();
        foreach($results as $result){
            $arr[$result->loan_id] = $result->amount_paid?:0;
        }
        return $arr;
    }

    function get_loan_repayment_details($loan_id=0){
        $this->select_all_secure('loan_repayments');
        //$this->db->where($this->dx('loan_repayments.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
       // $this->db->where($this->dx('loan_repayments.group_id').'="'.$this->group->id.'"');
        return $this->db->get('loan_repayments')->result();
    }

    function get_all_group_loan_repayments(){
        $this->select_all_secure('loan_repayments');
        $this->db->select(array(
                $this->dx('loans.loan_amount').'as loan_amount',
            ));
        $this->db->where($this->dx('loan_repayments.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('loan_repayments.group_id').'="'.$this->group->id.'"');
        $this->db->join('loans',$this->dx('loan_repayments.loan_id').'=loans.id');
        return $this->db->get('loan_repayments')->result();
    }

    function count_all_group_loan_repayments(){
        $this->db->where($this->dx('loan_repayments.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('loan_repayments.group_id').'="'.$this->group->id.'"');
        return $this->db->count_all_results('loan_repayments')?:0;
    }


    function delete_loan_payments($loan_id=0){
        $this->select_all_secure('loan_repayments');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $result = $this->db->get('loan_repayments')->result();

        if($result)
        {
            foreach ($result as $res) 
            {
                $this->db->where('id',$res->id);
                $this->db->delete('loan_repayments');
            }
            return TRUE;
        }
        else
        {
            return TRUE;
        }
    }


    function last_loan_repayment_date($loan_id=0){
        $this->select_all_secure('loan_repayments');
        $this->db->select(array($this->dx('receipt_date').' as repayment_date'));
        $this->db->where($this->dx('active').'= "1"',NULL,FALSE);
        $this->db->where($this->dx('loan_id').'= "'.$loan_id.'"',NULL,FALSE);
        $this->db->order_by($this->dx('receipt_date'),'DESC',FALSE);
        $this->db->limit(1);
        $date = $this->db->get('loan_repayments')->row();
        if($date){
            return $date->repayment_date;
        }else{
            return FALSE;
        }
    }

    function get_total_loan_paid($group_id=0){
        $this->db->select('sum('.$this->dx('loan_repayments.amount').') as amount');
        $this->db->where($this->dx("loan_repayments.active").'="1"',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $amount = $this->db->get('loan_repayments')->row();
        if($amount){
            return $amount->amount?:0;
        }else{
            return 0;
        }
    }

    function update_group_back_dating_loan_repayments_cut_off_date($group_id = 0,$input = array()){
        $where = " ".$this->dx('is_a_back_dating_record')." = '1' AND ".$this->dx('group_id')." = '".$group_id."' AND ".$this->dx('active')." = '1' ;";
        if($this->update_where($where,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function update_all_repayments_loan_id_for_modified_loan($old_loan_id = '',$new_loan_id = ''){
        $this->select_all_secure('loan_repayments');
        $this->db->where($this->dx('loan_id').' = '.$old_loan_id);
        $results = $this->db->get('loan_repayments')->result();
        if($results){
            $input = array(
                'loan_id' => $new_loan_id,
                'modified_by' => $this->user->id,
                'modified_on' => time(),
            );
            foreach ($results as $result) {
                $this->update($result->id,$input);
            }
            return TRUE;
        }else{
            return TRUE;
        }
        
    }

    function get_all_loan_repayments(){
        $this->db->select('id');
        $this->db->where('('.$this->dx('active').' = "" OR '.$this->dx('active').' IS NULL OR '.$this->dx('active').' ="" OR '.$this->dx('active').' ="0" )',NULL,FALSE);
        return $this->db->get('loan_repayments')->result();
    }

    function get_group_contribution_transfers_to_loan_repayments($group_id = 0){
        $this->select_all_secure('loan_statements');
        $this->db->where($this->dx("loan_statements.active").'="1"',NULL,FALSE);
        $this->db->where($this->dx("loan_statements.transaction_type").'="4"',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        return $this->db->get('loan_statements')->result();
    }
   

}