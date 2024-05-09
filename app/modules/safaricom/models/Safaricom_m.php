<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Safaricom_m extends MY_Model {

	protected $_table = 'safaricomb2crequest';

	protected $special_url_segments = array("/edit/","/view/",'/statement/',"/listing\/page/");

	protected $starting_response_request_id = '100000001';

	public function __construct()
	{
		parent::__construct();

		$this->load->dbforge();
		$this->install();
	}


	public function install()
	{
		$this->db->query("
		create table if not exists safaricomb2crequest(
			id int not null auto_increment primary key,
			`paybill` blob,
			`amount` blob,
			`request_status` blob,
			`originator_conversation_id` blob,
			`request_time` blob,
			`phone` blob,
			`group_id` blob,
			`result_type` blob,
			`result_code` blob,
			`result_description` blob,
			`conversation_id` blob,
			`transaction_id` blob,
			`request_url` blob,
			`callback_url` blob,
			`callback_result_description` blob,
			`callback_result_code` blob,
			`transaction_receipt` blob,
			`transaction_amount` blob,
			`b2c_charges_paid_account_available_funds` blob,
			`b2c_receipt_is_registered_customer` blob,
			`transaction_completed_time` blob,
			`receiver_party_public_name` blob,
			`b2c_working_account_available_funds` blob,
			`b2c_utility_account_available_funds` blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");

		$this->db->query("
		create table if not exists safaricomc2bpayments(
			id int not null auto_increment primary key,
			`transaction_id` blob,
			`reference_number` blob,
			`transaction_date` blob,
			`amount` blob,
			`active` blob,
			`currency` blob,
			`transaction_type` blob,
			`transaction_particulars` blob,
			`phone` blob,
			`account` blob,
			`customer_name` blob,
			`status` blob,
			`shortcode` blob,
			`organization_balance` blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");

		$this->db->query("
		create table if not exists safaricomb2btransactions(
			id int not null auto_increment primary key,
			`command_id` blob,
			`originator_conversation_id` blob,
			`request_amount` blob,
			`account_reference` blob,
			`sender_party` blob,
			`receiver_party` blob,
			`request_time` blob,
			`response_code` blob,
			`conversation_id` blob,
			`response_description` blob,
			`service_status` blob,
			`result_type` blob,
			`result_code` blob,
			`result_description` blob,
			`transaction_id` blob,
			`debit_account_balance` blob,
			`initiator_account_current_balance` blob,
			`debit_account_current_balance` blob,
			`amount` blob,
			`transaction_completed_time` blob,
			`debit_party_charges` blob,
			`receiver_party_public_name` blob,
			`sender_party_public_name` blob,
			`currency` blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	

		$this->db->query("
		create table if not exists safaricomstkpushrequests(
			id int not null auto_increment primary key,
			`shortcode` blob,
			`phone` blob,
			`request_id` blob,
			`group_id` blob,
			`user_id` blob,
			`amount` blob,
			`request_callback_url` blob,
			`response_code` blob,
			`response_description` blob,
			`checkout_request_id` blob,
			`customer_message` blob,
			`result_code` blob,
			`result_description` blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");

		$this->db->query("
		create table if not exists safaricomcheckidentityrequests(
			id int not null auto_increment primary key,
			`shortcode` blob,
			`phone` blob,
			`request_id` blob,
			`group_id` blob,
			`user_id` blob,
			`request_callback_url` blob,
			`response_code` blob,
			`response_description` blob,
			`checkout_request_id` blob,
			`customer_message` blob,
			`result_code` blob,
			`result_description` blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");

		$this->db->query("
		create table if not exists safaricomquerykycrequests(
			id int not null auto_increment primary key,
			`shortcode` blob,
			`phone` blob,
			`request_id` blob,
			`user_id` blob,
			`request_transaction_id` blob,
			`result_transaction_id` blob,
			`request_callback_url` blob,
			`response_code` blob,
			`response_description` blob,
			`checkout_request_id` blob,
			`customer_message` blob,
			`result_type` blob,
			`result_code` blob,
			`result_description` blob,
			`first_name` blob,
			`last_name` blob,
			`surname` blob,
			`document_type` blob,
			`document_number` blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");

		$this->db->query("
		create table if not exists safaricom_configurations(
			id int not null auto_increment primary key,
			`username` blob,
			`password` blob,
			`api_key` blob,
			`access_token` blob,
			`access_token_expires_at` blob,
			`access_token_type` blob,
			`is_default` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	}

	/************************B2C Requests*******************/
	public function get_b2c_request($id = 0)
	{
		$this->select_all_secure('safaricomb2crequest');
		$this->db->where('id',$id);
		return $this->db->get('safaricomb2crequest')->row();
	}

	function update_b2c_request($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'safaricomb2crequest',$input);
    }
	
	function insert_b2c($input = array(),$skip_value = FALSE)
	{
		return $this->insert_secure_data('safaricomb2crequest',$input);
	}

	function get_all_b2c_requests()
	{
		$this->select_all_secure('safaricomb2crequest');
		$this->db->order_by($this->dx('request_time'),'DESC',FALSE);
		return $this->db->get('safaricomb2crequest')->result();
	}

	function count_all_active_b2c()
	{
		return $this->db->count_all_results('safaricomb2crequest');
	}

	function calculate_entry(){
		$this->db->select('id as id');
		$this->db->order_by('id','DESC');
		$this->db->limit(1);
		$id = $this->db->get('safaricomb2crequest')->row();
		if($id){
			return $id->id+1;
		}else{
			return 1;
		}
	}

	function get_b2c_request_by_originator_conversation_id($originator_conversation_id=''){
		$this->select_all_secure('safaricomb2crequest');
		$this->db->where($this->dx('originator_conversation_id').'="'.$originator_conversation_id.'"',NULL,FALSE);
		$result = $this->db->get('safaricomb2crequest')->row();
		return $result;
	}

	function get_b2c_request_by_transaction_id($transaction_id=''){
		$this->select_all_secure('safaricomb2crequest');
		$this->db->where($this->dx('transaction_id').'="'.$transaction_id.'"',NULL,FALSE);
		$result = $this->db->get('safaricomb2crequest')->row();
		return $result;
	}

	function count_all_b2c_requests(){
		return $this->db->count_all_results('safaricomb2crequest')?:0;
	}

	function get_all_b2c_payments_requests_pending_results(){
    	$this->select_all_secure('safaricomb2crequest');
    	$this->db->where($this->dx('result_code').' ="0"',NULL,FALSE);
    	$this->db->where('('.$this->dx('callback_result_code').' =""  OR '.$this->dx('callback_result_code').' =" " OR '.$this->dx('callback_result_code').' IS NULL )',NULL,FALSE);
    	$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')."),'%Y %D %M') = '" . date('Y jS F',time()) . "'", NULL, FALSE);
    	$this->db->where($this->dx('account_id').' > "0"',NULL,FALSE);
    	$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
    	return $this->db->get('safaricomb2crequest')->result();
    }
    function get_all_pending_account_transactions($account_id=0){
    	$this->db->where($this->dx('result_code').' ="0"',NULL,FALSE);
    	$this->db->where('('.$this->dx('callback_result_code').' =""  OR '.$this->dx('callback_result_code').' =" " OR '.$this->dx('callback_result_code').' IS NULL )',NULL,FALSE);
    	$this->db->where($this->dx('account_id').' = "'.$account_id.'"',NULL,FALSE);
    	return $this->db->count_all_results('safaricomb2crequest');
    }

	function update_pending_transactions_to_failed($account_id=0){
		$this->select_all_secure('safaricomb2crequest');
		$this->db->where($this->dx('result_code').' ="0"',NULL,FALSE);
    	$this->db->where('('.$this->dx('callback_result_code').' =""  OR '.$this->dx('callback_result_code').' =" " OR '.$this->dx('callback_result_code').' IS NULL )',NULL,FALSE);
    	$this->db->where($this->dx('account_id').' = "'.$account_id.'"',NULL,FALSE);
		$results = $this->db->get("safaricomb2crequest")->result();
		if($results){
			$update = array(
				"callback_result_code" => 10,
			);
			foreach($results as $result){
				$this->update_b2c_request($result->id,$update);
			}
		}
		echo 'done';
	}

    function get_receiver($transaction_id = ''){
    	$this->db->select(array(
    		$this->dx('receiver_party_public_name').' as receiver_party_public_name',
    	));
    	$this->db->where($this->dx('transaction_id').'="'.$transaction_id.'"',NULL,FALSE);
    	$result = $this->db->get('safaricomb2crequest')->row();
    	if($result){
    		return $result->receiver_party_public_name;
    	}
    }

    function get_safaricom_b2c_array($ids = array()){
		$this->select_all_secure('safaricomb2crequest');
    	if(empty($ids)){
			$this->db->where('id'." = 0 ",NULL,FALSE);
		}else{
			$this->db->where('id'." IN ( ".implode(',',array_unique($ids))." ) ",NULL,FALSE);
		}
		$arr = array();
		$results = $this->db->get('safaricomb2crequest')->result();
		foreach ($results as $key => $result) {
			$arr[$result->id] = $result;
		}
		return $arr;
	}


	/**************************C2B Requests******************/
	function insert_c2b($input = array(),$skip_value = FALSE)
	{
		return $this->insert_secure_data('safaricomc2bpayments',$input);
	}

	function update_c2b_payment($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'safaricomc2bpayments',$input);
    }

    function get_c2b_request($id=0){
    	$this->select_all_secure('safaricomc2bpayments');
		$this->db->where('id',$id);
		return $this->db->get('safaricomc2bpayments')->row();
	}

    function update_c2b_by_transaction_id($transaction_id=0,$organization_balance=0,$transaction_type='',$transaction_date=0){
    	if($transaction_id){
    		$this->db->select(array(
    			'id',
    			$this->dxa('transaction_type'),
    		));
    		$this->db->where($this->dx('transaction_id').'="'.$transaction_id.'"',NULL,FALSE);
    		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
    		$result = $this->db->get('safaricomc2bpayments')->row();
    		if($result){
    			if($this->update_c2b_payment($result->id,array(
    					'organization_balance' => $organization_balance,
    					'transaction_type' => $transaction_type?:$result->transaction_type,
    					'transaction_date' => $transaction_date?:time(),
    					'status'	=>	1,
    					'modified_on' => time(),
    					'modified_by' => 1,
    				))){
    			 	return $result->id;
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

    function is_transaction_dublicate($transaction_id=0){
		$this->db->where($this->dx('transaction_id').'="'.$transaction_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		return $this->db->count_all_results('safaricomc2bpayments')?:0;
	}

	function is_loan_number_recognized($loan_id=0)
	{
		$loan_id=$this->loans_m->get($loan_id);
		if($loan_id){
			return TRUE;
		}else{
			return FALSE;
		}
		
	}


	function count_all_c2b_requests(){
		return $this->db->count_all_results('safaricomc2bpayments')?:0;
	}

	function get_all_c2b_requests(){
		$this->select_all_secure('safaricomc2bpayments');
		$this->db->order_by($this->dx('transaction_date'),'DESC',FALSE);
		return $this->db->get('safaricomc2bpayments')->result();
	}

	function delete_c2b($id=0){
		$this->db->where('id',$id);
		return $this->db->delete('safaricomc2bpayments');
	}

	function get_unsent_c2b_notifications(){
		$this->select_all_secure('safaricomc2bpayments');
		$this->db->where('('.$this->dx('transaction_send_status').' IS NULL OR '.$this->dx('transaction_send_status').' ="0" OR '.$this->dx('transaction_send_status').' ="" OR '.$this->dx('transaction_send_status').' =" " )',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('safaricomc2bpayments')->result();
	}

	function get_c2b_payment_by_transaaction_id($transaction_id = 0){
		$this->select_all_secure('safaricomc2bpayments');
		$this->db->where($this->dx('transaction_id').'="'.$transaction_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		return $this->db->get('safaricomc2bpayments')->row();
	}


	function get_c2b_payment_by_account($account=0){
		$this->select_all_secure('safaricomc2bpayments');
		$this->db->where($this->dx('account').'="'.$account.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		return $this->db->get('safaricomc2bpayments')->row();
	}

	function get_hashed_c2b_payments(){
		$this->select_all_secure('safaricomc2bpayments');
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('phone').' IS NOT NULL AND '.$this->dx('phone').' != "" AND '.$this->dx('phone').' NOT LIKE "254%" ',NULL,FALSE);
		return $this->db->get('safaricomc2bpayments')->result();
	}

	function count_hashed_c2b_payments(){
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('phone').' IS NOT NULL AND '.$this->dx('phone').' != "" AND '.$this->dx('phone').' NOT LIKE "254%" ',NULL,FALSE);
		return $this->db->count_all_results('safaricomc2bpayments');
	}

	/*******************B2B requests****************/

	function insert_b2b_transactions($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('safaricomb2btransactions',$input);
	}

	function update_b2b_transactions($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'safaricomb2btransactions',$input);
    }

    function get_b2b_transaction($id=0){
    	$this->select_all_secure('safaricomb2btransactions');
		$this->db->where('id',$id);
		return $this->db->get('safaricomb2btransactions')->row();
	}

	function count_all_b2b_transactions(){
		return $this->db->count_all_results('safaricomb2btransactions')?:0;
	}

	function get_all_b2b_transactions(){
		$this->select_all_secure('safaricomb2btransactions');
		$this->db->order_by($this->dx('request_time'),'DESC',FALSE);
		return $this->db->get('safaricomb2btransactions')->result();
	}

	function generate_b2b_originator_conversation_id(){
		$this->db->select('id as id');
		$this->db->order_by('id','DESC');
		$this->db->limit(1);
		$id = $this->db->get('safaricomb2btransactions')->row();
		if($id){
			return $id->id+1;
		}else{
			return 1;
		}
	}

	function get_b2b_transaction_by_originator_conversation_id($originator_conversation_id=''){
		$this->select_all_secure('safaricomb2btransactions');
		$this->db->where($this->dx('originator_conversation_id').'="'.$originator_conversation_id.'"',NULL,FALSE);
		$result = $this->db->get('safaricomb2btransactions')->row();
		return $result;
	}

	function get_user_credit_score($phone=0){
		$this->db->select('*');
		$this->db->select('mpesa_credit_score.MSISDN as MpesaMSISDN');
		$this->db->where('MSISDN',$phone);
		$result = $this->db->get('mpesa_credit_score')->row();
		return $result;
	}

	/*******************STK Push Requests**********************/

	function generate_stkpush_request_id(){
		$this->db->select(array(
            $this->dx('request_id').' as request_id',
        ));
        $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
        $this->db->limit(1);
        $res = $this->db->get('safaricomstkpushrequests')->row();
        if($res){
            return substr(chunk_split((str_replace('-','',$res->request_id)+1), 5, '-'), 0, -1);
        }else{
            return $this->starting_response_request_id;
        }
	}

	function insert_stk_push_request($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('safaricomstkpushrequests',$input);
	}

	function get_stk_request_by_request_id($request_id = 0,$get_result_code = TRUE,$get_response_code = TRUE){
		$this->select_all_secure('safaricomstkpushrequests');
		$this->db->where($this->dx('request_id').' = "'.$request_id.'"',NULL,FALSE);
		if($get_result_code){
			//$this->db->where($this->dx('result_code').' iS NULL',NULL,FALSE);	
		}
		if($get_response_code){
			$this->db->where($this->dx('response_code').' = "0"',NULL,FALSE);
		}
		return $this->db->get('safaricomstkpushrequests')->row();
	}

	function get_stk_request_by_merchant_request_id_and_checkout_request_id($checkout_request_id=0,$merchant_request_id=0){
		$this->select_all_secure('safaricomstkpushrequests');
		$this->db->where($this->dx('checkout_request_id').' = "'.$checkout_request_id.'"',NULL,FALSE);
		$this->db->where($this->dx('merchant_request_id').' = "'.$merchant_request_id.'"',NULL,FALSE);
		return $this->db->get('safaricomstkpushrequests')->row();
	}

	function get_stk_request_by_checkout_request_id($checkout_request_id=0){
		$this->select_all_secure('safaricomstkpushrequests');
		$this->db->where($this->dx('checkout_request_id').' = "'.$checkout_request_id.'"',NULL,FALSE);
		return $this->db->get('safaricomstkpushrequests')->row();
	}

	function get_stk_request($id = 0){
		$this->select_all_secure('safaricomstkpushrequests');
		$this->db->where('id',$id);
		return $this->db->get('safaricomstkpushrequests')->row();
	}

	function update_stkpushrequest($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'safaricomstkpushrequests',$input);
    }

    function count_all_stk_push_requests(){
    	return $this->db->count_all_results('safaricomstkpushrequests')?:0;
    }

    function get_all_stk_push_requests(){
    	$this->select_all_secure('safaricomstkpushrequests');
    	$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
    	return $this->db->get('safaricomstkpushrequests')->result();
    }

    function get_all_stk_push_requests_pending_results(){
    	$this->select_all_secure('safaricomstkpushrequests');
    	$this->db->where($this->dx('response_code').' ="0"',NULL,FALSE);
    	$this->db->where('('.$this->dx('result_code').' =""  OR '.$this->dx('result_code').' =" " OR '.$this->dx('result_code').' IS NULL )',NULL,FALSE);
    	$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')."),'%Y %D %M') = '" . date('Y jS F',time()) . "'", NULL, FALSE);
    	$this->db->where($this->dx('account_id').' > "0"',NULL,FALSE);
    	$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
    	return $this->db->get('safaricomstkpushrequests')->result();
    }

    function get_request_where_reference_number($reference_number=0){
    	$this->select_all_secure('safaricomstkpushrequests');
		$this->db->where($this->dx('reference_number').' = "'.$reference_number.'"',NULL,FALSE);
		return $this->db->get('safaricomstkpushrequests')->row();
    }

    function get_stk_payment_by_transaaction_id($transaction_id=0){
    	$this->select_all_secure('safaricomstkpushrequests');
		$this->db->where($this->dx('transaction_id').' = "'.$transaction_id.'"',NULL,FALSE);
		return $this->db->get('safaricomstkpushrequests')->row();
    }

    function get_uncomplete_payments(){
    	echo date('Y F',time());
    	$this->select_all_secure('safaricomstkpushrequests');
    	$this->db->select(array(
    		"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')."),'%Y %M') as created_on2"
    	));
    	$this->db->where($this->dx('response_code'). ' ="0"',NULL,FALSE);
    	$this->db->where('('.$this->dx('result_code'). 'IS NULL OR '.$this->dx('result_code').' = "" OR '.$this->dx('result_code').' =" " )',NULL,FALSE);
    	$this->db->where($this->dx('reference_number').' > "0"',NULL,FALSE);
    	$this->db->where($this->dx('account_id').' > "0"',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')."),'%Y %M') = '" . date('Y F',time()) . "'", NULL, FALSE);
		$this->db->limit(5);
		return $this->db->get('safaricomstkpushrequests')->result();
    }

    function get_stk_push_array($ids = array()){
		$this->select_all_secure('safaricomstkpushrequests');
    	if(empty($ids)){
			$this->db->where('id'." = 0 ",NULL,FALSE);
		}else{
			$this->db->where('id'." IN ( ".implode(',',array_unique($ids))." ) ",NULL,FALSE);
		}
		$arr = array();
		$results = $this->db->get('safaricomstkpushrequests')->result();
		foreach ($results as $key => $result) {
			$arr[$result->id] = $result;
		}
		return $arr;
	}

    /**************************************Checkidentity requests*****************/

    function generate_checkidentity_request_id(){
		$this->db->select(array(
            $this->dx('request_id').' as request_id',
        ));
        $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
        $this->db->limit(1);
        $res = $this->db->get('safaricomcheckidentityrequests')->row();
        if($res){
            return substr(chunk_split((str_replace('-','',$res->request_id)+1), 8, '-'), 0, -1);
        }else{
            return 100000000001;
        }
	}

	function insert_checkidentity_request($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('safaricomcheckidentityrequests',$input);
	}

	function get_checkidentity_request_by_request_id($request_id = 0,$checkout_request_id = 0){
		$this->select_all_secure('safaricomcheckidentityrequests');
		$this->db->where($this->dx('request_id').' = "'.$request_id.'"',NULL,FALSE);
		if($checkout_request_id){
			$this->db->where($this->dx('checkout_request_id').' = "'.$checkout_request_id.'"',NULL,FALSE);
		}
		return $this->db->get('safaricomcheckidentityrequests')->row();
	}

	function get_check_identity_request($id =0){
		$this->select_all_secure('safaricomcheckidentityrequests');
		return $this->db->where('id',$id)->get('safaricomcheckidentityrequests')->row();
	}

	function update_checkidentity_request($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'safaricomcheckidentityrequests',$input);
    }

    function count_all_checkidentity_requests(){
    	return $this->db->count_all_results('safaricomcheckidentityrequests')?:0;
    }


    function get_all_checkidentity_requests(){
    	$this->select_all_secure('safaricomcheckidentityrequests');
    	$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
    	return $this->db->get('safaricomcheckidentityrequests')->result();
    }

    /********************Query KYC**********************/

    function generate_query_kyc_request_id(){
		$this->db->select('id');
        $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
        $this->db->limit(1);
        $res = $this->db->get('safaricomquerykycrequests')->row();
        if($res){
            return ($res->id+1);
        }else{
            return 1;
        }
	}

    function insert_query_kyc($input = array(),$skip_value = FALSE){
    	return $this->insert_secure_data('safaricomquerykycrequests',$input);
    }

    function get_query_user_kyc($id = 0){
    	$this->select_all_secure('safaricomquerykycrequests');
		$this->db->where('id',$id);
		return $this->db->get('safaricomquerykycrequests')->row();
    }

    function count_all_query_kyc_requests(){
    	return $this->db->count_all_results('safaricomquerykycrequests')?:0;
    }


    function get_all_query_kyc_requests(){
    	$this->select_all_secure('safaricomquerykycrequests');
    	$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
    	return $this->db->get('safaricomquerykycrequests')->result();
    }

    function get_kyc_query_request_by_request_id($request_id = 0,$checkout_request_id=0){
    	$this->select_all_secure('safaricomquerykycrequests');
		$this->db->where($this->dx('request_id').' = "'.$request_id.'"',NULL,FALSE);
		$this->db->where($this->dx('checkout_request_id').' = "'.$checkout_request_id.'"',NULL,FALSE);
		return $this->db->get('safaricomquerykycrequests')->row();
    }

    function update_kyc_query_user($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'safaricomquerykycrequests',$input);
    }

    /*************************safaricom configuration*******************************************/
    function is_unique_username($username = '',$id = 0,$shortcode = 0){
		if($id){
			$this->db->where('id !=',$id);
		}
		$this->db->where($this->dx('username').' = "'.$username.'"',NULL,FALSE);
		$this->db->where($this->dx('shortcode').' = "'.$shortcode.'"',NULL,FALSE);
		return $this->db->count_all_results('safaricom_configurations')?0:1;
	}

	function insert_configuration($input = array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('safaricom_configurations',$input);
	}

	function update_configuration($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'safaricom_configurations',$input);
    }

	function get_configurations(){
		$this->select_all_secure('safaricom_configurations');
		$this->db->order_by($this->dx('created_on'),FALSE);
		return $this->db->get('safaricom_configurations')->result();
	}

	function get_configuation($id=0){
		$this->select_all_secure('safaricom_configurations');
		$this->db->where('id',$id);
		return $this->db->get('safaricom_configurations')->row();
	}

	function delete_configuration($id=0){
		$this->db->where('id',$id);
		return $this->db->delete('safaricom_configurations');
	}

	function get_default_configuration(){
		$this->select_all_secure('safaricom_configurations');
		$this->db->where($this->dx('is_default').' = "1"',NULL,FALSE);
		return $this->db->get('safaricom_configurations')->row();
	}


	function get_paybill_configuration($shortcode = 0){
		$this->select_all_secure('safaricom_configurations');
		$this->db->where($this->dx('shortcode').' = "'.$shortcode.'"',NULL,FALSE);
		return $this->db->get('safaricom_configurations')->row();
	}

	function get_token($shortcode = 0){
		$configuration = $this->get_paybill_configuration($shortcode);
		if($configuration){
			if($configuration->access_token){
				if($configuration->access_token_expires_at > time()){
					return $configuration->access_token_type.' '.$configuration->access_token;
				}else{
					if($this->curl->darajaRequests->generate_token($configuration)){
						$token = $this->get_token($shortcode);
						if($token){
							return $token;
						}else{
							$this->get_token($shortcode);
						}
					}else{
						die('Failed to generate token with configuration access token');
					}
				}
			}else{
				if($this->curl->darajaRequests->generate_token($configuration)){
					$token = $this->get_token($shortcode);
					if($token){
						return $token;
					}else{
						$this->get_token($shortcode);
					}
				}else{
					die('failed to generate token with configuration empty access token');
				}
			}
		}else{
			die('No default configuration');
		}
	}
}?>