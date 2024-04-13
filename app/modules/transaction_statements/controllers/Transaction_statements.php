<?php
class Transaction_statements extends Public_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('transaction_statements/transaction_statements_m');
		$this->load->model('loan_repayments/loan_repayments_m');
		$this->load->model('loans/loans_m');
		$this->load->model('deposits/deposits_m');

		$this->load->library('transactions');
	}

	public function fix(){
		$transactions_with_account_id_error = $this->transaction_statements_m->get_all_transactions_with_account_id_error();
		foreach ($transactions_with_account_id_error as $transaction_with_account_id_error) {
			// print_r($transaction_with_account_id_error->account_id);
			$new_account_id = str_replace('bank-bank','bank',$transaction_with_account_id_error->account_id);
			
			$id = $transaction_with_account_id_error->id;
			$input = array(
				'account_id' => $new_account_id,
			);

			if($this->transaction_statements_m->update_transactions_with_account_id_error($id,$input)){
				echo 'updated '.$transaction_with_account_id_error->account_id.' to '.$new_account_id;
				echo "\n";
			}else{
				echo 'Error updating '.$transaction_with_account_id_error->account_id.' to '.$new_account_id.'<br>';
				echo "\n";
			}
			
		}
	}

	function fix_all_statements(){
		$statements = array();
		$loan_repayments = $this->loan_repayments_m->get_all_loan_repayments();
		if($loan_repayments){
			$ids = '';
			foreach ($loan_repayments as $loan_repayment) {
				if($ids){
					$ids.=','.$loan_repayment->id;
				}else{
					$ids=$loan_repayment->id;
				}
			}

			if($ids){
				$statements = $this->transaction_statements_m->get_transaction_statements_by_loan_repayment_id($ids);
			}
		}

		$loans = $this->loans_m->get_all_loans();
		if($loans){
			$loan_ids = '';
			foreach ($loans as $loan) {
				if($loan_ids){
					$loan_ids.=','.$loan->id;
				}else{
					$loan_ids=$loan->id;
				}
			}

			if($loan_ids){
				$statements = $this->transaction_statements_m->get_transaction_statements_by_loan_id($loan_ids);
			}
		}

		$deposits = $this->deposits_m->get_all_voided_deposits();
		if($deposits){
			$deposit_ids = '';
			foreach ($deposits as $deposit) {
				if($deposit_ids){
					$deposit_ids.=','.$deposit->id;
				}else{
					$deposit_ids=$deposit->id;
				}
			}
			if($deposit_ids){
				$statements = $this->transaction_statements_m->get_transaction_statements_by_deposit_id($deposit_ids);
			}
		}
		$total_amounts = array();
		$success = 0;
		$fails = 0;
		if($statements){
			foreach ($statements as $statement) {
				$id = $statement->id;
				$group_id = $statement->group_id;
				$transaction_type = $statement->transaction_type;
				$account_id = $statement->account_id;
				$amount = $statement->amount;
				//if($group_id == 5407){
					// if($this->transactions->fix_voided_transactions($id,$group_id,$transaction_type,$account_id,$amount)){
					// 	if(array_key_exists($group_id, $total_amounts)){
					// 		$total_amounts[$group_id]+=$amount;
					// 	}else{
					// 		$total_amounts[$group_id]=$amount;
					// 	}
					// 	++$success;
					// }else{
					// 	++$fails;
					// }
				//}
				++$success;
				if(array_key_exists($group_id, $total_amounts)){
					$total_amounts[$group_id]+=$amount;
				}else{
					$total_amounts[$group_id]=$amount;
				}
			}
		}else{
			echo 'No records are available to fix <br/>';
		}
		if($success){
			echo 'Fixed '.$success.' transactions totaling '.number_to_currency(array_sum($total_amounts)).'<br/>';
			print_r('<pre>');
			print_r($total_amounts);
			print_r('</pre>');
		}

		if($fails){
			echo 'Could not fix '.$fails.' transactions <br/>';
		}
	}


    function fix_orphan_transaction_statements(){
    	$this->transactions->fix_orphan_transaction_statements();
    }
}