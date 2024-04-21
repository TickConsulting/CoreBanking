<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Reports_m extends MY_Model {
	//protected $group = array();

	protected $interest_paid_per_year_array = array();
	protected $principal_paid_per_year_array = array();
	protected $over_payments_per_year_array = array();

	function __construct(){
		parent::__construct();
		$this->load->dbforge();
		////$this->install();
		$this->load->model('deposits/deposits_m');
	}

	function install()
	{
		$this->db->query("
			create table if not exists group_bank_branch_pairings(
				id int not null auto_increment primary key,
				`group_id` blob,
				`bank_id` blob,
				`bank_branch_id` blob,
				`created_by` blob,
				`created_on` blob
			)"
		);
	}

	function insert_group_bank_branch_pairing($input = array(),$key=FALSE){
		return $this->insert_secure_data('group_bank_branch_pairings', $input);
	}

	function delete_group_bank_branch_pairing($group_id = 0,$bank_id = 0,$bank_branch_id = 0){
		$this->db->where($this->dx('group_id').' = '.$group_id.' ',NULL,FALSE);
		$this->db->where($this->dx('bank_id').' = '.$bank_id.' ',NULL,FALSE);
		$this->db->where($this->dx('bank_branch_id').' = '.$bank_branch_id.' ',NULL,FALSE);
		return $this->db->delete('group_bank_branch_pairings');
	}

	function get_group_bank_branch_pairings_array(){
		$this->select_all_secure('group_bank_branch_pairings');
		$group_bank_branch_pairings = $this->db->get('group_bank_branch_pairings')->result();
		$arr = array();
		foreach($group_bank_branch_pairings as $group_bank_branch_pairing):
			$arr[$group_bank_branch_pairing->group_id] = $group_bank_branch_pairing->bank_branch_id;
		endforeach;
		return $arr;
	}

	function get_group_bank_branch_pairing_arrays(){
		$this->select_all_secure('group_bank_branch_pairings');
		$group_bank_branch_pairings = $this->db->get('group_bank_branch_pairings')->result();
		$arr = array();
		foreach($group_bank_branch_pairings as $group_bank_branch_pairing):
			$arr[$group_bank_branch_pairing->group_id][] = $group_bank_branch_pairing->bank_branch_id;
		endforeach;
		return $arr;
	}

	function get_group_bank_branch_pairings_array_group_id_and_bank_branch_id_as_key(){
		$this->select_all_secure('group_bank_branch_pairings');
		$group_bank_branch_pairings = $this->db->get('group_bank_branch_pairings')->result();
		$arr = array();
		foreach($group_bank_branch_pairings as $group_bank_branch_pairing):
			$arr[$group_bank_branch_pairing->group_id][$group_bank_branch_pairing->bank_branch_id] = $group_bank_branch_pairing->bank_branch_id;
		endforeach;
		return $arr;
	}

	function average_number_of_members_per_group(){
		$this->db->select(
			array(
				'sum('.$this->dx('active_size').') as size ',
			)
		);
		$this->db->where($this->dx('status').' = 1 ',NULL,FALSE);
		$result = $this->db->get('investment_groups')->row();
		$size = $result->size;

		$this->db->where($this->dx('status').' = 1 ',NULL,FALSE);
		$count = $this->db->count_all_results('investment_groups');
		return round($size/$count);
	}

	function average_contribution_amounts_per_group_per_member_per_month(){
		//get monthly contributions
		$this->db->select(
			array(
				'sum('.$this->dx('contributions.amount').')/count('.$this->dx('contributions.group_id').')  as average_amount ',
			)
		);
		$this->db->where($this->dx('contributions.active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('contributions.type').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('investment_groups.status').' = 1 ',NULL,FALSE);
		$this->db->where($this->dx('regular_contribution_settings.contribution_frequency').' = "1" ',NULL,FALSE);
		$this->db->join('regular_contribution_settings','contributions.id = '.$this->dx('regular_contribution_settings.contribution_id'));
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('contributions.group_id'));
		$result = $this->db->get('contributions')->row();
		return $result->average_amount;
	}

	function average_bank_balance_per_group_bank_account(){
		$this->db->select(
			array(
				'sum('.$this->dx('current_balance').')/count('.$this->dx('bank_accounts.group_id').') as average_balance ',
			)
		);
		$this->db->where($this->dx('bank_accounts.active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('investment_groups.status').' = 1 ',NULL,FALSE);
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('bank_accounts.group_id'));
		$result = $this->db->get('bank_accounts')->row();
		return $result->average_balance;
	}

	function get_group_total_loan_principal_paid_per_month_array($group_id = 0){
		$this->_distribute_principal_and_interest_paid_per_month($group_id);
		return $this->principal_paid_per_month_array;
	}

	function get_group_member_total_contributions_paid_per_contribution_array($group_member_id = 0){
		//Get contributions paid
		$member_total_contributions_per_contribution_array = $this->deposits_m->get_group_member_total_contributions_per_contribution_array($this->group->id,0,0,$group_member_id);
		
		//Subtract contribution refunds
		$member_total_contribution_refunds_per_contribution_array = $this->withdrawals_m->get_group_member_total_contribution_refunds_per_contribution_array($this->group->id,$group_member_id);
        foreach($member_total_contributions_per_contribution_array as $member_id => $contribution):
        	foreach($contribution as $contribution_id => $amount):
        		$member_total_contributions_per_contribution_array[$member_id][$contribution_id] -= $member_total_contribution_refunds_per_contribution_array[$member_id][$contribution_id];
        	endforeach;
        endforeach;   
        //Subtract contribution transfers from respective contributions
        $member_total_contribution_transfers_from_per_contribution_array = $this->statements_m->get_group_member_total_contribution_transfers_from_per_contribution_array($this->group->id,0,0,$group_member_id);

        foreach($member_total_contributions_per_contribution_array as $member_id => $contribution):
        	foreach($contribution as $contribution_id => $amount):
        		$member_total_contributions_per_contribution_array[$member_id][$contribution_id] -= 
        	$member_total_contribution_transfers_from_per_contribution_array[$member_id][$contribution_id];
        	endforeach;
        endforeach;
        //Add contribution transfers to respective contributions 
        $member_total_contribution_transfers_to_per_contribution_array = $this->statements_m->get_group_member_total_contribution_transfers_to_per_contribution_array($this->group->id,0,0,$group_member_id);
		foreach($member_total_contributions_per_contribution_array as $member_id => $contribution):
        	foreach($contribution as $contribution_id => $amount):
        		$member_total_contributions_per_contribution_array[$member_id][$contribution_id] += $member_total_contribution_transfers_to_per_contribution_array[$member_id][$contribution_id];
        	endforeach;
        endforeach;
        //Subtract contribution transfers to fines
        $members_contribution_transfers_from_contribution_to_fine_category_per_contribution_array = $this->statements_m->get_group_members_contribution_transfers_from_contribution_to_fine_category_per_contribution_array($this->group->id,0,0,$group_member_id);
        foreach($member_total_contributions_per_contribution_array as $member_id => $contribution):
        	foreach($contribution as $contribution_id => $amount):
        		$member_total_contributions_per_contribution_array[$member_id][$contribution_id] -= $members_contribution_transfers_from_contribution_to_fine_category_per_contribution_array[$member_id][$contribution_id];
        	endforeach;
        endforeach;
        
        //subtract ignored contribution to transfer
        $member_total_contribution_transfers_to_ignore_per_contribution_array = $this->statements_m->get_group_member_total_contribution_transfers_to_ignore_per_contribution_array($this->group->id,0,0,$group_member_id);
		foreach($member_total_contributions_per_contribution_array as $member_id => $contribution):
        	foreach($contribution as $contribution_id => $amount):
        		$member_total_contributions_per_contribution_array[$member_id][$contribution_id] -= ($member_total_contribution_transfers_to_ignore_per_contribution_array[$member_id][$contribution_id]>0)?$member_total_contribution_transfers_to_ignore_per_contribution_array[$member_id][$contribution_id]:0;
        	endforeach;
        endforeach;

        //add ignored contribution from transfer
        $member_total_contribution_transfers_from_ignore_per_contribution_array = $this->statements_m->get_group_member_total_contribution_transfers_from_ignore_per_contribution_array($this->group->id,0,0,$group_member_id);
		foreach($member_total_contributions_per_contribution_array as $member_id => $contribution):
        	foreach($contribution as $contribution_id => $amount):
        		$member_total_contributions_per_contribution_array[$member_id][$contribution_id] += ($member_total_contribution_transfers_from_ignore_per_contribution_array[$member_id][$contribution_id]>0)?$member_total_contribution_transfers_from_ignore_per_contribution_array[$member_id][$contribution_id]:0;
        	endforeach;
        endforeach;
        return $member_total_contributions_per_contribution_array;
	}

	function get_group_total_contributions_paid_per_contribution_array($group_id = 0,$from = 0,$to = 0,$contribution_options = array()){
		//Get contributions paid
		if(empty($contribution_options)){
			$contribution_id_list = "";
		}else{
			
			$count = 1;
			foreach($contribution_options as $contribution_id => $name):
				if($count == 1){
					$contribution_id_list = $contribution_id;
				}else{
					$contribution_id_list .= ",".$contribution_id;
				}
				$count++;
			endforeach;
		}
		$group_total_contributions_per_contribution_array = $this->deposits_m->get_group_total_contributions_per_contribution_array($this->group->id,$to,$from,$contribution_id_list);
		//Subtract contribution refunds
		$group_total_contribution_refunds_per_contribution_array = $this->withdrawals_m->get_group_total_contribution_refunds_per_contribution_array($this->group->id,$to,$from,$contribution_id_list);

        foreach($group_total_contributions_per_contribution_array as $contribution_id => $amount):
        	$group_total_contributions_per_contribution_array[$contribution_id] -= $group_total_contribution_refunds_per_contribution_array[$contribution_id];
        endforeach;
        
        //Subtract contribution transfers to fines
        $group_contribution_transfers_from_contribution_to_fine_category_per_contribution_array = $this->statements_m->get_group_contribution_transfers_from_contribution_to_fine_category_per_contribution_array($this->group->id,$to,$from,$contribution_id_list);
        foreach($group_total_contributions_per_contribution_array as $contribution_id => $amount):
        		$group_total_contributions_per_contribution_array[$contribution_id] -= $group_contribution_transfers_from_contribution_to_fine_category_per_contribution_array[$contribution_id];
        endforeach;
        
        //subtract ignored contribution to transfer
        $group_total_contribution_transfers_to_ignore_per_contribution_array = $this->statements_m->get_group_total_contribution_transfers_to_ignore_per_contribution_array($this->group->id,$to,$from,$contribution_id_list);
		foreach($group_total_contributions_per_contribution_array as $contribution_id => $amount):
        	$group_total_contributions_per_contribution_array[$contribution_id] -= ($group_total_contribution_transfers_to_ignore_per_contribution_array[$contribution_id]>0)?$group_total_contribution_transfers_to_ignore_per_contribution_array[$contribution_id]:0;
        endforeach;
        
        //add ignored contribution from transfer
        $group_total_contribution_transfers_from_ignore_per_contribution_array = $this->statements_m->get_group_total_contribution_transfers_from_ignore_per_contribution_array($this->group->id,$to,$from,$contribution_id_list);
		foreach($group_total_contributions_per_contribution_array as $contribution_id => $amount):
        		$group_total_contributions_per_contribution_array[$contribution_id] += ($group_total_contribution_transfers_from_ignore_per_contribution_array[$contribution_id]>0)?$group_total_contribution_transfers_from_ignore_per_contribution_array[$contribution_id]:0;
        endforeach;
        
        return $group_total_contributions_per_contribution_array;
	}



	function get_group_total_contributions_paid_per_contribution_per_year_array($group_id = 0,$start_year = 2000,$end_year = 2039){
		//Get contributions paid


		$group_total_contributions_per_contribution_per_year_array = $this->deposits_m->get_group_total_contributions_per_contribution_per_year_array($this->group->id,$start_year,$end_year);
		//Subtract contribution refunds
		$group_total_contribution_refunds_per_contribution_per_year_array = $this->withdrawals_m->get_group_total_contribution_refunds_per_contribution_per_year_array($this->group->id,$start_year,$end_year);

        foreach($group_total_contributions_per_contribution_per_year_array as $contribution_id => $amount):
        	for($i = $start_year; $i < $end_year; $i++):
	        	$group_total_contributions_per_contribution_per_year_array[$contribution_id][$i] -= $group_total_contribution_refunds_per_contribution_per_year_array[$contribution_id][$i];
	        endfor;
        endforeach;
        
        //Subtract contribution transfers to fines
        $group_contribution_transfers_from_contribution_to_fine_category_per_contribution_per_year_array = $this->statements_m->get_group_contribution_transfers_from_contribution_to_fine_category_per_contribution_per_year_array($this->group->id,$start_year,$end_year);
        foreach($group_total_contributions_per_contribution_per_year_array as $contribution_id => $amount):
        	for($i = $start_year; $i < $end_year; $i++):
        		$group_total_contributions_per_contribution_per_year_array[$contribution_id][$i] -= $group_contribution_transfers_from_contribution_to_fine_category_per_contribution_per_year_array[$contribution_id][$i];
        	endfor;	
        endforeach;
        
        //subtract ignored contribution to transfer
        $group_total_contribution_transfers_to_ignore_per_contribution_per_year_array = $this->statements_m->get_group_total_contribution_transfers_to_ignore_per_contribution_per_year_array($this->group->id,$start_year,$end_year);
		foreach($group_total_contributions_per_contribution_per_year_array as $contribution_id => $amount):
			for($i = $start_year; $i < $end_year; $i++):
        		$group_total_contributions_per_contribution_per_year_array[$contribution_id][$i] -= ($group_total_contribution_transfers_to_ignore_per_contribution_per_year_array[$contribution_id][$i]>0)?$group_total_contribution_transfers_to_ignore_per_contribution_per_year_array[$contribution_id][$i]:0;
        	endfor;
        endforeach;
        
        //add ignored contribution from transfer
        $group_total_contribution_transfers_from_ignore_per_contribution_per_year_array = $this->statements_m->get_group_total_contribution_transfers_from_ignore_per_contribution_per_year_array($this->group->id,$start_year,$end_year);
		foreach($group_total_contributions_per_contribution_per_year_array as $contribution_id => $amount):
			for($i = $start_year; $i < $end_year; $i++):
        		$group_total_contributions_per_contribution_per_year_array[$contribution_id][$i] += ($group_total_contribution_transfers_from_ignore_per_contribution_per_year_array[$contribution_id][$i]>0)?$group_total_contribution_transfers_from_ignore_per_contribution_per_year_array[$contribution_id][$i]:0;
        	endfor;
        endforeach;

        return $group_total_contributions_per_contribution_per_year_array;
	}


	function get_group_total_contributions_paid_cumulatively_per_contribution_per_year_array($group_id = 0,$start_year = 2000,$end_year = 2039){
		//Get contributions paid


		$group_total_contributions_per_contribution_per_year_array = $this->deposits_m->get_group_total_contributions_per_contribution_per_year_array($this->group->id,$start_year,$end_year);
		//Subtract contribution refunds
		$group_total_contribution_refunds_per_contribution_per_year_array = $this->withdrawals_m->get_group_total_contribution_refunds_per_contribution_per_year_array($this->group->id,$start_year,$end_year);

        foreach($group_total_contributions_per_contribution_per_year_array as $contribution_id => $amount):
        	for($i = $start_year; $i < $end_year; $i++):
	        	$group_total_contributions_per_contribution_per_year_array[$contribution_id][$i] -= $group_total_contribution_refunds_per_contribution_per_year_array[$contribution_id][$i];
	        endfor;
        endforeach;
        
        //Subtract contribution transfers to fines
        $group_contribution_transfers_from_contribution_to_fine_category_per_contribution_per_year_array = $this->statements_m->get_group_contribution_transfers_from_contribution_to_fine_category_per_contribution_per_year_array($this->group->id,$start_year,$end_year);
        foreach($group_total_contributions_per_contribution_per_year_array as $contribution_id => $amount):
        	for($i = $start_year; $i < $end_year; $i++):
        		$group_total_contributions_per_contribution_per_year_array[$contribution_id][$i] -= $group_contribution_transfers_from_contribution_to_fine_category_per_contribution_per_year_array[$contribution_id][$i];
        	endfor;	
        endforeach;
        
        //subtract ignored contribution to transfer
        $group_total_contribution_transfers_to_ignore_per_contribution_per_year_array = $this->statements_m->get_group_total_contribution_transfers_to_ignore_per_contribution_per_year_array($this->group->id,$start_year,$end_year);
		foreach($group_total_contributions_per_contribution_per_year_array as $contribution_id => $amount):
			for($i = $start_year; $i < $end_year; $i++):
        		$group_total_contributions_per_contribution_per_year_array[$contribution_id][$i] -= ($group_total_contribution_transfers_to_ignore_per_contribution_per_year_array[$contribution_id][$i]>0)?$group_total_contribution_transfers_to_ignore_per_contribution_per_year_array[$contribution_id][$i]:0;
        	endfor;
        endforeach;
        
        //add ignored contribution from transfer
        $group_total_contribution_transfers_from_ignore_per_contribution_per_year_array = $this->statements_m->get_group_total_contribution_transfers_from_ignore_per_contribution_per_year_array($this->group->id,$start_year,$end_year);
		foreach($group_total_contributions_per_contribution_per_year_array as $contribution_id => $amount):
			for($i = $start_year; $i < $end_year; $i++):
        		$group_total_contributions_per_contribution_per_year_array[$contribution_id][$i] += ($group_total_contribution_transfers_from_ignore_per_contribution_per_year_array[$contribution_id][$i]>0)?$group_total_contribution_transfers_from_ignore_per_contribution_per_year_array[$contribution_id][$i]:0;
        	endfor;
        endforeach;

        //subtract

        $group_contribution_transfers_from_contribution_to_loans_per_contribution_per_year_array = $this->statements_m->get_group_contribution_transfers_from_contribution_to_loans_per_contribution_per_year_array($this->group->id,$start_year,$end_year);
        foreach($group_total_contributions_per_contribution_per_year_array as $contribution_id => $amount):
        	for($i = $start_year; $i < $end_year; $i++):
        		$group_total_contributions_per_contribution_per_year_array[$contribution_id][$i] -= $group_contribution_transfers_from_contribution_to_loans_per_contribution_per_year_array[$contribution_id][$i];
        	endfor;	
        endforeach;

		//subtract from contribution

        $group_contribution_transfers_from_contribution_to_contribution_per_year_array = $this->statements_m->get_group_contribution_transfers_from_contribution_to_contribution_per_year_array($this->group->id,$start_year,$end_year);
        foreach($group_total_contributions_per_contribution_per_year_array as $contribution_id => $amount):
        	for($i = $start_year; $i < $end_year; $i++):
        		$group_total_contributions_per_contribution_per_year_array[$contribution_id][$i] -= $group_contribution_transfers_from_contribution_to_contribution_per_year_array[$contribution_id][$i];
        	endfor;	
        endforeach;


        foreach($group_total_contributions_per_contribution_per_year_array as $contribution_id => $amount):
        	for($i = $start_year; $i < $end_year; $i++):
				if(isset($group_total_contributions_per_contribution_per_year_array[$contribution_id][($i - 1)])){
        			$group_total_contributions_per_contribution_per_year_array[$contribution_id][$i] += $group_total_contributions_per_contribution_per_year_array[$contribution_id][($i - 1)];
        		}
        	endfor;
		endforeach;




        return $group_total_contributions_per_contribution_per_year_array;
	}




	function get_group_total_contributions_paid_per_member_array($group_id = 0,$group_member_options = array()){
		//Get contributions paid
		$total_contributions_per_member_array = $this->deposits_m->get_group_total_contributions_per_member_array($group_id,'','',$group_member_options);
		//Subtract contribution refunds
		//print_r($total_contributions_per_member_array);
        //die;
		$total_contribution_refunds_per_member_array = $this->withdrawals_m->get_group_total_contribution_refunds_per_member_array($group_id,'','','',$group_member_options);
        foreach($total_contribution_refunds_per_member_array as $member_id => $amount):
        	$total_contributions_per_member_array[$member_id] -= $total_contribution_refunds_per_member_array[$member_id];
        endforeach;   
        //Subtract contribution transfers from respective contributions
        $total_contribution_transfers_from_per_member_array = $this->statements_m->get_group_total_contribution_transfers_from_per_member_array($group_id,0,0,$group_member_options);
        foreach($total_contribution_transfers_from_per_member_array as $member_id => $amount):
        	$total_contributions_per_member_array[$member_id] -= $total_contribution_transfers_from_per_member_array[$member_id];
        endforeach;
        //Add contribution transfers to respective contributions 
        $total_contribution_transfers_to_per_member_array = $this->statements_m->get_group_total_contribution_transfers_to_per_member_array($group_id,0,0,$group_member_options);
		foreach($total_contribution_transfers_to_per_member_array as $member_id => $amount):
        	$total_contributions_per_member_array[$member_id] += $total_contribution_transfers_to_per_member_array[$member_id];
        endforeach;
        //Subtract contribution transfers to fines
        $total_contribution_transfers_from_contribution_to_fine_category_per_member_array = $this->statements_m->get_group_contribution_transfers_from_contribution_to_fine_category_per_member_array($group_id,0,0,$group_member_options);
        foreach($total_contribution_transfers_from_contribution_to_fine_category_per_member_array as $member_id => $amount):
        	$total_contributions_per_member_array[$member_id] -= $total_contribution_transfers_from_contribution_to_fine_category_per_member_array[$member_id];
        endforeach;

        
        /**
	        //subtract ignored contribution to transfer
	        $total_contribution_transfers_to_ignore_per_member_array = $this->statements_m->get_group_member_total_contribution_transfers_to_ignore_per_contribution_array();
			foreach($member_total_contributions_per_contribution_array as $member_id => $contribution):
	        	foreach($contribution as $contribution_id => $amount):
	        		$member_total_contributions_per_contribution_array[$member_id][$contribution_id] -= ($member_total_contribution_transfers_to_ignore_per_contribution_array[$member_id][$contribution_id]>0)?$member_total_contribution_transfers_to_ignore_per_contribution_array[$member_id][$contribution_id]:0;
	        	endforeach;
	        endforeach;

	        //add ignored contribution from transfer
	        $member_total_contribution_transfers_from_ignore_per_contribution_array = $this->statements_m->get_group_member_total_contribution_transfers_from_ignore_per_contribution_array();
			foreach($member_total_contributions_per_contribution_array as $member_id => $contribution):
	        	foreach($contribution as $contribution_id => $amount):
	        		$member_total_contributions_per_contribution_array[$member_id][$contribution_id] += ($member_total_contribution_transfers_from_ignore_per_contribution_array[$member_id][$contribution_id]>0)?$member_total_contribution_transfers_from_ignore_per_contribution_array[$member_id][$contribution_id]:0;
	        	endforeach;
	        endforeach;
        **/
        return $total_contributions_per_member_array;
	}

	function get_group_total_contribution_balances_per_member_array($group_id=0,$ignore_contribution_transfers=FALSE,$group_member_options=array()){
		if($group_id){

		}else{
			$group_id = $this->group->id;
		}
		$contribution_ids_list = "0";
		if($arrears_contribution_ids_list = $this->contributions_m->get_group_arrears_contribution_id_list($group_id)){
			$contribution_ids_list = $arrears_contribution_ids_list;
		}
		if(isset($this->group->disable_ignore_contribution_transfers)){
			$ignore_contribution_transfers = $ignore_contribution_transfers?:($this->group->disable_ignore_contribution_transfers?FALSE:TRUE);
		}else{
			$ignore_contribution_transfers = $ignore_contribution_transfers?:TRUE;
		}
		$total_contribution_balances_per_member_array = $this->statements_m->get_group_total_contribution_balances_per_member_array($group_id,0,0,$contribution_ids_list,$ignore_contribution_transfers,$group_member_options);
		
		//add arears from disabled arrears contribution.
		/**
		$member_total_contribution_transfers_to_ignore_array = $this->statements_m->get_group_member_total_contribution_transfers_to_ignore_array();
		foreach($total_contribution_balances_per_member_array as $member_id => $amount):
        		$total_contribution_balances_per_member_array[$member_id] -= ($member_total_contribution_transfers_to_ignore_array[$member_id]>0)?$member_total_contribution_transfers_to_ignore_array[$member_id]:0;
        endforeach;
        **/

    
		return $total_contribution_balances_per_member_array;
	}

	function get_group_total_contribution_balance(){
		$total_contribution_balances_per_member_array = $this->get_group_total_contribution_balances_per_member_array($this->group->id,FALSE,$this->group_member_options);
		$total_contribution_balance = 0;
		foreach($total_contribution_balances_per_member_array as $member_balance):
			if($member_balance>0){
				$total_contribution_balance += $member_balance;
			}
		endforeach;
		return $total_contribution_balance;
	}

	function get_group_total_contribution_overpayment(){
		$total_contribution_balances_per_member_array = $this->get_group_total_contribution_balances_per_member_array($this->group->id,FALSE,$this->group_member_options);
		$total_contribution_overpayment = 0;
		foreach($total_contribution_balances_per_member_array as $member_balance):
			if($member_balance<0){
				$total_contribution_overpayment += abs($member_balance);
			}
		endforeach;
		return $total_contribution_overpayment;

	}

	function get_group_total_fines_paid_per_member_array($member_id = 0,$group_id = 0,$group_member_options =array()){
		if($group_id){

		}else{
			$group_id = $this->group->id;
		}
        $group_member_fine_totals = $this->deposits_m->get_group_total_fines_per_member_array($group_id,0,0,$member_id,$group_member_options);
        //Add contribution transfers to fines
        $member_total_contribution_transfers_to_fines_array = $this->statements_m->get_group_member_total_contribution_transfers_to_fines_per_member_array($group_id,0,0,$member_id,$group_member_options);
       
        foreach($member_total_contribution_transfers_to_fines_array as $member_id => $amount):
        	$group_member_fine_totals[$member_id] += $amount;
        endforeach;
		//We shall add new functions as the need arises to correct this figure if the need arises
        return $group_member_fine_totals;
	}

	function get_group_total_fine_overpayments($group_id = 0){
		if($group_id){

		}else{
			$group_id = $this->group->id;
		}
		$group_total_fines_paid = $this->deposits_m->get_group_total_fines($group_id);
		$group_total_contribution_transfers = $this->statements_m->get_group_total_contribution_transfers_to_fines($group_id);
		$group_total_fines_payable = $this->invoices_m->get_group_total_fine_invoices_amount_payable();

		return $group_total_fines_payable - ($group_total_fines_paid + $group_total_contribution_transfers);

	}

	function get_group_total_contribution_fines_paid_per_member_array($member_id = 0){
        $group_member_contribution_fine_totals = $this->deposits_m->get_group_member_total_contribution_fines_per_contribution_array($this->group->id,0,0,$member_id);
        //Add contribution transfers to fines
        $member_total_contribution_transfers_to_contribution_fines_array = $this->statements_m->get_group_member_total_contribution_transfers_to_contribution_fines_per_contribution_array($this->group->id,0,0,$member_id);
        foreach($member_total_contribution_transfers_to_contribution_fines_array as $member_id => $contribution):
        	foreach($contribution as $contribution_id => $amount):
        		$group_member_contribution_fine_totals[$member_id]
        		[$contribution_id] += $amount;
        	endforeach;
        endforeach;
		//We shall add new functions as the need arises to correct this figure if the need arises
        return $group_member_contribution_fine_totals;
	}

	function get_group_total_fines_balances_per_member_array($group_id=0,$group_member_options=array()){
        $group_member_fine_balance_totals = $this->statements_m->get_group_member_total_fine_balances_array($group_id,$group_member_options);
		//We shall add new functions as the need arises to correct this figure if the need arises
		return $group_member_fine_balance_totals;
	}
 	
 
	function get_group_total_fine_balance(){
		$group_member_fine_balance_totals = $this->get_group_total_fines_balances_per_member_array();
		$total_fine_balance = 0;
		foreach($group_member_fine_balance_totals as $fine_balance):
			if($fine_balance>0){
				$total_fine_balance += $fine_balance;
			}
		endforeach;
		return $total_fine_balance;
	}

	function get_group_member_total_fines_paid_per_fine_category_array($member_id = 0){
        $group_member_fine_totals = $this->deposits_m->get_group_member_total_fines_per_fine_category_array($this->group->id,0,0,$member_id);
        //Add contribution transfers to fines
        $member_total_contribution_transfers_to_fines_array = $this->statements_m->get_group_member_total_contribution_transfers_to_fines_per_fine_category_array($this->group->id,0,0,$member_id);
        foreach($member_total_contribution_transfers_to_fines_per_fine_category_array as $member_id => $fi):
        	$group_member_fine_totals[$member_id] += $amount;
        endforeach;
		//We shall add new functions as the need arises to correct this figure if the need arises
        return $group_member_fine_totals;
	}

	function get_group_member_total_contributions($member_id = 0,$contribution_options = array()){

		if(empty($contribution_options)){
			$contribution_id_list = "";
		}else{
			$count = 1;
			foreach($contribution_options as $contribution_id => $name):
				if($count == 1){
					$contribution_id_list = $contribution_id;
				}else{
					$contribution_id_list .= ",".$contribution_id;
				}
				$count++;
			endforeach;
			//$contribution_id_list = implode(',',array_flip($contribution_options));
		}
		//Get contributions paid
		$total_member_contributions = $this->deposits_m->get_group_member_total_contributions($member_id,'',$contribution_id_list);
		//echo $total_member_contributions;
		//die;
		//Subtract contribution refunds
        $total_member_contribution_refunds = $this->withdrawals_m->get_group_member_total_contribution_refunds($member_id);
		$total_member_contributions -= $total_member_contribution_refunds;
        //Subtract contribution transfers from respective contributions
        $total_member_contribution_transfers_from = $this->statements_m->get_group_member_total_contribution_transfers_from($this->group->id,$member_id);
        $total_member_contributions -= $total_member_contribution_transfers_from;
        //Add contribution transfers to respective contributions 
        $total_member_contribution_transfers_to = $this->statements_m->get_group_member_total_contribution_transfers_to($member_id);
        $total_member_contributions += $total_member_contribution_transfers_to;
        //Subtract contribution transfers to fines
        $total_member_contribution_transfers_from_contribution_to_fine_category = $this->statements_m->get_group_member_total_contribution_transfers_from_contribution_to_fine_category($member_id);
        $total_member_contributions -= $total_member_contribution_transfers_from_contribution_to_fine_category;    
       
        /**
	        //subtract ignored contribution to transfer
	        $member_total_contribution_transfers_to_ignore_per_contribution_array = $this->statements_m->get_group_member_total_contribution_transfers_to_ignore_per_contribution_array();
			foreach($member_total_contributions_per_contribution_array as $member_id => $contribution):
	        	foreach($contribution as $contribution_id => $amount):
	        		$member_total_contributions_per_contribution_array[$member_id][$contribution_id] -= ($member_total_contribution_transfers_to_ignore_per_contribution_array[$member_id][$contribution_id]>0)?$member_total_contribution_transfers_to_ignore_per_contribution_array[$member_id][$contribution_id]:0;
	        	endforeach;
	        endforeach;

	        //add ignored contribution from transfer
	        $member_total_contribution_transfers_from_ignore_per_contribution_array = $this->statements_m->get_group_member_total_contribution_transfers_from_ignore_per_contribution_array();
			foreach($member_total_contributions_per_contribution_array as $member_id => $contribution):
	        	foreach($contribution as $contribution_id => $amount):
	        		$member_total_contributions_per_contribution_array[$member_id][$contribution_id] += ($member_total_contribution_transfers_from_ignore_per_contribution_array[$member_id][$contribution_id]>0)?$member_total_contribution_transfers_from_ignore_per_contribution_array[$member_id][$contribution_id]:0;
	        	endforeach;
	        endforeach;
	    **/

        return $total_member_contributions;
	}

	function get_group_total_contributions($group_id = 0,$from = 0,$to = 0,$contribution_options = array()){
		if(empty($contribution_options)){
			$contribution_id_list = "";
		}else{
			
			$count = 1;
			foreach($contribution_options as $contribution_id => $name):
				if($count == 1){
					$contribution_id_list = $contribution_id;
				}else{
					$contribution_id_list .= ",".$contribution_id;
				}
				$count++;
			endforeach;
		}
		//Get contributions paid
		$total_group_contributions = $this->deposits_m->get_group_total_contributions($this->group->id,$from,$to,$contribution_id_list);
		//Subtract contribution refunds
        $total_group_contribution_refunds = $this->withdrawals_m->get_group_total_contribution_refunds($this->group->id,$from,$to,$contribution_id_list);
		$total_group_contributions -= $total_group_contribution_refunds;
        //Subtract contribution transfers from respective contributions
        $total_group_contribution_transfers_from = $this->statements_m->get_group_total_contribution_transfers_from($this->group->id,$from,$to,$contribution_id_list);
        $total_group_contributions -= $total_group_contribution_transfers_from;
        //Add contribution transfers to respective contributions 
        $total_group_contribution_transfers_to = $this->statements_m->get_group_total_contribution_transfers_to($this->group->id,$from,$to,$contribution_id_list);
        $total_group_contributions += $total_group_contribution_transfers_to;
        //Subtract contribution transfers to fines
        $total_group_contribution_transfers_from_contribution_to_fine_category = $this->statements_m->get_group_total_contribution_transfers_from_contribution_to_fine_category($this->group->id,$from,$to,$contribution_id_list);
        $total_group_contributions -= $total_group_contribution_transfers_from_contribution_to_fine_category;    
       
        /**
	        //subtract ignored contribution to transfer
	        $member_total_contribution_transfers_to_ignore_per_contribution_array = $this->statements_m->get_group_member_total_contribution_transfers_to_ignore_per_contribution_array();
			foreach($member_total_contributions_per_contribution_array as $member_id => $contribution):
	        	foreach($contribution as $contribution_id => $amount):
	        		$member_total_contributions_per_contribution_array[$member_id][$contribution_id] -= ($member_total_contribution_transfers_to_ignore_per_contribution_array[$member_id][$contribution_id]>0)?$member_total_contribution_transfers_to_ignore_per_contribution_array[$member_id][$contribution_id]:0;
	        	endforeach;
	        endforeach;

	        //add ignored contribution from transfer
	        $member_total_contribution_transfers_from_ignore_per_contribution_array = $this->statements_m->get_group_member_total_contribution_transfers_from_ignore_per_contribution_array();
			foreach($member_total_contributions_per_contribution_array as $member_id => $contribution):
	        	foreach($contribution as $contribution_id => $amount):
	        		$member_total_contributions_per_contribution_array[$member_id][$contribution_id] += ($member_total_contribution_transfers_from_ignore_per_contribution_array[$member_id][$contribution_id]>0)?$member_total_contribution_transfers_from_ignore_per_contribution_array[$member_id][$contribution_id]:0;
	        	endforeach;
	        endforeach;
	    **/

        return $total_group_contributions;
	}

	function get_group_total_contributions_per_year_array($group_id = 0,$from = 0,$to = 0,$contribution_options = array()){
		if(empty($contribution_options)){
			$contribution_id_list = "";
		}else{
			
			$count = 1;
			foreach($contribution_options as $contribution_id => $name):
				if($count == 1){
					$contribution_id_list = $contribution_id;
				}else{
					$contribution_id_list .= ",".$contribution_id;
				}
				$count++;
			endforeach;
		}
		//Get contributions paid
		$total_group_contributions_per_year = $this->deposits_m->get_group_total_contributions_per_year($this->group->id,$from,$to,$contribution_id_list);
		//Subtract contribution refunds
        $total_group_contribution_refunds_per_year = $this->withdrawals_m->get_group_total_contribution_refunds_per_year($this->group->id,$from,$to,$contribution_id_list);
        //Subtract contribution transfers from respective contributions
        $total_group_contribution_transfers_from_per_year = $this->statements_m->get_group_total_contribution_transfers_from_per_year($this->group->id,$from,$to,$contribution_id_list);
        //Add contribution transfers to respective contributions 
        $total_group_contribution_transfers_to_per_year = $this->statements_m->get_group_total_contribution_transfers_to_per_year($this->group->id,$from,$to,$contribution_id_list);
        //Subtract contribution transfers to fines
        $total_group_contribution_transfers_from_contribution_to_fine_category_per_year = $this->statements_m->get_group_total_contribution_transfers_from_contribution_to_fine_category_per_year($this->group->id,$from,$to,$contribution_id_list);

        //subtract
        $total_group_contribution_transfers_to_loans_per_year = $this->statements_m->get_group_total_contribution_transfers_to_loan_per_year($this->group->id,$from,$to,$contribution_id_list);

        // print_r($total_group_contribution_transfers_to_loans_per_year);
        // die;
       
       	$arr = array();

       	foreach($total_group_contributions_per_year as $row):
			
			$arr[$row->year] = 0;
			
		endforeach;

       	foreach($total_group_contribution_refunds_per_year as $row):
			
			$arr[$row->year] = 0;
			
		endforeach;
       
       	foreach($total_group_contribution_transfers_from_per_year as $row):
			
			$arr[$row->year] = 0;
			
		endforeach;

       	foreach($total_group_contribution_transfers_to_per_year as $row):
			
			$arr[$row->year] = 0;
			
		endforeach;

       	foreach($total_group_contribution_transfers_from_contribution_to_fine_category_per_year as $row):
			
			$arr[$row->year] = 0;
			
		endforeach;

       	foreach($total_group_contribution_transfers_to_loans_per_year as $row):
			
			$arr[$row->year] = 0;
			
		endforeach;

		//here

		foreach($total_group_contributions_per_year as $row):
			
			$arr[$row->year] += $row->amount;
			
		endforeach;


       	foreach($total_group_contribution_refunds_per_year as $row):
			
			$arr[$row->year] -= $row->amount;
			
		endforeach;


       	foreach($total_group_contribution_transfers_from_per_year as $row):
			
			$arr[$row->year] -= $row->amount;
			
		endforeach;


       	foreach($total_group_contribution_transfers_to_per_year as $row):
			
			$arr[$row->year] += $row->amount;
			
		endforeach;

       	foreach($total_group_contribution_transfers_from_contribution_to_fine_category_per_year as $row):
			
			$arr[$row->year] -= $row->amount;
			
		endforeach;

       	foreach($total_group_contribution_transfers_to_loans_per_year as $row):
			
			$arr[$row->year] -= $row->amount;
			
		endforeach;

		foreach($arr as $key => $value):
			if(isset($arr[($key - 1)])){
				$arr[$key] += $arr[($key - 1)];
			}
		endforeach;

        return $arr;
	}

	function get_group_total_fine_payments($group_id=0,$from=0,$to=0){
		$paid_fines = $this->deposits_m->get_group_total_fine_payments($group_id,$from,$to);
		$transfered_fines = $this->statements_m->get_group_total_fines($group_id,$from,$to);
		return $paid_fines+$transfered_fines;
	}

	function get_group_member_total_fine_payments($group_id=0,$from=0,$to=0,$member_id = 0){
		$paid_fines = $this->deposits_m->get_group_total_fine_payments($group_id,$from,$to,$member_id);
		$transfered_fines = $this->statements_m->get_group_total_fines($group_id,$from,$to,$member_id);
		return $paid_fines+$transfered_fines;
	}

	function get_group_total_loan_balance($group_id=0,$from=0,$to=0){
		return $this->loan_invoices_m->get_group_total_loan_balance($group_id,$from,$to);
	}

	function get_group_total_loan_principal_balance($group_id=0,$from=0,$to=0){
		return $this->loan_invoices_m->get_group_total_loan_principal_balance($group_id,$from,$to);
	}

	function get_group_total_asset_value($group_id=0,$from=0,$to=0){
		return $this->withdrawals_m->get_group_asset_purchase_total_amount($group_id,$from,$to);
	}

	function get_group_total_bank_loan_balance($group_id=0,$from=0,$to=0){
		return $this->bank_loans_m->get_group_total_bank_loan_balance($group_id,$from,$to);
	}

	function get_group_total_fines_per_year_array($group_id = 0,$from = 0,$to = 0){
		$group_total_fines_per_year = $this->deposits_m->get_group_total_fines_per_year($group_id);
		$group_total_contribution_transfers_to_fines_per_year = $this->statements_m->get_group_total_contribution_transfers_to_fines_per_year($group_id);

		$arr = array();

       	foreach($group_total_fines_per_year as $row):
			
			$arr[$row->year] = 0;
			
		endforeach;

       	foreach($group_total_contribution_transfers_to_fines_per_year as $row):
			
			$arr[$row->year] = 0;
			
		endforeach;

       	foreach($group_total_fines_per_year as $row):
			
			$arr[$row->year] += $row->amount;
			
		endforeach;

       	foreach($group_total_contribution_transfers_to_fines_per_year as $row):
			
			$arr[$row->year] += $row->amount;
			
		endforeach;

		return $arr;
	}

	function get_group_total_loan_interest_paid_per_year_array($group_id = 0){
		$this->_distribute_principal_and_interest_paid_per_year($group_id);
		return $this->interest_paid_per_year_array;
	}

	function get_group_total_loan_principal_paid_per_year_array($group_id = 0){
		$this->_distribute_principal_and_interest_paid_per_year($group_id);
		return $this->principal_paid_per_year_array;
	}

	function get_group_total_loan_overpayments_per_year_array($group_id = 0){
		$this->_distribute_principal_and_interest_paid_per_year($group_id);
		$arr = $this->over_payments_per_year_array;
        foreach($arr as $key => $value):
            if(isset($arr[($key - 1)])){
                $arr[$key] += $arr[($key - 1)];
            }
        endforeach;
		return $arr;
	}

	function _distribute_principal_and_interest_paid_per_year($group_id = 0,$from = 0,$to = 0){

		$this->principal_paid_per_year_array = array();
		$this->interest_paid_per_year_array = array();
		$this->over_payments_per_year_array = array();
		$this->loan_amounts_paid_per_loan_array = array();
		$this->loan_amounts_payable_per_loan_array = array();
		$this->principal_amounts_paid_per_loan_array = array();
		$this->interest_amounts_paid_per_loan_array = array();
		$this->loan_balances_per_loan_array = array();

		$loans = $this->loans_m->get_group_loans();

		$deposits_per_loan_array = $this->deposits_m->get_group_loan_repayment_deposits_per_loan_array($group_id,$from,$to);

		$contribution_transfers_to_loan_per_loan_array = $this->deposits_m->get_group_contribution_transfers_to_loan_per_loan_array($group_id,$from,$to);

		$loan_invoices_per_loan_array = $this->loan_invoices_m->get_group_loan_invoices_per_loan_array($group_id);

		$total_amount_paid = 0;
		$total_overpayments = 0;

		foreach($loans as $loan):

			$amount_to_distribute = 0;
			$amount_payable = 0;
			$amount_paid = 0;
			$loan_invoices = isset($loan_invoices_per_loan_array[$loan->id])?$loan_invoices_per_loan_array[$loan->id]:array();
			$deposits = isset($deposits_per_loan_array[$loan->id])?$deposits_per_loan_array[$loan->id]:array();
			$contribution_transfers = isset($contribution_transfers_to_loan_per_loan_array[$loan->id])?$contribution_transfers_to_loan_per_loan_array[$loan->id]:array();
			if($contribution_transfers){
				foreach($contribution_transfers as $contribution_transfer):
					$deposit = new stdClass();
					$deposit->deposit_date = $contribution_transfer->transfer_date;
					$deposit->loan_id = $contribution_transfer->loan_to_id;
					$deposit->amount = $contribution_transfer->amount;
					$deposits[] = $deposit;
				endforeach;
			}

			foreach($loan_invoices as $loan_invoice):
				$loan_invoice->amount_paid = 0;
				$loan_invoice->is_fully_paid = 0;
				$loan_invoice->principle_paid = 0;
				$loan_invoice->interest_paid = 0;
				if($loan_invoice->type == 2 || $loan_invoice->type == 3){
					$loan_invoice->interest_payable = 0;
					$loan_invoice->principle_amount_payable = 0;
				}
				$amount_payable += $loan_invoice->amount_payable;
				if(isset($this->loan_balances_per_loan_array[$loan_invoice->loan_id])){
					$this->loan_balances_per_loan_array[$loan_invoice->loan_id] += $loan_invoice->amount_payable;
				}else{
					$this->loan_balances_per_loan_array[$loan_invoice->loan_id] = $loan_invoice->amount_payable;
				}
				if(isset($this->loan_amounts_payable_per_loan_array[$loan_invoice->loan_id])){
					$this->loan_amounts_payable_per_loan_array[$loan_invoice->loan_id] += $loan_invoice->amount_payable;
				}else{
					$this->loan_amounts_payable_per_loan_array[$loan_invoice->loan_id] = $loan_invoice->amount_payable;
				}
			endforeach;

			$count = 1;

			foreach($deposits as $deposit):

				if(isset($this->loan_amounts_paid_per_loan_array[$deposit->loan_id])){
					$this->loan_amounts_paid_per_loan_array[$deposit->loan_id] += $deposit->amount;
				}else{
					$this->loan_amounts_paid_per_loan_array[$deposit->loan_id] = $deposit->amount;
				}

				$amount_paid += $deposit->amount;

				$amount_to_distribute = $deposit->amount;

				$invoice_count = 1;

				foreach($loan_invoices as $key => $loan_invoice):
					//echo "Amount to distribute: ".number_to_currency($amount_to_distribute)."<br/>";
					if($amount_to_distribute > 0){

						if($loan_invoice->is_fully_paid){

							//do nothing here

							//echo "#".$loan_invoice->id." Ignored as it is fully paid. <br/>";

						}else{
		 
							$loan_invoice_balance = $loan_invoice->amount_payable - $loan_invoice->amount_paid;

							//echo "#".$loan_invoice->id.": loan invoice balance: ".number_to_currency($loan_invoice_balance)."<br/>";

							if($amount_to_distribute >= $loan_invoice_balance){

								$loan_invoice_principal_balance = $loan_invoice->principle_amount_payable - $loan_invoice->principle_paid;

								$loan_invoice_interest_balance = $loan_invoice->interest_amount_payable - $loan_invoice->interest_paid;

								if($loan_invoice_principal_balance == 0){

									if($loan_invoice_interest_balance == 0){

									}else{
										
										if(isset($this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)])){
											$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] += round($loan_invoice_interest_balance,2);
										}else{
											$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] = round($loan_invoice_interest_balance,2);
										}

										if(isset($this->interest_amounts_paid_per_loan_array[$deposit->loan_id])){
											$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] += round($loan_invoice_interest_balance,2);
										}else{
											$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] = round($loan_invoice_interest_balance,2);
										}

										if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
											$this->loan_balances_per_loan_array[$deposit->loan_id] -=  round($loan_invoice_interest_balance,2);
										}else{
											$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($loan_invoice_interest_balance,2);
										}

										$amount_to_distribute -= $loan_invoice_interest_balance;								

									}

								}else{

									if(isset($this->principal_paid_per_year_array[date('Y',$deposit->deposit_date)])){
										$this->principal_paid_per_year_array[date('Y',$deposit->deposit_date)] += round($loan_invoice_principal_balance,2);
									}else{
										$this->principal_paid_per_year_array[date('Y',$deposit->deposit_date)] = round($loan_invoice_principal_balance,2);
									}

									if(isset($this->principal_amounts_paid_per_loan_array[$deposit->loan_id])){
										$this->principal_amounts_paid_per_loan_array[$deposit->loan_id] += round($loan_invoice_principal_balance,2);
									}else{
										$this->principal_amounts_paid_per_loan_array[$deposit->loan_id] = round($loan_invoice_principal_balance,2);
									}


									if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
										$this->loan_balances_per_loan_array[$deposit->loan_id] -=  round($loan_invoice_principal_balance,2);
									}else{
										$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($loan_invoice_principal_balance,2);
									}


									$amount_to_distribute -= $loan_invoice_principal_balance;

									if(isset($this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)])){
										$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] += round($loan_invoice_interest_balance,2);
									}else{
										$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] = round($loan_invoice_interest_balance,2);
									}

									if(isset($this->interest_amounts_paid_per_loan_array[$deposit->loan_id])){
										$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] += round($loan_invoice_interest_balance,2);
									}else{
										$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] = round($loan_invoice_interest_balance,2);
									}

									if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
										$this->loan_balances_per_loan_array[$deposit->loan_id] -=  round($loan_invoice_interest_balance,2);
									}else{
										$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($loan_invoice_interest_balance,2);
									}

									$amount_to_distribute -= $loan_invoice_interest_balance;

								}

								$loan_invoice->amount_paid = $loan_invoice->amount_payable;
								$loan_invoice->is_fully_paid = 1;
								$loan_invoice->principle_paid = $loan_invoice->principle_amount_payable;
								$loan_invoice->interest_paid = $loan_invoice->interest_amount_payable;

							}else{

								$loan_invoice_principal_balance = $loan_invoice->principle_amount_payable - $loan_invoice->principle_paid;

								if($loan_invoice_principal_balance > 0){

									if($amount_to_distribute >= $loan_invoice_principal_balance){

										
										$amount_to_distribute -= $loan_invoice_principal_balance;

										if(isset($this->principal_paid_per_year_array[date('Y',$deposit->deposit_date)])){
											$this->principal_paid_per_year_array[date('Y',$deposit->deposit_date)] += round($loan_invoice_principal_balance,2);
										}else{
											$this->principal_paid_per_year_array[date('Y',$deposit->deposit_date)] = round($loan_invoice_principal_balance,2);
										}

										if(isset($this->principal_amounts_paid_per_loan_array[$deposit->loan_id])){
											$this->principal_amounts_paid_per_loan_array[$deposit->loan_id] += round($loan_invoice_principal_balance,2);
										}else{
											$this->principal_amounts_paid_per_loan_array[$deposit->loan_id] = round($loan_invoice_principal_balance,2);
										}

										if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
											$this->loan_balances_per_loan_array[$deposit->loan_id] -=  round($loan_invoice_principal_balance,2);
										}else{
											$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($loan_invoice_principal_balance,2);
										}
											
										$loan_invoice->principle_paid += $loan_invoice_principal_balance;

										$loan_invoice->amount_paid += $loan_invoice_principal_balance;

										if($amount_to_distribute > 0){

											$loan_invoice_interest_balance = $loan_invoice->interest_amount_payable - $loan_invoice->interest_paid;

											if($amount_to_distribute >= $loan_invoice_interest_balance){

												$amount_to_distribute -= $loan_invoice_interest_balance;

												if(isset($this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)])){
													$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] += round($loan_invoice_interest_balance,2);
												}else{
													$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] = round($loan_invoice_interest_balance,2);
												}

												if(isset($this->interest_amounts_paid_per_loan_array[$deposit->loan_id])){
													$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] += round($loan_invoice_interest_balance,2);
												}else{
													$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] = round($loan_invoice_interest_balance,2);
												}


												if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
													$this->loan_balances_per_loan_array[$deposit->loan_id] -= round($loan_invoice_interest_balance,2);
												}else{
													$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($loan_invoice_interest_balance,2);
												}
											

												$loan_invoice->interest_paid += $loan_invoice->interest_amount_payable;
												
												$loan_invoice->amount_paid += $loan_invoice->interest_amount_payable;

												$loan_invoice->is_fully_paid = 1;

											}else{

												if(isset($this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)])){
													$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] += round($amount_to_distribute,2);
												}else{
													$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] = round($amount_to_distribute,2);
												}


												if(isset($this->interest_amounts_paid_per_loan_array[$deposit->loan_id])){
													$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] += round($amount_to_distribute,2);
												}else{
													$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] = round($amount_to_distribute,2);
												}


												if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
													$this->loan_balances_per_loan_array[$deposit->loan_id] -= round($amount_to_distribute,2);
												}else{
													$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($amount_to_distribute,2);
												}


												$loan_invoice->interest_paid += $amount_to_distribute;
												
												$loan_invoice->amount_paid += $amount_to_distribute;

												$amount_to_distribute = 0;

												break;

											}

										}

									}else{

										if(isset($this->principal_paid_per_year_array[date('Y',$deposit->deposit_date)])){
											$this->principal_paid_per_year_array[date('Y',$deposit->deposit_date)] += round($amount_to_distribute,2);
										}else{
											$this->principal_paid_per_year_array[date('Y',$deposit->deposit_date)] = round($amount_to_distribute,2);
										}

										if(isset($this->principal_amounts_paid_per_loan_array[$deposit->loan_id])){
											$this->principal_amounts_paid_per_loan_array[$deposit->loan_id] += round($amount_to_distribute,2);
										}else{
											$this->principal_amounts_paid_per_loan_array[$deposit->loan_id] = round($amount_to_distribute,2);
										}

	
										if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
											$this->loan_balances_per_loan_array[$deposit->loan_id] -= round($amount_to_distribute,2);
										}else{
											$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($amount_to_distribute,2);
										}

										$loan_invoice->principle_paid += $amount_to_distribute; 

										$loan_invoice->amount_paid += $amount_to_distribute; 

										$amount_to_distribute = 0;

									}

								}else{

									$loan_invoice_interest_balance = $loan_invoice->interest_amount_payable - $loan_invoice->interest_paid;

									if($amount_to_distribute >= $loan_invoice_interest_balance){

										if(isset($this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)])){
											$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] += round($loan_invoice_interest_balance,2);
										}else{
											$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] = round($loan_invoice_interest_balance,2);
										}


										if(isset($this->interest_amounts_paid_per_loan_array[$deposit->loan_id])){
											$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] += round($loan_invoice_interest_balance,2);
										}else{
											$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] = round($loan_invoice_interest_balance,2);
										}


										
										if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
											$this->loan_balances_per_loan_array[$deposit->loan_id] -= round($loan_invoice_interest_balance,2);
										}else{
											$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($loan_invoice_interest_balance,2);
										}


										$loan_invoice->interest_paid = $loan_invoice->interest_amount_payable;
										
										$loan_invoice->amount_paid += $loan_invoice->interest_amount_payable;

										$loan_invoice->is_fully_paid = 1;

										$amount_to_distribute -= $loan_invoice_interest_balance;

									}else{

										if(isset($this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)])){
											$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] += round($amount_to_distribute,2);
										}else{
											$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] = round($amount_to_distribute,2);
										}


										if(isset($this->interest_amounts_paid_per_loan_array[$deposit->loan_id])){
											$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] += round($amount_to_distribute,2);
										}else{
											$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] = round($amount_to_distribute,2);
										}


										
										if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
											$this->loan_balances_per_loan_array[$deposit->loan_id] -= round($amount_to_distribute,2);
										}else{
											$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($amount_to_distribute,2);
										}

										$loan_invoice->interest_paid += $amount_to_distribute;
										
										$loan_invoice->amount_paid += $amount_to_distribute;

										$amount_to_distribute = 0;

										break;

									}
								}

							}
						}

					}else{
						break;
					}

					$invoice_count++;						

					$loan_invoices[$key] = $loan_invoice;

				endforeach;
				if($amount_to_distribute > 0){
					if(isset($this->over_payments_per_year_array[date('Y',$deposit->deposit_date)])){
						$this->over_payments_per_year_array[date('Y',$deposit->deposit_date)] += round($amount_to_distribute,2);
					}else{
						$this->over_payments_per_year_array[date('Y',$deposit->deposit_date)] = round($amount_to_distribute,2);
					}

					if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
						$this->loan_balances_per_loan_array[$deposit->loan_id] -= round($amount_to_distribute,2);
					}else{
						$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($amount_to_distribute,2);
					}
					
					$amount_to_distribute = 0;

				}
				// if($amount_to_distribute == 0){

				// }else{
				// 	echo $amount_to_distribute.'<br/>';
				// }

				$total_amount_paid += $deposit->amount;

				$count++;

			endforeach;

			$balance = $amount_payable - $amount_paid;
			if($balance < 0){
				$total_overpayments += $balance;
			}

		endforeach;
	}

	function get_group_loan_amounts_paid_per_loan_array($group_id = 0,$from = 0,$to = 0){
		$this->_distribute_principal_and_interest_paid_per_year($group_id,$from,$to);
		return $this->loan_amounts_paid_per_loan_array;
	}

	function get_group_loan_amounts_payable_per_loan_array($group_id = 0,$from = 0,$to = 0){
		$this->_distribute_principal_and_interest_paid_per_year($group_id,$from,$to);
		return $this->loan_amounts_payable_per_loan_array;
	}

	function get_group_principal_amounts_paid_per_loan_array($group_id = 0,$from = 0,$to = 0){
		$this->_distribute_principal_and_interest_paid_per_year($group_id,$from,$to);
		return $this->principal_amounts_paid_per_loan_array;
	}

	function get_group_interest_amounts_paid_per_loan_array($group_id = 0,$from = 0,$to = 0){
		$this->_distribute_principal_and_interest_paid_per_year($group_id,$from,$to);
		return $this->interest_amounts_paid_per_loan_array;
	}

	function get_group_loan_balances_per_loan_array($group_id = 0,$from = 0,$to = 0){
		$this->_distribute_principal_and_interest_paid_per_year($group_id,$from,$to);
		return $this->loan_balances_per_loan_array;
	}

	function test_interest_paid_logic($group_id = 0,$loan_id = 0,$from = 0,$to = 0){

		$this->principal_paid_per_year_array = array();
		$this->interest_paid_per_year_array = array();
		$this->over_payments_per_year_array = array();
		$this->loan_amounts_paid_per_loan_array = array();
		$this->loan_amounts_payable_per_loan_array = array();
		$this->principal_amounts_paid_per_loan_array = array();
		$this->interest_amounts_paid_per_loan_array = array();
		$this->loan_balances_per_loan_array = array();

		$contribution_transfers_to_loan_per_loan_array = $this->deposits_m->get_group_contribution_transfers_to_loan_per_loan_array($group_id);
		$deposits_per_loan_array = $this->deposits_m->get_group_loan_repayment_deposits_per_loan_array($group_id);
		$loan_invoices_per_loan_array = $this->loan_invoices_m->get_group_loan_invoices_per_loan_array($group_id);
		$amount_payable = 0;
		$amount_paid = 0;
		$loan_invoices = isset($loan_invoices_per_loan_array[$loan_id])?$loan_invoices_per_loan_array[$loan_id]:array();
		$deposits = isset($deposits_per_loan_array[$loan_id])?$deposits_per_loan_array[$loan_id]:array();
		$contribution_transfers = isset($contribution_transfers_to_loan_per_loan_array[$loan_id])?$contribution_transfers_to_loan_per_loan_array[$loan_id]:array();
		if($contribution_transfers){
			foreach($contribution_transfers as $contribution_transfer):
				$deposit = new stdClass();
				$deposit->deposit_date = $contribution_transfer->transfer_date;
				$deposit->loan_id = $contribution_transfer->loan_to_id;
				$deposit->amount = $contribution_transfer->amount;
				$deposits[] = $deposit;
			endforeach;
		}

		$total_amount_paid = 0;

			foreach($loan_invoices as $loan_invoice):
				$loan_invoice->amount_paid = 0;
				$loan_invoice->is_fully_paid = 0;
				$loan_invoice->principle_paid = 0;
				$loan_invoice->interest_paid = 0;
				if($loan_invoice->type == 2 || $loan_invoice->type == 3){
					$loan_invoice->interest_payable = 0;
					$loan_invoice->principle_amount_payable = 0;
				}
				$amount_payable += $loan_invoice->amount_payable;
				if(isset($this->loan_balances_per_loan_array[$loan_invoice->loan_id])){
					$this->loan_balances_per_loan_array[$loan_invoice->loan_id] += $loan_invoice->amount_payable;
				}else{
					$this->loan_balances_per_loan_array[$loan_invoice->loan_id] = $loan_invoice->amount_payable;
				}
				if(isset($this->loan_amounts_payable_per_loan_array[$loan_invoice->loan_id])){
					$this->loan_amounts_payable_per_loan_array[$loan_invoice->loan_id] += $loan_invoice->amount_payable;
				}else{
					$this->loan_amounts_payable_per_loan_array[$loan_invoice->loan_id] = $loan_invoice->amount_payable;
				}
			endforeach;

			//print_r($this->loan_balances_per_loan_array);

			echo "Starting Balance: ".number_to_currency($this->loan_balances_per_loan_array[$loan_invoice->loan_id])."<br/>";

			$count = 1;

			foreach($deposits as $deposit):

				if(isset($this->loan_amounts_paid_per_loan_array[$deposit->loan_id])){
					$this->loan_amounts_paid_per_loan_array[$deposit->loan_id] += $deposit->amount;
				}else{
					$this->loan_amounts_paid_per_loan_array[$deposit->loan_id] = $deposit->amount;
				}

				$amount_paid += $deposit->amount;

				$amount_to_distribute = $deposit->amount;

				$invoice_count = 1;

				foreach($loan_invoices as $key => $loan_invoice):
					//echo "Amount to distribute: ".number_to_currency($amount_to_distribute)."<br/>";
					if($amount_to_distribute > 0){

						if($loan_invoice->is_fully_paid){

							//do nothing here

							//echo "#".$loan_invoice->id." Ignored as it is fully paid. <br/>";

						}else{

							$loan_invoice_balance = $loan_invoice->amount_payable - $loan_invoice->amount_paid;

							//echo "#".$loan_invoice->id.": loan invoice balance: ".number_to_currency($loan_invoice_balance)."<br/>";

							if($amount_to_distribute >= $loan_invoice_balance){

								$loan_invoice_principal_balance = $loan_invoice->principle_amount_payable - $loan_invoice->principle_paid;

								$loan_invoice_interest_balance = $loan_invoice->interest_amount_payable - $loan_invoice->interest_paid;

								if($loan_invoice_principal_balance == 0){

									if($loan_invoice_interest_balance == 0){

									}else{
										
										if(isset($this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)])){
											$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] += round($loan_invoice_interest_balance,2);
										}else{
											$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] = round($loan_invoice_interest_balance,2);
										}

										if(isset($this->interest_amounts_paid_per_loan_array[$deposit->loan_id])){
											$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] += round($loan_invoice_interest_balance,2);
										}else{
											$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] = round($loan_invoice_interest_balance,2);
										}

										if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
											$this->loan_balances_per_loan_array[$deposit->loan_id] -=  round($loan_invoice_interest_balance,2);
										}else{
											$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($loan_invoice_interest_balance,2);
										}

										$amount_to_distribute -= $loan_invoice_interest_balance;								

									}

								}else{

									if(isset($this->principal_paid_per_year_array[date('Y',$deposit->deposit_date)])){
										$this->principal_paid_per_year_array[date('Y',$deposit->deposit_date)] += round($loan_invoice_principal_balance,2);
									}else{
										$this->principal_paid_per_year_array[date('Y',$deposit->deposit_date)] = round($loan_invoice_principal_balance,2);
									}

									if(isset($this->principal_amounts_paid_per_loan_array[$deposit->loan_id])){
										$this->principal_amounts_paid_per_loan_array[$deposit->loan_id] += round($loan_invoice_principal_balance,2);
									}else{
										$this->principal_amounts_paid_per_loan_array[$deposit->loan_id] = round($loan_invoice_principal_balance,2);
									}


									if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
										$this->loan_balances_per_loan_array[$deposit->loan_id] -=  round($loan_invoice_principal_balance,2);
									}else{
										$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($loan_invoice_principal_balance,2);
									}


									$amount_to_distribute -= $loan_invoice_principal_balance;

									if(isset($this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)])){
										$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] += round($loan_invoice_interest_balance,2);
									}else{
										$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] = round($loan_invoice_interest_balance,2);
									}

									if(isset($this->interest_amounts_paid_per_loan_array[$deposit->loan_id])){
										$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] += round($loan_invoice_interest_balance,2);
									}else{
										$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] = round($loan_invoice_interest_balance,2);
									}

									if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
										$this->loan_balances_per_loan_array[$deposit->loan_id] -=  round($loan_invoice_interest_balance,2);
									}else{
										$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($loan_invoice_interest_balance,2);
									}

									$amount_to_distribute -= $loan_invoice_interest_balance;

								}

								$loan_invoice->amount_paid = $loan_invoice->amount_payable;
								$loan_invoice->is_fully_paid = 1;
								$loan_invoice->principle_paid = $loan_invoice->principle_amount_payable;
								$loan_invoice->interest_paid = $loan_invoice->interest_amount_payable;

							}else{


								$loan_invoice_principal_balance = $loan_invoice->principle_amount_payable - $loan_invoice->principle_paid;


									

								if($loan_invoice_principal_balance > 0){



									if($amount_to_distribute >= $loan_invoice_principal_balance){

										
										$amount_to_distribute -= $loan_invoice_principal_balance;

										if(isset($this->principal_paid_per_year_array[date('Y',$deposit->deposit_date)])){
											$this->principal_paid_per_year_array[date('Y',$deposit->deposit_date)] += round($loan_invoice_principal_balance,2);
										}else{
											$this->principal_paid_per_year_array[date('Y',$deposit->deposit_date)] = round($loan_invoice_principal_balance,2);
										}

										if(isset($this->principal_amounts_paid_per_loan_array[$deposit->loan_id])){
											$this->principal_amounts_paid_per_loan_array[$deposit->loan_id] += round($loan_invoice_principal_balance,2);
										}else{
											$this->principal_amounts_paid_per_loan_array[$deposit->loan_id] = round($loan_invoice_principal_balance,2);
										}

										if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
											$this->loan_balances_per_loan_array[$deposit->loan_id] -=  round($loan_invoice_principal_balance,2);
										}else{
											$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($loan_invoice_principal_balance,2);
										}
											
										$loan_invoice->principle_paid += $loan_invoice_principal_balance;

										$loan_invoice->amount_paid += $loan_invoice_principal_balance;

										if($amount_to_distribute > 0){

											$loan_invoice_interest_balance = $loan_invoice->interest_amount_payable - $loan_invoice->interest_paid;

											if($amount_to_distribute >= $loan_invoice_interest_balance){

												$amount_to_distribute -= $loan_invoice_interest_balance;

												if(isset($this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)])){
													$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] += round($loan_invoice_interest_balance,2);
												}else{
													$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] = round($loan_invoice_interest_balance,2);
												}

												if(isset($this->interest_amounts_paid_per_loan_array[$deposit->loan_id])){
													$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] += round($loan_invoice_interest_balance,2);
												}else{
													$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] = round($loan_invoice_interest_balance,2);
												}


												if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
													$this->loan_balances_per_loan_array[$deposit->loan_id] -= round($loan_invoice_interest_balance,2);
												}else{
													$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($loan_invoice_interest_balance,2);
												}
											

												$loan_invoice->interest_paid += $loan_invoice->interest_amount_payable;
												
												$loan_invoice->amount_paid += $loan_invoice->interest_amount_payable;

												$loan_invoice->is_fully_paid = 1;

											}else{

												if(isset($this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)])){
													$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] += round($amount_to_distribute,2);
												}else{
													$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] = round($amount_to_distribute,2);
												}


												if(isset($this->interest_amounts_paid_per_loan_array[$deposit->loan_id])){
													$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] += round($amount_to_distribute,2);
												}else{
													$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] = round($amount_to_distribute,2);
												}


												if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
													$this->loan_balances_per_loan_array[$deposit->loan_id] -= round($amount_to_distribute,2);
												}else{
													$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($amount_to_distribute,2);
												}


												$loan_invoice->interest_paid += $amount_to_distribute;
												
												$loan_invoice->amount_paid += $amount_to_distribute;

												$amount_to_distribute = 0;

												break;

											}

										}

									}else{

										if(isset($this->principal_paid_per_year_array[date('Y',$deposit->deposit_date)])){
											$this->principal_paid_per_year_array[date('Y',$deposit->deposit_date)] += round($amount_to_distribute,2);
										}else{
											$this->principal_paid_per_year_array[date('Y',$deposit->deposit_date)] = round($amount_to_distribute,2);
										}

										if(isset($this->principal_amounts_paid_per_loan_array[$deposit->loan_id])){
											$this->principal_amounts_paid_per_loan_array[$deposit->loan_id] += round($amount_to_distribute,2);
										}else{
											$this->principal_amounts_paid_per_loan_array[$deposit->loan_id] = round($amount_to_distribute,2);
										}

	
										if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
											$this->loan_balances_per_loan_array[$deposit->loan_id] -= round($amount_to_distribute,2);
										}else{
											$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($amount_to_distribute,2);
										}

										$loan_invoice->principle_paid += $amount_to_distribute; 

										$loan_invoice->amount_paid += $amount_to_distribute; 

										$amount_to_distribute = 0;

									}

								}else{


									$loan_invoice_interest_balance = $loan_invoice->interest_amount_payable - $loan_invoice->interest_paid;

									if($amount_to_distribute >= $loan_invoice_interest_balance){

										if($count == 18){
											echo "Amount Paid: ".$deposit->amount."<br/>";
											echo "Balance: ".$this->loan_balances_per_loan_array[$deposit->loan_id];
											die();
										}


										if(isset($this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)])){
											$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] += round($loan_invoice_interest_balance,2);
										}else{
											$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] = round($loan_invoice_interest_balance,2);
										}


										if(isset($this->interest_amounts_paid_per_loan_array[$deposit->loan_id])){
											$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] += round($loan_invoice_interest_balance,2);
										}else{
											$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] = round($loan_invoice_interest_balance,2);
										}


										
										if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
											$this->loan_balances_per_loan_array[$deposit->loan_id] -= round( $loan_invoice_interest_balance,2);
										}else{
											$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($loan_invoice_interest_balance,2);
										}


										$loan_invoice->interest_paid = $loan_invoice->interest_amount_payable;
										
										$loan_invoice->amount_paid += $loan_invoice->interest_amount_payable;

										$loan_invoice->is_fully_paid = 1;

										$amount_to_distribute -= $loan_invoice_interest_balance;

									}else{

										if(isset($this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)])){
											$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] += round($amount_to_distribute,2);
										}else{
											$this->interest_paid_per_year_array[date('Y',$deposit->deposit_date)] = round($amount_to_distribute,2);
										}


										if(isset($this->interest_amounts_paid_per_loan_array[$deposit->loan_id])){
											$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] += round($amount_to_distribute,2);
										}else{
											$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] = round($amount_to_distribute,2);
										}


										
										if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
											$this->loan_balances_per_loan_array[$deposit->loan_id] -= round($amount_to_distribute,2);
										}else{
											$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($amount_to_distribute,2);
										}

										$loan_invoice->interest_paid += $amount_to_distribute;
										
										$loan_invoice->amount_paid += $amount_to_distribute;

										$amount_to_distribute = 0;

										break;

									}
								}

							}
						}

					}else{
						break;
					}

					$invoice_count++;						

					$loan_invoices[$key] = $loan_invoice;

				endforeach;

				if($amount_to_distribute > 0){
					if(isset($this->over_payments_per_year_array[date('Y',$deposit->deposit_date)])){
						$this->over_payments_per_year_array[date('Y',$deposit->deposit_date)] += round($amount_to_distribute,2);
					}else{
						$this->over_payments_per_year_array[date('Y',$deposit->deposit_date)] = round($amount_to_distribute,2);
					}
					if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
						//echo $amount_to_distribute."<br/>";
						//die("Am in");
						$this->loan_balances_per_loan_array[$deposit->loan_id] -= round($amount_to_distribute,2);
					}else{
						echo $amount_to_distribute."<br/>";

						$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($amount_to_distribute,2);
					}	

					//$amount_to_distribute = 0;
				}

				$total_amount_paid += $deposit->amount;

				echo " {$count} . Updated Balance: ".number_to_currency($this->loan_balances_per_loan_array[$deposit->loan_id])." | Amount Paid: ".number_to_currency($deposit->amount)."<br/>";

				$count++;

			endforeach;
			
			echo "Total Amount Paid: ".number_to_currency($total_amount_paid);;

			die;

			$balance = $amount_payable - $amount_paid;
			if($balance < 0){
				$total_overpayments += $balance;
			}

		echo "Principal paid: <hr/>";

		print_r($this->principal_paid_per_year_array);

		echo "<hr/>";

		echo "Interest paid: <hr/>";

		print_r($this->interest_paid_per_year_array);

		echo "<hr/>";

		echo "Overpayments: <hr/>";

		print_r($this->over_payments_per_year_array);

		echo "<hr/>";

		echo "Total amount paid: ".number_to_currency($total_amount_paid)."<br/>";

		echo "Loan balances <hr/>";

		print_r($this->loan_balances_per_loan_array);

		echo "<hr/>";
		
		echo "Loan invoices <hr/>";

		echo "
		<table>
			<thead>
				<tr>
					<th>#</th>
					<th>Invoice date</th>
					<th>Principal Payable</th>
					<th>Principal Paid</th>
					<th>Interest Payable</th>
					<th>Interest Paid</th>
					<th>Amount Payable</th>
					<th>Amount Paid</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>";
				foreach($loan_invoices as $loan_invoice):
					echo "
						<tr>
							<th>#</th>
							<th>".timestamp_to_date($loan_invoice->invoice_date)."</th>
							<th>".number_to_currency($loan_invoice->principle_amount_payable)."</th>
							<th>".number_to_currency($loan_invoice->principle_paid)."</th>
							<th>".number_to_currency($loan_invoice->interest_amount_payable)."</th>
							<th>".number_to_currency($loan_invoice->interest_paid)."</th>
							<th>".number_to_currency($loan_invoice->amount_payable)."</th>
							<th>".number_to_currency($loan_invoice->amount_paid)."</th>
							<th>".$loan_invoice->is_fully_paid."</th>
						</tr>
					";
				endforeach;
			echo "
			</tbody>
		</table>
		";

	}

	function get_group_total_contributions_per_month_array($group_id = 0,$from = 0,$to = 0,$contribution_options = array(),$months_array = array()){
		if(empty($contribution_options)){
			$contribution_id_list = "";
		}else{
			
			$count = 1;
			foreach($contribution_options as $contribution_id => $name):
				if($count == 1){
					$contribution_id_list = $contribution_id;
				}else{
					$contribution_id_list .= ",".$contribution_id;
				}
				$count++;
			endforeach;
		}
		//Get contributions paid
		$total_group_contributions_per_month = $this->deposits_m->get_group_total_contributions_per_month($this->group->id,$from,$to,$contribution_id_list);
		//Subtract contribution refunds
        $total_group_contribution_refunds_per_month = $this->withdrawals_m->get_group_total_contribution_refunds_per_month($this->group->id,$from,$to,$contribution_id_list);
        //Subtract contribution transfers from respective contributions
        $total_group_contribution_transfers_from_per_month = $this->statements_m->get_group_total_contribution_transfers_from_per_month($this->group->id,$from,$to,$contribution_id_list);
        //Add contribution transfers to respective contributions 
        $total_group_contribution_transfers_to_per_month = $this->statements_m->get_group_total_contribution_transfers_to_per_month($this->group->id,$from,$to,$contribution_id_list);

        //Subtract contribution transfers to fines
        $total_group_contribution_transfers_from_contribution_to_fine_category_per_month = $this->statements_m->get_group_total_contribution_transfers_from_contribution_to_fine_category_per_month($this->group->id,$from,$to,$contribution_id_list);

        //subtract
        $total_group_contribution_transfers_to_loans_per_month = $this->statements_m->get_group_total_contribution_transfers_to_loan_per_month($this->group->id,$from,$to,$contribution_id_list);

        // print_r($total_group_contribution_transfers_to_loans_per_year);
        // die;
       
       	$arr = array();

       	foreach($total_group_contributions_per_month as $row):
			
			$arr[$row->month.' '.$row->year] = 0;
			
		endforeach;

       	foreach($total_group_contribution_refunds_per_month as $row):
			
			$arr[$row->month.' '.$row->year] = 0;
			
		endforeach;
       
       	foreach($total_group_contribution_transfers_from_per_month as $row):
			
			$arr[$row->month] = 0;
			
		endforeach;

       	foreach($total_group_contribution_transfers_to_per_month as $row):
			
			$arr[$row->month] = 0;
			
		endforeach;

       	foreach($total_group_contribution_transfers_from_contribution_to_fine_category_per_month as $row):
			
			$arr[$row->month] = 0;
			
		endforeach;

       	foreach($total_group_contribution_transfers_to_loans_per_month as $row):
			
			$arr[$row->month] = 0;
			
		endforeach;

		//here

		foreach($total_group_contributions_per_month as $row):
			
			$arr[$row->month.' '.$row->year] += $row->amount;
			
		endforeach;


       	foreach($total_group_contribution_refunds_per_month as $row):
			
			$arr[$row->month.' '.$row->year] -= $row->amount;
			
		endforeach;


       	foreach($total_group_contribution_transfers_from_per_month as $row):
			
			$arr[$row->month] -= $row->amount;
			
		endforeach;


       	foreach($total_group_contribution_transfers_to_per_month as $row):
			
			$arr[$row->month] += $row->amount;
			
		endforeach;

       	foreach($total_group_contribution_transfers_from_contribution_to_fine_category_per_month as $row):
			
			$arr[$row->month] -= $row->amount;
			
		endforeach;

       	foreach($total_group_contribution_transfers_to_loans_per_month as $row):
			
			$arr[$row->month] -= $row->amount;
			
		endforeach;

		foreach($months_array as $month):
			$previous_month = date('M Y',strtotime('-1 month',strtotime($month)));
			if(isset($arr[$previous_month])){
				if(isset($arr[$month])){
					$arr[$month] += $arr[$previous_month];
				}
			}
		endforeach;

        return $arr;
	}

	function get_group_total_loan_interest_paid_per_month_array($group_id = 0){
		$this->_distribute_principal_and_interest_paid_per_month($group_id);
		return $this->interest_paid_per_month_array;
	}

	function _distribute_principal_and_interest_paid_per_month($group_id = 0,$from = 0,$to = 0){

		$this->principal_paid_per_month_array = array();
		$this->interest_paid_per_month_array = array();
		$this->over_payments_per_month_array = array();
		$this->loan_amounts_paid_per_loan_array = array();
		$this->loan_amounts_payable_per_loan_array = array();
		$this->principal_amounts_paid_per_loan_array = array();
		$this->interest_amounts_paid_per_loan_array = array();
		$this->loan_balances_per_loan_array = array();

		$loans = $this->loans_m->get_group_loans();

		$deposits_per_loan_array = $this->deposits_m->get_group_loan_repayment_deposits_per_loan_array($group_id,$from,$to);

		$contribution_transfers_to_loan_per_loan_array = $this->deposits_m->get_group_contribution_transfers_to_loan_per_loan_array($group_id,$from,$to);

		$loan_invoices_per_loan_array = $this->loan_invoices_m->get_group_loan_invoices_per_loan_array($group_id);

		$total_amount_paid = 0;
		$total_overpayments = 0;

		foreach($loans as $loan):

			$amount_to_distribute = 0;
			$amount_payable = 0;
			$amount_paid = 0;
			$loan_invoices = isset($loan_invoices_per_loan_array[$loan->id])?$loan_invoices_per_loan_array[$loan->id]:array();
			$deposits = isset($deposits_per_loan_array[$loan->id])?$deposits_per_loan_array[$loan->id]:array();
			$contribution_transfers = isset($contribution_transfers_to_loan_per_loan_array[$loan->id])?$contribution_transfers_to_loan_per_loan_array[$loan->id]:array();

			if($contribution_transfers){
				foreach($contribution_transfers as $contribution_transfer):
					$deposit = new stdClass();
					$deposit->deposit_date = $contribution_transfer->transfer_date;
					$deposit->loan_id = $contribution_transfer->loan_to_id;
					$deposit->amount = $contribution_transfer->amount;
					$deposits[] = $deposit;
				endforeach;
			}

			foreach($loan_invoices as $loan_invoice):
				$loan_invoice->amount_paid = 0;
				$loan_invoice->is_fully_paid = 0;
				$loan_invoice->principle_paid = 0;
				$loan_invoice->interest_paid = 0;
				if($loan_invoice->type == 2 || $loan_invoice->type == 3){
					$loan_invoice->interest_payable = 0;
					$loan_invoice->principle_amount_payable = 0;
				}
				$amount_payable += $loan_invoice->amount_payable;
				if(isset($this->loan_balances_per_loan_array[$loan_invoice->loan_id])){
					$this->loan_balances_per_loan_array[$loan_invoice->loan_id] += $loan_invoice->amount_payable;
				}else{
					$this->loan_balances_per_loan_array[$loan_invoice->loan_id] = $loan_invoice->amount_payable;
				}
				if(isset($this->loan_amounts_payable_per_loan_array[$loan_invoice->loan_id])){
					$this->loan_amounts_payable_per_loan_array[$loan_invoice->loan_id] += $loan_invoice->amount_payable;
				}else{
					$this->loan_amounts_payable_per_loan_array[$loan_invoice->loan_id] = $loan_invoice->amount_payable;
				}
			endforeach;

			$count = 1;

			foreach($deposits as $deposit):

				if(isset($this->loan_amounts_paid_per_loan_array[$deposit->loan_id])){
					$this->loan_amounts_paid_per_loan_array[$deposit->loan_id] += $deposit->amount;
				}else{
					$this->loan_amounts_paid_per_loan_array[$deposit->loan_id] = $deposit->amount;
				}

				$amount_paid += $deposit->amount;

				$amount_to_distribute = $deposit->amount;

				$invoice_count = 1;

				foreach($loan_invoices as $key => $loan_invoice):
					//echo "Amount to distribute: ".number_to_currency($amount_to_distribute)."<br/>";
					if($amount_to_distribute > 0){

						if($loan_invoice->is_fully_paid){

							//do nothing here

							//echo "#".$loan_invoice->id." Ignored as it is fully paid. <br/>";

						}else{
		 
							$loan_invoice_balance = $loan_invoice->amount_payable - $loan_invoice->amount_paid;

							//echo "#".$loan_invoice->id.": loan invoice balance: ".number_to_currency($loan_invoice_balance)."<br/>";

							if($amount_to_distribute >= $loan_invoice_balance){

								$loan_invoice_principal_balance = $loan_invoice->principle_amount_payable - $loan_invoice->principle_paid;

								$loan_invoice_interest_balance = $loan_invoice->interest_amount_payable - $loan_invoice->interest_paid;

								if($loan_invoice_principal_balance == 0){

									if($loan_invoice_interest_balance == 0){

									}else{
										
										if(isset($this->interest_paid_per_month_array[date('M Y',$deposit->deposit_date)])){
											$this->interest_paid_per_month_array[date('M Y',$deposit->deposit_date)] += round($loan_invoice_interest_balance,2);
										}else{
											$this->interest_paid_per_month_array[date('M Y',$deposit->deposit_date)] = round($loan_invoice_interest_balance,2);
										}

										if(isset($this->interest_amounts_paid_per_loan_array[$deposit->loan_id])){
											$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] += round($loan_invoice_interest_balance,2);
										}else{
											$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] = round($loan_invoice_interest_balance,2);
										}

										if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
											$this->loan_balances_per_loan_array[$deposit->loan_id] -=  round($loan_invoice_interest_balance,2);
										}else{
											$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($loan_invoice_interest_balance,2);
										}

										$amount_to_distribute -= $loan_invoice_interest_balance;								

									}

								}else{

									if(isset($this->principal_paid_per_month_array[date('M Y',$deposit->deposit_date)])){
										$this->principal_paid_per_month_array[date('M Y',$deposit->deposit_date)] += round($loan_invoice_principal_balance,2);
									}else{
										$this->principal_paid_per_month_array[date('M Y',$deposit->deposit_date)] = round($loan_invoice_principal_balance,2);
									}

									if(isset($this->principal_amounts_paid_per_loan_array[$deposit->loan_id])){
										$this->principal_amounts_paid_per_loan_array[$deposit->loan_id] += round($loan_invoice_principal_balance,2);
									}else{
										$this->principal_amounts_paid_per_loan_array[$deposit->loan_id] = round($loan_invoice_principal_balance,2);
									}


									if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
										$this->loan_balances_per_loan_array[$deposit->loan_id] -=  round($loan_invoice_principal_balance,2);
									}else{
										$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($loan_invoice_principal_balance,2);
									}


									$amount_to_distribute -= $loan_invoice_principal_balance;

									if(isset($this->interest_paid_per_month_array[date('M Y',$deposit->deposit_date)])){
										$this->interest_paid_per_month_array[date('M Y',$deposit->deposit_date)] += round($loan_invoice_interest_balance,2);
									}else{
										$this->interest_paid_per_month_array[date('M Y',$deposit->deposit_date)] = round($loan_invoice_interest_balance,2);
									}

									if(isset($this->interest_amounts_paid_per_loan_array[$deposit->loan_id])){
										$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] += round($loan_invoice_interest_balance,2);
									}else{
										$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] = round($loan_invoice_interest_balance,2);
									}

									if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
										$this->loan_balances_per_loan_array[$deposit->loan_id] -=  round($loan_invoice_interest_balance,2);
									}else{
										$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($loan_invoice_interest_balance,2);
									}

									$amount_to_distribute -= $loan_invoice_interest_balance;

								}

								$loan_invoice->amount_paid = $loan_invoice->amount_payable;
								$loan_invoice->is_fully_paid = 1;
								$loan_invoice->principle_paid = $loan_invoice->principle_amount_payable;
								$loan_invoice->interest_paid = $loan_invoice->interest_amount_payable;

							}else{

								$loan_invoice_principal_balance = $loan_invoice->principle_amount_payable - $loan_invoice->principle_paid;

								if($loan_invoice_principal_balance > 0){

									if($amount_to_distribute >= $loan_invoice_principal_balance){

										
										$amount_to_distribute -= $loan_invoice_principal_balance;

										if(isset($this->principal_paid_per_month_array[date('M Y',$deposit->deposit_date)])){
											$this->principal_paid_per_month_array[date('M Y',$deposit->deposit_date)] += round($loan_invoice_principal_balance,2);
										}else{
											$this->principal_paid_per_month_array[date('M Y',$deposit->deposit_date)] = round($loan_invoice_principal_balance,2);
										}

										if(isset($this->principal_amounts_paid_per_loan_array[$deposit->loan_id])){
											$this->principal_amounts_paid_per_loan_array[$deposit->loan_id] += round($loan_invoice_principal_balance,2);
										}else{
											$this->principal_amounts_paid_per_loan_array[$deposit->loan_id] = round($loan_invoice_principal_balance,2);
										}

										if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
											$this->loan_balances_per_loan_array[$deposit->loan_id] -=  round($loan_invoice_principal_balance,2);
										}else{
											$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($loan_invoice_principal_balance,2);
										}
											
										$loan_invoice->principle_paid += $loan_invoice_principal_balance;

										$loan_invoice->amount_paid += $loan_invoice_principal_balance;

										if($amount_to_distribute > 0){

											$loan_invoice_interest_balance = $loan_invoice->interest_amount_payable - $loan_invoice->interest_paid;

											if($amount_to_distribute >= $loan_invoice_interest_balance){

												$amount_to_distribute -= $loan_invoice_interest_balance;

												if(isset($this->interest_paid_per_month_array[date('M Y',$deposit->deposit_date)])){
													$this->interest_paid_per_month_array[date('M Y',$deposit->deposit_date)] += round($loan_invoice_interest_balance,2);
												}else{
													$this->interest_paid_per_month_array[date('M Y',$deposit->deposit_date)] = round($loan_invoice_interest_balance,2);
												}

												if(isset($this->interest_amounts_paid_per_loan_array[$deposit->loan_id])){
													$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] += round($loan_invoice_interest_balance,2);
												}else{
													$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] = round($loan_invoice_interest_balance,2);
												}


												if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
													$this->loan_balances_per_loan_array[$deposit->loan_id] -= round($loan_invoice_interest_balance,2);
												}else{
													$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($loan_invoice_interest_balance,2);
												}
											

												$loan_invoice->interest_paid += $loan_invoice->interest_amount_payable;
												
												$loan_invoice->amount_paid += $loan_invoice->interest_amount_payable;

												$loan_invoice->is_fully_paid = 1;

											}else{

												if(isset($this->interest_paid_per_month_array[date('M Y',$deposit->deposit_date)])){
													$this->interest_paid_per_month_array[date('M Y',$deposit->deposit_date)] += round($amount_to_distribute,2);
												}else{
													$this->interest_paid_per_month_array[date('M Y',$deposit->deposit_date)] = round($amount_to_distribute,2);
												}


												if(isset($this->interest_amounts_paid_per_loan_array[$deposit->loan_id])){
													$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] += round($amount_to_distribute,2);
												}else{
													$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] = round($amount_to_distribute,2);
												}


												if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
													$this->loan_balances_per_loan_array[$deposit->loan_id] -= round($amount_to_distribute,2);
												}else{
													$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($amount_to_distribute,2);
												}


												$loan_invoice->interest_paid += $amount_to_distribute;
												
												$loan_invoice->amount_paid += $amount_to_distribute;

												$amount_to_distribute = 0;

												break;

											}

										}

									}else{

										if(isset($this->principal_paid_per_month_array[date('M Y',$deposit->deposit_date)])){
											$this->principal_paid_per_month_array[date('M Y',$deposit->deposit_date)] += round($amount_to_distribute,2);
										}else{
											$this->principal_paid_per_month_array[date('M Y',$deposit->deposit_date)] = round($amount_to_distribute,2);
										}

										if(isset($this->principal_amounts_paid_per_loan_array[$deposit->loan_id])){
											$this->principal_amounts_paid_per_loan_array[$deposit->loan_id] += round($amount_to_distribute,2);
										}else{
											$this->principal_amounts_paid_per_loan_array[$deposit->loan_id] = round($amount_to_distribute,2);
										}

	
										if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
											$this->loan_balances_per_loan_array[$deposit->loan_id] -= round($amount_to_distribute,2);
										}else{
											$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($amount_to_distribute,2);
										}

										$loan_invoice->principle_paid += $amount_to_distribute; 

										$loan_invoice->amount_paid += $amount_to_distribute; 

										$amount_to_distribute = 0;

									}

								}else{

									$loan_invoice_interest_balance = $loan_invoice->interest_amount_payable - $loan_invoice->interest_paid;

									if($amount_to_distribute >= $loan_invoice_interest_balance){

										if(isset($this->interest_paid_per_month_array[date('M Y',$deposit->deposit_date)])){
											$this->interest_paid_per_month_array[date('M Y',$deposit->deposit_date)] += round($loan_invoice_interest_balance,2);
										}else{
											$this->interest_paid_per_month_array[date('M Y',$deposit->deposit_date)] = round($loan_invoice_interest_balance,2);
										}


										if(isset($this->interest_amounts_paid_per_loan_array[$deposit->loan_id])){
											$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] += round($loan_invoice_interest_balance,2);
										}else{
											$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] = round($loan_invoice_interest_balance,2);
										}


										
										if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
											$this->loan_balances_per_loan_array[$deposit->loan_id] -= round($loan_invoice_interest_balance,2);
										}else{
											$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($loan_invoice_interest_balance,2);
										}


										$loan_invoice->interest_paid = $loan_invoice->interest_amount_payable;
										
										$loan_invoice->amount_paid += $loan_invoice->interest_amount_payable;

										$loan_invoice->is_fully_paid = 1;

										$amount_to_distribute -= $loan_invoice_interest_balance;

									}else{

										if(isset($this->interest_paid_per_month_array[date('M Y',$deposit->deposit_date)])){
											$this->interest_paid_per_month_array[date('M Y',$deposit->deposit_date)] += round($amount_to_distribute,2);
										}else{
											$this->interest_paid_per_month_array[date('M Y',$deposit->deposit_date)] = round($amount_to_distribute,2);
										}


										if(isset($this->interest_amounts_paid_per_loan_array[$deposit->loan_id])){
											$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] += round($amount_to_distribute,2);
										}else{
											$this->interest_amounts_paid_per_loan_array[$deposit->loan_id] = round($amount_to_distribute,2);
										}


										
										if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
											$this->loan_balances_per_loan_array[$deposit->loan_id] -= round($amount_to_distribute,2);
										}else{
											$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($amount_to_distribute,2);
										}

										$loan_invoice->interest_paid += $amount_to_distribute;
										
										$loan_invoice->amount_paid += $amount_to_distribute;

										$amount_to_distribute = 0;

										break;

									}
								}

							}
						}

					}else{
						break;
					}

					$invoice_count++;						

					$loan_invoices[$key] = $loan_invoice;

				endforeach;
				if($amount_to_distribute > 0){
					if(isset($this->over_payments_per_month_array[date('M Y',$deposit->deposit_date)])){
						$this->over_payments_per_month_array[date('M Y',$deposit->deposit_date)] += round($amount_to_distribute,2);
					}else{
						$this->over_payments_per_month_array[date('M Y',$deposit->deposit_date)] = round($amount_to_distribute,2);
					}

					if(isset($this->loan_balances_per_loan_array[$deposit->loan_id])){
						$this->loan_balances_per_loan_array[$deposit->loan_id] -= round($amount_to_distribute,2);
					}else{
						$this->loan_balances_per_loan_array[$deposit->loan_id] = 0 - round($amount_to_distribute,2);
					}
					
					$amount_to_distribute = 0;

				}
				// if($amount_to_distribute == 0){

				// }else{
				// 	echo $amount_to_distribute.'<br/>';
				// }

				$total_amount_paid += $deposit->amount;

				$count++;

			endforeach;

			$balance = $amount_payable - $amount_paid;
			if($balance < 0){
				$total_overpayments += $balance;
			}

		endforeach;
	}

	function get_group_total_contributions_paid_per_contribution_per_month_array($group_id = 0,$months_array = array()){
		//Get contributions paid
		$group_total_contributions_per_contribution_per_month_array = $this->deposits_m->get_group_total_contributions_per_contribution_per_month_array($this->group->id,$months_array);
		//Subtract contribution refunds
		$group_total_contribution_refunds_per_contribution_per_month_array = $this->withdrawals_m->get_group_total_contribution_refunds_per_contribution_per_month_array($this->group->id,$months_array);

        foreach($group_total_contributions_per_contribution_per_month_array as $contribution_id => $amount):
        	foreach($months_array as $month):
	        	$group_total_contributions_per_contribution_per_month_array[$contribution_id][$month] -= $group_total_contribution_refunds_per_contribution_per_month_array[$contribution_id][$month];
	        endforeach;
        endforeach;


        //Subtract contribution transfers to fines
        $group_contribution_transfers_from_contribution_to_fine_category_per_contribution_per_month_array = $this->statements_m->get_group_contribution_transfers_from_contribution_to_fine_category_per_contribution_per_month_array($this->group->id,$months_array);
        foreach($group_total_contributions_per_contribution_per_month_array as $contribution_id => $amount):
        	foreach($months_array as $month):
        		$group_total_contributions_per_contribution_per_month_array[$contribution_id][$month] -= $group_contribution_transfers_from_contribution_to_fine_category_per_contribution_per_month_array[$contribution_id][$month];
        	endforeach;	
        endforeach;
        
        
        //subtract ignored contribution to transfer
        $group_total_contribution_transfers_to_ignore_per_contribution_per_month_array = $this->statements_m->get_group_total_contribution_transfers_to_ignore_per_contribution_per_month_array($this->group->id,$months_array);
		foreach($group_total_contributions_per_contribution_per_month_array as $contribution_id => $amount):
			foreach($months_array as $month):
        		$group_total_contributions_per_contribution_per_month_array[$contribution_id][$month] -= ($group_total_contribution_transfers_to_ignore_per_contribution_per_month_array[$contribution_id][$month]>0)?$group_total_contribution_transfers_to_ignore_per_contribution_per_month_array[$contribution_id][$month]:0;
        	endforeach;
        endforeach;
        
        //add ignored contribution from transfer
        $group_total_contribution_transfers_from_ignore_per_contribution_per_month_array = $this->statements_m->get_group_total_contribution_transfers_from_ignore_per_contribution_per_month_array($this->group->id,$months_array);
		foreach($group_total_contributions_per_contribution_per_month_array as $contribution_id => $amount):
			foreach($months_array as $month):
        		$group_total_contributions_per_contribution_per_month_array[$contribution_id][$month] += ($group_total_contribution_transfers_from_ignore_per_contribution_per_month_array[$contribution_id][$month]>0)?$group_total_contribution_transfers_from_ignore_per_contribution_per_month_array[$contribution_id][$month]:0;
        	endforeach;
        endforeach;

        return $group_total_contributions_per_contribution_per_month_array;
	}

	function get_group_total_fines_per_month_array($group_id = 0,$from = 0,$to = 0){
		$group_total_fines_per_month = $this->deposits_m->get_group_total_fines_per_month($group_id);
		$group_total_contribution_transfers_to_fines_per_month = $this->statements_m->get_group_total_contribution_transfers_to_fines_per_month($group_id);

		$arr = array();

       	foreach($group_total_fines_per_month as $row):
			
			$arr[$row->month.' '.$row->year] = 0;
			
		endforeach;

       	foreach($group_total_contribution_transfers_to_fines_per_month as $row):
			
			$arr[$row->month] = 0;
			
		endforeach;

       	foreach($group_total_fines_per_month as $row):
			
			$arr[$row->month.' '.$row->year] += $row->amount;
			
		endforeach;

       	foreach($group_total_contribution_transfers_to_fines_per_month as $row):
			
			$arr[$row->month] += $row->amount;
			
		endforeach;

		return $arr;
	}

	function get_group_total_loan_overpayments_per_month_array($group_id = 0){
		$this->_distribute_principal_and_interest_paid_per_month($group_id);
		$arr = $this->over_payments_per_month_array;
        foreach($arr as $key => $value):
            if(isset($arr[($key - 1)])){
                $arr[$key] += $arr[($key - 1)];
            }
        endforeach;
		return $arr;
	}

	function get_group_member_contributions_per_year_array($group_id = 0,$from = 0,$to = 0,$contribution_options = array(),$member_options = array()){
		$contribution_id_list = array();

		$count = 1;
		foreach($contribution_options as $contribution_id => $name):
				$contribution_id_list[] = $contribution_id;
			$count++;
		endforeach;

		$from = $from?$from:strtotime('-10 years');
        $to = $to?$to:time();

		//Get contributions paid
		$total_group_member_contributions_per_year = $this->deposits_m->get_group_member_total_contributions_per_year_per_month($this->group->id,$from,$to,implode(',', $contribution_id_list));
		//Subtract contribution refunds
        $total_group_member_contribution_refunds_per_year = $this->withdrawals_m->get_group_member_total_contribution_refunds_per_year_per_month($this->group->id,$from,$to,implode(',', $contribution_id_list));
        //Subtract contribution transfers from respective contributions
        $total_group_member_contribution_transfers_from_per_year = $this->statements_m->get_group_member_total_contribution_transfers_from_per_year_per_month($this->group->id,$from,$to,implode(',', $contribution_id_list));
        //Add contribution transfers to respective contributions 
        $total_group_member_contribution_transfers_to_per_year = $this->statements_m->get_group_member_total_contribution_transfers_to_per_year_per_month($this->group->id,$from,$to,implode(',', $contribution_id_list));

        //Subtract contribution transfers to fines
        $total_group_member_contribution_transfers_from_contribution_to_fine_category_per_year = $this->statements_m->get_group_member_total_contribution_transfers_from_contribution_to_fine_category_per_year($this->group->id,$from,$to,implode(',', $contribution_id_list));

        //subtract
        $total_group_member_contribution_transfers_to_loans_per_year = $this->statements_m->get_group_member_total_contribution_transfers_to_loan_per_year($this->group->id,$from,$to,implode(',', $contribution_id_list));  

        $arr = array();
        
        $years_array = generate_previous_years_array($from,$to);
        foreach ($member_options as $member_id => $member):
        	foreach($years_array as $year):
        		for ($i=1; $i <= 12 ; $i++) :
					$arr[$member_id][$year->year][$i] = 0;
					if($total_group_member_contributions_per_year){
						if(array_key_exists($member_id, $total_group_member_contributions_per_year)){
							if(array_key_exists($year->year, $total_group_member_contributions_per_year[$member_id])){
								if(array_key_exists($i, $total_group_member_contributions_per_year[$member_id][$year->year])){
									$arr[$member_id][$year->year][$i] = $total_group_member_contributions_per_year[$member_id][$year->year][$i]??0;
								}
							}
						}
					}
					if($total_group_member_contribution_refunds_per_year){
						if(array_key_exists($member_id, $total_group_member_contribution_refunds_per_year)){
							if(array_key_exists($year->year, $total_group_member_contribution_refunds_per_year[$member_id])){
								$arr[$member_id][$year->year][$i] -= $total_group_member_contribution_refunds_per_year[$member_id][$year->year][$i]??0;
							}
						}
					}


			       	if($total_group_member_contribution_transfers_from_per_year){
			       		if(array_key_exists($member_id, $total_group_member_contribution_transfers_from_per_year)){
				       		if(array_key_exists($year->year, $total_group_member_contribution_transfers_from_per_year[$member_id])){
				       			$arr[$member_id][$year->year][$i] -= $total_group_member_contribution_transfers_from_per_year[$member_id][$year->year][$i]??0;
							}
						}
					}


			       	if($total_group_member_contribution_transfers_to_per_year){
			       		if(array_key_exists($member_id, $total_group_member_contribution_transfers_to_per_year)){
				       		if(array_key_exists($year->year, $total_group_member_contribution_transfers_to_per_year[$member_id])){
					       		$arr[$member_id][$year->year][$i] += $total_group_member_contribution_transfers_to_per_year[$member_id][$year->year][$i]??0;
							}
						}
					}

		       		if($total_group_member_contribution_transfers_from_contribution_to_fine_category_per_year){
		       			if(array_key_exists($member_id, $total_group_member_contribution_transfers_from_contribution_to_fine_category_per_year)){
			       			if(array_key_exists($year->year, $total_group_member_contribution_transfers_from_contribution_to_fine_category_per_year[$member_id])){
					       		$arr[$member_id][$year->year][$i] -= $total_group_member_contribution_transfers_from_contribution_to_fine_category_per_year[$member_id][$year->year][$i]??0;
							}
						}
					}

			       	if($total_group_member_contribution_transfers_to_loans_per_year){
			       		if(array_key_exists($member_id, $total_group_member_contribution_transfers_to_loans_per_year)){
				       		if(array_key_exists($year->year, $total_group_member_contribution_transfers_to_loans_per_year[$member_id])){
						       	$arr[$member_id][$year->year][$i] -= $total_group_member_contribution_transfers_to_loans_per_year[$member_id][$year->year][$i]??0;
							}
						}
					}
				endfor;
			endforeach;			
		endforeach;
        return $arr;

	}

	

}