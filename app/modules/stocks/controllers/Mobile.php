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

    protected $validation_rules=array(
        array(
            'field' => 'purchase_date',
            'label' => 'Purchase Date',
            'rules' => 'required|xss_clean|trim|date',
        ),
        array(
            'field' => 'name',
            'label' => 'Stock Purchase Name',
            'rules' => 'required|xss_clean|trim',
        ),
        array(
            'field' => 'number_of_shares',
            'label' => 'Number of Shares',
            'rules' => 'required|xss_clean|trim|numeric',
        ),    
        array(
            'field' => 'account_id',
            'label' => 'Account',
            'rules' => 'required|xss_clean|trim|callback__valid_account_id',
        ), 
        array(
            'field' => 'price_per_share',
            'label' => 'Price per Share',
            'rules' => 'required|xss_clean|trim|currency',
        ),
    );

    protected $stock_sale_validation_rules=array(
        array(
            'field' =>  'account_id',
            'label' =>  'Account',
            'rules' =>  'xss_clean|trim|required|callback__valid_account_id',
        ),array(
            'field' =>  'sale_date',
            'label' =>  'Stocks Sale Date',
            'rules' =>  'xss_clean|trim|required|date',
        ),
        array(
            'field' =>  'number_of_shares_to_be_sold',
            'label' =>  'Number of Shares Sold',
            'rules' =>  'xss_clean|trim|required|numeric|is_natural_no_zero|callback__is_less_than_or_equal_to_shares_available',
        ),
        array(
            'field' =>  'number_of_shares_available',
            'label' =>  'Shares Available',
            'rules' =>  'xss_clean|trim|required|numeric',
        ),
        array(
            'field' =>  'sale_price_per_share',
            'label' =>  'Sale Price per Share',
            'rules' =>  'xss_clean|trim|required|currency',
        ),array(
            'field' =>  'number_of_previously_sold_shares',
            'label' =>  'Number of Previously Sold Shares',
            'rules' =>  'xss_clean|trim|numeric',
        ),
    );

    function _is_less_than_or_equal_to_shares_available(){
        if($this->input->post('number_of_shares_available')<$this->input->post('number_of_shares_to_be_sold')){
            $this->form_validation->set_message('_is_less_than_or_equal_to_shares_available', 'Number of sold shares cannot exceed available shares');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    function _valid_account_id(){
        $account_id = $this->input->post('account_id');
        $group_id = $this->input->post('group_id');
        if($this->accounts_m->check_if_group_account_exists($account_id,$group_id)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_valid_account_id','Group account does not exist');
            return FALSE;
        }
    }

    public function __construct(){
        parent::__construct();
        $this->load->model('stocks_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->model('deposits/deposits_m');
        $this->load->model('withdrawals/withdrawals_m');
        $this->load->library('transactions');
    }

    function record_stock_purchase(){
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
                        $result = $this->transactions->record_stock_purchase(
                            $this->group->id,
                            $this->input->post('purchase_date'),
                            $this->input->post('name'),
                            $this->input->post('number_of_shares'),
                            $this->input->post('account_id'),
                            $this->input->post('price_per_share')
                        );
                        if($result){
                           $response = array(
                                'status' => 1,
                                'message' => 'Stock Purchase successfully recorded',
                            ); 
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Stock Purchase could not be recorded',
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
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function update_stock_current_price(){
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
                    $stock = $this->stocks_m->get_group_stock($id,$this->group->id);
                    if($stock){
                        if(valid_currency($this->input->post('current_price'))){
                            $input = array(
                                'current_price'=> valid_currency($this->input->post('current_price')),
                                'modified_on'=>time(),
                            );
                            if($result = $this->stocks_m->update($stock->id,$input)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Stock current price successfully updated',
                                );
                            }else{
                               $response = array(
                                    'status' => 0,
                                    'message' => 'Unable to update stock current price',
                                ); 
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Current price is not valid',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are trying to alter Stock Price which is currently unavailable',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function sell_stocks(){
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
                    if($stock = $this->stocks_m->get_group_stock($id,$this->group->id)){
                        $_POST['number_of_shares_available']=$stock->number_of_shares-$stock->number_of_shares_sold;
                        $_POST['number_of_previously_sold_shares']=$stock->number_of_shares_sold;
                        $this->form_validation->set_rules($this->stock_sale_validation_rules);
                        if($this->form_validation->run()){
                            $res = $this->transactions->record_stock_sale(
                                $this->group->id,
                                $stock->id,
                                $this->input->post('sale_date'),
                                $this->input->post('account_id'),
                                $this->input->post('number_of_shares_to_be_sold'),
                                $this->input->post('sale_price_per_share'),
                                $this->input->post('number_of_previously_sold_shares')?:0
                            );
                            if($res){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Stock sale successfully added',
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Error adding stock sale',
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
                            'message' => 'You are trying to sell a Stock which is not available',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find member details',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group details',
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
                    if($post = $this->stocks_m->get_group_stock($id,$this->group->id)){
                        $withdrawal = $this->withdrawals_m->get_withdrawal_by_stock_id($post->id,$this->group->id);
                        if($withdrawal){
                            if($this->transactions->void_group_withdrawal($withdrawal->id,$withdrawal,TRUE,$this->group->id)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'successfully voided stock'
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Error occured. Try again later',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Stock details not found',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Stock details not found',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find member details',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group details',
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

    function get_group_stocks_list(){
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
                    $total_rows = $this->stocks_m->count_group_stocks('',$this->group->id);
                    $lower_limit = $this->input->post('lower_limit')?:0;
                    $upper_limit = $this->input->post('upper_limit')?:100;
                    $records_per_page = $upper_limit - $lower_limit;
                    if($lower_limit>$upper_limit){
                        $records_per_page = 100;
                    }
                    $pagination =create_custom_pagination('mobile',$total_rows,$records_per_page,$lower_limit,TRUE);
                    $posts = $this->stocks_m->limit($pagination['limit'])->get_group_stocks('',$this->group->id);
                    $group_stocks = array();
                    foreach ($posts as $post) {
                        $group_stocks[] = array(
                            'id' => $post->id,
                            'date' => timestamp_to_mobile_shorttime($post->purchase_date),
                            'name' => $post->name,
                            'price' => $post->purchase_price,
                            'shares' => $post->number_of_shares,
                            'current_price' => $post->current_price,
                        );
                    }

                    $response = array(
                        'status' => 1,
                        'message' =>'success',
                        'group_stocks' => $group_stocks,
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find member details',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group details',
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

    function get_group_stock_sales_list(){
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
                    $lower_limit = $this->input->post('lower_limit')?:0;
                    $upper_limit = $this->input->post('upper_limit')?:100;
                    $records_per_page = $upper_limit - $lower_limit;
                    if($lower_limit>$upper_limit){
                        $records_per_page = 100;
                    }
                    $total_rows = $this->deposits_m->count_group_stock_sale_deposits($this->group->id);
                    $pagination =create_custom_pagination('mobile',$total_rows,$records_per_page,$lower_limit,TRUE);
                    $account_options = $this->accounts_m->get_group_account_options(FALSE,'',$this->group->id);
                    $stock_options = $this->stocks_m->get_group_stock_options($this->group->id);
                    $posts = $this->deposits_m->limit($pagination['limit'])->get_group_stock_sale_deposits($this->group->id);
                    $stock_sales = array();
                    foreach ($posts as $post) {
                        $stock_sales[] = array(
                            'id' => $post->id,
                            'date' => timestamp_to_mobile_shorttime($post->deposit_date),
                            'name' => $stock_options[$post->stock_id],
                            'account' => $account_options[$post->account_id],
                            'shares_sold' => $post->number_of_shares_sold,
                            'per_share_sale_price' => $post->sale_price_per_share,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'success',
                        'stock_sales' => $stock_sales,
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find member details',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group details',
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