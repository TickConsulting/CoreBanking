<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Ipn extends CI_Controller
{
    /* IPN DEPOSITORs ID***
    *   Will be used for each depositor id
    *   1. Equity
    *   2. MPESA 
    *
    /****/

    public $ipn_depositors = array(
            1 => 'Equity',
            2 => 'MPESA',
            3 => 'MTN UGANDA'
        );

    public $headers = '';


    protected $list_to_notify = array(
            'geoffrey.githaiga@digitalvision.co.ke',
            'edwin.njoroge@digitalvision.co.ke',
            'samuel.wahome@digitalvision.co.ke',
            'lucy.muthoni@digitalvision.co.ke',
            'accounts@digitalvision.co.ke',
            'edwin.kapkei@digitalvision.co.ke',
            'lois.nduku@digitalvision.co.ke',
            'accounts@chamasoft.com',
        );

    protected $error_mailing_list = array(
            'geoffrey.githaiga@digitalvision.co.ke',
            'edwin.njoroge@digitalvision.co.ke',
            'samuel.wahome@digitalvision.co.ke',

        );

    protected $error_coding_status;

    function __construct()
    {
        parent::__construct();
        $this->load->model('ipn_m');
        $this->load->model('emails/emails_m');
        $this->load->library('billing_settings');
        $this->load->library('curl');
        $this->load->library('billing/billing_m');
        $this->load->library('curl');

        $this->error_coding_status = $this->billing_settings->ipn_status;
        $this->headers = 'From: Chamasoft Notifications - <notifications@chamasoft.com>' . "\r\n" .
                    'Reply-To: billing@chamasoft.com' . "\r\n".
                    'X-Mailer: PHP/' . phpversion();
    }

    function record_stk_push_payment(){
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
                        if($request = $this->ipn_m->get_stk_request_by_merchant_request_id_and_checkout_request_id($CheckoutRequestID,$merchant_request_id)){
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
                                'request_reconcilled' => 1,
                            );
                            if($this->ipn_m->update_stkpushrequest($request->id,$update)){
                                $request = $this->ipn_m->get_stk_request($request->id);
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
        }
        echo json_encode($response);
    }

    function check_if_valid_account(){
        $file = file_get_contents('php://input');
        $response = array();
        $ipn_depositor = 2;
        $forward_result = array();
        if($file){
            file_put_contents("logs/mpesa_validationlog.txt","\n".date("d-M-Y h:i A")."\n".$file,FILE_APPEND);
            $result = json_decode($file);
            if($result){
                $reference_number = isset($result->BillRefNumber)?$result->BillRefNumber:'';
                $invoice_number = isset($result->InvoiceNumber)?$result->InvoiceNumber:'';
                $debit_account = $invoice_number?:$reference_number;
                if($debit_account){
                    $transaction_id = isset($result->TransID)?$result->TransID:'';
                    if(!$this->ipn_m->is_transaction_dublicate($transaction_id,$ipn_depositor)){
                        if($this->billing_settings->is_account_number_recognized($debit_account,$ipn_depositor)){
                            $transaction_time = isset($result->TransTime)?$result->TransTime:'';
                            $transaction_date = strtotime($transaction_time);
                            $transaction_amount = isset($result->TransAmount)?$result->TransAmount:'';
                            $transaction_currency = 'KES';
                            $transaction_type = isset($result->TransactionType)?$result->TransactionType:'';
                            $transaction_particulars = 'MPESA Transaction';
                            $phone = isset($result->MSISDN)?$result->MSISDN:'';
                            $shortcode = isset($result->BusinessShortCode)?$result->BusinessShortCode:'';
                            $first_name = isset($result->FirstName)?$result->FirstName:'';
                            $middle_name = isset($result->MiddleName)?$result->MiddleName:'';
                            $last_name = isset($result->LastName)?$result->LastName:'';
                            if($transaction_id&&$transaction_date&&$shortcode&&$debit_account&&$phone){
                                $input_data = array(
                                    'ipn_depositor' => $ipn_depositor,
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
                                    'customer_name' => $first_name.' '.$middle_name.' '.$last_name,
                                    'created_on' => time(),
                                );
                                if($id = $this->ipn_m->insert($input_data)){
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
            // if(preg_match('/(chamasoft\.com)/',$_SERVER['HTTP_HOST'])){
            //     $forwarders = $this->billing_m->get_ipn_forwarders();
            //     if($forwarders){
            //         $result = FALSE;
            //         foreach ($forwarders as $validation_endpoint){
            //             $url = $validation_endpoint->mpesa_validation_end_point;
            //             $forward_result[$url] = $this->curl->post($file,$url)?:$this->session->flashdata('error');
            //         }
            //         //return $result;
            //     }
            // }
        }else{
            $response = array(
                "ResultDesc" => "Empty File",
                "ResultCode" => "1"
            );
        }
        header("Content-Type: application/json");
        $response = array_merge($response,array(
            'server' => $_SERVER['HTTP_HOST'],
            'forward_result' => $forward_result,
        ));
        echo json_encode($response);
    }

    /*function delete_transaction($transaction_id = 0){
        $this->ipn_m->delete_transaction($transaction_id,2);
    }*/

    function record_direct_account_payment(){
        $response = array();
        $forward_result = array();
        $file = file_get_contents('php://input');
        $ipn_depositor = 2;
        if($file){
            file_put_contents("logs/mpesa_confirmationlog.txt","\n".date("d-M-Y h:i A")."\n".$file,FILE_APPEND);
            $result = json_decode($file);
            if($result){
                $transaction_id = isset($result->TransID)?$result->TransID:'';
                if($this->ipn_m->is_transaction_dublicate($transaction_id,$ipn_depositor,7)){
                    $ipn_transaction = $this->ipn_m->get_ipn_transaction($transaction_id,$ipn_depositor);
                    $organization_balance = isset($result->OrgAccountBalance)?$result->OrgAccountBalance:'';
                    $transaction_type = isset($result->TransactionType)?$result->TransactionType:'';
                    $update = array(
                        'paybill_balance'=>$organization_balance,
                        'transaction_type' => $transaction_type,
                    );
                    $status = 0;
                    if($this->ipn_m->update($ipn_transaction->id,$update)){
                        //$this->cron_job_process_ipn_payments($transaction_id);
                        if(preg_match('/sms/',strtolower($ipn_transaction->account))){
                            $status = $this->_process_sms_payment($ipn_transaction->amount,$ipn_transaction->account,$ipn_transaction->transaction_id,$ipn_transaction->transaction_date,$ipn_transaction->customer_name,$ipn_transaction->phone,$ipn_transaction->ipn_depositor);
                            $process = 1;
                        }else{
                            $status = $this->_processs_billing_payment($ipn_transaction->amount,$ipn_transaction->account,$ipn_transaction->transaction_id,$ipn_transaction->transaction_date,$ipn_transaction->customer_name,$ipn_transaction->phone,$ipn_depositor);
                            $process = 2;
                        }
                        $this->ipn_m->update($ipn_transaction->id,array('status'=>$status)); 
                        $ipn_transaction = $this->ipn_m->get_ipn_transaction($transaction_id,$ipn_depositor);
$message = 'Payment Details: <br/><br/>

Customer Name: '.$ipn_transaction->customer_name.' <br/><br/>

Account Number: '.$ipn_transaction->account.' <br/><br/>
                
Amount Paid: '.$ipn_transaction->currency.' '.$ipn_transaction->amount.' <br/><br/>
                
Customer Number: '.$ipn_transaction->phone.' <br/><br/>
                
Payment Message: '.$ipn_transaction->transaction_id.' Confirmed. on '.timestamp_to_date_and_time($ipn_transaction->transaction_date).' '.$ipn_transaction->currency.' '.$ipn_transaction->amount.' received from '.$ipn_transaction->customer_name.' '.$ipn_transaction->phone.'.  Account Number '.$ipn_transaction->account;
if($ipn_transaction->paybill_balance){
    $message.=' New Utility balance is '.$ipn_transaction->currency.'. '.number_to_currency($ipn_transaction->paybill_balance);
}
                        $this->_send_report($status,$process,$message,$ipn_transaction->ipn_depositor);
                        $response = array(
                            "ResultDesc" => "successful : ".$transaction_id,
                            "ResultCode" => "0"
                        );

                        if(preg_match('/(chamasoft\.com)/',$_SERVER['HTTP_HOST'])){
                            $ipn_forwarder_input = array(
                                'ipn_id' => $ipn_transaction->id,
                                'forward_status' => '0',
                                'created_on' => time(),
                            );
                            $this->ipn_m->insert_ipn_forward_alert($ipn_forwarder_input);
                        }
                    }else{
                       $response = array(
                            "ResultDesc" => "Cound not reconcile transaction",
                            "ResultCode" => "1"
                        ); 
                    }
                }else{
                    $response = array(
                        "ResultDesc" => "Cound not reconcile transaction - Duplicate payment",
                        "ResultCode" => "1"
                    ); 
                }
            }else{
                $response = array(
                    "ResultDesc" => "Result file sent : file format error",
                    "ResultCode" => "1"
                );
            }
            
            // if(preg_match('/(chamasoft\.com)/',$_SERVER['HTTP_HOST'])){
            //     $forwarders = $this->billing_m->get_ipn_forwarders();
            //     if($forwarders){
            //         $result = FALSE;
            //         foreach ($forwarders as $validation_endpoint){
            //             $url = $validation_endpoint->mpesa_confirmation_end_point;
            //             $forward_result[$url] = $this->curl->post($file,$url)?:$this->session->flashdata('error');
            //         }
            //         //return $result;
            //     }
            // }
        }else{
            $response = array(
                "ResultDesc" => "Empty File",
                "ResultCode" => "1"
            );
        }
        header("Content-Type: application/json");
        $response = array_merge($response,array(
            'server' => $_SERVER['HTTP_HOST'],
            'forward_result' => $forward_result,
        ));
        echo json_encode($response);
    }

    function mtn_receive_subscription_payment(){
        $file = file_get_contents('php://input');
        $response = array();
        if($file){
            $result = json_decode($file);
            if($result){
                $reference_number = isset($result->data->reference_number)?$result->data->reference_number:'';
                if($request = $this->ipn_m->get_stk_request_by_reference_number($reference_number)){
                    $status = $result->code;
                    $result_description = $result->description;
                    $transaction_id = isset($result->data->transaction_id)?$result->data->transaction_id:'';
                    if($status == '200'){
                        $result_code = '0';
                    }else{
                        $result_code = $status;
                    }
                    $update = array(
                        'result_code' => $result_code,
                        'result_description' => $result_description,
                        'transaction_id' => $transaction_id,
                        'organization_balance' => 0,
                        'transaction_date' => time(),
                        'modified_on' => time(),
                        'request_reconcilled' => 1,
                    );
                    if($this->ipn_m->update_stkpushrequest($request->id,$update)){
                        $request = $this->ipn_m->get_stk_request($request->id);
                        if($status == '200'){
                            $ipn_depositor = 4;
                            if(!$this->ipn_m->is_transaction_dublicate($transaction_id,$ipn_depositor)){
                                $debit_account = $request->reference_number;
                                $transaction_time = time();
                                $transaction_date = time();
                                $transaction_amount = $request->amount;
                                $transaction_currency = $request->currency;
                                $transaction_type = '';
                                $transaction_particulars = 'MTN Mobile Money Transaction';
                                $phone = $request->phone;
                                if($transaction_id&&$transaction_date&&$debit_account&&$phone){
                                    if($request->currency){
                                        $currency = $transaction_currency;
                                        $amount = convert_currency($transaction_amount,$request->currency,'KES');
                                    }else{
                                        $currency = 'UGX';
                                        $amount = currency($transaction_amount);
                                    }
                                    $input_data = array(
                                        'ipn_depositor' => $ipn_depositor,
                                        'transaction_id' => $transaction_id,
                                        'reference_number' => $reference_number,
                                        'transaction_date' => $transaction_date,
                                        'amount' => $transaction_amount,
                                        'active' => 1,
                                        'currency' => $currency,
                                        'transaction_type' => $transaction_type,
                                        'particulars' => $transaction_particulars,
                                        'phone' => $phone,
                                        'account' => $debit_account,
                                        'created_on' => time(),
                                    );
                                    if($id = $this->ipn_m->insert($input_data)){
                                        if(preg_match('/sms/',strtolower($debit_account))){
                                            $status = $this->_process_sms_payment($amount,$debit_account,$transaction_id,$transaction_date,'',$phone,$ipn_depositor);
                                            $process = 1;
                                        }else{
                                            $status = $this->_processs_billing_payment($amount,$debit_account,$transaction_id,$transaction_date,'',$phone,$ipn_depositor);
                                            $process = 2;
                                        }
$message = 'Payment Details: <br/><br/>

Customer Name: MTN CUSTOMER <br/><br/>

Account Number: '.$debit_account.' <br/><br/>
                
Amount Paid: '.$request->currency.' '.$transaction_amount.' <br/><br/>
                
Customer Number: '.$phone.' <br/><br/>
                
Payment Message: '.$transaction_id.' Confirmed. on '.timestamp_to_date_and_time($transaction_date).' '.$currency.' '.$transaction_amount.' received from MTN'.$phone.'.  Account Number '.$debit_account;
                                            $this->_send_report($status,$process,$message,$ipn_depositor);
                                            $response = array(
                                                "ResultDesc" => "successful : ".$transaction_id,
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
                                    "ResultDesc" => "Duplicate transaction ID",
                                    "ResultCode" => "1"
                                );
                            }
                        }else{
                            $response = array(
                                "ResultDesc" => "Died natural death",
                                "ResultCode" => "1"
                            );
                        }
                    }else{
                        $response = array(
                            "ResultDesc" => "Failed to update payment",
                            "ResultCode" => "1"
                        );
                    }
                }else{
                    $response = array(
                        "ResultDesc" => "Could not find request",
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


    function mpesa_validation(){
        if(isset($_REQUEST))
        {
           $get = $_GET;
           $post = $_POST;

           $data = file_get_contents('php://input');
           $mail = $data;
           $server_ip = $_SERVER['REMOTE_ADDR'];
           file_put_contents("logs/mpesa_validationlog.txt","\n".date("d-M-Y h:i A").serialize($_POST)."\n".serialize($_GET)."\n".serialize($_SERVER).$data.serialize($_REQUEST),FILE_APPEND);
           if($_SERVER['SERVER_PORT'] == '4043' || preg_match('/(45\.33\.18\.205)/',$_SERVER['REMOTE_ADDR']) || $_SERVER['SERVER_PORT'] == '80' || preg_match('/173\.255\.205\.7/',$_SERVER['REMOTE_ADDR']) || preg_match('/45\.33\.11\.77/',$_SERVER['REMOTE_ADDR'])){
                if($data){
                    $ipn_depositor = 2;
                    //decode the xml file to retrive contents
                    $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $data);
                    $xml = new SimpleXMLElement($response);
                    $body = $xml->soapenvBody->ns1C2BPaymentValidationRequest;
                    if($body->TransID && $body->TransAmount){
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
                            if(!$this->ipn_m->is_transaction_dublicate($transaction_id,$ipn_depositor)){
                                if($this->billing_settings->is_account_number_recognized($debit_account,$ipn_depositor)){  
                                    $transaction_date = strtotime($transaction_date);
                                    $input_data = array(
                                            'ipn_depositor' => $ipn_depositor,
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
                                    $id = $this->ipn_m->insert($input_data);
                                    if($id){
                                        $mail ='REMOTE_ADDR '.$server_ip.' Server Address '.$_SERVER['SERVER_ADDR'].' some files '.$transaction_id;
                                        header("Content-type: text/xml");
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
                                        $status = 5;
                                        $mail = ' REMOTE_ADDR '.$server_ip.' Server Address '.$_SERVER['SERVER_ADDR'].' Unable to decode';
                                        header("Content-type: text/xml");
                                        echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment"> 
                                            <soapenv:Header/> 
                                                <soapenv:Body> 
                                                    <c2b:C2BPaymentValidationResult> 
                                                        <ResultCode>1</ResultCode> 
                                                        <ResultDesc>Error processing the entry</ResultDesc> 
                                                        <ThirdPartyTransID>123123</ThirdPartyTransID> 
                                                    </c2b:C2BPaymentValidationResult> 
                                                </soapenv:Body> 
                                            </soapenv:Envelope>';
                                    }
                                }else{
                                    $mail = ' REMOTE_ADDR '.$server_ip.' Server Address '.$_SERVER['SERVER_ADDR'].' Account entered error '.$debit_account.' and Trans Id'.$transaction_id;
                                    header("Content-type: text/xml");
                                    echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment"> 
                                        <soapenv:Header/> 
                                            <soapenv:Body> 
                                                <c2b:C2BPaymentValidationResult> 
                                                    <ResultCode>1</ResultCode> 
                                                    <ResultDesc>Account Number Not Recognized</ResultDesc> 
                                                    <ThirdPartyTransID>'.$debit_account.'</ThirdPartyTransID> 
                                                </c2b:C2BPaymentValidationResult> 
                                            </soapenv:Body> 
                                        </soapenv:Envelope>';
                                }
                            }else{
                                $mail = ' REMOTE_ADDR '.$server_ip.' Server Address '.$_SERVER['SERVER_ADDR'].' Duplicate entry '.$transaction_id;
                                header("Content-type: text/xml");
                                echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment"> 
                                    <soapenv:Header/> 
                                        <soapenv:Body> 
                                            <c2b:C2BPaymentValidationResult> 
                                                <ResultCode>1</ResultCode> 
                                                <ResultDesc>Duplicate entry</ResultDesc> 
                                                <ThirdPartyTransID>'.$transaction_id.'</ThirdPartyTransID> 
                                            </c2b:C2BPaymentValidationResult> 
                                        </soapenv:Body> 
                                    </soapenv:Envelope>';
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
                            header("Content-type: text/xml");
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
                        $mail = ' REMOTE_ADDR '.$server_ip.' Server Address '.$_SERVER['SERVER_ADDR'].' Unable to decode';
                        header("Content-type: text/xml");
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
                    $mail = ' REMOTE_ADDR '.$server_ip.' Server Address '.$_SERVER['SERVER_ADDR'].' No files';
                    header("Content-type: text/xml");
                    echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment"> 
                            <soapenv:Header/> 
                                <soapenv:Body> 
                                    <c2b:C2BPaymentValidationResult> 
                                        <ResultCode>1</ResultCode> 
                                        <ResultDesc>Parameters missing</ResultDesc> 
                                    </c2b:C2BPaymentValidationResult> 
                                </soapenv:Body> 
                            </soapenv:Envelope>';
               }
                $forwarders = $this->billing_m->get_ipn_forwarders();
                if($forwarders){
                    $response = FALSE;
                    foreach ($forwarders as $validation_endpoint){
                       $result = $this->curl->curl_post_xml($data,$validation_endpoint->mpesa_validation_end_point);
                    }
                    return $response;
                }
            }else{
                $mail.= ' REMOTE_ADDR '.$server_ip.' Server Address '.$_SERVER['SERVER_ADDR'].' wrong port '.$_SERVER['SERVER_PORT'];
                header("Content-type: text/xml");
                echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment"> 
                        <soapenv:Header/> 
                            <soapenv:Body> 
                                c2b:C2BPaymentValidationResult> 
                                        <ResultCode>1</ResultCode> 
                                        <ResultDesc>Wrong port'.$_SERVER['SERVER_PORT'].' or Address '.$_SERVER['REMOTE_ADDR'].'</ResultDesc> 
                                    </c2b:C2BPaymentValidationResult> 
                            </soapenv:Body> 
                        </soapenv:Envelope>';
            }
            //use new email manager library
            //$this->pmailer->send_mail('geoffrey.githaiga@digitalvision.co.ke','MPESA Validation File',$mail,'notifications@chamasoft.com','','','','','Chamasoft Mpesa FIle');
        }else{
            file_put_contents("mpesa_validationlog.txt","\n".date("d-M-Y h:i A").serialize($_POST)."\n".serialize($_GET)."\n".serialize($_SERVER).$data.serialize($_REQUEST),FILE_APPEND);
             //use new email manager library
            //$this->pmailer->send_mail('geoffrey.githaiga@digitalvision.co.ke','MPESA Validation File','nothing','notifications@chamasoft.com','','','','','Chamasoft Mpesa FIle');
        }
    }

    function mpesa_confirmation(){
        if(isset($_REQUEST))
        {
           $get = $_GET;
           $post = $_POST;
           $data = file_get_contents('php://input');
           $file = array('data'=>$data,'request'=>$_REQUEST,'get'=>$get,'post'=>$post,'server'=>$_SERVER);
           $mail = serialize($data);
           $server_ip = $_SERVER['REMOTE_ADDR'];
           file_put_contents("logs/mpesa_confirmationlog.txt","\n".date("d-M-Y h:i A").serialize($_POST)."\n".serialize($_GET)."\n".serialize($_SERVER).$data.serialize($_REQUEST),FILE_APPEND);
           if($_SERVER['SERVER_PORT'] == '4043' || preg_match('/(45\.33\.18\.205)/',$_SERVER['REMOTE_ADDR']) || $_SERVER['SERVER_PORT'] == '80' || preg_match('/173\.255\.205\.7/',$_SERVER['REMOTE_ADDR']) || preg_match('/45\.33\.11\.77/',$_SERVER['REMOTE_ADDR'])){
                if($data){
                    $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $data);
                    $xml = new SimpleXMLElement($response);
                    $body = $xml->soapenvBody->ns1C2BPaymentConfirmationRequest;
                    if($body){
                        $ipn_depositor = 2;
                        $transaction_id = $body->TransID;
                        $this->cron_job_process_ipn_payments($transaction_id);
                        if($this->ipn_m->is_transaction_dublicate($transaction_id,$ipn_depositor,7)){
                            $ipn_id = $this->ipn_m->get_ipn_id($transaction_id,$ipn_depositor);
                            $update = array('paybill_balance'=>$body->OrgAccountBalance);
                            $this->ipn_m->update($ipn_id,$update);
                            $mail = $mail.' REMOTE_ADDR '.$server_ip.' Server Address '.$_SERVER['SERVER_ADDR'].' Mpesa Files'.serialize($data);
                            header("Content-type: text/xml");
                            echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment">
                                   <soapenv:Header/>
                                   <soapenv:Body>
                                      <c2b:C2BPaymentConfirmationResult>0 Success</c2b:C2BPaymentConfirmationResult>
                                   </soapenv:Body>
                                </soapenv:Envelope>';
                        }else{
                            $mail ='REMOTE_ADDR '.$server_ip.' Server Address '.$_SERVER['SERVER_ADDR'].' Not received'.serialize($data).' Transaction Id '.$transaction_id;
                            header("Content-type: text/xml");
                            echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment"> 
                                <soapenv:Header/> 
                                    <soapenv:Body> 
                                        <c2b:C2BPaymentConfirmationResult>
                                            C2B Payment not received.
                                        </c2b:C2BPaymentConfirmationResult> 
                                    </soapenv:Body> 
                                </soapenv:Envelope>';
                        }
                    }else{
                        $mail = $mail.' REMOTE_ADDR '.$server_ip.' Server Address '.$_SERVER['SERVER_ADDR'].' Error decoding'.serialize($data);
                        header("Content-type: text/xml");
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
                $mail = $mail.' REMOTE_ADDR '.$server_ip.' Server Address '.$_SERVER['SERVER_ADDR'].' No files';
                    header("Content-type: text/xml");
                    echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment"> 
                            <soapenv:Header/> 
                                <soapenv:Body> 
                                    <c2b:C2BPaymentConfirmationResult>
                                        C2B Payment parameters missing.
                                    </c2b:C2BPaymentConfirmationResult> 
                                </soapenv:Body> 
                            </soapenv:Envelope>';
               }

                $forwarders = $this->billing_m->get_ipn_forwarders();
                if($forwarders){
                    $response = FALSE;
                    foreach ($forwarders as $validation_endpoint){
                       $result = $this->curl->curl_post_xml($data,$validation_endpoint->mpesa_confirmation_end_point);
                    }
                    return $response;  
                }
           }else{
                $mail = $mail.' REMOTE_ADDR '.$server_ip.' Server Address '.$_SERVER['SERVER_ADDR'].' worng port '.$_SERVER['SERVER_PORT'];
                header("Content-type: text/xml");
                echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment"> 
                        <soapenv:Header/> 
                            <soapenv:Body> 
                                <c2b:C2BPaymentConfirmationResult>
                                    C2B Payment not accepted on port'.$_SERVER['SERVER_PORT'].'.
                                </c2b:C2BPaymentConfirmationResult> 
                            </soapenv:Body> 
                        </soapenv:Envelope>';
           }
           if($mail){   
                //$this->pmailer->send_mail('geoffrey.githaiga@digitalvision.co.ke','MPESA Confirmation File',$mail,'notifications@chamasoft.com','','','','','Chamasoft Mpesa FIle');
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
            file_put_contents("mpesa_confirmationlog.txt","\n".date("d-M-Y h:i A").serialize($_POST)."\n".serialize($_GET)."\n".serialize($_SERVER).$data.serialize($_REQUEST),FILE_APPEND);
            //$this->pmailer->send_mail('geoffrey.githaiga@digitalvision.co.ke','MPESA Confirmation File','nothing','notifications@chamasoft.com','','','','','Chamasoft Mpesa FIle');
        }
    }


    function register_mpesa_url(){
        $soap_file = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:req="http://api-v1.gen.mm.vodafone.com/mminterface/request">    
                    <soapenv:Header> 
                         <tns:RequestSOAPHeader xmlns:tns="http://www.huawei.com/schema/osg/common/v2_1">          
                            <tns:spId>100474</tns:spId>          
                            <tns:spPassword>YTU0NDM2MmQ2MTQwMGE3N2Q3Njg0YWU0YWJjMDZjMWRkYWJlM2YwMTlmOWQ5ZmUwZWVkMzYyZWNlNGQwMmQyYQ==</tns:spPassword> 
                            <tns:timeStamp>694911600</tns:timeStamp>
                            <tns:serviceId>100474000</tns:serviceId>       
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
                                        <Value>https://45.33.18.205:4043/ipn/mpesa_validation</Value>             
                                    </ReferenceItem> 
                                    <ReferenceItem>                 
                                        <Key>ConfirmationURL</Key>                 
                                        <Value>https://45.33.18.205:4043/ipn/mpesa_confirmation</Value>             
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
                                    <ShortCode>967600</ShortCode>         
                                </PrimaryParty>     
                            </Identity>     
                            <KeyOwner>1</KeyOwner> 
                            </request>]]>
                        </req:RequestMsg>    
                    </soapenv:Body> 
                </soapenv:Envelope>';

        $url = 'https://portal.safaricom.com/registerURL';

        $response = $this->_curl_post_xml($url,$soap_file);

        print_r($response);
    }

    function equity(){
        $password = $this->input->get_post('password');
        $username = $this->input->get_post('username');
        $status = '';
        $mail='Equity File '.date("d-M-Y h:i A").serialize($_REQUEST);
        file_put_contents("logs/equity-payment-ipn.txt","\n".date("d-M-Y h:i A").serialize($_SERVER).serialize($_REQUEST),FILE_APPEND);
        if(preg_match('/196\.216\.242\.171/',$_SERVER['REMOTE_ADDR']) || preg_match('/(45\.33\.18\.205)/',$_SERVER['REMOTE_ADDR']) || preg_match('/196\.216\.242\.163/',$_SERVER['REMOTE_ADDR']) || preg_match('/127\.0\.0\.1/',$_SERVER['REMOTE_ADDR']) || preg_match('/173\.255\.205\.7/',$_SERVER['REMOTE_ADDR']) || preg_match('/45\.33\.11\.77/',$_SERVER['REMOTE_ADDR'])){
            if($username=='equity-bank' && $password=='equitybankIPN')
            {
                if(isset($_REQUEST) && !empty($_REQUEST) && !empty($_REQUEST['tranid'])){
                    $ipn_depositor = 1;
                    $transaction_id = trim($this->input->get_post('tranid'));
                    $reference_number = trim($this->input->get_post('refNo'));
                    $transaction_date = trim($this->input->get_post('tranDate'));
                    $transaction_amount = trim($this->input->get_post('tranAmount'));
                    $transaction_currency = trim($this->input->get_post('tranCurrency'));
                    $transaction_type = trim($this->input->get_post('tranType'));
                    $transaction_particulars = str_replace('${','',str_replace('}','',trim($this->input->get_post('tranParticular'))));
                    $phone = str_replace('${','',str_replace('}','',trim($this->input->get_post('phonenumber'))));
                    $debit_account = str_replace('${','',str_replace('}','',trim($this->input->get_post('debitaccount'))));
                    $debit_customer = str_replace('${','',str_replace('}','',trim($this->input->get_post('debitcustname'))));
                    if($transaction_id && $debit_account && $transaction_amount && $transaction_date && $transaction_currency){
                        if(!$this->ipn_m->is_transaction_dublicate($transaction_id,$ipn_depositor)){
                            $transaction_date = time();
                            $data = array(
                                    'ipn_depositor' => $ipn_depositor,
                                    'transaction_id' => $transaction_id,
                                    'reference_number' => $debit_account,
                                    'transaction_date' => $transaction_date,
                                    'amount' => $transaction_amount,
                                    'active' => 1,
                                    'currency' => $transaction_currency,
                                    'transaction_type' => $transaction_type,
                                    'particulars' => $transaction_particulars,
                                    'phone' => $phone,
                                    'account' => $reference_number,
                                    'customer_name' => $debit_customer,
                                    'created_on' => time(),
                                    
                                );
                            $id = $this->ipn_m->insert($data);
                            if($id){
                                $mail.=' - Some files - '.$transaction_id;
                                echo 'Successful|Update Token '.$transaction_id;
                            }else{
                                $status = 5;
                                $mail.=' - Failed to capture files';
                                echo 'Failed Error|Unable to capture the entry';
                            }
                            $this->cron_job_process_ipn_payments($status);
                        }else{
                            $mail.=' - Transaction Duplicate';
                            //echo 'Successful|Update Token '.$transaction_id;
                            echo 'Successful|Update Token 112233';
                        }
                    }else{
                        $mail.=' - Missing parameters';
                        echo 'Failed Error|Either tranid or debitaccount or tranAmount  or tranDate or tranCurrency missing';
                    }
                }else{
                    $mail.= '  - No file sent ';
                    echo 'Failed Error|No data sent';
                }

            }else{
                $mail.=' - credentails error';
                echo 'IPN credentials failed';
            }
            $search_string = substr(basename($_SERVER['REQUEST_URI']),strpos(basename($_SERVER['REQUEST_URI']), "?"));
            $forwarders = $this->billing_m->get_ipn_forwarders();
            if($forwarders){
                $response = FALSE;
                foreach ($forwarders as $validation_endpoint){
                    $url = $validation_endpoint->equity_ipn_end_point.$search_string;
                    $result = $this->_curl_post_json($url,json_encode(array()));
                } 
            }
            if($mail){
                $headers = 'From: Chamasoft Equity IPn <notifications@chamasoft.com>' . "\r\n" .
                        'Reply-To: billing@chamasoft.com' . "\r\n".
                        'X-Mailer: PHP/' . phpversion();
                //@mail('geoffrey.githaiga@digitalvision.co.ke','Equity IPN Files',$mail,$headers);
            }
        }else{
            echo 'Failed Error|Not from a whitelisted IP '.$_SERVER['REMOTE_ADDR'];
            $headers = 'From: Chamasoft Equity IPn <notifications@chamasoft.com>' . "\r\n" .
                        'Reply-To: billing@chamasoft.com' . "\r\n".
                        'X-Mailer: PHP/' . phpversion();
            //@mail('geoffrey.githaiga@digitalvision.co.ke','Equity IPN Files',$mail.$_SERVER['REMOTE_ADDR'],$headers);
        }
    }

    function cron_job_process_ipn_payments($accepted_transaction_id=0){
        $notifications = $this->ipn_m->get_unallocated_notications();
        if($notifications){
            foreach ($notifications as $notification){
                if($notification->ipn_depositor==1 || (!empty($accepted_transaction_id) && $notification->transaction_id == $accepted_transaction_id)){
                   if(preg_match('/sms/',strtolower($notification->account))){
                    $status = $this->_process_sms_payment($notification->amount,$notification->account,$notification->transaction_id,$notification->transaction_date,$notification->customer_name,$notification->phone,$notification->ipn_depositor);
                        $process = 1;
                    }else{
                        $status = $this->_processs_billing_payment($notification->amount,$notification->account,$notification->transaction_id,$notification->transaction_date,$notification->customer_name,$notification->phone,$notification->ipn_depositor);
                        $process = 2;
                    }
                $this->ipn_m->update($notification->id,array('status'=>$status)); 
$message = 'Payment Details: <br/><br/>

Customer Name: '.$notification->customer_name.' <br/><br/>

Account Number: '.$notification->account.' <br/><br/>
                
Amount Paid: '.$notification->currency.' '.$notification->amount.' <br/><br/>
                
Customer Number: '.$notification->phone.' <br/><br/>
                
Payment Message: '.$notification->transaction_id.' Confirmed. on '.timestamp_to_date_and_time($notification->transaction_date).' '.$notification->currency.' '.$notification->amount.' received from '.$notification->customer_name.' '.$notification->phone.'.  Account Number '.$notification->account;
if($notification->paybill_balance){
    $message.=' Account Balance '.$notification->currency.'. '.number_to_currency($notification->paybill_balance);
}

                    $this->_send_report($status,$process,$message,$notification->ipn_depositor); 
                }else{
                    $this->ipn_m->update($notification->id,array('status'=>7)); 
                }
            }
        }else{

        }
    }

    function _processs_billing_payment($amount=0,$account=0,$transaction_id=0,$transaction_date=0,$debit_customer=0,$phone=0,$ipn_depositor=0){
         if($amount && $account && $transaction_id &&$transaction_date&&$ipn_depositor){
            return($this->billing_settings->process_payment_ipn($amount,$account,$transaction_id,$transaction_date,$debit_customer,$phone,$ipn_depositor));
         }else{
            $status = 2;
         }
         return $status;
    }

    function _process_sms_payment($amount=0,$account=0,$transaction_id=0,$transaction_date=0,$debit_customer=0,$phone=0,$ipn_depositor=0){
        if($amount && $account && $transaction_id&&$ipn_depositor){
            $account = strtolower($account);
            $debit_account = explode('sms', $account);
            $account = trim($debit_account[1]);
            return $this->billing_settings->process_sms_payment_ipn($amount,$account,$transaction_id,$transaction_date,$debit_customer,$phone,$ipn_depositor);
        }
        else{
            $status = 2;
        }

        return $status;
    }

    function _send_report($status=0,$process=0,$message='',$ipn_depositor=''){
        $report = '';
        if(array_key_exists($status, $this->error_coding_status)){
            $report = $this->error_coding_status[$status];
            
        }
        $email = '';
        if($process==1){
            $process = 'SMS Purchase ';
        }else{
            $process = 'Bill Payment ';
        }
        if($status==1){
            $headers = 'From: Chamasoft Notifications - <notifications@chamasoft.com>' . "\r\n" .
                    'Reply-To: billing@chamasoft.com' . "\r\n".
                    'X-Mailer: PHP/' . phpversion();
        }else{
            $headers = 'From: Chamasoft Notifications - Error <notifications@chamasoft.com>' . "\r\n" .
                    'Reply-To: billing@chamasoft.com' . "\r\n".
                    'X-Mailer: PHP/' . phpversion();
        }

        $message = $process.
        $message.' - '.$report;
        if(preg_match('/45\.33\.18\.205/', $_SERVER['SERVER_ADDR'])){
            foreach ($this->list_to_notify as $email) {
                if($this->emails_manager->send_via_sendgrid(
                    $email,$this->ipn_depositors[$ipn_depositor].' 967600 Paybill Payment Received',
                    $message,'Chamasoft Notifications','notifications@chamasoft.com','info@chamasoft.com')){
                }else{
                    $message = str_replace('<br/>','', $message);
                     @mail($email, $this->ipn_depositors[$ipn_depositor].' 967600 Paybill Payment Received',$message,$headers);
                }
            } 
        }

        
        
    }

    function _curl_post_xml($url,$xml)
    {
        $ch = curl_init();  
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);    
        $output=curl_exec($ch);
        curl_close($ch);
        echo 'Check this out '.$output;die;
        return  $output;
     
    }

    function _curl_post_json($url,$json)
    {
        $ch = curl_init();  
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST, true );
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $json);    
        $output=curl_exec($ch);
        curl_close($ch);
        return json_encode($output);
    }

    function mpesa_post_notice(){
        $data = $this->input->post('data');
        if($data){
            $data_arr = explode("|",$this->_decryptData($data));
            $code = $data_arr[1];
            $mpesa_code = $data_arr[0];
            if(!$this->ipn_m->is_transaction_dublicate($mpesa_code,2)){
                $input = array(
                        'ipn_depositor'     =>  2,
                        'transaction_id'    =>  $data_arr[0],
                        'account'           =>  $data_arr[1],
                        'phone'             =>  $data_arr[2],
                        'amount'            =>  $data_arr[3],
                        'customer_name'     =>  $data_arr[4],
                        'transaction_date'  =>  $this->_make_date($data_arr[5],$data_arr[6]),
                        'reference_number'  =>  '',
                        'currency'          =>  'KES',
                        'particulars'       =>  'MPESA Payment from IPN',
                        'active'            =>  1,
                        'created_on'        =>  time(),
                    );
                $id = $this->ipn_m->insert($input);
                if($id){
                    echo 'Successful|Update Token '.$data_arr[0];
                }else{
                    echo 'Error';
                }
                $this->cron_job_process_ipn_payments();
            }else{
                echo 'Duplicate';
            }
        }else{
            echo 'Empty data sent';
        }
    }

    public function _make_date($mpesa_date,$time){
        $dt = explode("/",$mpesa_date);
        $date = $dt[0]."-".$dt[1]."-20".$dt[2].$time;
        return  strtotime($date);
    } 

    function _decryptData($value){ 
        $value  = base64_decode($value);
        $key = "PD]wq(H@bSUN23"; 
        $crypttext = $value; 
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB); 
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND); 
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $crypttext, MCRYPT_MODE_ECB, $iv); 
       return trim($decrypttext); 
    }



    function forward_ipns($limit = 10){
        $ipns = $this->ipn_m->get_all_unforwarded_ips($limit);
        if($ipns){
            foreach ($ipns as $ipn) {
                $result = '';
                if($ipn->ipn_depositor == 1){
                    $data_query = array(
                        'username' => 'equity-bank',
                        'password' => 'equitybankIPN',
                        'tranid' => $ipn->transaction_id,
                        'refNo' => $ipn->reference_number,
                        'tranDate' => date('d-m-Y',$ipn->transaction_date),
                        'tranAmount' => $ipn->amount,
                        'tranCurrency' => $ipn->currency,
                        'tranType' => $ipn->transaction_type,
                        'tranParticular' => $ipn->particulars,
                        'phonenumber' => $ipn->phone,
                        'debitaccount' => $ipn->account,
                        'debitcustname' => $ipn->customer_name,
                    );
                    $query_string = http_build_query($data_query);
                    $forwarders = $this->billing_m->get_ipn_forwarders();
                    if($forwarders){
                        $response = FALSE;
                        foreach ($forwarders as $validation_endpoint){
                            $url = $validation_endpoint->equity_ipn_end_point.'?'.$query_string;
                            $result = $this->_curl_post_json($url,json_encode(array()));
                        } 
                    }
                }elseif($ipn->ipn_depositor == 2){
                    $validation_xml = '
                            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                                <soapenv:Body>
                                    <ns1:C2BPaymentValidationRequest xmlns:ns1="http://cps.huawei.com/cpsinterface/c2bpayment">
                                        <TransType>'.$ipn->particulars.'</TransType>
                                        <TransID>'.$ipn->transaction_id.'</TransID>
                                        <TransTime>'.date('Y-m-dHis',$ipn->transaction_date).'</TransTime>
                                        <TransAmount>'.$ipn->amount.'</TransAmount>
                                        <BusinessShortCode>967600</BusinessShortCode>
                                        <BillRefNumber>'.$ipn->account.'</BillRefNumber>
                                        <MSISDN>'.$ipn->phone.'</MSISDN>
                                        <KYCInfo>
                                            <KYCName>[Personal Details][First Name]</KYCName>
                                            <KYCValue>'.$ipn->customer_name.'</KYCValue>
                                        </KYCInfo>
                                        <KYCInfo>
                                            <KYCName>[Personal Details][Middle Name]</KYCName>
                                            <KYCValue> </KYCValue>
                                        </KYCInfo>
                                        <KYCInfo>
                                            <KYCName>[Personal Details][Last Name]</KYCName>
                                            <KYCValue> </KYCValue>
                                        </KYCInfo>
                                    </ns1:C2BPaymentValidationRequest>
                                </soapenv:Body>
                            </soapenv:Envelope>';

                    $Confirmation_xml = '
                            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                                <soapenv:Body>
                                    <ns1:C2BPaymentConfirmationRequest xmlns:ns1="http://cps.huawei.com/cpsinterface/c2bpayment">
                                        <TransType>'.$ipn->particulars.'</TransType>
                                        <TransID>'.$ipn->transaction_id.'</TransID>
                                        <TransTime>'.date('YmdHis',$ipn->transaction_date).'</TransTime>
                                        <TransAmount>'.$ipn->amount.'</TransAmount>
                                        <BusinessShortCode>967600</BusinessShortCode>
                                        <BillRefNumber>'.$ipn->account.'</BillRefNumber>
                                        <OrgAccountBalance>'.$ipn->paybill_balance.'</OrgAccountBalance>
                                        <ThirdPartyTransID>'.$ipn->transaction_id.'</ThirdPartyTransID>
                                        <MSISDN>'.$ipn->phone.'</MSISDN>
                                        <KYCInfo>
                                            <KYCName>[Personal Details][First Name]</KYCName>
                                            <KYCValue>'.$ipn->customer_name.'</KYCValue>
                                        </KYCInfo>
                                        <KYCInfo>
                                            <KYCName>[Personal Details][Last Name]</KYCName>
                                            <KYCValue> </KYCValue>
                                        </KYCInfo>
                                    </ns1:C2BPaymentConfirmationRequest>
                                </soapenv:Body>
                            </soapenv:Envelope>';

                    $forwarders = $this->billing_m->get_ipn_forwarders();
                    if($forwarders){
                        foreach ($forwarders as $validation_endpoint){
                            $result = $this->curl->curl_post_xml($validation_xml,$validation_endpoint->mpesa_validation_end_point);
                            if($result){
                                if(!preg_match('/port/', strtolower($result)) || !preg_match('/duplicate/', strtolower($result))){
                                    echo $result;die;
                                }
                                
                            }
                            
                            if(preg_match('/duplicate/', strtolower($result))){
                            }elseif (preg_match('/successful/', $result)) {
                                $this->curl->curl_post_xml($Confirmation_xml,$validation_endpoint->mpesa_confirmation_end_point);
                            }
                        } 

                    }
                }
                $this->ipn_m->update($ipn->id,array('is_forwarded' => 1));
            }
            echo 'Done';
        }else{
            echo 'No result';
        }
    }

    function reset_forwarding(){
        $this->ipn_m->reset_forwarding();
    }

    function count_forwading(){
        echo $this->ipn_m->count_forwading();
    }
}

?>