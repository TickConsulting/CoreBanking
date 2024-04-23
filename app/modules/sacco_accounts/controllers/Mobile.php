<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mobile extends Mobile_Controller{

    function __construct(){
        parent::__construct();
        $this->load->model('sacco_accounts_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('sacco_branches/sacco_branches_m');
        $this->load->model('transaction_statements/transaction_statements_m');
    }

    protected $validation_rules = array(
        array(
                'field' =>  'account_name',
                'label' =>  'Account Name',
                'rules' =>  'required|xss_clean|trim'
            ),
        array(
                'field' =>  'sacco_id',
                'label' =>  'Sacco Name',
                'rules' =>  'required|xss_clean|trim|numeric|callback__sacco_exists'
            ),
        array(
                'field' =>  'sacco_branch_id',
                'label' =>  'Sacco Branch Name',
                'rules' =>  'required|xss_clean|trim|numeric'
            ),
        array(
                'field' =>  'account_number',
                'label' =>  'Account Number',
                'rules' =>  'required|xss_clean|trim|numeric|callback__is_unique_account|min_length[5]|max_length[20]'
            ),
        array(
                'field' =>  'initial_balance',
                'label' =>  'Bank Branch Name',
                'rules' =>  'xss_clean|trim|currency'
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
            ))
        );
    }

    function _sacco_exists(){
        $sacco_id = $this->input->post('sacco_id');
        if($this->saccos_m->get($sacco_id)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_sacco_exists','Kindly select a different sacco as the selected does not exist');
            return FALSE;
        }
    }

    function _is_unique_account(){
        $account_number = $this->input->post('account_number');
        $sacco_id = $this->input->post('sacco_id');
        $id = $this->input->post('id');
        $account_exists = $this->sacco_accounts_m->check_if_account_exists($id,$account_number,$sacco_id);
        if($account_exists){
            $this->form_validation->set_message('_is_unique_account','Sorry the account number '.'`'.$account_number.'`'.' is already registered and cannot allow duplicate');
            return FALSE;
        }
        else{
            return TRUE;
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
        $_POST['account_slug'] = generate_slug($this->input->post('account_name'));
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $this->form_validation->set_rules($this->validation_rules);
                    if($this->form_validation->run()){
                        $id = $this->sacco_accounts_m->insert(array(
                            'group_id'          =>  $this->group->id,
                            'account_number'    =>  $this->input->post('account_number'),
                            'account_name'      =>  $this->input->post('account_name'),
                            'initial_balance'   =>  $this->input->post('initial_balance'),
                            'sacco_branch_id'    =>  $this->input->post('sacco_branch_id'),
                            'sacco_id'           =>  $this->input->post('sacco_id'),
                            'created_by'        =>  $this->user->id,
                            'created_on'        =>  time(),
                            'active'            =>  1,
                        ));
                        if($id){
                            $this->session->set_flashdata('success', 'Group Sacco Account was successfully added');
                            $response = array(
                                'status' => 1,
                                'message' => 'success',
                                'id' => $id,
                                'name' => $this->input->post('account_name'),
                            );
                        }
                        else{
                            $response = array(
                                'status' => 0,
                                'message' => 'There was an error adding new Group Sacco Account',
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
                        'message' => 'Could not find Applicant Details
',
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

    function edit(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $_POST['account_slug'] = generate_slug($this->input->post('account_name'));
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $id = $this->input->post('id');
                    if($post = $this->sacco_accounts_m->get_group_sacco_account($id,$this->group->id)){
                        $this->form_validation->set_rules($this->validation_rules);
                        if($this->form_validation->run()){
                            $id = $this->sacco_accounts_m->update($post->id,array(
                                'account_number'    =>  $this->input->post('account_number'),
                                'account_name'      =>  $this->input->post('account_name'),
                                'initial_balance'   =>  $this->input->post('initial_balance'),
                                'sacco_branch_id'    =>  $this->input->post('sacco_branch_id'),
                                'sacco_id'           =>  $this->input->post('sacco_id'),
                                'modified_by'        =>  $this->user->id,
                                'modified_on'        =>  time(),
                            ));
                            if($id){
                                $this->session->set_flashdata('success', 'Group Sacco Account was successfully added');
                                $response = array(
                                    'status' => 1,
                                    'message' => 'success',
                                    'id' => $post->id,
                                    'name' => $this->input->post('account_name'),
                                );
                            }
                            else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'There was an error adding new Group Sacco Account',
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
                            'message' => 'Could not find group sacco account',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

    function get_group_sacco_accounts_list(){
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
                    $posts = $this->sacco_accounts_m->get_group_sacco_accounts($this->group->id);
                    $saccos=array();
                    foreach ($posts as $post) {
                        $saccos[] = array(
                            'id' => $post->id,
                            'account_name' => $post->account_name,
                            'description' => $post->sacco_name.' ('.$post->sacco_branch.') - '.$post->account_number,
                            'account_balance' => $post->initial_balance + $post->current_balance,
                            'sacco_id' => $post->sacco_id,
                            'sacco_branch_id' => $post->sacco_branch_id,
                            'account_number' => $post->account_number,
                            'sacco_name' => $post->sacco_name,
                            'sacco_branch' => $post->sacco_branch,
                            'is_closed' => $post->is_closed?1:0,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'successful',
                        'saccos' => $saccos,
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

    function close(){
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
                    if($this->member->group_role_id || $this->member->is_admin){
                        $id = $this->input->post('id');
                        if($post = $this->sacco_accounts_m->get_group_sacco_account($id,$this->group->id)){
                            if($post->is_closed){
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Sorry, the sacco account is already closed',
                                );
                            }
                            $update = array(
                                'is_closed'=>1,
                                'modified_by'=>$this->user->id,
                                'modified_on'=>time()
                            );
                            if($res = $this->sacco_accounts_m->update($post->id,$update)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Sacco account successfully closed',
                                );
                            }else{
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Unable to close the sacco account',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not close group sacco account. Select a different account',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

    function activate(){
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
                    if($this->member->group_role_id || $this->member->is_admin){
                        $id = $this->input->post('id');
                        if($post = $this->sacco_accounts_m->get_group_sacco_account($id,$this->group->id)){
                            if(!$post->is_closed){
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Sorry, the sacco account is already activate',
                                );
                            }
                            $update = array(
                                'is_closed'=>0,
                                'modified_by'=>$this->user->id,
                                'modified_on'=>time()
                            );
                            if($res = $this->sacco_accounts_m->update($post->id,$update)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Sacco account successfully activated',
                                );
                            }else{
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Unable to activate the sacco account',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not activate group sacco account. Select a different account',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

    function delete(){
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
                    if($this->member->group_role_id || $this->member->is_admin){
                        $id = $this->input->post('id');
                        $identity = $this->user->email?:$this->user->phone;
                        $password = $this->input->post('password');
                        if($this->ion_auth->login($identity,$password)){
                            if($post = $this->sacco_accounts_m->get_group_sacco_account($id,$this->group->id)){
                                if($this->transaction_statements_m->check_if_group_account_has_transactions('sacco-'.$post->id,$post->group_id)){
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'The sacco account has transactions associated to it, void all transactions associated to this account before deleting it',
                                    );
                                }else{
                                    if($this->sacco_accounts_m->delete($post->id,$post->group_id)){
                                        $response = array(
                                            'status' => 1,
                                            'message' => 'Sacco account deleted successfully',
                                        );
                                    }else{
                                        $response = array(
                                            'status' => 0,
                                            'message' => 'Sacco account could not be deleted',
                                        );
                                    }
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Could not delete group sacco account. Select a different account',
                                );
                            }

                            
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'You entered the wrong password.',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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