<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
	
	protected $data = array();
	protected $wallet_account = array();
    protected $wallet_id;
    protected $wallet_ids = 0;
	protected $current_balance;
    
    function __construct(){
        parent::__construct();
        $this->load->model('wallets_m');
        $this->load->model('withdrawals/withdrawals_m');
        $this->load->model('expense_categories/expense_categories_m');
        $this->load->model('transaction_statements/transaction_statements_m');

        $this->wallet_account = $this->wallets_m->get_wallet_account();
        $this->wallet_accounts = $this->wallets_m->get_wallet_accounts();
        $this->current_balance = 0;
    	$this->wallet_id = $this->wallet_account?'bank-'.$this->wallet_account->id:0;
        if($this->wallet_accounts){
            foreach ($this->wallet_accounts as $key => $wallet_account) {
                $this->wallet_ids.=",'bank-".$wallet_account->id."'";
                $this->current_balance+=($wallet_account->current_balance+$wallet_account->initial_balance);
            }
        }
    }

    function get_account_summary_graph($period =0){
        if(array_key_exists($this->member->id, $this->member_role_holder_options)){
        	$from = strtotime(" 1st ".date('M Y',strtotime("-11 months",time())));
            $format = 'M Y';
            $month_format = "M";
            $result_format = 'M';
            $add_one = TRUE;
            if($period){
                if($period == 'last_7'){
                    $format = 'Ymd';
                    $month_format = "Ymd";
                    $from = strtotime("-7 days",time());
                    $add_one = FALSE;
                    $result_format = 'D';
                }else if ($period == 'last_1') {
                    $format = 'Ymd';
                    $month_format = "Ymd";
                    $add_one = FALSE;
                    $result_format = 'd M';
                    $from = strtotime(" 1st ".date('M Y',strtotime("-1 month",time())));
                }else if($period == 'last_3'){
                    $from = strtotime(" 1st ".date('M Y',strtotime("-2 months",time())));
                }else if($period == 'last_6'){
                    $from = strtotime(" 1st ".date('M Y',strtotime("-5 months",time())));
                }else if($period == 'last_10'){
                    $format = 'Y';
                    $month_format = "Y";
                    $result_format = 'Y';
                    $from = strtotime(" 1st day of ".date('Y',strtotime("-10 years",time())));
                }
            }
        	$posts = $this->transaction_statements_m->get_group_transaction_statement($from,$this->wallet_ids,$this->group->id,0,'DESC',0);
        	$group_transaction_summation = array();
        	foreach ($posts as $post) {
                $date = date($format,$post->transaction_date);
                if(in_array($post->transaction_type, $this->transactions->deposit_transaction_types)){
                    if(array_key_exists($date,$group_transaction_summation)){
                        $group_transaction_summation[$date]+= $post->amount;
                    }else{
                        $group_transaction_summation[$date]= $post->amount;
                    }
                }elseif(in_array($post->transaction_type,$this->transactions->withdrawal_transaction_types)){
                    if(array_key_exists($date,$group_transaction_summation)){
                        $group_transaction_summation[$date]-= $post->amount;
                    }else{
                        $group_transaction_summation[$date]= 0-$post->amount;
                    }   
                }
            }
            $finala = array();
            $today = strtotime(($add_one?'1 ':'').date($format,time()));
            $days = (strtotime(date('d-M-Y',time())) - $from)/(24*60*60)+1;
            for ($i=0; $i < $days; $i++) { 
                $date = date($format,$today);
                if(array_key_exists(date($month_format,$today), $finala)){
                }else{
                    $previous_month = date($month_format,strtotime(($add_one?'+1 month ':'+1 day '),strtotime(($add_one?'1 ':'').date($format,$today))));
                    $previous_months = date($format,strtotime(($add_one?'+1 month ':'+1 day '),strtotime(($add_one?'1 ':'').date($format,$today))));
                    if($date == date($format,time())){
                        $finala[date($month_format,$today)] = floatval($this->current_balance);
                    }else{
                        if(array_key_exists($previous_month, $finala)){
                            $previous_bank_balance = $finala[$previous_month];
                        }else{
                            $previous_bank_balance = 0;
                        }
                        if(array_key_exists($previous_months, $group_transaction_summation)){
                            $previous_bank_balances = $group_transaction_summation[$previous_months];
                        }else{
                            $previous_bank_balances = 0;
                        }
                        $finala[date($month_format,$today)] = ($previous_bank_balance)-($previous_bank_balances);
                    }
                }
                $today-=(24*60*60);
            }
            $reversed = array_reverse($finala,TRUE);
            $months = array();
            $bank_values = array();
            $group = FALSE;
            if(count($reversed) > 16 && count($reversed) < 20){
                $group=TRUE;
                $modulus = 4;
            }elseif (count($reversed) > 20 && count($reversed) < 30 ) {
                $group=TRUE;
                $modulus = 5;
            }elseif (count($reversed) >= 30) {
                $group=TRUE;
                $modulus = 6;
            }
            $i = 0;
            foreach ($reversed as $key => $value) {
                if($group){
                    if(date('Ymd') == date('Ymd',strtotime($key))){
                        $months[] = date($result_format,strtotime($key));
                        $bank_values[] = round($value,2);
                    }else{
                        if(($i%$modulus)==0){
                            $months[] = date($result_format,strtotime($key));
                            $bank_values[] = round($value,2);
                        }
                    }  
                }else{
                    if(is_numeric($key)){
                        $months[] = date($result_format,strtotime($key));
                    }else{
                        $months[] = date($result_format,strtotime('first day of '.$key));
                    }                
                    $bank_values[] = round($value,2);
                }
                $i++;
            }
            $response = array(
                'months' => $months,
                'bank_values' => $bank_values,
            );
            echo json_encode($response);
        }else{
            $response = array(
                'status' => 0,
                'refer' => site_url('authentication'),
                'message' => 'You dont have rights to access this panel.',
            );
            echo json_encode($response);die;
        }
    }

    function wallet_deposits_summary(){
        if(array_key_exists($this->member->id, $this->member_role_holder_options)){
        	$deposits = $this->wallets_m->get_deposits_summary_by_type('',$this->wallet_ids);
        	$arr = array();
        	$other_amounts = 0;
        	$categories = array();
    		$amount = array();
        	foreach ($deposits as $key=>$deposit) {
        		if($key>5){
                    $other_amounts+=($deposit->amount);
                }else{
                    $categories[] = $this->transactions->deposit_transaction_names[$deposit->type];
                    $amount[] = ($deposit->amount);
                }
        	}
        	if($other_amounts){
                $categories[] = 'Other Deposits';
                $amount[] = $other_amounts;
            }
            $response = array(
                'categories' => $categories,
                'amount' => $amount,
            );
        	echo json_encode($response);
        }else{
            $response = array(
                'status' => 0,
                'refer' => site_url('authentication'),
                'message' => 'You dont have rights to access this panel.',
            );
            echo json_encode($response);die;
        }
    }

    function wallet_withdrawal_summary(){
        if(array_key_exists($this->member->id, $this->member_role_holder_options)){
            $withdrawals = $this->wallets_m->get_withdrawal_summary_by_type('',$this->wallet_ids);
            $arr = array();
            $other_amounts = 0;
            $categories = array();
            $amount = array();
            foreach ($withdrawals as $key=>$withdrawal) {
                if($key>5){
                    $other_amounts+=($withdrawal->amount);
                }else{
                    $categories[] = $this->transactions->withdrawal_transaction_names[$withdrawal->type];
                    $amount[] = ($withdrawal->amount);
                }
            }
            if($other_amounts){
                $categories[] = 'Other Deposits';
                $amount[] = $other_amounts;
            }
            $response = array(
                'categories' => $categories,
                'amount' => $amount,
            );
            echo json_encode($response);
        }else{
            $response = array(
                'status' => 0,
                'refer' => site_url('authentication'),
                'message' => 'You dont have rights to access this panel.',
            );
            echo json_encode($response);die;
        }
    }

    function get_expenses_categories_summary(){
        if(array_key_exists($this->member->id, $this->member_role_holder_options)){
            $expense_category_options = $this->expense_categories_m->get_group_expense_category_options();
            $group_expense_category_totals = $this->withdrawals_m->get_group_expense_category_totals_array('','','',$this->wallet_ids);
            $total_expenses = 0; 
            $total_arrears = 0; 
            $count = 1; 
            $categories =  array();
            $amount = array();
            $i=0;
            $other_amounts = 0 ;
            foreach($group_expense_category_totals as $expense_category_id => $group_expense_category_total): 
                if($i>5){
                    $other_amounts+=($group_expense_category_total);
                }else{
                    $total_expenses += $group_expense_category_total;
                    $categories[] = $expense_category_options[$expense_category_id];
                    $amount[] = ($group_expense_category_total);
                }
                $i++;
            endforeach;
            if($other_amounts){
                $categories[] = 'Other Expenses';
                $amount[] = $other_amounts;
            }
            $response = array(
                'categories' => $categories,
                'amount' => $amount,
            );
            echo json_encode($response);
        }else{
            $response = array(
                'status' => 0,
                'refer' => site_url('authentication'),
                'message' => 'You dont have rights to access this panel.',
            );
            echo json_encode($response);die;
        }
    }

    function make_payment_validation(){
        $response = array();
        if($_POST){
            $payment_fors = $this->input->post('payment_fors');
            $contribution_ids = $this->input->post('contribution_ids');
            $fine_categories = $this->input->post('fine_categories');
            $descriptions = $this->input->post('descriptions');
            $loan_ids = $this->input->post('loan_ids');
            $amounts = $this->input->post('amounts');
            $total_amount = 0;
            $valid_form = TRUE;
            $errors = array();
            $form_error = array();
            foreach ($payment_fors as $key => $payment_for) {
                if($payment_for){
                    if($amounts[$key]){
                        if(currency($amounts[$key])){
                            $total_amount+=currency($amounts[$key]);
                            if($payment_for == 1){
                                if($contribution_ids[$key]){
                                    continue;
                                }else{
                                    $errors[$key]['contribution_ids'] = 'Select contribution to pay for';
                                    $valid_form = FALSE;
                                }
                            }elseif ($payment_for == 2) {
                                if($fine_categories[$key]){
                                    continue;
                                }else{
                                    $errors[$key]['fine_categories'] = 'Select Fine Category to pay for';
                                    $valid_form = FALSE;
                                }
                            }else if($payment_for == 3){  
                                if($loan_ids[$key]){
                                    continue;
                                }else{
                                    $errors[$key]['loan_ids'] = 'Select your loan to pay for';
                                    $valid_form = FALSE;
                                }
                            }elseif ($payment_for ==4) {
                                if($descriptions[$key]){
                                    continue;
                                }else{
                                    $errors[$key]['descriptions'] = 'Enter description for Miscellaneous Payment';
                                    $valid_form = FALSE;
                                }
                            }else{
                                $errors[$key]['payment_fors'] = 'Invalid payment for option';
                                $valid_form = FALSE;
                            }
                        }else{
                            $errors[$key]['amounts'] = 'Invalid Amount';
                            $valid_form = FALSE;
                        }
                    }else{
                        $errors[$key]['amounts'] = 'Amount to pay can not be null';
                        $valid_form = FALSE;
                    }
                }else{
                    $valid_form = FALSE;
                    $errors[$key]['payment_fors'] = 'Select what you are paying for';
                } 
            }
            if($valid_form == FALSE){
                $response = array(
                    'status' => 0,
                    'message' => 'Form validation errors',
                    'validation_errors' => $form_error,
                    'fine_validation_errors' => $errors,
                );
            }else{
                if($total_amount>=1){
                    $response = array(
                        'status' => 1,
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Form validation errors',
                        'validation_errors' => 'Ensure amount to pay is more than '.$this->group_currency.' '.number_to_currency(1),
                    );
                }
                
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Form validation errors',
                'validation_errors' => 'Error. No data was submitted',
            );
        }
        echo json_encode($response);
    }

    function calculate_convenience_charge(){
        $response = array();
        if($group_default_bank_account = $this->bank_accounts_m->get_group_default_bank_account($this->group->id)){
            $amounts = $this->input->get_post('amounts')?:array();
            $total_amount = 0;
            foreach ($amounts as $amount) {
                $total_amount+=currency($amount);
            }
            $payment_fors = $this->input->post('payment_fors');
            $contribution_ids = $this->input->post('contribution_ids');
            $fine_categories = $this->input->post('fine_categories');
            $descriptions = $this->input->post('descriptions');
            $loan_ids = $this->input->post('loan_ids');
            $fine_category_options = $this->fine_categories_m->get_group_options(FALSE);
            $contribution_options = $this->contributions_m->get_active_group_contribution_options();
            if(valid_currency($total_amount)){
                $result = $this->transactions->calculate_convenience_charge($this->user,$this->group,$this->member,$group_default_bank_account,$total_amount);
                if($result == 0 || is_numeric($result)){
                    $total_amount_to_pay = number_to_currency($result+$total_amount);
                    $data_fields = '
                        <table class="table table-striped table-bordered table-hover table-payments">
                            <thead>
                                <tr>
                                    <th width="2%">
                                        #
                                    </th>
                                    <th width="70%">
                                        Items
                                    </th>
                                    <th class="text-right" width="28%">
                                        Amount(KES)
                                    </th>
                                </tr>
                            </thead>
                            <tbody>';
                                foreach ($amounts as $key => $amount) {
                                    $payment_for = '';
                                    if($payment_fors[$key]==1){
                                        $payment_for = 'Contribution Payment : '.$contribution_options[$contribution_ids[$key]];
                                    }
                                    if($payment_fors[$key]==2){
                                        $payment_for = 'Fine Payment: '.$fine_category_options[$fine_categories[$key]];
                                    }
                                    if($payment_fors[$key]==3){
                                        $payment_for = 'Loan Repayment';
                                    }
                                    if($payment_fors[$key]==4){
                                        $payment_for = 'Miscellaneous Payment : '.$descriptions[$key];
                                    }
                                    $data_fields.='
                                        <tr>
                                            <td>'.($key+1).'</td>
                                            <td>'.$payment_for.'</td>
                                            <td class="text-right">'.number_to_currency($amount).'</td>
                                        </tr>
                                    ';
                                }
                                if($result){
                                    $data_fields.='
                                        <tr>
                                            <td>'.($key+2).'</td>
                                            <td>Convenience charge</td>
                                            <td class="text-right">'.number_to_currency($result).'</td>
                                        </tr>
                                    ';
                                }
                        $data_fields.=
                            '</tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-left">Total Amount</td>
                                    <td class="text-right"><span class="doubleUnderline">'.$total_amount_to_pay.'</span></td>
                                </tr>
                            </tfoot>
                        </table>
                    ';
                    $response = array(
                        'status' => 200,
                        'message' => 'Transaction charge',
                        "data_fields" => $data_fields,
                        'total_amount_to_pay' => $total_amount_to_pay,
                    );
                }else{
                    $message = $this->session->flashdata('message');
                    if($message){
                        $response = array(
                            'status' => 0,
                            'message' => "Server error: ".$message,
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => "message ".$result,
                        );
                    }
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
                'message' => 'Group does not have an active wallet account. Contact Admin for support',
            );
        }
        echo json_encode($response);
    }

    protected $validation_rules = array(
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

    public $payment_types = array(
        1 => 'Contribution payment',
        2 => 'Fine Payment',
        3 => 'Loan Repayment',
        4 => 'Miscellaneous Payment'
    );

    function _valid_payment_fors(){
        $deposit_type = $this->input->post('payment_fors');
        $amounts = $this->input->post('amounts');
        $contribution_ids = $this->input->post('contribution_ids');
        $fine_category_ids = $this->input->post('fine_categories');
        $group_id = $this->group->id;
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
                            $this->form_validation->set_message('_valid_payment_fors','Loan selected is invalid');
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
            return TRUE;
        }else{
            $this->form_validation->set_message('_valid_amounts','Kindly select atleast 1 item to pay for');
            return FALSE;
        }
    }

    function initiate_member_payments(){
        if($group_default_bank_account = $this->bank_accounts_m->get_group_default_bank_account($this->group->id)){
            $this->form_validation->set_rules($this->validation_rules);
            if($this->form_validation->run()){
                $total_amount = $this->input->post('total_amount');
                $amounts = $this->input->post('amounts');
                $contribution_ids = $this->input->post('contribution_ids');
                $fine_category_ids = $this->input->post('fine_categories');
                $loan_ids = $this->input->post('loan_ids');
                $descriptions = $this->input->post('descriptions');
                $deposit_type = $this->input->post('payment_fors');
                $phone = $this->input->post('phone');
                if(valid_phone($phone)){
                    if($amounts&&$deposit_type){
                        $total_amount = 0;
                        foreach ($amounts as $amount) {
                            $total_amount+=currency($amount);
                        }
                        $transactions = new StdClass();
                        $transactions->total_amount = currency($total_amount);
                        $transactions->amounts = $amounts;
                        $transactions->contribution_ids = $contribution_ids;
                        $transactions->fine_category_ids = $fine_category_ids;
                        $transactions->loan_ids = $loan_ids;
                        $transactions->descriptions = $descriptions;
                        $transactions->deposit_type = $deposit_type;
                        if($result = $this->transactions->make_online_group_payment($this->user,$this->group,$this->member,$group_default_bank_account,$transactions,1,$phone)){
                            if(is_object($result)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Payment in progress. Please wait to enter pin',
                                    'refer' => site_url('group/wallets/deposits'),
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
                    $response = array(
                        'status' => 0,
                        'message' => "Kindly enter a valid phone number to make payment",
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
            );
        }
        echo json_encode($response);
    }

    function get_group_wallet_deposits(){
        $transaction_alert_id = $this->input->get('transaction_alert');
        $data = array();
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $member_only = $this->input->get_post('formember')?:0;
        if(preg_match('/group/', $this->uri->uri_string())){
            $controller_starter = 'group';
        }else{
            $controller_starter = 'member';
        }
        $controller = $member_only?$controller_starter.'/deposits/your_deposits/pages':'group/deposits/listing/pages';
        $filter_parameters = array(
            'transaction_alert_id' => $transaction_alert_id,
            'member_id' => ($member_only?array($this->member->id):($this->input->get('member_id')?:'')),
            'type' => $this->input->get('deposit_for')?:'',
            'contributions' => $this->input->get('contributions')?:'',
            'fine_categories' => $this->input->get('fine_categories')?:'',
            'income_categories' => $this->input->get('income_categories')?:'',
            'stocks' => $this->input->get('stocks')?:'',
            'money_market_investments' => $this->input->get('money_market_investments')?:'',
            'assets' => $this->input->get('assets')?:'',
            'accounts' => array($this->wallet_id),
            'from' => $from,
            'to' => $to,
        );
        
        $deposit_transaction_names = $this->transactions->deposit_transaction_names;
        $deposit_type_options = $this->transactions->deposit_type_options;
        $contribution_options = $this->contributions_m->get_group_contribution_options();
        $fine_category_options = $this->fine_categories_m->get_group_options(FALSE);
        $deposit_method_options = $this->transactions->deposit_method_options;
        $deposit_for_options = $this->transactions->deposit_for_options;
        $depositor_options = $this->depositors_m->get_group_depositor_options();
        $income_category_options = $this->income_categories_m->get_group_income_category_options();
        $stock_options = $this->stocks_m->get_group_stock_options();
        $money_market_investment_options = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
        $accounts = $this->accounts_m->get_group_account_options(FALSE);
        $total_rows = $this->deposits_m->count_group_deposits($this->group->id,$filter_parameters);
        $pagination = create_pagination($controller,$total_rows,50,5,TRUE);
        $posts = $this->deposits_m->limit($pagination['limit'])->get_group_deposits($this->group->id,$filter_parameters);
        if(!empty($posts)){
        echo form_open('group/deposits/action', ' id="form"  class="form-horizontal"');
        if(!empty($pagination['links'])):
            echo '
            <div class="row col-md-12">
                <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Deposits</p>';
                echo '<br/>';
                echo '<div class ="top-bar-pagination">';
                echo $pagination['links']; 
                echo '</div></div>';
                endif; 
            echo '  

                <table class="table m-table m-table--head-separator-primary">
                    <thead>
                        <tr>
                            <th width=\'2%\' nowrap>
                                <label class="m-checkbox">
                                    <input type="checkbox" name="check" value="all" class="check_all">
                                    <span></span>
                                </label>
                            </th>
                            <th nowrap>
                               #
                            </th>
                            <th nowrap>
                               Type
                            </th>
                            <th nowrap>
                               Deposited By
                            </th>
                            <th class=\'text-right\' nowrap>
                                Amount ('.$this->group_currency.')
                            </th>
                            <th  class="text-right" nowrap>
                               Deposited On
                            </th>
                            <th nowrap>
                               &nbsp;
                            </th>
                            ';
                            if($member_only){

                            }else{
                                echo '
                                    <th>
                                        
                                    </th>
                                ';
                            } 
                           echo '
                        </tr>
                    </thead>
                    <tbody>';
                        $i = $this->uri->segment(5, 0); $i++;
                        foreach($posts as $post):
                            echo '
                                <tr>
                                    <td scope="row">
                                        <label class="m-checkbox">
                                            <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" />
                                            <span></span>
                                        </label>
                                    </td>
                                    <td scope="row">'.
                                        $i++.'
                                    </td>
                                    <td nowrap>'.$deposit_transaction_names[$post->type];
                                        if($post->transaction_alert_id){
                                            
                                        }
                                    echo '</td><td>';
                                        if($post->type==13||$post->type==14||$post->type==15||$post->type==16){
                                            echo $depositor_options[$post->depositor_id];
                                        }else if($post->type==17||$post->type==18||$post->type==19||$post->type==20){
                                            echo $this->group_member_options[$post->member_id]; 
                                        }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){
                                            echo ' - ';
                                        }else if($post->type==29||$post->type==30||$post->type==31||$post->type==32){
                                            echo $money_market_investment_options[$post->money_market_investment_id];
                                        }else if($post->type==21||$post->type==22||$post->type==23||$post->type==24){
                                            echo ' - ';
                                        }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){
                                            echo ' - ';
                                        }else if($post->type==33||$post->type==34||$post->type==35||$post->type==36){
                                            echo ' - ';
                                        }else if($post->type==37||$post->type==38||$post->type==39||$post->type==40){
                                            echo ' - ';
                                        }else if($post->type==45||$post->type==46||$post->type==47||$post->type==48){
                                            echo ' - ';
                                        }else if($post->type==49||$post->type==50||$post->type==51||$post->type==52){
                                            echo ' - ';
                                        }else{
                                            echo isset($this->group_member_options[$post->member_id])?$this->group_member_options[$post->member_id]:''; 
                                        }
                                    echo '
                                        </td><td class="text-right">'.
                                            number_to_currency($post->amount).
                                        '</td><td class="text-right">'.
                                            timestamp_to_date($post->deposit_date).
                                        '</td>';
                                    if($member_only){

                                    }else{
                                        echo '
                                        <td nowrap>
                                            <a href="javascript:;" class="btn btn-sm btn-primary m-btn m-btn--icon view_deposit action_button" id="'.$post->id.'" data-toggle="modal" data-target="#deposit_receipt">
                                                <span>
                                                    <i class="la la-eye"></i>
                                                    <span>
                                                        More &nbsp;&nbsp; 
                                                    </span>
                                                </span>
                                            </a>
                                            <a href="'.site_url('group/deposits/void/'.$post->id).'" class="btn btn-sm confirmation_link btn-danger m-btn m-btn--icon action_button" data-message="Are you sure you want to void deposit?">
                                                <span>
                                                    <i class="la la-trash"></i>
                                                    <span>
                                                        Void &nbsp;&nbsp;
                                                    </span>
                                                </span>
                                            </a>';
                                        echo '
                                            </td>';
                                    }
                                    echo'
                                </tr>';
                        endforeach;

                        echo '
                    </tbody>
                </table>

                <div class="row col-md-12">'.(empty($pagination['links'])?'</div>':$pagination['links'].'</div>');
                    // echo (empty($pagination['links'])?'</div>':$pagination['links'].'</div>';
            if($member_only){

            }else{
                if($posts):
                    echo '<button class="btn btn-sm btn-info" name=\'btnAction\' value=\'bulk_pdf_receipts\' data-placement="top"> <i class=\'fa fa-copy\'></i> Generate Bulk Receipts</button>';
                    echo '&nbsp; &nbsp;';
                    echo '<button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_void\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-trash-o\'></i> Bulk Void</button>';
                endif;
            }
            echo form_close();
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'.translate('Sorry').'!</strong> '.translate('There are no deposit records to display').'.
                </div>
            ';
        }
    }

    function get_member_wallet_deposits(){
        $transaction_alert_id = $this->input->get('transaction_alert');
        $data = array();
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $member_only = $this->input->get_post('formember')?:0;
        if(preg_match('/group/', $this->uri->uri_string())){
            $controller_starter = 'group';
        }else{
            $controller_starter = 'member';
        }
        $controller = $member_only?$controller_starter.'/deposits/your_deposits/pages':'group/deposits/listing/pages';
        $filter_parameters = array(
            'transaction_alert_id' => $transaction_alert_id,
            'member_id' => array($this->member->id),
            'type' => $this->input->get('deposit_for')?:'',
            'contributions' => $this->input->get('contributions')?:'',
            'fine_categories' => $this->input->get('fine_categories')?:'',
            'income_categories' => $this->input->get('income_categories')?:'',
            'stocks' => $this->input->get('stocks')?:'',
            'money_market_investments' => $this->input->get('money_market_investments')?:'',
            'assets' => $this->input->get('assets')?:'',
            'accounts' => array(),
            'from' => $from,
            'to' => $to,
        );
        
        $deposit_transaction_names = $this->transactions->deposit_transaction_names;
        $deposit_type_options = $this->transactions->deposit_type_options;
        $contribution_options = $this->contributions_m->get_group_contribution_options();
        $fine_category_options = $this->fine_categories_m->get_group_options(FALSE);
        $deposit_method_options = $this->transactions->deposit_method_options;
        $deposit_for_options = $this->transactions->deposit_for_options;
        $depositor_options = $this->depositors_m->get_group_depositor_options();
        $income_category_options = $this->income_categories_m->get_group_income_category_options();
        $stock_options = $this->stocks_m->get_group_stock_options();
        $money_market_investment_options = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
        $accounts = $this->accounts_m->get_group_account_options(FALSE);
        $total_rows = $this->deposits_m->count_group_deposits($this->group->id,$filter_parameters);
        $pagination = create_pagination($controller,$total_rows,50,5,TRUE);
        $posts = $this->deposits_m->limit($pagination['limit'])->get_group_deposits($this->group->id,$filter_parameters);
        if(!empty($posts)){
        echo form_open('group/deposits/action', ' id="form"  class="form-horizontal"');
        if(!empty($pagination['links'])):
            echo '
            <div class="row col-md-12">
                <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Deposits</p>';
                echo '<br/>';
                echo '<div class ="top-bar-pagination">';
                echo $pagination['links']; 
                echo '</div></div>';
                endif; 
            echo '  

                <table class="table m-table m-table--head-separator-primary">
                    <thead>
                        <tr>
                            <th width=\'2%\' nowrap>
                                <label class="m-checkbox">
                                    <input type="checkbox" name="check" value="all" class="check_all">
                                    <span></span>
                                </label>
                            </th>
                            <th nowrap>
                               #
                            </th>
                            <th nowrap>
                               '.translate('Type').'
                            </th>
                            <th nowrap>
                              '.translate('Deposited By').'
                            </th>
                            <th class=\'text-right\' nowrap>
                               '.translate('Amount ').'('.$this->group_currency.')
                            </th>
                            <th  class="text-right" nowrap>
                               '.translate('Deposited On').'
                            </th>
                            <th nowrap>
                               &nbsp;
                            </th>
                            ';
                            if($member_only){

                            }else{
                                echo '
                                    <th>
                                        
                                    </th>
                                ';
                            } 
                           echo '
                        </tr>
                    </thead>
                    <tbody>';
                        $i = $this->uri->segment(5, 0); $i++;
                        foreach($posts as $post):
                            echo '
                                <tr>
                                    <td scope="row">
                                        <label class="m-checkbox">
                                            <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" />
                                            <span></span>
                                        </label>
                                    </td>
                                    <td scope="row">'.
                                        $i++.'
                                    </td>
                                    <td nowrap>'.$deposit_transaction_names[$post->type];
                                        if($post->transaction_alert_id){
                                            
                                        }
                                        if($post->contribution_id){
                                            echo '  - '.$contribution_options[$post->contribution_id];
                                        }
                                    echo '</td><td>';
                                        if($post->type==13||$post->type==14||$post->type==15||$post->type==16){
                                            echo $depositor_options[$post->depositor_id];
                                        }else if($post->type==17||$post->type==18||$post->type==19||$post->type==20){
                                            echo $this->group_member_options[$post->member_id]; 
                                        }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){
                                            echo ' - ';
                                        }else if($post->type==29||$post->type==30||$post->type==31||$post->type==32){
                                            echo $money_market_investment_options[$post->money_market_investment_id];
                                        }else if($post->type==21||$post->type==22||$post->type==23||$post->type==24){
                                            echo ' - ';
                                        }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){
                                            echo ' - ';
                                        }else if($post->type==33||$post->type==34||$post->type==35||$post->type==36){
                                            echo ' - ';
                                        }else if($post->type==37||$post->type==38||$post->type==39||$post->type==40){
                                            echo ' - ';
                                        }else if($post->type==45||$post->type==46||$post->type==47||$post->type==48){
                                            echo ' - ';
                                        }else if($post->type==49||$post->type==50||$post->type==51||$post->type==52){
                                            echo ' - ';
                                        }else{
                                            echo isset($this->group_member_options[$post->member_id])?$this->group_member_options[$post->member_id]:''; 
                                        }
                                    echo '
                                        </td><td class="text-right">'.
                                            number_to_currency($post->amount).
                                        '</td><td class="text-right">'.
                                            timestamp_to_date($post->deposit_date).
                                        '</td>';
                                    if($member_only){

                                    }else{
                                        echo '
                                        <td nowrap>
                                            <a href="javascript:;" class="btn btn-sm btn-primary m-btn m-btn--icon view_deposit action_button" id="'.$post->id.'" data-toggle="modal" data-target="#deposit_receipt">
                                                <span>
                                                    <i class="la la-eye"></i>
                                                    <span>
                                                        More &nbsp;&nbsp; 
                                                    </span>
                                                </span>
                                            </a>';
                                        echo '
                                            </td>';
                                    }
                                    echo'
                                </tr>';
                        endforeach;

                        echo '
                    </tbody>
                </table>

                <div class="row col-md-12">'.(empty($pagination['links'])?'</div>':$pagination['links'].'</div>');
                    // echo (empty($pagination['links'])?'</div>':$pagination['links'].'</div>';
            if($member_only){

            }else{
                if($posts):
                endif;
            }
            echo form_close();
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'.translate('Sorry').'!</strong> '.translate('There are no deposit records to display').'.
                </div>
            ';
        }
    }
}
?>
