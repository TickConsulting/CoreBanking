<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Statements_m extends MY_Model {

	protected $_table = 'statements';

	function __construct(){
		parent::__construct();
		$this->load->dbforge();
		$this->install();
		$this->load->model('contributions/contributions_m');
		$this->load->library('transactions');
	}

	/*
		1. Contribution invoice
		2. Contribution fine invoice
		3. Fine invoice
		4. General group invoice for special purposes defined by the group admin
		5. Back dated contribution invoice
		6. Back dated contribution fine invoice
		7. Back dated fine invoice
		8. Back dated general group invoice for special purposes defined by the group admin
		9. Contribution payment (bank)
		10. Contribution payment (sacco)
		11. Contribution payment (mobile)
		10. Contribution fine payment
		11. Fine payment
		12. General invoice payment for special purposes defined by the group admin 
		18. Backdated contribution refund
	*/

	public function install(){
		$this->db->query("
		create table if not exists statements(
			id int not null auto_increment primary key,
			`transaction_type` blob,
			`transaction_date` blob,
			`contribution_id` blob,
			`refund_id` blob,
			`fine_id` blob,
			`user_id` blob,
			`member_id` blob,
			`group_id` blob,
			`invoice_id` blob,
			`payment_id` blob,
			`amount` blob,
			`contribution_balance` blob,
			`balance` blob,
			`active` blob,
			`created_by` blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");

		$this->db->query("
		create table if not exists email_download_statement_request(
			id int not null auto_increment primary key,
			`user_id` blob,
			`member_id` blob,
			`group_id` blob,
			`statement_file_type` blob,
			`type` blob,
			`action` blob,
			`date_from` blob,
			`date_to` blob,
			`active` blob,
			`email` blob,
			`loan_id` blob,
			`created_by` blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");

		$this->db->query("
		create table if not exists member_contributions_fines_balances(
			id int not null auto_increment primary key,
			`member_id` blob,
			`group_id` blob,
			`contributions_amount_payable` blob,
			`contributions_amount_paid` blob,
			`contributions_balance` blob,
			`fines_amount_payable` blob,
			`fines_amount_paid` blob,
			`fines_balance` blob,
			`created_by` blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");

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
				`modified_by` blob
			)"
		);
	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('statements',$input);
	}

	function get($id=0){
		$this->select_all_secure('statements');
		$this->db->where('id',$id);
		return $this->db->get('statements')->row();
	}

	function insert_statements_batch($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_chunked_batch_secure_data('statements',$input);
	}

	function insert_statement_request($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('email_download_statement_request',$input);
	}

	function insert_member_contributions_fines_balances($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('member_contributions_fines_balances',$input);
	}

	function batch_insert_statement_request($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_chunked_batch_secure_data('email_download_statement_request',$input);
	}

	
	function get_queued_statement_requests($limit=2){
		$this->select_all_secure('email_download_statement_request');
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->limit($limit);
		return $this->db->get('email_download_statement_request')->result();
	}

	function update_statement_requests($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'email_download_statement_request',$input);
    }

    function delete_old_statement_requests(){
    	$this->db->where($this->dx('created_on').' <= "'.strtotime("-1 day",time()).'"',NULL,FALSE);
    	$this->db->delete('email_download_statement_request');
    }

	function get_member_contribution_statement($id = 0,$contribution_id_list = '',$from = 0,$to = 0,$group_id=0){
		$this->select_all_secure('statements');
		$this->db->where($this->dx('member_id').' = "'.$id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($contribution_id_list){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }
        
		if($to){
			$this->db->where($this->dx('transaction_date').' <= "'.$to.'"',NULL,FALSE);
		}

		if($from){
			$this->db->where($this->dx('transaction_date').' >= "'.$from.'"',NULL,FALSE);
		}
        $this->db->order_by($this->dx('transaction_date'), 'ASC', FALSE);
		return $this->db->get('statements')->result();
	}

	function get_member_deposit_statement_array($id = 0,$contribution_id_list = '',$from = 0,$to = 0,$group_id=0){
		$this->select_all_secure('statements');
		$this->db->where($this->dx('member_id').' = "'.$id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($contribution_id_list){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }
		if($to){
			$this->db->where($this->dx('transaction_date').' <= "'.$to.'"',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date').' >= "'.$from.'"',NULL,FALSE);
		}
		if(empty($this->transactions->paid_transaction_types_array)||empty($this->transactions->paid_deductable_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->paid_transaction_types_array,$this->transactions->paid_deductable_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
        $this->db->order_by($this->dx('transaction_date'), 'ASC', FALSE);
		$results =  $this->db->get('statements')->result();
		$arr = array();
		foreach ($results as $result) {
			$arr[$result->contribution_id][] = $result;
		}
		return $arr;
	}


	function get_member_test_contribution_statement($id = 0,$group_id=0){
		$this->select_all_secure('statements');
		$this->db->where($this->dx('member_id').' = "'.$id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
        $this->db->order_by($this->dx('transaction_date'), 'ASC', FALSE);
		return $this->db->get('statements')->result();
	}


	function get_member_fine_statement($id = 0,$contribution_id_list = '',$fine_category_id_list = '',$from = 0,$to = 0,$group_id=0){
		$this->select_all_secure('statements');
		$this->db->where($this->dx('member_id').' = "'.$id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('transaction_type').' IN (2,3,12,13,14,16,28) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('transaction_type').' = "2" OR '.$this->dx('transaction_type').' = "3" OR '.$this->dx('transaction_type').' = "12" OR '.$this->dx('transaction_type').' = "13" OR '.$this->dx('transaction_type').' = "14" OR '.$this->dx('transaction_type').' = "16" OR '.$this->dx('transaction_type').' = "28" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($contribution_id_list){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }
        if($fine_category_id_list){
        	$this->db->where($this->dx('fine_category_id').' IN ('.$fine_category_id_list.')',NULL,FALSE);
        }
		if($to){
			$this->db->where($this->dx('transaction_date').' < "'.$to.'"',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date').' > "'.$from.'"',NULL,FALSE);
		}
        $this->db->order_by($this->dx('transaction_date'), 'ASC', FALSE);
		return $this->db->get('statements')->result();
	}

	function get_member_miscellaneous_statement($id = 0,$from = 0,$to = 0){
		$this->select_all_secure('statements');
		$this->db->where($this->dx('member_id').' = "'.$id.'"',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where('('.$this->dx('transaction_type').' = "4" OR '.$this->dx('transaction_type').' = "17" OR '.$this->dx('transaction_type').' = "18" OR '.$this->dx('transaction_type').' = "19" OR '.$this->dx('transaction_type').' = "20" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($to){
			$this->db->where($this->dx('transaction_date').' < "'.$to.'"',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date').' > "'.$from.'"',NULL,FALSE);
		}
        $this->db->order_by($this->dx('transaction_date'), 'ASC', FALSE);
		return $this->db->get('statements')->result();
	}

	function get_member_contribution_statement_entries_to_reconcile($group_id = 0,$member_id = 0,$contribution_id = 0,$date = 0){
		$this->select_all_secure('statements');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
			$date -=24*60*60;
			$this->db->where($this->dx('transaction_date').' > "'.$date.'"',NULL,FALSE);
		}
        $this->db->order_by($this->dx('transaction_date'), 'ASC', FALSE);
		return $this->db->get('statements')->result();
	}

	function get_member_contribution_fine_statement_entries_to_reconcile($group_id = 0,$member_id = 0,$contribution_id = 0,$date = 0){
		$this->select_all_secure('statements');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		$this->db->where('('.$this->dx('transaction_type').' = "2" OR '.$this->dx('transaction_type').' = "12" OR '.$this->dx('transaction_type').' = "13" OR '.$this->dx('transaction_type').' = "14" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
			$date -=24*60*60;
			$this->db->where($this->dx('transaction_date').' > "'.$date.'"',NULL,FALSE);
		}
        $this->db->order_by($this->dx('transaction_date'), 'ASC', FALSE);
		return $this->db->get('statements')->result();
	}

	function get_member_miscellaneous_statement_entries_to_reconcile($group_id = 0,$member_id = 0,$date = 0){
		$this->select_all_secure('statements');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where('('.$this->dx('transaction_type').' = "4" OR '.$this->dx('transaction_type').' = "17" OR '.$this->dx('transaction_type').' = "18" OR '.$this->dx('transaction_type').' = "19" OR '.$this->dx('transaction_type').' = "20" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
			$date -=24*60*60;
			$this->db->where($this->dx('transaction_date').' > "'.$date.'"',NULL,FALSE);
		}
        $this->db->order_by($this->dx('transaction_date'), 'ASC', FALSE);
		return $this->db->get('statements')->result();
	}

	function get_member_fine_statement_entries_to_reconcile($group_id = 0,$member_id = 0,$fine_category_id = 0,$date = 0){
		$this->select_all_secure('statements');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('fine_category_id').' = "'.$fine_category_id.'"',NULL,FALSE);
		$this->db->where('('.$this->dx('transaction_type').' = "3" OR '.$this->dx('transaction_type').' = "12" OR '.$this->dx('transaction_type').' = "13" OR '.$this->dx('transaction_type').' = "14" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
			$date -=24*60*60;
			$this->db->where($this->dx('transaction_date').' > "'.$date.'"',NULL,FALSE);
		}
        $this->db->order_by($this->dx('transaction_date'), 'ASC', FALSE);
		return $this->db->get('statements')->result();
	}

	function get_member_contribution_balance($group_id = 0,$member_id = 0,$contribution_id_list = '',$date = 0,$from = 0,$ignore_contribution_transfers = FALSE){
		if($date){
			$date -=24*60*60; 
		}
		$this->select(array(' sum('.$this->dx('amount').') as payable '));
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		if($ignore_contribution_transfers){
        	$this->db->where($this->dx('transaction_type').' IN (1,21,22,23,24,27,30)',NULL,FALSE);
		}else{
        	$this->db->where($this->dx('transaction_type').' IN (1,21,22,23,24,25,27,30)',NULL,FALSE);
		}
		//$this->db->where('('.$this->dx('transaction_type').' = "1" OR '.$this->dx('transaction_type').' ="21" OR '.$this->dx('transaction_type').' ="22" OR '.$this->dx('transaction_type').' ="23" OR '.$this->dx('transaction_type').' ="24" OR '.$this->dx('transaction_type').' ="27" )',NULL,FALSE);
		if($contribution_id_list){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }elseif($contribution_id_list=='0'){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
            $this->db->where($this->dx("transaction_date") . " <= " .$date, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->limit(1);
        $return = $this->db->get('statements')->row();
        $payable = 0;
        if($return){
        	 if($return){
	            $payable = $return->payable;
	        }else{
	            $payable = 0;
	        }
        }
       
        $this->select(array(' sum('.$this->dx('amount').') as paid '));
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		if($ignore_contribution_transfers){
        	$this->db->where($this->dx('transaction_type').' IN (9,10,11,15)',NULL,FALSE);
		}else{
        	$this->db->where($this->dx('transaction_type').' IN (9,10,11,15,26)',NULL,FALSE);
		}
		//$this->db->where('('.$this->dx('transaction_type').' = "9" OR '.$this->dx('transaction_type').' = "10" OR '.$this->dx('transaction_type').' = "11" OR '.$this->dx('transaction_type').' = "15" )',NULL,FALSE);
		if($contribution_id_list){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }elseif($contribution_id_list=='0'){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
            $this->db->where($this->dx("transaction_date") . " <= " .$date, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->limit(1);
        $return = $this->db->get('statements')->row();
        $paid = 0;
        if($return){
	        $paid = $return->paid;
	       
        }
        
        $balance = $payable - $paid;
        return $balance;
        
	}


	function get_member_contribution_amount_payable($group_id = 0,$member_id = 0,$contribution_id_list = '',$date = 0,$from = 0){
		if($date){
			$date -=24*60*60; 
		}
		$this->select(array(' SUM('.$this->dx('amount').') as payable '));
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		if($contribution_id_list){
        	$this->db->where($this->dx('transaction_type').' IN (1,27,25)',NULL,FALSE);
		}else{
        	$this->db->where($this->dx('transaction_type').' IN (1,27)',NULL,FALSE);
        }

		//$this->db->where('('.$this->dx('transaction_type').' = "1" OR '.$this->dx('transaction_type').' ="21" OR '.$this->dx('transaction_type').' ="22" OR '.$this->dx('transaction_type').' ="23" OR '.$this->dx('transaction_type').' ="24" OR '.$this->dx('transaction_type').' ="27" )',NULL,FALSE);
		if($contribution_id_list){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }elseif($contribution_id_list=='0'){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
            $this->db->where($this->dx("transaction_date") . " <= " .$date, NULL, FALSE);
        }
        if($from){
            //$this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->limit(1);
        $return = $this->db->get('statements')->row();
        $payable = 0;
        if($return){
            $payable = $return->payable;
        }else{
            $payable = 0;
        }
        
        return $payable;
        
	}


	function get_member_contribution_amount_paid($group_id = 0,$member_id = 0,$contribution_id_list = '',$date = 0,$from = 0){
		if($date){
			$date -=24*60*60; 
		}

        $this->select(array(' SUM('.$this->dx('amount').') as paid '));
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		if($contribution_id_list){
        	$this->db->where($this->dx('transaction_type').' IN (9,10,11,15,26,27)',NULL,FALSE);
		}else{
        	$this->db->where($this->dx('transaction_type').' IN (9,10,11,15,27)',NULL,FALSE);
        }
		//$this->db->where('('.$this->dx('transaction_type').' = "9" OR '.$this->dx('transaction_type').' = "10" OR '.$this->dx('transaction_type').' = "11" OR '.$this->dx('transaction_type').' = "15" )',NULL,FALSE);
		if($contribution_id_list){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }elseif($contribution_id_list=='0'){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
            $this->db->where($this->dx("transaction_date") . " <= " .$date, NULL, FALSE);
        }
        if($from){
            //$this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->limit(1);
        $return = $this->db->get('statements')->row();
        $paid = 0;
        if($return){
            $paid = $return->paid;
        }else{
            $paid = 0;
        }

        $this->select(array(' sum('.$this->dx('amount').') as refund '));
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
        $this->db->where($this->dx('transaction_type').' IN (21,22,23,24)',NULL,FALSE);
		//$this->db->where('('.$this->dx('transaction_type').' = "9" OR '.$this->dx('transaction_type').' = "10" OR '.$this->dx('transaction_type').' = "11" OR '.$this->dx('transaction_type').' = "15" )',NULL,FALSE);
		if($contribution_id_list){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }elseif($contribution_id_list=='0'){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
            $this->db->where($this->dx("transaction_date") . " <= " .$date, NULL, FALSE);
        }
        if($from){
            //$this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->limit(1);
        $return = $this->db->get('statements')->row();
        $refund = 0;
        if($return){
            $refund = $return->refund;
        }else{
            $refund = 0;
        }

        
        return $paid = $paid - $refund;
	}


	function get_group_member_total_contribution_balances_array($group_id = 0){
		$arr = array();
		foreach ($this->group_member_options as $member_id => $name){
			if($group_id){
				$arr[$member_id] = $this->get_member_contribution_balance($group_id,$member_id);
			}else{
				$arr[$member_id] = $this->get_member_contribution_balance($this->group->id,$member_id);
			}
		}
		return $arr;
	}

	function get_group_member_total_cumulative_contribution_balances_array($group_id = 0){
		$cummulative_arrears_contribution_ids_list = $this->contributions_m->get_group_cumulative_arrears_contribution_id_list();
		//die;
		$arr = array();
		foreach ($this->group_member_options as $member_id => $name){
			if($group_id){
				$arr[$member_id] = $this->get_member_contribution_balance($group_id,$member_id,$cummulative_arrears_contribution_ids_list,0,0,TRUE);
			}else{
				$arr[$member_id] = $this->get_member_contribution_balance($this->group->id,$member_id,$cummulative_arrears_contribution_ids_list,0,0,TRUE);
			}
		}
		return $arr;
	}

	function get_group_member_total_cumulative_contribution_balance($group_id = 0,$member_id = 0){
		$cummulative_arrears_contribution_ids_list = $this->contributions_m->get_group_cumulative_arrears_contribution_id_list();
		return $this->get_member_contribution_balance($group_id,$member_id,$cummulative_arrears_contribution_ids_list);
	}

	function get_group_member_total_contribution_balances_per_contribution_array($group_id = 0,$to = 0,$from = 0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		foreach ($this->group_member_options as $member_id => $name){
			foreach($contribution_options as $contribution_id => $contribution_name):
				if($group_id){
					$arr[$member_id][$contribution_id] = $this->get_member_contribution_balance($group_id,$member_id,$contribution_id,$to,$from);
				}else{
					$arr[$member_id][$contribution_id] = $this->get_member_contribution_balance($this->group->id,$member_id,$contribution_id,$to,$from);
				}
			endforeach;
		}
		return $arr;
	}

	function get_group_total_contribution_balances_per_member_array($group_id = 0,$to = 0,$from = 0,$contribution_ids_list = '',$ignore_contribution_transfers = TRUE,$group_member_options = array()){
		if($group_member_options){

		}elseif($this->group_member_options){
			$group_member_options = $this->group_member_options;
		}
		$arr = array();
		foreach ($group_member_options as $member_id => $name){
			if($group_id){
				$arr[$member_id] = $this->get_member_contribution_balance($group_id,$member_id,$contribution_ids_list,$to,$from,$ignore_contribution_transfers);
			}else{
				$arr[$member_id] = $this->get_member_contribution_balance($this->group->id,$member_id,$contribution_ids_list,$to,$from,$ignore_contribution_transfers);
			}
		}
		return $arr;
	}

	function get_member_fine_balance($group_id = 0,$member_id = 0,$contribution_id_list = '',$fine_category_id_list = '',$date = 0){
		$this->select(array(' sum('.$this->dx('amount').') as payable '));
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' = "'.$this->member->id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('transaction_type').' IN (2,3)',NULL,FALSE);
		//$this->db->where('('.$this->dx('transaction_type').' = "2" OR '.$this->dx('transaction_type').' = "3" )',NULL,FALSE);
		if($contribution_id_list){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }
        if($fine_category_id_list){
        	$this->db->where($this->dx('fine_category_id').' IN ('.$fine_category_id_list.')',NULL,FALSE);
        }
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
            $this->db->where($this->dx("transaction_date") . " <= " .$date, NULL, FALSE);
        }
        $this->db->limit(1);
        $return = $this->db->get('statements')->row();
        $payable = 0;
        if($return){
        	$payable = $return->payable;
        }
        $this->select(array(' sum('.$this->dx('amount').') as paid '));
        if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' = "'.$this->member->id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('transaction_type').' IN (12,13,14,16,28)',NULL,FALSE);
		//$this->db->where('('.$this->dx('transaction_type').' = "12" OR '.$this->dx('transaction_type').' = "13" OR '.$this->dx('transaction_type').' = "14" OR '.$this->dx('transaction_type').' = "16" OR '.$this->dx('transaction_type').' = "28" )',NULL,FALSE);
		if($contribution_id_list){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }
        if($fine_category_id_list){
        	$this->db->where($this->dx('fine_category_id').' IN ('.$fine_category_id_list.')',NULL,FALSE);
        }
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
            $this->db->where($this->dx("transaction_date") . " <= " .$date, NULL, FALSE);
        }
        $this->db->limit(1);
        $return = $this->db->get('statements')->row();
        $paid = 0;
        if($return){
        	$paid = $return->paid;
        }
        $balance = $payable - $paid;
        return $balance;
	}

	function get_group_member_total_fine_balances_array($group_id = 0,$group_member_options = array()){
		if($group_member_options){}else{
			$group_member_options = $this->group_member_options;
		}
		$arr = array();
		foreach ($group_member_options as $member_id => $name){
			if($group_id){
				$arr[$member_id] = $this->get_member_fine_balance($group_id,$member_id);
			}else{
				$arr[$member_id] = $this->get_member_fine_balance($this->group->id,$member_id);
			}
		}
		return $arr;
	}

	function get_group_total_fines($group_id=0,$from=0,$to=0,$member_id = 0){
		$this->db->select('sum('.$this->dx('amount').') as amount');
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('contribution_from_id')." != 'loan' ",NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "28" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->limit(1);
		$result = $this->db->get('statements')->row();
		$amount = 0;
		if($result){
			$amount = $result->amount;
		}
		return $amount;
	}

	function get_member_miscellaneous_balance($group_id = 0,$member_id = 0,$date = 0){
		$this->select(array(' sum('.$this->dx('amount').') as payable '));
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "4" ',NULL,FALSE);

		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
            $this->db->where($this->dx("transaction_date") . " <= " .$date, NULL, FALSE);
        }
        $this->db->limit(1);
        $return = $this->db->get('statements')->row();
        $payable = 0;
        if($return){
            $payable = $return->payable;
        }else{
            $payable = 0;
        }

        $this->select(array(' sum('.$this->dx('amount').') as paid '));
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where('('.$this->dx('transaction_type').' = "17" OR '.$this->dx('transaction_type').' = "18" OR '.$this->dx('transaction_type').' = "19" OR '.$this->dx('transaction_type').' = "20" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
            $this->db->where($this->dx("transaction_date") . " <= " .$date, NULL, FALSE);
        }
        $this->db->limit(1);
        $return = $this->db->get('statements')->row();
        $paid = 0;
        if($return){
            $paid = $return->paid;
        }else{
            $paid = 0;
        }
        $balance = $payable - $paid;
        return $balance;
	}

	function get_statement_entry_by_invoice_id($invoice_id = 0){
		$this->select_all_secure('statements');
		$this->db->where($this->dx('invoice_id').' = "'.$invoice_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
        $this->db->limit(1);
        return $this->db->get('statements')->row();
	}

	function get_statement_entry_by_deposit_id($deposit_id = 0,$group_id=0){
		$this->select_all_secure('statements');
		$this->db->where($this->dx('deposit_id').' = "'.$deposit_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
        $this->db->limit(1);
        return $this->db->get('statements')->row();
	}

	function get_statement_entry_by_refund_id($refund_id=0){
		$this->select_all_secure('statements');
		$this->db->where($this->dx('refund_id').' = "'.$refund_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
        $this->db->limit(1);
        return $this->db->get('statements')->row();
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'statements',$input);
    }

    function update_statement($id=0,$input=array(),$group_id=0){
    	return $this->update_secure_data($id,'statements',$input,$group_id);
    }
    
    function update_where($where = "",$input = array()){
    	return $this->update_secure_where($where,'statements',$input);
    }

	function update_by_invoice_id($invoice_id,$input,$val=FALSE){
		$where = " ".$this->dx('invoice_id')." = ".$invoice_id." ; ";
    	return $this->update_secure_where($where,'statements',$input);
    }

    function get_member_contribution_balances_array($contribution_id = 0,$date = 0,$group_id = 0,$contribution_invoice_due_date = 0){
    	$arr = array();
    	if($date){
			$date -=24*60*60; 
		}
		$this->select(array($this->dx('member_id').' as member_id ',' sum('.$this->dx('amount').') as payable '));
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
        $this->db->where($this->dx('transaction_type').' IN (1,21,22,23,24,25,27,30)',NULL,FALSE);
		if($contribution_id){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id.')',NULL,FALSE);
        }elseif($contribution_id=='0'){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id.')',NULL,FALSE);
        }
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
            $this->db->where($this->dx("transaction_date") . " <= " .$date, NULL, FALSE);
        }
        
        $this->db->group_by(array($this->dx("member_id")));
        $payables = $this->db->get('statements')->result();

        $this->select(array($this->dx('member_id').' as member_id ',' sum('.$this->dx('amount').') as paid '));
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
        $this->db->where($this->dx('transaction_type').' IN (9,10,11,15,26)',NULL,FALSE);
		if($contribution_id){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id.')',NULL,FALSE);
        }elseif($contribution_id=='0'){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id.')',NULL,FALSE);
        }
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
            $this->db->where($this->dx("transaction_date") . " <= " .$date, NULL, FALSE);
        }
        $this->db->group_by(array($this->dx("member_id")));
        $paids = $this->db->get('statements')->result();

        if($contribution_invoice_due_date){
			if($date){
				$date -=24*60*60; 
			}
			$this->select(array($this->dx('member_id').' as member_id ',' sum('.$this->dx('amount').') as payable '));
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
	        $this->db->where($this->dx('transaction_type').' = 1 ',NULL,FALSE);
			if($contribution_id){
	        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id.')',NULL,FALSE);
	        }elseif($contribution_id=='0'){
	        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id.')',NULL,FALSE);
	        }
			$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
			if($date){
	            //$this->db->where($this->dx("transaction_date") . " < " .$date, NULL, FALSE);
	        }
	        if($contribution_invoice_due_date){
	            $this->db->where($this->dx("contribution_invoice_due_date") . " > " .$contribution_invoice_due_date, NULL, FALSE);
	        }
	        $this->db->group_by(array($this->dx("member_id")));
	        $ignore_payables = $this->db->get('statements')->result();
        }

        foreach ($ignore_payables as $ignore_payable) {
        	$arr[$ignore_payable->member_id] = 0;
        }

        foreach ($payables as $payable) {
        	$arr[$payable->member_id] = 0;
        }

        foreach ($paids as $paid) {
        	# code...
        	$arr[$paid->member_id] = 0;
        }

        foreach ($payables as $payable) {
        	# code...
        	$arr[$payable->member_id] = $payable->payable;
        }

        foreach ($ignore_payables as $ignore_payable) {
        	# code...
        	$arr[$ignore_payable->member_id] -= $ignore_payable->payable;
        }

        foreach ($paids as $paid) {
        	# code...
        	$arr[$paid->member_id] -= $paid->paid;
        }
        return $arr;
    }

    function get_contribution_transfer_statement_entries($contribution_transfer_id = 0,$group_id = 0){
    	$this->db->select('id');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('contribution_transfer_id').' = "'.$contribution_transfer_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where("(".$this->dx('transaction_type').' = "25" OR '.$this->dx('transaction_type').' = "26" OR '.$this->dx('transaction_type').' = "27" OR '.$this->dx('transaction_type').' = "28" OR '.$this->dx('transaction_type').'="29" OR '.$this->dx('transaction_type').'="30" OR '.$this->dx('transaction_type').'="31")',NULL,FALSE);
        return $this->db->get('statements')->result();
    }

    function get_all_contribution_transfer_statement_entries($contribution_transfer_id = 0,$group_id = 0){
    	$this->select_all_secure('statements');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('contribution_transfer_id').' = "'.$contribution_transfer_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where("(".$this->dx('transaction_type').' = "25" OR '.$this->dx('transaction_type').' = "26" OR '.$this->dx('transaction_type').' = "27" OR '.$this->dx('transaction_type').' = "28" OR '.$this->dx('transaction_type').'="29" OR '.$this->dx('transaction_type').'="30" OR '.$this->dx('transaction_type').'="31")',NULL,FALSE);
        return $this->db->get('statements')->result();
    }

    function get_group_contribution_transfer_by_loan_invoice($loan_invoice_id=0,$group_id=0){
		$this->select_all_secure('statements');
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').'="29"',NULL,FALSE);
		$this->db->where($this->dx('loan_transfer_invoice_id').'="'.$loan_invoice_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->get('statements')->row();
	}

	function get_group_contribution_transfer_by_contribution_transfer_id($contribution_transfer_id=0){
		$this->select_all_secure('statements');
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').'="26"',NULL,FALSE);
		$this->db->where($this->dx('contribution_transfer_id').'="'.$contribution_transfer_id.'"',NULL,FALSE);
		return $this->db->get('statements')->row();
	}

    function get_group_member_total_contribution_transfers_to_per_contribution_array($group_id = 0,$to = 0,$from = 0,$group_member_id = 0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		if($group_member_id){
			foreach($contribution_options as $contribution_id => $contribution_name){
				$arr[$group_member_id][$contribution_id] = 0;
			}
		}else{
			foreach ($this->group_member_options as $member_id => $name){
				foreach($contribution_options as $contribution_id => $contribution_name){
					$arr[$member_id][$contribution_id] = 0;
				}
			}
		}
		$this->db->select(
			array(
				"sum(".$this->dx('amount').') as amount ',
				$this->dx('contribution_id').' as contribution_id ',
				$this->dx('member_id').' as member_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "26" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		if($group_member_id){
			$this->db->where($this->dx('member_id').' = "'.$group_member_id.'"',NULL,FALSE);
		}
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->group_by(array($this->dx("member_id"),$this->dx("contribution_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$arr[$row->member_id][$row->contribution_id] = $row->amount;
			}
		}
		return $arr;
	}

    function get_group_total_contribution_transfers_to_per_member_array($group_id = 0,$to = 0,$from = 0,$group_member_options=array()){
		$arr = array();
		if($group_member_options){

		}elseif(isset($this->group_member_options)){
			$group_member_options = $this->group_member_options;
		}
		foreach ($group_member_options as $member_id => $name){
			$arr[$member_id] = 0;
		}
		$this->db->select(
			array(
				"sum(".$this->dx('amount').') as amount ',
				$this->dx('member_id').' as member_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "26" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->group_by(array($this->dx("member_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			$arr[$row->member_id] = $row->amount;
		}
		return $arr;
	}
    
    function get_group_member_contribution_transfers_to_per_contribution_array($member_id = 0,$group_id=0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options($group_id);
		foreach($contribution_options as $contribution_id => $contribution_name):
			$arr[$contribution_id] = 0;
		endforeach;
		$this->db->select(
			array(
				$this->dx('amount').' as amount ',
				$this->dx('contribution_id').' as contribution_id ',
				$this->dx('member_id').' as member_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "26" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
        $this->db->group_by(array($this->dx("member_id"),$this->dx("contribution_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$arr[$row->contribution_id] = $row->amount;
			}
		}
		return $arr;
	}

	function get_group_member_total_contribution_transfers_to($member_id = 0,$group_id=0){
		$arr = array();
		$this->db->select(
			array(
				"sum(".$this->dx('amount').') as amount ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "26" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->limit(1);
		$result = $this->db->get('statements')->row();
		return $result->amount;
	}

	function get_group_total_contribution_transfers_to($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		$arr = array();
		$this->db->select(
			array(
				" SUM(".$this->dx('amount').') as amount ',
			)
		);
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "26" ',NULL,FALSE);
		if($group_id){

			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->limit(1);
		$result = $this->db->get('statements')->row();
		return $result->amount;
	}

	function get_group_total_contribution_transfers_to_per_year($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		$arr = array();
		$this->db->select(
			array(
				" SUM(".$this->dx('amount').') as amount ',

				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
			)
		);
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "26" ',NULL,FALSE);
		if($group_id){

			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->group_by(
			array(
				'year'
			)
		);
		return $this->db->get('statements')->result();
	}

	function get_group_member_contribution_transfers_from_loan_to_contribution_per_contribution_array($member_id = 0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		foreach($contribution_options as $contribution_id => $contribution_name):
			$arr[$contribution_id] = 0;
		endforeach;
		$this->db->select(
			array(
				$this->dx('amount').' as amount ',
				$this->dx('contribution_id').' as contribution_id ',
				$this->dx('member_id').' as member_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('contribution_from_id').' = "loan" ',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "26" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
        $this->db->group_by(array($this->dx("member_id"),$this->dx("contribution_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$arr[$row->contribution_id] = $row->amount;
			}
		}
		return $arr;
	}

	function get_group_member_contribution_transfers_from_per_contribution_array($member_id = 0,$group_id=0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options($group_id);
		foreach($contribution_options as $contribution_id => $contribution_name):
			$arr[$contribution_id] = 0;
		endforeach;
		$this->db->select(
			array(
				$this->dx('amount').' as amount ',
				$this->dx('contribution_id').' as contribution_id ',
				$this->dx('member_id').' as member_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "25" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
        $this->db->group_by(array($this->dx("member_id"),$this->dx("contribution_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$arr[$row->contribution_id] = $row->amount;
			}
		}
		return $arr;
	}

	function get_group_member_contribution_transfers_from_contribution($group_id = 0,$member_id = 0,$contribution_id = 0){
		$this->db->select(
			array(
				$this->dx('amount').' as amount ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "25" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		$this->db->limit(1);
		$result = $this->db->get('statements')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_member_total_contribution_transfers_from($group_id = 0,$member_id = 0){
		$this->db->select(
			array(
				"sum(".$this->dx('amount').') as amount ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "25" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->limit(1);
		$result = $this->db->get('statements')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_total_contribution_transfers_from($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		$this->db->select(
			array(
				" SUM(".$this->dx('amount').') as amount ',
			)
		);

		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "25" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->limit(1);
		$result = $this->db->get('statements')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_total_contribution_transfers_from_per_year($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		$this->db->select(
			array(
				" SUM(".$this->dx('amount').') as amount ',

				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
			)
		);

		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "25" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->group_by(
			array(
				'year'
			)
		);
		return $this->db->get('statements')->result();
	}

	function get_group_total_contribution_transfers_to_loan_per_year($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		$this->db->select(
			array(
				" SUM(".$this->dx('amount').') as amount ',

				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
			)
		);

		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "30" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->group_by(
			array(
				'year'
			)
		);
		return $this->db->get('statements')->result();
	}

	function get_group_member_contribution_transfers_from_contribution_to_fine_category_per_contribution_array($member_id = 0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		foreach($contribution_options as $contribution_id => $contribution_name):
			$arr[$contribution_id] = 0;
		endforeach;
		$this->db->select(
			array(
				$this->dx('amount').' as amount ',
				$this->dx('contribution_id').' as contribution_id ',
				$this->dx('member_id').' as member_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "27" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
        $this->db->group_by(array($this->dx("member_id"),$this->dx("contribution_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$arr[$row->contribution_id] = $row->amount;
			}
		}
		return $arr;
	}

	function get_group_members_contribution_transfers_from_contribution_to_fine_category_per_contribution_array($group_id = 0,$to = 0,$from = 0,$group_member_id = 0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		if($group_member_id){
			foreach($contribution_options as $contribution_id => $contribution_name){
				$arr[$group_member_id][$contribution_id] = 0;
			}
		}else{
			foreach ($this->group_member_options as $member_id => $name){
				foreach($contribution_options as $contribution_id => $contribution_name){
					$arr[$member_id][$contribution_id] = 0;
				}
			}
		}
		$this->db->select(
			array(
				"sum(".$this->dx('amount').') as amount ',
				$this->dx('contribution_id').' as contribution_id ',
				$this->dx('member_id').' as member_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "27" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		if($group_member_id){
			$this->db->where($this->dx('member_id').' = "'.$group_member_id.'"',NULL,FALSE);
		}
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->group_by(array($this->dx("member_id"),$this->dx("contribution_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$arr[$row->member_id][$row->contribution_id] = $row->amount;
			}
		}
		return $arr;
	}

	function get_group_contribution_transfers_from_contribution_to_fine_category_per_contribution_array($group_id = 0,$to = 0,$from = 0,$contribution_id_list = ""){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();

		foreach($contribution_options as $contribution_id => $contribution_name){
			$arr[$contribution_id] = 0;
		}
		
		$this->db->select(
			array(
				"sum(".$this->dx('amount').') as amount ',
				$this->dx('contribution_id').' as contribution_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "27" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        if($contribution_id_list){
			$this->db->where($this->dx('contribution_id')." IN (".$contribution_id_list.") ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('contribution_id')." IN (0) ",NULL,FALSE);
		}
        $this->db->group_by(array($this->dx("contribution_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$arr[$row->contribution_id] = $row->amount;
			}
		}
		return $arr;
	}

	function get_group_contribution_transfers_from_contribution_to_fine_category_per_contribution_per_year_array($group_id = 0,$start_year = 2000,$end_year = 2039){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		foreach($contribution_options as $contribution_id => $contribution_name){
			for($i = $start_year; $i < $end_year; $i++):
				$arr[$contribution_id][$i] = 0;
			endfor;
		}
		
		$this->db->select(
			array(
				"SUM(".$this->dx('amount').') as amount ',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
				$this->dx('contribution_id').' as contribution_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "27" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
        $this->db->group_by(
        	array(
        		$this->dx("contribution_id"),
        		'year'
        	)
        );
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$arr[$row->contribution_id][$row->year] = $row->amount;
			}
		}
		return $arr;
	}

	function get_group_contribution_transfers_from_contribution_to_loans_per_contribution_per_year_array($group_id = 0,$start_year = 2000,$end_year = 2039){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		foreach($contribution_options as $contribution_id => $contribution_name){
			for($i = $start_year; $i < $end_year; $i++):
				$arr[$contribution_id][$i] = 0;
			endfor;
		}
		
		$this->db->select(
			array(
				"SUM(".$this->dx('amount').') as amount ',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
				$this->dx('contribution_id').' as contribution_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "30" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
        $this->db->group_by(
        	array(
        		$this->dx("contribution_id"),
        		'year'
        	)
        );
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$arr[$row->contribution_id][$row->year] = $row->amount;
			}
		}
		return $arr;
	}

	function get_group_contribution_transfers_from_contribution_to_fine_category_per_member_array($group_id = 0,$to = 0,$from = 0,$group_member_options=array()){
		$arr = array();
		if($group_member_options){

		}elseif(isset($this->group_member_options)){
			$group_member_options = $this->group_member_options;
		}
		foreach ($group_member_options as $member_id => $name) {
			# code...
			$arr[$member_id] = 0;
		}
		$this->db->select(
			array(
				"sum(".$this->dx('amount').') as amount ',
				$this->dx('member_id').' as member_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "27" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->group_by(array($this->dx("member_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			$arr[$row->member_id] = $row->amount;
		}
		return $arr;
	}


	function get_group_member_contribution_transfers_from_contribution_to_fine_category_fine_category_as_key_array($member_id = 0){
		$arr = array();
		$fine_category_options = $this->fine_categories_m->get_group_options();
		foreach($fine_category_options as $fine_category_id => $fine_category_name):
			$arr[$fine_category_id] = 0;
		endforeach;
		$this->db->select(
			array(
				$this->dx('amount').' as amount ',
				$this->dx('fine_category_to_id').' as fine_category_to_id ',
				$this->dx('member_id').' as member_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "28" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('fine_category_to_id').' > "0"',NULL,FALSE);
        $this->db->group_by(array($this->dx("member_id"),$this->dx("fine_category_to_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->fine_category_to_id){
				$arr[$row->fine_category_to_id] = $row->amount;
			}
		}
		return $arr;
	}


	function get_group_member_contribution_transfers_from_contribution_to_fine_category_contribution_id_as_key_array($member_id = 0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		foreach($contribution_options as $contribution_id => $name):
			$arr[$contribution_id] = 0;
		endforeach;
		$this->db->select(
			array(
				$this->dx('amount').' as amount ',
				$this->dx('contribution_to_id').' as contribution_to_id ',
				$this->dx('member_id').' as member_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "28" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('contribution_to_id').' > "0"',NULL,FALSE);
        $this->db->group_by(array($this->dx("member_id"),$this->dx("contribution_to_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_to_id){
				$arr[$row->contribution_to_id] = $row->amount;
			}
		}
		return $arr;
	}

	function get_group_member_total_contribution_transfers_from_contribution_to_fine_category($member_id = 0,$group_id = 0){
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "27" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
        $this->db->group_by(array($this->dx("member_id")));
        $this->db->limit(1);
		$result = $this->db->get('statements')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_total_contribution_transfers_from_contribution_to_fine_category($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as amount ',
			)
		);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "27" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'" ',NULL,FALSE);
		}
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'" ',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date').' <="'.$to.'" ',NULL,FALSE);
		}
        $this->db->limit(1);
		$result = $this->db->get('statements')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_total_contribution_transfers_from_contribution_to_fine_category_per_year($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as amount ',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
			)
		);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "27" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'" ',NULL,FALSE);
		}
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'" ',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date').' <="'.$to.'" ',NULL,FALSE);
		}
        $this->db->group_by(
			array(
				'year'
			)
		);
		return $this->db->get('statements')->result();
	}

	function get_group_member_total_contribution_transfers_from_loan_to_contribution($member_id = 0,$group_id = 0){
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "26" ',NULL,FALSE);
		$this->db->where($this->dx('contribution_from_id').' = "loan" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' = "'.$this->member->id.'"',NULL,FALSE);
		}
        $this->db->group_by(array($this->dx("member_id")));
        $this->db->limit(1);
		$result = $this->db->get('statements')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_total_contribution_transfers_from_loan_to_contribution($group_id = 0){
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "26" ',NULL,FALSE);
		$this->db->where($this->dx('contribution_from_id').' = "loan" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->limit(1);
		$result = $this->db->get('statements')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_member_total_contribution_transfers_from_loan_to_fine($member_id = 0){
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "28" ',NULL,FALSE);
		$this->db->where($this->dx('contribution_from_id').' = "loan" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
        $this->db->group_by(array($this->dx("member_id")));
        $this->db->limit(1);
		$result = $this->db->get('statements')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_member_total_contribution_transfers_to_ignore_per_contribution_array($group_id = 0,$to = 0,$from = 0,$group_member_id = 0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		if($group_member_id){
			foreach($contribution_options as $contribution_id => $contribution_name){
				$arr[$group_member_id][$contribution_id] = 0;
			}
		}else{
			foreach ($this->group_member_options as $member_id => $name){
				foreach($contribution_options as $contribution_id => $contribution_name){
					$arr[$member_id][$contribution_id] = 0;
				}
			}
		}
		$this->db->select(
			array(
				"sum(".$this->dx('amount').') as amount ',
				$this->dx('contribution_from_id').' as contribution_from_id ',
				$this->dx('contribution_id').' as contribution_id ',
				$this->dx('member_id').' as member_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "26" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		if($group_member_id){
			$this->db->where($this->dx('member_id').' = "'.$group_member_id.'"',NULL,FALSE);
		}
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->group_by(array($this->dx("member_id"),$this->dx("contribution_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$contribution = $this->contributions_m->get_group_contribution($row->contribution_from_id);
				if($contribution->display_contribution_arrears_cumulatively){
					$arr[$row->member_id][$row->contribution_id] = $row->amount;
				}else{
					$arr[$row->member_id][$row->contribution_id] = 0;
				}
			}
		}
		return $arr;
	}

	function get_group_total_contribution_transfers_to_ignore_per_contribution_array($group_id = 0,$to = 0,$from = 0,$contribution_id_list = ""){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		
		foreach($contribution_options as $contribution_id => $contribution_name){
			$arr[$contribution_id] = 0;
		}
		
		$this->db->select(
			array(
				"sum(".$this->dx('amount').') as amount ',
				$this->dx('contribution_from_id').' as contribution_from_id ',
				$this->dx('contribution_id').' as contribution_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "26" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id')." IN (".$contribution_id_list.") ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('contribution_id')." IN (0) ",NULL,FALSE);
		}
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->group_by(array($this->dx("contribution_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$contribution = $this->contributions_m->get_group_contribution($row->contribution_from_id);
				if($contribution->display_contribution_arrears_cumulatively){
					$arr[$row->contribution_id] = $row->amount;
				}else{
					$arr[$row->contribution_id] = 0;
				}
			}
		}
		return $arr;
	}

	function get_group_total_contribution_transfers_to_ignore_per_contribution_per_year_array($group_id = 0,$start_year = 2000,$end_year = 2039){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		foreach($contribution_options as $contribution_id => $contribution_name){
			for($i = $start_year; $i < $end_year; $i++):
				$arr[$contribution_id][$i] = 0;
			endfor;
		}
		$this->db->select(
			array(
				"SUM(".$this->dx('amount').') as amount ',
				$this->dx('contribution_from_id').' as contribution_from_id ',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
				$this->dx('contribution_id').' as contribution_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "26" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
        $this->db->group_by(
        	array(
        		$this->dx("contribution_id"),
        		'year'
        	)
        );
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$contribution = $this->contributions_m->get_group_contribution($row->contribution_from_id);
				if($contribution->display_contribution_arrears_cumulatively){
					$arr[$row->contribution_id][$row->year] = $row->amount;
				}else{
					$arr[$row->contribution_id][$row->year] = 0;
				}
			}
		}
		return $arr;
	}

	function get_group_member_total_contribution_transfers_to_ignore_array($group_id = 0,$to = 0,$from = 0){
		$arr = array();
	
		foreach ($this->group_member_options as $member_id => $name){
			$arr[$member_id] = 0;
		}
		
		$this->db->select(
			array(
				"sum(".$this->dx('amount').') as amount ',
				$this->dx('contribution_from_id').' as contribution_from_id ',
				$this->dx('contribution_id').' as contribution_id ',
				$this->dx('member_id').' as member_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "26" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->group_by(array($this->dx("member_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$contribution = $this->contributions_m->get_group_contribution($row->contribution_from_id);
				if($contribution->display_contribution_arrears_cumulatively){
					$arr[$row->member_id] = $row->amount;
				}else{
					$arr[$row->member_id] = 0;
				}
			}
		}
		return $arr;
	}

	function get_group_member_total_contribution_transfers_from_ignore_per_contribution_array($group_id = 0,$to = 0,$from = 0,$group_member_id = 0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		if($group_member_id){
			foreach($contribution_options as $contribution_id => $contribution_name){
				$arr[$group_member_id][$contribution_id] = 0;
			}
		}else{
			foreach ($this->group_member_options as $member_id => $name){
				foreach($contribution_options as $contribution_id => $contribution_name){
					$arr[$member_id][$contribution_id] = 0;
				}
			}
		}
		$this->db->select(
			array(
				$this->dx('amount').' as amount ',
				//$this->dx('contribution_from_id').' as contribution_from_id ',
				$this->dx('contribution_id').' as contribution_id ',
				$this->dx('member_id').' as member_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "25" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		if($group_member_id){
			$this->db->where($this->dx('member_id').' = "'.$group_member_id.'"',NULL,FALSE);
		}
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->group_by(array($this->dx("member_id"),$this->dx("contribution_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$contribution = $this->contributions_m->get_group_contribution($row->contribution_id);
				if($contribution->display_contribution_arrears_cumulatively){
					$arr[$row->member_id][$row->contribution_id] = $row->amount;
				}else{
					$arr[$row->member_id][$row->contribution_id] = 0;
				}
			}
		}
		return $arr;
	}

	function get_group_total_contribution_transfers_from_ignore_per_contribution_array($group_id = 0,$to = 0,$from = 0,$contribution_id_list = ""){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		foreach($contribution_options as $contribution_id => $contribution_name){
			$arr[$contribution_id] = 0;
		}
		
		$this->db->select(
			array(
				$this->dx('amount').' as amount ',
				//$this->dx('contribution_from_id').' as contribution_from_id ',
				$this->dx('contribution_id').' as contribution_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "25" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        if($contribution_id_list){
			$this->db->where($this->dx('contribution_id')." IN (".$contribution_id_list.") ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('contribution_id')." IN (0) ",NULL,FALSE);
		}
        $this->db->group_by(array($this->dx("contribution_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$contribution = $this->contributions_m->get_group_contribution($row->contribution_id);
				if($contribution->display_contribution_arrears_cumulatively){
					$arr[$row->contribution_id] = $row->amount;
				}else{
					$arr[$row->contribution_id] = 0;
				}
			}
		}
		return $arr;
	}

	function get_group_total_contribution_transfers_from_ignore_per_contribution_per_year_array($group_id = 0,$start_year = 2000,$end_year = 2039){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		foreach($contribution_options as $contribution_id => $contribution_name){
			for($i = $start_year; $i < $end_year; $i++):
				$arr[$contribution_id][$i] = 0;
			endfor;
		}
		
		$this->db->select(
			array(
				$this->dx('amount').' as amount ',
				//$this->dx('contribution_from_id').' as contribution_from_id ',
				$this->dx('contribution_id').' as contribution_id ',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "25" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
        $this->db->group_by(
        	array(
        		$this->dx("contribution_id"),
        		'year'
        	)
        );
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$contribution = $this->contributions_m->get_group_contribution($row->contribution_id);
				if($contribution->display_contribution_arrears_cumulatively){
					$arr[$row->contribution_id][$row->year] = $row->amount;
				}else{
					$arr[$row->contribution_id][$row->year] = 0;
				}
			}
		}
		return $arr;
	}

    function get_group_member_total_contribution_transfers_from_per_contribution_array($group_id = 0,$to = 0,$from = 0,$group_member_id = 0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		if($group_member_id){
			foreach($contribution_options as $contribution_id => $contribution_name){
				$arr[$group_member_id][$contribution_id] = 0;
			}
		}else{
			foreach ($this->group_member_options as $member_id => $name){
				foreach($contribution_options as $contribution_id => $contribution_name){
					$arr[$member_id][$contribution_id] = 0;
				}
			}
		}
		$this->db->select(
			array(
				"sum(".$this->dx('amount').') as amount ',
				$this->dx('contribution_id').' as contribution_id ',
				$this->dx('member_id').' as member_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "25" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		if($group_member_id){
			$this->db->where($this->dx('member_id').' = "'.$group_member_id.'"',NULL,FALSE);
		}
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->group_by(array($this->dx("member_id"),$this->dx("contribution_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			$arr[$row->member_id][$row->contribution_id] = $row->amount;
		}
		return $arr;
	}

    function get_group_total_contribution_transfers_from_per_contribution_array($group_id = 0,$to = 0,$from = 0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();

		foreach($contribution_options as $contribution_id => $contribution_name){
			$arr[$contribution_id] = 0;
		}
		$this->db->select(
			array(
				"sum(".$this->dx('amount').') as amount ',
				$this->dx('contribution_id').' as contribution_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "25" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->group_by(array($this->dx("contribution_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			$arr[$row->contribution_id] = $row->amount;
		}
		return $arr;
	}

	function get_group_total_contribution_transfers_from_per_member_array($group_id = 0,$to = 0,$from = 0,$group_member_options=array()){
		$arr = array();
		if($group_member_options){

		}elseif(isset($this->group_member_options)){
			$group_member_options = $this->group_member_options;
		}
		foreach ($group_member_options as $member_id => $name){
				$arr[$member_id] = 0;
		}
		$this->db->select(
			array(
				"sum(".$this->dx('amount').') as amount ',
				$this->dx('member_id').' as member_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "25" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->group_by(array($this->dx("member_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			$arr[$row->member_id] = $row->amount;
		}
		return $arr;
	}

	function get_group_total_contribution_transfers_to_fines($group_id = 0,$from = 0,$to = 0){
		$this->db->select(array('sum('.$this->dx('amount').') as amount'));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "28" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->group_by(array($this->dx("group_id")));
        $this->db->limit(1);
		$result = $this->db->get('statements')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_total_transfers_array($group_id = 0, $from=0,$to=0){
		$this->db->select(array(
			'sum('.$this->dx('amount').') as amount',
			$this->dx('transaction_type').' as transaction_type',
			$this->dx('contribution_from_id').' as contribution_from_id',

		));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' IN (28,30,26) ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->group_by(array($this->dx("transaction_type",$this->dx("group_id"))));
		$results = $this->db->get('statements')->result();
		return $results;
	}


	function get_group_total_contribution_transfers_to_fines_per_year($group_id = 0){
		//$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "28" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
        $this->db->group_by(
        	array(
        		'year'
        	)
        );
		return $statements = $this->db->get('statements')->result();
	}

	function get_group_member_total_contribution_transfers_to_fines_array($group_id = 0,$to = 0,$from = 0,$member_id = 0){
		$arr = array();
		foreach ($this->group_member_options as $member_id => $name){
			$arr[$member_id] = 0;
		}
		$this->db->select(array('sum('.$this->dx('amount').') as amount',$this->dx('member_id').' as member_id'));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('contribution_from_id')." != 'loan' ",NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "28" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->limit($this->group->size);
        $this->db->group_by(array($this->dx("member_id")));
		$result = $this->db->get('statements')->result();
		foreach ($result as $row) {
			# code...
			$arr[$row->member_id] = $row->amount;
		}
		return $arr;
	}

	function get_group_member_total_contribution_transfers_to_fines_per_fine_category_array($group_id = 0,$to = 0,$from = 0,$member_id = 0){
		$arr = array();
		$fine_category_options = $this->fine_categories_m->get_group_options();
		if($member_id){
			foreach($fine_category_options as $fine_category_id => $name):
				$arr[$member_id][$fine_category_id] = 0;
			endforeach;
		}else{
			foreach($fine_category_options as $fine_category_id => $name):
				foreach ($this->group_member_options as $member_id => $name){
					$arr[$member_id][$fine_category_id] = 0;
				}
			endforeach;
		}
		
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('member_id').' as member_id',
				$this->dx('fine_category_id').' as fine_category_id',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('contribution_from_id')." != 'loan' ",NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "28" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		if($member_id){
			$this->db->where($this->dx('member_id')." = '".$member_id."' ",NULL,FALSE);
		}
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->limit($this->group->size);
        $this->db->group_by(array($this->dx("member_id"),$this->dx("fine_category_id")));
		$result = $this->db->get('statements')->result();
		foreach ($result as $row) {
			# code...
			$arr[$row->member_id][$row->fine_category_id] = $row->amount;
		}
		return $arr;
	}

	function get_group_member_total_contribution_transfers_to_fines_per_member_array($group_id = 0,$to = 0,$from = 0,$member_id=0,$group_member_options = array()){
		$arr = array();
		if($group_member_options){

		}else{
			$group_member_options = $this->group_member_options;
		}
		foreach ($group_member_options as $member_id => $name){
			$arr[$member_id] = 0;
		}
		
		
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('member_id').' as member_id',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('contribution_from_id')." != 'loan' ",NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "28" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('member_id')." = '".$member_id."' ",NULL,FALSE);
		}
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->limit(count($group_member_options));
        $this->db->group_by(array($this->dx("member_id")));
		$result = $this->db->get('statements')->result();
		foreach ($result as $row) {
			# code...
			$arr[$row->member_id] = $row->amount;
		}
		return $arr;
	}

	function get_group_member_total_contribution_transfers_to_contribution_fines_per_contribution_array($group_id = 0,$to = 0,$from = 0,$member_id = 0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		if($member_id){
			foreach($contribution_options as $contribution_id => $name):
				$arr[$member_id][$contribution_id] = 0;
			endforeach;
		}else{
			foreach($contribution_options as $contribution_id => $name):
				foreach ($this->group_member_options as $member_id => $name){
					$arr[$member_id][$contribution_id] = 0;
				}
			endforeach;
		}

		//print_r($contribution_options);
		//die;
		
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('member_id').' as member_id',
				$this->dx('contribution_id').' as contribution_id',
				$this->dx('contribution_from_id').' as contribution_from_id',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('contribution_from_id')." != 'loan' ",NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "28" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		if($member_id){
			$this->db->where($this->dx('member_id')." = '".$member_id."' ",NULL,FALSE);
		}
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->limit($this->group->size);
        $this->db->group_by(array($this->dx("member_id"),$this->dx("contribution_from_id")));
		$result = $this->db->get('statements')->result();
		foreach ($result as $row) {
			# code...
			$arr[$row->member_id][$row->contribution_from_id] = $row->amount;
		}
		return $arr;
	}

	function get_group_member_total_contribution_transfers_from_loans_array($group_id = 0,$to = 0,$from = 0){
		$arr = array();
		foreach ($this->group_member_options as $member_id => $name){
			$arr[$member_id] = 0;
		}
		$this->db->select(array('sum('.$this->dx('amount').') as amount',$this->dx('member_id').' as member_id'));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('contribution_from_id').' = "loan"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "26" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->limit($this->group->size);
        $this->db->group_by(array($this->dx("member_id")));
		$result = $this->db->get('statements')->result();
		foreach ($result as $row) {
			# code...
			$arr[$row->member_id] = $row->amount;
		}
		return $arr;
	}	

	function get_group_member_total_contribution_transfers_from_loans_to_fine_array($group_id = 0,$to = 0,$from = 0){
		$arr = array();
		foreach ($this->group_member_options as $member_id => $name){
			$arr[$member_id] = 0;
		}
		$this->db->select(array('sum('.$this->dx('amount').') as amount',$this->dx('member_id').' as member_id'));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('contribution_from_id').' = "loan"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "28" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->limit($this->group->size);
        $this->db->group_by(array($this->dx("member_id")));
		$result = $this->db->get('statements')->result();
		foreach ($result as $row) {
			# code...
			$arr[$row->member_id] = $row->amount;
		}
		return $arr;
	}

	function get_all(){
		$this->select_all_secure('statements');
		return $this->db->get('statements')->result();
	}

	function void_contribution_statements($contribution_id = ''){
		$where = " ".$this->dx('contribution_id')." = '".$contribution_id."';";
		$input = array(
			'active' => 0,
			'modified_on' => time(),
		);
		return $this->update_secure_where($where,'statements',$input);
	}

	function check_if_contribution_has_transactions($contribution_id = 0,$group_id = 0){
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $this->db->count_all_results('statements');
	}

	function get_group_member_total_contribution_transfers_to_loan_array($group_id = 0,$to = 0,$from = 0){
		$arr = array();
		foreach ($this->group_member_options as $member_id => $name){
			$arr[$member_id] = 0;
		}
		$this->db->select(array('sum('.$this->dx('amount').') as amount',$this->dx('member_id').' as member_id'));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "30" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		if($to){
            $this->db->where($this->dx("transaction_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->group_by(array($this->dx("member_id")));
		$result = $this->db->get('statements')->result();
		foreach ($result as $row) {
			# code...
			$arr[$row->member_id] = $row->amount;
		}
		return $arr;
	}


	function get_group_total_contribution_transfers_to_loans($group_id = 0){
		$this->db->select(array('sum('.$this->dx('amount').') as amount'));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "30" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
        $this->db->group_by(array($this->dx("group_id")));
        $this->db->limit(1);
		$result = $this->db->get('statements')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_member_total_contribution_transfers_to_loan($member_id = 0,$group_id = 0){
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "30" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' = "'.$this->member->id.'"',NULL,FALSE);
		}
        $this->db->group_by(array($this->dx("member_id")));
        $this->db->limit(1);
		$result = $this->db->get('statements')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_member_contribution_transfers_to_loan_per_contribution_array($member_id = 0,$group_id=0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options($group_id);
		foreach($contribution_options as $contribution_id => $contribution_name):
			$arr[$contribution_id] = 0;
		endforeach;
		$this->db->select(
			array(
				"sum(".$this->dx('amount').') as amount ',
				$this->dx('contribution_id').' as contribution_id ',
				$this->dx('member_id').' as member_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "30" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
        $this->db->group_by(array($this->dx("member_id"),$this->dx("contribution_id")));
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$arr[$row->contribution_id] = $row->amount;
			}
		}
		return $arr;
	}


	function update_group_back_dating_statement_entries_cut_off_date($group_id = 0,$input = array()){
		$where = " ".$this->dx('is_a_back_dating_record')." = '1' AND ".$this->dx('group_id')." = '".$group_id."' AND ".$this->dx('active')." = '1' ;";
        if($this->update_where($where,$input)){
        	return TRUE;
        }else{
        	return FALSE;
        }
	}

	function get_group_member_total_contribution_transfers_from_contribution($member_id = 0,$group_id = 0){
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "25" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
        $this->db->group_by(array($this->dx("member_id")));
		$result = $this->db->get('statements')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_member_total_contribution_transfers_to_contribution($member_id = 0,$group_id=0){
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "26" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
        $this->db->group_by(array($this->dx("member_id")));
		$result = $this->db->get('statements')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}


	function get_member_deposits_opening_balance_by_contribution_array($group_id = 0,$member_id = 0,$contribution_id_list = '',$date = 0,$from = 0){
		$arr = array();
		if($date){
			$date -=24*60*60; 
		}
		$this->select(
			array(
				' sum('.$this->dx('amount').') as amount ',
				$this->dx('contribution_id').' as contribution_id '
			)
		);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
        $this->db->where($this->dx('transaction_type').' IN (1,21,22,23,24,27)',NULL,FALSE);
		//$this->db->where('('.$this->dx('transaction_type').' = "1" OR '.$this->dx('transaction_type').' ="21" OR '.$this->dx('transaction_type').' ="22" OR '.$this->dx('transaction_type').' ="23" OR '.$this->dx('transaction_type').' ="24" OR '.$this->dx('transaction_type').' ="27" )',NULL,FALSE);
		if($contribution_id_list){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }elseif($contribution_id_list=='0'){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
            $this->db->where($this->dx("transaction_date") . " <= " .$date, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->group_by(
        	array(
        		$this->dx('contribution_id'),
        	)
        );
        $payables = $this->db->get('statements')->result();
        

        $this->select(
        	array(
        		' sum('.$this->dx('amount').') as amount ',
        		$this->dx('contribution_id').' as contribution_id '
    		)
    	);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
        $this->db->where($this->dx('transaction_type').' IN (9,10,11,15)',NULL,FALSE);
		//$this->db->where('('.$this->dx('transaction_type').' = "9" OR '.$this->dx('transaction_type').' = "10" OR '.$this->dx('transaction_type').' = "11" OR '.$this->dx('transaction_type').' = "15" )',NULL,FALSE);
		if($contribution_id_list){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }elseif($contribution_id_list=='0'){
        	$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
            $this->db->where($this->dx("transaction_date") . " <= " .$date, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->group_by(
        	array(
        		$this->dx('contribution_id'),
        	)
        );
        $paids = $this->db->get('statements')->result();
        //print_r($paids);
        
        foreach($paids as $paid):
        	$arr[$paid->contribution_id] = 0; 
        endforeach;

        foreach($payables as $payable):
        	$arr[$payable->contribution_id] = 0; 
        endforeach;

        foreach($paids as $paid):
        	$arr[$paid->contribution_id] += $paid->amount; 
        endforeach;

        foreach($payables as $payable):
        	//$arr[$payable->contribution_id] -= $payable->amount; 
        endforeach;

        return $arr;
        
	}


	function get_member_deposit_statement($id = 0,$contribution_id_list = '',$from = 0,$to = 0,$group_id = 0){
		$arr = array();
		$this->select_all_secure('statements');
		$this->db->where($this->dx('statements.member_id').' = "'.$id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('statements.group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('statements.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('statements.active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('statements.transaction_type').' IN (9,10,11,15,21,22,23,24,25,26,27,30)',NULL,FALSE);
		if($contribution_id_list){
        	$this->db->where($this->dx('statements.contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
        }
		if($to){
			$this->db->where($this->dx('statements.transaction_date').' <= "'.$to.'"',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('statements.transaction_date').' >= "'.$from.'"',NULL,FALSE);
		}
        $this->db->order_by($this->dx('statements.transaction_date'), 'ASC', FALSE);
		$result = $this->db->get('statements')->result();
		foreach($result as $row):
			$arr[$row->contribution_id][] = $row;
		endforeach;
		return $arr;
	}

	/*****contibution member balances******/
	function insert_batch_member_contributions_fines_balances($input,$skip_validation=FALSE){
        return $this->insert_batch_secure_data('member_contributions_fines_balances',$input);
    }

	function delete_existing_contibutions_fines_balances($group_id=0,$member_id=0){
		if($member_id){
			$member_statement = " AND ".$this->dx("member_id")." ='".$member_id."'";
		}else{
			$member_statement = '';
		}
		return $this ->db-> query("delete from member_contributions_fines_balances  
                where ".$this->dx("group_id")." ='".$group_id."'".$member_statement); 
	}

	function get_group_member_contributions_fines_balances($group_id=0,$member_id=0){
		$this->select_all_secure('member_contributions_fines_balances');
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		return $this->db->get('member_contributions_fines_balances')->row();
	}

	function count_group_contributions_and_fines($group_id=0){
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		return $this->db->count_all_results('member_contributions_fines_balances');
	}

	function get_group_contributions_fines_balances($group_id = 0){
		$this->db->select(array(
			'SUM('.$this->dx('contributions_amount_paid').') as contributions_amount_paid',
			'SUM('.$this->dx('fines_amount_paid').') as fines_amount_paid',
			'COUNT(*) as total_counts'
		));
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		return $this->db->get('member_contributions_fines_balances')->row();
	}

	function get_group_contributions_and_fines(){
		//$this->select_all_secure('member_contributions_fines_balances');
		$this->db->select(array(
			$this->dx('group_id').' as group_id',
		));
		$this->db->group_by(array($this->dx("group_id")));
		return $this->db->get('member_contributions_fines_balances')->result();
	}

	function get_group_member_balance_payments($group_id=0,$group_member_options = array()){
		$list = '0';
		if($group_member_options){
			foreach ($group_member_options as $member_id => $member_name) {
				if($list){
					$list.=','.$member_id;
				}else{
					$list = $member_id;
				}
			}
		}
		$this->select_all_secure('member_contributions_fines_balances');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('member_id').' IN('.$list.')',NULL,FALSE);
		$results =  $this->db->get('member_contributions_fines_balances')->result();
		$arr = array();
		foreach ($results as $result) {
			$arr[$result->member_id] = array(
				'contributions_amount_payable' => $result->contributions_amount_payable,
				'contributions_amount_paid' => $result->contributions_amount_paid,
				'contributions_balance' => $result->contributions_balance,
				'fines_amount_payable' => $result->fines_amount_payable,
				'fines_amount_paid' => $result->fines_amount_paid,
				'fines_balance' => $result->fines_balance,
			);
		}
		return $arr;
	}

	/*function get_group_member_contribution_statements($group_ids = array(),$member_ids = array()){
		$this->select_all_secure('statements');
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id').' IN ( 0 ) ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' IN ( '.implode(",",$group_ids)." ) ",NULL,FALSE);
		}
		if(empty($member_ids)){
			$this->db->where($this->dx('member_id').' IN ( 0 ) ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' IN ( '.implode(",",array_filter($member_ids))." ) ",NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);      
		if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		//$this->db->where($this->dx('transaction_type').' IN (1,2) ',NULL,FALSE);
        $this->db->order_by($this->dx('transaction_date'), 'ASC', FALSE);
        $this->db->order_by('id','ASC',FALSE);
        return $this->db->get('statements')->result();
	}*/

	function get_group_member_contribution_statements($group_ids = array(),$member_ids = array(),$date = 0,$contribution_ids=''){
		$this->select_all_secure('statements');
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id').' IN ( 0 ) ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' IN ( '.implode(",",$group_ids)." ) ",NULL,FALSE);
		}
		if(empty(array_filter($member_ids))){
			$this->db->where($this->dx('member_id').' IN ( 0 ) ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' IN ( '.implode(",",array_filter($member_ids))." ) ",NULL,FALSE);
		}
		if($contribution_ids){
			$this->db->where($this->dx('contribution_id').' IN ( '.implode(",",array_filter($contribution_ids))." ) ",NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);      
		if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}

		if($date){
			$date -= (86400*30);
			$date = strtotime(date('d-m-Y',$date));
			$this->db->where($this->dx('transaction_date').' >= '.$date .' ',NULL,FALSE);
		}
		//$this->db->where($this->dx('transaction_type').' IN (1,2) ',NULL,FALSE);
        $this->db->order_by($this->dx('transaction_date'), 'ASC', FALSE);
        $this->db->order_by('id','ASC',FALSE);
        return $this->db->get('statements')->result();
	}

	// function get_group_member_new_contribution_statements_array($old_statements_ids = array()){
	// 	$this->select_all_secure('statements');
	// 	if(empty($old_statements_ids)){
	// 		$this->db->where($this->dx('old_statement_id').' IN ( 0 ) ',NULL,FALSE);
	// 	}else{
	// 		$this->db->where($this->dx('old_statement_id').' IN ( '.implode(",",$old_statements_ids)." ) ",NULL,FALSE);
	// 	}
	// 	$this->db->where($this->dx('active').' = "1"',NULL,FALSE);  
	// 	//$this->db->where($this->dx('transaction_type').' IN (1,2) ',NULL,FALSE);
 //        $this->db->order_by($this->dx('transaction_date'), 'ASC', FALSE);
 //        $this->db->order_by('id','ASC',FALSE);
 //        return $this->db->get('statements')->result();
	// }

	function get_group_member_new_contribution_statements_array($group_ids = array(),$old_statements_ids = array()){
		$this->select_all_secure('statements');
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id').' IN ( 0 ) ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' IN ( '.implode(",",array_unique($group_ids))." ) ",NULL,FALSE);
		}
		if(empty($old_statements_ids)){
			$this->db->where($this->dx('old_statement_id').' IN ( 0 ) ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('old_statement_id').' IN ( '.implode(",",$old_statements_ids)." ) ",NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);  
        return $this->db->get('statements')->result();
	}

	function get_group_member_fine_statements($group_ids = array(),$member_ids = array()){
		$this->select_all_secure('statements');
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id').' IN ( 0 ) ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' IN ( '.implode(",",$group_ids)." ) ",NULL,FALSE);
		}
		if(empty($member_ids)){
			$this->db->where($this->dx('member_id').' IN ( 0 ) ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' IN ( '.implode(",",$member_ids)." ) ",NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);      
		if(empty($this->transactions->fine_payable_transaction_types_array)||empty($this->transactions->fine_paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->fine_payable_transaction_types_array,$this->transactions->fine_paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		//$this->db->where($this->dx('transaction_type').' IN (1,2) ',NULL,FALSE);
        $this->db->order_by($this->dx('transaction_date'), 'ASC', FALSE);
        $this->db->order_by('id','ASC',FALSE);
        return $this->db->get('statements')->result();
	}

	function void_group_member_contribution_statements($group_ids = array(),$member_ids = array()){
        $input = array(
            'active' => 0,
            'modified_on' => time()
        );
        if(empty($group_ids)){
            $group_id_list = "0";
        }else{
            $group_id_list = implode(",",$group_ids);
        }
        if(empty($member_ids)){
            $member_id_list = "0";
        }else{
            $member_id_list = implode(",",$member_ids);
        }
        if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$transaction_type_list = '0';
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$transaction_type_list = implode(",",$transaction_types_array);
		}
        $where = " ".$this->dx('group_id')." IN (".$group_id_list.") AND ".$this->dx('member_id')." IN (".$member_id_list.") AND ".$this->dx('active')." = 1  AND ".$this->dx('transaction_type')." IN (".$transaction_type_list.") ";
        return $this->update_secure_where($where,'statements',$input);
    }

  //   function void_contribution_statements_by_ids_array($ids_array = array()){
  //   	$input = array(
  //           'active' => 0,
  //           'modified_on' => time()
  //       );
  //       if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
		// 	$transaction_type_list = '0';
		// }else{
		// 	$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
		// 	$transaction_type_list = implode(",",$transaction_types_array);
		// }
		//  if(empty($ids_array)){
  //           $id_list = "0";
  //       }else{
  //           $id_list = implode(",",$ids_array);
  //       }
  //       $where = " id IN (".$id_list.") AND ".$this->dx('active')." = 1  AND ".$this->dx('transaction_type')." IN (".$transaction_type_list.") ";
  //       return $this->update_secure_where($where,'statements',$input);
  //   }

    function void_contribution_statements_by_ids_array($ids_array = array(),$group_ids = array()){
    	$input = array(
            'active' => 0,
            'modified_on' => time()
        );
        if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$transaction_type_list = '0';
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$transaction_type_list = implode(",",$transaction_types_array);
		}
		if(empty($ids_array)){
            $id_list = "0";
        }else{
            $id_list = implode(",",$ids_array);
        }
        if(empty($group_ids)){
            $group_id_list = "0";
        }else{
            $group_id_list = implode(",",$group_ids);
        }
        $where = " id IN (".$id_list.") AND ".$this->dx('group_id')." IN (".$group_id_list.") AND ".$this->dx('active')." = 1  AND ".$this->dx('transaction_type')." IN (".$transaction_type_list.") ";
        return $this->update_secure_where($where,'statements',$input);
    }

    function get_group_member_contribution_statement_ids_array($group_ids = array(),$member_ids = array()){
    	$arr = array();
    	$this->db->select(
    		array(
    			'id'
    		)
    	);
    	if(empty($group_ids)){
            $group_id_list = "0";
        }else{
            $group_id_list = implode(",",$group_ids);
        }
        $this->db->where($this->dx('group_id')." IN (".$group_id_list.") ",NULL,FALSE);
        if(empty($member_ids)){
            $member_id_list = "0";
        }else{
            $member_id_list = implode(",",$member_ids);
        }
        $this->db->where($this->dx('member_id')." IN (".$member_id_list.") ",NULL,FALSE);
        if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$transaction_type_list = '0';
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$transaction_type_list = implode(",",$transaction_types_array);
		}
        $this->db->where($this->dx('transaction_type')." IN (".$transaction_type_list.") ",NULL,FALSE);
        $this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
        $statements = $this->db->get('statements')->result();
        foreach($statements as $statement):
        	$arr[] = $statement->id;
        endforeach;
        return $arr;
    }

    function void_group_member_fine_statements($group_ids = array(),$member_ids = array()){
        $input = array(
            'active' => 0,
            'modified_on' => time()
        );
        if(empty($group_ids)){
            $group_id_list = "0";
        }else{
            $group_id_list = implode(",",$group_ids);
        }
        if(empty($member_ids)){
            $member_id_list = "0";
        }else{
            $member_id_list = implode(",",$member_ids);
        }
        if(empty($this->transactions->fine_payable_transaction_types_array)||empty($this->transactions->fine_paid_transaction_types_array)){
			$transaction_type_list = '0';
		}else{
			$transaction_types_array = array_merge($this->transactions->fine_payable_transaction_types_array,$this->transactions->fine_paid_transaction_types_array);
			$transaction_type_list = implode(",",$transaction_types_array);
		}
        $where = " ".$this->dx('group_id')." IN (".$group_id_list.") AND ".$this->dx('member_id')." IN (".$member_id_list.") AND ".$this->dx('active')." = 1  AND ".$this->dx('transaction_type')." IN (".$transaction_type_list.") ";
        return $this->update_secure_where($where,'statements',$input);
    }

    function void_fine_statements_by_ids_array($ids_array = array()){
    	$input = array(
            'active' => 0,
            'modified_on' => time()
        );
        if(empty($this->transactions->fine_payable_transaction_types_array)||empty($this->transactions->fine_paid_transaction_types_array)){
			$transaction_type_list = '0';
		}else{
			$transaction_types_array = array_merge($this->transactions->fine_payable_transaction_types_array,$this->transactions->fine_paid_transaction_types_array);
			$transaction_type_list = implode(",",$transaction_types_array);
		}
		if(empty($ids_array)){
            $id_list = "0";
        }else{
            $id_list = implode(",",$ids_array);
        }
        $where = " id IN (".$id_list.") AND ".$this->dx('active')." = 1  AND ".$this->dx('transaction_type')." IN (".$transaction_type_list.") ";
        return $this->update_secure_where($where,'statements',$input);
    }

	function void_group_fine_statements_by_ids_array($group_ids = array(),$ids_array = array()){
		$input = array(
            'active' => 0,
            'modified_on' => time()
        );
        if(empty($this->transactions->fine_payable_transaction_types_array)||empty($this->transactions->fine_paid_transaction_types_array)){
			$transaction_type_list = '0';
		}else{
			$transaction_types_array = array_merge($this->transactions->fine_payable_transaction_types_array,$this->transactions->fine_paid_transaction_types_array);
			$transaction_type_list = implode(",",$transaction_types_array);
		}
		if(empty($ids_array)){
            $id_list = "0";
        }else{
            $id_list = implode(",",$ids_array);
        }
		if(empty($group_ids)){
        	$group_id_list = "0";
        }else{
        	$group_id_list = implode(",",$group_ids);        	
        }

        $where = " id IN (".$id_list.") AND ".$this->dx('active')." = 1 AND ".$this->dx('group_id')." IN (".$group_id_list.")  AND ".$this->dx('transaction_type')." IN (".$transaction_type_list.") ";
        return $this->update_secure_where($where,'statements',$input);
	}

	function void_group_miscellaneous_statements_by_ids_array($group_ids = array(),$ids_array = array()){
		$input = array(
            'active' => 0,
            'modified_on' => time()
        );
		$transaction_types_array = array(4,17,18,19,20);
        // if(empty($this->transactions->fine_payable_transaction_types_array)||empty($this->transactions->fine_paid_transaction_types_array)){
		// 	$transaction_type_list = '0';
		// }else{
		// 	$transaction_types_array = array_merge($this->transactions->fine_payable_transaction_types_array,$this->transactions->fine_paid_transaction_types_array);
			$transaction_type_list = implode(",",$transaction_types_array);
		// }
		if(empty($ids_array)){
            $id_list = "0";
        }else{
            $id_list = implode(",",$ids_array);
        }
		if(empty($group_ids)){
        	$group_id_list = "0";
        }else{
        	$group_id_list = implode(",",$group_ids);        	
        }

        $where = " id IN (".$id_list.") AND ".$this->dx('active')." = 1 AND ".$this->dx('group_id')." IN (".$group_id_list.")  AND ".$this->dx('transaction_type')." IN (".$transaction_type_list.") ";
        return $this->update_secure_where($where,'statements',$input);
	}

	function get_group_member_total_cumulative_contribution_arrears_per_member_array($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				" MAX(id) as id "
			)
		);
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->group_by(
        	array(
        		$this->dx('member_id')
        	)
        );
        $this->db->order_by('id','DESC',FALSE);
        $statements = $this->db->get('statements')->result();
        
        $statement_ids_array = array();

        foreach($statements as $statement):
        	$statement_ids_array[] = $statement->id;
        endforeach;
     
		$this->db->select(
			array(
				$this->dx('balance')." as cumulative_balance ",
				$this->dx('member_id')." as member_id "
			)
		);
		if(empty($statement_ids_array)){
			$this->db->where_in('id','0');
		}else{
			$this->db->where_in('id',$statement_ids_array);
		}
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$cumulative_balances = $this->db->get('statements')->result();

		$member_options = $this->members_m->get_active_group_member_options($group_id);

		foreach($member_options as $member_id => $member_name):
			$arr[$member_id] = 0;
		endforeach;

		foreach($cumulative_balances as $cumulative_balance):
			$arr[$cumulative_balance->member_id] = $cumulative_balance->cumulative_balance;
		endforeach;

		return $arr;
	}

	function get_group_member_total_contribution_arrears_per_contribution_per_member_array($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				" MAX(id) as id ",
				$this->dx('contribution_id')." as contribution_id "
			)
		);
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->group_by(
        	array(
        		$this->dx('member_id'),
        		'contribution_id'
        	)
        );
        $this->db->order_by('id','DESC',FALSE);
        $statements = $this->db->get('statements')->result();
        
        $statement_ids_array = array();

        foreach($statements as $statement):
        	$statement_ids_array[] = $statement->id;
        endforeach;
     
		$this->db->select(
			array(
				$this->dx('contribution_balance')." as contribution_balance ",
				$this->dx('member_id')." as member_id ",
				$this->dx('contribution_id')." as contribution_id "
			)
		);
		if(empty($statement_ids_array)){
			$this->db->where_in('id','0');
		}else{
			$this->db->where_in('id',$statement_ids_array);
		}
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$contribution_balances = $this->db->get('statements')->result();

		$member_options = $this->members_m->get_active_group_member_options($group_id);
		$contribution_options = $this->contributions_m->get_group_contribution_options($group_id);

		foreach($member_options as $member_id => $member_name):
			foreach($contribution_options as $contribution_id => $contribution_name):
				$arr[$contribution_id][$member_id] = 0;
			endforeach;
		endforeach;

		foreach($contribution_balances as $contribution_balance):
			$arr[$contribution_balance->contribution_id][$contribution_balance->member_id] = $contribution_balance->contribution_balance;
		endforeach;

		return $arr;
	}

	function get_group_member_total_cumulative_fine_arrears_per_member_array($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				" MAX(id) as id "
			)
		);
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->fine_payable_transaction_types_array)||empty($this->transactions->fine_paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->fine_payable_transaction_types_array,$this->transactions->fine_paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->group_by(
        	array(
        		$this->dx('member_id')
        	)
        );
        $this->db->order_by('id','DESC',FALSE);
        $statements = $this->db->get('statements')->result();
        
        $statement_ids_array = array();

        foreach($statements as $statement):
        	$statement_ids_array[] = $statement->id;
        endforeach;
     
		$this->db->select(
			array(
				$this->dx('balance')." as cumulative_balance ",
				$this->dx('member_id')." as member_id "
			)
		);
		if(empty($statement_ids_array)){
			$this->db->where_in('id','0');
		}else{
			$this->db->where_in('id',$statement_ids_array);
		}
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->fine_payable_transaction_types_array)||empty($this->transactions->fine_paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->fine_payable_transaction_types_array,$this->transactions->fine_paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$cumulative_balances = $this->db->get('statements')->result();

		$member_options = $this->members_m->get_active_group_member_options($group_id);

		foreach($member_options as $member_id => $member_name):
			$arr[$member_id] = 0;
		endforeach;

		foreach($cumulative_balances as $cumulative_balance):
			$arr[$cumulative_balance->member_id] = $cumulative_balance->cumulative_balance;
		endforeach;

		return $arr;
	}

	function get_group_member_total_cumulative_contribution_paid_per_member_array($group_id = 0,$limit=0){
		$arr = array();
		$this->db->select(
			array(
				" MAX(id) as id "
			)
		);
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->paid_transaction_types_array)||empty($this->transactions->payable_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->paid_transaction_types_array,$this->transactions->payable_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->group_by(
        	array(
        		$this->dx('member_id')
        	)
        );
        $this->db->order_by('id','DESC',FALSE);
        $statements = $this->db->get('statements')->result();
        
        $statement_ids_array = array();

        foreach($statements as $statement):
        	$statement_ids_array[] = $statement->id;
        endforeach;

		$this->db->select(
			array(
				$this->dx('cumulative_paid')." as cumulative_paid ",
				$this->dx('member_id')." as member_id "
			)
		);
		if(empty($statement_ids_array)){
			$this->db->where_in('id','0');
		}else{
			$this->db->where_in('id',$statement_ids_array);
		}
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->paid_transaction_types_array)||empty($this->transactions->payable_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->paid_transaction_types_array,$this->transactions->payable_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		
		$cumulative_paids = $this->db->get('statements')->result();

		$member_options = $this->members_m->get_active_group_member_options($group_id);

		foreach($member_options as $member_id => $member_name):
			$arr[$member_id] = 0;
		endforeach;

		foreach($cumulative_paids as $cumulative_paid):
			$arr[$cumulative_paid->member_id] = $cumulative_paid->cumulative_paid;
		endforeach;

		return $arr;
	}

	function get_group_member_total_cumulative_contribution_paid_per_member_array_tests($group_id = 0){
		$arr = array();
		/*$this->db->select(
			array(
				" MAX(id) as id "
			)
		);*/
		$this->select_all_secure('statements');
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->paid_transaction_types_array,array(25));
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->group_by(
        	array(
        		$this->dx('member_id')
        	)
        );
        $this->db->order_by('id','DESC',FALSE);
        $statements = $this->db->get('statements')->result();
        
        $statement_ids_array = array();

        foreach($statements as $statement):
        	$statement_ids_array[] = $statement->id;
        endforeach;

		/*$this->db->select(
			array(
				$this->dx('cumulative_paid')." as cumulative_paid ",
				$this->dx('member_id')." as member_id "
			)
		);*/
		$this->select_all_secure('statements');
		if(empty($statement_ids_array)){
			$this->db->where_in('id','0');
		}else{
			$this->db->where_in('id',$statement_ids_array);
		}
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->paid_transaction_types_array,array(25));
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$cumulative_paids = $this->db->get('statements')->result();

		$member_options = $this->members_m->get_active_group_member_options($group_id);

		foreach($member_options as $member_id => $member_name):
			$arr[$member_id] = 0;
		endforeach;

		foreach($cumulative_paids as $cumulative_paid):
			$arr[$cumulative_paid->member_id] = $cumulative_paid->cumulative_paid;
		endforeach;

		return $cumulative_paids;
	}

	function get_group_member_total_contribution_paid_per_contribution_per_member_array($group_id = 0,$contribution_ids=''){
		$contribution_id_list='';
		if($contribution_ids){
			foreach ($contribution_ids as $id => $name) {
				if($contribution_id_list){
					$contribution_id_list.=','.$id;
				}else{
					$contribution_id_list=$id;
				}
			}
		}
		$arr = array();
		$this->db->select(
			array(
				" MAX(id) as id ",
				$this->dx('contribution_id')." as contribution_id "
			)
		);
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN('.$contribution_id_list.')',NULL,FALSE);
		}
		if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->group_by(
        	array(
        		$this->dx('member_id'),
        		'contribution_id'
        	)
        );
        $this->db->order_by('id','DESC',FALSE);
        $statements = $this->db->get('statements')->result();
        
        $statement_ids_array = array();

        foreach($statements as $statement):
        	$statement_ids_array[] = $statement->id;
        endforeach;
     
		$this->db->select(
			array(
				$this->dx('contribution_paid')." as contribution_paid ",
				$this->dx('member_id')." as member_id ",
				$this->dx('contribution_id')." as contribution_id "
			)
		);
		if(empty($statement_ids_array)){
			$this->db->where_in('id','0');
		}else{
			$this->db->where_in('id',$statement_ids_array);
		}
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN('.$contribution_id_list.')',NULL,FALSE);
		}
		if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$statements = $this->db->get('statements')->result();

		$member_options = $this->members_m->get_active_group_member_options($group_id);
		if($contribution_ids){
			$contribution_options = $contribution_ids;
		}else{
			$contribution_options = $this->contributions_m->get_group_contribution_options($group_id);
		}

		foreach($member_options as $member_id => $member_name):
			foreach($contribution_options as $contribution_id => $contribution_name):
				$arr[$contribution_id][$member_id] = 0;
			endforeach;
		endforeach;

		foreach($statements as $statement):
			$arr[$statement->contribution_id][$statement->member_id] = $statement->contribution_paid;
		endforeach;

		return $arr;
	}

	function get_group_statement_by_invoice_ids_array($group_id=0,$invoice_ids = array()){
		$this->db->select(
			array(
				'id',
				$this->dx('deposit_id')." as deposit_id ",
				$this->dx('transaction_type')." as transaction_type ",
				$this->dx('group_id')." as group_id ",
				$this->dx('member_id')." as member_id ",
				$this->dx('contribution_id')." as contribution_id ",
				$this->dx('transaction_date')." as transaction_date ",

			)
		);
		$this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		$this->db->where($this->dx('group_id')." = ".$group_id." ",NULL,FALSE);
		if(empty(array_filter($invoice_ids))){
			$this->db->where($this->dx('invoice_id')." IN (0) ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('invoice_id')." IN (".implode(',',array_filter($invoice_ids)).") ",NULL,FALSE);
		}
		return $this->db->get('statements')->result();
	}

	function void_group_contribution_statements_by_ids_array($group_ids = array(),$ids_array = array()){
    	$input = array(
            'active' => 0,
            'modified_on' => time()
        );
        if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$transaction_type_list = '0';
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$transaction_type_list = implode(",",$transaction_types_array);
		}
		if(empty($ids_array)){
            $id_list = "0";
        }else{
            $id_list = implode(",",$ids_array);
        }
        if(empty($group_ids)){
        	$group_id_list = "0";
        }else{

        	$group_id_list = implode(",",$group_ids);        	
        }

        $where = " id IN (".$id_list.") AND ".$this->dx('active')." = 1 AND ".$this->dx('group_id')." IN (".$group_id_list.") AND ".$this->dx('transaction_type')." IN (".$transaction_type_list.") ";
        return $this->update_secure_where($where,'statements',$input);
    }

	function get_group_member_total_cumulative_fine_paid_per_member_array($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				" MAX(id) as id "
			)
		);
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = $this->transactions->fine_paid_transaction_types_array;
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->group_by(
        	array(
        		$this->dx('member_id')
        	)
        );
        $this->db->order_by('id','DESC',FALSE);
        $statements = $this->db->get('statements')->result();
        
        $statement_ids_array = array();

        foreach($statements as $statement):
        	$statement_ids_array[] = $statement->id;
        endforeach;

		$this->db->select(
			array(
				$this->dx('cumulative_paid')." as cumulative_paid ",
				$this->dx('member_id')." as member_id "
			)
		);
		if(empty($statement_ids_array)){
			$this->db->where_in('id','0');
		}else{
			$this->db->where_in('id',$statement_ids_array);
		}
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->fine_paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = $this->transactions->fine_paid_transaction_types_array;
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$cumulative_paids = $this->db->get('statements')->result();

		$member_options = $this->members_m->get_active_group_member_options($group_id);

		foreach($member_options as $member_id => $member_name):
			$arr[$member_id] = 0;
		endforeach;

		foreach($cumulative_paids as $cumulative_paid):
			$arr[$cumulative_paid->member_id] = $cumulative_paid->cumulative_paid;
		endforeach;

		return $arr;
	}

	function get_group_member_contribution_balance($group_id = 0,$member_id = 0,$contribution_id = 0,$date = 0,$from = 0){
		$this->db->select(
			array(
				$this->dx('contribution_balance')." as contribution_balance "
			)
		);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
        $this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
            $this->db->where($this->dx("transaction_date") . " <= " .$date, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->limit(1);
        $this->db->order_by('id','DESC',FALSE);
        $row = $this->db->get('statements')->row();
        if($row){
        	return $row->contribution_balance;
        }else{
        	return 0;
        }
	}

	function get_group_member_contribution_fine_balance($group_id = 0,$member_id = 0,$contribution_id = 0,$date = 0,$from = 0){
		$this->db->select(
			array(
				$this->dx('contribution_balance')." as contribution_balance "
			)
		);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		if(empty($this->transactions->fine_payable_transaction_types_array)||empty($this->transactions->fine_paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->fine_payable_transaction_types_array,$this->transactions->fine_paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
        $this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
            $this->db->where($this->dx("transaction_date") . " <= " .$date, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->limit(1);
        $this->db->order_by('id','DESC',FALSE);
        $row = $this->db->get('statements')->row();
        if($row){
        	return $row->contribution_balance;
        }else{
        	return 0;
        }
	}

	function get_group_member_fine_balance($group_id = 0,$member_id = 0,$contribution_id = 0,$date = 0,$from = 0){
		$this->db->select(
			array(
				$this->dx('contribution_balance')." as contribution_balance "
			)
		);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		if(empty($this->transactions->fine_payable_transaction_types_array)||empty($this->transactions->fine_paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->fine_payable_transaction_types_array,$this->transactions->fine_paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
        $this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
            $this->db->where($this->dx("transaction_date") . " <= " .$date, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->limit(1);
        $this->db->order_by('id','DESC',FALSE);
        $row = $this->db->get('statements')->row();
        if($row){
        	return $row->contribution_balance;
        }else{
        	return 0;
        }
	}

	function get_group_member_cumulative_balance($group_id = 0,$member_id = 0,$contribution_id = 0,$date = 0,$from = 0){
		$this->db->select(
			array(
				$this->dx('balance')." as balance "
			)
		);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
            $this->db->where($this->dx("transaction_date") . " <= " .$date, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("transaction_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->limit(1);
        $this->db->order_by('id','DESC',FALSE);
        $row = $this->db->get('statements')->row();
        if($row){
        	return $row->balance;
        }else{
        	return 0;
        }
	}

	function get_cumulative_balances_array($group_ids = array(),$member_ids = array()){
		$arr = array();
		$this->db->select(
			array(
				" MAX(id) as id "
			)
		);
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id')." IN ('0') ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." IN (".implode(',',$group_ids).") ",NULL,FALSE);
		}
		// if(empty($member_ids)){
		// 	$this->db->where($this->dx('member_id')." = '0' ",NULL,FALSE);
		// }else{
		// 	//$this->db->where($this->dx('member_id')." = '".implode(',',$member_ids)."' ",NULL,FALSE);
		// }
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->group_by(
        	array(
        		$this->dx('member_id'),
        		$this->dx('group_id')
        	)
        );
        $this->db->order_by('id','DESC',FALSE);
        $statements = $this->db->get('statements')->result();
        
        $statement_ids_array = array();

        foreach($statements as $statement):
        	$statement_ids_array[] = $statement->id;
        endforeach;

		$this->db->select(
			array(
				$this->dx('balance')." as cumulative_balance ",
				$this->dx('member_id')." as member_id ",
				$this->dx('group_id')." as group_id "
			)
		);
		if(empty($statement_ids_array)){
			$this->db->where_in('id','0');
		}else{
			$this->db->where_in('id',$statement_ids_array);
		}
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id')." = '0' ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." IN (".implode(',',$group_ids).") ",NULL,FALSE);
		}
		// if(empty($member_ids)){
		// 	$this->db->where($this->dx('member_id')." = '0' ",NULL,FALSE);
		// }else{
		// 	//$this->db->where($this->dx('member_id')." = '".implode(',',$member_ids)."' ",NULL,FALSE);
		// }
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$cumulative_balances = $this->db->get('statements')->result();

		foreach($group_ids as $group_id):
			foreach($member_ids as $member_id):
				$arr[$group_id][$member_id] = 0;
			endforeach;
		endforeach;

		foreach($cumulative_balances as $cumulative_balance):
			$arr[$cumulative_balance->group_id][$cumulative_balance->member_id] = $cumulative_balance->cumulative_balance;
		endforeach;

		return $arr;
	}

	function get_cumulative_fine_balances_array($group_ids = array(),$member_ids = array()){
		$arr = array();
		$this->db->select(
			array(
				" MAX(id) as id "
			)
		);
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id')." = '0' ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." = (".implode(',',$group_ids).") ",NULL,FALSE);
		}
		if(empty($member_ids)){
			$this->db->where($this->dx('member_id')." = '0' ",NULL,FALSE);
		}else{
			//$this->db->where($this->dx('member_id')." = '".implode(',',$member_ids)."' ",NULL,FALSE);
		}
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->fine_payable_transaction_types_array)||empty($this->transactions->fine_paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->fine_payable_transaction_types_array,$this->transactions->fine_paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->group_by(
        	array(
        		$this->dx('member_id'),
        		$this->dx('group_id')
        	)
        );
        $this->db->order_by('id','DESC',FALSE);
        $statements = $this->db->get('statements')->result();
        
        $statement_ids_array = array();

        foreach($statements as $statement):
        	$statement_ids_array[] = $statement->id;
        endforeach;

		$this->db->select(
			array(
				$this->dx('balance')." as cumulative_balance ",
				$this->dx('member_id')." as member_id ",
				$this->dx('group_id')." as group_id "
			)
		);
		if(empty($statement_ids_array)){
			$this->db->where_in('id','0');
		}else{
			$this->db->where_in('id',$statement_ids_array);
		}
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id')." = '0' ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." = '".implode(',',$group_ids)."' ",NULL,FALSE);
		}
		if(empty($member_ids)){
			$this->db->where($this->dx('member_id')." = '0' ",NULL,FALSE);
		}else{
			//$this->db->where($this->dx('member_id')." = '".implode(',',$member_ids)."' ",NULL,FALSE);
		}
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->fine_payable_transaction_types_array)||empty($this->transactions->fine_paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->fine_payable_transaction_types_array,$this->transactions->fine_paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$cumulative_balances = $this->db->get('statements')->result();

		foreach($group_ids as $group_id):
			foreach($member_ids as $member_id):
				$arr[$group_id][$member_id] = 0;
			endforeach;
		endforeach;

		foreach($cumulative_balances as $cumulative_balance):
			$arr[$cumulative_balance->group_id][$cumulative_balance->member_id] = $cumulative_balance->cumulative_balance;
		endforeach;

		return $arr;
	}

	function get_contribution_balances_array($group_ids = array(),$member_ids = array(),$contribution_ids = array()){
		$arr = array();
		$this->db->select(
			array(
				" MAX(id) as id "
			)
		);
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id')." = '0' ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." = '".implode(',',$group_ids)."' ",NULL,FALSE);
		}
		if(empty($member_ids)){
			$this->db->where($this->dx('member_id')." = '0' ",NULL,FALSE);
		}else{
			//$this->db->where($this->dx('member_id')." = '".implode(',',$member_ids)."' ",NULL,FALSE);
		}
		if(empty($contribution_ids)){
			$this->db->where($this->dx('contribution_id')." = '0' ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('contribution_id')." = '".implode(',',$contribution_ids)."' ",NULL,FALSE);
		}
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->group_by(
        	array(
        		$this->dx('member_id'),
        		$this->dx('group_id')
        	)
        );
        $this->db->order_by('id','DESC',FALSE);
        $statements = $this->db->get('statements')->result();
        
        $statement_ids_array = array();

        foreach($statements as $statement):
        	$statement_ids_array[] = $statement->id;
        endforeach;

		$this->db->select(
			array(
				$this->dx('contribution_balance')." as contribution_balance ",
				$this->dx('member_id')." as member_id ",
				$this->dx('group_id')." as group_id ",
				$this->dx('contribution_id')." as contribution_id "
			)
		);
		if(empty($statement_ids_array)){
			$this->db->where_in('id','0');
		}else{
			$this->db->where_in('id',$statement_ids_array);
		}
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id')." = '0' ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." = '".implode(',',$group_ids)."' ",NULL,FALSE);
		}
		if(empty($member_ids)){
			$this->db->where($this->dx('member_id')." = '0' ",NULL,FALSE);
		}else{
			//$this->db->where($this->dx('member_id')." = '".implode(',',$member_ids)."' ",NULL,FALSE);
		}
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$contribution_balances = $this->db->get('statements')->result();

		foreach($group_ids as $group_id):
			foreach($member_ids as $member_id):
				foreach($contribution_ids as $contribution_id):
					$arr[$group_id][$member_id][$contribution_id] = 0;
				endforeach;
			endforeach;
		endforeach;

		foreach($contribution_balances as $contribution_balance):
			$arr[$contribution_balance->group_id][$contribution_balance->member_id][$contribution_balance->contribution_id] = $contribution_balance->contribution_balance;
		endforeach;

		return $arr;
	}

	function get_contribution_fine_balances_array($group_ids = array(),$member_ids = array(),$contribution_ids = array()){
		$arr = array();
		$this->db->select(
			array(
				" MAX(id) as id "
			)
		);
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id')." = '0' ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." = '".implode(',',$group_ids)."' ",NULL,FALSE);
		}
		if(empty($member_ids)){
			$this->db->where($this->dx('member_id')." = '0' ",NULL,FALSE);
		}else{
			//$this->db->where($this->dx('member_id')." = '".implode(',',$member_ids)."' ",NULL,FALSE);
		}
		if(empty($contribution_ids)){
			$this->db->where($this->dx('contribution_id')." = '0' ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('contribution_id')." = '".implode(',',$contribution_ids)."' ",NULL,FALSE);
		}
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->fine_payable_transaction_types_array)||empty($this->transactions->fine_paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->fine_payable_transaction_types_array,$this->transactions->fine_paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->group_by(
        	array(
        		$this->dx('member_id'),
        		$this->dx('group_id')
        	)
        );
        $this->db->order_by('id','DESC',FALSE);
        $statements = $this->db->get('statements')->result();
        
        $statement_ids_array = array();

        foreach($statements as $statement):
        	$statement_ids_array[] = $statement->id;
        endforeach;

		$this->db->select(
			array(
				$this->dx('contribution_fine_balance')." as contribution_fine_balance ",
				$this->dx('member_id')." as member_id ",
				$this->dx('group_id')." as group_id ",
				$this->dx('contribution_id')." as contribution_id "
			)
		);
		if(empty($statement_ids_array)){
			$this->db->where_in('id','0');
		}else{
			$this->db->where_in('id',$statement_ids_array);
		}
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id')." = '0' ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." = '".implode(',',$group_ids)."' ",NULL,FALSE);
		}
		if(empty($member_ids)){
			$this->db->where($this->dx('member_id')." = '0' ",NULL,FALSE);
		}else{
			//$this->db->where($this->dx('member_id')." = '".implode(',',$member_ids)."' ",NULL,FALSE);
		}
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->fine_payable_transaction_types_array)||empty($this->transactions->fine_paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->fine_payable_transaction_types_array,$this->transactions->fine_paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$contribution_fine_balances = $this->db->get('statements')->result();

		foreach($group_ids as $group_id):
			foreach($member_ids as $member_id):
				foreach($contribution_ids as $contribution_id):
					$arr[$group_id][$member_id][$contribution_id] = 0;
				endforeach;
			endforeach;
		endforeach;

		foreach($contribution_fine_balances as $contribution_fine_balance):
			$arr[$contribution_fine_balance->group_id][$contribution_fine_balance->member_id][$contribution_fine_balance->contribution_id] = $contribution_fine_balance->contribution_fine_balance;
		endforeach;

		return $arr;
	}

	function get_orphan_deposit_statement_entries(){
		$this->db->select(
			array(
				'statements.id',
				$this->dx('statements.group_id')." as group_id ",
				$this->dx('statements.deposit_id')." as deposit_id ",
				$this->dx('statements.member_id')." as member_id ",
			)
		);
		$this->db->where($this->dx('statements.active')." = '1' ",NULL,FALSE);
		$this->db->where($this->dx('deposits.active')." = '0' ",NULL,FALSE);
		$this->db->join('deposits',$this->dx('statements.deposit_id')." = deposits.id ");
		return $this->db->get('statements')->result();
	}

	function get_orphan_invoice_statement_entries(){
		$this->db->select(
			array(
				'statements.id',
				$this->dx('statements.group_id')." as group_id ",
				$this->dx('statements.invoice_id')." as invoice_id ",
				$this->dx('statements.member_id')." as member_id ",
			)
		);
		$this->db->where($this->dx('statements.active')." = '1' ",NULL,FALSE);
		$this->db->where($this->dx('invoices.active')." = '0' ",NULL,FALSE);
		$this->db->join('invoices',$this->dx('statements.invoice_id')." = invoices.id ");
		return $this->db->get('statements')->result();
	}


	function get_orphan_contribution_refund_statement_entries(){
		$this->db->select(
			array(
				'statements.id',
				$this->dx('statements.group_id')." as group_id ",
				$this->dx('statements.refund_id')." as refund_id ",
				$this->dx('statements.member_id')." as member_id ",
			)
		);
		$this->db->where($this->dx('statements.active')." = '1' ",NULL,FALSE);
		$this->db->where($this->dx('contribution_refunds.active')." = '0' ",NULL,FALSE);
		$this->db->join('contribution_refunds',$this->dx('statements.refund_id')." = contribution_refunds.id ");
		return $this->db->get('statements')->result();
	}

	function get_group_member_total_contribution_paid($member_id = 0,$group_id = 0,$contribution_id_list = array()){
		$arr = array();
		$this->db->select(
			array(
				" MAX(id) as id "
			)
		);
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('member_id')." = '".$member_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->paid_transaction_types_array)||empty($this->transactions->payable_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->paid_transaction_types_array,$this->transactions->payable_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->group_by(
        	array(
        		$this->dx('member_id')
        	)
        );
        $this->db->order_by('id','DESC',FALSE);
        $statements = $this->db->get('statements')->result();  
        $statement_ids_array = array();
        foreach($statements as $statement):
        	$statement_ids_array[] = $statement->id;
        endforeach;
		$this->db->select(
			array(
				$this->dx('cumulative_paid')." as cumulative_paid "
			)
		);
		if(empty($statement_ids_array)){
			$this->db->where_in('id','0');
		}else{
			$this->db->where_in('id',$statement_ids_array);
		}
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->paid_transaction_types_array)||empty($this->transactions->payable_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->paid_transaction_types_array,$this->transactions->payable_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		return $this->db->get('statements')->row();
	}

	function get_group_member_total_paid_by_contribution_array($member_id = 0,$group_id = 0,$contribution_id_list = array(),$to=0){
		$arr = array();
		$this->db->select(
			array(
				"id",
				'SUM('.$this->dx('amount').') as total_amount',
				$this->dx('transaction_type').' as transaction_type',
				$this->dx('contribution_id').' as contribution_id',
			)
		);
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('member_id')." = '".$member_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->paid_transaction_types_array)||empty($this->transactions->paid_deductable_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->paid_transaction_types_array,$this->transactions->paid_deductable_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		if($contribution_id_list || is_numeric($contribution_id_list)){
			$this->db->where($this->dx('contribution_id')." IN (".$contribution_id_list.") ",NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date').'<"'.$to.'"',NULL,FALSE);
		}
        $this->db->order_by('id','DESC',FALSE);
        $this->db->group_by(array(
        	'contribution_id',
        	'transaction_type'
        ));
        $statements = $this->db->get('statements')->result(); 
        $paids_array = $this->transactions->paid_transaction_types_array;
        $payable_array = $this->transactions->paid_deductable_transaction_types_array;
        $arr = array();
        foreach ($statements as $statement) {
        	if(in_array($statement->transaction_type, $paids_array)){
        		if(array_key_exists($statement->contribution_id, $arr)){
        			$arr[$statement->contribution_id]+=$statement->total_amount;
        		}else{
        			$arr[$statement->contribution_id]=$statement->total_amount;
        		}
        	}else if(in_array($statement->transaction_type, $payable_array)){
        		if(array_key_exists($statement->contribution_id, $arr)){
        			$arr[$statement->contribution_id]-=$statement->total_amount;
        		}else{
        			$arr[$statement->contribution_id]=0-$statement->total_amount;
        		}
        	}
        }
        return $arr;
	}

	function get_group_members_total_paid_by_contribution_array($group_id = 0,$contribution_id_list = array(),$to=0){
		$arr = array();
		$this->db->select(
			array(
				"id",
				'SUM('.$this->dx('amount').') as total_amount',
				$this->dx('transaction_type').' as transaction_type',
				$this->dx('contribution_id').' as contribution_id',
				$this->dx('member_id').' as member_id',
			)
		);
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		//$this->db->where($this->dx('member_id')." = '".$member_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->paid_transaction_types_array)||empty($this->transactions->paid_deductable_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->paid_transaction_types_array,$this->transactions->paid_deductable_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		if($contribution_id_list || is_numeric($contribution_id_list)){
			$this->db->where($this->dx('contribution_id')." IN (".$contribution_id_list.") ",NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date').'<"'.$to.'"',NULL,FALSE);
		}
        $this->db->order_by('id','DESC',FALSE);
        $this->db->group_by(array(
        	'contribution_id',
        	'transaction_type',
        	'member_id'
        ));
        $statements = $this->db->get('statements')->result();
        $paids_array = $this->transactions->paid_transaction_types_array;
        $payable_array = $this->transactions->paid_deductable_transaction_types_array;
        $arr = array();
        foreach ($statements as $statement) {
        	if(in_array($statement->transaction_type, $paids_array)){
        		if(array_key_exists($statement->member_id, $arr)){
        			if(array_key_exists($statement->contribution_id, $arr[$statement->member_id])){
        				$arr[$statement->member_id][$statement->contribution_id]+=$statement->total_amount;
        			}else{
        				$arr[$statement->member_id][$statement->contribution_id]=$statement->total_amount;
        			}
        		}else{
        			$arr[$statement->member_id][$statement->contribution_id]=$statement->total_amount;
        		}
        	}else if(in_array($statement->transaction_type, $payable_array)){
        		if(array_key_exists($statement->member_id, $arr)){
        			if(array_key_exists($statement->contribution_id, $arr[$statement->member_id])){
        				$arr[$statement->member_id][$statement->contribution_id]-=$statement->total_amount;
        			}else{
        				$arr[$statement->member_id][$statement->contribution_id]=0-$statement->total_amount;
        			}
        		}else{
        			$arr[$statement->member_id][$statement->contribution_id]=0-$statement->total_amount;
        		}
        	}
        }
        return $arr;
	}

	function get_group_member_total_paid_by_contribution_array_monthly($member_id = 0,$group_id = 0,$contribution_id_list = array(),$from=0){
		$arr = array();
		$this->db->select(
			array(
				"id",
				'SUM('.$this->dx('amount').') as total_amount',
				$this->dx('transaction_type').' as transaction_type',
				$this->dx('contribution_id').' as contribution_id',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y%m') as year ",
			)
		);
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('member_id')." = '".$member_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->paid_transaction_types_array)||empty($this->transactions->paid_deductable_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->paid_transaction_types_array,$this->transactions->paid_deductable_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id')." IN (".$contribution_id_list.") ",NULL,FALSE);
		}
		if($from){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y%m') >= '" . date('Ym',$from) . "'", NULL, FALSE);
		}
        $this->db->order_by($this->dx('transaction_date'),'ASC',FALSE);
        $this->db->group_by(array(
        	'contribution_id',
        	'transaction_type',
        	'year',
        ));
        $statements = $this->db->get('statements')->result(); 
        $paids_array = $this->transactions->paid_transaction_types_array;
        $payable_array = $this->transactions->paid_deductable_transaction_types_array;
        $big_arr = array();
        foreach ($statements as $statement) {
        	if(in_array($statement->transaction_type, $paids_array)){
	        	if(array_key_exists($statement->year, $arr)){
	        		if(array_key_exists($statement->contribution_id, $arr[$statement->year])){
	        			$arr[$statement->year][$statement->contribution_id]+=$statement->total_amount;
	        		}else{
	        			$arr[$statement->year][$statement->contribution_id] = $statement->total_amount;
	        		}
	        	}else{
	        		$arr[$statement->year][$statement->contribution_id]= $statement->total_amount;
	        	}
	        }else if(in_array($statement->transaction_type, $payable_array)){
	        	if(array_key_exists($statement->year, $arr)){
	        		if(array_key_exists($statement->contribution_id, $arr[$statement->year])){
	        			$arr[$statement->year][$statement->contribution_id]-=$statement->total_amount;
	        		}else{
	        			$arr[$statement->year][$statement->contribution_id]=0-$statement->total_amount;
	        		}
	        	}else{
	        		$arr[$statement->year][$statement->contribution_id]=0-$statement->total_amount;
	        	}
	        }
        }
        return $arr;
	}

	function get_group_member_total_paid_loan_payment_array_monthly($member_id = 0,$group_id = 0,$from=0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as total_amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y%m') as year ",
			)
		);
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('member_id')." = '".$member_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		$this->db->where($this->dx('transaction_type')." IN (".implode(',',$this->transactions->loan_repayment_transaction_types).") ",NULL,FALSE);
		if($from){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y%m') >= '" . date('Ym',$from) . "'", NULL, FALSE);
		}
        $this->db->order_by($this->dx('transaction_date'),'ASC',FALSE);
        $this->db->group_by(array(
        	'year',
        ));
        $results = $this->db->get('transaction_statements')->result(); 
        $arr = array();
        foreach ($results as $key => $result) {
        	$arr[$result->year] = $result->total_amount;
        }
        return $arr;
	}

	function get_group_member_total_contribution_arrears($member_id=0,$group_id = 0){
		$arr = array();
		// $this->db->select(
		// 	array(
		// 		" MAX(id) as id "
		// 	)
		// );
		$this->db->select('id');
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('member_id')." = '".$member_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->limit(1);
        $this->db->order_by('id','DESC',FALSE);
        $statements = $this->db->get('statements')->row();
        // $statement_ids_array = array();
        // foreach($statements as $statement):
        // 	$statement_ids_array[] = $statement->id;
        // endforeach;
		$this->db->select(
			array(
				$this->dx('balance')." as cumulative_balance ",
			)
		);
		if(empty($statements)){
			$this->db->where_in('id','0');
		}else{
			$this->db->where_in('id',$statements->id);
		}
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('member_id')." = '".$member_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->payable_transaction_types_array)||empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->payable_transaction_types_array,$this->transactions->paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$cumulative_balances = $this->db->get('statements')->row();
		return $cumulative_balances;
	}

	function get_group_member_total_fine_paid($member_id=0,$group_id = 0){
		$arr = array();
		// $this->db->select(
		// 	array(
		// 		" MAX(id) as id "
		// 	)
		// );
		$this->db->select('id');
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('member_id')." = '".$member_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = $this->transactions->fine_paid_transaction_types_array;
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->limit(1);
        $this->db->order_by('id','DESC',FALSE);
        $statements = $this->db->get('statements')->row();
        // $statement_ids_array = array();
        // foreach($statements as $statement):
        // 	$statement_ids_array[] = $statement->id;
        // endforeach;

		$this->db->select(
			array(
				$this->dx('cumulative_paid')." as cumulative_paid ",
			)
		);
		if(empty($statements)){
			$this->db->where_in('id','0');
		}else{
			$this->db->where_in('id',$statements->id);
		}
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('member_id')." = '".$member_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->fine_paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = $this->transactions->fine_paid_transaction_types_array;
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$cumulative_paids = $this->db->get('statements')->row();
		return $cumulative_paids;
	}

	function get_group_member_tota_fine_arrears($member_id=0,$group_id=0){
		$arr = array();
		// $this->db->select(
		// 	array(
		// 		" MAX(id) as id "
		// 	)
		// );
		$this->db->select('id');
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('member_id')." = '".$member_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->fine_payable_transaction_types_array)||empty($this->transactions->fine_paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->fine_payable_transaction_types_array,$this->transactions->fine_paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->limit(1);
        $this->db->order_by('id','DESC',FALSE);
        $statements = $this->db->get('statements')->row();
        
        // $statement_ids_array = array();

        // foreach($statements as $statement):
        // 	$statement_ids_array[] = $statement->id;
        // endforeach;
     
		$this->db->select(
			array(
				$this->dx('balance')." as cumulative_balance ",
			)
		);
		if(empty($statements)){
			$this->db->where_in('id','0');
		}else{
			$this->db->where_in('id',$statements->id);
		}
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('member_id')." = '".$member_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->fine_payable_transaction_types_array)||empty($this->transactions->fine_paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->fine_payable_transaction_types_array,$this->transactions->fine_paid_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$cumulative_balances = $this->db->get('statements')->row();
		return $cumulative_balances;
	}

	function count_voided_statements(){
		$this->db->where($this->dx('active')." = '0' ",NULL,FALSE);
		return $this->db->count_all_results('statements');
	}

	function count_statements(){
		return $this->db->count_all_results('statements');
	}


	function void_group_contribution_fine_statements($group_id = 0,$statement_ids = array()){
		$input = array(
			'active' => 0,
			'modified_on' => time(),
		);
		$statement_id_list = 0;
		if(empty($statement_ids)){

		}else{
			$statement_id_list = implode(',', $statement_ids);
		}

        $where = " ".$this->dx('group_id')." = (".$group_id.") AND id IN (".$invoice_id_list.") ";
        return $this->update_secure_where($where,'statements',$input); 
	}

	function get_voided_statements($limit=0){
		$this->select_all_secure('statements');
		$arr = array();
		$this->db->where($this->dx('active')." = '0' ",NULL,FALSE);
        $this->db->order_by('id','DESC',FALSE);
        $this->db->limit($limit);
		$results = $this->db->get('statements')->result();
		foreach($results as $statement):
        	$arr[$statement->id] = $statement;
        endforeach;
        return $arr;
	}

	function get_voided_statements_parent_ids_options($limit=0){
		$this->select_all_secure('voided_statements');
		//$this->select_all_secure('voided_statements');
		$arr = array();
		$this->db->where($this->dx('created_on').' <= "'.strtotime("-1 day",time()).'"',NULL,FALSE);
        $this->db->order_by('id','DESC',FALSE);
		$results = $this->db->get('voided_statements')->result();
		foreach($results as $statement):
        	$arr[$statement->parent_statement_id] = $statement->id;
        endforeach;
        return $arr;
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


	function get_voided_statements_parent_ids_options_from($from=0 ,$to=0){
		$this->select_all_secure('voided_statements');
		//$this->select_all_secure('voided_statements');
		$arr = array();
		//$this->db->where($this->dx('created_on').' <= "'.strtotime("-1 day",time()).'"',NULL,FALSE);
		$this->db->where('id'." >= '".$from."' ",NULL,FALSE);
		$this->db->where('id'." >= '".$to."' ",NULL,FALSE);
		//$this->db->where('id' >= $from);
		//$this->db->where('id', <= $to);
        $this->db->order_by('id','DESC',FALSE);
		$results = $this->db->get('voided_statements')->result();
		foreach($results as $statement):
        	$arr[$statement->parent_statement_id] = $statement->id;
        endforeach;
        return $arr;
	}

	function insert_chunk_voided_statements($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_chunked_batch_secure_data('voided_statements',$input);
	}

	function delete_voided_statements(){
		$this->db->where($this->dx('active')." = '0' ",NULL,FALSE);
		return $this->db->delete('statements');
	}
	
	function delete_voided_statements_array($old_statements_ids){
		$delete_count = 0;
		$this->db->where($this->dx('active').'="0"',NULL,FALSE);
		foreach ($old_statements_ids as $key => $id) {
			$delete_count++;
			$this->db->where('id',$id);
			$this->db->delete('statements');			
		}
		return $delete_count;
	}

	function voided_table_count(){
		return $this->db->count_all_results('voided_statements');
	}

	function get_group_contribution_transfers_from_contribution_to_contribution_per_year_array($group_id = 0,$start_year = 2000,$end_year = 2039){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		foreach($contribution_options as $contribution_id => $contribution_name){
			for($i = $start_year; $i < $end_year; $i++):
				$arr[$contribution_id][$i] = 0;
			endfor;
		}
		
		$this->db->select(
			array(
				" ".$this->dx('amount').' as amount ',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
				$this->dx('contribution_id').' as contribution_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "25" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
        /*$this->db->group_by(
        	array(
        		$this->dx("contribution_id"),
        		'year'
        	)
        );*/
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$arr[$row->contribution_id][$row->year] += currency($row->amount);
			}
		}
		return $arr;
	}

	function get_group_contribution_transfers_from_contribution_to_fine_category_per_contribution_per_month_array($group_id = 0,$months_array = array()){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		foreach($contribution_options as $contribution_id => $contribution_name){
			foreach($months_array as $month):
				$arr[$contribution_id][$month] = 0;
			endforeach;
		}		
		$this->db->select(
			array(
				" ".$this->dx('amount').' as amount ',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%b') as month ",
				$this->dx('contribution_id').' as contribution_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "27" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
       /* $this->db->group_by(
        	array(
        		$this->dx("contribution_id"),
        		'year'
        	)
        );*/
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				if(isset($arr[($row->contribution_id)][($row->month.' '.$row->year)])){
					$arr[($row->contribution_id)][($row->month.' '.$row->year)] += currency($row->amount);
				}else{
					$arr[($row->contribution_id)][($row->month.' '.$row->year)] = currency($row->amount);
				}
			}
		}
		return $arr;
	}

	function get_group_total_contribution_transfers_to_ignore_per_contribution_per_month_array($group_id = 0,$months_array = array()){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		foreach($contribution_options as $contribution_id => $contribution_name){
			foreach($months_array as $month):
				$arr[$contribution_id][$month] = 0;
			endforeach;
		}
		$this->db->select(
			array(
				" ".$this->dx('amount').' as amount ',
				$this->dx('contribution_from_id').' as contribution_from_id ',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%b') as month ",
				$this->dx('contribution_id').' as contribution_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "26" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
        /*$this->db->group_by(
        	array(
        		$this->dx("contribution_id"),
        		'year'
        	)
        );*/
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				if($contribution = $this->contributions_m->get_group_contribution($row->contribution_from_id)){
					if($contribution->display_contribution_arrears_cumulatively){
						$arr[$row->contribution_id][$row->month.' '.$row->year] += currency($row->amount);
					}else{
						$arr[$row->contribution_id][$row->month.' '.$row->year] = 0;
					}
				}
			}
		}
		return $arr;
	}

	function get_group_total_contribution_transfers_from_ignore_per_contribution_per_month_array($group_id = 0,$months_array = array()){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();

		foreach($contribution_options as $contribution_id => $contribution_name){
        	foreach($months_array as $month):
				$arr[$contribution_id][$month] = 0;
			endforeach;
		}
		
		$this->db->select(
			array(
				$this->dx('amount').' as amount ',
				//$this->dx('contribution_from_id').' as contribution_from_id ',
				$this->dx('contribution_id').' as contribution_id ',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%b') as month ",
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "25" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
        $this->db->group_by(
        	array(
        		$this->dx("contribution_id"),
        		$this->dx("statements.amount"),
        		'year',
        		'month'
        	)
        );
		$result = $this->db->get('statements')->result();
		foreach($result as $row){
			if($row->contribution_id){
				$contribution = $this->contributions_m->get_group_contribution($row->contribution_id);
				if($contribution->display_contribution_arrears_cumulatively){
					$arr[$row->contribution_id][$row->month.' '.$row->year] = $row->amount;
				}else{
					$arr[$row->contribution_id][$row->month.' '.$row->year] = 0;
				}
			}
		}
		return $arr;
	}

	function get_group_total_contribution_transfers_to_fines_per_month($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				' '.$this->dx('amount').' as amount',
				$this->dx('member_id').' as member_id',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%b') as month ",
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "28" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
        /*$this->db->group_by(
        	array(
        		'year'
        	)
        );*/
		$results = $this->db->get('statements')->result();

		$month_arr = array();
		foreach($results as $result):
			$month_arr[$result->member_id] = ''; 
            $arr[$result->member_id] = 0;
        endforeach;

        foreach ($results as $result):
        	$month_arr[$result->member_id] = $result->month.' '.$result->year;
            $arr[$result->member_id] += currency($result->amount);
        endforeach;
        $transfers_per_month = array();
        foreach ($arr as $key => $transfers_out) {
        	$transfers_per_month[] = (object) array(
        		'month'=>$month_arr[$key],
        		'amount'=>$arr[$key]
        	);
        }
        
		return $transfers_per_month;
	}

	function get_group_total_contribution_transfers_from_per_month($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		$this->db->select(
			array(
				" ".$this->dx('amount').' as amount ',
				$this->dx('member_id').' as member_id',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%b') as month ",
			)
		);

		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "25" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		/*$this->db->group_by(
			array(
				'year'
			)
		);*/

		$results = $this->db->get('statements')->result();
		$arr = array();
		$month_arr = array();
		foreach($results as $result):
			$month_arr[$result->member_id] = ''; 
            $arr[$result->member_id] = 0;
        endforeach;

        foreach ($results as $result):
        	$month_arr[$result->member_id] = $result->month.' '.$result->year;
            $arr[$result->member_id] += currency($result->amount);
        endforeach;
        $transfers_per_month = array();
        foreach ($arr as $key => $transfers_out) {
        	$transfers_per_month[] = (object) array(
        		'month'=>$month_arr[$key],
        		'amount'=>$arr[$key]
        	);
        }

		return $transfers_per_month;
	}

	function get_group_total_contribution_transfers_to_per_month($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		$arr = array();
		$this->db->select(
			array(
				" ".$this->dx('amount').' as amount ',
				$this->dx('member_id').' as member_id',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%b') as month ",
			)
		);
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "26" ',NULL,FALSE);
		if($group_id){

			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		/*$this->db->group_by(
			array(
				'year'
			)
		);*/
		$results = $this->db->get('statements')->result();

		$month_arr = array();
		foreach($results as $result):
			$month_arr[$result->member_id] = ''; 
            $arr[$result->member_id] = 0;
        endforeach;

        foreach ($results as $result):
        	$month_arr[$result->member_id] = $result->month.' '.$result->year;
            $arr[$result->member_id] += currency($result->amount);
        endforeach;
        $transfers_per_month = array();
        foreach ($arr as $key => $transfers_out) {
        	$transfers_per_month[] = (object) array(
        		'month'=>$month_arr[$key],
        		'amount'=>$arr[$key]
        	);
        }

		return $transfers_per_month;
	}

	function get_group_total_contribution_transfers_from_contribution_to_fine_category_per_month($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		$this->db->select(
			array(
				' '.$this->dx('amount').' as amount ',
				$this->dx('member_id').' as member_id',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%b') as month ",
			)
		);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "27" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'" ',NULL,FALSE);
		}
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'" ',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date').' <="'.$to.'" ',NULL,FALSE);
		}
        /*$this->db->group_by(
			array(
				'year'
			)
		);*/
		//return $this->db->get('statements')->result();
		$results = $this->db->get('statements')->result();
		$arr = array();
		$month_arr = array();
		$transfers_to_fine_per_month = array();
		foreach($results as $result):
			$month_arr[$result->member_id] = ''; 
            $arr[$result->member_id] = 0;
        endforeach;

        foreach ($results as $result):
        	$month_arr[$result->member_id] = $result->month.' '.$result->year;
            $arr[$result->member_id] += currency($result->amount);
        endforeach;
        
        foreach ($arr as $key => $transfers_out) {
        	$transfers_to_fine_per_month[] = (object) array(
        		'month'=>$month_arr[$key],
        		'amount'=>$arr[$key]
        	);
        }
		return $transfers_to_fine_per_month;
	}

	function get_group_total_contribution_transfers_to_loan_per_month($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		$this->db->select(
			array(
				" ".$this->dx('amount').' as amount ',
				$this->dx('member_id').' as member_id',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%b') as month ",
			)
		);

		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "30" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		/*$this->db->group_by(
			array(
				'year'
			)
		);*/
		$results = $this->db->get('statements')->result();

		$arr = array();
		$month_arr = array();
		$transfers_to_loan_per_month = array();
		foreach($results as $result):
			$month_arr[$result->member_id] = ''; 
            $arr[$result->member_id] = 0;
        endforeach;

        foreach ($results as $result):
        	$month_arr[$result->member_id] = $result->month.' '.$result->year;
            $arr[$result->member_id] += currency($result->amount);
        endforeach;
        
        foreach ($arr as $key => $transfers_out) {
        	$transfers_to_loan_per_month[] = (object) array(
        		'month'=>$month_arr[$key],
        		'amount'=>$arr[$key]
        	);
        }

		return $transfers_to_loan_per_month;
	}

	function find_duplicate_contribution_fine_statement_entries($group_id = 0){
		$this->db->select(
			array(
				$this->dx('statements.transaction_type')." as transaction_type ",
				"COUNT(".$this->dx('statements.transaction_type').") as count ",
				$this->dx('statements.transaction_date')." as transaction_date ",
				"COUNT(".$this->dx('statements.transaction_date').")",
				$this->dx('statements.contribution_id')." as contribution_id ",
				"COUNT(".$this->dx('statements.contribution_id').")",
				$this->dx('statements.group_id')." as group_id ",
				"COUNT(".$this->dx('statements.group_id').")",
				$this->dx('statements.member_id')." as member_id ",
				"COUNT(".$this->dx('statements.member_id').")",
				$this->dx('statements.created_on')." as created_on ",
				"COUNT(".$this->dx('statements.created_on').")",
				$this->dx('statements.invoice_id')." as invoice_id ",
				"COUNT(".$this->dx('statements.invoice_id').")",
			)
		);
		$this->db->where($this->dx('invoices.active')." = 1 ",NULL,FALSE);
		$this->db->where($this->dx('statements.active')." = 1 ",NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('statements.group_id')." = ".$group_id." ",NULL,FALSE);
			$this->db->where($this->dx('invoices.group_id')." = ".$group_id." ",NULL,FALSE);
		}
		$this->db->group_by(
			array(
				$this->dx('statements.transaction_type'),
				$this->dx('statements.transaction_date'),
				$this->dx('statements.contribution_id'),
				$this->dx('statements.group_id'),
				$this->dx('statements.member_id'),
				$this->dx('statements.created_on'),
				$this->dx('statements.invoice_id'),
			)
		);
		$this->db->having("(COUNT(".$this->dx('statements.transaction_type').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.transaction_date').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.contribution_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.group_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.member_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.created_on').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.invoice_id').") > 1)",NULL,FALSE);
		$this->db->order_by($this->dx('statements.transaction_date'),'DESC',FALSE);
		$this->db->join('invoices',$this->dx('statements.invoice_id')." = invoices.id ");
		$statement_entries = $this->db->get('statements')->result();
		return $statement_entries;
	}

	function get_duplicate_contribution_fine_statements($group_ids = array(),$member_ids = array(),$transaction_types = array(),$contribution_ids = array(),$transaction_dates = array(),$created_ons = array()){
		$this->db->select(
			array(
				'id',
				$this->dx('group_id')." as group_id ",
				$this->dx('member_id')." as member_id ",
				$this->dx('transaction_date')." as transaction_date ",
				$this->dx('transaction_type')." as transaction_type ",
				$this->dx('contribution_id')." as contribution_id ",
			)
		);
		$this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." IN ( ".implode(',',array_unique($group_ids))." ) ",NULL,FALSE);
		}
		if(empty($member_ids)){
			$this->db->where($this->dx('member_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id')." IN ( ".implode(',',array_unique($member_ids))." ) ",NULL,FALSE);
		}
		if(empty($transaction_types)){
			$this->db->where($this->dx('transaction_type')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('transaction_type')." IN ( ".implode(',',array_unique($transaction_types))." ) ",NULL,FALSE);
		}
		if(empty(array_filter($contribution_ids))){
			$this->db->where($this->dx('contribution_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('contribution_id')." IN ( ".implode(',',array_unique(array_filter($contribution_ids)))." ) ",NULL,FALSE);
		}
		if(empty($transaction_dates)){
			$this->db->where($this->dx('transaction_date')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('transaction_date')." IN ( ".implode(',',array_unique($transaction_dates))." ) ",NULL,FALSE);
		}
		// if(empty($created_ons)){
		// 	$this->db->where($this->dx('created_on')." = 0 ",NULL,FALSE);
		// }else{
		// 	$this->db->where($this->dx('created_on')." IN ( ".implode(',',array_unique($created_ons))." ) ",NULL,FALSE);
		// }
		return $this->db->get('statements')->result();
	}

	function delete_where_in($ids_array = array(),$group_id = 0){
		if(empty(array_filter($ids_array))){
			$this->db->where_in('id',0);
		}else{
			$this->db->where_in('id',array_filter($ids_array));
		}
		$this->db->where($this->dx('group_id')." = ".$group_id." ",NULL,FALSE);
		$this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		$this->db->delete('statements');
		return $this->db->affected_rows();
	}

	function find_duplicate_fine_statement_entries($group_id = 0){
		$this->db->select(
			array(
				$this->dx('statements.transaction_type')." as transaction_type ",
				"COUNT(".$this->dx('statements.transaction_type').") as count ",
				$this->dx('statements.transaction_date')." as transaction_date ",
				"COUNT(".$this->dx('statements.transaction_date').")",
				$this->dx('statements.fine_id')." as fine_id ",
				"COUNT(".$this->dx('statements.fine_id').")",
				$this->dx('statements.fine_category_id')." as fine_category_id ",
				"COUNT(".$this->dx('statements.fine_category_id').")",
				$this->dx('statements.group_id')." as group_id ",
				"COUNT(".$this->dx('statements.group_id').")",
				$this->dx('statements.member_id')." as member_id ",
				"COUNT(".$this->dx('statements.member_id').")",
				$this->dx('statements.created_on')." as created_on ",
				"COUNT(".$this->dx('statements.created_on').")",
				$this->dx('statements.invoice_id')." as invoice_id ",
				"COUNT(".$this->dx('statements.invoice_id').")",
			)
		);
		$this->db->where($this->dx('invoices.active')." = 1 ",NULL,FALSE);
		$this->db->where($this->dx('statements.active')." = 1 ",NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('statements.group_id')." = ".$group_id." ",NULL,FALSE);
			$this->db->where($this->dx('invoices.group_id')." = ".$group_id." ",NULL,FALSE);
		}
		$this->db->group_by(
			array(
				$this->dx('statements.transaction_type'),
				$this->dx('statements.transaction_date'),
				$this->dx('statements.fine_id'),
				$this->dx('statements.fine_category_id'),
				$this->dx('statements.group_id'),
				$this->dx('statements.member_id'),
				$this->dx('statements.created_on'),
				$this->dx('statements.invoice_id'),
			)
		);
		//$this->db->having("(COUNT(".$this->dx('statements.transaction_type').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.transaction_date').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.fine_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.fine_category_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.group_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.member_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.created_on').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.invoice_id').") > 1)",NULL,FALSE);
		$this->db->order_by($this->dx('statements.transaction_date'),'DESC',FALSE);
		$this->db->join('invoices',$this->dx('statements.invoice_id')." = invoices.id ");
		$statement_entries = $this->db->get('statements')->result();
		return $statement_entries;
	}

		function get_duplicate_fine_statements($group_ids = array(),$member_ids = array(),$transaction_types = array(),$fine_ids = array(),$transaction_dates = array(),$created_ons = array()){
		$this->db->select(
			array(
				'id',
				$this->dx('group_id')." as group_id ",
				$this->dx('member_id')." as member_id ",
				$this->dx('transaction_date')." as transaction_date ",
				$this->dx('transaction_type')." as transaction_type ",
				$this->dx('fine_id')." as fine_id ",
			)
		);
		$this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." IN ( ".implode(',',array_unique($group_ids))." ) ",NULL,FALSE);
		}
		if(empty($member_ids)){
			$this->db->where($this->dx('member_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id')." IN ( ".implode(',',array_unique($member_ids))." ) ",NULL,FALSE);
		}
		if(empty($transaction_types)){
			$this->db->where($this->dx('transaction_type')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('transaction_type')." IN ( ".implode(',',array_unique($transaction_types))." ) ",NULL,FALSE);
		}
		if(empty(array_filter($fine_ids))){
			$this->db->where($this->dx('fine_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('fine_id')." IN ( ".implode(',',array_unique(array_filter($fine_ids)))." ) ",NULL,FALSE);
		}
		if(empty($transaction_dates)){
			$this->db->where($this->dx('transaction_date')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('transaction_date')." IN ( ".implode(',',array_unique($transaction_dates))." ) ",NULL,FALSE);
		}
		// if(empty($created_ons)){
		// 	$this->db->where($this->dx('created_on')." = 0 ",NULL,FALSE);
		// }else{
		// 	$this->db->where($this->dx('created_on')." IN ( ".implode(',',array_unique($created_ons))." ) ",NULL,FALSE);
		// }
		return $this->db->get('statements')->result();
	}

	function get_duplicate_deposit_statements($group_ids = array(),$member_ids = array(),$transaction_types = array(),$deposit_ids = array(),$transaction_dates = array(),$created_ons = array()){
		$this->db->select(
			array(
				'id',
				$this->dx('group_id')." as group_id ",
				$this->dx('member_id')." as member_id ",
				$this->dx('transaction_date')." as transaction_date ",
				$this->dx('transaction_type')." as transaction_type ",
				$this->dx('deposit_id')." as deposit_id ",
			)
		);
		$this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." IN ( ".implode(',',array_unique($group_ids))." ) ",NULL,FALSE);
		}
		if(empty($member_ids)){
			$this->db->where($this->dx('member_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id')." IN ( ".implode(',',array_unique($member_ids))." ) ",NULL,FALSE);
		}
		if(empty($transaction_types)){
			$this->db->where($this->dx('transaction_type')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('transaction_type')." IN ( ".implode(',',array_unique($transaction_types))." ) ",NULL,FALSE);
		}
		if(empty(array_filter($deposit_ids))){
			$this->db->where($this->dx('deposit_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('deposit_id')." IN ( ".implode(',',array_unique(array_filter($deposit_ids)))." ) ",NULL,FALSE);
		}
		if(empty($transaction_dates)){
			$this->db->where($this->dx('transaction_date')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('transaction_date')." IN ( ".implode(',',array_unique($transaction_dates))." ) ",NULL,FALSE);
		}

		//$this->db->limit(2000);
		// if(empty($created_ons)){
		// 	$this->db->where($this->dx('created_on')." = 0 ",NULL,FALSE);
		// }else{
		// 	$this->db->where($this->dx('created_on')." IN ( ".implode(',',array_unique($created_ons))." ) ",NULL,FALSE);
		// }
		return $this->db->get('statements')->result();
	}

	function get_duplicate_contribution_transfer_statements($group_ids = array(),$member_ids = array(),$transaction_types = array(),$deposit_ids = array(),$transaction_dates = array(),$contribution_from_ids = array(),$contribution_to_ids = array()){
		$this->db->select(
			array(
				'id',
				$this->dx('group_id')." as group_id ",
				$this->dx('member_id')." as member_id ",
				$this->dx('transaction_date')." as transaction_date ",
				$this->dx('transaction_type')." as transaction_type ",
				$this->dx('deposit_id')." as deposit_id ",
				$this->dx('contribution_from_id')." as contribution_from_id ",
				$this->dx('contribution_to_id')." as contribution_to_id ",
			)
		);
		$this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		if(empty(array_unique($group_ids))){
			$this->db->where($this->dx('group_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." IN ( ".implode(',',array_unique($group_ids))." ) ",NULL,FALSE);
		}
		if(empty(array_unique($member_ids))){
			$this->db->where($this->dx('member_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id')." IN ( ".implode(',',array_unique($member_ids))." ) ",NULL,FALSE);
		}
		if(empty(array_unique($transaction_types))){
			$this->db->where($this->dx('transaction_type')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('transaction_type')." IN ( ".implode(',',array_unique($transaction_types))." ) ",NULL,FALSE);
		}
		// if(empty($deposit_ids)){
		// 	$this->db->where($this->dx('deposit_id')." = 0 ",NULL,FALSE);
		// }else{
		// 	$this->db->where($this->dx('deposit_id')." IN ( ".implode(',',array_unique(array_filter($deposit_ids)))." ) ",NULL,FALSE);
		// }
		if(empty(array_filter($contribution_from_ids))){
			$this->db->where($this->dx('contribution_from_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('contribution_from_id')." IN ( ".implode(',',array_unique(array_filter($contribution_from_ids)))." ) ",NULL,FALSE);
		}
		if(empty(array_filter($contribution_to_ids))){
			$this->db->where($this->dx('contribution_to_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('contribution_to_id')." IN ( ".implode(',',array_unique(array_filter($contribution_to_ids)))." ) ",NULL,FALSE);
		}
		if(empty($transaction_dates)){
			$this->db->where($this->dx('transaction_date')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('transaction_date')." IN ( ".implode(',',array_unique($transaction_dates))." ) ",NULL,FALSE);
		}

		//$this->db->limit(2000);
		// if(empty($created_ons)){
		// 	$this->db->where($this->dx('created_on')." = 0 ",NULL,FALSE);
		// }else{
		// 	$this->db->where($this->dx('created_on')." IN ( ".implode(',',array_unique($created_ons))." ) ",NULL,FALSE);
		// }
		return $this->db->get('statements')->result();
	}

	function get_duplicate_contribution_transfer_to_fine_statements($group_ids = array(),$member_ids = array(),$transaction_types = array(),$deposit_ids = array(),$transaction_dates = array(),$contribution_from_ids = array(),$fine_category_to_ids = array()){
		$this->db->select(
			array(
				'id',
				$this->dx('group_id')." as group_id ",
				$this->dx('member_id')." as member_id ",
				$this->dx('transaction_date')." as transaction_date ",
				$this->dx('transaction_type')." as transaction_type ",
				$this->dx('deposit_id')." as deposit_id ",
				$this->dx('contribution_from_id')." as contribution_from_id ",
				$this->dx('fine_category_to_id')." as fine_category_to_id ",
			)
		);
		$this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." IN ( ".implode(',',array_unique($group_ids))." ) ",NULL,FALSE);
		}
		if(empty($member_ids)){
			$this->db->where($this->dx('member_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id')." IN ( ".implode(',',array_unique($member_ids))." ) ",NULL,FALSE);
		}
		if(empty($transaction_types)){
			$this->db->where($this->dx('transaction_type')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('transaction_type')." IN ( ".implode(',',array_unique($transaction_types))." ) ",NULL,FALSE);
		}
		// if(empty($deposit_ids)){
		// 	$this->db->where($this->dx('deposit_id')." = 0 ",NULL,FALSE);
		// }else{
		// 	$this->db->where($this->dx('deposit_id')." IN ( ".implode(',',array_unique(array_filter($deposit_ids)))." ) ",NULL,FALSE);
		// }
		if(empty(array_filter($contribution_from_ids))){
			$this->db->where($this->dx('contribution_from_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('contribution_from_id')." IN ( ".implode(',',array_unique(array_filter($contribution_from_ids)))." ) ",NULL,FALSE);
		}
		if(empty(array_filter($fine_category_to_ids))){
			$this->db->where($this->dx('fine_category_to_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('fine_category_to_id')." IN ( ".implode(',',array_unique(array_filter($fine_category_to_ids)))." ) ",NULL,FALSE);
		}
		if(empty($transaction_dates)){
			$this->db->where($this->dx('transaction_date')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('transaction_date')." IN ( ".implode(',',array_unique($transaction_dates))." ) ",NULL,FALSE);
		}

		//$this->db->limit(2000);
		// if(empty($created_ons)){
		// 	$this->db->where($this->dx('created_on')." = 0 ",NULL,FALSE);
		// }else{
		// 	$this->db->where($this->dx('created_on')." IN ( ".implode(',',array_unique($created_ons))." ) ",NULL,FALSE);
		// }
		return $this->db->get('statements')->result();
	}

	function get_duplicate_contribution_refund_statements($group_ids = array(),$member_ids = array(),$transaction_types = array(),$transaction_dates = array(),$refund_ids = array()){
		$this->db->select(
			array(
				'id',
				$this->dx('group_id')." as group_id ",
				$this->dx('member_id')." as member_id ",
				$this->dx('transaction_date')." as transaction_date ",
				$this->dx('transaction_type')." as transaction_type ",
				$this->dx('deposit_id')." as deposit_id ",
				$this->dx('contribution_from_id')." as contribution_from_id ",
				$this->dx('contribution_to_id')." as contribution_to_id ",
				$this->dx('refund_id')." as refund_id ",
			)
		);
		$this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." IN ( ".implode(',',array_unique($group_ids))." ) ",NULL,FALSE);
		}
		if(empty($member_ids)){
			$this->db->where($this->dx('member_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id')." IN ( ".implode(',',array_unique($member_ids))." ) ",NULL,FALSE);
		}
		if(empty($transaction_types)){
			$this->db->where($this->dx('transaction_type')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('transaction_type')." IN ( ".implode(',',array_unique($transaction_types))." ) ",NULL,FALSE);
		}
		// if(empty($deposit_ids)){
		// 	$this->db->where($this->dx('deposit_id')." = 0 ",NULL,FALSE);
		// }else{
		// 	$this->db->where($this->dx('deposit_id')." IN ( ".implode(',',array_unique(array_filter($deposit_ids)))." ) ",NULL,FALSE);
		// }
		if(empty(array_filter($refund_ids))){
			$this->db->where($this->dx('refund_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('refund_id')." IN ( ".implode(',',array_unique(array_filter($refund_ids)))." ) ",NULL,FALSE);
		}
		// if(empty($contribution_to_ids)){
		// 	$this->db->where($this->dx('contribution_to_id')." = 0 ",NULL,FALSE);
		// }else{
		// 	$this->db->where($this->dx('contribution_to_id')." IN ( ".implode(',',array_unique(array_filter($contribution_to_ids)))." ) ",NULL,FALSE);
		// }
		if(empty($transaction_dates)){
			$this->db->where($this->dx('transaction_date')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('transaction_date')." IN ( ".implode(',',array_unique($transaction_dates))." ) ",NULL,FALSE);
		}

		//$this->db->limit(2000);
		// if(empty($created_ons)){
		// 	$this->db->where($this->dx('created_on')." = 0 ",NULL,FALSE);
		// }else{
		// 	$this->db->where($this->dx('created_on')." IN ( ".implode(',',array_unique($created_ons))." ) ",NULL,FALSE);
		// }
		return $this->db->get('statements')->result();
	}

	function find_duplicate_deposit_statement_entries($group_id = 0){
		$this->db->select(
			array(
				$this->dx('statements.transaction_type')." as transaction_type ",
				"COUNT(".$this->dx('statements.transaction_type').") as count ",
				$this->dx('statements.transaction_date')." as transaction_date ",
				"COUNT(".$this->dx('statements.transaction_date').")",
				$this->dx('statements.deposit_id')." as deposit_id ",
				"COUNT(".$this->dx('statements.deposit_id').")",
				$this->dx('statements.group_id')." as group_id ",
				"COUNT(".$this->dx('statements.group_id').")",
				$this->dx('statements.member_id')." as member_id ",
				"COUNT(".$this->dx('statements.member_id').")",
				$this->dx('statements.created_on')." as created_on ",
				"COUNT(".$this->dx('statements.created_on').")",
				$this->dx('statements.invoice_id')." as invoice_id ",
				"COUNT(".$this->dx('statements.invoice_id').")",
			)
		);
		$this->db->where($this->dx('statements.active')." = 1 ",NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('statements.group_id')." = ".$group_id." ",NULL,FALSE);
		}
		$this->db->group_by(
			array(
				$this->dx('statements.transaction_type'),
				$this->dx('statements.transaction_date'),
				$this->dx('statements.deposit_id'),
				$this->dx('statements.group_id'),
				$this->dx('statements.member_id'),
				$this->dx('statements.created_on'),
				$this->dx('statements.invoice_id'),
			)
		);
		$this->db->having("(COUNT(".$this->dx('statements.transaction_type').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.transaction_date').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.deposit_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.group_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.member_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.created_on').") > 1)",NULL,FALSE);
		$this->db->order_by($this->dx('statements.transaction_date'),'DESC',FALSE);
		$statement_entries = $this->db->get('statements')->result();
		return $statement_entries;
	}

	function find_duplicate_share_transfer_statement_entries($group_id = 0){
		$this->db->select(
			array(
				$this->dx('statements.transaction_type')." as transaction_type ",
				"COUNT(".$this->dx('statements.transaction_type').") as count ",
				$this->dx('statements.transaction_date')." as transaction_date ",
				"COUNT(".$this->dx('statements.transaction_date').")",
				$this->dx('statements.deposit_id')." as deposit_id ",
				"COUNT(".$this->dx('statements.deposit_id').")",
				$this->dx('statements.group_id')." as group_id ",
				"COUNT(".$this->dx('statements.group_id').")",
				$this->dx('statements.member_id')." as member_id ",
				"COUNT(".$this->dx('statements.member_id').")",
				$this->dx('statements.created_on')." as created_on ",
				"COUNT(".$this->dx('statements.created_on').")",
				$this->dx('statements.invoice_id')." as invoice_id ",
				"COUNT(".$this->dx('statements.invoice_id').")",
				$this->dx('statements.contribution_from_id')." as contribution_from_id ",
				"COUNT(".$this->dx('statements.contribution_from_id').")",
				$this->dx('statements.contribution_to_id')." as contribution_to_id ",
				"COUNT(".$this->dx('statements.contribution_to_id').")",
			)
		);
		$this->db->where($this->dx('statements.active')." = 1 ",NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('statements.group_id')." = ".$group_id." ",NULL,FALSE);
		}
		$this->db->group_by(
			array(
				$this->dx('statements.transaction_type'),
				$this->dx('statements.transaction_date'),
				$this->dx('statements.deposit_id'),
				$this->dx('statements.group_id'),
				$this->dx('statements.member_id'),
				$this->dx('statements.created_on'),
				$this->dx('statements.invoice_id'),
				$this->dx('statements.contribution_from_id'),
				$this->dx('statements.contribution_to_id'),
			)
		);
		$this->db->having("(COUNT(".$this->dx('statements.transaction_type').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.transaction_date').") > 1)",NULL,FALSE);
		//$this->db->having("(COUNT(".$this->dx('statements.deposit_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.group_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.member_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.contribution_from_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.contribution_to_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.created_on').") > 1)",NULL,FALSE);
		$this->db->order_by($this->dx('statements.transaction_date'),'DESC',FALSE);
		$statement_entries = $this->db->get('statements')->result();
		return $statement_entries;
	}

	function find_duplicate_share_to_fine_transfer_statement_entries($group_id = 0){
		$this->db->select(
			array(
				$this->dx('statements.transaction_type')." as transaction_type ",
				"COUNT(".$this->dx('statements.transaction_type').") as count ",
				$this->dx('statements.transaction_date')." as transaction_date ",
				"COUNT(".$this->dx('statements.transaction_date').")",
				$this->dx('statements.deposit_id')." as deposit_id ",
				"COUNT(".$this->dx('statements.deposit_id').")",
				$this->dx('statements.group_id')." as group_id ",
				"COUNT(".$this->dx('statements.group_id').")",
				$this->dx('statements.member_id')." as member_id ",
				"COUNT(".$this->dx('statements.member_id').")",
				$this->dx('statements.created_on')." as created_on ",
				"COUNT(".$this->dx('statements.created_on').")",
				$this->dx('statements.invoice_id')." as invoice_id ",
				"COUNT(".$this->dx('statements.invoice_id').")",
				$this->dx('statements.contribution_from_id')." as contribution_from_id ",
				"COUNT(".$this->dx('statements.contribution_from_id').")",
				$this->dx('statements.fine_category_to_id')." as fine_category_to_id ",
				"COUNT(".$this->dx('statements.fine_category_to_id').")",
			)
		);
		$this->db->where($this->dx('statements.active')." = 1 ",NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('statements.group_id')." = ".$group_id." ",NULL,FALSE);
		}
		$this->db->group_by(
			array(
				$this->dx('statements.transaction_type'),
				$this->dx('statements.transaction_date'),
				$this->dx('statements.deposit_id'),
				$this->dx('statements.group_id'),
				$this->dx('statements.member_id'),
				$this->dx('statements.created_on'),
				$this->dx('statements.invoice_id'),
				$this->dx('statements.contribution_from_id'),
				$this->dx('statements.fine_category_to_id'),
			)
		);
		$this->db->having("(COUNT(".$this->dx('statements.transaction_type').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.transaction_date').") > 1)",NULL,FALSE);
		//$this->db->having("(COUNT(".$this->dx('statements.deposit_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.group_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.member_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.contribution_from_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.fine_category_to_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.created_on').") > 1)",NULL,FALSE);
		$this->db->order_by($this->dx('statements.transaction_date'),'DESC',FALSE);
		$statement_entries = $this->db->get('statements')->result();
		return $statement_entries;
	}

	function find_duplicate_contribution_refund_statement_entries($group_id = 0){
		$this->db->select(
			array(
				$this->dx('statements.transaction_type')." as transaction_type ",
				"COUNT(".$this->dx('statements.transaction_type').") as count ",
				$this->dx('statements.transaction_date')." as transaction_date ",
				"COUNT(".$this->dx('statements.transaction_date').")",
				$this->dx('statements.deposit_id')." as deposit_id ",
				"COUNT(".$this->dx('statements.deposit_id').")",
				$this->dx('statements.group_id')." as group_id ",
				"COUNT(".$this->dx('statements.group_id').")",
				$this->dx('statements.member_id')." as member_id ",
				"COUNT(".$this->dx('statements.member_id').")",
				$this->dx('statements.created_on')." as created_on ",
				"COUNT(".$this->dx('statements.created_on').")",
				$this->dx('statements.invoice_id')." as invoice_id ",
				"COUNT(".$this->dx('statements.invoice_id').")",
				$this->dx('statements.contribution_from_id')." as contribution_from_id ",
				"COUNT(".$this->dx('statements.contribution_from_id').")",
				$this->dx('statements.contribution_to_id')." as contribution_to_id ",
				"COUNT(".$this->dx('statements.contribution_to_id').")",
				$this->dx('statements.refund_id')." as refund_id ",
				"COUNT(".$this->dx('statements.refund_id').")",
			)
		);
		$this->db->where($this->dx('statements.active')." = 1 ",NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('statements.group_id')." = ".$group_id." ",NULL,FALSE);
		}
		$this->db->group_by(
			array(
				$this->dx('statements.transaction_type'),
				$this->dx('statements.transaction_date'),
				$this->dx('statements.deposit_id'),
				$this->dx('statements.group_id'),
				$this->dx('statements.member_id'),
				$this->dx('statements.created_on'),
				$this->dx('statements.invoice_id'),
				$this->dx('statements.contribution_from_id'),
				$this->dx('statements.contribution_to_id'),
				$this->dx('statements.refund_id'),
			)
		);
		$this->db->having("(COUNT(".$this->dx('statements.transaction_type').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.transaction_date').") > 1)",NULL,FALSE);
		//$this->db->having("(COUNT(".$this->dx('statements.deposit_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.group_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.member_id').") > 1)",NULL,FALSE);
		// $this->db->having("(COUNT(".$this->dx('statements.contribution_from_id').") > 1)",NULL,FALSE);
		// $this->db->having("(COUNT(".$this->dx('statements.contribution_to_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.created_on').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.refund_id').") > 1)",NULL,FALSE);
		$this->db->order_by($this->dx('statements.transaction_date'),'DESC',FALSE);
		$statement_entries = $this->db->get('statements')->result();
		return $statement_entries;
	}



	function get_group_member_latest_statement_entries($group_ids = array(),$member_ids = array(),$date = 0){
    	if($date){
	    	$this->db->select(
				array(
					" MAX(id) as id "
				)
			);
			if(empty($group_ids)){
				$this->db->where($this->dx('group_id').' IN ( 0 ) ',NULL,FALSE);
			}else{
				$this->db->where($this->dx('group_id').' IN ( '.implode(",",$group_ids)." ) ",NULL,FALSE);
			}
			if(empty($member_ids)){
				$this->db->where($this->dx('member_id').' IN ( 0 ) ',NULL,FALSE);
			}else{
				$this->db->where($this->dx('member_id').' IN ( '.implode(",",$member_ids)." ) ",NULL,FALSE);
			}
			$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
			if(empty($this->transactions->paid_transaction_types_array)||empty($this->transactions->payable_transaction_types_array)){
				$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
			}else{
				$transaction_types_array = array_merge($this->transactions->paid_transaction_types_array,$this->transactions->payable_transaction_types_array);
				$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
			}
			$this->db->group_by(
	        	array(
	        		$this->dx('member_id'),
	        		$this->dx('group_id')
	        	)
	        );
	        if($date){
				$date -= (86400*30);
				$date = strtotime(date('d-m-Y',$date));
				$this->db->where($this->dx('transaction_date').' < '.$date .' ',NULL,FALSE);
			}else{
				$this->db->where(' id = 0 ',NULL,FALSE);
			}
	        //$this->db->order_by('id','DESC',FALSE);

	        $statements = $this->db->get('statements')->result();

	        $statement_ids_array = array();

	        foreach($statements as $statement):
	        	$statement_ids_array[] = $statement->id;
	        endforeach;

			$this->select_all_secure('statements');
			if(empty($statement_ids_array)){
				$this->db->where_in('id','0');
			}else{
				$this->db->where_in('id',$statement_ids_array);
			}
			if(empty($group_ids)){
				$this->db->where($this->dx('group_id').' IN ( 0 ) ',NULL,FALSE);
			}else{
				$this->db->where($this->dx('group_id').' IN ( '.implode(",",$group_ids)." ) ",NULL,FALSE);
			}
			$statement_entries = $this->db->get('statements')->result();
			$arr = array();

			foreach($statement_entries as $statement_entry):
				$arr[$statement_entry->group_id][$statement_entry->member_id] = $statement_entry;
			endforeach;

			return $arr;
		}else{
			return array();
		}
    }


    function get_group_member_contribution_latest_statement_entries($group_ids = array(),$member_ids = array(),$date = 0){
    	if($date){
	    	$this->db->select(
				array(
					" MAX(id) as id "
				)
			);
			if(empty($group_ids)){
				$this->db->where($this->dx('group_id').' IN ( 0 ) ',NULL,FALSE);
			}else{
				$this->db->where($this->dx('group_id').' IN ( '.implode(",",$group_ids)." ) ",NULL,FALSE);
			}
			if(empty(array_filter($member_ids))){
				$this->db->where($this->dx('member_id').' IN ( 0 ) ',NULL,FALSE);
			}else{
				$this->db->where($this->dx('member_id').' IN ( '.implode(",",array_filter($member_ids))." ) ",NULL,FALSE);
			}
			$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
			if(empty($this->transactions->paid_transaction_types_array)||empty($this->transactions->payable_transaction_types_array)){
				$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
			}else{
				$transaction_types_array = array_merge($this->transactions->paid_transaction_types_array,$this->transactions->payable_transaction_types_array);
				$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
			}
			$this->db->group_by(
	        	array(
	        		$this->dx('member_id'),
	        		$this->dx('group_id'),
	        		$this->dx('contribution_id'),
	        	)
	        );

	        if($date){
				$date -= (86400*30);
				$date = strtotime(date('d-m-Y',$date));
				$this->db->where($this->dx('transaction_date').' < '.$date .' ',NULL,FALSE);
			}else{
				$this->db->where(' id = 0 ',NULL,FALSE);
			}

	        $this->db->order_by('id','DESC',FALSE);

	        $statements = $this->db->get('statements')->result();

	        $statement_ids_array = array();

	        foreach($statements as $statement):
	        	$statement_ids_array[] = $statement->id;
	        endforeach;

			$this->select_all_secure('statements');
			if(empty($statement_ids_array)){
				$this->db->where_in('id','0');
			}else{
				$this->db->where_in('id',$statement_ids_array);
			}
			if(empty($group_ids)){
				$this->db->where($this->dx('group_id').' IN ( 0 ) ',NULL,FALSE);
			}else{
				$this->db->where($this->dx('group_id').' IN ( '.implode(",",$group_ids)." ) ",NULL,FALSE);
			}
			$statement_entries = $this->db->get('statements')->result();

			$arr = array();

			foreach($statement_entries as $statement_entry):
				$arr[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] = $statement_entry;
			endforeach;

			return $arr;
		}else{
			return array();
		}
    }

    function get_group_member_fine_latest_statement_entries($group_ids = array(),$member_ids = array(),$date = 0){
    	if($date){
	    	$this->db->select(
				array(
					" MAX(id) as id "
				)
			);
			if(empty($group_ids)){
				$this->db->where($this->dx('group_id').' IN ( 0 ) ',NULL,FALSE);
			}else{
				$this->db->where($this->dx('group_id').' IN ( '.implode(",",$group_ids)." ) ",NULL,FALSE);
			}
			if(empty($member_ids)){
				$this->db->where($this->dx('member_id').' IN ( 0 ) ',NULL,FALSE);
			}else{
				$this->db->where($this->dx('member_id').' IN ( '.implode(",",$member_ids)." ) ",NULL,FALSE);
			}
			$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
			if(empty($this->transactions->fine_payable_transaction_types_array)||empty($this->transactions->fine_paid_transaction_types_array)){
				$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
			}else{
				$transaction_types_array = array_merge($this->transactions->fine_payable_transaction_types_array,$this->transactions->fine_paid_transaction_types_array);
				$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
			}
			$this->db->group_by(
	        	array(
	        		$this->dx('member_id'),
	        		$this->dx('group_id')
	        	)
	        );
	        if($date){
				$date -= (86400*7);
				$date = strtotime(date('d-m-Y',$date));
				$this->db->where($this->dx('transaction_date').' < '.$date .' ',NULL,FALSE);
			}else{
				$this->db->where(' id = 0 ',NULL,FALSE);
			}
	        //$this->db->order_by('id','DESC',FALSE);

	        $statements = $this->db->get('statements')->result();

	        $statement_ids_array = array();

	        foreach($statements as $statement):
	        	$statement_ids_array[] = $statement->id;
	        endforeach;

			$this->select_all_secure('statements');
			if(empty($statement_ids_array)){
				$this->db->where_in('id','0');
			}else{
				$this->db->where_in('id',$statement_ids_array);
			}
			if(empty($group_ids)){
				$this->db->where($this->dx('group_id').' IN ( 0 ) ',NULL,FALSE);
			}else{
				$this->db->where($this->dx('group_id').' IN ( '.implode(",",$group_ids)." ) ",NULL,FALSE);
			}
			$statement_entries = $this->db->get('statements')->result();
			$arr = array();

			foreach($statement_entries as $statement_entry):
				$arr[$statement_entry->group_id][$statement_entry->member_id] = $statement_entry;
			endforeach;

			return $arr;
		}else{
			return array();
		}
    }

    function get_group_member_contribution_fine_latest_statement_entries($group_ids = array(),$member_ids = array(),$date = 0){
    	if($date){
	    	$this->db->select(
				array(
					" MAX(id) as id "
				)
			);
			if(empty($group_ids)){
				$this->db->where($this->dx('group_id').' IN ( 0 ) ',NULL,FALSE);
			}else{
				$this->db->where($this->dx('group_id').' IN ( '.implode(",",$group_ids)." ) ",NULL,FALSE);
			}
			if(empty(array_filter($member_ids))){
				$this->db->where($this->dx('member_id').' IN ( 0 ) ',NULL,FALSE);
			}else{
				$this->db->where($this->dx('member_id').' IN ( '.implode(",",array_filter($member_ids))." ) ",NULL,FALSE);
			}
			$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
			if(empty($this->transactions->fine_paid_transaction_types_array)||empty($this->transactions->fine_payable_transaction_types_array)){
				$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
			}else{
				$transaction_types_array = array_merge($this->transactions->fine_paid_transaction_types_array,$this->transactions->fine_payable_transaction_types_array);
				$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
			}
			$this->db->where($this->dx('contribution_id').' > 0',NULL,FALSE);
			$this->db->group_by(
	        	array(
	        		$this->dx('member_id'),
	        		$this->dx('group_id'),
	        		$this->dx('contribution_id'),
	        	)
	        );

	        if($date){
				$date -= (86400*7);
				$date = strtotime(date('d-m-Y',$date));
				$this->db->where($this->dx('transaction_date').' < '.$date .' ',NULL,FALSE);
			}else{
				$this->db->where(' id = 0 ',NULL,FALSE);
			}

	        $this->db->order_by('id','DESC',FALSE);

	        $statements = $this->db->get('statements')->result();

	        $statement_ids_array = array();

	        foreach($statements as $statement):
	        	$statement_ids_array[] = $statement->id;
	        endforeach;

			$this->select_all_secure('statements');
			if(empty($statement_ids_array)){
				$this->db->where_in('id','0');
			}else{
				$this->db->where_in('id',$statement_ids_array);
			}
			if(empty($group_ids)){
				$this->db->where($this->dx('group_id').' IN ( 0 ) ',NULL,FALSE);
			}else{
				$this->db->where($this->dx('group_id').' IN ( '.implode(",",$group_ids)." ) ",NULL,FALSE);
			}
			$statement_entries = $this->db->get('statements')->result();

			$arr = array();

			foreach($statement_entries as $statement_entry):
				$arr[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] = $statement_entry;
			endforeach;

			return $arr;
		}else{
			return array();
		}
    }

    function get_group_member_fine_category_latest_statement_entries($group_ids = array(),$member_ids = array(),$date = 0){
    	if($date){
	    	$this->db->select(
				array(
					" MAX(id) as id "
				)
			);
			if(empty($group_ids)){
				$this->db->where($this->dx('group_id').' IN ( 0 ) ',NULL,FALSE);
			}else{
				$this->db->where($this->dx('group_id').' IN ( '.implode(",",$group_ids)." ) ",NULL,FALSE);
			}
			if(empty(array_filter($member_ids))){
				$this->db->where($this->dx('member_id').' IN ( 0 ) ',NULL,FALSE);
			}else{
				$this->db->where($this->dx('member_id').' IN ( '.implode(",",array_filter($member_ids))." ) ",NULL,FALSE);
			}
			$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
			if(empty($this->transactions->fine_paid_transaction_types_array)||empty($this->transactions->fine_payable_transaction_types_array)){
				$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
			}else{
				$transaction_types_array = array_merge($this->transactions->fine_paid_transaction_types_array,$this->transactions->fine_payable_transaction_types_array);
				$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
			}
			$this->db->where($this->dx('fine_category_id').' > 0',NULL,FALSE);
			$this->db->group_by(
	        	array(
	        		$this->dx('member_id'),
	        		$this->dx('group_id'),
	        		$this->dx('fine_category_id'),
	        	)
	        );

	        if($date){
				$date -= (86400*7);
				$date = strtotime(date('d-m-Y',$date));
				$this->db->where($this->dx('transaction_date').' < '.$date .' ',NULL,FALSE);
			}else{
				$this->db->where(' id = 0 ',NULL,FALSE);
			}

	        $this->db->order_by('id','DESC',FALSE);

	        $statements = $this->db->get('statements')->result();

	        $statement_ids_array = array();

	        foreach($statements as $statement):
	        	$statement_ids_array[] = $statement->id;
	        endforeach;

			$this->select_all_secure('statements');
			if(empty($statement_ids_array)){
				$this->db->where_in('id','0');
			}else{
				$this->db->where_in('id',$statement_ids_array);
			}
			if(empty($group_ids)){
				$this->db->where($this->dx('group_id').' IN ( 0 ) ',NULL,FALSE);
			}else{
				$this->db->where($this->dx('group_id').' IN ( '.implode(",",$group_ids)." ) ",NULL,FALSE);
			}
			$statement_entries = $this->db->get('statements')->result();

			$arr = array();

			foreach($statement_entries as $statement_entry):
				$arr[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->fine_category_id] = $statement_entry;
			endforeach;

			return $arr;
		}else{
			return array();
		}
    }

    function get_group_total_contributions_paid($group_id = 0){
		$this->db->select(
			array(
				" MAX(id) as id "
			)
		);
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->paid_transaction_types_array)||empty($this->transactions->payable_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->paid_transaction_types_array,$this->transactions->payable_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->group_by(
        	array(
        		$this->dx('member_id')
        	)
        );
        $this->db->order_by('id','DESC',FALSE);
        $statements = $this->db->get('statements')->result();
        
        $statement_ids_array = array();

        foreach($statements as $statement):
        	$statement_ids_array[] = $statement->id;
        endforeach;

        $this->db->select(
			array(
				"SUM(".$this->dx('cumulative_paid').") as cumulative_paid ",
			)
		);
		if(empty($statement_ids_array)){
			$this->db->where_in('id','0');
		}else{
			$this->db->where_in('id',$statement_ids_array);
		}
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->paid_transaction_types_array)||empty($this->transactions->payable_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = array_merge($this->transactions->paid_transaction_types_array,$this->transactions->payable_transaction_types_array);
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->limit(1);
		return $this->db->get('statements')->row()->cumulative_paid;

	}

	function get_group_total_fines_paid($group_id = 0){
		$this->db->select(
			array(
				" MAX(id) as id "
			)
		);
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = $this->transactions->fine_paid_transaction_types_array;
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		$this->db->group_by(
        	array(
        		$this->dx('member_id')
        	)
        );
        $this->db->order_by('id','DESC',FALSE);
        $statements = $this->db->get('statements')->result();
        
        $statement_ids_array = array();

        foreach($statements as $statement):
        	$statement_ids_array[] = $statement->id;
        endforeach;


		$this->db->select(
			array(
				"SUM( ".$this->dx('cumulative_paid').") as cumulative_paid ",
			)
		);
		if(empty($statement_ids_array)){
			$this->db->where_in('id','0');
		}else{
			$this->db->where_in('id',$statement_ids_array);
		}
		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if(empty($this->transactions->fine_paid_transaction_types_array)){
			$this->db->where($this->dx('transaction_type')." IN (0) ",NULL,FALSE);
		}else{
			$transaction_types_array = $this->transactions->fine_paid_transaction_types_array;
			$this->db->where($this->dx('transaction_type')." IN (".implode(',',$transaction_types_array).") ",NULL,FALSE);
		}
		return $this->db->get('statements')->row()->cumulative_paid;
	}


	function get_group_member_total_contribution_transfers_from_per_year_per_month($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		$this->db->select(
			array(
				" ".$this->dx('amount').' as amount ',
				$this->dx('member_id').' as member_id',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%c') as month ",
			)
		);

		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "25" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		/*$this->db->group_by(
			array(
				'year'
			)
		);*/

		$results = $this->db->get('statements')->result();
		$arr = array();
		$year_arr = array();
		foreach($results as $result):
			$year_arr[$result->member_id] = ''; 
            $arr[$result->member_id][$result->year][$result->month] = 0;
        endforeach;

        foreach ($results as $result):
        	$year_arr[$result->member_id] = $result->year;
            $arr[$result->member_id][$result->year][$result->month] += currency($result->amount);
        endforeach;
        $transfers_per_year = array();
        foreach ($arr as $key => $transfers_out) {
        	$transfers_per_year[] = (object) array(
        		'year'=>$year_arr[$key],
        		'amount'=>$arr[$key]
        	);
        }

		return $arr;
	}

	function get_group_member_total_contribution_transfers_to_per_year_per_month($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		$arr = array();
		$this->db->select(
			array(
				" ".$this->dx('amount').' as amount ',
				$this->dx('member_id').' as member_id',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%c') as month ",
			)
		);
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "26" ',NULL,FALSE);
		if($group_id){

			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		/*$this->db->group_by(
			array(
				'year'
			)
		);*/
		$results = $this->db->get('statements')->result();

		$year_arr = array();
		foreach($results as $result):
			$year_arr[$result->member_id] = ''; 
            $arr[$result->member_id][$result->year][$result->month] = 0;
        endforeach;

        foreach ($results as $result):
        	$year_arr[$result->member_id] = $result->year;
            $arr[$result->member_id][$result->year][$result->month] += currency($result->amount);
        endforeach;
        $transfers_per_year = array();
        foreach ($arr as $key => $transfers_out) {
        	$transfers_per_year[] = (object) array(
        		'year'=>$year_arr[$key],
        		'amount'=>$arr[$key]
        	);
        }

		return $arr;
	}

	function get_group_member_total_contribution_transfers_from_contribution_to_fine_category_per_year($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		$this->db->select(
			array(
				' '.$this->dx('amount').' as amount ',
				$this->dx('member_id').' as member_id',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%c') as month ",
			)
		);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "27" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'" ',NULL,FALSE);
		}
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'" ',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date').' <="'.$to.'" ',NULL,FALSE);
		}
        /*$this->db->group_by(
			array(
				'year'
			)
		);*/
		//return $this->db->get('statements')->result();
		$results = $this->db->get('statements')->result();
		$arr = array();
		$year_arr = array();
		$transfers_to_fine_per_year = array();
		foreach($results as $result):
			$year_arr[$result->member_id] = ''; 
            $arr[$result->member_id][$result->year] = 0;
        endforeach;
        foreach ($results as $result):
        	$year_arr[$result->member_id] = $result->year;
            $arr[$result->member_id][$result->year] += currency($result->amount);
        endforeach;
        
        foreach ($arr as $key => $transfers_out) {
        	$transfers_to_fine_per_year[] = (object) array(
        		'year'=>$year_arr[$key],
        		'amount'=>$arr[$key]
        	);
        }
		return $arr;
	}

	function get_group_member_total_contribution_transfers_to_loan_per_year($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		$this->db->select(
			array(
				" ".$this->dx('amount').' as amount ',
				$this->dx('member_id').' as member_id',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%c') as month ",
			)
		);

		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').' = "30" ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		/*$this->db->group_by(
			array(
				'year'
			)
		);*/
		$results = $this->db->get('statements')->result();

		$arr = array();
		$year_arr = array();
		$transfers_to_loan_per_year = array();
		foreach($results as $result):
			$year_arr[$result->member_id] = ''; 
            $arr[$result->member_id][$result->year] = 0;
        endforeach;

        foreach ($results as $result):
        	$year_arr[$result->member_id] = $result->year;
            $arr[$result->member_id][$result->year] += currency($result->amount);
        endforeach;
        
        foreach ($arr as $key => $transfers_out) {
        	$transfers_to_loan_per_year[] = (object) array(
        		'year'=>$year_arr[$key],
        		'amount'=>$arr[$key]
        	);
        }

		return $arr;
	}

	function find_duplicate_dividend_payout_statement_entries($group_id=0){
		$this->db->select(
			array(
				$this->dx('statements.transaction_type')." as transaction_type ",
				"COUNT(".$this->dx('statements.transaction_type').") as count ",
				$this->dx('statements.transaction_date')." as transaction_date ",
				"COUNT(".$this->dx('statements.transaction_date').")",
				$this->dx('statements.contribution_id')." as contribution_id ",
				"COUNT(".$this->dx('statements.contribution_id').")",
				$this->dx('statements.withdrawal_id')." as withdrawal_id ",
				"COUNT(".$this->dx('statements.withdrawal_id').")",
				$this->dx('statements.group_id')." as group_id ",
				"COUNT(".$this->dx('statements.group_id').")",
				$this->dx('statements.member_id')." as member_id ",
				"COUNT(".$this->dx('statements.member_id').")",
				$this->dx('statements.created_on')." as created_on ",
				"COUNT(".$this->dx('statements.created_on').")",
			)
		);
		$this->db->where($this->dx('statements.active')." = 1 ",NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('statements.group_id')." = ".$group_id." ",NULL,FALSE);
		}
		$this->db->group_by(
			array(
				$this->dx('statements.transaction_type'),
				$this->dx('statements.transaction_date'),
				$this->dx('statements.contribution_id'),
				$this->dx('statements.withdrawal_id'),
				$this->dx('statements.group_id'),
				$this->dx('statements.member_id'),
				$this->dx('statements.created_on'),
			)
		);
		$this->db->having("(COUNT(".$this->dx('statements.transaction_type').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.transaction_date').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.contribution_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.withdrawal_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.group_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.member_id').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('statements.created_on').") > 1)",NULL,FALSE);
		$this->db->order_by($this->dx('statements.transaction_date'),'DESC',FALSE);
		$statement_entries = $this->db->get('statements')->result();
		return $statement_entries;
	}

	function get_duplicate_dividend_payout_statements($group_ids = array(),$member_ids = array(),$transaction_types = array(),$transaction_dates = array(),$withdrawal_ids = array()){
		$this->db->select(
			array(
				'id',
				$this->dx('group_id')." as group_id ",
				$this->dx('member_id')." as member_id ",
				$this->dx('transaction_date')." as transaction_date ",
				$this->dx('transaction_type')." as transaction_type ",
				$this->dx('withdrawal_id')." as withdrawal_id ",
			)
		);
		$this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." IN ( ".implode(',',array_unique($group_ids))." ) ",NULL,FALSE);
		}
		if(empty($member_ids)){
			$this->db->where($this->dx('member_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id')." IN ( ".implode(',',array_unique($member_ids))." ) ",NULL,FALSE);
		}
		if(empty($transaction_types)){
			$this->db->where($this->dx('transaction_type')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('transaction_type')." IN ( ".implode(',',array_unique($transaction_types))." ) ",NULL,FALSE);
		}
		if(empty(array_filter($withdrawal_ids))){
			$this->db->where($this->dx('withdrawal_id')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('withdrawal_id')." IN ( ".implode(',',array_unique(array_filter($withdrawal_ids)))." ) ",NULL,FALSE);
		}
		if(empty($transaction_dates)){
			$this->db->where($this->dx('transaction_date')." = 0 ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('transaction_date')." IN ( ".implode(',',array_unique($transaction_dates))." ) ",NULL,FALSE);
		}
		return $this->db->get('statements')->result();
	}
	function get_all_group_deposit_statement_entries($group_id = 0){
		$this->select_all_secure('statements');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
    	$this->db->where($this->dx('transaction_type').' IN (9,10,11,15,26,21,22,23,24,25,27,30)',NULL,FALSE);
		return $this->db->get('statements')->result();
	}
	function get_all_group_invoice_statement_entries($group_id = 0){
		$this->select_all_secure('statements');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
    	$this->db->where($this->dx('transaction_type').' IN (1,2,3,4)',NULL,FALSE);
		return $this->db->get('statements')->result();
	}
	function get_all_statements_updated_yesterday($limit=500,$group_id=0){
    	$this->select_all_secure('statements');
    	$this->db->where($this->dx('active').' ="0" ',NULL,FALSE);
    	$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
    	$this->db->where($this->dx('modified_on').' >="'.strtotime('30-03-2022 16:30:00').'"',NULL,FALSE);
    	$this->db->where($this->dx('modified_on').' <="'.strtotime('30-03-2022 20:30:00').'"',NULL,FALSE);
    	$this->db->where($this->dx('modified_by').'="0"',NULL,FALSE);
    	//$this->db->limit($limit);
    	return $this->db->get('statements')->result();
    }
}