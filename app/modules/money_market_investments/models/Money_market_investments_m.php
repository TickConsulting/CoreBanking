<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Money_market_investments_m extends MY_Model {

	protected $_table = 'money_market_investments';

	public function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->install();
	}

	public function install(){
		$this->db->query("
		create table if not exists money_market_investments(
			id int not null auto_increment primary key,
			`investment_institution_name` blob,
			`withdrawal_account_id` blob,
			`deposit_account_id` blob,
			`investment_date` blob,
			`cash_in_date` blob,
			`investment_amount` blob,
			`cash_in_amount` blob,
			`is_closed` blob,
			`group_id` blob,
			`description` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	}

	public function get($id = 0,$group_id=0){
		$this->select_all_secure('money_market_investments');
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->get('money_market_investments')->row();
	}

	public function insert($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('money_market_investments',$input);
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'money_market_investments',$input);
    }

    function update_where($where = "",$input = array()){
        return $this->update_secure_where($where,'money_market_investments',$input);
    }

	public function get_group_money_market_investment($id = 0,$group_id = 0){
		$this->select_all_secure('money_market_investments');
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		return $this->db->get('money_market_investments')->row();
	}

	public function get_group_money_market_investment_options($group_id=0,$group_currency = 'KES'){
		$arr = array();
		$this->db->select(
			array(
				'id',
				$this->dx('investment_institution_name').' as investment_institution_name ',
				$this->dx('investment_amount').' as investment_amount ',
				$this->dx('investment_date').' as investment_date ',
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$money_market_investments = $this->db->get('money_market_investments')->result();
		foreach($money_market_investments as $money_market_investment){
			$arr[$money_market_investment->id] = $money_market_investment->investment_institution_name.' ( '.$group_currency.' '.number_to_currency($money_market_investment->investment_amount).' invested on '.timestamp_to_date($money_market_investment->investment_date,TRUE).')';
		}
		return $arr;
	}

	public function get_group_open_money_market_investment_options($group_id=0,$group_currency = 'KES'){
		$arr = array();
		$this->db->select(
			array(
				'id',
				$this->dx('investment_institution_name').' as investment_institution_name ',
				$this->dx('investment_amount').' as investment_amount ',
				$this->dx('investment_date').' as investment_date ',
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('is_closed').'="0"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$money_market_investments = $this->db->get('money_market_investments')->result();
		foreach($money_market_investments as $money_market_investment){
			$arr[$money_market_investment->id] = $money_market_investment->investment_institution_name.' ( '.$group_currency.' '.number_to_currency($money_market_investment->investment_amount).' invested on '.timestamp_to_date($money_market_investment->investment_date,TRUE).')';
		}
		return $arr;
	}

	public function get_group_money_market_investments($filter_parameters = array(),$group_id=0){
		$this->select_all_secure('money_market_investments');
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('investment_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('investment_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
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
        		$this->db->where($this->dx('withdrawal_account_id').' IN ('.$account_list.')',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['money_market_investments']) && $filter_parameters['money_market_investments']){
			$money_market_investment_list = '0';
			$money_market_investments = $filter_parameters['money_market_investments'];
			$count = 1;
			foreach($money_market_investments as $money_market_investment_id){
				if($money_market_investment_id){
					if($count==1){
						$money_market_investment_list = '"'.$money_market_investment_id.'"';
					}else{
						$money_market_investment_list .= ',"'.$money_market_investment_id.'"';
					}
					$count++;
				}
			}
			if($money_market_investment_list){
        		$this->db->where(' id IN ('.$money_market_investment_list.')',NULL,FALSE);
			}
		}
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->order_by($this->dx('investment_date'),'DESC',FALSE);
		return $this->db->get('money_market_investments')->result();
	}

	public function count_group_money_market_investments($filter_parameters = array(),$group_id=0){
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('investment_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('investment_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
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
        		$this->db->where($this->dx('withdrawal_account_id').' IN ('.$account_list.')',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['money_market_investments']) && $filter_parameters['money_market_investments']){
			$money_market_investment_list = '0';
			$money_market_investments = $filter_parameters['money_market_investments'];
			$count = 1;
			foreach($money_market_investments as $money_market_investment_id){
				if($money_market_investment_id){
					if($count==1){
						$money_market_investment_list = '"'.$money_market_investment_id.'"';
					}else{
						$money_market_investment_list .= ',"'.$money_market_investment_id.'"';
					}
					$count++;
				}
			}
			if($money_market_investment_list){
        		$this->db->where(' id IN ('.$money_market_investment_list.')',NULL,FALSE);
			}
		}
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		return $this->db->count_all_results('money_market_investments');
	}

	function get_group_back_dated_past_money_market_investment(){
		$this->select_all_secure('money_market_investments');
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('is_closed').'="1"',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').'="1"',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('money_market_investments')->row();
	}

	function get_group_back_dated_ongoing_money_market_investment(){
		$this->select_all_secure('money_market_investments');
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('is_closed').'="0"',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').'="1"',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('money_market_investments')->row();
	}

	 function update_group_back_dating_money_market_investments_cut_off_date($group_id = 0,$input = array()){
        $where = " ".$this->dx('is_a_back_dating_record')." = '1' AND ".$this->dx('group_id')." = '".$group_id."' AND ".$this->dx('active')." = '1' ;";
        if($this->update_where($where,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function get_group_total_money_market_interest($group_id = 0,$from = 0,$to = 0){
    	$this->db->select(
    		array(
    			"SUM(".$this->dx('cash_in_amount').") - SUM(".$this->dx('investment_amount').") as interest",
    		)
    	);
    	if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
    	}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'" ',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('cash_in_date').' >= "'.$from.'" ',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('cash_in_date').' <= "'.$to.'" ',NULL,FALSE);
		}
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('is_closed').'="1"',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('money_market_investments')->row()->interest;
    }

    function get_group_total_money_market_investment(){
    	$this->db->select(
    		array(
    			"SUM(".$this->dx('investment_amount').") as amount",
    		)
    	);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('is_closed').'="0"',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('money_market_investments')->row()->amount;
    }

    function get_group_total_money_market_interest_per_year_array($group_id = 0){
    	$arr = array();
    	$this->db->select(
    		array(
    			"SUM(".$this->dx('cash_in_amount').") - SUM(".$this->dx('investment_amount').") as interest",
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('cash_in_date')." ),'%Y') as year ",

    		)
    	);
    	if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
    	}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'" ',NULL,FALSE);
		}
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('is_closed').'="1"',NULL,FALSE);
		$this->db->group_by(
            array(
                'year',
            )
        );
		$money_market_investments = $this->db->get('money_market_investments')->result();
		foreach($money_market_investments as $money_market_investment):
			$arr[$money_market_investment->year] = $money_market_investment->interest;
		endforeach;
		return $arr;
    }

    function get_group_total_principal_money_market_investment_out_per_year_array($group_id = 0){
    	$arr = array();
        $cash_ins_array = array();
    	$this->select_all_secure('money_market_investments');
    	if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
    	}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'" ',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);

        //$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('investment_date')."),'%Y') !=  DATE_FORMAT(FROM_UNIXTIME(".$this->dx('cash_in_date')."),'%Y')",NULL,FALSE);

		$this->db->order_by($this->dx('investment_date'),'ASC',FALSE);
		$money_market_investments = $this->db->get('money_market_investments')->result();

		foreach($money_market_investments as $money_market_investment):
			if(isset($arr[date('Y',$money_market_investment->investment_date)])){
				$arr[date('Y',$money_market_investment->investment_date)] += $money_market_investment->investment_amount;
			}else{
				$arr[date('Y',$money_market_investment->investment_date)] = $money_market_investment->investment_amount;
			}
		endforeach;

		foreach($money_market_investments as $money_market_investment):
			if(isset($arr[date('Y',$money_market_investment->cash_in_date)])){
				$arr[date('Y',$money_market_investment->cash_in_date)] -= $money_market_investment->investment_amount;
			}
		endforeach;

		foreach($arr as $key => $value):
			if(isset($arr[($key - 1)])){
				$arr[$key] += $arr[($key - 1)];
			}
		endforeach;

		$current_year = date('Y');
		foreach($money_market_investments as $money_market_investment):
			if($money_market_investment->is_closed){

			}else{
				$year = date('Y',$money_market_investment->investment_date) + 1;
				for($i = $year; $i <= $current_year; $i++):
					if(isset($arr[$i])){
						$arr[$i] -= $money_market_investment->investment_amount;
					}else{
						$arr[$i] = $money_market_investment->investment_amount;
					}
				endfor;
			}
		endforeach;

		// print_r($arr);
		// die;
		
		return ($arr);

    }

    function get_total_group_money_market_investment_value(){
    	$this->db->select(
    		array(
    			"SUM(".$this->dx('investment_amount').") as amount",
    		)
    	);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		//$this->db->where($this->dx('is_closed').'="0"',NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('money_market_investments')->row()->amount;
    }

     function get_group_total_principal_money_market_investment_out_per_month_array($group_id = 0){
    	$this->db->select(
        	array(
        		'id',
        		$this->dx('investment_date')." as investment_date",
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('investment_date')." ),'%Y') as investment_year ",	
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('investment_date')." ),'%b') as investment_month ",	
        		$this->dx('cash_in_date')." as cash_in_date",
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('cash_in_date')." ),'%Y') as cash_in_year ",	
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('cash_in_date')." ),'%b') as cash_in_month ",	
        		$this->dx('investment_amount')." as investment_amount",
        		$this->dx('cash_in_amount')." as cash_in_amount",
        		$this->dx('is_closed')." as is_closed",
        	)
        );
		$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->order_by($this->dx('investment_date'),'ASC',FALSE);
		$money_market_investments = $this->db->get('money_market_investments')->result();
		$first_month = date('M Y');
		foreach($money_market_investments as $money_market_investment):
			$first_month = $money_market_investment->investment_month.' '.$money_market_investment->investment_year;
			break;
		endforeach;

		$current_month = date('M Y');
	
		$months_array = generate_months_array(strtotime($first_month),strtotime($current_month));
		$arr = array();
		foreach($months_array as $month):
			$arr[$month] = 0;
		endforeach;

		foreach($money_market_investments as $money_market_investment):
			if(isset($arr[$money_market_investment->investment_month.' '.$money_market_investment->investment_year])){
				$arr[$money_market_investment->investment_month.' '.$money_market_investment->investment_year] += $money_market_investment->investment_amount;
			}else{
				$arr[$money_market_investment->investment_month.' '.$money_market_investment->investment_year] = $money_market_investment->investment_amount;
			}
		endforeach;

		foreach($money_market_investments as $money_market_investment):
			if($money_market_investment->is_closed){
				$arr[$money_market_investment->cash_in_month.' '.$money_market_investment->cash_in_year] -= $money_market_investment->investment_amount;
			}
		endforeach;
		foreach($months_array as $month):
			if($month == $first_month){

			}else{
				$previous_month = date('M Y',strtotime('-1 month',strtotime($month)));
				if(array_key_exists($previous_month, $arr)){
					$arr[$month] += $arr[($previous_month)];
				}				
			}
		endforeach;

		return $arr;

    }

    function get_group_total_money_market_interest_per_month_array($group_id = 0){
    	$arr = array();
    	$this->db->select(
    		array(
    			"SUM(".$this->dx('cash_in_amount').") - SUM(".$this->dx('investment_amount').") as interest",
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('cash_in_date')." ),'%Y') as year ",
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('cash_in_date')." ),'%b') as month ",

    		)
    	);
    	if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'" ',NULL,FALSE);
    	}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'" ',NULL,FALSE);
		}
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where($this->dx('is_closed').'="1"',NULL,FALSE);
		$this->db->group_by(
            array(
                'year',
                'month',
            )
        );
		$money_market_investments = $this->db->get('money_market_investments')->result();
		foreach($money_market_investments as $money_market_investment):
			$arr[$money_market_investment->month.' '.$money_market_investment->year] = $money_market_investment->interest;
		endforeach;
		return $arr;
    }


}
		