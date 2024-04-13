<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{
	private $data = array();

	function __construct(){
        parent::__construct();
        $this->load->model('accounts/accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
    }

	function index(){
        $data = array();
        $data['active_accounts'] = $this->accounts_m->get_active_group_account_options();
        $this->template->title(translate('Loan Applications'))->build('group/loan_applications',$data);
    }

    function respond($id = 0){
    	$id or redirect('group/loan_applications');
    	$loan_application = $this->loan_applications_m->get($id);
    	$loan_application or redirect('group/loan_applications');
    	if($loan_application->is_approved || $loan_application->is_declined){
    		$this->session->set_flashdata('warning','The loan application has already been'.$loan_application->is_approved?' approved':' declined.');
    		redirect('group/loan_applications');
    	}
    	$signatory_approval_request = $this->loans_m->get_loan_application_signatory_request($id,$this->member->id);
    	$signatory_approval_request or redirect('group/loan_applications');
        if($signatory_approval_request->is_approved || $signatory_approval_request->is_declined){
            $this->session->set_flashdata('warning','You have already'.($loan_application->is_approved?' approved':' declined').' the loan application.');
            redirect('group/loan_applications');
        }
    	$this->data['account'] = $this->accounts_m->get_group_account($loan_application->account_id);
    	$this->data['loan_type_options'] = $this->loan_types_m->get_options();
    	$this->data['signatory_approval_request'] = $signatory_approval_request;
    	$this->data['loan_application'] = $loan_application;
        $this->data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        $this->data['banks'] = $this->banks_m->get_group_bank_options();
        $this->data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $this->data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $this->template->set_layout('default_full_width.html')->title('Respond to Withdrawal Request')->build('group/respond',$this->data);

    }
}