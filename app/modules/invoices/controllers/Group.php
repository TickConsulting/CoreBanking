<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{

	public $invoice_type_options;

    protected $sms_template_default = '';

    protected $validation_rules = array(
        array(
            'field' => 'type',
            'label' => 'Invoice Type',
            'rules' => 'trim|required|numeric',
        ),array(
            'field' => 'send_to',
            'label' => 'Send to',
            'rules' => 'trim|required|numeric',
        ),array(
            'field' => 'member_id',
            'label' => 'Member',
            'rules' => '',
        ),array(
            'field' => 'amount_payable',
            'label' => 'Amount Payable',
            'rules' => 'trim|required|currency',
        ),array(
            'field' => 'invoice_date',
            'label' => 'Invoice Date',
            'rules' => 'trim|required',
        ),array(
            'field' => 'due_date',
            'label' => 'Due Date',
            'rules' => 'trim|required',
        ),array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'trim',
        ),array(
            'field' => 'send_sms_notification',
            'label' => 'Send SMS Notification',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'send_email_notification',
            'label' => 'Send Email Notification',
            'rules' => 'trim|numeric',
        ),array(
            'field' => 'sms_template',
            'label' => 'SMS Template',
            'rules' => 'trim',
        ),array(
            'field' => 'contribution_id',
            'label' => 'Contribution',
            'rules' => 'trim',
        ),array(
            'field' => 'fine_category_id',
            'label' => 'Fine Category',
            'rules' => 'trim',
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

    function index(){
        $this->template->title('Group Invoices')->build('group/index');
    }

    function listing(){
        $data['from'] = $this->input->get('from')?strtotime($this->input->get('from')):'';
        $data['to'] = $this->input->get('to')?strtotime($this->input->get('to')):'';
        $total_rows = $this->invoices_m->count_group_invoices();   
        $data['invoice_type_options'] = $this->invoice_type_options;
        $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
    	$data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
        if($this->input->get_post('generate_excel')==1){
            $filter_parameters = array(
                'from' => $data['from'],
                'to' => $data['to'],
                'type' => $this->input->get('type'),
                'member_ids' => $this->input->get('member_ids'),
                'contributions' => $this->input->get('contributions'),
                'fine_categories' => $this->input->get('fine_categories'),
            );
            $data['filter_parameters'] = $filter_parameters;
            $data['posts'] = $this->invoices_m->get_group_invoices($filter_parameters);
            $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
            $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
            $data['members'] = $this->group_member_options;
            $data['group'] = $this->group;
            $data['group_currency'] = $this->group_currency;
            $data['invoice_type_options'] = $this->invoice_type_options;
            $json_file = json_encode($data);
            
            $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/invoices/listing',$this->group->name.' Invoice List');
        }
        $this->template->title(translate('List Invoices'))->build('group/listing',$data);
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
                                <a href="'.site_url('group/invoices/view/'.$post->id).'" class="btn btn-xs default">
                                    <i class="fa fa-eye"></i> View &nbsp;&nbsp; 
                                </a>
                                <a href="'.site_url('group/invoices/void/'.$post->id).'" class="btn confirmation_link btn-xs red">
                                    <i class="fa fa-trash-o"></i> Void &nbsp;&nbsp; 
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
                <button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_void\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-trash-o\'></i> Bulk Void</button>';
            endif;
            echo form_close(); 
        }else{
            echo '
            <div class="alert alert-info">
                <h4 class="block">Information! No records to display</h4>
                <p>
                    No invoices to display.
                </p>
            </div>';
        }
    }

    function create(){
        $data = array();
        $post = new stdClass();
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
                    $send_sms_notification = $this->input->post('amount_payable');
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
                        $this->session->set_flashdata('success','Invoice created successfully');
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
                        $this->session->set_flashdata('error','Invoice could not be created');
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
                        $this->session->set_flashdata('success','Contribution fine created successfully');
                    }else{
                        $this->session->set_flashdata('error','Contribution fine could not be created');
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
                        $this->session->set_flashdata('success','Fine created successfully');
                    }else{
                        $this->session->set_flashdata('error','Fine could not be created successfully');
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
                        $this->session->set_flashdata('success','Miscellaneous created successfully');
                    }else{
                        $this->session->set_flashdata('error','Miscellaneous could not be created');
                    } 
                }
            }
            if($update_group_member_contribution_balances){
                $group_ids = array($this->group->id);
                if($this->transactions->update_group_member_contribution_statement_balances($group_ids,$member_ids)){
            
                }else{
                    $this->session->set_flashdata('warning','Something went wrong when updating the balances');
                }

                if($this->transactions->send_invoice_notifications($invoices,$group_ids,$member_ids,$contribution_ids,$member_objects_array,$contribution_objects_array,$contribution_settings_objects_array)){
                    
                }else{
                    $this->session->set_flashdata('warning','Something went wrong when sending notifications');
                }
            }
            redirect('group/invoices/listing');
        }else{
            foreach ($this->validation_rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }   
        preg_match_all("/\[[^\]]*\]/", $this->sms_template_default,$placeholders);
        $data['placeholders'] = isset($placeholders[0])?$placeholders[0]:array();
        $data['sms_template_default'] = $this->sms_template_default;
        $data['group_contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $data['group_fine_category_options'] = $this->fine_categories_m->get_group_options();
        $data['send_to_options'] = $this->send_to_options;
        $data['post'] = $post;
        //print_r($data['post']); die();
        $this->template->title('Create Invoice')->build('group/form',$data);
    }

    // function void($id = 0,$redirect = TRUE){
    //     $id OR redirect('group/invoices/listing');
    //     $post = $this->invoices_m->get_group_invoice($id);
    //     $post OR redirect('group/invoices/listing');
    //     if($post->type==1){
    //         if($this->transactions->void_contribution_invoice($id)){
    //             $this->session->set_flashdata('success','Contribution invoice successfully voided.');
    //         }else{
    //             $this->session->set_flashdata('error','Contribution invoice could not be voided.');
    //         }
    //         if($redirect){
    //             if($this->agent->referrer()){
    //                 redirect($this->agent->referrer());
    //             }else{
    //                 redirect('group/invoices/listing');
    //             }
    //         }
    //     }else if($post->type==2){
    //         if($this->transactions->void_fine_invoice($id,'',$post->contribution_id)){
    //             $this->session->set_flashdata('success','Fine invoice successfully voided.');
    //         }else{
    //             $this->session->set_flashdata('error','Fine invoice could not be voided.');
    //         }
    //         if($redirect){
    //             if($this->agent->referrer()){
    //                 redirect($this->agent->referrer());
    //             }else{
    //                 redirect('group/invoices/listing');
    //             }
    //         }
    //     }else if($post->type==3){
    //         if($this->transactions->void_fine_invoice($id,$post->fine_id)){
    //             $this->session->set_flashdata('success','Fine invoice successfully voided.');
    //         }else{
    //             $this->session->set_flashdata('error','Fine invoice could not be voided.');
    //         }
    //         if($redirect){
    //             if($this->agent->referrer()){
    //                 redirect($this->agent->referrer());
    //             }else{
    //                 redirect('group/invoices/listing');
    //             }
    //         }
    //     }else if($post->type==4){
    //         if($this->transactions->void_miscellaneous_invoice($id)){
    //             $this->session->set_flashdata('success','Miscellaneous invoice successfully voided.');
    //         }else{
    //             $this->session->set_flashdata('error','Miscellaneous invoice could not be voided.');
    //         }
    //         if($redirect){
    //             if($this->agent->referrer()){
    //                 redirect($this->agent->referrer());
    //             }else{
    //                 redirect('group/invoices/listing');
    //             }
    //         }
    //     }
    // }

     function void($id = 0,$redirect = TRUE){
        $id OR redirect('group/invoices/listing');
        $post = $this->invoices_m->get_group_invoice($id);
        $post OR redirect('group/invoices/listing');
        if($post->type==1){
            if($this->transactions->void_contribution_invoice($id,$post->group_id)){
                $this->session->set_flashdata('success','Contribution invoice successfully voided.');
            }else{
                $this->session->set_flashdata('error','Contribution invoice could not be voided.');
            }
            if($redirect){
                if($this->agent->referrer()){
                    redirect($this->agent->referrer());
                }else{
                    redirect('group/invoices/listing');
                }
            }
        }else if($post->type==2){
            if($this->transactions->void_fine_invoice($id,'',$post->contribution_id)){
                $this->session->set_flashdata('success','Fine invoice successfully voided.');
            }else{
                $this->session->set_flashdata('error','Fine invoice could not be voided.');
            }
            if($redirect){
                if($this->agent->referrer()){
                    redirect($this->agent->referrer());
                }else{
                    redirect('group/invoices/listing');
                }
            }
        }else if($post->type==3){
            if($this->transactions->void_fine_invoice($id,$post->fine_id)){
                $this->session->set_flashdata('success','Fine invoice successfully voided.');
            }else{
                $this->session->set_flashdata('error','Fine invoice could not be voided.');
            }
            if($redirect){
                if($this->agent->referrer()){
                    redirect($this->agent->referrer());
                }else{
                    redirect('group/invoices/listing');
                }
            }
        }else if($post->type==4){
            if($this->transactions->void_miscellaneous_invoice($id,$post->id)){
                $this->session->set_flashdata('success','Miscellaneous invoice successfully voided.');
            }else{
                $this->session->set_flashdata('error','Miscellaneous invoice could not be voided.');
            }
            if($redirect){
                if($this->agent->referrer()){
                    redirect($this->agent->referrer());
                }else{
                    redirect('group/invoices/listing');
                }
            }
        }
    }

    // function action(){
    //     $action_to = $this->input->post('action_to');
    //     $action = $this->input->post('btnAction');
    //     if($action == 'bulk_void'){
    //         for($i=0;$i<count($action_to);$i++){
    //             $this->void($action_to[$i],FALSE);
    //         }
    //     }else if($action == 'bulk_fine_void'){
    //         for($i=0;$i<count($action_to);$i++){
    //             $this->void($action_to[$i],FALSE);
    //         }
    //     }
    //     if($this->agent->referrer()){
    //         redirect($this->agent->referrer());
    //     }else{
    //         redirect('group/invoices/listing');
    //     }
    // }


    function bulk_void($group_id = 0,$ids = array(),$redirect = TRUE){
        $this->output->enable_profiler(TRUE);
        if($ids){            
            $invoices = $this->invoices_m->get_invoices_by_ids($group_id,$ids);
            if($invoices){
                $contribution_invoice_ids = array();
                $fine_invoice_ids = array();
                $contribution_fine_invoice_ids = array();
                $miscellaneous_invoice_ids = array();
                foreach ($invoices as $key => $invoice):
                    if($invoice->type == 1){
                        $contribution_invoice_ids[] = $invoice->id;
                    }else if($invoice->type == 2){
                        $contribution_fine_invoice_ids[] = $invoice->id;
                    }else if($invoice->type == 3){
                        $fine_invoice_ids[] = $invoice->id;
                    }else if($invoice->type == 4){
                        $miscellaneous_invoice_ids[] = $invoice->id;                        
                    }
                endforeach;
                $number_voided = count($contribution_invoice_ids)+count($contribution_fine_invoice_ids)+count($fine_invoice_ids)+count($miscellaneous_invoice_ids);
                if($contribution_invoice_ids){
                    if($this->transactions->void_bulk_contribution_invoice($group_id,$contribution_invoice_ids)){
                        $this->session->set_flashdata('success',$number_voided.' Contribution invoice successfully voided.');
                    }
                }
                if($contribution_fine_invoice_ids){
                    if($this->transactions->void_bulk_fine_invoice($group_id,$contribution_fine_invoice_ids)){
                        $this->session->set_flashdata('success',$number_voided.' Contribution fine invoice successfully  voided.');
                    } 
                }
                if($fine_invoice_ids){
                    if($this->transactions->void_bulk_fine_invoice($group_id,$fine_invoice_ids)){
                        $this->session->set_flashdata('success',$number_voided.' Fine invoice successfully  voided.');
                    } 
                }

                if($miscellaneous_invoice_ids){
                    if($this->transactions->void_bulk_miscellaneous_invoice($group_id,$miscellaneous_invoice_ids)){
                        $this->session->set_flashdata('success',$number_voided.' Fine invoice successfully  voided.');
                    } 
                }

            }else{
              $this->session->set_flashdata('error','Could not find invoices.');   
            }            
        }else{
            $this->session->set_flashdata('error','Kindly check on invoices to void.');  
        }
    }

    function bulk_void_duplicates($group_id = 0,$ids = array(),$redirect = TRUE){
        $this->output->enable_profiler(TRUE);
        print_r($ids); die(); 
        if($ids){ 

            $invoices = $this->invoices_m->get_invoices_by_ids($group_id,$ids);
            if($invoices){
                $contribution_invoice_ids = array();
                $fine_invoice_ids = array();
                $contribution_fine_invoice_ids = array();
                $miscellaneous_invoice_ids = array();
                $invoice_ids = array();
                foreach ($invoices as $key => $invoice):
                    $invoice_ids[] = $invoice->id;
                    if($invoice->type == 1){
                        $contribution_invoice_ids[] = $invoice->id;
                    }else if($invoice->type == 2){
                        $contribution_fine_invoice_ids[] = $invoice->id;
                    }else if($invoice->type == 3){
                        $fine_invoice_ids[] = $invoice->id;
                    }else if($invoice->type == 4){
                        $miscellaneous_invoice_ids[] = $invoice->id;                        
                    }
                endforeach;
                $number_voided = count($contribution_invoice_ids)+count($contribution_fine_invoice_ids)+count($fine_invoice_ids)+count($miscellaneous_invoice_ids);                
                $statement_entries_array = $this->statements_m->get_group_statement_by_invoice_ids_array($group_id,$invoice_ids);
                if($statement_entries_array){
                    foreach ($statement_entries_array as $key => $statement):
                        $member_statements_reconciled++;
                        $member_ids[] = $statement->member_id;
                        $group_ids[] = $statement->group_id;
                        $statement_ids[] = $statement->id;
                    endforeach;
                    if($statement_ids && $member_ids && $group_ids){
                        if($this->statements_m->void_group_contribution_statements_by_ids_array($group_ids,$statement_ids)){
                            if($this->invoices_m->void_group_invoices($group_id,$ids)){
                                $this->session->set_flashdata('success','Statements and invoices voided sucessfully)');
                                return FALSE;
                            }else{
                                $this->session->set_flashdata('error','Some parameters are missing(var)');
                                return FALSE;  
                            } 
                            //return $this->update_group_member_contribution_statement_balances($group_ids,$member_ids);
                        }
                    }else{
                        $this->session->set_flashdata('error','Some parameters are missing(var)');
                        return FALSE;   
                    }
                }

            }else{
              $this->session->set_flashdata('error','Could not find invoices.');   
            }            
        }else{
            $this->session->set_flashdata('error','Kindly check on invoices to void.');  
        }
    }

    function action(){
        $action_to = $this->input->post('action_to'); 
        $action = $this->input->post('btnAction');
        $group_id = $this->group->id;
        if($action == 'bulk_void'){
            $this->bulk_void($group_id,$action_to);
        }else if($action == 'bulk_fine_void'){
            for($i=0;$i<count($action_to);$i++){
                $this->void($action_to[$i],FALSE);
            }
        }else if($action == 'bulk_void_duplicates'){
           $this->bulk_void_duplicates($group_id,$action_to); 
        }else{
            $this->bulk_void($group_id,$action_to);
        }
        if($this->agent->referrer()){
            redirect($this->agent->referrer());
        }else{
            redirect('group/invoices/listing');
        }
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

    function ajax_create_back_dating_invoices(){
        $posts = $_POST;
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        if($group_cut_off_date){
            if(isset($posts)){
                $member_arrears = $this->input->post('member_arrears');
                if(empty($member_arrears)){
                    echo "Member arrears post value empty";
                }else{

                    $members = $this->members_m->get_group_members();
                    $contributions = $this->contributions_m->get_group_contributions();

                    $member_objects_array = array();
                    foreach($members as $member):
                        $member_objects_array[$member->id] = $member;
                    endforeach;

                    $contribution_objects_array = array();
                    foreach($contributions as $contribution):
                        $contribution_objects_array[$contribution->id] = $contribution;
                    endforeach;
                    $result = TRUE;
                    foreach($member_arrears as $member_id => $contributions):
                        if(isset($member_objects_array[$member_id])){
                            foreach($contributions as $contribution_id => $amount):
                                if(isset($contribution_objects_array[$contribution_id])){
                                    if($amount){
                                        if($this->transactions->create_invoice(1,$this->group->id,$member_objects_array[$member_id],$contribution_objects_array[$contribution_id],$group_cut_off_date->cut_off_date,$group_cut_off_date->cut_off_date,valid_currency($amount),"Back dating invoice",'sms_template',FALSE,FALSE,TRUE)){

                                        }else{
                                            $result = FALSE;
                                        }
                                    }
                                }else{
                                    $result = FALSE;
                                }
                            endforeach;
                        }else{
                            $result = FALSE;
                        }
                    endforeach;
                    if($result){
                        echo "success";
                    }else{
                        
                        echo "error";
                    }
                }
            }else{
                echo "No data posted";
            }
        }else{
            echo "Group cut off date not yet set";
        }
    }

    function ajax_contribution_target_listing(){
        $contribution_options = $this->contributions_m->get_group_contribution_options();
        $group_member_total_contribution_back_dated_arrears_per_contribution_array = $this->invoices_m->get_group_member_total_contribution_back_dated_arrears_per_contribution_array();
        $group_member_total_contributions_back_dated_paid_per_contribution_array = $this->deposits_m->get_group_member_total_contributions_back_dated_paid_per_contribution_array();
        if(!empty($contribution_options)){
            foreach($contribution_options as $contribution_id => $contribution_name):
                $contribution = $this->contributions_m->get_group_contribution($contribution_id);
                    echo '<h4>'.$contribution_name.'</h4>';
                    echo '
                        <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                            <thead>
                                <tr>
                                    <th width="8px">
                                        #
                                    </th>
                                    <th>
                                        Member Name
                                    </th>
                                    <th class="text-right">
                                        Payable ('.$this->group_currency.')
                                    </th>
                                    <th class="text-right">
                                        Paid ('.$this->group_currency.')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>';
                                    $count = 1; 
                                    $total_member_arrears = 0;
                                    $total_paid = 0;
                                    foreach($this->group_member_options as $member_id => $member_name): 
                                    $member_arrears = $group_member_total_contribution_back_dated_arrears_per_contribution_array[$member_id][$contribution_id];
                                    $total_member_arrears += $member_arrears;
                                    $paid = $group_member_total_contributions_back_dated_paid_per_contribution_array[$member_id][$contribution_id];
                                    $total_paid += $paid;
                                echo '
                                    <tr>
                                        <td>'.$count++.'</td>
                                        <td>'.$member_name.'</td>
                                        <td  class="text-right">'.number_to_currency($member_arrears).'</td>
                                        <td  class="text-right">'.number_to_currency($paid).'</td>
                                    </tr>';
                                    endforeach; 
                                echo '
                                <tr>
                                    <td>#</td>
                                    <td>Totals</td>
                                    <td class="text-right">
                                        '.number_to_currency($total_member_arrears).'
                                    </td>
                                    <td class="text-right">
                                        '.number_to_currency($total_paid).'
                                    </td>
                                </tr>
                            </tbody>
                        </table>';
            endforeach;
        }          
    }

    function ajax_void_group_back_dating_invoices(){
        $invoices = $this->invoices_m->get_group_back_dating_contribution_invoices();
        $result = TRUE;
        foreach($invoices as $invoice):
            if($this->transactions->void_contribution_invoice($invoice->id)){

            }else{
                $result = FALSE;
            }
        endforeach;
        if($result){
            echo "success";
        }else{
            echo "error";
        }
    }

    function ajax_contribution_target_form(){

        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $contribution_options = $this->contributions_m->get_group_contribution_options();
        $group_member_total_contribution_back_dated_arrears_per_contribution_array = $this->invoices_m->get_group_member_total_contribution_back_dated_arrears_per_contribution_array();
        $group_member_total_contributions_back_dated_paid_per_contribution_array = $this->deposits_m->get_group_member_total_contributions_back_dated_paid_per_contribution_array();
        
        echo '
        <div class="alert alert-info">
            <strong>Information!</strong> Enter the amount each member <strong>should have</strong> contributed and has paid during the period '.timestamp_to_date($group_cut_off_date->group_start_date).' to '.timestamp_to_date($group_cut_off_date->cut_off_date).'
        </div>';
            if(!empty($contribution_options)){
                foreach($contribution_options as $contribution_id => $contribution_name):
                    $contribution = $this->contributions_m->get_group_contribution($contribution_id);
                        echo '<h4>'.$contribution_name.'</h4>';
            echo '
            <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                <thead>
                    <tr>
                        <th width="8px">
                            #
                        </th>
                        <th>
                            Member Name
                        </th>
                        <th class="text-right">
                            Payable ('.$this->group_currency.')
                        </th>
                        <th class="text-right">
                            Paid ('.$this->group_currency.')
                        </th>
                    </tr>
                </thead>
                <tbody>';
                        $count = 1; 
                        $total_member_arrears = 0;
                        $total_paid = 0;
                        foreach($this->group_member_options as $member_id => $member_name): 
                        $member_arrears = $group_member_total_contribution_back_dated_arrears_per_contribution_array[$member_id][$contribution_id];
                        $total_member_arrears += $member_arrears;
                        $paid = $group_member_total_contributions_back_dated_paid_per_contribution_array[$member_id][$contribution_id];
                        $total_paid += $paid;
                        echo '
                        <tr>
                            <td>'.$count++.'</td>
                            <td>'.$member_name.'</td>
                            <td class="text-right"> 
                                '.form_input('member_arrears['.$member_id.']['.$contribution_id.']',$member_arrears," class='form-control currency'").'
                            </td>
                            <td class="text-right"> 
                                '.form_input('paid['.$member_id.']['.$contribution_id.']',$paid," class='form-control currency'").'
                            </td>
                        </tr>'; 
                        endforeach; 
                echo '
                </tbody>
            </table>';
            endforeach;
            }
    }

    function ajax_fines_issued_listing(){
        $fine_category = $this->fine_categories_m->get_group_back_dating_fine_category();
        $group_member_total_fines_back_dated_arrears_per_fine_category_array = $this->invoices_m->get_group_member_total_fines_issued_back_dated_arrears_per_fine_category_array();
        $group_member_total_fines_paid_back_dated_paid_per_fine_category_array = $this->deposits_m->get_group_member_total_fines_paid_back_dated_paid_per_fine_category_array();
        if($fine_category){
            echo '<h4>'.$fine_category->name.'</h4>';
            echo '
                <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                    <thead>
                        <tr>
                            <th width="8px">
                                #
                            </th>
                            <th>
                                Member Name
                            </th>
                            <th class="text-right">
                                Fines Issued ('.$this->group_currency.')
                            </th>
                            <th class="text-right">
                                Fines Paid ('.$this->group_currency.')
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                            $count = 1; 
                            $total_member_fine_arrears = 0;
                            $total_fines_paid = 0;
                            foreach($this->group_member_options as $member_id => $member_name): 
                            $member_fine_arrears = $group_member_total_fines_back_dated_arrears_per_fine_category_array[$member_id][$fine_category->id];
                            $total_member_fine_arrears += $member_fine_arrears;
                            $fines_paid = $group_member_total_fines_paid_back_dated_paid_per_fine_category_array[$member_id][$fine_category->id];
                            $total_fines_paid += $fines_paid;
                        echo '
                            <tr>
                                <td>'.$count++.'</td>
                                <td>'.$member_name.'</td>
                                <td  class="text-right">'.number_to_currency($member_fine_arrears).'</td>
                                <td  class="text-right">'.number_to_currency($fines_paid).'</td>
                            </tr>';
                            endforeach; 
                        echo '
                        <tr>
                            <td>#</td>
                            <td>Totals</td>
                            <td class="text-right">
                                '.number_to_currency($total_member_fine_arrears).'
                            </td>
                            <td class="text-right">
                                '.number_to_currency($total_fines_paid).'
                            </td>
                        </tr>
                    </tbody>
                </table>';
        }   
    }

    function ajax_fines_issued_form(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $fine_category = $this->fine_categories_m->get_group_back_dating_fine_category();
        $group_member_total_fines_back_dated_arrears_per_fine_category_array = $this->invoices_m->get_group_member_total_fines_issued_back_dated_arrears_per_fine_category_array();
        $group_member_total_fines_paid_back_dated_paid_per_fine_category_array = $this->deposits_m->get_group_member_total_fines_paid_back_dated_paid_per_fine_category_array();
        echo '
        <div class="alert alert-info">
            <strong>Information!</strong> Enter the amount each member <strong>had</strong> been fined and had paid between the period of '.timestamp_to_date($group_cut_off_date->group_start_date).' to '.timestamp_to_date($group_cut_off_date->cut_off_date).'
        </div>';
            if($fine_category){
            echo '<h4>'.$fine_category->name.'</h4>';
            echo '
            <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                <thead>
                    <tr>
                        <th width="8px">
                            #
                        </th>
                        <th>
                            Member Name
                        </th>
                        <th class="text-right">
                            Fines Issued ('.$this->group_currency.')
                        </th>
                        <th class="text-right">
                            Fines Paid ('.$this->group_currency.')
                        </th>
                    </tr>
                </thead>
                <tbody>';
                        $count = 1; 
                        $total_member_fine_arrears = 0;
                        $total_fines_paid = 0;
                        foreach($this->group_member_options as $member_id => $member_name): 
                        $member_fine_arrears = $group_member_total_fines_back_dated_arrears_per_fine_category_array[$member_id][$fine_category->id];
                        $total_member_fine_arrears += $member_fine_arrears;
                        $fines_paid = $group_member_total_fines_paid_back_dated_paid_per_fine_category_array[$member_id][$fine_category->id];
                        $total_fines_paid += $fines_paid;
                        echo '
                        <tr>
                            <td>'.$count++.'</td>
                            <td>'.$member_name.'</td>
                            <td class="text-right"> 
                                '.form_input('member_fine_arrears['.$member_id.']',$member_fine_arrears," class='form-control currency'").'
                            </td>
                            <td class="text-right"> 
                                '.form_input('fines_paid['.$member_id.']',$fines_paid," class='form-control currency'").'
                            </td>
                        </tr>'; 
                        endforeach; 
                echo '
                </tbody>
            </table>';
        }
    }

    function ajax_create_back_dating_fine_invoices(){
        $posts = $_POST;
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        if($group_cut_off_date){
            if(isset($posts)){
                $member_fine_arrears = $this->input->post('member_fine_arrears');
                if(empty($member_fine_arrears)){
                    echo "Member fine arrears post value empty";
                }else{

                    $members = $this->members_m->get_group_members();
                    $fine_category = $this->fine_categories_m->get_group_back_dating_fine_category();

                    $member_objects_array = array();
                    foreach($members as $member):
                        $member_objects_array[$member->id] = $member;
                    endforeach;

                    $result = TRUE;
                    foreach($member_fine_arrears as $member_id => $amount):
                        if(isset($member_objects_array[$member_id])){
                            if($fine_category){
                                if($amount){
                                    if($this->transactions->create_fine_invoice(2,$this->group->id,$group_cut_off_date->cut_off_date,$member_objects_array[$member_id],$fine_category->id,valid_currency($amount),FALSE,FALSE,"Back-dating fine invoice",TRUE)){

                                    }else{
                                        $result = FALSE;
                                    }
                                }
                            }else{
                                $result = FALSE;
                            }
                        }else{

                            $result = FALSE;
                        }
                    endforeach;
                    if($result){
                        echo "success";
                    }else{
                        echo "error";
                    }
                }
            }else{
                echo "No data posted";
            }
        }else{
            echo "Group cut off date not yet set";
        }
    }

    function ajax_void_group_back_dating_fine_invoices(){
        $invoices = $this->invoices_m->get_group_back_dating_fine_invoices();
        $result = TRUE;
        foreach($invoices as $invoice):
            if($this->transactions->void_fine_invoice($invoice->id,$invoice->fine_id)){

            }else{
                $result = FALSE;
            }
        endforeach;
        if($result){
            echo "success";
        }else{
            echo "error";
        }
    }

     function view($id = 0){
        $id OR redirect('group/invoices/listing');
        $post = $this->invoices_m->get_group_invoice($id);
        $post OR redirect('group/invoices/listing');
        $data['post'] = $post;
        $data['member'] = $this->members_m->get_group_member($post->member_id);
        $this->template->title('View Invoice')->build('shared/view',$data);
    }


}