<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller{

	protected $data = array();

    function __construct(){
        parent::__construct();
        $this->load->model('withdrawals_m');
        $this->load->model('members/members_m');
        $this->load->library('transactions');
    }

    /*function failed_withdrawal_requests(){
    	$total_rows = $this->withdrawals_m->count_failed_withdrawal_requests();
        $pagination = create_pagination('admin/withdrawals/failed_withdrawal_requests/pages', $total_rows,50,5,TRUE);
        $this->data['withdrawal_request_transaction_names'] = $this->transactions->withdrawal_request_transaction_names;
    	$this->data['posts'] = $this->withdrawals_m->limit($pagination['limit'])->get_failed_withdrawal_requests();
    	$this->data['pagination'] = $pagination;
    	$this->data['group_options'] = $this->groups_m->get_options();
    	$this->template->title('Failed Withdrawal Requests')->build('admin/failed_withdrawal_requests',$this->data);
    }*/

    // function test(){
    //     print_r($this->withdrawals_m->get_withdrawal_request(1));die;

    //     $update = array(
    //         "disbursement_result_status" => 1,
    //         "disbursement_result_description" => 'Peter',
    //         "modified_on" => time(),
    //         'status' => 2,
    //         'active' => 1,
    //     );
    //     $update +=array(
    //         'is_disbursement_declined' => 1,
    //         'declined_on' => time(),
    //         'disbursement_failed_error_message' => 'Ola ola',
    //         "disbursement_status" => 4,
    //     );

    //     print_r($this->withdrawals_m->update_withdrawal_request(1,$update));die;
    // }
    function fix_chamasoft_imported_records($group_id = ''){
       $requests = $this->withdrawals_m->get_group_withdrawal_requests('',$group_id);
       foreach ($requests as $request) {
        // print_r(array_keys((array)$request)); die;
        $withdrawals_for = array(
            1=> 2,
            2=> 5,
            3=>2,
            4=>4,
            5=>1
        );
        $input = array(
            'withdrawal_for' => isset($withdrawals_for[$request->withdrawal_for])?$withdrawals_for[$request->withdrawal_for]:$request->withdrawal_for,
        );
        // print_r($input);
        print_r($this->withdrawals_m->update_withdrawal_request($request->id,$input));
       }
    }
    function fix(){
        $withdrawal_requests = $this->withdrawals_m->get_all_withdrawal_requests();
        foreach ($withdrawal_requests as $key => $value) {
            if($value->disbursement_failed_error_message && $value->is_approved){
                if(!$value->is_disbursed){
                    if($value->is_disbursement_declined){
                        //skip
                    }else{
                        $update =array(
                            'is_disbursement_declined' => 1,
                        );
                        print_r($this->withdrawals_m->update_withdrawal_request($value->id,$update));
                    }
                }
            }
        }
    }

    function declined_requests(){
        $total_rows = $this->withdrawals_m->count_declined_requests();
        $pagination = create_pagination('admin/withdrawals/declined_requests/pages', $total_rows,50,5,TRUE);
        $this->data['withdrawal_request_transaction_names'] = $this->transactions->withdrawal_request_transaction_names;
        $posts = $this->withdrawals_m->limit($pagination['limit'])->get_declined_requests();
        $withdrawal_approval_requests = $this->withdrawals_m->get_withdrawal_approval_requests_array($posts);
        $this->data['posts'] = $posts;
        $this->data['withdrawal_approval_requests'] = $withdrawal_approval_requests;
        $this->data['pagination'] = $pagination;
        $this->data['group_options'] = $this->groups_m->get_options();
        $this->template->title('Member Declined Requests')->build('admin/declined_requests',$this->data);
    }

    function ongoing_withdrawal_requests(){
        $total_rows = $this->withdrawals_m->count_ongoing_disbursement();
        $pagination = create_pagination('admin/withdrawals/pending_withdrawal_requests/pages', $total_rows,50,5,TRUE);
        $this->data['withdrawal_request_transaction_names'] = $this->transactions->withdrawal_request_transaction_names;
        $posts = $this->withdrawals_m->limit($pagination['limit'])->get_ongoing_disbursement();
        $this->data['posts'] = $posts;
        $withdrawal_approval_requests = $this->withdrawals_m->get_withdrawal_approval_requests_array($posts);
        $this->data['withdrawal_approval_requests'] = $withdrawal_approval_requests;
        $this->data['pagination'] = $pagination;
        $this->data['group_options'] = $this->groups_m->get_options();
        $this->template->title('Ongoing Withdrawal Requests')->build('admin/ongoing_withdrawal_requests',$this->data);
    }

    function pending_withdrawal_requests(){
    	$total_rows = $this->withdrawals_m->count_approved_withdrawal_requests_pending_disbursement();
        $pagination = create_pagination('admin/withdrawals/pending_withdrawal_requests/pages', $total_rows,50,5,TRUE);
        $this->data['withdrawal_request_transaction_names'] = $this->transactions->withdrawal_request_transaction_names;
    	$posts = $this->withdrawals_m->limit($pagination['limit'])->get_approved_withdrawal_requests_pending_disbursement();
        $withdrawal_approval_requests = $this->withdrawals_m->get_withdrawal_approval_requests_array($posts);
        $this->data['withdrawal_approval_requests'] = $withdrawal_approval_requests;
        $this->data['posts'] = $posts;
    	$this->data['pagination'] = $pagination;
    	$this->data['group_options'] = $this->groups_m->get_options();
    	$this->template->title('Pending Withdrawal Requests')->build('admin/pending_withdrawal_requests',$this->data);	
    }

    function disbursed_withdrawal_requests(){
        $total_rows = $this->withdrawals_m->count_disbursed_withdrawal_requests();
        $pagination = create_pagination('admin/withdrawals/disbursed_withdrawal_requests/pages', $total_rows,50,5,TRUE);
        $this->data['posts'] = $this->withdrawals_m->limit($pagination['limit'])->get_disbursed_withdrawal_requests();
        $this->data['pagination'] = $pagination;
        $this->data['group_options'] = $this->groups_m->get_options();
        $this->template->title('Disbursed Withdrawal Requests')->build('admin/disbursed_withdrawal_requests',$this->data);
    }

    function declined_withdrawal_requests(){
        $total_rows = $this->withdrawals_m->count_declined_withdrawal_requests();
        $pagination = create_pagination('admin/withdrawals/declined_withdrawal_requests/pages', $total_rows,50,5,TRUE);
        $this->data['withdrawal_request_transaction_names'] = $this->transactions->withdrawal_request_transaction_names;
        $this->data['posts'] = $this->withdrawals_m->limit($pagination['limit'])->get_declined_withdrawal_requests();
        $this->data['pagination'] = $pagination;
        $this->data['group_options'] = $this->groups_m->get_options();
        $this->template->title('Declined Withdrawal Requests')->build('admin/declined_withdrawal_requests',$this->data);
    }

    function restore_withdrawal_request($id = 0,$redirect = TRUE){
        $id OR redirect("admin/withdrawals/failed_withdrawal_requests");
        $post = $this->withdrawals_m->get_failed_withdrawal_request($id);
        $post OR redirect("admin/withdrawals/failed_withdrawal_requests");
        $input = array(
            'status' => 1,
            'disbursement_failed' => 0,
        );
        if($this->withdrawals_m->update_withdrawal_request($id,$input)){
            $this->session->set_flashdata('success',"Withdrawal request restored.");
        }else{
            $this->session->set_flashdata('success',"Withdrawal request could not restored.");
        }
        if($redirect){
            if($this->agent->referrer()){
                redirect($this->agent->referrer());
            }else{
                redirect("admin/withdrawals/failed_withdrawal_requests");
            }
        }else{
            return TRUE;
        }
    }
    
    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_restore'){
            for($i=0;$i<count($action_to);$i++){
                $this->restore_withdrawal_request($action_to[$i],FALSE);
            }
        }
        redirect('admin/withdrawals/failed_withdrawal_requests');
    }

    function retry_disbursement($id=0){
        $id OR redirect('admin/withdrawals/declined_withdrawal_requests');
        $post = $this->withdrawals_m->get_withdrawal_request($id);
        if(!$post){
            $this->session->set_flashdata('info','Could not find the selected withdrawal request');
            redirect('admin/withdrawals/declined_withdrawal_requests');
        }
        $update = array(
            'disbursement_result_status' => NULL,
            'disbursement_result_description'=> NULL,
            'disbursement_status' => NULL,
            'status' => 1,
            'is_approved'=>1,
            'is_disbursed' =>0,
            'is_declined'=>0,
            'disbursed_on' => NULL,
            'disbursement_charges' => NULL,
            'disbursement_receipt_number' => NULL,
            'is_disbursement_declined' => NULL,
            'disbursement_failed_error_message'  => NULL,
        );
        //print_r($post); die();
        if($this->withdrawals_m->update_withdrawal_request($post->id,$update)){
            $this->session->set_flashdata('success','successfully restored withdrawal disbursement declined request for retrying');
        }else{
            $this->session->set_flashdata('error','Could not find the selected withdrawal request');
        }
        redirect('admin/withdrawals/declined_withdrawal_requests');
    }

    function cancel_disbursement($id=0){
        $id OR redirect('admin/withdrawals/pending_withdrawal_requests');
        $post = $this->withdrawals_m->get_pending_withdrawal_request($id);
        if(!$post){
            $this->session->set_flashdata('error','Kindly select a pending withdrawal request to cancel');
            redirect('admin/withdrawals/pending_withdrawal_requests');
        }
        $reason = $this->input->get('confirmation_code');
        if($reason){
            $update = array(
                'disbursement_status' => 2,
                'status' => 2,
                'is_disbursement_declined' => 1,
                'disbursement_failed_error_message' => 'Admin declined: Reason - '.$reason,
                'modified_on' => time(),
            );
            if($this->withdrawals_m->update_withdrawal_request($post->id,$update)){
                $this->session->set_flashdata('success','Disbursement request successfully canceled');
            }else{
                $this->session->set_flashdata('error','Unable to cancel disbursement request');
            }
        }else{
            $this->session->set_flashdata('error','Kindly enter a valid reason to cancel a disbursement request');
        }
        redirect('admin/withdrawals/pending_withdrawal_requests');
    }
    function disburse_disbursement($id=0){
        $id OR redirect('admin/withdrawals/pending_withdrawal_requests');
        $post = $this->withdrawals_m->get_pending_withdrawal_request($id);
        if(!$post){
            $this->session->set_flashdata('error','Kindly select a pending withdrawal request to cancel');
            redirect('admin/withdrawals/pending_withdrawal_requests');
        }
        $reason = $this->input->get('confirmation_code');
        if($reason){
            $update = array(
                'is_disbursed' => 1,
                'status'=>3,
                'is_approved' => 1,
                'active' => 1,
                'disbursement_failed_error_message' => NULL,
                'disbursed_on'=>time(),
                'modified_on' => time(),
            );
            if($this->withdrawals_m->update_withdrawal_request($post->id,$update)){
                $this->session->set_flashdata('success','Disbursement request successfully Disbursed');
            }else{
                $this->session->set_flashdata('error','Unable to Disburse disbursement request');
            }
        }else{
            $this->session->set_flashdata('error','Kindly enter a valid reason to cancel a disbursement request');
        }
        redirect('admin/withdrawals/disbursed_withdrawal_requests');
    }

    function admin_update_disburment(){
        $ids = [3,4];
        foreach ($ids as $key => $value) {

            $update = array(
                'disbursement_result_status' => NULL,
                'disbursement_result_description'=> NULL,
                'disbursement_status' => NULL,
                'status' => 1,
                'is_approved'=>1,
                'is_disbursed' =>0,
                'is_declined'=>0,
                'disbursed_on' => NULL,
                'disbursement_charges' => NULL,
                'disbursement_receipt_number' => NULL,
                'is_disbursement_declined' => NULL,
                'disbursement_failed_error_message'  => NULL,
            );

            if($this->withdrawals_m->update_withdrawal_request($value,$update)){
                echo $value." Updated<br>";
            }else{
                echo $value." Failed<br>";
            }
        }
        
    }
}