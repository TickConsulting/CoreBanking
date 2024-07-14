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
        $this->load->library('excel_library');
        $this->load->library('pdf_library');
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
    function aging_loans_report()
    {
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-6 months');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('-3 months');
        // $aging_loan_applications = [];

        // $loans = $this->loans_m->get_group_loans($this->group->id); // 10mins
        $loan_type_options = $this->loan_types_m->get_options();
        // foreach ($loans as $loan) {
        //     if(!$loan->is_fully_paid){
        //         $this->loan_repayments_m->last_loan_repayment_date($loan->id);
        //         $loan->last_loan_repayment_date = $this->loan_repayments_m->last_loan_repayment_date($loan->id);
        //         array_push($aging_loan_applications, $loan);
        //     }
        // }
        // print_r($aging_loan_applications[1]);
        // print_r( $this->loan_repayments_m->last_loan_repayment_date(46));
        
        // die;
        
     

        // $this->data['aging_loan_applications'] = $aging_loan_applications;
        $this->data['loan_type_options'] = $loan_type_options;
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $this->data['additional_member_details'] =  $this->members_m->get_member_additional_fields_data();
        $this->template->title(translate('Loan Arrears Report'))->build('shared/aging_loans_summary',$this->data);
    }
    public function loans_summary_old($generate_pdf=FALSE,$generate_excel=FALSE){
       
        $member_ids = $this->input->get_post('member_ids')?:0;
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-10 year');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $this->data['member_ids'] = $member_ids;
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $this->data['group_member_options'] = $this->members_m->get_group_member_options();
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
            
            $this->data['group_member_options'] = $this->members_m->get_group_members();
          
            $json_file = json_encode($this->data);

            $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/reports/eazzyclub_loan_summary',$this->group->name.' Loans Summary');

            print_r($response);

            die;
        }
        $this->template->set_layout('default_full_width.html')->title('Loans Summary')->build('shared/eazzyclub_loans_summary',$this->data);
    }

    public function loans_summary($generate_pdf=FALSE,$generate_excel=FALSE){
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-10 year');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $member_ids = $this->input->get_post('member_ids')?:0;
        $this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $this->data['member_ids'] = $member_ids;
        $this->data['loan_type_options'] = $this->loan_types_m->get_options();
        $this->data['group_member_options'] = $this->members_m->get_group_member_options();
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
            $this->data['members'] = $this->members_m->get_group_member_options();
            $this->data['group_member_options'] = $this->members_m->get_group_member_options();
            $this->data['debtors'] = $this->debtors_m->get_options();
            $this->data['posts'] = $posts;
            $this->data['external_lending_post'] = $external_lending_post;
            $this->data['group'] = $this->application_settings;
            $this->data['group_currency'] = "KES";

            $json_file = json_encode($this->data);
            
            if($this->input->get_post('generate_excel')){
              
                $this->excel_library->generate_loans_summary($json_file);
                print_r($json_file); die();
                $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/loans_summary',$this->application_settings->application_name.' Loans Summary');
                print_r($response);die;
            }else if($this->input->get_post('generate_pdf')){
                    $this->data['pdf_true'] = TRUE;
                    $html = $this->load->view('shared/view_loans_summary',$this->data,TRUE);
                    $this->pdf_library->generate_loans_summary($html);
                    die;
            }
        }
        $this->template->title(translate('Loans Summary'))->build('shared/loans_summary',$this->data);
    }
    public function loans_in_arrears($generate_pdf=FALSE,$generate_excel=FALSE){
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-10 year');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $member_ids = $this->input->get_post('member_ids')?:0;
        $this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $this->data['member_ids'] = $member_ids;
        $this->data['loan_type_options'] = $this->loan_types_m->get_options();
        $this->data['group_member_options'] = $this->members_m->get_group_member_options();
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
            $this->data['members'] = $this->members_m->get_group_member_options();
            $this->data['group_member_options'] = $this->members_m->get_group_member_options();
            $this->data['debtors'] = $this->debtors_m->get_options();
            $this->data['posts'] = $posts;
            $this->data['external_lending_post'] = $external_lending_post;
            $this->data['group'] = $this->application_settings;
            $this->data['group_currency'] = "KES";

            $json_file = json_encode($this->data);
            
            if($this->input->get_post('generate_excel')){
              
                $this->excel_library->generate_loans_summary($json_file);
                print_r($json_file); die();
                $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/loans_summary',$this->application_settings->application_name.' Loans Summary');
                print_r($response);die;
            }else if($this->input->get_post('generate_pdf')){
                    $this->data['pdf_true'] = TRUE;
                    $html = $this->load->view('shared/view_loans_summary',$this->data,TRUE);
                    $this->pdf_library->generate_loans_summary($html);
                    die;
            }
        }
        $this->template->title(translate('Loans Summary'))->build('shared/loans_in_arrears',$this->data);
    }
}