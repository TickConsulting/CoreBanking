<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends Member_Controller{

    protected $data = array();

    function __construct()
    {
        parent::__construct();
        $this->load->model('accounts/accounts_m');
        $this->load->model('bank_loans_m');
        $this->load->model('members/members_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->library('transactions');
        $this->load->library('loan');
        $this->accounts = $this->accounts_m->get_group_account_options(FALSE,TRUE);
        $this->data['accounts'] = $this->accounts;
        $this->data['members'] = $this->members_m->get_group_member_options();
        $this->data['banks'] = $this->banks_m->get_group_bank_options();
        $this->data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $this->data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $this->data['withdrawal_method_options'] = $this->transactions->withdrawal_method_options;
    }
    
    function index(){
        $this->template->title('Group Bank Loans')->build('member/index');
    }


    function listing(){
        $post = $this->bank_loans_m->get_group_bank_loans();
        $data['posts'] = $post;
        $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
        $this->template->title('Bank Loan List')->build('member/listing',$data);
    }

    function statement($id=0)
    {
        $id OR redirect('group/bank_loans/listing');
        $loan = $this->bank_loans_m->get($id);
        if(empty($loan)){
            $this->session->set_flashdata('error','Sorry, the bank loan is not available');
            redirect('group/bank_loans/listing');
            return FALSE;
        }
        $posts = $this->bank_loans_m->get_all_bank_loan_repayments($id);

        $this->data['posts'] = $posts;
        $this->data['loan'] = $loan;
        $this->accounts = $this->accounts_m->get_group_account_options(FALSE);
        $this->data['accounts'] = $this->accounts;
        $this->template->title('Bank loan statement')->build('shared/statement',$this->data);
    }


    function repayment_listing(){

        $total_rows = $this->bank_loans_m->count_repayments();
        $pagination = create_pagination('group/bank_loans/repayment_listing/pages',$total_rows);
        $posts = $this->bank_loans_m->limit($pagination['limit'])->get_all_bank_loan_payments();

        $this->data['posts'] = $posts;
        $this->accounts = $this->accounts_m->get_group_account_options(FALSE);
        $this->data['accounts'] = $this->accounts;
        $this->data['pagination'] = $pagination;

        $this->template->title('Bank loan repayments list')->build('group/repayment_listing',$this->data);
    }

    function view_repayment($id=0){
        $id OR redirect('group/bank_loans/repayment_listing');

        $post = $this->bank_loans_m->get_bank_loan_repayment($id);
        if(!$post){
            $this->session->set_flashdata('error','The repayment does not exist');
            redirect('group/bank_loans/repayment_listing');
            return FALSE;
        }

        $this->data['post'] = $post;
        $this->accounts = $this->accounts_m->get_group_account_options(FALSE);
        $this->data['accounts'] = $this->accounts;
        $this->template->title('View Repayment')->build('group/view_repayment',$this->data);
    }

}