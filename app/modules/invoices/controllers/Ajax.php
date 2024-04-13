<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
	
	public $invoice_type_options;

    protected $sms_template_default = '';

    protected $validation_rules = array(
        array(
            'field' => 'type',
            'label' => 'Invoice Type',
            'rules' => 'xss_clean|trim|required|numeric',
        ),array(
            'field' => 'send_to',
            'label' => 'Send to',
            'rules' => 'xss_clean|trim|required|numeric',
        ),array(
            'field' => 'member_id',
            'label' => 'Member',
            'rules' => 'xss_clean|',
        ),array(
            'field' => 'amount_payable',
            'label' => 'Amount Payable',
            'rules' => 'xss_clean|trim|required|currency',
        ),array(
            'field' => 'invoice_date',
            'label' => 'Invoice Date',
            'rules' => 'xss_clean|trim|required',
        ),array(
            'field' => 'due_date',
            'label' => 'Due Date',
            'rules' => 'xss_clean|trim|required',
        ),array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'xss_clean|trim',
        ),array(
            'field' => 'send_sms_notification',
            'label' => 'Send SMS Notification',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'send_email_notification',
            'label' => 'Send Email Notification',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'sms_template',
            'label' => 'SMS Template',
            'rules' => 'xss_clean|trim',
        ),array(
            'field' => 'contribution_id',
            'label' => 'Contribution',
            'rules' => 'xss_clean|trim',
        ),array(
            'field' => 'fine_category_id',
            'label' => 'Fine Category',
            'rules' => 'xss_clean|trim',
        )
    );

    protected $send_to_options = array(
        ' ' => '--Select members to invoice--',
        '1' => 'All Members',
        '2' => 'Individual Members',
    );

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

    function check_template_placeholders(){

        preg_match_all("/\[[^\]]*\]/", $this->sms_template_default,$default_placeholders);
        preg_match_all("/\[[^\]]*\]/", $this->input->post('sms_template'),$placeholders);
        $valid_template = TRUE;
        $missing_placeholders = array();
        foreach($default_placeholders[0] as $placeholder){
            if(in_array($placeholder,$placeholders[0])){

            }else{
                $valid_template = FALSE;
                $missing_placeholders[] = $placeholder;
            }
        }

        if($valid_template){
            return TRUE;
        }else{
            $count = count($missing_placeholders);
            $i=1;
            foreach($missing_placeholders as $placeholder): 
                if($i==1){
                    $missing_placeholders_string = $placeholder;
                }else{
                    $missing_placeholders_string .= ','.$placeholder;
                }
                $i++;
            endforeach;
            if($count==1){
                $this->form_validation->set_message('check_template_placeholders', $missing_placeholders_string.' placeholder missing in SMS Template.');
            }else{
                $this->form_validation->set_message('check_template_placeholders', $missing_placeholders_string.' placeholders missing in SMS Template.');
            }
            return FALSE;
        }
    }

    function _member_id_is_not_empty(){
        $member_ids = $this->input->post('member_id');
        if(empty($member_ids)){
            $this->form_validation->set_message('_member_id_is_not_empty','Select members to invoice');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    function create(){
    	$data = array();
        $response = array();
        $post = new stdClass();
        $posts = $_POST;
        $message = '';
        $this->_conditional_validation_rules();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
        	 if($this->input->post('send_to')==1){
                $member_ids = array_flip($this->active_group_member_options);
            }else{
                $member_ids = $this->input->post('member_id');
            }
            $contribution_ids = array();
            $invoices = array();
            $member_objects_array = array();
            $contribution_objects_array = array();
            $contribution_settings_objects_array = array();
            $update_group_member_contribution_balances = FALSE;
            foreach ($member_ids as $member_id) {
                $invoice = new stdClass();
                $contribution_setting = new stdClass();
                # code...
                $member = $this->members_m->get_group_member($member_id);
                $contribution = $this->contributions_m->get_group_contribution($this->input->post('contribution_id'));
                if($this->input->post('type')==1){
                    $invoice_date = strtotime($this->input->post('invoice_date'));
                    $due_date = strtotime($this->input->post('due_date'));
                    $amount_payable = currency($this->input->post('amount_payable'));
                    $send_sms_notification = $this->input->post('send_sms_notification');
                    $send_email_notification = $this->input->post('send_email_notification');
                    $sms_template = $this->input->post('sms_template');
                    if($this->transactions->create_invoice(1,
                        $this->group->id,
                        $member,
                        $contribution,
                        $invoice_date,
                        $due_date,
                        $amount_payable,
                        $this->input->post('description'),
                        $sms_template,
                        $send_sms_notification,
                        $send_email_notification
                        )){
                    	$message = 'Invoice created successfully';
                        if(in_array($contribution->id,$contribution_ids)){

                        }else{
                            $contribution_ids[] = $contribution->id;
                        }
                        if(in_array($member->id,$member_objects_array)){

                        }else{
                            $member_objects_array[$member->id] = $member;
                        }
                        if(in_array($contribution->id,$contribution_objects_array)){

                        }else{
                            $contribution_objects_array[$contribution->id] = $contribution;
                        }
                        $update_group_member_contribution_balances = TRUE;
                        $invoice->invoice_date = $invoice_date;
                        $invoice->due_date = $due_date;
                        $invoice->amount_payable = $amount_payable;
                        $invoice->group_id = $this->group->id;
                        $invoice->member_id = $member->id;
                        $invoice->contribution_id = $contribution->id;
                        $invoices[] = $invoice;
                        $contribution_setting->sms_notifications_enabled = $send_sms_notification;
                        $contribution_setting->email_notifications_enabled = $send_email_notification;
                        $contribution_setting->sms_template = $sms_template;
                        if(in_array($contribution->id,$contribution_settings_objects_array)){

                        }else{
                           $contribution_settings_objects_array[$contribution->id] = $contribution_setting;
                        }

                    }else{
                    	$response = array(
	                        'status' => 0,
	                        'message' => 'Invoice could not be created',
	                    );
                    } 
                    

                }else if($this->input->post('type')==2){
                    if($this->transactions->create_contribution_fine_invoice(2,
                        $this->group->id,
                        $member,
                        $contribution,
                        strtotime($this->input->post('invoice_date')),
                        strtotime($this->input->post('due_date')),
                        $this->input->post('amount_payable'),
                        $this->input->post('description'),
                        '',
                        $this->input->post('send_sms_notification'),
                        $this->input->post('send_email_notification')
                        )){
                    	$message = 'Contribution fine created successfully';
                    }else{
                    	$message = 'Contribution fine could not be created';
                    } 
                }else if($this->input->post('type')==3){
                    if($this->transactions->create_fine_invoice(3,
                        $this->group->id,
                        strtotime($this->input->post('invoice_date')),
                        $member,
                        $this->input->post('fine_category_id'),
                        $this->input->post('amount_payable'),
                        $this->input->post('send_sms_notification'),
                        $this->input->post('send_email_notification'),
                        $this->input->post('description')
                        )
                    ){
                    	$message = 'Fine created successfully';
                    }else{
                    	$message = 'Fine could not be created successfully';
                    }                  
                }else if($this->input->post('type')==4){
                    if($this->transactions->create_miscellaneous_invoice(4,
                        $this->group->id,
                        $member,
                        strtotime($this->input->post('invoice_date')),
                        strtotime($this->input->post('due_date')),
                        $this->input->post('amount_payable'),
                        $this->input->post('description'),
                        'sms_templates',
                        $this->input->post('send_sms_notification'),
                        $this->input->post('send_email_notification')
                        )){
                    	$message = 'Miscellaneous invoice created successfully';
                    }else{
                    	$message = 'Miscellaneous invoice could not be created';
                    } 
                }
            }

            if($update_group_member_contribution_balances){
                $group_ids = array($this->group->id);
                if($this->transactions->update_group_member_contribution_statement_balances($group_ids,$member_ids)){
            
                }else{
                	$message = 'Something went wrong when updating the balances';
                }
                if($this->transactions->send_invoice_notifications($invoices,$group_ids,$member_ids,$contribution_ids,$member_objects_array,$contribution_objects_array,$contribution_settings_objects_array)){
                    
                }else{
                	$message = 'Something went wrong when sending notifications';
                    //$this->session->set_flashdata('warning','Something went wrong when sending notifications');
                }
            }

            $response = array(
                'status' => 1,
                'message' => 'Invoice successfully created: '.$message,
                'refer'=>site_url('group/invoices/listing')
            );

        }else{
        	$post = array();
            $form_errors = $this->form_validation->error_array();
            foreach ($form_errors as $key => $value) {
                $post[$key] = $value;
            }
            $response = array(
                'status' => 0,
                'message' => 'There are some errors on the form. Please review and try again.',
                'validation_errors' => $post,
            );
        }
    	echo json_encode($response);
    }

    function void(){
        $response = array();
        $id = $this->input->post('id');
        if($id){
            $post = $this->invoices_m->get_group_invoice($id);
            if($post){
                if($post->type==1){
                    if($this->transactions->void_contribution_invoice($id)){
                        $response = array(
                            'status'=>1,
                            'refer'=>site_url('group/invoices/listing'),
                            'message'=>'Contribution invoice successfully voided'
                        );
                        //$this->session->set_flashdata('success','Contribution invoice successfully voided.');
                    }else{
                        $response = array(
                            'status'=>0,
                            'message'=>'Contribution invoice could not be voided'
                        );
                    }
                }else if($post->type==2){
                    if($this->transactions->void_fine_invoice($id,'',$post->contribution_id)){
                        $response = array(
                            'status'=>1,
                            'refer'=>site_url('group/invoices/listing'),
                            'message'=>'Fine invoice successfully voided'
                        );
                    }else{
                        $response = array(
                            'status'=>0,
                            'message'=>'Fine invoice could not be voided'
                        );
                    }
                }else if($post->type==3){
                    if($this->transactions->void_fine_invoice($id,$post->fine_id)){
                        $response = array(
                            'status'=>1,
                            'refer'=>site_url('group/invoices/listing'),
                            'message'=>'Fine invoice successfully voided'
                        );
                        //$this->session->set_flashdata('success','Fine invoice successfully voided.');
                    }else{
                        $response = array(
                            'status'=>0,
                            'message'=>'Fine invoice could not be voided'
                        );
                        //$this->session->set_flashdata('error','Fine invoice could not be voided.');
                    }
                }else if($post->type==4){
                    if($this->transactions->void_miscellaneous_invoice($id)){
                        $response = array(
                            'status'=>1,
                            'refer'=>site_url('group/invoices/listing'),
                            'message'=>'Miscellaneous invoice successfully voided.'
                        );
                        //$this->session->set_flashdata('success','Miscellaneous invoice successfully voided.');
                    }else{
                        $response = array(
                            'status'=>0,
                            'message'=>'Miscellaneous invoice could not be voided'
                        );
                        //$this->session->set_flashdata('error','Miscellaneous invoice could not be voided.');
                    }
                }
            }else{
               $response = array(
                    'status'=>0,
                    'message'=>'Could not find invoice details'
                ); 
            }
        }else{
            $response = array(
                'status'=>0,
                'message'=>'Invoice id is required'
            );

        }
        echo json_encode($response);
    }


    function _conditional_validation_rules(){

    	if($this->input->post('type')==1||$this->input->post('type')==2||$this->input->post('type')==5||$this->input->post('type')==6){ 
            $this->validation_rules[] = array(
                'field' => 'contribution_id',
                'label' => 'Contribution',
                'rules' => 'trim|required|numeric',
            );
        }
        if($this->input->post('type')==1){
            if($this->input->post('send_sms_notification')){
                $this->validation_rules[] = array(
                    'field' => 'sms_template',
                    'label' => 'SMS Template',
                    'rules' => 'trim|required|callback_check_template_placeholders',
                );
            }
        }else if($this->input->post('type')==4){
            $this->validation_rules[] = array(
                'field' => 'description',
                'label' => 'Description',
                'rules' => 'trim|required',
            );
        }
        if($this->input->post('send_to')==2){
            $this->validation_rules[] = array(
                'field' => 'member_id',
                'label' => 'Member',
                'rules' => 'callback__member_id_is_not_empty',
            );
        }
        if($this->input->post('type')==3){
            $this->validation_rules[] = array(
                'field' => 'fine_category_id',
                'label' => 'Fine Category',
                'rules' => 'trim|required',
            );
        }
    }

    function get_invoice($id = 0){
        $post = $this->invoices_m->get_group_invoice($id);
        if(empty($post)){
           echo 'Sorry, the entry does not exists.'; 
        }else{
            if($post->active){
                $contribution_options = $this->contributions_m->get_group_contribution_options();
                $fine_category_options = $this->fine_categories_m->get_group_options(FALSE);
                $member = $this->group_member_options[$post->member_id];
                if($post->type==1){
                    $description = $this->invoice_type_options[$post->type].' for '.$contribution_options[$post->contribution_id].' contribution';
                }else if($post->type==2){
                    $description = $this->invoice_type_options[$post->type].' for '.$contribution_options[$post->contribution_id].' contribution';
                }else if($post->type==3){
                    $description = $this->invoice_type_options[$post->type].' for '.$fine_category_options[$post->fine_category_id];
                }else if($post->type==4){
                    $description = $this->invoice_type_options[$post->type].' for '.$post->description;
                }
                $response = array(
                    'member' => $member,
                    'description' => $description,
                    'invoice_for' => $this->invoice_type_options[$post->type],
                    'invoice_date' => timestamp_to_date($post->invoice_date),
                    'due_date' => timestamp_to_date($post->due_date),
                    'created_on' => timestamp_to_date_and_time($post->created_on),
                    'amount_payable' => $this->group_currency.' '.number_to_currency($post->amount_payable),
                );
                echo json_encode($response);
            }else{
                echo 'Sorry, the entry does not exists.';
            }
        }
    }
    function ajax_get_invoices_listing(){
        $from = $this->input->get('from')?strtotime($this->input->get('from')):'';
        $to = $this->input->get('to')?strtotime($this->input->get('to')):'';
        $filter_parameters = array(
            'from' => $from,
            'to' => $to,
            'type' => $this->input->get('type'),
            'member_ids' => $this->input->get('member_ids'),
            'contributions' => $this->input->get('contributions'),
            'fine_categories' => $this->input->get('fine_categories'),
        );
        $total_rows = $this->invoices_m->count_group_invoices($filter_parameters);
        $pagination = create_pagination('group/invoices/listing/pages', $total_rows,50,5,TRUE);
        $posts = $this->invoices_m->limit($pagination['limit'])->get_group_invoices($filter_parameters);
        // print_r($posts); die;
        $contribution_options = $this->contributions_m->get_group_contribution_options();
        $fine_category_options = $this->fine_categories_m->get_group_options(FALSE);
        if(!empty($posts)){
            echo form_open('group/invoices/action', ' id="form"  class="form-horizontal"');
                if ( ! empty($pagination['links'])):
                echo '
                <div class="row col-md-12">
                    <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Invoices</p>';
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links']; 
                    echo '</div></div>';
                endif; 
            echo '
            <table class="table m-table m-table--head-separator-primary">
                <thead>
                    <tr>
                        <th width=\'2%\'>
                             <input type="checkbox" name="check" value="all" class="check_all">
                        </th>
                        <th width=\'2%\'>
                            #
                        </th>
                        <th>
                            Invoice Details
                        </th>
                        <th class=\'text-right\'>
                            Payable ('.$this->group_currency.')
                        </th> 
                        <th class=\'text-right\'>
                            Paid ('.$this->group_currency.')
                        </th>  
                        <th>
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>';
                    $i = $this->uri->segment(5, 0); $i++; foreach($posts as $post):
                    echo '
                        <tr>
                            <td><input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" /></td>
                            <td>'.($i++).'</td>
                            <td>
                                <strong>Invoice Date: </strong>'.timestamp_to_date($post->invoice_date).'<br/>
                                <strong>Due Date: </strong>'.timestamp_to_date($post->due_date).'<br/>
                                <strong>Created On: </strong>'.timestamp_to_datetime($post->created_on).'<br/>
                                <strong>Member: </strong>'.$this->group_member_options[$post->member_id].'<br/>
                                <strong>Description</strong><hr/>';
                                    if($post->type==1){
                                        echo $this->invoice_type_options[$post->type].' for '.$contribution_options[$post->contribution_id].' contribution';
                                    }else if($post->type==2){
                                        echo $this->invoice_type_options[$post->type].' for '.$contribution_options[$post->contribution_id].' contribution';
                                    }else if($post->type==3){
                                        echo $this->invoice_type_options[$post->type].' for '.$fine_category_options[$post->fine_category_id];
                                    }else if($post->type==4){
                                        echo $this->invoice_type_options[$post->type].' for '.$post->description;
                                    }
                            echo '
                            </td>
                            <td class=\'text-right\'>
                                '.number_to_currency($post->amount_payable).'
                            </td>
                            <td  class=\'text-right\'>
                                '.number_to_currency($post->amount_paid).'
                            </td>  
                             <td>
                                 <a href="javascript:;" class="btn btn-sm btn-primary m-btn m-btn--icon action_button view_invoice" id="'.$post->id.'" data-toggle="modal" data-target="#invoice_receipt">
                                     <span>
                                         <i class="la la-eye"></i>
                                         <span>
                                            More &nbsp;&nbsp; 
                                         </span>
                                     </span>
                                 </a>

                                 <a data-id="'.$post->id.'" id="'.$post->id.'" href="javascript:;" class="btn btn-sm btn-danger m-btn m-btn--icon action_button prompt_confirmation_message_link">
                                     <span>
                                         <i class="fa fa-trash-alt"></i>
                                         <span>
                                             Void &nbsp;&nbsp; 
                                         </span>
                                     </span>
                                 </a>
                             </td>
                        </tr>';
                    endforeach;
                    echo '
                </tbody>
            </table>
            <div class="clearfix"></div>
            <div class="row col-md-12">';
                if( ! empty($pagination['links'])): 
                echo $pagination['links']; 
                endif; 
            echo '  
            </div>
            <div class="clearfix"></div>';
            if($posts):
                echo '
                <button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_void\' data-toggle="confirmation" data-title="Confirm Bulk Reconciliation" data-placement="top"> <i class=\'fa fa-trash-o\'></i> Bulk Void</button>';
            endif;
            echo form_close(); 
        }else{
            echo '
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="m-alert m-alert--outline alert alert-info fade show" role="alert"">
                            <strong>Information!</strong> No invoices to display.
                        </div>
                    </div>
                </div>
            </div>';
        }
    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_void'){
            for($i=0;$i<count($action_to);$i++){
                $this->void($action_to[$i],FALSE);
            }
        }else if($action == 'bulk_fine_void'){
            for($i=0;$i<count($action_to);$i++){
                $this->void($action_to[$i],FALSE);
            }
        }
        if($this->agent->referrer()){
            redirect($this->agent->referrer());
        }else{
            redirect('group/invoices/listing');
        }
    }

    


}