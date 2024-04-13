<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Mobile extends Mobile_Controller{

  function __construct(){
    parent::__construct();
        $this->load->model('accounts_m');
  }

  public function _remap($method, $params = array()){
       if(method_exists($this, $method)){
           return call_user_func_array(array($this, $method), $params);
       }
       $this->output->set_status_header('404');
       header('Content-Type: application/json');
       $file = file_get_contents('php://input')?(array)json_decode(file_get_contents('php://input')):array();
       $request = $_REQUEST+$file;
       echo encrypt_json_encode(
        array(
          'response' => array(
            'status'  =>  404,
            'message'   =>  '404 Method Not Found for URI: '.$this->uri->uri_string(),
        )

        )
        );
  }


    function get_group_account_options(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $show_account_balances = $this->input->post('show_account_balances')?1:0;
                    $accounts = array();
                    $bank_accounts = array();
                    $banks = $this->bank_accounts_m->get_group_bank_accounts($this->group->id);
                    foreach ($banks as $bank) {
                        $bank_accounts[] = array(
                          'id' => $bank->id,
                          'name' => $bank->bank_name.'('.$bank->bank_branch.') - '.$bank->account_name.'('.$bank->account_number.')',
                        );
                    }
                    $sacco_accounts = array();
                    $saccos = $this->sacco_accounts_m->get_group_sacco_accounts($this->group->id);
                    foreach ($saccos as $sacco) {
                        $sacco_accounts[] = array(
                          'id' => $sacco->id,
                          'name' => $sacco->sacco_name.'('.$sacco->sacco_branch.') - '.$sacco->account_name.'('.$sacco->account_number.')',
                        );
                    }
                    $petty_cash_accounts = array();
                    $petty_accounts = $this->petty_cash_accounts_m->get_group_petty_cash_accounts($this->group->id);
                    foreach ($petty_accounts as $petty_account) {
                        $petty_cash_accounts[] = array(
                          'id' => $petty_account->id,
                          'name' => $petty_account->account_name,
                        );
                    }
                    $mobile_money_accounts = array();
                    $mobile_moneys = $this->mobile_money_accounts_m->get_group_mobile_money_accounts($this->group->id);
                    foreach ($mobile_moneys as $mobile_money) {
                        $mobile_money_accounts[] = array(
                          'id' => $mobile_money->id,
                          'name' => $mobile_money->mobile_money_provider_name.'-'.$mobile_money->account_name.'('.$mobile_money->account_number.')',
                        );
                    }
                    $accounts = array(
                      'bank_accounts' => $bank_accounts,
                      'sacco_accounts' => $sacco_accounts,
                      'mobile_money_accounts' => $mobile_money_accounts,
                      'petty_cash_accounts' => $petty_cash_accounts,
                    );
                    $response = array(
                        "status" => 1,
                        "message" => 'successful',
                        "accounts" => $accounts,
                        'terms_and_conditions_link' => site_url(),
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function get_group_active_account_options(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $show_account_balances = $this->input->post('show_account_balances')?1:0;
                    $accounts = array();
                    $bank_accounts = array();
                    $banks = $this->bank_accounts_m->get_group_active_bank_accounts($this->group->id);
                    foreach ($banks as $bank) {
                        $bank_accounts[] = array(
                          'id' => $bank->id,
                          'name' => $bank->bank_name.'('.$bank->bank_branch.') - '.$bank->account_name.'('.$bank->account_number.')',
                        );
                    }
                    $sacco_accounts = array();
                    $saccos = $this->sacco_accounts_m->get_group_active_sacco_accounts($this->group->id);
                    foreach ($saccos as $sacco) {
                        $sacco_accounts[] = array(
                          'id' => $sacco->id,
                          'name' => $sacco->sacco_name.'('.$sacco->sacco_branch.') - '.$sacco->account_name.'('.$sacco->account_number.')',
                        );
                    }
                    $petty_cash_accounts = array();
                    $petty_accounts = $this->petty_cash_accounts_m->get_group_active_petty_cash_accounts($this->group->id);
                    foreach ($petty_accounts as $petty_account) {
                        $petty_cash_accounts[] = array(
                          'id' => $petty_account->id,
                          'name' => $petty_account->account_name,
                        );
                    }
                    $mobile_money_accounts = array();
                    $mobile_moneys = $this->mobile_money_accounts_m->get_group_active_mobile_money_accounts($this->group->id);
                    foreach ($mobile_moneys as $mobile_money) {
                        $mobile_money_accounts[] = array(
                          'id' => $mobile_money->id,
                          'name' => $mobile_money->mobile_money_provider_name.'-'.$mobile_money->account_name.'('.$mobile_money->account_number.')',
                        );
                    }
                    $accounts = array(
                      'bank_accounts' => $bank_accounts,
                      'sacco_accounts' => $sacco_accounts,
                      'mobile_money_accounts' => $mobile_money_accounts,
                      'petty_cash_accounts' => $petty_cash_accounts,
                    );
                    $response = array(
                        "status" => 1,
                        "message" => 'successful',
                        "accounts" => $accounts,
                        'terms_and_conditions_link' => site_url(),
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }


}?>