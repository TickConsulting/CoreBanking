<?php  defined('BASEPATH') OR exit('No direct script access allowed');
class Mobile extends Mobile_Controller{

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
                    'status'    =>  404,
                    'message'       =>  '404 Method Not Found for URI: '.$this->uri->uri_string(),
                ),

        ));
    }

    public function __construct(){
        parent::__construct();
        $this->load->model('money_market_investments_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->library('transactions');
    }

    protected $validation_rules=array(
        array(
                'field' =>  'investment_institution_name',
                'label' =>  'Investment Institution Name',
                'rules' =>  'xss_clean|trim|required',
            ),
        array(
                'field' =>   'investment_date',
                'label' =>   'Investment Date',
                'rules' =>   'xss_clean|trim|required|date',
            ),
        array(
                'field' =>   'investment_amount',
                'label' =>   'Investment Amount',
                'rules' =>   'xss_clean|trim|required|currency',
            ),
        array(
                'field' =>   'withdrawal_account_id',
                'label' =>   'Account',
                'rules' =>   'xss_clean|trim|required|callback__valid_account_id',
            ),
        array(
                'field' =>   'description',
                'label' =>   'Description',
                'rules' =>   'xss_clean|trim',
            ),
    );

    protected $topup_validation_rules = array(
        array(
                'field' =>  'top_up_description',
                'label' =>  'Top Up Description',
                'rules' =>  'xss_clean|trim',
            ),
        array(
                'field' =>   'top_up_date',
                'label' =>   'Top Up Date',
                'rules' =>   'xss_clean|trim|required|date',
            ),
        array(
                'field' =>   'top_up_amount',
                'label' =>   'Top Up Amount',
                'rules' =>   'xss_clean|trim|required|currency',
            ),
        array(
                'field' =>   'top_up_withdrawal_account_id',
                'label' =>   'Account',
                'rules' =>   'xss_clean|trim|required|callback__valid_topup_account_id',
            ),

    );

    protected $cashin_validation_rules = array(
        array(
                'field' =>   'cash_in_date',
                'label' =>   'Cash in Date',
                'rules' =>   'xss_clean|trim|required|date',
            ),
        array(
                'field' =>   'cash_in_amount',
                'label' =>   'Cash In Amount',
                'rules' =>   'xss_clean|trim|required|currency',
            ),
        array(
                'field' =>   'cash_in_deposit_account_id',
                'label' =>   'Account',
                'rules' =>   'xss_clean|trim|required|callback__valid_cashin_account_id',
            ),
    );

    function _valid_account_id(){
        $account_id = $this->input->post('withdrawal_account_id');
        $group_id = $this->input->post('group_id');
        if($this->accounts_m->check_if_group_account_exists($account_id,$group_id)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_valid_account_id','Group account does not exist');
            return FALSE;
        }
    }

    function _valid_topup_account_id(){
        $account_id = $this->input->post('top_up_withdrawal_account_id');
        $group_id = $this->input->post('group_id');
        if($this->accounts_m->check_if_group_account_exists($account_id,$group_id)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_valid_topup_account_id','Group account does not exist');
            return FALSE;
        }
    }

    function _valid_cashin_account_id(){
        $account_id = $this->input->post('cash_in_deposit_account_id');
        $group_id = $this->input->post('group_id');
        if($this->accounts_m->check_if_group_account_exists($account_id,$group_id)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_valid_cashin_account_id','Group account does not exist');
            return FALSE;
        }
    }

    function create(){
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
                    $this->form_validation->set_rules($this->validation_rules);
                    if($this->form_validation->run()){
                        $result = $this->transactions->create_money_market_investment(
                                $this->group->id,
                                $this->input->post('investment_institution_name'),
                                $this->input->post('investment_date'),
                                $this->input->post('investment_amount'),
                                $this->input->post('withdrawal_account_id'),
                                $this->input->post('description')
                            );
                         if($result){
                            $response = array(
                                'status' => 1,
                                'message' => 'Money market investment successfully added',
                            ); 
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Error occured while adding money market investment',
                            );
                        }
                    }else{
                        $post = array();
                        $form_errors = $this->form_validation->error_array();
                        foreach ($form_errors as $key => $value) {
                            $post[$key] = $value;
                        }
                        $response = array(
                                'status' => 0,
                                'message' => 'Form validation failed',
                                'validation_errors' => $post,
                            );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find Applicant Details
',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group detals',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function topup(){
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
                    $id = $this->input->post('id');
                    $money_market_investment = $this->money_market_investments_m->get($id,$this->group->id);
                    if($money_market_investment){
                        $this->form_validation->set_rules($this->topup_validation_rules);
                        if($this->form_validation->run()){
                            $result = $this->transactions->top_up_money_market_investment(
                                    $this->group->id,
                                    $money_market_investment->id,
                                    $this->input->post('top_up_date'),
                                    $this->input->post('top_up_amount'),
                                    $this->input->post('top_up_withdrawal_account_id'),
                                    $this->input->post('top_up_description')
                                );
                             if($result){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Money market investment topup successfully added',
                                ); 
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Error occured while adding money market investment topup',
                                );
                            }
                        }else{
                            $post = array();
                            $form_errors = $this->form_validation->error_array();
                            foreach ($form_errors as $key => $value) {
                                $post[$key] = $value;
                            }
                            $response = array(
                                    'status' => 0,
                                    'time' => time(),
                                    'message' => 'Form validation failed',
                                    'validation_errors' => $post,
                                );
                        }
                    }else{
                         $response = array(
                            'status' => 0,
                            'message' => 'Money market investment to topup not found',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find Applicant Details
',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group detals',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function cashin(){
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
                    $id = $this->input->post('id');
                    $money_market_investment = $this->money_market_investments_m->get($id,$this->group->id);
                    if($money_market_investment){
                        $this->form_validation->set_rules($this->cashin_validation_rules);
                        if($this->form_validation->run()){
                            $result = $this->transactions->record_money_market_investment_cash_in_deposit(
                                $this->group->id,
                                $money_market_investment->id,
                                $this->input->post('cash_in_date'),
                                $this->input->post('cash_in_deposit_account_id'),
                                $this->input->post('cash_in_amount')
                            );
                             if($result){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Money market investment Cashin successfully added',
                                ); 
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Error occured while adding money market investment Cashin',
                                );
                            }
                        }else{
                            $post = array();
                            $form_errors = $this->form_validation->error_array();
                            foreach ($form_errors as $key => $value) {
                                $post[$key] = $value;
                            }
                            $response = array(
                                'status' => 0,
                                'time' => time(),
                                'message' => 'Form validation failed',
                                'validation_errors' => $post,
                            );
                        }
                    }else{
                         $response = array(
                            'status' => 0,
                            'message' => 'Money market investment to Cashin not found',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find Applicant Details
',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group detals',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function void(){
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
                    $id = $this->input->post('id');
                    $post = $this->money_market_investments_m->get_group_money_market_investment($id,$this->group->id);
                    if($post){
                        $withdrawal = $this->withdrawals_m->get_money_market_investment_withdrawal_by_money_market_investment_id($id,$this->group->id);
                        if($withdrawal){
                            if($this->transactions->void_group_withdrawal($withdrawal->id,$withdrawal,TRUE,$this->group->id)){
                                $response = array(
                                    'status' => 1,
                                    'time' => time(),
                                    'message' => 'successfully voided money market investment',
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'time' => time(),
                                    'message' => 'Error occured. Try again later',
                                );
                            }
                            
                        }else{
                            $response = array(
                                'status' => 0,
                                'time' => time(),
                                'message' => 'Money Market Investment details are missing',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'time' => time(),
                            'message' => 'Money Market Investment details are missing',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find Applicant Details
',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group detals',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function get_money_market_investment_list(){
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
                    $total_rows = $this->money_market_investments_m->count_group_money_market_investments('',$this->group->id);
                    $lower_limit = $this->input->post('lower_limit')?:0;
                    $upper_limit = $this->input->post('upper_limit')?:100;
                    $records_per_page = $upper_limit - $lower_limit;
                    if($lower_limit>$upper_limit){
                        $records_per_page = 100;
                    }
                    $pagination =create_custom_pagination('mobile',$total_rows,$records_per_page,$lower_limit,TRUE);
                    $account_options = $this->accounts_m->get_group_account_options(FALSE,'',$this->group->id);
                    $posts = $this->money_market_investments_m->limit($pagination['limit'])->get_group_money_market_investments('',$this->group->id);
                    $investments = array();
                    foreach ($posts as $post) {
                        $description = $post->description?': '.$post->description:'';
                        $investments[] = array(
                            'id' => $post->id,
                            'date' => timestamp_to_mobile_shorttime($post->investment_date),
                            'description' => $post->investment_institution_name.' '.$description,
                            'is_closed' => $post->is_closed?1:0,
                            'amount' => $post->investment_amount,
                            'investment_institution_name' => $post->investment_institution_name,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'success',
                        'investments' => $investments,
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find Applicant Details
',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group detals',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function get_money_market_investment_cashins(){
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
                    $total_rows = $this->deposits_m->count_group_money_market_investment_cash_in_deposits($this->group->id);
                    $lower_limit = $this->input->post('lower_limit')?:0;
                    $upper_limit = $this->input->post('upper_limit')?:100;
                    $records_per_page = $upper_limit - $lower_limit;
                    if($lower_limit>$upper_limit){
                        $records_per_page = 100;
                    }
                    $pagination =create_custom_pagination('mobile',$total_rows,$records_per_page,$lower_limit,TRUE);
                    $account_options = $this->accounts_m->get_group_account_options(FALSE,'',$this->group->id);
                    $group_currency = $this->countries_m->get_group_currency_name($this->group->id);
                     $money_market_investment_options = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$group_currency);
                    $posts = $this->deposits_m->limit($pagination['limit'])->get_group_money_market_investment_cash_in_deposits($this->group->id);
                    $cashins = array();
                    foreach ($posts as $post) {
                        $cashins[] = array(
                            'id' => $post->id,
                            'date' => timestamp_to_mobile_shorttime($post->deposit_date),
                            'name' => $money_market_investment_options[$post->money_market_investment_id],
                            'account' => $account_options[$post->account_id],
                            'amount' => $post->amount,
                        );
                    }

                    $response = array(
                        'status' => 1,
                        'message' => 'success',
                        'cashins' => $cashins,
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find Applicant Details
',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group detals',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }
    
}?>