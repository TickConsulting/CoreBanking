<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Transaction_alerts_m extends MY_Model {

	protected $_table = 'transaction_alerts';

	function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->install();
	}

	public function install(){
		$this->db->query("
		create table if not exists equity_bank_transaction_alerts(
			id int not null auto_increment primary key,
			`tranCurrency` blob,
			`tranDate` blob,
			`tranid` blob,
			`tranAmount` blob,
			`trandrcr` blob,
			`accid` blob,
			`refNo` blob,
			`tranType` blob,
			`created_on` blob,
			`tranParticular` blob,
			`tranRemarks` blob
		)");


		$this->db->query("
		create table if not exists kcb_bank_transaction_alerts(
			id int not null auto_increment primary key,
			`tranCurrency` blob,
			`tranDate` blob,
			`tranid` blob,
			`tranAmount` blob,
			`trandrcr` blob,
			`accid` blob,
			`refNo` blob,
			`tranType` blob,
			`created_on` blob,
			`tranParticular` blob,
			`tranRemarks` blob,
			`ip_address` blob
		)");

		$this->db->query("
		create table if not exists t24_transaction_alerts(
			id int not null auto_increment primary key,
			`currency` blob,
			`transaction_date` blob,
			`transaction_id` blob,
			`transaction_amount` blob,
			`transaction_type` blob,
			`transaction_narrative` blob,
			`transaction_channel` blob,
			`account_number` blob,
			`created_on` blob
		)");


		$this->db->query("
		create table if not exists safaricom_transaction_alerts(
			id int not null auto_increment primary key,
			`currency` blob,
			`transaction_date` blob,
			`transaction_id` blob,
			`transaction_amount` blob,
			`transaction_type` blob,
			`transaction_narrative` blob,
			`transaction_channel` blob,
			`account_number` blob,
			`created_on` blob
		)");


		$this->db->query("
		create table if not exists jambo_pay_transaction_alerts(
			id int not null auto_increment primary key,
			`currency` blob,
			`transaction_date` blob,
			`transaction_id` blob,
			`transaction_amount` blob,
			`transaction_type` blob,
			`transaction_narrative` blob,
			`transaction_channel` blob,
			`account_number` blob,
			`created_on` blob
		)");



		$this->db->query("
			create table if not exists transaction_alerts(
				id int not null auto_increment primary key,
				`type` blob,
				`equity_bank_transaction_alert_id` blob,
				`bank_id` blob,
				`transaction_id` blob,
				`account_number` blob,
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
				`description` blob,
				`balance` blob,
				`fine_category_id` blob,
				`depositor_id` blob,
				`stock_sale_id` blob,
				`money_market_investment_id` blob,
				`asset_id` blob,
				`bank_loan_id` blob,
				`withdrawal_id` blob,
				`contribution_refund_id` blob,
				`stock_id` blob,
				`loan_id` blob,
				`account_transfer_id` blob,
				`from_account_id` blob,
				`to_account_id` blob,
				`loan_repayment_id` blob,
				created_by blob,
				created_on blob,
				modified_on blob,
				modified_by blob
		)");

		$this->db->query("
			create table if not exists transaction_alert_forwarders(
				id int not null auto_increment primary key,
				`name` blob,
				`bank_id` blob,
				`account_name` blob,
				`account_number` blob,
				`url` blob,
				created_by blob,
				created_on blob,
				modified_on blob,
				modified_by blob
		)");

		$this->db->query("
			create table if not exists online_banking_transaction_alerts(
				id int not null auto_increment primary key,
				`tranCurrency` blob,
				`tranDate` blob,
				`tranid` blob,
				`tranAmount` blob,
				`trandrcr` blob,
				`accid` blob,
				`refNo` blob,
				`tranType` blob,
				`created_on` blob,
				`tranParticular` blob,
				`tranRemarks` blob,
				`ip_address` blob
		)");
		$this->db->query("
			create table if not exists transaction_alerts_merges(
				id int not null auto_increment primary key,
				`transaction_alert_id` blob,
				`parent_transaction_alert_id` blob,
				`modified_on` blob,
				`modified_by` blob,
				`active` blob
		)");

		$this->db->query("
			create table if not exists transaction_alert_forwards(
				id int not null auto_increment primary key,
				`transaction_alert_id` blob,
				`tranCurrency` blob,
				`tranDate` blob,
				`tranid` blob,
				`tranAmount` blob,
				`trandrcr` blob,
				`accid` blob,
				`refNo` blob,
				`tranType` blob,
				`tranParticular` blob,
				`tranRemarks` blob,
				`active` blob,
				`is_forwarded` blob,
				`ip_address` blob,
				`response` blob,
				`url` blob,
				created_by blob,
				created_on blob,
				modified_on blob,
				modified_by blob
		)");
		
	}

	function insert_equity_bank_transaction_alert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('equity_bank_transaction_alerts',$input);
	}

	function insert_t24_transaction_alert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('t24_transaction_alerts',$input);
	}

	function insert_kcb_bank_transaction_alert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('kcb_bank_transaction_alerts',$input);
	}

	function insert_jambo_pay_transaction_alert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('jambo_pay_transaction_alerts',$input);
	}

	function insert_safaricom_transaction_alert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('safaricom_transaction_alerts',$input);
	}

	function insert_online_checkout_transaction_request($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('online_checkout_transaction_request',$input);
	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('transaction_alerts',$input);
	}
	
	function insert_transaction_alert_forwarder($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('transaction_alert_forwarders',$input);
	}

	function insert_online_banking_transaction_alert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('online_banking_transaction_alerts',$input);
	}

	function insert_transaction_alerts_merges($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('transaction_alerts_merges',$input);
	}

	function get($id=0){
		$this->select_all_secure('transaction_alerts');
		$this->db->where('id',$id);
		return $this->db->get('transaction_alerts')->row();
	}
	
	function get_transaction_alert_forwarders(){
		$this->select_all_secure('transaction_alert_forwarders');
        $this->db->order_by($this->dx('name'),'DESC',FALSE);
		return $this->db->get('transaction_alert_forwarders')->result();
	}


	function get_transaction_alert_by_transaction_id($transaction_id = 0){
		$this->select_all_secure('transaction_alerts');
		$this->db->where($this->dx('transaction_id')." = '".$transaction_id."' ",NULL,FALSE);
		return $this->db->get('transaction_alerts')->row();
	}

	function get_equity_bank_transaction_alert_by_transaction_id($transaction_id = 0){
		$this->select_all_secure('equity_bank_transaction_alerts');
		$this->db->where($this->dx('tranid')." = '".$transaction_id."' ",NULL,FALSE);
		return $this->db->get('equity_bank_transaction_alerts')->row();
	}

	function get_transaction_alert_forwarder($id = 0){
		$this->select_all_secure('transaction_alert_forwarders');
		$this->db->where('id',$id);
		$this->db->limit(1);
		return $this->db->get('transaction_alert_forwarders')->row();
	}

	function update_transaction_alert_forwarder($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'transaction_alert_forwarders',$input);
	}

	function update_online_checkout_transaction_request($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'online_checkout_transaction_request',$input);
	}

	function delete_transaction_alert_forwarder($id = 0){
		$this->db->where('id',$id);
		return $this->db->delete('transaction_alert_forwarders');
	}


	function get_transaction_alert_forwarders_by_bank_id_and_account_number($bank_id = 0,$account_number = 0){
		$this->select_all_secure('transaction_alert_forwarders');
		$this->db->where($this->dx('bank_id').' = "'.$bank_id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' = "2"',NULL,FALSE);
		$this->db->where($this->dx('account_number').' = "'.$account_number.'"',NULL,FALSE);
		return $this->db->get('transaction_alert_forwarders')->result();
	}

	function get_all_posts_transaction_alert_forwarders(){
		$this->select_all_secure('transaction_alert_forwarders');
		$this->db->where($this->dx('type').' = "1"',NULL,FALSE);
		return $this->db->get('transaction_alert_forwarders')->result();
	}

	function update($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'transaction_alerts',$input);
	}

    function update_where($where = "",$input = array()){
    	return $this->update_secure_where($where,'transaction_alerts',$input);
    }

    function update_transaction_alert_by_equity_bank_transaction_alert_id($equity_bank_transaction_alert_id = 0,$input = array()){
		$where = " ".$this->dx('equity_bank_transaction_alert_id')." = '".$equity_bank_transaction_alert_id."' ; ";
        if($this->update_where($where,$input)){
        	return TRUE;
        }else{
            return FALSE;
        }
    }

	function get_equity_bank_transaction_alerts($account_number = 0 ,$from = 0 , $to = 0 ,$amount = 0){
		$this->select_all_secure('equity_bank_transaction_alerts');
		if($account_number){
			$this->db->where($this->dx('accid')." LIKE '%".$this->db->escape_like_str($account_number)."%'",NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('created_on').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){			
			$this->db->where($this->dx('created_on').' <= "'.$to.'"',NULL,FALSE);
		}
		if(currency($amount)){			
			$this->db->where($this->dx('tranAmount').' <= "'.currency($amount).'"',NULL,FALSE);
		}
        $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('equity_bank_transaction_alerts')->result();
	}

	function get_t24_transaction_alerts(){
		$this->select_all_secure('t24_transaction_alerts');
        $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('t24_transaction_alerts')->result();
	}


	function get_jambo_pay_transaction_alerts(){
		$this->select_all_secure('jambo_pay_transaction_alerts');
        $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('jambo_pay_transaction_alerts')->result();
	}

	function get_safaricom_transaction_alerts(){
		$this->select_all_secure('safaricom_transaction_alerts');
        $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('safaricom_transaction_alerts')->result();
	}


	function get_transaction_alerts($name = '' ,$account_number = 0 ,$from= 0 ,$to = 0 ,$amount = 0 ,$transaction_id){
		$this->select_all_secure('transaction_alerts');
		if($account_number){
			$this->db->where($this->dx('account_number')." LIKE '%".$this->db->escape_like_str($account_number)."%'",NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('created_on').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){			
			$this->db->where($this->dx('created_on').' <= "'.$to.'"',NULL,FALSE);
		}
		if(currency($amount)){			
			$this->db->where($this->dx('amount').' <= "'.currency($amount).'"',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transaction_date')." >= '".$from."' ",NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date')." <= '".$to."' ",NULL,FALSE);
		}
		if($transaction_id){
			$this->db->where($this->dx('transaction_id')." = '".$transaction_id."' ",NULL,FALSE);
		}
        $this->db->order_by($this->dx('transaction_date'),'DESC',FALSE);
        $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('transaction_alerts')->result();
	}

	function get_eazzychama_transaction_alerts($from = 0,$to = 0){
		$this->select_all_secure('eazzychama_transaction_alerts');
		if($from){
			$this->db->where($this->dx('transaction_date')." >= '".$from."' ",NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transaction_date')." <= '".$to."' ",NULL,FALSE);
		}
        $this->db->order_by($this->dx('transaction_date'),'DESC',FALSE);
        $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('eazzychama_transaction_alerts')->result();
	}

	function get_online_checkout_transaction_requests(){
		$this->select_all_secure('online_checkout_transaction_request');
        $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('online_checkout_transaction_request')->result();	
	}


	function get_online_checkout_transaction_requests_by_phone_and_account($account_number=0,$phone=0){
		$this->select_all_secure('online_checkout_transaction_request');
		$this->db->where($this->dx('account_number').' = "'.$account_number.'"',NULL,FALSE);
		$this->db->where($this->dx('status').' = "1"',NULL,FALSE);
		$this->db->where('('.$this->dx('phone').' = "'.$phone.'" OR '.$this->dx('phone').' = "'.valid_phone($phone).'" )',NULL,FALSE);
		$this->db->where($this->dx('created_on').' > "'.(time()-180).'"',NULL,FALSE);
		return $this->db->get('online_checkout_transaction_request')->row();
	}

	function count_equity_bank_transaction_alerts($name = '' ,$account_number = 0 ,$from= 0 ,$to = 0 ,$amount = 0){
		if($account_number){
			$this->db->where($this->dx('accid')." LIKE '%".$this->db->escape_like_str($account_number)."%'",NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('created_on').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){			
			$this->db->where($this->dx('created_on').' <= "'.$to.'"',NULL,FALSE);
		}
		if(currency($amount)){			
			$this->db->where($this->dx('tranAmount').' <= "'.currency($amount).'"',NULL,FALSE);
		}
		return $this->db->count_all_results('equity_bank_transaction_alerts');
	}

	function count_safaricom_transaction_alerts(){
		return $this->db->count_all_results('safaricom_transaction_alerts');
	}


	function count_t24_transaction_alerts(){
		return $this->db->count_all_results('t24_transaction_alerts');
	}

	function count_jambo_pay_transaction_alerts(){
		return $this->db->count_all_results('jambo_pay_transaction_alerts');
	}

	function count_transaction_alerts($name = '' ,$account_number = 0 ,$from= 0 ,$to = 0 ,$amount = 0 ,$transaction_id=0){
		if($account_number){
			$this->db->where($this->dx('account_number')." LIKE '%".$this->db->escape_like_str($account_number)."%'",NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('created_on').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){			
			$this->db->where($this->dx('created_on').' <= "'.$to.'"',NULL,FALSE);
		}
		if(currency($amount)){			
			$this->db->where($this->dx('amount').' <= "'.currency($amount).'"',NULL,FALSE);
		}
		if($transaction_id){
			$this->db->where($this->dx('transaction_id')." = '".$transaction_id."' ",NULL,FALSE);
		}
		return $this->db->count_all_results('transaction_alerts');
	}

	function check_if_equity_bank_transaction_is_duplicate($tranid = 0){
		$this->db->where($this->dx('tranid').' = "'.$tranid.'"');
		return $this->db->count_all_results('equity_bank_transaction_alerts')?:0;
	}

	function check_if_kcb_bank_transaction_is_duplicate($tranid = 0){
		$this->db->where($this->dx('tranid').' = "'.$tranid.'"');
		return $this->db->count_all_results('kcb_bank_transaction_alerts')?:0;
	}

	function check_if_t24_transaction_is_duplicate($transaction_id = 0){
		$this->db->where($this->dx('transaction_id').' = "'.$transaction_id.'"');
		return $this->db->count_all_results('t24_transaction_alerts')?:0;
	}


	function check_if_jambo_pay_transaction_is_duplicate($transaction_id = 0){
		$this->db->where($this->dx('transaction_id').' = "'.$transaction_id.'"');
		return $this->db->count_all_results('jambo_pay_transaction_alerts')?:0;
	}


	function check_if_safaricom_transaction_is_duplicate($transaction_id = 0){
		$this->db->where($this->dx('transaction_id').' = "'.$transaction_id.'"');
		return $this->db->count_all_results('safaricom_transaction_alerts')?:0;
	}

	function check_if_online_banking_transaction_is_duplicate($transaction_id = 0){
		$this->db->where($this->dx('tranid').' = "'.$transaction_id.'"');
		return $this->db->count_all_results('online_banking_transaction_alerts')?:0;
	}

	function get_online_banking_transaction($transaction_id = 0){
		$this->select_all_secure('online_banking_transaction_alerts');
		$this->db->where($this->dx('tranid').' = "'.$transaction_id.'"');
		return $this->db->get('online_banking_transaction_alerts')->row();
	}

	function update_online_banking_transaction($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'online_banking_transaction_alerts',$input);
	}

	function delete_alert_for_specific_account(){
		$this->select_all_secure('equity_bank_transaction_alerts');
		$alerts = $this->db->where($this->dx('accid').' ="0260164896187"',NULL,FALSE)
							->get('equity_bank_transaction_alerts')->result();
		$feeds = count($alerts);
		$i=0;
		foreach ($alerts as $alert) 
		{
			$this->db->where('id',$alert->id);
        	$deletion = $this->db->delete('equity_bank_transaction_alerts');
        	if($deletion){
        		$i++;
        	}
		}
		return 'received '.$feeds.' feeds and deleted '.$i;  
	}


	function get_group_unreconciled_deposits($bank_account_number_list = '',$mobile_account_number_list='',$timestamp = 0){
		if($bank_account_number_list==0 && $mobile_account_number_list==0){
			return FALSE;
		}
		$this->db->select('*');
		$this->db->where('type',1);
		$this->db->where('active',1);
		$this->db->where('reconciled !=',1);
		$this->db->where('is_merged !=',1);
		$this->db->where_in('account_number',explode(',',$bank_account_number_list));
		if($timestamp){
        	$this->db->where("(created_on > '".$timestamp."' OR modified_on '".$timestamp."' ) ",NULL,FALSE);
        }
        $this->db->order_by('transaction_date','DESC');
        $this->db->order_by('created_on','DESC');
		return $this->db->get('transaction_alerts')->result();
	}

	function get_transaction_alerts_merge_parent_ids($bank_account_number_list = '',$mobile_account_number_list='',$timestamp = 0){
		if($bank_account_number_list==0 && $mobile_account_number_list==0){
			return FALSE;
		}
		$this->db->select('merged_transaction_alert_id');
		$this->db->where('type',1);
		$this->db->where('active',1);
		$this->db->where('is_merged =',1);
		$this->db->where_in('account_number',explode(',',$bank_account_number_list));
		if($timestamp){
        	$this->db->where("(created_on > '".$timestamp."' OR modified_on '".$timestamp."' ) ",NULL,FALSE);
        }
		$result =  $this->db->get('transaction_alerts')->result();
		$arr = array();
		foreach($result as $result){
			$arr[$result->merged_transaction_alert_id] = $result->merged_transaction_alert_id;
		}
		return $arr;
	}

	function get_merged_transaction_alerts_by_merge_id($id=0){
		$this->db->select('*');
		$this->db->where('merged_transaction_alert_id',$id);
		$this->db->where('is_merged =',1);
		return $this->db->get('transaction_alerts')->result();
	}

	function get_group_transaction_alert($id = 0,$bank_account_number_list = '',$mobile_account_number_list = ''){
		if($bank_account_number_list == '' && $mobile_account_number_list == ''){
			return FALSE;
		}
		$this->select_all_secure('transaction_alerts');
		$this->db->where('id',$id);
		$this->db->where(' ( '.$this->dx('reconciled').' IS NULL OR '.$this->dx('reconciled').' = 0 ) ',NULL,FALSE);
		$this->db->where(' ( '.$this->dx('is_merged').' IS NULL OR '.$this->dx('is_merged').' = 0 ) ',NULL,FALSE);
		$this->db->where('('.$this->dx('account_number').' IN ('.$bank_account_number_list.') OR '.$this->dx('account_number').' IN ('.$mobile_account_number_list.') )',NULL,FALSE);
		$this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('transaction_alerts')->row();

	}

	function get_group_reconciled_deposits($account_number_list = ' ',$filter_parameters = array()){
		$this->db->select('*');
		if(isset($filter_parameters['transaction_alert_id'])){
			$this->db->where('id',$filter_parameters['transaction_alert_id']);
		}
		$this->db->where('type',1);
		$this->db->where('reconciled >=',1);
		$this->db->where('is_merged !=',1);
		$this->db->where('active',1);
		$this->db->where_in('account_number',explode(',',$account_number_list));
		$this->db->order_by('transaction_date','DESC');
        $this->db->order_by('created_on','DESC');
		return $this->db->get('transaction_alerts')->result();

		// $this->select_all_secure('transaction_alerts');
		// if(isset($filter_parameters['transaction_alert_id'])){
		// 	$this->db->where('id',$filter_parameters['transaction_alert_id']);
		// }
		// $this->db->where($this->dx('type').' = "1"',NULL,FALSE);
		// $this->db->where($this->dx('reconciled').' >= 1 ',NULL,FALSE);
		// $this->db->where(' ( '.$this->dx('is_merged').' IS NULL OR '.$this->dx('is_merged').' = 0 ) ',NULL,FALSE);
		// $this->db->where($this->dx('account_number').' IN ('.$account_number_list.')',NULL,FALSE);
  //       $this->db->where($this->dx('active')." = 1 ",NULL,FALSE);

  //       $this->db->order_by($this->dx('transaction_date'),'DESC',FALSE);
  //       $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		// return $this->db->get('transaction_alerts')->result();
	}

	function get_group_reconciled_withdrawals($account_number_list = ' ',$filter_parameters = array()){
		$this->db->select('*');
		if(isset($filter_parameters['transaction_alert_id'])){
			$this->db->where('id',$filter_parameters['transaction_alert_id']);
		}
		$this->db->where('type',2);
		$this->db->where('reconciled >=',1);
		$this->db->where('is_merged !=',1);
		$this->db->where('active',1);
		$this->db->where_in('account_number',explode(',',$account_number_list));
		$this->db->order_by('transaction_date','DESC');
        $this->db->order_by('created_on','DESC');
		return $this->db->get('transaction_alerts')->result();



		// $this->select_all_secure('transaction_alerts');
		// if(isset($filter_parameters['transaction_alert_id'])){
		// 	$this->db->where('id',$filter_parameters['transaction_alert_id']);
		// }
  //       $this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		// $this->db->where($this->dx('type').' = "2"',NULL,FALSE);
		// $this->db->where(' ( '.$this->dx('is_merged').' IS NULL OR '.$this->dx('is_merged').' = 0 ) ',NULL,FALSE);
		// $this->db->where($this->dx('reconciled').' = 1 ',NULL,FALSE);
		// $this->db->where($this->dx('account_number').' IN ('.$account_number_list.')',NULL,FALSE);
  //       $this->db->order_by($this->dx('transaction_date'),'DESC',FALSE);
  //       $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		// return $this->db->get('transaction_alerts')->result();
	}

	function count_group_reconciled_deposits($account_number_list = ' ',$filter_parameters = array()){
		if(isset($filter_parameters['transaction_alert_id'])){
			$this->db->where('id',$filter_parameters['transaction_alert_id']);
		}
  //       $this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		// $this->db->where($this->dx('type').' = "1"',NULL,FALSE);
		// $this->db->where(' ( '.$this->dx('is_merged').' IS NULL OR '.$this->dx('is_merged').' = 0 ) ',NULL,FALSE);
		// $this->db->where($this->dx('reconciled').' = 1 ',NULL,FALSE);
		// $this->db->where($this->dx('account_number').' IN ('.$account_number_list.')',NULL,FALSE);

		$this->db->where('active',1);
		$this->db->where('type',1);
		$this->db->where('reconciled =',1);
		$this->db->where('is_merged !=',1);
		$this->db->where_in('account_number',explode(',',$account_number_list));


		return $this->db->count_all_results('transaction_alerts');
	}

	function count_group_reconciled_withdrawals($account_number_list = ' ',$filter_parameters = array()){
		if(isset($filter_parameters['transaction_alert_id'])){
			$this->db->where('id',$filter_parameters['transaction_alert_id']);
		}
  //       $this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		// $this->db->where($this->dx('type').' = "2"',NULL,FALSE);
		// $this->db->where(' ( '.$this->dx('is_merged').' IS NULL OR '.$this->dx('is_merged').' = 0 ) ',NULL,FALSE);
		// $this->db->where($this->dx('reconciled').' = 1 ',NULL,FALSE);
		// $this->db->where($this->dx('account_number').' IN ('.$account_number_list.')',NULL,FALSE);

		$this->db->where('active',1);
		$this->db->where('type',2);
		$this->db->where('reconciled',1);
		$this->db->where('is_merged !=',1);
		$this->db->where_in('account_number',explode(',',$account_number_list));



		return $this->db->count_all_results('transaction_alerts');
	}

	function count_group_unreconciled_deposits($bank_account_number_list = ' ',$mobile_account_number_list=''){
		if($bank_account_number_list==0 && $mobile_account_number_list ==0){
			return 0;
		}
		$this->db->where('active',1);
		$this->db->where('type',1);
		$this->db->where('reconciled !=',1);
		$this->db->where('is_merged !=',1);
		$this->db->where_in('account_number',explode(',',$bank_account_number_list));

  //       $this->db->where($this->dx('active')." = 1 ",NULL,FALSE);

		// $this->db->where($this->dx('type').' = "1"',NULL,FALSE);
		// $this->db->where(' ( '.$this->dx('reconciled').' IS NULL OR '.$this->dx('reconciled').' = 0 ) ',NULL,FALSE);
		// $this->db->where(' ( '.$this->dx('is_merged').' IS NULL OR '.$this->dx('is_merged').' = 0 ) ',NULL,FALSE);
		// $this->db->where('('.$this->dx('account_number').' IN ('.$bank_account_number_list.') OR '.$this->dx('account_number').' IN ('.$mobile_account_number_list.') )',NULL,FALSE);
		return $this->db->count_all_results('transaction_alerts');
	}

	function get_group_unreconciled_withdrawals($bank_account_number_list = ' ',$mobile_account_number_list='',$timestamp = 0){
		if($bank_account_number_list==0 && $mobile_account_number_list==0){
			return FALSE;
		}
		$this->db->select('*');
		$this->db->where('type',2);
		$this->db->where('active',1);
		$this->db->where('reconciled !=',1);
		$this->db->where('is_merged !=',1);
		$this->db->where_in('account_number',explode(',',$bank_account_number_list));
		if($timestamp){
        	$this->db->where("(created_on > '".$timestamp."' OR modified_on '".$timestamp."' ) ",NULL,FALSE);
        }
        $this->db->order_by('transaction_date','DESC');
        $this->db->order_by('created_on','DESC');
		return $this->db->get('transaction_alerts')->result();



		// if($bank_account_number_list==0 && $mobile_account_number_list==0){
		// 	return FALSE;
		// }
		// $this->select_all_secure('transaction_alerts');
		// $this->db->where($this->dx('type').' = "2"',NULL,FALSE);
		// $this->db->where(' ( '.$this->dx('reconciled').' IS NULL OR '.$this->dx('reconciled').' = 0 ) ',NULL,FALSE);
		// $this->db->where(' ( '.$this->dx('is_merged').' IS NULL OR '.$this->dx('is_merged').' = 0 ) ',NULL,FALSE);
		// $this->db->where('('.$this->dx('account_number').' IN ('.$bank_account_number_list.') OR '.$this->dx('account_number').' IN ('.$mobile_account_number_list.') )',NULL,FALSE);
  //       if($timestamp){
  //       	$this->db->where("( ".$this->dx('created_on')." > '".$timestamp."' OR ".$this->dx('modified_on')." > '".$timestamp."' ) ",NULL,FALSE);
  //       }
  //       $this->db->where($this->dx('active')." = 1 ",NULL,FALSE);

  //       $this->db->order_by($this->dx('transaction_date'),'DESC',FALSE);
  //       $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		// return $this->db->get('transaction_alerts')->result();
	}

	function count_group_unreconciled_withdrawals($bank_account_number_list = ' ',$mobile_account_number_list=''){
		if($bank_account_number_list==0 && $mobile_account_number_list ==0){
			return 0;
		}
		$this->db->where('active',1);
		$this->db->where('type',2);
		$this->db->where('reconciled !=',1);
		$this->db->where('is_merged !=',1);
		$this->db->where_in('account_number',explode(',',$bank_account_number_list));


  //       $this->db->where($this->dx('active')." = 1 ",NULL,FALSE);

		// $this->db->where($this->dx('type').' = "2"',NULL,FALSE);
		// $this->db->where(' ( '.$this->dx('reconciled').' IS NULL OR '.$this->dx('reconciled').' = 0 ) ',NULL,FALSE);
		// $this->db->where(' ( '.$this->dx('is_merged').' IS NULL OR '.$this->dx('is_merged').' = 0 ) ',NULL,FALSE);
		// $this->db->where('('.$this->dx('account_number').' IN ('.$bank_account_number_list.') OR '.$this->dx('account_number').' IN ('.$mobile_account_number_list.') )',NULL,FALSE);
		return $this->db->count_all_results('transaction_alerts');
	}

	function get_total_transactions_amount($bank_id = 0,$account_numbers_object = array()){

		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as amount '
			)
		);

		if($bank_id){
			$this->db->where($this->dx('bank_id').' = "'.$bank_id.'"',NULL,FALSE);
		}
		
		if(preg_match('/(eazzyclub)/',$_SERVER['HTTP_HOST'])){
        	$this->db->where($this->dx('currency')." = 'UGX' ",NULL,FALSE);
        }else if(preg_match('/(eazzykikundi)/',$_SERVER['HTTP_HOST'])){
			$this->db->where($this->dx('currency')." = 'TZS' ",NULL,FALSE);
		}else{
        	$this->db->where($this->dx('currency')." = 'KES' ",NULL,FALSE);
        }
       	$this->db->where('('.$this->dx('account_number').' != "1003200903478" AND '.$this->dx('account_number').' != "1003100988920" ) ',NULL,FALSE);


        $this->db->where($this->dx('active')." = 1 ",NULL,FALSE);

		$result = $this->db->get('transaction_alerts')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function count_transaction_alert_bank_accounts(){
		$this->db->select(
			array(
				'COUNT( DISTINCT ('.$this->dx('account_number').') ) as count '
			)
		);

		// if($bank_id){
		// 	$this->db->where($this->dx('bank_id').' = "'.$bank_id.'"',NULL,FALSE);
		// }
		
		if(preg_match('/(eazzyclub)/',$_SERVER['HTTP_HOST'])){
        	$this->db->where($this->dx('currency')." = 'UGX' ",NULL,FALSE);
        }else{
        	$this->db->where($this->dx('currency')." = 'KES' ",NULL,FALSE);
        }
        $this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
        return $this->db->get('transaction_alerts')->row()->count;
	}


	function get_total_withdrawal_transactions_amount($bank_id = 0,$account_numbers_object = array()){
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount '
			)
		);
		if($bank_id){
			$this->db->where($this->dx('bank_id').' = "'.$bank_id.'"',NULL,FALSE);
		}
		
		if(preg_match('/(eazzyclub)/',$_SERVER['HTTP_HOST'])){
        	$this->db->where($this->dx('currency')." = 'UGX' ",NULL,FALSE);
        }else if(preg_match('/(eazzykikundi)/',$_SERVER['HTTP_HOST'])){
			$this->db->where($this->dx('currency')." = 'TZS' ",NULL,FALSE);
		}else{
        	$this->db->where($this->dx('currency')." = 'KES' ",NULL,FALSE);
        }
       	$this->db->where('('.$this->dx('account_number').' != "1003200903478" AND '.$this->dx('account_number').' != "1003100988920" ) ',NULL,FALSE);

        $this->db->where($this->dx('active')." = 1 ",NULL,FALSE);

		$this->db->where($this->dx('type').' = "2"',NULL,FALSE);
		$result = $this->db->get('transaction_alerts')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}


	function get_total_deposit_transactions_amount($bank_id = 0,$account_numbers_object = array()){
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount '
			)
		);
		if($bank_id){
			$this->db->where($this->dx('bank_id').' = "'.$bank_id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('active')." = 1 ",NULL,FALSE);

		if(preg_match('/(eazzyclub)/',$_SERVER['HTTP_HOST'])){
        	$this->db->where($this->dx('currency')." = 'UGX' ",NULL,FALSE);
        }else if(preg_match('/(eazzykikundi)/',$_SERVER['HTTP_HOST'])){
			$this->db->where($this->dx('currency')." = 'TZS' ",NULL,FALSE);
		}else{
        	$this->db->where($this->dx('currency')." = 'KES' ",NULL,FALSE);
        }

       	$this->db->where('('.$this->dx('account_number').' != "1003200903478" AND '.$this->dx('account_number').' != "1003100988920" ) ',NULL,FALSE);

		$this->db->where($this->dx('type').' = "1"',NULL,FALSE);
		$result = $this->db->get('transaction_alerts')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_total_deposit_transactions_amount_for_today_by_bank_branch_id_array($bank_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'sum('.$this->dx('transaction_alerts.amount').') as amount ',
				$this->dx('bank_accounts.bank_branch_id').' as bank_branch_id ',
			)
		);
		if($bank_id){
			$this->db->where($this->dx('bank_id').' = "'.$bank_id.'"',NULL,FALSE);
		}
		$this->db->join("bank_accounts",$this->dx('transaction_alerts.account_number')." = ".$this->dx('bank_accounts.account_number'));
		$this->db->where($this->dx('type').' = "1"',NULL,FALSE);
        $this->db->group_by(array($this->dx("bank_branch_id")));
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_alerts.created_on')."),'%Y %D %M') = '" . date('Y jS F') . "'", NULL, FALSE);
        $this->db->where($this->dx('bank_accounts.active')." = 1 ",NULL,FALSE);
   		
		$result = $this->db->get('transaction_alerts')->result();
		foreach($result as $row):
			$arr[$row->bank_branch_id] = $row->amount;
		endforeach;
		return $arr;
	}

	function get_total_withdrawal_transactions_amount_for_today_by_bank_branch_id_array($bank_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'sum('.$this->dx('transaction_alerts.amount').') as amount ',
				$this->dx('bank_accounts.bank_branch_id').' as bank_branch_id ',
			)
		);
		if($bank_id){
			$this->db->where($this->dx('bank_id').' = "'.$bank_id.'"',NULL,FALSE);
		}
		$this->db->join("bank_accounts",$this->dx('transaction_alerts.account_number')." = ".$this->dx('bank_accounts.account_number'));
		$this->db->where($this->dx('type').' = "2"',NULL,FALSE);
        $this->db->group_by(array($this->dx("bank_branch_id")));
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_alerts.created_on')."),'%Y %D %M') = '" . date('Y jS F') . "'", NULL, FALSE);
        $this->db->where($this->dx('bank_accounts.active')." = 1 ",NULL,FALSE);
   		
		$result = $this->db->get('transaction_alerts')->result();
		foreach($result as $row):
			$arr[$row->bank_branch_id] = $row->amount;
		endforeach;
		return $arr;
	}



	function get_total_deposit_transactions_amounts_by_group_bank_account_number_array($bank_id = 0,$account_numbers_object = array()){
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount ',
				$this->dx('account_number').' as account_number '
			)
		);
		if($bank_id){
			$this->db->where($this->dx('bank_id').' = "'.$bank_id.'"',NULL,FALSE);
		}
		if(empty($account_numbers_object)){
			$this->db->where($this->dx('account_number')." IN (0) ");
		}else{
			$arr = array();
			foreach($account_numbers_object as $account_number):
				$arr[] = $account_number->account_number;
			endforeach;
			$this->db->where($this->dx('account_number').' IN ('.implode(",",$arr).') ',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' = "1"',NULL,FALSE);
		$this->db->group_by(
			array($this->dx('account_number'))
		);
        $this->db->where($this->dx('active')." = 1 ",NULL,FALSE);

		$result = $this->db->get('transaction_alerts')->result();
		$arr = array();

		foreach ($result as $row) {
			# code...
			$arr[$row->account_number] = $row->amount;
		}
		return $arr;
	}


	function get_total_withdrawal_transactions_amounts_by_group_bank_account_number_array($bank_id = 0,$account_numbers_object = array()){
		
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount ',
				$this->dx('account_number').' as account_number '
			)
		);
		if($bank_id){
			$this->db->where($this->dx('bank_id').' = "'.$bank_id.'"',NULL,FALSE);
		}

		if(empty($account_numbers_object)){
			$this->db->where($this->dx('account_number')." IN (0) ");
		}else{
			$arr = array();
			foreach($account_numbers_object as $account_number):
				$arr[] = $account_number->account_number;
			endforeach;
			$this->db->where($this->dx('account_number').' IN ('.implode(",",$arr).') ',NULL,FALSE);
		}
        $this->db->where($this->dx('active')." = 1 ",NULL,FALSE);

		$this->db->where($this->dx('type').' = "2"',NULL,FALSE);
		$this->db->group_by(
			array($this->dx('account_number'))
		);
		$result = $this->db->get('transaction_alerts')->result();
		$arr = array();
		foreach ($result as $row) {
			# code...
			$arr[$row->account_number] = $row->amount;
		}
		return $arr;
	}



	function get_total_deposits_by_month_array($bank_id = 0,$account_numbers_object = array()){
		$arr = array();
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%c') as month ",
			)
		);

		if($bank_id){
			$this->db->where($this->dx('bank_id').' = "'.$bank_id.'"',NULL,FALSE);
		}

		if(preg_match('/(eazzyclub)/',$_SERVER['HTTP_HOST'])){
        	$this->db->where($this->dx('currency')." = 'UGX' ",NULL,FALSE);
        }else if(preg_match('/(eazzykikundi)/',$_SERVER['HTTP_HOST'])){
			$this->db->where($this->dx('currency')." = 'TZS' ",NULL,FALSE);
		}else{
        	$this->db->where($this->dx('currency')." = 'KES' ",NULL,FALSE);
        }
       	$this->db->where('('.$this->dx('account_number').' != "1003200903478" AND '.$this->dx('account_number').' != "1003100988920" ) ',NULL,FALSE);


        $this->db->where($this->dx('active')." = 1 ",NULL,FALSE);

		$this->db->where($this->dx('type').' = "1"',NULL,FALSE);
        $this->db->group_by(
        	array(
        		'year',
        		'month'
        	)
        );
		$result = $this->db->get('transaction_alerts')->result();
		foreach($result as $row){
			$arr[$row->year][$row->month] = $row->amount;
		}
		return $arr;
	}
	function get_total_deposits_by_month_array_tests($from = 0 ,$to ){
		$arr = array();
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%c') as month ",
			)
		);
		$this->db->where($this->dx('created_on')." >= ".$from,NULL,FALSE);
        $this->db->where($this->dx('created_on')." <= ".$to,NULL,FALSE);
        $this->db->where($this->dx('active')." = 1 ",NULL,FALSE);

		$this->db->where($this->dx('type').' = "1"',NULL,FALSE);
        $this->db->group_by(
        	array(
        		'year',
        		'month'
        	)
        );
		$result = $this->db->get('transaction_alerts')->result();
		foreach($result as $row){
			$arr[$row->year][$row->month] = $row->amount;
		}
		return $arr;
	}
	function get_total_withdrawals_by_month_array_tests($from = 0 ,$to){
		$arr = array();
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%c') as month ",
			)
		);
		$this->db->where($this->dx('created_on')." >= ".$from,NULL,FALSE);
        $this->db->where($this->dx('created_on')." <= ".$to,NULL,FALSE);		
		$this->db->where($this->dx('type').' = "2"',NULL,FALSE);
        $this->db->group_by(
        	array(
        		'year',
        		'month'
        	)
        );
        $this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		$result = $this->db->get('transaction_alerts')->result();
		foreach($result as $row){
			$arr[$row->year][$row->month] = $row->amount;
		}
		return $arr;
	}

	function get_total_withdrawals_by_month_array($bank_id = 0,$account_numbers_object = array()){
		$arr = array();
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('transaction_date')." ),'%c') as month ",
			)
		);

		if($bank_id){
			$this->db->where($this->dx('bank_id').' = "'.$bank_id.'"',NULL,FALSE);
		}
		
		if(preg_match('/(eazzyclub)/',$_SERVER['HTTP_HOST'])){
        	$this->db->where($this->dx('currency')." = 'UGX' ",NULL,FALSE);
        }else if(preg_match('/(eazzykikundi)/',$_SERVER['HTTP_HOST'])){
			$this->db->where($this->dx('currency')." = 'TZS' ",NULL,FALSE);
		}else{
        	$this->db->where($this->dx('currency')." = 'KES' ",NULL,FALSE);
        }
       	$this->db->where('('.$this->dx('account_number').' != "1003200903478" AND '.$this->dx('account_number').' != "1003100988920" ) ',NULL,FALSE);
		$this->db->where($this->dx('type').' = "2"',NULL,FALSE);
        $this->db->group_by(
        	array(
        		'year',
        		'month'
        	)
        );
        $this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		$result = $this->db->get('transaction_alerts')->result();
		foreach($result as $row){
			$arr[$row->year][$row->month] = $row->amount;
		}
		return $arr;
	}


	function update_transaction_alerts_from_mobile_provider(){
		$this ->db-> query("update transaction_alerts set 
                account_number=".$this->exa('null')."
                where ".$this->dx("account_number")." ='dvea'");  
		 return $this ->db-> query("update transaction_alerts set 
                account_number=".$this->exa('null')."
                where ".$this->dx("account_number")." ='test'");
	}

	function get_group_transaction_alert_counts_array(){
		$this->db->select(
			array(
				$this->dx("bank_accounts.group_id")." as group_id ",
				"COUNT(transaction_alerts.id) as transaction_alerts_count"
			)
		);
		$this->db->join('bank_accounts',$this->dx('transaction_alerts.account_number')." = ".$this->dx('bank_accounts.account_number'));
        $this->db->group_by(array($this->dx("bank_accounts.group_id")));
        $this->db->where($this->dx('transaction_alerts.active')." = 1 ",NULL,FALSE);

		$transaction_alerts = $this->db->get('transaction_alerts')->result();
		$arr = array();
		foreach($transaction_alerts as $transaction_alert):
			$arr[$transaction_alert->group_id] = $transaction_alert->transaction_alerts_count;
		endforeach;
		return $arr;
	}

    function get_group_matching_withdrawal_transaction_alert($group_id = 0,$transaction_date = 0,$from_account_id = 0,$to_account_id = 0,$amount = 0){
		$from_account_id = str_replace('bank-','',$from_account_id);
		$this->select_all_secure('transaction_alerts');
		$this->db->where($this->dx('transaction_alerts.type')." = '2' ",NULL,FALSE);
		//$this->db->where($this->dx('transaction_alerts.reconciled')." IS NULL ",NULL,FALSE);
		// $this->db->where(' ( '.$this->dx('is_merged').' IS NULL OR '.$this->dx('is_merged').' = 0 ) ',NULL,FALSE);
		$this->db->where($this->dx('transaction_alerts.active')." = '1' ",NULL,FALSE);
		$this->db->where($this->dx('transaction_alerts.amount')." = '".$amount."' ",NULL,FALSE);
		$this->db->where($this->dx('transaction_alerts.transaction_date')." = '".$transaction_date."' ",NULL,FALSE);
		$this->db->where($this->dx('bank_accounts.group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where('bank_accounts.id',$from_account_id);

		$this->db->join('bank_accounts',$this->dx('bank_accounts.account_number')." = ".$this->dx('transaction_alerts.account_number'));
		$this->db->limit(1);
		return $this->db->get('transaction_alerts')->row();
    }

    function get_group_matching_deposit_transaction_alert($group_id = 0,$transaction_date = 0,$from_account_id = 0,$to_account_id = 0,$amount = 0){
		$to_account_id = str_replace('bank-','',$to_account_id);
		$this->select_all_secure('transaction_alerts');
		$this->db->where($this->dx('transaction_alerts.type')." = '1' ",NULL,FALSE);
		//$this->db->where($this->dx('transaction_alerts.reconciled')." IS NULL ",NULL,FALSE);
		// $this->db->where(' ( '.$this->dx('is_merged').' IS NULL OR '.$this->dx('is_merged').' = 0 ) ',NULL,FALSE);
		$this->db->where($this->dx('transaction_alerts.active')." = '1' ",NULL,FALSE);
		$this->db->where($this->dx('transaction_alerts.amount')." = '".$amount."' ",NULL,FALSE);
		$this->db->where($this->dx('transaction_alerts.transaction_date')." = '".$transaction_date."' ",NULL,FALSE);
		$this->db->where($this->dx('bank_accounts.group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where('bank_accounts.id',$to_account_id);

		$this->db->join('bank_accounts',$this->dx('bank_accounts.account_number')." = ".$this->dx('transaction_alerts.account_number'));
		$this->db->limit(1);
		return $this->db->get('transaction_alerts')->row();
    }

    function get_group_lace_deposit_transaction_alerts($bank_account_number_list = '',$mobile_account_number_list=''){
    	$this->select_all_secure('transaction_alerts');
		$this->db->where($this->dx('transaction_alerts.type')." = '1' ",NULL,FALSE);
		$this->db->where(' ( '.$this->dx('reconciled').' IS NULL OR '.$this->dx('reconciled').' = 0 ) ',NULL,FALSE);
		$this->db->where(' ( '.$this->dx('is_merged').' IS NULL OR '.$this->dx('is_merged').' = 0 ) ',NULL,FALSE);
		$this->db->where('('.$this->dx('account_number').' IN ('.$bank_account_number_list.') OR '.$this->dx('account_number').' IN ('.$mobile_account_number_list.') )',NULL,FALSE);
		$this->db->where(" CONVERT(" . $this->dx('particulars') . " USING 'latin1')  like '%" .'LACE'. "%' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		return $this->db->get('transaction_alerts')->result();
    }

    function get_group_unreconciled_deposit_transaction_alerts(){
    	$this->select_all_secure('transaction_alerts');
		$this->db->where($this->dx('transaction_alerts.type')." = '1' ",NULL,FALSE);
		$this->db->where($this->dx('reconciled').' = 0  ',NULL,FALSE);
		$this->db->where(' ( '.$this->dx('is_merged').' IS NULL OR '.$this->dx('is_merged').' = 0 ) ',NULL,FALSE);
		$this->db->where($this->dx('modified_on').' > 0  ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = '.$this->group->id.'  ',NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		return $this->db->get('transaction_alerts')->result();
    }

    function insert_batch_transaction_alert_forwards($input = array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_chunked_batch_secure_data('transaction_alert_forwards',$input);
    }

    function update_transaction_alert_forward($id = 0,$input = array(),$SKIP_VALIDATION = FALSE){
		return $this->update_secure_data($id,'transaction_alert_forwards',$input);
    }
		
    function get_transaction_alert_forwards_by_transaction_alert_id($transaction_alert_id = 0){
    	$this->select_all_secure('transaction_alert_forwards');
		$this->db->where($this->dx('transaction_alert_id')." = '".$transaction_alert_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('transaction_alert_forwards')->result();
    }

		
    function get_transaction_alert_forwards(){
    	$this->select_all_secure('transaction_alert_forwards');
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('transaction_alert_forwards')->result();
    }

    function get_transaction_alerts_to_forward($limit = 10){
    	$this->select_all_secure('transaction_alert_forwards');
		$this->db->where($this->dx('is_forwarded')." = '0' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		if($limit){
			$this->db->limit($limit);
		}
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('transaction_alert_forwards')->result();
    }

    function get_group_transaction_alerts_deposits_by_account_number($account_number_list = ' ',$filter_parameters = array()){
		$this->select_all_secure('transaction_alerts');
		if(isset($filter_parameters['transaction_alert_id'])){
			$this->db->where('id',$filter_parameters['transaction_alert_id']);
		}
		//$this->db->where($this->dx('type').' = "1"',NULL,FALSE);
		//$this->db->where($this->dx('reconciled').' = 1 ',NULL,FALSE);
		//$this->db->where(' ( '.$this->dx('is_merged').' IS NULL OR '.$this->dx('is_merged').' = 0 ) ',NULL,FALSE);
		$this->db->where($this->dx('account_number')." = '".$account_number_list."' ",NULL,FALSE);
		//$this->db->where($this->dx('account_number').' IN ('.$account_number_list.')',NULL,FALSE);
       // $this->db->where($this->dx('active')." = 1 ",NULL,FALSE);

        $this->db->order_by($this->dx('transaction_date'),'DESC',FALSE);
        $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('transaction_alerts')->result();
	}

	function get_similar_transaction_alert($bank_id=0 ,$account_number = 0 , $transaction_id = 0){
		$this->select_all_secure('transaction_alerts');
		if($account_number){
			$this->db->where($this->dx('account_number')." = '".$account_number."' ",NULL,FALSE);
		}
		if($transaction_id){
			$this->db->where($this->dx('transaction_id')." = '".$transaction_id."' ",NULL,FALSE);
		}
		$this->db->where($this->dx('bank_id')." = '".$bank_id."' ",NULL,FALSE);
		//$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		return $this->db->get('transaction_alerts')->result();
	}

	function get_reconciled_duplicate_transaction_alerts($from = 0,$to = 0){
		$this->select_all_secure('transaction_alerts');
		$this->db->where($this->dx('reconciled')." = 1 ",NULL,FALSE);
		$this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		
		if($from){
			$this->db->where($this->dx('created_on').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){			
			$this->db->where($this->dx('created_on').' <= "'.$to.'"',NULL,FALSE);
		}
		//$this->db->where(' ( '.$this->dx('reconciled').' IS NULL OR '.$this->dx('reconciled').' = 0 ) ',NULL,FALSE);
		//$this->db->where($this->dx('account_number')." = '0240177753889' ",NULL,FALSE);
		return $this->db->get('transaction_alerts')->result();
	}

	function count_e_wallet_transaction_alerts($name = '' ,$account_number = 0 ,$from= 0 ,$to = 0 ,$amount = 0 ,$transaction_id=0){
		if($account_number){
			$this->db->where($this->dx('account_number')." LIKE '%".$this->db->escape_like_str($account_number)."%'",NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('created_on').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){			
			$this->db->where($this->dx('created_on').' <= "'.$to.'"',NULL,FALSE);
		}
		if(currency($amount)){			
			$this->db->where($this->dx('tranAmount').' <= "'.currency($amount).'"',NULL,FALSE);
		}
		if($transaction_id){
			$this->db->where($this->dx('transaction_id')." = '".$transaction_id."' ",NULL,FALSE);
		}
		return $this->db->count_all_results('online_banking_transaction_alerts');
	}

	function get_e_wallet_transaction_alerts($name = '' ,$account_number = 0 ,$from= 0 ,$to = 0 ,$amount = 0 ,$transaction_id = 0){
		$this->select_all_secure('online_banking_transaction_alerts');
		if($account_number){
			$this->db->where($this->dx('account_number')." LIKE '%".$this->db->escape_like_str($account_number)."%'",NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('created_on').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){			
			$this->db->where($this->dx('created_on').' <= "'.$to.'"',NULL,FALSE);
		}
		if(currency($amount)){			
			$this->db->where($this->dx('tranAmount').' <= "'.currency($amount).'"',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('tranDate')." >= '".$from."' ",NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('tranDate')." <= '".$to."' ",NULL,FALSE);
		}
		if($transaction_id){
			$this->db->where($this->dx('transaction_id')." = '".$transaction_id."' ",NULL,FALSE);
		}
        $this->db->order_by($this->dx('tranDate').'+0','DESC',FALSE);
        //$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('online_banking_transaction_alerts')->result();
	}

	function fix_transaction_alerts_fields(){
		$this->db->query('update transaction_alerts set type='.$this->exa('2').' where '.$this->dx('type').' = "D"');
	}

	function count_transaction_alerts_to_forward(){
		$this->_table = 'transaction_alert_forwards';
		$this->db->where($this->dx('is_forwarded')." = '0' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		return $this->db->count_all_results('transaction_alert_forwards');
    }
	

	function get_group_account_all_transaction_alerts($account_numbers = '0'){
		$this->db->where_in('account_number',$account_numbers);
		return $this->db->get('transaction_alerts')->result();
	}

	function insert_batch_transaction_alerts($input=array(),$SKIP_VALIDATION = FALSE){
		$this->_table = 'transaction_alerts';
		return $this->db->insert_batch('transaction_alerts', $input);
	}

	function find_duplicate_transaction_alerts_entries_with_limit($account_number = 0,$group_id =0){
		$this->db->select(
			array(
				'transaction_alerts.id'." as id ",
				$this->dx('transaction_alerts.transaction_type')." as transaction_type ",
				"COUNT(".$this->dx('transaction_alerts.transaction_type').") as count ",
				$this->dx('transaction_alerts.transaction_date')." as transaction_date ",
				"COUNT(".$this->dx('transaction_alerts.transaction_date').")",
				$this->dx('transaction_alerts.amount')." as amount ",
				"COUNT(".$this->dx('transaction_alerts.amount').")",
				$this->dx('transaction_alerts.type')." as type ",
				"COUNT(".$this->dx('transaction_alerts.type').")",
				$this->dx('transaction_alerts.account_number')." as account_number ",
				"COUNT(".$this->dx('transaction_alerts.account_number').")",
				$this->dx('transaction_alerts.created_on')." as created_on ",
				"COUNT(".$this->dx('transaction_alerts.created_on').")",
				$this->dx('transaction_alerts.created_on')." as created_on ",
				"COUNT(".$this->dx('transaction_alerts.created_on').")",				
			)
		);

		$this->db->where(' ( '.$this->dx('reconciled').' IS NULL OR '.$this->dx('reconciled').' = 0 ) ',NULL,FALSE);
		$this->db->where(' ( '.$this->dx('is_merged').' IS NULL OR '.$this->dx('is_merged').' = 0 ) ',NULL,FALSE);
		$this->db->where($this->dx('account_number')." = '".$account_number."' ",NULL,FALSE);
		$this->db->group_by(
			array(
				$this->dx('transaction_alerts.transaction_date'),
				$this->dx('transaction_alerts.amount'),
				$this->dx('transaction_alerts.account_number'),
				//$this->dx('transaction_alerts.created_on'),
				$this->dx('transaction_alerts.type'),
			)
		);
		$this->db->having("(COUNT(".$this->dx('transaction_alerts.transaction_date').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('transaction_alerts.amount').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('transaction_alerts.account_number').") > 1)",NULL,FALSE);
		//$this->db->having("(COUNT(".$this->dx('transaction_alerts.created_on').") > 1)",NULL,FALSE);
		$this->db->having("(COUNT(".$this->dx('transaction_alerts.type').") > 1)",NULL,FALSE);
		$this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		$this->db->order_by($this->dx('transaction_alerts.transaction_date'),'DESC',FALSE);
		$this->db->limit(50);
		$transaction_alert_entries = $this->db->get('transaction_alerts')->result();
		return $transaction_alert_entries;
	}

	function get_duplicate_uneconciled_alert_entries($transaction_date_arr = array(), $amount_arr = array(),$type_arr = array(),$account_number = 0){
		$this->select_all_secure('transaction_alerts');
		if(empty($transaction_date_arr)){
			$this->db->where($this->dx('transaction_date')." IN (0) ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('transaction_date').' IN ('.implode(",",$transaction_date_arr).') ',NULL,FALSE);
		}
		if(empty($amount_arr)){
			$this->db->where($this->dx('amount')." IN (0) ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('amount').' IN ('.implode(",",$amount_arr).') ',NULL,FALSE);
		}
		if(empty($type_arr)){
			$this->db->where($this->dx('type')." IN (0) ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('type').' IN ('.implode(",",$type_arr).') ',NULL,FALSE);
		}
		$this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		$this->db->where(' ( '.$this->dx('reconciled').' IS NULL OR '.$this->dx('reconciled').' = 0 ) ',NULL,FALSE);
		$this->db->where(' ( '.$this->dx('is_merged').' IS NULL OR '.$this->dx('is_merged').' = 0 ) ',NULL,FALSE);
		$this->db->where($this->dx('account_number')." = '".$account_number."' ",NULL,FALSE);
		$this->db->order_by($this->dx('transaction_alerts.transaction_date'),'DESC',FALSE);
		$transaction_alert_entries = $this->db->get('transaction_alerts')->result();
		return $transaction_alert_entries;

	}
}