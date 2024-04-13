<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Mobile extends Mobile_Controller{

	protected $deposits_validation_rules = array(
		array(
			'field' => 'transaction_alert_id',
			'label' => 'Transaction Alert',
			'rules' => 'required|numeric'
		),
		array(
			'field' => 'deposit_for',
			'label' => 'Deposit for',
			'rules' => 'callback__valid_deposit_for'
		),
		array(
			'field' => 'contributions',
			'label' => 'Contribution',
			'rules' => 'callback__valid_contribution'
		),
		array(
			'field' => 'members',
			'label' => 'Member',
			'rules' => 'callback__valid_member'
		),
		array(
			'field' => 'fine_categories',
			'label' => 'Fine Categories',
			'rules' => 'callback__valid_fine_category'
		),
		array(
			'field' => 'depositors',
			'label' => 'Depositors',
			'rules' => 'callback__valid_depositor'
		),
		array(
			'field' => 'income_categories',
			'label' => 'Income Categories',
			'rules' => 'callback__valid_income_category'
		),
		array(
			'field' => 'loans',
			'label' => 'Loans',
			'rules' => 'callback__valid_member_loan'
		),
		array(
			'field' => 'from_account_ids',
			'label' => 'Account From',
			'rules' => 'callback__valid_from_account_ids'
		),
		array(
			'field' => 'stock_ids',
			'label' => 'Stocks',
			'rules' => 'callback__valid_stock_ids'
		),
		array(
			'field' => 'asset_ids',
			'label' => 'Assets',
			'rules' => 'callback__valid_asset_ids'
		),
		array(
			'field' => 'money_market_investment_ids',
			'label' => 'Money Markets',
			'rules' => 'callback__valid_money_market_ids'
		),
	);

	protected $withdrawal_validation_rules = array(
		array(
			'field' => 'transaction_alert_id',
			'label' => 'Transaction Alert',
			'rules' => 'required|numeric'
		),
		array(
			'field' => 'withdrawal_fors',
			'label' => 'Withdrawal for',
			'rules' => 'callback__valid_withdrawal_for'
		),
		array(
			'field' => 'contributions',
			'label' => 'Contribution',
			'rules' => 'callback__valid_contribution'
		),
		array(
			'field' => 'members',
			'label' => 'Member',
			'rules' => 'callback__valid_member'
		),
		array(
			'field' => 'loans',
			'label' => 'Loans',
			'rules' => 'callback__valid_member_loan'
		),
		array(
			'field' => 'to_account_ids',
			'label' => 'Account To',
			'rules' => 'callback__valid_from_account_ids'
		),
		array(
			'field' => 'stock_ids',
			'label' => 'Stocks',
			'rules' => 'callback__valid_stock_ids'
		),
		array(
			'field' => 'money_market_investment_ids',
			'label' => 'Money Markets',
			'rules' => 'callback__valid_money_market_ids'
		),
		array(
			'field' => 'asset_ids',
			'label' => 'Assets',
			'rules' => 'callback__valid_asset_ids'
		),
		array(
			'field' => 'bank_loan_ids',
			'label' => 'Bank Loan',
			'rules' => 'callback__valid_bank_loan_ids'
		),
		array(
			'field' => 'expense_categories',
			'label' => 'Expense Category',
			'rules' => 'callback__valid_expense_category'
		),
	);

	protected $payment_validation_rules = array(
		array(
			'field' => 'group_id',
			'label' => 'Group',
			'rules' => 'required|trim|numeric',
		),
		array(
			'field' => 'current_user_id',
			'label' => 'User',
			'rules' => 'required|trim|numeric',
		),
		array(
			'field' => 'amount',
			'label' => 'Amount',
			'rules' => 'required|trim|valid_currency',
		),
		array(
			'field' => 'phone',
			'label' => 'Phone Number',
			'rules' => 'trim|valid_phone',
		),
		array(
			'field' => 'loan_id',
			'label' => 'Loan',
			'rules' => 'trim|callback__valid_loan',
		),
		array(
			'field' => 'contribution_id',
			'label' => 'Contribution',
			'rules' => 'trim|callback__valid_single_contribution',
		),
		array(
			'field' => 'fine_category',
			'label' => 'Fine',
			'rules' => 'trim|callback__valid_single_fine_category',
		),
	);

	protected $deposit_for_options;
	protected $withdrawal_for_options;

	function __construct(){
		parent::__construct();
		$this->load->model('income_categories/income_categories_m');
		$this->load->model('assets/assets_m');
		$this->load->model('expense_categories/expense_categories_m');
		$this->deposit_for_options = $this->transactions->deposit_for_options;
		$this->withdrawal_for_options = $this->transactions->withdrawal_for_options;
	}

	function _valid_deposit_for(){
		$deposit_for = $this->input->post('deposit_for');
		if(empty($deposit_for)){
			$this->form_validation->set_message('_valid_deposit_for','Deposit for cannot be empty');
		}else{
			foreach ($deposit_for as $deposit_for_type) {
				if(!array_key_exists($deposit_for_type, $this->deposit_for_options)){
					$this->form_validation->set_message('_valid_deposit_for','Deposit for is invalid');
					return FALSE;
				}
			}
			return TRUE;
		}
	}

	function _valid_withdrawal_for(){
		$withdrawal_fors = $this->input->post('withdrawal_fors');
		if(empty($withdrawal_fors)){
			$this->form_validation->set_message('_valid_withdrawal_for','Withdrawal for cannot be empty');
		}else{
			foreach ($withdrawal_fors as $withdrawal_for) {
				if(!array_key_exists($withdrawal_for, $this->withdrawal_for_options)){
					$this->form_validation->set_message('_valid_withdrawal_for','Withdrawal for is invalid');
					return FALSE;
				}
			}
			return TRUE;
		}
	}


	function _valid_contribution(){
		$contributions = $this->input->post('contributions');
		$group_id = $this->input->post('group_id');
		if($contributions){
			foreach ($contributions as $contribution_id) {
				if(!$this->contributions_m->contribution_exists_in_group($contribution_id,$group_id)){
					$this->form_validation->set_message('_valid_contribution','Contribution selected does not exist in this group');
					return FALSE;
				}
			}
			return TRUE;
		}else{
			return TRUE;
		}
	}

	function _valid_member(){
		$members = $this->input->post('members');
		$group_id = $this->input->post('group_id');
		if($members){
			foreach ($members as $member_id) {
				if(!$this->members_m->get_member_where_member_id($member_id,$group_id)){
					$this->form_validation->set_message('_valid_member','One of the members does not exist in the group');
					return FALSE;
				}
			}
			return TRUE;
		}else{
			return TRUE;
		}
	}

	function _valid_fine_category(){
		$fine_categories = $this->input->post('fine_categories');
		$group_id = $this->input->post('group_id');
		if($fine_categories){
			foreach ($fine_categories as $fine_category) {
				if(preg_match('/contribution-/', $fine_category)){
		            $contribution_id = str_replace('contribution-','',$fine_category);
		            $fine_category_id = 0;
		        }else if(preg_match('/fine_category-/', $fine_category)){
		            $fine_category_id = str_replace('fine_category-','',$fine_category);
		            $contribution_id = 0;
		        }else{
		            $fine_category_id = 0;
		            $contribution_id = 0;
		        }

		        if($contribution_id){
		        	if(!$this->contributions_m->contribution_exists_in_group($contribution_id,$group_id)){
						$this->form_validation->set_message('_valid_fine_category','Fine Category  selected does not exist in this group');
						return FALSE;
					}
		        }

		        if($fine_category_id){
		        	if(!$this->fine_categories_m->get_group_fine_category($fine_category_id,$group_id)){
						$this->form_validation->set_message('_fine_category_exists','Fine Category  selected does not exist in this group');
						return FALSE;
					}
		        }
			}
			return TRUE;
		}else{
			return TRUE;
		}
	}

	function _valid_single_fine_category(){
		$fine_category = $this->input->post('fine_category');
		$group_id = $this->input->post('group_id');
		if($fine_category && $group_id){
			if(preg_match('/contribution-/', $fine_category)){
	            $contribution_id = str_replace('contribution-','',$fine_category);
	            $fine_category_id = 0;
	        }else if(preg_match('/fine_category-/', $fine_category)){
	            $fine_category_id = str_replace('fine_category-','',$fine_category);
	            $contribution_id = 0;
	        }else{
	            $fine_category_id = 0;
	            $contribution_id = 0;
	        }

	        if($contribution_id){
	        	if(!$this->contributions_m->contribution_exists_in_group($contribution_id,$group_id)){
					$this->form_validation->set_message('_valid_fine_category','Fine Category  selected does not exist in this group');
					return FALSE;
				}
	        }else if($fine_category_id){
	        	if(!$this->fine_categories_m->get_group_fine_category($fine_category_id,$group_id)){
					$this->form_validation->set_message('_fine_category_exists','Fine Category  selected does not exist in this group');
					return FALSE;
				}
	        }else{
	        	return TRUE;
	        }
		}else{
			return TRUE;
		}
	}

	function _valid_depositor(){
		$depositors = $this->input->post('depositors');
		$group_id = $this->input->post('group_id');
		if($depositors){
			foreach ($depositors as $depositor_id) {
				if(!$this->depositors_m->get_group_depositor($depositor_id,$group_id)){
					$this->form_validation->set_message('_valid_depositor','Depositor selected does not exist in this group');
					return FALSE;
				}
			}
			return TRUE;
		}else{
			return TRUE;
		}
	}

	function _valid_income_category(){
		$income_categories = $this->input->post('income_categories');
		$group_id = $this->input->post('group_id');
		if($income_categories){
			foreach ($income_categories as $income_category_id) {
				if(!$this->income_categories_m->income_category_exists($income_category_id,$group_id)){
					$this->form_validation->set_message('_valid_income_category','Income Category selected does not exist in this group');
					return FALSE;
				}
			}
			return TRUE;
		}else{
			return TRUE;
		}
	}

	function _valid_member_loan(){
		$loans = $this->input->post('loans');
		$members = $this->input->post('members');
		$group_id = $this->input->post('group_id');
		if($loans){
			foreach ($loans as $key => $loan_id) {
				$member_id = $members[$key];
				if(!$this->loans_m->loan_exists_in_group($loan_id,$group_id,$member_id)){
					$this->form_validation->set_message('_valid_member_loan','Loan selected does not exist in this group');
					return FALSE;
				}
			}
			return TRUE;
		}else{
			return TRUE;
		}
	}

	function _valid_loan(){
		$loan_id = $this->input->post('loan_id');
		$member_id = $this->input->post('member_id');
		$group_id = $this->input->post('group_id');
		if($member_id && $loan_id){
			if(!$this->loans_m->loan_exists_in_group($loan_id,$group_id,$member_id)){
				$this->form_validation->set_message('_valid_loan','Loan selected does not exist in this group');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}

	function _valid_single_contribution(){
		$contribution_id = $this->input->post('contribution_id');
		$group_id = $this->input->post('group_id');
		if($contribution_id && $group_id){
			if(!$this->contributions_m->contribution_exists_in_group($contribution_id,$group_id)){
				$this->form_validation->set_message('_valid_single_contribution','Contribution selected does not exist in this group');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}

	function _valid_from_account_ids(){
		$from_account_ids = $this->input->post('from_account_ids');
		$group_id = $this->input->post('group_id');
		if($from_account_ids){
			foreach ($from_account_ids as $account_id) {
				if(!$this->accounts_m->check_if_group_account_exists($account_id,$group_id)){
					$this->form_validation->set_message('_valid_from_account_ids','Group account does not exist');
					return FALSE;
				}
			}
			return TRUE;
		}else{
			return TRUE;
		}
	}

	function _valid_stock_ids(){
		$stock_ids = $this->input->post('stock_ids');
		$group_id = $this->input->post('group_id');
		$number_of_shares_solds = $this->input->post('number_of_shares_solds');
		if($stock_ids){
			foreach ($stock_ids as $key=>$stock_id){
				$stock = $this->stocks_m->get_group_stock($stock_id,$group_id);
				if(!$stock){
					$this->form_validation->set_message('_valid_stock_ids','Stock selected does not exist within the group');
					return FALSE;
				}else{
					$number_of_shares_available = $stock->number_of_shares-$stock->number_of_shares_sold;
					$number_of_shares_to_be_sold = $number_of_shares_solds[$key];

					if($number_of_shares_available<$number_of_shares_to_be_sold){
			            $this->form_validation->set_message('_valid_stock_ids', 'Number of shares  to be sold ie. '.$number_of_shares_to_be_sold.' cannot exceed available shares ie. '.$number_of_shares_available);
			            return FALSE;
			        }else{
			            return TRUE;
			        }
				}

			}
			return TRUE;
		}else{
			return TRUE;
		}
	}

	function _valid_asset_ids(){
		$asset_ids = $this->input->post('asset_ids');
		$group_id = $this->input->post('group_id');
		if($asset_ids){
			foreach ($asset_ids as $asset_id) {
				if(!$this->assets_m->get($asset_id,$group_id)){
					$this->form_validation->set_message('_valid_asset_ids','Asset selected does not exist');
					return FALSE;
				}
			}
			return TRUE;
		}else{
			return TRUE;
		}
	}

	function _valid_money_market_ids(){
		$money_market_investment_ids = $this->input->post('money_market_investment_ids');
		$group_id = $this->input->post('group_id');
		if($money_market_investment_ids){
			foreach ($money_market_investment_ids as $money_market_investment_id) {
				if(!$this->money_market_investments_m->get($money_market_investment_id,$group_id)){
		            $this->form_validation->set_message('_valid_money_market_ids','Money market investment selected does not exist');
		            return FALSE;
		        }
			}
			return TRUE;
		}else{
			return TRUE;
		}
	}

	function _valid_bank_loan_ids(){
		$bank_loan_ids = $this->input->post('bank_loan_ids');
		$group_id = $this->input->post('group_id');
		if($bank_loan_ids){
			foreach ($bank_loan_ids as $bank_loan_id) {
				if(!$this->bank_loans_m->get($bank_loan_id,$group_id)){
		            $this->form_validation->set_message('_valid_bank_loan_ids','Loan selected does not exist');
		            return FALSE;
		        }
			}
			return TRUE;
		}else{
			return TRUE;
		}
	}

	function _valid_expense_category(){
		$expense_categories = $this->input->post('expense_categories');
		$group_id = $this->input->post('group_id');
		if($expense_categories){
			foreach ($expense_categories as $expense_category_id) {
				if(!$this->expense_categories_m->expense_category_exists($expense_category_id,$group_id)){
					$this->form_validation->set_message('_valid_expense_category',"Expense Category selected does not exist");
					return FALSE;
				}
			}
			return TRUE;
		}else{
			return TRUE;
		}
	}


	function get_group_unreconcilled_deposits(){
		foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
        	$this->ion_auth->update_last_login($this->user->id);
        	$group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                	$group_partner_bank_account_number_list = $this->bank_accounts_m->get_group_verified_bank_account_number_list($this->group->id);
                   	$group_partner_mobile_money_account_number_list = $this->mobile_money_accounts_m->get_group_verified_mobile_money_account_number_list($this->group->id);
                	$unreconciled_deposits = $this->transaction_alerts_m->get_group_unreconciled_deposits($group_partner_bank_account_number_list,$group_partner_mobile_money_account_number_list);
        			$bank_account_options = $this->bank_accounts_m->get_group_verified_partner_bank_account_options($this->group->id);
        			$mobile_money_account_options = $this->mobile_money_accounts_m->get_group_verified_partner_mobile_account_options($this->group->id);

        			$posts = array();
        			if($unreconciled_deposits){
        				foreach ($unreconciled_deposits as $unreconciled_deposit) {
        					$account_details = isset($bank_account_options[$unreconciled_deposit->account_number])?$bank_account_options[$unreconciled_deposit->account_number]:(isset($mobile_money_account_options[$unreconciled_deposit->account_number])?$mobile_money_account_options[$unreconciled_deposit->account_number]:'');
        					$posts[] = array(
        						'type' => 1,
        						'transaction_type' => $unreconciled_deposit->transaction_type,
        						'description' => $unreconciled_deposit->description,
        						'amount' => $unreconciled_deposit->amount,
        						'transactionAlertId' => $unreconciled_deposit->id,
        						'isReconciled' => $unreconciled_deposit->reconciled?1:0,
        						'particulars' => $unreconciled_deposit->particulars,
        						'accountNUmber' => $unreconciled_deposit->account_number,
        						'transaction_id' => $unreconciled_deposit->transaction_id,
        						'account_details' => $account_details,
        						'transactionDate' => timestamp_to_mobile_shorttime($unreconciled_deposit->transaction_date),
        					);
        				}
        			}
        			$response = array(
        				'status' => 1,
        				'message' => 'successful',
        				'unreconciled_deposits' => $posts,
        			);
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
	}

	function get_group_unreconcilled_withdrawals(){
		foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
        	$this->ion_auth->update_last_login($this->user->id);
        	$group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                	$group_partner_bank_account_number_list = $this->bank_accounts_m->get_group_verified_bank_account_number_list($this->group->id);
                   	$group_partner_mobile_money_account_number_list = $this->mobile_money_accounts_m->get_group_verified_mobile_money_account_number_list($this->group->id);
                	$unreconciled_deposits = $this->transaction_alerts_m->get_group_unreconciled_withdrawals($group_partner_bank_account_number_list,$group_partner_mobile_money_account_number_list);
        			$bank_account_options = $this->bank_accounts_m->get_group_verified_partner_bank_account_options($this->group->id);
        			$mobile_money_account_options = $this->mobile_money_accounts_m->get_group_verified_partner_mobile_account_options($this->group->id);

        			$posts = array();
        			if($unreconciled_deposits){
        				foreach ($unreconciled_deposits as $unreconciled_deposit) {
        					$account_details = isset($bank_account_options[$unreconciled_deposit->account_number])?$bank_account_options[$unreconciled_deposit->account_number]:(isset($mobile_money_account_options[$unreconciled_deposit->account_number])?$mobile_money_account_options[$unreconciled_deposit->account_number]:'');
        					$posts[] = array(
        						'type' => 2,
        						'transaction_type' => $unreconciled_deposit->transaction_type,
        						'description' => $unreconciled_deposit->description,
        						'amount' => $unreconciled_deposit->amount,
        						'transactionAlertId' => $unreconciled_deposit->id,
        						'isReconciled' => $unreconciled_deposit->reconciled?1:0,
        						'particulars' => $unreconciled_deposit->particulars,
        						'accountNUmber' => $unreconciled_deposit->account_number,
        						'transaction_id' => $unreconciled_deposit->transaction_id,
        						'account_details' => $account_details,
        						'transactionDate' => timestamp_to_mobile_shorttime($unreconciled_deposit->transaction_date),
        					);
        				}
        			}
        			$response = array(
        				'status' => 1,
        				'message' => 'successful',
        				'unreconciled_withdrawals' => $posts,
        			);
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
	}

	function reconcile_deposits(){
		$deposit_for = array();
		$contributions = array();
		$fine_categories = array();
		$members = array();
		$descriptions = array();
		$depositors = array();
		$income_categories = array();
		$loans = array();
		$amount_payables = array();
		$from_account_ids = array();
		$stock_ids = array();
		$number_of_shares_solds = array();
		$price_per_shares = array();
		$asset_ids = array();
		$money_market_investment_ids = array();
		$enable_notifications = isset($result->enable_email_and_sms_notification)?$result->enable_email_and_sms_notification:0;
		$amounts = array();
		foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
            	if($key == "reconcile_deposits_break_down" && is_array($value)){
					foreach ($value as $key_value => $value_value) {
						$deposit_for[$key_value] = $value_value->deposit_for_type;
						$amounts[$key_value] = $value_value->amount;
						if(isset($value_value->contribution_id)){
							$contributions[$key_value]= $value_value->contribution_id;
						}
						if(isset($value_value->fine_category_id)){
							$fine_categories[$key_value] = $value_value->fine_category_id;
						}
						if(isset($value_value->fine_category_id)){
							$fine_categories[$key_value] = $value_value->fine_category_id;
						}
						if(isset($value_value->member_id)){
							$members[$key_value] = $value_value->member_id;
						}
						if(isset($value_value->description)){
							$descriptions[$key_value] = $value_value->description;
						}else{
							$descriptions[$key_value] = "";
						}
						if(isset($value_value->depositor_id)){
							$depositors[$key_value] = $value_value->depositor_id;
						}
						if(isset($value_value->income_category_id)){
							$income_categories[$key_value] = $value_value->income_category_id;
						}
						if(isset($value_value->loan_id)){
							$loans[$key_value] = $value_value->loan_id;
						}
						if(isset($value_value->amount_payable)){
							$amount_payables[$key_value] = $value_value->amount_payable;
						}
						if(isset($value_value->account_id)){
							$from_account_ids[$key_value] = $value_value->account_id;
						}
						if(isset($value_value->stock_id)){
							$stock_ids[$key_value] = $value_value->stock_id;
						}
						if(isset($value_value->number_of_share_sold)){
							$number_of_shares_solds[$key_value] = $value_value->number_of_share_sold;
						}
						if(isset($value_value->price_per_share)){
							$price_per_shares[$key_value] = $value_value->price_per_share;
						}
						if(isset($value_value->asset_id)){
							$asset_ids[$key_value] = $value_value->asset_id;
						}
						if(isset($value_value->money_market_investment_cash_in_id)){
							$money_market_investment_ids[$key_value] = $value_value->money_market_investment_cash_in_id;
						}
						if(isset($value_value->money_market_investment_cash_in_id)){
							$money_market_investment_ids[$key_value] = $value_value->money_market_investment_cash_in_id;
						}
					}
				}else{
					$_POST[$key] = $value;
				}
            }
        }
        $_POST['deposit_for'] = $deposit_for;
		$_POST['amounts'] = $amounts;
		$_POST['contributions'] = $contributions;
		$_POST['fine_categories'] = $fine_categories;
		$_POST['members'] = $members;
		$_POST['descriptions'] = $descriptions;
		$_POST['depositors'] = $depositors;
		$_POST['income_categories'] = $income_categories;
		$_POST['loans'] = $loans;
		$_POST['amount_payables'] = $amount_payables;
		$_POST['from_account_ids'] = $from_account_ids;
		$_POST['stock_ids'] = $stock_ids;
		$_POST['number_of_shares_solds'] = $number_of_shares_solds;
		$_POST['price_per_shares'] = $price_per_shares;
		$_POST['asset_ids'] = $asset_ids;
		$_POST['money_market_investment_ids'] = $money_market_investment_ids;
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
        	$this->ion_auth->update_last_login($this->user->id);
        	$group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                	$error_message = '';
					$this->form_validation->set_rules($this->deposits_validation_rules);
					if($this->form_validation->run()){
						$transaction_alert_id = $this->input->post('transaction_alert_id');
						$transaction_alert = $this->transaction_alerts_m->get($transaction_alert_id);
						if($transaction_alert){
							if($transaction_alert){
								if($transaction_alert->reconciled==1){
									$response = array(
										'status' => 0,
										'time' => time(),
										'message' => "Transaction already reconciled",

									);
					            }else{
									$account_id = $this->bank_accounts_m->get_group_bank_account_id_by_account_number($transaction_alert->account_number,$this->group->id);
							        if($account_id){
							          $account_id = 'bank-'.$account_id;  
							        }else{
							            $account_id = $this->mobile_money_accounts_m->get_group_mobile_money_account_id_by_account_number($transaction_alert->account_number,$this->group->id);
							            if($account_id){
							               $account_id = 'mobile-'.$account_id; 
							            }else{
							                
							            }
							        }

							        $amount_reconciled = 0;
							        if(isset($amounts)):
							            foreach($amounts as $amount){
							                $amount_reconciled+=valid_currency($amount);
							            }
							        endif;
							        if(isset($deposit_for)&&$transaction_alert&&$account_id){
						                $entries_are_valid = TRUE;
						                $count = count($deposit_for)-1;
						                for($i=0;$i<=$count;$i++):
						                    if(isset($deposit_for[$i])){
						                        if($deposit_for[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            //do nothing for now
						                        }
						                    }


						                    //Members
						                    if(isset($members[$i])){
						                        if($members[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(is_numeric($members[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE;
						                            }
						                        }
						                    }

						                    //Members
						                    if(isset($loans[$i])){
						                        if($loans[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(is_numeric($loans[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE;
						                            }
						                        }
						                    }

						                    //Depositors
						                    if(isset($depositors[$i])){
						                        if($depositors[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(is_numeric($depositors[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE;
						                            }
						                        }
						                    }

						                    //Contributions
						                    if(isset($income_categories[$i])){
						                        if($income_categories[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(is_numeric($income_categories[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE;
						                            }
						                        }
						                    }

						                    //Contributions
						                    if(isset($contributions[$i])){
						                        if($contributions[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(is_numeric($contributions[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE;
						                            }
						                        }
						                    }

						                    //from account ids
						                    if(isset($from_account_ids[$i])){
						                        if($from_account_ids[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            //do nothing for now
						                        }
						                    }


						                    if(isset($deposit_for[$i])){
						                        if($deposit_for[$i]==3||$deposit_for[$i]==6){
						                        	if($deposit_for[$i]==6){
						                        		$descriptions[$i] = "Loan from a Bank instution of Amount ".$amounts[$i];
						                        	}

						                            if($descriptions[$i]==''){
						                                $entries_are_valid = FALSE;
						                            }else{
						                                //do nothing for now
						                            }
						                        }
						                    }

						                    //Fine category
						                    if(isset($fine_categories[$i])){
						                        if($fine_categories[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            //do nothing for now
						                        }
						                    }
						                    //amounts
						                    if(isset($amounts[$i])){
						                        if($amounts[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(valid_currency($amounts[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE; 
						                            }
						                        }
						                    }
						                    //amounts  payables
						                    if(isset($amount_payables[$i])){
						                        if($amount_payables[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(valid_currency($amount_payables[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE; 
						                            }
						                        }
						                    }
						                    //price per shares
						                    if(isset($price_per_shares[$i])){
						                        if($price_per_shares[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(valid_currency($price_per_shares[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE; 
						                            }
						                        }
						                    }
						                    //number of shares sold
						                    if(isset($number_of_shares_solds[$i])){
						                        if($number_of_shares_solds[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(is_numeric($number_of_shares_solds[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE; 
						                            }
						                        }
						                    }
						                    //stock id
						                    if(isset($stock_ids[$i])){
						                        if($stock_ids[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(is_numeric($stock_ids[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE; 
						                            }
						                        }
						                    }

						                    //asset id
						                    if(isset($asset_ids[$i])){
						                        if($asset_ids[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(is_numeric($asset_ids[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE; 
						                            }
						                        }
						                    }
						                    //money market investment id
						                    if(isset($money_market_investment_ids[$i])){
						                        if($money_market_investment_ids[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(is_numeric($money_market_investment_ids[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE; 
						                            }
						                        }
						                    }
						                endfor;
							        }else{
							           $entries_are_valid = FALSE; 
							        }

							        if($amount_reconciled==$transaction_alert->amount){

							        }else{
							        	$response = array(
						    				'status' => 0,
						    				'time' => time(),
						    				'message' => 'The amount reconciled has to be equal to the amount deposited',
						    			);
							            $entries_are_valid = FALSE; 
							        }
							        if($entries_are_valid){
							            //make entries

							            $status = TRUE;
							            $count = count($deposit_for)-1;
							            for($i=0;$i<=$count;$i++):
							                if($deposit_for[$i]==1){
							                    //contribution payment
							                    if($this->transactions->record_contribution_payment(
							                    	$this->group->id,
							                    	$transaction_alert->transaction_date,
							                    	$members[$i],
							                    	$contributions[$i],
							                    	$account_id,1,
							                    	$descriptions[$i],
							                    	valid_currency($amounts[$i]),
							                    	$enable_notifications,
							                    	$enable_notifications,
							                    	$transaction_alert->id)){
							                        //update transaction alerts
							                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }
							                }else if($deposit_for[$i]==2){
							                    //fine payment
							                    if($this->transactions->record_fine_payment($this->group->id,$transaction_alert->transaction_date,$members[$i],$fine_categories[$i],$account_id,1,$descriptions[$i],valid_currency($amounts[$i]),$enable_notifications,$enable_notifications,$transaction_alert->id)){
							                        //update transaction alerts
							                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }
							                }else if($deposit_for[$i]==3){
							                    //miscellaneous payment
							                    if($this->transactions->record_miscellaneous_payment($this->group->id,$transaction_alert->transaction_date,$members[$i],$account_id,1,$descriptions[$i],valid_currency($amounts[$i]),$enable_notifications,$enable_notifications,$transaction_alert->id)){
							                        //update transaction alerts
							                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }
							                }else if($deposit_for[$i]==4){
							                    //income deposit

							                    if($this->transactions->record_income_deposit(
							                    	$this->group->id,
							                    	$transaction_alert->transaction_date,
							                    	$depositors[$i],
							                    	$income_categories[$i],
							                    	$account_id,1,
							                    	$descriptions[$i],
							                    	valid_currency($amounts[$i]),
							                    	$transaction_alert->id)){//update transaction alerts
							                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }
							                }else if($deposit_for[$i]==5){
							                    //loan repayment
							                    $member = $this->members_m->get_group_member($members[$i],$this->group->id);
							                    if($member){
							                        if($this->loan->record_loan_repayment(
							                        	$this->group->id,
							                        	$transaction_alert->transaction_date,
							                        	$member,
							                        	$loans[$i],
							                        	$account_id,1,
							                        	$descriptions[$i],
							                        	valid_currency($amounts[$i]),
							                        	$enable_notifications,
							                        	$enable_notifications,
							                        	$member,
							                        	$member->user_id,
							                        	$transaction_alert->id)){
							                            //update transaction alerts
							                            if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

							                            }else{
							                                $status = FALSE;
							                            }
							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }
							                }else if($deposit_for[$i]==6){
							                    //bank loan disbursement disbursement deposit
							                    if($this->transactions->create_bank_loan($this->group->id,$descriptions[$i],valid_currency($amounts[$i]),$amount_payables[$i],$amount_payables[$i],$transaction_alert->transaction_date,$transaction_alert->transaction_date,$account_id,0,$transaction_alert->id)){
							                        //update transaction alerts
							                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }
							                }else if($deposit_for[$i]==7){
							                    //incoming bank transfer
							                    if($this->transactions->record_account_transfer($this->group->id,$transaction_alert->transaction_date,$from_account_ids[$i],$account_id,valid_currency($amounts[$i]),$descriptions[$i],$transaction_alert->id)){
							                        //update transaction alerts
							                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){
														if($transaction_alert = $this->transaction_alerts_m->get_group_matching_withdrawal_transaction_alert($this->group->id,$transaction_alert->transaction_date,$from_account_ids[$i],$account_id,valid_currency($amounts[$i]))){
							                                if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

							                                }else{
							                                    return FALSE;
							                                }
							                            }else{

							                            }
							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }
							                }else if($deposit_for[$i]==8){
							                    //stock sales
							                    $stock = $this->stocks_m->get_group_stock($stock_ids[$i],$this->group->id);
							                    if($stock){
							                        if($this->transactions->record_stock_sale($this->group->id,
							                            $stock_ids[$i],$transaction_alert->transaction_date,$account_id,$number_of_shares_solds[$i],
							                            valid_currency($price_per_shares[$i]),$stock->number_of_shares_sold,$transaction_alert->id)){
							                            //update transaction alerts
							                            if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

							                            }else{
							                                $status = FALSE;
							                            }
							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }   
							                }else if($deposit_for[$i]==9){
							                    //asset sale
							                    if($this->transactions->record_asset_sale_deposit($this->group->id,$asset_ids[$i],$transaction_alert->transaction_date,$account_id,valid_currency($amounts[$i]),$transaction_alert->id)){
							                        //update transaction alerts
							                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }
							                }else if($deposit_for[$i]==10){
							                    //money market investment cash in
							                    if($this->transactions->record_money_market_investment_cash_in_deposit($this->group->id,$money_market_investment_ids[$i],$transaction_alert->transaction_date,$account_id,valid_currency($amounts[$i]),$transaction_alert->id)){
							                        //update transaction alerts
							                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }
							                }
							            endfor;
							            if($status){
							                $response = array(
							                	'status' => 1,
							                	'time' => time(),
							                	'message' => 'Reconcillation successfully recorded',
							                );
							            }else{
							            	$response = array(
							                	'status' => 0,
							                	'time' => time(),
							                	'message' => 'Something went wrong',
							                );
							            }
							        }else{
							            $response = array(
						    				'status' => 0,
						    				'time' => time(),
						    				'message' => 'All fields on the form are required. Kindly review your entries and try again'
						    			);
							        }
					            }
					            
							}else{
								$response = array(
				    				'status' => 0,
				    				'time' => time(),
				    				'message' => 'Error with the transaction alert',
				    			);
							}
						}else{
							$response = array(
			    				'status' => 0,
			    				'time' => time(),
			    				'message' => 'Transaction alert error',
			    			);
						}
					}else{
						$post = array();
			            $form_errors = $this->form_validation->error_array();
						foreach ($form_errors as $key => $value) {
							$post[$key] = $value;
						}
			            $response = array(
			    				'status' => 0,
			    				'time' => time(),
			    				'message' => 'Form validation failed',
			    				'validation_errors' => $post,
			    			);
					}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
	}

	function reconcile_withdrawals(){
		$withdrawal_fors = array();
		$amounts = array();
		$expense_categories = array();
		$descriptions = array();
		$asset_ids = array();
		$loans = array();
		$stock_names = array();
		$number_of_shares = array();
		$price_per_shares = array();
		$money_market_investment_names = array();
		$money_market_investment_ids = array();
		$members = array();
		$contributions = array();
		$bank_loan_ids = array();
		$to_account_ids = array();
		foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
            	if($key == "reconcile_withdrawal_break_down" && is_array($value)){
					foreach ($value as $key_value => $value_value) {
						$withdrawal_fors[$key_value] = $value_value->withdrawal_for_type;
						$amounts[$key_value] = currency($value_value->amount);
						if(isset($value_value->expense_category_id)){
							$expense_categories[$key_value] = $value_value->expense_category_id;
						}
						if(isset($value_value->description)){
							$descriptions[$key_value] = trim($value_value->description);
						}else{
							$descriptions[$key_value] = '';
						}
						if(isset($value_value->asset_id)){
							$asset_ids[$key_value] = $value_value->asset_id;
						}
						if(isset($value_value->loan_id)){
							$loans[$key_value] = $value_value->loan_id;
						}
						if(isset($value_value->stock_name)){
							$stock_names[$key_value] = trim($value_value->stock_name);
						}
						if(isset($value_value->number_of_shares)){
							$number_of_shares[$key_value] = is_numeric($value_value->number_of_shares)?$value_value->number_of_shares:0;
						}
						if(isset($value_value->price_per_share)){
							$price_per_shares[$key_value] = currency($value_value->price_per_share);
						}
						if(isset($value_value->money_market_investment_name)){
							$money_market_investment_names[$key_value] = trim($value_value->money_market_investment_name);
						}
						if(isset($value_value->money_market_investment_id)){
							$money_market_investment_ids[$key_value] = $value_value->money_market_investment_id;
						}
						if(isset($value_value->member_id)){
							$members[$key_value] = $value_value->member_id;
						}
						if(isset($value_value->contribution_id)){
							$contributions[$key_value] = $value_value->contribution_id;
						}
						if(isset($value_valuevalue->bank_loan_id)){
							$bank_loan_ids[$key_value] = $value_value->bank_loan_id;
						}
						if(isset($value_value->account_id)){
							$to_account_ids[$key_value] = $value_value->account_id;
						}
					}
				}else{
					$_POST[$key] = $value;
				}
			}
        }
        $_POST['withdrawal_fors'] = $withdrawal_fors;
		$_POST['amounts'] = $amounts;
		$_POST['expense_categories'] = $expense_categories;
		$_POST['descriptions'] = $descriptions;
		$_POST['asset_ids'] = $asset_ids;
		$_POST['loans'] = $loans;
		$_POST['stock_names'] = $stock_names;
		$_POST['number_of_shares'] = $number_of_shares;
		$_POST['price_per_shares'] = $price_per_shares;
		$_POST['money_market_investment_names'] = $money_market_investment_names;
		$_POST['money_market_investment_ids'] = $money_market_investment_ids;
		$_POST['contributions'] = $contributions;
		$_POST['members'] = $members;
		$_POST['bank_loan_ids'] = $bank_loan_ids;
		$_POST['to_account_ids'] = $to_account_ids;
		$user_id = $this->input->post('user_id')?:0;
		if($this->user = $this->ion_auth->get_user($user_id)){
        	$this->ion_auth->update_last_login($this->user->id);
        	$group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                	$error_message = '';
					$this->form_validation->set_rules($this->withdrawal_validation_rules);
					if($this->form_validation->run()){
						$transaction_alert_id = $this->input->post('transaction_alert_id');
						$transaction_alert = $this->transaction_alerts_m->get($transaction_alert_id);
						if($transaction_alert){
							if($transaction_alert){
								if($transaction_alert->reconciled==1){
									$response = array(
										'status' => 0,
										'time' => time(),
										'message' => "Transaction already reconciled",

									);
					            }else{
									$account_id = $this->bank_accounts_m->get_group_bank_account_id_by_account_number($transaction_alert->account_number,$this->group->id);
							        if($account_id){
							          $account_id = 'bank-'.$account_id;  
							        }else{
							            $account_id = $this->mobile_money_accounts_m->get_group_mobile_money_account_id_by_account_number($transaction_alert->account_number,$this->group->id);
							            if($account_id){
							               $account_id = 'mobile-'.$account_id; 
							            }else{
							                
							            }
							        }
							        $amount_reconciled = 0;
							        $loan_match_error = FALSE;
							        if(isset($amounts)):
							            foreach($amounts as $amount){
							                $amount_reconciled+=valid_currency($amount);
							            }
							        endif;
							        if(isset($withdrawal_fors)&&$transaction_alert&&$account_id){
						                $loan_match_amount = 0;
						                $loan_recorded_amount = 0;
						                $loan_selections_are_valid = TRUE;
						                $loan_ids_array = array();
						                $entries_are_valid = TRUE;
						                $count = count($withdrawal_fors)-1;

						                for($i=0;$i<=$count;$i++):
						                    if(isset($withdrawal_fors[$i])){
						                        if($withdrawal_fors[$i]==3){
						                            if($members[$i]&&$loans[$i]){
						                                if($loan = $this->loans_m->get_group_loan($loans[$i],$this->group->id)){
						                                    $loan_recorded_amount += $loan->loan_amount;
						                                    if(in_array($loan->id,$loan_ids_array)){
						                                        $loan_selections_are_valid = FALSE;
						                                    }else{
						                                        $loan_ids_array[] = $loan->id;
						                                    }
						                                }
						                                if($amounts[$i]){
						                                    $loan_match_amount += valid_currency($amounts[$i]);
						                                }
						                            }
						                        }
						                    }
						                endfor;

						                if($loan_recorded_amount==$loan_match_amount){

						                }else{
						                    $loan_match_error = TRUE;
						                    $entries_are_valid = FALSE;
						                }

						                if($loan_selections_are_valid){

						                }else{
						                    $entries_are_valid = FALSE;
						                }

						                for($i=0;$i<=$count;$i++):

						                    if(isset($withdrawal_fors[$i])){
						                        if($withdrawal_fors[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            //do nothing for now
						                        }
						                    }


						                    //loans
						                    if(isset($loans[$i])){
						                        if($loans[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(is_numeric($loans[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE;
						                            }
						                        }
						                    }
						                    if(isset($withdrawal_fors[$i])){
						                        if($withdrawal_fors[$i]==3){
						                            if($members[$i]&&$loans[$i]){
						                                $loan = $this->loans_m->get_group_loan($loans[$i],$this->group->id);
						                                if($loan){
						                                    /*
						                                    if(($loan->member_id==$members[$i])&&('bank-'.$account_id==$loan->account_id)&&($amounts[$i]==$loan->loan_amount)){
						                                        //do nothing for now
						                                    }else{
						                                        $loan_match_error = TRUE;
						                                        $entries_are_valid = FALSE;
						                                    }
						                                    */
						                                }else{
						                                    $entries_are_valid = FALSE;
						                                }
						                            }else{
						                                $entries_are_valid = FALSE;
						                            }
						                        }
						                    }

						                    //amounts
						                    if(isset($amounts[$i])){
						                        if($amounts[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(valid_currency($amounts[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE; 
						                            }
						                        }
						                    }


						                    //expense category id
						                    if(isset($expense_categories[$i])){
						                        if($expense_categories[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(is_numeric($expense_categories[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE; 
						                            }
						                        }
						                    }

						                    //to account ids
						                    if(isset($to_account_ids[$i])){
						                        if($to_account_ids[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            //do nothing for now
						                        }
						                    }
						                    //asset id
						                    if(isset($asset_ids[$i])){
						                        if($asset_ids[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(is_numeric($asset_ids[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE; 
						                            }
						                        }
						                    }
						                    //asset id
						                    if(isset($bank_loan_ids[$i])){
						                        if($bank_loan_ids[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(is_numeric($bank_loan_ids[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE; 
						                            }
						                        }
						                    }
						                    //asset id
						                    if(isset($money_market_investment_ids[$i])){
						                        if($money_market_investment_ids[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(is_numeric($money_market_investment_ids[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE; 
						                            }
						                        }
						                    }
						                    //stock names
						                    if(isset($stock_names[$i])){
						                        if($stock_names[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                                //do nothing for now
						                            
						                        }
						                    }
						                    //number of shares
						                    if(isset($number_of_shares[$i])){
						                        if($number_of_shares[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(is_numeric($number_of_shares[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE; 
						                            }
						                        }
						                    }
						                    //price of shares
						                    if(isset($price_per_shares[$i])){
						                        if($price_per_shares[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(is_numeric($price_per_shares[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE; 
						                            }
						                        }
						                    }

						                    //stock names
						                    if(isset($money_market_investment_names[$i])){
						                        if($money_market_investment_names[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                                //do nothing for now
						                            
						                        }
						                    }
						                    //Members
						                    if(isset($members[$i])){
						                        if($members[$i]==''){
						                            $entries_are_valid = FALSE;
						                        }else{
						                            if(is_numeric($members[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE;
						                            }
						                        }
						                    }
						                    //Contributions
						                    if(isset($contributions[$i])){
						                        if($contributions[$i]==''){								                            $entries_are_valid = FALSE;
						                        }else{
						                            if(is_numeric($contributions[$i])){
						                                //do nothing for now
						                            }else{
						                                $entries_are_valid = FALSE;
						                            }
						                        }
						                    }
						                endfor;
						                for($i=0;$i<=$count;$i++):
						                    if(isset($withdrawal_fors[$i])){
						                        if($withdrawal_fors[$i]==9){
						                            if("bank-".$account_id == $to_account_ids[$i]){
						                                $error_message .= "On funds transfer select a recipient account different to the account withdrawn from.>";
						                                $entries_are_valid = FALSE;
						                            }
						                        }
						                    }
						                endfor;
							        }else{
							           $entries_are_valid = FALSE; 
							        }
							        if($loan_selections_are_valid){

							        }else{
							        	$error_message.=". You cannot match the same loan twice. Remove one and try again";
							            $entries_are_valid = FALSE; 
							        }

							        if($amount_reconciled==$transaction_alert->amount){

							        }else{
							        	$error_message.=". The amount reconciled ie .".number_to_currency($amount_reconciled)." has to be equal to the amount withdrawn which is ".number_to_currency($transaction_alert->amount);
							            $entries_are_valid = FALSE; 
							        }
							        if($loan_match_error){
							        	$error_message.=". Cannot match loan, the loan has to match the member, the bank account and amount withdrawn";
							        }
							        if($entries_are_valid){
							            //make entries
							            $status = TRUE;
							            $count = count($withdrawal_fors)-1;
							            $account_id = 'bank-'.$account_id;
							            for($i=0;$i<=$count;$i++):
							                if($withdrawal_fors[$i]==1){
							                    //expense withdrawal
							                    if($this->transactions->record_expense_withdrawal($this->group->id,$transaction_alert->transaction_date,$expense_categories[$i],1,$account_id,$descriptions[$i],valid_currency($amounts[$i]),$transaction_alert->id)){
							                        //update transaction alerts
							                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }
							                }else if($withdrawal_fors[$i]==2){
							                    //asset purchase payment
							                    if($this->transactions->record_asset_purchase_payment($this->group->id,$transaction_alert->transaction_date,$asset_ids[$i],$account_id,1,$descriptions[$i],valid_currency($amounts[$i]),$transaction_alert->id)){
							                        //update transaction alerts
							                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }
							                }else if($withdrawal_fors[$i]==3){
							                    //loan disbursement match
							                    if($this->transactions->match_loan_disbursement_to_transaction_alert($this->group->id,$loans[$i],$transaction_alert->id)){
							                        //update transaction alerts
							                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }
							                }else if($withdrawal_fors[$i]==4){
							                    //stock purchase
							                    if($this->transactions->record_stock_purchase($this->group->id,$transaction_alert->transaction_date,$stock_names[$i],$number_of_shares[$i],$account_id,$price_per_shares[$i],$transaction_alert->id)){
							                        //update transaction alerts
							                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }
							                }else if($withdrawal_fors[$i]==5){
							                    //money market investment
							                    if($this->transactions->create_money_market_investment($this->group->id,$money_market_investment_names[$i],$transaction_alert->transaction_date,valid_currency($amounts[$i]),$account_id,$descriptions[$i],$transaction_alert->id)){
							                        //update transaction alerts
							                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }
							                }else if($withdrawal_fors[$i]==6){
							                    //money market investment
							                    if($this->transactions->top_up_money_market_investment($this->group->id,$money_market_investment_ids[$i],$transaction_alert->transaction_date,valid_currency($amounts[$i]),$account_id,$descriptions[$i],$transaction_alert->id)){
							                        //update transaction alerts
							                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }
							                }else if($withdrawal_fors[$i]==7){
							                    if($this->transactions->record_contribution_refund($this->group->id,
							                        $transaction_alert->transaction_date,
							                        $members[$i],$account_id,
							                        $contributions[$i],1,
							                        '',
							                        valid_currency($amounts[$i]),1,$transaction_alert->id)){
							                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){
							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }
							                }else if($withdrawal_fors[$i]==8){
							                    $description = $descriptions[$i]?:'Bank Loan Repayment';
							                    if($this->loan->bank_loan_repayment($bank_loan_ids[$i],valid_currency($amounts[$i]),$transaction_alert->transaction_date,$this->group->id,$account_id,1,$description,$user->id,$transaction_alert->id)){
							                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){
							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }
							                }else if($withdrawal_fors[$i]==9){
							                    if($this->transactions->record_account_transfer($this->group->id,$transaction_alert->transaction_date,$account_id,$to_account_ids[$i],valid_currency($amounts[$i]),$descriptions[$i],$transaction_alert->id)){
							                        //update transaction alerts
							                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){
														if($transaction_alert = $this->transaction_alerts_m->get_group_matching_deposit_transaction_alert($this->group->id,$transaction_alert->transaction_date,$account_id,$to_account_ids[$i],valid_currency($amounts[$i]))){
							                                if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

							                                }else{
							                                    return FALSE;
							                                }
							                            }else{

							                            }
							                        }else{
							                            $status = FALSE;
							                        }
							                    }else{
							                        $status = FALSE;
							                    }
							                }
							            endfor;
							            if($status){
							            	$response = array(
							            		'status' => 1,
							            		'time' => time(),
							            		'message' => 'Reconcillation successfully recorded',
							            	);
							            }else{
							            	$response = array(
							            		'status' => 0,
							            		'time' => time(),
							            		'message' => 'Something went wrong',
						            		);
							            }
							        }else{
							            $response = array(
						            		'status' => 0,
						            		'time' => time(),
						            		'message' => $error_message.'. All fields on the form are required. Kindly review your entries and try again',
					            		);
							        }
					            }
							}else{
								$response = array(
				    				'status' => 0,
				    				'time' => time(),
				    				'message' => 'Error with the transaction alert',
				    			);
							}
						}else{
							$response = array(
			    				'status' => 0,
			    				'time' => time(),
			    				'message' => 'Transaction alert error',
			    			);
						}
					}else{
						$post = array();
			            $form_errors = $this->form_validation->error_array();
						foreach ($form_errors as $key => $value) {
							$post[$key] = $value;
						}
			            $response = array(
			    				'status' => 0,
			    				'time' => time(),
			    				'message' => 'Form validation failed',
			    				'validation_errors' => $post,
			    			);
					}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
    	echo json_encode(array('response'=>$response));
	}


	function payment_request(){
		$file = file_get_contents('php://input');
    	$response = array();
    	$request = array();
    	header('Content-Type: application/json');
    	$time = time();
    	if($file){
    		$result = json_decode($file);
    		$request = $result;
    		if($result){
    			$user_id = isset($result->current_user_id)?$result->current_user_id:0;
    			$group_id = isset($result->group_id)?$result->group_id:0;
    			if($user_id&&$group_id){
    				$user = $this->ion_auth->get_user($user_id);
    				if($user){
    					$group = $this->groups_m->get($group_id);
    					if($group){
    						$member = $this->members_m->get_member_where_user_id($user_id,$group_id);
    						if($member){
    							foreach ($result as $result_key => $result_value) {
	    							$_POST[$result_key] = $result_value;
	    						}
	    						$_POST['member_id'] = $member->id;
	    						$this->form_validation->set_rules($this->payment_validation_rules);
	    						if($this->form_validation->run()){
	    							$url = "http://api.chamasoft.com/safaricom/service_pin";
	    							$paybill_account = $this->mobile_money_accounts_m->get_group_verified_mobile_money_account($this->group->id);
	    							if($paybill_account && count($paybill_account) ==1 ){

	    								$request_phone = valid_phone($this->input->post('phone'))?:$user->phone;
	    								$created_on = time();

	    								$transaction_id = $this->transaction_alerts_m->insert_online_checkout_transaction_request(array(
	    									'user_id' => $user_id,
	    									'group_id' => $group_id,
	    									'member_id' => $member->id,
	    									'phone' => $request_phone,
	    									'amount' => $this->input->post('amount'),
	    									'account_number' => $paybill_account->account_number,
	    									'account_id' => 'mobile-'.$paybill_account->id,
	    									'loan_id' => $this->input->post('loan_id'),
	    									'fine_category' => $this->input->post('fine_category'),
	    									'contribution_id' => $this->input->post('contribution_id'),
	    									'status' => 0,
	    									'created_on' => $created_on,
	    									'created_by' => $user_id,
	    								));
	    								if($transaction_id){
	    									$request_data = array(
		    									'authentication' => array(
		    										'identity' => '0763747066',
		    										'password' => '31784253',
		    									),
		    									'request' => array(
		    										'account_number' => $paybill_account->account_number,
			    									'transaction_id' => $transaction_id,
			    									'timestamp' => time(),
			    									'phone' => $request_phone,
			    									'amount' => $this->input->post('amount'),
		    									),
			    							);
			    							$json_file = json_encode($request_data);
			    							$result_curl = $this->curl->post_json($json_file,$url);
			    							$decode_result_file = json_decode($result_curl);
			    							if($decode_result_file){
			    								if($decode_result_file->response->status==1){
			    									$response = array(
									    				'status' => 1,
									    				'time' => time(),
									    				'message' => $decode_result_file->response->response,
									    			);
			    								}else{
			    									$response = array(
									    				'status' => 0,
									    				'time' => time(),
									    				'message' => $decode_result_file->response->response,
									    			);
			    								}
			    								$this->transaction_alerts_m->update_online_checkout_transaction_request(
			    									$transaction_id,
			    									array(
			    										'status' => trim($decode_result_file->response->status),
			    										'transaction_id' => $decode_result_file->response->mpesa_transaction_id,
			    										'description' => $decode_result_file->response->response,
			    									)
			    								);
			    							}else{
			    								$response = array(
								    				'status' => 0,
								    				'time' => time(),
								    				'message' => 'Service unavailable. Try again later.',
								    			);
			    							}
	    								}else{
	    									$response = array(
	    										'status' => 0,
	    										'time' => $created_on,
	    										'message' => 'System unavailable. Try again later',
	    									);
	    								}
	    							}else{
	    								$response = array(
						    				'status' => 0,
						    				'time' => time(),
						    				'message' => 'Group does not have an existing paybill account',
						    			);
	    							}
	    						}else{
	    							$post = array();
						            $form_errors = $this->form_validation->error_array();
									foreach ($form_errors as $key => $value) {
										$post[$key] = $value;
									}
						            $response = array(
						    				'status' => 0,
						    				'time' => time(),
						    				'message' => 'Form validation failed',
						    				'validation_errors' => $post,
						    			);
	    						}
    						}else{
    							$response = array(
				    				'status' => 0,
				    				'time' => time(),
				    				'message' => 'Member details not found',
				    			);
    						}
						}else{
    						$response = array(
			    				'status' => 0,
			    				'time' => time(),
			    				'message' => 'Group details not found',
			    			);
    					}
    				}else{
    					$response = array(
		    				'status' => 0,
		    				'time' => time(),
		    				'message' => 'User details not found',
		    			);
    				}
    			}else{
    				$response = array(
	    				'status' => 0,
	    				'time' => time(),
	    				'message' => 'essential values missing',
	    			);
    			}
    		}else{
    			$response = array(
    				'status' => 0,
    				'time' => time(),
    				'message' => 'Invalid file sent',
    			);
    		}
    	}else{
    		$response = array(
    				'status' => 0,
    				'time' => time(),
    				'message' => 'No file sent',
    			);
    	}

    	echo json_encode(array('response'=>$response,'request'=>$request));		
	}

}
?>