<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller{

    protected $data = array();
    protected $repayment_status_options = array(
        1 => "Fully Paid",
        0 => "In Progress",
    );

	function __construct(){
        parent::__construct();
        $this->load->model('bank_accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('bank_branches/bank_branches_m');
        $this->load->model('loan_types/loan_types_m');
        $this->load->model('loan_applications/loan_applications_m');
        $this->load->model('loan_applications/loan_applications_m');
    }

    function pending_bank_loan_approvals(){
        $this->data['repayment_status_options'] = $this->repayment_status_options;
        $this->template->title('Pending Bank Approval Member Loans')->build('group/pending_loan_bank_approvals',$this->data);
    }

    function delete_test_loans($id =0){
        if($id){
            $applications = $this->loan_applications_m->get($id);
            print_r($applications);
            die('Not Allowed');
            if($applications){
                if($applications->group_id == 4168){
                    if($this->loan_applications_m->safe_delete($applications->id,$applications->group_id)){
                         echo 'Successfully safe deleted';
                    }else{
                        echo 'Could not delete application doesnt exist';  
                    }
                }else{
                    echo 'group doesnt exist';
                }
            }else{
                echo 'loan application details is missing';  
            }
        }else{
            echo 'loan id is required';
        }

    }
}