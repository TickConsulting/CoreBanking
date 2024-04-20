<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Api extends Mobile_Controller{

	protected $group_registration_rules = array(
	    array(
            'field' =>  'group_name',
            'label' =>  'Group Name',
            'rules' =>  'required|trim|callback__group_name_validation_check',
        ),
        array(
            'field' =>  'country_id',
            'label' =>  'Country Name',
            'rules' =>  'trim|numeric',
        )
    );
	protected $user_registration_rules =array(
        array(
            'field' => 'id_number',
            'label' => 'ID Number',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'phone_number',
            'label' => 'Phone Number',
            'rules' => 'required|trim|valid_phone'
        ),
        array(
            'field' => 'last_name',
            'label' => 'Last Name',
            'rules' => 'required|trim'
        ),
        array(
            'field' => 'calling_code',
            'label' => 'Calling Code',
            'rules' => 'required|trim'
        ),
        array(
            'field' => 'first_name',
            'label' => 'First Name',
            'rules' => 'required|trim'
        ),
        array(
            'field' => 'middle_name',
            'label' => 'Middle Name',
            'rules' => 'trim'
        ),
        array(
            'field' =>  'email',
            'label' =>  'Email address',
            'rules' =>  'trim|valid_email',
        ),
        array(
            'field' => 'loan_limit',
            'label' => 'Loan  Limit',
            'rules' => 'trim|required|valid_currency'
        ),
        /*array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'required'
        ),*/
    );
	protected $validation_rules_user_details = array(
        array(
            'field' => 'loan_limit',
            'label' => 'Loan Limit',
            'rules' => 'required|valid_currency'
        ),
        array(
            'field' => 'id_number',
            'label' => 'ID number',
            'rules' => 'required|trim'
        ),
    );
    protected $validation_rules_check_limit = array(
  
        array(
            'field' => 'id_number',
            'label' => 'ID number',
            'rules' => 'required|trim'
        ),
    );

	protected $signup_rules = array(
	    array(
	            'field' =>  'first_name',
	            'label' =>  'First Name',
	            'rules' =>  'required|trim|max_length[10]',
	        ),
	    array(
	            'field' =>  'last_name',
	            'label' =>  'Last Name',
	            'rules' =>  'required|trim|max_length[10]',
	        ),
	    array(
	            'field' =>  'identity',
	            'label' =>  'Phone Number / Email Address',
	            'rules' =>  'required|trim|callback__valid_identity',
	        ),
	    array(
	            'field' =>  'password',
	            'label' =>  'Password',
	            'rules' =>  'required|trim|min_length[8]',
	        ),
	    array(
	            'field' =>  'group_name',
	            'label' =>  'Group Name',
	            'rules' =>  'required|trim',
	        ),  
	    array(
	            'field' =>  'group_size',
	            'label' =>  'Group Size',
	            'rules' =>  'required|trim|numeric|min_length[1]|max_length[10000]',
	        ),
    );

	protected $register_member_validation_rules = array(
	 		array(
	 			'field' => 'first_name',
	 			'label' => 'First Name',
	 			'rules' => 'required|trim|max_length[10]',
	 		),
	 		array(
	 			'field' => 'last_name',
	 			'label' => 'Last Name',
	 			'rules' => 'required|trim|max_length[10]',
	 		),
	 		array(
	 			'field' => 'phone',
	 			'label' => 'phone',
	 			'rules' => 'required|trim|valid_phone',
	 		),
	 		array(
	 			'field' => 'phone',
	 			'label' => 'phone',
	 			'rules' => 'trim|valid_email',
	 		),
	 		array(
	 			'field' => 'group_role_id',
	 			'label' => 'Group Role',
	 			'rules' => 'trim|numeric',
	 		),
	 		array(
	 			'field' => 'mobile_local_id',
	 			'label' => 'Role',
	 			'rules' => 'trim|numeric|required',
	 		),
	 		array(
	 			'field' => 'user_id',
	 			'label' => 'user ID',
	 			'rules' => 'trim|numeric|required',
	 		),
	 		array(
	 			'field' => 'group_id',
	 			'label' => 'Group ID',
	 			'rules' => 'trim|numeric|required',
	 		),
	 	);

	protected $user_registration_rules = array(
		array(
	 			'field' => 'first_name',
	 			'label' => 'First Name',
	 			'rules' => 'required|trim|alpha|max_length[10]',
	 		),
 		array(
 			'field' => 'last_name',
 			'label' => 'Last Name',
 			'rules' => 'required|trim|alpha|max_length[10]',
 		),
    	array(
	            'field' =>  'identity',
	            'label' =>  'User identity',
	            'rules' =>  'required|trim|callback__valid_identity',
	        ),
	    array(
	            'field' =>  'document_type',
	            'label' =>  'Document Type',
	            'rules' =>  'trim|numeric',
	        ),
	    array(
	            'field' =>  'document_number',
	            'label' =>  'Document Number',
	            'rules' =>  'trim',
	        ),
	    array(
	            'field' =>  'password',
	            'label' =>  'Password',
	            'rules' =>  'required|min_length[8]',
	        ),
	    array(
	            'field' =>  'confirm_password',
	            'label' =>  'Password confirmation',
	            'rules' =>  'required|min_length[8]|matches[password]',
	        ),
	    array(
	            'field' =>  'terms_and_conditions',
	            'label' =>  'Terms and Conditions',
	            'rules' =>  'trim|numeric',
	        )
    );

    function _group_name_validation_check(){
	 	$group_name = $this->input->post('group_name');
	 	if(is_character_allowed($group_name)){
	 		if(in_array(strtolower($group_name), $this->investment_groups->reserved_words)){
	 			$this->form_validation->set_message('_group_name_validation_check','Group name not allowed');
	 			return FALSE;
	 		}else{
	 			return TRUE;
	 		}
        }else{
            $this->form_validation->set_message('_group_name_validation_check','You have entered illegal characters in the Group Name field, avoid using the following: % $ - - & * ? < > ');
            return FALSE;
        }
	}
	 
 
	function __construct(){
		parent::__construct();
		$this->load->model('members/members_m');
		$this->load->model('users/users_m');
		$this->load->library('investment_groups');
		$this->load->library('group_members');
		$this->load->library('transactions');
		$this->load->model('countries/countries_m');
		$this->load->library('contribution_invoices');
		$this->load->model('contributions/contributions_m');
		$this->load->library('setup_tasks_tracker');
		$this->load->model('asset_categories/asset_categories_m');
		$this->load->model('loan_types/loan_types_m');
	}

	// Remap the 404 error functions
   public function _remap($method, $params = array()){
       if(method_exists($this, $method)){
           return call_user_func_array(array($this, $method), $params);
       }
       $this->output->set_status_header('404');
       header('Content-Type: application/json');
       $file = file_get_contents('php://input')?(array)json_decode(file_get_contents('php://input')):array();
       $request = $_REQUEST+$file;
       echo encrypt_json_encode(
       	array(
       		'response' => array(
		       		'status'	=>	0,
		       		'error'		=>	'404 Method Not Found for URI: '.$this->uri->uri_string(),
       			),
       	)

       	);
	}
	function get_member_user_groups(){
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
            		if($this->member->active){
            			$user_groups = $this->_get_user_groups($this->user->id);
				        $response = array(
				        	'status' => 1,
                    		'time' => time(),
				            'user_groups' =>$user_groups
				        );            			
            		}else{
            			$response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
            		}
            	}else{
            		$response = array(
                        'status' => 0,
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


	function get_member_dashboard(){
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
            		if($this->member->active){
            			if($this->group->statements_reconciled){
			            }else{
			                $group_ids[] = $this->group->id;
			                $member_ids = array();
			                $group_member_options = $this->members_m->get_group_member_options($this->group->id);
			                foreach($group_member_options as $member_id => $name):
			                    $member_ids[] = $member_id;
			                endforeach;
			                if($this->transactions->update_group_member_contribution_statement_balances($group_ids,$member_ids)){
			                    $input = array(
			                        'statements_reconciled' => 1,
			                        'modified_on' => time(),
			                        'modified_by' => $this->user->id
			                    );
			                    $this->groups_m->update($this->group->id,$input);
			                }
			            }
			            if($this->group->fine_statements_reconciled){

			            }else{
			                $group_ids[] = $this->group->id;
			                $member_ids = array();
			                $group_member_options = $this->members_m->get_group_member_options($this->group->id);
			                foreach($group_member_options as $member_id => $name):
			                    $member_ids[] = $member_id;
			                endforeach;
			                if($this->transactions->update_group_member_fine_statement_balances($group_ids,$member_ids)){
			                    $input = array(
			                        'fine_statements_reconciled' => 1,
			                        'modified_on' => time(),
			                        'modified_by' => $this->user->id
			                    );
			                    $this->groups_m->update($this->group->id,$input);
			                }
			            }
            			$group_roles = $this->group_roles_m->get_group_role_options($this->group->id);
                    	if($this->version_code > 57){
                    		$group_recent_transactions = array();
                    		$member_recent_transactions = array();
                    		$group_data['total_group_contributions'] = 0;
                    		$group_data['total_group_fines'] = 0;
                    		$group_data['total_group_loan_balances'] = 0;
                    		$group_data['total_group_expenses'] = 0;

                    		$member_data['total_member_contribution_arrears'] = 0;
                    		$member_data['total_member_fine_arrears'] = 0;
                    		$member_data['total_member_contribution_arrears'] = 0;
                    		$member_data['total_member_contributions'] = 0;
                    		$member_data['total_member_loan_balances'] = 0;
                    		$member_data['total_member_fines'] = 0;
                    	}else{
                    		$member_recent_transactions = $this->_get_member_recent_transactions($this->group->id, $this->member->id);
	                    	if($this->group->enable_member_information_privacy && !($this->member->is_admin || $this->member->group_role_id)){
	                    		$group_member_options = array(
	                    			$this->member->id => ($this->user->first_name.' '.$this->user->last_name)
	                    		);
	                    		$group_recent_transactions = $this->_get_group_latest_transactions($this->group->id,"DESC",$group_member_options,'',$this->member->id);
	                    	}else{
	                    		$group_member_options = $this->members_m->get_group_member_options($this->group->id);
	                    		$group_recent_transactions = $this->_get_group_latest_transactions($this->group->id,"DESC",$group_member_options);
	                    	}
	                    	$group_data = $this->_get_group_dashboard_data($this->group->id);
                    		$member_data = $this->_get_member_group_dashboard_data($this->group->id,$this->member->id,$this->group->disable_arrears);
                    	}
                    	$group_datails = array(
                    		'total_contributions' => $group_data['total_group_contributions'],
                    		'total_fines' => $group_data['total_group_fines'],
                    		'total_loan_balances' => $group_data['total_group_loan_balances'],
                    		'total_expense_payments' => $group_data['total_group_expenses'],
                    		'bank_balances' => $this->_get_group_account_balance($this->group->id),
                    		'cash_balances' => $this->_get_group_cash_account_balance($this->group->id),
                    		'account_balances' => $this->_get_individual_account_balances($this->group->id),
                    		'recent_transactions' => $group_recent_transactions,
                    	);
                    	$next_contribution_date = $this->_get_group_contribution_date($this->group->id);
                    	$contribution_arrears = $member_data['total_member_contribution_arrears'];
		                $fine_arrears = $member_data['total_member_fine_arrears'];
                    	if($fine_arrears>0 && $contribution_arrears>0){
                    		$total_arrears = ($contribution_arrears+$fine_arrears)?:0;
                    	}elseif ($fine_arrears>0) {
                    		$total_arrears = ($fine_arrears)?:0;
                    	}elseif ($contribution_arrears>0) {
                    		$total_arrears = ($contribution_arrears)?:0;
                    	}elseif ($contribution_arrears==0 && $fine_arrears<0) {
                    		$total_arrears = $fine_arrears;
                    	}elseif ($fine_arrears==0 && $contribution_arrears<0) {
                    		$total_arrears = $contribution_arrears;
                    	}else{
                    		$total_arrears = 0;
                    	}
                    	$member_details = array(
                    		'total_contributions' => $member_data['total_member_contributions'],
                    		'total_fines' => $member_data['total_member_fines'],
                    		'total_loan_balances' => $member_data['total_member_loan_balances'],
                    		'contribution_arrears' => $contribution_arrears,
                    		'fine_arrears' => $fine_arrears,
                    		'total_arrears' => $total_arrears,
                    		'next_contribution_date' => timestamp_to_mobile_shorttime($next_contribution_date),
                    		'contribution_date_days_left' => daysAgo($next_contribution_date),
                    		'recent_transactions' => $member_recent_transactions,
                    		"role" => isset($group_roles[$this->member->group_role_id])?$group_roles[$this->member->group_role_id]:($this->member->is_admin?'Admin':'Member'),
                    		"member_id" => $this->member->id,
                   		);
                   		$has_partner_bank = $this->bank_accounts_m->check_if_group_has_partner_bank_account()?1:0;
                   		$group_default_bank_account = $this->bank_accounts_m->get_group_default_bank_account($this->group->id);
                   		$has_partner_bank = $has_partner_bank?:($group_default_bank_account?1:0);
                   		$unreconciled_deposits_count = 0;
						$unreconciled_withdrawals_count = 0;
                   		if($has_partner_bank || $group_default_bank_account){
                   			$group_partner_bank_account_number_list = $this->bank_accounts_m->get_group_verified_bank_account_number_list($this->group->id);
                   			$group_partner_mobile_money_account_number_list = $this->mobile_money_accounts_m->get_group_verified_mobile_money_account_number_list($this->group->id);
                   			$unreconciled_deposits_count = $this->transaction_alerts_m->count_group_unreconciled_deposits($group_partner_bank_account_number_list, $group_partner_mobile_money_account_number_list);
							$unreconciled_withdrawals_count = $this->transaction_alerts_m->count_group_unreconciled_withdrawals($group_partner_bank_account_number_list, $group_partner_mobile_money_account_number_list);
                   		}
                    	$response = array(
                    		'status' => 1,
                    		'time' => time(),
                    		'group_details' => $group_datails,
                    		'member_details' => $member_details,
                    		'notification_count' => $this->notifications_m->count_unread_member_notifications($this->group->id,$this->member->id),
                    		'has_partner_bank' => $has_partner_bank,
                    		'unreconciled_deposits_count' => $unreconciled_deposits_count,
                    		'unreconciled_withdrawals_count' => $unreconciled_withdrawals_count,
                    	);
            		}else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
                	}
				}else{
                    $response = array(
                        'status' => 0,
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
        echo encrypt_json_encode(array('response'=>$response));
	}

	function get_member_and_group_chart_data(){
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
            		if($this->member->active){
            			$group_chart_data = $this->_get_group_chart_data($this->group->id,$this->member->id);
            			if($this->group->enable_member_information_privacy && !($this->member->is_admin || $this->member->group_role_id)){
                    		$group_member_options = array(
                    			$this->member->id => ($this->user->first_name.' '.$this->user->last_name)
                    		);
                    		$group_transactions = $group_chart_data['group_transactions'];
                    	}else{
                    		$group_member_options = $this->members_m->get_group_member_options($this->group->id);
                    		$group_transactions = $group_chart_data['group_transactions'];
                    	}
                    	$response = array(
                    		'status' => 1,
                    		'time' => time(),
                    		'member_records' => $group_chart_data['member_records'],
                    		'deposit_withdrawal_records' => $group_chart_data['deposit_withdrawal_records'],
                    		'expenses_records' => $group_chart_data['expenses_records'],
                    		'group_transactions' => $group_transactions,
                    		'member_transactions' => $group_chart_data['member_transactions'],
                    	);
            		}else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
                	}
				}else{
                    $response = array(
                        'status' => 0,
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
        echo encrypt_json_encode(array('response'=>$response));
	}

	function get_ios_member_and_group_chart_data(){		
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
            		if($this->member->active){
            			$group_chart_data = $this->_get_group_chart_data($this->group->id,$this->member->id);
            			if($this->group->enable_member_information_privacy && !($this->member->is_admin || $this->member->group_role_id)){
                    		$group_member_options = array(
                    			$this->member->id => ($this->user->first_name.' '.$this->user->last_name)
                    		);
                    		$group_transactions = $group_chart_data['group_transactions'];
                    	}else{
                    		$group_member_options = $this->members_m->get_group_member_options($this->group->id);
                    		$group_transactions = $group_chart_data['group_transactions'];
                    	}

                    	$deposits_array = array();
                    	$withdrawals_array = array();
                    	$expenses_array = array();                    	
                    	$member_transactions_array = array();
                    	if($group_chart_data['member_transactions']){
                    		foreach ($group_chart_data['member_transactions'] as $key => $deposit) :
                    			$member_transactions_array[] = array(
                    				'month'=>$key,
                    				'amount'=>$deposit,
                    			);                    			
                    		endforeach;	
                    	}
                    	if($group_transactions['deposits']){
                    		foreach ($group_transactions['deposits'] as $key => $deposit) :
                    			$deposits_array[] = array(
                    				'month'=>$key,
                    				'amount'=>$deposit,
                    			);                    			
                    		endforeach;
                    	}
                    	if($group_transactions['withdrawals']){
                    		foreach ($group_transactions['withdrawals'] as $key => $withdrawal) :
                    			$withdrawals_array[] = array(
                    				'month'=>$key,
                    				'amount'=>$withdrawal,
                    			);                    			
                    		endforeach;
                    	}
                    	if($group_transactions['expenses']){
							foreach ($group_transactions['expenses'] as $key => $expense) :
								$expense = (object)$expense;
                    			$expenses_array[] = array(
                    				'expense_name'=>$expense->expense_name,
                    				'amount'=>$expense->amount,
                    			);                    			
                    		endforeach;
                    	}
                    	$transactions_array = array(
                    		'deposits'=>$deposits_array,
							'withdrawals'=>$withdrawals_array,
							'expenses'=>$expenses_array,
                    	);
                    	$response = array(
                    		'status' => 1,
                    		'time' => time(),
                    		'member_records' => $group_chart_data['member_records'],
                    		'deposit_withdrawal_records' => $group_chart_data['deposit_withdrawal_records'],
                    		'expenses_records' => $group_chart_data['expenses_records'],
                    		'group_transactions' => $transactions_array,
                    		'member_transactions' => $member_transactions_array,
                    	);
            		}else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
                	}
				}else{
                    $response = array(
                        'status' => 0,
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
        echo encrypt_json_encode(array('response'=>$response));
	}

	function get_group_more_dashboard_data(){
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
            		if($this->member->active){
            			$group_data = $this->_get_group_dashboard_data($this->group->id);
                    	$member_data = $this->_get_member_group_dashboard_data($this->group->id,$this->member->id,$this->group->disable_arrears);
            			$group_datails = array(
                    		'total_contributions' => $group_data['total_group_contributions'],
                    		'total_fines' => $group_data['total_group_fines'],
                    		'total_loan_balances' => $group_data['total_group_loan_balances'],
                    		'total_expense_payments' => $group_data['total_group_expenses'],
                    	);
                    	$contribution_arrears = $member_data['total_member_contribution_arrears'];
		                $fine_arrears = $member_data['total_member_fine_arrears'];
                    	if($fine_arrears>0 && $contribution_arrears>0){
                    		$total_arrears = ($contribution_arrears+$fine_arrears)?:0;
                    	}elseif ($fine_arrears>0) {
                    		$total_arrears = ($fine_arrears)?:0;
                    	}elseif ($contribution_arrears>0) {
                    		$total_arrears = ($contribution_arrears)?:0;
                    	}elseif ($contribution_arrears==0 && $fine_arrears<0) {
                    		$total_arrears = $fine_arrears;
                    	}elseif ($fine_arrears==0 && $contribution_arrears<0) {
                    		$total_arrears = $contribution_arrears;
                    	}else{
                    		$total_arrears = 0;
                    	}
                    	$member_details = array(
                    		'total_contributions' => $member_data['total_member_contributions'],
                    		'total_fines' => $member_data['total_member_fines'],
                    		'total_loan_balances' => $member_data['total_member_loan_balances'],
                    		'contribution_arrears' => $contribution_arrears,
                    		'fine_arrears' => $fine_arrears,
                    		'total_arrears' => $total_arrears,
                    		"member_id" => $this->member->id,
                   		);

                   		$response = array(
                    		'status' => 1,
                    		'time' => time(),
                    		'group_details' => $group_datails,
                    		'member_details' => $member_details,
                    	);
            		}else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
                	}
				}else{
                    $response = array(
                        'status' => 0,
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
        echo encrypt_json_encode(array('response'=>$response));
	}

	function get_member_dashboard_data(){
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
            		if($this->member->active){
            			$group_roles = $this->group_roles_m->get_group_role_options($this->group->id);
            			$member_recent_transactions = $this->_get_member_recent_transactions($this->group->id, $this->member->id);
            			$member_contribution_summary = $this->_get_member_contribution_summary($this->group->id, $this->member->id);
                		$member_data['total_member_contribution_arrears'] = 0;
                		$member_data['total_member_fine_arrears'] = 0;
                		$member_data['total_member_contribution_arrears'] = 0;
                		$member_data['total_member_contributions'] = 0;
                		$member_data['total_member_loan_balances'] = 0;
                		$member_data['total_member_fines'] = 0;
                		$member_data = $this->_get_member_group_dashboard_data($this->group->id,$this->member->id,$this->group->disable_arrears);
                    	$next_contribution_date = $this->_get_group_contribution_date($this->group->id);
                    	$contribution_arrears = $member_data['total_member_contribution_arrears'];
		                $fine_arrears = $member_data['total_member_fine_arrears'];
                    	if($fine_arrears>0 && $contribution_arrears>0){
                    		$total_arrears = ($contribution_arrears+$fine_arrears)?:0;
                    	}elseif ($fine_arrears>0) {
                    		$total_arrears = ($fine_arrears)?:0;
                    	}elseif ($contribution_arrears>0) {
                    		$total_arrears = ($contribution_arrears)?:0;
                    	}elseif ($contribution_arrears==0 && $fine_arrears<0) {
                    		$total_arrears = $fine_arrears;
                    	}elseif ($fine_arrears==0 && $contribution_arrears<0) {
                    		$total_arrears = $contribution_arrears;
                    	}else{
                    		$total_arrears = 0;
                    	}
                    	$member_details = array(
                    		'total_contributions' => $member_data['total_member_contributions'],
                    		'total_fines' => $member_data['total_member_fines'],
                    		'total_loan_balances' => $member_data['total_member_loan_balances'],
                    		'contribution_arrears' => $contribution_arrears,
                    		'fine_arrears' => $fine_arrears,
                    		'total_arrears' => $total_arrears,
                    		'next_contribution_date' => timestamp_to_mobile_shorttime($next_contribution_date),
                    		'contribution_date_days_left' => daysAgo($next_contribution_date),
                    		'recent_transactions' => $member_recent_transactions,
                    		'member_contribution_summary' => $member_contribution_summary,
                   		);
                   		$has_partner_bank = $this->bank_accounts_m->check_if_group_has_partner_bank_account()?1:0;
                   		$group_default_bank_account = $this->bank_accounts_m->get_group_default_bank_account($this->group->id);
                   		$has_partner_bank = $has_partner_bank?:($group_default_bank_account?1:0);
                   		$unreconciled_deposits_count = 0;
						$unreconciled_withdrawals_count = 0;
                   		if($has_partner_bank || $group_default_bank_account){
                   			$group_partner_bank_account_number_list = $this->bank_accounts_m->get_group_verified_bank_account_number_list($this->group->id);
                   			$group_partner_mobile_money_account_number_list = $this->mobile_money_accounts_m->get_group_verified_mobile_money_account_number_list($this->group->id);
                   			$unreconciled_deposits_count = $this->transaction_alerts_m->count_group_unreconciled_deposits($group_partner_bank_account_number_list, $group_partner_mobile_money_account_number_list);
							$unreconciled_withdrawals_count = $this->transaction_alerts_m->count_group_unreconciled_withdrawals($group_partner_bank_account_number_list, $group_partner_mobile_money_account_number_list);
                   		}
                    	$response = array(
                    		'status' => 1,
                    		'time' => time(),
                    		"role" => isset($group_roles[$this->member->group_role_id])?$group_roles[$this->member->group_role_id]:($this->member->is_admin?'Admin':'Member'),
                    		"member_id" => $this->member->id,
                    		'member_details' => $member_details,
                    		'notification_count' => $this->notifications_m->count_unread_member_notifications($this->group->id,$this->member->id),
                    		'unreconciled_deposits_count' => $unreconciled_deposits_count,
                    		'unreconciled_withdrawals_count' => $unreconciled_withdrawals_count,
                    	);
            		}else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
                	}
				}else{
                    $response = array(
                        'status' => 0,
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
        echo encrypt_json_encode(array('response'=>$response));
	}

	function get_group_dashboard_data(){
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
            		if($this->member->active){
                		$group_data['total_group_contributions'] = 0;
                		$group_data['total_group_fines'] = 0;
                		$group_data['total_group_loan_balances'] = 0;
                		$group_data['total_group_expenses'] = 0;
                		$group_data = $this->_get_group_dashboard_data($this->group->id);
                    	$group_datails = array(
                    		'total_contributions' => $group_data['total_group_contributions'],
                    		'total_fines' => $group_data['total_group_fines'],
                    		'total_loan_balances' => $group_data['total_group_loan_balances'],
                    		'total_loaned_amount' => $group_data['total_loaned_amount'],
                    		'total_loan_repaid' => $group_data['total_loan_repaid'],
                    		'total_expense_payments' => $group_data['total_group_expenses'],
                    		'bank_balances' => $this->_get_group_account_balance($this->group->id),
                    		'cash_balances' => $this->_get_group_cash_account_balance($this->group->id),
                    		'account_balances' => $this->_get_individual_account_balances($this->group->id),
                    	);
                   		$has_partner_bank = $this->bank_accounts_m->check_if_group_has_partner_bank_account()?1:0;
                   		$group_default_bank_account = $this->bank_accounts_m->get_group_default_bank_account($this->group->id);
                   		$has_partner_bank = $has_partner_bank?:($group_default_bank_account?1:0);
                   		$unreconciled_deposits_count = 0;
						$unreconciled_withdrawals_count = 0;
                   		if($has_partner_bank || $group_default_bank_account){
                   			$group_partner_bank_account_number_list = $this->bank_accounts_m->get_group_verified_bank_account_number_list($this->group->id);
                   			$group_partner_mobile_money_account_number_list = $this->mobile_money_accounts_m->get_group_verified_mobile_money_account_number_list($this->group->id);
                   			$unreconciled_deposits_count = $this->transaction_alerts_m->count_group_unreconciled_deposits($group_partner_bank_account_number_list, $group_partner_mobile_money_account_number_list);
							$unreconciled_withdrawals_count = $this->transaction_alerts_m->count_group_unreconciled_withdrawals($group_partner_bank_account_number_list, $group_partner_mobile_money_account_number_list);
                   		}
                    	$response = array(
                    		'status' => 1,
                    		'time' => time(),
                    		'group_details' => $group_datails,
                    		'notification_count' => $this->notifications_m->count_unread_member_notifications($this->group->id,$this->member->id),
                    		'has_partner_bank' => $has_partner_bank,
                    		'unreconciled_deposits_count' => $unreconciled_deposits_count,
                    		'unreconciled_withdrawals_count' => $unreconciled_withdrawals_count,
                    	);
            		}else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
                	}
				}else{
                    $response = array(
                        'status' => 0,
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
        echo encrypt_json_encode(array('response'=>$response));
	}

	function _get_individual_account_balances($group_id=0){
		return $this->accounts_m->get_individual_groups_account_summary_balances_array($group_id)?:array();
	}

	function _get_group_account_balance($group_id = 0){
		return $this->accounts_m->get_group_total_cash_at_bank($group_id)?:0;
	}

	function _get_group_cash_account_balance($group_id = 0){
		return $this->accounts_m->get_group_total_cash_at_hand($group_id)?:0;
	}

	function _get_group_latest_transactions($group_id = 0,$order ='DESC',$group_member_options= array(),$depositor_options = array(),$member_id=0){
		$posts = $this->transaction_statements_m->get_group_transaction_statement(0,'',$group_id,10,$order,$member_id);
		$transaction_names = $this->transactions->transaction_names;
		$transactions = array();
		foreach ($posts as $post) {
			$date = time();
			$amount = 0;
			$type = '';
			if(in_array($post->transaction_type, $this->transactions->deposit_transaction_types)){
				$date = timestamp_to_mobile_shorttime($post->transaction_date);
				$amount = $post->amount;
				$type = $transaction_names[$post->transaction_type];
				if(isset($group_member_options[$post->member_id])){
					$type.=' made by '.$group_member_options[$post->member_id];
				}elseif(isset($depositor_options[$post->depositor_id])){
					$type.=' made by '.$depositor_options[$post->depositor_id];
				}
			}elseif(in_array($post->transaction_type,$this->transactions->withdrawal_transaction_types)){
				$date = timestamp_to_mobile_shorttime($post->transaction_date);
				$amount = $post->amount;
				$type = $transaction_names[$post->transaction_type];
			}
			$transactions[] = array(
				'type' => $type,
				'date' => $date,
				'amount' => $amount,
			);
		}
		return $transactions;
	}

	function _get_group_contribution_date($group_id= 0){
		return $this->contributions_m->get_group_contribution_date($group_id);
	}

	function _get_member_contribution_summary($group_id = 0, $member_id = 0){
		$result = array();
		$open_contributions = $this->contributions_m->get_group_open_contribution_options($group_id,TRUE);
		if($open_contributions){
			$total_contributions_paid_per_contribution_array = $this->statements_m->get_group_member_total_contributions_paid_per_contribution_array($group_id,$member_id,array_keys($open_contributions),true);
			arsort($total_contributions_paid_per_contribution_array);
			foreach ($total_contributions_paid_per_contribution_array as $contribution_id => $amounts) {
				if(array_key_exists($contribution_id, $open_contributions)){
					$result[] = array(
						'name' => isset($open_contributions[$contribution_id])?$open_contributions[$contribution_id]:'Unknown',
						'paid' => $amounts['paid'],
						'balance' => $amounts['balance'],
					);
				}	
			}
		}
		return $result;	
	}


	function _get_member_recent_transactions($group_id = 0, $member_id = 0, $order ='DESC'){
		$posts = $this->deposits_m->get_group_deposits($group_id,array("member_id" =>array($member_id)),'','',7);
		$deposit_transaction_names = $this->transactions->deposit_transaction_names;
		$deposit_method_options = $this->transactions->deposit_method_options;
		$contribution_options = $this->contributions_m->get_group_contribution_options();
        $fine_category_options = $this->fine_categories_m->get_group_options(FALSE);
		//print_r($posts);die;
        $transactions = array();
		foreach ($posts as $post) {
			if($post->contribution_id){
				$description = $contribution_options[$post->contribution_id];
			}else if($post->fine_category_id){
	            $description = $fine_category_options[$post->fine_category_id];
	        }else{
	        	$description = $post->description;
	        }
			$transactions[] = array(
				'type' => $deposit_transaction_names[$post->type],
				'date' => timestamp_to_mobile_report_time($post->deposit_date),
				'amount' => $post->amount,
				'payment_method' => $deposit_method_options[$post->deposit_method],
				"description" => $description,
			);
		}
		return $transactions;
	}

	function generate_pin(){
		foreach ($this->request as $key => $value) {
			if(preg_match('/phone/', $key)){
				$_POST[$key] = valid_phone($value);
			}else{
				$_POST[$key] = $value;
			}
		}
		$client_secret=$this->input->post('client_secret');
		$client_id=$this->input->post('client_id');
		if($client_secret=="NTMyMjVCODMtMUVGMi00QTY2LTk1N0QtNDI2NzhGM0M" && $client_id=="E44EF1C7427" ){
			if($identity = "254748974489"){
				$email = '';
				$phone = '';
				if(valid_email($identity)){
					$email = $identity;
				}else{
					$phone = $identity;
				}
				if(valid_phone($identity) || valid_email($identity)){
					if($identity == 'innovations@chamasoft.com'){
						$this->beta = FALSE;
					}else{
						if($identity == valid_phone('254797181989')){
							$this->beta = FALSE;
						}
					}
					
					if($auth = $this->ion_auth->generate_user_pin($identity,$this->beta)){
	
								
								$this->user = $this->users_m->get_user_by_identity($identity);
								if($this->user){
									$auth = $this->users_m->get_user_authentication_by_identity($identity,$phone,$email);
									if($token = $this->_generate_access_token($auth->id,$this->user->id)){
										$user = array(
											'first_name' => strtoupper($this->user->first_name),
											'last_name' => strtoupper($this->user->last_name),
											'phone' => $this->user->phone,
											'id' => $this->user->id,
											'avatar' => is_file('./uploads/groups/'.$this->user->avatar)?$this->user->avatar:null,
											'email' => $this->user->email,
										);
										$response = array(
											'status' => 1,
											'message' => 'Successfully logged in',
											'time' => time(),
											'access_token' => $token->access_token,
											'user' => $user,
											'user_exists' => 1,
											'unique_code' => $auth->unique_code,
											'user_is_validated' => ($this->user->is_validated)?:0,
										);
										$group_data = $this->_get_checkin_data((object)$user);
										// $response = array_merge($response,$group_data);
									}else{
										$response = array(
											'status' => 9,
											'message' => 'Token generation failed, login to proceed',
											'time' => time(),
											'access_token' => '',
											'expirer_in' => '',
										);
									}	    		
								}else{
									$response = array(
										'status' => 1,
										'message' => 'Credentials not found',
										 
									);
								}
							
					
					}else{
						$response = array(
							'status' => 0,
							'message' => 'OTP Failed',
						);
					}
				}else{
					$response = array(
						'status' => 0,
						'message' => 'Invalid phone number or email address',
					);
				}
			}else{
				$response = array(
					'status' => 0,
					'message' => 'Phone field empty',
				);
			}
		}
		else{
			$response = array(
				'status' => 0,
				'message' => 'Invalid client secret or client ID',
			);
		}
		
		
    	echo encrypt_json_encode(array('response'=>$response));
	}

	function resend_pin(){
		foreach ($this->request as $key => $value) {
			if(preg_match('/phone/', $key)){
				$_POST[$key] = valid_phone($value);
			}else{
				$_POST[$key] = $value;
			}
		}
		$identity = $this->input->post('identity');
		$email = '';
		$phone = '';
		if(valid_email($identity)){
			$email = $identity;
		}else{
			$phone = $identity;
		}
		if(valid_phone($identity) || valid_email($identity)){
			if($identity == 'innovations@chamasoft.com'){
				$this->beta = TRUE;
			}else{
				if($identity == valid_phone('254797181989')){
					$this->beta = TRUE;
				}
			}
			$auth = $this->users_m->get_user_authentication_by_identity($identity,$phone,$email);
			if($auth){
				if(preg_match('/eazzyclub/',$_SERVER['HTTP_HOST']) || preg_match('/eazzychama/',$_SERVER['HTTP_HOST']) && $this->version_code>76){
					$this->user = $this->users_m->get_user_by_identity($identity);
					if($this->user){
						$auth = $this->users_m->get_user_authentication_by_identity($identity,$phone,$email);
						if($token = $this->_generate_access_token($auth->id,$this->user->id)){
    						$user = array(
    							'first_name' => strtoupper($this->user->first_name),
    							'last_name' => strtoupper($this->user->last_name),
    							'phone' => $this->user->phone,
    							'id' => $this->user->id,
    							'avatar' => is_file('./uploads/groups/'.$this->user->avatar)?$this->user->avatar:null,
    							'email' => $this->user->email,
    						);
    						$response = array(
    							'status' => 1,
			    				'message' => 'Successfully logged in',
			    				'time' => time(),
			    				'access_token' => $token->access_token,
			    				'user' => $user,
			    				'user_exists' => 1,
			    				'unique_code' => $auth->unique_code,
			    				'user_is_validated' => ($this->user->is_validated)?:0,
    						);
    					}else{
    						$response = array(
			    				'status' => 9,
			    				'message' => 'Token generation failed, login to proceed',
			    				'time' => time(),
			    				'access_token' => '',
			    				'expirer_in' => '',
			    			);
    					}	    		
					}else{
						$response = array(
			    			'status' => 1,
			    			'message' => 'Pin successfully verified',
			    			'user' => $this->user,
			    			'unique_code' => $auth->unique_code,
			    			'user_exists' => 0,
			    			'user_is_validated' => 0
			    		);
					}
				}else{
					if($this->messaging->send_user_otp($auth)){
	    				$response = array(
			    			'status' => 1,
			    			'message' => 'OTP sent',
			    		);
	    			}else{
	    				$response = array(
			    			'status' => 0,
			    			'message' => 'Error occured sending pin. Try again later',
			    		);
	    			}
	    		}
			}else{
				$response = array(
	    			'status' => 0,
	    			'message' => 'Generate new pin',
	    		);
			}
		}else{
			$response = array(
    			'status' => 0,
    			'message' => 'Invalid phone number or email address',
    		);
		}
    	echo encrypt_json_encode(array('response'=>$response));
	}

	function verify_pin(){
		foreach ($this->request as $key => $value) {
			if(preg_match('/phone/', $key)){
				$_POST[$key] = valid_phone($value);
			}else{
				$_POST[$key] = $value;
			}
		}
		$identity = $this->input->post('identity');
		$terms_and_conditions = 1;
		$pin = $this->input->post('pin');
		if($pin&&$identity){
			$email = '';
			$phone = '';
			if(valid_email($identity)){
				$email = $identity;
			}else{
				$phone = $identity;
			}
			if(valid_phone($identity) || valid_email($identity)){
				$auth = $this->users_m->get_user_authentication_by_identity($identity,$phone,$email);
				if($auth){
					if($auth->pin == $pin){
						// $this->messaging->notify_otp($identity,1);
						if($this->users_m->update_user_pin_access_token($auth->id,array('terms_and_conditions'=>$terms_and_conditions,'modified_on'=>time()))){
							$this->user = $this->users_m->get_user_by_identity($identity);
							if($this->user){
								if($token = $this->_generate_access_token($auth->id,$this->user->id)){
		    						$user = array(
		    							'first_name' => strtoupper($this->user->first_name),
		    							'last_name' => strtoupper($this->user->last_name),
		    							'phone' => $this->user->phone,
		    							'id' => $this->user->id,
		    							'avatar' => is_file('./uploads/groups/'.$this->user->avatar)?$this->user->avatar:null,
		    							'email' => $this->user->email,
		    						);
		    						$response = array(
		    							'status' => 1,
					    				'message' => 'Successfully logged in',
					    				'time' => time(),
					    				'access_token' => $token->access_token,
					    				'user' => $user,
					    				'user_exists' => 1,
					    				// 'unique_code' => $auth->unique_code,
					    				'user_is_validated' => ($this->user->is_validated)?:0,
		    						);
		    						$group_data = $this->_get_checkin_data((object)$user);
		    						$response = array_merge($response,$group_data);
		    					}else{
		    						$response = array(
					    				'status' => 9,
					    				'message' => 'Token generation failed, login to proceed',
					    				'time' => time(),
					    				'access_token' => '',
					    				'expirer_in' => '',
					    			);
		    					}	    		
							}else{
								$response = array(
					    			'status' => 1,
					    			'message' => 'Pin successfully verified',
					    			'user' => $this->user,
					    			'unique_code' => $auth->unique_code,
					    			'user_exists' => 0,
					    			'user_is_validated' => 0
					    		);
							}
						}else{
							$response = array(
				    			'status' => 0,
				    			'message' => 'An error occurred. Kindly try again',
				    			
				    		);
						}
					}else{
						// $this->messaging->notify_otp($identity,0);
						$response = array(
			    			'status' => 0,
			    			'message' => 'User verification failed. The pin entered does not match pin sent to you.',
			    			
			    		);
					}
				}else{
					$response = array(
		    			'status' => 9,
		    			'message' => 'Authentication does not exists. Try generating a new pin',
		    			
		    		);
				}
			}else{
				$response = array(
	    			'status' => 0,
	    			'message' => 'Invalid user identity. Kindly use a valid phone number or email address',
	    			
	    		);
			}
		}else{
			$response = array(
    			'status' => 0,
    			'message' => 'User identity and pin is invalid',
    			
    		);
		}
    	echo encrypt_json_encode(array('response'=>$response));
	}
	
	function login(){
    	foreach ($this->request as $key => $value) {
			if(preg_match('/phone/', $key)){
				$_POST[$key] = valid_phone($value);
			}else{
				$_POST[$key] = $value;
			}
		}
		$email = $this->input->post('email');
		$phone = $this->input->post('phone');
		$password = $this->input->post('password');
		$remember = (bool) $this->input->post('remember');
		if((valid_phone($phone) || valid_email($email))&& $password){
			$identity = valid_phone($phone)?:$email;
			$auth = $this->users_m->get_user_authentication_by_identity($identity,$phone,$email);
			if($auth){
				if($this->ion_auth->get_user_by_identity($identity)){
					if($this->ion_auth->login($identity,$password,$remember)){
    					$this->user = $this->ion_auth->get_user_by_identity($identity);
    					if($this->user){
    						if($token = $this->_generate_access_token($auth->id,$this->user->id)){
    							$user_groups = $this->_get_user_groups($this->user->id);
	    						$invited_user_groups = array();
	    						$user = array(
	    							'first_name' => strtoupper($this->user->first_name),
	    							'last_name' => strtoupper($this->user->last_name),
	    							'phone' => $this->user->phone,
	    							'id' => $this->user->id,
	    							'avatar' => is_file('./uploads/groups/'.$this->user->avatar)?$this->user->avatar:null,
	    							'email' => $this->user->email,
	    						);
	    						$response = array(
	    							'status' => 1,
				    				'message' => 'Successfully logged in',
				    				'time' => time(),
				    				'access_token' => $token->access_token,
				    				'user' => $user,
				    				'user_is_validated' => ($this->user->is_validated)?:0,
	    						);
	    						$group_data = $this->_get_checkin_data((object)$user);
	    						$response = array_merge($response,$group_data);
							}else{
								$response = array(
				    				'status' => 9,
				    				'message' => 'Token generation failed, login to proceed',
				    				'time' => time(),
				    				'access_token' => '',
				    				'expirer_in' => '',
				    			);
							}
    					}else{
    						$response = array(
			    				'status' => 4,
			    				'message' => 'Error occured getting user details',
			    				'time' => time(),
			    			);
    					}
    				}else{
    					$error = strtolower($this->ion_auth->errors());
    					if(preg_match('/incorrect login/', $error)){
    						$error = "Invalid password username combination. Use the correct password";
    					}
    					$response = array(
		    				'status' => 0,
		    				'message' => $error,
		    				'time' => time(),
		    			);
    				}
				}else{
					$response = array(
						'status' => 4,
						'message' => 'User not registered',
						'time' => time(),
					);
				}
			}else{
				$response = array(
    				'status' => 9,
    				'message' => 'Authentication failed. Generate a new pin',
    				'time' => time(),
    			);
			}
		}else{
            $response = array(
				'status' => 0,
				'message' => 'User information is invalid. Try again',
				'time' => time(),
			);
		}
    	echo encrypt_json_encode(array('response'=>$response));
	}

	function register_user(){
		foreach ($this->request as $key => $value) {
			if(preg_match('/phone/', $key)){
				$_POST[$key] = valid_phone($value);
			}else{
				$_POST[$key] = $value;
			}
		}
		$unique_code = $this->input->post('unique_code');
		$this->form_validation->set_rules($this->user_registration_rules);
		if($this->form_validation->run()){
			$first_name = $this->input->post('first_name');
			$last_name = $this->input->post('last_name');
			$identity = $this->input->post('identity');
			$document_type = $this->input->post('document_type');
			$document_number = $this->input->post('document_number');
			$password = $this->input->post('password');
			$avatar = $this->input->post('avatar');
			$terms_and_conditions = $this->input->post('terms_and_conditions');
			$exempted_names = array('customer','equity');
			if(!in_array(strtolower($first_name),$exempted_names)){
				$original_phone = '';
				$calling_code ='';
				$phone = 0;
				$email = '';
				if(valid_phone($identity)){
					$original_phone = substr($identity,-9);
					$calling_code = substr($identity,0,3);
					$phone = valid_phone($identity);
				}elseif (valid_email($identity)) {
					$email = $identity;
				}
				$auth = $this->users_m->get_user_authentication_by_identity($identity,$phone,$email);
				if($auth && ($auth->unique_code == $unique_code)){
					$this->user = $this->ion_auth->get_user_by_identity($identity);
					$update_user_profile = FALSE;
					$user_exists = FALSE;
					if($this->user){
						$user_exists = TRUE;
					}
					if($user_exists){
						$response = array(
							'status' => 0,
							'message' => 'User already exists. Proceed to next step',
							'time' => time(),
						);
					}else{
						$group_id = $this->ion_auth->get_group_by_name('member');
						if($group_id){
							$groups = array($group_id->id);
						}
						if($groups){
							$message = 'Please confirm your MPesa Identity to link to '.$this->chamasoft_settings->application_name.'.';
							if($avatar){
								$directory = './uploads/groups';
								if(!is_dir($directory)){
									mkdir($directory,0777,TRUE);
								}
								$file_name = generate_slug($first_name.$last_name.rand(99999,1000000).time()).'.jpeg';
								if(base64ToImage($avatar,$directory.'/'.$file_name)){
									if(is_file($directory.'/'.$file_name)){
									}else{
										$file_name=null;
									}
								}else{
									$file_name = null;
								}
							}else{
								$file_name = null;
							}
							$additional_data = array(
								'created_on'=>time(),
								'active'=>1,
								'ussd_pin'=>rand(1000,9999),
								'first_name'=> strtoupper($first_name),
								'last_name'=> strtoupper($last_name),
								'date_of_birth' => '',
								'gender' => '',
								'document_type' => $document_type,
								'document_number' => $document_number,
								'terms_and_conditions' => $terms_and_conditions,
								'phone' => $phone,
								'original_phone' => $original_phone,
								'calling_code' => $calling_code,
								'is_validated' => 1,
								'avatar' => $file_name,
							);
							$user_id = $this->ion_auth->register($identity,$password,'', $additional_data,$groups,TRUE);
							if($user_id){
								$this->user = $this->ion_auth->get_user($user_id);
								$this->ion_auth->update_last_login($this->user->id);
								$user = (object)array(
									'id' => $this->user->id,
									'first_name' => strtoupper($this->user->first_name),
									'last_name' => strtoupper($this->user->last_name),
									'username' => $this->user->username,
									'phone' => valid_phone($this->user->phone)?:0,
									'email' => $this->user->email,
									'avatar' => is_file('./uploads/groups/'.$this->user->avatar)?$this->user->avatar:null,
									'is_validated' => 1,
								);
								$token = $this->_generate_access_token($auth->id,$this->user->id);
								$response = array(
									'status' => 1,
									'message' => 'Registration request successful',
									'time' => time(),
									'access_token' => $token->access_token,
									'user' => $user,
								);
								$group_data = $this->_get_checkin_data($user,array());
								$response = array_merge($response,$group_data);
							}else{
								$response = array(
									'status' => 0,
									'message' => 'Error occured while adding user details',
									'time' => time(),
								);
							}
						}else{
							$response = array(
								'status' => 7,
								'message' => 'Could not get user group',
								'time' => time(),
							);
						}
					}
				}else{
					$response = array(
						'status' => 9,
						'message' => 'Authentication failed. Generate a new pin',
						'time' => time(),
					);
				}
			}else{
				$response = array(
					'status' => 9,
					'message' => 'First Name not accepted',
					'time' => time(),
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
				'message' => 'Form validation failed',
				'validation_errors' => $post,
				'time' => time(),
			);
		}
    	echo encrypt_json_encode(array('response'=>$response));
	}

	
	function validate_user(){
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
        	$user = (object)array(
				'id' => $this->user->id,
				'first_name' => strtoupper($this->user->first_name),
				'last_name' => strtoupper($this->user->last_name),
				'username' => $this->user->username,
				'phone' => valid_phone($this->user->phone)?:0,
				'email' => $this->user->email,
				'avatar' => is_file('./uploads/groups/'.$this->user->avatar)?$this->user->avatar:null,
			);
			$request_file = array(
				'user' => $user,
				'type' => 1,
			);

			if($this->user->is_validated==1){
				$response = array(
					'status' => 1,
					'is_user_validated' => 1,
					'message' => 'User already validated',
					'time' => time(),
				);
			}else{
				$message = 'Please confirm your MPesa Identity to link to '.$this->chamasoft_settings->application_name.'.';
	            $feedback = $this->transactions->get_user_identity_details($this->user->phone,$message);
    			if($feedback){
    				$response = array(
	    				'status' => 1,
	    				'message' => '',
	    				'time' => time(),			    			
	    			);
    			}else{
    				$response = array(
	    				'status' => 0,
	    				'message' => $this->session->flashdata('error'),
	    				'time' => time(),
	    			);
    			}
			}
		}else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
	}

	function check_user_validation_status(){
		$usernames = array();
        $phones = array();
        $group_role_ids = array();
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id')?:0;
        $request_id = $this->input->post('request_id')?:'';
        if($user_id){
        	if($this->user = $this->ion_auth->get_user($user_id)){
            	$this->ion_auth->update_last_login($this->user->id);
            	if($this->user->is_validated){
            		$response = array(
            			'status' => 1,
            			'message' => 'Verification successful',
            			'is_user_validated' => 1,
            			'time' => time(),
            		);
            	}elseif($this->user->user_validation_failed){
            		if($this->user->document_validation_failed){
            			$document_validation_error = 1;
            		}else{
            			$document_validation_error = 0;
            		}
            		$response = array(
        				'status' => 1,
        				'message' => $this->user->validation_failure_reason,
        				'document_validation_error' => $document_validation_error,
        				'retry_request' => 1,
        				'time' => time(),
        			);
            	}else{
            		$response = array(
        				'status' => 1,
        				'message' => 'Retry',
        				'time' => time(),
        			);
            	}
			}else{
                $response = array(
                    'status' => 4,
                    'message' => 'Could not find user details',
                    'retry_request' => 1,
                    'time' => time(),
                );
            }
        }else{
        	$response = array(
                'status' => 0,
                'message' => 'Empty user identity',
                'retry_request' => 1,
                'time' => $time,
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
	}

	function create_group(){
		foreach ($this->request as $key => $value) {
			if(preg_match('/phone/', $key)){
				$_POST[$key] = valid_phone($value);
			}else{
				$_POST[$key] = $value;
			}

		}
		$user_id = $this->input->post('user_id')?:0;
		if($this->user = $this->ion_auth->get_user($user_id)){
			$this->ion_auth->update_last_login($this->user->id);
			$this->form_validation->set_rules($this->group_registration_rules);
			if($this->form_validation->run()){
				$group_name = $this->input->post('group_name');
				$avatar = $this->input->post('avatar');
				$country_id = $this->input->post('country_id');
				$referrer_id = 0;
	            $referrer_information = "";
	            $activate_group = TRUE;
				if($group_id = $this->investment_groups->create_group(
					$this->user->id,
					$group_name,
					1,
					$referrer_id,
					$referrer_information,
					$activate_group,'')
				){
                   	$user = (object)array(
						'id' => $this->user->id,
						'first_name' => strtoupper($this->user->first_name),
						'last_name' => strtoupper($this->user->last_name),
						'username' => $this->user->username,
						'phone' => $this->user->phone,
						'email' => $this->user->email,
						'avatar' => is_file('./uploads/groups/'.$this->user->avatar)?$this->user->avatar:null,
					);

                   	if($avatar){
                		$directory = './uploads/groups';
			            if(!is_dir($directory)){
			                mkdir($directory,0777,TRUE);
			            }
			            $file_name = generate_slug($group_name.rand(99999,1000000).time()).'.jpeg';
			            if(base64ToImage($avatar,$directory.'/'.$file_name)){
			            	if(is_file($directory.'/'.$file_name)){
			            	}else{
			            		$file_name=null;
			            	}
			            }else{
			            	$file_name = null;
			            }
                	}else{
                		$file_name = null;
                	}
                	if($country_id){
                		$this->groups_m->update($group_id,array(
                			'country_id' => $country_id,
                			'avatar' => $file_name,
                		));
                	}
                   	$this->group = $this->groups_m->get($group_id);
                   	$this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id);
                   	$group_roles = $this->group_roles_m->get_group_role_options($this->group->id);
                   	$registered_group = array(
						'id' => $group_id,
						'name' => $this->group->name,
						'slug' => $this->group->slug,
						'account_number' => $this->group->account_number,
						'country_id' => $this->group->country_id,
						'currency_id' => $this->group->country_id,
						'avatar' => is_file('./uploads/groups/'.$this->group->avatar)?$this->group->avatar:null,
						'is_validated' => $this->group ->is_validated,
						'online_banking_enabled' => $this->group->online_banking_enabled,
						'enable_member_information_privacy' => $this->group->enable_member_information_privacy,
						'active' => $this->group ->active,
						'owner' => $this->group->owner,
						'role' => $this->member->group_role_id?$group_roles[$this->member->group_role_id]:'Member',
						'is_admin' => $this->member->group_role_id?1:($this->member->is_admin?1:0),
        				'is_initiator' => 1,
					);

					$registered_group = array_merge($registered_group,$this->_get_group_activation_data($this->group,$this->user->id));
                   	$response = array(
	    				'status' => 1,
	    				'message' => $this->group->name.' successfully registered. Kindly proceed to Group Setup',
	    				'time' => time(),
	    				'user' => $user,
	    			);
	    			$group_data = $this->_get_checkin_data($user,array((object)$registered_group));
					$response = array_merge($response,$group_data);
				}else{
					$response = array(
						'status' => 0,
						'message' => "Something went wrong during group creation. Try again",
						'time' => time(),
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
    				'message' => 'Form validation failed',
    				'validation_errors' => $post,
    				'time' => time(),
    			);
			}
		}else{
			$response = array(
				'status' => 4,
				'message' => 'User account not found',
				'time' => time(),
			);
		}
    	echo encrypt_json_encode(array('response'=>$response));
	}

	function forgot_password(){
		foreach ($this->request as $key => $value) {
			if(preg_match('/phone/', $key)){
				$_POST[$key] = valid_phone($value);
			}else{
				$_POST[$key] = $value;
			}

		}
		$email = $this->input->post('email');
		$phone = $this->input->post('phone');
		$identity = $phone?:$email;
		if($auth = $this->users_m->get_user_authentication_by_identity($identity,$phone,$email)){
			$this->user = $this->users_m->get_user_by_identity($identity);
			$exempted_names = array('customer','equity');
			if($this->user && strlen($this->user->first_name) <= 10 && !in_array($this->user->first_name,$exempted_names) && ctype_alpha($this->user->first_name)){
				$remember_code = ($this->beta)?'1234':rand(1111,9999);
				if(valid_phone($identity) == ('2547398712777')){
					$remember_code = '1234';
				}
				$update = array(
					'remember_code' => $remember_code,
					'forgotten_password_time' => time(),
					'expiry_time' => strtotime("+1 hour",time()),
					'modified_by' => $this->user->id,
					'modified_on' => time(),
				);
				if($this->ion_auth->update($this->user->id,$update)){
					if($this->beta){
						$response = array(
							'status' => 1,
							'message' => 'Reset password code sent',
							'time' => time(),
						);
					}else{
						if($this->messaging->send_user_reset_password_code($this->user,$remember_code)){
							$response = array(
								'status' => 1,
								'message' => 'Reset password code sent',
								'time' => time(),
							);
						}else{
							$response = array(
								'status' => 0,
								'message' => 'Error occured sending reset password code. Try again',
								'time' => time(),
							);
						}
					}
				}else{
					$response = array(
						'status' => 0,
						'message' => 'Error occured requesting for password reset. Try again later',
						'time' => time(),
					);
				}
			}else{
				$response = array(
					'status' => 4,
					'message' => $identity.' is not registered. Kindly counter check',
					'time' => time(),
				);
			}
		}else{
			$response = array(
				'status' => 9,
				'message' => 'Authentication failed. Generate a new pin',
				'time' => time(),
			);
		}
    	echo encrypt_json_encode(array('response'=>$response));
	}


	function validate_forgot_password_code(){
		foreach ($this->request as $key => $value) {
			if(preg_match('/phone/', $key)){
				$_POST[$key] = valid_phone($value);
			}else{
				$_POST[$key] = $value;
			}

		}
		$email = $this->input->post('email');
		$phone = $this->input->post('phone');
		$identity = $phone?:$email;
		$pin = $this->input->post('pin');
		$document_number = $this->input->post('document_number');
		$document_type = $this->input->post('document_type');
		if($auth = $this->users_m->get_user_authentication_by_identity($identity,$phone,$email)){
			if($this->user = $this->users_m->get_user_by_identity($identity)){
				if($this->user->remember_code == $pin){
					if($this->user->expiry_time<time()){
						$response = array(
							'status' => 0,
							'message' => 'Reset pin has already expired. Kindly click resend code to start again',
							'time' => time(),
						);
					}else{
						$secret_key = random_string('alnum', 32);
						$update = array(
							'forgotten_password_code' => $secret_key,
							'modified_by' => $this->user->id,
							'modified_on' => time(),
						);
						if($this->ion_auth->update($this->user->id,$update)){
							$response = array(
								'status' => 1,
								'message' => 'Proceed to reset password',
								'time' => time(),
								'secret_key' =>$secret_key,
							);
						}else{
							$response = array(
								'status' => 0,
								'message' => 'Error occured updating user data. Try again later',
								'time' => time(),
							);
						}
					}
				}else{
					$response = array(
						'status' => 0,
						'message' => 'Invalid pin submitted. Kindly retry',
						'time' => time(),
					);
				}
			}else{
				$response = array(
					'status' => 4,
					'message' => $identity.' is not registered. Kindly counter check',
					'time' => time(),
				);
			}
		}else{
			$response = array(
				'status' => 9,
				'message' => 'Authentication failed. Generate a new pin',
				'time' => time(),
			);
		}
    	echo encrypt_json_encode(array('response'=>$response));
    }

    function reset_password(){
		foreach ($this->request as $key => $value) {
			if(preg_match('/phone/', $key)){
				$_POST[$key] = valid_phone($value);
			}else{
				$_POST[$key] = $value;
			}

		}
		$this->form_validation->set_rules('secret_key', 'Secret Key', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
    	$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
    	$email = $this->input->post('email');
		$phone = $this->input->post('phone');
    	if(valid_email($email) || valid_phone($phone)){
    		if($this->form_validation->run()){
				$identity = $phone?:$email;
				$secret_key = $this->input->post('secret_key');
				$password = $this->input->post('password');
				$confirm_password = $this->input->post('confirm_password');
				if($auth = $this->users_m->get_user_authentication_by_identity($identity,$phone,$email)){
					if($this->user = $this->users_m->get_user_by_identity($identity)){
						if($this->user->expiry_time > time()){
							if($this->user->forgotten_password_code == $secret_key){
								$object = $this->ion_auth->forgotten_password_complete($secret_key,$password);
				                if($object){
				                    $object = (object)$object;
				                    if($this->ion_auth->reset_password($object->identity,$password)){
				                        $this->ion_auth->clear_forgotten_password_code($secret_key);
				                        if($token = $this->_generate_access_token($auth->id,$this->user->id)){
				                        	if($this->ion_auth->login($object->identity,$object->new_password,0)){
				                        		$this->messaging->notify_user_password_change($this->user);
					                            $user_groups = $this->_get_user_groups($this->user->id);
					    						$invited_user_groups = array();
					    						$user = array(
					    							'first_name' => strtoupper($this->user->first_name),
					    							'last_name' => strtoupper($this->user->last_name),
					    							'phone' => $this->user->phone?:0,
					    							'email' => $this->user->email,
					    							'id' => $this->user->id,
					    							'avatar' => is_file('./uploads/groups/'.$this->user->avatar)?$this->user->avatar:null,
					    						);
					    						$response = array(
					    							'status' => 1,
								    				'message' => 'Password successfully changed',
								    				'time' => time(),
								    				'access_token' => $token->access_token,
								    				'user' => $user,
								    				'user_is_validated' => 1,
					    						);
					    						$group_data = $this->_get_checkin_data((object)$user);
					    						$response = array_merge($response,$group_data);
					                        }else{
					                            $response = array(
							                    	'status'=>0,
							                    	'message' => $this->ion_auth->errors(),
							                    	'time' => time(),
							                    );
					                        }
										}else{
					    					$response = array(
							    				'status' => 0,
							    				'message' => 'Token generation failed, login to proceed',
							    				'time' => time(),
							    				'access_token' => '',
							    				'expirer_in' => '',
							    			);
					    				}
				                    }else{
				                        $response = array(
					                    	'status' => 0,
					                    	'message' => $this->ion_auth->errors(),
					                    	'time' => time(),
					                    );
				                    }
				                }else{
				                    $response = array(
				                    	'status'=>0,
				                    	'message' => $this->ion_auth->errors(),
				                    	'time' => time(),
				                    );
				                }
							}else{
								$response = array(
									'status' => 9,
									'message' => 'Sorry, we are unable to complete password change at the moment. Try again later. secret_key: '.$this->user->forgotten_password_code,
									'time' => time(),
								);
							}
						}else{
							$response = array(
								'status' => 9,
								'message' => 'Password reset expired. Try again',
								'time' => time(),
							);
						}
					}else{
						$response = array(
							'status' => 4,
							'message' => $identity.' is not registered. Kindly counter check',
							'time' => time(),
						);
					}
				}else{
					$response = array(
	    				'status' => 9,
	    				'message' => 'Authentication failed. Generate a new pin',
	    				'time' => time(),
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
					'message' => 'Form validation failed',
					'validation_errors' => $post,
					'time' => time(),
				);
	    	}
    	}else{
    		$response = array(
    			'status' => 0,
    			'message' => 'Enter a valid email address or phone number',
    		);
    	}
    	
    	echo encrypt_json_encode(array('response'=>$response,'request'=>$this->request));
    }

    function get_user_checkin_data(){
		$group_role_ids = array();
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
        	$user = (object)array(
				'id' => $this->user->id,
				'first_name' => strtoupper($this->user->first_name),
				'last_name' => strtoupper($this->user->last_name),
				'username' => strtoupper($this->user->username),
				'phone' => valid_phone($this->user->phone),
				'email' => $this->user->email,
				'avatar' => is_file('./uploads/groups/'.$this->user->avatar)?$this->user->avatar:null,
			);
        	$response = array(
				'status' => 1,
				'message' => 'Checkin data',
				'time' => time(),
				'user' => $user,
			);
			$group_data = $this->_get_checkin_data($user);
			$response = array_merge($response,$group_data);
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
	}


	function change_user_password(){
		foreach ($this->request as $key => $value) {
			if(preg_match('/phone/', $key)){
				$_POST[$key] = valid_phone($value);
			}else{
				$_POST[$key] = $value;
			}
		}
		$this->form_validation->set_rules('user_id', 'Phone', 'required|numeric');
		$this->form_validation->set_rules('old_password', 'Old Password', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
    	$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
    	if($this->form_validation->run()){
    		$user_id = $this->input->post('user_id');
			$old_password = $this->input->post('old_password');
			$password = $this->input->post('password');
			$confirm_password = $this->input->post('confirm_password');
			if($this->user = $this->ion_auth->get_user($user_id)){
				$identity = valid_email($this->user->email)?$this->user->email:(valid_phone($this->user->phone)?:0);
				if($this->ion_auth->login($identity,$old_password)){
					if($this->ion_auth->change_password($identity,$old_password,$password)){
						$this->messaging->notify_user_password_change($this->user);
						$response = array(
							'status' => 1,
							'message' => 'Password successfully changed',
							'time' => time(),
						);
					}else{
						$response = array(
							'status' => 0,
							'message' => strtolower(strip_tags($this->ion_auth->errors())),
							'time' => time(),
						);
					}
				}else{
					$response = array(
						'status' => 0,
						'message' => 'Password change failed: Wrong password entered',
						'time' => time(),
					);
				}
			}else{
				$response = array(
					'status' => 4,
					'message' => $identity.' is not registered. Kindly counter check',
					'time' => time(),
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
				'message' => 'Form validation failed',
				'validation_errors' => $post,
				'time' => time(),
			);
    	}
    	echo encrypt_json_encode(array('response'=>$response));
	}

	function get_group_data(){
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
	            	$response = array(
						'status' => 1,
						'message' => 'Group data',
					);
					$group_data = $this->_get_checkin_data($this->user,array((object)$this->group));
					$response = array_merge($response,$group_data);
				}else{
	                $response = array(
	                    'status' => 0,
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
        echo encrypt_json_encode(array('response'=>$response));
	}
















	/**************unencrypted functions****************/	

	function get_user_group_data_information($user_id=0){
		$length_limit = '3000000';
		set_time_limit(0);
        ini_set('memory_limit','512M');
        ini_set('max_execution_time', 12000);
        error_reporting(E_ALL);
		ini_set('display_errors', 1);
		$timestamp = $this->input->get_post('timestamp');
		$user_id = $this->input->get_post('user_id')?:$user_id;
		if($user_id){
			$member_group_ids_list = $this->members_m->get_member_group_ids_list_where_user_id($user_id);
			$member_user_ids_list = $this->members_m->get_member_user_ids_list_by_group_id_list($member_group_ids_list);
			$group_data = $this->mobile_m->get_user_group_data($user_id,$member_group_ids_list,$member_user_ids_list,$timestamp);
			$data_length = strlen(encrypt_json_encode($group_data));
			$group_data_information = array( 
				"data_length" => mb_strlen(encrypt_json_encode($group_data)),
				"number_of_queries" => ceil($data_length/$length_limit),
			);
			$result = encrypt_json_encode($group_data_information);
		}else{
			$result = '{
				"response_code":"-1",
				"message":"User profile not found"
			}';
		}
		file_put_contents("logs/app_log.dat","\n".date("d-M-Y h:i A")."\t".serialize($result)."\t",FILE_APPEND);
		echo $result;
	}

	function signup(){
		$first_name = $this->input->post('first_name');
		$last_name = $this->input->post('last_name');
		$identity = trim($this->input->post('identity'));
		$password = trim($this->input->post('password'));
		$group_name = $this->input->post('group_name');
		$group_size = $this->input->post('group_size');
		$this->form_validation->set_rules($this->signup_rules);
		if($this->form_validation->run()){
			if(is_character_allowed($first_name)==FALSE || is_character_allowed($last_name)==FALSE || $group_size>100000 || is_character_allowed($group_name)==FALSE || preg_match('/mailinator/',$identity)){
			       	echo '{
						"response_code":"-2",
						"message":"Sign Up Character Validation Failed"
					}';
                }
		        $additional_data = array(
                    'created_on'=>time(),
                    'active'=>1, 
                    'ussd_pin'=>rand(1000,9999),
                    'first_name'=>ucfirst($first_name), 
                    'last_name'=>ucfirst($last_name), 
                );
                if(valid_phone($identity)){
                    $identity = valid_phone($identity,'',TRUE);
                }
                $group_id = $this->ion_auth->get_group_by_name('member');
                if($group_id){
                    $groups = array($group_id->id);
                }else{
                    $groups = array(2);
                }
                $user_id = $this->ion_auth->register($identity,$password,'', $additional_data,$groups,TRUE);
                if($user_id){
                	if($group_id = $this->investment_groups->create_group($user_id,strip_tags($group_name),$group_size,'','')){
	                    if($this->ion_auth->login($identity,$password,1)){
	                       $user = $this->ion_auth->get_user($user_id);
	                       $group = $this->groups_m->get($group_id);
	                       echo '{
									"response_code":"1",
									"message":"Sign Up Successful",
									"user" : '.encrypt_json_encode($user).',
									"group" : '.encrypt_json_encode($group).'
								}';
	                    }else{
	                       echo '{
								"response_code":"-4",
								"message":"Encountered error while trying to login current user",
								"user" : '.encrypt_json_encode(array()).',
								"group" : '.encrypt_json_encode(array()).'
							}'; 
	                    }
	                }else{
	                    echo '{
							"response_code":"-3",
							"message":"Encountered error creating group profile",
							"user" : '.encrypt_json_encode(array()).',
							"group" : '.encrypt_json_encode(array()).'
						}';
	                }
                }else{
                	if($this->users_m->check_if_identity_exists($identity)){
						echo '{
							"response_code":"0",
							"message":"'.$identity.' already registered",
							"user" : "'.encrypt_json_encode(array()).'",
							"group" : "'.encrypt_json_encode(array()).'"
						}';
					}else{
						echo '{
							"response_code":"-2",
							"message":"Encountered error creating user profile",
							"user" : '.encrypt_json_encode(array()).',
							"group" : '.encrypt_json_encode(array()).'
						}';
					}
                }
		}else{
			echo '{
				"response_code":"-1",
				"message":"Sign Up Validation Failed",
				"validation_errors" : "'.validation_errors().'"
			}';
		}
	}

	protected $new_signup_rules = array(
			array(
					'field' => 'group_name',
					'label' => 'Group Name',
					'rules' => 'trim|required',
				),
			array(
					'field' => 'group_size',
					'label' => 'Group Size',
					'rules' => 'trim|required|numeric',
				),
			array(
					'field' => 'user_id',
					'label' => 'Current User Id',
					'rules' => 'trim|required|numeric',
				),
		);

	function new_signup(){
		$file = file_get_contents('php://input');
    	$response = array();
    	$request = array();
    	header('Content-Type: application/json');
    	$time = time();
    	if($file){
    		$result = json_decode($file);
    		$request = $result;
    		if($result){
    			foreach ($result as $result_key=>$result_value) {
    				$_POST[$result_key] = $result_value;
    			}
    			$this->form_validation->set_rules($this->new_signup_rules);
    			if($this->form_validation->run()){
    				$user = $this->ion_auth->get_user($this->input->post('user_id'));
                	$this->ion_auth->update_last_login($user->id);
    				if($user){
    					$group_name = $this->input->post('group_name');
    					$group_size = $this->input->post('group_size');
     					if($group_id = $this->investment_groups->create_group($user->id,strip_tags($group_name),$group_size,'','',TRUE)){
	                       $group = $this->groups_m->get($group_id);
	                       $response = array(
			    				'status' => 1,
			    				'time' => time(),
			    				'success' => 'Successfully created group',
			    				'group' => $group,
			    				'user' => $user,
			    			);
		                }else{
		                    $response = array(
			    				'status' => 0,
			    				'time' => time(),
			    				'error' => 'Error creating new group',
			    			);
		                }
    				}else{
    					$response = array(
		    				'status' => 0,
		    				'time' => time(),
		    				'error' => 'User details currently unavailable',
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
		    				'error' => 'Form validation failed',
		    				'validation_errors' => $post,
		    			);
		        }
			}else{
    			$response = array(
	    				'status' => 0,
	    				'time' => time(),
	    				'error' => 'File sent has the wrong format',
	    			);
    		}
    	}else{
    		$response = array(
    				'status' => 0,
    				'time' => time(),
    				'error' => 'No file sent',
    			);
    	}

    	echo encrypt_json_encode(array('response'=>$response,'request'=>$request));
	}

	function activate(){
		$group_id = $this->input->post('group_id');
		$user_id = $this->input->post('user_id');
		$activation_code = $this->input->post('activation_code');
		//$activation_code = $this->input->post('activation_code');
		$this->form_validation->set_rules('activation_code', 'Activation Code', 'trim|required|numeric');
		$this->form_validation->set_rules('user_id', 'User Id', 'trim|required|numeric');
		$this->form_validation->set_rules('group_id', 'Group Id', 'trim|required|numeric');
		if($this->form_validation->run()){
			if($group = $this->groups_m->get($group_id)){
				if($group->activation_code==$activation_code){
					$data = array(
						'lock_access'=>0,
						'modified_on'=>time(),
						'modified_by'=>$user_id
					);
					if($result = $this->groups_m->update($group->id,$data)){
						if($user = $this->ion_auth->get_user($user_id)){
							echo '{
								"response_code":"1",
								"message":"Group Successfully Activated"
							}';
						}else{
							echo '{
								"response_code":"-3",
								"message":"Could not find User Profile"
							}';
						}
					}else{
						//$this->session->set_flashdata('error','Something went wrong during group activation');
						echo '{
							"response_code":"-5",
							"message":"The activation code you entered is does not match the code sent to you"
						}';
					}
				}else{
					echo '{
						"response_code":"-4",
						"message":"The activation code you entered is does not match the code sent to you"
					}';
				}
			}else{
				echo '{
					"response_code":"-1",
					"message":"Could not find your group profile"
				}';
			}
		}else{
			echo '{
				"response_code":"-2",
				"message":"Group Activation Validation Failed",
				"validation_errors" : "'.validation_errors().'"
			}';
		}	
	}

	function resend_activation_code(){
		$group_id = $this->input->post('group_id');
		$user_id = $this->input->post('user_id');
		$this->form_validation->set_rules('user_id', 'User Id', 'trim|required|numeric');
		$this->form_validation->set_rules('group_id', 'Group Id', 'trim|required|numeric');
		if($this->form_validation->run()){
			$group = $this->groups_m->get($group_id);
			if($group){
				$user = $this->ion_auth->get_user($user_id);
				if($user){
					if($this->messaging->send_activation_code($user,$group->activation_code,$group->slug,$group->name)){
						if($user->email&&$user->phone){
							echo '{
								"response_code":"1",
								"message":"Activation code sent to your phone number '.$user->phone.' and e-mail address '.$user->email.' "
							}';
						}else if($user->email){
							echo '{
								"response_code":"1",
								"message":"Activation code sent to your e-mail address'.$user->email.'"
							}';
						}else if($user->phone){
							echo '{
								"response_code":"1",
								"message":"Activation code sent to your phone number '.$user->phone.'"
							}';
						}else{
							echo '{
								"response_code":"-5",
								"message":"Could not send activation code"
							}';
						}
					}else{
						echo '{
							"response_code":"-4",
							"message":"Could not send activation code"
						}';
					}
				}else{
					echo '{
						"response_code":"-3",
						"message":"Could not find user profile"
					}';
				}
			}else{
				echo '{
					"response_code":"-2",
					"message":"Could not find group profile"
				}';
			}
		}else{
			echo '{
				"response_code":"-1",
				"message":"Group Resend Activation Validation Failed",
				"validation_errors" : "'.validation_errors().'"
			}';
		}
	}

	function change_email_address(){
		$email = trim($this->input->post('email'));
		$_POST['email'] = $email;
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$change_email_address_rules = array(
			array(
                'field' =>  'email',
                'label' =>  'Email Address',
                'rules' =>  'required|trim|valid_email|callback__unique_email_identity',
            ),
            array(
                'field' =>  'user_id',
                'label' =>  'User',
                'rules' =>  'required|trim|numeric',
            ),
            array(
                'field' =>  'group_id',
                'label' =>  'Group',
                'rules' =>  'required|trim|numeric',
            ),
		);
		
		$this->form_validation->set_rules($change_email_address_rules);
		if($this->form_validation->run()){
			$user = $this->ion_auth->get_user($user_id);
			if($user){
				$data = array(
					'email' => $email,
	                'modified_on'   => time(),
	                'modified_by'   => $user_id,
	            );
				$result = $this->ion_auth->update($user->id,$data);
				if($result){
					$user = $this->ion_auth->get_user($user_id);
					if($user){
						if($group = $this->groups_m->get($group_id)){
							if($this->messaging->send_activation_code($user,$group->activation_code,$group->slug,$group->name)){
								if($user->email&&$user->phone){
									echo '{
										"response_code":"1",
										"message":"Successfully changed your contact information and resent the activation code to both '.$user->phone.' and '.$user->email.' "
									}';
								}else if($user->email){
									echo '{
										"response_code":"1",
										"message":"Successfully changed your e-mail address and resent the activation code to '.$user->email.' "
									}';
								}else if($user->phone){
									echo '{
										"response_code":"1",
										"message":"Successfully changed your phone number and resent the activation code to '.$user->phone.' "
									}';
								}
							}else{
								echo '{
									"response_code":"-6",
									"message":"Could not resend activation  code"
								}';
							}
						}else{
							echo '{
								"response_code":"-5",
								"message":"Could not find group profile"
							}';
						}
					}else{
						echo '{
							"response_code":"-4",
							"message":"Could not find user profile"
						}';
					}
				}else{
					echo '{
						"response_code":"-3",
						"message":"Could not update user profile"
					}';
				}
			}else{
				echo '{
					"response_code":"-2",
					"message":"Could not find user profile"
				}';
			}
		}else{
			//do nothing validation failed
			echo '{
				"response_code":"-1",
				"message":"Change Email Address Validation Failed",
				"validation_errors" : "'.validation_errors().'"
			}';
		}
	}

	function _unique_email_identity(){
		$email = $this->input->post('email');
		$user = $this->ion_auth->get_user($this->input->post('user_id'));
		if($user){
			if($user->email==$email){
				$this->form_validation->set_message('_unique_email_identity','Enter another email address, you have entered the current email address');
	            return FALSE;
			}else{
				if($this->ion_auth->get_user_by_email($email)){
					$this->form_validation->set_message('_unique_email_identity','The email you entered is already registered, please enter another one');
	            	return FALSE;
				}else{
	            	return TRUE;
				}
			}
		}else{
			$this->form_validation->set_message('_unique_email_identity','Enter your email address, we could not find your user profile');
	        return FALSE;
		}
	}

	function change_phone_number(){
		$phone = $this->input->post('phone');
		$user_id = $this->input->post('user_id');
		$group_id = $this->input->post('group_id');
		$change_phone_number_rules = array(
			array(
                'field' =>  'phone',
                'label' =>  'Phone Number',
                'rules' =>  'required|trim|callback__unique_phone_number_identity',
            ),
            array(
                'field' =>  'user_id',
                'label' =>  'User',
                'rules' =>  'required|trim|numeric',
            ),
            array(
                'field' =>  'group_id',
                'label' =>  'Group',
                'rules' =>  'required|trim|numeric',
            ),
		);
		$this->form_validation->set_rules($change_phone_number_rules);
		if($this->form_validation->run()){
			$user = $this->ion_auth->get_user($user_id);
			if($user){
				$data = array(
					'phone' => valid_phone($phone),
	                'modified_on'   => time(),
	                'modified_by'   => $user_id,
	            );
				$result = $this->ion_auth->update($user->id,$data);
				if($result){
					if($user){
						$user = $this->ion_auth->get_user($user_id);
						if($group = $this->groups_m->get($group_id)){
							if($this->messaging->send_activation_code($user,$group->activation_code,$group->slug,$group->name)){
								echo '{
									"response_code":"1",
									"message":"Successfully changed your phone number"
								}';
							}else{
								echo '{
									"response_code":"-6",
									"message":"Could not resend activation  code"
								}';
							}
						}else{
							echo '{
								"response_code":"-5",
								"message":"Could not find group profile"
							}';
						}
					}else{
						echo '{
							"response_code":"-4",
							"message":"Could not find user profile"
						}';
					}
				}else{
					echo '{
						"response_code":"-3",
						"message":"Could not update user profile"
					}';
				}
			}else{
				echo '{
					"response_code":"-2",
					"message":"Could not find user profile"
				}';
			}
		}else{
			//do nothing validation failed
			echo '{
				"response_code":"-1",
				"message":"Change Phone Number Validation Failed",
				"validation_errors" : "'.validation_errors().'"
			}';
		}
	}

	function _unique_phone_number_identity(){
		$user = $this->ion_auth->get_user($this->input->post('user_id'));
		if($user){
			if($phone = valid_phone($this->input->post('phone'))){
				if($user->phone==$phone){
					$this->form_validation->set_message('_unique_phone_number_identity','Enter another phone number, you have entered the current phone number');
		            return FALSE;
				}else{
					if($user = $this->ion_auth->get_user_by_phone($phone)){
						$this->form_validation->set_message('_unique_phone_number_identity','The phone number you entered is already registered, please enter another one');
		            	return FALSE;
					}else{
		            	return TRUE;
					}
				}
			}else{
				$this->form_validation->set_message('_unique_phone_number_identity','The phone number you entered is invalid');
		        return FALSE;
			}
		}else{
			$this->form_validation->set_message('_unique_phone_number_identity','Enter your phone number, we could not find your user profile');
	        return FALSE;
		}
	}

	function _valid_identity(){
        $identity = trim($this->input->post('identity'));
        if(valid_phone($identity)){
        	return TRUE;
        }else if(valid_email($identity)){
        	return TRUE;
        }else{
        	$this->form_validation->set_message('_valid_identity','Enter a valid Email or Phone Number'.$identity);
            return FALSE;
        }
    }

    function new_forgot_password(){
		$file = file_get_contents('php://input');
    	$response = array();
    	$request = array();
    	header('Content-Type: application/json');
    	$time = time();
    	if($file){
    		$result = json_decode($file);
    		$request = $result;
    		if($result){
    			foreach ($result as $result_key=>$result_value) {
    				$_POST[$result_key] = $result_value;
    			}
    			$forgot_password_rules = array(
    				array(
    					"field" => "identity",
    					"label" => "Identity",
    					"rules" => "required|trim|callback__valid_identity"
    				)
    			);
    			$this->form_validation->set_rules($forgot_password_rules);
    			if($this->form_validation->run()){
    				$identity = $this->input->post('identity');
    				$this->user = $this->ion_auth->get_user_by_identity($identity);
                	$this->ion_auth->update_last_login($this->user->id);
    				if($this->user){
    					$remember_code = rand(11111,99999);
    					$update = array(
							'remember_code' => $remember_code,
							'forgotten_password_time' => time(),
							'expiry_time' => strtotime("+1 hour",time()),
							'modified_by' => $this->user->id,
							'modified_on' => time(),
						);
						if($this->ion_auth->update($this->user->id,$update)){
							if($this->messaging->send_user_reset_password_code($this->user,$remember_code)){
								$response = array(
									'status' => 1,
									'message' => 'Reset password code sent',
									'time' => time(),
								);
							}else{
								$response = array(
									'status' => 0,
									'message' => 'Error occured sending reset password code. Try again',
									'time' => time(),
								);
							}
						}else{
							$response = array(
								'status' => 0,
								'message' => 'Error occured requesting for password reset. Try again later',
								'time' => time(),
							);
						}
    				}else{
    					$response = array(
		    				'status' => 0,
		    				'time' => time(),
		    				'message' => 'Could not find your profile. Kindly register',
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
	    				'message' => 'File sent has the wrong format',
	    			);
    		}
    	}else{
    		$response = array(
    				'status' => 0,
    				'time' => time(),
    				'message' => 'No file sent',
    			);
    	}

    	echo encrypt_json_encode(array('response'=>$response,'request'=>$request));
	}

    function new_reset_password(){
		$file = file_get_contents('php://input');
    	$response = array();
    	$request = array();
    	$time = time();
    	if($file){
    		$request = json_decode($file);
    		if($request){
    			foreach ($request as $key => $value) {
    				if(preg_match('/phone/', $key)){
						$_POST[$key] = valid_phone($value);
					}else{
						$_POST[$key] = $value;
					}

    			}
    			$this->form_validation->set_rules('identity', 'Identity', 'required|callback__valid_identity');
    			$this->form_validation->set_rules('secret_key', 'Secret Key', 'required');
    			$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
            	$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
            	if($this->form_validation->run()){
            		$identity = $this->input->post('identity');
	    			$secret_key = $this->input->post('secret_key');
	    			$password = $this->input->post('password');
	    			$confirm_password = $this->input->post('confirm_password');
	    			if($this->user = $this->users_m->get_user_by_identity($identity)){
						if($this->user->expiry_time > time()){
							if($this->user->forgotten_password_code == $secret_key){
								$object = $this->ion_auth->forgotten_password_complete($secret_key,$password);
				                if($object){
				                    $object = (object)$object;
				                    if($this->ion_auth->reset_password($object->identity,$password)){
				                        $this->ion_auth->clear_forgotten_password_code($secret_key);
				                        if($this->ion_auth->login($object->identity,$object->new_password,0)){
				    						$response = array(
				    							'status' => 1,
							    				'message' => 'Password successfully changed',
							    				'time' => time(),
				    						);
				                        }else{
				                            $response = array(
						                    	'status'=>0,
						                    	'message' => $this->ion_auth->errors(),
						                    	'time' => time(),
						                    );
				                        }
				                    }else{
				                        $response = array(
					                    	'status' => 0,
					                    	'message' => $this->ion_auth->errors(),
					                    	'time' => time(),
					                    );
				                    }
				                }else{
				                    $response = array(
				                    	'status'=>0,
				                    	'message' => $this->ion_auth->errors(),
				                    	'time' => time(),
				                    );
				                }
							}else{
								$response = array(
									'status' => 0,
									'message' => 'Sorry, we are unable to complete password change at the moment. Try again later',
									'time' => time(),
								);
							}
						}else{
							$response = array(
								'status' => 0,
								'message' => 'Password reset expired. Try again',
								'time' => time(),
							);
						}
					}else{
						$response = array(
							'status' => 0,
							'message' => $identity.' is not registered. Kindly counter check',
							'time' => time(),
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
	    				'message' => 'Form validation failed',
	    				'validation_errors' => $post,
	    				'time' => time(),
	    			);
            	}
			}else{
    			$response = array(
	    			'status' => 0,
	    			'message' => 'File format error',
	    			
	    		);
    		}
    	}else{
    		$response = array(
    			'status' => 0,
    			'message' => 'Empty file sent',
    			
    		);
    	}
    	echo encrypt_json_encode(array('response'=>$response,'request'=>$request));
    }

    public function register_member(){
		die("disabled adding member to group");
    	$file = file_get_contents('php://input');
    	$response = array();
    	$result = array();
    	header('Content-Type: application/json');
    	if($file){
    		$result = json_decode($file);
    		$group_id = isset($result->group_id)?trim($result->group_id):0;
    		$user_id = isset($result->user_id)?trim($result->user_id):0;
    		$member = array();
    		$result = isset($result->members)?$result->members:'';
    		if($group_id){
    			$group = $this->groups_m->get($group_id);
    			if($group){
    				if($user_id){
    					$user = $this->ion_auth->get_user($user_id);
    					if($user){
    						$member = $this->members_m->get_group_member_by_user_id($group->id,$user->id);
    						if($member){

    						}else{
    							$response[0] = array(
									'mobile_local_id' => 0,
									'status'	=>	0,
									'member_id' => 0,
									'error'		=>	'Member details could not be found',
								);
    						}
    						if(is_array($result)){
				    			foreach ($result as $import_key => $user_data) {
				    				$member_id = FALSE;
				    				$first_name = $user_data->first_name;
				    				$last_name = isset($user_data->last_name)?$user_data->last_name:"";
				    				$phone = $user_data->phone;
				    				$email = $user_data->email;
				    				$group_role_id = $user_data->group_role_id?:0;
				    				if(!valid_email($email)){$email='';}
				    				if($first_name&&(valid_phone($phone)||valid_email($email))&&(is_numeric($group_role_id)||$group_role_id=="")){
				    					//$ignore_last_name_validation = TRUE;
				   						$member_id = $this->group_members->add_member_to_group($group,$first_name,$last_name,valid_phone($phone),$email,TRUE,TRUE,$user,$member->id,$group_role_id);
	   									if($member_id){
	   										$response[0] = array(
	   												'member_id' => $member_id,
	   												'status'	=>	1,
	   												'error'		=>	'',
	   											);
	   										$this->setup_tasks_tracker->set_completion_status('add-group-members',$group->id,$user->id);
	   									}else{
	   										$member_id = $this->members_m->member_exists_in_group(valid_phone($phone),$group_id);
	   										if($member_id){
	   											$errors = $this->session->userdata('warning_feedback');
		   										$error = '';
		   										foreach ($errors as $key=>$e) {
		   											$error = $e;
		   										}
	   											$response[0] = array(
	   												'status' =>	2,
	   												'member_id' => $member_id,
	   												'error'		=>	$error,
	   											);
	   										}else{
	   											$errors = $this->session->userdata('warning_feedback');
		   										$error = '';
		   										foreach ($errors as $key=>$e) {
		   											$error = $e;
		   										}
		   										$response[0] = array(
		   												'status' =>	0,
		   												'member_id' => $member_id,
		   												'error'		=>	$error,
		   											);
	   										}
	   									}
				    				}else{
				    					$response[0] = array(
											'status'	=>	0,
											'member_id' => $member_id,
											'error'		=>	'Member details validation failed',
										);
				    				}
				    			}
				    		}else{
				    			$response[0] = array(
									'status'	=>	0,
									'member_id' => 0,
									'error'		=>	'Result is not in the correct  format',
								);
				    		}
    					}else{
    						$response[0] = array(
								'status'	=>	0,
								'member_id' => 0,
								'error'		=>	'User details could not be found',
							);
    					}
    				}else{
    					$response[0] = array(
							'status'	=>	0,
							'member_id' => 0,
							'error'		=>	'Logged in user details are empty',
						);
    				}
    			}else{
    				$response[0] = array(
						'status'	=>	0,
						'member_id' => 0,
						'error'		=>	'Group not found',
					);
    			}
    		}else{
    			$response[0] = array(
					'status'	=>	0,
					'member_id' => 0,
					'error'		=>	'Empty group details',
				);
    		}
    	}else{
    		$response[0] = array(
				'status'	=>	0,
				'member_id' => 0,
				'error'		=>	'No file sent',
			);
    	}
    	echo encrypt_json_encode($response);
    } 

    function delete_group_member(){
    	$file = file_get_contents('php://input');
    	$response = array();
    	header('Content-Type: application/json');
    	$time = time();
    	if($file){
    		$result = json_decode($file);
    		if(is_object($result) && isset($result->member_id)){
    			$group_id = trim($result->group_id);
    			$member_id = trim($result->member_id);
    			$user_id = trim($result->current_user_id);
    			$mobile_local_id = trim($result->mobile_local_id);
    			if($user_id&&$member_id&&$group_id&&$mobile_local_id){
    				$user = $this->ion_auth->get_user($user_id);
    				if($user_id){
    					$member_to_delete = $this->members_m->get_group_member($member_id,$group_id);
    					if($member_to_delete){
    						if($this->transactions->void_all_group_member_transactions($group_id,$member_to_delete->id)){
		                        $input = array(
		                            'active' => 0,
		                            'is_deleted' => 1,
		                            'modified_on' => $time,
		                            'modified_by' => $user->id
		                        );
		                        if($this->members_m->update($member_to_delete->id,$input)){
		                        	$response= array(
										'mobile_local_id' => $mobile_local_id,
										'status'	=>	1,
										
										'member_id' => $member_id,
										'user_id' => $user_id,				
										'error'		=>	'',
									);
		                        }else{
		                        	$response= array(
										'mobile_local_id' => $mobile_local_id,
										'status'	=>	2,
										'member_id' => $member_id,
										
										'user_id' => $user_id,				
										'error'		=>	'Unable to delete group member',
									);
		                        }
		                    }else{
		                    	$response= array(
										'mobile_local_id' => $mobile_local_id,
										'status'	=>	2,
										'member_id' => $member_id,
										'user_id' => $user_id,	
													
										'error'		=>	'Error deleting member transactions',
									);
		                    }
    					}else{
    						$response= array(
								'mobile_local_id' => $mobile_local_id,
								'status'	=>	2,
								'member_id' => $member_id,
								'user_id' => $user_id,	
											
								'error'		=>	'Member and group not found',
							);
    					}
    				}else{
    					$response= array(
							'mobile_local_id' => $mobile_local_id,
							'status'	=>	2,
							'member_id' => $member_id,
							'user_id' => $user_id,
											
							'error'		=>	'User not found',
						);
    				}
    			}else{
    				$response= array(
						'mobile_local_id' => 0,
						'status'	=>	0,
						'member_id' => 0,
						'user_id' => 0,				
						'error'		=>	'Missing parameters',
					);
    			}
    		}else{
    			$response= array(
					'mobile_local_id' => 0,
					'status'	=>	0,
					'member_id' => 0,
					
					'user_id' => 0,				
					'error'		=>	'Invalid file sent',
				);
    		}
    	}else{
    		$response= array(
				'mobile_local_id' => 0,
				'status'	=>	0,
				'member_id' => 0,
				
				'user_id' => 0,				
				'error'		=>	'No file sent',
			);
    	}
    	echo encrypt_json_encode((object)$response);
    }


    function manage_group_account(){
    	/*
			Type
			1 - bank account
			2 - Sacco account
			3 - Mobile money account
			4- petty cash

			Action Type
			1 - Delete
			2 - edit
			3 - create
			4 - open
			5 - close
			6 - Hide
			7 - unhide

    	*/
    	$file = file_get_contents('php://input');
    	$response = array();
    	$request = array();
    	$mobile_local_id = 0;
    	header('Content-Type: application/json');
    	if($file){
    		$result = json_decode($file);
    		$request = $result;
    		if(is_object($result) && isset($result->type) && isset($result->action_type) && isset($result->user_id) && isset($result->group_id)){
    			$type = trim($result->type);
    			$action_type = trim($result->action_type);
    			$user_id = trim($result->user_id);
    			$group_id = trim($result->group_id);
    			$mobile_local_id = isset($result->mobile_local_id)?$result->mobile_local_id:0;
    			if($user_id and $group_id){
    				$user = $this->ion_auth->get_user($user_id);
    				$group = $this->groups_m->get($group_id);

    				if($user && $group){
    					if($type==1){//bank Accounts
		    				if(isset($result->account_name) && isset($result->bank_id) && isset($result->bank_branch_id) && isset($result->account_number)){
		    					$account_name = trim($result->account_name);
		    					$bank_id = trim($result->bank_id);
		    					$bank_branch_id = trim($result->bank_branch_id);
		    					$account_number = trim($result->account_number);
		    					$initial_balance = trim($result->initial_balance);
		    					$id = trim($result->id);
		    					$password = isset($result->password)?trim($result->password):'';
		    					if($action_type==1){//Delete Bank Account
		    						if($id && is_numeric($id)){
		    							if($this->ion_auth->login($user->phone,$password)){
		    								$bank_account = $this->bank_accounts_m->get($id);
			    							if($bank_account){
			    								if($this->transaction_statements_m->check_if_group_account_has_transactions('bank-'.$bank_account->id,$bank_account->group_id)){
								                    $response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	2,			
														'error'		=>	'Account has existing transactions. Void transactions to delete account'
													);
								                }else{
								                	if($this->bank_accounts_m->delete($bank_account->id,$bank_account->group_id)){
								                		$response= array(
															'mobile_local_id' => $mobile_local_id,
															'time' => time(),
															'status'	=>	1,			
															'error'		=>	'Bank Account Successfully deleted'
														);
								                	}else{
								                		$response= array(
															'mobile_local_id' => $mobile_local_id,
															'time' => time(),
															'status'	=>	0,			
															'error'		=>	'Bank Account could not be deleted'
														);
								                	}
								                }
			    							}else{
			    								$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Bank Account does not exist'
												);
			    							}
		    							}else{
		    								$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Authentication failed. Account not deleted'
												);
		    							}
		    						}else{
		    							$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Essential paremeters missing'
										);
		    						}
		    					}else if($action_type==2){//edit bank account
		    						if($account_name && $bank_id && $bank_branch_id && valid_currency($initial_balance) && (strlen($account_number)>=5 && strlen($account_number)<=20) && (!empty($id) && is_numeric($id))){
		    							if($this->_is_unique_account($account_number,$bank_id,$id)){
		    								$bank = $this->banks_m->get($bank_id);
			    							$bank_branch = $this->bank_branches_m->get($bank_branch_id);
			    							$bannk_account = $this->bank_accounts_m->get($id);
			    							if($bank && $bank_branch){
			    								if($bannk_account){
			    									$bank_account_id = $this->bank_accounts_m->update($id,array(
										                'group_id'          =>  $group->id,
										                'account_number'    =>  $account_number,
										                'account_name'      =>  $account_name,
										                'initial_balance'   =>  currency($initial_balance),
										                'bank_branch_id'    =>  $bank_branch->id,
										                'bank_id'           =>  $bank->id,
										                'modified_by'        =>  $user->id,
										                'modified_on'        =>  time(),
										            ));
										            if($bank_account_id){
										            	$response= array(
															'mobile_local_id' => $mobile_local_id,
															'time' => time(),
															'bank_account_id' => $id,	
															'status'	=>	1,		
															'error'		=>	'Bank Account Successfully updated'
														);
										            }else{
										            	$response= array(
															'mobile_local_id' => $mobile_local_id,
															'time' => time(),
															'status'	=>	0,			
															'error'		=>	'Error occured updating bank account'
														);
										            }
			    								}else{
			    									$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	0,			
														'error'		=>	'Bank account you want to edit does not exist'
													);
			    								}
			    							}else{
			    								$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Bank or Bank branch does not exist'
												);
			    							}
		    							}else{
		    								$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),	
													'status'	=>	0,		
													'error'		=>	'Duplicate account'
												);
		    							}
		    						}else{
		    							$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Some parameters maybe missing or invalid'
										);
		    						}
		    					}else if($action_type==3){//create bank account
		    						if($account_name && $bank_id && $bank_branch_id && valid_currency($initial_balance) && (strlen($account_number)>=5 && strlen($account_number)<=20)){
		    							if($this->_is_unique_account($account_number,$bank_id,$id)){
		    								$bank = $this->banks_m->get($bank_id);
			    							$bank_branch = $this->bank_branches_m->get($bank_branch_id);
			    							if($bank && $bank_branch){
			    								$bank_account_id = $this->bank_accounts_m->insert(array(
									                'group_id'          =>  $group->id,
									                'account_number'    =>  $account_number,
									                'account_name'      =>  $account_name,
									                'initial_balance'   =>  currency($initial_balance),
									                'bank_branch_id'    =>  $bank_branch->id,
									                'bank_id'           =>  $bank->id,
									                'enable_email_transaction_alerts_to_members'  =>  0,
									                'created_by'        =>  $user->id,
									                'created_on'        =>  time(),
									                'active'            =>  1,
									            ));
									            if($bank_account_id){
									            	$this->setup_tasks_tracker->set_completion_status('create-group-bank-account',$group->id,$user->id);
									            	$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'bank_account_id' => $bank_account_id,	
														'status'	=>	1,		
														'error'		=>	'Bank Account Successfully added'
													);
									            }else{
									            	$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	0,			
														'error'		=>	'Error occured adding bank account'
													);
									            }
			    							}else{
			    								$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Bank or Bank branch does not exist'
												);
			    							}
		    							}else{
		    								$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),	
													'status'	=>	0,		
													'error'		=>	'Duplicate account'
												);
		    							}
		    						}else{
		    							$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Some parameters maybe missing or invalid'
										);
		    						}
		    					}else if($action_type==4){//open bank account
		    						if($id && is_numeric($id)){
		    							$bank_account = $this->bank_accounts_m->get($id);
		    							if($bank_account){
		    								if(!$bank_account->is_closed){
		    									$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Bank account already open'
												);
		    								}else{
		    									$res = $this->bank_accounts_m->update($bank_account->id,array(
		    											'is_closed'=>'',
		    											'modified_by'=>$user->id,
		    											'modified_on'=>time()
		    											));
		    									if($res){
		    										$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	1,			
														'error'		=>	''
													);
		    									}else{
		    										$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	0,			
														'error'		=>	'Error occured while re-opening bank account'
													);
		    									}
		    								}
		    							}else{
		    								$response= array(
												'mobile_local_id' => $mobile_local_id,
												'time' => time(),
												'status'	=>	0,			
												'error'		=>	'Bank account does not exist'
											);
		    							}
		    						}else{
		    							$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Missing essential parameters'
										);
		    						}
		    					}else if($action_type==5){//close bank account
		    						if($id && is_numeric($id)){
		    							$bank_account = $this->bank_accounts_m->get($id);
		    							if($bank_account){
		    								if($bank_account->is_closed){
		    									$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Bank account already closed'
												);
		    								}else{
		    									$res = $this->bank_accounts_m->update($bank_account->id,array(
		    											'is_closed'=>1,
		    											'modified_by'=>$user->id,
		    											'modified_on'=>time()
		    											));
		    									if($res){
		    										$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	1,			
														'error'		=>	'Bank account Successfully closed'
													);
		    									}else{
		    										$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	0,			
														'error'		=>	'Error occured while closing'
													);
		    									}
		    								}
		    							}else{
		    								$response= array(
												'mobile_local_id' => $mobile_local_id,
												'time' => time(),
												'status'	=>	0,			
												'error'		=>	'Bank account does not exist'
											);
		    							}
		    						}else{
		    							$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Missing essential parameters'
										);
		    						}
		    					}else if($action_type==6){
		    						
		    					}else if($action_type==7){
		    						
		    					}else{
		    						$response= array(
										'mobile_local_id' => $mobile_local_id,
										'time' => time(),
										'status'	=>	0,			
										'error'		=>	'Invalid action type provided'
									);
		    					}
		    				}else{
		    					$response= array(
									'mobile_local_id' => $mobile_local_id,
									'time' => time(),
									'status'	=>	0,			
									'error'		=>	'Missing files'
								);
		    				}
		    			}else if($type==2){//sacco Accounts
		    				$result->sacco_id = isset($result->sacco_id)?trim($result->sacco_id):isset($result->bank_id);
		    				$result->sacco_branch_id = isset($result->sacco_branch_id)?trim($result->sacco_branch_id):isset($result->bank_branch_id);
		    				if(isset($result->account_name) && isset($result->sacco_id) && isset($result->sacco_branch_id) && isset($result->account_number)){
		    					$account_name = trim($result->account_name);
		    					$sacco_id = trim($result->sacco_id);
		    					$sacco_branch_id = trim($result->sacco_branch_id);
		    					$account_number = trim($result->account_number);
		    					$initial_balance = trim($result->initial_balance);
		    					$id = trim($result->id);
		    					$password = isset($result->password)?trim($result->password):'';
		    					if($action_type==1){
		    						if($id && is_numeric($id)){
		    							if($this->ion_auth->login($user->phone,$password)){
		    								$sacco_account = $this->sacco_accounts_m->get($id);
			    							if($sacco_account){
			    								if($this->transaction_statements_m->check_if_group_account_has_transactions('sacco-'.$sacco_account->id,$sacco_account->group_id)){
								                    $response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	2,			
														'error'		=>	'Account has existing transactions. Void transactions to delete account'
													);
								                }else{
								                	if($this->sacco_accounts_m->delete($sacco_account->id,$sacco_account->group_id)){
								                		$response= array(
															'mobile_local_id' => $mobile_local_id,
															'time' => time(),
															'status'	=>	1,			
															'error'		=>	'Sacco Account Successfully deleted'
														);
								                	}else{
								                		$response= array(
															'mobile_local_id' => $mobile_local_id,
															'time' => time(),
															'status'	=>	0,			
															'error'		=>	'Sacco Account could not be deleted'
														);
								                	}
								                }
			    							}else{
			    								$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Sacco Account does not exist'
												);
			    							}
		    							}else{
		    								$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Authentication failed. Account not deleted'
												);
		    							}
		    						}else{
		    							$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Essential paremeters missing'
										);
		    						}
			    				}else if($action_type==2){
			    					if($account_name && $sacco_id && $sacco_branch_id && valid_currency($initial_balance) && (strlen($account_number)>=5 && strlen($account_number)<=20) && (!empty($id) && is_numeric($id))){
		    							if($this->_is_unique_sacco_account($account_number,$sacco_id,$id)){
		    								$sacco = $this->saccos_m->get($sacco_id);
			    							$sacco_branch = $this->sacco_branches_m->get($sacco_branch_id);
			    							$sacco_account = $this->sacco_accounts_m->get($id);
			    							if($sacco && $sacco_branch){
			    								if($sacco_account){
			    									$sacco_account_id = $this->sacco_accounts_m->update($id,array(
										                'group_id'          =>  $group->id,
										                'account_number'    =>  $account_number,
										                'account_name'      =>  $account_name,
										                'initial_balance'   =>  currency($initial_balance),
										                'sacco_branch_id'    =>  $sacco_branch->id,
										                'sacco_id'           =>  $sacco->id,
										                'modified_by'        =>  $user->id,
										                'modified_on'        =>  time(),
										            ));
										            if($sacco_account_id){
										            	$response= array(
															'mobile_local_id' => $mobile_local_id,
															'time' => time(),
															'sacco_account_id' => $id,	
															'status'	=>	1,		
															'error'		=>	'Sacco Account Successfully updated'
														);
										            }else{
										            	$response= array(
															'mobile_local_id' => $mobile_local_id,
															'time' => time(),
															'status'	=>	0,			
															'error'		=>	'Error occured updating sacco account'
														);
										            }
			    								}else{
			    									$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	0,			
														'error'		=>	'Sacco account you want to edit does not exist'
													);
			    								}
			    							}else{
			    								$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Saaco or Sacco branch does not exist'
												);
			    							}
		    							}else{
		    								$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),	
													'status'	=>	0,		
													'error'		=>	'Duplicate account'
												);
		    							}
		    						}else{
		    							$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Some parameters maybe missing or invalid'
										);
		    						}
			    				}else if($action_type==3){//create Sacco
			    					if($account_name && $sacco_id && $sacco_branch_id && valid_currency($initial_balance) && (strlen($account_number)>=5 && strlen($account_number)<=20)){
		    							if($this->_is_unique_sacco_account($account_number,$sacco_id,$id)){
		    								$sacco = $this->saccos_m->get($sacco_id);
			    							$sacco_branch = $this->sacco_branches_m->get($sacco_branch_id);
			    							if($sacco && $sacco_branch){
			    								$sacco_account_id = $this->sacco_accounts_m->insert(array(
									                    'group_id'          =>  $group->id,
									                    'account_number'    =>  $account_number,
									                    'account_name'      =>  $account_name,
									                    'initial_balance'   =>  currency($initial_balance),
									                    'sacco_branch_id'    =>  $sacco_branch->id,
									                    'sacco_id'           => $sacco->id,
									                    'created_by'        =>  $user->id,
									                    'created_on'        =>  time(),
									                    'active'            =>  1,
									                ));
			    								if($sacco_account_id){
			    									$this->setup_tasks_tracker->set_completion_status('create-group-bank-account',$group->id,$user->id);
									            	$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),	
														'sacco_account_id' => $sacco_account_id,
														'status'	=>	1,		
														'error'		=>	'Sacco Account Successfully added'
													);
									            }else{
									            	$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	0,			
														'error'		=>	'Error occured adding Sacco account'
													);
									            }
			    							}else{
			    								$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Sacco or Sacco branch does not exist'
												);
			    							}
			    						}else{
			    							$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),	
													'status'	=>	0,		
													'error'		=>	'Duplicate account'
												);
			    						}
			    					}else{
			    						$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Some parameters maybe missing or invalid'
										);
			    					}
			    				}else if($action_type==4){//open sacco account
		    						if($id && is_numeric($id)){
		    							$sacco_account = $this->sacco_accounts_m->get($id);
		    							if($sacco_account){
		    								if(!$sacco_account->is_closed){
		    									$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Sacco account already open'
												);
		    								}else{
		    									$res = $this->sacco_accounts_m->update($sacco_account->id,array(
		    											'is_closed'=>'',
		    											'modified_by'=>$user->id,
		    											'modified_on'=>time()
		    											));
		    									if($res){
		    										$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	1,			
														'error'		=>	''
													);
		    									}else{
		    										$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	0,			
														'error'		=>	'Error occured while re-opening sacco account'
													);
		    									}
		    								}
		    							}else{
		    								$response= array(
												'mobile_local_id' => $mobile_local_id,
												'time' => time(),
												'status'	=>	0,			
												'error'		=>	'Sacco account does not exist'
											);
		    							}
		    						}else{
		    							$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Missing essential parameters'
										);
		    						}
		    					}else if($action_type==5){//close Sacco account
		    						if($id && is_numeric($id)){
		    							$sacco_account = $this->sacco_accounts_m->get($id);
		    							if($sacco_account){
		    								if($sacco_account->is_closed){
		    									$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Sacco account already closed'
												);
		    								}else{
		    									$res = $this->sacco_accounts_m->update($sacco_account->id,array(
		    											'is_closed'=>1,
		    											'modified_by'=>$user->id,
		    											'modified_on'=>time()
		    											));
		    									if($res){
		    										$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	1,			
														'error'		=>	'Sacco account Successfully closed'
													);
		    									}else{
		    										$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	0,			
														'error'		=>	'Error occured while closing'
													);
		    									}
		    								}
		    							}else{
		    								$response= array(
												'mobile_local_id' => $mobile_local_id,
												'time' => time(),
												'status'	=>	0,			
												'error'		=>	'Sacco account does not exist'
											);
		    							}
		    						}else{
		    							$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Missing essential parameters'
										);
		    						}
		    					}else if($action_type==6){
			    					
			    				}else if($action_type==7){
			    					
			    				}else{
			    					$response= array(
										'mobile_local_id' => $mobile_local_id,
										'time' => time(),
										'status'	=>	0,			
										'error'		=>	'Invalid action type provided'
									);
			    				}
		    				}else{
		    					$response= array(
									'mobile_local_id' => $mobile_local_id,
									'time' => time(),
									'status'	=>	0,			
									'error'		=>	'Missing files'
								);
		    				}
		    			}else if($type==3){//mobile money accounts
		    				$result->mobile_money_provider_id = isset($result->mobile_money_provider_id)?trim($result->mobile_money_provider_id):isset($result->bank_id);
		    				if(isset($result->account_name) && isset($result->mobile_money_provider_id) && isset($result->account_number)){
		    					$account_name = trim($result->account_name);
		    					$mobile_money_provider_id = trim($result->mobile_money_provider_id);
		    					$account_number = trim($result->account_number);
		    					$initial_balance = trim($result->initial_balance);
		    					$id = trim($result->id);
		    					$password = isset($result->password)?trim($result->password):'';
		    					if($action_type==1){//delete mobile money account
		    						if($id && is_numeric($id)){
		    							if($this->ion_auth->login($user->phone,$password)){
		    								$mobile_money_account = $this->mobile_money_accounts_m->get($id);
			    							if($mobile_money_account){
			    								if($this->transaction_statements_m->check_if_group_account_has_transactions('mobile-'.$mobile_money_account->id,$mobile_money_account->group_id)){
								                    $response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	2,			
														'error'		=>	'Account has existing transactions. Void transactions to delete account'
													);
								                }else{
								                	if($this->mobile_money_accounts_m->delete($mobile_money_account->id,$mobile_money_account->group_id)){
								                		$response= array(
															'mobile_local_id' => $mobile_local_id,
															'time' => time(),
															'status'	=>	1,			
															'error'		=>	'Mobile Money Account Successfully deleted'
														);
								                	}else{
								                		$response= array(
															'mobile_local_id' => $mobile_local_id,
															'time' => time(),
															'status'	=>	0,			
															'error'		=>	'Mobile Money Account could not be deleted'
														);
								                	}
								                }
			    							}else{
			    								$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Mobile Money Account does not exist'
												);
			    							}
		    							}else{
		    								$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Authentication failed. Account not deleted'
												);
		    							}
		    						}else{
		    							$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Essential paremeters missing'
										);
		    						}
			    				}else if($action_type==2){//edit mobile money account
			    					if($account_name && $mobile_money_provider_id && valid_currency($initial_balance) && (strlen($account_number)>=5 && strlen($account_number)<=20) && (!empty($id)&&is_numeric($id))){
		    							if($this->_is_unique_mobile_money_account($account_number,$mobile_money_provider_id,$id)){
		    								$mobile_money_provider = $this->mobile_money_providers_m->get($mobile_money_provider_id);
		    								$mobile_money_account = $this->mobile_money_accounts_m->get($id);
			    							if($mobile_money_provider){
			    								if($mobile_money_account){
			    									$mobile_account_id = $this->mobile_money_accounts_m->update($mobile_money_account->id,array(
											                'account_name' =>  $account_name,
											                'mobile_money_provider_id'  =>  $mobile_money_provider->id,
											                'account_number'            =>  $account_number,
											                'initial_balance'           =>  currency($initial_balance),
											                'modified_by'                =>  $user->id,
											                'modified_on'                =>  time(),
											                'group_id'                  =>  $group->id,
											            ));
				    								if($mobile_account_id){
										            	$response= array(
															'mobile_local_id' => $mobile_local_id,
															'time' => time(),	
															'mobile_account_id' => $id,
															'status'	=>	1,		
															'error'		=>	'Mobile Money Account Successfully updated'
														);
										            }else{
										            	$response= array(
															'mobile_local_id' => $mobile_local_id,
															'time' => time(),
															'status'	=>	0,			
															'error'		=>	'Error occured updating Mobile Money account'
														);
										            }
			    								}else{
			    									$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	0,			
														'error'		=>	'Mobile money account does not exist'
													);
			    								}
			    							}else{
			    								$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Mobile money provider does not exist'
												);
			    							}
			    						}else{
			    							$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),	
													'status'	=>	0,		
													'error'		=>	'Duplicate account details'
												);
			    						}
			    					}else{
			    						$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Some parameters maybe missing or invalid'
										);
			    					}
			    				}else if($action_type==3){//create mobile money account
			    					if($account_name && $mobile_money_provider_id && valid_currency($initial_balance) && (strlen($account_number)>=5 && strlen($account_number)<=20)){
		    							if($this->_is_unique_mobile_money_account($account_number,$mobile_money_provider_id,$id)){
		    								$mobile_money_provider = $this->mobile_money_providers_m->get($mobile_money_provider_id);
			    							if($mobile_money_provider){
			    								$mobile_account_id = $this->mobile_money_accounts_m->insert(array(
										                'account_name' =>  $account_name,
										                'mobile_money_provider_id'  =>  $mobile_money_provider->id,
										                'account_number'            =>  $account_number,
										                'initial_balance'           =>  currency($initial_balance),
										                'created_by'                =>  $user->id,
										                'created_on'                =>  time(),
										                'group_id'                  =>  $group->id,
										                'active'                    =>  1,
										            ));
			    								if($mobile_account_id){
			    									$this->setup_tasks_tracker->set_completion_status('create-group-bank-account',$group->id,$user->id);
									            	$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),	
														'mobile_account_id' => $mobile_account_id,
														'status'	=>	1,		
														'error'		=>	'Mobile Money Account Successfully added'
													);
									            }else{
									            	$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	0,			
														'error'		=>	'Error occured adding Mobile Money account'
													);
									            }
			    							}else{
			    								$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Mobile money provider does not exist'
												);
			    							}
			    						}else{
			    							$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),	
													'status'	=>	0,		
													'error'		=>	'Duplicate account'
												);
			    						}
			    					}else{
			    						$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Some parameters maybe missing or invalid'
										);
			    					}
			    				}else if($action_type==4){//open Mobile money account
		    						if($id && is_numeric($id)){
		    							$mobile_money_account = $this->mobile_money_accounts_m->get($id);
		    							if($mobile_money_account){
		    								if(!$mobile_money_account->is_closed){
		    									$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Mobile Money account already open'
												);
		    								}else{
		    									$res = $this->mobile_money_accounts_m->update($mobile_money_account->id,array(
		    											'is_closed'=>'',
		    											'modified_by'=>$user->id,
		    											'modified_on'=>time()
		    											));
		    									if($res){
		    										$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	1,			
														'error'		=>	''
													);
		    									}else{
		    										$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	0,			
														'error'		=>	'Error occured while re-opening mobile money account'
													);
		    									}
		    								}
		    							}else{
		    								$response= array(
												'mobile_local_id' => $mobile_local_id,
												'time' => time(),
												'status'	=>	0,			
												'error'		=>	'Mobile money account does not exist'
											);
		    							}
		    						}else{
		    							$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Missing essential parameters'
										);
		    						}
		    					}else if($action_type==5){//close Mobile money account
		    						if($id && is_numeric($id)){
		    							$mobile_money_account = $this->mobile_money_accounts_m->get($id);
		    							if($mobile_money_account){
		    								if($mobile_money_account->is_closed){
		    									$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Mobile Money account already closed'
												);
		    								}else{
		    									$res = $this->mobile_money_accounts_m->update($mobile_money_account->id,array(
		    											'is_closed'=>1,
		    											'modified_by'=>$user->id,
		    											'modified_on'=>time()
		    											));
		    									if($res){
		    										$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	1,			
														'error'		=>	'Mobile Money account Successfully closed'
													);
		    									}else{
		    										$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	0,			
														'error'		=>	'Error occured while closing'
													);
		    									}
		    								}
		    							}else{
		    								$response= array(
												'mobile_local_id' => $mobile_local_id,
												'time' => time(),
												'status'	=>	0,			
												'error'		=>	'Mobile Money account does not exist'
											);
		    							}
		    						}else{
		    							$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Missing essential parameters'
										);
		    						}
		    					}else if($action_type==6){
			    					
			    				}else if($action_type==7){
			    					
			    				}else{
			    					$response= array(
										'mobile_local_id' => $mobile_local_id,
										'time' => time(),
										'status'	=>	0,			
										'error'		=>	'Invalid action type provided'
									);
			    				}
		    				}else{
		    					$response= array(
									'mobile_local_id' => $mobile_local_id,
									'time' => time(),
									'status'	=>	0,			
									'error'		=>	'Missing files'
								);
		    				}
		    			}else if($type == 4){//Petty Cash
		    				if(isset($result->account_name)){
		    					$account_name = trim($result->account_name);
		    					$initial_balance = trim($result->initial_balance);
		    					$account_slug = generate_slug($account_name);
		    					$id = trim($result->id);
		    					$password = isset($result->password)?trim($result->password):'';
		    					if($action_type==1){//Delete petty cash account
									if($id && is_numeric($id)){
		    							if($this->ion_auth->login($user->phone,$password)){
		    								$petty_cash_account = $this->petty_cash_accounts_m->get($id);
			    							if($petty_cash_account){
			    								if($this->transaction_statements_m->check_if_group_account_has_transactions('petty-'.$petty_cash_account->id,$petty_cash_account->group_id)){
								                    $response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	2,			
														'error'		=>	'Account has existing transactions. Void transactions to delete account'
													);
								                }else{
								                	if($this->petty_cash_accounts_m->delete($petty_cash_account->id,$petty_cash_account->group_id)){
								                		$response= array(
															'mobile_local_id' => $mobile_local_id,
															'time' => time(),
															'status'	=>	1,			
															'error'		=>	'Petty Cash Account Successfully deleted'
														);
								                	}else{
								                		$response= array(
															'mobile_local_id' => $mobile_local_id,
															'time' => time(),
															'status'	=>	0,			
															'error'		=>	'Petty Cash Account could not be deleted'
														);
								                	}
								                }
			    							}else{
			    								$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Petty Cash Account does not exist'
												);
			    							}
		    							}else{
		    								$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Authentication failed. Account not deleted'
												);
		    							}
		    						}else{
		    							$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Essential paremeters missing'
										);
		    						}
			    				}else if($action_type==2){//Edit petty cash Account
			    					if($account_name && valid_currency($initial_balance) && (!empty($id) && is_numeric($id))){
			    						$petty_cash_account = $this->petty_cash_accounts_m->get($id);
			    						if($petty_cash_account){
			    							if($this->_is_unique_petty_cash_account($account_slug,$id)){
				    							$petty_cash_account_id = $this->petty_cash_accounts_m->update($petty_cash_account->id,array(
										                'account_name'      =>  $account_name,
										                'account_slug'      =>  $account_slug,
										                'initial_balance'   =>  currency($initial_balance),
										                'modified_by'        =>  $user->id,
										                'modified_on'        =>  time(),
										                'group_id'          =>  $group->id,
										            ));
				    							if($petty_cash_account_id){
				    								$response= array(
															'mobile_local_id' => $mobile_local_id,
															'time' => time(),
															'petty_cash_account_id' => $petty_cash_account->id,	
															'status'	=>	1,		
															'error'		=>	'Petty Cash Account Successfully updated'
														);
				    							}else{
				    								$response= array(
															'mobile_local_id' => $mobile_local_id,
															'time' => time(),
															'status'	=>	0,			
															'error'		=>	'Error occured updating Petty cash account'
														);
				    							}
				    						}else{
				    							$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),	
														'status'	=>	0,		
														'error'		=>	'Duplicate account. Could not update'
													);
				    						}
			    						}else{
			    							$response= array(
												'mobile_local_id' => $mobile_local_id,
												'time' => time(),
												'status'	=>	0,			
												'error'		=>	'Petty cash account to update does not exist'
											);
			    						}
			    					}else{
			    						$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Some parameters maybe missing or invalid'
										);
			    					}
			    				}else if($action_type==3){//Create petty cash account
			    					if($account_name && valid_currency($initial_balance)){
		    							if($this->_is_unique_petty_cash_account($account_slug,$id)){
			    							$petty_cash_account_id = $this->petty_cash_accounts_m->insert(array(
									                'account_name'      =>  $account_name,
									                'account_slug'      =>  $account_slug,
									                'initial_balance'   =>  currency($initial_balance),
									                'created_by'        =>  $user->id,
									                'created_on'        =>  time(),
									                'group_id'          =>  $group->id,
									                'active'            =>  1,
									            ));
			    							if($petty_cash_account_id){
			    								$this->setup_tasks_tracker->set_completion_status('create-group-bank-account',$group->id,$user->id);
			    								$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'petty_cash_account_id' => $petty_cash_account_id,	
														'status'	=>	1,		
														'error'		=>	'Petty Cash Account Successfully added'
													);
			    							}else{
			    								$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	0,			
														'error'		=>	'Error occured adding Petty cash account'
													);
			    							}
			    						}else{
			    							$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),	
													'status'	=>	0,		
													'error'		=>	'Duplicate account'
												);
			    						}
			    					}else{
			    						$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Some parameters maybe missing or invalid'
										);
			    					}
			    				}else if($action_type==4){//open Petty Cash account
		    						if($id && is_numeric($id)){
		    							$petty_cash_account = $this->petty_cash_accounts_m->get($id);
		    							if($petty_cash_account){
		    								if(!$petty_cash_account->is_closed){
		    									$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Petty Cash account already open'
												);
		    								}else{
		    									$res = $this->petty_cash_accounts_m->update($petty_cash_account->id,array(
		    											'is_closed'=>'',
		    											'modified_by'=>$user->id,
		    											'modified_on'=>time()
		    											));
		    									if($res){
		    										$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	1,			
														'error'		=>	''
													);
		    									}else{
		    										$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	0,			
														'error'		=>	'Error occured while re-opening Petty cash account'
													);
		    									}
		    								}
		    							}else{
		    								$response= array(
												'mobile_local_id' => $mobile_local_id,
												'time' => time(),
												'status'	=>	0,			
												'error'		=>	'Petty Cash account does not exist'
											);
		    							}
		    						}else{
		    							$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Missing essential parameters'
										);
		    						}
		    					}else if($action_type==5){//close Petty cash account
		    						if($id && is_numeric($id)){
		    							$petty_cash_account = $this->petty_cash_accounts_m->get($id);
		    							if($petty_cash_account){
		    								if($petty_cash_account->is_closed){
		    									$response= array(
													'mobile_local_id' => $mobile_local_id,
													'time' => time(),
													'status'	=>	0,			
													'error'		=>	'Petty Cash account already closed'
												);
		    								}else{
		    									$res = $this->petty_cash_accounts_m->update($petty_cash_account->id,array(
		    											'is_closed'=>1,
		    											'modified_by'=>$user->id,
		    											'modified_on'=>time()
		    											));
		    									if($res){
		    										$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	1,			
														'error'		=>	'Petty Cash account Successfully closed'
													);
		    									}else{
		    										$response= array(
														'mobile_local_id' => $mobile_local_id,
														'time' => time(),
														'status'	=>	0,			
														'error'		=>	'Error occured while closing'
													);
		    									}
		    								}
		    							}else{
		    								$response= array(
												'mobile_local_id' => $mobile_local_id,
												'time' => time(),
												'status'	=>	0,			
												'error'		=>	'Petty cash account does not exist'
											);
		    							}
		    						}else{
		    							$response= array(
											'mobile_local_id' => $mobile_local_id,
											'time' => time(),
											'status'	=>	0,			
											'error'		=>	'Missing essential parameters'
										);
		    						}
		    					}else if($action_type==6){
			    					
			    				}else if($action_type==7){
			    					
			    				}else{
			    					$response= array(
										'mobile_local_id' => $mobile_local_id,
										'time' => time(),
										'status'	=>	0,			
										'error'		=>	'Invalid action type provided'
									);
			    				}
		    				}else{
		    					$response= array(
									'mobile_local_id' => $mobile_local_id,
									'time' => time(),
									'status'	=>	0,			
									'error'		=>	'Missing files'
								);
		    				}
		    			}else{
		    				$response= array(
								'mobile_local_id' => $mobile_local_id,
								'time' => time(),
								'status'	=>	0,			
								'error'		=>	'Account Type does not exist'
							);
		    			}
    				}else{
    					$response= array(
							'mobile_local_id' => $mobile_local_id,
							'time' => time(),
							'status'	=>	0,			
							'error'		=>	'User or group does not exist'
						);
    				}
    			}else{
    				$response= array(
						'mobile_local_id' => $mobile_local_id,
						'time' => time(),
						'status'	=>	0,			
						'error'		=>	'Empty user and group'
					);
    			}
    		}else{
    			$response= array(
					'mobile_local_id' => $mobile_local_id,
					'time' => time(),
					'status'	=>	0,			
					'error'		=>	'Invalid object sent'
				);
    		}
    	}else{
    		$response= array(
				'mobile_local_id' => $mobile_local_id,
				'time' => time(),
				'status'	=>	0,			
				'error'		=>	'No file sent',
			);
    	}
    	echo encrypt_json_encode(array("response"=>(object)$response,"request"=>(object)$request));
    }


    function _is_unique_account($account_number=0,$bank_id=0,$id=0){
    	if($bank_id&&$account_number){
    		$account_exists = $this->bank_accounts_m->check_if_account_exists($id,$account_number,$bank_id);
	        if($account_exists){
	            return FALSE;
	        }else{
	            return TRUE;
	        }
    	}
    }

    function _is_unique_sacco_account($account_number=0,$sacco_id=0,$id=0){
    	if($sacco_id&&$account_number){
    		$account_exists = $this->sacco_accounts_m->check_if_account_exists($id,$account_number,$sacco_id);
	        if($account_exists){
	            return FALSE;
	        }else{
	            return TRUE;
	        }
    	}
    }

    function _is_unique_mobile_money_account($account_number=0,$mobile_money_provider_id=0,$id=0){
    	if($mobile_money_provider_id&&$account_number){
    		if($this->mobile_money_accounts_m->check_if_account_exists($id,$mobile_money_provider_id,$account_number)){
	            return FALSE;
	        }else{
	            return TRUE;
	        }
    	}
    }

    function _is_unique_petty_cash_account($account_slug='',$id=0){
        if($account_slug){
        	if($this->petty_cash_accounts_m->check_if_account_exists($id,$account_slug)){	            
        		return FALSE;
	        }else{
	            return TRUE;
	        }
        }else{
        	return FALSE;
        }
    }

    protected $validation_rules = array(
        array(
            'field' => 'name',
            'label' => 'Contribution Name',
            'rules' => 'trim|required',
        ),array(
            'field' => 'amount',
            'label' => 'Contribution Amount',
            'rules' => 'trim|required|currency',
        ),array(
            'field' => 'type',
            'label' => 'Contribution Type',
            'rules' => 'trim|numeric|required',
        ),array(
            'field' => 'regular_invoicing_active',
            'label' => 'Activate Invoicing',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'one_time_invoicing_active',
            'label' => 'Activate Invoicing',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'invoice_date',
            'label' => 'Invoice Date',
            'rules' => 'trim',
        ),array(
            'field' => 'contribution_date',
            'label' => 'Contribution Date/Due Date',
            'rules' => 'trim',
        ),array(
            'field' => 'contribution_frequency',
            'label' => 'How often do members contribute',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'month_day_monthly',
            'label' => 'Day of the Month',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'week_day_monthly',
            'label' => 'Day of the Week',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'week_day_weekly',
            'label' => 'Day of the Week',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'week_day_fortnight',
            'label' => 'Day of the Week',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'week_day_multiple',
            'label' => 'Day of the Week',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'week_number_fortnight',
            'label' => 'Day of the Week',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'month_day_multiple',
            'label' => 'Day of the Month',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'start_month_multiple',
            'label' => 'Staring Month',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'invoice_days',
            'label' => 'Invoice days',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'sms_notifications_enabled',
            'label' => 'Enable SMS Notifications',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'email_notifications_enabled',
            'label' => 'Enable Email Notifications',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'sms_template',
            'label' => 'SMS template',
            'rules' => 'trim',
        ),array(
            'field' => 'enable_fines',
            'label' => 'Enable Fines',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'enable_contribution_member_list',
            'label' => 'Enable Contribution Member List',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'disable_overpayments',
            'label' => 'Disable Overpayments',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'enable_contribution_summary_display_configuration',
            'label' => 'Enable contribution summary display configuration',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'display_contribution_arrears_cumulatively',
            'label' => 'Enable display of contribution arrears as a cumulative',
            'rules' => 'trim|numeric',
        )
    );

    function manage_group_contributions(){
    	/*
    		Action Type
			1 - Delete
			2 - edit
			3 - create
			4 - open
			5 - close
			6 - Hide
			7 - unhide
		*/
    	$file = file_get_contents('php://input');
    	$response = array();
    	$request = array();
    	header('Content-Type: application/json');
    	$time = time();
    	if($file){
    		$result = json_decode($file);
    		$request = $result;
    		if($result){
    			$action_type = isset($result->action_type)?$result->action_type:0;
    			$user_id = isset($result->current_user_id)?$result->current_user_id:0;
    			$group_id = isset($result->group_id)?$result->group_id:0;
    			if($action_type&&$user_id&&$group_id){
    				$user = $this->ion_auth->get_user($user_id);
    				if($user){
    					$group = $this->groups_m->get($group_id);
	    				if($group){
	    					$member = $this->members_m->get_group_member_by_user_id($group->id,$user->id);
	    					if($member){
	    						foreach ($result as $key => $value) {
	    							if(is_array($value) || is_object($value)){
	    								$data = array();
	    								$fine_settings= array();
	    								foreach ($value as $value_key => $value_value) {
	    									if(isset($value_value->member_id)){
	    										$data[$value_key] = $value_value->member_id;
	    									}else{
	    										foreach ($value_value as $new_key => $new_value) {
	    											if(array_key_exists($new_key, $fine_settings)){
	    												$fine_settings[$new_key] = $fine_settings[$new_key]+array( $value_key=> $new_value);
	    											}else{
	    												$fine_settings[$new_key] = array( $value_key=> $new_value);
	    											}
	    										}
	    									}
	    								}
	    								$_POST[$key] = $data;
	    								$_POST = $_POST+$fine_settings;
	    							}else{
	    								$_POST[$key] = $value;
	    							}
	    							
	    						}
	    						$this->sms_template_default = $this->contribution_invoices->sms_template_default;
	    						$_POST['sms_template'] = $this->sms_template_default;
	    						if($action_type==1){//delete contribution
	    							$contribution_id = isset($result->contribution_id)?trim($result->contribution_id):0;
	    							$password = isset($result->password)?trim($result->password):'';
	    							if($contribution_id){
	    								$contribution = $this->contributions_m->get_group_contribution($contribution_id,$group->id);
	    								if($contribution){
	    									if($this->ion_auth->login($user->phone?:$user->email,$password)){
	    										if($this->transaction_statements_m->check_if_contribution_has_transactions($contribution_id,$group->id)||$this->statements_m->check_if_contribution_has_transactions($contribution_id,$group->id)){
									                    $response = array(
										    				'status' => 0,
										    				'time' => time(),
										    				'error' => 'The contribution has transactions associated to it, void all transactions associated to this account before deleting it',
										    				'warning' => '',
										    			);
									                }else{
									                    if($this->contributions_m->safe_delete($contribution_id,$group->id)){
									                        $response = array(
											    				'status' => 1,
											    				'time' => time(),
											    				'error' => '',
											    				'success' => 'Successfully deleted',
											    			);
									                    }else{
									                        $response = array(
											    				'status' => 0,
											    				'time' => time(),
											    				'error' => 'Could not delete contribution',
											    			);
									                    }
									                }
	    									}else{
	    										$response = array(
								    				'status' => 0,
								    				'time' => time(),
								    				'error' => 'Authentication failed',
								    			);
	    									}
	    								}else{
	    									$response = array(
							    				'status' => 0,
							    				'time' => time(),
							    				'error' => 'Could not find group contribution',
							    			);
	    								}
	    							}else{
	    									$response = array(
							    				'status' => 0,
							    				'time' => time(),
							    				'error' => 'Contribution ID is missing',
							    			);
	    							}
			    				}else if($action_type==2){//edit group contribution
			    					$contribution_id = $result->contribution_id;
			    					if($contribution_id){
			    						$contribution = $this->contributions_m->get_group_contribution($contribution_id,$group->id);
			    						if($contribution){
			    							$this->_conditional_validation_rules();
			    							$this->form_validation->set_rules($this->validation_rules);
			    							if($contribution->type==1){
									            $regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($contribution_id,$group->id);
									            $contribution = (object) array_merge((array) $regular_contribution_setting, (array) $contribution);
									        }else if($contribution->type==2){
									            $one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($contribution_id,$group->id);
									            $contribution = (object) array_merge((array) $one_time_contribution_setting, (array) $contribution);
									        }else{

									        }
									        $post = new StdClass();
									        foreach ($this->validation_rules as $key => $field) {
									            if(isset($post->$field['field'])){
									                //do nothing for now
									            }else{
									                $post->$field['field'] = set_value($field['field']);
									            }
									        }
									        $posts = $_POST;
									        $fine_entries_are_valid = TRUE;

									        if($this->input->post('enable_fines')){
									            if(isset($posts['fine_type'])){ 
									                $count = 0; foreach($posts['fine_type'] as $fine_type):
									                    if($fine_type){
									                        $fine_limit = isset($posts['fine_limit'][$count])?$posts['fine_limit'][$count]:0;
									                        if($fine_type==1){
									                            if($posts['fixed_amount'][$count]&&$posts['fixed_fine_mode'][$count]&&$posts['fixed_fine_chargeable_on'][$count]){
									                                if(is_numeric(currency($posts['fixed_amount'][$count]))&&
									                                    is_numeric($posts['fixed_fine_mode'][$count])
									                                    &&is_numeric($posts['fixed_fine_frequency'][$count])
									                                    &&is_numeric($fine_limit)){
									                                    //do for nothing now
									                                }else{
									                                    $fine_entries_are_valid = FALSE;
									                                }
									                            }else{
									                                $fine_entries_are_valid = FALSE;
									                            }
									                        }else if($fine_type==2){
									                            $percentage_fine_frequency = isset($posts['percentage_fine_frequency'][$count])?$posts['percentage_fine_frequency'][$count]:0;
									                            if($posts['percentage_rate'][$count]
									                                &&$posts['percentage_fine_on'][$count]
									                                &&$posts['percentage_fine_chargeable_on'][$count]
									                                &&$posts['percentage_fine_mode'][$count]){
									                                if(is_numeric($posts['percentage_rate'][$count])
									                                    &&is_numeric($posts['percentage_fine_on'][$count])
									                                    &&is_numeric($posts['percentage_fine_mode'][$count])
									                                    &&is_numeric($fine_limit)
									                                    &&is_numeric($percentage_fine_frequency)){
									                                    //do for nothing now
									                                }else{
									                                    $fine_entries_are_valid = FALSE;
									                                }
									                            }else{
									                                $fine_entries_are_valid = FALSE;
									                            }
									                        }else{
									                            $fine_entries_are_valid = FALSE;
									                        }
									                    }else{
									                        $fine_entries_are_valid = FALSE;
									                    }
									                    $count++;
									                endforeach;
									            }
									        }

									        if($this->form_validation->run()&&$fine_entries_are_valid){
									        	$input = array(
									                'name' => $this->input->post('name'),
									                'amount' => $this->input->post('amount'),
									                'type' => $this->input->post('type'),
									                'regular_invoicing_active' => $this->input->post('regular_invoicing_active')?1:0,
									                'one_time_invoicing_active' => $this->input->post('one_time_invoicing_active')?1:0,
									                'enable_contribution_summary_display_configuration' => $this->input->post('enable_contribution_summary_display_configuration')?1:0,
									                'display_contribution_arrears_cumulatively' => $this->input->post('display_contribution_arrears_cumulatively')?1:0,
									                'active' => 1,
									                'modified_by' => $user->id,
									                'modified_on' => time(),
									            );
									        	if($result = $this->contributions_m->update($contribution_id,$input)){
									                if($this->input->post('type')==1){
									                    $contribution_date = $this->_contribution_date();
									                    
								                    	$second_contribution_date = $this->_second_contribution_date();
									                    $invoice_date = $contribution_date - (24*60*60*$this->input->post('invoice_days'));
									                    $regular_contribution_settings_input = array(
									                        'contribution_id'=>$contribution_id,
									                        'group_id'=>$group->id,
									                        'invoice_date'=>$invoice_date,
									                        'contribution_date'=>$contribution_date,
									                        'contribution_frequency'=>$this->input->post('contribution_frequency'),
									                        'invoice_days'=>$this->input->post('invoice_days'),
									                        'sms_notifications_enabled'=>$this->input->post('sms_notifications_enabled')?1:0,
									                        'email_notifications_enabled'=>$this->input->post('email_notifications_enabled')?1:0,
									                        'sms_template'=>$this->sms_template_default,
									                        'month_day_monthly'=>$this->input->post('month_day_monthly'),
									                        'week_day_monthly'=>$this->input->post('week_day_monthly'),
									                        'week_day_weekly'=>$this->input->post('week_day_weekly'),
									                        'week_day_fortnight'=>$this->input->post('week_day_fortnight'),
									                        'week_number_fortnight'=>$this->input->post('week_number_fortnight'),
									                        'month_day_multiple'=>$this->input->post('month_day_multiple'),
									                        'week_day_multiple'=>$this->input->post('week_day_multiple'),
									                        'start_month_multiple'=>$this->input->post('start_month_multiple'),
									                        'disable_overpayments'=>$this->input->post('disable_overpayments'),
									                        'enable_fines'=>$this->input->post('enable_fines'),
									                        'enable_contribution_member_list'=>$this->input->post('enable_contribution_member_list')?1:0,
									                        'after_second_contribution_date'=>$second_contribution_date,
															'after_first_contribution_day_option'=>$this->input->post('after_first_contribution_day_option'),
							                            	'after_first_day_week_multiple'=>$this->input->post('after_first_day_week_multiple'),
							                            	'after_first_starting_day'=>$this->input->post('after_first_starting_day'),
							                            	'after_second_contribution_day_option'=>$this->input->post('after_second_contribution_day_option'),
							                            	'after_second_day_week_multiple'=>$this->input->post('after_second_day_week_multiple'),
							                            	'after_second_starting_day'=>$this->input->post('after_second_starting_day'),//end
									                        'active'=>1,
									                        'modified_by'=>$user->id,
									                        'modified_on'=>time(),
									                    );
									                    if($regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($contribution_id,$group->id)){
									                        if($result = $this->contributions_m->update_regular_contribution_setting($regular_contribution_setting->id,$regular_contribution_settings_input)){
									                            //do nothing for now
									                        }else{
									                        	$response = array(
													    				'status' => 0,
													    				'time' => time(),
													    				'error' => 'Could not save changes to regular contribution setting',
													    			);
									                        }
									                    }else{
									                        if($regular_contribution_setting_id = $this->contributions_m->insert_regular_contribution_setting($regular_contribution_settings_input)){
									                            //do nothing for now
									                        }else{
									                            $response = array(
													    				'status' => 0,
													    				'time' => time(),
													    				'error' => 'Could create regular contribution setting',
													    			);
									                        }
									                    }
									                }else if($this->input->post('type')==2){
									                    $invoice_date = strtotime($this->input->post('invoice_date'));
									                    $contribution_date = strtotime($this->input->post('contribution_date'));
									                    $one_time_contribution_settings_input = array(
									                        'contribution_id'=>$contribution_id,
									                        'group_id'=>$group->id,
									                        'invoice_date'=>$invoice_date,
									                        'contribution_date'=>$contribution_date,
									                        'disable_overpayments'=>$this->input->post('disable_overpayments'),
									                        'sms_notifications_enabled'=>$this->input->post('sms_notifications_enabled')?1:0,
									                        'email_notifications_enabled'=>$this->input->post('email_notifications_enabled')?1:0,
									                        'sms_template'=>$this->sms_template_default,
									                        'enable_fines'=>$this->input->post('enable_fines'),
									                        'enable_contribution_member_list'=>$this->input->post('enable_contribution_member_list')?1:0,
									                        'active'=>1,
									                        'invoices_queued'=>0,
									                        'modified_by'=>$user->id,
									                        'modified_on'=>time(),
									                    );
									                    if($one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($contribution_id,$group->id)){
									                        if($result = $this->contributions_m->update_one_time_contribution_setting($one_time_contribution_setting->id,$one_time_contribution_settings_input)){
									                            //do nothing for now
									                        }else{
									                        	 $response = array(
													    				'status' => 0,
													    				'time' => time(),
													    				'error' => 'Could not save changes to one time contribution setting',
													    			);
									                        }
									                    }else{
									                        if($one_time_contribution_setting_id = $this->contributions_m->insert_one_time_contribution_setting($one_time_contribution_settings_input)){
									                            //do nothing for now
									                        }else{
									                            $response = array(
													    				'status' => 0,
													    				'time' => time(),
													    				'error' => 'Could create one time contribution setting',
													    			);
									                        }
									                    }
									                }

									                if($this->input->post('type')==1||$this->input->post('type')==2){
									                    if($this->input->post('enable_contribution_member_list')){
									                        $this->contributions_m->delete_contribution_member_pairings($contribution_id,$group->id);
									                        $group_member_ids = $this->input->post('contribution_member_list');
									                        foreach($group_member_ids as $member_id){
									                            $input = array(
									                                'member_id'=>$member_id,
									                                'group_id'=>$group->id,
									                                'contribution_id'=>$contribution_id,
									                                'created_on'=>time(),
									                                'created_by'=>$user->id,
									                            );
									                            if($contribution_member_pairing_id = $this->contributions_m->insert_contribution_member_pairing($input)){

									                            }else{
									                            	$response = array(
													    				'status' => 0,
													    				'time' => time(),
													    				'error' => 'Could not insert contribution member pairing',
													    			);
									                            }
									                        }
									                    }

									                    if($this->input->post('enable_fines')){
									                        $this->contributions_m->delete_contribution_fine_settings($contribution_id,$group->id);
									                        if(isset($posts['fine_type'])){ 
									                            $count = 0; foreach($posts['fine_type'] as $fine_type):
									                                if($fine_type){
									                                    $fine_date = $this->_fine_date($contribution_date,$fine_type,$posts['fixed_fine_chargeable_on'][$count],$posts['percentage_fine_chargeable_on'][$count]);
									                                    $input = array(
									                                        'contribution_id'=>$contribution_id,
									                                        'group_id'=>$group->id,
									                                        'fine_type'=>$fine_type,
									                                        'fixed_amount'=>currency($posts['fixed_amount'][$count]),
									                                        'fixed_fine_mode'=>$posts['fixed_fine_mode'][$count],
									                                        'fixed_fine_chargeable_on'=>$posts['fixed_fine_chargeable_on'][$count],
									                                        'fixed_fine_frequency'=>isset($posts['fixed_fine_frequency'][$count])?$posts['fixed_fine_frequency'][$count]:0,
									                                        'percentage_rate'=>$posts['percentage_rate'][$count],
									                                        'percentage_fine_on'=>$posts['percentage_fine_on'][$count],
									                                        'percentage_fine_chargeable_on'=>$posts['percentage_fine_chargeable_on'][$count],
									                                        'percentage_fine_mode'=>$posts['percentage_fine_mode'][$count],
									                                        'percentage_fine_frequency'=>isset($posts['percentage_fine_frequency'][$count])?$posts['percentage_fine_frequency'][$count]:0,
									                                        'fine_limit'=>$posts['fine_limit'][$count],
									                                        'fine_date'=>isset($posts['fine_date'][$count])?(($posts['fine_date'][$count]>=strtotime('today'))?$posts['fine_date'][$count]:$fine_date):$fine_date,
									                                        //'fine_date'=>$fine_date,
									                                        'active'=>1,
									                                        'fine_sms_notifications_enabled'=>isset($posts['fine_sms_notifications_enabled'][$count])?1:0,
									                                        'fine_email_notifications_enabled'=>isset($posts['fine_email_notifications_enabled'][$count])?1:0,
									                                        'created_on'=>time(),
									                                        'created_by'=>$user->id
									                                    );
									                                    if($contrbution_fine_setting_id = $this->contributions_m->insert_contribution_fine_setting($input)){
									                                        //do nothing for now
									                                    }else{
									                                        $this->session->set_flashdata('error','Could not insert contribution fine setting');
									                                    }
									                                }
									                                $count++;
									                            endforeach;
									                        }
									                    }
									                    if($this->input->post('type')==1||$this->input->post('type')==2){
									                        $this->contributions_m->delete_contribution_fine_settings($contribution_id,$group->id);

										                    if($this->input->post('enable_fines')){
										                        if(isset($posts['fine_type'])){ 
										                            $count = 0; foreach($posts['fine_type'] as $fine_type):
										                                if($fine_type){
										                                    $input = array(
										                                        'contribution_id'=>$contribution_id,
										                                        'group_id'=>$group->id,
										                                        'fine_type'=>$fine_type,
										                                        'fixed_amount'=>currency($posts['fixed_amount'][$count]),
										                                        'fixed_fine_mode'=>$posts['fixed_fine_mode'][$count],
										                                        'fixed_fine_chargeable_on'=>$posts['fixed_fine_chargeable_on'][$count],
										                                        'fixed_fine_frequency'=>isset($posts['fixed_fine_frequency'][$count])?$posts['fixed_fine_frequency'][$count]:0,
										                                        'percentage_rate'=>$posts['percentage_rate'][$count],
										                                        'percentage_fine_on'=>$posts['percentage_fine_on'][$count],
										                                        'percentage_fine_chargeable_on'=>$posts['percentage_fine_chargeable_on'][$count],
										                                        'percentage_fine_mode'=>$posts['percentage_fine_mode'][$count],
										                                        'percentage_fine_frequency'=>isset($posts['percentage_fine_frequency'][$count])?$posts['percentage_fine_frequency'][$count]:0,
										                                        'fine_limit'=>$posts['fine_limit'][$count],
										                                        'fine_date'=>$this->_fine_date($contribution_date,$fine_type,$posts['fixed_fine_chargeable_on'][$count],$posts['percentage_fine_chargeable_on'][$count]),
										                                        'active'=>1,
										                                        'fine_sms_notifications_enabled'=>isset($posts['fine_sms_notifications_enabled'][$count])?1:0,
										                                        'fine_email_notifications_enabled'=>isset($posts['fine_email_notifications_enabled'][$count])?1:0,
										                                        'created_on'=>time(),
										                                        'created_by'=>$user->id
										                                    );
										                                    if($contrbution_fine_setting_id = $this->contributions_m->insert_contribution_fine_setting($input)){
										                                        //do nothing for now
										                                    }else{
										                                    	$response = array(
																	    				'status' => 0,
																	    				'time' => time(),
																	    				'error' => 'Could not insert contribution fine setting',
																	    			);
										                                    }
										                                }
										                                $count++;
										                            endforeach;
										                        }
										                    }
									                	}
									                	$response = array(
											    				'status' => 1,
											    				'time' => time(),
											    				'success' => 'Successfully edited',
											    				'contribution_id' => $contribution_id,
											    			);
									                }
									            }else{
									            	 $response = array(
											    				'status' => 1,
											    				'time' => time(),
											    				'error' => 'Changes could not be saved.',
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
									    				'error' => 'Form validation failed',
									    				'validation_errors' => $post,
									    			);
									        }
			    						}else{
			    							$response = array(
								    				'status' => 0,
								    				'time' => time(),
								    				'error' => 'Contribution does not exist',
								    				'contribution_id' => $contribution_id,
								    			);
			    						}
			    					}else{
			    						$response = array(
								    				'status' => 0,
								    				'time' => time(),
								    				'error' => 'Contribution Id is missing',
								    			);
			    					}
			    				}else if($action_type==3){//create contribution
		    						if(isset($result->enable_fines)){
		    							$fine_entries_are_valid = TRUE;
		    							$posts = $_POST;
		    							$this->_conditional_validation_rules();
			    						if($result->enable_fines){
								            if(isset($posts['fine_type'])){ 
								                $count = 0; foreach($posts['fine_type'] as $fine_type):
								                    if($fine_type){
								                        if($fine_type==1){
								                            if($posts['fixed_amount'][$count]&&$posts['fixed_fine_mode'][$count]&&$posts['fixed_fine_chargeable_on'][$count]){
								                                if(is_numeric(currency($posts['fixed_amount'][$count]))&&is_numeric($posts['fixed_fine_mode'][$count])&&is_numeric($posts['fixed_fine_frequency'][$count])&&is_numeric($posts['fine_limit'][$count])){
								                                    //do for nothing now
								                                }else{
								                                    $fine_entries_are_valid = FALSE;
								                                }
								                            }else{
								                                $fine_entries_are_valid = FALSE;
								                            }
								                        }else if($fine_type==2){
								                            if($posts['percentage_rate'][$count]&&$posts['percentage_fine_on'][$count]&&$posts['percentage_fine_chargeable_on'][$count]&&$posts['percentage_fine_mode'][$count]){
								                                if(is_numeric($posts['percentage_rate'][$count])&&is_numeric($posts['percentage_fine_on'][$count])&&is_numeric($posts['percentage_fine_mode'][$count])&&is_numeric($posts['fine_limit'][$count])&&is_numeric($posts['percentage_fine_frequency'][$count])){
								                                    //do for nothing now
								                                }else{
								                                    $fine_entries_are_valid = FALSE;
								                                }
								                            }else{
								                                $fine_entries_are_valid = FALSE;
								                            }
								                        }else{
								                            $fine_entries_are_valid = FALSE;
								                        }
								                    }else{
								                        $fine_entries_are_valid = FALSE;
								                    }
								                    $count++;
								                endforeach;
								            }
								        }
								        $this->form_validation->set_rules($this->validation_rules);
			    						if($this->form_validation->run()&&$fine_entries_are_valid){
			    							$input = array(
								                'name' => $this->input->post('name'),
								                'amount' => $this->input->post('amount'),
								                'type' => $this->input->post('type'),
								                'regular_invoicing_active' => $this->input->post('regular_invoicing_active')?1:0,
								                'one_time_invoicing_active' => $this->input->post('one_time_invoicing_active')?1:0,
								                'enable_contribution_summary_display_configuration' => $this->input->post('enable_contribution_summary_display_configuration')?1:0,
								                'display_contribution_arrears_cumulatively' => $this->input->post('display_contribution_arrears_cumulatively')?1:0,
								                'active' => 1,
								                'group_id' => $group->id,
								                'is_hidden' => 0,
								                'created_by' => $user->id,
								                'created_on' => time(),
								            );
								            if($contribution_id = $this->contributions_m->insert($input)){
								                if($this->input->post('type')==1){
								                    $contribution_date = $this->_contribution_date();
								                    $second_contribution_date = $this->_second_contribution_date();
								                    $invoice_date = $contribution_date - (24*60*60*$this->input->post('invoice_days'));
								                    $regular_contribution_settings_input = array(
								                        'contribution_id'=>$contribution_id,
								                        'group_id'=>$group->id,
								                        'invoice_date'=>$invoice_date,
								                        'contribution_date'=>$contribution_date,
								                        'contribution_frequency'=>$this->input->post('contribution_frequency'),
								                        'invoice_days'=>$this->input->post('invoice_days'),
								                        'sms_notifications_enabled'=>$this->input->post('sms_notifications_enabled')?1:0,
								                        'email_notifications_enabled'=>$this->input->post('email_notifications_enabled')?1:0,
								                        'sms_template'=>$this->sms_template_default,
								                        'month_day_monthly'=>$this->input->post('month_day_monthly'),
								                        'week_day_monthly'=>$this->input->post('week_day_monthly'),
								                        'week_day_weekly'=>$this->input->post('week_day_weekly'),
								                        'week_day_fortnight'=>$this->input->post('week_day_fortnight'),
								                        'week_number_fortnight'=>$this->input->post('week_number_fortnight'),
								                        'month_day_multiple'=>$this->input->post('month_day_multiple'),
								                        'week_day_multiple'=>$this->input->post('week_day_multiple'),
								                        'start_month_multiple'=>$this->input->post('start_month_multiple'),
								                        'disable_overpayments'=>$this->input->post('disable_overpayments'),
								                        'enable_fines'=>$this->input->post('enable_fines'),
								                        'enable_contribution_member_list'=>$this->input->post('enable_contribution_member_list')?1:0,
								                        'active'=>1,
								                        'after_second_contribution_date'=>$second_contribution_date,
														'after_first_contribution_day_option'=>$this->input->post('after_first_contribution_day_option'),
							                            'after_first_day_week_multiple'=>$this->input->post('after_first_day_week_multiple'),
							                            'after_first_starting_day'=>$this->input->post('after_first_starting_day'),
							                            'after_second_contribution_day_option'=>$this->input->post('after_second_contribution_day_option'),
							                            'after_second_day_week_multiple'=>$this->input->post('after_second_day_week_multiple'),
							                            'after_second_starting_day'=>$this->input->post('after_second_starting_day'),//end
								                        'created_by'=>$user->id,
								                        'created_on'=>time(),
								                    );
								                    if($regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($contribution_id,$group->id)){
								                        if($result = $this->contributions_m->update_regular_contribution_setting($regular_contribution_setting->id,$regular_contribution_settings_input)){
								                            //do nothing for now
								                        }else{
								                            $response = array(
												    				'status' => 0,
												    				'time' => time(),
												    				'error' => 'Could not add regular contribution settting',
												    			);
								                        }
								                    }else{
								                        if($regular_contribution_setting_id = $this->contributions_m->insert_regular_contribution_setting($regular_contribution_settings_input)){
								                            //do nothing for now
								                        }else{
								                        	$response = array(
												    				'status' => 0,
												    				'time' => time(),
												    				'error' => 'Could create regular contribution setting',
												    			);
								                        }
								                    }
								                }else if($this->input->post('type')==2){
								                    $invoice_date = strtotime($this->input->post('invoice_date'));
								                    $contribution_date = strtotime($this->input->post('contribution_date'));
								                    $one_time_contribution_settings_input = array(
								                        'contribution_id'=>$contribution_id,
								                        'group_id'=>$group->id,
								                        'invoice_date'=>$invoice_date,
								                        'contribution_date'=>$contribution_date,
								                        'disable_overpayments'=>$this->input->post('disable_overpayments'),
								                        'sms_notifications_enabled'=>$this->input->post('sms_notifications_enabled')?1:0,
								                        'email_notifications_enabled'=>$this->input->post('email_notifications_enabled')?1:0,
								                        'sms_template'=>$this->input->post('sms_template'),
								                        'enable_fines'=>$this->input->post('enable_fines'),
								                        'enable_contribution_member_list'=>$this->input->post('enable_contribution_member_list')?1:0,
								                        'active'=>1,
								                        'invoices_queued'=>0,
								                        'created_by'=>$user->id,
								                        'created_on'=>time(),
								                    );
								                    if($one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($contribution_id,$group->id)){
								                        if($result = $this->contributions_m->update_one_time_contribution_setting($one_time_contribution_setting->id,$one_time_contribution_settings_input)){
								                            //do nothing for now
								                        }else{
								                        	$response = array(
												    				'status' => 0,
												    				'time' => time(),
												    				'error' => 'Could not save changes to one time contribution setting',
												    			);
								                        }
								                    }else{
								                        if($one_time_contribution_setting_id = $this->contributions_m->insert_one_time_contribution_setting($one_time_contribution_settings_input)){
								                            //do nothing for now
								                        }else{
								                        	$response = array(
												    				'status' => 0,
												    				'time' => time(),
												    				'error' => 'Could create one time contribution setting',
												    			);
								                        }
								                    }
								                }

								                if($this->input->post('type')==1||$this->input->post('type')==2){
								                    if($this->input->post('enable_fines')){
								                        if(isset($posts['fine_type'])){ 
								                            $count = 0; foreach($posts['fine_type'] as $fine_type):
								                                if($fine_type){
								                                    $input = array(
								                                        'contribution_id'=>$contribution_id,
								                                        'group_id'=>$group->id,
								                                        'fine_type'=>$fine_type,
								                                        'fixed_amount'=>currency($posts['fixed_amount'][$count]),
								                                        'fixed_fine_mode'=>$posts['fixed_fine_mode'][$count],
								                                        'fixed_fine_chargeable_on'=>$posts['fixed_fine_chargeable_on'][$count],
								                                        'fixed_fine_frequency'=>isset($posts['fixed_fine_frequency'][$count])?$posts['fixed_fine_frequency'][$count]:0,
								                                        'percentage_rate'=>$posts['percentage_rate'][$count],
								                                        'percentage_fine_on'=>$posts['percentage_fine_on'][$count],
								                                        'percentage_fine_chargeable_on'=>$posts['percentage_fine_chargeable_on'][$count],
								                                        'percentage_fine_mode'=>$posts['percentage_fine_mode'][$count],
								                                        'percentage_fine_frequency'=>isset($posts['percentage_fine_frequency'][$count])?$posts['percentage_fine_frequency'][$count]:0,
								                                        'fine_limit'=>$posts['fine_limit'][$count],
								                                        'fine_date'=>$this->_fine_date($contribution_date,$fine_type,$posts['fixed_fine_chargeable_on'][$count],$posts['percentage_fine_chargeable_on'][$count]),
								                                        'active'=>1,
								                                        'fine_sms_notifications_enabled'=>isset($posts['fine_sms_notifications_enabled'][$count])?1:0,
								                                        'fine_email_notifications_enabled'=>isset($posts['fine_email_notifications_enabled'][$count])?1:0,
								                                        'created_on'=>time(),
								                                        'created_by'=>$user->id
								                                    );
								                                    if($contrbution_fine_setting_id = $this->contributions_m->insert_contribution_fine_setting($input)){
								                                        //do nothing for now
								                                    }else{
								                                    	$response = array(
															    				'status' => 0,
															    				'time' => time(),
															    				'error' => 'Could not insert contribution fine setting',
															    			);
								                                    }
								                                }
								                                $count++;
								                            endforeach;
								                        }
								                    }

								                    if($this->input->post('enable_contribution_member_list')){
								                        $group_member_ids = $this->input->post('contribution_member_list');
								                        foreach($group_member_ids as $member_id){
								                            $input = array(
								                                'member_id'=>$member_id,
								                                'group_id'=>$group->id,
								                                'contribution_id'=>$contribution_id,
								                                'created_on'=>time(),
								                                'created_by'=>$user->id,
								                            );
								                            if($contribution_member_pairing_id = $this->contributions_m->insert_contribution_member_pairing($input)){

								                            }else{
								                            	$response = array(
															    				'status' => 0,
															    				'time' => time(),
															    				'error' => 'Could not insert contribution member pairing',
															    			);
								                            }
								                        }
								                    }
								                }
								                $this->setup_tasks_tracker->set_completion_status('create-contribution',$group->id,$user->id);
								                $response = array(
										    				'status' => 1,
										    				'time' => time(),
										    				'error' => '',
										    				'success' => 'Successfully created contribution',
										    				'contribution_id' => $contribution_id,
										    			);
								            }else{
								                $response = array(
										    				'status' => 1,
										    				'time' => time(),
										    				'error' => 'Contribution could not be created.',
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
								    				'error' => 'Form validation failed',
								    				'validation_errors' => $post,
								    			);
			    						}
		    						}else{
		    							$response = array(
								    				'status' => 0,
								    				'time' => time(),
								    				'error' => 'Some parameters were not sent',
								    			);
		    						}
			    				}else if($action_type==4){//open

			    				}else if($action_type==5){//close

			    				}else if($action_type==6){//Hide
	    							$contribution_id = isset($result->contribution_id)?trim($result->contribution_id):0;
	    							if($contribution_id){
	    								$contribution = $this->contributions_m->get_group_contribution($contribution_id,$group->id);
	    								if($contribution){
	    									if($contribution->is_hidden){
									            $response = array(
								    				'status' => 0,
								    				'time' => time(),
								    				'error' => 'Sorry, the Contribution is already hidden',
								    			);
									        }else{
									        	 $input = array(
										            'is_hidden'=>1,
										            'modified_by'=>$user->id,
										            'modified_on'=>time(),
										        );
										        if($result = $this->contributions_m->update($contribution_id,$input)){
										            $response = array(
									    				'status' => 1,
									    				'time' => time(),
									    				'error' => '',
									    			);
										        }else{
										            $response = array(
									    				'status' => 0,
									    				'time' => time(),
									    				'error' => 'Unable to hide contribution',
									    			);
										        }
									        }
	    								}else{
	    									$response = array(
							    				'status' => 0,
							    				'time' => time(),
							    				'error' => 'Could not find group contribution',
							    			);
	    								}
	    							}else{
	    									$response = array(
							    				'status' => 0,
							    				'time' => time(),
							    				'error' => 'Contribution ID is missing',
							    			);
	    							}
			    				}else if($action_type==7){//Unhide
			    					$contribution_id = isset($result->contribution_id)?trim($result->contribution_id):0;
	    							if($contribution_id){
	    								$contribution = $this->contributions_m->get_group_contribution($contribution_id,$group->id);
	    								if($contribution){
	    									if(!$contribution->is_hidden){
									            $response = array(
								    				'status' => 0,
								    				'time' => time(),
								    				'error' => 'Sorry, the Contribution is already visible',
								    			);
									        }else{
									        	 $input = array(
										            'is_hidden'=>'',
										            'modified_by'=>$user->id,
										            'modified_on'=>time(),
										        );
										        if($result = $this->contributions_m->update($contribution_id,$input)){
										            $response = array(
									    				'status' => 1,
									    				'time' => time(),
									    				'error' => '',
									    			);
										        }else{
										            $response = array(
									    				'status' => 0,
									    				'time' => time(),
									    				'error' => 'Unable to unhide contribution',
									    			);
										        }
									        }
	    								}else{
	    									$response = array(
							    				'status' => 0,
							    				'time' => time(),
							    				'error' => 'Could not find group contribution',
							    			);
	    								}
	    							}else{
	    									$response = array(
							    				'status' => 0,
							    				'time' => time(),
							    				'error' => 'Contribution ID is missing',
							    			);
	    							}
			    				}else{
			    					$response = array(
						    				'status' => 0,
						    				'time' => time(),
						    				'error' => 'Action type supplied is invalid',
						    			);
			    				}
	    					}else{
	    						$response = array(
					    				'status' => 0,
					    				'time' => time(),
					    				'error' => 'Member details unavailable',
					    			);
	    					}
	    				}else{
	    					$response = array(
				    				'status' => 0,
				    				'time' => time(),
				    				'error' => 'Group details unavailable',
				    			);
	    				}
    				}else{
    					$response = array(
				    				'status' => 0,
				    				'time' => time(),
				    				'error' => 'Member details unavailable',
				    			);
    				}
    			}else{
    				$response = array(
		    				'status' => 0,
		    				'time' => time(),
		    				'error' => 'Essential parameters missing',
		    			);
    			}
    		}else{
    			$response = array(
	    				'status' => 0,
	    				'time' => time(),
	    				'error' => 'File sent has the wrong format',
	    			);
    		}
    	}else{
    		$response = array(
    				'status' => 0,
    				'time' => time(),
    				'error' => 'No file sent',
    			);
    	}

    	echo encrypt_json_encode(array('response'=>$response,'request'=>$request));
    }


    /**************Contribution Rules****************/

    function check_template_placeholders(){

        preg_match_all("/\[[^\]]*\]/", $this->sms_template_default,$default_placeholders);
        preg_match_all("/\[[^\]]*\]/", $this->input->post('sms_template'),$placeholders);
        $valid_template = TRUE;
        $missing_placeholders = array();
        foreach($default_placeholders[0] as $placeholder){
            if(in_array($placeholder,$placeholders[0])){

            }else{
                $valid_template = FALSE;
                $missing_placeholders[] = $placeholder;
            }
        }

        if($valid_template){
            return TRUE;
        }else{
            $count = count($missing_placeholders);
            $i=1;
            foreach($missing_placeholders as $placeholder): 
                if($i==1){
                    $missing_placeholders_string = $placeholder;
                }else{
                    $missing_placeholders_string .= ','.$placeholder;
                }
                $i++;
            endforeach;
            if($count==1){
                $this->form_validation->set_message('check_template_placeholders', $missing_placeholders_string.' placeholder missing in SMS Template.');
            }else{
                $this->form_validation->set_message('check_template_placeholders', $missing_placeholders_string.' placeholders missing in SMS Template.');
            }
            return FALSE;
        }
    }

    function check_contribution_member_list(){
        $contribution_list_members = $this->input->post('contribution_member_list');
        $count = count($contribution_list_members);
        if($count>0){
            return TRUE;
        }else{
            $this->form_validation->set_message('check_contribution_member_list', 'At least one member should be selected under limit invoicing for this contribution to specific members');
            return FALSE;
        }
    }

    function _conditional_validation_rules(){
        if($this->input->post('regular_invoicing_active')){
            $this->validation_rules[] = array(
                'field' => 'contribution_frequency',
                'label' => 'How often do members contribute',
                'rules' => 'trim|numeric|required',
            );
            $this->validation_rules[] = array(
                'field' => 'invoice_days',
                'label' => 'When do want to send invoices to members',
                'rules' => 'trim|numeric|required',
            );

            if($this->input->post('contribution_frequency')==1){
                //monthly
                $this->validation_rules[] =
                     array(
                        'field' => 'month_day_monthly',
                        'label' => 'When do members contribute',
                        'rules' => 'trim|required|numeric'
                    );

                $this->validation_rules[] =
                     array(
                        'field' => 'week_day_monthly',
                        'label' => 'Week Day',
                        'rules' => 'trim|numeric'
                    );     
            }else if($this->input->post('contribution_frequency')==6){
               //once a week 
                $this->validation_rules[] =
                     array(
                        'field' => 'week_day_weekly',
                        'label' => 'When do members contribute',
                        'rules' => 'trim|required'
                    );
            }else if($this->input->post('contribution_frequency')==7){
                //once in two weeks
                $this->validation_rules[] =
                    array(
                        'field' => 'week_day_fortnight',
                        'label' => 'When do members contribute',
                        'rules' => 'trim|required'
                    );
                $this->validation_rules[] =
                    array(
                        'field' => 'week_number_fortnight',
                        'label' => 'When do members contribute Week Number',
                        'rules' => 'trim|required'
                    );
            }else if($this->input->post('contribution_frequency')==2||$this->input->post('contribution_frequency')==3||$this->input->post('contribution_frequency')==4||$this->input->post('contribution_frequency')==5){
                //multiple months
                 $this->validation_rules[] =
                     array(
                        'field' => 'month_day_multiple',
                        'label' => 'When do members contribute',
                        'rules' => 'trim|required|numeric'
                    );

                 $this->validation_rules[] =
                     array(
                        'field' => 'week_day_multiple',
                        'label' => 'Week Day',
                        'rules' => 'trim|numeric'
                    );

                $this->validation_rules[] =
                     array(
                        'field' => 'start_month_multiple',
                        'label' => 'Starting Month',
                        'rules' => 'trim|required|numeric'
                    ); 
            }else if($this->input->post('contribution_frequency')==8){

            }else if($this->input->post('contribution_frequency') ==9){
                $this->validation_rules[] = array(
                    'field' => 'after_first_day_week_multiple',
                    'label' => 'First Day of the Week',
                    'rules' => 'trim|required|numeric',
                );
                $this->validation_rules[] = array(
                    'field' => 'after_first_starting_day',
                    'label' => 'First Date  of  Contribution',
                    'rules' => 'trim|numeric|required',
                );
                $this->validation_rules[] =array(
                    'field' => 'after_second_contribution_day_option',
                    'label' => 'Second Contribution Day Option',
                    'rules' => 'trim|numeric|required',
                );
                $this->validation_rules[] = array(
                    'field' => 'after_second_day_week_multiple',
                    'label' => 'Second Day of the Week',
                    'rules' => 'trim|numeric|required',
                );
                $this->validation_rules[] = array(
                    'field' => 'after_second_starting_day',
                    'label' => 'The Second Date  of  Contribution',
                    'rules' => 'trim|numeric|required',
                );
                $this->validation_rules[] = array(
                    'field' => 'day_out_of_range',
                    'label' => 'Day out of range',
                    'rules' => 'callback_check_if_date_is_out_of_range',
                );
                $this->validation_rules[] = array(
                    'field' => 'second_day_out_of_range',
                    'label' => 'Second Day out of range',
                    'rules' => 'callback_check_if_second_date_is_out_of_range',
                );
            }

        }

        if($this->input->post('one_time_invoicing_active')){
            $this->validation_rules[] = array(
                'field' => 'invoice_date',
                'label' => 'Invoice Date',
                'rules' => 'trim|required|callback_check_if_invoice_date_is_less_than_contribution_date',
            );
            $this->validation_rules[] = array(
                'field' => 'contribution_date',
                'label' => 'Contribution Date',
                'rules' => 'trim|required',
            );
        }


        if($this->input->post('sms_notifications_enabled')){
            $this->validation_rules[] = array(
                'field' => 'sms_template',
                'label' => 'SMS Template',
                'rules' => 'trim|required|callback_check_template_placeholders',
            );

            $this->validation_rules[] = array(
                'field' => 'sms_template',
                'label' => 'SMS Template',
                'rules' => 'trim|required',
            );
        }

        if($this->input->post('enable_contribution_member_list')){
            $this->validation_rules[] = array(
                'field' => 'contribution_member_list',
                'label' => 'Contribution member list',
                'rules' => 'callback_check_contribution_member_list',
            );
        }
    }

    function _contribution_date(){
         return $contribution_date = $this->contribution_invoices->get_regular_contribution_contribution_date(
            $this->input->post('contribution_frequency'),
            $this->input->post('month_day_monthly'),
            $this->input->post('week_day_monthly'),
            $this->input->post('week_day_weekly'),
            $this->input->post('week_day_fortnight'),
            $this->input->post('week_number_fortnight'),
            $this->input->post('month_day_multiple'),
            $this->input->post('week_day_multiple'),
            $this->input->post('start_month_multiple'),
            $this->input->post('after_first_contribution_day_option'),
            $this->input->post('after_first_day_week_multiple'),
            $this->input->post('after_first_starting_day'),
            $this->input->post('after_second_contribution_day_option'),
            $this->input->post('after_second_day_week_multiple'),
            $this->input->post('after_second_starting_day')
        );         

         //print_r(date("F j, Y, g:i a",$contribution_date)); die();
    }

    function _second_contribution_date(){
        return $second_contribution_date = $this->contribution_invoices->get_second_regular_contribution_contribution_date(
            $this->input->post('contribution_frequency'),
            $this->input->post('month_day_monthly'),
            $this->input->post('week_day_monthly'),
            $this->input->post('week_day_weekly'),
            $this->input->post('week_day_fortnight'),
            $this->input->post('week_number_fortnight'),
            $this->input->post('month_day_multiple'),
            $this->input->post('week_day_multiple'),
            $this->input->post('start_month_multiple'),
            $this->input->post('after_first_contribution_day_option'),
            $this->input->post('after_first_day_week_multiple'),
            $this->input->post('after_first_starting_day'),
            $this->input->post('after_second_contribution_day_option'),
            $this->input->post('after_second_day_week_multiple'),
            $this->input->post('after_second_starting_day')    
           
        );
    }

    function _fine_date($contribution_date = 0,$fine_type = 0,$fixed_fine_chargeable_on = 0,$percentage_fine_chargeable_on = 0){
        return $this->contribution_invoices->get_contribution_fine_date($contribution_date,$fine_type,$fixed_fine_chargeable_on,$percentage_fine_chargeable_on,0,0,0,$contribution_date);
    }

    function check_if_invoice_date_is_less_than_contribution_date(){
        $invoice_date = strtotime($this->input->post('invoice_date'));
        $contribution_date = strtotime($this->input->post('contribution_date'));
        if($invoice_date<=$contribution_date){
            return TRUE;
        }else{
            $this->form_validation->set_message('check_if_invoice_date_is_less_than_contribution_date', 'The invoice date has to be earlier than the contribution date');
            return FALSE;
        }
    }


    function check_if_date_is_out_of_range(){
        $contribution_date = $this->_contribution_date();
        $lastday = date('Y-m-d', strtotime('last day of this month')); '<br>';
        $firstday = date('Y-m-d', strtotime('first day of this month')); 

        $startDate = strtotime($firstday);
        $endDate = strtotime($lastday);
       
       if($contribution_date >= $startDate && $contribution_date <= $endDate){
            return TRUE;
       }else{
            $this->form_validation->set_message('check_if_date_is_out_of_range',' First Contribution Date is Out Of Range');
           return FALSE;
       }
        
    }

    function check_if_second_date_is_out_of_range(){
        $contribution_date = $this->_contribution_date();
        $second_contribution_date = $this->_second_contribution_date();
        $lastday = date('Y-m-d', strtotime('last day of this month')); '<br>';
        $firstday = date('Y-m-d', strtotime('first day of this month')); 

        $startDate = strtotime($firstday);
        $endDate = strtotime($lastday);
       
       if($second_contribution_date >= $startDate && $second_contribution_date <= $endDate){
            return TRUE;
       }else{
            $this->form_validation->set_message('check_if_second_date_is_out_of_range','Second Contribution Date is Out Of Range');
           return FALSE;
       }
    }

    protected $loan_type_validation_rules = array(
			array(
				'field' => 'name',
				'label' => 'Loan Type Name',
				'rules' => 'required|trim'
			),array(
				'field' => 'minimum_loan_amount',
				'label' => 'Minimum Loan Amount',
				'rules' => 'required|trim|currency'
			),array(
				'field' => 'maximum_loan_amount',
				'label' => 'Maximum Loan Amount',
				'rules' => 'required|trim|currency|callback__is_greater_than_minimum_loan_amount'
			),
			array(
				'field' => 'interest_type',
				'label' => 'Loan Interest Type',
				'rules' => 'required|trim|currency'
			),
			array(
				'field' => 'interest_rate',
				'label' => 'Loan Interest Rate',
				'rules' => 'required|trim|currency'
			),array(
				'field' => 'loan_interest_rate_per',
				'label' => 'Loan Interest Rate Per',
				'rules' => 'required|trim|currency'
			),
			array(
				'field' => 'minimum_repayment_period',
				'label' => 'Minimum Repayment Period',
				'rules' => 'required|trim|numeric'
			),array(
				'field' => 'maximum_repayment_period',
				'label' => 'Maximum Repayment Period',
				'rules' => 'required|trim|numeric|callback__is_greater_than_minimum_repayment_period'
			),array(
				'field' => 'enable_loan_guarantors',
				'label' => 'Enable Loan Guarantors',
				'rules' => 'trim|numeric'
			),
			array(
				'field' => 'minimum_guarantors',
				'label' => 'Minimum Guarantors',
				'rules' => 'trim|numeric'
			),array(
				'field' => 'maximum_guarantors',
				'label' => 'Maximum Guarantors',
				'rules' => 'trim|numeric'
			),
			array(
				'field' => 'enable_loan_processing_fee',
				'label' => 'Enable Loan Processing Fee',
				'rules' => 'trim|numeric'
			),
			array(
	            'field' =>  'loan_processing_fee_type',
	            'label' =>  'Loan Processing Fee Type',
	            'rules' =>  'trim'
	        ),
	        array(
	            'field' =>  'loan_processing_fee_fixed_amount',
	            'label' =>  'Loan Processing Fee Fixed Amount',
	            'rules' =>  'trim'
	        ),
	        array(
	            'field' =>  'loan_processing_fee_percentage_rate',
	            'label' =>  'Loan Processing Fee Fixed Percentage Rate',
	            'rules' =>  'trim'
	        ),
	        array(
	            'field' =>  'loan_processing_fee_percentage_charged_on',
	            'label' =>  'Loan Processing Fee Fixed Percentage Charged On',
	            'rules' =>  'trim'
	        ),
	        array(
	            'field' => 'loan_interest_rate_per',
	            'label' => 'Loan Interest Rate Per',
	            'rules' => 'numeric|trim',
	        ),
	        array(
	            'field' => 'enable_loan_fines',
	            'label' => 'Enable Loan Fines',
	            'rules' => 'numeric|trim',
	        ),
	        array(
	            'field' => 'loan_fine_type',
	            'label' => 'Loan Fine Type',
	            'rules' => 'numeric|trim',
	        ),
	        array(
	            'field' => 'fixed_fine_amount',
	            'label' => 'Fixed Fine Amount',
	            'rules' => 'trim|currency',
	        ),
	        array(
	            'field' => 'fixed_amount_fine_frequency',
	            'label' => 'Fixed Fine Amount Frequency',
	            'rules' => 'numeric|trim',
	        ),
	        array(
	            'field' => 'fixed_amount_fine_frequency_on',
	            'label' => 'Fixed Fine Amount Frequency On',
	            'rules' => 'numeric|trim',
	        ),
	        array(
	            'field' => 'percentage_fine_rate',
	            'label' => 'Percentage Fine Rate',
	            'rules' => 'numeric|trim',
	        ),
	        array(
	            'field' => 'percentage_fine_frequency',
	            'label' => 'Percentage Fine Frequency',
	            'rules' => 'numeric|trim',
	        ),
	        array(
	            'field' => 'percentage_fine_on',
	            'label' => 'Percentage Fine On',
	            'rules' => 'numeric|trim',
	        ),
	        array(
	            'field' => 'one_off_fine_type',
	            'label' => 'One Off Fine Type',
	            'rules' => 'numeric|trim',
	        ),
	        array(
	            'field' => 'one_off_fixed_amount',
	            'label' => 'One Off Fixed Amount',
	            'rules' => 'trim|currency',
	        ),
	        array(
	        	'field' => 'one_off_percentage_rate',
	            'label' => 'One Off Percentage Rate',
	            'rules' => 'trim|numeric',
	        ),
	        array(
	        	'field' => 'one_off_percentage_rate_on',
	            'label' => 'One Off Percentage Rate On',
	            'rules' => 'trim|numeric',
	        ),
	        array(
	        	'field' => 'enable_outstanding_loan_balance_fines',
	            'label' => 'Enable Outstanding Loan Balance Fines',
	            'rules' => 'trim|numeric',
	        ),
	        array(
	        	'field' => 'outstanding_loan_balance_fine_type',
	            'label' => 'Outstanding Loan Balance Fine Type',
	            'rules' => 'trim|numeric',
	        ),
	        array(
	        	'field' => 'outstanding_loan_balance_fine_fixed_amount',
	            'label' => 'Outstanding Loan Balance Fine Fixed Amount',
	            'rules' => 'trim|currency',
	        ),
	        array(
	        	'field' => 'outstanding_loan_balance_fixed_fine_frequency',
	            'label' => 'Outstanding Loan Balance Fine Fixed Fine Frequency',
	            'rules' => 'trim|numeric',
	        ),
	        array(
	        	'field' => 'outstanding_loan_balance_percentage_fine_rate',
	            'label' => 'Outstanding Loan Balance Percentage Fine Rate',
	            'rules' => 'trim|currency',
	        ),
	        array(
	        	'field' => 'outstanding_loan_balance_percentage_fine_frequency',
	            'label' => 'Outstanding Loan Balance Percentage Fine Frequency',
	            'rules' => 'trim|numeric',
	        ),
	        array(
	        	'field' => 'outstanding_loan_balance_percentage_fine_on',
	            'label' => 'Outstanding Loan Balance Percentage Fine On',
	            'rules' => 'trim|numeric',
	        ),
	        array(
	        	'field' => 'outstanding_loan_balance_fine_one_off_amount',
	            'label' => 'Outstanding Loan Balance Percentage Fine One Off Amount',
	            'rules' => 'trim|currency',
	        ),    
		);
	

	function _is_greater_than_minimum_loan_amount(){
		if(currency($this->input->post('minimum_loan_amount'))>currency($this->input->post('maximum_loan_amount'))){
			$this->form_validation->set_message('_is_greater_than_minimum_loan_amount','Maximum Loan Amount must be greater than Minimum Loan Amount');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function _is_greater_than_minimum_repayment_period(){
		if(currency($this->input->post('minimum_repayment_period'))>currency($this->input->post('maximum_repayment_period'))){
			$this->form_validation->set_message('_is_greater_than_minimum_repayment_period','Maximum Repayment Period must be greater than Minimum Repayment Period');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function _is_greater_than_minimum_guarantors(){
		if(currency($this->input->post('minimum_guarantors'))>currency($this->input->post('maximum_guarantors'))){
			$this->form_validation->set_message('_is_greater_than_minimum_guarantors','Maximum Guarantprs more than Minimum Guarantors');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function _additional_loan_types_validation_rules(){
    	if($this->input->post('enable_loan_guarantors')){
    		$this->loan_type_validation_rules[] = array(
					'field' => 'minimum_guarantors',
					'label' => 'Minimum Guarantors',
					'rules' => 'trim|numeric|required'
				);
			$this->loan_type_validation_rules[] = array(
					'field' => 'maximum_guarantors',
					'label' => 'Maximum Guarantors',
					'rules' => 'trim|numeric|required|callback__is_greater_than_minimum_guarantors'
				);
    	}

    	if($this->input->post('enable_loan_processing_fee')){
    		$this->loan_type_validation_rules[] = array(
		            'field' =>  'loan_processing_fee_type',
		            'label' =>  'Loan Processing Fee Type',
		            'rules' =>  'trim|required'
		        );
    		if($this->input->post('loan_processing_fee_type')==1){
    			$this->loan_type_validation_rules[] = array(
		            'field' =>  'loan_processing_fee_fixed_amount',
		            'label' =>  'Loan Processing Fee Fixed Amount',
		            'rules' =>  'trim|currency|required'
		        );
    		}else{
    			$this->loan_type_validation_rules[] = array(
			            'field' =>  'loan_processing_fee_percentage_rate',
			            'label' =>  'Loan Processing Fee Fixed Percentage Rate',
			            'rules' =>  'trim|required|currency'
			        );
	        	$this->loan_type_validation_rules[] = array(
			            'field' =>  'loan_processing_fee_percentage_charged_on',
			            'label' =>  'Loan Processing Fee Fixed Percentage Charged On',
			            'rules' =>  'trim|required|numeric'
			        );
    		}
    	}

    	if($this->input->post('enable_loan_fines')){
    		$this->loan_type_validation_rules[] =  array(
			            'field' => 'loan_fine_type',
			            'label' => 'Loan Fine Type',
			            'rules' => 'numeric|trim|required',
			        );
    		if($this->input->post('loan_fine_type') == 1){
    			$this->loan_type_validation_rules[] = array(
			            'field' => 'fixed_fine_amount',
			            'label' => 'Fixed Fine Amount',
			            'rules' => 'trim|currency|required',
			        );
		        $this->loan_type_validation_rules[] = array(
			            'field' => 'fixed_amount_fine_frequency',
			            'label' => 'Fixed Fine Amount Frequency',
			            'rules' => 'numeric|trim|required',
			        );
		        $this->loan_type_validation_rules[] = array(
			            'field' => 'fixed_amount_fine_frequency_on',
			            'label' => 'Fixed Fine Amount Frequency On',
			            'rules' => 'numeric|trim|required',
			        );
    		}else if($this->input->post('loan_fine_type') == 2){
    			$this->loan_type_validation_rules[] = array(
		            'field' => 'percentage_fine_rate',
		            'label' => 'Percentage Fine Rate',
		            'rules' => 'numeric|trim|required',
		        );
		        $this->loan_type_validation_rules[] = array(
		            'field' => 'percentage_fine_frequency',
		            'label' => 'Percentage Fine Frequency',
		            'rules' => 'numeric|trim|required',
		        );
		        $this->loan_type_validation_rules[] = array(
		            'field' => 'percentage_fine_on',
		            'label' => 'Percentage Fine On',
		            'rules' => 'numeric|trim|required',
		        );
    		}else if($this->input->post('loan_fine_type') == 3){
    			$this->loan_type_validation_rules[] = array(
		            'field' => 'one_off_fine_type',
		            'label' => 'One Off Fine Type',
		            'rules' => 'numeric|trim|required',
		        );
		        if($this->input->post('one_off_fine_type')==1){
		        	$this->loan_type_validation_rules[] = array(
			            'field' => 'one_off_fixed_amount',
			            'label' => 'One Off Fixed Amount',
			            'rules' => 'trim|currency|required',
			        );
		        }else if($this->input->post('one_off_fine_type')==2){
		        	$this->loan_type_validation_rules[] = array(
			        	'field' => 'one_off_percentage_rate',
			            'label' => 'One Off Percentage Rate',
			            'rules' => 'trim|numeric|required',
			        );
			        $this->loan_type_validation_rules[] = array(
			        	'field' => 'one_off_percentage_rate_on',
			            'label' => 'One Off Percentage Rate On',
			            'rules' => 'trim|numeric|required',
			        );
		        }
    		}
    	}

    	if($this->input->post('enable_outstanding_loan_balance_fines')){
    		$this->loan_type_validation_rules[] = array(
	        	'field' => 'outstanding_loan_balance_fine_type',
	            'label' => 'Outstanding Loan Balance Fine Type',
	            'rules' => 'trim|numeric|required',
	        );
	        if($this->input->post('outstanding_loan_balance_fine_type')==1){
	        	$this->loan_type_validation_rules[] =  array(
		        	'field' => 'outstanding_loan_balance_fine_fixed_amount',
		            'label' => 'Outstanding Loan Balance Fine Fixed Amount',
		            'rules' => 'trim|currency|required',
		        );
		        $this->loan_type_validation_rules[] = array(
		        	'field' => 'outstanding_loan_balance_fixed_fine_frequency',
		            'label' => 'Outstanding Loan Balance Fine Fixed Fine Frequency',
		            'rules' => 'trim|numeric|required',
		        );
	        }else if($this->input->post('outstanding_loan_balance_fine_type')==2){
	        	$this->loan_type_validation_rules[] = array(
		        	'field' => 'outstanding_loan_balance_percentage_fine_rate',
		            'label' => 'Outstanding Loan Balance Percentage Fine Rate',
		            'rules' => 'trim|currency|required',
		        );
		        $this->loan_type_validation_rules[] = array(
		        	'field' => 'outstanding_loan_balance_percentage_fine_frequency',
		            'label' => 'Outstanding Loan Balance Percentage Fine Frequency',
		            'rules' => 'trim|numeric|required',
		        );
		        $this->loan_type_validation_rules[] = array(
		        	'field' => 'outstanding_loan_balance_percentage_fine_on',
		            'label' => 'Outstanding Loan Balance Percentage Fine On',
		            'rules' => 'trim|numeric|required',
		        );
	        }else if($this->input->post('outstanding_loan_balance_fine_type')==3){
	        	$this->loan_type_validation_rules[] = array(
		        	'field' => 'outstanding_loan_balance_fine_one_off_amount',
		            'label' => 'Outstanding Loan Balance Percentage Fine One Off Amount',
		            'rules' => 'trim|currency|required',
		        );
	        }
    	}
    }


    function manage_group_loan_type(){
    	/*
    		Action Type
			1 - Delete
			2 - edit
			3 - create
			4 - open
			5 - close
			6 - Hide
			7 - unhide
		*/
    	$file = file_get_contents('php://input');
    	$response = array();
    	$request = array();
    	header('Content-Type: application/json');
    	$time = time();
    	if($file){
    		$result = json_decode($file);
    		$request = $result;
    		if($result){
    			$action_type = isset($result->action_type)?$result->action_type:0;
    			$user_id = isset($result->current_user_id)?$result->current_user_id:0;
    			$group_id = isset($result->group_id)?$result->group_id:0;
    			if($action_type&&$user_id&&$group_id){
    				$user = $this->ion_auth->get_user($user_id);
    				if($user){
    					$group = $this->groups_m->get($group_id);
    					if($group){
    						foreach ($result as $key => $value) {
    							$_POST[$key] = $value;    							
    						}
    						if($action_type==1){//delete action
    							$id = isset($result->loan_type_id)?trim($result->loan_type_id):0;
    							$_POST['id'] = $id;
    							if($id){
    								$post = $this->loan_types_m->get($id,$group->id);
    								if($post){
    									if($this->loan_types_m->safe_delete($post->id,$group->id)){
    										$response = array(
							    				'status' => 1,
							    				'time' => time(),
							    				'error' => 'Successfully deleted loan type',
						    				);
    									}else{
    										$response = array(
							    				'status' => 0,
							    				'time' => time(),
							    				'error' => 'Could not delete loan type',
						    				);
    									}
    								}else{
    									$response = array(
						    				'status' => 0,
						    				'time' => time(),
						    				'error' => 'Could not find loan type specified',
					    				);
    								}
    							}else{
    								$response = array(
					    				'status' => 0,
					    				'time' => time(),
					    				'error' => 'Loan type id is missing',
					    			);
    							}
    						}else if($action_type==2){//edit action
    							$id = isset($result->loan_type_id)?trim($result->loan_type_id):0;
    							$_POST['id'] = $id;
    							if($id){
    								$post = $this->loan_types_m->get($id,$group->id);
    								if($post){
    									$this->_additional_loan_types_validation_rules();
								    	$this->form_validation->set_rules($this->loan_type_validation_rules);
								    	if($this->form_validation->run()){
								    		$data = array(
								    				'name'	=>	$this->input->post('name'),
								    				'minimum_loan_amount' => $this->input->post('minimum_loan_amount'),
								    				'maximum_loan_amount' => $this->input->post('maximum_loan_amount'),
								    				'minimum_repayment_period' => $this->input->post('minimum_repayment_period'),
								    				'maximum_repayment_period' => $this->input->post('maximum_repayment_period'),
								    				'interest_rate' => $this->input->post('interest_rate'),
								    				'interest_type' => $this->input->post('interest_type'),
								    				'loan_interest_rate_per' => $this->input->post('loan_interest_rate_per'),
								    				'enable_loan_fines' => $this->input->post('enable_loan_fines')?:0,
								    				'enable_outstanding_loan_balance_fines' => $this->input->post('enable_outstanding_loan_balance_fines')?:0,
								    				'enable_loan_processing_fee' => $this->input->post('enable_loan_processing_fee')?:0,
								    				'enable_loan_fine_deferment' => $this->input->post('enable_loan_fine_deferment')?:0,
								    				'enable_loan_guarantors' => $this->input->post('enable_loan_guarantors')?:0,
								    				'modified_by' => $user->id,
								    				'modified_on' => time(),
								    			); 
								    		if($this->input->post('enable_loan_fines')){
								    			$data = $data+array('loan_fine_type'=>$this->input->post('loan_fine_type'));
								    			if($this->input->post('loan_fine_type')==1){
								    				$data = $data+array(
								    						'fixed_fine_amount' => $this->input->post('fixed_fine_amount'),
								    						'fixed_amount_fine_frequency' => $this->input->post('fixed_amount_fine_frequency'),
								    						'fixed_amount_fine_frequency_on' => $this->input->post('fixed_amount_fine_frequency_on'),
								    					);
								    			}else if($this->input->post('loan_fine_type')==2){
								    				$data = $data+array(
								    						'percentage_fine_rate' => $this->input->post('percentage_fine_rate'),
								    						'percentage_fine_frequency' => $this->input->post('percentage_fine_frequency'),
								    						'percentage_fine_on' => $this->input->post('percentage_fine_on'),
								    					);
								    			}else if($this->input->post('loan_fine_type')==3){
								    				$data = $data + array('one_off_fine_type'=>$this->input->post('one_off_fine_type'));
								    				if($this->input->post('one_off_fine_type')==1){
								    					$data = $data+array('one_off_fixed_amount'=>$this->input->post('one_off_fixed_amount'));
								    				}else if($this->input->post('one_off_fine_type')==2){
								    					$data = $data+array(
								    							'one_off_percentage_rate' => $this->input->post('one_off_percentage_rate'),
																'one_off_percentage_rate_on' => $this->input->post('one_off_percentage_rate_on'),
								    						);
								    				}
								    			}
								    		}

								    		if($this->input->post('enable_outstanding_loan_balance_fines')){
								    			$data = $data + array('outstanding_loan_balance_fine_type'=>$this->input->post('outstanding_loan_balance_fine_type'));
								    			if($this->input->post('outstanding_loan_balance_fine_type')==1){
								    				$data = $data + array(
									    						'outstanding_loan_balance_fine_fixed_amount' => $this->input->post('outstanding_loan_balance_fine_fixed_amount'),
																'outstanding_loan_balance_fixed_fine_frequency' => $this->input->post('outstanding_loan_balance_fixed_fine_frequency'),
															);
								    			}else if($this->input->post('outstanding_loan_balance_fine_type')==2){
								    				$data = $data + array(
									    						'outstanding_loan_balance_percentage_fine_rate' => $this->input->post('outstanding_loan_balance_percentage_fine_rate'),
																'outstanding_loan_balance_percentage_fine_frequency' => $this->input->post('outstanding_loan_balance_percentage_fine_frequency'),
																'outstanding_loan_balance_percentage_fine_on' => $this->input->post('outstanding_loan_balance_percentage_fine_on'),
															);
								    			}else if($this->input->post('outstanding_loan_balance_fine_type')==3){
								    				$data = $data + array(
									    						'outstanding_loan_balance_fine_one_off_amount' => $this->input->post('outstanding_loan_balance_fine_one_off_amount'),
															);
								    			}
								    		}

								    		if($this->input->post('enable_loan_processing_fee')){
								    			$data = $data + array('loan_processing_fee_type'=>$this->input->post('loan_processing_fee_type'));
								    			if($this->input->post('loan_processing_fee_type')==1){
								    				$data = $data + array('loan_processing_fee_fixed_amount'=>$this->input->post('loan_processing_fee_fixed_amount'));
								    			}elseif($this->input->post('loan_processing_fee_type')==2){
								    				$data = $data+array(
								    						'loan_processing_fee_percentage_rate' => $this->input->post('loan_processing_fee_percentage_rate'),
															'loan_processing_fee_percentage_charged_on' => $this->input->post('loan_processing_fee_percentage_charged_on'),
														);
								    			}
								    		}

								    		if($this->input->post('enable_loan_guarantors')){
								    			$data = $data + array(
								    				'minimum_guarantors' => $this->input->post('minimum_guarantors'),
								    				'maximum_guarantors' => $this->input->post('maximum_guarantors'),
								    			);
								    		}

								    		if($this->loan_types_m->update($post->id,$data)){
								    			$response = array(
								    				'status' => 1,
								    				'time' => time(),
								    				'error' => '',
								    				'success' => 'Loan type successfully updated'
								    			);
								    		}else{
								    			$response = array(
								    				'status' => 0,
								    				'time' => time(),
								    				'error' => '',
								    				'success' => 'Error updating loan type'
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
								    				'error' => 'Form validation failed',
								    				'validation_errors' => $post,
								    			);
								    	}
    								}else{
    									$response = array(
						    				'status' => 0,
						    				'time' => time(),
						    				'error' => 'Could not find loan type specified',
					    				);
    								}
    							}else{
    								$response = array(
					    				'status' => 0,
					    				'time' => time(),
					    				'error' => 'Loan type id is missing',
					    			);
    							}
    						}else if($action_type==3){//create action
    							$this->_additional_loan_types_validation_rules();
	    						$this->form_validation->set_rules($this->loan_type_validation_rules);
	    						if($this->form_validation->run()){
	    							$data = array(
						    				'group_id' => $group->id,
						    				'active' => 1,
						    				'name'	=>	$this->input->post('name'),
						    				'minimum_loan_amount' => $this->input->post('minimum_loan_amount'),
						    				'maximum_loan_amount' => $this->input->post('maximum_loan_amount'),
						    				'minimum_repayment_period' => $this->input->post('minimum_repayment_period'),
						    				'maximum_repayment_period' => $this->input->post('maximum_repayment_period'),
						    				'interest_rate' => $this->input->post('interest_rate'),
						    				'interest_type' => $this->input->post('interest_type'),
						    				'loan_interest_rate_per' => $this->input->post('loan_interest_rate_per'),
						    				'grace_period' => 1,
						    				'enable_loan_fines' => $this->input->post('enable_loan_fines')?:0,
						    				'enable_outstanding_loan_balance_fines' => $this->input->post('enable_outstanding_loan_balance_fines')?:0,
						    				'enable_loan_processing_fee' => $this->input->post('enable_loan_processing_fee')?:0,
						    				'enable_loan_fine_deferment' => $this->input->post('enable_loan_fine_deferment')?:0,
						    				'enable_loan_guarantors' => $this->input->post('enable_loan_guarantors')?:0,
						    				'created_by' => $user->id,
						    				'created_on' => time(),
						    			); 
						    		if($this->input->post('enable_loan_fines')){
						    			$data = $data+array('loan_fine_type'=>$this->input->post('loan_fine_type'));
						    			if($this->input->post('loan_fine_type')==1){
						    				$data = $data+array(
						    						'fixed_fine_amount' => $this->input->post('fixed_fine_amount'),
						    						'fixed_amount_fine_frequency' => $this->input->post('fixed_amount_fine_frequency'),
						    						'fixed_amount_fine_frequency_on' => $this->input->post('fixed_amount_fine_frequency_on'),
						    					);
						    			}else if($this->input->post('loan_fine_type')==2){
						    				$data = $data+array(
						    						'percentage_fine_rate' => $this->input->post('percentage_fine_rate'),
						    						'percentage_fine_frequency' => $this->input->post('percentage_fine_frequency'),
						    						'percentage_fine_on' => $this->input->post('percentage_fine_on'),
						    					);
						    			}else if($this->input->post('loan_fine_type')==3){
						    				$data = $data + array('one_off_fine_type'=>$this->input->post('one_off_fine_type'));
						    				if($this->input->post('one_off_fine_type')==1){
						    					$data = $data+array('one_off_fixed_amount'=>$this->input->post('one_off_fixed_amount'));
						    				}else if($this->input->post('one_off_fine_type')==2){
						    					$data = $data+array(
						    							'one_off_percentage_rate' => $this->input->post('one_off_percentage_rate'),
														'one_off_percentage_rate_on' => $this->input->post('one_off_percentage_rate'),
						    						);
						    				}
						    			}
						    		}

						    		if($this->input->post('enable_outstanding_loan_balance_fines')){
						    			$data = $data + array('outstanding_loan_balance_fine_type'=>$this->input->post('outstanding_loan_balance_fine_type'));
						    			if($this->input->post('outstanding_loan_balance_fine_type')==1){
						    				$data = $data + array(
							    						'outstanding_loan_balance_fine_fixed_amount' => $this->input->post('outstanding_loan_balance_fine_fixed_amount'),
														'outstanding_loan_balance_fixed_fine_frequency' => $this->input->post('outstanding_loan_balance_fixed_fine_frequency'),
													);
						    			}else if($this->input->post('outstanding_loan_balance_fine_type')==2){
						    				$data = $data + array(
							    						'outstanding_loan_balance_percentage_fine_rate' => $this->input->post('outstanding_loan_balance_percentage_fine_rate'),
														'outstanding_loan_balance_percentage_fine_frequency' => $this->input->post('outstanding_loan_balance_percentage_fine_frequency'),
														'outstanding_loan_balance_percentage_fine_on' => $this->input->post('outstanding_loan_balance_percentage_fine_on'),
													);
						    			}else if($this->input->post('outstanding_loan_balance_fine_type')==3){
						    				$data = $data + array(
							    						'outstanding_loan_balance_fine_one_off_amount' => $this->input->post('outstanding_loan_balance_fine_one_off_amount'),
													);
						    			}
						    		}

						    		if($this->input->post('enable_loan_processing_fee')){
						    			$data = $data + array('loan_processing_fee_type'=>$this->input->post('loan_processing_fee_type'));
						    			if($this->input->post('loan_processing_fee_type')==1){
						    				$data = $data + array('loan_processing_fee_fixed_amount'=>$this->input->post('loan_processing_fee_fixed_amount'));
						    			}elseif($this->input->post('loan_processing_fee_type')==2){
						    				$data = $data+array(
						    						'loan_processing_fee_percentage_rate' => $this->input->post('loan_processing_fee_percentage_rate'),
													'loan_processing_fee_percentage_charged_on' => $this->input->post('loan_processing_fee_percentage_charged_on'),
												);
						    			}
						    		}

						    		if($this->input->post('enable_loan_guarantors')){
						    			$data = $data + array(
						    				'minimum_guarantors' => $this->input->post('minimum_guarantors'),
						    				'maximum_guarantors' => $this->input->post('maximum_guarantors'),
						    			);
						    		}
						    		if($loan_type_id = $this->loan_types_m->insert($data)){
						    			$response = array(
						    				'status' => 1,
						    				'time' => time(),
						    				'error' => '',
						    				'success' => 'Successfully created loan type',
						    				'id ' => $loan_type_id,
						    			);
						    			$this->setup_tasks_tracker->set_completion_status('loan-type-setting',$group->id,$user->id);
						    		}else{
						    			$response = array(
						    				'status' => 0,
						    				'time' => time(),
						    				'error' => 'Could not add loan type',
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
						    				'error' => 'Form validation failed',
						    				'validation_errors' => $post,
						    			);
	    						}
    						}else if($action_type==4){//open action

    						}else if($action_type==5){//Close action

    						}else if($action_type==6){//hide action
    							$id = isset($result->loan_type_id)?trim($result->loan_type_id):0;
    							$_POST['id'] = $id;
    							if($id){
    								$post = $this->loan_types_m->get($id,$group->id);
    								if($post){
    									if($post->is_hidden){
    										$response = array(
								    				'status' => 0,
								    				'time' => time(),
								    				'error' => 'Loan type is already hidden',
							    				);
    									}else{
    										if($this->loan_types_m->update($post->id,array('is_hidden'=>1,'modified_on'=>time(),'modified_by'=>$user->id))){
	    										$response = array(
								    				'status' => 1,
								    				'time' => time(),
								    				'error' => 'Successfully updated',
							    				);
	    									}else{
	    										$response = array(
								    				'status' => 0,
								    				'time' => time(),
								    				'error' => 'Unable to update loan type',
							    				);
	    									}
    									}
    								}else{
    									$response = array(
						    				'status' => 0,
						    				'time' => time(),
						    				'error' => 'Could not find loan type specified',
					    				);
    								}
    							}else{
    								$response = array(
					    				'status' => 0,
					    				'time' => time(),
					    				'error' => 'Loan type id is missing',
					    			);
    							}
    						}else if($action_type==7){//unhide action
    							$id = isset($result->loan_type_id)?trim($result->loan_type_id):0;
    							$_POST['id'] = $id;
    							if($id){
    								$post = $this->loan_types_m->get($id,$group->id);
    								if($post){
    									if(!$post->is_hidden){
    										$response = array(
								    				'status' => 0,
								    				'time' => time(),
								    				'error' => 'Loan type is already visible',
							    				);
    									}else{
    										if($this->loan_types_m->update($post->id,array('is_hidden'=>'','modified_on'=>time(),'modified_by'=>$user->id))){
	    										$response = array(
								    				'status' => 1,
								    				'time' => time(),
								    				'error' => '',
							    				);
	    									}else{
	    										$response = array(
								    				'status' => 0,
								    				'time' => time(),
								    				'error' => 'Unable to update loan type',
							    				);
	    									}
    									}
    								}else{
    									$response = array(
						    				'status' => 0,
						    				'time' => time(),
						    				'error' => 'Could not find loan type specified',
					    				);
    								}
    							}else{
    								$response = array(
					    				'status' => 0,
					    				'time' => time(),
					    				'error' => 'Loan type id is missing',
					    			);
    							}
    						}else{
								$response = array(
					    				'status' => 0,
					    				'time' => time(),
					    				'error' => 'Action type supplied is invalid',
					    			);
    						}
    					}else{
    						$response = array(
			    				'status' => 0,
			    				'time' => time(),
			    				'error' => 'Group details unavailable',
			    			);
    					}
    				}else{
    					$response = array(
			    				'status' => 0,
			    				'time' => time(),
			    				'error' => 'User details unavailable',
			    			);
    				}
    			}else{
    				$response = array(
		    				'status' => 0,
		    				'time' => time(),
		    				'error' => 'Essential parameters missing',
		    			);
    			}
    		}else{
    			$response = array(
	    				'status' => 0,
	    				'time' => time(),
	    				'error' => 'File sent has the wrong format',
	    			);
    		}
    	}else{
    		$response = array(
    				'status' => 0,
    				'time' => time(),
    				'error' => 'No file sent',
    			);
    	}

    	echo encrypt_json_encode(array('response'=>$response,'request'=>$request));
    }


    function manage_asset_categories(){
   		$file = file_get_contents('php://input');
    	$response = array();
    	$request = array();
    	header('Content-Type: application/json');
    	$time = time();
    	if($file){
    		$result = json_decode($file);
    		$request = $result;
    		if($result){
    			$action_type = isset($result->action_type)?$result->action_type:0;
    			$user_id = isset($result->current_user_id)?$result->current_user_id:0;
    			$group_id = isset($result->group_id)?$result->group_id:0;
    			if($action_type&&$user_id&&$group_id){
    				$user = $this->ion_auth->get_user($user_id);
    				if($user){
    					$group = $this->groups_m->get($group_id);
    					$result = (object)((array)$result+array('slug'=>generate_slug($result->name)));
    					if($group){
    						foreach ($result as $key => $value) {
    							$_POST[$key] = $value;    							
    						}
    						if($action_type==1){//delete action
    							$response = array(
				    				'status' => 0,
				    				'time' => time(),
				    				'error' => 'Action unavailable at the moment',
				    			);
    						}else if($action_type==2){//edit action
    							$asset_category_id = isset($result->asset_category_id)?trim($result->asset_category_id):0;
    							$_POST['id'] = $asset_category_id;
    							if($asset_category_id){
    								$post = $this->asset_categories_m->get($asset_category_id,$group->id);
    								if($post){
    									$this->group_id = $group->id;
			    						$this->form_validation->set_rules($this->asset_categories_validation_rules);
			    						if($this->form_validation->run()){
			    							if($this->asset_categories_m->update($post->id,array(
								                'name'  =>  $this->input->post('name'),
								                'slug'  =>  $this->input->post('slug'),
								                'description' => $this->input->post('description'),
								                'modified_by'    =>  $user->id,
								                'modified_on'    =>  time(),
								                )
								            )){
			    								$response = array(
								    				'status' => 1,
								    				'time' => time(),
								    				'error' => '',
								    				'id' => $post->id,
								    				'success' => $this->input->post('name').' as a(n) asset category was successfully updated',
								    			);
								            }else{
								                $response = array(
								    				'status' => 0,
								    				'time' => time(),
								    				'error' => 'Unable to update a new asset category',
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
								    				'error' => 'Form validation failed',
								    				'validation_errors' => $post,
								    			);
			    						}
    								}else{
    									$response = array(
						    				'status' => 0,
						    				'time' => time(),
						    				'error' => 'category is unavailable',
						    			);
    								}
    							}else{
    								$response = array(
					    				'status' => 0,
					    				'time' => time(),
					    				'error' => 'Category id is missing',
					    			);
    							}
    						}else if($action_type==3){//create action
    							$this->group_id = $group->id;
	    						$this->form_validation->set_rules($this->asset_categories_validation_rules);
	    						if($this->form_validation->run()){
	    							$id = $this->asset_categories_m->insert(array(
						                'name'  =>  $this->input->post('name'),
						                'slug'  =>  $this->input->post('slug'),
						                'description' => $this->input->post('description'),
						                'group_id'  =>  $group->id,
						                'active'    =>  1,
						                'created_by'    =>  $user->id,
						                'created_on'    =>  time(),
						                )
						            );

						            if($id){
						               $response = array(
						    				'status' => 1,
						    				'time' => time(),
						    				'error' => '',
						    				'id' => $id,
						    				'success' => $this->input->post('name').' as a(n) asset category was successfully created',
						    			);
						            }else{
						                $response = array(
						    				'status' => 0,
						    				'time' => time(),
						    				'error' => 'Unable to create a new asset category',
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
						    				'error' => 'Form validation failed',
						    				'validation_errors' => $post,
						    			);
	    						}
    						}else if($action_type==4){//open action

    						}else if($action_type==5){//Close action

    						}else if($action_type==6){//hide action
    							$asset_category_id = isset($result->asset_category_id)?trim($result->asset_category_id):0;
    							$_POST['id'] = $asset_category_id;
    							if($asset_category_id){
    								$post = $this->asset_categories_m->get($asset_category_id,$group->id);
    								if($post){
    									$this->group_id = $group->id;
    									if($post->is_hidden){
    										$response = array(
							    				'status' => 0,
							    				'time' => time(),
							    				'error' => 'Asset category is already headen',
							    			);
    									}else{
    										if($this->asset_categories_m->update($post->id,array(
    												'is_hidden' => 1,
    												'modified_on' => time(),
    												'modified_by' => $user->id,
    											))){
    											$response = array(
								    				'status' => 1,
								    				'time' => time(),
								    				'error' => '',
								    				'success' => 'Asset category successfully hidden',
								    			);
    										}else{
    											$response = array(
								    				'status' => 1,
								    				'time' => time(),
								    				'error' => 'Unable to hide asset category',
								    			);
    										}
    									}
    								}else{
    									$response = array(
						    				'status' => 0,
						    				'time' => time(),
						    				'error' => 'Asset category is not available',
						    			);
    								}
    							}else{
    								$response = array(
						    				'status' => 0,
						    				'time' => time(),
						    				'error' => 'Category Id was not supplied',
						    			);
    							}
    						}else if($action_type==7){//unhide action
    							$asset_category_id = isset($result->asset_category_id)?trim($result->asset_category_id):0;
    							$_POST['id'] = $asset_category_id;
    							if($asset_category_id){
    								$post = $this->asset_categories_m->get($asset_category_id,$group->id);
    								if($post){
    									$this->group_id = $group->id;
    									if(!$post->is_hidden){
    										$response = array(
							    				'status' => 0,
							    				'time' => time(),
							    				'error' => 'Asset category is visible and cannot be hidden',
							    			);
    									}else{
    										if($this->asset_categories_m->update($post->id,array(
    												'is_hidden' => '',
    												'modified_on' => time(),
    												'modified_by' => $user->id,
    											))){
    											$response = array(
								    				'status' => 1,
								    				'time' => time(),
								    				'error' => '',
								    				'success' => 'Asset category successfully unhidden',
								    			);
    										}else{
    											$response = array(
								    				'status' => 1,
								    				'time' => time(),
								    				'error' => 'Unable to unhide asset category',
								    			);
    										}
    									}
    								}else{
    									$response = array(
						    				'status' => 0,
						    				'time' => time(),
						    				'error' => 'Asset category is not available',
						    			);
    								}
    							}else{
    								$response = array(
						    				'status' => 0,
						    				'time' => time(),
						    				'error' => 'Category Id was not supplied',
						    			);
    							}

    						}else{
								$response = array(
					    				'status' => 0,
					    				'time' => time(),
					    				'error' => 'Action type supplied is invalid',
					    			);
    						}
    					}else{
    						$response = array(
			    				'status' => 0,
			    				'time' => time(),
			    				'error' => 'Group details unavailable',
			    			);
    					}
    				}else{
    					$response = array(
			    				'status' => 0,
			    				'time' => time(),
			    				'error' => 'User details unavailable',
			    			);
    				}
    			}else{
    				$response = array(
		    				'status' => 0,
		    				'time' => time(),
		    				'error' => 'Essential parameters missing',
		    			);
    			}
    		}else{
    			$response = array(
	    				'status' => 0,
	    				'time' => time(),
	    				'error' => 'File sent has the wrong format',
	    			);
    		}
    	}else{
    		$response = array(
    				'status' => 0,
    				'time' => time(),
    				'error' => 'No file sent',
    			);
    	}

    	echo encrypt_json_encode(array('response'=>$response,'request'=>$request));
   }


    protected $asset_categories_validation_rules =array(
        array(
                'field' =>   'name',
                'label' =>   'Category Name',
                'rules' =>   'trim|required',
            ),
        array(
                'field' =>   'slug',
                'label' =>   'Category Name Slug',
                'rules' =>   'trim|required|callback__is_unique_asset_category_name',
            ),
        array(
                'field' =>   'description',
                'label' =>   'Category Description',
                'rules' =>   'trim',
            ),
    );

	function _is_unique_asset_category_name(){
        $id = $this->input->post('id');
        $group_id = $this->group_id;
        $slug = $this->input->post('slug');
        if($slug){
            if($this->asset_categories_m->get_by_slug($slug,$id,$group_id)){
                $this->form_validation->set_message('_is_unique_asset_category_name','Another Asset Category by the name `'.$this->input->post('name').'` already exists');
                return FALSE;
            }else{
                return TRUE;
            }
        }  
    }

    function _get_user_groups($user_id=0,$groups = array()){
		if($groups){

		}else{
			$groups = $this->investment_groups->current_user_groups($user_id);
		}
		$group_data = array();
		foreach ($groups as $key => $group) {
			if(!is_file('./uploads/groups/'.$group->avatar)){
				$group->avatar = null;
			}
			$group_data[] = array_merge((array)$group,$this->_get_group_activation_data($group,$user_id));
		}
		return $group_data;
	}

	function _get_group_activation_data($group = array(),$user_id=0){
		$member = $this->members_m->get_group_member_by_user_id($group->id,$user_id);
		$group_roles = $this->group_roles_m->get_group_role_options($group->id);
		$activation_status = 'Pending';
		$activation_status_color_code = '#FF0000';
		$is_on_free_plan = 0;
		if($group->is_validated){
			$activation_status = 'Active';
			$activation_status_color_code = '#00FF00';
		}
		if(isset($group->billing_package_id)){
			$billing_package = $this->billing_m->get_package($group->billing_package_id);
			if($billing_package){
				if($billing_package->billing_type == 3){
					if($group->billing_cycle == 1){
						$is_on_free_plan = 1;
					}
				}
			}
		}
		return array(
			'group_roles' => $group_roles,
			'activation_status' => $activation_status,
			'activation_status_color_code' => $activation_status_color_code,
			'group_currency' => $this->groups_m->get_this_group_currency($group->id),
			'role' => $member->group_role_id?(isset($group_roles[$member->group_role_id])?$group_roles[$member->group_role_id]:'Member'):'Member',
			'group_role_id' => $member->group_role_id,
			'is_on_free_plan' => $is_on_free_plan,
    		'is_admin' => $member->group_role_id?1:($member->is_admin?1:0),
		);
	}

	function _get_user_invited_group_list($user_id=0){
		$invited_groups = $this->members_m->get_user_invited_groups($user_id);
		$groups = array();
		foreach ($invited_groups as $group) {
			$group_roles = $this->group_roles_m->get_group_role_options($group->group_id);
			$member = $this->members_m->get_group_member_by_user_id($group->group_id,$user_id);
			$groups[] = array_merge((array)$group,array(
				'role' => $member->group_role_id?$group_roles[$member->group_role_id]:'Member',
    			'is_admin' => $member->group_role_id?1:($member->is_admin?1:0),
	            'is_initiator' => ($group->owner == $user_id)?1:0,
			));
		}
		return $groups;
	}

	function _get_checkin_data($user=array(),$groups = array()){
		if($groups){
			//$user_groups = array($groups);
			$user_groups = $this->_get_user_groups($user->id,$groups);
			$invited_user_groups = array();
		}else{
			$user_groups = $this->_get_user_groups($user->id);
			$invited_user_groups = array();
		}
		return array(
			'user_groups' => $user_groups,
			'invited_user_groups' => $invited_user_groups,
		);
	}
	function update_user_details(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
                $_GET[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
                $_GET[$key] = $value;
            }
        }   
        $this->form_validation->set_rules($this->validation_rules_user_details);
        if($this->form_validation->run()){
            $user_id = $this->input->post('id_number')?:$this->input->get('id_number');
        if($this->user = $this->users_m->get_user_by_id_number($user_id)){
             
            $this->ion_auth->update_last_login($this->user->id);
            if($this->input->post('loan_limit') || $this->input->get('loan_limit')){
                $loan_limit =($this->input->post('loan_limit')) ??$this->input->get('loan_limit');
            }
            else{
                $loan_limit=$this->user->loan_limit;
            }
            $update=array(
                "loan_limit"=>$loan_limit
            );
            if($this->users_m->update_user($this->user->id,$update)){
                $response = array(
                    'status' => 0,
                    'message' => 'User details updated successfully',
                    'time' => time(),
                );
            }
            else{
                $response = array(
                    'status' => 1,
                    'message' => 'Something went wrong when updating user Details',
                    'time' => time(),
                );
            }
               
        
            
        }else{
            $response = array(
                'status' => 1,
                'message' => 'Could not find user details',
                'time' => time(),
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
            'message' => 'Form validation failed',
            'validation_errors' => $post,
            'time' => time(),
        );
    }
        echo json_encode(array('response'=>$response));
    }

    /*****************************/
	function check_user_loan_limit(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $this->form_validation->set_rules($this->validation_rules_check_limit);
        if($this->form_validation->run()){
            $user_id = $this->input->post('id_number')?:0;
        if($this->user = $this->users_m->get_user_by_id_number($user_id)){
             
            $this->ion_auth->update_last_login($this->user->id);  
            $response = array(
                'status' => 0,
                'message' => 'User details Found',
                'limit'=>$this->user->loan_limit,
                'time' => time(),
            );  
        }else{
            $response = array(
                'status' => 1,
                'message' => 'Could not find user details',
                'time' => time(),
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
            'message' => 'Form validation failed',
            'validation_errors' => $post,
            'time' => time(),
        );
    }
        echo json_encode(array('response'=>$response));
    }
	function register_user(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $id_number=$this->input->post("id_number");
        $phone_number=$this->input->post("phone_number");
        $email_address=($this->input->post("email"))?$this->input->post("email"):'';
        $first_name=$this->input->post("first_name");
        $send_invitation_sms=0;
        $send_invitation_email=0;
        $middle_name=$this->input->post("middle_name");
        $last_name=$this->input->post("last_name");
        $calling_code=$this->input->post("calling_code");
        $loan_limit=$this->input->post("loan_limit");
        $response=array();
        $this->form_validation->set_rules($this->user_registration_rules);
        if($this->form_validation->run()){
        if(!$this->user = $this->users_m->get_user_by_phone_or_id_number($phone_number,$id_number)){
           
            $this->form_validation->set_rules($this->user_registration_rules);
            $this->group=array(
                'id'=>1
             );
             $this->user=array(
                'id'=>1
             );
             if ($member_id=$this->group_members->add_member_to_group(
                $this->group,
                $first_name,
                $last_name,
                $phone_number,
                $email_address,
                FALSE,
                FALSE,
                $this->user,
                '',
                1,
                '',
                $calling_code,
                $phone_number,
                FALSE,
                $id_number,
                $loan_limit,
            )) {
                         
                    $response = array(
                        'status' => 0,
                        'message' => 'A user Registered successfully'
                    );  
                }
                else{
                    $response = array(
                        'status' => 1,
                        'message' => 'A user Not registered'
                    );
                }
                
           
        }else{
            
            $response = array(
                'status' => 1,
                'message' => 'A user is already registered to that phone number or ID'
            );
        }
    }
    else{
        $post = array();
        $form_errors = $this->form_validation->error_array();
        foreach ($form_errors as $key => $value) {
            $post[$key] = $value;
        }
        $response = array(
            'status' => 0,
            'message' => 'Form validation failed',
            'validation_errors' => $post,
            'time' => time(),
        );
    }

        echo json_encode(array('response'=>$response));
    }

	function get_loan_types_list(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $id_number = $this->input->post('id_number')??0;
        if($this->user = $this->users_m->get_user_by_id_number($id_number)){
            $this->ion_auth->update_last_login($this->user->id);
                    $lower_limit = $this->input->post('lower_limit')?:0;
                    $upper_limit = $this->input->post('upper_limit')?:20;
                    $records_per_page = $upper_limit - $lower_limit;
                    $total_rows = $this->loan_types_m->count_group_loan_types();
                    $pagination = create_custom_pagination('group',$total_rows,$records_per_page,$lower_limit,TRUE);
                    $posts = $this->loan_types_m->limit($pagination['limit'])->get_group_loan_types();
                    $loan_types = array();
                    $group_currency = "KES";
                    foreach ($posts as $post) {
                        $repayment_period ='';
                        if($post->loan_repayment_period_type == 1){
                            $repayment_period = 'Fixed repayment of '.$post->fixed_repayment_period.' month(s)';
                        }elseif($post->loan_repayment_period_type == 2){
                            $repayment_period = 'Varying repayment between '.$post->minimum_repayment_period.' and '.$post->maximum_repayment_period.' month(s)';
                        }
                        if($post->loan_amount_type == 1){
                            $minimum_loan_amount = $post->minimum_loan_amount;
                            $maximum_loan_amount = $post->maximum_loan_amount;
                            $loan_amount = 'Between '.$group_currency.' '.number_to_currency($minimum_loan_amount).' - '.$group_currency.' '.number_to_currency($maximum_loan_amount);
                        }elseif($post->loan_amount_type == 2){  
                            $loan_amount = $post->loan_times_number.' times member savings';
                        }else{
                            $loan_amount = '';
                        }
                        if($post->enable_loan_processing_fee):
                            if($post->loan_processing_fee_type==1){
                                $loan_processing= 'Fixed Amount of '.number_to_currency($post->loan_processing_fee_fixed_amount);
                            }else{
                                $loan_processing = $post->loan_processing_fee_percentage_rate.'% of '.$this->loan->loan_processing_fee_percentage_charged_on[$post->loan_processing_fee_percentage_charged_on];
                            }
                        else:
                            $loan_processing = 'No Charge';
                        endif;

                        if($post->enable_loan_guarantors == 1){
                                if($post->loan_guarantors_type == 1){
                                    $guarantors= 'Atleast '.$post->minimum_guarantors.' guarantors every time a member is applying a loan ';   
                                }else if($post->loan_guarantors_type == 2){
                                   $guarantors= 'Atleast '.$post->minimum_guarantors.' guarantors when loan request exceeds maximum loan amount';
                                }else{
                                    $guarantors= 'Unknown value '.$post->loan_guarantors_type;
                                }
                        }else{
                            $guarantors = 'Not Required';  
                        }
                        if($post->enable_loan_fines):
                            $late_payment_fines= $this->loan->late_loan_payment_fine_types[$post->loan_fine_type].' of ';
                                if($post->loan_fine_type==1){
                                    $late_payment_fines.= $group_currency.' '.number_to_currency($post->fixed_fine_amount).' fine '.$this->loan->late_payments_fine_frequency[$post->fixed_amount_fine_frequency].' on ';
                                    $late_payment_fines.= isset($this->loan->fixed_amount_fine_frequency_on[$post->fixed_amount_fine_frequency_on])?$this->loan->fixed_amount_fine_frequency_on[$post->fixed_amount_fine_frequency_on]:'';
                                }else if($post->loan_fine_type==2){
                                    $late_payment_fines.= $post->percentage_fine_rate.'% fine '.$this->loan->late_payments_fine_frequency[$post->percentage_fine_frequency].' on '.$this->loan->percentage_fine_on[$post->percentage_fine_on];
                                }else if($post->loan_fine_type==3){
                                    if($post->one_off_fine_type==1){
                                        $late_payment_fines.= $group_currency.' '.number_to_currency($post->one_off_fixed_amount).' per Installment';
                                    }else if($post->one_off_fine_type==2){
                                        $late_payment_fines.=  $post->one_off_percentage_rate.'% on '.$this->loan->percentage_fine_on[$post->one_off_percentage_rate_on];
                                    }
                                }else{

                                }
                        else:
                            $late_payment_fines = 'Disabled';
                        endif;
                        if($post->enable_outstanding_loan_balance_fines):
                            if($post->outstanding_loan_balance_fine_type==1){
                                $outstanding_payment_fines = $group_currency.' '.number_to_currency($post->outstanding_loan_balance_fine_fixed_amount).' '.$this->loan->late_payments_fine_frequency[$post->outstanding_loan_balance_fixed_fine_frequency];
                            }else if($post->outstanding_loan_balance_fine_type==2){
                                $outstanding_payment_fines =  $post->outstanding_loan_balance_percentage_fine_rate.'% fine '.$this->loan->late_payments_fine_frequency[$post->outstanding_loan_balance_percentage_fine_frequency].' on '.$this->loan->percentage_fine_on[$post->outstanding_loan_balance_percentage_fine_on];
                            }else{
                                $outstanding_payment_fines =  'One Off Amount of '.$group_currency.' '.number_to_currency($post->outstanding_loan_balance_fine_one_off_amount);
                            }
                        else:
                            $outstanding_payment_fines = 'Disabled';
                        endif;
                        $loan_types[] = array(
                            'id' => $post->id,
                            'name' => $post->name,
                            'repayment_period' => $repayment_period,
                            'loan_amount' => $loan_amount,
                            'interest_rate' =>  $post->interest_rate.'% per '.$this->loan->loan_interest_rate_per[$post->loan_interest_rate_per].' on '.$this->loan->interest_types[$post->interest_type],
                            'loan_processing' => $loan_processing,
                            'guarantors' => $guarantors,
                            'late_payment_fines'=> $late_payment_fines,
                            'outstanding_payment_fines'=> $outstanding_payment_fines,
                            'is_hidden' => $post->active?0:1,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'time' => time(),
                        'message' => 'Loan Types list',
                        'loan_types' => $loan_types,
                    );
                
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }
    function generate_member_statement(){
    	$file = file_get_contents('php://input');
    	$response = array();
    	$request = array();
    	header('Content-Type: application/json');
    	$time = time();
    	if($file || isset($_GET['user_id'])){
    		$result = json_decode($file);
    		if($result || isset($_GET['user_id'])){
    			$user_id = isset($result->user_id)?$result->user_id:(isset($_GET['user_id'])?$_GET['user_id']:0);
    			$group_id = isset($result->group_id)?$result->group_id:(isset($_GET['group_id'])?$_GET['group_id']:0);
    			$statement_type = isset($result->statement_type)?$result->statement_type:(isset($_GET['statement_type'])?$_GET['statement_type']:'');
    			$action_type = isset($result->action_type)?$result->action_type:(isset($_GET['action_type'])?$_GET['action_type']:'');
    			if($user_id&&$group_id&&$statement_type&&$action_type){
    				$user = $this->ion_auth->get_user($user_id);
    				if($user){
    					$group = $this->groups_m->get($group_id);
    					if($group){
    						$member = $this->members_m->get_group_member_by_user_id($group->id,$user->id);
    						if($member){
						        $count = 1;
						        $contribution_id_list = '';
						        $contribution_ids = array();
						        if(is_array($contribution_ids)){
						            foreach ($contribution_ids as $contribution_id) {
						                if($contribution_id){
						                    if($count==1){
						                        $contribution_id_list.=$contribution_id;
						                    }else{
						                        $contribution_id_list.=','.$contribution_id;
						                    }
						                    $count++;
						                }
						            }
						        }
						        $from = $this->transaction_statements_m->group_oldest_transaction($group_id);
						        $to = strtotime('+1 day',time());
						        $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE,$group->id);
						        $data['contribution_ids'] = $contribution_ids;
						        $data['from'] = $from;
						        $data['to'] = $to;
						        $data['contribution_options'] = $this->contributions_m->get_group_contribution_options($group->id);
						        $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE,'',$group->id);
						        $data['member'] = $member;
						        $data['balance'] = $this->statements_m->get_group_member_cumulative_balance($group->id,$member->id);
						        $data['amount_payable'] = $this->statements_m->get_member_contribution_amount_payable($group->id,$member->id,$contribution_id_list,$from);
						        $data['amount_paid'] = $this->statements_m->get_member_contribution_amount_paid($group->id,$member->id,$contribution_id_list,$from);
						        $data['posts'] = $this->statements_m->get_member_contribution_statement($member->id,$contribution_id_list,$from,$to,$group->id);
						        $data['statement_transaction_names'] = $this->transactions->statement_transaction_names;
						        $data['group'] = $group;
						        $data['group_currency'] = $this->countries_m->get_currency_code($group->currency_id);
						        $data['chamasoft_settings'] = $this->chamasoft_settings;
						        if(is_file(FCPATH.'uploads/groups/'.$group->avatar)){
						            $data['group_logo'] = site_url('uploads/groups/'.$group->avatar);
						        }else{
						            $data['group_logo'] = site_url('uploads/logos/'.$this->chamasoft_settings->paper_header_logo);
						        }
						        $response = $this->curl_post_data->curl_post_json_pdf((encrypt_json_encode($data)),'https://pdfs.chamasoft.com/contribution_statement',$member->first_name.' Contribution Statement - '.$group->name);
						        print_r($response);die;
    						}else{
    							$response = array(
				    				'status' => 0,
				    				'time' => time(),
				    				'error' => 'The member does not exist within the said group',
				    			);
    						}
    					}else{
    						$response = array(
			    				'status' => 0,
			    				'time' => time(),
			    				'error' => 'Group details not found',
			    			);
    					}
    				}else{
    					$response = array(
			    				'status' => 0,
			    				'time' => time(),
			    				'error' => 'User details not found',
			    			);
    				}
    			}else{
    				$response = array(
		    				'status' => 0,
		    				'time' => time(),
		    				'error' => 'Essential parameters missing',
		    			);
    			}
    		}else{
	    		$response = array(
	    				'status' => 0,
	    				'time' => time(),
	    				'error' => 'Invalid file sent',
	    			);
    		}
    		$request = $result;
    	}else{
    		$response = array(
    				'status' => 0,
    				'time' => time(),
    				'error' => 'No file sent',
    			);
    	}

    	echo encrypt_json_encode(array('response'=>$response,'request'=>$request));
    }

    function update_group_loan_setting(){
    	/***
			'' => group offer loans
			1 => group does not offer loan

			values
			{
				"group_id":
				"current_user_id":
				"loan_setting_response":
			}
    	***/
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
    						$response = array(
					    				'status' => 1,
					    				'time' => time(),
					    				'error' => '',
					    				'success' => 'Successfully updated loan type',
					    				'id ' => $loan_type_id,
					    			);
						    $this->setup_tasks_tracker->set_completion_status('loan-type-setting',$group->id,$user->id);
    					}else{
    						$response = array(
			    				'status' => 0,
			    				'time' => time(),
			    				'error' => 'Group details not found',
			    			);
    					}
    				}else{
    					$response = array(
		    				'status' => 0,
		    				'time' => time(),
		    				'error' => 'User details not found',
		    			);
    				}
    			}else{
    				$response = array(
	    				'status' => 0,
	    				'time' => time(),
	    				'error' => 'essential values missing',
	    			);
    			}
    		}else{
    			$response = array(
    				'status' => 0,
    				'time' => time(),
    				'error' => 'Invalid file sent',
    			);
    		}
    	}else{
    		$response = array(
    				'status' => 0,
    				'time' => time(),
    				'error' => 'No file sent',
    			);
    	}

    	echo encrypt_json_encode(array('response'=>$response,'request'=>$request));
    }


    function send_push_notifications(){
		$this->member_notifications->send_push_notification();
    }

    function get_all_otps(){
    	header("Content-Type: text/plain");
    	print_r($this->users_m->get_all_otps());
    }

	function get_member_upcoming_payments(){
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
            		if($this->member->active){
            			$loan_details = array();
            			$contributions = $this->contributions_m->get_group_contributions_with_active_invoicing();
            			$regular_contribution_settings = $this->contributions_m->get_group_regular_contribution_settings_array($this->group->id);
            			$one_time_contribution_settings = $this->contributions_m->get_group_one_time_contribution_settings_array($this->group->id);
            			$contribution_details = array();
            			$frequency_options  = $this->contribution_invoices->mobile_frequency_options;
            			foreach($contributions as $key => $contribution):
            				if($contribution->regular_invoicing_active){
            					if(array_key_exists($contribution->id, $regular_contribution_settings)){
            						$frequency = $frequency_options[$regular_contribution_settings[$contribution->id]->contribution_frequency];
            					}else{
            						$frequency = '';
            					}
            				}else if($contribution->one_time_invoicing_active){
            					if(array_key_exists($contribution->id, $one_time_contribution_settings)){
            						$frequency = $frequency_options[$one_time_contribution_settings[$contribution->id]->contribution_frequency];
            					}else{
            						$frequency = '';	
            					}
            				}else{
            					$frequency = 'Regular';
            				}
            				$next_contribution_date = $this->contributions_m->get_group_contribution_date_by_contribution_id($this->group->id,$contribution->id);
            				$contribution_details[] = array(
            					'id'=>$contribution->id,
            					'name'=>$contribution->name,
            					'amount'=>$contribution->amount,
            					'description'=>$frequency,
            					'day'=>date("d",$next_contribution_date),
            					'date'=>timestamp_to_datemonth($next_contribution_date),
            					'type'=>1,
            				);
            			endforeach;
            			$base_where = array('member_id'=>$this->member->id,'is_fully_paid'=>0);
                        $ongoing_member_loans = $this->loans_m->get_many_by($base_where);
                        $loan_types = $this->loan_types_m->get_options();
                        foreach($ongoing_member_loans as $ongoing_member_loan):
                        	$newest_invoice = $this->loan_invoices_m->get_newest_invoice($ongoing_member_loan->id);
                        	if($newest_invoice){
                        		if(time() > $newest_invoice->due_date){
                        			$overdue = 1;
                        		}else{
                        			$overdue = 0;
                        		}
                        		$loan_details[] = array(
                                    'id'=>$ongoing_member_loan->id,                                    
                                    'name'=>isset($loan_types[$ongoing_member_loan->loan_type_id])?$loan_types[$ongoing_member_loan->loan_type_id]:'',
                                    'description'=>'Next Installment',
                                    'loan_amount'=>$ongoing_member_loan->loan_amount,
                                    'overdue'=>$overdue,
                                    'day'=>date("d",$newest_invoice->due_date),
            						'date'=>timestamp_to_datemonth($newest_invoice->due_date),
                                    'next_payment_date'=>timestamp_to_datepicker($newest_invoice->due_date),
                                    'amount'=>$newest_invoice->amount_payable,
                                    'type'=>2,
                                );
                        	}
                        endforeach;

                        $from = date(strtotime("-12 months"));
                        $to = time();                        
                        // $member_recent_transactions = $this->_get_member_recent_deposits($this->group->id, $this->member->id,$from,$to);
						$member_recent_transactions = $this->_get_member_recent_transactions($this->group->id, $this->member->id);
                        $response = array(
                    		'status' => 1,
                    		'time' => time(),
                    		'contribution_details' => $contribution_details,
                    		'loan_details'=>$loan_details,
                    		'deposits'=>$member_recent_transactions,
                    	);          			
            		}else{
            			$response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
            		}
            	}else{
            		$response = array(
                        'status' => 0,
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

	function _get_member_deposit_totals($group_id = 0 , $member_id = 0){
		$contributions = $this->contributions_m->get_group_contribution_detail_options();
        $share_contributions = 0;
        $savings_contributions = 0;
        $other_contributions = 0;
        $total_payments = 0;
        $deposits_array = 0;
        $total_deposits = 0;
        $total_savings = 0;
        $contribution_display_options = $this->contributions_m->get_group_contribution_display_options($this->group->id,TRUE);
        $contribution_list = '0';
        foreach ($contribution_display_options as $id=>$name) {
            if($contribution_list){
                $contribution_list.=','.$id;
            }else{
                $contribution_list=$id;
            }
        }        
        $deposits = $this->statements_m->get_group_member_total_paid_by_contribution_array($member_id,$group_id,$contribution_list);  
        if($deposits){
        	foreach ($deposits as $key => $deposit):
        		if(array_key_exists($key,$contributions)){
        			if($contributions[$key]->category == 1){
	                	if($key){
		                    $share_contributions+=$deposits[$key];
		                }
	                }else if($contributions[$key]->category == 2){
	                	if($key){
		                    $savings_contributions+=$deposits[$key];
		                }
	                }else{
	                    $other_contributions += $deposits[$key];
	                }
        		}
        	endforeach;

        	$total_deposits = $other_contributions+$share_contributions+$savings_contributions;
        	$total_savings = $share_contributions+$savings_contributions;
        }       
        $deposits_array = array(
            'share_account'=>$share_contributions,
            'savings_account'=>$savings_contributions,
            'other_accounts'=>$other_contributions,
            'total_deposits'=>$total_savings,
            
        );
        return $deposits_array;
	}

	function get_member_deposit_summary(){
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
            		if($this->member->active){
            			$deposits = $this->_get_member_deposit_totals($this->group->id,$this->member->id);
            			//$dashboard_summaries = $this->_get_member_deposit_summaries($this->group->id,$this->member->id);
            			$outstanding_loan = 0;
            			$ongoing_loan_amounts_paid = array();
            			$ongoing_loan_amounts_payable = array();
            			$base_where = array('member_id'=>$this->member->id,'is_fully_paid'=>0);
                        $ongoing_member_loans = $this->loans_m->get_many_by($base_where);
                        if($ongoing_member_loans){
	                        foreach($ongoing_member_loans as $ongoing_member_loan):
	                        	$ongoing_loan_amounts_paid[$ongoing_member_loan->id]
	                            = $this->loan_repayments_m->get_loan_total_payments($ongoing_member_loan->id);
	                            $ongoing_loan_amounts_payable[$ongoing_member_loan->id]
	                            = $this->loans_m->get_summation_for_invoice($ongoing_member_loan->id)->total_amount_payable;
	                        endforeach;

	                        if($ongoing_loan_amounts_payable){
		                        foreach($ongoing_loan_amounts_payable as $key => $amount_payable):
		                            $outstanding_loan+= $amount_payable - $ongoing_loan_amounts_paid[$key];
		                        endforeach;
		                    }
		                }
				        $response = array(
				        	'status' => 1,
                    		'time' => time(),
                    		///'summaries'=>$dashboard_summaries,
				            'deposits' =>$deposits,
				            'loan_balance'=>$outstanding_loan,
				        );            			
            		}else{
            			$response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
            		}
            	}else{
            		$response = array(
                        'status' => 0,
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
}