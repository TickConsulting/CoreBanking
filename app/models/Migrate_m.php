<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migrate_m extends MY_Model{
    
    /**
     * The constructor
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('members/members_m');
        $this->load->model('dbfix_m');
        $this ->load->dbforge();
    }

    public function delete_group_data($group_id = 0,$table_name = ''){
        $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        return $this->db->delete($table_name);
    }

    public function unset_group_transaction_alerts($group_id = 0){
        $this->db->query("
            update transaction_alerts set 
                group_id='0',
                reconciled = '0'
            where ".$this->dx("group_id")." ='".$group_id."'");  
    }

    function count_rows($group_id = 0,$table_name = ''){
        $row_count = $this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE)->count_all_results($table_name);
        return $row_count;
    }

    function get_rows($group_id = 0,$table_name = ''){
        $this->select_all_secure($table_name);
        $this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
        return $this->db->get($table_name)->result();
    }

    function import_new_group($file_path=''){
        if(preg_match('/app\.chamasoft\.com/', $_SERVER['HTTP_HOST'])||preg_match('/eazzychama\.co\.ke/', $_SERVER['HTTP_HOST'])||preg_match('/eazzyclub\.co\.ug/', $_SERVER['HTTP_HOST'])){
            //Kindly do not remove this. Kindly, kindly
            die("You cannot do this activity on this application.");
        }
        if($file_path){
            /*
                create users
                Create groups
                Add Members to the group
                create all default data  (categories)
                create contribution
                create loan types
                create bank account
            */
            if($file_path){
                $file = $file_path;
            }else{
                $this->reset_group_data($group_id);
                $file = "./logs/group_demo_data.txt";
            }
            set_time_limit(0);
            ini_set('memory_limit','2048M');
            ini_set('max_execution_time', 2400);
            $directory = '';
            $zip_file = '';
            if(preg_match('/\.zip/', $file)){
                $zip_file = $file;
                if(!is_file($file)){
                    $this->session->set_flashdata('error','Error occured restoring group data');
                    return FALSE;
                }
                $directory = unzip_file($file);
                $files = (scandir($directory));
                foreach ($files as $file) {
                    if($file){
                        $file = $file;
                    }
                }
                $file = $directory.'/'.$file;
            }
            $contents = file_get_contents($file);
            //$contents = file_get_contents($file_path);
            if($contents){
                $data = json_decode($contents);
                if($data){
                    $users = isset($data->users)?$data->users:array();
                    $user_ids = $this->create_users($users);
                    $group  = isset($data->investment_groups)?$data->investment_groups:array();
                    $group_id = $this->create_user_group($group,$user_ids);
                    $members = isset($data->members)?$data->members:array();
                    $member_ids = $this->create_group_members($members,$group_id,$user_ids);
                    $group_data_ids = $this->create_default_data($data,$group_id,$user_ids,$member_ids);
                    $final_response = $this->fix_migrated_data($group_data_ids,$group_id,$user_ids,$member_ids);
                    return $group_id;
                }else{
                    $this->session->set_flashdata('error','File has empty data');
                    return FALSE;
                }
            }else{
                $this->session->set_flashdata('error','File has empty contents');
                return FALSE;
            }        
        }else{
            $this->session->set_flashdata('error','Invalid file path');
            return FALSE;
        }
    }

    function create_users($users= array()){
        $user_ids = array();
        foreach ($users as $user) {
            if($user){
                $additional_data = array();
                $identity = $user->phone?:$user->email;
                $old_id = $user->id;
                $phone = $user->phone;
                $password = $user->password;
                $email = $user->email;
                if($exist_user = $this->ion_auth->get_user_by_phone($phone)){
                    $id = $exist_user->id;
                }else{
                    if($exist_user = $this->ion_auth->get_user_by_email($email)){
                        $id = $exist_user->id;
                    }else{
                        unset($user->id);
                        unset($user->phone);
                        unset($user->password);
                        unset($user->email);
                        $additional_data = (array)$user;
                        $id = $this->ion_auth->register($phone,$password,$email,$additional_data,array(2),TRUE);
                    }
                }
                $update = array(
                    'password' => $password,
                );
                if($id){
                    $this->users_m->update_user($id,$update);
                    $user_ids[$old_id] = $id;
                }else{
                    print_r($user);die;
                }
                
            }
        }
        return $user_ids;
    }

    function create_user_group($group=array(),$user_ids=array()){
        if($existing_group = $this->groups_m->get_group_by_old_id($group->id)){
            $id = $existing_group->id;
        }else{
            $group->owner = isset($user_ids[$group->owner])?$user_ids[$group->owner]:$group->owner;
            $group->created_by = isset($user_ids[$group->created_by])?$user_ids[$group->created_by]:$group->created_by;
            $group->modified_by = isset($user_ids[$group->modified_by])?$user_ids[$group->modified_by]:$group->modified_by;
            $fields = $this->db->list_fields('investment_groups');
            $input = array();
            foreach ($fields as $field) {
                if($field == 'id' || $field == 'old_id'){
                    $input+=array(
                        'old_id'  =>  $group->id,
                    );
                }else{
                    if(isset($group->$field)){
                        if($group->$field==null){

                        }else{
                            $input+=array(
                                $field  =>  $group->$field,
                            );
                        }
                    }
                }
            }
            unset($input['id']);
            $id = $this->groups_m->insert($input);
        }
        return $id;
    }

    function create_group_members($members=array(),$group_id=0,$user_ids=array()){
        $member_ids = array();
        foreach ($members as $member) {
            $member->group_id = $group_id;
            $member->user_id = isset($user_ids[$member->user_id])?$user_ids[$member->user_id]:$member->user_id;
            $member->created_by = isset($user_ids[$member->created_by])?$user_ids[$member->created_by]:$member->created_by;
            $member->modified_by = isset($user_ids[$member->modified_by])?$user_ids[$member->modified_by]:$member->modified_by;
            $member->old_id = $member->id;
            if($existing_member = $this->members_m->get_group_old_member($member->id)){
                $member_ids[$member->old_id] = $existing_member->id;
            }else{
                unset($member->id);
                $member_data = array();
                foreach ($member as $member_key => $member_value) {
                    if($member_value == null){

                    }else{
                        $member_data[$member_key] = $member_value;
                    }
                }
                $id = $this->members_m->insert($member_data);
                $member_ids[$member->old_id] = $id;
            }
        }
        return $member_ids;
    }

    function create_default_data($data=array(),$group_id=0,$user_ids=array(),$member_ids=array()){
        $default_data_ids = array();
        $ignore_tables = array(
            'investment_groups',
            'members',
            'users',
            'users_groups'
        );
        $account_tables = array(
            'sacco' => 'sacco_accounts',
            'mobile' => 'mobile_money_accounts',
            'bank' => 'bank_accounts',
            'petty' => 'petty_cash_accounts',
        );
        $existing_tables = $this->db->list_tables();
        foreach ($data as $table => $table_datas) {
            if(in_array($table, $existing_tables)){
                if(!in_array($table, $ignore_tables)){
                    $this->dbfix_m->add_column($table, array(
                        'old_id' => array(
                            'type' => 'blob'
                        )
                    ));
                    $categories_ids = array();
                    foreach ($table_datas as $key => $value) {
                        if(isset($value->group_id)){
                            $value->group_id = $group_id;
                        }
                        if(isset($value->created_by)){
                            $value->created_by = isset($user_ids[$value->created_by])?$user_ids[$value->created_by]:$value->created_by;
                        }
                        if(isset($value->modified_by)){
                            $value->modified_by = isset($user_ids[$value->modified_by])?$user_ids[$value->modified_by]:$value->modified_by;
                        }
                        if(isset($value->member_id)){
                            $value->member_id = isset($member_ids[$value->member_id])?$member_ids[$value->member_id]:$value->member_id;
                        }
                        $value->old_id = $value->id;
                        if($category = $this->get_old_id($table,$value->id,$group_id)){
                            if(in_array($table, $account_tables)){
                                $account_key = array_search($table, $account_tables).'-';
                                $categories_ids[$account_key.$value->id] = $account_key.$category->id;
                            }else{
                                $categories_ids[$value->id] = $category->id;
                            }
                        }else{
                            unset($value->id);
                            $id = $this->insert_table($table,$value);
                            if(in_array($table, $account_tables)){
                                $account_key = array_search($table, $account_tables).'-';
                                $categories_ids[$account_key.$value->old_id] = $account_key.$id;
                            }else{
                                $categories_ids[$value->old_id] = $id;
                            }
                        }
                    }
                    $default_data_ids[$table] = $categories_ids;
                }
            }else{
                continue;
            }
        }
        return ($default_data_ids);
    }

    protected $is_to_be_stopped = FALSE;

    function fix_migrated_data($group_data_ids=array(),$group_id=0,$user_ids=array(),$member_ids=array()){
        $tables = $this->db->list_tables();
        $fields_label = array();
        foreach ($tables as $table) {
            $fields = $this->db->list_fields($table);
            $table_fields = array();
            foreach ($fields as $field) {
                if(preg_match('/_id/', $field)){
                    $table_fields[] = $field;
                }
            }
            $fields_label[$table] = $table_fields;
        }
        $default_tables = array(
            'asset_categories',
            'bank_accounts',
            'contributions',
            'depositors',
            'debtors',
            'expense_categories',
            'fine_categories',
            'group_roles',
            'income_categories',
            'paybills',
            'petty_cash_accounts',
            'setup_tasks_tracker',
            'loan_types',
            'sacco_accounts',
            'mobile_money_accounts',
            'accounts',
            'transaction_alerts'
        );

        $fix_tables = array(
            'regular_contribution_settings'
        );
        $group_data_ids['bank_accounts'] = isset($group_data_ids['bank_accounts'])?$group_data_ids['bank_accounts']:array();
        $group_data_ids['sacco_accounts'] = isset($group_data_ids['sacco_accounts'])?$group_data_ids['sacco_accounts']:array();
        $group_data_ids['mobile_money_accounts'] = isset($group_data_ids['mobile_money_accounts'])?$group_data_ids['mobile_money_accounts']:array();
        $group_data_ids['petty_cash_accounts'] = isset($group_data_ids['petty_cash_accounts'])?$group_data_ids['petty_cash_accounts']:array();
        $accounts = array_merge($group_data_ids['bank_accounts'],$group_data_ids['sacco_accounts']);
        $accounts = array_merge($accounts,$group_data_ids['mobile_money_accounts']);
        $accounts = array_merge($accounts,$group_data_ids['petty_cash_accounts']);
        $group_data_ids['accounts'] = $accounts;
        foreach ($group_data_ids as $table_name => $datas) {
            if(!in_array($table_name, $default_tables)){
                $group_table_datas = $this->get_all_group_data($table_name,$group_id);
                $new_group_table_datas = array();
                foreach ($group_table_datas as $key_row=>$group_table_data) {
                    $new_object = new StdClass();
                    $new_object->id = $group_table_data->id;
                    if(property_exists($group_table_data,'is_deleted')){
                        if($group_table_data->is_deleted){
                            $new_object->is_deleted = $group_table_data->is_deleted;
                        }else{
                            unset($group_table_data->is_deleted);
                        }
                    }
                    if(property_exists($group_table_data,'asset_category_id')){
                        if(isset($group_data_ids['asset_categories'])){
                            $categories = $group_data_ids['asset_categories'];
                            $new_object->asset_category_id = isset($categories[$group_table_data->asset_category_id])?$categories[$group_table_data->asset_category_id]:$group_table_data->asset_category_id;
                        }
                    }if(property_exists($group_table_data,'contribution_id')){
                        if(isset($group_data_ids['contributions'])){
                            $categories = $group_data_ids['contributions'];
                            $new_object->contribution_id = isset($categories[$group_table_data->contribution_id])?$categories[$group_table_data->contribution_id]:$group_table_data->contribution_id;
                        }
                    }if(property_exists($group_table_data,'contribution_to_id')){
                        if(isset($group_data_ids['contributions'])){
                            $categories = $group_data_ids['contributions'];
                            $new_object->contribution_to_id = isset($categories[$group_table_data->contribution_to_id])?$categories[$group_table_data->contribution_to_id]:$group_table_data->contribution_to_id;
                        }
                    }if(property_exists($group_table_data,'contribution_from_id')){
                        if(isset($group_data_ids['contributions'])){
                            $categories = $group_data_ids['contributions'];
                            $new_object->contribution_from_id = isset($categories[$group_table_data->contribution_from_id])?$categories[$group_table_data->contribution_from_id]:$group_table_data->contribution_from_id;
                        }
                    }if(property_exists($group_table_data,'depositor_id')){
                        if(isset($group_data_ids['depositors'])){
                            $categories = $group_data_ids['depositors'];
                            $new_object->depositor_id = isset($categories[$group_table_data->depositor_id])?$categories[$group_table_data->depositor_id]:$group_table_data->depositor_id;
                        }
                    }if(property_exists($group_table_data,'debtor_id')){
                        if(isset($group_data_ids['debtors'])){
                            $categories = $group_data_ids['debtors'];
                            $new_object->debtor_id = isset($categories[$group_table_data->debtor_id])?$categories[$group_table_data->debtor_id]:$group_table_data->debtor_id;
                        }
                    }if(property_exists($group_table_data,'expense_category_id')){
                        if(isset($group_data_ids['expense_categories'])){
                            $categories = $group_data_ids['expense_categories'];
                            $new_object->expense_category_id = isset($categories[$group_table_data->expense_category_id])?$categories[$group_table_data->expense_category_id]:$group_table_data->expense_category_id;
                        }
                    }if(property_exists($group_table_data,'fine_category_id')){
                        if(isset($group_data_ids['fine_categories'])){
                            $categories = $group_data_ids['fine_categories'];
                            $new_object->fine_category_id = isset($categories[$group_table_data->fine_category_id])?$categories[$group_table_data->fine_category_id]:$group_table_data->fine_category_id;
                        }
                    }if(property_exists($group_table_data,'group_role_id')){
                        if(isset($group_data_ids['group_roles'])){
                            $categories = $group_data_ids['group_roles'];
                            $new_object->group_role_id = isset($categories[$group_table_data->group_role_id])?$categories[$group_table_data->group_role_id]:$group_table_data->group_role_id;
                        }
                    }if(property_exists($group_table_data,'income_category_id')){
                        if(isset($group_data_ids['income_categories'])){
                            $categories = $group_data_ids['income_categories'];
                            $new_object->income_category_id = isset($categories[$group_table_data->income_category_id])?$categories[$group_table_data->income_category_id]:$group_table_data->income_category_id;
                        }
                    }if(property_exists($group_table_data,'paybill_id')){
                        if(isset($group_data_ids['paybills'])){
                            $categories = $group_data_ids['paybills'];
                            $new_object->paybill_id = isset($categories[$group_table_data->paybill_id])?$categories[$group_table_data->paybill_id]:$group_table_data->paybill_id;
                        }
                    }if(property_exists($group_table_data,'loan_type_id')){
                        if(isset($group_data_ids['loan_types'])){
                            $categories = $group_data_ids['loan_types'];
                            $new_object->loan_type_id = isset($categories[$group_table_data->loan_type_id])?$categories[$group_table_data->loan_type_id]:$group_table_data->loan_type_id;
                        }
                    }if(property_exists($group_table_data,'account_id')){
                        $categories = $group_data_ids['accounts'];
                        $new_object->account_id = isset($categories[$group_table_data->account_id])?$categories[$group_table_data->account_id]:$group_table_data->account_id;
                    }if(property_exists($group_table_data,'to_account_id')){
                        $categories = $group_data_ids['accounts'];
                        $new_object->to_account_id = isset($categories[$group_table_data->to_account_id])?$categories[$group_table_data->to_account_id]:$group_table_data->to_account_id;
                    }if(property_exists($group_table_data,'from_account_id')){
                        $categories = $group_data_ids['accounts'];
                        $new_object->from_account_id = isset($categories[$group_table_data->from_account_id])?$categories[$group_table_data->from_account_id]:$group_table_data->from_account_id;
                    }if(property_exists($group_table_data,'transaction_alert_id')){
                        if(isset($group_data_ids['transaction_alerts'])){
                            $categories = $group_data_ids['transaction_alerts'];
                            $new_object->transaction_alert_id = isset($categories[$group_table_data->transaction_alert_id])?$categories[$group_table_data->transaction_alert_id]:$group_table_data->transaction_alert_id;
                        }
                    }if(property_exists($group_table_data,'bank_loan_id')){
                        if(isset($group_data_ids['bank_loans'])){
                            $categories = $group_data_ids['bank_loans'];
                            $new_object->bank_loan_id = isset($categories[$group_table_data->bank_loan_id])?$categories[$group_table_data->bank_loan_id]:$group_table_data->bank_loan_id;
                        }
                    }if(property_exists($group_table_data,'fine_category_to_id')){
                        if(isset($group_data_ids['fine_categories'])){
                            $categories = $group_data_ids['fine_categories'];
                            $new_object->fine_category_to_id = isset($categories[$group_table_data->fine_category_to_id])?$categories[$group_table_data->fine_category_to_id]:$group_table_data->fine_category_to_id;
                        }
                    }if(property_exists($group_table_data,'loan_from_id')){
                        if(isset($group_data_ids['loans'])){
                            $categories = $group_data_ids['loans'];
                            $new_object->loan_from_id = isset($categories[$group_table_data->loan_from_id])?$categories[$group_table_data->loan_from_id]:$group_table_data->loan_from_id;
                        }
                    }if(property_exists($group_table_data,'loan_to_id')){
                        if(isset($group_data_ids['loans'])){
                            $categories = $group_data_ids['loans'];
                            $new_object->loan_to_id = isset($categories[$group_table_data->loan_to_id])?$categories[$group_table_data->loan_to_id]:$group_table_data->loan_to_id;
                        }
                    }if(property_exists($group_table_data,'member_to_id')){
                        $categories = $member_ids;
                        $new_object->member_to_id = isset($categories[$group_table_data->member_to_id])?$categories[$group_table_data->member_to_id]:$group_table_data->member_to_id;
                    }if(property_exists($group_table_data,'member_from_id')){
                        $categories = $member_ids;
                        $new_object->member_from_id = isset($categories[$group_table_data->member_from_id])?$categories[$group_table_data->member_from_id]:$group_table_data->member_from_id;
                    }if(property_exists($group_table_data,'share_transfer_recipient_member_id')){
                        $categories = $member_ids;
                        $new_object->share_transfer_recipient_member_id = isset($categories[$group_table_data->share_transfer_recipient_member_id])?$categories[$group_table_data->share_transfer_recipient_member_id]:$group_table_data->share_transfer_recipient_member_id;
                    }if(property_exists($group_table_data,'debtor_loan_id')){
                        if(isset($group_data_ids['debtor_loans'])){
                            $categories = $group_data_ids['debtor_loans'];
                            $new_object->debtor_loan_id = isset($categories[$group_table_data->debtor_loan_id])?$categories[$group_table_data->debtor_loan_id]:$group_table_data->debtor_loan_id;
                        }
                    }if(property_exists($group_table_data,'debtor_id')){
                        if(isset($group_data_ids['debtors'])){
                            $categories = $group_data_ids['debtors'];
                            $new_object->debtor_id = isset($categories[$group_table_data->debtor_id])?$categories[$group_table_data->debtor_id]:$group_table_data->debtor_id;
                        }
                    }if(property_exists($group_table_data,'contribution_transfer_id')){
                        if(isset($group_data_ids['contribution_transfers'])){
                            $categories = $group_data_ids['contribution_transfers'];
                            $new_object->contribution_transfer_id = isset($categories[$group_table_data->contribution_transfer_id])?$categories[$group_table_data->contribution_transfer_id]:$group_table_data->contribution_transfer_id;
                        }
                    }if(property_exists($group_table_data,'incoming_loan_transfer_invoice_id')){
                        if(isset($group_data_ids['loan_invoices'])){
                            $categories = $group_data_ids['loan_invoices'];
                            $new_object->incoming_loan_transfer_invoice_id = isset($categories[$group_table_data->incoming_loan_transfer_invoice_id])?$categories[$group_table_data->incoming_loan_transfer_invoice_id]:$group_table_data->incoming_loan_transfer_invoice_id;
                        }
                    }if(property_exists($group_table_data,'incoming_contribution_transfer_id')){
                        if(isset($group_data_ids['contribution_transfers'])){
                            $categories = $group_data_ids['contribution_transfers'];
                            $new_object->incoming_contribution_transfer_id = isset($categories[$group_table_data->incoming_contribution_transfer_id])?$categories[$group_table_data->incoming_contribution_transfer_id]:$group_table_data->incoming_contribution_transfer_id;
                        }
                    }if(property_exists($group_table_data,'old_incoming_contribution_transfer_id')){
                        if(isset($group_data_ids['contribution_transfers'])){
                            $categories = $group_data_ids['contribution_transfers'];
                            $new_object->old_incoming_contribution_transfer_id = isset($categories[$group_table_data->old_incoming_contribution_transfer_id])?$categories[$group_table_data->old_incoming_contribution_transfer_id]:$group_table_data->old_incoming_contribution_transfer_id;
                        }
                    }if(property_exists($group_table_data,'debtor_loan_invoice_id')){
                        if(isset($group_data_ids['debtor_loan_invoices'])){
                            $categories = $group_data_ids['debtor_loan_invoices'];
                            $new_object->debtor_loan_invoice_id = isset($categories[$group_table_data->debtor_loan_invoice_id])?$categories[$group_table_data->debtor_loan_invoice_id]:$group_table_data->debtor_loan_invoice_id;
                        }
                    }if(property_exists($group_table_data,'debtor_loan_payment_id')){
                        if(isset($group_data_ids['debtor_loan_repayments'])){
                            $categories = $group_data_ids['debtor_loan_repayments'];
                            $new_object->debtor_loan_payment_id = isset($categories[$group_table_data->debtor_loan_payment_id])?$categories[$group_table_data->debtor_loan_payment_id]:$group_table_data->debtor_loan_payment_id;
                        }
                    }if(property_exists($group_table_data,'debtor_loan_payment_id')){
                        if(isset($group_data_ids['debtor_loan_repayments'])){
                            $categories = $group_data_ids['debtor_loan_repayments'];
                            $new_object->debtor_loan_payment_id = isset($categories[$group_table_data->debtor_loan_payment_id])?$categories[$group_table_data->debtor_loan_payment_id]:$group_table_data->debtor_loan_payment_id;
                        }
                    }if(property_exists($group_table_data,'stock_sale_id')){
                        if(isset($group_data_ids['stock_sales'])){
                            $categories = $group_data_ids['stock_sales'];
                            $new_object->stock_sale_id = isset($categories[$group_table_data->stock_sale_id])?$categories[$group_table_data->stock_sale_id]:$group_table_data->stock_sale_id;
                        }
                    }if(property_exists($group_table_data,'money_market_investment_id')){
                        if(isset($group_data_ids['money_market_investments'])){
                            $categories = $group_data_ids['money_market_investments'];
                            $new_object->money_market_investment_id = isset($categories[$group_table_data->money_market_investment_id])?$categories[$group_table_data->money_market_investment_id]:$group_table_data->money_market_investment_id;
                        }
                    }if(property_exists($group_table_data,'asset_id')){
                        if(isset($group_data_ids['assets'])){
                            $categories = $group_data_ids['assets'];
                            $new_object->asset_id = isset($categories[$group_table_data->asset_id])?$categories[$group_table_data->asset_id]:$group_table_data->asset_id;
                        }
                    }if(property_exists($group_table_data,'bank_loan_id')){
                        if(isset($group_data_ids['bank_loans'])){
                            $categories = $group_data_ids['bank_loans'];
                            $new_object->bank_loan_id = isset($categories[$group_table_data->bank_loan_id])?$categories[$group_table_data->bank_loan_id]:$group_table_data->bank_loan_id;
                        }
                    }if(property_exists($group_table_data,'withdrawal_id')){
                        if(isset($group_data_ids['withdrawals'])){
                            $categories = $group_data_ids['withdrawals'];
                            $new_object->withdrawal_id = isset($categories[$group_table_data->withdrawal_id])?$categories[$group_table_data->withdrawal_id]:$group_table_data->withdrawal_id;
                        }
                    }if(property_exists($group_table_data,'contribution_refund_id')){
                        if(isset($group_data_ids['contribution_refunds'])){
                            $categories = $group_data_ids['contribution_refunds'];
                            $new_object->contribution_refund_id = isset($categories[$group_table_data->contribution_refund_id])?$categories[$group_table_data->contribution_refund_id]:$group_table_data->contribution_refund_id;
                        }
                    }if(property_exists($group_table_data,'stock_id')){
                        if(isset($group_data_ids['stocks'])){
                            $categories = $group_data_ids['stocks'];
                            $new_object->stock_id = isset($categories[$group_table_data->stock_id])?$categories[$group_table_data->stock_id]:$group_table_data->stock_id;
                        }
                    }if(property_exists($group_table_data,'loan_id')){
                        if(isset($group_data_ids['loans'])){
                            $categories = $group_data_ids['loans'];
                            $new_object->loan_id = isset($categories[$group_table_data->loan_id])?$categories[$group_table_data->loan_id]:$group_table_data->loan_id;
                        }
                    }if(property_exists($group_table_data,'account_transfer_id')){
                        if(isset($group_data_ids['account_transfers'])){
                            $categories = $group_data_ids['account_transfers'];
                            $new_object->account_transfer_id = isset($categories[$group_table_data->account_transfer_id])?$categories[$group_table_data->account_transfer_id]:$group_table_data->account_transfer_id;
                        }
                    }if(property_exists($group_table_data,'loan_repayment_id')){
                        if(isset($group_data_ids['loan_repayments'])){
                            $categories = $group_data_ids['loan_repayments'];
                            $new_object->loan_repayment_id = isset($categories[$group_table_data->loan_repayment_id])?$categories[$group_table_data->loan_repayment_id]:$group_table_data->loan_repayment_id;
                        }
                    }if(property_exists($group_table_data,'merged_transaction_alert_id')){
                        if(isset($group_data_ids['transaction_alerts'])){
                            $categories = $group_data_ids['transaction_alerts'];
                            $new_object->merged_transaction_alert_id = isset($categories[$group_table_data->merged_transaction_alert_id])?$categories[$group_table_data->merged_transaction_alert_id]:$group_table_data->merged_transaction_alert_id;
                        }
                    }if(property_exists($group_table_data,'parent_transaction_alert_id')){
                        if(isset($group_data_ids['transaction_alerts'])){
                            $categories = $group_data_ids['transaction_alerts'];
                            $new_object->parent_transaction_alert_id = isset($categories[$group_table_data->parent_transaction_alert_id])?$categories[$group_table_data->parent_transaction_alert_id]:$group_table_data->parent_transaction_alert_id;
                        }
                    }if(property_exists($group_table_data,'deposit_id')){
                        if(isset($group_data_ids['deposits'])){
                            $categories = $group_data_ids['deposits'];
                            $new_object->deposit_id = isset($categories[$group_table_data->deposit_id])?$categories[$group_table_data->deposit_id]:$group_table_data->deposit_id;
                        }
                    }if(property_exists($group_table_data,'deposit_id')){
                        if(isset($group_data_ids['deposits'])){
                            $categories = $group_data_ids['deposits'];
                            $new_object->deposit_id = isset($categories[$group_table_data->deposit_id])?$categories[$group_table_data->deposit_id]:$group_table_data->deposit_id;
                        }
                    }if(property_exists($group_table_data,'bank_loan_repayment_id')){
                        if(isset($group_data_ids['bank_loan_repayments'])){
                            $categories = $group_data_ids['bank_loan_repayments'];
                            $new_object->bank_loan_repayment_id = isset($categories[$group_table_data->bank_loan_repayment_id])?$categories[$group_table_data->bank_loan_repayment_id]:$group_table_data->bank_loan_repayment_id;
                        }
                    }if(property_exists($group_table_data,'debtor_loan_repayment_id')){
                        if(isset($group_data_ids['debtor_loan_repayments'])){
                            $categories = $group_data_ids['debtor_loan_repayments'];
                            $new_object->debtor_loan_repayment_id = isset($categories[$group_table_data->debtor_loan_repayment_id])?$categories[$group_table_data->debtor_loan_repayment_id]:$group_table_data->debtor_loan_repayment_id;
                        }
                    }if(property_exists($group_table_data,'checkoff_id')){
                        if(isset($group_data_ids['checkoffs'])){
                            $categories = $group_data_ids['checkoffs'];
                            $new_object->checkoff_id = isset($categories[$group_table_data->checkoff_id])?$categories[$group_table_data->checkoff_id]:$group_table_data->checkoff_id;
                        }
                    }if(property_exists($group_table_data,'loan_application_id')){
                        if(isset($group_data_ids['loan_applications'])){
                            $categories = $group_data_ids['loan_applications'];
                            $new_object->loan_application_id = isset($categories[$group_table_data->loan_application_id])?$categories[$group_table_data->loan_application_id]:$group_table_data->loan_application_id;
                        }
                    }if(property_exists($group_table_data,'loan_applicant_user_id')){
                        $categories = $user_ids;
                        $new_object->loan_applicant_user_id = isset($categories[$group_table_data->loan_applicant_user_id])?$categories[$group_table_data->loan_applicant_user_id]:$group_table_data->loan_applicant_user_id;
                        
                    }if(property_exists($group_table_data,'loan_member_id')){
                        $categories = $member_ids;
                        $new_object->loan_member_id = isset($categories[$group_table_data->loan_member_id])?$categories[$group_table_data->loan_member_id]:$group_table_data->loan_member_id;
                        
                    }if(property_exists($group_table_data,'hr_member_id')){
                        $categories = $member_ids;
                        $new_object->hr_member_id = isset($categories[$group_table_data->hr_member_id])?$categories[$group_table_data->hr_member_id]:$group_table_data->hr_member_id;
                        
                    }if(property_exists($group_table_data,'hr_user_id')){
                        $categories = $user_ids;
                        $new_object->hr_user_id = isset($categories[$group_table_data->hr_user_id])?$categories[$group_table_data->hr_user_id]:$group_table_data->hr_user_id;
                        
                    }if(property_exists($group_table_data,'fine_id')){
                        if(isset($group_data_ids['fines'])){
                            $categories = $group_data_ids['fines'];
                            $new_object->fine_id = isset($categories[$group_table_data->fine_id])?$categories[$group_table_data->fine_id]:$group_table_data->fine_id;
                        }
                    }if(property_exists($group_table_data,'sacco_manager_member_id')){
                        $categories = $member_ids;
                        $new_object->sacco_manager_member_id = isset($categories[$group_table_data->sacco_manager_member_id])?$categories[$group_table_data->sacco_manager_member_id]:$group_table_data->sacco_manager_member_id;
                        
                    }if(property_exists($group_table_data,'member_supervisor_id')){
                        $categories = $member_ids;
                        $new_object->member_supervisor_id = isset($categories[$group_table_data->member_supervisor_id])?$categories[$group_table_data->member_supervisor_id]:$group_table_data->member_supervisor_id;
                        
                    }if(property_exists($group_table_data,'loan_request_applicant_member_id')){
                        $categories = $member_ids;
                        $new_object->loan_request_applicant_member_id = isset($categories[$group_table_data->loan_request_applicant_member_id])?$categories[$group_table_data->loan_request_applicant_member_id]:$group_table_data->loan_request_applicant_member_id;
                        
                    }if(property_exists($group_table_data,'guarantor_member_id')){
                        $categories = $member_ids;
                        $new_object->guarantor_member_id = isset($categories[$group_table_data->guarantor_member_id])?$categories[$group_table_data->guarantor_member_id]:$group_table_data->guarantor_member_id;
                        
                    }if(property_exists($group_table_data,'loan_applicant_member_id')){
                        $categories = $member_ids;
                        $new_object->loan_applicant_member_id = isset($categories[$group_table_data->loan_applicant_member_id])?$categories[$group_table_data->loan_applicant_member_id]:$group_table_data->loan_applicant_member_id;
                        
                    }if(property_exists($group_table_data,'loan_request_applicant_user_id')){
                        $categories = $user_ids;
                        $new_object->loan_request_applicant_user_id = isset($categories[$group_table_data->loan_request_applicant_user_id])?$categories[$group_table_data->loan_request_applicant_user_id]:$group_table_data->loan_request_applicant_user_id;
                        
                    }if(property_exists($group_table_data,'guarantor_user_id')){
                        $categories = $user_ids;
                        $new_object->guarantor_user_id = isset($categories[$group_table_data->guarantor_user_id])?$categories[$group_table_data->guarantor_user_id]:$group_table_data->guarantor_user_id;
                        
                    }if(property_exists($group_table_data,'fine_parent_loan_invoice_id')){
                        if(isset($group_data_ids['loan_invoices'])){
                            $categories = $group_data_ids['loan_invoices'];
                            $new_object->fine_parent_loan_invoice_id = isset($categories[$group_table_data->fine_parent_loan_invoice_id])?$categories[$group_table_data->fine_parent_loan_invoice_id]:$group_table_data->fine_parent_loan_invoice_id;
                        }
                    }if(property_exists($group_table_data,'loan_request_member_id')){
                        $categories = $member_ids;
                        $new_object->loan_request_member_id = isset($categories[$group_table_data->loan_request_member_id])?$categories[$group_table_data->loan_request_member_id]:$group_table_data->loan_request_member_id;
                        
                    }if(property_exists($group_table_data,'signatory_user_id')){
                        $categories = $user_ids;
                        $new_object->signatory_user_id = isset($categories[$group_table_data->signatory_user_id])?$categories[$group_table_data->signatory_user_id]:$group_table_data->signatory_user_id;
                        
                    }if(property_exists($group_table_data,'signatory_member_id')){
                        $categories = $member_ids;
                        $new_object->signatory_member_id = isset($categories[$group_table_data->signatory_member_id])?$categories[$group_table_data->signatory_member_id]:$group_table_data->signatory_member_id;
                        
                    }if(property_exists($group_table_data,'loan_payment_id')){
                        if(isset($group_data_ids['loan_repayments'])){
                            $categories = $group_data_ids['loan_repayments'];
                            $new_object->loan_payment_id = isset($categories[$group_table_data->loan_payment_id])?$categories[$group_table_data->loan_payment_id]:$group_table_data->loan_payment_id;
                        }
                    }if(property_exists($group_table_data,'member_suspension_request_id')){
                        if(isset($group_data_ids['member_suspension_requests'])){
                            $categories = $group_data_ids['member_suspension_requests'];
                            $new_object->member_suspension_request_id = isset($categories[$group_table_data->member_suspension_request_id])?$categories[$group_table_data->member_suspension_request_id]:$group_table_data->member_suspension_request_id;
                        }
                    }if(property_exists($group_table_data,'organization_role_id')){
                        if(isset($group_data_ids['organization_roles'])){
                            $categories = $group_data_ids['organization_roles'];
                            $new_object->organization_role_id = isset($categories[$group_table_data->organization_role_id])?$categories[$group_table_data->organization_role_id]:$group_table_data->organization_role_id;
                        }
                    }if(property_exists($group_table_data,'withdrawal_account_id')){
                        $categories = $group_data_ids['accounts'];
                        $new_object->withdrawal_account_id = isset($categories[$group_table_data->withdrawal_account_id])?$categories[$group_table_data->withdrawal_account_id]:$group_table_data->withdrawal_account_id;
                    }if(property_exists($group_table_data,'deposit_account_id')){
                        $categories = $group_data_ids['accounts'];
                        $new_object->deposit_account_id = isset($categories[$group_table_data->deposit_account_id])?$categories[$group_table_data->deposit_account_id]:$group_table_data->deposit_account_id;
                    }if(property_exists($group_table_data,'invoice_id')){
                        if(isset($group_data_ids['invoices'])){
                            $categories = $group_data_ids['invoices'];
                            $new_object->invoice_id = isset($categories[$group_table_data->invoice_id])?$categories[$group_table_data->invoice_id]:$group_table_data->invoice_id;
                        }
                    }if(property_exists($group_table_data,'loan_transfer_invoice_id')){
                        if(isset($group_data_ids['loan_invoices'])){
                            $categories = $group_data_ids['loan_invoices'];
                            $new_object->loan_transfer_invoice_id = isset($categories[$group_table_data->loan_transfer_invoice_id])?$categories[$group_table_data->loan_transfer_invoice_id]:$group_table_data->loan_transfer_invoice_id;
                        }
                    }if(property_exists($group_table_data,'share_transfer_giver_member_id')){
                        $categories = $member_ids;
                        $new_object->share_transfer_giver_member_id = isset($categories[$group_table_data->share_transfer_giver_member_id])?$categories[$group_table_data->share_transfer_giver_member_id]:$group_table_data->share_transfer_giver_member_id;
                    }if(property_exists($group_table_data,'share_transfer_recipient_member_id')){
                        $categories = $member_ids;
                        $new_object->share_transfer_recipient_member_id = isset($categories[$group_table_data->share_transfer_recipient_member_id])?$categories[$group_table_data->share_transfer_recipient_member_id]:$group_table_data->share_transfer_recipient_member_id;
                    }if(property_exists($group_table_data,'contribution_ids')){
                        if(isset($group_data_ids['contributions'])){
                            $categories = $group_data_ids['contributions'];
                            $contribution_ids = unserialize($group_table_data->contribution_ids);
                            $ids = array();
                            if($contribution_ids){
                                foreach ($contribution_ids as $contribution_id) {
                                    $ids[] = isset($categories[$contribution_id])?$categories[$contribution_id]:$contribution_id;
                                }
                            }
                            $new_object->contribution_ids = serialize($ids);
                        }
                    }if(property_exists($group_table_data,'fine_category_ids')){
                        if(isset($group_data_ids['fine_categories'])){
                            $categories = $group_data_ids['fine_categories'];
                            $fine_category_ids = unserialize($group_table_data->fine_category_ids);
                            $ids = array();
                            if($fine_category_ids){
                                foreach ($fine_category_ids as $fine_category_id) {
                                    $ids[] = isset($categories[$fine_category_id])?$categories[$fine_category_id]:$fine_category_id;
                                }
                            }
                            $new_object->fine_category_ids = serialize($ids);
                        }
                    }if(property_exists($group_table_data,'loan_ids')){
                        if(isset($group_data_ids['loans'])){
                            $categories = $group_data_ids['loans'];
                            $loan_ids = unserialize($group_table_data->loan_ids);
                            $ids = array();
                            if($loan_ids){
                                foreach ($loan_ids as $loan_id) {
                                    $ids[] = isset($categories[$loan_id])?$categories[$loan_id]:$loan_id;
                                }
                            }
                            $new_object->loan_ids = serialize($ids);
                        }
                    }
                    if($new_object){
                        if(count((array)$new_object)>1){
                            $new_group_table_datas[$key_row] = $new_object;
                        }
                    }   
                }
                if($new_group_table_datas){
                    $this->update_batch_data($table_name,$new_group_table_datas);
                }
            }   
        }
    }

    function get_old_id($table_name='',$id=0,$group_id=0){
        
        $this->select_all_secure($table_name);
        if($this->db->field_exists('group_id',$table_name)){
            $this->db->where("group_id",$group_id);
        }
        $this->db->where($this->dx('old_id').' = "'.$id.'"',NULL,FALSE);
        return $this->db->get($table_name)->row();
    }

    function insert_table($table_name='',$input=array(),$SKIP_VALIDATION = FALSE){
        $data = array();
        // foreach ($update_value as $key => $value) {
        //     if($value==null){

        //     }else{
        //         $update[$key] = trim($value);
        //     }
        // }
        $fields = $this->db->list_fields($table_name);
        foreach ($fields as $field) {
            if($field == 'id' || $field == 'old_id'){
                $data+=array(
                    'old_id'  =>  $input->old_id,
                );
            }else{
                if(isset($input->$field)){
                    if($input->$field || is_numeric($input->$field)){
                        $data+=array(
                            $field  =>  $input->$field,
                        );
                    }else{
                    }
                }
            }
        }
        if($table_name == 'regular_contribution_settings'){
            // $this->insert_secure_data($table_name,$data);
            // print_r($data);die;
        }
        return $this->insert_secure_data($table_name,$data);
    }

    function get_group_by_old_id($id=0){
        $this->select_all_secure('investment_groups');
        $this->db->where($this->dx('old_id').' = "'.$id.'"',NULL,FALSE);
        return $this->db->get('investment_groups')->row();
    }

    function import_group(){
        if(preg_match('/app\.chamasoft\.com/', $_SERVER['HTTP_HOST'])||preg_match('/eazzychama\.co\.ke/', $_SERVER['HTTP_HOST'])||preg_match('/eazzyclub\.co\.ug/', $_SERVER['HTTP_HOST'])){
            //Kindly do not remove this. Kindly, kindly
            die("You cannot do this activity on this application.");
        }
        $file = "./app/logs/Mwala Associates_3675_1578985375.txt";
        $contents = file_get_contents($file);
        $group_data = json_decode($contents);
        $users = isset($group_data->users)?$group_data->users:array();
        $group = isset($group_data->investment_groups)?$group_data->investment_groups:array();
        $members = isset($group_data->members)?$group_data->members:array();
        $created_tables = array();
        // print_r(array_keys((array)$group_data)); die;
        // print_r($group_data->asset_categories); die;
        if(empty($users)){
           
        }else{
            // $user_ids_tracker = array();
            $ids_tracker = array();
            foreach ($users as $user) {
                $user_data = array();
                foreach ($user as $key => $value) {
                    if($value == null){

                    }else{
                        $user_data[$key] = $value;
                    }
                }
                $user = (array)$user;
                $old_id = $user['id'];
                unset($user_data['id']);
                unset($user_data['user_id']);
                $user_id = $this->insert_secure_data('users',$user_data);
                if($user_id){
                     // $created_tables['users'] = array($user_id);
                    // $user_ids_tracker[$user_id] = 
                    echo 'created user'.$user['first_name']." \n";
                    $ids_tracker['user_id'][$old_id] = $user_id;
                    // print_r($ids_tracker);
                    if($old_id == $group->owner){
                        // print_r($new_group_owner);die;
                        $new_group_owner = $user_id;
                        // $old_group_owner = $user->id;
                    }
                }
            }

            unset($group_data->users);
            $group = (array)$group;
            if(empty($group)){

            }else{
                $group_data = array();
                foreach ($group as $key => $value) {
                    if($value == null){

                    }else{
                        $group_data[$key] = $value;
                    }
                }
                $old_id = $group['id'];
                unset($group_data['id']);
                $group_data['owner'] = $new_group_owner;
                $group_data['created_by'] = $new_group_owner;
                $group_id = $this->insert_secure_data('investment_groups',$group_data);
                if($group_id){
                    $created_tables['investment_groups'] = array($group_id);
                    $ids_tracker['group_id'][$old_id] = $group_id;
                    echo 'Group created. ID '.$group_id." \n";
                    unset($group_data->investment_groups);
                    foreach ($members as $member) {
                        $member_data = array();
                        foreach ($member as $key => $value) {
                            if($value == null){

                            }else{
                                $member_data[$key] = $value;
                            }
                        }
                        $member = (array)$member;
                        $old_user_id = $member['user_id'];
                        echo 'old user ID'.$old_user_id." \n";

                        unset($member_data['id']);
                        $member_data['user_id'] = $ids_tracker['user_id'][$old_user_id];
                        $member_data['group_id'] = $group_id;
                        $member_data['created_by'] = $new_group_owner;
                        $member_data['created_on'] = time();
                        $member_id = $this->insert_secure_data('members',$member_data);
                        // $this->select_all_secure('members');
                        // $this->db->where("id = ".$member_id,NULL,FALSE);
                        // print_r($this->db->get('members')->row());
                    }
                }
            }
        }
        
    }

    function fix_ids($created_tables = array(), $ids_tracker = array()){
        print_r($created_tables);
        print_r($ids_tracker);
        die;
        foreach ($ids_tracker as $key => $value) {
            foreach ($value as $old_id => $new_id) {
                foreach ($created_tables as $table => $ids) {
                    // print_r($created_tables); die;
                    foreach ($ids as $id) {
                        $this->select_all_secure($table);
                        $this->db->where('id = "'.$id.'"',NULL,FALSE);
                        $row = $this->db->get($table)->row();
                        if(isset($row->$key)){
                            $update = array(
                                $key => $new_id,
                                'modified_on' => time(),
                            );
                            if($this->update_secure_data($id,$table,$update)){
                                echo 'updated '.$table." \n";
                            }else{
                                echo 'Could not update '.$table." \n";
                            }
                        }
                    }
                }
            }
        }
    }

    function restore_group($group_id=0,$file_path = ''){
        // $ignore_tables = array("activity_log","email_templates","sms_templates","bank_menus","admin_menus","admin_quick_action_menus","ara_sessions","equity_bank_transaction_alerts","ipns","t24_transaction_alerts","contribution_invoicing_queue","contribution_fine_invoicing_queue","loan_invoicing_queue","email_queue","sms_result","user_bank_pairings","transaction_alert_forwarders","login_attempts");
        // $table_name = array();
        // $tables = $this->db->list_tables();
        // foreach ($tables as $table) {
        //     if(in_array($table, $ignore_tables)){

        //     }else{
        //         $table_name[$table] = $this->db->list_fields($table);
        //     }
            
        // }
        // $success=0;
        // $fails = 0;
        // $updated_tables = array();
        // $table_names = array();
        // $group_data = array();
        // foreach ($table_name as $key => $value) {
        //     $updated_tables[$key] = $key;
        //     if(in_array('group_id', $value)){
        //         $table_names[$key] = $value;
        //         $data = $this->get_data_by_group_id($group_id,$key);
        //         if($data){
        //             $group_data[$key] = $data;
        //             if($key == 'members'){
        //                 foreach ($data as $data_key => $data_value) {
        //                     $user_data[] =  $this->get_users($data_value->user_id);
        //                 }
        //                 $group_data['users'] = $user_data;
        //             }
        //         }
        //     }
        // }
        // $group = $this->get_group($group_id);
        // $group_data['investment_groups'] = $group;
        // $file = "./logs/group_demo_data.txt";
        // file_put_contents($file,json_encode($group_data));
        // die;
        if($file_path){
            $file = $file_path;
        }else{
            $this->reset_group_data($group_id);
            $file = "./logs/group_demo_data.txt";
        }
        set_time_limit(0);
        ini_set('memory_limit','2048M');
        ini_set('max_execution_time', 2400);
        $directory = '';
        $zip_file = '';
        if(preg_match('/\.zip/', $file)){
            $zip_file = $file;
            if(!is_file($file)){
                $this->session->set_flashdata('error','Error occured restoring group data');
                return FALSE;
            }
            $directory = unzip_file($file);
            $files = (scandir($directory));
            foreach ($files as $file) {
                if($file){
                    $file = $file;
                }
            }
            $file = $directory.'/'.$file;
        }
        $contents = file_get_contents($file);
        $group_data = json_decode($contents);
        $users = isset($group_data->users)?$group_data->users:array();
        if($users){
            foreach ($users as $user_array) {
                $this->create_user($user_array);
            }
            unset($group_data->users);
        }
        $group = isset($group_data->investment_groups)?$group_data->investment_groups:array();
        if($group){
            $group_id = $this->create_group($group);
            unset($group_data->investment_groups);
        }
        $this->create_group_data($group_data,$group_id);  
        if($file_path){
            unlink($file);
            if($directory){
                rmdir($directory);
            }
            unlink($zip_file);
            return $group_id;
        }
    }

    function reset_group_data($group_id=0){
        set_time_limit(0);
        $result = TRUE;
        $members= $this->members_m->get_group_members_by_id_for_admin($group_id);
        foreach ($members as $member) {
            if($member->active){
            }else{
                
            }
            if($this->members_m->delete($member->id)){
                $member_group_count = $this->groups_m->count_current_user_groups($member->user_id) + 1;
                if($member_group_count==1){
                    if($this->users_m->delete($member->user_id)){

                    }else{
                        $result = FALSE;
                    }
                }
            }else{
                $result = FALSE;
            }
        }
        if(preg_match('/(sandbox\.co)/',$_SERVER['HTTP_HOST'])){
            $database = 'eazzychama_uganda';
        }else if(preg_match('/(eazzyclub\.co)/',$_SERVER['HTTP_HOST'])){
            $database = 'eazzyclub';
        }else if(preg_match('/173\.255\.205\.7/',$_SERVER['SERVER_ADDR'])||preg_match('/45\.56\.79\.118/',$_SERVER['SERVER_ADDR'])){
            $database = 'eazzychama';
        }else{
            $database = 'chamasoft';
        }
        $tables=$this->db->query("SELECT t.TABLE_NAME AS table_name FROM INFORMATION_SCHEMA.TABLES AS t WHERE t.TABLE_SCHEMA = '".$database."' ")->result_array();    
        $count = 1;
        $ignore_tables = array('equity_bank_transaction_alerts','transaction_alerts','investment_groups','members');
        foreach($tables as $key => $val) {
            $table_name = $val['table_name'];
            if($this->db->field_exists('group_id',$table_name)){
                if(in_array($table_name,$ignore_tables)){
                    if($table_name=='transaction_alerts'){
                        $this->migrate_m->unset_group_transaction_alerts($group_id);
                    }
                }else{
                    if($this->migrate_m->delete_group_data($group_id,$table_name)){
                        //do nothing for now
                    }else{
                        $result = FALSE;
                    }
                }
            }
            if($result){
                $this->session->set_flashdata('success','All went well during the deletion of the group and group data');
            }else{
                $this->session->set_flashdata('warning','Something went wrong during the deletion of the group and group data');
            }
        }
        // echo 'Group reset <br/>'; 
    }

    function backup_group($group_id = 0,$delete = 0,$reset=0,$user_id=0){
        $ignore_tables = array("activity_log","email_templates","sms_templates","bank_menus","admin_menus","admin_quick_action_menus","ara_sessions","equity_bank_transaction_alerts","ipns","t24_transaction_alerts","contribution_invoicing_queue","contribution_fine_invoicing_queue","loan_invoicing_queue","email_queue","sms_result","user_bank_pairings","transaction_alert_forwarders","login_attempts","emails","voided_statements","group_deletions","transaction_alerts_backup","statements","transaction_alerts_old");
        $table_name = array();
        $tables = $this->db->list_tables();
        foreach ($tables as $table) {
            if(in_array($table, $ignore_tables)){

            }else{
                $table_name[$table] = $this->db->list_fields($table);
            }
            
        }
        $success=0;
        $fails = 0;
        $updated_tables = array();
        $table_names = array();
        $group_data = array();
        $encoded_data = '';

        $group_account_number = $this->bank_accounts_m->get_group_verified_bank_account_number_list($group_id);
        foreach ($table_name as $key => $value) {
            $updated_tables[$key] = $key;
            if(in_array('group_id', $value)){
                $table_names[$key] = $value;
                $data = $this->get_data_by_group_id($group_id,$key);
                if($data){
                    if($data_coded = json_encode($data)){
                        if($encoded_data){
                            $encoded_data =$encoded_data.",".'"'.$key.'":'.$data_coded;
                        }else{
                            $encoded_data ='"'.$key.'":'.$data_coded;
                        }

                        if($key == 'members'){
                            $user_data = array();
                            foreach ($data as $data_key => $data_value) {
                                $user_data[] =  $this->get_users($data_value->user_id);
                            }
                            $encoded_data =$encoded_data.",".'"users":'.json_encode($user_data);
                        }
                    }
                }
            }
        }

        $statement_count = $this->count_data_by_group_id($group_id,'statements');
        $per_page = 40000;
        if($statement_count > $per_page){
            $posts = $this->_loop_statements($group_id,$statement_count,$per_page);
            $encoded_data =$encoded_data.",".'"statements":'.json_encode($posts);
        }else{
            if($statement_data = $this->get_data_by_group_id($group_id,'statements')){
                $encoded_data =$encoded_data.",".'"statements":'.json_encode($statement_data);
            }
        }

        if($group_account_number){
            $transaction_alerts = $this->transaction_alerts_m->get_group_account_all_transaction_alerts($group_account_number);
            $encoded_data =$encoded_data.",".'"transaction_alerts":'.json_encode($transaction_alerts);
        }

        $group = $this->get_group($group_id);
        $group_data['investment_groups'] = $group;
        if(!is_dir('./logs/deleted_groups')){
            mkdir('./logs/deleted_groups',0777,TRUE);
        }else{
            
        }

        if(!is_dir('./logs/reset_groups')){
            mkdir('./logs/reset_groups',0777,TRUE);
        }else{
        
        }
        $file = "";
        $result ="{".$encoded_data.",".'"investment_groups":'.json_encode($group)."}";
        if($group):
            if($delete){
                $file_name = $group->name.'_'.$group_id.'_'.time().".txt";
                $path = "./logs/deleted_groups/";
                $file = $path.$file_name;
            }elseif ($reset) {
                $file_name = $group->name.'_'.$group_id.'_'.time().".txt";
                $path = "./logs/reset_groups/";
                $file = $path.$file_name;
            }else{
                $file_name = $group->name.'_'.$group_id.'_'.time().".txt";
                $path = "./logs/reset_groups/";
                $file = $path.$file_name;
                
            }
            file_put_contents($file,$result);
            $files = array(
                $file_name => $file
            );
            $file = create_zip($files,$file,1);
        endif;
        return $file;
    }

    function _loop_statements($group_id=0,$statement_count=0,$per_page=20){
        $posts = array();
        $lower_limit = 0;
        for($i=0;$i<$statement_count;($i+=$per_page)){
            $pagination = create_custom_pagination('group',$statement_count,$per_page,$i,TRUE);
            $posts=array_merge($posts,$this->migrate_m->limit($pagination['limit'])->get_data_by_group_id($group_id,'statements'));
            if($i>=400000){
                //break;
            }
        }
        return $posts;
    }

    function get_users($user_id){
        return $this->ion_auth->get_user($user_id);
    }

    function get_group($group_id = 0)
{        $this->select_all_secure('investment_groups');
        $this->db->where('id',$group_id);
        return $this->db->get('investment_groups')->row();
    }

    function get_data_by_group_id($group_id=0,$table_name=''){
        $this->select_all_secure($table_name);
        $this->db->where($this->dx($table_name.'.group_id').' = "'.$group_id.'"',NULL,FALSE);
        // if($this->db->field_exists('active',$table_name)){
        //     $this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        // }
        return $this->db->get($table_name)->result();
    }

    function count_data_by_group_id($group_id=0,$table_name=''){
        $this->db->where($this->dx($table_name.'.group_id').' = "'.$group_id.'"',NULL,FALSE);
        if($this->db->field_exists('active',$table_name)){
            $this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        }
        return $this->db->count_all_results($table_name);
    }

    function create_user($user=array()){
        if($user){
            $id = $user->id;
            $this->db->where('id',$id)->delete('users');
            $data = array(
                'id' => $id,
            );
            $this->db->insert('users', $data);
            $user = (array)$user;
            unset($user['id']);
            unset($user['user_id']);
            $update = array();
            foreach ($user as $key => $value) {
                if($value==null){

                }else{
                    $update[$key] = trim($value);
                }
            }
            if($this->update_secure_data($id,'users',$update)){
                return TRUE;
                //echo 'created user <br/>';
            }
        }else{
            return TRUE;
        }
        
    }

    function create_group($group=array()){
        $id = 0;
        if($group){
            $id = $group->id;
            $this->db->where('id',$id)->delete('investment_groups');
            $data = array(
                'id' => $id,
            );
            $this->db->insert('investment_groups', $data);
            $group = (array)$group;
            unset($group['id']);
            $update = array();
            foreach ($group as $key => $value) {
                if($value==null){

                }else{
                    $update[$key] = trim($value);
                }
            }
            if($this->update_secure_data($id,'investment_groups',$update)){
                //print_r('created Group <br/>');
            }
        }
        return $id;
    }

    function create_group_data($group_data = array(),$group_id=0){
        if($group_data){
            $table_name = array();
            $tables = $this->db->list_tables();
            foreach ($tables as $table) {
                $table_name[$table] = $this->db->list_fields($table);
            }
            $success=0;
            $fails = 0;
            $ignore_tables = array(
                // 'voided_statements',
                // "notifications" ,
                // 'contribution_member_pairings',
                // 'deposits',
                // 'invoices',
                // 'statements',
                // 'loan_statements'
            );
            foreach ($table_name as $key => $value) {
                if(isset($group_data->$key)){
                    $data = $group_data->$key;
                    if($key == 'voided_statements' || $key == "notifications" || $key =="users_groups"){
                        continue;
                    }

                    // if($key=="deposits"){
                    //     print_r('<pre>');
                    //     print_r($data);
                    //     print_r('</pre>');die;
                    // }
                    $this->restore_table($data,$key,$group_id);
                }else{

                }
            }
            //print_r('done');
        }
    }
    

    function restore_table($data = array(),$table_name='',$group_id=0){
        if($data){
            $values = array();
            $upperLimit = 300;
            if($table_name=="transaction_alerts"){
                $alerts = array();
                foreach ($data as $key => $value) {
                    $id = isset($value->id)?$value->id:0;
                    $this->db->where('id',$id)->where("group_id",$group_id)->delete($table_name);
                    foreach ($value as $_key => $_value) {
                        $alerts[$key][$_key] = $_value;
                    }
                }
                if($alerts){
                    $this->transaction_alerts_m->insert_batch_transaction_alerts($alerts);
                }
            }else{
                if(count($data) > $upperLimit){
                    for ($i=0; $i < count($data); $i+=$upperLimit) { 
                        $values = array();
                        foreach ($data as $key => $value) {
                            if($key>=$i && $key < ($i+$upperLimit)){
                                $id = isset($value->id)?$value->id:0;
                                if($id != 0){
                                    $this->db->where('id',$id)
                                        ->where("group_id",$group_id)
                                        ->delete($table_name);
                                }
                                foreach ($value as $_key => $_value) {
                                     if($_value==null){
                                        $values[$_key][$key] = NULL;
                                    }else{
                                        $values[$_key][$key] = $_value;
                                    }
                                }
                            }else{
                                continue;
                            } 
                        }
                        if($values){
                            if($table_name=="transaction_alerts"){
                                //print_r($values);die;
                            }else{
                                $this->insert_batch_data($table_name,$values);
                            }
                        }
                    }
                }else{
                    foreach ($data as $key => $value) {
                        $id = isset($value->id)?$value->id:0;
                        if($id != 0){
                            $this->db->where('id',$id)
                                ->where("group_id",$group_id)
                                ->delete($table_name);
                        }
                        foreach ($value as $_key => $_value) {
                             if($_value==null){
                                $values[$_key][$key] = NULL;
                            }else{
                                $values[$_key][$key] = $_value;
                            }
                        }
                    }
                    if($table_name=="transaction_alerts"){
                        //print_r($values);die;
                    }else{
                        $this->insert_batch_data($table_name,$values);
                    }
                }
            }
        }
    }

    function get_entry($id=0,$table_name=0){
        $this->select_all_secure($table_name);
        $this->where('id',$id);
        print_r('<pre>');
        print_r($this->db->get($table_name)->row());
        print_r('</pre>');
    }

    function get_bank_accounts(){
        $id = 917;
        $this->select_all_secure('bank_accounts');
        $this->db->select('id');
        $this->db->where('id',$id);
        print_r($this->db->get('bank_accounts')->row());
    }

    function manually_update($column_name='',$table_name=''){
        $array = array(
            $column_name => NULL,
        );

        $this->db->set($array);
        $this->db->insert($table_name);
    }

    function get_all_group_data($table_name='',$group_id=0){
        $this->select_all_secure($table_name);
        $this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
        return $this->db->get($table_name)->result();
    }

    function update_batch_data($table_name='',$data=array()){
        $data = json_decode(json_encode($data),TRUE);
        return $this->batch_update_secure_data($table_name,$data);
    }

    function insert_batch_data($table_name='',$input,$skip_validation=FALSE){
        return $this->insert_batch_secure_data($table_name,$input);
    }
    

    function import_group_to_uat($group_data = '',$group_id = ''){
        if(preg_match('/app\.chamasoft\.com/', $_SERVER['HTTP_HOST'])||preg_match('/eazzychama\.co\.ke/', $_SERVER['HTTP_HOST'])||preg_match('/eazzyclub\.co\.ug/', $_SERVER['HTTP_HOST'])){
            //Kindly do not remove this. Kindly, kindly
            die("You cannot do this activity on this application.");
        }
        if($group_data){
            $this->reset_group_data($group_id);
        }else{
            return FALSE;
        }


        set_time_limit(0);
        ini_set('memory_limit','2048M');
        ini_set('max_execution_time', 2400);
        // $contents = file_get_contents($file);
        // $group_data = json_decode($contents);
        $users = isset($group_data->users)?$group_data->users:array();
        if($users){
            foreach ($users as $user_array) {
                $this->create_user($user_array);
            }
            unset($group_data->users);
        }
        $group = isset($group_data->investment_groups)?$group_data->investment_groups:array();
        if($group){
            $group_id = $this->create_group($group);
            unset($group_data->investment_groups);
        }
        $this->create_group_data($group_data,$group_id);
        return $group_id;
    }
}