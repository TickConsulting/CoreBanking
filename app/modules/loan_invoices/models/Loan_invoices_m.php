<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Loan_invoices_m extends MY_Model{

    protected $_table = 'loan_invoices';

    function __construct(){
        parent::__construct();
        ////$this->install();
    }

    /***
        Status
        1. unpaid loan invoice
        2. invoice paid

        disable penalties
        1. true 
        else not disabled

        Loan Type
        1 . Invoice
        2. Fine Invoice
        3. outstanding loan balances fines
        4. Loan Payment
    **/


    function install(){
        $this->db->query('
            create table if not exists loan_invoices(
                id int not null auto_increment primary key,
                `loan_id` blob,
                `group_id` blob,
                `member_id` blob,
                `invoice_no` blob,
                `type` blob,
                `interest_amount_payable` blob,
                `principle_amount_payable` blob,
                `invoice_date` blob,
                `due_date` blob,
                `fine_date` blob,
                `amount_payable` blob,
                `fine_parent_loan_invoice_id` blob,
                `amount_paid` blob,
                `is_sent` blob,
                `disable_fines` blob,
                `status` blob,
                `active` blob,
                `created_by` blob,
                `created_on` blob,
                `modified_on` blob,
                `modified_by` blob
        )');

        $this->db->query("
        create table if not exists loan_invoicing_queue(
            id int not null auto_increment primary key,
            `loan_id` blob,
            `invoice_id` blob,
            `invoice_no` blob,
            `member_id` blob,
            `group_id` blob,
            `invoice_date` blob,
            `invoice_type` blob,
            `due_date` blob,
            `amount_payable` blob,
            `loan_balance` blob,
            `lump_sum_remaining` blob,
            `description` blob,
            created_on blob
        )");

        $this->db->query("
        create table if not exists loan_invoices_sent(
            id int not null auto_increment primary key,
            `loan_id` blob,
            `invoice_id` blob,
            `invoice_no` blob,
            `invoice_type` blob,
            `member_id` blob,
            `group_id` blob,
            `invoice_date` blob,
            `due_date` blob,
            `amount_payable` blob,
            `loan_balance` blob,
            `lump_sum_remaining` blob,
            `description` blob,
            `notification_created` blob,
            `email_sent` blob,
            `email_body` blob,
            `sms_sent` blob,
            `sms_message` blob,
            created_on blob
        )");


    }

    function insert($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('loan_invoices',$input);
    }

    function insert_batch($input,$skip_validation=FALSE){
        return $this->insert_batch_secure_data('loan_invoices',$input);
    }


    function update($id,$input,$val=FALSE){
        return $this->update_secure_data($id,'loan_invoices',$input);
    }

    function update_batch($input,$SKIP_VALIDATION = FALSE){
        return $this->batch_update_secure_data('loan_invoices',$input);
    }

    function update_where($where = "",$input = array()){
        return $this->update_secure_where($where,'loan_invoices',$input);
    }

    function calculate_invoice_no($group_id=0)
    {
        $this->db->where($this->dx('loan_invoices.group_id').'="'.$group_id.'"',NULL,FALSE);
        $this->db->from('loan_invoices');
        $count = $this->db->count_all_results();
        return $count + 1;
    }

    function get($id)
    {
        $this->select_all_secure('loan_invoices');
        $this->db->where('id',$id);
        return $this->db->get('loan_invoices')->row();
    }

    function get_active($id){
        $this->select_all_secure('loan_invoices');
        $this->db->where('id',$id);
        $this->db->where($this->dx('active').' = "1"',NULL,FALSE);
        return $this->db->get('loan_invoices')->row();
    }

    function delete_all_invoices($loan_id=0){
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $result = $this->db->get('loan_invoices')->result();

        if($result){
            foreach ($result as $res) {
                $this->db->where('id',$res->id);
                $this->db->delete('loan_invoices');
            }
            return TRUE;
        }else{
            return TRUE;
        }

    }

    function void_all_invoices($loan_id=0)
    {
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $result = $this->db->get('loan_invoices')->result();

        if($result)
        {
            foreach ($result as $res) 
            {
                $this->update($res->id,array('active'=>NULL,'status'=>NULL));
            }
            return TRUE;
        }
        else
        {
            return TRUE;
        }

    }

    function get_past_unsent_invoices($id=0,$group_id=0){
        $this->select_all_secure('loan_invoices');
        //$this->select_all_secure('loans');
        $this->db->select(array(
                    "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loan_invoices.invoice_date')." ),'%Y %D %M') as invoice_date2 ",
                ));
        $this->db->where($this->dx('loan_invoices.loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where('('.$this->dx('loan_invoices.is_sent').'="0" OR '.$this->dx('loan_invoices.is_sent').'IS NULL)',NULL,FALSE);
        $this->db->where($this->dx('loans.active').'="1"',NULL,FALSE);
        if($group_id){
        }else{
            $this->db->where($this->dx('loans.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where('('.$this->dx('loans.is_fully_paid').'="0" OR '.$this->dx('loans.is_fully_paid').' is NULL OR '.$this->dx('loans.is_fully_paid').' = "")',NULL,FALSE);
        $this->db->where($this->dx('loan_invoices.invoice_date').'<',time());
        $this->db->join('loans', 'loans.id = '.$this->dx('loan_invoices.loan_id'));
        return $this->db->get('loan_invoices')->result();
    }

    function get_all_past_invoices($id=0,$group_id=0){
        $this->select_all_secure('loan_invoices');
        $this->db->select(array(
                    "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loan_invoices.invoice_date')." ),'%Y %D %M') as invoice_date2 ",
                ));
        $this->db->where($this->dx('loan_invoices.loan_id').'="'.$id.'"',NULL,FALSE);
        if($group_id){
        }else{
            $this->db->where($this->dx('loans.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where('('.$this->dx('loans.is_fully_paid').'="0" OR '.$this->dx('loans.is_fully_paid').' is NULL OR '.$this->dx('loans.is_fully_paid').' = "")',NULL,FALSE);
        $this->db->where($this->dx('loan_invoices.invoice_date').'<',time());
        $this->db->join('loans', 'loans.id = '.$this->dx('loan_invoices.loan_id'));
        return $this->db->get('loan_invoices')->result();
    }

    function get_loan_installments($id=0){
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_invoices.loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx('loan_invoices.active').'="1"',NULL,FALSE);
        $this->db->order_by($this->dx('invoice_date').'+0','ASC',FALSE);
        $this->db->order_by($this->dx('due_date').'+0','ASC',FALSE);
        $this->db->order_by($this->dx('type').'+0','ASC',FALSE);
        return $this->db->get('loan_invoices')->result();
    }

    function get_loan_installmets_invoice_array($id=0){
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_invoices.loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx('loan_invoices.active').'="1"',NULL,FALSE);
        $this->db->order_by($this->dx('invoice_date').'+0','ASC',FALSE);
        $this->db->order_by($this->dx('due_date').'+0','ASC',FALSE);
        $this->db->order_by($this->dx('type').'+0','ASC',FALSE);
        $results = $this->db->get('loan_invoices')->result();
        $arr = array();
        foreach ($results as $key => $result):
            $arr[$result->id] = $result;
        endforeach;
        return $results;
    }

    function get_unpaid_loan_installments($id=0){
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_invoices.loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx('loan_invoices.active').'="1"',NULL,FALSE);
        $this->db->where('('.$this->dx('status').'="1" OR '.$this->dx('status').'="" OR '.$this->dx('status').'="0" OR '.$this->dx('status').'=" " OR '.$this->dx('status').'is NULL )',NULL,FALSE);
        $this->db->order_by($this->dx('invoice_date').'+0','ASC',FALSE);
        $this->db->order_by($this->dx('due_date').'+0','ASC',FALSE);
        $this->db->order_by($this->dx('type').'+0','ASC',FALSE);
        return $this->db->get('loan_invoices')->result();
    }

    function get_last_invoice($id=0){
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx("active").'="1"',NULL,FALSE);
        $this->db->where($this->dx("type").'="1"',NULL,FALSE);
        $this->db->order_by($this->dx('invoice_date'), 'DESC',FALSE);
        $this->db->limit(1);
        $query = $this->db->get('loan_invoices');
        $return = $query->row();
        $query->free_result();
        return $return;
    }

    function get_newest_invoice($id=0){
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx("active").'="1"',NULL,FALSE);
        $this->db->where($this->dx("type").'="1"',NULL,FALSE);
        $this->db->where('('.$this->dx("status").'="1" OR '.$this->dx("status").' IS NULL OR '.$this->dx("status").'="0" OR '.$this->dx("status").'="")',NULL,FALSE);
        $this->db->order_by($this->dx('invoice_date'), 'ASC',FALSE);
        $this->db->limit(1);
        $query = $this->db->get('loan_invoices');
        $return = $query->row();
        $query->free_result();
        return $return;
    }

    function get_newest_invoices($id=0,$date=0, $date_format_new = FALSE,$group_id=0){
        $this->select_all_secure('loan_invoices');
        // $this->db->select(array(
        //             "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loan_invoices.invoice_date')." ),'%Y %D %M') as invoice_date2 ",
        //         ));
        $this->db->where($this->dx('loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx("active").'="1"',NULL,FALSE);
        $this->db->where($this->dx("type").'="1"',NULL,FALSE);
        $this->db->where('('.$this->dx("status").'="1" OR '.$this->dx("status").' IS NULL OR '.$this->dx("status").'="0" OR '.$this->dx("status").'="")',NULL,FALSE);
        if($date){
            if($date_format_new){
                $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('due_date')."),'%Y%m') > '" . date('Ym',$date) . "'", NULL, FALSE);
            }else{
                $this->db->where($this->dx('due_date').'>="'.$date.'"',NULL,FALSE);
            }
        }
        if($group_id){
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->order_by($this->dx('invoice_date'), 'ASC',FALSE);
        return $query = $this->db->get('loan_invoices')->result();
    }

    function get_paid_existing_invoices($id=0,$date=0,$date_format_new = FALSE){
        $this->select_all_secure('loan_invoices');
        $this->db->select(array(
            "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loan_invoices.due_date')." ),'%Y %D %M') as due_date2 ",
        ));
        $this->db->where($this->dx('loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx("active").'="1"',NULL,FALSE);
        $this->db->where($this->dx("type").'="1"',NULL,FALSE);
        $this->db->where('('.$this->dx("status").'="2"  OR '.$this->dx("amount_paid").'>="1" )',NULL,FALSE);
        if($date_format_new){
            $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('due_date')."),'%Y%m') > '" . date('Ym',$date) . "'", NULL, FALSE);
        }else{
            $this->db->where($this->dx('due_date').'>="'.$date.'"',NULL,FALSE);
        }
        $this->db->order_by($this->dx('invoice_date'), 'ASC',FALSE);
        return $query = $this->db->get('loan_invoices')->result();
    }


    function fine_invoice_list($id=0,$date=0)
    {   
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx("active").'="1"',NULL,FALSE);
        $this->db->where($this->dx("type").'="1"',NULL,FALSE);
        $this->db->where($this->dx('due_date').' < "'.$date.'"',NULL,FALSE);
        $this->db->where('('.$this->dx('loan_invoices.disable_fines').'=" " OR '.$this->dx('loan_invoices.disable_fines').'is NULL OR '.$this->dx('loan_invoices.disable_fines').' = "")');
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('fine_date')."),'%Y %d %m') = '" . date('Y d m',$date) . "'", NULL, FALSE);
        return $this->db->get('loan_invoices')->result();
    }

    function get_all_fines_for_list($id=0){
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx("active").'="1"',NULL,FALSE);
        $this->db->where($this->dx("type").'="1"',NULL,FALSE);
        //$this->db->where($this->dx('due_date').' < "'.$date.'"',NULL,FALSE);
        $this->db->where('('.$this->dx('loan_invoices.disable_fines').'=" " OR '.$this->dx('loan_invoices.disable_fines').'is NULL OR '.$this->dx('loan_invoices.disable_fines').' = "")');
        //$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('fine_date')."),'%Y %d %m') = '" . date('Y d m',$date) . "'", NULL, FALSE);
        return $this->db->get('loan_invoices')->result();
    }

    function get_today_loan_invoices_to_queue($date=0,$limit=0,$group_id=0)
    {
        if($date){
            //do nothing for now
        }else{
            $date = time();
        }
        if($limit){

        }else{
            $limit=50;
        }
        $is_sent_list = "0,'',' '";
        $this->select_all_secure('loan_invoices');
        $this->db->select(array(
                'loan_invoices.id as id',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loan_invoices.invoice_date')." ),'%Y %D %M') as invoice_date2 ",
            ));
        $this->db->where($this->dx('loan_invoices.active').'="1"',NULL,FALSE);
        if($group_id){
        }else{
            $this->db->where($this->dx('loans.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('loan_invoices.is_sent').' IN('.$is_sent_list.')',NULL,FALSE);
        $this->db->where($this->dx('loan_invoices.type').'="1"',NULL,FALSE);
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loan_invoices.invoice_date')."),'%Y %D %M') = '" . date('Y jS F',$date) . "'", NULL, FALSE);
        $this->db->limit($limit);
        return $this->db->get('loan_invoices')->result();
    }

    function fine_invoice_exists_fixer($id,$date,$loan_id=0){
        return $this->db->where($this->dx('type').'="2"',NULL,FALSE)
                        ->where($this->dx('fine_parent_loan_invoice_id').'="'.$id.'"',NULL,FALSE)
                        ->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE)
                        ->where($this->dx('active').'="1"',NULL,FALSE)
                        ->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('invoice_date')."),'%Y %D %M') = '" . date('Y jS F',$date) . "'", NULL, FALSE)
                        ->count_all_results('loan_invoices') > 0;
    }


    function get_all_fines($loan_id=0){
        $this->select_all_secure('loan_invoices');
        return  $this->db->where($this->dx('type').'="2"',NULL,FALSE)
                        ->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE)
                        ->where($this->dx('active').'="1"',NULL,FALSE)
                        ->get('loan_invoices')->result();
    }

    function fetch_loan($id, $type = 0)
    {
        $this->select_all_secure('loans');
        $query = $this->db->where('id',$id)->get('loans');
        $query2 = $query->row();
        $query->free_result();
        return $query2;
    }

     function get_to_update_fixer($id,$date){
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx('type').'="1"',NULL,FALSE);
        $this->db->where($this->dx('due_date').' < "'.$date.'"',NULL,FALSE);
        return $this->db->get('loan_invoices')->result();
    }

    function get_all_loan_invoices_for_update($id=0){
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx('type').'="1"',NULL,FALSE);
        return $this->db->get('loan_invoices')->result();
    }


    function get_total_unpaid_loan_installments($invoice_id=0,$date=0,$group_id=0){
        if($date){

        }else{
            $date = time();
        }
        $this->select_all_secure('loan_invoices');
        $this->db->where("id", $invoice_id);
        $loan_invoice = $this->db->get("loan_invoices")->row();
        if($loan_invoice){
            $amount_payable = $loan_invoice->amount_payable;
        }else
        {
            $amount_payable=0;
        }
        if($loan_invoice){
            $this->select_all_secure('loans');
            $this->db->where('id',$loan_invoice->loan_id);
            $loan = $this->db->get('loans')->row();
            if($loan){
                if($loan->enable_loan_fine_deferment){
                    $this->db->select('sum('.$this->dx('amount_payable').') as amount_payable');
                    $this->db->where($this->dx('loan_id').'="'.$loan_invoice->loan_id.'"',NULL,FALSE);
                    $this->db->where($this->dx('invoice_date').' <= "'.$loan_invoice->invoice_date.'"',NULL,FALSE);
                    $this->db->where($this->dx('type').'="1"',NULL,FALSE);
                    $this->db->where($this->dx('active').'="1"',NULL,FALSE);
                    $loan_invoices = $this->db->get('loan_invoices')->row()->amount_payable;
                    if($loan_invoices){
                        $total_amount_payable = $loan_invoices->amount_payable;
                    }else{
                        $total_amount_payable = 0;
                    }
                }else{
                    $this->db->select('sum('.$this->dx('amount_payable').') as amount_payable');
                    $this->db->where($this->dx('loan_id').'="'.$loan_invoice->loan_id.'"',NULL,FALSE);
                    $this->db->where($this->dx('invoice_date').' <= "'.$loan_invoice->invoice_date.'"',NULL,FALSE);
                    $this->db->where($this->dx('active').'="1"',NULL,FALSE);
                    $loan_invoices = $this->db->get('loan_invoices')->row();
                    if($loan_invoices){
                        $total_amount_payable = $loan_invoices->amount_payable;
                    }else{
                        $total_amount_payable = 0;
                    }
                }
                
                $this->db->select(array(
                    "SUM(".$this->dx('amount').") as total_paid",
                ));
                $this->db->select($this->dx('amount') .' as amount');
                $this->db->where($this->dx('loan_id').'="'.$loan_invoice->loan_id.'"',NULL,FALSE);
                if($group_id){

                }else{
                    $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
                }
                $this->db->where($this->dx('active').'="1"',NULL,FALSE);
                $this->db->where($this->dx('receipt_date').' <"'.$date.'"',NULL,FALSE);
                $loan_repayment = $this->db->get('loan_repayments')->row();
                if($loan_repayment)
                {
                    $total_amount_paid = $loan_repayment->total_paid;
                }
                else
                {
                    $total_amount_paid = 0;
                }
                $diff = $total_amount_payable - $total_amount_paid;
                
                if($diff>0){
                    if($diff>$amount_payable){
                        return $amount_payable;
                    }else{
                        return $diff;          
                    }
                }else{
                    return 0;
                }
            }
        }else
        {
            return 0;
        }
    }


    function get_outstanding_balance($member_id, $loan_id, $invoice_date = 0,$group_id=0){
        $this->db->select('sum('.$this->dx('amount_payable').')  as amount_payable ');
        $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE);
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if ($invoice_date){
            $this->db->where($this->dx('invoice_date').'<= "'.$invoice_date.'"',NULL,FALSE);
        }
        $amount_payable = $this->db->get("loan_invoices")->row()->amount_payable;

        $this->db->select(array(
            "SUM(".$this->dx('amount').") as total_paid",
        ));
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('receipt_date').' <= "'.$invoice_date.'"',NULL,FALSE);
        if($group_id){

        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $loan_repayment = $this->db->get('loan_repayments')->row();
        if($loan_repayment){
            $total_amount_paid = $loan_repayment->total_paid;
        }else{
            $total_amount_paid = 0;
        }

        return $amount_payable-$total_amount_paid;
    }

    function get_installment_principle_balance($loan_id=0,$group_id=0){
        //$this->db->select('sum('.$this->dx('principle_amount_payable').') as balance ');
        // $this->select_all_secure('loan_invoices');
        // $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        // $this->db->where($this->dx('type').'="1"',NULL,FALSE);
        // $this->db->where($this->dx('active').'="1"',NULL,FALsE);
        // $this->db->where($this->dx('loan_invoices.status').'="2"',NULL,FALsE);
        // $balance =  $this->db->get("loan_invoices")->result();
        // print_r($balance);die;

        $this->db->select('sum('.$this->dx('principle_amount_payable').') as balance ');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('type').'="1"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALsE);
        $this->db->where('('.$this->dx('status').'="1" OR '.$this->dx('status').'="" OR '.$this->dx('status').'="0" OR '.$this->dx('status').'=" " OR '.$this->dx('status').'is NULL )',NULL,FALSE);
        if($group_id){

        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        return   $paid_balance =  $this->db->get("loan_invoices")->row()->balance;
        
    }

    function get_loan_lump_sum_as_date($loan_id=0,$date='',$group_id=0){
        if($date){
        }else{
            $date = time();
        }

        $this->select_all_secure('loans');
        $this->db->where('id',$loan_id);
        if($group_id){
        }else{
            $this->db->where($this->dx('loans.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $loan = $this->db->get('loans')->row();
        if($loan->interest_type==2 && $loan->enable_reducing_balance_installment_recalculation){
            $this->db->select('sum('.$this->dx('amount_payable').') - sum('.$this->dx('amount_paid').') as balance ');
            $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
            $this->db->where($this->dx('active').'="1"',NULL,FALsE);
            $this->db->where($this->dx('due_date').'<"'.$date.'"',NULL,FALSE);
            if($group_id){
            }else{
                $this->db->where($this->dx('loan_invoices.group_id').'="'.$this->group->id.'"',NULL,FALSE);
            }
            $balance = $this->db->get("loan_invoices")->row()->balance;

            $this->db->select('sum('.$this->dx('principle_amount_payable').') - sum('.$this->dx('amount_paid').') as balance ');
            $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
            $this->db->where($this->dx('active').'="1"',NULL,FALsE);
            $this->db->where($this->dx('due_date').'>="'.$date.'"',NULL,FALSE);
            if($group_id){
            }else{
                $this->db->where($this->dx('loan_invoices.group_id').'="'.$this->group->id.'"',NULL,FALSE);
            }
            $principle = $this->db->get("loan_invoices")->row()->balance;

            return $balance+$principle;
        }else{
            $total_installment_payable = $this->get_total_installment_loan_payable($loan_id);
            $total_fines = $this->get_total_loan_fines_payable($loan_id);
            $total_paid = $this->loan_repayments_m->get_loan_total_payments($loan_id);
            $total_transfers_out = $this->get_total_loan_transfers_out($loan_id);

            return ($total_installment_payable+$total_fines-$total_paid+$total_transfers_out);
        }
    }

    function get_outstanding_loan_balance($member_id,$loan_id, $invoice_date = 0)
    {
        $this->db->select('sum('.$this->dx('amount_payable').')  as total_amount_payable');
        $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE);
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALsE);
        $this->db->where($this->dx('type').'="1"',NULL,FALsE);
        if ($invoice_date){
            $this->db->where($this->dx('invoice_date').'<="'.$invoice_date.'"',NULL,FALSE);
        }
        $amount_payable = $this->db->get("loan_invoices")->row()->total_amount_payable;

        $this->db->select(array(
            "SUM(".$this->dx('amount').") as total_paid",
        ));
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('receipt_date').' < "'.$invoice_date.'"',NULL,FALSE);
        if($group_id){
        }else{
            $this->db->where($this->dx('loan_repayments.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $loan_repayment = $this->db->get('loan_repayments')->row();
        if($loan_repayment){
            $total_amount_paid = $loan_repayment->total_paid;
        }else{
            $total_amount_paid = 0;
        }

        return $amount_payable - $total_amount_paid;
    }


    function outstanding_loan_balance_fine_invoice_exists_fixer($loan_id=0,$date=0)
    {
        $this->db->where($this->dx('type').'= "3"',NULL,FALSE);
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALsE);
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('fine_date')."),'%Y %D %M') = '" . date('Y jS F',$date) . "'", NULL, FALSE);
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('invoice_date')."),'%Y %D %M') = '" . date('Y jS F',$date) . "'", NULL, FALSE);
        $count = $this->db->count_all_results('loan_invoices')?:0;
        return $count;
    }

    function get_loan_instalments($loan_id=0)
    {
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where('('.$this->dx('type').'="1" OR '.$this->dx('type').'="5"',NULL,FALSE);
        return $this->db->get('loan_invoices')->result();
    }

    function count_active_loan_invoices($loan_id=0){
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('type').'="1"',NULL,FALSE);
        return $this->db->count_all_results('loan_invoices');
    }

    function get_loan_fines($loan_id=0)
    {
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"');
        $this->db->where($this->dx('active').'="1"');
        $this->db->where($this->dx('type').'!="1"');
        return $this->db->get('loan_invoices')->result();
    }


    function get_total_installment_loan_payable($loan_id=0,$group_id=0)
    {
        $this->db->select('sum('.$this->dx('amount_payable').') as amount_payable');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('type').'="1"',NULL,FALSE);
        return $this->db->get('loan_invoices')->row()->amount_payable?:0;

    }

    function get_total_loan_fines_payable($loan_id,$group_id=0)
    {
        $this->db->select('sum('.$this->dx('amount_payable').') as amount_payable');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('type').'!="1"',NULL,FALSE);
        $this->db->where($this->dx('type').'!="5"',NULL,FALSE);
        if($group_id){
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        return $this->db->get('loan_invoices')->row()->amount_payable?:0;
    }

    function get_total_loan_transfers_out($loan_id){
        $this->db->select('sum('.$this->dx('amount_payable').') as amount_payable');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('type').'="5"',NULL,FALSE);
        // if($group_id){
        // }else{
        //     $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        // }
        return $this->db->get('loan_invoices')->row()->amount_payable?:0;
    }

    function get_group_test_loan_invoices($loan_id = 0){
        $arr = array();
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_invoices.active').' = "1" ',NULL,FALSE);
        $this->db->order_by($this->dx('invoice_date'),'ASC',FALSE);
        $loan_invoices = $this->db->get('loan_invoices')->result();
        foreach($loan_invoices as $loan_invoice):
            $arr[$loan_invoice->loan_id][] = $loan_invoice;
        endforeach;
        return $arr;
    }


    /****invoices fines***/

    function get_late_loan_payment_fine_list($date=0){
        if($date){

        }else{
            $date=time();
        }
        $this->select_all_secure('loan_invoices');
        $this->db->select(array(
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loan_invoices.fine_date')." ),'%Y %D %M') as fine_date2 ",
                $this->dx('loan_invoices.status').' as invoice_status',
            ));  
        $this->db->where($this->dx('loan_invoices.type').'="1"',NULL,FALSE);  
        $this->db->where($this->dx('loan_invoices.active').'="1"',NULL,FALSE);
        $this->db->where('('.$this->dx('status').'="1" OR '.$this->dx('status').'="" OR '.$this->dx('status').'="0" OR '.$this->dx('status').'=" " OR '.$this->dx('status').'is NULL )',NULL,FALSE);
        $this->db->where('('.$this->dx('loan_invoices.disable_fines').'=" " OR '.$this->dx('loan_invoices.disable_fines').'is NULL OR '.$this->dx('loan_invoices.disable_fines').' = "")');
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loan_invoices.fine_date')."),'%Y %D %M') = '". date('Y jS F',$date) . "'", NULL, FALSE);
        return $this->db->get('loan_invoices')->result();
    }



    function get_invoice_sent_today($loan_id=0,$date=0){
        $this->db->select('id');
        $this->db->where($this->dx('loan_id').' = "'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('loan_invoices.active').'="1"',NULL,FALSE);
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loan_invoices.invoice_date')."),'%Y %D %M') = '". date('Y jS F',$date) . "'", NULL, FALSE);
        $this->db->where($this->dx('loan_invoices.type').'="2"',NULL,FALSE);
        return $this->db->get('loan_invoices')->row();
    }


    /***loan invoices queue*****/


    function insert_loan_invoicing_queue($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('loan_invoicing_queue',$input);
    }

    function insert_loan_invoicing_batch_queue($input,$skip_validation=FALSE){
        return $this->insert_batch_secure_data('loan_invoicing_queue',$input);
    }

    function get_queued_loan_invoices($limit=0)
    {
        if($limit){

        }else{
            $limit=25;
        }

        $this->select_all_secure('loan_invoicing_queue');
        $this->db->where($this->dx('loan_invoicing_queue.invoice_type').'="1"',NULL,FALSE);
        $this->db->order_by('loan_invoicing_queue.id','ASC');
        $this->db->limit($limit);
        return $this->db->get('loan_invoicing_queue')->result();
    }

    function delete_queued_loan_invoices($id=0){
        return $this->db->where('id',$id)->delete('loan_invoicing_queue');
    }


    function get_queued_loan_invoices_fine_list($limit=0){
        if($limit){
        }else{
            $limit=25;
        }

        $this->select_all_secure('loan_invoicing_queue');
        $this->db->where($this->dx('loan_invoicing_queue.invoice_type').'="2"',NULL,FALSE);
        $this->db->order_by('loan_invoicing_queue.id','ASC');
        $this->db->limit($limit);
        return $this->db->get('loan_invoicing_queue')->result();
    }


    function get_queued_outstanding_loan_balance_invoices_fine_list($limit){
        if($limit){
        }else{
            $limit=25;
        }

        $this->select_all_secure('loan_invoicing_queue');
        $this->db->where($this->dx('loan_invoicing_queue.invoice_type').'="3"',NULL,FALSE);
        $this->db->order_by('loan_invoicing_queue.id','ASC');
        $this->db->limit($limit);
        return $this->db->get('loan_invoicing_queue')->result();
    }



    /********loan_invoices_sent*****/

    function insert_loan_invoices_sent($input=array(),$SKIP_VALIDATION=FALSE){
       return $this->insert_secure_data('loan_invoices_sent',$input); 
    }

    function get_all_group_sent_loan_invoices()
    {

        $this->select_all_secure('loans');
        $this->select_all_secure('loan_invoices_sent');
        $this->db->select(array(
            $this->dx('users.first_name').' as first_name',
            $this->dx('users.last_name').' as last_name',
            $this->dx('users.phone').' as phone',
            $this->dx('users.email').' as email',
            'users.id as user_id',        
        ));
        $this->db->where($this->dx('loan_invoices_sent.group_id').'="'.$this->group->id.'"',NULL,FALSE);

        $this->db->join('loans',$this->dx('loan_invoices_sent.loan_id').'=loans.id');
        $this->db->join('members','members.id = '.$this->dx('loan_invoices_sent.member_id'));
        $this->db->join('users','users.id = '.$this->dx('members.user_id'));
        $this->db->order_by($this->dx('loan_invoices_sent.created_on'),'DESC',FALSE);
        return $this->db->get('loan_invoices_sent')->result();
    }


    function count_all_loan_invoices($loan_id=0){
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where('('.$this->dx('type').'="1")',NULL,FALSE);
        $this->db->where('('.$this->dx('active').'="1")',NULL,FALSE);
        $this->db->where('('.$this->dx('status').'="1" OR '.$this->dx('status').'="" OR '.$this->dx('status').'="0" OR '.$this->dx('status').'=" " OR '.$this->dx('status').'is NULL )',NULL,FALSE);
        return $this->db->count_all_results('loan_invoices')?:0;
    }


    function void_children_invoices_fined_wrongly($parent_id=0){
        $this->select_all_secure('loan_invoices');
        $this->db->select(array(
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loan_invoices.due_date')." ),'%Y %D %M') as deu_date2 ",
            ));
        $this->db->where($this->dx('fine_parent_loan_invoice_id').'="'.$parent_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $fines = $this->db->get('loan_invoices')->result();
        if($fines){
            foreach ($fines as $fine) {
                $this->update($fine->id,array('status'=>'','active'=>''));
                if($fine->is_sent){
                    $this->update_statement_where($fine->id);
                }
            }
            return TRUE;

        }else{
            return FALSE;
        }
    }

    function void_children_invoices_fined_wrongly_after_date($parent_id=0,$due_date=0){
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('fine_parent_loan_invoice_id').'="'.$parent_id.'"',NULL,FALSE);
        $this->db->where($this->dx('due_date').'>"'.$due_date.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $fines = $this->db->get('loan_invoices')->result();
        if($fines){
            foreach ($fines as $fine) {
                $this->update($fine->id,array('status'=>'','active'=>''));
                if($fine->is_sent){
                    $this->update_statement_where($fine->id);
                }
            }
            return TRUE;

        }else{
            return FALSE;
        }
    }

    function update_statement_where($fine_id=0){
       return $this -> db -> query("update loan_statements set 
                active=".$this->exa('0').",
                status = ".$this->exa('0')." 
                where ".$this->dx("loan_invoice_id")." ='".$fine_id."'");  
    }

    function update_invoices_remove_amount($loan_id=0){
       return $this ->db->query("update loan_invoices set 
                status =".$this->exa('0').",
                amount_paid = ".$this->exa('0').", 
                modified_on = ".$this->exa(time())." 
                where ".$this->dx("loan_id")." ='".$loan_id."'");  
    }


    function get_paid_invoices($loan_id=0){
        $this->select_all_secure('loan_invoices');
        $this->db->select(array(
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loan_invoices.due_date')." ),'%Y %D %M') as deu_date2 ",
            ));
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"');
        $this->db->where($this->dx('amount_paid').'> "0"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"');
        $this->db->order_by($this->dx('due_date'),'DESC',FALSE);
        return $this->db->get('loan_invoices')->result();
    }

    function void_outstanding_balance($loan_id=0){
        $this->db->select('loan_invoices.id');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('type').'="3"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $invoices = $this->db->get('loan_invoices')->result();
        foreach ($invoices as $invoice) 
        {
            $this->update($invoice->id,array('active'=>NULL,'status'=>NULL));
        }

       return TRUE; 
    }

    function has_outstanding_balance($loan_id=0){
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('type').'="3"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->count_all_results('loan_invoices')?:0;
    }

    function loan_invoices_and_fines($loan_id=0,$group_id=0){

        $this->db->select('sum('.$this->dx('amount_payable').') - sum('.$this->dx('amount_paid').') as balance');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('type').'!="3"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if($group_id){
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $balance = $this->db->get('loan_invoices')->row()->balance;
        if($balance==0 || $balance<0){
            return 0;
        }
        else{
            return $balance;
        }
    }


    function test_server_time(){
         $this->select_all_secure('loan_invoices');
        $this->db->select(array(
                    "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loan_invoices.invoice_date')." ),'%Y %D %M') as invoice_date2 ",
                ));
        $this->db->limit(2);
        return $this->db->get('loan_invoices')->result();
    }

    function count_loan_invoices_sent_today(){
        $this->select_all_secure('loan_invoices_sent');
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')."),'%Y %d %m') = '" . date('Y d m',time()) . "'", NULL, FALSE);
        return $this->db->count_all_results('loan_invoices_sent')?:0;
    }

    function count_sent_loan_invoices($filter_params=array()){
        if(isset($filter_params['group_id']) && $filter_params['group_id']){
            $groups_id_list = '0';
            $groups = $filter_params['group_id'];
            $count = 1;
            foreach($groups as $group_id){
                if($group_id){
                    if($count==1){
                        $groups_id_list = $group_id;
                    }else{
                        $groups_id_list .= ','.$group_id;
                    }
                    $count++;
                }
            }
            if($groups_id_list){
                $this->db->where($this->dx('loan_invoices_sent.group_id').' IN ('.$groups_id_list.')',NULL,FALSE);
            }
        }
        if((isset($filter_params['from']) && isset($filter_params['to'])) && ($filter_params['from'] && $filter_params['to'])){
           $this->db->where($this->dx('loan_invoices_sent.created_on').' >="'.$filter_params['from'].'"', NULL, FALSE);
           $this->db->where($this->dx('loan_invoices_sent.created_on').' <="'.$filter_params['to'].'"', NULL, FALSE);
        }
        return $this->db->count_all_results('loan_invoices_sent')?:0;
    }

    function get_all_sent_loan_invoices($filter_params=array())
    {
        $this->select_all_secure('loans');
        $this->select_all_secure('loan_invoices_sent');
        $this->db->select(array(
            $this->dx('users.first_name').' as first_name',
            $this->dx('users.last_name').' as last_name',
            $this->dx('users.phone').' as phone',
            $this->dx('users.email').' as email',
            'users.id as user_id',        
        ));
        if(isset($filter_params['group_id']) && $filter_params['group_id']){
            $groups_id_list = '0';
            $groups = $filter_params['group_id'];
            $count = 1;
            foreach($groups as $group_id){
                if($group_id){
                    if($count==1){
                        $groups_id_list = $group_id;
                    }else{
                        $groups_id_list .= ','.$group_id;
                    }
                    $count++;
                }
            }
            if($groups_id_list){
                $this->db->where($this->dx('loan_invoices_sent.group_id').' IN ('.$groups_id_list.')',NULL,FALSE);
            }
        }
        if((isset($filter_params['from']) && isset($filter_params['to'])) && ($filter_params['from'] && $filter_params['to'])){
           $this->db->where($this->dx('loan_invoices_sent.created_on').' >="'.$filter_params['from'].'"', NULL, FALSE);
           $this->db->where($this->dx('loan_invoices_sent.created_on').' <="'.$filter_params['to'].'"', NULL, FALSE);
        }
        $this->db->join('loans',$this->dx('loan_invoices_sent.loan_id').'=loans.id');
        $this->db->join('members','members.id = '.$this->dx('loan_invoices_sent.member_id'));
        $this->db->join('users','users.id = '.$this->dx('members.user_id'));
        $this->db->order_by($this->dx('loan_invoices_sent.created_on'),'DESC',FALSE);
        return $this->db->get('loan_invoices_sent')->result();
    }


    function get_transfered_invoice($loan_id=0,$transfer_id=0,$void_single_invoice=FALSE){
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('type').'="5"',NULL,FALSE);
        if($void_single_invoice){
            $this->db->where($this->dx('contribution_transfer_id').'="'.$transfer_id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('loan_invoices')->result();
    }

    function get_custom_invoices($loan_id=0){
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('type').'="1"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->order_by($this->dx('invoice_date'),'ASC',FALSE);
        return $this->db->get('loan_invoices')->result();
    }

    function get_group_member_total_back_dated_loans_payable_array($group_id=0){
        $arr = array();
        foreach ($this->group_member_options as $member_id => $name){
            $arr[$member_id] = 0;
        }
        $this->db->select(
            array(
                " sum(".$this->dx('amount_payable').") as amount_payable ",
                $this->dx('member_id')." as member_id ",
            )
        );  
        $this->db->where($this->dx('type').'="1"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('is_a_back_dating_record').'="1"',NULL,FALSE);
        $this->db->group_by(
            array(
                $this->dx('member_id'),
            )
        );
        if($group_id){
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $loan_invoices = $this->db->get('loan_invoices')->result();
        foreach($loan_invoices as $loan_invoice):
            $arr[$loan_invoice->member_id] = $loan_invoice->amount_payable;
        endforeach;
        return $arr;
    }

    function update_group_back_dating_loan_invoices_cut_off_date($group_id = 0,$input = array()){
        $where = " ".$this->dx('is_a_back_dating_record')." = '1' AND ".$this->dx('group_id')." = '".$group_id."' AND ".$this->dx('active')." = '1' ;";
        if($this->update_where($where,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function update_voided_loan_invoices($loan_id = 0){
        $where = " ".$this->dx('loan_id')." = ".$loan_id." ;";
        $input = array(
            'active' => 0,
            'modified_on' => time(),
        );
        if($this->update_where($where,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function get_group_total_loan_interest_payable($group_id = 0,$from = 0,$to = 0){
        $this->db->select(
            array(
                " SUM(".$this->dx('interest_amount_payable').") as interest_amount_payable "
            )
        );
        if($group_id){
            $this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
        }

        if($from){
            $this->db->where($this->dx('invoice_date').' >= "'.$from.'" ',NULL,FALSE);
        }
        if($to){
            $this->db->where($this->dx('invoice_date').' <= "'.$to.'" ',NULL,FALSE);
        }
        $this->db->where($this->dx('type').' IN (1) ',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        return $this->db->get('loan_invoices')->row()->interest_amount_payable;
    }

    function get_group_total_loan_interest_paid_per_year_array($group_id = 0){

        $arr = array();
        $this->db->select(
            array(
                " SUM(".$this->dx('amount_paid').") - SUM(".$this->dx('principle_amount_payable').") as interest_paid ",
                " DATE_FORMAT(FROM_UNIXTIME(".$this->dx('due_date')." ),'%Y') as year ",
            )
        );
        if($group_id){
            $this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('amount_paid')." > ".$this->dx('principle_amount_payable')." ",NULL,FALSE);
        $this->db->where($this->dx('type').' IN (1) ',NULL,FALSE);
        $this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $this->db->where($this->dx('group_id').' = "'.$this->group->id.'" ',NULL,FALSE);
        $this->db->group_by(
            array(
                'year',
            )
        );
        $loan_invoices = $this->db->get('loan_invoices')->result();
        foreach($loan_invoices as $loan_invoice):
            $arr[$loan_invoice->year] = ($loan_invoice->interest_paid > 0)?$loan_invoice->interest_paid:0;
        endforeach;
        return $arr;
    }

    function get_group_total_loan_fines_payable($group_id = 0,$from = 0,$to = 0){
        $this->db->select(
            array(
                " SUM(".$this->dx('amount_payable').") as amount_payable "
            )
        );
        if($group_id){
            $this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
        }

        if($from){
            $this->db->where($this->dx('invoice_date').' >= "'.$from.'" ',NULL,FALSE);
        }
        if($to){
            $this->db->where($this->dx('invoice_date').' <= "'.$to.'" ',NULL,FALSE);
        }
        $this->db->where($this->dx('type').' IN (2,3) ',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('loan_invoices')->row()->amount_payable;
    }
    function update_all_repayments_loan_id_for_modified_loan($loan_id = ''){
        $this->select_all_secure('loan_repayments');
        $this->db->where($this->dx('loan_id').' = '.$loan_id);
        $results = $this->db->get('loan_repayments')->result();
        if($results){
           $input = array(
                'loan_id' => $loan_id,
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

    function get_months_loan_installments($group_id = 0,$month_count = 0,$year = 0){
        //$this->select_all_secure('loan_invoices');
        $this->db->select(
            array(
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loan_invoices.due_date')." ),'%Y %D %M') as due_date ",
                $this->dx('loan_invoices.loan_id')." as loan_id",
                $this->dx('loan_invoices.member_id')." as member_id",
                $this->dx('loan_invoices.principle_amount_payable')." as principle_amount_payable",
                $this->dx('loan_invoices.interest_amount_payable')." as interest_amount_payable ",
                $this->dx('loan_invoices.amount_payable')." as amount_payable",
                $this->dx('loan_invoices.amount_paid')." as amount_paid",
                $this->dx('loans.loan_amount')." as loan_amount",
                $this->dx('loans.disbursement_date')." as disbursement_date",
                $this->dx('loans.interest_rate')." as interest_rate",
                $this->dx('loans.loan_interest_rate_per')." as loan_interest_rate_per",
                $this->dx('loans.repayment_period')." as repayment_period",
                $this->dx('loans.interest_type')." as interest_type",
            )
        );
        if($group_id){
            $this->db->where($this->dx('loan_invoices.group_id').' = "'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loan_invoices.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
        }
        if($month_count){

        }else{
            $month_count = date('n');
        }
        if($year){

        }else{
            $year = date('Y');
        }

        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('due_date')."),'%c %Y') = '" . $month_count .' '.$year."  '", NULL, FALSE);
        $this->db->where($this->dx('loan_invoices.active').' = "1" ',NULL,FALSE);
        $this->db->where('('.$this->dx('loans.is_fully_paid').' = "" OR '.$this->dx('loans.is_fully_paid').' IS NULL OR '.$this->dx('loans.is_fully_paid').' = "0" )',NULL,FALSE);
        //$this->db->where('('.$this->dx('loan_invoices.status').' = "" OR '.$this->dx('loan_invoices.status').' IS NULL OR '.$this->dx('loan_invoices.status').' = "1" )',NULL,FALSE);
        $this->db->group_by(
            array(
                $this->dx('loan_invoices.loan_id'),
            )
        );
        $this->db->join('loans',$this->dx('loan_invoices.loan_id')." = loans.id ");
        $this->db->order_by($this->dx('loans.disbursement_date'),'DESC',FALSE);
        return $this->db->get('loan_invoices')->result();

    }

    function get_group_total_loan_balance($group_id=0,$from=0,$to=0){
        $this->db->select(
            array(
                " SUM(".$this->dx('amount_payable').") - SUM(".$this->dx('amount_paid').") as total_loan_balance "
            )
        );
        if($from){
            $this->db->where($this->dx('invoice_date').' >= "'.$from.'" ',NULL,FALSE);
        }
        if($to){
            $this->db->where($this->dx('invoice_date').' <= "'.$to.'" ',NULL,FALSE);
        }
        $this->db->where($this->dx('loan_invoices.active').' = "1" ',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('loan_invoices.group_id').' = "'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loan_invoices.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
        }

        return $this->db->get('loan_invoices')->row()->total_loan_balance;

    }

    function get_group_total_loan_principal_balance($group_id=0,$from=0,$to=0){
        $this->db->select(
            array(
                " SUM(".$this->dx('principle_amount_payable').") - SUM(".$this->dx('amount_paid').") as total_principle_balance "
            )
        );
        if($from){
            $this->db->where($this->dx('invoice_date').' >= "'.$from.'" ',NULL,FALSE);
        }
        if($to){
            $this->db->where($this->dx('invoice_date').' <= "'.$to.'" ',NULL,FALSE);
        }
        $this->db->where($this->dx('loan_invoices.active').' = "1" ',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('loan_invoices.group_id').' = "'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loan_invoices.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('amount_paid')." < ".$this->dx('principle_amount_payable'),NULL,FALSE);

        return $this->db->get('loan_invoices')->row()->total_principle_balance;

    }

    function get_group_loan_invoices($group_id = 0){
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_invoices.active').' = "1" ',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('loan_invoices.group_id').' = "'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loan_invoices.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
        }
        return $this->db->get('loan_invoices')->result();
    }

    function get_group_loan_invoices_per_loan_array($group_id = 0){
        $arr = array();
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_invoices.active').' = "1" ',NULL,FALSE);
        //$this->db->where($this->dx('loan_invoices.type').' = "1" ',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('loan_invoices.group_id').' = "'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loan_invoices.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->order_by($this->dx('invoice_date'),'ASC',FALSE);
        $loan_invoices = $this->db->get('loan_invoices')->result();
        foreach($loan_invoices as $loan_invoice):
            $arr[$loan_invoice->loan_id][] = $loan_invoice;
        endforeach;
        return $arr;
    }

    function get_loan_principal_installments($loan_id = 0,$group_id=0){
        $this->select_all_secure('loan_invoices');
        $this->db->where($this->dx('loan_invoices.active').' = "1" ',NULL,FALSE);
        $this->db->where($this->dx('loan_invoices.loan_id').' = "'.$loan_id.'" ',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('loan_invoices.group_id').' = "'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('loan_invoices.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('loan_invoices.type').' = "1" ',NULL,FALSE);
        return $this->db->get('loan_invoices')->result();
    }

    function void_future_outstanding_loan_invoices($loan_id=0,$due_date=0){
        $this->select_all_secure('loan_invoices');
        $this->db->select(array(
                    "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loan_invoices.due_date')." ),'%Y%m%d') as due_date2 ",
                ));
        $this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
        $this->db->where('('.$this->dx('type').'="3")',NULL,FALSE);
        $this->db->where('('.$this->dx('active').'="1")',NULL,FALSE);
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('due_date')."),'%Y%m%d') >= '" . date('Ymd',$due_date) . "'", NULL, FALSE);
        $return =  $this->db->delete('loan_invoices');
    }


    function count_unpaid_loan_invoices($loan_id = 0){
        $this->db->where($this->dx('loan_invoices.type').' = "1" ',NULL,FALSE);
        $this->db->where('('.$this->dx('status').'="1" OR '.$this->dx('status').'="" OR '.$this->dx('status').'="0" OR '.$this->dx('status').'=" " OR '.$this->dx('status').'is NULL )',NULL,FALSE);
        $this->db->where($this->dx('loan_invoices.active').' = "1" ',NULL,FALSE);
        $this->db->where($this->dx('loan_invoices.loan_id').' = "'.$loan_id.'" ',NULL,FALSE);
        return $this->db->count_all_results('loan_invoices');
    }
}