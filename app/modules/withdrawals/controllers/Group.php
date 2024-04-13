<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Group extends Group_Controller{
    private $data = array();
	function __construct(){
        parent::__construct();
        $this->load->model('withdrawals_m');
        $this->load->model('expense_categories/expense_categories_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('assets/assets_m');
        $this->load->model('recipients/recipients_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->library('transactions');
        $this->load->library('loan');
        $this->load->library('excel_library');        
        $this->load->model('paybills/paybills_m');
    }

    function index(){
        $data = array();
        $this->template->title('Withdrawals')->build('shared/index',$data);
    }

    function listing(){
        $transaction_alert_id = $this->input->get('transaction_alert');
        $data = array();
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $filter_parameters = array(
            'transaction_alert_id' => $transaction_alert_id,
            'from' => $from,
            'to' => $to,
            'type' => $this->input->get('type'),
        );
        $data['from'] = $from;
        $data['to'] = $to;
        $total_rows = $this->withdrawals_m->count_group_withdrawals($filter_parameters);
        $pagination = create_pagination('group/withdrawals/listing/pages', $total_rows,50,5,TRUE);
        $data['asset_options'] = $this->assets_m->get_group_asset_options();
        $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $data['withdrawal_transaction_names'] = $this->transactions->withdrawal_transaction_names;
        $data['withdrawal_type_options'] = $this->transactions->withdrawal_type_options;
        $data['expense_category_options'] = $this->expense_categories_m->get_group_expense_category_options();
        $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
        $data['stock_options'] = $this->stocks_m->get_group_stock_options();
        $data['money_market_investment_options'] = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
        if($this->input->get('generate_excel') == 1){
            $data['group'] = $this->group;
            $data['group_currency'] = $this->group_currency;
            $data['posts'] = $this->withdrawals_m->get_group_withdrawals($filter_parameters);
            $data['filters'] = $filter_parameters;
            $data['group_member_options'] = $this->group_member_options;
            $data['group_debtor_options'] = $this->group_debtor_options;
            $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
            $json_file = json_encode($data);
            $this->excel_library->generate_withdrawals_listing($json_file);
            //print_r($json_file); die();
        }
        $data['pagination'] = $pagination;
        $data['posts'] = $this->withdrawals_m->limit($pagination['limit'])->get_group_withdrawals($filter_parameters);
        $this->template->title(translate('List Withdrawals'))->build('group/listing',$data);
    }

    function record_dividend_payments(){
        $data = array();
        $posts = $_POST;
        $errors = array();
        $error_messages = array();
        $successes = array();
        if($this->input->post('submit')){
            $entries_are_valid = TRUE;
            if(!empty($posts)){ 
                if(isset($posts['expense_dates'])){
                    $count = count($posts['expense_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['expense_dates'][$i])&&isset($posts['expense_categories'][$i])&&isset($posts['withdrawal_methods'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])):    
                            //Deposit dates
                            if($posts['expense_dates'][$i]==''){
                                $successes['expense_dates'][$i] = 0;
                                $errors['expense_dates'][$i] = 1;
                                $error_messages['expense_dates'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['expense_dates'][$i] = 1;
                                $errors['expense_dates'][$i] = 0;
                            }
                            //Members
                            if($posts['expense_categories'][$i]==''){
                                $successes['expense_categories'][$i] = 0;
                                $errors['expense_categories'][$i] = 1;
                                $error_messages['expense_categories'][$i] = 'Please select an expense category';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['expense_categories'][$i])){
                                    $successes['expense_categories'][$i] = 1;
                                    $errors['expense_categories'][$i] = 0;
                                }else{
                                    $successes['expense_categories'][$i] = 0;
                                    $errors['expense_categories'][$i] = 1;
                                    $error_messages['expense_categories'][$i] = 'Please enter a valid expense category value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Contributions
                            if($posts['withdrawal_methods'][$i]==''){
                                $successes['withdrawal_methods'][$i] = 0;
                                $errors['withdrawal_methods'][$i] = 1;
                                $error_messages['withdrawal_methods'][$i] = 'Please select a withdrawal method';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['withdrawal_methods'][$i])){
                                    $successes['withdrawal_methods'][$i] = 1;
                                    $errors['withdrawal_methods'][$i] = 0;
                                }else{
                                    $successes['withdrawal_methods'][$i] = 0;
                                    $errors['withdrawal_methods'][$i] = 1;
                                    $error_messages['withdrawal_methods'][$i] = 'Please select a valid withdrawal method value';
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
                            //amounts
                            if($posts['amounts'][$i]==''){
                                $successes['amounts'][$i] = 0;
                                $errors['amounts'][$i] = 1;
                                $error_messages['amounts'][$i] = 'Please enter a expense amount';
                                $entries_are_valid = FALSE;
                            }else{
                                if(valid_currency($posts['amounts'][$i])){
                                    $successes['amounts'][$i] = 1;
                                    $errors['amounts'][$i] = 0;
                                }else{
                                    $successes['amounts'][$i] = 0;
                                    $errors['amounts'][$i] = 1;
                                    $error_messages['amounts'][$i] = 'Please enter a valid expense amount';
                                    $entries_are_valid = FALSE; 
                                }
                            }
                        endif;
                    endfor;
                }
            }

            if($entries_are_valid){
                $this->session->set_flashdata('error','');
                $successful_expense_entry_count = 0;
                $unsuccessful_expense_entry_count = 0;
                if(isset($posts['expense_dates'])){$
                    $count = count($posts['expense_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['expense_dates'][$i])&&isset($posts['expense_categories'][$i])&&isset($posts['withdrawal_methods'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])):    
                            $amount = valid_currency($posts['amounts'][$i]);
                            $expense_date = strtotime($posts['expense_dates'][$i]); 
                            $description = isset(posts['expense_descriptions'][$i])?$posts['expense_descriptions'][$i]:'';


                            if($this->transactions->record_expense_withdrawal($this->group->id,$expense_date,$posts['expense_categories'][$i],$posts['withdrawal_methods'][$i],$posts['accounts'][$i],$description,$amount)){
                                $successful_expense_entry_count++;
                            }else{
                                $unsuccessful_expense_entry_count++;
                            }
                        endif;
                    endfor;
                }
                if($successful_expense_entry_count){
                    if($successful_expense_entry_count==1){
                        $this->session->set_flashdata('success',$successful_expense_entry_count.' expense successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('success',$successful_expense_entry_count.' expenses successfully recorded. ');
                    }
                }

                if($unsuccessful_expense_entry_count){
                    if($unsuccessful_expense_entry_count==1){
                        $this->session->set_flashdata('error',$unsuccessful_expense_entry_count.' expense was not successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('error',$unsuccessful_expense_entry_count.' expenses were not successfully recorded. ');
                    }
                }
                redirect('group/withdrawals/listing');
            }
            else{   
              $this->session->set_flashdata('error','There are some errors on the form. Please review and try again.');
            //   print_r($posts);
            // //   die();
            
            }

            
          
           
            
        }
        $data['errors'] = $errors;
        $data['error_messages'] = $error_messages;
        $data['successes'] = $successes;
        $data['posts'] = $posts;
        $data['banks'] = $this->banks_m->get_group_bank_options();
        $data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $data['withdrawal_method_options'] = $this->transactions->withdrawal_method_options;
        $data['expense_category_options'] = $this->expense_categories_m->get_group_expense_category_options();
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        $this->template->title(translate('Record Dividend Payments'))->build('group/record_dividend_payments',$data);

        // print_r($amount);
        // die();
       
    }

    function make_withdrawal(){
        $withdrawal_fors = array(
            1 => 'Loan Disbursement',
            2 => 'Expense Payment',
            3 => 'Dividend Payout',
            4 => 'Welfare',
            5 => 'Shares Refund',
        );
        $this->data['withdrawal_fors'] = $withdrawal_fors;
        $this->data['loan_type_options'] = $this->loan_types_m->get_options($this->group->id);
        $this->data['expense_category_options'] = $this->expense_categories_m->get_group_expense_category_options();
        $this->data['recipient_options'] = $this->withdrawals_m->get_recipient_options();
        $this->template->title('Make Withdrawal')->build('group/make_withdrawal',$this->data);

    }

    function withdraw_money(){
        $this->data['active_accounts'] = $this->accounts_m->get_active_group_account_options('','','','',TRUE);
        $this->data['bank_account_options'] = $this->bank_accounts_m->get_group_verified_and_linked_partner_bank_account_options_ids($this->group->id);
        $this->data['transfer_to_options'] = $this->transactions->recipient_options;
        $this->data['withdrawal_fors'] = $this->transactions->withdrawal_request_transaction_names;
        $this->data['loan_type_options'] = $this->loan_types_m->get_options($this->group->id);
        $this->data['bank_options'] = $this->banks_m->get_group_bank_options();
        $this->data['expense_category_options'] = $this->expense_categories_m->get_group_expense_category_options();
        // $this->data['recipient_options'] = $this->recipients_m->get_group_recipient_options();
        $this->data['bank_account_recipients'] = $this->recipients_m->get_group_bank_account_recipient_options();
        $this->data['mobile_money_account_recipients'] = $this->recipients_m->get_group_mobile_money_account_recipient_options();
        // $this->data['paybill_account_recipients'] = $this->recipients_m->get_group_paybill_account_recipient_options();
        $this->data['contribution_options'] = $this->contributions_m->get_active_group_contribution_options();
        $this->template->title(translate('Withdraw Money'))->build('group/withdraw_money',$this->data);

    }

    function record_expenses(){
    	$data = array();
        $posts = $_POST;
        $errors = array();
        $error_messages = array();
        $successes = array();
        if($this->input->post('submit')){
            $entries_are_valid = TRUE;
            if(!empty($posts)){ 
                if(isset($posts['expense_dates'])){
                    $count = count($posts['expense_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['expense_dates'][$i])&&isset($posts['expense_categories'][$i])&&isset($posts['withdrawal_methods'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])):    
                            //Deposit dates
                            if($posts['expense_dates'][$i]==''){
                                $successes['expense_dates'][$i] = 0;
                                $errors['expense_dates'][$i] = 1;
                                $error_messages['expense_dates'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['expense_dates'][$i] = 1;
                                $errors['expense_dates'][$i] = 0;
                            }
                            //Members
                            if($posts['expense_categories'][$i]==''){
                                $successes['expense_categories'][$i] = 0;
                                $errors['expense_categories'][$i] = 1;
                                $error_messages['expense_categories'][$i] = 'Please select an expense category';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['expense_categories'][$i])){
                                    $successes['expense_categories'][$i] = 1;
                                    $errors['expense_categories'][$i] = 0;
                                }else{
                                    $successes['expense_categories'][$i] = 0;
                                    $errors['expense_categories'][$i] = 1;
                                    $error_messages['expense_categories'][$i] = 'Please enter a valid expense category value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Contributions
                            if($posts['withdrawal_methods'][$i]==''){
                                $successes['withdrawal_methods'][$i] = 0;
                                $errors['withdrawal_methods'][$i] = 1;
                                $error_messages['withdrawal_methods'][$i] = 'Please select a withdrawal method';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['withdrawal_methods'][$i])){
                                    $successes['withdrawal_methods'][$i] = 1;
                                    $errors['withdrawal_methods'][$i] = 0;
                                }else{
                                    $successes['withdrawal_methods'][$i] = 0;
                                    $errors['withdrawal_methods'][$i] = 1;
                                    $error_messages['withdrawal_methods'][$i] = 'Please select a valid withdrawal method value';
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
                            //amounts
                            if($posts['amounts'][$i]==''){
                                $successes['amounts'][$i] = 0;
                                $errors['amounts'][$i] = 1;
                                $error_messages['amounts'][$i] = 'Please enter a expense amount';
                                $entries_are_valid = FALSE;
                            }else{
                                if(valid_currency($posts['amounts'][$i])){
                                    $successes['amounts'][$i] = 1;
                                    $errors['amounts'][$i] = 0;
                                }else{
                                    $successes['amounts'][$i] = 0;
                                    $errors['amounts'][$i] = 1;
                                    $error_messages['amounts'][$i] = 'Please enter a valid expense amount';
                                    $entries_are_valid = FALSE; 
                                }
                            }
                        endif;
                    endfor;
                }
            }

            if($entries_are_valid){
                $this->session->set_flashdata('error','');
                $successful_expense_entry_count = 0;
                $unsuccessful_expense_entry_count = 0;
                if(isset($posts['expense_dates'])){
                    $count = count($posts['expense_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['expense_dates'][$i])&&isset($posts['expense_categories'][$i])&&isset($posts['withdrawal_methods'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])):    
                            $amount = valid_currency($posts['amounts'][$i]);
                            $expense_date = strtotime($posts['expense_dates'][$i]); 
                            $description = isset($posts['expense_descriptions'][$i])?$posts['expense_descriptions'][$i]:'';
                            if($this->transactions->record_expense_withdrawal($this->group->id,$expense_date,$posts['expense_categories'][$i],$posts['withdrawal_methods'][$i],$posts['accounts'][$i],$description,$amount)){
                                $successful_expense_entry_count++;
                            }else{
                                $unsuccessful_expense_entry_count++;
                            }
                        endif;
                    endfor;
                }
                if($successful_expense_entry_count){
                    if($successful_expense_entry_count==1){
                        $this->session->set_flashdata('success',$successful_expense_entry_count.' expense successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('success',$successful_expense_entry_count.' expenses successfully recorded. ');
                    }
                }

                if($unsuccessful_expense_entry_count){
                    if($unsuccessful_expense_entry_count==1){
                        $this->session->set_flashdata('error',$unsuccessful_expense_entry_count.' expense was not successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('error',$unsuccessful_expense_entry_count.' expenses were not successfully recorded. ');
                    }
                }
                redirect('group/withdrawals/listing');
            }else{
                $this->session->set_flashdata('error','There are some errors on the form. Please review and try again.');
            }
            
        }
        $data['errors'] = $errors;
        $data['error_messages'] = $error_messages;
        $data['successes'] = $successes;
        $data['posts'] = $posts;
        $data['banks'] = $this->banks_m->get_group_bank_options();
        $data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $data['withdrawal_method_options'] = $this->transactions->withdrawal_method_options;
        $data['expense_category_options'] = $this->expense_categories_m->get_group_expense_category_options();
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        $this->template->title(translate('Record Expenses'))->build('group/record_expenses',$data);
    }

    function void($id = 0,$redirect = TRUE){
        $id OR redirect('group/withdrawals/listing');
        $post = $this->withdrawals_m->get_group_withdrawal($id);
        $post OR redirect('group/withdrawals/listing');
        $this->transactions->void_group_withdrawal($post->id,$post,TRUE,$this->group->id);
        if($redirect){
            if($this->agent->referrer()){
                redirect($this->agent->referrer());
            }else{
                redirect('group/withdrawals/listing');
            }
        }
    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if(empty($action_to)){
            $this->session->set_flashdata('error','Select atleast one withdrawal to void');
            redirect($this->agent->referrer());
        }
        if($action == 'bulk_void'){
            for($i=0;$i<count($action_to);$i++){
                $this->void($action_to[$i],FALSE);
            }
        }
        if($this->agent->referrer()){
            redirect($this->agent->referrer());
        }else{
            redirect('group/withdrawals/listing');
        }
    }

    function ajax_contribution_refunds_listing(){
        $contribution_options = $this->contributions_m->get_group_contribution_options();
        $group_member_total_back_dated_contribution_refunds_per_contribution_array = $this->withdrawals_m->get_group_member_total_back_dated_contribution_refunds_per_contribution_array();
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
                                        Amount Refunded ('.$this->group_currency.')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>';
                                    $count = 1; 
                                    $total_refunded = 0;
                                    foreach($this->group_member_options as $member_id => $member_name): 
                                    $refund = $group_member_total_back_dated_contribution_refunds_per_contribution_array[$member_id][$contribution_id];
                                    $total_refunded += $refund;
                                echo '
                                    <tr>
                                        <td>'.$count++.'</td>
                                        <td>'.$member_name.'</td>
                                        <td  class="text-right">'.number_to_currency($refund).'</td>
                                    </tr>';
                                    endforeach; 
                                echo '
                                <tr>
                                    <td>#</td>
                                    <td>Totals</td>
                                    <td class="text-right">
                                        '.number_to_currency($total_refunded).'
                                    </td>
                                </tr>
                            </tbody>
                        </table>';
            endforeach;
        }       
    }

    function ajax_contribution_refunds_form(){
            $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
            $contribution_options = $this->contributions_m->get_group_contribution_options();
            $group_member_total_back_dated_contribution_refunds_per_contribution_array = $this->withdrawals_m->get_group_member_total_back_dated_contribution_refunds_per_contribution_array();
            echo '
            <div class="alert alert-info">
                <strong>Information!</strong> Enter the amount each member <strong>had</strong> been refunded per contribution as at '.timestamp_to_date($group_cut_off_date->cut_off_date).'
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
                                Amount Refunded ('.$this->group_currency.')
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                            $count = 1; 
                            $total_refunded = 0;
                            foreach($this->group_member_options as $member_id => $member_name): 
                            $refund = $group_member_total_back_dated_contribution_refunds_per_contribution_array[$member_id][$contribution_id];
                            $total_refunded += $refund;
                            echo '
                            <tr>
                                <td>'.$count++.'</td>
                                <td>'.$member_name.'</td>
                                <td class="text-right"> 
                                    '.form_input('member_refunds['.$member_id.']['.$contribution_id.']',$refund," class='form-control currency'").'
                                </td>
                            </tr>'; 
                            endforeach; 
                    echo '
                    </tbody>
                </table>';
                endforeach;
            }
    }  

    function ajax_record_back_dating_contribution_refunds(){
        $posts = $_POST;
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        if($suspense_petty_cash_account = $this->petty_cash_accounts_m->get_group_back_dating_petty_cash_suspense_account()){
            $account_id = "petty-".$suspense_petty_cash_account->id;
            if($group_cut_off_date){
                if(isset($posts)){
                    $refunds = $this->input->post('member_refunds');
                    if(empty($refunds)){
                        echo "Refunds post value empty";
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
                        foreach($refunds as $member_id => $contributions):
                            if(isset($member_objects_array[$member_id])){
                                foreach($contributions as $contribution_id => $amount):
                                    if(isset($contribution_objects_array[$contribution_id])){
                                        if($amount){
                                            if($this->transactions->record_contribution_refund($this->group->id,$group_cut_off_date->cut_off_date,$member_id,$account_id,$contribution_id,1,'Back dating contribution refund',valid_currency($amount),$this->user->id,0,TRUE)){

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

    function ajax_void_group_back_dating_contribution_refunds(){
        $withdrawals = $this->withdrawals_m->get_group_back_dating_contribution_refunds();
        $result = TRUE;
        foreach($withdrawals as $withdrawal):
            if($this->transactions->void_group_withdrawal($withdrawal->id,$withdrawal,FALSE,$this->group->id)){

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

    function ajax_expenses_listing(){
        $expense_category_options = $this->expense_categories_m->get_group_expense_category_options();
        $group_total_back_dated_expenses_per_expense_category_array = $this->withdrawals_m->get_group_total_back_dated_expenses_per_expense_category_array();
        if(!empty($expense_category_options)){
            echo '<h4>Back-dated Expenses</h4>';
            echo '
                <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                    <thead>
                        <tr>
                            <th width="8px">
                                #
                            </th>
                            <th>
                                Expense Category
                            </th>
                            <th class="text-right">
                                Amount Expensed ('.$this->group_currency.')
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                            $count = 1; 
                            $total_expense = 0;
                            foreach($expense_category_options as $expense_category_id => $expense_category_name): 
                            $expense = $group_total_back_dated_expenses_per_expense_category_array[$expense_category_id];
                            $total_expense += $expense;
                        echo '
                            <tr>
                                <td>'.$count++.'</td>
                                <td>'.$expense_category_name.'</td>
                                <td  class="text-right">'.number_to_currency($expense).'</td>
                            </tr>';
                            endforeach; 
                        echo '
                        <tr>
                            <td>#</td>
                            <td>Totals</td>
                            <td class="text-right">
                                '.number_to_currency($total_expense).'
                            </td>
                        </tr>
                    </tbody>
                </table>';
        }       
    }

    function ajax_expenses_form(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $expense_category_options = $this->expense_categories_m->get_group_expense_category_options();
        $group_total_back_dated_expenses_per_expense_category_array = $this->withdrawals_m->get_group_total_back_dated_expenses_per_expense_category_array();
        if(!empty($expense_category_options)){
            echo '
            <div class="alert alert-info">
                <strong>Information!</strong> Enter the amount the group <strong>had</strong> expensed per expense category as at '.timestamp_to_date($group_cut_off_date->cut_off_date).'
            </div>';
            echo '<h4>Back-dated Expenses</h4>';
            echo '
                <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                    <thead>
                        <tr>
                            <th width="8px">
                                #
                            </th>
                            <th>
                                Expense Category
                            </th>
                            <th class="text-right">
                                Amount Expensed ('.$this->group_currency.')
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                            $count = 1; 
                            $total_expense = 0;
                            foreach($expense_category_options as $expense_category_id => $expense_category_name): 
                            $expense = $group_total_back_dated_expenses_per_expense_category_array[$expense_category_id];
                            $total_expense += $expense;
                        echo '
                            <tr>
                                <td>'.$count++.'</td>
                                <td>'.$expense_category_name.'</td>
                                <td  class="text-right">'.form_input('expenses['.$expense_category_id.']',$expense," class='form-control currency'").'</td>
                            </tr>';
                            endforeach; 
                        echo '
                    </tbody>
                </table>';
        }       
    }

    function ajax_void_group_back_dating_expenses(){
        $withdrawals = $this->withdrawals_m->get_group_back_dating_expenses();
        $result = TRUE;
        foreach($withdrawals as $withdrawal):
            if($this->transactions->void_group_withdrawal($withdrawal->id,$withdrawal,FALSE,$this->group->id)){

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

    function ajax_record_back_dating_expenses(){
        $posts = $_POST;
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        if($suspense_petty_cash_account = $this->petty_cash_accounts_m->get_group_back_dating_petty_cash_suspense_account()){
            $account_id = "petty-".$suspense_petty_cash_account->id;
            if($group_cut_off_date){
                if(isset($posts)){
                    $expenses = $this->input->post('expenses');
                    if(empty($expenses)){
                        echo "Expenses post value empty";
                    }else{
                        $result = TRUE;
                        foreach($expenses as $expense_category_id => $amount):
                            if($amount){
                                if($this->transactions->record_expense_withdrawal($this->group->id,$group_cut_off_date->cut_off_date,$expense_category_id,1,$account_id,'Back-dating expense',valid_currency($amount),0,TRUE)){

                                }else{
                                    $result = FALSE;
                                }
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

    function ajax_loans_borrowed_form(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $group_member_total_back_dated_loans_borrowed_array = $this->withdrawals_m->get_group_member_total_back_dated_loans_borrowed_array();
        $group_member_total_back_dated_loans_payable_array = $this->loan_invoices_m->get_group_member_total_back_dated_loans_payable_array();
        if($group_member_total_back_dated_loans_borrowed_array&&$group_member_total_back_dated_loans_payable_array){
            echo '
            <div class="alert alert-info">
                <strong>Information!</strong> Enter the amount each member <strong>had</strong> borrowed in total (since the group started) as at '.timestamp_to_date($group_cut_off_date->cut_off_date).'
            </div>';
            echo '<h4>Back-dated Loans Borrowed</h4>';
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
                                Loan Amount ('.$this->group_currency.')
                            </th>
                            <th class="text-right">
                                Loan Amount Payable('.$this->group_currency.')
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                            $count = 1; 
                            $total_loan_borrowed = 0;
                            $total_loan_payable = 0;
                            foreach($this->group_member_options as $member_id => $member_name): 
                            $loan_borrowed = $group_member_total_back_dated_loans_borrowed_array[$member_id];
                            $loan_payable = $group_member_total_back_dated_loans_payable_array[$member_id];
                            $total_loan_borrowed += $loan_borrowed;
                            $total_loan_payable += $loan_payable;
                        echo '
                            <tr>
                                <td>'.$count++.'</td>
                                <td>'.$member_name.'</td>
                                <td  class="text-right">'.form_input('loans_borrowed['.$member_id.']',$loan_borrowed," class='form-control currency'").'</td>
                                <td  class="text-right">'.form_input('loans_payable['.$member_id.']',$loan_payable," class='form-control currency'").'</td>
                            </tr>';
                            endforeach; 
                        echo '
                    </tbody>
                </table>';
        }  
    }

    function ajax_loans_borrowed_listing(){
        $group_member_total_back_dated_loans_borrowed_array = $this->withdrawals_m->get_group_member_total_back_dated_loans_borrowed_array();
        $group_member_total_back_dated_loans_payable_array = $this->loan_invoices_m->get_group_member_total_back_dated_loans_payable_array();
        if($group_member_total_back_dated_loans_borrowed_array&&$group_member_total_back_dated_loans_payable_array){
            echo '<h4>Back-dated Loans Borrowed</h4>';
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
                                Loan Amount Borrowed ('.$this->group_currency.')
                            </th>
                            <th class="text-right">
                                Loan Amount Payable ('.$this->group_currency.')
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                            $count = 1; 
                            $total_loan_borrowed = 0;
                            $total_loan_payable = 0;
                            foreach($this->group_member_options as $member_id => $member_name): 
                            $loan_borrowed = $group_member_total_back_dated_loans_borrowed_array[$member_id];
                            $loan_payable = $group_member_total_back_dated_loans_payable_array[$member_id];
                            $total_loan_borrowed += $loan_borrowed;
                            $total_loan_payable += $loan_payable;
                        echo '
                            <tr>
                                <td>'.$count++.'</td>
                                <td>'.$member_name.'</td>
                                <td  class="text-right">'.number_to_currency($loan_borrowed).'</td>
                                <td  class="text-right">'.number_to_currency($loan_payable).'</td>
                            </tr>';
                            endforeach; 
                        echo '
                        <tr>
                            <td>#</td>
                            <td>Totals</td>
                            <td class="text-right">
                                '.number_to_currency($total_loan_borrowed).'
                            </td>
                            <td class="text-right">
                                '.number_to_currency($total_loan_payable).'
                            </td>
                        </tr>
                    </tbody>
                </table>';
        }  
    }

    function ajax_void_group_back_dating_loans_borrowed(){
        $withdrawals = $this->withdrawals_m->get_group_back_dating_loans_borrowed();
        $result = TRUE;
        foreach($withdrawals as $withdrawal):
            if($this->transactions->void_group_withdrawal($withdrawal->id,$withdrawal,FALSE,$this->group->id)){

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

    function ajax_record_back_dating_loans_borrowed(){
        $posts = $_POST;
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        if($suspense_petty_cash_account = $this->petty_cash_accounts_m->get_group_back_dating_petty_cash_suspense_account()){
            $account_id = "petty-".$suspense_petty_cash_account->id;
            if($group_cut_off_date){
                if(isset($posts)){
                    $loans_borrowed = $this->input->post('loans_borrowed');
                    $loans_payable = $this->input->post('loans_payable');
                    if(empty($loans_borrowed)||empty($loans_payable)){
                        echo "Loans borrowed post value empty";
                    }else{
                        $members = $this->members_m->get_group_members();

                        $member_objects_array = array();
                        foreach($members as $member):
                            $member_objects_array[$member->id] = $member;
                        endforeach;

                        $result = TRUE;
                        foreach($loans_borrowed as $member_id => $amount):
                            if(isset($member_objects_array[$member_id])){
                                if($amount){
                                    $loan_details = array(
                                        'disbursement_date' =>  $group_cut_off_date->cut_off_date,
                                        'loan_amount'   =>  currency($loans_borrowed[$member_id]),
                                        'account_id'    =>  $account_id,
                                        'repayment_period'  =>  1,
                                        'interest_rate' =>  0,
                                        'loan_interest_rate_per' =>  4,
                                        'interest_type' =>  3,
                                        'custom_interest_procedure'=>2,
                                        'grace_period'  => 1,
                                        'sms_notifications_enabled' =>  0,
                                        'sms_template'  =>  '',
                                        'email_notifications_enabled' =>  0,
                                        'enable_loan_fines' =>  0,
                                        'enable_outstanding_loan_balance_fines'=>0,
                                        'enable_loan_processing_fee' => 0,
                                        'enable_loan_fine_deferment' => 0,
                                        'enable_loan_guarantors' => 0,
                                        'enable_reducing_balance_installment_recalculation' => 0,
                                        'active'    =>  1,
                                        'created_by'    =>  $this->user->id,
                                        'created_on'    =>  time(),
                                        'is_a_back_dating_record'    => 1,
                                    );
                                    $custom_loan_values = array(
                                        'payment_date' =>  array($group_cut_off_date->cut_off_date),
                                        'amount_payable' =>  array(currency($loans_payable[$member_id])),
                                        'is_a_back_dating_record' =>  array(TRUE),
                                    );
                                    $custom_rate_procedure = " ";
                                    if($this->loan->create_automated_group_loan(1,$member_id,$this->group->id,$loan_details,$custom_loan_values,2,array(),TRUE)){
                                        
                                    }else{
                                        $result = FALSE;
                                    }
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

    function ajax_group_loans_paid_listing(){
        $group_loan_paid = $this->withdrawals_m->get_group_back_dated_loans_paid_amount();
        echo '<h4>Back-dated Group Loans Paid</h4>';
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
                            Group Loans Paid Total ('.$this->group_currency.')
                        </th>
                    </tr>
                </thead>
                <tbody>';
                        echo '
                        <tr>
                            <td>1</td>
                            <td>Group Loans Paid</td>
                            <td class="text-right"> 
                                '.number_to_currency($group_loan_paid).'
                            </td>
                        </tr>'; 
                echo '
                </tbody>
            </table>';
    }

    function ajax_group_loans_paid_form(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $group_loan_paid = $this->withdrawals_m->get_group_back_dated_loans_paid_amount();
        echo '
            <div class="alert alert-info">
                <strong>Information!</strong> Enter the amount that <strong>had</strong> been paid back by the group in total (since the group started) as at '.timestamp_to_date($group_cut_off_date->cut_off_date).'
            </div>';
        echo '<h4>Back-dated Group Loans Paid</h4>';
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
                            Group Loans Paid Total ('.$this->group_currency.')
                        </th>
                    </tr>
                </thead>
                <tbody>';
                        echo '
                        <tr>
                            <td>1</td>
                            <td>Group Loans Paid Total</td>
                            <td class="text-right"> 
                                '.form_input('group_loan_paid',$group_loan_paid," class='form-control currency'").'
                            </td>
                        </tr>'; 
                echo '
                </tbody>
            </table>';
    }

    function ajax_void_group_back_dating_bank_loan_repayments(){
        $withdrawals = $this->withdrawals_m->get_group_back_dating_bank_loan_repayments();
        $result = TRUE;
        foreach($withdrawals as $withdrawal):
            if($this->transactions->void_group_withdrawal($withdrawal->id,$withdrawal,FALSE,$this->group->id)){

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

    function ajax_record_group_back_dating_group_loans_paid(){
        $posts = $_POST;
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        if($suspense_petty_cash_account = $this->petty_cash_accounts_m->get_group_back_dating_petty_cash_suspense_account()){
            $account_id = "petty-".$suspense_petty_cash_account->id;
            if($group_cut_off_date){
                if(isset($posts)){
                    $group_loan_paid = $this->input->post('group_loan_paid');
                    if(empty($group_loan_paid)){
                        echo "success";
                    }else{
                        $result = TRUE;
                        if($group_loan_paid){
                            if($bank_loan = $this->bank_loans_m->get_group_back_dating_group_loan_borrowed()){
                                if($this->loan->bank_loan_repayment($bank_loan->id,valid_currency($group_loan_paid),$group_cut_off_date->cut_off_date,$this->group->id,$account_id,1,'Back-dating group loan repayment',$this->user->id,0,TRUE)){

                                }else{
                                    $result = FALSE;
                                }
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

    function ajax_void_group_back_dating_stocks(){
        $withdrawals = $this->withdrawals_m->get_group_back_dating_stock_purchases();
        $result = TRUE;
        foreach($withdrawals as $withdrawal):
            if($this->transactions->void_group_withdrawal($withdrawal->id,$withdrawal,FALSE,$this->group->id)){

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

    function ajax_record_group_back_dating_stocks(){
        $posts = $_POST;
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        if($suspense_petty_cash_account = $this->petty_cash_accounts_m->get_group_back_dating_petty_cash_suspense_account()){
            $account_id = "petty-".$suspense_petty_cash_account->id;
            if($group_cut_off_date){
                if(isset($posts)){
                    $entries_are_valid = TRUE;
                    $stock_names = $this->input->post('stock_names');
                    $number_of_stocks_purchased = $this->input->post('number_of_stocks_purchased');
                    $price_per_share = $this->input->post('price_per_share');
                    $number_of_stocks_sold = $this->input->post('number_of_stocks_sold');
                    $sale_price_per_share = $this->input->post('sale_price_per_share');

                    foreach($stock_names as $key => $value):
                        if($value==""){
                            $entries_are_valid = FALSE;
                        }
                    endforeach;

                    foreach($number_of_stocks_purchased as $key => $value):
                        if($value==""){
                            $entries_are_valid = FALSE;
                        }else if(is_numeric($value)&&$value>0){
                            if($value<$number_of_stocks_sold[$key]){
                                $entries_are_valid = FALSE;
                            }else{

                            }
                        }else{
                            $entries_are_valid = FALSE;
                        }
                    endforeach;

                    foreach($price_per_share as $key => $value):
                        if($value==""){
                            $entries_are_valid = FALSE;
                        }else if(is_numeric(valid_currency($value))&&valid_currency($value)>0){

                        }else{
                            $entries_are_valid = FALSE;
                        }
                    endforeach;

                    foreach($number_of_stocks_sold as $key => $value):
                        if($value==""){

                        }else if(is_numeric($value)&&$value>0){
                            if($value>$number_of_stocks_purchased[$key]){
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric(valid_currency($sale_price_per_share[$key]))&&valid_currency($sale_price_per_share[$key])>0){

                                }else{
                                    $entries_are_valid = FALSE;
                                }
                            }
                        }else{
                            $entries_are_valid = FALSE;
                        }
                    endforeach;

                    foreach($sale_price_per_share as $key => $value):
                        if($value==""){

                        }else if(is_numeric(valid_currency($value))&&valid_currency($value)>0){
                            if(is_numeric(valid_currency($number_of_stocks_sold[$key]))&&valid_currency($number_of_stocks_sold[$key])>0){

                            }else{
                                $entries_are_valid = FALSE;
                            }
                        }else{
                            $entries_are_valid = FALSE;
                        }
                    endforeach;

                    if($entries_are_valid){
                        $count = count($stock_names);
                        $keys_and_stock_id_array = array();
                        $result = TRUE;
                        for($i = 0;$i < $count;$i++ ):
                            if($stock_id = $this->transactions->record_stock_purchase($this->group->id,$group_cut_off_date->cut_off_date,$stock_names[$i],$number_of_stocks_purchased[$i],$account_id,valid_currency($price_per_share[$i]),0,TRUE)){
                                $keys_and_stock_id_array[$i] = $stock_id;
                            }else{
                                $result = FALSE;
                            }
                        endfor;
                        for($i = 0;$i < $count;$i++ ):
                            if($number_of_stocks_sold[$i]&&$sale_price_per_share[$i]):
                                if($this->transactions->record_stock_sale($this->group->id,$keys_and_stock_id_array[$i],$group_cut_off_date->cut_off_date,$account_id,$number_of_stocks_sold[$i],$sale_price_per_share[$i],0,0,TRUE)){

                                }else{
                                    $result = FALSE;
                                }
                            endif;
                        endfor;
                        if($result){
                            echo "success";
                        }else{
                            echo "error";
                        }
                    }else{
                        echo "Data sent failed server side validation";
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

    function ajax_void_group_back_dating_money_market_investments(){
        $withdrawals = $this->withdrawals_m->get_group_back_dating_money_market_investments();
        $result = TRUE;
        foreach($withdrawals as $withdrawal):
            if($this->transactions->void_group_withdrawal($withdrawal->id,$withdrawal,FALSE,$this->group->id)){

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

    function ajax_record_group_back_dating_money_market_investments(){
        $posts = $_POST;
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        if($suspense_petty_cash_account = $this->petty_cash_accounts_m->get_group_back_dating_petty_cash_suspense_account()){
            $account_id = "petty-".$suspense_petty_cash_account->id;
            if($group_cut_off_date){
                if(isset($posts)){
                    $result = TRUE;
                    $past_money_market_investment = $this->input->post('past_money_market_investment');
                    if($past_money_market_investment){
                        if($money_market_investment_id = $this->transactions->create_money_market_investment($this->group->id,"Back-dating Recalled/Cashed In Money Market Investment",$group_cut_off_date->cut_off_date,valid_currency($past_money_market_investment),$account_id,'Back-dating Money Market Investment',0,TRUE)){

                        }else{
                            $result = FALSE;
                        }
                        $cashed_in_money_market_investment = $this->input->post('cashed_in_money_market_investment');
                        if($cashed_in_money_market_investment){
                            if($this->transactions->record_money_market_investment_cash_in_deposit($this->group->id,$money_market_investment_id,$group_cut_off_date->cut_off_date,$account_id,valid_currency($cashed_in_money_market_investment),0,TRUE)){

                            }else{
                                $result = FALSE;
                            }
                        }
                    }

                    $ongoing_money_market_investment = $this->input->post('ongoing_money_market_investment');
                    if($ongoing_money_market_investment){
                        if($money_market_investment_id = $this->transactions->create_money_market_investment($this->group->id,"Back-dating Ongoing Money Market Investment",$group_cut_off_date->cut_off_date,valid_currency($past_money_market_investment),$account_id,'Back-dating Ongoing Money Market Investment',0,TRUE)){

                        }else{
                            $result = FALSE;
                        }
                    }
                    if($result){
                        echo "success";
                    }else{
                        echo "error";
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

    function ajax_void_group_back_dating_assets(){
        $assets = $this->assets_m->get_group_back_dating_assets();
        $result = TRUE;
        foreach($assets as $asset):
            $input = array(
                'active' => 0,
                'modified_on' => time(),
                'modified_by' => $this->user->id,
            );
            if($this->assets_m->update($asset->id,$input)){

            }else{
                $result = FALSE;
            }
        endforeach;
        $withdrawals = $this->withdrawals_m->get_group_back_dating_asset_purchase_payments();
        foreach($withdrawals as $withdrawal):
            if($this->transactions->void_group_withdrawal($withdrawal->id,$withdrawal,FALSE,$this->group->id)){

            }else{
                $result = FALSE;
            }
        endforeach;
        $deposits = $this->deposits_m->get_group_back_dating_asset_sales();
        foreach($deposits as $deposit):
            if($this->transactions->void_group_deposit($deposit->id,$deposit,FALSE,$this->group->id)){

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

    function ajax_record_group_back_dating_assets(){
        $posts = $_POST;
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        if($suspense_petty_cash_account = $this->petty_cash_accounts_m->get_group_back_dating_petty_cash_suspense_account()){
            $account_id = "petty-".$suspense_petty_cash_account->id;
            if($group_cut_off_date){
                if(isset($posts)){
                    $entries_are_valid = TRUE;
                    $asset_names = $this->input->post('asset_names');
                    $asset_categories = $this->input->post('asset_categories');
                    $costs = $this->input->post('costs');
                    $payments_made = $this->input->post('payments_made');
                    $sales_made = $this->input->post('sales_made');

                    foreach($asset_names as $key => $value):
                        if($value==""){
                            $entries_are_valid = FALSE;
                        }
                    endforeach;

                    foreach($asset_categories as $key => $value):
                        if($value==""){
                            $entries_are_valid = FALSE;
                        }else if(is_numeric($value)&&$value>0){
                            
                        }else{
                            $entries_are_valid = FALSE;
                        }
                    endforeach;

                    foreach($costs as $key => $value):
                        if($value==""){
                            $entries_are_valid = FALSE;
                        }else if(is_numeric(valid_currency($value))&&valid_currency($value)>0){

                        }else{
                            $entries_are_valid = FALSE;
                        }
                    endforeach;

                    foreach($payments_made as $key => $value):
                        if($value==""){
                            $entries_are_valid = FALSE;
                        }else if(is_numeric(valid_currency($value))&&valid_currency($value)>0){

                        }else{
                            $entries_are_valid = FALSE;
                        }
                    endforeach;

                    foreach($sales_made as $key => $value):
                        if($value==""){
                            
                        }else if(is_numeric(valid_currency($value))&&valid_currency($value)>0){

                        }else{
                            $entries_are_valid = FALSE;
                        }
                    endforeach;
                    if($entries_are_valid){
                        $count = count($asset_names);
                        $result = TRUE;
                        $asset_id_key_array = array();
                        for($i = 0;$i < $count;$i++ ):
                            $input = array(
                                'name' => $asset_names[$i],
                                'asset_category_id' => $asset_categories[$i],
                                'cost' => valid_currency($costs[$i]),
                                'group_id' => $this->group->id,
                                'active' => 1,
                                'is_a_back_dating_record' => 1,
                                'created_on' => time(),
                                'created_by' => $this->user->id,
                            );
                            if($asset_id = $this->assets_m->insert($input)){
                                $asset_id_key_array[$i] = $asset_id;
                            }else{
                                $result = FALSE;
                            }
                        endfor;
                        for($i = 0;$i < $count;$i++ ):
                            if(isset($asset_id_key_array[$i])){     
                                if($this->transactions->record_asset_purchase_payment($this->group->id,$group_cut_off_date->cut_off_date,$asset_id_key_array[$i],$account_id,1,'Back dating asset purchase payment',valid_currency($payments_made[$i]),0,TRUE)){
                                    
                                }else{
                                    $result = FALSE;
                                }
                            }else{
                                $result = FALSE;
                            }
                        endfor;
                        for($i = 0;$i < $count;$i++ ):
                            if($sales_made[$i]):

                                if(isset($asset_id_key_array[$i])){ 

                                    if($this->transactions->record_asset_sale_deposit($this->group->id,$asset_id_key_array[$i],$group_cut_off_date->cut_off_date,$account_id,valid_currency($sales_made[$i]),0,TRUE)){
                                        
                                    }else{
                                        $result = FALSE;
                                    }
                                }else{
                                    $result = FALSE;
                                }
                            endif;
                        endfor;
                        if($result){
                            echo "success";
                        }else{
                            echo "error";
                        }
                    }else{
                        echo "Data sent failed server side validation";
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

    function respond($id = 0){
        $id OR redirect("group/withdrawals/withdrawal_requests");
        $post = $this->withdrawals_m->get_group_member_withdrawal_approval_request_by_member_id($id);
        $post OR redirect("group/withdrawals/withdrawal_requests");
        if($post->is_approved || $post->is_declined){
            $this->session->set_flashdata('info',"You already responded to that withdrawal approval request.");
            redirect("group/withdrawals/withdrawal_requests");
        }
        $this->data['withdrawal_request_transaction_names'] = $this->transactions->withdrawal_request_transaction_names;
        $withdrawal_request = $this->withdrawals_m->get_group_withdrawal_request($post->withdrawal_request_id);
        $this->data['withdrawal_request'] = $withdrawal_request;
        $this->data['post'] = $post;
        $this->data['expense_category_options'] = $this->expense_categories_m->get_group_expense_category_options();
        $this->data['contribution_options'] = $this->contributions_m->get_active_group_contribution_options();
        $this->data['loan_type_options'] = $this->loan_types_m->get_options();
        $this->data['recipient_options'] = $this->recipients_m->get_group_recipient_options($this->group->id,TRUE);
        $this->template->set_layout('default_full_width.html')->title('Respond to Withdrawal Request')->build('shared/respond',$this->data);
    }
    
    function withdrawal_requests(){
        $data = array();
        $this->template->title(translate('Withdrawal Requests'))->build('shared/withdrawal_requests',$data);
    }

    function withdrawal_request($id = 0){
        $data = array();
        $id OR redirect("group/withdrawals/withdrawal_requests");
        $post = $this->withdrawals_m->get_group_withdrawal_request($id);
        $post OR redirect("group/withdrawals/withdrawal_requests");
        $this->data['withdrawal_request_transaction_names'] = $this->transactions->withdrawal_request_transaction_names;
        $this->data['post'] = $post;
        $this->data['expense_category_options'] = $this->expense_categories_m->get_group_expense_category_options();
        $this->data['withdrawal_approval_requests'] = $this->withdrawals_m->get_group_withdrawal_approval_requests($id);
        $this->data['contribution_options'] = $this->contributions_m->get_active_group_contribution_options();
        $this->template->title(translate('Withdrawal Request'))->build('shared/withdrawal_request',$this->data);
    }

    function check_missing_transaction_statements(){
        $withdrawals = $this->withdrawals_m->get_group_withdrawals();
        echo count($withdrawals);
        $transaction_statement_withdrawal_ids_array = $this->transaction_statements_m->get_group_transaction_statement_withdrawal_ids_array($this->group->id);
        foreach($withdrawals as $withdrawal):
            if(isset($transaction_statement_withdrawal_ids_array[$withdrawal->id])){

            }else{
                echo "Am in.<br/>";
            }
        endforeach;
        
    }

}