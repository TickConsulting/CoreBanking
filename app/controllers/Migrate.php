<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Migrate extends Public_Controller{

	protected $registered_users_emails = array();
	protected $registered_users_phones = array();

	function __construct(){
		parent::__construct();
        $this->load->model('migrate_m');
        $this->load->model('safaricom/safaricom_m');
        $this->load->model('withdrawals/withdrawals_m');
        $this->load->model('loan_repayments/loan_repayments_m');
        $this->load->model('loan_invoices/loan_invoices_m');
        $this->load->model('members/members_m');
    }

    function partitiondb(){
    	$this->migrate_m->partitiondb();
    }

    function get_all_transactions_pending_disbursement($limit=1){
        $configuration=$this->safaricom_m->get_default_configuration();
        $transactions=$this->withdrawals_m->get_approved_withdrawal_requests_pending_disbursement($limit);
        foreach($transactions as $transaction){
            $transaction->withdrawal_for=  "Loan Disbursement";
            $member_id = str_replace('member-', '', $transaction->recipient_id);
            $member = $this->members_m->get_group_member($member_id,$transaction->group_id);
            //change this when going live
            $transaction->recipient=$member->phone;

             //change to this  when on test environment
            // $transaction->recipient="254728762287";
        }
        $response=array();
        if($configuration && $transactions){
            $response= array(
                "statusCode"=>1,
                "configuration"=>$configuration,
                "transactions"=>$transactions
            );
        }
        else{
            $response= array(
                "statusCode"=>0,
                "statusMessage"=>"No transactions found",
                "transactions"=>$transactions
            );  
        }
       
        echo (json_encode($response));
    }
    function calculate_loan_balance($loan_id=0){
        $amount_payable=($this->loan_invoices_m->get_total_installment_loan_payable($loan_id));
        $amount_paid=($this->loan_repayments_m->get_loan_total_payments($loan_id));
        $balance= ($amount_payable-$amount_paid);
        $balance=isset($balance) && $balance>=0?$balance:0;
        return number_to_currency($balance);
    }
    function get_all_loans_in_arrears($limit=1){
        $configuration=$this->safaricom_m->get_default_configuration();
        $filter_parameters = array(
            'is_fully_paid' =>0
        );
        $posts = $this->loans_m->get_group_loans($filter_parameters);
    
        foreach($posts as $loan){
            $loan->balance= $this->calculate_loan_balance($loan->id);
            $member = $this->members_m->get_group_member($loan->member_id,$loan->group_id);
            //change this when going live
            $loan->recipient=$member->phone;

             //change to this  when on test environment
            // $transaction->recipient="254728762287";
        }
        $response=array();
        if($configuration && $posts){
            $response= array(
                "statusCode"=>1,
                "configuration"=>$configuration,
                "transactions"=>$posts
            );
        }
        else{
            $response= array(
                "statusCode"=>0,
                "statusMessage"=>"No transactions found",
                "transactions"=>$transactions
            );  
        }
       
        echo (json_encode($response));
    }
    function translate(){
    	$str = $this->input->post('str');
    	echo translate($str);
    }
    function phpInfo(){
        print_r(phpinfo());
        die;
    }

    function handle_localhost_requests(){
        
    }
}