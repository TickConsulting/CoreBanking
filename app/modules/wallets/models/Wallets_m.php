<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Wallets_m extends MY_Model {

	protected $_table = 'wallets';

	public function __construct(){
		parent::__construct();
		$this->load->dbforge();
		$this->install();
	}

	public function install(){
		$this->db->query("
		create table if not exists wallets(
			id int not null auto_increment primary key,
			`name` blob,
			`logo` blob,
			`channel` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");

		$this->db->query("
		create table if not exists wallet_country_pairing(
			id int not null auto_increment primary key,
			`wallet_id` blob,
			`country_id` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	}

	public function insert($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('wallets',$input);
	}

	function update($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'wallets',$input);
	}

	function delete($id){
		$this->db->where('id',$id);
		return $this->db->delete('wallets');
	}

	function is_unique_channel($channel_number=0,$id=0){
		$this->db->where($this->dx('channel').' = "'.$channel_number.'"',NULL,FALSE);
		if($id){
			$this->db->where('id !=',$id);
		}
		return $this->db->count_all_results('wallets')?0:1;
	}

	function batch_insert_country_wallet_pairing($input,$skip_validation=FALSE){
        return $this->insert_batch_secure_data('wallet_country_pairing',$input);
    }

    function get($id=0){
    	$this->select_all_secure('wallets');
    	$this->db->where('id',$id);
    	return $this->db->get('wallets')->row();
    }

    function get_all(){
    	$this->select_all_secure('wallets');
    	return $this->db->get('wallets')->result();
    }

    function get_wallet_countrys($id=0){
    	$this->db->select(array(
    		$this->dxa('country_id'),
    	));
    	$this->db->where($this->dx('wallet_id').' ="'.$id.'"',NULL,FALSE);
    	$results =  $this->db->get('wallet_country_pairing')->result();
    	$arr = array();
    	if($results){
    		foreach ($results as $result) {
    			$arr[] = $result->country_id;
    		}
    	}
    	return $arr;
    }

    function get_wallet_country_pairings(){
    	$this->select_all_secure('wallet_country_pairing');

    	$results =  $this->db->get('wallet_country_pairing')->result();
    	$arr = array();
    	foreach ($results as $result) {
    		$arr[$result->wallet_id][]=$result->country_id;
    	}
    	return $arr;
    }

    function delete_wallet_country_pairings($id=0){
    	$this->db->where($this->dx('wallet_id').' ="'.$id.'"',NULL,FALSE);
    	return $this->db->delete('wallet_country_pairing');
    }

	function get_wallet_account($group_id=0){
		$this->select_all_secure('bank_accounts');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('bank_accounts.is_verified').' = "1"',NULL,FALSE);
		return $this->db->get('bank_accounts')->row();
	}

	function get_wallet_accounts($group_id=0){
		// $this->select_all_secure('bank_accounts');
		$this->db->select(
			array(
				'bank_accounts.id as id',
				$this->dx('account_number').' as account_number',
				$this->dx('account_name').' as account_name',
				$this->dx('actual_balance').' as actual_balance',
				$this->dx('initial_balance').' as initial_balance',
				$this->dx('current_balance').' as current_balance',
				$this->dx('account_currency_id').' as account_currency_id',
				$this->dx('is_verified').' as is_verified',
				$this->dx('banks.name').' as bank_name',
				$this->dx('bank_branches.name').' as bank_branch_name',

			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('bank_accounts.is_verified').' = "1"',NULL,FALSE);
		$this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        $this->db->join('bank_branches',$this->dx('bank_accounts.bank_branch_id').'= bank_branches.id','INNER');
		return $this->db->get('bank_accounts')->result();
	}

	function get_group_account_balance($group_id = 0){
		$this->db->select(
			array(
				'sum('.$this->dx('current_balance').') as current_balance ',
				'sum('.$this->dx('initial_balance').') as initial_balance',
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_verified').' ="1"',NULL,FALSE);
		if($bank_account = $this->db->get('bank_accounts')->row()){
			return (floatval($bank_account->current_balance)+floatval($bank_account->initial_balance))?:0;
		}else{
			return 0;
		}
	}

	function get_group_total_deposits($group_id = 0, $wallet_ids = 0){
		$this->db->select(
			array(
				' SUM('.$this->dx('amount').') as amount '
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		if($wallet_ids){
			$this->db->where($this->dx('account_id').' IN('.$wallet_ids.')',NULL,FALSE);
		}
		$this->db->limit(1);
		$result = $this->db->get('deposits')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_total_withdrawals($group_id = 0, $wallet_ids = 0){
		$this->db->select(
			array(
				' SUM('.$this->dx('amount').') as amount '
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		if($wallet_ids){
			$this->db->where($this->dx('account_id').' IN('.$wallet_ids.')',NULL,FALSE);
		}		
		$this->db->limit(1);
		$result = $this->db->get('withdrawals')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_total_expenses($group_id = 0, $wallet_ids = 0){
		$this->db->select(
			array(
				' SUM('.$this->dx('amount').') as amount '
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		if($wallet_ids){
			$this->db->where($this->dx('account_id').' IN('.$wallet_ids.')',NULL,FALSE);
		}	
		$this->db->where($this->dx('type').' IN (1,2,3,4) ',NULL,FALSE);
		$this->db->limit(1);
		$result = $this->db->get('withdrawals')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_deposits_summary_by_type($group_id=0,$wallet_ids=0){
		$this->db->select(
			array(
				' SUM('.$this->dx('amount').') as amount ',
				$this->dx('type').' as type '
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		if($wallet_ids){
			$this->db->where($this->dx('account_id').' IN('.$wallet_ids.')',NULL,FALSE);
		}	
		$this->db->group_by(array(
			'type',
		));
		return $this->db->get('deposits')->result();
	}

	function get_withdrawal_summary_by_type($group_id=0,$wallet_ids=0){
		$this->db->select(
			array(
				' SUM('.$this->dx('amount').') as amount ',
				$this->dx('type').' as type '
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		if($wallet_ids){
			$this->db->where($this->dx('account_id').' IN('.$wallet_ids.')',NULL,FALSE);
		}	
		$this->db->group_by(array(
			'type',
		));
		return $this->db->get('withdrawals')->result();
	}

	function get_wallets_in_country($country_id=0){
		$this->select_all_secure('wallets');
		$this->db->where($this->dx('wallet_country_pairing.country_id').' = "'.$country_id.'"',NULL,FALSE);
		$this->db->join('wallet_country_pairing',$this->dx('wallet_country_pairing.wallet_id').' = wallets.id');
		return $this->db->get('wallets')->result();
	}
}
?>