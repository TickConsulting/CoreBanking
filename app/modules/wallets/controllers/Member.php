<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends Member_Controller{
	protected $data = array();
    protected $path = 'uploads/logos/wallet_logos/';
	public function __construct(){
        parent::__construct();
        $this->load->model('wallets_m');
        $this->load->model('deposits/deposits_m');
    }

    function index(){
    	/*$wallet_account = $this->wallets_m->get_wallet_account();
    	$wallet_id = $wallet_account?$wallet_account->id:0;
    	$this->data['wallet_balance'] = $this->wallets_m->get_group_account_balance();
    	$this->data['wallet_deposits'] = $this->wallets_m->get_group_total_deposits('',$wallet_id);
        $this->data['wallet_withdrawals'] = $this->wallets_m->get_group_total_withdrawals('',$wallet_id);
    	$this->data['wallet_expenses'] = $this->wallets_m->get_group_total_expenses('',$wallet_id);*/
        $this->benchmark->mark('code_start');
        $data['total_group_fines'] = $this->deposits_m->get_group_total_fines();
        $data['total_group_contributions'] = $this->deposits_m->get_group_total_contributions();
        $data['total_group_contribution_refunds'] = $this->withdrawals_m->get_group_total_contribution_refunds();
        $data['total_group_contribution_transfers_from_loan_to_contribution'] = $this->statements_m->get_group_total_contribution_transfers_from_loan_to_contribution($this->member->id);
        $data['total_group_contributions_transfers_to_fines'] = $this->statements_m->get_group_total_contribution_transfers_to_fines();
        $data['total_group_contributions_by_month_array'] = $this->deposits_m->get_group_total_contributions_by_month_array();
        $data['total_group_fines_by_month_array'] = $this->deposits_m->get_group_total_fines_by_month_array();
        $data['total_group_miscellaneous_payments_by_month_array'] = $this->deposits_m->get_group_total_miscellaneous_payments_by_month_array();
        $data['total_group_income_by_month_array'] = $this->deposits_m->get_group_total_income_by_month_array();
        $data['total_group_expenses_by_month_array'] = $this->withdrawals_m->get_group_total_expenses_by_month_array();
    
        $data['total_group_expenses'] = $this->withdrawals_m->get_group_total_expenses();
        $data['total_cash_at_bank'] = $this->accounts_m->get_group_total_cash_at_bank();
        $data['total_cash_at_hand'] = $this->accounts_m->get_group_total_cash_at_hand();
        $data['bank_accounts'] = $this->bank_accounts_m->get_group_bank_accounts();
        $data['membership_numbers'] = $this->members_m->get_membership_number();
        $data['total_group_contributions_transfers_to_loans'] = $this->statements_m->get_group_total_contribution_transfers_to_loans();
        $this->template->title(translate('Bank Account Dashboard'))->build('member/index',$data);
    	//$this->template->title('Wallet Dashboard')->build('member/index',$data);
    }

    function deposits(){
        $this->data['from'] = time();
        $this->data['to'] = time();
        $this->data['deposit_type_options'] = array();
        $this->data['account_options'] = array();
        $this->data['contribution_options'] = array();
        $this->data['fine_category_options'] = array();
        $this->data['income_category_options'] = array();
        $this->data['stock_options'] = array();
        $this->data['money_market_investment_options'] = array();
        $this->data['asset_options'] = array();
        $this->template->title(translate('Your Deposits'))->build('member/deposits',$this->data);
    }

    

    function make_deposit(){
        $country_id = $this->group->country_id;
        $wallets = $this->wallets_m->get_wallets_in_country($country_id);
        $this->data['wallets'] = $wallets;
        $this->data['path'] = $this->path;
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
        $data['member_only'] = TRUE;
    	$this->template->title('Make Wallet Deposit')->build('shared/make_deposit',$this->data);
    }

}
?>