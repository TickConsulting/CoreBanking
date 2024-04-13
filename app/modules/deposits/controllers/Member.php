<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Member extends Member_Controller{

    protected $deposit_type_options = array(
        1 => "Contribution payment",
        2 => "Contribution fine payment",
        3 => "Fine payment",
        4 => "Incoming Bank Transfer",
        5 => "External deposit",
        6 => "Group Expense payment",
        7 => "Loan repayment",
        8 => "Financial Institution Loan",
        9 => "Other user defined deposit",
    );

    protected $transfer_to_options = array(
        1 => "Another contribution",
    );

	function __construct(){
        parent::__construct();
        $this->load->model('deposits_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('assets/assets_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->model('contributions/contributions_m');
        $this->load->model('fine_categories/fine_categories_m');
        $this->load->model('depositors/depositors_m');
        $this->load->model('income_categories/income_categories_m');
        $this->load->model('stocks/stocks_m');
        $this->load->model('money_market_investments/money_market_investments_m');
        $this->load->library('transactions');
        $this->load->library('loan');
    }

    function index(){
        $data = array();
        $this->template->title('Deposits')->build('member/index',$data);
    }

    function your_deposits(){
        $member = $this->member;
        $transaction_alert_id = $this->input->get('transaction_alert');
        $data = array();
        $filter_parameters = array(
            'transaction_alert_id' => $transaction_alert_id
        );
        $data['deposit_transaction_names'] = $this->transactions->deposit_transaction_names;
        $total_rows = $this->deposits_m->count_group_and_member_deposits($this->group->id,$member->id,$filter_parameters);
        $data['from'] = strtotime($this->input->get('from'))?:'';
        $data['to'] = strtotime($this->input->get('to'))?:'';
        $pagination = create_pagination('member/deposits/listing/pages', $total_rows,50,5,TRUE);
        $data['pagination'] = $pagination;
        $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
        $data['deposit_method_options'] = $this->transactions->deposit_method_options;
        $data['posts'] = $this->deposits_m->limit($pagination['limit'])->get_group_and_member_deposits($this->group->id,$member->id,$filter_parameters);
        $data['depositor_options'] = $this->depositors_m->get_group_depositor_options();
        $data['income_category_options'] = $this->income_categories_m->get_group_income_category_options();
        $data['stock_options'] = $this->stocks_m->get_group_stock_options();
        $data['money_market_investment_options'] = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
        $data['accounts'] = $this->accounts_m->get_group_account_options(FALSE);
        $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
        $data['asset_options'] = $this->assets_m->get_group_asset_options();
        $data['deposit_type_options'] = $this->transactions->deposit_type_options;
        $data['member_only'] = TRUE;
        $this->template->title('Your Deposits')->build('shared/listing',$data);
    }

    function listing(){
        $transaction_alert_id = $this->input->get('transaction_alert');
        $data = array();
        $filter_parameters = array(
            'transaction_alert_id' => $transaction_alert_id
        );
        $data['deposit_transaction_names'] = $this->transactions->deposit_transaction_names;
        $total_rows = $this->deposits_m->count_group_deposits($this->group->id,$filter_parameters);
        $pagination = create_pagination('member/deposits/listing/pages', $total_rows,50,5,TRUE);
        $data['pagination'] = $pagination;
        $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
        $data['deposit_method_options'] = $this->transactions->deposit_method_options;
        $data['posts'] = $this->deposits_m->limit($pagination['limit'])->get_group_deposits($this->group->id,$filter_parameters);
        $data['depositor_options'] = $this->depositors_m->get_group_depositor_options();
        $data['income_category_options'] = $this->income_categories_m->get_group_income_category_options();
        $data['stock_options'] = $this->stocks_m->get_group_stock_options();
        $data['money_market_investment_options'] = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
        $data['accounts'] = $this->accounts_m->get_group_account_options(FALSE);
        $this->template->title('List Deposits')->build('member/listing',$data);
    }

    function view($id=0){
        $id OR redirect($this->agent->referrer(),'refresh');
        $post = $this->deposits_m->get_group_deposit($id);
        if(empty($post)){
            $this->session->set_flashdata('error','Sorry, the entry does not exists.');
            redirect($this->agent->referrer(),'refresh');
            die;
        }

        $data['deposit_transaction_names'] = $this->transactions->deposit_transaction_names;
        $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
        $data['deposit_method_options'] = $this->transactions->deposit_method_options;
        $data['depositor_options'] = $this->depositors_m->get_group_depositor_options();
        $data['income_category_options'] = $this->income_categories_m->get_group_income_category_options();
        $data['stock_options'] = $this->stocks_m->get_group_stock_options();
        $data['money_market_investment_options'] = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
        $data['accounts'] = $this->accounts_m->get_group_account_options(FALSE);

        $data['post'] = $post;
        //print_r($post);die;
        $this->template->title('View Deposit')->build('shared/view',$data);
    }

    function make_payment(){
        $this->data['default_calling_code'] = $this->countries_m->get_default_calling_code();
        $this->data['total_member_payments'] = $this->deposits_m->get_member_total_payments();
        $this->data['member_active_loans'] = $this->loans_m->get_member_loans_option($this->member->id);
        $this->data['payment_for_options'] = $this->transactions->payment_for_options;
        $this->data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
        $this->data['contribution_options'] = $this->contributions_m->get_active_group_contribution_options();
        $this->data['total_group_member_contribution_arrears'] = $this->statements_m->get_member_contribution_balance($this->group->id,$this->member->id);
        $this->data['total_group_member_fine_arrears'] = $this->statements_m->get_member_fine_balance($this->group->id,$this->member->id);
        $group_default_bank_account = $this->bank_accounts_m->get_group_default_bank_account($this->group->id);
        $default_account_number = 0;
        if($group_default_bank_account){
            $default_account_number = $group_default_bank_account->account_number;
        }
        $this->data['group_default_bank_account'] = $default_account_number;

        $ongoing_loan_amounts_payable = array();
        $ongoing_loan_amounts_paid = array();
        $base_where = array('member_id'=>$this->member->id,'is_fully_paid'=>0);
        $ongoing_member_loans = $this->loans_m->get_many_by($base_where);
        foreach ($ongoing_member_loans as $ongoing_member_loan){
            $ongoing_loan_amounts_payable[$ongoing_member_loan->id]
            = $this->loans_m->get_summation_for_invoice($ongoing_member_loan->id)->total_amount_payable;
            $ongoing_loan_amounts_paid[$ongoing_member_loan->id]
            = $this->loan_repayments_m->get_loan_total_payments($ongoing_member_loan->id);
        }
        $ongoing_payable_loan_amount = array_sum($ongoing_loan_amounts_payable);
        $ongoing_loans_paid_amount = array_sum($ongoing_loan_amounts_paid);
        $this->data['total_loan_balances'] = $ongoing_payable_loan_amount - $ongoing_loans_paid_amount;
        $this->template->title('Make Group Contributions')->build('shared/make_payments',$this->data);
    }
}