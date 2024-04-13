<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
Class Bank_accounts_m extends MY_Model{

	protected $_table = 'bank_accounts';

	function __construct(){
		parent::__construct();
		$this->install();
	}

	/***
		Bank accounts can exhibit the following status

		1. active
		2. active but hidden

		is verified
		true 1 - account is verified ownership
		else- not yet verified

		is closed 
		 1 true - a closed group account
		 else account is still open  
	***/

	function install(){

		$this->db->query("
			create table if not exists bank_accounts(
				id int not null auto_increment primary key,
				`bank_id` blob,
				`bank_branch_id` blob,
				`group_id` blob,
				`account_name` blob,
				`account_number` blob,
				`initial_balance` blob,
				`current_balance` blob,
				`signatory_phone` blob,
				`signatory_id_number` blob,
				`is_verified` blob,
				`active` blob,
				`is_closed` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_by` blob,
				`modified_on` blob
			)"
		);

		$this->db->query("
			create table if not exists bank_account_email_transaction_alert_member_pairings(
				id int not null auto_increment primary key,
				`bank_account_id` blob,
				`group_id` blob,
				`member_id` blob,
				`created_by` blob,
				`created_on` blob
			)"
		);

		$this->db->query("
			create table if not exists bank_account_sms_transaction_alert_member_pairings(
				id int not null auto_increment primary key,
				`bank_account_id` blob,
				`group_id` blob,
				`member_id` blob,
				`created_by` blob,
				`created_on` blob
			)"
		);

		$this->db->query("
			create table if not exists bank_account_signatories(
				id int not null auto_increment primary key,
				`bank_account_id` blob,
				`group_id` blob,
				`member_id` blob,
				`created_by` blob,
				`created_on` blob
			)"
		);

		

	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('bank_accounts',$input);
	}

	function insert_bank_account_email_transaction_alert_member_pairing($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('bank_account_email_transaction_alert_member_pairings',$input);
	}

	function delete_bank_account_email_transaction_alert_member_pairings($id = 0){
		$this->db->where($this->dx('bank_account_id').'="'.$id.'"',NULL,FALSE);
		return $this->db->delete('bank_account_email_transaction_alert_member_pairings');
	}

	function delete_bank_account_signatories($id){
		$this->db->where($this->dx('bank_account_id').'="'.$id.'"',NULL,FALSE);
		return $this->db->delete('bank_account_signatories');
	}

	function insert_bank_account_sms_transaction_alert_member_pairing($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('bank_account_sms_transaction_alert_member_pairings',$input);
	}

	function delete_bank_account_sms_transaction_alert_member_pairings($id = 0){
		$this->db->where($this->dx('bank_account_id').'="'.$id.'"',NULL,FALSE);
		return $this->db->delete('bank_account_sms_transaction_alert_member_pairings');
	}

	function get_bank_account_sms_transaction_alert_member_pairings_array($bank_account_id = 0,$group_id = 0){
    	$arr = array();
    	$this->db->select(array($this->dx('member_id').' as member_id '));
		$this->db->where($this->dx('bank_account_id').' = "'.$bank_account_id.'"',NULL,FALSE);
		// if($group_id){
		// 	$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		// }else{
		// 	$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		// }
		$bank_account_sms_transaction_alert_member_pairings = $this->db->get('bank_account_sms_transaction_alert_member_pairings')->result();
		foreach($bank_account_sms_transaction_alert_member_pairings as $bank_account_sms_transaction_alert_member_pairing){
			$arr[] = $bank_account_sms_transaction_alert_member_pairing->member_id;
		}
		return $arr;
    }

	function get_bank_account_email_transaction_alert_member_pairings_array($bank_account_id = 0,$group_id = 0){
    	$arr = array();
    	$this->db->select(array($this->dx('member_id').' as member_id '));
		$this->db->where($this->dx('bank_account_id').' = "'.$bank_account_id.'"',NULL,FALSE);
		 
		$bank_account_email_transaction_alert_member_pairings = $this->db->get('bank_account_email_transaction_alert_member_pairings')->result();
		foreach($bank_account_email_transaction_alert_member_pairings as $bank_account_email_transaction_alert_member_pairing){
			$arr[] = $bank_account_email_transaction_alert_member_pairing->member_id;
		}
		return $arr;
    }

    function get_all_bank_account_sms_transaction_alert_member_pairings_array($group_id=0){
    	$arr = array();
    	$this->db->select(array($this->dx('member_id').' as member_id ',$this->dx('bank_account_id').' as bank_account_id '));
	 
		$bank_account_sms_transaction_alert_member_pairings = $this->db->get('bank_account_sms_transaction_alert_member_pairings')->result();
		foreach($bank_account_sms_transaction_alert_member_pairings as $bank_account_sms_transaction_alert_member_pairing){
			$arr[$bank_account_sms_transaction_alert_member_pairing->bank_account_id][] = $bank_account_sms_transaction_alert_member_pairing->member_id;
		}
		return $arr;
    }

    function get_all_bank_account_email_transaction_alert_member_pairings_array($group_id=0){
    	$arr = array();
    	$this->db->select(array($this->dx('member_id').' as member_id ',$this->dx('bank_account_id').' as bank_account_id '));
		 
		$bank_account_email_transaction_alert_member_pairings = $this->db->get('bank_account_email_transaction_alert_member_pairings')->result();
		foreach($bank_account_email_transaction_alert_member_pairings as $bank_account_email_transaction_alert_member_pairing){
			$arr[$bank_account_email_transaction_alert_member_pairing->bank_account_id][] = $bank_account_email_transaction_alert_member_pairing->member_id;
		}
		return $arr;
    }

	function update($id,$input=array(),$SKIP_VALIDATION=FALSE)
	{
		return $this->update_secure_data($id,'bank_accounts',$input);
	}

	function get($id=0){
		$this->select_all_secure('bank_accounts');
		$this->db->where('id',$id);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		return $this->db->get('bank_accounts')->row();
	}

	function get_group_bank_id($created_by=0,$group_id=0){
		$this->select_all_secure('bank_accounts');
		$this->db->where('created_by',$created_by);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		return $this->db->get('bank_accounts')->row();
	}

	function get_group_bank_account($id=0,$group_id = 0){
		$this->select_all_secure('bank_accounts');
		$this->db->where('id',$id);
		 	
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		return $this->db->get('bank_accounts')->row();
	}

	function get_bank_account($id = 0){
		$this->select_all_secure('bank_accounts');
		$this->db->where('id',$id);
		$this->db->limit(1);	
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		return $this->db->get('bank_accounts')->row();
	}


	function get_group_bank_account_id_by_account_number($account_number=0,$group_id=0){
		$this->db->select('id');
		$this->db->where($this->dx('account_number').'="'.$account_number.'"',NULL,FALSE);
		 
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$return = $this->db->get('bank_accounts')->row();
		if($return){
			return $return->id;
		}else{
			return 0;
		}
	}

	function get_group_verified_bank_account_id_by_account_number($account_number=0,$group_id=0){
		$this->db->select('id');
		$this->db->where($this->dx('account_number').'="'.$account_number.'"',NULL,FALSE);
		 
        $this->db->where($this->dx('bank_accounts.is_verified').' = "1" ',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$return = $this->db->get('bank_accounts')->row();
		if($return){
			return $return->id;
		}else{
			if($wallets = $this->get_group_e_wallet_accounts()){
				return $wallets->id;
			}
		}
	}

	function get_group_verified_bank_account_by_id($id=0,$group_id=0){
		$this->select_all_secure('bank_accounts');
		$this->db->where('id',$id);
		 
        // $this->db->where($this->dx('bank_accounts.is_verified').' = "1" ',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		return $this->db->get('bank_accounts')->row();
		
	}

	function get_group_bank_account_by_account_number($account_number = 0,$group_id = 0){
		$this->select_all_secure('bank_accounts');
		$this->db->where($this->dx('account_number').'="'.$account_number.'"',NULL,FALSE);
		 
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		return $this->db->get('bank_accounts')->row();
	}

	function get_group_bank_account_group_id_as_key_options($show_if_connected = TRUE){
		$arr = array();
		$this->select_all_secure('bank_accounts');
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$bank_accounts = $this->db->get('bank_accounts')->result();
		foreach($bank_accounts as $bank_account):
			if($bank_account->is_verified){
				if($show_if_connected){
					$arr[$bank_account->group_id][] = $bank_account->account_number." - (Connected) ";
				}else{
					$arr[$bank_account->group_id][] = $bank_account->account_number;
				}
			}else{
				$arr[$bank_account->group_id][] = $bank_account->account_number;
			}
		endforeach;
		return $arr;
	}
	
	function get_group_unverified_partner_bank_account_options(){
		$arr = array();
		$this->db->select(
			array(
				$this->dx('account_number').' as account_number',
				$this->dx('account_name').' as account_name',
				$this->dx('is_verified').' as is_verified',
			)
		);
        $this->db->select(array($this->dx('banks.name').'as bank_name',$this->dx('bank_branches.name').' as bank_branch_name'));
    	// $this->db->where($this->dx('bank_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
    	$this->db->where('('.$this->dx('bank_accounts.is_verified').' IS NULL OR '.$this->dx('bank_accounts.is_verified').' = 0 )',NULL,FALSE);
    	$this->db->where($this->dx('banks.partner').'="1"',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        $this->db->join('bank_branches',$this->dx('bank_accounts.bank_branch_id').'= bank_branches.id','INNER');
    	$bank_accounts = $this->db->get('bank_accounts')->result();
    	foreach ($bank_accounts as $key => $value) {
    		$arr[$value->account_number] = $value->bank_name.' ('.$value->bank_branch_name.') - '.$value->account_name.' ('.$value->account_number.')';
        }
        return $arr;
	}

	function get_group_unlinked_partner_bank_account_options(){
		$arr = array();
		$this->db->select(
			array(
				$this->dx('account_number').' as account_number',
				$this->dx('account_name').' as account_name',
				$this->dx('is_linked').' as is_linked',
			)
		);
        $this->db->select(array($this->dx('banks.name').'as bank_name',$this->dx('bank_branches.name').' as bank_branch_name'));
    	// $this->db->where($this->dx('bank_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
    	$this->db->where('('.$this->dx('bank_accounts.is_linked').' IS NULL OR '.$this->dx('bank_accounts.is_linked').' = 0 )',NULL,FALSE);
    	$this->db->where($this->dx('banks.partner').'="1"',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        $this->db->join('bank_branches',$this->dx('bank_accounts.bank_branch_id').'= bank_branches.id','INNER');
    	$bank_accounts = $this->db->get('bank_accounts')->result();
    	foreach ($bank_accounts as $key => $value) {
    		$arr[$value->account_number] = $value->bank_name.' ('.$value->bank_branch_name.') - '.$value->account_name.' ('.$value->account_number.')';
        }
        return $arr;
	}

	function get_group_verified_partner_bank_account_options($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				$this->dx('account_number').' as account_number',
				$this->dx('account_name').' as account_name',
				$this->dx('is_verified').' as is_verified',
			)
		);
        $this->db->select(array($this->dx('banks.name').'as bank_name',$this->dx('bank_branches.name').' as bank_branch_name'));
         
    	$this->db->where($this->dx('bank_accounts.is_verified').'  = 1 ',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
    	$this->db->where($this->dx('banks.partner').'="1"',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        $this->db->join('bank_branches',$this->dx('bank_accounts.bank_branch_id').'= bank_branches.id','INNER');
    	$bank_accounts = $this->db->get('bank_accounts')->result();
    	foreach ($bank_accounts as $key => $value) {
    		$arr[$value->account_number] = $value->bank_name.' ('.$value->bank_branch_name.') - '.$value->account_name.' ('.$value->account_number.')';
        }
        $e_wallet_account = $this->get_group_e_wallet_accounts($group_id);
        if($e_wallet_account){
        	$account = array();
        	$account[$e_wallet_account->account_number] = $e_wallet_account->bank_name.' - '.$e_wallet_account->account_name.' ('.$e_wallet_account->account_number.')';
        	$arr = $arr+$account;
        }
        return $arr;
	}

	function get_group_verified_and_linked_partner_bank_account_options_ids($group_id = 0,$add_id_value=FALSE){
		$arr = array();
		$this->db->select(
			array(
				'bank_accounts.id as id',
				$this->dx('account_number').' as account_number',
				$this->dx('account_name').' as account_name',
				$this->dx('is_verified').' as is_verified',
				$this->dx('is_linked').' as is_linked',
			)
		);
        $this->db->select(array($this->dx('banks.name').'as bank_name',$this->dx('bank_branches.name').' as bank_branch_name'));
        
    	// $this->db->where($this->dx('bank_accounts.is_verified').'  = 1 ',NULL,FALSE);
    	// $this->db->where($this->dx('bank_accounts.is_linked').'  = 1 ',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
    	// $this->db->where($this->dx('banks.partner').'="1"',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        $this->db->join('bank_branches',$this->dx('bank_accounts.bank_branch_id').'= bank_branches.id','INNER');
    	$bank_accounts = $this->db->get('bank_accounts')->result();
    	foreach ($bank_accounts as $key => $value) {
    		if($add_id_value){
    			$arr['bank-'.$value->id] = $value->bank_name.' ('.$value->bank_branch_name.') - '.$value->account_name.' ('.$value->account_number.')';
    		}else{
    			$arr[$value->id] = $value->bank_name.' ('.$value->bank_branch_name.') - '.$value->account_name.' ('.$value->account_number.')';
    		}
        }
        return $arr;
	}

	function get_group_verified_partner_bank_account_options_ids($group_id = 0,$add_id_value=FALSE){
		$arr = array();
		$this->db->select(
			array(
				'bank_accounts.id as id',
				$this->dx('account_number').' as account_number',
				$this->dx('account_name').' as account_name',
				$this->dx('is_verified').' as is_verified',
				$this->dx('is_linked').' as is_linked',
			)
		);
        $this->db->select(array($this->dx('banks.name').'as bank_name',$this->dx('bank_branches.name').' as bank_branch_name'));
 
    	$this->db->where($this->dx('bank_accounts.is_verified').'  = 1 ',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
    	$this->db->where($this->dx('banks.partner').'="1"',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        $this->db->join('bank_branches',$this->dx('bank_accounts.bank_branch_id').'= bank_branches.id','INNER');
    	$bank_accounts = $this->db->get('bank_accounts')->result();
    	foreach ($bank_accounts as $key => $value) {
    		if($add_id_value){
    			$arr['bank-'.$value->id] = $value->bank_name.' ('.$value->bank_branch_name.') - '.$value->account_name.' ('.$value->account_number.')';
    		}else{
    			$arr[$value->id] = $value->bank_name.' ('.$value->bank_branch_name.') - '.$value->account_name.' ('.$value->account_number.')';
    		}
        }
        return $arr;
	}

	function count_verified_partner_bank_accounts($bank_id = 0){
		if($bank_id){
			$this->db->where('banks.id',$bank_id);
		}
		$this->db->where($this->dx('bank_accounts.is_verified').'  = 1 ',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
    	$this->db->where($this->dx('banks.partner').'="1"',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        return $this->db->count_all_results('bank_accounts');
	}

	function get_verified_partner_bank_accounts($bank_id = 0){
		$this->select_all_secure('bank_accounts');
		if($bank_id){
			$this->db->where('banks.id',$bank_id);
		}
		$this->db->where($this->dx('bank_accounts.is_verified').'  = 1 ',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
    	$this->db->where($this->dx('banks.partner').'="1"',NULL,FALSE);
    	$this->db->distinct();
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        return $accounts =  $this->db->get('bank_accounts')->result();
	}

	function get_partner_bank_accounts($bank_id = 0,$paying_group_ids = array()){
		$this->select_all_secure('bank_accounts');
		if($bank_id){
			$this->db->where('banks.id',$bank_id);
		}
		if(empty($paying_group_ids)){

		}else{
			$this->db->where($this->dx('bank_accounts.group_id')." IN (".implode(',',$paying_group_ids).") ");
		}
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		//$this->db->where($this->dx('bank_accounts.is_verified').'  = 1 ',NULL,FALSE);
    	$this->db->where($this->dx('banks.partner').'="1"',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        return $this->db->get('bank_accounts')->result();
	}

	function get_group_unlinked_partner_bank_accounts(){
		$this->db->select(
			array(
				'bank_accounts.id as id',
				$this->dx('account_number').' as account_number',
				$this->dx('account_name').' as account_name',
				$this->dx('is_verified').' as is_verified',
				$this->dx('is_linked').' as is_linked',
				$this->dx('bank_accounts.created_on').' as created_on',
			)
		);
        $this->db->select(array($this->dx('banks.name').'as bank_name',$this->dx('bank_branches.name').' as bank_branch_name'));
    	$this->db->where($this->dx('bank_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
    	// $this->db->where('('.$this->dx('bank_accounts.is_verified').' IS NULL OR '.$this->dx('bank_accounts.is_verified').' = 0 )',NULL,FALSE);
    	$this->db->where('('.$this->dx('bank_accounts.is_linked').' IS NULL OR '.$this->dx('bank_accounts.is_linked').' = 0 )',NULL,FALSE);
    	$this->db->where($this->dx('banks.partner').'="1"',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        $this->db->join('bank_branches',$this->dx('bank_accounts.bank_branch_id').'= bank_branches.id','INNER');
    	return $bank_accounts = $this->db->get('bank_accounts')->result();
	}

	function get_group_unverified_partner_bank_accounts(){
		$this->db->select(
			array(
				'bank_accounts.id as id',
				$this->dx('account_number').' as account_number',
				$this->dx('account_name').' as account_name',
				$this->dx('is_verified').' as is_verified',
				$this->dx('is_linked').' as is_linked',
				$this->dx('bank_accounts.created_on').' as created_on',
			)
		);
        $this->db->select(array($this->dx('banks.name').'as bank_name',$this->dx('bank_branches.name').' as bank_branch_name'));
    	// $this->db->where($this->dx('bank_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
    	$this->db->where('('.$this->dx('bank_accounts.is_verified').' IS NULL OR '.$this->dx('bank_accounts.is_verified').' = 0 )',NULL,FALSE);
    	$this->db->where('('.$this->dx('bank_accounts.is_linked').' IS NULL OR '.$this->dx('bank_accounts.is_linked').' = 0 )',NULL,FALSE);
    	$this->db->where($this->dx('banks.partner').'="1"',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        $this->db->join('bank_branches',$this->dx('bank_accounts.bank_branch_id').'= bank_branches.id','INNER');
    	return $bank_accounts = $this->db->get('bank_accounts')->result();
	}


	function get_group_verified_partner_bank_accounts($group_id=0){
		$this->db->select(
			array(
				'bank_accounts.id as id',
				$this->dx('banks.country_id').' as country_id',
				$this->dx('countries.calling_code').' as calling_code',
				$this->dx('account_number').' as account_number',
				$this->dx('account_name').' as account_name',
				$this->dx('is_verified').' as is_verified',
				$this->dx('account_currency_id').' as account_currency_id',
			)
		);
        $this->db->select(array($this->dx('banks.name').'as bank_name',$this->dx('bank_branches.name').' as bank_branch_name'));
    	
    	$this->db->where($this->dx('bank_accounts.is_verified').' = "1" ',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
    	$this->db->where($this->dx('banks.partner').'="1"',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        $this->db->join('countries',$this->dx('banks.country_id').'= countries.id','INNER');
        $this->db->join('bank_branches',$this->dx('bank_accounts.bank_branch_id').'= bank_branches.id','INNER');
    	return $bank_accounts = $this->db->get('bank_accounts')->result();
	}

	function get_group_verified_bank_account($id=0,$group_id=0){
		$this->db->select(
			array(
				'bank_accounts.id as id',
				$this->dx('banks.country_id').' as country_id',
				$this->dx('countries.calling_code').' as calling_code',
				$this->dx('account_number').' as account_number',
				$this->dx('account_name').' as account_name',
				$this->dx('is_verified').' as is_verified',
				$this->dx('account_currency_id').' as account_currency_id',
			)
		);
		$this->db->where('bank_accounts.id',$id);
        $this->db->select(array($this->dx('banks.name').'as bank_name',$this->dx('bank_branches.name').' as bank_branch_name'));
    	
    	$this->db->where($this->dx('bank_accounts.is_verified').' = "1" ',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
    	$this->db->where($this->dx('banks.partner').'="1"',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        $this->db->join('countries',$this->dx('banks.country_id').'= countries.id','INNER');
        $this->db->join('bank_branches',$this->dx('bank_accounts.bank_branch_id').'= bank_branches.id','INNER');
    	return $bank_accounts = $this->db->get('bank_accounts')->row();
	}

	function get_group_verified_partner_bank_account($group_id=0){
		$this->db->select(
			array(
				'bank_accounts.id as id',
				$this->dx('account_number').' as account_number',
				$this->dx('account_name').' as account_name',
				$this->dx('actual_balance').' as actual_balance',
				$this->dx('account_currency_id').' as account_currency_id',
				$this->dx('is_verified').' as is_verified',
				$this->dx('banks.name').' as bank_name',
				$this->dx('bank_branches.name').' as bank_branch_name',

			)
		);
        $this->db->select(array($this->dx('banks.name').'as bank_name',$this->dx('bank_branches.name').' as bank_branch_name'));
		
    	$this->db->where($this->dx('bank_accounts.is_verified').' = "1" ',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
    	$this->db->where($this->dx('banks.partner').'="1"',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        $this->db->join('bank_branches',$this->dx('bank_accounts.bank_branch_id').'= bank_branches.id','INNER');
    	return $bank_accounts = $this->db->get('bank_accounts')->row();
	}

	function get_group_bank_account_current_balance($id = 0,$group_id = 0){
		$this->db->select(
			array($this->dx('current_balance').' as current_balance ')
		);
		$this->db->where('id',$id);
		
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		if($bank_account = $this->db->get('bank_accounts')->row()){
			return $bank_account->current_balance;
		}else{
			return 0;
		}
	}

	function get_group_bank_account_balance($id = 0,$group_id = 0){
		$this->db->select(
			array(
				$this->dx('current_balance').' as current_balance ',
				$this->dx('initial_balance').' as initial_balance ',
			)
		);
		$this->db->where('id',$id);
		
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		if($bank_account = $this->db->get('bank_accounts')->row()){
			return $bank_account->current_balance + $bank_account->initial_balance;
		}else{
			return 0;
		}
	}

	function get_group_bank_account_number($id = 0,$group_id = 0){
		$this->db->select(
			array(
				$this->dx('account_number').' as account_number ',
			)
		);
		$this->db->where('id',$id);
		
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		if($bank_account = $this->db->get('bank_accounts')->row()){
			return $bank_account->account_number;
		}else{
			return 0;
		}
	}

	function get_group_total_initial_bank_balance($group_id=0){
		$this->db->select(
			array(
				'sum('.$this->dx('initial_balance').') as initial_balance',
			)
		);
		 
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		if($bank_account = $this->db->get('bank_accounts')->row()){
			return $bank_account->initial_balance?:0;
		}else{
			return 0;
		}
	}
	
	function get_group_total_bank_account_balance($group_id = 0){
		$this->db->select(
			array(
				'sum('.$this->dx('current_balance').') as current_balance ',
				'sum('.$this->dx('initial_balance').') as initial_balance',
			)
		);
		 
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		if($bank_account = $this->db->get('bank_accounts')->row()){
			return (floatval($bank_account->current_balance)+floatval($bank_account->initial_balance))?:0;
		}else{
			return 0;
		}
	}

	function get_group_verified_total_bank_account_balance($group_id = 0){
		$this->db->select(
			array(

				'sum('.$this->dx('actual_balance').') as actual_balance ',
			)
		);
		 
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        //$this->db->where($this->dx('partner').'="1"',NULL,FALSE);
		$this->db->where($this->dx('is_verified').'="1"',NULL,FALSE);
		if($bank_account = $this->db->get('bank_accounts')->row()){
			return (floatval($bank_account->actual_balance))?:0;
		}else{
			return 0;
		}
	}

	function get_group_total_actual_bank_account_balance($group_id = 0){
		$this->db->select(
			array(
				'sum('.$this->dx('actual_balance').') as balance ',
			)
		);
	 
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		if($bank_account = $this->db->get('bank_accounts')->row()){
			return $bank_account->balance?:0;
		}else{
			return 0;
		}
	}

	function get_all(){
		$this->select_all_secure('bank_accounts');
		if(isset($this->group->id)){
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		return $this->db->get('bank_accounts')->result();
	}

	function delete($id = 0,$group_id = 0){
		$input = array(
			'modified_on' => time(),
			//'modified_by' => $this->user->id,
			'is_deleted' => 1
		);
		return $this->update($id,$input);
	}

	function check_if_account_exists($id='',$account_number='',$bank_id='')
	{
		$this->select_all_secure('bank_accounts');
		$this->db->where($this->dx('account_number').'="'.$account_number.'"',NULL,FALSE);
		$this->db->where($this->dx('bank_id').'="'.$bank_id.'"',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
	 
		return $this->db->get('bank_accounts')->result();
	}

	function get_group_bank_accounts($group_id = '',$filter_params=array()){
		$this->select_all_secure('bank_accounts');
		$this->db->select(array(
				$this->dx('banks.name').'as bank_name',
				$this->dx('banks.default_bank').'as is_default',
				$this->dx('banks.partner').'as partner',
				$this->dx('bank_branches.name').'as bank_branch',
				$this->dx('banks.wallet').'as wallet',
			));
		if($filter_params){
			foreach ($filter_params as $key => $value) {
				if($value && $this->db->field_exists($key, 'bank_accounts')){
					$this->db->where("
					CONVERT(" . $this->dx($key) . " USING 'latin1')  like '%" . $this->escape_str($value) . "%'
					",NULL,FALSE);
				}
			}
		}
		 
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('banks','banks.id ='.$this->dx('bank_accounts.bank_id'),'inner');
		$this->db->join('bank_branches','bank_branches.id ='.$this->dx('bank_accounts.bank_branch_id'),'INNER');
		return $this->db->get('bank_accounts')->result();
	}

	function count_group_bank_accounts($group_id = '',$filter_params=array()){
		if($filter_params){
			foreach ($filter_params as $key => $value) {
				if($value && $this->db->field_exists($key, 'bank_accounts')){
					$this->db->where("
					CONVERT(" . $this->dx($key) . " USING 'latin1')  like '%" . $this->escape_str($value) . "%'
					",NULL,FALSE);
				}
			}
		}
		 
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('banks','banks.id ='.$this->dx('bank_accounts.bank_id'),'inner');
		$this->db->join('bank_branches','bank_branches.id ='.$this->dx('bank_accounts.bank_branch_id'),'INNER');
		return $this->db->count_all_results('bank_accounts');
	}

	function get_group_active_bank_accounts($group_id =0){
		$this->select_all_secure('bank_accounts');
		$this->db->select(array(
				$this->dx('banks.name').'as bank_name',
				$this->dx('banks.partner').'as partner',
				$this->dx('bank_branches.name').'as bank_branch',
			));
	 
		$this->db->where('('.$this->dx('is_closed').'= "" OR '.$this->dx('is_closed').'is NULL OR '.$this->dx('is_closed').' =" " OR '.$this->dx('is_closed').' ="0" )',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_default').'IS NULL',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.active').' = "1" ',NULL,FALSE);
		$this->db->join('banks','banks.id ='.$this->dx('bank_accounts.bank_id'),'inner');
		$this->db->join('bank_branches','bank_branches.id ='.$this->dx('bank_accounts.bank_branch_id'),'INNER');
		return $this->db->get('bank_accounts')->result();
	}

	function count_all(){
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		return $this->db->count_all_results('bank_accounts');
	}

	function check_if_group_has_partner_bank_account(){
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('partner').'="1"',NULL,FALSE);
		$this->db->join('banks','banks.id ='.$this->dx('bank_accounts.bank_id'),'inner');
		$result = $this->db->count_all_results('bank_accounts');
		if($result){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	function get_group_verified_bank_account_number_list($group_id = 0){
		$this->db->select(
			array($this->dx('account_number').' as account_number ')
		);
		// $this->db->where($this->dx('is_verified').'="1"',NULL,FALSE);
		 
		$this->db->where($this->dx('partner').'="1"',NULL,FALSE);
		$this->db->where($this->dx('is_verified').'="1"',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('banks','banks.id ='.$this->dx('bank_accounts.bank_id'),'inner');
		$result = $this->db->get('bank_accounts')->result();
		$count = 1;
		if($result){
			$account_number_list = "";
			foreach($result as $row){
				if($count==1){
					$account_number_list=$row->account_number;
				}else{
					$account_number_list.=','.$row->account_number;
				}
				$count++;
			}
		}else{
			$account_number_list = "0";
		}

		//print_r($account_number_list);die('here');

		return $account_number_list;
	}

	function get_group_e_wallet_accounts($group_id = 0){
		$this->select_all_secure('bank_accounts');
		$this->db->select(array($this->dx('banks.name').'as bank_name'));
		 
		$this->db->limit(1);
		$this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('banks.slug').' = "chamasoft-e-wallet"',NULL,FALSE);
		$this->db->join('banks',$this->dx('bank_accounts.bank_id').' = banks.id');
		return $this->db->get('bank_accounts')->row();
	}

	function get_groups_verified_bank_account_number_list($group_ids_list = '0'){
		$account_number_list = "0";
		$this->db->select(
			array($this->dx('account_number').' as account_number ')
		);
		$this->db->where($this->dx('is_verified').'="1"',NULL,FALSE);
		$this->db->where($this->dx('group_id').' IN ('.$group_ids_list.')',NULL,FALSE);
		$this->db->where($this->dx('partner').'="1"',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('banks','banks.id ='.$this->dx('bank_accounts.bank_id'),'inner');
		$result = $this->db->get('bank_accounts')->result();
		if($result){
			$count = 1;
			foreach($result as $row){
				if($count==1){
					$account_number_list.=$row->account_number;
				}else{
					$account_number_list.=','.$row->account_number;
				}
				$count++;
			}
		}
		return $account_number_list;
	}

	function get_bank_accounts(){
		$this->select_all_secure('bank_accounts');
		return $this->db->get('bank_accounts')->result();
	}

	function get_bank_accounts_by_bank_branch_count($bank_id = 0,$account_numbers_object = array()){
		$arr = array();
		$this->db->select(
			array(
				'count('.$this->dx('bank_branch_id').') as group_count ',
				$this->dx('bank_id').' as bank_id ',
				$this->dx('bank_branch_id').' as bank_branch_id ',
			)
		);
		if($bank_id){
			$this->db->where($this->dx('bank_id').' = "'.$bank_id.'"',NULL,FALSE);
		}
		if(empty($account_numbers_object)){
			$this->db->where($this->dx('account_number')." IN (0) ",NULL,FALSE);
		}else{
			$arr = array();
			foreach($account_numbers_object as $account_number):
				$arr[] = $account_number->account_number;
			endforeach;
			$this->db->where($this->dx('account_number').' IN ('.implode(",",$arr).') ',NULL,FALSE);
		}
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('is_verified').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
        $this->db->order_by('group_count','DESC',FALSE);
        $this->db->group_by(array($this->dx("bank_branch_id")));
		return $this->db->get('bank_accounts')->result();
	}

	function get_group_default_bank_account($group_id = 0){
		$this->select_all_secure('bank_accounts');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('banks.slug').' = "chamasoft-e-wallet"',NULL,FALSE);
		$this->db->limit(1);
		$this->db->join('banks',$this->dx('bank_accounts.bank_id').' = banks.id');
		return $this->db->get('bank_accounts')->row();
	}

	function get_admin_wallet_account(){
		$this->select_all_secure('bank_accounts');
		$this->db->where($this->dx('is_admin').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('banks.slug').' = "chamasoft-e-wallet"',NULL,FALSE);
		$this->db->limit(1);
		$this->db->join('banks',$this->dx('bank_accounts.bank_id').' = banks.id');
		return $this->db->get('bank_accounts')->row();
	}


	function get_group_default_bank_account_by_account_number($account_number = 0){
		$this->select_all_secure('bank_accounts');
		$this->db->where($this->dx('account_number').' = "'.$account_number.'"',NULL,FALSE);
		$this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('banks.slug').' = "chamasoft-e-wallet"',NULL,FALSE);
		$this->db->limit(1);
		$this->db->join('banks',$this->dx('bank_accounts.bank_id').' = banks.id');
		return $this->db->get('bank_accounts')->row();
	}



	function insert_batch_account_signatories($input=array()){
		return $this->insert_batch_secure_data('bank_account_signatories',$input);
	}

	function get_group_bank_accounts_signatories_array($bank_accounts=array(),$group_id=0,$members_options=array()){
		$bank_account_list = '0';
		if($bank_accounts){
			foreach ($bank_accounts as $key => $bank_account) {
				if($bank_account_list){
					$bank_account_list.=','.$bank_account->id;
				}else{
					$bank_account_list=$bank_account->id;
				}
			}
		}
		$this->select_all_secure('bank_account_signatories');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($bank_account_list){
			$this->db->where($this->dx('bank_account_id').' IN ('.$bank_account_list.')',NULL,FALSE);
		}
		$signatories = $this->db->get('bank_account_signatories')->result();
		$arr = array();
		foreach ($signatories as $signatory) {
			$arr[$signatory->bank_account_id][] = $members_options[$signatory->member_id];
		}
		return $arr;
	}

	function get_group_bank_account_signatories($bank_account_id = 0 ,$group_id = 0){
		$this->select_all_secure('bank_account_signatories');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($bank_account_id){
			$this->db->where($this->dx('bank_account_id').' = "'.$bank_account_id.'"',NULL,FALSE);
		}
		$signatories = $this->db->get('bank_account_signatories')->result();
		$arr = array();
		foreach ($signatories as $signatory) {
			$arr[] = $signatory->member_id;
		}
		return $arr;
	}

	function check_if_member_is_signatory($member_id = 0 ,$group_id = 0){
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		return $this->db->count_all_results('bank_account_signatories')?1:0;
	}


	function get_group_active_account_signatories($bank_account_id=0,$group_id=0){
		$this->db->select(
			array(
				'members.id as id',
				'users.id as user_id',
				$this->dx('users.first_name')." as first_name ",
				$this->dx('users.middle_name')." as middle_name ",
				$this->dx('users.last_name')." as last_name ",
				$this->dx('users.phone')."as phone",
				$this->dx('users.email')."as email",
				$this->dx('users.language_id')."as language_id",
				$this->dx('bank_account_signatories.bank_account_id')."as bank_account_id",
			)
		);
		if($group_id){
			$this->db->where($this->dx('bank_account_signatories.group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('bank_account_signatories.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('members.active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('bank_account_id').' = "'.$bank_account_id.'"',NULL,FALSE);
		$this->db->join('users','users.id = '.$this->dx('members.user_id'));
		$this->db->join('bank_account_signatories',$this->dx('bank_account_signatories.member_id').' = members.id');
		return $this->db->get('members')->result()?:array();
	}

	function get_groups_bank_accounts($groups=array()){
		$groups_id_list = '0';
		foreach ($groups as $group) {
			if($groups_id_list){
				$groups_id_list.=','.$group->id;
			}else{
				$groups_id_list = $group->id;
			}
		}
		$this->select_all_secure('bank_accounts');
		$this->db->select(array(
				$this->dx('banks.name').'as bank_name',
				$this->dx('bank_branches.name').'as bank_branch',
			));
		$this->db->where($this->dx('bank_accounts.group_id').' IN('.$groups_id_list.')',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_verified').' ="1"',NULL,FALSE);
		$this->db->join('banks','banks.id ='.$this->dx('bank_accounts.bank_id'),'inner');
		$this->db->join('bank_branches','bank_branches.id ='.$this->dx('bank_accounts.bank_branch_id'),'INNER');
		$results =  $this->db->get('bank_accounts')->result();
		$arr = array();
		foreach ($results as $result) {
			$arr[$result->group_id][] = $result;
		}
		return $arr;
	}


	function get_verified_partner_bank_accounts_with_transaction_alerts($bank_id = 0){
		$this->db->distinct($this->dx('account_number'));
		$this->db->select(
			array(
				$this->dx('account_number')." as account_number "
			)
		);
		$transaction_alerts = $this->db->get('transaction_alerts')->result();
		$arr = array();
		foreach($transaction_alerts as $transaction_alert):
			if($transaction_alert->account_number){
				$arr[] = $transaction_alert->account_number;
			}
		endforeach;
		$this->select_all_secure('bank_accounts');
		if($bank_id){
			$this->db->where('banks.id',$bank_id);
		}
		$this->db->where($this->dx('bank_accounts.is_verified').'  = 1 ',NULL,FALSE);
		if($arr){
			$this->db->where($this->dx('bank_accounts.account_number').'  IN ('.implode(',',$arr).') ',NULL,FALSE);
		}
        $this->db->where("(".$this->dx('bank_accounts.is_deleted').' IS NULL OR '.$this->dx('bank_accounts.is_deleted').' = 0 ) ',NULL,FALSE);
    	$this->db->where($this->dx('banks.partner').'="1"',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        return $this->db->get('bank_accounts')->result();
	}

	function get_bank_accounts_without_account_currency_id(){
		$this->db->select(
			array(
				$this->dx('bank_accounts.id')." as bank_account_id",
				$this->dx('bank_accounts.account_number')." as account_number",
				$this->dx('bank_accounts.account_name')." as account_name",
				$this->dx('bank_accounts.account_currency_id')." as account_currency_id",
			)
		);
		$this->db->where($this->dx('bank_accounts.account_currency_id').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('bank_accounts.is_verified').' ="1"',NULL,FALSE);
		$this->db->join('banks','banks.id ='.$this->dx('bank_accounts.bank_id'),'inner');
		return $this->db->get('bank_accounts')->result();
	}
}