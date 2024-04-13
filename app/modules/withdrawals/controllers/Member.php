<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Member extends Member_Controller{

	function __construct(){
        parent::__construct();
        $this->load->model('withdrawals_m');
        $this->load->model('expense_categories/expense_categories_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('assets/assets_m');
        $this->load->model('recipients/recipients_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->library('transactions');
        $this->load->library('loan');
        $this->load->library('excel_library');        
        $this->load->model('paybills/paybills_m');
    }

    function index(){
        $data = array();
        $this->template->title('Withdrawals')->build('sharsed/index',$data);
    }

    function listing(){
        $transaction_alert_id = $this->input->get('transaction_alert');
        $data = array();
        $filter_parameters = array(
            'transaction_alert_id' => $transaction_alert_id
        );
        $total_rows = $this->withdrawals_m->count_group_withdrawals($filter_parameters);
        $pagination = create_pagination('member/withdrawals/listing/pages', $total_rows,50,5,TRUE);
        $data['pagination'] = $pagination;
        $data['asset_options'] = $this->assets_m->get_group_asset_options();
        $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $data['withdrawal_transaction_names'] = $this->transactions->withdrawal_transaction_names;
        $data['expense_category_options'] = $this->expense_categories_m->get_group_expense_category_options();
        $data['posts'] = $this->withdrawals_m->limit($pagination['limit'])->get_group_withdrawals($filter_parameters);
        $this->template->title('List Withdrawals')->build('member/listing',$data);
    }

    function withdrawal_requests(){
        $data = array();
        $this->template->title('Withdrawal Requests')->build('shared/withdrawal_requests',$data);
    } 

    function respond($id = 0){
        $id OR redirect("group/withdrawals/withdrawal_requests");
        $post = $this->withdrawals_m->get_group_member_withdrawal_approval_request_by_member_id($id);
        $post OR redirect("group/withdrawals/withdrawal_requests");
        if($post->is_approved || $post->is_declined){
            $this->session->set_flashdata('info',"You already responded to that withdrawal approval request.");
            redirect("group/withdrawals/withdrawal_requests");
        }
        $this->data['withdrawal_request_transaction_names'] = $this->transactions->withdrawal_request_transaction_names;
        $withdrawal_request = $this->withdrawals_m->get_group_withdrawal_request($post->withdrawal_request_id);
        $this->data['withdrawal_request'] = $withdrawal_request;
        $this->data['post'] = $post;
        $this->data['expense_category_options'] = $this->expense_categories_m->get_group_expense_category_options();
        $this->data['contribution_options'] = $this->contributions_m->get_active_group_contribution_options();
        $this->data['loan_type_options'] = $this->loan_types_m->get_options();
        $this->data['recipient_options'] = $this->recipients_m->get_group_recipient_options($this->group->id,TRUE);
        $this->template->title('Respond to Withdrawal Request')->build('shared/respond',$this->data);
    }

}