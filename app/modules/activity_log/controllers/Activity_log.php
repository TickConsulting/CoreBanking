<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_log extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('activity_log_m');
    }
    
    function activity_log_per_module(){
        $result = $this->activity_log_m->get_all_for_pricing();
        $array = array();
        $deposit = array();
        $withdraw = array();
        $member = array();
        $loan = array();
        $fine = array();
        $email = array();
        $sms = array();
        $notification= array();
        $asset= array();
        $stock= array();
        $money_market= array();
        $report= array();
        $accounts= array();
        $contribution= array();
        $invoice= array();
        $billing = array();
        $member_loan= array();
        $statement= array();
        $member_email= array();
        $member_report= array();
        $member_member= array();
        $member_sms= array();
        $member_notification= array();
        foreach($result as $res){
            if(preg_match('/group/', $res->url)){
                if(preg_match('/deposit/',$res->url) || preg_match('/income_cate/',$res->url)){
                    $deposit[] = $res;
                }
                if(preg_match('/withdraw/',$res->url) || preg_match('/expense_cate/',$res->url)){
                    $withdraw[] = $res;
                }
                if(preg_match('/member/',$res->url)){
                    $member[] = $res;
                }
                if(preg_match('/loan/',$res->url)){
                    $loan[] = $res;
                }
                if(preg_match('/fine/',$res->url) || preg_match('/fine_categories/',$res->url)){
                    $fine[] = $res;
                }
                if(preg_match('/email/',$res->url)){
                    $email[] = $res;
                }
                if(preg_match('/sms/',$res->url)){
                    $sms[] = $res;
                }
                if(preg_match('/notification/',$res->url)){
                    $notification[] = $res;
                }
                if(preg_match('/asset/',$res->url) || preg_match('/asset_cate/',$res->url)){
                    $asset[] = $res;
                }
                if(preg_match('/stock/',$res->url)){
                    $stock[] = $res;
                }
                if(preg_match('/money_market/',$res->url)){
                    $money_market[] = $res;
                }
                if(preg_match('/report/',$res->url)){
                    $report[] = $res;
                }
                if(preg_match('/account/',$res->url)){
                    $accounts[] = $res;
                }
                if(preg_match('/contribution/',$res->url)){
                    $contribution[] = $res;
                }
                if(preg_match('/invoice/',$res->url)){
                    $invoice[] = $res;
                }
                if(preg_match('/billing/',$res->url)){
                    $billing[] = $res;
                }
                $array[] = $res;
            }
            else if(preg_match('/member/', $res->url)){
                if(preg_match('/member\/loan/',$res->url)){
                    $member_loan[] = $res;
                }
                if(preg_match('/member\/statement/',$res->url)){
                    $statement[] = $res;
                }
                if(preg_match('/member\/email/',$res->url)){
                    $member_email[] = $res;
                }
                if(preg_match('/member\/report/',$res->url)){
                    $member_report[] = $res;
                }
                if(preg_match('/member\/member/',$res->url)){
                    $member_member[] = $res;
                }
                if(preg_match('/member\/sms/',$res->url)){
                    $member_sms[] = $res;
                }
                if(preg_match('/member\/notification/',$res->url)){
                    $member_notification[] = $res;
                }
            }
        }

        $result = array(
                'deposit' => count($deposit),
                'withdraw' => count($withdraw),
                'member' => count($member),
                'loan' => count($loan),
                'fine' => count($fine),
                'email' => count($fine),
                'sms' => count($sms),
                'notification' => count($notification),
                'asset' =>  count($asset),
                'stock' =>  count($stock),
                'money_market' =>  count($money_market),
                'report' =>  count($report),
                'accounts' =>  count($accounts),
                'contribution' =>  count($contribution),
                'invoice' =>  count($invoice),
                'billing'   =>  count($billing),
                'Total Result' =>  count($array),
            );
        $member_result = array(
                'Loans' => count($member_loan),
                'statement' => count($statement),
                'Email' => count($member_email),
                'Reports' => count($member_report),
                'Member Directory' => count($member_member),
                'SMS' => count($member_sms),
                'Notifications' => count($member_notification),
            );
        arsort($result);
        arsort($member_result);
        echo 'Groups Activity<br/>';
        print_r('<pre>');
        print_r($result);
        print_r('</pre>');
        echo 'Member Activity<br/>';
        print_r('<pre>');
        print_r($member_result);
        print_r('</pre>');
    }


    function contribution_summary(){
        
    }


 }