<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoices extends Public_Controller{

	function __construct(){
        parent::__construct();
        $this->load->model('statements/statements_m');
        $this->load->model('contributions/contributions_m');
        //$this->load->model('invoices/invoices_m');
        $this->load->library('contribution_invoices');
        $this->load->library('transactions');
    }

    function queue_contribution_invoices($date = ""){
        $this->output->enable_profiler(TRUE);
        $this->contribution_invoices->update_next_invoice_dates();
        $this->contribution_invoices->queue_regular_contribution_invoices($date);
    	$this->contribution_invoices->queue_one_time_contribution_invoices($date);
    }

    function queue_contribution_fine_invoices($date = 0,$ignore_contribution_fine_date = 0,$group_id = 0,$contribution_id = 0,$contribution_fine_setting_id = 0,$fine_date = 0){
        if($date){

        }else{
            $date = date("d-m-Y");
        }
        $this->contribution_invoices->queue_contribution_fine_invoices($date,$ignore_contribution_fine_date,$group_id,$contribution_id,$contribution_fine_setting_id,$fine_date);
    }

    function process_contribution_invoices_queue($limit = 10){
        $this->output->enable_profiler(TRUE);
    	$this->contribution_invoices->process_contribution_invoices_queue($limit);
    }

    function process_contribution_fine_invoices_queue($limit = 10,$group_id = 0){
        $this->output->enable_profiler(TRUE);
        $this->contribution_invoices->process_contribution_fine_invoices_queue($limit,$group_id);
    }

    function delete(){
        $this->invoices_m->delete_contribution_fine_invoicing_queue();
    }

    function count_queued_contribution_invoices(){
        $this->output->enable_profiler(TRUE);
        echo $this->invoices_m->count_queued_contribution_invoices();
    }


}