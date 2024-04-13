<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_alerts extends Public_Controller{

    protected $equity_transaction_type_options = array(
        1=>'C',//Deposit
        2=>'D',//Withdrawal
    );

    protected $equity_transaction_type_name_options = array(
        1=>'deposit',
        2=>'withdrawal',
    );

    protected $t24_transaction_type_options = array(
        1=>'CR',//Deposit
        2=>'DR',//Withdrawal
    );

    protected $safaricom_transaction_type_options = array(
        1=>'CR',//Deposit
        2=>'DR',//Withdrawal
    );

    protected $t24_transaction_type_name_options = array(
        1=>'deposit',
        2=>'withdrawal',
    );

    protected $jambo_pay_transaction_type_options = array(
        1=>'CR',//Deposit
        2=>'DR',//Withdrawal
    );

    protected $jambo_pay_transaction_type_name_options = array(
        1=>'deposit',
        2=>'withdrawal',
    );


    protected $equity_transaction_type_options_keys;
    protected $t24_transaction_type_options_keys;
    protected $jambo_pay_transaction_type_options_keys;
    protected $safaricom_transaction_type_options_keys;

    protected $safaricom_transaction_type_name_options = array(
        1=>'deposit',
        2=>'withdrawal',
    );


    public function reinitialize_equity_transaction_alerts($account_number = 0){
        $transaction_alerts = $this->transaction_alerts_m->get_transaction_alerts('',$account_number);
        foreach ($transaction_alerts as $key => $alert) {
            if($alert->type == 1){ //deposit
                if($alert->reconciled){
                    $deposit = $this->deposits_m->get_deposit_by_transaction_alert_id($alert->id);
                    if($deposit){
                        if($this->transactions->void_group_deposit($deposit->id,$deposit,TRUE,$deposit->group_id)){
                            $input = array(
                                'merged_transaction_alert_id' => '',
                                'active' => 0,
                                'reconciled' => 0,
                                'is_merged' => 0,
                                'marked_as_reconciled' => 0,
                                'modified_on' => time(),
                                'modified_by' => 0,
                            );
                                
                            if($this->transaction_alerts_m->update($alert->id,$input)){
                                echo 'Success \n \t';
                            }else{
                                echo 'Could not void alert \n \t';
                            }
                        }else{
                            echo 'Something went wrong \n \t'; 
                        }
                    }else{
                        echo 'Could not find deposit \n \t'; 
                    }
                }else{
                    $input = array(
                        'merged_transaction_alert_id' => '',
                        'active' => 0,
                        'reconciled' => 0,
                        'is_merged' => 0,
                        'marked_as_reconciled' => 0,
                        'modified_on' => time(),
                        'modified_by' => 0,
                    );
                    if($this->transaction_alerts_m->update($alert->id,$input)){
                        echo 'Success \n \t';
                    }else{
                        echo 'Could not void alerts \n \t';
                    }
                }
            }elseif($alert->type == 2){ //withdrawal
                if($alert->reconciled){
                    $withdrawal = $this->withdrawals_m->get_withdrawal_by_transaction_alert_id($alert->id);
                    if($withdrawal){
                        if($this->transactions->void_group_withdrawal($withdrawal->id,$withdrawal,TRUE,$withdrawal->group_id)){
                            $input = array(
                                'merged_transaction_alert_id' => '',
                                'active' => 0,
                                'reconciled' => 0,
                                'is_merged' => 0,
                                'marked_as_reconciled' => 0,
                                'modified_on' => time(),
                                'modified_by' => 0,
                            );
                               
                            if($this->transaction_alerts_m->update($alert->id,$input)){
                                echo 'Success \n \t';
                            }else{
                                 echo 'Could not void alerts \n \t';
                            }
                        }else{
                            echo 'Something went wrong \n \t'; 
                        }
                    }else{
                        echo 'Could not find withdrawal \n \t'; 
                    }
                }else{
                    $input = array(
                        'merged_transaction_alert_id' => '',
                        'active' => 0,
                        'reconciled' => 0,
                        'is_merged' => 0,
                        'marked_as_reconciled' => 0,
                        'modified_on' => time(),
                        'modified_by' => 0,
                    );
                    if($this->transaction_alerts_m->update($alert->id,$input)){
                        echo 'Success /n /t';
                    }else{
                        echo 'Could not unmerge merged alerts /n /t';
                    }
                }
            }
        }

        $transaction_alerts = $this->transaction_alerts_m->get_equity_bank_transaction_alerts($account_number);
        $bank_id = $this->banks_m->get_bank_id_by_slug('equity-bank');
        foreach ($transaction_alerts as $key => $alert) {
            $description = "<strong>Transaction ID:</strong>".$alert->tranid."<br/>
                        <strong>Transaction Transaction Type:</strong>".$alert->tranType."<br/>
                        <strong>Transaction Reference Number:</strong>".$alert->refNo."<br/>
                        <strong>Transaction Debit or Credit:</strong>".$alert->trandrcr."<br/>
                        <strong>Transaction Remarks:</strong>".$alert->tranRemarks."<br/>
                        <strong>Transaction Particular:</strong>".$alert->tranParticular;
            $input = array(
                'equity_bank_transaction_alert_id'=>$alert->id,
                'created_on'=>time(),
                'transaction_id'=>$alert->tranid,
                'type'=>$this->equity_transaction_type_options_keys[$alert->trandrcr],
                'account_number'=>$alert->accid,
                'amount'=>valid_currency($alert->tranAmount),
                'transaction_date'=>strtotime($alert->tranDate),
                'bank_id'=>$bank_id,
                'active'=>1,
                'reconciled'=>0,
                'is_merged' => 0,
                'particulars'=>$alert->tranParticular,
                'description'=>$description,
            );
            // print_r($input); die;
            if($this->transaction_alerts_m->insert($input)){
                echo 'Success \n \t';
            }else{
                echo 'Failed to create new alert \n \t';
            }
        }
    }

    public function __construct(){
        parent::__construct();
        // $this->load->library('banks/banks_m');
        $this->load->model('bank_branches/bank_branches_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('sacco_branches/sacco_branches_m');
        // $this->load->library('mobile_money_providers/mobile_money_providers_m');

        $this->load->model('transaction_alerts_m');
        $this->load->model('groups/groups_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->library('transactions');
        $this->load->model('members/members_m');
        $this->load->model('deposits/deposits_m');
        $this->load->model('banks/banks_m');


        

        $this->equity_transaction_type_options_keys = array_flip($this->equity_transaction_type_options);
        $this->t24_transaction_type_options_keys = array_flip($this->t24_transaction_type_options);
        $this->jambo_pay_transaction_type_options_keys = array_flip($this->jambo_pay_transaction_type_options);
        $this->safaricom_transaction_type_options_keys = array_flip($this->safaricom_transaction_type_options);
    }

    public function equity_bank(){
        @ini_set('memory_limit','500M');
        error_reporting(0);
        if(isset($_REQUEST)){
            file_put_contents("equity_bank_request_data.dat",date("d-M-Y h:i A")."\t".serialize($_REQUEST)."\t".serialize($_GET)."\n",FILE_APPEND);
            $data = file_get_contents('php://input');
            $json_data = json_decode($data);
            file_put_contents("equity_bank_request_post_data.dat",date("d-M-Y h:i A")."\t".serialize(json_decode($data))."\n",FILE_APPEND);
            $responseCode = 2;
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
                        'particulars'=>$json_data->tranParticular,
                        'description'=>$description,
                    );
                    if($this->transaction_alerts_m->insert($input)){
                        echo json_encode(array('status'=>'success','input'=>'post','message'=>'Request OK','ACK'=>'OK','responseCode'=>$responseCode));
                        die; 
                    }else{
                        echo json_encode(array('status'=>'success','input'=>'post','message'=>'Could not insert transaction alert','ACK'=>'NO','responseCode'=>2)); 
                        die;
                    }
                }else{
                    echo json_encode(array('status'=>'success','input'=>'post','message'=>'Could not insert transaction alert','ACK'=>'NO','responseCode'=>2)); 
                    die;
                }
            }
            echo json_encode(array('status'=>'success','input'=>'post','message'=>'Blank Request','ACK'=>'NO','responseCode'=>2)); 
            die;
        }
        else{

           echo json_encode(array('status'=>'success','input'=>'post','message'=>'Blank Request','ACK'=>'NO','responseCode'=>2)); 
        }
    }

    public function softgen(){
        @ini_set('memory_limit','500M');
        error_reporting(0);
        if(isset($_REQUEST)){
            file_put_contents("logs/t24_request_data.dat",date("d-M-Y h:i A")."\t".serialize($_REQUEST)."\t".serialize($_GET)."\n",FILE_APPEND);
            $data = file_get_contents('php://input');
            $json_data = json_decode($data);
            file_put_contents("logs/t24_request_post_data.dat",date("d-M-Y h:i A")."\t".serialize(json_decode($data))."\n",FILE_APPEND);
            $responseCode = 2;
            if(!empty($json_data)){
                if(isset($json_data->transaction_id)){
                    $responseCode = 0;
                }
                if(!isset($json_data->transaction_narrative)){
                    $json_data->transaction_narrative = '0';
                }

                if($this->transaction_alerts_m->check_if_t24_transaction_is_duplicate($json_data->transaction_id)){
                    echo json_encode(array('status'=>'success','input'=>'post','message'=>'Duplicate Request','ACK'=>'OK','responseCode'=>1));
                    die;
                }

                $input = array(
                    'transaction_id'=>$json_data->transaction_id,
                    'transaction_date'=>$json_data->transaction_date,
                    'transaction_id'=>$json_data->transaction_id,
                    'transaction_amount'=>$json_data->transaction_amount,
                    'transaction_type'=>$json_data->transaction_type,
                    'account_number'=>$json_data->account_number,
                    'transaction_narrative'=>$json_data->transaction_narrative,
                    'created_on'=>time(),
                );
                   
                if($t24_transaction_alert_id = $this->transaction_alerts_m->insert_t24_transaction_alert($input)){
                    $bank_id = $this->banks_m->get_bank_id_by_slug('softgen');
                    $description = "<strong>Transaction ID:</strong>".$json_data->transaction_id."<br/>
                                    <strong>Transaction Transaction Type:</strong>".$json_data->transaction_type."<br/>
                                    <strong>Transaction Particular:</strong>".$json_data->transaction_narrative;
                    $input = array(
                        't24_transaction_alert_id'=>$t24_transaction_alert_id,
                        'created_on'=>time(),
                        'transaction_id'=>$json_data->transaction_id,
                        'type'=>$this->t24_transaction_type_options_keys[$json_data->transaction_type],
                        'account_number'=>$json_data->account_number,
                        'amount'=>valid_currency($json_data->transaction_amount),
                        'transaction_date'=>$json_data->transaction_date,
                        'bank_id'=>$bank_id,
                        'active'=>1,
                        'particulars'=>$json_data->transaction_narrative,
                        'description'=>$description,
                        'group_members_notified'=>0,
                    );
                    if($this->transaction_alerts_m->insert($input)){
                        echo json_encode(array('status'=>'success','input'=>'post','message'=>'Request OK','ACK'=>'OK','responseCode'=>$responseCode));
                        $this->transactions->send_transaction_alert_notification($json_data->account_number,$json_data->transaction_date,$this->t24_transaction_type_name_options[$this->t24_transaction_type_options_keys[$json_data->transaction_type]],valid_currency($json_data->transaction_amount),$description,$json_data->currency);
                        die; 
                    }else{
                        echo json_encode(array('status'=>'success','input'=>'post','message'=>'Could not insert transaction alert','ACK'=>'NO','responseCode'=>2)); 
                        die;
                    }
                }else{
                    echo json_encode(array('status'=>'success','input'=>'post','message'=>'Could not insert transaction alert','ACK'=>'NO','responseCode'=>2)); 
                    die;
                }
            }
            echo json_encode(array('status'=>'success','input'=>'post','message'=>'Blank Request','ACK'=>'NO','responseCode'=>2)); 
            die;
        }else{
           echo json_encode(array('status'=>'success','input'=>'post','message'=>'Blank Request','ACK'=>'NO','responseCode'=>2)); 
        }
    }

    public function jambo_pay(){
        @ini_set('memory_limit','500M');
        error_reporting(0);
        if(isset($_REQUEST)){
            file_put_contents("logs/jambo_pay_request_data.dat",date("d-M-Y h:i A")."\t".serialize($_REQUEST)."\t".serialize($_GET)."\n",FILE_APPEND);
            $data = file_get_contents('php://input');
            $json_data = json_decode($data);
            file_put_contents("logs/jambo_pay_request_post_data.dat",date("d-M-Y h:i A")."\t".serialize(json_decode($data))."\n",FILE_APPEND);
            $responseCode = 2;
            if(!empty($json_data)){
                if(isset($json_data->transaction_id)){
                    $responseCode = 0;
                }
                if(!isset($json_data->transaction_narrative)){
                    $json_data->transaction_narrative = '0';
                }

                if($this->transaction_alerts_m->check_if_jambo_pay_transaction_is_duplicate($json_data->transaction_id)){
                    echo json_encode(array('status'=>'success','input'=>'post','message'=>'Duplicate Request','ACK'=>'OK','responseCode'=>1));
                    die;
                }

                $input = array(
                    'transaction_id'=>$json_data->transaction_id,
                    'transaction_date'=>$json_data->transaction_date,
                    'transaction_id'=>$json_data->transaction_id,
                    'transaction_amount'=>$json_data->transaction_amount,
                    'transaction_type'=>$json_data->transaction_type,
                    'account_number'=>$json_data->account_number,
                    'transaction_narrative'=>$json_data->transaction_narrative,
                    'created_on'=>time(),
                );
                   
                if($jambo_pay_transaction_alert_id = $this->transaction_alerts_m->insert_jambo_pay_transaction_alert($input)){
                    $bank_id = $this->banks_m->get_bank_id_by_slug('jambo-pay');
                    $description = "<strong>Transaction ID:</strong>".$json_data->transaction_id."<br/>
                                    <strong>Transaction Transaction Type:</strong>".$json_data->transaction_type."<br/>
                                    <strong>Transaction Particular:</strong>".$json_data->transaction_narrative;
                    $input = array(
                        'jambo_pay_transaction_alert_id'=>$jambo_pay_transaction_alert_id,
                        'created_on'=>time(),
                        'transaction_id'=>$json_data->transaction_id,
                        'type'=>$this->jambo_pay_transaction_type_options_keys[$json_data->transaction_type],
                        'account_number'=>$json_data->account_number,
                        'amount'=>valid_currency($json_data->transaction_amount),
                        'transaction_date'=>$json_data->transaction_date,
                        'bank_id'=>$bank_id,
                        'active'=>1,
                        'particulars'=>$json_data->transaction_narrative,
                        'description'=>$description,
                        'group_members_notified'=>0,
                    );
                    if($this->transaction_alerts_m->insert($input)){
                        echo json_encode(array('status'=>'success','input'=>'post','message'=>'Request OK','ACK'=>'OK','responseCode'=>$responseCode));
                        $this->transactions->send_transaction_alert_notification($json_data->account_number,$json_data->transaction_date,$this->t24_transaction_type_name_options[$this->t24_transaction_type_options_keys[$json_data->transaction_type]],valid_currency($json_data->transaction_amount),$description,$json_data->currency);
                        die; 
                    }else{
                        echo json_encode(array('status'=>'success','input'=>'post','message'=>'Could not insert transaction alert','ACK'=>'NO','responseCode'=>2)); 
                        die;
                    }
                }else{
                    echo json_encode(array('status'=>'success','input'=>'post','message'=>'Could not insert transaction alert','ACK'=>'NO','responseCode'=>2)); 
                    die;
                }
            }
            echo json_encode(array('status'=>'success','input'=>'post','message'=>'Blank Request','ACK'=>'NO','responseCode'=>2)); 
            die;
        }else{
           echo json_encode(array('status'=>'success','input'=>'post','message'=>'Blank Request','ACK'=>'NO','responseCode'=>2)); 
        }
    }

    public function safaricom(){
        @ini_set('memory_limit','500M');
        error_reporting(1);
        if(isset($_REQUEST)){
            file_put_contents("logs/safaricom_request_data.dat",date("d-M-Y h:i A")."\t".serialize($_REQUEST)."\t".serialize($_GET)."\n",FILE_APPEND);
            $data = file_get_contents('php://input');
            $json_data = json_decode($data);
            file_put_contents("logs/safaricom_request_post_data.dat",date("d-M-Y h:i A")."\t".serialize(json_decode($data))."\n",FILE_APPEND);
            $responseCode = 2;
            if(preg_match('/(45\.33\.18\.205)/',$_SERVER['REMOTE_ADDR'])||preg_match('/(23\.239\.27\.43)/',$_SERVER['REMOTE_ADDR'])||preg_match('/(127\.0\.0\.1)/',$_SERVER['SERVER_ADDR'])||($json_data->username=="chamasoft"&&$json_data->password=="NuFN=FbktVBfJb9Tt4ew8scAT#RRHD=j##Eug95nndmt4g+Aky93DR9RY_6C+") || preg_match('/(197\.211\.20\.60)/',$_SERVER['REMOTE_ADDR'])){
                if(!empty($json_data)){
                    if(isset($json_data->transaction_id)){
                            $responseCode = 0;
                    }
                    if(!isset($json_data->transaction_type)){
                        $json_data->transaction_type = '0';
                    }
                    if(!isset($json_data->transaction_narrative)){
                        $json_data->transaction_narrative = '0';
                    }
                    if($this->transaction_alerts_m->check_if_safaricom_transaction_is_duplicate($json_data->transaction_id)){
                        echo json_encode(array('status'=>'success','input'=>'post','message'=>'Duplicate Request','ACK'=>'OK','responseCode'=>1));
                        die;
                    }


                    $input = array(
                        'transaction_id'=>$json_data->transaction_id,
                        'transaction_date'=>$json_data->transaction_date,
                        'transaction_id'=>$json_data->transaction_id,
                        'transaction_amount'=>$json_data->transaction_amount,
                        'transaction_type'=>$json_data->transaction_type,
                        'account_number'=>$json_data->account_number,
                        'transaction_narrative'=>$json_data->transaction_narrative,
                        'created_on'=>time(),
                    );

                       
                    if($equity_bank_transaction_alert_id = $this->transaction_alerts_m->insert_safaricom_transaction_alert($input)){
                        $mobile_money_provider_id = $this->mobile_money_providers_m->get_id_by_slug('safaricom-m-pesa');
                        $description = "<strong>Transaction ID:</strong>".$json_data->transaction_id."<br/>
                                            <strong>Transaction Transaction Type:</strong>".$json_data->transaction_type."<br/>
                                            <strong>Transaction Particular:</strong>".$json_data->transaction_narrative;
                            $input = array(
                                't24_transaction_alert_id'=>$t24_transaction_alert_id,
                                'created_on'=>time(),
                                'transaction_id'=>$json_data->transaction_id,
                                'type'=>$this->safaricom_transaction_type_options_keys[$json_data->transaction_type],
                                'account_number'=>$json_data->account_number,
                                'amount'=>valid_currency($json_data->transaction_amount),
                                'transaction_date'=>$json_data->transaction_date,
                                'bank_id'=>'',
                                'mobile_money_provider_id' => $mobile_money_provider_id,
                                'active'=>1,
                                'particulars'=>$json_data->transaction_narrative,
                                'ip_address'=>$_SERVER['REMOTE_ADDR']?:'',
                                'description'=>$description,
                                'group_members_notified'=>0,
                            );
                        if($transaction_alert_id = $this->transaction_alerts_m->insert($input)){
                            echo json_encode(array('status'=>'success','input'=>'post','message'=>'Request OK','ACK'=>'OK','responseCode'=>0));

                            $this->autoreconcile_online_checkouts($transaction_alert_id,$json_data->phone,$json_data->account_number,valid_currency($json_data->transaction_amount));

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
                echo json_encode(array('status'=>'error','input'=>'post','message'=>'Access Denied','ACK'=>'NO','responseCode'=>2)); 
                die;
            }
        }else{
           echo json_encode(array('status'=>'success','input'=>'post','message'=>'Blank Request','ACK'=>'NO','responseCode'=>2)); 
        }
    }

    public function kcb_bank(){
        header('Content-Type: application/json');
        @ini_set('memory_limit','500M');
        $res = array();
        error_reporting(0);
        if(isset($_REQUEST)){
            file_put_contents("kcb_bank_request_data.dat",date("d-M-Y h:i A")."\t".file_get_contents('php://input')."\n",FILE_APPEND);
            $data = file_get_contents('php://input');
            $json_data = json_decode($data);
            $responseCode = 2;
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
                $accid = $json_data->accid;
                $refNo = $json_data->refNo;
                $tranid = $json_data->tranid;
                $tranDate = $json_data->tranDate;
                $tranAmount = $json_data->tranAmount;
                if($accid && $refNo && $tranid && $tranDate && $tranAmount){
                    if($this->transaction_alerts_m->check_if_kcb_bank_transaction_is_duplicate($json_data->tranid)){
                        $res = array(
                            'status' => 'success',
                            'input' => 'post',
                            'message' => 'Duplicate Request',
                            'ACK'   =>  'OK',
                            'responseCode' => 1,
                        );
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
                        'created_on'=>time(),
                    );
                    if($equity_bank_transaction_alert_id = $this->transaction_alerts_m->insert_kcb_bank_transaction_alert($input)){
                        $bank_id = $this->banks_m->get_bank_id_by_slug('kcb-bank-ltd');
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
                            'particulars'=>$json_data->tranParticular,
                            'description'=>$description,
                        );
                        if($this->transaction_alerts_m->insert($input)){
                            $res = array(
                                'status'=>'success',
                                'input'=>'post',
                                'message'=>'Request OK',
                                'ACK'=>'OK',
                                'responseCode'=>$responseCode
                            );
                        }else{
                            $res = array(
                                'status'=>'success',
                                'input'=>'post',
                                'message'=>'Could not insert transaction alert',
                                'ACK'=>'NO','responseCode'=>2
                            );
                        }
                    }else{
                        $res = array(
                            'status'=>'success',
                            'input'=>'post',
                            'message'=>'Could not insert transaction alert',
                            'ACK'=>'NO','responseCode'=>2
                        );
                    }
                }else{
                    $res = array(
                        'status' => 'success',
                        'input' => 'post',
                        'message' => 'Some values are missing',
                        'ACK'   =>  'OK',
                        'responseCode' => 2,
                    );
                }
            }else{
                $res = array(
                    'status'=>'success',
                    'input'=>'post',
                    'message'=>'Blank Request',
                    'ACK'=>'NO','responseCode'=>2,
                ); 
            }
        }
        else{
            $res = array(
                'status'=>'success',
                'input'=>'post',
                'message'=>'Blank Request',
                'ACK'=>'NO',
                'responseCode'=>2
            ); 
        }
        echo json_encode($res);
    }

    public function mark_bank_account_as_verified($id = 0){
        $this->load->model('bank_accounts/bank_accounts_m');
        $input = array(
            'is_verified' => 1
        );
        $this->bank_accounts_m->update($id,$input); 
    }

    function autoreconcile_online_checkouts($transaction_alert_id=0,$phone=0,$account_number=0,$amount=0){
        if($transaction_alert_id&&$phone&&$account_number&&$amount){
            $online_checkout_request = $this->transaction_alerts_m->get_online_checkout_transaction_requests_by_phone_and_account($account_number,$phone);
            $transaction_alert = $this->transaction_alerts_m->get($transaction_alert_id); 
            if($online_checkout_request && $transaction_alert && $transaction_alert->reconciled!=1){
                $contribution_id = 0;
                $loan_id = 0;
                $fine_category = 0;
                if($online_checkout_request->contribution_id){
                    if($this->transactions->record_contribution_payment(
                        $online_checkout_request->group_id,
                        $transaction_alert->transaction_date,
                        $online_checkout_request->member_id,
                        $online_checkout_request->contribution_id,
                        $online_checkout_request->account_id,1,
                        '',
                        valid_currency($amount),
                        0,0,$transaction_alert->id)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$online_checkout_request->group_id)){
                            @mail('geoffrey.githaiga@digitalvision.co.ke','Auto reconcile contribution',
                                    serialize($online_checkout_request),'From: Safaricom Files <notifications@chamasoft.com>' . "\r\n" .'Reply-To: billing@chamasoft.com' . "\r\n".'X-Mailer: PHP/' . phpversion());
                        }
                    }
                }else if($online_checkout_request->fine_category){
                    if($this->transactions->record_fine_payment(
                        $online_checkout_request->group_id,
                        $transaction_alert->transaction_date,
                        $online_checkout_request->member_id,
                        $online_checkout_request->fine_category,
                        $online_checkout_request->account_id,1,
                        '',
                        valid_currency($amount),
                        0,
                        0,
                        $transaction_alert->id)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$online_checkout_request->group_id)){
                            @mail('geoffrey.githaiga@digitalvision.co.ke','Auto reconcile Fine',
                                    serialize($online_checkout_request),'From: Safaricom Files <notifications@chamasoft.com>' . "\r\n" .'Reply-To: billing@chamasoft.com' . "\r\n".'X-Mailer: PHP/' . phpversion());
                        }
                    }
                }else if($online_checkout_request->loan_id){
                    $member = $this->members_m->get($online_checkout_request->member_id);
                    if($member){
                        if($this->loan->record_loan_repayment(
                            $online_checkout_request->group_id,
                            $transaction_alert->transaction_date,
                            $member,
                            $online_checkout_request->loan_id,
                            $online_checkout_request->account_id,1,
                            '',
                            valid_currency($amount),
                            0,
                            0,
                            $member,
                            $member->user_id,
                            $transaction_alert->id)){
                            if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$online_checkout_request->group_id)){
                                @mail('geoffrey.githaiga@digitalvision.co.ke','Auto reconcile loan',
                                    serialize($online_checkout_request),'From: Safaricom Files <notifications@chamasoft.com>' . "\r\n" .'Reply-To: billing@chamasoft.com' . "\r\n".'X-Mailer: PHP/' . phpversion());

                            }
                        }
                    }
                }

                $this->transaction_alerts_m->update_online_checkout_transaction_request(
                                                    $online_checkout_request->id,
                                                    array(
                                                        'status' => 2,  
                                                    ));
            }
        }
    }

    function online_banking_payment(){
        $file = file_get_contents('php://input');
        $response = array();
        if($file){
            $result = json_decode($file);
            if($result){
                $code = isset($result->code)?$result->code:'';
                $description = isset($result->description)?$result->description:'';
                $reference_number = isset($result->data->reference_number)?$result->data->reference_number:'';
                if($code && $reference_number){
                    $deposit = $this->deposits_m->get_request_by_reference_number($reference_number);
                    if($deposit){
                        $payment_description = isset($result->data->payment_description)?$result->data->payment_description:'';
                        $update = array(
                            "result_code" => $code,
                            "result_description" => $payment_description?:$description,
                            "modified_on" => time(),
                            "transaction_date" => $result->data->transaction_date?:time(),
                            "transaction_id" => $result->data->transaction_id,
                            'account_number' => $result->data->account_number,
                            "status" => ($code=="200")?3:2,
                        );
                        $this->deposits_m->update_payment_request($deposit->id,$update);
                        if($code == "200"){//record transaction alert
                            $this->_record_online_payment_transaction_alert($deposit->id);

                        }else{
                           $response = array(
                                'code' => "0",
                                "response" => 'Updated',
                            ); 
                        }
                        $user = $this->ion_auth->get_user($deposit->user_id);
                        $this->notifications->create(
                            'Transaction Update',
                            'Transaction : '.$description,
                            $user,
                            $deposit->member_id,
                            $deposit->user_id,
                            $deposit->member_id,
                            $deposit->group_id,
                            'View Transactions',
                            '/group',
                            19,'','','','','','',$reference_number,($code == '200')?1:0);
                    }else{
                        $response = array(
                            'code' => "1",
                            "response" => 'Deposit not found',
                        );    
                    }
                }else{
                    $response = array(
                        'code' => "1",
                        "response" => 'missing files',
                    );
                }
            }else{
                $response = array(
                    'code' => "1",
                    "response" => 'File error',
                );
            }
        }else{
            $response = array(
                'code' => "1",
                "response" => 'No message sent',
            );
        }
        echo json_encode($response);
    }


    function reconcile_direct_online_banking_payment(){
        if(preg_match('/(23\.239\.27\.43)/',$_SERVER['REMOTE_ADDR']) || preg_match('/(127\.0\.0\.1)/',$_SERVER['REMOTE_ADDR'])){
            $file = file_get_contents('php://input');
            $response = array();
            if($file){
                @mail('geoffrey.githaiga@digitalvision.co.ke','Auto reconcile contribution',$file,'From: Safaricom Files <notifications@chamasoft.com>' . "\r\n" .'Reply-To: billing@chamasoft.com' . "\r\n".'X-Mailer: PHP/' . phpversion());
                $result = json_decode($file);
                if($result){
                    $code = isset($result->code)?$result->code:'';
                    $description = isset($result->description)?$result->description:'';
                    $reference_number = isset($result->data->reference_number)?$result->data->reference_number:'';
                    $phone = isset($result->data->phone_number)?$result->data->phone_number:'';
                    $account_number = isset($result->data->account_number)?$result->data->account_number:'';
                    $amount = isset($result->data->amount)?$result->data->amount:'';
                    $transaction_date = isset($result->data->transaction_date)?$result->data->transaction_date:time();
                    $transaction_id = isset($result->data->transaction_id)?$result->data->transaction_id:0;
                    $customer_name = isset($result->data->customer_name)?$result->data->customer_name:'';
                    $transaction_narrative = $description.' by '.$customer_name.' via '.$phone;
                    if($code&&$phone&&$account_number&&$amount){
                        if($code == 200){
                            $account = $this->bank_accounts_m->get_group_default_bank_account_by_account_number($account_number);
                            if($account_number){
                                $user = $this->ion_auth->get_user_by_identity(valid_phone($phone));
                                if($user){
                                    if($account && $group = $this->groups_m->get($account->group_id)){
                                        if($member = $this->members_m->get_group_member_by_user_id($group->id,$user->id)){
                                            if($reference_number = $this->transactions->make_group_arrears_payment($group,$user,$member,$account,$amount,0)){
                                                $deposit = $this->deposits_m->get_request_by_reference_number($reference_number);
                                                if($deposit){
                                                    $update = array(
                                                        "result_code" => $code,
                                                        "result_description" => $transaction_narrative,
                                                        "modified_on" => time(),
                                                        "transaction_date" => $result->data->transaction_date?:time(),
                                                        "transaction_id" => $result->data->transaction_id,
                                                        'account_number' => $account_number,
                                                        "status" => ($code=="200")?3:2,
                                                    );
                                                    $this->deposits_m->update_payment_request($deposit->id,$update);
                                                    if($code == "200"){//record transaction alert
                                                        $this->_record_online_payment_transaction_alert($deposit->id);
                                                    }else{
                                                        $this->_create_e_wallet_transaction_alert($transaction_id,$transaction_date,$amount,$account_number,$transaction_narrative,$reference_number);
                                                       $response = array(
                                                            'code' => "0",
                                                            "response" => 'Updated',
                                                        ); 
                                                    }
                                                    $user = $this->ion_auth->get_user($deposit->user_id);
                                                    $this->notifications->create(
                                                        'Transaction Update',
                                                        'Transaction : '.$description,
                                                        $user,
                                                        $deposit->member_id,
                                                        $deposit->user_id,
                                                        $deposit->member_id,
                                                        $deposit->group_id,
                                                        'View Transactions',
                                                        site_url('group'),
                                                        19,'','','','','','',$reference_number,($code == '200')?1:0);
                                                }else{
                                                    $this->_create_e_wallet_transaction_alert($transaction_id,$transaction_date,$amount,$account_number,$transaction_narrative,$reference_number);
                                                    $response = array(
                                                        'code' => "1",
                                                        "response" => 'Deposit not found',
                                                    );    
                                                }

                                            }else{
                                                $this->_create_e_wallet_transaction_alert($transaction_id,$transaction_date,$amount,$account_number,$transaction_narrative,$reference_number);
                                                $response = array(
                                                    'code' => "1",
                                                    "response" => 'Could not create group reference number',
                                                ); 
                                            }
                                        }else{
                                            $this->_create_e_wallet_transaction_alert($transaction_id,$transaction_date,$amount,$account_number,$transaction_narrative,$reference_number);
                                            $response = array(
                                                'code' => "1",
                                                "response" => 'Group membr not found',
                                            ); 
                                        }
                                    }else{
                                        $this->_create_e_wallet_transaction_alert($transaction_id,$transaction_date,$amount,$account_number,$transaction_narrative,$reference_number);
                                        $response = array(
                                            'code' => "1",
                                            "response" => 'Group not found',
                                        );
                                    }
                                }else{
                                    $this->_create_e_wallet_transaction_alert($transaction_id,$transaction_date,$amount,$account_number,$transaction_narrative,$reference_number);
                                    $response = array(
                                        'code' => "1",
                                        "response" => 'user not found',
                                    );
                                }
                            }else{
                                $response = array(
                                    'code' => "1",
                                    "response" => 'Group account number not found',
                                );
                            }                        
                        }else{
                            $response = array(
                                'code' => "1",
                                "response" => 'Code number '.$code,
                            ); 
                        }
                    }else{
                        $response = array(
                            'code' => "1",
                            "response" => 'missing files',
                        );
                    }
                }else{
                    $response = array(
                        'code' => "1",
                        "response" => 'File error',
                    );
                }
            }else{
                $response = array(
                    'code' => "1",
                    "response" => 'No message sent',
                );
            }
        }else{
            $response = array(
                'code' => "1",
                "response" => 'Unknown host',
            );
        }
        echo json_encode($response);
    }

    function get_all_online_payments(){
        print_r($this->deposits_m->get_all_online_payments());
    }

    function _record_online_payment_transaction_alert($id=0,$reconcile_transaction = 1){
        if($id){
            $deposit = $this->deposits_m->get_online_payment($id);
            if($deposit && !$deposit->is_reconcilled){
                $transaction_narrative = $deposit->result_description;
                $transaction_id = $deposit->transaction_id;
                $input = array(
                    'tranCurrency' => 'KES',
                    'tranid'=> $transaction_id,
                    'tranDate'=>$deposit->transaction_date,
                    'tranAmount'=>$deposit->amount,
                    'trandrcr'=> "CR",
                    "tranType" => "CR",
                    'accid'=>$deposit->account_number,
                    'tranParticular'=>$transaction_narrative,
                    'tranRemarks'=>$transaction_narrative,
                    'refNo' => $deposit->reference_number,
                    'ip_address' => $_SERVER['REMOTE_ADDR']?:'',
                    'created_on'=>time(),
                );                     
                if($this->transaction_alerts_m->check_if_online_banking_transaction_is_duplicate($deposit->transaction_id)){
                    echo "Duplicate";
                     die;
                }else{
                    if($online_banking_transaction_alert_id = $this->transaction_alerts_m->insert_online_banking_transaction_alert($input)){
                        $bank_id = $this->banks_m->get_bank_id_by_slug('chamasoft-e-wallet');
                        $description = "<strong>Transaction ID:</strong>".$deposit->transaction_id."<br/>
                                        <strong>Transaction Transaction Type:</strong>CR<br/>
                                        <strong>Transaction Reference Number:</strong>".$deposit->reference_number."<br/>
                                        <strong>Transaction Debit or Credit:</strong>Credit<br/>
                                        <strong>Transaction Particular:</strong>".$transaction_narrative;
                        $input = array(
                            'online_banking_transaction_alert_id'=>$online_banking_transaction_alert_id,
                            'created_on'=>time(),
                            'transaction_id'=>$deposit->transaction_id,
                            'type'=>$this->safaricom_transaction_type_options_keys["CR"],
                            'account_number'=>$deposit->account_number,
                            'amount'=>valid_currency($deposit->amount),
                            'transaction_date'=>$deposit->transaction_date,
                            'bank_id'=>$bank_id,
                            'mobile_money_provider_id' => "",
                            'active'=>1,
                            'particulars'=>$transaction_narrative,
                            'ip_address'=>$_SERVER['REMOTE_ADDR']?:'',
                            'description'=>$description,
                            'reconciled' => 0,
                            'is_merged' => 0,
                            'group_members_notified'=>0,
                        );
                        if($transaction_alert_id = $this->transaction_alerts_m->insert($input)){
                            if($reconcile_transaction){
                                if($this->transactions->reconcile_payment_request($deposit->id,$transaction_alert_id,$deposit->transaction_id,$deposit->transaction_date)){
                                    $message = 'Wallet Payment Details:

                                    Transaction Details: '.$transaction_narrative.'
                                                    
                                    Amount Paid: KES '.valid_currency($deposit->amount).'
                                                    
                                    Payment Details:    Transaction ID:'.$deposit->transaction_id.'
                                                        Transaction Transaction Type:CR
                                                        Transaction Reference Number:'.$deposit->reference_number.'
                                                        Transaction Debit or Credit:Credit';                                 

                                    @mail('geoffrey.githaiga@digitalvision.co.ke','Auto reconcile contribution',$message,'From: Safaricom Files <notifications@chamasoft.com>' . "\r\n" .'Reply-To: billing@chamasoft.com' . "\r\n".'X-Mailer: PHP/' . phpversion());
                                    @mail('edwin.kapkei@digitalvision.co.ke','Auto reconcile contribution',$message,'From: Safaricom Files <notifications@chamasoft.com>' . "\r\n" .'Reply-To: billing@chamasoft.com' . "\r\n".'X-Mailer: PHP/' . phpversion());
                                    echo 'done';
                                    die;
                                }else{
                                    echo $this->session->flashdata('error');
                                     die;
                                }
                            }
                        }else{
                            echo 'insert transaction alert failed';
                             die;
                        }  
                    }else{
                        echo 'online_banking_transaction_alert_id failed';
                         die;
                    }
                }
            }else{
                echo 'is_reconcilled failed';
                 die;
            }
        }else{
            echo 'no id failed';
            die;
        }
    }

    function _create_e_wallet_transaction_alert($transaction_id=0,$transaction_date=0,$amount=0,$account_number=0,$transaction_narrative='',$reference_number=0){
        if($transaction_id&&$transaction_date&&$amount&&$account_number&&$transaction_narrative&&$reference_number){
            $input = array(
                'tranCurrency' => 'KES',
                'tranid'=> $transaction_id,
                'tranDate'=>$transaction_date,
                'tranAmount'=>$amount,
                'trandrcr'=> "CR",
                "tranType" => "CR",
                'accid'=>$account_number,
                'tranParticular'=>$transaction_narrative,
                'tranRemarks'=>$transaction_narrative,
                'refNo' => $reference_number,
                'ip_address' => $_SERVER['REMOTE_ADDR']?:'',
                'created_on'=>time(),
            );                    
            if($this->transaction_alerts_m->check_if_online_banking_transaction_is_duplicate($transaction_id)){
                echo "Duplicate";
                 die;
            }else{
                if($online_banking_transaction_alert_id = $this->transaction_alerts_m->insert_online_banking_transaction_alert($input)){
                    $bank_id = $this->banks_m->get_bank_id_by_slug('chamasoft-e-wallet');
                    $description = "<strong>Transaction ID:</strong>".$transaction_id."<br/>
                                    <strong>Transaction Transaction Type:</strong>CR<br/>
                                    <strong>Transaction Reference Number:</strong>".$reference_number."<br/>
                                    <strong>Transaction Debit or Credit:</strong>Credit<br/>
                                    <strong>Transaction Particular:</strong>".$transaction_narrative;
                    $input = array(
                        'online_banking_transaction_alert_id'=>$online_banking_transaction_alert_id,
                        'created_on'=>time(),
                        'transaction_id'=>$transaction_id,
                        'type'=>$this->safaricom_transaction_type_options_keys["CR"],
                        'account_number'=>$account_number,
                        'amount'=>valid_currency($amount),
                        'transaction_date'=>$transaction_date,
                        'bank_id'=>$bank_id,
                        'mobile_money_provider_id' => "",
                        'active'=>1,
                        'particulars'=>$transaction_narrative,
                        'ip_address'=>$_SERVER['REMOTE_ADDR']?:'',
                        'description'=>$description,
                        'reconciled' => 0,
                        'is_merged' => 0,
                        'group_members_notified'=>0,
                    );
                    if($transaction_alert_id = $this->transaction_alerts_m->insert($input)){
                        $message = 'Wallet Payment Details:

                            Transaction Details: '.$transaction_narrative.'
                                            
                            Amount Paid: KES '.valid_currency($amount).'
                                            
                            Payment Details:    Transaction ID:'.$transaction_id.'
                                                Transaction Transaction Type:CR
                                                Transaction Reference Number:'.$deposit->reference_number.'
                                                Transaction Debit or Credit:Credit';  

                        @mail('geoffrey.githaiga@digitalvision.co.ke','Auto reconcile contribution',$message,'From: Safaricom Files <notifications@chamasoft.com>' . "\r\n" .'Reply-To: billing@chamasoft.com' . "\r\n".'X-Mailer: PHP/' . phpversion());
                        @mail('edwin.kapkei@digitalvision.co.ke','Auto reconcile contribution',$message,'From: Safaricom Files <notifications@chamasoft.com>' . "\r\n" .'Reply-To: billing@chamasoft.com' . "\r\n".'X-Mailer: PHP/' . phpversion());
                        echo 'done';
                        die;
                    }else{
                        echo 'insert transaction alert failed';
                        die;
                    }  
                }else{
                    echo 'online_banking_transaction_alert_id failed';
                    die;
                }
            }
        }else{
            echo 'Files missing';
            die;
        }
    }

    function reconcile_online_banking_withdrawal(){
        if(preg_match('/(23\.239\.27\.43)/',$_SERVER['REMOTE_ADDR'])){
            $file = file_get_contents('php://input');
            $response = array();
            if($file){
                @mail('peter.kimutai@digitalvision.co.ke','Auto reconcile Withdrawal',$file,'From: Safaricom Files <notifications@chamasoft.com>' . "\r\n" .'Reply-To: billing@chamasoft.com' . "\r\n".'X-Mailer: PHP/' . phpversion());
                @mail('geoffrey.githaiga@digitalvision.co.ke','Auto reconcile Withdrawal',$file,'From: Safaricom Files <notifications@chamasoft.com>' . "\r\n" .'Reply-To: billing@chamasoft.com' . "\r\n".'X-Mailer: PHP/' . phpversion());
                $result = json_decode($file);
                if($result){
                    $code = isset($result->code)?$result->code:'';
                    $description = isset($result->description)?$result->description:'';
                    $data = isset($result->data)?$result->data:'';
                    $reference_number = isset($data->reference_number)?$data->reference_number:'';
                    $amount = isset($data->amount)?$data->amount:'';
                    $transaction_id = isset($data->transaction_id)?$data->transaction_id:'';
                    $phone_number = isset($data->phone_number)?$data->phone_number:'';
                    $account_number = isset($data->account_number)?$data->account_number:'';
                    $organization_balance = isset($data->organization_balance)?$data->organization_balance:'';
                    $transaction_date = isset($data->transaction_date)?$data->transaction_date:'';
                    $recipient = isset($data->recipient)?$data->recipient:'';
                    $charges = isset($data->charges)?$data->charges:'';
                    if($code&&$reference_number&&$account_number&&$amount){
                        $withdrawal_request = $this->withdrawals_m->get_request_by_reference_number($reference_number);
                        if($withdrawal_request){
                            $update = array();
                            if($code == 200){
                                $update +=array(
                                    "disbursement_result_status" => $code,
                                    "disbursement_result_description" => $recipient,
                                    "modified_on" => time(),
                                    'status' => 2,
                                    'is_disbursed' => 1,
                                    'disbursed_on' => $transaction_date,
                                    'is_disbursement_declined' => 0,
                                    'disbursement_charges' => $charges,
                                    'disbursement_receipt_number' => $transaction_id,
                                    "disbursement_status" => 3,
                                );
                            }else{
                                $update +=array(
                                    "disbursement_result_status" => $code,
                                    "disbursement_result_description" => $description,
                                    "modified_on" => time(),
                                    'status' => 2,
                                    'is_disbursed' => 0,
                                    'is_disbursement_declined' => 1,
                                    'declined_on' => time(),
                                    'disbursement_failed_error_message' => $description,
                                    "disbursement_status" => 4,
                                );
                            }

                            if($this->withdrawals_m->update_withdrawal_request($withdrawal_request->id,$update)){
                                if($code == "200"){//record transaction alert
                                    $this->_record_online_withdrawal_transaction_alert($withdrawal_request->id,$withdrawal_request->group_id);
                                }else{
                                   $response = array(
                                        'code' => "0",
                                        "response" => 'Updated',
                                    ); 
                                }
                            }else{
                                $response = array(
                                    'code' => "1",
                                    "response" => 'Update failed',
                                ); 
                            }
                        }else{
                            $this->_record_wild_transaction_alert($transaction_id,$transaction_date,$amount,$account_number,$recipient,$reference_number);
                        }
                    }else{
                        $response = array(
                            'code' => "1",
                            "response" => 'missing files',
                        );
                    }
                }else{
                    $response = array(
                        'code' => "1",
                        "response" => 'File error',
                    );
                }
            }else{
                $response = array(
                    'code' => "1",
                    "response" => 'No message sent',
                );
            }
        }else{
            $response = array(
                'code' => "1",
                "response" => 'Unknown host',
            );
        }
        echo json_encode($response);
    }

    function _record_online_withdrawal_transaction_alert($id=0,$group_id=0){
        if($id&&$group_id){
            $withdrawal = $this->withdrawals_m->get_group_withdrawal_request($id,$group_id);
            $group_account = $this->bank_accounts_m->get_group_default_bank_account($group_id);
            if($withdrawal && !$withdrawal->is_reconcilled){
                $transaction_narrative = $withdrawal->disbursement_result_description;
                $transaction_id = $withdrawal->disbursement_receipt_number;
                $input = array(
                    'tranCurrency' => 'KES',
                    'tranid'=> $transaction_id,
                    'tranDate'=>$withdrawal->disbursed_on,
                    'tranAmount'=>$withdrawal->amount,
                    'trandrcr'=> "DR",
                    "tranType" => "DR",
                    'accid'=>$group_account->account_number,
                    'tranParticular'=>$transaction_narrative,
                    'tranRemarks'=>$transaction_narrative,
                    'refNo' => $withdrawal->reference_number,
                    'ip_address' => $_SERVER['REMOTE_ADDR']?:'',
                    'created_on'=>time(),
                );                   
                if($this->transaction_alerts_m->check_if_online_banking_transaction_is_duplicate($transaction_id)){
                    echo "Duplicate";
                     die;
                }else{
                    if($online_banking_transaction_alert_id = $this->transaction_alerts_m->insert_online_banking_transaction_alert($input)){
                        $bank_id = $this->banks_m->get_bank_id_by_slug('chamasoft-e-wallet');
                        $description = "<strong>Transaction ID:</strong>".$transaction_id."<br/>
                                            <strong>Transaction Transaction Type:</strong>DR<br/>
                                            <strong>Transaction Reference Number:</strong>".$withdrawal->reference_number."<br/>
                                            <strong>Transaction Debit or Credit:</strong>Debit<br/>
                                            <strong>Transaction Particular:</strong>".$transaction_narrative;
                        $input = array(
                            'online_banking_transaction_alert_id'=>$online_banking_transaction_alert_id,
                            'created_on'=>time(),
                            'transaction_id'=>$transaction_id,
                            'type'=>$this->safaricom_transaction_type_options_keys["DR"],
                            'account_number'=>$group_account->account_number,
                            'amount'=>valid_currency($withdrawal->amount),
                            'transaction_date'=>$withdrawal->disbursed_on,
                            'bank_id'=>$bank_id,
                            'mobile_money_provider_id' => "",
                            'active'=>1,
                            'particulars'=>$transaction_narrative,
                            'ip_address'=>$_SERVER['REMOTE_ADDR']?:'',
                            'description'=>$description,
                            'reconciled' => 0,
                            'is_merged' => 0,
                            'group_members_notified'=>0,
                        );
                        if($transaction_alert_id = $this->transaction_alerts_m->insert($input)){
                            if($this->transactions->reconcile_withdrawal_request($withdrawal->id,$transaction_alert_id,$transaction_id,$withdrawal->disbursed_on,$group_id,$group_account->id)){
                                $transaction_narrative = $withdrawal->disbursement_result_description.' Withdrawal charges';
                                $transaction_id = $withdrawal->disbursement_receipt_number.'-1';
                                $input = array(
                                    'tranCurrency' => 'KES',
                                    'tranid'=> $transaction_id,
                                    'tranDate'=>$withdrawal->disbursed_on,
                                    'tranAmount'=>$withdrawal->disbursement_charges,
                                    'trandrcr'=> "DR",
                                    "tranType" => "DR",
                                    'accid'=>$group_account->account_number,
                                    'tranParticular'=>$transaction_narrative,
                                    'tranRemarks'=>$transaction_narrative,
                                    'refNo' => $withdrawal->reference_number,
                                    'ip_address' => $_SERVER['REMOTE_ADDR']?:'',
                                    'created_on'=>time(),
                                );  
                                if($this->transaction_alerts_m->check_if_online_banking_transaction_is_duplicate($transaction_id)){
                                    echo "Duplicate";
                                     die;
                                }else{
                                    if($online_banking_charges_transaction_alert_id = $this->transaction_alerts_m->insert_online_banking_transaction_alert($input)){
                                        $description = "<strong>Transaction ID:</strong>".$transaction_id."<br/>
                                                            <strong>Transaction Transaction Type:</strong>DR<br/>
                                                            <strong>Transaction Particular:</strong>".$transaction_narrative;
                                        $input = array(
                                            'online_banking_transaction_alert_id'=>$online_banking_charges_transaction_alert_id,
                                            'created_on'=>time(),
                                            'transaction_id'=>$transaction_id,
                                            'type'=>$this->safaricom_transaction_type_options_keys["DR"],
                                            'account_number'=>$group_account->account_number,
                                            'amount'=>valid_currency($withdrawal->disbursement_charges),
                                            'transaction_date'=>$withdrawal->disbursed_on,
                                            'bank_id'=>$bank_id,
                                            'mobile_money_provider_id' => "",
                                            'active'=>1,
                                            'particulars'=>$transaction_narrative,
                                            'ip_address'=>$_SERVER['REMOTE_ADDR']?:'',
                                            'description'=>$description,
                                            'group_members_notified'=>0,
                                            'reconciled' => 0,
                                            'is_merged' => 0,
                                        );

                                        if($charge_transaction_alert_id = $this->transaction_alerts_m->insert($input)){
                                            if($this->transactions->reconcile_withdrawal_request_bank_charges($withdrawal->id,$charge_transaction_alert_id,$transaction_id,$withdrawal->disbursed_on,$group_id,$group_account->id)){
$message = 'Wallet Withdrawal Details:

Transaction Details: '.$transaction_narrative.'
                
Amount Withdrawn: KES '.valid_currency($withdrawal->amount).'

Amount Charged: KES '.valid_currency($withdrawal->disbursement_charges).'
                
Withdrawal Details: Transaction ID:'.$transaction_id.'
                    Transaction Transaction Type:DR
                    Transaction Debit or Credit:Debit';   
                                                @mail('geoffrey.githaiga@digitalvision.co.ke','Auto reconcile withdrawal',$message,'From: Safaricom Files <notifications@chamasoft.com>' . "\r\n" .'Reply-To: billing@chamasoft.com' . "\r\n".'X-Mailer: PHP/' . phpversion());
                                                @mail('edwin.kapkei@digitalvision.co.ke','Auto reconcile withdrawal',$message,'From: Safaricom Files <notifications@chamasoft.com>' . "\r\n" .'Reply-To: billing@chamasoft.com' . "\r\n".'X-Mailer: PHP/' . phpversion());
                                                echo 'done';
                                                 die;
                                            }else{
                                                echo $this->session->flashdata('error');
                                                die;
                                            }  
                                        }else{
                                            echo 'insert transaction alert failed';
                                            die;
                                        }
                                    }else{
                                        echo 'online_banking_transaction_alert_id failed';
                                        die;
                                    }
                                }  
                            }else{
                                echo $this->session->flashdata('error');
                                die;
                            }
                        }else{
                            echo 'insert transaction alert failed';
                             die;
                        }  
                    }else{
                        echo 'online_banking_transaction_alert_id failed';
                         die;
                    }
                }
            }else{
                echo 'is_reconcilled failed';
                 die;
            }
        }else{
            echo 'no id failed';
            die;
        }
    }

    function _record_wild_transaction_alert($transaction_id=0,$transaction_date=0,$amount=0,$account_number=0,$transaction_narrative='',$reference_number=''){
        if($transaction_id&&$transaction_date&&$amount&&$account_number&&$transaction_narrative&&$reference_number){
            $input = array(
                'tranCurrency' => 'KES',
                'tranid'=> $transaction_id,
                'tranDate'=>$transaction_date,
                'tranAmount'=>$amount,
                'trandrcr'=> "DR",
                "tranType" => "DR",
                'accid'=>$account_number,
                'tranParticular'=>$transaction_narrative,
                'tranRemarks'=>$transaction_narrative,
                'refNo' => $reference_number,
                'ip_address' => $_SERVER['REMOTE_ADDR']?:'',
                'created_on'=>time(),
            ); 

            if($request = $this->transaction_alerts_m->get_online_banking_transaction($transaction_id)){
                // $update = array(
                //     'tranDate' => time(),
                // );
                // $this->transaction_alerts_m->update_online_checkout_transaction_request($request->id,time());
                print_r($request);
                echo "Duplicate";
                print_r($this->transaction_alerts_m->get_transaction_alert_by_transaction_id($transaction_id));
                 die;
            }else{
                if($online_banking_transaction_alert_id = $this->transaction_alerts_m->insert_online_banking_transaction_alert($input)){
                    $description = "<strong>Transaction ID:</strong>".$transaction_id."<br/>
                                        <strong>Transaction Transaction Type:</strong>DR<br/>
                                        <strong>Transaction Particular:</strong>".$transaction_narrative;
                    $bank_id = $this->banks_m->get_bank_id_by_slug('chamasoft-e-wallet');
                    $input = array(
                        'online_banking_transaction_alert_id'=>$online_banking_transaction_alert_id,
                        'created_on'=>time(),
                        'transaction_id'=>$transaction_id,
                        'type'=>$this->safaricom_transaction_type_options_keys["DR"],
                        'account_number'=>$account_number,
                        'amount'=>valid_currency($amount),
                        'transaction_date'=>$transaction_date,
                        'bank_id'=>$bank_id,
                        'mobile_money_provider_id' => "",
                        'active'=>1,
                        'particulars'=>$transaction_narrative,
                        'ip_address'=>$_SERVER['REMOTE_ADDR']?:'',
                        'description'=>$description,
                        'reconciled' => 0,
                        'is_merged' => 0,
                        'group_members_notified'=>0,
                    );

                    if($charge_transaction_alert_id = $this->transaction_alerts_m->insert($input)){
                        echo 'okay'; 
                    }else{
                        echo 'insert transaction alert failed';
                        die;
                    }
                }else{
                    echo 'online_banking_transaction_alert_id failed';
                    die;
                }
            }
        }else{
            echo 'invalid paramaters';
        }
    }

    function forward_transaction_alerts($limit = 1){
        $transaction_alert_forwards_count = $this->transaction_alerts_m->count_transaction_alerts_to_forward();
        echo $transaction_alert_forwards_count." Transaction alerts to be forwarded. <br/>";
        $transaction_alert_forwards = $this->transaction_alerts_m->get_transaction_alerts_to_forward($limit);
        foreach($transaction_alert_forwards as $transaction_alert_forward):
            $data = json_encode($transaction_alert_forward);
            if($response = $this->curl->post_json($data,$transaction_alert_forward->url)){
                if($result = json_decode($response)){
                    if($result->responseCode == 0 || $result->responseCode == 1){
                        $input = array(
                            'is_forwarded' => 1,
                            'response' => $response,
                            'modified_on' => time()
                        );
                        if($this->transaction_alerts_m->update_transaction_alert_forward($transaction_alert_forward->id,$input)){
                            echo "Transaction alert forwarded successfully. <br/>";
                        }else{
                            echo "Transaction alert forwarded successfully but could not update transaction alert forward. <br/>";
                        }
                    }else{
                        $input = array(
                            'response' => $response,
                            'modified_on' => time()
                        );
                        if($this->transaction_alerts_m->update_transaction_alert_forward($transaction_alert_forward->id,$input)){
                            echo "Transaction alert forward attempted but unexpected response received. <br/>";
                        }else{
                            echo "Transaction alert forward attempted but unexpected response received and could not update transaction alert forward. <br/>";
                        }
                    }
                }else{
                    echo "Could not decode reesponse from ".$transaction_alert_forward->url.". <br/>";
                }
            }else{
                echo "Forward failed.<br/>";
            }

        endforeach;
    }

    function fix_reconciled_alerts($group_id = 0){
        $this->group_partner_bank_account_number_list = $this->bank_accounts_m->get_group_verified_bank_account_number_list($group_id);
        $bank_account_options = $this->bank_accounts_m->get_group_verified_partner_bank_account_options($group_id);
        $reconciled_deposits = $this->transaction_alerts_m->get_group_reconciled_deposits($this->group_partner_bank_account_number_list);
        $i = 0;
        if($reconciled_deposits){
            foreach ($reconciled_deposits as $reconciled_deposit) {
                $filter_parameters=array(
                    'transaction_alert_id' => $reconciled_deposit->id,
                );
                $posts = $this->deposits_m->get_group_deposits($group_id,$filter_parameters);
                if($posts){

                }else{
                    $input = array(
                        'reconciled'=>0,
                        'modified_on'=>time()
                    );
                    if($this->transaction_alerts_m->update($reconciled_deposit->id,$input)){
                        ++$i;
                    }
                }
            }
        }

        $reconciled_withdrawals = $this->transaction_alerts_m->get_group_reconciled_withdrawals($this->group_partner_bank_account_number_list);

        if($reconciled_withdrawals){
            foreach ($reconciled_withdrawals as $reconciled_withdrawal) {
                $filter_parameters=array(
                    'transaction_alert_id' => $reconciled_withdrawal->id,
                );
                $w_posts = $this->withdrawals_m->get_group_withdrawals($filter_parameters,$group_id);
                if($w_posts){

                }else{
                    $input = array(
                        'reconciled'=>0,
                        'modified_on'=>time()
                    );
                    if($this->transaction_alerts_m->update($reconciled_withdrawal->id,$input)){
                        ++$i;
                    }
                }
            }
        }
        echo "Fixed ".$i;
    }

    function export_accounts(){
       $importer_array = array(
            'banks' => array(),
            'saccos' => array(),
            'mobile_money_providers' => array(),
        );
        $banks = $this->banks_m->get_all();
        foreach ($banks as $bank) {
            $bank->bank_branches = $this->bank_branches_m->get_all($bank->id);
            unset($bank->id);
            $bank->is_wallet = $bank->wallet;
            unset($bank->wallet);
            $importer_array['banks'][] = $bank;
        }
        $saccos = $this->saccos_m->get_all();
        foreach ($saccos as $sacco) {
            $sacco->sacco_branches = $this->sacco_branches_m->get_all($sacco->id);
            unset($sacco->id);
            $importer_array['saccos'][] = $sacco;
        }
        $importer_array['mobile_money_providers'] = $this->mobile_money_providers_m->get_all();
        print_r(json_encode($importer_array)); die;
    }


    function test_daraja_portal_c2b_validation(){
        $file = file_get_contents('php://input');
        if($file){
            file_put_contents("logs/test_daraja_portal_c2b_validation.dat",date("d-M-Y h:i A")."\t".$file."\n",FILE_APPEND);
            header('Content-Type: application/json');
            $url = 'https://api.chamasoft.com/safaricom/check_if_valid_account';
            echo $this->curl->post($file,$url);
        }else{
            echo json_encode(array(
                "ResultDesc" => "No File",
                "ResultCode" => "1"
            ));
        }
    }

    function test_daraja_portal_c2b_confirmation(){
        $file = file_get_contents('php://input');
        if($file){
            file_put_contents("logs/test_daraja_portal_c2b_confirmation.dat",date("d-M-Y h:i A")."\t".$file."\n",FILE_APPEND);
            header('Content-Type: application/json');
            $url = 'https://api.chamasoft.com/safaricom/record_direct_account_payment';
            echo $this->curl->post($file,$url);
        }else{
            echo json_encode(array(
                "ResultDesc" => "No File",
                "ResultCode" => "1"
            ));
        }
    }

    function daraja_stk_payment_callback(){
        $file = file_get_contents('php://input');
        if($file){
            file_put_contents("logs/daraja_stk_payment_callback.dat",date("d-M-Y h:i A")."\t".$file."\n",FILE_APPEND);
            header('Content-Type: application/json');
            $url = 'https://api.chamasoft.com/safaricom/record_stk_push_account_payment';
            echo $this->curl->post($file,$url);
        }else{
            echo json_encode(array(
                "ResultDesc" => "No File",
                "ResultCode" => "1"
            ));
        }
    }

    function daraja_funds_disbursement_callback(){
        $file = file_get_contents('php://input');
        if($file){
            file_put_contents("logs/daraja_funds_disbursement_callback.dat",date("d-M-Y h:i A")."\t".$file."\n",FILE_APPEND);
            @mail("geoffrey.githaiga@digitalvision.co.ke","daraja_funds_disbursement_callback",$file,$this->headers);
            header('Content-Type: application/json');
            header('Content-Type: application/json');
            $url = 'https://api.chamasoft.com/safaricom/record_disbursement';
            echo $this->curl->post($file,$url);
        }else{
            echo json_encode(array(
                "ResultDesc" => "No File",
                "ResultCode" => "1"
            ));
        }
    }

    function daraja_funds_reversal_callback(){
        $file = file_get_contents('php://input');
        if($file){
            file_put_contents("logs/daraja_funds_reversal_callback.dat",date("d-M-Y h:i A")."\t".$file."\n",FILE_APPEND);
            header('Content-Type: application/json');
            $url = 'https://api.chamasoft.com/safaricom/record_transaction_reversal';
            echo $this->curl->post($file,$url);
        }else{
            echo json_encode(array(
                "ResultDesc" => "No File",
                "ResultCode" => "1"
            ));
        }
    }

    function daraja_business_to_business_transfer_callback(){
        $file = file_get_contents('php://input');
        $this->headers = 'From: Safaricom Files <notifications@chamasoft.com>' . "\r\n" .
                    'Reply-To: billing@chamasoft.com' . "\r\n".
                    'X-Mailer: PHP/' . phpversion();
        if($file){
            file_put_contents("logs/daraja_funds_b2b_disbursement_callback.dat",date("d-M-Y h:i A")."\t".$file."\n",FILE_APPEND);
            @mail("geoffrey.githaiga@digitalvision.co.ke","daraja_business_to_business_transfer_callback",$file,$this->headers);
            header('Content-Type: application/json');
            $url = 'https://api.chamasoft.com/safaricom/record_organization_settlement';
            echo $this->curl->post($file,$url);
        }else{
            echo json_encode(array(
                "ResultDesc" => "No File",
                "ResultCode" => "1"
            ));
        }
    }

    function transaction_alert_statistics(){
        $bank_accounts = $this->bank_accounts_m->get_verified_partner_bank_accounts_with_transaction_alerts();

        $group_ids = array();
        $account_numbers = array();
        foreach($bank_accounts as $bank_account):
            if(in_array($bank_account->group_id,$group_ids)){

            }else{
                $group_ids[] = $bank_account->group_id;
            }
        endforeach;
        foreach($bank_accounts as $bank_account):
            if(in_array($bank_account->account_number,$account_numbers)){

            }else{
                $account_numbers[] = $bank_account->account_number;
            }
        endforeach;

        $total_transactions_amount = $this->transaction_alerts_m->get_total_transactions_amount(0,$account_numbers);


        $total_deposit_transactions_amount = $this->transaction_alerts_m->get_total_deposit_transactions_amount(0,$account_numbers);
       //print_r($this->data['partner_bank_accounts']);
        $total_withdrawal_transactions_amount = $this->transaction_alerts_m->get_total_withdrawal_transactions_amount(0,$account_numbers);


       $total_deposits_by_month_array = $this->transaction_alerts_m->get_total_deposits_by_month_array(0,$account_numbers);
       $total_withdrawals_by_month_array = $this->transaction_alerts_m->get_total_withdrawals_by_month_array(0,$account_numbers);

        echo "Groups: ".count($group_ids)."<br/>";
        echo "Accounts: ".count($account_numbers)."<br/>";
        echo "Total Transaction Amount: ".number_to_currency($total_transactions_amount)."<br/>";
        echo "Deposit Transaction Amount: ".number_to_currency($total_deposit_transactions_amount)."<br/>";
        echo "Withdrawal Transaction Amount: ".number_to_currency($total_withdrawal_transactions_amount)."<br/>";
    }

    function fix_transaction_alerts_fields(){
        $transaction_alerts = $this->transaction_alerts_m->fix_transaction_alerts_fields();
    }


    function fix_duplicate_transaction_alert($account_number = 0){
        $fix_count = 0;
        $exempt_ids = [];        
        if($account_number){
            $alerts = $this->transaction_alerts_m->find_duplicate_transaction_alerts_entries_with_limit($account_number);
            if($alerts){
                $transaction_date_arr = [];
                $amount_arr = [];
                $type_arr = [];
                foreach ($alerts as $key => $alert) {
                    $exempt_ids[] = $alert->id;
                    $transaction_date_arr[] = $alert->transaction_date ;
                    $amount_arr[] = $alert->amount;
                    $type_arr[] = $alert->type;                    
                }
                $duplicate_alerts = $this->transaction_alerts_m->get_duplicate_uneconciled_alert_entries($transaction_date_arr,
                    $amount_arr,$type_arr,$account_number);
                foreach ($duplicate_alerts as $key => $alert_):
                    if(in_array($alert_->id, $exempt_ids)){

                    }else{
                        $input = array(
                            'active' => 0,
                            'modified_by' => 1,
                            'modified_on' => time(),
                        );
                        $fix_count++;
                        if($this->transaction_alerts_m->update($alert_->id,$input)){
                            echo "Success. <br/>";
                        }
                    }
                endforeach;
            }
        }
    }


    
} 
