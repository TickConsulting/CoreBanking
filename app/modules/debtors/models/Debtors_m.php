<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Debtors_m extends MY_Model{

	protected $_table = 'debtors';

	function __construct(){
		parent::__construct();
		$this->install();
	}

	function install(){
		
		$this->db->query("
			create table if not exists debtors(
				id int not null auto_increment primary key,
				`name` blob,
				`email` blob,
				`phone` blob,
				`description` blob,
				`group_id` blob,
				`active` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_by` blob,
				`modified_on` blob
			)"
		);


		$this->db->query("
			create table if not exists debtor_loans(
				id int not null auto_increment primary key,
			  	`loan_type` blob,
			  	`debtor_id` blob,
			  	`group_id` blob,
			  	`disbursement_date` blob,
			  	`loan_end_date` blob,
			  	`loan_amount` blob,
			  	`account_id` blob,
			  	`repayment_period` blob,
			  	`interest_rate` blob,
			  	`interest_type` blob,
			  	`grace_period` blob,
			  	`grace_period_end_date` blob,
			  	`sms_notifications_enabled` blob,
			  	`sms_template` blob,
			  	`email_notifications_enabled` blob,
			  	`enable_loan_fines` blob,
			  	`loan_fine_type` blob,
			  	`fixed_fine_amount` blob,
			  	`fixed_amount_fine_frequency` blob,
			  	`percentage_fine_rate` blob,
			  	`percentage_fine_frequency` blob,
			  	`percentage_fine_on` blob,
			  	`one_off_fine_type` blob,
			  	`one_off_fixed_amount` blob,
			  	`one_off_percentage_rate` blob,
			  	`one_off_percentage_rate_on` blob,
			  	`enable_outstanding_loan_balance_fines` blob,
			  	`outstanding_loan_balance_fine_type` blob,
			  	`outstanding_loan_balance_fine_fixed_amount` blob,
			  	`outstanding_loan_balance_fixed_fine_frequency` blob,
			  	`outstanding_loan_balance_percentage_fine_rate` blob,
			  	`outstanding_loan_balance_percentage_fine_frequency` blob,
			  	`outstanding_loan_balance_percentage_fine_on` blob,
			  	`outstanding_loan_balance_fine_one_off_amount` blob,
			  	`outstanding_loan_balance_fine_date` blob,
			  	`enable_loan_processing_fee` blob,
			  	`loan_processing_fee_type` blob,
			  	`loan_processing_fee_fixed_amount` blob,
			  	`loan_processing_fee_percentage_rate` blob,
			  	`loan_processing_fee_percentage_charged_on` blob,
			  	`enable_loan_fine_deferment` blob,
			  	`enable_loan_guarantors` blob,
			  	`active` blob,
			  	`is_fully_paid` blob,
			  	`created_by` blob,
			  	`created_on` blob,
			  	`modified_by` blob,
			  	`modified_on` blob,
			  	`loan_interest_rate_per` blob,
			  	`custom_interest_procedure` blob,
			  	`is_edited` blob,
			  	`fixed_amount_fine_frequency_on` blob,
			  	`enable_reducing_balance_installment_recalculation` blob,
			  	`is_a_back_dating_record` blob
			)
		");


		$this->db->query("
			create table if not exists debtor_loan_invoices(
				id int not null auto_increment primary key,
			  	`debtor_loan_id` blob,
				`group_id` blob,
				`debtor_id` blob,
				`invoice_no` blob,
				`type` blob,
				`interest_amount_payable` blob,
				`principle_amount_payable` blob,
				`invoice_date` blob,
				`due_date` blob,
				`fine_date` blob,
				`amount_payable` blob,
				`fine_parent_loan_invoice_id` blob,
				`amount_paid` blob,
				`is_sent` blob,
				`disable_fines` blob,
				`status` blob,
				`active` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_on` blob,
				`modified_by` blob,
				`transfer_to` blob,
				`contribution_transfer_id` blob,
				`is_a_back_dating_record` blob
			)
		");




		$this->db->query("
			create table if not exists debtor_loan_repayments(
					id int not null auto_increment primary key,
					`debtor_loan_id` blob,
					`group_id` blob,
					`debtor_id` blob,
					`account_id` blob,
					`account_type` blob,
					`receipt_date` blob,
					`payment_method` blob,
					`amount` blob,
					`status` blob,
					`active` blob,
					`created_by` blob,
					`created_on` blob,
					`modified_on` blob,
					`modified_by` blob,
					`incoming_loan_transfer_invoice_id` blob,
					`incoming_contribution_transfer_id` blob,
					`transfer_from` blob,
  					`is_a_back_dating_record` blob
			)
		");

		$this->db->query("
			create table if not exists debtor_loan_statements(
					id int not null auto_increment primary key,
					`debtor_id` blob,
				  	`group_id` blob,
				  	`transaction_date` blob,
				  	`transaction_type` blob,
				  	`debtor_loan_id` blob,
				  	`debtor_loan_invoice_id` blob,
				  	`debtor_loan_payment_id` blob,
				  	`amount` blob,
				  	`balance` blob,
				  	`active` blob,
				  	`status` blob,
				  	`created_by` blob,
				  	`created_on` blob,
				  	`modified_on` blob,
				  	`modified_by` blob,
				  	`account_id` blob,
				  	`payment_method` blob,
				  	`transfer_to` blob,
				  	`transfer_from` blob,
				  	`is_a_back_dating_record` blob
			)
		");

		$this->db->query("
			create table if not exists debtor_loan_guarantors(
					id int not null auto_increment primary key,
					`debtor_loan_id` blob,
					`group_id` blob,
					`member_id` blob,
					`guaranteed_amount` blob,
					`comment` blob,
					`active` blob,
					`created_by` blob,
					`created_on` blob,
					`modified_on` blob,
					`modified_by` blob
			)
		");
	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('debtors',$input);
	}


	function update($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'debtors',$input);
	}

	function get($id=0,$group_id=0){
		$this->select_all_secure('debtors');
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
		}else{
			// $this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->get('debtors')->row();
	}


	function get_all($group_id=0,$params=array()){
		$this->select_all_secure('debtors');
		$list = '';
		foreach ($params as $key => $value) {
			if(is_array($value)){
				foreach ($value as $key_value => $value_v) {
					if($list){
						$list.=','.$value_v;
					}else{
						$list = $value_v;
					}
				}
				if($list){
					if($key=='id'){
						$this->db->where('id IN('.$list.')',NULL,FALSE);
					}else{
						$this->db->where($this->dx($key).' IN('.$list.')',NULL,FALSE);
					}
				}
			}else{
				if($this->db->field_exists($key,'debtors')){
					if($value){
						if($key=='id'){
							$this->db->where('id',$v);
						}else{
							$this->db->where($this->dx($key).' = "'.$value.'"',NULL,FALSE);
						}
					}
				}
			}
		}
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->get('debtors')->result();
	}

	function get_options($group_id=0){
		$this->select_all_secure('debtors');
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		$debtors = $this->db->get('debtors')->result();

		$arr = array();
		foreach ($debtors as $debtor) {
			if(valid_phone($debtor->phone)){
				$arr[$debtor->id] = $debtor->name.'('.$debtor->phone.')';
			}elseif ($debtor->email) {
				$arr[$debtor->id] = $debtor->name.'('.$debtor->email.')';
			}else{
				$arr[$debtor->id] = $debtor->name;
			}
		}
		return $arr;
	}

	function get_active_options($group_id=0,$show_details = FALSE){
		$this->select_all_secure('debtors');
		$this->db->where($this->dx('active').' ="1"',NULL,FALSE);
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
		}
		$debtors = $this->db->get('debtors')->result();

		$arr = array();
		foreach ($debtors as $debtor) {
			if($show_details){
				if(valid_phone($debtor->phone)){
					$arr[$debtor->id] = $debtor->name.'('.$debtor->phone.')';
				}elseif ($debtor->email) {
					$arr[$debtor->id] = $debtor->name.'('.$debtor->email.')';
				}else{
					$arr[$debtor->id] = $debtor->name;
				}
			}else{
				$arr[$debtor->id] = $debtor->name;
			}
			
		}
		return $arr;
	}

	function get_group_debtor($group_id=0){
		$this->select_all_secure('debtors');
		$this->db->where($this->dx('active').' ="1"',NULL,FALSE);
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->get('debtors')->row();
	}

	function count_group_debtors($group_id=0,$params = array()){
		$list = '';
		foreach ($params as $key => $value) {
			if(is_array($value)){
				foreach ($value as $key_value => $value_v) {
					if($list){
						$list.=','.$value_v;
					}else{
						$list = $value_v;
					}
				}
				if($this->db->field_exists($key,'debtors')){
					if($list){
						if($key=='id'){
							$this->db->where('id IN('.$list.')',NULL,FALSE);
						}else{
							$this->db->where($this->dx($key).' IN('.$list.')',NULL,FALSE);
						}
					}
				}
				
			}else{
				if($this->db->field_exists($key,'debtors')){
					if($value){
						if($key=='id'){
							$this->db->where('id',$v);
						}else{
							$this->db->where($this->dx($key).' = "'.$value.'"',NULL,FALSE);
						}
					}
				}
			}
		}
		if($group_id){
			$this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->count_all_results('debtors')?:0;
	}



	/*******debtor loan***********/

	function insert_loan($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('debtor_loans',$input);
	}


	function update_loan($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'debtor_loans',$input);
	}

	function get_loan($id=0,$group_id=0){
		$this->select_all_secure('debtor_loans');
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
		}else if(isset($this->group)){
			$this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->get('debtor_loans')->row();
	}


	function get_all_loans($group_id = o,$params=array()){
		$this->select_all_secure('debtor_loans');
		$list = '';
		foreach ($params as $key => $value) {
			if(is_array($value)){
				foreach ($value as $key_value => $value_v) {
					if($list){
						$list.=','.$value_v;
					}else{
						$list = $value_v;
					}
				}
				if($this->db->field_exists($key,'debtor_loans')){
					if($list){
						if($key=='id'){
							$this->db->where('id IN('.$list.')',NULL,FALSE);
						}else{
							$this->db->where($this->dx($key).' IN('.$list.')',NULL,FALSE);
						}
					}
				}
				
			}else{
				if($this->db->field_exists($key,'debtor_loans')){
					if($value){
						if($key=='id'){
							$this->db->where('id',$v);
						}else{
							$this->db->where($this->dx($key).' = "'.$value.'"',NULL,FALSE);
						}
					}
				}else{
					if($key == 'to' &&($params['from']&&$params['to'])){
						$this->db->where($this->dx('disbursement_date').' >= "'.$params['from'].'"',NULL,FALSE);
						$this->db->where($this->dx('disbursement_date').' <= "'.$params['to'].'"',NULL,FALSE);
					}
				}
			}
		}
		$this->db->order_by($this->dx('disbursement_date'),'DESC',FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		$this->db->where($this->dx('active').' ="1" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->get('debtor_loans')->result();
	}

	function count_group_debtor_loans($group_id=0,$params = array()){
		$list = '';
		foreach ($params as $key => $value) {
			if(is_array($value)){
				foreach ($value as $key_value => $value_v) {
					if($list){
						$list.=','.$value_v;
					}else{
						$list = $value_v;
					}
				}
				if($this->db->field_exists($key,'debtor_loans')){
					if($list){
						if($key=='id'){
							$this->db->where('id IN('.$list.')',NULL,FALSE);
						}else{
							$this->db->where($this->dx($key).' IN('.$list.')',NULL,FALSE);
						}
					}
				}
				
			}else{
				if($this->db->field_exists($key,'debtor_loans')){
					if($value){
						if($key=='id'){
							$this->db->where('id',$v);
						}else{
							$this->db->where($this->dx($key).' = "'.$value.'"',NULL,FALSE);
						}
					}
				}else{
					if($key == 'to' &&($params['from']&&$params['to'])){
						$this->db->where($this->dx('disbursement_date').' >= "'.$params['from'].'"',NULL,FALSE);
						$this->db->where($this->dx('disbursement_date').' <= "'.$params['to'].'"',NULL,FALSE);
					}
				}
			}
		}
		$this->db->where($this->dx('active').' ="1" ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->count_all_results('debtor_loans')?:0;
	}

	function get_active_debtor_loans_option($debtor_id=0){
        $this->select_all_secure('debtor_loans');
        $this->db->where($this->dx('debtor_id').'="'.$debtor_id.'"',NULL,FALSE);
        $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where('('.$this->dx('is_fully_paid').'="" OR '.$this->dx('is_fully_paid').' IS NULL OR '.$this->dx('is_fully_paid').'="0")',NULL,FALSE);
        $loans = $this->db->get('debtor_loans')->result(); 
        $arr=array();
        foreach ($loans as $value){
            $arr[$value->id] = $this->group_currency.' '.number_to_currency($value->loan_amount).' - Disbursed '.timestamp_to_date($value->disbursement_date,TRUE);
        }
        return $arr;
    }

    function get_loan_borrowed($id){
        $this->db->select(array($this->dx('loan_amount').' as amount_borrowed'));
        $this->db->where('id',$id);
        $loan_amount = $this->db->get('debtor_loans')->row();
        if($loan_amount){
            return $loan_amount->amount_borrowed;
        }else{
            return 0;
        }
    }

    function get_group_loan_options(){
        $this->select_all_secure('debtor_loans');
        $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $loans = $this->db->get('debtor_loans')->result(); 
        $arr=array();
        foreach ($loans as $value) {
            $arr[$value->id] = $this->group_currency.' '.number_to_currency($value->loan_amount).' - Disbursed '.timestamp_to_date($value->disbursement_date,TRUE);
        }
        return $arr;
    }

    function get_loan_and_debtor($id=0,$group_id=0){
    	$this->select_all_secure('debtor_loans');
        $this->db->select(array(
                $this->dx('debtors.name').' as name',
                $this->dx('debtors.email').' as email',
                $this->dx('debtors.phone').' as phone',
            ));
        $this->db->join('debtors',$this->dx('debtor_loans.debtor_id').' = debtors.id');
        if($group_id){
        	$this->db->where($this->dx('debtor_loans.group_id').' ="'.$group_id.'"',NULL,FALSE);
        }else{
        	$this->db->where($this->dx('debtor_loans.group_id').' ="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where('debtor_loans.id',$id);
        return $this->db->get('debtor_loans')->row();
    }

    function get_many_by($params = array()){   
        $this->select_all_secure('debtor_loans');
        foreach($params as $column_name => $value){
            $column_name = trim($column_name);
            if($column_name=='id'){
                $this->db->where('id',$value);
            }else{
                if($value){
                    $this->db->where($this->dx($column_name).'="'.$value.'"',NULL,FALSE);
                }else{
                    $this->db->where("(".$this->dx($column_name).'="0" OR '.$this->dx($column_name).'="" OR '.$this->dx($column_name).' IS NULL OR '.$this->dx($column_name).' =" " )',NULL,FALSE);
                }
            }
        } 
        // $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').' ="1"',NULL,FALSE);
        $this->db->order_by($this->dx('disbursement_date'),'DESC',FALSE);
        return $this->db->get('debtor_loans')->result();
    }

    function get_total_loaned_amount($group_id=0){
        $this->db->select('sum('.$this->dx('debtor_loans.loan_amount').') as amount');
        $this->db->where($this->dx("debtor_loans.active").'="1"',NULL,FALSE);
        $this->db->where($this->dx("debtor_loans.created_on").'<="'.time().'"',NULL,FALSE);
        $amount = $this->db->get('debtor_loans')->row();
        if($amount){
            return $amount->amount?:0;
        }else{
            return 0;
        }
    }

    function get_loans_to_fine_outstanding_balance($date=0){  
        if($date){}else{
            $date=time();
        }
        $this->select_all_secure('debtor_loans');
        $this->db->select(array(
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('debtor_loans.outstanding_loan_balance_fine_date')." ),'%Y %D %M') as outstanding_balance_fine_date ",
            ));
        $this->db->where($this->dx('debtor_loans.active').'="1"',NULL,FALSE);
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('debtor_loans.outstanding_loan_balance_fine_date')."),'%Y %D %M') = '" . date('Y jS F',$date) . "'", NULL, FALSE);
        $this->db->where('('.$this->dx('is_fully_paid').' IS NULL OR '.$this->dx('is_fully_paid').' ="" OR '.$this->dx('is_fully_paid').' ="0" OR '.$this->dx('is_fully_paid').' = " " )',NULL,FALSE);
        $this->db->where($this->dx('enable_outstanding_loan_balance_fines').'="1"',NULL,FALSE);
        $query = $this->db->get('debtor_loans');
        $query2 = $query->result();
        $query->free_result();
        return $query2;
    } 


    function get_fully_paid_loans_id($group_id=0,$from=0,$to=0){
        $this->db->select('id');
        $this->db->where($this->dx('is_fully_paid').' ="1"',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id')." = '".$this->group->id."' ",NULL,FALSE);
        }
        if($from){
            $this->db->where($this->dx('disbursement_date').'>="'.$from.'"',NULL,FALSE);
        }
        if($to){
            $this->db->where($this->dx('disbursement_date').'<="'.$to.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
        $ids = $this->db->get('debtor_loans')->result();
        $arr = array();
        foreach ($ids as $value) {
            $arr[] = $value->id;
        }
        return $arr;
    }



	/**************debtor loan invoices************/
	function insert_loan_invoice($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('debtor_loan_invoices',$input);
	}

	function insert_many_loan_invoices($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_batch_secure_data('debtor_loan_invoices',$input);
	}

	function insert_batch($input,$skip_validation=FALSE){
        return $this->insert_batch_secure_data('debtor_loan_invoices',$input);
    }

	function get_invoice($id=0){
		$this->select_all_secure('debtor_loan_invoices');
		$this->db->where('id',$id);
		return $this->db->get('debtor_loan_invoices')->row();
	}

	function update_loan_invoices($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'debtor_loan_invoices',$input);
	}

	function update_batch($input,$SKIP_VALIDATION = FALSE){
        return $this->batch_update_secure_data('debtor_loan_invoices',$input);
    }

	function delete_all_invoices($debtor_loan_id=0){
		return $this ->db->query("update debtor_loan_invoices set 
                active=".$this->exa('0').",
                modified_on=".$this->exa(time())."
                where ".$this->dx("debtor_loan_id")." ='".$debtor_loan_id."'"); 
	}

	function calculate_invoice_no($group_id=0){
        $this->db->where($this->dx('debtor_loan_invoices.group_id').'="'.$group_id.'"',NULL,FALSE);
        $count = $this->db->count_all_results('debtor_loan_invoices')?:0;
        return $count + 1;
    }

    function get_past_unsent_invoices($id=0){
    	$this->select_all_secure('debtor_loan_invoices');
        $this->db->select(array(
                    "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('debtor_loan_invoices.invoice_date')." ),'%Y %D %M') as invoice_date2 ",
                ));
        $this->db->where($this->dx('debtor_loan_invoices.debtor_loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where('('.$this->dx('debtor_loan_invoices.is_sent').'="0" OR '.$this->dx('debtor_loan_invoices.is_sent').'IS NULL)',NULL,FALSE);
       	$this->db->where($this->dx('debtor_loans.active').'="1"',NULL,FALSE);
        $this->db->where('('.$this->dx('debtor_loans.is_fully_paid').'="0" OR '.$this->dx('debtor_loans.is_fully_paid').' is NULL OR '.$this->dx('debtor_loans.is_fully_paid').' = "")',NULL,FALSE);
        $this->db->where($this->dx('debtor_loan_invoices.invoice_date').'<',time());
        $this->db->join('debtor_loans', 'debtor_loans.id = '.$this->dx('debtor_loan_invoices.debtor_loan_id'));
        return $this->db->get('debtor_loan_invoices')->result();
    }

   	function get_last_invoice($id=0){
        $this->select_all_secure('debtor_loan_invoices');
        $this->db->where($this->dx('debtor_loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx("active").'="1"',NULL,FALSE);
        $this->db->where($this->dx("type").'="1"',NULL,FALSE);
        $this->db->order_by($this->dx('invoice_date'), 'DESC',FALSE);
        $this->db->limit(1);
        return $this->db->get('debtor_loan_invoices')->row();
    }

    function fine_invoice_list($id=0,$date=0){ 
    	$this->select_all_secure('debtor_loan_invoices');
        $this->db->where($this->dx('debtor_loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx("active").'="1"',NULL,FALSE);
        $this->db->where($this->dx("type").'="1"',NULL,FALSE);
        $this->db->where($this->dx('due_date').' < "'.$date.'"',NULL,FALSE);
        $this->db->where('('.$this->dx('debtor_loan_invoices.disable_fines').'=" " OR '.$this->dx('debtor_loan_invoices.disable_fines').'is NULL OR '.$this->dx('debtor_loan_invoices.disable_fines').' = "")');
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('fine_date')."),'%Y %d %m') = '" . date('Y d m',$date) . "'", NULL, FALSE);
        return $this->db->get('debtor_loan_invoices')->result();  
    }

    function fine_invoice_exists_fixer($id,$date){
        return $this->db->where($this->dx('type').'="2"',NULL,FALSE)
                        ->where($this->dx('fine_parent_loan_invoice_id').'="'.$id.'"',NULL,FALSE)
                        ->where($this->dx('active').'="1"',NULL,FALSE)
                        ->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('invoice_date')."),'%Y %D %M') = '" . date('Y jS F',$date) . "'", NULL, FALSE)
                        ->count_all_results('debtor_loan_invoices') > 0;
    }


    function get_invoice_sent_today($debtor_loan_id=0,$date=0){
        $this->db->select('id');
        $this->db->where($this->dx('debtor_loan_id').' = "'.$debtor_loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('debtor_loan_invoices.active').'="1"',NULL,FALSE);
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('debtor_loan_invoices.invoice_date')."),'%Y %D %M') = '". date('Y jS F',$date) . "'", NULL, FALSE);
        $this->db->where($this->dx('debtor_loan_invoices.type').'="2"',NULL,FALSE);
        return $this->db->get('debtor_loan_invoices')->row();
    }


    function get_total_unpaid_loan_installments($invoice_id=0,$date=0){
        $this->db->select($this->dx('amount_payable').' as amount_payable');
        $this->db->where("id", $invoice_id);
        $amount_payable = $this->db->get("debtor_loan_invoices")->row();
        if($amount_payable){
            $amount_payable = $amount_payable->amount_payable;
        }else{
            $amount_payable=0;
        }
        $this->select_all_secure('debtor_loan_invoices');
        $this->db->where('id',$invoice_id);
        $loan =  $this->db->get('debtor_loan_invoices')->row();
        if($loan){
            $this->select_all_secure('debtor_loans');
            $this->db->where('id',$loan->debtor_loan_id);
            $loan_application = $this->db->get('debtor_loans')->row();
        }else{
            return 0;
        }
        if($loan_application->enable_loan_fine_deferment){
            $this->db->select('sum('.$this->dx('amount_payable').') as amount_payable');
            $this->db->where($this->dx('debtor_loan_id').'="'.$loan->debtor_loan_id.'"',NULL,FALSE);
            $this->db->where($this->dx('invoice_date').' <= "'.$loan->invoice_date.'"',NULL,FALSE);
            $this->db->where($this->dx('type').'="1"',NULL,FALSE);
            $this->db->where($this->dx('active').'="1"',NULL,FALSE);
            $total_amount_payable = $this->db->get('debtor_loan_invoices')->row()->amount_payable;
        }else{
            $this->db->select('sum('.$this->dx('amount_payable').') as amount_payable');
            $this->db->where($this->dx('debtor_loan_id').'="'.$loan->debtor_loan_id.'"',NULL,FALSE);
            $this->db->where($this->dx('invoice_date').' <= "'.$loan->invoice_date.'"',NULL,FALSE);
            $this->db->where($this->dx('active').'="1"',NULL,FALSE);
            $total_amount_payable = $this->db->get('debtor_loan_invoices')->row()->amount_payable;
        }

        $this->select_all_secure('debtor_loan_repayments');
        $this->db->select($this->dx('amount') .' as amount');
        $this->db->where($this->dx('debtor_loan_id').'="'.$loan->debtor_loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('receipt_date').' <= "'.$date.'"',NULL,FALSE);
        $total_amount_paid = $this->db->get('debtor_loan_repayments')->row();
        if($total_amount_paid){
            $total_amount_paid = $total_amount_paid->amount;
        }else{
            $total_amount_paid = 0;
        }
        
        $diff = $total_amount_payable - $total_amount_paid;
        
        if($diff>0){
            if($diff>$amount_payable){
                return $amount_payable;
            }else{
                return $diff;          
            }
        }else{
            return 0;
        }
    }

    function get_outstanding_balance($debtor_id=0, $debtor_loan_id=0, $invoice_date = 0){
        $this->db->select('sum('.$this->dx('amount_payable').') - sum('.$this->dx('amount_paid').') as balance ');
        $this->db->where($this->dx('debtor_id').'="'.$debtor_id.'"',NULL,FALSE);
        $this->db->where($this->dx('debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALsE);
        if ($invoice_date){
            $this->db->where($this->dx('invoice_date').'<"'.$invoice_date.'"',NULL,FALSE);
        }
        return $this->db->get("debtor_loan_invoices")->row()->balance;
    }

    function get_outstanding_loan_balance($debtor_id=0,$debtor_loan_id=0, $invoice_date = 0){
        $this->db->select('sum('.$this->dx('amount_payable').') - sum('.$this->dx('amount_paid').') as balance');
        $this->db->where($this->dx('debtor_id').'="'.$debtor_id.'"',NULL,FALSE);
        $this->db->where($this->dx('debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALsE);
        $this->db->where($this->dx('type').'="1"',NULL,FALsE);
        if ($invoice_date){
            $this->db->where($this->dx('invoice_date').'<"'.$invoice_date.'"',NULL,FALSE);
        }

        return $this->db->get("debtor_loan_invoices")->row()->balance;
    }


    function get_to_update_fixer($id,$date){
        $this->db->where($this->dx('debtor_loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx('type').'="1"',NULL,FALSE);
        $this->db->where($this->dx('due_date').' < "'.$date.'"',NULL,FALSE);
        return $this->db->get('debtor_loan_invoices')->result();
    }

    function get_amount_payable_for_parent($debtor_loan_invoice_id=0){
    	$this->select_all_secure('debtor_loan_invoices');
        $this->db->where('id',$debtor_loan_invoice_id);
        $loan_invoice = $this->db->get('debtor_loan_invoices')->row();

        if($loan_invoice){
        	$this->select_all_secure('debtor_loan_invoices');
            $this->db->select(array(
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('debtor_loan_invoices.due_date')." ),'%Y %D %M') as due_date2 ",
            ));
            $this->db->where('id',$loan_invoice->fine_parent_loan_invoice_id);
            $parent_loan_invoice = $this->db->get('debtor_loan_invoices')->row();

            if($parent_loan_invoice){
                $this->db->select('sum('.$this->dx('amount').') as amount');
                $this->db->where($this->dx('debtor_loan_id').'="'.$loan_invoice->debtor_loan_id.'"',NULL,FALSE);
                $this->db->where($this->dx('active').'="1"',NULL,FALSE); 
                $this->db->where('('.$this->dx('transaction_type').'= "1" OR '.$this->dx('transaction_type').'="2" OR '.$this->dx('transaction_type').' = "3" ) '); 
                $this->db->where($this->dx('transaction_date').' <="'.$parent_loan_invoice->due_date.'"',NULL,FALSE); 
                $amount = $this->db->get('debtor_loan_statements')->row();

                if($amount){
                	return $amount->amount;
                }else{
                	return 0;
                }
            }else{
                return 0;
            }
        }else{
            return 0;
        }

        $this->db->select('sum('.$this->dx('amount').') as amount');
        $this->db->where("debtor_loan_id",$id);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE); 
        $this->db->where('('.$this->dx('transaction_type').' = "1" OR '.$this->dx('transaction_type').' = "2" OR '.$this->dx('transaction_type').' = "3" '); 
        $this->db->where($this->dx('transaction_date').' <="'.$date.'"',NULL,FALSE); 
        $amount = $this->db->get('debtor_loan_statements')->row();

        if($amount){
        	return $amount->amount;
        }
        else{
        	return 0;
        }
    }

    function outstanding_loan_balance_fine_invoice_exists_fixer($debtor_loan_id=0,$date=0){
        $this->db->where($this->dx('type').'= "3"',NULL,FALSE);
        $this->db->where($this->dx('debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALsE);
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')."),'%Y %D %M') = '" . date('Y jS F',$date) . "'", NULL, FALSE);
        $count = $this->db->count_all_results('debtor_loan_invoices');
        return $count;
    }

    function get_loans_interest_and_principle_amount($debtor_loan_id=0){
    	$this->db->select('sum('.$this->dx('interest_amount_payable').') as amount');
    	$this->db->where($this->dx('debtor_loan_id').'= "'.$debtor_loan_id.'"',NULL,FALSE);
    	$this->db->where($this->dx('active').'="1"',NULL,FALSE);
    	$interest_amount = $this->db->get('debtor_loan_invoices')->row()->amount;
    	$this->db->select('sum('.$this->dx('principle_amount_payable').') as amount');
    	$this->db->where($this->dx('debtor_loan_id').'= "'.$debtor_loan_id.'"',NULL,FALSE);
    	$this->db->where($this->dx('active').'="1"',NULL,FALSE);
    	$principle_amount = $this->db->get('debtor_loan_invoices')->row()->amount;
    	return $interest_amount + $principle_amount;
    }

    function get_loan_installments($id=0){
        $this->select_all_secure('debtor_loan_invoices');
        $this->db->where($this->dx('debtor_loan_invoices.debtor_loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx('debtor_loan_invoices.active').'="1"',NULL,FALSE);
        $this->db->order_by($this->dx('invoice_date').'+0','ASC',FALSE);
        $this->db->order_by($this->dx('due_date').'+0','ASC',FALSE);
        return $this->db->get('debtor_loan_invoices')->result();
    }

    function get_installment_principle_balance($debtor_loan_id=0){
        $this->db->select('sum('.$this->dx('principle_amount_payable').') - sum('.$this->dx('amount_paid').') as balance ');
        $this->db->where($this->dx('debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('type').'="1"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALsE);
        return $this->db->get("debtor_loan_invoices")->row()->balance;
    }

    function get_newest_invoice($id=0){
        $this->select_all_secure('debtor_loan_invoices');
        $this->db->where($this->dx('debtor_loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx("active").'="1"',NULL,FALSE);
        $this->db->where($this->dx("type").'="1"',NULL,FALSE);
        $this->db->where('('.$this->dx("status").'="1" OR '.$this->dx("status").' IS NULL OR '.$this->dx("status").'="0" OR '.$this->dx("status").'="")',NULL,FALSE);
        $this->db->order_by($this->dx('invoice_date'), 'ASC',FALSE);
        $this->db->limit(1);
        $query = $this->db->get('debtor_loan_invoices');
        $return = $query->row();
        $query->free_result();
        return $return;
    }


    function get_paid_existing_invoices($id=0,$date=0,$date_format_new=FALSE){
        $this->select_all_secure('debtor_loan_invoices');
        $this->db->select(array(
            "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('debtor_loan_invoices.due_date')." ),'%Y %D %M') as due_date2 ",
        ));
        $this->db->where($this->dx('debtor_loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx("active").'="1"',NULL,FALSE);
        $this->db->where($this->dx("type").'="1"',NULL,FALSE);
        $this->db->where('('.$this->dx("status").'="2"  OR '.$this->dx("amount_paid").'>="1" )',NULL,FALSE);
        if($date_format_new){
            $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('due_date')."),'%Y%m') > '" . date('Ym',$date) . "'", NULL, FALSE);
        }else{
            $this->db->where($this->dx('due_date').'>="'.$date.'"',NULL,FALSE);
        }
        $this->db->order_by($this->dx('invoice_date'), 'ASC',FALSE);
        return $query = $this->db->get('debtor_loan_invoices')->result();
    }

    function get_newest_invoices($id=0,$date=0,$date_format_new=FALSE){
        $this->select_all_secure('debtor_loan_invoices');
        $this->db->where($this->dx('debtor_loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx("active").'="1"',NULL,FALSE);
        $this->db->where($this->dx("type").'="1"',NULL,FALSE);
        $this->db->where('('.$this->dx("status").'="1" OR '.$this->dx("status").' IS NULL OR '.$this->dx("status").'="0" OR '.$this->dx("status").'="")',NULL,FALSE);
        if($date){
            if($date_format_new){
                $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('due_date')."),'%Y%m') > '" . date('Ym',$date) . "'", NULL, FALSE);
            }else{
                $this->db->where($this->dx('due_date').'>="'.$date.'"',NULL,FALSE);
            }
        }
        $this->db->order_by($this->dx('invoice_date'), 'ASC',FALSE);
        return $query = $this->db->get('debtor_loan_invoices')->result();
    }


    function void_children_invoices_fined_wrongly($parent_id=0){
        $this->select_all_secure('debtor_loan_invoices');
         $this->db->select(array(
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('debtor_loan_invoices.due_date')." ),'%Y %D %M') as deu_date2 ",
            ));
        $this->db->where($this->dx('fine_parent_loan_invoice_id').'="'.$parent_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $fines = $this->db->get('debtor_loan_invoices')->result();
        //print_r($fines);die;
        if($fines){
            foreach ($fines as $fine) {
                $this->update_loan_invoices($fine->id,array('status'=>'','active'=>''));
                if($fine->is_sent){
                    $this->update_statement_where($fine->id);
                }
            }
            return TRUE;

        }else{
            return FALSE;
        }
    }


    function void_future_outstanding_loan_invoices($debtor_loan_id=0,$due_date=0){
        $this->db->where($this->dx('debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
        $this->db->where('('.$this->dx('type').'="3")',NULL,FALSE);
        $this->db->where('('.$this->dx('active').'="1")',NULL,FALSE);
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('due_date')."),'%Y%m%d') >= '" . date('Ymd',$due_date) . "'", NULL, FALSE);
        $return =  $this->db->delete('debtor_loan_invoices');
    }

    function count_all_loan_invoices($debtor_loan_id=0){
        $this->select_all_secure('debtor_loan_invoices');
        $this->db->where($this->dx('debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
        $this->db->where('('.$this->dx('type').'="1")',NULL,FALSE);
        $this->db->where('('.$this->dx('active').'="1")',NULL,FALSE);
        $this->db->where('('.$this->dx('status').'="1" OR '.$this->dx('status').'="" OR '.$this->dx('status').'="0" OR '.$this->dx('status').'=" " OR '.$this->dx('status').'is NULL )',NULL,FALSE);
        return $this->db->count_all_results('debtor_loan_invoices')?:0;
    }

    function get_unpaid_loan_balance($id){
        $this->db->select('sum('.$this->dx('amount_payable').') - sum('.$this->dx('amount_paid').') as balance');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('debtor_loan_id').'="'.$id.'"',NULL,FALSE);
        $balance = $this->db->get('debtor_loan_invoices')->row()->balance;
        if($balance){
            return $balance;
        }else{
            return 0;
        }
    }

    function get_loan_balance($id=0){
        $this->db->select(array($this->dx('debtor_loan_statements.balance').' as balance'));
        $this->select_all_secure('debtor_loan_statements');
        $this->db->where($this->dx('debtor_loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->order_by($this->dx('id'),'DESC',FALSE);
        $this->db->order_by($this->dx('transaction_date').'+0','DESC',FALSE);
        $this->db->order_by($this->dx('created_on').'+0','DESC',FALSE);
        $this->db->order_by($this->dx('debtor_loan_invoice_id').'+0','DESC',FALSE);
        $this->db->limit(1);
        $query = $this->db->get('debtor_loan_statements');
        $result = $query->row();
        $query->free_result();
        if($result){
            if($result->balance<=0){
                $this->db->select('sum('.$this->dx('amount_payable').') - sum('.$this->dx('amount_paid').') as balance');
                $this->db->where($this->dx('active').'="1"',NULL,FALSE);
                $this->db->where($this->dx('debtor_loan_id').'="'.$id.'"',NULL,FALSE);
                $balance = $this->db->get('debtor_loan_invoices')->row()->balance;
                if($balance){
                    return $balance;
                }else{
                    return 0;
                }
            }else{
                return $result->balance;
            }
        }else{
            return 0;
        }
    }

    function get_total_loan_fines_payable($debtor_loan_id = 0){
        $this->db->select('sum('.$this->dx('amount_payable').') as amount_payable');
        $this->db->where($this->dx('debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('type').'!="1"',NULL,FALSE);
        $this->db->where($this->dx('type').'!="5"',NULL,FALSE);
        return $this->db->get('debtor_loan_invoices')->row()->amount_payable?:0;
    }

    function get_total_installment_loan_payable($debtor_loan_id=0){
        $this->db->select('sum('.$this->dx('amount_payable').') as amount_payable');
        $this->db->where($this->dx('debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('type').'="1"',NULL,FALSE);
        return $this->db->get('debtor_loan_invoices')->row()->amount_payable?:0;
    }

    function get_loan_lump_sum_as_date($debtor_loan_id=0,$date=''){
        if($date){
        }else{
            $date = time();
        }

        $this->select_all_secure('debtor_loans');
        $this->db->where('id',$debtor_loan_id);
        $loan = $this->db->get('debtor_loans')->row();
        if($loan->interest_type==2){
            $this->db->select('sum('.$this->dx('amount_payable').') - sum('.$this->dx('amount_paid').') as balance ');
            $this->db->where($this->dx('debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
            $this->db->where($this->dx('active').'="1"',NULL,FALsE);
            $this->db->where($this->dx('due_date').'<"'.$date.'"',NULL,FALSE);
            $balance = $this->db->get("debtor_loan_invoices")->row()->balance;

            $this->db->select('sum('.$this->dx('principle_amount_payable').') - sum('.$this->dx('amount_paid').') as balance ');
            $this->db->where($this->dx('debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
            $this->db->where($this->dx('active').'="1"',NULL,FALsE);
            $this->db->where($this->dx('due_date').'>="'.$date.'"',NULL,FALSE);
            $principle = $this->db->get("debtor_loan_invoices")->row()->balance;

            return $balance+$principle;
        }else{
            $total_installment_payable = $this->get_total_installment_loan_payable($debtor_loan_id);
            $total_fines = $this->get_total_loan_fines_payable($debtor_loan_id);
            $total_paid = $this->get_loan_total_payments($debtor_loan_id);
            return ($total_installment_payable+$total_fines-$total_paid);
        }
    }

    function get_unpaid_loan_installments($id=0){
        $this->select_all_secure('debtor_loan_invoices');
        $this->db->where($this->dx('debtor_loan_invoices.debtor_loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx('debtor_loan_invoices.active').'="1"',NULL,FALSE);
        $this->db->where('('.$this->dx('status').'="1" OR '.$this->dx('status').'="" OR '.$this->dx('status').'="0" OR '.$this->dx('status').'=" " OR '.$this->dx('status').'is NULL )',NULL,FALSE);
        $this->db->order_by($this->dx('due_date').'+0','ASC',FALSE);
        return $this->db->get('debtor_loan_invoices')->result();
    }

    function void_all_invoices($debtor_loan_id=0){
    	return $this ->db->query("update debtor_loan_invoices set 
                active=".$this->exa('0').",
                status=".$this->exa('0').",
                modified_on=".$this->exa(time())."
                where ".$this->dx("debtor_loan_id")." ='".$debtor_loan_id."'"); 
    }

    function get_paid_invoices($debtor_loan_id=0){
        $this->select_all_secure('debtor_loan_invoices');
        $this->db->select(array(
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('debtor_loan_invoices.due_date')." ),'%Y %D %M') as deu_date2 ",
            ));
        $this->db->where($this->dx('debtor_loan_id').'="'.$debtor_loan_id.'"');
        $this->db->where($this->dx('amount_paid').'> "0"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"');
        $this->db->order_by($this->dx('due_date'),'DESC',FALSE);
        return $this->db->get('debtor_loan_invoices')->result();
    }

    function get_summation_for_invoice($debtor_loan_id=0){
        $this->db->select(array(
                'sum('.$this->dx('interest_amount_payable').') as total_interest_payable',
                'sum('.$this->dx('principle_amount_payable').') as total_principle_payable',
                'sum('.$this->dx('amount_payable').') as total_amount_payable',
                'sum('.$this->dx('amount_paid').') as total_amount_paid',
            ));
        $this->select_all_secure('debtor_loans');
        $this->db->where($this->dx('debtor_loan_invoices.debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('debtor_loan_invoices.active').'="1"',NULL,FALSE);
        $this->db->join('debtor_loans',$this->dx('debtor_loan_invoices.debtor_loan_id').'= debtor_loans.id');
        return $this->db->get('debtor_loan_invoices')->row();
    }

    function loan_payable_and_principle_todate($debtor_loan_id=0){
        $this->db->select(array(
                'sum('.$this->dx('debtor_loan_invoices.amount_payable').') as todate_amount_payable',
                'sum('.$this->dx('debtor_loan_invoices.principle_amount_payable').') as todate_principle_payable'
            ));
        $this->db->where($this->dx('debtor_loan_invoices.debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('debtor_loan_invoices.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('due_date').' <"'.time().'"',NULL,FALSE);
        $result =  $this->db->get('debtor_loan_invoices')->row();
        return $result;
    }

    function get_projected_interest($debtor_loan_id = 0,$amount_paid = 0){
        if($amount_paid){
            $this->select_all_secure('debtor_loan_invoices');
            $this->db->where($this->dx("debtor_loans.active").'="1"',NULL,FALSE);
            $this->db->where($this->dx('debtor_loan_invoices.active').'="1"',NULL,FALSE);
            $this->db->where($this->dx('debtor_loan_invoices.debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
            $this->db->where($this->dx("debtor_loans.group_id").'="'.$this->group->id.'"',NULL,FALSE);
            $this->db->join('debtor_loans',$this->dx('debtor_loan_invoices.debtor_loan_id').'= debtor_loans.id');
            $this->db->order_by($this->dx('debtor_loan_invoices.invoice_date'),'ASC',FALSE);
            $debtor_loan_invoices = $this->db->get('debtor_loan_invoices')->result();

            if(!empty($debtor_loan_invoices)){
                $projected_interest = 0;
                foreach($debtor_loan_invoices as $debtor_loan_invoice){
                    if($amount_paid>0){
                        if($debtor_loan_invoice->type==1){
                            if($debtor_loan_invoice->amount_payable<=$amount_paid){
                                $projected_interest+=$debtor_loan_invoice->interest_amount_payable;
                            }else{
                                if($debtor_loan_invoice->principle_amount_payable<=$amount_paid){
                                    $projected_interest+=$amount_paid - $debtor_loan_invoice->principle_amount_payable;
                                }else{
                                    //do nothing
                                }
                            }
                        }else if($debtor_loan_invoice->type==2||$debtor_loan_invoice->type==3){
                            if($debtor_loan_invoice->amount_payable<=$amount_paid){
                                $projected_interest+=$debtor_loan_invoice->amount_payable;
                            }else{
                                $projected_interest+=$amount_paid;
                            }
                        }
                        $amount_paid-=$debtor_loan_invoice->amount_payable;
                    }
                }
                return $projected_interest;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
        
    }

    function get_today_loan_invoices_to_queue($date=0){
        if($date){
        }else{
            $date = time();
        }
        $this->select_all_secure('debtor_loan_invoices');
        $this->select_all_secure('debtor_loans');
        $this->db->select(array(
                'debtor_loan_invoices.id as id',
            ));
        $this->db->where($this->dx('debtor_loan_invoices.active').'="1"',NULL,FALSE);
        $this->db->where('('.$this->dx('debtor_loan_invoices.is_sent').'="" OR '.$this->dx('is_sent').'is NULL OR '.$this->dx('is_sent').' ="0")',NULL,FALSE);
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('debtor_loan_invoices.invoice_date')."),'%Y %D %M') = '" . date('Y jS F',$date) . "'", NULL, FALSE);
        $this->db->where($this->dx('debtor_loans.active').'="1"',NULL,FALSE);
        $this->db->join('debtor_loans','debtor_loans.id ='.$this->dx('debtor_loan_invoices.debtor_loan_id'));
        return $this->db->get('debtor_loan_invoices')->result();
    }

    function get_late_loan_payment_fine_list($date=0){
        if($date){
        }else{
            $date=time();
        }
        //$this->select_all_secure('debtor_loans');
        $this->select_all_secure('debtor_loan_invoices');
        $this->db->select(array(
                "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('debtor_loan_invoices.fine_date')." ),'%Y %D %M') as fine_date2 ",
                $this->dx('debtor_loan_invoices.status').' as invoice_status',
            ));
        $this->db->where('('.$this->dx('debtor_loan_invoices.status').'is NULL OR '.$this->dx('debtor_loan_invoices.status').'="1")',NULL,FALSE);
        $this->db->where($this->dx('debtor_loan_invoices.active').'="1"',NULL,FALSE);
        $this->db->where('('.$this->dx('debtor_loan_invoices.disable_fines').'=" " OR '.$this->dx('debtor_loan_invoices.disable_fines').'is NULL OR '.$this->dx('debtor_loan_invoices.disable_fines').' = "")');
        //$this->db->where($this->dx('debtor_loans.active').'="1"',NULL,FALSE);
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('debtor_loan_invoices.fine_date')."),'%Y %D %M') = '". date('Y jS F',$date) . "'", NULL, FALSE);
        //$this->db->join('debtor_loans', 'debtor_loans.id = '.$this->dx('debtor_loan_invoices.debtor_loan_id'));
        return $this->db->get('debtor_loan_invoices')->result();
    }


    function void_children_invoices_fined_wrongly_after_date($parent_id=0,$due_date=0){
        $this->select_all_secure('debtor_loan_invoices');
        $this->db->where($this->dx('fine_parent_loan_invoice_id').'="'.$parent_id.'"',NULL,FALSE);
        $this->db->where($this->dx('due_date').'>"'.$due_date.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $fines = $this->db->get('debtor_loan_invoices')->result();
        if($fines){
            foreach ($fines as $fine) {
                $this->update_loan_invoices($fine->id,array('status'=>'','active'=>''));
                if($fine->is_sent){
                    $this->update_statement_where($fine->id);
                }
            }
            return TRUE;

        }else{
            return FALSE;
        }
    }

    function get_loan_principal_installments($debtor_loan_id = 0,$group_id=0){
        $this->select_all_secure('debtor_loan_invoices');
        $this->db->where($this->dx('debtor_loan_invoices.active').' = "1" ',NULL,FALSE);
        $this->db->where($this->dx('debtor_loan_invoices.debtor_loan_id').' = "'.$debtor_loan_id.'" ',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('debtor_loan_invoices.group_id').' = "'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('debtor_loan_invoices.group_id').' = "'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('debtor_loan_invoices.type').' = "1" ',NULL,FALSE);
        return $this->db->get('debtor_loan_invoices')->result();
    }

    /*****************Debtor Loan Statements*******************************/

    function update_statement_where($fine_id=0){
       return $this -> db -> query("update debtor_loan_statements set 
                active=".$this->exa('0').",
                status = ".$this->exa('0')." 
                where ".$this->dx("debtor_loan_invoice_id")." ='".$fine_id."'");  
    }

    function insert_loan_statement($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('debtor_loan_statements',$input);
	}

	function update_statement($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'debtor_loan_statements',$input);
	}

	function delete_all_statement_entries($debtor_loan_id=0){
    	return $this ->db->query("update debtor_loan_statements set 
                active=".$this->exa('0').",
                modified_on=".$this->exa(time())."
                where ".$this->dx("debtor_loan_id")." ='".$debtor_loan_id."'"); 
    }

	function get_loan_statement_for_library($id=0){
    	$this->select_all_secure('debtor_loan_statements');
        $this->db->where($this->dx('debtor_loan_statements.debtor_loan_id').'="'.$id.'"',NULL,FALSE);
    	$this->db->where($this->dx('debtor_loan_statements.active').'="1"',NULL,FALSE);
        $this->db->order_by($this->dx('transaction_date'),'ASC',FALSE);
    	return $this->db->get('debtor_loan_statements')->result();
    }

	function get_current_amount_paid($debtor_loan_id=0,$date=0){
        $this->db->select('sum('.$this->dx('amount').') as amount');
        $this->db->where("debtor_loan_id",$debtor_loan_id);
        $this->db->where($this->dx('active').'= "1"',NULL,FALSE); 
        $this->db->where($this->dx('transaction_type').'= "4"',NULL,FALSE); 
        $this->db->where($this->dx('transaction_date').'<="'.$date.'"',NULL,FALSE); 
       	$amount = $this->db->get('debtor_loan_statements')->row();
       	if($amount){
       		return $amount->amount;
       	}else{
       		return 0;
       	}
    }


    function get_loan_statement_for_library_for_payments($debtor_loan_id=0){
        $this->select_all_secure('debtor_loan_statements');
        $this->db->where($this->dx('debtor_loan_statements.debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('debtor_loan_statements.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('debtor_loan_statements.transaction_type').'="4"',NULL,FALSE);
        $this->db->order_by($this->dx('transaction_date'),'ASC',FALSE);
        $this->db->order_by($this->dx('created_on'),'ASC',FALSE);
        return $this->db->get('debtor_loan_statements')->result();
    }

    function get_loan_statement($id=0){
    	$this->select_all_secure('debtor_loan_statements');
    	$this->db->where($this->dx('debtor_loan_statements.debtor_loan_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where('('.$this->dx('transaction_type').'="4" OR '.$this->dx('transaction_type').'="5")',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
    	$this->db->order_by($this->dx('transaction_date'),'ASC',FALSE);
    	return $this->db->get('debtor_loan_statements')->result();
    }

    function update_statement_payment($debtor_loan_payment_id=0){
       return $this -> db -> query("update debtor_loan_statements set 
                active=".$this->exa('0').",
                status = ".$this->exa('0')." ,
                modified_on = ".$this->exa(time())."
                where ".$this->dx("debtor_loan_payment_id")." ='".$debtor_loan_payment_id."'");  
    }

    function void_statement_entries($debtor_loan_id=0){
    	return $this ->db->query("update debtor_loan_statements set 
                active=".$this->exa('0').",
                modified_on=".$this->exa(time())."
                where ".$this->dx("debtor_loan_id")." ='".$debtor_loan_id."'"); 
    }


    function void_loan_incoices_and_fines_statement_entries($debtor_loan_id=0){
    	return $this ->db->query("update debtor_loan_statements set 
                active=".$this->exa('0').",
                modified_on=".$this->exa(time())."
                where ".$this->dx("debtor_loan_id")." ='".$debtor_loan_id."' AND (".$this->dx('transaction_type')." = '1' OR ".$this->dx('transaction_type')." ='2' OR ".$this->dx('transaction_type')." = '3' )"); 
    }

    function void_loan_payment_statement($debtor_loan_id = 0){
        return $this ->db->query("update debtor_loan_statements set 
                active=".$this->exa('0').",
                status = ".$this->exa('0')." 
                where ".$this->dx("debtor_loan_id")." ='".$debtor_loan_id."' AND ".$this->dx('transaction_type')." = '4'");  
    }

    function insert_batch_statements($input,$skip_validation=FALSE){
        return $this->insert_batch_secure_data('debtor_loan_statements',$input);
    }


    function delete_all_loan_invoices_and_fines_entries($debtor_loan_id=0){
        $this->select_all_secure('debtor_loan_statements');
        $this->db->where($this->dx('debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
        $this->db->where('('.$this->dx('transaction_type').'="1" OR '.$this->dx('transaction_type').' ="2" OR '.$this->dx('transaction_type').' ="3")',NULL,FALSE);
        $result = $this->db->get('debtor_loan_statements')->result();
        if($result)
        {
            foreach ($result as $res) 
            {
                $this->db->where('id',$res->id);
                $this->db->delete('debtor_loan_statements');
            }
            return TRUE;
        }
        else
        {
            return TRUE;
        }
    }

    function get_transfer_out_invoice($debtor_loan_id=0){
        $this->select_all_secure('debtor_loan_statements');
        $this->db->where($this->dx('debtor_loan_statements.debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('debtor_loan_statements.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('debtor_loan_statements.transaction_type').'="5"',NULL,FALSE);
        return $this->db->get('debtor_loan_statements')->result();
    }



	/************Debtor Loan Repayments********************/

	function insert_loan_repayments($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('debtor_loan_repayments',$input);
	}

	function get_payment($id=0){
		$this->select_all_secure('debtor_loan_repayments');
		$this->db->where('id',$id);
		return $this->db->get('debtor_loan_repayments')->row();
	}

	function update_loan_repayment($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'debtor_loan_repayments',$input);
	}


	function get_loan_repayments($debtor_loan_id=0,$incoming_loan_transfer_invoice_id=0,$incoming_contribution_transfer_id=0){
        $this->select_all_secure('debtor_loan_repayments');
        $this->db->where($this->dx('debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
        if($incoming_loan_transfer_invoice_id){
            $this->db->where($this->dx('incoming_loan_transfer_invoice_id').' IN ('.$incoming_loan_transfer_invoice_id.')',NULL,FALSE);
        }
        if($incoming_contribution_transfer_id){
            $this->db->where($this->dx('incoming_contribution_transfer_id').'="'.$incoming_contribution_transfer_id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('debtor_loan_repayments')->result();
    } 

    function get_total_payment($debtor_loan_id=0){
        $this->db->select($this->dx('amount').'as amount');
        $this->db->where($this->dx("debtor_loan_id").'="'.$debtor_loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx("active").'="1"',NULL,FALSE);
        $query = $this->db->get('debtor_loan_repayments');
        $query2 = $query->row();
        $query->free_result();
        return $query2;
    }

    function get_payment_by_date($debtor_loan_id=0,$receipt_date=0){
        if($receipt_date){
            $this->db->select('sum('.$this->dx('amount').') as amount');
            $this->db->where($this->dx("debtor_loan_id").'="'.$debtor_loan_id.'"',NULL,FALSE);
            $this->db->where($this->dx("active").'="1"',NULL,FALSE);
            $this->db->where($this->dx("receipt_date").'<="'.$receipt_date.'"',NULL,FALSE);
            $amount = $this->db->get('debtor_loan_repayments')->row();

            if($amount) {
                return $amount->amount;
            }else{
                return 0; 
            }
        }else{
            return 0; 
        }
    }

    function get_loan_total_payments($debtor_loan_id=0){
        $this->db->select('sum('.$this->dx('amount').') as amount_paid');
        $this->db->where($this->dx('debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('debtor_loan_repayments')->row()->amount_paid?:0;
    }

    function get_total_loan_paid($group_id=0){
        $this->db->select('sum('.$this->dx('debtor_loan_repayments.amount').') as amount');
        $this->db->where($this->dx("debtor_loan_repayments.active").'="1"',NULL,FALSE);
      
        $amount = $this->db->get('debtor_loan_repayments')->row();
        if($amount){
            return $amount->amount?:0;
        }else{
            return 0;
        }
    }

    /************************Debtor Loan Guarantor*********************/

    function insert_loan_guarantor($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('debtor_loan_guarantors',$input);
	}


	function update_loan_guarantor($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'debtor_loan_guarantors',$input);
	}

	function delete_guarantors($debtor_loan_id=0){
		return $this ->db->query("update debtor_loan_guarantors set 
                active=".$this->exa('0').",
                modified_on=".$this->exa(time())."
                where ".$this->dx("debtor_loan_id")." ='".$debtor_loan_id."'"); 
	}

	function get_loan_guarantors_array(){
		$this->select_all_secure('debtor_loan_guarantors');
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		//$this->db->group_by(array($this->dx('debtor_loan_id')));
		$guarantors = $this->db->get('debtor_loan_guarantors')->result();
		$arr = array();
		foreach ($guarantors as $guarantor) {
			$arr[$guarantor->debtor_loan_id][] = array(
					'member_id' => $guarantor->member_id,
					'amount' => $guarantor->guaranteed_amount,
					'comment' => $guarantor->comment,
				);
		}
		return $arr;
	}

	function get_loan_guarantors($debtor_loan_id=0){
        $this->db->select(array(
                $this->dx('member_id').' as guarantor_id',
                $this->dx('guaranteed_amount').' as guaranteed_amount',
                $this->dx('comment').' as guarantor_comment',
                $this->dx('users.first_name').' as guarantor_first_name',
                $this->dx('users.last_name').' as guarantor_last_name',
                $this->dx('users.email').' as guarantor_email',
                $this->dx('users.phone').' as guarantor_phone',

            ));
        $this->db->where($this->dx('debtor_loan_id').'="'.$debtor_loan_id.'"',NULL,FALSE);
        $this->db->where($this->dx('debtor_loan_guarantors.active').'="1"',NULL,FALSE);
        $this->db->join('members',$this->dx('debtor_loan_guarantors.member_id').' = members.id');
        $this->db->join('users',$this->dx('members.user_id').' = users.id');
        return $this->db->get('debtor_loan_guarantors')->result();
    }

    function void_loan_guarantors($debtor_loan_id=0){
    	return $this ->db->query("update debtor_loan_guarantors set 
                active=".$this->exa('0').",
                modified_on=".$this->exa(time())."
                where ".$this->dx("debtor_loan_id")." ='".$debtor_loan_id."'"); 
    }



}