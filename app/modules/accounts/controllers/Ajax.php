<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends Ajax_Controller{ 

	protected $data = array();
    
    protected $validation_rules = array(
        array(
            'field' => 'transfer_date',
            'label' => 'Transfer Date',
            'rules' => 'required|trim|xss_clean|callback__valid_date',
        ),array(
            'field' => 'from_account_id',
            'label' => 'Account to transfer from',
            'rules' => 'required|trim|xss_clean|callback__is_from_account_id_equal_to_to_account_id',
        ),
        array(
            'field' => 'to_account_id',
            'label' => 'Account to transfer to',
            'rules' => 'required|trim|xss_clean',
        ),
        array(
            'field' => 'amount',
            'label' => 'Amount',
            'rules' => 'required|trim|xss_clean|currency|callback__is_less_than_or_equal_from_account_balance',
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'trim|xss_clean',
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
                $this->form_validation->set_message('_is_less_than_or_equal_from_account_balance', 'You cannot transfer an amount greater than the account balance. Account balance is :&nbsp;'. $account_balance );
                return FALSE;
            }else{
                return TRUE;
            }
        }else{
            return TRUE;
        }
    }

    function _valid_date(){
        $date = $this->input->post('transfer_date');
        if(valid_date($date)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_valid_date','Kindly use a valid date');
            return FALSE;  
        }
    }
/* 
    function _valid_date(){
        $date = $this->input->post('transfer_date');
        if($date){
            $last_timestamp = strtotime('-2 years', time());
            $today = time();
            $tomorrow_timestamp = strtotime('+1 day', time());
            $date_check = strtotime($date);
            if($date_check > $last_timestamp){
                if(date('dm',$date_check) < date('dm',$tomorrow_timestamp)){
                    if(date('y',$date_check) <= date('y',$tomorrow_timestamp)){
                        return TRUE;
                    }else{
                        $this->form_validation->set_message('_valid_date','Kindly use a valid date');
                        return FALSE;
                    }
                }else{
                    $this->form_validation->set_message('_valid_date','Kindly use a valid date');
                    return FALSE;  
                }
            }else{
                $this->form_validation->set_message('_valid_date','Kindly use a valid date');
                return FALSE;
            }
        }else{
            $this->form_validation->set_message('_valid_date','Kindly use a valid date');
            return FALSE;  
        }
    } */


    function ajax_record_transfer(){
    	$response = array();
    	$this->form_validation->set_rules($this->validation_rules);
    	if($this->form_validation->run()){
    		$transfer_date = strtotime($this->input->post('transfer_date'));
            $from_account_id = $this->input->post('from_account_id');
            $to_account_id = $this->input->post('to_account_id');
            $amount = valid_currency($this->input->post('amount'));
            $description = $this->input->post('description');
            if(valid_date($this->input->post('transfer_date'))){
                if($this->transactions->record_account_transfer($this->group->id,$transfer_date,$from_account_id,$to_account_id,$amount,$description)){
                	$response = array(
    	                'status' => 1,
    	                'message' => 'Account transfer recorded successfully.',
    	                'refer'=> site_url('group/withdrawals/listing')
    	            );
                }else{
                	$response = array(
    	                'status' => 1,
    	                'message' => 'Account transfer not recorded successfully',
    	                'refer'=> site_url('group/withdrawals/listing')
    	            );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Use a valid date.',
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