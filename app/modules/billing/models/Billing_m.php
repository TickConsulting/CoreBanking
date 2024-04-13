<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Billing_m extends MY_Model{

    protected $_table = 'billing';

    function __construct(){
        parent::__construct();
        $this->load->library('billing_settings');
        //$this->install();
    }
    /******
    ****Key Notes****
    billing packages
    1. active
    '' is disable and cannot be used
    
    1. is_default // the default package - can only be one at a time and must be active
    ''. any other package

    billing invoices
    1. active invoice
    ''. voided invoice

    status
    1. paid invoice
    '' unpaid invoice

    billing payments
    1. active
    '' . voided payment and thus not valid
    ****/
    function install(){
        $this->db->query("
            create table if not exists billing_packages(
                    id int not null auto_increment primary key,
                    `name` blob,
                    `slug` blob,
                    `billing_type` blob,
                    `rate` blob,
                    `rate_on` blob,
                    `monthly_amount` blob,
                    `quarterly_amount` blob,
                    `annual_amount` blob,
                    `monthly_smses` blob,
                    `quarterly_smses` blob,
                    `annual_smses` blob,
                    `enable_tax` blob,
                    `percentage_tax` blob,
                    `active` blob,
                    `is_default` blob,
                    `created_by` blob,
                    `created_on` blob,
                    `modified_on` blob,
                    `modified_by` blob
            )");

        $this->db->query("
            create table if not exists billing_packages_plans(
                    id int not null auto_increment primary key,
                    `plan_names` blob,
                    `user_limits` blob,
                    `monthly_smses` blob,
                    `backdating` blob,
                    `e_wallet` blob,
                    `profit_and_loss` blob,
                    `trial_balance` blob,
                    `balance_sheet` blob,
                    `charge_amount` blob,
                    `cycle` blob,
                    `billing_package_id` blob,
                    `created_by` blob,
                    `created_on` blob,
                    `modified_on` blob,
                    `modified_by` blob
            )");

        $this->db->query("
            create table if not exists billing_invoices(
                    id int not null auto_increment primary key,
                    `group_id` blob,
                    `billing_date` blob,
                    `due_date` blob,
                    `amount` blob,
                    `tax` blob,
                    `amount_paid` blob,
                    `billing_cycle` blob,
                    `billing_package_id` blob,
                    `status` blob,
                    `active` blob,
                    `created_by` blob,
                    `created_on` blob,
                    `modified_on` blob,
                    `modified_by` blob
            )");

         $this->db->query("
            create table if not exists billing_payments(
                    id int not null auto_increment primary key,
                    `group_id` blob,
                    `ipn_transaction_code` blob,
                    `receipt_date` blob,
                    `amount` blob,
                    `tax` blob,
                    `payment_method` blob,
                    `billing_invoice_id` blob,
                    `description` blob,
                    `active` blob,
                    `created_by` blob,
                    `created_on` blob,
                    `modified_on` blob,
                    `modified_by` blob
            )");

         $this->db->query("
            create table if not exists billing_sms_payments(
                    id int not null auto_increment primary key,
                    `group_id` blob,
                    `ipn_transaction_code` blob,
                    `billing_receipt_number` blob,
                    `receipt_date` blob,
                    `amount` blob,
                    `payment_method` blob,
                    `description` blob,
                    `sms_purchased` blob,
                    `active` blob,
                    `created_by` blob,
                    `created_on` blob,
                    `modified_on` blob,
                    `modified_by` blob
            )");

         $this->db->query("
            create table if not exists billing_package_menu_pairing(
                    id int not null auto_increment primary key,
                    `package_id` blob,
                    `menu_id` blob,
                    `active` blob,
                    `type` blob,
                    `created_by` blob,
                    `created_on` blob,
                    `modified_on` blob,
                    `modified_by` blob
            )");

          $this->db->query("
            create table if not exists billing_payments_forwarder(
                    id int not null auto_increment primary key,
                    `title` blob,
                    `account_validation_end_point` blob,
                    `equity_ipn_end_point` blob,
                    `mpesa_validation_end_point` blob,
                    `mpesa_confirmation_end_point` blob,
                    `active` blob,
                    `created_by` blob,
                    `created_on` blob,
                    `modified_on` blob,
                    `modified_by` blob
            )");

    }

    /****************billing packages****************/

    function insert_package($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('billing_packages',$input);
    }

    function update_package($id,$input,$val=FALSE){
        return $this->update_secure_data($id,'billing_packages',$input);
    }

    function get_package($id=0)
    {
        $this->select_all_secure('billing_packages');
        $this->db->where('id',$id);
        return $this->db->get('billing_packages')->row();
    }
   
    function get_all_packages($display = FALSE)
    {
        $this->select_all_secure('billing_packages');
        if($display){
            $this->where($this->dx('display_reports').'="1"',NULL,FALSE);
        }
        return $this->db->get('billing_packages')->result();
    }

    function is_unique_slug_package($slug=0,$id=0){
        $this->where($this->dx('slug').'="'.$slug.'"',NULL,FALSE);
        $this->where($this->dx('active').'="1"',NULL,FALSE);
        if($id){
           $this->where('id !=',$id); 
        }
        return $this->db->count_all_results('billing_packages')?:0;
    }

    function count_default(){
        $this->where($this->dx('active').'="1"',NULL,FALSE);
        $this->where($this->dx('is_default').'="1"',NULL,FALSE);
        return $this->db->count_all_results('billing_packages')?:0;
    }

    function get_default_package(){
        $this->select_all_secure('billing_packages');
        $this->where($this->dx('active').'="1"',NULL,FALSE);
        $this->where($this->dx('is_default').'="1"',NULL,FALSE);
        return $this->db->get('billing_packages')->row();
    }

    function billing_packages_options(){
        $this->select_all_secure('billing_packages');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $result = $this->db->get('billing_packages')->result();
        $arr = array();
        if($result){
            foreach ($result as $value) {
                if($value->is_default){
                    $default = " - Default";
                }else{
                    $default='';
                }
                if($value->billing_type==1){
                    $arr[$value->id] = $value->name.' - ('.$this->billing_settings->billing_type[$value->billing_type].') of '.number_to_currency($value->monthly_amount).' Monthly'.$default;
                }else if($value->billing_type==2){
                    $arr[$value->id] = $value->name.' - ('.$this->billing_settings->billing_type[$value->billing_type].') - '.$value->rate.'% on '.$this->billing_settings->billing_percentage_on[$value->rate_on].$default;
                }else{
                    $arr[$value->id] = $value->name;
                }
            }
        }
        return $arr;
    }
    function delete_billing_packages_plans($billing_package_id =0 ){
        $this->db->where($this->dx('billing_package_id').' = "'.$billing_package_id.'"',NULL,FALSE);
        return $this->db->delete('billing_packages_plans');
    }


    // function insert_batch_billing_package_plan($input=array()){
    //     return $this->insert_batch_secure_data('billing_packages_plans',$input);
    // }

    function insert_batch_billing_package_plan($input,$skip_validation=FALSE){
        return $this->insert_batch_secure_data('billing_packages_plans',$input);
    }

    function insert_package_plan($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('billing_packages_plans',$input);
    }

    function get_billing_package_plans($billing_package_id=0){
        $this->select_all_secure('billing_packages_plans');
        $this->db->where($this->dx('billing_package_id').' = "'.$billing_package_id.'"',NULL,FALSE);
        $results = $this->db->get('billing_packages_plans')->result();
        return $results;
    }

    function get_billing_package_plans_array(){
        $this->select_all_secure('billing_packages_plans');
        $results = $this->db->get('billing_packages_plans')->result();
        $arr = array();
        foreach ($results as $key => $result) {
            $arr[$result->billing_package_id][] = $result;
        }
        return $arr;
    }


    /*****************billing invoices******************/

    function insert_invoices($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('billing_invoices',$input);
    }
    function update_invoice($id,$input,$val=FALSE){
        return $this->update_secure_data($id,'billing_invoices',$input);
    }

    function calculate_billing_invoice_number($group_id=0)
    {
        $this->db->where($this->dx('billing_invoices.group_id').'="'.$group_id.'"',NULL,FALSE);
        $this->db->from('billing_invoices');
        $count = $this->db->count_all_results();
        $count = $count+1;
        if($count>=100){
        }else if($count>=10){
            $count = '0'.$count;
        }else{
            $count='00'.$count;
        }
        return $count;
    }

    function get_invoice($id=0){
        $this->select_all_secure('billing_invoices');
        $this->db->where('id',$id);
        return $this->db->get('billing_invoices')->row();
    }

    function get_group_invoices($group_id=0,$order){
        $this->select_all_secure('billing_invoices');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        if($order){
            $this->db->order_by($this->dx('billing_date'),'ASC',FALSE);
        }else{
            $this->db->order_by($this->dx('billing_date'),'DESC',FALSE);
        }
        return $this->db->get('billing_invoices')->result();
    }

    function get_all_active_invoices($order='',$group_id=0,$billing_cycle=0,$billing_date=0,$due_date=0,$invoice_status=0){
        $this->select_all_secure('billing_invoices');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if(isset($group_id) && $group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }
        if(isset($billing_cycle) && $billing_cycle){
           $this->db->where($this->dx('billing_cycle').'="'.$billing_cycle.'"',NULL,FALSE); 
        }
        if(isset($invoice_status) && $invoice_status){
            if($invoice_status==1){
                $this->db->where($this->dx('status').'="1"',NULL,FALSE); 
            }else{
                $this->db->where($this->dx('status').'!="1"',NULL,FALSE); 
            }
        }
        if(isset($billing_date) && $billing_date){
            $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('billing_date')."),'%Y %d %m') = '" . date('Y d m',$billing_date) . "'", NULL, FALSE);
        }
        if(isset($due_date) && $due_date){
            $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('due_date')."),'%Y %d %m') = '" . date('Y d m',$due_date) . "'", NULL, FALSE);
        }
        if($order){
            $this->db->order_by($this->dx('billing_date'),'ASC',FALSE);
            $this->db->order_by($this->dx('created_on'),'ASC',FALSE);
        }else{
            $this->db->order_by($this->dx('billing_date'),'DESC',FALSE);
            $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
        }
        return $this->db->get('billing_invoices')->result();
    }

    function count_all_active_invoices($group_id=0,$billing_cycle=0,$billing_date=0,$due_date=0,$invoice_status=0){
         if(isset($group_id) && $group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }
        if(isset($billing_cycle) && $billing_cycle){
           $this->db->where($this->dx('billing_cycle').'="'.$billing_cycle.'"',NULL,FALSE); 
        }
        if(isset($invoice_status) && $invoice_status){
            if($invoice_status==1){
                $this->db->where($this->dx('status').'="1"',NULL,FALSE); 
            }else{
                $this->db->where($this->dx('status').'!="1"',NULL,FALSE); 
            }
        }
        if(isset($billing_date) && $billing_date){
            $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('billing_date')."),'%Y %d %m') = '" . date('Y d m',$billing_date) . "'", NULL, FALSE);
        }
        if(isset($due_date) && $due_date){
            $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('due_date')."),'%Y %d %m') = '" . date('Y d m',$due_date) . "'", NULL, FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->count_all_results('billing_invoices')?:0;
    }

    function get_group_billing_amount_payable($group_id=0){
        $this->db->select('sum('.$this->dx('amount').') as amount_payable');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        $amount_payable = $this->db->get('billing_invoices')->row();
        if($amount_payable){
            return $amount_payable->amount_payable;
        }else{
            return 0;
        }
    }


    function get_groups_billing_payable_amounts_array($group_ids = array()){
        $arr = array();
        $this->db->select(
            array(
                'sum('.$this->dx('amount').') as amount',
                $this->dx('group_id')." as id"
            )
        );
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if(empty($group_ids)){
            $this->db->where($this->dx('group_id').' IN (0)',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' IN ('.implode(',',$group_ids).')',NULL,FALSE);
        }
        $this->db->group_by(
            array(
                $this->dx('group_id')
            )
        );
        $groups = $this->db->get('billing_invoices')->result();
        foreach($groups as $group):
            $arr[$group->id] = $group->amount;
        endforeach;
        return $arr;
    }

    function get_groups_billing_last_payment_dates_array($group_ids = array()){
        $arr = array();
        $this->db->select(
            array(
                $this->dx('group_id')." as id ",
                $this->dx('receipt_date')." as receipt_date ",
            )
        );
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if(empty($group_ids)){
            $this->db->where($this->dx('group_id').' IN (0)',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' IN ('.implode(',',$group_ids).')',NULL,FALSE);
        }
        $this->db->order_by($this->dx('receipt_date'),'DESC',FALSE);
        $groups = $this->db->get('billing_payments')->result();
        foreach($groups as $group):
            if(isset($arr[$group->id])){

            }else{
                $arr[$group->id] = $group->receipt_date;
            }
        endforeach;
        return $arr;
    }


    function get_groups_billing_first_payment_dates_array($group_ids = array()){
        $arr = array();
        $this->db->select(
            array(
                $this->dx('group_id')." as id ",
                $this->dx('receipt_date')." as receipt_date ",
            )
        );
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if(empty($group_ids)){
            $this->db->where($this->dx('group_id').' IN (0)',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' IN ('.implode(',',$group_ids).')',NULL,FALSE);
        }
        $this->db->group_by(
            array(
                $this->dx('group_id')
            )
        );
        $this->db->order_by($this->dx('receipt_date'),'ASC',FALSE);
        $groups = $this->db->get('billing_payments')->result();
        foreach($groups as $group):
            $arr[$group->id] = $group->receipt_date;
        endforeach;
        return $arr;
    }


    function get_group_billing_invoice($id=0,$group_id=0){
        $this->select_all_secure('billing_invoices');
        $this->where('id',$id);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('billing_invoices')->row();
    }



    /*************billing payments*********/
    function insert_payments($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('billing_payments',$input);
    }
    function update_payment($id,$input,$val=FALSE){
        return $this->update_secure_data($id,'billing_payments',$input);
    }

    function get_payment($id=0){
        $this->select_all_secure('billing_payments');
        $this->db->where('id',$id);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('billing_payments')->row();
    }

    function get_group_billing_paid_amount($group_id=0){
        $this->db->select('sum('.$this->dx('amount').') as amount_paid');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        $amount_paid = $this->db->get('billing_payments')->row();

        if($amount_paid){
            return $amount_paid->amount_paid;
        }else{
            return 0;
        }
    }

    function get_groups_billing_paid_amounts_array($group_ids = array()){
        $arr = array();
        $this->db->select(
            array(
                'sum('.$this->dx('amount').') as amount',
                $this->dx('group_id')." as id"
            )
        );
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if(empty($group_ids)){
            $this->db->where($this->dx('group_id').' IN (0)',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' IN ('.implode(',',$group_ids).')',NULL,FALSE);
        }
        $this->db->group_by(
            array(
                $this->dx('group_id')
            )
        );
        $groups = $this->db->get('billing_payments')->result();
        foreach($groups as $group):
            $arr[$group->id] = $group->amount;
        endforeach;
        return $arr;

    }

    function get_total_billing_paid_amount($group_id=0){
        $this->db->select('sum('.$this->dx('amount').') as amount_paid');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $amount_paid = $this->db->get('billing_payments')->row();

        if($amount_paid){
            return $amount_paid->amount_paid;
        }else{
            return 0;
        }
    }

    function calculate_billing_receipt_number($group_id=0)
    {
        $this->db->where($this->dx('billing_payments.group_id').'="'.$group_id.'"',NULL,FALSE);
        $this->db->from('billing_payments');
        $count = $this->db->count_all_results();
        $count = $count+1;
        if($count>=100){
        }else if($count>=10){
            $count = '0'.$count;
        }else{
            $count='00'.$count;
        }
        return $count;
    }

    function get_group_billing_receipt($id=0,$group_id=0){
        $this->select_all_secure('billing_payments');
        $this->where('id',$id);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('billing_payments')->row();
    }

    function get_payment_by_billing_invoice_id($billing_invoice_id=0){
        $this->select_all_secure('billing_payments');
        $this->db->where($this->dx('billing_invoice_id').'="'.$billing_invoice_id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('billing_payments')->row();
    }

    function count_all_active_payments($group_id=0,$payment_method=0,$receipt_date=0,$minimum_amount = 0,$receipt_date_from = 0,$receipt_date_to = 0){
        if(isset($receipt_date) && $receipt_date){
            $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('receipt_date')."),'%Y %d %m') = '" . date('Y d m',$receipt_date) . "'", NULL, FALSE);
        }
        if($receipt_date_from){
            $this->db->where($this->dx('receipt_date').'>="'.$receipt_date_from.'"',NULL, FALSE);
        }
        if($receipt_date_to){
            $this->db->where($this->dx('receipt_date').'<="'.$receipt_date_to.'"',NULL, FALSE);
        }
        if(isset($group_id) && $group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL, FALSE);
        }
        if(isset($payment_method) && $payment_method){
            $this->db->where($this->dx('payment_method').'="'.$payment_method.'"',NULL, FALSE);
        }
        if($minimum_amount){
            $this->db->where($this->dx('amount').'>="'.$minimum_amount.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->count_all_results('billing_payments')?:0;
    }

    function get_all_active_payments($order=FALSE,$group_id=0,$payment_method=0,$receipt_date=0,$minimum_amount = 0,$receipt_date_from = 0,$receipt_date_to = 0){
        $this->select_all_secure('billing_payments');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if(isset($receipt_date) && $receipt_date){
            $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('receipt_date')."),'%Y %d %m') = '" . date('Y d m',$receipt_date) . "'", NULL, FALSE);
        }
        if($receipt_date_from){
            $this->db->where($this->dx('receipt_date').'>="'.$receipt_date_from.'"',NULL, FALSE);
        }
        if($receipt_date_to){
            $this->db->where($this->dx('receipt_date').'<="'.$receipt_date_to.'"',NULL, FALSE);
        }
        if(isset($group_id) && $group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL, FALSE);
        }
        if(isset($payment_method) && $payment_method){
            $this->db->where($this->dx('payment_method').'="'.$payment_method.'"',NULL, FALSE);
        }
        if($minimum_amount){

            $this->db->where($this->dx('amount').'>="'.$minimum_amount.'"',NULL,FALSE);
        }
        if($order){
            $this->db->order_by($this->dx('receipt_date'),'ASC',FALSE);
        }else{
            $this->db->order_by($this->dx('receipt_date'),'DESC',FALSE);
            $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
        }
        return $this->db->get('billing_payments')->result();
    }

    function get_payments_grouped_monthly($filter = array()){
        $this->db->select(array(
            'SUM('.$this->dx('amount').') as amount_paid',
            "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('receipt_date')." ),'%Y %M ') as receipt_date_month_year ",
        ));
        $this->db->where($this->dx('active').' ="1"',NULL,FALSE);
        if($filter){
            if(array_key_exists('billing_cycle', $filter) && $filter['billing_cycle']){
                $this->db->where($this->dx('billing_cycle').' ="'.$filter['billing_cycle'].'"',NULL,FALSE);
            }
            if(array_key_exists('group_ids', $filter)){
                $groups = '0';
                $group_ids = $filter['group_ids'];
                if($group_ids):
                    foreach ($group_ids as $group_id) {
                        if($groups){
                            $groups .=','.$group_id;
                        }else{
                            $groups = $group_id;
                        }
                    }
                endif;
                if($groups){
                    $this->db->where($this->dx('group_id').' IN ('.$groups.')',NULL,FALSE);
                }                
            }
        }
        $this->db->group_by(array(
            "receipt_date_month_year"
        ));
        $this->db->order_by($this->dx('receipt_date'),'DESC',FALSE);
        $payments =  $this->db->get('billing_payments')->result();
        $arr = array();
        foreach ($payments as $payment) {
            $arr[date('Ym',strtotime($payment->receipt_date_month_year))] = $payment->amount_paid;
        }
        return $arr;
    }


    /***************SMS Payments*****************/

    function insert_sms_payments($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('billing_sms_payments',$input);
    }


    function count_all_active_sms_payments($group_id=0,$payment_method=0,$receipt_date=0){
        if(isset($receipt_date) && $receipt_date){
            $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('receipt_date')."),'%Y %d %m') = '" . date('Y d m',$receipt_date) . "'", NULL, FALSE);
        }
        if(isset($group_id) && $group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL, FALSE);
        }
        if(isset($payment_method) && $payment_method){
            $this->db->where($this->dx('payment_method').'="'.$payment_method.'"',NULL, FALSE);
        }
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->count_all_results('billing_sms_payments')?:0;
    }

    function get_all_active_sms_payments($order=FALSE,$group_id=0,$payment_method=0,$receipt_date=0){
        $this->select_all_secure('billing_sms_payments');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if(isset($receipt_date) && $receipt_date){
            $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('receipt_date')."),'%Y %d %m') = '" . date('Y d m',$receipt_date) . "'", NULL, FALSE);
        }
        if(isset($group_id) && $group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL, FALSE);
        }
        if(isset($payment_method) && $payment_method){
            $this->db->where($this->dx('payment_method').'="'.$payment_method.'"',NULL, FALSE);
        }
        if($order){
            $this->db->order_by($this->dx('receipt_date'),'ASC',FALSE);
        }else{
            $this->db->order_by($this->dx('receipt_date'),'DESC',FALSE);
            $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
        }
        return $this->db->get('billing_sms_payments')->result();
    }

    function calculate_billing_sms_receipt_number($group_id=0){
        $this->db->where($this->dx('billing_sms_payments.group_id').'="'.$group_id.'"',NULL,FALSE);
        $this->db->from('billing_sms_payments');
        $count = $this->db->count_all_results();
        $count = $count+1;
        if($count>=100){
        }else if($count>=10){
            $count = '0'.$count;
        }else{
            $count='00'.$count;
        }
        return $count;
    }




    /******************************************/
    function get_paying_group_id_array($from = 0,$to = 0){
        $arr = array();
        $this->db->select(
            array(
                $this->dx('billing_payments.group_id')." as group_id "
            )
        );
        $this->db->where($this->dx('billing_payments.active').'="1"',NULL,FALSE);
        if($from&&$to){
            $this->db->where($this->dx('investment_groups.created_on')." >= ".$from,NULL,FALSE);
            $this->db->where($this->dx('investment_groups.created_on')." <= ".$to,NULL,FALSE);
            $this->db->join('investment_groups',$this->dx('billing_payments.group_id')." = investment_groups.id ");
        }
        $this->db->group_by(
            array(
                $this->dx('group_id')
            )
        );
        $billing_payments = $this->db->get('billing_payments')->result();
        foreach($billing_payments as $billing_payment):
            $arr[] = $billing_payment->group_id;
        endforeach;
        return $arr;
    }

    function get_group_account_arrears($group_id=0){
        if($group_id){
            $amount_paid = $this->get_group_billing_paid_amount($group_id);
            $amount_payable = $this->get_group_billing_amount_payable($group_id);
            $arrears = $amount_payable - $amount_paid;
            return $arrears;
        }else{
            return FALSE;
        }
    }

    function get_group_last_invoice($group_id=0){
        $this->select_all_secure('billing_invoices');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        //$this->db->where('(DATE_FORMAT(FROM_UNIXTIME('.$this->dx('due_date').'),"%Y %d %m") =  "' . date('Y d m',time()) . '" OR DATE_FORMAT(FROM_UNIXTIME('.$this->dx('due_date').'),"%Y %d %m") >  "' . date('Y d m',time()) . '")', NULL, FALSE);
        $this->db->where($this->dx('due_date').'>="'.time().'"',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
         }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
         }
        $this->db->order_by($this->dx('due_date'),'DESC',FALSE);
        $this->db->limit(1);
        $invoice = $this->db->get('billing_invoices')->row();
        if(!$invoice){
            return FALSE;
        }else{
            $balance = $invoice->amount-$invoice->amount_paid;
            if($balance){
                return $balance;
            }
            else{
                return FALSE;
            }
        }
    }

    function get_group_last_invoice_id($group_id=0){
        $this->db->select('id');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
         }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
         }
        $this->db->order_by($this->dx('due_date'),'DESC',FALSE);
        $this->db->limit(1);
        return $this->db->get('billing_invoices')->row();
        
    }

    function count_unpaid_billing_invoices($group_id=0){  
         $this->db->where($this->dx('active').'="1"',NULL,FALSE);
         $this->db->where($this->dx('status').'!="1"',NULL,FALSE);
         if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
         }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
         }
         return $this->db->count_all_results('billing_invoices')?:0;
    }

    function get_group_unpaid_invoices($group_id=0){
        $this->select_all_secure('billing_invoices');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where('('.$this->dx('status').' ="" OR '.$this->dx('status').' IS NULL OR '.$this->dx('status').' = " " OR '.$this->dx('status').' = "0" )',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        return $this->db->get('billing_invoices')->result();
    }

    function get_group_unpaid_invoices_from_date($group_id=0,$date_from=0,$limit=0){
        $this->select_all_secure('billing_invoices');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
         $this->db->where($this->dx('status').'!="1"',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('billing_date').' >= "'.$date_from.'"',NULL,FALSE);
        if($limit){
            $this->db->limit($limit);
        }
        return $this->db->get('billing_invoices')->result();
    }

    function get_invoices_grouped_monthly($filter = array()){
        $this->db->select(array(
            'COUNT(id) as number_of_invoices',
            'SUM('.$this->dx('amount').') as amount_payable',
            'SUM('.$this->dx('tax').') as tax_payable',
            'SUM('.$this->dx('prorated_amount').') as   prorated_amount_payable',
            "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('billing_date')." ),'%Y %M ') as billing_month_year ",
        ));
        $this->db->where($this->dx('active').' ="1"',NULL,FALSE);
        if($filter){
            if(array_key_exists('billing_cycle', $filter) && $filter['billing_cycle']){
                $this->db->where($this->dx('billing_cycle').' ="'.$filter['billing_cycle'].'"',NULL,FALSE);
            }
            if(array_key_exists('group_ids', $filter)){
                $groups = '0';
                $group_ids = $filter['group_ids'];
                if($group_ids):
                    foreach ($group_ids as $group_id) {
                        if($groups){
                            $groups .=','.$group_id;
                        }else{
                            $groups = $group_id;
                        }
                    }
                endif;
                if($groups){
                    $this->db->where($this->dx('group_id').' IN ('.$groups.')',NULL,FALSE);
                }                
            }
        }
        $this->db->group_by(array(
            "billing_month_year"
        ));
        $this->db->order_by($this->dx('billing_date'),'DESC',FALSE);
        $invoices = $this->db->get('billing_invoices')->result();
        $arr = array();
        foreach ($invoices as $invoice) {
            $arr[date('Y',strtotime($invoice->billing_month_year))][date('Ym',strtotime($invoice->billing_month_year))] = array(
                'number_of_invoices' => $invoice->number_of_invoices,
                'amount_payable' => $invoice->amount_payable,
                'tax_payable' => $invoice->tax_payable,
                'prorated_amount_payable' => $invoice->prorated_amount_payable,
                'month' => date('Ym',strtotime($invoice->billing_month_year)),
            );
        }
        return $arr;
    }


    function get_group_last_unpaid_invoice($group_id=0){
        $this->select_all_secure('billing_invoices');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where('('.$this->dx('status').' ="" OR '.$this->dx('status').' IS NULL OR '.$this->dx('status').' = " " OR '.$this->dx('status').' = "0" )',NULL,FALSE);
        $this->db->order_by($this->dx('due_date'),'DESC',FALSE);
        $this->db->limit(1);
        return $this->db->get('billing_invoices')->row();
    }


    /*****************menu pairing******************/
    function insert_menu_package_pairing($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('billing_package_menu_pairing',$input);
    }

    function delete_package_menu_pairing($post=array()){
        if($post){
            foreach ($post as $key => $value) {
                $this->db->where('id', $key);
                $this->db->delete('billing_package_menu_pairing'); 
               /*$this->db->where('id',$key);
               $this->db->delete('billing_package_menu_pairing');*/
           }
        }
       return TRUE;
    }


    function get_package_menu_pairing($id=0){
        $this->select_all_secure('billing_package_menu_pairing');
        $this->db->where($this->dx('package_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('type').'="1"',NULL,FALSE);
        $pairing = $this->db->get('billing_package_menu_pairing')->result();
        $arr = array();
        if($pairing){
            foreach ($pairing as $pair) {
                $arr[$pair->id] = $pair->menu_id;
            }
        }
        return $arr;
    }

   function get_package_quick_action_menu_pairing($id=0){
    $this->select_all_secure('billing_package_menu_pairing');
        $this->db->where($this->dx('package_id').'="'.$id.'"',NULL,FALSE);
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('type').'="2"',NULL,FALSE);
        $pairing = $this->db->get('billing_package_menu_pairing')->result();
        $arr = array();
        if($pairing){
            foreach ($pairing as $pair) {
                $arr[$pair->id] = $pair->menu_id;
            }
        }
        return $arr;
   }



   function get_all_active_group_invoices($group_id = 0){
        $this->select_all_secure('billing_invoices');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->order_by($this->dx('billing_date'),'DESC',FALSE);
        $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
        return $this->db->get('billing_invoices')->result();
    }

    function get_all_active_group_payments($group_id = 0){
        $this->select_all_secure('billing_payments');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
        return $this->db->get('billing_payments')->result();
    }

    function billing_account_number(){
        $this->select_all_secure('investment_groups');
        $groups = $this->db->get('investment_groups')->result();

        return $groups;
    }


    /*************insert billing payment forwarder*******/
    function insert_billing_payments_forwarder($input=array(),$SKIP_VALIDATION = FALSE){
        return $this->insert_secure_data('billing_payments_forwarder',$input);
    }

    function update_billing_payments_forwarder($id,$input,$val=FALSE){
        return $this->update_secure_data($id,'billing_payments_forwarder',$input);
    }

    function get_ipn_forwarders(){
        $this->select_all_secure('billing_payments_forwarder');
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('billing_payments_forwarder')->result();
    }

    function get_ipn_validation_forwarders(){
        $this->db->select(array($this->dx('account_validation_end_point').' as endpoint '));
        $this->db->where($this->dx('active').'="1"',NULL,FALSE);
        return $this->db->get('billing_payments_forwarder')->result();
    }

    function get_billing_payment_forwarder($id=0){
        $this->select_all_secure('billing_payments_forwarder');
        $this->db->where('id',$id);
        return $this->db->get('billing_payments_forwarder')->row();
    }

    function delete_billing_payments_forwarder($id=0){
        $this->db->where('id', $id);
        return $this->db->delete('billing_payments_forwarder'); 
    }

    


}