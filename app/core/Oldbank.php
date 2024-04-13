<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Code here is run before admin controllers
class Oldbank_Controller extends CI_Controller
{
    public $user;
    public $bank;
    public $chamasoft_settings;
    public $current_user_groups;
    public $currency_options;
    public $default_country;
    public $currency_code_options;
    public $partner_banks;

    public function __construct()
    {
        parent::__construct();
        // $this->load->library('ion_auth');
        // $this->load->library('user_agent');
        $this->load->library('investment_groups');
        // $this->load->model('sms/sms_m');
        // $this->load->model('banks/banks_m');
        // $this->load->model('settings/settings_m');
        // $this->load->model('countries/countries_m');
        // $this->load->model('emails/emails_m');
        $this->load->model('bank_menus/bank_menus_m');
        // $this->load->model('admin_menus/admin_menus_m');
        // $this->load->model('admin_quick_action_menus/admin_quick_action_menus_m');
        $this->chamasoft_settings = $this->session->userdata('chamasoft_settings');
        $this->partner_banks = $this->banks_m->get_partner_banks();
        $this->currency_options= $this->countries_m->get_currency_options();
        $this->currency_code_options= $this->countries_m->get_currency_code_options();
        $this->default_country = $this->countries_m->get_default_country();  
        if (!$this->_check_login()){
            if($this->ion_auth->logged_in()){
                $this->user = $this->ion_auth->get_user();
                $this->session->set_flashdata('success', 'Successfully logged in.');
                $this->current_user_groups =$this->investment_groups->current_user_groups($this->user->id);
                if(!$this->ion_auth->is_admin()&&!$this->ion_auth->is_in_group(4)){
                    redirect('checkin');
                    return FALSE;
                }else{
                    redirect('admin');
                }
            }else{
                //$this->session->set_flashdata('error', 'You Need Admin Rights to Access the Control Panel');
                 redirect('login?refer='.urlencode($_SERVER['REQUEST_URI']));   
            }
        }else{
            if($this->ion_auth->logged_in()){
                $this->user = $this->ion_auth->get_user();
                if(!$this->ion_auth->is_admin()&&!$this->ion_auth->is_in_group($this->user->id,4)){
                    redirect('checkin');
                    return FALSE;
                }
                if($this->user){
                    $this->current_user_groups =$this->investment_groups->current_user_groups($this->user->id);
                    $bank_id = $this->session->userdata('bank_id');
                    $bank = $this->banks_m->get($bank_id);
                    if($bank){
                        if($this->banks_m->check_user_bank_pairing($this->user->id,$bank->id)||$this->ion_auth->is_admin()){
                            $this->bank = $bank;
                        }else{
                            $this->session->set_flashdata('warning','You do not have permissions to access the panel');
                            redirect('checkin');
                        }
                    }else{
                        if($this->_check_bank_id()){
                            
                        }else{
                            redirect('checkin');
                        }
                    }
                }else{
                    $this->ion_auth->logout();
                    unset($_SESSION);
                }
                
            }
        }

        $admin_theme_name = 'admin_themes/admin';
        if (!defined('ADMIN_THEME'))
        {
            define('ADMIN_THEME', $admin_theme_name);
        }
        // Prepare Asset library
        $this->asset->set_theme($admin_theme_name);
                $this->template->enable_parser(TRUE)
                ->set_theme($admin_theme_name)
                ->set_layout('bank_default.html');
    }

    function _check_bank_id(){
        $uri_string = $this->uri->uri_string();
        $access_exempt = array(
            'checkin'
        );

        foreach ($access_exempt as $key => $value){
             $access = explode('/', $value);
             if(preg_match('/'.$access[0].'/', $uri_string))
             {
                return TRUE;
             }
         }
         
        if(!$this->session->userdata('bank_id')){      
            return FALSE;
        }
        return TRUE;
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