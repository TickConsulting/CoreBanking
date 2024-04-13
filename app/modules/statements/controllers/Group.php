<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{

	function __construct(){
        parent::__construct();
        $this->load->model('statements_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('members/members_m');
        $this->load->model('contributions/contributions_m');
        $this->load->model('fine_categories/fine_categories_m');
        $this->load->library('transactions');
        $this->load->library('pdf_library');
    }
   
    public function index(){
        $data = array();
        $this->template->title('Group Statements')->build('group/index',$data);
    }

    function listing(){
        $data = array();    
        $this->template->title(translate('List Statements'))->build('group/listing',$data);
    }

    function send_email_statements(){
        $members = $this->input->get_post('members');
        $success = 0;
        $fails = 0;
        if($members){
            foreach ($members as $member_id) {
                $member = $this->members_m->get_group_member($member_id,$this->group->id);
                if($member){
                    $email = $member->email;
                    if($email){
                        $input = array(
                            'user_id' => $member->user_id,
                            'member_id' => $member->id,
                            'group_id' => $this->group->id,
                            'statement_file_type' => 1,
                            'type'  => 1,
                            'date_from' => strtotime('-3 months'),
                            'date_to' => time(),
                            'created_on' => time(),
                            'created_by' => $this->user->id,
                            'active' => 1,
                            'action' => 1,
                            'email' => $email,
                        );
                        if($this->statements_m->insert_statement_request($input)){
                            ++$success;
                        }else{
                            ++$fails;
                        }
                        $input = array(
                            'user_id' => $member->user_id,
                            'member_id' => $member->id,
                            'group_id' => $this->group->id,
                            'statement_file_type' => 1,
                            'type'  => 2,
                            'date_from' => strtotime('-3 months'),
                            'date_to' => time(),
                            'created_on' => time(),
                            'created_by' => $this->user->id,
                            'active' => 1,
                            'action' => 1,
                            'email' => $email,
                        );
                        if($this->statements_m->insert_statement_request($input)){
                            ++$success;
                        }else{
                            ++$fails;
                        }
                    }else{
                        ++$fails;
                    }
                }
            }
        }else{
            $members = $this->active_group_member_options;
            foreach ($members as $member_id=>$member_name) {
                $member = $this->members_m->get_group_member($member_id,$this->group->id);
                if($member){
                    $email = $member->email;
                    if($email){
                        $input = array(
                            'user_id' => $member->user_id,
                            'member_id' => $member->id,
                            'group_id' => $this->group->id,
                            'statement_file_type' => 1,
                            'type'  => 1,
                            'date_from' => strtotime('-1 years'),
                            'date_to' => time(),
                            'created_on' => time(),
                            'created_by' => $this->user->id,
                            'active' => 1,
                            'action' => 1,
                            'email' => $email,
                        );
                        if($this->statements_m->insert_statement_request($input)){
                            ++$success;
                        }else{
                            ++$fails;
                        }
                        $input = array(
                            'user_id' => $member->user_id,
                            'member_id' => $member->id,
                            'group_id' => $this->group->id,
                            'statement_file_type' => 1,
                            'type'  => 2,
                            'date_from' => strtotime('-1 years'),
                            'date_to' => time(),
                            'created_on' => time(),
                            'created_by' => $this->user->id,
                            'active' => 1,
                            'action' => 1,
                            'email' => $email,
                        );
                        if($this->statements_m->insert_statement_request($input)){
                            $success++;
                        }else{
                            $fails++;
                        }
                    }else{
                        $fails++;
                    }
                }
                
            }
        }
        if($success){
            $this->session->set_flashdata('success','Email statement(s) queued for '.round($success/2).' members');
        }
        if($fails){
            $this->session->set_flashdata('info','Email statements for '.$fails.' members were not queued.');
        }
        redirect($this->agent->referrer());
    }

    private function _get_next_group_member($current_member_id = 0) {
        $next_member_id = 0;
        $found_next_member_id = FALSE;
        $count = 1;
        foreach ($this->active_group_member_options as $member_id => $member_name) {
            if($count==1){
                $next_member_id = $member_id;
            }
            if ($found_next_member_id) {
                # code...
                return $member_id;
            }
            # code...
            if($current_member_id==$member_id){
                $found_next_member_id = TRUE;
            }
            $count++;
        }
        return $next_member_id;
    }

    function view($id = 0,$generate_pdf=FALSE){
        if($this->group->disable_arrears){
            redirect('group/statements/deposit_statement/'.$id);
        }
        $id OR redirect('group/members');
        $member = $this->members_m->get_group_member($id);
        $member OR redirect('group/members');
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime(date('d-m-Y',strtotime('-3 months')));
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime(date('d-m-Y',strtotime('tomorrow')));
        //$open_contribution_options = $this->contributions_m->get_group_open_contribution_options();
        $open_contribution_options = $this->contributions_m->get_group_contribution_display_options($this->group->id,TRUE);
        $contribution_ids = $this->input->get('contributions');
        $count = 1;
        $contribution_id_list = '';
        if(is_array($contribution_ids) && !empty($contribution_ids)){
            foreach ($contribution_ids as $contribution_id) {
                if($contribution_id){
                    if($count==1){
                        $contribution_id_list.=$contribution_id;
                    }else{
                        $contribution_id_list.=','.$contribution_id;
                    }
                    $count++;
                }
            }
        }

        if(!$contribution_id_list)
            if($open_contribution_options)
                $contribution_id_list = implode(',',array_keys($open_contribution_options));


        $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
        $data['next_group_member_id'] = $this->_get_next_group_member($member->id);
        $data['contribution_ids'] = $contribution_ids;
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
        $data['group_currency'] = $this->group_currency;
        $data['application_settings'] = $this->application_settings;
        $data['display'] = '';
        $data['show_next_member'] = TRUE;
        // $data['group_member_options'] = $this
       
        if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
            $data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
        }else{
            $data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
        }
        if($generate_pdf==TRUE){
            $data['pdf_true'] = TRUE;
            $html = $this->load->view('shared/view',$data,TRUE);
            $this->pdf_library->generate_landscape_report($html);
            die;
        }else{
            $this->template->title('View Contribution Statement',$member->first_name.' '.$member->last_name)->build('shared/view',$data); 
        }
    }

    function fine_statement($id = 0,$generate_pdf=FALSE){
        $id OR redirect('group/members');
        $post = $this->members_m->get_group_member($id);
        $post OR redirect('group/members');
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-3 months');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $contribution_ids = $this->input->get('contributions');
        $fine_category_ids = $this->input->get('fine_categories');
        $count = 1;
        $contribution_id_list = '';
        if(is_array($contribution_ids)){
            foreach ($contribution_ids as $contribution_id) {
                if($contribution_id){
                    if($count==1){
                        $contribution_id_list.=$contribution_id;
                    }else{
                        $contribution_id_list.=','.$contribution_id;
                    }
                    $count++;
                }
            }
        }
        $count = 1;
        $fine_category_id_list = '';
        if(is_array($fine_category_ids)){
            foreach ($fine_category_ids as $fine_category_id) {
                if($fine_category_id){
                    if($count==1){
                        $fine_category_id_list.=$fine_category_id;
                    }else{
                        $fine_category_id_list.=','.$fine_category_id;
                    }
                    $count++;
                }
            }
        }
        $data['contribution_ids'] = $contribution_ids;
        $data['fine_category_ids'] = $fine_category_ids;
        $data['from'] = $from;
        $data['to'] = $to;
        $data['contribution_fine_options'] = $this->contributions_m->get_group_contribution_fine_options();
        $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
        $data['member'] = $post;
        $data['balance'] = $this->statements_m->get_member_fine_balance($this->group->id,$post->id,$contribution_id_list,$fine_category_id_list,$from);
        $data['posts'] = $this->statements_m->get_member_fine_statement($post->id,$contribution_id_list,$fine_category_id_list,$from,$to);
        $data['statement_transaction_names'] = $this->transactions->statement_transaction_names;
        $data['next_group_member_id'] = $this->_get_next_group_member($post->id);
        $data['group'] = $this->group;
        $data['group_currency'] = $this->group_currency;
        $data['application_settings'] = $this->application_settings;
        if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
            $data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
        }else{
            $data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
        }

        if($generate_pdf==TRUE){
            $data['pdf_true'] = TRUE;
            $html = $this->load->view('shared/fine_statement',$data,TRUE);
            $this->pdf_library->generate_landscape_report($html);
            die;

            /*$response = $this->curl_post_data->curl_post_json_pdf((json_encode($data)),'https://pdfs.chamasoft.com/fine_statement',$post->first_name.' Fine Statement - '.$this->group->name);
            print_r($response);die;*/
        }else{
            $this->template->title('View Contribution Fine Statement',$post->first_name.' '.$post->last_name)->build('shared/fine_statement',$data);
        }
    }

    function miscellaneous_statement($id = 0 , $generate_pdf=FALSE){
        $id OR redirect('group/members');
        $post = $this->members_m->get_group_member($id);
        $post OR redirect('group/members');
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-3 months');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $data['from'] = $from;
        $data['to'] = $to;
        $data['member'] = $post;
        $data['balance'] = $this->statements_m->get_member_miscellaneous_balance($this->group->id,$post->id,$from);
        $data['posts'] = $this->statements_m->get_member_miscellaneous_statement($post->id,$from,$to);
        $data['statement_transaction_names'] = $this->transactions->statement_transaction_names;
        $data['next_group_member_id'] = $this->_get_next_group_member($post->id);
        $data['group'] = $this->group;
        $data['group_currency'] = $this->group_currency;
        $data['application_settings'] = $this->application_settings;
        if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
            $data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
        }else{
            $data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
        }
        if($generate_pdf==TRUE){
            $data['pdf_true'] = TRUE;
            $html = $this->load->view('shared/miscellaneous_statement',$data,TRUE);
            $this->pdf_library->generate_landscape_report($html);
            die;
        }else{
            $this->template->title('View Miscellaneous Statement',$post->first_name.' '.$post->last_name)->build('shared/miscellaneous_statement',$data);
        }
    }

    function fix(){
        set_time_limit(0);
        ini_set('memory_limit','1536M');
        $statement_entries = $this->statements_m->get_all();
        $count = 1;
        foreach ($statement_entries as $statement_entry) {
            # code...
            if($statement_entry->invoice_id){
                $invoice = $this->invoices_m->get($statement_entry->invoice_id);
                if($invoice->type==1){
                    $input = array(
                        'contribution_invoice_due_date' => $invoice->due_date,
                        'modified_on' => time()
                    );
                    $this->statements_m->update($statement_entry->id,$input);
                }else if($invoice->type==2||$invoice->type==3){
                    $input = array(
                        'fine_invoice_due_date' => $invoice->due_date,
                        'modified_on' => time()
                    );
                    $this->statements_m->update($statement_entry->id,$input);
                }
                //echo timestamp_to_date($invoice->due_date).'<br/>';
                $count++;
            }
        }
    }

    
    function deposit_statement($id = 0,$generate_pdf=FALSE){
        $id OR redirect('group/members');
        $member = $this->members_m->get_group_member($id);
        $member OR redirect('group/members');
        // redirect('group/statements/view/'.$id);
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-6 months');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $contribution_ids = $this->input->get('contributions')?:array();
        $count = 1;
        $contribution_id_list = '';
        if(is_array($contribution_ids)){
            foreach ($contribution_ids as $contribution_id) {
                if($contribution_id){
                    if($count==1){
                        $contribution_id_list.=$contribution_id;
                    }else{
                        $contribution_id_list.=','.$contribution_id;
                    }
                    $count++;
                }
            }
        }
        $this->data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
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
        if($generate_pdf==TRUE){
            $this->data['pdf_true'] = TRUE;
            $html = $this->load->view('group/deposit_statement',$this->data,TRUE);
            $this->pdf_library->generate_landscape_report($html);
            die;
        }else{
            $view = $this->template->title(translate('View Contribution Statement'),$member->first_name.' '.$member->last_name)->build('group/deposit_statement',$this->data);
        }
    }

}