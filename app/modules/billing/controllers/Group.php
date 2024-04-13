<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{

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
                redirect('group/billing/billing_information');
            }else{
                foreach ($subscription_rules as $key => $field) {
                     $post->$field['field'] = set_value($field['field']);
                }
            }
        }

        $invoices = $this->billing_m->get_all_active_group_invoices();
        $payments = $this->billing_m->get_all_active_group_payments();
        if($group->status!=1 || $group->status!=2){
            $billing_payments = $this->billing_settings->get_amount_payable($group->billing_package_id,'',$group->id,TRUE,'',TRUE);
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
        $active_size = $this->groups_m->get_group_active_size($this->group->id);
        $this->data['payable_amount'] = $this->billing_settings->get_amount_payable(0,3,$this->group->id,TRUE,$active_size);
        $this->template->title('Billing Information')->set_layout('default_full_width.html')->build('group/billing_information',$this->data);
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

    function make_mpesa_payment(){
        $service_ = $this->input->post('service_');
        $phone_number = valid_phone($this->input->post('phone_pay'));
        $amount = $this->input->post('amount_pay');
        $reference_number = $this->group->account_number;
        if($service_=='sms'){
            $reference_number='SMS'.$reference_number;
        }
        //print_r($phone_number); die;
        $response = $this->billing_settings->initiate_transaction_payment($amount,$phone_number,$this->group,$this->user,$reference_number,'');
        if($response){
            $response = array_merge((array)$response,array('status' => 1));
        }else{
            $response = array(
                'status' => 0,
                'message' => $this->session->flashdata('error'),
            );
        }
        echo json_encode($response);
    }

    function receive_complete_billing(){
        $amount = currency($this->input->post('amount'));
        $data = $this->input->post('data');
        $service_ = $this->input->post('service_');
        if($amount&&$data){
            $payment_id = isset($data['paymentID'])?$data['paymentID']:'';
            $payment_token = isset($data['paymentToken'])?$data['paymentToken']:'';
            $order_id = isset($data['orderID'])?$data['orderID']:'';
            $payer_id = isset($data['payerID'])?$data['payerID']:'';
            $intent = isset($data['intent'])?$data['intent']:'';
            $return_url = isset($data['returnUrl'])?$data['returnUrl']:'';
            $group_id = $this->group->id;
            $package_id = $this->group->billing_package_id;
            $billing_invoice_id = '';
            $receipt_date = time();
            $payment_method = 5;
            $ipn_transaction_code = $payment_id;
            $description = 'Order Id : '.$order_id;
            $created_by = $this->user->id;
            if($service_=='sms'){
                $amount = convert_currency($amount,$this->default_country->currency_code,'KES');
                $sms_purchased = $this->billing_settings->calculate_sms_purchase($amount);
                if($this->billing_settings->record_billing_sms_payments($group_id,$sms_purchased,$amount,$ipn_transaction_code,$receipt_date,$payment_method,$description,$created_by,TRUE,TRUE)){
                    echo 'Successful';
                }else{
                    echo 'Failed payment';
                }
            }else{
                if($this->billing_settings->record_billing_payments($amount,$group_id,$package_id,$billing_invoice_id,$receipt_date,$payment_method,$ipn_transaction_code,$description,$created_by,TRUE,TRUE)){
                    echo 'Successful';
                }else{
                    echo 'Failed payment';
                }
            }
            
        }else{
            echo 'FAILED';
        }
    }

    function calculate_conversion(){
        $amount = $this->input->post('amount');
        echo round(convert_currency($amount,$this->default_country->currency_code),2);
    }

    function calculate_coupon(){
        $coupon = $this->input->post('coupon');
        $response = $this->billing_settings->generate_coupon($coupon,$this->group,$this->user);
        echo json_encode($response);
    }

    function get_new_amount_payable($show_arrears = 0){
        $billing_package_id = $this->input->post('package_id');
        $billing_cycle = $this->input->post('billing_cycle_id');
        $billing_group_id = $this->input->post('group_id');
        $result = $this->billing_settings->get_amount_payable($billing_package_id, $billing_cycle, $billing_group_id,'','',TRUE,'',$show_arrears);
        echo json_encode($result);
    }

    function update_group_billing_cycle(){
        $billing_cycle = $this->input->post('billing_cycle');
        if($billing_cycle && is_numeric($billing_cycle)){
            $update = array(
                'billing_cycle' => $billing_cycle,
                'modified_on' => time(),
                'modified_by' => $this->user->id,
            );
            if($this->groups_m->update($this->group->id,$update)){
                echo 'Group billing cycle set successfully';//$this->session->set_flashdata('success','Group billing cycle');
            }else{
                echo 'Could not update group billing cycle';//$this->session->set_flashdata('error','Could not update group billing cycle');
            }
        }else{
            echo 'Invalid billing cycle selected';//$this->session->set_flashdata('error','Invalid billing cycle selected');
        }
    }

    public function get_group_unpaid_arrears_and_due_date(){
        $arrears = $this->billing_m->get_group_account_arrears($this->group->id);
        $due_date = time();
        if($arrears){
            $invoice = $this->billing_m->get_group_last_unpaid_invoice($this->group->id);
            if($invoice){
                $due_date = $invoice->due_date;
            }
        }
        echo json_encode(array(
            'arrears' => $arrears,
            'due_date' => timestamp_to_mobile_shorttime($due_date),
        ));
    }
}