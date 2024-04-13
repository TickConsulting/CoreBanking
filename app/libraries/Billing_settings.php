<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Billing_settings{

    public $default_country;

	protected $ci;
    
    protected $paybills = array(
        '967600' => array(
            'passkey' => '34164b9e845bd6deb189dba77fd24c43459628e172ccc03e2ddacae7830aa2b1',
            'timestamp' => '20170901154023',
            'environment' => 'Production',
            'initiator_password' => 'KKihenju2015!!C$sH',
            'username' => "chamasoftInit",

        ),
    );

    public $subscription_statuses = array(
        1 =>    'Group on ongoing trials',
        2 =>    'Group not subscribed but trial expired',
        3 =>    'Paying group, all invoices paid - Active groups',
        4 =>    'Paying group, has unpaid invoice which is not yet due date',
        5 =>    'Paying group but invoice overdue',
        6 =>    'Group in arrears and locked but has some free trial days',
        7 =>    'Group has been temporary suspended and no one can login',
        8 =>    'Groups locked',
    );
    /***
    1. Default billing package
    2. Discounted billing package
    3 Percentage of monthly contribution billing package
    4. Special offer billing package

    ***/
    
    public $billing_type = array(
            1 => 'Fixed Amount',
            2 => 'Percentage',
            3 => 'Modulated Billing'
        );

    public $billing_percentage_on = array(
            1 => 'Per Member Average Monthly Contribution',
            2 => 'Total Group Monthly Contribution',
        );

    public $billing_cycle = array(
            1 => 'Monthly',
            2 => 'Quarterly',
            3 => 'Annual'
        );

    public $payment_method = array(
            1 => 'Cash',
            2 => 'Mpesa',
            3 => 'Equitel',
            4 => 'Cheque',
            5 => 'Paypal',
            6 => 'Coupon',
            7 => 'MTN',
        );
    protected $chamasoft_settings;

    protected $allowed_activation_urls = array(
        'group/activate',
        'group/resend_activation_code',
        'group/change_email_address',
        'group/change_phone_number',
        'group/wallets'
    );

    protected $allowed_trial_expired_urls = array(
        'group/billing/billing_information',
        'group/billing/invoice',
        'group/billing/receipt',
        'group/billing/calculate_conversion',
        'group/billing/receive_complete_billing',
        'group/wallets'
    );

    protected $allowed_suspended_group_urls = array(  
        'group/billing/billing_information',
    );

    public $ipn_depositor = array(
        '1' => 'Equity',
        '2' => 'MPESA ',
        '3' => 'PAYPAL',
        '4' => 'MTN',
    );
    public $ipn_status = array(
        '1' => 'Successfull',
        '2' => 'Parameters are missing',
        '3' => 'Unable to determine group from the account number paid to',
        '4' => 'Unable to purchase SMS for determined group',
        '5' => 'FATEL ERROR - Coundn\'t create an IPN entry. ',
        '6' => 'Billing payment error',
        '7' => 'Payment failed confirmation',
    );
    /****
        Login Actions
        1. Trials are active
        2. Trials are expired and not subscribed
        3. Has deficit but has trial days
        4. Within the due date period for invoice 
        5. Has a deficit but had trial days
        6. Has a deficit but had trial days
        7. Has no arrears, has trial days and not invoice
        8. Has not deficit has no trial days - member is active
    ****/

    public function __construct(){
        $this->ci= & get_instance();
        $this->ci->load->model('billing/billing_m');
        $this->ci->load->model('settings/settings_m');
        $this->ci->load->model('sms/sms_m');
        $this->ci->load->model('contributions/contributions_m');
        $this->ci->load->model('groups/groups_m');
        $this->ci->load->model('countries/countries_m');
        $this->ci->load->model('ipn_m');
        $this->ci->load->library('ion_auth');
        $this->ci->load->library('messaging');
        $this->ci->load->library('member_notifications');
        $this->ci->load->library('curl');
        $this->default_country = $this->ci->countries_m->get_default_country();
        if(!isset($_SESSION['application_settings'])){
            $sessions = $this->ci->settings_m->get_settings();
           $this->ci->session->set_userdata('application_settings',$sessions);
        }
        $this->chamasoft_settings = $this->ci->session->userdata('application_settings');
    }

    function get_amount_payable($billing_package_id=0,$billing_cycle=0,$group_id=0,$billing_calc=FALSE,$number_of_members=0,$is_prorated = FALSE,$billing_date=0,$show_arrears = FALSE){
        $billing_date= $billing_date?:time();
        if($billing_cycle&&$billing_package_id){
            $package  = $this->ci->billing_m->get_package($billing_package_id);
            if($package && $package->active){
                //package id found
                $old_billing_cycle = $billing_cycle;
                $result = array();
                $amount = 0;
                $tax = 0;
                $plan_amount = 0;
                $past_ten_members_amount = 0;
                $group_active_size = $this->ci->groups_m->get_group_active_size($group_id);
                if($group_active_size>20){
                    if($package->enable_extra_member_charge){
                        $extra_members = $group_active_size-20;
                        $extra_monthly_amount = ($extra_members*($package->monthly_pay_over));
                        $extra_quarterly_amount = ($extra_members*($package->quarterly_pay_over));
                        $extra_annual_amount = ($extra_members*($package->annual_pay_over));
                    }else{
                        $extra_monthly_amount=0;
                        $extra_quarterly_amount = 0;
                        $extra_annual_amount = 0;
                    }
                }
                else{
                    $extra_monthly_amount=0;
                    $extra_quarterly_amount = 0;
                    $extra_annual_amount = 0;
                }
                if($package->billing_type==1){
                    //fixed amount
                    if($billing_cycle==1){
                        //monthly payment
                        $amount = $package->monthly_amount+$extra_monthly_amount;
                        $plan_amount = $package->monthly_amount;
                        $past_ten_members_amount = $extra_monthly_amount;
                    }else if($billing_cycle==2){
                        //quarterly payment
                        $amount = $package->quarterly_amount+$extra_quarterly_amount;
                        $plan_amount = $package->quarterly_amount;
                        $past_ten_members_amount = $extra_quarterly_amount;
                    }
                    else if($billing_cycle==3){
                        //annual payment
                        $amount = $package->annual_amount+$extra_annual_amount;  
                        $plan_amount = $package->annual_amount;
                        $past_ten_members_amount = $extra_annual_amount;                      
                    }
                    else{
                        $this->ci->session->set_flashdata('error','Invalid billing cycle sent');
                        return FALSE;
                    }
                }else if($package->billing_type == 2){
                    //percentage payable
                    if($group_id){
                        $contributions = $this->ci->contributions_m->get_group_regular_contributions($group_id);
                        if($contributions){
                            $total_amount = 0;
                            $total_members = 0;
                            $average_members=0;
                            $i = 0;
                            //average per member contribution
                            foreach ($contributions as $value) {
                                $value = (object)$value;
                                $total_members+= $value->members;
                                if($value->frequency==1){
                                    $total_amount+= ($value->amount)*$value->members;
                                }else if($value->frequency==2){
                                    $total_amount+= (($value->amount)/2)*$value->members;
                                }else if($value->frequency==3){
                                    $total_amount+= (($value->amount)/3)*$value->members;
                                }else if($value->frequency==4){
                                    $total_amount+= (($value->amount)/6)*$value->members;
                                }else if($value->frequency==5){
                                    $total_amount+= (($value->amount)/12)*$value->members;
                                }else if($value->frequency==6){
                                    $total_amount+= (($value->amount)*4)*$value->members;
                                }
                                ++$i;
                            }
                            $average_members = $total_members/$i;
                            if($package->rate_on==1 && $total_amount){
                                //average member contribution
                                $amount = ($total_amount/$average_members)*($package->rate/100);

                            }else if($package->rate_on==2 && $total_amount){
                                //total group contribution
                                $amount = ($total_amount)*($package->rate/100);
                            }
                            else{
                                $this->get_amount_payable('default',$billing_cycle);
                            }
                            if($billing_cycle==1){
                                $amount = $amount;
                            }else if($billing_cycle==2){
                                $amount = $amount*3;
                            }else if($billing_cycle==3){
                                $amount = $amount*12;
                            }
                            $plan_amount = $amount;
                            $past_ten_members_amount = 0;
                        }else{
                            $this->get_amount_payable('default',$billing_cycle);
                        }
                    }else{
                        $this->get_amount_payable('default',$billing_cycle);
                    }
                }else if($package->billing_type == 3){
                    $plans = $this->ci->billing_m->get_billing_package_plans($package->id);
                    $number_of_members = $group_active_size?:$number_of_members;
                    foreach ($plans as $key => $plan) {
                        if($key ==($billing_cycle-1)){
                            $selected_plan = $plan;
                            break;
                        }
                    }
                    if($selected_plan){
                        $amount = $selected_plan->charge_amount;
                        $billing_cycle = $selected_plan->cycle;
                        $extra_amount= 0;
                        if($number_of_members > $selected_plan->user_limits){
                            $extra_members = $number_of_members - $selected_plan->user_limits;
                            $extra_amount = ($extra_members * $selected_plan->extra_member_charge_amount);
                            if($billing_cycle==1){
                            }elseif ($billing_cycle==2) {
                                $extra_amount = $extra_amount*3;
                            }else if($billing_cycle == 3){
                                $extra_amount = $extra_amount*12;
                            }
                            $amount = $amount+$extra_amount;
                        }
                        $plan_amount = $selected_plan->charge_amount;
                        $past_ten_members_amount = $extra_amount;
                    }else{
                        $this->ci->session->set_flashdata('error','Invalid billing cycle sent');
                        return FALSE;
                    }
                }
                $prorated_amount = 0;
                $remaining_days = 0;
                if($is_prorated){
                    $end_month = date("Y-m-t",$billing_date);
                    $date_and_month = date('M Y',$billing_date);
                    $daily_amount = 0;
                    if($billing_cycle == 1){
                        $daily_amount = $amount/30;
                    }elseif ($billing_cycle ==2) {
                        $daily_amount = $amount/(30*3);
                    }elseif ($billing_cycle ==3) {
                        $daily_amount = $amount/(365);
                    }
                    if(date('dmY',$billing_date) == date('dmY',strtotime('first day of '.$date_and_month))){
                    }else if(date('dmY',$billing_date) == date('dmY',strtotime($end_month))){
                        $remaining_days = 0.5;
                    }else{
                        $remaining_days =  (preg_replace('/\D/', '', daysAgo($billing_date,strtotime($end_month)))+1);
                    }
                    if($billing_cycle==1){
                        if($remaining_days >=15){
                            $amount = $remaining_days * $daily_amount;
                        }else{
                            $prorated_amount = $remaining_days * $daily_amount;
                        }
                    }else{
                        $prorated_amount = $remaining_days * $daily_amount;
                    }
                    
                }
                if($amount || $package->monthly_amount==0){
                    $percentage_tax = 0;
                    if($package->enable_tax){
                        $percentage_tax = $package->percentage_tax;
                        if($percentage_tax){
                            $tax = (($percentage_tax/100)*($amount+$prorated_amount));
                            $tax = round($tax,2);
                        }else{
                            $tax = 0;
                        }
                    }else{
                        $tax = 0;
                    }
                    $arrears = $this->ci->billing_m->get_group_account_arrears($group_id);
                    if($show_arrears && ($arrears>0)){
                        $tax = (($arrears*$percentage_tax)/(100+$percentage_tax));
                        $result = array(
                            'amount' => ($arrears-$tax),
                            'prorated_amount' => 0,
                            'remaining_days' => $remaining_days,
                            'tax' => $tax,
                            'billing_package_id'=>$package->id,
                            'percentage_tax'=>$percentage_tax,
                            'account_arrears' => $arrears,
                            'billing_cycle' => $billing_cycle,
                            'overpayments' => 0,
                            // 'billing_date' => timestamp_to_report_time($billing_date),
                            'billing_date' => timestamp_to_report_time($this->ci->group->billing_date),
                            'next_biling_date' => timestamp_to_report_time($this->_next_biling_date($old_billing_cycle,$remaining_days,$billing_date,$package)),
                            'plan_amount' => intval($plan_amount),
                            'past_ten_members_amount' => $past_ten_members_amount,
                        );
                    }else{
                        $overpayments = 0;
                        if($arrears < ($amount+$prorated_amount+$tax)){
                            $overpayments = abs($arrears);
                        }
                        $result = array(
                            'amount' => round($amount),
                            'prorated_amount' => round($prorated_amount),
                            'remaining_days' => $remaining_days,
                            'tax' => round($tax),
                            'billing_package_id'=>$package->id,
                            'percentage_tax'=>$percentage_tax,
                            'account_arrears' => $this->ci->billing_m->get_group_account_arrears($group_id),
                            'overpayments' => $overpayments,
                            'billing_cycle' => $billing_cycle,
                            'billing_date' => timestamp_to_report_time($billing_date),
                            'next_biling_date' => timestamp_to_report_time($this->_next_biling_date($old_billing_cycle,$remaining_days,$billing_date,$package)),
                            'plan_amount' => intval($plan_amount),
                            'past_ten_members_amount' => $past_ten_members_amount,
                        );
                    }
                    $result = (object)$result;
                    return $result;
                }else{
                    $this->ci->session->set_flashdata('error','Returned false values. Kindly review the package');
                    return FALSE;
                }
            }else{
                //no such package or not active thus use the default package
                $package = $this->ci->billing_m->get_default_package();
                if($package){
                    return($this->get_amount_payable($package->id,$billing_cycle,$group_id,$billing_calc,$number_of_members));
                }else{
                    $this->ci->session->set_flashdata('error','Package not found and also no default package found');
                    return FALSE;
                }
            }
        }else if($billing_calc){
            $package = $this->ci->billing_m->get_package($billing_package_id);
            if($package && $package->active){
                $monthly_amount=0;
                $monthly_tax=0;
                $quarterly_amount=0;
                $quarterly_tax=0;
                $annual_amount=0;
                $annual_tax=0;
                $monthly_smses = $package->monthly_smses;
                $quarterly_smses = $package->quarterly_smses;
                $annual_smses = $package->annual_smses;
                $plan_names = array();
                if($package->billing_type==1){
                    //fixed amount
                    $monthly_amount = $package->monthly_amount;
                    $quarterly_amount = $package->quarterly_amount;
                    $annual_amount = $package->annual_amount;
                    if($group_id){
                        if(!$number_of_members){
                            $number_of_members = $this->ci->groups_m->get_group_active_size($group_id);
                        }
                    }
                    if($number_of_members>20){
                        if($package->enable_extra_member_charge){
                            $extra_members = $number_of_members-20;
                            $extra_monthly_amount = ($extra_members*($package->monthly_pay_over));
                            $extra_quarterly_amount = ($extra_members*($package->quarterly_pay_over));
                            $extra_annual_amount = ($extra_members*($package->annual_pay_over));

                            $monthly_amount=$monthly_amount+$extra_monthly_amount;
                            $quarterly_amount = $quarterly_amount+$extra_quarterly_amount;
                            $annual_amount= $annual_amount+$extra_annual_amount;
                        }
                    }
                }else if($package->billing_type==2){
                    //percentage payable
                    if($group_id){
                        $contributions = $this->ci->contributions_m->get_group_regular_contributions($group_id);
                        if($contributions){
                            $total_amount = 0;
                            $total_members = 0;
                            $average_members=0;
                            $i = 0;
                            //average per member contribution
                            foreach ($contributions as $value) {
                                $value = (object)$value;
                                $total_members+= $value->members;
                                if($value->frequency==1){
                                    $total_amount+= ($value->amount)*$value->members;
                                }else if($value->frequency==2){
                                    $total_amount+= (($value->amount)/2)*$value->members;
                                }else if($value->frequency==3){
                                    $total_amount+= (($value->amount)/3)*$value->members;
                                }else if($value->frequency==4){
                                    $total_amount+= (($value->amount)/6)*$value->members;
                                }else if($value->frequency==5){
                                    $total_amount+= (($value->amount)/12)*$value->members;
                                }else if($value->frequency==6){
                                    $total_amount+= (($value->amount)*4)*$value->members;
                                }
                                ++$i;
                            }
                            $average_members = $total_members/$i;
                            if($package->rate_on==1 && $total_amount){
                                //average member contribution
                                $amount = ($total_amount/$average_members)*($package->rate/100);

                            }else if($package->rate_on==2 && $total_amount){
                                //total group contribution
                                $amount = ($total_amount)*($package->rate/100);
                            }
                            else{
                                $this->get_amount_payable('default',$billing_cycle);
                            }
                            $monthly_amount = $amount;
                            $quarterly_amount = $amount*3;
                            $annual_amount = $amount*12;
                        }else{
                            $this->get_amount_payable('default',$billing_cycle);
                        }
                    }else{
                        $this->get_amount_payable('default',$billing_cycle);
                    }
                }else if($package->billing_type==3){
                    $plans = $this->ci->billing_m->get_billing_package_plans($package->id);
                    foreach ($plans as $key => $plan) {
                        $monthly_amount = $plan->charge_amount;
                        $quarterly_amount = $plan->charge_amount;
                        $annual_amount = $plan->charge_amount;
                        $plan_names[] = array(
                            'names' => $plan->plan_names,
                            'amount' => $plan->charge_amount,
                            'cycle' => $plan->cycle,
                            'monthly_smses' => $plan->monthly_smses,
                        );
                    }
                }
                if($package->enable_tax && $package->percentage_tax)
                {
                    $percentage_tax = $package->percentage_tax;
                    if($monthly_amount){
                        $monthly_tax = (($percentage_tax/100)*$monthly_amount);
                        $monthly_tax = round($monthly_tax,2);
                    }
                    if($quarterly_amount){
                        $quarterly_tax = (($percentage_tax/100)*$quarterly_amount);
                        $quarterly_tax = round($quarterly_tax,2);
                    }
                    if($annual_amount){
                        $annual_tax = (($percentage_tax/100)*$annual_amount);
                        $annual_tax = round($annual_tax,2);
                    }
                }
                return array(
                    'monthly_amount' => $monthly_amount,
                    'monthly_tax' => $monthly_tax,
                    'monthly_smses' => $monthly_smses,
                    'total_monthly' => ($monthly_amount+$monthly_tax),
                    'quarterly_amount' => $quarterly_amount,
                    'quarterly_tax' => $quarterly_tax,
                    'quarterly_smses' => $quarterly_smses,
                    'total_quarterly' => ($quarterly_amount+$quarterly_tax),
                    'annual_amount' => $annual_amount,
                    'annual_tax' => $annual_tax,
                    'annual_smses' => $annual_smses,
                    'total_annual' => ($annual_amount+$annual_tax),
                    'plan_names' => $plan_names,
                    'billing_package_type' => $package->billing_type,
                );
            }else{
                $package = $this->ci->billing_m->get_default_package();
                if($package){
                    if($group_id){
                        return ($this->get_amount_payable($package->id,$billing_cycle,$group_id,TRUE,$number_of_members));
                    }else{
                        return ($this->get_amount_payable($package->id,'',$group_id,TRUE,$number_of_members));
                    }
                }else{
                    return FALSE;
                } 
            }
        }else{
            $package = $this->ci->billing_m->get_default_package();
            if($package){
                if($billing_cycle){
                    return ($this->get_amount_payable($package->id,$billing_cycle,$group_id,$billing_calc,$number_of_members,$is_prorated,$billing_date,$show_arrears));
                }else{
                    return($this->get_amount_payable($package->id,1,$group_id,$billing_calc,$number_of_members));
                }
            }else{
                $this->ci->session->set_flashdata('error','Package not found and also no default package found');
                return FALSE;
            }
        }
    }

    function _next_biling_date($billing_cycle=0,$remaining_days=0,$billing_date=0,$package=''){
        $group_next_billing_date = strtotime('tomorrow');
        if($package){
            if($package->billing_type == 1 || $package->billing_type == 2){
                if($billing_cycle==1){
                    if($remaining_days){
                        if($remaining_days>=15){
                            $month = date('M Y',strtotime("+1 month",$billing_date));
                            $group_next_billing_date = strtotime('First day of '.$month);
                        }else{
                            $month = date('M Y',strtotime("+2 months",$billing_date));
                            $group_next_billing_date = strtotime('First day of '.$month);
                        }
                    }else{
                        $group_next_billing_date = strtotime('+ 1 Month',$billing_date);
                    }
                }else if($billing_cycle==2){
                    $month = $remaining_days?date('M Y',strtotime("+4 months",$billing_date)):date('M Y',strtotime("+3 months",$billing_date));
                    $group_next_billing_date = strtotime('First day of '.$month);
                }else if($billing_cycle==3){
                    $month = $remaining_days?date('M Y',strtotime("+13 months",$billing_date)):date('M Y',strtotime("+1 Year",$billing_date));
                    $group_next_billing_date = strtotime('First day of '.$month);
                }
            }else if($package->billing_type == 3){
                $plans = $this->ci->billing_m->get_billing_package_plans($package->id);
                $selected_plan = '';
                foreach ($plans as $key => $plan) {
                    if($key ==($billing_cycle-1)){
                        $selected_plan = $plan;
                        break;
                    }
                }
                if($selected_plan){
                    $billing_cycle = $selected_plan->cycle;
                }
                if($billing_cycle == 2 || $billing_cycle == 3){
                    if($remaining_days){
                        $month = $remaining_days?date('M Y',strtotime("+13 months")):date('M Y',strtotime("+1 Year"));
                        $group_next_billing_date = strtotime('First day of '.$month);
                    }else{
                        $group_next_billing_date = strtotime('+1 year');
                    }
                }else{
                    if($remaining_days){
                        if($remaining_days>=15){
                            $month = date('M Y',strtotime("+1 month"));
                            $group_next_billing_date = strtotime('First day of '.$month);
                        }else{
                            $month = date('M Y',strtotime("+2 months"));
                            $group_next_billing_date = strtotime('First day of '.$month);
                        }
                    }else{
                        $group_next_billing_date = strtotime('+ 1 Month',$billing_date);
                    }
                }
            }
            
        }
        return $group_next_billing_date;
    }

    function update_invoices($group_id=0){
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

    function subscribe_group($group_id=0,$create_invoice=TRUE){
        if($group_id){
            $group = $this->ci->groups_m->get($group_id);
            if($group){
               if($this->ci->groups_m->update($group_id,array(
                    'status' => 1,
                ))){
                    $billing_date= time();
                    $due_date = strtotime('+3 days',time());
                    $billing_cycle = $group->billing_cycle;
                    $amount = $this->get_amount_payable($group->billing_package_id,$billing_cycle,$group->id,'','',TRUE);
                    if($amount){
                        if($create_invoice){
                            $invoice_id = $this->create_invoice($group_id,$billing_date,$due_date,$amount,$billing_cycle,'',0,'','');
                            if($invoice_id){
                                return TRUE;
                            }
                        }else{
                            $remaining_days = $amount->remaining_days;
                            $billing_package = $this->ci->billing_m->get_package($group->billing_package_id);
                            $group_next_billing_date = $this->_next_biling_date($billing_cycle,$remaining_days,$billing_date,$billing_package);
                            $this->ci->groups_m->update($group_id,array('billing_date'=>$group_next_billing_date));
                            return TRUE;
                        }
                    }
                }else{
                    return FALSE;
                } 
                $this->update_group_subscription_status($group_id);
            }else{
                return FALSE;
            }
            
        }else{
            return FALSE;
        }
    }

    function record_billing_payments($amount=0,$group_id=0,$package_id=0,$billing_invoice_id=0,$receipt_date=0,$payment_method=1,$ipn_transaction_code='',$description='',$created_by=0,$purchase_sms=TRUE,$sms_notify=TRUE,$email_notify=TRUE){
        if($group_id&&$amount&&$receipt_date&&$payment_method){
            $group_active_size = $this->ci->groups_m->get_group_active_size($group_id);
            $group_owner = $this->ci->groups_m->get_group_owner($group_id);
            if($group_owner->status == '' || $group_owner->status == 0){
                $this->subscribe_group($group_id);
            }
            if($package_id){
                $package = $this->ci->billing_m->get_package($package_id);
                if($package){
                    //do nothing
                }else{
                    //get the default package
                    $package = $this->ci->billing_m->get_default_package();
                }
            }else{
                $package = $this->ci->billing_m->get_package($group_owner->billing_package_id);
                if($package){
                    //do nothing
                }else{
                    $package = $this->ci->billing_m->get_default_package();
                }
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
                'billing_receipt_number' => 'RCPT-'.$this->ci->billing_m->calculate_billing_receipt_number($group_id),
                'amount' => $amount,
                'tax' => $tax,
                'payment_method'=> $payment_method,
                'active'=>1,
                'description' => $description,
                'ipn_transaction_code' => $ipn_transaction_code,
                'created_on' => time(),
                'billing_invoice_id'=> $billing_invoice_id,
                'billing_package_id' => $package->id,
                'created_by' => $created_by,
            ));
            $this->update_invoices($group_id);
            $this->update_group_subscription_status($group_id);
            if($id){
                $account_arrears = $this->ci->billing_m->get_group_account_arrears($group_id);
                if($account_arrears<0 && $group_owner->billing_cycle!=3){
                    $amounts_payable = $this->get_amount_payable($package->id,'',$group_id,TRUE,$group_active_size);
                    if($amounts_payable){
                        $amounts_payable = (object)$amounts_payable;
                        $monthly_pay = $amounts_payable->monthly_amount+$amounts_payable->monthly_tax;
                        $quarterly_pay = $amounts_payable->quarterly_amount+$amounts_payable->quarterly_tax;
                        $annual_pay = $amounts_payable->annual_amount+$amounts_payable->annual_tax;
                        
                        $package_amount_tobe_in = abs($account_arrears)+$amount;
                        if(($package_amount_tobe_in)>$quarterly_pay && ($package_amount_tobe_in)<$annual_pay){
                            //edit last invoice to $quarterly Amount and group billing to quarterly
                            $billing_cycle = 2;
                            $amount_to_be_billed = $quarterly_pay;
                            $tax_to_be_billed = $amounts_payable->quarterly_tax;
                        }
                        else if(($package_amount_tobe_in+$amount)>$annual_pay){
                            //edit last invoice to annual pay and group billing to annual
                            $billing_cycle = 3;
                            $amount_to_be_billed = $annual_pay;
                            $tax_to_be_billed = $amounts_payable->annual_tax;
                        }
                        // $this->ci->groups_m->update($group_id,array('billing_cycle'=>$billing_cycle));
                        // $last_invoice_id = $this->ci->billing_m->get_group_last_invoice_id($group_id);
                        // // if($last_invoice_id->id){
                        // //     //$this->ci->billing_m->update_invoice($last_invoice_id->id,array('amount'=>$amount_to_be_billed,'tax'=>$tax_to_be_billed,'billing_cycle'=>$billing_cycle));
                        // // }
                        $this->update_invoices($group_id);
                        $account_arrears = $this->ci->billing_m->get_group_account_arrears($group_id);
                    }
                }
                if($purchase_sms){
                    if($account_arrears<=0){
                        $amount_payable_for_package = $this->get_amount_payable($package->id,'',$group_id,TRUE);
                        if($amount_payable_for_package){
                            $smses =0;
                            if($package->billing_type == 1 || $package->billing_type == 2){
                                $amount_payable_for_package = (object)$amount_payable_for_package;
                                $monthly_amount =  $amount_payable_for_package->monthly_amount+$amount_payable_for_package->monthly_tax;
                                $quarterly_amount =  $amount_payable_for_package->quarterly_amount+$amount_payable_for_package->quarterly_tax;
                                $annual_amount =  $amount_payable_for_package->annual_amount+$amount_payable_for_package->annual_tax;
                                if($amount>=$annual_amount){
                                    $smses = $package->annual_smses;
                                }else if($amount>=$quarterly_amount&&$amount<$annual_amount){
                                    $smses = $package->quarterly_smses;
                                }
                                else{
                                    $smses = $package->monthly_smses;
                                }
                            }else{
                                $plans = $this->ci->billing_m->get_billing_package_plans($package->id);
                                $selected_plan = '';
                                foreach ($plans as $key => $plan) {
                                    if($key ==($group_owner->billing_cycle-1)){
                                        $selected_plan = $plan;
                                        break;
                                    }
                                }
                                $monthly_smses  = $selected_plan->monthly_smses;
                                $cycle = $selected_plan->cycle;
                                if($cycle==1){
                                    $smses = $monthly_smses;
                                }elseif ($cycle == 2) {
                                    $smses = $monthly_smses*3;
                                }elseif ($cycle == 3) {
                                    $smses = $monthly_smses*12;
                                }
                            }
                            if($smses){
                                $update = $this->ci->groups_m->update_group_sms($group_id,$smses);
                            }else{
                                //do nothing
                            }    
                        }else{
                            //do nothing
                        }
                    }
                }
                $receipt = $this->ci->billing_m->get_group_billing_receipt($id,$group_id);
                $group = $this->ci->groups_m->get($group_id);
                
                if($sms_notify){
                    $result = $this->ci->messaging->sms_billing_payment_received($group->id,$account_arrears,$amount,$receipt_date,$group_owner,$tax);
                }

                if($email_notify){
                    $this->ci->messaging->email_billing_payment_received($group->id,$account_arrears,$amount,$receipt_date,$group_owner,$tax,$receipt,$package,$this->payment_method);
                }
                
                if($account_arrears>0){
                    $balance = ' Group outstanding balance is '.$this->default_country->currency_code.'. '.number_to_currency($account_arrears);
                }
                else if($account_arrears==0){
                    $balance=' You have fully cleared your balance';
                }
                else if($account_arrears<0){
                    $account_arrears = abs($account_arrears);
                    $balance ='Group bill overpayments is '.$this->default_country->currency_code.'. '.number_to_currency($account_arrears);
                }
                else{
                   $balance = ' Group outstanding balance is '.$this->default_country->currency_code.'. '.number_to_currency(0);
                }
                $this->ci->member_notifications->create('['.$this->chamasoft_settings->application_name.'] Billing Payment Received',
                    'Dear '.$group_owner->first_name.', your groups billing payment of '.$this->default_country->currency_code.'. '.number_to_currency($amount).' has been received.'.$balance.'. Thank you.',
                    $this->ci->ion_auth->get_user($group_owner->user_id),
                    $group_owner->member_id,
                    $group_owner->user_id,
                    $group_owner->member_id,
                    $group_id,
                    'View Receipt',
                    'group/billing/receipt/'.$id,11);
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

    function record_billing_sms_payments($group_id=0,$sms_purchased=0,$amount=0,$ipn_transaction_code='',$receipt_date=0,$payment_method=1,$description='',$created_by=0,$sms_notify=TRUE,$email_notify=TRUE){
        if($group_id&&$sms_purchased&&$amount&&$receipt_date){
            $id = $this->ci->billing_m->insert_sms_payments(array(
                    'group_id' => $group_id,
                    'billing_receipt_number' => 'RCPT-'.$this->ci->billing_m->calculate_billing_sms_receipt_number($group_id),
                    'amount' => $amount,
                    'sms_purchased' => $sms_purchased,
                    'payment_method' => $payment_method,
                    'ipn_transaction_code'=>$ipn_transaction_code,
                    'receipt_date' => $receipt_date,
                    'created_by' => $created_by,
                    'created_on' => time(),
                    'active' => 1,
                    'description' => $description
            ));
            if($id){
                $update = $this->ci->groups_m->update_group_sms($group_id,$sms_purchased);
                if($update){
                    $group_owner = $this->ci->groups_m->get_group_owner($group_id);

                    $this->ci->messaging->notify_sms_purchase_payment($group_id,$sms_purchased,$amount,$receipt_date,$group_owner,'','',$sms_notify,$email_notify);
                    $this->ci->member_notifications->create('['.$this->chamasoft_settings->application_name.'] Billing SMS Payment Received',
                        'Dear '.$group_owner->first_name.' '.$sms_purchased.' SMSes purchased on '.timestamp_to_receipt($receipt_date).' have been recorded. Thank you',
                        $this->ci->ion_auth->get_user($group_owner->user_id),
                        $group_owner->member_id,
                        $group_owner->user_id,
                        $group_owner->member_id,
                        $group_id,
                        'View Receipt',
                        'group/billing/sms_payment_receipt/'.$id,11);
                    return $group_owner;
                }else{
                    $this->ci->session->set_flashdata('error','Error updating group smses');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Error adding the sms payment');
                return FALSE;
            }
        }{
            $this->ci->session->set_flashdata('error','Some parameters are missing');
            return FALSE;
        }

    }

    function process_payment_ipn($amount=0,$account_number=0,$ipn_transaction_code=0,$receipt_date=0,$customer=0,$phone='',$ipn_depositor=0){
        if($amount&&$account_number&&$ipn_transaction_code&&$receipt_date&&$ipn_depositor){
            $group_id = $this->ci->groups_m->get_group_by_account($account_number);
            if($ipn_depositor==1){
                $payment_method = 3;
            }else if($ipn_depositor==2){
                $payment_method=2;
            }else{
                $payment_method=1;
            }
            $description = $phone.' '.$customer;

            if($group_id){
                    $group_owner = $this->record_billing_payments($amount,$group_id,'','',$receipt_date,$payment_method,$ipn_transaction_code,$description,'',TRUE,FALSE,TRUE);
                    if($group_owner){
                        return 1;
                    }else{
                        return 6;
                    }
                }else{
                    return 3;
                }
            }else{
                return 2;
            }
    }

    function is_account_number_recognized($account_number=0,$ipn_depositor=0){
       if($ipn_depositor&&$account_number){
            $account_number = strtolower($account_number);
            if(preg_match('/domain/', $account_number) || preg_match('/dvea/', $account_number) || preg_match('/chamasoft/', $account_number)){
                return TRUE;
            }else{
                $account_number = strtolower($account_number);
                if(preg_match('/sms/', $account_number)){
                    $account_numbers = explode('sms', $account_number);
                    $account_number = $account_numbers[1];
                }
                if($this->ci->groups_m->get_group_by_account($account_number)){
                    return TRUE;
                }else{
                    //Get all global validations endpoints
                    $request = array(
                            'account_number' => $account_number,
                            'ipn_depositor' => $ipn_depositor,
                        );
                    if(preg_match('/(45\.33\.18\.205)/',$_SERVER['SERVER_ADDR'])){
                        if(is_numeric($account_number) && (strlen($account_number) > 4 && strlen($account_number)<7) ){
                            return TRUE;
                        }
                        // $forwarders = $this->ci->billing_m->get_ipn_validation_forwarders();
                        // if($forwarders){
                        //     $response = FALSE;
                        //     foreach ($forwarders as $validation_endpoint) {
                        //        $result = $this->ci->curl->post_json(json_encode($request),$validation_endpoint->endpoint);
                        //        $result = json_decode($result);
                        //        if($result){
                        //             if(isset($result->exists)){
                        //                 if($result->exists==1){
                        //                     $response=TRUE;
                        //                     break;
                        //                 }else{
                        //                     $response = FALSE;
                        //                 }
                        //             }else{
                        //                 $response = FALSE;
                        //             }
                        //        }else{
                        //            $response = FALSE; 
                        //        }
                        //     }
                        //     return $response;
                        // }else{
                        //     return FALSE;
                        // }
                    }else{
                        return FALSE;
                    }
                }
            }
        }else{
            return FALSE;
        }
    }

    function calculate_sms_purchase($amount=0){
        if($amount){
            /****
                1. Amount 1-499 -> sms=amount/2
                2. Amount 500-999 -> sms=amount*(3/5)
                3. Amount 1000 -> sms=amount*(7/10)
            ***/
            $sms_purchased = 0;
            if($amount>=1000){
                $sms_purchased = round(($amount*(7/10)));
            }else if($amount>=500 && $amount<1000){
                $sms_purchased = round(($amount*(3/5)));
            }else{
                $sms_purchased = round(($amount/2));
            }
            return $sms_purchased;
        }else{
            return 0;
        }
    }
    
    function process_sms_payment_ipn($amount=0,$account_number=0,$ipn_transaction_code=0,$receipt_date=0,$customer=0,$phone='',$ipn_depositor=0){
        if($amount&&$account_number&&$ipn_transaction_code&&$receipt_date&&$ipn_depositor){
            $group_id = $this->ci->groups_m->get_group_by_account($account_number);
            if($group_id){
                /****
                    ipn depositor 
                    1 - Equitel - 3
                    2 - Mpesa - 4
                ***/
                $sms_purchased = $this->calculate_sms_purchase($amount);
                
                if($ipn_depositor==1){
                    $payment_method = 3;
                }else if($ipn_depositor==2){
                    $payment_method=2;
                }else{
                    $payment_method=1;
                }

                $description = $phone.' '.$customer;
                
                if($sms_purchased)
                {
                    $group_owner = $this->record_billing_sms_payments($group_id,$sms_purchased,$amount,$ipn_transaction_code,$receipt_date,$payment_method,$description,'',FALSE,TRUE);
                    if($group_owner)
                    {
                        $this->ci->messaging->notify_sms_purchase_payment($group_id,$sms_purchased,$amount,$receipt_date,$group_owner,$phone,$customer,TRUE,FALSE);
                        $status = 1;
                    }else{
                        $status = 4;
                    }
                }else{
                    $status = 4;
                }

            }else{
                $status = 3;
            }
        }else{
            $status = 2;
        }
        return $status;
    }


    function create_invoice($group_id=0,$billing_date=0,$due_date=0,$amount=array(),$billing_cycle=0,$created_by=array(),$amount_paid=0,$payment_method=1,$send_invoice_notifications=TRUE){
        if($group_id&&$billing_date&&$due_date&&$amount&&$billing_cycle){
            if(round($amount->amount+$amount->tax+$amount->prorated_amount)){
                if($amount_paid){
                }else{
                    $amount_paid=0;
                }
                if($created_by){
                    $created_by_id = $created_by->id;
                }else{
                    $created_by_id = 0;
                }

                $data = array(
                    'group_id'=> $group_id,
                    'billing_invoice_number' => 'INV-'.$this->ci->billing_m->calculate_billing_invoice_number($group_id),
                    'billing_date'=> $billing_date,
                    'due_date'=> $due_date,
                    'amount'=> round($amount->amount+$amount->tax+$amount->prorated_amount,2),
                    'tax' => $amount->tax,
                    'prorated_amount' => $amount->prorated_amount,
                    'billing_cycle' => $billing_cycle,
                    'billing_package_id'=>$amount->billing_package_id,
                    'created_by' => $created_by_id,
                    'created_on' => time(),
                    'amount_paid' => $amount_paid,
                    'active' => 1,
                );
                $id = $this->ci->billing_m->insert_invoices($data);
                
                $billing_package = $this->ci->billing_m->get_package($amount->billing_package_id);
                
                if($id){
                    if($amount_paid){
                        $payment = $this->record_billing_payments($amount_paid,$group_id,$amount->billing_package_id,$id,time(),$payment_method,'','Created from an invoice generated manually',$created_by_id,FALSE);
                            if($payment){
                                //continue;
                            }else{
                                $this->session->set_flashdata('error','Unable to record payment');
                                //return FALSE;
                            }
                    }
                    $remaining_days = $amount->remaining_days;
                    $group_next_billing_date = strtotime(str_replace(',','',$amount->next_biling_date)); 
                    //$this->_next_biling_date($billing_cycle,$remaining_days,$billing_date,$billing_package);
                    $this->ci->groups_m->update($group_id,array('billing_date'=>$group_next_billing_date,'status'=>1));
                    
                    $this->update_invoices($group_id);
                    
                    $this->update_group_subscription_status($group_id);
                   
                    if($send_invoice_notifications){
                        $invoice = $this->ci->billing_m->get_group_billing_invoice($id,$group_id);                        
                        $this->send_invoice_notifications($group_id,$amount,$due_date,$billing_cycle,$billing_date,$group_next_billing_date,$id,$invoice,$billing_package);                        
                    }
                    $this->ci->session->set_flashdata('success','Invoice successfully created');
                    return $id;
                }else{
                    $this->ci->session->set_flashdata('error','Could not create the invoice');
                    return FALSE;
                }
            }else{
                $group_next_billing_date = strtotime(str_replace(',','',$amount->next_biling_date)); 
                $this->ci->groups_m->update($group_id,array('billing_date'=>$group_next_billing_date));
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Some essential parameters are missing');
            return FALSE;
        }
        
    }

    function update_group_subscription_status($group_id=0){
        /***
            1. Group on ongoing trials
            2. Group not subscribed but trial expired
            3. Group paying and has no unpaid invoice------- real wise
            4. Group paying but has unpaid invoice but yet due date
            5. Paying group but invoice overdue
            6. Group in arrears and locked but has some free trial days
            7. Group has been temporary suspended and no one can login
        **/

        /*****Status check*******
         * 1. Active group
         * 2. Suspended group
         * Empty - on trial 
         * 
         * ***/
        if($group_id){
            $group = $this->ci->groups_m->get($group_id);
            if($group){
                $update = array();
                $trial_days = $group->trial_days;
                if($group->status){
                    if($group->status == 1){                        
                        $account_arrears = $this->ci->billing_m->get_group_account_arrears($group->id);                        
                        if($account_arrears>0){
                            $invoices = $this->ci->billing_m->get_group_unpaid_invoices($group->id);                            
                            $subscription_status = 0;
                            if($invoices){                              
                                foreach ($invoices as $invoice) {
                                    if($invoice->due_date < time()){
                                        $subscription_status = 5;
                                        break;
                                    }else{
                                        $subscription_status = 4;
                                    }
                               }
                            }
                            if($subscription_status==5){
                                if($group->trial_days > 0){
                                    $subscription_status=6;
                                }
                            }
                            $update = array(
                                'subscription_status' => $subscription_status,
                                'modified_on' => time(),
                            );
                        }else{
                            $update = array(
                                'subscription_status' => 3,
                                'modified_on' => time(),
                            );
                        }
                    }else if($group->status == 2){
                        $update = array(
                            'subscription_status' => 7,
                            'modified_on' => time(),
                        );
                    }
                }else{
                    $trial_days = intval($group->trial_days);
                    // if($group->lock_access == '1'){
                    //     $update = array(
                    //         'subscription_status' => 8,
                    //         'modified_on' => time(),
                    //     );
                    // }else{
                        if($trial_days >= 1){
                            $update = array(
                                'subscription_status' => 1,
                                'lock_access' => 0,
                                'status' =>1,
                                'modified_on' => time()
                            );
                        }else{
                            $update = array(
                                'subscription_status' => 2,
                                'lock_access' => 0,
                                'modified_on' => time(),
                            );
                        }
                    // }
                }
                if($update){
                    $update['arrears'] = $this->ci->billing_m->get_group_account_arrears($group_id);
                    $result = $this->ci->groups_m->update($group->id,$update);
                    return $result;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    function generate_billing_cycle_name($billing_cycle='',$billing_package='',$package_id=''){
        if($package_id){
            $billing_package = $this->ci->billing_m->get_package($package_id);
        }
        if($billing_cycle&&$billing_package){
            if($billing_package->billing_type == 3){
                $billing_plans = $this->ci->billing_m->get_billing_package_plans($billing_package->id);
                foreach ($billing_plans as $key => $plan) {
                    if($key ==($billing_cycle-1)){
                        $selected_plan = $plan;
                        break;
                    }
                }
                return $selected_plan->plan_names.' plan';
            }else{
                return (isset($this->billing_cycle[$billing_cycle])?$this->billing_cycle[$billing_cycle]:'Monthly').' plan';
            }
        }else{
            return 'Annual plan';
        }
    }

    function send_invoice_notifications($group_id=0,$amount=array(),$due_date=0,$billing_cycle=0,$billing_date=0,$group_next_billing_date=0,$invoice_id=0,$invoice='',$billing_package=array()){
        $default_country = $this->ci->countries_m->get_default_country();        
        $group = $this->ci->groups_m->get($group_id);
        if($group_id&&$amount&&$due_date&&$billing_cycle&&$default_country&&$group){
            $group_owner = $this->ci->groups_m->get_group_owner($group_id);            
            $account_arrears = $this->ci->billing_m->get_group_account_arrears($group_id);            
            if($group_owner){
                if($account_arrears>0){
                    $balance = ' Your '.$group->name.' outstanding balance is '.$default_country->currency_code.' '.number_to_currency($account_arrears).'. Kindly pay the balance before '.timestamp_to_receipt($due_date).' to avoid any inconveniences.';
                }
                else if($account_arrears==0){
                    $balance=' You do not have any arrears.';
                }
                else if($account_arrears<0){
                    $account_arrears = abs($account_arrears);
                    $balance ='Your '.$group->name.' bill overpayments are '.$default_country->currency_code.' '.number_to_currency($account_arrears).'.';
                }else{
                   $balance = 'Your '.$group->name.' outstanding balance is '.$default_country->currency_code.' '.number_to_currency(0).'.';
                }

                $this->ci->member_notifications->create(
                    '['.$this->chamasoft_settings->application_name.'] subscription invoice',
                    'Dear '.$group_owner->first_name.', your '.($this->generate_billing_cycle_name($billing_cycle,$billing_package)).' '.$this->chamasoft_settings->application_name.' subscription proforma invoice of '.$default_country->currency_code.' '.number_to_currency($amount->amount+$amount->tax+$amount->prorated_amount).' is ready.'.$balance.' Your Group Number is '.$group_owner->account_number.'. Click here for instructions on how to pay. ',
                    $this->ci->ion_auth->get_user($group_owner->user_id),
                    $group_owner->member_id,
                    $group_owner->user_id,
                    $group_owner->member_id,
                    $group_id,
                    'How to Pay for Subscription',
                    'group/billing/invoice/'.$invoice_id,
                    10);
                $this->ci->messaging->send_billing_invoice_sms_notification($group_id,$group_owner,$amount,$due_date,$billing_cycle,$account_arrears,$this->billing_cycle,$group);
                
                $this->ci->messaging->send_billing_invoice_email_notification($group_id,$group_owner,$amount,$due_date,$billing_cycle,$account_arrears,$this->billing_cycle,$billing_date,$group_next_billing_date,$invoice,$billing_package,($this->generate_billing_cycle_name($billing_cycle,$billing_package)));
                
            }else{
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Cannot send notifications. Essential parameters missing');
            return FALSE;
        }
    }

    function alter_trial_days(){
        $groups = $this->ci->groups_m->groups_with_trial_days();
        if($groups){
            $trial_days = 0;
            foreach ($groups as $group){
                $trial_days_end_date = '';
                $trial_days = $group->trial_days;
                $expiry_date = strtotime('+'.($trial_days-1).' days,',time());
                if($trial_days==1){
                    //set trial_days_end_date
                    $trial_days_end_date = time();
                }else if($trial_days==6){
                    //notify admin that the trial days will be over
                    $group_owner = $this->ci->groups_m->get_group_owner($group->id);
                    if($group->status!=1){
                        $messaging = $this->ci->messaging->sms_notify_trial_days($group->id,$group_owner,$trial_days-1);
                        $messaging = $this->ci->messaging->email_notify_trial_days($group->id,$group_owner,$trial_days-1);
                        $this->ci->member_notifications->create('Group Trial days',
                        'Dear '.$group_owner->first_name.', group trial days will expire on '.timestamp_to_receipt($expiry_date).'. Kindly subscribe to continue enjoying '.$this->chamasoft_settings->application_name.' services. To contact support call 0733366240. Thank you.',
                        $this->ci->ion_auth->get_user($group_owner->user_id),
                        $group_owner->member_id,
                        $group_owner->user_id,
                        $group_owner->member_id,
                        $group->id,
                        'Subscribe',
                        'group/billing/billing_information',12);  
                    }
                    
                }else if($trial_days == 2){
                    //remind the admin trial days will be over tomorrow and therefore will not be able to access records
                    $group_owner = $this->ci->groups_m->get_group_owner($group->id);
                    if($group->status!=1){
                        $messaging = $this->ci->messaging->sms_notify_trial_days($group->id,$group_owner,$trial_days-1);
                        $messaging = $this->ci->messaging->email_notify_trial_days($group->id,$group_owner,$trial_days-1);
                        $this->ci->member_notifications->create('Group Trial days',
                        'Dear '.$group_owner->first_name.', group trial days will expire Tomorrow. Kindly subscrbe and choose your convinient billing cycle. For support, call 0733366240. Thank you.',
                        $this->ci->ion_auth->get_user($group_owner->user_id),
                        $group_owner->member_id,
                        $group_owner->user_id,
                        $group_owner->member_id,
                        $group->id,
                        'Subscribe',
                        'group/billing/billing_information',12);
                    }
                }

                $trial_days = $trial_days-1; 
                $this->ci->groups_m->update($group->id,array(
                    'trial_days' => $trial_days,
                    'trial_days_end_date' => $trial_days_end_date,
                ));
                $this->update_group_subscription_status($group->id);

                unset($trial_days);
                unset($trial_days_end_date);
            }
        }else{
            return TRUE;
        }
    }

    function logged_in_group_checkin($group=array(),$user=array(),$slug='',$member = array()){
        if($group&&$user&&$slug){
            $this->update_group_subscription_status($group->id);
            /*****
                1. active group
                2. suspended group
                ''. group on trial
            ***/

        }else{
            redirect($this->chamasoft_settings->protocol.'.'.$this->chamasoft_settings->url.'/checkin');
        }
    }

    function count_unpaid_billing_invoices($group_id=0){
        return $this->ci->billing_m->count_unpaid_billing_invoices($group_id);
    }

    function automated_group_billing_invoice($date=0,$limit=0){
        //runs for 3 hours(12-1-2-3) after every 5 minutes. Each query is only limited to five groups to be billed today.
        if($date){
        }else{
            $date = time();
        }
        $success = 0;
        $fails = 0;
        $limit = 200;
        $groups = $this->ci->groups_m->get_groups_to_be_billed_today($date,$limit);
        if($groups){
            foreach ($groups as $group){
               if(date('dmy',$group->billing_date) == date('dmy',$date)){
                    $due_date = strtotime('+ 7 days',$group->billing_date);
                    $billing_cycle = $group->billing_cycle?:1;
                    $amount = $this->get_amount_payable($group->billing_package_id,$billing_cycle,$group->id,'','',TRUE,$date); 
                    if($amount){
                        $result = $this->create_invoice($group->id,$group->billing_date,$due_date,$amount,$billing_cycle);
                        if($result){                            
                            echo $group->name.'<br/>';
                            ++$success;
                        }
                    }
                    else{
                        ++$fails;
                    }
               }else{
                    ++$fails;
               }
            }
        }else{
            //no groups
        }
        if($success){
            echo $success.' Group(s) billed successfully on'.date('d-m-Y',$date);
        }
        if($fails){
            echo $fails.' Group(s) billing failed on '.date('d-m-Y',$date);
        }

        if(!$fails && !$success){
            echo 'No groups to billed on '.date('d-m-Y',$date);
        }
    }

    function unbilled_past_groups($date=0,$limit=0){
        if($date){
        }else{
            $date = time();
        }
        $success = 0;
        $fails = 0;
        $groups = $this->ci->groups_m->get_groups_past_billing($date,$limit);
        if($groups){
            foreach ($groups as $group){
                $due_date = strtotime('+ 7 days',$group->billing_date);
                $billing_cycle = $group->billing_cycle?:1;
                $date = $group->billing_date;
                $amount = $this->get_amount_payable($group->billing_package_id,$billing_cycle,$group->id,'','',TRUE,$date);
                if($amount){
                    if($amount->amount){
                        if($this->create_invoice($group->id,$group->billing_date,$due_date,$amount,$billing_cycle)){
                            echo $group->name.'<br/>';
                            ++$success;
                        }
                    }else{
                        $update = array(
                            'next_biling_date' => strtotime('first day of next month'),
                        );
                        $this->ci->groups_m->update($group->id,$update);
                    }
                }
                else{
                    ++$fails;
                }
                // if( ((date('Y',$group->billing_date) == date('Y',strtotime('last year')))  || (date('Y',$group->billing_date) == date('Y')))){
                    
                // }
            }
        }else{
            //no groups
        }
        if($success){
            echo $success.' Group(s) billed successfully on'.date('d-m-Y',$date);
            redirect(current_url());
        }
        if($fails){
            echo $fails.' Group(s) billing failed on '.date('d-m-Y',$date);
        }

        if(!$fails && !$success){
            echo 'No groups to billed on '.date('d-m-Y',$date);
        }
    }

    function duplicate_automated_group_billing_invoice($date=0,$limit=0){
        //runs for 3 hours(12-1-2-3) after every 5 minutes. Each query is only limited to five groups to be billed today.
        if($date){
        }else{
            $date = time();
        }
        $success = 0;
        $fails = 0;
        $backdated_groups = array();
        $groups = $this->ci->groups_m->get_groups_to_be_billed_today($date,$limit);
        print_r($groups);die('Groups here');
        if($groups){
            foreach ($groups as $group){
               if(date('dmy',$group->billing_date) == date('dmy',$date)){
                    $due_date = strtotime('+ 7 days',$group->billing_date);
                    $amount = $this->get_amount_payable($group->billing_package_id,$group->billing_cycle,$group->id);
                    if($amount){
                        if($this->create_invoice($group->id,$group->billing_date,$due_date,$amount,$group->billing_cycle,'','','',FALSE)){
                            $backdated_groups[] = $group;
                            ++$success;
                        }
                    }
                    else{
                        ++$fails;
                    }
               }else{
                    ++$fails;
               }
            }
        }else{
            //no groups
        }
        if($success){
            //echo $success.' Group(s) billed successfully on <br/>'.date('d-m-Y',$date);
        }
        if($fails){
            //echo $fails.' Group(s) billing failed on <br/>'.date('d-m-Y',$date);
        }

        if(!$fails && !$success){
            //echo 'No groups to bill on <br/>'.date('d-m-Y',$date);
        }
        
        return $backdated_groups;
        //print_r($backdated_groups);die;
    }

    function create_notification_for_insufficient_group_sms(){
        $insufficient_sms_errors = $this->ci->sms_m->get_all_smses_with_insufficient_error();
        if($insufficient_sms_errors){
            foreach ($insufficient_sms_errors as $smses){
                $smses = (object)$smses;
                if($smses->unset_smses){
                    $group = $this->ci->groups_m->get_group_owner($smses->group_id);
                    $this->ci->member_notifications->create(
                        $smses->unset_smses.' Unset SMSes',
                        'Dear '.$group->first_name.', you have '.$smses->unset_smses.' unsent SMS(es). Kindly topup your Group SMSes to avoid missing out on any Communication.',
                        $this->ci->ion_auth->get_user($group->user_id),
                        $group->member_id,
                        $group->user_id,
                        $group->member_id,
                        $smses->group_id,
                        'Top Up Now',
                        'group/billing/billing_information/#smses',
                        12
                        );
                }
            }
        }  
    }


    function menu_acceptable_for_package($package_id=0,$menu_id=0){
        $this->ci->load->model('menus/menus_m');
        $menu_pairing = $this->ci->billing_m->get_package_menu_pairing($package_id);
        if($menu_pairing && is_numeric($menu_id)){
            if(in_array($menu_id, $menu_pairing)){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            $menu = $this->ci->menus_m->get_menu_by_link_url($menu_id);
            if($menu && $package_id){
                return($this->menu_acceptable_for_package($package_id,$menu->id));
            }else{
                if($package_id){
                    return TRUE;
                }else{
                    $package = $this->ci->billing_m->get_default_package();
                    if($package){
                        return($this->menu_acceptable_for_package($package->id,$menu_id));
                    }else{
                     return FALSE;
                    }
                }
            }
       }
    }


    // function generate_access_permissions_style(){
    //     echo '<style>
    //             .confirmation_link,.confirmation_bulk_action{
    //                 display:none;
    //             }
    //     </style>';
    // }


    function setting_account_number_error(){
        $this->ci->load->library('investment_groups');
        $groups =  $this->ci->billing_m->billing_account_number();
        $group_dups = array();
        foreach ($groups as $group) {
            if($this->ci->groups_m->count_groups_by_account($group->account_number)>1){
               $group_dups[]=$group;  
            }
        }
        print_r($group_dups);
        if($group_dups){
            foreach ($group_dups as $group_dup) {
                $account_number = $group_dup->account_number;
                $groups_same_account = $this->ci->groups_m->get_groups_by_account($account_number);
                if(count($groups_same_account>1)){
                    if((isset($groups_same_account[0]) && isset($groups_same_account[1]))&&($groups_same_account[0]->created_on > $groups_same_account[1]->created_on)){
                        //edit the greater
                        $this->ci->groups_m->update($groups_same_account[0]->id,array('account_number'=>$this->ci->investment_groups->generate_account_number($groups_same_account[0]->id)));
                    }
                }else{

                }
            }
        }
    }


    function initiate_transaction_payment($amount=0,$phone_number=0,$group=array(),$user=array(),$reference_number,$callback_url='',$currency='KES',$channel = 1){
        if(valid_currency($amount)&&valid_phone($phone_number)&&$group&&$reference_number&&$user){
            if($channel ==1){
                $amount = currency($amount);
                $url = "https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest";
                $shortcode = "967600";
                if(array_key_exists($shortcode, $this->paybills)){
                    $paybills_data = $this->paybills[$shortcode];
                    $timestamp = $paybills_data['timestamp'];
                    $passkey = $paybills_data['passkey'];
                    $base_password = base64_encode($shortcode.$passkey.$timestamp);
                    $input = array(
                        'shortcode' => $shortcode,
                        'request_id' => $reference_number,
                        'amount' => $amount,
                        'phone' => $phone_number,
                        'request_callback_url' => $callback_url,
                        'group_id' => $group->id,
                        'reference_number' => $reference_number,
                        'created_on' => time(),
                        'user_id' => $user->id,
                        "customer_name" => strtoupper(strtolower($user->first_name)).' '.strtoupper(strtolower($user->last_name)),
                        'channel' => $channel,
                        'currency' => $currency,
                    );
                    if($id = $this->ci->ipn_m->insert_stk_push_request($input)){
                        $phone_number = str_replace("+","",valid_phone($phone_number));
                        $post_data = json_encode(array(
                            "BusinessShortCode" => $shortcode,
                            "Password" => $base_password,
                            "Timestamp" => $timestamp,
                            "TransactionType" => "CustomerPayBillOnline",
                            "Amount" => $amount,
                            "PartyA" => $phone_number,
                            "PartyB" => $shortcode,
                            "PhoneNumber" => $phone_number,
                            "CallBackURL" => "https://chamasoft.com:443/ipn/record_stk_push_payment",
                            "AccountReference" =>  $reference_number,
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
                                        if($this->ci->ipn_m->update_stkpushrequest($id,$update)){
                                            return $this->ci->ipn_m->get_stk_request($id);
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

                    }
                }else{
                    $this->ci->session->set_flashdata('error','Paybill error. Try again later');
                    return FALSE;
                }
            }elseif($channel==2){
                $request_id = $reference_number.time();
                $amount = currency($amount);
                $input = array(
                    'request_id' => $request_id,
                    'amount' => $amount,
                    'phone' => $phone_number,
                    'request_callback_url' => $callback_url,
                    'group_id' => $group->id,
                    'reference_number' => $reference_number,
                    'created_on' => time(),
                    'user_id' => $user->id,
                    "customer_name" => strtoupper(strtolower($user->first_name)).' '.strtoupper(strtolower($user->last_name)),
                    'channel' => $channel,
                    'currency' => $currency,
                );
                if($id = $this->ci->ipn_m->insert_stk_push_request($input)){
                    $phone_number = str_replace("+","",valid_phone($phone_number));
                    $post_data = json_encode(array(
                        "request_id" => time(),
                        "data" => array(
                            'transaction' => array(
                                "amount" => $amount,
                                "account_number" => '10020352',
                                "reference_number" => $request_id,
                                "security_pass" => openssl_key_encrypt("4aU8PshRtwB7GFI"),
                                "channel"  =>  3
                            ),
                            "user" => array(
                                'phone_number' => $phone_number,
                            ),
                            "callback_url" => site_url('/ipn/mtn_receive_subscription_payment'),
                        ),
                    ));
                    $url = "https://api.chamasoft.com:443/api/transactions/make_payment";
                    if($response = $this->ci->curl->post_json_payment($post_data,$url)){
                        if($res = json_decode($response)){
                            if(isset($res->data)){
                                $update = array(
                                    'response_code' => $res->data->response_code,
                                    'response_description' => $res->data->response_description,
                                    'modified_on' => time(),
                                );
                                if($this->ci->ipn_m->update_stkpushrequest($id,$update)){
                                    return $this->ci->ipn_m->get_stk_request($id);
                                }else{
                                    $this->ci->session->set_flashdata('error',"Error occured receiving response. Try again later");
                                    return FALSE;
                                }
                            }else{
                                return $res->description;
                            }
                        }else{
                            print_r($response);
                            $this->ci->session->set_flashdata('error','Error occured while processing request. Try again later');
                            return FALSE;  
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Could not make payment. '.$this->ci->session->flashdata('error'));
                        return FALSE;
                    }

                }
            }
        }else{
            $this->ci->session->set_flashdata('error','Missing request parameters. Update and try again');
            return FALSE;
        }   
    }

    function reverse_payment($transaction_code ='',$amount=0){
        $shortcode = 967600;
        if(array_key_exists($shortcode, $this->paybills)){
            $paybills_data = $this->paybills[$shortcode];
            $username = $paybills_data['username'];
            $initiator_password = $paybills_data['initiator_password'];
            $encypted_initiator_password = openssl_key_encrypt($initiator_password,FALSE,TRUE);
            $post_data = json_encode(array(
                "Initiator" => $username,
                "SecurityCredential" => $encypted_initiator_password,
                "CommandID" => "TransactionReversal",
                "TransactionID" => $transaction_code,
                "Amount" => $amount,
                "ReceiverParty" => $shortcode,
                "RecieverIdentifierType" => "11",
                "ResultURL" =>  "https://chamasoft.com:443/transaction_alerts/daraja_funds_reversal_callback",
                "QueueTimeOutURL" =>  "https://chamasoft.com:443/transaction_alerts/daraja_funds_reversal_callback",
                "Remarks" =>  "Reverse transaction",
                "Occasion" => ""
            ));
            $url = 'https://api.safaricom.co.ke/mpesa/reversal/v1/request';
            if($status_query = $this->ci->curl->darajaRequests->process_request($post_data,$url,$shortcode)){
                return($status_query);
            }else{
                $this->ci->session->flashdata('error','No response');
                return FALSE;
            }
        }else{
            $this->ci->session->flashdata('error','Some files are missing');
            return FALSE;
        }
    }



    function update_group_billing_cycle($billing_cycle = 0,$group=array(),$user_id=0){
        if($billing_cycle && is_numeric($billing_cycle) && $group){
            if($group->billing_package_id){
                $default_package = $this->ci->billing_m->get_package($group->billing_package_id);
                if(empty($default_package)){
                    $default_package = $this->ci->billing_m->get_default_package();
                }
            }else{
                $default_package = $this->ci->billing_m->get_default_package();
            }
            if($default_package) $billing_plans = $this->ci->billing_m->get_billing_package_plans($default_package->id);
            $billing_cycles = $this->billing_cycle;


            $selected_plan = '';
            foreach ($billing_plans as $key => $plan) {
                if($key ==($billing_cycle-1)){
                    $selected_plan = $plan;
                    break;
                }
            }
            $backdating_disabled = NULL;
            $disable_e_wallet = NULL;
            $disable_profit_and_loss = NULL;
            $disable_trial_balance = NULL;
            $disable_balance_sheet = NULL;
            $disable_loans = NULL;
            $disable_cash_flow = NULL;
            $disable_transaction_statement = NULL;
            $disable_bank_loans_summary = NULL;
            $disable_loans_summary = NULL;
            if($selected_plan){
                if($selected_plan->backdating){
                    $backdating_disabled = NULL;
                }else{
                    $backdating_disabled = 1;
                }

                if($selected_plan->e_wallet){
                    $disable_e_wallet = NULL;
                }else{
                    $disable_e_wallet = 1;
                }

                if($selected_plan->profit_and_loss){
                    $disable_profit_and_loss = NULL;
                }else{
                    $disable_profit_and_loss = 1;
                }

                if($selected_plan->trial_balance){
                    $disable_trial_balance = NULL;
                }else{
                    $disable_trial_balance = 1;
                }

                if($selected_plan->balance_sheet){
                    $disable_balance_sheet = NULL;
                }else{
                    $disable_balance_sheet = 1;
                }

                if($selected_plan->member_loans){
                    $disable_loans = NULL;
                }else{
                    $disable_loans = 1;
                }

                if($selected_plan->cash_flow_statement){
                    $disable_cash_flow = NULL;
                }else{
                    $disable_cash_flow = 1;
                }

                if($selected_plan->transaction_statement){
                    $disable_transaction_statement = NULL;
                }else{
                    $disable_transaction_statement = 1;
                }

                if($selected_plan->bank_loans_summary){
                    $disable_bank_loans_summary = NULL;
                }else{
                    $disable_bank_loans_summary = 1;
                }

                if($selected_plan->loans_summary){
                    $disable_loans_summary = NULL;
                }else{
                    $disable_loans_summary = 1;
                }
            }
            $update = array(
                'backdating_disabled' => $backdating_disabled,
                'disable_loans' => $disable_loans,
                'disable_e_wallet' => $disable_e_wallet,
                'disable_profit_and_loss' => $disable_profit_and_loss,
                'disable_trial_balance' => $disable_trial_balance,
                'disable_balance_sheet' => $disable_balance_sheet,
                'disable_cash_flow' => $disable_cash_flow,
                'disable_transaction_statement' => $disable_transaction_statement,
                'disable_bank_loans_summary' => $disable_bank_loans_summary,
                'disable_loans_summary' => $disable_loans_summary,
                'billing_cycle' => $billing_cycle,
                'modified_on' => time(),
                'modified_by' => $user_id,
            );
            if($this->ci->groups_m->update($group->id,$update)){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    function forward_ipns($alerts = array()){
        $forwarders = $this->ci->billing_m->get_ipn_forwarders();
        if($forwarders){
            foreach ($alerts as $alert) {
                $payment = $this->ci->ipn_m->get($alert->ipn_id);
                if($payment){
                    $validation = json_encode(array(
                        "TransactionType" => "",
                        "TransID"=> $payment->transaction_id,
                        "TransTime"=>  date('YmdHis',$payment->transaction_date),
                        "TransAmount"=> $payment->amount,
                        "BusinessShortCode" => "967600",
                        "BillRefNumber" => $payment->account,
                        "InvoiceNumber"=> "",
                        "OrgAccountBalance"=> "",
                        "ThirdPartyTransID"=> "",
                        "MSISDN"=> $payment->phone,
                        "FirstName"=> $payment->customer_name,
                        "MiddleName"=> "",
                        "LastName" => ""
                    ));

                    $confirmation = json_encode(array(
                        "TransactionType" => "Pay Bill",
                        "TransID" =>  $payment->transaction_id,
                        "TransTime" => date('YmdHis',$payment->transaction_date),
                        "TransAmount"=> $payment->amount,
                        "BusinessShortCode"=> "967600",
                        "BillRefNumber" => $payment->account,
                        "InvoiceNumber" => "",
                        "OrgAccountBalance" => $payment->paybill_balance,
                        "ThirdPartyTransID" => " ",
                        "MSISDN" => $payment->phone,
                        "FirstName" => $payment->customer_name, 
                        "MiddleName"=> "",
                        "LastName"=> ""
                    ));
                    $result = FALSE;
                    foreach ($forwarders as $forwarder) {
                        $validation_url = $forwarder->mpesa_validation_end_point;
                        $confirmation_url = $forwarder->mpesa_confirmation_end_point;
                        if($this->ci->curl->post($validation,$validation_url)){
                            $conf_result = $this->ci->curl->post($confirmation,$confirmation_url);
                            if($data = json_decode($conf_result)){
                                if($data->ResultCode == "1" || $data->ResultCode == "0"){
                                    $result = TRUE;
                                }else{
                                    break;
                                }
                            }else{
                                break;
                            }
                        }else{
                            break;
                        }
                    }
                    if($result){
                        $update = array(
                            'forward_status' => 1,
                            'forwarded_on' => time(),
                        );
                        $this->ci->ipn_m->update_ipn_forward($alert->id,$update);
                    }
                }else{
                   $update = array(
                        'forward_status' => 2,
                        'forwarded_on' => time(),
                    );
                    $this->ci->ipn_m->update_ipn_forward($alert->id,$update); 
                }
            }
            return TRUE;
        }else{
            return FALSE;
        }
    }


}