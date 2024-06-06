<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Bank extends Bank_Controller{

    protected $deposit_type_options = array(
        1 => "Contribution payment",
        2 => "Contribution fine payment",
        3 => "Fine payment",
        4 => "Incoming Bank Transfer",
        5 => "External deposit",
        6 => "Group Expense payment",
        7 => "Loan repayment",
        8 => "Financial Institution Loan",
        9 => "Other user defined deposit",
    );

    protected $transfer_to_options = array(
        1 => "Contribution share",
        2 => "Fine payment",
        3 => "Loan share",
        4 => "Another member",
    );

    protected $member_transfer_to_options = array(
        1 => "Contribution share",
        2 => "Fine payment",
        3 => "Loan share",
    );

	function __construct(){
        parent::__construct();
        $this->load->model('deposits_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->model('contributions/contributions_m');
        $this->load->model('fine_categories/fine_categories_m');
        $this->load->model('depositors/depositors_m');
        $this->load->model('income_categories/income_categories_m');
        $this->load->model('stocks/stocks_m');
        $this->load->model('assets/assets_m');
        $this->load->model('money_market_investments/money_market_investments_m');
        $this->load->library('transactions');
        $this->load->library('loan');
        $this->load->library('contribution_invoices');
        $this->load->library('excel_library');
        
        $this->sms_template_default = $this->contribution_invoices->sms_template_default;
    }

    function index(){
        $data = array();
        $data['total_group_contributions'] = $this->deposits_m->get_group_total_contributions();
        $data['total_group_fines'] = $this->deposits_m->get_group_total_fines();
        $data['total_miscelleneous_paid'] = $this->deposits_m->get_total_miscellaneous_amount_paid();
        $this->template->title(translate('Deposits'))->build('group/index',$data);
    }

    function generate_deposits_bulk_pdf_receipts($ids = array()){
        
    }

    // function set_checkoff($generate_pdf=FALSE,$generate_excel=FALSE){
    //     $data = array();
    //     $contribution_options = $this->contributions_m->get_group_checkoff_contribution_options();
    //     if(!$contribution_options){
    //         $this->session->set_flashdata('warning','You have not setup any savings contributions');
    //         if($this->agent->referrer() && ($this->agent->referrer() != current_url())){
    //             redirect($this->agent->referrer());
    //         }else{
    //             redirect('group/deposits/listing');
    //         }
    //     }
      
    //     $data['member_checkoff_contribution_amount_pairings'] = $this->contributions_m->get_group_member_checkoff_contribution_amount_pairings_array();
    //     $data['contribution_options'] = $contribution_options;
    //     $data['group'] = $this->group;
    //     $data['application_settings'] = $this->application_settings;
    //     $data['active_group_member_options'] = $this->active_group_member_options;
    //     $data['group_currency'] = $this->group_currency;
    //     $data['membership_numbers'] = $this->membership_numbers;
    //     if($generate_pdf==TRUE){
    //         if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
    //             $data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
    //         }else{
    //             $data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
    //         }
    //         $response = $this->curl_post_data->curl_post_json_pdf((json_encode($data)),'https://pdfs.chamasoft.com/deposits/set_checkoff',$this->group->name.' Checkoff');
    //         print_r($response);die;
    //     }
    //     if($generate_excel==TRUE){
    //         if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
    //             $data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
    //         }else{
    //             $data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
    //         }
    //         $response = $this->curl_post_data->curl_post_json_excel((json_encode($data)),'https://excel.chamasoft.com/deposits/set_checkoff',$this->group->name.' Checkoff');
    //         print_r($response);die;
    //     }
    //     $this->template->title('Set Check Off')->build('group/set_checkoff',$data);
    // }

    // function submit_checkoff(){
    //     $contribution_options = $this->contributions_m->get_group_checkoff_contribution_options();
    //     if(!$contribution_options){
    //         $this->session->set_flashdata('warning','You have not setup any savings contributions');
    //         if($this->agent->referrer() && ($this->agent->referrer() != current_url())){
    //             redirect($this->agent->referrer());
    //         }else{
    //             redirect('group/deposits/listing');
    //         }
    //     }
    //     set_time_limit(0);
    //     ini_set("memory_limit", "-1");
    //     $data = array();
    //     $validation_rules = array(
    //         array(
    //             'field' => 'checkoff_date',
    //             'label' => 'Checkoff Date',
    //             'rules' => 'required|trim',
    //         ),
    //         array(
    //             'field' => 'account_id',
    //             'label' => 'Account',
    //             'rules' => 'required|trim',
    //         ),
    //     );
    //     $this->form_validation->set_rules($validation_rules);
    //     if($this->form_validation->run()){
    //         $checkoff_date = strtotime($this->input->post('checkoff_date'));
    //         $account_id = $this->input->post('account_id');
    //         $input = array(
    //             'checkoff_date' => $checkoff_date,
    //             'group_id' => $this->group->id,
    //             'account_id' => $account_id,
    //             'active' => 1,
    //             'amount' => 0,
    //             'created_on' => time(),
    //             'created_by' => $this->user->id,
    //         );
    //         if($checkoff_id = $this->deposits_m->insert_checkoff($input)){
    //             $checkoff_amounts = $this->input->post('checkoff_amounts');
    //             $result = TRUE;
    //             $total_amount = 0;
    //             foreach($checkoff_amounts as $contribution_id => $members):
    //                 foreach($members as $member_id => $amount):
    //                     if($amount):
    //                         $amount = valid_currency($amount);
    //                         if($this->transactions->record_contribution_payment($this->group->id,$checkoff_date,$member_id,$contribution_id,$account_id,1,'',$amount,FALSE,FALSE,0,$checkoff_id)){
    //                             $total_amount+=$amount;
    //                         }else{
    //                             $result = FALSE;
    //                         }   
    //                     endif;
    //                 endforeach;
    //             endforeach;
    //             if($result){
    //                 $this->session->set_flashdata('success','Check off submitted successfully.');
    //                 $input = array(
    //                     'amount' => $total_amount,
    //                     'modified_by' => $this->user->id,
    //                     'modified_on' => time()
    //                 );
    //                 if($this->deposits_m->update_checkoff($checkoff_id,$input)){

    //                 }else{
    //                     $this->session->set_flashdata('warning','Something went wrong when updating the checkoff entries.');
    //                 }
    //             }else{
    //                 $this->session->set_flashdata('warning','Something went wrong when submitting the checkoff sheet.');
    //             }
    //         }else{
    //             $this->session->set_flashdata('error','Could not save checkoff ');
    //         }
    //         redirect('group/deposits/checkoffs_listing');
    //     }
    //     $data['account_options'] = $this->accounts_m->get_group_account_options();
    //     $data['member_checkoff_contribution_amount_pairings'] = $this->contributions_m->get_group_member_checkoff_contribution_amount_pairings_array();
    //     $data['contribution_options'] = $contribution_options;
    //     $data['membership_numbers'] = $this->membership_numbers;
    //     $this->template->title('Submit Check Off')->build('group/submit_checkoff',$data);
    // }

    // function view_checkoff($id = 0,$generate_pdf = FALSE,$generate_excel=FALSE){
    //     $data = array();
    //     $id OR redirect('group/deposits/checkoffs_listing');
    //     $post = $this->deposits_m->get_group_checkoff($id);
    //     $post OR redirect('group/deposits/checkoffs_listing');
    //     $data['post'] = $post;
    //     $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
    //     $data['checkoff_amounts'] = $this->deposits_m->get_group_checkoff_amounts_array_by_checkoff_id($post->id);
    //     $member_ids = array();
    //     foreach($data['checkoff_amounts'] as $contribution_id => $members):
    //         foreach($members as $member_id => $amount):
    //             if(in_array($member_id,$member_ids)){

    //             }else{
    //                 $member_ids[] = $member_id;
    //             }
    //         endforeach;
    //     endforeach; 
    //     $data['member_ids'] = $member_ids;
    //     $data['accounts'] = $this->accounts_m->get_group_account_options(FALSE);
    //     $data['group'] = $this->group;
    //     $data['application_settings'] = $this->application_settings;
    //     $data['group_member_options'] = $this->group_member_options;
    //     $data['group_currency'] = $this->group_currency;
    //     $data['membership_numbers'] = $this->membership_numbers;
    //     if($this->input->get('generate_pdf') == 1){
    //         if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
    //             $data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
    //         }else{
    //             $data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
    //         }
    //         $response = $this->curl_post_data->curl_post_json_pdf((json_encode($data)),'https://pdfs.chamasoft.com/deposits/view_checkoff',$this->group->name.' View Checkoff');
    //         print_r($response);die;
    //     }
    //     if($this->input->get('generate_excel') == 1){
    //         if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
    //             $data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
    //         }else{
    //             $data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
    //         }
    //         $response = $this->curl_post_data->curl_post_json_excel((json_encode($data)),'https://excel.chamasoft.com/deposits/view_checkoff',$this->group->name.' View Checkoff');
    //         print_r($response);die;
    //     }
    //     $this->template->set_layout('default_full_width.html')->title('View Check Off')->build('group/view_checkoff',$data);
    // }


    // function checkoffs_listing(){
    //     $data = array();
    //     $data['posts'] = $this->deposits_m->get_group_checkoffs();
    //     $this->template->title('Check Offs')->build('group/checkoffs_listing',$data);
    // }


    function listing($generate_excel = 0){
        $data = array();
        $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $data['from'] = strtotime($this->input->get('from'))?:'';
        $data['to'] = strtotime($this->input->get('to'))?:'';
        $data['group_member_options'] = $this->members_m->get_group_member_options();
        $data['deposit_type_options'] = $this->transactions->deposit_type_options;
        $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
        $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
        $data['income_category_options'] = $this->income_categories_m->get_group_income_category_options();
        $data['stock_options'] = $this->stocks_m->get_group_stock_options();
        $data['asset_options'] = $this->assets_m->get_group_asset_options();
        $data['money_market_investment_options'] = $this->money_market_investments_m->get_group_money_market_investment_options('',$this->group_currency);
        if($this->input->get_post('generate_excel')==1){
            $transaction_alert_id = $this->input->get('transaction_alert');
            $from = strtotime($this->input->get('from'))?:'';
            $to = strtotime($this->input->get('to'))?:'';
            $filter_parameters = array(
                'transaction_alert_id' => $transaction_alert_id,
                'member_id' => $this->input->get('member_id')?:'',
                'type' => $this->input->get('deposit_for')?:'',
                'contributions' => $this->input->get('contributions')?:'',
                'fine_categories' => $this->input->get('fine_categories')?:'',
                'income_categories' => $this->input->get('income_categories')?:'',
                'stocks' => $this->input->get('stocks')?:'',
                'money_market_investments' => $this->input->get('money_market_investments')?:'',
                'assets' => $this->input->get('assets')?:'',
                'accounts' => $this->input->get('accounts')?:'',
                'from' => $from,
                'to' => $to,
            );
            $data['deposit_transaction_names'] = $this->transactions->deposit_transaction_names;
            $data['deposit_type_options'] = $this->transactions->deposit_type_options;
            $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
            $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
            $data['deposit_method_options'] = $this->transactions->deposit_method_options;
            $data['deposit_for_options'] = $this->transactions->deposit_for_options;
            $data['depositor_options'] = $this->depositors_m->get_group_depositor_options();
            $data['income_category_options'] = $this->income_categories_m->get_group_income_category_options();
            $data['stock_options'] = $this->stocks_m->get_group_stock_options();
            $data['money_market_investment_options'] = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
            $data['accounts'] = $this->accounts_m->get_group_account_options(FALSE);
            $data['posts'] = $this->deposits_m->get_group_deposits($this->group->id,$filter_parameters);
            $data['from'] = $from;
            $data['to'] = $to;
            $data['group_member_options'] = $this->members_m->get_group_member_options();
            $data['group_debtor_options'] = $this->group_debtor_options;
            $data['filters'] = $filter_parameters;
            $data['group'] = $this->group;
            $data['group_currency'] = $this->group_currency;
            $json_file = json_encode($data);
            $this->excel_library->generate_deposits_listing($json_file);
           print_r($json_file); die();
            // print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/deposits/listing',$this->group->name.' List of Deposits'));
            // die;
        }
        $this->template->title(translate('List Deposits'))->build('group/listing',$data);
    }

    function record_contribution_payments(){
    	$data = array();
        $posts = $_POST;
        $errors = array();
        $error_messages = array();
        $successes = array();
        if($this->input->post('submit')){
            $entries_are_valid = TRUE;
            if(!empty($posts)){ 
                if(isset($posts['deposit_dates'])){
                    $count = count($posts['deposit_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['deposit_dates'][$i])&&isset($posts['members'][$i])&&isset($posts['contributions'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])&&isset($posts['deposit_methods'][$i])):    
                            //Deposit dates
                            if($posts['deposit_dates'][$i]==''){
                                $successes['deposit_dates'][$i] = 0;
                                $errors['deposit_dates'][$i] = 1;
                                $error_messages['deposit_dates'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            }else if(date('Ymd',strtotime($posts['deposit_dates'][$i]))!= date('Ymd',time())){
                                $successes['deposit_dates'][$i] = 0;
                                $errors['deposit_dates'][$i] = 1;
                                $error_messages['deposit_dates'][$i] = 'Please enter a date not greater or less than today';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['deposit_dates'][$i] = 1;
                                $errors['deposit_dates'][$i] = 0;
                            }
                            //Members
                            if($posts['members'][$i]==''){
                                $successes['members'][$i] = 0;
                                $errors['members'][$i] = 1;
                                $error_messages['members'][$i] = 'Please select a member';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['members'][$i])){
                                    $successes['members'][$i] = 1;
                                    $errors['members'][$i] = 0;
                                }else{
                                    $successes['members'][$i] = 0;
                                    $errors['members'][$i] = 1;
                                    $error_messages['members'][$i] = 'Please enter a valid member value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Contributions
                            if($posts['contributions'][$i]==''){
                                $successes['contributions'][$i] = 0;
                                $errors['contributions'][$i] = 1;
                                $error_messages['contributions'][$i] = 'Please select a contribution';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['contributions'][$i])){
                                    $successes['contributions'][$i] = 1;
                                    $errors['contributions'][$i] = 0;
                                }else{
                                    $successes['contributions'][$i] = 0;
                                    $errors['contributions'][$i] = 1;
                                    $error_messages['contributions'][$i] = 'Please select a valid contribution value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                             //Accounts
                            if($posts['accounts'][$i]==''){
                                $successes['accounts'][$i] = 0;
                                $errors['accounts'][$i] = 1;
                                $error_messages['accounts'][$i] = 'Please select an account';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['accounts'][$i] = 1;
                                $errors['accounts'][$i] = 0;
                            }
                            //Deposit Method
                            if($posts['deposit_methods'][$i]==''){
                                $successes['deposit_methods'][$i] = 0;
                                $errors['deposit_methods'][$i] = 1;
                                $error_messages['deposit_methods'][$i] = 'Please select a deposit method';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['deposit_methods'][$i])){
                                    $successes['deposit_methods'][$i] = 1;
                                    $errors['deposit_methods'][$i] = 0;
                                }else{
                                    $successes['deposit_methods'][$i] = 0;
                                    $errors['deposit_methods'][$i] = 1;
                                    $error_messages['deposit_methods'][$i] = 'Please enter a valid deposit method';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //amounts
                            if($posts['amounts'][$i]=='' || $posts['amounts'][$i] < 1){
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
                $this->session->set_flashdata('error','');
                $successful_contribution_payment_entry_count = 0;
                $unsuccessful_contribution_payment_entry_count = 0;
                $contribution_payments = array();

                if(isset($posts['deposit_dates'])){
                    $count = count($posts['deposit_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['deposit_dates'][$i])&&isset($posts['members'][$i])&&isset($posts['contributions'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])&&isset($posts['deposit_methods'][$i])): 

                            $contribution_payment = new stdClass();

                            $amount = currency($posts['amounts'][$i]);
                            $deposit_date = strtotime($posts['deposit_dates'][$i]); 
                            $send_sms_notification = isset($posts['send_sms_notification'][$i])?$posts['send_sms_notification'][$i]:0;
                            $send_email_notification = isset($posts['send_email_notification'][$i])?$posts['send_email_notification'][$i]:0;
                            $description = isset($posts['deposit_descriptions'][$i])?$posts['deposit_descriptions'][$i]:'';

                            $contribution_payment->deposit_date = $deposit_date;
                            $contribution_payment->member_id = $posts['members'][$i];
                            $contribution_payment->contribution_id = $posts['contributions'][$i];
                            $contribution_payment->account_id = $posts['accounts'][$i];
                            $contribution_payment->amount = $amount;
                            $contribution_payment->deposit_method = $posts['deposit_methods'][$i];
                            $contribution_payment->send_sms_notification = $send_sms_notification;
                            $contribution_payment->send_email_notification = $send_email_notification;
                            $contribution_payment->description = $description;

                            $contribution_payments[] = $contribution_payment;
                            

                                // if($this->transactions->record_contribution_payment($this->group->id,$deposit_date,$posts['members'][$i],$posts['contributions'][$i],$posts['accounts'][$i],$posts['deposit_methods'][$i],$description,$amount,$send_sms_notification,$send_email_notification)){
                                //     $successful_contribution_payment_entry_count++;
                                // }else{
                                //     $unsuccessful_contribution_payment_entry_count++;
                                // }




                        endif;
                    endfor;

                    if($this->transactions->record_group_contribution_payments($this->group->id,$contribution_payments)){
                        //die("am in");
                        $this->session->set_flashdata('success','Contributions recorded successfully.');
                    }else{
                        $this->session->set_flashdata('warning','Something went wrong while recording the contribution payments.');
                    }
                }
                if($successful_contribution_payment_entry_count){
                    if($successful_contribution_payment_entry_count==1){
                        $this->session->set_flashdata('success',$successful_contribution_payment_entry_count.' contribution payment successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('success',$successful_contribution_payment_entry_count.' contribution payments successfully recorded. ');
                    }
                }
                if($unsuccessful_contribution_payment_entry_count){
                    if($unsuccessful_contribution_payment_entry_count==1){
                        $this->session->set_flashdata('error',$unsuccessful_contribution_payment_entry_count.' contribution payment was not successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('error',$unsuccessful_contribution_payment_entry_count.' contribution payments were not successfully recorded. ');
                    }
                }
                redirect('group/deposits/listing');
            }else{
                $this->session->set_flashdata('error','There are some errors on the form. Please review and try again.');
            }
        }
        $data['group_role_options'] = $this->group_roles_m->get_group_role_options();
        $data['placeholders'] = isset($placeholders[0])?$placeholders[0]:array();
        $data['invoice_days'] = $this->contribution_invoices->invoice_days;
        $data['month_days'] = $this->contribution_invoices->month_days;
        $data['week_days'] = $this->contribution_invoices->week_days;
        $data['days_of_the_month'] = $this->contribution_invoices->days_of_the_month;
        $data['every_two_week_days'] = $this->contribution_invoices->every_two_week_days;
        $data['months'] = $this->contribution_invoices->months;
        $data['starting_months'] = $this->contribution_invoices->starting_months;
        $data['week_numbers'] = $this->contribution_invoices->week_numbers;
        $data['contribution_frequency_options'] = $this->contribution_invoices->contribution_frequency_options;
        $data['contribution_type_options'] = $this->contribution_invoices->contribution_type_options;
        $data['sms_template_default'] = $this->sms_template_default;
        $data['fine_types'] = $this->contribution_invoices->fine_types;
        $data['fine_chargeable_on_options'] = $this->contribution_invoices->fine_chargeable_on_options;
        $data['fine_frequency_options'] = $this->contribution_invoices->fine_frequency_options;
        $data['fine_mode_options'] = $this->contribution_invoices->fine_mode_options;
        $data['fine_limit_options'] = $this->contribution_invoices->fine_limit_options;
        $data['percentage_fine_on_options'] = $this->contribution_invoices->percentage_fine_on_options;
        $data['errors'] = $errors;
        $data['error_messages'] = $error_messages;
        $data['successes'] = $successes;
        $data['deposit_type_options'] = $this->deposit_type_options;
        $data['deposit_method_options'] = $this->transactions->deposit_method_options;
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        $data['contribution_options'] = $this->contributions_m->get_active_group_contribution_options();
        $data['banks'] = $this->banks_m->get_group_bank_options();
        $data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $data['contribution_category_options'] = $this->contribution_invoices->contribution_category_options;
        $data['posts'] = $posts;
        $this->template->title(translate('Record Contribution Payments'))->build('group/record_contribution_payments',$data);
    }

    function contribution_template_download(){
        $this->data['group'] = $this->group;
        $this->data['contribution_options'] = $this->contributions_m->get_active_group_contribution_options();
        $this->data['active_group_member_options'] = $this->members_m->get_active_group_members($this->group->id);
        // $this->data['active_group_member_options'] = $this->active_group_member_options;
        $this->data['group_currency'] = $this->group_currency;
        $json_file = json_encode($this->data);
        print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/deposits/contribution_template',$this->group->name.' contributions_template'));
            die;
    }

    function payments_template_download(){
        $this->data['group'] = $this->group;
        $this->data['group_currency'] = $this->group_currency;
        $this->data['contribution_options'] = $this->contributions_m->get_active_group_contribution_options();
        $this->data['active_group_member_options'] = $this->members_m->get_active_group_members($this->group->id);
        $this->data['loans'] = $this->loans_m->get_active_group_loans();
        $this->data['interest_types'] = $this->loan->interest_types;
        $this->data['loan_interest_rate_per'] = $this->loan->loan_interest_rate_per;
        $json_file = json_encode($this->data);
        print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/deposits/payments_template',$this->group->name.' contributions_template'));
            die;
    }

    function loan_repayment_template_download(){
        $this->data['group'] = $this->group;
        $this->data['active_group_member_options'] = $this->active_group_member_options;
        $this->data['loans'] = $this->loans_m->get_active_group_loans();
        $this->data['group_currency'] = $this->group_currency;
        $this->data['interest_types'] = $this->loan->interest_types;
        $this->data['loan_interest_rate_per'] = $this->loan->loan_interest_rate_per;
        $json_file = json_encode($this->data);
        print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/deposits/loan_repayment_template',$this->group->name.' loan repayment template'));
            die;
    }

    function upload_contribution_payments(){
        set_time_limit(0);
        ini_set('memory_limit','1536M');
        $data = array();
        $post = new stdClass();
        $validation_rules = array(
            array(
                'field' =>  'account_id',
                'label' =>  'Account',
                'rules' =>  'trim|required',
            ),
        );
        $this->form_validation->set_rules($validation_rules);
        if($this->form_validation->run()){
            $directory = './uploads/files/csvs';
            if(!is_dir($directory)){
                mkdir($directory,0777,TRUE);
            }
            $config['upload_path'] = FCPATH . 'uploads/files/csvs/';
            $config['allowed_types'] = 'xls|xlsx|csv';
            $config['max_size'] = '1024';
            $account_id = $this->input->post('account_id');
            $send_sms_notification = $this->input->post('send_sms_notification');
            $send_email_notification = $this->input->post('send_email_notification');
            $deposit_method = 1;
            $description = $this->input->post('description');
            $this->load->library('upload',$config);
            if($this->upload->do_upload('contributions_template')){
                $unsuccessful_invitations_count = 0;
                $upload_data = $this->upload->data();
                $file_path = $upload_data['full_path'];
                $this->load->library('Excel');
                $excel_sheet = new PHPExcel();
                if(file_exists($file_path)){
                    $file_type = PHPExcel_IOFactory::identify($file_path);
                    $excel_reader = PHPExcel_IOFactory::createReader($file_type);
                    $excel_book = $excel_reader->load($file_path);
                    $sheet = $excel_book->getSheet(0);
                    $contribution_options = $this->contributions_m->get_active_group_contribution_options();
                    $allowed_column_headers = array_merge(array('','Member Name','Membership Number','Member Email','Deposit Date'),$contribution_options);
                    $contribution_ids = array();
                    $count = count($allowed_column_headers)-1;
                    for($column = 0; $column <= $count; $column++){
                        $value = $sheet->getCellByColumnAndRow($column, 2)->getValue();
                        $value = str_replace('Amount ('.$this->group_currency.')','',$value);
                        if(in_array(trim($value), $allowed_column_headers)){
                            $column_validation = true;
                            if(in_array(trim($value), $contribution_options)){
                                $contribution_ids[$column] = array_search(trim($value), $contribution_options);
                            }
                        }else{
                            $column_validation = false;
                            break;
                        }
                    }
                    if($column_validation){
                        $highestRow = $sheet->getHighestRow();
                        $member_payments = array();
                        $contribution_payment_amounts = array();
                        for($row = 3; $row <= $highestRow; $row++){
                            $member_name = '';
                            $member_id = 0;
                            for($column = 0; $column <= $count; $column++){
                                if($column == 1){
                                    $member_name = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                    $member_id = array_search($member_name, $this->active_group_member_options);
                                }
                                if($column == 4){
                                    if(PHPExcel_Shared_Date::isDateTime($sheet->getCellByColumnAndRow($column,$row))){
                                         $deposit_date = PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow($column,$row)->getValue()); 
                                    }else{
                                         $deposit_date = strtotime($sheet->getCellByColumnAndRow($column,$row)->getValue()); 
                                    }

                                }
                                foreach($contribution_ids as $column_key => $contribution_id){
                                    $contribution_payment_amounts[$contribution_id] = $sheet->getCellByColumnAndRow($column_key,$row)->getValue();
                                }
                            }
                            $member_payments[] = array(
                                'member_id' => $member_id,
                                'member_name' => $member_name,
                                'deposit_date' => $deposit_date,
                                'payment_amounts' => $contribution_payment_amounts,
                                'total_member_payments' => array_sum($contribution_payment_amounts),
                            );
                        }
                        if(empty($member_payments)){
                            $this->session->set_flashdata('info','Error in the import list file');
                        }else{
                            $successes = 0;
                            $fails = 0;
                            $duplicates = 0;
                            $ignores = 0;
                            $errors = 0;
                            $phones = array();
                            $emails = array();
                            $row = 2;
                            $contribution_payments = array();
                            foreach($member_payments as $member_payment){
                                $member_payment = (object)$member_payment;
                                $member_id = $member_payment->member_id;
                                $deposit_date = $member_payment->deposit_date;
                                // $deposit_date = strtotime($member_payment->deposit_date);
                                if($member_id){
                                    foreach ($member_payment->payment_amounts as $contribution_id => $amount) {
                                        if(currency($amount)>0){

                                            if(number_to_currency($amount) && $contribution_id){
                                                $contribution_payment = new stdClass();

                                                $contribution_payment->deposit_date = $member_payment->deposit_date;
                                                $contribution_payment->member_id = $member_payment->member_id;
                                                $contribution_payment->contribution_id = $contribution_id;
                                                $contribution_payment->account_id = $account_id;
                                                $contribution_payment->amount = $amount;
                                                $contribution_payment->deposit_method = $deposit_method;
                                                $contribution_payment->send_sms_notification = $send_sms_notification;
                                                $contribution_payment->send_email_notification = $send_email_notification;
                                                $contribution_payment->description = $description;

                                                $contribution_payments[] = $contribution_payment;

                                                //add more like account_id,#deposit_date etc
                                                // if($this->transactions->record_contribution_payment($this->group->id,
                                                //     $deposit_date,
                                                //     $member_id,
                                                //     $contribution_id,
                                                //     $account_id,
                                                //     $deposit_method,
                                                //     $description,
                                                //     $amount,
                                                //     $send_sms_notification,
                                                //     $send_email_notification)){
                                                //     ++$successes;
                                                // }else{
                                                //     ++$fails;
                                                // }

                                           }
                                        }
                                    }
                                }else{
                                    ++$errors;
                                }
                            }
                            //die();

                            if($this->transactions->record_group_contribution_payments($this->group->id,$contribution_payments)){
                                //die("am in");
                                $this->session->set_flashdata('success','Contributions recorded successfully.');
                            }else{
                                $this->session->set_flashdata('warning','Something went wrong while recording the contribution payments.');
                            }
                            // if($errors){
                            //     if($errors==1){
                            //         $this->session->set_flashdata('error',$errors.' error encountered while importing, some details were missing.');
                            //     }else{
                            //         $this->session->set_flashdata('error',$errors.' errors encountered while importing, some details were missing.');
                            //     }
                            //     foreach($validation_rules as $key => $field){
                            //         $post->$field['field'] = set_value($field['field']);
                            //     }
                            // }
                            // if($successes){
                            //     if($successes==1){
                            //         $this->session->set_flashdata('success',$successes.' payment was recorded.');
                            //     }else{
                            //         $this->session->set_flashdata('success',$successes.' were recorded.');
                            //     }
                            // }
                            // if($fails){
                            //     if($fails==1){
                            //        $this->session->set_flashdata('error',$fails.' fails encountered while importing, some details were missing.');
                            //     }else{
                            //         $this->session->set_flashdata('error',$fails.' fails encountered while importing, some details were missing.');
                            //     }
                            //     foreach($validation_rules as $key => $field){
                            //         $post->$field['field'] = set_value($field['field']);
                            //     }
                            // }
                        }
                    }else{
                        $this->session->set_flashdata('error','Contibution Payment list file does not have the correct format');
                    }
                }else{
                    $this->session->set_flashdata('error','Contibution Payment list file was not found');
                }
            }else{
                $this->session->set_flashdata('error','Contibution Payment list file type is not allowed');
            }
            redirect('group/deposits/listing');
        }else{
            foreach($validation_rules as $key => $field){
                $field_name = $field['field'];
                $post->$field_name = set_value($field['field']);
            }
        }
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        $data['post'] = $post;
        $this->template->title(translate('Upload Contribution Payments'))->build('group/upload_contribution_payments',$data);
    }

    function upload_payments($transaction_alert_id = 0){
        $transaction_alert_id OR redirect('group/transaction_alerts/reconcile_deposits');
        $transaction_alert = $this->transaction_alerts_m->get_group_transaction_alert($transaction_alert_id,$this->group_partner_bank_account_number_list,$this->group_partner_mobile_money_account_number_list);
        $transaction_alert OR redirect('group/transaction_alerts/reconcile_deposits');
        if($transaction_alert->reconciled){
            redirect('group/transaction_alerts/reconcile_deposits');
        }
        set_time_limit(0);
        ini_set('memory_limit','1536M');
        $data = array();
        $post = new stdClass();
        $validation_rules = array(
           
        );
        $this->form_validation->set_rules($validation_rules);
        if($this->input->post('import')){
            $directory = './uploads/files/csvs';
            if(!is_dir($directory)){
                mkdir($directory,0777,TRUE);
            }
            $config['upload_path'] = FCPATH . 'uploads/files/csvs/';
            $config['allowed_types'] = 'xls|xlsx|csv';
            $config['max_size'] = '1024';
            $deposit_date = strtotime($this->input->post('deposit_date'));
            $account_id = $this->input->post('account_id');
            $send_sms_notification = $this->input->post('send_sms_notification');
            $send_email_notification = $this->input->post('send_email_notification');
            $deposit_method = 1;
            $description = $this->input->post('description');
            $this->load->library('upload',$config);
            if($this->upload->do_upload('contributions_template')){
                // $unsuccessful_invitations_count = 0;
                $upload_data = $this->upload->data();
                $file_path = $upload_data['full_path'];
                $this->load->library('Excel');
                $excel_sheet = new PHPExcel();
                if(file_exists($file_path)){
                    $file_type = PHPExcel_IOFactory::identify($file_path);
                    $excel_reader = PHPExcel_IOFactory::createReader($file_type);
                    $excel_book = $excel_reader->load($file_path);
                    $sheet = $excel_book->getSheet(0);
                    $contribution_options = $this->contributions_m->get_active_group_contribution_options();
                    $allowed_column_headers = array_merge(array('','Member Name','Membership Number'),$contribution_options);
                    $contribution_ids = array();
                    $count = count($allowed_column_headers)-1;
                    for($column = 0; $column <= $count; $column++){
                        $value = $sheet->getCellByColumnAndRow($column,2)->getValue();
                        $value = str_replace('Amount ('.$this->group_currency.')','',$value);
                        if(in_array(trim($value), $allowed_column_headers)){
                            $column_validation = true;
                            if(in_array(trim($value), $contribution_options)){
                                $contribution_ids[$column] = array_search(trim($value), $contribution_options);
                            }
                        }else{
                            $column_validation = false;
                            break;
                        }
                    }
                    try{
                        $sheet = $excel_book->getSheet(1);
                        $allowed_column_headers = array('','Loan ID','Member Name','Loan Description',' Amount');
                        $count = count($allowed_column_headers)-1;
                        for($column = 0; $column <= $count; $column++){
                            $value = $sheet->getCellByColumnAndRow($column,2)->getValue();
                            $value = str_replace('Repayment Amount ('.$this->group_currency.')','',$value);
                            if(in_array(trim($value), $allowed_column_headers)){
                                $column_validation = true;
                            }else{
                                $column_validation = false;
                                break;
                            }
                        }
                        if($column_validation){
                            $total_amount = 0;
                            //die;
                            $sheet = $excel_book->getSheet(0);
                            $highestRow = $sheet->getHighestRow();
                            $contribution_payments = array();
                            $contribution_payment_amounts = array();
                            for($row = 3; $row <= $highestRow; $row++){
                                $member_name = '';
                                $member_id = 0;
                                for($column = 0; $column <= $count; $column++){
                                    if($column == 1){
                                        $member_name = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                        $member_id = array_search($member_name, $this->active_group_member_options);
                                    }
                                    foreach($contribution_ids as $column_key => $contribution_id){
                                        //$contribution_payment = new stdClass();
                                        // $contribution_payment_amounts[$contribution_id] = $sheet->getCellByColumnAndRow($column_key,$row)->getValue();
                                        

                                        $contribution_payment->contribution_id = $contribution_id;
                                        $contribution_payment->amount = $sheet->getCellByColumnAndRow($column_key,$row)->getValue();


                                    }
                                }
                                $contribution_payment->member_id = $member_id;
                                // $contribution_payments[] = array(
                                //     'member_id' => $member_id,
                                //     'member_name' => $member_name,
                                //     'payment_amounts' => $contribution_payment_amounts,
                                //     'total_member_payments' => array_sum($contribution_payment_amounts),
                                // );
                                $total_amount += array_sum($contribution_payment_amounts);

                            }

                            $sheet = $excel_book->getSheet(1);
                            $highestRow = $sheet->getHighestRow();
                            $loan_repayments = array();
                            for($row = 3; $row <= $highestRow; $row++){
                                $member_name = '';
                                $member_id = 0;
                                for($column = 0; $column <= $count; $column++){
                                    if($column == 1){
                                        $loan_id = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                    }
                                    if($column == 2){
                                        $member_name = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                        $member_id = array_search($member_name,$this->active_group_member_options);
                                    }
                                    if($column == 4){
                                        $repayment_amount = valid_currency($sheet->getCellByColumnAndRow($column,$row)->getValue());
                                    }
                                }
                                $loan_repayments[] = array(
                                    'loan_id' => $loan_id,
                                    'member_id' => $member_id,
                                    'repayment_amount' => $repayment_amount,
                                );
                                $total_amount += $repayment_amount;
                            }
                            if($total_amount == $transaction_alert->amount){
                                $bank_account = $this->bank_accounts_m->get_group_bank_account_by_account_number($transaction_alert->account_number);
                                if($bank_account){
                                    $contribution_payment->account_id = 'bank-'.$bank_account->id;
                                    $contribution_payment->deposit_date = $transaction_alert->transaction_date;
                                    $contribution_payment->deposit_method = $deposit_method;
                                    $contribution_payment->send_sms_notification = $send_sms_notification;
                                    $contribution_payment->send_email_notification = $send_email_notification;
                                    $contribution_payment->description = $description;
                                    $contribution_payments[] = $contribution_payment;

                                    $account_id = 'bank-'.$bank_account->id;
                                    $deposit_date = $transaction_alert->transaction_date;
                                    $successes = 0;
                                    $fails = 0;
                                    $duplicates = 0;
                                    $ignores = 0;
                                    $errors = 0;
                                    $phones = array();
                                    $emails = array();
                                    $row = 2;
                                    $this->record_group_contribution_payments($this->group->id,$contribution_payments);
                                    // foreach($contribution_payments as $member_payment){
                                    //     $member_payment = (object)$member_payment;
                                    //     $member_id = $member_payment->member_id;
                                    //     if($member_id){
                                    //         foreach ($member_payment->payment_amounts as $contribution_id => $amount) {
                                    //             if(currency($amount)>0){
                                    //                 if(number_to_currency($amount) && $contribution_id){
                                    //                 //add more like account_id,#deposit_date etc
                                    //                     if($this->transactions->record_contribution_payment(
                                    //                         $this->group->id,
                                    //                         $deposit_date,
                                    //                         $member_id,
                                    //                         $contribution_id,
                                    //                         $account_id,
                                    //                         $deposit_method,
                                    //                         $description,
                                    //                         $amount,
                                    //                         $send_sms_notification,
                                    //                         $send_email_notification,
                                    //                         $transaction_alert->id
                                    //                     )){
                                    //                         ++$successes;
                                    //                     }else{
                                    //                         ++$fails;
                                    //                     }
                                    //                 }
                                    //             }
                                    //         }
                                    //     }else{
                                    //         ++$errors;
                                    //     }
                                    // }
                                    $row = 2;
                                    foreach($loan_repayments as $loan_repayment){
                                        $loan_repayment = (object)$loan_repayment;
                                        $member_id = $loan_repayment->member_id;
                                        $loan_id = $loan_repayment->loan_id;
                                        $repayment_amount = $loan_repayment->repayment_amount;
                                        $repayment_date = $transaction_alert->transaction_date;

                                        if($member_id&&$loan_id&&valid_currency($repayment_amount)){
                                            if($member = $this->members_m->get_group_member($member_id)){
                                                if($loan = $this->loans_m->get_group_loan($loan_id)){
                                                    if($this->loan->record_loan_repayment(
                                                        $this->group->id,
                                                        $repayment_date,
                                                        $member,
                                                        $loan_id,
                                                        $account_id,
                                                        2,
                                                        '',
                                                        $repayment_amount,
                                                        $send_sms_notification,
                                                        $send_email_notification,
                                                        $this->user,
                                                        $member->user_id,
                                                        $transaction_alert->id,
                                                        FALSE)
                                                    ){
                                                        ++$successes;
                                                    }else{
                                                        ++$errors;
                                                    }
                                                }else{
                                                    ++$errors;
                                                }
                                            }else{
                                                ++$errors;
                                            }
                                        }else{
                                            ++$errors;
                                        }
                                    }


                                    if($errors){
                                        if($errors==1){
                                            //$this->session->set_flashdata('error',$errors.' error encountered while importing, some details were missing.');
                                        }else{
                                            //$this->session->set_flashdata('error',$errors.' errors encountered while importing, some details were missing.');
                                        }
                                        foreach($validation_rules as $key => $field){
                                            $post->$field['field'] = set_value($field['field']);
                                        }
                                    }
                                    if($successes){
                                        if($successes==1){
                                            $this->session->set_flashdata('success',$successes.' payment was recorded.');
                                        }else{
                                            $this->session->set_flashdata('success',$successes.' were recorded.');
                                        }
                                    }
                                    if($fails){
                                        if($fails==1){
                                            //$this->session->set_flashdata('error',$fails.' fails encountered while importing, some details were missing.');
                                        }else{
                                            //$this->session->set_flashdata('error',$fails.' fails encountered while importing, some details were missing.');
                                        }
                                        foreach($validation_rules as $key => $field){
                                            $post->$field['field'] = set_value($field['field']);
                                        }
                                    }

                                    if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                                    }else{
                                        $this->session->set_flashdata('error','Could not mark the transaction alert as reconciled');

                                    }
                                }else{
                                    $this->session->set_flashdata('error','Could not find the bank account to credit');
                                }
                            }else{
                                $this->session->set_flashdata('error','The total amount submitted in the excel is not equal to the transaction amount; submitted total is '.number_to_currency($total_amount).' it should be '.number_to_currency($transaction_alert->amount));
                            }
                        }else{
                            $this->session->set_flashdata('error','Contibution Payment list file does not have the correct format');
                        }
                    }catch(Exception $e){
                        $this->session->set_flashdata('error','Seems you are using the wrong excel format, download the correct format on the intruction panel below, fill it then upload it');
                        redirect('group/deposits/upload_payments/'.$transaction_alert_id);
                    }
                }else{
                    $this->session->set_flashdata('error','Contibution Payment list file was not found');
                }
            }else{
                $this->session->set_flashdata('error','Contibution Payment list file type is not allowed');
            }
            redirect('group/deposits/listing');
        }else{
            foreach($validation_rules as $key => $field){
                $field_name = $field['field'];
                $post->$field_name = set_value($field['field']);
            }
        }
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',TRUE);
        $data['post'] = $post;
        $data['transaction_alert'] = $transaction_alert;
        $this->template->title('Upload Payments')->build('group/upload_payments',$data);
    }

    function upload_loan_repayments(){
        set_time_limit(0);
        ini_set('memory_limit','1536M');
        $data = array();
        $post = new stdClass();
        $validation_rules = array(
            array(
                'field' =>  'repayment_date',
                'label' =>  'Loan Repayment Date',
                'rules' =>  'trim|required',
            ),array(
                'field' =>  'account_id',
                'label' =>  'Account',
                'rules' =>  'trim|required',
            ),
        );
        $this->form_validation->set_rules($validation_rules);
        if($this->form_validation->run()){
            $directory = './uploads/files/csvs';
            if(!is_dir($directory)){
                mkdir($directory,0777,TRUE);
            }
            $config['upload_path'] = FCPATH . 'uploads/files/csvs/';
            $config['allowed_types'] = 'xls|xlsx|csv';
            $config['max_size'] = '1024';
            $repayment_date = strtotime($this->input->post('repayment_date'));
            $account_id = $this->input->post('account_id');
            $send_sms_notification = $this->input->post('send_sms_notification');
            $send_email_notification = $this->input->post('send_email_notification');
            $deposit_method = 1;
            $description = $this->input->post('description');
            $this->load->library('upload',$config);
            if($this->upload->do_upload('loan_repayment_template')){
                $unsuccessful_invitations_count = 0;
                $upload_data = $this->upload->data();
                $file_path = $upload_data['full_path'];
                $this->load->library('Excel');
                $excel_sheet = new PHPExcel();
                if(file_exists($file_path)){
                    $file_type = PHPExcel_IOFactory::identify($file_path);
                    $excel_reader = PHPExcel_IOFactory::createReader($file_type);
                    $excel_book = $excel_reader->load($file_path);
                    $sheet = $excel_book->getSheet(0);
                    $allowed_column_headers = array('','Loan ID','Member Name','Loan Description',' Amount');
                    $count = count($allowed_column_headers)-1;
                    for($column = 0; $column <= $count; $column++){
                        $value = $sheet->getCellByColumnAndRow($column, 2)->getValue();
                        $value = str_replace('Repayment Amount ('.$this->group_currency.')','',$value);
                        if(in_array(trim($value), $allowed_column_headers)){
                            $column_validation = true;
                        }else{
                            $column_validation = false;
                            break;
                        }
                    }

                    if($column_validation){
                        $highestRow = $sheet->getHighestRow();
                        $member_payments = array();
                        $contribution_payment_amounts = array();
                        for($row = 3; $row <= $highestRow; $row++){
                            $member_name = '';
                            $member_id = 0;
                            for($column = 0; $column <= $count; $column++){
                                if($column == 1){
                                    $loan_id = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }
                                if($column == 2){
                                    $member_name = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                    $member_id = array_search($member_name,$this->active_group_member_options);
                                }
                                if($column == 4){
                                    $repayment_amount = valid_currency($sheet->getCellByColumnAndRow($column,$row)->getValue());
                                }
                            }
                            
                            $loan_repayments[] = array(
                                'loan_id' => $loan_id,
                                'member_id' => $member_id,
                                'repayment_amount' => $repayment_amount,
                            );
                        }

                        if(empty($loan_repayments)){
                            $this->session->set_flashdata('info','Error in the import list file');
                        }else{
                            $successes = 0;
                            $fails = 0;
                            $duplicates = 0;
                            $ignores = 0;
                            $errors = 0;
                            $phones = array();
                            $emails = array();
                            $row = 2;
                            foreach($loan_repayments as $loan_repayment){
                                $loan_repayment = (object)$loan_repayment;
                                $member_id = $loan_repayment->member_id;
                                $loan_id = $loan_repayment->loan_id;
                                $repayment_amount = $loan_repayment->repayment_amount;

                                if($member_id&&$loan_id&&valid_currency($repayment_amount)){
                                    if($member = $this->members_m->get_group_member($member_id)){
                                        if($loan = $this->loans_m->get_group_loan($loan_id)){
                                            if($this->loan->record_loan_repayment(
                                                $this->group->id,
                                                $repayment_date,
                                                $member,
                                                $loan_id,
                                                $account_id,
                                                2,
                                                '',
                                                $repayment_amount,
                                                $send_sms_notification,
                                                $send_email_notification,
                                                $this->user,
                                                $member->user_id,
                                                0,
                                                FALSE)
                                            ){
                                                ++$successes;
                                            }else{
                                                ++$errors;
                                            }
                                        }else{
                                            ++$errors;
                                        }
                                    }else{
                                        ++$errors;
                                    }
                                }else{
                                    ++$errors;
                                }
                            }
                            if($errors){
                                if($errors==1){
                                    $this->session->set_flashdata('error',$errors.' error encountered while importing, some details were missing.');
                                }else{
                                    $this->session->set_flashdata('error',$errors.' errors encountered while importing, some details were missing.');
                                }
                                foreach($validation_rules as $key => $field){
                                    $post->$field['field'] = set_value($field['field']);
                                }
                            }
                            if($successes){
                                if($successes==1){
                                    $this->session->set_flashdata('success',$successes.' payment was recorded.');
                                }else{
                                    $this->session->set_flashdata('success',$successes.' were recorded.');
                                }
                            }
                            if($fails){
                                if($fails==1){
                                   $this->session->set_flashdata('error',$fails.' fails encountered while importing, some details were missing.');
                                }else{
                                    $this->session->set_flashdata('error',$fails.' fails encountered while importing, some details were missing.');
                                }
                                foreach($validation_rules as $key => $field){
                                    $post->$field['field'] = set_value($field['field']);
                                }
                            }
                        }
                    }else{
                        $this->session->set_flashdata('error','Contibution Payment list file does not have the correct format');
                    }
                }else{
                    $this->session->set_flashdata('error','Contibution Payment list file was not found');
                }
            }else{
                $this->session->set_flashdata('error','Contibution Payment list file type is not allowed');
            }
            redirect('group/deposits/listing');
        }else{
            foreach($validation_rules as $key => $field){
                $field_name = $field['field'];
                $post->$field_name = set_value($field['field']);
            }
        }
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',TRUE);
        $data['post'] = $post;
        $this->template->title('Upload Loan Repayments')->build('group/upload_loan_repayments',$data);
    }

    function record_fine_payments(){
        $data = array();
        $posts = $_POST;
        $errors = array();
        $error_messages = array();
        $successes = array();
        if($this->input->post('submit')){
            $entries_are_valid = TRUE;
            if(!empty($posts)){ 
                if(isset($posts['deposit_dates'])){
                    $count = count($posts['deposit_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['deposit_dates'][$i])&&isset($posts['members'][$i])&&isset($posts['fine_categories'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])&&isset($posts['deposit_methods'][$i])):    
                            //Deposit dates
                            if($posts['deposit_dates'][$i]==''){
                                $successes['deposit_dates'][$i] = 0;
                                $errors['deposit_dates'][$i] = 1;
                                $error_messages['deposit_dates'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['deposit_dates'][$i] = 1;
                                $errors['deposit_dates'][$i] = 0;
                            }
                            //Members
                            if($posts['members'][$i]==''){
                                $successes['members'][$i] = 0;
                                $errors['members'][$i] = 1;
                                $error_messages['members'][$i] = 'Please select a member';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['members'][$i])){
                                    $successes['members'][$i] = 1;
                                    $errors['members'][$i] = 0;
                                }else{
                                    $successes['members'][$i] = 0;
                                    $errors['members'][$i] = 1;
                                    $error_messages['members'][$i] = 'Please enter a valid member value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Fine categories
                            if($posts['fine_categories'][$i]==''){
                                $successes['fine_categories'][$i] = 0;
                                $errors['fine_categories'][$i] = 1;
                                $error_messages['fine_categories'][$i] = 'Please select a fine category';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['fine_categories'][$i] = 1;
                                $errors['fine_categories'][$i] = 0;
                            }
                             //Accounts
                            if($posts['accounts'][$i]==''){
                                $successes['accounts'][$i] = 0;
                                $errors['accounts'][$i] = 1;
                                $error_messages['accounts'][$i] = 'Please select an account';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['accounts'][$i] = 1;
                                $errors['accounts'][$i] = 0;
                            }
                            //Deposit Method
                            if($posts['deposit_methods'][$i]==''){
                                $successes['deposit_methods'][$i] = 0;
                                $errors['deposit_methods'][$i] = 1;
                                $error_messages['deposit_methods'][$i] = 'Please select a deposit method';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['deposit_methods'][$i])){
                                    $successes['deposit_methods'][$i] = 1;
                                    $errors['deposit_methods'][$i] = 0;
                                }else{
                                    $successes['deposit_methods'][$i] = 0;
                                    $errors['deposit_methods'][$i] = 1;
                                    $error_messages['deposit_methods'][$i] = 'Please enter a valid deposit method';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //amounts
                            if($posts['amounts'][$i]=='' || $posts['amounts'][$i] < 1){
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
                $this->session->set_flashdata('error','');
                $successful_fine_payment_entry_count = 0;
                $unsuccessful_fine_payment_entry_count = 0;
                if(isset($posts['deposit_dates'])){
                    $count = count($posts['deposit_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['deposit_dates'][$i])&&isset($posts['members'][$i])&&isset($posts['fine_categories'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])&&isset($posts['deposit_methods'][$i])):    
                            $amount = valid_currency($posts['amounts'][$i]);
                            $deposit_date = strtotime($posts['deposit_dates'][$i]); 
                            $send_sms_notification = isset($posts['send_sms_notification'][$i])?$posts['send_sms_notification'][$i]:0;
                            $send_email_notification = isset($posts['send_email_notification'][$i])?$posts['send_email_notification'][$i]:0;
                            $description = isset($posts['deposit_descriptions'][$i])?$posts['deposit_descriptions'][$i]:'';
                            if($this->transactions->record_fine_payment($this->group->id,$deposit_date,$posts['members'][$i],$posts['fine_categories'][$i],$posts['accounts'][$i],$posts['deposit_methods'][$i],$description,$amount,$send_sms_notification,$send_email_notification)){
                                $successful_fine_payment_entry_count++;
                            }else{
                                $unsuccessful_fine_payment_entry_count++;
                            }
                        endif;
                    endfor;
                }
                if($successful_fine_payment_entry_count){
                    if($successful_fine_payment_entry_count==1){
                        $this->session->set_flashdata('success',$successful_fine_payment_entry_count.' fine payment successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('success',$successful_fine_payment_entry_count.' fine payments successfully recorded. ');
                    }
                }
                if($unsuccessful_fine_payment_entry_count){
                    if($unsuccessful_fine_payment_entry_count==1){
                        $this->session->set_flashdata('error',$unsuccessful_fine_payment_entry_count.' fine payment was not recorded. ');
                    }else{
                        $this->session->set_flashdata('error',$unsuccessful_fine_payment_entry_count.' fine payments were not recorded. ');
                    }
                }
                redirect('group/deposits/listing');
            }else{
                $this->session->set_flashdata('error','There are some errors on the form. Please review and try again.');
            }
        }
        $data['group_role_options'] = $this->group_roles_m->get_group_role_options();
        $data['errors'] = $errors;
        $data['error_messages'] = $error_messages;
        $data['successes'] = $successes;
        $data['deposit_type_options'] = $this->deposit_type_options;
        $data['deposit_method_options'] = $this->transactions->deposit_method_options;
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        $data['fine_category_options'] = $this->fine_categories_m->get_group_fine_category_options();
        $data['banks'] = $this->banks_m->get_group_bank_options();
        $data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $data['posts'] = $posts;
        $this->template->title(translate('Record Fine Payments'))->build('group/record_fine_payments',$data);
    }

    function record_loan_repayments(){
        $data = array();
        $posts = $_POST;
        $errors = array();
        $error_messages = array();
        $loans_options = array();
        $successes = array();
        if(isset($_GET) && !$this->input->post('submit') && $this->input->get('member_id')){
            $loans_options = $this->loans_m->get_active_member_loans_option($this->input->get('member_id'));
        }
        if($this->input->post('submit')){
            $entries_are_valid = TRUE;
            if(!empty($posts)){ 
                if(isset($posts['deposit_dates'])){
                    $count = count($posts['deposit_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['deposit_dates'][$i])&&isset($posts['members'][$i])&&isset($posts['loans'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])&&isset($posts['deposit_methods'][$i])):    
                            //Deposit dates
                            if($posts['deposit_dates'][$i]==''){
                                $successes['deposit_dates'][$i] = 0;
                                $errors['deposit_dates'][$i] = 1;
                                $error_messages['deposit_dates'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['deposit_dates'][$i] = 1;
                                $errors['deposit_dates'][$i] = 0;
                            }
                            //Members
                            if($posts['members'][$i]==''){
                                $loans_options[] = array(''=>'--Select member first--');
                                $successes['members'][$i] = 0;
                                $errors['members'][$i] = 1;
                                $error_messages['members'][$i] = '--Please select a member--';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['members'][$i])){
                                    $successes['members'][$i] = 1;
                                    $errors['members'][$i] = 0;
                                    if($loans_option = $this->loans_m->get_active_member_loans_option($posts['members'][$i])){
                                        $loans_options[] = array(''=>'--Select a loan--')+$loans_option;
                                    }else{
                                        $loans_options[] = array(''=>'Member has no active loans');
                                    }
                                    
                                }else{
                                    $loans_options[] = array(''=>'--Select member first--');
                                    $successes['members'][$i] = 0;
                                    $errors['members'][$i] = 1;
                                    $error_messages['members'][$i] = 'Please enter a valid member value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Loans
                            if($posts['loans'][$i]==''){
                                $successes['loans'][$i] = 0;
                                $errors['loans'][$i] = 1;
                                $error_messages['loans'][$i] = 'Please select loan ';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['loans'][$i])){
                                    $successes['loans'][$i] = 1;
                                    $errors['loans'][$i] = 0;
                                }else{
                                    $successes['loans'][$i] = 0;
                                    $errors['loans'][$i] = 1;
                                    $error_messages['loans'][$i] = 'Please select a valid loan value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                             //Accounts
                            if($posts['accounts'][$i]==''){
                                $successes['accounts'][$i] = 0;
                                $errors['accounts'][$i] = 1;
                                $error_messages['accounts'][$i] = 'Please select an account';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['accounts'][$i] = 1;
                                $errors['accounts'][$i] = 0;
                            }
                            //Deposit Method
                            if($posts['deposit_methods'][$i]==''){
                                $successes['deposit_methods'][$i] = 0;
                                $errors['deposit_methods'][$i] = 1;
                                $error_messages['deposit_methods'][$i] = 'Please select a deposit method';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['deposit_methods'][$i])){
                                    $successes['deposit_methods'][$i] = 1;
                                    $errors['deposit_methods'][$i] = 0;
                                }else{
                                    $successes['deposit_methods'][$i] = 0;
                                    $errors['deposit_methods'][$i] = 1;
                                    $error_messages['deposit_methods'][$i] = 'Please enter a valid deposit method';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //amounts
                            if($posts['amounts'][$i]=='' || $posts['amounts'][$i] < 1 || $posts['amounts'][$i] < 1){
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
            }else{
                $entries_are_valid = FALSE;
            }
            if($entries_are_valid){
                $this->session->set_flashdata('error','');
                $successful_contribution_payment_entry_count = 0;
                $unsuccessful_contribution_payment_entry_count = 0;
                if(isset($posts['deposit_dates'])){
                    $count = count($posts['deposit_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['deposit_dates'][$i])&&isset($posts['members'][$i])&&isset($posts['loans'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])&&isset($posts['deposit_methods'][$i])):    
                            $amount = valid_currency($posts['amounts'][$i]);
                            $deposit_date = strtotime($posts['deposit_dates'][$i]); 
                            $send_sms_notification = isset($posts['send_sms_notification'][$i])?$posts['send_sms_notification'][$i]:0;
                            $send_email_notification = isset($posts['send_email_notification'][$i])?$posts['send_email_notification'][$i]:0;
                            $member = $this->members_m->get_group_member($posts['members'][$i],$this->group->id);
                            $created_by = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id);
                            $description = isset($posts['deposit_descriptions'][$i])?$posts['deposit_descriptions'][$i]:'';
                            if($this->loan->record_loan_repayment($this->group->id,$deposit_date,$member,$posts['loans'][$i],$posts['accounts'][$i],$posts['deposit_methods'][$i],$description,$amount,$send_sms_notification,$send_email_notification,$created_by)){
                                $successful_contribution_payment_entry_count++;
                            }else{
                                $unsuccessful_contribution_payment_entry_count++;
                            }
                        endif;
                    endfor;
                }
                if($successful_contribution_payment_entry_count){
                    if($successful_contribution_payment_entry_count==1){
                        $this->session->set_flashdata('success',$successful_contribution_payment_entry_count.' loan repayment successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('success',$successful_contribution_payment_entry_count.' loan repayments successfully recorded. ');
                    }
                }
                if($unsuccessful_contribution_payment_entry_count){
                    if($unsuccessful_contribution_payment_entry_count==1){
                        $this->session->set_flashdata('error',$unsuccessful_contribution_payment_entry_count.' loan repayment was not successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('error',$unsuccessful_contribution_payment_entry_count.' loan repayments were not successfully recorded. ');
                    }
                }
                redirect('group/deposits/listing');
            }else{
                $this->session->set_flashdata('error','There are some errors on the form. Please review and try again.');
            }
        }
        $data['group_role_options'] = $this->group_roles_m->get_group_role_options();
        $data['loans_options'] = $loans_options;
        $data['interest_types'] = $this->loan->interest_types;
        $data['loan_interest_rate_per'] = $this->loan->loan_interest_rate_per;
        $data['loan_grace_periods'] = $this->loan->loan_grace_periods;
        $data['loan_days'] = $this->loan->loan_days;
        $data['sms_template_default'] = $this->loan->sms_template_default;
        $data['late_loan_payment_fine_types'] = $this->loan->late_loan_payment_fine_types;
        $data['late_payments_fine_frequency'] = $this->loan->late_payments_fine_frequency;
        $data['fixed_amount_fine_frequency_on'] = $this->loan->fixed_amount_fine_frequency_on;
        $data['percentage_fine_on'] = $this->loan->percentage_fine_on;
        $data['one_off_fine_types'] = $this->loan->one_off_fine_types;
        $data['one_off_percentage_rate_on'] = $this->loan->one_off_percentage_rate_on;
        $data['loan_processing_fee_types'] = $this->loan->loan_processing_fee_types;
        $data['loan_processing_fee_percentage_charged_on'] = $this->loan->loan_processing_fee_percentage_charged_on;
        preg_match_all("/\[[^\]]*\]/", $data['sms_template_default'],$placeholders);
        $data['placeholders'] = isset($placeholders[0])?$placeholders[0]:array();
        $data['errors'] = $errors;
        $data['error_messages'] = $error_messages;
        $data['successes'] = $successes;
        $data['deposit_type_options'] = $this->deposit_type_options;
        $data['deposit_method_options'] = $this->transactions->deposit_method_options;
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        $data['contribution_options'] = $this->contributions_m->get_active_group_contribution_options();
        $data['banks'] = $this->banks_m->get_group_bank_options();
        $data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $data['active_group_member_options'] = $this->members_m->get_group_member_options();
        $data['posts'] = $posts;
        $this->template->title(translate('Record Loan Repayments'))->build('group/record_loan_repayments',$data);
    }

    function record_debtor_loan_repayments(){
        $data = array();
        $posts = $_POST;
        $errors = array();
        $error_messages = array();
        $loans_options = array();
        $successes = array();
        if(isset($_GET) && !$this->input->post('submit') && $this->input->get('debtor_id')){
            $loans_options = $this->debtors_m->get_active_debtor_loans_option($this->input->get('debtor_id'));
        }
        if($this->input->post('submit')){
            $entries_are_valid = TRUE;
            if(!empty($posts)){ 
                if(isset($posts['deposit_dates'])){
                    $count = count($posts['deposit_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['deposit_dates'][$i])&&isset($posts['debtors'][$i])&&isset($posts['loans'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])&&isset($posts['deposit_methods'][$i])):    
                            //Deposit dates
                            if($posts['deposit_dates'][$i]==''){
                                $successes['deposit_dates'][$i] = 0;
                                $errors['deposit_dates'][$i] = 1;
                                $error_messages['deposit_dates'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['deposit_dates'][$i] = 1;
                                $errors['deposit_dates'][$i] = 0;
                            }
                            //Debtors
                            if($posts['debtors'][$i]==''){
                                $loans_options[] = array(''=>'--Select debtor first--');
                                $successes['debtors'][$i] = 0;
                                $errors['debtors'][$i] = 1;
                                $error_messages['debtors'][$i] = '--Please select a debtor--';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['debtors'][$i])){
                                    $successes['debtors'][$i] = 1;
                                    $errors['debtors'][$i] = 0;
                                    if($loans_option = $this->debtors_m->get_active_debtor_loans_option($posts['debtors'][$i])){
                                        $loans_options[] = array(''=>'--Select a loan--')+$loans_option;
                                    }else{
                                        $loans_options[] = array(''=>'Debtor has no active loans');
                                    }
                                    
                                }else{
                                    $loans_options[] = array(''=>'--Select debtor first--');
                                    $successes['debtors'][$i] = 0;
                                    $errors['debtors'][$i] = 1;
                                    $error_messages['debtors'][$i] = 'Please enter a valid debtor value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Loans
                            if($posts['loans'][$i]==''){
                                $successes['loans'][$i] = 0;
                                $errors['loans'][$i] = 1;
                                $error_messages['loans'][$i] = 'Please select loan ';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['loans'][$i])){
                                    $successes['loans'][$i] = 1;
                                    $errors['loans'][$i] = 0;
                                }else{
                                    $successes['loans'][$i] = 0;
                                    $errors['loans'][$i] = 1;
                                    $error_messages['loans'][$i] = 'Please select a valid loan value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                             //Accounts
                            if($posts['accounts'][$i]==''){
                                $successes['accounts'][$i] = 0;
                                $errors['accounts'][$i] = 1;
                                $error_messages['accounts'][$i] = 'Please select an account';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['accounts'][$i] = 1;
                                $errors['accounts'][$i] = 0;
                            }
                            //Deposit Method
                            if($posts['deposit_methods'][$i]==''){
                                $successes['deposit_methods'][$i] = 0;
                                $errors['deposit_methods'][$i] = 1;
                                $error_messages['deposit_methods'][$i] = 'Please select a deposit method';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['deposit_methods'][$i])){
                                    $successes['deposit_methods'][$i] = 1;
                                    $errors['deposit_methods'][$i] = 0;
                                }else{
                                    $successes['deposit_methods'][$i] = 0;
                                    $errors['deposit_methods'][$i] = 1;
                                    $error_messages['deposit_methods'][$i] = 'Please enter a valid deposit method';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //amounts
                            if($posts['amounts'][$i]=='' || $posts['amounts'][$i] < 1){
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
                $this->session->set_flashdata('error','');
                $successful_contribution_payment_entry_count = 0;
                $unsuccessful_contribution_payment_entry_count = 0;
                if(isset($posts['deposit_dates'])){
                    $count = count($posts['deposit_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['deposit_dates'][$i])&&isset($posts['debtors'][$i])&&isset($posts['loans'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])&&isset($posts['deposit_methods'][$i])):    
                            $amount = valid_currency($posts['amounts'][$i]);
                            $deposit_date = strtotime($posts['deposit_dates'][$i]); 
                            $send_sms_notification = isset($posts['send_sms_notification'][$i])?$posts['send_sms_notification'][$i]:0;
                            $send_email_notification = isset($posts['send_email_notification'][$i])?$posts['send_email_notification'][$i]:0;
                            $debtor = $this->debtors_m->get($posts['debtors'][$i],$this->group->id);


                            $created_by = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id);

                            $description = isset($posts['deposit_descriptions'][$i])?$posts['deposit_descriptions'][$i]:'';

                            if($this->loan->record_debtor_loan_repayment(
                                    $this->group->id,
                                    $deposit_date,
                                    $debtor,
                                    $posts['loans'][$i],
                                    $posts['accounts'][$i],
                                    $posts['deposit_methods'][$i],
                                    $description,
                                    $amount,
                                    $send_sms_notification,
                                    $send_email_notification,
                                    $created_by
                                )){
                                $successful_contribution_payment_entry_count++;
                            }else{
                                $unsuccessful_contribution_payment_entry_count++;
                            }
                        endif;
                    endfor;
                }
                if($successful_contribution_payment_entry_count){
                    if($successful_contribution_payment_entry_count==1){
                        $this->session->set_flashdata('success',$successful_contribution_payment_entry_count.' external loan repayment successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('success',$successful_contribution_payment_entry_count.' external loan repayments successfully recorded. ');
                    }
                }
                if($unsuccessful_contribution_payment_entry_count){
                    if($unsuccessful_contribution_payment_entry_count==1){
                        $this->session->set_flashdata('error',$unsuccessful_contribution_payment_entry_count.' external loan repayment was not successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('error',$unsuccessful_contribution_payment_entry_count.' external loan repayments were not successfully recorded. ');
                    }
                }
                redirect('group/deposits/listing');
            }else{
                $this->session->set_flashdata('error','There are some errors on the form. Please review and try again.');
            }
        }
        $data['loans_options'] = $loans_options;
        $data['interest_types'] = $this->loan->interest_types;
        $data['loan_interest_rate_per'] = $this->loan->loan_interest_rate_per;
        $data['loan_grace_periods'] = $this->loan->loan_grace_periods;
        $data['loan_days'] = $this->loan->loan_days;
        $data['sms_template_default'] = $this->loan->sms_template_default;
        $data['late_loan_payment_fine_types'] = $this->loan->late_loan_payment_fine_types;
        $data['late_payments_fine_frequency'] = $this->loan->late_payments_fine_frequency;
        $data['fixed_amount_fine_frequency_on'] = $this->loan->fixed_amount_fine_frequency_on;
        $data['percentage_fine_on'] = $this->loan->percentage_fine_on;
        $data['one_off_fine_types'] = $this->loan->one_off_fine_types;
        $data['one_off_percentage_rate_on'] = $this->loan->one_off_percentage_rate_on;
        $data['loan_processing_fee_types'] = $this->loan->loan_processing_fee_types;
        $data['loan_processing_fee_percentage_charged_on'] = $this->loan->loan_processing_fee_percentage_charged_on;
        preg_match_all("/\[[^\]]*\]/", $data['sms_template_default'],$placeholders);
        $data['placeholders'] = isset($placeholders[0])?$placeholders[0]:array();
        $data['errors'] = $errors;
        $data['error_messages'] = $error_messages;
        $data['successes'] = $successes;
        $data['deposit_type_options'] = $this->deposit_type_options;
        $data['deposit_method_options'] = $this->transactions->deposit_method_options;
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        $data['contribution_options'] = $this->contributions_m->get_active_group_contribution_options();
        $data['banks'] = $this->banks_m->get_group_bank_options();
        $data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $data['posts'] = $posts;
        $this->template->title('Record Debtor Loan Repayments')->build('group/record_debtor_loan_repayments',$data);
    }

    function record_miscellaneous_payments(){
        $data = array();
        $posts = $_POST;
        $errors = array();
        $error_messages = array();
        $successes = array();
        if($this->input->post('submit')){
            $entries_are_valid = TRUE;
            if(!empty($posts)){ 
                if(isset($posts['deposit_dates'])){
                    $count = count($posts['deposit_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['deposit_dates'][$i])&&isset($posts['members'][$i])&&isset($posts['miscellaneous_deposit_descriptions'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])&&isset($posts['deposit_methods'][$i])):    
                            //Deposit dates
                            if($posts['deposit_dates'][$i]==''){
                                $successes['deposit_dates'][$i] = 0;
                                $errors['deposit_dates'][$i] = 1;
                                $error_messages['deposit_dates'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['deposit_dates'][$i] = 1;
                                $errors['deposit_dates'][$i] = 0;
                            }
                            //Members
                            if($posts['members'][$i]==''){
                                $successes['members'][$i] = 0;
                                $errors['members'][$i] = 1;
                                $error_messages['members'][$i] = 'Please select a member';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['members'][$i])){
                                    $successes['members'][$i] = 1;
                                    $errors['members'][$i] = 0;
                                }else{
                                    $successes['members'][$i] = 0;
                                    $errors['members'][$i] = 1;
                                    $error_messages['members'][$i] = 'Please enter a valid member value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Fine categories
                            if($posts['miscellaneous_deposit_descriptions'][$i]==''){
                                $successes['miscellaneous_deposit_descriptions'][$i] = 0;
                                $errors['miscellaneous_deposit_descriptions'][$i] = 1;
                                $error_messages['miscellaneous_deposit_descriptions'][$i] = 'Please enter a description';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['miscellaneous_deposit_descriptions'][$i] = 1;
                                $errors['miscellaneous_deposit_descriptions'][$i] = 0;
                            }
                             //Accounts
                            if($posts['accounts'][$i]==''){
                                $successes['accounts'][$i] = 0;
                                $errors['accounts'][$i] = 1;
                                $error_messages['accounts'][$i] = 'Please select an account';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['accounts'][$i] = 1;
                                $errors['accounts'][$i] = 0;
                            }
                            //Deposit Method
                            if($posts['deposit_methods'][$i]==''){
                                $successes['deposit_methods'][$i] = 0;
                                $errors['deposit_methods'][$i] = 1;
                                $error_messages['deposit_methods'][$i] = 'Please select a deposit method';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['deposit_methods'][$i])){
                                    $successes['deposit_methods'][$i] = 1;
                                    $errors['deposit_methods'][$i] = 0;
                                }else{
                                    $successes['deposit_methods'][$i] = 0;
                                    $errors['deposit_methods'][$i] = 1;
                                    $error_messages['deposit_methods'][$i] = 'Please enter a valid deposit method';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //amounts
                            if($posts['amounts'][$i]=='' || $posts['amounts'][$i] < 1){
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
                $this->session->set_flashdata('error','');
                $successful_miscellaneous_payment_entry_count = 0;
                $unsuccessful_miscellaneous_payment_entry_count = 0;
                if(isset($posts['deposit_dates'])){
                    $count = count($posts['deposit_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['deposit_dates'][$i])&&isset($posts['members'][$i])&&isset($posts['miscellaneous_deposit_descriptions'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])&&isset($posts['deposit_methods'][$i])):    
                            $amount = valid_currency($posts['amounts'][$i]);
                            $deposit_date = strtotime($posts['deposit_dates'][$i]); 
                            $send_sms_notification = isset($posts['send_sms_notification'][$i])?$posts['send_sms_notification'][$i]:0;
                            $send_email_notification = isset($posts['send_email_notification'][$i])?$posts['send_email_notification'][$i]:0;
                            $description = isset($posts['miscellaneous_deposit_descriptions'][$i])?$posts['miscellaneous_deposit_descriptions'][$i]:'';
                            if($this->transactions->record_miscellaneous_payment($this->group->id,$deposit_date,$posts['members'][$i],$posts['accounts'][$i],$posts['deposit_methods'][$i],$description,$amount,$send_sms_notification,$send_email_notification)){
                                $successful_miscellaneous_payment_entry_count++;
                            }else{
                                $unsuccessful_miscellaneous_payment_entry_count++;
                            }
                        endif;
                    endfor;
                }
                if($successful_miscellaneous_payment_entry_count){
                    if($successful_miscellaneous_payment_entry_count==1){
                        $this->session->set_flashdata('success',$successful_miscellaneous_payment_entry_count.' miscellaneous payment successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('success',$successful_miscellaneous_payment_entry_count.' miscellaneous payments successfully recorded. ');
                    }
                }
                if($unsuccessful_miscellaneous_payment_entry_count){
                    if($unsuccessful_miscellaneous_payment_entry_count==1){
                        $this->session->set_flashdata('error',$unsuccessful_miscellaneous_payment_entry_count.' miscellaneous payment was not recorded. ');
                    }else{
                        $this->session->set_flashdata('error',$unsuccessful_miscellaneous_payment_entry_count.' miscellaneous payments were not recorded. ');
                    }
                }
                redirect('group/deposits/listing');
            }else{
                $this->session->set_flashdata('error','There are some errors on the form. Please review and try again.');
            }
        }
        $data['group_role_options'] = $this->group_roles_m->get_group_role_options();
        $data['errors'] = $errors;
        $data['error_messages'] = $error_messages;
        $data['successes'] = $successes;
        $data['deposit_type_options'] = $this->deposit_type_options;
        $data['deposit_method_options'] = $this->transactions->deposit_method_options;
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        $data['banks'] = $this->banks_m->get_group_bank_options();
        $data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $data['posts'] = $posts;
        $this->template->title(translate('Record Miscellaneous Payments'))->build('group/record_miscellaneous_payments',$data);
    }

    function record_income(){
        $data = array();
        $posts = $_POST;
        $errors = array();
        $error_messages = array();
        $successes = array();
        if($this->input->post('submit')){
            $entries_are_valid = TRUE;
            if(!empty($posts)){ 
                if(isset($posts['deposit_dates'])){
                    $count = count($posts['deposit_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['deposit_dates'][$i])&&isset($posts['depositors'][$i])&&isset($posts['income_categories'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])&&isset($posts['deposit_methods'][$i])):    
                            //Deposit dates
                            if($posts['deposit_dates'][$i]==''){
                                $successes['deposit_dates'][$i] = 0;
                                $errors['deposit_dates'][$i] = 1;
                                $error_messages['deposit_dates'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['deposit_dates'][$i] = 1;
                                $errors['deposit_dates'][$i] = 0;
                            }
                            //Members
                            if($posts['depositors'][$i]==''){
                                $successes['depositors'][$i] = 0;
                                $errors['depositors'][$i] = 1;
                                $error_messages['depositors'][$i] = 'Please select a depositor';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['depositors'][$i])){
                                    $successes['depositors'][$i] = 1;
                                    $errors['depositors'][$i] = 0;
                                }else{
                                    $successes['depositors'][$i] = 0;
                                    $errors['depositors'][$i] = 1;
                                    $error_messages['depositors'][$i] = 'Please enter a valid depositor value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Fine categories
                            if($posts['income_categories'][$i]==''){
                                $successes['income_categories'][$i] = 0;
                                $errors['income_categories'][$i] = 1;
                                $error_messages['income_categories'][$i] = 'Please enter an income category';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['income_categories'][$i] = 1;
                                $errors['income_categories'][$i] = 0;
                            }
                             //Accounts
                            if($posts['accounts'][$i]==''){
                                $successes['accounts'][$i] = 0;
                                $errors['accounts'][$i] = 1;
                                $error_messages['accounts'][$i] = 'Please select an account';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['accounts'][$i] = 1;
                                $errors['accounts'][$i] = 0;
                            }
                            //Deposit Method
                            if($posts['deposit_methods'][$i]==''){
                                $successes['deposit_methods'][$i] = 0;
                                $errors['deposit_methods'][$i] = 1;
                                $error_messages['deposit_methods'][$i] = 'Please select a deposit method';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['deposit_methods'][$i])){
                                    $successes['deposit_methods'][$i] = 1;
                                    $errors['deposit_methods'][$i] = 0;
                                }else{
                                    $successes['deposit_methods'][$i] = 0;
                                    $errors['deposit_methods'][$i] = 1;
                                    $error_messages['deposit_methods'][$i] = 'Please enter a valid deposit method';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //amounts
                            if($posts['amounts'][$i]=='' || $posts['amounts'][$i] < 1){
                                $successes['amounts'][$i] = 0;
                                $errors['amounts'][$i] = 1;
                                $error_messages['amounts'][$i] = 'Please enter an amount';
                                $entries_are_valid = FALSE;
                            }else{
                                if(valid_currency($posts['amounts'][$i])){
                                    $successes['amounts'][$i] = 1;
                                    $errors['amounts'][$i] = 0;
                                }else{
                                    $successes['amounts'][$i] = 0;
                                    $errors['amounts'][$i] = 1;
                                    $error_messages['amounts'][$i] = 'Please enter a valid amount';
                                    $entries_are_valid = FALSE; 
                                }
                            }
                        endif;
                    endfor;
                }
            }
            if($entries_are_valid){
                $this->session->set_flashdata('error','');
                $successful_income_entry_count = 0;
                $unsuccessful_income_entry_count = 0;
                if(isset($posts['deposit_dates'])){
                    $count = count($posts['deposit_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['deposit_dates'][$i])&&isset($posts['depositors'][$i])&&isset($posts['income_categories'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])&&isset($posts['deposit_methods'][$i])):    
                            $amount = valid_currency($posts['amounts'][$i]);
                            $deposit_date = strtotime($posts['deposit_dates'][$i]); 
                            $description = isset($posts['deposit_descriptions'][$i])?$posts['deposit_descriptions'][$i]:'';
                            if($this->transactions->record_income_deposit($this->group->id,$deposit_date,$posts['depositors'][$i],$posts['income_categories'][$i],$posts['accounts'][$i],$posts['deposit_methods'][$i],$description,$amount)){
                                $successful_income_entry_count++;
                            }else{
                                $unsuccessful_income_entry_count++;
                            }
                        endif;
                    endfor;
                }
                if($successful_income_entry_count){
                    if($successful_income_entry_count==1){
                        $this->session->set_flashdata('success',$successful_income_entry_count.' income successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('success',$successful_miscellaneous_payment_entry_count.' incomes successfully recorded. ');
                    }
                }
                if($unsuccessful_income_entry_count){
                    if($unsuccessful_income_entry_count==1){
                        $this->session->set_flashdata('error',$unsuccessful_income_entry_count.' income was not recorded. ');
                    }else{
                        $this->session->set_flashdata('error',$unsuccessful_income_entry_count.' income were not recorded. ');
                    }
                }
                redirect('group/deposits/listing');
            }else{
                $this->session->set_flashdata('error','There are some errors on the form. Please review and try again.');
            }
        }
        $data['group_role_options'] = $this->group_roles_m->get_group_role_options();
        $data['errors'] = $errors;
        $data['error_messages'] = $error_messages;
        $data['successes'] = $successes;
        $data['deposit_type_options'] = $this->deposit_type_options;
        $data['deposit_method_options'] = $this->transactions->deposit_method_options;
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        $data['income_category_options'] = $this->income_categories_m->get_group_income_category_options();
        $data['depositor_options'] = $this->depositors_m->get_group_depositor_options();
        $data['banks'] = $this->banks_m->get_group_bank_options();
        $data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $data['posts'] = $posts;
        $this->template->title(translate('Record Income'))->build('group/record_income',$data);
    }

    function record_contribution_transfers(){
        $data = array();
        $post = new stdClass();
        $validation_rules=array(
            array(
                'field' =>  'transfer_date',
                'label' =>  'Transfer Date',
                'rules' =>  'trim|required',
            ),array(
                'field' =>  'contribution_from_id',
                'label' =>  'Contribution from',
                'rules' =>  'trim|required|callback__is_contribution_from_id_does_not_match_contribution_to_id',
            ),array(
                'field' =>  'transfer_to',
                'label' =>  'Transfer to',
                'rules' =>  'trim|numeric',
            ),array(
                'field' =>  'contribution_to_id',
                'label' =>  'Contribution to',
                'rules' =>  'trim|numeric',
            ),array(
                'field' =>  'fine_category_to_id',
                'label' =>  'Fine Category to',
                'rules' =>  'trim',
            ),array(
                'field' =>  'member_id',
                'label' =>  'Member',
                'rules' =>  'trim|required|numeric',
            ),array(
                'field' =>  'amount',
                'label' =>  'Amount',
                'rules' =>  'trim|required|currency',
            ),array(
                'field' =>  'description',
                'label' =>  'Description',
                'rules' =>  'trim',
            ),
            array(
                'field' =>  'loan_from_id',
                'label' =>  'Loan from',
                'rules' =>  'trim',
            ),
            array(
                'field' =>  'loan_to_id',
                'label' =>  'Loan to',
                'rules' =>  'trim',
            ),
            array(
                'field' =>  'member_to_id',
                'label' =>  'Recipient Member',
                'rules' =>  'trim',
            ),
            array(
                'field' =>  'member_transfer_to',
                'label' =>  'Member transfer to',
                'rules' =>  'trim',
            ),
            array(
                'field' =>  'member_contribution_to_id',
                'label' =>  'Contribution to',
                'rules' =>  'trim',
            ),
            array(
                'field' =>  'member_fine_category_to_id',
                'label' =>  'Fine category to',
                'rules' =>  'trim',
            ),
            array(
                'field' =>  'member_loan_to_id',
                'label' =>  'Member loan to',
                'rules' =>  'trim',
            ),
        );

        if($this->input->post('contribution_from_id')=='loan'){
            $validation_rules[] = array(
                'field' => 'loan_from_id',
                'label' => 'Loan from',
                'rules' => 'trim|numeric|required|callback__loan_transfer_from_has_payment',
            ); 
        }
        if($this->input->post('transfer_to')==1){
            $validation_rules[] = array(
                'field' => 'contribution_to_id',
                'label' => 'Contribution to',
                'rules' => 'trim|numeric|required',
            ); 
        }else if($this->input->post('transfer_to')==2){
            $validation_rules[] = array(
                'field' => 'fine_category_to_id',
                'label' => 'Fine Category to',
                'rules' => 'trim|required',
            ); 
        }else if($this->input->post('transfer_to')==3){
            $validation_rules[] = array(
                'field' => 'loan_to_id',
                'label' => 'Loan Share to',
                'rules' => 'trim|required|numeric|callback__loan_transfer_to_is_not_same_as_loan_from',
            ); 
        }else if($this->input->post('transfer_to')==4){

            $validation_rules[] = array(
                'field' => 'member_to_id',
                'label' => 'Member to Receive Transfer',
                'rules' => 'trim|required|numeric|callback__member_to_id_is_not_same_as_member_id',
            ); 
            $validation_rules[] = array(
                'field' => 'member_transfer_to',
                'label' => 'Select Transfer To',
                'rules' => 'trim|required|numeric',
            ); 
        }
        
        if($this->input->post('member_transfer_to')==1){
            $validation_rules[] = array(
                'field' => 'member_contribution_to_id',
                'label' => 'Member Contribution To',
                'rules' => 'trim|required|numeric',
            ); 
        }else if($this->input->post('member_transfer_to')==2){
            $validation_rules[] = array(
                'field' => 'member_fine_category_to_id',
                'label' => 'Member Fine Category To',
                'rules' => 'trim|required',
            ); 
        }else if($this->input->post('member_transfer_to')==3){
            $validation_rules[] = array(
                'field' => 'member_loan_to_id',
                'label' => 'Member Loan To',
                'rules' => 'trim|required|numeric',
            ); 
        }

        $this->form_validation->set_rules($validation_rules);

        if($this->form_validation->run()){
            $transfer_date = $this->input->post('transfer_date');
            $contribution_from_id = $this->input->post('contribution_from_id');
            $transfer_to = $this->input->post('transfer_to');
            $contribution_to_id = $this->input->post('contribution_to_id');
            $fine_category_to_id = $this->input->post('fine_category_to_id');
            $member_id = $this->input->post('member_id');
            $amount = currency($this->input->post('amount'));
            $description = $this->input->post('description');
            $loan_from_id = $this->input->post('loan_from_id');
            $loan_to_id = $this->input->post('loan_to_id');
            $member_to_id = $this->input->post('member_to_id');
            $member_transfer_to = $this->input->post('member_transfer_to');
            $member_contribution_to_id = $this->input->post('member_contribution_to_id');
            $member_fine_category_to_id = $this->input->post('member_fine_category_to_id');
            $member_loan_to_id = $this->input->post('member_loan_to_id');
            if($this->transactions->record_contribution_transfer($this->group->id,$transfer_date,$contribution_from_id,$transfer_to,$contribution_to_id,$fine_category_to_id,$member_id,$amount,
                $description,$loan_from_id,
                $loan_to_id,
                $member_to_id,
                $member_transfer_to,
                $member_contribution_to_id,
                $member_fine_category_to_id,
                $member_loan_to_id
                )){

                $group_ids = array(
                    $this->group->id
                );

                $member_to_id = $member_to_id?:0;

                $member_ids = array(
                    $member_id,
                    $member_to_id
                );

                if($this->transactions->update_group_member_contribution_statement_balances($group_ids,$member_ids)){
                    
                }else{
                    $this->session->set_flashdata('warning','Could not reconcile contrbution statement');  
                }
                $this->session->set_flashdata('success','Contribution transfer recorded successfully');
            }else{
                $this->session->set_flashdata('error','Contribution transfer not recorded successfully');
            }
            redirect('group/deposits/contribution_transfers');
        }else{
            foreach($validation_rules as $key => $field){
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $data['placeholders'] = isset($placeholders[0])?$placeholders[0]:array();
        $data['invoice_days'] = $this->contribution_invoices->invoice_days;
        $data['month_days'] = $this->contribution_invoices->month_days;
        $data['week_days'] = $this->contribution_invoices->week_days;
        $data['days_of_the_month'] = $this->contribution_invoices->days_of_the_month;
        $data['every_two_week_days'] = $this->contribution_invoices->every_two_week_days;
        $data['months'] = $this->contribution_invoices->months;
        $data['starting_months'] = $this->contribution_invoices->starting_months;
        $data['week_numbers'] = $this->contribution_invoices->week_numbers;
        $data['contribution_frequency_options'] = $this->contribution_invoices->contribution_frequency_options;
        $data['contribution_type_options'] = $this->contribution_invoices->contribution_type_options;
        $data['sms_template_default'] = $this->sms_template_default;
        $data['fine_types'] = $this->contribution_invoices->fine_types;
        $data['fine_chargeable_on_options'] = $this->contribution_invoices->fine_chargeable_on_options;
        $data['fine_frequency_options'] = $this->contribution_invoices->fine_frequency_options;
        $data['fine_mode_options'] = $this->contribution_invoices->fine_mode_options;
        $data['fine_limit_options'] = $this->contribution_invoices->fine_limit_options;
        $data['percentage_fine_on_options'] = $this->contribution_invoices->percentage_fine_on_options;
        $data['fine_category_options'] = $this->fine_categories_m->get_group_fine_category_options();
        $data['contribution_options'] = $this->contributions_m->get_active_group_contribution_options();
        $data['transfer_to_options'] = $this->transfer_to_options;
        $data['member_transfer_to_options'] = $this->member_transfer_to_options;
        $data['post'] = $post;
        $this->template->title(translate('Record Contribution Transfers'))->build('group/record_contribution_transfers',$data);
    }

    function _is_contribution_from_id_does_not_match_contribution_to_id(){
        if($this->input->post('transfer_to')==1){
            if($this->input->post('contribution_from_id')==$this->input->post('contribution_to_id')){
                $this->form_validation->set_message('_is_contribution_from_id_does_not_match_contribution_to_id', 'The contribution from must be different from contribution to.');
                return FALSE;
            }else{
                return TRUE;
            }
        }else{
            return TRUE;
        }
    }

    function _loan_transfer_from_has_payment(){
        $loan_from_id = $this->input->post('loan_from_id');
        $amount_to_transfer = currency($this->input->post('amount'));
        if($loan_from_id){
            $amount = $this->loan_repayments_m->get_loan_total_payments($loan_from_id);
            $transfers_out = $this->loan_invoices_m->get_total_loan_transfers_out($loan_from_id)?:0;
            $amount = $amount-$transfers_out;
            if($amount){
                if($amount_to_transfer>$amount){
                    $this->form_validation->set_message('_loan_transfer_from_has_payment', "Amount paid is ".number_to_currency($amount)." which is less than ".number_to_currency($amount_to_transfer));
                    return FALSE; 
                }else{
                    return TRUE;
                }
            }else{
                $this->form_validation->set_message('_loan_transfer_from_has_payment', 'Loan from has no payments.');
                return FALSE;
            }
        }else{
            $this->form_validation->set_message('_loan_transfer_from_has_payment', 'Loan from field is required.');
                return FALSE;
        }
    }

    function _loan_transfer_to_is_not_same_as_loan_from(){
        if($this->input->post('contribution_from_id')=='loan'){
            $loan_from_id = $this->input->post('loan_from_id');
            $loan_to_id = $this->input->post('loan_to_id');
            if($loan_from_id==$loan_to_id){
                $this->form_validation->set_message('_loan_transfer_to_is_not_same_as_loan_from', 'Loan from can not be the same as Loan to');
                return FALSE;
            }else{
                return TRUE;
            }
        }else{
            return TRUE;
        }
    }

    function _member_to_id_is_not_same_as_member_id(){
        if($this->input->post('transfer_to')==4){
            $member_to_id = $this->input->post('member_to_id');
            $member_id = $this->input->post('member_id');
            if($member_id==$member_to_id){
                $this->form_validation->set_message('_member_to_id_is_not_same_as_member_id', 'Select another member to transfer to, you cannot transfer to the same member');
                return FALSE;
            }else{
                return TRUE;
            }
        }else{
            return TRUE;
        }
    }

    function contribution_transfers(){
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $data['from'] = $from;
        $data['to'] = $to;
        $data['transfer_to_options'] = $this->transfer_to_options;
        $data['member_transfer_to_options'] = $this->member_transfer_to_options;
        $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
        if($this->input->get('generate_excel')==1){
            $filter_parameters = array(
                'from' => $from,
                'to' => $to,
                'transfer_to' => $this->input->get('transfer_to'),
                'member_transfer_to' => $this->input->get('member_transfer_to'),
                'member_id' => $this->input->get('member_id'),
                'contribution_from_id' => $this->input->get('contribution_from_id'),
                'member_to_id' => $this->input->get('member_to_id'),
            );
            $data['group_currency'] = $this->group_currency;
            $data['group'] = $this->group;
            $data['group_member_options'] = $this->group_member_options;
            $data['posts'] = $this->deposits_m->get_group_contribution_transfers($filter_parameters);
            $json_file = json_encode($data);
            //print_r($json_file);
            print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/deposits/contribution_transfers',$this->group->name.' Contribution Transfers'));
            die;
        }
        $this->template->title(translate('List Contribution Transfers'))->build('group/contribution_transfers',$data);
    }


    function void_contribution_transfer($id = 0,$redirect = TRUE){
        // $id OR redirect('group/deposits/contribution_transfers');
        // $post = $this->deposits_m->get_group_contribution_transfer($id);
        // $post OR redirect('group/deposits/contribution_transfers');
        // $this->transactions->void_contribution_transfer($post->id,$post,TRUE,$this->group->id,$this->user);
        // if($redirect){
        //     if($this->agent->referrer()){
        //         redirect($this->agent->referrer());
        //     }else{
        //         redirect('group/deposits/contribution_transfers');
        //     }
        // }
        $id OR redirect('group/deposits/contribution_transfers');
        $post = $this->deposits_m->get_group_contribution_transfer($id);
        $post OR redirect('group/deposits/contribution_transfers');
        if($this->transactions->void_contribution_transfer($id,$this->group->id)){
            $this->session->set_flashdata('success','Contribution transfer voided successfully');
        }else{
            $this->session->set_flashdata('error','Could not void contribution transfer successfully');
        }
        if($redirect){
            if($this->agent->referrer()){
                redirect($this->agent->referrer());
            }else{
                redirect('group/deposits/contribution_transfers');
            }
        }
    }

    function void($id = 0,$redirect = TRUE){
        $id OR redirect('bank/deposits/listing');
        $post = $this->deposits_m->get_group_deposit($id);
         
        $post OR redirect('bank/deposits/listing');
        $this->transactions->void_group_deposit($post->id,$post,TRUE,$this->group->id,$this->user);
        if($redirect){
            if($this->agent->referrer()){
                redirect($this->agent->referrer());
            }else{
                redirect('bank/deposits/listing');
            }
        }
    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if(empty($action_to)){
            $this->session->set_flashdata('error','Select atleast one deposit to void');
            redirect($this->agent->referrer());
        }
        if($action == 'bulk_void'){
            for($i=0;$i<count($action_to);$i++){
                $this->void($action_to[$i],FALSE);
            }
            redirect($this->agent->referrer());
        }else if($action == 'bulk_void_contribution_transfers'){
            for($i=0;$i<count($action_to);$i++){
                $this->void_contribution_transfer($action_to[$i],FALSE);
            }
            redirect($this->agent->referrer());
        }else if($action == 'bulk_pdf_receipts'){
            if(empty($action_to)){
                $this->session->set_flashdata('error','Select atleast one deposit to generate a receipt for');
                redirect('group/deposits/listing');
            }else{
                $data = array();
                for ($i = 0; $i <count($action_to); $i++) {
                    $posts[] = $this->deposits_m->get($action_to[$i]);
                }
                if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
                    $data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
                }else{
                    $data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                }
                $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
                $data['posts'] = $posts;
                $data['deposit_type_options'] = $this->transactions->deposit_type_options;
                $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
                $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
                $data['income_category_options'] = $this->income_categories_m->get_group_income_category_options();
                $data['stock_options'] = $this->stocks_m->get_group_stock_options();
                $data['asset_options'] = $this->assets_m->get_group_asset_options();
                $data['money_market_investment_options'] = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
                $data['deposit_transaction_names'] = $this->transactions->deposit_transaction_names;
                $data['deposit_type_options'] = $this->transactions->deposit_type_options;
                $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
                $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
                $data['deposit_method_options'] = $this->transactions->deposit_method_options;
                $data['deposit_for_options'] = $this->transactions->deposit_for_options;
                $data['depositor_options'] = $this->depositors_m->get_group_depositor_options();
                $data['income_category_options'] = $this->income_categories_m->get_group_income_category_options();
                $data['stock_options'] = $this->stocks_m->get_group_stock_options();
                $data['money_market_investment_options'] = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
                $data['accounts'] = $this->accounts_m->get_group_account_options(FALSE);
                $data['group_member_options'] = $this->group_member_options;
                $data['group_debtor_options'] = $this->group_debtor_options;
                $data['group'] = $this->group;
                $data['group_currency'] = $this->group_currency;
                $data['application_settings'] = $this->application_settings;
                $response = $this->curl_post_data->curl_post_json_pdf((json_encode($data)),'https://pdfs.chamasoft.com/bulk_deposit_receipts',$this->group->name.' Deposit Receipt ');
                print_r($response);die;    
            }
            
        }
    }

    function view($id=0,$generate_pdf = FALSE){
        $id OR redirect($this->agent->referrer(),'refresh');
        $post = $this->deposits_m->get_group_deposit($id);
        if(empty($post)){
            $this->session->set_flashdata('error','Sorry, the entry does not exists.');
            redirect($this->agent->referrer(),'refresh');
            die;
        }

        $data['deposit_transaction_names'] = $this->transactions->deposit_transaction_names;
        $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
        $data['deposit_method_options'] = $this->transactions->deposit_method_options;
        $data['depositor_options'] = $this->depositors_m->get_group_depositor_options();
        $data['income_category_options'] = $this->income_categories_m->get_group_income_category_options();
        $data['stock_options'] = $this->stocks_m->get_group_stock_options();
        $data['money_market_investment_options'] = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
        $data['accounts'] = $this->accounts_m->get_group_account_options(FALSE);
        $data['post'] = $post;
        $data['group'] = $this->group;
        $data['application_settings'] = $this->application_settings;
        $data['group_member_options'] = $this->group_member_options;
        $data['group_currency'] = $this->group_currency;
        if($generate_pdf==TRUE){
            if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
                $data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
            }else{
                $data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
            }
            $response = $this->curl_post_data->curl_post_json_pdf((json_encode($data)),'https://pdfs.chamasoft.com/deposit_receipt',$this->group->name.' Deposit Receipt ');
            print_r($response);die;
        }
        $this->template->title('View Deposit')->build('shared/view',$data);
    }

    function generate_pdf_deposit_receipt($id=0){
        $id OR redirect($this->agent->referrer(),'refresh');
        $post = $this->deposits_m->get_group_deposit($id);
        if($post){
            $data['deposit_transaction_names'] = $this->transactions->deposit_transaction_names;
            $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
            $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
            $data['deposit_method_options'] = $this->transactions->deposit_method_options;
            $data['depositor_options'] = $this->depositors_m->get_group_depositor_options();
            $data['income_category_options'] = $this->income_categories_m->get_group_income_category_options();
            $data['stock_options'] = $this->stocks_m->get_group_stock_options();
            $data['money_market_investment_options'] = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
            $data['accounts'] = $this->accounts_m->get_group_account_options(FALSE);
            $data['post'] = $post;
            $data['group'] = $this->group;
            $data['application_settings'] = $this->application_settings;
            $data['group_member_options'] = $this->group_member_options;
            $data['group_currency'] = $this->group_currency;
            if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
                $data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
            }else{
                $data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
            }
            $response = $this->curl_post_data->curl_post_json_pdf((json_encode($data)),'https://pdfs.chamasoft.com/deposit_receipt',$this->group->name.' Deposit Receipt ');
            print_r($response);die;
        }else{
            $this->session->set_flashdata('error','Sorry, the entry does not exists.');
            redirect($this->agent->referrer(),'refresh');
            die;
        }

        
    }

    function view_voided_deposit($id = 0){
        $post = $this->deposits_m->get_group_deposit($id);
        die('no a valid url');
        if(empty($post)){
            $this->session->set_flashdata('error','Sorry, the entry does not exists.');
            redirect($this->agent->referrer(),'refresh');
            die;
        }

        $data['deposit_transaction_names'] = $this->transactions->deposit_transaction_names;
        $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
        $data['deposit_method_options'] = $this->transactions->deposit_method_options;
        $data['depositor_options'] = $this->depositors_m->get_group_depositor_options();
        $data['income_category_options'] = $this->income_categories_m->get_group_income_category_options();
        $data['stock_options'] = $this->stocks_m->get_group_stock_options();
        $data['money_market_investment_options'] = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
        $data['accounts'] = $this->accounts_m->get_group_account_options(FALSE);
        $data['post'] = $post;
        $data['group'] = $this->group;
        $data['application_settings'] = $this->application_settings;
        $data['group_member_options'] = $this->group_member_options;
        $data['group_currency'] = $this->group_currency;
        print_r($data); die();
        $this->template->title('View Deposit')->build('shared/view',$data);

    }

    function find_voided_deposits_active_transaction_statement_entries(){
        $group_deposits = $this->deposits_m->get_voided_group_deposits($this->group->id);
        echo count($group_deposits);
        foreach ($group_deposits as $group_deposit) {
            # code...
            $transaction_statement_entry = $this->transaction_statements_m->get_transaction_statement_entry_by_deposit_id($group_deposit->id,$this->group->id);
            if($transaction_statement_entry){
                print_r($transaction_statement_entry);
            }
        }
    }

    function ajax_record_back_dating_contributions(){
        $posts = $_POST;
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        if($suspense_petty_cash_account = $this->petty_cash_accounts_m->get_group_back_dating_petty_cash_suspense_account()){
            $account_id = "petty-".$suspense_petty_cash_account->id;
            if($group_cut_off_date){
                if(isset($posts)){
                    $paid = $this->input->post('paid');
                    if(empty($paid)){
                        echo "Paid post value empty";
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
                        foreach($paid as $member_id => $contributions):
                            if(isset($member_objects_array[$member_id])){
                                foreach($contributions as $contribution_id => $amount):
                                    if(isset($contribution_objects_array[$contribution_id])){
                                        if($amount){
                                            if($this->transactions->record_contribution_payment($this->group->id,$group_cut_off_date->cut_off_date,$member_id,$contribution_id,$account_id,1,'Backdating contribution payment',valid_currency($amount),FALSE,FALSE,0,TRUE)){

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
        }else{
            echo "Back dating petty cash account not found";
        }
    }

    function ajax_void_group_back_dating_contribution_deposits(){
        $deposits = $this->deposits_m->get_group_back_dating_contributions();
        $result = TRUE;
        foreach($deposits as $deposit):
            if($this->transactions->void_group_deposit($deposit->id,$deposit,FALSE,$this->group->id,$this->user)){

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

    function ajax_void_group_back_dating_fine_deposits(){
        $deposits = $this->deposits_m->get_group_back_dating_fines();
        $result = TRUE;
        foreach($deposits as $deposit):
            if($this->transactions->void_group_deposit($deposit->id,$deposit,FALSE,$this->group->id,$this->user)){

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

    function ajax_record_back_dating_fines(){
        $posts = $_POST;
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        if($suspense_petty_cash_account = $this->petty_cash_accounts_m->get_group_back_dating_petty_cash_suspense_account()){
            $account_id = "petty-".$suspense_petty_cash_account->id;
            if($group_cut_off_date){
                if(isset($posts)){
                    $fines_paid = $this->input->post('fines_paid');
                    if(empty($fines_paid)){
                        echo "Fines paid post value empty";
                    }else{

                        $members = $this->members_m->get_group_members();
                        $fine_category = $this->fine_categories_m->get_group_back_dating_fine_category();

                        $member_objects_array = array();
                        foreach($members as $member):
                            $member_objects_array[$member->id] = $member;
                        endforeach;

                        $result = TRUE;
                        foreach($fines_paid as $member_id => $amount):
                            if(isset($member_objects_array[$member_id])){
                                if($fine_category){
                                    if($amount){
                                        if($this->transactions->record_fine_payment($this->group->id,$group_cut_off_date->cut_off_date,$member_id,'fine_category-'.$fine_category->id,$account_id,1,'Back-dating fine payment',valid_currency($amount),FALSE,FALSE,0,TRUE)){

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
        }else{
            echo "Back dating petty cash account not found";
        }
    }

    function ajax_loans_paid_listing(){
        $group_member_total_back_dated_loans_paid_per_array = $this->deposits_m->get_group_member_total_back_dated_loans_paid_per_array();
        if($group_member_total_back_dated_loans_paid_per_array){
            echo '<h4>Back-dated Loans Paid</h4>';
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
                                Loans Paid ('.$this->group_currency.')
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                            $count = 1; 
                            $total_loans_paid = 0;
                            foreach($this->group_member_options as $member_id => $member_name): 
                            $loans_paid = $group_member_total_back_dated_loans_paid_per_array[$member_id];
                            $total_loans_paid += $loans_paid;
                        echo '
                            <tr>
                                <td>'.$count++.'</td>
                                <td>'.$member_name.'</td>
                                <td  class="text-right">'.number_to_currency($loans_paid).'</td>
                            </tr>';
                            endforeach; 
                        echo '
                        <tr>
                            <td>#</td>
                            <td>Totals</td>
                            <td class="text-right">
                                '.number_to_currency($total_loans_paid).'
                            </td>
                        </tr>
                    </tbody>
                </table>';
        }       
    }

    function ajax_loans_paid_form(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $group_member_total_back_dated_loans_paid_per_array = $this->deposits_m->get_group_member_total_back_dated_loans_paid_per_array();
        echo '
        <div class="alert alert-info">
            <strong>Information!</strong> Enter the amount each member <strong>had</strong> paid towards loans as at '.timestamp_to_date($group_cut_off_date->cut_off_date).'
        </div>';
            echo '<h4>Back-dated Loans Paid</h4>';
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
                            Loans Paid ('.$this->group_currency.')
                        </th>
                    </tr>
                </thead>
                <tbody>';
                        $count = 1; 
                        $total_loans_paid = 0;
                        foreach($this->group_member_options as $member_id => $member_name): 
                        $loans_paid = $group_member_total_back_dated_loans_paid_per_array[$member_id];
                        $total_loans_paid += $loans_paid;
                        echo '
                        <tr>
                            <td>'.$count++.'</td>
                            <td>'.$member_name.'</td>
                            <td class="text-right"> 
                                '.form_input('loans_paid['.$member_id.']',$loans_paid," class='form-control currency'").'
                            </td>
                        </tr>'; 
                        endforeach; 
                echo '
                </tbody>
            </table>';
    }

    function ajax_record_group_back_dating_loans_paid(){
        $posts = $_POST;
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        if($suspense_petty_cash_account = $this->petty_cash_accounts_m->get_group_back_dating_petty_cash_suspense_account()){
            $account_id = "petty-".$suspense_petty_cash_account->id;
            if($group_cut_off_date){
                if(isset($posts)){
                    $loans_paid = $this->input->post('loans_paid');
                    if(empty($loans_paid)){
                        echo "Loans paid post value empty";
                    }else{
                        $members = $this->members_m->get_group_members();

                        $loans = $this->loans_m->get_group_member_back_dated_loans();

                        $member_objects_array = array();
                        foreach($members as $member):
                            $member_objects_array[$member->id] = $member;
                        endforeach;

                        $loan_objects_array = array();
                        foreach($loans as $loan):
                            $loan_objects_array[$loan->member_id] = $loan;
                        endforeach;

                        $result = TRUE;
                        foreach($loans_paid as $member_id => $amount):
                            if(isset($member_objects_array[$member_id])){
                                if(isset($loan_objects_array[$member_id])){
                                    if($amount){
                                        if($this->loan->record_loan_repayment($this->group->id,$group_cut_off_date->cut_off_date,$member_objects_array[$member_id],$loan_objects_array[$member_id]->id,$account_id,1,'Back-dating loan repayment',valid_currency($amount),FALSE,FALSE,$this->user,$member_objects_array[$member_id]->user_id,0,TRUE)){

                                        }else{
                                            $result = FALSE;
                                        }
                                    }
                                }else{
                                    //$result = FALSE;
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
        }else{
            echo "Back dating petty cash account not found";
        }
    }

    function ajax_void_group_back_dating_loans_paid(){
        $deposits = $this->deposits_m->get_group_back_dating_loans_paid_deposits();
        $result = TRUE;
        foreach($deposits as $deposit):
            if($this->transactions->void_group_deposit($deposit->id,$deposit,FALSE,$this->group->id,$this->user)){

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

    function ajax_group_loans_borrowed_form(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        if($bank_loan = $this->bank_loans_m->get_group_back_dating_group_loan_borrowed()){
            $group_loan_borrowed = $bank_loan->amount_loaned;
            $group_loan_payable = $bank_loan->amount_payable;
        }else{
            $group_loan_borrowed = 0;
            $group_loan_payable = 0;
        }
        echo '
        <div class="alert alert-info">
            <strong>Information!</strong> Enter the amount the group <strong>had</strong> borrowed in total as at '.timestamp_to_date($group_cut_off_date->cut_off_date).'
        </div>';
            echo '<h4>Back-dated Group Loans Borrowed</h4>';
            echo '
            <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                <thead>
                    <tr>
                        <th width="8px">
                            #
                        </th>
                        <th>
                            Decription
                        </th>
                        <th class="text-right">
                            Group Loans Borrowed Total ('.$this->group_currency.')
                        </th>
                        <th class="text-right">
                            Total Payable ('.$this->group_currency.')
                        </th>
                    </tr>
                </thead>
                <tbody>';
                        echo '
                        <tr>
                            <td>1</td>
                            <td>Group Borrowed Total</td>
                            <td class="text-right"> 
                                '.form_input('group_loan_borrowed',$group_loan_borrowed," class='form-control currency'").'
                            </td>
                            <td class="text-right"> 
                                '.form_input('group_loan_payable',$group_loan_payable," class='form-control currency'").'
                            </td>
                        </tr>'; 
                echo '
                </tbody>
            </table>';
    }

    function ajax_group_loans_borrowed_listing(){
        if($bank_loan = $this->bank_loans_m->get_group_back_dating_group_loan_borrowed()){
            $group_loan_borrowed = $bank_loan->amount_loaned;
            $group_loan_payable = $bank_loan->amount_payable;
        }else{
            $group_loan_borrowed = 0;
            $group_loan_payable = 0;
        }
            echo '<h4>Back-dated Group Loans Borrowed</h4>';
            echo '
            <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                <thead>
                    <tr>
                        <th width="8px">
                            #
                        </th>
                        <th>
                            Decription
                        </th>
                        <th class="text-right">
                            Group Loans Borrowed Total ('.$this->group_currency.')
                        </th>
                        <th class="text-right">
                            Total Payable ('.$this->group_currency.')
                        </th>
                    </tr>
                </thead>
                <tbody>';
                        echo '
                        <tr>
                            <td>1</td>
                            <td>Group Borrowed Total</td>
                            <td class="text-right"> 
                                '.number_to_currency($group_loan_borrowed).'
                            </td>
                            <td class="text-right"> 
                                '.number_to_currency($group_loan_payable).'
                            </td>
                        </tr>'; 
                echo '
                </tbody>
            </table>';
    }

    function ajax_void_group_back_dating_group_loans_borrowed(){
        $deposits = $this->deposits_m->get_group_back_dating_group_loans_borrowed_deposits();
        $result = TRUE;
        foreach($deposits as $deposit):
            if($this->transactions->void_group_deposit($deposit->id,$deposit,FALSE,$this->group->id,$this->user)){

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

    function ajax_record_group_back_dating_group_loans_borrowed(){
        $posts = $_POST;
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        if($suspense_petty_cash_account = $this->petty_cash_accounts_m->get_group_back_dating_petty_cash_suspense_account()){
            $account_id = "petty-".$suspense_petty_cash_account->id;
            if($group_cut_off_date){
                if(isset($posts)){
                    $group_loan_borrowed = $this->input->post('group_loan_borrowed');
                    $group_loan_payable = $this->input->post('group_loan_payable');
                    if(empty($group_loan_borrowed)&&empty($group_loan_payable)){
                        echo "success";
                    }else{
                        $result = TRUE;
                        if($group_loan_borrowed&&$group_loan_payable){
                            if($this->transactions->create_bank_loan($this->group->id,'Back-dating group loans borrowed',valid_currency($group_loan_borrowed),valid_currency($group_loan_payable),valid_currency($group_loan_payable),$group_cut_off_date->cut_off_date,$group_cut_off_date->cut_off_date,$account_id,0,0,TRUE)){

                            }else{
                                $result = FALSE;
                            }
                        }
                        if($result){
                            echo "success";
                        }else{
                            echo "error";
                        }
                    }   
                }else{
                    echo "Post data not set";
                }   
            }else{
                echo "Group cut off date not yet set";
            }
        }else{
            echo "Back dating petty cash account not found";
        }
    }

    function ajax_income_form(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $income_category_options = $this->income_categories_m->get_group_income_category_options();
        $group_total_back_dated_incomes_per_income_category_array = $this->deposits_m->get_group_total_back_dated_income_per_income_category_array();
        if(!empty($income_category_options)){
            echo '
            <div class="alert alert-info">
                <strong>Information!</strong> Enter the amount the group <strong>had</strong> earned per income category as at '.timestamp_to_date($group_cut_off_date->cut_off_date).'
            </div>';
            echo '<h4>Back-dated Income</h4>';
            echo '
                <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                    <thead>
                        <tr>
                            <th width="8px">
                                #
                            </th>
                            <th>
                                Income Category
                            </th>
                            <th class="text-right">
                                Amount Earned ('.$this->group_currency.')
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                            $count = 1; 
                            $total_income = 0;
                            foreach($income_category_options as $income_category_id => $income_category_name): 
                            $income = $group_total_back_dated_incomes_per_income_category_array[$income_category_id];
                            $total_income += $income;
                        echo '
                            <tr>
                                <td>'.$count++.'</td>
                                <td>'.$income_category_name.'</td>
                                <td  class="text-right">'.form_input('income['.$income_category_id.']',$income," class='form-control currency'").'</td>
                            </tr>';
                            endforeach; 
                        echo '
                    </tbody>
                </table>';
        }   
    }

    function ajax_income_listing(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $income_category_options = $this->income_categories_m->get_group_income_category_options();
        $group_total_back_dated_incomes_per_income_category_array = $this->deposits_m->get_group_total_back_dated_income_per_income_category_array();
        if(!empty($income_category_options)){
            echo '<h4>Back-dated Income</h4>';
            echo '
                <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                    <thead>
                        <tr>
                            <th width="8px">
                                #
                            </th>
                            <th>
                                Income Category
                            </th>
                            <th class="text-right">
                                Amount Earned ('.$this->group_currency.')
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                            $count = 1; 
                            $total_income = 0;
                            foreach($income_category_options as $income_category_id => $income_category_name): 
                            $income = $group_total_back_dated_incomes_per_income_category_array[$income_category_id];
                            $total_income += $income;
                        echo '
                            <tr>
                                <td>'.$count++.'</td>
                                <td>'.$income_category_name.'</td>
                                <td  class="text-right">'.number_to_currency($income).'</td>
                            </tr>';
                            endforeach; 
                        echo '
                            <tr>
                                <td>#</td>
                                <td>Totals</td>
                                <td class="text-right">'.number_to_currency($total_income).'</td>
                            </tr>';
                        echo '
                    </tbody>
                </table>';
        } 
    }

    function ajax_void_group_back_dating_income(){
        $deposits = $this->deposits_m->get_group_back_dating_income_deposits();
        $result = TRUE;
        foreach($deposits as $deposit):
            if($this->transactions->void_group_deposit($deposit->id,$deposit,FALSE,$this->group->id,$this->user)){

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

    function ajax_record_back_dating_income(){
        $posts = $_POST;
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        if($suspense_petty_cash_account = $this->petty_cash_accounts_m->get_group_back_dating_petty_cash_suspense_account()){
            $account_id = "petty-".$suspense_petty_cash_account->id;
            if($group_cut_off_date){
                if(isset($posts)){
                    $income = $this->input->post('income');
                    if(empty($income)){
                        echo "Income post value empty";
                    }else{
                        $result = TRUE;
                        if($depositor = $this->depositors_m->get_group_back_dating_depositor()){
                            foreach($income as $income_category_id => $amount):
                                if($amount){
                                    if($this->transactions->record_income_deposit($this->group->id,$group_cut_off_date->cut_off_date,$depositor->id,$income_category_id,$account_id,1,'Back-dating Income',valid_currency($amount),0,TRUE)){

                                    }else{
                                        $result = FALSE;
                                    }
                                }
                            endforeach;
                        }else{
                            $result = FALSE;
                        }
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
        }else{
            echo "Back dating petty cash account not found";
        }
    }

    function make_payment(){
        $this->data['default_calling_code'] = $this->countries_m->get_default_calling_code();
        $this->data['total_member_payments'] = $this->deposits_m->get_member_total_payments();
        $this->data['member_active_loans'] = $this->loans_m->get_member_loans_option($this->member->id);
        $this->data['payment_for_options'] = $this->transactions->payment_for_options;
        $this->data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
        $this->data['contribution_options'] = $this->contributions_m->get_active_group_contribution_options();
        $this->data['total_group_member_contribution_arrears'] = $this->statements_m->get_member_contribution_balance($this->group->id,$this->member->id);
        $this->data['total_group_member_fine_arrears'] = $this->statements_m->get_member_fine_balance($this->group->id,$this->member->id);
        $group_default_bank_account = $this->bank_accounts_m->get_group_default_bank_account($this->group->id);
        $default_account_number = 0;
        if($group_default_bank_account){
            $default_account_number = $group_default_bank_account->account_number;
        }
        $this->data['group_default_bank_account'] = $default_account_number;
        $ongoing_loan_amounts_payable = array();
        $ongoing_loan_amounts_paid = array();
        $base_where = array('member_id'=>$this->member->id,'is_fully_paid'=>0);
        $ongoing_member_loans = $this->loans_m->get_many_by($base_where);
        foreach ($ongoing_member_loans as $ongoing_member_loan){
            $ongoing_loan_amounts_payable[$ongoing_member_loan->id]
            = $this->loans_m->get_summation_for_invoice($ongoing_member_loan->id)->total_amount_payable;
            $ongoing_loan_amounts_paid[$ongoing_member_loan->id]
            = $this->loan_repayments_m->get_loan_total_payments($ongoing_member_loan->id);
        }
        $ongoing_payable_loan_amount = array_sum($ongoing_loan_amounts_payable);
        $ongoing_loans_paid_amount = array_sum($ongoing_loan_amounts_paid);
        $this->data['total_loan_balances'] = $ongoing_payable_loan_amount - $ongoing_loans_paid_amount;
        $data['member_only'] = TRUE;
        $this->template->title('Make Group Contributions')->build('shared/make_payments',$this->data);
    }

    function your_payments(){
        $member = $this->member;
        $transaction_alert_id = $this->input->get('transaction_alert');
        $data = array();
        $filter_parameters = array(
            'transaction_alert_id' => $transaction_alert_id
        );
        $data['deposit_transaction_names'] = $this->transactions->deposit_transaction_names;
        $total_rows = $this->deposits_m->count_group_and_member_deposits($this->group->id,$member->id,$filter_parameters);
        $data['from'] = strtotime($this->input->get('from'))?:'';
        $data['to'] = strtotime($this->input->get('to'))?:'';
        $pagination = create_pagination('member/deposits/listing/pages', $total_rows,50,5,TRUE);
        $data['pagination'] = $pagination;
        $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
        $data['deposit_method_options'] = $this->transactions->deposit_method_options;
        $data['posts'] = $this->deposits_m->limit($pagination['limit'])->get_group_and_member_deposits($this->group->id,$member->id,$filter_parameters);
        $data['depositor_options'] = $this->depositors_m->get_group_depositor_options();
        $data['income_category_options'] = $this->income_categories_m->get_group_income_category_options();
        $data['stock_options'] = $this->stocks_m->get_group_stock_options();
        $data['money_market_investment_options'] = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
        $data['accounts'] = $this->accounts_m->get_group_account_options(FALSE);
        $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
        $data['asset_options'] = $this->assets_m->get_group_asset_options();
        $data['deposit_type_options'] = $this->transactions->deposit_type_options;
        $data['member_only'] = TRUE;
        $this->template->title('Your Group Payments')->build('shared/listing',$data);
    }

    function check_missing_transaction_statements(){
        $deposits = $this->deposits_m->get_group_deposits();
        echo count($deposits);
        $transaction_statement_deposit_ids_array = $this->transaction_statements_m->get_group_transaction_statement_deposit_ids_array($this->group->id);
        foreach($deposits as $deposit):
            if(isset($transaction_statement_deposit_ids_array[$deposit->id])){

            }else{
                echo "Am in.<br/>";
            }
        endforeach;
        
    }

}