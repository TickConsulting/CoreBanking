<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{


    protected $data = array();

    function __construct()
    {
        parent::__construct();

        $this->load->model('loan_invoices_m');
        $this->load->library('loan');
    }

    function disable_invoice_penalties($id=0){
    	$id OR redirect($this->agent->referrer());
        $post = $this->loan_invoices_m->get_active($id);
        $post OR redirect($this->agent->referrer());
    	$diasabled = $this->loan_invoices_m->update($id,array('disable_fines'=>1,'modified_by'=>$this->user->id,'modified_on'=>time()));
    	if($diasabled){
            $this->loan->update_loan_invoices($post->loan_id);
    		$this->session->set_flashdata('success','Successfully disabled');
    	}
    	else
    	{
    		$this->session->set_flashdata('error','Unable to disable loan invoice Penalties');
    	}
    	redirect($this->agent->referrer());
    }

    function enable_invoice_penalties($id=0)
    {
    	$id OR redirect($this->agent->referrer());
        $post = $this->loan_invoices_m->get_active($id);
        $post OR redirect($this->agent->referrer());
    	$diasabled = $this->loan_invoices_m->update($id,array('disable_fines'=>NULL,'modified_by'=>$this->user->id,'modified_on'=>time()));
    	if($diasabled){
            $this->loan->update_loan_invoices($post->loan_id);
    		$this->session->set_flashdata('success','Successfully disabled');
    	}
    	else
    	{
    		$this->session->set_flashdata('error','Unable to disable loan invoice Penalties');
    	}
    	redirect($this->agent->referrer());
    }

    function disable_book_interest($id=0,$redirect=FALSE){
        $id OR redirect($this->agent->referrer());
        $post = $this->loan_invoices_m->get_active($id);
        $post OR redirect($this->agent->referrer());
        $diasabled = $this->loan_invoices_m->update($id,array('book_interest'=>'','modified_by'=>$this->user->id,'modified_on'=>time()));
        if($diasabled){
            if($redirect){
                $this->loan->update_loan_invoices($post->loan_id);
            }
            $this->session->set_flashdata('success','Successfully disabled');
            return $post->loan_id;
        }
        else
        {
            $this->session->set_flashdata('error','Unable to disable loan invoice Penalties');
        }
        if($redirect){
            redirect($this->agent->referrer());
        }
    }

    function enable_book_interest($id=0,$redirect = TRUE){
        $id OR redirect($this->agent->referrer());
        $post = $this->loan_invoices_m->get_active($id);
        $post OR redirect($this->agent->referrer());
        $diasabled = $this->loan_invoices_m->update($id,array('book_interest'=>1,'modified_by'=>$this->user->id,'modified_on'=>time()));
        if($diasabled){
            if($redirect){
                $this->loan->update_loan_invoices($post->loan_id);
            }
            $this->session->set_flashdata('success','Successfully disabled');
            return $post->loan_id;
        }
        else
        {
            $this->session->set_flashdata('error','Unable to disable loan invoice Penalties');
        }
        if($redirect){
            redirect($this->agent->referrer());
        }
    }

    function action(){
        $ids = $this->input->post('action_to');
        $btnAction = $this->input->post('btnAction');
        $loan_id = 0;
        if($btnAction == 'bulk_book_interest'){
            foreach ($ids as $id) {
                if($id){
                    $loan_id = $this->enable_book_interest($id,FALSE);
                }
            }
        }elseif ($btnAction == 'disable_bulk_book_interest') {
           foreach ($ids as $id) {
                if($id){
                    $loan_id = $this->disable_book_interest($id,FALSE);
                }
            }
        }
        $this->loan->update_loan_invoices($loan_id);
        $this->session->set_flashdata('success','Successfully updated');
        redirect($this->agent->referrer());
    }
}