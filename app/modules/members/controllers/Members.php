<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Members extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('members_m');
        $this->load->model('groups/groups_m');
        $this->load->helper('string');
    }

    // public function self_member_registration($join_code=0){
    //     $join_code OR redirect('/');

    //     $group = $this->groups_m->get();

    //     $this->data = array();

    //     $this->data['group_name'] = $group->name;

    //     //$join_code = strtolower(random_string('alnum', 6));

    //     //$join_code = random_string('alnum', 6,$group_id);

    //     $join_code = base_convert($group_id, 10, 36);

    //     echo $join_code;

    //     //$this->template->set_layout('authentication.html')->title('Self Member Registration')->build('member/self_member_registration', $this->data);
    // }
}