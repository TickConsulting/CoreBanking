<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Bank extends Bank_Controller{

	protected $data = array();

	function __construct(){
        parent::__construct();
        $this->load->model('reports_m');
        $this->load->model('groups/groups_m');
        $this->load->model('bank_accounts/bank_accounts_m');
        $this->load->model('bank_branches/bank_branches_m');
        $this->load->model('transaction_alerts/transaction_alerts_m');
    }

    function index(){
    	
    }

    function daily_kpis(){
        $this->data['groups_signed_up_today_by_bank_branch'] = $this->groups_m->get_groups_signed_up_today_by_bank_branch();
        $this->data['groups_signed_up_today_count'] = $this->groups_m->count_groups_signed_up_today();
        $this->data['users_signed_up_today_count'] = $this->users_m->count_users_signed_up_today();
        $this->data['bank_branch_options'] = $this->bank_branches_m->get_bank_branch_options_by_bank_id($this->bank->id);
        $this->data['groups_signed_up_today_count_by_bank_branch_array'] = $this->groups_m->get_groups_signed_up_today_count_by_bank_branch_array($this->data['bank_branch_options']);
        $this->data['bank_accounts_by_bank_branch_count'] = $this->bank_accounts_m->get_bank_accounts_by_bank_branch_count($this->bank->id);
        $this->data['total_deposit_transactions_amount_for_today_by_bank_branch_id_array'] = $this->transaction_alerts_m->get_total_deposit_transactions_amount_for_today_by_bank_branch_id_array();
        $this->data['total_withdrawal_transactions_amount_for_today_by_bank_branch_id_array'] = $this->transaction_alerts_m->get_total_withdrawal_transactions_amount_for_today_by_bank_branch_id_array();
        $this->template->title('Daily KPIs')->build('bank/daily_kpis',$this->data);
    }

}