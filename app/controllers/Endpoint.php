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

}