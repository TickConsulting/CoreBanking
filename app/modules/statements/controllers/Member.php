<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends Member_Controller{

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

    function index($generate_pdf=FALSE){
        if($this->group->disable_arrears){
            $member = $this->members_m->get_group_member($this->member->id);
            $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-3 months');
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
            $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
            $data['next_group_member_id'] = $this->_get_next_group_member($member->id);
            $data['contribution_ids'] = $contribution_ids;
            $data['from'] = $from;
            $data['to'] = $to;
            
            $contribution_options = $this->contributions_m->get_group_contribution_options($this->group->id,FALSE);
            $data['contribution_options'] = $contribution_options;
            $data['contribution_display_options'] = $this->contributions_m->get_group_contribution_options($this->group->id,TRUE);
            $data['total_member_deposit_amount'] = $this->deposits_m->get_total_group_member_deposit_amount($member->id);
            $data['total_member_contribution_transfers_from_contribution'] = $this->statements_m->get_group_member_total_contribution_transfers_from_contribution($member->id);
            $data['total_member_contribution_transfers_to_contribution'] = $this->statements_m->get_group_member_total_contribution_transfers_to_contribution($member->id);
            $data['total_member_contribution_transfers_to_loan'] = $this->statements_m->get_group_member_total_contribution_transfers_to_loan($member->id);

            $data['total_member_deposit_amount_by_contribution_array'] = $this->deposits_m->get_total_group_member_deposit_amount_by_contribution_array($member->id);
            $data['member_contribution_transfers_to'] = $this->statements_m->get_group_member_contribution_transfers_to_per_contribution_array($member->id);
            $data['member_contribution_transfers_from'] = $this->statements_m->get_group_member_contribution_transfers_from_per_contribution_array($member->id);
            $data['member_contribution_transfers_to_loan'] = $this->statements_m->get_group_member_contribution_transfers_to_loan_per_contribution_array($member->id);
            $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
            $data['member'] = $member;
            $data['opening_balances'] = $this->statements_m->get_member_deposits_opening_balance_by_contribution_array($this->group->id,$member->id,$contribution_id_list,$from);
            $data['amount_payable'] = $this->statements_m->get_member_contribution_amount_payable($this->group->id,$member->id,$contribution_id_list,$from);
            $data['amount_paid'] = $this->statements_m->get_member_contribution_amount_paid($this->group->id,$member->id,$contribution_id_list,$from);
            $data['posts'] = $this->statements_m->get_member_deposit_statement($member->id,$contribution_id_list,$from,$to);
            
            $data['statement_transaction_names'] = $this->transactions->statement_transaction_names;
            $data['group'] = $this->group;
            $data['group_currency'] = $this->group_currency;
            $data['application_settings'] = $this->application_settings;
            if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
                $data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
            }else{
                $data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
            }
            if($generate_pdf==TRUE){
                $response = $this->curl_post_data->curl_post_json_pdf((json_encode($data)),'https://pdfs.chamasoft.com/deposit_statement',$member->first_name.' Contribution Statement - '.$this->group->name);
                print_r($response);die;
            }else{
                $this->template->title(translate('My Contribution Statement'),$member->first_name.' '.$member->last_name)->build('shared/deposit_statement',$data); 
            }
        }else{
            $member = $this->members_m->get_group_member($this->member->id);
            $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-3 months');
            $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
            $contribution_ids = $this->input->get('contributions');
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
            $data['amount_payable'] = $this->statements_m->get_member_contribution_amount_payable($this->group->id,$member->id,$contribution_id_list,$from);
            $data['amount_paid'] = $this->statements_m->get_member_contribution_amount_paid($this->group->id,$member->id,$contribution_id_list,$from);
            $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
            $data['next_group_member_id'] = $this->_get_next_group_member($member->id);
            $data['contribution_ids'] = $contribution_ids;
            $data['from'] = $from;
            $data['to'] = $to;
            $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
            $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
            $data['member'] = $member;
            $data['balance'] = $this->statements_m->get_member_contribution_balance($this->group->id,$member->id,$contribution_id_list,$from);
            $data['posts'] = $this->statements_m->get_member_contribution_statement($member->id,$contribution_id_list,$from,$to);
            $data['statement_transaction_names'] = $this->transactions->statement_transaction_names;
            $this->template->title(translate('My Contribution Statement'),$member->first_name.' '.$member->last_name)->build('shared/view',$data);
        }
    }

    function view($id = 0,$generate_pdf=0){
        if(!$id){$id = $this->member->id;}
        if($this->group->disable_arrears){
            redirect('member/statements/deposit_statement/'.$id);
        }
        if($this->group->enable_member_information_privacy){ 
            if($id == $this->member->id){
            }else{
                $this->session->set_flashdata('error','You are not allowed to view another members statement');
                redirect("member/members/view/".$this->member->id);
            }
        }
        $member = $this->members_m->get_group_member($id);
        $member OR redirect('member/members');
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

        $data['show_next_member'] = FALSE;
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


    function fine_statement($id = 0, $generate_pdf=FALSE){
        if($id){

        }else{
        	$id = $this->member->id;
        }

        if($this->group->enable_member_information_privacy){ 
            if($id == $this->member->id){

            }else{
                redirect("member/members/view/".$this->member->id);
            }
        }
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
        if($generate_pdf==TRUE){
            $data['pdf_true'] = TRUE;
            $html = $this->load->view('shared/fine_statement',$data,TRUE);
            $this->pdf_library->generate_landscape_report($html);
            die;
        }else{
            $this->template->title(translate('My Contribution Fine Statement'),$post->first_name.' '.$post->last_name)->build('shared/fine_statement',$data);
        }       
        
    }

    function contribution_statement($id = 0,$generate_pdf=FALSE){
        if($this->group->disable_arrears){
            redirect('group/statements/deposit_statement/'.$id);
        }
        $id OR redirect('group/members');
        $member = $this->members_m->get_group_member($id);
        $member OR redirect('group/members');
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-3 months');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $contribution_ids = $this->input->get('contributions');
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
        $data['fine_category_options'] = $this->fine_categories_m->get_group_options(FALSE);
        $data['next_group_member_id'] = $this->_get_next_group_member($member->id);
        $data['contribution_ids'] = $contribution_ids;
        $data['from'] = $from;
        $data['to'] = $to;
        $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
        $data['member'] = $member;
        $data['balance'] = $this->statements_m->get_member_contribution_balance($this->group->id,$member->id,$contribution_id_list,$from);
        $data['amount_payable'] = $this->statements_m->get_member_contribution_amount_payable($this->group->id,$member->id,$contribution_id_list,$from);
        $data['amount_paid'] = $this->statements_m->get_member_contribution_amount_paid($this->group->id,$member->id,$contribution_id_list,$from);
        $data['posts'] = $this->statements_m->get_member_contribution_statement($member->id,$contribution_id_list,$from,$to);
        $data['statement_transaction_names'] = $this->transactions->statement_transaction_names;
        $data['group'] = $this->group;
        $data['group_currency'] = $this->group_currency;
        $data['application_settings'] = $this->application_settings;
        if($this->group->id == 6){
            //print_r($data);die;
        }
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
        }else{
            $this->template->title('My Contribution Statement',$member->first_name.' '.$member->last_name)->build('shared/contribution_statement',$data); 
        }
    }

    function miscellaneous_statement($id = 0 , $generate_pdf=FALSE){
        if($id){

        }else{
        	$id = $this->member->id;
        }

        if($this->group->enable_member_information_privacy){ 
            if($id == $this->member->id){

            }else{
                redirect("member/members/view/".$this->member->id);
            }
        }
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
        if($generate_pdf==TRUE){
            $data['pdf_true'] = TRUE;
            $html = $this->load->view('shared/miscellaneous_statement',$data,TRUE);
            $this->pdf_library->generate_landscape_report($html);
            die;
        }else{
            $this->template->title(translate('My Miscellaneous Statement'),$post->first_name.' '.$post->last_name)->build('shared/miscellaneous_statement',$data); 
        }
        
    }


    function deposit_statement($generate_pdf=FALSE){
       if(!$this->group->disable_arrears){
            redirect('group/statements/view/'.$this->member->id);
        }
        $id = $this->member->id;
        $member = $this->member;
        $member OR redirect('group/members');
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-12 months');
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
        $contribution_list = '';
        foreach ($contribution_display_options as $id=>$name) {
            if($contribution_list){
                $contribution_list.=','.$id;
            }else{
                $contribution_list=$id;
            }
        }
        $this->data['contribution_display_options'] = $contribution_display_options;
        $this->data['total_member_deposit_amounts'] = $this->statements_m->get_group_member_total_paid_by_contribution_array($member->id,$this->group->id,$contribution_list);
        $this->data['opening_balances'] = $this->statements_m->get_group_member_total_paid_by_contribution_array($member->id,$this->group->id,$contribution_list,$from);
        $this->data['statement_transaction_names'] = $this->transactions->statement_transaction_names;
        $this->data['group'] = $this->group;
        $this->data['group_currency'] = $this->group_currency;
        $this->data['application_settings'] = $this->application_settings;
        $this->data['member'] = $member;
        $this->data['posts'] = $this->statements_m->get_member_deposit_statement_array($member->id,$contribution_list,$from,$to);    
        if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
            $this->data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
        }else{
            $this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
        }
        if($generate_pdf==TRUE){
            $this->data['pdf_true'] = TRUE;
            $html = $this->load->view('member/deposit_statement',$this->data,TRUE);
            $this->pdf_library->generate_landscape_report($html);
            die;
            $response = $this->curl_post_data->curl_post_json_pdf((json_encode($this->data)),'https://pdfs.chamasoft.com/deposit_statement',$member->first_name.' Contribution Statement - '.$this->group->name);
            print_r($response);
            die;
        }else{
            $this->template->title(translate('View Contribution Statement'),$member->first_name.' '.$member->last_name)->build('member/deposit_statement',$this->data); 
        }
    }

}