<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bank extends Bank_Controller{

    protected $data = array();
    protected $setup_tasks;
    protected $loan_repayment_period_type = array(
        1=>  'Fixed Repayment Period',
        2=>  'Varying Repayment Period',
    );
    protected $loan_amount_type = array(
        1=>'Based on Amount Range',
        2=>'Based On Member Savings',
    );

	function __construct(){
        parent::__construct();
        $this->load->model('bank_accounts/bank_accounts_m');
        $this->load->model('mobile_money_accounts/mobile_money_accounts_m');
        $this->load->model('sacco_accounts/sacco_accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->model('group_roles/group_roles_m');
        $this->load->library('contribution_invoices');
        // $this->load->model('setup_tasks_m');
        // $this->load->library('setup_tasks_tracker');
        // $this->sms_template_default = $this->contribution_invoices->sms_template_default;
        // $this->completed_setup_tasks = $this->setup_tasks_m->get_completed_setup_tasks_array();
        // $this->setup_tasks = $this->setup_tasks_m->get_ordered_setup_tasks();
        // $this->data['setup_tasks'] = $this->setup_tasks;
    }

    public function listing(){
        $this->template->title('Group Setup Tasks')->build('group/listing',$this->data);
    }
  
    public function index(){
        if($this->setup_tasks){
            $task_0 = $this->setup_tasks[0];
            $url = $task_0->call_to_action_link;
            redirect('group/setup_tasks/group_setup');
        }else{
            redirect('group/setup_tasks/group_setup');
        }        
    }

    public function group_setup($group_id=0){
        if(!$this->group = $this->groups_m->get($group_id)){
            $this->session->set_flashdata('error','The group selected could not be found');
            redirect('bank');
        }
        $this->session->set_userdata('group_id',$group_id);
        $this->active_group_member_options = $this->members_m->get_active_group_member_options($group_id);
        $this->group_currency = $this->currency_code_options[$this->group->currency_id];
        
        // print_r($this->group); die;
        $this->load->model('setup_tasks_m');
        $this->load->library('setup_tasks_tracker');
        $this->sms_template_default = $this->contribution_invoices->sms_template_default;
        $this->completed_setup_tasks = $this->setup_tasks_m->get_completed_setup_tasks_array();
        $this->setup_tasks = $this->setup_tasks_m->get_ordered_setup_tasks();
        $this->data['setup_tasks'] = $this->setup_tasks;
        
        $this->data['invoice_days'] = $this->contribution_invoices->invoice_days;
        $this->data['month_days'] = $this->contribution_invoices->month_days;
        $this->data['week_days'] = $this->contribution_invoices->week_days;
        $this->data['days_of_the_month'] = $this->contribution_invoices->days_of_the_month;
        $this->data['every_two_week_days'] = $this->contribution_invoices->every_two_week_days;
        $this->data['months'] = $this->contribution_invoices->months;
        $this->data['starting_months'] = $this->contribution_invoices->starting_months;
        $this->data['week_numbers'] = $this->contribution_invoices->week_numbers;
        $this->data['contribution_frequency_options'] = $this->contribution_invoices->contribution_frequency_options;
        $this->data['contribution_type_options'] = $this->contribution_invoices->contribution_type_options;
        $this->data['contribution_category_options'] = $this->contribution_invoices->contribution_category_options;
        $this->data['sms_template_default'] = $this->sms_template_default;
        $this->data['fine_types'] = $this->contribution_invoices->fine_types;
        $this->data['fine_chargeable_on_options'] = $this->contribution_invoices->fine_chargeable_on_options;
        $this->data['fine_frequency_options'] = $this->contribution_invoices->fine_frequency_options;
        $this->data['fine_mode_options'] = $this->contribution_invoices->fine_mode_options;
        $this->data['fine_limit_options'] = $this->contribution_invoices->fine_limit_options;
        $this->data['percentage_fine_on_options'] = $this->contribution_invoices->percentage_fine_on_options;
        $this->data['contribution_days_option']=$this->contribution_invoices->contribution_days_option;        
        $this->data['starting_days'] = $this->contribution_invoices->starting_days;
        $this->data['twice_every_one_month'] = $this->contribution_invoices->twice_every_one_month;
        $this->data['starting_months'] = $this->contribution_invoices->starting_months;
        $this->data['type_of_groups'] = $this->investment_groups->type_of_groups;
        $this->data['coutries'] = $this->countries_m->get_country_options();
        $this->data['currencies'] = $this->countries_m->get_currency_options();
        $this->data['type_of_groups'] = $this->investment_groups->type_of_groups;
        $group_roles= $this->group_roles_m->get_group_role_options();
        $assigned_roles = $this->members_m->get_assigned_group_role_options();
        foreach ($assigned_roles as $assigned_role_id) {
            unset($group_roles[$assigned_role_id]);
        }
        $this->data['group_roles'] = $group_roles+array(''=>'Member','0'=>'--Add new role--');
        $this->data['interest_types'] = $this->loan->interest_types;
        $this->data['loan_repayment_period_type'] = $this->loan->loan_repayment_period_type;
        $this->data['loan_amount_type'] = $this->loan->loan_amount_type;
        $this->data['loan_interest_rate_per'] = $this->loan->loan_interest_rate_per;
        $this->data['loan_processing_fee_types'] = $this->loan->loan_processing_fee_types;
        $this->data['late_loan_payment_fine_types'] = $this->loan->late_loan_payment_fine_types;
        $this->data['late_payments_fine_frequency'] = $this->loan->late_payments_fine_frequency;
        $this->data['fixed_amount_fine_frequency_on'] = $this->loan->fixed_amount_fine_frequency_on;
        $this->data['percentage_fine_on'] = $this->loan->percentage_fine_on;
        $this->data['one_off_fine_types'] = $this->loan->one_off_fine_types;
        $this->data['one_off_percentage_rate_on'] = $this->loan->one_off_percentage_rate_on;
        $this->data['loan_grace_periods'] = $this->loan->loan_grace_periods;
        $this->data['loan_days'] = $this->loan->loan_days;
        $this->data['loan_processing_fee_percentage_charged_on'] = $this->loan->loan_processing_fee_percentage_charged_on;
        $this->data['system_group_roles'] = $this->investment_groups->system_group_roles;
        $this->data['banks'] = $this->banks_m->get_group_bank_options('',$this->group->country_id);
        $this->data['saccos'] = $this->saccos_m->get_group_sacco_options($this->group->country_id);
        // $this->data['saccos'] = $this->saccos_m->get_group_sacco_options($this->group->country_id);
        $this->data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options($this->group->country_id);
        $default_bank = $this->banks_m->get_default_bank();
        $this->data['default_bank'] = $default_bank?:(object)array('id'=>0);
        if($default_bank){
            $this->data['bank_branches'] = $this->bank_branches_m->get_bank_branch_options_by_bank_id($default_bank->id);
        }else{
            $this->data['bank_branches'] = array();
        }
        $this->template->set_layout('setup_tasks.html')->title('Group Setup Tasks')->build('bank/group_setup',$this->data);
    }

    public function accounts(){
        $default_bank = $this->banks_m->get_default_bank();
        $this->data['default_bank'] = $default_bank;
        $this->data['bank_accounts'] = $this->bank_accounts_m->get_group_bank_accounts();
        $this->data['sacco_accounts'] = $this->sacco_accounts_m->get_group_sacco_accounts();
        $this->data['banks'] = $this->banks_m->get_group_bank_options();
        $this->data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $this->data['mobile_money_accounts'] = $this->mobile_money_accounts_m->get_group_mobile_money_accounts();
        $this->data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $this->template->set_layout('setup_tasks.html')->title('Group Accounts')->build('bank/accounts',$this->data);
    }

    public function members(){
        if($this->application_settings->enforce_group_setup_tasks){
            if(in_array('create-group-bank-account',$this->completed_setup_tasks)){
            }else{
            }
        }
        $this->data['group_role_options'] = $this->group_roles_m->get_group_role_options();
        $this->data['members'] = $this->members_m->get_group_members();
        $this->template->set_layout('setup_tasks.html')->title('Group Members')->build('bank/members',$this->data);
    }

    public function contributions(){
        if($this->application_settings->enforce_group_setup_tasks){
            if(in_array('add-group-members',$this->completed_setup_tasks)){

            }else{
                //$this->session->set_flashdata('info',"You need to add all ".$this->group->size." group members before you can proceed. ");
                //redirect('group/setup_tasks/members');
            }
        }
        $posts = $_POST;
        $this->data['posts'] = $posts;
        $this->data['placeholders'] = isset($placeholders[0])?$placeholders[0]:array();
        $this->data['invoice_days'] = $this->contribution_invoices->invoice_days;
        $this->data['month_days'] = $this->contribution_invoices->month_days;
        $this->data['week_days'] = $this->contribution_invoices->week_days;
        $this->data['days_of_the_month'] = $this->contribution_invoices->days_of_the_month;
        $this->data['every_two_week_days'] = $this->contribution_invoices->every_two_week_days;
        $this->data['months'] = $this->contribution_invoices->months;
        $this->data['starting_months'] = $this->contribution_invoices->starting_months;
        $this->data['week_numbers'] = $this->contribution_invoices->week_numbers;
        $this->data['contribution_frequency_options'] = $this->contribution_invoices->contribution_frequency_options;
        $this->data['contribution_type_options'] = $this->contribution_invoices->contribution_type_options;
        $this->data['sms_template_default'] = $this->sms_template_default;
        $this->data['fine_types'] = $this->contribution_invoices->fine_types;
        $this->data['fine_chargeable_on_options'] = $this->contribution_invoices->fine_chargeable_on_options;
        $this->data['fine_frequency_options'] = $this->contribution_invoices->fine_frequency_options;
        $this->data['fine_mode_options'] = $this->contribution_invoices->fine_mode_options;
        $this->data['fine_limit_options'] = $this->contribution_invoices->fine_limit_options;
        $this->data['percentage_fine_on_options'] = $this->contribution_invoices->percentage_fine_on_options;
        $this->template->set_layout('setup_tasks.html')->title('Group Contributions')->build('bank/contributions',$this->data);
    }

    public function date(){
        if($this->incomplete_setup_tasks_count>0){
            $this->session->set_flashdata('info',"Complete your group set up first, you need to add at least one bank account, invite all your members and set up your contributions to be able to back-date");
            redirect("group/setup_tasks/accounts");
        }
        $this->data = array();
        $this->data['group_cut_off_date'] = $this->transaction_statements_m->get_group_cut_off_date();
        $this->template->set_layout('back_date.html')->title('Set Date')->build('bank/date',$this->data);
    }

    public function contribution_targets(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $group_cut_off_date OR redirect("group/setup_tasks/date");
        $this->data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $this->data['group_member_total_contribution_back_dated_arrears_per_contribution_array'] = $this->invoices_m->get_group_member_total_contribution_back_dated_arrears_per_contribution_array();
        $this->data['group_cut_off_date'] = $group_cut_off_date;
        $this->template->set_layout('back_date.html')->title('Back Date Contributions')->build('bank/contribution_targets',$this->data);
    }

    public function contribution_refunds(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $group_cut_off_date OR redirect("group/setup_tasks/date");
        $this->data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $this->data['group_member_total_contribution_back_dated_arrears_per_contribution_array'] = $this->invoices_m->get_group_member_total_contribution_back_dated_arrears_per_contribution_array();
        $this->data['group_cut_off_date'] = $group_cut_off_date;
        $this->template->set_layout('back_date.html')->title('Back Date Contributions Refunds')->build('bank/contribution_refunds',$this->data);
    }

    public function fines_issued(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $group_cut_off_date OR redirect("group/setup_tasks/date");
        $this->data['group_cut_off_date'] = $group_cut_off_date;
        $this->data['fine_category_options'] = $this->fine_categories_m->get_group_fine_category_options();
        $this->template->set_layout('back_date.html')->title('Back Date Fines Issued')->build('bank/fines_issued',$this->data);
    }
    
    public function expenses(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $group_cut_off_date OR redirect("group/setup_tasks/date");
        $this->data['group_cut_off_date'] = $group_cut_off_date;
        $this->data['expense_category_options'] = $this->expense_categories_m->get_group_expense_category_options();
        $this->template->set_layout('back_date.html')->title('Back Date Expenses')->build('bank/expenses',$this->data);
    }

    public function loans_borrowed(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $group_cut_off_date OR redirect("group/setup_tasks/date");
        $this->data['group_cut_off_date'] = $group_cut_off_date;
        $this->template->set_layout('back_date.html')->title('Back Date Loans Borrowed')->build('group/loans_borrowed',$this->data);
    }

    public function loans_paid(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $group_cut_off_date OR redirect("group/setup_tasks/date");
        $this->data['group_cut_off_date'] = $group_cut_off_date;
        $this->template->set_layout('back_date.html')->title('Back Date Loans Paid')->build('group/loans_paid',$this->data);
    } 

    public function group_loans_borrowed(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $group_cut_off_date OR redirect("group/setup_tasks/date");
        $this->data['group_cut_off_date'] = $group_cut_off_date;
        $this->template->set_layout('back_date.html')->title('Back Date Group Loans Borrowed')->build('group/group_loans_borrowed',$this->data);
    }

    public function group_loans_paid(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $group_cut_off_date OR redirect("group/setup_tasks/date");
        $this->data['group_cut_off_date'] = $group_cut_off_date;
        $this->template->set_layout('back_date.html')->title('Back Date Group Loans Paid')->build('group/group_loans_paid',$this->data);
    }

    public function stocks(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $group_cut_off_date OR redirect("group/setup_tasks/date");
        $this->data['group_cut_off_date'] = $group_cut_off_date;
        $this->template->set_layout('back_date.html')->title('Back Date Group Stocks')->build('group/stocks',$this->data);
    }

    public function money_market_investments(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $group_cut_off_date OR redirect("group/setup_tasks/date");
        $this->data['group_cut_off_date'] = $group_cut_off_date;
        $this->template->set_layout('back_date.html')->title('Back Date Group Money Market Investments')->build('group/money_market_investments',$this->data);
    }

    public function assets(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $group_cut_off_date OR redirect("group/setup_tasks/date");
        $this->data['group_cut_off_date'] = $group_cut_off_date;
        $this->data['asset_category_options'] = $this->asset_categories_m->get_group_asset_category_options();
        $this->template->set_layout('back_date.html')->title('Back Date Group Assets')->build('group/assets',$this->data);
    }

    public function income(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $group_cut_off_date OR redirect("group/setup_tasks/date");
        $this->data['group_cut_off_date'] = $group_cut_off_date;
        $this->template->set_layout('back_date.html')->title('Back Date Group Income')->build('group/income',$this->data);
    }

    public function account_balances(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $group_cut_off_date OR redirect("group/setup_tasks/date");
        if($this->input->post('submit')){
            //die;
            if($suspense_petty_cash_account = $this->petty_cash_accounts_m->get_group_back_dating_petty_cash_suspense_account()){
                $suspense_account_id = "petty-".$suspense_petty_cash_account->id;
                $account_balances = $this->input->post('account_balances');
                $current_account_balances = $this->input->post('current_account_balances');
                //print_r($account_balances);
                if($account_balances&&$current_account_balances){
                    $result = TRUE;
                    if($deposits = $this->deposits_m->get_group_back_dating_incoming_account_transfers()){
                        foreach($deposits as $deposit):
                            if($this->transactions->void_group_deposit($deposit->id,$deposit,FALSE,$this->group->id)){

                            }else{
                                $result = FALSE;
                            }
                        endforeach;
                    }else{

                    }
                    foreach($account_balances as $account_id => $amount):
                        if(currency($amount)==currency($current_account_balances[$account_id])){
                            //ignore for now
                           
                        }else{
                            $difference = currency($amount) - currency($current_account_balances[$account_id]);
                            
                            if($difference>0){
                                
                                //transfer from 
                                if($this->transactions->record_account_transfer($this->group->id,$group_cut_off_date->cut_off_date,$suspense_account_id,$account_id,currency($difference),'Back-dating funds transfer',0,TRUE)){

                                }else{
                                    $result = FALSE;
                                }
                            }else if($difference<0){
                                //transfer from
                                if($this->transactions->record_account_transfer($this->group->id,$group_cut_off_date->cut_off_date,$account_id,$suspense_account_id,currency(abs($difference)),'Back-dating funds transfer',0,TRUE)){

                                }else{
                                    $result = FALSE;
                                }
                            }
                        }
                    endforeach;
                    if($result){
                        $this->session->set_flashdata('success',"Funds successfully transferred from the back-dating suspense account to your operating accounts");
                    }else{
                        $this->session->set_flashdata('error',"Something went wrong");
                    }
                }else{
                    $this->session->set_flashdata('warning',"Account balances inputs are not set");
                }
            }else{
                $this->session->set_flashdata('warning',"Suspense account not found");
            }
        }
        $this->data['group_back_dating_outgoing_account_transfer_amounts_array'] = $this->withdrawals_m->get_group_back_dating_outgoing_account_transfer_amounts_array();
        $this->data['group_back_dating_incoming_account_transfer_amounts_array'] = $this->deposits_m->get_group_back_dating_incoming_account_transfer_amounts_array();
        $this->data['group_cut_off_date'] = $group_cut_off_date;
        $this->data['suspense_petty_cash_account'] = $this->petty_cash_accounts_m->get_group_back_dating_petty_cash_suspense_account();
        $this->data['account_options'] = $this->accounts_m->get_active_group_account_options(TRUE,'','','',TRUE);
        //print_r($this->data['account_options']);
        //die;
        $this->data['account_balances'] = $this->accounts_m->get_group_account_balances_array();
        $this->template->set_layout('back_date.html')->title('Back Date Account Balances')->build('group/account_balances',$this->data);
    }

    function loan_types(){
        $posts = $_POST;
        $this->data['posts'] = $posts;
        $loan_types = $this->loan_types_m->get_all();
        $this->data['loan_types'] = $loan_types;
        $this->data['interest_types'] = $this->loan->interest_types;
        $this->data['loan_interest_rate_per'] = $this->loan->loan_interest_rate_per;
        $this->data['loan_processing_fee_types'] = $this->loan->loan_processing_fee_types;
        $this->data['late_loan_payment_fine_types'] = $this->loan->late_loan_payment_fine_types;
        $this->data['late_payments_fine_frequency'] = $this->loan->late_payments_fine_frequency;
        $this->data['fixed_amount_fine_frequency_on'] = $this->loan->fixed_amount_fine_frequency_on;
        $this->data['percentage_fine_on'] = $this->loan->percentage_fine_on;
        $this->data['one_off_fine_types'] = $this->loan->one_off_fine_types;
        $this->data['one_off_percentage_rate_on'] = $this->loan->one_off_percentage_rate_on;
        $this->data['loan_processing_fee_percentage_charged_on'] = $this->loan->loan_processing_fee_percentage_charged_on;
        $this->data['group_role_options'] = $this->group_roles_m->get_group_role_options();
        $this->data['loan_amount_type'] = $this->loan_amount_type;
        $this->data['loan_repayment_period_type'] = $this->loan_repayment_period_type;
        $this->template->set_layout('setup_tasks.html')->title('Set Up Lending Rules')->build('group/lending_rules',$this->data);
    }

}