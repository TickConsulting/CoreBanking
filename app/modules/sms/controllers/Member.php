<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends Member_Controller
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
        $this->template->title(translate('SMSes'))->build('member/index',$data);
    }

    function received_smses()
    {
        $total_rows = $this ->sms_m->count_all_group_member_received_smses();

        $pagination = create_pagination('member/listing/page/', $total_rows);

        $this->data['pagination'] = $pagination;

        $posts = $this->sms_m->limit($pagination['limit'])->get_all_group_member_received_smses();
        $this->data['posts'] = $posts;
        $this->template->title('Received SMSes')->build('member/listing',$this->data);
    }
  
}