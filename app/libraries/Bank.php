<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Bank{

	protected $ci;

	public function __construct(){
		$this->ci= & get_instance();
        $this->ci->load->library('curl');
        $this->ci->load->library('setup_tasks_tracker');
        $this->ci->load->model('groups/groups_m');
        $this->ci->load->model('banks/banks_m');
        $this->ci->load->model('bank_branches/bank_branches_m');
        $this->ci->load->model('bank_accounts/bank_accounts_m');
	}

    public function request_one_time_password($account_number = "",$phone_number = "",$calling_code = "254",$country_code="KE", $create_otp_url = "https://ws.equitybankgroup.com/chamasoft/createOTP"){
        $calling_code = substr($phone_number,0,3);
        if($account_number&&$phone_number){
            $password = (object)array(
                "accNumber" => strip_tags(trim($account_number),'<br/>'),
                "phoneNumber" => strip_tags(trim($phone_number),'<br/>'),
                "PasswordCategory" => "account-based",
                "passwordCategory" => "NON",
                "country_code" => $country_code,
            );
            $rqData =(object)array(
                "actionCode" => "createOTP",
                "countryCode" => $country_code,
                'password'   => $password,
            );
            $data_string = json_encode(
                (object)array(
                    'msgID' => '100100000',
                    'source' => 'MDE',
                    'msgType' => 'Enterprise',
                    'service' => 'PasswordES',
                    'rqData' => $rqData,
                )
            );
            $response = $this->_execute_curl_json_request($create_otp_url,$data_string);
            return $response;
        }else{
            return FALSE;
        }
    }

    public function verify_one_time_password($account_number = "",$phone_number = "",$verification_code = 0,$calling_code = "254",$country_code="KE", $verify_otp_url = "https://ws.equitybankgroup.com/chamasoft/verifyOTP"){
        if($account_number && $phone_number && $verification_code&&$calling_code){
            //$phone_number = $calling_code.substr($phone_number,-9);
            $password =  (object)array(
                "accNumber"  =>  $account_number,
                "phoneNumber"  =>  $phone_number,
                "passwordToken" =>  $verification_code,
            );

            $rqData =(object)array(
                "actionCode"  =>  "verifyPassword",
                'password'  =>  $password,
            );

            $data_string = json_encode(
                (object)array(
                    'msgID' => '100100000',
                    'source' => 'MDE',
                    'msgType' => 'Enterprise',
                    'service' => 'PasswordES',
                    'rqData' => $rqData,
                )
            );
            $response = $this->_execute_curl_json_request($verify_otp_url,$data_string);
            return $response;
        }else{
            return FALSE;
        }
    }
    
    function format_create_otp_request_string($account_no,$phone_number,$calling_code = "254"){
        if($account_no && $phone_number){
            $phone_number = $calling_code.substr($phone_number,-9);

            $password = (object)array(
                                "accNumber"         =>  strip_tags(trim($account_no), '<br/>'),
                                "phoneNumber"       =>  strip_tags(trim($phone_number), '<br/>'),
                                "PasswordCategory"  =>  "account-based",
                                "passwordCategory"  =>  "NON",
                            );
            $rqData =(object)array(
                                "actionCode" => "createOTP",
                                'password'   => $password,
                            );

            $data_string = json_encode((object)array(
                                    'msgID' => '100100000',
                                    'source' => 'MDE',
                                    'msgType' => 'Enterprise',
                                    'service' => 'PasswordES',
                                    'rqData' => $rqData,
                                    ));
            return $data_string;
        }else{
            return false;
        }
    }

    private function _execute_curl_json_request($url = "",$json = ""){
        $ch = curl_init();  
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST, true );
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $json);    
        $output=curl_exec($ch);
        curl_close($ch);
        return json_encode($output);
    }


    function open_online_account($user=array(),$group=array(),$member = array()){
        if($user && $group && $member){
            if($account = $this->ci->bank_accounts_m->get_group_default_bank_account($group->id)){
                $this->ci->session->set_flashdata('error','Group already has an existing online banking wallet');
                return FALSE;
            }else{
                $bank_branch = $this->ci->bank_branches_m->get_online_banking_headoffice();
                if($bank_branch){
                    $reference_number = time()+$group->id;
                    $post_data = json_encode(array(
                        "request_id" => time(),
                        "data" => array(
                            "account_name" => strtoupper($group->name).' C.E.W',
                            'notification_url' => site_url('transaction_alerts/reconcile_direct_online_banking_payment'),
                            "reference_number" => $reference_number,
                            'member' => array(
                                "full_name" => $user->first_name.' '.$user->last_name,
                                "id_number" => $group->account_number,
                                "phone_number" => $user->phone,
                            ),
                            "currency" => $group->currency_id?$this->ci->countries_m->get_currency_code($group->currency_id):"KES",
                            "disable_charges" => 1,
                        ),
                    ));
                    if(
                        preg_match('/\.local/', $_SERVER['HTTP_HOST']) || 
                        preg_match('/uat\.chamasoft\.com/', $_SERVER['HTTP_HOST']) || 
                        preg_match('/chamasoftbeta/', $_SERVER['HTTP_HOST']) || 
                        preg_match('/demo\.websacco\.com/', $_SERVER['HTTP_HOST']) || 
                        preg_match('/uat\.websacco\.com/', $_SERVER['HTTP_HOST'])
                    ){
                        $url = "https://api-test.chamasoft.com:443/api/accounts/open_account";
                    }else{
                        $url = "https://api.chamasoft.com:443/api/accounts/open_account";
                    }
                    if($response = $this->ci->curl->post_json_payment($post_data,$url)){
                        if($res = json_decode($response)){
                            $code = $res->code;
                            $description = $res->description;
                            if($code == 200){
                                $data = $res->data;
                                $account = $data->account;
                                $account_name = $account->account_name;
                                $account_number = $account->account_number;
                                $security_pass = $account->security_pass;
                                $balance = $account->balance;
                                $currency = $account->currency;
                                $input = array(
                                    'group_id'          =>  $group->id,
                                    'account_number'    =>  $account_number,
                                    'account_name'      =>  $account_name,
                                    'initial_balance'   =>  currency($balance),
                                    'bank_branch_id'    =>  $bank_branch->id,
                                    'bank_id'           =>  $bank_branch->bank_id,
                                    'created_by'        =>  $user->id,
                                    'account_password'  =>  $security_pass,
                                    'is_default'        =>  1,
                                    'active'            =>  1,
                                    'is_closed'         =>  0,
                                    'created_on'        =>  time(),
                                );
                                $id = $this->ci->bank_accounts_m->insert($input);
                                if($id){
                                    $this->ci->setup_tasks_tracker->set_completion_status('create-group-bank-account',$group->id,$user->id);
                                    if($this->ci->groups_m->update($group->id,array(
                                        "online_banking_enabled" => 1,
                                        'modified_by'   =>  $user->id,
                                        'modified_on'   =>  time()
                                    ))){
                                        return TRUE;
                                    }else{
                                        $this->ci->session->set_flashdata('error','Could not complete group account setup');
                                        return FALSE; 
                                    }                                    
                                }else{
                                    $this->ci->session->set_flashdata('error','Could not complete group account setup');
                                    return FALSE;
                                }
                            }else{
                                $this->ci->session->set_flashdata('error',$description);
                                return FALSE;  
                            }
                        }else{
                            $this->ci->session->set_flashdata('error','Error occured while processing request. Try again later');
                            return FALSE;  
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Could not make payment. '.$this->ci->session->flashdata('error'));
                        return FALSE;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Contact support to enable E-Wallet');
                    return FALSE;
                }
            }
        }else{
            $this->ci->session->set_flashdata('error','Essential parameters are missing');
            return FALSE;
        }
    }

}