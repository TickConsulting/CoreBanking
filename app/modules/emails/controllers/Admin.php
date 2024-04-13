<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller
{

    protected $message_template;

	function __construct()
    {
        parent::__construct();

        $this->load->model('emails/emails_m');
        $this->load->model('users/users_m');
        $this->load->model('groups/groups_m');
    }

    function index(){
    	
    }


    function listing(){
        $group_id = $this->input->get('group_id');
        $from = $this->input->get('from')?strtotime($this->input->get('from')):0;
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $total_rows = $this ->emails_m->sent_emails_count($group_id,$from,$to);
        $pagination = create_pagination('admin/emails/listing/page/', $total_rows,250,5);
        $posts = $this->emails_m->limit($pagination['limit'])->get_all($group_id,$from,$to);
        $this->data['posts'] = $posts;
        $this->data['pagination'] = $pagination;
        $this->data['groups'] = $this->groups_m->get_options();
        $this->template->title('Emails Log')->build('admin/listing',$this->data);
    }

    function queued_emails(){
        $this->data['posts'] = $this->emails_m->get_email_queue();
        $this->data['groups'] = $this->groups_m->get_options();
        $this->template->title('Queued Emails')->build('admin/queued_emails',$this->data);
    }

    function delete($id=0,$redirect=TRUE){
        $id OR redirect('admin/emails/queued_emails');
        $post = $this->emails_m->get_email_from_queue($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the emails does not exist');
            if($redirect){
                redirect('admin/emails/queued_emails');
            }
            return FALSE;
        }
        $delete_id = $this->emails_m->delete_email_queue($id);
        if($delete_id){
            $this->session->set_flashdata('success','Successfully deleted');
            if($redirect){
                redirect('admin/emails/queued_emails');
            }
            return TRUE;
        }else{
            $this->session->set_flashdata('error','Unable to delete the queued smses');
            if($redirect){
               redirect('admin/emails/queued_emails'); 
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
        redirect('admin/emails/queued_emails');
    }

    function view($id=0){
        $id OR redirect('admin/emails/listing');
        $post = $this->emails_m->get($id);
        if(empty($post)){
            $this->session->set_flashdata('error','Sorry, the email does not exist');
            redirect('admin/emails/listing');
            return FALSE;
        }

        $this->data['post'] = $post;
        $this->data['group_name'] = $this->groups_m->get_group_owner($post->group_id);

        $this->template->title($post->subject.' - View Mail')->build('admin/view',$this->data);
    }

    function view_queued($id=0){
        $id OR redirect('admin/emails/listing');
        $post = $this->emails_m->get_email_from_queue($id);
        if(empty($post)){
            $this->session->set_flashdata('error','Sorry, the email does not exist');
            redirect('admin/emails/listing');
            return FALSE;
        }

        $this->data['post'] = $post;
        $this->data['group_name'] = $this->groups_m->get_group_owner($post->group_id);

        $this->template->title($post->subject.' - View Mail')->build('admin/view',$this->data);
    }
}