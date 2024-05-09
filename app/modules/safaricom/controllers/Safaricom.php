<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Safaricom extends Public_Controller{
    protected $data=array();
    protected $rules=array();

    public $b2c_shortcode_from = '521609';
    public $b2c_shortcode_to = '819332';
    public $b2c_shortcode = '819332';

    public $b2c_username = 'ChamasoftInitiator';
    public $b2c_initiator_password = 'KKihenju2015!!C$sH';

    public $spPassword;
    public $endpoint_url;

    public $b2b_username = 'ChamasoftInit';
    public $b2b_initiator_password = 'KKihenju2015!!C$sH';


    public $b2b_spid = '100736';
    public $b2b_password = 'Zaq12wsx@987';// 'Kenya123!';
    public $b2b_service_id = '100736000';
    public $timestamp;


    public $QueueTimeoutURL = 'https://23.239.27.43:4043/safaricom/queuetimeout';
    public $result_url = 'https://23.239.27.43:4043/safaricom/result_url';
    public $Validation_url = 'https://23.239.27.43:4043/safaricom/validation';
    public $confirmation_url = 'https://23.239.27.43:4043/safaricom/confirmation';
    public $OriginatorConversationID ;
    public $initiator_pass;
    public $headers;

    public $paybills = array();

    function __construct(){
        parent::__construct();
        $this->load->model('safaricom_m');
        $this->load->library('transactions');
        $this->load->library('mailer');
        $this->load->model('transactions/transactions_m');
        $this->endpoint_url = 'https://196.201.214.137:18423/mminterface/request';
        //http://196.201.214.136:8310/mminterface/request';
        $this->headers = 'From: Safaricom Files <notifications@chamasoft.com>' . "\r\n" .
                    'Reply-To: billing@chamasoft.com' . "\r\n".
                    'X-Mailer: PHP/' . phpversion();

        file_put_contents("logs/payment_log.dat","\n".date("d-M-Y h:i A")."\t".current_url()."\t".serialize($_REQUEST)."\t".serialize($_GET)."\t".serialize(file_get_contents('php://input'))."\t Headers are: \n",FILE_APPEND);

        // $this->paybills = $this->transactions->paybills;
        // $this->load->config('transaction');
        $this->paybills  = $this->config->item('paybills');
    }

    function index(){
        echo 'Safaricom file';
    }

    function promotionPayment($amount=0,$phone_number='',$group_id='',$callback_url=''){
        $amount =$amount?:6000;
        $phone_number =$phone_number?:'0728747061';
        $group_id=$group_id?:10;
        $request_time = date('YmdHis',time());
        $request_url = $this->agent->referrer();
        $callback_url = $callback_url?:'';
        print_r($this->_promotion_payment($phone_number,$amount,$group_id,$callback_url,$request_url,1,488228));
    }

    function b2c_withdrawal_request(){
        @ini_set('memory_limit','500M');
        error_reporting(1);
        $request_server = array(
            'server'=>array(
                'request_url' => $_SERVER['HTTP_REFERER'],
                'request_type' => $_SERVER['REQUEST_METHOD'],
                'request_server_ip' => $_SERVER['REMOTE_ADDR'],
            )
        );
        $request_file = array();
        $request = file_get_contents('php://input');
        if($request){
            $request = json_decode($request);
            if($request){
                $request_file = $request;
                $user_id = $request->customer->user_id;
                $phone = $request->customer->phone;
                $amount = $request->transaction->amount;
                $shortcode = $request->transaction->shortcode;
                $command_id = $request->transaction->command_id;
                $callback_url = $request->result->callback_url?:'';
                $username = $request->credentials->username;
                $password = $request->credentials->password;
                $group_id = $request->group->group_id;
                $group_name = $request->group->group_name;
                if($this->ion_auth->login_to_api_request(trim($username),trim($password))){
                    if($user_id&&valid_phone($phone)&&is_numeric($amount)&&$group_id&&$group_name&&is_numeric($shortcode)){
                        $result = $this->_promotion_payment($phone,$amount,$group_id,$callback_url,'',$user_id,$shortcode,$group_name,$command_id);
                        if($result){
                            $response = array(
                                'response'=>array(
                                        'status' => (string)$result->ResponseCode,
                                        'description'=>(string)$result->ResponseDesc,
                                        'connversation_ID' => (string)$result->ConversationID,
                                        'request_id' => (string)$result->OriginatorConversationID,
                                    ),
                            );
                        }else{
                            $response = array(
                                'response'=>array(
                                        'status' => 1,
                                        'description'=>'System internal error. Try again later',
                                    ),
                            );
                        }
                    }else{
                        $response = array(
                            'response'=>array(
                                    'status' => 1,
                                    'description'=>'Some essential Parameters are missing',
                                ),
                        );
                    }
                }else{
                    $response = array(
                        'response'=>array(
                                'status' => 1,
                                'description'=>'Wrong credentials used',
                            ),
                    );
                }
            }else{
                $response = array(
                    'response'=>array(
                            'status' => 1,
                            'description'=>'The file format is not acceptable',
                        ),
                );
            }
        }else{
            $response = array(
                    'response'=>array(
                            'status' => 1,
                            'description'=>'There was no file sent',
                        ),
                );
        }
        echo json_encode($request_server+$response+array('request'=>$request_file),JSON_PRETTY_PRINT);
    }

    function b2c_get_call_back_status(){
        $file = file_get_contents('php://input');
        $file = json_decode($file);
        if($file){
            $originator_conversation_id = $file->request_id;
            if($originator_conversation_id){
                $callback = $this->safaricom_m->get_b2c_request_by_originator_conversation_id($originator_conversation_id);
                if($callback){
                    $callback_result = array(
                            'transaction_id' => $callback->transaction_id,
                            'callback_result_description' => $callback->callback_result_description,
                            'callback_result_code' => $callback->callback_result_code,
                            'transaction_receipt' => $callback->transaction_receipt,
                            'transaction_amount' => $callback->transaction_amount,
                            'b2c_charges_paid' => $callback->b2c_charges_paid_account_available_funds,
                            'customer_registered' => $callback->b2c_receipt_is_registered_customer,
                            'transaction_completed_time' => $callback->transaction_completed_time,
                            'receiver_party_public_name' => $callback->receiver_party_public_name,
                            'working_account_available_funds' => $callback->b2c_working_account_available_funds,
                            'utility_account_available_funds' => $callback->b2c_utility_account_available_funds,
                        );
                    echo json_encode($callback_result,JSON_PRETTY_PRINT);
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    function _promotion_payment($phone=0,$amount=0,$group_id=0,$callback_url='',$request_url='',$user_id='',$shortcode = 0,$group_name='',$command_id=''){
        if($phone && $amount&&$group_id&&$shortcode){
            if(array_key_exists($shortcode, $this->paybills)){
                $request_time = date('YmdHis',time());
                $phone = valid_phone($phone);
                $paybills_data = $this->paybills[$shortcode];
                $spid = $paybills_data['spId'];
                $service_id = $paybills_data['service_id'];
                $password = $paybills_data['password'];
                $timestamp = $paybills_data['timestamp'];
                $initiator_password = $paybills_data['initiator_password'];
                $spPassword = base64_encode(hash('sha256',$spid.$password.$timestamp));
                $OriginatorConversationID = $spid . "_Chamasoft_" .$this->safaricom_m->calculate_entry();
                $username = $paybills_data['username'];
                $result_url = 'https://23.239.27.43:4043/safaricom/result_url';
                $timeout_url = 'https://23.239.27.43:4043/safaricom/queuetimeout';
                $environment = $paybills_data['environment'];
                $command_id = $command_id?:'PromotionPayment';
                if(preg_match('/Test/', $environment)){
                    $test_environment = TRUE;
                    $encrypted_initiator_password = openssl_key_encrypt($initiator_password,FALSE);
                    $endpoint_url = 'http://196.201.214.136:8310/mminterface/request';
                    $phone = valid_phone('254796778039'); 
                }else{
                    $test_environment = FALSE;
                    $encrypted_initiator_password = openssl_key_encrypt($initiator_password);
                    $endpoint_url = 'https://196.201.214.137:18423/mminterface/request';
                }

                $soap = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                       <soapenv:Header>
                          <tns:RequestSOAPHeader xmlns:tns="http://www.huawei.com/schema/osg/common/v2_1">
                             <tns:spId>'.$spid.'</tns:spId>
                             <tns:spPassword>'.$spPassword.'</tns:spPassword>
                             <tns:serviceId>'.$service_id.'</tns:serviceId>
                             <tns:timeStamp>'.$timestamp.'</tns:timeStamp>
                          </tns:RequestSOAPHeader>
                       </soapenv:Header>
                        <soapenv:Body>
                            <req:RequestMsg>
                            <![CDATA[<?xml version="1.0" encoding="UTF-8"?>
                            <request xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                            <Transaction>
                                <CommandID>'.$command_id.'</CommandID>
                                <LanguageCode>0</LanguageCode>
                                <OriginatorConversationID>'.$OriginatorConversationID.'</OriginatorConversationID>
                                <ConversationID></ConversationID>
                                <Remark>0</Remark>
                                <Parameters><Parameter>
                                    <Key>Amount</Key>
                                    <Value>'.$amount.'</Value>
                                    </Parameter></Parameters>
                                    <ReferenceData>
                                        <ReferenceItem>
                                            <Key>QueueTimeoutURL</Key>
                                            <Value>'.$timeout_url.'</Value>
                                        </ReferenceItem>
                                    </ReferenceData>
                                    <Timestamp>'.$request_time.'</Timestamp>
                            </Transaction>
                            <Identity>
                                <Caller>
                                    <CallerType>2</CallerType>
                                    <ThirdPartyID></ThirdPartyID>
                                    <Password>Password0</Password>
                                    <CheckSum>CheckSum0</CheckSum>
                                    <ResultURL>'.$result_url.'</ResultURL>
                                </Caller>
                                <Initiator>
                                  <IdentifierType>11</IdentifierType>
                                    <Identifier>'.$username.'</Identifier>
                                    <SecurityCredential>'.$encrypted_initiator_password.'</SecurityCredential>
                                  <ShortCode>'.$shortcode.'</ShortCode>
                                 </Initiator>
                                <PrimaryParty>
                                    <IdentifierType>4</IdentifierType>
                                    <Identifier>'.$shortcode.'</Identifier>
                                    <ShortCode>'.$shortcode.'</ShortCode>
                                </PrimaryParty>
                                <ReceiverParty>
                                    <IdentifierType>1</IdentifierType>
                                    <Identifier>'.$phone.'</Identifier>
                                    <ShortCode>ShortCode1</ShortCode>
                                </ReceiverParty>
                                <AccessDevice>
                                    <IdentifierType>1</IdentifierType>
                                    <Identifier>Identifier3</Identifier>
                                </AccessDevice>
                            </Identity>
                            <KeyOwner>1</KeyOwner>
                            </request>]]></req:RequestMsg>
                        </soapenv:Body>
                    </soapenv:Envelope>';
                $response = $this->curl->post_with_ssl($soap,$endpoint_url);
                @mail("geoffrey.githaiga@digitalvision.co.ke","PromotionPayment",$response,$this->headers);
                if($response){
                    $response_object = xml_create_object($response);
                    $result = $response_object->soapenvBody->reqResponseMsg->response;
                    $data = array(
                            'paybill'           =>  $shortcode,
                            'amount'            =>  $amount,
                            'request_status'    =>  $result->ResultType?:1,
                            'result_code'      =>   $result->ResponseCode?:'',
                            'phone'             =>  $phone,
                            'group_id'          =>  $group_id,
                            'group_name'          =>  $group_name,
                            'originator_conversation_id' => $OriginatorConversationID,
                            'created_on'        =>  time(),
                            'request_time'      =>  $request_time,
                            'request_url'       =>  $request_url,
                            'callback_url'      =>  $callback_url,
                            'result_description'=>  $result->ResponseDesc?:'',
                            'conversation_id'   =>  $result->ConversationID?:'',
                            'user_id'           =>  $user_id,
                            'test_environment' => $test_environment,
                            'command_id' => $command_id,
                        );
                    $req_id = $this->safaricom_m->insert_b2c($data);
                    if($req_id){
                        return $result;
                    }else{
                        return $result;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    function _transaction_status($shortcode=0,$mpesa_receipt_number=''){
        if($shortcode && $mpesa_receipt_number){
            if(array_key_exists($shortcode, $this->paybills)){
                $request_time = date('YmdHis',time());
                $paybills_data = $this->paybills[$shortcode];
                $spid = $paybills_data['spId'];
                $service_id = $paybills_data['service_id'];
                $password = $paybills_data['password'];
                $timestamp = $paybills_data['timestamp'];
                $initiator_password = $paybills_data['initiator_password'];
                $spPassword = base64_encode(hash('sha256',$spid.$password.$timestamp));
                $OriginatorConversationID = $spid . "_Chamasoft_" .$this->safaricom_m->calculate_entry();
                $username = $paybills_data['username'];
                $result_url = 'https://23.239.27.43:4043/safaricom/transaction_status_callback';
                $timeout_url = 'https://23.239.27.43:4043/safaricom/queuetimeout';
                $environment = $paybills_data['environment'];
                if(preg_match('/Test/', $environment)){
                    $test_environment = TRUE;
                    $encrypted_initiator_password = openssl_key_encrypt($initiator_password,FALSE);
                    $endpoint_url = 'http://196.201.214.136:8310/mminterface/request';
                }else{
                    $test_environment = FALSE;
                    $encrypted_initiator_password = openssl_key_encrypt($initiator_password);
                    $endpoint_url = 'https://196.201.214.137:18423/mminterface/request';
                }
                $soap = '
                    <?xml version="1.0" encoding="UTF-8"?>
                        <soapenv:Envelope 
                            xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
                            xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                            <soapenv:Header>
                                <tns:RequestSOAPHeader
                                    xmlns:tns="http://www.huawei.com/schema/osg/common/v2_1">
                                    <tns:spId>'.$spid.'</tns:spId>
                                     <tns:spPassword>'.$spPassword.'</tns:spPassword>
                                     <tns:timeStamp>'.$timestamp.'</tns:timeStamp>
                                     <tns:serviceId>'.$service_id.'</tns:serviceId>
                                </tns:RequestSOAPHeader>
                            </soapenv:Header>
                            <soapenv:Body>
                                <req:RequestMsg>
                                    <![CDATA[
                                    <?xml version="1.0" encoding="UTF-8"?>
                                    <Request 
                                        xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                                        <Transaction>
                                            <CommandID>TransactionStatusQuery</CommandID>
                                            <LanguageCode>0</LanguageCode>
                                            <OriginatorConversationID>'.$OriginatorConversationID.'</OriginatorConversationID>
                                            <ConversationID></ConversationID>
                                            <Remark>0</Remark>
                                            <Parameters>
                                                <Parameter>
                                                    <Key>ReceiptNumber</Key>
                                                    <Value>'.$mpesa_receipt_number.'</Value>
                                                </Parameter>
                                                <Parameter>
                                                    <Key>OriginatorConversationID</Key>
                                                    <Value>9fc27bc0-6196-478f-a37d-940f95cc03db</Value>
                                                </Parameter>
                                            </Parameters>
                                            <ReferenceData>
                                                <ReferenceItem>
                                                    <Key>QueueTimeoutURL</Key>
                                                    <Value>'.$timeout_url.'</Value>
                                                </ReferenceItem>
                                            </ReferenceData>
                                            <Timestamp>2017-03-13T16:26:43.835Z</Timestamp>
                                        </Transaction>
                                        <Identity>
                                            <Caller>
                                                <CallerType>2</CallerType>
                                                <ThirdPartyID>broker_4</ThirdPartyID>
                                                <Password>T50mhFnEwrPNy0BU0b+n+8Hwdb2LhsKG0KSPemuiXiZrcYoemz5vIl0uUzs1OSUPi5cumPF4djZuuIERNVA+znH85Iy2k+DQQtFRGTVKBWNZZpDjus9RE0BD7iuBFjiAzr5UNJcpeetSO0nmG7O9sfXJ/tBWCnRPRE8vWNzlrq0tBhFl1EtWvkBDY7Daj/MWeigkumOGwB0/GDvO0AsOJZtHuGeddGHEi/lb1oJxlCOKXts8ZxopnbuDN5sB4qD3P5QUxgTfE1KFHEeklvwWUcnNpuDz7q12k0yzYhsJEE4MyiVwjZVuo66TPQd4AjU+JDzEIAwG4IJx98dh5C4AOA==</Password>
                                                <ResultURL>'.$result_url.'</ResultURL>
                                            </Caller>
                                            <Initiator>
                                                <IdentifierType>11</IdentifierType>
                                                <Identifier>'.$username.'</Identifier>
                                                <SecurityCredential>'.$encrypted_initiator_password.'</SecurityCredential>
                                                <ShortCode>'.$shortcode.'</ShortCode>
                                            </Initiator>
                                        </Identity>
                                        <KeyOwner>1</KeyOwner>
                                    </Request>
                                    ]]>
                                </req:RequestMsg>
                            </soapenv:Body>
                        </soapenv:Envelope>
                ';
                return $this->curl->post_with_ssl($soap,$endpoint_url);
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }  
    }

    function b2b_request($amount=0,$sender_party=0,$receiver_party=0,$command_id=''){
        $sender_party = '488776';
        $receiver_party = '488228';
        $amount = 50;
        print_r($this->_b2b_request($amount,$sender_party,$receiver_party,$command_id));
    }

    function b2b_transaction_request(){
        @ini_set('memory_limit','500M');
        $response = '';
        error_reporting(1);
        $request_server = array(
                    'request'=>array(
                        'request_url' => $_SERVER['HTTP_REFERER'],
                        'request_type' => $_SERVER['REQUEST_METHOD'],
                        'request_server_ip' => $_SERVER['REMOTE_ADDR'],
                        'request_server_ip' => $_SERVER['REMOTE_ADDR'],
                    )
                );
        $request = file_get_contents('php://input');
        if($request){
            $request = json_decode($request);
            if($request){
                $sender_party = $request->sender->sender_shortcode;
                $receiver_party = $request->receiver->receiver_shortcode;
                $amount = $request->transaction->amount;
                $callback_url = $request->result->callback_url?:'';
                $command_id = $request->transaction->command_id;
                $username = $request->credentials->username;
                $password = $request->credentials->password;
                if($this->ion_auth->login_to_api_request(trim($username),trim($password))){
                    if(is_numeric($sender_party)&&is_numeric($receiver_party)&&is_numeric($amount)){
                        $result = $this->_b2b_request($amount,$sender_party,$receiver_party,$command_id);
                        if($result){
                            $response = array(
                                'response'=>array(
                                        'status' => (string)$result->ResponseCode,
                                        'description'=>(string)$result->ResponseDesc,
                                        'connversation_ID' => (string)$result->ConversationID,
                                        'request_id' => (string)$result->OriginatorConversationID,
                                        'server_status' => (int)$result->ServiceStatus,
                                    ),
                            );
                        }else{
                            $response = array(
                                'response'=>array(
                                        'status' => 1,
                                        'description'=>'System internal error. Try again later',
                                    ),
                            );
                        }
                    }else{
                        $response = array(
                            'response'=>array(
                                    'status' => 1,
                                    'description'=>'Some essential Parameters are missing',
                                ),
                        );
                    }
                }else{
                    $response = array(
                        'response'=>array(
                                'status' => 1,
                                'description'=>'Wrong credentials used',
                            ),
                    );
                }
            }else{
                $response = array(
                    'response'=>array(
                            'status' => 1,
                            'description'=>'The file format is not acceptable',
                        ),
                );
            }
        }else{
            $response = array(
                    'response'=>array(
                            'status' => 1,
                            'description'=>'There was no file sent',
                        ),
                );
        }
        echo json_encode($request_server+$response,JSON_PRETTY_PRINT);
    }

    function b2b_get_call_back_status(){
        $file = file_get_contents('php://input');
        $file = json_decode($file);
        if($file){
            $originator_conversation_id = $file->request_id;
            if($originator_conversation_id){
                $callback = $this->safaricom_m->get_b2b_transaction_by_originator_conversation_id($originator_conversation_id);
                if($callback){
                    $callback_result = array(
                            'result_code' => $callback->result_code,
                            'result_description' => $callback->result_description,
                            'transaction_id' => $callback->transaction_id,
                            'debit_account_balance' => $callback->debit_account_balance,
                            'initiator_account_current_balance' => $callback->initiator_account_current_balance,
                            'debit_account_balance' => $callback->debit_account_balance,
                            'transaction_completed_time' => $callback->transaction_completed_time,
                            'amount' => $callback->amount,
                            'debit_party_charges' => $callback->debit_party_charges,
                            'receiver_party_public_name' => $callback->receiver_party_public_name,
                            'sender_party_public_name' => $callback->sender_party_public_name,
                            'currency' => $callback->currency,
                        );
                    echo json_encode($callback_result,JSON_PRETTY_PRINT);
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    function _b2b_request($amount=0,$sender_party=0,$receiver_party=0,$command_id=''){
        if($amount&&$sender_party&&$receiver_party){
            if(array_key_exists($sender_party, $this->paybills)){
                $request_time = date('YmdHis',time());
                $originator_conversation_id = rand(1111,9999).'-Chamasoft-'.$this->safaricom_m->generate_b2b_originator_conversation_id();
                $command_id = $command_id?:'BusinessToBusinessTransfer';
                $sender_party = $sender_party?:'521609';
                $receiver_party = $receiver_party?:'819332';
                $paybills_data = $this->paybills[$sender_party];
                $username = $paybills_data['username'];
                $initiator_password = $paybills_data['initiator_password'];
                $encrypted_initiator_password = openssl_key_encrypt($initiator_password);
                $spid = $paybills_data['spId'];
                $service_id = $paybills_data['service_id'];
                $password = $paybills_data['password'];
                $timestamp = $paybills_data['timestamp'];
                $spPassword = base64_encode(hash('sha256',$spid.$password.$timestamp));
                $result_url = 'https://23.239.27.43:4043/safaricom/b2b_result_url';
                $QueueTimeoutURL = 'https://23.239.27.43:4043/safaricom/queuetimeout';
                $environment = $paybills_data['environment'];
                if(preg_match('/Test/', $environment)){
                    $test_environment = TRUE;
                    $encrypted_initiator_password = openssl_key_encrypt($initiator_password,FALSE);
                    $endpoint_url = 'http://196.201.214.136:8310/mminterface/request'; 
                }else{
                    $test_environment = FALSE;
                    $encrypted_initiator_password = openssl_key_encrypt($initiator_password);
                    $endpoint_url = 'https://196.201.214.137:18423/mminterface/request';
                }
                $initiator_information ='
                        <Initiator>
                            <IdentifierType>11</IdentifierType>
                            <Identifier>'.$username.'</Identifier>
                            <SecurityCredential>'.$encrypted_initiator_password.'</SecurityCredential>
                            <ShortCode>'.$sender_party.'</ShortCode>
                        </Initiator>';

                $soap = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                <soapenv:Header>
                  <tns:RequestSOAPHeader xmlns:tns="http://www.huawei.com/schema/osg/common/v2_1">
                     <tns:spId>'.$spid.'</tns:spId>
                     <tns:spPassword>'.$spPassword.'</tns:spPassword>
                     <tns:timeStamp>'.$timestamp.'</tns:timeStamp>
                     <tns:serviceId>'.$service_id.'</tns:serviceId>
                  </tns:RequestSOAPHeader>
                </soapenv:Header>
                <soapenv:Body>
                  <req:RequestMsg><![CDATA[<?xml version=\'1.0\' encoding=\'UTF-8\'?><request xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                <Transaction>
                        <CommandID>'.$command_id.'</CommandID>
                        <LanguageCode></LanguageCode>
                        <OriginatorConversationID>'.$originator_conversation_id.'</OriginatorConversationID>
                        <ConversationID></ConversationID>
                        <Remark>0</Remark>
                <Parameters>
                <Parameter>
                        <Key>Amount</Key>
                        <Value>'.$amount.'</Value>
                </Parameter>
                <Parameter>
                        <Key>AccountReference</Key>
                        <Value>'.$amount.'</Value>
                </Parameter>
                </Parameters>
                <ReferenceData>
                        <ReferenceItem>
                                <Key>QueueTimeoutURL</Key>
                                <Value>'.$QueueTimeoutURL.'</Value>
                        </ReferenceItem></ReferenceData>
                        <Timestamp>'.$request_time.'</Timestamp>
                </Transaction>
                <Identity>
                        <Caller>
                               <CallerType>2</CallerType>
                                <ThirdPartyID>broker_4</ThirdPartyID>
                                <Password> +JDzEIAwG4IJx98dh5C4AOA==</Password>
                                <CheckSum>null</CheckSum>
                                <ResultURL>'.$result_url.'</ResultURL>
                        </Caller>'.
                        $initiator_information
                        .'<PrimaryParty>
                                <IdentifierType>4</IdentifierType>
                                <Identifier>'.$sender_party.'</Identifier>
                                <ShortCode></ShortCode>
                        </PrimaryParty>
                        <ReceiverParty>
                                <IdentifierType>4</IdentifierType>
                                <Identifier>'.$receiver_party.'</Identifier>
                                <ShortCode></ShortCode>
                        </ReceiverParty>
                        <AccessDevice>
                                <IdentifierType>4</IdentifierType>
                                <Identifier>1</Identifier>
                                </AccessDevice></Identity>
                                <KeyOwner>1</KeyOwner>
                        </request>]]></req:RequestMsg>
                   </soapenv:Body>
                </soapenv:Envelope>';
                $response = $this->curl->post_with_ssl($soap,$endpoint_url);
                $data = array(
                        'command_id' => $command_id,
                        'originator_conversation_id' => $originator_conversation_id,
                        'request_amount' =>  $amount,
                        'account_reference' => $amount,
                        'sender_party' => $sender_party,
                        'receiver_party' => $receiver_party,
                        'request_time' => $request_time,
                        'created_on' => time(),
                    );
                if($response){
                    $response = xml_create_object($response);
                    $soap_body = $response->soapenvBody->reqResponseMsg->response;
                    $data = $data+array(
                            'response_code' => (int)$soap_body->ResponseCode,
                            'conversation_id' => (string)$soap_body->ConversationID,
                            'response_description' => (string)$soap_body->ResponseDesc,
                            'service_status' => (int)$soap_body->ServiceStatus,
                        );
                }
                $id = $this->safaricom_m->insert_b2b_transactions($data);
                if($id){
                    return $soap_body;
                }else{
                    return $soap_body;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    function b2b_result_url(){
        $file = file_get_contents('php://input');
        if($file){
            if($_SERVER['SERVER_PORT'] == '4043'){
                file_put_contents("logs/b2b_result_file.txt","\n".date("d-M-Y h:i A").$file,FILE_APPEND);
                $soap_body = xml_create_object($file);
                $result = $soap_body->soapenvBody->resResultMsg->Result;
                $request = $this->safaricom_m->get_b2b_transaction_by_originator_conversation_id($result->OriginatorConversationID);
                if($request){
                    $update = array(
                        'result_type' => (int)$result->ResultType,
                        'result_code' => (int)$result->ResultCode,
                        'result_description' => (string)$result->ResultDesc,
                        'transaction_id' => (string)$result->TransactionID,
                    );
                    $ResultParameters = $result->ResultParameters;
                    if(isset($ResultParameters)){
                        $result_params = $ResultParameters->ResultParameter;

                        $update = $update+ array(
                                'debit_account_balance' => (string)$result_params[0]->Value,
                                'initiator_account_current_balance' => (string)$result_params[1]->Value,
                                'debit_account_current_balance' => (string)$result_params[2]->Value,
                                'amount' => (string)$result_params[3]->Value,
                                'transaction_completed_time' => (string)$result_params[4]->Value,
                                'debit_party_charges' => (string)$result_params[5]->Value,
                                'receiver_party_public_name' => (string)$result_params[6]->Value,
                                'sender_party_public_name' => (string)$result_params[7]->Value,
                                'currency' => (string)$result_params[8]->Value,
                            );
                    }else{
                        @mail('edwin.njoroge@digitalvision.co.ke','B2B Result URL','Result Parameter is not set',$this->headers);
                    echo 'none no result';
                    }
                    $update_id = $this->safaricom_m->update_b2b_transactions($request->id,$update);
                    if($update_id){
                        @mail('edwin.njoroge@digitalvision.co.ke','B2B Result URL','Updated '.$request->id,$this->headers);
                    }else{
                        @mail('edwin.njoroge@digitalvision.co.ke','B2B Result URL','Didnt Update '.$request->id,$this->headers);
                    }
                }else{
                    @mail('edwin.njoroge@digitalvision.co.ke','B2B Result URL','Unable to get request from the result',$this->headers);
                }
                header("Content-type: text/xml");
                echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                       <soapenv:Header/>
                       <soapenv:Body>
                          <req:ResponseMsg><![CDATA[<?xml version="1.0" encoding="UTF-8"?>
                    <response xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/response">
                        <ResponseCode>00000000</ResponseCode>
                        <ResponseDesc>success</ResponseDesc>
                    </response>]]></req:ResponseMsg>
                       </soapenv:Body>
                    </soapenv:Envelope>';
            }else{
                header("Content-type: text/xml");
                echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                       <soapenv:Header/>
                       <soapenv:Body>
                          <req:ResponseMsg><![CDATA[<?xml version="1.0" encoding="UTF-8"?>
                    <response xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/response">
                        <ResponseCode>10000202</ResponseCode>
                        <ResponseDesc>Wrong port '.$_SERVER['SERVER_PORT'].'</ResponseDesc>
                    </response>]]></req:ResponseMsg>
                       </soapenv:Body>
                    </soapenv:Envelope>';
            }
        }else{
            header("Content-type: text/xml");
            echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                   <soapenv:Header/>
                   <soapenv:Body>
                      <req:ResponseMsg><![CDATA[<?xml version="1.0" encoding="UTF-8"?>
                <response xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/response">
                    <ResponseCode>10000000</ResponseCode>
                    <ResponseDesc>NO file Sent</ResponseDesc>
                </response>]]></req:ResponseMsg>
                   </soapenv:Body>
                </soapenv:Envelope>';
        }
    }

    function queuetimeout(){
        @ini_set('memory_limit','500M');
        error_reporting(1);
        $file = file_get_contents('php://input');
        $headers = 'From: B2C Safaricom Files <notifications@chamasoft.com>' . "\r\n" .
                    'Reply-To: billing@chamasoft.com' . "\r\n".
                    'X-Mailer: PHP/' . phpversion();
        if($file){
            @mail('geoffrey.githaiga@digitalvision.co.ke','b2c files timeout',$file,$headers);
        }else{
            @mail('geoffrey.githaiga@digitalvision.co.ke','b2c files timeout','No file',$headers);
        }
    }

    function result_url(){
        @ini_set('memory_limit','500M');
        error_reporting(1);
        $file = file_get_contents('php://input');
        $headers = 'From: B2C Safaricom Files <notifications@chamasoft.com>' . "\r\n" .
                    'Reply-To: billing@chamasoft.com' . "\r\n".
                    'X-Mailer: PHP/' . phpversion();
                    $file1 = $file;
        if($_SERVER['SERVER_PORT'] == '4043'){
            if($file){
                file_put_contents("logs/b2c_result_file.txt","\n".date("d-M-Y h:i A").$file,FILE_APPEND);
                $response = xml_create_object($file);
                $response = $response->soapenvBody->resResultMsg->Result;
                $request = $this->safaricom_m->get_b2c_request_by_originator_conversation_id($response->OriginatorConversationID);
                // Some code happens here
                if($request){
                    $result = $response->ResultParameters;
                    $update= array(
                        'callback_result_description'   =>  $response->ResultDesc,
                        'callback_result_code'          =>  $response->ResultCode,
                        'transaction_id'                =>  $response->TransactionID,
                        'modified_on'                   =>  time(),
                    );

                    if($result->ResultParameter){
                        $update=$update+array(
                                'transaction_receipt'           =>  $result->ResultParameter[0]->Value,
                                'transaction_amount'            =>  $result->ResultParameter[1]->Value,
                                'b2c_charges_paid_account_available_funds' =>  $result->ResultParameter[2]->Value,
                                'b2c_receipt_is_registered_customer' =>  $result->ResultParameter[3]->Value,
                                'transaction_completed_time'    =>  strtotime($result->ResultParameter[4]->Value),
                                'receiver_party_public_name'    =>  $result->ResultParameter[5]->Value,
                                'b2c_working_account_available_funds'    =>  $result->ResultParameter[6]->Value,
                                'b2c_utility_account_available_funds'    =>  $result->ResultParameter[7]->Value,
                            );
                    }
                    $update_id = $this->safaricom_m->update_b2c_request($request->id,$update);
                    if($update_id){
                          //notify the callback url
                        $callback_result = $this->safaricom_m->get_b2c_request_by_originator_conversation_id($response->OriginatorConversationID);
                        if($callback_result->callback_url){
                            $result = array(
                                'result' => array(
                                    'request_id' => $callback_result->originator_conversation_id,
                                    'conversation_id' => $callback_result->conversation_id,
                                    'result_status' => $callback_result->callback_result_code,
                                    'result_description' => $callback_result->callback_result_description,
                                    'transaction_id' => $callback_result->transaction_id,
                                    'transaction_receipt' => $callback_result->transaction_receipt,
                                    'transaction_amount' => $callback_result->transaction_amount,
                                    'b2c_charges_paid_account_available_funds' => $callback_result->b2c_charges_paid_account_available_funds,
                                    'b2c_receipt_is_registered_customer' => $callback_result->b2c_receipt_is_registered_customer,
                                    'transaction_completed_time' => $callback_result->transaction_completed_time,
                                    'receiver_party_public_name' => $callback_result->receiver_party_public_name,
                                    'b2c_working_account_available_funds' => $callback_result->b2c_working_account_available_funds,
                                    'b2c_utility_account_available_funds'=> $callback_result->b2c_utility_account_available_funds,
                                ),
                            );
                            $result = $this->curl->post_json(json_encode($result),$callback_result->callback_url);
                            @mail('geoffrey.githaiga@digitalvision.co.ke','B2C files callback result from sandbox - Success',$result.' callback '.$callback_result->callback_url,$headers);
                        }
                        header("Content-type: text/xml");
                        echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                           <soapenv:Header/>
                           <soapenv:Body>
                              <req:ResponseMsg><![CDATA[<?xml version="1.0" encoding="UTF-8"?>
                        <response xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/response">
                            <ResponseCode>00000000</ResponseCode>
                            <ResponseDesc>success</ResponseDesc>
                        </response>]]></req:ResponseMsg>
                           </soapenv:Body>
                        </soapenv:Envelope>';
                        @mail('geoffrey.githaiga@digitalvision.co.ke','B2C files - Success','Update Id on Request ID'.$request->id.$file1,$headers);
                    }else{
                        header("Content-type: text/xml");
                        echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                           <soapenv:Header/>
                           <soapenv:Body>
                              <req:ResponseMsg><![CDATA[<?xml version="1.0" encoding="UTF-8"?>
                        <response xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/response">
                            <ResponseCode>20000003</ResponseCode>
                            <ResponseDesc>Unable to receive the callback</ResponseDesc>
                        </response>]]></req:ResponseMsg>
                           </soapenv:Body>
                        </soapenv:Envelope>';
                       @mail('geoffrey.githaiga@digitalvision.co.ke','B2C files - Failed Update','Request ID'.$request->id,$headers);
                    }

                  
                }else{
                    header("Content-type: text/xml");
                    echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                       <soapenv:Header/>
                       <soapenv:Body>
                          <req:ResponseMsg><![CDATA[<?xml version="1.0" encoding="UTF-8"?>
                    <response xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/response">
                        <ResponseCode>20000003</ResponseCode>
                        <ResponseDesc>Error locating request</ResponseDesc>
                    </response>]]></req:ResponseMsg>
                       </soapenv:Body>
                    </soapenv:Envelope>';
                   @mail('geoffrey.githaiga@digitalvision.co.ke','B2C files - Failed','No Request ID '.$response,$headers);
                }

            }else{
                header("Content-type: text/xml");
                echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                       <soapenv:Header/>
                       <soapenv:Body>
                          <req:ResponseMsg><![CDATA[<?xml version="1.0" encoding="UTF-8"?>
                    <response xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/response">
                        <ResponseCode>20000003</ResponseCode>
                        <ResponseDesc>No files sent</ResponseDesc>
                    </response>]]></req:ResponseMsg>
                       </soapenv:Body>
                    </soapenv:Envelope>';
                  @mail('geoffrey.githaiga@digitalvision.co.ke','B2C files - Failed','No Files Sent ',$headers);
            }
        }else{
            header("Content-type: text/xml");
            echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                       <soapenv:Header/>
                       <soapenv:Body>
                          <req:ResponseMsg><![CDATA[<?xml version="1.0" encoding="UTF-8"?>
                    <response xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/response">
                        <ResponseCode>20000003</ResponseCode>
                        <ResponseDesc>Wrong Port '.$_SERVER['SERVER_PORT'].'</ResponseDesc>
                    </response>]]></req:ResponseMsg>
                       </soapenv:Body>
                    </soapenv:Envelope>';
                  @mail('geoffrey.githaiga@digitalvision.co.ke','B2C files - Failed','Wrong Port '.$_SERVER['SERVER_PORT'],$headers);
        }
    }

    function test_callback($id=0){
        $callback_result = $this->safaricom_m->get_b2c_request($id);
                        if($callback_result->callback_url){
                            $result = array(
                                'result' => array(
                                    'request_id' => $callback_result->originator_conversation_id,
                                    'conversation_id' => $callback_result->conversation_id,
                                    'result_status' => $callback_result->callback_result_code,
                                    'result_description' => $callback_result->callback_result_description,
                                    'transaction_id' => $callback_result->transaction_id,
                                    'transaction_receipt' => $callback_result->transaction_receipt,
                                    'transaction_amount' => $callback_result->transaction_amount,
                                    'b2c_charges_paid_account_available_funds' => $callback_result->b2c_charges_paid_account_available_funds,
                                    'b2c_receipt_is_registered_customer' => $callback_result->b2c_receipt_is_registered_customer,
                                    'transaction_completed_time' => $callback_result->transaction_completed_time,
                                    'receiver_party_public_name' => $callback_result->receiver_party_public_name,
                                    'b2c_working_account_available_funds' => $callback_result->b2c_working_account_available_funds,
                                    'b2c_utility_account_available_funds'=> $callback_result->b2c_utility_account_available_funds,
                                ),
                            );
                            print_r($this->curl->post_json(json_encode($result),$callback_result->callback_url));
                        }
    }

    function c2b_register(){
        $soap = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                    <soapenv:Header>
                         <tns:RequestSOAPHeader xmlns:tns="http://www.huawei.com/schema/osg/common/v2_1">
                            <tns:spId>'.$this->spid.'</tns:spId>
                            <tns:spPassword>'.$this->spPassword.'</tns:spPassword>
                            <tns:timeStamp>'.$this->timestamp.'</tns:timeStamp>
                            <tns:serviceId>'.$this->service_id.'</tns:serviceId>
                        </tns:RequestSOAPHeader>
                    </soapenv:Header>
                    <soapenv:Body>
                        <req:RequestMsg><![CDATA[<?xml version="1.0" encoding="UTF-8"?>
                            <request xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                            <Transaction>
                                <CommandID>RegisterURL</CommandID>
                                <OriginatorConversationID>Reg-266-1126</OriginatorConversationID>
                                <Parameters>
                                    <Parameter>
                                        <Key>ResponseType</Key>
                                        <Value>Completed</Value>
                                    </Parameter>
                                </Parameters>
                                 <ReferenceData>
                                    <ReferenceItem>
                                        <Key>ValidationURL</Key>
                                        <Value>'.$this->Validation_url.'</Value>
                                    </ReferenceItem>
                                    <ReferenceItem>
                                        <Key>ConfirmationURL</Key>
                                        <Value>'.$this->confirmation_url.'</Value>
                                    </ReferenceItem>
                                </ReferenceData>
                            </Transaction>
                            <Identity>
                                <Caller>
                                    <CallerType>0</CallerType>
                                    <ThirdPartyID/>
                                    <Password/>
                                    <CheckSum/>
                                    <ResultURL/>
                                </Caller>
                                <Initiator>
                                    <IdentifierType>1</IdentifierType>
                                    <Identifier/>
                                    <SecurityCredential/>
                                    <ShortCode/>
                                </Initiator>
                                <PrimaryParty>
                                    <IdentifierType>1</IdentifierType>
                                    <Identifier/>
                                    <ShortCode>521609</ShortCode>
                                </PrimaryParty>
                            </Identity>
                            <KeyOwner>1</KeyOwner>
                            </request>]]>
                        </req:RequestMsg>
                    </soapenv:Body>
                </soapenv:Envelope>';

        $url = 'https://196.201.214.137:18423/mminterface/registerURL';

        print_r($soap);
        print_r($url);

        $response = $this->curl->post_with_ssl($soap,$url);

        print_r($response);
    }

    function validation(){
        if(isset($_REQUEST))
        {
            $file = file_get_contents('php://input');
            $body = json_decode($file);
           
            file_put_contents('logs/c2b_validation_file.txt',"\n".date("d-M-Y h:i A").$file,FILE_APPEND);
         
            $server_ip = $_SERVER['REMOTE_ADDR'];
           if($file){
                   
                    if($body->TransID && $body->TransAmount){
                        $transaction_id = trim($body->TransID);
                        $reference_number = trim($body->BillRefNumber);
                        $transaction_date = trim($body->TransTime);
                        $transaction_amount = trim($body->TransAmount);
                        $transaction_currency = 'KES';
                        $transaction_type = trim($body->TransactionType);
                        $transaction_particulars = 'MPESA Transaction';
                        $phone = trim($body->MSISDN);
                        $debit_account = trim($body->InvoiceNumber)?:trim($body->BillRefNumber);
                        $customer_info = $body->FirstName.' '.$body->MiddleName.' '.$body->LastName ;
                        $shortcode = $body->BusinessShortCode;
                        $debit_customer = $customer_info;
                        if($transaction_id && $debit_account && $transaction_amount && $transaction_date && $transaction_currency){
                            if(!$this->safaricom_m->is_transaction_dublicate($transaction_id)){
                                if($this->safaricom_m->is_loan_number_recognized($debit_account)){
                                    $transaction_date = strtotime($transaction_date);
                                    $input_data = array(
                                            'transaction_id' => $transaction_id,
                                            'reference_number' => $reference_number,
                                            'transaction_date' => $transaction_date,
                                            'amount' => $transaction_amount,
                                            'active' => 1,
                                            'currency' => $transaction_currency,
                                            'transaction_type' => $transaction_type,
                                            'transaction_particulars' => $transaction_particulars,
                                            'phone' => $phone,
                                            'account' => $debit_account,
                                            'shortcode' => $shortcode,
                                            'customer_name' => $debit_customer,
                                            'created_on' => time(),
                                        );
                                    $id = $this->safaricom_m->insert_c2b($input_data);
                                   
                                    if($id){
                                        $response=array(
                                            "ResultCode"=>0,
                                            "ResultDesc"=>"Service processing successful. Awaiting Confirmation"
                                        );
                                 file_put_contents('logs/c2b_validation_response_file.txt',"\n".date("d-M-Y h:i A").json_encode($response),FILE_APPEND);

                                       echo json_encode($response);
                                    }else{
                                        
                                         $response=array(
                                            "ResultCode"=>1,
                                            "ResultDesc"=>"Error processing-insert the entry"
                                        );
                                    file_put_contents('logs/c2b_validation_response_file.txt',"\n".date("d-M-Y h:i A").json_encode($response),FILE_APPEND);

                                       echo json_encode($response);

                                    }
                                }else{  

                                    $response=array(
                                            "ResultCode"=>1,
                                            "ResultDesc"=>"This loan is not recognized"
                                        );
                                 file_put_contents('logs/c2b_validation_response_file.txt',"\n".date("d-M-Y h:i A").json_encode($response),FILE_APPEND);

                                       echo json_encode($response);
                                }
                            }else{
                                $response=array(
                                    "ResultCode"=>0,
                                    "ResultDesc"=>"Duplicate entry"
                                );
                                file_put_contents('logs/c2b_validation_response_file.txt',"\n".date("d-M-Y h:i A").json_encode($response),FILE_APPEND);

                               echo json_encode($response);
                            }
                        }
                        else{
                            $mail = ' REMOTE_ADDR '.$server_ip.' Server Address '.$_SERVER['SERVER_ADDR'].' Some parameters are missing';
                            if(!$transaction_id){
                                $mail.=' Transaction id empty '.$transaction_id;
                            }
                            if(!$debit_account){
                                $mail.=' Debit account empty '.$debit_account;
                            }
                            if(!$transaction_amount){
                                $mail.=' Amount empty '.$transaction_amount;
                            }
                            if(!$transaction_date){
                                $mail.=' Date empty '.$transaction_amount;
                            }
                            $response=array(
                                "ResultCode"=>1,
                                "ResultDesc"=>"Some parameter are missing"
                            );
                            file_put_contents('logs/c2b_validation_response_file.txt',"\n".date("d-M-Y h:i A").json_encode($response),FILE_APPEND);

                           echo json_encode($response);
                        }
                    }else{
                        $response=array(
                            "ResultCode"=>1,
                            "ResultDesc"=>"Some parameter are missing"
                        );
                        file_put_contents('logs/c2b_validation_response_file.txt',"\n".date("d-M-Y h:i A").json_encode($response),FILE_APPEND);

                       echo json_encode($response);
                    }
               
            }else{
                $response=array(
                    "ResultCode"=>0,
                    "ResultDesc"=>"Some parameter are missing"
                );
               echo json_encode($response);
            }
        }
    }

    function confirmation(){
        if($_REQUEST){
            $file = file_get_contents('php://input');
            file_put_contents('logs/c2b_confirmation_file.txt',"\n".date("d-M-Y h:i A").$file,FILE_APPEND);
            $headers = 'From: C2B Safaricom Validation Files <notifications@chamasoft.com>' . "\r\n" .
                    'Reply-To: billing@chamasoft.com' . "\r\n".
                    'X-Mailer: PHP/' . phpversion();
            
            @mail('edwin.njoroge@digitalvision.co.ke','C2B Confirmation File',$file,$headers);
            if($file){
               
                    $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $file);
                    $xml = new SimpleXMLElement($response);
                    $body = $xml->soapenvBody->ns1C2BPaymentConfirmationRequest;
                    if($body){
                        $transaction_id = $body->TransID;
                        //update account balance
                        $organization_balance = $body->OrgAccountBalance;
                        $update_id = $this->safaricom_m->update_c2b_by_transaction_id($transaction_id,$organization_balance);
                        if($update_id){
                            header("Content-type: text/xml");
                            echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment">
                               <soapenv:Header/>
                               <soapenv:Body>
                                  <c2b:C2BPaymentConfirmationResult>0 Success</c2b:C2BPaymentConfirmationResult>
                               </soapenv:Body>
                            </soapenv:Envelope>';
                            // $this->send_c2b_notifications();
                        }else{
                            header("Content-type: text/xml");
                             echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment">
                               <soapenv:Header/>
                               <soapenv:Body>
                                  <c2b:C2BPaymentConfirmationResult>Unable to Update the request</c2b:C2BPaymentConfirmationResult>
                               </soapenv:Body>
                            </soapenv:Envelope>';
                        }
                    }else{
                        header("Content-type: text/xml");
                        echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment">
                           <soapenv:Header/>
                           <soapenv:Body>
                              <c2b:C2BPaymentConfirmationResult>C2B Body Error</c2b:C2BPaymentConfirmationResult>
                           </soapenv:Body>
                        </soapenv:Envelope>';
                    }
                
            }else{
                header("Content-type: text/xml");
                echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment">
                    <soapenv:Header/>
                        <soapenv:Body>
                            <c2b:C2BPaymentConfirmationResult>
                                C2B Payment No files sent.
                            </c2b:C2BPaymentConfirmationResult>
                        </soapenv:Body>
                    </soapenv:Envelope>';
            }
        }
    }

    function organizationSettleMent(){
        $shortcode = '521609';
        if($this->paybills[$shortcode]){
            $paybills_data = $this->paybills[$shortcode];
            $spid = $paybills_data['spId'];
            $service_id = $paybills_data['service_id'];
            $password = $paybills_data['password'];
            $timestamp = $paybills_data['timestamp'];
            $spPassword = base64_encode(hash('sha256',$spid.$password.$timestamp));
            $QueueTimeoutURL = "https://chamasoft.com/safaricom/test_daraja_portal";
            $username = $paybills_data['username'];
            $request_time = date('YmdHis',time());
            $initiator_password = $paybills_data['initiator_password'];
            $encrypted_initiator_password = openssl_key_encrypt($initiator_password);
            $soap = '<?xml version="1.0" encoding="UTF-8"?>
                        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                        <soapenv:Header>
                          <tns:RequestSOAPHeader xmlns:tns="http://www.huawei.com/schema/osg/common/v2_1">
                             <tns:spId>'.$spid.'</tns:spId>
                             <tns:spPassword>'.$this->spPassword.'</tns:spPassword>
                             <tns:serviceId>'.$service_id.'</tns:serviceId>
                             <tns:timeStamp>'.$timestamp.'</tns:timeStamp>
                          </tns:RequestSOAPHeader>
                        </soapenv:Header>
                        <soapenv:Body>
                            <req:RequestMsg><![CDATA[<?xml version="1.0" encoding="UTF-8"?>
                                        <request xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                                        <Transaction>
                                            <CommandID>OrgRevenueSettlement</CommandID>
                                            <LanguageCode></LanguageCode>
                                            <OriginatorConversationID>'.time().'-244w1a-ggz</OriginatorConversationID>
                                            <ConversationID></ConversationID>
                                            <Remark>0</Remark>
                                            <Parameters>
                                                <Parameter>
                                                    <Key>HeadOffice</Key>
                                                    <Value></Value>
                                                </Parameter>
                                            </Parameters>
                                            <ReferenceData>
                                                <ReferenceItem>
                                                    <Key>QueueTimeoutURL</Key>
                                                    <Value>'.$QueueTimeoutURL.'</Value>
                                                </ReferenceItem>
                                            </ReferenceData>
                                            <Timestamp>'.$request_time.'</Timestamp>
                                            </Transaction>
                                                <Identity>
                                                    <Caller>
                                                        <CallerType>2</CallerType>
                                                        <ThirdPartyID>broker_4</ThirdPartyID>
                                                        <Password>null</Password>
                                                        <CheckSum>null</CheckSum>
                                                        <ResultURL>https://23.239.27.43:4043/safaricom/result_urlorgsettlement</ResultURL>
                                                    </Caller>
                                                    <Initiator>
                                                        <IdentifierType>11</IdentifierType>
                                                        <Identifier>'.$username.'</Identifier>
                                                        <SecurityCredential>'.$encrypted_initiator_password.'</SecurityCredential>
                                                        <ShortCode>'.$shortcode.'</ShortCode>
                                                    </Initiator>
                                                    <PrimaryParty>
                                                        <IdentifierType>4</IdentifierType>
                                                        <Identifier>'.$shortcode.'</Identifier>
                                                        <ShortCode></ShortCode>
                                                    </PrimaryParty>
                                                    <ReceiverParty>
                                                        <IdentifierType>4</IdentifierType>
                                                        <Identifier>'.$shortcode.'</Identifier>
                                                        <ShortCode></ShortCode>
                                                    </ReceiverParty>
                                                    <AccessDevice>
                                                        <IdentifierType>4</IdentifierType>
                                                        <Identifier>1</Identifier>
                                                    </AccessDevice>
                                                </Identity>
                                                <KeyOwner>1</KeyOwner>
                                            </request>]]></req:RequestMsg>
                                           </soapenv:Body>
                                        </soapenv:Envelope>';
                $response = $this->curl->post_with_ssl($soap,$this->endpoint_url);
                header("Content-type: text/xml");
                print_r($response);
        }else{
            echo 'Paybill not found';
        }
    }

    function result_urlorgsettlement(){
        $file = file_get_contents('php://input');
        @mail('edwin.njoroge@digitalvision.co.ke','Organization Settlement Result URL',$file,$this->headers);
        if($file){
            if($_SERVER['SERVER_PORT'] == '4043'){
                file_put_contents("logs/organization_settlement_result_file.txt","\n".date("d-M-Y h:i A").$file,FILE_APPEND);
                header("Content-type: text/xml");
                echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                       <soapenv:Header/>
                       <soapenv:Body>
                          <req:ResponseMsg><![CDATA[<?xml version="1.0" encoding="UTF-8"?>
                    <response xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/response">
                        <ResponseCode>00000000</ResponseCode>
                        <ResponseDesc>success</ResponseDesc>
                    </response>]]></req:ResponseMsg>
                       </soapenv:Body>
                    </soapenv:Envelope>';
            }else{
                header("Content-type: text/xml");
                echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                       <soapenv:Header/>
                       <soapenv:Body>
                          <req:ResponseMsg><![CDATA[<?xml version="1.0" encoding="UTF-8"?>
                    <response xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/response">
                        <ResponseCode>10000202</ResponseCode>
                        <ResponseDesc>Wrong port '.$_SERVER['SERVER_PORT'].'</ResponseDesc>
                    </response>]]></req:ResponseMsg>
                       </soapenv:Body>
                    </soapenv:Envelope>';
            }
        }else{
            header("Content-type: text/xml");
            echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                   <soapenv:Header/>
                   <soapenv:Body>
                      <req:ResponseMsg><![CDATA[<?xml version="1.0" encoding="UTF-8"?>
                <response xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/response">
                    <ResponseCode>10000000</ResponseCode>
                    <ResponseDesc>NO file Sent</ResponseDesc>
                </response>]]></req:ResponseMsg>
                   </soapenv:Body>
                </soapenv:Envelope>';
        }
    }

    public function send_c2b_notifications(){
        $url = 'https://chamasoft.com/transaction_alerts/safaricom';
        $notifications = $this->safaricom_m->get_unsent_c2b_notifications();
        $i = 0 ;
        $j = 0;
        if($notifications){
            foreach ($notifications as $notification) {
                $notification_alert = array(
                        'currency' => $notification->currency,
                        'transaction_date' => $notification->transaction_date,
                        'transaction_id' => $notification->transaction_id,
                        'transaction_amount' => $notification->amount,
                        'transaction_type' => 'CR',
                        'transaction_narrative' => 'Phone number '.$notification->phone.', paid by '.$notification->customer_name,
                        'phone' => $notification->phone,
                        'transaction_channel' => 'MPESA',
                        'account_number' => $notification->reference_number,
                    );
                $data_file = json_encode($notification_alert);
                $response = $this->curl->post_json($data_file,$url);
                print_r($response);
                if($res = json_decode($response)){
                    if($res->responseCode == 0 || $res->responseCode == 1){
                        $this->safaricom_m->update_c2b_payment($notification->id,array('transaction_send_status'=>1));
                    }
                    ++$j;
                }
                ++$i;
            }
        }
        echo 'Entries '.$i.'<br/>';
        echo 'Records '.$j.'<br/>';
    }

    function update_transactions(){
        $notifications = $this->safaricom_m->get_all_c2b_requests();
        $i = 0;
        foreach ($notifications as $notification) {
           $this->safaricom_m->update_c2b_payment($notification->id,array('transaction_send_status'=>''));
           ++$i;
        }
        echo $i;
    }

    function checkidentity_request(){
        @ini_set('memory_limit','500M');
        error_reporting(1);
        $request_server = array(
                    'server'=>array(
                        'request_url' => $_SERVER['HTTP_REFERER'],
                        'request_type' => $_SERVER['REQUEST_METHOD'],
                        'request_server_ip' => $_SERVER['REMOTE_ADDR'],
                        'request_server_ip' => $_SERVER['REMOTE_ADDR'],
                    )
                );
        $request_file = array();
        $request = file_get_contents('php://input');
        if($request){
            $request = json_decode($request);
            if($request){
                $request_file = $request;
                $phone = $request->customer->phone;
                $get_user_details = $request->customer->get_user_details;
                $message = $request->transaction->message;
                $shortcode = $request->transaction->shortcode;
                $callback_url = $request->result->callback_url?:'';
                $username = $request->credentials->username;
                $password = $request->credentials->password;
                if($this->ion_auth->login_to_api_request(trim($username),trim($password))){
                    if(valid_phone($phone)&&is_numeric($shortcode)){
                        $result = $this->_checkidentity_requests($shortcode,$phone,$callback_url,$message,$get_user_details);
                        if($result){
                            $result = json_decode($result);
                            $response = array(
                                'response'=>array(
                                    'status' => $result->response_code,
                                    'description'=>(string)$result->response_description?:$result->customer_message,
                                    'request_id' => (string)$result->request_id,
                                ),
                            );
                        }else{
                            $response = array(
                                'response'=>array(
                                        'status' => 1,
                                        'description'=>'System internal error. Try again later',
                                    ),
                            );
                        }
                    }else{
                        $response = array(
                            'response'=>array(
                                    'status' => 1,
                                    'description'=>'Some essential Parameters are missing',
                                ),
                        );
                    }
                }else{
                    $response = array(
                        'response'=>array(
                                'status' => 1,
                                'description'=>'Wrong credentials used',
                            ),
                    );
                }
            }else{
                $response = array(
                    'response'=>array(
                            'status' => 1,
                            'description'=>'The file format is not acceptable',
                        ),
                );
            }
        }else{
            $response = array(
                    'response'=>array(
                            'status' => 1,
                            'description'=>'There was no file sent',
                        ),
                );
        }
        echo json_encode($request_server+$response+array('request'=>$request_file),JSON_PRETTY_PRINT);
    }

    function _checkidentity_requests($shortcode=0,$phone_number=0,$request_callback_url='',$message='',$get_user_details=0){
        if($shortcode&&$phone_number){
            if(array_key_exists($shortcode, $this->paybills)){
                $passkey = $this->paybills[$shortcode]['passkey'];
                $timestamp = $this->paybills[$shortcode]['timestamp'];
                $base_password = base64_encode($shortcode.$passkey.$timestamp);
                $request_id = $this->safaricom_m->generate_checkidentity_request_id();
                $endpoint_url = 'http://196.201.214.172:15508/SharedResources/Services/MCOService_Stub.serviceagent/MerchantCheckOut_EP';
                $soap_action = '/SharedResources/Services/MCOService_Stub.serviceagent/MerchantCheckOutEndpoint1/ProcessCheckout';
                $callback_url = "http://23.239.27.43:80/safaricom/checkidentity_callback";
                $timeout_url = "http://23.239.27.43:80/safaricom/checkidentity_timeout_callback";
                $message = $message?:'Please confirm your MPesa Identity to link to Chamasoft.';
                $xml = '
                    <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:mer="http://merchantcheckout.safaricom.com/">
                       <soapenv:Header/>
                       <soapenv:Body>
                          <ns0:ProcessCheckout xmlns:ns0="http://merchantcheckout.safaricom.com/">
                             <ProcessCheckoutRequest>
                                <checkoutHeader>
                                   <BusinessShortCode>'.$shortcode.'</BusinessShortCode>
                                   <Password>'.$base_password.'</Password>
                                   <Timestamp>'.$timestamp.'</Timestamp>
                                </checkoutHeader>
                                <checkoutTransaction>
                                   <SourceApp>Chamasoft</SourceApp>
                                   <TransactionType>CheckIdentity</TransactionType>
                                   <MerchantRequestID>'.$request_id.'</MerchantRequestID>
                                   <PhoneNumber>'.$phone_number.'</PhoneNumber>
                                   <CallBackURL>'.$callback_url.'</CallBackURL>
                                   <ns0:Parameter>
                                      <ReferenceItem>
                                         <Key>QueueTimeoutURL</Key>
                                         <Value>'.$timeout_url.'</Value>
                                      </ReferenceItem>
                                      <ReferenceItem>
                                         <Key>ServiceID</Key>
                                         <Value>455555</Value>
                                      </ReferenceItem>
                                      <ReferenceItem>
                                         <Key>CustomerPrompt</Key>
                                         <Value>'.$message.'</Value>
                                      </ReferenceItem>
                                   </ns0:Parameter>
                                   <TransactionDesc>test</TransactionDesc>
                                </checkoutTransaction>
                             </ProcessCheckoutRequest>
                          </ns0:ProcessCheckout>
                       </soapenv:Body>
                    </soapenv:Envelope>
                ';
                $response = $this->curl->post_with_ssl($xml,$endpoint_url,$soap_action);
                $input = array(
                    'shortcode' => $shortcode,
                    'request_id' => $request_id,
                    'phone' => $phone_number,
                    'request_callback_url' => $request_callback_url,
                    'created_on' => time(),
                    'get_user_details' => $get_user_details?1:0,
                );
                if($response){
                   $input = array_merge($input,array(
                        'response_code' => $this->everything_in_tags($response,'ResponseCode'),
                        'response_description' => $this->everything_in_tags($response,'ResponseDesc'),
                        'checkout_request_id' => $this->everything_in_tags($response,'CheckoutRequestID'),
                        'customer_message' => $this->everything_in_tags($response,'CustomerMessage'),
                   ));
                }
                if($id = $this->safaricom_m->insert_checkidentity_request($input)){
                    return json_encode($input);
                }else{
                    $this->session->set_flashdata('error','Error occured saving request');
                    return FALSE;
                }
            }else{
                $this->session->set_flashdata('error','Invalid shortcode');
                return FALSE;
            }
        }else{
            $this->session->set_flashdata('error','Missing Parameters');
            return FALSE;
        }
    }

    function checkidentity_callback(){
        $file = file_get_contents('php://input');
        @mail('geoffrey.githaiga@digitalvision.co.ke','CheckIdentity callback VPN',$file,$this->headers);
        if($file){
            $file = str_replace('SOAP-ENV','soapenv', $file);
            $file = str_replace('ns0:','', $file);
            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $file);
            $body = new SimpleXMLElement($response);
            $callback = $body->soapenvBody->stkCallback;
            $request_id = trim($callback->MerchantRequestID);
            $checkout_request_id = trim($callback->CheckoutRequestID);
            if($request = $this->safaricom_m->get_checkidentity_request_by_request_id($request_id,$checkout_request_id)){
                $metadata = $callback->CallbackMetadata;
                $amount = 0;
                $phone = 0;
                $transaction_id = 0;
                $balance = 0;
                $transaction_date = 0;
                if($metadata){
                    for ($i=0; $i < 4; $i++) {
                        $value_data = $metadata->Item[$i];
                        $name = $value_data->Name;
                        $value = $value_data->Value;
                        if(preg_match('/Amount/', $name)){
                            $amount = trim($value);
                        }elseif (preg_match('/PhoneNumber/', $name)) {
                            $phone = trim($value);
                        }elseif (preg_match('/MpesaReceiptNumber/', $name)) {
                            $transaction_id = trim($value);
                        }elseif (preg_match('/Balance/', $name)) {
                            $balance = trim($value);
                        }elseif (preg_match('/TransactionDate/', $name)) {
                            $transaction_date = strtotime(trim($value))?:time();
                        }
                    }
                }
                $update = array(
                    'result_code' => trim($callback->ResultCode),
                    'result_description' => trim($callback->ResultDesc),
                    'amount' => currency($amount),
                    'transaction_id' => $transaction_id,
                    'balance' => $balance,
                    'modified_on' => time(),
                );
                if($this->safaricom_m->update_checkidentity_request($request->id,$update)){
                    $callback_request = $this->safaricom_m->get_check_identity_request($request->id);
                    if($callback_request){
                        $this->curl->post_json(json_encode($callback_request),$callback_request->request_callback_url);
                    }
                }else{
                    die('out');
                }
            }else{
                die('No checkout');
            }
        }else{
            die('out');
        }
    }

    function query_user_kyc_details(){
        @ini_set('memory_limit','500M');
        error_reporting(1);
        $request_server = array(
                    'server'=>array(
                        'request_url' => $_SERVER['HTTP_REFERER'],
                        'request_type' => $_SERVER['REQUEST_METHOD'],
                        'request_server_ip' => $_SERVER['REMOTE_ADDR'],
                        'request_server_ip' => $_SERVER['REMOTE_ADDR'],
                    )
                );
        $request_file = array();
        $request = file_get_contents('php://input');
        if($request){
            $request = json_decode($request);
            if($request){
                $request_file = $request;
                $phone = $request->customer->phone;
                $request_id = $request->transaction->request_id;
                $transaction_id = $request->transaction->transaction_id;
                $shortcode = $request->transaction->shortcode;
                $callback_url = $request->result->callback_url?:'';
                $username = $request->credentials->username;
                $password = $request->credentials->password;
                if($request_id){
                    $checkIdentity_request = $this->safaricom_m->get_checkidentity_request_by_request_id($request_id);
                    $transaction_id = $checkIdentity_request->transaction_id;
                }
                if($transaction_id){
                    if($this->ion_auth->login_to_api_request(trim($username),trim($password))){
                        if(valid_phone($phone)&&is_numeric($shortcode)){
                            $result = $this->_query_user_kyc_details($shortcode,$phone,$transaction_id,$callback_url);
                            if($result){
                                $result = json_decode($result);
                                $response = array(
                                    'response'=>array(
                                        'status' => $result->response_code,
                                        'description'=>(string)$result->response_description,
                                        'request_id' => (string)$result->request_id,
                                    ),
                                );
                            }else{
                                $response = array(
                                    'response'=>array(
                                            'status' => 1,
                                            'description'=>'System internal error. Try again later',
                                        ),
                                );
                            }
                        }else{
                            $response = array(
                                'response'=>array(
                                        'status' => 1,
                                        'description'=>'Some essential Parameters are missing',
                                    ),
                            );
                        }
                    }else{
                        $response = array(
                            'response'=>array(
                                    'status' => 1,
                                    'description'=>'Wrong credentials used',
                                ),
                        );
                    }
                }else{
                   $response = array(
                        'response'=>array(
                                'status' => 1,
                                'description'=>'Could not complete transaction. Try again later',
                            ),
                    ); 
                }
            }else{
                $response = array(
                    'response'=>array(
                            'status' => 1,
                            'description'=>'The file format is not acceptable',
                        ),
                );
            }
        }else{
            $response = array(
                    'response'=>array(
                            'status' => 1,
                            'description'=>'There was no file sent',
                        ),
                );
        }
        echo json_encode($request_server+$response+array('request'=>$request_file),JSON_PRETTY_PRINT);
    }

    function _query_user_kyc_details($shortcode=0,$phone_number=0,$mpesa_receipt_number='',$request_callback_url=''){
        if($shortcode && $phone_number&&$mpesa_receipt_number){
            if(array_key_exists($shortcode, $this->paybills)){
                $request_time = date('YmdHis',time());
                $phone = valid_phone($phone_number);
                $paybills_data = $this->paybills[$shortcode];
                $spid = $paybills_data['spId'];
                $service_id = $paybills_data['service_id'];
                $password = $paybills_data['password'];
                $timestamp = $paybills_data['timestamp'];
                $initiator_password = $paybills_data['initiator_password'];
                $spPassword = base64_encode(hash('sha256',$spid.$password.$timestamp));
                $OriginatorConversationID = $spid . "_Chamasoftkyc_" .$this->safaricom_m->generate_query_kyc_request_id();
                $username = $paybills_data['username'];
                $result_url = 'https://23.239.27.43:4043/safaricom/query_kyc_callback';
                $timeout_url = 'https://23.239.27.43:4043/safaricom/queuetimeout';
                $environment = $paybills_data['environment'];
                if(preg_match('/Test/', $environment)){
                    $test_environment = TRUE;
                    $encrypted_initiator_password = openssl_key_encrypt($initiator_password,FALSE);
                    $endpoint_url = 'http://196.201.214.136:8310/mminterface/request';
                    $phone = valid_phone('254796778039'); 
                }else{
                    $test_environment = FALSE;
                    $encrypted_initiator_password = openssl_key_encrypt($initiator_password);
                    $endpoint_url = 'https://196.201.214.137:18423/mminterface/request';
                }
                $xml = '
                    <soapenv:Envelope 
                        xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
                        xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                        <soapenv:Header>
                            <tns:RequestSOAPHeader
                                xmlns:tns="http://www.huawei.com/schema/osg/common/v2_1">
                                <tns:spId>'.$spid.'</tns:spId>
                                 <tns:spPassword>'.$spPassword.'</tns:spPassword>
                                 <tns:timeStamp>'.$timestamp.'</tns:timeStamp>
                                 <tns:serviceId>'.$service_id.'</tns:serviceId>
                            </tns:RequestSOAPHeader>
                        </soapenv:Header>
                        <soapenv:Body>
                            <req:RequestMsg>
                                <![CDATA[
                                <?xml version=\'1.0\' encoding=\'UTF-8\'?>          
                                <Request 
                                    xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                                    <Transaction>
                                        <CommandID>QueryCustomerKYC</CommandID>
                                        <LanguageCode>0</LanguageCode>
                                        <OriginatorConversationID>'.$OriginatorConversationID.'</OriginatorConversationID>
                                        <ConversationID></ConversationID>
                                        <Remark>0</Remark>
                                        <Parameters>
                                            <Parameter>
                                            </Parameter>
                                        </Parameters>
                                        <ReferenceData> 
                                            <ReferenceItem>
                                                <Key>QueueTimeoutURL</Key>
                                                <Value>'.$timeout_url.'</Value>
                                            </ReferenceItem>
                                            <ReferenceItem>
                                                <Key>Check Identity Receipt</Key>
                                                <Value>'.$mpesa_receipt_number.'</Value>
                                            </ReferenceItem>
                                        </ReferenceData>
                                        <Timestamp>'.$timestamp.'</Timestamp>
                                    </Transaction>
                                    <Identity>
                                        <Caller>
                                            <CallerType>2</CallerType>
                                            <ThirdPartyID>broker_4</ThirdPartyID>
                                            <Password>T50mhFnEwrPNy0BU0b+n+8Hwdb2LhsKG0KSPemuiXiZrcYoemz5vIl0uUzs1OSUPi5cumPF4djZuuIERNVA+znH85Iy2k+DQQtFRGTVKBWNZZpDjus9RE0BD7iuBFjiAzr5UNJcpeetSO0nmG7O9sfXJ/tBWCnRPRE8vWNzlrq0tBhFl1EtWvkBDY7Daj/MWeigkumOGwB0/GDvO0AsOJZtHuGeddGHEi/lb1oJxlCOKXts8ZxopnbuDN5sB4qD3P5QUxgTfE1KFHEeklvwWUcnNpuDz7q12k0yzYhsJEE4MyiVwjZVuo66TPQd4AjU+JDzEIAwG4IJx98dh5C4AOA==</Password>
                                            <ResultURL>'.$result_url.'</ResultURL>
                                        </Caller>
                                        <Initiator>
                                            <IdentifierType>14</IdentifierType>
                                            <Identifier>groups api operator</Identifier>
                                            <SecurityCredential>J7ilZz9nMqaP1e384BuSpIZM+TgF5wVUKTL/UWUhBoFmNPTXdiCnZYHhwEnOacGX5xv99FIpMIIb1L+lsYAH6sDiMWOd02gPU9hisKKWJzCxqJkT+0IFIB3vh/Vrd9smr94FrPEu5ZiLkkkesIAyXMUOU6QskIosnzgcASqdFhOjIdi+gyWKm9W9LKCmtG4H+R4FoMpWPZjSNJi7km+5yRmX1UK8yA+xg3lxI9bVu0HZ5ORXz7ZWbHO9EvrbiHNib4uajkij6q/5vFKqUMwQmM7JUn7mXuDA2BOiMlavu78ZSvbQ5Ws6mlD8e7R8rRPz6dVdj8KWPftJ6bH6PiqG2Q==</SecurityCredential>
                                        </Initiator>
                                        <ReceiverParty>
                                            <IdentifierType>1</IdentifierType>
                                            <Identifier>'.$phone_number.'</Identifier>
                                        </ReceiverParty>
                                        <AccessDevice>
                                            <IdentifierType>4</IdentifierType>
                                            <Identifier>1</Identifier>
                                        </AccessDevice>
                                    </Identity>
                                    <KeyOwner>1</KeyOwner>
                                </Request>]]>
                            </req:RequestMsg>
                        </soapenv:Body>
                    </soapenv:Envelope>
                ';
                if($response = $this->curl->post_with_ssl($xml,$endpoint_url)){
                    $response_object = xml_create_object($response);
                    $result = $response_object->soapenvBody->reqResponseMsg->response;
                    $input = array(
                        'request_id' => $OriginatorConversationID,
                        'shortcode' => $shortcode,
                        'phone' => $phone,
                        'user_id' => '',
                        'request_transaction_id' => $mpesa_receipt_number,
                        'request_callback_url' => $request_callback_url,
                        'response_code' => trim($result->ResponseCode),
                        'response_description' => trim($result->ResponseDesc),
                        'checkout_request_id' => trim($result->ConversationID),
                        'created_on' => time(),
                    );
                    if($id = $this->safaricom_m->insert_query_kyc($input)){
                        return json_encode($this->safaricom_m->get_query_user_kyc($id));
                    }else{
                        $this->session->set_flashdata('error','Could not insert request');
                        return FALSE;
                    }
                }else{
                    $this->session->set_flashdata('error','No response');
                    return FALSE;
                }
            }else{
                $this->session->set_flashdata('error','Invalid shortcode');
                return FALSE;
            }
        }else{
            $this->session->set_flashdata('error','Missing Parameters');
            return FALSE;
        }
    }

    function query_kyc_callback(){
        $file = file_get_contents('php://input');
        if($file){
            $file = str_replace("@attributes",'attributes',$file);
            $body = xml_create_object($file);
            $result = $body->soapenvBody->resResultMsg->Result;
            if($result) {
                $request_id = $result->OriginatorConversationID;
                $checkout_request_id = $result->ConversationID;
                if($request = $this->safaricom_m->get_kyc_query_request_by_request_id($request_id,$checkout_request_id)){
                    $result_params = isset($result->ResultParameters)?$result->ResultParameters:'';
                    $phone = 0;
                    $customet_type = '';
                    $identity_status = '';
                    $registration_date = '';
                    $registered_by = '';
                    $language = '';
                    $trust_level = '';
                    $charge_profile = '';
                    $identity_rule_profile = '';
                    $segments = '';
                    $first_name = '';
                    $last_name = '';
                    $surname = '';
                    $gender = '';
                    $date_of_birth = '';
                    $document_type = '';
                    $document_number = 0;
                    if($result_params){
                        for($i=0;$i<30;$i++){
                            $value_data = isset($result_params->ResultParameter[$i])?$result_params->ResultParameter[$i]:array();
                            if($value_data){
                                $name = $value_data->Key;
                                $value = $value_data->Value;
                                if(preg_match('/MSISDN/', $name)){
                                    $phone = trim($value);
                                }elseif (preg_match('/Customer Type/', $name)) {
                                    $customet_type = trim($value);
                                }elseif (preg_match('/Identity Status/', $name)) {
                                    $identity_status = trim($value);
                                }elseif (preg_match('/Registration Date/', $name)) {
                                    $registration_date = trim($value);
                                }elseif (preg_match('/Registered By/', $name)) {
                                    $registered_by = trim($value);
                                }elseif (preg_match('/Language/', $name)) {
                                    $language = trim($value);
                                }elseif (preg_match('/Trust Level/', $name)) {
                                    $trust_level = trim($value);
                                }elseif (preg_match('/Charge Profile/', $name)) {
                                    $charge_profile = trim($value);
                                }elseif (preg_match('/Identity Rule Profile/', $name)) {
                                    $identity_rule_profile = trim($value);
                                }elseif (preg_match('/Segments/', $name)) {
                                    $segments = trim($value);
                                }elseif (preg_match('/First Name/', $name)) {
                                    $first_name = strtoupper(trim($value));
                                }elseif (preg_match('/Last Name/', $name)) {
                                    $last_name = strtoupper(trim($value));
                                }elseif (preg_match('/Surname Name/', $name)) {
                                    $surname = strtoupper(trim($value));
                                }elseif (preg_match('/Gender/', $name)) {
                                    $gender = trim($value);
                                }elseif (preg_match('/DOB/', $name)) {
                                    $date_of_birth = trim($value);
                                }elseif (preg_match('/ID Details/', $name)) {
                                    $id_details = $value->Record;
                                    if($id_details){
                                        for($j=0;$j<5;$j++){
                                            $id_details_data = isset($id_details->KYC[$j])?$id_details->KYC[$j]:array();
                                            if($id_details_data){
                                                $name_detail = isset($id_details_data['FieldType'])?$id_details_data['FieldType']:'';
                                                $value_detail = isset($id_details_data['FieldValue'])?$id_details_data['FieldValue']:'';
                                                if(preg_match('/ID Type/', $name_detail)){
                                                    $document_type = trim($value_detail);
                                                }elseif(preg_match('/ID Number/', $name_detail)){
                                                    $document_number = trim($value_detail);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    $update = array(
                        'result_transaction_id' => (string)$result->TransactionID,
                        'result_type' => (string)$result->ResultType,
                        'result_code' => (string)$result->ResultCode,
                        'result_description' => (string)$result->ResultDesc,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'surname' => $surname,
                        'document_type' => $document_type,
                        'document_number' => $document_number,
                        'modified_on' => time(),
                    );
                    if($this->safaricom_m->update_kyc_query_user($request->id,$update)){
                        $querykycrequest = $this->safaricom_m->get_query_user_kyc($request->id);
                        if($querykycrequest){
                            @mail("geoffrey.githaiga@digitalvision.co.ke","KYC callback_url", json_encode($querykycrequest),$this->headers);
                            $this->curl->post_json(json_encode($querykycrequest),$querykycrequest->request_callback_url);
                        }
                    }else{
                        echo 'update failed';
                    }
                }else{
                    echo 'No request';
                }
            }else{
                echo "no result";
            }
        }else{
            echo "no file";
        }
    }

    function transaction_status_callback(){
        $file = file_get_contents('php://input');
        @mail('geoffrey.githaiga@digitalvision.co.ke','Transaction status callback VPN',$file,$this->headers);
    }

    function _stk_push_request($shortcode=0,$amount=0,$phone_number=0,$request_callback_url=''){
        if($shortcode&&$amount&&$phone_number){
            if(array_key_exists($shortcode, $this->paybills)){
                $passkey = $this->paybills[$shortcode]['passkey'];
                $timestamp = $this->paybills[$shortcode]['timestamp'];
                $base_password = base64_encode($shortcode.$passkey.$timestamp);
                $request_id = $this->safaricom_m->generate_stkpush_request_id();
                $callback_url = "http://23.239.27.43:80/safaricom/checkout_callback";
                $endpoint_url = 'http://196.201.214.172:15508/SharedResources/Services/MCOService_Stub.serviceagent/MerchantCheckOut_EP';
                $soap_action = '/SharedResources/Services/MCOService_Stub.serviceagent/MerchantCheckOutEndpoint1/ProcessCheckout';
                $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://merchantcheckout.safaricom.com/">
                            <soapenv:Header/>
                            <soapenv:Body>
                                <ns1:ProcessCheckout>
                                    <ProcessCheckoutRequest>
                                        <checkoutHeader>
                                            <BusinessShortCode>'.$shortcode.'</BusinessShortCode>
                                            <Password>'.$base_password.'</Password>
                                            <Timestamp>'.$timestamp.'</Timestamp>
                                        </checkoutHeader>
                                        <checkoutTransaction>
                                            <SourceApp>TEST</SourceApp>
                                            <TransactionType>CustomerPayBillOnline</TransactionType>
                                            <MerchantRequestID>'.$request_id.'</MerchantRequestID>
                                            <Amount>'.$amount.'</Amount>
                                            <PartyA>'.$phone_number.'</PartyA> <!-- debit party -->
                                            <PartyB>'.$shortcode.'</PartyB> <!-- credit party -->
                                            <PhoneNumber>'.$phone_number.'</PhoneNumber> <!-- same as debit party above -->
                                            <CallBackURL>'.$callback_url.'</CallBackURL>
                                            <ns1:Parameter>
                                                <ReferenceItem>
                                                    <Key>AccountReference</Key>
                                                    <Value>'.$phone_number.'</Value>
                                                </ReferenceItem>
                                                <ReferenceItem>
                                                    <Key>Shortcode</Key>
                                                    <Value>'.$shortcode.'</Value>
                                                </ReferenceItem>
                                                <ReferenceItem>
                                                    <Key>MerchantName</Key>
                                                    <Value>Test C2B</Value>
                                                </ReferenceItem>
                                            </ns1:Parameter>
                                            <TransactionDesc>CustomerBuyGoodsOnline-  '.$phone_number.' - '.$amount.'</TransactionDesc>
                                        </checkoutTransaction>
                                    </ProcessCheckoutRequest>
                                </ns1:ProcessCheckout>
                            </soapenv:Body>
                        </soapenv:Envelope>';
                $response = $this->curl->post_with_ssl($xml,$endpoint_url,$soap_action);
                $input = array(
                    'shortcode' => $shortcode,
                    'request_id' => $request_id,
                    'amount' => $amount,
                    'phone' => $phone_number,
                    'request_callback_url' => $request_callback_url,
                    'created_on' => time(),
                );
                if($response){
                   $input = array_merge($input,array(
                        'response_code' => $this->everything_in_tags($response,'ResponseCode'),
                        'response_description' => $this->everything_in_tags($response,'ResponseDesc'),
                        'checkout_request_id' => $this->everything_in_tags($response,'CheckoutRequestID'),
                        'customer_message' => $this->everything_in_tags($response,'CustomerMessage'),
                   ));
                }
                if($id = $this->safaricom_m->insert_stk_push_request($input)){
                    return json_encode($input);
                }else{
                    $this->session->set_flashdata('error','Error occured saving request');
                    return FALSE;
                }
            }else{
                $this->session->set_flashdata('error','Invalid shortcode');
                return FALSE;
            }
        }else{
            $this->session->set_flashdata('error','Missing Parameters');
            return FALSE;
        }
    }

    function _stkpush_transaction_status_query($shortcode=0,$request_id = '',$checkout_request_id=''){
        if($shortcode&&$request_id&&$checkout_request_id){
            if(array_key_exists($shortcode, $this->paybills)){
                $passkey = $this->paybills[$shortcode]['passkey'];
                $timestamp = $this->paybills[$shortcode]['timestamp'];
                $base_password = base64_encode($shortcode.$passkey.$timestamp);
                $endpoint_url = 'http://196.201.214.172:15508/SharedResources/Services/MCOService_Stub.serviceagent/MerchantCheckOut_EP';
                $soap_action = '/SharedResources/Services/MCOService_Stub.serviceagent/MerchantCheckOutEndpoint1/QueryCheckOut';
                $soap = '
                    <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:mer="http://merchantcheckout.safaricom.com/">
                       <soapenv:Header/>
                       <soapenv:Body>
                          <mer:QueryCheckout>
                             <QueryCheckoutRequest>
                                <checkoutHeader>
                                       <BusinessShortCode>'.$shortcode.'</BusinessShortCode>
                                        <Password>'.$base_password.'</Password>
                                         <Timestamp>'.$timestamp.'</Timestamp>
                                  </checkoutHeader>
                                <queryTransaction>
                                   <mer:CheckoutRequestID>'.$checkout_request_id.'</mer:CheckoutRequestID>
                                   <mer:MerchantRequestID>'.$request_id.'</mer:MerchantRequestID>
                                </queryTransaction>
                             </QueryCheckoutRequest>
                          </mer:QueryCheckout>
                       </soapenv:Body>
                    </soapenv:Envelope>
                ';
                $response = $this->curl->post_with_ssl($soap,$endpoint_url,$soap_action);
                header("Content-type: text/xml");
                print_r($response);
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    function checkout_callback(){
        $file = file_get_contents('php://input');
        @mail('geoffrey.githaiga@digitalvision.co.ke','STK Push callback VPN',$file,$this->headers);
        if($file){
            $file = str_replace('SOAP-ENV','soapenv', $file);
            $file = str_replace('ns0:','', $file);
            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $file);
            $body = new SimpleXMLElement($response);
            $callback = $body->soapenvBody->stkCallback;
            if ($callback) {
                $request_id = trim($callback->MerchantRequestID);
                $checkout_request_id = trim($callback->CheckoutRequestID);
                if($request = $this->safaricom_m->get_stk_request_by_merchant_request_id_and_checkout_request_id($request_id,$checkout_request_id)){
                    $result_code = trim($callback->ResultCode);
                    $result_description = trim($callback->ResultDesc);
                    $amount = '';
                    $phone= '';
                    $transaction_id= '';
                    $balance= '';
                    $transaction_date= '';
                    if($result_code == '0'){
                        $callback_metadatas = $callback->CallbackMetadata;
                        if($callback_metadatas){        
                            for ($i=0; $i < 4; $i++) { 
                                $value_data = $callback_metadatas->Item[$i];
                                $name = $value_data->Name;
                                $value = $value_data->Value;
                                if(preg_match('/Amount/', $name)){
                                    $amount = trim($value);
                                }elseif (preg_match('/PhoneNumber/', $name)) {
                                    $phone = trim($value);
                                }elseif (preg_match('/MpesaReceiptNumber/', $name)) {
                                    $transaction_id = trim($value);
                                }elseif (preg_match('/Balance/', $name)) {
                                    $balance = trim($value);
                                }elseif (preg_match('/TransactionDate/', $name)) {
                                    $transaction_date = strtotime(trim($value))?:time();
                                }
                            }
                        }
                    }
                    $update = array(
                        'result_code' => $result_code,
                        'result_description' => $result_description,
                        'transaction_id' => $transaction_id,
                        'organization_balance' => $balance,
                        'transaction_date' => $transaction_date,
                        'modified_on' => time(),
                    );
                    if($this->safaricom_m->update_stkpushrequest($request->id,$update)){
                        die('in');
                    }else{
                        die('out');
                    }
                }else{
                    die('No checkout');
                }
            }else{
                die('out');
            }
        }else{
            die('out');
        }
    }

    function service_pin(){
        $file = file_get_contents('php://input');
        $response = array();
        $request = array();
        if($file){
            @mail('geoffrey.githaiga@digitalvision.co.ke','Service pin call',$file,'From: Safaricom Files <notifications@chamasoft.com>' . "\r\n" .
                    'Reply-To: billing@chamasoft.com' . "\r\n".
                    'X-Mailer: PHP/' . phpversion());
            $request = json_decode($file);
            if($request){
                $identity = isset($request->authentication->identity)?$request->authentication->identity:'';
                $password = isset($request->authentication->password)?$request->authentication->password:'';
                $amount = isset($request->request->amount)?$request->request->amount:'20';
                $account_number = isset($request->request->account_number)?$request->request->account_number:'';
                $merchant_transaction_id = isset($request->request->transaction_id)?$request->request->transaction_id:'';
                $timestamp = isset($request->request->timestamp)?$request->request->timestamp:time();
                $phone = isset($request->request->phone)?$request->request->phone:'0728747061';
                if($this->ion_auth->login_to_api_request(trim($identity),trim($password))){
                    $passkey = '157c97e947a5c0cbb11426d98b37dc7e5675a618d1b518104bd74efb6df636e7';
                    $shortcode = '521609';
                    $timestamp = date('YmdHis',$timestamp);
                    $callback = "http://23.239.27.43/safaricom/checkout_successfull";

                    $base_password = base64_encode(hash('sha256',$shortcode.$passkey.$timestamp));

                    $endpoint_url = "https://safaricom.co.ke/mpesa_online/lnmo_checkout_server.php?wsdl";

                    $soap = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tns="tns:ns">
                                <soapenv:Header>
                                    <tns:CheckOutHeader>
                                        <MERCHANT_ID>'.$shortcode.'</MERCHANT_ID>
                                        <PASSWORD>'.$base_password.'</PASSWORD>
                                        <TIMESTAMP>'.$timestamp.'</TIMESTAMP>
                                    </tns:CheckOutHeader>
                                </soapenv:Header>
                                <soapenv:Body>
                                    <tns:processCheckOutRequest>
                                        <MERCHANT_TRANSACTION_ID>chama-'.$merchant_transaction_id.'</MERCHANT_TRANSACTION_ID>
                                        <REFERENCE_ID>'.$account_number.'</REFERENCE_ID>
                                        <AMOUNT>'.$amount.'</AMOUNT>
                                        <MSISDN>'.$phone.'</MSISDN>
                                        <ENC_PARAMS>
                                        </ENC_PARAMS>
                                        <CALL_BACK_URL>'.$callback.'</CALL_BACK_URL>
                                        <CALL_BACK_METHOD>xml</CALL_BACK_METHOD>
                                        <TIMESTAMP>'.$timestamp.'</TIMESTAMP>
                                    </tns:processCheckOutRequest>
                                </soapenv:Body>
                            </soapenv:Envelope>';

                    $result = $this->curl->post_with_ssl($soap,$endpoint_url);
                    print_r($result);die;
                    if($result){
                        $return_code = $this->everything_in_tags($result,'RETURN_CODE');
                        $transaction_id = $this->everything_in_tags($result,'TRX_ID');
                        if($return_code=='00'){
                            $confirmTransaction = '
                                <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tns="tns:ns"> <soapenv:Header>
                                        <tns:CheckOutHeader>
                                            <MERCHANT_ID>'.$shortcode.'</MERCHANT_ID>
                                            <PASSWORD>'.$base_password.'</PASSWORD>
                                            <TIMESTAMP>'.$timestamp.'</TIMESTAMP>
                                        </tns:CheckOutHeader>
                                    </soapenv:Header>
                                    <soapenv:Body>
                                        <tns:transactionConfirmRequest>
                                            <TRX_ID>'.$transaction_id.'</TRX_ID>
                                            <MERCHANT_TRANSACTION_ID>chama-'.$merchant_transaction_id.'</MERCHANT_TRANSACTION_ID>
                                        </tns:transactionConfirmRequest>
                                    </soapenv:Body>
                                </soapenv:Envelope>
                            ';
                            $confirmation_response = $this->curl->post_with_ssl($confirmTransaction,$endpoint_url);
                            if($confirmation_response){
                                $return_code = $this->everything_in_tags($result,'RETURN_CODE');
                                $transaction_id = $this->everything_in_tags($result,'TRX_ID');
                                if($return_code == '00'){
                                    $response = array(
                                        'status' => 1,
                                        'response' => 'Success',
                                        'time' => time(),
                                        'mpesa_transaction_id' => $transaction_id,
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'time' => time(),
                                        'mpesa_transaction_id' => $transaction_id,
                                        'response' => $this->everything_in_tags($confirmation_response,'DESCRIPTION'),
                                    );
                                }
                            }else{
                              $response = array(
                                    'status' => 0,
                                    'time' => time(),
                                    'mpesa_transaction_id' => $transaction_id,
                                    'response' => 'Transaction failed. Try again later',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'time' => time(),
                                'mpesa_transaction_id' => $transaction_id,
                                'response' => $this->everything_in_tags($result,'DESCRIPTION'),
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'time' => time(),
                            'mpesa_transaction_id' => '',
                            'response' => 'Service failed. Try again',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'time' => time(),
                        'mpesa_transaction_id' => '',
                        'response' => 'Authentication Failed',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'time' => time(),
                    'mpesa_transaction_id' => '',
                    'response' => 'Invalid file sent',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'time' => time(),
                'mpesa_transaction_id' => '',
                'response' => 'Empty file sent',
            );
        }
        echo json_encode(array('response'=>$response,'request'=>$request));
    }

    function service_pin_callback(){
        $file = file_get_contents('php://input');
        @mail('edwin.njoroge@digitalvision.co.ke','Service pin call back',$file,'From: Safaricom Files <notifications@chamasoft.com>' . "\r\n" .
                    'Reply-To: billing@chamasoft.com' . "\r\n".
                    'X-Mailer: PHP/' . phpversion());
    }

    function everything_in_tags($string='', $tagname=''){
        $pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
        preg_match($pattern, $string, $matches);
        return isset($matches[1])?$matches[1]:FALSE;
    }

    function api_authentication(){
        $url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
        $consumer_key = "Jc9XPOoIAShuAQWazaBftymWPq1ZJD7h";
        $consumer_secret = "duqavJvvSmXaJkdV";
        $credentials = base64_encode($consumer_key.':'.$consumer_secret);
        print_r($this->curl->authentication($credentials,$url));
    }

    function encryption(){
        $publicKey = '/home/admin/api.chamasoft.com/certificates/23.239.27.43.key';
        $publicCrt = "/home/admin/api.chamasoft.com/certificates/23.239.27.43.cer";
        $plaintext = "31784253";

        $fp=fopen($publicCrt,"r");
        $pub_key_string=fread($fp,8192);
        fclose($fp);
        openssl_public_encrypt($plaintext, $encrypted, $pub_key_string, OPENSSL_PKCS1_PADDING);
        return base64_encode($encrypted);
    }

    function initiate_payment(){
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer ACCESS_TOKEN')); //setting custom header


        $curl_post_data = array(
          //Fill in the request parameters with valid values
          'BusinessShortCode' => ' ',
          'Password' => ' ',
          'Timestamp' => ' ',
          'TransactionType' => 'CustomerPayBillOnline',
          'Amount"' => ' ',
          'PartyA' => ' ',
          'PartyB' => ' ',
          'PhoneNumber' => ' ',
          'CallBackURL' => 'https://ip_address:port/callback',
          'AccountReference' => ' ',
          'TransactionDesc' => ' '
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);
        print_r($curl_response);

        echo $curl_response;
    }


    function mpesa_credit_score(){
        header('Content-Type: application/json');
        $result = file_get_contents('php://input');
        $response = array();
        if($result){
            $data = json_decode($result);
            if($data){
                $phone = $data->MSISDN;
                if($phone){
                    $credit_score = $this->safaricom_m->get_user_credit_score($phone);
                    if($credit_score){
                        //print_r($credit_score);die;
                        $result = array_merge(
                            array(
                                "ResponseCode" => "4000",
                                "ResponseMsg" =>  "Success",
                                "ResponseRefID" => rand(1111,9999).'-'.rand(1111111,9999999).'-'.rand(1,9),
                                "errorMessage" => "User details not available",
                            ),
                            ((array)$credit_score)
                        );
                        $response = $result;
                    }else{
                        $response = array(
                            "ResponseRefID" => rand(1111,9999).'-'.rand(1111111,9999999).'-'.rand(1,9),
                            "errorCode" => "500.00.4004",
                            "errorMessage" => "User details not available",
                        );
                    }
                }else{
                    $response = array(
                        "ResponseRefID" => rand(1111,9999).'-'.rand(1111111,9999999).'-'.rand(1,9),
                        "errorCode" => "500.003.4004",
                        "errorMessage" => "Phone need to be supplied",
                    );
                }
            }else{
                $response = array(
                    "ResponseRefID" => rand(1111,9999).'-'.rand(1111111,9999999).'-'.rand(1,9),
                    "errorCode" => "500.003.4004",
                    "errorMessage" => "Submitted json is invalid",
                );
            }
        }else{
            $response = array(
                "ResponseRefID" => rand(1111,9999).'-'.rand(1111111,9999999).'-'.rand(1,9),
                "errorCode" => "500.003.4004",
                "errorMessage" => "Submitted data is invalid",
            );
        }
        echo json_encode($response);
    }





    /********************************************************************************************************/

    function queue_timeout_url_via_vpn(){
        $file = file_get_contents('php://input');
        @mail('edwin.njoroge@digitalvision.co.ke','queue timeout URL via VPN',$file,$this->headers);
        @mail('geoffrey.githaiga@digitalvision.co.ke','queue timeout URL via VPN',$file,$this->headers);
    }

    function b2b_result_url_via_vpn(){
        $file = file_get_contents('php://input');
        @mail('geoffrey.githaiga@digitalvision.co.ke','B2B result url via VPN',$file,$this->headers);
        @mail('edwin.njoroge@digitalvision.co.ke','B2B result url via VPN',$file,$this->headers);
    }

    function b2c_result_url_via_vpn(){
        $file = file_get_contents('php://input');
        @mail('geoffrey.githaiga@digitalvision.co.ke','B2C result url via VPN',$file,$this->headers);
        @mail('edwin.njoroge@digitalvision.co.ke','B2C result url via VPN',$file,$this->headers);
    }

    function reverse_result_url_via_vpn(){
        $file = file_get_contents('php://input');
        @mail('geoffrey.githaiga@digitalvision.co.ke','Payment Reversal via VPN',$file,$this->headers);
        @mail('edwin.njoroge@digitalvision.co.ke','Payment Reversal via VPN',$file,$this->headers);
        if($file){
            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $file);
            $body = new SimpleXMLElement($response);
            print_r($body);
        }else{
            die('out');
        }
    }

    

    function checkout_successfull(){
        $file = file_get_contents('php://input');
        @mail('geoffrey.githaiga@digitalvision.co.ke','checkout callback successful URL',$file,$this->headers);
        @mail('edwin.njoroge@digitalvision.co.ke','checkout callback successful URL',$file,$this->headers);
    }

    function checkout_failed(){
        $file = file_get_contents('php://input');
        @mail('geoffrey.githaiga@digitalvision.co.ke','checkout  callback Failed URL',$file,$this->headers);
        @mail('edwin.njoroge@digitalvision.co.ke','checkout  callback Failed URL',$file,$this->headers);
    }

    

    function checkidentity_timeout_callback(){
        $file = file_get_contents('php://input');
        @mail('geoffrey.githaiga@digitalvision.co.ke','checkidentity time out callback URL',$file,$this->headers);
        @mail('edwin.njoroge@digitalvision.co.ke','checkidentity time out callback URL',$file,$this->headers);
    }

    function stkpush_via_vpn($phone=0,$amount = 0){
        $shortcode = '546448';
        //$shortcode = '488776';
        $phone_number = valid_phone($phone)?:'254728747061';
        //$phone_number = '254728747061';
        $amount = $amount?:'100';
        if($response = $this->_stk_push_request($shortcode,$amount,$phone_number)){
            print_r($response);
        }else{
            die($this->session->flashdata('error'));
        }        
    }

    function stkpush_payment_status_query($request_id=0,$checkout_request_id=0){
        $shortcode = '707008';
        $request_id = $request_id?:'10000-0018';
        $checkout_request_id = $checkout_request_id?:'10000-0018';
        if($response = $this->_stkpush_transaction_status_query($shortcode,$request_id,$checkout_request_id)){
            print_r($response);
        }else{
            die($this->session->flashdata('error'));
        }    
    }



    function b2b_via_vpn(){
        $username = 'testapi';
        $sender_party = '707008';
        $receiver_party = '902004';
        $initiator_password = openssl_key_encrypt('Test##2018',FALSE);
        $spId = '107031';
        $password = 'Safaricom123!';
        $timestamp = '20171215122846';
        $service_id = '107031000';
        $command_id = 'BusinessToBusinessTransfer';
        $originator_conversation_id = rand(1111,9999).'-Chamasoft-'.$this->safaricom_m->generate_b2b_originator_conversation_id();
        $amount = '200';
        $request_time = date('YmdHis',time());
        $queue_timeout_url = 'https://23.239.27.43:4043/safaricom/queue_timeout_url_via_vpn';
        $result_url = 'https://23.239.27.43:4043/safaricom/b2b_result_url_via_vpn';
        $endpoint_url = 'http://196.201.214.136:8310/mminterface/request';
        $spPassword = base64_encode(hash('sha256',$spId.$password.$timestamp));
        $initiator_information ='
            <Initiator>
                <IdentifierType>11</IdentifierType>
                <Identifier>'.$username.'</Identifier>
                <SecurityCredential>'.$initiator_password.'</SecurityCredential>
                <ShortCode>'.$sender_party.'</ShortCode>
            </Initiator>';
            $soap = '
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                <soapenv:Header>
                  <tns:RequestSOAPHeader xmlns:tns="http://www.huawei.com/schema/osg/common/v2_1">
                     <tns:spId>'.$spId.'</tns:spId>
                     <tns:spPassword>'.$spPassword.'</tns:spPassword>
                     <tns:timeStamp>'.$timestamp.'</tns:timeStamp>
                     <tns:serviceId>'.$service_id.'</tns:serviceId>
                  </tns:RequestSOAPHeader>
                </soapenv:Header>
                <soapenv:Body>
                    <req:RequestMsg>
                        <![CDATA[
                            <?xml version=\'1.0\' encoding=\'UTF-8\'?>
                            <request xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                                <Transaction>
                                    <CommandID>'.$command_id.'</CommandID>
                                    <LanguageCode></LanguageCode>
                                    <OriginatorConversationID>'.$originator_conversation_id.'</OriginatorConversationID>
                                    <ConversationID></ConversationID>
                                    <Remark>0</Remark>
                                    <Parameters>
                                        <Parameter>
                                            <Key>Amount</Key>
                                            <Value>'.$amount.'</Value>
                                        </Parameter>
                                        <Parameter>
                                            <Key>AccountReference</Key>
                                            <Value>'.$amount.'</Value>
                                        </Parameter>
                                    </Parameters>
                                    <ReferenceData>
                                        <ReferenceItem>
                                                <Key>QueueTimeoutURL</Key>
                                                <Value>'.$queue_timeout_url.'</Value>
                                        </ReferenceItem>
                                    </ReferenceData>
                                    <Timestamp>'.$request_time.'</Timestamp>
                                </Transaction>
                                <Identity>
                                <Caller>
                                       <CallerType>2</CallerType>
                                        <ThirdPartyID>broker_4</ThirdPartyID>
                                        <Password> +JDzEIAwG4IJx98dh5C4AOA==</Password>
                                        <CheckSum>null</CheckSum>
                                        <ResultURL>'.$result_url.'</ResultURL>
                                </Caller>'.
                                $initiator_information
                                .'<PrimaryParty>
                                    <IdentifierType>4</IdentifierType>
                                    <Identifier>'.$sender_party.'</Identifier>
                                    <ShortCode></ShortCode>
                                </PrimaryParty>
                                <ReceiverParty>
                                    <IdentifierType>4</IdentifierType>
                                    <Identifier>'.$receiver_party.'</Identifier>
                                    <ShortCode></ShortCode>
                                </ReceiverParty>
                                <AccessDevice>
                                    <IdentifierType>4</IdentifierType>
                                    <Identifier>1</Identifier>
                                </AccessDevice></Identity>
                                <KeyOwner>1</KeyOwner>
                            </request>
                        ]]>
                    </req:RequestMsg>
                </soapenv:Body>
            </soapenv:Envelope>';
        $response = $this->curl->post_with_ssl($soap,$endpoint_url);
        header("Content-type: text/xml");
        print_r($response);
    }

    function b2c_via_vpn(){
        $username = 'testapi';
        $initiator_password = openssl_key_encrypt('Test##2018',FALSE);
        $spId = '107031';
        $password = 'Safaricom123!';
        $timestamp = '20171215122846';
        $service_id = '107031000';
        $command_id = 'BusinessToBusinessTransfer';
        $originator_conversation_id = rand(1111,9999).'-Chamasoft-'.$this->safaricom_m->generate_b2b_originator_conversation_id();
        $amount = '200';
        $request_time = date('YmdHis',time());
        $queue_timeout_url = 'https://23.239.27.43:4043/safaricom/queue_timeout_url_via_vpn';
        $b2c_result_url = 'https://23.239.27.43:4043/safaricom/b2c_result_url_via_vpn';
        $endpoint_url = 'http://196.201.214.136:8310/mminterface/request';
        $spPassword = base64_encode(hash('sha256',$spId.$password.$timestamp));
        $phone = valid_phone('254796778039');
        $shortcode = '707008';
        $soap = '
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                <soapenv:Header>
                    <tns:RequestSOAPHeader xmlns:tns="http://www.huawei.com/schema/osg/common/v2_1">
                        <tns:spId>'.$spId.'</tns:spId>
                        <tns:spPassword>'.$spPassword.'</tns:spPassword>
                        <tns:serviceId>'.$service_id.'</tns:serviceId>
                        <tns:timeStamp>'.$timestamp.'</tns:timeStamp>
                    </tns:RequestSOAPHeader>
               </soapenv:Header>
                <soapenv:Body>
                    <req:RequestMsg>
                        <![CDATA[
                            <?xml version="1.0" encoding="UTF-8"?>
                            <request xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                                <Transaction>
                                    <CommandID>PromotionPayment</CommandID>
                                    <LanguageCode>0</LanguageCode>
                                    <OriginatorConversationID>'.$originator_conversation_id.'</OriginatorConversationID>
                                    <ConversationID></ConversationID>
                                    <Remark>0</Remark>
                                    <Parameters><Parameter>
                                        <Key>Amount</Key>
                                        <Value>'.$amount.'</Value>
                                        </Parameter></Parameters>
                                        <ReferenceData>
                                            <ReferenceItem>
                                                <Key>QueueTimeoutURL</Key>
                                                <Value>'.$queue_timeout_url.'</Value>
                                            </ReferenceItem>
                                        </ReferenceData>
                                        <Timestamp>'.$request_time.'</Timestamp>
                                </Transaction>
                                <Identity>
                                    <Caller>
                                        <CallerType>2</CallerType>
                                        <ThirdPartyID></ThirdPartyID>
                                        <Password>Password0</Password>
                                        <CheckSum>CheckSum0</CheckSum>
                                        <ResultURL>'.$b2c_result_url.'</ResultURL>
                                    </Caller>
                                    <Initiator>
                                      <IdentifierType>11</IdentifierType>
                                        <Identifier>'.$username.'</Identifier>
                                        <SecurityCredential>'.$initiator_password.'</SecurityCredential>
                                      <ShortCode>'.$shortcode.'</ShortCode>
                                     </Initiator>
                                    <PrimaryParty>
                                        <IdentifierType>4</IdentifierType>
                                        <Identifier>'.$shortcode.'</Identifier>
                                        <ShortCode>'.$shortcode.'</ShortCode>
                                    </PrimaryParty>
                                    <ReceiverParty>
                                        <IdentifierType>1</IdentifierType>
                                        <Identifier>'.$phone.'</Identifier>
                                        <ShortCode>ShortCode1</ShortCode>
                                    </ReceiverParty>
                                    <AccessDevice>
                                        <IdentifierType>1</IdentifierType>
                                        <Identifier>Identifier3</Identifier>
                                    </AccessDevice>
                                </Identity>
                                <KeyOwner>1</KeyOwner>
                            </request>
                        ]]>
                    </req:RequestMsg>
                </soapenv:Body>
            </soapenv:Envelope>';
        $response = $this->curl->post_with_ssl($soap,$endpoint_url);
        header("Content-type: text/xml");
        print_r($response);
    }

    function checkindentity_via_vpn($phone = 0){
        //$phone_number = '254728747061';
        $phone_number = valid_phone($phone)?:'254796778039';
        $shortcode = '707008';
        if($response = $this->_checkidentity_requests($shortcode,$phone_number)){
            print_r($response);
        }else{
            die($this->session->flashdata('error'));
        }
    }

    function query_user_kyc_via_vpn($mpesa_receipt_number='',$phone_number=0){
        $phone_number = valid_phone()?:"254796778039";
        $shortcode = '707008';
        if($response = $this->_query_user_kyc_details($shortcode,$phone_number,$mpesa_receipt_number)){
            print_r($response);
        }else{
            die($this->session->flashdata('error'));
        }
    }

    function check_transaction_status($mpesa_receipt_number=''){
        $shortcode = '707008';
        $mpesa_receipt_number = $mpesa_receipt_number;
        if($response = $this->_transaction_status($shortcode,$mpesa_receipt_number)){
            header("Content-type: text/xml");
            print_r($response);
        }else{
            die($this->session->flashdata('error'));
        }
    }

    function c2b_payment_validation(){
        $file = file_get_contents('php://input');
        file_put_contents('logs/c2b_validation_file.txt',"\n".date("d-M-Y h:i A").$file,FILE_APPEND);
        @mail('geofrey.ongidi@digitalvision.co.ke','C2B Payment validation URL',$file,$this->headers);
        header("Content-type:text/xml");
        if ($file){
            $response =  json_decode($file);
            
            if($body){
                $transaction_id = trim($body->TransID);
                $reference_number = trim($body->BillRefNumber);
                $transaction_date = trim($body->TransTime);
                $transaction_amount = trim($body->TransAmount);
                $transaction_currency = 'KES';
                $transaction_type = trim($body->TransType);
                $transaction_particulars = 'MPESA Transaction';
                $phone = trim($body->MSISDN);
                $debit_account = trim($body->InvoiceNumber)?:trim($body->BillRefNumber);
                $customer_info = $body->KYCInfo;
                if($customer_info){
                    $customer_name = '';
                    foreach ($customer_info as $customer) {
                        $customer_name.=$customer->KYCValue.' ';
                    }
                }
                $debit_customer = $customer_name;
                if($transaction_id && $debit_account && $transaction_amount && $transaction_date && $transaction_currency){
                    $input_data = array(
                        'transaction_id' => $transaction_id,
                        'reference_number' => $reference_number,
                        'transaction_date' => $transaction_date,
                        'amount' => $transaction_amount,
                        'active' => 1,
                        'currency' => $transaction_currency,
                        'transaction_type' => $transaction_type,
                        'particulars' => $transaction_particulars,
                        'phone' => $phone,
                        'account' => $debit_account,
                        'customer_name' => $debit_customer,
                        'created_on' => time(),
                    );
                    echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment"> 
                        <soapenv:Header/> 
                            <soapenv:Body> 
                                <c2b:C2BPaymentValidationResult> 
                                    <ResultCode>0</ResultCode> 
                                    <ResultDesc>Service processing successful. Awaiting Confirmation</ResultDesc> 
                                    <ThirdPartyTransID>'.$transaction_id.'</ThirdPartyTransID> 
                                </c2b:C2BPaymentValidationResult> 
                            </soapenv:Body> 
                        </soapenv:Envelope>';
                }else{

                }
            }else{
                echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment"> 
                    <soapenv:Header/> 
                        <soapenv:Body> 
                            <c2b:C2BPaymentValidationResult> 
                                <ResultCode>1</ResultCode> 
                                <ResultDesc>Some parameter are missing</ResultDesc> 
                                <ThirdPartyTransID>123123</ThirdPartyTransID> 
                            </c2b:C2BPaymentValidationResult> 
                        </soapenv:Body> 
                    </soapenv:Envelope>';
            }
        }else{
            echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment"> 
                <soapenv:Header/> 
                    <soapenv:Body> 
                        <c2b:C2BPaymentValidationResult> 
                                <ResultCode>1</ResultCode> 
                                <ResultDesc>Empty File Submitted</ResultDesc> 
                            </c2b:C2BPaymentValidationResult> 
                    </soapenv:Body> 
                </soapenv:Envelope>';
        }
    }

    function c2b_payment_confirmation(){
        $file = file_get_contents('php://input');
        @mail('geoffrey.githaiga@digitalvision.co.ke','C2B Payment Confirmation URL',$file,$this->headers);
        header("Content-type:text/xml");
        if($file){
            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $file);
            $xml = new SimpleXMLElement($response);
            $body = $xml->soapenvBody->ns1C2BPaymentConfirmationRequest;
            if($body){
                $customer_info = $body->KYCInfo;
                if($customer_info){
                    $customer_name = '';
                    foreach ($customer_info as $customer) {
                        $customer_name.=$customer->KYCValue.' ';
                    }
                }
                $update = array(
                    'TransType' => trim($body->TransType),
                    'TransID' => trim($body->TransID),
                    'TransTime' => trim($body->TransTime),
                    'TransAmount' => trim($body->TransAmount),
                    'BusinessShortCode' =>trim($body->BusinessShortCode),
                    'BillRefNumber' => trim($body->BillRefNumber),
                    'OrgAccountBalance' => trim($body->OrgAccountBalance),
                    'ThirdPartyTransID' => trim($body->ThirdPartyTransID),
                    'MSISDN' => trim($body->MSISDN),
                    'customer_name' => $customer_name,
                );
                echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment">
                       <soapenv:Header/>
                       <soapenv:Body>
                          <c2b:C2BPaymentConfirmationResult>0 Success</c2b:C2BPaymentConfirmationResult>
                       </soapenv:Body>
                    </soapenv:Envelope>';
            }else{
                echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment"> 
                        <soapenv:Header/> 
                            <soapenv:Body> 
                                <c2b:C2BPaymentConfirmationResult>
                                    C2B Payment Confirmation decode error.
                                </c2b:C2BPaymentConfirmationResult> 
                            </soapenv:Body> 
                        </soapenv:Envelope>';
            }
        }else{
            echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment"> 
                    <soapenv:Header/> 
                        <soapenv:Body> 
                            <c2b:C2BPaymentConfirmationResult>
                                C2B Payment No files sent.
                            </c2b:C2BPaymentConfirmationResult> 
                        </soapenv:Body> 
                    </soapenv:Envelope>';
        }





        /*@mail('geoffrey.githaiga@digitalvision.co.ke','C2B Payment Confirmation URL',$file,$this->headers);
        @mail('edwin.njoroge@digitalvision.co.ke','C2B Payment Confirmation URL',$file,$this->headers);
        header("Content-type: text/xml");
        echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment">
               <soapenv:Header/>
               <soapenv:Body>
                  <c2b:C2BPaymentConfirmationResult>0 Success</c2b:C2BPaymentConfirmationResult>
               </soapenv:Body>
            </soapenv:Envelope>';*/
    }

    function c2b_register_url_via_vpn(){
        $validation_url = 'https://23.239.27.43:4043/safaricom/c2b_payment_validation';
        $confirmation_url = 'https://23.239.27.43:4043/safaricom/c2b_payment_confirmation';
        $spId = '107031';
        $password = 'Safaricom123!';
        $timestamp = '20171215122846';
        $service_id = '107031000';
        $spPassword = base64_encode(hash('sha256',$spId.$password.$timestamp));
        $shortcode = '707008';   
        $endpoint_url = 'http://196.201.214.136:8310/mminterface/registerURL';     
        $soap = '
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                <soapenv:Header>
                     <tns:RequestSOAPHeader xmlns:tns="http://www.huawei.com/schema/osg/common/v2_1">
                        <tns:spId>'.$spId.'</tns:spId>
                        <tns:spPassword>'.$spPassword.'</tns:spPassword>
                        <tns:timeStamp>'.$timestamp.'</tns:timeStamp>
                        <tns:serviceId>'.$service_id.'</tns:serviceId>
                    </tns:RequestSOAPHeader>
                </soapenv:Header>
                <soapenv:Body>
                    <req:RequestMsg>
                        <![CDATA[
                            <?xml version="1.0" encoding="UTF-8"?>
                            <request xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                                <Transaction>
                                    <CommandID>RegisterURL</CommandID>
                                    <OriginatorConversationID>Reg-266-1126</OriginatorConversationID>
                                    <Parameters>
                                        <Parameter>
                                            <Key>ResponseType</Key>
                                            <Value>Completed</Value>
                                        </Parameter>
                                    </Parameters>
                                     <ReferenceData>
                                        <ReferenceItem>
                                            <Key>ValidationURL</Key>
                                            <Value>'.$validation_url.'</Value>
                                        </ReferenceItem>
                                        <ReferenceItem>
                                            <Key>ConfirmationURL</Key>
                                            <Value>'.$confirmation_url.'</Value>
                                        </ReferenceItem>
                                    </ReferenceData>
                                </Transaction>
                                <Identity>
                                    <Caller>
                                        <CallerType>0</CallerType>
                                        <ThirdPartyID/>
                                        <Password/>
                                        <CheckSum/>
                                        <ResultURL/>
                                    </Caller>
                                    <Initiator>
                                        <IdentifierType>1</IdentifierType>
                                        <Identifier/>
                                        <SecurityCredential/>
                                        <ShortCode/>
                                    </Initiator>
                                    <PrimaryParty>
                                        <IdentifierType>1</IdentifierType>
                                        <Identifier/>
                                        <ShortCode>'.$shortcode.'</ShortCode>
                                    </PrimaryParty>
                                </Identity>
                                <KeyOwner>1</KeyOwner>
                            </request>
                        ]]>
                    </req:RequestMsg>
                </soapenv:Body>
            </soapenv:Envelope>';
        //$url = 'https://196.201.214.137:18423/mminterface/registerURL';
        $response = $this->curl->post_with_ssl($soap,$endpoint_url);
        header("Content-type: text/xml");
        print_r($response);
    }

    function reverse_payment_transaction_via_vpn($transaction_id=''){
        $username = 'testapi';
        $shortcode = '707008';
        $initiator_password = openssl_key_encrypt('Test##2018',FALSE);
        $spId = '107031';
        $password = 'Safaricom123!';
        $timestamp = '20171215122846';
        $service_id = '107031000';
        $command_id = 'BusinessToBusinessTransfer';
        $originator_conversation_id = rand(1111,9999).'-Chamasoft-'.$this->safaricom_m->generate_b2b_originator_conversation_id();
        $amount = '200';
        $request_time = date('YmdHis',time());
        $queue_timeout_url = 'https://23.239.27.43:4043/safaricom/queue_timeout_url_via_vpn';
        $result_url = 'https://23.239.27.43:4043/safaricom/reverse_result_url_via_vpn';
        $endpoint_url = 'http://196.201.214.136:8310/mminterface/request';
        $spPassword = base64_encode(hash('sha256',$spId.$password.$timestamp));
        $transaction_id = $transaction_id?:'MDQ86PS5NG';
        $soap = '
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                <soapenv:Header>
                    <tns:RequestSOAPHeader
                        xmlns:tns="http://www.huawei.com/schema/osg/common/v2_1">
                        <tns:spId>'.$spId.'</tns:spId>
                        <tns:spPassword>'.$spPassword.'</tns:spPassword>
                        <tns:timeStamp>'.$timestamp.'</tns:timeStamp>
                        <tns:serviceId>'.$service_id.'</tns:serviceId>
                    </tns:RequestSOAPHeader>
                </soapenv:Header>
                <soapenv:Body>
                    <req:RequestMsg>
                        <![CDATA[
                        <?xml version=\'1.0\' encoding=\'UTF-8\'?>
                        <Request 
                            xmlns="http://api-v1.gen.mm.vodafone.com/mminterface/request">
                            <Transaction>
                                <CommandID>TransactionReversal</CommandID>
                                <LanguageCode>0</LanguageCode>
                                <OriginatorConversationID>'.$originator_conversation_id.'</OriginatorConversationID>
                                <ConversationID></ConversationID>
                                <Remark>0</Remark>
                                <Parameters>
                                    <Parameter>
                                        <Key>OriginalTransactionID</Key>
                                        <Value>'.$transaction_id.'</Value>
                                    </Parameter>
                                </Parameters>
                                <ReferenceData>
                                    <ReferenceItem>
                                        <Key>QueueTimeoutURL</Key>
                                        <Value>'.$queue_timeout_url.'</Value>
                                    </ReferenceItem>
                                </ReferenceData>
                                <Timestamp>2017-03-13T16:26:43.835Z</Timestamp>
                            </Transaction>
                            <Identity>
                                <Caller>
                                    <CallerType>2</CallerType>
                                    <ThirdPartyID>broker_4</ThirdPartyID>
                                    <Password>T50mhFnEwrPNy0BU0b+n+8Hwdb2LhsKG0KSPemuiXiZrcYoemz5vIl0uUzs1OSUPi5cumPF4djZuuIERNVA+znH85Iy2k+DQQtFRGTVKBWNZZpDjus9RE0BD7iuBFjiAzr5UNJcpeetSO0nmG7O9sfXJ/tBWCnRPRE8vWNzlrq0tBhFl1EtWvkBDY7Daj/MWeigkumOGwB0/GDvO0AsOJZtHuGeddGHEi/lb1oJxlCOKXts8ZxopnbuDN5sB4qD3P5QUxgTfE1KFHEeklvwWUcnNpuDz7q12k0yzYhsJEE4MyiVwjZVuo66TPQd4AjU+JDzEIAwG4IJx98dh5C4AOA==</Password>
                                    <ResultURL>'.$result_url.'</ResultURL>
                                </Caller>
                                <Initiator>
                                    <IdentifierType>11</IdentifierType>
                                    <Identifier>'.$username.'</Identifier>
                                    <SecurityCredential>'.$initiator_password.'</SecurityCredential>
                                    <ShortCode>'.$shortcode.'</ShortCode>
                                </Initiator>
                                <AccessDevice>
                                    <IdentifierType>1</IdentifierType>
                                    <Identifier>1</Identifier>
                                </AccessDevice>
                            </Identity>
                            <KeyOwner>1</KeyOwner>
                        </Request>
                        ]]>
                    </req:RequestMsg>
                </soapenv:Body>
            </soapenv:Envelope>
        ';
        $response = $this->curl->post_with_ssl($soap,$endpoint_url);
        header("Content-type: text/xml");
        print_r($response);
    }

    function generate_checkout_access_token(){
        $response = $this->curl->post_with_ssl();
        print_r($response);
        $this->curl->generate_checkout_access_token();
    }


    function c2b_validation(){
        $file = file_get_contents('php://input');
        @mail("geoffrey.githaiga@digitalvision.co.ke","C2b Validation",$file,$this->headers);
    }

    function c2b_confirmation(){
        $file = file_get_contents('php://input');
        @mail("geoffrey.githaiga@digitalvision.co.ke","C2b Confirmation",$file,$this->headers);
    }

    function base64encoder(){
        $timestamp = '20170901154023';
        $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $shortcode = '174379';
        $base_password = ($shortcode.$passkey.$timestamp);
        echo $base_password = base64_encode($base_password);
    }

    function check_if_valid_account(){ //check for valid accounts
        $file = file_get_contents('php://input');
        file_put_contents("logs/account_validation.dat",$file.' '.date('Y-m-d').'\n');
        $response = array();
        if($file){
            $result = json_decode($file);
            if($result){
                $reference_number = isset($result->BillRefNumber)?$result->BillRefNumber:'';
                $invoice_number = isset($result->InvoiceNumber)?$result->InvoiceNumber:'';
                $debit_account = $invoice_number?:$reference_number;
                if($debit_account){
                    $description = '';
                    $transaction_id = isset($result->TransID)?$result->TransID:'';
                    if(!$this->safaricom_m->is_transaction_dublicate($transaction_id)){
                        if(!(int) filter_var($debit_account, FILTER_SANITIZE_NUMBER_INT)){
                            $description = $debit_account;
                            $debit_account = $reference_number;
                        }
                        if($this->transactions->check_if_valid_account($debit_account)){
                            $transaction_time = isset($result->TransTime)?$result->TransTime:'';
                            $transaction_date = strtotime($transaction_time)?:time();
                            $transaction_amount = isset($result->TransAmount)?$result->TransAmount:'';
                            $transaction_currency = 'KES';
                            $transaction_type = isset($result->TransactionType)?$result->TransactionType:'';
                            $transaction_particulars = 'MPESA Transaction '.$description;
                            $phone = isset($result->MSISDN)?$result->MSISDN:'';
                            $shortcode = isset($result->BusinessShortCode)?$result->BusinessShortCode:'';
                            $first_name = isset($result->FirstName)?$result->FirstName:'';
                            $middle_name = isset($result->MiddleName)?$result->MiddleName:'';
                            $last_name = isset($result->LastName)?$result->LastName:'';
                            if( (date('Ymd',$transaction_date) > date('Ymd',time())) || (date('Ymd',$transaction_date) < date('Ymd',time()))){
                                $transaction_date = time();
                            }

                            if($phone){

                                $request_data = array(
                                    'hashedPhone' => $phone
                                );

                                $decode_phone = $this->curl->darajaRequests->decode_msisdn(json_encode($request_data));

                                if($decode_phone) {
                                    $phone = $decode_phone->phone;
                                }

                            }

                            if($transaction_id&&$transaction_date&&$shortcode&&$debit_account&&$phone){
                                $input_data = array(
                                    'transaction_id' => $transaction_id,
                                    'reference_number' => $reference_number,
                                    'transaction_date' => $transaction_date,
                                    'amount' => $transaction_amount,
                                    'active' => 1,
                                    'currency' => $transaction_currency,
                                    'transaction_type' => $transaction_type,
                                    'transaction_particulars' => $transaction_particulars,
                                    'phone' => $phone,
                                    'account' => $debit_account,
                                    'shortcode' => $shortcode,
                                    'customer_name' => $first_name.' '.$middle_name.' '.$last_name,
                                    'created_on' => time(),
                                );
                                if($id = $this->safaricom_m->insert_c2b($input_data)){
                                    $response = array(
                                        "ResultDesc" => "successful transaction_id ".$transaction_id,
                                        "ResultCode" => "0"
                                    );
                                }else{
                                    $response = array(
                                        "ResultDesc" => "Error occured",
                                        "ResultCode" => "1"
                                    );
                                }
                            }else{
                                $response = array(
                                    "ResultDesc" => "MIssing essential parameters",
                                    "ResultCode" => "1"
                                );
                            }
                        }else{
                            $response = array(
                                "ResultDesc" => "Account not recognized",
                                "ResultCode" => "1"
                            );
                        }
                    }else{
                        $response = array(
                            "ResultDesc" => "Duplicate transaction ID",
                            "ResultCode" => "1"
                        );
                    }
                }else{
                    $response = array(
                        "ResultDesc" => "Invalid account number",
                        "ResultCode" => "1"
                    );
                }
            }else{
                $response = array(
                    "ResultDesc" => "Result file sent : file format error",
                    "ResultCode" => "1"
                );
            }
        }else{
            $response = array(
                "ResultDesc" => "Empty File",
                "ResultCode" => "1"
            );
        }
        echo json_encode($response);
    }

    function record_direct_account_payment(){ // check record_direct_account_payment
        $response = array();
        $file = file_get_contents('php://input');
        file_put_contents('logs/c2b_confirmation_file.txt',"\n".date("d-M-Y h:i A").$file,FILE_APPEND);

        if($file){
            $result = json_decode($file);
            if($result){
                $transaction_id = isset($result->TransID)?$result->TransID:'';
                $organization_balance = isset($result->OrgAccountBalance)?$result->OrgAccountBalance:'';
                $transaction_type = isset($result->TransactionType)?$result->TransactionType:'';
                $transaction_date = isset($result->TransTime)?strtotime($result->TransTime):0;
                if( (date('Ymd',$transaction_date) > date('Ymd',time())) || (date('Ymd',$transaction_date) < date('Ymd',time()))){
                    $transaction_date = time();
                }
                $update_id = $this->safaricom_m->update_c2b_by_transaction_id($transaction_id,$organization_balance,$transaction_type,$transaction_date);
                if($update_id){

                    $response = array(
                        "ResultDesc" => "successful : ".$transaction_id,
                        "ResultCode" => "0"
                    );
                }else{
                    $response = array(
                        "ResultDesc" => "Unable to receive payment",
                        "ResultCode" => "1"
                    );
                }
            }else{
                $response = array(
                    "ResultDesc" => "Result file sent : file format error",
                    "ResultCode" => "1"
                );
            }
        }else{
            $response = array(
                "ResultDesc" => "Empty File",
                "ResultCode" => "1"
            );
        }
        file_put_contents('logs/c2b_confirmation_response_file.txt',"\n".date("d-M-Y h:i A").json_encode($response),FILE_APPEND);

        echo json_encode($response);
    }

    function record_direct_payment($id=0){
        print_r($this->transactions->record_direct_payment($id));

    }

    function test_stk_callback(){
        $request = $this->safaricom_m->get_stk_request(144);
        if($request){
            print_r($this->transactions->send_customer_callback($request)); 
        }
        
    }

    function record_stk_push_account_payment(){
        $file = file_get_contents('php://input');
        $response = array();
        if($file){
            $result = json_decode($file);
            if($result){
                $data_body = isset($result->Body)?$result->Body:'';
                if($data_body){
                    $callback = $data_body->stkCallback;
                    if($callback){
                        $merchant_request_id = $callback->MerchantRequestID;
                        $CheckoutRequestID = $callback->CheckoutRequestID;
                        if($request = $this->safaricom_m->get_stk_request_by_merchant_request_id_and_checkout_request_id($CheckoutRequestID,$merchant_request_id)){
                            $result_code = trim($callback->ResultCode);
                            $result_description = trim($callback->ResultDesc);
                            $amount = '';
                            $phone= '';
                            $transaction_id= '';
                            $balance= '';
                            $transaction_date= '';
                            if($result_code == '0'){
                                $callback_metadatas = $callback->CallbackMetadata;
                                if($callback_metadatas){        
                                    for ($i=0; $i < 4; $i++) { 
                                        $value_data = $callback_metadatas->Item[$i];
                                        $name = isset($value_data->Name)?$value_data->Name:'';
                                        $value = isset($value_data->Value)?$value_data->Value:'';
                                        if(preg_match('/Amount/', $name)){
                                            $amount = trim($value);
                                        }elseif (preg_match('/PhoneNumber/', $name)) {
                                            $phone = trim($value);
                                        }elseif (preg_match('/MpesaReceiptNumber/', $name)) {
                                            $transaction_id = trim($value);
                                        }elseif (preg_match('/Balance/', $name)) {
                                            $balance = trim($value);
                                        }elseif (preg_match('/TransactionDate/', $name)) {
                                            $transaction_date = strtotime(trim($value))?:time();
                                        }
                                    }
                                }
                            }
                            $update = array(
                                'result_code' => $result_code,
                                'result_description' => $result_description,
                                'transaction_id' => $transaction_id,
                                'organization_balance' => $balance,
                                'transaction_date' => $transaction_date,
                                'modified_on' => time(),
                            );
                            if($this->safaricom_m->update_stkpushrequest($request->id,$update)){
                                $update = array(
                                    'result_code' => $result_code,
                                    'result_description' => $result_description,
                                    'merchant_request_id' => $merchant_request_id,
                                    'checkout_request_id' => $CheckoutRequestID,
                                    'transaction_id' => $transaction_id,
                                    'status' => ($result_code=='0')?4:3,
                                    'transaction_date' => $transaction_date,
                                );
                                if($payment_transaction = $this->transactions_m->get_payment_transaction($request->reference_number,$request->account_id)){
                                     $this->transactions_m->update_payment($payment_transaction->id,$update);
                                }
                                $request = $this->safaricom_m->get_stk_request($request->id);
                                
                                if($result_code == '0'){
                                    if($this->transactions->record_transaction($request)){
                                        $response = array(
                                            "ResultDesc" => "successful: ".$transaction_id,
                                            "ResultCode" => "0"
                                        );
                                    }else{
                                        $response = array(
                                            "ResultDesc" => "Failed reconcillation",
                                            "ResultCode" => "1"
                                        );
                                    }
                                }
                                $this->transactions->send_customer_callback($request);
                            }else{
                                $response = array(
                                    "ResultDesc" => "Could not update payment",
                                    "ResultCode" => "1"
                                );
                            }
                        }else{
                            $response = array(
                                "ResultDesc" => "No Initial request",
                                "ResultCode" => "1"
                            );
                        }
                    }else{
                        $response = array(
                            "ResultDesc" => "Empty Callback",
                            "ResultCode" => "1"
                        );
                    }
                }else{
                    $response = array(
                        "ResultDesc" => "Empty Body",
                        "ResultCode" => "1"
                    );
                }
            }else{
                $response = array(
                    "ResultDesc" => "Result file sent : file format error",
                    "ResultCode" => "1"
                );
            }
        }else{
            $response = array(
                "ResultDesc" => "Empty File",
                "ResultCode" => "1"
            );
        }if($file){
            $this->curl->post($file,'https://api-test.chamasoft.com/safaricom/record_stk_push_account_payment');
        }
        echo json_encode($response);
    }


    function send_callback($id=0){
        $request = $this->safaricom_m->get_b2c_request($id);
        print_r($this->transactions->send_customer_disbursement_callback($request));die;
    }

    function record_disbursement(){
        $file = file_get_contents('php://input');
        file_put_contents("logs/b2c_disbursement.txt","\n".date("d-M-Y h:i A").$file,FILE_APPEND);
        $response = array();
        if($file){
            $result = json_decode($file);
            if($result){
                $result_body = isset($result->Result)?$result->Result:'';
                if($result_body){
                    $originator_conversation_id = isset($result_body->OriginatorConversationID)?$result_body->OriginatorConversationID:'';
                    $conversation_id = isset($result_body->ConversationID)?$result_body->ConversationID:'';
                    $transaction_id = isset($result_body->TransactionID)?$result_body->TransactionID:'';
                    $result_type = isset($result_body->ResultType)?$result_body->ResultType:'';
                    $result_code = isset($result_body->ResultCode)?$result_body->ResultCode:'';
                    $result_description = isset($result_body->ResultDesc)?$result_body->ResultDesc:'';

                    $update= array(
                        'callback_result_description'   =>  $result_description,
                        'callback_result_code'          =>  $result_code,
                        'transaction_id'                =>  $transaction_id,
                        'modified_on'                   =>  time(),
                    );
                    if($result_code == '0'){
                        $result_params = isset($result_body->ResultParameters)?$result_body->ResultParameters:'';
                        if($result_params){
                            $result_parameter = $result_params->ResultParameter;
                            $transaction_receipt = '';
                            $transaction_amount = '';
                            $b2c_charges_paid_account_available_funds = '';
                            $b2c_receipt_is_registered_customer = '';
                            $transaction_completed_time = '';
                            $receiver_party_public_name = '';
                            $b2c_working_account_available_funds = '';
                            $b2c_utility_account_available_funds = '';
                            for($i=0;$i<20;$i++){
                                $value_data = isset($result_parameter[$i])?$result_parameter[$i]:'';
                                if($value_data){
                                    $name = isset($value_data->Key)?$value_data->Key:'';
                                    $value = isset($value_data->Value)?$value_data->Value:'';
                                    if(preg_match('/TransactionAmount/', $name)){
                                        $transaction_amount = $value;
                                    }
                                    if(preg_match('/TransactionReceipt/', $name)){
                                        $transaction_receipt = $value;
                                    }
                                    if(preg_match('/B2CRecipientIsRegisteredCustomer/', $name)){
                                        $b2c_receipt_is_registered_customer = $value;
                                    }
                                    if(preg_match('/B2CChargesPaidAccountAvailableFunds/', $name)){
                                        $b2c_charges_paid_account_available_funds = $value;
                                    }
                                    if(preg_match('/ReceiverPartyPublicName/', $name)){
                                        $receiver_party_public_name = $value;
                                    }
                                    if(preg_match('/TransactionCompletedDateTime/', $name)){
                                        $transaction_completed_time = $value;
                                    }
                                    if(preg_match('/B2CUtilityAccountAvailableFunds/', $name)){
                                        $b2c_utility_account_available_funds = $value;
                                    }
                                    if(preg_match('/B2CWorkingAccountAvailableFunds/', $name)){
                                        $b2c_working_account_available_funds = $value;
                                    }
                                }
                            }
                            $update = $update+array(
                                'transaction_receipt'           =>  $transaction_receipt,
                                'transaction_amount'            =>  $transaction_amount,
                                'b2c_charges_paid_account_available_funds' =>  $b2c_charges_paid_account_available_funds,
                                'b2c_receipt_is_registered_customer' =>  $b2c_receipt_is_registered_customer,
                                'transaction_completed_time'    =>  strtotime($transaction_completed_time),
                                'receiver_party_public_name'    =>  $receiver_party_public_name,
                                'b2c_working_account_available_funds'    =>  $b2c_working_account_available_funds,
                                'b2c_utility_account_available_funds'    =>  $b2c_utility_account_available_funds,
                            );
                        }
                    }
                    if($request = $this->safaricom_m->get_b2c_request_by_originator_conversation_id($originator_conversation_id)){
                        if($update_id = $this->safaricom_m->update_b2c_request($request->id,$update)){
                            $request = $this->safaricom_m->get_b2c_request($request->id);
                            if($this->transactions->reconcile_account_disbursement($request)){
                                $response = array(
                                    "ResultDesc" => "success",
                                    "ResultCode" => "0"
                                );
                                print_r($this->transactions->send_customer_disbursement_callback($request));die;
                            }else{
                                $response = array(
                                    "ResultDesc" => "Result file sent : ".$this->session->flashdata('error'),
                                    "ResultCode" => "1"
                                );  
                            }
                        }else{
                            $response = array(
                                "ResultDesc" => "Result file sent : Transaction not recorded",
                                "ResultCode" => "1"
                            );  
                        }
                    }else{
                        $response = array(
                            "ResultDesc" => "Result file sent : Transaction not found",
                            "ResultCode" => "1"
                        );  
                    }
                }else{
                    $response = array(
                        "ResultDesc" => "Result file sent : Result body not found",
                        "ResultCode" => "1"
                    );  
                }           
            }else{
                $response = array(
                    "ResultDesc" => "Result file sent : file format error",
                    "ResultCode" => "1"
                );
            }
        }else{
            $response = array(
                "ResultDesc" => "Empty File",
                "ResultCode" => "1"
            );
        }
        echo json_encode($response);
    }


    function record_transaction_reversal(){
        $file = file_get_contents('php://input');
        $response = array();
        if($file){
            @mail("geoffrey.githaiga@digitalvision.co.ke","Reversal",$file,$this->headers);
            $result = json_decode($file);
            if($result){
                if(isset($result->Result)){
                    $result = $result->Result;
                    $result_description = isset($result->ResultDesc)?$result->ResultDesc:'';
                    $result_code = isset($result->ResultCode)?$result->ResultCode:'';
                    if($result_code=='0'){
                        $transaction_id = isset($result->TransactionID)?$result->TransactionID:'';
                        $ResultParameters = isset($result->ResultParameters)?$result->ResultParameters:'';
                        if($ResultParameters){
                            $ResultParameter = isset($ResultParameters->ResultParameter)?$ResultParameters->ResultParameter:'';
                            if($ResultParameter){
                                $debit_account_balance = '';
                                $amount = '';
                                $transaction_completed_time = '';
                                $original_transaction_id = '';
                                $charge = '';
                                $credit_public_party_name = '';
                                $debit_public_party_name = '';
                                for($i=0;$i<20;$i++){
                                    $value_data = isset($ResultParameter[$i])?$ResultParameter[$i]:'';
                                    if($value_data){
                                        $name = isset($value_data->Key)?$value_data->Key:'';
                                        $value = isset($value_data->Value)?$value_data->Value:'';
                                        if(preg_match('/DebitAccountBalance/', $name)){
                                            $debit_account_balance = $value;
                                        }
                                        if(preg_match('/Amount/', $name)){
                                            $amount = $value;
                                        }
                                        if(preg_match('/TransCompletedTime/', $name)){
                                            $transaction_completed_time = $value;
                                        }
                                        if(preg_match('/OriginalTransactionID/', $name)){
                                            $original_transaction_id = $value;
                                        }
                                        if(preg_match('/Charge/', $name)){
                                            $charge = $value;
                                        }
                                        if(preg_match('/CreditPartyPublicName/', $name)){
                                            $credit_public_party_name = $value;
                                        }
                                        if(preg_match('/DebitPartyPublicName/', $name)){
                                            $debit_public_party_name = $value;
                                        }
                                    }
                                }
                                if($original_transaction_id){
                                    $reversal = new StdClass();
                                    $reversal->transaction_id  = $transaction_id;
                                    $reversal->debit_account_balance  = $debit_account_balance;
                                    $reversal->amount  = $amount;
                                    $reversal->transaction_completed_time  = $transaction_completed_time;
                                    $reversal->original_transaction_id  = $original_transaction_id;
                                    $reversal->charge  = $charge;
                                    $reversal->credit_public_party_name  = $credit_public_party_name;
                                    $reversal->debit_public_party_name  = $debit_public_party_name;
                                    $c2b_payment_request = $this->safaricom_m->get_c2b_payment_by_transaaction_id($original_transaction_id);
                                    $stk_push_payment_request = $this->safaricom_m->get_stk_payment_by_transaaction_id($original_transaction_id);
                                    if($c2b_payment_request){
                                        if($this->transactions->record_payment_transaction_reversal($c2b_payment_request,$stk_push_payment_request,$reversal)){
                                            $response = array(
                                                "ResultDesc" => "successful",
                                                "ResultCode" => "0"
                                            );
                                        }else{
                                            $response = array(
                                                "ResultDesc" => "Reversal: Reversal failed :".$this->session->flashdata('error'),
                                                "ResultCode" => "1"
                                            );
                                        }
                                    }else{
                                        $response = array(
                                            "ResultDesc" => "Reversal: No available payment request",
                                            "ResultCode" => "1"
                                        );
                                    }
                                }else{
                                    $response = array(
                                        "ResultDesc" => "Reversal: No available original_transaction_id",
                                        "ResultCode" => "1"
                                    );
                                }
                            }else{
                                $response = array(
                                    "ResultDesc" => "Result file sent : No result parameter body",
                                    "ResultCode" => "1"
                                );
                            }
                        }else{
                            $response = array(
                                "ResultDesc" => "Result file sent : No result parameters",
                                "ResultCode" => "1"
                            );
                        }
                    }else{
                        $response = array(
                            "ResultDesc" => "Result file sent : ".$result_description,
                            "ResultCode" => "1"
                        );
                    }
                }else{
                    $response = array(
                        "ResultDesc" => "Result file sent : Result body not found",
                        "ResultCode" => "1"
                    );
                }
            }else{
                $response = array(
                    "ResultDesc" => "Result file sent : file format error",
                    "ResultCode" => "1"
                );
            }
        }else{
            $response = array(
                "ResultDesc" => "Empty File",
                "ResultCode" => "1"
            );
        }
        echo json_encode($response);die;
    }

    function record_organization_settlement(){
        $file = file_get_contents('php://input');
        $response = array();
        if($file){
            $result = json_decode($file);
            if($result){
                $result_body = isset($result->Result)?$result->Result:'';
                if($result_body){
                    $originator_conversation_id = isset($result_body->OriginatorConversationID)?$result_body->OriginatorConversationID:'';
                    $conversation_id = isset($result_body->ConversationID)?$result_body->ConversationID:'';
                    $transaction_id = isset($result_body->TransactionID)?$result_body->TransactionID:'';
                    $result_type = isset($result_body->ResultType)?$result_body->ResultType:'';
                    $result_code = isset($result_body->ResultCode)?$result_body->ResultCode:'';
                    $result_description = isset($result_body->ResultDesc)?$result_body->ResultDesc:'';
                    $update = array(
                        'result_type' => $result_type,
                        'result_code' => $result_code,
                        'result_description' => $result_description,
                        'transaction_id' => $transaction_id,
                        'modified_on' =>  time(),
                    );
                    if($result_code == '0'){
                        $result_params = isset($result_body->ResultParameters)?$result_body->ResultParameters:'';
                        if($result_params){
                            $result_parameter = $result_params->ResultParameter;
                            $debit_account_balance = '';
                            $initiator_account_current_balance = '';
                            $debit_account_current_balance = '';
                            $transaction_amount = '';
                            $transaction_completed_time = '';
                            $debit_party_charges = '';
                            $credit_party_public_name = '';
                            $debit_party_public_name = '';
                            $credit_account_balance = '';
                            $currency = '';
                            for($i=0;$i<20;$i++){
                                $value_data = isset($result_parameter[$i])?$result_parameter[$i]:'';
                                if($value_data){
                                    $name = isset($value_data->Key)?$value_data->Key:'';
                                    $value = isset($value_data->Value)?$value_data->Value:'';
                                    if(preg_match('/DebitAccountBalance/', $name)){
                                        $debit_account_balance = $value;
                                    }
                                    if(preg_match('/InitiatorAccountCurrentBalance/', $name)){
                                        $initiator_account_current_balance = $value;
                                    }
                                    if(preg_match('/DebitAccountCurrentBalance/', $name)){
                                        $debit_account_current_balance = $value;
                                    }
                                    if(preg_match('/Amount/', $name)){
                                        $transaction_amount = $value;
                                    }
                                    if(preg_match('/TransCompletedTime/', $name)){
                                        $transaction_completed_time = $value;
                                    }
                                    if(preg_match('/DebitPartyCharges/', $name)){
                                        $debit_party_charges = $value;
                                    }
                                    if(preg_match('/CreditPartyPublicName/', $name)){
                                        $credit_party_public_name = $value;
                                    }
                                    if(preg_match('/DebitPartyPublicName/', $name)){
                                        $debit_party_public_name = $value;
                                    }
                                    if(preg_match('/CreditAccountBalance/', $name)){
                                        $credit_account_balance = $value;
                                    }
                                    if(preg_match('/Currency/', $name)){
                                        $currency = $value;
                                    }
                                }
                            }
                            $update = $update+ array(
                                'debit_account_balance' => $debit_account_balance,
                                'initiator_account_current_balance' => $initiator_account_current_balance,
                                'debit_account_current_balance' => $debit_account_current_balance,
                                'amount' => currency($transaction_amount),
                                'transaction_completed_time' => strtotime($transaction_completed_time),
                                'debit_party_charges' => $debit_party_charges,
                                'receiver_party_public_name' => $credit_party_public_name,
                                'sender_party_public_name' => $debit_party_public_name,
                                'credit_account_balance' => $credit_account_balance,
                                'currency' => $currency,
                            );
                        }
                    }
                    if($request = $this->safaricom_m->get_b2b_transaction_by_originator_conversation_id($originator_conversation_id)){
                        if($update_id = $this->safaricom_m->update_b2b_transactions($request->id,$update)){
                            $request = $this->safaricom_m->get_b2b_transaction($request->id);
                            if($this->transactions->reconcile_account_disbursement($request,2)){
                                $response = array(
                                    "ResultDesc" => "success",
                                    "ResultCode" => "0"
                                );
                                //print_r($this->transactions->send_customer_disbursement_callback($request));die;
                            }else{
                                $response = array(
                                    "ResultDesc" => "Result file sent : ".$this->session->flashdata('error'),
                                    "ResultCode" => "1"
                                );  
                            }
                            // $response = array(
                            //     "ResultDesc" => "success",
                            //     "ResultCode" => "0"
                            // );
                        }else{
                            $response = array(
                                "ResultDesc" => "Result file sent : Transaction not recorded",
                                "ResultCode" => "1"
                            );  
                        }
                    }else{
                        $response = array(
                            "ResultDesc" => "Result file sent : Transaction not found",
                            "ResultCode" => "1"
                        );  
                    }
                }else{
                    $response = array(
                        "ResultDesc" => "Result file sent : Result body not found",
                        "ResultCode" => "1"
                    );  
                }           
            }else{
                $response = array(
                    "ResultDesc" => "Result file sent : file format error",
                    "ResultCode" => "1"
                );
            }
        }else{
            $response = array(
                "ResultDesc" => "Empty File",
                "ResultCode" => "1"
            );
        }
        echo json_encode($response);
    }

    function reverse_test(){
        $this->transactions->reverse_test('1000','MJ572GB8LT');
    }

    function get_account_charges(){
        $this->transactions->get_account_charges(150);
    }

    function reconcile_charges($id=0){
        $request = $this->safaricom_m->get_stk_request($id);                      
        if($request->result_code == '0'){
            $charge = $request->charge;
            if($charge){
                $this->transactions->reconcile_charges_public($charge,$request,1,'',1);
                echo 'done';
            }else{

            }
        }else{
        }
        echo $this->transactions->send_customer_callback($request);
    }


    function reconcile_payments_pending_result(){
        $requests = $this->safaricom_m->get_all_stk_push_requests_pending_results();
        foreach ($requests as $request) {
            $reference_number = $request->reference_number;
            $amount = $request->amount;
            $account_id = $request->account_id;
            $response = $this->transactions->get_transaction_status($reference_number,$amount,$account_id);
            print_r($response);
            echo $request->request_id.'<br/>';
        }
        $this->disburse_funds_transaction_status();
    }

    function disburse_funds_transaction_status(){
        $requests = $this->safaricom_m->get_all_b2c_payments_requests_pending_results();
        foreach ($requests as $request) {
            $reference_number = $request->reference_number;
            $amount = $request->amount;
            $account_id = $request->account_id;
            $shortcode = $request->paybill;
            $phone = $request->phone;
            $originator_conversation_id = $request->originator_conversation_id;
            $response = $this->transactions->disburse_funds_transaction_status($reference_number,$amount,$account_id,$shortcode,$phone,$originator_conversation_id);
            print_r($response);
            echo $request->originator_conversation_id.'<br/>';
        }
    }

    function reverse(){
        $shortcode = 967600;
        if(array_key_exists($shortcode, $this->transactions->paybills)){
            $paybills_data = $this->transactions->paybills[$shortcode];
            $username = $paybills_data['username'];
            $initiator_password = $paybills_data['initiator_password'];
            $encypted_initiator_password = openssl_key_encrypt($initiator_password,FALSE,TRUE);
            $post_data = json_encode(array(
                "Initiator" => $username,
                "SecurityCredential" => $encypted_initiator_password,
                "CommandID" => "TransactionReversal",
                "TransactionID" =>  'ODB1FF3X73',
                "Amount" => '696',
                "ReceiverParty" => $shortcode,
                "RecieverIdentifierType" => "11",
                "ResultURL" =>  "https://chamasoft.com:443/transaction_alerts/daraja_funds_reversal_callback",
                "QueueTimeOutURL" =>  "https://chamasoft.com:443/transaction_alerts/daraja_funds_reversal_callback",
                "Remarks" =>  "Reverse transaction",
                "Occasion" => ""
            ));
            $url = 'https://api.safaricom.co.ke/mpesa/reversal/v1/request';
            if($status_query = $this->curl->darajaRequests->process_request($post_data,$url,$shortcode)){
                print_r($status_query);
            }
        }
    }

    function check_payment_status(){
        $requests =  $this->safaricom_m->get_uncomplete_payments();
        foreach ($requests as $key => $request) {
            if($result = $this->transactions->get_transaction_status($request->reference_number,$request->amount,$request->account_id)){
                // echo $key.' ---->';
                // print_r($result);
            }else{
                //echo $this->session->flashdata('error');
            }

        }
    }


    function reconcile_transaction(){
        $this->safaricom_m->update_stkpushrequest(2793,array(
            'response_code'=>0,
            'transaction_id'=>'OHL6TPDBN0',
            'checkout_request_id'=>'ws_CO_220820202245586837',
            'merchant_request_id'=>'20313-691832222-1',
            'result_code' => 0,
            'result_description' => 'successful',
            'transaction_date' => strtotime('21-08-2020 , 10:10 PM'),
            'organization_balance' => 0,
            'modified_on' => time(),
        ));

        $payment_input = array(
            'account_id' => '1988',
            'reference_number' => '1598039117',
            'phone_number' => '254711376111',
            'amount' => currency(288),
            'type' => 1,
            'channel' => 1,
            'status' => 4,
            'active' => 1,
            'response_code' => 0,
            'response_description' => 'successful',
            'transaction_date' => time(),
            'shortcode' => '546448',
            'merchant_request_id' => '20313-691832222-1',
            'transaction_id' => 'OHL6TPDBN0',
            'narration' => '',
            'checkout_request_id' => 'ws_CO_220820202245586837',
        );
        $this->transactions_m->insert_payment($payment_input);
    }

    function dateTest(){
        echo $date = '20210204165715';

        echo '<br/>';
        echo $strtotime = strtotime($date);
        echo '<br/>';
        echo date('d-m-Y H:i:s',$strtotime);
    }

    function update_transaction_failed($id=0){
        $id or die("no way through");
        $this->safaricom_m->update_pending_transactions_to_failed($id);
    }

    function count_hashed_phone_numbers(){
        $total_rows = $this->safaricom_m->count_hashed_c2b_payments();
        print_r("the total rows is ");
        print_r($total_rows);
        die;
    }

    function decode_hashed_phone_numbers(){
		// pagination flow.

		// get the count.
		$total_rows = $this->safaricom_m->count_hashed_c2b_payments();
		$step_size = 10;
		$pagination = create_pagination('transactions/decode_hashed_phone_numbers/',$total_rows,$step_size,3,TRUE);
		$posts = $this->safaricom_m->limit($pagination['limit'])->get_hashed_c2b_payments();

		if(count($posts) > 0){
            foreach ($posts as $post) {
                if($post->phone && substr($post->phone,0,3) !== '254'){
                    // send request to the phone hash service.
                    $payload = array(
                        "hashedPhone" => $post->phone
                    );
                    $unhashed_result = $this->curl->darajaRequests->decode_msisdn(json_encode($payload));
                    if($unhashed_result && $unhashed_result->phone){
                        $update = array(
                            'phone' => $unhashed_result->phone
                        );
                        $result = $this->safaricom_m->update_c2b_payment($post->id,$update);
                        if($result){
                            echo "Success ".$post->id;
                        }else{
                            echo "Failure ".$post->id;
                        }
                    }else{
                        echo "No result for unhashed ".$post->id." value is ".$post->phone."<br/>";
                    }
                }else{
                    echo "Skip post ".$post->id."<br/>";
                }
           }
        }

        if($pagination){
            $total = $pagination['total'];
            $current_page = $pagination['current_page'];
            $next_page = ($current_page+$step_size);
            if($next_page<$total){
                $url = site_url('safaricom/decode_hashed_phone_numbers/'.($next_page));
                echo '
                    <script>
                        window.location = "'.$url.'";
                    </script>

                ';
                redirect($url);
            }
        }

        print_r($pagination);
        echo count($posts);

	}
    
    function update_b2c_request_to_failed($id=0){
        $id OR die("no way through");
        $b2c_request = $this->safaricom_m->get_b2c_request($id);
        if($b2c_request){
            if($post->phone="254721892568"){
                $update = array(
                    'callback_result_code' => '1',
                    'callback_result_description' => 'admin cancelled b2c request'
                );
                if($this->safaricom_m->update_b2c_request($id,$update)){
                    die('all good');
                }else{
                    die('error updating');
                }
            }else{
                die("bad request");
            }
        }else{
            die("no such request exists");
        }
    }
}
