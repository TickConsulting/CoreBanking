<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');

class Deposits_m extends MY_Model{

	protected $_table = 'deposits';

	function __construct(){
		parent::__construct();
		$this->install();
		$this->load->model('contributions/contributions_m');
		$this->load->model('fine_categories/fine_categories_m');
	}

	function install(){
		
		$this->db->query("
			create table if not exists deposits(
				id int not null auto_increment primary key,
				`type` blob,
				`account_id` blob,
				`group_id` blob,
				`deposit_date` blob,
				`member_id` blob,
				`contribution_date` blob,
				`deposit_method` blob,
				`description` blob,
				`amount` blob,
				`active` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_by` blob,
				`modified_on` blob
			)"
		);

		$this->db->query("
			create table if not exists contribution_transfers(
				id int not null auto_increment primary key,
				`type` blob,
				`member_id` blob,
				`contribution_to_id` blob,
				`contribution_from_id` blob,
				`group_id` blob,
				`transfer_date` blob,
				`description` blob,
				`amount` blob,
				`active` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_by` blob,
				`modified_on` blob
			)"
		);

		$this->db->query("
			create table if not exists online_payment_requests(
				id int not null auto_increment primary key,
				`user_id` blob,
				`contribution_ids` blob,
                `fine_category_ids` blob,
                `loan_ids` blob,
                `descriptions` blob,
                `amounts` blob,
                `payment_for` blob,
                `reference_number` blob,
                `account_id` blob,
                `group_id` blob,
                `member_id` blob,
                `amount` blob,
                `phone` blob,
                `status` blob,
                `active` blob,
                `response_code` blob,
                `transaction_date` blob,
                `response_description` blob,
                `result_code` blob,
                `result_description` blob,
                `created_on` blob,
                `created_by` blob,
				`modified_by` blob,
				`modified_on` blob
			)"
		);
	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('deposits',$input);
	}

	function insert_contribution_transfer($input = array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('contribution_transfers',$input);
	}

