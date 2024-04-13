<?php 
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Member extends Member_Controller{

	public function __construct(){
        parent::__construct();
        $this->load->model('fines_m');
        $this->load->model('fine_categories/fine_categories_m');
        $this->load->library('transactions');
    }

    function index(){
        $this->template->title('Group Fines')->build('group/index');
    }

    function your_fines(){
        $data = array();
        $total_rows = $this->fines_m->count_group_and_member_fines();
        $pagination = create_pagination('member/fines/your_fines/pages', $total_rows,50,5,TRUE);
        $data['posts'] = $this->fines_m->limit($pagination['limit'])->get_group_and_member_fines();
        $data['pagination'] = $pagination;
        $data['fine_category_options'] = $this->fine_categories_m->get_group_options();
        $this->template->title('Your Fines')->build('member/listing',$data);
    }
    
    public function listing(){
        $data = array();
        $total_rows = $this->fines_m->count_group_fines();
        $pagination = create_pagination('member/fines/listing/pages', $total_rows,50,5,TRUE);
        $data['posts'] = $this->fines_m->limit($pagination['limit'])->get_group_fines();
        $data['pagination'] = $pagination;
        $data['fine_category_options'] = $this->fine_categories_m->get_group_options();
        $this->template->title('List Fines')->build('member/listing',$data);
    }

}