<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Withdrawals_m extends MY_Model{

	protected $_table = 'withdrawals';

	function __construct(){
		parent::__construct();
		$this->install();
		$this->load->model('contributions/contributions_m');
	}

	function install(){
		$this->db->query("
			create table if not exists withdrawals(
				id int not null auto_increment primary key,
				`type` blob,
				`group_id` blob,
				`account_id` blob,
				`withdrawal_date` blob,
				`expense_category_id` blob,
				`withdrawal_method` blob,
				`amount` blob,
				`description` blob,
				`active` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_by` blob,
				`modified_on` blob
			)"
		);

		$this->db->query("
			create table if not exists withdrawal_requests(
				id int not null auto_increment primary key,
				`withdrawal_for` blob,
				`declined_on` blob,
				`expense_category_id` blob,
				`amount` blob,
				`loan_type_id` blob,
				`account_number` blob,
				`account_name` blob,
				`member_id` blob,
				`description` blob,
				`contribution_id` blob,
				`disbursement_channel` blob,
				`bank_id` blob,
				`welfare_recipient` blob,
				`request_date` blob,
				`group_id` blob,
				`user_id` blob,
				`recipient_id` blob,
				`active` blob,
				`status` blob,
				`transfer_from_account_id` blob,
				`is_approved` blob,
				`is_declined` blob,
				`created_on` blob,
				`created_by` blob,
				`modified_by` blob,
				`modified_on` blob
			)"
		);

		
		$this->db->query("
			create table if not exists withdrawal_approval_requests(
				id int not null auto_increment primary key,
				`group_id` blob,
				`member_id` blob,
				`status` blob,
				`withdrawal_request_id` blob,
				`is_approved` blob,
				`approved_on` blob,
				`description` blob,
				`is_declined` blob,
				`declined_on` blob,
				`comments` blob,
				`active` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_by` blob,
				`modified_on` blob
			)"
		);

		$this->db->query("
			create table if not exists withdrawal_request_reference_numbers(
				id int not null auto_increment primary key,
				`reference_number` blob,
				`created_on` blob
			)"
		);
	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('withdrawals',$input);
	}

	function update($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'withdrawals',$input);
	}

	function update_where($where = "",$input = array()){
    	return $this->update_secure_where($where,'withdrawals',$input);
    }

	function update_account_transfers_where($where = "",$input = array()){
    	return $this->update_secure_where($where,'account_transfers',$input);
    }

	function update_bank_loan_repayments_where($where = "",$input = array()){
    	return $this->update_secure_where($where,'bank_loan_repayments',$input);
    }

	function get($id=0){
		$this->select_all_secure('withdrawals');
		$this->db->where('id',$id);
		return $this->db->get('withdrawals')->row();
	}

	function get_group_withdrawal($id=0,$group_id=0){
		$this->select_all_secure('withdrawals');
		$this->db->where('id',$id);
		
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('withdrawals')->row();
	}

	function get_group_withdrawals($filter_parameters = array(),$group_id=0,$sort_by='',$sort_order="DESC"){
		$this->select_all_secure('withdrawals');
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('withdrawal_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('withdrawal_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['transaction_alert_id'])){
			$this->db->where($this->dx('transaction_alert_id').' = "'.$filter_parameters['transaction_alert_id'].'"');
		}
		if(isset($filter_parameters['type']) && $filter_parameters['type']){
			$this->db->where($this->dx('type')." IN (".$filter_parameters['type'].")",NULL,FALSE);
		}
		if(isset($filter_parameters['expense_categories']) && $filter_parameters['expense_categories']){
			if(empty($filter_parameters['expense_categories'])){
				$this->db->where($this->dx('expense_category_id')." IN (0)",NULL,FALSE);
			}else{
				$this->db->where($this->dx('expense_category_id')." IN (".implode(',',$filter_parameters['expense_categories']).")",NULL,FALSE);
			}
		}
		if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
			$member_list = '0';
			$members = $filter_parameters['member_id'];
			$count = 1;
			foreach($members as $member_id){
				if($member_id){
					if($count==1){
						$member_list = $member_id;
					}else{
						$member_list .= ','.$member_id;
					}
					$count++;
				}
			}
			if($member_list){
        		$this->db->where($this->dx('member_id').' IN ('.$member_list.')',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['accounts']) && $filter_parameters['accounts']){
			$account_list = '0';
			$accounts = $filter_parameters['accounts'];
			$count = 1;
			foreach($accounts as $account_id){
				if($count==1){
					$account_list = '"'.$account_id.'"';
				}else{
					$account_list .= ',"'.$account_id.'"';
				}
				$count++;
			}
			if($account_list){
        		$this->db->where($this->dx('account_id').' IN ('.$account_list.')',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['stocks']) && $filter_parameters['stocks']){
			$stocks = $filter_parameters['stocks'];
			if(empty($stocks)){
        		$this->db->where($this->dx('stock_id').' IN (0)',NULL,FALSE);
			}else{
        		$this->db->where($this->dx('stock_id').' IN ('.implode(',',$stocks).')',NULL,FALSE);
			}
		}


		if(isset($filter_parameters['money_market_investments']) && $filter_parameters['money_market_investments']){
			$money_market_investments = $filter_parameters['money_market_investments'];
			if(empty($money_market_investments)){
        		$this->db->where($this->dx('money_market_investment_id').' IN (0)',NULL,FALSE);
			}else{
        		$this->db->where($this->dx('money_market_investment_id').' IN ('.implode(',',$money_market_investments).')',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['contributions']) && $filter_parameters['contributions']){
			$contribution_id_list = '0';
			$contributions = $filter_parameters['contributions'];
			$count = 1;
			foreach($contributions as $contribution_id){
				if($contribution_id){
					if($count==1){
						$contribution_id_list = $contribution_id;
					}else{
						$contribution_id_list .= ','.$contribution_id;
					}
					$count++;
				}
			}
			if($contribution_id_list){
        		$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
			}
		}
		
		$this->db->where("(".$this->dx('is_a_back_dating_record').' IS NULL OR '.$this->dx('is_a_back_dating_record')." = '0' )",NULL,FALSE);
		
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		if($sort_order&&$sort_by){
			$this->db->order_by($this->dx($sort_by)+'0',$sort_order,FALSE);
		}else{
			$this->db->order_by($this->dx('withdrawal_date'),'DESC',FALSE);
		}
		return $this->db->get('withdrawals')->result();
	}

	function batch_insert_withdrawal_requests($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_chunked_batch_secure_data('withdrawal_requests',$input);
    }

	function count_group_withdrawals($filter_parameters = array(),$group_id = 0){
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('withdrawal_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('withdrawal_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['transaction_alert_id'])){
			$this->db->where($this->dx('transaction_alert_id').' = "'.$filter_parameters['transaction_alert_id'].'"',NULL,FALSE);
		}
		if(isset($filter_parameters['type']) && $filter_parameters['type']){
			$this->db->where($this->dx('type')." IN (".$filter_parameters['type'].")",NULL,FALSE);
		}
		if(isset($filter_parameters['expense_categories']) && $filter_parameters['expense_categories']){
			if(empty($filter_parameters['expense_categories'])){
				$this->db->where($this->dx('expense_category_id')." IN (0)",NULL,FALSE);
			}else{
				$this->db->where($this->dx('expense_category_id')." IN (".implode(',',$filter_parameters['expense_categories']).")",NULL,FALSE);
			}
		}
		if(isset($filter_parameters['assets']) && $filter_parameters['assets']){
			if(empty($filter_parameters['assets'])){
				$this->db->where($this->dx('asset_id')." IN (0)",NULL,FALSE);
			}else{
				$this->db->where($this->dx('asset_id')." IN (".implode(',',$filter_parameters['assets']).")",NULL,FALSE);
			}
		}
		if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
			$member_list = '0';
			$members = $filter_parameters['member_id'];
			$count = 1;
			foreach($members as $member_id){
				if($member_id){
					if($count==1){
						$member_list = $member_id;
					}else{
						$member_list .= ','.$member_id;
					}
					$count++;
				}
			}
			if($member_list){
        		$this->db->where($this->dx('member_id').' IN ('.$member_list.')',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['accounts']) && $filter_parameters['accounts']){
			$account_list = '0';
			$accounts = $filter_parameters['accounts'];
			$count = 1;
			foreach($accounts as $account_id){
				if($count==1){
					$account_list = '"'.$account_id.'"';
				}else{
					$account_list .= ',"'.$account_id.'"';
				}
				$count++;
			}
			if($account_list){
        		$this->db->where($this->dx('account_id').' IN ('.$account_list.')',NULL,FALSE);
			}
		}
		

		if(isset($filter_parameters['stocks']) && $filter_parameters['stocks']){
			$stocks = $filter_parameters['stocks'];
			if(empty($stocks)){
        		$this->db->where($this->dx('stock_id').' IN (0)',NULL,FALSE);
			}else{
        		$this->db->where($this->dx('stock_id').' IN ('.implode(',',$stocks).')',NULL,FALSE);
			}
		}


		if(isset($filter_parameters['money_market_investments']) && $filter_parameters['money_market_investments']){
			$money_market_investments = $filter_parameters['money_market_investments'];
			if(empty($money_market_investments)){
        		$this->db->where($this->dx('money_market_investment_id').' IN (0)',NULL,FALSE);
			}else{
        		$this->db->where($this->dx('money_market_investment_id').' IN ('.implode(',',$money_market_investments).')',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['contributions']) && $filter_parameters['contributions']){
			$contribution_id_list = '0';
			$contributions = $filter_parameters['contributions'];
			$count = 1;
			foreach($contributions as $contribution_id){
				if($contribution_id){
					if($count==1){
						$contribution_id_list = $contribution_id;
					}else{
						$contribution_id_list .= ','.$contribution_id;
					}
					$count++;
				}
			}
			if($contribution_id_list){
        		$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
			}
		}
		
		$this->db->where("(".$this->dx('is_a_back_dating_record').' IS NULL OR '.$this->dx('is_a_back_dating_record')." = '0' )",NULL,FALSE);
		
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		return $this->db->count_all_results('withdrawals');
	}

	function get_group_total_expenses_by_month_array($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%c') as month ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,4) ',NULL,FALSE);

		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "4")',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
        $this->db->group_by(
        	array(
        		'year',
        		'month'
        	)
        );
		$result = $this->db->get('withdrawals')->result();
		foreach($result as $row){
			$arr[$row->year][$row->month] = $row->amount;
		}
		return $arr;
	}

	function get_group_total_expenses($group_id = 0,$from = 0,$to = 0){
		$this->db->select(array('sum('.$this->dx('amount').') as amount'));
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		// $this->db->where($this->dx('type').' IN (1,2,3,4) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('withdrawal_date').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('withdrawal_date').' <= "'.$to.'"',NULL,FALSE);
		}
		$this->db->limit(1);
		$result = $this->db->get('withdrawals')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}

	}

	function get_group_expense_category_totals_array($group_id = 0,$date_from=0,$date_to=0,$account_list=''){
		$arr = array();
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('expense_category_id').' as expense_category_id ',
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($date_from&&$date_to){
			$this->db->where($this->dx('withdrawal_date').'>="'.$date_from.'"',NULL,FALSE);
			$this->db->where($this->dx('withdrawal_date').'<="'.$date_to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,4) ',NULL,FALSE);
		if($account_list){
			$this->db->where($this->dx('account_id').' IN('.$account_list.')',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->group_by(
        	array(
        		'expense_category_id',
        	)
        );
        $this->db->order_by($this->dx('amount').'+0','DESC',FALSE);
		$result = $this->db->get('withdrawals')->result();
		foreach($result as $row){
			$arr[$row->expense_category_id] = $row->amount;
		}
		return $arr;
	}

	function get_group_expense_category_totals_per_year_array($group_id = 0,$start_year = 2000,$end_year = 2039){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as amount',
				$this->dx('expense_category_id').' as expense_category_id ',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%Y') as year ",

			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,4) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "4")',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->group_by(
        	array(
        		$this->dx('expense_category_id'),
        		'year'
        	)
        );
		$result = $this->db->get('withdrawals')->result();
		foreach($result as $row){
			$arr[$row->expense_category_id][$row->year] = $row->amount;
		}
		return $arr;
	}

	function get_group_asset_purchase_withdrawals($filter_parameters = array()){
		$this->select_all_secure('withdrawals');
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('withdrawal_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('withdrawal_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['assets']) && $filter_parameters['assets']){
			if(empty($filter_parameters['assets'])){
				$this->db->where($this->dx('asset_id')." IN (0)",NULL,FALSE);
			}else{
				$this->db->where($this->dx('asset_id')." IN (".implode(',',$filter_parameters['assets']).")",NULL,FALSE);
			}
		}

		if(isset($filter_parameters['accounts']) && $filter_parameters['accounts']){
			$account_list = '0';
			$accounts = $filter_parameters['accounts'];
			$count = 1;
			foreach($accounts as $account_id){
				if($count==1){
					$account_list = '"'.$account_id.'"';
				}else{
					$account_list .= ',"'.$account_id.'"';
				}
				$count++;
			}
			if($account_list){
        		$this->db->where($this->dx('account_id').' IN ('.$account_list.')',NULL,FALSE);
			}
		}
		$this->db->where($this->dx('type').' IN (5,6,7,8) ',NULL,FALSE);
		//$this->db->where("(".$this->dx('type').'="5" OR '.$this->dx('type').'="6" OR '.$this->dx('type').'="7" OR '.$this->dx('type').'="8" )',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->order_by($this->dx('withdrawal_date'),'DESC',FALSE);
		return $this->db->get('withdrawals')->result();
	}

	function get_group_total_asset_purchase_payments_per_year_array($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%Y') as year ",			
			)
		);
		$this->db->where($this->dx('type').' IN (5,6,7,8) ',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->order_by($this->dx('withdrawal_date'),'ASC',FALSE);
		$this->db->group_by(
        	array(
        		'year',
        	)
        );
		$withdrawals = $this->db->get('withdrawals')->result();
		foreach($withdrawals as $withdrawal){
			$arr[$withdrawal->year] = $withdrawal->amount;
		}
		foreach($arr as $key => $value):
			if(isset($arr[($key - 1)])){
				$arr[$key] += $arr[($key - 1)];
			}
		endforeach;

		ksort($arr);

        $current_year = date('Y');
        if(isset($withdrawal)){
        	$year = $withdrawal->year + 1;
	        $amount = $arr[$withdrawal->year];
	        for($i = $year; $i <= $current_year; $i++):
	            $arr[$i] = $amount;
	        endfor;
        }
        
		return $arr;
	}

	function get_group_asset_purchase_withdrawals_by_asset_id($id = 0,$group_id = 0){
		$this->select_all_secure('withdrawals');
		$this->db->where($this->dx('asset_id').' = "'.$id.'" ',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (5,6,7,8) ',NULL,FALSE);
		
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		return $this->db->get('withdrawals')->result();
	}

	function count_group_asset_purchase_withdrawals($filter_parameters = array()){
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('withdrawal_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('withdrawal_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['assets']) && $filter_parameters['assets']){
			if(empty($filter_parameters['assets'])){
				$this->db->where($this->dx('asset_id')." IN (0)",NULL,FALSE);
			}else{
				$this->db->where($this->dx('asset_id')." IN (".implode(',',$filter_parameters['assets']).")",NULL,FALSE);
			}
		}
		
		if(isset($filter_parameters['accounts']) && $filter_parameters['accounts']){
			$account_list = '0';
			$accounts = $filter_parameters['accounts'];
			$count = 1;
			foreach($accounts as $account_id){
				if($count==1){
					$account_list = '"'.$account_id.'"';
				}else{
					$account_list .= ',"'.$account_id.'"';
				}
				$count++;
			}
			if($account_list){
        		$this->db->where($this->dx('account_id').' IN ('.$account_list.')',NULL,FALSE);
			}
		}
		$this->db->where($this->dx('type').' IN (5,6,7,8) ',NULL,FALSE);
		//$this->db->where("(".$this->dx('type').'="5" OR '.$this->dx('type').'="6" OR '.$this->dx('type').'="7" OR '.$this->dx('type').'="8" )',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		return $this->db->count_all_results('withdrawals');
	}

	function get_group_asset_purchase_total_amount($group_id=0,$from=0,$to=0){
		$this->db->select(
			array(
				' SUM('.$this->dx('amount').') as amount '
			)
		);
		$this->db->where($this->dx('type').' IN (5,6,7,8) ',NULL,FALSE);
		
		$this->db->limit(1);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('withdrawal_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('withdrawal_date').' <="'.$to.'"',NULL,FALSE);
		}
		return $this->db->get('withdrawals')->row()->amount;
	}

	function get_money_market_investment_withdrawals_by_money_market_investment_id($money_market_investment_id = 0,$group_id = 0){
		$this->select_all_secure('withdrawals');
		$this->db->where($this->dx('money_market_investment_id').'="'.$money_market_investment_id.'"',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (17,18,19,20) ',NULL,FALSE);
		//$this->db->where("(".$this->dx('type').'="17" OR '.$this->dx('type').'="18" OR '.$this->dx('type').'="19" OR '.$this->dx('type').'="20" )',NULL,FALSE);
		return $this->db->get('withdrawals')->result();
	}

	function get_money_market_investment_withdrawal_by_money_market_investment_id($money_market_investment_id = 0,$group_id = 0){
		$this->select_all_secure('withdrawals');
		$this->db->where($this->dx('money_market_investment_id').'="'.$money_market_investment_id.'"',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (17,18,19,20) ',NULL,FALSE);
		$this->db->limit(1);
		//$this->db->where("(".$this->dx('type').'="17" OR '.$this->dx('type').'="18" OR '.$this->dx('type').'="19" OR '.$this->dx('type').'="20" )',NULL,FALSE);
		return $this->db->get('withdrawals')->row();
	}

	function get_group_money_market_investment_total_amount($group_id=0,$date_from=0,$date_to=0){
		$this->db->select('sum('.$this->dx('amount').') as amount');
		
		if($date_from&&$date_to){
			$this->db->where($this->dx('withdrawal_date').'>="'.$date_from.'"',NULL,FALSE);
			$this->db->where($this->dx('withdrawal_date').'<="'.$date_to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (17,18,19,20) ',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('withdrawals')->row()->amount?:0;
	}


	function get_withdrawal_by_contribution_refund_id($id = 0,$group_id = 0){
		$this->select_all_secure('withdrawals');
		$this->db->where($this->dx('contribution_refund_id').'="'.$id.'"',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (21,22,23,24) ',NULL,FALSE);
		$this->db->limit(1);
		//$this->db->where("(".$this->dx('type').'="21" OR '.$this->dx('type').'="22" OR '.$this->dx('type').'="23" OR '.$this->dx('type').'="24" )',NULL,FALSE);
		return $this->db->get('withdrawals')->row();
	}

	function get_withdrawal_by_loan_id($loan_id=0,$group_id=0){
		$this->select_all_secure('withdrawals');
		$this->db->where($this->dx('loan_id').'="'.$loan_id.'"',NULL,FALSE);
		
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (9,10,11,12) ',NULL,FALSE);
		$this->db->limit(1);
		//$this->db->where("(".$this->dx('type').'="9" OR '.$this->dx('type').'="10" OR '.$this->dx('type').'="11" OR '.$this->dx('type').'="12" )',NULL,FALSE);
		return $this->db->get('withdrawals')->row();
	}

	function get_withdrawal_by_stock_id($id = 0,$group_id = 0){
		$this->select_all_secure('withdrawals');
		$this->db->where($this->dx('stock_id').'="'.$id.'"',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (13,14,15,16) ',NULL,FALSE);
		$this->db->limit(1);
		//$this->db->where("(".$this->dx('type').'="13" OR '.$this->dx('type').'="14" OR '.$this->dx('type').'="15" OR '.$this->dx('type').'="16" )',NULL,FALSE);
		return $this->db->get('withdrawals')->row();
	}

	function get_group_stock_purchase_options($group_id = 0){
		$this->load->model('stocks/stocks_m');
		$arr = array();
		$this->select_all_secure('withdrawals');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (13,14,15,16) ',NULL,FALSE);
		//$this->db->where("(".$this->dx('type').'="13" OR '.$this->dx('type').'="14" OR '.$this->dx('type').'="15" OR '.$this->dx('type').'="16" )',NULL,FALSE);
		$stock_purchases = $this->db->get('withdrawals')->result();
		$stock_options = $this->stocks_m->get_group_stock_options();
		foreach ($stock_purchases as $stock_purchase) {
			# code...
			$arr[$stock_purchase->stock_id] = $stock_options[$stock_purchase->stock_id].' shares ';
		}
		return $arr;
	}

	function get_group_stock_purchase_total_amount($group_id=0,$date_from=0,$date_to=0){
		$this->db->select('sum('.$this->dx('amount').') as amount');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($date_from&&$date_to){
			$this->db->where($this->dx('withdrawal_date').'>="'.$date_from.'"',NULL,FALSE);
			$this->db->where($this->dx('withdrawal_date').'<="'.$date_to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (13,14,15,16) ',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('withdrawals')->row()->amount?:0;
	}

	function get_group_total_stock_purchases_per_year_array($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as amount ',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%Y') as year ",	
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (13,14,15,16) ',NULL,FALSE);
		$this->db->group_by(
        	array(
        		'year',
        	)
        );
		$withdrawals = $this->db->get('withdrawals')->result();
		foreach($withdrawals as $withdrawal){
			$arr[$withdrawal->year] = $withdrawal->amount;
		}
		foreach($arr as $key => $value):
			if(isset($arr[($key - 1)])){
				$arr[$key] += $arr[($key - 1)];
			}
		endforeach;

		$current_year = date('Y');
		foreach($withdrawals as $withdrawal):
			$year = $withdrawal->year + 1;
			for($i = $year; $i <= $current_year; $i++):
				if(isset($arr[$i])){
					$arr[$i] -= $withdrawal->amount;
				}else{
					$arr[$i] = $withdrawal->amount;
				}
			endfor;
		endforeach;

		return $arr;
	}

	function get_withdrawal_by_account_transfer_id($account_transfer_id = 0,$group_id = 0){
		$this->select_all_secure('withdrawals');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('account_transfer_id').' = "'.$account_transfer_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (29,30,31,32) ',NULL,FALSE);
		$this->db->limit(1);
		//$this->db->where('('.$this->dx('type').' = "29" OR '.$this->dx('type').' = "30" OR '.$this->dx('type').' = "31" OR '.$this->dx('type').' = "32" )',NULL,FALSE);
		return $this->db->get('withdrawals')->row();
	}

	function get_outgoing_account_transfer_total_amount($group_id=0){
		$this->db->select('sum('.$this->dx('amount').') as amount');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (29,30,31,32) ',NULL,FALSE);
		$this->db->limit(1);
		//$this->db->where('('.$this->dx('type').' = "29" OR '.$this->dx('type').' = "30" OR '.$this->dx('type').' = "31" OR '.$this->dx('type').' = "32" )',NULL,FALSE);
		return $this->db->get('withdrawals')->row()->amount?:0;
	}

	function get_group_withdrawal_siblings_by_transaction_alert_id($transaction_alert_id = 0,$group_id = 0){
		$this->select_all_secure('withdrawals');
		$this->db->where($this->dx('transaction_alert_id').' = "'.$transaction_alert_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('withdrawals')->result();
	}

	function get_group_withdrawal_by_bank_loan_repayment_id($bank_loan_repayment_id = 0,$group_id = 0){
		$this->select_all_secure('withdrawals');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('bank_loan_repayment_id').' = "'.$bank_loan_repayment_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (25,26,27,28) ',NULL,FALSE);
		$this->db->limit(1);
		//$this->db->where('('.$this->dx('type').' = "25" OR '.$this->dx('type').' = "26" OR '.$this->dx('type').' = "27" OR '.$this->dx('type').' = "28" )',NULL,FALSE);
		return $this->db->get('withdrawals')->row();
	}

	function get_group_total_bank_loan_repayment($group_id=0,$date_from=0,$date_to=0){
		$this->db->select('sum('.$this->dx('amount').') as amount');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($date_from&&$date_to){
			$this->db->where($this->dx('withdrawal_date').'>="'.$date_from.'"',NULL,FALSE);
			$this->db->where($this->dx('withdrawal_date').'<="'.$date_to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (25,26,27,28) ',NULL,FALSE);
		$this->db->limit(1);
		//$this->db->where('('.$this->dx('type').' = "25" OR '.$this->dx('type').' = "26" OR '.$this->dx('type').' = "27" OR '.$this->dx('type').' = "28" )',NULL,FALSE);
		return $this->db->get('withdrawals')->row()->amount?:0;
	}

	function get_group_total_bank_loans_interest_paid_per_year_array($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as amount_paid ',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%Y') as year ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_bank_loan_interest').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (25,26,27,28) ',NULL,FALSE);
		$this->db->group_by(
            array(
                'year',
            )
        );
		$bank_loan_repayments = $this->db->get('withdrawals')->result();
		foreach($bank_loan_repayments as $bank_loan_repayment):
			$arr[$bank_loan_repayment->year] = $bank_loan_repayment->amount_paid;
		endforeach;
		return $arr;
	}

	function get_group_withdrawal_by_loan_id($loan_id = 0,$group_id = 0){
		$this->select_all_secure('withdrawals');
		 
		$this->db->where($this->dx('loan_id').' = "'.$loan_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (9,10,11,12) ',NULL,FALSE);
		$this->db->limit(1);
		//$this->db->where('('.$this->dx('type').' = "9" OR '.$this->dx('type').' = "10" OR '.$this->dx('type').' = "11" OR '.$this->dx('type').' = "12" )',NULL,FALSE);
		return $this->db->get('withdrawals')->row();
	}

	function get_group_withdrawal_by_debtor_loan_id($debtor_loan_id = 0,$group_id = 0){
		$this->select_all_secure('withdrawals');
		 
		$this->db->where($this->dx('debtor_loan_id').' = "'.$debtor_loan_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (33,34,35,36) ',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('withdrawals')->row();
	}


	function get_group_member_total_contribution_refunds_array($group_id = 0,$date_from=0,$date_to=0){
		$arr = array();
		foreach ($this->group_member_options as $member_id => $name){
			$arr[$member_id] = 0;
		}
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('member_id').' as member_id',
				)
		);
		 
		if($date_from&&$date_to){
			$this->db->where($this->dx('withdrawal_date').'>="'.$date_from.'"',NULL,FALSE);
			$this->db->where($this->dx('withdrawal_date').'<="'.$date_to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (21,22,23,24) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "21" OR '.$this->dx('type').' = "22" OR '.$this->dx('type').' = "23" OR '.$this->dx('type').' = "24" )',NULL,FALSE);
		$this->db->group_by(array($this->dx("member_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
        $this->db->limit($this->group->size);
		$withdrawals = $this->db->get('withdrawals')->result();
		foreach($withdrawals as $withdrawal){
			$arr[$withdrawal->member_id] = $withdrawal->amount;
		}
		return $arr;
	}

	function get_group_member_total_contribution_refunds($member_id = 0,$group_id = 0){
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				)
		);
		 
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' = "'.$this->member->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (21,22,23,24) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "21" OR '.$this->dx('type').' = "22" OR '.$this->dx('type').' = "23" OR '.$this->dx('type').' = "24" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('withdrawals')->row()->amount;
	}

	function get_group_total_contribution_refunds($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ''){
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				)
		);
	 
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
			
		}
		$this->db->where($this->dx('type').' IN (21,22,23,24) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "21" OR '.$this->dx('type').' = "22" OR '.$this->dx('type').' = "23" OR '.$this->dx('type').' = "24" )',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('withdrawal_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('withdrawal_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('withdrawals')->row()->amount;
	}

	function get_group_total_contribution_refunds_per_year($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ''){
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%Y') as year ",
			)
		);
	 
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
			
		}
		$this->db->where($this->dx('type').' IN (21,22,23,24) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "21" OR '.$this->dx('type').' = "22" OR '.$this->dx('type').' = "23" OR '.$this->dx('type').' = "24" )',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('withdrawal_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('withdrawal_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->group_by(
			array(
				'year'
			)
		);
		return $this->db->get('withdrawals')->result();
	}

	function get_group_member_total_contribution_refunds_per_contribution_array($group_id = 0,$group_member_id = 0,$to = 0,$from = 0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		if($group_member_id){
			foreach($contribution_options as $contribution_id => $contribution_name){
				$arr[$group_member_id][$contribution_id] = 0;
			}
		}else{
			foreach ($this->group_member_options as $member_id => $name){
				foreach($contribution_options as $contribution_id => $contribution_name){
					$arr[$member_id][$contribution_id] = 0;
				}
			}
		}
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('member_id').' as member_id',
				$this->dx('contribution_id').' as contribution_id',
				)
		);
		 
		if($group_member_id){
			$this->db->where($this->dx('member_id').' = "'.$group_member_id.'"',NULL,FALSE);
		}

		$this->db->where($this->dx('type').' IN (21,22,23,24) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "21" OR '.$this->dx('type').' = "22" OR '.$this->dx('type').' = "23" OR '.$this->dx('type').' = "24" )',NULL,FALSE);
		if($to){
            $this->db->where($this->dx("withdrawal_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("withdrawal_date") . " >= " .$from, NULL, FALSE);
        }
		$this->db->group_by(array($this->dx("member_id"),$this->dx("contribution_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$withdrawals = $this->db->get('withdrawals')->result();
		foreach($withdrawals as $withdrawal){
			$arr[$withdrawal->member_id][$withdrawal->contribution_id] = $withdrawal->amount;
		}
		return $arr;
	}

	function get_group_total_contribution_refunds_per_contribution_array($group_id = 0,$to = 0,$from = 0,$contribution_id_list = ""){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		foreach($contribution_options as $contribution_id => $contribution_name){
			$arr[$contribution_id] = 0;
		}
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('contribution_id').' as contribution_id',
			)
		);
		 

		$this->db->where($this->dx('type').' IN (21,22,23,24) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "21" OR '.$this->dx('type').' = "22" OR '.$this->dx('type').' = "23" OR '.$this->dx('type').' = "24" )',NULL,FALSE);
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id')." IN (".$contribution_id_list.") ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('contribution_id')." IN (0) ",NULL,FALSE);
		}
		if($to){
            $this->db->where($this->dx("withdrawal_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("withdrawal_date") . " >= " .$from, NULL, FALSE);
        }
		$this->db->group_by(array($this->dx("contribution_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$withdrawals = $this->db->get('withdrawals')->result();
		foreach($withdrawals as $withdrawal){
			$arr[$withdrawal->contribution_id] = $withdrawal->amount;
		}
		return $arr;
	}

	function get_group_total_contribution_refunds_per_contribution_per_year_array($group_id = 0,$start_year = 2000,$end_year = 2039){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		foreach($contribution_options as $contribution_id => $contribution_name){
			for($i = $start_year; $i < $end_year; $i++):
				$arr[$contribution_id][$i] = 0;
			endfor;
		}
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as amount',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%Y') as year ",
				$this->dx('contribution_id').' as contribution_id',
			)
		);
		 

		$this->db->where($this->dx('type').' IN (21,22,23,24) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "21" OR '.$this->dx('type').' = "22" OR '.$this->dx('type').' = "23" OR '.$this->dx('type').' = "24" )',NULL,FALSE);
	
		$this->db->group_by(
			array(
				$this->dx("contribution_id"),
				"year",
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$withdrawals = $this->db->get('withdrawals')->result();
		foreach($withdrawals as $withdrawal){
			$arr[$withdrawal->contribution_id][$withdrawal->year] = $withdrawal->amount;
		}
		return $arr;
	}

	function get_group_total_contribution_refunds_per_member_array($group_id = 0,$group_member_id = 0,$to = 0,$from = 0,$group_member_options=array()){
		$arr = array();
		if($group_member_options){

		}elseif(isset($this->group_member_options)){
			$group_member_options = $this->group_member_options;
		}
		foreach ($group_member_options as $member_id => $name){
			$arr[$member_id] = 0;
		}
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('member_id').' as member_id',
			)
		);
		 
		if($group_member_id){
			$this->db->where($this->dx('member_id').' = "'.$group_member_id.'"',NULL,FALSE);
		}

		$this->db->where($this->dx('type').' IN (21,22,23,24) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "21" OR '.$this->dx('type').' = "22" OR '.$this->dx('type').' = "23" OR '.$this->dx('type').' = "24" )',NULL,FALSE);
		if($to){
            $this->db->where($this->dx("withdrawal_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("withdrawal_date") . " >= " .$from, NULL, FALSE);
        }
		$this->db->group_by(array($this->dx("member_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$withdrawals = $this->db->get('withdrawals')->result();
		foreach($withdrawals as $withdrawal){
			$arr[$withdrawal->member_id] = $withdrawal->amount;
		}
		return $arr;
	}

	function get_group_loans_disbursment($group_id=0,$date_from=0,$date_to=0){
		$members = $this->group_member_options;
		$amount=array();
		foreach($members as $member_id => $member_name){
				$this->db->select('sum('.$this->dx('amount').') as amount_paid');
				 
				$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
				$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
				if($date_from&&$date_to){
					$this->db->where($this->dx('withdrawal_date').'>="'.$date_from.'"',NULL,FALSE);
					$this->db->where($this->dx('withdrawal_date').'<="'.$date_to.'"',NULL,FALSE);
				}
				$this->db->where($this->dx('type').' IN (9,10,11,12) ',NULL,FALSE);
				$this->db->limit(1);
				//$this->db->where('('.$this->dx('type').' = "9" OR '.$this->dx('type').' = "10" OR '.$this->dx('type').' = "11" OR '.$this->dx('type').' = "12" )',NULL,FALSE);
				$amount[$member_id] = $this->db->get('withdrawals')->row()->amount_paid?:0;
			}
		return $amount;
	}

	function get_group_total_loan_disbursed_amount($group_id=0,$date_from=0,$date_to=0){
		$this->db->select('sum('.$this->dx('amount').') as amount_paid');
		 
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' >= "1"',NULL,FALSE);
		if($date_from&&$date_to){
			$this->db->where($this->dx('withdrawal_date').'>="'.$date_from.'"',NULL,FALSE);
			$this->db->where($this->dx('withdrawal_date').'<="'.$date_to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (9,10,11,12) ',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('withdrawals')->row()->amount_paid?:0;
	}

	function get_group_total_debtor_loan_disbursed_amount($group_id=0,$date_from=0,$date_to=0){
		$this->db->select('sum('.$this->dx('amount').') as amount_paid');
	 
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('debtor_id').' >= "1"',NULL,FALSE);
		if($date_from&&$date_to){
			$this->db->where($this->dx('withdrawal_date').'>="'.$date_from.'"',NULL,FALSE);
			$this->db->where($this->dx('withdrawal_date').'<="'.$date_to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (33,34,35,36) ',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('withdrawals')->row()->amount_paid?:0;
	}


	function get_group_debtor_loan_disbursements($group_id=0,$date_from=0,$date_to=0){
		$this->db->select(array(
			'sum('.$this->dx('amount').') as amount_paid',
			$this->dx('debtor_id').' as debtor_id',
		));
		 
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('debtor_id').' >= "1"',NULL,FALSE);
		if($date_from&&$date_to){
			$this->db->where($this->dx('withdrawal_date').'>="'.$date_from.'"',NULL,FALSE);
			$this->db->where($this->dx('withdrawal_date').'<="'.$date_to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (33,34,35,36) ',NULL,FALSE);
		$this->db->group_by(array(
			$this->dx('debtor_id'),
		));
		$results = $this->db->get('withdrawals')->result();
		$arr = array();
		if($results){
			foreach ($results as $result) {
				$arr[$result->debtor_id] = $result->amount_paid;
			}
		}
		return $arr;
	}

	function get_group_member_withdrawals($member_id = 0,$group_id = 0){
		$this->select_all_secure('withdrawals');
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('withdrawals')->result();
	}

	function group_total_withdrawals($group_id=0)
	{
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('withdrawal_date').'>="'.strtotime('31st Dec 2015').'"',NULL,FALSE);
		return $this->db->count_all_results('withdrawals')?:0;
	}

	function count_from_date($from=0,$to=0){
		if($from){
			$this->db->where($this->dx('withdrawal_date').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('withdrawal_date').' <= "'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->count_all_results('withdrawals');
	}

	function value_of_deposits_where($from=0,$to=0){
		$this->db->select(array("sum(".$this->dx("amount").") as total"));
		if($from){
			$this->db->where($this->dx('withdrawal_date').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('withdrawal_date').' <= "'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('withdrawals')->row()->total;
	}

	function count_withdrawals_based_on_options($options=''){
		$this->db->where($this->dx('type')." IN (".$options.")",NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		return $this->db->count_all_results('withdrawals')?:0;
	}

	function get_group_member_total_back_dated_contribution_refunds_per_contribution_array($group_id = 0,$to = 0,$from = 0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		foreach ($this->group_member_options as $member_id => $name){
			foreach($contribution_options as $contribution_id => $contribution_name){
				$arr[$member_id][$contribution_id] = 0;
			}
		}
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('member_id').' as member_id',
				$this->dx('contribution_id').' as contribution_id',
				)
		);
		 
		$this->db->where($this->dx('type').' IN (21,22,23,24) ',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "7" )',NULL,FALSE);
		$this->db->group_by(array($this->dx("member_id"),$this->dx("contribution_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$withdrawals = $this->db->get('withdrawals')->result();
		foreach($withdrawals as $withdrawal){
			$arr[$withdrawal->member_id][$withdrawal->contribution_id] = $withdrawal->amount;
		}
		return $arr;
	}

	function get_group_back_dating_contribution_refunds(){
		$this->select_all_secure('withdrawals');
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (21,22,23,24) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1"',NULL,FALSE);
		return $this->db->get('withdrawals')->result();
	}

	function get_group_total_back_dated_expenses_per_expense_category_array(){
		$expense_category_options = $this->expense_categories_m->get_group_expense_category_options();
		$arr = array();
		foreach($expense_category_options as $expense_category_id => $expense_category_name):
			$arr[$expense_category_id] = 0;
		endforeach;
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('expense_category_id').' as expense_category_id',
			)
		);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (1,2,3,4) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1"',NULL,FALSE);
		$this->db->group_by(
        	array(
        		$this->dx('expense_category_id'),
        	)
        );
		$expenses = $this->db->get('withdrawals')->result();
		foreach($expenses as $expense):
			$arr[$expense->expense_category_id] = $expense->amount;
		endforeach;
		return $arr;
	}

	function get_group_back_dating_expenses(){
		$this->select_all_secure('withdrawals');
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (1,2,3,4) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1"',NULL,FALSE);
		return $this->db->get('withdrawals')->result();
	}

	function get_group_member_total_back_dated_loans_borrowed_array(){
		$arr = array();
		foreach ($this->group_member_options as $member_id => $name){
			$arr[$member_id] = 0;
		}
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('member_id').' as member_id',
			)
		);
		$this->db->where($this->dx('type').' IN (9,10,11,12) ',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1"',NULL,FALSE);
		$this->db->group_by(
        	array(
        		$this->dx('member_id'),
        	)
        );
        $withdrawals = $this->db->get('withdrawals')->result();
        foreach($withdrawals as $withdrawal):
        	$arr[$withdrawal->member_id] = $withdrawal->amount;
        endforeach;
        return $arr;
	}

	function get_group_back_dating_loans_borrowed(){
		$this->select_all_secure('withdrawals');
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (9,10,11,12) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1"',NULL,FALSE);
		return $this->db->get('withdrawals')->result();
	}

	function get_group_withdrawal_by_transaction_alert_id($transaction_alert_id = 0,$group_id = 0){
		$this->select_all_secure('withdrawals');
		$this->db->where($this->dx('transaction_alert_id').' = "'.$transaction_alert_id.'"',NULL,FALSE);
		 
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('withdrawals')->row();
	}

	function get_group_back_dating_bank_loan_repayments(){
		$this->select_all_secure('withdrawals');
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (25,26,27,28) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1"',NULL,FALSE);
		return $this->db->get('withdrawals')->result();
	}

	function get_group_back_dating_stock_purchases(){
		$this->select_all_secure('withdrawals');
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (13,14,15,16) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1"',NULL,FALSE);
		return $this->db->get('withdrawals')->result();
	}

	function get_group_back_dating_money_market_investments(){
		$this->select_all_secure('withdrawals');
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (17,18,19,20) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1"',NULL,FALSE);
		return $this->db->get('withdrawals')->result();
	}

	function get_group_back_dating_asset_purchase_payments(){
		$this->select_all_secure('withdrawals');
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (5,6,7,8) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1"',NULL,FALSE);
		return $this->db->get('withdrawals')->result();
	}

	function get_group_back_dated_loans_paid_amount(){
		$this->db->select(
			array(
				"sum(".$this->dx('amount').") as amount"
			)
		);	
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (25,26,27,28) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1"',NULL,FALSE);
		return $this->db->get('withdrawals')->row()->amount;
	}

	function get_group_back_dating_asset_purchase_objects_array($group_id = 0){
		$this->select_all_secure('withdrawals');
		 
		$this->db->where($this->dx('type').' IN (5,6,7,8) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		//$this->db->group_by(array($this->dx("stock_id")));
		$asset_purchases = $this->db->get('withdrawals')->result();
		$arr = array();
		foreach($asset_purchases as $asset_purchase):
			$arr[$asset_purchase->asset_id] = $asset_purchase;
		endforeach;
		return $arr;
	}


	function get_group_back_dating_outgoing_account_transfer_amounts_array($group_id=0){
		$arr = array();
		$account_options = $this->accounts_m->get_group_account_options(FALSE,FALSE);
		foreach($account_options as $account_id => $name):
			$arr[$account_id] = 0;
		endforeach;

		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('account_id')." as account_id",
			)
		);
		 
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (29,30,31,32) ',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		$this->db->group_by(array($this->dx("account_id")));
		//$this->db->where('('.$this->dx('type').' = "37" OR '.$this->dx('type').' = "38" OR '.$this->dx('type').' = "39" OR '.$this->dx('type').' = "40" )',NULL,FALSE);
		$account_transfers = $this->db->get('withdrawals')->result();
		foreach ($account_transfers as $account_transfer) {
			# code...
			$arr[$account_transfer->account_id] = $account_transfer->amount;
		}
		return $arr;
	}

	function update_group_back_dating_account_transfers_cut_off_date($group_id = 0,$input = array()){
		$where = " ".$this->dx('is_a_back_dating_record')." = '1' AND ".$this->dx('group_id')." = '".$group_id."' AND ".$this->dx('active')." = '1' ;";
        if($this->update_account_transfers_where($where,$input)){
        	return TRUE;
        }else{
        	return FALSE;
        }
	}

	function update_group_back_dating_bank_loan_repayments_cut_off_date($group_id = 0,$input = array()){
		$where = " ".$this->dx('is_a_back_dating_record')." = '1' AND ".$this->dx('group_id')." = '".$group_id."' AND ".$this->dx('active')." = '1' ;";
        if($this->update_bank_loan_repayments_where($where,$input)){
        	return TRUE;
        }else{
        	return FALSE;
        }
	}

	function update_group_back_dating_withdrawals_cut_off_date($group_id = 0,$input = array()){
		$where = " ".$this->dx('is_a_back_dating_record')." = '1' AND ".$this->dx('group_id')." = '".$group_id."' AND ".$this->dx('active')." = '1' ;";
        if($this->update_where($where,$input)){
        	return TRUE;
        }else{
        	return FALSE;
        }
	}

    function get_group_total_bank_loan_repayments_per_bank_loan_array(){
    	$arr = array();
    	$bank_loan_options = $this->bank_loans_m->get_group_bank_loan_options();
    	foreach($bank_loan_options as $bank_loan_id => $description):
    		$arr[$bank_loan_id] = 0;
    	endforeach;
    	$this->db->select(
    		array(
    			'sum('.$this->dx('amount').') as amount_paid',
    			$this->dx('bank_loan_id').' as bank_loan_id '
    		)
    	);
    	$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (25,26,27,28) ',NULL,FALSE);
		$this->db->group_by(array($this->dx("bank_loan_id")));
		$bank_loan_repayments = $this->db->get('withdrawals')->result();
		foreach($bank_loan_repayments as $bank_loan_repayment):
			$arr[$bank_loan_repayment->bank_loan_id] = $bank_loan_repayment->amount_paid;
		endforeach;
		return $arr;
    }

    function get_total_withdrawals_amount($date= '',$paying_group_ids = array()){
    	$this->db->select(
            array(
                'sum('.$this->dx('amount  ').') as amount',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%c') as month ",
        ));
        if($date){
			$this->db->where($this->dx('created_on').' >= "'.$date.'"',NULL,FALSE);
		}
		if(empty($paying_group_ids)){
			$this->db->where($this->dx('group_id ').' IN ( 0 ) ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id ').' IN ('.implode(',',$paying_group_ids).') ',NULL,FALSE);
		}

        $this->db->where($this->dx('active')."= '1'",NULL,FALSE);
        return $this->db->get('withdrawals')->row();
    }

    function get_total_withdrawals_by_month_array_tests($from = 0 ,$to ,$paying_group_ids){
		$arr = array();
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%c') as month "
			)
		);
		$this->db->where($this->dx('created_on')." >= ".$from,NULL,FALSE);
        $this->db->where($this->dx('created_on')." <= ".$to,NULL,FALSE);
        $this->db->where($this->dx('active')."= '1'",NULL,FALSE);
		if(empty($paying_group_ids)){
			$this->db->where($this->dx('group_id ').' IN ( 0 ) ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id ').' IN ('.implode(',',$paying_group_ids).') ',NULL,FALSE);
		}
        $this->db->group_by(
        	array(
        		'year',
        		'month'
        	)
        );
		$result = $this->db->get('withdrawals')->result();
		foreach($result as $row){
			$arr[$row->year][$row->month] = $row->amount;
		}
		return $arr;
	}

	function get_total_withdrawals_of_that_month($from ='' ,$to ='' ,$paying_group_ids = array()){
		$arr = array();
		$this->db->select(
			array(
				$this->dx('amount').' as amount',
				$this->dx('group_id').' as group_id',
				$this->dx('member_id  ').' as member_id',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%c') as month "
			)
		);
		$this->db->where($this->dx('created_on')." >= ".$from,NULL,FALSE);
        $this->db->where($this->dx('created_on')." <= ".$to,NULL,FALSE);
        $this->db->where($this->dx('active')."= '1'",NULL,FALSE);
		if(empty($paying_group_ids)){
			$this->db->where($this->dx('group_id ').' IN ( 0 ) ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id ').' IN ('.implode(',',$paying_group_ids).') ',NULL,FALSE);
		}
		$result = $this->db->get('withdrawals')->result();
		foreach($result as $row){
			$arr[$row->year][$row->month] = $row->amount;
		}
		return $result;

	}


    /************************Withdrawak requests***************************/

    function insert_withdrawal_request($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('withdrawal_requests',$input);
	}

	function insert_withdrawal_approval_request($input = array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('withdrawal_approval_requests',$input);
    }

    function batch_insert_withdrawal_approval_request($input = array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_chunked_batch_secure_data('withdrawal_approval_requests',$input);
    }

    function get_withdrawal_request($id=0){
    	$this->select_all_secure('withdrawal_requests');
    	$this->db->where('id',$id);
    	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
    	$this->db->limit(1);
    	return $this->db->get('withdrawal_requests')->row();
    }
	function get_total_disbursed_withdrawal_request(){
		$this->db->select('sum('.$this->dx('amount').') as amount');
    	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
    	$this->db->limit(1);
    	return $this->db->get('withdrawal_requests')->row();
    }


    function get_group_withdrawal_request($id = 0,$group_id = 0){
    	$this->select_all_secure('withdrawal_requests');
    	$this->db->where('id',$id);
    	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		 
    	$this->db->limit(1);
    	return $this->db->get('withdrawal_requests')->row();
    }

    function get_group_withdrawal_request_approval_requests($id = 0,$group_id = 0){
    	$this->select_all_secure('withdrawal_approval_requests');
    	$this->db->where($this->dx('withdrawal_request_id')." = '".$id."' ",NULL,FALSE);
    	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
    	if($group_id){
    		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
    	}else{
    		$this->db->where($this->dx('group_id')." = '".$this->group->id."' ",NULL,FALSE);
    	}
    	return $this->db->get('withdrawal_approval_requests')->result();
    }

    function get_group_withdrawal_approval_request($id = 0,$group_id = 0){
    	$this->select_all_secure('withdrawal_approval_requests');
    	$this->db->where('id',$id);
    	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
    	 
		return $this->db->get('withdrawal_approval_requests')->row();
    }


    function get_group_member_withdrawal_approval_request_by_member_id($id=0,$member_id=0){
    	$this->select_all_secure('withdrawal_approval_requests');
    	$this->db->where($this->dx('withdrawal_request_id').' = "'.$id.'"',NULL,FALSE);
    	if($member_id){
    		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
    	}else{
    		$this->db->where($this->dx('member_id').' = "'.$this->member->id.'"',NULL,FALSE);
    	}
    	return $this->db->get('withdrawal_approval_requests')->row();
    }

    function count_group_withdrawal_requests($status = "",$group_id=0,$member_ids=array()){
    	if(is_array($status)){
    		if(count($status)==1){
    			$status = $status[0];
    			if($status ==1){
		    		$this->db->where($this->dx('status')." ='0'",NULL,FALSE);
		    	}else if($status ==2){
		    		$this->db->where($this->dx('status')." = '1' ",NULL,FALSE);
		    		$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
		    	}else if($status ==3){
		    		$this->db->where($this->dx('status')." = '1' ",NULL,FALSE);
		    		$this->db->where($this->dx('is_declined')." = '1' ",NULL,FALSE);
		    	}elseif($status == 14){
			    	$this->db->where($this->dx('status')." = '1' ",NULL,FALSE);
			    	$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
			    	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
			    	$this->db->where('('.$this->dx('is_disbursed')." IS NULL OR ".$this->dx('is_disbursed')." = '' )",NULL,FALSE);
			    	$this->db->where('('.$this->dx('is_disbursement_declined')." IS NULL OR ".$this->dx('is_disbursement_declined')." = '' )",NULL,FALSE);
		    	}elseif($status==15){
		    		$this->db->where($this->dx('status')." = '2' ",NULL,FALSE);
    				$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
    				$this->db->where($this->dx('is_disbursed')." = '1' ",NULL,FALSE);
		    	}elseif($status==16){
		    		$this->db->where($this->dx('status')." = '2' ",NULL,FALSE);
			    	$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
			    	$this->db->where($this->dx('is_disbursement_declined')." = '1' ",NULL,FALSE);
		    	}
    		}else{
    			if(in_array(1, $status)){
					$this->db->where($this->dx('status').'IN(0,1)',NULL,FALSE);
					if(in_array(2, $status)&&in_array(3, $status)){
						
					}elseif(in_array(2, $status)){
						$this->db->where($this->dx('is_approved').' IN (0,1)',NULL,FALSE);
						$this->db->where($this->dx('is_declined').' < "1"',NULL,FALSE);
					}elseif (in_array(3, $status)) {
						$this->db->where($this->dx('is_declined').' IN (0,1)',NULL,FALSE);
						$this->db->where($this->dx('is_approved').' < "1"',NULL,FALSE);
					}
				}elseif(in_array(14, $status)){
					$this->db->where($this->dx('status').'IN(1,2)',NULL,FALSE);
			    	$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
			    	if(in_array(15, $status) && in_array(6, $status)){

			    	}elseif (in_array(15, $status)) {
			    		$this->db->where($this->dx('is_disbursed')." = '1' ",NULL,FALSE);
			    	}elseif (in_array(16, $status)) {
			    		$this->db->where($this->dx('is_disbursement_declined')." = '1' ",NULL,FALSE);
			    	}
				}else{
					if(in_array(15, $status) && in_array(16, $status)){
						$this->db->where($this->dx('status').'="2"',NULL,FALSE);
			    	}else{
			    		$this->db->where($this->dx('status').'="1"',NULL,FALSE);
			    	}
				}
    		}
    	}else{
    		if($status == 'pending'){
	    		$this->db->where($this->dx('status')." ='0'",NULL,FALSE);
	    	}else if($status == 'approved'){
	    		$this->db->where($this->dx('status')." = '1' ",NULL,FALSE);
	    		$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
	    	}else if($status == 'declined'){
	    		$this->db->where($this->dx('status')." = '1' ",NULL,FALSE);
	    		$this->db->where($this->dx('is_declined')." = '1' ",NULL,FALSE);
	    	}
    	}
    	$member_list='';
    	if($member_ids){
    		if(is_array($member_ids)){
    			foreach ($member_ids as $key => $member_id) {
    				if($member_list){
    					$member_list.=','.$member_id;
    				}else{
    					$member_list=$member_id;
    				}
    			}
    		}
    	}
    	if($member_list){
    		$this->db->where($this->dx('member_id').' IN('.$member_list.')',NULL,FALSE);
    	}
    	 
    	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
    	return $this->db->count_all_results('withdrawal_requests');
    }
    function get_all_withdrawal_requests(){
    	$this->select_all_secure('withdrawal_requests');
    	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
    	return $this->db->get('withdrawal_requests')->result();
    }
    function get_group_withdrawal_requests($status = "",$group_id=0,$member_ids= array(),$sort_by='created_on',$sort_order='DESC'){
    	$this->select_all_secure('withdrawal_requests');
    	if(is_array($status)){
    		if(count($status)==1){
    			$status = $status[0];
    			if($status ==1){
		    		$this->db->where($this->dx('status')." ='0'",NULL,FALSE);
		    	}else if($status ==2){
		    		$this->db->where($this->dx('status')." = '1' ",NULL,FALSE);
		    		$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
		    	}else if($status ==3){
		    		$this->db->where($this->dx('status')." = '1' ",NULL,FALSE);
		    		$this->db->where($this->dx('is_declined')." = '1' ",NULL,FALSE);
		    	}elseif($status == 14){
			    	$this->db->where($this->dx('status')." = '1' ",NULL,FALSE);
			    	$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
			    	$this->db->where('('.$this->dx('is_disbursed')." IS NULL OR ".$this->dx('is_disbursed')." = '' )",NULL,FALSE);
			    	$this->db->where('('.$this->dx('is_disbursement_declined')." IS NULL OR ".$this->dx('is_disbursement_declined')." = '' )",NULL,FALSE);
		    	}elseif($status==15){
		    		$this->db->where($this->dx('status')." = '2' ",NULL,FALSE);
    				$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
    				$this->db->where($this->dx('is_disbursed')." = '1' ",NULL,FALSE);
		    	}elseif($status==16){
		    		$this->db->where($this->dx('status')." = '2' ",NULL,FALSE);
			    	$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
			    	$this->db->where($this->dx('is_disbursement_declined')." = '1' ",NULL,FALSE);
		    	}
    		}else{
    			if(in_array(1, $status)){
					$this->db->where($this->dx('status').'IN(0,1)',NULL,FALSE);
					if(in_array(2, $status)&&in_array(3, $status)){
						
					}elseif(in_array(2, $status)){
						$this->db->where($this->dx('is_approved').' IN (0,1)',NULL,FALSE);
						$this->db->where($this->dx('is_declined').' < "1"',NULL,FALSE);
					}elseif (in_array(3, $status)) {
						$this->db->where($this->dx('is_declined').' IN (0,1)',NULL,FALSE);
						$this->db->where($this->dx('is_approved').' < "1"',NULL,FALSE);
					}
				}elseif(in_array(14, $status)){
					$this->db->where($this->dx('status').'IN(1,2)',NULL,FALSE);
			    	$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
			    	if(in_array(15, $status) && in_array(16, $status)){

			    	}elseif (in_array(15, $status)) {
			    		$this->db->where($this->dx('is_disbursed')." = '1' ",NULL,FALSE);
			    	}elseif (in_array(16, $status)) {
			    		$this->db->where($this->dx('is_disbursement_declined')." = '1' ",NULL,FALSE);
			    	}
				}else{
					if(in_array(15, $status) && in_array(16, $status)){
						$this->db->where($this->dx('status').'="2"',NULL,FALSE);
			    	}else{
			    		$this->db->where($this->dx('status').'="1"',NULL,FALSE);
			    	}
				}
    		}
    	}else{
			 
    		if($status == 'pending'){
	    		$this->db->where($this->dx('is_approved')." ='0'",NULL,FALSE);
	    		$this->db->where($this->dx('is_declined')." ='0'",NULL,FALSE);
	    		$this->db->where($this->dx('status')." ='0'",NULL,FALSE);
	    	}else if($status == 'approved'){
	    		$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
	    	}else if($status == 'declined'){
	    		$this->db->where($this->dx('is_declined')." = '1' ",NULL,FALSE);
	    	}
    	}
    	$member_list='';
    	if($member_ids){
    		if(is_array($member_ids)){
    			foreach ($member_ids as $key => $member_id) {
    				if($member_list){
    					$member_list.=','.$member_id;
    				}else{
    					$member_list=$member_id;
    				}
    			}
    		}
    	}
    	if($member_list){
    		$this->db->where($this->dx('member_id').' IN('.$member_list.')',NULL,FALSE);
    	}
    	
    	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
    	if($sort_order){
    		if ($sort_by == 'created_on') {
    			$this->db->order_by($this->dx('request_date'),"DESC",FALSE);
    			$this->db->order_by($this->dx('created_on'),"DESC",FALSE);
    		}else{
    			$this->db->order_by($this->dx($sort_by),$sort_order,FALSE);
    		}
    	}else{
    		$this->db->order_by($this->dx('request_date'),"DESC",FALSE);
    		$this->db->order_by($this->dx('created_on'),"DESC",FALSE);
    	}
    	return $this->db->get('withdrawal_requests')->result();
    }

    function get_withdrawal_approval_requests_array($withdrawal_requests = array()){
    	if($withdrawal_requests){
    		$withdrawal_request_ids = array();
    		foreach($withdrawal_requests as $withdrawal_request):
    			$withdrawal_request_ids[] = $withdrawal_request->id;
    		endforeach;
    		$arr = array();
    		$this->db->select(
    			array(
    				'id',
    				$this->dx('member_id')." as member_id ",
    				$this->dx('status')." as status ",
    				$this->dx('is_approved')." as is_approved ",
    				$this->dx('is_declined')." as is_declined ",
    				$this->dx('comments')." as comments ",
    				$this->dx('withdrawal_request_id')." as withdrawal_request_id ",
    			)
    		);
    		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
    		if($withdrawal_request_ids){
    			$this->db->where($this->dx('withdrawal_request_id')." IN(".implode(',',$withdrawal_request_ids).") ",NULL,FALSE);
    		}else{
    			$this->db->where($this->dx('withdrawal_request_id')." IN(0) ",NULL,FALSE);
    		}
    		$withdrawal_approval_requests = $this->db->get('withdrawal_approval_requests')->result();
    		foreach($withdrawal_approval_requests as $withdrawal_approval_request):
    			$arr[$withdrawal_approval_request->withdrawal_request_id][$withdrawal_approval_request->member_id] = array(
    				'id' => $withdrawal_approval_request->id,
    				'status' => $withdrawal_approval_request->status,
    				'member_id' => $withdrawal_approval_request->member_id,
    				'is_approved' => $withdrawal_approval_request->is_approved,
    				'is_declined' => $withdrawal_approval_request->is_declined,
    				'comments' => $withdrawal_approval_request->comments,
    			);
    		endforeach;
    		return $arr;
    	}else{
    		return FALSE;
    	}
    }


    function update_withdrawal_request($id = 0,$input = array(),$SKIP_VALIDATION = FALSE){
		return $this->update_secure_data($id,'withdrawal_requests',$input);
    }

    function update_withdrawal_approval_request($id = 0,$input = array(),$SKIP_VALIDATION = FALSE){
		return $this->update_secure_data($id,'withdrawal_approval_requests',$input);
    }

    function count_approved_withdrawal_requests_pending_disbursement(){
    	$this->db->where($this->dx('status')." = '1' ",NULL,FALSE);
    	$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
    	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
    	$this->db->where('('.$this->dx('is_disbursed')." IS NULL OR ".$this->dx('is_disbursed')." = '' OR ".$this->dx('is_disbursed')." = '0' )",NULL,FALSE);
    	$this->db->where('('.$this->dx('is_disbursement_declined')." IS NULL OR ".$this->dx('is_disbursement_declined')." = '' )",NULL,FALSE);
    	return $this->db->count_all_results('withdrawal_requests');
    }

    function get_approved_withdrawal_requests_pending_disbursement($limit = 0){
    	$this->select_all_secure('withdrawal_requests');
    	$this->db->where($this->dx('status')." = '1' ",NULL,FALSE);
    	$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
    	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
    	$this->db->where('('.$this->dx('is_disbursed')." IS NULL OR ".$this->dx('is_disbursed')." = '' OR ".$this->dx('is_disbursed')." = '0' )",NULL,FALSE);
    	$this->db->where('('.$this->dx('is_disbursement_declined')." IS NULL OR ".$this->dx('is_disbursement_declined')." = '' )",NULL,FALSE);
    	$this->db->order_by($this->dx('approved_on'),'ASC',FALSE);
    	if($limit){
    		$this->db->limit($limit);
    	}
    	return $this->db->get('withdrawal_requests')->result();
    }

    function get_pending_withdrawal_request($id = 0){
    	$this->select_all_secure('withdrawal_requests');
    	$this->db->where($this->dx('status')." = '1' ",NULL,FALSE);
    	$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
    	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
    	$this->db->where('('.$this->dx('is_disbursed')." IS NULL OR ".$this->dx('is_disbursed')." = '' OR ".$this->dx('is_disbursed')." = '0' )",NULL,FALSE);
    	$this->db->where('('.$this->dx('is_disbursement_declined')." IS NULL OR ".$this->dx('is_disbursement_declined')." = '' )",NULL,FALSE);
    	$this->db->where('id',$id);
    	return $this->db->get('withdrawal_requests')->row();
    }

    function count_declined_withdrawal_requests(){
    	$this->db->where($this->dx('status')." = '2' ",NULL,FALSE);
    	$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
    	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
    	$this->db->where($this->dx('is_disbursement_declined')." = '1' ",NULL,FALSE);
    	return $this->db->count_all_results('withdrawal_requests');
    }

    function get_declined_withdrawal_requests($limit = 0){
    	$this->select_all_secure('withdrawal_requests');
    	$this->db->where($this->dx('status')." = '2' ",NULL,FALSE);
    	$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
    	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
    	$this->db->where($this->dx('is_disbursement_declined')." = '1' ",NULL,FALSE);
    	$this->db->order_by($this->dx('declined_on'),'DESC',FALSE);
    	if($limit){
    		$this->db->limit($limit);
    	}
    	return $this->db->get('withdrawal_requests')->result();
    }

    function count_disbursed_withdrawal_requests(){
    	$this->db->where($this->dx('status')." = '3' ",NULL,FALSE);
    	$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
    	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
    	$this->db->where($this->dx('is_disbursed')." = '1' ",NULL,FALSE);
    	return $this->db->count_all_results('withdrawal_requests');
    }
    
    function get_disbursed_withdrawal_requests($limit = 0){
    	$this->select_all_secure('withdrawal_requests');
    	$this->db->where($this->dx('status')." = '3' ",NULL,FALSE);
    	$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
    	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
    	$this->db->where($this->dx('is_disbursed')." = '1' ",NULL,FALSE);
    	$this->db->order_by($this->dx('disbursed_on'),'DESC',FALSE);
    	if($limit){
    		$this->db->limit($limit);
    	}
    	return $this->db->get('withdrawal_requests')->result();
    }
	function get_disbursed_withdrawal_requests_total_amount($limit = 0){
		$this->db->select('sum('.$this->dx('amount').') as amount');
    	$this->db->where($this->dx('status')." = '3' ",NULL,FALSE);
    	$this->db->where($this->dx('is_approved')." = '1' ",NULL,FALSE);
    	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
    	$this->db->where($this->dx('is_disbursed')." = '1' ",NULL,FALSE);
    	$this->db->order_by($this->dx('disbursed_on'),'DESC',FALSE);
    	$result = $this->db->get('withdrawal_requests')->row();
		if($result){
			$amount = $result->amount;
		}
		return $amount;
    }

    function count_group_member_pending_withdrawal_approval_requests($member_id = 0){
    	if($member_id){
    		$this->db->where($this->dx('withdrawal_approval_requests.member_id')." = '".$member_id."' ",NULL,FALSE);
    	}else{
    		$this->db->where($this->dx('withdrawal_approval_requests.member_id')." = '".$this->member->id."' ",NULL,FALSE);
    	}
    	$this->db->where($this->dx('withdrawal_approval_requests.status')." = '0' ",NULL,FALSE);
    	$this->db->where($this->dx('withdrawal_approval_requests.active')." = '1' ",NULL,FALSE);
    	$this->db->where($this->dx('withdrawal_requests.status')." = '0' ",NULL,FALSE);
    	$this->db->where($this->dx('withdrawal_approval_requests.group_id')." = '".$this->group->id."' ",NULL,FALSE);
    	$this->db->join('withdrawal_requests',$this->dx('withdrawal_approval_requests.withdrawal_request_id').' = withdrawal_requests.id');
    	return $this->db->count_all_results('withdrawal_approval_requests');
    }

    function count_group_pending_withdrawal_approval_requests($group_id = 0){
    	if($group_id){
    		$this->db->where($this->dx('withdrawal_requests.group_id')." = '".$group_id."' ",NULL,FALSE);
    	}else{
    		$this->db->where($this->dx('withdrawal_requests.group_id')." = '".$this->group->id."' ",NULL,FALSE);
    	}
    	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
    	$this->db->where($this->dx('withdrawal_requests.is_approved')." = '0' ",NULL,FALSE);
    	$this->db->where($this->dx('withdrawal_requests.is_declined')." = '0' ",NULL,FALSE);
    	return $this->db->count_all_results('withdrawal_requests');
    }

    function get_group_member_withdrawal_approval_request($id = 0){
    	$this->select_all_secure('withdrawal_approval_requests');
    	$this->db->where('id',$id);
    	$this->db->limit(1);
    	return $this->db->get('withdrawal_approval_requests')->row();
    }

    function get_group_undisbursed_approved_withdrawal_requests($group_id=''){
		$this->select_all_secure('withdrawal_requests');
		$this->db->where($this->dx('is_disbursed').' = "0"',NULL,FALSE);
		$this->db->where($this->dx('status').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_approved').' ="1"',NULL,FALSE);
		$this->db->where($this->dx('active').' ="1"',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('withdrawal_requests')->result();
	}

	function get_group_disbursement_failed_withdrawal_requests($group_id=''){
		$this->select_all_secure('withdrawal_requests');
		//$this->db->where($this->dx('is_disbursed').' = "0" OR '.$this->dx('is_disbursed').' IS NULL',NULL,FALSE);
		$this->db->where($this->dx('is_disbursement_declined').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('status').' = "2"',NULL,FALSE);
		$this->db->where($this->dx('is_approved').' ="1"',NULL,FALSE);
		$this->db->where($this->dx('active').' ="1"',NULL,FALSE);
		
    	$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('withdrawal_requests')->result();
	}

	function get_group_disbursed_withdrawal_requests($group_id=''){
		$this->select_all_secure('withdrawal_requests');
		$this->db->where($this->dx('is_disbursed').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('status').' = "3"',NULL,FALSE);
		$this->db->where($this->dx('is_approved').' ="1"',NULL,FALSE);
		$this->db->where($this->dx('active').' ="1"',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('withdrawal_requests')->result();
	}

    function get_undisbursed_approved_withdrawal_requests($limit=1){
		$this->select_all_secure('withdrawal_requests');
		$this->db->where($this->dx('is_disbursed').' = "0"',NULL,FALSE);
		$this->db->where($this->dx('status').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_approved').' ="1"',NULL,FALSE);
		$this->db->where($this->dx('active').' ="1"',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		$this->db->limit($limit);
		return $this->db->get('withdrawal_requests')->result();
	}

	function get_undisbursed_approved_withdrawal_requests_past_expiry($limit=50){
		$this->select_all_secure('withdrawal_requests');
		$this->db->where('('.$this->dx('is_disbursed')." IS NULL OR ".$this->dx('is_disbursed')." = '' OR ".$this->dx('is_disbursed')." = '0' )",NULL,FALSE);
		$this->db->where('('.$this->dx('is_disbursement_declined')." IS NULL OR ".$this->dx('is_disbursement_declined')." = '' )",NULL,FALSE);
		$this->db->where('('.$this->dx('is_declined').' IS NULL OR '.$this->dx('is_declined').' ="0")',NULL,FALSE);
		$this->db->where('('.$this->dx('is_approved').' IS NULL OR '.$this->dx('is_approved').' ="0")',NULL,FALSE);
		$this->db->limit($limit);
		$this->db->where($this->dx('expiry_time').' <= "'.time().'"',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('withdrawal_requests')->result();
	}

	function withdrawal_request_reference_number_already_used($reference_number = 0){
		$this->db->where($this->dx('reference_number').' = "'.$reference_number.'"',NULL,FALSE);
		return $this->db->count_all_results('withdrawal_request_reference_numbers')?TRUE:FALSE;
	}
	
	function insert_withdrawal_request_reference_number($input = array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('withdrawal_request_reference_numbers',$input);
	}

	function get_request_by_reference_number($reference_number = 0){
		$this->select_all_secure('withdrawal_requests');
		$this->db->where($this->dx('reference_number').' = "'.$reference_number.'"',NULL,FALSE);
		return $this->db->get('withdrawal_requests')->row();
	}

	function count_ongoing_disbursement(){
		$this->db->where('('.$this->dx('is_disbursed')." IS NULL OR ".$this->dx('is_disbursed')." = '0' )",NULL,FALSE);
		$this->db->where('('.$this->dx('is_disbursement_declined')." IS NULL OR ".$this->dx('is_disbursement_declined')." = '' )",NULL,FALSE);
		$this->db->where('('.$this->dx('is_declined').' IS NULL OR '.$this->dx('is_declined').' ="0")',NULL,FALSE);
		$this->db->where('('.$this->dx('is_approved').' IS NULL OR '.$this->dx('is_approved').' ="0")',NULL,FALSE);
		return $this->db->count_all_results('withdrawal_requests');
	}

	function get_ongoing_disbursement(){
		$this->select_all_secure('withdrawal_requests');
		$this->db->where('('.$this->dx('is_disbursed')." IS NULL OR ".$this->dx('is_disbursed')." = '0' )",NULL,FALSE);
		$this->db->where('('.$this->dx('is_disbursement_declined')." IS NULL OR ".$this->dx('is_disbursement_declined')." = '' )",NULL,FALSE);
		$this->db->where('('.$this->dx('is_declined').' IS NULL OR '.$this->dx('is_declined').' ="0")',NULL,FALSE);
		$this->db->where('('.$this->dx('is_approved').' IS NULL OR '.$this->dx('is_approved').' ="0")',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('withdrawal_requests')->result();
	}

	function count_declined_requests(){
		$this->db->where('('.$this->dx('is_disbursed')." IS NULL OR ".$this->dx('is_disbursed')." = '' )",NULL,FALSE);
		$this->db->where('('.$this->dx('is_disbursement_declined')." IS NULL OR ".$this->dx('is_disbursement_declined')." = '' )",NULL,FALSE);
		$this->db->where($this->dx('is_declined').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('status').' = "1"',NULL,FALSE);
		return $this->db->count_all_results('withdrawal_requests');
	}

	function get_declined_requests(){
		$this->select_all_secure('withdrawal_requests');
		$this->db->where('('.$this->dx('is_disbursed')." IS NULL OR ".$this->dx('is_disbursed')." = '' OR ".$this->dx('is_disbursed')." = '0')",NULL,FALSE);
		$this->db->where('('.$this->dx('is_disbursement_declined')." IS NULL OR ".$this->dx('is_disbursement_declined')." = '' )",NULL,FALSE);
		$this->db->where($this->dx('is_declined').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('status').' = "1"',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('withdrawal_requests')->result();
	}

    function check_missing_transaction_statements(){
        $withdrawals = $this->withdrawals_m->get_group_withdrawals();
        echo count($withdrawals);
        $transaction_statement_withdrawal_ids_array = $this->transaction_statements_m->get_group_transaction_statement_withdrawal_ids_array($this->group->id);
        foreach($withdrawals as $withdrawal):
            if(isset($transaction_statement_withdrawal_ids_array[$withdrawal->id])){

            }else{
                echo "Am in.<br/>";
            }
        endforeach;
        
    }

    function get_group_total_bank_loan_principal_paid_per_year_array($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as amount',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%Y') as year ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where("(".$this->dx('is_bank_loan_interest').' IN (0,"") OR '.$this->dx('is_bank_loan_interest').' IS NULL )',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (25,26,27,28) ',NULL,FALSE);
		$this->db->group_by(
            array(
                'year',
            )
        );
        $bank_loan_repayments = $this->db->get('withdrawals')->result();
		foreach($bank_loan_repayments as $bank_loan_repayment):
			$arr[$bank_loan_repayment->year] = $bank_loan_repayment->amount;
		endforeach;
		return $arr;
	}

	function get_group_total_asset_purchase_payments_per_month_array($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as amount',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%Y') as year ",
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%b') as month ",
			)
		);
		$this->db->where($this->dx('type').' IN (5,6,7,8) ',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->order_by($this->dx('withdrawal_date'),'ASC',FALSE);
		$this->db->group_by(
        	array(
        		'year',
        		'month',
        	)
        );
		$withdrawals = $this->db->get('withdrawals')->result();

		

		// foreach($arr as $key => $value):
		// 	$previous_month = date('M Y',strtotime('-1 month',strtotime($key)));
		// 	if(isset($arr[($previous_month)])){
		// 		$arr[$key] += $arr[($previous_month)];
		// 	}
		// endforeach;
		$first_month = date('M Y');

		foreach($withdrawals as $withdrawal){
			$first_month = $withdrawal->month.' '.$withdrawal->year;
			//$arr[$withdrawal->month.' '.$withdrawal->year] = $withdrawal->amount;
			break;
		}
		$current_month = date('M Y');
		$months_array = generate_months_array(strtotime($first_month),strtotime($current_month));

		foreach($months_array as $month):
			$arr[$month] = 0;
		endforeach;

		foreach($withdrawals as $withdrawal){
			$arr[$withdrawal->month.' '.$withdrawal->year] = $withdrawal->amount;
		}

		foreach($months_array as $month):
			if($month == $first_month){

			}else{
				$previous_month = date('M Y',strtotime('-1 month',strtotime($month)));
				if(isset($arr[($previous_month)])){
					$arr[$month] += $arr[($previous_month)];
				}
			}
		endforeach;



		//ksort($arr);

  		//$current_month = date('M Y');
  //       if(isset($withdrawal)){
  //       	$month = strtotime('+1 month',strtotime($withdrawal->month.' '.$withdrawal->year));
	 //        $amount = $arr[$withdrawal->month.' '.$withdrawal->year];
	 //        $months_array = generate_months_array(strtotime($month),strtotime($current_month));
	 //        foreach($months_array as $month):
	 //            $arr[$month] = $amount;
	 //    	endforeach;
  //       }
        
		return $arr;
	}

	function get_group_total_bank_loans_interest_paid_per_month_array($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as amount_paid ',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%Y') as year ",
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%b') as month ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_bank_loan_interest').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (25,26,27,28) ',NULL,FALSE);
		$this->db->group_by(
            array(
                'month',
                'year',
            )
        );
		$bank_loan_repayments = $this->db->get('withdrawals')->result();
		foreach($bank_loan_repayments as $bank_loan_repayment):
			$arr[$bank_loan_repayment->month.' '.$bank_loan_repayment->year] = $bank_loan_repayment->amount_paid;
		endforeach;
		return $arr;
	}

	function get_group_expense_category_totals_per_month_array($group_id = 0,$start_year = 2000,$end_year = 2039){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as amount',
				$this->dx('expense_category_id').' as expense_category_id ',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%Y') as year ",
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%b') as month ",

			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,4) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "4")',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->group_by(
        	array(
        		$this->dx('expense_category_id'),
        		'year',
        		'month',
        	)
        );
		$result = $this->db->get('withdrawals')->result();
		foreach($result as $row){
			$arr[$row->expense_category_id][$row->month.' '.$row->year] = $row->amount;
		}
		return $arr;
	}

	function get_group_total_dividends_per_month_array($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as amount',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%Y') as year ",
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%b') as month ",
			)
		);

		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (37,38,39,40) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->group_by(
			array(
				'year',
				'month',
			)
		);
		$withdrawals = $this->db->get('withdrawals')->result();
		foreach($withdrawals as $withdrawal){
			$arr[$withdrawal->month.' '.$withdrawal->year] = $withdrawal->amount;
		}
		return $arr;
	}

	function get_group_total_contribution_refunds_per_contribution_per_month_array($group_id = 0,$months_array = array()){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		foreach($contribution_options as $contribution_id => $contribution_name){
			foreach($months_array as $month):
				$arr[$contribution_id][$month] = 0;
			endforeach;
		}
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as amount',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%Y') as year ",
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%b') as month ",
				$this->dx('contribution_id').' as contribution_id',
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}

		$this->db->where($this->dx('type').' IN (21,22,23,24) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "21" OR '.$this->dx('type').' = "22" OR '.$this->dx('type').' = "23" OR '.$this->dx('type').' = "24" )',NULL,FALSE);
	
		$this->db->group_by(
			array(
				$this->dx("contribution_id"),
				"year",
				"month",
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$withdrawals = $this->db->get('withdrawals')->result();
		foreach($withdrawals as $withdrawal){
			$arr[$withdrawal->contribution_id][$withdrawal->month.' '.$withdrawal->year] = $withdrawal->amount;
		}
		return $arr;
	}

	function get_group_total_contribution_refunds_per_month($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ''){
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%b') as month ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
			
		}
		$this->db->where($this->dx('type').' IN (21,22,23,24) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "21" OR '.$this->dx('type').' = "22" OR '.$this->dx('type').' = "23" OR '.$this->dx('type').' = "24" )',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('withdrawal_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('withdrawal_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->group_by(
			array(
				'year',
				'month'
			)
		);
		return $this->db->get('withdrawals')->result();
	}

	function get_total_withdrawals_amount_from_group_ids($group_ids = array()){
		$this->db->select(array(
			'SUM('.$this->dx('amount').') as amount',
		));
		if(empty($group_ids)){
    		$this->db->where($this->dx('group_id').' IN (0)',NULL,FALSE);
		}else{
    		$this->db->where($this->dx('group_id').' IN ('.implode(',',$group_ids).')',NULL,FALSE);
		}
		$result = $this->db->get('withdrawals')->row();		
		return $result->amount?$result->amount:0;
	}


	function get_group_expense_totals_per_year_array($group_id = 0,$start_year = 2000,$end_year = 2039){
		$arr = array();
		$this->db->select(
			array(
				$this->dx('amount').' as amount',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%Y') as year ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,4) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "4")',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		/*$this->db->group_by(
        	array(
        		'year',
        	)
        );*/
		$result = $this->db->get('withdrawals')->result();
		foreach($result as $row){
			$arr[$row->year] = 0;
		}
		foreach($result as $row){
			$arr[$row->year] += currency($row->amount);
		}
		return $arr;
	}

	function get_group_total_dividends_per_year_array($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as amount',
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%Y') as year ",
			)
		);

		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (37,38,39,40) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->group_by(
			array(
				'year'
			)
		);
		$withdrawals = $this->db->get('withdrawals')->result();
		foreach($withdrawals as $withdrawal){
			$arr[$withdrawal->year] = $withdrawal->amount;
		}
		return $arr;
	}

	function get_group_member_total_contribution_refunds_per_year_per_month($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		//echo $contribution_id_list;
		//die;
		$this->db->select(
			array(
				$this->dx('amount').' as amount ',
				$this->dx('member_id').' as member_id',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('withdrawal_date')." ),'%c') as month ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (21,22,23,24) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('withdrawal_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('withdrawal_date').' <="'.$to.'"',NULL,FALSE);
		}
		/*$this->db->group_by(
        	array(
        		'member_id',
        		'year',
        		'month'
        	)
        );*/
		$result = $this->db->get('withdrawals')->result();
		$arr = array();
		foreach($result as $row){
			$arr[$row->member_id][$row->year][$row->month] = 0;
		}
		foreach($result as $row){
			$arr[$row->member_id][$row->year][$row->month] += currency($row->amount);
		}
		return $arr;
	}

	
}