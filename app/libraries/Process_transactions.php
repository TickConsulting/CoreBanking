<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Process_transactions{

	protected $ci;

	public function __construct(){
		$this->ci= & get_instance();
		set_time_limit(0);
        ini_set('memory_limit','2048M');
        ini_set('max_execution_time', 1200);
		$this->ci->load->model('transactions/transactions_m');
		$this->ci->load->model('safaricom/safaricom_m');
        $this->ci->load->model('banks/banks_m');
        // $this->ci->load->config('transactions');
	}


    function initiate_transaction_payment($amount=0,$phone_number=0,$callback_url='',$account=array(),$reference_number=0,$charge=0,$channel=0,$currency =''){
        $amount = currency($amount);
        $url = "https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest";
        $shortcode = "546448";
        if(array_key_exists($shortcode, $this->ci->config->item('paybills'))){
            $paybills = $this->ci->config->item('paybills');
            $paybills_data = $paybills[$shortcode];
            $timestamp = $paybills_data['timestamp'];
            $passkey = $paybills_data['passkey'];
            $base_password = base64_encode($shortcode.$passkey.$timestamp);
            $request_id = $this->ci->safaricom_m->generate_stkpush_request_id();
            $input = array(
                'shortcode' => $shortcode,
                'request_id' => $request_id,
                'amount' => $amount,
                'charge' => $charge,
                'phone' => $phone_number,
                'request_callback_url' => $callback_url,
                'account_id' => $account->id,
                'reference_number' => $reference_number,
                'created_on' => time(),
            );
            if($id = $this->ci->safaricom_m->insert_stk_push_request($input)){
                $phone_number = str_replace("+","",valid_phone($phone_number));
                $post_data = json_encode(array(
                    "BusinessShortCode" => $shortcode,
                    "Password" => $base_password,
                    "Timestamp" => $timestamp,
                    "TransactionType" => "CustomerPayBillOnline",
                    "Amount" => ($amount+$charge),
                    "PartyA" => $phone_number,
                    "PartyB" => $shortcode,
                    "PhoneNumber" => $phone_number,
                    "CallBackURL" => "https://chamasoft.com:443/transaction_alerts/daraja_stk_payment_callback",
                    "AccountReference" =>  $request_id,
                    "TransactionDesc" => "online payment"
                ));
                $response = $this->ci->curl->darajaRequests->process_request($post_data,$url,$shortcode);
                if($response){
                    if($res = json_decode($response)){
                        $checkout_request_id = isset($res->CheckoutRequestID)?$res->CheckoutRequestID:'';
                        $merchant_request_id = isset($res->MerchantRequestID)?$res->MerchantRequestID:'';
                        $response_code = isset($res->ResponseCode)?$res->ResponseCode:'';
                        $response_description = isset($res->ResponseDescription)?$res->ResponseDescription:'';
                        $customer_message = isset($res->CustomerMessage)?$res->CustomerMessage:'';
                        $error_code =  isset($res->errorCode)?$res->errorCode:'';
                        $error_message =  isset($res->errorMessage)?$res->errorMessage:'';
                        if($error_code){
                            $this->ci->session->set_flashdata('error',$error_message);
                            return FALSE;
                        }else{
                            if($response_description || $error_message){
                                $update = array(
                                    'response_code' => $response_code,
                                    'response_description' => $response_description,
                                    'checkout_request_id' => $checkout_request_id,
                                    'merchant_request_id' => $merchant_request_id,
                                    'customer_message' => $customer_message,
                                    'modified_on' => time(),
                                );
                                if($this->ci->safaricom_m->update_stkpushrequest($id,$update)){
                                    $payment_input = array(
                                        'account_id' => $account->id,
                                        'reference_number' => $reference_number,
                                        'phone_number' => $phone_number,
                                        'amount' => currency($amount),
                                        'type' => 1,
                                        'channel' => $channel,
                                        'status' => ($response_code == '0')?1:2,
                                        'active' => 1,
                                        'response_code' => $response_code,
                                        'response_description' => $response_description,
                                        'transaction_date' => time(),
                                        'shortcode' => $shortcode,
                                        'merchant_request_id' => $merchant_request_id,
                                        'transaction_id' => '',
                                        'narration' => '',
                                        'checkout_request_id' => $checkout_request_id,
                                    );
                                    $this->ci->transactions_m->insert_payment($payment_input);
                                    return $this->ci->safaricom_m->get_stk_request($id);
                                }else{
                                    $this->ci->session->set_flashdata('error',"Error occured receiving response. Try again later");
                                    return FALSE;
                                }
                            }else{
                                $this->ci->session->set_flashdata('error',"Could not make payment at the moment. Error occured. Try again later.");
                                return FALSE;
                            }
                        }
                    }else{
                        $this->ci->session->set_flashdata('error',"invalid response received. Try again later");
                        return FALSE;
                    }
                }else{
                    $this->ci->session->flashdata('error');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Transaction request failed. Try again');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Paybill error. Try again later');
            return FALSE;
        }
    }

    function get_transaction_status($reference_number=0,$amount=0,$account_id=0,$payment_transaction=array()){
        if($payment_transaction){
            if($payment_transaction->amount == $amount){
                if($payment_transaction->status == 1){//go online and check payment status
                    $shortcode = $payment_transaction->shortcode;
                    $paybills = $this->ci->config->item('paybills');
                    if(array_key_exists($shortcode, $paybills)){
                        $paybills_data = $paybills[$shortcode];
                        $timestamp = $paybills_data['timestamp'];
                        $passkey = $paybills_data['passkey'];
                        $base_password = base64_encode($shortcode.$passkey.$timestamp);
                        $post_data = json_encode(array(
                            "BusinessShortCode" => $shortcode,
                            "Password" => $base_password,
                            "Timestamp" => $timestamp,
                            "CheckoutRequestID" => $payment_transaction->checkout_request_id,
                        ));
                        $status_query = $this->ci->curl->darajaRequests->query_stk_payment_status($post_data,$shortcode);
                        if($status_query){
                            if($response = json_decode($status_query)){
                                $error_code = isset($response->errorCode)?$response->errorCode:'';
                                $response_code = isset($response->ResponseCode)?$response->ResponseCode:'';
                                if($error_code){
                                    $error_message = isset($response->errorMessage)?$response->errorMessage:'';
                                    return array(
                                        "code" => "API036",
                                        "description" => $error_message,
                                        'data' => array(
                                            'reference_number' => $payment_transaction->reference_number,
                                            'account_number' => $this->ci->accounts_m->get_account_number($payment_transaction->account_id),
                                            'phone' => $payment_transaction->phone_number,
                                            'transaction_date' => $payment_transaction->transaction_date,
                                        )
                                    );
                                }else{
                                    $response_description = isset($response->ResponseDescription)?$response->ResponseDescription:'';
                                    $result_code = isset($response->ResultCode)?$response->ResultCode:'';
                                    $result_description = isset($response->ResultDesc)?$response->ResultDesc:'';
                                    $checkout_request_id = isset($response->CheckoutRequestID)?$response->CheckoutRequestID:'';
                                    $merchant_request_id = isset($response->MerchantRequestID)?$response->MerchantRequestID:'';
                                    $transaction_id = isset($response->transaction_id)?$response->transaction_id:'';
                                    $transaction_date = strtotime($payment_transaction->transaction_date)?:time();

                                    $request = $this->ci->safaricom_m->get_stk_request_by_merchant_request_id_and_checkout_request_id($checkout_request_id,$merchant_request_id);
                                    if($request){
                                        if($transaction_id){
                                            $organization_balance = 0;
                                        }else{
                                            $c2b_payment = $this->ci->safaricom_m->get_c2b_payment_by_account($request->request_id);
                                            $transaction_id = $c2b_payment->transaction_id;
                                            $organization_balance = $c2b_payment->organization_balance;
                                        }
                                        $update = array(
                                            'result_code' => $result_code,
                                            'result_description' => $result_description,
                                            'transaction_id' => $transaction_id,
                                            'organization_balance' => $organization_balance,
                                            'transaction_date' => $transaction_date,
                                            'modified_on' => time(),
                                        );
                                        if($this->ci->safaricom_m->update_stkpushrequest($request->id,$update)){
                                            $update = array(
                                                'result_code' => $result_code,
                                                'result_description' => $result_description,
                                                'merchant_request_id' => $merchant_request_id,
                                                'checkout_request_id' => $checkout_request_id,
                                                'transaction_id' => $transaction_id,
                                                'status' => ($result_code=='0')?4:3,
                                                'transaction_date' => $transaction_date,
                                            );
                                            if($payment_transaction = $this->ci->transactions_m->get_payment_transaction($request->reference_number,$request->account_id)){
                                                 $this->ci->transactions_m->update_payment($payment_transaction->id,$update);
                                            }else{

                                            }
                                            $request = $this->ci->safaricom_m->get_stk_request($request->id);
                                            if($result_code == '0'){
                                                if($this->record_transaction($request)){
                                                    
                                                }else{
                                                    
                                                }
                                            }
                                            $this->send_customer_callback($request);
                                            return $this->get_transaction_status($reference_number,$amount,$request->account_id);
                                        }else{

                                        }
                                    }else{

                                    }
                                }
                            }else{
                                return array(
                                    "code" => "API035",
                                    "description" => 'invalid response received. Try again later',
                                );
                            }
                        }else{
                            return array(
                                "code" => "API034",
                                "description" => 'Response from server: '.$this->ci->session->flashdata(),
                            );
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Paybill error. Try again later');
                        return FALSE;
                    }
                }elseif($payment_transaction->status == 2 || $payment_transaction->status == 3){
                    return array(
                        "code" => "API037",
                        'description' => $this->payment_status[$payment_transaction->status],
                        'data' => array(
                            'reference_number' => $payment_transaction->reference_number,
                            'response_description' => $payment_transaction->response_description,
                            'result_description' => $payment_transaction->result_description,
                            'account_number' => $this->ci->accounts_m->get_account_number($payment_transaction->account_id),
                            'phone' => $payment_transaction->phone_number,
                            'transaction_date' => $payment_transaction->transaction_date,
                        )
                    );
                }elseif($payment_transaction->status ==4){
                    return array(
                        'code' => 200,
                        'description' => 'Successful',
                        'data' => array(
                            'reference_number' => $payment_transaction->reference_number,
                            'transaction_id' => $payment_transaction->transaction_id,
                            'narration' => $payment_transaction->narration,
                            'channel' => $this->payment_channels[$payment_transaction->channel],
                            'amount' => number_to_currency($payment_transaction->amount),
                            'result_description' => $payment_transaction->result_description,
                            'account_number' => $this->ci->accounts_m->get_account_number($payment_transaction->account_id),
                            'phone' => $payment_transaction->phone_number,
                            'transaction_date' => $payment_transaction->transaction_date,
                        )
                    );
                }
            }else{
                return array(
                    "code" => "API033",
                    "description" => 'Amount submitted not equal to transaction amount',
                );
            }
        }else{
            $this->ci->session->set_flashdata('error','Payment transaction could not be found');
            return FALSE;
        }
    }

    function reverse_transaction($payment_transaction=array(),$amount=0,$use_post=0){
        if($payment_transaction){
            if($payment_transaction->amount == $amount){
                if($payment_transaction->status == 4 || ($use_post==1 && $payment_transaction->status == 1)){
                    $shortcode = $payment_transaction->shortcode;
                    $paybills = $this->ci->config->item('paybills');
                    if(array_key_exists($shortcode, $paybills)){
                        $paybills_data = $paybills[$shortcode];
                        $username = $paybills_data['username'];
                        $initiator_password = $paybills_data['initiator_password'];
                        $encypted_initiator_password = openssl_key_encrypt($initiator_password,FALSE,TRUE);
                        $post_data = json_encode(array(
                            "Initiator" => $username,
                            "SecurityCredential" => $encypted_initiator_password,
                            "CommandID" => "TransactionReversal",
                            "TransactionID" =>  $payment_transaction->transaction_id,
                            "Amount" => round($payment_transaction->amount),
                            "ReceiverParty" => $shortcode,
                            "RecieverIdentifierType" => "11",
                            "ResultURL" =>  "https://chamasoft.com:443/transaction_alerts/daraja_funds_reversal_callback",
                            "QueueTimeOutURL" =>  "https://chamasoft.com:443/transaction_alerts/daraja_funds_reversal_callback",
                            "Remarks" =>  "Reverse transaction",
                            "Occasion" => ""
                        ));
                        $url = 'https://api.safaricom.co.ke/mpesa/reversal/v1/request';
                        if($status_query = $this->ci->curl->darajaRequests->process_request($post_data,$url,$shortcode)){
                            if($res = json_decode($status_query)){
                                $error_code = isset($res->errorCode)?$res->errorCode:'';
                                $error_message = isset($res->errorMessage)?$res->errorMessage:'';
                                $response_code = isset($res->ResponseCode)?$res->ResponseCode:'';
                                $response_description = isset($res->ResponseDescription)?$res->ResponseDescription:'';
                                print_r(json_decode($post_data));
                                print_r($res);
                                if($response_code=='0'){
                                    return $response = array(
                                        "code" => "200",
                                        "description" => 'Reversal request successful: Kindly wait for notification',
                                    );
                                }else{
                                    $this->ci->session->set_flashdata('error','Reversal failed: '.$error_message);
                                    return FALSE;
                                }
                            }else{
                                $this->ci->session->set_flashdata('error','Reversal failed. Invalid response from server');
                                return FALSE;
                            }
                        }else{
                            $this->ci->session->set_flashdata('error','Reversal failed. Try again later');
                            return FALSE;
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Paybill error. Try again later');
                        return FALSE;
                    }
                }else{
                    return array(
                        "code" => "API045",
                        "description" => 'Transaction can not be reversed',
                    );
                }
            }else{
                return array(
                    "code" => "API044",
                    "description" => 'Amount submitted not equal to transaction amount',
                );
            }
        }else{
            $this->ci->session->set_flashdata('error','Payment transaction could not be found');
            return FALSE;
        }
    }

    function disburse_funds($amount=0,$phone_number=0,$account=array(),$reference_number=0,$full_name='',$remarks='',$channel=1,$request_callback_url='',$disburse_charge=0,$currency=''){
        $initiator_password='';
        $shortcode='';
        $user_name='';
        if(preg_match('/54\.93\.184\.124/', $_SERVER['HTTP_HOST']) || preg_match('/local/', $_SERVER['HTTP_HOST'])){
            $shortcode = '600996';
            $user_name='testapi';
            $initiator_password='Safaricom999!*!';
            $url = 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest';
        }else{
            $shortcode = '600996';
            $user_name='testapi';
            $initiator_password='Safaricom999!*!';
            $url = 'https://api.safaricom.co.ke/mpesa/b2c/v1/paymentrequest';
        }
        if($shortcode && $user_name && $initiator_password && $url){
            // $result_url = 'https://tickconsulting.co.ke//transaction_alerts/daraja_funds_disbursement_callback';
            $result_url = 'https://tickconsulting.co.ke/daraja_funds_disbursement_callback.php';
            $encypted_initiator_password = openssl_key_encrypt($initiator_password,FALSE,TRUE);
            $amount = currency($amount);
            $phone_number = str_replace("+", "", valid_phone($phone_number));
            $command_id = "BusinessPayment";
            $post_data = json_encode(array(
                "InitiatorName" => $username,
                "SecurityCredential" => $encypted_initiator_password,
                "CommandID" => $command_id,
                "Amount" =>  $amount,
                "PartyA" => $shortcode,
                "PartyB"  => $phone_number,
                "Remarks" =>  $remarks,
                "QueueTimeOutURL" => $result_url,
                "ResultURL" => $result_url,
                "Occassion" =>  ""
            ));
            //$url = 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest';
            if($response = $this->ci->curl->darajaRequests->process_request($post_data,$url,$shortcode)){
                if($res = json_decode($response)){
                    $error_code = isset($res->errorCode)?$res->errorCode:'';
                    $error_message = isset($res->errorMessage)?$res->errorMessage:'';
                    $response_code = isset($res->ResponseCode)?$res->ResponseCode:'';
                    $response_description = isset($res->ResponseDescription)?$res->ResponseDescription:'';
                    $OriginatorConversationID = isset($res->OriginatorConversationID)?$res->OriginatorConversationID:'';
                    $ConversationID = isset($res->ConversationID)?$res->ConversationID:'';
                    if($response_description || $error_message){
                        $data = array(
                            'paybill'           =>  $shortcode,
                            'amount'            =>  $amount,
                            'request_status'    =>  1,
                            'result_code'      =>   $response_description?$response_code:$error_code,
                            'phone'             =>  $phone_number,
                            'originator_conversation_id' => $OriginatorConversationID,
                            'created_on'        =>  time(),
                            'request_time'      =>  date('YmdHis',time()),
                            'callback_url'      =>  $request_callback_url,
                            'result_description'=>  $response_description?:$error_message,
                            'conversation_id'   =>  $ConversationID,
                            'user_id'           =>  1,
                            'test_environment' => 'Production',
                            'command_id' => $command_id,
                            'account_id' => $account->id,
                            'reference_number' => $reference_number,
                            'disburse_charge' => $disburse_charge,
                        );
                        if($req_id = $this->ci->safaricom_m->insert_b2c($data)){
                            $payment_input = array(
                                'account_id' => $account->id,
                                'reference_number' => $reference_number,
                                'phone_number' => $phone_number,
                                'amount' => currency($amount),
                                'type' => 3,
                                'channel' => 1,
                                'status' => ($response_code == '0')?1:2,
                                'active' => 1,
                                'response_code' => $response_code,
                                'response_description' => $response_description,
                                'transaction_date' => time(),
                                'shortcode' => $shortcode,
                                'merchant_request_id' => $OriginatorConversationID,//merchant_request_id
                                'checkout_request_id' => $ConversationID,
                                'disburse_charge' => $disburse_charge,
                            );
                            $this->ci->transactions_m->insert_payment($payment_input);
                            if($error_code){
                                clear_transaction($account->account_number);
                                return array(
                                    'code' => 'API063',
                                    'description' => $error_message,
                                );
                            }else{
                                return array(
                                    'code' => 200,
                                    'description' => $response_description,
                                );
                            }
                        }else{
                            $this->ci->session->set_flashdata('error','Could not complete transaction at the moment. Try again later');
                            return FALSE;
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','This transaction could not be completed at the moment. Try again later.');
                        return FALSE;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Could not receive response from server. Try again later');
                    return FALSE;
                }
            }else{
                $this->ci->session->flashdata('error');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Invalid paybill and paybill data');
            return FALSE;
        }
    }


    function initiate_mtm_disbursement_request($amount=0,$currency='UGX',$reference_number='',$phone_number=0,$remarks='',$request_callback_url='',$account=array(),$disburse_charge=0){
        if($amount&&$currency&&$reference_number&&$phone_number&&$account){
            $post_data = json_encode(array(
                "amount" => $amount,
                "currency" => $currency,
                "externalId" => $reference_number,
                "payee" => array(
                    "partyIdType" =>"MSISDN",
                    "partyId" => $phone_number
                ),
                "payerMessage" => "Chamasoft Disbursement",
                "payeeNote" => $remarks,
            ));
            $request_id = gen_uuid();
            $callback_url = site_url('mtn/process_disbursment_callback');
            $response_code = '100';
            $response_description = '';
            $currency_id = $this->ci->countries_m->get_currency_by_currency_code($currency);
            if($response = $this->ci->curl->mtnRequests->process_disbursment($post_data,$request_id,$callback_url)){
                $response_code = '0';
                $response_description = 'Request Successful';
            }else{
                $this->ci->session->flashdata('error');
                $response_description = $this->ci->session->flashdata('error');
            }
            $data = array(
                'amount'            =>  currency($amount),
                'request_status'    =>  1,
                'result_code'      =>   $response_code,
                'phone'             =>  $phone_number,
                'created_on'        =>  time(),
                'request_time'      =>  date('YmdHis',time()),
                'callback_url'      =>  $request_callback_url,
                'originator_conversation_id' => $request_id,
                'result_description'=>  $response_description,
                'account_id' => $account->id,
                'reference_number' => $reference_number,
                'disburse_charge' => $disburse_charge,
                'currency' => $currency,
                'currency_id' => $currency_id,
                'remarks' => $remarks,
            );
            if($req_id = $this->ci->mtn_m->insert_disbursement($data)){
                $payment_input = array(
                    'account_id' => $account->id,
                    'reference_number' => $reference_number,
                    'phone_number' => $phone_number,
                    'amount' => currency($amount),
                    'type' => 3,
                    'channel' => 3,
                    'status' => ($response_code == '0')?1:2,
                    'active' => 1,
                    'response_code' => $response_code,
                    'response_description' => $response_description,
                    'transaction_date' => time(),
                    'merchant_request_id' => $request_id,//merchant_request_id
                    'disburse_charge' => $disburse_charge,
                );
                $this->ci->transactions_m->insert_payment($payment_input);
                if($response_code == '0'){
                    return array(
                        'code' => 200,
                        'description' => $response_description,
                    );
                }else{
                    clear_transaction($account->account_number);
                    return array(
                        'code' => 'API063',
                        'description' => $response_description,
                    );
                }
            }else{
                $this->ci->session->set_flashdata('error','Could not complete transaction at the moment. Try again later');
                return FALSE;
            }

            return array(
                'code' => 200,
                'description' => 'Successful',
            );

        }else{
            $this->ci->session->set_flashdata('error','Phone number, currency or amount is missing');
        }
    }

    function organization_fund_settlement($amount=0,$account_reference=0,$source=0,$destination=0,$remarks='',$account=array()){
        $source_paybill = $source?:'546448';
        $paybills = $this->ci->config->item('paybills');
        if(array_key_exists($source_paybill, $paybills)){
            $paybills_data = $paybills[$source_paybill];
            $result_url = 'https://chamasoft.com:443/transaction_alerts/daraja_business_to_business_transfer_callback';
            $username = $paybills_data['username'];
            $initiator_password = $paybills_data['initiator_password'];
            $encypted_initiator_password = openssl_key_encrypt($initiator_password,FALSE,TRUE);
            $command_id = 'BusinessToBusinessTransfer';
            $destination_paybill = $destination?:'546866';
            $request_time = date('YmdHis',time());
            $post_data = json_encode(array(
                'Initiator' => $username,
                'SecurityCredential' => $encypted_initiator_password,
                'CommandID' => $command_id,
                'SenderIdentifierType' => '4',
                'RecieverIdentifierType' => '4',
                'Amount' => ($amount),
                'PartyA' => $source_paybill,
                'PartyB' => $destination_paybill,
                'AccountReference' => $account_reference,
                'BillRefNumber' => $account_reference,
                'Remarks' => $remarks,
                'QueueTimeOutURL' => $result_url,
                'ResultURL' => $result_url,
            ));
            $url = 'https://api.safaricom.co.ke/mpesa/b2b/v1/paymentrequest';
            if($response = $this->ci->curl->darajaRequests->process_request($post_data,$url,$source_paybill)){
                if($res = json_decode($response)){
                    $error_code = isset($res->errorCode)?$res->errorCode:'';
                    $error_message = isset($res->errorMessage)?$res->errorMessage:'';
                    $response_code = isset($res->ResponseCode)?$res->ResponseCode:'';
                    $response_description = isset($res->ResponseDescription)?$res->ResponseDescription:'';
                    $originator_conversation_id = isset($res->OriginatorConversationID)?$res->OriginatorConversationID:'';
                    $conversation_id = isset($res->ConversationID)?$res->ConversationID:'';
                    if($response_description || $error_message){
                        $input = array(
                            'command_id' => $command_id,
                            'originator_conversation_id' => $originator_conversation_id,
                            'request_amount' =>  $amount,
                            'account_reference' => $account_reference,
                            'sender_party' => $source_paybill,
                            'receiver_party' => $destination_paybill,
                            'request_time' => $request_time,
                            'created_on' => time(),
                            'response_code' => $response_code,
                            'response_code' => $response_description?$response_code:$error_code,
                            'conversation_id' => $conversation_id,
                            'response_description' => $response_description?:$error_message,
                            'service_status' => 1,
                            'account_id' => $account->id,
                            'disburse_charge' => 0,
                        );
                        $id = $this->ci->safaricom_m->insert_b2b_transactions($input);
                        if($error_code){
                            return array(
                                'code' => 'API065',
                                'description' => $error_message,
                            );
                        }else{
                            return array(
                                'code' => 200,
                                'description' => $response_description,
                            );
                        }
                    }else{
                        //print_r($res);
                        $this->ci->session->set_flashdata('error','This transaction could not be completed at the moment. Try again later.');
                        return FALSE;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Could not receive response from server. Try again later');
                    return FALSE;
                }
            }else{
                $this->ci->session->flashdata('error');
                return FALSE;
            }   
        }else{
            $this->ci->session->set_flashdata('error','Invalid paybill and paybill data');
            return FALSE;
        }
    }

    function disburse_bank_transfers($amount=0,$account=array(),$reference_number=0,$bank_name='',$bank_code=0,$bank_account_number=0,$account_name='',$remarks='',$request_callback_url='',$disburse_charge=0,$currency='KES'){
        if($bank = $this->ci->banks_m->get_bank_by_bank_code($bank_code)){
            $remarks = clean_data($remarks);
            $source_account_number = "01192163948300";
            $unique_reference_number = $this->ci->coop_bank_m->generate_unique_request_id();
            $destination_reference_number = $unique_reference_number."_1";
            $post_data = json_encode(array(
                "ISO2CountryCode" => "KE",
                "MessageReference" => $unique_reference_number,
                "CallBackUrl"=> "https://api.chamasoft.com:8443/coop_bank/payment_callbacks",
                "Source"=>array(
                    "AccountNumber"=> $source_account_number,
                    "Amount"=> currency($amount),
                    "TransactionCurrency"=> $currency,
                    "Narration"=> $remarks
                ),
                "Destinations"=> array(
                    array(
                        "ReferenceNumber" => $destination_reference_number,
                        "AccountNumber" => $bank_account_number,
                        "BankCode" => (int)$bank_code,
                        "Amount" => currency($amount),
                        "TransactionCurrency" => $currency,
                        "Narration" => $remarks,
                    ),
                )
            ));
            if($response = $this->ci->curl->coopBankRequests->pesalink_funds_transfer($post_data)){
                if($res = json_decode($response)){
                    $response_code = $res->MessageCode;
                    $response_description = $res->MessageDescription;
                    $request_time = strtotime($res->MessageDateTime);
                    $input = array(
                        'account_number' => $source_account_number,
                        'amount' => currency($amount),
                        'response_code' => $response_code,
                        'response_description' => $response_description,
                        'unique_reference_number' => $unique_reference_number,
                        'reference_number' => $reference_number,
                        'destination_reference_number' => $destination_reference_number,
                        'destination_account_number' => $bank_account_number,
                        'destination_bank_id' => $bank->id,
                        'description' => $remarks,
                        'currency' => $currency,
                        'account_id' => $account->id,
                        'request_time'      => $request_time,
                        'disburse_charge' => $disburse_charge,
                        'transfer_type' => 2,
                        'created_on' => time(),
                        'callback_url' => $request_callback_url,
                    );
                    if($this->ci->coop_bank_m->insert_bank_transfer($input)){
                        $payment_input = array(
                            'account_id' => $account->id,
                            'unique_reference_number' => $unique_reference_number,
                            'reference_number' => $reference_number,
                            'bank_account_number' => $bank_account_number,
                            'amount' => currency($amount),
                            'type' => 3,
                            'channel' => 4,
                            'status' => ($response_code == '0')?1:2,
                            'active' => 1,
                            'response_code' => $response_code,
                            'response_description' => $response_description,
                            'transaction_date' => time(),
                            'source_account_number' => $source_account_number,
                            'destination_reference_number' => $destination_reference_number,
                            'disburse_charge' => $disburse_charge,
                        );
                        $this->ci->transactions_m->insert_payment($payment_input);
                        if($response_code =="0"){
                            return array(
                                'code' => 200,
                                'description' => $response_description,
                            );
                        }else{
                            clear_transaction($account->account_number);
                            return array(
                                'code' => 'API065',
                                'description' => $response_description,
                            );
                        }
                    }else{
                        $this->ci->session->set_flashdata('error',"Something occured. Try again later");
                        return FALSE;
                    }
                }else{
                    $this->ci->session->set_flashdata('error',"Invalid data received from the bank");
                    return FALSE;
                }
            }else{  
                $this->ci->session->set_flashdata('error',"Error occured disbursing funds. Try again. System error: ".$this->ci->session->flashdata('error'));
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Destination bank is not a valid one');
        }

    }

    function disburse_funds_transaction_status($reference_number = 0,$amount=0,$account_id=0,$shortcode=0,$phone=0,$originator_conversation_id=0){
        $shortcode = '546866';
        $paybills = $this->ci->config->item('paybills');
        if(array_key_exists($shortcode, $paybills)){
            $paybills_data = $paybills[$shortcode];
            $username = $paybills_data['username'];
            $initiator_password = $paybills_data['initiator_password'];
            $encypted_initiator_password = openssl_key_encrypt($initiator_password,FALSE,TRUE);
            $amount = currency($amount);
            $post_data = json_encode(array(
                'Initiator' => $username,
                'SecurityCredential' => $encypted_initiator_password,
                'CommandID' => 'TransactionStatusQuery',
                'TransactionID' => $originator_conversation_id,
                'PartyA' => $phone,
                'IdentifierType' => '1',
                'ResultURL' => 'https://eazzychamademo.com/transaction_alerts/test_daraja_portal_callbacks',
                'QueueTimeOutURL' => 'https://eazzychamademo.com/transaction_alerts/test_daraja_portal_callbacks',
                'Remarks' => 'Withdrawal for Expense Payment',
                'Occasion' => ' '
            ));
            $url = 'https://api.safaricom.co.ke/mpesa/transactionstatus/v1/query';
            return $response = $this->ci->curl->darajaRequests->process_request($post_data,$url,$shortcode);
        }else{

            echo 'No paybill';
        }
    }


}?>