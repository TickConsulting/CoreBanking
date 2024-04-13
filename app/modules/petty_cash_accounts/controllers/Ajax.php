<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
	
	protected $data = array();
    
    function __construct(){
        parent::__construct();
        $this->load->library('bank');
        $this->load->model('petty_cash_accounts_m');
    }

    protected $validation_rules = array(
        array(
            'field' =>  'account_name',
            'label' =>  'Account Name',
            'rules' =>  'xss_clean|trim|required'
        ),
        array(
            'field' =>  'slug',
            'label' =>  'Account Name',
            'rules' =>  'xss_clean|required|trim|callback__is_unique_account_name'
        ),
        array(
            'field' =>  'initial_balance',
            'label' =>  'Initial Petty Cash Account balances',
            'rules' =>  'xss_clean|trim'
        ),
    );

    function _is_unique_account_name(){
        $account_slug = $this->input->post('slug');
        $id = $this->input->post('id');
        if($this->petty_cash_accounts_m->check_if_account_exists($id,$account_slug)){
            $this->form_validation->set_message('_is_unique_account_name','Another Petty Cash Account by the name <strong>`'.$this->input->post('account_name').'`</strong> already exists');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    function create(){
        $response = array();
        $_POST['slug'] = generate_slug($this->input->post('account_name'));
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $petty_cash_account = array(
                'account_name'      =>  $this->input->post('account_name'),
                'account_slug'      =>  $this->input->post('slug'),
                'initial_balance'   =>  currency($this->input->post('initial_balance')?:0),
                'created_by'        =>  $this->user->id,
                'created_on'        =>  time(),
                'group_id'          =>  $this->group->id,
                'active'            =>  1,
            );
            $id = $this->petty_cash_accounts_m->insert($petty_cash_account);
            if($id){
                $petty_cash_account['id'] = $id;
                $response = array(
                    'petty_cash_account' => $petty_cash_account,
                    'status' => 1,
                    'refer' => site_url('group/petty_cash_accounts/listing'),
                    'message' => 'Petty cash account successfully added',
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not add petty cash account',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'There are some errors on the form. Please review and try again.',
                'validation_errors' => $this->form_validation->error_array(),
            );
        }
        echo json_encode($response);
    }

    function delete(){
        $response = array();
        $id = abs((int) filter_var($this->input->post('id'), FILTER_SANITIZE_NUMBER_INT));
        if($id){
            $post = $this->petty_cash_accounts_m->get_group_petty_cash_account($id);
            if($post){
                if($this->user->id==$this->group->owner||$this->ion_auth->is_admin()){
                    $password = $this->input->post('password');
                    $identity = valid_phone($this->user->phone)?:$this->user->email;
                    if($this->ion_auth->login($identity,$password)){
                        if($this->transaction_statements_m->check_if_group_account_has_transactions('petty-'.$post->id,$post->group_id)){
                            $response = array(
                                'status' => 0,
                                'message'=>'The petty cash account has transactions associated to it, void all transactions associated to this account before deleting it'
                            );
                        }else{
                            if($this->petty_cash_accounts_m->delete($post->id,$post->group_id)){
                                $response = array(
                                    'status' => 1,
                                    'message'=>'Petty cash account deleted successfullyy'
                                );
                            }else{
                                $response = array(
                                    'status'=>0,
                                    'message'=>'Petty cash account can not be deleted'
                                );
                            }
                        }
                    }else{
                        $response = array(
                            'status'=>0,
                            'message'=>'You entered the wrong password'
                        );
                    }
                }else{
                    $response = array(
                        'status'=>0,
                        'message'=>'You do not have sufficient permissions to delete this mobile money account'
                    );
                }
            }else{
                $response = array(
                    'status'=>0,
                    'message'=>'Could not find mobile money account'
                );
            }
        }else{
            $response = array(
                'status'=>0,
                'message'=>'Mobile money account id is required'
            );
        }
        echo json_encode($response);
    }

    function get(){
        $response = array();
        $id = abs((int) filter_var($this->input->post('id'), FILTER_SANITIZE_NUMBER_INT));
        if($id){
            $post = $this->petty_cash_accounts_m->get_group_petty_cash_account($id);
            if($post){
                echo json_encode($post);die;
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find the requested mobile money account',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find the requested mobile money account',
            );
        }
        echo json_encode($response);
    }

    public function edit(){
        $response = array();
        $id = $this->input->post('id');
        $post = $this->petty_cash_accounts_m->get($id);
        $_POST['slug'] = generate_slug($this->input->post('name'));
        if($post){
            $this->form_validation->set_rules($this->validation_rules);
            if($this->form_validation->run()){                
                $update = $this->petty_cash_accounts_m->update($post->id,array(
                    'account_name'      =>  $this->input->post('account_name'),
                    'account_slug'      =>  $this->input->post('slug'),
                    'initial_balance'   =>  currency($this->input->post('initial_balance')?:0),
                    'modified_by'       =>  $this->user->id,
                    'modified_on'       =>  time(),
                ));
                if($update){
                    $response = array(
                        'status' => 1,
                        'message' => 'Success petty cash account edited',
                        'refer'=>site_url('group/petty_cash_accounts/listing'),
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not update petty cash account',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => '',
                    'validation_errors' => $this->form_validation->error_array(),
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not not find petty cash account Details',
            ); 
        }
        echo json_encode($response);
    }
}