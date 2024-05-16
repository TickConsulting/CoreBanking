<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Endpoint extends CI_Controller{
    
    protected $equity_transaction_type_options = array(
        1=>'C',//Deposit
        2=>'D',//Withdrawal
    );

    protected $equity_transaction_type_name_options = array(
        1=>'deposit',
        2=>'withdrawal',
    );

    protected $equity_transaction_type_options_keys;
    public $group;
    

    public function __construct(){
        parent::__construct();
        $this->load->model('transaction_alerts/transaction_alerts_m');
        $this->load->model('banks/banks_m');
        $this->load->library('transactions');
        $this->load->model('safaricom/safaricom_m');
        $this->load->model('withdrawals/withdrawals_m');
        $this->load->model('members/members_m');
        $this->load->library('mailer');
        $this->load->library('loan');
        
        $this->load->model('transactions/transactions_m');
        $this->equity_transaction_type_options_keys = array_flip($this->equity_transaction_type_options);
    }

    public function index(){
        @ini_set('memory_limit','500M');
        error_reporting(-1);
        if(isset($_REQUEST)){
            file_put_contents("logs/equity_bank_request_data.dat",date("d-M-Y h:i A")."\t".serialize($_REQUEST)."\t".serialize($_GET)."\n",FILE_APPEND);
            $data = file_get_contents('php://input');
            $json_data = json_decode($data);
            file_put_contents("logs/equity_bank_request_post_data.dat",date("d-M-Y h:i A")."\t".serialize(json_decode($data))."\n",FILE_APPEND);
            $responseCode = 2;
            $username = "";
            if(isset($json_data->username)){
                $username = $json_data->username;
            }
            $password = "";
            if(isset($json_data->password)){
                $password = $json_data->password;
            }
            if(preg_match('/(45\.33\.18\.205)/',$_SERVER['REMOTE_ADDR'])
                ||preg_match('/(196\.216\.242\.171)/',$_SERVER['REMOTE_ADDR'])
                ||preg_match('/(127\.0\.0\.1)/',$_SERVER['SERVER_ADDR'])
                ||($username=="chamasoft"&&$password=="NuFN=FbktVBfJb9Tt4ew8scAT#RRHD=j##Eug95nndmt4g+Aky93DR9RY_6C+")
                || preg_match('/173\.255\.205\.7/',$_SERVER['REMOTE_ADDR'])
                || preg_match('/45\.33\.11\.77/',$_SERVER['REMOTE_ADDR'])
                || preg_match('/169\.254\.131\.1/',$_SERVER['REMOTE_ADDR'])
                || preg_match('/169\.254\.132\.1/',$_SERVER['REMOTE_ADDR'])
                || preg_match('/(azurewebsites\.net)/',$_SERVER['HTTP_HOST'])
            ){
                if(!empty($json_data)){
                    if(isset($json_data->tranid)){
                            $responseCode = 0;
                    }
                    if(!isset($json_data->tranParticular)){
                        $json_data->tranParticular = '0';
                    }
                    if(!isset($json_data->tranRemarks)){
                        $json_data->tranRemarks = '0';
                    }
                    if($this->transaction_alerts_m->check_if_equity_bank_transaction_is_duplicate($json_data->tranid)){
                        echo json_encode(array('status'=>'success','input'=>'post','message'=>'Duplicate Request','ACK'=>'OK','responseCode'=>1));
                        die;
                    }
                    $input = array(
                        'tranCurrency'=>$json_data->tranCurrency,
                        'tranDate'=>$json_data->tranDate,
                        'tranid'=>$json_data->tranid,
                        'tranAmount'=>$json_data->tranAmount,
                        'trandrcr'=>$json_data->trandrcr,
                        'accid'=>$json_data->accid,
                        'refNo'=>$json_data->refNo,
                        'tranType'=>$json_data->tranType,
                        'tranParticular'=>$json_data->tranParticular,
                        'tranRemarks'=>$json_data->tranRemarks,
                        'ip_address'=>$_SERVER['REMOTE_ADDR']?:'',
                        'created_on'=>time(),
                    );
                       
                    if($equity_bank_transaction_alert_id = $this->transaction_alerts_m->insert_equity_bank_transaction_alert($input)){
                        $bank_id = $this->banks_m->get_bank_id_by_slug('equity-bank');
                        $description = "<strong>Transaction ID:</strong>".$json_data->tranid."<br/>
                                        <strong>Transaction Transaction Type:</strong>".$json_data->tranType."<br/>
                                        <strong>Transaction Reference Number:</strong>".$json_data->refNo."<br/>
                                        <strong>Transaction Debit or Credit:</strong>".$json_data->trandrcr."<br/>
                                        <strong>Transaction Remarks:</strong>".$json_data->tranRemarks."<br/>
                                        <strong>Transaction Particular:</strong>".$json_data->tranParticular;
                        $input = array(
                            'equity_bank_transaction_alert_id'=>$equity_bank_transaction_alert_id,
                            'created_on'=>time(),
                            'transaction_id'=>$json_data->tranid,
                            'type'=>$this->equity_transaction_type_options_keys[$json_data->trandrcr],
                            'account_number'=>$json_data->accid,
                            'amount'=>valid_currency($json_data->tranAmount),
                            'transaction_date'=>strtotime($json_data->tranDate),
                            'is_merged'=> 0,
                            'reconciled'=> 0,
                            'bank_id'=>$bank_id,
                            'active'=>1,
                            'particulars'=>$json_data->tranParticular,
                            'ip_address'=>$_SERVER['REMOTE_ADDR']?:'',
                            'description'=>$description,
                            'group_members_notified'=>0,
                            'currency'=>$json_data->tranCurrency,
                        );
                        if($transaction_alert_id = $this->transaction_alerts_m->insert($input)){
                            echo json_encode(array('status'=>'success','input'=>'post','message'=>'Request OK','ACK'=>'OK','responseCode'=>$responseCode));
                            if($this->equity_transaction_type_options_keys[$json_data->trandrcr] == 1){
                                $this->transactions->send_transaction_alert_notification($json_data->accid,strtotime($json_data->tranDate),$this->equity_transaction_type_name_options[$this->equity_transaction_type_options_keys[$json_data->trandrcr]],valid_currency($json_data->tranAmount),$json_data->tranRemarks,$json_data->tranCurrency,$transaction_alert_id);
                            }
                            else{
                                $this->transactions->send_transaction_alert_notification($json_data->accid,strtotime($json_data->tranDate),$this->equity_transaction_type_name_options[$this->equity_transaction_type_options_keys[$json_data->trandrcr]],valid_currency($json_data->tranAmount),$json_data->tranRemarks,$json_data->tranCurrency,$transaction_alert_id);
                            }

                            // if(trim($this->equity_transaction_type_name_options[$this->equity_transaction_type_options_keys[$json_data->trandrcr]])=="withdrawal"){
                            //     $this->transactions->send_withdrawal_transaction_alert_notification($json_data->accid,strtotime($json_data->tranDate),$this->equity_transaction_type_name_options[$this->equity_transaction_type_options_keys[$json_data->trandrcr]],valid_currency($json_data->tranAmount),$json_data->tranRemarks,$json_data->tranCurrency,$transaction_alert_id);
                            // }
                            $this->transactions->queue_transaction_alert_forwards($transaction_alert_id,$data);
                        }else{
                            echo json_encode(array('status'=>'success','input'=>'post','message'=>'Could not insert transaction alert : '.serialize($this->db->error()),'ACK'=>'NO','responseCode'=>2)); 
                            die;
                        }
                    }else{
                        echo json_encode(array('status'=>'success','input'=>'post','message'=>'Could not insert transaction alert jj','ACK'=>'NO','responseCode'=>2)); 
                        die;
                    }
                }else{
                    echo json_encode(array('status'=>'success','input'=>'post','message'=>'Blank Request','ACK'=>'NO','responseCode'=>2)); 
                    die;
                }
            }else{
                echo json_encode(array('status'=>'error','input'=>'post','message'=>'Access Denied from '.$_SERVER['REMOTE_ADDR'],'ACK'=>'NO','responseCode'=>2)); 
                die;
            }
        }else{
           echo json_encode(array('status'=>'success','input'=>'post','message'=>'Blank Request','ACK'=>'NO','responseCode'=>2)); 
        }
    }
    function record_stk_push_account_payment($loan_id=0){
        $file = file_get_contents('php://input');
        file_put_contents("logs/stk_push_callback.txt","\n".date("d-M-Y h:i A").$file,FILE_APPEND);
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
                                    for ($i=0; $i <=4; $i++) { 
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
                                'response_code' => $result_code,
                                'amount' => $amount,
                                'loan_id'=>$loan_id,
                                'phone' => $phone,
                                'shortcode' => "4135763",
                                'customer_message'=>$result_description,
                                'checkout_request_id'=>$CheckoutRequestID,
                                'merchant_request_id'=>$merchant_request_id,
                                'customer_message'=>$result_description,
                                'customer_message'=>$result_description,
                                'result_description' => $result_description,
                                'transaction_id' => $transaction_id,
                                'organization_balance' => $balance,
                                'transaction_date' => $transaction_date,
                                'modified_on' => time(),
                            );
                            if(!$request = $this->safaricom_m->get_stk_request_by_merchant_request_id_and_checkout_request_id($CheckoutRequestID,$merchant_request_id)){
                            if($id = $this->safaricom_m->insert_stk_push_request($update)){
                                $loan=$this->loans_m->get($loan_id);
                                
                                if($loan){
                                    $deposit_date =$transaction_date ; 
                                    $send_sms_notification =0;
                                    $deposit_method =1;
                                    $send_email_notification =0;
                                    $description='Payment via STK Push';
                                    $member = $this->members_m->get_group_member($loan->member_id,$loan->group_id);
                                   
                                    $created_by = $this->members_m->get_group_member_by_user_id($loan->group_id,$member->user_id);
                                     
                                    if($amount && $deposit_date && $member && $created_by){
                                        
                                        if($this->loan->record_loan_repayment($loan->group_id,$deposit_date,$member,$loan->id,"mobile-",$deposit_method,$description,$amount,$send_sms_notification,$send_email_notification,$created_by)){
                                            $response = array(
                                                "ResultDesc" => "Received and Reconciled",
                                                "ResultCode" => "0"
                                            );
                                            file_put_contents("logs/stk_push_callback_response.txt","\n".date("d-M-Y h:i A").json_encode($response),FILE_APPEND);
                                            
                                        }else{
                                            $response = array(
                                                "ResultDesc" => "Received and Not reconciled",
                                                "ResultCode" => "0"
                                            );
                                            file_put_contents("logs/stk_push_callback_response.txt","\n".date("d-M-Y h:i A").json_encode($response),FILE_APPEND);

                                            
                                        }
                                        
                                    } else {
                                        $response = array(
                                            "ResultDesc" => "Received ,Missing Params",
                                            "ResultCode" => "0",
                                            "missingParams"=>true
                                        );
                                        file_put_contents("logs/stk_push_callback_response.txt","\n".date("d-M-Y h:i A").json_encode($response),FILE_APPEND);
                                        
                                    }
                                    }
                                    else{
                                        $response = array(
                                            "ResultDesc" => "Received. Transaction Not found",
                                            "ResultCode" => "0"
                                        );
                                        file_put_contents("logs/stk_push_callback_response.txt","\n".date("d-M-Y h:i A").json_encode($response),FILE_APPEND);
                                    }
                                   
                                // $this->transactions->send_customer_callback($request);
                            }else{
                                $response = array(
                                    "ResultDesc" => "Could not insert payment.",
                                    "ResultCode" => "1",
                                    "MerchantRequestId"=>$merchant_request_id
                                );
                                file_put_contents("logs/stk_push_callback_response.txt","\n".date("d-M-Y h:i A").json_encode($response),FILE_APPEND);
                            }
                        }
                        else{
                            $response = array(
                                "ResultDesc" => "Duplicate",
                                "ResultCode" => "0"
                            );
                            file_put_contents("logs/stk_push_callback_response.txt","\n".date("d-M-Y h:i A").json_encode($response),FILE_APPEND);

                        }
                        
                    }else{
                        $response = array(
                            "ResultDesc" => "Empty Callback",
                            "ResultCode" => "1"
                        );
                        file_put_contents("logs/stk_push_callback_response.txt","\n".date("d-M-Y h:i A").json_encode($response),FILE_APPEND);
                    }
                }else{
                    $response = array(
                        "ResultDesc" => "Empty Body",
                        "ResultCode" => "1"
                    );
                    file_put_contents("logs/stk_push_callback_response.txt","\n".date("d-M-Y h:i A").json_encode($response),FILE_APPEND);
                }
            }else{
                $response = array(
                    "ResultDesc" => "Result file sent : file format error",
                    "ResultCode" => "1"
                );
                file_put_contents("logs/stk_push_callback_response.txt","\n".date("d-M-Y h:i A").json_encode($response),FILE_APPEND);

            }
        }else{
            $response = array(
                "ResultDesc" => "Empty File",
                "ResultCode" => "1"
            );
            file_put_contents("logs/stk_push_callback_response.txt","\n".date("d-M-Y h:i A").json_encode($response),FILE_APPEND);

        }
        echo json_encode($response);
    }

    public function sandbox(){
        @ini_set('memory_limit','500M');
        error_reporting(1);
        if(isset($_REQUEST)){
            file_put_contents("logs/equity_bank_request_data.dat",date("d-M-Y h:i A")."\t".serialize($_REQUEST)."\t".serialize($_GET)."\n",FILE_APPEND);
            $data = file_get_contents('php://input');
            $json_data = json_decode($data);
            file_put_contents("logs/equity_bank_request_post_data.dat",date("d-M-Y h:i A")."\t".serialize(json_decode($data))."\n",FILE_APPEND);
            $responseCode = 2;
            //196.216.242.163
            if( preg_match('/(45\.33\.18\.205)/',$_SERVER['REMOTE_ADDR'])
                ||preg_match('/(196\.216\.242\.171)/',$_SERVER['REMOTE_ADDR'])
                ||preg_match('/(127\.0\.0\.1)/',$_SERVER['SERVER_ADDR'])
                ||($json_data->username=="chamasoft"&&$json_data->password=="NuFN=FbktVBfJb9Tt4ew8scAT#RRHD=j##Eug95nndmt4g+Aky93DR9RY_6C+") 
                ||preg_match('/173\.255\.205\.7/',$_SERVER['REMOTE_ADDR']) 
                ||preg_match('/45\.33\.11\.77/',$_SERVER['REMOTE_ADDR'])
                ||preg_match('/169\.254\.131\.1/',$_SERVER['REMOTE_ADDR'])
                ||preg_match('/169\.254\.132\.1/',$_SERVER['REMOTE_ADDR'])
                ||preg_match('/(azurewebsites\.net)/',$_SERVER['HTTP_HOST'])
            ){
            //if(TRUE){
                if(!empty($json_data)){
                    if(isset($json_data->tranid)){
                            $responseCode = 0;
                    }
                    
                    if(!isset($json_data->tranParticular)){
                        $json_data->tranParticular = '0';
                    }
                    if(!isset($json_data->tranRemarks)){
                        $json_data->tranRemarks = '0';
                    }
                    if($this->transaction_alerts_m->check_if_equity_bank_transaction_is_duplicate($json_data->tranid)){
                        echo json_encode(array('status'=>'success','input'=>'post','message'=>'Duplicate Request','ACK'=>'OK','responseCode'=>1));
                        die;
                    }

                    $input = array(
                        'tranCurrency'=>$json_data->tranCurrency,
                        'tranDate'=>$json_data->tranDate,
                        'tranid'=>$json_data->tranid,
                        'tranAmount'=>$json_data->tranAmount,
                        'trandrcr'=>$json_data->trandrcr,
                        'accid'=>$json_data->accid,
                        'refNo'=>$json_data->refNo,
                        'tranType'=>$json_data->tranType,
                        'tranParticular'=>$json_data->tranParticular,
                        'tranRemarks'=>$json_data->tranRemarks,
                        'ip_address'=>$_SERVER['REMOTE_ADDR']?:'',
                        'created_on'=>time(),
                    );
                       
                    if($equity_bank_transaction_alert_id = $this->transaction_alerts_m->insert_equity_bank_transaction_alert($input)){
                        $bank_id = $this->banks_m->get_bank_id_by_slug('equity-bank');
                        $description = "<strong>Transaction ID:</strong>".$json_data->tranid."<br/>
                                        <strong>Transaction Transaction Type:</strong>".$json_data->tranType."<br/>
                                        <strong>Transaction Reference Number:</strong>".$json_data->refNo."<br/>
                                        <strong>Transaction Debit or Credit:</strong>".$json_data->trandrcr."<br/>
                                        <strong>Transaction Remarks:</strong>".$json_data->tranRemarks."<br/>
                                        <strong>Transaction Particular:</strong>".$json_data->tranParticular;
                        $input = array(
                            'equity_bank_transaction_alert_id'=>$equity_bank_transaction_alert_id,
                            'created_on'=>time(),
                            'transaction_id'=>$json_data->tranid,
                            'type'=>$this->equity_transaction_type_options_keys[$json_data->trandrcr],
                            'account_number'=>$json_data->accid,
                            'amount'=>valid_currency($json_data->tranAmount),
                            'transaction_date'=>strtotime($json_data->tranDate),
                            'bank_id'=>$bank_id,
                            'active'=>1,
                            'is_merged'=> 0,
                            'reconciled'=> 0,
                            'particulars'=>$json_data->tranParticular,
                            'ip_address'=>$_SERVER['REMOTE_ADDR']?:'',
                            'description'=>$description,
                            'group_members_notified'=>0,
                        );
                        if($this->transaction_alerts_m->insert($input)){
                            echo json_encode(array('status'=>'success','input'=>'post','message'=>'Request OK','ACK'=>'OK','responseCode'=>$responseCode));
                            $this->transactions->send_transaction_alert_notification($json_data->accid,strtotime($json_data->tranDate),$this->equity_transaction_type_name_options[$this->equity_transaction_type_options_keys[$json_data->trandrcr]],valid_currency($json_data->tranAmount),$description,$json_data->tranCurrency);
                            $this->transactions->forward_transaction_alert($bank_id,$json_data->accid,$data);
                            $this->transactions->forward_transaction_alert_to_urls($data);
                            die; 
                        }else{
                            echo json_encode(array('status'=>'success','input'=>'post','message'=>'Could not insert transaction alert','ACK'=>'NO','responseCode'=>2)); 
                            die;
                        }
                    }else{
                        echo json_encode(array('status'=>'success','input'=>'post','message'=>'Could not insert transaction alert','ACK'=>'NO','responseCode'=>2)); 
                        die;
                    }
                }else{
                    echo json_encode(array('status'=>'success','input'=>'post','message'=>'Blank Request','ACK'=>'NO','responseCode'=>2)); 
                    die;
                }
            }else{
                echo json_encode(array('status'=>'error','input'=>'post','message'=>'Access Denied from '.$_SERVER['REMOTE_ADDR'],'ACK'=>'NO','responseCode'=>2)); 
                die;
            }
        }else{
           echo json_encode(array('status'=>'success','input'=>'post','message'=>'Blank Request','ACK'=>'NO','responseCode'=>2)); 
        }
    }

    function in_multiarray($elem, $array,$field)
    {
        $top = sizeof($array) - 1;
        $bottom = 0;
        while($bottom <= $top)
        {
            if($array[$bottom][$field] == $elem)
                return true;
            else 
                if(is_array($array[$bottom][$field]))
                    if(in_multiarray($elem, ($array[$bottom][$field])))
                        return true;

            $bottom++;
        }        
        return false;
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
                        'originator_conversation_id'          =>  $originator_conversation_id,
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
                    if($update_id = $this->safaricom_m->insert_b2c($update)){
                                        $response = array(
                                        "ResultDesc" => "success",
                                        "ResultCode" => "0"
                                    );
                    if($request = $this->withdrawals_m->get_request_by_reference_number($originator_conversation_id)){
                        if($result_code== '0'){
                            $data = array(
                                'is_disbursed' => 1,
                                'status'=>3,
                                'is_approved' => 1,
                                'active' => 1,
                                'disbursement_failed_error_message' => NULL,
                                'disbursed_on'=>time(),
                                'modified_on' => time(),
                            );
                          
                        }
                        else{
                             $data = array(
                            'is_disbursed' => 0,
                            'status'=>2,
                            'is_approved' => 1,
                            'is_disbursement_declined'=>1,
                            'active' => 1,
                            'disbursement_failed_error_message' => $result_description,
                            'modified_on' => time()
                        );
                        }
                        $this->withdrawals_m->update_withdrawal_request($request->id,$data);
                    }
                    }
                    // if($request = $this->withdrawals_m->get_request_by_reference_number($originator_conversation_id)){
                    //     if($result_code== '0'){
                    //         $data = array(
                    //             'is_disbursed' => 1,
                    //             'status'=>3,
                    //             'is_approved' => 1,
                    //             'active' => 1,
                    //             'disbursement_failed_error_message' => NULL,
                    //             'disbursed_on'=>time(),
                    //             'modified_on' => time(),
                    //         );
                    //        $this->withdrawals_m->update_withdrawal_request($request->id,$data);
                    //     }
                    //     if($update_id = $this->safaricom_m->insert_b2c($update)){   
                    //         // $request = $this->safaricom_m->get_b2c_request($request->id);
                    //         $response = array(
                    //                     "ResultDesc" => "success",
                    //                     "ResultCode" => "0"
                    //                 );
                    //         // if($this->transactions->reconcile_account_disbursement($request)){
                    //         //     $response = array(
                    //         //         "ResultDesc" => "success",
                    //         //         "ResultCode" => "0"
                    //         //     );
                    //         //     // print_r($this->transactions->send_customer_disbursement_callback($request));die;
                    //         // }else{
                    //         //     $response = array(
                    //         //         "ResultDesc" => "Result file sent : ".$this->session->flashdata('error'),
                    //         //         "ResultCode" => "1"
                    //         //     );  
                    //         // }
                    //     }else{
                    //         $response = array(
                    //             "ResultDesc" => "Result file sent : Transaction not recorded",
                    //             "ResultCode" => "1"
                    //         );  
                    //     }
                    // }else{
                    //     $response = array(
                    //         "ResultDesc" => "Result file sent : Transaction not found",
                    //         "ResultCode" => "1"
                    //     );  
                    // }
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
     
    function record_b2c_request($id='',$amount='',$shortcode='',$phone_number='',$reference_number=''){
        $file = file_get_contents('php://input');
        $json_data=json_decode($file);
        $response=array(
        "status"=>1,
        "message"=>"received"
        );
        file_put_contents("logs/b2c_requests.txt","\n".date("d-M-Y h:i A").$file,FILE_APPEND);
        $error_code = isset($json_data->errorCode)?$json_data->errorCode:'';
        $error_message = isset($json_data->errorMessage)?$json_data->errorMessage:'';
        $response_code = isset($json_data->ResponseCode)?$json_data->ResponseCode:'';
        $response_description = isset($json_data->ResponseDescription)?$json_data->ResponseDescription:'';
        $OriginatorConversationID = isset($json_data->OriginatorConversationID)?$json_data->OriginatorConversationID:'';
        $ConversationID = isset($json_data->ConversationID)?$json_data->ConversationID:'';
        $data = array(
            'paybill'           =>  $shortcode,
            'amount'            =>  $amount,
            'withdrawal_request_id'=>  $id,
            'request_status'    =>  1,
            'result_code'      =>   $response_code,
            'phone'             =>  $phone_number,
            'originator_conversation_id' => $OriginatorConversationID,
            'created_on'        =>  time(),
            'request_time'      =>  date('YmdHis',time()),
            'result_description'=>  $response_description?:$error_message,
            'conversation_id'   =>  $ConversationID,
            'user_id'           =>  1
        );
        $update = array(
            'is_disbursed' => 1,
            'status'=>3,
            'is_approved' => 1,
            'active' => 1,
            'disbursement_failed_error_message' => NULL,
            'disbursed_on'=>time(),
            'modified_on' => time(),
        );
       $this->withdrawals_m->update_withdrawal_request($id,$update);
        if($req_id = $this->safaricom_m->insert_b2c($data)){
            $response=array(
                'status'=>1,
                "statusMessage"=>"recorded and forwaded Successfully",
                "data"=>$json_data,
            );
        }
        else{
            $response=array(
                'status'=>1,
                "statusMessage"=>"Forwaded Successfully",
                "data"=>$json_data,
            );  
        }
       
        echo json_encode($response);
    }
}