<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Mobile extends Mobile_Controller{

    protected $validation_rules=array(
        array(
                'field' =>   'payment_date',
                'label' =>   'Payment Date',
                'rules' =>   'xss_clean|trim|required|date',
            ),
        array(
                'field' =>   'asset_id',
                'label' =>   'Asset',
                'rules' =>   'xss_clean|trim|required|callback__valid_asset',
            ),
        array(
                'field' =>   'account_id',
                'label' =>   'Account',
                'rules' =>   'xss_clean|trim|required|callback__valid_account_id',
            ),
         array(
                'field' =>   'payment_method',
                'label' =>   'Payment Method',
                'rules' =>   'xss_clean|trim|required|numeric',
            ),
        array(
                'field' =>   'description',
                'label' =>   'Asset Description',
                'rules' =>   'xss_clean|trim',
            ),
        array(
                'field' =>   'amount',
                'label' =>   'Amount',
                'rules' =>   'xss_clean|trim|required|currency',
            ),
    );

    protected $sale_validation_rules=array(
        array(
                'field' =>  'amount',
                'label' =>  'Sale Amount',
                'rules' =>  'xss_clean|trim|required|currency',
            ),
        array(
                'field' =>   'sale_date',
                'label' =>   'Sale Date',
                'rules' =>   'xss_clean|trim|required|date',
            ),
        array(
                'field' =>   'account_id',
                'label' =>   'Account',
                'rules' =>   'xss_clean|trim|required|callback__valid_account_id',
            ),
    );

    
    protected $validation_rules_create_asset = array(
        array(
                'field' =>   'name',
                'label' =>   'Asset Name',
                'rules' =>   'xss_clean|trim|required',
            ),
        array(
                'field' =>   'cost',
                'label' =>   'Asset Cost',
                'rules' =>   'xss_clean|trim|required|currency',
            ),
        array(
                'field' =>   'asset_category_id',
                'label' =>   'Asset Category',
                'rules' =>   'xss_clean|trim|required|numeric|callback__valid_asset_category_id',
            ),
        array(
                'field' =>   'description',
                'label' =>   'Asset Description',
                'rules' =>   'xss_clean|trim',
            ),
    );

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
            )

        ));
    }


    public function __construct(){
        parent::__construct();
        $this->load->model('assets_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->model('asset_categories/asset_categories_m');
        $this->load->library('transactions');
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

    function _valid_asset(){
        $asset_id = $this->input->post('asset_id');
        $group_id = $this->input->post('group_id');
        if($this->assets_m->get($asset_id,$group_id)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_valid_asset','Asset selected does not exist');
            return FALSE;
        }
    }

    function _valid_asset_category_id(){
        $asset_category_id = $this->input->post('asset_category_id');
        $group_id = $this->input->post('group_id');
        if($this->asset_categories_m->get($asset_category_id,$group_id)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_valid_asset_category_id','Asset Category does not exist');
            return FALSE;
        }
    }

    function create_asset(){
        $response = array();
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
                    $this->form_validation->set_rules($this->validation_rules_create_asset);
                    if($this->form_validation->run()){
                        $update = array(
                            'name'  =>  $this->input->post('name'),
                            'cost'  =>  $this->input->post('cost'),
                            'asset_category_id'  =>  $this->input->post('asset_category_id'),
                            'group_id'  =>  $this->group->id,
                            'active'    =>  1,
                            'created_by'    =>  $this->user->id,
                            'created_on'    =>  time(),
                        );
                        if($this->assets_m->insert($update)){
                            $response = array(
                                'status' => 1,
                                'message' => 'Successfully added asset',
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Error occured adding asset',
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

    function get_group_assets(){
        $response = array();
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
                    $total_rows = $this->assets_m->count_group_assets($this->group->id);
                    $pagination = create_custom_pagination('mobile',$total_rows,$records_per_page,$lower_limit,TRUE);
                    $posts = array();
                    $group_assets = $this->asset_categories_m->limit($pagination['limit'])->get_group_asset_categories($this->group->id);

                    $asset_category_options = $this->asset_categories_m->get_group_asset_category_options($this->group->id);
                    $assets = $this->assets_m->limit($pagination['limit'])->get_group_assets();
                    $total_cost = 0;
                    foreach ($assets as $asset) {
                        $posts[] = array(
                            'id' => $asset->id,
                            'name' => $asset->name,
                            'cost' => ($asset->cost),
                            'Category' => $asset_category_options[$asset->asset_category_id],
                            'description' => $asset->description,
                            'active' => $asset->active?1:0,
                        );
                        $total_cost += $asset->cost;
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'success',
                        'asset' => $posts,
                        'total_cost' => $total_cost,
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

    function record_asset_purchase_payments(){
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
                        $result = $this->transactions->record_asset_purchase_payment(
                                $this->group->id,
                                $this->input->post('payment_date'),
                                $this->input->post('asset_id'),
                                $this->input->post('account_id'),
                                $this->input->post('payment_method'),
                                $this->input->post('description'),
                                $this->input->post('amount')
                            );
                        if($result){
                            $response = array(
                                'status'    =>  1,
                                'message'   =>  'Successfully recorded asset purchase payment'
                            );
                        }else{
                            $response = array(
                                'status'    =>  0,
                                'message'   =>  $this->session->flashdata('error'),
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

    function sell(){
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
                    $this->form_validation->set_rules($this->sale_validation_rules);
                    if($this->form_validation->run()){
                        $id = $this->input->post('id');
                        if($asset = $this->assets_m->get($id,$this->group->id)){
                            $result = $this->transactions->record_asset_sale_deposit(
                                $this->group->id,
                                $asset->id,
                                $this->input->post('sale_date'),
                                $this->input->post('account_id'),
                                $this->input->post('amount')
                            );
                            if($result){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Asset sale Successfully sold'
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Error. Could not locate asset sale',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'time' => time(),
                                'error' => 'Error. Could not find asset',
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
                    $post = $this->assets_m->get_group_asset($id,$this->group->id);
                    if($post){
                        if($this->transactions->void_asset($id,$post)){
                             $response = array(
                                'status' => 1,
                                'message' => 'Successfully voided payment'
                            );
                        }else{
                             $response = array(
                                'status' => 0,
                                'message' => 'Error occured while voiding. Try again later',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not find selected group asset to void details.',
                        );
                    }
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

    function asset_purchase_payments(){
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
                    if($asset = $this->assets_m->get_group_asset($id,$this->group->id)){
                        $filter_parameters = array(
                            'assets' => array($asset->id),
                        );
                        $account_options = $this->accounts_m->get_group_account_options(FALSE);
                        $posts = $this->withdrawals_m->get_group_asset_purchase_withdrawals($filter_parameters);
                        $payments = array();
                        $total_paid_amount = 0;
                        foreach ($posts as $post) {
                            $total_paid_amount+=$post->amount;
                            $payments[] = array(
                                'id' => $post->id,
                                'account' => $account_options[$post->account_id],
                                'amount' => $post->amount,
                                'description' => $post->description,
                                'date' => timestamp_to_mobile_shorttime($post->withdrawal_date),
                            );
                        }
                        $response = array(
                            'payments' => $payments,
                            'status' => 1,
                            'message' => 'success',
                            'total_paid_amount' => $total_paid_amount,
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Sorry, the selected asset could not be found. Select a different asset',
                        );
                    }
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

    function asset_sales_list(){
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
                    $upper_limit = $this->input->post('upper_limit')?:20;
                    $records_per_page = $upper_limit - $lower_limit;
                    $total_rows = $this->deposits_m->count_group_asset_sale_deposits($this->group->id);
                    $pagination = create_custom_pagination('group',$total_rows,$records_per_page,$lower_limit,TRUE);
                    $account_options = $this->accounts_m->get_group_account_options(FALSE,'',$this->group->id);
                    $asset_options = $this->assets_m->get_group_asset_options($this->group->id);
                    $posts = $this->deposits_m->limit($pagination['limit'])->get_group_asset_sale_deposits($this->group->id);
                    $asset_sales = array();
                    foreach ($posts as $post){
                        $asset_sales[] = array(
                            'id' => $post->id,
                            'date' => timestamp_to_mobile_shorttime($post->deposit_date),
                            'name' => $asset_options[$post->asset_id],
                            'account' => $account_options[$post->account_id],
                            'amount' => $post->amount,
                            'description' => $post->description,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'success',
                        'asset_sales' => $asset_sales,
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
}
?>