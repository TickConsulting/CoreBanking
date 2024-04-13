<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Code here is run before admin controllers
class Group_Controller extends Authentication_Controller{
    public $group;
    public $group_has_partner_bank_account;
    public $group_unverified_bank_accounts;
    public $unreconciled_deposits_count;
    public $unreconciled_withdrawals_count;
    public $member;
    public $group_setup_tasks;
    public $completed_setup_tasks_count;
    public $currency_options;
    public $currency_code_options;
    public $incomplete_setup_tasks_count;
    public $pending_bank_account_tasks_count;
    public $notifications_count;
    public $setup_tasks_count;
    public $group_currency;
    public $group_calling_code;
    public $group_calling_code_options;
    public $unread_member_notifications;
    public $unread_member_notifications_count;
    public $unread_member_emails_count;
    public $group_member_options;
    public $group_debtor_options;
    public $active_group_debtor_options;
    public $active_group_member_options_by_user_id;
    public $active_group_member_options;
    public $active_group_membership_number_options;
    public $group_partner_bank_account_number_list;
    public $group_partner_mobile_money_account_number_list;
    public $group_billing_information;
    public $group_deficit;
    public $login_action;
    public $theme;
    public $member_role_permissions;
    public $member_listing_order_by_options;
    public $order_by_options;
    public $show_membership_number;
    public $membership_numbers;
    public $active_loan_applications;
    public $unread_member_email_count = 0;
    public $unread_member_emails_array = array();
    public $pending_withdrawal_approval_requests;
    public $pending_withdrawal_approval_requests_count;
    public $pending_withdrawal_approval_requests_count_and_reconcile;
    public $withdrawal_tasks_count;
    public $statement_sending_date_options;
    public $group_user_options;
    public $group_type_options;
    public $group_member_detail_options;
    public $group_role_options;
    