	function update($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'deposits',$input);
	}

    function update_where($where = "",$input = array()){
        return $this->update_secure_where($where,'deposits',$input);
    }

    function update_stock_sales_where($where = "",$input = array()){
        return $this->update_secure_where($where,'stock_sales',$input);
    }

	function update_contribution_transfer($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'contribution_transfers',$input);
	}

	// function update_checkoff($id,$input=array(),$SKIP_VALIDATION=FALSE){
	// 	return $this->update_secure_data($id,'checkoffs',$input);
	// }

	function get($id=0){
		$this->select_all_secure('deposits');
		$this->db->where('id',$id);
		$this->db->limit(1);
		return $this->db->get('deposits')->row();
	}

	function get_group_deposit($id=0,$group_id=0){
		$this->select_all_secure('deposits');
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where('('.$this->dx('is_admin').'= "0" OR '.$this->dx('is_admin').'is NULL )',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('deposits')->row();
	}

	function get_group_deposits_by_checkoff_id($checkoff_id = 0,$group_id = 0){
		$this->select_all_secure('deposits');
		$this->db->where($this->dx('checkoff_id').' = "'.$checkoff_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where('('.$this->dx('is_admin').'= "0" OR '.$this->dx('is_admin').'is NULL )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->order_by($this->dx('deposit_date'),'DESC',FALSE);
		return $this->db->get('deposits')->result();
	}
	function get_group_deposit_siblings_by_transaction_alert_id($transaction_alert_id = 0,$group_id = 0){
		$this->select_all_secure('deposits');
		$this->db->where($this->dx('transaction_alert_id').' = "'.$transaction_alert_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('deposits')->result();
	}

	function get_group_deposit_by_transaction_alert_id($transaction_alert_id = 0,$group_id = 0){
		$this->select_all_secure('deposits');
		$this->db->where($this->dx('transaction_alert_id').' = "'.$transaction_alert_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('deposits')->row();
	}

	function get_all(){
		$this->select_all_secure('deposits');
		return $this->db->get('deposits')->result();
	}

	function get_group_total_contributions($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		//echo $contribution_id_list;
		//die;
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
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('deposit_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->limit(1);
		$result = $this->db->get('deposits')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_total_deposits($group_id = 0,$from = 0,$to = 0){
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
		if($from){
			$this->db->where($this->dx('deposit_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->limit(1);
		$result = $this->db->get('deposits')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_total_contributions_per_year($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		//echo $contribution_id_list;
		//die;
		$this->db->select(
			array(
				' SUM('.$this->dx('amount').') as amount ',

				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
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
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('deposit_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->group_by(
			array(
				'year'
			)
		);
		$result = $this->db->get('deposits')->result();
		return $result;
	}

	function get_group_savings_contribution_options($group_id = 0){
		$arr = array();
		$this->db->select(array(
				'id',
				$this->dx('name').' as name '
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('contributions.category').' = "2" ',NULL,FALSE);
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted').' = "")',NULL,FALSE);
		$contributions = $this->db->get('contributions')->result();
		foreach($contributions as $contribution){
			$arr[$contribution->id] = $contribution->name;
		}
		return $arr;
	}

	function get_group_member_total_contributions($member_id = 0,$group_id = 0,$contribution_id_list = ""){
		$this->db->select(array('SUM('.$this->dx('amount').') as amount'));
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' = "'.$this->member->id.'"',NULL,FALSE);
		}
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		//echo $contribution_id_list;
		//die;
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.') ',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->limit(1);
		$result = $this->db->get('deposits')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_member_contribution_totals($member_id = 0){

		$arr = array();
		$this->db->select(array('id'));
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$contributions = $this->db->get('contributions')->result();
		foreach($contributions as $contribution){
			$arr[$contribution->id] = 0;
		}

		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('contribution_id').' as contribution_id',
			)
		);
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' = "'.$this->member->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "7" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->group_by(
        	array(
        		$this->dx('member_id'),
        		'contribution_id'
        	)
        );
		$result = $this->db->get('deposits')->result();
		foreach($result as $row){
			$arr[$row->contribution_id] = $row->amount;
		}
		return $arr;
	}

	function get_group_total_contributions_by_month_array($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%c') as month ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "7" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
        $this->db->group_by(
        	array(
        		'year',
        		'month'
        	)
        );
		$result = $this->db->get('deposits')->result();
		foreach($result as $row){
			$arr[$row->year][$row->month] = $row->amount;
		}
		return $arr;
	}

	function get_group_member_total_contributions_by_month_array($group_id = 0,$member_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%c') as month ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "7" )',NULL,FALSE);
		
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
        $this->db->group_by(
        	array(
        		'year',
        		'month'
        	)
        );
		$result = $this->db->get('deposits')->result();
		foreach($result as $row){
			$arr[$row->year][$row->month] = $row->amount;
		}
		return $arr;
	}

	function get_group_member_total_contributions_array($group_id = 0,$to = 0,$from = 0){
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
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,7)',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "7" )',NULL,FALSE);
		$this->db->group_by(array($this->dx("member_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($to){
            $this->db->where($this->dx("deposit_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("deposit_date") . " >= " .$from, NULL, FALSE);
        }
        $this->db->limit($this->group->size);
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit){
			$arr[$deposit->member_id] = $deposit->amount;
		}
		return $arr;
	}


	function get_group_member_total_contributions_per_contribution_array($group_id = 0,$to = 0,$from = 0,$group_member_id = 0){
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
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($group_member_id){
			$this->db->where($this->dx('member_id').' = "'.$group_member_id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "7" )',NULL,FALSE);
		if($to){
            $this->db->where($this->dx("deposit_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("deposit_date") . " >= " .$from, NULL, FALSE);
        }
		$this->db->group_by(array($this->dx("member_id"),$this->dx("contribution_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit){
			$arr[$deposit->member_id][$deposit->contribution_id] = $deposit->amount;
		}
		return $arr;
	}

	function get_group_total_contributions_per_contribution_array($group_id = 0,$to = 0,$from = 0,$contribution_id_list = ""){
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
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		//echo $contribution_id_list;
		if($contribution_id_list){
			$this->db->where($this->dx('contribution_id')." IN (".$contribution_id_list.") ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('contribution_id')." IN (0) ",NULL,FALSE);
		}
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "7" )',NULL,FALSE);
		if($to){
            $this->db->where($this->dx("deposit_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("deposit_date") . " >= " .$from, NULL, FALSE);
        }
		$this->db->group_by(array($this->dx("contribution_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit){
			$arr[$deposit->contribution_id] = $deposit->amount;
		}
		return $arr;
	}

	function get_group_total_contributions_per_contribution_per_year_array($group_id = 0,$start_year = 2000,$end_year = 2039){
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
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
				$this->dx('contribution_id').' as contribution_id',
			)
		);

		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->group_by(
			array(
				$this->dx("contribution_id"),
				'year'
			)
		);
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit){
			$arr[$deposit->contribution_id][$deposit->year] = $deposit->amount;
		}
		return $arr;
	}

	function get_group_total_contributions_per_member_array($group_id = 0,$to = 0,$from = 0,$group_member_options=array()){
		$arr = array();
		if($group_member_options){

		}elseif($this->group_member_options){
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
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		if($to){
            $this->db->where($this->dx("deposit_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("deposit_date") . " >= " .$from, NULL, FALSE);
        }
		$this->db->group_by(array($this->dx("member_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit){
			$arr[$deposit->member_id] = $deposit->amount;
		}
		return $arr;
	}

	function get_group_total_fines_per_member_array($group_id = 0,$to = 0,$from = 0,$member_id = 0,$group_member_options=array()){
		$arr = array();
		if($group_member_options){

		}else{
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
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		if($to){
            $this->db->where($this->dx("deposit_date") . " <= " .$to, NULL, FALSE);
        }
        if($from){
            $this->db->where($this->dx("deposit_date") . " >= " .$from, NULL, FALSE);
        }
		$this->db->group_by(array($this->dx("member_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit){
			$arr[$deposit->member_id] = $deposit->amount;
		}
		return $arr;
	}

	function total_contributions_per_contribution_array($group_id=0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		foreach($contribution_options as $contribution_id => $contribution_name){
			$arr[$contribution_id] = 0;
		}
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('member_id').' as member_id',
				$this->dx('contribution_id').' as contribution_id',
				)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "7" )',NULL,FALSE);
		$this->db->group_by(array($this->dx("contribution_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit){
			$arr[$deposit->contribution_id] = $deposit->amount;
		}
		return $arr;

	}

	function get_group_income_categories_total_per_income_array($group_id = 0,$date_from=0,$date_to=0){
		$arr = array();
		$this->db->select(array('sum('.$this->dx('amount').') as amount',$this->dx('income_category_id').' as income_category_id'));
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($date_from && $date_to){
			$this->db->where($this->dx('deposit_date').'>="'.$date_from.'"',NULL,FALSE);
			$this->db->where($this->dx('deposit_date').'<="'.$date_to.'"',NULL,FALSE);
		}

		$this->db->where($this->dx('type').' IN (13,14,15,16) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "13" OR '.$this->dx('type').' = "14" OR '.$this->dx('type').' = "15" OR '.$this->dx('type').' = "16" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->group_by(array($this->dx("income_category_id")));
		$arr = $this->db->get('deposits')->result();
		return($arr);
	}

	function get_group_income_total_amounts($group_id = 0,$from = 0,$to = 0){
		$total_amount = 0;
		$this->db->select('sum('.$this->dx('amount').') as amount');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($from && $to){
			$this->db->where($this->dx('deposit_date').'>="'.$from.'"',NULL,FALSE);
			$this->db->where($this->dx('deposit_date').'<="'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (13,14,15,16) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$income = $this->db->get('deposits')->row();
		if($income){
			$total_amount = $income->amount;
		}
		return $total_amount;
	}


	function get_total_contribution_amount_paid($member_id = 0,$contribution_id = 0,$group_id = 0){
		$this->db->select(array('sum('.$this->dx('amount').') as amount'));
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "7" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$result = $this->db->get('deposits')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_total_contribution_amount_paid_after_date($group_id = 0,$member_id = 0,$contribution_id = 0,$date = 0){
		$this->db->select(array('sum('.$this->dx('amount').') as amount'));
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($date){
			$this->db->where($this->dx('deposit_date').' > "'.$date.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "7" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$result = $this->db->get('deposits')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}


	function get_group_member_total_fines($member_id = 0,$group_id = 0){
		$this->db->select(array('sum('.$this->dx('amount').') as amount'));
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' = "'.$this->member->id.'"',NULL,FALSE);
		}
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "4" OR '.$this->dx('type').' = "5" OR '.$this->dx('type').' = "6" OR '.$this->dx('type').' = "8")',NULL,FALSE);
		$this->db->where($this->dx('fine_category_id').' > "0"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$result = $this->db->get('deposits')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_member_total_contribution_fines($member_id = 0){
		$this->db->select(array('sum('.$this->dx('amount').') as amount'));
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' = "'.$this->member->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "4" OR '.$this->dx('type').' = "5" OR '.$this->dx('type').' = "6" OR '.$this->dx('type').' = "8")',NULL,FALSE);
		$this->db->where($this->dx('contribution_id').' > "0"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$result = $this->db->get('deposits')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_total_fines($group_id = 0,$from = 0,$to = 0){
		$this->db->select(array('sum('.$this->dx('amount').') as amount'));
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		if($from){
			$this->db->where($this->dx("deposit_date") . " >= " .$from, NULL, FALSE);
		}
		if($to){
			$this->db->where($this->dx("deposit_date") . " <= " .$to, NULL, FALSE);
		}
		//$this->db->where('('.$this->dx('type').' = "4" OR '.$this->dx('type').' = "5" OR '.$this->dx('type').' = "6" OR '.$this->dx('type').' = "8")',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->limit(1);
		$result = $this->db->get('deposits')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_total_fines_per_year($group_id = 0){
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->group_by(
        	array(
        		'year',
        	)
        );
		return $deposits = $this->db->get('deposits')->result();
	}

	function get_group_member_fine_totals($member_id = 0){

		$arr = array();
		$this->db->select(array('id'));
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$fine_categories = $this->db->get('fine_categories')->result();
		foreach($fine_categories as $fine_category){
			$arr[$fine_category->id] = 0;
		}

		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('fine_category_id').' as fine_category_id',
			)
		);
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' = "'.$this->member->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('fine_category_id').' > "0"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "4" OR '.$this->dx('type').' = "5" OR '.$this->dx('type').' = "6" OR '.$this->dx('type').' = "8")',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->group_by(
        	array(
        		$this->dx('member_id'),
        		'fine_category_id',
        	)
        );
		$result = $this->db->get('deposits')->result();
		foreach($result as $row){
			$arr[$row->fine_category_id] = $row->amount;
		}
		return $arr;
	}

	function get_group_member_contribution_fine_totals($member_id = 0){
		$arr = array();
		$this->db->select(array('id'));
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$contributions = $this->db->get('contributions')->result();
		foreach($contributions as $contribution){
			$arr[$contribution->id] = 0;
		}

		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('contribution_id').' as contribution_id',
			)
		);
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' = "'.$this->member->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "4" OR '.$this->dx('type').' = "5" OR '.$this->dx('type').' = "6" OR '.$this->dx('type').' = "8")',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->group_by(
        	array(
        		$this->dx('member_id'),
        		'contribution_id'
        	)
        );
		$result = $this->db->get('deposits')->result();
		foreach($result as $row){
			$arr[$row->contribution_id] = $row->amount;
		}
		return $arr;
	}

	function get_group_total_fine_payments($group_id = 0,$from=0,$to=0,$member_id = 0){
		$amount = 0;
		$this->db->select('sum('.$this->dx('amount').') as amount');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		$this->db->where($this->dx('active').' >= "1"',NULL,FALSE);
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx("deposit_date") . " >= " .$from, NULL, FALSE);
		}
		if($to){
			$this->db->where($this->dx("deposit_date") . " <= " .$to, NULL, FALSE);
		}
		$amounts = $this->db->get('deposits')->row();
		if($amounts){
			$amount = $amounts->amount;
		}
		return $amount;
	}


	function get_group_member_total_fines_array($group_id = 0){
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
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "4" OR '.$this->dx('type').' = "5" OR '.$this->dx('type').' = "6" OR '.$this->dx('type').' = "8")',NULL,FALSE);
		$this->db->group_by(array($this->dx("member_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->limit($this->group->size);
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit){
			$arr[$deposit->member_id] = $deposit->amount;
		}
		return $arr;
	}

	function get_group_member_total_miscellaneous_array($group_id = 0){
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
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (9,10,11,12) ',NULL,FALSE);
		$this->db->group_by(array($this->dx("member_id")));

		$this->db->limit($this->group->size);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit){
			$arr[$deposit->member_id] = $deposit->amount;
		}
		return $arr;
	}

	function get_group_member_total_miscellaneous_amount($group_id = 0,$from = 0,$to = 0){
		$total_amount = 0;
		$this->db->select('sum('.$this->dx('amount').') as amount');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('deposit_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (9,10,11,12) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' >= "1"',NULL,FALSE);
		$deposits = $this->db->get('deposits')->row();
		if($deposits){
			$total_amount = $deposits->amount;
		}
		return $total_amount;
	}


	function get_group_total_fines_by_month_array($group_id = 0){
		$arr = array();
		
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%c') as month ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "4" OR '.$this->dx('type').' = "5" OR '.$this->dx('type').' = "6" OR '.$this->dx('type').' = "8")',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
        $this->db->group_by(
        	array(
        		'year',
        		'month'
        	)
        );
		$result = $this->db->get('deposits')->result();
		foreach($result as $row){
			$arr[$row->year][$row->month] = $row->amount;
		}
		return $arr;
	}

	function get_group_member_total_fines_by_month_array($group_id = 0,$member_id = 0){
		$arr = array();
		
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%c') as month ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "4" OR '.$this->dx('type').' = "5" OR '.$this->dx('type').' = "6" OR '.$this->dx('type').' = "8")',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
        $this->db->group_by(
        	array(
        		'year',
        		'month'
        	)
        );
		$result = $this->db->get('deposits')->result();
		foreach($result as $row){
			$arr[$row->year][$row->month] = $row->amount;
		}
		return $arr;
	}

	function get_total_contribution_fine_amount_paid($group_id = 0,$member_id = 0,$contribution_id = 0,$date = 0){
		$this->db->select(array('sum('.$this->dx('amount').') as amount'));
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "4" OR '.$this->dx('type').' = "5" OR '.$this->dx('type').' = "6" OR '.$this->dx('type').' = "8")',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$result = $this->db->get('deposits')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_total_contribution_fine_amount_paid_after_date($group_id = 0,$member_id = 0,$contribution_id = 0,$date = 0){
		$this->db->select(array('sum('.$this->dx('amount').') as amount'));
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}

		if($date){
			$this->db->where($this->dx('deposit_date').' > "'.$date.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "4" OR '.$this->dx('type').' = "5" OR '.$this->dx('type').' = "6" OR '.$this->dx('type').' = "8")',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$result = $this->db->get('deposits')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_total_fine_amount_paid($group_id = 0,$member_id = 0,$fine_category_id = 0,$date = 0){
		$this->db->select(array('sum('.$this->dx('amount').') as amount'));
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('fine_category_id').' = "'.$fine_category_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "4" OR '.$this->dx('type').' = "5" OR '.$this->dx('type').' = "6" OR '.$this->dx('type').' = "8")',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$result = $this->db->get('deposits')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_total_fine_amount_paid_after_date($group_id = 0,$member_id = 0,$fine_category_id = 0,$date = 0){
		$this->db->select(array('sum('.$this->dx('amount').') as amount'));
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('fine_category_id').' = "'.$fine_category_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		
		if($date){
			$this->db->where($this->dx('deposit_date').' > "'.$date.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "4" OR '.$this->dx('type').' = "5" OR '.$this->dx('type').' = "6" OR '.$this->dx('type').' = "8")',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$result = $this->db->get('deposits')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_total_miscellaneous_amount_paid($group_id = 0,$member_id = 0){
		$this->db->select(array('sum('.$this->dx('amount').') as amount'));
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (9,10,11,12) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "9" OR '.$this->dx('type').' = "10" OR '.$this->dx('type').' = "11" OR '.$this->dx('type').' = "12" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$result = $this->db->get('deposits')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	function get_group_total_miscellaneous_payments_by_month_array($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%c') as month ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (9,10,11,12) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "9" OR '.$this->dx('type').' = "10" OR '.$this->dx('type').' = "11" OR '.$this->dx('type').' = "12" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
        $this->db->group_by(
        	array(
        		'year',
        		'month'
        	)
        );
		$result = $this->db->get('deposits')->result();
		foreach($result as $row){
			$arr[$row->year][$row->month] = $row->amount;
		}
		return $arr;
	}

	function get_group_member_total_miscellaneous_payments_by_month_array($group_id = 0,$member_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%c') as month ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (9,10,11,12) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "9" OR '.$this->dx('type').' = "10" OR '.$this->dx('type').' = "11" OR '.$this->dx('type').' = "12" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
        $this->db->group_by(
        	array(
        		'year',
        		'month'
        	)
        );
		$result = $this->db->get('deposits')->result();
		foreach($result as $row){
			$arr[$row->year][$row->month] = $row->amount;
		}
		return $arr;
	}

	function get_total_miscellaneous_amount_paid_after_date($group_id = 0,$member_id = 0,$date = 0){
		$this->db->select(array('sum('.$this->dx('amount').') as amount'));
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($date){
			$this->db->where($this->dx('deposit_date').' > "'.$date.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (9,10,11,12) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "9" OR '.$this->dx('type').' = "10" OR '.$this->dx('type').' = "11" OR '.$this->dx('type').' = "12" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$result = $this->db->get('deposits')->row();
		if($result){
			return $result->amount;
		}else{
			return 0;
		}
	}

	

	function get_group_deposits($group_id = 0,$filter_parameters = array(),$sort_by='',$sort_order=''){
		$this->select_all_secure('deposits');
		if(isset($filter_parameters['transaction_alert_id'])){
			$this->db->where($this->dx('transaction_alert_id').' = "'.$filter_parameters['transaction_alert_id'].'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where('('.$this->dx('is_admin').'= "0" OR '.$this->dx('is_admin').'is NULL )',NULL,FALSE);

		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('deposit_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('deposit_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
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

		if(isset($filter_parameters['fine_categories']) && $filter_parameters['fine_categories']){
			$fine_category_list = '0';
			$fine_categories = $filter_parameters['fine_categories'];
			$count = 1;
			foreach($fine_categories as $fine_category_id){
				if($fine_category_id){
					if($count==1){
						$fine_category_list = $fine_category_id;
					}else{
						$fine_category_list .= ','.$fine_category_id;
					}
					$count++;
				}
			}
			if($fine_category_list){
        		$this->db->where($this->dx('fine_category_id').' IN ('.$fine_category_list.')',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['accounts']) && $filter_parameters['accounts']){
			$account_list = '0';
			$accounts = $filter_parameters['accounts'];
			$count = 1;
			foreach($accounts as $account_id){
				if($account_id){
					if($count==1){
						$account_list = '"'.$account_id.'"';
					}else{
						$account_list .= ',"'.$account_id.'"';
					}
					$count++;
				}
			}
			if($account_list){
        		$this->db->where($this->dx('account_id').' IN ('.$account_list.')',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['income_categories']) && $filter_parameters['income_categories']){
			$income_category_list = '0';
			$income_categories = $filter_parameters['income_categories'];
			$count = 1;
			foreach($income_categories as $income_category_id){
				if($income_category_id){
					if($count==1){
						$income_category_list = $income_category_id;
					}else{
						$income_category_list .= ','.$income_category_id;
					}
					$count++;
				}
			}
			if($income_category_list){
        		$this->db->where($this->dx('income_category_id').' IN ('.$income_category_list.')',NULL,FALSE);
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

		if(isset($filter_parameters['assets']) && $filter_parameters['assets']){
			$assets = $filter_parameters['assets'];
			if(empty($assets)){
        		$this->db->where($this->dx('asset_id').' IN (0)',NULL,FALSE);
			}else{
        		$this->db->where($this->dx('asset_id').' IN ('.implode(',',$assets).')',NULL,FALSE);
			}
		}

		
		if(isset($filter_parameters['type']) && $filter_parameters['type']){
			$this->db->where($this->dx('type')." IN (".$filter_parameters['type'].")",NULL,FALSE);
		}
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where("(".$this->dx('is_a_back_dating_record').' IS NULL OR '.$this->dx('is_a_back_dating_record')." = '0' )",NULL,FALSE);
		if($sort_by && $sort_order){
			$this->db->order_by($this->dx($sort_by).'+0',$sort_order,FALSE);
		}else{
			$this->db->order_by($this->dx('deposit_date'),'DESC',FALSE);
		}
		return $this->db->get('deposits')->result();
	}


	function get_voided_group_deposits($group_id = 0,$filter_parameters = array()){
		$this->select_all_secure('deposits');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "0"',NULL,FALSE);
		$this->db->where("(".$this->dx('is_a_back_dating_record').' IS NULL  OR '.$this->dx('is_a_back_dating_record').' = 0 )',NULL,FALSE);
		$this->db->order_by($this->dx('deposit_date'),'DESC',FALSE);
		return $this->db->get('deposits')->result();
	}

	function get_group_and_member_deposits($group_id=0,$member_id=0,$filter_parameters=array()){
		$this->select_all_secure('deposits');
		if(isset($filter_parameters['transaction_alert_id'])){
			$this->db->where($this->dx('transaction_alert_id').' = "'.$filter_parameters['transaction_alert_id'].'"',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' = "'.$this->member->id.'"',NULL,FALSE);
		}
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->order_by($this->dx('deposit_date'),'DESC',FALSE);
		return $this->db->get('deposits')->result();
	}

	function get_group_member_deposits($member_id = 0,$group_id = 0){
		$this->select_all_secure('deposits');
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('deposits')->result();
	}

	function count_group_deposits($group_id = 0,$filter_parameters = array()){
		if(isset($filter_parameters['transaction_alert_id'])){
			$this->db->where($this->dx('transaction_alert_id').' = "'.$filter_parameters['transaction_alert_id'].'"',NULL,FALSE);
		}
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('deposit_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('deposit_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
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

		if(isset($filter_parameters['fine_categories']) && $filter_parameters['fine_categories']){
			$fine_category_list = '0';
			$fine_categories = $filter_parameters['fine_categories'];
			$count = 1;
			foreach($fine_categories as $fine_category_id){
				if($fine_category_id){
					if($count==1){
						$fine_category_list = $fine_category_id;
					}else{
						$fine_category_list .= ','.$fine_category_id;
					}
					$count++;
				}
			}
			if($fine_category_list){
        		$this->db->where($this->dx('fine_category_id').' IN ('.$fine_category_list.')',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['income_categories']) && $filter_parameters['income_categories']){
			$income_category_list = '0';
			$income_categories = $filter_parameters['income_categories'];
			$count = 1;
			foreach($income_categories as $income_category_id){
				if($income_category_id){
					if($count==1){
						$income_category_list = $income_category_id;
					}else{
						$income_category_list .= ','.$income_category_id;
					}
					$count++;
				}
			}
			if($income_category_list){
        		$this->db->where($this->dx('income_category_id').' IN ('.$income_category_list.')',NULL,FALSE);
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

		if(isset($filter_parameters['assets']) && $filter_parameters['assets']){
			$assets = $filter_parameters['assets'];
			if(empty($assets)){
        		$this->db->where($this->dx('asset_id').' IN (0)',NULL,FALSE);
			}else{
        		$this->db->where($this->dx('asset_id').' IN ('.implode(',',$assets).')',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['type']) && $filter_parameters['type']){
			$this->db->where($this->dx('type')." IN (".$filter_parameters['type'].")",NULL,FALSE);
		}
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where("(".$this->dx('is_a_back_dating_record').' IS NULL OR '.$this->dx('is_a_back_dating_record')." = '0' )",NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->count_all_results('deposits');
	}

	function count_from_date($from=0,$to=0){
		if($from){
			$this->db->where($this->dx('deposit_date').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <= "'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->count_all_results('deposits');
	}

	function value_of_deposits_where($from=0,$to=0){
		$this->db->select(array("sum(".$this->dx("amount").") as total"));
		if($from){
			$this->db->where($this->dx('deposit_date').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <= "'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('deposits')->row()->total;
	}
	function count_group_and_member_deposits($group_id=0,$member_id=0,$filter_parameters=array()){
		if(isset($filter_parameters['transaction_alert_id'])){
			$this->db->where($this->dx('transaction_alert_id').' = "'.$filter_parameters['transaction_alert_id'].'"',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' = "'.$this->member->id.'"',NULL,FALSE);
		}
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->count_all_results('deposits');
	}



	function get_group_stock_sale_deposits($group_id = 0,$filter_parameters = array()){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}

		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('deposit_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('deposit_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
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
		
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);

		$this->db->where($this->dx('type').' IN (25,26,27,28) ',NULL,FALSE);
		///$this->db->where('('.$this->dx('type').' = "25" OR '.$this->dx('type').' = "26" OR '.$this->dx('type').' = "27" OR '.$this->dx('type').' = "28" )',NULL,FALSE);
		$this->db->order_by($this->dx('deposit_date'),'DESC',FALSE);
		return $this->db->get('deposits')->result();
	}

	function count_group_stock_sale_deposits($group_id = 0,$filter_parameters = array()){
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}

		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('deposit_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('deposit_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
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
		
		$this->db->where($this->dx('type').' IN (25,26,27,28) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "25" OR '.$this->dx('type').' = "26" OR '.$this->dx('type').' = "27" OR '.$this->dx('type').' = "28" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->count_all_results('deposits');
	}

	// function insert_checkoff($input=array(),$SKIP_VALIDATION = FALSE){
	// 	return $this->insert_secure_data('checkoffs',$input);
	// }

	function get_group_total_stock_sale_amount($group_id=0,$date_from=0,$date_to=0){
		$this->db->select('sum('.$this->dx('amount').') as amount');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($date_from && $date_to){
			$this->db->where($this->dx('deposit_date').'>="'.$date_from.'"',NULL,FALSE);
			$this->db->where($this->dx('deposit_date').'<="'.$date_to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (25,26,27,28) ',NULL,FALSE);
		return $this->db->get('deposits')->row()->amount?:0;
	}


	function get_group_stock_sale_deposits_by_stock_id($stock_id = 0,$group_id = 0){
		$this->select_all_secure('deposits');
		$this->db->where($this->dx('stock_id').' = "'.$stock_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);

		$this->db->where($this->dx('type').' IN (25,26,27,28) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "25" OR '.$this->dx('type').' = "26" OR '.$this->dx('type').' = "27" OR '.$this->dx('type').' = "28" )',NULL,FALSE);
		$this->db->order_by($this->dx('deposit_date'),'DESC',FALSE);
		return $this->db->get('deposits')->result();
	}

	function get_money_market_investment_cash_in_deposit_by_money_market_investment_id($money_market_investment_id = 0,$group_id = 0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('money_market_investment_id').' = "'.$money_market_investment_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);

		$this->db->where($this->dx('type').' IN (29,30,31,32) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "29" OR '.$this->dx('type').' = "30" OR '.$this->dx('type').' = "31" OR '.$this->dx('type').' = "32" )',NULL,FALSE);
		return $this->db->get('deposits')->row();
	}

	function get_group_money_market_investment_cash_in_deposits($group_id = 0,$filter_parameters = array()){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);

		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('deposit_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('deposit_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
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

		if(isset($filter_parameters['money_market_investments']) && $filter_parameters['money_market_investments']){
			$money_market_investments = $filter_parameters['money_market_investments'];
			if(empty($money_market_investments)){
        		$this->db->where($this->dx('money_market_investment_id').' IN (0)',NULL,FALSE);
			}else{
        		$this->db->where($this->dx('money_market_investment_id').' IN ('.implode(',',$money_market_investments).')',NULL,FALSE);
			}
		}
		$this->db->where($this->dx('type').' IN (29,30,31,32) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "29" OR '.$this->dx('type').' = "30" OR '.$this->dx('type').' = "31" OR '.$this->dx('type').' = "32" )',NULL,FALSE);
		$this->db->order_by($this->dx('deposit_date'),'DESC',FALSE);
		return $this->db->get('deposits')->result();
	}

	function get_group_money_market_investment_cash_in_deposits_options($group_id = 0,$money_market_investment_id = 0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);

		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('deposit_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('deposit_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}
		$this->db->where($this->dx('money_market_investment_id').' = "'.$money_market_investment_id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (29,30,31,32) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "29" OR '.$this->dx('type').' = "30" OR '.$this->dx('type').' = "31" OR '.$this->dx('type').' = "32" )',NULL,FALSE);
		$this->db->order_by($this->dx('deposit_date'),'DESC',FALSE);
		return $this->db->get('deposits')->result();
	}

	function count_group_money_market_investment_cash_in_deposits($group_id = 0,$filter_parameters = array()){
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}

		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('deposit_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('deposit_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
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
		
		if(isset($filter_parameters['money_market_investments']) && $filter_parameters['money_market_investments']){
			$money_market_investments = $filter_parameters['money_market_investments'];
			if(empty($money_market_investments)){
        		$this->db->where($this->dx('money_market_investment_id').' IN (0)',NULL,FALSE);
			}else{
        		$this->db->where($this->dx('money_market_investment_id').' IN ('.implode(',',$money_market_investments).')',NULL,FALSE);
			}
		}
		$this->db->where($this->dx('type').' IN (29,30,31,32) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->count_all_results('deposits');
	}

	function get_group_total_money_market_cash_in_amount($group_id=0,$date_from=0,$date_to=0){
		$this->db->select('sum('.$this->dx('amount').') as amount');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($date_from && $date_to){
			$this->db->where($this->dx('deposit_date').'>="'.$date_from.'"',NULL,FALSE);
			$this->db->where($this->dx('deposit_date').'<="'.$date_to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (29,30,31,32) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('deposits')->row()->amount?:0;
	}


	function get_group_asset_sale_deposits($group_id = 0,$filter_parameters = array()){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}

		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('deposit_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('deposit_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['accounts']) && $filter_parameters['accounts']){
			$account_list = '0';
			$accounts = $filter_parameters['accounts'];
			$count = 1;
			foreach($accounts as $account_id){
				if($account_id){
					if($count==1){
						$account_list = '"'.$account_id.'"';
					}else{
						$account_list .= ',"'.$account_id.'"';
					}
					$count++;
				}
			}
			if($account_list){
        		$this->db->where($this->dx('account_id').' IN ('.$account_list.')',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['assets']) && $filter_parameters['assets']){
			$assets = $filter_parameters['assets'];
			if(empty($assets)){
        		$this->db->where($this->dx('asset_id').' IN (0)',NULL,FALSE);
			}else{
        		$this->db->where($this->dx('asset_id').' IN ('.implode(',',$assets).')',NULL,FALSE);
			}
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);

		$this->db->where($this->dx('type').' IN (33,34,35,36) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "33" OR '.$this->dx('type').' = "34" OR '.$this->dx('type').' = "35" OR '.$this->dx('type').' = "36" )',NULL,FALSE);
		$this->db->order_by($this->dx('deposit_date'),'DESC',FALSE);
		return $this->db->get('deposits')->result();
	}



	function get_group_asset_sale_deposits_by_asset_id($id = 0,$group_id = 0){
		$this->select_all_secure('deposits');
		$this->db->where($this->dx('asset_id').' = "'.$id.'" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (33,34,35,36) ',NULL,FALSE);
		$this->db->order_by($this->dx('deposit_date'),'DESC',FALSE);
		return $this->db->get('deposits')->result();
	}


	function count_group_asset_sale_deposits($group_id = 0,$filter_parameters = array()){
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}

		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('deposit_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('deposit_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['accounts']) && $filter_parameters['accounts']){
			$account_list = '0';
			$accounts = $filter_parameters['accounts'];
			$count = 1;
			foreach($accounts as $account_id){
				if($account_id){
					if($count==1){
						$account_list = '"'.$account_id.'"';
					}else{
						$account_list .= ',"'.$account_id.'"';
					}
					$count++;
				}
			}
			if($account_list){
        		$this->db->where($this->dx('account_id').' IN ('.$account_list.')',NULL,FALSE);
			}
		}
		
		if(isset($filter_parameters['assets']) && $filter_parameters['assets']){
			$assets = $filter_parameters['assets'];
			if(empty($assets)){
        		$this->db->where($this->dx('asset_id').' IN (0)',NULL,FALSE);
			}else{
        		$this->db->where($this->dx('asset_id').' IN ('.implode(',',$assets).')',NULL,FALSE);
			}
		}
		$this->db->where($this->dx('type').' IN (33,34,35,36) ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "33" OR '.$this->dx('type').' = "34" OR '.$this->dx('type').' = "35" OR '.$this->dx('type').' = "36" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->count_all_results('deposits');
	}

	function get_group_total_asset_sale_amount($group_id=0,$date_from=0,$date_to=0){
		$this->db->select('sum('.$this->dx('amount').') as amount');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($date_from && $date_to){
			$this->db->where($this->dx('deposit_date').'>="'.$date_from.'"',NULL,FALSE);
			$this->db->where($this->dx('deposit_date').'<="'.$date_to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (33,34,35,36) ',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('deposits')->row()->amount?:0;
	}

	function get_bank_loan_disbursement_deposit_by_bank_loan_id($bank_loan_id = 0,$group_id = 0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('bank_loan_id').' = "'.$bank_loan_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (21,22,23,24) ',NULL,FALSE);
		$this->db->limit(1);
		//$this->db->where('('.$this->dx('type').' = "21" OR '.$this->dx('type').' = "22" OR '.$this->dx('type').' = "23" OR '.$this->dx('type').' = "24" )',NULL,FALSE);
		return $this->db->get('deposits')->row();
	}

	function get_total_bank_loan_amount($group_id=0,$date_from=0,$date_to=0){
		$this->db->select('sum('.$this->dx('amount').') as amount');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($date_from && $date_to){
			$this->db->where($this->dx('deposit_date').'>="'.$date_from.'"',NULL,FALSE);
			$this->db->where($this->dx('deposit_date').'<="'.$date_to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (21,22,23,24) ',NULL,FALSE);
		$this->db->limit(1);
		//$this->db->where('('.$this->dx('type').' = "21" OR '.$this->dx('type').' = "22" OR '.$this->dx('type').' = "23" OR '.$this->dx('type').' = "24" )',NULL,FALSE);
		return $this->db->get('deposits')->row()->amount?:0;
	}

	function get_deposit_by_account_transfer_id($account_transfer_id = 0,$group_id = 0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('account_transfer_id').' = "'.$account_transfer_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);

		$this->db->where($this->dx('type').' IN (37,38,39,40) ',NULL,FALSE);
		$this->db->limit(1);
		//$this->db->where('('.$this->dx('type').' = "37" OR '.$this->dx('type').' = "38" OR '.$this->dx('type').' = "39" OR '.$this->dx('type').' = "40" )',NULL,FALSE);
		return $this->db->get('deposits')->row();
	}

	function get_incoming_account_transfer_amount($group_id=0){
		$this->db->select('sum('.$this->dx('amount').') as amount');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (37,38,39,40) ',NULL,FALSE);
		$this->db->limit(1);
		//$this->db->where('('.$this->dx('type').' = "37" OR '.$this->dx('type').' = "38" OR '.$this->dx('type').' = "39" OR '.$this->dx('type').' = "40" )',NULL,FALSE);
		return $this->db->get('deposits')->row()->amount;
	}

	function get_deposit_by_loan_repayment_id($loan_repayment_id = 0,$group_id = 0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('loan_repayment_id').' = "'.$loan_repayment_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (17,18,19,20) ',NULL,FALSE);
		$this->db->limit(1);
		//$this->db->where('('.$this->dx('type').' = "17" OR '.$this->dx('type').' = "18" OR '.$this->dx('type').' = "19" OR '.$this->dx('type').' = "20" )',NULL,FALSE);
		return $this->db->get('deposits')->row();
	}

	function get_deposit_by_external_loan_repayment_id($debtor_loan_repayment_id = 0,$group_id = 0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('debtor_loan_repayment_id').' = "'.$debtor_loan_repayment_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (49,50,51,52) ',NULL,FALSE);
		$this->db->limit(1);
		//$this->db->where('('.$this->dx('type').' = "17" OR '.$this->dx('type').' = "18" OR '.$this->dx('type').' = "19" OR '.$this->dx('type').' = "20" )',NULL,FALSE);
		return $this->db->get('deposits')->row();
	}

	function get_deposit_for_loan_processing_by_loan_id($loan_id=0,$group_id=0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('loan_id').' = "'.$loan_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "41" OR '.$this->dx('type').' = "42" OR '.$this->dx('type').' = "43" OR '.$this->dx('type').' = "44" )',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (41,42,43,44) ',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('deposits')->row();
	}

	function get_deposit_for_external_lending_loan_processing_by_debtor_loan_id($debtor_loan_id=0,$group_id=0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('debtor_loan_id').' = "'.$debtor_loan_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (45,46,47,48) ',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('deposits')->row();
	}

	function get_group_total_income_by_month_array($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%c') as month ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (13,14,15,16) ',NULL,FALSE);

		//$this->db->where('('.$this->dx('type').' = "13" OR '.$this->dx('type').' = "14" OR '.$this->dx('type').' = "15" OR '.$this->dx('type').' = "16" )',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
        $this->db->group_by(
        	array(
        		'year',
        		'month'
        	)
        );
		$result = $this->db->get('deposits')->result();
		foreach($result as $row){
			$arr[$row->year][$row->month] = $row->amount;
		}
		return $arr;
	}

	function get_group_loan_repayments($group_id=0,$date_from=0,$date_to=0){
		$members = $this->group_member_options;
		$amount=array();

		foreach($members as $member_id => $member_name){
				$this->db->select('sum('.$this->dx('amount').') as amount_paid');
				if($group_id){
					$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
				}else{
					$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
				}
				if($date_from && $date_to){
					$this->db->where($this->dx('deposit_date').'>="'.$date_from.'"',NULL,FALSE);
					$this->db->where($this->dx('deposit_date').'<="'.$date_to.'"',NULL,FALSE);
				}
				$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
				$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
				$this->db->where($this->dx('type').' IN (17,18,19,20) ',NULL,FALSE);
				$this->db->limit(1);
				//$this->db->where('('.$this->dx('type').' = "17" OR '.$this->dx('type').' = "18" OR '.$this->dx('type').' = "19" OR '.$this->dx('type').' = "20" )',NULL,FALSE);
				$amount[$member_id] = $this->db->get('deposits')->row()->amount_paid?:0;
			}

		return $amount;

	}

	function get_group_active_loan_repayment_deposits($group_id = 0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (17,18,19,20) ',NULL,FALSE);
		return $this->db->get('deposits')->result();
	}

	function get_group_loan_repayment_deposits($group_id = 0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (17,18,19,20) ',NULL,FALSE);
		$this->db->order_by($this->dx('deposit_date'),'ASC',FALSE);
		return $this->db->get('deposits')->result();
	}

	function get_group_loan_repayment_deposits_per_loan_array($group_id = 0,$from = 0,$to = 0){
		$arr = array();
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('deposit_date').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <= "'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (17,18,19,20) ',NULL,FALSE);
		$this->db->order_by($this->dx('deposit_date'),'ASC',FALSE);
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit):
			$arr[$deposit->loan_id][] = $deposit;
		endforeach;
		return $arr;
	}

	function get_group_loan_repayments_total_amount($group_id=0,$date_from=0,$date_to=0,$loan_ids=array()){
		$amount = 0;
		$list = '';
		if($loan_ids){
			foreach ($loan_ids as $loan_id) {
				if($list){
					$list.=','.$loan_id;
				}else{
					$list=$loan_id;
				}
			}
		}
		$this->db->select('sum('.$this->dx('amount').') as amount_paid');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($date_from && $date_to){
			$this->db->where($this->dx('deposit_date').'>="'.$date_from.'"',NULL,FALSE);
			$this->db->where($this->dx('deposit_date').'<="'.$date_to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' >= "1"',NULL,FALSE);
		if($list){
			
			$this->db->where($this->dx('loan_id').' IN('.$list.')',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (17,18,19,20) ',NULL,FALSE);
		$this->db->limit(1);
		$result = $this->db->get('deposits')->row();
		if($result){
			$amount = $result->amount_paid;
		}
		return $amount;
	}

	function get_group_debtor_loan_repayments($group_id=0,$date_from=0,$date_to=0){
		$debtors = $this->group_debtor_options;
		$amount=array();
		foreach($debtors as $debtor_id => $debtor_name){
			$this->db->select('sum('.$this->dx('amount').') as amount_paid');
			if($group_id){
				$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
			}else{
				$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
			}
			if($date_from && $date_to){
				$this->db->where($this->dx('deposit_date').'>="'.$date_from.'"',NULL,FALSE);
				$this->db->where($this->dx('deposit_date').'<="'.$date_to.'"',NULL,FALSE);
			}
			$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
			$this->db->where($this->dx('debtor_id').' = "'.$debtor_id.'"',NULL,FALSE);
			$this->db->where($this->dx('type').' IN (49,50,51,52) ',NULL,FALSE);
			$this->db->limit(1);
			$amount[$debtor_id] = $this->db->get('deposits')->row()->amount_paid?:0;
		}
		return $amount;
	}

	function get_group_debtor_loan_repayments_total_amount($group_id=0,$date_from=0,$date_to=0,$debtor_loan_ids=array()){
		$amount = 0;
		$list = '';
		if($debtor_loan_ids){
			foreach ($debtor_loan_ids as $debtor_loan_id) {
				if($list){
					$list.=','.$debtor_loan_id;
				}else{
					$list=$debtor_loan_id;
				}
			}
		}
		$this->db->select('sum('.$this->dx('amount').') as amount_paid');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($date_from && $date_to){
			$this->db->where($this->dx('deposit_date').'>="'.$date_from.'"',NULL,FALSE);
			$this->db->where($this->dx('deposit_date').'<="'.$date_to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('debtor_id').' => "1"',NULL,FALSE);
		if($list){
			$this->db->where($this->dx('debtor_loan_id').' IN('.$list.')',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (49,50,51,52) ',NULL,FALSE);
		$this->db->limit(1);
		$result = $this->db->get('deposits')->row();
		if($result){
			$amount = $result->amount_paid;
		}
		return $amount;
	}

	function get_group_total_loan_processing_income($group_id = 0,$from=0,$to=0){
		$this->db->select(array(
			'sum('.$this->dx('amount').') as total_amount',
		));
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (41,42,43,44) ',NULL,FALSE);
		$this->db->where($this->dx('member_id').' >= "1"',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('deposit_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		$result = $this->db->get('deposits')->row();
		if($result){
			return $result->total_amount;
		}
	}

	function get_group_total_loan_processing_income_per_year_array($group_id = 0,$from=0,$to=0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as total_amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (41,42,43,44) ',NULL,FALSE);
		$this->db->where($this->dx('member_id').' >= "1"',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('deposit_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->group_by(
            array(
                'year',
            )
        );
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit):
            $arr[$deposit->year] = ($deposit->total_amount > 0)?$deposit->total_amount:0;
        endforeach;
        return $arr;
	}

	function get_group_total_income_per_year_array($group_id = 0,$from=0,$to=0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as total_amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);

		$this->db->where($this->dx('type').' IN (13,14,15,16) ',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('deposit_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->group_by(
            array(
                'year',
            )
        );
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit):
            $arr[$deposit->year] = ($deposit->total_amount > 0)?$deposit->total_amount:0;
        endforeach;
        return $arr;
	}

	function get_group_total_miscellaneous_income_per_year_array($group_id = 0,$from=0,$to=0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as total_amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);

		$this->db->where($this->dx('type').' IN (9,10,11,12) ',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('deposit_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->group_by(
            array(
                'year',
            )
        );
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit):
            $arr[$deposit->year] = ($deposit->total_amount > 0)?$deposit->total_amount:0;
        endforeach;
        return $arr;
	}

	function get_group_loan_processing_income_deposits($group_id = 0,$from=0,$to=0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (41,42,43,44) ',NULL,FALSE);
		$this->db->where($this->dx('member_id').' >= "1"',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('deposit_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		return $this->db->get('deposits')->result();
	}

	function get_debtor_loan_processing_income($group_id = 0,$from=0,$to=0){
		$this->db->select(array(
			'sum('.$this->dx('amount').') as total_amount',
		));
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (45,48,47,48) ',NULL,FALSE);
		$this->db->where($this->dx('debtor_id').' >= "1"',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('deposit_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		$result = $this->db->get('deposits')->row();
		if($result){
			return $result->total_amount;
		}
	}

	function get_total_group_loan_payments($group_id=0){
		$this->select('sum('.$this->dx('amount').') as amount');
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (17,18,19,20) ',NULL,FALSE);
		$this->db->limit(1);
		//$this->db->where('('.$this->dx('type').' = "17" OR '.$this->dx('type').' = "18" OR '.$this->dx('type').' = "19" OR '.$this->dx('type').' = "20" )',NULL,FALSE);
		return $this->db->get('deposits')->row()->amount?:0;
	}

	function get_group_contribution_transfer($id = 0,$group_id=0){
		$this->select_all_secure('contribution_transfers');
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where('id',$id);
		return $this->db->get('contribution_transfers')->row();
	}

	function get_group_member_contribution_transfers($member_id = 0,$group_id = 0){
		$this->select_all_secure('contribution_transfers');
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		return $this->db->get('contribution_transfers')->result();
	}

	function get_group_contribution_transfers($filter_parameters = array(),$group_id = 0){
		$this->select_all_secure('contribution_transfers');
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('transfer_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('transfer_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
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
		}

		if(isset($filter_parameters['transfer_to']) && $filter_parameters['transfer_to']){
			$this->db->where($this->dx('transfer_to')." = '".$filter_parameters['transfer_to']."' ",NULL,FALSE);
		}


		if(isset($filter_parameters['contribution_from_id']) && $filter_parameters['contribution_from_id']){
			$contribution_list = '0';
			$contributions = $filter_parameters['contribution_from_id'];
			$count = 1;
			foreach($contributions as $contribution_from_id){
				if($contribution_from_id){
					if($count==1){
						$contribution_list = '"'.$contribution_from_id.'"';
					}else{
						$contribution_list .= ',"'.$contribution_from_id.'"';
					}
					$count++;
				}
			}
			if($contribution_list){
        		$this->db->where($this->dx('contribution_from_id').' IN ('.$contribution_list.')',NULL,FALSE);
			}
		}


		if(isset($filter_parameters['member_to_id']) && $filter_parameters['member_to_id']){
			if(isset($filter_parameters['member_to_id']) && $filter_parameters['member_to_id']){
				$member_list = '0';
				$members = $filter_parameters['member_to_id'];
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
	        		$this->db->where($this->dx('member_to_id').' IN ('.$member_list.')',NULL,FALSE);
				}
			}
		}


		if(isset($filter_parameters['member_transfer_to']) && $filter_parameters['member_transfer_to']){
			$this->db->where($this->dx('member_transfer_to')." = '".$filter_parameters['member_transfer_to']."' ",NULL,FALSE);
		}

		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->order_by($this->dx('transfer_date'),'DESC',FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('contribution_transfers')->result();
	}

	function get_group_contribution_transfers_to_loan($group_id = 0){
		$this->select_all_secure('contribution_transfers');
		$this->db->where($this->dx('transfer_to').' ="3" ',NULL,FALSE);
		$this->db->where($this->dx('loan_to_id').' >= 1 ',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->order_by($this->dx('transfer_date'),'ASC',FALSE);
		return $this->db->get('contribution_transfers')->result();
	}

	function get_group_contribution_transfers_to_loan_per_loan_array($group_id = 0,$from = 0,$to = 0){
		$arr = array();
		$this->select_all_secure('contribution_transfers');
		$this->db->where($this->dx('transfer_to').' ="3" ',NULL,FALSE);
		$this->db->where($this->dx('loan_to_id').' >= 1 ',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('transfer_date').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('transfer_date').' <= "'.$to.'"',NULL,FALSE);
		}
		$this->db->order_by($this->dx('transfer_date'),'ASC',FALSE);
		$contribution_transfers = $this->db->get('contribution_transfers')->result();
		foreach($contribution_transfers as $contribution_transfer):
			$arr[$contribution_transfer->loan_to_id][] = $contribution_transfer;
		endforeach;
		return $arr;
	}

	function count_group_contribution_transfers($filter_parameters = array(),$group_id = 0){
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('transfer_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('transfer_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
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
		}

		if(isset($filter_parameters['transfer_to']) && $filter_parameters['transfer_to']){
			$this->db->where($this->dx('transfer_to')." = '".$filter_parameters['transfer_to']."' ",NULL,FALSE);
		}

		if(isset($filter_parameters['contribution_from_id']) && $filter_parameters['contribution_from_id']){
			$contribution_list = '0';
			$contributions = $filter_parameters['contribution_from_id'];
			$count = 1;
			foreach($contributions as $contribution_from_id){
				if($contribution_from_id){
					if($count==1){
						$contribution_list = '"'.$contribution_from_id.'"';
					}else{
						$contribution_list .= ',"'.$contribution_from_id.'"';
					}
					$count++;
				}
			}
			if($contribution_list){
        		$this->db->where($this->dx('contribution_from_id').' IN ('.$contribution_list.')',NULL,FALSE);
			}
		}


		if(isset($filter_parameters['member_to_id']) && $filter_parameters['member_to_id']){
			if(isset($filter_parameters['member_to_id']) && $filter_parameters['member_to_id']){
				$member_list = '0';
				$members = $filter_parameters['member_to_id'];
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
	        		$this->db->where($this->dx('member_to_id').' IN ('.$member_list.')',NULL,FALSE);
				}
			}
		}

		if(isset($filter_parameters['member_transfer_to']) && $filter_parameters['member_transfer_to']){
			$this->db->where($this->dx('member_transfer_to')." = '".$filter_parameters['member_transfer_to']."' ",NULL,FALSE);
		}

		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->count_all_results('contribution_transfers');
	}


	function group_total_deposits($group_id=0){
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('deposit_date').'>="'.strtotime('31st Dec 2015').'"',NULL,FALSE);
		return $this->db->count_all_results('deposits')?:0;
	}

	function get_total_deposits($date = '' , $paying_group_ids = array()){
		//print_r(date('Y-m-d',$date)); die();
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount'
		));
		if($date){
			$this->db->where($this->dx('deposit_date').' >= "'.$date.'"',NULL,FALSE);
		}	
		if(empty($paying_group_ids)){
			$this->db->where($this->dx('group_id ').' IN ( 0 ) ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id ').' IN ('.implode(',',$paying_group_ids).') ',NULL,FALSE);
		}	
		$this->db->where($this->dx('active')."= '1'",NULL,FALSE);
		return $this->db->get('deposits')->row();
	}
	function get_total_deposits_by_month_array_tests($from = 0 ,$to ,$paying_group_ids){
		$arr = array();
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as sum',
				$this->dx('amount').' as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%c') as month "
			)
		);
		$this->db->where($this->dx('created_on')." >= ".$from,NULL,FALSE);
        $this->db->where($this->dx('created_on')." <= ".$to,NULL,FALSE);
        $this->db->where($this->dx('active')."= '1'",NULL,FALSE);
		//$this->db->where($this->dx('type').' = "2"',NULL,FALSE);
		if(empty($paying_group_ids)){
			$this->db->where($this->dx('group_id ').' IN ( 0 ) ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id ').' IN ('.implode(',',$paying_group_ids).') ',NULL,FALSE);
		}
		$this->db->order_by($this->dx('created_on'),'ASC',FALSE);
        $this->db->group_by(
        	array(
        		'year',
        		'month'
        	)
        );
		$result = $this->db->get('deposits')->result();
		foreach($result as $row){
			$arr[$row->year][$row->month] = $row->amount;
		}
		return $arr;
	}
	function get_total_deposits_of_that_month($from ='' ,$to ='' ,$paying_group_ids = array()){
		$arr = array();	
		//$this->select_all_secure('withdrawals');	
		$this->db->select(
			array(
				$this->dx('amount').' as amount',
				$this->dx('group_id').' as group_id',
				$this->dx('member_id  ').' as member_id',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%c') as month "
			)
		);
		//$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')."),'%Y  %M') = '" . date('Y  F',$from) . "'", NULL, FALSE);
		$this->db->where($this->dx('created_on')." >= ".$from,NULL,FALSE);
        $this->db->where($this->dx('created_on')." <= ".$to,NULL,FALSE);
        $this->db->where($this->dx('active')."= '1'",NULL,FALSE);
		if(empty($paying_group_ids)){
			$this->db->where($this->dx('group_id ').' IN ( 0 ) ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id ').' IN ('.implode(',',$paying_group_ids).') ',NULL,FALSE);
		}
		$result = $this->db->get('deposits')->result();
		foreach($result as $row){
			$arr[$row->year][$row->month] = $row->amount;
		}
		return $result;
		//return $arr;
	}

	function get_group_member_deposit_reconciled_counts_array(){
		$this->db->select(
			array(
				$this->dx('deposits.group_id').' as group_id ',
				'COUNT(DISTINCT'.$this->dx('deposits.member_id').') as member_count ',
			)
		);
		$this->db->where($this->dx('transaction_alert_id')." > 0 ",NULL,FALSE);
		$this->db->where($this->dx('member_id')." > 0 ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		$this->db->group_by(array($this->dx("group_id")));
		$deposits = $this->db->get('deposits')->result();
		$arr = array();
		foreach($deposits as $deposit):
			$arr[$deposit->group_id] = $deposit->member_count;
		endforeach;
		return $arr;
	}

	function get_group_member_total_contributions_back_dated_paid_per_contribution_array($group_id = 0,$to = 0,$from = 0){
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
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "7" )',NULL,FALSE);
		$this->db->group_by(array($this->dx("member_id"),$this->dx("contribution_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit){
			$arr[$deposit->member_id][$deposit->contribution_id] = $deposit->amount;
		}
		return $arr;
	}

	function get_group_member_total_fines_paid_back_dated_paid_per_fine_category_array($group_id = 0,$to = 0,$from = 0){
		$arr = array();
		$fine_category = $this->fine_categories_m->get_group_back_dating_fine_category();
		foreach ($this->group_member_options as $member_id => $name){
			$arr[$member_id][$fine_category->id] = 0;
		}
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('member_id').' as member_id',
				$this->dx('fine_category_id').' as fine_category_id',
				)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "7" )',NULL,FALSE);
		$this->db->group_by(array($this->dx("member_id"),$this->dx("fine_category_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit){
			$arr[$deposit->member_id][$deposit->fine_category_id] = $deposit->amount;
		}
		return $arr;
	}

	function get_group_member_total_fines_per_fine_category_array($group_id = 0,$to = 0,$from = 0,$group_member_id = 0){
		$arr = array();
		$fine_category_options = $this->fine_categories_m->get_group_options();
		if($group_member_id){
			foreach($fine_category_options as $fine_category_id => $name):
				$arr[$group_member_id][$fine_category_id] = 0;
			endforeach;
		}else{
			foreach($fine_category_options as $fine_category_id => $name):
				foreach ($this->group_member_options as $member_id => $name){
					$arr[$member_id][$fine_category_id] = 0;
				}
			endforeach;
		}
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('member_id').' as member_id',
				$this->dx('fine_category_id').' as fine_category_id',
				)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}
		//$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "7" )',NULL,FALSE);
		$this->db->group_by(array($this->dx("member_id"),$this->dx("fine_category_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit){
			$arr[$deposit->member_id][$deposit->fine_category_id] = $deposit->amount;
		}
		return $arr;
	}

	function get_group_member_total_contribution_fines_per_contribution_array($group_id = 0,$to = 0,$from = 0,$member_id = 0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		if($member_id){
			foreach($contribution_options as $contribution_id => $name):
				$arr[$member_id][$contribution_id] = 0;
			endforeach;
		}else{
			foreach($contribution_options as $contribution_id => $name):
				foreach ($this->group_member_options as $member_id => $name){
					$arr[$member_id][$contribution_id] = 0;
				}
			endforeach;
		}
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('member_id').' as member_id',
				$this->dx('contribution_id').' as contribution_id',
				)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}
		//$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "7" )',NULL,FALSE);
		$this->db->group_by(array($this->dx("member_id"),$this->dx("contribution_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit){
			$arr[$deposit->member_id][$deposit->contribution_id] = $deposit->amount;
		}
		return $arr;
	}

	function get_group_back_dating_contributions($group_id = 0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('deposits')->result();
	}

	function get_group_back_dating_fines($group_id = 0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('deposits')->result();
	}

	function get_group_member_total_back_dated_loans_paid_per_array($group_id = 0){
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
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (17,18,19,20) ',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "7" )',NULL,FALSE);
		$this->db->group_by(array($this->dx("member_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit){
			$arr[$deposit->member_id] = $deposit->amount;
		}
		return $arr;
	}

	function get_group_back_dating_loans_paid_deposits($group_id = 0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (17,18,19,20) ',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('deposits')->result();
	}

	function get_group_back_dating_asset_sales($group_id = 0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (33,34,35,36) ',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('deposits')->result();
	}

	function get_group_back_dating_group_loans_borrowed_deposits($group_id = 0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (21,22,23,24) ',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('deposits')->result();
	}

	function get_group_back_dating_stock_sales_objects_array($group_id = 0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (25,26,27,28) ',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		//$this->db->group_by(array($this->dx("stock_id")));
		$stock_sales = $this->db->get('deposits')->result();
		$arr = array();
		foreach($stock_sales as $stock_sale):
			$arr[$stock_sale->stock_id] = $stock_sale;
		endforeach;
		return $arr;
	}

	function get_group_back_dating_asset_sale_objects_array($group_id = 0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (33,34,35,36) ',NULL,FALSE);

		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		//$this->db->group_by(array($this->dx("stock_id")));
		$asset_sales = $this->db->get('deposits')->result();
		$arr = array();
		foreach($asset_sales as $asset_sale):
			$arr[$asset_sale->asset_id] = $asset_sale;
		endforeach;
		return $arr;
	}

	function get_group_total_back_dated_income_per_income_category_array(){
		$income_category_options = $this->income_categories_m->get_group_income_category_options();
		$arr = array();
		foreach($income_category_options as $income_category_id => $income_category_name):
			$arr[$income_category_id] = 0;
		endforeach;
		$this->db->select(
			array(
				'sum('.$this->dx('amount').') as amount',
				$this->dx('income_category_id').' as income_category_id',
			)
		);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (13,14,15,16) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1"',NULL,FALSE);
		$this->db->group_by(
        	array(
        		$this->dx('income_category_id'),
        	)
        );
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit):
			$arr[$deposit->income_category_id] = $deposit->amount;
		endforeach;
		return $arr;
	}

	function get_group_back_dating_income_deposits($group_id = 0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (13,14,15,16) ',NULL,FALSE);

		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		//$this->db->group_by(array($this->dx("stock_id")));
		return $this->db->get('deposits')->result();
	}

	function get_group_back_dating_incoming_account_transfers($group_id = 0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (37,38,39,40) ',NULL,FALSE);

		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		//$this->db->group_by(array($this->dx("stock_id")));
		return $this->db->get('deposits')->result();
	}

	function get_group_back_dating_incoming_account_transfer_amounts_array($group_id=0){
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
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (37,38,39,40) ',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		$this->db->group_by(array($this->dx("account_id")));
		//$this->db->where('('.$this->dx('type').' = "37" OR '.$this->dx('type').' = "38" OR '.$this->dx('type').' = "39" OR '.$this->dx('type').' = "40" )',NULL,FALSE);
		$account_transfers = $this->db->get('deposits')->result();
		foreach ($account_transfers as $account_transfer) {
			# code...
			$arr[$account_transfer->account_id] = $account_transfer->amount;
		}
		return $arr;
	}


    function update_group_back_dating_deposits_cut_off_date($group_id = 0,$input = array()){
        $where = " ".$this->dx('is_a_back_dating_record')." = '1' AND ".$this->dx('group_id')." = '".$group_id."' AND ".$this->dx('active')." = '1' ;";
        if($this->update_where($where,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function update_group_back_dating_stock_sales_cut_off_date($group_id = 0,$input = array()){
        $where = " ".$this->dx('is_a_back_dating_record')." = '1' AND ".$this->dx('group_id')." = '".$group_id."' AND ".$this->dx('active')." = '1' ;";
        if($this->update_stock_sales_where($where,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function get_member_deposits_by_member_id_and_contribution_id($group_id=0){
    	$this->db->select(array(
    		'sum('.$this->dx('amount').') as total',
    		$this->dx('member_id').' as member_id',
    		$this->dx('contribution_id').' as contribution_id',
    	));
    	if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		$this->db->where($this->dx('member_id').' >= "1"',NULL,FALSE);
		$this->db->where($this->dx('contribution_id').' >= "1"',NULL,FALSE);
		$this->db->group_by(array(
			$this->dx('member_id'),
			$this->dx('contribution_id'),
		));
		$results = $this->db->get('deposits')->result();
		$arr = array();
		if($results){
			foreach ($results as $result) {
				$arr[$result->member_id][$result->contribution_id] = $result->total;
			}
		}
		return $arr;
    }


	function get_total_group_member_deposit_amount($member_id = 0,$group_id = 0){
		$this->select('sum('.$this->dx('deposits.amount').') as amount');
		$this->db->where($this->dx('deposits.type').' IN (1,2,3,7) ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('deposits.group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('deposits.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('deposits.member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('deposits.active').'="1"',NULL,FALSE);
		//$this->db->where($this->dx('contributions.enable_deposit_statement_display').'="1"',NULL,FALSE);
		//$this->db->join('contributions',$this->dx('deposits.contribution_id').' = contributions.id ');
		return $this->db->get('deposits')->row()->amount?:0;
	}

	function get_total_group_member_deposit_amount_by_contribution_array($member_id = 0,$group_id=0){
		$arr = array();
		$this->db->select(
			array(
				'sum('.$this->dx('deposits.amount').') as amount',
				$this->dx('contribution_id').' as contribution_id '
			)
		);
		$this->db->where($this->dx('deposits.type').' IN (1,2,3,7) ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('deposits.group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('deposits.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('deposits.member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('deposits.active').'="1"',NULL,FALSE);
		//$this->db->where($this->dx('contributions.enable_deposit_statement_display').'="1"',NULL,FALSE);
		//$this->db->join('contributions',$this->dx('deposits.contribution_id').' = contributions.id ');
		$this->db->group_by(
        	array(
        		'contribution_id',
        	)
        );
        $result = $this->db->get('deposits')->result();
        foreach($result as $row):
        	$arr[$row->contribution_id] = $row->amount;
        endforeach;
        return $arr;

	}

	function get_member_total_payments($group_id=0,$member_id=0){
		$this->db->select(
			array(
				'sum('.$this->dx('deposits.amount').') as amount',
			)
		);
		if($group_id){
			$this->db->where($this->dx('deposits.group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('deposits.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('deposits.member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('deposits.member_id').' = "'.$this->member->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('deposits.active').'="1"',NULL,FALSE);
		$res = $this->db->get('deposits')->row();
		if($res){
			return $res->amount;
		}
		return FALSE;
	}

	function get_member_total_contribution_payments($group_id=0,$member_id=0,$contribution_list=''){
		$this->db->select(
			array(
				'sum('.$this->dx('deposits.amount').') as amount',
				$this->dx('deposits.contribution_id').' as contribution_id',
			)
		);
		if($group_id){
			$this->db->where($this->dx('deposits.group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('deposits.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('deposits.member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('deposits.member_id').' = "'.$this->member->id.'"',NULL,FALSE);
		}
		if($contribution_list){
			$this->db->where($this->dx('deposits.contribution_id').' IN('.$contribution_list.')',NULL,FALSE);
		}
		$this->db->where($this->dx('deposits.type').' IN(1,2,3,7)',NULL,FALSE);
		$this->db->where($this->dx('deposits.active').'="1"',NULL,FALSE);
		$this->db->group_by(array(
			'contribution_id',
			'amount',
		));
		$results = $this->db->get('deposits')->result();
		$arr = array();
		if($results){
			foreach ($results as $result) {
				if(array_key_exists($result->contribution_id, $arr)){
					$arr[$result->contribution_id]+=$result->amount;
				}else{
					$arr[$result->contribution_id] = $result->amount;
				}
			}
		}
		return $arr;
	}

	function get_member_payments($group_id =0, $member_id =0){
		$this->select_all_secure('deposits');
		if($group_id){
			$this->db->where($this->dx('deposits.group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('deposits.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('deposits.member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('deposits.member_id').' = "'.$this->member->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('deposits.active').'="1"',NULL,FALSE);
		return $this->db->get('deposits')->result();
	}

	function get_online_payment_reference_number(){
		$this->db->select('id');
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		$this->db->limit(1);
		$res =  $this->db->get('online_payment_requests')->row();
		if($res){
			return $res->id;
		}
	}

	function insert_online_payment_request($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('online_payment_requests',$input);
	}

	function update_payment_request($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'online_payment_requests',$input);
	}

	function get_request_by_reference_number($reference_number=0){
		$this->select_all_secure('online_payment_requests');
		$this->db->where($this->dx('reference_number').' = "'.$reference_number.'"',NULL,FALSE);
		return $this->db->get('online_payment_requests')->row();
	}

	function get_online_payment($id=0){
		$this->select_all_secure('online_payment_requests');
		$this->db->where('id',$id);
		$this->db->limit(1);
		return $this->db->get('online_payment_requests')->row();
	}

	function get_all_online_payments($id=0){
		$this->select_all_secure('online_payment_requests');
		return $this->db->get('online_payment_requests')->result();
	}

	function get_loan_repayments($group_id=0){
		$this->select_all_secure('deposits');
		// $this->db->select(array(
		// 	'transaction_alerts.id as transaction_alert_id_main',
		// 	$this->dx('transaction_alerts.reconciled').' as reconciled',
		// ));
		// $this->db->where($this->dx('deposits.loan_repayment_id').' >= "1"',NULL,FALSE);
		// $this->db->where($this->dx('deposits.transaction_alert_id').' >= "1"',NULL,FALSE);
		// $this->db->where($this->dx('deposits.active').' = "1"',NULL,FALSE);
		// $this->db->where($this->dx('deposits.group_id').' >= "'.$group_id.'"',NULL,FALSE);
		// $this->db->where($this->dx('transaction_alerts.reconciled').' ="0"',NULL,FALSE);
		// $this->db->join('transaction_alerts',$this->dx('deposits.transaction_alert_id').' = transaction_alerts.id ');
		$this->db->where('id',74014);
		return $this->db->get('deposits')->result();
	}

	function get_all_voided_deposits(){
        $this->db->select('id');
        $this->db->where('('.$this->dx('active').' = "" OR '.$this->dx('active').' IS NULL OR '.$this->dx('active').' ="" OR '.$this->dx('active').' ="0" )',NULL,FALSE);
        return $this->db->get('deposits')->result();
    }

    function get_group_total_income_per_year_per_income_category_array($group_id = 0,$from=0,$to=0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as total_amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
				$this->dx('income_category_id')." as income_category_id ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);

		$this->db->where($this->dx('type').' IN (13,14,15,16) ',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('deposit_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->group_by(
            array(
                'year',
                'income_category_id',
            )
        );
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit):
            $arr[$deposit->income_category_id][$deposit->year] = ($deposit->total_amount > 0)?$deposit->total_amount:0;
        endforeach;
        return $arr;
	}

	function member_monthly_deposits($group_id=0,$member_id=0,$from=0,$to=0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as total_amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y%m') as year ",
				$this->dx('contribution_id')." as contribution_id ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' = "'.$this->member_id->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);

		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		if($from){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')."),'%Y%m') >= '" . date('Ym',$from) . "'", NULL, FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->group_by(
            array(
                'year',
                'contribution_id',
            )
        );
		$deposits = $this->db->get('deposits')->result();
		return $deposits;
	}

	function member_monthly_loan_deposits($group_id=0,$member_id=0,$from=0,$to=0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as total_amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y%m') as year ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' = "'.$this->member_id->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);

		$this->db->where($this->dx('type').' IN (17,18,19,20) ',NULL,FALSE);
		if($from){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')."),'%Y%m') >= '" . date('Ym',$from) . "'", NULL, FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->group_by(
            array(
                'year',
            )
        );
		$deposits = $this->db->get('deposits')->result();
		return $deposits;
	}

	function get_group_total_loan_processing_income_per_month_array($group_id = 0,$from=0,$to=0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as total_amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%b') as month ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (41,42,43,44) ',NULL,FALSE);
		$this->db->where($this->dx('member_id').' >= "1"',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('deposit_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->group_by(
            array(
                'year',
                'month',
            )
        );
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit):
            $arr[$deposit->month.' '.$deposit->year] = ($deposit->total_amount > 0)?$deposit->total_amount:0;
        endforeach;
        return $arr;
	}

	function get_group_total_income_per_month_array($group_id = 0,$from=0,$to=0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as total_amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%b') as month ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);

		$this->db->where($this->dx('type').' IN (13,14,15,16) ',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('deposit_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->group_by(
            array(
                'year',
                'month',
            )
        );
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit):
            $arr[$deposit->month.' '.$deposit->year] = ($deposit->total_amount > 0)?$deposit->total_amount:0;
        endforeach;
        return $arr;
	}

	function get_group_total_income_per_month_per_income_category_array($group_id = 0,$from=0,$to=0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as total_amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%b') as month ",
				$this->dx('income_category_id')." as income_category_id ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);

		$this->db->where($this->dx('type').' IN (13,14,15,16) ',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('deposit_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->group_by(
            array(
                'year',
                'month',
                'income_category_id',
            )
        );
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit):
            $arr[$deposit->income_category_id][$deposit->month.' '.$deposit->year] = ($deposit->total_amount > 0)?$deposit->total_amount:0;
        endforeach;
        return $arr;
	}

	function get_group_total_miscellaneous_income_per_month_array($group_id = 0,$from=0,$to=0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as total_amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%b') as month ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);

		$this->db->where($this->dx('type').' IN (9,10,11,12) ',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('deposit_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->group_by(
            array(
                'year',
                'month',
            )
        );
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit):
            $arr[$deposit->month.' '.$deposit->year] = ($deposit->total_amount > 0)?$deposit->total_amount:0;
        endforeach;
        return $arr;
	}

	function get_group_total_contributions_per_contribution_per_month_array($group_id = 0,$months_array = array()){
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
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%b') as month ",
				$this->dx('contribution_id').' as contribution_id',
			)
		);

		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->group_by(
			array(
				$this->dx("contribution_id"),
				'year',
				'month',
			)
		);
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit){
			$arr[$deposit->contribution_id][$deposit->month.' '.$deposit->year] = $deposit->amount;
		}
		return $arr;
	}

	function get_group_total_fines_per_month($group_id = 0){
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%b') as month ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (4,5,6,8) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->group_by(
        	array(
        		'year',
        		'month',
        	)
        );
		return $deposits = $this->db->get('deposits')->result();
	}

	function get_group_total_contributions_per_month($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		//echo $contribution_id_list;
		//die;
		$this->db->select(
			array(
				' SUM('.$this->dx('amount').') as amount ',

				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%b') as month ",
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
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('deposit_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->group_by(
			array(
				'year',
				'month',
			)
		);
		$result = $this->db->get('deposits')->result();
		return $result;
	}

	function get_total_deposit_amount_from_group_ids($group_ids = array()){
		$this->db->select(array(
			'SUM('.$this->dx('amount').') as amount',
		));
		if(empty($group_ids)){
    		$this->db->where($this->dx('group_id').' IN (0)',NULL,FALSE);
		}else{
    		$this->db->where($this->dx('group_id').' IN ('.implode(',',$group_ids).')',NULL,FALSE);
		}
		$result = $this->db->get('deposits')->row();		
		return $result->amount?$result->amount:0;
	}


	function get_group_member_total_contributions_per_year_per_month($group_id = 0,$from = 0,$to = 0,$contribution_id_list = ""){
		//echo $contribution_id_list;
		//die;
		$this->db->select(
			array(
				$this->dx('amount').' as amount ',
				$this->dx('member_id').' as member_id',
				$this->dx('deposit_date').' as deposit_date',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('deposit_date')." ),'%c') as month ",
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
		$this->db->where($this->dx('type').' IN (1,2,3,7) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		//here
		if($from){
			// $this->db->where($this->dx('deposit_date').' >="'.$from.'"',NULL,FALSE);
		}
		if($to){
			// $this->db->where($this->dx('deposit_date').' <="'.$to.'"',NULL,FALSE);
		}
		/*$this->db->group_by(
        	array(
        		'member_id',
        		'year',
        		'month'
        	)
        );*/
		$results = $this->db->get('deposits')->result();
		$arr = array();
		foreach($results as $row){
			$arr[$row->member_id][$row->year][$row->month] = 0;
		}
		foreach($results as $row){
			$arr[$row->member_id][$row->year][$row->month] += currency($row->amount);
		}
		return $arr;
	}
	

}