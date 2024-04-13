<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
Class Petty_cash_accounts_m extends MY_Model{

	protected $_table = 'petty_cash_accounts';

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
			create table if not exists petty_cash_accounts(
				id int not null auto_increment primary key,
				`account_name` blob,
				`account_slug` blob,
				`group_id` blob,
				`initial_balance` blob,
				`current_balance` blob,
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
		return $this->insert_secure_data('petty_cash_accounts',$input);
	}

	function insert_batch($input=array(),$SKIP_VALIDATION = FALSE)
	{
		return $this->insert_batch_secure_data('petty_cash_accounts',$input);
	}

	function update($id,$input=array(),$SKIP_VALIDATION=FALSE)
	{
		return $this->update_secure_data($id,'petty_cash_accounts',$input);
	}

	function get($id=0){
		$this->select_all_secure('petty_cash_accounts');
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where('id',$id);
		return $this->db->get('petty_cash_accounts')->row();
	}

	function get_group_petty_cash_account($id=0,$group_id=0)
	{
		$this->select_all_secure('petty_cash_accounts');
		$this->db->where('id',$id);
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        if($group_id){
        	$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
        	$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
		return $this->db->get('petty_cash_accounts')->row();
	}

	function get_group_petty_cash_account_current_balance($id = 0,$group_id = 0){
		$this->db->select(
			array($this->dx('current_balance').' as current_balance ')
		);
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		if($petty_cash_account = $this->db->get('petty_cash_accounts')->row()){
			return $petty_cash_account->current_balance;
		}else{
			return 0;
		}
	}

	function get_group_petty_cash_account_balance($id = 0,$group_id = 0){
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
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);

		if($petty_cash_account = $this->db->get('petty_cash_accounts')->row()){
			return $petty_cash_account->current_balance + $petty_cash_account->initial_balance;
		}else{
			return 0;
		}
	}

	function get_group_total_petty_cash_account_balance($group_id = 0){
		$this->db->select(
			array(
				'sum('.$this->dx('current_balance').') as current_balance',
				'sum('.$this->dx('initial_balance').') as initial_balance',
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('is_system').' IS NULL ',NULL,FALSE);
		if($petty_cash_account = $this->db->get('petty_cash_accounts')->row()){
			return (floatval($petty_cash_account->current_balance)?:0) + (floatval($petty_cash_account->initial_balance)?:0);
		}else{
			return 0;
		}
	}

	function get_group_total_initial_petty_cash_balance($group_id=0){
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

        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);

		if($petty_cash_account = $this->db->get('petty_cash_accounts')->row()){
			return $petty_cash_account->initial_balance?:0;
		}else{
			return 0;
		}
	}

	function get_all($group_id = 0)
	{
		$this->select_all_secure('petty_cash_accounts');
		if(!$group_id)
		{
			if(isset($this->group->id))
			{
				$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
			}
		}
		else
		{
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
			//$this->db->where('('.$this->dx('is_closed').'!="1" OR '.$this->dx('is_closed').'is NULL )',NULL,FALSE);

		}

        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);

		$this->db->where($this->dx('is_system').' IS NULL ',NULL,FALSE);
		return $this->db->get('petty_cash_accounts')->result();
	}

	function delete($id=0,$group_id=0)
	{
		$input = array(
			'modified_on' => time(),
			//'modified_by' => $this->user->id,
			'is_deleted' => 1
		);
		return $this->update($id,$input);
	}

	function check_if_account_exists($id='',$account_slug='',$group_id = 0)
	{
		$this->db->where($this->dx('account_slug').'="'.$account_slug.'"',NULL,FALSE);
		if($id){
			$this->db->where('id !=',$id);
		}
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        if($group_id){
        	$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
        	$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
		return $this->db->get('petty_cash_accounts')->result();
	}


	function count_all()
	{
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);

		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('is_system').' IS NULL ',NULL,FALSE);
		return $this->db->count_all_results('petty_cash_accounts');
	}

	function check_if_group_account_exists($id = 0,$group_id = 0){
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);


		return $this->db->count_all_results('petty_cash_accounts')?:0;
	}

	function get_group_back_dating_petty_cash_suspense_account(){
		$this->select_all_secure('petty_cash_accounts');
		$this->db->where($this->dx('account_slug')." = 'back-dating-petty-cash-suspense-account' ",NULL,FALSE);
		$this->db->where($this->dx('group_id')." = '".$this->group->id."' ",NULL,FALSE);
		$this->db->where($this->dx('is_system')." = '1' ",NULL,FALSE);
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('active')." = '0' ",NULL,FALSE);
		$this->db->limit(1);
		$suspense_petty_cash_account = $this->db->get('petty_cash_accounts')->row();
		if($suspense_petty_cash_account){
			return $suspense_petty_cash_account;
		}else{
			$input = array(
				'is_system' => 1,
				'active' => 0,
				'group_id' => $this->group->id,
				'account_slug' => 'back-dating-petty-cash-suspense-account',
				'account_name' => 'Back-dating Petty Cash Suspense Account',
				'initial_balance' => 0,
				'current_balance' => 0,
				'created_on' => time(),
				'created_by' => $this->user->id
			);
			if($id = $this->insert($input)){
				if($petty_cash_account = $this->get_group_petty_cash_account($id)){
					return $petty_cash_account;
				}else{
					return FALSE;
				}
			}else{
				return FALSE;
			}
		}
	}

	function get_group_active_petty_cash_accounts($group_id=0){
		$this->select_all_secure('petty_cash_accounts');
		if(!$group_id){
			if(isset($this->group->id)){
				$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
			}
		}else{
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE); 
		}
		$this->db->where('('.$this->dx('petty_cash_accounts.is_closed').'!="1" OR '.$this->dx('petty_cash_accounts.is_closed').'is NULL )',NULL,FALSE);
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->where($this->dx('petty_cash_accounts.active').' = "1" ',NULL,FALSE);
		return $this->db->get('petty_cash_accounts')->result();
	}

	function count_group_petty_cash_accounts($group_id=0,$filter_params=array()){
		if($filter_params){
			foreach ($filter_params as $key => $value) {
				if($value && $this->db->field_exists($key, 'petty_cash_accounts')){
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
		$this->db->where('('.$this->dx('petty_cash_accounts.is_closed').'!="1" OR '.$this->dx('petty_cash_accounts.is_closed').'is NULL )',NULL,FALSE);
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		return $this->db->count_all_results('petty_cash_accounts');
	}

	function get_group_petty_cash_accounts($group_id=0,$filter_params=array()){
		$this->select_all_secure('petty_cash_accounts');
		if($filter_params){
			foreach ($filter_params as $key => $value) {
				if($value && $this->db->field_exists($key, 'petty_cash_accounts')){
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
		$this->db->where('('.$this->dx('petty_cash_accounts.is_closed').'!="1" OR '.$this->dx('petty_cash_accounts.is_closed').'is NULL )',NULL,FALSE);
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		return $this->db->get('petty_cash_accounts')->result();
	}
}