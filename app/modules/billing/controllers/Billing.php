<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Billing extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('billing_m');
        $this->load->model('groups/groups_m');
        $this->load->library('billing_settings');
        $this->load->library('investment_groups');
    }    

    function alter_trial_days(){
        $this->billing_settings->alter_trial_days();
    }

    function automated_group_billing_invoice($date=0,$limit=0){
        $this->billing_settings->automated_group_billing_invoice($date,$limit);
    }

    function duplicate_automated_group_billing_invoice($date=0,$limit=0){
        return $this->billing_settings->duplicate_automated_group_billing_invoice($date,$limit);
    }

    function update_group_subscription_status($group_id=0){        
        $this->billing_settings->update_group_subscription_status($group_id);
    }

    function backdate_group_billing_invoices($startdate = 0,$enddate = 0){
        set_time_limit(0);
        ini_set('memory_limit','4096M');
        ini_set('max_execution_time', 4800);
        $startdate = $startdate?:strtotime("1st June 2017");
        $enddate = $enddate?:time();
        for ($i=$startdate; $i <= $enddate; $i++) { 
               $groups = $this->duplicate_automated_group_billing_invoice($i,30);
               if($groups){
                    file_put_contents("logs/backdated_groups.txt","\n".'Date: '.date("d-M-Y h:i A",$i)."\t Groups: ".serialize($groups)."\n",FILE_APPEND);
               }
        }
        echo 'done';
    }

    function create_notification_for_insufficient_group_sms(){
        $this->billing_settings->create_notification_for_insufficient_group_sms();
    }

    function price_calculator($billing_cycle=0,$number_of_members=0){
        if(isset($_POST) && !empty($_POST['payment_plan'])){
            $billing_cycle = $this->input->post('payment_plan');
            if($number_of_members){

            }else{
                $number_of_members = $this->input->post('number_of_members');
            }
        }
        $result = $this->billing_settings->get_amount_payable('',$billing_cycle,'',TRUE,$number_of_members);
        if(isset($_POST)){
            echo json_encode($result);
        }else{
            print_r('<pre>');
            print_r($result);
            print_r('</pre>');
        }
        
    }

    function count_invoices($group_id=0){
        echo $this->billing_settings->count_unpaid_billing_invoices($group_id);
    }

    function billing_account_number(){
        $groups = $this->billing_settings->setting_account_number_error();
    }

    function billing_account_exists(){
        $file = file_get_contents('php://input');
        if($file){
            $file = json_decode($file);
            $response = $this->billing_settings->is_account_number_recognized($file->account_number,$file->ipn_depositor);
            if($response){
                $result = array('exists'=>'1','file'=>'yes');
            }else{
                $result = array('exists'=>'0','file'=>'yes');
            }
        }else{
            $result = array('exists'=>'0','file'=>'no');
        }
        $result = (object)$result;
        echo json_encode($result);
    }


    function set_default_permissions($group_id=0,$user_id=0){
        $this->investment_groups->set_default_permissions($group_id,$user_id);
    }


    function run_previous_billing($from=0,$to = 0){
        if($to>time())
            $to=time();
        if($from<$to){
            $days = ($to-$from)/(86400) + 1;
            for($i=0;$i<$days;$i++){
                //echo date('d-m-Y',strtotime('+'.$i.'days',$from)).'<br/>';
                echo $this->automated_group_billing_invoice(strtotime('+'.$i.'days',$from)).'<br/>';
            }
        }else{
            echo 'error';
        }

    }

 }