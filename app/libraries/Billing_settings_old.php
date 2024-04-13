<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Billing_settings{
	protected $ci;

    public $payment_methods = array(
        1 => 'Cash',
        2 => 'Mpesa',
        3 => 'Equitel',
        4 => 'Cheque',
        5 => 'Paypal',
        6 => 'Coupon',
        7 => 'MTN',
    );

    public $billing_cycles = array();

    public function __construct(){
        $this->ci= & get_instance();
        $this->ci->load->model('billing/billing_m');
        $this->ci->load->model('groups/groups_m');
    }

    public function create_invoice($group_id = '',$billing_package_id = '',$billing_date = '',$due_date = '',$disable_prorating = '',$billing_cycle = '',$num_of_months = '',$num_of_quarters = '',$num_of_years = ''){
        if($group_id && $billing_package_id && $billing_date && $due_date && $disable_prorating && $billing_cycle && $num_of_months && $num_of_quarters  && $num_of_years){
            
            $billing_invoice = array(
                'group_id' => $group_id,
                'billing_package_id' => $billing_package_id,
                'billing_date' => $billing_date,
                'due_date' => $due_date,
                'disable_prorating' => $disable_prorating,
                'billing_cycle' => $billing_cycle,
                'num_of_months' => $num_of_months,
                'num_of_quarters' => $num_of_quarters,
                'num_of_years' => $num_of_years,
            );

            $result = $this->ci->billing_m->insert_billing_invoice($billing_invoice);

            return $result;

        }
        
        
    }

    public function calculate_amount_payable($group_id = '',$billing_package_id = '',$cycle = '',$cycle_period = ''){
        $amount_payable = 0;
        if($group_id && $billing_package_id && $cycle && $cycle_period){
            $billing_package = $this->ci->billing_m->get_package($billing_package); 
            $group_members = $this->ci->groups_m->members_in_group($group_id);
            if($cycle == 1){ // monthly
                $amount_payable = $billing_package->monthly_amount * $cycle_period;
                if($group_members > $billing_package->members_limit && $billing_package->enable_extra_member_charge){
                    $members_over = $group_members - $billing_package->members_limit;
                    $amount_payable += ($members_over * $billing_package->monthly_pay_over);
                }
            }else if($cycle == 2){ // quartely
                $amount_payable = $billing_package->quarterly_amount * $cycle_period;
                if($group_members > $billing_package->members_limit && $billing_package->enable_extra_member_charge){
                    $members_over = $group_members - $billing_package->members_limit;
                    $amount_payable += ($members_over * $billing_package->quarterly_pay_over);
                }
            }else if($cycle == 3){ // annually
                $amount_payable = $billing_package->annual_amount * $cycle_period;
                if($group_members > $billing_package->members_limit && $billing_package->enable_extra_member_charge){
                    $members_over = $group_members - $billing_package->members_limit;
                    $amount_payable += ($members_over * $billing_package->annual_pay_over);
                }
            }
            if($billing_package->enable_tax){ // charge tax
                $amount_payable +=  ( ($billing_package->percentage_tax / 100) * $amount_payable );
            }
        }
        return $amount_payable;        
    }

    public function manage_prorating($invoice_id){
        
    }

    public function group_next_billing_date($group_id = ''){

        $next_billing_date = '';

        if($group_id){

            $group_invoices = $this->ci->billing_m->get_group_invoices($group_id,TRUE);
            $invoice = $group_invoices[0];
            
            if($invoice->cycle == 1){ // Monthly
                $date_today = date("Y-m-d");
                $next_billing_date = date('Y-m-d',strtotime("+1 month",$date_today));
            }else if($invoice->cycle == 2){ // Quartely
                $date_today = date("Y-m-d");
                $next_billing_date = date('Y-m-d',strtotime("+3 months",$date_today));
            }else if($invoice->cycle == 3){ // Annually
                $date_today = date("Y-m-d");
                $next_billing_date = date('Y-m-d',strtotime("+1 year",$date_today));
            }
        }
        
        return $next_billing_date;
    }


    function record_billing_payment($amount=0,$group_id=0,$receipt_date=0,$payment_method=1,$ipn_transaction_code='',$description='',$created_by=0,$purchase_sms=TRUE,$sms_notify=TRUE,$email_notify=TRUE){
        if($group_id&&$amount&&$receipt_date&&$payment_method){
            $group_owner = $this->ci->groups_m->get_group_owner($group_id);
            if($group_owner->status == '' || $group_owner->status == 0){
                $this->subscribe_group($group_id);
            }
            if(!$package = $this->ci->billing_m->get_package($group_owner->billing_package_id)){
                $package = $this->ci->billing_m->get_default_package();
            }
            if(!$package){
                $this->ci->session->set_flashdata('error','No package found');
                return FALSE;
            }
            $tax = 0;
            if($package->enable_tax){
                $percentage_tax = $package->percentage_tax;
                if($percentage_tax){
                    $tax = round(($amount*($percentage_tax)/(100+$percentage_tax)),2);
                }
            }
            $id = $this->ci->billing_m->insert_payments(array(
                'group_id' => $group_id,
                'receipt_date' => $receipt_date,
                'billing_receipt_number' => 'RCPT-'.rand(10000,9999999),
                'amount' => currency($amount),
                'tax' => currency($tax),
                'payment_method'=> $payment_method,
                'active'=>1,
                'description' => $description,
                'ipn_transaction_code' => $ipn_transaction_code,
                'created_on' => time(),
                'created_by' => $created_by,
            ));
            $this->update_invoices($group_id);
            if($id){
                return $id;
            }else{
                $this->ci->session->set_flashdata('error','Unable to record billing payment.');
                return FALSE;
            }

        }else{
            $this->ci->session->set_flashdata('Some essential parameters are missing.');
            return FALSE;
        }

    }


    function _update_invoices($group_id=0){
        if($group_id){
            $invoices = $this->ci->billing_m->get_group_invoices($group_id,TRUE);
            $total_amount_paid = $this->ci->billing_m->get_group_billing_paid_amount($group_id);
            if($invoices){
                $amount = 0;
                $amount_paid = $total_amount_paid?:0;
                $invoice_amount_paid = 0;
                foreach ($invoices as $invoice){
                    $amount_payable = $invoice->amount?:0;
                    if($amount_payable<=$amount_paid){
                        if($this->ci->billing_m->update_invoice($invoice->id,array('amount_paid'=>$amount_payable,'status'=>'1'))){   
                            $amount_paid-=$amount_payable; 
                        }
                    }else if($amount_paid>0&&$amount_paid<$amount_payable){
                        if($this->ci->billing_m->update_invoice($invoice->id,array('amount_paid'=>$amount_paid,'status'=>NULL))){  
                            $amount_paid = 0;
                        }
                    }else{
                        if($this->ci->billing_m->update_invoice($invoice->id,array('amount_paid'=>0,'status'=>NULL))){
                            continue;
                        }
                    }
                }
                return TRUE;
            }else{
                return TRUE;
            }

        }else{
            $this->session->set_flashdata('error','Group id must be passed');
            return FALSE;
        }
    }


}