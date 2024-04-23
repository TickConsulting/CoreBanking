<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mobile extends Mobile_Controller{

    function __construct(){
        parent::__construct();
        $this->load->library('billing_settings');
    }

    public function _remap($method, $params = array()){
        if(method_exists($this, $method)){
            return call_user_func_array(array($this, $method), $params);
        }
        $this->output->set_status_header('404');
        header('Content-Type: application/json');
        $file = file_get_contents('php://input')?(array)json_decode(file_get_contents('php://input')):array();
        $request = $_REQUEST+$file;
        echo json_encode(
        array(
            'response' => array(
                'status'    =>  404,
                'message'       =>  '404 Method Not Found for URI: '.$this->uri->uri_string(),
            )

        ));
    }

    
    function get_group_billing_invoices(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->group_role_id || $this->member->is_admin){
                        $invoices = $this->billing_m->get_all_active_group_invoices($this->group->id);
                        $posts = array();
                        $summary = array();
                        if($invoices){
                            $total_subscription = 0; 
                            $total_tax = 0; 
                            $total_prorated_amount = 0; 
                            $total_amount = 0; 
                            $total_balance = 0;
                            foreach ($invoices as $invoice) {
                                if($invoice->status){
                                    $status = 'Paid';
                                }else{
                                    $status = 'Unpaid';
                                }
                                $posts[] = array(
                                    'id' => $invoice->id,
                                    'due_date' => timestamp_to_mobile_shorttime($invoice->due_date),
                                    'subscription' => ($subscription = ($invoice->amount-$invoice->tax-$invoice->prorated_amount)),
                                    'title' => $this->billing_settings->billing_cycle[$invoice->billing_cycle],
                                    'tax' => ($tax = $invoice->tax),
                                    'prorated_amount' => ($prorated_amount = $invoice->prorated_amount),
                                    'amount' => ($amount = $invoice->amount),
                                    'balance' => ($balance = $invoice->amount - $invoice->amount_paid),
                                    'status' => $status,
                                );
                                $total_subscription+=$subscription;
                                $total_tax+=$tax;
                                $total_prorated_amount+=$prorated_amount;
                                $total_amount+=$amount;
                                $total_balance+=$balance;
                            }

                            $summary = array(
                                'total_subscription' => $total_subscription,
                                'total_tax' => $total_tax,
                                'total_prorated_amount' => $total_prorated_amount,
                                'total_amount' => $total_amount,
                                'total_balance' => $total_balance,
                            );
                        }
                        $response = array(
                            'status' => 1,
                            'message' => 'Group billing invoices',
                            'invoices' => $posts,
                            'summary' => $summary,
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
    }

    function get_group_billing_payments(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->group_role_id || $this->member->is_admin){
                        $payments = $this->billing_m->get_all_active_group_payments($this->group->id);
                        $posts = array();
                        $summary = array();
                        if($payments){
                            $total_amount = 0;
                            $total_tax = 0;
                            foreach ($payments as $payment) {
                                $posts[] = array(
                                    'id' => $payment->id,
                                    'date' => timestamp_to_mobile_shorttime($payment->receipt_date),
                                    'receipt_number' => $payment->billing_receipt_number,
                                    'tax' => ($total_tax = $payment->tax),
                                    'amount' => ($total_amount = $payment->amount),
                                    'payment_method' => $this->billing_settings->payment_method[$payment->payment_method],
                                    'transaction_code' => $payment->ipn_transaction_code,
                                );
                            }
                            $summary = array(
                                'total_amount' => $total_amount,
                                'total_tax' => $total_tax,
                            );
                        }
                        $response = array(
                            'status' => 1,
                            'message' => 'Group payment receipts',
                            'payments' => $posts,
                            'summary' => $summary,
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
    }

    function get_group_amount_payables(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $billing_payments = $this->billing_settings->get_amount_payable('','',$this->group->id,TRUE);
                    $response = array(
                        'status' => 1,
                        'message' => 'Success',
                        'payments' => $billing_payments,
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
    }
}