<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends Member_Controller{

	public $invoice_type_options;

    protected $sms_template_default = '';


	function __construct(){
        parent::__construct();
        $this->load->model('invoices_m');
        $this->load->model('contributions/contributions_m');
        $this->load->library('contribution_invoices');
        $this->load->library('transactions');
        $this->invoice_type_options = array(
            1=>"Contribution invoice",
            2=>"Contribution fine invoice",
            3=>"Fine invoice",
            4=>"Miscellaneous invoice",
            //5=>"Back dated contribution invoice",
            //6=>"Back dated contrbution fine invoice",
            //7=>"Back dated fine invoice",
            //8=>"Back dated general invoice",
        );
        $this->sms_template_default = $this->contribution_invoices->sms_template_default;
    }

    function index(){
        $this->template->title('Group Invoices')->build('shared/index');
    }

    function listing(){
    	$data = array();
        $total_rows = $this->invoices_m->count_group_member_invoices();
        $pagination = create_pagination('member/invoices/listing/pages', $total_rows,50,5,TRUE);
    	$data['posts'] = $this->invoices_m->limit($pagination['limit'])->get_group_member_invoices();
    	$data['pagination'] = $pagination;
        $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
    	$data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
        $this->template->title('List Invoices')->build('member/listing',$data);
    }

}