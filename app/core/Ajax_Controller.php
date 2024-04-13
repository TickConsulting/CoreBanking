<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Code here is run before ajax controllers
class Ajax_Controller extends Authentication_Controller{
    public $group;
    public $member;
    public $currency_options;
    public $currency_code_options;
    public $group_currency;
    public $group_calling_code;
    public $group_member_options;
    public $active_group_member_options;
    public $active_group_membership_number_options;
    public $membership_numbers;
    public $show_membership_number;
    public $active_group_member_options_by_user_id;
    public $group_member_detail_options;
    public $group_calling_code_options;
    public $member_role_holder_options;

    public function __construct(){
        parent::__construct();
        if($this->ion_auth->logged_in()){
            // if(!$this->ion_auth->is_in_group($this->user->id,2) && !$this->ion_auth->is_admin()&& !$this->ion_auth->is_bank_admin()){                  
            //     $this->session->set_flashdata('info', 'You dont have rights to access that panel.');
                     
                        
            if(!$this->ion_auth->is_admin($this->user->id) && !$this->ion_auth->is_bank_admin($this->user->id) && !$this->ion_auth->is_group_member($this->user->id) && !$this->ion_auth->is_group_account_manager($this->user->id)){
                $response = array(
                    'status' => 200,
                    'refer' => 'authentication',
                    'message' => 'You dont have rights to access that panel.',
                );
                echo json_encode($response);die;
            }
            // if(!$this->ion_auth->is_in_group($this->user->id,2) && !$this->ion_auth->is_in_group($this->user->id,1)&& !$this->ion_auth->is_in_group($this->user->id,3)){                  
            //     $this->session->set_flashdata('info', 'You dont have rights to access that panel.');
            //     $response = array(
            //         'status' => 200,
            //         'refer' => 'authentication',
            //         'message' => 'You dont have rights to access that panel.',
            //     );
            //     echo json_encode($response);die;
            // }
            $this->load->model('countries/countries_m');
            $this->load->model('notifications/notifications_m');
            $this->load->model('emails/emails_m');
            $this->load->model('debtors/debtors_m');
            $this->load->library('notifications');
            $this->load->library('billing_settings');
            $slug = '';
            $url_segments = explode(".",$_SERVER['HTTP_HOST']);
            $url_segments_dots = count($url_segments);
            // if($url_segments_dots>2){
                $slug = $url_segments[0];
                $group = array();
                $session_group_id = $this->session->userdata('group_id');
                // if($slug && !preg_match('/app/', $slug) && !preg_match('/uat/', $slug)){
                //     $current_url = current_url();
                //     $group = $this->groups_m->get_by_slug($slug);
                //     $this->session->set_userdata('group_id',$group->id);        
                // }else if($session_group_id){
                $group = $this->groups_m->get($session_group_id);
                // }
                if($group){
                    $this->group = $group;
                    $this->member_role_holder_options = $this->members_m->get_active_group_role_holder_options($this->group->id);
                   if($this->ion_auth->is_admin()||$this->ion_auth->is_bank_admin()||$this->ion_auth->is_group_account_manager()){
                        if($member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){

                        }else{
                            $member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->group->owner);
                        }
                        $this->member = $member; 
                    }else{
                        $member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id);
                        //print_r($member->group_role_id); die();
                       //  if(TRUE){
                       // // if($member->group_role_id){
                       //  }else{
                       //      if(TRUE){
                       //      //if($this->_group_member_allowed_routes()){
                       //          //$member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id);
                       //          if($member){
                       //              $this->member = $member; 
                       //          }else if($group_account_manager = $this->group_account_managers_m->get_group_account_manager_by_user_id($this->group->id,$this->user->id)){
                       //              if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->group->owner)){

                       //              }else{
                       //                  echo "Could not find member profile to assign to account manager.";
                       //                  die;
                       //              }
                       //          }else{
                       //              echo "Could not find member profile.";
                       //              die;
                       //          }
                       //      }else{
                       //          $response = array(
                       //              'status' => 0,
                       //              'refer' => site_url('authentication'),
                       //              'message' => 'You dont have rights to access this panel.',
                       //          );
                       //          echo json_encode($response);die;                            
                                
                       //      }
                       //  }
                        $this->member= $member;
                    }
                    $this->currency_options= $this->countries_m->get_currency_options();
                    $this->currency_code_options= $this->countries_m->get_currency_code_options();
                    $this->group_member_options = $this->members_m->get_group_member_options();
                    $this->group_member_detail_options = $this->members_m->get_group_members();
                    $this->membership_numbers = $this->members_m->get_membership_number();
                    $this->active_group_member_options_by_user_id = $this->members_m->get_active_group_member_options_by_user_id();
                    $this->group_currency = $this->currency_code_options[$this->group->currency_id];
                    $this->group_calling_code = $this->countries_m->get_group_calling_code();
                    $this->group_calling_code_options = $this->countries_m->get_country_code_options();
                    $this->active_group_member_options = $this->members_m->get_active_group_member_options();
                    $this->active_group_membership_number_options = $this->members_m->get_active_group_membership_number_options();
                    $this->group_debtor_options = $this->debtors_m->get_options();
                    $this->active_group_debtor_options = $this->debtors_m->get_active_options();
                    // $this->billing_settings->logged_in_group_checkin($this->group,$this->user,$slug,$this->member);
                    $action = array(
                        'group_id'=>$this->group->id,
                        'action'=>isset($this->activity_log_options[uri_string()])?$this->activity_log_options[uri_string()]['action']:'',
                        'description'=>isset($this->activity_log_options[uri_string()])?$this->activity_log_options[uri_string()]['description']:'',
                        'user_id'=>$this->user->id,
                        'member_id'=>$this->member->id??'',
                        'url'=>$_SERVER['REQUEST_URI']?:'',
                        'request_method'=>$_SERVER['REQUEST_METHOD']?:'',
                        'ip_address'=>$_SERVER['REMOTE_ADDR']?:'',
                        'created_on'=>time(),
                    );
                    $this->activity_log->log_action($action);
                    
                }else{

                    if($this->_group_check_setup_tasks()){

                    }else{
                        $response = array(
                            'status' => 0,
                            'refer' => 'Could not find Group profile',
                            'message' => '',
                        );
                        echo json_encode($response);die;
                    }
                }
            // }else{
            //     $session_group_id = $this->session->userdata('group_id');
            //     if($session_group_id){
            //         $this->group = $this->groups_m->get($session_group_id);
            //     }

