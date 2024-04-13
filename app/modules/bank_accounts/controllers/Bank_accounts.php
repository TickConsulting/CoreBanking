<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bank_accounts extends Public_Controller{

	function __construct(){
        parent::__construct();
        $this->load->library('bank');
        $this->load->model('bank_accounts/bank_accounts_m');
    }


    function create_otp(){
        if(preg_match('/173\.255\.205\.7/',$_SERVER['REMOTE_ADDR'])||preg_match('/45\.33\.18\.205/',$_SERVER['REMOTE_ADDR'])||preg_match('/23\.239\.27\.43/',$_SERVER['REMOTE_ADDR'])||preg_match('/45\.56\.79\.118/',$_SERVER['REMOTE_ADDR']) || preg_match('/197\.232\.248\.54/', $_SERVER['REMOTE_ADDR'])|| preg_match('/104\.237\.137\.190/', $_SERVER['REMOTE_ADDR'])){
            $account_number = $this->input->post('account_number');
            $phone_number = $this->input->post('phone_number');
            $calling_code = $this->input->post('calling_code');
            $country_code = $this->input->post('country_code');
            $create_otp_url = $this->input->post('create_otp_url');
            if($account_number&&$phone_number&&$calling_code&&$country_code&&$create_otp_url){
                $response = $this->bank->request_one_time_password(trim($account_number),$phone_number,$calling_code,$country_code,$create_otp_url);
                print_r($response);
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    function verify_otp(){
        if(preg_match('/173\.255\.205\.7/',$_SERVER['REMOTE_ADDR'])||preg_match('/45\.33\.18\.205/',$_SERVER['REMOTE_ADDR'])||preg_match('/23\.239\.27\.43/',$_SERVER['REMOTE_ADDR'])||preg_match('/45\.56\.79\.118/',$_SERVER['REMOTE_ADDR']) || preg_match('/197\.237\.131\.21/', $_SERVER['REMOTE_ADDR']) || preg_match('/104\.237\.137\.190/', $_SERVER['REMOTE_ADDR']) ){
            $account_number = $this->input->post('account_number');
            $phone_number = $this->input->post('phone_number');
            $verification_code = $this->input->post('verification_code');
            $calling_code = $this->input->post('calling_code');
            $country_code = $this->input->post('country_code');
            $verify_otp_url = $this->input->post('verify_otp_url');
            if($account_number&&$phone_number&&$verification_code&&$calling_code&&$country_code&&$verify_otp_url){
                $response = $this->bank->verify_one_time_password(trim($account_number),$phone_number,trim($verification_code),$calling_code,$country_code,$verify_otp_url);
                print_r($response);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    function verify_account($account_number = 0, $group_id ='5373'){
        if($account_number && $group_id){
            $id = $this->bank_accounts_m->get_group_bank_account_id_by_account_number($account_number,$group_id);
            if($id){
                $this->bank_accounts_m->update($id,array('is_verified'=>1,'modified_on'=>time()));
                echo 'done';
            }else{
                echo 'Empty';
            }
        }else{
            echo 'Failed';
        }
    }

    function update_balance($id = 0,$balance = 0){
        $this->bank_accounts_m->update($id,array(
            'current_balance' => $balance
        ));
    }

    function get_account_balance(){
        $account_number = '3015111203906';
        if($balance = $this->curl->equityBankRequests->get_account_balance($account_number,55)){
            echo $balance;
        }else{
            echo $this->session->flashdata('error');
        }
    }

    function telco_lookup(){
        $phone_number = '254728747061';
        if($details = $this->curl->equityBankRequests->telco_lookup($phone_number,"Mpesa")){
            print_r($details);
        }else{
            echo $this->session->flashdata('error');
        }
    }

    function fix_banks_country_id(){
        $country = $this->countries_m->get_country_by_calling_code(254);

        $banks = $this->banks_m->get_all();
        foreach ($banks as $bank) {
            $update = array(
                'country_id' => $country->id,
                'modified_on' => time(),
            );
            $this->banks_m->update($bank->id,$update);
        }
    }

    function initiate_account_linkage($account_number=0,$phone_number=0){
        $res = $this->curl->equityBankRequests->initiate_account_linkage($account_number,$phone_number);
        if($res){
            print_r($res);
        }else{
            echo $this->session->flashdata('error');
        }
    }

    function account_linkage_send_otp($account_number=0,$recipient_key=0){
        $res = $this->curl->equityBankRequests->account_linkage_send_otp($account_number,$recipient_key);
        if($res){
            print_r($res);
        }else{
            echo $this->session->flashdata('error');
        }
    }

    function generate_otp($phone_number=0){
        $json_file = json_encode(array(
            "reference" => time().''.rand(100,999)."",
            "to" => $phone_number,
            "platform" => 1,
            "operation" => "onboarding",
            "source" => "chatbot",
            "noOfDigit" => 6,
            "customerId" => "0170194290581",
            "signature" => "jhldhsfkdfdsklf"
        ));
        $res = $this->curl->equityBankRequests->generate_otp($json_file);
        if($res){
            print_r($res);
        }else{
            echo $this->session->flashdata('error');
        }
    }

    function send_otp($phone_number=0){
        $otp_array = array(
            'pin'=>'1234',
            'phone'=>$phone_number?:'254728747061',
            'email'=>'githaiga.geoffrey@gmail.com',
            'first_name'=>ucwords('geoffrey'),
            'last_name'=>ucwords('githaiga'),
            'user_id' => 1,
            'language_id' => 1,
            'expiry_time' => strtotime("+10 minutes",time()),
        );
        $res = $this->messaging->send_user_otp($otp_array);
        if($res){
            print_r($res);
        }else{
            echo $this->session->flashdata('error');
        }
    }

    function account_linkage_status($account_number=0,$countryCode='KE'){
        $res = $this->curl->equityBankRequests->account_linkage_status($account_number,$countryCode);
        if($res){
            print_r($res);
        }else{
            echo $this->session->flashdata('error');
        }
    }

    function disconnect_all_accounts(){
        $bank_accounts = $this->bank_accounts_m->get_bank_accounts();

        foreach($bank_accounts as $account){
            $update = array(
                'is_linked' => 0,
                // 'is_verified' => 0,
            );
            $this->bank_accounts_m->update($account->id,$update);
        }
        echo 'done';
    }

    function get_bank_accounts_without_bank_account_currency_id(){
        $results = $this->bank_accounts_m->get_bank_accounts_without_account_currency_id();
        $count = count($results);
        print_r("Total: ".$count);
        echo "<br>";
        print_r(json_encode($results));
        die;
    }

    function account_lookup(){
        $account_number = $this->input->get("account_number");
        $bank_id = $this->input->get("bank_id");

        if(preg_match("/eazzychamademo\.com/",$_SERVER['HTTP_HOST'])){
            echo $this->curl->equityBankRequests->account_lookup($account_number,$bank_id);
        }
    }
}