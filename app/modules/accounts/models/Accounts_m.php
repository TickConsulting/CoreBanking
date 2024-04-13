<?php if(!defined('BASEPATH'))  exit('You are not allowed to view this script');
class Accounts_m extends My_Model{

    public function __construct(){
        parent::__construct();
        $this->install();
        $this->load->model('bank_accounts/bank_accounts_m');
        $this->load->model('sacco_accounts/sacco_accounts_m');
        $this->load->model('mobile_money_accounts/mobile_money_accounts_m');
        $this->load->model('petty_cash_accounts/petty_cash_accounts_m');

    }

    public function install()
    {
        $this->db->query("
        create table if not exists account_transfers(
            id int not null auto_increment primary key,
            `to_account_id` blob,
            `group_id` blob,
            `from_account_id` blob,
            `transfer_date` blob,
            amount blob,
            description blob,
            `active` blob,
            created_by blob,
            created_on blob,
            modified_on blob,
            modified_by blob
        )");
    }

    /****
    Account type
        1. Bank Account
        2. Sacco Account
        3. Mobile Money Account
        4. Petty Cash Account
    *****/

    function insert_account_transfer($input = array(),$skip_value = FALSE){
        return $this->insert_secure_data('account_transfers',$input);
    }

    
    function update_account_transfer($id,$input=array(),$SKIP_VALIDATION=FALSE){
        return $this->update_secure_data($id,'account_transfers',$input);
    }

    function get_group_account_options($return_option_groups = TRUE,$show_account_balances = FALSE,$group_id=0){
    	$this->select_all_secure('bank_accounts');
        $this->db->select(array($this->dx('banks.name').'as bank_name',$this->dx('bank_branches.name').' as bank_branch_name'));
        
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        $this->db->join('bank_branches',$this->dx('bank_accounts.bank_branch_id').'= bank_branches.id','INNER');
    	$bank_accounts = $this->db->get('bank_accounts')->result();

    	$bank = array();
    	$mobile = array();
    	$petty=array();
    	$sacco = array();

    	foreach ($bank_accounts as $key => $value) {
            if($show_account_balances){
                  $bank['bank-'.$value->id] = $value->bank_name.' ('.$value->bank_branch_name.') - '.$value->account_name.' ('.$value->account_number.') - '.$this->group_currency.' '.number_to_currency($value->initial_balance+$value->current_balance);
            }else{
    		      $bank['bank-'.$value->id] = $value->bank_name.' ('.$value->bank_branch_name.') - '.$value->account_name.' ('.$value->account_number.')';
    	    }
        }

    	$this->select_all_secure('sacco_accounts');
        $this->db->select(array($this->dx('saccos.name').'as sacco_name',$this->dx('sacco_branches.name').' as sacco_branch_name'));
    	
        
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->join('saccos',$this->dx('sacco_accounts.sacco_id').'= saccos.id','INNER');
        $this->db->join('sacco_branches',$this->dx('sacco_accounts.sacco_branch_id').'= sacco_branches.id','INNER');
    	$sacco_accounts = $this->db->get('sacco_accounts')->result();

    	foreach ($sacco_accounts as $key => $value) {
            if($show_account_balances){
                  $sacco['sacco-'.$value->id] = $value->sacco_name.' ('.$value->sacco_branch_name.') - '.$value->account_name.' ('.$value->account_number.') - '.$this->group_currency.' '.number_to_currency($value->initial_balance+$value->current_balance);
    		}else{
                  $sacco['sacco-'.$value->id] = $value->sacco_name.' ('.$value->sacco_branch_name.') - '.$value->account_name.' ('.$value->account_number.')';
    	    }
        }

    	$this->select_all_secure('mobile_money_accounts');
         $this->db->select(array($this->dx('mobile_money_providers.name').'as mobile_money_provider_name'));
        
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->join('mobile_money_providers',$this->dx('mobile_money_accounts.mobile_money_provider_id').'= mobile_money_providers.id','INNER');
    	$mobile_money_accounts = $this->db->get('mobile_money_accounts')->result();

    	foreach ($mobile_money_accounts as $key => $value) {
            if($show_account_balances){
              $mobile['mobile-'.$value->id] =$value->mobile_money_provider_name.' - '.$value->account_name.' ('.$value->account_number.') - '.$this->group_currency.' '.number_to_currency($value->initial_balance+$value->current_balance);
    		}else{  
              $mobile['mobile-'.$value->id] =$value->mobile_money_provider_name.' - '.$value->account_name.' ('.$value->account_number.')';
    	   }
        }


    	$this->select_all_secure('petty_cash_accounts');
         
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);
    	$petty_cash_accounts = $this->db->get('petty_cash_accounts')->result();

    	foreach ($petty_cash_accounts as $key => $value) {
            if($show_account_balances){
    		  $petty['petty-'.$value->id] = $value->account_name.' - '.$this->group_currency.' '.number_to_currency($value->initial_balance+$value->current_balance);
            }else{
              $petty['petty-'.$value->id] = $value->account_name;
            }
    	}
        if($return_option_groups){
            return array(
                'Bank Accounts' => $bank,
                'Sacco Accounts'    =>  $sacco,
                'Mobile Money Accounts' => $mobile,
                'Petty Cash Accounts' => $petty,
            );
        }else{
            return $bank + $sacco + $mobile + $petty;
        }
    }

