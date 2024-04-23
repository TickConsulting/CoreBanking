<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Mobile extends Mobile_Controller{
    public $filter_status = array(
        1 => 'Expenses',
        2 => 'Asset Purchase Payments',
        3 => 'Loan Disbursements',
        4 => 'Stock Purchase',
        5 => 'Money Market Investment',
        6 => 'Contribution Refund',
        7 => 'Bank Loan Repayment',
        8 => 'Funds Transfer',
        9 => 'External Loan Disbursements',
    );

    function __construct(){
        parent::__construct();
        $this->load->model('withdrawals_m');
        $this->load->model('expense_categories/expense_categories_m');
        $this->load->model('assets/assets_m');
        $this->load->model('expense_categories/expense_categories_m');
        $this->load->model('debtors/debtors_m');
        $this->withdrawal_types_status_options = $this->transactions->withdrawal_types_status_options;
        $this->withdrawal_pairing = array(
            1 => $this->withdrawal_types_status_options[1],
            2 => $this->withdrawal_types_status_options[2],
            3 => $this->withdrawal_types_status_options[5],
            4 => $this->withdrawal_types_status_options[3],
            5 => $this->withdrawal_types_status_options[4],
            6 => $this->withdrawal_types_status_options[6],
            7 => $this->withdrawal_types_status_options[7],
            8 => $this->withdrawal_types_status_options[8],
            9 => $this->withdrawal_types_status_options[9],
        );
    }

    protected $expense_validation_rules = array(
        array(
            'field' => 'expense_date',
            'label' => 'Expense Date',
            'rules' => 'required|xss_clean|trim|date',
        ),
        array(
            'field' => 'expense_category_id',
            'label' => 'Expense Category',
            'rules' => 'required|xss_clean|trim|numeric|callback__is_valid_expense_category',
        ),
        array(
            'field' => 'account_id',
            'label' => 'Account Id',
            'rules' => 'xss_clean|trim|required|callback__valid_account_id'
        ),
        array(
            'field' => 'withdrawal_method',
            'label' => 'Withdrawal Method',
            'rules' => 'required|xss_clean|trim|numeric',
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'xss_clean|trim',
        ),
        array(
            'field' => 'amount',
            'label' => 'Deposit Amount',
            'rules' => 'xss_clean|trim|required|currency'
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


    protected $funds_transfer_validation_rules = array(
        array(
            'field' => 'transfer_date',
            'label' => 'Transfer Date',
            'rules' => 'required|xss_clean|trim|date',
        ),array(
            'field' => 'from_account_id',
            'label' => 'Account to transfer from',
            'rules' => 'required|xss_clean|trim|callback__is_from_account_id_equal_to_to_account_id',
        ),
        array(
            'field' => 'to_account_id',
            'label' => 'Account to transfer to',
            'rules' => 'required|xss_clean|trim',
        ),
        array(
            'field' => 'amount',
            'label' => 'Amount',
            'rules' => 'required|xss_clean|trim|currency|callback__is_less_than_or_equal_from_account_balance',
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'xss_clean|trim',
        ),
    );
    
    protected $contribution_transfer_validation_rules = array(
        array(
            'field' => 'member_id',
            'label' => 'Member Name',
            'rules' => 'xss_clean|trim|required|numeric|callback__member_exists'
        ),
        array(
            'field' => 'refund_date',
            'label' => 'Refund date',
            'rules' => 'required|xss_clean|trim|date',
        ),
        array(
            'field' => 'contribution_id',
            'label' => 'Contribution to refund from',
            'rules' => 'xss_clean|trim|required|numeric|callback__contribution_exists',
        ),
         array(
            'field' => 'account_id',
            'label' => 'Account From',
            'rules' => 'required|xss_clean|trim|callback__valid_account_id',
        ),
         array(
            'field' => 'refund_method',
            'label' => 'Refund method',
            'rules' => 'required|xss_clean|trim',
        ),
        array(
            'field' => 'amount',
            'label' => 'Refund Amount',
            'rules' => 'required|xss_clean|trim|currency',
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'xss_clean|trim',
        ),
    );

    protected $bank_loan_repayment_validation_rules = array(
        array(
            'field' => 'bank_loan_id',
            'label' => 'Bank Loan',
            'rules' => 'xss_clean|trim|required|numeric|callback__bank_loan_exists',
        ),
        array(
            'field' => 'repayment_date',
            'label' => 'Repayment Date',
            'rules' => 'xss_clean|trim|required|date',
        ),
        array(
            'field' => 'account_id',
            'label' => 'Group Account',
            'rules' => 'xss_clean|trim|required|callback__valid_account_id',
        ),
        array(
            'field' => 'repayment_method',
            'label' => 'Repayment Method',
            'rules' => 'xss_clean|trim|required|numeric',
        ),
        array(
            'field' => 'description',
            'label' => 'Repayment Descriptions',
            'rules' => 'xss_clean|trim|required',
        ),
        array(
            'field' => 'amount',
            'label' => 'Amount',
            'rules' => 'xss_clean|trim|required|currency',
        ), 
    );

    protected $transfer_funds_validation_rules = array(
        array(
            'field' => 'withdrawal_for',
            'label' => 'Withdrawal For',
            'rules' => 'required|numeric|xss_clean|trim|callback__valid_withdrawal_for',
        ),
        array(
            'field' => 'amount',
            'label' => 'Withdrawal Amount',
            'rules' => 'required|currency|xss_clean|trim',
        ),
        array(
            'field' => 'recipient',
            'label' => 'Recipient',
            'rules' => 'xss_clean|trim|required',
        )
    );

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
                'status'    =>  404,
                'message'       =>  '404 Method Not Found for URI: '.$this->uri->uri_string(),
            )

        ));
    }

    function _is_valid_expense_category(){
        $group_id = $this->input->post('group_id');
        $expense_category_id = $this->input->post('expense_category_id');
        if($this->expense_categories_m->expense_category_exists($expense_category_id,$group_id)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_is_valid_expense_category',"Expense Category selected does not exist");
            return FALSE;
        }
    }

    function _is_from_account_id_equal_to_to_account_id(){
        $group_id = $this->input->post('group_id');
        if($this->input->post('from_account_id')==$this->input->post('to_account_id')){
            $this->form_validation->set_message('_is_from_account_id_equal_to_to_account_id', 'You cannot transfer money to the same account.');
            return FALSE;
        }else{
            if(!$this->accounts_m->check_if_group_account_exists($this->input->post('from_account_id'),$group_id)){
                $this->form_validation->set_message('_is_from_account_id_equal_to_to_account_id','Account From is invalid');
                return FALSE;
            }
            if(!$this->accounts_m->check_if_group_account_exists($this->input->post('to_account_id'),$group_id)){
                $this->form_validation->set_message('_is_from_account_id_equal_to_to_account_id','Account To is invalid');
                return FALSE;
            }
            return TRUE;
        }
    }

    function _is_less_than_or_equal_from_account_balance(){
        $group_id = $this->input->post('group_id');
        if($this->input->post('amount')&&$this->input->post('from_account_id')){
            $amount = valid_currency($this->input->post('amount'));
            $account_id = $this->input->post('from_account_id');
            $account_balance = $this->accounts_m->get_group_account_balance($account_id,$group_id);
            if($amount>$account_balance){
                $this->form_validation->set_message('_is_less_than_or_equal_from_account_balance', 'You cannot transfer an amount greater than the account balance.');
                return FALSE;
            }else{
                return TRUE;
            }
        }else{
            return TRUE;
        }
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

    function _bank_loan_exists(){
        $group_id = $this->input->post('group_id');
        $bank_loan_id = $this->input->post('bank_loan_id');
        if($this->bank_loans_m->get($bank_loan_id,$group_id)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_bank_loan_exists','Loan selected does not exist');
            return FALSE;
        }
    }

    function new_record_expenses(){
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
                        $this->form_validation->set_rules($this->expense_validation_rules);
                        if($this->form_validation->run()){
                            $expense_date = $this->input->post("expense_date");
                            $expense_category_id = $this->input->post("expense_category_id");
                            $withdrawal_method = $this->input->post("withdrawal_method");
                            $account_id = $this->input->post("account_id");
                            $description = $this->input->post("description");
                            $amount = currency($this->input->post("amount"));
                            if($this->transactions->record_expense_withdrawal($this->group->id,$expense_date,$expense_category_id,$withdrawal_method,$account_id,$description,$amount)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Successfully recorded expense payment',
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Could not record expense payment. Try again later',
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
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
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

    function record_expenses(){
        $expenses = array();
        $expense_dates = array();
        $expense_category_ids = array();
        $withdrawal_methods = array();
        $account_ids = array();
        $amounts = array();
        $descriptions = array();
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                if(is_array($value)){
                    foreach ($value as $key_value => $value_value) {
                        if($value_value->expense_date){
                            $expense_dates[$key_value] = $value_value->expense_date;
                        }
                        if($value_value->expense_category_id){
                            $expense_category_ids[$key_value] = $value_value->expense_category_id;
                        }
                        if($value_value->withdrawal_method){
                            $withdrawal_methods[$key_value] = $value_value->withdrawal_method;
                        }
                        if($value_value->account_id){
                            $account_ids[$key_value] = $value_value->account_id;
                        }
                        if($value_value->amount){
                            $amounts[$key_value] = currency($value_value->amount);
                        }
                        if($value_value->description){
                            $descriptions[$key_value] = $value_value->description;
                        }
                        $expenses[$key_value] = array($key_value => 'item');
                    }
                }else{
                    $_POST[$key] = $value;
                }
            }
        }
        $_POST['expense_dates'] = $expense_dates;
        $_POST['expense_category_ids'] = $expense_category_ids;
        $_POST['withdrawal_methods'] = $withdrawal_methods;
        $_POST['account_ids'] = $account_ids;
        $_POST['amounts'] = $amounts;
        $_POST['descriptions'] = $descriptions;
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->group_role_id || $this->member->is_admin){
                        $expense_dates = $this->input->post('expense_dates');
                        $expense_category_ids = $this->input->post('expense_category_ids');
                        $withdrawal_methods = $this->input->post('withdrawal_methods');
                        $account_ids = $this->input->post('account_ids');
                        $amounts = $this->input->post('amounts');
                        $descriptions = $this->input->post('descriptions');
                        if(empty($expenses)){
                            $response = array(
                                'status' => 0,
                                'time' => time(),
                                'message' => 'Error expenses array empty.',
                            );
                        }else{
                            $entries_are_valid = TRUE;
                            foreach($expenses as $key=>$expense):
                                if(isset($expense_dates[$key])&&isset($expense_category_ids[$key])&&isset($withdrawal_methods[$key])&&isset($account_ids[$key])&&isset($amounts[$key])):    
                                    //Deposit dates
                                    if($expense_dates[$key]==''){
                                        $entries_are_valid = FALSE;
                                    }else{

                                    }
                                    //Members
                                    if($expense_category_ids[$key]==''){
                                        $entries_are_valid = FALSE;
                                    }else{
                                        if(is_numeric($expense_category_ids[$key])){

                                        }else{
                                            $entries_are_valid = FALSE;
                                        }
                                    }
                                    //Contributions
                                    if($withdrawal_methods[$key]==''){
                                        $entries_are_valid = FALSE;
                                    }else{
                                        if(is_numeric($withdrawal_methods[$key])){

                                        }else{
                                            $entries_are_valid = FALSE;
                                        }
                                    }
                                     //Accounts
                                    if($account_ids[$key]==''){
                                        $entries_are_valid = FALSE;
                                    }else{

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
                                foreach($expenses as $key => $expense):
                                    $expense_date = strtotime($expense_dates[$key]);
                                    $amount = valid_currency($amounts[$key]);
                                    $description = isset($descriptions[$key])?$descriptions[$key]:'';
                                    if($this->transactions->record_expense_withdrawal($this->group->id,$expense_date,$expense_category_ids[$key],$withdrawal_methods[$key],$account_ids[$key],$description,$amount)){

                                    }else{
                                        $transaction_result = FALSE;
                                    }
                                endforeach;
                                if($transaction_result){
                                    $response = array(
                                        'status' => 1,
                                        'time' => time(),
                                        'success' => 'Successfully added transactions',
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

    function _valid_withdrawal_for(){
        $withdrawal_for = $this->input->post('withdrawal_for');
        if(array_key_exists($withdrawal_for, $this->transactions->withdrawal_request_transaction_names)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_valid_withdrawal_for','Kindly select a valid withdrawal type');
            return FALSE;
        }
    }

    function record_funds_transfer(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                if(is_array($value)){
                    
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
                        $this->form_validation->set_rules($this->funds_transfer_validation_rules);
                        if($this->form_validation->run()){
                            $transaction_id = $this->transactions->record_account_transfer(
                                    $this->group->id,
                                    $this->input->post('transfer_date'),
                                    $this->input->post('from_account_id'),
                                    $this->input->post('to_account_id'),
                                    $this->input->post('amount'),
                                    $this->input->post('description')
                                );
                            if($transaction_id){
                                $response = array(
                                        'status' => 1,
                                        'time' => time(),
                                        'success' => 'Successfully recorded a transaction',
                                    );
                            }else{
                                $response = array(
                                        'status' => 0,
                                        'time' => time(),
                                        'message' => 'Error recording transaction',
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

    function record_contribution_refund(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                if(is_array($value)){
                    
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
                        $this->form_validation->set_rules($this->contribution_transfer_validation_rules);
                        if($this->form_validation->run()){
                            $transaction_id = $this->transactions->record_contribution_refund(
                                    $this->group->id,
                                    $this->input->post('refund_date'),
                                    $this->input->post('member_id'),
                                    $this->input->post('account_id'),
                                    $this->input->post('contribution_id'),
                                    $this->input->post('refund_method'),
                                    $this->input->post('description'),
                                    $this->input->post('amount'),
                                    $this->user->id);
                            if($transaction_id){
                                $response = array(
                                        'status' => 1,
                                        'time' => time(),
                                        'success' => 'Successfully recorded a transaction',
                                    );
                            }else{
                                $response = array(
                                        'status' => 0,
                                        'time' => time(),
                                        'message' => 'Error recording transaction',
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

    function record_bank_loan_repayment(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                if(is_array($value)){
                    
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
                        $this->form_validation->set_rules($this->bank_loan_repayment_validation_rules);
                        if($this->form_validation->run()){
                            $bank_loan = $this->bank_loans_m->get($this->input->post('bank_loan_id'),$this->group->id);
                            if($bank_loan){
                                $transaction_id = $this->loan->bank_loan_repayment(
                                        $bank_loan->id,
                                        $this->input->post('amount'),
                                        $this->input->post('repayment_date'),
                                        $this->group->id,
                                        $this->input->post('account_id'),
                                        $this->input->post('repayment_method'),
                                        $this->input->post('description'),
                                        $this->user->id
                                    );
                                if($transaction_id){
                                    $response = array(
                                        'status' => 1,
                                        'success' => 'Successfully recorded a transaction',
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Error recording transaction: '.$this->session->flashdata('error'),
                                    );
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Could not find bank loan',
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
                if(is_array($value)){
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
                        $id = $this->input->post('withdrawal_id')?:0;
                        if($id){
                            $post = $this->withdrawals_m->get_group_withdrawal($id,$this->group->id);
                            if($post){
                                if($this->transactions->void_group_withdrawal($post->id,$post,TRUE,$this->group->id)){
                                    $response = array(
                                        'status' => 1,
                                        'time' => time(),
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
                                    'time' => time(),
                                    'message' => 'Withdrawal request trying to void is not available. Refresh page and try again',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'time' => time(),
                                'message' => 'Invoice details are missing',
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
        echo encrypt_json_encode(array('response'=>$response));
    }

    function get_group_withdrawal_list(){
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
                                $types = isset($this->withdrawal_pairing[$stat])?$this->withdrawal_pairing[$stat]:'0';
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
                        $sort_by = 'withdrawal_date';
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
                    $lower_limit = $this->input->post('lower_limit')?:0;
                    $upper_limit = $this->input->post('upper_limit')?:20;
                    $records_per_page = $upper_limit - $lower_limit;
                    $total_rows = $this->withdrawals_m->count_group_withdrawals($filter_params,$this->group->id);
                    $pagination = create_custom_pagination('group',$total_rows,$records_per_page,$lower_limit,TRUE);
                    $posts = $this->withdrawals_m->limit($pagination['limit'])->get_group_withdrawals($filter_params,$this->group->id,$sort_by,$sort_order);
                    $withdrawals  = array();
                    $accounts = $this->accounts_m->get_group_account_options('','',$this->group->id);
                    $asset_options = $this->assets_m->get_group_asset_options($this->group->id);
                    $contribution_options = $this->contributions_m->get_group_contribution_options($this->group->id);
                    $withdrawal_transaction_names = $this->transactions->withdrawal_transaction_names;
                    $withdrawal_type_options = $this->transactions->withdrawal_type_options;
                    $expense_category_options = $this->expense_categories_m->get_group_expense_category_options($this->group->id);
                    $group_member_options = $this->members_m->get_group_member_options($this->group->id);
                    $group_debtor_options = $this->debtors_m->get_options($this->group->id);
                    foreach ($posts as $key => $post) {
                        $narration = '';
                        if($post->type==1||$post->type==2||$post->type==3||$post->type==4){
                            $narration = $withdrawal_transaction_names[$post->type].' for '.($expense_category_options[$post->expense_category_id]??'');
                        }else if($post->type==5||$post->type==6||$post->type==7||$post->type==8){
                            $narration = $withdrawal_transaction_names[$post->type].' for '.($asset_options[$post->asset_id]??'');
                        }else if($post->type==9||$post->type==10||$post->type==11||$post->type==12){
                            $narration =  $withdrawal_transaction_names[$post->type];
                            if($post->member_id){ 
                                $narration.=' to '.$group_member_options[$post->member_id];
                            }
                        }else if($post->type==21||$post->type==22||$post->type==23||$post->type==24){
                            $narration = $withdrawal_transaction_names[$post->type].' to '.($group_member_options[$post->member_id]??'').' for '.($contribution_options[$post->contribution_id]??'');
                        }else if($post->type==13||$post->type==14||$post->type==15||$post->type==16){
                            $narration = $withdrawal_transaction_names[$post->type];
                        }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){
                            $narration = $withdrawal_transaction_names[$post->type];
                        }else if($post->type==29||$post->type==30||$post->type==31||$post->type==32){
                            $narration = $withdrawal_transaction_names[$post->type];
                        }else if($post->type==33||$post->type==34||$post->type==35||$post->type==36){
                            $narration = $withdrawal_transaction_names[$post->type];
                            if($post->debtor_id){ 
                                $narration.= ' to '.($group_debtor_options[$post->debtor_id]??'');
                            }
                        }
                        if($post->description){
                            $narration.=' : '.$post->description;
                        }
                        if($post->transaction_alert_id){
                            $reconcillation ='Reconciled';
                        }else{
                            $reconcillation ='Manually Recorded';
                        }

                        $withdrawals[] = array(
                            'id' => $post->id,
                            'type' => $withdrawal_transaction_names[$post->type].' --- '.$narration,
                            'withdrawal_date' => timestamp_to_mobile_shorttime($post->withdrawal_date),
                            'recorded_on' => timestamp_to_mobile_shorttime($post->created_on),
                            'narration' => '',
                            'reconcilation' => $reconcillation,
                            'amount' => $post->amount,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'time' => time(),
                        'message' => 'deposit list',
                        'withdrawals' => $withdrawals,
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

    function request_funds_transfer(){
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
                $this->group_currency = $this->countries_m->get_currency_code($this->group->currency_id);
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->group_role_id || $this->member->is_admin){
                        $this->form_validation->set_rules($this->transfer_funds_validation_rules);
                        if($this->form_validation->run()){
                            $amount = $this->input->post('amount');
                            $request_data = new StdClass();
                            $request_data->recipient = $this->input->post('recipient');
                            $request_data->withdrawal_for = $this->input->post('withdrawal_for');
                            $request_data->phone = $this->input->post('phone');
                            $request_data->expense_category_id = $this->input->post('expense_category_id');
                            $request_data->description = $this->input->post('description');
                            $request_data->paybill_number = $this->input->post('paybill_number');
                            $request_data->paybill_account_number = $this->input->post('paybill_account_number');
                            $request_data->bank_id = $this->input->post('bank_id');
                            $request_data->account_number = $this->input->post('account_number');
                            $request_data->member_id = $this->input->post('member_id');
                            $request_data->contribution_id = $this->input->post('contribution_id');
                            $request_data->transfer_from = $this->input->post('transfer_from');
                            $request_data->transfer_to = $this->input->post('transfer_to');
                            $request_data->loan_type_id = $this->input->post('loan_type_id');
                            if($request_id = $this->transactions->record_funds_transfer_request($this->user,$this->group,$this->member,$amount,$request_data,$this->group_currency)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Request Successfully submitted',
                                    'request_id' => $request_id,
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
                                'message' => 'Form validation failed',
                                'validation_errors' => $post,
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
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
        if($response['status'] == '0'){
            update_request_id($this->request->request_id);
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function view_withdrawal_request(){
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
                    $this->group_currency = $this->countries_m->get_currency_code($this->group->currency_id);
                    $group_member_options = $this->members_m->get_group_member_options($this->group->id);
                    //$group_paybil_options = $this->paybills_m->get_group_paybill_options($this->group->id,TRUE);
                    $group_recipient_options = $this->transactions->recipient_options;
                    $id = $this->input->post('id');
                    if(is_numeric($id)){
                        $post = $this->withdrawals_m->get_group_withdrawal_request($id,$this->group->id);
                        if($post){
                            $has_responded = 0;
                            $response_status = 0;
                            $withdrawal_approval_requests = $this->withdrawals_m->get_group_withdrawal_approval_requests($id,$this->group->id);
                            $withdrawal_request_transaction_names = $this->transactions->withdrawal_request_transaction_names;
                            $description = '';
                            $recipient = $this->transactions->recipient_options[$post->recipient].' ';
                            if($post->recipient == 1){
                                $recipient.= ' -- Phone Number: '.$post->recipient_phone_number;
                            }elseif($post->recipient == 2){
                                $recipient.= $post->recipient_paybill_number.' Account '.$post->recipient_paybill_account_number;
                            }elseif($post->recipient == 3){
                                $bank = $this->banks_m->get($post->recipient_bank_id);
                                $name = $bank?$bank->name:'';
                                $recipient.= ' -- Bank Name: '.$name.', Account: '.$post->recipient_account_number;
                            }
                            if($post->recipient_member_id){
                                $recipient.=' - '.$group_member_options[$post->recipient_member_id];
                            }
                            if($post->withdrawal_for == 1){
                                $expense_category_options = $this->expense_categories_m->get_group_expense_category_options($this->group->id);
                                $description = $withdrawal_request_transaction_names[$post->withdrawal_for]." ".$expense_category_options[$post->expense_category_id]." of  ".$this->group_currency." ".number_to_currency($post->amount)." ";
                                if($post->description){
                                    $description.=" : ".$post->description;
                                }
                            }elseif($post->withdrawal_for == 2){
                                $contribution_options = $this->contributions_m->get_active_group_contribution_options($this->group->id);
                                $description=$withdrawal_request_transaction_names[$post->withdrawal_for]." to ".$group_member_options[$post->recipient_member_id]." of  ".$this->group_currency." ".number_to_currency($post->amount)." from ".$contribution_options[$post->contribution_id]." contribution ";
                                if($post->description){
                                    $description.=" : ".$post->description;
                                }
                            }elseif($post->withdrawal_for == 3){
                                $description = $withdrawal_request_transaction_names[$post->withdrawal_for]." to ".$group_member_options[$post->member_id]." of  ".$this->group_currency." ".number_to_currency($post->amount)." ";
                                if($post->description){
                                    $description.=" : ".$post->description;
                                }
                            }elseif ($post->withdrawal_for == 4) {
                                $description=$withdrawal_request_transaction_names[$post->withdrawal_for].
                                " of ".$this->group_currency." ".number_to_currency($post->amount)." by ".
                                $group_member_options[$post->member_id].' for ';
                                if($post->loan_type_id){
                                    $loan_type_options = $this->loan_types_m->get_options($this->group->id);
                                    //$loan = $this->debtors_m->get_temporary_loan($post->debtor_loan_id);
                                    //$interest_types = $this->loan->interest_types;
                                    $description.=$loan_type_options[$post->loan_type_id]." loan ";
                                }
                                if($post->description){
                                    $description.=$post->description;
                                }
                            }elseif ($post->withdrawal_for == 5) {
                                $group_account_options = $this->accounts_m->get_group_account_options(FALSE,'',$this->group->id);
                                $description=$withdrawal_request_transaction_names[$post->withdrawal_for]." of ".$this->group_currency." ".number_to_currency($post->amount)." from ".$group_account_options[$post->transfer_from_account_id].' to '.$group_account_options[$post->transfer_to_account_id]." loan ";
                                $recipient = $group_account_options[$post->transfer_to_account_id];
                            }
                            $approved_members = array();
                            $declined_members = array();
                            $pending_approvals = array();
                            $sent_member_ids = array();
                            foreach($withdrawal_approval_requests as $withdrawal_approval_request):
                                $sent_member_ids=array_merge($sent_member_ids,array($withdrawal_approval_request->member_id));
                                if($post->member_id == $withdrawal_approval_request->member_id){
                                    //owner
                                    /*if($this->member->id == $withdrawal_approval_request->member_id){
                                        $approved_members[] = $group_member_options[$withdrawal_approval_request->member_id].'- You';
                                        $has_responded = 1;
                                        $response_status = 1;
                                    }else{
                                        $approved_members[] = $group_member_options[$withdrawal_approval_request->member_id];
                                    }*/  
                                    if($withdrawal_approval_request->is_approved){
                                        if($this->member->id == $withdrawal_approval_request->member_id){
                                            $approved_members[] = $group_member_options[$withdrawal_approval_request->member_id].'- You';
                                            $has_responded = 1;
                                            $response_status = 1; 
                                        }else{
                                            $approved_members[] = $group_member_options[$withdrawal_approval_request->member_id];
                                        }                                
                                   }elseif($withdrawal_approval_request->is_declined){
                                        if($this->member->id == $withdrawal_approval_request->member_id){
                                            $declined_members[] = $group_member_options[$withdrawal_approval_request->member_id].'- You: Comments --- '.$withdrawal_approval_request->comments;
                                            $has_responded = 1;
                                            $response_status = 0; 
                                        }else{
                                            $declined_members[] = $group_member_options[$withdrawal_approval_request->member_id].' : Comments --- '.$withdrawal_approval_request->comments;
                                        }                                       
                                   }else{
                                        if($this->member->id == $withdrawal_approval_request->member_id){
                                            $has_responded = 0;
                                            $response_status = 0;
                                            $member = isset($group_member_options[$withdrawal_approval_request->member_id])?($group_member_options[$withdrawal_approval_request->member_id].'- You'):'';
                                            if($member){
                                                $pending_approvals[] = $member;
                                            }
                                        }else{
                                            $member = isset($group_member_options[$withdrawal_approval_request->member_id])?$group_member_options[$withdrawal_approval_request->member_id]:'';
                                            if($member){
                                                $pending_approvals[] = $member;
                                            }
                                        }
                                   }
                                }else{
                                    if($withdrawal_approval_request->is_approved){
                                        if($this->member->id == $withdrawal_approval_request->member_id){
                                            $approved_members[] = $group_member_options[$withdrawal_approval_request->member_id].'- You';
                                            $has_responded = 1;
                                            $response_status = 1; 
                                        }else{
                                            $approved_members[] = $group_member_options[$withdrawal_approval_request->member_id];
                                        }                                
                                   }elseif($withdrawal_approval_request->is_declined){
                                        if($this->member->id == $withdrawal_approval_request->member_id){
                                            $declined_members[] = $group_member_options[$withdrawal_approval_request->member_id].'- You: Comments --- '.$withdrawal_approval_request->comments;
                                            $has_responded = 1;
                                            $response_status = 0; 
                                        }else{
                                            $declined_members[] = $group_member_options[$withdrawal_approval_request->member_id].' : Comments --- '.$withdrawal_approval_request->comments;
                                        }                                       
                                   }else{
                                        if($this->member->id == $withdrawal_approval_request->member_id){
                                            $has_responded = 0;
                                            $response_status = 0;
                                            $member = isset($group_member_options[$withdrawal_approval_request->member_id])?($group_member_options[$withdrawal_approval_request->member_id].'- You'):'';
                                            if($member){
                                                $pending_approvals[] = $member;
                                            }
                                        }else{
                                            $member = isset($group_member_options[$withdrawal_approval_request->member_id])?$group_member_options[$withdrawal_approval_request->member_id]:'';
                                            if($member){
                                                $pending_approvals[] = $member;
                                            }
                                        }
                                   }
                                }                                   
                            endforeach;

                            
                            if(!in_array($this->member->id, $sent_member_ids)){
                                $has_responded = 1;
                            }
                            if($post->status){
                                if($post->is_approved){
                                    $approval_status = 'Approved';
                                }else if($post->is_declined){
                                    $approval_status = 'Declined: Reason -- '.$post->decline_reason;
                                    $has_responded = 1;
                                }else{
                                    $approval_status = 'Pending Approval';
                                }
                            }else{
                                $approval_status = 'Pending Approval';
                            }
                            
                            $is_owner = ($post->member_id == $this->member->id)?1:0;
                            $response = array(
                                'status' => 1,
                                'message' => 'Withdrawal request',
                                'time' => time(),
                                'withdrawal_for' => $this->transactions->withdrawal_request_transaction_names[$post->withdrawal_for],
                                'date' => timestamp_to_datemonth_and_time($post->created_on),
                                'request_by' => $group_member_options[$post->member_id],
                                'amount' => $post->amount,
                                'description' => $description,
                                'recipient' => $recipient,
                                'approval_status' => $approval_status,
                                'approved_members' => $approved_members,
                                'declined_members' => $declined_members,
                                'pending_approvals' => $pending_approvals,
                                'is_owner' => $is_owner,
                                'has_responded' => $is_owner?:$has_responded,
                                'response_status' => $response_status,
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Withdrawal request not found',
                                'time' => time(),
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not find group withdrawal request',
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

    function respond_to_withdrawal_request(){
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
                    $this->group_currency = $this->countries_m->get_currency_code($this->group->currency_id);
                    $group_member_options = $this->members_m->get_group_member_options($this->group->id,TRUE);
                    $group_recipient_options = $this->transactions->recipient_options;
                    $id = $this->input->post('id');
                    $approve  = $this->input->post('approve');
                    $decline  = $this->input->post('decline');
                    $comments = $this->input->post('reason');
                    $post = $this->withdrawals_m->get_group_withdrawal_request($id);
                    if($post){
                        if($approve || $decline){
                            if($approve){
                                if($this->transactions->approve_withdrawal_request($this->user->id,$this->group->id,$id,$this->member->id)){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'You have successfully approved request',
                                        'time' => time(),
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => $this->session->flashdata('error'),
                                        'time' => time(),
                                    );
                                }
                            }elseif ($decline && $comments) {
                                if($this->transactions->decline_withdrawal_request($this->user->id,$this->group->id,$id,$this->member->id,$comments)){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'You have successfully declined withdrawal request',
                                        'time' => time(),
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => $this->session->flashdata('error'),
                                        'time' => time(),
                                    );
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Request could not be handled at the moment. Kindly check your request and try again later',
                                    'time' => time(),
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'You must approve or decline withdrawal',
                                'time' => time(),
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Withdrawal request not found',
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

    function withdrawal_request_list(){
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
                    $group_member_options = $this->members_m->get_group_member_options($this->group->id,FALSE);
                    //$group_paybil_options = $this->paybills_m->get_group_paybill_options($this->group->id,TRUE);
                    $group_recipient_options = $this->transactions->recipient_options;
                    $status = $this->input->post('status')?:array(1);
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
                        $sort_by = 'created_on';
                    }else{
                        $sort_by = '';
                    }
                    if($status){
                        $lower_limit = $this->input->post('lower_limit')?:0;
                        $upper_limit = $this->input->post('upper_limit')?:10;
                        $total_rows = $this->withdrawals_m->count_group_withdrawal_requests($status,$this->group->id,$member_ids);
                        $records_per_page = $upper_limit - $lower_limit;
                        $pagination = create_custom_pagination('group',$total_rows,$records_per_page,$lower_limit,TRUE);
                        $withdrawal_posts = $this->withdrawals_m->limit($pagination['limit'])->get_group_withdrawal_requests($status,$this->group->id,$member_ids,$sort_by,$sort_order);
                        $withdrawal_approval_requests_array = $this->withdrawals_m->get_withdrawal_approval_requests_array($withdrawal_posts);
                        $posts = array();
                        foreach ($withdrawal_posts as $post) {
                            $approved = 0;
                            $declined = 0;
                            $pending = 0;
                            $status = 'Pending Signatories Approval';
                            $status_code =1;
                            $description = '';
                            $has_responded = 0;
                            $response_status = 0;
                            if(isset($withdrawal_approval_requests_array[$post->id])):
                                foreach ($withdrawal_approval_requests_array[$post->id] as $member_id => $approvals) {
                                    if($approvals['is_approved'] == 1){
                                        if($approvals['member_id'] == $this->member->id){
                                            $has_responded = 1;
                                            $response_status = 1;
                                        }
                                        ++$approved;
                                    }elseif ($approvals['is_declined'] == 1) {
                                        if($approvals['member_id'] == $this->member->id){
                                            $has_responded = 1;
                                            $response_status = 0;
                                        }
                                        ++$declined;
                                    }else{
                                        ++$pending;
                                        if($approvals['member_id'] == $this->member->id){
                                            $has_responded = 0;
                                            $response_status = 0;
                                        }
                                    }
                                }
                            endif;
                            if($post->status == 1){
                                if($post->is_approved){
                                    $status = 'Approved by Signatories- Pending Disbursement';
                                    $status_code = 2;
                                    $has_responded = 1;
                                }else{
                                    $status = $post->decline_reason?:'Declined by Signatories';
                                    $status_code = 3;
                                    $has_responded = 1;
                                }
                            }elseif($post->status == 2){
                                if($post->is_disbursement_declined){
                                    $status = 'Disbursement Failed';
                                    $description = $post->disbursement_failed_error_message;
                                    $status_code = 6;
                                    $has_responded = 1;
                                }else{
                                    $status_code = 5;
                                    $status = 'Disbursed';
                                    $description = $post->disbursement_result_description;
                                    $has_responded = 1;
                                }
                            }
                            
                            $recipient = $this->transactions->recipient_options[$post->recipient].' ';
                            if($post->recipient == 1){
                                $recipient.= ' -- Phone Number: '.$post->recipient_phone_number;
                            }elseif($post->recipient == 2){
                                $recipient.= $post->recipient_paybill_number.' Account '.$post->recipient_paybill_account_number;
                            }elseif($post->recipient == 3){
                                $bank = $this->banks_m->get($post->recipient_bank_id);
                                $name = $bank?$bank->name:'';
                                $recipient.= ' -- Bank Name: '.$name.', Account: '.$post->recipient_account_number;
                            }
                            if($post->recipient_member_id){
                                $recipient.=' - '.($group_member_options[$post->recipient_member_id]??'');
                            }

                            $is_owner = ($post->member_id == $this->member->id)?1:0;
                            $posts[] = array(
                                'id' => $post->id,
                                'date' => timestamp_to_datemonth_and_time($post->created_on),
                                'name' => $group_member_options[$post->member_id],
                                'withdrawal_for' => $this->transactions->withdrawal_request_transaction_names[$post->withdrawal_for],
                                'amount' => $post->amount,
                                'approved' => $approved,
                                'declined' => $declined,
                                'pending' => $pending,
                                'status' => ucfirst(strtolower($status)),
                                'status_code' => $status_code,
                                'is_owner' => $is_owner,
                                'recipient' => $recipient,
                                'has_responded' => $is_owner?:$has_responded,
                                'response_status' => $response_status,
                                'description' =>$description,
                            );
                        }
                        $response = array(
                            'status' => 1,
                            'message' => 'Withdrawal requests',
                            'time' => time(),
                            'posts' => $posts,
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Invalid status',
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

    function get_group_withdrawal(){
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
                    $withdrawal_id = $this->input->post('withdrawal_id');
                    if($post = $this->withdrawals_m->get_group_withdrawal($withdrawal_id,$this->group->id)){
                        $title = 'Withdrawal Receipt';
                        if($this->member->id == $post->member_id){
                            $member = $this->member;
                        }else{
                            $member = $this->members_m->get_group_member($post->member_id,$this->group->id);
                        }
                        $currency = $this->countries_m->get_group_currency_name($this->group->id);
                        $withdrawal_transaction_names = $this->transactions->withdrawal_transaction_names;
                        $narration = '';
                        if($post->type==1||$post->type==2||$post->type==3||$post->type==4){
                            $expense_category_options = $this->expense_categories_m->get_group_expense_category_options($this->group->id);
                            $narration = $withdrawal_transaction_names[$post->type].' for '.$expense_category_options[$post->expense_category_id];
                        }else if($post->type==5||$post->type==6||$post->type==7||$post->type==8){
                            $asset_options = $this->assets_m->get_group_asset_options($this->group->id);
                            $narration = $withdrawal_transaction_names[$post->type].' for '.$asset_options[$post->asset_id];
                        }else if($post->type==9||$post->type==10||$post->type==11||$post->type==12){
                            $narration =  $withdrawal_transaction_names[$post->type];
                            if($post->member_id){ 
                                $member = $this->members_m->get_group_member($post->member_id,$this->group->id);
                                $narration.=' to '.($member->first_name.' '.$member->last_name);
                            }
                        }else if($post->type==21||$post->type==22||$post->type==23||$post->type==24){
                            $contribution_options = $this->contributions_m->get_group_contribution_options($this->group->id);
                            $member = $this->members_m->get_group_member($post->member_id,$this->group->id);
                            $narration = $withdrawal_transaction_names[$post->type].' to '.($member->first_name.' '.$member->last_name).' for '.$contribution_options[$post->contribution_id];
                        }else if($post->type==13||$post->type==14||$post->type==15||$post->type==16){
                            $narration = $withdrawal_transaction_names[$post->type];
                        }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){
                            $narration = $withdrawal_transaction_names[$post->type];
                        }else if($post->type==29||$post->type==30||$post->type==31||$post->type==32){
                            $narration = $withdrawal_transaction_names[$post->type];
                        }else if($post->type==33||$post->type==34||$post->type==35||$post->type==36){
                            $narration = $withdrawal_transaction_names[$post->type];
                            if($post->debtor_id){ 
                                $group_debtor_options = $this->debtors_m->get_options($this->group->id);
                                $narration.= ' to '.$group_debtor_options[$post->debtor_id];
                            }
                        }
                        if($post->description){
                            $narration.=' : '.$post->description;
                        }


                        $response = array(
                            'status' => 1,
                            'message' => 'successful',
                            'data' => array(
                                'title' => $title,
                                'member' => $member->first_name.' '.$member->last_name,
                                'amount_paid' => ucwords(number_to_words($post->amount)).' '.$currency.'s only',
                                'withdrawal_for' => strip_tags($narration),
                                'amount' => $post->amount,
                                'date' => timestamp_to_mobile_shorttime($post->withdrawal_date),
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

    function cancel_withdrawal_request(){
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
                    $id = $this->input->post('id');
                    if($post = $this->withdrawals_m->get_group_withdrawal_request($id,$this->group->id)){
                        if($post->is_approved){
                            $response = array(
                                'status' => 0,
                                'message' => 'Request has already been approved therefore can not be canceled',
                            );
                        }else if($post->is_declined){
                            $response = array(
                                'status' => 0,
                                'message' => 'Request has already been declined therefore can not be canceled',
                            );
                        }else{
                            if($post->created_by == $this->user->id){
                                $reason = $this->input->post('reason');
                                if($reason){
                                    if($this->transactions->decline_withdrawal_request($this->user->id,$this->group->id,$id,$this->member->id,$reason,TRUE)){
                                        $response = array(
                                            'status' => 1,
                                            'message' => 'Successfully cancelled this request',
                                        );
                                    }else{
                                        $response = array(
                                            'status' => 0,
                                            'message' => $this->session->flashdata('error'),
                                        );
                                    }
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Kindly give a reason why you want to cancel this request',
                                    );
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'You are not allowed to cancel a request you did not request.',
                                );
                            }
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not find group withdrawal request',
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
}?>