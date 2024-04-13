<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{

    protected $validation_rules = array(
        array(
                'field' =>  'account_name',
                'label' =>  'Account Name',
                'rules' =>  'required|trim'
            ),
        array(
                'field' =>  'sacco_id',
                'label' =>  'Sacco Name',
                'rules' =>  'required|trim|numeric'
            ),
        array(
                'field' =>  'sacco_branch_id',
                'label' =>  'Sacco Branch Name',
                'rules' =>  'required|trim|numeric'
            ),
        array(
                'field' =>  'account_number',
                'label' =>  'Account Number',
                'rules' =>  'required|trim|numeric|callback__is_unique_account|min_length[5]|max_length[20]'
            ),
        array(
                'field' =>  'initial_balance',
                'label' =>  'Bank Branch Name',
                'rules' =>  'trim|currency'
            ),

        );

    protected $data = array();
	
	function __construct(){
        parent::__construct();
        $this->load->model('sacco_accounts_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('sacco_branches/sacco_branches_m');
        $this->load->model('transaction_statements/transaction_statements_m');
    }

    function _is_unique_account()
    {
        $account_number = $this->input->post('account_number');
        $sacco_id = $this->input->post('sacco_id');
        $id = $this->input->post('id');

        $account_exists = $this->sacco_accounts_m->check_if_account_exists($id,$account_number,$sacco_id);
        if($account_exists)
        {
            $this->form_validation->set_message('_is_unique_account','Sorry the account number '.'`'.$account_number.'`'.' is already registered and cannot allow duplicate');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    function index(){
        $this->template->title('Group Sacco Accounts List')->build('group/listing',$this->data);
    }


    public function create()
    {
        $post = new stdClass();
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
                if($this->input->post('new_item'))
                {
                    redirect('group/sacco_accounts/create','refresh');
                }
                else
                {
                    redirect('group/sacco_accounts/listing','refresh');
                }
            }
            else
            {
                $this->session->set_flashdata('error', 'There was an error adding new Group Sacco Account');
                redirect('group/sacco_accounts/create','refresh');
            }
        }
        else{
            foreach ($this->validation_rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }

        $this->data['post'] = $post;
        $this->data['id'] = '';
        $this->data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $this->template->title('Create Group Sacco Account')->build('group/form',$this->data);
    }


    public function ajax_create(){
        $data = array();
        $response = array();
        $post = new stdClass();
        $posts = $_POST;
        $message = '';
        $response = array();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $sacco_account = array(
                'group_id'          =>  $this->group->id,
                'account_number'    =>  $this->input->post('account_number'),
                'account_name'      =>  $this->input->post('account_name'),
                'initial_balance'   =>  $this->input->post('initial_balance'),
                'sacco_branch_id'    =>  $this->input->post('sacco_branch_id'),
                'sacco_id'           =>  $this->input->post('sacco_id'),
                'created_by'        =>  $this->user->id,
                'created_on'        =>  time(),
                'active'            =>  1,
            );
            $id = $this->sacco_accounts_m->insert($sacco_account);
            if($id){
                if($sacco_account = $this->sacco_accounts_m->get_group_sacco_account($id)){
                    $saccos = $this->saccos_m->get_group_sacco_options();
                    $sacco_branches = $this->sacco_branches_m->get_sacco_branch_options_by_sacco_id($sacco_account->sacco_id);
                    $sacco_account->sacco_details = $saccos[$sacco_account->sacco_id].' ('.$sacco_branches[$sacco_account->sacco_branch_id].')';
                    $sacco_account->sacco_name = $saccos[$sacco_account->sacco_id];
                    $sacco_account->sacco_branch = $sacco_branches[$sacco_account->sacco_branch_id];
                    $sacco_account->sacco_account_id = $sacco_account->id;
                    $response = array(
                        'status' => 1,
                        'sacco_account'=>$sacco_account,
                        'message' => 'Sacco Account created successfully',
                        'refer'=>site_url('group/sacco_accounts/listing')
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not add find any sacco account',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not add sacco account',
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
                'message' => 'There are some errors on the form. Please review and try again.',
                'validation_errors' => $post,
            );            
        }
        echo json_encode($response);
    }

    public function ajax_edit(){
        $response = array();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $id = $this->input->post('id');
            $result = $this->sacco_accounts_m->update($id,array(
                'group_id'          =>  $this->group->id,
                'account_number'    =>  $this->input->post('account_number'),
                'account_name'      =>  $this->input->post('account_name'),
                'sacco_branch_id'    =>  $this->input->post('sacco_branch_id'),
                'sacco_id'           =>  $this->input->post('sacco_id'),
                'modified_by'        =>  $this->user->id,
                'modified_on'        =>  time()
            ));
            if($result){
                if($sacco_account = $this->sacco_accounts_m->get_group_sacco_account($id)){
                    $saccos = $this->saccos_m->get_group_sacco_options();
                    $sacco_branches = $this->sacco_branches_m->get_sacco_branch_options_by_sacco_id($sacco_account->sacco_id);
                    $sacco_account->sacco_details = $saccos[$sacco_account->sacco_id].' ('.$sacco_branches[$sacco_account->sacco_branch_id].')';
                    $sacco_account->sacco_name = $saccos[$sacco_account->sacco_id];
                    $sacco_account->sacco_branch = $sacco_branches[$sacco_account->sacco_branch_id];
                    $sacco_account->sacco_account_id = $sacco_account->id;
                    $response = array(
                        'status' => 1,
                        'sacco_account'=>$sacco_account,
                        'refer'=>site_url('group/sacco_accounts/listing'),
                        'message' => 'Edited successfully.',
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not add find any bank account.',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not add bank account.',
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
                'message' => 'There are some errors on the form. Please review and try again.',
                'validation_errors' => $post,
            );
        }
        echo json_encode($response);
    }

    function ajax_get_sacco_branches()
    {
        $sacco_id = $this->input->post('sacco_id');
        $post = $this->saccos_m->get($sacco_id);
        $branch_id = $this->input->post('branch_id');
        if($sacco_id)
        {
            $branches = $this->sacco_branches_m->get_sacco_branch_options_by_sacco_id($sacco_id);
            // print_r($sacco_id); die;
            echo form_dropdown('sacco_branch_id',array(''=>'--Select '.$post->name.' branch--')+$branches,$branch_id?:'','class="form-control select2" id="sacco_branch_id"');
        }
    }

    function edit($id=0)
    {
        $id OR redirect('group/sacco_accounts/listing');

        $post = new StdClass();

        $post = $this->sacco_accounts_m->get($id);
        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the sacco account does not exist');
            redirect('group/sacco_accounts/listing');
            return FALSE;
        }

        $this->form_validation->set_rules($this->validation_rules);

        if($this->form_validation->run())
        {
            $update = $this->sacco_accounts_m->update($post->id,array(
                    'group_id'          =>  $this->group->id,
                    'account_number'    =>  $this->input->post('account_number'),
                    'account_name'      =>  $this->input->post('account_name'),
                    'initial_balance'   =>  $this->input->post('initial_balance'),
                    'sacco_branch_id'   =>  $this->input->post('sacco_branch_id'),
                    'sacco_id'          =>  $this->input->post('sacco_id'),
                    'modified_by'       =>  $this->user->id,
                    'modified_on'       =>  time(),
                ));

            if($update)
            {
                $this->session->set_flashdata('success', 'Group Sacco Account was successfully updates');
                if($this->input->post('new_item'))
                {
                    redirect('group/sacco_accounts/create','refresh');
                }
                else
                {
                    redirect('group/sacco_accounts/listing','refresh');
                }
            }
            else
            {
                $this->session->set_flashdata('error', 'There was an error updating Group Sacco Account');
                redirect('group/sacco_accounts/edit/'.$id,'refresh');
            }

        }
        else
        {
            // Go through all the known fields and get the post values
            foreach (array_keys($this->validation_rules) as $field)
            {
                 if (isset($_POST[$field]))
                {
                    $post->$field = $this->form_validation->$field;
                }
            }
        }
        $this->data['post'] = $post;
        $this->data['id'] = $id;
        $this->data['saccos'] = $this->saccos_m->get_group_sacco_options();

        $this->template->title('Edit Group Sacco Account')->build('group/form',$this->data);
    }


    function listing(){
        $this->template->title('Group Sacco Accounts List')->build('group/listing',$this->data);
    }

    function hide($id=0,$redirect = TRUE)
    {
        $id OR redirect('group/sacco_accounts/listing');

        $post = $this->sacco_accounts_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the Sacco account does not exist');
            redirect('group/sacco_accounts/listing');
            return FALSE; 
        }

        if(!$post->active)
        {
            $this->session->set_flashdata('Error','Sorry, the Sacco account is already hidden');
            redirect('group/sacco_accounts/listing');
            return FALSE; 
        }

        $res = $this->sacco_accounts_m->update($post->id,array('active'=>NULL,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Sacco Account successfully hidden');
            if($redirect)
            {
                redirect('group/sacco_accounts/listing');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to hide the Sacco account');
            if($redirect)
            {
                redirect('group/sacco_accounts/listing');
            }
        }
    }

    function activate($id=0,$redirect = TRUE)
    {
        $id OR redirect('group/sacco_accounts/listing');

        $post = $this->sacco_accounts_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the Sacco account does not exist');
            redirect('group/sacco_accounts/listing');
            return FALSE; 
        }

        if($post->active)
        {
            $this->session->set_flashdata('Error','Sorry, the Sacco account is already active');
            redirect('group/sacco_accounts/listing');
            return FALSE; 
        }

        $res = $this->sacco_accounts_m->update($post->id,array('active'=>1,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Sacco Account successfully activated');
            if($redirect)
            {
                redirect('group/sacco_accounts/listing');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to actvate the Sacco account');
            if($redirect)
            {
                redirect('group/sacco_accounts/listing');
            }
        }
    }


    function close($id=0 , $redirect = TRUE)
    {
        $id OR redirect('group/sacco_accounts/listing');

        $post = $this->sacco_accounts_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the Sacco account does not exist');
            redirect('group/sacco_accounts/listing');
            return FALSE; 
        }

        if($post->is_closed)
        {
            $this->session->set_flashdata('Error','Sorry, the Sacco account is already closed');
            redirect('group/sacco_accounts/listing');
            return FALSE; 
        }

        $res = $this->sacco_accounts_m->update($post->id,array('is_closed'=>1,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Sacco Account successfully closed');
            if($redirect)
            {
                redirect('group/sacco_accounts/listing');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to close the Sacco account');
            if($redirect)
            {
                redirect('group/sacco_accounts/listing');
            }
        }
    }

    function reopen($id=0 , $redirect = TRUE)
    {
        $id OR redirect('group/sacco_accounts/listing');

        $post = $this->sacco_accounts_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the Sacco account does not exist');
            redirect('group/sacco_accounts/listing');
            return FALSE; 
        }

        if(!$post->is_closed)
        {
            $this->session->set_flashdata('Error','Sorry, the Sacco account is already open');
            redirect('group/sacco_accounts/listing');
            return FALSE; 
        }

        $res = $this->sacco_accounts_m->update($post->id,array('is_closed'=>NULL,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Sacco Account successfully re-opened');
            if($redirect)
            {
                redirect('group/sacco_accounts/listing');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to re-open the Sacco account');
            if($redirect)
            {
                redirect('group/sacco_accounts/listing');
            }
        }
    }

    function delete($id=0){
        if($id){
            $post = $this->sacco_accounts_m->get($id);
            if($this->user->id==$this->group->owner||$this->ion_auth->is_admin()){
                //$password = $this->input->post('password');
                //$identity = valid_phone($this->user->phone)?:$this->user->email;
                //if($this->ion_auth->login($identity,$password)){
                    if($this->transaction_statements_m->check_if_group_account_has_transactions('sacco-'.$post->id,$post->group_id)){
                        $this->session->set_flashdata('error','The sacco account has transactions associated to it, void all transactions associated to this account before deleting it');
                    }else{
                        if($this->sacco_accounts_m->delete($post->id,$post->group_id)){
                            $this->session->set_flashdata('success','Sacco account successfully deleted');
                        }else{
                            $response = array(
                                'status'=>0,
                                'message'=>'Sacco account could not be deleted'
                            );
                            $this->session->set_flashdata('error','Sacco account could not be deleted');
                        }
                    }
                // }else{
                //     $this->session->set_flashdata('error','You entered the wrong password');
                // }
            }else{
                $this->session->set_flashdata('error','You do not have sufficient permissions to delete Sacco account');
            }
        }else{
            $this->session->set_flashdata('error','Could not find sacco account');
        }
        redirect('group/sacco_accounts/listing');
    }


    /*function delete($id = 0){
        die();
        $id OR redirect('group/sacco_accounts/listing');
        $post = new stdClass();
        $post = $this->sacco_accounts_m->get($id);
        if($this->user->id==$this->group->owner){
            $password = $this->input->get('confirmation_string');
            $identity = valid_phone($this->user->phone)?:$this->user->email;
            if($this->ion_auth->login($identity,$password)){
                if($this->transaction_statements_m->check_if_group_account_has_transactions('sacco-'.$post->id,$post->group_id)){
                    $this->session->set_flashdata('warning','The sacco account has transactions associated to it, void all transactions associated to this account before deleting it');
                }else{
                    if($this->sacco_accounts_m->delete($post->id,$post->group_id)){
                        $this->session->set_flashdata('success','Sacco account deleted successfully');
                    }else{
                        $this->session->set_flashdata('error','Sacco account could not be deleted');
                    }
                }
            }else{
                $this->session->set_flashdata('warning','You entered the wrong password.');
            }
        }else{
            $this->session->set_flashdata('warning','You do not have sufficient permissions to delete a sacco account.');
        }
        redirect('group/sacco_accounts/listing');
    }*/


}