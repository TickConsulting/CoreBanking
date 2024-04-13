<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends Member_Controller{

    protected $data = array();
    

    function __construct()
    {
        parent::__construct();
        $this->load->model('billing_m');
        $this->load->model('groups/groups_m');
        $this->load->library('billing_settings');
    }

    
    function index(){
        $this->template->title('Group Billing')->build('group/index');
    }

    function billing_information(){
        $post = new stdClass();
        $group = $this->group;
        $invoices = $this->billing_m->get_all_active_group_invoices();
        $payments = $this->billing_m->get_all_active_group_payments();
        if($group->status!=1 || $group->status!=2){
            $billing_payments = $this->billing_settings->get_amount_payable('','',$group->id,TRUE);
            if($billing_payments){
                $this->data['billing_payments'] = (object)$billing_payments;
            }
        }
        
        $this->data['payments'] = $payments;
        $this->data['post'] = $post;
        $this->data['invoices'] = $invoices;
        $this->data['group'] = $group;
        $this->data['country_options'] = $this->countries_m->get_country_options();
        $this->data['billing_cycles'] = $this->billing_settings->billing_cycle;
        $this->template->title('Billing Information')->set_layout('member_default.html')->build('group/billing_information',$this->data);
    }

    function group_account_information(){
        $post = new stdClass();
        $group = $this->group;
        if($this->input->post('confirm_subscription')){
            $subscription_rules = array(
                array(
                    'field' => 'agree_terms',
                    'label' => 'Agree with terms',
                    'rules' => 'required|trim',
                ),
                array(
                    'field' => 'billing_cycle',
                    'label' => 'Billing Cycle',
                    'rules' => 'required|trim',
                ),
            );

            $this->form_validation->set_rules($subscription_rules);
            if($this->form_validation->run()){
                $billing_cycle = $this->input->post('billing_cycle');
                $billing_package_id = $group->billing_package_id?:$this->application_settings->default_billing_package;

                $amount = $this->billing_settings->get_amount_payable($billing_package_id,$billing_cycle,$group->id,'','',TRUE);
                if($amount){
                    if($this->groups_m->update($group->id,array(
                            'billing_cycle' => $billing_cycle,
                            'billing_package_id' => $billing_package_id,
                            'status' => 1,
                        ))){
                        $billing_date= time();
                        $due_date = strtotime('+3 days',time());
                        $created_by = $this->user;
                        $invoice_id = $this->billing_settings->create_invoice($group->id,$billing_date,$due_date,$amount,$billing_cycle,$this->user,0,1);
                        if($invoice_id){
                            $this->session->set_flashdata('success','Successfully updated and created invoice');
                        }
                    }else{
                        $this->session->set_flashdata('error','Unable to update group details');
                    }
                }else{
                    $this->session->set_flashdata('error','Error generating amount payable');
                }
                redirect('group/billing/group_account_information');
            }else{
                foreach ($subscription_rules as $key => $field) {
                     $post->$field['field'] = set_value($field['field']);
                }
            }
        }

        $invoices = $this->billing_m->get_all_active_group_invoices();
        $payments = $this->billing_m->get_all_active_group_payments();
        if($group->status!=1 || $group->status!=2){
            $billing_payments = $this->billing_settings->get_amount_payable('','',$group->id,TRUE);
            if($billing_payments){
                $this->data['billing_payments'] = (object)$billing_payments;
            }
        }
        
        $this->data['payments'] = $payments;
        $this->data['post'] = $post;
        $this->data['invoices'] = $invoices;
        $this->data['group'] = $group;
        $this->data['country_options'] = $this->countries_m->get_country_options();
        $this->data['billing_cycles'] = $this->billing_settings->billing_cycle;
        $this->template->title('Group Account Information')->set_layout('default_full_width.html')->build('group/group_account_information',$this->data);
    }

    function invoice($id=0,$generate_pdf=FALSE)
    {
        $id OR redirect('group/billing/billing_information');
        $post = $this->billing_m->get_group_billing_invoice($id);

        if(empty($post)){
            $this->session->set_flashdata('error','Invoice not found');
            redirect('group/billing/billing_information');
            return FALSE;
        }

        $this->data['package'] = $this->billing_m->get_package($post->billing_package_id);
        $this->data['post'] = $post;
        $this->data['billing_cycles'] = $this->billing_settings->billing_cycle;
        $this->data['group'] = $this->groups_m->get_group_owner($post->group_id);
        $this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
        if($generate_pdf==TRUE){
            $response = $this->curl_post_data->curl_post_json_pdf((json_encode($this->data)),'https://pdfs.chamasoft.com/billing_invoices',$this->group->name.' - '.$this->application_settings->application_name.' billing invoice ');
            print_r($response);die;
        }else{
            $this->template->title($post->billing_invoice_number.' - Billing Invoice')->build('group/invoice',$this->data);
        }
    }

    function receipt($id=0 , $generate_pdf=FALSE)
    {
        $id OR redirect('group/billing/billing_information');
        $post = $this->billing_m->get_group_billing_receipt($id);

        if(empty($post)){
            $this->session->set_flashdata('error','Invoice not found');
            redirect('group/billing/billing_information');
            return FALSE;
        }

        $this->data['package'] = $this->billing_m->get_package($post->billing_package_id);
        $this->data['post'] = $post;
        $this->data['payment_methods'] = $this->billing_settings->payment_method;
        $this->data['group'] = $this->groups_m->get_group_owner($post->group_id);
        $this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
        if($generate_pdf==TRUE){
            $response = $this->curl_post_data->curl_post_json_pdf((json_encode($this->data)),'https://pdfs.chamasoft.com/billing_receipts',$this->group->name.' - '.$this->application_settings->application_name.' billing receipt ');
            print_r($response);die;
        }
        else{
            $this->template->title($post->billing_receipt_number.' - Billing Payment Receipt')->build('group/receipt',$this->data);
        }
    }

}