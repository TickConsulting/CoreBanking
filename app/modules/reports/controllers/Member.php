<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends Member_Controller{

    protected $data = array();
	
	function __construct(){
        parent::__construct();
        $this->load->library('transactions');
        $this->load->model('accounts/accounts_m');
        $this->load->model('contributions/contributions_m');
        $this->load->model('fine_categories/fine_categories_m');
        $this->load->model('income_categories/income_categories_m');
        $this->load->model('expense_categories/expense_categories_m');
        $this->load->model('depositors/depositors_m');
        $this->load->model('deposits/deposits_m');
        $this->load->model('stocks/stocks_m');
        $this->load->model('loans/loans_m');
        $this->load->model('loan_repayments/loan_repayments_m');
        $this->load->model('assets/assets_m');
        $this->load->model('bank_loans/bank_loans_m');
        $this->load->model('money_market_investments/money_market_investments_m');
        $this->load->model('transaction_statements/transaction_statements_m');
    }

    function index(){
        $this->template->title('Group Reports')->build('shared/index');
    }

    public function account_balances(){
        $this->data['account_options'] = $this->accounts_m->get_active_group_account_options();
        $this->data['account_balances'] = $this->accounts_m->get_group_account_balances_array();
        $this->template->title('Account Balances')->build('shared/account_balances',$this->data);
    }

    public function transaction_statement(){
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-3 months');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $account_ids = $this->input->get('accounts');
        $this->data['account_ids'] = $account_ids;
        $account_list_ids = '0';
        $count = 1;
        $account_options = $this->accounts_m->get_active_group_account_options(FALSE);
        if($account_ids){
            foreach ($account_ids as $account_id) {
                if($account_id){
                    if($count==1){
                        $account_list_ids='"'.$account_id.'"';
                    }else{
                        $account_list_ids.=',"'.$account_id.'"';
                    }
                    $count++;
                }
            }
        }else{
            foreach ($account_options as $account_id => $account_name) {
                if($account_id){
                    if($count==1){
                        $account_list_ids='"'.$account_id.'"';
                    }else{
                        $account_list_ids.=',"'.$account_id.'"';
                    }
                    $count++;
                }
            }
        }
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $this->data['starting_balance'] = $this->transaction_statements_m->get_starting_balance($from,$account_list_ids);
        $this->data['transaction_names'] = $this->transactions->transaction_names;
        $this->data['posts'] = $this->transaction_statements_m->get_group_transaction_statement($from,$account_list_ids);
        $this->data['account_options'] = $account_options;
        $this->data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $this->data['fine_category_options'] = $this->fine_categories_m->get_group_options();
        $this->data['income_category_options'] = $this->income_categories_m->get_group_income_category_options();
        $this->data['expense_category_options'] = $this->expense_categories_m->get_group_expense_category_options();
        $this->data['stock_sale_options'] = $this->stocks_m->get_group_stock_sale_options();
        $this->data['depositor_options'] = $this->depositors_m->get_group_depositor_options();
        $this->data['bank_loan_options'] = $this->bank_loans_m->get_group_bank_loan_options();
        $this->data['loan_options'] = $this->loans_m->get_group_loan_options();
        $this->data['asset_options'] = $this->assets_m->get_group_asset_options();
        $this->data['stock_purchase_options'] = $this->withdrawals_m->get_group_stock_purchase_options();
        $this->data['money_market_investment_options'] = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
        $this->data['group'] = $this->group;
        $this->data['group_currency'] = $this->group_currency;
        $this->data['application_settings'] = $this->application_settings;
        if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
            $this->data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
        }else{
            $this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
        }
        if($generate_pdf==TRUE){
            $this->data['transactions'] = $this->transactions;
            $this->data['group_member_options'] = $this->group_member_options;
            $response = $this->curl_post_data->curl_post_json_pdf((json_encode($this->data)),'https://pdfs.chamasoft.com/transaction_statement',$this->group->name.' Transaction Statement');
            print_r($response);die;
        }else{
            $this->template->set_layout('default_full_width.html')->title('Transaction Statement')->build('shared/transaction_statement',$this->data);
        }    
    }

    public function loans_summary(){
        
        if($this->group->enable_member_information_privacy){ 
            
            $this->session->set_flashdata("info","You do not have permissions to view this report");
            redirect("member");
            
        }
        $post = array();
        $amount_paid = array();
        $base_where = array();
        $amount_payable_to_date = array();
        $projected_profit = array();
        $total_loan_out = $this->loans_m->get_total_loaned_amount();
        $total_loan_paid = $this->loan_repayments_m->get_total_loan_paid();
        $loans = $this->loans_m->get_many_by($base_where);
        foreach ($loans as $loan){
            $post[] = $this->loans_m->get_summation_for_invoice($loan->id);
            $amount_paid[$loan->id] = $this->loan_repayments_m->get_loan_total_payments($loan->id);
            $amount_payable_to_date[$loan->id] = $this->loans_m->loan_payable_and_principle_todate($loan->id);
            $projected_profit[$loan->id] = $this->loans_m->get_projected_interest($loan->id,$amount_paid[$loan->id]);
        }
        $this->data['posts'] = $post;
        $this->data['total_loan_out'] = $total_loan_out;
        $this->data['total_loan_paid'] = $total_loan_paid;
        $this->data['amount_paid'] = $amount_paid;
        $this->data['projected_profit'] = $projected_profit;
        $this->data['amount_payable_to_date'] = $amount_payable_to_date;
        $this->data['members'] = $this->group_member_options;
        $this->template->title('Loans Summary')->build('shared/loans_summary',$this->data);
    }

    function cash_flow_statement(){
        $member_total_contribution_refunds_per_contribution_array =$this->withdrawals_m->get_group_member_total_contribution_refunds_per_contribution_array();
        $this->data['member_total_contribution_refunds_per_contribution_array'] = $member_total_contribution_refunds_per_contribution_array;
        $this->data['total_contribution_refunds'] =array_sum($this->withdrawals_m->get_group_member_total_contribution_refunds_array());
        $this->data['member_total_contributions_per_contribution_array'] = $this->deposits_m->get_group_member_total_contributions_per_contribution_array();
        $this->data['members'] = $this->group_member_options;
        $this->data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $group_member_fine_totals = $this->deposits_m->get_group_member_total_fines_array();
        $this->data['total_member_fines'] = array_sum($group_member_fine_totals);
        $this->data['group_member_fine_totals'] = $group_member_fine_totals;
        $group_member_miscellaneous_totals = $this->deposits_m->get_group_member_total_miscellaneous_array();
        $this->data['total_group_member_miscellaneous_totals'] = array_sum($group_member_miscellaneous_totals);
        $this->data['group_member_miscellaneous_totals'] = $group_member_miscellaneous_totals;
        $this->data['group_member_miscellaneous_totals'] = $group_member_miscellaneous_totals;
        $group_income_totals = $this->deposits_m->get_group_income_categories_total_per_income_array();
        $total_incomes=0;
        foreach ($group_income_totals as $key => $income) {
            $total_incomes+=$income->amount;
        }        $this->data['total_group_income_totals'] = $total_incomes;
        $this->data['group_income_totals'] = $group_income_totals;
        $loan_repayments = $this->deposits_m->get_group_loan_repayments();
        $this->data['total_loan_repayments'] = array_sum($loan_repayments);
        $this->data['loan_repayments'] = $loan_repayments;
        $this->data['bank_loan_amount'] = $this->deposits_m->get_total_bank_loan_amount();
        $this->data['incoming_account_tranfer_amount'] = $this->deposits_m->get_incoming_account_transfer_amount();
        $this->data['total_asset_sale_amount'] = $this->deposits_m->get_group_total_asset_sale_amount();
        $this->data['total_stock_sale_amount'] = $this->deposits_m->get_group_total_stock_sale_amount();
        $this->data['total_money_market_cash_in_amount'] = $this->deposits_m->get_group_total_money_market_cash_in_amount();

        $this->data['income_categories'] = $this->income_categories_m->get_group_income_category_options();

        $this->data['loans'] = $this->withdrawals_m->get_group_loans_disbursment();
        $this->data['total_amount_loaned']= array_sum($this->data['loans']);
        $this->data['expense_category_options'] = $this->expense_categories_m->get_group_expense_category_options();
        $this->data['group_expense_category_totals'] = $this->withdrawals_m->get_group_expense_category_totals_array();
        $this->data['bank_loan_repayment_amount'] = $this->withdrawals_m->get_group_total_bank_loan_repayment();
        $this->data['account_transfer_amount'] = $this->withdrawals_m->get_outgoing_account_transfer_total_amount();
        $this->data['total_asset_purchase_amount'] = $this->withdrawals_m->get_group_asset_purchase_total_amount();
        $this->data['total_stock_purchase_amount'] = $this->withdrawals_m->get_group_stock_purchase_total_amount();
        $this->data['money_market_investment_amount'] = $this->withdrawals_m->get_group_money_market_investment_total_amount();

        //print_r($this->data);die;
        $this->template->set_layout('member_default.html')->title('Cash Flow Statement')->build('shared/cash_flow_statement',$this->data);
    }

    function balance_sheet_statement(){

        $this->data['asset_valuation'] = $this->assets_m->get_group_asset_value();
        $this->data['stock_valuation'] = $this->stocks_m->get_group_current_stocks_value();

        $total_money_market_cash_in_amount = $this->deposits_m->get_group_total_money_market_cash_in_amount();
        $money_market_investment_amount = $this->withdrawals_m->get_group_money_market_investment_total_amount();
        $this->data['money_market_profit'] =$total_money_market_cash_in_amount - $money_market_investment_amount;
        $this->data['bank_account_balance']  = $this->bank_accounts_m->get_group_total_bank_account_balance();
        $this->data['sacco_account_balance']  = $this->sacco_accounts_m->get_group_total_sacco_account_balance();
        $this->data['mobile_money_account_balance']  = $this->mobile_money_accounts_m->get_group_total_mobile_money_account_balance();
        $this->data['petty_cash_account_balance']  = $this->petty_cash_accounts_m->get_group_total_petty_cash_account_balance();
        $this->data['member_total_contribution_balances_per_contribution_array'] = $this->statements_m->get_group_member_total_contribution_balances_per_contribution_array();
        $total_member_balances=0;
        foreach ($this->data['member_total_contribution_balances_per_contribution_array'] as $key => $value) {
           $total_member_balances+=array_sum($value);
        }
        if($total_member_balances>0){

        }else{
            $total_member_balances=0;
        };
        $this->data['total_member_balances'] = $total_member_balances;
        $this->data['total_asset_purchase_paid_amount'] = $this->withdrawals_m->get_group_asset_purchase_total_amount();

        $this->data['total_contribution_per_contribution'] = $this->deposits_m->total_contributions_per_contribution_array();
        
        
        $this->data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $this->data['group_contribution_totals'] = array_sum($this->data['total_contribution_per_contribution'])+$total_member_balances;
        $this->data['group_contribution_refund_totals'] = array_sum($this->withdrawals_m->get_group_member_total_contribution_refunds_array());
        $group_fine_totals = array_sum($this->deposits_m->get_group_member_total_fines_array());
        $group_fine_balance_totals = array_sum($this->statements_m->get_group_member_total_fine_balances_array());
        $this->data['fine_balances'] = $group_fine_balance_totals;
        $this->data['group_fines'] = $group_fine_balance_totals+$group_fine_totals;
        $this->data['group_member_miscellaneous_totals'] = array_sum($this->deposits_m->get_group_member_total_miscellaneous_array());
        $group_stock_sales_value = $this->stocks_m->get_group_stock_value();
        $this->data['stock_profit'] = ($group_stock_sales_value->total_current_price - $group_stock_sales_value->total_initial_price);
        $this->data['group_stock_sales_value'] = $group_stock_sales_value;
        $bank_loan_amount_received = $this->deposits_m->get_total_bank_loan_amount();
        $bank_loan_amount_paid = $this->withdrawals_m->get_group_total_bank_loan_repayment();
        $this->data['bank_loan_amount'] = $bank_loan=$bank_loan_amount_received-$bank_loan_amount_paid;
        $group_income_totals = $this->deposits_m->get_group_income_categories_total_per_income_array();
        $total_loan_repaid = $this->deposits_m->get_total_group_loan_payments();
        $total_loan_disbursed = array_sum($this->withdrawals_m->get_group_loans_disbursment());
        $this->data['unpaid_loan'] = $total_loan_disbursed - $total_loan_repaid;
        $total_incomes=0;
        foreach ($group_income_totals as $key => $income) {
            $total_incomes+=$income->amount;
        }
        $this->data['group_income_totals'] = $total_incomes;
        $this->data['loan_profit'] = 0;
        $this->data['loan_debt']=0;
        $this->data['group_expense_totals'] = array_sum($this->withdrawals_m->get_group_expense_category_totals_array());
        //print_r($this->data);die;
        $this->template->set_layout('member_default.html')->title('Balance Sheet Statement')->build('shared/balance_sheet_statement',$this->data);
    }

    function income_statement(){
        //incomes
        if($this->input->get('set_date')){
            $date_from = strtotime($this->input->get('date_from'));
            $date_to = strtotime($this->input->get('date_to'));
           $this->data['bank_loan_amount'] = $this->deposits_m->get_total_bank_loan_amount('',$date_from,$date_to);
            $this->data['total_stock_sale_amount'] = $this->deposits_m->get_group_total_stock_sale_amount('',$date_from,$date_to);
            $this->data['total_asset_sale_amount'] = $this->deposits_m->get_group_total_asset_sale_amount('',$date_from,$date_to);
            $this->data['total_money_market_cash_in_amount'] = $this->deposits_m->get_group_total_money_market_cash_in_amount('',$date_from,$date_to);
            $this->data['loan_repayments'] = array_sum($this->deposits_m->get_group_loan_repayments('',$date_from,$date_to));
            $external_income_totals = $this->deposits_m->get_group_income_categories_total_per_income_array('',$date_from,$date_to);

            $total_extenal_income=0;
            foreach ($external_income_totals as $external_income_total){
                $total_extenal_income+= $external_income_total->amount;
            }
            $this->data['total_external_incomes'] =$total_extenal_income;
            $this->data['per_category_external_income'] = $external_income_totals;
            $this->data['income_categories'] = $this->income_categories_m->get_group_income_category_options();

            //expenses
            $this->data['expense_category_options'] = $this->expense_categories_m->get_group_expense_category_options();
            $group_expense_category_totals = $this->withdrawals_m->get_group_expense_category_totals_array('',$date_from,$date_to);
            $this->data['total_expenses_amount'] = array_sum($group_expense_category_totals);
            $this->data['group_expense_category_totals'] = $group_expense_category_totals;
            $this->data['bank_loan_repayment_amount'] = $this->withdrawals_m->get_group_total_bank_loan_repayment('',$date_from,$date_to);
            //$this->data['group_contribution_refund_totals'] = array_sum($this->withdrawals_m->get_group_member_total_contribution_refunds_array('',$date_from,$date_to));
            $this->data['loans'] = array_sum($this->withdrawals_m->get_group_loans_disbursment('',$date_from,$date_to));
            $this->data['money_market_investment_amount'] = $this->withdrawals_m->get_group_money_market_investment_total_amount('',$date_from,$date_to);
            $this->data['total_stock_purchase_amount'] = $this->withdrawals_m->get_group_stock_purchase_total_amount('',$date_from,$date_to); 
            $this->data['date_to'] =$this->input->get('date_to');
            $this->data['date_from'] =$this->input->get('date_from');
        }
        //print_r($this->data);die;
        $this->template->title('Income Statement')->build('shared/income_statement',$this->data);
    }

    function bank_loans_summary(){
        $posts = array();
        $this->data['total_loan_received_and_repaid'] = $this->bank_loans_m->total_loan_received_and_paid();

        $this->data['posts'] = $this->bank_loans_m->get_loan_report();
        //print_r($this->data);die;
        $this->template->set_layout('member_default.html')->title('Bank Loans Summary')->build('shared/bank_loans_summary',$this->data);
    }

    function contributions_summary($generate_excel=FALSE){

        if($this->group->enable_member_information_privacy){ 
            
            $this->session->set_flashdata("info","You do not have permissions to view this report");
            redirect("member");
            
        }
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-3 years');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        /**
        $this->data['member_total_contribution_balances_per_contribution_array'] = $this->statements_m->get_group_member_total_contribution_balances_per_contribution_array($this->group->id,$to,$from);
        $this->data['member_total_contribution_refunds_per_contribution_array'] = $this->withdrawals_m->get_group_member_total_contribution_refunds_per_contribution_array($this->group->id,0,$to,$from);
        //$this->data['member_total_contribution_transfers_to_per_contribution_array'] = $this->statements_m->get_group_member_total_contribution_transfers_to_per_contribution_array($this->group->id,$to,$from);
        $this->data['member_total_contribution_transfers_to_per_contribution_array'] = array();
        $this->data['member_total_contribution_transfers_from_per_contribution_array'] = $this->statements_m->get_group_member_total_contribution_transfers_from_per_contribution_array($this->group->id,$to,$from);
        //$this->data['member_total_contribution_transfers_from_per_contribution_array'] = array();
        $this->data['members_contribution_transfers_from_contribution_to_fine_category_per_contribution_array'] = $this->statements_m->get_group_members_contribution_transfers_from_contribution_to_fine_category_per_contribution_array($this->group->id,$to,$from);
        $this->data['member_total_contribution_transfers_to_ignore_per_contribution_array'] = $this->statements_m->get_group_member_total_contribution_transfers_to_ignore_per_contribution_array($this->group->id,$to,$from);
        $this->data['member_total_contributions_per_contribution_array'] = $this->deposits_m->get_group_member_total_contributions_per_contribution_array($this->group->id,$to,$from);
        $this->data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $this->data['group_member_contribution_totals'] = $this->deposits_m->get_group_member_total_contributions_array($this->group->id,$to,$from);
        $this->data['group_member_contribution_refund_totals'] = $this->withdrawals_m->get_group_member_total_contribution_refunds_array($this->group->id,$from,$to);
        $this->data['member_total_contribution_transfers_to_fines_array'] = $this->statements_m->get_group_member_total_contribution_transfers_to_fines_array($this->group->id,$to,$from);
        $this->data['suspended_members_ids_array'] = $this->members_m->get_suspended_members_ids_array();
        **/
        
        $this->data['member_total_contribution_transfers_to_fines_array'] = $this->statements_m->get_group_member_total_contribution_transfers_to_fines_array();
        
        $this->data['group_member_contribution_totals'] = $this->deposits_m->get_group_member_total_contributions_array();
        $this->data['group_member_contribution_refund_totals'] = $this->withdrawals_m->get_group_member_total_contribution_refunds_array();
        $this->data['group_member_contribution_balance_totals'] = $this->statements_m->get_group_member_total_contribution_balances_array();
        $this->data['group_member_cumulative_contribution_balance_totals'] = $this->statements_m->get_group_member_total_cumulative_contribution_balances_array();
        
        $this->data['group_member_options'] = $this->group_member_options;
        $this->data['group'] = $this->group;
        $this->data['group_currency'] = $this->group_currency;
        if($generate_excel==TRUE){
        }
        $this->template->title('Contribution Summary')->build('shared/contributions_summary',$this->data);
    }

    function fines_summary(){

        if($this->group->enable_member_information_privacy){ 
            
            $this->session->set_flashdata("info","You do not have permissions to view this report");
            redirect("member");
            
        }
        $this->data['member_total_contribution_transfers_to_fines_array'] = $this->statements_m->get_group_member_total_contribution_transfers_to_fines_array();
        $this->data['suspended_members_ids_array'] = $this->members_m->get_suspended_members_ids_array();
        $this->data['group_member_fine_totals'] = $this->deposits_m->get_group_member_total_fines_array();
        $this->data['group_member_fine_balance_totals'] = $this->statements_m->get_group_member_total_fine_balances_array();
        $this->template->title('Fines Summary')->build('shared/fines_summary',$this->data);
    }

    function expenses_summary(){
        $this->data['expense_category_options'] = $this->expense_categories_m->get_group_expense_category_options();
        $this->data['group_expense_category_totals'] = $this->withdrawals_m->get_group_expense_category_totals_array();
        $this->template->title('Expenses Summary')->build('shared/expenses_summary',$this->data);
    }

    function trial_balance(){
        $this->data['bank_account_balance']  = $this->bank_accounts_m->get_group_total_bank_account_balance();
        $this->data['sacco_account_balance']  = $this->sacco_accounts_m->get_group_total_sacco_account_balance();
        $this->data['mobile_money_account_balance']  = $this->mobile_money_accounts_m->get_group_total_mobile_money_account_balance();
        $this->data['petty_cash_account_balance']  = $this->petty_cash_accounts_m->get_group_total_petty_cash_account_balance();
        $this->data['starting_capital'] = $this->accounts_m->get_total_group_accounts_starting_balances();
        $this->data['asset_valuation'] = $this->assets_m->get_group_asset_value();
        $this->data['stock_valuation'] = $this->stocks_m->get_group_current_stocks_value();
        $this->data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $this->data['member_total_contribution_balances_per_contribution_array'] = $this->statements_m->get_group_member_total_contribution_balances_per_contribution_array();
        $total_member_balances=0;
        foreach ($this->data['member_total_contribution_balances_per_contribution_array'] as $key => $value) {
           $total_member_balances+=array_sum($value);
        }
        if($total_member_balances>0){

        }else{
            $total_member_balances=0;
        };
        $this->data['total_member_balances'] = $total_member_balances;
        $this->data['total_asset_purchase_paid_amount'] = $this->withdrawals_m->get_group_asset_purchase_total_amount();
        $this->data['total_contribution_per_contribution'] = $this->deposits_m->total_contributions_per_contribution_array();
        $this->data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $this->data['group_contribution_totals'] = array_sum($this->data['total_contribution_per_contribution'])+$total_member_balances;
        $this->data['group_contribution_refund_totals'] = array_sum($this->withdrawals_m->get_group_member_total_contribution_refunds_array());
        $group_fine_totals = array_sum($this->deposits_m->get_group_member_total_fines_array());
        $group_fine_balance_totals = array_sum($this->statements_m->get_group_member_total_fine_balances_array());
        if($group_fine_balance_totals>0){

        }else{
            $group_fine_balance_totals=0;
        }
        $this->data['fine_balances'] = $group_fine_balance_totals;
        $this->data['group_fines'] = $group_fine_balance_totals+$group_fine_totals;
        $this->data['group_member_miscellaneous_totals'] = array_sum($this->deposits_m->get_group_member_total_miscellaneous_array());
        $group_income_totals = $this->deposits_m->get_group_income_categories_total_per_income_array();
        $total_incomes=0;
        foreach ($group_income_totals as $key => $income) {
            $total_incomes+=$income->amount;
        }
        $this->data['group_income_totals'] = $total_incomes;
        $this->data['total_money_market_cash_in_amount'] = $this->deposits_m->get_group_total_money_market_cash_in_amount();
        $this->data['money_market_investment_amount'] = $this->withdrawals_m->get_group_money_market_investment_total_amount();
        $this->data['expense_category_options'] = $this->expense_categories_m->get_group_expense_category_options();
        $this->data['group_expense_category_totals'] = $this->withdrawals_m->get_group_expense_category_totals_array();
        $group_stock_sales_value = $this->stocks_m->get_group_stock_value();
        $this->data['stock_profit'] = ($group_stock_sales_value->total_current_price - $group_stock_sales_value->total_initial_price);
        $this->data['group_stock_sales_value'] = $group_stock_sales_value;
        $total_loan_repaid = $this->deposits_m->get_total_group_loan_payments();
        $total_loan_disbursed = array_sum($this->withdrawals_m->get_group_loans_disbursment());
        $this->data['unpaid_loan'] = $total_loan_disbursed - $total_loan_repaid;
        $this->data['loan_profit'] = 0;
        $this->data['loan_debt']=0;
        $this->template->title('Trial Balance')->build('shared/trial_balance',$this->data);
    }
}