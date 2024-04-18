<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Transactions_m extends MY_Model {

	protected $_table = 'transactions';

	function __construct(){
		parent::__construct();
		$this->load->dbforge();
		$this->load->library('transactions');
		$this->install();
	}

	/**

	payment_transactions types 

	1 - payment
	2 - Payment Reversal
	3 - Disbursement
	4 - Disbursement reversal

	***/
	public $payment_transaction_types = array(
		1 => 'DEPOSIT',
		2 => 'PAYMENT REVERSAL',
		3 => 'DISBURSEMENT',
		4 => 'DISBURSEMENT REVERSAL',
		5 => 'TRANSACTION CHARGES',
		6 => 'INCOME',
		7 => 'INCOMING FUNDS TRANSFER',
		8 => 'OUTGOING FUNDS TRANSFER',
		9 => 'REVERSAL: OUTGOING FUNDS TRANSFER',
		10 => 'REVERSAL: INCOMING FUNDS TRANSFER',
		11 => 'B2C DISBURSEMENT CHARGES',
		12 => 'INCOME FROM WITHDRAWAL CHARGES'
	);

	public $deposit_transaction_types = array(
		1,4,6,7,9,12
	);

	public $withdrawal_transaction_types = array(
		2,3,5,8,10,11
	);

	protected $deposit_transaction_types_list = '1,4,6,7,9,12';
	protected $withdrawal_transaction_types_list = '2,3,5,8,10,11';

	public function install(){
		$this->db->query("
			create table if not exists transaction_statements(
				id int not null auto_increment primary key,
				`transaction_type` blob,
				`transaction_particulars` blob,
				`transaction_id` blob,
				`account_id` blob,
				`amount` blob,
				`balance` blob,
				`transaction_date` blob,
				`transaction_channel` blob,
				`deposit_id` blob,
				`withdrawal_id` blob,
				`active` blob,
				created_by blob,
				created_on blob,
				modified_on blob,
				modified_by blob
		)");

		$this->db->query("
			create table if not exists payment_transactions(
				id int not null auto_increment primary key,
				`type` blob,
				`channel` blob,
				`amount` blob,
				`reference_number` blob,
				`status` blob,
				`active` blob,
				`response_code` blob,
				`response_description` blob,
				`result_code` blob,
				`result_description` blob,
				`account_id` blob,
				`phone_number` blob,
				created_by blob,
				created_on blob,
				modified_on blob,
				modified_by blob
		)");
	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('transaction_statements',$input);
	}

	function get_statement_by_deposit_id($deposit_id=0){
		$this->select_all_secure('transaction_statements');
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('deposit_id').' = "'.$deposit_id.'"',NULL,FALSE);
		return $this->db->get('transaction_statements')->row();
	}


	function update($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'transaction_statements',$input);
	}

	function get_account_statement_balance($account_id=0,$transaction_date=0){
		$total_deposits = 0;
		$total_withdrawals = 0;
		$this->db->select(array(
			'SUM('.$this->dx('amount').') as total_deposits',
		));
		$this->db->where($this->dx('transaction_type').' IN('.$this->deposit_transaction_types_list.')', NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('account_id').' = "'.$account_id.'"',NULL,FALSE);
		if($transaction_date){
			$this->db->where($this->dx('transaction_date').' <= "'.$transaction_date.'"',NULL,FALSE);
		}
		$deposits = $this->db->get('transaction_statements')->row();
		if($deposits){
			$total_deposits = $deposits->total_deposits;
		}

		$this->db->select(array(
			'SUM('.$this->dx('amount').') as total_withdrawals',
		));
		$this->db->where($this->dx('transaction_type').' IN('.$this->withdrawal_transaction_types_list.')', NULL,FALSE);
		$this->db->where($this->dx('account_id').' = "'.$account_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($transaction_date){
			$this->db->where($this->dx('transaction_date').' <= "'.$transaction_date.'"',NULL,FALSE);
		}
		$withdrawals = $this->db->get('transaction_statements')->row();
		if($withdrawals){
			$total_withdrawals = $withdrawals->total_withdrawals;
		}
		return ($total_deposits-$total_withdrawals);
	}

	function get_all(){
		$this->select_all_secure('transaction_statements');
    	return $this->db->get('transaction_statements')->result();
	}

	function get_account_statement($account_id=0,$from=0,$to=0){
		$this->select_all_secure('transaction_statements');
		if($from&&$to){
			$this->db->where($this->dx('transaction_date').' >= "'.$from.'"',NULL,FALSE);
			$this->db->where($this->dx('transaction_date').' <= "'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('account_id').' = "'.$account_id.'"',NULL,FALSE);
		$this->db->order_by($this->dx('transaction_date'), 'ASC', FALSE);
		$this->db->order_by($this->dx('created_on'), 'ASC', FALSE);
		$this->db->order_by('id', 'ASC', FALSE);
    	return $this->db->get('transaction_statements')->result();
	}

	function get_all_transaction_entries($types = '0'){
		$this->select_all_secure('transaction_statements');
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($types){
			$this->db->where($this->dx('transaction_type').' IN('.$types.')', NULL,FALSE);
		}
		$this->db->order_by($this->dx('transaction_date'), 'ASC', FALSE);
		$this->db->order_by($this->dx('created_on'), 'ASC', FALSE);
		$this->db->order_by('id', 'ASC', FALSE);
    	return $this->db->get('transaction_statements')->result();
	}

	function get_total_group_balances($account_id=0,$from=0){
		$this->db->select(array(
			' SUM('.$this->dx('amount').') as sum',
		));
		if($from){
			$this->db->where($this->dx('transaction_date').' < "'.$from.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('transaction_type').' IN('.$this->withdrawal_transaction_types_list.')',NULL,FALSE);
		$this->db->where($this->dx('account_id').' = "'.$account_id.'"',NULL,FALSE);
    	$withdrawals =  $this->db->get('transaction_statements')->row()->sum;
    	$this->db->select(array(
			' SUM('.$this->dx('amount').') as sum',
		));
		if($from){
			$this->db->where($this->dx('transaction_date').' < "'.$from.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('transaction_type').' IN('.$this->deposit_transaction_types_list.')',NULL,FALSE);
		$this->db->where($this->dx('account_id').' = "'.$account_id.'"',NULL,FALSE);
    	$deposits =  $this->db->get('transaction_statements')->row()->sum;
    	return $deposits-$withdrawals;
	}

	function count_statement_entries($account_id=0){
		$this->db->where($this->dx('account_id').' = "'.$account_id.'"',NULL,FALSE);
    	return $this->db->count_all_results('transaction_statements');
	}

	function total_deposits_today(){
		$this->db->select(array(
			'SUM('.$this->dx('amount').') as total_deposits',
		));
		$this->db->where($this->dx('transaction_type').' IN('.$this->deposit_transaction_types_list.')', NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') = '" . date('Y d m',time()) . "'", NULL, FALSE);
		return $this->db->get('transaction_statements')->row()->total_deposits;
	}

	function total_withdrawals_today(){
		$this->db->select(array(
			'SUM('.$this->dx('amount').') as total_deposits',
		));
		$this->db->where($this->dx('transaction_type').' IN('.$this->withdrawal_transaction_types_list.')', NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') = '" . date('Y d m',time()) . "'", NULL, FALSE);
		return $this->db->get('transaction_statements')->row()->total_deposits;
	}

	function total_income_received_today(){
		$this->db->select(array(
			'SUM('.$this->dx('amount').') as total_deposits',
		));
		$this->db->where($this->dx('transaction_type').' IN('.$this->deposit_transaction_types_list.')', NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('account_id').' = "1"',NULL,FALSE);
		$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') = '" . date('Y d m',time()) . "'", NULL, FALSE);
		$deposit = $this->db->get('transaction_statements')->row()->total_deposits?:0;

		$this->db->select(array(
			'SUM('.$this->dx('amount').') as total_deposits',
		));
		$this->db->where($this->dx('transaction_type').' IN('.$this->withdrawal_transaction_types_list.')', NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('account_id').' = "1"',NULL,FALSE);
		$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') = '" . date('Y d m',time()) . "'", NULL, FALSE);
		$withdrawals =  $this->db->get('transaction_statements')->row()->total_deposits;
		return $deposit-$withdrawals;
	}

	function total_income_received_from_dates($from=0,$to=0,$account_list=''){
		$this->db->select(array(
			'SUM('.$this->dx('amount').') as total_deposits',
		));

		$this->db->where($this->dx('transaction_type').' IN('.$this->deposit_transaction_types_list.')', NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('account_id').' = "1"',NULL,FALSE);
		$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') = '" . date('Y d m',time()) . "'", NULL, FALSE);
		if($from&&$to){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'"',NULL,FALSE);
			$this->db->where($this->dx('transaction_date').' <="'.$to.'"',NULL,FALSE);
		}		
		$this->db->where($this->dx('account_id').' NOT IN('.$account_list.')',NULL,FALSE);
		$deposit = $this->db->get('transaction_statements')->row()->total_deposits?:0;

		$this->db->select(array(
			'SUM('.$this->dx('amount').') as total_deposits',
		));
		$this->db->where($this->dx('transaction_type').' IN('.$this->withdrawal_transaction_types_list.')', NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('account_id').' = "1"',NULL,FALSE);
		$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') = '" . date('Y d m',time()) . "'", NULL, FALSE);
		if($from&&$to){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'"',NULL,FALSE);
			$this->db->where($this->dx('transaction_date').' <="'.$to.'"',NULL,FALSE);
		}
		
		$this->db->where($this->dx('account_id').' NOT IN('.$account_list.')',NULL,FALSE);
		$withdrawals =  $this->db->get('transaction_statements')->row()->total_deposits;
		return $deposit-$withdrawals;
	}

	function total_deposits($from = '',$to = '',$account_list=''){
		$this->db->select(array(
			'SUM('.$this->dx('amount').') as total_deposits',
		));
		$this->db->where($this->dx('transaction_type').' IN('.$this->deposit_transaction_types_list.')', NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($to){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') <= '" . date('Y d m',$to) . "'", NULL, FALSE);
		}
		if($from){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') >= '" . date('Y d m',$from) . "'", NULL, FALSE);
		}
		if($account_list){
			$this->db->where($this->dx('account_id').' NOT IN('.$account_list.')',NULL,FALSE);
		}
		return $this->db->get('transaction_statements')->row()->total_deposits;
	}

	function get_deposits($from = '',$to = '',$account_list=''){
		$from = strtotime(date('01-m-Y'));
		$this->select_all_secure('transaction_statements');
		$this->db->where($this->dx('transaction_type').' IN('.$this->deposit_transaction_types_list.')', NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($to){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') <= '" . date('Y d m',$to) . "'", NULL, FALSE);
		}
		$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') >= '" . date('Y d m',$from) . "'", NULL, FALSE);
		if($account_list){
			$this->db->where($this->dx('account_id').' NOT IN('.$account_list.')',NULL,FALSE);
		}
		return $this->db->get('transaction_statements')->result();
	}

	function get_withdrawals($from = '',$to = '',$account_list=''){
		$this->select_all_secure('transaction_statements');
		$this->db->where($this->dx('transaction_type').' IN('.$this->withdrawal_transaction_types_list.')', NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($to){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') <= '" . date('Y d m',$to) . "'", NULL, FALSE);
		}
		if($from){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') >= '" . date('Y d m',$from) . "'", NULL, FALSE);
		}
		if($account_list){
			$this->db->where($this->dx('account_id').' NOT IN('.$account_list.')',NULL,FALSE);
		}
		return $this->db->get('transaction_statements')->result();
	}

	function total_withdrawals($from = '',$to = '',$account_list=''){
		$this->db->select(array(
			'SUM('.$this->dx('amount').') as total_deposits',
		));
		$this->db->where($this->dx('transaction_type').' IN('.$this->withdrawal_transaction_types_list.')', NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($to){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') <= '" . date('Y d m',$to) . "'", NULL, FALSE);
		}
		if($from){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') >= '" . date('Y d m',$from) . "'", NULL, FALSE);
		}
		if($account_list){
			$this->db->where($this->dx('account_id').' NOT IN('.$account_list.')',NULL,FALSE);
		}
		return $this->db->get('transaction_statements')->row()->total_deposits;
	}

	function total_income_received($from = '',$to = '',$account_list=''){
		$this->db->select(array(
			'SUM('.$this->dx('amount').') as total_deposits',
		));
		$this->db->where($this->dx('transaction_type').' IN('.$this->deposit_transaction_types_list.')', NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('account_id'),1);
		if($to){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') <= '" . date('Y d m',$to) . "'", NULL, FALSE);
		}
		if($from){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') >= '" . date('Y d m',$from) . "'", NULL, FALSE);
		}
		if($account_list){
			$this->db->where($this->dx('account_id').' NOT IN('.$account_list.')',NULL,FALSE);
		}
		$result = $this->db->get('transaction_statements')->row();

		$deposit = $result->total_deposits?$result->total_deposits:0;

		$this->db->select(array(
			'SUM('.$this->dx('amount').') as total_withdrawals',
		));
		$this->db->where($this->dx('transaction_type').' IN('.$this->withdrawal_transaction_types_list.')', NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('account_id'),1);
		if($to){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') <= '" . date('Y d m',$to) . "'", NULL, FALSE);
		}
		if($from){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') >= '" . date('Y d m',$from) . "'", NULL, FALSE);
		}
		if($account_list){
			$this->db->where($this->dx('account_id').' NOT IN('.$account_list.')',NULL,FALSE);
		}
		$result = $this->db->get('transaction_statements')->row();
		
		$withdrawals = $result->total_withdrawals?$result->total_withdrawals:0;
		return $deposit-$withdrawals;
	}


	function get_default_account_deposits($from = '',$to = ''){
		$this->select_all_secure('transaction_statements');
		$this->db->where($this->dx('transaction_type').' IN('.$this->deposit_transaction_types_list.')', NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('account_id'),1);
		if($to){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') <= '" . date('Y d m',$to) . "'", NULL, FALSE);
		}
		if($from){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') >= '" . date('Y d m',$from) . "'", NULL, FALSE);
		}
		return $this->db->get('transaction_statements')->result();
	}

	function count_number_of_transactions_per_month($from = '',$to = ''){
		//$this->select_all_secure('transaction_statements');
		$this->db->select(array(
            "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%M - %Y') as transaction_date ",
            $this->dx('transaction_type')." as transaction_type",
            'SUM('.$this->dx('amount').') as amount',
            'COUNT(*) as items'
        ));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('transaction_type').'IN(11,12)',NULL,FALSE);
		$this->db->where($this->dx('account_id'),1);
		// if($to){
		// 	$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') <= '" . date('Y d m',$to) . "'", NULL, FALSE);
		// }
		// if($from){
		// 	$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') >= '" . date('Y d m',$from) . "'", NULL, FALSE);
		// }
		$this->db->order_by($this->dx('transaction_date'),'DESC',FALSE);
		$this->db->group_by(
			array(
				"transaction_date","transaction_type"
			)
		);
		$results = $this->db->get('transaction_statements')->result();
		$arr = array();
		// $with = array();
		// $depo = array();
		foreach ($results as $result) {
			if(in_array($result->transaction_type, $this->withdrawal_transaction_types)){
				$arr[$result->transaction_date] = array(
					'amount' => isset($arr[$result->transaction_date])?($arr[$result->transaction_date]['amount'] + $result->amount):$result->amount,
					'items' => isset($arr[$result->transaction_date])?($arr[$result->transaction_date]['items'] + $result->items):$result->items,
				);
			}elseif(in_array($result->transaction_type, $this->deposit_transaction_types)){
				$arr[$result->transaction_date] = array(
					'amount' => isset($arr[$result->transaction_date])?($arr[$result->transaction_date]['amount'] - $result->amount):(0-$result->amount),
					'items' => isset($arr[$result->transaction_date])?($arr[$result->transaction_date]['items'] + $result->items):$result->items,
				);
			}
		}
		// $arr = array(
		// 	"deposits" => $depo,
		// 	"withdrawals" => $with,
		// );

		return $arr;
	}

	function get_default_account_withdrawals($from = '',$to = ''){
		$this->select_all_secure('transaction_statements');
		$this->db->where($this->dx('transaction_type').' IN('.$this->withdrawal_transaction_types_list.')', NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('account_id'),1);
		if($to){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') <= '" . date('Y d m',$to) . "'", NULL, FALSE);
		}
		if($from){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')."),'%Y %d %m') >= '" . date('Y d m',$from) . "'", NULL, FALSE);
		}
		return $this->db->get('transaction_statements')->result();
	}

	function get_transaction_statements($from=0,$to=0){
		$this->select_all_secure('transaction_statements');
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->order_by($this->dx('transaction_date'), 'DESC', FALSE);
		if($from&&$to){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'"',NULL,FALSE);
			$this->db->where($this->dx('transaction_date').' <="'.$to.'"',NULL,FALSE);
		}
		
		$this->db->where($this->dx('account_id').' NOT IN(352,423)',NULL,FALSE);
		return $this->db->get('transaction_statements')->result();
	}

	function get_endpoint_transaction_statements($from=0,$to=0,$account_list=''){
		$this->select_all_secure('transaction_statements');
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->order_by($this->dx('transaction_date'), 'DESC', FALSE);
		if($from&&$to){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'"',NULL,FALSE);
			$this->db->where($this->dx('transaction_date').' <="'.$to.'"',NULL,FALSE);
		}
		
		$this->db->where($this->dx('account_id').' NOT IN('.$account_list.')',NULL,FALSE);
		return $this->db->get('transaction_statements')->result();
	}

	function get_income_endpoint_transaction_statements($from=0,$to=0){
		$this->select_all_secure('transaction_statements');
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->order_by($this->dx('transaction_date'), 'DESC', FALSE);
		if($from&&$to){
			$this->db->where($this->dx('transaction_date').' >="'.$from.'"',NULL,FALSE);
			$this->db->where($this->dx('transaction_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('account_id').' = "1"',NULL,FALSE);
		return $this->db->get('transaction_statements')->result();
	}

	/***************Payment**************************/

	function insert_payment($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('payment_transactions',$input);
	}

	function update_payment($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'payment_transactions',$input);
	}

	function is_unique_reference_number($reference_number = 0,$account_id = 0){
		$this->db->where($this->dx('account_id').' = "'.$account_id.'"',NULL,FALSE);
		$this->db->where($this->dx('reference_number').' = "'.$reference_number.'"',NULL,FALSE);
		return $this->db->count_all_results('payment_transactions')?0:1;
	}

	function get_payment_transaction($reference_number=0,$account_id=0){
		$this->select_all_secure('payment_transactions');
		$this->db->where($this->dx('reference_number').' = "'.$reference_number.'"',NULL,FALSE);
		$this->db->where($this->dx('account_id').' = "'.$account_id.'"',NULL,FALSE);
		return $this->db->get('payment_transactions')->row();
	}

	function get_payment_transaction_by_reference_number($reference_number=0){
		$this->select_all_secure('payment_transactions');
		$this->db->where($this->dx('reference_number').' = "'.$reference_number.'"',NULL,FALSE);
		return $this->db->get('payment_transactions')->result();
	}

	function get_all_payment_transactions(){
		$this->select_all_secure('payment_transactions');
		return $this->db->get('payment_transactions')->result();
	}

	function get_payment_transaction_by_checkout_request_id($checkout_request_id=0){
		$this->select_all_secure('payment_transactions');
		$this->db->where($this->dx('checkout_request_id').' = "'.$checkout_request_id.'"',NULL,FALSE);
		return $this->db->get('payment_transactions')->row();
	}

	function get_payment_transaction_by_originator_conversation_id($merchant_request_id=0){
		$this->select_all_secure('payment_transactions');
		$this->db->where($this->dx('merchant_request_id').' = "'.$merchant_request_id.'"',NULL,FALSE);
		return $this->db->get('payment_transactions')->row();
	}

	function get_payment_transaction_by_transaction_id($transaction_id=0){
		$this->select_all_secure('transaction_statements');
		$this->db->where($this->dx('transaction_id').' = "'.$transaction_id.'"',NULL,FALSE);
		return $this->db->get('transaction_statements')->row();
	}

	function update_transaction_payment($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'transaction_statements',$input);
	}

	

	function get_payment_transaction_by_destination_reference_number($destination_reference_number=0){
		$this->select_all_secure('payment_transactions');
		$this->db->where($this->dx('destination_reference_number').' = "'.$destination_reference_number.'"',NULL,FALSE);
		return $this->db->get('payment_transactions')->row();
	}

	function count_deposits_distribution_by_months($lower_limit='',$upper_limit=''){
		$this->db->select(
			array(
				'count(id) as count',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y %M') as year ",
			)
		);
		$this->db->where($this->dx('transaction_type').' IN(1,4,6,7,9,12)');
		if($lower_limit){
			$this->db->where($this->dx('amount').' >= '.$lower_limit);
		}
		if($upper_limit){
			$this->db->where($this->dx('amount').' <= '.$upper_limit);
		}
		$this->db->group_by(
        	array(
        		'year',
        	)
        );
		return $this->db->get('transaction_statements')->result();
	}

	function count_deposits_by_month(){
		$this->db->select(
			array(
				'count(id) as count',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y %M') as year ",
			)
		);
		$this->db->where($this->dx('transaction_type').' IN(1,4,6,7,9,12)');
		$this->db->group_by(
        	array(
        		'year',
        	)
        );
		return $this->db->get('transaction_statements')->result();
	}

	function get_average_deposit_amount_by_month(){
		$this->db->select(
			array(
				'AVG('.$this->dx('amount').') as average_amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y %M') as year ",
			)
		);
		$this->db->where($this->dx('transaction_type').' IN(1,4,6,7,9,12)');
		$this->db->group_by(
        	array(
        		'year',
        	)
        );
		return $this->db->get('transaction_statements')->result();
	}

	function count_unique_depositing_accounts_by_month(){
		$this->db->select(
			array(
				'count(DISTINCT('.$this->dx('account_id').')) as count',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y %M') as year ",
			)
		);
		$this->db->where($this->dx('transaction_type').' IN(1,4,6,7,9,12)');
		$this->db->group_by(
        	array(
        		'year',
        	)
        );
		return $this->db->get('transaction_statements')->result();
	}

	function count_unique_withdrawing_accounts_by_month(){
		$this->db->select(
			array(
				'count(DISTINCT('.$this->dx('account_id').')) as count',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y %M') as year ",
			)
		);
		$this->db->where($this->dx('transaction_type').' IN(2,3,5,8,10,11)');
		$this->db->group_by(
        	array(
        		'year',
        	)
        );
		return $this->db->get('transaction_statements')->result();
	}

	function count_withdrawals_by_month(){
		$this->db->select(
			array(
				'count(id) as count',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y %M') as year ",
			)
		);
		$this->db->where($this->dx('transaction_type').' IN(2,3,5,8,10,11)');
		$this->db->group_by(
        	array(
        		'year',
        	)
        );
		return $this->db->get('transaction_statements')->result();
	}

	function get_average_withdrawal_amount_by_month(){
		$this->db->select(
			array(
				'AVG('.$this->dx('amount').') as average_amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y %M') as year ",
			)
		);
		$this->db->where($this->dx('transaction_type').' IN(2,3,5,8,10,11)');
		$this->db->group_by(
        	array(
        		'year',
        	)
        );
		return $this->db->get('transaction_statements')->result();
	}

	function count_withdrawals_distribution_by_months($lower_limit='',$upper_limit=''){
		$this->db->select(
			array(
				'count(id) as count',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y %M') as year ",
			)
		);
		$this->db->where($this->dx('transaction_type').' IN(2,3,5,8,10,11)');
		if($lower_limit){
			$this->db->where($this->dx('amount').' >= '.$lower_limit);
		}
		if($upper_limit){
			$this->db->where($this->dx('amount').' <= '.$upper_limit);
		}
		$this->db->group_by(
        	array(
        		'year',
        	)
        );
		return $this->db->get('transaction_statements')->result();
	}

	function get_transactions_over_time(){
		$account_list = '352,423,860,1406,313,39,1,2,1471';
		$this->db->select(array(
			"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y%m') as month_year ",
			'SUM('.$this->dx('amount').') as total_amount',
			$this->dx('transaction_type').' as transaction_type',
			'COUNT(*) as total_items'
		));
		$this->db->where($this->dx('account_id').' NOT IN(352,423)',NULL,FALSE);
		$this->db->group_by(
			array(
				"month_year",
				'transaction_type'
			)
		);
		$this->db->order_by($this->dx('transaction_date'), 'DESC', FALSE);
		$results =  $this->db->get('transaction_statements')->result();
		$arr = array();
		foreach ($results as $result) {
			if($result->transaction_type==12){
				$arr['income'][] = $result;
			}elseif(in_array($result->transaction_type, $this->deposit_transaction_types)){
				$arr['deposits'][] = $result;
			}else{
				$arr['withdrawals'][] = $result;
			}
		}
		return $arr;
	}


	function get_transactions_not_unique(){
		//transaction_id
		$this->select_all_secure('transaction_statements');
		$this->db->select(array(
			'id',
			$this->dx('transaction_id').' as transaction_id',
			$this->dx('account_id').' as account_id',
			$this->dx('amount').' as amount',
			"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%D %M - %Y') as transaction_date2 ",
			'COUNT(*) as count',
		));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->group_by(
        	array(
        		'transaction_id',
        	)
        );
        $this->db->having('count > 1');
        $this->db->where('('.$this->dx('transaction_id').' IS NOT NULL OR '.$this->dx('transaction_id').' != "")',NULL,FALSE);
        $this->db->where($this->dx('account_id').' > 1',NULL,FALSE);
        $result = $this->db->get('transaction_statements')->result();
        return $result;
	}

	function delete_transaction($id=0){
		$this->db->where('id',$id);
		return $this->db->delete('transaction_statements');
	}

	function update_inactive_all_transactions($account_id = 0){
		return $this ->db-> query("update transaction_statements set 
            active=".$this->exa('0')."
            where ".$this->dx("account_id")." ='".$account_id."'"); 
	}

	function get_future_transactions($date=0){
		$this->select_all_secure('transaction_statements');
		$this->db->where($this->dx('transaction_date').' > "'.$date.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'), 'ASC', FALSE);
		$this->db->order_by('id', 'ASC', FALSE);
    	return $this->db->get('transaction_statements')->result();
	}
}