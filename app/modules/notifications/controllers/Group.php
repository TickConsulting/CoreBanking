<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{

    protected $notification_status_options = array(
        0 => "Unread",
        1 => "Read",
    );

	function __construct(){
        parent::__construct();
        $this->load->model('notifications_m');
    }

    function index(){
        $data = array();
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $data['from'] = $from;
        $data['to'] = $to;
        $data['notification_status_options'] = $this->notification_status_options;
        $this->template->title('Notifications')->build('shared/index',$data);
    }

    function listing(){
    	$data = array();
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $data['from'] = $from;
        $data['to'] = $to;
        $data['notification_status_options'] = $this->notification_status_options;
        $this->template->title(translate('List Notifications'))->build('group/listing',$data);
    }


    function mark_as_read($id=0,$redirect=TRUE){
        $id OR redirect('group/notifications/listing');
        $post = $this->notifications_m->get_member_notification($id);    
        $post OR redirect('group/notifications/listing');
        $input = array(
            'is_read'=>1,
            'modified_by'=>$this->user->id,
            'modified_on'=>time(),
        );
        if($result = $this->notifications_m->update($id,$input)){
            $this->session->set_flashdata('success','Notification was successfully marked as read');
        }else{
            $this->session->set_flashdata('error','Unable to mark the notification as read ');
        }
        if($redirect){
            redirect('group/notifications/listing');
        }
        return TRUE;
    }

    function mark_as_unread($id=0,$redirect=TRUE){
        $id OR redirect('group/notifications/listing');
        $post = $this->notifications_m->get_member_notification($id);    
        $post OR redirect('group/notifications/listing');
        $input = array(
            'is_read'=>0,
            'modified_by'=>$this->user->id,
            'modified_on'=>time(),
        );
        if($result = $this->notifications_m->update($id,$input)){
            $this->session->set_flashdata('success','Notification was successfully marked as unread');
        }else{
            $this->session->set_flashdata('error','Unable to mark notification as unread');
        }
        if($redirect){
            redirect('group/notifications/listing');
        }
        return TRUE;
    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_mark_as_read'){
            for($i=0;$i<count($action_to);$i++){
                $this->mark_as_read($action_to[$i],FALSE);
            }
        }else if($action == 'bulk_mark_as_unread'){
            for($i=0;$i<count($action_to);$i++){
                $this->mark_as_unread($action_to[$i],FALSE);
            }
        }
        redirect('group/notifications/listing');
    }

}