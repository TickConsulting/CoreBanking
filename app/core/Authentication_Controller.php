<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Code here is run before admin controllers
class Authentication_Controller extends CI_Controller
{
    public $user;
    public $application_settings;
    public $activity_log_options = array();
    public $default_country;
    public $current_country;
    public $current_user_groups;
    public $default_theme;
    public $selected_language_name;
    public $selected_language_id = '1';
    public $user_groups;
    public $default_country_code;
    public $group;
    public $access_exempt = array(
        'login',
        'logout',
        'forgot_password',
        'reset_password',
        'confirm_code',
        'signup',
        'join',
        'change_language'
    );
    public function __construct(){
        parent::__construct();
        // die("number 1");
        $this->load->library('ion_auth');
        $this->load->library('investment_groups');
        $this->load->library('user_agent');
        $this->load->library('themes');
        $this->load->model('sms/sms_m');
        $this->load->model('settings/settings_m');
        $this->load->model('setup_tasks/setup_tasks_m');
        $this->load->model('emails/emails_m');
        $this->load->model('menus/menus_m');
        $this->load->model('member_menus/member_menus_m');
        $this->load->model('permissions/permissions_m');
        $this->load->model('settings_menus/settings_menus_m');
        $this->load->model('member_settings_menus/member_settings_menus_m');
        $this->load->model('countries/countries_m');
        $this->load->model('quick_action_menus/quick_action_menus_m');
        $this->load->model('languages/languages_m');
        $this->languages = $this->languages_m->get_all();
        $this->currency_options= $this->countries_m->get_currency_options();
        $this->currency_code_options= $this->countries_m->get_currency_code_options();
        $this->country_code_options= $this->countries_m->get_country_code_options();
        $this->country_options= $this->countries_m->get_country_options();
        $this->default_country_code = COUNTRY_CODE;//ip_meta("Visitor", "Country Code");
        switch (ENVIRONMENT) {
            case 'maintenance':
                redirect($this->application_settings->protocol.$this->application_settings->url);
                die;
                break;
            default:
                # code...
                break;
        }


        $this->activity_log_options = array(
            '' => array(
                'description' => 'View Group Dashboard',
                'action' => 'Read',
            ),
            '/group' => array(
                'description' => 'View Group Dashboard',
                'action' => 'Read',
            ),
            'login' => array(
                'description' => 'Log into Chamasoft',
                'action' => 'Read',
                ),
            'logout' => array(
                'description' => 'Logout of Chamasoft',
                'action' => 'Read',
                ),
            'checkin' => array(
                'description' => 'Check into Chamasoft',
                'action' => 'Read',
                ),
        ); 
        $this->application_settings = $this->settings_m->get_settings()?:'';
        if($this->application_settings->session_length){
            $this->config->set_item('sess_expiration',$this->application_settings->session_length);
        }else{
            $this->config->set_item('sess_expiration',86400);
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
        $ajax_file = FALSE;
        $refer = $_SERVER['REQUEST_URI'];
        $refers = explode('/', $refer);
        if(in_array('ajax', $refers)){
            $ajax_file = TRUE;
        }
        if (!$this->_check_login()){
            if($this->ion_auth->logged_in()){
                $this->user = $this->ion_auth->get_user();
                $this->session->set_flashdata('success', 'Successfully logged in.');
                if(!preg_match('/logout/', $uri_string)){
                    if(($this->user->is_validated == 2 ||  !$this->user->login_validated)&& (!preg_match('/verify_otp/', $uri_string) && !preg_match('/resend_otp/', $uri_string))){
                        if($this->application_settings->enable_two_factor_auth){
                            if($ajax_file){
                                $response = array(
                                    'status' => '202',
                                    'message' => 'Account session expired. Login to proceed',
                                    'refer' => $this->application_settings->protocol.''.$this->application_settings->url.'/verify_otp',
                                );
                                echo json_encode($response);die;
                            }
                            redirect('verify_otp');
                        }
                    }else{
                        if($this->user->prompt_to_change_password == 1 && !preg_match('/set_new_password/', $uri_string) && !preg_match('/verify_otp/', $uri_string) && !preg_match('/resend_otp/', $uri_string)){
                            if($ajax_file){
                                $response = array(
                                    'status' => '202',
                                    'message' => 'Account session expired. Login to proceed',
                                    'refer' => $this->application_settings->protocol.''.$this->application_settings->url.'/set_new_password',
                                );
                                echo json_encode($response);die;
                            }
                            redirect('set_new_password');
                        }
                    }
                }
                redirect('');
            }else{
                if($ajax_file){
                    $response = array(
                        'status' => '202',
                        'message' => 'Account session expired. Login to proceed',
                        'refer' => $this->application_settings->protocol.''.$this->application_settings->url.'/login',
                    );
                    echo json_encode($response);die;
                }else{
                   $url = $this->application_settings->protocol.''.$this->application_settings->url.'/login?refer='.urlencode($_SERVER['REQUEST_URI']); 
                    redirect($url,'refresh'); 
                }                
            }
        }else{
            if($this->ion_auth->logged_in()){
                $uri_string = $this->uri->uri_string();
                $strings = explode('/', $uri_string);
                // if(isset($strings[1]) && in_array($strings[1], $this->access_exempt)){
                //     $response = array(
                //         'status' => 200,
                //         'refer' => 'checkin',
                //         'message' => '',
                //     );
                //     echo json_encode($response);die;
                // }
                $this->user = $this->ion_auth->get_user();
                if($this->user){
                    if(!preg_match('/logout/', $uri_string)){
                        if(($this->user->is_validated == 2 ||  !$this->user->login_validated)&& (!preg_match('/verify_otp/', $uri_string) && !preg_match('/resend_otp/', $uri_string))){
                            if($this->application_settings->enable_two_factor_auth){
                                if($ajax_file){
                                    $response = array(
                                        'status' => '202',
                                        'message' => 'Account session expired. Login to proceed',
                                        'refer' => $this->application_settings->protocol.''.$this->application_settings->url.'/verify_otp',
                                    );
                                    echo json_encode($response);die;
                                }
                                redirect('verify_otp');
                            }
                        }else{
                            if($this->user->prompt_to_change_password == 1 && !preg_match('/set_new_password/', $uri_string) && !preg_match('/verify_otp/', $uri_string) && !preg_match('/resend_otp/', $uri_string)){
                                if($ajax_file){
                                    $response = array(
                                        'status' => '202',
                                        'message' => 'Account session expired. Login to proceed',
                                        'refer' => $this->application_settings->protocol.''.$this->application_settings->url.'/set_new_password',
                                    );
                                    echo json_encode($response);die;
                                }
                                redirect('set_new_password');
                            }
                        }
                    }
                    $this->current_user_groups =$this->investment_groups->current_user_groups($this->user->id);
                    $this->user_groups = $this->groups_m->count_current_user_groups($this->user->id);
                    if($this->user->language_id){
                        $language_id = $this->user->language_id;
                    }else{
                        $language_id = isset($this->application_settings->default_language_id)?$this->application_settings->default_language_id:1;
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
                            $language_id = isset($this->application_settings->default_language_id)?$this->application_settings->default_language_id:1;
                        }
                    }
                }else{
                    $this->ion_auth->logout();
                    unset($_SESSION);
                }
            }else{
                if(isset($_COOKIE['language_id']) && $_COOKIE['language_id']){
                    $language_id = $_COOKIE['language_id'];
                }else{
                    $language_id = isset($this->application_settings->default_language_id)?$this->application_settings->default_language_id:1;
                }
                foreach($this->languages as $language){
                    if($language->id == $language_id){
                        $this->selected_language_name = $language->name;
                        $this->selected_language_id = $language->country_id;
                    }
                }
                if($language = $this->languages_m->get($language_id)){
                    //die($language->short_code);
                    $this->lang->load('application',$language->short_code);
                }else{
                    // Do nothing 
                }
            }
            if($language = $this->languages_m->get($language_id)){
                $this->lang->load('application',$language->short_code);
            }else{
            }
        }
        $admin_theme_name = 'groups';
        // if(!preg_match('/app\./', current_url())){
        //     $first_segment =  $this->uri->segment(1);
        //     if(in_array($first_segment, $this->access_exempt)){
        //         // if(preg_match('/app\./',$this->application_settings->url)){
        //         //     redirect($this->application_settings->protocol.$this->application_settings->url.'/'.$first_segment);
        //         // }else{
        //         //     redirect($this->application_settings->protocol.'app.'.$this->application_settings->url.'/'.$first_segment);
        //         // }
        //         redirect($this->application_settings->protocol.$this->application_settings->url.'/'.$first_segment);
        //     }
        // }
        if (!defined('ADMIN_THEME'))
        {
            define('ADMIN_THEME', $admin_theme_name);
        }
        // Prepare Asset library
        $this->asset->set_theme($admin_theme_name);
                $this->template->enable_parser(TRUE)
                ->set_theme($admin_theme_name)
                ->set_layout('default.html');
    }


    function _check_login(){
        $uri_string = $this->uri->uri_string();
        foreach ($this->access_exempt as $key => $value){
            $access = explode('/', $value);
            if(preg_match('/'.$access[0].'/', $uri_string)){
                return TRUE;
            }
        }
        if(!$this->ion_auth->logged_in()){      
            return FALSE;
        }
        return TRUE;
    }

    function __remove_url_for_this_uris(){
        $uri_string = $this->uri->uri_string();
        $access_exempt = array(
                            'forgot_password',
                            'reset_password',
                            'confirm_code',
                            'signup',
                            'join',
                        );

        foreach ($access_exempt as $key => $value) 
         {
            $access = explode('/', $value);
            if(preg_match('/'.$access[0].'/', $uri_string)){
                remove_subdomain_from_url($this->application_settings->protocol.''.$this->application_settings->url); 
            }
         }
    }
    
}