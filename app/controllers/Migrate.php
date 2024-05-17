<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Migrate extends Public_Controller{

	protected $registered_users_emails = array();
	protected $registered_users_phones = array();

	function __construct(){
		parent::__construct();
        $this->load->model('migrate_m');
        $this->load->model('safaricom/safaricom_m');
        $this->load->model('withdrawals/withdrawals_m');
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