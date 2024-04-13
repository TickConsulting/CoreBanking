<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{

    protected $data = array();
    
    protected $validation_rules = array(
        array(
            'field' => 'transfer_date',
            'label' => 'Transfer Date',
            'rules' => 'required|trim',
        ),array(
            'field' => 'from_account_id',
            'label' => 'Account to transfer from',
            'rules' => 'required|trim|callback__is_from_account_id_equal_to_to_account_id',
        ),
        array(
            'field' => 'to_account_id',
            'label' => 'Account to transfer to',
            'rules' => 'required|trim',
        ),
        array(
            'field' => 'amount',
            'label' => 'Amount',
            'rules' => 'required|trim|currency|callback__is_less_than_or_equal_from_account_balance',
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'trim',
        ),
    );
    
    function __construct(){
        parent::__construct();
        $this->load->model('accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->library('transactions');
    }

    function index(){
        $this->template->title(translate('Group Banking Accounts'))->build('group/index');
    }

    function record_transfer(){
        $post = new stdClass();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $transfer_date = strtotime($this->input->post('transfer_date'));
            $from_account_id = $this->input->post('from_account_id');
            $to_account_id = $this->input->post('to_account_id');
            $amount = valid_currency($this->input->post('amount'));
            $description = $this->input->post('description');
            if($this->transactions->record_account_transfer($this->group->id,$transfer_date,$from_account_id,$to_account_id,$amount,$description)){
                $this->session->set_flashdata('success','Account transfer recorded successfully');
            }else{
                $this->session->set_flashdata('error','Account transfer not recorded successfully');
            }
            redirect('group/withdrawals/listing');
        }else{
            foreach ($this->validation_rules as $key => $field){
                $field_name = $field['field'];
                $post->$field_name = set_value($field_name);
            }
        }
        $data['banks'] = $this->banks_m->get_group_bank_options();
        $data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $data['post'] = $post;
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        $this->template->title(translate('Record Account to Account Money Transfer'))->build('group/record_transfer',$data);
    }

    function _is_from_account_id_equal_to_to_account_id(){
        if($this->input->post('from_account_id')==$this->input->post('to_account_id')){
            $this->form_validation->set_message('_is_from_account_id_equal_to_to_account_id', 'You cannot transfer money to the same account.');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    function _is_less_than_or_equal_from_account_balance(){
        if($this->input->post('amount')&&$this->input->post('from_account_id')){
            $amount = valid_currency($this->input->post('amount'));
            $account_id = $this->input->post('from_account_id');
            $account_balance = $this->accounts_m->get_group_account_balance($account_id);
            if($amount>$account_balance){
                $this->form_validation->set_message('_is_less_than_or_equal_from_account_balance', 'You cannot transfer an amount greater than the account balance.');
                return FALSE;
            }else{
                return TRUE;
            }
        }else{
            return TRUE;
        }
    }
}

?>