<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Mobile extends Mobile_Controller{

    function __construct(){
        parent::__construct();

        $this->load->model('reports_m');
        $this->load->model('loans/loans_m');
        $this->load->model('loan_repayments/loan_repayments_m');
        $this->load->model('expense_categories/expense_categories_m');
        $this->load->model('withdrawals/withdrawals_m');
        $this->load->model('income_categories/income_categories_m');
        $this->load->model('assets/assets_m');
    }
    
    function contribution_summary(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->group_role_id || $this->member->is_admin){
                        $group_member_options =  $this->members_m->get_group_member_options($this->group->id);
                        $active_group_member_options = $this->members_m->get_active_group_member_options($this->group->id);
                    }else{
                        if($this->group->enable_member_information_privacy){
                            $arr[$this->member->id] = $this->user->first_name.' '.$this->user->last_name;
                            $group_member_options = $arr;
                            $active_group_member_options = $arr; 
                        }else{
                            $group_member_options =  $this->members_m->get_group_member_options($this->group->id);
                            $active_group_member_options = $this->members_m->get_active_group_member_options($this->group->id); 
                        }
                    }

                    //$member_payments = $this->statements_m->get_group_member_balance_payments($this->group->id,$group_member_options);
                    $group_member_total_cumulative_contribution_paid_per_member_array = $this->statements_m->get_group_member_total_cumulative_contribution_paid_per_member_array($this->group->id);
                    $group_member_total_cumulative_contribution_arrears_per_member_array = $this->statements_m->get_group_member_total_cumulative_contribution_arrears_per_member_array($this->group->id);
                    if($group_member_total_cumulative_contribution_paid_per_member_array&&$group_member_total_cumulative_contribution_arrears_per_member_array){
                        $i = 1; 
                        $balances = array();
                        foreach ($active_group_member_options as $member_id => $name): 
                            if($this->group->disable_arrears){
                                $arrears = 0;
                            }else{
                                $arrears = $group_member_total_cumulative_contribution_arrears_per_member_array[$member_id];
                            }
                            
                            $balances[] = array(
                                'member_id' => $member_id, 
                                'name' => $name,
                                'paid' => $group_member_total_cumulative_contribution_paid_per_member_array[$member_id],
                                'arrears' => $arrears,
                            );
                        endforeach;
                    }else{
                        $total_contributions_paid_per_member_array = $this->reports_m->get_group_total_contributions_paid_per_member_array($this->group->id,$group_member_options);
                        if($this->group->disable_arrears){
                            $total_contribution_balances_per_member_array = 0;
                        }else{
                            $total_contribution_balances_per_member_array = $this->reports_m->get_group_total_contribution_balances_per_member_array($this->group->id,$this->group->disable_ignore_contribution_transfers,$group_member_options);
                        }
                        
                        $i = 1; 
                        $balances = array();
                        foreach ($active_group_member_options as $member_id => $name): 
                            $balances[] = array(
                                'member_id' => $member_id, 
                                'name' => $name,
                                'paid' => $total_contributions_paid_per_member_array[$member_id],
                                'arrears' => $total_contribution_balances_per_member_array[$member_id],
                            );
                        endforeach;
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'success',
                        'time' => time(),
                        'balances' => $balances,
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }

        echo encrypt_json_encode(array('response'=>$response));
    }

    function fines_summary(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->group_role_id || $this->member->is_admin){
                        $group_member_options =  $this->members_m->get_group_member_options($this->group->id);
                        $active_group_member_options = $this->members_m->get_active_group_member_options($this->group->id);
                    }else{
                        if($this->group->enable_member_information_privacy){
                            $arr[$this->member->id] = $this->user->first_name.' '.$this->user->last_name;
                            $group_member_options = $arr;
                            $active_group_member_options = $arr; 
                        }else{
                            $group_member_options =  $this->members_m->get_group_member_options($this->group->id);
                            $active_group_member_options = $this->members_m->get_active_group_member_options($this->group->id); 
                        }
                    }
                    $group_member_total_cumulative_fine_paid_per_member_array = $this->statements_m->get_group_member_total_cumulative_fine_paid_per_member_array($this->group->id);
                    $group_member_total_cumulative_fine_arrears_per_member_array = $this->statements_m->get_group_member_total_cumulative_fine_arrears_per_member_array($this->group->id);
                    if($group_member_total_cumulative_fine_paid_per_member_array && $group_member_total_cumulative_fine_arrears_per_member_array){
                        $balances = array();
                        foreach ($active_group_member_options as $member_id => $name): 
                            if($this->group->disable_arrears){
                                $fines_arrears = 0;
                            }else{
                                $fines_arrears = $group_member_total_cumulative_fine_arrears_per_member_array[$member_id];
                            }                            
                            $balances[] = array(
                                'member_id' => $member_id, 
                                'name' => $name,
                                'paid' => $group_member_total_cumulative_fine_paid_per_member_array[$member_id],
                                'arrears' => $fines_arrears,
                            );
                        endforeach;
                    }else{
                        $group_total_fines_paid_per_member_array = $this->reports_m->get_group_total_fines_paid_per_member_array(0,$this->group->id,$group_member_options);
                        $group_total_fines_balances_per_member_array = $this->reports_m->get_group_total_fines_balances_per_member_array($this->group->id,$group_member_options);
                        $balances = array();
                        foreach ($active_group_member_options as $member_id => $name): 
                            $balances[] = array(
                                'member_id' => $member_id, 
                                'name' => $name,
                                'paid' => $group_total_fines_paid_per_member_array[$member_id],
                                'arrears' => $group_total_fines_balances_per_member_array[$member_id],
                            );
                        endforeach;
                    }                   
                    $response = array(
                        'status' => 1,
                        'message' => 'success',
                        'time' => time(),
                        'balances' => $balances,
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function loans_summary(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $this->group_member_options =  $this->members_m->get_group_member_options($this->group->id);
                    $total_loan_out = $this->loans_m->get_total_loaned_amount($this->group->id);
                    $total_loan_paid = $this->loan_repayments_m->get_total_loan_paid($this->group->id);
                    $posts = array();
                    $amount_paid = array();
                    $amount_payable_to_date = array();
                    $projected_profit = array();
                    $loans = $this->loans_m->get_many_by(array(),$this->group->id);
                    foreach ($loans as $loan){
                        $posts[] = $this->loans_m->get_summation_for_invoice($loan->id);
                        $amount_paid[$loan->id] = $this->loan_repayments_m->get_loan_total_payments($loan->id);
                        $amount_payable_to_date[$loan->id] = $this->loans_m->loan_payable_and_principle_todate($loan->id);
                        $projected_profit[$loan->id] = $this->loans_m->get_projected_interest($loan->id,$amount_paid[$loan->id],$this->group->id);
                    }
                    $total_loan=0;
                    $total_interest=0;
                    $total_paid=0;
                    $total_balance=0;
                    $total_projected=0;
                    $total_outstanding_profit=0;
                    $total_profits=0;
                    $i=0;
                    $loans_summary = array();
                    foreach($posts as $post):
                        if(isset($post->id)):
                            $total_amount_payable_to_date=$amount_payable_to_date[$post->id]->todate_amount_payable?:0;
                            $principle_payable_todate = $amount_payable_to_date[$post->id]->todate_principle_payable?:0;
                            if((round($total_amount_payable_to_date-$amount_paid[$post->id])) <= 0){
                                $intere = $total_amount_payable_to_date - $principle_payable_todate;
                                $overpayments = $amount_paid[$post->id] - $total_amount_payable_to_date;
                                if($overpayments<0){
                                    $overpayments = '';
                                }
                                $due_inter = '';
                                $pen = ($post->total_amount_payable) - ($post->total_interest_payable+$post->total_principle_payable);
                                if($pen>0){
                                    $penalty = $pen;
                                }
                                else{
                                    $penalty = 0;
                                }
                            }  
                            else{
                                $intere = '';
                                $overpayments = '';
                                $penalty = ($post->total_amount_payable) - ($post->total_interest_payable+$post->total_principle_payable);
                            }

                            $loans_summary[] = array(
                                'position' => ++$i,
                                'member' => $this->group_member_options[$post->member_id],
                                'disbursement_date' => timestamp_to_datepicker($post->disbursement_date).' - '.timestamp_to_datepicker($post->loan_end_date),
                                'amount' => ($loan = $post->loan_amount),
                                'interest' => ($interest = $post->total_interest_payable),
                                'amount_paid' => ($paid = $amount_paid[$post->id]),
                                'balance' => ($balance = $post->total_amount_payable - $paid),
                                'projected_profit' => ($profit = $projected_profit[$post->id]),
                                'outstanding_profit' => ($outstanding_profit = round(($post->total_interest_payable+$penalty)-$profit)),
                                'projected_profits' => ($projected_profits = $post->total_interest_payable+$penalty),
                            );
                            $total_loan+=$loan; 
                            $total_interest+=$interest;
                            $total_paid+=$paid;
                            $total_balance+=$balance; 
                            $total_profits+=$profit; 
                            $total_projected+=$projected_profits; 
                            $total_outstanding_profit+=$outstanding_profit;
                        endif;
                    endforeach;
                    $statement_footer = array(
                        'total_loan' => $total_loan,
                        'total_interest' => $total_interest,
                        'total_paid' => $total_paid,
                        'total_balance' => $total_balance,
                        'total_profits' => $total_profits,
                        'total_projected_profit' => $total_projected,
                        'total_outstanding_profit' => $total_outstanding_profit,
                    );
                    $response = array(
                        'status' => 1,
                        'message' => 'successful',
                        'time' => time(),
                        'data' => array(
                            'statement_body' => $loans_summary,
                            'statement_footer' => $statement_footer,
                        ),
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function account_balances(){     
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->version_code>76){
                        $account_options = $this->accounts_m->get_active_group_accounts();
                        $account_balances = $this->accounts_m->get_group_account_balances_array($this->group->id);
                        if(!empty($account_options)){ 
                            $grand_total_balance = 0;
                            $category_account_balances_display = array();
                            foreach($account_options as $account_category => $accounts):
                                if($accounts){
                                    $account_balances_display = array();
                                    $total_balance = 0; $count=1; 
                                    foreach($accounts as $account):
                                        $account_id = $account->id;
                                        if(preg_match('/bank/i', $account_category)){
                                            $account_id = 'bank-'.$account->id;
                                        }else if(preg_match('/sacco/i', $account_category)){
                                            $account_id = 'sacco-'.$account->id;
                                        }else if(preg_match('/mobile/i', $account_category)){
                                            $account_id = 'mobile-'.$account->id;
                                        }else if(preg_match('/petty/i', $account_category)){
                                            $account_id = 'petty-'.$account->id;
                                        }
                                        $total_balance += isset($account_balances[$account_category][$account_id])?$account_balances[$account_category][$account_id]:0;
                                        $grand_total_balance += isset($account_balances[$account_category][$account_id])?$account_balances[$account_category][$account_id]:0;
                                        $account_balances_display[] = array(
                                            'position' => $count++,
                                            'account_name' => $account->full_name,
                                            'account_number' => isset($account->account_number)?$account->account_number:0,
                                            'account_balance' => isset($account_balances[$account_category][$account_id])?$account_balances[$account_category][$account_id]:0,
                                        );
                                    endforeach;
                                    $category_account_balances_display[] = array(
                                        'category_name' => $account_category,
                                        'total_balance' => $total_balance,
                                        'account_balances' => $account_balances_display,
                                    );
                                }
                            endforeach;
                        }
                    }else{
                        $account_options = $this->accounts_m->get_active_group_account_options();
                        $account_balances = $this->accounts_m->get_group_account_balances_array($this->group->id);
                        if(!empty($account_options)){ 
                            $grand_total_balance = 0;
                            $category_account_balances_display = array();
                            foreach($account_options as $account_category => $accounts):
                                if($accounts){
                                    $account_balances_display = array();
                                    $total_balance = 0; $count=1; 
                                    foreach($accounts as $account_id => $account_name):
                                        $total_balance += $account_balances[$account_category][$account_id];
                                        $grand_total_balance += $account_balances[$account_category][$account_id];
                                        $account_balances_display[] = array(
                                            'position' => $count++,
                                            'account_name' => $account_name,
                                            'account_balance' => ($account_balances[$account_category][$account_id])?:0,
                                        );
                                    endforeach;
                                    $category_account_balances_display[] = array(
                                        'category_name' => $account_category,
                                        'total_balance' => $total_balance,
                                        'account_balances' => $account_balances_display,
                                    );
                                }
                            endforeach;
                        }
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'successful',
                        'time' => time(),
                        'data' => array(
                            'balances' => $category_account_balances_display,
                            'grand_total_balance' => $grand_total_balance,
                        ),
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function expenses_summary(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $expense_category_options = $this->expense_categories_m->get_group_expense_category_options($this->group->id);
                    $group_expense_category_totals = $this->withdrawals_m->get_group_expense_category_totals_array($this->group->id);
                    $total_expenses = 0; 
                    $total_arrears = 0; 
                    $count = 1; 
                    $expenses = array();
                    foreach($group_expense_category_totals as $expense_category_id => $group_expense_category_total):
                        $total_expenses += $group_expense_category_total;
                        if($group_expense_category_total){
                            $expenses[] = array(
                                'position' => ($count++),
                                'expense_name' => $expense_category_options[$expense_category_id]??'',
                                'amount' => $group_expense_category_total,
                            );
                        }
                    endforeach;
                    $response = array(
                        'status' => 1,
                        'message' => 'successful',
                        'time' => time(),
                        'data' => array(
                            'expenses' => $expenses,
                            'total_expenses' => $total_expenses,
                        ),
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function bank_loans_summary(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $bank_loans_summary = array();
                    $total_loan_received_and_repaid = $this->bank_loans_m->total_loan_received_and_paid();
                    $posts = $this->bank_loans_m->get_group_bank_loans('',$this->group->id);
                    $account_options = $this->accounts_m->get_group_account_options(FALSE,'',$this->group->id);
                    $statement_footer = array(
                        'total_received' => $total_loan_received_and_repaid->total_amount_received?:0,
                        'total_repaid' => $total_loan_received_and_repaid->total_amount_repaid?:0,
                        'total_arrears' => $total_loan_received_and_repaid->total_arrears?:0,
                    );

                    foreach ($posts as $post) {
                        $bank_loans_summary[] = array(
                            'description' => $post->description,
                            'start_date' => timestamp_to_report_time($post->loan_start_date),
                            'end_date' => timestamp_to_report_time($post->loan_end_date),
                            'account' => $account_options[$post->account_id],
                            'is_active' => $post->active?1:0,
                            'is_fully_paid' => $post->is_fully_paid,
                            'loaned_amount' => $post->amount_loaned?:0,
                            'amount_payble' => $post->total_loan_amount_payable?:0,
                            'loan_balance' => $post->loan_balance?:0,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'successful',
                        'data' => array(
                            'statement_body' => $bank_loans_summary,
                            'statement_footer' => $statement_footer,
                        ),
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function transaction_statement(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-4 months');
                    $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
                    $account_options = $this->accounts_m->get_group_account_options(FALSE,'',$this->group->id);
                    $account_list_ids = '0';
                    $count = 1;
                    foreach ($account_options as $account_id => $account_name) {
                        if($account_id){
                            if($count==1){
                                $account_list_ids='"'.$account_id.'"';
                            }else{
                                $account_list_ids.=',"'.$account_id.'"';
                            }
                            $count++;
                        }
                    }
                    $starting_balance = $this->transaction_statements_m->get_starting_balance($from,$account_list_ids,$to);
                    $transaction_names = $this->transactions->transaction_names;
                    $posts = $this->transaction_statements_m->get_group_transaction_statement($from,$account_list_ids,$this->group->id,0,'',0,$to);
                    $currency = $this->countries_m->get_group_currency_name($this->group->id);
                    $contribution_options = $this->contributions_m->get_group_contribution_options($this->group->id);
                    $fine_category_options = $this->fine_categories_m->get_group_options('',$this->group->id);
                    $income_category_options = $this->income_categories_m->get_group_income_category_options($this->group->id);
                    $expense_category_options = $this->expense_categories_m->get_group_expense_category_options($this->group->id);
                    $stock_sale_options = $this->stocks_m->get_group_stock_sale_options($this->group->id,$currency);
                    $depositor_options = $this->depositors_m->get_group_depositor_options($this->group->id);
                    $bank_loan_options = $this->bank_loans_m->get_group_bank_loan_options($this->group->id);
                    $loan_options = $this->loans_m->get_group_mobile_loan_options($this->group->id);
                    $external_lending_loan_options = $this->debtors_m->get_group_loan_options($this->group->id);
                    $asset_options = $this->assets_m->get_group_asset_options($this->group->id);
                    $stock_purchase_options = $this->withdrawals_m->get_group_stock_purchase_options($this->group->id);
                    $money_market_investment_options = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$currency);
                    $group_member_options = $this->members_m->get_group_member_options($this->group->id);
                    $group_debtor_options = $this->debtors_m->get_options($this->group->id);
                    $statement_details = array(
                        'statement_as_at' => timestamp_to_mobile_report_time(time(),TRUE),
                        'statement_period_from' => timestamp_to_mobile_report_time($from,TRUE),
                        'statement_period_to' => timestamp_to_mobile_report_time($to,TRUE),
                    );
                    $statement_header = array(
                        'description' => 'Balance B/F',
                        'date'  =>  timestamp_to_mobile_report_time($from,TRUE),
                        'withdrawn' => 0,
                        'deposited' => 0,
                        'balance' => $starting_balance?:0,
                    );
                    $body = array();
                    $balance = $starting_balance;
                    foreach($posts as $post): 
                        $withdrawn = 0;
                        $deposited = 0;
                        $description = '';
                        $transaction_type= 0;
                        $transaction_date = '';
                        if(in_array($post->transaction_type,$this->transactions->deposit_transaction_types)){
                            $balance+=$post->amount;
                            $transaction_date = timestamp_to_mobile_report_time($post->transaction_date);
                            //$description = $transaction_names[$post->transaction_type];
                            if($post->transaction_type){
                                $transaction_type = $post->transaction_type;
                            }
                            if(in_array($post->transaction_type,$this->transactions->contribution_payment_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' from '.$group_member_options[$post->member_id].' for '.$contribution_options[$post->contribution_id].' to '.$account_options[$post->account_id]; 
                                if($post->description){
                                    $description.=':'.$post->description;
                                }
                            }else if(in_array($post->transaction_type,$this->transactions->fine_payment_transaction_types)){
                                $for = isset($contribution_options[$post->contribution_id])?$contribution_options[$post->contribution_id]:
                                $fine_category_options[$post->fine_category_id];
                                $description.= $transaction_names[$post->transaction_type].' from '.$group_member_options[$post->member_id].' for '.$for.' to '.$account_options[$post->account_id]; 
                                if($post->description){
                                    $description.=' : '.$post->description;
                                }
                            }else if(in_array($post->transaction_type,$this->transactions->miscellaneous_payment_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' from '.$group_member_options[$post->member_id].' to '.$account_options[$post->account_id].' for '; 
                                if($post->description){
                                    $description.= ' '.$post->description;
                                }
                            }else if(in_array($post->transaction_type,$this->transactions->income_deposit_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' from '.$depositor_options[$post->depositor_id].' to '.$account_options[$post->account_id].' for '.$income_category_options[$post->income_category_id]; 
                                if($post->description){
                                    $description.= ' : '.$post->description;
                                }
                            }else if(in_array($post->transaction_type,$this->transactions->stock_sale_deposit_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' of '.$stock_sale_options[$post->stock_sale_id].', deposited to '.$account_options[$post->account_id]; 
                                if($post->description){
                                    $description.= ' : '.$post->description;
                                }
                            }else if(in_array($post->transaction_type,$this->transactions->bank_loan_disbursement_deposit_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' for '.$bank_loan_options[$post->bank_loan_id].', deposited to '.$account_options[$post->account_id]; 
                                if($post->description){
                                    $description.= ' : '.$post->description;
                                }
                            }else if(in_array($post->transaction_type,$this->transactions->loan_repayment_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' by '.$group_member_options[$post->member_id].' for the loan of '.$loan_options[$post->loan_id].', deposited to '.$account_options[$post->account_id]; 
                                if($post->description){
                                    $description.= ' : '.$post->description;
                                }
                            }else if(in_array($post->transaction_type,$this->transactions->money_market_investment_cash_in_deposit_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' for '.$money_market_investment_options[$post->money_market_investment_id].', deposited to '.$account_options[$post->account_id]; 
                                if($post->description){
                                    $description.= ' : '.$post->description;
                                }
                            }else if(in_array($post->transaction_type,$this->transactions->asset_sale_deposit_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' of '.$asset_options[$post->asset_id].', deposited to '.$account_options[$post->account_id]; 
                                if($post->description){
                                    $description.= ' : '.$post->description;
                                }
                            }else if(in_array($post->transaction_type,$this->transactions->incoming_account_transfer_withdrawal_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' from ';
                                $description.= isset($account_options[$post->from_account_id])?$account_options[$post->from_account_id]:'';
                                $description.= ' to ';
                                $description.= isset($account_options[$post->to_account_id])?$account_options[$post->to_account_id]:''; 
                                if($post->description){
                                    $description.= ' : '.$post->description;
                                }
                            }else if(in_array($post->transaction_type,$this->transactions->statement_loan_processing_income_deposit_transaction_types)){
                                $description.= 'Charged on Loan disbursed to '.$group_member_options[$post->member_id];
                               
                            }else if(in_array($post->transaction_type,$this->transactions->statement_external_lending_processing_income_transaction_types)){
                                $description.= 'Charged on Loan disbursed to '.$group_debtor_options[$post->debtor_id];
                               
                            }else if(in_array($post->transaction_type,$this->transactions->statement_external_lending_loan_repayment_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' by '.$group_debtor_options[$post->debtor_id];
                                if(isset($external_lending_loan_options[$post->debtor_loan_id])){
                                    $description.= ' for the loan of '.$external_lending_loan_options[$post->debtor_loan_id];
                                }
                                $description.= ', deposited to '.$account_options[$post->account_id]; 
                                if($post->description){
                                    $description.= ' : '.$post->description;
                                }
                            }

                            if($post->transaction_alert_id){
                                $description.= ' - Reconciled ';
                            }

                            $withdrawn = 0;
                            $deposited = $post->amount;
                            $balance = $balance;
                        }else if(in_array($post->transaction_type,$this->transactions->withdrawal_transaction_types)){
                            $balance-=$post->amount;
                            $transaction_date = timestamp_to_mobile_report_time($post->transaction_date);
                           // $description = $transaction_names[$post->transaction_type];
                            if(in_array($post->transaction_type,$this->transactions->statement_expense_withdrawal_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' for '.$expense_category_options[$post->expense_category_id].',withdrawn from ';
                                $description.= isset($account_options[$post->account_id])?$account_options[$post->account_id]:''; 
                                if($post->description){
                                    $description.= ' : '.$post->description;
                                }
                            }else if(in_array($post->transaction_type,$this->transactions->statement_stock_purchase_withdrawal_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' for '.$stock_purchase_options[$post->stock_id].', withdrawn from '.$account_options[$post->account_id]; 
                                if($post->description){
                                    $description.= ' : '.$post->description;
                                }
                            }else if(in_array($post->transaction_type,$this->transactions->statement_loan_disbursement_withdrawal_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' to '.$group_member_options[$post->member_id].', withdrawn from '.$account_options[$post->account_id]; 
                                if($post->description){
                                    $description.= ' : '.$post->description;
                                }
                            }else if(in_array($post->transaction_type,$this->transactions->statement_money_market_investment_withdrawal_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' for '.$money_market_investment_options[$post->money_market_investment_id].', withdrawn from '.$account_options[$post->account_id]; 
                                if($post->description){
                                    $description.= ' : '.$post->description;
                                }
                            }else if(in_array($post->transaction_type,$this->transactions->statement_asset_purchase_withdrawal_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' for '.$asset_options[$post->asset_id].', withdrawn from '.$account_options[$post->account_id]; 
                                if($post->description){
                                    $description.= ' : '.$post->description;
                                }
                            }else if(in_array($post->transaction_type,$this->transactions->statement_contribution_refund_withdrawal_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' to '.$group_member_options[$post->member_id].' from '.$contribution_options[$post->contribution_id].', withdrawn from '.$account_options[$post->account_id]; 
                                if($post->description){
                                    $description.= ' : '.$post->description;
                                }
                            }else if(in_array($post->transaction_type,$this->transactions->statement_contribution_refund_withdrawal_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' to '.$group_member_options[$post->member_id].' from '.$contribution_options[$post->contribution_id].', withdrawn from '.$account_options[$post->account_id]; 
                                if($post->description){
                                    $description.= ' : '.$post->description;
                                }
                            }else if(in_array($post->transaction_type,$this->transactions->statement_bank_loan_repayment_withdrawal_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' to '.$bank_loan_options[$post->bank_loan_id].', withdrawn from '.$account_options[$post->account_id]; 
                                if($post->description){
                                    $description.= ' : '.$post->description;
                                }
                            }else if(in_array($post->transaction_type,$this->transactions->statement_outgoing_account_transfer_withdrawal_transaction_types)){
                                $description.= $transaction_names[$post->transaction_type].' from ';
                                $description.= isset($account_options[$post->from_account_id])?$account_options[$post->from_account_id]:'';
                                $description.= ' to ';
                                $description.= isset($account_options[$post->to_account_id])?$account_options[$post->to_account_id]:''; 
                                if($post->description){
                                    $description.= ' : '.$post->description;
                                }
                                
                            }else if(in_array($post->transaction_type,$this->transactions->statement_external_lending_withdrawal_transaction_types)){
                                if($post->debtor_id){
                                   echo $transaction_names[$post->transaction_type].' to '.$group_debtor_options[$post->debtor_id].', withdrawn from '.$account_options[$post->account_id]; 
                                    if($post->description){
                                        echo ' : '.$post->description;
                                    } 
                                }
                            }
                            if($post->transaction_alert_id){
                                $description.= '  Reconciled ';
                            }
                            $withdrawn = $post->amount;
                            if($post->transaction_type){
                                $transaction_type = $post->transaction_type;
                            }
                            $deposited = 0;
                            $balance = $balance;
                        }
                        $body[] = array(
                            'transaction_date' => $transaction_date,
                            'description' => $description,
                            'withdrawn' => $withdrawn,
                            'transaction_type' =>$transaction_type,
                            'deposited' => $deposited,
                            'balance' => $balance,
                        );
                    endforeach;
                    $statement_footer = array(
                        'description' => 'Totals',
                        'balance' => $balance,
                    );

                    $response = array(
                        'status' => 1,
                        'message' => 'successful',
                        'data' => array(
                            'statement_details' => $statement_details,
                            'statement_header'  => $statement_header,
                            'statement_body'   => $body,
                            'statement_footer'  => $statement_footer,
                        ),
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

}
?>