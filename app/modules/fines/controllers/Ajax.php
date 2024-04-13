<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
	
	protected $data = array();
    protected $validation_rules = array(
        array(
            'field' => 'deposit_dates',
            'label' => 'Fine date',
            'rules' => 'required|trim'
        )
    );
      
    
    function __construct(){
        parent::__construct();
        $this->load->model('fines_m');
        $this->load->model('fine_categories/fine_categories_m');
        $this->load->model('invoices/invoices_m');
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

        $this->fine_invoice_type_options = array(
            2=>"Contribution fine invoice",
            3=>"Fine invoice",
            //5=>"Back dated contribution invoice",
            //6=>"Back dated contrbution fine invoice",
            //7=>"Back dated fine invoice",
            //8=>"Back dated general invoice",
        );
    }

    public function get_fines_listing(){
        $from = strtotime(xss_clean_input($this->input->get('from')))?:'';
        $to = strtotime(xss_clean_input($this->input->get('to')))?:'';
        $filter_parameters = array(
            'from' => $from,
            'to' => $to,
            'type' => $this->input->get('type')?:'',
            'member_id' => $this->input->get('member_id')?:'',
            'contributions' => $this->input->get('contributions')?:'',
            'fine_categories' => $this->input->get('fine_categories')?:'',
        );
        
        $total_rows = $this->invoices_m->count_group_fine_invoices($filter_parameters);
        $pagination = create_pagination('group/fines/listing/pages', $total_rows,50,5,TRUE);
        $fine_category_options = $this->fine_categories_m->get_group_options();
        $contribution_options = $this->contributions_m->get_group_contribution_options();
        $posts = $this->invoices_m->limit($pagination['limit'])->get_group_fine_invoices($filter_parameters);   
        if(!empty($posts)){
            echo form_open('group/invoices/action', ' id="form"  class="form-horizontal"');
                if ( ! empty($pagination['links'])):
                    echo '
                    <div class="row col-md-12">
                        <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Fines </p>';
            
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links']; 
                    echo '</div></div>';
                endif; 
                echo '
                <table class="table m-table m-table--head-separator-primary">
                    <thead>
                        <tr>
                            <th width=\'2%\'>
                                 <label class="m-checkbox">
                                    <input type="checkbox" name="check" value="all" class="check_all">
                                    <span></span>
                                </label>
                            </th>
                            <th width=\'2%\'>
                                #
                            </th>
                            <th>
                                Details
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
                            <tr id='.$post->id.'_active_row>
                                <td>
                                    <label class="m-checkbox">
                                        <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" />
                                        <span></span>
                                    </label>
                                </td>
                                <td>'.($i++).'</td>
                                <td><strong>Fine Date : </strong> '.timestamp_to_date($post->invoice_date).'<br/>
                                    <strong>Member : </strong> '.$this->group_member_options[$post->member_id].'<br/>
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
                                    <a href="'.site_url('group/invoices/view/'.$post->id).'" class="btn btn-sm btn-info btn-accent ">
                                        <i class="fa fa-eye"></i> View &nbsp;&nbsp; 
                                    </a>

                                    <a href="'.site_url('group/invoices/void/'.$post->id).'" class="btn btn-sm confirmation_link btn-danger m-btn m-btn--icon">
                                        <span>
                                            <i class="la la-trash"></i>
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
                    <button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_void\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-trash-o\'></i> Bulk Void</button>';
                endif;
            echo form_close(); 
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'.translate('Sorry').'!</strong> '.translate(' No invoices to display.').'.
                </div>';
        } 
    }

    function fine_members(){
        $data = array();
        $posts = $_POST;
        $errors = array();
        $response = array();
        $error_messages = array();
        $successes = array();
        $entries_are_valid = TRUE;        
        if(!empty($posts)){ 
            if(isset($posts['fine_dates'])){
                $count = count($posts['fine_dates']);
                for($i=0;$i<=$count;$i++):
                    if(isset($posts['fine_dates'][$i])&&isset($posts['members'][$i])&&isset($posts['fine_categories'][$i])&&isset($posts['amounts'][$i])):
                        //fine dates
                            if($posts['fine_dates'][$i]==''){
                                $successes['fine_dates'][$i] = 0;
                                $errors['fine_dates'][$i] = 1;
                                $error_messages['fine_dates'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['fine_dates'][$i] = 1;
                                $errors['fine_dates'][$i] = 0;
                            }
                        //members
                            if(empty($posts['members'][$i])){
                                $successes['members'][$i] = 0;
                                $errors['members'][$i] = 1;
                                $error_messages['members'][$i] = 'Please select at least one member';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['members'][$i] = 1;
                                $errors['members'][$i] = 0;
                            }
                        //fine categories
                            if($posts['fine_categories'][$i]==''){
                                $successes['fine_categories'][$i] = 0;
                                $errors['fine_categories'][$i] = 1;
                                $error_messages['fine_categories'][$i] = 'Please enter a fine category';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['fine_categories'][$i] = 1;
                                $errors['fine_categories'][$i] = 0;
                            }
                            //amounts
                            if($posts['amounts'][$i]==''){
                                $successes['amounts'][$i] = 0;
                                $errors['amounts'][$i] = 1;
                                $error_messages['amounts'][$i] = 'Please enter a fine amount';
                                $entries_are_valid = FALSE;
                            }else{
                                if(valid_currency($posts['amounts'][$i])){
                                    $successes['amounts'][$i] = 1;
                                    $errors['amounts'][$i] = 0;
                                }else{
                                    $successes['amounts'][$i] = 0;
                                    $errors['amounts'][$i] = 1;
                                    $error_messages['amounts'][$i] = 'Please enter a valid fine amount';
                                    $entries_are_valid = FALSE; 
                                }
                            }
                    endif;
                endfor;
            }
        }

        if($entries_are_valid){
            if(isset($posts['fine_dates'])){
                $count = count($posts['fine_dates']);
                $successful_fine_entry_count = 0;
                $unsuccessful_fine_entry_count = 0;
                $no_member_found_count = 0;
                $count = count($posts['fine_dates']);
                
                for($i=0;$i<=$count;$i++):
                    if(isset($posts['fine_dates'][$i])&&isset($posts['members'][$i])&&isset($posts['fine_categories'][$i])&&isset($posts['amounts'][$i])):
                        //fine dates
                        $send_sms_notification = isset($posts['send_sms_notification'][$i])?$posts['send_sms_notification'][$i]:0;
                        $send_email_notification = isset($posts['send_email_notification'][$i])?$posts['send_email_notification'][$i]:0;
                        $member_ids = $posts['members'];
                        foreach($member_ids as $member_id){
                            $member_count = count($member_id);
                            for ($j=0; $j < $member_count; $j++) {                                 
                                $member = $this->members_m->get_group_member($member_id[$j]);
                               
                                if($member){
                                    $amount = valid_currency($posts['amounts'][$i]);
                                    $fine_date = strtotime($posts['fine_dates'][$i]);
                                    if($this->transactions->create_fine_invoice(3,$this->group->id,$fine_date,$member,$posts['fine_categories'][$i],$amount,$send_sms_notification,$send_email_notification)){
                                        $successful_fine_entry_count++;
                                    }else{
                                        $unsuccessful_fine_entry_count++;
                                    }
                                }else{
                                    $no_member_found_count++;
                                }
                            }
                            
                        }
                    endif;
                endfor;
            }
            if($unsuccessful_fine_entry_count ||$no_member_found_count){
                $response = array(
                    'status' => 0,
                    'message' => 'Something went wrong no member found.',
                );
            }
            if($successful_fine_entry_count){
                if($successful_fine_entry_count==1){
                    $response = array(
                        'status' => 1,
                        'message' => $successful_fine_entry_count.' fine was successfully recorded',
                        'refer' => site_url('group/fines/listing'),
                    );
                }else{
                    $response = array(
                        'status' => 1,
                        'message' => $successful_fine_entry_count.' fines were successfully recorded',
                        'refer' => site_url('group/fines/listing'),
                    );
                }
            }
        }else{
            $response = array(
                'status' => 0,
                'validation_errors'=>$error_messages,
                'message' => 'There are some errors on the form. Please review and try again.',
            );
        }
        echo json_encode($response);

    }

}