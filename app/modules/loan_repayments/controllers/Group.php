<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{


    protected $data = array();

    function __construct()
    {
        parent::__construct();

        $this->load->model('loans/loans_m');
        $this->load->model('loan_invoices/loan_invoices_m');
        $this->load->model('loan_repayments_m');
        $this->load->library('loan');
    }


    function index()
    {
    	
    }

    function loan_repayment_per_id($id = 0){
        $results = $this->loan_repayments_m->get_loan_payment_by_loan_id($id);
        print_r($results); 

    }

}