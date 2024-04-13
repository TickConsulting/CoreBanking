<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
	
	protected $data = array();
    protected $validation_rules = array(
            array(
                'field' => 'member_id',
                'label' => 'Member name',
                'rules' => 'required|trim|numeric',
            ),
            array(
                'field' => 'refund_date',
                'label' => 'Refund date',
                'rules' => 'required|trim|callback__valid_date',
            ),
            array(
                'field' => 'contribution_id',
                'label' => 'Contribution to refund from',
                'rules' => 'required|trim|numeric',
            ),
             array(
                'field' => 'account_id',
                'label' => 'Account id',
                'rules' => 'required|trim',
            ),
             array(
                'field' => 'refund_method',
                'label' => 'Refund method',
                'rules' => 'required|trim',
            ),
            array(
                'field' => 'amount',
                'label' => 'Refund Amount',
                'rules' => 'required|trim|currency',
            ),
            array(
                'field' => 'description',
                'label' => 'Description',
                'rules' => 'trim',
            ),
        );
    
    function __construct(){
        parent::__construct();
        $this->data['active_contribution_options'] = $this->contributions_m->get_active_group_contribution_options();
    }

    function _valid_date(){
        $date = $this->input->post('refund_date');
        if(valid_date($date)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_valid_date','Kindly use a valid date');
            return FALSE;  
        }
    }

    function create(){
        $response = array();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $id = $this->transactions->record_contribution_refund(
                $this->group->id,
                strtotime($this->input->post('refund_date')),
                $this->input->post('member_id'),
                $this->input->post('account_id'),
                $this->input->post('contribution_id'),
                $this->input->post('refund_method'),
                $this->input->post('description'),
                $this->input->post('amount'),
                $this->user->id);
            if($id){
                $response = array(
                    'status' => 1,
                    'message' => 'Refund successfully created.',
                    'refer'=>site_url('group/withdrawals/listing')
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Refund was not successfully created',
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
    

}