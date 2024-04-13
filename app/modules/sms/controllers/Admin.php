<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller{
    
    protected $data= array();

	function __construct(){
        parent::__construct();
        $this->load->model('sms_m');
        $this->load->model('groups/groups_m');
    }

    function index(){
    	
    }

    function listing(){
        $from = $this->input->get('from')?strtotime($this->input->get('from')):0;
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $group_id = $this->input->get('group_id');
        $total_rows = $this->sms_m->count_all($group_id,$from,$to);
        $pagination = create_pagination('admin/sms/listing/page/', $total_rows,250,5);
        $this->data['pagination'] = $pagination;
        $posts = $this->sms_m->limit($pagination['limit'])->get_all($group_id,$from,$to);
        $this->data['posts'] = $posts;
        $this->data['groups'] = $this->groups_m->get_options();
        $this->template->title('SMS Log')->build('admin/listing',$this->data);
    }

    function queued_smses(){
        $group_id = $this->input->get('group_id');
        $total_rows = $this ->sms_m->count_all_queued_smses($group_id);
        $pagination = create_pagination('admin/sms/queued_smses/page/', $total_rows,250,5);
        $this->data['pagination'] = $pagination;
        $posts = $this->sms_m->limit($pagination['limit'])->get_sms_queue($group_id);
        $this->data['posts'] = $posts;
        $this->data['groups'] = $this->groups_m->get_options();
        $this->template->title('Queued SMSes')->build('admin/queued_smses',$this->data);
    }

    function delete($id=0,$redirect=TRUE){
        $id OR redirect('admin/sms/queued_sms');
        $post = $this->sms_m->get_queued_sms($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the sms does not exist');
            if($redirect){
                redirect('admin/sms/queued_smses');
            }
            return FALSE;
        }
        $delete_id = $this->sms_m->delete_sms_queue($id);
        if($delete_id){
            $this->session->set_flashdata('success','Successfully deleted');
            if($redirect){
                redirect('admin/sms/queued_smses');
            }
            return TRUE;
        }else{
            $this->session->set_flashdata('error','Unable to delete the queued smses');
            if($redirect){
               redirect('admin/sms/queued_smses'); 
            }
            return FALSE;
        }
    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_delete'){
            for($i=0;$i<count($action_to);$i++){
                $this->delete($action_to[$i],FALSE);
            }
        }
        redirect('admin/sms/queued_smses');
    }
  
}