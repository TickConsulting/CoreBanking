<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Code here is run before admin controllers
class Member_Controller extends Authentication_Controller{
    public $group;
    public $member;
    public $theme;
    public $currency_options;
    public $currency_code_options;
    public $group_currency;
    public $group_calling_code;
    public $unread_member_emails_array;
    public $unread_member_email_count;
    public $group_member_options;
    public $active_group_member_options;
    public $show_membership_number;
    public $unread_member_notifications_count;
    public $unread_member_notifications;
    public $unread_member_emails_count;
    public $unread_member_emails;
    public $pending_loan_applications;
    public $pending_withdrawal_approval_requests_count;

    public function __construct(){
        parent::__construct();
        // if(!$this->ion_auth->is_in_group($this->user->id,2) && !$this->ion_auth->is_in_group($this->user->id,1))
        // {                      
        //     $this->session->set_flashdata('info', 'You dont have rights to access that panel.');
        //     redirect('authentication');
        // }
        if(!$this->ion_auth->is_admin($this->user->id) && !$this->ion_auth->is_group_member($this->user->id) && !$this->ion_auth->is_group_account_manager($this->user->id) && !!$this->ion_auth->is_bank_admin($this->user->id)){
            $this->session->set_flashdata('info', 'You dont have rights to access that panel.');
            redirect('authentication');
        }
        $admin_theme_name = 'groups';
        if (!defined('ADMIN_THEME')){
            define('ADMIN_THEME', $admin_theme_name);
        }
        // Prepare Asset library
        // $this->asset->set_theme($admin_theme_name);
        // $this->template->enable_parser(TRUE)->set_theme($admin_theme_name)->set_layout('member_default.html');
        $this->load->model('countries/countries_m');
        $this->load->model('notifications/notifications_m');
        $this->load->model('emails/emails_m');
        $this->load->library('notifications');
        $this->load->library('billing_settings');
        $slug = '';
        $url_segments = explode(".",$_SERVER['HTTP_HOST']);
        $url_segments_dots = count($url_segments);
        // if($url_segments_dots>2){
            $slug = $url_segments[0];
            $group = array();
            $slug = $url_segments[0];
            $session_group_id = $this->session->userdata('group_id');
            // if($slug && !preg_match('/app/', $slug) && !preg_match('/uat/', $slug)){
            //     $current_url = current_url();
            //     $group = $this->groups_m->get_by_slug($slug);
            //     $this->session->set_userdata('group_id',$group->id);
            //     //$new_url = str_replace($slug,'app', $current_url);
            //     $new_url = str_replace($slug, $current_url);
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
                 if($this->ion_auth->is_admin()||$this->ion_auth->is_bank_admin()||$this->ion_auth->is_group_account_manager()){
                    $member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)?:$this->members_m->get_group_member_by_user_id($this->group->id,$this->group->owner);
                }else{
                    $member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id);
                }
                if($member){
                    $this->member = $member; 
                }else{
                    // if(preg_match('/app\./',$this->application_settings->url)){
                    //     redirect($this->application_settings->protocol.$this->application_settings->url.'/checkin');
                    // }else{
                    //     redirect($this->application_settings->protocol.'app.'.$this->application_settings->url.'/checkin');
                    // }
                    redirect($this->application_settings->protocol.$this->application_settings->url.'/checkin');
                }
            }else{
                // if(preg_match('/app\./',$this->application_settings->url)){
                //     redirect($this->application_settings->protocol.$this->application_settings->url);
                // }else{
                //     redirect($this->application_settings->protocol.'app.'.$this->application_settings->url);
                // }
                redirect($this->application_settings->protocol.$this->application_settings->url);
            }
        // }else{
        //     // if(preg_match('/app\./',$this->application_settings->url)){
        //     //     redirect($this->application_settings->protocol.$this->application_settings->url);
        //     // }else{
        //     //     redirect($this->application_settings->protocol.'app.'.$this->application_settings->url);
        //     // }
        //     redirect($this->application_settings->protocol.$this->application_settings->url);
        // }
        $unread_member_notifications_array = $this->notifications_m->get_unread_member_notifications_array();
        $this->notifications->mark_member_notification_as_read($unread_member_notifications_array,uri_string(),$this->member->id);
        $this->currency_options= $this->countries_m->get_currency_options();
        $this->currency_code_options= $this->countries_m->get_currency_code_options();
        $this->unread_member_notifications = $this->notifications_m->get_unread_member_notifications(10);
        $this->unread_member_notifications_count = $this->notifications_m->count_unread_member_notifications();
        $this->unread_member_emails = $this->emails_m->inbox_group_emails(10);
        $this->unread_member_emails_count = count($this->unread_member_emails);
        $this->group_member_options = $this->members_m->get_group_member_options();
        $this->group_currency = $this->currency_code_options[$this->group->currency_id];
        $this->group_calling_code = $this->countries_m->get_group_calling_code();
        $this->active_group_member_options = $this->members_m->get_active_group_member_options();
        $this->billing_settings->logged_in_group_checkin($this->group,$this->user,$slug,$this->member);
        $this->notifications_count = $this->unread_member_notifications_count +$this->unread_member_emails_count;
        $this->currency_options= $this->countries_m->get_currency_options();
        $this->currency_code_options= $this->countries_m->get_currency_code_options();
        $this->country_code_options= $this->countries_m->get_country_code_options();
        $this->country_options= $this->countries_m->get_country_options();
        $this->languages = $this->languages_m->get_all();
        $this->pending_loan_applications = $this->loan_applications_m->count_all_pending_member_loan_applications();
        $this->pending_withdrawal_approval_requests_count = $this->withdrawals_m->count_group_member_pending_withdrawal_approval_requests($this->member->id);
        $action = array(
            'group_id'=>$this->group->id,
            'action'=>isset($this->activity_log_options[uri_string()])?$this->activity_log_options[uri_string()]['action']:'',
            'description'=>isset($this->activity_log_options[uri_string()])?$this->activity_log_options[uri_string()]['description']:'',
            'user_id'=>$this->user->id,
            'member_id'=>$this->member->id,
            'url'=>$_SERVER['REQUEST_URI']?:'',
            'request_method'=>$_SERVER['REQUEST_METHOD']?:'',
            'ip_address'=>$_SERVER['REMOTE_ADDR']?:'',
            'created_on'=>time(),
        );
        $this->activity_log->log_action($action);

        $admin_theme_name = 'groups';
        if (!defined('ADMIN_THEME'))
        {
            define('ADMIN_THEME', $admin_theme_name);
        }
        // Prepare Asset library
        $this->asset->set_theme($admin_theme_name);
                $this->template->enable_parser(TRUE)
                ->set_theme($admin_theme_name)
                ->set_layout('member_default.html');
    }
}