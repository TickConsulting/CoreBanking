<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Bank_loans_m extends MY_Model{

	protected $_table = 'bank_loans';

	function __construct(){
		parent::__construct();
		////$this->install();
	}
    /**
        is_full_paid
        1 - fully paid 
        '' not paid fully

        active 
         1. active
         ''/ null voided
    */
	function install(){
        $this->db->query('create table if not exists bank_loans(
                id int not null auto_increment primary key,
                `description` blob,
                `amount_loaned` blob,
                `total_loan_amount_payable` blob,
                `loan_balance` blob,
                `loan_start_date` blob,
                `loan_end_date` blob,
                `account_id` blob,
                `group_id` blob,
                `account_type` blob,
                `active` blob,
                `is_fully_paid` blob,
                `created_by` blob,
                `created_on` blob,
                `modified_by` blob,
                `modified_on` blob
            )');

         $this->db->query('
            create table if not exists bank_loan_repayments(
                id int not null auto_increment primary key,
                `bank_loan_id` blob,
                `group_id` blob,
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
		return $this->insert_secure_data('bank_loans',$input);
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'bank_loans',$input);
    }

    function update_where($where = "",$input = array()){
        return $this->update_secure_where($where,'bank_loans',$input);
    }

    function get($id=0,$group_id=0)
    {
    	$this->select_all_secure('bank_loans');
    	$this->db->where('id',$id);
        if($group_id){
            $this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
        }
    	return $this->db->get('bank_loans')->row();
    }

   
    function get_all()
    {
    	$this->select_all_secure('bank_loans');
    	return $this->db->get('bank_loans')->result();
    }

    function get_group_bank_loans($filter_parameters = array(),$group_id = 0){
        $this->select_all_secure('bank_loans');
        if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
            if($filter_parameters['from'] && $filter_parameters['to']){
                $this->db->where($this->dx('loan_start_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
                $this->db->where($this->dx('loan_start_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
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
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').' = "1"',NULL,FALSE);
        return $this->db->get('bank_loans')->result();
    }

    function get_group_bank_loan_options($group_id =0 ){
        $arr = array();
        $this->db->select(
            array(
                'id',
                $this->dx('description').' as description '
            )
        );
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').' = "1"',NULL,FALSE);
        $return = $this->db->get('bank_loans')->result();
        foreach ($return as $row) {
            # code...
            $arr[$row->id] = $row->description;
        }
        return $arr;
    }


    //bank loan repayments

    function insert_repayment($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('bank_loan_repayments',$input);
    }

    function get_bank_loan_repayment($id=0){
        $this->select_all_secure('bank_loan_repayments');
        $this->db->select(array(
                $this->dx('bank_loans.description').' as description',
                'bank_loans.id'.' as loan_id',
            ));
        $this->db->where('bank_loan_repayments.id',$id);
        $this->db->join('bank_loans',$this->dx('bank_loan_repayments.bank_loan_id').'=bank_loans.id');
        return $this->db->get('bank_loan_repayments')->row();
    }

    function get_all_bank_loan_repayments($id=0){
        $this->select_all_secure('bank_loan_repayments');
        $this->db->where($this->dx('bank_loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx('status').'="1"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->order_by($this->dx('receipt_date'),'ASC',FALSE);
        return $this->db->get('bank_loan_repayments')->result();
    }

    function get_sum_paid($id=0){
        $this->db->select('sum('.$this->dx('amount').' ) as amount_paid');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('status').'="1"',NULL,FALSE);
        $amount_paid = $this->db->get('bank_loan_repayments')->row()->amount_paid;
        if($amount_paid>0){
            return $amount_paid;
        }else{
            return 0;
        }
    }

    function update_bank_loan_repayment($id,$input,$val=FALSE){
        return $this->update_secure_data($id,'bank_loan_repayments',$input);
    }

    function get_all_bank_loan_payments(){
        $this->select_all_secure('bank_loan_repayments');
        $this->db->select(array(
                $this->dx('bank_loans.description').' as description',
                'bank_loans.id'.' as loan_id',
            ));
        $this->db->where($this->dx('bank_loan_repayments.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('bank_loans.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('bank_loans.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->order_by($this->dx('receipt_date'),'DESC',FALSE);
        $this->db->join('bank_loans',$this->dx('bank_loan_repayments.bank_loan_id').'=bank_loans.id');
        return $this->db->get('bank_loan_repayments')->result();
    }

    function count_repayments(){
        $this->db->where($this->dx('bank_loan_repayments.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('bank_loans.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('bank_loans.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->join('bank_loans',$this->dx('bank_loan_repayments.bank_loan_id').'=bank_loans.id');
        return $this->db->count_all_results('bank_loan_repayments')?:0;
    }

    function get_loan_report(){
        $this->db->where($this->dx('bank_loans.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('bank_loans.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('bank_loans.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $count = $this->db->count_all_results('bank_loans')?:0;
        if($count){
            $this->select_all_secure('bank_loans');
            $this->db->where($this->dx('bank_loans.active').'="1"',NULL,FALSE);
            $this->db->where($this->dx('bank_loans.group_id').'="'.$this->group->id.'"',NULL,FALSE);
            $result = $this->db->get('bank_loans')->result();
            return $result;
        }else{
            return array();
        }
    }

    function total_loan_received_and_paid(){
        $this->db->select(array(
                    'sum('.$this->dx('bank_loans.amount_loaned').' ) as total_amount_received',
                    'sum('.$this->dx('bank_loans.loan_balance').' ) as total_arrears'
                ));
        $this->db->where($this->dx('bank_loans.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('bank_loans.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $total_amount_received = $this->db->get('bank_loans')->row();

        $this->db->select('sum('.$this->dx('bank_loan_repayments.amount').' ) as total_amount_repaid');
        $this->db->where($this->dx('bank_loan_repayments.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('bank_loan_repayments.status').'="1"',NULL,FALSE);
        $this->db->where($this->dx('bank_loan_repayments.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $total_amount_repaid  = $this->db->get('bank_loan_repayments')->row();
        
        if($total_amount_received){
            $total_arrears = $total_amount_received->total_arrears;
            $total_amount_received=$total_amount_received->total_amount_received;
        }else{
            $total_amount_repaid=0;
            $total_arrears = 0;
        }
        if($total_amount_repaid){
            $total_amount_repaid=$total_amount_repaid->total_amount_repaid;
        }else{
            $total_amount_repaid=0;
        }
        $result = array('total_amount_received'=>$total_amount_received,'total_amount_repaid'=>$total_amount_repaid,'total_arrears'=>$total_arrears);
        return (object)($result);
       
    }

    function get_group_back_dating_group_loan_borrowed(){
        $this->db->select(
            array(
                'id',
                'sum('.$this->dx('amount_loaned').') as amount_loaned',
                'sum('.$this->dx('total_loan_amount_payable').') as amount_payable',
            )
        );
        $this->db->where($this->dx('group_id')." = '".$this->group->id."' ",NULL,FALSE);
        $this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
        $this->db->where($this->dx('is_a_back_dating_record')." = '1' ",NULL,FALSE);
        return $this->db->get('bank_loans')->row();
    }

    function update_group_back_dating_bank_loans_cut_off_date($group_id = 0,$input = array()){
        $where = " ".$this->dx('is_a_back_dating_record')." = '1' AND ".$this->dx('group_id')." = '".$group_id."' AND ".$this->dx('active')." = '1' ;";
        if($this->update_where($where,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function get_group_bank_loans_interest($group_id=0,$date_from=0,$date_to=0){
        $this->db->select(array(
            'sum('.$this->dx('amount_loaned').') as total_amount_received',
            'sum('.$this->dx('total_loan_amount_payable').') as total_loan_amount_payable',
        ));
        if($group_id){
            $this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').' ="1"',NULL,FALSE);
        if($date_from){
            $this->db->where($this->dx('loan_start_date').' >="'.$date_from.'"',NULL,FALSE);
        }
        if($date_to){
            $this->db->where($this->dx('loan_start_date').' <="'.$date_to.'"',NULL,FALSE);
        }
        $amounts = $this->db->get('bank_loans')->row();
        if($amounts){
            $amount_received = $amounts->total_amount_received;
            $amount_payable = $amounts->total_loan_amount_payable;
            return $amount_payable-$amount_received;
        }
        
    }

    function get_group_total_bank_loan_payable(){
        $this->db->select(
            array(
                "SUM(".$this->dx('total_loan_amount_payable').") as payable "
            )
        );
        $this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').' ="1"',NULL,FALSE);
        return $this->db->get('bank_loans')->row()->payable;
    }

    function get_group_total_bank_loan_received(){
        $this->db->select(
            array(
                "SUM(".$this->dx('amount_loaned').") as received "
            )
        );
        $this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').' ="1"',NULL,FALSE);
        return $this->db->get('bank_loans')->row()->received;
    }

    function get_group_total_bank_loan_balance($group_id=0,$from=0,$to=0){
        $this->db->select(
            array(
                " SUM(".$this->dx('loan_balance').") as loan_balance "
            )
        );
        if($group_id){
            $this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
        }
        if($to){
            $this->db->where($this->dx("loan_start_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("loan_start_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->where($this->dx('active').' ="1"',NULL,FALSE);
        return $this->db->get('bank_loans')->row()->loan_balance;
    }


    function get_group_total_interest_bearing_liability_per_year_array($group_id = 0){
        $arr = array();

        $this->db->select(
            array(
                'SUM('.$this->dx('amount_loaned').') as amount ',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('loan_start_date')." ),'%Y') as year ",
            )
        );
        if($group_id){
            $this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'" ',NULL,FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->order_by($this->dx('loan_start_date'),'ASC',FALSE);
        $this->db->group_by(
            array(
                'year',
            )
        );

        $bank_loans = $this->db->get('bank_loans')->result();

        foreach($bank_loans as $bank_loan):
            if(isset($arr[$bank_loan->year])){
                $arr[$bank_loan->year] += $bank_loan->amount;
            }else{
                $arr[$bank_loan->year] = $bank_loan->amount;
            }
        endforeach;

        ksort($arr);

        $current_year = date('Y');

        foreach($bank_loans as $bank_loan):
            $year = $bank_loan->year + 1;
            for($i = $year; $i <= $current_year; $i++):
                if(isset($arr[$i])){
                    $arr[$i] += $bank_loan->amount;
                }else{
                    $arr[$i] = $bank_loan->amount;
                }
            endfor;
        endforeach;

        ksort($arr);

        $total_bank_loan_principal_paid_per_year_array = $this->withdrawals_m->get_group_total_bank_loan_principal_paid_per_year_array($group_id);

        ksort($total_bank_loan_principal_paid_per_year_array);

        foreach($total_bank_loan_principal_paid_per_year_array as $year => $principal_paid):
            for($i = $year; $i <= $current_year; $i++):
                $arr[$i] -= $principal_paid;
            endfor;
        endforeach;
        
        return $arr;
    }

}   