<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
Class Mobile_money_accounts_m extends MY_Model{

	protected $_table = 'mobile_money_accounts';

	function __construct()
	{
		parent::__construct();

		//$this->install();
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

	function install()
	{
		$this->db->query("
			create table if not exists mobile_money_accounts(
				id int not null auto_increment primary key,
				`account_name` blob,
				`mobile_money_provider_id` blob,
				`account_number` blob,
				`group_id` blob,
				`initial_balance` blob,
				`current_balance` blob,
				`signatory_phone` blob,
				`signatory_id_number` blob,
				`is_verified` blob,
				`active` blob,
				`is_closed` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_on` blob,
				`modified_by` blob
		)");
	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE)
	{
		return $this->insert_secure_data('mobile_money_accounts',$input);
	}

	function update($id,$input=array(),$SKIP_VALIDATION=FALSE)
	{
		return $this->update_secure_data($id,'mobile_money_accounts',$input);
	}

	function get($id=0)
	{
		$this->select_all_secure('mobile_money_accounts');
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where('id',$id);
		return $this->db->get('mobile_money_accounts')->row();
	}


	function get_group_mobile_money_account($id=0,$group_id=0)
	{
		$this->select_all_secure('mobile_money_accounts');
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->get('mobile_money_accounts')->row();
	}


	function get_group_mobile_money_account_current_balance($id = 0,$group_id = 0){
		$this->db->select(
			array($this->dx('current_balance').' as current_balance ')
		);
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		if($mobile_money_account = $this->db->get('mobile_money_accounts')->row()){
			return $mobile_money_account->current_balance;
		}else{
			return 0;
		}
	}

	function get_group_mobile_money_account_balance($id = 0,$group_id = 0){
		$this->db->select(
			array(
				$this->dx('current_balance').' as current_balance ',
				$this->dx('initial_balance').' as initial_balance ',
				)
		);
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		if($mobile_money_account = $this->db->get('mobile_money_accounts')->row()){
			return $mobile_money_account->current_balance + $mobile_money_account->initial_balance;
		}else{
			return 0;
		}
	}

	function get_group_mobile_money_account_number($id = 0,$group_id = 0){
		$this->db->select(
			array(
				$this->dx('account_number').' as account_number ',
				)
		);
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		if($mobile_money_account = $this->db->get('mobile_money_accounts')->row()){
			return $mobile_money_account->account_number;
		}else{
			return 0;
		}
	}


	function get_group_total_initial_mobile_money_balance($group_id=0){
		$this->db->select(
			array(
				'sum('.$this->dx('initial_balance').') as initial_balance ',
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		if($mobile_money_account = $this->db->get('mobile_money_accounts')->row()){
			return $mobile_money_account->initial_balance?:0;
		}else{
			return 0;
		}
	}

	
	function get_group_total_mobile_money_account_balance($group_id = 0){
		$this->db->select(
			array(
				'sum('.$this->dx('current_balance').') as current_balance',
				'sum('.$this->dx('initial_balance').') as initial_balance ',
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		if($mobile_money_account = $this->db->get('mobile_money_accounts')->row()){
			return (floatval($mobile_money_account->current_balance)+floatval($mobile_money_account->initial_balance))?:0;
		}else{
			return 0;
		}
	}

	function get_all()
	{
		$this->select_all_secure('mobile_money_accounts');
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		return $this->db->get('mobile_money_accounts')->result();
	}

	function delete($id=0,$group_id = 0)
	{
		$input = array(
			'modified_on' => time(),
			//'modified_by' => $this->user->id,
			'is_deleted' => 1
		);
		return $this->update($id,$input);
	}

	function check_if_account_exists($id='',$mobile_money_provider_id='',$account_number='')
	{
		$this->db->where($this->dx('account_number').'="'.$account_number.'"',NULL,FALSE);
		$this->db->where($this->dx('mobile_money_provider_id').'="'.$mobile_money_provider_id.'"',NULL,FALSE);
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		if($id){
			$this->db->where('id !=',$id);
		}
		return $this->db->get('mobile_money_accounts')->result();
	}


	function count_all(){
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		return $this->db->count_all_results('mobile_money_accounts');
	}

	function count_group_mobile_money_accounts($group_id=0,$filter_params = array()){
		if($filter_params){
			foreach ($filter_params as $key => $value) {
				if($value && $this->db->field_exists($key, 'mobile_money_accounts')){
					$this->db->where("
					CONVERT(" . $this->dx($key) . " USING 'latin1')  like '%" . $this->escape_str($value) . "%'
					",NULL,FALSE);
				}
			}
		}
		if(!$group_id){
			if(isset($this->group->id)){
				$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
			}
		}else{
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('mobile_money_providers','mobile_money_providers.id = '.$this->dx('mobile_money_accounts.mobile_money_provider_id'),'inner');
		return $this->db->count_all_results('mobile_money_accounts');
	}

	function get_group_mobile_money_accounts($group_id=0,$filter_params=array()){
		$this->select_all_secure('mobile_money_accounts');
		$this->db->select(array(
				$this->dx('mobile_money_providers.name').'as mobile_money_provider_name',
			));
		if($filter_params){
			foreach ($filter_params as $key => $value) {
				if($value && $this->db->field_exists($key, 'mobile_money_accounts')){
					$this->db->where("
					CONVERT(" . $this->dx($key) . " USING 'latin1')  like '%" . $this->escape_str($value) . "%'
					",NULL,FALSE);
				}
			}
		}
		if(!$group_id){
			if(isset($this->group->id)){
				$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
			}
		}else{
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('mobile_money_accounts.active').'= "1"',NULL,FALSE);
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('mobile_money_providers','mobile_money_providers.id = '.$this->dx('mobile_money_accounts.mobile_money_provider_id'),'inner');
		return $this->db->get('mobile_money_accounts')->result();
	}

	function get_group_verified_mobile_money_account_number_list($group_id=0){
		$account_number_list = "0";
		$this->db->select(
			array($this->dx('account_number').' as account_number ')
		);
		//$this->db->where($this->dx('is_verified').'="1"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);

		$this->db->where($this->dx('partner').'="1"',NULL,FALSE);
		$this->db->join('mobile_money_providers','mobile_money_providers.id ='.$this->dx('mobile_money_accounts.mobile_money_provider_id'),'inner');
		$result = $this->db->get('mobile_money_accounts')->result();
		if($result){
			$count = 1;
			foreach($result as $row){
				if($count==1){
					$account_number_list="'".$row->account_number."'";
				}else{
					$account_number_list.=",'".$row->account_number."'";
				}
				$count++;
			}
		}
		return $account_number_list;
	}

	function get_group_verified_mobile_money_account($group_id=0){
		$this->select_all_secure('mobile_money_accounts');
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('partner').'="1"',NULL,FALSE);
		$this->db->join('mobile_money_providers','mobile_money_providers.id ='.$this->dx('mobile_money_accounts.mobile_money_provider_id'),'inner');
		$result = $this->db->get('mobile_money_accounts')->row();
		return $result;
	}

	function get_groups_verified_mobile_money_accounts($group_list = '0'){
		$this->select_all_secure('mobile_money_accounts');
		$this->db->where($this->dx('group_id').' IN ('.$group_list.')',NULL,FALSE);
		$this->db->where($this->dx('partner').'="1"',NULL,FALSE);
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('mobile_money_providers','mobile_money_providers.id ='.$this->dx('mobile_money_accounts.mobile_money_provider_id'),'inner');
		$result = $this->db->get('mobile_money_accounts')->result();
		$account_number_list = '0';
		if($result){
			$count = 1;
			foreach($result as $row){
				if($count==1){
					$account_number_list="'".$row->account_number."'";
				}else{
					$account_number_list.=",'".$row->account_number."'";
				}
				$count++;
			}
		}

		return $account_number_list;
	}

	function get_group_verified_partner_mobile_account_options($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				$this->dx('account_number').' as account_number',
				$this->dx('account_name').' as account_name',
				$this->dx('is_verified').' as is_verified',
			)
		);
        $this->db->select(array($this->dx('mobile_money_providers.name').'as mobile_money_provider_name'));
    	if($group_id){
        	$this->db->where($this->dx('mobile_money_accounts.group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
        	$this->db->where($this->dx('mobile_money_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);

    	//$this->db->where($this->dx('mobile_money_accounts.is_verified').'  = 1 ',NULL,FALSE);
    	$this->db->where($this->dx('mobile_money_providers.partner').'="1"',NULL,FALSE);
        $this->db->join('mobile_money_providers',$this->dx('mobile_money_accounts.mobile_money_provider_id').'= mobile_money_providers.id','INNER');
    	$mobile_money_accounts = $this->db->get('mobile_money_accounts')->result();
    	foreach ($mobile_money_accounts as $key => $value) {
    		$arr[$value->account_number] = $value->mobile_money_provider_name.' - '.$value->account_name.' ('.$value->account_number.')';
        }
        return $arr;
	}

	function get_group_mobile_money_account_id_by_account_number($account_number=0,$group_id=0){
		$this->db->select('id');
		$this->db->where($this->dx('account_number').'="'.$account_number.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$return = $this->db->get('mobile_money_accounts')->row();
		if($return){
			return $return->id;
		}else{
			return 0;
		}
	}

	function get_group_active_mobile_money_accounts($group_id = 0){
		$this->select_all_secure('mobile_money_accounts');
		$this->db->select(array(
			$this->dx('mobile_money_providers.name').'as mobile_money_provider_name',
		));
		if(!$group_id)
		{
			if(isset($this->group->id))
			{
				$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
			}
		}
		else{
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}
		$this->db->where('('.$this->dx('mobile_money_accounts.is_closed').'!="1" OR '.$this->dx('mobile_money_accounts.is_closed').'is NULL )',NULL,FALSE);
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->where($this->dx('mobile_money_accounts.active').' = "1" ',NULL,FALSE);
		$this->db->join('mobile_money_providers','mobile_money_providers.id = '.$this->dx('mobile_money_accounts.mobile_money_provider_id'),'inner');
		return $this->db->get('mobile_money_accounts')->result();
	}
}