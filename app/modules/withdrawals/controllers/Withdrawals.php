<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Withdrawals extends Public_Controller{
	
	protected $data = array();

    protected $validation_rules = array();
    
    function __construct(){
        parent::__construct();
        $this->load->model('withdrawals_m');
        $this->load->library('transactions');
    }

    function process_withdrawal_request_disbursement($limit=20){
       echo $this->transactions->process_bulk_withdrawal_disbursement_requests($limit).' requests';
    }

    function test_name_check($account_number=0){
        if($res = $this->curl->equityBankRequests->account_lookup($account_number)){
            print_r($res);
        }else{
            echo $this->session->flashdata('error');
        }
    }

    function test_telco($phone_number=0){
        $reference = $this->transactions->get_withdrawal_request_reference_number()."";
        $amount = 1000;
        $source_account_number = "3001111550220";
        if($res = $this->curl->equityBankRequests->mobile_money_funds_transfer($reference,$phone_number,$amount,$source_account_number)){
            print_r($res);
        }else{
            echo $this->session->flashdata('error');
        }
    }

    function check_expired_requests($limit=1){
        $requests = $this->withdrawals_m->get_undisbursed_approved_withdrawal_requests_past_expiry($limit);
        foreach ($requests as $request) {
            $this->transactions->mark_withdrawal_request_expired($request);
        }
    }

    function generate_access_token_test(){
        if($res = $this->curl->equityBankRequests->client_account_token()){
            print_r($res);
        }else{
            echo $this->session->flashdata('error');
        }
        //print_r($this->curl->equityBankRequests->client_account_token()); die();
    }

}