<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Code here is run before bank controllers
class Bank_Controller extends CI_Controller
{
    public $user;
    public $application_settings;
    public $current_user_groups;
    public $currency_options;
    public $default_country;
    public $currency_code_options;
    public $partner_banks;
    public $development_remote_ips;
    public $group;
    public $unread_notifications_count;
    public $unread_notifications;
    public $notifications_count;
    public $default_country_code;
    public $unread_emails_count;
    public $unread_emails;
    public $languages;
    public $user_groups;
    public $member_role_permissions;
    public $unreconciled_deposits_count;
    public $unreconciled_withdrawals_count;
    public $total_unreconciled_deposits_and_withdrawals_count;
    public $active_loan_applications;
    public $pending_withdrawal_approval_requests_count;
    public $withdrawal_tasks_count;
    public $active_group_member_options;
    public $group_currency;

    public $selected_language_name;
    public $selected_language_id = '1';


    public $current_country;





    public function __construct(){
        parent::__construct();
        $this->load->library('ion_auth');
        $this->load->library('user_agent');
        $this->load->library('investment_groups');
        $this->load->model('sms/sms_m');
        $this->load->model('banks/banks_m');
        $this->load->model('settings/settings_m');
        $this->load->model('countries/countries_m');
        $this->load->model('emails/emails_m');
        $this->load->model('bank_menus/bank_menus_m');
        $this->load->model('admin_quick_action_menus/admin_quick_action_menus_m');
        $this->load->model('languages/languages_m');
        $this->development_remote_ips = array(
            '197.237.131.21',
            '197.237.27.18',
            '197.237.134.37',
            '197.232.248.54'
        );
        $this->application_settings = $this->settings_m->get_settings()?:''; 
        $this->partner_banks = $this->banks_m->get_partner_banks();
        $this->currency_options= $this->countries_m->get_currency_options();
        $this->currency_code_options= $this->countries_m->get_currency_code_options();
        $this->country_code_options= $this->countries_m->get_country_code_options();

        $this->country_options= $this->countries_m->get_country_options();
        $this->default_country_code = COUNTRY_CODE;
        $this->languages = $this->languages_m->get_all();
        $this->session->set_userdata('active_checkin','bank');
        $this->group = new stdClass();
        if (!$this->_check_login()){
            if($this->ion_auth->logged_in()){
                $this->user = $this->ion_auth->get_user();
                if($this->user->language_id){
                        $language_id = $this->user->language_id;
                    }else{
                        $language_id = $this->application_settings->default_language_id;
                    }
                    foreach($this->languages as $language){
                        if($language->id == $language_id){
                            $this->selected_language_name = $language->name;
                            $this->selected_language_id = $language->country_id;
                        }
                    }
                    if($language = $this->languages_m->get($language_id)){
                        $this->lang->load('application',$language->short_code);
                    }else{
                        if(isset($_COOKIE['language_id']) && $_COOKIE['language_id']){
                            $language_id = $_COOKIE['language_id'];
                        }else{
                            $language_id = $this->application_settings->default_language_id;
                        }
                    }
                $this->session->set_flashdata('success', 'Successfully logged in.');
                if(!$this->ion_auth->is_bank_admin() && !$this->ion_auth->is_admin())
                {
                    redirect('checkin');
                    return FALSE;
                }else
                {
                    redirect('bank');
                }
            }else{
                //$this->session->set_flashdata('error', 'You Need Admin Rights to Access the Control Panel');
                 redirect('login?refer='.urlencode($_SERVER['REQUEST_URI']));   
            }
        }else{
            if($this->ion_auth->logged_in()){
                $this->user = $this->ion_auth->get_user();
                if(!$this->ion_auth->is_bank_admin() && !$this->ion_auth->is_admin())
                {
                    redirect('');
                    return FALSE;
                }
                $this->user = $this->ion_auth->get_user();
                if($this->user){
                    $this->current_user_groups = $this->investment_groups->current_user_groups($this->user->id);
                    if($this->user->language_id){
                        $language_id = $this->user->language_id;
                    }else{
                        $language_id = $this->application_settings->default_language_id;
                    }
                    foreach($this->languages as $language){
                        if($language->id == $language_id){
                            $this->selected_language_name = $language->name;
                            $this->selected_language_id = $language->country_id;
                        }
                    }
                    if($language = $this->languages_m->get($language_id)){
                        $this->lang->load('application',$language->short_code);
                    }else{
                        if(isset($_COOKIE['language_id']) && $_COOKIE['language_id']){
                            $language_id = $_COOKIE['language_id'];
                        }else{
                            $language_id = $this->application_settings->default_language_id;
                        }
                    }
                }else{
                    $this->ion_auth->logout();
                    unset($_SESSION);
                }
                
            }
        }

        $this->default_country = $this->countries_m->get_default_country(); 
        if(defined('COUNTRY_CODE') && defined('CALLING_CODE')){
            $this->current_country = $this->countries_m->get_country_by_code(COUNTRY_CODE);
        }else{
            if($this->default_country){
                if($this->default_country->calling_code){
                    define('CALLING_CODE',$this->default_country->calling_code);
                    define('COUNTRY_CODE',$this->default_country->code);
                }else{
                    define('CALLING_CODE',"254");
                    define('COUNTRY_CODE','KE');
                }
            }else{
                define('CALLING_CODE',"254");
                define('COUNTRY_CODE','KE');
            }
        } 

        if (!defined('ADMIN_THEME')){
            define('ADMIN_THEME', 'bank');
        }
        // if(!preg_match('/app\./', current_url())){
        //     if(preg_match('/app\./',$this->application_settings->url)){
        //         redirect($this->application_settings->protocol.$this->application_settings->url.'/bank');
        //     }else{
        //         redirect($this->application_settings->protocol.'app.'.$this->application_settings->url.'/bank');
        //     }
        // }

        $session_group_id = $this->session->userdata('group_id');
        if($session_group_id){
            $this->group = $this->groups_m->get($session_group_id);
        }
        // Prepare Asset library
        $this->asset->set_theme('bank');
                $this->template->enable_parser(TRUE)
                ->set_theme('bank')
                ->set_layout('default.html');
    }

    function _check_login(){
        $uri_string = $this->uri->uri_string();
        $access_exempt = array(
            'bank/login',
            'bank/logout',
            'bank/forgot_password',
            'bank/reset_password',
            'bank/confirm_code'
        );

        foreach ($access_exempt as $key => $value) 
         {
             $access = explode('/', $value);
             if(preg_match('/'.$access[0].'\/'.$access[1].'/', $uri_string))
             {
                return TRUE;
             }
         }
         
        if(!$this->ion_auth->logged_in()){      
            return FALSE;
        }
        return TRUE;
    }
}