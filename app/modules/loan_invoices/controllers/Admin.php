<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller{


    protected $data = array();

    function __construct()
    {
        parent::__construct();

        $this->load->model('loan_invoices_m');
        $this->load->library('loan');
    }

    function listing()
    {
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $filter_params = array(
                'from' => $from,
                'to' => $to,
                'group_id' => $this->input->get('group_id'),
            );
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $total_rows = $this->loan_invoices_m->count_sent_loan_invoices($filter_params);
        $pagination = create_pagination('admin/loan_invoices/listing/pages', $total_rows,50,5,TRUE);
    	$posts = $this->loan_invoices_m->limit($pagination['limit'])->get_all_sent_loan_invoices($filter_params);
        $this->data['group_options'] = $this->groups_m->get_options();
        $this->data['member_options'] = $this->members_m->get_options();
        $this->data['pagination'] = $pagination;
        $this->data['posts'] = $posts;
        $this->template->title('Sent Loan Invoices')->build('admin/listing',$this->data);
    }
}