    function get_group_cash_at_hand_account_options($group_id=0){
        $this->select_all_secure('mobile_money_accounts');
         $this->db->select(array($this->dx('mobile_money_providers.name').'as mobile_money_provider_name'));
        if($group_id){
            $this->db->where($this->dx('mobile_money_accounts.group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('mobile_money_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->join('mobile_money_providers',$this->dx('mobile_money_accounts.mobile_money_provider_id').'= mobile_money_providers.id','INNER');
        $mobile_money_accounts = $this->db->get('mobile_money_accounts')->result();
        $this->select_all_secure('petty_cash_accounts');
        if($group_id){
            $this->db->where($this->dx('petty_cash_accounts.group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('petty_cash_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $petty_cash_accounts = $this->db->get('petty_cash_accounts')->result();
        $arr = array();
        foreach ($mobile_money_accounts as $mobile_money_account) {
             $arr['mobile-'.$mobile_money_account->id] = $mobile_money_account->account_name;
        }
        foreach ($petty_cash_accounts as $petty_cash_account) {
             $arr['petty-'.$petty_cash_account->id] = $petty_cash_account->account_name;
        }
        return $arr;
    }

    function get_group_cash_at_bank_account_options($group_id=0){
        $this->select_all_secure('bank_accounts');
        $this->db->select(array($this->dx('banks.name').'as bank_name',$this->dx('bank_branches.name').' as bank_branch_name'));
        if($group_id){
            $this->db->where($this->dx('bank_accounts.group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('bank_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        $this->db->join('bank_branches',$this->dx('bank_accounts.bank_branch_id').'= bank_branches.id','INNER');
        $bank_accounts = $this->db->get('bank_accounts')->result();
        $this->select_all_secure('sacco_accounts');
        $this->db->select(array($this->dx('saccos.name').'as sacco_name',$this->dx('sacco_branches.name').' as sacco_branch_name'));
        
        if($group_id){
            $this->db->where($this->dx('sacco_accounts.group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('sacco_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->join('saccos',$this->dx('sacco_accounts.sacco_id').'= saccos.id','INNER');
        $this->db->join('sacco_branches',$this->dx('sacco_accounts.sacco_branch_id').'= sacco_branches.id','INNER');
        $sacco_accounts = $this->db->get('sacco_accounts')->result();

        $arr = array();
        foreach ($bank_accounts as $bank_account) {
             $arr['bank-'.$bank_account->id] = $bank_account->account_name;
        }
        foreach ($sacco_accounts as $sacco_account) {
             $arr['sacco-'.$sacco_account->id] = $sacco_account->account_name;
        }
        return $arr;
    }

    function get_group_account_ids_account_number_as_keys_options($group_id = 0){
        $arr = array();
        $this->db->select(
            array(
                'id',
                $this->dx('account_number').' as account_number '
            )
        );
        if($group_id){
            $this->db->where($this->dx('bank_accounts.group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('bank_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $bank_accounts = $this->db->get('bank_accounts')->result();

        foreach ($bank_accounts as $key => $value) {
            $arr[$value->account_number] = 'bank-'.$value->id;
        }

        $this->db->select(
            array(
                'id',
                $this->dx('account_number').' as account_number '
            )
        );
        if($group_id){
            $this->db->where($this->dx('sacco_accounts.group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('sacco_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }

        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $sacco_accounts = $this->db->get('sacco_accounts')->result();

        foreach ($sacco_accounts as $key => $value) {
            $arr[$value->account_number] = 'sacco-'.$value->id;
        }

        $this->db->select(
            array(
                'id',
                $this->dx('account_number').' as account_number '
            )
        );
        if($group_id){
            $this->db->where($this->dx('mobile_money_accounts.group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('mobile_money_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $mobile_money_accounts = $this->db->get('mobile_money_accounts')->result();

        foreach ($mobile_money_accounts as $key => $value) {
            $arr[$value->account_number] = 'mobile-'.$value->id;
        }
        return $arr;

    }

    function get_active_group_account_options($return_option_groups = TRUE,$show_account_balances = FALSE,$include_back_dating_suspense_account = FALSE,$group_id = 0,$hide_verified_accounts = TRUE){
        $this->select_all_secure('bank_accounts');
        $this->db->select(array($this->dx('banks.name').'as bank_name',$this->dx('bank_branches.name').' as bank_branch_name'));
        
        $this->db->where($this->dx('bank_accounts.active').'="1"',NULL,FALSE);
        if($hide_verified_accounts){
            $this->db->where($this->dx('bank_accounts.is_verified').'IS NULL',NULL,FALSE);
        }
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        $this->db->join('bank_branches',$this->dx('bank_accounts.bank_branch_id').'= bank_branches.id','INNER');
        $bank_accounts = $this->db->get('bank_accounts')->result();
        $bank = array();
        $mobile = array();
        $petty=array();
        $sacco = array();

        foreach ($bank_accounts as $key => $value) {
            if($show_account_balances){
                    $balance = (float)$value->initial_balance + 
                    (float)$value->current_balance;
                  $bank['bank-'.$value->id] = $value->bank_name.'
                   ('.$value->bank_branch_name.') - '.$value->account_name.' 
                   ('.$value->account_number.') - '.$this->group_currency.' 
                   '.number_to_currency($balance);
            }else{
                  $bank['bank-'.$value->id] = $value->bank_name.' ('.$value->bank_branch_name.') - '.$value->account_name.' ('.$value->account_number.')';
            }
        }

        $this->select_all_secure('sacco_accounts');
        $this->db->select(array($this->dx('saccos.name').'as sacco_name',$this->dx('sacco_branches.name').' as sacco_branch_name'));
       
        $this->db->where($this->dx('sacco_accounts.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->join('saccos',$this->dx('sacco_accounts.sacco_id').'= saccos.id','INNER');
        $this->db->join('sacco_branches',$this->dx('sacco_accounts.sacco_branch_id').'= sacco_branches.id','INNER');
        $sacco_accounts = $this->db->get('sacco_accounts')->result();

        foreach ($sacco_accounts as $key => $value) {
            $initial_balance = $value->initial_balance?$value->initial_balance:0;
            $current_balance = $value->current_balance?$value->current_balance:0;
            if($show_account_balances){
                  $sacco['sacco-'.$value->id] = $value->sacco_name.' ('.$value->sacco_branch_name.') - '.$value->account_name.' ('.$value->account_number.') - '.$this->group_currency.' '.number_to_currency($initial_balance + $current_balance);
            }else{
                  $sacco['sacco-'.$value->id] = $value->sacco_name.' ('.$value->sacco_branch_name.') - '.$value->account_name.' ('.$value->account_number.')';
            }
        }

        $this->select_all_secure('mobile_money_accounts');
        $this->db->select(array($this->dx('mobile_money_providers.name').'as mobile_money_provider_name'));
         
        $this->db->where($this->dx('mobile_money_accounts.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->join('mobile_money_providers',$this->dx('mobile_money_accounts.mobile_money_provider_id').'= mobile_money_providers.id','INNER');
        $mobile_money_accounts = $this->db->get('mobile_money_accounts')->result();

        foreach ($mobile_money_accounts as $key => $value) {
            $initial_balance = $value->initial_balance?$value->initial_balance:0;
            $current_balance = $value->current_balance?$value->current_balance:0;
            if($show_account_balances){
              $mobile['mobile-'.$value->id] =$value->mobile_money_provider_name.' - '.$value->account_name.' ('.$value->account_number.') - '.$this->group_currency.' '.number_to_currency($initial_balance+$current_balance);
            }else{  
              $mobile['mobile-'.$value->id] =$value->mobile_money_provider_name.' - '.$value->account_name.' ('.$value->account_number.')';
           }
        }


        $this->select_all_secure('petty_cash_accounts');
       
        if($include_back_dating_suspense_account){

        }else{
            $this->db->where($this->dx('petty_cash_accounts.active').'="1"',NULL,FALSE);
        }
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);

        $petty_cash_accounts = $this->db->get('petty_cash_accounts')->result();

        foreach ($petty_cash_accounts as $key => $value) {
            if($show_account_balances){
              $petty['petty-'.$value->id] = $value->account_name.' - '.$this->group_currency.' '.number_to_currency($value->initial_balance+$value->current_balance);
            }else{
              $petty['petty-'.$value->id] = $value->account_name;
            }
        }
        if($return_option_groups){
            return array(
                'Bank Accounts' => $bank,
                'Sacco Accounts' => $sacco,
                'Mobile Money Accounts' => $mobile,
                'Petty Cash Accounts' => $petty,
            );
        }else{
            return $bank + $sacco + $mobile + $petty;
        }
    }

    function get_group_unverified_account_options($return_option_groups = TRUE,$show_account_balances = FALSE){
        $this->select_all_secure('bank_accounts');
        $this->db->select(array($this->dx('banks.name').'as bank_name',$this->dx('bank_branches.name').' as bank_branch_name'));
        // $this->db->where($this->dx('bank_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        $this->db->join('bank_branches',$this->dx('bank_accounts.bank_branch_id').'= bank_branches.id','INNER');
        $bank_accounts = $this->db->get('bank_accounts')->result();

        $bank = array();
        $mobile = array();
        $petty=array();
        $sacco = array();

        foreach ($bank_accounts as $key => $value) {
            if($value->is_verified){

            }else{
                if($show_account_balances){
                      $bank['bank-'.$value->id] = $value->bank_name.' ('.$value->bank_branch_name.') - '.$value->account_name.' ('.$value->account_number.') - '.$this->group_currency.' '.number_to_currency($value->initial_balance+$value->current_balance);
                }else{
                      $bank['bank-'.$value->id] = $value->bank_name.' ('.$value->bank_branch_name.') - '.$value->account_name.' ('.$value->account_number.')';
                }
            }
        }

        $this->select_all_secure('sacco_accounts');
        $this->db->select(array($this->dx('saccos.name').'as sacco_name',$this->dx('sacco_branches.name').' as sacco_branch_name'));
        // $this->db->where($this->dx('sacco_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);

        $this->db->join('saccos',$this->dx('sacco_accounts.sacco_id').'= saccos.id','INNER');
        $this->db->join('sacco_branches',$this->dx('sacco_accounts.sacco_branch_id').'= sacco_branches.id','INNER');
        $sacco_accounts = $this->db->get('sacco_accounts')->result();

        foreach ($sacco_accounts as $key => $value) {
            if($show_account_balances){
                  $sacco['sacco-'.$value->id] = $value->sacco_name.' ('.$value->sacco_branch_name.') - '.$value->account_name.' ('.$value->account_number.') - '.$this->group_currency.' '.number_to_currency($value->initial_balance+$value->current_balance);
            }else{
                  $sacco['sacco-'.$value->id] = $value->sacco_name.' ('.$value->sacco_branch_name.') - '.$value->account_name.' ('.$value->account_number.')';
            }
        }

        $this->select_all_secure('mobile_money_accounts');
        $this->db->select(array($this->dx('mobile_money_providers.name').'as mobile_money_provider_name'));
        // $this->db->where($this->dx('mobile_money_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);

        $this->db->join('mobile_money_providers',$this->dx('mobile_money_accounts.mobile_money_provider_id').'= mobile_money_providers.id','INNER');
        $mobile_money_accounts = $this->db->get('mobile_money_accounts')->result();

        foreach ($mobile_money_accounts as $key => $value) {
            if($show_account_balances){
              $mobile['mobile-'.$value->id] =$value->mobile_money_provider_name.' - '.$value->account_name.' ('.$value->account_number.') - '.$this->group_currency.' '.number_to_currency($value->initial_balance+$value->current_balance);
            }else{  
              $mobile['mobile-'.$value->id] =$value->mobile_money_provider_name.' - '.$value->account_name.' ('.$value->account_number.')';
           }
        }


        $this->select_all_secure('petty_cash_accounts');
        // $this->db->where($this->dx('petty_cash_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $petty_cash_accounts = $this->db->get('petty_cash_accounts')->result();

        foreach ($petty_cash_accounts as $key => $value) {
            if($show_account_balances){
              $petty['petty-'.$value->id] = $value->account_name.' - '.$this->group_currency.' '.number_to_currency($value->initial_balance+$value->current_balance);
            }else{
              $petty['petty-'.$value->id] = $value->account_name;
            }
        }
        if($return_option_groups){
            return array(
                'Bank Accounts' => $bank,
                'Sacco Accounts'    =>  $sacco,
                'Mobile Money Accounts' => $mobile,
                'Petty Cash Accounts' => $petty,
            );
        }else{
            return $bank + $sacco + $mobile + $petty;
        }
    }

    function get_group_account_balances_array($group_id=0){
        $this->db->select(
            array(
                'id',
                $this->dx('initial_balance').' as initial_balance',
                $this->dx('current_balance').' as current_balance '
            )
        );
        // if($group_id){
        //     $this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
        // }
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $bank_accounts = $this->db->get('bank_accounts')->result();

        $bank = array();
        $mobile = array();
        $petty=array();
        $sacco = array();
        foreach ($bank_accounts as $key => $value) {
            $bank['bank-'.$value->id] = ($value->initial_balance+$value->current_balance);
        }

        $this->db->select(
            array(
                'id',
                $this->dx('initial_balance').' as initial_balance',
                $this->dx('current_balance').' as current_balance '
            )
        );
        if($group_id){
            $this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $sacco_accounts = $this->db->get('sacco_accounts')->result();

        foreach ($sacco_accounts as $key => $value) {
            $sacco['sacco-'.$value->id] = ($value->initial_balance+$value->current_balance);
        }

        $this->db->select(
            array(
                'id',
                $this->dx('initial_balance').' as initial_balance',
                $this->dx('current_balance').' as current_balance '
            )
        );
        if($group_id){
            $this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $mobile_money_accounts = $this->db->get('mobile_money_accounts')->result();

        foreach ($mobile_money_accounts as $key => $value) {
            $mobile['mobile-'.$value->id] =($value->initial_balance+$value->current_balance);
        }


        $this->db->select(
            array(
                'id',
                $this->dx('initial_balance').' as initial_balance',
                $this->dx('current_balance').' as current_balance '
            )
        );
        if($group_id){
            $this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);

        $petty_cash_accounts = $this->db->get('petty_cash_accounts')->result();

        foreach ($petty_cash_accounts as $key => $value) {
            $petty['petty-'.$value->id] = ($value->initial_balance+$value->current_balance);
        }
        return array(
            'Bank Accounts' => $bank,
            'Sacco Accounts'    =>  $sacco,
            'Mobile Money Accounts' => $mobile,
            'Petty Cash Accounts' => $petty,
        );
       
    }

    function get_group_account_balance($account_id = '',$group_id=0){
        if(preg_match('/bank-/', $account_id)){
            $account_id = str_replace('bank-','',$account_id);
            return $this->bank_accounts_m->get_group_bank_account_balance($account_id,$group_id);
        }else if(preg_match('/sacco-/', $account_id)){
            $account_id = str_replace('sacco-','',$account_id);
            return $this->sacco_accounts_m->get_group_sacco_account_balance($account_id,$group_id);
        }else if(preg_match('/mobile-/', $account_id)){
            $account_id = str_replace('mobile-','',$account_id);
            return $this->mobile_money_accounts_m->get_group_mobile_money_account_balance($account_id,$group_id);
        }else if(preg_match('/petty-/', $account_id)){
            $account_id = str_replace('petty-','',$account_id);
            return $this->petty_cash_accounts_m->get_group_petty_cash_account_balance($account_id,$group_id);
        }else{
            return FALSE;
        }
    }

    function get_group_account_number($account_id = '',$group_id=0){
        if(preg_match('/bank-/', $account_id)){
            $account_id = str_replace('bank-','',$account_id);
            return $this->bank_accounts_m->get_group_bank_account_number($account_id,$group_id);
        }else if(preg_match('/sacco-/', $account_id)){
            $account_id = str_replace('sacco-','',$account_id);
            return $this->sacco_accounts_m->get_group_sacco_account_number($account_id,$group_id);
        }else if(preg_match('/mobile-/', $account_id)){
            $account_id = str_replace('mobile-','',$account_id);
            return $this->mobile_money_accounts_m->get_group_mobile_money_account_number($account_id,$group_id);
        }else{
            return FALSE;
        }
    }

    function get_group_account($account_id = '',$group_id=0){
        if($group_id){

        }else{
            $group_id = $this->group->id;
        }
        if(preg_match('/bank-/', $account_id)){
            $account_id = str_replace('bank-','',$account_id);
            return $this->bank_accounts_m->get_group_bank_account($account_id,$group_id);
        }else if(preg_match('/sacco-/', $account_id)){
            $account_id = str_replace('sacco-','',$account_id);
            return $this->sacco_accounts_m->get_group_sacco_account($account_id,$group_id);
        }else if(preg_match('/mobile-/', $account_id)){
            $account_id = str_replace('mobile-','',$account_id);
            return $this->mobile_money_accounts_m->get_group_mobile_money_account($account_id,$group_id);
        }else if(preg_match('/petty-/', $account_id)){
            $account_id = str_replace('petty-','',$account_id);
            return $this->petty_cash_accounts_m->get_group_petty_cash_account($account_id,$group_id);
        }else{
            return FALSE;
        }
    }

    function check_if_group_account_exists($account_id='',$group_id=0){
        if($group_id){
            
        }else{
            if(isset($this->group)){
                $group_id = $this->group->id;
            }   
        }
        $res = FALSE;
        if(preg_match('/bank-/', $account_id)){
            $account_id = str_replace('bank-','',$account_id);
            $res = $this->bank_accounts_m->get_group_bank_account_number($account_id,$group_id);
        }else if(preg_match('/sacco-/', $account_id)){
            $account_id = str_replace('sacco-','',$account_id);
            $res= $this->sacco_accounts_m->get_group_sacco_account_number($account_id,$group_id);
        }else if(preg_match('/mobile-/', $account_id)){
            $account_id = str_replace('mobile-','',$account_id);
            $res= $this->mobile_money_accounts_m->get_group_mobile_money_account_number($account_id,$group_id);
        }else if(preg_match('/petty-/', $account_id)){
            $account_id = str_replace('petty-','',$account_id);
            $res= $this->petty_cash_accounts_m->check_if_group_account_exists($account_id,$group_id);
        }

        return $res;
    }

    function get_group_total_account_balance(){
        $total_bank_account_balance = $this->bank_accounts_m->get_group_total_bank_account_balance();
        $total_sacco_account_balance = $this->sacco_accounts_m->get_group_total_sacco_account_balance();
        $total_mobile_money_account_balance = $this->mobile_money_accounts_m->get_group_total_mobile_money_account_balance();
        $total_petty_cash_account_balance = $this->petty_cash_accounts_m->get_group_total_petty_cash_account_balance();
        return $total_bank_account_balance + $total_sacco_account_balance + $total_mobile_money_account_balance + $total_petty_cash_account_balance;
    }

    function get_group_total_cash_at_hand($group_id=0){
        $total_mobile_money_account_balance = $this->mobile_money_accounts_m->get_group_total_mobile_money_account_balance($group_id);
        $total_petty_cash_account_balance = $this->petty_cash_accounts_m->get_group_total_petty_cash_account_balance($group_id);
        return $total_mobile_money_account_balance + $total_petty_cash_account_balance;
    }

    function get_group_total_cash_at_bank($group_id = 0){
        $total_bank_account_balance = $this->bank_accounts_m->get_group_total_bank_account_balance($group_id = 0);
        $total_sacco_account_balance = $this->sacco_accounts_m->get_group_total_sacco_account_balance($group_id = 0);
        return $total_bank_account_balance + $total_sacco_account_balance;
    }

    function get_group_total_actual_bank_balance($group_id=0){
        return $this->bank_accounts_m->get_group_verified_total_bank_account_balance($group_id = 0);
    }

    function get_group_total_actual_cash_at_bank(){
        $total_bank_account_balance = $this->bank_accounts_m->get_group_total_actual_bank_account_balance($group_id = 0);
        return $total_bank_account_balance;
    }

    function get_total_group_accounts_starting_balances($group_id=0){
        /****1 Bank Account**/
        $bank_account_initial_balance = $this->bank_accounts_m->get_group_total_initial_bank_balance();
        $sacco_account_initial_balance = $this->sacco_accounts_m->get_group_total_initial_sacco_balance();
        $mobile_money_account_initial_balance = $this->mobile_money_accounts_m->get_group_total_initial_mobile_money_balance();
        $petty_cash_account_initial_balance = $this->petty_cash_accounts_m->get_group_total_initial_petty_cash_balance();

        $total_group_initial_balance = $bank_account_initial_balance+$sacco_account_initial_balance+$mobile_money_account_initial_balance+$petty_cash_account_initial_balance;

        return $total_group_initial_balance;
    }

    function get_individual_groups_account_balances_array($group_id=0){
        $banks = array();
        $saccos = array();
        $mobiles = array();
        $pettys = array();

        $this->db->select(
            array(
                'bank_accounts.id',
                $this->dx('initial_balance').' + '.$this->dx('current_balance').' as balance ',
                $this->dxa('account_name'),
                $this->dx('banks.name').' as bank_name',
            )
        );
        if($group_id){
            $this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.active').' ="1"',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').' = banks.id');
        $bank_accounts = $this->db->get('bank_accounts')->result();

        foreach ($bank_accounts as $key => $bank_account) {
            $banks[$bank_account->id] = array(
                'balance'   => $bank_account->balance,
                'account_name' => $bank_account->account_name,
                'bank_name' => $bank_account->bank_name,
            );
        }


        $this->db->select(
            array(
                'sacco_accounts.id',
                $this->dx('initial_balance').' + '.$this->dx('current_balance').' as balance ',
                $this->dxa('account_name'),
                $this->dx('saccos.name').' as sacco_name',
            )
        );
        if($group_id){
            $this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);

        $this->db->where($this->dx('sacco_accounts.active').' ="1"',NULL,FALSE);
        $this->db->join('saccos',$this->dx('sacco_accounts.sacco_id').' = saccos.id');
        $sacco_accounts = $this->db->get('sacco_accounts')->result();

        foreach ($sacco_accounts as $key => $sacco_account) {
            $saccos[$sacco_account->id] = array(
                'balance'   => $sacco_account->balance,
                'account_name' => $sacco_account->account_name,
                'sacco_account' => $sacco_account->sacco_name,
            );
        }


        $this->db->select(
            array(
                'mobile_money_accounts.id',
                $this->dx('initial_balance').' + '.$this->dx('current_balance').' as balance ',
                $this->dxa('account_name'),
                $this->dx('mobile_money_providers.name').' as mobile_money_provider_name',
            )
        );
        if($group_id){
            $this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);

        $this->db->where($this->dx('mobile_money_accounts.active').' ="1"',NULL,FALSE);
        $this->db->join('mobile_money_providers',$this->dx('mobile_money_accounts.mobile_money_provider_id').' = mobile_money_providers.id');
        $mobile_money_accounts = $this->db->get('mobile_money_accounts')->result();

        foreach ($mobile_money_accounts as $key => $mobile_money_account) {
            $mobiles[$mobile_money_account->id] = array(
                'balance'   => $mobile_money_account->balance,
                'account_name' => $mobile_money_account->account_name,
                'mobile_money_provider_name' => $mobile_money_account->mobile_money_provider_name,
            );
        }



        $this->db->select(
            array(
                'petty_cash_accounts.id',
                $this->dx('initial_balance').' + '.$this->dx('current_balance').' as balance ',
                $this->dxa('account_name'),
            )
        );
        if($group_id){
            $this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);

        $this->db->where($this->dx('petty_cash_accounts.active').' ="1"',NULL,FALSE);
        $petty_cash_accounts = $this->db->get('petty_cash_accounts')->result();

        foreach ($petty_cash_accounts as $key => $petty_cash_account) {
            $pettys[$petty_cash_account->id] = array(
                'balance'   => $petty_cash_account->balance,
                'account_name' => $petty_cash_account->account_name,
            );
        }

        return array(
            'banks' => $banks,
            'saccos' => $saccos,
            'mobiles' => $mobiles,
            'pettys' => $pettys,
        );

    }

    function get_individual_groups_account_summary_balances_array($group_id=0){
        $arr = array();
        $this->db->select(
            array(
                'bank_accounts.id',
                $this->dx('initial_balance').' as initial_balance ',
                $this->dx('current_balance').' as current_balance ',
                $this->dxa('account_name'),
                $this->dx('banks.name').' as bank_name',
            )
        );
        if($group_id){
            $this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('bank_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->where($this->dx('bank_accounts.active').' ="1"',NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').' = banks.id');
        $bank_accounts = $this->db->get('bank_accounts')->result();
        $i = 0;

        foreach ($bank_accounts as $key => $bank_account) {
            $arr[$i] = array(
                'balance'   => floatval($bank_account->initial_balance)+ floatval($bank_account->current_balance),
                'description' => $bank_account->bank_name.' -'.$bank_account->account_name,
            );
            $i++;
        }

        $this->db->select(
            array(
                'sacco_accounts.id',
                $this->dx('initial_balance').' as initial_balance ',
                $this->dx('current_balance').' as current_balance ',
                $this->dxa('account_name'),
                $this->dx('saccos.name').' as sacco_name',
            )
        );
        if($group_id){
            $this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);

        $this->db->where($this->dx('sacco_accounts.active').' ="1"',NULL,FALSE);
        $this->db->join('saccos',$this->dx('sacco_accounts.sacco_id').' = saccos.id');
        $sacco_accounts = $this->db->get('sacco_accounts')->result();

        foreach ($sacco_accounts as $key => $sacco_account) {
            $arr[$i] = array(
                'balance'   => floatval($sacco_account->initial_balance)+floatval($sacco_account->current_balance),
                'description' => $sacco_account->sacco_name.' -'.$sacco_account->account_name,
            ); 
            $i++;
        }


        $this->db->select(
            array(
                'mobile_money_accounts.id',
                $this->dx('initial_balance').' as initial_balance ',
                $this->dx('current_balance').' as current_balance ',
                $this->dxa('account_name'),
                $this->dx('mobile_money_providers.name').' as mobile_money_provider_name',
            )
        );
        if($group_id){
            $this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);

        $this->db->where($this->dx('mobile_money_accounts.active').' ="1"',NULL,FALSE);
        $this->db->join('mobile_money_providers',$this->dx('mobile_money_accounts.mobile_money_provider_id').' = mobile_money_providers.id');
        $mobile_money_accounts = $this->db->get('mobile_money_accounts')->result();

        foreach ($mobile_money_accounts as $key => $mobile_money_account) {
            $arr[$i] = array(
                'balance'   => floatval($mobile_money_account->initial_balance)+floatval($mobile_money_account->current_balance),
                'description' => $mobile_money_account->mobile_money_provider_name.' -'.$mobile_money_account->mobile_money_provider_name,
            );
            $i++;
        }



        $this->db->select(
            array(
                'petty_cash_accounts.id',
                $this->dx('initial_balance').' as initial_balance ',
                $this->dx('current_balance').' as current_balance ',
                $this->dxa('account_name'),
            )
        );
        if($group_id){
            $this->db->where($this->dx('group_id').' ="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').' ="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);

        $this->db->where($this->dx('petty_cash_accounts.active').' ="1"',NULL,FALSE);
        $petty_cash_accounts = $this->db->get('petty_cash_accounts')->result();

        foreach ($petty_cash_accounts as $key => $petty_cash_account) {
            $arr[$i] = array(
                'balance'   => floatval($petty_cash_account->initial_balance)+floatval($petty_cash_account->current_balance),
                'description' => $petty_cash_account->account_name,
            );
            $i++;
        }

        return $arr;

    }

    function get_active_group_accounts($return_option_groups = TRUE,$include_back_dating_suspense_account = FALSE,$group_id = 0,$hide_default = FALSE){
        $this->select_all_secure('bank_accounts');
        $this->db->select(array(
            $this->dx('banks.name').'as bank_name',
            $this->dx('bank_branches.name').' as bank_branch_name',
            'CONCAT('.$this->dx('banks.name').',('.$this->dx('bank_branches.name').') ,"-" ,'.$this->dx('bank_accounts.account_name').') as full_name'
            // .'as bank_name',$this->dx('bank_branches.name').' as bank_branch_name'

             // $mobile['mobile-'.$value->id] =$value->mobile_money_provider_name.' - '.$value->account_name.' ('.$value->account_number.')';
              // $value->bank_name.' ('.trim($value->bank_branch_name).') - '.$value->account_name.' ('.$value->account_number.')';
        ));
        if($group_id){
            $this->db->where($this->dx('bank_accounts.group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('bank_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('bank_accounts.active').'="1"',NULL,FALSE);
        if($hide_default){
            $this->db->where($this->dx('bank_accounts.is_default').'IS NULL',NULL,FALSE);
        }
        $this->db->where("(".$this->dx('bank_accounts.is_deleted').' IS NULL OR '.$this->dx('bank_accounts.is_deleted')." = 0 )",NULL,FALSE);
        $this->db->join('banks',$this->dx('bank_accounts.bank_id').'= banks.id','INNER');
        $this->db->join('bank_branches',$this->dx('bank_accounts.bank_branch_id').'= bank_branches.id','INNER');
        $bank_accounts = $this->db->get('bank_accounts')->result();
        // print_r($bank_accounts); die;
        $bank = array();
        $mobile = array();
        $petty=array();
        $sacco = array();

        foreach ($bank_accounts as $key => $value) {
            $bank['bank-'.$value->id] = $value;
        }

        $this->select_all_secure('sacco_accounts');
        $this->db->select(array(
            $this->dx('saccos.name').'as sacco_name',
            $this->dx('sacco_branches.name').' as sacco_branch_name',
            'CONCAT('.$this->dx('saccos.name').',('.$this->dx('sacco_branches.name').') ,"-" ,'.$this->dx('sacco_accounts.account_name').') as full_name'
        ));
        if($group_id){
            $this->db->where($this->dx('sacco_accounts.group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('sacco_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('sacco_accounts.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('sacco_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->join('saccos',$this->dx('sacco_accounts.sacco_id').'= saccos.id','INNER');
        $this->db->join('sacco_branches',$this->dx('sacco_accounts.sacco_branch_id').'= sacco_branches.id','INNER');
        $sacco_accounts = $this->db->get('sacco_accounts')->result();

        foreach ($sacco_accounts as $key => $value) {
            $sacco['sacco-'.$value->id] = $value;
        }

        $this->select_all_secure('mobile_money_accounts');
        $this->db->select(array(
            $this->dx('mobile_money_providers.name').'as mobile_money_provider_name',
            'CONCAT('.$this->dx('mobile_money_providers.name').', "-" ,'.$this->dx('mobile_money_accounts.account_name').') as full_name'
        ));
        if($group_id){
            $this->db->where($this->dx('mobile_money_accounts.group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('mobile_money_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        $this->db->where($this->dx('mobile_money_accounts.active').'="1"',NULL,FALSE);
        $this->db->where($this->dx('mobile_money_accounts.is_deleted').' IS NULL ',NULL,FALSE);
        $this->db->join('mobile_money_providers',$this->dx('mobile_money_accounts.mobile_money_provider_id').'= mobile_money_providers.id','INNER');
        $mobile_money_accounts = $this->db->get('mobile_money_accounts')->result();

        foreach ($mobile_money_accounts as $key => $value) {
            $mobile['mobile-'.$value->id] =$value;
        }


        $this->select_all_secure('petty_cash_accounts');
         $this->db->select(array(
            $this->dx('petty_cash_accounts.account_name').'as full_name',
        ));
        if($group_id){
            $this->db->where($this->dx('petty_cash_accounts.group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('petty_cash_accounts.group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        if($include_back_dating_suspense_account){

        }else{
            $this->db->where($this->dx('petty_cash_accounts.active').'="1"',NULL,FALSE);
        }
        $this->db->where($this->dx('petty_cash_accounts.is_deleted').' IS NULL ',NULL,FALSE);

        $petty_cash_accounts = $this->db->get('petty_cash_accounts')->result();

        foreach ($petty_cash_accounts as $key => $value) {
            $petty['petty-'.$value->id] = $value;
        }
        if($return_option_groups){
            return array(
                'Bank Accounts' => $bank,
                'Sacco Accounts' => $sacco,
                'Mobile Money Accounts' => $mobile,
                'Petty Cash Accounts' => $petty,
            );
        }else{
            //die("Am in");
            return $bank + $sacco + $mobile + $petty;
        }
    }

}
?>