            //     if($this->_group_check_setup_tasks()){

            //     }else{
            //         $response = array(
            //             'status' => 200,
            //             'refer' => 'authentication',
            //             'message' => '',
            //         );
            //         echo json_encode($response);die;
            //     }
                
            // }
        }else{
            if($this->_check_login()){
            }else{ 
                $response = array(
                    'status' => 200,
                    'refer' => 'authentication',
                    'message' => 'user already logged in',
                );
                echo json_encode($response);die;
            }
        }
        $admin_theme_name = 'groups';
        if (!defined('ADMIN_THEME')){
            define('ADMIN_THEME', $admin_theme_name);
        }
        // Prepare Asset library
        $this->asset->set_theme($admin_theme_name);
        $this->template->enable_parser(TRUE)
                ->set_theme($admin_theme_name)
                ->set_layout('member_default.html');
    }


    function _group_check_setup_tasks(){
        $uri_string = $this->uri->uri_string();
        $access_exempt = array(
            'ajax/create_group',
        );

        foreach ($access_exempt as $key => $value){
            $access = explode('/', $value);
            if(preg_match('/'.$access[0].'/', $uri_string)){
                return TRUE;
            }
         }
        return FALSE;
    }

    function _group_member_allowed_routes(){
        $uri_string = $this->uri->uri_string();
        $access_exempt = [ 
            'ajax/reports/get_member_deposit_distribution',
            'ajax/reports/get_member_deposit_summary',
            'ajax/wallets/get_member_wallet_deposits',
            'ajax/wallets/get_member_wallet_deposits',
            'ajax/reports/get_member_monthly_loan_repayments',
            'ajax/loan_types/get_loan_type_information1',
            'ajax/loan_types/get_loan_type_information',
            'ajax/loans/apply',
            'ajax/loans/loan_calculator',
            'ajax/loan_applications/get_pending_member_loan_applications',
            'ajax/loan_applications/get_declined_member_loan_applications',
            'ajax/loan_applications/get_member_approved_loan_applications_pending_disbursement',
            'ajax/loan_applications/get_disbursement_failed_member_loan_applications',
            'ajax/loan_applications/get_disbursed_member_loan_applications',
            'ajax/notifications/get_notifications_listing',
            'ajax/sms/get_member_received_sms_listing',
            'member/emails/app_app_inbox',
            'ajax/emails/count_mails',
            'ajax/emails/count_mails',
            'ajax/wallets/make_payment_validation',
            'ajax/wallets/calculate_convenience_charge',
            'ajax/members/edit_members',
            'ajax/users/change_password',
            'ajax/reports/get_expenses_summary',
            'ajax/reports/get_expenses_categories_summary',
            'ajax/reports/get_account_summary_graph',
            'ajax/withdrawals/get_pending_withdrawal_requests',
            'ajax/withdrawals/get_declined_withdrawal_requests',
            'ajax/withdrawals/get_disbursement_pending_withdrawal_requests',
            'ajax/withdrawals/get_disbursed_withdrawal_requests',
            'ajax/withdrawals/get_disbursement_failed_withdrawal_requests',
            'ajax/withdrawals/get_withdrawal_request',
            'ajax/withdrawals/send_approval_code',
            'ajax/withdrawals/verify_approval_code',
            'ajax/withdrawals/approve_withdrawal_request'
        ];
        $explode_uri = explode('/', $uri_string);
        foreach ($access_exempt as $key => $value){
            $access = explode('/', $value);
            if(preg_match('/'.$access[2].'/', $uri_string)){
                return TRUE;
            }
         }
        return FALSE;
    }
}