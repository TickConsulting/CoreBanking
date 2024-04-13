<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{

    protected $data = array();
	
	function __construct(){
        parent::__construct();
        $this->load->library('transactions');
        $this->load->library('excel_library');        
        $this->load->model('reports_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('contributions/contributions_m');
        $this->load->model('fine_categories/fine_categories_m');
        $this->load->model('income_categories/income_categories_m');
        $this->load->model('expense_categories/expense_categories_m');
        $this->load->model('depositors/depositors_m');
        $this->load->model('deposits/deposits_m');
        $this->load->model('stocks/stocks_m');
        $this->load->model('loans/loans_m');
        $this->load->model('loan_invoices/loan_invoices_m');
        $this->load->model('invoices/invoices_m');
        $this->load->model('loan_repayments/loan_repayments_m');
        $this->load->model('assets/assets_m');
        $this->load->model('bank_loans/bank_loans_m');
        $this->load->model('money_market_investments/money_market_investments_m');
        $this->load->model('transaction_statements/transaction_statements_m');
        $this->load->library('pdf_library');
    }

    function index(){
        $member_ids = $this->input->get_post('member_ids')?:0;
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-10 year');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $this->data['member_ids'] = $member_ids;
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $refundable_contribution_options = $this->contributions_m->get_group_refundable_contribution_options();
        $this->data['contribution_options'] = $refundable_contribution_options;
        if($this->input->get('generate_excel')==1){
            if($member_ids){
                $member_options = array_flip(array_filter($member_ids))?:$this->active_group_member_options;
            }else{
                $member_options = $this->active_group_member_options;
            }
            $refundable_contributions_per_year_array = $this->reports_m->get_group_member_contributions_per_year_array($this->group->id,$from,$to,$refundable_contribution_options,$member_options);
            if($refundable_contributions_per_year_array){
                $years_array = array();
                $grand_total_balance = array();
                $year_months_array = array();
                $years_array = generate_years_from_dates($from,$to);
                $years_months_array = generate_years_months_from_dates($from,$to);
                $year_months_array = generate_years_months_from_dates($from,$to);
                foreach ($refundable_contributions_per_year_array as $key => $years) {           
                    foreach ($years as $year_key => $months) {                   
                        $years_array[] = $year_key;
                        foreach ($months as $month_value => $amount) {
                            $grand_total_balance[$year_key][$month_value] = 0;
                        } 
                    }               
                } 
                $years_array = array_unique($years_array);
                $group_members = array();
                foreach ($this->active_group_member_options as $member_id => $member_name) {
                    if(array_key_exists($member_id, $member_options)){
                        $group_members[$member_id] = $member_name;
                    }
                }

                $this->data['years'] = array_unique($years_array);
                $this->data['year_months_array'] = generate_years_months_from_dates($from,$to);
                $this->data['refundable_contributions_per_year_array'] = $refundable_contributions_per_year_array;
                $this->data['grand_total_balance'] = $grand_total_balance;
                $this->data['group'] = $this->group;
                $this->data['active_group_member_options'] = $group_members;
                $this->data['group_currency'] = $this->group_currency;
            }
            print_r($this->curl_post_data->curl_post_json_excel(json_encode($this->data),'https://excel.chamasoft.com/deposits/member_deposits_summary',$this->group->name.' Deposits Summary'));
            die;
        }  
        $this->template->title(translate('Members Deposits Summary'))->build('shared/deposits_summary',$this->data); 
    }

    public function account_balances(){
        if($this->input->get('generate_excel')==1){
            $this->data['account_options'] = $this->accounts_m->get_active_group_account_options();
            $this->data['account_balances'] = $this->accounts_m->get_group_account_balances_array($this->group->id);
           
            $this->data['group_currency'] = $this->group_currency;
            $this->data['group'] = $this->group;
            $json_file = json_encode($this->data);
            $this->excel_library->generate_account_balances($json_file);
            print_r($json_file); die();/*
            $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/account_balances',$this->group->name.' Account balances');
            print_r($response);die;*/
        }
        $this->template->title(translate('Account Balances'))->build('shared/account_balances',$this->data);
    }

    function open_pdf_view($html=''){
        $html = $this->input->post('html');
        $this->pdf_library->generate_landscape_report($html);
    }

    public function transaction_statement($generate_pdf=FALSE){
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-3 months');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $account_ids = $this->input->get('accounts');
        $this->data['account_ids'] = $account_ids;
        $account_list_ids = '0';
        $count = 1;
        $account_options = $this->accounts_m->get_active_group_account_options(FALSE,FALSE,TRUE,'',FALSE);
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
        $this->data['account_options'] = $account_options;
        if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
            $this->data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
        }else{
            $this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
        }
        
        if($this->input->get('generate_excel') == 1 || $generate_pdf){
            
            $this->data['starting_balance'] = $this->transaction_statements_m->get_starting_balance($from,$account_list_ids,$to);
            $this->data['transaction_names'] = $this->transactions->transaction_names;
            $this->data['posts'] = $this->transaction_statements_m->get_group_transaction_statement($from,$account_list_ids,$this->group->id,0,'',0,$to);
            
            $this->data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
            $this->data['fine_category_options'] = $this->fine_categories_m->get_group_options();
            $this->data['income_category_options'] = $this->income_categories_m->get_group_income_category_options();
            $this->data['expense_category_options'] = $this->expense_categories_m->get_group_expense_category_options();
            $this->data['stock_sale_options'] = $this->stocks_m->get_group_stock_sale_options();
            $this->data['depositor_options'] = $this->depositors_m->get_group_depositor_options();
            $this->data['bank_loan_options'] = $this->bank_loans_m->get_group_bank_loan_options();
            $this->data['loan_options'] = $this->loans_m->get_group_loan_options();
            $this->data['external_lending_loan_options'] = $this->debtors_m->get_group_loan_options();
            $this->data['asset_options'] = $this->assets_m->get_group_asset_options();
            $this->data['stock_purchase_options'] = $this->withdrawals_m->get_group_stock_purchase_options();
            $this->data['money_market_investment_options'] = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
            $this->data['group'] = $this->group;
            $this->data['group_currency'] = $this->group_currency;
            $this->data['application_settings'] = $this->application_settings;
            $this->data['transactions'] = $this->transactions;
            $this->data['group_member_options'] = $this->group_member_options;
            $this->data['group_debtor_options'] = $this->group_debtor_options;
            $json_file = json_encode($this->data);

            if($generate_pdf){
                $data['pdf_true'] = TRUE;
                $html = $this->load->view('shared/transaction_statement',$this->data,TRUE);
                $this->pdf_library->generate_landscape_report($html);
                die;
            }else{
                $this->excel_library->generate_transaction_statement($json_file);
            }
            die; 
        }
        else{
            $this->template->title(translate('Transaction Statement'))->build('shared/transaction_statement',$this->data);
        }
    }

    public function loans_summary($generate_pdf=FALSE,$generate_excel=FALSE){
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-10 year');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $member_ids = $this->input->get_post('member_ids')?:0;

        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $this->data['member_ids'] = $member_ids;
        $this->data['loan_type_options'] = $this->loan_types_m->get_options();
        if(isset($_GET) && !empty($_GET)){
            $this->data['total_loan_out'] = $this->loans_m->get_total_loaned_amount();
            $this->data['total_loan_paid'] = $this->loan_repayments_m->get_total_loan_paid();
            $external_lending_total_loan_out = $this->debtors_m->get_total_loaned_amount();
            $external_lending_total_loan_paid = $this->debtors_m->get_total_loan_paid();
            $base_where = array(
                'member_id'=>$member_ids,
                'from' => $from,
                'to' => $to,
            );
            $posts = array();
            $amount_paid = array();
            $amount_payable_to_date = array();
            $projected_profit = array();
            $loans = $this->loans_m->get_many_by($base_where);
            foreach ($loans as $loan){
                $posts[] = $this->loans_m->get_summation_for_invoice($loan->id);
                $amount_paid[$loan->id] = $this->loan_repayments_m->get_loan_total_payments($loan->id);
                $amount_payable_to_date[$loan->id] = $this->loans_m->loan_payable_and_principle_todate($loan->id);
                $projected_profit[$loan->id] = $this->loans_m->get_projected_interest($loan->id,$amount_paid[$loan->id]);
            }
            $this->data['amount_paid'] = $amount_paid;

            $external_lending_post = array();
            $external_lending_amount_paid = array();
            $external_lending_amount_payable_to_date = array();
            $external_lending_projected_profit = array();
            $external_lending_total_loan_out = $this->debtors_m->get_total_loaned_amount();
            $external_lending_total_loan_paid = $this->debtors_m->get_total_loan_paid();
            $external_lending_loans = $this->debtors_m->get_many_by();
            foreach ($external_lending_loans as $loan){
                $external_lending_post[] = $this->debtors_m->get_summation_for_invoice($loan->id);
                $external_lending_amount_paid[$loan->id] = $this->debtors_m->get_loan_total_payments($loan->id);
                $external_lending_amount_payable_to_date[$loan->id] = $this->debtors_m->loan_payable_and_principle_todate($loan->id);
                $external_lending_projected_profit[$loan->id] = $this->debtors_m->get_projected_interest($loan->id,$external_lending_amount_paid[$loan->id]);
            }
            $this->data['external_lending_amount_paid'] = $external_lending_amount_paid;
            $this->data['projected_profit'] = $projected_profit;
            $this->data['external_lending_projected_profit'] = $external_lending_projected_profit;
            $this->data['amount_payable_to_date'] = $amount_payable_to_date;
            $this->data['external_lending_amount_payable_to_date'] = $external_lending_amount_payable_to_date;
            $this->data['members'] = $this->group_member_options;
            $this->data['debtors'] = $this->group_debtor_options;
            $this->data['posts'] = $posts;
            $this->data['external_lending_post'] = $external_lending_post;
            $this->data['group'] = $this->group;
            $this->data['group_currency'] = $this->group_currency;

            $json_file = json_encode($this->data);
            
            if($this->input->get_post('generate_excel')){
                $this->excel_library->generate_loans_summary($json_file);
                print_r($json_file); die();
                $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/loans_summary',$this->group->name.' Loans Summary');
                print_r($response);die;
            }else if($this->input->get_post('generate_pdf')){
                if(preg_match('/local/', $_SERVER['HTTP_HOST'])){
                    $this->data['pdf_true'] = TRUE;
                    $html = $this->load->view('shared/view_loans_summary',$this->data,TRUE);
                    $this->pdf_library->generate_loans_summary($html);
                    die;
                    
                }else{
                    $response = $this->curl_post_data->curl_post_json($json_file,'https://pdf.chamasoft.com/loans_summary',$this->group->name.' Loans Summary');
                    print_r($response);die;
                }
            }
        }
        $this->template->title(translate('Loans Summary'))->build('shared/loans_summary',$this->data);
    }


    public function eazzyclub_loans_summary($generate_pdf=FALSE,$generate_excel=FALSE){
        $member_ids = $this->input->get_post('member_ids')?:0;
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-10 year');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $this->data['member_ids'] = $member_ids;
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        if($this->input->get_post('generate_excel')){

            $filter_parameters = array(
                'member_id' => $member_ids?:'',
                'is_fully_paid' => $this->input->get('is_fully_paid')?:'',
                'from' => $from,
                'to' => $to,
            );

            $this->data['loans'] = $this->loans_m->get_group_loans($filter_parameters,$this->group->id);

            $this->data['loan_amounts_paid_per_loan_array'] = $this->reports_m->get_group_loan_amounts_paid_per_loan_array($this->group->id,$from,$to);

            $this->data['loan_amounts_payable_per_loan_array'] = $this->reports_m->get_group_loan_amounts_payable_per_loan_array($this->group->id,$from,$to);

            $this->data['principal_amounts_paid_per_loan_array'] = $this->reports_m->get_group_principal_amounts_paid_per_loan_array($this->group->id,$from,$to);

            $this->data['interest_amounts_paid_per_loan_array'] = $this->reports_m->get_group_interest_amounts_paid_per_loan_array($this->group->id,$from,$to);

            $this->data['loan_balances_per_loan_array'] = $this->reports_m->get_group_loan_balances_per_loan_array($this->group->id,$from,$to);

            $this->data['group'] = $this->group;

            $this->data['group_member_options'] = $this->group_member_options;

            $json_file = json_encode($this->data);

            $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/reports/eazzyclub_loan_summary',$this->group->name.' Loans Summary');

            print_r($response);

            die;
        }
        $this->template->set_layout('default_full_width.html')->title('Loans Summary')->build('shared/eazzyclub_loans_summary',$this->data);
    }


    function cash_flow_statement(){
        $date_from = strtotime($this->input->get_post('from'));
        $date_to = strtotime($this->input->get_post('to'));
        $this->data['from'] = $date_from;
        $this->data['to'] = $date_to;
        $this->template->title(translate('Cash Flow Statement'))->build('shared/cash_flow_statement',$this->data);
    }


    function balance_sheet_statement($generate_excel=FALSE){
        if($this->input->get('generate_excel') == 1){
            $this->data['current_assets_total'] = 0;
            $this->data['cash_at_bank'] = $this->accounts_m->get_group_total_cash_at_bank();
            $this->data['current_assets_total'] += $this->data['cash_at_bank'];
            $this->data['cash_at_hand'] = $this->accounts_m->get_group_total_cash_at_hand();
            $this->data['current_assets_total'] += $this->data['cash_at_hand'];
            $this->data['total_money_market_investment'] = $this->money_market_investments_m->get_group_total_money_market_investment();
            $this->data['current_assets_total'] += $this->data['total_money_market_investment'];
            $this->data['total_contribution_arrears'] = $this->reports_m->get_group_total_contribution_balance();
            $this->data['current_assets_total'] += $this->data['total_contribution_arrears'];
            $this->data['total_fine_arrears'] = $this->reports_m->get_group_total_fine_balance();
            $this->data['current_assets_total'] += $this->data['total_fine_arrears'];
            $this->data['total_loan_balance'] = $this->loan_invoices_m->get_group_total_loan_balance();
            $this->data['current_assets_total'] += $this->data['total_loan_balance'];

            $this->data['long_term_investments_total'] = 0;
            $this->data['total_stock_purchase_amount'] = $this->withdrawals_m->get_group_stock_purchase_total_amount();
            $this->data['long_term_investments_total'] += $this->data['total_stock_purchase_amount'];

            $this->data['fixed_assets_total'] = 0;
            $this->data['asset_purchase_total'] = $this->withdrawals_m->get_group_asset_purchase_total_amount();
            $this->data['fixed_assets_total'] += $this->data['asset_purchase_total'];

            $this->data['assets_total'] = 0;
            $this->data['assets_total'] += $this->data['fixed_assets_total'];
            $this->data['assets_total'] += $this->data['long_term_investments_total'];
            $this->data['assets_total'] += $this->data['current_assets_total'];

            $this->data['share_holders_equity_total'] = 0;

            $this->data['total_contributions_payable'] = $this->invoices_m->get_group_total_contribution_invoices_amount_payable();
            $this->data['share_holders_equity_total'] += $this->data['total_contributions_payable'];

            if($this->group->disable_arrears){
                $this->data['total_group_contribution_refunds'] = $this->withdrawals_m->get_group_total_contribution_refunds();
                $this->data['share_holders_equity_total'] -= $this->data['total_group_contribution_refunds'];
            }

            if($this->group->disable_arrears){
                $$this->data['total_contribution_overpayments'] = $this->deposits_m->get_group_total_contributions();
                $this->data['share_holders_equity_total'] += $this->data['total_contribution_overpayments'];
            }else{
                $this->data['total_contribution_overpayments'] = $this->reports_m->get_group_total_contribution_overpayment();
                $this->data['share_holders_equity_total'] += $this->data['total_contribution_overpayments'];
            }

            
            if($total_fine_overpayments < 0){
                $total_fines_payable = abs($total_fine_overpayments);
            }else{
                $total_fines_payable = $this->invoices_m->get_group_total_fine_invoices_amount_payable();
            }
            $share_holders_equity_total += $total_fines_payable;


            $this->data['total_fines_payable'] = $total_fines_payable;
            $this->data['share_holders_equity_total'] += $this->data['total_fines_payable'];

            $this->data['net_profit_or_loss'] = 0;
            $this->data['total_miscellaneous_income'] = $this->deposits_m->get_group_member_total_miscellaneous_amount();
            $this->data['net_profit_or_loss'] += $this->data['total_miscellaneous_income'];
            $this->data['total_income'] = $this->deposits_m->get_group_income_total_amounts();
            $this->data['net_profit_or_loss'] += $this->data['total_income'];
            $this->data['total_money_market_interest'] = $this->money_market_investments_m->get_group_total_money_market_interest();
            $this->data['net_profit_or_loss'] += $this->data['total_money_market_interest'];
            $this->data['total_expenses'] = $this->withdrawals_m->get_group_total_expenses();
            $this->data['net_profit_or_loss'] -= $this->data['total_expenses'];
            $this->data['total_loan_interest_and_fines'] = $this->loan_invoices_m->get_group_total_loan_interest_payable();
            $this->data['total_loan_interest_and_fines'] += $this->loan_invoices_m->get_group_total_loan_fines_payable();                    
            $this->data['net_profit_or_loss'] += $this->data['total_loan_interest_and_fines'];
            $this->data['total_asset_sales'] = $this->deposits_m->get_group_total_asset_sale_amount();
            $this->data['net_profit_or_loss'] += $this->data['total_asset_sales'];                          
            $this->data['total_bank_loan_interest'] = $this->bank_loans_m->get_group_bank_loans_interest();
            $this->data['net_profit_or_loss'] -= $this->data['total_bank_loan_interest'];
            $this->data['share_holders_equity_total'] += $this->data['net_profit_or_loss'];

            $this->data['current_liabilities_total'] = 0;

            $this->data['long_term_liabilities_total'] = 0;
            $this->data['total_bank_loans_payable'] = $this->bank_loans_m->get_group_total_bank_loan_payable();
            $this->data['total_bank_loan_repayments'] = $this->withdrawals_m->get_group_total_bank_loan_repayment();
            $this->data['total_bank_loan_balance'] = $this->data['total_bank_loans_payable'] - $this->data['total_bank_loan_repayments'];
            $this->data['long_term_liabilities_total'] += $this->data['total_bank_loan_balance'];

            $this->data['liabilities_total'] = 0;
            $this->data['liabilities_total'] += $this->data['share_holders_equity_total'];
            $this->data['liabilities_total'] += $this->data['current_liabilities_total'];
            $this->data['liabilities_total'] += $this->data['long_term_liabilities_total'];
            $this->data['group'] = $this->group;
            // print_r($this->data); die;
            $json_file = json_encode($this->data);
            $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/reports/balance_sheet_statement',$this->group->name.' Balance Sheet Statement');
                print_r($response);die;
        }
        $this->template->set_layout('default_full_width.html')->title('Balance Sheet Statement')->build('shared/balance_sheet_statement',$this->data);
    }

    // function income_statement($generate_pdf=FALSE){
    //     $date_from = strtotime($this->input->get_post('from'));
    //     $date_to = strtotime($this->input->get_post('to'));
    //     if($this->input->get('set_date')){
    //         if($this->input->get_post('generate_excel')==1){
    //             $this->data['from'] = strtotime($this->input->get_post('from'));
    //             $this->data['to'] = strtotime($this->input->get_post('to'));
    //             $this->data['total_revenue'] = 0;
    //             $this->data['total_miscellaneous_income'] = $this->deposits_m->get_group_member_total_miscellaneous_amount($this->group->id,$this->data['from'],$this->data['to']);
    //             $this->data['total_revenue'] += $this->data['total_miscellaneous_income'];
    //             $this->data['total_income'] = $this->deposits_m->get_group_income_total_amounts($this->group->id,$this->data['from'],$this->data['to']);
    //             $this->data['total_revenue'] += $this->data['total_income'];
    //             $this->data['total_money_market_interest'] = $this->money_market_investments_m->get_group_total_money_market_interest($this->group->id,$this->data['from'],$this->data['to']);
    //             $this->data['total_revenue'] += $this->data['total_money_market_interest'];

    //             $this->data['total_loan_interest_and_fines'] = $this->loan_invoices_m->get_group_total_loan_interest_payable($this->group->id,$this->data['from'],$this->data['to']);
    //             $this->data['$total_loan_processing_income'] = $this->deposits_m->get_group_total_loan_processing_income($this->group->id,$this->data['from'],$this->data['to']);
    //             $this->data['total_revenue'] += $total_loan_processing_income;

    //             $this->data['total_loan_interest_and_fines'] += $this->loan_invoices_m->get_group_total_loan_fines_payable($this->group->id,$this->data['from'],$this->data['to']);
    //             $this->data['total_revenue'] += $this->data['total_loan_interest_and_fines'];

    //             $this->data['total_group_expenses'] = 0;
    //             $this->data['total_expenses'] = $this->withdrawals_m->get_group_total_expenses($this->group->id,$this->data['from'],$this->data['to']);
    //             $this->data['total_group_expenses'] += $this->data['total_expenses'];
    //             $this->data['total_bank_loan_interest'] = $this->bank_loans_m->get_group_bank_loans_interest($this->group->id,$this->data['from'],$this->data['to']);
    //             $this->data['total_group_expenses'] += $this->data['total_bank_loan_interest'];

    //             $this->data['date_to'] = $date_to;
    //             $this->data['date_from'] = $date_from;
    //             $this->data['group'] = $this->group;
    //             $this->data['group_currency'] = $this->group_currency;
    //             $this->data['application_settings'] = $this->application_settings;

    //             $json_file = json_encode($this->data);
    //             // print_r($this->data); die;
    //             $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/reports/income_statement',$this->group->name.' Income Statement Report');
    //             print_r($response);die;
    //         }
    //     }
    //     $this->data['from'] = $date_from;
    //     $this->data['to'] = $date_to;
    //     $this->template->title('Income Statement')->build('shared/income_statement',$this->data);
    // }

    function bank_loans_summary(){

        
        $posts = array();
        if($this->input->get('generate_excel')==1){
            $this->data['total_loan_received_and_repaid'] = $this->bank_loans_m->total_loan_received_and_paid();
            $this->data['posts'] = $this->bank_loans_m->get_group_bank_loans();
            $this->data['group_total_bank_loan_repayments_per_bank_loan_array'] = $this->withdrawals_m->get_group_total_bank_loan_repayments_per_bank_loan_array();
        
            $this->data['group'] = $this->group;
            $this->data['group_currency'] = $this->group_currency;
            $this->data['application_settings'] = $this->application_settings;
            $json_file = json_encode($this->data);
            //print_r($json_file);
            $this->excel_library->generate_bank_loans_summary($json_file);
            print_r($json_file); die();
            $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/bank_loans_summary',$this->group->name.' Bank Loans Summary');
            print_r($response);die;
            die;
        }
        $this->template->title(translate('Bank Loans Summary'))->build('shared/bank_loans_summary',$this->data);
    }


    function contributions_summary(){
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-3 years');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $filter_all_with_arrears = $this->input->get('filter_all_with_arrears')?$this->input->get('filter_all_with_arrears'):0;
        $filter_all_with_arrears = $filter_all_with_arrears==1?1:0;


        $member_ids = $this->input->get('members');
        $this->data['member_ids'] = $member_ids;
        $member_list_ids = '0';
        $count = 1;
        $member_options = $this->members_m->get_active_group_member_with_arears_options(FALSE,FALSE,TRUE,'',FALSE);

        if($member_ids){
            foreach ($member_ids as $member_id) {
                if($member_id){
                    if($count==1){
                        $member_list_ids='"'.$member_id.'"';
                    }else{
                        $member_list_ids.=',"'.$member_id.'"';
                    }
                    $count++;
                }
            }
        }else{
            foreach ($member_options as $member_id => $member_name) {
                if($member_id){
                    if($count==1){
                        $member_list_ids='"'.$member_id.'"';
                    }else{
                        $member_list_ids.=',"'.$member_id.'"';
                    }
                    $count++;
                }
            }
        }
        if($this->input->get_post('generate_excel')==1){
            $this->data['from'] = '';
            $this->data['to'] = '';
            $this->data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
            $this->data['member_total_contributions_paid_per_contribution_array'] = $this->statements_m->get_group_member_total_contribution_paid_per_contribution_per_member_array($this->group->id);

            $this->data['active_group_member_options'] = $this->group_member_detail_options;
            $this->data['group_currency'] = $this->group_currency;
            $this->data['group'] = $this->group;
            $json_file = json_encode($this->data);
            $response = $this->excel_library->contribution_summary($json_file);
            print_r($response);die;
        }
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $this->data['filter_all_with_arrears'] = $filter_all_with_arrears;
        // $this->data['member_list_ids'] = $member_list_ids;
        $this->data['member_options'] = $member_options;
        
        $this->template->title(translate('Contribution Summary'))->build('shared/contributions_summary',$this->data);
    }

    function contributions_minus_expenses_summary($generate_excel=FALSE){
        if($this->input->get_post('generate_excel')==1){
            $this->data['group_member_contribution_totals'] = $this->deposits_m->get_group_member_total_contributions_array();
            $this->data['group_member_contribution_refund_totals'] = $this->withdrawals_m->get_group_member_total_contribution_refunds_array();
            $this->data['group_member_contribution_balance_totals'] = $this->statements_m->get_group_member_total_contribution_balances_array();
            $this->data['group_member_cumulative_contribution_balance_totals'] = $this->statements_m->get_group_member_total_cumulative_contribution_balances_array();
            $this->data['member_total_contribution_transfers_to_fines_array'] = $this->statements_m->get_group_member_total_contribution_transfers_to_fines_array();
            $this->data['member_total_contribution_transfers_from_loans_array'] = $this->statements_m->get_group_member_total_contribution_transfers_from_loans_array();
            $this->data['member_total_contribution_transfers_to_loan_array'] = $this->statements_m->get_group_member_total_contribution_transfers_to_loan_array();
            $total_group_expenses = $this->withdrawals_m->get_group_total_expenses();
            $this->data['total_group_expenses'] = $total_group_expenses;
            $this->data['expense_per_member'] = $total_group_expenses/count($this->active_group_member_options);
            $this->data['active_group_member_options'] = $this->active_group_member_options;
            $this->data['group_currency'] = $this->group_currency;
            $this->data['group'] = $this->group;
            $json_file = json_encode($this->data);
            $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/contributions_summary/less_expenses',$this->group->name.' contributions Summary Minus Expenses');
            print_r($response);
            die;
        }
        $this->template->title('Contribution Summary')->build('shared/contributions_minus_expenses_summary',$this->data);
    }

    function fines_summary($generate_excel=FALSE){
        if($this->input->get('generate_excel')==1){
            $this->data['member_total_contribution_transfers_to_fines_array'] = $this->statements_m->get_group_member_total_contribution_transfers_to_fines_array();
            $this->data['suspended_members_ids_array'] = $this->members_m->get_suspended_members_ids_array();
            $this->data['group_member_fine_totals'] = $this->deposits_m->get_group_member_total_fines_array();
            $this->data['group_member_fine_balance_totals'] = $this->statements_m->get_group_member_total_fine_balances_array();
            $this->data['group_member_options'] = $this->group_member_options;
            $this->data['group_currency'] = $this->group_currency;
            $this->data['group'] = $this->group;
            $json_file = json_encode($this->data);
            //print_r($json_file);die;
            $this->excel_library->generate_fines_summary($json_file);
            print_r($json_file); die();
        }
        $this->template->title(translate('Fines Summary'))->build('shared/fines_summary',$this->data);
    }


    function expenses_summary(){
        if($this->input->get('generate_excel')==1){
            $this->data['expense_category_options'] = $this->expense_categories_m->get_group_expense_category_options();
            $this->data['group_expense_category_totals'] = $this->withdrawals_m->get_group_expense_category_totals_array();
            $this->data['group_currency'] = $this->group_currency;
            $this->data['group'] = $this->group;
            $json_file = json_encode($this->data);
            //print_r($json_file);die;

            $json_file = json_encode($this->data);
            //print_r($json_file);die;
            $this->excel_library->generate_expense_summary($json_file);
            print_r($json_file); die();
        }
        $this->template->title(translate('Expenses Summary'))->build('shared/expenses_summary',$this->data);
    }


    function trial_balance($generate_excel=FALSE){
        if($this->input->get_post('generate_excel')==1){
            $json_file = json_encode($this->data);
            $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/reports/eazzy_club_balance_sheet','EazzyClub Balance Sheet Template');
            print_r($response);die;
        }
        $date_from = strtotime($this->input->get_post('from'));
        $date_to = strtotime($this->input->get_post('to'));
        $this->data['from'] = $date_from;
        $this->data['to'] = $date_to;
        $this->template->title(translate('Trial Balance'))->build('shared/trial_balance',$this->data);
    }

    /**
        function comprehensive_income_statement(){
            
            $date_from = strtotime($this->input->get_post('from'));
            $date_to = strtotime($this->input->get_post('to'));
            if($this->input->get('set_date')){
                if($this->input->get_post('generate_excel')==1){
                    $this->data['from'] = strtotime($this->input->get_post('from'));
                    $this->data['to'] = strtotime($this->input->get_post('to'));
                    $this->data['total_revenue'] = 0;
                    $this->data['total_miscellaneous_income'] = $this->deposits_m->get_group_member_total_miscellaneous_amount($this->group->id,$this->data['from'],$this->data['to']);
                    $this->data['total_revenue'] += $this->data['total_miscellaneous_income'];
                    $this->data['total_income'] = $this->deposits_m->get_group_income_total_amounts($this->group->id,$this->data['from'],$this->data['to']);
                    $this->data['total_revenue'] += $this->data['total_income'];
                    $this->data['total_money_market_interest'] = $this->money_market_investments_m->get_group_total_money_market_interest($this->group->id,$this->data['from'],$this->data['to']);
                    $this->data['total_revenue'] += $this->data['total_money_market_interest'];

                    $this->data['total_loan_interest_and_fines'] = $this->loan_invoices_m->get_group_total_loan_interest_payable($this->group->id,$this->data['from'],$this->data['to']);
                    $this->data['$total_loan_processing_income'] = $this->deposits_m->get_group_total_loan_processing_income($this->group->id,$this->data['from'],$this->data['to']);
                    $this->data['total_revenue'] += $total_loan_processing_income;

                    $this->data['total_loan_interest_and_fines'] += $this->loan_invoices_m->get_group_total_loan_fines_payable($this->group->id,$this->data['from'],$this->data['to']);
                    $this->data['total_revenue'] += $this->data['total_loan_interest_and_fines'];

                    $this->data['total_group_expenses'] = 0;
                    $this->data['total_expenses'] = $this->withdrawals_m->get_group_total_expenses($this->group->id,$this->data['from'],$this->data['to']);
                    $this->data['total_group_expenses'] += $this->data['total_expenses'];
                    $this->data['total_bank_loan_interest'] = $this->bank_loans_m->get_group_bank_loans_interest($this->group->id,$this->data['from'],$this->data['to']);
                    $this->data['total_group_expenses'] += $this->data['total_bank_loan_interest'];

                    $this->data['date_to'] = $date_to;
                    $this->data['date_from'] = $date_from;
                    $this->data['group'] = $this->group;
                    $this->data['group_currency'] = $this->group_currency;
                    $this->data['application_settings'] = $this->application_settings;

                    $json_file = json_encode($this->data);
                    // print_r($this->data); die;
                    $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/reports/income_statement',$this->group->name.' Income Statement Report');
                    print_r($response);die;
                }
            }
            $this->data['from'] = $date_from;
            $this->data['to'] = $date_to;
            $this->template->title('Comprehensive Income Statement')->build('shared/comprehensive_income_statement',$this->data);
        }
    **/

    function loans_guarantor_summary(){

        if($this->input->get_post('generate_excel') == 1){
            $this->data['group'] = $this->group;
            $this->data['group_member_options'] = $this->group_member_options;
            $this->data['group_currency'] = $this->group_currency;
            $this->data['loan_guarantors'] = $this->loans_m->get_group_member_loan_guarantors();
            $this->data['interest_types'] = $this->loan->interest_types;
            $this->data['loan_interest_rate_per'] = $this->loan->loan_interest_rate_per;

            $json_file = json_encode($this->data);

            //print_r($json_file);
            //die;

            $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/reports/loans_guarantor_summary',$this->group->name.' Loan Guarantor Summary');
            print_r($response);
            die;
        }

        $this->template->set_layout('default_full_width.html')->title('Loan Guarantor Summary')->build('shared/loans_guarantor_summary',$this->data);
    }

    function eazzyclub_income_statement(){
        $date_from = strtotime($this->input->get_post('from'));
        $date_to = strtotime($this->input->get_post('to'));
        $this->data['from'] = $date_from;
        $this->data['to'] = $date_to;
        $this->template->set_layout('default_full_width.html')->title('Income Statement')->build('shared/eazzyclub_income_statement',$this->data);
    }

    function income_statement(){
        $date_from = strtotime($this->input->get_post('from'));
        $date_to = strtotime($this->input->get_post('to'));
        $this->data['from'] = $date_from;
        $this->data['to'] = $date_to;
        $this->template->title(translate('Income Statement'))->build('shared/income_statement',$this->data);
    }

    function monthly_income_statement(){
        $date_from = strtotime($this->input->get_post('from'));
        $date_to = strtotime($this->input->get_post('to'));
        $this->data['from'] = $date_from;
        $this->data['to'] = $date_to;
        $this->template->title('Monthly Income Statement')->build('shared/monthly_income_statement',$this->data);
    }

    function eazzyclub_balance_sheet(){
        if($this->input->get_post('generate_excel')==1){
            $json_file = json_encode($this->data);
            $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/reports/eazzy_club_balance_sheet','EazzyClub Balance Sheet Template');
            print_r($response);die;
        }
        $date_from = strtotime($this->input->get_post('from'));
        $date_to = strtotime($this->input->get_post('to'));
        $this->data['from'] = $date_from;
        $this->data['to'] = $date_to;
        $this->template->set_layout('default_full_width.html')->title('Balance Sheet')->build('shared/eazzyclub_balance_sheet',$this->data);
    }

    function monthly_balance_sheet(){
        if($this->input->get_post('generate_excel')==1){
            $json_file = json_encode($this->data);
            $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/reports/eazzy_club_balance_sheet','EazzyClub Balance Sheet Template');
            print_r($response);die;
        }
        $date_from = strtotime($this->input->get_post('from'));
        $date_to = strtotime($this->input->get_post('to'));
        $this->data['from'] = $date_from;
        $this->data['to'] = $date_to;
        $this->template->title('Monthly Balance Sheet')->build('shared/monthly_balance_sheet',$this->data);
    }

    function balance_sheet(){
        $date_from = strtotime($this->input->get_post('from'));
        $date_to = strtotime($this->input->get_post('to'));
        $this->data['from'] = $date_from;
        $this->data['to'] = $date_to;
        if($this->input->get_post('generate_excel')==1){
            $json_file = json_encode($this->data);
            $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/reports/eazzy_club_balance_sheet','EazzyClub Balance Sheet Template');
            print_r($response);die;
        }else if($this->input->get_post('generate_pdf')==1){
            $data['pdf_true'] = TRUE;
            $html = $this->load->view('shared/balance_sheet',$this->data,TRUE);
            $this->pdf_library->generate_landscape_report($html);
            die;
        }
        $this->template->title(translate('Balance Sheet'))->build('shared/balance_sheet',$this->data);
    }

     function monthly_trial_balance($generate_excel=FALSE){
        if($this->input->get_post('generate_excel')==1){
            $json_file = json_encode($this->data);
            $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/reports/eazzy_club_balance_sheet','EazzyClub Balance Sheet Template');
            print_r($response);die;
        }
        $date_from = strtotime($this->input->get_post('from'));
        $date_to = strtotime($this->input->get_post('to'));
        $this->data['from'] = $date_from;
        $this->data['to'] = $date_to;
        $this->template->title('Monthly Trial Balance')->build('shared/monthly_trial_balance',$this->data);
    }

    function test_loans_out(){
        $total_principal_loans_out_per_year_array = $this->loans_m->get_group_total_principal_loans_out_per_year_array($this->group->id);
        print_r($total_principal_loans_out_per_year_array);
    }

    function investment_summary(){
        $date_from = strtotime($this->input->get_post('from'));
        $date_to = strtotime($this->input->get_post('to'));
        $this->data['from'] = $date_from;
        $this->data['to'] = $date_to;
        $this->template->title('Investment Summary')->build('shared/investment_summary',$this->data);
    }

}