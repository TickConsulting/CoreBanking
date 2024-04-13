<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_statements_m extends MY_Model {

	protected $_table = 'transaction_statements';

	public function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->install();
		$this->load->library('transactions');
	}

	public function install(){
		$this->db->query("
		create table if not exists transaction_statements(
			id int not null auto_increment primary key,
			`transaction_type` blob,
			`transaction_date` blob,
			`account_id` blob,
			`withhdrawal_id` blob,
			`deposit_id` blob,
			`group_id` blob,
			`member_id` blob,
			`contribution_id` blob,
			`amount` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");

		$this->db->query("
		create table if not exists group_cut_off_dates(
			id int not null auto_increment primary key,
			`group_id` blob,
			`cut_off_date` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	}

	function insert($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('transaction_statements',$input);
	}

	function insert_batch($input = array(),$skip_value = FALSE){
		return $this->insert_chunked_batch_secure_data('transaction_statements',$input);
	}

	function insert_group_cut_off_date($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('group_cut_off_dates',$input);
	}

	function get_all_transactions_with_account_id_error(){
		$this->select_all_secure('transaction_statements');
		$this->db->where($this->dx('account_id').' like "%bank-bank%" ',NULL,FALSE);
		return $this->db->get('transaction_statements')->result();
	}
	function update_transactions_with_account_id_error($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'transaction_statements',$input);
	}
	function get_group_cut_off_date($group_id=0){
		$this->select_all_secure('group_cut_off_dates');
		$this->db->select(
			array(
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('cut_off_date')."),'%d-%m-%Y') as formatted_cut_off_date ",
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('group_start_date')."),'%d-%m-%Y') as formatted_group_start_date "
			)
		);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." = '".$this->group->id."' ",NULL,FALSE);
		}
		$this->db->limit(1);
		return $this->db->get('group_cut_off_dates')->row();
	}

	function disable_group_cut_off_date(){
		$where = " ".$this->dx('active')." = '1' AND ".$this->dx('group_id')." = '".$this->group->id."' ;";
		$input = array(
			'active' => 0,
			'modified_by' => $this->user->id,
			'modified_on' => time(),
		);
		return $this->update_secure_where($where,'group_cut_off_dates',$input);
	}

	function void_contribution_statements($contribution_id = ''){
		$where = " ".$this->dx('contribution_id')." = '".$contribution_id."';";
		$input = array(
			'active' => 0,
			'modified_on' => time(),
		);
		return $this->update_secure_where($where,'transaction_statements',$input);
	}

	function update($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'transaction_statements',$input);
	}
	
    function update_where($where = "",$input = array()){
        return $this->update_secure_where($where,'transaction_statements',$input);
    }

	function get_group_transaction_statement($from = 0,$account_id_list = 0,$group_id = 0,$limit = 0,$order ='ASC',$member_id = 0,$to = 0,$transaction_type_list=''){
		$this->select_all_secure('transaction_statements');
		if($from){
			$this->db->where($this->dx('transaction_date').' >= "'.$from.'" ',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date').' <= "'.$to.'" ',NULL,FALSE);
		}
		if($account_id_list){
        	$this->db->where($this->dx('account_id').' IN ('.$account_id_list.')',NULL,FALSE);
		}
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'" ',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'" ',NULL,FALSE);
		}
		if($transaction_type_list){
			$this->db->where($this->dx('transaction_type').' IN ('.$transaction_type_list.')',NULL,FALSE);
		}
		if($limit){
			$this->db->limit($limit);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->order_by($this->dx('transaction_date'),$order,FALSE);
		return $this->db->get('transaction_statements')->result();
	}

	function get_starting_balance($from = 0,$account_id_list = 0){
		//deposits
		$this->db->select('sum('.$this->dx('amount').') as total_deposit_amount');
		
		if($from){
			$this->db->where($this->dx('transaction_date').' < "'.$from.'" ',NULL,FALSE);
		}
		if($account_id_list){
        	$this->db->where($this->dx('account_id').' IN ('.$account_id_list.')',NULL,FALSE);
		}
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		if(empty($this->transactions->deposit_transaction_types)){
        	$this->db->where($this->dx('transaction_type').' IN (0)',NULL,FALSE);
		}else{
        	$this->db->where($this->dx('transaction_type').' IN ('.implode(',',$this->transactions->deposit_transaction_types).')',NULL,FALSE);
		}
        $total_deposit_amount = $this->db->get('transaction_statements')->row()->total_deposit_amount;
		//withdrawals

		$this->db->select('sum('.$this->dx('amount').') as total_withdrawal_amount');
		
		if($from){
			$this->db->where($this->dx('transaction_date').' < "'.$from.'" ',NULL,FALSE);
		}
		if($account_id_list){
        	$this->db->where($this->dx('account_id').' IN ('.$account_id_list.')',NULL,FALSE);
		}
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		if(empty($this->transactions->withdrawal_transaction_types)){
        	$this->db->where($this->dx('transaction_type').' IN (0)',NULL,FALSE);
		}else{
        	$this->db->where($this->dx('transaction_type').' IN ('.implode(',',$this->transactions->withdrawal_transaction_types).')',NULL,FALSE);
		}
        $total_withdrawal_amount = $this->db->get('transaction_statements')->row()->total_withdrawal_amount;
        return $total_deposit_amount - $total_withdrawal_amount;

	}

	function count_group_transaction_statement(){
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $this->db->count_all_results('transaction_statements');
	}

	function get_transaction_statement_entries_to_reconcile($account_id = 0,$group_id = 0,$transaction_date = 0){
		$this->select_all_secure('transaction_statements');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('account_id').' = "'.$account_id.'" ',NULL,FALSE);
		if($transaction_date){
			$this->db->where($this->dx('transaction_date').' >= "'.$transaction_date.'" ',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->order_by($this->dx('transaction_date'),'DESC',FALSE);
		return $this->db->get('transaction_statements')->result();
	}

	function get_account_balance($account_id = 0,$group_id = 0,$transaction_date = 0){
		$this->db->select(array($this->dx('balance').' as balance '));
		$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('account_id').' = "'.$account_id.'" ',NULL,FALSE);
		if($transaction_date){
			$this->db->where($this->dx('transaction_date').' >= "'.$transaction_date.'" ',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->order_by($this->dx('transaction_date'),'DESC',FALSE);
		$this->db->limit(1);
		$transaction_statement = $this->db->get('transaction_statements')->row();
		if($transaction_statement){
			return $transaction_statement->balance;
		}else{
			return 0;
		}
	}

	function get_transaction_statement_entry_by_deposit_id($deposit_id = 0,$group_id = 0){
		$this->select_all_secure('transaction_statements');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('deposit_id').' = "'.$deposit_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $this->db->get('transaction_statements')->row();
	}


	function get_transaction_statement_entry_by_withdrawal_id($withdrawal_id = 0,$group_id = 0){
		$this->select_all_secure('transaction_statements');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('withdrawal_id').' = "'.$withdrawal_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $this->db->get('transaction_statements')->row();
	}

	function check_if_group_account_has_transactions($account_id = 0,$group_id = 0){
		$this->db->where($this->dx('account_id').' = "'.$account_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $this->db->count_all_results('transaction_statements');
	}

	function check_if_contribution_has_transactions($contribution_id = 0,$group_id = 0){
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $this->db->count_all_results('transaction_statements');
	}

	function check_if_fine_category_has_transactions($fine_category_id=0,$group_id= 0){
		$this->db->where($this->dx('fine_category_id').' = "'.$fine_category_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $this->db->count_all_results('transaction_statements');
	}

	function check_if_income_category_has_transactions($income_category_id=0,$group_id=0){
		$this->db->where($this->dx('income_category_id').' = "'.$income_category_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $this->db->count_all_results('transaction_statements');
	}

	function check_if_expense_category_has_transactions($expense_category_id = 0, $group_id=0){
		$this->db->where($this->dx('expense_category_id').' = "'.$expense_category_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $this->db->count_all_results('transaction_statements');
	}

	function group_oldest_transaction($group_id=0){
		$this->db->select(array($this->dx('transaction_date').' as transaction_date'));
		if($group_id){
			$this->db->where($this->dx('group_id').'= "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'= "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->order_by($this->dx('transaction_date'),'ASC',FALSE);
		$this->db->limit(1);
		$result = $this->db->get('transaction_statements')->row();
		if($result){
			return strtotime('-7 days',$result->transaction_date);
		}else{
			return time();
		}
	}

	function update_group_back_dating_transaction_statements_cut_off_date($group_id = 0,$input = array()){
        $where = " ".$this->dx('is_a_back_dating_record')." = '1' AND ".$this->dx('group_id')." = '".$group_id."' AND ".$this->dx('active')." = '1' ;";
        if($this->update_where($where,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function get_group_account_balances_per_year_array($group_id = 0){
    	$arr = array();
    	$this->select_all_secure('transaction_statements');
    	if($group_id){
			$this->db->where($this->dx('group_id').'= "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'= "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->order_by($this->dx('transaction_date'),'ASC',FALSE);
		$transaction_statements = $this->db->get('transaction_statements')->result();

		foreach($transaction_statements as $transaction_statement):
			
			$arr[date('Y',$transaction_statement->transaction_date)] = 0;
			
		endforeach;

		foreach($transaction_statements as $transaction_statement):
            if(in_array($transaction_statement->transaction_type,$this->transactions->deposit_transaction_types)){
				$arr[date('Y',$transaction_statement->transaction_date)] += $transaction_statement->amount;
            }else if(in_array($transaction_statement->transaction_type,$this->transactions->withdrawal_transaction_types)){
				$arr[date('Y',$transaction_statement->transaction_date)] -= $transaction_statement->amount;
            }
		endforeach;

		foreach($arr as $key => $value):
			if(isset($arr[($key - 1)])){
				$arr[$key] += $arr[($key - 1)];
			}
		endforeach;
		return $arr;
    }

    function get_group_transaction_statement_deposit_ids_array($group_id = 0){
    	$this->select_all_secure('transaction_statements');
    	if($group_id){
			$this->db->where($this->dx('group_id').'= "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'= "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$transaction_statements = $this->db->get('transaction_statements')->result();
		$arr = array();
		foreach($transaction_statements as $transaction_statement):
			if($transaction_statement->deposit_id){
				$arr[$transaction_statement->deposit_id] = $transaction_statement->id;
			}
		endforeach;
		return $arr;
    }

    function get_group_transaction_statement_withdrawal_ids_array($group_id = 0){
    	$this->select_all_secure('transaction_statements');
    	if($group_id){
			$this->db->where($this->dx('group_id').'= "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'= "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$transaction_statements = $this->db->get('transaction_statements')->result();
		$arr = array();
		foreach($transaction_statements as $transaction_statement):
			if($transaction_statement->withdrawal_id){
				$arr[$transaction_statement->withdrawal_id] = $transaction_statement->id;
			}
		endforeach;
		return $arr;
    }

    function get_transaction_statements_by_loan_repayment_id($loan_repayment_ids=''){
    	if($loan_repayment_ids){
    		$this->select_all_secure('transaction_statements');
    		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
    		$this->db->where($this->dx('loan_repayment_id').' IN('.$loan_repayment_ids.')',NULL,FALSE);
    		return $this->db->get('transaction_statements')->result();
    	}
    }

    function get_transaction_statements_by_loan_id($loan_ids=''){
    	if($loan_ids){
    		$this->select_all_secure('transaction_statements');
    		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
    		$this->db->where($this->dx('loan_id').' IN('.$loan_ids.')',NULL,FALSE);
    		return $this->db->get('transaction_statements')->result();
    	}
    }

    function get_group_transaction_statements($group_id = 0){
    	$this->select_all_secure('transaction_statements');

    	if($group_id){
			$this->db->where($this->dx('group_id').'= "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'= "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $transaction_statements = $this->db->get('transaction_statements')->result();
    }

    function get_group_active_loan_repayment_deposit_transaction_statements($group_id = 0){
    	$this->select_all_secure('transaction_statements');
    	if($group_id){
			$this->db->where($this->dx('group_id').'= "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'= "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('transaction_type').' IN (17,18,19,20) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $transaction_statements = $this->db->get('transaction_statements')->result();
    }

    function get_transaction_statements_by_deposit_id($deposit_ids=''){
    	if($deposit_ids){
    		$this->select_all_secure('transaction_statements');
    		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
    		$this->db->where($this->dx('deposit_id').' IN('.$deposit_ids.')',NULL,FALSE);
    		return $this->db->get('transaction_statements')->result();
    	}
    }

    function get_orphan_transaction_statements(){
    	$this->select_all_secure('transaction_statements');
    	$this->db->where($this->dx('transaction_statements.active')." = 1 ",NULL,FALSE);
    	$this->db->where($this->dx('deposits.active')." = 0 ",NULL,FALSE);
    	$this->db->join('deposits',$this->dx('transaction_statements.deposit_id')." = deposits.id ");
    	return $this->db->get('transaction_statements')->result();
    }

    function get_group_account_balances_per_month_array($group_id = 0){
    	$arr = array();
    	$this->select_all_secure('transaction_statements');
    	if($group_id){
			$this->db->where($this->dx('group_id').'= "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'= "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->order_by($this->dx('transaction_date'),'ASC',FALSE);
		$transaction_statements = $this->db->get('transaction_statements')->result();

		foreach($transaction_statements as $transaction_statement):
			
			$arr[date('M Y',$transaction_statement->transaction_date)] = 0;
			
		endforeach;

		foreach($transaction_statements as $transaction_statement):
            if(in_array($transaction_statement->transaction_type,$this->transactions->deposit_transaction_types)){
				$arr[date('M Y',$transaction_statement->transaction_date)] += $transaction_statement->amount;
            }else if(in_array($transaction_statement->transaction_type,$this->transactions->withdrawal_transaction_types)){
				$arr[date('M Y',$transaction_statement->transaction_date)] -= $transaction_statement->amount;
            }
		endforeach;

		foreach($transaction_statements as $transaction_statement):
            $first_month = date('M Y',$transaction_statement->transaction_date);
            break;
        endforeach;

        $current_month = date('M Y');

        $months_array = generate_months_array(strtotime($first_month),strtotime($current_month));

		foreach($months_array as $month):
            if($month == $first_month){

            }else{
                $previous_month = date('M Y',strtotime('-1 month',strtotime($month)));
                if(isset($arr[($month)])){
                	$arr[$month] += $arr[($previous_month)];
                }else{
                	$arr[$month] = $arr[($previous_month)];
                }
            }
        endforeach;

		return $arr;
    }


}