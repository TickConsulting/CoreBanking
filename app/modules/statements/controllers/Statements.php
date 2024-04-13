<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statements extends Public_Controller{

    public $chamasoft_settings = array();

    public function __construct(){
        parent::__construct();
        $this->load->library('messaging');
        $this->load->library('pdf_library');
        // $this->load->library('member_notifications');
        $this->load->library('loan');
        $this->load->model('statements/statements_m');
        $this->load->model('fine_categories/fine_categories_m');
        $this->load->model("accounts/accounts_m");
        $this->load->library("transactions");
        $this->load->model("loans/loans_m");
        set_time_limit(0);
        ini_set('memory_limit','2048M');
        ini_set('max_execution_time', 3000);
        $this->chamasoft_settings = $this->settings_m->get_settings()?:'';
    }
    
    function get_group_member_total_cumulative_contribution_arrears_per_member_array($group_id = ''){
        print_r($this->statements_m->get_group_member_total_cumulative_contribution_arrears_per_member_array($group_id));
    }

    function queue_monthly_statement_request($date = ''){
        $this->statements_m->delete_old_statement_requests();
        if($date){
            $date = strtotime($date);
        }else{
            $date = time();
        }
        $from = strtotime('-3 months',$date);
        $successes = 0;
        $failures = 0;
        $groups_with_monthly_email_statements_due_today = $this->groups_m->get_groups_with_monthly_email_statements_due_today($date);
        if($groups_with_monthly_email_statements_due_today){
            foreach ($groups_with_monthly_email_statements_due_today as $group){
                $input = array();
                $members = $this->members_m->get_active_group_members($group->id);
                foreach ($members as $member){
                    if(valid_email($member->email)){
                        $input[] = array(
                            'user_id' => $member->user_id,
                            'member_id' => $member->id,
                            'group_id' => $group->id,
                            'statement_file_type' => 1,
                            'type'  => 1,
                            'date_from' => $from,
                            'date_to' => $date,
                            'created_on' => time(),
                            'created_by' => 0,
                            'active' => 1,
                            'action' => 1,
                            'email' => $member->email,
                        );
                    }
                }
                if(!empty($input)){
                    if($this->statements_m->batch_insert_statement_request($input)){
                        $successes += count($input);
                        $group_data = array(
                            'next_monthly_contribution_statement_send_date' => mktime(0, 0, 0, date('m',$date)+1,$group->statement_send_date?:date('d',$date), date('Y',$date)),
                            'modified_on' => time(),
                            'modified_by' => 0,
                        );
                        $this->groups_m->update($group->id,$group_data);
                    }
                }else{
                    $group_data = array(
                        'next_monthly_contribution_statement_send_date' => mktime(0, 0, 0, date('m',$date)+1,$group->statement_send_date?:date('d',$date), date('Y',$date)),
                        'modified_on' => time(),
                        'modified_by' => 0,
                    );
                    $this->groups_m->update($group->id,$group_data);
                }
            }
            echo $successes.' queued';
        }else{
            echo 'There are no groups with monthly email statements enabled with due statements to queue today';
        }
    }

    function process_user_statement_requests($limit = 10){
        //die;
        $statement_requests = $this->statements_m->get_queued_statement_requests($limit);
        $items = 0;        
        foreach ($statement_requests as $statement_request) {
            if($statement_request->type==1){//contribution
                $this->_generate_contibution_statement($statement_request);
                $update = array(
                    'active' => 2,
                    'modified_on' => time(),
                );
                $this->statements_m->update_statement_requests($statement_request->id,$update);
            }else if($statement_request->type==2){//fine
                $this->_generate_fine_statement($statement_request);
                $update = array(
                    'active' => 2,
                    'modified_on' => time(),
                );
                $this->statements_m->update_statement_requests($statement_request->id,$update);
            }else if($statement_request->type==3){//loan
                $this->_generate_loan_statement($statement_request);
                $update = array(
                    'active' => 2,
                    'modified_on' => time(),
                );
                $this->statements_m->update_statement_requests($statement_request->id,$update);
            }
            ++$items;
        }
        echo $items.' statements sent';
    }

    function _generate_contibution_statement($statement_request = array()){
        $this->group = $this->groups_m->get($statement_request->group_id);
        $member = $this->members_m->get_group_member($statement_request->member_id,$this->group->id);
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime(date('d-m-Y',strtotime('-18 months')));
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime(date('d-m-Y',strtotime('tomorrow')));
        $statement_type = 'Contribution Statement';
        if($this->group->disable_arrears){//deposit statement
            $this->data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE,$this->group->id);
            $this->data['next_group_member_id'] = $this->_get_next_group_member($member->id);
            $this->data['contribution_ids'] = $contribution_ids;
            $this->data['from'] = $from;
            $this->data['to'] = $to;
            $contribution_options = $this->contributions_m->get_group_contribution_options($this->group->id,FALSE);
            $this->data['contribution_options'] = $contribution_options;
            $contribution_display_options = $this->contributions_m->get_group_contribution_display_options($this->group->id,TRUE);
            $contribution_list = '0';
            foreach ($contribution_display_options as $id=>$name) {
                if($contribution_list){
                    $contribution_list.=','.$id;
                }else{
                    $contribution_list=$id;
                }
            }
            $this->data['contribution_display_options'] = $contribution_display_options;
            $this->data['contributions'] = $this->contributions_m->get_group_contributions();
            $this->data['total_member_deposit_amounts'] = $this->statements_m->get_group_member_total_paid_by_contribution_array($member->id,$this->group->id,$contribution_list);
            $this->data['opening_balances'] = $this->statements_m->get_group_member_total_paid_by_contribution_array($member->id,$this->group->id,$contribution_list,$from);
            $this->data['statement_transaction_names'] = $this->transactions->statement_transaction_names;
            $this->data['group'] = $this->group;
            $this->data['group_currency'] = $this->group_currency;
            $this->data['application_settings'] = $this->application_settings;
            $this->data['member'] = $member;
            $this->data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
            $this->data['posts'] = $this->statements_m->get_member_deposit_statement_array($member->id,$contribution_list,$from,$to); 
            if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
                $this->data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
            }else{
                $this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
            }
            $this->data['pdf_true'] = TRUE;
            $html = $this->load->view('group/deposit_statement',$this->data,TRUE);
            $this->pdf_library->generate_landscape_report($html,TRUE,(($member->first_name.$member->last_name.$member->id).' Contribution Deposit Statement - '.$this->group->name));
            $file_path = $this->pdf_library->generate_landscape_report($html,TRUE,(($member->first_name.$member->last_name.$member->id).' Contribution Contribution Statement - '.$this->group->name));
            $user = $this->ion_auth->get_user($statement_request->user_id);
            $file_size = filesize($file_path);
            $cc = $statement_request->cc;
            if($this->messaging->send_member_statement($user,$member,$this->group,''.$file_path,$statement_request->email,$from,$to,$statement_type,$cc)){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            //$open_contribution_options = $this->contributions_m->get_group_open_contribution_options();
            $open_contribution_options = $this->contributions_m->get_group_contribution_display_options($this->group->id,TRUE);
            $contribution_ids = $this->input->get('contributions');
            $count = 1;
            $contribution_id_list = '';
            if(!$contribution_id_list)
                if($open_contribution_options)
                    $contribution_id_list = implode(',',array_keys($open_contribution_options));
            $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE,$this->group->id);
            $data['next_group_member_id'] = '';
            $data['contribution_ids'] = array();
            $data['from'] = $from;
            $data['to'] = $to;
            $data['open_contribution_options'] = $open_contribution_options;
            $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
            $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
            $data['member'] = $member;
            $data['balance'] = $this->statements_m->get_member_contribution_balance($this->group->id,$member->id,$contribution_id_list,$from);
            $data['amount_payable'] = $this->statements_m->get_member_contribution_amount_payable($this->group->id,$member->id,$contribution_id_list,$from);
            $data['amount_paid'] = $this->statements_m->get_member_contribution_amount_paid($this->group->id,$member->id,$contribution_id_list,$from);
            $data['posts'] = $this->statements_m->get_member_contribution_statement($member->id,$contribution_id_list,$from,$to);
            // print_r($data['posts']); die;
            $data['statement_transaction_names'] = $this->transactions->statement_transaction_names;
            $data['group'] = $this->group;
            $data['group_currency'] = $this->groups_m->get_this_group_currency($statement_request->group_id);
            $data['application_settings'] = $this->application_settings;
            $data['display'] = '';
            $data['show_next_member'] = TRUE;
            // $data['group_member_options'] = $this
            if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
                $data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
            }else{
                $data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
            }
            $data['pdf_true'] = TRUE;
            $html = $this->load->view('shared/view',$data,TRUE);
            $file_path = $this->pdf_library->generate_landscape_report($html,TRUE,(($member->first_name.$member->last_name.$member->id).' Contribution Contribution Statement - '.$this->group->name));
            $user = $this->ion_auth->get_user($statement_request->user_id);
            $file_size = filesize($file_path);
            $cc = $statement_request->cc;
            if($this->messaging->send_member_statement($user,$member,$this->group,''.$file_path,$statement_request->email,$from,$to,$statement_type,$cc)){
                return TRUE;
            }else{
                return FALSE;
            }
        }
    }

    function _generate_fine_statement($statement_request=array()){
        $group = $this->groups_m->get($statement_request->group_id);
        $post = $this->members_m->get_group_member($statement_request->member_id,$group->id);
        $from = $statement_request->date_from;
        $to = $statement_request->date_to;
        $data['contribution_ids'] = array();
        $data['fine_category_ids'] = array();
        $data['from'] = $from;
        $data['to'] = $to;
        $data['contribution_fine_options'] = $this->contributions_m->get_group_contribution_fine_options($group->id);
        $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE,$group->id);
        $data['member'] = $post;
        $data['balance'] = $this->statements_m->get_member_fine_balance($group->id,$post->id,'','',$from);
        $data['posts'] = $this->statements_m->get_member_fine_statement($post->id,'','',$from,$to,$group->id);
        $data['statement_transaction_names'] = $this->transactions->statement_transaction_names;
        $data['next_group_member_id'] = '';
        $data['group'] = $group;
        $data['group_currency'] = $this->groups_m->get_this_group_currency($statement_request->group_id);
        $data['chamasoft_settings'] = $this->chamasoft_settings;
        if(is_file(FCPATH.'uploads/groups/'.$group->avatar)){
            $data['group_logo'] = site_url('uploads/groups/'.$group->avatar);
        }else{
            $data['group_logo'] = site_url('uploads/logos/'.$this->chamasoft_settings->paper_header_logo);
        }
        $user = $this->ion_auth->get_user($statement_request->user_id);
        $response = $this->curl_post_data->curl_post_json_pdf((json_encode($data)),'https://pdfs.chamasoft.com/fine_statement',($post->first_name.$post->last_name.$post->id).' Fine Statement - '.$group->name,FALSE);
        $statement_type = 'Fine Statement';
        if($statement_request->action == 1){
            if($this->messaging->send_member_statement($user,$post,$group,'./'.$response,$statement_request->email,$from,$to,$statement_type)){
                return TRUE;
            }else{
                return FALSE;
            }
        }elseif($statement_request->action == 2){
            $file_size = filesize('./'.$response);
            $file_path  = base_url().$response;
            // if($this->member_notifications->create(
            //     'File Type Ready',
            //     'Fine Statement ready for download',
            //     $user,$post->id,$user->id,$post->id,$group->id,'View File',site_url('group/statements/view'),16,'','','',$file_size,$file_path,1)){
            //     return TRUE;
            // }else{
            //     return FALSE;
            // }
        }
    }

    function _generate_loan_statement($statement_request=array()){
        $id = $statement_request->loan_id;
        $this->group = $this->groups_m->get($statement_request->group_id);
        $loan = $this->loans_m->get_loan_and_member($id);
        if($loan){
            $from = $loan->disbursement_date;
            $to = time();
            $group = $this->groups_m->get($statement_request->group_id);
            $member = $this->members_m->get_group_member($statement_request->member_id,$group->id);
            $user = $this->ion_auth->get_user($statement_request->user_id);
            $total_installment_payable = $this->loan_invoices_m->get_total_installment_loan_payable($id);
            $total_fines = $this->loan_invoices_m->get_total_loan_fines_payable($id);
            $total_transfers_out = $this->loan_invoices_m->get_total_loan_transfers_out($id);
            $total_paid = $this->loan_repayments_m->get_loan_total_payments($id);
            $loan_balance =$this->loans_m->get_loan_balance($id);
            $posts = $this->loans_m->get_loan_statement($id,$group->id);
            $this->data['loan'] = $loan;
            $this->data['posts'] = $posts;
            $this->data['total_installment_payable'] = $total_installment_payable;
            $this->data['total_fines'] = $total_fines;
            $this->data['total_transfers_out'] = $total_transfers_out;
            $this->data['total_paid'] = $total_paid;
            $this->data['lump_sum_remaining'] = $this->loan_invoices_m->get_loan_lump_sum_as_date($id);
            $this->accounts = $this->accounts_m->get_group_account_options(FALSE,'',$group->id);
            $this->data['accounts'] = $this->accounts;
            $this->data['deposit_options']=$this->transactions->deposit_method_options;
            $this->data['group'] = $group;
            $this->data['group_currency'] = $this->groups_m->get_this_group_currency($statement_request->group_id);
            $this->data['chamasoft_settings'] = $this->chamasoft_settings;
            $this->data['transfer_options'] = $this->loan->transfer_options;
            if(is_file(FCPATH.'uploads/groups/'.$group->avatar)){
                $this->data['group_logo'] = site_url('uploads/groups/'.$group->avatar);
            }else{
                $this->data['group_logo'] = site_url('uploads/logos/'.$this->chamasoft_settings->paper_header_logo);
            }
            $json_file = json_encode($this->data);
            $response = $this->curl_post_data->curl_post_json_pdf($json_file,'https://pdfs.chamasoft.com/loan_statement',($member->first_name.$member->last_name.$member->id).' Loan Statement - '.$group->name,FALSE);
            $statement_type = 'Loan Statement';
            if($statement_request->action == 1){
                if($this->messaging->send_member_statement($user,$member,$group,'./'.$response,$statement_request->email,$from,$to,$statement_type)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }elseif($statement_request->action == 2){
                $file_size = filesize('./'.$response);
                $file_path  = base_url().$response;
                // if($this->member_notifications->create(
                //     'File Type Ready',
                //     'Loan Statement ready for download',
                //     $user,$member->id,$user->id,$member->id,$group->id,'View File',site_url('group/statements/view'),16,'','','',$file_size,$file_path,1)){
                //     return TRUE;
                // }else{
                //     return FALSE;
                // }
            }
        }
    }

    function remove_voided_invoices_statement_entries($group_id = 0){
        $statements = $this->statements_m->get_all_group_invoice_statement_entries($group_id);
        if($statements){
            echo count($statements);
            $statement_entries = array();
            $count = 0;
            foreach ($statements as $statement) {
                if($statement->invoice_id){
                    if($this->invoices_m->get_active_invoice($statement->invoice_id)){
                        // echo $statement->id.' has valid invoice';
                    }else{
                        if($this->statements_m->update($statement->id,array(
                            'active' => 0,
                            'modified_by' => 0
                        ))){
                            echo $statement->id.' voided';
                            $count++;
                        }else{
                            echo $statement->id.' encountered error voiding';
                        }
                    }
                }
            }
            echo $count.' statement entries successfully voided';
        }else{
            echo 'No statement records found';
        }
       
    }

    function remove_voided_deposit_statement_entries($group_id = 0){
        $statements = $this->statements_m->get_all_group_deposit_statement_entries($group_id);
        // print_r($statements);die;
        if($statements){
            // echo count($statements);
            $statement_entries = array();
            $count = 0;
            foreach ($statements as $statement) {
                if($statement->deposit_id){
                    $deposit = $this->deposits_m->get_group_deposit($statement->deposit_id,$group_id);
                    if($deposit && $deposit->active == 1){
                        // echo $statement->id.' has valid invoice';
                    }else{
                        if($this->statements_m->update($statement->id,array(
                            'active' => 0,
                            'modified_by' => 0,
                            'modified_on' => time()
                        ))){
                            echo $statement->id.' voided';
                            $count++;
                        }else{
                            echo $statement->id.' encountered error voiding';
                        }
                    }
                }
            }
            echo $count.' statement entries successfully voided';
        }else{
            echo 'No statement records found';
        }
    }



    function delete_statement_requests(){
        $this->statements_m->delete_old_statement_requests();
    }

    function reconcile_group_member_contribution_statements($group_id = 0){
        $this->output->enable_profiler(TRUE);
        $group_ids = array($group_id);
        $member_options = $this->members_m->get_group_member_options($group_id);
        $member_ids = array_flip($member_options);
        $member_contribution_balances_array = array();
        $member_cumulative_balances_array = array();
        $date = strtotime('01-12-2000');
        if($this->transactions->update_group_member_contribution_statement_balances($group_ids,$member_ids,$date)){
            echo "Success";
        }
    }

    function reconcile_group_member_fine_statements($group_id = 0){
        $this->output->enable_profiler(TRUE);
        $group_ids = array($group_id);
        $member_options = $this->members_m->get_group_member_options($group_id);
        $member_ids = array_flip($member_options);
        //$member_ids = array(23);
        //$statement_entries = $this->statements_m->get_group_member_contribution_statements($group_ids,$member_ids);
        $member_contribution_balances_array = array();
        $member_cumulative_balances_array = array();
        $date = strtotime('01-12-2000');

        if($this->transactions->update_group_member_fine_statement_balances($group_ids,$member_ids,$date)){
            echo "Success";
        }
    }

    function get_contribution_balances(){
        $this->output->enable_profiler(TRUE);
        $group_member_total_cumulative_contribution_paid_per_member_array = $this->statements_m->get_group_member_total_cumulative_contribution_paid_per_member_array(4);
        $group_member_total_cumulative_contribution_arrears_per_member_array = $this->statements_m->get_group_member_total_cumulative_contribution_arrears_per_member_array(4);
        print_r($group_member_total_cumulative_contribution_paid_per_member_array)."<br/>";
        print_r($group_member_total_cumulative_contribution_arrears_per_member_array);
    }

    function get_fine_balances(){
        $this->output->enable_profiler(TRUE);
        $group_member_total_cumulative_fine_paid_per_member_array = $this->statements_m->get_group_member_total_cumulative_fine_paid_per_member_array(4);
        $group_member_total_cumulative_fine_arrears_per_member_array = $this->statements_m->get_group_member_total_cumulative_fine_arrears_per_member_array(4);
        print_r($group_member_total_cumulative_fine_paid_per_member_array)."<br/>";
        print_r($group_member_total_cumulative_fine_arrears_per_member_array);
    }

    function clean_statements_table(){
        // $orphan_deposit_statement_entries = $this->statements_m->get_orphan_deposit_statement_entries();
        // print_r($orphan_deposit_statement_entries);
        // $orphan_invoice_statement_entries = $this->statements_m->get_orphan_invoice_statement_entries();
        // print_r($orphan_invoice_statement_entries);
        // $orphan_contribution_refund_statement_entries = $this->statements_m->get_orphan_contribution_refund_statement_entries();
        // print_r($orphan_contribution_refund_statement_entries);
        // $group_ids = array();
        // $member_ids = array();
        // foreach($orphan_deposit_statement_entries as $statement_entry):
        //     $input = array(
        //         'active' => 0,
        //         'modified_on' => time(),
        //     );
        //     $this->statements_m->update($statement_entry->id,$input);
        //     if(in_array($statement_entry->group_id,$group_ids)){

        //     }else{
        //         $group_ids[] = $statement_entry->group_id;
        //     }
        //     if(in_array($statement_entry->member_id,$member_ids)){

        //     }else{
        //         $member_ids[] = $statement_entry->member_id;
        //     }
        // endforeach;
        // foreach($orphan_invoice_statement_entries as $statement_entry):
        //     $input = array(
        //         'active' => 0,
        //         'modified_on' => time(),
        //     );
        //     $this->statements_m->update($statement_entry->id,$input);
        //     if(in_array($statement_entry->group_id,$group_ids)){

        //     }else{
        //         $group_ids[] = $statement_entry->group_id;
        //     }
        //     if(in_array($statement_entry->member_id,$member_ids)){

        //     }else{
        //         $member_ids[] = $statement_entry->member_id;
        //     }
        // endforeach;
        // foreach($orphan_contribution_refund_statement_entries as $statement_entry):
        //     $input = array(
        //         'active' => 0,
        //         'modified_on' => time(),
        //     );
        //     $this->statements_m->update($statement_entry->id,$input);
        //     if(in_array($statement_entry->group_id,$group_ids)){

        //     }else{
        //         $group_ids[] = $statement_entry->group_id;
        //     }
        //     if(in_array($statement_entry->member_id,$member_ids)){

        //     }else{
        //         $member_ids[] = $statement_entry->member_id;
        //     }
        // endforeach;
        // $this->transactions->update_group_member_contribution_statement_balances($group_ids,$member_ids);

        // $deposits = $this->deposits_m->get_voided_group_deposits(5006);
        // print_r($deposits);

        // foreach($deposits as $deposit):
        //     $this->transactions->void_contribution_payment($deposit->id,$deposit);
        // endforeach;
        $group_ids = array(4);
        $member_ids = array(22,23,24,25,26);
        $cumulative_balance_array =  $cumulative_balance_array = $this->statements_m->get_cumulative_balances_array($group_ids,$member_ids);
        print_r($cumulative_balance_array);

    }



    function count_statements(){
        echo $this->statements_m->count_statements();
    }
    
    function get_member_contributions_details($member_id = 0 ,$group_id = 0){
        $balances = array();
        $member_id OR die('provide member id');
        $total = 0;
        $total_contribution = 0;
        $statements = $this->statements_m->get_member_test_contribution_statement($member_id,$group_id);
        foreach ($statements as $key => $value) {
            $balances[] = $value->contribution_balance;
            $amounts[] = $value->amount;
            $total += $value->amount;
        }
        /*print_r($balances);
        print_r($amounts);
        print_r($total);
        print_r($statements);*/
        $contribution_options = $this->contributions_m->get_group_contribution_options($group_id);

        $total_contribution_arrears_per_contribution_per_member_array = $this->statements_m->get_group_member_total_contribution_arrears_per_contribution_per_member_array($group_id);
        $total_contribution_paid_per_contribution_per_member_array = $this->statements_m->get_group_member_total_contribution_paid_per_contribution_per_member_array($group_id);

        $member_array = $this->statements_m->get_group_member_total_cumulative_contribution_paid_per_member_array_tests($group_id);

        print_r($member_array);
        /*foreach ($contribution_options as $contribution_id => $contribution_name){
           $amount_paid = $total_contribution_paid_per_contribution_per_member_array[$contribution_id][16970];
           echo $amount_paid .'<br>';
           $total_contribution += $amount_paid;
        }
        print_r($total_contribution);*/
    }

    function test_contribution_balances_function($group_id = 0){
        $group_ids = array($group_id);
        $member_options = $this->members_m->get_group_member_options($group_id);
        $member_ids = array_flip($member_options);
        $contribution_options = $this->contributions_m->get_group_contribution_options($group_id);
        $contribution_ids = array_flip($contribution_options);
        $contribution_balance_array = $this->statements_m->get_contribution_balances_array($group_ids,$member_ids,$contribution_ids);
        print_r($contribution_balance_array);
    }

    function count_voided_statements(){
        echo $this->statements_m->count_voided_statements();
    }

    function delete_voided_statements(){
        echo $this->statements_m->delete_voided_statements();
    }

    function find_duplicate_contribution_deposit_statement_entries($group_id = 0){
        $statements = $this->statements_m->find_duplicate_contribution_deposit_statement_entries($group_id);

        $group_ids = array();
        $member_ids = array();
        $transaction_types = array();
        $contribution_ids = array();
        $transaction_dates = array();
        $created_ons = array();

        foreach ($statements as $statement) {
            $group_ids[] = $statement->group_id;
            $member_ids[] = $statement->member_id;
            $transaction_types[] = $statement->transaction_type;
            $contribution_ids[] = $statement->contribution_id;
            $transaction_dates[] = $statement->transaction_date;
            $created_ons[] = $statement->created_on;
        }

        $statements = $this->statements_m->get_duplicate_contribution_fine_statements($group_ids,$member_ids,$transaction_types,$contribution_ids,$transaction_dates,$created_ons);
        // print_r($statements);

        $statement_ids = array();

        $statments_array = array();

        foreach($statements as $statement):
            if(isset($statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->contribution_id][$statement->transaction_date])){
                $statement_ids[] = $statement->id;
            }else{
                $statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->contribution_id][$statement->transaction_date] = 1;
            }
        endforeach;
        // print_r($statement_ids); die;

        if($this->statements_m->delete_where_in($statement_ids,$group_id)){
            echo "Success";
        }
    }

    function find_duplicate_contribution_statement_entries($group_id = 0){
        $statements = $this->statements_m->find_duplicate_contribution_fine_statement_entries($group_id);
        $group_ids = array();
        $member_ids = array();
        $transaction_types = array();
        $contribution_ids = array();
        $transaction_dates = array();
        $created_ons = array();

        foreach ($statements as $statement) {
            $group_ids[] = $statement->group_id;
            $member_ids[] = $statement->member_id;
            $transaction_types[] = $statement->transaction_type;
            $contribution_ids[] = $statement->contribution_id;
            $transaction_dates[] = $statement->transaction_date;
            $created_ons[] = $statement->created_on;
        }

        $statements = $this->statements_m->get_duplicate_contribution_fine_statements($group_ids,$member_ids,$transaction_types,$contribution_ids,$transaction_dates,$created_ons);

        $statement_ids = array();

        $statements_array = array();

        foreach($statements as $statement):
            if(isset($statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->contribution_id][$statement->transaction_date])){
                $statement_ids[] = $statement->id;
            }else{
                $statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->contribution_id][$statement->transaction_date] = 1;
            }
        endforeach;

        if($this->statements_m->delete_where_in($statement_ids,$group_id)){
            echo "Success";
        }

    }

    function find_duplicate_group_member_contribution_statement_entries($group_id=0,$member_id=0){
        $statements = $this->statements_m->find_duplicate_group_member_contribution_fine_statement_entries($group_id,$member_id);
        $group_ids = array();
        $member_ids = array();
        $transaction_types = array();
        $contribution_ids = array();
        $transaction_dates = array();
        $created_ons = array();

        foreach ($statements as $statement) {
            $group_ids[] = $statement->group_id;
            $member_ids[] = $statement->member_id;
            $transaction_types[] = $statement->transaction_type;
            $contribution_ids[] = $statement->contribution_id;
            $transaction_dates[] = $statement->transaction_date;
            $created_ons[] = $statement->created_on;
        }

        $statements = $this->statements_m->get_duplicate_contribution_fine_statements($group_ids,$member_ids,$transaction_types,$contribution_ids,$transaction_dates,$created_ons);

        $statement_ids = array();

        $statments_array = array();

        foreach($statements as $statement):
            if(isset($statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->contribution_id][$statement->transaction_date])){
                $statement_ids[] = $statement->id;
            }else{
                $statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->contribution_id][$statement->transaction_date] = 1;
            }
        endforeach;

        if($this->statements_m->delete_where_in($statement_ids,$group_id)){
            echo "Success";
        }
    }

    function find_duplicate_fine_statement_entries($group_id = 0){

        $statements = $this->statements_m->find_duplicate_fine_statement_entries($group_id);
        $group_ids = array();
        $member_ids = array();
        $transaction_types = array();
        $fine_ids = array();
        $transaction_dates = array();
        $created_ons = array();

        foreach ($statements as $statement) {
            $group_ids[] = $statement->group_id;
            $member_ids[] = $statement->member_id;
            $transaction_types[] = $statement->transaction_type;
            $fine_ids[] = $statement->fine_id;
            $transaction_dates[] = $statement->transaction_date;
            $created_ons[] = $statement->created_on;
        }

        $statements = $this->statements_m->get_duplicate_fine_statements($group_ids,$member_ids,$transaction_types,$fine_ids,$transaction_dates,$created_ons);

        $statement_ids = array();

        $statments_array = array();

        foreach($statements as $statement):
            if(isset($statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->fine_id][$statement->transaction_date])){
                $statement_ids[] = $statement->id;
            }else{
                $statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->fine_id][$statement->transaction_date] = 1;
            }
        endforeach;

        if($this->statements_m->delete_where_in($statement_ids)){
            echo "Success";
        }

    }

    function find_duplicate_group_member_fine_statement_entries($group_id=0,$member_id=0){
        $statements = $this->statements_m->find_duplicate_group_member_fine_statement_entries($group_id,$member_id);
        $group_ids = array();
        $member_ids = array();
        $transaction_types = array();
        $fine_ids = array();
        $transaction_dates = array();
        $created_ons = array();

        foreach ($statements as $statement) {
            $group_ids[] = $statement->group_id;
            $member_ids[] = $statement->member_id;
            $transaction_types[] = $statement->transaction_type;
            $fine_ids[] = $statement->fine_id;
            $transaction_dates[] = $statement->transaction_date;
            $created_ons[] = $statement->created_on;
        }

        $statements = $this->statements_m->get_duplicate_fine_statements($group_ids,$member_ids,$transaction_types,$fine_ids,$transaction_dates,$created_ons);

        $statement_ids = array();

        $statements_array = array();

        foreach($statements as $statement):
            if(isset($statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->fine_id][$statement->transaction_date])){
                $statement_ids[] = $statement->id;
            }else{
                $statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->fine_id][$statement->transaction_date] = 1;
            }
        endforeach;

        if($this->statements_m->delete_where_in($statement_ids)){
            echo "Success";
        }
    }

    function find_duplicate_deposit_statement_entries($group_id = 0){
        $statements = $this->statements_m->find_duplicate_deposit_statement_entries($group_id);
        $group_ids = array();
        $member_ids = array();
        $transaction_types = array();
        $deposit_ids = array();
        $transaction_dates = array();
        $created_ons = array();

        foreach ($statements as $statement) {
            $group_ids[] = $statement->group_id;
            $member_ids[] = $statement->member_id;
            $transaction_types[] = $statement->transaction_type;
            $deposit_ids[] = $statement->deposit_id;
            $transaction_dates[] = $statement->transaction_date;
            $created_ons[] = $statement->created_on;
        }

        $statements = $this->statements_m->get_duplicate_deposit_statements($group_ids,$member_ids,$transaction_types,$deposit_ids,$transaction_dates,$created_ons);
        $statement_ids = array();
        $statements_array = array();
        foreach($statements as $statement):
            if(isset($statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->deposit_id][$statement->transaction_date])){            
                $statement_ids[] = $statement->id;
            }else{
                $statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->deposit_id][$statement->transaction_date] = 1;
            }
        endforeach;
        $chunked_statement_ids = array_chunk($statement_ids,2000);

        foreach($chunked_statement_ids  as $statement_ids):
            if($rows = $this->statements_m->delete_where_in($statement_ids,$group_id)){
                echo $rows." rows deleted. <br/>";
            }
        endforeach;

    }

    function find_duplicate_share_transfer_entries($group_id = 0){

        $statements = $this->statements_m->find_duplicate_share_transfer_statement_entries($group_id);

        $group_ids = array();
        $member_ids = array();
        $transaction_types = array();
        $deposit_ids = array();
        $transaction_dates = array();
        $created_ons = array();
        $contribution_from_ids = array();
        $contribution_to_ids = array();

        foreach ($statements as $statement) {
            $group_ids[] = $statement->group_id;
            $member_ids[] = $statement->member_id;
            $transaction_types[] = $statement->transaction_type;
            $deposit_ids[] = $statement->deposit_id;
            $transaction_dates[] = $statement->transaction_date;
            $created_ons[] = $statement->created_on;
            $contribution_from_ids[] = $statement->contribution_from_id;
            $contribution_to_ids[] = $statement->contribution_to_id;
        }

        $statements = $this->statements_m->get_duplicate_contribution_transfer_statements($group_ids,$member_ids,$transaction_types,$deposit_ids,$transaction_dates,$contribution_from_ids,$contribution_to_ids);

        // echo count($statements);
        // die;

        $statement_ids = array();

        $statments_array = array();

        foreach($statements as $statement):
            if(isset($statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->transaction_date][$statement->contribution_from_id][$statement->contribution_to_id])){
                $statement_ids[] = $statement->id;
            }else{
                $statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->transaction_date][$statement->contribution_from_id][$statement->contribution_to_id] = 1;
            }
        endforeach;

         $chunked_statement_ids = array_chunk($statement_ids,2000);

        foreach($chunked_statement_ids  as $statement_ids):
            if($rows = $this->statements_m->delete_where_in($statement_ids,$group_id)){
                echo $rows." rows deleted. <br/>";
            }
        endforeach;

    }

    function find_duplicate_share_to_fine_transfer_entries($group_id = 0){

        $statements = $this->statements_m->find_duplicate_share_to_fine_transfer_statement_entries($group_id);

        $group_ids = array();
        $member_ids = array();
        $transaction_types = array();
        $deposit_ids = array();
        $transaction_dates = array();
        $created_ons = array();
        $contribution_from_ids = array();
        $fine_category_to_ids = array();

        foreach ($statements as $statement) {
            $group_ids[] = $statement->group_id;
            $member_ids[] = $statement->member_id;
            $transaction_types[] = $statement->transaction_type;
            $deposit_ids[] = $statement->deposit_id;
            $transaction_dates[] = $statement->transaction_date;
            $created_ons[] = $statement->created_on;
            $contribution_from_ids[] = $statement->contribution_from_id;
            $fine_category_to_ids[] = $statement->fine_category_to_id;
        }

        $statements = $this->statements_m->get_duplicate_contribution_transfer_to_fine_statements($group_ids,$member_ids,$transaction_types,$deposit_ids,$transaction_dates,$contribution_from_ids,$fine_category_to_ids);

        // echo count($statements);
        // die;

        $statement_ids = array();

        $statments_array = array();

        foreach($statements as $statement):
            if(isset($statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->transaction_date][$statement->contribution_from_id][$statement->fine_category_to_id])){
                $statement_ids[] = $statement->id;
            }else{
                $statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->transaction_date][$statement->contribution_from_id][$statement->fine_category_to_id] = 1;
            }
        endforeach;

         $chunked_statement_ids = array_chunk($statement_ids,2000);

        foreach($chunked_statement_ids  as $statement_ids):
            if($rows = $this->statements_m->delete_where_in($statement_ids,$group_id)){
                echo $rows." rows deleted. <br/>";
            }
        endforeach;

    }

    function find_duplicate_contribution_refund_entries($group_id = 0){
        $statements = $this->statements_m->find_duplicate_contribution_refund_statement_entries($group_id);

        $group_ids = array();
        $member_ids = array();
        $transaction_types = array();
        $deposit_ids = array();
        $transaction_dates = array();
        $created_ons = array();
        $refund_ids = array();

        foreach ($statements as $statement) {
            $group_ids[] = $statement->group_id;
            $member_ids[] = $statement->member_id;
            $transaction_types[] = $statement->transaction_type;
            $deposit_ids[] = $statement->deposit_id;
            $transaction_dates[] = $statement->transaction_date;
            $created_ons[] = $statement->created_on;
            $contribution_from_ids[] = $statement->contribution_from_id;
            $contribution_to_ids[] = $statement->contribution_to_id;
            $refund_ids[] = $statement->refund_id;
        }

        $statements = $this->statements_m->get_duplicate_contribution_refund_statements($group_ids,$member_ids,$transaction_types,$transaction_dates,$refund_ids);
        
        $statement_ids = array();

        $statments_array = array();

        foreach($statements as $statement):
            if(isset($statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->transaction_date][$statement->refund_id])){
                $statement_ids[] = $statement->id;
            }else{
                $statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->transaction_date][$statement->refund_id] = 1;
            }
        endforeach;

        $chunked_statement_ids = array_chunk($statement_ids,2000);

        foreach($chunked_statement_ids  as $statement_ids):
            if($rows = $this->statements_m->delete_where_in($statement_ids,$group_id)){
                echo $rows." rows deleted. <br/>";
            }
        endforeach;
    }

    function find_duplicate_dividend_payout_entries($group_id=0){
        $statements = $this->statements_m->find_duplicate_dividend_payout_statement_entries($group_id);
        $group_ids = array();
        $member_ids = array();
        $transaction_types = array();
        $withdrawal_ids = array();
        $transaction_dates = array();
        $created_ons = array();
        
        foreach ($statements as $statement) {
            $group_ids[] = $statement->group_id;
            $member_ids[] = $statement->member_id;
            $transaction_types[] = $statement->transaction_type;
            $withdrawal_ids[] = $statement->withdrawal_id;
            $transaction_dates[] = $statement->transaction_date;
            $created_ons[] = $statement->created_on;
        }

        $statements = $this->statements_m->get_duplicate_dividend_payout_statements($group_ids,$member_ids,$transaction_types,$transaction_dates,$withdrawal_ids);        
        $statement_ids = array();
        $statments_array = array();

        foreach($statements as $statement):
            if(isset($statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->transaction_date][$statement->withdrawal_id])){
                $statement_ids[] = $statement->id;
            }else{
                $statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->transaction_date][$statement->withdrawal_id] = 1;
            }
        endforeach;

        $chunked_statement_ids = array_chunk($statement_ids,2000);

        foreach($chunked_statement_ids  as $statement_ids):
            if($rows = $this->statements_m->delete_where_in($statement_ids,$group_id)){
                echo $rows." rows deleted. <br/>";
            }
        endforeach;
    }

    function remove_duplicate_statements_entries(){
        $this->output->enable_profiler(TRUE);
        $group = $this->groups_m->get_group_to_remove_duplicates();
        if($group){
            $this->fix_group_duplicate_statement_entries($group->id);
            $input = array(
                'remove_duplicates'=>1,
                'modified_on'=>time()
            );
            $this->groups_m->update($group->id,$input);
        }else{
           echo "No group data"; 
        }
    }

    function mark_group_as_removed_duplicates($group_id =0){
        $group = $this->groups_m->get_group_to_remove_duplicates($group_id);
        if($group){
            //$this->fix_group_duplicate_statement_entries($group->id);
            $input = array(
                'remove_duplicates'=>1,
                'modified_on'=>time()
            );
            $this->groups_m->update($group->id,$input);
        }else{
           echo "No group data"; 
        }
    }

    function unmark_remove_duplicates(){
        $this->groups_m->unmark_remove_duplicate_entries();
    }

    function fix_group_duplicate_statement_entries($group_id = 0){
        $this->find_duplicate_contribution_statement_entries($group_id);
        $this->find_duplicate_fine_statement_entries($group_id);
        $this->find_duplicate_deposit_statement_entries($group_id);
        $this->find_duplicate_share_transfer_entries($group_id);
        $this->find_duplicate_contribution_refund_entries($group_id);
        $this->find_duplicate_share_to_fine_transfer_entries($group_id);
        $this->find_duplicate_dividend_payout_entries($group_id);
        $this->reconcile_group_member_contribution_statements($group_id);
        $this->reconcile_group_member_fine_statements($group_id);
    }

     function fix_group_member_duplicate_statement_entries($group_id = 0,$member_id = 0){
        $this->find_duplicate_group_member_contribution_statement_entries($group_id,$member_id);
        $this->find_duplicate_group_member_fine_statement_entries($group_id,$member_id);
        /*$this->find_duplicate_deposit_statement_entries($group_id);
        $this->find_duplicate_share_transfer_entries($group_id);
        $this->find_duplicate_contribution_refund_entries($group_id);
        $this->find_duplicate_share_to_fine_transfer_entries($group_id);*/
        $this->reconcile_group_member_contribution_statements($group_id);
    }


    function get_statements_modified($limit=0,$group_id=0){
        //echo $this->statements_m->count_all_statements_updated_yesterday($group_id);die;
        $statements = $this->statements_m->get_all_statements_updated_yesterday($limit,$group_id);
        //print_r($statements);die;
        $item = 0;
        $total = count($statements);
        foreach ($statements as $statement) {
            if($statement->deposit_id){
                if($ans = $this->deposits_m->is_active_deposit($statement->deposit_id)){
                    $count = $this->statements_m->is_distinct_deposit($statement->deposit_id,$statement->group_id);
                    if($count == 0){
                        ++$item;
                        $this->statements_m->update($statement->id,array('active'=>1));
                    }
                }
            }elseif ($statement->invoice_id) {
                if($ans = $this->invoices_m->is_active_invoice($statement->invoice_id)){
                    $count2 = $this->statements_m->is_distinct_invoice($statement->invoice_id,$statement->group_id);
                    if($count2 == 0){
                        ++$item;
                        $this->statements_m->update($statement->id,array('active'=>1));
                    }
                }
            }
        }
        echo 'out and done for '.$item.' out of '.$total;
        if($item){
            $this->reconcile_group_member_contribution_statements($group_id);
        }
    }

    function void_orphan_statements($group_id = 0){
        $statements = $this->statements_m->get_orphan_deposit_statement_entries();
        echo count($statements).' Statements';
        foreach($statements as $statement):
            $input = array(
                'active' => 0
            );
            $this->statements_m->update($statement->id,$input);
        endforeach;
    }

    function fix_fine_statements_with_duplicates($group_id = 0){
        if($group_id){
            $fine_statements = $this->statements_m->get_group_fine_statements_array($group_id);
            if($fine_statements){
                $invoice_ids = array();
                foreach ($fine_statements as $key => $fine_statement):
                    $invoice_ids[] = $fine_statement->invoice_id;
                endforeach;
                if($invoice_ids){
                    $active_invoice_ids = $this->invoices_m->check_if_invoice_exist_array($group_id,$invoice_ids);
                    if($active_invoice_ids){
                        $flip_invoice_ids = array_flip(array_filter($invoice_ids));
                        foreach ($active_invoice_ids as $key => $invoice_id):
                           unset($flip_invoice_ids[$invoice_id]);
                        endforeach;
                        if($flip_invoice_ids){
                            $this->statements_m->void_fine_statements_by_invoice_ids_array($group_id,array_flip($flip_invoice_ids));
                            echo count($flip_invoice_ids)." Successfully voided ";
                        }
                    }
                }
            }else{
                echo "Fine invoice not found ";
            }
        }else{
           echo "group id required"; 
        }
    }

    function void_group_statements($group_id = 0){
        //echo $this->statements_m->void_group_statements($group_id);
    }

    function update_contributions(){
        $this->contributions_m->update_contributions_from_null();
    }

    function fix_remove_duplicates_fields(){
        $transaction_alerts = $this->groups_m->fix_remove_duplicates_fields();
    }

    function fix_group_paginated_duplicate_fine_statements(){
        $this->output->enable_profiler(TRUE);        
        $group = $this->groups_m->get_group_to_remove_duplicates();
        if($group){
           // $this->fix_group_duplicate_statement_entries($group->id);
            //$total_rows = $this->statements_m->count_duplicate_contribution_fine_statement_entries(4295);
            //$pagination = create_pagination('', $total_rows,1000,5,TRUE);
            //$pagination = create_custom_pagination('statement',$total_rows,1000,0,TRUE);
            $statements = $this->statements_m->find_duplicate_contribution_fine_statement_entries_with_limit(4295);
            if($statements){
                //$statements = $this->statements_m->find_duplicate_fine_statement_entries($group_id);
                $group_ids = array();
                $member_ids = array();
                $transaction_types = array();
                $fine_ids = array();
                $transaction_dates = array();
                $created_ons = array();

                foreach ($statements as $statement) {
                    $group_ids[] = $statement->group_id;
                    $member_ids[] = $statement->member_id;
                    $transaction_types[] = $statement->transaction_type;
                    $fine_ids[] = $statement->fine_id;
                    $transaction_dates[] = $statement->transaction_date;
                    $created_ons[] = $statement->created_on;
                }

                $statements = $this->statements_m->get_duplicate_fine_statements($group_ids,$member_ids,$transaction_types,$fine_ids,$transaction_dates,$created_ons);

                $statement_ids = array();

                $statments_array = array();

                foreach($statements as $statement):
                    if(isset($statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->fine_id][$statement->transaction_date])){
                        $statement_ids[] = $statement->id;
                    }else{
                        $statements_array[$statement->group_id][$statement->member_id][$statement->transaction_type][$statement->fine_id][$statement->transaction_date] = 1;
                    }
                endforeach;

                if($this->statements_m->delete_where_in($statement_ids)){
                    echo "Success";
                }
                /*$input = array(
                    'remove_duplicates'=>1,
                    'modified_on'=>time()
                );
                $this->groups_m->update($group->id,$input);*/
            }else{

            }
        }else{
           echo "No group data"; 
        }
    }

    function fix_fine_duplicates_from_a_certain_date(){
        die();
        $group_id = 4295 ;
        $invoices = $this->invoices_m->get_group_fine_invoices_to_void("",$group_id); 
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
                    echo 'success',$number_voided.' Contribution invoice successfully voided.';
                }
            }
            if($contribution_fine_invoice_ids){
                if($this->transactions->void_bulk_fine_invoice($group_id,$contribution_fine_invoice_ids)){
                    echo 'success',$number_voided.' Contribution fine invoice successfully  voided.';
                   // $this->session->set_flashdata('success',$number_voided.' Contribution fine invoice successfully  voided.');
                } 
            }
            if($fine_invoice_ids){
                if($this->transactions->void_bulk_fine_invoice($group_id,$fine_invoice_ids)){
                    echo 'success',$number_voided.' Fine invoice successfully  voided.';
                } 
            }

            if($miscellaneous_invoice_ids){
                if($this->transactions->void_bulk_miscellaneous_invoice($group_id,$miscellaneous_invoice_ids)){
                    echo 'success',$number_voided.' Fine invoice successfully  voided.';
                } 
            }
        }else{
             echo 'Could not find invoices';  
        } 
    }

    function delete_group_fine_statements(){
        $group_id = 4295;
        $statements = $this->statements_m->get_group_fine_statements_array_to_delete($group_id); 
        $group_ids = array();
        $member_ids = array();
        $transaction_types = array();
        $fine_ids = array();
        $transaction_dates = array();
        $created_ons = array();
        $statement_ids = array();
        $statments_array = array();

        foreach($statements as $statement):
            $statement_ids[] = $statement->id;
        endforeach;

        if($this->statements_m->delete_where_in($statement_ids)){
            echo "Success";
        }
    }
    
}
