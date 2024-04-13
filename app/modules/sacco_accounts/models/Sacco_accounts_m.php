<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
Class Sacco_accounts_m extends MY_Model{

	protected $_table = 'sacco_accounts';

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
			create table if not exists sacco_accounts(
				id int not null auto_increment primary key,
				`sacco_id` blob,
				`sacco_branch_id` blob,
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
	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE)
	{
		return $this->insert_secure_data('sacco_accounts',$input);
	}

	function update($id,$input=array(),$SKIP_VALIDATION=FALSE)
	{
		return $this->update_secure_data($id,'sacco_accounts',$input);
	}

	function get($id=0){
		$this->select_all_secure('sacco_accounts');
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where('id',$id);
		return $this->db->get('sacco_accounts')->row();
	}

	function get_group_sacco_account($id=0,$group_id=0)
	{
		$this->select_all_secure('sacco_accounts');
		$this->db->where('id',$id);
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        if($group_id){
        	$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
        	$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
		return $this->db->get('sacco_accounts')->row();
	}

	function get_group_sacco_account_current_balance($id = 0,$group_id = 0){
		$this->db->select(
			array($this->dx('current_balance').' as current_balance ')
		);
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		if($sacco_account = $this->db->get('sacco_accounts')->row()){
			return $sacco_account->current_balance;
		}else{
			return 0;
		}
	}

	function get_group_total_sacco_account_balance($group_id = 0){
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
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		if($sacco_account = $this->db->get('sacco_accounts')->row()){
			return (floatval($sacco_account->initial_balance)+floatval($sacco_account->current_balance))?:0;
		}else{
			return 0;
		}
	}

	function get_group_total_initial_sacco_balance($group_id=0){
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
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);

		if($sacco_account = $this->db->get('sacco_accounts')->row()){
			return $sacco_account->initial_balance?:0;
		}else{
			return 0;
		}
	}


	function get_group_sacco_account_balance($id = 0,$group_id = 0){
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
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);

		if($sacco_account = $this->db->get('sacco_accounts')->row()){
			return $sacco_account->current_balance + $sacco_account->initial_balance;
		}else{
			return 0;
		}
	}

	function get_group_sacco_account_number($id = 0,$group_id = 0){
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

        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);

		if($sacco_account = $this->db->get('sacco_accounts')->row()){
			return $sacco_account->account_number;
		}else{
			return 0;
		}
	}

	function get_all(){
		$this->select_all_secure('sacco_accounts');
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		return $this->db->get('sacco_accounts')->result();
	}

	function delete($id = 0,$group_id = 0){
		$input = array(
			'modified_on' => time(),
			//'modified_by' => $this->user->id,
			'is_deleted' => 1
		);
		return $this->update($id,$input);
	}

	function check_if_account_exists($id='',$account_number='',$sacco_id='')
	{
		$this->db->where($this->dx('account_number').'="'.$account_number.'"',NULL,FALSE);
		$this->db->where($this->dx('sacco_id').'="'.$sacco_id.'"',NULL,FALSE);
		if($id){
			$this->db->where('id !=',$id);
		}
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		return $this->db->get('sacco_accounts')->result();
	}

	function count_group_sacco_accounts($group_id =0,$filter_params=array()){
		if($filter_params){
			foreach ($filter_params as $key => $value) {
				if($value && $this->db->field_exists($key, 'sacco_accounts')){
					$this->db->where("
					CONVERT(" . $this->dx($key) . " USING 'latin1')  like '%" . $this->escape_str($value) . "%'
					",NULL,FALSE);
				}
			}
		}
		if(!$group_id){
			if(isset($this->group->id))
			{
				$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
			}
		}else{
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('saccos','saccos.id ='.$this->dx('sacco_accounts.sacco_id'),'inner');
		$this->db->join('sacco_branches','sacco_branches.id ='.$this->dx('sacco_accounts.sacco_branch_id'),'inner');
		return $this->db->count_all_results('sacco_accounts');
	}

	function get_group_sacco_accounts($group_id =0,$filter_params=array()){
		$this->select_all_secure('sacco_accounts');

		$this->db->select(array(
				$this->dx('saccos.name').'as sacco_name',
				$this->dx('sacco_branches.name').'as sacco_branch',
			));
		if($filter_params){
			foreach ($filter_params as $key => $value) {
				if($value && $this->db->field_exists($key, 'sacco_accounts')){
					$this->db->where("
					CONVERT(" . $this->dx($key) . " USING 'latin1')  like '%" . $this->escape_str($value) . "%'
					",NULL,FALSE);
				}
			}
		}
		if(!$group_id)
		{
			if(isset($this->group->id))
			{
				$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
			}
		}else
		{
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
			//$this->db->where('('.$this->dx('is_closed').'!="1" OR '.$this->dx('is_closed').'is NULL )',NULL,FALSE);
		}
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('saccos','saccos.id ='.$this->dx('sacco_accounts.sacco_id'),'inner');
		$this->db->join('sacco_branches','sacco_branches.id ='.$this->dx('sacco_accounts.sacco_branch_id'),'inner');

		return $this->db->get('sacco_accounts')->result();
	}

	function count_all()
	{
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		return $this->db->count_all_results('sacco_accounts');
	}

	function get_group_active_sacco_accounts($group_id =0){
		$this->select_all_secure('sacco_accounts');
		$this->db->select(array(
				$this->dx('saccos.name').'as sacco_name',
				$this->dx('sacco_branches.name').'as sacco_branch',
			));
		if(!$group_id){
			if(isset($this->group->id)){
				$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
			}
		}else{
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE); 
			$this->db->where('('.$this->dx('is_closed').'!="1" OR '.$this->dx('is_closed').'is NULL )',NULL,FALSE);
		}
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->where($this->dx('sacco_accounts.active').' = "1" ',NULL,FALSE);
		$this->db->join('saccos','saccos.id ='.$this->dx('sacco_accounts.sacco_id'),'inner');
		$this->db->join('sacco_branches','sacco_branches.id ='.$this->dx('sacco_accounts.sacco_branch_id'),'INNER');
		return $this->db->get('sacco_accounts')->result();
	}
}