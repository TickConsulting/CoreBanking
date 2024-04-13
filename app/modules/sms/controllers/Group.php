<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller
{
    protected $data= array();
    protected $members;
    protected $send_to_list = array(
            ' ' => '--Select members to message--',
            '1' => 'All Members',
            '2' => 'Individual Members',
        );

    protected $validation_rules = array(
            array(
                'field' =>  'send_to',
                'label' =>  'Send Message To',
                'rules' =>  'required|trim|numeric',
            ),
            array(
                'field' =>  'message',
                'label' =>  'Message',
                'rules' =>  'required',
            ),
        );

	function __construct()
    {
        parent::__construct();
        $this->load->model('members/members_m');
        $this->load->model('sms_m');

        $this->load->library('messaging');

        $this->data['send_to_list'] = $this->send_to_list;
        $this->members = $this->members_m->get_group_member_options_for_messaging();
    }
    
    
    public function index(){
        $data = array();
        $this->template->title(translate('Group SMSes'))->build('group/index',$data);
    }

    function listing(){
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $this->template->title('Sent SMSes')->build('group/listing',$this->data);
    }


    function create(){
        $post = new StdClass();

        if($this->input->post('send_to')==2){
            $this->validation_rules[] = array(
                'field' =>  'member_id[]',
                'label' =>  'Member Name',
                'rules' =>  'required'
            );
        }

        $this->form_validation->set_rules($this->validation_rules);

        if($this->form_validation->run()){
            if($this->input->post('send_to')==1){
                //all members of the group
                foreach($this->members as $key => $value) {
                    $member[] = $this->members_m->get_group_member($key);
                }
            }else{
                $member_id = $this->input->post('member_id');
                $member = array();
                foreach($member_id as $key => $value) {
                    $member[] = $this->members_m->get_group_member($value);
                }
            }
            $message = $this->input->post('message');
            $message_id = $this->messaging->create_and_queue_sms($member,$message,$this->user,$this->group->id);
            if($message_id){
                if($this->input->post('new_item')){
                    redirect('group/sms/create','refresh');
                }else{
                     redirect('group/sms/queued_sms','refresh');
                }
            }
            else{
                redirect('group/sms/listing','refresh');
            }
            
        }
        foreach($this->validation_rules as $key => $field) {
            $field_name = $field['field'];
            $post->$field_name = set_value($field['field']);
        }

        $this->data['members'] = $this->members;
        
        $this->template->title('Create SMS')->build('group/form',$this->data);
    }

    function queued_sms(){
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $this->template->title('Queued Group SMSes')->build('group/queued_sms',$this->data);
    }

    function delete($id=0,$redirect=TRUE){
        $id OR redirect('group/sms?a=queued_sms');
        $post = $this->sms_m->get_queued_sms($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the sms does not exist');
            if($redirect){
                redirect('group/sms?a=queued_sms');
            }
            return FALSE;
        }
        $delete_id = $this->sms_m->delete_sms_queue($id);
        if($delete_id){
            $this->session->set_flashdata('success','Successfully deleted');
            if($redirect){
                redirect('group/sms?a=queued_sms');
            }
            return TRUE;
        }else{
            $this->session->set_flashdata('error','Unable to delete the queued smses');
            if($redirect){
               redirect('group/sms?a=queued_sms'); 
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
        redirect('group/sms?a=queued_sms');
    }
  
}