<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Code here is run before admin controllers
class Mobile_Controller extends CI_Controller{
    protected $chamasoft_settings;
    protected $request = array();
    protected $request_headers;
    protected $token_key = "d8ng63ttyjp88cnjpkme65efgz6b2gwg";
    protected $token = '';
    protected $information_key = "p4dza3gxvn3kw36a";
    protected $ignore_secret_key = FALSE;
    public $version_code = 0;
    protected $user_id = 0;
    protected $default_country;
    protected $require_user_id = FALSE;
    public $member_listing_order_by_options;
    public $order_by_options;
    public $beta;
    public $secret_key;
    public $group_currency;
    
    public function __construct(){
        parent::__construct();
        $this->load->model('sms/sms_m');
        $this->load->model('countries/countries_m');
        $this->load->model('emails/emails_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('sacco_branches/sacco_branches_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->model('transaction_statements/transaction_statements_m');
        $this->load->model('mobile_m');
        $this->load->model('settings/settings_m');
        $this->load->model('accounts/accounts_m');
        $this->load->library('files_uploader');
        $this->load->library('member_notifications');
        $this->load->library('investment_groups');
        $this->load->model('members/members_m');
        $this->load->helper('string');
        $this->beta = FALSE;
        if(preg_match('/uat\.chamasoft\.com/', $_SERVER['HTTP_HOST']) || preg_match('/eazzychama\.local/', $_SERVER['HTTP_HOST']) || preg_match('/eazzykikundidemo\.com/', $_SERVER['HTTP_HOST'])){
            $this->beta = TRUE;
        }
        $this->request_headers = apache_request_headers();
        if(isset($_REQUEST)){
            if (!file_exists('logs')) {
                mkdir('logs', 0777, true);
            }
            file_put_contents("logs/app_log_user.dat","\n".date("d-M-Y h:i A")."\t".current_url()."\t".serialize($_REQUEST)."\t".serialize($_GET)."\t".serialize(file_get_contents('php://input'))."\t Headers are:".serialize(apache_request_headers()). "\n",FILE_APPEND);
        }
        $this->chamasoft_settings = $this->settings_m->get_settings()?:'';
        $this->default_country = $this->countries_m->get_default_country();
        $this->currency_code_options= $this->countries_m->get_currency_code_options();
        $this->member_listing_order_by_options = array(
                'users.first_name' => 'Member First Name',
                'users.last_name' => 'Member Last Name',
                'members.created_on' => 'Registration Date',
                'members.date_of_birth' => 'Member\'s Date of Birth',
                'members.membership_number' => 'Membership Number',
            );
        $this->order_by_options = array(
                'ASC' => 'Smallest to Largest (A-Z / Youngest to Oldest / 1-100)',
                'DESC' => 'Largest to Smallest (Z-A / Oldest to Youngest / 100-1)',
            );
        header('Content-Type: application/json');
        if($this->token = $this->_verify_authentication()){
            if($this->secret_key = $this->_get_secret_key()){
                $this->version_code = $this->_get_version_code()?:0;
                if($this->version_code <=73){
                    echo json_encode(array(
                        'status' => 0,
                        'message' => 'You are using an outdated application. Update to the latest application',
                    ));die;
                }
                if($this->version_code>=77){
                    define('NEWAPPLICATION',1);
                }
                $arr = array(
                    'information_key'=>$this->information_key,
                    'token'=>$this->token,
                    'secret_key' => $this->secret_key,
                    'version_code' => $this->version_code,
                );
                
                $file = file_get_contents('php://input');
                if($file){
                    if($this->token == 'abcdefghhgfedcba' || preg_match('/127\.0\.0\.1/', $_SERVER['REMOTE_ADDR'])||$this->version_code ==75){
                        //do nothing
                        $this->load->library('Encryptdecrypt',$arr);
                        if(!defined('LOCALENVIRONMENT')){
                            define('LOCALENVIRONMENT',1);
                        }
                        //$file = $this->encryptdecrypt->decryptPrivate($file);
                    }else{
                        $this->load->library('Encryptdecrypt',$arr);
                        $file = $this->encryptdecrypt->decryptPrivate($file);
                    }
                    $request = json_decode($file);
                    if($request){
                        $host_user_id = isset($request->user_id)?$request->user_id:0;
                        $host_group_id = isset($request->group_id)?$request->group_id:0;
                        $request_id = isset($request->request_id)?$request->request_id:0;
                        if($host_group_id){
                            //there is need to log actions to know what members are doing that are affecting thr system
                            $activity_log_options = array(
                                '' => array(
                                    'description' => 'View Group Dashboard via Mobile App',
                                    'action' => 'Read',
                                ),
                                '/group' => array(
                                    'description' => 'View Group Dashboard via Mobile App',
                                    'action' => 'Read',
                                ),
                                'login' => array(
                                    'description' => 'Log into Chamasoft via Mobile App',
                                    'action' => 'Read',
                                    ),
                                'logout' => array(
                                    'description' => 'Logout of Chamasoft via Mobile App',
                                    'action' => 'Read',
                                    ),
                                'checkin' => array(
                                    'description' => 'Check into Chamasoft via Mobile App',
                                    'action' => 'Read',
                                    ),
                            ); 
                            $action = array(
                                'group_id'=>$host_group_id,
                                'action'=>isset($activity_log_options[uri_string()])?$activity_log_options[uri_string()]['action']:'',
                                'description'=>isset($activity_log_options[uri_string()])?$activity_log_options[uri_string()]['description'].' Via Mobile App':'Via Mobile App',
                                'user_id'=>$host_user_id,
                                'member_id'=>'',
                                'url'=>current_url(),
                                'request_method'=>$_SERVER['REQUEST_METHOD']??'',
                                'ip_address'=>$_SERVER['REMOTE_ADDR']??'',
                                'created_on'=>time(),
                            );
                            $this->activity_log->log_action($action);
                            // if(preg_match('/eazzyclub/',$_SERVER['HTTP_HOST']) && $host_group_id=='5224' && ENVIRONMENT == 'production'){
                            //     echo json_encode(array(
                            //         'status' => 400,
                            //         'message' => 'System currently under maintenance, please check back later.',
                            //     ) );
                            //     die;
                            // }
                            $this->investment_groups->update_last_seen($host_group_id,$host_user_id);
                        }
                        if($this->token == 'abcdefghhgfedcba'){
                            $this->request = $request;
                        }else{
                            if($this->_request_id_required($request_id) ){
                                if($this->require_user_id){
                                    if($this->user_id == $host_user_id){
                                        $this->request = $request;
                                    }else{
                                        echo json_encode(array(
                                            'status' => 400,
                                            'message' => 'user Authentication token is invalid.',
                                        ) );
                                        die;
                                    }
                                }else{
                                    $this->request = $request;
                                }
                            }else{
                                echo json_encode(array(
                                    'status' => 3,
                                    'message' => 'You are currently using an outdated application. Kindly update to latest version. Section 1',
                                ));
                                die;
                            }   
                        }              
                    }else{
                        echo json_encode(array(
                            'status' => 3,
                            'message' => 'You are currently using an outdated application. Kindly update to latest version. Section 2',
                            "info" => $arr,
                        ));
                        die;
                    }
                }else{
                    if($this->_is_image_upload()){

                    }else{
                        echo json_encode(array(
                            'status' => 2,
                            'message' => 'Empty file sent from the client',
                        ));
                        die;
                    }
                }

            }else{
                echo json_encode(array(
                    'status' => '401',
                    'message' => 'You are using an outdated application. Update to the latest application',
                ));
                die;
            }
        }else{
            echo json_encode(array(
                'status' => 400,
                'message' => 'Authentication token expired',
            ) );
            die;
        }
    }

    public function _remap($method, $params = array()){
       if(method_exists($this, $method)){
           return call_user_func_array(array($this, $method), $params);
       }
       $this->output->set_status_header('404');
       header('Content-Type: application/json');
       $file = file_get_contents('php://input')?(array)json_decode(file_get_contents('php://input')):array();
       $request = $_REQUEST+$file;
       echo json_encode(
        array(
            'response' => array(
                    'status'    =>  0,
                    'error'     =>  '404 Method Not Found for URI: '.$this->uri->uri_string(),
                )
        )
        );
    }

    function _ignore_token_check(){
        $uri_string = $this->uri->uri_string();
        $url_exceptions = array(
            'mobile/index',
            'mobile/generate_pin',
            ///'mobile/check_connection',
            'mobile/verify_pin',
            'mobile/encrypt_test',
            'mobile/decrypt_test',
            //'mobile/test_header',
            'mobile/resend_pin',
            'mobile/login',
            'mobile/get_user_invited_groups',
            'mobile/accept_decline_invitation',
            'mobile/get_checkin_data',
            'mobile/register_user',
            'mobile/forgot_password',
            'mobile/validate_forgot_password_code',
            'mobile/reset_password',
        );
        $result = FALSE;
        if(in_array(trim($uri_string), $url_exceptions)){
            $result = TRUE;
            $this->ignore_secret_key = TRUE;
        }
        return $result;
    }

    function _verify_authentication(){
        if($this->_ignore_token_check()){
            return $this->token_key;
        }else{
            if($this->request_headers){
                if(isset($this->request_headers['Authorization'])){
                    $access_token = $this->request_headers['Authorization'];
                    $access_token = trim(str_replace("Bearer","",$access_token));
                    $access_token = trim(str_replace("Basic","",$access_token));
                    $this->user_id = $this->users_m->is_access_token_valid($access_token);
                    //'/197\.237\.107\.3/'
                    if($access_token == 'abcdefghhgfedcba'||preg_match('/197\.237\.106\.185/', $_SERVER['REMOTE_ADDR']) || preg_match('/127\.0\.0\.1/', $_SERVER['REMOTE_ADDR'])){
                        return $access_token;
                    }
                    if($this->user_id){
                        $this->require_user_id = TRUE;
                        return $access_token;
                    }else{
                        return FALSE;
                    }              
                }else{
                    echo json_encode(array(
                        'status' => '401',
                        'message' => 'You are currently using an outdated application. Update to the latest version',
                    ));
                    die;
                }
            }else{
                echo json_encode(array(
                    'status' => '401',
                    'message' => 'You are currently using an outdated application. Update to the latest version',
                ));
                die;
            }
        }
        return $result;
    }

    function _get_secret_key(){
        if($this->request_headers){
            if(isset($this->request_headers['Secret'])){
                return $this->request_headers['Secret'];
            }else{
                if ($this->ignore_secret_key) {
                    return TRUE;
                }else{
                    echo json_encode(array(
                        'status' => '401',
                        'message' => 'You are currently using an outdated application. Update to the latest version',
                ));
                die;
              }
            }
          }else{
                echo json_encode(array(
                    'status' => '401',
                    'message' => 'You are currently using an outdated application. Update to the latest version',
                ));
                die;
        }
    }

    function _get_version_code(){
        if($this->request_headers){
            if(isset($this->request_headers['Versioncode'])){
                return $this->request_headers['Versioncode'];
            }else if(isset($this->request_headers['VersionCode'])){
                return $this->request_headers['VersionCode'];
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

    function _generate_access_token($id=0,$user_id = 0,$identity=0){
        if($id && ($user_id||$identity)){
            $access_token = random_string('alnum', 32);
            if($access_token){
                if(preg_match('/chamasoft/i', $this->chamasoft_settings->application_name)){
                    $input = array(
                        'user_id' => $user_id,
                        'user_id' => $user_id,
                        'access_token' => $access_token,
                        'access_token_expire_on' => strtotime("+100 days"),
                        'access_token_created_on' => time(),
                        'modified_on' => time(),
                        'modified_by' => 1,
                    );
                }else{
                    $input = array(
                        'user_id' => $user_id,
                        'user_id' => $user_id,
                        'access_token' => $access_token,
                        'access_token_expire_on' => strtotime("+10 minutes"),
                        'access_token_created_on' => time(),
                        'modified_on' => time(),
                        'modified_by' => 1,
                    );
                }

                if($this->users_m->update_user_pin_access_token($id,$input)){
                    return (object)$input;
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

    function _request_id_required($request_id=0){
        $uri_string = $this->uri->uri_string();
        $required_endpoints = array(
            'mobile/deposits/record_contribution_payments',
            'mobile/deposits/record_fine_payments',
            'mobile/deposits/record_loan_repayments',
            'mobile/deposits/record_miscellaneous_payments',
            'mobile/deposits/record_income',
            'mobile/deposits/record_bank_loan',
            'mobile/deposits/record_contribution_transfer',
            'mobile/withdrawals/record_expenses',
            'mobile/withdrawals/record_funds_transfer',
            'mobile/withdrawals/record_contribution_refund',
            'mobile/withdrawals/record_bank_loan_repayment',
            'mobile/withdrawals/request_funds_transfer',
            'mobile/loan_types/create_loan_type',
            'mobile/setup_tasks/create_group_contribution_setting',
            'mobile/invoices/create',
            'mobile/assets/record_asset_purchase_payments',
            'mobile/stocks/record_stock_purchase',
        );
        if(in_array($uri_string, $required_endpoints)){
            if($request_id){
                $request_id_params = explode('_',$request_id);
                if(isset($request_id_params[0])){
                    if(date('dmy',$request_id_params[0]) == date('dmy')){
                        if(unique_request($request_id)){
                            return TRUE;
                        }else{
                            echo json_encode(array(
                                'status' => 12,
                                'message' => 'Duplicate request',
                            ));
                            die;
                        }
                    }else{
                        echo json_encode(array(
                            'status' => 13,
                            'message' => 'Request id format is invalid. Kindly try again',
                        ));
                        die;
                    }
                }else{
                    echo json_encode(array(
                        'status' => 11,
                        'message' => 'You are currently using an outdated application. Update to the latest version',
                    ));
                    die;
                }
            }else{
                echo json_encode(array(
                    'status' => 11,
                    'message' => 'You are currently using an outdated application. Update to the latest version',
                ));
                die;
            }
        }else{
            return TRUE;
        }
    }

    function _is_image_upload(){
        $uri_string = $this->uri->uri_string();
        $upload_endpoints = array(
            'mobile/members/edit_profile_photo',
            'mobile/users/edit_profile_photo',
            'mobile/groups/edit_profile_photo',
        );
        if(in_array($uri_string, $upload_endpoints)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function _get_group_members_count($group_id=0){
        $members_count = $this->members_m->count_all_members($group_id);
        return $members_count;
    }

    function _get_member_group_dashboard_data($group_id = 0,$member_id = 0,$disable_arrears = 0){
        $post = array();
        $total_loan_balances = $this->loans_m->get_total_loan_balances($group_id,$member_id);
        $total_loan_lump_sum_balance = $this->loans_m->get_total_loan_lump_sum_as_date($group_id,$member_id,time());

        $group_member_total_contribution_paid = $this->statements_m->get_group_member_total_contribution_paid($member_id,$group_id);    
        $group_member_total_contribution_arrears = $this->statements_m->get_group_member_total_contribution_arrears($member_id,$group_id);
        $group_member_total_fine_paid = $this->statements_m->get_group_member_total_fine_paid($member_id,$group_id);
        $group_member_total_fine_arrears = $this->statements_m->get_group_member_tota_fine_arrears($member_id,$group_id);
        if($group_member_total_fine_paid&&$group_member_total_fine_arrears){
            $post['total_member_fines'] = $group_member_total_fine_paid->cumulative_paid;
            if($disable_arrears){
                $post['total_member_fine_arrears'] = 0;
            }else{
                $post['total_member_fine_arrears'] = $group_member_total_fine_arrears->cumulative_balance;
            }
        }else{
            $total_group_member_total_fines = $this->deposits_m->get_group_member_total_fines($member_id,$group_id);
            $total_member_contribution_transfers_from_contribution_to_fine_category = $this->statements_m->get_group_member_total_contribution_transfers_from_contribution_to_fine_category($member_id,$group_id);
            $post['total_member_fines'] = ($total_group_member_total_fines+$total_member_contribution_transfers_from_contribution_to_fine_category)?:0;
            if($disable_arrears){
                $total_group_member_fine_arrears = 0; 
            }else{
                $total_group_member_fine_arrears = $this->statements_m->get_member_fine_balance($group_id,$member_id);
            }
            $post['total_member_fine_arrears'] = ($total_group_member_fine_arrears)?:0;
        }

        if($group_member_total_contribution_paid && $group_member_total_contribution_arrears){
            $post['total_member_contributions'] = $group_member_total_contribution_paid->cumulative_paid;
            if($disable_arrears){
                $post['total_member_contribution_arrears'] = 0;
                $total_group_member_fine_arrears = 0;
                $post['total_member_fine_arrears'] = 0;
            }else{
                $post['total_member_contribution_arrears'] = $group_member_total_contribution_arrears->cumulative_balance;
            }
        }else{
            $total_group_member_contributions = $this->deposits_m->get_group_member_total_contributions($member_id,$group_id);
            $total_member_contribution_refunds = $this->withdrawals_m->get_group_member_total_contribution_refunds($member_id,$group_id);
            $total_member_contribution_transfers_to_loan = $this->statements_m->get_group_member_total_contribution_transfers_to_loan($member_id,$group_id);
            $total_member_contribution_transfers_from_loan_to_contribution = $this->statements_m->get_group_member_total_contribution_transfers_from_loan_to_contribution($member_id,$group_id);
            if($disable_arrears){
                $total_group_member_contribution_arrears = 0; 
                $total_group_member_fine_arrears = 0; 
            }else{
                $total_group_member_contribution_arrears = $this->statements_m->get_member_contribution_balance($group_id,$member_id);
                $total_group_member_fine_arrears = $this->statements_m->get_member_fine_balance($group_id,$member_id);
            }
            $total_member_contribution_transfers_from_contribution_to_fine_category = $this->statements_m->get_group_member_total_contribution_transfers_from_contribution_to_fine_category($member_id,$group_id);
            $post['total_member_contributions'] = ($total_group_member_contributions-$total_member_contribution_transfers_from_contribution_to_fine_category-$total_member_contribution_refunds-$total_member_contribution_transfers_to_loan+$total_member_contribution_transfers_from_loan_to_contribution)?:0;
            $post['total_member_contribution_arrears'] = ($total_group_member_contribution_arrears)?:0;
        }
        $post['total_member_loan_balances'] = $total_loan_balances?:0;
        $post['total_loan_lump_sum_balance'] = $total_loan_lump_sum_balance;
        return $post;
    }

    function _get_group_dashboard_data($group_id = 0){
        $post = array();
        $total_loan_balances = $this->loans_m->get_total_loan_balances($group_id);
        $post['total_group_loan_balances'] = $total_loan_balances?:0;
        $post['total_group_expenses'] = $this->withdrawals_m->get_group_total_expenses($group_id)?:0;
        $post['total_group_contributions'] = $this->statements_m->get_group_total_contributions_paid($group_id)?:0;
        $post['total_group_fines'] = $this->statements_m->get_group_total_fines_paid($group_id)?:0;
        $post['total_loaned_amount'] = $this->loans_m->get_total_loaned_amount($group_id)?:0;
        $post['total_loan_repaid'] = $this->loan_repayments_m->get_total_loan_paid($group_id)?:0;
        return $post;
    }

    function _get_group_chart_data($group_id = 0, $member_id = 0, $order ='DESC'){
        $member_records = 0;
        $deposit_withdrawal_records = 0;
        $from = strtotime(" 1st ".date('M Y',strtotime("-11 months",time())));
        $posts = $this->transaction_statements_m->get_group_transaction_statement($from,'',$group_id,0,$order,0);
        $transaction_names = $this->transactions->transaction_names;
        $data = array();
        $summation = array();
        $group_deposit_summation = array();
        $group_withdrawal_summation = array();
        $days = (strtotime(date('d-M-Y',time())) - $from)/(24*60*60)+1;
        foreach ($posts as $post) {
            $date = date('M Y',$post->transaction_date);
            if(in_array($post->transaction_type, $this->transactions->deposit_transaction_types)){
                if(in_array($post->transaction_type, $this->transactions->incoming_account_transfer_withdrawal_transaction_types)){
                    continue;
                }
                if($post->member_id == $this->member->id){
                    if(array_key_exists($date, $summation)){
                        $summation[$date] += $post->amount;
                    }else{
                        $summation[$date] = $post->amount;
                    }
                    ++$member_records;
                }
                if(array_key_exists($date, $group_deposit_summation)){
                    $group_deposit_summation[$date] += $post->amount;
                }else{
                    $group_deposit_summation[$date] = $post->amount;
                }
                ++$deposit_withdrawal_records;
            }elseif(in_array($post->transaction_type,$this->transactions->withdrawal_transaction_types)){
                if(in_array($post->transaction_type, $this->transactions->statement_outgoing_account_transfer_withdrawal_transaction_types)){
                    continue;
                }
                if(array_key_exists($date, $group_withdrawal_summation)){
                    $group_withdrawal_summation[$date] += $post->amount;
                }else{
                    $group_withdrawal_summation[$date] = $post->amount;
                }
            }   
        }
        $final = array();
        $group_deposit_final = array();
        $group_withdrawals_final  = array();
        for ($i=0; $i <$days; $i++) { 
            $date_from = date('M Y',$from);
            if(array_key_exists($date_from, $summation)){
                $final[date('M',$from)] = $summation[$date_from];
            }else{
                $final[date('M',$from)] = 0;
            }
            if(array_key_exists($date_from, $group_deposit_summation)){
                $group_deposit_final[date('M',$from)] = $group_deposit_summation[$date_from];
            }else{
                $group_deposit_final[date('M',$from)] = 0;
            }
            if(array_key_exists($date_from, $group_withdrawal_summation)){
                $group_withdrawals_final[date('M',$from)] = $group_withdrawal_summation[$date_from];
            }else{
                $group_withdrawals_final[date('M',$from)] = 0;
            }
            $from+=(24*60*60);
        }
        $expense_category_options = $this->expense_categories_m->get_group_expense_category_options($group_id);
        $group_expense_category_totals = $this->withdrawals_m->get_group_expense_category_totals_array($group_id);
        $expenses = array();
        $expenses_records = count($group_expense_category_totals);
        foreach($group_expense_category_totals as $expense_category_id => $group_expense_category_total):
            if($group_expense_category_total){
                $expenses[] = array(
                    'expense_name' => $expense_category_options[$expense_category_id]??'',
                    'amount' => $group_expense_category_total,
                );
            }
        endforeach;
        $data['member_transactions'] = $final;
        $data['member_records'] = $member_records;
        $data['deposit_withdrawal_records'] = $deposit_withdrawal_records;
        $data['expenses_records'] = $expenses_records;
        $data['group_transactions'] = array(
            'deposits' => $group_deposit_final,
            'withdrawals' => $group_withdrawals_final,
            'expenses' => $expenses,
        );
        return $data;
    }


}