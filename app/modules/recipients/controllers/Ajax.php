<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
	
	protected $data = array();

    protected $validation_rules=array(
        array(
            'field' =>  'account_name',
            'label' =>  'Recipient Name',
            'rules' =>  'xss_clean|trim',
        ),
        array(
            'field' =>   'type',
            'label' =>   'Recipient Type',
            'rules' =>   'xss_clean|trim|required|numeric',
        ),
        array(
            'field' =>  'description',
            'label' =>  'Recipient Description',
            'rules' =>  'xss_clean|trim',
        ),
    );
    
    function __construct(){
        parent::__construct();
        $this->load->model('recipients_m');
        $this->load->model('banks/banks_m');
    }

    function create(){
        $response = array();
        $this->_conditional_validation_rules();
    	$this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $default_bank = $this->banks_m->get_default_bank();
            $input = array();
            $type = $this->input->post('type');
            if($type == 1){ //mobile number
                $phone_number = valid_phone($this->input->post('phone_number'));
                $name = $this->input->post('name');
                $str = 'mobile-';
                $input = array(
                    'name' => $name,
                    'phone_number' => $phone_number,
                    'description' => $this->input->post('description'),
                    'type' => $type,
                    'group_id' => $this->group->id,
                    'is_hidden'    =>  0,
                    'active'    =>  1,
                    'created_by'    =>  $this->user->id,
                    'created_on'    =>  time(),
                );
                if($id = $this->recipients_m->insert($input)){
                    $response = array(
                        'status' => 1,
                        'message' => translate('Recipient account successfully added'),
                        'recipient' => array(
                            'id' => $id,
                            'name' => $name,
                            'phone_number' => $phone_number,
                        ),
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'We have experienced a technical error. Kindly lookup again',
                    ); 
                }
            }elseif($type == 2){ //paybill
                $str = 'paybill-';
                $input = array(
                    'name' => $this->input->post('name'),
                    'account_number' => $this->input->post('account_number'),
                    'paybill_number' => $this->input->post('paybill_number'),
                    'description' => $this->input->post('description'),
                    'type' => $this->input->post('type'),
                    'group_id' => $this->group->id,
                    'is_hidden'    =>  0,
                    'active'    =>  1,
                    'created_by'    =>  $this->user->id,
                    'created_on'    =>  time(),
                );

            }elseif($type == 3){ //bank
                $account_number = $this->input->post('account_number');
                if($recipient = $this->recipients_m->get_recipient_by_account_number($account_number,$type,$this->user->id)){
                    if($this->recipients_m->update($recipient->id,array(
                        'group_id' => $this->group->id,
                    ))){
                        $str = 'bank-';
                        $input = array(
                            'name' => $recipient->account_name,
                            'bank_id' => $default_bank->id,
                            'account_number' => $account_number,
                            'account_name' => $recipient->account_name,
                            'description' => '',
                            'type' => $type,
                            'group_id' => $this->group->id,
                            'is_hidden'    =>  0,
                            'active'    =>  1,
                            'created_by'    =>  $this->user->id,
                            'created_on'    =>  time(),
                        );
                        $response = array(
                            'status' => 1,
                            'message' => 'Success.',
                            'recipient' => array_merge($input, array('id' => $str.$recipient->id)),
                            'validation_errors' => '',
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not validate this account. Kindly refresh and try again.',
                            'validation_errors' => '',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not validate this account. Kindly refresh and try again.',
                        'validation_errors' => '',
                    );
                }
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

    function _conditional_validation_rules(){
        $type = $this->input->post('type');
        if($type == 1){ //mobile number
            $this->validation_rules [] = array(
                'field' =>   'phone_number',
                'label' =>   'Phone Number',
                'rules' =>   'trim|required|valid_phone',
            );
        }elseif($type == 2){ //paybill
            $this->validation_rules [] = array(
                'field' =>   'paybill_number',
                'label' =>   'Paybill Number',
                'rules' =>   'trim|required|numeric',
            );
            $this->validation_rules [] = array(
                'field' =>   'account_number',
                'label' =>   'Account Number',
                'rules' =>   'trim|required|numeric',
            );
        }elseif($type == 3){ //bank
            $this->validation_rules [] = array(
                'field' =>   'account_number',
                'label' =>   'Account Number',
                'rules' =>   'trim|required|numeric',
            );

            $this->validation_rules [] = array(
                'field' =>   'account_name',
                'label' =>   'Account Name',
                'rules' =>   'trim|required',
            );
        }
        
    }

    function get_group_bank_account_recipients(){
        $bank_account_recipients = $this->recipients_m->get_group_bank_account_recipient_options();
        echo form_dropdown('recipient',array(''=>'--Select Recipient--',0 => 'Create New Bank Account Recipient')+$bank_account_recipients,'','class="form-control m-select2-append" id="recipient"');
    }

    function get_group_mobile_money_account_recipients(){
        $mobile_money_account_recipients = $this->recipients_m->get_group_mobile_money_account_recipient_options();
        echo form_dropdown('recipient',array(''=>'--Select Recipient--',0 => 'Create New Mobile Money Recipient')+$mobile_money_account_recipients,'','class="form-control m-select2-append" id="recipient"');
    }

    function get_group_paybill_account_recipients(){
        $paybill_account_recipients = $this->recipients_m->get_group_paybill_account_recipient_options();
        echo form_dropdown('recipient',array(''=>'--Select Recipient--',0 => 'Create New Paybill Recipient')+$paybill_account_recipients,'','class="form-control m-select2-append" id="recipient"');
    }

    function lookup_account_details(){
        $response = array();
        $account_number = trim($this->input->post('account_number'));
        $recipient_bank_id = trim($this->input->post('recipient_bank_id'));
        $bank_id = trim($this->input->post('bank_id'));
        $ignore_bank_search = $this->input->post('ignore_bank_search');
        if($account_number && $recipient_bank_id){
            if($ignore_bank_search){
                $calling_code = $recipient_bank_id;
            }else{
                $bank = $this->banks_m->get($bank_id);
                $country = $this->countries_m->get($bank->country_id);
                $calling_code = substr($country->calling_code,-2);
            }
            if(is_numeric($account_number)){
                $account_details = $this->curl->equityBankRequests->account_lookup($account_number,$calling_code);
                if($account_details){
                    $default_bank = $this->banks_m->get_default_bank();
                    $input = array(
                        'name' => $account_details->account_name,
                        'bank_id' => $default_bank->id,
                        'account_number' => $account_details->account_number,
                        'account_name' => $account_details->account_name,
                        'account_currency' => $account_details->account_currency,
                        'cif' => $account_details->cif,
                        'type' => 3,
                        'group_id' => 0,
                        'is_hidden'    =>  0,
                        'active'    =>  1,
                        'created_by'    =>  $this->user->id,
                        'created_on'    =>  time(),
                    );
                    if($this->recipients_m->insert($input)){
                        $response = array(
                            'status' => 1,
                            'message' => 'Account lookup successful',
                            'account_name' =>$account_details->account_name,
                            'account_currency' => $account_details->account_currency,
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'We have experienced a technical error. Kindly lookup again',
                        ); 
                    }
                }else{
                   $response = array(
                        'status' => 0,
                        'message' => $this->session->flashdata('error'),
                    ); 
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Account number should be numeric characters only',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Account number or Recipient country is required',
            );
        }
        echo json_encode($response);
    }


}