<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Code here is run before admin controllers
class Admin_Controller extends Authentication_Controller
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

    public function __construct(){
        parent::__construct();
        $this->group = '';
        $this->load->library('ion_auth');
        $this->load->library('user_agent');
        $this->load->library('investment_groups');
        $this->load->model('sms/sms_m');
        $this->load->model('banks/banks_m');
        $this->load->model('settings/settings_m');
        $this->load->model('countries/countries_m');
        $this->load->model('emails/emails_m');
        $this->load->model('admin_menus/admin_menus_m');
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
        $this->default_country = $this->countries_m->get_default_country();  
        $this->session->set_userdata('active_checkin','admin');
        if (!$this->_check_login()){
            if($this->ion_auth->logged_in()){
                $this->user = $this->ion_auth->get_user();
                $this->session->set_flashdata('success', 'Successfully logged in.');
                if(!$this->ion_auth->is_admin())
                {
                    redirect('group');
                    return FALSE;
                }else
                {
                    redirect('admin');
                }
            }else{
                //$this->session->set_flashdata('error', 'You Need Admin Rights to Access the Control Panel');
                 redirect('login?refer='.urlencode($_SERVER['REQUEST_URI']));   
            }
        }else{
            if($this->ion_auth->logged_in()){
                if(!$this->ion_auth->is_admin())
                {
                    redirect('');
                    return FALSE;
                }
                $this->user = $this->ion_auth->get_user();
                if($this->user){
                    $this->current_user_groups = $this->investment_groups->current_user_groups($this->user->id);
                }else{
                    $this->ion_auth->logout();
                    unset($_SESSION);
                }
                
            }
        }
        $admin_theme_name = 'admin';
        if (!defined('ADMIN_THEME'))
        {
            define('ADMIN_THEME', $admin_theme_name);
        }
        
        // if(!preg_match('/app\./', current_url())){
        //     if(preg_match('/app\./',$this->application_settings->url)){
        //         redirect($this->application_settings->protocol.$this->application_settings->url.'/admin');
        //     }else{
        //        redirect($this->application_settings->protocol.'app.'.$this->application_settings->url.'/admin');
        //     }
        // }
        // Prepare Asset library
        $this->asset->set_theme($admin_theme_name);
                $this->template->enable_parser(TRUE)
                ->set_theme($admin_theme_name)
                ->set_layout('default.html');
    }

    function _check_login(){
        $uri_string = $this->uri->uri_string();
        $access_exempt = array(
            'admin/login',
            'admin/logout',
            'admin/forgot_password',
            'admin/reset_password',
            'admin/confirm_code'
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