<?php 
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Group extends Group_Controller{
    public $invoice_type_options;
	
    public function __construct(){
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

    public function fine_members(){
    	$data = array();
    	$posts = $_POST;
    	$errors = array();
    	$error_messages = array();
    	$successes = array();
    	if($this->input->post('submit')){
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
                        	$member_ids = $posts['members'][$i];
                        	foreach($member_ids as $member_id){
                        		$member = $this->members_m->get_group_member($member_id);
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
                        endif;
                    endfor;
                }
                if($unsuccessful_fine_entry_count||$no_member_found_count){
                	$this->session->set_flashdata('error','Something went wrong');
                }
                if($successful_fine_entry_count){
                    if($successful_fine_entry_count==1){
                        $this->session->set_flashdata('success',$successful_fine_entry_count.' fine was successfully recorded');
                    }else{
                        $this->session->set_flashdata('success',$successful_fine_entry_count.' fines were successfully recorded');
                    }
                }
                redirect('group/fines/listing');
            }
        }
    	$data['errors'] = $errors;
    	$data['error_messages'] = $error_messages;
    	$data['successes'] = $successes;
    	$data['posts'] = $posts;
    	$data['fine_category_options'] = $this->fine_categories_m->get_group_options();
        //print_r($data['fine_category_options']);
        //die;
        $this->template->title('Fine Members')->build('group/fine_members',$data);
    }

    public function listing(){
    	$data = array();
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        if($this->input->get('generate_excel')==1){
            $filter_parameters = array(
                'from' => $from,
                'to' => $to,
                'type' => $this->input->get('type')?:'',
                'member_id' => $this->input->get('member_id')?:'',
                'contributions' => $this->input->get('contributions')?:'',
                'fine_categories' => $this->input->get('fine_categories')?:'',
            );
            $data['fine_category_options'] = $this->fine_categories_m->get_group_options();
            $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
            $data['posts'] = $this->invoices_m->get_group_fine_invoices($filter_parameters);
            $data['group'] = $this->group;
            $data['group_currency'] = $this->group_currency;
            $data['invoice_type_options'] = $this->invoice_type_options;
            $data['group_member_options'] = $this->group_member_options;
            $json_file = json_encode($data);
            //print_r($json_file);die;
            print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/fines/listing',$this->group->name.' Fine List'));
            die;
        }
        $data['fine_category_options'] = $this->fine_categories_m->get_group_options();
        $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $data['fine_invoice_type_options'] = $this->fine_invoice_type_options;
        $data['from'] = $from;
        $data['to'] = $to;
        $this->template->title(translate('List Fines'))->build('group/listing',$data);
    }
    
    function index(){
        $this->template->title('Group Fines')->build('group/index');
    }
}