    public function __construct(){
        parent::__construct();
        $this->load->model('bank_accounts/bank_accounts_m');
        $this->load->model('mobile_money_accounts/mobile_money_accounts_m');
        $this->load->model('transaction_alerts/transaction_alerts_m');
        $this->load->model('setup_tasks/setup_tasks_m');
        $this->load->model('countries/countries_m');
        $this->load->model('notifications/notifications_m');
        $this->load->model('billing/billing_m');
        $this->load->model('emails/emails_m');
        $this->load->model('themes/themes_m');
        $this->load->model('group_account_managers/group_account_managers_m');
        $this->load->library('notifications');
        $this->load->library('billing_settings');
        $this->load->library('setup_tasks_tracker');
        $this->load->library('themes');
        $this->load->model('loan_applications/loan_applications_m');
        $this->load->model('debtors/debtors_m');
        if(preg_match('/\.local/', $_SERVER['HTTP_HOST'])){
            //$this->output->enable_profiler(TRUE);
        }        

        if(!$this->ion_auth->is_admin($this->user->id) && !$this->ion_auth->is_bank_admin($this->user->id) && !$this->ion_auth->is_group_member($this->user->id) && !$this->ion_auth->is_group_account_manager($this->user->id)){
            $this->session->set_flashdata('info', 'You dont have rights to access that panel.');
            redirect('authentication/checkin');
        }
        
        $admin_theme_name = 'groups';
        if (!defined('ADMIN_THEME')){
            define('ADMIN_THEME', $admin_theme_name);
        }
        // Prepare Asset library
        $this->asset->set_theme($admin_theme_name);
        $this->template->enable_parser(TRUE)->set_theme($admin_theme_name)->set_layout('default.html');
        $slug = '';
        $url_segments = explode(".",$_SERVER['HTTP_HOST']);
        $url_segments_dots = count($url_segments);
        // if($url_segments_dots>2){
            $group = array();
            $slug = $url_segments[0];
            $session_group_id = $this->session->userdata('group_id');
            // if($slug && !preg_match('/app/', $slug) && !preg_match('/uat/', $slug)){
            //     $current_url = current_url();
            //     $group = $this->groups_m->get_by_slug($slug);
            //     $this->session->set_userdata('group_id',$group->id);
            //     //$new_url = str_replace($slug,(preg_match('/(uat)/',$_SERVER['HTTP_HOST'])?'uat':'app'), $current_url);
            //     $new_url = $current_url;
            //     $get_string = $_SERVER['QUERY_STRING'];
            //     if($get_string){
            //         $new_url = $new_url."?".$get_string;
            //     }
            //     redirect($new_url);        
            // }else if($session_group_id){
            $group = $this->groups_m->get($session_group_id);
            // }
            if($group){
                $this->group = $group;
                if($group->theme){
                    if($theme = $this->themes_m->get_by_slug($group->theme)){
                        $this->theme = $theme;
                    }
                }
                $this->theme_slug = $group->theme;
                $this->session->set_userdata('active_checkin',$group->id);
                $this->investment_groups->update_last_seen($this->group->id,$this->user->id);
                if($this->ion_auth->is_admin()||$this->ion_auth->is_bank_admin()||$this->ion_auth->is_group_account_manager()){
                    $member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id);
                    if($member){
                        $this->member = $member;
                    }else{
                        $this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->group->owner);
                    }
                    if($this->group->group_setup_status){ //if admin is member in group, check setup tasks

                    }else{
                        if(preg_match('/group\/setup_tasks/',current_url())){

                        }else{
                            redirect('group/setup_tasks/');
                        }
                    }
                }else{
                    $member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id);
                    if($member){
                        if($member->group_role_id){
                            $member_role_permissions=$this->permissions_m->get_group_member_role_permissions($member->group_role_id);
                        }
                        if($member->is_admin || $member->group_role_id){
                            $this->member = $member; 
                            $this->member_role_permissions='';

                        }else if($group_account_manager = $this->group_account_managers_m->get_group_account_manager_by_user_id($this->group->id,$this->user->id)){
                            if($group_account_manager->active){
                                $this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->group->owner);
                            }else{
                                $this->session->set_flashdata('warning','Your '.$group->name.' group manager account has been suspended.');
                                // if(preg_match('/app\./',$this->application_settings->url)){
                                //     redirect($this->application_settings->protocol.$this->application_settings->url.'/checkin');
                                // }else{
                                //     redirect($this->application_settings->protocol.'app.'.$this->application_settings->url.'/checkin');
                                // }
                                redirect($this->application_settings->protocol.$this->application_settings->url.'/checkin');
                            }
                        }else{
                            redirect(site_url('member'));
                        }
                    }else{
                        if($group_account_manager = $this->group_account_managers_m->get_group_account_manager_by_user_id($this->group->id,$this->user->id)){
                            if($group_account_manager->active){
                                $this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->group->owner);
                            }else{
                                $this->session->set_flashdata('warning','Your '.$group->name.' group manager account has been suspended.');
                                // if(preg_match('/app\./',$this->application_settings->url)){
                                //     redirect($this->application_settings->protocol.$this->application_settings->url.'/checkin');
                                // }else{
                                //     redirect($this->application_settings->protocol.'app.'.$this->application_settings->url.'/checkin');
                                // }
                                redirect($this->application_settings->protocol.$this->application_settings->url.'/checkin');
                            }
                        }else{
                            // if(preg_match('/app\./',$this->application_settings->url)){
                            //     redirect($this->application_settings->protocol.$this->application_settings->url.'/checkin');
                            // }else{
                            //     redirect($this->application_settings->protocol.'app.'.$this->application_settings->url.'/checkin');
                            // }
                            redirect($this->application_settings->protocol.$this->application_settings->url.'/checkin');
                        }
                    }
                    if($this->group->group_setup_status){
                        
                    }else{
                        if(preg_match('/group\/setup_tasks/',current_url())){

                        }else{
                            redirect('group/setup_tasks/');
                        }
                    }
                }
            }else{
                // if(preg_match('/app\./',$this->application_settings->url)){
                //     redirect($this->application_settings->protocol.$this->application_settings->url.'/checkin');
                // }else{
                //     redirect($this->application_settings->protocol.'app.'.$this->application_settings->url.'/checkin');
                // }
                redirect($this->application_settings->protocol.$this->application_settings->url.'/checkin');
            }

        // }else{
            // if(preg_match('/app\./',$this->application_settings->url)){
            //     redirect($this->application_settings->protocol.$this->application_settings->url.'/checkin');
            // }else{
            //     redirect($this->application_settings->protocol.'app.'.$this->application_settings->url.'/checkin');
            // }
        //     redirect($this->application_settings->protocol.$this->application_settings->url.'/checkin');
        // }
        // $this->billing_settings->logged_in_group_checkin($this->group,$this->user,$slug);
        $this->login_action = $this->session->userdata('action');
        
        if(isset($this->login_action)){
            $this->login_action = $this->session->userdata('action');
        }else{
            $this->login_action='';
        }
        $unread_member_notifications_array = $this->notifications_m->get_unread_member_notifications_array();
        //$this->notifications->mark_member_notification_as_read($unread_member_notifications_array,uri_string(),$this->member->id);
        $this->completed_group_setup_tasks_count = $this->setup_tasks_m->count_complete_setup_tasks();
        $this->group_setup_tasks = $this->setup_tasks_m->get_group_setup_tasks();
        $this->unread_member_notifications = $this->notifications_m->get_unread_member_notifications(10);        
        $this->unread_member_notifications_count = $this->notifications_m->count_unread_member_notifications();
        $this->unread_member_emails = $this->emails_m->inbox_group_emails(10);
        $this->unread_member_emails_count = count($this->unread_member_emails);
        $this->group_member_options = $this->members_m->get_group_member_options();
        $this->group_debtor_options = $this->debtors_m->get_options();
        $this->active_group_debtor_options = $this->debtors_m->get_active_options();
        $this->active_group_member_options = $this->members_m->get_active_group_member_options();
        $this->active_group_membership_number_options = $this->members_m->get_active_group_membership_number_options();
        $this->active_group_member_options_by_user_id = $this->members_m->get_active_group_member_options_by_user_id();
        $this->group_user_options = $this->users_m->get_group_user_options($this->group->id);
        $this->group_type_options = $this->investment_groups->type_of_groups;
        $this->group_setup_tasks_count = count($this->group_setup_tasks);
        $this->incomplete_group_setup_tasks_count = $this->group_setup_tasks_count - $this->completed_group_setup_tasks_count;
        if($this->incomplete_group_setup_tasks_count < 0){
            $this->incomplete_group_setup_tasks_count = 0;
        }
        $this->group_currency = $this->currency_code_options[$this->group->currency_id];
        $this->group_calling_code = $this->countries_m->get_group_calling_code();
        $this->group_calling_code_options = $this->countries_m->get_country_code_options();
        $this->group_has_partner_bank_account = $this->bank_accounts_m->check_if_group_has_partner_bank_account();
        $this->group_unverified_bank_accounts = $this->bank_accounts_m->get_group_unverified_partner_bank_accounts();
        $this->group_unlinked_bank_accounts = $this->bank_accounts_m->get_group_unlinked_partner_bank_accounts();        
        $this->group_partner_bank_account_number_list = $this->bank_accounts_m->get_group_verified_bank_account_number_list();
        $this->group_partner_mobile_money_account_number_list = $this->mobile_money_accounts_m->get_group_verified_mobile_money_account_number_list();
        $this->unreconciled_deposits_count = $this->transaction_alerts_m->count_group_unreconciled_deposits($this->group_partner_bank_account_number_list, $this->group_partner_mobile_money_account_number_list);

        // echo $this->unreconciled_deposits_count;die;
        $this->unreconciled_withdrawals_count = $this->transaction_alerts_m->count_group_unreconciled_withdrawals($this->group_partner_bank_account_number_list, $this->group_partner_mobile_money_account_number_list);
        $this->unreconciled_deposits_count>0?$deposit_notification_count = 1:$deposit_notification_count = 0;
        $this->unreconciled_withdrawals_count>0?$withdrawal_notification_count = 1:$withdrawal_notification_count = 0;
        $this->pending_bank_account_tasks_count = count($this->group_unlinked_bank_accounts)+$withdrawal_notification_count + $deposit_notification_count;
        $this->total_unreconciled_deposits_and_withdrawals_count = $this->unreconciled_deposits_count+$this->unreconciled_withdrawals_count;
        $this->pending_withdrawal_approval_requests_count = $this->withdrawals_m->count_group_pending_withdrawal_approval_requests($this->group->id);
        $this->pending_withdrawal_approval_requests_count_and_reconcile = $this->pending_withdrawal_approval_requests_count+$this->total_unreconciled_deposits_and_withdrawals_count;
        $this->group_member_detail_options = $this->members_m->get_group_members();
        $this->withdrawal_tasks_count = $this->unreconciled_withdrawals_count;
        $this->group_membership_requests_count = $this->notifications_m->count_group_membership_notifications();
        $this->notifications_count = $this->unread_member_notifications_count+$this->unread_member_emails_count+$this->total_unreconciled_deposits_and_withdrawals_count+$this->pending_withdrawal_approval_requests_count+count($this->group_unlinked_bank_accounts);
        
        $this->group_role_options = $this->group_roles_m->get_group_role_options();
        $this->member_listing_order_by_options = array(
                'users.first_name' => 'Member First Name',
                'users.last_name' => 'Member Last Name',
                'members.created_on' => 'Registration Date',
                'members.date_of_birth' => 'Member\'s Date of Birth',
                'members.membership_number' => 'Membership Number',
            );

        $this->statement_sending_date_options = $this->investment_groups->statement_sending_date_options;

        $this->order_by_options = array(
                'ASC' => 'Smallest to Largest (A-Z / Youngest to Oldest / 1-100)',
                'DESC' => 'Largest to Smallest (Z-A / Oldest to Youngest / 100-1)',
            );
        $this->membership_numbers = $this->members_m->get_membership_number();
        if($this->group->member_listing_order_by == 'members.membership_number' && strlen(implode($this->membership_numbers))!=0)
        {
            $this->show_membership_number=TRUE;
        }else{
            $this->show_membership_number=FALSE;
        }
        
        $this->active_loan_applications = $this->loan_applications_m->count_group_active_loan_applications()?:0;
        $action = array(
            'group_id'=>$this->group->id,
            'action'=>isset($this->activity_log_options[uri_string()])?$this->activity_log_options[uri_string()]['action']:'',
            'description'=>isset($this->activity_log_options[uri_string()])?$this->activity_log_options[uri_string()]['description']:'',
            'user_id'=>$this->user->id,
            //'member_id'=>$this->member->id,
            'url'=>$_SERVER['REQUEST_URI']?:'',
            'request_method'=>$_SERVER['REQUEST_METHOD']?:'',
            'ip_address'=>$_SERVER['REMOTE_ADDR']?:'',
            'created_on'=>time(),
        );
        $this->activity_log->log_action($action);
        if($this->application_settings->enforce_group_setup_tasks){
            if($this->incomplete_group_setup_tasks_count>0){
                if($this->_check_group_setup_tasks()){
                    
                }else{
                    //$this->session->set_flashdata('info',"You need to add at least one contribution before you proceed. ");
                    // redirect('group/setup_tasks/contributions');
                }
            }
        }
    }

    function _check_group_setup_tasks(){
        $uri_string = $this->uri->uri_string();
        $access_exempt = array(
            'setup_tasks/accounts',
            'setup_tasks/members',
            'setup_tasks/contributions',
            'setup_tasks',
            'ajax',
            'activate',
            'change_email_address',
            'resend_activation_code',
            'change_phone_number'
        );
        foreach ($access_exempt as $key => $value){
             $access = explode('/', $value);
             if(preg_match('/'.$access[0].'/', $uri_string))
             {
                return TRUE;
             }
         }
        return FALSE;
    }
}