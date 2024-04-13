<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller{
	
	function __construct(){
        parent::__construct();
        $this->load->model('activity_log_m');
        $this->load->model('groups/groups_m');
        $this->load->model('users/users_m');
    }

    function index(){
        $this->template->title('Activity Log')->build('admin/index');
    }

    function log(){
        $from = $this->input->get_post('from')?strtotime($this->input->get_post('from')):'';
        $to = $this->input->get_post('to')?strtotime($this->input->get_post('to')):strtotime('+1 day',time());
        $group_ids = $this->input->get_post('group_id');
        $total_rows = $this->activity_log_m->count_all($to,$from,$group_ids);
        $pagination = create_pagination('admin/activity_log/log/pages', $total_rows,1000,5,TRUE);
        $data['posts'] = $this->activity_log_m->limit($pagination['limit'])->get_all($to,$from,$group_ids);
        $data['pagination'] = $pagination;
        $data['group_options'] = $this->groups_m->get_options();
        $data['user_options'] = $this->users_m->get_options();
        $data['from'] = $from;
        $data['to'] = $to;
        $this->template->title('Activity Log')->build('admin/log',$data);
    }

}