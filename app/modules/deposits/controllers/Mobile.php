<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Mobile extends Mobile_Controller{

	public $payment_types = array(
        1 => 'Contribution payment',
        2 => 'Fine Payment',
        3 => 'Loan Repayment',
        4 => 'Miscellaneous Payment'
    );


	public $filter_status = array(
	 	1 => 'Contribution Payments',
	 	2 => 'Fine payments',
	 	3 => 'Loan repayments',
	 	4 => 'Miscellaneous Payments',
	 	5 => 'Income Receipts',
	 	6 => 'Bank Loan Disbursements',
	 	7 => 'Stock sale',
	 	8 => 'Money market investment cashins',
	 	9 => 'Asset sale',
	 	10 => 'Fund transfer',
	 	11 => 'Loan processing income',
	 	12 => 'External Lending processing income',
	 	13 => 'External Lending loan repayments'
	);
    protected $validation_rules = array(
        array(
            'field' =>  'total_amount',
            'label' =>  'Total Amount',
            'rules' =>  'required|xss_clean|trim|currency',
        ),
        array(
            'field' =>  'amounts',
            'label' =>  'Payment Amounts',
            'rules' =>  'callback__valid_amounts',
        ),
        array(
            'field' =>  'deposit_type',
            'label' =>  'Payment For',
            'rules' =>  'callback__valid_payment_fors',
        ),
    );

	protected $contribution_payments_validation_rules = array(
		array(
			'field' => 'deposit_date',
			'label' => 'Deposit Date',
			'rules' => 'xss_clean|trim|required|date'
		),
		array(
			'field' => 'member_id',
			'label' => 'Member Name',
			'rules' => 'xss_clean|trim|required|numeric|callback__member_exists'
		),
		array(
			'field' => 'amount',
			'label' => 'Deposit Amount',
			'rules' => 'xss_clean|trim|required|currency'
		),
		array(
			'field' => 'account_id',
			'label' => 'Account Id',
			'rules' => 'xss_clean|trim|required|callback__valid_account_id'
		),
		array(
			'field' => 'deposit_method',
			'label' => 'Deposit Method',
			'rules' => 'xss_clean|trim|required|numeric'
		),
		array(
			'field' => 'contribution_id',
			'label' => 'Contribution',
			'rules' => 'xss_clean|trim|required|numeric|callback__contribution_exists'
		),
		array(
			'field' => 'description',
			'label' => 'Description',
			'rules' => 'xss_clean|trim'
		),
		array(
			'field' => 'send_sms_notification',
			'label' => 'Send SMS Notification',
			'rules' => 'xss_clean|trim|numeric'
		),
		array(
			'field' => 'send_email_notification',
			'label' => 'Send Email Notification',
			'rules' => 'xss_clean|trim|numeric'
		),
	);

	protected $new_record_contribution_payments_validation_rules = array(
		array(
			'field' => 'deposit_date',
			'name' => 'Deposit Date',
			'rules' => 'xss_clean|trim|required|date'
		),
		array(
			'field' => 'deposit_method',
			'label' => 'Deposit Method',
			'rules' => 'xss_clean|trim|required|numeric'
		),
		array(
			'field' => 'contribution_id',
			'label' => 'Contribution',
			'rules' => 'xss_clean|trim|required|numeric|callback__contribution_exists'
		),
		array(
			'field' => 'account_id',
			'label' => 'Account Id',
			'rules' => 'xss_clean|trim|required|callback__valid_account_id'
		),
		array(
			'field' => 'amount',
			'label' => 'Deposit Amount',
			'rules' => 'xss_clean|trim|currency'
		),
		array(
			'field' => 'member_type_id',
			'label' => 'Deposit Amount',
			'rules' => 'xss_clean|trim|numeric|callback__valid_payment_amounts'
		),
	);

	protected $new_record_income_payments_validation_rules = array(
		array(
			'field' => 'deposit_date',
			'name' => 'Deposit Date',
			'rules' => 'xss_clean|trim|required|date'
		),
		array(
			'field' => 'deposit_method',
			'label' => 'Deposit Method',
			'rules' => 'xss_clean|trim|required|numeric'
		),
		array(
			'field' => 'depositor_id',
			'label' => 'Depositor',
			'rules' => 'xss_clean|trim|required|numeric|callback__depositor_exists'
		),
		array(
			'field' => 'income_category_id',
			'label' => 'Income Category',
			'rules' => 'xss_clean|trim|required|numeric|callback__income_category_exists'
		),
		array(
			'field' => 'account_id',
			'label' => 'Account Id',
			'rules' => 'xss_clean|trim|required|callback__valid_account_id'
		),
		array(
			'field' => 'amount',
			'label' => 'Deposit Amount',
			'rules' => 'xss_clean|trim|currency'
		),
		array(
			'field' => 'description',
			'label' => 'Description',
			'rules' => 'xss_clean|trim'
		),
	);

	protected $new_record_fine_payments_validation_rules = array(
		array(
			'field' => 'deposit_date',
			'name' => 'Deposit Date',
			'rules' => 'xss_clean|trim|required|date'
		),
		array(
			'field' => 'deposit_method',
			'label' => 'Deposit Method',
			'rules' => 'xss_clean|trim|required|numeric'
		),
		array(
			'field' => 'fine_category_id',
			'label' => 'Fine Category',
			'rules' => 'xss_clean|trim|required|callback__fine_category_exists'
		),
		array(
			'field' => 'account_id',
			'label' => 'Account Id',
			'rules' => 'xss_clean|trim|required|callback__valid_account_id'
		),
		array(
			'field' => 'amount',
			'label' => 'Deposit Amount',
			'rules' => 'xss_clean|trim|currency'
		),
		array(
			'field' => 'member_type_id',
			'label' => 'Deposit Amount',
			'rules' => 'xss_clean|trim|numeric|callback__valid_payment_amounts'
		),
	);

	protected $new_record_miscellaneous_payments_validation_rules = array(
		array(
			'field' => 'deposit_date',
			'name' => 'Deposit Date',
			'rules' => 'xss_clean|trim|required|date'
		),
		array(
			'field' => 'deposit_method',
			'label' => 'Deposit Method',
			'rules' => 'xss_clean|trim|required|numeric'
		),
		array(
			'field' => 'member_id',
			'label' => 'Group Member',
			'rules' => 'xss_clean|trim|required|callback__member_exists'
		),
		array(
			'field' => 'account_id',
			'label' => 'Account Id',
			'rules' => 'xss_clean|trim|required|callback__valid_account_id'
		),
		array(
			'field' => 'amount',
			'label' => 'Deposit Amount',
			'rules' => 'xss_clean|trim|currency'
		),
		array(
			'field' => 'description',
			'label' => 'Description',
			'rules' => 'xss_clean|trim'
		),
	);

	protected $fine_payments_validation_rules = array(
		array(
			'field' => 'deposit_date',
			'label' => 'Deposit Date',
			'rules' => 'xss_clean|trim|required|date'
		),
		array(
			'field' => 'member_id',
			'label' => 'Member Name',
			'rules' => 'xss_clean|trim|required|numeric|callback__member_exists'
		),
		array(
			'field' => 'amount',
			'label' => 'Deposit Amount',
			'rules' => 'xss_clean|trim|required|currency'
		),
		array(
			'field' => 'account_id',
			'label' => 'Account Id',
			'rules' => 'xss_clean|trim|required|callback__valid_account_id'
		),
		array(
			'field' => 'deposit_method',
			'label' => 'Deposit Method',
			'rules' => 'xss_clean|trim|required|numeric'
		),
		array(
			'field' => 'fine_category_id',
			'label' => 'Fine Category',
			'rules' => 'xss_clean|trim|required|callback__fine_category_exists'
		),
		array(
			'field' => 'description',
			'label' => 'Description',
			'rules' => 'xss_clean|trim'
		),
		array(
			'field' => 'send_sms_notification',
			'label' => 'Send SMS Notification',
			'rules' => 'xss_clean|trim|numeric'
		),
		array(
			'field' => 'send_email_notification',
			'label' => 'Send Email Notification',
			'rules' => 'xss_clean|trim|numeric'
		),
	);

	protected $loan_repayments_validation_rules = array(
		array(
			'field' => 'deposit_date',
			'label' => 'Deposit Date',
			'rules' => 'xss_clean|trim|required|date'
		),
		array(
			'field' => 'member_id',
			'label' => 'Member Name',
			'rules' => 'xss_clean|trim|required|numeric|callback__member_exists'
		),
		array(
			'field' => 'amount',
			'label' => 'Deposit Amount',
			'rules' => 'xss_clean|trim|required|currency'
		),
		array(
			'field' => 'account_id',
			'label' => 'Account Id',
			'rules' => 'xss_clean|trim|required|callback__valid_account_id'
		),
		array(
			'field' => 'deposit_method',
			'label' => 'Deposit Method',
			'rules' => 'xss_clean|trim|required|numeric'
		),
		array(
			'field' => 'loan_id',
			'label' => 'Member Loan',
			'rules' => 'xss_clean|trim|required|callback__loan_exists'
		),
		array(
			'field' => 'description',
			'label' => 'Description',
			'rules' => 'xss_clean|trim'
		),
		array(
			'field' => 'send_sms_notification',
			'label' => 'Send SMS Notification',
			'rules' => 'xss_clean|trim|numeric'
		),
		array(
			'field' => 'send_email_notification',
			'label' => 'Send Email Notification',
			'rules' => 'xss_clean|trim|numeric'
		),
	);

	protected $miscellaneous_payments_validation_rules = array(
		array(
			'field' => 'deposit_date',
			'label' => 'Deposit Date',
			'rules' => 'xss_clean|trim|required|date'
		),
		array(
			'field' => 'member_id',
			'label' => 'Member Name',
			'rules' => 'xss_clean|trim|required|numeric|callback__member_exists'
		),
		array(
			'field' => 'amount',
			'label' => 'Deposit Amount',
			'rules' => 'xss_clean|trim|required|currency'
		),
		array(
			'field' => 'account_id',
			'label' => 'Account Id',
			'rules' => 'xss_clean|trim|required|callback__valid_account_id'
		),
		array(
			'field' => 'deposit_method',
			'label' => 'Deposit Method',
			'rules' => 'xss_clean|trim|required|numeric'
		),
		array(
			'field' => 'description',
			'label' => 'Description',
			'rules' => 'xss_clean|trim|required'
		),
		array(
			'field' => 'send_sms_notification',
			'label' => 'Send SMS Notification',
			'rules' => 'xss_clean|trim|numeric'
		),
		array(
			'field' => 'send_email_notification',
			'label' => 'Send Email Notification',
			'rules' => 'xss_clean|trim|numeric'
		),
	);

	protected $income_payments_validation_rules = array(
		array(
			'field' => 'deposit_date',
			'label' => 'Deposit Date',
			'rules' => 'xss_clean|trim|required|date'
		),
		array(
			'field' => 'depositor_id',
			'label' => 'Depositor Name',
			'rules' => 'xss_clean|trim|required|numeric|callback__depositor_exists'
		),
		array(
			'field' => 'amount',
			'label' => 'Deposit Amount',
			'rules' => 'xss_clean|trim|required|currency'
		),
		array(
			'field' => 'account_id',
			'label' => 'Account Id',
			'rules' => 'xss_clean|trim|required|callback__valid_account_id'
		),
		array(
			'field' => 'income_category_id',
			'label' => 'Income Category',
			'rules' => 'xss_clean|trim|required|callback__income_category_exists'
		),
		array(
			'field' => 'deposit_method',
			'label' => 'Deposit Method',
			'rules' => 'xss_clean|trim|required|numeric'
		),
		array(
			'field' => 'description',
			'label' => 'Description',
			'rules' => 'xss_clean|trim'
		),
		array(
			'field' => 'send_sms_notification',
			'label' => 'Send SMS Notification',
			'rules' => 'xss_clean|trim|numeric'
		),
		array(
			'field' => 'send_email_notification',
			'label' => 'Send Email Notification',
			'rules' => 'xss_clean|trim|numeric'
		),
	);

	protected $bank_loan_validation_rules = array(
		array(
                'field' => 'description',
                'label' => 'Bank loan description',
                'rules' => 'required|xss_clean|trim',
            ),
        array(
                'field' => 'amount_loaned',
                'label' => 'Bank loan amount received',
                'rules' => 'required|xss_clean|trim|currency',
            ),
        array(
                'field' => 'total_loan_amount_payable',
                'label' => 'Bank loan total amount payable',
                'rules' => 'required|xss_clean|trim|currency',
            ),
        array(
                'field' => 'loan_balance',
                'label' => 'Bank loan balance',
                'rules' => 'required|xss_clean|trim|currency',
            ),
        array(
                'field' => 'loan_start_date',
                'label' => 'Bank loan start date',
                'rules' => 'required|xss_clean|trim',
            ),
        array(
                'field' => 'loan_end_date',
                'label' => 'Bank loan end date',
                'rules' => 'required|xss_clean|trim|callback__end_date_is_great_than_start_date',
            ),
        array(
                'field' => 'account_id',
                'label' => 'Group account loan deposited to',
                'rules' => 'required|xss_clean|trim|callback__valid_account_id',
            ),
	);

	protected $transfer_to_options = array(
        1 => "Contribution share",
        2 => "Fine payment",
        3 => "Loan share",
        4 => "Another member",
    );

    protected $member_transfer_to_options = array(
        1 => "Contribution share",
        2 => "Fine payment",
        3 => "Loan share",
    );

	function _valid_amounts(){
        $amounts = $this->input->post('amounts');
        if($amounts){
        	$summation = array_sum($amounts);
	        foreach ($amounts as $amount) {
	           if(!valid_currency($amount)){
	                $this->form_validation->set_message('_valid_amounts',$amount.' is not valid amount');
	                return FALSE;
	           }
	        }
	        $total_amount = $this->input->post('total_amount');
	        if(currency($total_amount) != currency($summation)){
	            $this->form_validation->set_message('_valid_amounts','Total amount('.$total_amount.') does not add up to individual payments sum('.$summation.')');
	            return FALSE;
	        }
	        return TRUE;
        }else{
        	$this->form_validation->set_message('_valid_amounts','Kindly select atleast 1 item to pay for');
        	return FALSE;
        }
        
    }

    function _valid_payment_fors(){
        $deposit_type = $this->input->post('deposit_type');
        $amounts = $this->input->post('amounts');
        $contribution_ids = $this->input->post('contribution_ids');
        $fine_category_ids = $this->input->post('fine_category_ids');
        $group_id = $this->input->post('group_id');
        $loan_ids = $this->input->post('loan_ids');
        if($deposit_type){
            foreach ($deposit_type as $row=>$type) {
                if(array_key_exists($type, $this->payment_types)){
                    if($type == 1){
                    	$contribution_id = $contribution_ids[$row];
                    	if($this->contributions_m->check_group_contribution($contribution_id,$group_id)){

                    	}else{
                    		$this->form_validation->set_message('_valid_payment_fors','Contribution selected is invalid');
                    		return FALSE;
                    	}
                    }elseif($type == 2){
                    	$fine_category_id = $fine_category_ids[$row];
                    	$fine_category_id = str_replace('fine_category-','', $fine_category_id);
                    	if($this->fine_categories_m->check_group_fine_category($fine_category_id,$group_id)){

                    	}else{
                    		$this->form_validation->set_message('_valid_payment_fors','Fine category selected is invalid');
                    		return FALSE;
                    	}
                    }elseif($type == 3){
                    	$loan_id = $loan_ids[$row];
                    	if($this->loans_m->check_group_loan($loan_id,$group_id)){

                    	}else{
                    		$this->form_validation->set_message('_valid_payment_fors','Contribution selected is invalid');
                    		return FALSE;
                    	}
                    }
                }else{
                	$this->form_validation->set_message('_valid_payment_fors','Payment not recognized');
                    return FALSE;
                }
                if(!isset($amounts[$row])){
                    $this->form_validation->set_message('_valid_payment_fors','Payment amount is required');
                    return FALSE;
                }
            }
        }else{
            $this->form_validation->set_message('_valid_payment_fors','Select at least one payment');
            return FALSE;
        }
    }

    protected $deposit_pairing = array();

	function __construct(){
		parent::__construct();
		$this->load->model('deposits_m');
		$this->load->model('fine_categories/fine_categories_m');
		$this->load->model('loans/loans_m');
		$this->load->library('loan');
		$this->load->library('transactions');
		$this->load->model('income_categories/income_categories_m');
		$this->load->model('depositors/depositors_m');

		$this->deposit_types_status_options = $this->transactions->deposit_types_status_options;
		$this->deposit_pairing = array(
			3 => $this->deposit_types_status_options[5],
			2 => $this->deposit_types_status_options[2],
			1 => $this->deposit_types_status_options[1],
			4 => $this->deposit_types_status_options[3],
			5 => $this->deposit_types_status_options[4],
			6 => $this->deposit_types_status_options[6],
			7 => $this->deposit_types_status_options[7],
			8 => $this->deposit_types_status_options[8],
			9 => $this->deposit_types_status_options[9],
			10 => $this->deposit_types_status_options[10],
			11 => $this->deposit_types_status_options[11],
			12 => $this->deposit_types_status_options[12],
			13 => $this->deposit_types_status_options[13],
		);
	}

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
	       		'status'	=>	404,
	       		'message'		=>	'404 Method Not Found for URI: '.$this->uri->uri_string(),
   			)

       	));
	}

	function _valid_account_id(){
		$account_id = $this->input->post('account_id');
		$group_id = $this->input->post('group_id');
		if($this->accounts_m->check_if_group_account_exists($account_id,$group_id)){
			return TRUE;
		}else{
			$this->form_validation->set_message('_valid_account_id','Group account does not exist');
			return FALSE;
		}
	}

	function _valid_payment_amounts(){
		$member_type_id = $this->input->post('member_type_id');
		if($member_type_id==1){
			$individual_payments = $this->input->post('individual_payments');
			if(count($individual_payments)<1){
				$this->form_validation->set_message('_valid_payment_amounts','Select atleast 1 member to record individual payment');
				return FALSE;
			}
			foreach ($individual_payments as $member_id => $amount) {
				if($member_id&&$amount){
					if(!is_numeric($member_id) || !valid_currency($amount)){
						$this->form_validation->set_message('_valid_payment_amounts','Individual payments amounts must be currency');
						return FALSE;
					}
				}else{
					$this->form_validation->set_message('_valid_payment_amounts','Individual payments fields must all be filled');
					return FALSE;
				}
			}
			return TRUE;
		}else{
			$amount = $this->input->post('amount');
			if($amount && valid_currency($amount)){
				return TRUE;
			}else{
				$this->form_validation->set_message('_valid_payment_amounts','Amount is required and must be currency');
				return FALSE;
			}
		}
	}

	function _member_exists(){
		$member_id = $this->input->post('member_id');
		$group_id = $this->input->post('group_id');
		if($this->members_m->get_member_where_member_id($member_id,$group_id)){
			return TRUE;
		}else{
			$this->form_validation->set_message('_member_exists','Member selected does not exist in this group');
			return FALSE;
		}
	}

	function _contribution_exists(){
		$group_id = $this->input->post('group_id');
		$contribution_id = $this->input->post('contribution_id');
		if($this->contributions_m->contribution_exists_in_group($contribution_id,$group_id)){
			return TRUE;
		}else{
			$this->form_validation->set_message('_contribution_exists','Contribution selected does not exist in this group');
			return FALSE;
		}
	}

	function _fine_category_exists(){
		$group_id = $this->input->post('group_id');
		$fine_category = $this->input->post('fine_category_id');
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
        	if($this->contributions_m->contribution_exists_in_group($contribution_id,$group_id)){
				return TRUE;
			}else{
				$this->form_validation->set_message('_fine_category_exists','Fine Category  selected does not exist in this group');
				return FALSE;
			}
        }

        if($fine_category_id){
        	if($this->fine_categories_m->get_group_fine_category($fine_category_id,$group_id)){
				return TRUE;
			}else{
				$this->form_validation->set_message('_fine_category_exists','Fine Category  selected does not exist in this group');
				return FALSE;
			}
        }

        else{
        	$this->form_validation->set_message('_fine_category_exists','Fine Category selected does not exist in this group');
			return FALSE;
        }
	}

	function _loan_exists(){
		$group_id = $this->input->post('group_id');
		$loan_id = $this->input->post('loan_id');
		$member_id = $this->input->post('member_id');
		if($this->loans_m->loan_exists_in_group($loan_id,$group_id,$member_id)){
			return TRUE;
		}else{
			$this->form_validation->set_message('_loan_exists','Loan selected does not exist in this group');
			return FALSE;
		}
	}

	function _income_category_exists(){
		$group_id = $this->input->post('group_id');
		$income_category_id = $this->input->post('income_category_id');
		if($this->income_categories_m->income_category_exists($income_category_id,$group_id)){
			return TRUE;
		}else{
			$this->form_validation->set_message('_income_category_exists','Income Category selected does not exist in this group');
			return FALSE;
		}
	}

	function _depositor_exists(){
		$group_id = $this->input->post('group_id');
		$depositor_id = $this->input->post('depositor_id');
		if($this->depositors_m->get_group_depositor($depositor_id,$group_id)){
			return TRUE;
		}else{
			$this->form_validation->set_message('_depositor_exists','Depositor selected does not exist in this group');
			return FALSE;
		}
	}

	function _end_date_is_great_than_start_date(){
        $loan_end_date = $this->input->post('loan_end_date');
        $loan_start_date = $this->input->post('loan_start_date');
        if(strtotime($loan_end_date)<strtotime($loan_start_date)){
            $this->form_validation->set_message('_end_date_is_great_than_start_date','Loan end date can not be before loan start date');
            return FALSE;
        }
        else{
            return TRUE;
        }
    }


	function record_contribution_payments(){
		$member_ids = array();
		$amounts = array();
		$account_ids = array();
		$deposit_dates = array();
		$deposit_methods = array();
		$contribution_ids = array();
		$descriptions = array();
		$contribution_payments = array();
		foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
            	if(is_array($value)){
            		foreach ($value as $key_value => $value_value) {
            			if($value_value->member_id){
            				$member_ids[$key_value] = $value_value->member_id;
            			}
            			if($value_value->amount){
            				$amounts[$key_value] = currency($value_value->amount);
            			}
            			if($value_value->account_id){
            				$account_ids[$key_value] = $value_value->account_id;
            			}
            			if($value_value->deposit_date){
            				$deposit_dates[$key_value] = $value_value->deposit_date;
            			}
            			if($value_value->deposit_method){
            				$deposit_methods[$key_value] = $value_value->deposit_method;
            			}
            			if($value_value->contribution_id){
            				$contribution_ids[$key_value] = $value_value->contribution_id;
            			}
            			if($value_value->description){
            				$descriptions[$key_value] = $value_value->description;
            			}
            			$contribution_payments[$key_value] = array($key_value=>'item');
            		}
            	}else{
            		$_POST[$key] = $value;
            	}
            }
        }
        $_POST['member_ids'] = $member_ids;
        $_POST['amounts'] = $amounts;
        $_POST['account_ids'] = $account_ids;
        $_POST['deposit_dates'] = $deposit_dates;
        $_POST['deposit_methods'] = $deposit_methods;
        $_POST['contribution_ids'] = $contribution_ids;
        $_POST['descriptions'] = $descriptions;
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
            	if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
            		if($this->member->group_role_id || $this->member->is_admin){
            			$member_ids = $this->input->post('member_ids');
		            	$amounts = $this->input->post('amounts');
		            	$account_ids = $this->input->post('account_ids');
		            	$deposit_dates = $this->input->post('deposit_dates');
		            	$deposit_methods = $this->input->post('deposit_methods');
		            	$contribution_ids = $this->input->post('contribution_ids');
		            	$descriptions = $this->input->post('descriptions');
						if(empty($contribution_payments)){
							$response = array(
			    				'status' => 0,
			    				'time' => time(),
			    				'message' => 'Error contribution payments array empty.',
			    			);
						}else{
							$entries_are_valid = TRUE;
		                    foreach($contribution_payments as $key=>$contribution_payment):
		                        if(isset($deposit_date[$key])&&isset($member_ids[$key])&&isset($contribution_ids[$key])&&isset($account_ids[$key])&&isset($amounts[$key])&&isset($deposit_methods[$key])):    
		                            //Deposit dates
		                            if($deposit_date[$key]==""){
		                                $entries_are_valid = FALSE;
		                            }else{

		                            }
		                            //Members
		                            if($member_ids[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{
		                                if(is_numeric($member_ids[$key])){

		                                }else{
		                                    $entries_are_valid = FALSE;
		                                }
		                            }
		                            //Contributions
		                            if($contribution_ids[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{
		                                if(is_numeric($contribution_ids[$key])){

		                                }else{
		                                    $entries_are_valid = FALSE;
		                                }
		                            }
		                             //Accounts
		                            if($account_ids[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{

		                            }
		                            //Deposit Method
		                            if($deposit_methods[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{
		                                if(is_numeric($deposit_methods[$key])){

		                                }else{
		                                    $entries_are_valid = FALSE;
		                                }
		                            }
		                            //amounts
		                            if($amounts[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{
		                                if(valid_currency($amounts[$key])){

		                                }else{
		                                    $entries_are_valid = FALSE; 
		                                }
		                            }
		                        endif;
		                    endforeach;
		                    if($entries_are_valid){
		                    	$transaction_result = TRUE;
								foreach($contribution_payments as $key => $contribution_payment):
									$deposit_date = strtotime($deposit_dates[$key]);
									$amount = valid_currency($amounts[$key]);
									$send_sms_notification = $this->input->post('send_sms_notification')?:0;
									$send_email_notification = $this->input->post('send_email_notification')?:0;
									$description = isset($descriptions[$key])?$descriptions[$key]:'';
			                    	if($this->transactions->record_contribution_payment($this->group->id,$deposit_date,$member_ids[$key],$contribution_ids[$key],$account_ids[$key],$deposit_methods[$key],$description,$amount,$send_sms_notification,$send_email_notification)){
		                            }else{
		                                $transaction_result = FALSE;
		                            }
		                        endforeach;
		                        if($transaction_result){
		                        	$response = array(
					    				'status' => 1,
					    				'time' => time(),
					    				'message' => 'Successfully added transactions',
					    			);
		                        }else{
		                        	$response = array(
					    				'status' => 0,
					    				'time' => time(),
					    				'message' => 'Error adding transactions.',
					    			);
		                        }
		                    }else{
		                    	$response = array(
				    				'status' => 0,
				    				'time' => time(),
				    				'message' => 'Error data validation errors, some values are missing.',
				    			);
		                    }
						}
            		}else{
            			$response = array(
	                        'status' => 0,
	                        'message' => 'You are not allowed to perform this request. For group admins only',
	                        'time' => time(),
	                    );
            		}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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
        if($response['status']==0){
        	update_request_id($this->request->request_id);
        }
        echo encrypt_json_encode(array('response'=>$response));
	}

	function new_record_contribution_payments(){
		$response = array();
		foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
            	if(is_object($value)){
            		$_POST[$key] = (array)$value;
            	}else{
            		$_POST[$key] = $value;
            	}
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
            	if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
            		if($this->member->group_role_id || $this->member->is_admin){
            			$this->form_validation->set_rules($this->new_record_contribution_payments_validation_rules);
            			if($this->form_validation->run()){
            				$deposit_date = $this->input->post('deposit_date');
            				$deposit_method = $this->input->post('deposit_method');
            				$amount = currency($this->input->post('amount'));
            				$account_id = $this->input->post('account_id');
            				$contribution_id = $this->input->post('contribution_id');
            				$member_type_id = $this->input->post('member_type_id');
            				$contribution_payments = array();
            				if($member_type_id == 2){
            					$members = $this->members_m->get_active_group_member_options(); 
	            				foreach ($members as $member_id => $member_name) {
	            					$contribution_payment = new stdClass();
	            					$contribution_payment->deposit_date = $deposit_date;
	            					$contribution_payment->member_id = $member_id;
	            					$contribution_payment->contribution_id = $contribution_id;
	            					$contribution_payment->account_id = $account_id;
	            					$contribution_payment->amount = $amount;
	            					$contribution_payment->deposit_method = $deposit_method;
	            					$contribution_payment->send_sms_notification = 0;
	                            	$contribution_payment->send_email_notification = 0;
	                            	$contribution_payment->description = '';
	                            	$contribution_payments[] = $contribution_payment;
	            				}
            				}else{
            					$individual_payments = $this->input->post('individual_payments');
            					foreach ($individual_payments as $member_id => $amount) {
            						$contribution_payment = new stdClass();
	            					$contribution_payment->deposit_date = $deposit_date;
	            					$contribution_payment->member_id = $member_id;
	            					$contribution_payment->contribution_id = $contribution_id;
	            					$contribution_payment->account_id = $account_id;
	            					$contribution_payment->amount = $amount;
	            					$contribution_payment->deposit_method = $deposit_method;
	            					$contribution_payment->send_sms_notification = 0;
	                            	$contribution_payment->send_email_notification = 0;
	                            	$contribution_payment->description = '';
	                            	$contribution_payments[] = $contribution_payment;
            					}
            				}
            				if($this->transactions->record_group_contribution_payments($this->group->id,$contribution_payments)){
		                        $response = array(
		                        	'status' => 1,
		                        	'message' => 'Contributions recorded successfully.',
		                        );
		                    }else{
		                    	$response = array(
		                        	'status' => 0,
		                        	'message' => 'Something went wrong while recording the contribution payments. Error: '.$this->session->flashdata('error'),
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
	                        'message' => 'You are not allowed to perform this request. For group admins only',
	                        'time' => time(),
	                    );
            		}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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
        if($response['status']==0){
        	update_request_id($this->request->request_id);
        }
        echo encrypt_json_encode(array('response'=>$response));
	}

	function record_fine_payments(){
		$fine_payments = array();
		$deposit_dates = array();
		$member_ids = array();
		$fine_category_ids = array();
		$account_ids = array();
		$amounts = array();
		$deposit_methods = array();
		$descriptions = array();
		foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
            	if(is_array($value)){
            		foreach ($value as $value_key => $value_value) {
            			if($value_value->member_id){
            				$member_ids[$value_key] = $value_value->member_id;
            			}
            			if($value_value->deposit_date){
            				$deposit_dates[$value_key] = $value_value->deposit_date;
            			}
            			if($value_value->fine_category_id){
            				$fine_category_ids[$value_key] = $value_value->fine_category_id;
            			}
            			if($value_value->account_id){
            				$account_ids[$value_key] = $value_value->account_id;
            			}
            			if($value_value->amount){
            				$amounts[$value_key] = currency($value_value->amount);
            			}
            			if($value_value->deposit_method){
            				$deposit_methods[$value_key] = ($value_value->deposit_method);
            			}
            			if($value_value->description){
            				$descriptions[$value_key] = ($value_value->description);
            			}
            			$fine_payments[$value_key] = array($value_key=>'item');
            		}
            	}else{
            		$_POST[$key] = $value;
            	}
            }
        }
        $_POST['deposit_dates'] = $deposit_dates;
        $_POST['member_ids'] = $member_ids;
        $_POST['fine_category_ids'] = $fine_category_ids;
        $_POST['account_ids'] = $account_ids;
        $_POST['amounts'] = $amounts;
        $_POST['deposit_methods'] = $deposit_methods;
        $_POST['descriptions'] = $descriptions;
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
            	if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
            		if($this->member->group_role_id || $this->member->is_admin){
            			$deposit_dates = $this->input->post('deposit_dates');
	        			$member_ids = $this->input->post('member_ids');
	        			$fine_category_ids = $this->input->post('fine_category_ids');
	        			$account_ids = $this->input->post('account_ids');
	        			$amounts = $this->input->post('amounts');
	        			$deposit_methods = $this->input->post('deposit_methods');
	        			$descriptions = $this->input->post('descriptions');
						if(empty($fine_payments)){
							$response = array(
			    				'status' => 0,
			    				'time' => time(),
			    				'message' => 'Error fine payments array empty.',
			    			);
						}else{
			    			$entries_are_valid = TRUE;
		                    foreach($fine_payments as $key => $fine_payment):
		                    	if(isset($deposit_dates[$key])&&isset($member_ids[$key])&&isset($fine_category_ids[$key])&&isset($account_ids[$key])&&isset($amounts[$key])&&isset($deposit_methods[$key])):    
		                            //Deposit dates
		                            if($deposit_dates[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{

		                            }
		                            //Members
		                            if($member_ids[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{
		                                if(is_numeric($member_ids[$key])){

		                                }else{
		                                    $entries_are_valid = FALSE;
		                                }
		                            }
		                            //Fine categories
		                            if($fine_category_ids[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{

		                            }
		                             //Accounts
		                            if($account_ids[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{

		                            }
		                            //Deposit Method
		                            if($deposit_methods[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{
		                                if(is_numeric($deposit_methods[$key])){

		                                }else{
		                                    $entries_are_valid = FALSE;
		                                }
		                            }
		                            //amounts
		                            if($amounts[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{
		                                if(valid_currency($amounts[$key])){

		                                }else{
		                                    $entries_are_valid = FALSE; 
		                                }
		                            }
		                        endif;
		                    endforeach;
		                    if($entries_are_valid){
		                    	$transaction_result = TRUE;
		                    	foreach($fine_payments as $key => $fine_payment):
		                    		$deposit_date = strtotime($deposit_dates[$key]);
									$amount = valid_currency($amounts[$key]);
									$send_sms_notification = $this->input->post('send_sms_notification')?:0;
									$send_email_notification = $this->input->post('send_email_notification')?:0;
									$description = isset($descriptions[$key])?$descriptions[$key]:'';
			                    	if($this->transactions->record_fine_payment($this->group->id,$deposit_date,$member_ids[$key],$fine_category_ids[$key],$account_ids[$key],$deposit_methods[$key],$description,$amount,$send_sms_notification,$send_email_notification)){

			                    	}else{
			                    		$transaction_result = FALSE;
			                    	}
		                    	endforeach;
		                    	if($transaction_result){
		                        	$response = array(
					    				'status' => 1,
					    				'time' => time(),
					    				'message' => 'Successfully added transactions',
					    			);
		                        }else{
		                        	$response = array(
					    				'status' => 0,
					    				'time' => time(),
					    				'message' => $this->session->flashdata('error'),
					    			);
		                        }
		                    }else{
		                    	$response = array(
				    				'status' => 0,
				    				'time' => time(),
				    				'message' => 'Error data validation errors, some values are missing.',
				    			);
		                    }
						}
					}else{
            			$response = array(
	                        'status' => 0,
	                        'message' => 'You are not allowed to perform this request. For group admins only',
	                        'time' => time(),
	                    );
            		}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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
        if($response['status']==0){
        	update_request_id($this->request->request_id);
        }
        echo encrypt_json_encode(array('response'=>$response));
	}

	function new_record_fine_payments(){
		$response = array();
		foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
            	if(is_object($value)){
            		$_POST[$key] = (array)$value;
            	}else{
            		$_POST[$key] = $value;
            	}
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
            	if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
            		if($this->member->group_role_id || $this->member->is_admin){
            			$this->form_validation->set_rules($this->new_record_fine_payments_validation_rules);
            			if($this->form_validation->run()){
            				$deposit_date = $this->input->post('deposit_date');
            				$deposit_method = $this->input->post('deposit_method');
            				$amount = currency($this->input->post('amount'));
            				$account_id = $this->input->post('account_id');
            				$fine_category_id = $this->input->post('fine_category_id');
            				$member_type_id = $this->input->post('member_type_id');
            				$description = $this->input->post('description');
            				$fine_payments = array();
            				$transaction_result = TRUE;
            				if($member_type_id==1){
            					$individual_payments = $this->input->post('individual_payments');
            					foreach ($individual_payments as $member_id => $amount) {
            						if($this->transactions->record_fine_payment($this->group->id,$deposit_date,$member_id,$fine_category_id,$account_id,$deposit_method,$description,$amount,0,0)){
			                    	}else{
			                    		$transaction_result = FALSE;
			                    	}
            					}
            				}else{
            					$members = $this->members_m->get_active_group_member_options(); 
            					foreach ($members as $member_id => $member_name) {
            						if($this->transactions->record_fine_payment($this->group->id,$deposit_date,$member_id,$fine_category_id,$account_id,$deposit_method,$description,$amount,0,0)){
			                    	}else{
			                    		$transaction_result = FALSE;
			                    	}
            					}
            				}

            				if($transaction_result){
	                        	$response = array(
				    				'status' => 1,
				    				'message' => 'Successfully added transactions',
				    			);
	                        }else{
	                        	$response = array(
				    				'status' => 0,
				    				'message' => $this->session->flashdata('error'),
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
	                        'message' => 'You are not allowed to perform this request. For group admins only',
	                        'time' => time(),
	                    );
            		}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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
        if($response['status']==0){
        	update_request_id($this->request->request_id);
        }
        echo encrypt_json_encode(array('response'=>$response));
	}

	function record_loan_repayments(){
		$loan_repayments = array();
		$deposit_dates = array();
		$member_ids = array();
		$loan_ids = array();
		$account_ids = array();
		$amounts = array();
		$deposit_methods = array();
		$descriptions = array();
		foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
            	if(is_array($value)){
            		foreach ($value as $key_value => $value_value) {
            			if($value_value->member_id){
            				$member_ids[$key_value] = $value_value->member_id;
            			}
            			if($value_value->deposit_date){
            				$deposit_dates[$key_value] = $value_value->deposit_date;
            			}
            			if($value_value->loan_id){
            				$loan_ids[$key_value] = $value_value->loan_id;
            			}
            			if($value_value->account_id){
            				$account_ids[$key_value] = $value_value->account_id;
            			}
            			if($value_value->amount){
            				$amounts[$key_value] = currency($value_value->amount);
            			}
            			if($value_value->deposit_method){
            				$deposit_methods[$key_value] = $value_value->deposit_method;
            			}
            			if($value_value->description){
            				$descriptions[$key_value] = $value_value->description;
            			}
            			$loan_repayments[$key_value] = array($key_value=>'item');
            		}
            	}
                $_POST[$key] = $value;
            }
        }
        $_POST['deposit_dates'] = $deposit_dates;
        $_POST['member_ids'] = $member_ids;
        $_POST['loan_ids'] = $loan_ids;
        $_POST['account_ids'] = $account_ids;
        $_POST['amounts'] = $amounts;
        $_POST['deposit_methods'] = $deposit_methods;
        $_POST['descriptions'] = $descriptions;
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
            	if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
            		if($this->member->group_role_id || $this->member->is_admin){
            			if(empty($loan_repayments)){
							$response = array(
			    				'status' => 0,
			    				'time' => time(),
			    				'message' => 'Error loan repayments array empty.',
			    			);
						}else{
							$deposit_dates = $this->input->post('deposit_dates');
							$member_ids = $this->input->post('member_ids');
							$loan_ids = $this->input->post('loan_ids');
							$account_ids = $this->input->post('account_ids');
							$amounts = $this->input->post('amounts');
							$deposit_methods = $this->input->post('deposit_methods');
							$descriptions = $this->input->post('descriptions');
							$entries_are_valid = TRUE;
		                    foreach($loan_repayments as $key=>$loan_repayment):
		                    	if(isset($deposit_dates[$key])&&isset($member_ids[$key])&&isset($loan_ids[$key])&&isset($account_ids[$key])&&isset($amounts[$key])&&isset($deposit_methods[$key])):    
		                            //Deposit dates
		                            if($deposit_dates[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{

		                            }
		                            //Members
		                            if($member_ids[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{
		                                if(is_numeric($member_ids[$key])){
		                                   
		                                }else{
		                                    $entries_are_valid = FALSE;
		                                }
		                            }
		                            //Loans
		                            if($loan_ids[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{
		                                if(is_numeric($loan_ids[$key])){

		                                }else{
		                                    $entries_are_valid = FALSE;
		                                }
		                            }
		                             //Accounts
		                            if($account_ids[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{

		                            }
		                            //Deposit Method
		                            if($deposit_methods[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{
		                                if(is_numeric($deposit_methods[$key])){
		                                }else{
		                                	$entries_are_valid = FALSE;
		                                }
		                            }
		                            //amounts
		                            if($amounts[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{
		                                if(valid_currency($amounts[$key])){

		                                }else{
		                                    $entries_are_valid = FALSE; 
		                                }
		                            }
		                        endif;
		                    endforeach;
		                    if($entries_are_valid){
		                    	$transaction_result = TRUE;
		                    	foreach($loan_repayments as $key => $loan_repayment):
		                    		$deposit_date = strtotime($deposit_dates[$key]);
									$amount = valid_currency($amounts[$key]);
									$send_sms_notification = $this->input->post('send_sms_notification')?:0;
									$send_email_notification = $this->input->post('send_email_notification')?:0;
									$description = isset($descriptions[$key])?$descriptions[$key]:'';
									$member = $this->members_m->get_group_member($member_ids[$key],$this->group->id);
									if($this->loan->record_loan_repayment($this->group->id,$deposit_date,$member,$loan_ids[$key],$account_ids[$key],$deposit_methods[$key],$description,$amount,$send_sms_notification,$send_email_notification,$this->user)){

									}else{
										$transaction_result = FALSE;
									}
			                    endforeach;
			                    if($transaction_result){
		                        	$response = array(
					    				'status' => 1,
					    				'time' => time(),
					    				'message' => 'Successfully added transactions',
					    			);
		                        }else{
		                        	$response = array(
					    				'status' => 0,
					    				'time' => time(),
					    				'message' => 'Error adding transactions.',
					    			);
		                        }
		                    }else{
		                    	$response = array(
				    				'status' => 0,
				    				'time' => time(),
				    				'message' => 'Error data validation errors, some values are missing.',
				    			);
		                    }
						}
					}else{
            			$response = array(
	                        'status' => 0,
	                        'message' => 'You are not allowed to perform this request. For group admins only',
	                        'time' => time(),
	                    );
            		}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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
        if($response['status']==0){
        	update_request_id($this->request->request_id);
        }
        echo encrypt_json_encode(array('response'=>$response));	
	}

	function record_miscellaneous_payments(){
		$deposit_dates = array();
		$member_ids = array();
		$descriptions = array();
		$account_ids = array();
		$amounts = array();
		$deposit_methods = array();
		foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
            	if(is_array($value)){
            		foreach ($value as $key_value => $value_value) {
            			if($value_value->deposit_method){
            				$deposit_methods[$key_value] = $value_value->deposit_method;
            			}
            			if($value_value->amount){
            				$amounts[$key_value] = currency($value_value->amount);
            			}
            			if($value_value->account_id){
            				$account_ids[$key_value] = $value_value->account_id;
            			}
            			if($value_value->description){
            				$descriptions[$key_value] = $value_value->description;
            			}
            			if($value_value->member_id){
            				$member_ids[$key_value] = $value_value->member_id;
            			}
            			if($value_value->deposit_date){
            				$deposit_dates[$key_value] = $value_value->deposit_date;
            			}
            			$miscellaneous_payments[$key_value] = array($key_value => 'item');
            		}
            	}else{
            		$_POST[$key] = $value;
            	}
            }
        }
        $_POST['deposit_methods'] = $deposit_methods;
        $_POST['amounts'] = $amounts;
        $_POST['account_ids'] = $account_ids;
        $_POST['descriptions'] = $descriptions;
        $_POST['member_ids'] = $member_ids;
        $_POST['deposit_dates'] = $deposit_dates;
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
            	if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
            		if($this->member->group_role_id || $this->member->is_admin){
            			$deposit_methods = $this->input->post('deposit_methods');
	            		$amounts = $this->input->post('amounts');
	            		$account_ids = $this->input->post('account_ids');
	            		$descriptions = $this->input->post('descriptions');
	            		$member_ids = $this->input->post('member_ids');
	            		$deposit_dates = $this->input->post('deposit_dates');
						if(empty($miscellaneous_payments)){
							$response = array(
			    				'status' => 0,
			    				'time' => time(),
			    				'message' => 'Error miscellaneous payments array empty.',
			    			);
						}else{
							$entries_are_valid = TRUE;
		                    foreach($miscellaneous_payments as $key => $miscellaneous_payment):
		                    	if(isset($deposit_dates[$key])&&isset($member_ids[$key])&&isset($descriptions[$key])&&isset($account_ids[$key])&&isset($amounts[$key])&&isset($deposit_methods)):    
		                            //Deposit dates
		                            if($deposit_dates[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{

		                            }
		                            //Members
		                            if($member_ids[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{
		                                if(is_numeric($member_ids[$key])){

		                                }else{
		                                    $entries_are_valid = FALSE;
		                                }
		                            }
		                            //Fine categories
		                            if($descriptions[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{

		                            }
		                             //Accounts
		                            if($account_ids[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{

		                            }
		                            //Deposit Method
		                            if($deposit_methods[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{
		                                if(is_numeric($deposit_methods[$key])){

		                                }else{
		                                    $entries_are_valid = FALSE;
		                                }
		                            }
		                            //amounts
		                            if($amounts[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{
		                                if(valid_currency($amounts[$key])){

		                                }else{
		                                    $entries_are_valid = FALSE; 
		                                }
		                            }
		                        endif;
		                    endforeach;
		                    if($entries_are_valid){
		                    	$transaction_result = TRUE;
		                    	foreach($miscellaneous_payments as $key => $miscellaneous_payment):
		                    		$deposit_date = strtotime($deposit_dates[$key]);
									$amount = valid_currency($amounts[$key]);
									$send_sms_notification = $this->input->post('send_sms_notification');
									$send_email_notification = $this->input->post('send_email_notification');
									$description = isset($descriptions[$key])?$descriptions[$key]:'';
									if($this->transactions->record_miscellaneous_payment($this->group->id,$deposit_date,$member_ids[$key],$account_ids[$key],$deposit_methods[$key],$description,$amount,$send_sms_notification,$send_email_notification)){

		                    		}else{
		                    			$transaction_result = FALSE;
		                    		}
		                    	endforeach;
		                    	if($transaction_result){
		                        	$response = array(
					    				'status' => 1,
					    				'time' => time(),
					    				'message' => 'Successfully added transactions',
					    			);
		                        }else{
		                        	$response = array(
					    				'status' => 0,
					    				'time' => time(),
					    				'message' => 'Error adding transactions.',
					    			);
		                        }
		                    }else{
		                    	$response = array(
				    				'status' => 0,
				    				'time' => time(),
				    				'message' => 'Error data validation errors, some values are missing.',
				    			);
		                    }
						}
					}else{
            			$response = array(
	                        'status' => 0,
	                        'message' => 'You are not allowed to perform this request. For group admins only',
	                        'time' => time(),
	                    );
            		}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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
        if($response['status']==0){
        	update_request_id($this->request->request_id);
        }
        echo encrypt_json_encode(array('response'=>$response));	
    }

    function new_record_miscellaneous_payments(){
    	$response = array();
		foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
            	if(is_object($value)){
            		$_POST[$key] = (array)$value;
            	}else{
            		$_POST[$key] = $value;
            	}
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
            	if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
            		if($this->member->group_role_id || $this->member->is_admin){
            			$this->form_validation->set_rules($this->new_record_miscellaneous_payments_validation_rules);
            			if($this->form_validation->run()){
            				$deposit_date = $this->input->post('deposit_date');
            				$deposit_method = $this->input->post('deposit_method');
            				$amount = currency($this->input->post('amount'));
            				$account_id = $this->input->post('account_id');
            				$description = $this->input->post('description');
            				$member_id = $this->input->post('member_id');
            				if($this->transactions->record_miscellaneous_payment($this->group->id,$deposit_date,$member_id,$account_id,$deposit_method,$description,$amount,0,0)){
                             	$response = array(
				    				'status' => 1,
				    				'message' => 'Miscellaneous payment successfully recorded',
				    			);      
                            }else{
                                $response = array(
				    				'status' => 0,
				    				'message' => $this->session->flashdata('error'),
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
	                        'message' => 'You are not allowed to perform this request. For group admins only',
	                        'time' => time(),
	                    );
            		}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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
        if($response['status']==0){
        	update_request_id($this->request->request_id);
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function record_income(){
    	$deposit_dates = array();
    	$depositor_ids = array();
    	$income_category_ids = array();
    	$account_ids = array();
    	$amounts = array();
    	$deposit_methods = array();
    	$descriptions = array();
    	$income_payments = array();
		foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
            	if(is_array($value)){
            		foreach ($value as $key_value => $value_value) {
            			if($value_value->deposit_date){
            				$deposit_dates[$key_value] = $value_value->deposit_date;
            			}
            			if($value_value->depositor_id){
            				$depositor_ids[$key_value] = $value_value->depositor_id;
            			}
            			if($value_value->income_category_id){
            				$income_category_ids[$key_value] = $value_value->income_category_id;
            			}
            			if($value_value->amount){
            				$amounts[$key_value] = currency($value_value->amount);
            			}
            			if($value_value->deposit_method){
            				$deposit_methods[$key_value] = $value_value->deposit_method;
            			}
            			if($value_value->account_id){
            				$account_ids[$key_value] = $value_value->account_id;
            			}
            			if($value_value->description){
            				$descriptions[$key_value] = $value_value->description;
            			}
            			$income_payments[$key_value] = array($key_value => 'item');
            		}
            	}else{
            		$_POST[$key] = $value;
            	}
            }
        }
        $_POST['deposit_dates'] = $deposit_dates;
        $_POST['depositor_ids'] = $depositor_ids;
        $_POST['income_category_ids'] = $income_category_ids;
        $_POST['amounts'] = $amounts;
        $_POST['deposit_methods'] = $deposit_methods;
        $_POST['account_ids'] = $account_ids;
        $_POST['descriptions'] = $descriptions;
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
            	if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
            		if($this->member->group_role_id || $this->member->is_admin){
            			$deposit_dates = $this->input->post('deposit_dates');
	            		$depositor_ids = $this->input->post('depositor_ids');
	            		$income_category_ids = $this->input->post('income_category_ids');
	            		$amounts = $this->input->post('amounts');
	            		$deposit_methods = $this->input->post('deposit_methods');
	            		$account_ids = $this->input->post('account_ids');
	            		$descriptions = $this->input->post('descriptions');
						if(empty($income_payments)){
							$response = array(
			    				'status' => 0,
			    				'time' => time(),
			    				'message' => 'Error income payments array empty.',
			    			);
						}else{
							$entries_are_valid = TRUE;
		                    foreach($income_payments as $key => $income_payment):
		                    	if(isset($deposit_dates[$key])&&isset($depositor_ids[$key])&&isset($income_category_ids[$key])&&isset($account_ids[$key])&&isset($amounts[$key])&&isset($deposit_methods[$key])):    
		                            //Deposit dates
		                            if($deposit_dates[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{

		                            }
		                            //Members
		                            if($depositor_ids[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{
		                                if(is_numeric($depositor_ids[$key])){

		                                }else{
		                                    $entries_are_valid = FALSE;
		                                }
		                            }
		                            //Fine categories
		                            if($income_category_ids[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{

		                            }
		                             //Accounts
		                            if($account_ids[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{

		                            }
		                            //Deposit Method
		                            if($deposit_methods[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{
		                                if(is_numeric($deposit_methods[$key])){

		                                }else{
		                                    $entries_are_valid = FALSE;
		                                }
		                            }
		                            //amounts
		                            if($amounts[$key]==''){
		                                $entries_are_valid = FALSE;
		                            }else{
		                                if(valid_currency($amounts[$key])){

		                                }else{
		                                    $entries_are_valid = FALSE; 
		                                }
		                            }
		                        endif;
		                	endforeach;
		                	if($entries_are_valid){
		                		$transaction_result = TRUE;
		                		foreach($income_payments as $key => $income_payment):
		                			$deposit_date = strtotime($deposit_dates[$key]);
									$amount = valid_currency($amounts[$key]);
									$depositor_id = $depositor_ids[$key];
									$income_category_id = $income_category_ids[$key];
									$account_id = $account_ids[$key];
									$deposit_method = $deposit_methods[$key];
									$description = isset($descriptions[$key])?$descriptions[$key]:'';
		                			if($this->transactions->record_income_deposit($this->group->id,$deposit_date,$depositor_id,$income_category_id,$account_id,$deposit_method,$description,$amount)){
		                                
		                            }else{
		                                $transaction_result = FALSE;
		                            }
		                		endforeach;
		                		if($transaction_result){
		                        	$response = array(
					    				'status' => 1,
					    				'time' => time(),
					    				'message' => 'Successfully added transactions',
					    			);
		                        }else{
		                        	$response = array(
					    				'status' => 0,
					    				'time' => time(),
					    				'message' => 'Error adding transactions: '.$this->session->flashdata('error'),
					    			);
		                        }
		                	}else{
		                		$response = array(
				    				'status' => 0,
				    				'time' => time(),
				    				'message' => 'Error data validation errors, some values are missing.',
				    			);
		                	}
						}
					}else{
            			$response = array(
	                        'status' => 0,
	                        'message' => 'You are not allowed to perform this request. For group admins only',
	                        'time' => time(),
	                    );
            		}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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
        if($response['status']==0){
        	update_request_id($this->request->request_id);
        }
        echo encrypt_json_encode(array('response'=>$response));		
    }

    function new_record_income_payment(){
    	$response = array();
		foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
            	if(is_object($value)){
            		$_POST[$key] = (array)$value;
            	}else{
            		$_POST[$key] = $value;
            	}
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
            	if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
            		if($this->member->group_role_id || $this->member->is_admin){
            			$this->form_validation->set_rules($this->new_record_income_payments_validation_rules);
            			if($this->form_validation->run()){
            				$deposit_date = $this->input->post('deposit_date');
            				$deposit_method = $this->input->post('deposit_method');
            				$amount = currency($this->input->post('amount'));
            				$account_id = $this->input->post('account_id');
            				$income_category_id = $this->input->post('income_category_id');
            				$description = $this->input->post('description');
            				$depositor_id = $this->input->post('depositor_id');

            				if($this->transactions->record_income_deposit($this->group->id,$deposit_date,$depositor_id,$income_category_id,$account_id,$deposit_method,$description,$amount)){
            					$response = array(
				    				'status' => 1,
				    				'message' => 'Income payment successfully recorded',
				    			);      
                            }else{
                                $response = array(
				    				'status' => 0,
				    				'message' => $this->session->flashdata('error'),
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
	                        'message' => 'You are not allowed to perform this request. For group admins only',
	                        'time' => time(),
	                    );
            		}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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
        if($response['status']==0){
        	update_request_id($this->request->request_id);
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function record_bank_loan(){
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
            		if($this->member->group_role_id || $this->member->is_admin){
            			$this->form_validation->set_rules($this->bank_loan_validation_rules);
						if($this->form_validation->run()){
							if($this->input->post('loan_balance')<=0 ){
				                $is_fully_paid = 1;
				            }else{
				                $is_fully_paid='';
				            }
				            $description = $this->input->post('description');
				            $amount_loaned = $this->input->post('amount_loaned');
				            $total_loan_amount_payable = $this->input->post('total_loan_amount_payable');
				            $loan_balance = $this->input->post('loan_balance');
				            $loan_start_date = strtotime($this->input->post('loan_start_date'));
				            $loan_end_date = strtotime($this->input->post('loan_end_date'));
				            $account_id = $this->input->post('account_id');
				            $transaction_id = $this->transactions->create_bank_loan(
					            	$this->group->id,
					            	$description,
					            	$amount_loaned,
					            	$total_loan_amount_payable,
					            	$loan_balance,
					            	$loan_start_date,
					            	$loan_end_date,
					            	$account_id,
					            	$is_fully_paid
					            );
				            if($transaction_id){
				                $response = array(
					    				'status' => 1,
					    				'time' => time(),
					    				'message' => '',
					    				'message' => 'Successfully added transaction'
					    			);
				            }else{
				                $response = array(
				    				'status' => 0,
				    				'time' => time(),
				    				'message' => 'Error creating transactions',
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
	                        'message' => 'You are not allowed to perform this request. For group admins only',
	                        'time' => time(),
	                    );
            		}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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
        if($response['status']==0){
        	update_request_id($this->request->request_id);
        }
        echo encrypt_json_encode(array('response'=>$response));    	
    }

    function void(){
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
            		if($this->member->group_role_id || $this->member->is_admin){
            			$id = trim($this->input->post('id'))?:0;
						if($id){
							$post = $this->deposits_m->get_group_deposit($id,$this->group->id);
							if($post){
								if($this->transactions->void_group_deposit($post->id,$post,TRUE,$this->group->id,$this->user)){
									$response = array(
					    				'status' => 1,
					    				'message' => 'success',
					    			);
								}else{
									$error = $this->session->flashdata('error');
                                    $response = array(
                                        'status' => 0,
                                        'time' => time(),
                                        'message' => $error?:'Could not complete voiding transaction. Try again later',
                                    );
								}
							}else{
								$response = array(
				    				'status' => 0,
				    				'message' => 'Error occured while voiding',
				    			);
							}
						}else{
							$response = array(
			    				'status' => 0,
			    				'message' => 'Invalid deposit tag',
			    			);
						}
					}else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
            );
        }
        echo encrypt_json_encode(array('response'=>$response));	    
    }

    function void_bank_loan(){
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
            		if($this->member->group_role_id || $this->member->is_admin){
            			$id = $this->input->post('bank_loan_id')?:0;
	                    if($id){
	                        $post = $this->bank_loans_m->get($id,$group->id);
	                        if($post){
	                        	$deposit = $this->deposits_m->get_bank_loan_disbursement_deposit_by_bank_loan_id($id,$group->id);
	                        	if($deposit){
	                        		if($this->transactions->void_group_deposit($deposit->id,$deposit,TRUE,$this->group->id)){
							            $response = array(
	                                        'status' => 1,
	                                        'time' => time(),
	                                    );
							        }else{
							            $response = array(
	                                        'status' => 0,
	                                        'time' => time(),
	                                        'message' => 'Error occured while voiding. Try again later',
	                                    );
							        }
	                        	}else{
	                        		$response = array(
	                                    'status' => 0,
	                                    'time' => time(),
	                                    'message' => 'Bank loan details unavailable',
	                                );
	                        	}
	                        }else{
	                        	$response = array(
	                                'status' => 0,
	                                'time' => time(),
	                                'message' => 'Bank loan details unavailable',
	                            );
	                        }
	                    }else{
	                        $response = array(
	                            'status' => 0,
	                            'time' => time(),
	                            'message' => 'Loan details are missing',
	                        );
	                    }
					}else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function void_contribution_transfer(){
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
            		if($this->member->group_role_id || $this->member->is_admin){
            			$id = $this->input->post('id')?:0;
	                    if($id){
	                        $post = $this->deposits_m->get_group_contribution_transfer($id,$this->group->id);
	                        if($post){
	                        	if($this->transactions->void_contribution_transfer($id,$this->group->id)){
						            $response = array(
	                                    'status' => 1,
	                                    'time' => time(),
	                                );
						        }else{
						            $response = array(
	                                    'status' => 0,
	                                    'time' => time(),
	                                    'message' => 'Error occured while voiding contribution transfer. Try again later',
	                                );
						        }
	                        }else{
	                        	$response = array(
	                                'status' => 0,
	                                'time' => time(),
	                                'message' => 'Could not find contribution transfer details',
	                            );
	                        }
	                    }else{
	                        $response = array(
	                            'status' => 0,
	                            'time' => time(),
	                            'message' => 'Contribution transfer details are missing',
	                        );
	                    }
					}else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function record_contribution_transfer(){
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
            		if($this->member->group_role_id || $this->member->is_admin){
            			$entries_are_valid = TRUE;
	            		$contribution_from_id = $this->input->post('contribution_from_id');
	            		$transfer_date = $this->input->post('transfer_date');
	            		$contribution_to_id = $this->input->post('contribution_to_id');
	            		$member_id = $this->input->post('member_id');
	            		$amount = $this->input->post('amount');
	            		$loan_from_id = $this->input->post('loan_from_id');
	            		$transfer_to = $this->input->post('transfer_to');
	            		$fine_category_to_id = $this->input->post('fine_category_to_id');
	            		$loan_to_id = $this->input->post('loan_to_id');
	            		$member_to_id = $this->input->post('member_to_id');
	            		$member_transfer_to = $this->input->post('member_transfer_to');
	            		$member_contribution_to_id = $this->input->post('member_contribution_to_id');
	            		$member_fine_category_to_id = $this->input->post('member_fine_category_to_id');
	            		$member_loan_to_id = $this->input->post('member_loan_to_id');
	            		$description = $this->input->post('description');
	            		$error_massage = '';
		    			if(trim($transfer_date)){

		    			}else{
		    				$error_massage = "Transafer date is missing";
		    				$entries_are_valid = FALSE;
		    			}
		    			if(trim($contribution_from_id)){
		    				if($transfer_to == 1){
			    				if(trim($contribution_from_id)==trim($contribution_to_id)){
			    					$error_massage = "Contribution from should not be the same as contribution to";
			    					$entries_are_valid = FALSE;
			    				}
			    			}
		    			}else{
		    				$error_massage = "Contribution from is required";
		    				$entries_are_valid = FALSE;
		    			}

		    			if(trim($contribution_from_id)){
			    			if(is_numeric(trim($contribution_from_id))){

			    			}else{
			    				$error_massage = "Contribution from id should be numeric";
								$entries_are_valid = FALSE;
			    			}
			    		}

			    		if(trim($member_id)){
			    			if(is_numeric(trim($member_id))){

			    			}else{
			    				$error_massage = "Member id should be numeric";
			    				$entries_are_valid = FALSE;
			    			}
			    		}else{
			    			$entries_are_valid = FALSE;
			    		}

			    		if(trim($amount)){
			    			if(valid_currency(trim($amount))){

			    			}else{
			    				$error_massage = "Valid amount is required";
								$entries_are_valid = FALSE;
			    			}
			    		}else{
			    			$error_massage = "Amount is required";
			    			$entries_are_valid = FALSE;
			    		}

			    		if(trim($contribution_from_id)=="loan"){
			    			if(trim($loan_from_id)){
			    				if(is_numeric(trim($loan_from_id))){
							        $amount_to_transfer = currency(trim($amount));
							        if($loan_from_id){
							            $amount = $this->loan_repayments_m->get_loan_total_payments($loan_from_id);
							            $transfers_out = $this->loan_invoices_m->get_total_loan_transfers_out($loan_from_id)?:0;
							            $amount = $amount-$transfers_out;
							            if($amount){
							                if($amount_to_transfer>$amount){
							                	$error_massage = "Amount to transfer should be greater than the Loan amount ";
							                    $entries_are_valid = FALSE;
							                }else{

							                }
							            }else{
							            	$error_massage = "Loan amount could not be computed";
							                $entries_are_valid = FALSE;
							            }
							        }else{
							        	$error_massage = "Loan from id is required";
							            $entries_are_valid = FALSE;
							        }
			    				}else{
			    					$error_massage = "Loan id should be numeric";
			    					$entries_are_valid = FALSE;
			    				}
			    			}else{
			    				$error_massage = "Loan from is required";
			    				$entries_are_valid = FALSE;
			    			}
			    		}

			    		$transfer_to = trim($transfer_to);
			    		if($transfer_to==1){
			    			$contribution_to_id = trim($contribution_to_id);
			    			if($contribution_to_id){
			    				if(is_numeric($contribution_to_id)){

			    				}else{
			    					$error_massage = "Contribution to id should be numeric";
									$entries_are_valid = FALSE;
			    				}
			    			}else{
			    				$error_massage = "Contribution to id is required";
			    				$entries_are_valid = FALSE;
			    			}
			    		}else if($transfer_to==2){
			    			if($fine_category_to_id){

			    			}else{
			    				$error_massage = "Fine category id is required";
			    				$entries_are_valid = FALSE;
			    			}
			    		}else if($transfer_to==3){
			    			if($loan_to_id){
			    				if(is_numeric($loan_to_id)){
			    					$loan_from_id = trim($loan_from_id);
			    					if($loan_from_id==$loan_to_id){
			    						$entries_are_valid = FALSE;
			    					}
			    				}else{
			    					$error_massage = "Loan to id should be numeric";
			    					$entries_are_valid = FALSE;
			    				}
			    			}else{
			    				$error_massage = "Loan to id is required";
			    				$entries_are_valid = FALSE;
			    			}
			    		}else if($transfer_to==4){
			    			if($member_to_id){
			    				if(is_numeric($member_to_id)){
			    					if($member_id==$member_to_id){
			    						$error_massage = "member to id should not be the same as member id";
										$entries_are_valid = FALSE;
			    					}
			    				}else{
			    					$error_massage = "Member to id should be numeric";
									$entries_are_valid = FALSE;
			    				}
			    			}else{
			    				$error_massage = "Memebr to id is required";
			    				$entries_are_valid = FALSE;
			    			}
			    			if($member_transfer_to){
			    				if(is_numeric($member_transfer_to)){

			    				}else{
			    					$error_massage = "Member transfer to should be numeric";
			    					$entries_are_valid = FALSE;
			    				}
			    			}else{
			    				$error_massage = "Member transfer to is required";
			    				$entries_are_valid = FALSE;
			    			}
			    		}
			    		if($member_transfer_to==1){
							if($member_contribution_to_id){
								if(is_numeric($member_contribution_to_id)){

								}else{
									$error_massage = "Member contribution to id should be numeric ";
									$entries_are_valid = FALSE;
								}
							}else{
								$error_massage = "Member contribution to id is required";
								$entries_are_valid = FALSE;
							}
			    		}else if($member_transfer_to==2){
			    			if($member_fine_category_to_id){

			    			}else{
			    				$error_massage = "Member fine category to id is required";
			    				$entries_are_valid = FALSE;
			    			}
			    		}else if($member_transfer_to==3){
			    			if($member_loan_to_id){
			    				if(is_numeric($member_loan_to_id)){

			    				}else{
			    					$error_massage = "Member loan to should be numeric required";
			    					$entries_are_valid = FALSE;
			    				}
			    			}else{
			    				$error_massage = "Member  loan to id is required ";
			    				$entries_are_valid = FALSE;
			    			}
			    		}
			    		
		    			if($entries_are_valid){
				            if($this->transactions->record_contribution_transfer($this->group->id,$transfer_date,$contribution_from_id,$transfer_to,$contribution_to_id,$fine_category_to_id,$member_id,$amount,
				                $description,$loan_from_id,
				                $loan_to_id,
				                $member_to_id,
				                $member_transfer_to,
				                $member_contribution_to_id,
				                $member_fine_category_to_id,
				                $member_loan_to_id
				                )){
				                $response = array(
									'status' => 1,
									'time' => time(),
									'message' => 'Contribution transfer recorded successfully',
								);
				            }else{
				                $response = array(
									'status' => 0,
									'time' => time(),
									'message' => 'Something went wrong when recording the contribution transfer',
								);
				            }
						}else{
							$response = array(
								'status' => 0,
								'time' => time(),
								'message' => 'Entries are invalid: ' .$error_massage,
							);
						}
					}else{
            			$response = array(
	                        'status' => 0,
	                        'message' => 'You are not allowed to perform this request. For group admins only',
	                        'time' => time(),
	                    );
            		}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
            );
        }
        if($response['status']==0){
        	update_request_id($this->request->request_id);
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function make_group_payment(){
        $deposit_type = array();
        $contribution_ids = array();
        $fine_category_ids = array();
        $loan_ids = array();
        $descriptions = array();
        $amounts = array();
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }elseif(is_array($value)){
                foreach ($value as $row => $payment) {
                    $deposit_type[$row] = $payment->deposit_for_type;
                    $amounts[$row] = $payment->amount;
                   if($payment->deposit_for_type==1){
                       $contribution_ids[$row] = $payment->contribution_id;
                   }elseif ($payment->deposit_for_type==2) {
                       $fine_category_ids[$row] = $payment->fine_category_id;
                   }elseif ($payment->deposit_for_type == 3) {
                       $loan_ids[$row] = $payment->loan_id;
                   }elseif ($payment->deposit_for_type==4) {
                       $descriptions[$row] = $payment->description;
                   }
                }
            }else{
                $_POST[$key] = $value;
            }
        }
        $_POST['contribution_ids'] = $contribution_ids;
        $_POST['fine_category_ids'] = $fine_category_ids;
        $_POST['loan_ids'] = $loan_ids;
        $_POST['descriptions'] = $descriptions;
        $_POST['amounts'] = $amounts;
        $_POST['deposit_type'] = $deposit_type;
        $user_id =$this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->active){
                        if($group_default_bank_account = $this->bank_accounts_m->get_group_default_bank_account($this->group->id)){
                            $this->form_validation->set_rules($this->validation_rules);
                            if($this->form_validation->run()){
                                $total_amount = $this->input->post('total_amount');
                                $amounts = $this->input->post('amounts');
                                $contribution_ids = $this->input->post('contribution_ids');
                                $fine_category_ids = $this->input->post('fine_category_ids');
                                $loan_ids = $this->input->post('loan_ids');
                                $descriptions = $this->input->post('descriptions');
                                $deposit_type = $this->input->post('deposit_type');
                                if($amounts&&$deposit_type){
                                	$transactions = new StdClass();
	                                $transactions->total_amount = currency($total_amount);
	                                $transactions->amounts = $amounts;
	                                $transactions->contribution_ids = $contribution_ids;
	                                $transactions->fine_category_ids = $fine_category_ids;
	                                $transactions->loan_ids = $loan_ids;
	                                $transactions->descriptions = $descriptions;
	                                $transactions->deposit_type = $deposit_type;
	                               	if($result = $this->transactions->make_online_group_payment($this->user,$this->group,$this->member,$group_default_bank_account,$transactions)){
	                                	if(is_object($result)){
	                               			$response = array(
		                                		'status' => 1,
		                                		'message' => 'Payment in progress. Please wait to enter pin',
		                                	);
	                               		}else{
	                               			$response = array(
		                                		'status' => 0,
		                                		"message" => $result,
		                                	);
	                               		}
	                                }else{
	                                	$response = array(
	                                		'status' => 0,
	                                		'message' => "Server error: ".$this->session->flashdata('message'),
	                                	);
	                                }
                                }else{
                                	$response = array(
                                		'status' => 0,
                                		'message' => "Select atleast one payment",
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
                                'message' => 'Group does not have an active bank account. Contact Admin for support',
                                'time' => time(),
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 11,
                            'message' => $this->member->suspension_reason,
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

    function make_new_group_payment(){
    	$validation_rules = array(
    		array(
    			'field' => 'payment_for',
				'name' => 'Payment For',
				'rules' => 'xss_clean|trim|required|numeric'
    		),
    		array(
    			'field' => 'amount',
				'name' => 'Amount',
				'rules' => 'xss_clean|trim|required|currency'
    		),
    		array(
    			'field' => 'phone_number',
				'name' => 'Phone Number',
				'rules' => 'xss_clean|trim|required|valid_phone'
    		),
    	);

        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }


    	$deposit_type = $this->input->post('payment_for');
    	if($deposit_type == 1){
    		$validation_rules[] = array(
				'field' => 'contribution_id',
				'label' => 'Contribution',
				'rules' => 'xss_clean|trim|required|numeric|callback__contribution_exists'
			);
    	}else if($deposit_type == 2){
    		$validation_rules[] =array(
				'field' => 'fine_category_id',
				'label' => 'Fine Category',
				'rules' => 'xss_clean|trim|required|callback__fine_category_exists'
			);
    	}else if($deposit_type == 3){
    		$validation_rules[] =array(
				'field' => 'loan_id',
				'label' => 'Member Loan',
				'rules' => 'xss_clean|trim|required|callback__loan_exists'
			);
    	}else{
    		$validation_rules[] =array(
				'field' => 'description',
				'label' => 'Description',
				'rules' => 'xss_clean|trim'
			);
    	}
        $user_id =$this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->active){
                        if($group_default_bank_account = $this->bank_accounts_m->get_group_default_bank_account($this->group->id)){
                            $this->form_validation->set_rules($validation_rules);
                            if($this->form_validation->run()){
                            	$total_amount = currency($this->input->post('amount'));
                            	$amount = currency($this->input->post('amount'));
                            	$phone_number = $this->input->post('phone_number');
                            	$contribution_id = $this->input->post('contribution_id');
                                $fine_category_id = $this->input->post('fine_category_id');
                                $loan_id = $this->input->post('loan_id');
                                $description = $this->input->post('description');
                                if($amount&&$deposit_type){
                                	$transactions = new StdClass();
	                                $transactions->total_amount = currency($total_amount);
	                                $transactions->amounts = array($amount);
	                                $transactions->contribution_ids = array($contribution_id);
	                                $transactions->fine_category_ids = array($fine_category_id);
	                                $transactions->loan_ids = array($loan_id);
	                                $transactions->descriptions = array($description);
	                                $transactions->deposit_type = array($deposit_type);
	                               	if($result = $this->transactions->make_online_group_payment($this->user,$this->group,$this->member,$group_default_bank_account,$transactions,1,$phone_number)){
	                                	if(is_object($result)){
	                               			$response = array(
		                                		'status' => 1,
		                                		'message' => 'Payment in progress. Please wait to enter pin',
		                                	);
	                               		}else{
	                               			$response = array(
		                                		'status' => 0,
		                                		"message" => $result,
		                                	);
	                               		}
	                                }else{
	                                	$response = array(
	                                		'status' => 0,
	                                		'message' => "Server error: ".$this->session->flashdata('message'),
	                                	);
	                                }
                                }else{
                                	$response = array(
                                		'status' => 0,
                                		'message' => "Select atleast one payment",
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
                                'message' => 'Group does not have an active bank account. Contact Admin for support',
                                'time' => time(),
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 11,
                            'message' => $this->member->suspension_reason,
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

    function make_group_arrears_payment(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id =$this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->active){
                        if($group_default_bank_account = $this->bank_accounts_m->get_group_default_bank_account($this->group->id)){
                            $amount = $this->input->post('amount');
                            if($amount && valid_currency($amount)){
                            	if($result = $this->transactions->make_group_arrears_payment($this->group,$this->user,$this->member,$group_default_bank_account,$amount)){
                            		if(is_object($result)){
				                        $response = array(
				                            'status' => 1,
				                            'message' => 'Payment in progress. Please wait to enter pin',
				                            "result" => $result,
				                        );
				                    }else{
				                        $response = array(
				                            'status' => 0,
				                            "message" => $result,
				                        );
				                    }
                            	}else{
                            		$response = array(
	                            		'status' => 0,
	                            		'message' => $this->session->flashdata('error'),
	                            	);
                            	}
                            }else{
                            	$response = array(
                            		'status' => 0,
                            		'message' => 'Amount to pay is invalid',
                            	);
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Group does not have an active bank account. Contact Admin for support',
                                'time' => time(),
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 11,
                            'message' => $this->member->suspension_reason,
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

    function calculate_convenience_charge(){
        $deposit_type = array();
        $contribution_ids = array();
        $fine_category_ids = array();
        $loan_ids = array();
        $descriptions = array();
        $amounts = array();
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
                        if($group_default_bank_account = $this->bank_accounts_m->get_group_default_bank_account($this->group->id)){
                        	$amount = $this->input->post('amount');
                        	if(valid_currency($amount)){
                        		$result = $this->transactions->calculate_convenience_charge($this->user,$this->group,$this->member,$group_default_bank_account,$amount);
                    			if(is_numeric($result)){
                                	$response = array(
                                		'status' => 1,
                                		'message' => 'Transaction charge',
                                		"charge" => $result,
                                	);
                                }else{
                                	if(is_string($result)){
                                		$error = $result;
                                	}else{
                                		$error = $this->session->flashdata('message');
                                	}
                                	$response = array(
                                		'status' => 0,
                                		'message' => "Server error: ".$error,
                                	);
                                }
                        	}else{
                        		$response = array(
                        			'status' => 0,
                        			'message' => 'The total amount passed is not valid currency',
                        		);
                        	}
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Group does not have an active bank account. Contact Admin for support',
                                'time' => time(),
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 11,
                            'message' => $this->member->suspension_reason,
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

    function get_deposits_list(){
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
                	$filter_params = array();
                	$status = $this->input->post('status')?:array();
                	$type = '';
                	if($status){
                		foreach ($status as $stat) {
                			if($stat){
                				$types = isset($this->deposit_pairing[$stat])?$this->deposit_pairing[$stat]:'0';
                				if($type){
                					$type.= ','.$types;
                				}else{
                					$type= $types;
                				}
                			}
                		}
                	}
                    $member_ids = $this->input->post('members')?:array();
                    $sort_by_order = $this->input->post('sort_by');
                    if(preg_match('/desc/', $sort_by_order)){
                        $sort_order = 'DESC';
                    }elseif (preg_match('/asc/', $sort_by_order)) {
                        $sort_order = 'ASC';
                    }else{
                        $sort_order = '';
                    }
                    if(preg_match('/amount/', $sort_by_order)){
                        $sort_by = 'amount';
                    }elseif (preg_match('/date/', $sort_by_order)) {
                        $sort_by = 'deposit_date';
                    }else{
                        $sort_by = '';
                    }

                    $filter_params = array(
                        'member_id' => $member_ids,
                    );

                	if($this->member->is_admin || $this->member->group_role_id){

                    }else{
                        if($this->group->enable_member_information_privacy){
                        	$member_ids = array(
                                $this->member->id
                            );
                        }else{

                        }
                    }

                    $filter_params = array(
                    	'type' => $type,
                    	'member_id' => $member_ids,
                    );
                	$deposit_transaction_names = $this->transactions->deposit_transaction_names;
                	$lower_limit = $this->input->post('lower_limit')?:0;
                    $upper_limit = $this->input->post('upper_limit')?:20;
                    $records_per_page = $upper_limit - $lower_limit;
                	$total_rows = $this->deposits_m->count_group_deposits($this->group->id,$filter_params);
					$pagination = create_custom_pagination('group',$total_rows,$records_per_page,$lower_limit,TRUE);
                	$posts = $this->deposits_m->limit($pagination['limit'])->get_group_deposits($this->group->id,$filter_params,$sort_by,$sort_order);
                	$deposits = array();
                	$currency = $this->countries_m->get_group_currency_name($this->group->id);
                	$depositor_options = $this->depositors_m->get_group_depositor_options($this->group->id);
                	$group_member_options = $this->members_m->get_group_member_options($this->group->id);
                	$money_market_investment_options = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id);
                	$contribution_options = $this->contributions_m->get_group_contribution_options($this->group->id,$currency);
                	$deposit_method_options = $this->transactions->deposit_method_options;
                	$fine_category_options = $this->fine_categories_m->get_group_options(FALSE,$this->group->id);
                	$income_category_options = $this->income_categories_m->get_group_income_category_options($this->group->id);
                	$stock_options = $this->stocks_m->get_group_stock_options($this->group->id);
                	$group_debtor_options = $this->debtors_m->get_options($this->group->id);
                	$accounts = $this->accounts_m->get_group_account_options('','',$this->group->id);
                	foreach ($posts as $post) {
                		$type = $deposit_transaction_names[$post->type];
                		if($post->type==13||$post->type==14||$post->type==15||$post->type==16){
                            $type.=$depositor_options[$post->depositor_id];
                        }else if($post->type==17||$post->type==18||$post->type==19||$post->type==20){
                            //$type.=' made by '.$group_member_options[$post->member_id];
                            $type = isset($group_member_options[$post->member_id])?$group_member_options[$post->member_id]:'';
                            $type.=' made by '.$type;  
                        }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){

                        }else if($post->type==29||$post->type==30||$post->type==31||$post->type==32){
                            $type=' from '.$money_market_investment_options[$post->money_market_investment_id];
                        }else if($post->type==21||$post->type==22||$post->type==23||$post->type==24){
                        }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){
                        }else if($post->type==33||$post->type==34||$post->type==35||$post->type==36){
                        }else if($post->type==37||$post->type==38||$post->type==39||$post->type==40){
                        }else if($post->type==45||$post->type==46||$post->type==47||$post->type==48){
                        }else if($post->type==49||$post->type==50||$post->type==51||$post->type==52){
                        }else{
                        	$member = isset($group_member_options[$post->member_id])?$group_member_options[$post->member_id]:'';
                            $type.=' made by '.$member; 
                        }                      
                        $narative = ucwords(number_to_words($post->amount)).' '.$currency.'s only -- ';
                        if($post->type==1||$post->type==2||$post->type==3||$post->type==7){
                            $narative.=$deposit_transaction_names[$post->type].' for "'.$contribution_options[$post->contribution_id].'" contribution via '.$deposit_method_options[$post->deposit_method];
                        }else if($post->type==4||$post->type==5||$post->type==6||$post->type==8){
                            if($post->contribution_id){
                                $for = $contribution_options[$post->contribution_id].' contribution late payment';
                            }else if($post->fine_category_id){
                                $for = isset($fine_category_options[$post->fine_category_id])?$fine_category_options[$post->fine_category_id]:'';
                            }else{
                                $for = '';
                            }
                            $narative.=$deposit_transaction_names[$post->type].' for "'.$for.'" via '.$deposit_method_options[$post->deposit_method];
                        }else if($post->type==9||$post->type==10||$post->type==11||$post->type==12){
                            $narative.=$deposit_transaction_names[$post->type];
                        }else if($post->type==13||$post->type==14||$post->type==15||$post->type==16){
                            $narative.=$deposit_transaction_names[$post->type].' from '.$income_category_options[$post->income_category_id];
                        }else if($post->type==17||$post->type==18||$post->type==19||$post->type==20){
                            $narative.=$deposit_transaction_names[$post->type].' via '.$deposit_method_options[$post->deposit_method];
                        }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){
                            $narative.=$deposit_transaction_names[$post->type].' of '.$post->number_of_shares_sold.' "'.$stock_options[$post->stock_id].'" shares';
                        }else if($post->type==29||$post->type==30||$post->type==31||$post->type==32){
                            $narative.=$deposit_transaction_names[$post->type];
                        }else if($post->type==21||$post->type==22||$post->type==23||$post->type==24){
                            $narative.= $deposit_transaction_names[$post->type];
                        }else if($post->type==37||$post->type==38||$post->type==39||$post->type==40){
                            $narative.= $deposit_transaction_names[$post->type];
                        }else if($post->type==45||$post->type==46||$post->type==47||$post->type==48){
                            $narative.= 'External lending to Debtor: '.$group_debtor_options[$post->debtor_id];
                        }else if($post->type==49||$post->type==50||$post->type==51||$post->type==52){
                            $narative.='External loan repayment by : '.$group_debtor_options[$post->debtor_id];
                        }
                        if($post->account_id){
                            $narative.= ' Deposited to '.$accounts[$post->account_id];
                        }


                        $reconciliation = 'Manually recorded';
                        if($post->transaction_alert_id){
                        	$reconciliation = 'Reconciled';
                        }


                		$deposits[] = array(
                			'type' => $type,
                			'date' => timestamp_to_mobile_shorttime($post->deposit_date),
                			'amount' => $post->amount,
                			'reconciliation' => $reconciliation,
                			'narative' => $narative,
                			'id' => $post->id,
                		);
                	}
                	$response = array(
                		'status' => 1,
                		'time' => time(),
                		'message' => 'deposit list',
                		'deposits' => $deposits,
                	);
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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


	function get_group_contribution_transfers(){
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
                	$contribution_options = $this->contributions_m->get_group_contribution_options($this->group->id);
        			$fine_category_options = $this->fine_categories_m->get_group_options(FALSE,$this->group->id);
                	$lower_limit = $this->input->post('lower_limit')?:0;
                    $upper_limit = $this->input->post('upper_limit')?:20;
                    $records_per_page = $upper_limit - $lower_limit;
                    $transfer_to_options = $this->transfer_to_options;
        			$member_transfer_to_options = $this->member_transfer_to_options;
        			$total_rows = $this->deposits_m->count_group_contribution_transfers('',$this->group->id);
        			$pagination = create_custom_pagination('group',$total_rows,$records_per_page,$lower_limit,TRUE);
        			$filter_params = array();
                    if($this->member->is_admin || $this->member->group_role_id){

                    }else{
                        if($this->group->enable_member_information_privacy){
                            $filter_params = array(
                                'member_id' => array(
                                    $this->member->id
                                ),
                            );
                        }else{

                        }
                    }
        			$posts = $this->deposits_m->limit($pagination['limit'])->get_group_contribution_transfers($filter_params,$this->group->id);
                	$transfers = array();
                	$group_member_options = $this->members_m->get_group_member_options($this->group->id);
                	foreach ($posts as $post) {
                		$description = '';
                		if($post->contribution_from_id=='loan'){
                            $description = 'Loan payment transfer to ';
                        }else{
                            $description = 'Contribution transfer to ';
                        }
                        $description.=$transfer_to_options[$post->transfer_to];
                		$transfers[] = array(
                			'id' => $post->id,
                			'date' => timestamp_to_mobile_shorttime($post->transfer_date),
                			'member' => $group_member_options[$post->member_id],
                			'description' => $description,
                			'amount' => $post->amount,
                		);
                	}
                	$response = array(
                		'status' => 1,
                		'time' => time(),
                		'message' => 'deposit list',
                		'deposits' => $transfers,
                	);
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

	function get_group_deposit(){
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
                	$deposit_id = $this->input->post('deposit_id');
                	if($post = $this->deposits_m->get_group_deposit($deposit_id,$this->group->id)){
                		$title = 'Deposit Receipt';
                		if($this->member->id == $post->member_id){
                			$member = $this->member;
                		}else{
                			$member = $this->members_m->get_group_member($post->member_id,$this->group->id);
                		}
                		$payment_for = '';
                		$deposit_transaction_names = $this->transactions->deposit_transaction_names;
                		$contribution_options = $this->contributions_m->get_group_contribution_options($this->group->id);
                		$deposit_method_options = $this->transactions->deposit_method_options;
                		$accounts = $this->accounts_m->get_group_account_options('','',$this->group->id);
                		$currency = $this->countries_m->get_group_currency_name($this->group->id);
                		if($post->type==1||$post->type==2||$post->type==3||$post->type==7){
	                        $payment_for = $deposit_transaction_names[$post->type].' for "'.$contribution_options[$post->contribution_id].'" contribution via '.$deposit_method_options[$post->deposit_method];
	                    }else if($post->type==4||$post->type==5||$post->type==6||$post->type==8){
	                        if($post->contribution_id){
	                            $for = $contribution_options[$post->contribution_id].' contribution late payment';
	                        }else if($post->fine_category_id){
                				$fine_category_options = $this->fine_categories_m->get_group_options(FALSE,$this->group->id);
	                            $for = $fine_category_options[$post->fine_category_id];
	                        }else{
	                            $for = '';
	                        }
	                        $payment_for = $deposit_transaction_names[$post->type].' for "'.$for.'" via '.$deposit_method_options[$post->deposit_method];
	                    }else if($post->type==9||$post->type==10||$post->type==11||$post->type==12){
	                        $payment_for = $deposit_transaction_names[$post->type];
	                    }else if($post->type==13||$post->type==14||$post->type==15||$post->type==16){
	                    	$income_category_options = $this->income_categories_m->get_group_income_category_options($this->group->id);
	                        $payment_for = $deposit_transaction_names[$post->type].' from '.$income_category_options[$post->income_category_id];
	                    }else if($post->type==17||$post->type==18||$post->type==19||$post->type==20){
	                        $payment_for = $deposit_transaction_names[$post->type].' via '.$deposit_method_options[$post->deposit_method].' to '.$accounts[$post->account_id];
	                    }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){
	                    	$stock_options = $this->stocks_m->get_group_stock_options($this->group->id);
	                        $payment_for = $deposit_transaction_names[$post->type].' of '.$post->number_of_shares_sold.' "'.$stock_options[$post->stock_id].'" shares';
	                    }else if($post->type==29||$post->type==30||$post->type==31||$post->type==32){
	                        $payment_for = $deposit_transaction_names[$post->type];
	                    }else if($post->type==21||$post->type==22||$post->type==23||$post->type==24){
	                        $payment_for = $deposit_transaction_names[$post->type];
	                    }else if($post->type==37||$post->type==38||$post->type==39||$post->type==40){
	                        $payment_for = $deposit_transaction_names[$post->type];
	                    }
	                    if($post->description){
	                        $payment_for.= ' : '.$post->description;
	                    }
                		$response = array(
                			'status' => 1,
                			'message' => 'successful',
                			'data' => array(
                				'title' => $title,
                				'member' => $member->first_name.' '.$member->last_name,
                				'amount_paid' => ucwords(number_to_words($post->amount)).' '.$currency.'s only',
                				'payment_for' => strip_tags($payment_for),
                				'amount' => $post->amount,
                				'date' => timestamp_to_mobile_shorttime($post->deposit_date),
                			),
                		);
                	}else{
                		$response = array(
                			'status' => 0,
                			'message' => 'Could not find group payment deposit',
                		);
                	}
				}else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

}
?>