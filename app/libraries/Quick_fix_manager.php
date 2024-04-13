<?php 
	if (!defined('BASEPATH')) exit('No direct script access allowed');
	class Quick_fix_manager{

		public function __construct(){
			$this->ci= & get_instance();
			$this->ci->load->library('transactions');
	        $this->ci->load->model('invoices/invoices_m');
	        $this->ci->load->model('statements/statements_m');
	        $this->ci->load->model('deposits/deposits_m');
	        $this->ci->load->model('statements/statements_m');

		}

		public function restore_group_deposit_statements($group_id = 0,$limit = 0){
			if ($group_id) {
				//$this->fix_group_duplicate_statement_entries($group_id);
				$deposit_statements = $this->restore_deposit_statements($group_id);
				$invoice_statements = $this->get_group_active_invoices($group_id);
				$duplicate_results = $this->fix_group_duplicate_statement_entries($group_id);
				$results = (object)array(
					'deposit_statements'=> $deposit_statements?$deposit_statements:0,
					'invoice_statements'=>$invoice_statements?$invoice_statements:0,
					'duplicate_results'=>$duplicate_results,
				);
				return $results;
			}else{
				echo "Group id varibale is required";
			}
		}

		public function fix_group_duplicate_statement_entries($group_id = 0){
	        $contribution_statement_entries = $this->find_duplicate_contribution_statement_entries($group_id);
	        $fine_statement_entries = $this->find_duplicate_fine_statement_entries($group_id);
	        $deposit_statement_entries = $this->find_duplicate_deposit_statement_entries($group_id);
	        $share_transfer_entries = $this->find_duplicate_share_transfer_entries($group_id);
	        $contribution_refund_entries = $this->find_duplicate_contribution_refund_entries($group_id);
	        $share_to_fine_entries = $this->find_duplicate_share_to_fine_transfer_entries($group_id);
	        $reconcile_statements = $this->reconcile_group_member_contribution_statements($group_id);
	        $reconcile_fine_statements = $this->reconcile_group_member_fine_statements($group_id);
	        $array =  (object)array(
	        	'contribution_statement_entries'=>$contribution_statement_entries?$contribution_statement_entries:0,
	        	'fine_statement_entries'=>$fine_statement_entries?$fine_statement_entries:0,
	        	'deposit_statement_entries'=>$deposit_statement_entries?$deposit_statement_entries:0,
	        	'share_transfer_entries'=>$share_transfer_entries?$share_transfer_entries:0,
	        	'contribution_refund_entries'=>$contribution_refund_entries?$contribution_refund_entries:0,
	        	'share_to_fine_entries'=>$share_to_fine_entries?$share_to_fine_entries:0,
	        	'reconcile_statements'=>$reconcile_statements?$reconcile_statements:0,
	        	'reconcile_fine_statements'=>$reconcile_fine_statements?$reconcile_fine_statements:0,

	        );
	        return $array;
	    }


		public function find_duplicate_contribution_statement_entries($group_id = 0){

	        $statements = $this->ci->statements_m->find_duplicate_contribution_fine_statement_entries($group_id);

	        $group_ids = array();
	        $member_ids = array();
	        $transaction_types = array();
	        $contribution_ids = array();
	        $transaction_dates = array();
	        $created_ons = array();

	        foreach ($statements as $statement) {
	            $group_ids[] = $statement->group_id;
	            $member_ids[] = $statement->member_id;
	            $transaction_types[] = $statement->transaction_type;
	            $contribution_ids[] = $statement->contribution_id;
	            $transaction_dates[] = $statement->transaction_date;
	            $created_ons[] = $statement->created_on;
	        }

	        $statements = $this->ci->statements_m->get_duplicate_contribution_fine_statements($group_ids,$member_ids,$transaction_types,$contribution_ids,$transaction_dates,$created_ons);

	        $statement_ids = array();

	        $statments_array = array();

	        foreach($statements as $statement):
	            if(isset($statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->contribution_id][$statement->transaction_date])){
	                $statement_ids[] = $statement->id;
	            }else{
	                $statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->contribution_id][$statement->transaction_date] = 1;
	            }
	        endforeach;

	        if($this->ci->statements_m->delete_where_in($statement_ids,$group_id)){
	            return count($statement_ids);
	        }

	    }

	    public function find_duplicate_fine_statement_entries($group_id = 0){

	        $statements = $this->ci->statements_m->find_duplicate_fine_statement_entries($group_id);
	        $group_ids = array();
	        $member_ids = array();
	        $transaction_types = array();
	        $fine_ids = array();
	        $transaction_dates = array();
	        $created_ons = array();

	        foreach ($statements as $statement) {
	            $group_ids[] = $statement->group_id;
	            $member_ids[] = $statement->member_id;
	            $transaction_types[] = $statement->transaction_type;
	            $fine_ids[] = $statement->fine_id;
	            $transaction_dates[] = $statement->transaction_date;
	            $created_ons[] = $statement->created_on;
	        }

	        $statements = $this->ci->statements_m->get_duplicate_fine_statements($group_ids,$member_ids,$transaction_types,$fine_ids,$transaction_dates,$created_ons);

	        $statement_ids = array();

	        $statments_array = array();

	        foreach($statements as $statement):
	            if(isset($statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->fine_id][$statement->transaction_date])){
	                $statement_ids[] = $statement->id;
	            }else{
	                $statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->fine_id][$statement->transaction_date] = 1;
	            }
	        endforeach;

	        if($this->ci->statements_m->delete_where_in($statement_ids)){
	            return count($statement_ids);
	        }

	    }

	    public function find_duplicate_deposit_statement_entries($group_id = 0){

	        $statements = $this->ci->statements_m->find_duplicate_deposit_statement_entries($group_id);

	        $group_ids = array();
	        $member_ids = array();
	        $transaction_types = array();
	        $deposit_ids = array();
	        $transaction_dates = array();
	        $created_ons = array();

	        foreach ($statements as $statement) {
	            $group_ids[] = $statement->group_id;
	            $member_ids[] = $statement->member_id;
	            $transaction_types[] = $statement->transaction_type;
	            $deposit_ids[] = $statement->deposit_id;
	            $transaction_dates[] = $statement->transaction_date;
	            $created_ons[] = $statement->created_on;
	        }

	        $statements = $this->ci->statements_m->get_duplicate_deposit_statements($group_ids,$member_ids,$transaction_types,$deposit_ids,$transaction_dates,$created_ons);

	        $statement_ids = array();

	        $statments_array = array();

	        foreach($statements as $statement):
	            if(isset($statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->deposit_id][$statement->transaction_date])){
	                $statement_ids[] = $statement->id;
	            }else{
	                $statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->deposit_id][$statement->transaction_date] = 1;
	            }
	        endforeach;

	        $chunked_statement_ids = array_chunk($statement_ids,2000);

	        foreach($chunked_statement_ids  as $statement_ids):
	            if($rows = $this->ci->statements_m->delete_where_in($statement_ids,$group_id)){
	                echo $rows." rows deleted. <br/>";
	            }
	        endforeach;
	        return count($statement_ids);

	    }

	    public function find_duplicate_share_transfer_entries($group_id = 0){

	        $statements = $this->ci->statements_m->find_duplicate_share_transfer_statement_entries($group_id);

	        $group_ids = array();
	        $member_ids = array();
	        $transaction_types = array();
	        $deposit_ids = array();
	        $transaction_dates = array();
	        $created_ons = array();
	        $contribution_from_ids = array();
	        $contribution_to_ids = array();

	        foreach ($statements as $statement) {
	            $group_ids[] = $statement->group_id;
	            $member_ids[] = $statement->member_id;
	            $transaction_types[] = $statement->transaction_type;
	            $deposit_ids[] = $statement->deposit_id;
	            $transaction_dates[] = $statement->transaction_date;
	            $created_ons[] = $statement->created_on;
	            $contribution_from_ids[] = $statement->contribution_from_id;
	            $contribution_to_ids[] = $statement->contribution_to_id;
	        }

	        $statements = $this->ci->statements_m->get_duplicate_contribution_transfer_statements($group_ids,$member_ids,$transaction_types,$deposit_ids,$transaction_dates,$contribution_from_ids,$contribution_to_ids);

	        // echo count($statements);
	        // die;

	        $statement_ids = array();

	        $statments_array = array();

	        foreach($statements as $statement):
	            if(isset($statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->transaction_date][$statement->contribution_from_id][$statement->contribution_to_id])){
	                $statement_ids[] = $statement->id;
	            }else{
	                $statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->transaction_date][$statement->contribution_from_id][$statement->contribution_to_id] = 1;
	            }
	        endforeach;

	        $chunked_statement_ids = array_chunk($statement_ids,2000);

	        foreach($chunked_statement_ids  as $statement_ids):
	            if($rows = $this->ci->statements_m->delete_where_in($statement_ids,$group_id)){
	                echo $rows." rows deleted. <br/>";
	            }
	        endforeach;

	        return count($statement_ids);

	    }

	    public function find_duplicate_contribution_refund_entries($group_id = 0){
	        $statements = $this->ci->statements_m->find_duplicate_contribution_refund_statement_entries($group_id);

	        $group_ids = array();
	        $member_ids = array();
	        $transaction_types = array();
	        $deposit_ids = array();
	        $transaction_dates = array();
	        $created_ons = array();
	        $refund_ids = array();

	        foreach ($statements as $statement) {
	            $group_ids[] = $statement->group_id;
	            $member_ids[] = $statement->member_id;
	            $transaction_types[] = $statement->transaction_type;
	            $deposit_ids[] = $statement->deposit_id;
	            $transaction_dates[] = $statement->transaction_date;
	            $created_ons[] = $statement->created_on;
	            $contribution_from_ids[] = $statement->contribution_from_id;
	            $contribution_to_ids[] = $statement->contribution_to_id;
	            $refund_ids[] = $statement->refund_id;
	        }

	        $statements = $this->ci->statements_m->get_duplicate_contribution_refund_statements($group_ids,$member_ids,$transaction_types,$transaction_dates,$refund_ids);
	        
	        $statement_ids = array();

	        $statments_array = array();

	        foreach($statements as $statement):
	            if(isset($statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->transaction_date][$statement->refund_id])){
	                $statement_ids[] = $statement->id;
	            }else{
	                $statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->transaction_date][$statement->refund_id] = 1;
	            }
	        endforeach;

	        $chunked_statement_ids = array_chunk($statement_ids,2000);

	        foreach($chunked_statement_ids  as $statement_ids):
	            if($rows = $this->ci->statements_m->delete_where_in($statement_ids,$group_id)){
	                echo $rows." rows deleted. <br/>";
	            }
	        endforeach;
	        return count($statement_ids);

	    }

	    public function find_duplicate_share_to_fine_transfer_entries($group_id = 0){

	        $statements = $this->ci->statements_m->find_duplicate_share_to_fine_transfer_statement_entries($group_id);

	        $group_ids = array();
	        $member_ids = array();
	        $transaction_types = array();
	        $deposit_ids = array();
	        $transaction_dates = array();
	        $created_ons = array();
	        $contribution_from_ids = array();
	        $fine_category_to_ids = array();

	        foreach ($statements as $statement) {
	            $group_ids[] = $statement->group_id;
	            $member_ids[] = $statement->member_id;
	            $transaction_types[] = $statement->transaction_type;
	            $deposit_ids[] = $statement->deposit_id;
	            $transaction_dates[] = $statement->transaction_date;
	            $created_ons[] = $statement->created_on;
	            $contribution_from_ids[] = $statement->contribution_from_id;
	            $fine_category_to_ids[] = $statement->fine_category_to_id;
	        }

	        $statements = $this->ci->statements_m->get_duplicate_contribution_transfer_to_fine_statements($group_ids,$member_ids,$transaction_types,$deposit_ids,$transaction_dates,$contribution_from_ids,$fine_category_to_ids);

	        // echo count($statements);
	        // die;

	        $statement_ids = array();

	        $statments_array = array();

	        foreach($statements as $statement):
	            if(isset($statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->transaction_date][$statement->contribution_from_id][$statement->fine_category_to_id])){
	                $statement_ids[] = $statement->id;
	            }else{
	                $statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->transaction_date][$statement->contribution_from_id][$statement->fine_category_to_id] = 1;
	            }
	        endforeach;

	         $chunked_statement_ids = array_chunk($statement_ids,2000);

	        foreach($chunked_statement_ids  as $statement_ids):
	            if($rows = $this->ci->statements_m->delete_where_in($statement_ids,$group_id)){
	                echo $rows." rows deleted. <br/>";
	            }
	        endforeach;
	        return count($statement_ids);
	    }

	    public function reconcile_group_member_contribution_statements($group_id = 0){
	        $group_ids = array($group_id);
	        $member_options = $this->ci->members_m->get_group_member_options($group_id);
	        $member_ids = array_flip($member_options);
	        $member_contribution_balances_array = array();
	        $member_cumulative_balances_array = array();
	        //print_r($member_ids);
	        $date = strtotime('01-12-2000');
	        if($this->ci->transactions->update_group_member_contribution_statement_balances($group_ids,$member_ids,$date)){
	            return TRUE;
	        }
	    }

	    public function reconcile_group_member_fine_statements($group_id = 0){
	        $group_ids = array($group_id);
	        $member_options = $this->ci->members_m->get_group_member_options($group_id);
	        $member_ids = array_flip($member_options);
	        //$member_ids = array(23);
	        //$statement_entries = $this->statements_m->get_group_member_contribution_statements($group_ids,$member_ids);
	        $member_contribution_balances_array = array();
	        $member_cumulative_balances_array = array();
	        if($this->ci->transactions->update_group_member_fine_statement_balances($group_ids,$member_ids)){
	            return TRUE;
	        }
	    }

	    public function restore_deposit_statements($group_id = 0 ){
	    	$deposits = $this->ci->deposits_m->get_group_statement_deposits($group_id);
	        $statements = $this->ci->statements_m->get_group_deposit_statement_entries($group_id);
	        $arr = array();
	        $input = array();
	        $deposit_ids_array = array();
	        foreach($statements as $statement):
	            $arr[] = $statement->deposit_id;
	        endforeach;
	        foreach ($deposits as $key => $deposit) {
	            # code...
	            if(in_array($deposit->id,$arr)){

	            }else{
	                if(in_array($deposit->type,array(1,2,3,7))){
	                    if(preg_match('/bank-/', $deposit->account_id)){
	                        $transaction_type = 9;
	                    }else if(preg_match('/sacco-/', $deposit->account_id)){
	                        $transaction_type = 10;
	                    }else if(preg_match('/mobile-/', $deposit->account_id)){
	                        $transaction_type = 11;
	                    }else if(preg_match('/petty-/', $deposit->account_id)){
	                        $transaction_type = 15;
	                    }else{
	                        $transaction_type = 0;
	                    }
	                }else if(in_array($deposit->type,array(4,5,6,8))){
	                    if(preg_match('/bank-/', $deposit->account_id)){
	                        $transaction_type = 12;
	                    }else if(preg_match('/sacco-/', $deposit->account_id)){
	                        $transaction_type = 13;
	                    }else if(preg_match('/mobile-/', $deposit->account_id)){
	                        $transaction_type = 14;
	                    }else if(preg_match('/petty-/', $deposit->account_id)){
	                        $transaction_type = 16;
	                    }else{
	                        $transaction_type = 0;
	                    }
	                }else if(in_array($deposit->type,array(9,10,11,12))){
	                    if(preg_match('/bank-/', $deposit->account_id)){
	                        $transaction_type = 17;
	                    }else if(preg_match('/sacco-/', $deposit->account_id)){
	                        $transaction_type = 18;
	                    }else if(preg_match('/mobile-/', $deposit->account_id)){
	                        $transaction_type = 19;
	                    }else if(preg_match('/petty-/', $deposit->account_id)){
	                        $transaction_type = 20;
	                    }else{
	                        $transaction_type = 0;
	                    }
	                }
	                $input[] = array(
	                    'transaction_type'=>$transaction_type,
	                    'group_id'=>$deposit->group_id,
	                    'transaction_date'=>$deposit->deposit_date,
	                    'member_id'=>$deposit->member_id,
	                    'deposit_id'=>$deposit->id,
	                    //'user_id'=>$member->user_id,
	                    'account_id'=>$deposit->account_id,
	                    'contribution_id'=>$deposit->contribution_id,
	                    'description'=>$deposit->deposit_method.' - '.$deposit->description,
	                    'amount'=>$deposit->amount,
	                    'active'=>1,
	                    'created_on'=>time(),
	                    'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
	                    'fine_category_id'=>$deposit->fine_category_id,
	                );
	            }
	        }
	        if($input){
	            if($this->ci->statements_m->insert_statements_batch($input)){
	                return count($input);
	            }
	        }else{
	            return false;
	        }
	    }

	    public function get_group_active_invoices($group_id = 0){

	        $invoices = $this->ci->invoices_m->get_group_active_invoices($group_id);
	        $statements = $this->ci->statements_m->get_group_active_statements($group_id);
	        $invoice_statements = array();
	        foreach($statements as $statement){
	            if($statement->invoice_id){
	                $invoice_statements[] = $statement->id;
	            }
	        }
	        $statement_invoice_ids = array();
	        foreach($statements as $statement):
	            $statement_invoice_ids[] = $statement->invoice_id;
	        endforeach;
	        $orphan_invoices = array();
	        foreach($invoices as $invoice):
	            if(in_array($invoice->id,$statement_invoice_ids)){

	            }else{
	                $orphan_invoices[] = $invoice;
	            }
	        endforeach;

	        $input = array();

	        foreach($orphan_invoices as $orphan_invoice):
	            if($orphan_invoice->type == 1){
	                $input[] = array(
	                    'transaction_type'=>1,
	                    'transaction_date'=>$orphan_invoice->invoice_date,
	                    'contribution_invoice_due_date'=>$orphan_invoice->due_date,
	                    'contribution_id'=>$orphan_invoice->contribution_id,
	                    //'user_id'=>$member->user_id,
	                    'member_id'=>$orphan_invoice->member_id,
	                    'group_id'=>$orphan_invoice->group_id,
	                    'invoice_id'=>$orphan_invoice->id,
	                    'amount'=>$orphan_invoice->amount_payable,
	                    'active'=>1,
	                    'created_on'=>time(),
	                    'is_a_back_dating_record' => $orphan_invoice->is_a_back_dating_record?1:0
	                );
	            }else if($orphan_invoice->type == 2){
	                $input[] = array(
	                    'transaction_type'=>2,
	                    'transaction_date'=>$orphan_invoice->invoice_date,
	                    'contribution_invoice_due_date'=>$orphan_invoice->due_date,
	                    'contribution_id'=>$orphan_invoice->contribution_id,
	                    //'user_id'=>$member->user_id,
	                    'member_id'=>$orphan_invoice->member_id,
	                    'group_id'=>$orphan_invoice->group_id,
	                    'invoice_id'=>$orphan_invoice->id,
	                    'amount'=>$orphan_invoice->amount_payable,
	                    'active'=>1,
	                    'created_on'=>time(),
	                    'is_a_back_dating_record' => $orphan_invoice->is_a_back_dating_record?1:0
	                );
	            }
	        endforeach;
	        
	        if($this->ci->statements_m->insert_statements_batch($input)){
	        	return count($input);
	        }

	    }

	    public function find_past_unsent_contribution_invoices($date =''){
	    	/*if($date){

	        }else{
	            $date = strtotime(date("F j, Y", strtotime( '-1 days' ) ));
	        } */
	        $yestarday = strtotime( '-1 days' );
	        $date = strtotime('27-03-2020');  
	        
			if($date < time()){
				if(date('Ymd',$date) == date('Ymd',time())){
					echo 'today';
				}else{
					$contributions = $this->ci->contributions_m->get_regular_contributions_with_past_invoice_date($date);
					//print_r($contributions); die();
					$successful_entries = 0;
					$queued_success = 0;
			        $successful_next_invoice_date_updates = 0;
			        $unsuccessful_entries = 0;
			        $unsuccessful_next_invoice_date_updates = 0; 
			        $invoice_queue_array = array();       
			        foreach($contributions as $contribution){
			        	if($contribution->invoice_date < $date ){

			        	}else{
				            $active_members = $this->ci->members_m->get_active_group_member_options($contribution->group_id);
				            $member_ids = array();
				            if($contribution->enable_contribution_member_list){
				                $member_ids = $this->ci->contributions_m->get_contribution_member_pairings_array($contribution->id,$contribution->group_id);
				            }else{
				                $member_ids = $this->ci->members_m->get_group_member_ids($contribution->group_id);
				            }
				            if($member_ids){
				                foreach($member_ids as $member_id){
				                    if(array_key_exists($member_id, $active_members)){
				                    	$queued_success++;
				                        $invoice_queue_array[] = array(
				                            'contribution_id'=>$contribution->id,
				                            'member_id'=>$member_id,
				                            'group_id'=>$contribution->group_id,
				                            'invoice_date'=>$contribution->invoice_date,
				                            'due_date'=>$contribution->contribution_date,
				                            'amount_payable'=>$contribution->amount,
				                            'description'=>$contribution->name,
				                            'created_on'=>time(),
				                        );
				                        /*if($this->ci->invoices_m->insert_contribution_invoicing_queue($input)){
				                            $successful_entries++;
				                        }else{
				                            $unsuccessful_entries++;
				                        }*/
				                    }
				                }
				            }


				            if($contribution->contribution_frequency==6){
				                $day_multiplier = 8;
				            }else{
				                $day_multiplier = 1;
				            }

				            //set next invoice date
				            $contribution_date = $this->ci->contribution_invoices->get_regular_contribution_contribution_date(
				                $contribution->contribution_frequency,
				                $contribution->month_day_monthly,
				                $contribution->week_day_monthly,
				                $contribution->week_day_weekly,
				                $contribution->week_day_fortnight,
				                $contribution->week_number_fortnight,
				                $contribution->month_day_multiple,
				                $contribution->week_day_multiple,
				                $contribution->start_month_multiple,
				                $contribution->after_first_contribution_day_option,
				                $contribution->after_first_day_week_multiple,
				                $contribution->after_first_starting_day,
				                $contribution->after_second_contribution_day_option,
				                $contribution->after_second_day_week_multiple,
				                $contribution->after_second_starting_day,
				                ($contribution->contribution_date+(24*60*60*$day_multiplier))
				            );
				            
				            $invoice_date = $contribution_date - (24*60*60*$contribution->invoice_days);
				            $input = array(
				                'invoice_date'=>$invoice_date,
				                'last_invoice_date'=>$contribution->invoice_date,
				                'contribution_date'=>$contribution_date,
				                'modified_on'=>time(),
				            );
				            if($this->ci->contributions_m->update_regular_contribution_setting($contribution->regular_contribution_setting_id,$input)){
				                $successful_next_invoice_date_updates++;
				            }else{
				                $unsuccessful_next_invoice_date_updates++;
				            }
				        }
			        }
			        if($invoice_queue_array){
			        	$this->ci->invoices_m->insert_batch_contribution_invoice_queue($invoice_queue_array);
			        }
			        if($queued_success){
			            echo  $queued_success.' invoices queued.<br/> ';
			        }
			       /* if($successful_entries){
			            echo  $successful_entries.' invoices queued.<br/> ';
			        }*/
			        if($unsuccessful_entries){
			            echo  $unsuccessful_entries.' invoices could not be queued.<br/> ';
			        }
			        if($successful_next_invoice_date_updates){
			            echo  $successful_next_invoice_date_updates.' invoice dates updated.<br/> ';
			        }
			        if($unsuccessful_next_invoice_date_updates){
			            echo  $unsuccessful_next_invoice_date_updates.' invoice dates could not be updated.<br/> ';
			        }
				}
			}else{
			    echo 'in future';
			}
	    }

	    public function find_past_unsent_contribution_fine_invoices($date =''){
	    	/*if($date){

	        }else{
	            $date = strtotime(date("F j, Y", strtotime( '-1 days' ) ));
	        } */  
	        $date = strtotime('26-03-2020');
			if($date < time()){
				if(date('Ymd',$date) == date('Ymd',time())){
					echo 'today';
				}else{
					$contributions = $this->ci->contributions_m->get_contributions_with_past_fine_invoice_date($date);
			        $successful_entries = 0;
			        $successful_next_fine_date_updates = 0;
			        $unsuccessful_entries = 0;
			        $unsuccessful_next_fine_date_updates = 0;

			        $group_ids = array();  
			        $contribution_ids = array();  
			        $member_ids = array();
			        $enable_contribution_member_list_group_ids = array(); 
			        $enable_contribution_member_list_contribution_ids = array(); 
			        $enable_contribution_member_list_member_ids = array(); 

			        foreach ($contributions as $contribution) {
			            $group_ids[] = $contribution->group_id;
			            $contribution_ids[] = $contribution->id;
			            if($contribution->enable_contribution_member_list){
			                $enable_contribution_member_list_group_ids[] = $contribution->group_id;
			                $enable_contribution_member_list_contribution_ids[] = $contribution->id;
			            }
			        }

			        $group_ids = array_unique($group_ids);
			        
			        $contribution_ids = array_unique($contribution_ids);

			        $enable_contribution_member_list_group_ids = array_unique($enable_contribution_member_list_group_ids);

			        $enable_contribution_member_list_contribution_ids = array_unique($enable_contribution_member_list_contribution_ids);

			        $group_member_ids_array = $this->ci->members_m->get_active_group_member_ids_by_group_array($group_ids);

			        $member_ids = $this->ci->members_m->get_active_group_member_ids_array($group_ids);

			        $contribution_member_ids_pairings_array = $this->ci->contributions_m->get_contribution_member_ids_pairings_array($enable_contribution_member_list_group_ids,$enable_contribution_member_list_contribution_ids);
			        
			        // $contribution_balances_array = $this->ci->statements_m->get_contribution_balances_array($group_ids,$member_ids,$contribution_ids,$date);
			        // $invoice_objects_array = $this->ci->statements_m->get_member_contribution_unpaid_invoice_objects_array($group_ids,$member_ids,$contribution_ids);
			        $fine_invoicing_queue = array();
			        foreach ($contributions as $contribution) {
			            # code...
			            $member_ids = array();
			            if($contribution->enable_contribution_member_list){
			                $member_ids = $contribution_member_ids_pairings_array[$contribution->group_id][$contribution->id];
			            }else{
			                $member_ids = $group_member_ids_array[$contribution->group_id];
			            }

			            $contribution_balances_array = $this->ci->statements_m->get_contribution_balances_array(array($contribution->group_id),$member_ids,array($contribution->id),$date);

			            $invoice_objects_array = $this->ci->statements_m->get_member_contribution_unpaid_invoice_objects_array(array($contribution->group_id),$member_ids,array($contribution->id));


			            // if($ignore_contribution_fine_date){
			            //     $member_contribution_balances_array = $this->ci->statements_m->get_member_contribution_balances_array($contribution->id,$date,$contribution->group_id,$fine_date);
			            // }else{
			            //     $member_contribution_balances_array = $this->ci->statements_m->get_member_contribution_balances_array($contribution->id,$date,$contribution->group_id,$date);
			            // }

			            if($member_ids&&$contribution_balances_array){
			                foreach($member_ids as $member_id){
			                    if(isset($contribution_balances_array[$contribution->group_id][$member_id][$contribution->id])){
			                        if($contribution_balances_array[$contribution->group_id][$member_id][$contribution->id] > 0){
			                            if($contribution->percentage_fine_mode == 1 || $contribution->fixed_fine_mode == 1){
			                                //Find all unpaid invoices
			                                $invoices = $invoice_objects_array[$contribution->group_id][$member_id][$contribution->id];
			                                $count = 1;
			                                foreach ($invoices as $invoice) {
			                                    # code...
			                                    $fine_invoicing_queue[] = array(
			                                        'contribution_id'=>$contribution->id,
			                                        'parent_invoice_id'=>$invoice->invoice_id,
			                                        'member_id'=>$member_id,
			                                        'group_id'=>$contribution->group_id,
			                                        'fine_date'=>$contribution->fine_date,
			                                        //'fine_date'=>$fine_date?:time(),
			                                        'fine_type'=>$contribution->fine_type,
			                                        'fixed_amount'=>$contribution->fixed_amount,
			                                        'fixed_fine_chargeable_on'=>$contribution->fixed_fine_chargeable_on,
			                                        'percentage_fine_chargeable_on'=>$contribution->percentage_fine_chargeable_on,
			                                        'fixed_fine_frequency'=>$contribution->fixed_fine_frequency,
			                                        'percentage_fine_frequency'=>$contribution->percentage_fine_frequency,
			                                        'fixed_fine_mode'=>$contribution->fixed_fine_mode,
			                                        'percentage_rate'=>$contribution->percentage_rate,
			                                        'percentage_fine_on'=>$contribution->percentage_fine_on,
			                                        'percentage_fine_mode'=>$contribution->percentage_fine_mode,
			                                        'fine_limit'=>$contribution->fine_limit,
			                                        'fine_sms_notifications_enabled'=>$contribution->fine_sms_notifications_enabled,
			                                        'fine_email_notifications_enabled'=>$contribution->fine_email_notifications_enabled,
			                                        'created_on'=>time(),
			                                    );
			                                    // if($this->ci->invoices_m->insert_contribution_fine_invoicing_queue($input)){
			                                    //     $successful_entries++;
			                                    // }else{
			                                    //     $unsuccessful_entries++;
			                                    // }
			                                    if($contribution->fine_limit == $count){
			                                        break;
			                                    }else if($count == 12){
			                                        break;
			                                    }
			                                    $count++;
			                                }
			                            }else{
			                                $fine_invoicing_queue[] = array(
			                                    'contribution_id'=>$contribution->id,
			                                    'parent_invoice_id'=> NULL,
			                                    'member_id'=>$member_id,
			                                    'group_id'=>$contribution->group_id,
			                                    'fine_date'=>$contribution->fine_date,
			                                    'fine_type'=>$contribution->fine_type,
			                                    'fixed_amount'=>$contribution->fixed_amount,
			                                    'fixed_fine_chargeable_on'=>$contribution->fixed_fine_chargeable_on,
			                                    'percentage_fine_chargeable_on'=>$contribution->percentage_fine_chargeable_on,
			                                    'fixed_fine_frequency'=>$contribution->fixed_fine_frequency,
			                                    'percentage_fine_frequency'=>$contribution->percentage_fine_frequency,
			                                    'fixed_fine_mode'=>$contribution->fixed_fine_mode,
			                                    'percentage_rate'=>$contribution->percentage_rate,
			                                    'percentage_fine_on'=>$contribution->percentage_fine_on,
			                                    'percentage_fine_mode'=>$contribution->percentage_fine_mode,
			                                    'fine_limit'=>$contribution->fine_limit,
			                                    'fine_sms_notifications_enabled'=>$contribution->fine_sms_notifications_enabled,
			                                    'fine_email_notifications_enabled'=>$contribution->fine_email_notifications_enabled,
			                                    'created_on'=>time(),
			                                );
			                                // if($this->ci->invoices_m->insert_contribution_fine_invoicing_queue($input)){
			                                //     $successful_entries++;
			                                // }else{
			                                //     $unsuccessful_entries++;
			                                // }
			                            }
			                        }
			                    }
			                }
			            }
			           
			            $next_contribution_date = $this->ci->contribution_invoices->get_regular_contribution_contribution_date(
			                $contribution->contribution_frequency,
			                $contribution->month_day_monthly,
			                $contribution->week_day_monthly,
			                $contribution->week_day_weekly,
			                $contribution->week_day_fortnight,
			                $contribution->week_number_fortnight,
			                $contribution->month_day_multiple,
			                $contribution->week_day_multiple,
			                $contribution->start_month_multiple,
			                (time()+24*60*60)
			            );
			            $fine_date = $this->ci->contribution_invoices->get_contribution_fine_date($date,$contribution->fine_type,$contribution->fixed_fine_chargeable_on,$contribution->percentage_fine_chargeable_on,$contribution->fixed_fine_frequency,$contribution->percentage_fine_frequency,FALSE,$next_contribution_date);           
			            $input = array(
			                'fine_date'=>$fine_date,
			                'modified_on'=>time(),
			            );
			            if($this->ci->contributions_m->update_contribution_fine_setting($contribution->contribution_fine_setting_id,$input)){
			                $successful_next_fine_date_updates++;
			            }else{
			                $unsuccessful_next_fine_date_updates++;
			            }
			        }

			        if(!empty($fine_invoicing_queue)){
			            if($this->ci->invoices_m->batch_insert_contribution_fine_invoicing_queue($fine_invoicing_queue)){
			                $successful_entries += count($fine_invoicing_queue);
			            }else{
			                $unsuccessful_entries += count($fine_invoicing_queue);
			            }
			        }
			        if($successful_entries){
			            echo  $successful_entries.' fine invoices queued.<br/> ';
			        }
			        if($unsuccessful_entries){
			            echo  $unsuccessful_entries.' fine invoices could not be queued.<br/> ';
			        }
			        if($successful_next_fine_date_updates){
			            echo  $successful_next_fine_date_updates.' fine dates updated.<br/> ';
			        }
			        if($unsuccessful_next_fine_date_updates){
			            echo  $unsuccessful_next_fine_date_updates.' fine dates could not be updated.<br/> ';
			        }
				}
			}else{
			    echo 'in future';
			}
	    }

	    public function find_and_void_queued_invoice_from_date($date =''){
	    	$two_days_ago = strtotime( '-2 days' );
	        $date = strtotime('26-03-2020');
	        $delete_count = 0;
	        $queued_contribution_invoice = $this->ci->invoices_m->get_contribution_invoices_from_certain_date($date,$two_days_ago);
	        if($queued_contribution_invoice){
        		foreach ($queued_contribution_invoice as $key => $invoice) {
        			if($invoice->invoice_date < $date){
        				if($this->ci->invoices_m->delete_contribution_invoice_queue($invoice->id)){
        					$delete_count++;
        				}
        			}
        		}
	        }
	        echo $delete_count .'Deleted succesfully';
	    }

	    public function find_deleted_group_member_and_activate($group_id =0, $member_id = 0){
	    	if($group_id&&$member_id){
	    		$post = $this->ci->members_m->get_deleted_group_member($member_id , $group_id);
	    		if($post){
	    			$input = array(
			            'active' => 1,
			            'is_deleted'=>NULL,
			            'modified_on' => time(),
			            //'modified_by' => $this->user->id
			        );
			        if($this->ci->members_m->update($post->id,$input)){
			        	$this->ci->transactions->activate_all_group_member_transactions($group_id,$member_id);
			            //$this->session->set_flashdata('success',$post->first_name.' '.$post->last_name.' activated successfully. ');
			        }else{

			        	echo "Member could not be activated";
			        }
	    		}
	    	}else{
	    		echo "Group id and member id varibale is required";	
	    	}
	    }
	 
	}
?>