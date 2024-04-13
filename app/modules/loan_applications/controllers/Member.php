<?php 
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Member extends Member_Controller{
    protected $data = array();

    public function __construct(){
    	parent::__construct();
    	$this->load->model('loan_applications_m');
    }

    function index(){
        $data = array();
        $data['active_accounts'] = $this->accounts_m->get_active_group_account_options();
        $this->template->title(translate('Loan Applications'))->build('member/loan_applications',$data);
    }

    function delete($id=0){
    	$id OR redirect('member/loans/loan_applications');
    	$post = $this->loan_applications_m->get_member_loan_application($id);
    	if(!$post){
    		$this->session->set_flashdata('error','Sorry the loan application is not available');
    		redirect('member/loans/loan_applications');
    	}
        if($post->status==1){
            if($this->loan_applications_m->update($post->id,array(
                    'is_deleted'=>1,
                    'modified_on'=>time(),
                    'modified_by'=>$this->user->id
                ))){
                $this->session->set_flashdata('success','Loan application successfully deleted');
            }else{
                $this->session->set_flashdata('error','Error deleting loan');
            }
            redirect('member/loans/loan_applications');
        }else{
            $this->session->set_flashdata('error','Sorry you cannot delete this loan');
            redirect('member/loans/loan_applications');
        }
    }

}
?>