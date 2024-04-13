<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loan_applications extends Public_Controller{

	function __construct(){
        parent::__construct();
        $this->load->library('loan');
        $this->load->model('loan_applications_m');
    }

    function process_approved_loan_applications(){
    	$this->loan->process_approved_loan_applications();
    }

    function remove_group_loan_application($id = 0){
        if($id){
            $loan_application = $this->loan_applications_m->get($id);
            if($loan_application){
                $approval_requests = $this->loan_applications_m->get_loan_application_approval_requests($id,$loan_application->group_id);
                $ids_array = array();
                $ids_array = array();
                foreach ($approval_requests as $approval_request) {
                   $this->loan_applications_m->update_loan_application_approval_request($approval_request->id,array('active' => 0));
                }
                $this->loan_applications_m->update($id,array('active' => 0));
            }else{
                echo "loan application details not found"; 
            }
        }else{
            echo "loan application id required";
        }
    }
}