<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Investment_groups{

	protected $ci;

	public $application_settings;

    public $default_currency_id;

    public $default_country_id;

    public $default_theme_slug;

	public $reserved_words = array(
        'porn',
        'sex',
        'www',
        'violence',
        'spam',
        'help',
        'cpanel',
        'whm',
        'mail',
        'admin',
        'administrator',
        '-',
        'xxx',
        'fuck',
        'analytics',
        'kice-foundation'
    );

    public $system_group_roles = array(
        1 => 'Chair Person',
        2 => 'Treasurer',
        3 => 'Secretary',
        // 4 => 'Member',
    );

    public $system_petty_cash_accounts = array(
        'Cash at Hand',
    );

    public $system_expense_categories = array(
        'Stationery',
        'Bank Charges',
        'Withholding Tax',
        'Company Registration',
        'Legal Fees',
        'Wages',
        'Salaries',
        'Donation',
        'Miscellaneous Expenses',
        'Subscription Fees',
        'Office Expenses',
        'Insurance Fees',
        'Excise Duty',
        'Transport',
        'Courier',
        'Search Fee',
        'Food and Drinks',
        'Brokerage Fees',
        'Operating Expenses',
        'Benevolence',
        'Company Seal',
        'Telephone & Postages',
        'Corporate Social Responsibility',
    );

    public $system_income_categories = array(
        'Rent',
        'Interest',
        'Sales',
        'Miscellaneous Income',
        'Dividends',
        'Lease',
    );

    public $system_fine_categories = array(
        'Absent without apology',
        'Lateness to attend meeting',
        'Rude behavior',
        'Phone calls during meetings',
        'Absent with apology',
        'Absconding duty',
        'Miscellaneous fine',
    );

    public $system_asset_categories = array(
        'Land',
        'Buildings and Factories',
        'Furniture, Fixtures and Equipment',
        'Intangible Assets',
        'Machinery and Equipment',
        'Motor Vehicle',
    );

    public $statement_sending_date_options = array(
        '1'=>'Every 1st',
        '2'=>'Every 2nd',
        '3'=>'Every 3rd',
        '4'=>'Every 4th',
        '5'=>'Every 5th',
        '6'=>'Every 6th',
        '7'=>'Every 7th',
        '8'=>'Every 8th',
        '9'=>'Every 9th',
        '10'=>'Every 10th',
        '11'=>'Every 11th',
        '12'=>'Every 12th',
        '13'=>'Every 13th',
        '14'=>'Every 14th',
        '15'=>'Every 15th',
        '16'=>'Every 16th',
        '17'=>'Every 17th',
        '18'=>'Every 18th',
        '19'=>'Every 19th',
        '20'=>'Every 20th',
        '21'=>'Every 21st',
        '22'=>'Every 22nd',
        '23'=>'Every 23rd',
        '24'=>'Every 24th',
        '25'=>'Every 25th',
        '26'=>'Every 26th',
        '27'=>'Every 27th',
        '28'=>'Every 28th',
    );

    public $type_of_groups = array(
        1 => 'Investment club',
        2 => 'Chama',
        3 => 'Sacco',
        4 => 'Merry-go-round',
        5 => 'Welfare Group',
        6 => 'Table Banking Group',
        7 => 'Self Help Group',
        8 => 'Digital Lender',
        9 => 'Other',
    );

	public function __construct(){
		$this->ci= & get_instance();
        $this->ci->load->model('groups/groups_m');
		$this->ci->load->model('partners/partners_m');
		$this->ci->load->model('emails/emails_m');
		$this->ci->load->model('settings/settings_m');
        $this->ci->load->model('sms/sms_m');
        $this->ci->load->model('themes/themes_m');
        $this->ci->load->model('countries/countries_m');
        $this->ci->load->model('petty_cash_accounts/petty_cash_accounts_m');
        $this->ci->load->model('expense_categories/expense_categories_m');
        $this->ci->load->model('income_categories/income_categories_m');
        $this->ci->load->model('fine_categories/fine_categories_m');
		$this->ci->load->model('asset_categories/asset_categories_m');
        $this->ci->load->model('group_roles/group_roles_m');
        $this->ci->load->model('menus/menus_m');        
        $this->ci->load->library('messaging');
        $this->ci->load->helper('string');
        $this->ci->load->library('group_members');
		// $this->ci->load->library('bank');

		$this->application_settings = $this->ci->settings_m->get_settings()?:'';

        $country = $this->ci->countries_m->get_default_country();
        $this->default_country_id = $country?$country->id:0;
        $this->default_currency_id = $country?$country->id:0;

        //$theme = $this->ci->themes_m->get_default_theme();
        //$this->default_theme_slug = $theme?$theme->slug:'';
        $this->ci->load->model('permissions/permissions_m');
	}

    function current_user_groups($user_id=''){
        return $this->ci->groups_m->current_user_groups($user_id);
    }

    function current_user_groups_managed($user_id=''){
        return $this->ci->groups_m->current_user_groups_managed($user_id);
    }

	function generate_slug($group_name = ''){
		$slug = trim(strtolower($group_name));
		if(in_array($slug,$this->reserved_words)){
            $slug = substr(md5(rand(1111, 9999)), 0, 6);
        }
        $slug = preg_replace('/\W/', '-', $slug);
        $slug = preg_replace('/(-)+/', '-', $slug);
        $slug = preg_replace('/(-)+$/', '', $slug);
        $slug = preg_replace('/^(-)+/', '', $slug);
        $slug = $this->ci->groups_m->generate_slug($slug);
        return $slug;
	}

	function create($name = '',$size = 0,$created_by = 0,$referrer_id = 0,$referrer_information = "",$activate_group = FALSE,$partner_slug = "",$additional_data=array()){
		//create the group
		$activation_code = rand(10000,99999);
        if($partner = $this->ci->partners_m->get_by_slug(trim($partner_slug))){
            $partner_id = $partner->id;
        }else{
            $partner_id = 0;
        }
        $data = array(
            'name' => $name,
            'size' => $size,
            'slug' => $this->generate_slug($name),
            'created_on' => time(),
            'created_by' => $created_by,
            'trial_days'  => $this->application_settings->trial_days,
            'active' => 1,
            'billing_package_id' => $this->application_settings->default_billing_package,
            'sms_balance' => $this->application_settings->billing_monthly_sms,
            'billing_cycle' => 3,
            'subscription_status' => 1,
            'group_setup_position' => 1,
            'owner' => '',
            'activation_code' => $activation_code,
            'lock_access'=> 0,
            'referrer_id'=> $referrer_id,
            'referrer_information'=> $referrer_information,
            'partner_id' => $partner_id,
            'enable_member_information_privacy' => 1,
            'disable_member_directory' => 1
        )+$additional_data;
        $group_id = $this->ci->groups_m->insert($data);
        if($group_id){
            $current_country = $this->get_group_country_region();
            $account_number = $this->generate_account_number($group_id);
            if($current_country){
                $account_number_update = $this->ci->groups_m->update($group_id,array('account_number'=>$account_number,'country_id' =>$current_country->id,'currency_id' => $current_country->id));
            }else{
               $account_number_update = $this->ci->groups_m->update($group_id,array('account_number'=>$account_number)); 
            }
            if($account_number_update)
            {
                // $user = $this->ci->ion_auth->get_user($created_by);
                // if($user){
                //     if($activate_group){
                //         return $group_id;
                //     }else{
                //         return $group_id;
                        /*if($this->ci->messaging->send_activation_code($user,$activation_code,$this->generate_slug($name),$name)){
                           
                        }else{
                            $this->ci->session->set_flashdata('warning','We could not send you the Activation code at this moment, kindly click on resend activation code to have it resent.');
                            return $group_id;
                        }*/
                //     }
                // }else{
                //     $this->ci->session->set_flashdata('error','User does not exist');
                //     return FALSE;
                // }
                return $group_id;
            }else{
                $this->ci->session->set_flashdata('error','Group account number could not be generated');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Group could not be created');
            return FALSE;
        }
	}

    function get_group_country_region(){
        if(defined('COUNTRY_CODE')){
            $current_country = $this->ci->countries_m->get_country_by_code(COUNTRY_CODE);
        }else{
            $current_country = $this->ci->countries_m->get_default_country();
        }
    }

    function create_group($user_id = 0,$group_name = 0,$group_size = 0,$referrer_id = 0,$referrer_information = "",$activate_group = FALSE,$partner_slug = "",$group_role_key = 0,$additional_data=array(),$create_member=true){
        $user = $this->ci->ion_auth->get_user($user_id);
        if($user){
            $group_id = $this->create($group_name,$group_size,$user->id,$referrer_id,$referrer_information,$activate_group,$partner_slug,$additional_data);
            if($group_id){ 
                if($group_role_key == -1){
                    //create staff
                }else{
                    if($create_member){
                        if($member_id = $this->ci->group_members->create($group_id,$user,$user->id,TRUE)){
                            $group = $this->ci->groups_m->get($group_id);
                            $member = $this->ci->members_m->get($member_id);
                            if($this->create_group_default_data($group,$user,$member)){
                                $member_update = array();
                                foreach ($this->system_group_roles as $key => $value) {
                                    $group_role =  array(
                                        'name' => $value,
                                        'group_id' => $group_id,
                                        'is_editable'=> 0,
                                        'created_by' => $user_id,
                                        'active' => 1,
                                        'created_on'=> time(),
                                    );
                                    $group_role_id = $this->ci->group_roles_m->insert($group_role);
                                    if($key == $group_role_key){
                                        $member_update =  array(
                                            'group_role_id' => $group_role_id,
                                            'modified_by' => $user_id,
                                            'modified_on'=> time(),
                                        );
                                    }
                                    
                                }
                                if(empty($member_update)){
                                    $this->ci->group_members->set_active_group_size($group_id);
                                    return $group_id; 
                                }else{
                                    if($this->ci->members_m->update($member_id,$member_update)){
                                        $this->ci->group_members->set_active_group_size($group_id);
                                        return $group_id; 
                                    }else{
                                        $this->ci->session->set_flashdata('error','Something went wrong during setting member group role');
                                        return FALSE;
                                    }
                                }
                            }else{
                                return FALSE;
                            }
                        }else{
                            $this->ci->session->set_flashdata('error','Something went wrong during member creation');
                            return FALSE;
                        }
                    }else{
                        $group = $this->ci->groups_m->get($group_id);
                        if($this->create_group_default_data($group,$user)){
                            $member_update = array();
                            foreach ($this->system_group_roles as $key => $value) {
                                $group_role =  array(
                                    'name' => $value,
                                    'group_id' => $group_id,
                                    'is_editable'=> 0,
                                    'created_by' => $user_id,
                                    'active' => 1,
                                    'created_on'=> time(),
                                );
                                $group_role_id = $this->ci->group_roles_m->insert($group_role);
                            }
                            $this->ci->group_members->set_active_group_size($group_id);
                            return $group_id; 
                        }else{
                            return FALSE;
                        }
                    }
                }
            }else{
                $this->ci->session->set_flashdata('error','Something went wrong during group creation');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error',$this->ci->ion_auth->errors());
            return FALSE;
        }
    }

    function set_default_permissions($group_id=0,$user_id = 0){
        $menus = $this->ci->menus_m->get_parent_menu_options();
        $group_roles = $this->ci->group_roles_m->get_group_role_options($group_id);
        $input = array();
        $group_ids = array();
        $menu_ids = array();
        $role_ids = array();
        $created_by_array = array();
        $created_on_array = array();
        $actives = array();
        if($menus && $group_roles){
            foreach ($group_roles as $group_role_id=>$group_role_name) {
                $group_role_name = remove_special_characters($group_role_name);
                if(array_key_exists($group_role_name, $this->group_roles_default_menus)){
                    $role_menus = $this->group_roles_default_menus[$group_role_name];
                    foreach ($menus as $menu_id => $menu_name) {
                        foreach ($role_menus as $role_menu) {
                            if(preg_match('/'.$role_menu.'/', remove_special_characters($menu_name))){
                                $group_ids[] = $group_id;
                                $menu_ids[] = $menu_id;
                                $role_ids[] = $group_role_id;
                                $created_by_array[] = $user_id;
                                $created_on_array[] = time();
                                $actives[] = 1;
                                if($this->ci->menus_m->has_active_children($menu_id)){
                                    $children_links = $this->ci->menus_m->get_active_children_links($menu_id);
                                    $i=0;
                                    foreach ($children_links as $child) {
                                        $group_ids[] = $group_id;
                                        $menu_ids[] = $child->id;
                                        $role_ids[] = $group_role_id;
                                        $created_by_array[] = $user_id;
                                        $created_on_array[] = time();
                                        $actives[] = 1;
                                        if($this->ci->menus_m->has_active_children($child->id)){
                                            $grand_children_links = $this->ci->menus_m->get_active_children_links($child->id);
                                            foreach ($grand_children_links as $grand_child) {
                                                $group_ids[] = $group_id;
                                                $menu_ids[] = $grand_child->id;
                                                $role_ids[] = $group_role_id;
                                                $created_by_array[] = $user_id;
                                                $created_on_array[] = time();
                                                $actives[] = 1;
                                            }
                                        }         
                                    }
                                }
                            }
                        }
                    }       
                }
            }
            $input = array(
                    'group_id'  =>  $group_ids,
                    'menu_id'   =>  $menu_ids,
                    'role_id'   =>  $role_ids,
                    'created_by'=>  $created_by_array,
                    'created_on'=>  $created_on_array,
                    'active'    =>  $actives,
                );
            if($input && !empty($input['menu_id' ])){
                if($this->ci->permissions_m->delete_group_permissions($group_id)){
                    if($this->ci->permissions_m->insert_batch($input)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    function create_group_default_data($group = array(),$user = array(),$member = array()){
        if($group&&$user){
            $group_id = $group->id;
            $user_id = $user->id;
            $result  = TRUE;
            $group_id_array = array();
            // $is_editable_array = array();
            $created_by_array = array();
            $active_array = array();
            $created_on_array = array();

            // foreach($this->system_group_roles as $group_role){
            //     $group_id_array[] = $group_id;
            //     $is_editable_array[] = 0;
            //     $created_by_array[] = $user_id;
            //     $active_array[] = 1;
            //     $created_on_array[] = time();
            // }

            // $input =  array(
            //     'name'=>$this->system_group_roles,
            //     'group_id'=>$group_id_array,
            //     'is_editable'=>$is_editable_array,
            //     'created_by'=>$created_by_array,
            //     'active'=>$active_array,
            //     'created_on'=>$created_on_array,
            // );

            // if($this->ci->group_roles_m->insert_batch($input)){
                // if($this->set_default_permissions($group_id,$user_id)){
                    //do nothing
                // }else{
                    // $this->ci->session->set_flashdata('error','Something went wrong during group role permissions creation.');
                    // $result = FALSE;
                // }
            // }else{
                // $this->ci->session->set_flashdata('error','Something went wrong during group role creation.');
                // $result = FALSE;
            // }

            $account_slug_array = array();
            $initial_balance_array = array();
            $created_by_array = array();
            $created_on_array = array();
            $group_id_array = array();
            $active_array = array();

            foreach($this->system_petty_cash_accounts as $petty_cash_account){
                $account_slug_array[] = strtolower(str_replace(" ","-",trim($petty_cash_account)));
                $initial_balance_array[] = 0;
                $created_by_array[] = $user_id;
                $created_on_array[] = time();
                $group_id_array[] = $group_id;
                $active_array[] = 1;
            }

            $input = array(
                'account_name' => $this->system_petty_cash_accounts,
                'account_slug' => $account_slug_array,
                'initial_balance' => $initial_balance_array,
                'created_by' => $created_by_array,
                'created_on' => $created_on_array,
                'group_id' => $group_id_array,
                'active' => $active_array,
            );

            if($this->ci->petty_cash_accounts_m->insert_batch($input)){

            }else{
                $this->ci->session->set_flashdata('error','Something went wrong during petty cash account creation.');
                $result = FALSE;
            }

            $slug_array = array();
            $description_array = array();
            $group_id_array = array();
            $active_array = array();
            $created_by_array = array();
            $created_on_array = array();

            foreach($this->system_expense_categories as $expense_category){
                $slug_array[] = strtolower(str_replace(" ","-",trim($expense_category)));
                $description_array[] = "";
                $group_id_array[] = $group_id;
                $active_array[] = 1;
                $created_by_array[] = $user_id;
                $created_on_array[] = time();
            }

            $input = array(
                'name' => $this->system_expense_categories,
                'slug' => $slug_array,
                'description' => $description_array,
                'group_id' => $group_id_array,
                'active' => $active_array,
                'created_by' => $created_by_array,
                'created_on' => $created_on_array,
            );

            if($this->ci->expense_categories_m->insert_batch($input)){

            }else{
                $this->ci->session->set_flashdata('error','Something went wrong during expense category creation.');
                $result = FALSE;
            }

            $slug_array = array();
            $description_array = array();
            $group_id_array = array();
            $active_array = array();
            $created_by_array = array();
            $created_on_array = array();

            foreach($this->system_income_categories as $income_category){
                $slug_array[] = strtolower(str_replace(" ","-",trim($income_category)));
                $description_array[] = "";
                $group_id_array[] = $group_id;
                $active_array[] = 1;
                $created_by_array[] = $user_id;
                $created_on_array[] = time();
            }

            $input = array(
                'name' => $this->system_income_categories,
                'slug' => $slug_array,
                'description' => $description_array,
                'group_id' => $group_id_array,
                'active' => $active_array,
                'created_by' => $created_by_array,
                'created_on' => $created_on_array,
            );

            if($this->ci->income_categories_m->insert_batch($input)){

            }else{
                $this->ci->session->set_flashdata('error','Something went wrong during income category creation.');
                $result = FALSE;
            }

            $slug_array = array();
            $description_array = array();
            $group_id_array = array();
            $active_array = array();
            $created_by_array = array();
            $created_on_array = array();

            foreach($this->system_fine_categories as $fine_category){
                $slug_array[] = strtolower(str_replace(" ","-",trim($fine_category)));
                $description_array[] = "";
                $group_id_array[] = $group_id;
                $active_array[] = 1;
                $created_by_array[] = $user_id;
                $created_on_array[] = time();
            }

            $input = array(
                'name' => $this->system_fine_categories,
                'slug' => $slug_array,
                'description' => $description_array,
                'group_id' => $group_id_array,
                'active' => $active_array,
                'created_by' => $created_by_array,
                'created_on' => $created_on_array,
            );

            if($this->ci->fine_categories_m->insert_batch($input)){

            }else{
                $this->ci->session->set_flashdata('error','Something went wrong during fine category creation.');
                $result = FALSE;
            }

            $slug_array = array();
            $group_id_array = array();
            $active_array = array();
            $created_by_array = array();
            $created_on_array = array();

            foreach($this->system_asset_categories as $asset_category){
                $slug_array[] = strtolower(str_replace(" ","-",trim($asset_category)));
                $group_id_array[] = $group_id;
                $active_array[] = 1;
                $created_by_array[] = $user_id;
                $created_on_array[] = time();
            }

            $input = array(
                'name' => $this->system_asset_categories,
                'slug' => $slug_array,
                'group_id' => $group_id_array,
                'active' => $active_array,
                'created_by' => $created_by_array,
                'created_on' => $created_on_array,
            );

            if(!$this->ci->asset_categories_m->insert_batch($input)){
                //$this->ci->bank->open_online_account($user,$group,$member);
                $this->ci->session->set_flashdata('error','Something went wrong during asset category creation.');
                $result = FALSE;
            }

            return $result;
        }else{
            $this->ci->session->set_flashdata('error','Something variables are missing. Could not create group data.');
            return FALSE;
        }
    }

    function generate_account_number($group_id='')
    {
        if($group_id){
            $bill_number_start = $this->application_settings->bill_number_start;
            $account_number = $bill_number_start+$group_id;
            if($this->ci->groups_m->count_groups_by_account($account_number)){
                $new_account_number = $account_number;
                for($i=0;$i<1000000;$i++){
                    if($this->ci->groups_m->count_groups_by_account($new_account_number)){
                        ++$new_account_number;
                    }else{
                        break;
                    }
                }
                return $new_account_number;
            }else{
                return $account_number;
            }
        }else{
            return FALSE;
        }
    }


    function backup_group_data($group_id=0,$delete=0,$reset=0,$user_id=0,$is_hidden=0){
        if($group_id){
            $group = $this->ci->groups_m->get($group_id);
            $file_name = $this->ci->migrate_m->backup_group($group_id,$delete,$reset,$user_id);
            $success = $reset?1:$this->ci->groups_m->delete($group_id);
            if($success){
                $result = TRUE;
                if($delete){
                    $creator = $this->ci->ion_auth->get_user($group->created_by,TRUE);
                     $group_data = array(
                        'backup_file' => $file_name,
                        'group_id' => $group->id,
                        'group_name' => $group->name,
                        'deleted_on' => time(),
                        'deleted_by' => $user_id?:$this->user->id,
                        'restore_status' => 0,
                        'is_hidden' => $is_hidden?:0,
                        'group_size' => $group->size,
                        'active_size' => $group->active_size,
                        'group_phone' => $group->phone,
                        'group_email' => $group->email,
                        'group_created_by' => $group->created_by,
                        'group_created_on' => $group->created_on,
                        "created_by_user" => $creator?($creator->first_name.' '.$creator->last_name):'',
                        "last_group_activity" => $group->last_group_activity,
                    );
                    $delete_id = $this->ci->groups_m->insert_group_deletion_data($group_data);
                    $members = $this->ci->members_m->get_group_members($group_id);
                    foreach ($members as $member) {
                        if($this->ci->members_m->delete($member->id)){
                            $member_deletion_data =array(
                                'user_id' => $member->user_id,
                                'group_id' => $member->group_id,
                                'group_name' => $group->name,
                                'group_deletion_id' => $delete_id,
                                'restore_status' => 0,
                                'is_hidden' => $is_hidden?:0,
                                'member_id' => $member->id,
                                'created_on' => time(),
                            );
                            $this->ci->members_m->insert_member_deletion_data($member_deletion_data);
                        }else{
                            $result = FALSE;
                        }
                    }
                    $ignore_tables = array('group_deletions','member_deletion_data');
                }else if($reset){
                    $ignore_tables = array(
                        'equity_bank_transaction_alerts',
                        'transaction_alerts',
                        'investment_groups',
                        'members',
                        'bank_accounts',
                        'sacco_accounts',
                        'mobile_money_accounts',
                        'petty_cash_accounts',
                        'contributions',
                        'regular_contribution_settings',
                        'one_time_contribution_settings',
                        'contribution_fine_settings',
                        'contribution_member_pairings',
                        'member_deletion_data'
                    );
                }
                if(preg_match('/(eazzyclub)/',$_SERVER['HTTP_HOST'])){
                    $database = 'eazzyclub';
                }else if(preg_match('/(eazzychama)/',$_SERVER['HTTP_HOST'])){
                    $database = 'eazzychama';
                }else{
                    $database = 'chamasoft';
                }
                $tables=$this->ci->db->query("SELECT t.TABLE_NAME AS table_name FROM INFORMATION_SCHEMA.TABLES AS t WHERE t.TABLE_SCHEMA = '".$database."' ")->result_array();    
                $count = 1;
                foreach($tables as $key => $val) {
                    $table_name = $val['table_name'];
                    if($this->ci->db->field_exists('group_id',$table_name)){
                        if(in_array($table_name,$ignore_tables)){
                            if($table_name=='transaction_alerts'){
                                $this->ci->migrate_m->unset_group_transaction_alerts($group_id);
                            }
                        }else{
                            if($this->ci->migrate_m->delete_group_data($group_id,$table_name)){
                            }else{
                                $result = FALSE;
                            }
                        }
                    }
                }
                if(in_array('bank_accounts', $ignore_tables)){
                    $this->ci->accounts_m->reset_group_bank_account($group_id);
                }
            }
            return TRUE;
        }else{
            $this->ci->session->set_flashdata('error','Group data missing');
            return FALSE;
        }
    }

    function restore_group_data($id=0){
        $deletion = $this->ci->groups_m->get_group_deletion($id);
        if($id = $this->ci->migrate_m->restore_group(0,$deletion->backup_file)){
            $this->ci->groups_m->update_secure_table($id,array(
                'modified_by' => $this->user->id,
                'modified_on' => time(),
                'restore_status' => 1,
            ),'group_deletions');
        }
        return $id;
    }


    function update_last_seen($group_id=0,$user_id=0){
        if($group_id && $user_id){
            if($this->ci->ion_auth->is_in_group($user_id,2)){
                $update = array(
                    'last_group_activity' => time(),
                    'modified_on' => time(),
                    'modified_by' => $user_id,
                );
                if($this->ci->groups_m->update($group_id,$update)){
                    return TRUE;
                }
            }
        }
    }
}
?>