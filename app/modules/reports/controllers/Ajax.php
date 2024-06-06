<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{

    protected $data = array();
	
	function __construct(){
        parent::__construct();
        set_time_limit(0);
        ini_set('memory_limit','2048M');
        ini_set('max_execution_time', 1200);
        $this->load->library('transactions');
        $this->load->model('reports_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('contributions/contributions_m');
        $this->load->model('fine_categories/fine_categories_m');
        $this->load->model('income_categories/income_categories_m');
        $this->load->model('expense_categories/expense_categories_m');
        $this->load->model('depositors/depositors_m');
        $this->load->model('deposits/deposits_m');
        $this->load->model('stocks/stocks_m');
        $this->load->model('loans/loans_m');
        $this->load->model('loan_repayments/loan_repayments_m');
        $this->load->model('assets/assets_m');
        $this->load->model('bank_loans/bank_loans_m');
        $this->load->model('money_market_investments/money_market_investments_m');
        $this->load->model('transaction_statements/transaction_statements_m');
        $this->load->library('pdf_library');
    }

    public function get_account_balances(){
        $account_options = $this->accounts_m->get_active_group_account_options(TRUE,'','','',FALSE);
        $account_balances = $this->accounts_m->get_group_account_balances_array($this->group->id);
        if(!empty($account_options)){ 
            $grand_total_balance = 0;
            foreach($account_options as $account_category => $accounts):
                if($accounts){
                    echo '<h4>'.$account_category.'</h4>';
                    echo '
                <div class="">
                    <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement">
                        <thead>
                            <tr>
                                <th width="8px">
                                    #
                                </th>
                                <th>
                                    ' . translate('Account Name') .'
                                </th>
                                <th class="text-right">
                                    Balance ('.$this->group_currency.')
                                </th>
                            </tr>
                        </thead>
                        <tbody>';
                            $total_balance = 0; $count=1; foreach($accounts as $account_id => $account_name): 
                            echo '
                                <tr>
                                    <td>
                                        '.$count++.'
                                    </td>
                                    <td>
                                        '.$account_name.'
                                    </td>
                                    <td class=" bold theme-font text-right">';
                                        
                                            $total_balance += $account_balances[$account_category][$account_id];
                                            $grand_total_balance += $account_balances[$account_category][$account_id];
                                            echo number_to_currency($account_balances[$account_category][$account_id]);  
                                    echo '
                                    </td>
                                </tr>';
                            endforeach;
                            echo '
                            <tr>
                                <td>#</td>
                                <td>Total</td>
                                <td class=" bold theme-font text-right">'.number_to_currency($total_balance).'</td>
                            </tr>
                        </tbody>
                    </table>
                </div>';
                }
            endforeach;
        }else{ 
            echo '
            <div class="m-alert m-alert--outline alert alert-info fade show" role="alert"">
                <strong>'.translate('Information').'!</strong> '.translate('No account balances to display').'.
            </div>';
        }     
    }

    function get_contributions_summary(){
        // $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-1 year',time());
        // $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $member_ids = ($this->input->get('member_ids'));
        if($member_ids){
            $member_options = array_flip(array_filter($member_ids))?:$this->active_group_member_options;
        }else{
            $member_options = $this->active_group_member_options;
        }
        
        $contribution_options = $this->contributions_m->get_group_contribution_options();
        if($contribution_options){
            // $total_contribution_arrears_per_contribution_per_member_array = $this->statements_m->get_group_member_total_contribution_arrears_per_contribution_per_member_array($this->group->id);
            // $total_contribution_paid_per_contribution_per_member_array = $this->statements_m->get_group_member_total_contribution_paid_per_contribution_per_member_array($this->group->id);
            // print_r($total_contribution_paid_per_contribution_per_member_array);die;
            $contribution_list = '';
            foreach ($contribution_options as $id=>$name) {
                if($contribution_list){
                    $contribution_list.=','.$id;
                }else{
                    $contribution_list=$id;
                }
            }
            $total_members_deposit_amounts = $this->statements_m->get_group_members_total_paid_by_contribution_array($this->group->id,$contribution_list);
            // adding the arrears column
            $group_member_total_cumulative_contribution_arrears_per_member_array = $this->statements_m->get_group_member_total_cumulative_contribution_arrears_per_member_array($this->group->id);
            
            echo'
            <div class="">
                <table class="table table-striped- table-bordered table-hover table-checkable" id="m_table_2">
                    <thead>
                        <tr>
                            <th width="8px">#</th>
                            <th class="member_number" >#</th>
                            <th class="member_name" >'.translate('Member Name').'</th>
                            <th class="" >'.translate('Status').'</th>';
                            $grand_total_balance = array();
                            foreach ($contribution_options as $contribution_id => $contribution_name) {
                                $grand_total_balance[$contribution_id] = 0;
                                echo '
                                    <th class="text-right">'.$contribution_name.'</th>
                                ';
                            }
                            if($this->group->disable_arrears){

                            }else{
                                echo '<th class="text-right">'.translate('Arrears').' ('.$this->group_currency.')</th>';
                            }

                        echo '
                            <th class="text-right">Totals</th>';
                            if(count($contribution_options)>4){
                                echo '<th>'.translate('Member Name').'</th>';
                            }
                        echo'
                        </tr>
                    </thead>
                    <tbody>';
                        // $i = 1; 
                        // foreach ($this->active_group_member_options as $member_id => $name): 
                        //     print_r($total_members_deposit_amounts[$member_id]);die;
                        // //     if($this->group->disable_arrears){
                        // //     }else{
                        // //         $arrears = ($group_member_total_cumulative_contribution_arrears_per_member_array[$member_id]);
                        // //         if($arrears>0){
                        // //             $arrears_class = ' font-red-mint text-danger';
                        // //         }elseif($arrears<0){
                        // //             $arrears_class = 'text-success';
                        // //         }else{
                        // //             $arrears_class= '';
                        // //         }
                        //     }


                        // $group_member_total_cumulative_contribution_arrears_per_member_array
                        // extract the arrears of each member
                        

                        $count = 1;                      
                        foreach ($this->group_member_detail_options as $key => $member) {
                            $member_name = $member->first_name .' '.$member->last_name;
                            $member_id = $member->id;
                            $total_amount = 0;
                            $status = $member->active?'Active':'Suspended';
                            $membership_number = $member->membership_number?$member->membership_number:'-';
                            $arrears = $group_member_total_cumulative_contribution_arrears_per_member_array[$member_id];

                            if($arrears>0){
                                $arrears_class = ' font-red-mint text-danger';
                            }elseif($arrears<0){
                                $arrears_class = 'text-success';
                            }else{
                                $arrears_class= '';
                            }
                            // 

                            echo '
                            <tr>
                                <td>'.$count.'</td>  
                                <td>'.$membership_number.'</td>                              
                                <td style="width: 100.5px !important;" class="member_name"><a href="'.site_url('group/members/view/'.$member_id).'">'.$member_name.'</a></td>                                
                                <td>'.$status.'</td>';
                                foreach ($contribution_options as $contribution_id => $contribution_name) {
                                    $amount = isset($total_members_deposit_amounts[$member_id][$contribution_id])?$total_members_deposit_amounts[$member_id][$contribution_id]:0;
                                    $total_amount+=$amount;
                                    $grand_total_balance[$contribution_id]+=$amount;
                                    echo '
                                        <td class="text-right">'.number_to_currency($amount).'</td>
                                    ';
                                }
                                if($this->group->disable_arrears){

                                }else{
                                    echo '
                                    <td class="'.$arrears_class.' text-right">'.number_to_currency($group_member_total_cumulative_contribution_arrears_per_member_array[$member_id]).'</td>';
                                }
                            echo '
                                <th class="text-right">'.number_to_currency($total_amount).'</th>';
                            if(count($contribution_options)>4){
                                echo '<td><a href="'.site_url('group/members/view/'.$member_id).'">'.$member_name.'</a></td>';
                            }
                            echo'
                            </tr>
                            ';

                            $count++;
                        }
                    echo '
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="member_name">Grand Total</th>';
                            $sum_total = 0;
                            foreach ($contribution_options as $contribution_id => $contribution_name){
                                $sum_total +=$grand_total_balance[$contribution_id];
                                echo '<th class="text-right">'.number_to_currency($grand_total_balance[$contribution_id]).'</th>';
                            }

                            if($this->group->disable_arrears){

                            }else{
                                echo '<th></th>';
                            }
                        echo'
                            <th class="text-right">'.number_to_currency($sum_total).'</th>';
                            if(count($contribution_options)>4){
                                echo '<th></th>';
                            }
                            echo '
                        </tr>
                    </tfoot>
                </table>
            </div>';
        }else{
            echo '
            <div class="m-alert m-alert--outline alert alert-info fade show" role="alert"">
                <strong>'.translate('Information').'!</strong> '.translate('No contributions to display').'
            </div>';
        }
    }

    function get_contributions_minus_expenses_summary(){
        $total_cumulative_contribution_paid_per_member_array = $this->statements_m->get_group_member_total_cumulative_contribution_paid_per_member_array($this->group->id);
        $total_cumulative_contribution_arrears_per_member_array = $this->statements_m->get_group_member_total_cumulative_contribution_arrears_per_member_array($this->group->id);
        $total_group_expenses = $this->withdrawals_m->get_group_total_expenses();
        $expense_per_member = $total_group_expenses/count($this->active_group_member_options);
        if($total_cumulative_contribution_paid_per_member_array&&$total_cumulative_contribution_arrears_per_member_array){ 
            echo '
            <div class="">
                <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement" id="m_table_2">
                    <thead>
                        <tr role="row" class="heading">
                            <th width="3px">#</th>';
                            if($this->show_membership_number){
                                echo '<th>#</th>';
                            }
                            echo '
                            <th>Name</th>
                            <th class="text-right">'.translate('Paid').' ('.$this->group_currency.')</th>';
                            if($this->group->disable_arrears){
                            }else{
                                echo '<th class="text-right">'.translate('Arrears').' ('.$this->group_currency.')</th>';
                            }
                        echo '  
                        </tr>
                    </thead>
                    <tbody>';
                        $total_amount_paid = 0;
                        $total_amount_paid_minus_expenses = 0;
                        $total_arrears = 0;
                        $i = 1; foreach ($this->active_group_member_options as $member_id => $name): 
                            $amount_paid = $total_cumulative_contribution_paid_per_member_array[$member_id];
                            $arrears = $total_cumulative_contribution_arrears_per_member_array[$member_id];
                            $amount_paid_minus_expenses = $amount_paid - $expense_per_member;

                            $total_amount_paid += $amount_paid;
                            $total_amount_paid_minus_expenses += $amount_paid_minus_expenses;
                            $total_arrears +=$arrears;
                            $arrears_class = $arrears>0?' font-red-mint ':'';
                            echo '
                            <tr>
                                <td>'.$i++.'</td>';
                                if($this->show_membership_number){
                                    echo '<td>'.$this->membership_numbers[$member_id].'</td>';
                                }
                                echo '
                                <td><a href="'.site_url("group/members/view/".$member_id).'">'.$name.'</a></td>
                                <td class="text-right">'.number_to_currency($amount_paid_minus_expenses).'</td>';
                                if($this->group->disable_arrears){
                                }else{
                                    echo '<td class="'.$arrears_class.' text-right">'.number_to_currency($arrears).'</td>';
                                }
                                echo '
                            </tr>';
                        endforeach;
                    echo '
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>#</td>';
                            if($this->show_membership_number){
                                echo '<td></td>';
                            }
                        echo '
                            <td>Totals</td>
                            <td class="text-right">'.number_to_currency($total_amount_paid_minus_expenses).'</td>';
                            if($this->group->disable_arrears){
                            }else{
                                echo '<td class="text-right">'.number_to_currency($total_arrears).'</td>';
                            }
                            echo '
                        </tr>
                    </tfoot>
                </table>
            </div>
            <hr/>
            <strong>'.translate('Total Contributions').' : </strong>'.number_to_currency($total_amount_paid).'<br/>
            <strong>'.translate('Total Contributions Minus Expenses').' : </strong>'.number_to_currency($total_amount_paid_minus_expenses).'<br/>
            <strong>'.translate('Total Expenses').' : </strong>'.number_to_currency($total_group_expenses).'<br/>
            <strong>'.translate('Total Expenses per Member').' : </strong>'.number_to_currency($expense_per_member).'<br/>

            ';
        }else{
            echo '
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="m-alert m-alert--outline alert alert-info fade show" role="alert"">
                            <strong>'.translate('Information').'!</strong> '.translate('No contributions to display').'
                        </div>
                    </div>
                </div>
            </div>';
        }
    }

    function get_fines_summary(){
        $total_cumulative_fine_paid_per_member_array = $this->statements_m->get_group_member_total_cumulative_fine_paid_per_member_array($this->group->id);
        $total_cumulative_fine_arrears_per_member_array = $this->statements_m->get_group_member_total_cumulative_fine_arrears_per_member_array($this->group->id);
        echo '            
            <div class="table-responsive table-scrollable">
                <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement" id="m_table_2">
                    <thead>
                        <tr>
                            <th width="8px">
                                #
                            </th>
                            <th>
                                '.translate('Member Name').'
                            </th>
                            <th class="text-right">
                                '.translate('Paid').' ('.$this->group_currency.')
                            </th>
                            <th class="text-right">
                                '.translate('Arrears').' ('.$this->group_currency.')
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                        
                            $total_amount_paid = 0; 
                            $total_arrears = 0; 
                            $count = 1; 
                            foreach($this->group_member_options as $member_id => $member_name): 
                                $amount_paid = isset($total_cumulative_fine_paid_per_member_array[$member_id]) ? $total_cumulative_fine_paid_per_member_array[$member_id] : 0;
                                $arrears = isset($total_cumulative_fine_arrears_per_member_array[$member_id]) ? $total_cumulative_fine_arrears_per_member_array[$member_id] : 0;
                                $total_amount_paid += $amount_paid; 
                                $total_arrears += $arrears; 
                                echo '
                                <tr>
                                    <td>'.($count++).'</td>
                                    <td><a href="'.site_url('group/members/view/'.$member_id).'">'.$member_name.'</a></td>
                                    <td  class="text-right">'.number_to_currency($amount_paid).'</td>
                                    <td class="'.($arrears>0?' font-red-mint ':'').' text-right">'.number_to_currency($arrears).'</td>
                                </tr>';
                            
                            endforeach;
                            echo '
                        <tr>
                            <td>#</td>
                            <td>Totals</td>
                            <td class="text-right">'.number_to_currency($total_amount_paid).'</td>
                            <td class="text-right">'.number_to_currency($total_arrears).'</td>
                        </tr>
                    </tbody>
                </table>
            </div>';
    }

    public function get_loans_summary(){
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-10 year');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $member_ids = $this->input->get_post('member_ids')?:0;
        $this->group_member_options = $this->members_m->get_group_member_options();
        $loan_type_ids = $this->input->get_post('loan_type_ids')?:0;
        $total_loan_out = $this->loans_m->get_total_loaned_amount();
        $total_loan_paid = $this->loan_repayments_m->get_total_loan_paid();
        $external_lending_total_loan_out = $this->debtors_m->get_total_loaned_amount();
        $external_lending_total_loan_paid = $this->debtors_m->get_total_loan_paid();
        $loan_type_options = $this->loan_types_m->get_options();
        $base_where = array(
            'member_id' => $member_ids,
            'loan_type_id' => $loan_type_ids,
            'from' => $from,
            'to' => $to,
        );
        $amount_paid = array();
        $amount_payable_to_date = array();
        $projected_profit = array();
        $loans = $this->loans_m->get_many_by($base_where);
        $loan_ids = '0';
        foreach($loans as $loan){
            $loan_ids.=','.$loan->id;
        }
        $invoice_summations = $this->loans_m->get_loans_summation_for_ivoices_by_loan_ids($loan_ids);
        $amount_paid_to_group = $this->loan_repayments_m->get_loan_total_payments_by_loan_ids($loan_ids);
        $amounts_payable_to_date = $this->loans_m->loan_payable_and_principle_todate_by_loan_ids($loan_ids);
        $projected_profits=$this->loans_m->get_projected_interest_by_loan_ids($loan_ids,$amount_paid_to_group);

        $external_lending_post = array();
        $external_lending_amount_paid = array();
        $external_lending_amount_payable_to_date = array();
        $external_lending_projected_profit = array();
        $external_lending_total_loan_out = $this->debtors_m->get_total_loaned_amount();
        $external_lending_total_loan_paid = $this->debtors_m->get_total_loan_paid();
        $external_lending_loans = $this->debtors_m->get_many_by();
        foreach ($external_lending_loans as $loan){
            $external_lending_post[] = $this->debtors_m->get_summation_for_invoice($loan->id);
            $external_lending_amount_paid[$loan->id] = $this->debtors_m->get_loan_total_payments($loan->id);
            $external_lending_amount_payable_to_date[$loan->id] = $this->debtors_m->loan_payable_and_principle_todate($loan->id);
            $external_lending_projected_profit[$loan->id] = $this->debtors_m->get_projected_interest($loan->id,$external_lending_amount_paid[$loan->id]);
        }
        
        $html='<div class="invoice-content-2 bordered document-border">
                <div class="row invoice-head">
                    <div class="col-md-7 col-xs-6">
                        <div class="invoice-logo">
                            <img src="';
                            $html.=is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                            $html.='" alt="" class="group-logo image-responsive" /> 
                        </div>
                    </div>
                    <div class="col-md-5 col-xs-6 text-right">
                        <div class="company-address">
                            <span class="bold uppercase">'.$this->application_settings->application_name.'</span><br/>
                            '.nl2br($this->application_settings->application_address).'<br/>
                            <span class="bold">'.translate('Telephone').': </span>'.$this->application_settings->application_phone.'
                            <br/>
                            <span class="bold">'.translate('Email Address').': </span> '.$this->application_settings->application_email.'
                            <br/>
                        </div>
                    </div>
                </div>
                <hr/>
                
                <div class="row invoice-body">';
                    $total_loan=0;
                    $total_interest=0;
                    $total_paid=0;
                    $total_balance=0;
                    $total_projected=0;
                    $total_outstanding_profit=0;
                    $total_profits=0;
                    if(!empty($loans)){
                        $html.=
                        '<div class="col-xs-12 table-responsive ">
                            <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement">
                                <thead>
                                    <tr>
                                        <th class="invoice-title ">#</th>
                                        <th class="invoice-title">'.translate('Applicant').'</th>
                                        <th class="invoice-title">'.translate('Loan Type').'</th>
                                        <th class="invoice-title">'.translate('Loan Duration').'</th>
                                        <th class="invoice-title  text-right">'.translate('Amount Loaned').'</th>
                                        <th class="invoice-title  text-right">'.translate('Interest').'</th>
                                        <th class="invoice-title  text-right">'.translate('Amount Paid').'</th>
                                        <th class="invoice-title  text-right">'.translate('Arrears').'</th>
                                        <th class="invoice-title  text-right">'.translate('Profits').'</th>
                                        <th class="invoice-title  text-right">'.translate('Outstanding Profits').'</th>
                                        <th class="invoice-title  text-right">'.translate('Projected Profits').'</th>
                                        <th class="invoice-title  text-right">'.translate('Status').'</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    $i=0;
                                    foreach($loans as $post):
                                        if(isset($post->id)):
                                            $total_interest_payable = isset($invoice_summations[$post->id])?$invoice_summations[$post->id]->total_interest_payable:0;
                                            $total_amount_payable = isset($invoice_summations[$post->id])?$invoice_summations[$post->id]->total_amount_payable:0;
                                            $total_principle_payable = isset($invoice_summations[$post->id])?$invoice_summations[$post->id]->total_principle_payable:0;
                                            $amount_paid = isset($amount_paid_to_group[$post->id])?$amount_paid_to_group[$post->id]:0;
                                            
                                            $total_amount_payable_to_date = isset($amounts_payable_to_date[$post->id])?$amounts_payable_to_date[$post->id]->todate_amount_payable:0;
                                            $principle_payable_todate  = isset($amounts_payable_to_date[$post->id])?$amounts_payable_to_date[$post->id]->todate_principle_payable:0;

                                            // $total_amount_payable_to_date=$amount_payable_to_date[$post->id]->todate_amount_payable?:0;
                                            // $principle_payable_todate = $amount_payable_to_date[$post->id]->todate_principle_payable?:0;
                                            if((round($total_amount_payable_to_date-$amount_paid)) <= 0){
                                                $intere = $total_amount_payable_to_date - $principle_payable_todate;
                                                $overpayments = $amount_paid - $total_amount_payable_to_date;
                                                if($overpayments<0){
                                                    $overpayments = '';
                                                }
                                                $due_inter = '';
                                                $pen = ($total_amount_payable) - ($total_interest_payable+$total_principle_payable);
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
                                                $penalty = ($total_amount_payable) - ($total_interest_payable+$total_principle_payable);
                                            }

                                            $html.='
                                            <tr>
                                                <td> <small>'.++$i.'</small></td>
                                                <td nowrap> <small>'.$this->group_member_options[$post->member_id].'</small></td>
                                                <td nowrap> <small>'.(isset($loan_type_options[$post->loan_type_id])?$loan_type_options[$post->loan_type_id]:'---').'</small></td>
                                                <td nowrap> <small>'.timestamp_to_date($post->disbursement_date).' - '.timestamp_to_date(strtotime("+".$post->repayment_period." months", $post->disbursement_date)).'</small></td>
                                                <td class="text-right"> <small>';
                                                    $loan = $post->loan_amount;
                                                    $html.=number_to_currency($loan).
                                                '</small></td>
                                                <td class="text-right"><small>';
                                                    $html.=number_to_currency($total_interest_payable).
                                                '</small></td>
                                                <td class="text-right"><small>';
                                                    $paid = $amount_paid;
                                                    $html.= number_to_currency($paid).
                                                '</small></td>
                                                <td class="text-right "><small>
                                                    <span class="tooltips" data-original-title="Interest Breakdown" data-content="Overpayment : '.number_to_currency($overpayments).' , Penalties : '.number_to_currency($penalty).'">';
                                                    $balance = $total_amount_payable - $paid;
                                                    $html.=number_to_currency($balance);
                                                    $html.=
                                                    '</span></small>
                                                </td>
                                                <td class="text-right"><small>';
                                                    $profit = isset($projected_profit[$post->id])?$projected_profit[$post->id]:0;
                                                    $html.=number_to_currency($profit);
                                                $html.='</small>
                                                </td>
                                                <td class="text-right"><small>';
                                                    $outstanding_profit = round(($total_interest_payable+$penalty)-$profit);
                                                    $html.=number_to_currency($outstanding_profit); 
                                                $html.='</small>
                                                </td>
                                                <td class="text-right"><small>';
                                                    $projected_profits = $total_interest_payable+$penalty;
                                                    $html.=number_to_currency($projected_profits).'</small>
                                                </td>
                                                <td><small>
                                                ';
                                                    if($post->is_a_bad_loan){
                                                        $html.='<span class="m-badge m-badge--warning m-badge--wide">'.translate('Bad Loan').'</span>';
                                                    }
                                                    else if($post->is_fully_paid){
                                                        $html.='<span class="m-badge m-badge--primary m-badge--wide">'.translate('Fully Paid').'</span>';
                                                    }
                                                    else{
                                                        $html.='<span class="m-badge m-badge--info m-badge--wide">'.translate('Active').'</span>';
                                                    }
                                                $html.= '</small>
                                                </td>
                                            </tr>';
                                                $total_loan+=$loan; 
                                                $total_interest+=$total_interest_payable;
                                                $total_paid+=$paid;
                                                $total_balance+=$balance; 
                                                $total_profits+=$profit; 
                                                $total_projected+=$projected_profits; 
                                                $total_outstanding_profit+=$outstanding_profit;
                                        endif;
                                    endforeach;
                                $html.='
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3">Totals</td>
                                        <td class="text-right">'.number_to_currency($total_loan).'</td>
                                        <td class="text-right">'.number_to_currency($total_interest).'</td>
                                        <td class="text-right">'.number_to_currency($total_paid).'</td>
                                        <td class="text-right">'.number_to_currency($total_balance).'</td>
                                        <td class="text-right">'.number_to_currency($total_profits).'</td>
                                        <td class="text-right">'.number_to_currency($total_outstanding_profit).'</td>
                                        <td class="text-right">'.number_to_currency($total_projected).'</td>
                                        <td class=""></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div> ';
                    }else{
                        $html.='
                        <div class="col-xs-12 margin-bottom-10 ">
                            <div class="m-alert m-alert--outline alert alert-info fade show" role="alert"">
                                <strong>'.translate('Information').'!</strong> '.translate('No Members loan records to display').'.
                            </div>
                        </div>';
                    }
                $html.='
                </div>

                <br/>';
                
                $external_lending_total_loan=0;
                $external_lending_total_interest=0;
                $external_lending_total_paid=0;
                $external_lending_total_balance=0;
                $external_lending_total_projected=0;
                $external_lending_total_outstanding_profit=0;
                $external_lending_total_profits=0;
                if($external_lending_post):
                    $html.='
                    <div class="clearfix"></div>
                    <br/>
                    <hr/>
                    <div class="row invoice-body">';
                        if(!empty($external_lending_post)){
                            $html.='<div class="col-xs-12 table-responsive ">
                                <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement">
                                    <thead>
                                        <tr>
                                            <th class="invoice-title ">#</th>
                                            <th class="invoice-title" width="17%">'.translate('Debtor').'</th>
                                            <th class="invoice-title">'.translate('Loan Duration').'</th>
                                            <th class="invoice-title  text-right">'.translate('Amount Loaned').'</th>
                                            <th class="invoice-title  text-right">'.translate('Interest').'</th>
                                            <th class="invoice-title  text-right">'.translate('Amount Paid').'</th>
                                            <th class="invoice-title  text-right">'.translate('Arrears').'</th>
                                            <th class="invoice-title  text-right">'.translate('Profits').'</th>
                                            <th class="invoice-title  text-right">'.translate('Outstanding Profits').'</th>
                                            <th class="invoice-title  text-right">'.translate('Projected Profits').'</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                        $i=0;
                                        foreach($external_lending_post as $post):
                                            if(isset($post->id)):
                                                $total_amount_payable_to_date=$external_lending_amount_payable_to_date[$post->id]->todate_amount_payable?:0;
                                                $principle_payable_todate = $external_lending_amount_payable_to_date[$post->id]->todate_principle_payable?:0;
                                                if((round($total_amount_payable_to_date-$external_lending_amount_paid[$post->id])) <= 0){
                                                    $intere = $total_amount_payable_to_date - $principle_payable_todate;
                                                    $overpayments = $external_lending_amount_paid[$post->id] - $total_amount_payable_to_date;
                                                    if($overpayments<0){
                                                        $overpayments = '';
                                                    }
                                                    $due_inter = '';
                                                    $pen = ($post->total_amount_payable) - ($post->total_interest_payable+$post->total_principle_payable);
                                                    if($pen>0){
                                                        $penalty = $pen;
                                                    }else{
                                                        $penalty = 0;
                                                    }
                                                }else{
                                                    $intere = '';
                                                    $overpayments = '';
                                                    $penalty = ($post->total_amount_payable) - ($post->total_interest_payable+$post->total_principle_payable);
                                                }
                                                $html.='
                                                <tr>
                                                    <td>'.
                                                        ++$i.'
                                                    </td>
                                                    <td>
                                                        '.$this->group_debtor_options[$post->debtor_id].'
                                                    </td>
                                                    <td>
                                                        '.timestamp_to_date($post->disbursement_date).' - '.timestamp_to_date(strtotime("+".$post->repayment_period." months", $post->disbursement_date)).'
                                                    </td>
                                                    <td class="text-right">';
                                                        $loan = $post->loan_amount;
                                                        $html.=number_to_currency($loan).'
                                                    </td>
                                                    <td class="text-right">
                                                        '.number_to_currency($interest = $post->total_interest_payable).'
                                                    </td>
                                                    <td class="text-right">
                                                        '.number_to_currency($paid = $external_lending_amount_paid[$post->id]).'
                                                    </td>
                                                    <td class="text-right ">
                                                        <span class="tooltips" data-original-title="Interest Breakdown" data-content="Overpayment : '.number_to_currency($overpayments).' , Penalties : '.number_to_currency($penalty).'">';
                                                        $balance = $post->total_amount_payable - $paid;
                                                        $html.=number_to_currency($balance).'</span>
                                                    </td>
                                                    <td class="text-right">';
                                                        $profit = $external_lending_projected_profit[$post->id];
                                                        $html.=number_to_currency($profit).'
                                                    </td>
                                                    <td class="text-right">';
                                                        $outstanding_profit = round(($post->total_interest_payable+$penalty)-$profit);
                                                        $html.=number_to_currency($outstanding_profit).'
                                                    </td>
                                                    <td class="text-right">';
                                                        $projected_profits = $post->total_interest_payable+$penalty;
                                                        $html.=number_to_currency($projected_profits).'
                                                    </td>
                                                </tr>';
                                                $external_lending_total_loan+=$loan; 
                                                $external_lending_total_interest+=$interest;
                                                $external_lending_total_paid+=$paid;
                                                $external_lending_total_balance+=$balance; 
                                                $external_lending_total_profits+=$profit; 
                                                $external_lending_total_projected+=$projected_profits; 
                                                $external_lending_total_outstanding_profit+=$outstanding_profit;
                                            endif;
                                        endforeach;
                                    $html.='
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3">'.translate('Totals').'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_loan).'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_interest).'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_paid).'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_balance).'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_profits).'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_outstanding_profit).'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_projected).'</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>'; 
                        }else{
                            $hntl.='
                            <div class="col-xs-12 margin-bottom-10 ">
                                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert"">
                                    <strong>'.translate('Information').'!</strong> '.translate('No Debtor loan records to display').'.
                                </div>
                            </div>';
                        } 
                    $html.='
                    <div class="row">
                        <div class="col-xs-12">
                            <a class="btn btn-sm blue hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class="fa fa-print"></i> Print</a>
                            
                        </div>
                    </div>
                    </div>';
                endif;

                $html.='
                <div class="row">
                    <div class="col-md-6 summary-details">
                        <h4>Loan Details</h4>
                        <span class="bold">'.translate('Total Loaned Amount').' : </span>'.$this->group_currency.' '.number_to_currency($total_loan+$external_lending_total_loan).'<br/>
                        <span class="bold">'.translate('Total Repaid Amount').' : </span> '.$this->group_currency.' '.number_to_currency($total_paid+$external_lending_total_paid).'<br/>
                        <span class="bold">'.translate('Total Loan Arrears').' : </span> '.$this->group_currency.' '.number_to_currency($total_balance+$external_lending_total_balance).'<br/>
                    </div>
                </div>
                <hr/>';
            $html.='
            </div>';

            echo $html;
    }

    function get_bank_loans_summary(){
        $total_loan_received_and_repaid = $this->bank_loans_m->total_loan_received_and_paid();
        $posts = $this->bank_loans_m->get_group_bank_loans();
        $group_total_bank_loan_repayments_per_bank_loan_array = $this->withdrawals_m->get_group_total_bank_loan_repayments_per_bank_loan_array();
        echo '
        <div class="row">
            <div class="col-lg-12">
                <div class="invoice-content-2 bordered document-border">
                    <div class="row invoice-head">
                        <div class="col-md-7 col-xs-6">
                            <div class="invoice-logo">
                                <img src="'.(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo)).'" alt="" class=\'group-logo image-responsive\' /> 
                            </div>
                        </div>
                        <div class="col-md-5 col-xs-6 text-right">
                            <div class="company-address">
                                <span class="bold uppercase">'.$this->group->name.'</span><br/>
                                '.nl2br($this->group->address).'<br/>
                                <span class="bold">'.translate('Telephone').': </span>'.$this->group->phone.'
                                <br/>
                                <span class="bold">'.translate('Email Address').': </span>'.$this->group->email.'
                                <br/>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-lg-6 summary-details">
                            <h4>'.translate('Bank Loan Details').'</h4>
                             <span class="bold">'.translate('Bank Loan Received').' : </span>'.$this->group_currency.' '.number_to_currency($total_received = $total_loan_received_and_repaid->total_amount_received).'<br/>
                            <span class="bold">'.translate('Bank Loan Repaid').' : </span>'.$this->group_currency.' '.number_to_currency($total_repaid=$total_loan_received_and_repaid->total_amount_repaid).'<br/>
                            <span class="bold">'.translate('Bank Loan Arrears').' : </span>'.$this->group_currency.' '.number_to_currency($total_loan_received_and_repaid->total_arrears).'<br/>
                        </div>
                        <div class="col-md-6">
                        </div>
                    </div>
                    <hr/>
                    <div class="row invoice-body">';
                        if(!empty($posts)){
                            echo '
                            <div class="col-lg-12 table-responsive ">
                                <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement">
                                    <thead>
                                        <tr>
                                            <th class="invoice-title ">#</th>
                                            <th class="invoice-title">'.translate('Description').'</th>
                                            <th class="invoice-title">'.translate('Loan Duration').'</th>
                                            <th class="invoice-title  text-right">'.translate('Amount Received').'</th>
                                            <th class="invoice-title  text-right">'.translate('Bank Interest').'</th>
                                            <th class="invoice-title  text-right">'.translate('Amount Payable').'</th>
                                            <th class="invoice-title  text-right">'.translate('Amount Paid').'</th>
                                            <th class="invoice-title  text-right">'.translate('Arrears').'</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                               $i=0;
                                                $total_loan=0;
                                                $total_interest=0;
                                                $total_paid=0;
                                                $total_balance=0;
                                                $total_payable=0;
                                                foreach ($posts as $post):
                                            echo '
                                            <tr>
                                                <td>'.(++$i).'</td>
                                                <td>'.$post->description.'</td>
                                                <td>'.timestamp_to_date($post->loan_start_date).' - '.timestamp_to_date($post->loan_end_date).'</td>
                                                <td class="text-right">'.number_to_currency($loan = $post->amount_loaned).'</td>
                                                <td class="text-right">'.number_to_currency($interest = $post->total_loan_amount_payable-$loan).'</td>
                                                <td class="text-right">'.number_to_currency($payable =$post->total_loan_amount_payable).'</td>
                                                <td class="text-right">'.number_to_currency($paid = $group_total_bank_loan_repayments_per_bank_loan_array[$post->id]).'</td>
                                                <td class="text-right">'.number_to_currency($balance = $post->loan_balance).'</td>
                                            </tr>';
                                               $total_loan+=$loan; 
                                                $total_interest+=$interest;
                                                $total_paid+=$paid;
                                                $total_balance+=$balance;
                                                $total_payable+=$payable;
                                                endforeach;
                                    echo '
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3">Totals</td>
                                            <td class="text-right">'.number_to_currency($total_loan).'</td>
                                            <td class="text-right">'.number_to_currency($total_interest).'</td>
                                            <td class="text-right">'.number_to_currency($total_payable).'</td>
                                            <td class="text-right">'.number_to_currency($total_paid).'</td>
                                            <td class="text-right">'.number_to_currency($total_balance).'</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>';
                        }else{
                            echo '
                            <div class="col-lg-12 margin-bottom-10 ">
                                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                                    <strong>'.translate('Ooops').'!</strong> '.translate('No loan records to display').'.
                                </div>
                            </div>';
                        }
                        echo'
                    </div>
                </div>
            </div>
        </div>';
    }

    function group_loans_summary_chart_data(){
        $loans = $this->loans_m->get_many_by();
        $first_day_this_month = strtotime("first day of this month");
        $first_day_last_month = strtotime("first day of last month");
        $last_month_amount_loaned = 0;
        $this_month_amount_loaned = 0;
        $projected_profit = 0;
        $defaulted_loan = 0;
        $total_arrears = 0;
        $total_amount_paid = 0;
        $total_amount_loaned = 0;
        $loan_ids = '0';
        foreach($loans as $loan){
            $loan_ids.=','.$loan->id;
            if($loan->is_a_bad_loan == 1){
                $defaulted_loan+=$loan->loan_amount;
            }
            if(date('M Y',$loan->disbursement_date) == date('M Y',$first_day_last_month)){
                $last_month_amount_loaned+=$loan->loan_amount;
            }
            if(date('M Y',$loan->disbursement_date) == date('M Y',$first_day_this_month)){
                $this_month_amount_loaned+=$loan->loan_amount;
            }
            $total_amount_loaned+=$loan->loan_amount;
        }
        $post = $this->loans_m->get_summation_for_ivoices_by_loan_ids($loan_ids);
        $amount_paid_group = $this->loan_repayments_m->get_loan_total_payments_by_loan_ids($loan_ids);
        $projected_profits=$this->loans_m->get_projected_interest_by_loan_ids($loan_ids,$amount_paid_group);

        $projected_profit = array_sum($projected_profits);
        $total_arrears= $post->total_amount_payable - array_sum($amount_paid_group);
        $total_amount_paid = array_sum($amount_paid_group);

        $external_lending_loans = $this->debtors_m->get_many_by();
        foreach ($external_lending_loans as $external_loan){
            $external_post = $this->debtors_m->get_summation_for_invoice($external_loan->id);
            if(date('M Y',$external_loan->disbursement_date) == date('M Y',$first_day_last_month)){
                $last_month_amount_loaned+=$external_loan->loan_amount;
            }
            if(date('M Y',$external_loan->disbursement_date) == date('M Y',$first_day_this_month)){
                $this_month_amount_loaned+=$external_loan->loan_amount;
            }
            $external_amount_paid = $this->debtors_m->get_loan_total_payments($external_loan->id);
            $projected_profit+=$this->debtors_m->get_projected_interest($external_loan->id,$external_amount_paid);
            $total_arrears+= $external_post->total_amount_payable - $external_amount_paid;
            $total_amount_paid+=$external_amount_paid;
            $total_amount_loaned+=$external_loan->loan_amount;
        }
        $percentage_loan = $last_month_amount_loaned?round(((($this_month_amount_loaned - $last_month_amount_loaned)/abs($last_month_amount_loaned))*100),2):0;
        $response = array(
            'amount_loaned' => number_to_currency($this_month_amount_loaned),
            'percentage_increase' => $percentage_loan,
            'defaulted_loan' => number_to_currency($defaulted_loan),
            'projected_profit' => number_to_currency($projected_profit),
            'total_arrears' => number_to_currency($total_arrears),
            'total_amount_paid' => number_to_currency($total_amount_paid),
            'percentage_repayments' => $total_amount_loaned?round((($total_amount_paid-$total_amount_loaned)/abs($total_amount_loaned)*100),2):0,
        );

        echo json_encode($response);die;
    }

    function get_expenses_summary(){
        $expense_category_options = $this->expense_categories_m->get_group_expense_category_options();
        $group_expense_category_totals = $this->withdrawals_m->get_group_expense_category_totals_array();
       $html= '<div class="invoice-content-2 bordered document-border">
        <div class="row invoice-head">
            <div class="col-md-7 col-xs-6">
                <div class="invoice-logo">
                    <img src="';
                    $html.=is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                    $html.='" alt="" class="group-logo image-responsive" /> 
                </div>
            </div>
            <div class="col-md-5 col-xs-6 text-right">
                <div class="company-address">
                    <span class="bold uppercase">'.$this->group->name.'</span><br/>
                    '.nl2br($this->group->address).'<br/>
                    <span class="bold">'.translate('Telephone').': </span>'.$this->group->phone.'
                    <br/>
                    <span class="bold">'.translate('Email Address').': </span> '.$this->group->email.'
                    <br/>
                </div>
            </div>
        </div>
        <hr/>';
        $html.= '
            <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement" id="m_table_2">
                <thead class="thead-inverse">
                    <tr>
                        <th>
                            #
                        </th>
                        <th>
                            '.translate('Expense Category').'
                        </th>
                        <th class="text-right">
                            '.translate('Paid').' ('.$this->group_currency.')
                        </th>
                    </tr>
                </thead>
                <tbody>';
                    
                        $total_expenses = 0; $total_arrears = 0; $count = 1; foreach($group_expense_category_totals as $expense_category_id => $group_expense_category_total): 
                        $total_expenses += $group_expense_category_total;
                        $html.= '
                        <tr>
                            <td>'.($count++).'</td>
                            <td>'.$expense_category_options[$expense_category_id].'</td>
                            <td  class="text-right">'.number_to_currency($group_expense_category_total).'</td>
                        </tr>';
                    endforeach;
                    $html.= '
                    <tr>
                        <td>#</td>
                        <td>'.translate('Totals').'</td>
                        <td class="text-right">'.number_to_currency($total_expenses).'</td>
                    </tr>
                </tbody>
            </table>
            <hr/>';
            $html.= "<strong>".translate('Total Expenses').":</strong> ".$this->group_currency.' '.number_to_currency($total_expenses);
           ;
           $expenses_available = 'false';
           if($total_expenses > 0){
               $expenses_available = 'true';
           }
        $html.= '<input class="expen_available" type="hidden" value="'.$expenses_available.'"/>';
        echo $html;
    }

    function get_expenses_categories_summary(){
        $expense_category_options = $this->expense_categories_m->get_group_expense_category_options();
        $group_expense_category_totals = $this->withdrawals_m->get_group_expense_category_totals_array();
        $total_expenses = 0; 
        $total_arrears = 0; 
        $count = 1; 
        $categories =  array();
        $amount = array();
        $i=0;
        $other_amounts = 0 ;
        foreach($group_expense_category_totals as $expense_category_id => $group_expense_category_total): 
            if($i>5){
                // $total_expenses += $group_expense_category_total;
                // $categories[] = $expense_category_options[$expense_category_id];
                // $amount[] = ($group_expense_category_total);
                $other_amounts+=($group_expense_category_total);
            }else{
                $name = $expense_category_options[$expense_category_id];
                $total_expenses += $group_expense_category_total;
                $categories[] = (strlen($name)>30)?substr($this->group->name,0,30).'...':$name;
                $amount[] = ($group_expense_category_total);
            }
            $i++;
        endforeach;
        if($other_amounts){
            $categories[] = 'Others';
            $amount[] = $other_amounts;
        }
        $response = array(
            'categories' => $categories,
            'amount' => $amount,
        );
        echo json_encode($response);
    }

    public function get_transaction_statement($generate_pdf=FALSE){
        
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-3 months');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $account_ids = $this->input->get('accounts');
        $account_list_ids = '0';
        $count = 1;
        $account_options = $this->accounts_m->get_active_group_account_options(FALSE,FALSE,TRUE,'',FALSE);
        if(empty($account_ids)){
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
        }else{
            foreach ($account_ids as $account_id) {
                if($account_id){
                    if($count==1){
                        $account_list_ids='"'.$account_id.'"';
                    }else{
                        $account_list_ids.=',"'.$account_id.'"';
                    }
                    $count++;
                }
            }
        }  
        $starting_balance = $this->transaction_statements_m->get_starting_balance($from,$account_list_ids,$to);
        $transaction_names = $this->transactions->transaction_names;
        $posts = $this->transaction_statements_m->get_group_transaction_statement($from,$account_list_ids,$this->group->id,0,'',0,$to);
        $account_options = $account_options;
        $contribution_options = $this->contributions_m->get_group_contribution_options();
        $fine_category_options = $this->fine_categories_m->get_group_options();
        $income_category_options = $this->income_categories_m->get_group_income_category_options();
        $expense_category_options = $this->expense_categories_m->get_group_expense_category_options();
        $stock_sale_options = $this->stocks_m->get_group_stock_sale_options();
        $depositor_options = $this->depositors_m->get_group_depositor_options();
        $bank_loan_options = $this->bank_loans_m->get_group_bank_loan_options();
        $loan_options = $this->loans_m->get_group_loan_options();
        $external_lending_loan_options = $this->debtors_m->get_group_loan_options();
        $asset_options = $this->assets_m->get_group_asset_options();
        $stock_purchase_options = $this->withdrawals_m->get_group_stock_purchase_options();
        $money_market_investment_options = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
        $html= '
            <div class="invoice-content-2 bordered document-border">
                <div class="row invoice-head">
                    <div class="col-md-7 col-xs-6">
                        <div class="invoice-logo">
                            <img src="';
                                $html.=is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                                $html.= '" alt="" class="group-logo image-responsive" /> 
                        </div>
                    </div>
                    <div class="col-md-5 col-xs-6 text-right">
                        <div class="company-address">
                            <span class="bold uppercase">'.$this->group->name.'</span><br/>';
                            $html.= nl2br($this->group->address);
                            $html.= '<br/>
                            <span class="bold">'.translate('Telephone').': </span> '.$this->group->phone.'
                            <br/>
                            <span class="bold">'.translate('Email Address').': </span> '.$this->group->email.'
                            <br/>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-center margin-top-10 margin-bottom-10">
                        '.ucfirst($this->group->name).'<br/>
                        Transaction Statement as at '.timestamp_to_report_time($from).' to '.timestamp_to_report_time($to).'
                    </div>
                </div>
                <hr/>
                <div class=" invoice-body">';
                    if(!empty($posts)){
                        $html.= '
                        <div class="col-xs-12 table-responsive ">
                            <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement">
                                <thead>
                                    <tr>
                                        <th class="invoice-title ">Date</th>
                                        <th width=\'50%\' class="invoice-title ">'.translate('Description').'</th>
                                        <th class="invoice-title  text-right">'.translate('Withdrawn').'('.$this->group_currency.')</th>
                                        <th class="invoice-title  text-right">'.translate('Deposited').'('.$this->group_currency.')</th>
                                        <th class="invoice-title  text-right">'.translate('Balance').'('.$this->group_currency.')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td nowrap>Balance B/F</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class=\'text-right\'>'.number_to_currency($starting_balance).'</td>
                                    </tr>';
                                        $balance = $starting_balance; foreach($posts as $post):  
                                            if(in_array($post->transaction_type,$this->transactions->deposit_transaction_types)){
                                            $balance+=$post->amount;
                                            $html.= '
                                        <tr>
                                            <td>'.timestamp_to_date($post->transaction_date).'</td>
                                            <td>';
                                                    $html.= '<strong>'.$transaction_names[$post->transaction_type].': </strong>';
                                                    if(in_array($post->transaction_type,$this->transactions->contribution_payment_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' from '.
                                                        $this->group_member_options[$post->member_id].' for '.
                                                        $contribution_options[$post->contribution_id].' to '.
                                                        $account_options[$post->account_id]; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->fine_payment_transaction_types)){
                                                        $for = isset($contribution_options[$post->contribution_id])?$contribution_options[$post->contribution_id]:
                                                        $fine_category_options[$post->fine_category_id];
                                                        $html.= $transaction_names[$post->transaction_type].' from '.$this->group_member_options[$post->member_id].' for '.$for.' to '.$account_options[$post->account_id]; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->miscellaneous_payment_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' from '.$this->group_member_options[$post->member_id].' to '.$account_options[$post->account_id].' for '; 
                                                        if($post->description){
                                                            $html.= ' '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->income_deposit_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' from '.$depositor_options[$post->depositor_id].' to '.$account_options[$post->account_id].' for '.$income_category_options[$post->income_category_id]; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->stock_sale_deposit_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' of '.$stock_sale_options[$post->stock_sale_id].', deposited to '.$account_options[$post->account_id]; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->bank_loan_disbursement_deposit_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' for '.$bank_loan_options[$post->bank_loan_id].', deposited to '.$account_options[$post->account_id]; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->loan_repayment_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' by 
                                                        '.$this->group_member_options[$post->member_id].' 
                                                        for the loan of '.$loan_options[$post->loan_id].', deposited to '.$account_options[$post->account_id]; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->money_market_investment_cash_in_deposit_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' for '.$money_market_investment_options[$post->money_market_investment_id].', deposited to '.$account_options[$post->account_id]; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->asset_sale_deposit_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' of '.$asset_options[$post->asset_id].', deposited to '.$account_options[$post->account_id]; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->incoming_account_transfer_withdrawal_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' from ';
                                                        $html.= isset($account_options[$post->from_account_id])?$account_options[$post->from_account_id]:'';
                                                        $html.= ' to ';
                                                        $html.= isset($account_options[$post->to_account_id])?$account_options[$post->to_account_id]:''; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->statement_loan_processing_income_deposit_transaction_types)){
                                                        $html.= 'Charged on Loan disbursed to '.$this->group_member_options[$post->member_id];
                                                    }else if(in_array($post->transaction_type,$this->transactions->statement_external_lending_processing_income_transaction_types)){
                                                        $html.= 'Charged on Loan disbursed to '.$this->group_debtor_options[$post->debtor_id];
                                                    }else if(in_array($post->transaction_type,$this->transactions->statement_external_lending_loan_repayment_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' by '.$this->group_debtor_options[$post->debtor_id];
                                                        if(isset($external_lending_loan_options[$post->debtor_loan_id])){
                                                            $html.= ' for the loan of '.$external_lending_loan_options[$post->debtor_loan_id];
                                                        }
                                                        $html.= ', deposited to '.$account_options[$post->account_id]; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }

                                                    if($post->transaction_alert_id){
                                                        $html.= '  <br/><span class="m-badge m-badge--brand m-badge--wide m-badge--rounded m-badge--warning"> Reconciled </span> ';
                                                    }
                                            $html.= '
                                            </td>
                                            <td></td>
                                            <td class=\' bold theme-font text-right\'>'.number_to_currency($post->amount).'</td>
                                            <td class=\'bold theme-font text-right\'>'.number_to_currency($balance).'</td>
                                        </tr>'; 
                                        }else if(in_array($post->transaction_type,$this->transactions->withdrawal_transaction_types)){
                                            $balance-=$post->amount;
                                        $html.= '
                                    <tr>
                                            <td>'.timestamp_to_date($post->transaction_date).'</td>
                                            <td>'; 
                                                    $html.= '<strong>'.$transaction_names[$post->transaction_type].': </strong>';
                                                    if(in_array($post->transaction_type,$this->transactions->statement_expense_withdrawal_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' for '.$expense_category_options[$post->expense_category_id].',withdrawn from ';
                                                        $html.= isset($account_options[$post->account_id])?$account_options[$post->account_id]:''; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->statement_stock_purchase_withdrawal_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' for '.$stock_purchase_options[$post->stock_id].', withdrawn from '.$account_options[$post->account_id]; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->statement_loan_disbursement_withdrawal_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' to '.$this->group_member_options[$post->member_id].', withdrawn from '.$account_options[$post->account_id]; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->statement_money_market_investment_withdrawal_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' for '.$money_market_investment_options[$post->money_market_investment_id].', withdrawn from '.$account_options[$post->account_id]; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->statement_asset_purchase_withdrawal_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' for '.$asset_options[$post->asset_id].', withdrawn from '.$account_options[$post->account_id]; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->statement_contribution_refund_withdrawal_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' to '.$this->group_member_options[$post->member_id].' from '.$contribution_options[$post->contribution_id].', withdrawn from '.$account_options[$post->account_id]; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->statement_contribution_refund_withdrawal_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' to '.$this->group_member_options[$post->member_id].' from '.$contribution_options[$post->contribution_id].', withdrawn from '.$account_options[$post->account_id]; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->statement_bank_loan_repayment_withdrawal_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' to '.$bank_loan_options[$post->bank_loan_id].', withdrawn from '.$account_options[$post->account_id]; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->statement_outgoing_account_transfer_withdrawal_transaction_types)){
                                                        $html.= $transaction_names[$post->transaction_type].' from ';
                                                        $html.= isset($account_options[$post->from_account_id])?$account_options[$post->from_account_id]:'';
                                                        $html.= ' to ';
                                                        $html.= isset($account_options[$post->to_account_id])?$account_options[$post->to_account_id]:''; 
                                                        if($post->description){
                                                            $html.= ' : '.$post->description;
                                                        }
                                                    }else if(in_array($post->transaction_type,$this->transactions->statement_external_lending_withdrawal_transaction_types)){
                                                        if($post->debtor_id){
                                                           $html.= $transaction_names[$post->transaction_type].' to '.$this->group_debtor_options[$post->debtor_id].', withdrawn from '.$account_options[$post->account_id]; 
                                                            if($post->description){
                                                                $html.= ' : '.$post->description;
                                                            } 
                                                        }
                                                    }
                                                    if($post->transaction_alert_id){
                                                        $html.= '  <br/><span class="m-badge m-badge--brand m-badge--wide m-badge--rounded m-badge--warning"> Reconciled </span> ';
                                                    }
                                                $html.= '
                                            </td>
                                            <td class=\'bold theme-font text-right\'>'.number_to_currency($post->amount).'</td>
                                            <td></td>
                                            <td class=\'bold theme-font text-right\'>'.number_to_currency($balance).'</td>
                                        </tr>';

                                        }
                                    endforeach; 

                                $html.= '
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>Totals</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class=\'bold theme-font text-right\'>'.number_to_currency($balance).'</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>';
                    }else{
                        $html.= '
                        <div class="col-xs-12 margin-bottom-10 ">
                            <div class="alert alert-info">
                                <h4 class="block">'.translate('Information! No records to display').'</h4>
                                <p>
                                    '.translate('No transactions to display').'.
                                </p>
                            </div>
                        </div>';
                    } 
                    $html.= '
                </div>
            </div>
        ';
        if($generate_pdf){
            $html_data = '
            <div id="statement_paper"  class="pt-3">
                <div id="transaction_statement">'.$html.'
                </div>
                <div id="statement_footer" style="display: none;">
                    <p style="text-align:center;">© '.date('Y').'. This statement was issued with no alteration </p>
                    <p style="text-align:center;">
                        <strong>Powered by:</strong>
                        <br>
                        <img width="150px" src="'.site_url('uploads/logos/'.$this->application_settings->paper_header_logo).'" alt="'.$this->application_settings->application_name.'">
                    </p>
                </div>
            </div>';
            header('Content-Disposition: attachment; filename=testfile'); 
            $this->pdf_library->generate_landscape_report($html_data);
            die;
        }else{
           echo $html; 
       }
    }
    public function get_aging_loans_summary_pdf($generate_pdf=FALSE){ 
        $query_string = $_GET;
        $query_string['generate_excel'] = 1;
        $generated_query_string = http_build_query($query_string);
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-10 year');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $member_ids = $this->input->get_post('member_ids')?:0;
        $loan_type_ids = $this->input->get_post('loan_type_ids')?:0;
        $total_loan_out = $this->loans_m->get_total_loaned_amount();
        $total_loan_paid = $this->loan_repayments_m->get_total_loan_paid();
        $external_lending_total_loan_out = $this->debtors_m->get_total_loaned_amount();
        $external_lending_total_loan_paid = $this->debtors_m->get_total_loan_paid();
        $loan_type_options = $this->loan_types_m->get_options();
        $base_where = array(
            'member_id' => $member_ids,
            'loan_type_id' => $loan_type_ids,
            'from' => $from,
            'to' => $to,
        );
        $amount_paid = array();
        $amount_payable_to_date = array();
        $projected_profit = array();
        $loans = $this->loans_m->_get_group_aging_loans_for_all_classes($base_where,$this->group->id);
    
        $loan_ids = '0';
        foreach($loans as $loan){
            $loan_ids.=','.$loan->id;
        }
        $invoice_summations = $this->loans_m->get_loans_summation_for_ivoices_by_loan_ids($loan_ids);
        $amount_paid_to_group = $this->loan_repayments_m->get_loan_total_payments_by_loan_ids($loan_ids);
        $amounts_payable_to_date = $this->loans_m->loan_payable_and_principle_todate_by_loan_ids($loan_ids);
        $projected_profits=$this->loans_m->get_projected_interest_by_loan_ids($loan_ids,$amount_paid_to_group);
 
        $external_lending_post = array();
        $external_lending_amount_paid = array();
        $external_lending_amount_payable_to_date = array();
        $external_lending_projected_profit = array();
        $external_lending_total_loan_out = $this->debtors_m->get_total_loaned_amount();
        $external_lending_total_loan_paid = $this->debtors_m->get_total_loan_paid();
        $external_lending_loans = $this->debtors_m->get_many_by();
        foreach ($external_lending_loans as $loan){
            $external_lending_post[] = $this->debtors_m->get_summation_for_invoice($loan->id);
            $external_lending_amount_paid[$loan->id] = $this->debtors_m->get_loan_total_payments($loan->id);
            $external_lending_amount_payable_to_date[$loan->id] = $this->debtors_m->loan_payable_and_principle_todate($loan->id);
            $external_lending_projected_profit[$loan->id] = $this->debtors_m->get_projected_interest($loan->id,$external_lending_amount_paid[$loan->id]);
        }
        $html= '            
        <div class="table-responsive table-scrollable" style="font-family: arial;">
        <div class="invoice-content-2 bordered document-border">

        <br><br>
        <div class="row invoice-head">
        <div class="col-md-7 col-lg-7">
             
            <div class="invoice-logo"  style="text-align:center;">
                <img src="';
                    $html.=is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                    $html.= '" alt="" class="group-logo image-responsive" style="max-height: 120px;" />
            </div>
        
        </div>
        <br>
        <div class="col-md-5 col-lg-5 text-right">
        <div class="company-address" style="text-align:center;">
                <span class="bold uppercase" style="font-weight:bold; text-transform:uppercase; font-size:22px; ">'.$this->group->name.'</span><br/>';
                $html.= nl2br($this->group->address);
                $html.= '<br/>
                <span class="bold" style="font-weight:bold;">'.translate('Telephone').': </span> '.$this->group->phone.'
                <br/>
                <span class="bold"  style="font-weight:bold;">'.translate('Email Address').': </span> '.$this->group->email.'
                <br/>
            </div>
        </div>

        <div class="company-address" style="text-align:center;">
                <span class="bold uppercase" style="font-weight:bold; font-size:18px; ">Loan Arrears Report As at '.timestamp_to_date($to).'</span><br/>';
                 
                $html.= '<br/>
                <span class="bold" style="font-weight:bold;"> 
                 
            </div>
        </div>
    </div>';
                    $total_loan=0;
                    $total_interest=0;
                    $total_paid=0;
                    $total_balance=0;
                    $total_projected=0;
                    $total_outstanding_profit=0;
                    $total_profits=0;
                    $total_pca_amount=0;
                    $total_value_for_class_A=0;
                    $total_value_for_class_B=0;
                    $total_value_for_class_C=0;
                    $total_value_for_class_D=0;
                    $total_value_for_class_E=0;
                    $total_balance=0;

                    if(!empty($loans)){
                        $html.=
                        '<div class="col-xs-12 table-responsive ">
                            <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement" style="width:100%;height:100%">
                                <thead>
                                    <tr>
                                        <th class="invoice-title ">#</th>
                                        <th class="invoice-title">'.translate('IPPS Number').'</th>
                                       
                                        <th class="invoice-title">'.translate('Loan Type').'</th>
                                        <th class="invoice-title">'.translate('Period').'</th>
                                        <th class="invoice-title  text-right">'.translate('Amount Loaned').'</th>
                                        <th class="invoice-title  text-right">'.translate('<=30').'</th>
                                        <th class="invoice-title">'.translate('31-60').'</th>
                                        <th class="invoice-title">'.translate('61-90').'</th>
                                        <th class="invoice-title">'.translate('91-180').'</th>
                                        <th class="invoice-title">'.translate('>180').'</th>
                                        <th class="invoice-title">'.translate('Balance').'</th>
                                        
                                     </tr>
                                </thead>
                                <tbody>';
                                    $i=0;
                                    foreach($loans as $post):
                                        $disbursement_date=$post->disbursement_date;
                                        //class A
                                          $to_for_class_A= strtotime('-30 days');
                                         //class B 
                                          $from_for_class_B= strtotime('-31 days');
                                          $to_for_class_B= strtotime('-60 days');
        
                                          //class C
                                          $from_for_class_C= strtotime('-61 days');
                                          $to_for_class_C= strtotime('-90 days');
        
                                           //class D
                                           $from_for_class_D= strtotime('-91 days');
                                           $to_for_class_D= strtotime('-180 days');
        
                                            //class E
                                        
                                        $loan_type = $this->loan_types_m->get($post->loan_type_id);
                                         
                                        // print_r($this->members_m->get_member_additional_fields_mapping_data($post->member_id));
                                        $secondary_data_value = $this->members_m->get_member_additional_fields_mapping_data_using_field_slug($post->member_id,'ipps-number');
                                        $created_by  = $this->members_m->get_group_member_by_user_id($this->group->id,$post->created_by);
                                      
 
                                        $created_by_full_name=$created_by->first_name.' '.$created_by->last_name;
                                      
                                        if(isset($post->id)):
                                            
                                            $total_interest_payable = isset($invoice_summations[$post->id])?$invoice_summations[$post->id]->total_interest_payable:0;
                                            $total_amount_payable = isset($invoice_summations[$post->id])?$invoice_summations[$post->id]->total_amount_payable:0;
                                            $total_principle_payable = isset($invoice_summations[$post->id])?$invoice_summations[$post->id]->total_principle_payable:0;
                                            $amount_paid = isset($amount_paid_to_group[$post->id])?$amount_paid_to_group[$post->id]:0;
                                            $balance=$total_amount_payable-$amount_paid;
                                            
                                            $total_amount_payable_to_date = isset($amounts_payable_to_date[$post->id])?$amounts_payable_to_date[$post->id]->todate_amount_payable:0;
                                            $principle_payable_todate  = isset($amounts_payable_to_date[$post->id])?$amounts_payable_to_date[$post->id]->todate_principle_payable:0;

                                                
                                           $from_for_class_E= strtotime('-180 days');
                                           $value_for_class_A =  ($disbursement_date<=$to_for_class_A)? number_to_currency($total_amount_payable):0;
                                           $value_for_class_B =  (($disbursement_date>=$from_for_class_B) && ($disbursement_date<=$to_for_class_B))? number_to_currency($total_amount_payable):0;
                                           $value_for_class_C  =  (($disbursement_date>=$from_for_class_C) && ($disbursement_date<=$to_for_class_C))? number_to_currency($total_amount_payable):0;
                                           $value_for_class_D   =  (($disbursement_date>=$from_for_class_D) && ($disbursement_date<=$to_for_class_D))? number_to_currency($total_amount_payable):0;
                                           $value_for_class_E  =  (($disbursement_date>=$from_for_class_E))? ($total_amount_payable):0;
                                           

                                            // $total_amount_payable_to_date=$amount_payable_to_date[$post->id]->todate_amount_payable?:0;
                                            // $principle_payable_todate = $amount_payable_to_date[$post->id]->todate_principle_payable?:0;
                                            if((round($total_amount_payable_to_date-$amount_paid)) <= 0){
                                                $intere = $total_amount_payable_to_date - $principle_payable_todate;
                                                $overpayments = $amount_paid - $total_amount_payable_to_date;
                                                if($overpayments<0){
                                                    $overpayments = '';
                                                }
                                                $due_inter = '';
                                                $pen = ($total_amount_payable) - ($total_interest_payable+$total_principle_payable);
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
                                                $penalty = ($total_amount_payable) - ($total_interest_payable+$total_principle_payable);
                                            }

                                            $html.='
                                            <tr>
                                                <td> <small>'.++$i.'</small></td>
                                                <td style="text-align:center;" nowrap> <small>'.(isset($secondary_data_value)?$secondary_data_value:'---').'</small></td>
                                                <td nowrap style="text-align:center;"> <small>'.(isset($loan_type_options[$post->loan_type_id])?$loan_type_options[$post->loan_type_id]:'---').'</small></td>
                                                <td nowrap style="text-align:center;"> <small>'.$post->repayment_period.'</small></td>
                                                <td class="text-center" style="text-align:center;"> <small>';
                                                $loan = $post->loan_amount;
                                                $html.=number_to_currency($loan).
                                            '</small></td>
                                            
                                            <td nowrap style="text-align:center;"> <small>'.round($value_for_class_A).'</small></td>
                                            <td nowrap style="text-align:center;"> <small>'.round($value_for_class_B).'</small></td>
                                            <td nowrap style="text-align:center;"> <small>'.round($value_for_class_C).'</small></td>
                                            <td nowrap style="text-align:center;"> <small>'.round($value_for_class_D).'</small></td>
                                            <td nowrap style="text-align:center;"> <small>'.round($value_for_class_E).'</small></td>
                                            <td nowrap style="text-align:center;"> <small>'.round($balance).'</small></td>
                                             
 
                                               
                                                 
                                            </tr>';
                                                $total_loan+=$loan; 
                                                $total_value_for_class_A+=$value_for_class_A;
                                                $total_value_for_class_B+=$value_for_class_B;
                                                $total_value_for_class_C+=$value_for_class_C;
                                                $total_value_for_class_D+=$value_for_class_D;
                                                $total_value_for_class_E+=$value_for_class_E;
                                                $total_balance+=$balance;
                                              
                                        endif;
                                    endforeach;
                                $html.='
                                </tbody>
                                <tfoot>
                                    <tr  style="font-weight:bold">
                                       <td colspan="4">Totals</td>
                                        <td class="text-right" style="text-align:center;">'.number_to_currency($total_loan).'</td>
                                        <td class="text-right" style="text-align:center;">'.number_to_currency(round($total_value_for_class_A)).'</td>
                                        <td class="text-right" style="text-align:center;">'.number_to_currency(round($total_value_for_class_B)).'</td>
                                        <td class="text-right" style="text-align:center;">'.number_to_currency(round($total_value_for_class_C)).'</td>
                                        <td class="text-right" style="text-align:center;">'.number_to_currency(round($total_value_for_class_D)).'</td>
                                        <td class="text-right" style="text-align:center;">'.number_to_currency(round($total_value_for_class_E)).'</td>
                                        <td class="text-right" style="text-align:center;">'.number_to_currency(round($total_balance)).'</td>
                                         
                                        <td class=""></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div> ';
                    }else{
                        $html.='
                        <div class="col-xs-12 margin-bottom-10 ">
                            <div class="m-alert m-alert--outline alert alert-info fade show" role="alert"">
                                <strong>'.translate('Information').'!</strong> '.translate('No Members loan records to display').'.
                            </div>
                        </div>';
                    }
                $html.='
                </div>

                <br/>';
                
                $external_lending_total_loan=0;
                $external_lending_total_interest=0;
                $external_lending_total_paid=0;
                $external_lending_total_balance=0;
                $external_lending_total_projected=0;
                $external_lending_total_outstanding_profit=0;
                $external_lending_total_profits=0;
                if($external_lending_post):
                    $html.='
                    <div class="clearfix"></div>
                    <br/>
                    <hr/>
                    <div class="row invoice-body">';
                        if(!empty($external_lending_post)){
                            $html.='<div class="col-xs-12 table-responsive ">
                                <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement">
                                    <thead>
                                        <tr>
                                            <th class="invoice-title ">#</th>
                                            <th class="invoice-title" width="17%">'.translate('Debtor').'</th>
                                            <th class="invoice-title">'.translate('Loan Duration').'</th>
                                            <th class="invoice-title  text-right">'.translate('Amount Loaned').'</th>
                                            <th class="invoice-title  text-right">'.translate('Interest').'</th>
                                            <th class="invoice-title  text-right">'.translate('Amount Paid').'</th>
                                            <th class="invoice-title  text-right">'.translate('Arrears').'</th>
                                            <th class="invoice-title  text-right">'.translate('Profits').'</th>
                                            <th class="invoice-title  text-right">'.translate('Outstanding Profits').'</th>
                                            <th class="invoice-title  text-right">'.translate('Projected Profits').'</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                        $i=0;
                                        foreach($external_lending_post as $post):
                                            if(isset($post->id)):
                                                $total_amount_payable_to_date=$external_lending_amount_payable_to_date[$post->id]->todate_amount_payable?:0;
                                                $principle_payable_todate = $external_lending_amount_payable_to_date[$post->id]->todate_principle_payable?:0;
                                                if((round($total_amount_payable_to_date-$external_lending_amount_paid[$post->id])) <= 0){
                                                    $intere = $total_amount_payable_to_date - $principle_payable_todate;
                                                    $overpayments = $external_lending_amount_paid[$post->id] - $total_amount_payable_to_date;
                                                    if($overpayments<0){
                                                        $overpayments = '';
                                                    }
                                                    $due_inter = '';
                                                    $pen = ($post->total_amount_payable) - ($post->total_interest_payable+$post->total_principle_payable);
                                                    if($pen>0){
                                                        $penalty = $pen;
                                                    }else{
                                                        $penalty = 0;
                                                    }
                                                }else{
                                                    $intere = '';
                                                    $overpayments = '';
                                                    $penalty = ($post->total_amount_payable) - ($post->total_interest_payable+$post->total_principle_payable);
                                                }
                                                $html.='
                                                <tr>
                                                    <td>'.
                                                        ++$i.'
                                                    </td>
                                                    <td>
                                                        '.$this->group_debtor_options[$post->debtor_id].'
                                                    </td>
                                                    <td>
                                                        '.timestamp_to_date($post->disbursement_date).' - '.timestamp_to_date(strtotime("+".$post->repayment_period." months", $post->disbursement_date)).'
                                                    </td>
                                                    <td class="text-right">';
                                                        $loan = $post->loan_amount;
                                                        $html.=number_to_currency($loan).'
                                                    </td>
                                                    <td class="text-right">
                                                        '.number_to_currency($interest = $post->total_interest_payable).'
                                                    </td>
                                                    <td class="text-right">
                                                        '.number_to_currency($paid = $external_lending_amount_paid[$post->id]).'
                                                    </td>
                                                    <td class="text-right ">
                                                        <span class="tooltips" data-original-title="Interest Breakdown" data-content="Overpayment : '.number_to_currency($overpayments).' , Penalties : '.number_to_currency($penalty).'">';
                                                        $balance = $post->total_amount_payable - $paid;
                                                        $html.=number_to_currency($balance).'</span>
                                                    </td>
                                                    <td class="text-right">';
                                                        $profit = $external_lending_projected_profit[$post->id];
                                                        $html.=number_to_currency($profit).'
                                                    </td>
                                                    <td class="text-right">';
                                                        $outstanding_profit = round(($post->total_interest_payable+$penalty)-$profit);
                                                        $html.=number_to_currency($outstanding_profit).'
                                                    </td>
                                                    <td class="text-right">';
                                                        $projected_profits = $post->total_interest_payable+$penalty;
                                                        $html.=number_to_currency($projected_profits).'
                                                    </td>
                                                </tr>';
                                                $external_lending_total_loan+=$loan; 
                                                $external_lending_total_interest+=$interest;
                                                $external_lending_total_paid+=$paid;
                                                $external_lending_total_balance+=$balance; 
                                                $external_lending_total_profits+=$profit; 
                                                $external_lending_total_projected+=$projected_profits; 
                                                $external_lending_total_outstanding_profit+=$outstanding_profit;
                                            endif;
                                        endforeach;
                                    $html.='
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4">'.translate('Totals').'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_loan).'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_interest).'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_paid).'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_balance).'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_profits).'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_outstanding_profit).'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_projected).'</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>'; 
                        }else{
                            $hntl.='
                            <div class="col-xs-12 margin-bottom-10 ">
                                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert"">
                                    <strong>'.translate('Information').'!</strong> '.translate('No Debtor loan records to display').'.
                                </div>
                            </div>';
                        } 
                    $html.='
                    <div class="row">
                        <div class="col-xs-12">
                            <a class="btn btn-sm blue hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class="fa fa-print"></i> Print</a>
                            
                        </div>
                    </div>
                    </div>';
                endif;

                $html.='
                <div class="row">
                    <div class="col-md-6 summary-details">
                        <h4>Loan Details</h4>
                        <span class="bold">'.translate('Total Loaned Amount').' : </span>'.$this->group_currency.' '.number_to_currency($total_loan+$external_lending_total_loan).'<br/>
                        
                </div>
                <hr/>';
            $html.='
            </div>';

            if($generate_pdf){
                // check if possible to add custom styles here.
                $html_data = '
                <html>
                <head>
                 <link href="http://app.eazzykikundi.local/templates/admin_themes/groups/css/pdf.css" type="text/css" rel="stylesheet" />
                </head>
                <div id="statement_paper"  class="pt-3">
                    <div id="transaction_statement">'.$html.'
                    </div>
                    <div id="statement_footer" style="display: none;">
                        <p style="text-align:center;">© '.date('Y').'. This statement was issued with no alteration </p>
                        <p style="text-align:center;">
                            <strong>Powered by:</strong>
                            <br>
                            <img width="150px" src="'.site_url('uploads/logos/'.$this->application_settings->paper_header_logo).'" alt="'.$this->application_settings->application_name.'">
                        </p>
                    </div>
                </div>
                </html>';
                header('Content-Disposition: attachment; filename=testfile'); 
                $this->pdf_library->generate_landscape_report($html_data);
                die;
            }else{
               echo $html; 
           }
        

            

            // echo $html;
    }
    public function get_aging_loans_summary($generate_pdf=FALSE){
        $query_string = $_GET;
        $query_string['generate_excel'] = 1;
        $generated_query_string = http_build_query($query_string);
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-10 year');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $member_ids = $this->input->get_post('member_ids')?:0;
        $loan_type_ids = $this->input->get_post('loan_type_ids')?:0;
        $total_loan_out = $this->loans_m->get_total_loaned_amount();
        $total_loan_paid = $this->loan_repayments_m->get_total_loan_paid();
        $external_lending_total_loan_out = $this->debtors_m->get_total_loaned_amount();
        $external_lending_total_loan_paid = $this->debtors_m->get_total_loan_paid();
        $loan_type_options = $this->loan_types_m->get_options();
        $base_where = array(
            'member_id' => $member_ids,
            'loan_type_id' => $loan_type_ids,
            'from' => $from,
            'to' => $to,
        );
        $amount_paid = array();
        $amount_payable_to_date = array();
        $projected_profit = array();
        $loans = $this->loans_m->_get_group_aging_loans_for_all_classes($base_where);
        
        $loan_ids = '0';
        foreach($loans as $loan){
            $loan_ids.=','.$loan->id;
        }
      $this->group=array(
        "avatar"=>''
      );
        $html='<div class="invoice-content-2 bordered document-border">
                <div class="row invoice-head">
                    <div class="col-md-7 col-xs-6">
                        <div class="invoice-logo">
                            <img src="';
                            $html.=is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                            $html.='" alt="" class="group-logo image-responsive" /> 
                        </div>
                    </div>
                    <div class="col-md-5 col-xs-6 text-right">
                        <div class="company-address">
                            <span class="bold uppercase">'.$this->application_settings->application_name.'</span><br/>
                            '.nl2br($this->group->address).'<br/>
                            <span class="bold">'.translate('Telephone').': </span>'.$this->application_settings->application_phone.'
                            <br/>
                            <span class="bold">'.translate('Email Address').': </span> '.$this->application_settings->application_email.'
                            <br/>
                        </div>
                    </div>
                
            </div>
                </div>
                <div class="company-address" style="text-align:center;">
                <span class="bold uppercase" style="font-weight:bold; font-size:18px; ">Loan Aging Report As at '.timestamp_to_date(time()).'</span><br/>';
                 
                $html.= '<br/>
                <span class="bold" style="font-weight:bold;"> 
                 
            </div>
                <hr/>
                
                <div class="row invoice-body">';
                    $total_loan=0;
                    $total_profits=0;
                    $total_pca_amount=0;
                    $value_for_class_A=0;
                    $value_for_class_B=0;
                    $value_for_class_C=0;
                    $value_for_class_D=0;
                    $value_for_class_E=0;
                   

                    if(!empty($loans)){
                        $html.=
                        '<div class="col-xs-12 table-responsive ">
                            <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement" style="width:100%;height:100%">
                                <thead>
                                    <tr>
                                        <th class="invoice-title ">#</th>';
                                       
                                        $html.='
                                        <th class="invoice-title">'.translate('Applicant  Name').'</th>
                                        <th class="invoice-title">'.translate('Loan Type').'</th>
                                        <th class="invoice-title">'.translate('Disbursement Date').'</th>
                                        <th class="invoice-title">'.translate('Period').'</th>
                                        <th class="invoice-title  text-right">'.translate('Amount Loaned').'</th>
                                        <th class="invoice-title  text-right">'.translate('<=30').'</th>
                                        <th class="invoice-title">'.translate('31-60').'</th>
                                        <th class="invoice-title">'.translate('61-90').'</th>
                                        <th class="invoice-title">'.translate('91-180').'</th>
                                        <th class="invoice-title">'.translate('>180').'</th>
                                        <th class="invoice-title">'.translate('Balance').'</th>
                                        
                                     </tr>
                                </thead>
                                <tbody>';
                                    $i=0;
                                    $value_for_class_A=0;
                                    $value_for_class_B=0;
                                    $value_for_class_C=0;
                                    $value_for_class_D=0;
                                    $value_for_class_E=0;
                                    $total_value_for_class_A=0;
                                    $total_value_for_class_B=0;
                                    $total_value_for_class_C=0;
                                    $total_value_for_class_D=0;
                                    $total_value_for_class_E=0;
                                    $total_balance=0;

                                    foreach($loans as $post):
                                        //class A
                                        /* Class A <=30 , Class B 31-60 , Class C 61 -90, Class D 91-180   Class E >180*/
                                           $number_of_days=(time()-$post->disbursement_date)/(24*60*60);
                                            $value_for_class_A =  ($number_of_days<=30)? (round($post->loan_amount)):0;
                                            $value_for_class_B =  (($number_of_days>30) && ($number_of_days<=60))? (round($post->loan_amount)):0;
                                            $value_for_class_C =  (($number_of_days>60) && ($number_of_days<=90))? (round($post->loan_amount)):0;
                                            $value_for_class_D =  (($number_of_days>90) && ($number_of_days<=180))? (round($post->loan_amount)):0;
                                            $value_for_class_E =  (($number_of_days>180))? (($post->loan_amount)):0;
                                            
                                            // print_r($value_for_class_A);
                                            // print_r($value_for_class_B);
                                            // print_r($value_for_class_C);
                                            // print_r($value_for_class_D);
                                            // print_r($value_for_class_E);
                                            // die();


                                        
                                        $loan_type = $this->loan_types_m->get($post->loan_type_id);
                                         
                                        $created_by  = $this->members_m->get_group_member_by_user_id($post->created_by);
                                        $members_details=$this->members_m->get_group_member($post->member_id);
                                        $created_by_full_name=$created_by->first_name.' '.$created_by->last_name;
                                        if(isset($post->id)):
                                            $html.='
                                            <tr>
                                                <td> <small>'.++$i.'</small></td>';
                                             
                                                $html.='
                                                <td style="text-align:center;" nowrap> <small>'.(isset($members_details)?$members_details->first_name.' '.$members_details->last_name:'---').'</small></td>
                                                <td nowrap style="text-align:center;"> <small>'.(isset($loan_type_options[$post->loan_type_id])?$loan_type_options[$post->loan_type_id]:'---').'</small></td>
                                                <td nowrap style="text-align:center;"> <small>'.timestamp_to_date($post->disbursement_date).'</small></td>
                                                <td nowrap style="text-align:center;"> <small>'.$post->repayment_period.'</small></td>
                                                <td class="text-center" style="text-align:center;"> <small>';
                                                $loan = $post->loan_amount;
                                                $html.=number_to_currency($loan).
                                            '</small></td>
                                            
                                            <td nowrap style="text-align:center;"> <small>'.number_format($value_for_class_A).'</small></td>
                                            <td nowrap style="text-align:center;"> <small>'.number_format($value_for_class_B).'</small></td>
                                            <td nowrap style="text-align:center;"> <small>'.number_format($value_for_class_C).'</small></td>
                                            <td nowrap style="text-align:center;"> <small>'.number_format($value_for_class_D).'</small></td>
                                            <td nowrap style="text-align:center;"> <small>'.number_format($value_for_class_E).'</small></td>
                                            <td nowrap style="text-align:center;"> <small>'.number_format($post->loan_amount).'</small></td>
                                             
 
                                               
                                                 
                                            </tr>';
                                                $total_loan+=$loan; 
                                                $total_value_for_class_A+=$value_for_class_A;
                                                $total_value_for_class_B+=$value_for_class_B;
                                                $total_value_for_class_C+=$value_for_class_C;
                                                $total_value_for_class_D+=$value_for_class_D;
                                                $total_value_for_class_E+=$value_for_class_E;
                                                $total_balance+=$post->loan_amount;
                                              
                                        endif;
                                    endforeach;
                                $html.='
                                </tbody>
                                <tfoot>
                                    <tr >
                                       <td colspan="4">Totals</td>
                                       <td></td>';
                                 
                                      
                                       $html.='
                                        <td  style="font-weight:bold" class="text-right" style="text-align:center;">'.number_to_currency($total_loan).'</td>
                                        <td  style="font-weight:bold" class="text-right" style="text-align:center;">'.number_to_currency(round($total_value_for_class_A)).'</td>
                                        <td  style="font-weight:bold" class="text-right" style="text-align:center;">'.number_to_currency(round($total_value_for_class_B)).'</td>
                                        <td  style="font-weight:bold" class="text-right" style="text-align:center;">'.number_to_currency(round($total_value_for_class_C)).'</td>
                                        <td  style="font-weight:bold" class="text-right" style="text-align:center;">'.number_to_currency(round($total_value_for_class_D)).'</td>
                                        <td  style="font-weight:bold" class="text-right" style="text-align:center;">'.number_to_currency(round($total_value_for_class_E)).'</td>
                                        <td  style="font-weight:bold" class="text-right" style="text-align:center;">'.number_to_currency(round($total_balance)).'</td>
                                         
                                        <td class=""></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div> ';
                    }else{
                        $html.='
                        <div class="col-xs-12 margin-bottom-10 ">
                            <div class="m-alert m-alert--outline alert alert-info fade show" role="alert"">
                                <strong>'.translate('Information').'!</strong> '.translate('No Members loan records to display').'.
                            </div>
                        </div>';
                    }
                $html.='
                </div>

                <br/>';
                
                $external_lending_total_loan=0;
                $external_lending_total_interest=0;
                $external_lending_total_paid=0;
                $external_lending_total_balance=0;
                $external_lending_total_projected=0;
                $external_lending_total_outstanding_profit=0;
                $external_lending_total_profits=0;
                if($external_lending_post):
                    $html.='
                    <div class="clearfix"></div>
                    <br/>
                    <hr/>
                    <div class="row invoice-body">';
                        if(!empty($external_lending_post)){
                            $html.='<div class="col-xs-12 table-responsive ">
                                <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement">
                                    <thead>
                                        <tr>
                                            <th class="invoice-title ">#</th>
                                            <th class="invoice-title" width="17%">'.translate('Debtor').'</th>
                                            <th class="invoice-title">'.translate('Loan Duration').'</th>
                                            <th class="invoice-title  text-right">'.translate('Amount Loaned').'</th>
                                            <th class="invoice-title  text-right">'.translate('Interest').'</th>
                                            <th class="invoice-title  text-right">'.translate('Amount Paid').'</th>
                                            <th class="invoice-title  text-right">'.translate('Arrears').'</th>
                                            <th class="invoice-title  text-right">'.translate('Profits').'</th>
                                            <th class="invoice-title  text-right">'.translate('Outstanding Profits').'</th>
                                            <th class="invoice-title  text-right">'.translate('Projected Profits').'</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                        $i=0;
                                        foreach($external_lending_post as $post):
                                            if(isset($post->id)):
                                                $total_amount_payable_to_date=$external_lending_amount_payable_to_date[$post->id]->todate_amount_payable?:0;
                                                $principle_payable_todate = $external_lending_amount_payable_to_date[$post->id]->todate_principle_payable?:0;
                                                if((round($total_amount_payable_to_date-$external_lending_amount_paid[$post->id])) <= 0){
                                                    $intere = $total_amount_payable_to_date - $principle_payable_todate;
                                                    $overpayments = $external_lending_amount_paid[$post->id] - $total_amount_payable_to_date;
                                                    if($overpayments<0){
                                                        $overpayments = '';
                                                    }
                                                    $due_inter = '';
                                                    $pen = ($post->total_amount_payable) - ($post->total_interest_payable+$post->total_principle_payable);
                                                    if($pen>0){
                                                        $penalty = $pen;
                                                    }else{
                                                        $penalty = 0;
                                                    }
                                                }else{
                                                    $intere = '';
                                                    $overpayments = '';
                                                    $penalty = ($post->total_amount_payable) - ($post->total_interest_payable+$post->total_principle_payable);
                                                }
                                                $html.='
                                                <tr>
                                                    <td>'.
                                                        ++$i.'
                                                    </td>
                                                    <td>
                                                        '.$this->group_debtor_options[$post->debtor_id].'
                                                    </td>
                                                    <td>
                                                        '.timestamp_to_date($post->disbursement_date).' - '.timestamp_to_date(strtotime("+".$post->repayment_period." months", $post->disbursement_date)).'
                                                    </td>
                                                    <td class="text-right">';
                                                        $loan = $post->loan_amount;
                                                        $html.=number_to_currency($loan).'
                                                    </td>
                                                    <td class="text-right">
                                                        '.number_to_currency($interest = $post->total_interest_payable).'
                                                    </td>
                                                    <td class="text-right">
                                                        '.number_to_currency($paid = $external_lending_amount_paid[$post->id]).'
                                                    </td>
                                                    <td class="text-right ">
                                                        <span class="tooltips" data-original-title="Interest Breakdown" data-content="Overpayment : '.number_to_currency($overpayments).' , Penalties : '.number_to_currency($penalty).'">';
                                                        $balance = $post->total_amount_payable - $paid;
                                                        $html.=number_to_currency($balance).'</span>
                                                    </td>
                                                    <td class="text-right">';
                                                        $profit = $external_lending_projected_profit[$post->id];
                                                        $html.=number_to_currency($profit).'
                                                    </td>
                                                    <td class="text-right">';
                                                        $outstanding_profit = round(($post->total_interest_payable+$penalty)-$profit);
                                                        $html.=number_to_currency($outstanding_profit).'
                                                    </td>
                                                    <td class="text-right">';
                                                        $projected_profits = $post->total_interest_payable+$penalty;
                                                        $html.=number_to_currency($projected_profits).'
                                                    </td>
                                                </tr>';
                                                $external_lending_total_loan+=$loan; 
                                                $external_lending_total_interest+=$interest;
                                                $external_lending_total_paid+=$paid;
                                                $external_lending_total_balance+=$balance; 
                                                $external_lending_total_profits+=$profit; 
                                                $external_lending_total_projected+=$projected_profits; 
                                                $external_lending_total_outstanding_profit+=$outstanding_profit;
                                            endif;
                                        endforeach;
                                    $html.='
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4">'.translate('Totals').'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_loan).'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_interest).'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_paid).'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_balance).'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_profits).'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_outstanding_profit).'</td>
                                            <td class="text-right">'.number_to_currency($external_lending_total_projected).'</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>'; 
                        }else{
                            $hntl.='
                            <div class="col-xs-12 margin-bottom-10 ">
                                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert"">
                                    <strong>'.translate('Information').'!</strong> '.translate('No Debtor loan records to display').'.
                                </div>
                            </div>';
                        } 
                    $html.='
                    <div class="row">
                        <div class="col-xs-12">
                            <a class="btn btn-sm blue hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class="fa fa-print"></i> Print</a>
                            
                        </div>
                    </div>
                    </div>';
                endif;

                $html.='
                <div class="row">
                    <div class="col-md-6 summary-details">
                        <h4>Loan Details</h4>
                        <span class="bold">'.translate('Total Loaned Amount').' : </span>'.$this->group_currency.' '.number_to_currency($total_loan+$external_lending_total_loan).'<br/>
                        
                </div>
                <hr/>';
            $html.='
            </div>';

            if($generate_pdf){
                // check if possible to add custom styles here.
                $html_data = '
                <html>
                <head>
                 <link href="http://api.riskTick.local/templates/admin_themes/groups/css/pdf.css" type="text/css" rel="stylesheet" />
                </head>
                <div id="statement_paper"  class="pt-3">
                    <div id="transaction_statement">'.$html.'
                    </div>
                    <div id="statement_footer" style="display: none;">
                        <p style="text-align:center;">© '.date('Y').'. This statement was issued with no alteration </p>
                        <p style="text-align:center;">
                            <strong>Powered by:</strong>
                            <br>
                            <img width="150px" src="'.site_url('uploads/logos/'.$this->application_settings->paper_header_logo).'" alt="'.$this->application_settings->application_name.'">
                        </p>
                    </div>
                </div>
                </html>';
                header('Content-Disposition: attachment; filename=testfile'); 
                $this->pdf_library->generate_landscape_report($html_data);
                die;
            }else{
               echo $html; 
           }
        

            

            // echo $html;
    }

    function get_cash_flow_statement(){
        $from = strtotime($this->input->get_post('from'));
        $to = strtotime($this->input->get_post('to'));

        $years_array = array();

        if($from && $to){
            $from_year = date('Y',$from);
            $to_year = date('Y',$to);
            for($i = $from_year; $i <= $to_year; $i++):
                $years_array[] = $i;
            endfor;
        }else{
            $from_year = date('Y',strtotime('-1 year'));
            $to_year = date('Y');
            $years_array = array(
                $from_year,
                $to_year,
            );
        }

        $income_category_options = $this->income_categories_m->get_group_income_category_options();

        $non_refundable_contributions = $this->contributions_m->get_group_non_equitable_non_refundable_contributions();

        $refundable_contribution_options = $this->contributions_m->get_group_refundable_contribution_options();

        $non_refundable_contribution_options = $this->contributions_m->get_group_equitable_non_refundable_contribution_options();

        $other_financial_assets_per_year_array = array();
        $total_assets_per_year_array = array();
        $total_liabilities_per_year_array = array();

        foreach($years_array as $year):
            $total_assets_per_year_array[$year] = 0;
            $other_financial_assets_per_year_array[$year] = 0;
            $total_liabilities_per_year_array[$year] = 0;
        endforeach;

        $total_principal_money_market_investment_out_per_year_array = $this->money_market_investments_m->get_group_total_principal_money_market_investment_out_per_year_array($this->group->id);

        $total_asset_purchase_payments_per_year_array = $this->withdrawals_m->get_group_total_asset_purchase_payments_per_year_array($this->group->id);

        $total_stock_purchases_per_year_array = $this->stocks_m->get_group_total_stocks_retained_per_year_array($this->group->id);

        $total_principal_loans_out_per_year_array = $this->loans_m->get_group_total_principal_loans_out_per_year_array($this->group->id);

        $total_interest_bearing_liability_per_year_array = $this->bank_loans_m->get_group_total_interest_bearing_liability_per_year_array($this->group->id);

        $account_balances_per_year_array = $this->transaction_statements_m->get_group_account_balances_per_year_array($this->group->id);

        $refundable_contributions_per_year_array = $this->reports_m->get_group_total_contributions_per_year_array($this->group->id,0,0,$refundable_contribution_options);

        $administrative_expense_categories = $this->expense_categories_m->get_group_administrative_expense_category_options();

        $other_expense_categories = $this->expense_categories_m->get_group_other_expense_category_options();

        $non_refundable_contributions_per_year_array = $this->reports_m->get_group_total_contributions_paid_cumulatively_per_contribution_per_year_array($this->group->id);

        $total_loan_interest_paid_per_year_array = $this->reports_m->get_group_total_loan_interest_paid_per_year_array($this->group->id);

        $total_money_market_interest_per_year_array = $this->money_market_investments_m->get_group_total_money_market_interest_per_year_array($this->group->id);


        $total_stocks_sale_income_per_year_array = $this->stocks_m->get_group_total_stocks_sale_income_per_year_array($this->group->id);

        $total_bank_loans_interest_paid_per_year_array = $this->withdrawals_m->get_group_total_bank_loans_interest_paid_per_year_array();

        $total_stocks_sale_losses_per_year_array = $this->stocks_m->get_group_total_stocks_sale_losses_per_year_array($this->group->id);

        $total_contributions_paid_per_contribution_per_year_array = $this->reports_m->get_group_total_contributions_paid_per_contribution_per_year_array();

        $expense_category_totals_per_year_array = $this->withdrawals_m->get_group_expense_category_totals_per_year_array();

        $total_loan_processing_income_per_year_array = $this->deposits_m->get_group_total_loan_processing_income_per_year_array();

        $total_income_per_year_array = $this->deposits_m->get_group_total_income_per_year_array();

        $total_expenses_per_year_array = $this->withdrawals_m->get_group_expense_totals_per_year_array();

        $total_dividends_per_year_array = $this->withdrawals_m->get_group_total_dividends_per_year_array();

        $total_miscellaneous_income_per_year_array = $this->deposits_m->get_group_total_miscellaneous_income_per_year_array();

        $total_fines_per_year_array = $this->reports_m->get_group_total_fines_per_year_array($this->group->id);

        $total_loan_overpayments_per_year_array = $this->reports_m->get_group_total_loan_overpayments_per_year_array($this->group->id);
        

        $alternative_years_array = array();

        $current_year = date('Y');

        for($i = 1970;$i <= $current_year; $i++):
            $alternative_years_array[] = $i;
        endfor;

        foreach($non_refundable_contributions as $contribution):
            foreach($alternative_years_array as $year):
                $total_non_refundable_contributions_paid_per_year_array[$year] += isset($total_contributions_paid_per_contribution_per_year_array[$contribution->id][$year])?$total_contributions_paid_per_contribution_per_year_array[$contribution->id][$year]:0;
            endforeach;
        endforeach;

        $money_in_per_year_array = array();
        $money_out_per_year_array = array();

        foreach($alternative_years_array as $key):

            $money_in_per_year_array[$key] = 0;
            $money_out_per_year_array[$key] = 0;

            if(isset($total_money_market_interest_per_year_array[($key - 1)])){
                if(isset($total_money_market_interest_per_year_array[$key])){

                }else{
                    $total_money_market_interest_per_year_array[$key] = 0;
                }
                $total_money_market_interest_per_year_array[$key] += $total_money_market_interest_per_year_array[($key - 1)];
            }

            if(isset($total_loan_interest_paid_per_year_array[($key - 1)])){
                if(isset($total_loan_interest_paid_per_year_array[$key])){

                }else{
                    $total_loan_interest_paid_per_year_array[$key] = 0;
                }
                $total_loan_interest_paid_per_year_array[$key] += $total_loan_interest_paid_per_year_array[($key - 1)];
            }

            if(isset($total_stocks_sale_income_per_year_array[($key - 1)])){
                if(isset($total_stocks_sale_income_per_year_array[$key])){

                }else{
                    $total_stocks_sale_income_per_year_array[$key] = 0;
                }
                $total_stocks_sale_income_per_year_array[$key] += $total_stocks_sale_income_per_year_array[($key - 1)];
            }

            if(isset($total_loan_processing_income_per_year_array[($key - 1)])){
                if(isset($total_loan_processing_income_per_year_array[$key])){

                }else{
                    $total_loan_processing_income_per_year_array[$key] = 0;
                }
                $total_loan_processing_income_per_year_array[$key] += $total_loan_processing_income_per_year_array[($key - 1)];
            }

            if(isset($total_fines_per_year_array[($key - 1)])){
                if(isset($total_fines_per_year_array[$key])){

                }else{
                    $total_fines_per_year_array[$key] = 0;
                }
                $total_fines_per_year_array[$key] += $total_fines_per_year_array[($key - 1)];
            }

            if(isset($total_miscellaneous_income_per_year_array[($key - 1)])){
                if(isset($total_miscellaneous_income_per_year_array[$key])){

                }else{
                    $total_miscellaneous_income_per_year_array[$key] = 0;
                }
                $total_miscellaneous_income_per_year_array[$key] += $total_miscellaneous_income_per_year_array[($key - 1)];
            }

            if(isset($total_income_per_year_array[($key - 1)])){
                if(isset($total_income_per_year_array[$key])){

                }else{
                    $total_income_per_year_array[$key] = 0;
                }
                $total_income_per_year_array[$key] += $total_income_per_year_array[($key - 1)];
            }

            if(isset($total_stocks_sale_losses_per_year_array[($key - 1)])){
                if(isset($total_stocks_sale_losses_per_year_array[$key])){

                }else{
                    $total_stocks_sale_losses_per_year_array[$key] = 0;
                }
                $total_stocks_sale_losses_per_year_array[$key] += $total_stocks_sale_losses_per_year_array[($key - 1)];
            }

            if(isset($total_bank_loans_interest_paid_per_year_array[($key - 1)])){
                if(isset($total_bank_loans_interest_paid_per_year_array[$key])){

                }else{
                    $total_bank_loans_interest_paid_per_year_array[$key] = 0;
                }
                $total_bank_loans_interest_paid_per_year_array[$key] += $total_bank_loans_interest_paid_per_year_array[($key - 1)];
            }

            if(isset($total_expenses_per_year_array[($key - 1)])){
                if(isset($total_expenses_per_year_array[$key])){

                }else{
                    $total_expenses_per_year_array[$key] = 0;
                }
                $total_expenses_per_year_array[$key] += $total_expenses_per_year_array[($key - 1)];
            }

            if(isset($total_dividends_per_year_array[($key - 1)])){
                if(isset($total_dividends_per_year_array[$key])){

                }else{
                    $total_dividends_per_year_array[$key] = 0;
                }
                $total_dividends_per_year_array[$key] += $total_dividends_per_year_array[($key - 1)];
            }

        endforeach;

        //Assets addition
        foreach($years_array as $year):
            if(isset($total_principal_money_market_investment_out_per_year_array[$year])){
                $other_financial_assets = $total_principal_money_market_investment_out_per_year_array[$year];
            }else{
                $other_financial_assets = 0;
            }
            $total_assets_per_year_array[$year] += $other_financial_assets;
        endforeach;

        $other_financial_assets = 0;

        foreach($years_array as $year):
            if(isset($total_principal_loans_out_per_year_array[$year])){
                $loan_to_members = $total_principal_loans_out_per_year_array[$year];
            }else{
                if(isset($loan_to_members)){

                }else{
                    $loan_to_members = 0;
                }
            }
            $total_assets_per_year_array[$year] += $loan_to_members;
        endforeach;

        foreach($years_array as $year):
            if(isset($total_interest_bearing_liability_per_year_array[$year])){
                $bank_loans = $total_interest_bearing_liability_per_year_array[$year];
            }else{
                if(isset($bank_loans)){

                }else{
                    $bank_loans = 0;
                }
            }
            $total_liabilities_per_year_array[$year] += $bank_loans;
        endforeach;
        $loan_to_members = 0;
        
        foreach($years_array as $year):
            if(isset($total_asset_purchase_payments_per_year_array[$year])){
                $fixed_asset_value = $total_asset_purchase_payments_per_year_array[$year];
            }else{
                if(isset($fixed_asset_value)){

                }else{
                    $fixed_asset_value = 0;
                }
            }
            $total_assets_per_year_array[$year] += $fixed_asset_value;
        endforeach;
        $fixed_asset_value = 0;

        foreach($years_array as $year):
            if(isset($total_stock_purchases_per_year_array[$year])){
                $stock_purchase_value = $total_stock_purchases_per_year_array[$year];
            }else{
                if(isset($stock_purchase_value)){

                }else{
                    $stock_purchase_value = 0;
                }
            }
            $total_assets_per_year_array[$year] += $stock_purchase_value;
        endforeach;

        $stock_purchase_value = 0;

        foreach($years_array as $year):
            
            if(isset($account_balances_per_year_array[$year])){
                $cash_at_bank = $account_balances_per_year_array[$year];
            }else{
                if(isset($cash_at_bank)){

                }else{
                    $cash_at_bank = 0;
                }
            }
            $total_assets_per_year_array[$year] += $cash_at_bank;
        endforeach;

        $cash_at_bank = 0;

        //liabilities
        foreach($years_array as $year):
            if(isset($refundable_contributions_per_year_array[$year])){
                $refundable_member_deposits = $refundable_contributions_per_year_array[$year];
            }else{
                if(isset($refundable_member_deposits)){

                }else{
                    $refundable_member_deposits = 0;
                }
            }
            $total_liabilities_per_year_array[$year] += $refundable_member_deposits;
        endforeach;
        $refundable_member_deposits = 0;

        foreach($years_array as $year):
            if(isset($total_loan_overpayments_per_year_array[$year])){
                $loan_overpayment = $total_loan_overpayments_per_year_array[$year];
            }else{
                if(isset($loan_overpayment)){

                }else{
                    $loan_overpayment = 0;
                }
            }
            $total_liabilities_per_year_array[$year] += $loan_overpayment;
        endforeach;
        $loan_overpayment = 0;

        //equity
        foreach($non_refundable_contribution_options as $contribution_id => $name):
            foreach($years_array as $year):
                if(isset($non_refundable_contributions_per_year_array[$contribution_id][$year])){
                    $non_refundable_member_deposits = $non_refundable_contributions_per_year_array[$contribution_id][$year];
                }else{
                    if(isset($non_refundable_member_deposits)){

                    }else{
                        $non_refundable_member_deposits = 0;
                    }
                }
                $total_owners_equity_per_year_array[$year] += $non_refundable_member_deposits;
            endforeach;
        endforeach;

        $non_refundable_member_deposits = 0;


        foreach($years_array as $year):
            //Money in
            $money_in_per_year_array[$year] += $refundable_contributions_per_year_array[$year];
            if(isset($non_refundable_contributions_per_year_array[$year])){
                $money_in_per_year_array[$year] += $non_refundable_contributions_per_year_array[$year];
            }
            if(isset($total_loan_overpayments_per_year_array[$year])){
                $money_in_per_year_array[$year] += $total_loan_overpayments_per_year_array[$year];
            }
            if(isset($total_loan_interest_paid_per_year_array[$year])){
                $money_in_per_year_array[$year] += $total_loan_interest_paid_per_year_array[$year];
            }
            if(isset($total_loan_interest_paid_per_year_array[$year])){
                $money_in_per_year_array[$year] += $total_loan_interest_paid_per_year_array[$year];
            }
            if(isset($total_money_market_interest_per_year_array[$year])){
                $money_in_per_year_array[$year] += $total_money_market_interest_per_year_array[$year];
            }
            if(isset($total_stocks_sale_income_per_year_array[$year])){
                $money_in_per_year_array[$year] += $total_stocks_sale_income_per_year_array[$year];
            }
            if(isset($total_loan_processing_income_per_year_array[$year])){
                $money_in_per_year_array[$year] += $total_loan_processing_income_per_year_array[$year];
            }
            if(isset($total_income_per_year_array[$year])){
                $money_in_per_year_array[$year] += $total_income_per_year_array[$year];
            }
            if(isset($total_fines_per_year_array[$year])){
                $money_in_per_year_array[$year] += $total_fines_per_year_array[$year];
            }
            if(isset($total_fines_per_year_array[$year])){
                $money_in_per_year_array[$year] += isset($total_miscellaneous_income_per_year_array[$year])?$total_miscellaneous_income_per_year_array[$year]:0;
            }

            //Money out
            if(isset($total_asset_purchase_payments_per_year_array[$year])){
                $money_out_per_year_array[$year] += $total_asset_purchase_payments_per_year_array[$year];
            }
            if(isset($total_principal_loans_out_per_year_array[$year])){
                $money_out_per_year_array[$year] += $total_principal_loans_out_per_year_array[$year];
            }
            if(isset($total_stocks_sale_losses_per_year_array[$year])){
                $money_out_per_year_array[$year] += $total_stocks_sale_losses_per_year_array[$year];
            }
            if(isset($total_bank_loans_interest_paid_per_year_array[$year])){
                $money_out_per_year_array[$year] += $total_bank_loans_interest_paid_per_year_array[$year];
            }
            if(isset($total_bank_loans_interest_paid_per_year_array[$year])){
                $money_out_per_year_array[$year] += $total_bank_loans_interest_paid_per_year_array[$year];
            }
            if(isset($total_dividends_per_year_array[$year])){
                $money_out_per_year_array[$year] += $total_dividends_per_year_array[$year];
            }
            if(isset($total_expenses_per_year_array[$year])){
                $money_out_per_year_array[$year] += $total_expenses_per_year_array[$year];
            }
            if(isset($total_principal_money_market_investment_out_per_year_array[$year])){
                $money_out_per_year_array[$year] += $total_principal_money_market_investment_out_per_year_array[$year];
            }
            if(isset($total_stock_purchases_per_year_array[$year])){
                $money_out_per_year_array[$year] += $total_stock_purchases_per_year_array[$year];
            }

        endforeach;

        $html = '
            <div class="report invoice-content-2 bordered document-border">
                <div class="row invoice-head">
                    <div class="col-md-7 col-xs-6">
                        <div class="invoice-logo">
                            <img src="';
                                $html.=is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                                $html.='" alt="" class="group-logo image-responsive" /> 
                        </div>
                    </div>
                    <div class="col-md-5 col-xs-6 text-right">
                        <div class="company-address">
                            <span class="bold uppercase">'.$this->group->name.'</span><br/>';
                            $html.=nl2br($this->group->address);
                            $html.='<br/>
                            <span class="bold">Telephone: </span> '.$this->group->phone.'
                            <br/>
                            <span class="bold">E-mail Address: </span> '.$this->group->email.'
                            <br/>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-center margin-top-10 margin-bottom-10">
                        '.ucfirst($this->group->name).'<br/>
                        Cash Flow Statement as at '.$from_year.' to '.$to_year.'
                    </div>
                </div>
                <hr/>
                <div class="invoice-body">
                    <div class="col-xs-12 table-responsive"> 
                        <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement">
                            <thead>
                                <tr>
                                    <th width="30%" class="invoice-title"></th>';

                                    $width = round(70/count($years_array));
                                    foreach($years_array as $year):
                                        $html .='
                                        <th class="text-right invoice-title" width="'.$width.'%">'.$year.'</th>';
                                    endforeach;
                                $html .='
                                </tr>
                            </thead>';
                            $html.= '
                                <tbody>
                                <tr>
                                    <td><strong>Money In</strong></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small></small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small><a href="'.base_url('group/reports').'">Member\'s deposits</a></small></td>';

                                    foreach($years_array as $year):
                                        if(isset($refundable_contributions_per_year_array[$year])){
                                            $refundable_member_deposits = $refundable_contributions_per_year_array[$year];
                                        }else{
                                            if(isset($refundable_member_deposits)){

                                            }else{
                                                $refundable_member_deposits = 0;
                                            }
                                        }
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($refundable_member_deposits).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>';

                                foreach($non_refundable_contribution_options as $contribution_id => $name):
                                    $html.='
                                    <tr class="listing">
                                        <td><small>&nbsp;&nbsp;&nbsp;'.$name.'</small></td>';
                                        foreach($years_array as $year):
                                            if(isset($non_refundable_contributions_per_year_array[$contribution_id][$year])){
                                                $non_refundable_member_deposits = $non_refundable_contributions_per_year_array[$contribution_id][$year];
                                            }else{
                                                if(isset($non_refundable_member_deposits)){

                                                }else{
                                                    $non_refundable_member_deposits = 0;
                                                }
                                            }
                                            $html .= ' 
                                            <td class="text-right"><small>'.number_to_currency($non_refundable_member_deposits).'</small></td>';
                                        endforeach;
                                    $html .='
                                    </tr>';
                                endforeach;

                                $html .= '
                                <tr class="listing">
                                    <td><small><a href="'.base_url('group/reports/member_loan_overpayments_summary').'">Member loan overpayments</a></small></td>';
                                    foreach($years_array as $year):
                                        if(isset($total_loan_overpayments_per_year_array[$year])){
                                            $loan_overpayment = $total_loan_overpayments_per_year_array[$year];
                                        }else{
                                            if(isset($loan_overpayment)){

                                            }else{
                                                $loan_overpayment = 0;
                                            }
                                        }
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($loan_overpayment).'</small></td>';
                                    endforeach;
                                $html .= '
                                </tr>';

                                $html .= '
                                <tr class="listing">
                                    <td><small><a href="'.base_url('group/reports/loan_interest_summary').'">Interest on loans</a>
                                    </a></small></td>';
                                    foreach($years_array as $year):
                                        if(isset($total_loan_interest_paid_per_year_array[$year])){
                                            $loan_interest_paid = $total_loan_interest_paid_per_year_array[$year];
                                        }else{
                                            if(isset($loan_interest_paid)){

                                            }else{
                                                $loan_interest_paid = 0;
                                            }
                                        }
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($loan_interest_paid).'</small></td>';
                                    endforeach;
                                $html .= '
                                </tr>';

                                $html .= '
                                <tr class="listing">
                                    <td><small><a href="'.base_url('group/reports/money_market_interest_summary').'">Interest money market investments</a></small></td>';
                                    foreach($years_array as $year):
                                        if(isset($total_money_market_interest_per_year_array[$year])){
                                            $money_market_interest = $total_money_market_interest_per_year_array[$year];
                                        }else{
                                            if(isset($money_market_interest)){

                                            }else{
                                                $money_market_interest = 0;
                                            }
                                        }
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($money_market_interest).'</small></td>';
                                    endforeach;
                                $html .= '
                                </tr>';


                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small><a href="'.base_url('group/reports/income_from_stock_summary').'">Income from stock sales</a></small></td>';
                                    foreach($years_array as $year):
                                        $html .= '
                                            <td class="text-right"><small>'.(isset($total_stocks_sale_income_per_year_array[$year])?number_to_currency($total_stocks_sale_income_per_year_array[$year]):number_to_currency(0)).'</small></td>
                                        ';
                                    endforeach;
                                    
                                $html .='
                                </tr>';

                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small><a href="'.base_url('group/reports/loan_charges_summary').'">Loan charges</a></small></td>';
                                    foreach($years_array as $year):
                                        $html .= '
                                            <td class="text-right"><small>'.(isset($total_loan_processing_income_per_year_array[$year])?number_to_currency($total_loan_processing_income_per_year_array[$year]):number_to_currency(0)).'</small></td>
                                        ';
                                    endforeach;
                                    
                                $html .='
                                </tr>';

                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small><a href="'.base_url('group/reports/income_summary').'">Income</a></small></td>';
                                    foreach($years_array as $year):
                                        $html .= '
                                            <td class="text-right"><small>'.(isset($total_income_per_year_array[$year])?number_to_currency($total_income_per_year_array[$year]):number_to_currency(0)).'</small></td>
                                        ';
                                    endforeach;
                                    
                                $html .='
                                </tr>';

                                $html.='
                                <tr class="listing">
                                    <td><small><a href="'.base_url('group/reports/income_fine_summary').'">Fines</a></small></td>';
                                    foreach($years_array as $year):
                                        $html .= '
                                            <td class="text-right"><small>'.number_to_currency( (isset($total_fines_per_year_array[$year])?($total_fines_per_year_array[$year]):(0))).'</small></td>
                                        ';

                                    endforeach;
                                $html.='
                                </tr>';

                                $html.='
                                <tr class="listing">
                                    <td><small><a href="'.base_url('group/reports/miscellaneous_summary').'">Miscellaneous</a></small></td>';
                                    foreach($years_array as $year):
                                        $html .= '
                                            <td class="text-right"><small>'.number_to_currency((isset($total_miscellaneous_income_per_year_array[$year])?($total_miscellaneous_income_per_year_array[$year]):(0))).'</small></td>
                                        ';

                                    endforeach;
                                $html.='
                                </tr>';

                                $html .='
                                <tr>
                                    <td><strong>Total Money In</strong></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($money_in_per_year_array[$year]).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>';

                                $html .='
                                <tr>
                                    <td></td>';
                                    foreach($years_array as $year):
                                        $html .= '
                                            <td class="text-right"></td>';
                                    endforeach;
                                $html .= '
                                </tr>';

                                $html.= '
                                    <tbody>
                                    <tr>
                                        <td><strong>Money Out</strong></td>';
                                        foreach($years_array as $year):
                                            $html .= ' 
                                            <td class="text-right"></td>';
                                        endforeach;
                                    $html .='
                                    </tr>';

                                    $html .= '
                                    <tr class="listing">
                                        <td><small><a href="'.base_url('group/reports/asset_purchase_summary').'">Asset purchases, property, plant and equipment</a></small></td>';
                                        foreach($years_array as $year):
                                            if(isset($total_asset_purchase_payments_per_year_array[$year])){
                                                $fixed_asset_value = $total_asset_purchase_payments_per_year_array[$year];
                                            }else{
                                                if(isset($fixed_asset_value)){

                                                }else{
                                                    $fixed_asset_value = 0;
                                                }
                                            }       
                                            $html .= ' 
                                            <td class="text-right"><small>'.number_to_currency($fixed_asset_value).'</small></td>';
                                        endforeach;
                                    $html .= '
                                    </tr>';
                                    $html .= '
                                    <tr class="listing">
                                        <td><small><a href="'.site_url('group/reports/eazzyclub_loans_summary').'">Loans to members</a></small></td>';
                                        foreach($years_array as $year):
                                            if(isset($total_principal_loans_out_per_year_array[$year])){
                                                //balancing_difference_per_year_array is undefined, the writer of this function ought to fix this
                                                // $loan_to_members = $total_principal_loans_out_per_year_array[$year] - $balancing_difference_per_year_array[$year];
                                                $loan_to_members = $total_principal_loans_out_per_year_array[$year];
                                            }else{
                                                $loan_to_members = 0;
                                            }

                                            $html .= ' 
                                            <td class="text-right"><small>'.($loan_to_members<0?'('.number_to_currency(abs($loan_to_members)).')':number_to_currency($loan_to_members)).'</small></td>';
                                        endforeach;
                                    $html .='
                                    </tr>';

                                    $html .='       
                                    <tr class="listing">
                                        <td><small><a href="'.base_url('group/reports/depreciation_of_stocks').'">Depreciation of stocks</a></small></td>';
                                        foreach($years_array as $year):
                                            $html .='
                                            <td class="text-right"><small>'.(isset($total_stocks_sale_losses_per_year_array[$year])?number_to_currency($total_stocks_sale_losses_per_year_array[$year]):number_to_currency(0)).'</small></td>';
                                        endforeach;
                                        $html .= '
                                    </tr>';

                                    $html .='       
                                    <tr class="listing">
                                        <td><small><a href="'.base_url('group/reports/interest_on_bank_loans').'">Interest on bank loans</a></small></td>';
                                        foreach($years_array as $year):
                                            $html .='
                                            <td class="text-right"><small>'.(isset($total_bank_loans_interest_paid_per_year_array[$year])?number_to_currency($total_bank_loans_interest_paid_per_year_array[$year]):number_to_currency(0)).'</small></td>';
                                        endforeach;
                                        $html .= '
                                    </tr>';

                                    $html .='       
                                    <tr class="listing">
                                        <td><small><a href="'.base_url('group/reports/interest_on_bank_loans').'">Loan interest type</a></small></td>';
                                        foreach($years_array as $year):
                                            $html .='
                                            <td class="text-right"><small>'.(isset($total_bank_loans_interest_paid_per_year_array[$year])?number_to_currency($total_bank_loans_interest_paid_per_year_array[$year]):number_to_currency(0)).'</small></td>';
                                        endforeach;
                                        $html .= '
                                    </tr>';

                                    $html .='       
                                    <tr class="listing">
                                        <td><small><a href="'.base_url('group/reports/dividends_summary').'">Dividends</a></small></td>';
                                        foreach($years_array as $year):
                                            $html .='
                                            <td class="text-right"><small>'.(isset($total_dividends_per_year_array[$year])?number_to_currency($total_dividends_per_year_array[$year]):number_to_currency(0)).'</small></td>';
                                        endforeach;
                                        $html .= '
                                    </tr>';

                                    $html .='       
                                    <tr class="listing">
                                        <td><small><a href="'.base_url('group/reports/expenses_summary').'">Expenses</a></small></td>';
                                        foreach($years_array as $year):
                                            $html .='
                                            <td class="text-right"><small>'.(isset($total_expenses_per_year_array[$year])?number_to_currency($total_expenses_per_year_array[$year]):number_to_currency(0)).'</small></td>';
                                        endforeach;
                                        $html .= '
                                    </tr>';

                                    $html .='       
                                    <tr class="listing">
                                        <td><small><a href="'.base_url('group/reports/financial_assets_summary').'">Other financial assets</a></small></td>';
                                        foreach($years_array as $year):
                                            if(isset($total_principal_money_market_investment_out_per_year_array[$year])){
                                                $other_financial_assets = $total_principal_money_market_investment_out_per_year_array[$year];
                                            }else{
                                                $other_financial_assets = 0;
                                            }

                                            if(isset($total_stock_purchases_per_year_array[$year])){
                                                $other_financial_assets += $total_stock_purchases_per_year_array[$year];
                                            }else{
                                                if(isset($other_financial_assets)){

                                                }else{
                                                    $other_financial_assets = 0;
                                                }
                                            }
                                            //$total_assets_per_year_array[$year] += $other_financial_assets;
                                            $html .= ' 
                                            <td class="text-right"><small>'.number_to_currency($other_financial_assets).'</small></td>';
                                        endforeach;
                                        $html .= '
                                    </tr>';

                                    $html .= '
                                        <tr>
                                            <td><strong>Total Money Out</strong></td>';
                                            foreach($years_array as $year):
                                                $html .= ' 
                                                <td class="text-right"><small>'.number_to_currency($money_out_per_year_array[$year]).'</small></td>';
                                            endforeach;
                                        $html .='
                                        </tr>
                                    ';

                                    $html .='
                                    <tr>
                                        <td></td>';
                                        foreach($years_array as $year):
                                            $html .= ' 
                                            <td class="text-right"></td>';
                                        endforeach;
                                    $html .='
                                    </tr>
                                    <tr>
                                        <td><strong>Total Cash at Bank/Hand</strong></td>';
                                        foreach($years_array as $year): 
                                            if(isset($account_balances_per_year_array[$year])){
                                                $cash_at_bank = $account_balances_per_year_array[$year];
                                            }else{
                                                if(isset($cash_at_bank)){

                                                }else{
                                                    $cash_at_bank = 0;
                                                }
                                            }
                                            $html .= ' 
                                            <td class="text-right"><small>'.(($cash_at_bank >= 0)?number_to_currency($cash_at_bank):"(".number_to_currency(abs($cash_at_bank)).")").'</small></td>';
                                        endforeach;
                                    $html .='
                                    </tr>
                                ';
                            $html .= '
                            </tbody>
                            ';
                        $html .='
                        </table>
                    </div>';
                    //if(preg_match('/41\.210\.141\.116/',$_SERVER['REMOTE_ADDR'])||preg_match('/127\.0\.0\.1/',$_SERVER['REMOTE_ADDR'])){
                    if($this->input->get('debug')){
                        // $balancing_difference_per_year_array = array_filter($balancing_difference_per_year_array);
                        // print_r($balancing_difference_per_year_array);
                        $html .= '
                        <hr/>
                        <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                            <tbody>
                                <tr>
                                    <td>Difference</td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($balancing_difference_per_year_array[$year]).'</small></td>';
                                    endforeach;
                                $html .= '
                                </tr>
                            </tbody>
                        </table>
                        ';
                    }
                $html.='
            </div>
        ';
        
        echo $html;
    }

    function get_cash_flow_statement_old(){
        $contribution_options = $this->contributions_m->get_group_contribution_options();
        $member_total_contributions_paid_per_contribution_array = $this->statements_m->get_group_member_total_contribution_paid_per_contribution_per_member_array($this->group->id);
        $total_contribution_paid_per_contribution_per_member_array = $this->statements_m->get_group_member_total_contribution_paid_per_contribution_per_member_array($this->group->id);
        $member_total_contribution_transfers_to_fines_array = $this->statements_m->get_group_member_total_contribution_transfers_to_fines_array();
        $group_member_fine_totals = $this->deposits_m->get_group_member_total_fines_array();
        $group_member_fine_balance_totals = $this->statements_m->get_group_member_total_fine_balances_array();
        $total_member_fines= array_sum($group_member_fine_totals);
        $loan_repayments = $this->deposits_m->get_group_loan_repayments();
        $total_loan_repayments = array_sum($loan_repayments);
        $group_member_miscellaneous_totals = $this->deposits_m->get_group_member_total_miscellaneous_array();
        $total_group_member_miscellaneous_totals = array_sum($group_member_miscellaneous_totals);
        $group_income_totals = $this->deposits_m->get_group_income_categories_total_per_income_array();
        $total_incomes=0;
        foreach ($group_income_totals as $key => $income) {
            $total_incomes+=$income->amount;
        }
        $total_member_loan_processing_income = $this->deposits_m->get_group_total_loan_processing_income();
        $total_debtor_loan_processing_income = $this->deposits_m->get_debtor_loan_processing_income();
        $total_group_income_totals = $total_incomes;
        $group_income_totals = $group_income_totals;
        $loan_repayments = $this->deposits_m->get_group_loan_repayments();
        $total_loan_repayments = array_sum($loan_repayments);
        $debtor_loan_repayments = $this->deposits_m->get_group_debtor_loan_repayments();
        $total_debtor_loan_repayments = array_sum($debtor_loan_repayments);
        $bank_loan_amount = $this->deposits_m->get_total_bank_loan_amount();
        $incoming_account_tranfer_amount = 0;
        $total_asset_sale_amount = $this->deposits_m->get_group_total_asset_sale_amount();
        $total_stock_sale_amount = $this->deposits_m->get_group_total_stock_sale_amount();
        $total_money_market_cash_in_amount = $this->deposits_m->get_group_total_money_market_cash_in_amount();
        $income_categories = $this->income_categories_m->get_group_income_category_options();
        $expense_category_options = $this->expense_categories_m->get_group_expense_category_options();
        $group_expense_category_totals = $this->withdrawals_m->get_group_expense_category_totals_array();
        $loans = $this->withdrawals_m->get_group_loans_disbursment();
        $total_amount_loaned = array_sum($loans);

        $debtor_loans = $this->withdrawals_m->get_group_debtor_loan_disbursements();
        // print_r($debtor_loans); die;
        $bank_loan_repayment_amount = $this->withdrawals_m->get_group_total_bank_loan_repayment();
        $account_transfer_amount = 0;
        $total_asset_purchase_amount = $this->withdrawals_m->get_group_asset_purchase_total_amount();
        $total_stock_purchase_amount = $this->withdrawals_m->get_group_stock_purchase_total_amount();
        $money_market_investment_amount = $this->withdrawals_m->get_group_money_market_investment_total_amount();

        $html='
        <div class="report invoice-content-2 bordered document-border">
            <div class="row invoice-head">
                <div class="col-md-7 col-xs-6">
                    <div class="invoice-logo">';
                        $html.='<img src="';
                        $html.=is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                        $html.='" alt="" class="group-logo image-responsive"/>
                    </div>
                </div>
                <div class="col-md-5 col-xs-6 text-right">
                    <div class="company-address">
                        <span class="bold uppercase">'.$this->group->name.'</span><br/>
                        '.nl2br($this->group->address).'<br/>
                        <span class="bold">Telephone: </span> '.$this->group->phone.'
                        <br/>
                        <span class="bold">E-mail Address: </span> '.$this->group->email.'
                        <br/>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="invoice-body">';
            $html.='<div class="m-accordion m-accordion--default" id="m_accordion_1" role="tablist">';
                    if(!empty($this->group_member_options)):
                        $total_contributions_amount=0; 
                        $total_banner_amount = 0;
                        $sum_total_banner =array();
                        foreach ($contribution_options as $contribution_id => $contribution_name){
                            $sum_total_banner[] = $total_contribution_paid_per_contribution_per_member_array[$contribution_id];
                        }
                        foreach ($sum_total_banner as $key => $contribution_totals) {
                            foreach ($contribution_totals as $key => $total) {
                                $total_banner_amount +=$total;
                            }                            
                        }
                        $html.='
                            <h5>Cash In</h5>
                            <div class="m-accordion__item">
                                <div class="m-accordion__item-head collapsed"  role="tab" id="m_accordion_1_item_1_head" data-toggle="collapse" href="#m_accordion_1_item_1_body" aria-expanded="    false">
                                    <span class="m-accordion__item-icon"><i class="fa flaticon-stopwatch"></i></span>
                                    <span class="m-accordion__item-title">Total Contributions '.$this->group_currency.' '.number_to_currency($total_banner_amount).'</span>
                                    <span class="m-accordion__item-mode"></span>     
                                </div>
                                <div class="m-accordion__item-body collapse" id="m_accordion_1_item_1_body" class=" " role="tabpanel" aria-labelledby="m_accordion_1_item_1_head" data-parent="#m_accordion_1"> 
                                    <div class="m-accordion__item-content">
                                        <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement" id="m_table_2">
                                            <thead>
                                                <tr>
                                                    <th width="8px">#</th>
                                                    <th nowrap >Member Name</th>';
                                                    foreach ($contribution_options as $contribution_id => $contribution_name):
                                                        $grand_total_balance[$contribution_id] = 0;
                                                        $html.='
                                                            <th class="text-right">'.$contribution_name.'</th>
                                                        ';
                                                    endforeach;
                                                $html.= '
                                                    <th class="text-right">Totals</th>';
                                                    if(count($contribution_options)>4){
                                                        echo '<th>Member Name</th>';
                                                    }
                                                $html.='
                                                </tr>
                                            </thead>
                                            <tbody>';
                                                $count = 1;                                
                                                foreach ($this->active_group_member_options as $member_id => $member_name) {
                                                    $total_amount = 0;
                                                    $html.= '
                                                    <tr>
                                                        <td>'.$count.'</td>
                                                        <td style="width: 100.5px !important;" class="member_name"><a href="'.site_url('group/statements/deposit_statement/'.$member_id).'">'.$member_name.'</a></td>';
                                                        foreach ($contribution_options as $contribution_id => $contribution_name) {
                                                            $amount = isset($total_contribution_paid_per_contribution_per_member_array[$contribution_id][$member_id])?$total_contribution_paid_per_contribution_per_member_array[$contribution_id][$member_id]:0;
                                                            $total_amount+=$amount;
                                                            $grand_total_balance[$contribution_id]+=$amount;
                                                            $html.= '
                                                                <td class="text-right">'.number_to_currency($amount).'</td>
                                                            ';
                                                        }

                                                    $html.= '
                                                        <th class="text-right">'.number_to_currency($total_amount).'</th>';
                                                    if(count($contribution_options)>4){
                                                        $html.= '<td><a href="'.site_url('group/members/view/'.$member_id).'">'.$member_name.'</a></td>';
                                                    }
                                                    $html.='
                                                    </tr>
                                                    ';

                                                    $count++;
                                                }
                                            $html.= '
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="2" class="member_name">Grand Total</th>';
                                                    $sum_total = 0;
                                                    foreach ($contribution_options as $contribution_id => $contribution_name){
                                                        $sum_total +=$grand_total_balance[$contribution_id];
                                                        $html.= '<th class="text-right">'.number_to_currency($grand_total_balance[$contribution_id]).'</th>';
                                                    }
                                                $html.= '
                                                    <th class="text-right">'.number_to_currency($sum_total).'</th>';
                                                    if(count($contribution_options)>4){
                                                        $html.= '<th></th>';
                                                    }
                                                    $html.= '
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>';
                            $total_fines_banner_amount=0;
                            $total_fines_amount =0 ; 
                            if($total_member_fines):
                                foreach($this->group_member_options as $member_key=>$member):
                                    if($group_member_fine_totals[$member_key] || $member_total_contribution_transfers_to_fines_array[$member_key]):
                                        $amount_paid = $group_member_fine_totals[$member_key]+$member_total_contribution_transfers_to_fines_array[$member_key];
                                        $total_fines_banner_amount += $amount_paid;
                                    endif;
                                endforeach;
                                $html.='<h5>Fines</h5>
                                    <div class="m-accordion__item">
                                        <div class="m-accordion__item-head collapsed"  role="tab" id="m_accordion_1_item_2_head" data-toggle="collapse" href="#m_accordion_1_item_2_body" aria-expanded="    false">
                                            <span class="m-accordion__item-icon"><i class="fa flaticon-stopwatch"></i></span>
                                            <span class="m-accordion__item-title">Total Fines '.$this->group_currency.' '.number_to_currency($total_fines_banner_amount).'</span>
                                            <span class="m-accordion__item-mode"></span>     
                                        </div>
                                        <div class="m-accordion__item-body collapse" id="m_accordion_1_item_2_body" class=" " role="tabpanel" aria-labelledby="m_accordion_1_item_2_head" data-parent="#m_accordion_1"> 
                                            <div class="m-accordion__item-content">
                                            <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement" id="m_table_2">
                                                <thead>
                                                    <tr>
                                                        <th width="8px">#</th>
                                                        <th nowrap >Member Name</th>
                                                        <th class="text-right">
                                                            Amount ('.$this->group_currency.')
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
                                                $i=0;                        
                                                foreach($this->group_member_options as $member_key=>$member):
                                                    if($group_member_fine_totals[$member_key] || $member_total_contribution_transfers_to_fines_array[$member_key]):
                                                        $amount_paid = $group_member_fine_totals[$member_key]+$member_total_contribution_transfers_to_fines_array[$member_key];
                                                        $total_fines_amount += $amount_paid;
                                                        $html.='
                                                        <tr>
                                                            <td>'.++$i.'</td>
                                                            <td style="width: 100.5px !important;" class="member_name"><a href="'.site_url('group/statements/fine_statement/'.$member_key).'">'.$member.'</a></td>
                                                            <td class="text-right">'.number_to_currency($amount_paid).'</td>
                                                        </tr>';
                                                    endif;
                                                endforeach; 
                                                unset($i);
                                                unset($member_key);
                                                $html.='</tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="2" class="member_name">Grand Total</th>';
                                                        $html.= '
                                                            <th class="text-right">'.number_to_currency($total_fines_banner_amount).'</th>';
                                                            $html.= '
                                                        </tr>
                                                    </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                ';
                            endif;                            

                            $total_loan_repayment_amount=0; 
                            if($total_loan_repayments):
                                foreach($this->group_member_options as $member_key=>$member):
                                    if($loan_repayments[$member_key]):
                                        $loan_repayment_amount = $loan_repayments[$member_key];
                                        $total_loan_repayment_amount+=$loan_repayment_amount;
                                    endif;
                                endforeach;
                                $html.='<h5>Loan Repayments</h5>
                                    <div class="m-accordion__item">
                                        <div class="m-accordion__item-head collapsed"  role="tab" id="m_accordion_1_item_3_head" data-toggle="collapse" href="#m_accordion_1_item_3_body" aria-expanded="    false">
                                            <span class="m-accordion__item-icon"><i class="fa flaticon-stopwatch"></i></span>
                                            <span class="m-accordion__item-title">Total Loan Repayments '.$this->group_currency.' '.number_to_currency($total_loan_repayment_amount).'</span>
                                            <span class="m-accordion__item-mode"></span>     
                                        </div>
                                        <div class="m-accordion__item-body collapse" id="m_accordion_1_item_3_body" class=" " role="tabpanel" aria-labelledby="m_accordion_1_item_3_head" data-parent="#m_accordion_1"> 
                                            <div class="m-accordion__item-content">
                                            <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement" id="m_table_2">
                                                <thead>
                                                    <tr>
                                                        <th width="8px">#</th>
                                                        <th nowrap >Member Name</th>
                                                        <th class="text-right">
                                                            Amount ('.$this->group_currency.')
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
                                                $i=0;                                 
                                                foreach($this->group_member_options as $member_key=>$member):
                                                    if($loan_repayments[$member_key]):
                                                        $html.='
                                                        <tr>
                                                            <td>'.++$i.'</td>
                                                            <td>'.$member.'</td>
                                                            <td class="text-right">'.number_to_currency($loan_repayment_amount = $loan_repayments[$member_key]).'</td>
                                                        </tr>';
                                                    $total_loan_repayment_amount+=$loan_repayment_amount; 
                                                    endif;
                                                endforeach; 
                                                unset($i);
                                                unset($member_key);
                                                $html.='</tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="2" class="member_name">Grand Total</th>';
                                                    $html.= '
                                                        <th class="text-right">'.number_to_currency($total_loan_repayment_amount).'</th>';
                                                        $html.= '
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                ';

                            endif;

                            $total_debtor_loan_repayment_amount=0; 
                            if($total_debtor_loan_repayments):
                                foreach($this->group_debtor_options as $debtor_id=>$debtor_name):
                                    if($debtor_loan_repayments[$debtor_id]):
                                        $debtor_loan_repayment_amount = $debtor_loan_repayments[$debtor_id];
                                        $total_debtor_loan_repayment_amount+=$debtor_loan_repayment_amount;
                                    endif;
                                endforeach;
                                $html.='<h5>Debtor Loan Repayments</h5>
                                    <div class="m-accordion__item">
                                        <div class="m-accordion__item-head collapsed"  role="tab" id="m_accordion_1_item_4_head" data-toggle="collapse" href="#m_accordion_1_item_4_body" aria-expanded="    false">
                                            <span class="m-accordion__item-icon"><i class="fa flaticon-stopwatch"></i></span>
                                            <span class="m-accordion__item-title">Total Debtor Loan Repayments '.$this->group_currency.' '.number_to_currency($total_debtor_loan_repayment_amount).'</span>
                                            <span class="m-accordion__item-mode"></span>     
                                        </div>
                                        <div class="m-accordion__item-body collapse" id="m_accordion_1_item_4_body" class=" " role="tabpanel" aria-labelledby="m_accordion_1_item_4_head" data-parent="#m_accordion_1"> 
                                            <div class="m-accordion__item-content">
                                            <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement" id="m_table_2">
                                                <thead>
                                                    <tr>
                                                        <th width="8px">#</th>
                                                        <th nowrap >Debtor Name</th>
                                                        <th class="text-right">
                                                            Amount ('.$this->group_currency.')
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
                                                $i=0;                                 
                                                foreach($this->group_debtor_options as $debtor_id=>$debtor_name):
                                                    if($debtor_loan_repayments[$debtor_id]):
                                                        $html.='
                                                        <tr>
                                                            <td>'.++$i.'</td>
                                                            <td>'.$debtor_name.'</td>
                                                            <td class="text-right">'.number_to_currency($debtor_loan_repayment_amount = $debtor_loan_repayments[$debtor_id]).'</td>
                                                        </tr>';
                                                    $total_debtor_loan_repayment_amount+=$debtor_loan_repayment_amount; 
                                                    endif;
                                                endforeach; 
                                                unset($i);
                                                unset($debtor_id);
                                                $html.='</tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="2" class="member_name">Grand Total</th>';
                                                        $html.= '
                                                            <th class="text-right">'.number_to_currency($total_debtor_loan_repayment_amount).'</th>';
                                                            $html.= '
                                                        </tr>
                                                    </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                ';

                            endif;

                            $total_miscellaneous_amount=0; 
                            if($total_group_member_miscellaneous_totals):                                
                                foreach($this->group_member_options as $member_key=>$member):
                                    $miscellaneous_amount = 0;
                                    if($group_member_miscellaneous_totals[$member_key]):
                                        $miscellaneous_amount = $group_member_miscellaneous_totals[$member_key];
                                    endif;
                                    $total_miscellaneous_amount+=$miscellaneous_amount;
                                endforeach;
                                $html.='<h5>Miscellaneous Deposits</h5>
                                    <div class="m-accordion__item">
                                        <div class="m-accordion__item-head collapsed"  role="tab" id="m_accordion_1_item_5_head" data-toggle="collapse" href="#m_accordion_1_item_5_body" aria-expanded="    false">
                                            <span class="m-accordion__item-icon"><i class="fa flaticon-stopwatch"></i></span>
                                            <span class="m-accordion__item-title">Total Miscellaneous Deposits '.$this->group_currency.' '.number_to_currency($total_miscellaneous_amount).'</span>
                                            <span class="m-accordion__item-mode"></span>     
                                        </div>
                                        <div class="m-accordion__item-body collapse" id="m_accordion_1_item_5_body" class=" " role="tabpanel" aria-labelledby="m_accordion_1_item_5_head" data-parent="#m_accordion_1"> 
                                            <div class="m-accordion__item-content">
                                            <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement" id="m_table_2">
                                                <thead>
                                                    <tr>
                                                        <th width="8px">#</th>
                                                        <th nowrap >Member Name</th>
                                                        <th nowrap>
                                                            Description
                                                        </th>
                                                        <th class="text-right">
                                                            Amount ('.$this->group_currency.')
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
                                                $i=0;                                    
                                                foreach($this->group_member_options as $member_key=>$member):
                                                    if($group_member_miscellaneous_totals[$member_key]):
                                                        $html.='
                                                        <tr>
                                                            <td>'.++$i.'</td>
                                                            <td>'.$member.'</td>
                                                            <td></td>
                                                            <td class="text-right">'.number_to_currency($miscellaneous_amount = $group_member_miscellaneous_totals[$member_key]).'</td>
                                                        </tr>'; 
                                                    endif;
                                                endforeach; 
                                                unset($i);
                                                unset($member_key);
                                                $html.='</tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="2" class="member_name">Grand Total</th>';
                                                    $html.= '
                                                        <th class="text-right">'.number_to_currency($total_miscellaneous_amount).'</th>';
                                                        $html.= '
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                ';
                            endif;

                            $total_external_income_banner_amount = 0; 
                            foreach($group_income_totals as $extenal_income):
                                if($extenal_income->amount):
                                    $extenal_income_amount = $extenal_income->amount;
                                    $total_external_income_banner_amount+=$extenal_income_amount; 
                                endif;
                            endforeach;                                
                            $other_incomes =$bank_loan_amount+$incoming_account_tranfer_amount+$total_asset_sale_amount+$total_stock_sale_amount+$total_money_market_cash_in_amount+$total_external_income_banner_amount+$total_member_loan_processing_income+$total_debtor_loan_processing_income;
                            $html.='<h5>Other Incomes</h5>
                                <div class="m-accordion__item">
                                    <div class="m-accordion__item-head collapsed"  role="tab" id="m_accordion_1_item_6_head" data-toggle="collapse" href="#m_accordion_1_item_6_body" aria-expanded="    false">
                                        <span class="m-accordion__item-icon"><i class="fa flaticon-stopwatch"></i></span>
                                        <span class="m-accordion__item-title">Other Incomes '.$this->group_currency.' '.number_to_currency($other_incomes?$other_incomes:0).'</span>
                                        <span class="m-accordion__item-mode"></span>     
                                    </div>
                                    <div class="m-accordion__item-body collapse" id="m_accordion_1_item_6_body" class=" " role="tabpanel" aria-labelledby="m_accordion_1_item_6_head" data-parent="#m_accordion_1"> 
                                        <div class="m-accordion__item-content">
                                        <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement" id="m_table_2">
                                            <thead>
                                                <tr>
                                                    <th width="8px">#</th>
                                                    <th nowrap>
                                                        Description
                                                    </th>
                                                    <th class="text-right">
                                                        Amount ('.$this->group_currency.')
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                            $i=0; 
                                            if($bank_loan_amount):
                                                $html.='
                                                <tr>
                                                    <td>'.(++$i).'</td>
                                                    <td>Bank Loan </td>
                                                    <td class="text-right">'.number_to_currency($bank_loan_amount).'</td>
                                                </tr>';
                                            endif;
                                            if($incoming_account_tranfer_amount):
                                                $html.='
                                                <tr>
                                                    <td>'.(++$i).'</td>
                                                    <td>Incoming Account Transfer</td>
                                                    <td class="text-right">'.number_to_currency($incoming_account_tranfer_amount).'</td>
                                                </tr>';
                                            endif;
                                            if($total_asset_sale_amount):
                                                $html.='
                                                <tr>
                                                    <td>'.(++$i).'</td>
                                                    <td>Total Asset Sales</td>
                                                    <td class="text-right">'.number_to_currency($total_asset_sale_amount).'</td>
                                                </tr>';
                                            endif;
                                            if($total_stock_sale_amount):
                                                $html.='
                                                <tr>
                                                    <td>'.(++$i).'</td>
                                                    <td>Total Stock Sales</td>
                                                    <td class="text-right">'.number_to_currency($total_stock_sale_amount).'</td>
                                                </tr>';
                                            endif;
                                            if($total_money_market_cash_in_amount):
                                                $html.='
                                                <tr>
                                                    <td>'.(++$i).'</td>
                                                    <td>Total Money Market Cash In Amount</td>
                                                    <td class="text-right">'.number_to_currency($total_money_market_cash_in_amount).'</td>
                                                </tr>';
                                            endif;

                                            if($total_member_loan_processing_income):
                                                $html.='
                                                <tr>
                                                    <td>'.(++$i).'</td>
                                                    <td>Member Loans Processing Income</td>
                                                    <td class="text-right">'.number_to_currency($total_member_loan_processing_income).'</td>
                                                </tr>';
                                            endif;

                                            if($total_debtor_loan_processing_income):
                                                $html.='
                                                <tr>
                                                    <td>'.(++$i).'</td>
                                                    <td>Debtor Loans Processing Income</td>
                                                    <td class="text-right">'.number_to_currency($total_debtor_loan_processing_income).'</td>
                                                </tr>';
                                            endif;

                                            $total_external_income_amount=0; 
                                            foreach($group_income_totals as $extenal_income):
                                                if($extenal_income->amount):
                                                    if(array_key_exists($extenal_income->income_category_id, $income_categories)):
                                                        $html.='
                                                        <tr>
                                                            <td>'.(++$i).'</td>
                                                            <td>'.$income_categories[$extenal_income->income_category_id].'</td>
                                                            <td class="text-right">'.number_to_currency($extenal_income_amount = $extenal_income->amount).'</td>
                                                        </tr>';
                                                        $total_external_income_amount+=$extenal_income_amount; 
                                                    endif;
                                                endif;
                                            endforeach; 
                                            unset($i);
                                            $html.='</tbody> 
                                            <tfoot>
                                                <tr>
                                                    <th colspan="2" class="sub_total">Sub Total</th>';
                                                $html.= '
                                                    <th class="text-right">'.$this->group_currency.' '.number_to_currency($other_incomes?$other_incomes:0).'</th>';                                                    
                                                    $html.= '
                                                </tr>
                                            </tfoot>                                           
                                        </table>
                                    </div>
                                </div>
                            </div>
                            ';

                            $total_income =$total_contributions_amount+$total_fines_amount+$total_loan_repayment_amount+ $total_miscellaneous_amount+$other_incomes+$total_debtor_loan_repayment_amount;


                        $html.='<span class="bold">Total Cash In : </span><span class="amount_borrowed">'.$this->group_currency.' '. number_to_currency($total_income) .'</span><br>';

                        $html.='<br><h5>Cash Out</h5><br>';
                        $total_expense_amount=0; 
                        if($expense_category_options):
                            $expense_amount = 0;
                            foreach($expense_category_options as $expense_category_option_key=>$expense_category_option):
                                if(isset($group_expense_category_totals[$expense_category_option_key]) && $group_expense_category_totals[$expense_category_option_key]):
                                    $expense_amount=$group_expense_category_totals[$expense_category_option_key];
                                endif;
                                $total_expense_amount+=$expense_amount; 
                            endforeach;
                            $html.='<h5>Expenses</h5>
                                <div class="m-accordion__item">
                                    <div class="m-accordion__item-head collapsed"  role="tab" id="m_accordion_1_item_7_head" data-toggle="collapse" href="#m_accordion_1_item_7_body" aria-expanded="    false">
                                        <span class="m-accordion__item-icon"><i class="fa flaticon-stopwatch"></i></span>
                                        <span class="m-accordion__item-title">Total Expenses '.$this->group_currency.' '.number_to_currency($total_expense_amount).'</span>
                                        <span class="m-accordion__item-mode"></span>     
                                    </div>
                                    <div class="m-accordion__item-body collapse" id="m_accordion_1_item_7_body" class=" " role="tabpanel" aria-labelledby="m_accordion_1_item_7_head" data-parent="#m_accordion_1"> 
                                        <div class="m-accordion__item-content">
                                        <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement" id="m_table_2">
                                            <thead>
                                                <tr>
                                                    <th width="8px">#</th>
                                                    <th nowrap > Expense Name</th>
                                                    <th class="text-right">
                                                        Amount ('.$this->group_currency.')
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                            $i=0;                                 
                                            foreach($expense_category_options as $expense_category_option_key=>$expense_category_option):
                                                if(isset($group_expense_category_totals[$expense_category_option_key]) && $group_expense_category_totals[$expense_category_option_key]):
                                                    $html.='
                                                    <tr>
                                                        <td>'.++$i.'</td>
                                                        <td>'.$expense_category_option.'</td>
                                                        <td class="text-right">'.number_to_currency($expense_amount=$group_expense_category_totals[$expense_category_option_key]).'</td>
                                                    </tr>';
                                                    $total_expense_amount+=$expense_amount; 
                                                endif;
                                            endforeach; 
                                            unset($i);
                                            $html.='</tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="2" class="sub_total">Sub Total</th>';
                                                    $sum_total = 0;
                                                $html.= '
                                                    <th class="text-right">'.$this->group_currency.' '.number_to_currency($total_expense_amount).'</th>';                                                    
                                                    $html.= '
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            ';

                        endif;

                        $total_amount_loaned = 0; 
                        if($loans):
                            $total_loan_amount=0; 
                            foreach($this->group_member_options as $member_key=>$member):
                                if($loans[$member_key]):
                                    $loan_amount = $loans[$member_key];  
                                    $total_loan_amount+=$loan_amount;
                                endif;  
                            endforeach;
                            $html.='<h5>Loan Disbursements</h5>
                                <div class="m-accordion__item">
                                    <div class="m-accordion__item-head collapsed"  role="tab" id="m_accordion_1_item_8_head" data-toggle="collapse" href="#m_accordion_1_item_8_body" aria-expanded="    false">
                                        <span class="m-accordion__item-icon"><i class="fa flaticon-stopwatch"></i></span>
                                        <span class="m-accordion__item-title">Total Loan Disbursements '.$this->group_currency.' '.number_to_currency($total_loan_amount).'</span>
                                        <span class="m-accordion__item-mode"></span>     
                                    </div>
                                    <div class="m-accordion__item-body collapse" id="m_accordion_1_item_8_body" class=" " role="tabpanel" aria-labelledby="m_accordion_1_item_8_head" data-parent="#m_accordion_1"> 
                                        <div class="m-accordion__item-content">
                                        <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement" id="m_table_2">
                                            <thead>
                                                <tr>
                                                    <th width="8px">#</th>
                                                    <th nowrap > Member Name</th>
                                                    <th class="text-right">
                                                        Amount ('.$this->group_currency.')
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                            $i=0;                                  
                                            foreach($this->group_member_options as $member_key=>$member):
                                                if($loans[$member_key]):
                                                    $html.='
                                                    <tr>
                                                        <td>'.++$i.'</td>
                                                        <td>'.$member.'</td>
                                                        <td class="text-right">'.number_to_currency($loan_amount = $loans[$member_key]).'</td>
                                                    </tr>';
                                                endif;
                                            endforeach;
                                            unset($member_key);
                                            $html.='</tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="2" class="sub_total">Sub Total</th>';
                                                    $sum_total = 0;
                                                $html.= '
                                                    <th class="text-right">'.$this->group_currency.' '.number_to_currency($total_loan_amount).'</th>';                                                    
                                                    $html.= '
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            ';

                        endif;

                        $total_debtor_loan_amount = 0; 
                        if($debtor_loans): 
                            foreach($this->group_debtor_options as $debtor_id=>$debtor_name):
                                if(isset($debtor_loans[$debtor_id])):
                                    $loan_amount = $debtor_loans[$debtor_id];  
                                    $total_debtor_loan_amount+=$loan_amount;
                                endif;  
                            endforeach;
                            $html.='<h5>Debtor Loan Disbursements</h5>
                                <div class="m-accordion__item">
                                    <div class="m-accordion__item-head collapsed"  role="tab" id="m_accordion_1_item_9_head" data-toggle="collapse" href="#m_accordion_1_item_9_body" aria-expanded="    false">
                                        <span class="m-accordion__item-icon"><i class="fa flaticon-stopwatch"></i></span>
                                        <span class="m-accordion__item-title">Total Debtor Loan Disbursements '.$this->group_currency.' '.number_to_currency($total_debtor_loan_amount).'</span>
                                        <span class="m-accordion__item-mode"></span>     
                                    </div>
                                    <div class="m-accordion__item-body collapse" id="m_accordion_1_item_9_body" class=" " role="tabpanel" aria-labelledby="m_accordion_1_item_9_head" data-parent="#m_accordion_1"> 
                                        <div class="m-accordion__item-content">
                                        <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement" id="m_table_2">
                                            <thead>
                                                <tr>
                                                    <th width="8px">#</th>
                                                    <th nowrap > Member Name</th>
                                                    <th class="text-right">
                                                        Amount ('.$this->group_currency.')
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                            $i=0; 
                                            foreach($this->group_debtor_options as $debtor_id=>$debtor_name):
                                                if(isset($debtor_loans[$debtor_id])):
                                                    $html.='
                                                    <tr>
                                                        <td>'.++$i.'</td>
                                                        <td>'.$debtor_name.'</td>
                                                        <td class="text-right">'.number_to_currency($loan_amount = $debtor_loans[$debtor_id]).'</td>
                                                    </tr>';
                                                    $total_debtor_loan_amount+=$loan_amount; 
                                                endif;
                                            endforeach; 
                                            unset($i);
                                            unset($debtor_id);
                                            $html.='</tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="2" class="sub_total">Sub Total</th>';
                                                    $sum_total = 0;
                                                $html.= '
                                                    <th class="text-right">'.$this->group_currency.' '.number_to_currency($total_debtor_loan_amount).'</th>';                                                    
                                                    $html.= '
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            ';

                        endif;

                        $other_expenditure = $bank_loan_repayment_amount+$account_transfer_amount+$total_asset_purchase_amount+$total_stock_purchase_amount+$money_market_investment_amount;

                        $html.='<h5>Other Expenditures</h5>
                            <div class="m-accordion__item">
                                <div class="m-accordion__item-head collapsed"  role="tab" id="m_accordion_1_item_10_head" data-toggle="collapse" href="#m_accordion_1_item_10_body" aria-expanded="    false">
                                    <span class="m-accordion__item-icon"><i class="fa flaticon-stopwatch"></i></span>
                                    <span class="m-accordion__item-title">Other Expenditures '.$this->group_currency.' '.number_to_currency($other_expenditure?$other_expenditure:0).'</span>
                                    <span class="m-accordion__item-mode"></span>     
                                </div>
                                <div class="m-accordion__item-body collapse" id="m_accordion_1_item_10_body" class=" " role="tabpanel" aria-labelledby="m_accordion_1_item_10_head" data-parent="#m_accordion_1"> 
                                    <div class="m-accordion__item-content">
                                    <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement" id="m_table_2">
                                        <thead>
                                            <tr>
                                                <th width="8px">#</th>
                                                <th nowrap>
                                                    Description
                                                </th>
                                                <th class="text-right">
                                                    Amount ('.$this->group_currency.')
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                                        $i=0; 
                                        if($bank_loan_repayment_amount):
                                            $html.='
                                            <tr>
                                                <td>'.++$i.'</td>
                                                <td>Bank Loan Repayments</td>
                                                <td class="text-right">'.number_to_currency($bank_loan_repayment_amount).'</td>
                                            </tr>';
                                        endif;
                                        if($account_transfer_amount):
                                            $html.='
                                            <tr>
                                                <td>'.++$i.'</td>
                                                <td>Outgoing Account Transfer</td>
                                                <td class="text-right">'.number_to_currency($account_transfer_amount).'</td>
                                            </tr>';
                                        endif;
                                        if($total_asset_purchase_amount):
                                            $html.='
                                            <tr>
                                                <td>'.++$i.'</td>
                                                <td>Total Asset Purchase</td>
                                                <td class="text-right">'.number_to_currency($total_asset_purchase_amount).'</td>
                                            </tr>';
                                        endif;
                                        if($total_stock_purchase_amount):
                                            $html.='
                                            <tr>
                                                <td>'.++$i.'</td>
                                                <td>Total Stock Purchase</td>
                                                <td class="text-right">'.number_to_currency($total_stock_purchase_amount).'</td>
                                            </tr>';
                                        endif;
                                        if($money_market_investment_amount):
                                            $html.='
                                            <tr>
                                                <td>'.++$i.'</td>
                                                <td>Total Money Market Investment</td>
                                                <td class="text-right">'.number_to_currency($money_market_investment_amount).'</td>
                                            </tr>';
                                        endif;
                                        $html.='</tbody>                                            
                                    </table>
                                </div>
                            </div>
                        </div>
                        ';
                        $total_expenditure =$total_amount_loaned+$total_expense_amount+$other_expenditure+$total_debtor_loan_amount;
                        $html.='<h5>Total Expenditure : '.$this->group_currency.' '. number_to_currency($total_expenditure) .'</h5> <br>';

                        $html.='<div class="row">
                                <div class="col-md-12 margin-top-10 margin-bottom-10 total-row">';
                                    $available_cash =$total_income-$total_expenditure;
                                    $html.='
                                    <h5>Available Cash : '.$this->group_currency.' '. number_to_currency($available_cash) .'</h5> 
                                </div>
                            </div>';

                    else:
                        $html.='
                        <div class="col-xs-12 margin-bottom-10 ">
                            <div class="alert alert-info">
                                <h4 class="block">Information! No records to display</h4>
                                <p>
                                    No transaction records to display.
                                </p>
                            </div>
                        </div>';
                    endif;
            $html.='
                </div>
            </div>
        </div>';
        echo $html;
    }

    function get_account_summary_graph($period =0 ){
        $from = strtotime(" 1st ".date('M Y',strtotime("-11 months",time())));
        $format = 'M Y';
        $month_format = "M";
        $result_format = 'M';
        $add_one = TRUE;
        if($period){
            if($period == 'last_7'){
                $format = 'Ymd';
                $month_format = "Ymd";
                $from = strtotime("-7 days",time());
                $add_one = FALSE;
                $result_format = 'D';
            }else if ($period == 'last_1') {
                $format = 'Ymd';
                $month_format = "Ymd";
                $add_one = FALSE;
                $result_format = 'd M';
                $from = strtotime(" 1st ".date('M Y',strtotime("-1 month",time())));
            }else if($period == 'last_3'){
                $from = strtotime(" 1st ".date('M Y',strtotime("-2 months",time())));
            }else if($period == 'last_6'){
                $from = strtotime(" 1st ".date('M Y',strtotime("-5 months",time())));
            }else if($period == 'last_10'){
                $format = 'Y';
                $month_format = "Y";
                $result_format = 'Y';
                $from = strtotime(" 1st day of ".date('Y',strtotime("-10 years",time())));
            }
        }
        $cash_at_bank_options = $this->accounts_m->get_group_cash_at_bank_account_options();
        $cash_at_cash_options = $this->accounts_m->get_group_cash_at_hand_account_options();
        $posts = $this->transaction_statements_m->get_group_transaction_statement($from,'',$this->group->id,0,'DESC',0);
        $transaction_names = $this->transactions->transaction_names;
        $group_bank_transaction_summation = array();
        $group_cash_transaction_summation = array();
        foreach ($posts as $post) {
            $date = date($format,$post->transaction_date);
            if(array_key_exists($post->account_id, $cash_at_bank_options)){
                if(in_array($post->transaction_type, $this->transactions->deposit_transaction_types)){
                    if(array_key_exists($date,$group_bank_transaction_summation)){
                        $group_bank_transaction_summation[$date]+= $post->amount;
                    }else{
                        $group_bank_transaction_summation[$date]= $post->amount;
                    }
                }elseif(in_array($post->transaction_type,$this->transactions->withdrawal_transaction_types)){
                    if(array_key_exists($date,$group_bank_transaction_summation)){
                        $group_bank_transaction_summation[$date]-= $post->amount;
                    }else{
                        $group_bank_transaction_summation[$date]= 0-$post->amount;
                    }   
                }
            }elseif(array_key_exists($post->account_id, $cash_at_cash_options)){
                if(in_array($post->transaction_type, $this->transactions->deposit_transaction_types)){
                    if(array_key_exists($date,$group_cash_transaction_summation)){
                        $group_cash_transaction_summation[$date]+= $post->amount;
                    }else{
                        $group_cash_transaction_summation[$date]= $post->amount;
                    }
                }elseif(in_array($post->transaction_type,$this->transactions->withdrawal_transaction_types)){
                    if(array_key_exists($date,$group_cash_transaction_summation)){
                        $group_cash_transaction_summation[$date]-= $post->amount;
                    }else{
                        $group_cash_transaction_summation[$date]= 0-$post->amount;
                    }   
                }
            }
        }
        $cash_at_hand = $this->accounts_m->get_group_total_cash_at_hand();
        $cash_at_bank = $this->accounts_m->get_group_total_cash_at_bank();
        $final = array();
        $finala = array();
        $today = strtotime(($add_one?'1 ':'').date($format,time()));
        $days = (strtotime(date('d-M-Y',time())) - $from)/(24*60*60)+1;
        for ($i=0; $i < $days; $i++) { 
            $date = date($format,$today);
            if(array_key_exists(date($month_format,$today), $final)){
            }else{
                $previous_month = date($month_format,strtotime(($add_one?'+1 month ':'+1 day '),strtotime(($add_one?'1 ':'').date($format,$today))));
                $previous_months = date($format,strtotime(($add_one?'+1 month ':'+1 day '),strtotime(($add_one?'1 ':'').date($format,$today))));
                if($date == date($format,time())){
                    $final[date($month_format,$today)] = $cash_at_hand;
                }else{
                    if(array_key_exists($previous_month, $final)){
                        $previous_bank_balance = $final[$previous_month];
                    }else{
                        $previous_bank_balance = 0;
                    }
                    if(array_key_exists($previous_months, $group_cash_transaction_summation)){
                        $previous_bank_balances = $group_cash_transaction_summation[$previous_months];
                    }else{
                        $previous_bank_balances = 0;
                    }
                    $final[date($month_format,$today)] = ($previous_bank_balance)-($previous_bank_balances);
                }
            }
            if(array_key_exists(date($month_format,$today), $finala)){
            }else{
                $previous_month = date($month_format,strtotime(($add_one?'+1 month ':'+1 day '),strtotime(($add_one?'1 ':'').date($format,$today))));
                $previous_months = date($format,strtotime(($add_one?'+1 month ':'+1 day '),strtotime(($add_one?'1 ':'').date($format,$today))));
                if($date == date($format,time())){
                    $finala[date($month_format,$today)] = $cash_at_bank;
                }else{
                    if(array_key_exists($previous_month, $finala)){
                        $previous_bank_balance = $finala[$previous_month];
                    }else{
                        $previous_bank_balance = 0;
                    }
                    if(array_key_exists($previous_months, $group_bank_transaction_summation)){
                        $previous_bank_balances = $group_bank_transaction_summation[$previous_months];
                    }else{
                        $previous_bank_balances = 0;
                    }
                    $finala[date($month_format,$today)] = ($previous_bank_balance)-($previous_bank_balances);
                }
            }
            $today-=(24*60*60);
        }
        $reversed = array_reverse($final,TRUE);
        $months = array();
        $cash_values = array();
        $bank_values = array();
        $group = FALSE;
        if(count($reversed) > 16 && count($reversed) < 20){
            $group=TRUE;
            $modulus = 4;
        }elseif (count($reversed) > 20 && count($reversed) < 30 ) {
            $group=TRUE;
            $modulus = 5;
        }elseif (count($reversed) >= 30) {
            $group=TRUE;
            $modulus = 6;
        }
        $i = 0;
        foreach ($reversed as $key => $value) {
            if($group){
                if(date('Ymd') == date('Ymd',strtotime($key))){
                    $months[] = date($result_format,strtotime($key));
                    $cash_values[] = round($value,2);
                    $bank_values[] = round($finala[$key],2);
                }else{
                    if(($i%$modulus)==0){
                        $months[] = date($result_format,strtotime($key));
                        $cash_values[] = round($value,2);
                        $bank_values[] = round($finala[$key],2);
                    }
                }  
            }else{
                if(is_numeric($key)){
                    $months[] = date($result_format,strtotime($key));
                }else{
                    $months[] = date($result_format,strtotime('first day of '.$key));
                }                
                $cash_values[] = round($value,2);
                $bank_values[] = round($finala[$key],2);
            }
            $i++;
        }
        $response = array(
            'months' => $months,
            'cash_values' => $cash_values,
            'bank_values' => $bank_values,
        );

        echo json_encode($response);
    }

    function get_deposits_summary_graph(){
        if(array_key_exists($this->member->id, $this->member_role_holder_options) || $this->member->is_admin){
            $from = strtotime(" 1st ".date('M Y',strtotime("-11 months",time())));
            $posts = $this->transaction_statements_m->get_group_transaction_statement($from,'',$this->group->id,0,'DESC',0);
            $transaction_names = $this->transactions->transaction_names;
            $group_deposit_summation = array();
            $summation = array();
            $days = (strtotime(date('d-M-Y',time())) - $from)/(24*60*60)+1;
            foreach ($posts as $post) {
                $date = date('M Y',$post->transaction_date);
                if(in_array($post->transaction_type, $this->transactions->deposit_transaction_types)){
                    if(in_array($post->transaction_type, $this->transactions->incoming_account_transfer_withdrawal_transaction_types)){
                        continue;
                    }
                    if($post->member_id == $this->member->id){
                        if(array_key_exists($date, $summation)){
                            $summation[$date] += $post->amount;
                        }else{
                            $summation[$date] = $post->amount;
                        }
                    }
                    if(array_key_exists($date, $group_deposit_summation)){
                        $group_deposit_summation[$date] += $post->amount;
                    }else{
                        $group_deposit_summation[$date] = $post->amount;
                    }
                }
            }
            $final = array();
            $group_deposit_final = array();
            $group_withdrawals_final  = array();
            for ($i=0; $i <$days; $i++) { 
                $date_from = date('M Y',$from);
                if(array_key_exists($date_from, $summation)){
                    $final[date('M',$from)] = $summation[$date_from];
                }else{
                    $final[date('M',$from)] = 0;
                }
                if(array_key_exists($date_from, $group_deposit_summation)){
                    $group_deposit_final[date('M',$from)] = $group_deposit_summation[$date_from];
                }else{
                    $group_deposit_final[date('M',$from)] = 0;
                }
                $from+=(24*60*60);
            }
            $months = array();
            $deposits = array();
            foreach ($group_deposit_final as $key => $value) {
                $months[] = date('M',strtotime($key));
                $deposits[] = (int)($value);
            }
            $response = array(
                'months' => $months,
                'deposits' => $deposits,
            );
            echo json_encode($response);
        }else{
            $response = array(
                'status' => 0,
                'refer' => site_url('authentication'),
                'message' => 'You dont have rights to access this panel.',
            );
            echo json_encode($response);die;
        }
    }

    function get_transactions_summary_graph(){
        $from = strtotime(" 1st ".date('M Y',strtotime("-11 months",time())));
        $posts = $this->transaction_statements_m->get_group_transaction_statement($from,'',$this->group->id,0,'DESC',0);
        $transaction_names = $this->transactions->transaction_names;
        $group_deposit_summation = array();
        $group_withdrawal_summation = array();
        $summation = array();
        $days = (strtotime(date('d-M-Y',time())) - $from)/(24*60*60)+1;
        foreach ($posts as $post) {
            $date = date('M Y',$post->transaction_date);
            if(in_array($post->transaction_type, $this->transactions->deposit_transaction_types)){
                if(in_array($post->transaction_type, $this->transactions->incoming_account_transfer_withdrawal_transaction_types)){
                    continue;
                }
                
                if(array_key_exists($date, $group_deposit_summation)){
                    $group_deposit_summation[$date] += $post->amount;
                }else{
                    $group_deposit_summation[$date] = $post->amount;
                }
            }elseif(in_array($post->transaction_type,$this->transactions->withdrawal_transaction_types)){
                if(in_array($post->transaction_type, $this->transactions->statement_outgoing_account_transfer_withdrawal_transaction_types)){
                    continue;
                }
                if(array_key_exists($date, $group_withdrawal_summation)){
                    $group_withdrawal_summation[$date] += $post->amount;
                }else{
                    $group_withdrawal_summation[$date] = $post->amount;
                }
            }
        }

        $group_deposit_final = array();
        $group_withdrawal_final  = array();
        for ($i=0; $i <$days; $i++) { 
            $date_from = date('M Y',$from);
            if(array_key_exists($date_from, $group_deposit_summation)){
                $group_deposit_final[date('M',$from)] = $group_deposit_summation[$date_from];
            }else{
                $group_deposit_final[date('M',$from)] = 0;
            }

            if(array_key_exists($date_from, $group_withdrawal_summation)){
                $group_withdrawal_final[date('M',$from)] = $group_withdrawal_summation[$date_from];
            }else{
                $group_withdrawal_final[date('M',$from)] = 0;
            }
            $from+=(24*60*60);
        }
        $months = array();
        $deposits = array();
        $withdrawals = array();
        foreach ($group_deposit_final as $key => $value) {
            $months[] = date('M',strtotime($key));
            $deposits[] = (int)($value);
        }
        foreach ($group_withdrawal_final as $key => $value) {
            $withdrawals[] = (int)($value);
        }
        $response = array(
            'months' => $months,
            'deposits' => $deposits,
            'withdrawals' => $withdrawals,
        );
        echo json_encode($response);
    }

    function get_deposits_less_withdrawals_summary_graph(){
        if(array_key_exists($this->member->id, $this->member_role_holder_options) || $this->member->is_admin){
            $from = strtotime(" 1st ".date('M Y',strtotime("-11 months",time())));
            $posts = $this->transaction_statements_m->get_group_transaction_statement($from,'',$this->group->id,0,'DESC',0);
            $transaction_names = $this->transactions->transaction_names;
            $group_deposit_summation = array();
            $group_withdrawal_summation = array();
            $summation = array();
            $days = (strtotime(date('d-M-Y',time())) - $from)/(24*60*60)+1;
            foreach ($posts as $post) {
                $date = date('M Y',$post->transaction_date);
                if(in_array($post->transaction_type, $this->transactions->deposit_transaction_types)){
                    if(in_array($post->transaction_type, $this->transactions->incoming_account_transfer_withdrawal_transaction_types)){
                        continue;
                    }
                    if(array_key_exists($date, $group_deposit_summation)){
                        $group_deposit_summation[$date] += $post->amount;
                    }else{
                        $group_deposit_summation[$date] = $post->amount;
                    }
                }elseif(in_array($post->transaction_type,$this->transactions->withdrawal_transaction_types)){
                    if(in_array($post->transaction_type, $this->transactions->statement_outgoing_account_transfer_withdrawal_transaction_types)){
                        continue;
                    }
                    if(array_key_exists($date, $group_withdrawal_summation)){
                        $group_withdrawal_summation[$date] += $post->amount;
                    }else{
                        $group_withdrawal_summation[$date] = $post->amount;
                    }
                }
            }
            $group_deposit_final = array();
            $group_withdrawals_final  = array();
            for ($i=0; $i <$days; $i++) { 
                $date_from = date('M Y',$from);
                if(array_key_exists($date_from, $group_deposit_summation)){
                    $group_deposit_final[date('M',$from)] = $group_deposit_summation[$date_from];
                }else{
                    $group_deposit_final[date('M',$from)] = 0;
                }
                if(array_key_exists($date_from, $group_withdrawal_summation)){
                    $group_withdrawals_final[date('M',$from)] = $group_withdrawal_summation[$date_from];
                }else{
                    $group_withdrawals_final[date('M',$from)] = 0;
                }
                $from+=(24*60*60);
            }
            $months = array();
            $incomes = array();
            $monthly_collections = 0;
            $this_month_income = 0;
            $last_month_income = 0;
            $last_last_month_income = 0;
            $previous_month = 0;
            $last_month = strtotime('last month');
            $last_last_month = strtotime('-2 months',time());
            foreach ($group_deposit_final as $key => $value) {
                $months[] = date('M',strtotime($key));
                $deposit = (int)($value);
                $withdrawal = (int)($group_withdrawals_final[$key]);
                $income = ($deposit - $withdrawal);
                if(date('M Y',strtotime($key)) == date('M Y',time())){
                    $monthly_collections+=($value);
                    $this_month_income = $income;
                }
                if(date('M Y',strtotime($key)) == date('M Y',$last_month)){
                    $previous_month+=($value);
                    $last_month_income = $income;
                }
                if(date('M Y',strtotime($key)) == date('M Y',$last_last_month)){
                    $last_last_month_income = $income;
                }
                $incomes[] = ($income<=0)?0:($income/1000);
            }
            $response = array(
                'months' => $months,
                'income' => $incomes,
                'monthly_collections' => number_to_currency($monthly_collections),
                'previous_month' => number_to_currency($previous_month),
                'this_month_income' => number_to_currency($this_month_income),
                'last_month_income' => number_to_currency($last_month_income),
                'last_last_month_income' => number_to_currency($last_last_month_income),
                'income_difference' => number_to_currency($this_month_income - $last_month_income),
                'percentage' => $previous_month?round((($monthly_collections - $previous_month)/abs($previous_month)*100),2):0,
                'income_percentage' => $last_month_income?round((($this_month_income - $last_month_income)/abs($last_month_income)*100),2):0,
                'profit_margin_percentage' => $last_last_month_income?round((($last_month_income-$last_last_month_income)/abs($last_last_month_income)*100),2):0,
            );
            echo json_encode($response);
        }else{
            $response = array(
                'status' => 0,
                'refer' => site_url('authentication'),
                'message' => 'You dont have rights to access this panel.',
            );
            echo json_encode($response);die;
        }
    }

    function load_member_contributions_summary(){
        if(array_key_exists($this->member->id, $this->member_role_holder_options) || $this->member->is_admin){
            if($this->group->statements_reconciled){

            }else{
                $group_ids[] = $this->group->id;
                $member_ids = array();
                foreach($this->active_group_member_options as $member_id => $name):
                    $member_ids[] = $member_id;
                endforeach;
                // print_r($member_ids);
                // die;
                if($this->transactions->update_group_member_contribution_statement_balances($group_ids,$member_ids)){
                    $input = array(
                        'statements_reconciled' => 1,
                        'modified_on' => time(),
                        'modified_by' => $this->user->id
                    );
                    $this->groups_m->update($this->group->id,$input);
                }
            }

            $limit = count($this->active_group_member_options);
            $contributions = $this->contributions_m->get_contributions_category_not_set($this->group->id);
            if($contributions){
                $html= 
                '<div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="m-alert m-alert--outline alert alert-info fade show">
                                Hello '.$this->user->first_name.', no need to worry. Proceed to set the contribution categories for the group contributions to display member balances.';
                                $html.='
                                <table class="table table-sm m-table m-table--head-bg-metal table-bordered table_fixed_header">
                                    <thead class="thead-inverse">
                                        <tr role="row" class="heading">
                                            <th width="3px">#</th>
                                            <th >Contribution</th>
                                            <th >Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                    foreach ($contributions as $key=>$contribution) {
                                       $html.='
                                            <tr>
                                                <td>'.($key+1).'</td>
                                                <td>'.($contribution->name).'</td>
                                                <td><a href="'.site_url('group/contributions/edit/'.$contribution->id).'">Set Category</a></td>
                                            </tr>
                                       ';
                                    }

                                $html.='
                                    </tbody>
                                <table>';
                            $html.=
                            '</div>
                        </div>
                    </div>
                </div>';
                echo $html;
            }else{
                $contribution_display_options = $this->contributions_m->get_group_contribution_display_options($this->group->id,TRUE);
                $total_contribution_paid_per_contribution_per_member_array = $this->statements_m->get_group_member_total_contribution_paid_per_contribution_per_member_array($this->group->id,$contribution_display_options);
                $i = 1; 
                $group_member_total_cumulative_contribution_paid_per_member_array = array();
                foreach ($this->active_group_member_options as $member_id => $name): 
                    foreach ($contribution_display_options as $contribution_id => $contribution_name) {
                        if(isset($group_member_total_cumulative_contribution_paid_per_member_array[$member_id])){
                            $group_member_total_cumulative_contribution_paid_per_member_array[$member_id]+=$total_contribution_paid_per_contribution_per_member_array[$contribution_id][$member_id];
                        }else{
                            $group_member_total_cumulative_contribution_paid_per_member_array[$member_id] = $total_contribution_paid_per_contribution_per_member_array[$contribution_id][$member_id];
                        }
                    }

                endforeach;

                $group_member_total_cumulative_contribution_arrears_per_member_array = $this->statements_m->get_group_member_total_cumulative_contribution_arrears_per_member_array($this->group->id);

                //print_r($contribution_display_options);die;

                if($group_member_total_cumulative_contribution_paid_per_member_array&&$group_member_total_cumulative_contribution_arrears_per_member_array){ 
                    echo '               
                        <table class="table table-sm m-table m-table--head-bg-metal table-bordered table_fixed_header">
                            <thead class="thead-inverse">
                                <tr role="row" class="heading">
                                    <th width="3px">#</th>';
                                    if($this->group->member_listing_order_by == 'members.membership_number' && strlen(implode($this->membership_numbers))!=0){
                                        echo '
                                        <th>#</th>';
                                    }
                                    echo '
                                        <th>'.translate('Member').'</th>
                                        <th class="text-right">'.translate('Paid').' ('.$this->group_currency.')</th>';
                                    if($this->group->disable_arrears){

                                    }else{
                                        echo '<th class="text-right">'.translate('Arrears').' ('.$this->group_currency.')</th>';
                                    }
                                    echo '
                                </tr>
                            </thead>
                            <tbody>';
                                $i = 1; foreach ($this->active_group_member_options as $member_id => $name): 
                                if($this->group->disable_arrears){

                                }else{
                                    $arrears = ($group_member_total_cumulative_contribution_arrears_per_member_array[$member_id]);
                                    if($arrears>0){
                                        $arrears_class = ' font-red-mint text-danger';
                                    }elseif($arrears<0){
                                        $arrears_class = 'text-success';
                                    }else{
                                        $arrears_class= '';
                                    }
                                }
                                    echo '
                                    <tr>
                                        <td>'.$i++.'</td>';
                                        if($this->group->member_listing_order_by == 'members.membership_number' && strlen(implode($this->membership_numbers))!=0){
                                             echo '<td>'.$this->membership_numbers[$member_id].'</td>';
                                        }
                                        echo 
                                        '<td><a href="'.site_url("group/members/view/".$member_id).'">'.$name.'</a></td>
                                        <td class="text-right">'.number_to_currency($group_member_total_cumulative_contribution_paid_per_member_array[$member_id]).'</td>';
                                        if($this->group->disable_arrears){

                                        }else{
                                            echo '
                                            <td class="'.$arrears_class.' text-right">'.number_to_currency($group_member_total_cumulative_contribution_arrears_per_member_array[$member_id]).'</td>';
                                        }
                                        echo '
                                    </tr>';
                                endforeach;
                            echo '
                            </tbody>
                        </table>';
                }else{
                    echo '
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="margin-top-10 alert alert-info">
                                    <strong class="block">'.translate('Information! No records to display').'</strong>
                                    <p class="margin-top-10" >
                                        '.translate('No contribution information to display').'.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            }
        }else{
            echo '
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="m-alert m-alert--outline alert alert-info fade show" role="alert"">
                            <strong>Information!</strong> You dont have rights to access this panel
                        </div>
                    </div>
                </div>
            </div>';
        }
    }

    function load_member_fines_summary(){
        if(array_key_exists($this->member->id, $this->member_role_holder_options) || $this->member->is_admin){
            if($this->group->fine_statements_reconciled){

            }else{
                $group_ids[] = $this->group->id;
                $member_ids = array();
                foreach($this->active_group_member_options as $member_id => $name):
                    $member_ids[] = $member_id;
                endforeach;
                // print_r($member_ids);
                // die;
                if($this->transactions->update_group_member_fine_statement_balances($group_ids,$member_ids)){
                    $input = array(
                        'fine_statements_reconciled' => 1,
                        'modified_on' => time(),
                        'modified_by' => $this->user->id
                    );
                    $this->groups_m->update($this->group->id,$input);
                }
            }

            $group_member_total_cumulative_fine_paid_per_member_array = $this->statements_m->get_group_member_total_cumulative_fine_paid_per_member_array($this->group->id);
            $group_member_total_cumulative_fine_arrears_per_member_array = $this->statements_m->get_group_member_total_cumulative_fine_arrears_per_member_array($this->group->id);
            
            if($group_member_total_cumulative_fine_paid_per_member_array&&$group_member_total_cumulative_fine_arrears_per_member_array){
                echo '
                    <table class="table table-sm m-table m-table--head-bg-metal table-bordered table_fixed_header">
                        <thead class="thead-inverse">
                            <tr role="row" class="heading">
                                <th width="3px">#</th>';
                                if($this->show_membership_number){
                                    echo '<th>#</th>';
                                }
                                echo '
                                <th>'.translate('Member').'</th>
                                <th class="text-right">'.translate('Paid').' ('.$this->group_currency.')</th>
                                <th class="text-right">'.translate('Arrears').' ('.$this->group_currency.')</th>
                            </tr>
                        </thead>
                        <tbody>';
                            $i = 1; foreach ($this->active_group_member_options as $member_id => $name):
                            $arrears = $group_member_total_cumulative_fine_arrears_per_member_array[$member_id];
                            if($arrears>0){
                                $arrears_class = ' font-red-mint text-danger';
                            }elseif($arrears<0){
                                $arrears_class = 'text-success';
                            }else{
                                $arrears_class= '';
                            }
                            echo'
                                <tr>
                                    <td>'.$i++.'</td>';
                                    if($this->show_membership_number){
                                        echo '<td>'.$this->membership_numbers[$member_id].'</td>';
                                    }
                                    echo '
                                    <td><a href="'.site_url("group/members/view/".$member_id).'">'.$name.'</a></td>
                                    <td class="text-right">'.number_to_currency($group_member_total_cumulative_fine_paid_per_member_array[$member_id]).'</td>
                                    <td class="'.$arrears_class.' text-right">'.number_to_currency($group_member_total_cumulative_fine_arrears_per_member_array[$member_id]).'</td>
                                </tr>';
                            endforeach;
                            echo'
                        </tbody>
                    </table>';
            }else{
                echo'
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin-top-10 alert alert-info">
                                <strong class="block">'.translate('Information! No records to display').'</strong>
                                <p class="margin-top-10" >
                                    '.translate('No fine information to display').'.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>';
            }
        }else{
            echo '
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="m-alert m-alert--outline alert alert-info fade show" role="alert"">
                            <strong>Information!</strong> You dont have rights to access this panel
                        </div>
                    </div>
                </div>
            </div>';
        }
    }

    function get_trial_balance(){

        $from = strtotime($this->input->get_post('from'));
        $to = strtotime($this->input->get_post('to'));

        $years_array = array();

        if($from && $to){
            $from_year = date('Y',$from);
            $to_year = date('Y',$to);
            for($i = $from_year; $i <= $to_year; $i++):
                $years_array[] = $i;
            endfor;
        }else{
            $from_year = date('Y',strtotime('-1 year'));
            $to_year = date('Y');
            $years_array = array(
                $from_year,
                $to_year,
            );
        }

        $debit_totals_per_year_array = array();
        $credit_totals_per_year_array = array();

        foreach($years_array as $year):
            $debit_totals_per_year_array[$year] = 0;
            $credit_totals_per_year_array[$year] = 0;
        endforeach;

        $non_refundable_contributions = $this->contributions_m->get_group_non_equitable_non_refundable_contributions();

        $refundable_contribution_options = $this->contributions_m->get_group_refundable_contribution_options();

        $non_refundable_contribution_options = $this->contributions_m->get_group_equitable_non_refundable_contribution_options();

        $other_financial_assets_per_year_array = array();
        $total_assets_per_year_array = array();
        $total_liabilities_per_year_array = array();

        foreach($years_array as $year):
            $total_assets_per_year_array[$year] = 0;
            $other_financial_assets_per_year_array[$year] = 0;
            $total_liabilities_per_year_array[$year] = 0;
        endforeach;

        $total_principal_money_market_investment_out_per_year_array = $this->money_market_investments_m->get_group_total_principal_money_market_investment_out_per_year_array($this->group->id);

        $total_asset_purchase_payments_per_year_array = $this->withdrawals_m->get_group_total_asset_purchase_payments_per_year_array($this->group->id);

        $total_stock_purchases_per_year_array = $this->withdrawals_m->get_group_total_stock_purchases_per_year_array($this->group->id);

        $total_principal_loans_out_per_year_array = $this->loans_m->get_group_total_principal_loans_out_per_year_array($this->group->id);

        $total_interest_bearing_liability_per_year_array = $this->bank_loans_m->get_group_total_interest_bearing_liability_per_year_array($this->group->id);

        $account_balances_per_year_array = $this->transaction_statements_m->get_group_account_balances_per_year_array($this->group->id);

        $refundable_contributions_per_year_array = $this->reports_m->get_group_total_contributions_per_year_array($this->group->id,0,0,$refundable_contribution_options);

        $administrative_expense_categories = $this->expense_categories_m->get_group_administrative_expense_category_options();

        $other_expense_categories = $this->expense_categories_m->get_group_other_expense_category_options();

        $non_refundable_contributions_per_year_array = $this->reports_m->get_group_total_contributions_paid_cumulatively_per_contribution_per_year_array($this->group->id);

        $total_loan_interest_paid_per_year_array = $this->reports_m->get_group_total_loan_interest_paid_per_year_array($this->group->id);

        $total_money_market_interest_per_year_array = $this->money_market_investments_m->get_group_total_money_market_interest_per_year_array($this->group->id);

        $total_bank_loans_interest_paid_per_year_array = $this->withdrawals_m->get_group_total_bank_loans_interest_paid_per_year_array();

        $total_contributions_paid_per_contribution_per_year_array = $this->reports_m->get_group_total_contributions_paid_per_contribution_per_year_array();

        $expense_category_totals_per_year_array = $this->withdrawals_m->get_group_expense_category_totals_per_year_array();

        $total_loan_processing_income_per_year_array = $this->deposits_m->get_group_total_loan_processing_income_per_year_array();

        $total_income_per_year_array = $this->deposits_m->get_group_total_income_per_year_array();

        $total_miscellaneous_income_per_year_array = $this->deposits_m->get_group_total_miscellaneous_income_per_year_array();

        $total_fines_per_year_array = $this->reports_m->get_group_total_fines_per_year_array($this->group->id);

        $total_loan_overpayments_per_year_array = $this->reports_m->get_group_total_loan_overpayments_per_year_array($this->group->id);
        
        $total_year_interest_array = array();
        $net_year_interest_income_array = array();
        $net_year_income_array = array();
        $total_year_income_array = array();
        $year_surplus_before_tax_array = array();
        $year_surplus_after_tax_array = array();
        $net_surplus_for_the_year_array = array();
        $total_non_refundable_contributions_paid_per_year_array = array();
        $other_operating_income_per_year_array = array();
        $loan_processing_income_per_year_array = array();
        $group_income_per_year_array = array();
        $group_expenses_per_year_array = array();
        $transfer_to_statutory_reserve_per_year_array = array();
        $retained_earnings_per_year_array = array();
        $total_owners_equity_per_year_array = array();

        $alternative_years_array = array();

        $current_year = date('Y');

        for($i = 1970;$i <= $current_year; $i++):
            $alternative_years_array[] = $i;
        endfor;

        foreach($alternative_years_array as $year):
            $total_year_interest_array[$year] = 0;
            $net_year_interest_income_array[$year] = 0;
            $net_year_income_array[$year] = 0;
            $total_year_income_array[$year] = 0;
            $year_surplus_before_tax_array[$year] = 0;
            $year_surplus_after_tax_array[$year] = 0;
            $net_surplus_for_the_year_array[$year] = 0;
            $total_non_refundable_contributions_paid_per_year_array[$year] = 0;
            $other_operating_income_per_year_array[$year] = 0;
            $loan_processing_income_per_year_array[$year] = 0;
            $group_income_per_year_array[$year] = 0;
            $group_expenses_per_year_array[$year] = 0;
            $transfer_to_statutory_reserve_per_year_array[$year] = 0;
            $retained_earnings_per_year_array[$year] = 0;
            $general_reserves_per_year_array[$year] = 0;
            $share_transfer_fund_per_year_array[$year] = 0;
            $institutional_capital_fund_per_year_array[$year] =  0;
            $educational_fund_per_year_array[$year] =  0;
            $total_owners_equity_per_year_array[$year] =  0;
            $balancing_difference_per_year_array[$year] = 0;
        endforeach;

        foreach($non_refundable_contributions as $contribution):
            foreach($alternative_years_array as $year):
                $total_non_refundable_contributions_paid_per_year_array[$year] += isset($total_contributions_paid_per_contribution_per_year_array[$contribution->id][$year])?$total_contributions_paid_per_contribution_per_year_array[$contribution->id][$year]:0;
            endforeach;
        endforeach;

        foreach($administrative_expense_categories as $administrative_expense_category_id =>  $administrative_expense_category_name):
            foreach($alternative_years_array as $year):
                $group_expenses_per_year_array[$year] += (isset($expense_category_totals_per_year_array[$administrative_expense_category_id][$year])?$expense_category_totals_per_year_array[$administrative_expense_category_id][$year]:0);
            endforeach;
        endforeach;

        foreach($other_expense_categories as $other_expense_category_id =>  $other_expense_category_name):
            foreach($alternative_years_array as $year):
                $group_expenses_per_year_array[$year] += (isset($expense_category_totals_per_year_array[$other_expense_category_id][$year])?$expense_category_totals_per_year_array[$other_expense_category_id][$year]:0);
            endforeach;
        endforeach;

        foreach($alternative_years_array as $year):

            $total_year_interest_array[$year] += 
            (isset($total_loan_interest_paid_per_year_array[$year])?$total_loan_interest_paid_per_year_array[$year]:0);

            $total_year_interest_array[$year] += (isset($total_money_market_interest_per_year_array[$year])?$total_money_market_interest_per_year_array[$year]:0);

            $net_year_interest_income_array[$year] += $total_year_interest_array[$year];

            $net_year_interest_income_array[$year] -= (isset($total_bank_loans_interest_paid_per_year_array[$year])?($total_bank_loans_interest_paid_per_year_array[$year]):0);

            $other_operating_income_per_year_array[$year] += $total_non_refundable_contributions_paid_per_year_array[$year];

            $other_operating_income_per_year_array[$year] += (isset($total_loan_processing_income_per_year_array[$year])?$total_loan_processing_income_per_year_array[$year]:0);

            $other_operating_income_per_year_array[$year] += (isset($total_income_per_year_array[$year])?$total_income_per_year_array[$year]:0);

            $other_operating_income_per_year_array[$year] += (isset($total_miscellaneous_income_per_year_array[$year])?$total_miscellaneous_income_per_year_array[$year]:0);

            $other_operating_income_per_year_array[$year] += (isset($total_fines_per_year_array[$year])?$total_fines_per_year_array[$year]:0);

            $year_surplus_before_tax_array[$year] += $net_year_interest_income_array[$year] + $other_operating_income_per_year_array[$year] - $group_expenses_per_year_array[$year];

            $year_surplus_after_tax_array[$year] = $year_surplus_before_tax_array[$year];

            $general_reserves_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.00));

            $share_transfer_fund_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.00));

            $institutional_capital_fund_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.00));

            $educational_fund_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.00));

            $transfer_to_statutory_reserve_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.00));

            $retained_earnings_per_year_array[$year] = (($year_surplus_before_tax_array[$year] - $transfer_to_statutory_reserve_per_year_array[$year]));

        endforeach;

        foreach($retained_earnings_per_year_array as $key => $value):

            if(isset($retained_earnings_per_year_array[($key - 1)])){
                $retained_earnings_per_year_array[$key] += $retained_earnings_per_year_array[($key - 1)];
            }
            if(isset($general_reserves_per_year_array[($key - 1)])){
                $general_reserves_per_year_array[$key] += $general_reserves_per_year_array[($key - 1)];
            }
            if(isset($share_transfer_fund_per_year_array[($key - 1)])){
                $share_transfer_fund_per_year_array[$key] += $share_transfer_fund_per_year_array[($key - 1)];
            }
            if(isset($institutional_capital_fund_per_year_array[($key - 1)])){
                $institutional_capital_fund_per_year_array[$key] += $institutional_capital_fund_per_year_array[($key - 1)];
            }
            if(isset($educational_fund_per_year_array[($key - 1)])){
                $educational_fund_per_year_array[$key] += $educational_fund_per_year_array[($key - 1)];
            }
        endforeach;


        //Assets addition
        foreach($years_array as $year):
            if(isset($total_principal_money_market_investment_out_per_year_array[$year])){
                $other_financial_assets = $total_principal_money_market_investment_out_per_year_array[$year];
            }else{
                $other_financial_assets = 0;
            }
            $total_assets_per_year_array[$year] += $other_financial_assets;
        endforeach;

        $other_financial_assets = 0;

        foreach($years_array as $year):
            if(isset($total_principal_loans_out_per_year_array[$year])){
                $loan_to_members = $total_principal_loans_out_per_year_array[$year];
            }else{
                if(isset($loan_to_members)){

                }else{
                    $loan_to_members = 0;
                }
            }
            $total_assets_per_year_array[$year] += $loan_to_members;
        endforeach;

        foreach($years_array as $year):
            if(isset($total_interest_bearing_liability_per_year_array[$year])){
                $bank_loans = $total_interest_bearing_liability_per_year_array[$year];
            }else{
                if(isset($bank_loans)){

                }else{
                    $bank_loans = 0;
                }
            }
            $total_liabilities_per_year_array[$year] += $bank_loans;
        endforeach;

        // print_r($total_principal_loans_out_per_year_array);
        // echo "<hr/>";
        // //print_r($total_liabilities_per_year_array);
        // die;


        $loan_to_members = 0;
        

        foreach($years_array as $year):
            if(isset($total_asset_purchase_payments_per_year_array[$year])){
                $fixed_asset_value = $total_asset_purchase_payments_per_year_array[$year];
            }else{
                if(isset($fixed_asset_value)){

                }else{
                    $fixed_asset_value = 0;
                }
            }
            $total_assets_per_year_array[$year] += $fixed_asset_value;
        endforeach;
        $fixed_asset_value = 0;

        foreach($years_array as $year):
            if(isset($total_stock_purchases_per_year_array[$year])){
                $stock_purchase_value = $total_stock_purchases_per_year_array[$year];
            }else{
                if(isset($stock_purchase_value)){

                }else{
                    $stock_purchase_value = 0;
                }
            }
            $total_assets_per_year_array[$year] += $stock_purchase_value;
        endforeach;

        $stock_purchase_value = 0;

        foreach($years_array as $year):
            
            if(isset($account_balances_per_year_array[$year])){
                $cash_at_bank = $account_balances_per_year_array[$year];
            }else{
                if(isset($cash_at_bank)){

                }else{
                    $cash_at_bank = 0;
                }
            }
            $total_assets_per_year_array[$year] += $cash_at_bank;
        endforeach;

        $cash_at_bank = 0;

        //liabilities
        foreach($years_array as $year):
            if(isset($refundable_contributions_per_year_array[$year])){
                $refundable_member_deposits = $refundable_contributions_per_year_array[$year];
            }else{
                if(isset($refundable_member_deposits)){

                }else{
                    $refundable_member_deposits = 0;
                }
            }
            $total_liabilities_per_year_array[$year] += $refundable_member_deposits;
        endforeach;
        $refundable_member_deposits = 0;

        foreach($years_array as $year):
            if(isset($total_loan_overpayments_per_year_array[$year])){
                $loan_overpayment = $total_loan_overpayments_per_year_array[$year];
            }else{
                if(isset($loan_overpayment)){

                }else{
                    $loan_overpayment = 0;
                }
            }
            $total_liabilities_per_year_array[$year] += $loan_overpayment;
        endforeach;
        $loan_overpayment = 0;

        //equity
        foreach($non_refundable_contribution_options as $contribution_id => $name):
            foreach($years_array as $year):
                if(isset($non_refundable_contributions_per_year_array[$contribution_id][$year])){
                    $non_refundable_member_deposits = $non_refundable_contributions_per_year_array[$contribution_id][$year];
                }else{
                    if(isset($non_refundable_member_deposits)){

                    }else{
                        $non_refundable_member_deposits = 0;
                    }
                }
                $total_owners_equity_per_year_array[$year] += $non_refundable_member_deposits;
            endforeach;
        endforeach;
        $non_refundable_member_deposits = 0;

        foreach($years_array as $year):
            if(isset($share_transfer_fund_per_year_array[$year])){
                $share_transfer_fund = $share_transfer_fund_per_year_array[$year];
            }else{
                if(isset($share_transfer_fund)){

                }else{
                    $share_transfer_fund = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $share_transfer_fund;
        endforeach;
        $share_transfer_fund = 0;

        foreach($years_array as $year):
            if(isset($general_reserves_per_year_array[$year])){
                $general_reserves = $general_reserves_per_year_array[$year];
            }else{
                if(isset($general_reserves)){

                }else{
                    $general_reserves = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $general_reserves;
        endforeach;
        $general_reserves = 0;

        foreach($years_array as $year):
            if(isset($institutional_capital_fund_per_year_array[$year])){
                $institutional_capital = $institutional_capital_fund_per_year_array[$year];
            }else{
                if(isset($institutional_capital_fund_per_year_array[$year])){

                }else{
                    $institutional_capital = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $institutional_capital;
        endforeach;
        $institutional_capital = 0;

        foreach($years_array as $year):
            if(isset($educational_fund_per_year_array[$year])){
                $educational_fund = $educational_fund_per_year_array[$year];
            }else{
                if(isset($educational_fund_per_year_array[$year])){

                }else{
                    $educational_fund = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $educational_fund;
        endforeach;
        $educational_fund = 0;

        foreach($years_array as $year):
            if(isset($retained_earnings_per_year_array[$year])){
                $retained_earnings = $retained_earnings_per_year_array[$year];
            }else{
                if(isset($retained_earnings)){

                }else{
                    $retained_earnings = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $retained_earnings;
        endforeach;
        $retained_earnings = 0;

        // 
        foreach($years_array as $year):

            $balancing_difference_per_year_array[$year] = $total_assets_per_year_array[$year] - ($total_owners_equity_per_year_array[$year] + $total_liabilities_per_year_array[$year]);

        endforeach;

        foreach($years_array as $year):
            $total_assets_per_year_array[$year] -= $balancing_difference_per_year_array[$year];
        endforeach;


        $html = '
            <div class="report invoice-content-2 bordered document-border">
                <div class="row invoice-head">
                    <div class="col-md-7 col-xs-6">
                        <div class="invoice-logo">
                            <img src="';
                                $html.=is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                                $html.='" alt="" class="group-logo image-responsive" /> 
                        </div>
                    </div>
                    <div class="col-md-5 col-xs-6 text-right">
                        <div class="company-address">
                            <span class="bold uppercase">'.$this->group->name.'</span><br/>';
                            $html.=nl2br($this->group->address);
                            $html.='<br/>
                            <span class="bold">'.translate('Telephone').': </span> '.$this->group->phone.'
                            <br/>
                            <span class="bold">'.translate('Email Address').': </span> '.$this->group->email.'
                            <br/>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-center margin-top-10 margin-bottom-10">
                        '.ucfirst($this->group->name).'<br/>
                        '.translate('Trial balance as at').' '.$from_year.' '.translate('to').' '.$to_year.'
                    </div>
                </div>
                <hr/>
                <div class="row invoice-body">
                    <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement">
                        <thead>
                            <tr>
                                <th width="20%"></th>';
                                $width = round(80/count($years_array));
                                foreach($years_array as $year):
                                    $html .='
                                    <th colspan="2" class="text-right" width="'.$width.'%">'.$year.'</th>';
                                endforeach;
                            $html .='
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>'.translate('Account Title').'</strong></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right">
                                        <strong>
                                            '.translate('Debit').'
                                        </strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>
                                            '.translate('Credit').'
                                        </strong>
                                    </td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>'.translate('Receivables').'</small></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="bold theme-font text-right"><small>'.number_to_currency(0).'</small></td>
                                    <td class="text-right"><small></small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>'.translate('Other financial assets').'</small></td>';
                                foreach($years_array as $year):
                                    if(isset($total_principal_money_market_investment_out_per_year_array[$year])){
                                        $other_financial_assets = $total_principal_money_market_investment_out_per_year_array[$year];
                                    }else{
                                        $other_financial_assets = 0;
                                    }

                                    if(isset($total_stock_purchases_per_year_array[$year])){
                                        $other_financial_assets += $total_stock_purchases_per_year_array[$year];
                                    }else{
                                        if(isset($other_financial_assets)){

                                        }else{
                                            $other_financial_assets = 0;
                                        }
                                    }
                                    $debit_totals_per_year_array[$year] += $other_financial_assets;

                                    //$total_assets_per_year_array[$year] += $other_financial_assets;
                                    $html .= ' 
                                    <td class=" bold theme-font text-right"><small>'.number_to_currency($other_financial_assets).'</small></td>
                                    <td class="text-right"><small></small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small><a href="'.site_url('group/reports/loans_summary').'">'.translate('Loans to members').'</a></small></td>';
                                foreach($years_array as $year):
                                    if(isset($total_principal_loans_out_per_year_array[$year])){
                                        $loan_to_members = $total_principal_loans_out_per_year_array[$year] - $balancing_difference_per_year_array[$year];
                                    }else{
                                        $loan_to_members = 0;
                                    }
                                    $debit_totals_per_year_array[$year] += $loan_to_members;
                                    $html .= ' 
                                    <td class=" bold theme-font text-right"><small>'.number_to_currency($loan_to_members).'</small></td>
                                    <td class="text-right"><small></small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>'.translate('Property, plant and equipment').'</small></td>';
                                foreach($years_array as $year):
                                    if(isset($total_asset_purchase_payments_per_year_array[$year])){
                                        $fixed_asset_value = $total_asset_purchase_payments_per_year_array[$year];
                                    }else{
                                        if(isset($fixed_asset_value)){

                                        }else{
                                            $fixed_asset_value = 0;
                                        }
                                    }
                                    $debit_totals_per_year_array[$year] += $fixed_asset_value;
                                    $html .= ' 
                                    <td class=" bold theme-font text-right"><small>'.number_to_currency($fixed_asset_value).'</small></td>
                                    <td class="text-right"><small></small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>'.translate('Cash/ Cash Equivalent').'</small></td>';
                                foreach($years_array as $year): 
                                    if(isset($account_balances_per_year_array[$year])){
                                        $cash_at_bank = $account_balances_per_year_array[$year];
                                    }else{
                                        if(isset($cash_at_bank)){

                                        }else{
                                            $cash_at_bank = 0;
                                        }
                                    }
                                    $debit_totals_per_year_array[$year] += $cash_at_bank;
                                    $html .= ' 
                                    <td class=" bold theme-font text-right"><small>'.(($cash_at_bank >= 0)?number_to_currency($cash_at_bank):"(".number_to_currency(abs($cash_at_bank)).")").'</small></td>
                                    <td class="text-right"><small></small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>'.translate('Members deposits').'</small></td>';
                                foreach($years_array as $year):
                                    if(isset($refundable_contributions_per_year_array[$year])){
                                        $refundable_member_deposits = $refundable_contributions_per_year_array[$year];
                                    }else{
                                        if(isset($refundable_member_deposits)){

                                        }else{
                                            $refundable_member_deposits = 0;
                                        }
                                    }
                                    $credit_totals_per_year_array[$year] += $refundable_member_deposits;

                                    $html .= ' 
                                    <td class="text-right"><small></small></td>
                                    <td class="bold theme-font text-right"><small>'.number_to_currency($refundable_member_deposits).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                                <tr class="listing">
                                    <td><small>'.translate('Members Interest payable').'</small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small></small></td>
                                        <td class=" bold theme-font text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>'.translate('Trades and other payables').'</small></td>';
                                    foreach($years_array as $year):
                                        $html .= '
                                        <td class="text-right"><small></small></td>
                                        <td class="bold theme-font text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>'.translate('Sundry creditors').'</small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small></small></td>
                                        <td class="bold theme-font text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>'.translate('Dividends payable').'</small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small></small></td>
                                        <td class="bold theme-font text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>'.translate('Interest bearing liability').'</small></td>';
                                    foreach($years_array as $year):
                                        if(isset($total_interest_bearing_liability_per_year_array[$year])){
                                            $bank_loans = $total_interest_bearing_liability_per_year_array[$year];
                                        }else{
                                            $bank_loans = 0;
                                        }
                                        $credit_totals_per_year_array[$year] += $bank_loans;
                                        $html .= ' 
                                        <td class="text-right"><small></small></td>
                                        <td class="text-right"><small>'.number_to_currency($bank_loans).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>

                                <tr class="listing">
                                    <td><small>'.translate('Member loan overpayments').' </small></td>';
                                    foreach($years_array as $year):
                                        if(isset($total_loan_overpayments_per_year_array[$year])){
                                            $loan_overpayment = $total_loan_overpayments_per_year_array[$year];
                                        }else{
                                            if(isset($loan_overpayment)){

                                            }else{
                                                $loan_overpayment = 0;
                                            }
                                        }
                                        $credit_totals_per_year_array[$year] += $loan_overpayment;
                                        $html .= ' 
                                        <td class="text-right"><small></small></td>
                                        <td class="bold theme-font text-right"><small>'.number_to_currency($loan_overpayment).'</small></td>';
                                    endforeach;
                                $html .='                                
                                </tr>';
                                foreach($non_refundable_contribution_options as $contribution_id => $name):
                                    $html.='
                                    <tr class="listing">
                                        <td><small>&nbsp;&nbsp;&nbsp;'.$name.'</small></td>';
                                        foreach($years_array as $year):
                                            if(isset($non_refundable_contributions_per_year_array[$contribution_id][$year])){
                                                $non_refundable_member_deposits = $non_refundable_contributions_per_year_array[$contribution_id][$year];
                                            }else{
                                                if(isset($non_refundable_member_deposits)){

                                                }else{
                                                    $non_refundable_member_deposits = 0;
                                                }
                                            }
                                            $html .= ' 
                                            <td class="bold theme-font text-right"><small>'.number_to_currency($non_refundable_member_deposits).'</small></td>';
                                        endforeach;
                                    $html .='
                                    </tr>';
                                endforeach;
                                $html .='
                                
                                <tr class="listing">
                                    <td><small>'.translate('Retained Earnings').'</small></td>';
                                    foreach($years_array as $year):

                                        if(isset($retained_earnings_per_year_array[$year])){
                                            $retained_earnings = $retained_earnings_per_year_array[$year];
                                        }else{
                                            if(isset($retained_earnings)){

                                            }else{
                                                $retained_earnings = 0;
                                            }
                                        }

                                        $credit_totals_per_year_array[$year] += $retained_earnings;
                                        $html .= ' 
                                        <td class="text-right"><small></small></td>
                                        <td class="bold theme-font text-right"><small>'.($retained_earnings < 0?'('.number_to_currency(abs($retained_earnings)).')':number_to_currency($retained_earnings)).'</small></td>
                                        ';
                                    
                                    endforeach;
                                $html .='
                                </tr>
                            <tr>
                                <td><strong>'.translate('Totals').'</strong></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="bold theme-font text-right">
                                        <strong class="bold theme-font ">
                                            <small>'.number_to_currency($debit_totals_per_year_array[$year]).'</small>
                                        </strong>
                                    </td>
                                    <td class="bold theme-font text-right">
                                        <strong class="bold theme-font ">
                                            <small>'.number_to_currency($credit_totals_per_year_array[$year]).'</small>
                                        </strong>
                                    </td>';
                                endforeach;
                            $html .='
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div> 
        ';

        echo $html;
    }

    function get_monthly_trial_balance(){

        $from_month = strtotime($this->input->get_post('from'));
        $to_month = strtotime($this->input->get_post('to'));

        $display_months_array = array();
        if($from_month && $to_month){
            $display_months_array = generate_months_array($from_month,$to_month);
        }else{
            $from_month = strtotime('-3 months');
            $to_month = strtotime('today');
            $display_months_array = generate_months_array($from_month,$to_month);
        }

        $current_month = date('M Y');
        $start_month = 'Jan 1971';

        $months_array = generate_months_array(strtotime($start_month),strtotime($current_month));

        $non_refundable_contributions = $this->contributions_m->get_group_non_equitable_non_refundable_contributions();

        $refundable_contribution_options = $this->contributions_m->get_group_refundable_contribution_options();

        $non_refundable_contribution_options = $this->contributions_m->get_group_equitable_non_refundable_contribution_options();

        $other_financial_assets_per_month_array = array();
        $total_assets_per_month_array = array();
        $total_liabilities_per_month_array = array();


        $total_principal_money_market_investment_out_per_month_array = $this->money_market_investments_m->get_group_total_principal_money_market_investment_out_per_month_array($this->group->id);

        $total_asset_purchase_payments_per_month_array = $this->withdrawals_m->get_group_total_asset_purchase_payments_per_month_array($this->group->id);

        $total_stock_purchases_per_month_array = $this->stocks_m->get_group_total_stocks_retained_per_month_array($this->group->id);

        $total_principal_loans_out_per_month_array = $this->loans_m->get_group_total_principal_loans_out_per_month_array($this->group->id);

        $total_interest_bearing_liability_per_month_array = $this->bank_loans_m->get_group_total_interest_bearing_liability_per_year_array($this->group->id);

        $account_balances_per_month_array = $this->transaction_statements_m->get_group_account_balances_per_month_array($this->group->id);

        $refundable_contributions_per_month_array = $this->reports_m->get_group_total_contributions_per_month_array($this->group->id,0,0,$refundable_contribution_options,$months_array);

        $administrative_expense_categories = $this->expense_categories_m->get_group_administrative_expense_category_options();

        $other_expense_categories = $this->expense_categories_m->get_group_other_expense_category_options();

        $non_refundable_contributions_per_month_array =  $this->reports_m->get_group_total_contributions_per_month_array($this->group->id,0,0,$non_refundable_contribution_options,$months_array);

        $total_loan_interest_paid_per_month_array = $this->reports_m->get_group_total_loan_interest_paid_per_month_array($this->group->id);

        $total_money_market_interest_per_month_array = $this->money_market_investments_m->get_group_total_money_market_interest_per_month_array($this->group->id);

        $total_stocks_sale_income_per_month_array = $this->stocks_m->get_group_total_stocks_sale_income_per_month_array($this->group->id);

        $total_bank_loans_interest_paid_per_month_array = $this->withdrawals_m->get_group_total_bank_loans_interest_paid_per_month_array();

        $total_stocks_sale_losses_per_month_array = $this->stocks_m->get_group_total_stocks_sale_losses_per_month_array($this->group->id);

        $total_contributions_paid_per_contribution_per_month_array = $this->reports_m->get_group_total_contributions_paid_per_contribution_per_month_array();

        $expense_category_totals_per_month_array = $this->withdrawals_m->get_group_expense_category_totals_per_month_array();

        $total_loan_processing_income_per_month_array = $this->deposits_m->get_group_total_loan_processing_income_per_month_array();

        $total_income_per_month_array = $this->deposits_m->get_group_total_income_per_month_array();

        $total_miscellaneous_income_per_month_array = $this->deposits_m->get_group_total_miscellaneous_income_per_month_array();

        $total_fines_per_month_array = $this->reports_m->get_group_total_fines_per_month_array($this->group->id);

        $total_loan_overpayments_per_month_array = $this->reports_m->get_group_total_loan_overpayments_per_month_array($this->group->id);
        
        $total_month_interest_array = array();
        $net_month_interest_income_array = array();
        $net_month_income_array = array();
        $total_month_income_array = array();
        $month_surplus_before_tax_array = array();
        $month_surplus_after_tax_array = array();
        $net_surplus_for_the_month_array = array();
        $total_non_refundable_contributions_paid_per_month_array = array();
        $other_operating_income_per_month_array = array();
        $loan_processing_income_per_month_array = array();
        $group_income_per_month_array = array();
        $group_expenses_per_month_array = array();
        $transfer_to_statutory_reserve_per_month_array = array();
        $retained_earnings_per_month_array = array();
        $total_owners_equity_per_month_array = array();


        foreach($months_array as $month):
            $total_month_interest_array[$month] = 0;
            $net_month_interest_income_array[$month] = 0;
            $net_month_income_array[$month] = 0;
            $total_month_income_array[$month] = 0;
            $month_surplus_before_tax_array[$month] = 0;
            $month_surplus_after_tax_array[$month] = 0;
            $net_surplus_for_the_month_array[$month] = 0;
            $total_non_refundable_contributions_paid_per_month_array[$month] = 0;
            $other_operating_income_per_month_array[$month] = 0;
            $loan_processing_income_per_month_array[$month] = 0;
            $group_income_per_month_array[$month] = 0;
            $group_expenses_per_month_array[$month] = 0;
            $transfer_to_statutory_reserve_per_month_array[$month] = 0;
            $retained_earnings_per_month_array[$month] = 0;
            $general_reserves_per_month_array[$month] = 0;
            $share_transfer_fund_per_month_array[$month] = 0;
            $institutional_capital_fund_per_month_array[$month] =  0;
            $educational_fund_per_month_array[$month] =  0;
            $total_owners_equity_per_month_array[$month] =  0;
            $balancing_difference_per_month_array[$month] = 0;
            $total_assets_per_month_array[$month] = 0;
            $other_financial_assets_per_month_array[$month] = 0;
            $total_liabilities_per_month_array[$month] = 0;
            $credit_totals_per_month_array[$month] = 0;
            $debit_totals_per_month_array[$month] = 0;
        endforeach;

        foreach($non_refundable_contributions as $contribution):
            foreach($months_array as $month):
                $total_non_refundable_contributions_paid_per_month_array[$month] += isset($total_contributions_paid_per_contribution_per_month_array[$contribution->id][$month])?$total_contributions_paid_per_contribution_per_month_array[$contribution->id][$month]:0;
            endforeach;
        endforeach;

        foreach($administrative_expense_categories as $administrative_expense_category_id =>  $administrative_expense_category_name):
            foreach($months_array as $month):
                $group_expenses_per_month_array[$month] += (isset($expense_category_totals_per_month_array[$administrative_expense_category_id][$month])?$expense_category_totals_per_month_array[$administrative_expense_category_id][$month]:0);
            endforeach;
        endforeach;

        foreach($other_expense_categories as $other_expense_category_id =>  $other_expense_category_name):
            foreach($months_array as $month):
                $group_expenses_per_month_array[$month] += (isset($expense_category_totals_per_month_array[$other_expense_category_id][$month])?$expense_category_totals_per_month_array[$other_expense_category_id][$month]:0);
            endforeach;
        endforeach;


        foreach($months_array as $month):

            $total_month_interest_array[$month] += (isset($total_loan_interest_paid_per_month_array[$month])?$total_loan_interest_paid_per_month_array[$month]:0);

            $total_month_interest_array[$month] += (isset($total_money_market_interest_per_month_array[$month])?$total_money_market_interest_per_month_array[$month]:0);

            $total_month_interest_array[$month] += (isset($total_stocks_sale_income_per_month_array[$month])?$total_stocks_sale_income_per_month_array[$month]:0);

            $net_month_interest_income_array[$month] += $total_month_interest_array[$month];

            $net_month_interest_income_array[$month] -= (isset($total_bank_loans_interest_paid_per_month_array[$month])?($total_bank_loans_interest_paid_per_month_array[$month]):0);

            $net_month_interest_income_array[$month] -= (isset($total_stocks_sale_losses_per_month_array[$month])?($total_stocks_sale_losses_per_month_array[$month]):0);

            $other_operating_income_per_month_array[$month] += $total_non_refundable_contributions_paid_per_month_array[$month];

            $other_operating_income_per_month_array[$month] += (isset($total_loan_processing_income_per_month_array[$month])?$total_loan_processing_income_per_month_array[$month]:0);

            $other_operating_income_per_month_array[$month] += (isset($total_income_per_month_array[$month])?$total_income_per_month_array[$month]:0);

            $other_operating_income_per_month_array[$month] += (isset($total_miscellaneous_income_per_month_array[$month])?$total_miscellaneous_income_per_month_array[$month]:0);

            $other_operating_income_per_month_array[$month] += (isset($total_fines_per_month_array[$month])?$total_fines_per_month_array[$month]:0);

            $month_surplus_before_tax_array[$month] += $net_month_interest_income_array[$month] + $other_operating_income_per_month_array[$month] - $group_expenses_per_month_array[$month];

            $month_surplus_after_tax_array[$month] = $month_surplus_before_tax_array[$month];

            $general_reserves_per_month_array[$month] = (($month_surplus_after_tax_array[$month] * 0.00));

            $share_transfer_fund_per_month_array[$month] = (($month_surplus_after_tax_array[$month] * 0.00));

            $institutional_capital_fund_per_month_array[$month] = (($month_surplus_after_tax_array[$month] * 0.00));

            $educational_fund_per_month_array[$month] = (($month_surplus_after_tax_array[$month] * 0.00));

            $transfer_to_statutory_reserve_per_month_array[$month] = (($month_surplus_after_tax_array[$month] * 0.00));

            $retained_earnings_per_month_array[$month] = (($month_surplus_before_tax_array[$month] - $transfer_to_statutory_reserve_per_month_array[$month]));
        endforeach;
        
        foreach($months_array as $month):

            $previous_month = date('M Y',strtotime('-1 month',strtotime($month)));

            if(isset($retained_earnings_per_month_array[($previous_month)])){
                $retained_earnings_per_month_array[$month] += $retained_earnings_per_month_array[($previous_month)];
            }
            if(isset($general_reserves_per_month_array[($previous_month)])){
                $general_reserves_per_month_array[$month] += $general_reserves_per_month_array[($previous_month)];
            }
            if(isset($share_transfer_fund_per_month_array[($previous_month)])){
                $share_transfer_fund_per_month_array[$month] += $share_transfer_fund_per_month_array[($previous_month)];
            }
            if(isset($institutional_capital_fund_per_month_array[($previous_month)])){
                $institutional_capital_fund_per_month_array[$month] += $institutional_capital_fund_per_month_array[($previous_month)];
            }
            if(isset($educational_fund_per_month_array[($previous_month)])){
                $educational_fund_per_month_array[$month] += $educational_fund_per_month_array[($previous_month)];
            }

        endforeach;

        //Assets addition
        foreach($months_array as $month):
            if(isset($total_principal_money_market_investment_out_per_month_array[$month])){
                $other_financial_assets = $total_principal_money_market_investment_out_per_month_array[$month];
            }else{
                $other_financial_assets = 0;
            }
            $total_assets_per_month_array[$month] += $other_financial_assets;
        endforeach;
        $other_financial_assets = 0;

        foreach($months_array as $month):
            if(isset($total_principal_loans_out_per_month_array[$month])){
                $loan_to_members = $total_principal_loans_out_per_month_array[$month];
            }else{
                if(isset($loan_to_members)){

                }else{
                    $loan_to_members = 0;
                }
            }
            $total_assets_per_month_array[$month] += $loan_to_members;
        endforeach;

        foreach($months_array as $month):
            if(isset($total_interest_bearing_liability_per_month_array[$month])){
                $bank_loans = $total_interest_bearing_liability_per_month_array[$month];
            }else{
                if(isset($bank_loans)){

                }else{
                    $bank_loans = 0;
                }
            }
            $total_liabilities_per_month_array[$month] += $bank_loans;
        endforeach;
        $loan_to_members = 0;
        
        foreach($months_array as $month):
            if(isset($total_asset_purchase_payments_per_month_array[$month])){
                $fixed_asset_value = $total_asset_purchase_payments_per_month_array[$month];
            }else{
                if(isset($fixed_asset_value)){

                }else{
                    $fixed_asset_value = 0;
                }
            }
            $total_assets_per_month_array[$month] += $fixed_asset_value;
        endforeach;
        $fixed_asset_value = 0;

        foreach($months_array as $month):
            if(isset($total_stock_purchases_per_month_array[$month])){
                $stock_purchase_value = $total_stock_purchases_per_month_array[$month];
            }else{
                if(isset($stock_purchase_value)){

                }else{
                    $stock_purchase_value = 0;
                }
            }
            $total_assets_per_month_array[$month] += $stock_purchase_value;
        endforeach;

        $stock_purchase_value = 0;

        foreach($months_array as $month):
            
            if(isset($account_balances_per_month_array[$month])){
                $cash_at_bank = $account_balances_per_month_array[$month];
            }else{
                if(isset($cash_at_bank)){

                }else{
                    $cash_at_bank = 0;
                }
            }
            $total_assets_per_month_array[$month] += $cash_at_bank;
        endforeach;

        $cash_at_bank = 0;

        //liabilities
        foreach($months_array as $month):
            if(isset($refundable_contributions_per_month_array[$month])){
                $refundable_member_deposits = $refundable_contributions_per_month_array[$month];
            }else{
                if(isset($refundable_member_deposits)){

                }else{
                    $refundable_member_deposits = 0;
                }
            }
            $total_liabilities_per_month_array[$month] += $refundable_member_deposits;
        endforeach;
        $refundable_member_deposits = 0;

        foreach($months_array as $month):
            if(isset($total_loan_overpayments_per_month_array[$month])){
                $loan_overpayment = $total_loan_overpayments_per_month_array[$month];
            }else{
                if(isset($loan_overpayment)){

                }else{
                    $loan_overpayment = 0;
                }
            }
            $total_liabilities_per_month_array[$month] += $loan_overpayment;
        endforeach;
        $loan_overpayment = 0;

        foreach($non_refundable_contribution_options as $contribution_id => $name):
            foreach($months_array as $month):
                if(isset($non_refundable_contributions_per_month_array[$contribution_id][$month])){
                    $non_refundable_member_deposits = $non_refundable_contributions_per_month_array[$contribution_id][$month];
                }else{
                    if(isset($non_refundable_member_deposits)){

                    }else{
                        $non_refundable_member_deposits = 0;
                    }
                }
                $total_owners_equity_per_month_array[$month] += $non_refundable_member_deposits;
            endforeach;
        endforeach;
        $non_refundable_member_deposits = 0;

        foreach($months_array as $month):
            if(isset($share_transfer_fund_per_month_array[$month])){
                $share_transfer_fund = $share_transfer_fund_per_month_array[$month];
            }else{
                if(isset($share_transfer_fund)){

                }else{
                    $share_transfer_fund = 0;
                }
            }
            $total_owners_equity_per_month_array[$month] += $share_transfer_fund;
        endforeach;
        $share_transfer_fund = 0;

        foreach($months_array as $month):
            if(isset($general_reserves_per_month_array[$month])){
                $general_reserves = $general_reserves_per_month_array[$month];
            }else{
                if(isset($general_reserves)){

                }else{
                    $general_reserves = 0;
                }
            }
            $total_owners_equity_per_month_array[$month] += $general_reserves;
        endforeach;
        $general_reserves = 0;

        foreach($months_array as $month):
            if(isset($institutional_capital_fund_per_month_array[$month])){
                $institutional_capital = $institutional_capital_fund_per_month_array[$month];
            }else{
                if(isset($institutional_capital_fund_per_month_array[$month])){

                }else{
                    $institutional_capital = 0;
                }
            }
            $total_owners_equity_per_month_array[$month] += $institutional_capital;
        endforeach;
        $institutional_capital = 0;

        foreach($months_array as $month):
            if(isset($educational_fund_per_month_array[$month])){
                $educational_fund = $educational_fund_per_month_array[$month];
            }else{
                if(isset($educational_fund_per_month_array[$month])){

                }else{
                    $educational_fund = 0;
                }
            }
            $total_owners_equity_per_month_array[$month] += $educational_fund;
        endforeach;
        $educational_fund = 0;

        foreach($months_array as $month):
            if(isset($retained_earnings_per_month_array[$month])){
                $retained_earnings = $retained_earnings_per_month_array[$month];
            }else{
                if(isset($retained_earnings)){

                }else{
                    $retained_earnings = 0;
                }
            }
            $total_owners_equity_per_month_array[$month] += $retained_earnings;
        endforeach;
        $retained_earnings = 0;

        foreach($months_array as $month):
            $total_assets_per_month_array[$month] -= $balancing_difference_per_month_array[$month];
        endforeach;

        
        $html = '
            <div class="report invoice-content-2 bordered document-border">
                <div class="row invoice-head">
                    <div class="col-md-7 col-xs-6">
                        <div class="invoice-logo">
                            <img src="';
                                $html.=is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                                $html.='" alt="" class="group-logo image-responsive" /> 
                        </div>
                    </div>
                    <div class="col-md-5 col-xs-6 text-right">
                        <div class="company-address">
                            <span class="bold uppercase">'.$this->group->name.'</span><br/>';
                            $html.=nl2br($this->group->address);
                            $html.='<br/>
                            <span class="bold">Telephone: </span> '.$this->group->phone.'
                            <br/>
                            <span class="bold">E-mail Address: </span> '.$this->group->email.'
                            <br/>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-center margin-top-10 margin-bottom-10">
                        '.ucfirst($this->group->name).'<br/>
                        Trial balance as at '.date("M Y",$from_month).' to '.date("M Y",$to_month).'
                    </div>
                </div>
                <hr/>
                <div class="row invoice-body">
                    <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement">
                        <thead>
                            <tr>
                                <th width="20%"></th>';
                                $width = round(80/count($display_months_array));
                                foreach($display_months_array as $month):
                                    $html .='
                                    <th colspan="2" class="text-right" width="'.$width.'%">'.$month.'</th>';
                                endforeach;
                            $html .='
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong class="bold theme-font">Account Title</strong></td>';
                                foreach($display_months_array as $month):
                                    $html .= ' 
                                    <td class="bold theme-font text-right">
                                        <strong class="bold theme-font">
                                            Debit
                                        </strong>
                                    </td>
                                    <td class="text-right">
                                        <strong class="bold theme-font">
                                            Credit
                                        </strong>
                                    </td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Receivables</small></td>';
                                foreach($display_months_array as $month):
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency(0).'</small></td>
                                    <td class="text-right"><small></small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Other financial assets</small></td>';
                                foreach($display_months_array as $month):
                                    if(isset($total_principal_money_market_investment_out_per_month_array[$month])){
                                        $other_financial_assets = $total_principal_money_market_investment_out_per_month_array[$month];
                                    }else{
                                        $other_financial_assets = 0;
                                    }

                                    if(isset($total_stock_purchases_per_month_array[$month])){
                                        $other_financial_assets += $total_stock_purchases_per_month_array[$month];
                                    }else{
                                        if(isset($other_financial_assets)){

                                        }else{
                                            $other_financial_assets = 0;
                                        }
                                    }
                                    $debit_totals_per_month_array[$month] += $other_financial_assets;

                                    //$total_assets_per_month_array[$month] += $other_financial_assets;
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency($other_financial_assets).'</small></td>
                                    <td class="text-right"><small></small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small><a href="'.site_url('group/reports/loans_summary').'">Loans to members</a></small></td>';
                                foreach($display_months_array as $month):
                                    if(isset($total_principal_loans_out_per_month_array[$month])){
                                        $loan_to_members = $total_principal_loans_out_per_month_array[$month] - $balancing_difference_per_month_array[$month];
                                    }else{
                                        $loan_to_members = 0;
                                    }
                                    $debit_totals_per_month_array[$month] += $loan_to_members;
                                    $html .= ' 
                                    <td class="text-right"><small>'.($loan_to_members<0?'('.number_to_currency(abs($loan_to_members)).')':number_to_currency($loan_to_members)).'</small></td>
                                    <td class="text-right"><small></small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Property, plant and equipment</small></td>';
                                foreach($display_months_array as $month):
                                    if(isset($total_asset_purchase_payments_per_month_array[$month])){
                                        $fixed_asset_value = $total_asset_purchase_payments_per_month_array[$month];
                                    }else{
                                        if(isset($fixed_asset_value)){

                                        }else{
                                            $fixed_asset_value = 0;
                                        }
                                    }
                                    $debit_totals_per_month_array[$month] += $fixed_asset_value;
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency($fixed_asset_value).'</small></td>
                                    <td class="text-right"><small></small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Cash/ Cash Equivalent</small></td>';
                                foreach($display_months_array as $month): 
                                    if(isset($account_balances_per_month_array[$month])){
                                        $cash_at_bank = $account_balances_per_month_array[$month];
                                    }else{
                                        if(isset($cash_at_bank)){

                                        }else{
                                            $cash_at_bank = 0;
                                        }
                                    }
                                    $debit_totals_per_month_array[$month] += $cash_at_bank;
                                    $html .= ' 
                                    <td class="text-right"><small>'.(($cash_at_bank >= 0)?number_to_currency($cash_at_bank):"(".number_to_currency(abs($cash_at_bank)).")").'</small></td>
                                    <td class="text-right"><small></small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Members\' deposits</small></td>';
                                foreach($display_months_array as $month):
                                    if(isset($refundable_contributions_per_month_array[$month])){
                                        $refundable_member_deposits = $refundable_contributions_per_month_array[$month];
                                    }else{
                                        if(isset($refundable_member_deposits)){

                                        }else{
                                            $refundable_member_deposits = 0;
                                        }
                                    }
                                    $credit_totals_per_month_array[$month] += $refundable_member_deposits;

                                    $html .= ' 
                                    <td class="text-right"><small></small></td>
                                    <td class="text-right"><small>'.number_to_currency($refundable_member_deposits).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                                <tr class="listing">
                                    <td><small>Members\' Interest payable</small></td>';
                                    foreach($display_months_array as $month):
                                        $html .= ' 
                                        <td class="text-right"><small></small></td>
                                        <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>Trades and other payables</small></td>';
                                    foreach($display_months_array as $month):
                                        $html .= '
                                        <td class="text-right"><small></small></td>
                                        <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>Sundry creditors</small></td>';
                                    foreach($display_months_array as $month):
                                        $html .= ' 
                                        <td class="text-right"><small></small></td>
                                        <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>Dividends payable</small></td>';
                                    foreach($display_months_array as $month):
                                        $html .= ' 
                                        <td class="text-right"><small></small></td>
                                        <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>Interest bearing liability</small></td>';
                                    foreach($display_months_array as $month):
                                        if(isset($total_interest_bearing_liability_per_month_array[$month])){
                                            $bank_loans = $total_interest_bearing_liability_per_month_array[$month];
                                        }else{
                                            $bank_loans = 0;
                                        }
                                        $credit_totals_per_month_array[$month] += $bank_loans;
                                        $html .= ' 
                                        <td class="text-right"><small></small></td>
                                        <td class="text-right"><small>'.number_to_currency($bank_loans).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>

                                <tr class="listing">
                                    <td><small>Member loan overpayments </small></td>';
                                    foreach($display_months_array as $month):
                                        if(isset($total_loan_overpayments_per_month_array[$month])){
                                            $loan_overpayment = $total_loan_overpayments_per_month_array[$month];
                                        }else{
                                            if(isset($loan_overpayment)){

                                            }else{
                                                $loan_overpayment = 0;
                                            }
                                        }
                                        $credit_totals_per_month_array[$month] += $loan_overpayment;
                                        $html .= ' 
                                        <td class="text-right"><small></small></td>
                                        <td class="text-right"><small>'.number_to_currency($loan_overpayment).'</small></td>';
                                    endforeach;
                                $html .='                                
                                </tr>';
                                foreach($non_refundable_contribution_options as $contribution_id => $name):
                                    $html.='
                                    <tr class="listing">
                                        <td><small>&nbsp;&nbsp;&nbsp;'.$name.'</small></td>';
                                        foreach($display_months_array as $month):
                                            if(isset($non_refundable_contributions_per_month_array[$contribution_id][$month])){
                                                $non_refundable_member_deposits = $non_refundable_contributions_per_month_array[$contribution_id][$month];
                                            }else{
                                                if(isset($non_refundable_member_deposits)){

                                                }else{
                                                    $non_refundable_member_deposits = 0;
                                                }
                                            }
                                            $html .= ' 
                                            <td class="text-right"><small>'.number_to_currency($non_refundable_member_deposits).'</small></td>';
                                        endforeach;
                                    $html .='
                                    </tr>';
                                endforeach;
                                $html .='
                                
                                <tr class="listing">
                                    <td><small>Retained Earnings</small></td>';
                                    foreach($display_months_array as $month):

                                        if(isset($retained_earnings_per_month_array[$month])){
                                            $retained_earnings = $retained_earnings_per_month_array[$month];
                                        }else{
                                            if(isset($retained_earnings)){

                                            }else{
                                                $retained_earnings = 0;
                                            }
                                        }

                                        $credit_totals_per_month_array[$month] += $retained_earnings;
                                        $html .= ' 
                                        <td class="text-right"><small></small></td>
                                        <td class="text-right"><small>'.($retained_earnings < 0?'('.number_to_currency(abs($retained_earnings)).')':number_to_currency($retained_earnings)).'</small></td>
                                        ';
                                    
                                    endforeach;
                                $html .='
                                </tr>
                            <tr>
                                <td class=" bold theme-font">Totals</td>';
                                foreach($display_months_array as $month):
                                    $html .= ' 
                                    <td class="text-right">
                                        <strong class=" bold theme-font">
                                           '.number_to_currency($debit_totals_per_month_array[$month]).'
                                        </strong>
                                    </td>
                                    <td class="text-right">
                                        <strong class=" bold theme-font">
                                            '.number_to_currency($credit_totals_per_month_array[$month]).'
                                        </strong>
                                    </td>';
                                endforeach;
                            $html .='
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div> 
        ';

        echo $html;
    }

    function get_balance_sheet_statement(){

        $current_assets_total = 0;
        $cash_at_bank = $this->accounts_m->get_group_total_cash_at_bank();
        $current_assets_total += $cash_at_bank;
        $cash_at_hand = $this->accounts_m->get_group_total_cash_at_hand();
        $current_assets_total += $cash_at_hand;
        $total_money_market_investment = $this->money_market_investments_m->get_group_total_money_market_investment();
        $current_assets_total += $total_money_market_investment;
        $total_contribution_arrears = $this->reports_m->get_group_total_contribution_balance();
        $current_assets_total += $total_contribution_arrears;
        $total_fine_arrears = $this->reports_m->get_group_total_fine_balance();
        $current_assets_total += $total_fine_arrears;
        $total_loan_balance = $this->loan_invoices_m->get_group_total_loan_balance();
        $current_assets_total += $total_loan_balance;

        $long_term_investments_total = 0;
        $total_stock_purchase_amount = $this->withdrawals_m->get_group_stock_purchase_total_amount();
        $long_term_investments_total += $total_stock_purchase_amount;

        $fixed_assets_total = 0;
        $asset_purchase_total = $this->withdrawals_m->get_group_asset_purchase_total_amount();
        $fixed_assets_total += $asset_purchase_total;

        $assets_total = 0;
        $assets_total += $fixed_assets_total;
        $assets_total += $long_term_investments_total;
        $assets_total += $current_assets_total;

        $share_holders_equity_total = 0;

        $total_contributions_payable = $this->invoices_m->get_group_total_contribution_invoices_amount_payable();
        $share_holders_equity_total += $total_contributions_payable;

        if($this->group->disable_arrears){
            $total_group_contribution_refunds = $this->withdrawals_m->get_group_total_contribution_refunds();
            $share_holders_equity_total -= $total_group_contribution_refunds;
        }

        if($this->group->disable_arrears){
            $total_contribution_overpayments = $this->deposits_m->get_group_total_contributions();
            $share_holders_equity_total += $total_contribution_overpayments;
        }else{
            $total_contribution_overpayments = $this->reports_m->get_group_total_contribution_overpayment();
            $share_holders_equity_total += $total_contribution_overpayments;
        }

        $total_fine_overpayments = $this->reports_m->get_group_total_fine_overpayments();

        if($total_fine_overpayments < 0){
            $total_fines_payable = abs($total_fine_overpayments);
        }else{
            $total_fines_payable = $this->invoices_m->get_group_total_fine_invoices_amount_payable();
        }
        $share_holders_equity_total += $total_fines_payable;

        $net_profit_or_loss = 0;
        $total_miscellaneous_income = $this->deposits_m->get_group_member_total_miscellaneous_amount();
        $net_profit_or_loss += $total_miscellaneous_income;
        $total_income = $this->deposits_m->get_group_income_total_amounts();
        $net_profit_or_loss += $total_income;
        $total_money_market_interest = $this->money_market_investments_m->get_group_total_money_market_interest();
        $net_profit_or_loss += $total_money_market_interest;
        $total_expenses = $this->withdrawals_m->get_group_total_expenses();
        $net_profit_or_loss -= $total_expenses;
        $total_loan_interest_and_fines = $this->loan_invoices_m->get_group_total_loan_interest_payable();
        $total_loan_interest_and_fines += $this->loan_invoices_m->get_group_total_loan_fines_payable();                    
        $net_profit_or_loss += $total_loan_interest_and_fines;
        $total_asset_sales = $this->deposits_m->get_group_total_asset_sale_amount();
        $net_profit_or_loss += $total_asset_sales;                          
        $total_bank_loan_interest = $this->bank_loans_m->get_group_bank_loans_interest();
        $net_profit_or_loss -= $total_bank_loan_interest;
        $share_holders_equity_total += $net_profit_or_loss;

        $current_liabilities_total = 0;

        $long_term_liabilities_total = 0;
        $total_bank_loans_payable = $this->bank_loans_m->get_group_total_bank_loan_payable();
        $total_bank_loan_repayments = $this->withdrawals_m->get_group_total_bank_loan_repayment();
        $total_bank_loan_balance = $total_bank_loans_payable - $total_bank_loan_repayments;
        $long_term_liabilities_total += $total_bank_loan_balance;

        $liabilities_total = 0;
        $liabilities_total += $share_holders_equity_total;
        $liabilities_total += $current_liabilities_total;
        $liabilities_total += $long_term_liabilities_total;

        echo '
            <div class="report invoice-content-2 bordered document-border">
                <div class="row invoice-head">
                    <div class="col-md-7 col-xs-6">
                        <div class="invoice-logo">
                            <img src="'.(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo)).'" alt="" class=\'group-logo image-responsive\' /> 
                        </div>
                    </div>
                    <div class="col-md-5 col-xs-6 text-right">
                        <div class="company-address">
                            <span class="bold uppercase">'.$this->group->name.'</span><br/>
                            '.nl2br($this->group->address).'<br/>
                            <span class="bold">';
                                $default_message='Telephone';
                                $this->languages_m->translate('telephone',$default_message);
                                echo '
                                : </span>'.$this->group->phone.'
                            <br/>
                            <span class="bold">';
                                $default_message='Email Address';
                                $this->languages_m->translate('email_address',$default_message);
                                echo '
                                : </span>'.$this->group->email.'
                            <br/>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-center margin-top-10 margin-bottom-10">
                            '.ucfirst($this->group->name).'<br/>
                        Statement of Financial Position as at '.timestamp_to_report_time(time()).'
                    </div>
                </div>
                <hr/>
                <div class="row invoice-body">
                    <table class="table table-bordered table-striped table-condensed">
                        <thead>
                            <tr>
                                <th width="50%">Assets</th>
                                <th width="50%">';
                                    $default_message='Liabilities';
                                    $this->languages_m->translate('liabilities',$default_message);
                                echo '
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr>
                                                <th colspan="4">';
                                                    $default_message='Current Assets';
                                                    $this->languages_m->translate('current_assets',$default_message);
                                                echo '
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                             <tr>
                                                <td width="40%">';
                                                    $default_message='Cash at Bank';
                                                    $this->languages_m->translate('total_cash_at_bank',$default_message);
                                                echo '
                                                </td>
                                                <td width="18%" class="text-right">'.number_to_currency($cash_at_bank).'</td>
                                                <td width="18%" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <td width="40%">';
                                                    $default_message='Cash at Hand';
                                                    $this->languages_m->translate('total_cash_at_hand',$default_message);
                                                echo '
                                                </td>
                                                <td width="18%" class="text-right">'.number_to_currency($cash_at_hand).'</td>
                                                <td width="18%" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <td width="40%">';
                                                    $default_message='Money Market Investments';
                                                    $this->languages_m->translate('money_market_investments',$default_message);
                                                echo '
                                                </td>
                                                <td width="18%" class="text-right">'.number_to_currency($total_money_market_investment).'</td>
                                                <td width="18%" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <td width="40%">';
                                                    $default_message='Contribution Arrears';
                                                    $this->languages_m->translate('contribution_arrears',$default_message);
                                                echo '
                                                </td>
                                                <td width="18%" class="text-right">'.number_to_currency($total_contribution_arrears).'</td>
                                                <td width="18%" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <td width="40%">';
                                                    $default_message='Fine Arrears';
                                                    $this->languages_m->translate('fine_arrears',$default_message);
                                                echo '
                                                </td>
                                                <td width="18%" class="text-right">'.number_to_currency($total_fine_arrears).'</td>
                                                <td width="18%" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <td width="40%">';
                                                    $default_message='Total Loans Out (Principal,Interest & Fines)';
                                                    $this->languages_m->translate('total_loans_out',$default_message);
                                                echo '
                                                </td>
                                                <td width="18%" class="text-right">'.number_to_currency($total_loan_balance).'</td>
                                                <td width="18%" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <td width="40%">';
                                                    $default_message='Total';
                                                    $this->languages_m->translate('total',$default_message);
                                                echo '
                                                </td>
                                                <td width="18%" class="text-right"></td>
                                                <td width="18%" class="text-right">'.number_to_currency($current_assets_total).'</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td>
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr>
                                                <th colspan="4">';
                                                    $default_message='Share Holders Equity';
                                                    $this->languages_m->translate('share_holders_equity',$default_message);
                                                echo '
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>';

                                            if($this->group->disable_arrears){
                                               echo '
                                                <tr>
                                                    <td width="40%">';
                                                        $default_message='Member Contributions';
                                                        $this->languages_m->translate('member_contributions',$default_message);
                                                    echo '
                                                    </td>
                                                    <td width="18%" class="text-right">'.number_to_currency($total_contribution_overpayments).'</td>
                                                    <td width="18%" class="text-right"></td>
                                                </tr>
                                                <tr>
                                                    <td width="40%">';
                                                        $default_message='Contribution Refunds';
                                                        $this->languages_m->translate('contribution_refunds',$default_message);
                                                    echo '
                                                    </td>
                                                    <td width="18%" class="text-right">('.number_to_currency($total_group_contribution_refunds).')</td>
                                                    <td width="18%" class="text-right"></td>
                                                </tr>';
                                            }else{
                                                echo '
                                                <tr>
                                                    <td width="40%">';
                                                        $default_message='Member Contributions';
                                                        $this->languages_m->translate('member_contributions',$default_message);
                                                    echo '
                                                    </td>
                                                    <td width="18%" class="text-right">'.number_to_currency($total_contributions_payable).'</td>
                                                    <td width="18%" class="text-right"></td>
                                                </tr>
                                                <tr>
                                                    <td width="40%">';
                                                        $default_message='Contribution Overpayments';
                                                        $this->languages_m->translate('contribution_overpayments',$default_message);
                                                    echo '
                                                    </td>
                                                    <td width="18%" class="text-right">'.number_to_currency($total_contribution_overpayments).'</td>
                                                    <td width="18%" class="text-right"></td>
                                                </tr>';
                                            }
                                            echo '
                                            <tr>
                                                <td width="40%">';
                                                    $default_message='Member Fines';
                                                    $this->languages_m->translate('member_fines',$default_message);
                                                echo '
                                                </td>
                                                <td width="18%" class="text-right">'.number_to_currency($total_fines_payable).'</td>
                                                <td width="18%" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <td width="40%">';
                                                    $default_message='Net Profit/Loss';
                                                    $this->languages_m->translate('net_profit_or_loss',$default_message);
                                                echo '
                                                </td>
                                                <td width="18%" class="text-right">';
                                                    if($net_profit_or_loss<0){
                                                        echo "(".number_to_currency(abs($net_profit_or_loss)).")";
                                                    }else{
                                                        echo "".number_to_currency(abs($net_profit_or_loss))."";
                                                    }
                                                echo '</td>
                                                <td width="18%" class="text-right"></td>
                                            </tr>

                                            <tr>
                                                <td width="40%">';
                                                    $default_message='Total';
                                                    $this->languages_m->translate('total',$default_message);
                                                echo '
                                                </td>
                                                <td width="18%" class="text-right"></td>
                                                <td width="18%" class="text-right">'.number_to_currency($share_holders_equity_total).'</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr>
                                                <th colspan="4">';
                                                    $default_message='Long Term Investments';
                                                    $this->languages_m->translate('long_term_investments',$default_message);
                                                echo '
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                             <tr>
                                                <td width="40%">';
                                                    $default_message='Stocks';
                                                    $this->languages_m->translate('stocks',$default_message);
                                                echo '
                                                </td>
                                                <td width="18%" class="text-right">'.number_to_currency($total_stock_purchase_amount).'</td>
                                                <td width="18%" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <td width="40%">';
                                                    $default_message='Total';
                                                    $this->languages_m->translate('total',$default_message);
                                                echo '
                                                </td>
                                                <td width="18%" class="text-right"></td>
                                                <td width="18%" class="text-right">'.number_to_currency($long_term_investments_total).'</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td>
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr>
                                                <th colspan="4">';
                                                    $default_message='Current Liabilities';
                                                    $this->languages_m->translate('current_liabilities',$default_message);
                                                echo '
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td width="40%">';
                                                    $default_message='Total';
                                                    $this->languages_m->translate('total',$default_message);
                                                echo '
                                                </td>
                                                <td width="18%" class="text-right"></td>
                                                <td width="18%" class="text-right">'.number_to_currency($current_liabilities_total).'</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr>
                                                <th colspan="4">';
                                                    $default_message='Fixed Assets';
                                                    $this->languages_m->translate('fixed_assets',$default_message);
                                                echo '
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                             <tr>
                                                <td width="40%">';
                                                    $default_message='Fixed Assets';
                                                    $this->languages_m->translate('fixed_assets',$default_message);
                                                echo '
                                                </td>
                                                <td width="18%" class="text-right">'.number_to_currency($asset_purchase_total).'</td>
                                                <td width="18%" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <td width="40%">';
                                                    $default_message='Total';
                                                    $this->languages_m->translate('total',$default_message);
                                                echo '
                                                </td>
                                                <td width="18%" class="text-right"></td>
                                                <td width="18%" class="text-right">'.number_to_currency($fixed_assets_total).'</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td>
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr>
                                                <th colspan="4">';
                                                    $default_message='Long Term Liabilities';
                                                    $this->languages_m->translate('long_term_liabilities',$default_message);
                                                echo '
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td width="40%">';
                                                    $default_message='Total Bank Loan Balance';
                                                    $this->languages_m->translate('total_bank_loan_balance',$default_message);
                                                echo '
                                                </td>
                                                <td width="18%" class="text-right"></td>
                                                <td width="18%" class="text-right">'.number_to_currency($total_bank_loan_balance).'</td>
                                            </tr>
                                            <tr>
                                                <td width="40%">';
                                                    $default_message='Total';
                                                    $this->languages_m->translate('total',$default_message);
                                                echo '
                                                </td>
                                                <td width="18%" class="text-right"></td>
                                                <td width="18%" class="text-right">'.number_to_currency($long_term_liabilities_total).'</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr>
                                                <th>';
                                                    $default_message='Total';
                                                    $this->languages_m->translate('total',$default_message);
                                                echo '
                                                </th>
                                                <th class="text-right">
                                                '.number_to_currency($assets_total).'
                                                </th>
                                            </tr>
                                        </thead>
                                    </table>
                                </td>
                                <td>
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr>
                                                <th>';
                                                    $default_message='Total';
                                                    $this->languages_m->translate('total',$default_message);
                                                echo '
                                                </th>
                                                <th class="text-right">
                                                '.number_to_currency($liabilities_total).'
                                                </th>
                                            </tr>
                                        </thead>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            ';
    }


    function get_comprehensive_income_statement(){
        $from = strtotime($this->input->get_post('from'));
        $to = strtotime($this->input->get_post('to'));
        $total_revenue = 0;
        $total_miscellaneous_income = $this->deposits_m->get_group_member_total_miscellaneous_amount($this->group->id,$from,$to);
        $total_revenue += $total_miscellaneous_income;
        $total_income = $this->deposits_m->get_group_income_total_amounts($this->group->id,$from,$to);
        $total_revenue += $total_income;

        $non_refundable_contributions = $this->contributions_m->get_group_non_refundable_contributions();
        $group_total_contributions_per_contribution_array = $this->reports_m->get_group_total_contributions_paid_per_contribution_array();
        foreach($non_refundable_contributions as $contribution):
            $total_revenue += $group_total_contributions_per_contribution_array[$contribution->id];
        endforeach;
        $total_money_market_interest = $this->money_market_investments_m->get_group_total_money_market_interest($this->group->id,$from,$to);
        $total_revenue += $total_money_market_interest;
        $total_loan_interest_and_fines = $this->loan_invoices_m->get_group_total_loan_interest_payable($this->group->id,$from,$to);
        $total_loan_interest_and_fines += $this->loan_invoices_m->get_group_total_loan_fines_payable($this->group->id,$from,$to);
        $total_revenue += $total_loan_interest_and_fines;
        $total_loan_processing_income = $this->deposits_m->get_group_total_loan_processing_income($this->group->id,$from,$to);
        $total_revenue += $total_loan_processing_income;


        $total_group_fines = $this->deposits_m->get_group_total_fines($this->group->id,$from,$to);
        $total_group_contributions_transfers_to_fines = $this->statements_m->get_group_total_contribution_transfers_to_fines($this->group->id,$from,$to);
        $total_fines_paid = $total_group_fines+$total_group_contributions_transfers_to_fines;
        $total_revenue += $total_fines_paid;
        $total_group_expenses = 0;
        $total_expenses = $this->withdrawals_m->get_group_total_expenses($this->group->id,$from,$to);
        $total_group_expenses += $total_expenses;
        $total_bank_loan_interest = $this->bank_loans_m->get_group_bank_loans_interest($this->group->id,$from,$to);
        $total_group_expenses += $total_bank_loan_interest;

        $expense_category_options = $this->expense_categories_m->get_group_expense_category_options();
        $group_expense_category_totals = $this->withdrawals_m->get_group_expense_category_totals_array($this->group->id,$from,$to);
        //print_r($group_total_contributions_per_contribution_array);
        //die;
        $html='
            <div class="report invoice-content-2 bordered document-border">
                <div class="row invoice-head">
                    <div class="col-md-7 col-xs-6">
                        <div class="invoice-logo">
                            <img src="';
                                $html.=is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                                $html.='" alt="" class="group-logo image-responsive" /> 
                        </div>
                    </div>
                    <div class="col-md-5 col-xs-6 text-right">
                        <div class="company-address">
                            <span class="bold uppercase">'.$this->group->name.'</span><br/>';
                            $html.=nl2br($this->group->address);
                            $html.='<br/>
                            <span class="bold">Telephone: </span> '.$this->group->phone.'
                            <br/>
                            <span class="bold">E-mail Address: </span> '.$this->group->email.'
                            <br/>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-center margin-top-10 margin-bottom-10">
                        '.ucfirst($this->group->name).'<br/>
                        Statement of Comprehensive Income statement from '.timestamp_to_report_time($from).' to '.timestamp_to_report_time($to).'
                    </div>
                </div>
                <hr/>
                <div class="row invoice-body">
                    <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                        <thead>
                            <tr>
                                <th width="50%">
                                    Income
                                </th>
                                <th width="25%"></th>
                                <th width="25%"></th>
                            </tr>
                        </thead>
                        <tbody>';
                            $html.='
                            <tr class="listing">
                                <td><strong>Interest Income</strong></td>
                                <td class="text-right"></td>
                                <td></td>
                            </tr>';
                            $html.='
                            <tr class="listing">
                                <td>Interest from Sacco loans disbursed</td>
                                <td class="text-right">'.$this->group_currency.'  '.number_to_currency($total_loan_interest_and_fines).'</td>
                                <td></td>
                            </tr>';

                            $html.='
                            <tr class="listing">
                                <td>Interest from Fixed deposits</td>
                                <td class="text-right">'.$this->group_currency.'  '.number_to_currency($total_money_market_interest).'</td>
                                <td></td>
                            </tr>';

                            $html.='
                            <tr class="listing">
                                <td><strong>Other Operating Income</strong></td>
                                <td class="text-right"></td>
                                <td></td>
                            </tr>';

                            $html.='
                            <tr class="listing">
                                <td>Loan Charges</td>
                                <td class="text-right">'.$this->group_currency.'  '.number_to_currency($total_loan_processing_income).'</td>
                                <td></td>
                            </tr>';

                            $html.='
                            <tr class="listing">
                                <td><strong>Other Fees</strong></td>
                                <td class="text-right"></td>
                                <td></td>
                            </tr>';

                            foreach($non_refundable_contributions as $contribution):
                                $html.='
                                <tr class="listing">
                                    <td>'.$contribution->name.'</td>
                                    <td class="text-right">'.number_to_currency($group_total_contributions_per_contribution_array[$contribution->id]).'</td>
                                    <td></td>
                                </tr>';
                            endforeach;

                            $html.='
                            <tr class="listing">
                                <td><strong>Other Income</strong></td>
                                <td class="text-right"></td>
                                <td></td>
                            </tr>';
                            $html.='
                            <tr class="listing">
                                <td>Miscellaneous Income</td>
                                <td class="text-right">'.$this->group_currency.'  '.number_to_currency($total_miscellaneous_income).'</td>
                                <td></td>
                            </tr>';

                            $html.='
                            <tr class="listing">
                                <td>Revenue/Income</td>
                                <td class="text-right">'.$this->group_currency.'  '.number_to_currency($total_income).'</td>
                                <td></td>
                            </tr>';
                            $html.='
                            <tr class="listing">
                                <td>Fines Paid</td>
                                <td class="text-right">'.$this->group_currency.'  '.number_to_currency($total_fines_paid).'</td>
                                <td></td>
                            </tr>';

                        $html.='
                        </tbody>
                        <tbody class="total">
                            <tr class="sub-total">
                                <td colspan="2" class="text-center">Gross Income</td>
                                <td class="text-right">';
                                    $html.=$this->group_currency.' '. number_to_currency($total_revenue);
                                $html.='
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th>Expenditure</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>';
                            foreach($group_expense_category_totals as $expense_category_id => $group_expense_category_total): 
                                $html.=
                                '<tr class="listing">
                                    <td>'.$expense_category_options[$expense_category_id].'</td>
                                    <td class="text-right">'.$this->group_currency.'   '. number_to_currency($group_expense_category_total).'</td>
                                    <td></td>
                                </tr>';
                            endforeach;
                            $html.=
                            '<tr class="listing">
                                <td>Total Bank Loan Interest Paid</td>
                                <td class="text-right">'.$this->group_currency.'   '. number_to_currency($total_bank_loan_interest).'</td>
                                <td></td>
                            </tr>';
                        $html.=
                        '
                        </tbody>';
                        $html.='
                        <tbody class="total">
                            <tr class="sub-total">
                                <td colspan="2" class="text-center">Total Expenses</td>
                                <td class="text-right">';
                                     $html.=$this->group_currency.'   '. number_to_currency($total_group_expenses);
                            $html.=
                                '</td>
                            </tr>
                        </tbody>
                        <tfoot>';
                            $net_profit_or_loss = $total_revenue-$total_group_expenses;
                            $html.='
                            <tr>
                                <td></td>
                                <td>';
                                    $html.='<span class=""> Net Surplus for the period c/c to retained earnings</span>';
                                    
                                $html.='
                                </td>
                                <td class="text-right">';
                                    if($net_profit_or_loss>=0){
                                        $html.='<span class="green">'.$this->group_currency.'  '.number_to_currency(abs($net_profit_or_loss)).'</span>';
                                    }else{
                                        $html.='<span class="red">'.$this->group_currency.'  '.number_to_currency(abs($net_profit_or_loss)).'</span>';
                                    }
                                $html.='
                                </td>
                            </tr>
                        </tfoot>
                    </table>';
                $html.='
                <hr/>
                <div class="row">
                    <div class="col-md-12 margin-top-10 margin-bottom-10 text-center">
                        <span class="sbold">Powered by</span><br/>
                         <div class="invoice-logo-footer">
                            <img src="';
                            $html.=site_url("uploads/logos/".$this->application_settings->paper_footer_logo);
                            $html.='" alt="" class="report-group-logo-footer image-responsive" /> 
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-xs-12">
                        <a class="btn btn-sm blue hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class="fa fa-print"></i> Print</a>
                    </div>
                </div>
            </div>
        ';

        echo $html;
    }

    function get_loan_guarantor_summary(){
        $loan_guarantors = $this->loans_m->get_group_member_loan_guarantors();
        $interest_types = $this->loan->interest_types;
        $loan_interest_rate_per = $this->loan->loan_interest_rate_per;

        if(empty($loan_guarantors)){

        }else{
            echo "<h5>Loan Guarantors</h5>";
            echo '
                <hr/>
                <div class="table-responsive table-scrollable">
                    <table class="table table-condensed table-striped table-hover table-header-fixed table-searchable">
                        <thead>
                            <tr>
                                <th width="8px">#</th>
                                <th>Member Name</th>
                                <th>Member Guaranteed</th>
                                <th class="text-right">Amount Guaranteed ('.$this->group_currency.')</th>
                                <th class="text-right">Loan Description</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>';
                        $i = 1;
                        foreach($loan_guarantors as $loan_guarantor):
                            echo "
                            <tr>
                                <td>".($i++).".</td>
                                <td>".$this->group_member_options[$loan_guarantor->member_id].".</td>
                                <td>".$this->group_member_options[$loan_guarantor->guaranteed_member_id].".</td>
                                <td class='text-right'>".number_to_currency($loan_guarantor->guaranteed_amount).".</td>
                                <td class='text-right'>";

                                echo 
                                "Loan disbursed of ".$this->group_currency." ".number_to_currency($loan_guarantor->loan_amount)." on ".timestamp_to_date($loan_guarantor->disbursement_date)." to ".
                                    $this->group_member_options[$loan_guarantor->guaranteed_member_id]." at ".$loan_guarantor->interest_rate." % per ".$loan_interest_rate_per[$loan_guarantor->loan_interest_rate_per]." for ".$loan_guarantor->repayment_period." months on ".$interest_types[$loan_guarantor->interest_type];

                                echo "
                                </td>
                                <td class=''>";
                                    echo 
                                    '<a data-toggle="" data-content="#" data-title="" data-id="" id=""  href="'.site_url('group/loans/loan_statement/'.$loan_guarantor->loan_id).'" class="btn btn-xs   blue">
                                        <i class="fa fa-eye"></i> View Loan Statement &nbsp;&nbsp; 
                                    </a>';
                                echo "</td>
                            </tr>
                            ";
                        endforeach;
                        echo '
                        </tbody>
                    </table>
                </div>
            ';
        }
    }

    function get_eazzyclub_income_statement(){

        $from = strtotime($this->input->get_post('from'));
        $to = strtotime($this->input->get_post('to'));

        $non_refundable_contributions = $this->contributions_m->get_group_non_equitable_non_refundable_contributions();
        $administrative_expense_categories = $this->expense_categories_m->get_group_administrative_expense_category_options();
        $other_expense_categories = $this->expense_categories_m->get_group_other_expense_category_options();

        $years_array = array();
        if($from && $to){
            $from_year = date('Y',$from);
            $to_year = date('Y',$to);
            for($i = $from_year; $i <= $to_year; $i++):
                $years_array[] = $i;
            endfor;
        }else{
            $from_year = date('Y',strtotime('-1 year'));
            $to_year = date('Y');
            $years_array = array(
                $from_year,
                $to_year,
            );
        }

        $total_loan_interest_paid_per_year_array = $this->reports_m->get_group_total_loan_interest_paid_per_year_array($this->group->id);

        $total_money_market_interest_per_year_array = $this->money_market_investments_m->get_group_total_money_market_interest_per_year_array($this->group->id);
        
        $total_bank_loans_interest_paid_per_year_array = $this->withdrawals_m->get_group_total_bank_loans_interest_paid_per_year_array();

        $total_contributions_paid_per_contribution_per_year_array = $this->reports_m->get_group_total_contributions_paid_per_contribution_per_year_array();

        $expense_category_totals_per_year_array = $this->withdrawals_m->get_group_expense_category_totals_per_year_array();

        $total_loan_processing_income_per_year_array = $this->deposits_m->get_group_total_loan_processing_income_per_year_array();

        $total_income_per_year_array = $this->deposits_m->get_group_total_income_per_year_array();

        $total_miscellaneous_income_per_year_array = $this->deposits_m->get_group_total_miscellaneous_income_per_year_array();

        $total_fines_per_year_array = $this->reports_m->get_group_total_fines_per_year_array($this->group->id);

        $total_year_interest_array = array();
        $net_year_interest_income_array = array();
        $net_year_income_array = array();
        $total_year_income_array = array();
        $year_surplus_before_tax_array = array();
        $year_surplus_after_tax_array = array();
        $net_surplus_for_the_year_array = array();
        $total_non_refundable_contributions_paid_per_year_array = array();
        $other_operating_income_per_year_array = array();
        $loan_processing_income_per_year_array = array();
        $group_income_per_year_array = array();
        $group_expenses_per_year_array = array();
        $transfer_to_statutory_reserve_per_year_array = array();
        $retained_earnings_per_year_array = array();

        foreach($years_array as $year):
            $total_year_interest_array[$year] = 0;
            $net_year_interest_income_array[$year] = 0;
            $net_year_income_array[$year] = 0;
            $total_year_income_array[$year] = 0;
            $year_surplus_before_tax_array[$year] = 0;
            $year_surplus_after_tax_array[$year] = 0;
            $net_surplus_for_the_year_array[$year] = 0;
            $total_non_refundable_contributions_paid_per_year_array[$year] = 0;
            $other_operating_income_per_year_array[$year] = 0;
            $loan_processing_income_per_year_array[$year] = 0;
            $group_income_per_year_array[$year] = 0;
            $group_expenses_per_year_array[$year] = 0;
            $transfer_to_statutory_reserve_per_year_array[$year] = 0;
            $retained_earnings_per_year_array[$year] = 0;
        endforeach;

        foreach($non_refundable_contributions as $contribution):
            foreach($years_array as $year):
                $total_non_refundable_contributions_paid_per_year_array[$year] += $total_contributions_paid_per_contribution_per_year_array[$contribution->id][$year];
            endforeach;
        endforeach;

        foreach($administrative_expense_categories as $administrative_expense_category_id =>  $administrative_expense_category_name):
            foreach($years_array as $year):
                $group_expenses_per_year_array[$year] += (isset($expense_category_totals_per_year_array[$administrative_expense_category_id][$year])?$expense_category_totals_per_year_array[$administrative_expense_category_id][$year]:0);
            endforeach;
        endforeach;

        foreach($other_expense_categories as $other_expense_category_id =>  $other_expense_category_name):
            foreach($years_array as $year):
                $group_expenses_per_year_array[$year] += (isset($expense_category_totals_per_year_array[$other_expense_category_id][$year])?$expense_category_totals_per_year_array[$other_expense_category_id][$year]:0);
            endforeach;
        endforeach;
        
        foreach($years_array as $year):

            $total_year_interest_array[$year] += (isset($total_loan_interest_paid_per_year_array[$year])?$total_loan_interest_paid_per_year_array[$year]:0);
            $total_year_interest_array[$year] += (isset($total_money_market_interest_per_year_array[$year])?$total_money_market_interest_per_year_array[$year]:0);

            $net_year_interest_income_array[$year] += $total_year_interest_array[$year];

            $net_year_interest_income_array[$year] -= (isset($total_bank_loans_interest_paid_per_year_array[$year])?($total_bank_loans_interest_paid_per_year_array[$year]):0);

            $other_operating_income_per_year_array[$year] += $total_non_refundable_contributions_paid_per_year_array[$year];

            $other_operating_income_per_year_array[$year] += (isset($total_loan_processing_income_per_year_array[$year])?$total_loan_processing_income_per_year_array[$year]:0);

            $other_operating_income_per_year_array[$year] += (isset($total_income_per_year_array[$year])?$total_income_per_year_array[$year]:0);

            $other_operating_income_per_year_array[$year] += (isset($total_miscellaneous_income_per_year_array[$year])?$total_miscellaneous_income_per_year_array[$year]:0);

            $other_operating_income_per_year_array[$year] += (isset($total_fines_per_year_array[$year])?$total_fines_per_year_array[$year]:0);

            $year_surplus_before_tax_array[$year] += $net_year_interest_income_array[$year] + $other_operating_income_per_year_array[$year] - $group_expenses_per_year_array[$year];

            $year_surplus_after_tax_array[$year] = $year_surplus_before_tax_array[$year];

            $transfer_to_statutory_reserve_per_year_array[$year] = (($year_surplus_after_tax_array[$year] < 0)?0:($year_surplus_after_tax_array[$year] * 0.21));

            $retained_earnings_per_year_array[$year] = $year_surplus_before_tax_array[$year]-$transfer_to_statutory_reserve_per_year_array[$year];

        endforeach;

        // print_r($total_income_per_year_array);
        // print_r($total_fines_per_year_array);
        // die;
        //number_to_currency((isset($total_income_per_year_array[$year])?($total_income_per_year_array[$year]):(0)) + (isset($total_miscellaneous_income_per_year_array[$year])?($total_miscellaneous_income_per_year_array[$year]):(0))+ (isset($total_fines_per_year_array[$year])?($total_fines_per_year_array[$year]):(0)))


        $html='
            <div class="report invoice-content-2 bordered document-border">
                <div class="row invoice-head">
                    <div class="col-md-7 col-xs-6">
                        <div class="invoice-logo">
                            <img src="';
                                $html.=is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                                $html.='" alt="" class="group-logo image-responsive" /> 
                        </div>
                    </div>
                    <div class="col-md-5 col-xs-6 text-right">
                        <div class="company-address">
                            <span class="bold uppercase">'.$this->group->name.'</span><br/>';
                            $html.=nl2br($this->group->address);
                            $html.='<br/>
                            <span class="bold">Telephone: </span> '.$this->group->phone.'
                            <br/>
                            <span class="bold">E-mail Address: </span> '.$this->group->email.'
                            <br/>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-center margin-top-10 margin-bottom-10">
                        '.ucfirst($this->group->name).'<br/>
                        Statement of Comprehensive Income statement for '.$from_year.' to '.$to_year.'
                    </div>
                </div>
                <hr/>
                <div class="row invoice-body">
                    <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                        <thead>
                            <tr>
                                <th width="30%"></th>';
                                $width = round(70/count($years_array));
                                foreach($years_array as $year):
                                    $html .='
                                    <th class="text-right" width="'.$width.'%">'.$year.'</th>';
                                endforeach;
                            $html .='
                            </tr>
                        </thead>';
                        $html.= '
                        <tbody>
                            <tr>
                                <td><strong>Income</strong></td>';
                                foreach($years_array as $year):
                                    $html .='
                                    <td class="text-right"></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr>
                                <td><strong>Interest income</strong></td>';
                                foreach($years_array as $year):
                                    $html .='
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Interest on loans</small></td>
                                ';
                                foreach($years_array as $year):
                                    $html .= '
                                        <td class="text-right"><small>'.(isset($total_loan_interest_paid_per_year_array[$year])?number_to_currency($total_loan_interest_paid_per_year_array[$year]):number_to_currency(0)).'</small></td>
                                    ';
                                endforeach;
                            $html .='
                            </tr>

                            <tr class="listing">
                                <td><small>Interest on other investments</small></td>';
                                foreach($years_array as $year):
                                    $html .= '
                                        <td class="text-right"><small>'.(isset($total_money_market_interest_per_year_array[$year])?number_to_currency($total_money_market_interest_per_year_array[$year]):number_to_currency(0)).'</small></td>
                                    ';
                                endforeach;
                                
                            $html .='
                            </tr>
                            <tr>
                                <td><strong>Total Interest Income</strong></td>';
                                foreach($years_array as $year):
                                    $html .= '     
                                        <td class="text-right">'.number_to_currency($total_year_interest_array[$year]).'</td>
                                    ';
                                endforeach;
                            $html .= '
                            </tr>
                            <tr>
                                <td></td>';
                                foreach($years_array as $year):
                                    $html .='
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .='
                            </tr>
                            <tr>
                                <td><strong>Interest Expense</strong></td>';
                                foreach($years_array as $year):
                                    $html .='
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Interest on deposits</small></td>';
                                foreach($years_array as $year):
                                    $html .='
                                    <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                endforeach;
                                $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Interest on bank loans</small></td>';
                                foreach($years_array as $year):
                                    $html .='
                                    <td class="text-right"><small>'.(isset($total_bank_loans_interest_paid_per_year_array[$year])?number_to_currency($total_bank_loans_interest_paid_per_year_array[$year]):number_to_currency(0)).'</small></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            <tr>
                                <td><strong>Net Interest Income</strong></td>';
                                foreach($years_array as $year):
                                    $html .='
                                    <td class="text-right">'.number_to_currency($net_year_interest_income_array[$year]).'</td>
                                    ';
                                endforeach;
                            $html.='
                            </tr>
                            <tr>
                                <td></td>';
                                foreach($years_array as $year):
                                    $html .='
                                    <td class="text-right"></td>
                                    ';
                                endforeach;
                            $html .= '
                            </tr>
                            <tr>
                                <td><strong>Other Operating Income</strong></td>';
                                foreach($years_array as $year):
                                    $html .='
                                    <td class="text-right"></td>
                                    ';
                                endforeach;
                            $html .= '
                            </tr>';
                            foreach($non_refundable_contributions as $contribution):
                                $html.='
                                <tr class="listing">
                                    <td><small>'.$contribution->name.'</small></td>';
                                    foreach($years_array as $year):
                                        $html .= '
                                            <td class="text-right"><small>'.number_to_currency($total_contributions_paid_per_contribution_per_year_array[$contribution->id][$year]).'</small></td>
                                        ';
                                    endforeach;
                            endforeach;
                            $html.='
                            <tr class="listing">
                                <td><small>Loan Charges</small></td>';
                                foreach($years_array as $year):
                                    $html .= '
                                        <td class="text-right"><small>'.(isset($total_loan_processing_income_per_year_array[$year])?number_to_currency($total_loan_processing_income_per_year_array[$year]):number_to_currency(0)).'</small></td>
                                    ';
                                endforeach;
                            $html .= '
                            </tr>';
                            $html.='
                            <tr class="listing">
                                <td><small>Other</small></td>';
                                foreach($years_array as $year):
                                    $html .= '
                                        <td class="text-right"><small>'.number_to_currency((isset($total_income_per_year_array[$year])?($total_income_per_year_array[$year]):(0)) + (isset($total_miscellaneous_income_per_year_array[$year])?($total_miscellaneous_income_per_year_array[$year]):(0))+ (isset($total_fines_per_year_array[$year])?($total_fines_per_year_array[$year]):(0))).'</small></td>
                                    ';


                                endforeach;
                            $html.='
                            </tr>';

                            $html .='
                            <tr>
                                <td></td>';
                                foreach($years_array as $year):
                                    $html .= '
                                        <td class="text-right">'.(($other_operating_income_per_year_array[$year] < 0)?'('.number_to_currency(abs($other_operating_income_per_year_array[$year])).")":number_to_currency($other_operating_income_per_year_array[$year])).'</td>';
                                endforeach;
                            $html .= '
                            </tr>
                            <tr>
                                <td></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            <tr>
                                <td><strong>Expenditure</strong></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            <tr>
                                <td>Administrative Expenses</td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            ';

                            foreach($administrative_expense_categories as $administrative_expense_category_id =>  $administrative_expense_category_name):
                                $html.='
                                <tr class="listing">
                                    <td><small>'.$administrative_expense_category_name.'</small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(isset($expense_category_totals_per_year_array[$administrative_expense_category_id][$year])?$expense_category_totals_per_year_array[$administrative_expense_category_id][$year]:0).'</small></td>';
                                    endforeach;
                                $html .= '
                                </tr>';
                            endforeach;

                            $html .='
                            <tr>
                                <td>Other Operating Expenses</td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            ';

                            foreach($other_expense_categories as $other_expense_category_id =>  $other_expense_category_name):
                                $html.='
                                <tr class="listing">
                                    <td><small>'.$other_expense_category_name.'</small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(isset($expense_category_totals_per_year_array[$other_expense_category_id][$year])?$expense_category_totals_per_year_array[$other_expense_category_id][$year]:0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>';
                            endforeach;

                            $html .='
                            <tr>
                                <td></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            <tr>
                                <td><strong>Net Operating Surplus/ Deficit for the year before tax</strong></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right">'.(($year_surplus_before_tax_array[$year] < 0)?'('.number_to_currency(abs($year_surplus_before_tax_array[$year])).")":number_to_currency($year_surplus_before_tax_array[$year]))
                                .'</td>';
                                endforeach;
                            $html .='
                            </tr>';

                            $html .='
                            <tr>
                                <td></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr>
                                <td><strong>Net Operating Surplus/ Deficit for the year after tax</strong></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right">'.(($year_surplus_after_tax_array[$year] < 0)?'('.number_to_currency(abs($year_surplus_after_tax_array[$year])).")":number_to_currency($year_surplus_after_tax_array[$year]))
                                .'</td>';
                                endforeach;
                            $html .= '
                            </tr>
                            <tr>
                                <td></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                            $html .='
                            </tr>';

                            $html .='
                            <tr>
                                <td><strong>Transfer to statutory Reserve 21%</strong></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right">'.number_to_currency($transfer_to_statutory_reserve_per_year_array[$year]).'</td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr>
                                <td></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                            $html .='
                            </tr>';

                            $html .='
                            <tr>
                                <td><strong>Proposed Dividends</strong></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right">'.number_to_currency(0).'</td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr>
                                <td></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                            $html .='
                            </tr>';

                            $html .='
                            <tr>
                                <td><strong>Net Surplus for the year c/d to retaining earnings</strong></td>';

                                foreach($years_array as $year):
                                    $html .= '
                                        <td class="text-right">'.(($retained_earnings_per_year_array[$year] < 0)?'('.number_to_currency(abs($retained_earnings_per_year_array[$year])).")":number_to_currency($retained_earnings_per_year_array[$year])).'</td>
                                    ';
                                endforeach;
                            $html .= '
                            </tr>
                            ';
                        $html .= '
                        </tbody>
                        ';
                    $html .='
                    </table>';
                $html.='
                <hr/>
                <div class="row">
                    <div class="col-md-12 margin-top-10 margin-bottom-10 text-center">
                        <span class="sbold">Powered by</span><br/>
                         <div class="invoice-logo-footer">
                            <img src="';
                            $html.=site_url("uploads/logos/".$this->application_settings->paper_footer_logo);
                            $html.='" alt="" class="report-group-logo-footer image-responsive" /> 
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-xs-12">
                        <a class="btn btn-sm blue hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class="fa fa-print"></i> Print</a>
                    </div>
                </div>
            </div>
        ';

        echo $html;
    }

    function get_monthly_income_statement(){

        error_reporting(-1);
        ini_set('display_errors', 1);
        $from_month = strtotime($this->input->get_post('from'));
        $to_month = strtotime($this->input->get_post('to'));

        $non_refundable_contributions = $this->contributions_m->get_group_non_equitable_non_refundable_contributions();
        $administrative_expense_categories = $this->expense_categories_m->get_group_administrative_expense_category_options();
        $other_expense_categories = $this->expense_categories_m->get_group_other_expense_category_options();

        $months_array = array();
        if($from_month && $to_month){
            $months_array = generate_months_array($from_month,$to_month);
        }else{
            $from_month = strtotime('-3 months');
            $to_month = strtotime('today');
            $months_array = generate_months_array($from_month,$to_month);
        }

        $income_category_options = $this->income_categories_m->get_group_income_category_options();

        $total_loan_interest_paid_per_month_array = $this->reports_m->get_group_total_loan_interest_paid_per_month_array($this->group->id);

        $total_money_market_interest_per_month_array = $this->money_market_investments_m->get_group_total_money_market_interest_per_month_array($this->group->id);

        $total_dividends_per_month_array = $this->withdrawals_m->get_group_total_dividends_per_month_array($this->group->id);

        $total_stocks_sale_income_per_month_array = $this->stocks_m->get_group_total_stocks_sale_income_per_month_array($this->group->id);

        $total_stocks_sale_losses_per_month_array = $this->stocks_m->get_group_total_stocks_sale_losses_per_month_array($this->group->id);

        $total_bank_loans_interest_paid_per_month_array = $this->withdrawals_m->get_group_total_bank_loans_interest_paid_per_month_array();

        $total_contributions_paid_per_contribution_per_month_array = $this->reports_m->get_group_total_contributions_paid_per_contribution_per_month_array($this->group->id,$months_array);

        $expense_category_totals_per_month_array = $this->withdrawals_m->get_group_expense_category_totals_per_month_array();

        $total_loan_processing_income_per_month_array = $this->deposits_m->get_group_total_loan_processing_income_per_month_array();

        $total_income_per_month_array = $this->deposits_m->get_group_total_income_per_month_array();

        $total_income_per_month_per_income_category_array = $this->deposits_m->get_group_total_income_per_month_per_income_category_array();

        $total_miscellaneous_income_per_month_array = $this->deposits_m->get_group_total_miscellaneous_income_per_month_array();

        $total_fines_per_month_array = $this->reports_m->get_group_total_fines_per_month_array($this->group->id);

        $total_month_interest_array = array();
        $net_month_interest_income_array = array();
        $net_month_income_array = array();
        $total_month_income_array = array();
        $month_surplus_before_tax_array = array();
        $month_surplus_after_tax_array = array();
        $net_surplus_for_the_month_array = array();
        $total_non_refundable_contributions_paid_per_month_array = array();
        $other_operating_income_per_month_array = array();
        $loan_processing_income_per_month_array = array();
        $group_income_per_month_array = array();
        $group_expenses_per_month_array = array();
        $transfer_to_statutory_reserve_per_month_array = array();
        $retained_earnings_per_month_array = array();

        foreach($months_array as $month):
            $total_month_interest_array[$month] = 0;
            $net_month_interest_income_array[$month] = 0;
            $net_month_income_array[$month] = 0;
            $total_month_income_array[$month] = 0;
            $month_surplus_before_tax_array[$month] = 0;
            $month_surplus_after_tax_array[$month] = 0;
            $net_surplus_for_the_month_array[$month] = 0;
            $total_non_refundable_contributions_paid_per_month_array[$month] = 0;
            $other_operating_income_per_month_array[$month] = 0;
            $loan_processing_income_per_month_array[$month] = 0;
            $group_income_per_month_array[$month] = 0;
            $group_expenses_per_month_array[$month] = 0;
            $transfer_to_statutory_reserve_per_month_array[$month] = 0;
            $retained_earnings_per_month_array[$month] = 0;
        endforeach;

        foreach($non_refundable_contributions as $contribution):
            foreach($months_array as $month):
                $total_non_refundable_contributions_paid_per_month_array[$month] += $total_contributions_paid_per_contribution_per_month_array[$contribution->id][$month];
            endforeach;
        endforeach;

        foreach($administrative_expense_categories as $administrative_expense_category_id =>  $administrative_expense_category_name):
            foreach($months_array as $month):
                $group_expenses_per_month_array[$month] += (isset($expense_category_totals_per_month_array[$administrative_expense_category_id][$month])?$expense_category_totals_per_month_array[$administrative_expense_category_id][$month]:0);
            endforeach;
        endforeach;

        foreach($other_expense_categories as $other_expense_category_id =>  $other_expense_category_name):
            foreach($months_array as $month):
                $group_expenses_per_month_array[$month] += (isset($expense_category_totals_per_month_array[$other_expense_category_id][$month])?$expense_category_totals_per_month_array[$other_expense_category_id][$month]:0);
            endforeach;
        endforeach;
        
        foreach($months_array as $month):

            $total_month_interest_array[$month] += (isset($total_loan_interest_paid_per_month_array[$month])?$total_loan_interest_paid_per_month_array[$month]:0);

            $total_month_interest_array[$month] += (isset($total_money_market_interest_per_month_array[$month])?$total_money_market_interest_per_month_array[$month]:0);

            $total_month_interest_array[$month] += (isset($total_stocks_sale_income_per_month_array[$month])?$total_stocks_sale_income_per_month_array[$month]:0);

            $net_month_interest_income_array[$month] += $total_month_interest_array[$month];

            $net_month_interest_income_array[$month] -= (isset($total_bank_loans_interest_paid_per_month_array[$month])?($total_bank_loans_interest_paid_per_month_array[$month]):0);

            $net_month_interest_income_array[$month] -= (isset($total_stocks_sale_losses_per_month_array[$month])?($total_stocks_sale_losses_per_month_array[$month]):0);

            $other_operating_income_per_month_array[$month] += $total_non_refundable_contributions_paid_per_month_array[$month];

            $other_operating_income_per_month_array[$month] += (isset($total_loan_processing_income_per_month_array[$month])?$total_loan_processing_income_per_month_array[$month]:0);

            $other_operating_income_per_month_array[$month] += (isset($total_income_per_month_array[$month])?$total_income_per_month_array[$month]:0);

            $other_operating_income_per_month_array[$month] += (isset($total_miscellaneous_income_per_month_array[$month])?$total_miscellaneous_income_per_month_array[$month]:0);

            $other_operating_income_per_month_array[$month] += (isset($total_fines_per_month_array[$month])?$total_fines_per_month_array[$month]:0);

            $month_surplus_before_tax_array[$month] += $net_month_interest_income_array[$month] + $other_operating_income_per_month_array[$month] - $group_expenses_per_month_array[$month];

            $month_surplus_after_tax_array[$month] = $month_surplus_before_tax_array[$month];

            $transfer_to_statutory_reserve_per_month_array[$month] = (($month_surplus_after_tax_array[$month] < 0)?0:($month_surplus_after_tax_array[$month] * 0.0));

            $retained_earnings_per_month_array[$month] = $month_surplus_before_tax_array[$month]-$transfer_to_statutory_reserve_per_month_array[$month];
        endforeach;

        $html='
            <div class="report invoice-content-2 bordered document-border">
                <div class="row invoice-head">
                    <div class="col-md-7 col-xs-6">
                        <div class="invoice-logo">
                            <img src="';
                                $html.=is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                                $html.='" alt="" class="group-logo image-responsive" /> 
                        </div>
                    </div>
                    <div class="col-md-5 col-xs-6 text-right">
                        <div class="company-address">
                            <span class="bold uppercase">'.$this->group->name.'</span><br/>';
                            $html.=nl2br($this->group->address);
                            $html.='<br/>
                            <span class="bold">Telephone: </span> '.$this->group->phone.'
                            <br/>
                            <span class="bold">E-mail Address: </span> '.$this->group->email.'
                            <br/>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-center margin-top-10 margin-bottom-10">
                        '.ucfirst($this->group->name).'<br/>
                        Statement of Comprehensive Income statement for '.date('M Y',$from_month).' to '.date('M Y',$to_month).'
                    </div>
                </div>
                <hr/>
                <div class="row invoice-body">
                    <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement">
                        <thead>
                            <tr>
                                <th width="30%"></th>';
                                $width = round(70/count($months_array));
                                foreach($months_array as $month):
                                    $html .='
                                    <th class="text-right" width="'.$width.'%">'.$month.'</th>';
                                endforeach;
                            $html .='
                            </tr>
                        </thead>';
                        $html.= '
                        <tbody>
                            <tr>
                                <td><strong>Income</strong></td>';
                                foreach($months_array as $month):
                                    $html .='
                                    <td class="text-right"></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr>
                                <td><strong>Interest income</strong></td>';
                                foreach($months_array as $month):
                                    $html .='
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Interest on loans</small></td>
                                ';
                                foreach($months_array as $month):
                                    $html .= '
                                        <td class="text-right"><small>'.(isset($total_loan_interest_paid_per_month_array[$month])?number_to_currency($total_loan_interest_paid_per_month_array[$month]):number_to_currency(0)).'</small></td>
                                    ';
                                endforeach;
                            $html .='
                            </tr>

                            <tr class="listing">
                                <td><small>Interest money market investments</small></td>';
                                foreach($months_array as $month):
                                    $html .= '
                                        <td class="text-right"><small>'.(isset($total_money_market_interest_per_month_array[$month])?number_to_currency($total_money_market_interest_per_month_array[$month]):number_to_currency(0)).'</small></td>
                                    ';
                                endforeach;
                                
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Income from stock sales</small></td>';
                                foreach($months_array as $month):
                                    $html .= '
                                        <td class="text-right"><small>'.(isset($total_stocks_sale_income_per_month_array[$month])?number_to_currency($total_stocks_sale_income_per_month_array[$month]):number_to_currency(0)).'</small></td>
                                    ';
                                endforeach;
                                
                            $html .='
                            </tr>
                            <tr>
                                <td><strong>Total Interest Income</strong></td>';
                                foreach($months_array as $month):
                                    $html .= '     
                                        <td class="text-right">'.number_to_currency($total_month_interest_array[$month]).'</td>
                                    ';
                                endforeach;
                            $html .= '
                            </tr>
                            <tr>
                                <td></td>';
                                foreach($months_array as $month):
                                    $html .='
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .='
                            </tr>
                            <tr>
                                <td><strong>Interest Expense</strong></td>';
                                foreach($months_array as $month):
                                    $html .='
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Interest on deposits</small></td>';
                                foreach($months_array as $month):
                                    $html .='
                                    <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                endforeach;
                                $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Interest on bank loans</small></td>';
                                foreach($months_array as $month):
                                    $html .='
                                    <td class="text-right"><small>'.(isset($total_bank_loans_interest_paid_per_month_array[$month])?number_to_currency($total_bank_loans_interest_paid_per_month_array[$month]):number_to_currency(0)).'</small></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            <tr class="listing">
                                <td><small>Depreciation of stocks</small></td>';
                                foreach($months_array as $month):
                                    $html .='
                                    <td class="text-right"><small>'.(isset($total_stocks_sale_losses_per_month_array[$month])?number_to_currency($total_stocks_sale_losses_per_month_array[$month]):number_to_currency(0)).'</small></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            <tr>
                                <td><strong>Net Interest Income</strong></td>';
                                foreach($months_array as $month):
                                    $html .='
                                    <td class="text-right">'.(($net_month_interest_income_array[$month]<0)?"(".number_to_currency(abs($net_month_interest_income_array[$month])).")":number_to_currency($net_month_interest_income_array[$month])).'</td>
                                    ';
                                endforeach;
                            $html.='
                            </tr>
                            <tr>
                                <td></td>';
                                foreach($months_array as $month):
                                    $html .='
                                    <td class="text-right"></td>
                                    ';
                                endforeach;
                            $html .= '
                            </tr>
                            <tr>
                                <td><strong>Other Operating Income</strong></td>';
                                foreach($months_array as $month):
                                    $html .='
                                    <td class="text-right"></td>
                                    ';
                                endforeach;
                            $html .= '
                            </tr>';
                            foreach($non_refundable_contributions as $contribution):
                                $html.='
                                <tr class="listing">
                                    <td><small>'.$contribution->name.'</small></td>';
                                    foreach($months_array as $month):
                                        $html .= '
                                            <td class="text-right"><small>'.number_to_currency($total_contributions_paid_per_contribution_per_month_array[$contribution->id][$month]).'</small></td>
                                        ';
                                    endforeach;
                            endforeach;
                            $html.='
                            <tr class="listing">
                                <td><small>Loan Charges</small></td>';
                                foreach($months_array as $month):
                                    $html .= '
                                        <td class="text-right"><small>'.(isset($total_loan_processing_income_per_month_array[$month])?number_to_currency($total_loan_processing_income_per_month_array[$month]):number_to_currency(0)).'</small></td>
                                    ';
                                endforeach;
                            $html .= '
                            </tr>';

                            foreach($income_category_options as $income_category_id => $name):
                                $html.='
                                <tr class="listing">
                                    <td><small>'.$name.'</small></td>';
                                    foreach($months_array as $month):
                                        $html .= '
                                        <td class="text-right"><small>'.number_to_currency(isset($total_income_per_month_per_income_category_array[$income_category_id][$month])?($total_income_per_month_per_income_category_array[$income_category_id][$month]):(0)).'</small></td>';
                                    endforeach;
                                $html .= '
                                </tr>';
                            endforeach;

                            $html.='
                            <tr class="listing">
                                <td><small>Fines</small></td>';
                                foreach($months_array as $month):
                                    $html .= '
                                        <td class="text-right"><small>'.number_to_currency( (isset($total_fines_per_month_array[$month])?($total_fines_per_month_array[$month]):(0))).'</small></td>
                                    ';


                                endforeach;
                            $html.='
                            </tr>';

                            $html.='
                            <tr class="listing">
                                <td><small>Miscellaneous</small></td>';
                                foreach($months_array as $month):
                                    $html .= '
                                        <td class="text-right"><small>'.number_to_currency((isset($total_miscellaneous_income_per_month_array[$month])?($total_miscellaneous_income_per_month_array[$month]):(0))).'</small></td>
                                    ';


                                endforeach;
                            $html.='
                            </tr>';

                            $html .='
                            <tr>
                                <td></td>';
                                foreach($months_array as $month):
                                    $html .= '
                                        <td class="text-right">'.(($other_operating_income_per_month_array[$month] < 0)?'('.number_to_currency(abs($other_operating_income_per_month_array[$month])).")":number_to_currency($other_operating_income_per_month_array[$month])).'</td>';
                                endforeach;
                            $html .= '
                            </tr>
                            <tr>
                                <td></td>';
                                foreach($months_array as $month):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            <tr>
                                <td><strong>Expenditure</strong></td>';
                                foreach($months_array as $month):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            <tr>
                                <td>Administrative Expenses</td>';
                                foreach($months_array as $month):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            ';

                            foreach($administrative_expense_categories as $administrative_expense_category_id =>  $administrative_expense_category_name):
                                $html.='
                                <tr class="listing">
                                    <td><small>'.$administrative_expense_category_name.'</small></td>';
                                    foreach($months_array as $month):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(isset($expense_category_totals_per_month_array[$administrative_expense_category_id][$month])?$expense_category_totals_per_month_array[$administrative_expense_category_id][$month]:0).'</small></td>';
                                    endforeach;
                                $html .= '
                                </tr>';
                            endforeach;

                            $html .='
                            <tr>
                                <td>Other Operating Expenses</td>';
                                foreach($months_array as $month):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            ';

                            foreach($other_expense_categories as $other_expense_category_id =>  $other_expense_category_name):
                                $html.='
                                <tr class="listing">
                                    <td><small>'.$other_expense_category_name.'</small></td>';
                                    foreach($months_array as $month):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(isset($expense_category_totals_per_month_array[$other_expense_category_id][$month])?$expense_category_totals_per_month_array[$other_expense_category_id][$month]:0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>';
                            endforeach;

                            $html .='
                            <tr>
                                <td></td>';
                                foreach($months_array as $month):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            <tr>
                                <td><strong>Net Operating Surplus/ Deficit for the month before tax</strong></td>';
                                foreach($months_array as $month):
                                    $html .= ' 
                                    <td class="text-right">'.(($month_surplus_before_tax_array[$month] < 0)?'('.number_to_currency(abs($month_surplus_before_tax_array[$month])).")":number_to_currency($month_surplus_before_tax_array[$month]))
                                .'</td>';
                                endforeach;
                            $html .='
                            </tr>';

                            $html .='
                            <tr>
                                <td></td>';
                                foreach($months_array as $month):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr>
                                <td><strong>Net Operating Surplus/ Deficit for the month after tax</strong></td>';
                                foreach($months_array as $month):
                                    $html .= ' 
                                    <td class="text-right">'.(($month_surplus_after_tax_array[$month] < 0)?'('.number_to_currency(abs($month_surplus_after_tax_array[$month])).")":number_to_currency($month_surplus_after_tax_array[$month]))
                                .'</td>';
                                endforeach;
                            $html .= '
                            </tr>
                            <tr>
                                <td></td>';
                                foreach($months_array as $month):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                            $html .='
                            </tr>';

                            // $html .='
                            // <tr>
                            //     <td><strong>Transfer to statutory Reserve 21%</strong></td>';
                            //     foreach($months_array as $month):
                            //         $html .= ' 
                            //         <td class="text-right">'.number_to_currency($transfer_to_statutory_reserve_per_month_array[$month]).'</td>';
                            //     endforeach;
                            $html .='
                            <tr>
                                <td></td>';
                                foreach($months_array as $month):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                            $html .='
                            </tr>';

                            $html .='
                            <tr>
                                <td><strong>Dividends</strong></td>';
                                foreach($months_array as $month):
                                    $html .= ' 
                                    <td class="text-right">';
                                        if(isset($total_dividends_per_month_array[$month])){
                                            $html .= (($total_dividends_per_month_array[$month] < 0)?'('.number_to_currency(abs($total_dividends_per_month_array[$month])).")":number_to_currency($total_dividends_per_month_array[$month]));
                                        }else{
                                            $html .= number_to_currency(0);
                                        }
                                    $html .
                                    '</td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr>
                                <td></td>';
                                foreach($months_array as $month):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                            $html .='
                            </tr>';

                            $html .='
                            <tr>
                                <td><strong>Net Surplus for the month c/d to retaining earnings</strong></td>';

                                foreach($months_array as $month):
                                    $html .= '
                                        <td class="text-right">'.(($retained_earnings_per_month_array[$month] < 0)?'('.number_to_currency(abs($retained_earnings_per_month_array[$month])).")":number_to_currency($retained_earnings_per_month_array[$month])).'</td>
                                    ';
                                endforeach;
                            $html .= '
                            </tr>
                            ';
                        $html .= '
                        </tbody>
                        ';
                    $html .='
                    </table>';
                $html.='
            </div>
        ';

        echo $html;
    }

    function get_income_statement(){

        $from = strtotime($this->input->get_post('from'));
        $to = strtotime($this->input->get_post('to'));

        $non_refundable_contributions = $this->contributions_m->get_group_non_equitable_non_refundable_contributions();
        $administrative_expense_categories = $this->expense_categories_m->get_group_administrative_expense_category_options();
        $other_expense_categories = $this->expense_categories_m->get_group_other_expense_category_options();

        $years_array = array();
        if($from && $to){
            $from_year = date('Y',$from);
            $to_year = date('Y',$to);
            for($i = $from_year; $i <= $to_year; $i++):
                $years_array[] = $i;
            endfor;
        }else{
            $from_year = date('Y',strtotime('-1 year'));
            $to_year = date('Y');
            $years_array = array(
                $from_year,
                $to_year,
            );
        }

        $income_category_options = $this->income_categories_m->get_group_income_category_options();

        $total_loan_interest_paid_per_year_array = $this->reports_m->get_group_total_loan_interest_paid_per_year_array($this->group->id);

        $total_money_market_interest_per_year_array = $this->money_market_investments_m->get_group_total_money_market_interest_per_year_array($this->group->id);
        
        $total_bank_loans_interest_paid_per_year_array = $this->withdrawals_m->get_group_total_bank_loans_interest_paid_per_year_array();

        $total_contributions_paid_per_contribution_per_year_array = $this->reports_m->get_group_total_contributions_paid_per_contribution_per_year_array();

        $expense_category_totals_per_year_array = $this->withdrawals_m->get_group_expense_category_totals_per_year_array();

        $total_loan_processing_income_per_year_array = $this->deposits_m->get_group_total_loan_processing_income_per_year_array();

        $total_income_per_year_array = $this->deposits_m->get_group_total_income_per_year_array();

        $total_income_per_year_per_income_category_array = $this->deposits_m->get_group_total_income_per_year_per_income_category_array();

        $total_miscellaneous_income_per_year_array = $this->deposits_m->get_group_total_miscellaneous_income_per_year_array();

        $total_fines_per_year_array = $this->reports_m->get_group_total_fines_per_year_array($this->group->id);

        $total_year_interest_array = array();
        $net_year_interest_income_array = array();
        $net_year_income_array = array();
        $total_year_income_array = array();
        $year_surplus_before_tax_array = array();
        $year_surplus_after_tax_array = array();
        $net_surplus_for_the_year_array = array();
        $total_non_refundable_contributions_paid_per_year_array = array();
        $other_operating_income_per_year_array = array();
        $loan_processing_income_per_year_array = array();
        $group_income_per_year_array = array();
        $group_expenses_per_year_array = array();
        $transfer_to_statutory_reserve_per_year_array = array();
        $retained_earnings_per_year_array = array();

        foreach($years_array as $year):
            $total_year_interest_array[$year] = 0;
            $net_year_interest_income_array[$year] = 0;
            $net_year_income_array[$year] = 0;
            $total_year_income_array[$year] = 0;
            $year_surplus_before_tax_array[$year] = 0;
            $year_surplus_after_tax_array[$year] = 0;
            $net_surplus_for_the_year_array[$year] = 0;
            $total_non_refundable_contributions_paid_per_year_array[$year] = 0;
            $other_operating_income_per_year_array[$year] = 0;
            $loan_processing_income_per_year_array[$year] = 0;
            $group_income_per_year_array[$year] = 0;
            $group_expenses_per_year_array[$year] = 0;
            $transfer_to_statutory_reserve_per_year_array[$year] = 0;
            $retained_earnings_per_year_array[$year] = 0;
        endforeach;

        foreach($non_refundable_contributions as $contribution):
            foreach($years_array as $year):
                $total_non_refundable_contributions_paid_per_year_array[$year] += $total_contributions_paid_per_contribution_per_year_array[$contribution->id][$year];
            endforeach;
        endforeach;

        foreach($administrative_expense_categories as $administrative_expense_category_id =>  $administrative_expense_category_name):
            foreach($years_array as $year):
                $group_expenses_per_year_array[$year] += (isset($expense_category_totals_per_year_array[$administrative_expense_category_id][$year])?$expense_category_totals_per_year_array[$administrative_expense_category_id][$year]:0);
            endforeach;
        endforeach;

        foreach($other_expense_categories as $other_expense_category_id =>  $other_expense_category_name):
            foreach($years_array as $year):
                $group_expenses_per_year_array[$year] += (isset($expense_category_totals_per_year_array[$other_expense_category_id][$year])?$expense_category_totals_per_year_array[$other_expense_category_id][$year]:0);
            endforeach;
        endforeach;
        
        foreach($years_array as $year):

            $total_year_interest_array[$year] += (isset($total_loan_interest_paid_per_year_array[$year])?$total_loan_interest_paid_per_year_array[$year]:0);
            $total_year_interest_array[$year] += (isset($total_money_market_interest_per_year_array[$year])?$total_money_market_interest_per_year_array[$year]:0);

            $net_year_interest_income_array[$year] += $total_year_interest_array[$year];

            $net_year_interest_income_array[$year] -= (isset($total_bank_loans_interest_paid_per_year_array[$year])?($total_bank_loans_interest_paid_per_year_array[$year]):0);

            $other_operating_income_per_year_array[$year] += $total_non_refundable_contributions_paid_per_year_array[$year];

            $other_operating_income_per_year_array[$year] += (isset($total_loan_processing_income_per_year_array[$year])?$total_loan_processing_income_per_year_array[$year]:0);

            $other_operating_income_per_year_array[$year] += (isset($total_income_per_year_array[$year])?$total_income_per_year_array[$year]:0);

            $other_operating_income_per_year_array[$year] += (isset($total_miscellaneous_income_per_year_array[$year])?$total_miscellaneous_income_per_year_array[$year]:0);

            $other_operating_income_per_year_array[$year] += (isset($total_fines_per_year_array[$year])?$total_fines_per_year_array[$year]:0);

            $year_surplus_before_tax_array[$year] += $net_year_interest_income_array[$year] + $other_operating_income_per_year_array[$year] - $group_expenses_per_year_array[$year];

            $year_surplus_after_tax_array[$year] = $year_surplus_before_tax_array[$year];

            $transfer_to_statutory_reserve_per_year_array[$year] = (($year_surplus_after_tax_array[$year] < 0)?0:($year_surplus_after_tax_array[$year] * 0.0));

            $retained_earnings_per_year_array[$year] = $year_surplus_before_tax_array[$year]-$transfer_to_statutory_reserve_per_year_array[$year];

        endforeach;



        $html='
            <div class="report invoice-content-2 bordered document-border">
                <div class="row invoice-head">
                    <div class="col-md-7 col-xs-6">
                        <div class="invoice-logo">
                            <img src="';
                                $html.=is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                                $html.='" alt="" class="group-logo image-responsive" /> 
                        </div>
                    </div>
                    <div class="col-md-5 col-xs-6 text-right">
                        <div class="company-address">
                            <span class="bold uppercase">'.$this->group->name.'</span><br/>';
                            $html.=nl2br($this->group->address);
                            $html.='<br/>
                            <span class="bold">'.translate('Telephone').': </span> '.$this->group->phone.'
                            <br/>
                            <span class="bold">'.translate('Email Address').': </span> '.$this->group->email.'
                            <br/>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-center margin-top-10 margin-bottom-10">
                        '.ucfirst($this->group->name).'<br/>
                        '.translate('Statement of Comprehensive Income statement for').' '.$from_year.' '.translate('to').' '.$to_year.'
                    </div>
                </div>
                <hr/>
                <div class="row invoice-body">
                    <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement">
                        <thead>
                            <tr>
                                <th width="30%"></th>';
                                $width = round(70/count($years_array));
                                foreach($years_array as $year):
                                    $html .='
                                    <th class="text-right" width="'.$width.'%">'.$year.'</th>';
                                endforeach;
                            $html .='
                            </tr>
                        </thead>';
                        $html.= '
                        <tbody>
                            <tr>
                                <td><strong>'.translate('Income').'</strong></td>';
                                foreach($years_array as $year):
                                    $html .='
                                    <td class="text-right"></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr>
                                <td><strong>'.translate('Interest income').'</strong></td>';
                                foreach($years_array as $year):
                                    $html .='
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>'.translate('Interest on loans').'</small></td>
                                ';
                                foreach($years_array as $year):
                                    $html .= '
                                        <td class="text-right"><small>'.(isset($total_loan_interest_paid_per_year_array[$year])?number_to_currency($total_loan_interest_paid_per_year_array[$year]):number_to_currency(0)).'</small></td>
                                    ';
                                endforeach;
                            $html .='
                            </tr>

                            <tr class="listing">
                                <td><small>'.translate('Interest money market investments').'</small></td>';
                                foreach($years_array as $year):
                                    $html .= '
                                        <td class="text-right"><small>'.(isset($total_money_market_interest_per_year_array[$year])?number_to_currency($total_money_market_interest_per_year_array[$year]):number_to_currency(0)).'</small></td>
                                    ';
                                endforeach;
                                
                            $html .='
                            </tr>
                            <tr>
                                <td><strong>'.translate('Total Interest Income').'</strong></td>';
                                foreach($years_array as $year):
                                    $html .= '     
                                        <td class="text-right">'.number_to_currency($total_year_interest_array[$year]).'</td>
                                    ';
                                endforeach;
                            $html .= '
                            </tr>
                            <tr>
                                <td></td>';
                                foreach($years_array as $year):
                                    $html .='
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .='
                            </tr>
                            <tr>
                                <td><strong>'.translate('Interest Expense').'</strong></td>';
                                foreach($years_array as $year):
                                    $html .='
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>'.translate('Interest on deposits').'</small></td>';
                                foreach($years_array as $year):
                                    $html .='
                                    <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                endforeach;
                                $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>'.translate('Interest on bank loans').'</small></td>';
                                foreach($years_array as $year):
                                    $html .='
                                    <td class="text-right"><small>'.(isset($total_bank_loans_interest_paid_per_year_array[$year])?number_to_currency($total_bank_loans_interest_paid_per_year_array[$year]):number_to_currency(0)).'</small></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            <tr>
                                <td><strong>'.translate('Net Interest Income').'</strong></td>';
                                foreach($years_array as $year):
                                    $html .='
                                    <td class="text-right">'.(($net_year_interest_income_array[$year]<0)?"(".number_to_currency(abs($net_year_interest_income_array[$year])).")":number_to_currency($net_year_interest_income_array[$year])).'</td>
                                    ';
                                endforeach;
                            $html.='
                            </tr>
                            <tr>
                                <td></td>';
                                foreach($years_array as $year):
                                    $html .='
                                    <td class="text-right"></td>
                                    ';
                                endforeach;
                            $html .= '
                            </tr>
                            <tr>
                                <td><strong>'.translate('Other Operating Income').'</strong></td>';
                                foreach($years_array as $year):
                                    $html .='
                                    <td class="text-right"></td>
                                    ';
                                endforeach;
                            $html .= '
                            </tr>';
                            foreach($non_refundable_contributions as $contribution):
                                $html.='
                                <tr class="listing">
                                    <td><small>'.$contribution->name.'</small></td>';
                                    foreach($years_array as $year):
                                        $html .= '
                                            <td class="text-right"><small>'.number_to_currency($total_contributions_paid_per_contribution_per_year_array[$contribution->id][$year]).'</small></td>
                                        ';
                                    endforeach;
                            endforeach;
                            $html.='
                            <tr class="listing">
                                <td><small>'.translate('Loan Charges').'</small></td>';
                                foreach($years_array as $year):
                                    $html .= '
                                        <td class="text-right"><small>'.(isset($total_loan_processing_income_per_year_array[$year])?number_to_currency($total_loan_processing_income_per_year_array[$year]):number_to_currency(0)).'</small></td>
                                    ';
                                endforeach;
                            $html .= '
                            </tr>';

                            foreach($income_category_options as $income_category_id => $name):
                                $html.='
                                <tr class="listing">
                                    <td><small>'.translate($name).'</small></td>';
                                    foreach($years_array as $year):
                                        $html .= '
                                        <td class="text-right"><small>'.number_to_currency(isset($total_income_per_year_per_income_category_array[$income_category_id][$year])?($total_income_per_year_per_income_category_array[$income_category_id][$year]):(0)).'</small></td>';
                                    endforeach;
                                $html .= '
                                </tr>';
                            endforeach;

                            $html.='
                            <tr class="listing">
                                <td><small>'.translate('Fines').'</small></td>';
                                foreach($years_array as $year):
                                    $html .= '
                                        <td class="text-right"><small>'.number_to_currency( (isset($total_fines_per_year_array[$year])?($total_fines_per_year_array[$year]):(0))).'</small></td>
                                    ';


                                endforeach;
                            $html.='
                            </tr>';

                            $html.='
                            <tr class="listing">
                                <td><small>'.translate('Miscellaneous').'</small></td>';
                                foreach($years_array as $year):
                                    $html .= '
                                        <td class="text-right"><small>'.number_to_currency((isset($total_miscellaneous_income_per_year_array[$year])?($total_miscellaneous_income_per_year_array[$year]):(0))).'</small></td>
                                    ';


                                endforeach;
                            $html.='
                            </tr>';

                            $html .='
                            <tr>
                                <td></td>';
                                foreach($years_array as $year):
                                    $html .= '
                                        <td class="text-right">'.(($other_operating_income_per_year_array[$year] < 0)?'('.number_to_currency(abs($other_operating_income_per_year_array[$year])).")":number_to_currency($other_operating_income_per_year_array[$year])).'</td>';
                                endforeach;
                            $html .= '
                            </tr>
                            <tr>
                                <td></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            <tr>
                                <td><strong>'.translate('Expenditure').'</strong></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            <tr>
                                <td>'.translate('Administrative Expenses').'</td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            ';

                            foreach($administrative_expense_categories as $administrative_expense_category_id =>  $administrative_expense_category_name):
                                $html.='
                                <tr class="listing">
                                    <td><small>'.$administrative_expense_category_name.'</small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(isset($expense_category_totals_per_year_array[$administrative_expense_category_id][$year])?$expense_category_totals_per_year_array[$administrative_expense_category_id][$year]:0).'</small></td>';
                                    endforeach;
                                $html .= '
                                </tr>';
                            endforeach;

                            $html .='
                            <tr>
                                <td>'.translate('Other Operating Expenses').'</td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            ';

                            foreach($other_expense_categories as $other_expense_category_id =>  $other_expense_category_name):
                                $html.='
                                <tr class="listing">
                                    <td><small>'.translate($other_expense_category_name).'</small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(isset($expense_category_totals_per_year_array[$other_expense_category_id][$year])?$expense_category_totals_per_year_array[$other_expense_category_id][$year]:0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>';
                            endforeach;

                            $html .='
                            <tr>
                                <td></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                                $html .= '
                            </tr>
                            <tr>
                                <td><strong>'.translate('Net Operating Surplus/ Deficit for the year before tax').'</strong></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right">'.(($year_surplus_before_tax_array[$year] < 0)?'('.number_to_currency(abs($year_surplus_before_tax_array[$year])).")":number_to_currency($year_surplus_before_tax_array[$year]))
                                .'</td>';
                                endforeach;
                            $html .='
                            </tr>';

                            $html .='
                            <tr>
                                <td></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr>
                                <td><strong>'.translate('Net Operating Surplus/ Deficit for the year after tax').'</strong></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right">'.(($year_surplus_after_tax_array[$year] < 0)?'('.number_to_currency(abs($year_surplus_after_tax_array[$year])).")":number_to_currency($year_surplus_after_tax_array[$year]))
                                .'</td>';
                                endforeach;
                            $html .= '
                            </tr>
                            <tr>
                                <td></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                            $html .='
                            </tr>';

                            // $html .='
                            // <tr>
                            //     <td><strong>Transfer to statutory Reserve 21%</strong></td>';
                            //     foreach($years_array as $year):
                            //         $html .= ' 
                            //         <td class="text-right">'.number_to_currency($transfer_to_statutory_reserve_per_year_array[$year]).'</td>';
                            //     endforeach;
                            $html .='
                            <tr>
                                <td></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                            $html .='
                            </tr>';

                            $html .='
                            <tr>
                                <td><strong>'.translate('Proposed Dividends').'</strong></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right">'.number_to_currency(0).'</td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr>
                                <td></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"></td>';
                                endforeach;
                            $html .='
                            </tr>';

                            $html .='
                            <tr>
                                <td><strong>'.translate('Net Surplus for the year c/d to retaining earnings').'</strong></td>';

                                foreach($years_array as $year):
                                    $html .= '
                                        <td class="text-right">'.(($retained_earnings_per_year_array[$year] < 0)?'('.number_to_currency(abs($retained_earnings_per_year_array[$year])).")":number_to_currency($retained_earnings_per_year_array[$year])).'</td>
                                    ';
                                endforeach;
                            $html .= '
                            </tr>
                            ';
                        $html .= '
                        </tbody>
                        ';
                    $html .='
                    </table>';
                $html.='
                <hr/>
                
        ';

        echo $html;
    }

    function get_eazzyclub_balance_sheet(){

        $from = strtotime($this->input->get_post('from'));
        $to = strtotime($this->input->get_post('to'));

        $years_array = array();

        if($from && $to){
            $from_year = date('Y',$from);
            $to_year = date('Y',$to);
            for($i = $from_year; $i <= $to_year; $i++):
                $years_array[] = $i;
            endfor;
        }else{
            $from_year = date('Y',strtotime('-1 year'));
            $to_year = date('Y');
            $years_array = array(
                $from_year,
                $to_year,
            );
        }

        $non_refundable_contributions = $this->contributions_m->get_group_non_equitable_non_refundable_contributions();

        $refundable_contribution_options = $this->contributions_m->get_group_refundable_contribution_options();

        $non_refundable_contribution_options = $this->contributions_m->get_group_equitable_non_refundable_contribution_options();

        $other_financial_assets_per_year_array = array();
        $total_assets_per_year_array = array();
        $total_liabilities_per_year_array = array();

        foreach($years_array as $year):
            $total_assets_per_year_array[$year] = 0;
            $other_financial_assets_per_year_array[$year] = 0;
            $total_liabilities_per_year_array[$year] = 0;
        endforeach;

        $total_principal_money_market_investment_out_per_year_array = $this->money_market_investments_m->get_group_total_principal_money_market_investment_out_per_year_array($this->group->id);

        $total_asset_purchase_payments_per_year_array = $this->withdrawals_m->get_group_total_asset_purchase_payments_per_year_array($this->group->id);

        $total_stock_purchases_per_year_array = $this->withdrawals_m->get_group_total_stock_purchases_per_year_array($this->group->id);

        $total_principal_loans_out_per_year_array = $this->loans_m->get_group_total_principal_loans_out_per_year_array($this->group->id);

        $account_balances_per_year_array = $this->transaction_statements_m->get_group_account_balances_per_year_array($this->group->id);

        $refundable_contributions_per_year_array = $this->reports_m->get_group_total_contributions_per_year_array($this->group->id,0,0,$refundable_contribution_options);

        $administrative_expense_categories = $this->expense_categories_m->get_group_administrative_expense_category_options();

        $other_expense_categories = $this->expense_categories_m->get_group_other_expense_category_options();

        $non_refundable_contributions_per_year_array = $this->reports_m->get_group_total_contributions_paid_cumulatively_per_contribution_per_year_array($this->group->id);

        $total_loan_interest_paid_per_year_array = $this->reports_m->get_group_total_loan_interest_paid_per_year_array($this->group->id);

        $total_money_market_interest_per_year_array = $this->money_market_investments_m->get_group_total_money_market_interest_per_year_array($this->group->id);

        $total_bank_loans_interest_paid_per_year_array = $this->withdrawals_m->get_group_total_bank_loans_interest_paid_per_year_array();

        $total_contributions_paid_per_contribution_per_year_array = $this->reports_m->get_group_total_contributions_paid_per_contribution_per_year_array();

        $expense_category_totals_per_year_array = $this->withdrawals_m->get_group_expense_category_totals_per_year_array();

        $total_loan_processing_income_per_year_array = $this->deposits_m->get_group_total_loan_processing_income_per_year_array();

        $total_income_per_year_array = $this->deposits_m->get_group_total_income_per_year_array();

        $total_miscellaneous_income_per_year_array = $this->deposits_m->get_group_total_miscellaneous_income_per_year_array();

        $total_fines_per_year_array = $this->reports_m->get_group_total_fines_per_year_array($this->group->id);

        $total_loan_overpayments_per_year_array = $this->reports_m->get_group_total_loan_overpayments_per_year_array($this->group->id);
        
        $total_year_interest_array = array();
        $net_year_interest_income_array = array();
        $net_year_income_array = array();
        $total_year_income_array = array();
        $year_surplus_before_tax_array = array();
        $year_surplus_after_tax_array = array();
        $net_surplus_for_the_year_array = array();
        $total_non_refundable_contributions_paid_per_year_array = array();
        $other_operating_income_per_year_array = array();
        $loan_processing_income_per_year_array = array();
        $group_income_per_year_array = array();
        $group_expenses_per_year_array = array();
        $transfer_to_statutory_reserve_per_year_array = array();
        $retained_earnings_per_year_array = array();
        $total_owners_equity_per_year_array = array();

        $alternative_years_array = array();

        $current_year = date('Y');

        for($i = 1970;$i <= $current_year; $i++):
            $alternative_years_array[] = $i;
        endfor;

        foreach($alternative_years_array as $year):
            $total_year_interest_array[$year] = 0;
            $net_year_interest_income_array[$year] = 0;
            $net_year_income_array[$year] = 0;
            $total_year_income_array[$year] = 0;
            $year_surplus_before_tax_array[$year] = 0;
            $year_surplus_after_tax_array[$year] = 0;
            $net_surplus_for_the_year_array[$year] = 0;
            $total_non_refundable_contributions_paid_per_year_array[$year] = 0;
            $other_operating_income_per_year_array[$year] = 0;
            $loan_processing_income_per_year_array[$year] = 0;
            $group_income_per_year_array[$year] = 0;
            $group_expenses_per_year_array[$year] = 0;
            $transfer_to_statutory_reserve_per_year_array[$year] = 0;
            $retained_earnings_per_year_array[$year] = 0;
            $general_reserves_per_year_array[$year] = 0;
            $share_transfer_fund_per_year_array[$year] = 0;
            $institutional_capital_fund_per_year_array[$year] =  0;
            $educational_fund_per_year_array[$year] =  0;
            $total_owners_equity_per_year_array[$year] =  0;
            $balancing_difference_per_year_array[$year] = 0;
        endforeach;

        foreach($non_refundable_contributions as $contribution):
            foreach($alternative_years_array as $year):
                $total_non_refundable_contributions_paid_per_year_array[$year] += isset($total_contributions_paid_per_contribution_per_year_array[$contribution->id][$year])?$total_contributions_paid_per_contribution_per_year_array[$contribution->id][$year]:0;
            endforeach;
        endforeach;

        foreach($administrative_expense_categories as $administrative_expense_category_id =>  $administrative_expense_category_name):
            foreach($alternative_years_array as $year):
                $group_expenses_per_year_array[$year] += (isset($expense_category_totals_per_year_array[$administrative_expense_category_id][$year])?$expense_category_totals_per_year_array[$administrative_expense_category_id][$year]:0);
            endforeach;
        endforeach;

        foreach($other_expense_categories as $other_expense_category_id =>  $other_expense_category_name):
            foreach($alternative_years_array as $year):
                $group_expenses_per_year_array[$year] += (isset($expense_category_totals_per_year_array[$other_expense_category_id][$year])?$expense_category_totals_per_year_array[$other_expense_category_id][$year]:0);
            endforeach;
        endforeach;

        foreach($alternative_years_array as $year):

            $total_year_interest_array[$year] += 
            (isset($total_loan_interest_paid_per_year_array[$year])?$total_loan_interest_paid_per_year_array[$year]:0);

            $total_year_interest_array[$year] += (isset($total_money_market_interest_per_year_array[$year])?$total_money_market_interest_per_year_array[$year]:0);

            $net_year_interest_income_array[$year] += $total_year_interest_array[$year];

            $net_year_interest_income_array[$year] -= (isset($total_bank_loans_interest_paid_per_year_array[$year])?($total_bank_loans_interest_paid_per_year_array[$year]):0);

            $other_operating_income_per_year_array[$year] += $total_non_refundable_contributions_paid_per_year_array[$year];

            $other_operating_income_per_year_array[$year] += (isset($total_loan_processing_income_per_year_array[$year])?$total_loan_processing_income_per_year_array[$year]:0);

            $other_operating_income_per_year_array[$year] += (isset($total_income_per_year_array[$year])?$total_income_per_year_array[$year]:0);

            $other_operating_income_per_year_array[$year] += (isset($total_miscellaneous_income_per_year_array[$year])?$total_miscellaneous_income_per_year_array[$year]:0);

            $other_operating_income_per_year_array[$year] += (isset($total_fines_per_year_array[$year])?$total_fines_per_year_array[$year]:0);

            $year_surplus_before_tax_array[$year] += $net_year_interest_income_array[$year] + $other_operating_income_per_year_array[$year] - $group_expenses_per_year_array[$year];

            $year_surplus_after_tax_array[$year] = $year_surplus_before_tax_array[$year];

            $general_reserves_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.10));

            $share_transfer_fund_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.05));

            $institutional_capital_fund_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.05));

            $educational_fund_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.01));

            $transfer_to_statutory_reserve_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.21));

            $retained_earnings_per_year_array[$year] = (($year_surplus_before_tax_array[$year] - $transfer_to_statutory_reserve_per_year_array[$year]));

        endforeach;

        foreach($retained_earnings_per_year_array as $key => $value):

            if(isset($retained_earnings_per_year_array[($key - 1)])){
                $retained_earnings_per_year_array[$key] += $retained_earnings_per_year_array[($key - 1)];
            }
            if(isset($general_reserves_per_year_array[($key - 1)])){
                $general_reserves_per_year_array[$key] += $general_reserves_per_year_array[($key - 1)];
            }
            if(isset($share_transfer_fund_per_year_array[($key - 1)])){
                $share_transfer_fund_per_year_array[$key] += $share_transfer_fund_per_year_array[($key - 1)];
            }
            if(isset($institutional_capital_fund_per_year_array[($key - 1)])){
                $institutional_capital_fund_per_year_array[$key] += $institutional_capital_fund_per_year_array[($key - 1)];
            }
            if(isset($educational_fund_per_year_array[($key - 1)])){
                $educational_fund_per_year_array[$key] += $educational_fund_per_year_array[($key - 1)];
            }
        endforeach;


        //Assets addition
        foreach($years_array as $year):
            if(isset($total_principal_money_market_investment_out_per_year_array[$year])){
                $other_financial_assets = $total_principal_money_market_investment_out_per_year_array[$year];
            }else{
                $other_financial_assets = 0;
            }
            $total_assets_per_year_array[$year] += $other_financial_assets;
        endforeach;

        $other_financial_assets = 0;

        foreach($years_array as $year):
            if(isset($total_principal_loans_out_per_year_array[$year])){
                $loan_to_members = $total_principal_loans_out_per_year_array[$year];
            }else{
                if(isset($loan_to_members)){

                }else{
                    $loan_to_members = 0;
                }
            }
            $total_assets_per_year_array[$year] += $loan_to_members;
        endforeach;

        // print_r($total_principal_loans_out_per_year_array);
        // echo "<hr/>";
        // //print_r($total_liabilities_per_year_array);
        // die;


        $loan_to_members = 0;
        

        foreach($years_array as $year):
            if(isset($total_asset_purchase_payments_per_year_array[$year])){
                $fixed_asset_value = $total_asset_purchase_payments_per_year_array[$year];
            }else{
                if(isset($fixed_asset_value)){

                }else{
                    $fixed_asset_value = 0;
                }
            }
            $total_assets_per_year_array[$year] += $fixed_asset_value;
        endforeach;
        $fixed_asset_value = 0;

        foreach($years_array as $year):
            if(isset($total_stock_purchases_per_year_array[$year])){
                $stock_purchase_value = $total_stock_purchases_per_year_array[$year];
            }else{
                if(isset($stock_purchase_value)){

                }else{
                    $stock_purchase_value = 0;
                }
            }
            $total_assets_per_year_array[$year] += $stock_purchase_value;
        endforeach;

        $stock_purchase_value = 0;

        foreach($years_array as $year):
            
            if(isset($account_balances_per_year_array[$year])){
                $cash_at_bank = $account_balances_per_year_array[$year];
            }else{
                if(isset($cash_at_bank)){

                }else{
                    $cash_at_bank = 0;
                }
            }
            $total_assets_per_year_array[$year] += $cash_at_bank;
        endforeach;

        $cash_at_bank = 0;

        //liabilities
        foreach($years_array as $year):
            if(isset($refundable_contributions_per_year_array[$year])){
                $refundable_member_deposits = $refundable_contributions_per_year_array[$year];
            }else{
                if(isset($refundable_member_deposits)){

                }else{
                    $refundable_member_deposits = 0;
                }
            }
            $total_liabilities_per_year_array[$year] += $refundable_member_deposits;
        endforeach;
        $refundable_member_deposits = 0;

        foreach($years_array as $year):
            if(isset($total_loan_overpayments_per_year_array[$year])){
                $loan_overpayment = $total_loan_overpayments_per_year_array[$year];
            }else{
                if(isset($loan_overpayment)){

                }else{
                    $loan_overpayment = 0;
                }
            }
            $total_liabilities_per_year_array[$year] += $loan_overpayment;
        endforeach;
        $loan_overpayment = 0;

        //equity
        foreach($non_refundable_contribution_options as $contribution_id => $name):
            foreach($years_array as $year):
                if(isset($non_refundable_contributions_per_year_array[$contribution_id][$year])){
                    $non_refundable_member_deposits = $non_refundable_contributions_per_year_array[$contribution_id][$year];
                }else{
                    if(isset($non_refundable_member_deposits)){

                    }else{
                        $non_refundable_member_deposits = 0;
                    }
                }
                $total_owners_equity_per_year_array[$year] += $non_refundable_member_deposits;
            endforeach;
        endforeach;
        $non_refundable_member_deposits = 0;

        foreach($years_array as $year):
            if(isset($share_transfer_fund_per_year_array[$year])){
                $share_transfer_fund = $share_transfer_fund_per_year_array[$year];
            }else{
                if(isset($share_transfer_fund)){

                }else{
                    $share_transfer_fund = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $share_transfer_fund;
        endforeach;
        $share_transfer_fund = 0;

        foreach($years_array as $year):
            if(isset($general_reserves_per_year_array[$year])){
                $general_reserves = $general_reserves_per_year_array[$year];
            }else{
                if(isset($general_reserves)){

                }else{
                    $general_reserves = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $general_reserves;
        endforeach;
        $general_reserves = 0;

        foreach($years_array as $year):
            if(isset($institutional_capital_fund_per_year_array[$year])){
                $institutional_capital = $institutional_capital_fund_per_year_array[$year];
            }else{
                if(isset($institutional_capital_fund_per_year_array[$year])){

                }else{
                    $institutional_capital = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $institutional_capital;
        endforeach;
        $institutional_capital = 0;

        foreach($years_array as $year):
            if(isset($educational_fund_per_year_array[$year])){
                $educational_fund = $educational_fund_per_year_array[$year];
            }else{
                if(isset($educational_fund_per_year_array[$year])){

                }else{
                    $educational_fund = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $educational_fund;
        endforeach;
        $educational_fund = 0;

        foreach($years_array as $year):
            if(isset($retained_earnings_per_year_array[$year])){
                $retained_earnings = $retained_earnings_per_year_array[$year];
            }else{
                if(isset($retained_earnings)){

                }else{
                    $retained_earnings = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $retained_earnings;
        endforeach;
        $retained_earnings = 0;

        // 
        foreach($years_array as $year):

            $balancing_difference_per_year_array[$year] = $total_assets_per_year_array[$year] - ($total_owners_equity_per_year_array[$year] + $total_liabilities_per_year_array[$year]);

        endforeach;

        foreach($years_array as $year):
            $total_assets_per_year_array[$year] -= $balancing_difference_per_year_array[$year];
        endforeach;

        $html='
            <div class="report invoice-content-2 bordered document-border">
                <div class="row invoice-head">
                    <div class="col-md-7 col-xs-6">
                        <div class="invoice-logo">
                            <img src="';
                                $html.=is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                                $html.='" alt="" class="group-logo image-responsive" /> 
                        </div>
                    </div>
                    <div class="col-md-5 col-xs-6 text-right">
                        <div class="company-address">
                            <span class="bold uppercase">'.$this->group->name.'</span><br/>';
                            $html.=nl2br($this->group->address);
                            $html.='<br/>
                            <span class="bold">Telephone: </span> '.$this->group->phone.'
                            <br/>
                            <span class="bold">E-mail Address: </span> '.$this->group->email.'
                            <br/>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-center margin-top-10 margin-bottom-10">
                        '.ucfirst($this->group->name).'<br/>
                        Statement of financial position as at '.$from_year.' to '.$to_year.'

                    </div>
                </div>
                <hr/>
                <div class="row invoice-body">
                    <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                        <thead>
                            <tr>
                                <th width="30%"></th>';

                                $width = round(70/count($years_array));
                                foreach($years_array as $year):
                                    $html .='
                                    <th class="text-right" width="'.$width.'%">'.$year.'</th>';
                                endforeach;
                            $html .='
                            </tr>
                        </thead>';
                        $html.= '
                            <tbody>
                            <tr>
                                <td><strong>Assets</strong></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Receivables</small></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Other financial assets</small></td>';
                                foreach($years_array as $year):
                                    if(isset($total_principal_money_market_investment_out_per_year_array[$year])){
                                        $other_financial_assets = $total_principal_money_market_investment_out_per_year_array[$year];
                                    }else{
                                        $other_financial_assets = 0;
                                    }

                                    if(isset($total_stock_purchases_per_year_array[$year])){
                                        $other_financial_assets += $total_stock_purchases_per_year_array[$year];
                                    }else{
                                        if(isset($other_financial_assets)){

                                        }else{
                                            $other_financial_assets = 0;
                                        }
                                    }
                                    //$total_assets_per_year_array[$year] += $other_financial_assets;
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency($other_financial_assets).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small><a href="'.site_url('group/reports/loans_summary').'">Loans to members</a></small></td>';
                                foreach($years_array as $year):
                                    if(isset($total_principal_loans_out_per_year_array[$year])){
                                        $loan_to_members = $total_principal_loans_out_per_year_array[$year] - $balancing_difference_per_year_array[$year];
                                    }else{
                                        $loan_to_members = 0;
                                    }

                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency($loan_to_members).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Property, plant and equipment</small></td>';
                                foreach($years_array as $year):
                                    if(isset($total_asset_purchase_payments_per_year_array[$year])){
                                        $fixed_asset_value = $total_asset_purchase_payments_per_year_array[$year];
                                    }else{
                                        if(isset($fixed_asset_value)){

                                        }else{
                                            $fixed_asset_value = 0;
                                        }
                                    }       
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency($fixed_asset_value).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Cash/ Cash Equivalent</small></td>';
                                foreach($years_array as $year): 
                                    if(isset($account_balances_per_year_array[$year])){
                                        $cash_at_bank = $account_balances_per_year_array[$year];
                                    }else{
                                        if(isset($cash_at_bank)){

                                        }else{
                                            $cash_at_bank = 0;
                                        }
                                    }
                                    $html .= ' 
                                    <td class="text-right"><small>'.(($cash_at_bank >= 0)?number_to_currency($cash_at_bank):"(".number_to_currency(abs($cash_at_bank)).")").'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr>
                                <td><strong>Total Assets</strong></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency($total_assets_per_year_array[$year]).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>';
                            $html.= '
                                <tbody>
                                <tr>
                                    <td><strong>Liabilities</strong></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                
                                <tr class="listing">
                                    <td><small>Members\' deposits</small></td>';
                                    foreach($years_array as $year):
                                        if(isset($refundable_contributions_per_year_array[$year])){
                                            $refundable_member_deposits = $refundable_contributions_per_year_array[$year];
                                        }else{
                                            if(isset($refundable_member_deposits)){

                                            }else{
                                                $refundable_member_deposits = 0;
                                            }
                                        }
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($refundable_member_deposits).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>Members\' Interest payable</small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>Trades and other payables</small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>Sundry creditors</small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>Dividends payable</small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>Interest bearing liability</small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>Member loan overpayments </small></td>';
                                    foreach($years_array as $year):
                                        if(isset($total_loan_overpayments_per_year_array[$year])){
                                            $loan_overpayment = $total_loan_overpayments_per_year_array[$year];
                                        }else{
                                            if(isset($loan_overpayment)){

                                            }else{
                                                $loan_overpayment = 0;
                                            }
                                        }
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($loan_overpayment).'</small></td>';
                                    endforeach;
                                $html .='                                
                                </tr>
                                <tr>
                                    <td><strong>Total Liabilities</strong></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($total_liabilities_per_year_array[$year]).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                            ';
                            $html.= '
                                <tbody>
                                <tr>
                                    <td><strong>Owners\' Equity</strong></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small><strong>Members\' share capital</strong></small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small></small></td>';
                                    endforeach;
                                $html .='
                                </tr>';
                                foreach($non_refundable_contribution_options as $contribution_id => $name):
                                    $html.='
                                    <tr class="listing">
                                        <td><small>&nbsp;&nbsp;&nbsp;'.$name.'</small></td>';
                                        foreach($years_array as $year):
                                            if(isset($non_refundable_contributions_per_year_array[$contribution_id][$year])){
                                                $non_refundable_member_deposits = $non_refundable_contributions_per_year_array[$contribution_id][$year];
                                            }else{
                                                if(isset($non_refundable_member_deposits)){

                                                }else{
                                                    $non_refundable_member_deposits = 0;
                                                }
                                            }
                                            $html .= ' 
                                            <td class="text-right"><small>'.number_to_currency($non_refundable_member_deposits).'</small></td>';
                                        endforeach;
                                    $html .='
                                    </tr>';
                                endforeach;
                            $html.='
                                <tr class="listing">
                                    <td><small><strong>Reserve funds</strong></small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small></small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>&nbsp;&nbsp;&nbsp;Share transfer fund</small></td>';

                                    foreach($years_array as $year):
                                        if(isset($share_transfer_fund_per_year_array[$year])){
                                            $share_transfer_fund = $share_transfer_fund_per_year_array[$year];
                                        }else{
                                            if(isset($share_transfer_fund)){

                                            }else{
                                                $share_transfer_fund = 0;
                                            }
                                        }
                                        $html .= ' 
                                        <td class="text-right"><small>'.($share_transfer_fund < 0?'('.number_to_currency(abs($share_transfer_fund)).')':number_to_currency($share_transfer_fund)).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>&nbsp;&nbsp;&nbsp;General reserves</small></td>';
                                    foreach($years_array as $year):
                                        if(isset($general_reserves_per_year_array[$year])){
                                            $general_reserves = $general_reserves_per_year_array[$year];
                                        }else{
                                            if(isset($general_reserves)){

                                            }else{
                                                $general_reserves = 0;
                                            }
                                        }

                                        $html .= ' 
                                        <td class="text-right"><small>'.($general_reserves < 0?'('.number_to_currency(abs($general_reserves)).')':number_to_currency($general_reserves)).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr><tr class="listing">
                                    <td><small>&nbsp;&nbsp;&nbsp;Institutional capital</small></td>';
                                    foreach($years_array as $year):
                                        if(isset($institutional_capital_fund_per_year_array[$year])){
                                            $institutional_capital = $institutional_capital_fund_per_year_array[$year];
                                        }else{
                                            if(isset($institutional_capital_fund_per_year_array[$year])){

                                            }else{
                                                $institutional_capital = 0;
                                            }
                                        }
                                        $html .= ' 
                                        <td class="text-right"><small>'.($institutional_capital < 0?'('.number_to_currency(abs($institutional_capital)).')':number_to_currency($institutional_capital)).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>&nbsp;&nbsp;&nbsp;Educational fund</small></td>';
                                    foreach($years_array as $year):
                                        if(isset($educational_fund_per_year_array[$year])){
                                            $educational_fund = $educational_fund_per_year_array[$year];
                                        }else{
                                            if(isset($educational_fund_per_year_array[$year])){

                                            }else{
                                                $educational_fund = 0;
                                            }
                                        }

                                        $html .= ' 
                                        <td class="text-right"><small>'.($educational_fund < 0?'('.number_to_currency(abs($educational_fund)).')':number_to_currency($educational_fund)).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>Retained Earnings</small></td>';
                                    foreach($years_array as $year):
                                        if(isset($retained_earnings_per_year_array[$year])){
                                            $retained_earnings = $retained_earnings_per_year_array[$year];
                                        }else{
                                            if(isset($retained_earnings)){

                                            }else{
                                                $retained_earnings = 0;
                                            }
                                        }

                                        $html .= ' 
                                        <td class="text-right"><small>'.($retained_earnings < 0?'('.number_to_currency(abs($retained_earnings)).')':number_to_currency($retained_earnings)).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr>
                                    <td><strong>Total Owners\' Equity</strong></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.($total_owners_equity_per_year_array[$year] < 0?'('.number_to_currency(abs($total_owners_equity_per_year_array[$year])).')':number_to_currency($total_owners_equity_per_year_array[$year])).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr>
                                    <td></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr>
                                    <td><strong>Total Owners\' Equity & Liabilities</strong></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($total_owners_equity_per_year_array[$year] + $total_liabilities_per_year_array[$year]).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                            ';
                        $html .= '
                        </tbody>
                        ';
                    $html .='
                    </table>';
                    //if(preg_match('/41\.210\.141\.116/',$_SERVER['REMOTE_ADDR'])||preg_match('/127\.0\.0\.1/',$_SERVER['REMOTE_ADDR'])){
                    if($this->input->get('debug')){
                        // $balancing_difference_per_year_array = array_filter($balancing_difference_per_year_array);
                        // print_r($balancing_difference_per_year_array);
                        $html .= '
                        <hr/>
                        <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                            <tbody>
                                <tr>
                                    <td>Difference</td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($balancing_difference_per_year_array[$year]).'</small></td>';
                                    endforeach;
                                $html .= '
                                </tr>
                            </tbody>
                        </table>
                        ';
                    }
                $html.='
                <hr/>
                <div class="row">
                    <div class="col-md-12 margin-top-10 margin-bottom-10 text-center">
                        <span class="sbold">Powered by</span><br/>
                         <div class="invoice-logo-footer">
                            <img src="';
                            $html.=site_url("uploads/logos/".$this->application_settings->paper_footer_logo);
                            $html.='" alt="" class="report-group-logo-footer image-responsive" /> 
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-xs-12">
                        <a class="btn btn-sm blue hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class="fa fa-print"></i> Print</a>
                    </div>
                </div>
            </div>
        ';
        
        echo $html;
    }

    function get_balance_sheet(){

        $from = strtotime($this->input->get_post('from'));
        $to = strtotime($this->input->get_post('to'));

        $years_array = array();

        if($from && $to){
            $from_year = date('Y',$from);
            $to_year = date('Y',$to);
            for($i = $from_year; $i <= $to_year; $i++):
                $years_array[] = $i;
            endfor;
        }else{
            $from_year = date('Y',strtotime('-1 year'));
            $to_year = date('Y');
            $years_array = array(
                $from_year,
                $to_year,
            );
        }

        $non_refundable_contributions = $this->contributions_m->get_group_non_equitable_non_refundable_contributions();

        $refundable_contribution_options = $this->contributions_m->get_group_refundable_contribution_options();

        $non_refundable_contribution_options = $this->contributions_m->get_group_equitable_non_refundable_contribution_options();

        $other_financial_assets_per_year_array = array();
        $total_assets_per_year_array = array();
        $total_liabilities_per_year_array = array();

        foreach($years_array as $year):
            $total_assets_per_year_array[$year] = 0;
            $other_financial_assets_per_year_array[$year] = 0;
            $total_liabilities_per_year_array[$year] = 0;
        endforeach;

        $total_principal_money_market_investment_out_per_year_array = $this->money_market_investments_m->get_group_total_principal_money_market_investment_out_per_year_array($this->group->id);

        $total_asset_purchase_payments_per_year_array = $this->withdrawals_m->get_group_total_asset_purchase_payments_per_year_array($this->group->id);

        $total_stock_purchases_per_year_array = $this->withdrawals_m->get_group_total_stock_purchases_per_year_array($this->group->id);

        $total_principal_loans_out_per_year_array = $this->loans_m->get_group_total_principal_loans_out_per_year_array($this->group->id);

        $total_interest_bearing_liability_per_year_array = $this->bank_loans_m->get_group_total_interest_bearing_liability_per_year_array($this->group->id);

        $account_balances_per_year_array = $this->transaction_statements_m->get_group_account_balances_per_year_array($this->group->id);

        $refundable_contributions_per_year_array = $this->reports_m->get_group_total_contributions_per_year_array($this->group->id,0,0,$refundable_contribution_options);

        $administrative_expense_categories = $this->expense_categories_m->get_group_administrative_expense_category_options();

        $other_expense_categories = $this->expense_categories_m->get_group_other_expense_category_options();

        $non_refundable_contributions_per_year_array = $this->reports_m->get_group_total_contributions_paid_cumulatively_per_contribution_per_year_array($this->group->id);

        $total_loan_interest_paid_per_year_array = $this->reports_m->get_group_total_loan_interest_paid_per_year_array($this->group->id);

        $total_money_market_interest_per_year_array = $this->money_market_investments_m->get_group_total_money_market_interest_per_year_array($this->group->id);

        $total_bank_loans_interest_paid_per_year_array = $this->withdrawals_m->get_group_total_bank_loans_interest_paid_per_year_array();

        $total_contributions_paid_per_contribution_per_year_array = $this->reports_m->get_group_total_contributions_paid_per_contribution_per_year_array();

        $expense_category_totals_per_year_array = $this->withdrawals_m->get_group_expense_category_totals_per_year_array();

        $total_loan_processing_income_per_year_array = $this->deposits_m->get_group_total_loan_processing_income_per_year_array();

        $total_income_per_year_array = $this->deposits_m->get_group_total_income_per_year_array();

        $total_miscellaneous_income_per_year_array = $this->deposits_m->get_group_total_miscellaneous_income_per_year_array();

        $total_fines_per_year_array = $this->reports_m->get_group_total_fines_per_year_array($this->group->id);

        $total_loan_overpayments_per_year_array = $this->reports_m->get_group_total_loan_overpayments_per_year_array($this->group->id);
        
        $total_year_interest_array = array();
        $net_year_interest_income_array = array();
        $net_year_income_array = array();
        $total_year_income_array = array();
        $year_surplus_before_tax_array = array();
        $year_surplus_after_tax_array = array();
        $net_surplus_for_the_year_array = array();
        $total_non_refundable_contributions_paid_per_year_array = array();
        $other_operating_income_per_year_array = array();
        $loan_processing_income_per_year_array = array();
        $group_income_per_year_array = array();
        $group_expenses_per_year_array = array();
        $transfer_to_statutory_reserve_per_year_array = array();
        $retained_earnings_per_year_array = array();
        $total_owners_equity_per_year_array = array();

        $alternative_years_array = array();

        $current_year = date('Y');

        for($i = 1970;$i <= $current_year; $i++):
            $alternative_years_array[] = $i;
        endfor;

        foreach($alternative_years_array as $year):
            $total_year_interest_array[$year] = 0;
            $net_year_interest_income_array[$year] = 0;
            $net_year_income_array[$year] = 0;
            $total_year_income_array[$year] = 0;
            $year_surplus_before_tax_array[$year] = 0;
            $year_surplus_after_tax_array[$year] = 0;
            $net_surplus_for_the_year_array[$year] = 0;
            $total_non_refundable_contributions_paid_per_year_array[$year] = 0;
            $other_operating_income_per_year_array[$year] = 0;
            $loan_processing_income_per_year_array[$year] = 0;
            $group_income_per_year_array[$year] = 0;
            $group_expenses_per_year_array[$year] = 0;
            $transfer_to_statutory_reserve_per_year_array[$year] = 0;
            $retained_earnings_per_year_array[$year] = 0;
            $general_reserves_per_year_array[$year] = 0;
            $share_transfer_fund_per_year_array[$year] = 0;
            $institutional_capital_fund_per_year_array[$year] =  0;
            $educational_fund_per_year_array[$year] =  0;
            $total_owners_equity_per_year_array[$year] =  0;
            $balancing_difference_per_year_array[$year] = 0;
        endforeach;

        foreach($non_refundable_contributions as $contribution):
            foreach($alternative_years_array as $year):
                $total_non_refundable_contributions_paid_per_year_array[$year] += isset($total_contributions_paid_per_contribution_per_year_array[$contribution->id][$year])?$total_contributions_paid_per_contribution_per_year_array[$contribution->id][$year]:0;
            endforeach;
        endforeach;

        foreach($administrative_expense_categories as $administrative_expense_category_id =>  $administrative_expense_category_name):
            foreach($alternative_years_array as $year):
                $group_expenses_per_year_array[$year] += (isset($expense_category_totals_per_year_array[$administrative_expense_category_id][$year])?$expense_category_totals_per_year_array[$administrative_expense_category_id][$year]:0);
            endforeach;
        endforeach;

        foreach($other_expense_categories as $other_expense_category_id =>  $other_expense_category_name):
            foreach($alternative_years_array as $year):
                $group_expenses_per_year_array[$year] += (isset($expense_category_totals_per_year_array[$other_expense_category_id][$year])?$expense_category_totals_per_year_array[$other_expense_category_id][$year]:0);
            endforeach;
        endforeach;

        foreach($alternative_years_array as $year):

            $total_year_interest_array[$year] += 
            (isset($total_loan_interest_paid_per_year_array[$year])?$total_loan_interest_paid_per_year_array[$year]:0);

            $total_year_interest_array[$year] += (isset($total_money_market_interest_per_year_array[$year])?$total_money_market_interest_per_year_array[$year]:0);

            $net_year_interest_income_array[$year] += $total_year_interest_array[$year];

            $net_year_interest_income_array[$year] -= (isset($total_bank_loans_interest_paid_per_year_array[$year])?($total_bank_loans_interest_paid_per_year_array[$year]):0);

            $other_operating_income_per_year_array[$year] += $total_non_refundable_contributions_paid_per_year_array[$year];

            $other_operating_income_per_year_array[$year] += (isset($total_loan_processing_income_per_year_array[$year])?$total_loan_processing_income_per_year_array[$year]:0);

            $other_operating_income_per_year_array[$year] += (isset($total_income_per_year_array[$year])?$total_income_per_year_array[$year]:0);

            $other_operating_income_per_year_array[$year] += (isset($total_miscellaneous_income_per_year_array[$year])?$total_miscellaneous_income_per_year_array[$year]:0);

            $other_operating_income_per_year_array[$year] += (isset($total_fines_per_year_array[$year])?$total_fines_per_year_array[$year]:0);

            $year_surplus_before_tax_array[$year] += $net_year_interest_income_array[$year] + $other_operating_income_per_year_array[$year] - $group_expenses_per_year_array[$year];

            $year_surplus_after_tax_array[$year] = $year_surplus_before_tax_array[$year];

            $general_reserves_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.00));

            $share_transfer_fund_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.00));

            $institutional_capital_fund_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.00));

            $educational_fund_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.00));

            $transfer_to_statutory_reserve_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.00));

            $retained_earnings_per_year_array[$year] = (($year_surplus_before_tax_array[$year] - $transfer_to_statutory_reserve_per_year_array[$year]));

        endforeach;

        foreach($retained_earnings_per_year_array as $key => $value):

            if(isset($retained_earnings_per_year_array[($key - 1)])){
                $retained_earnings_per_year_array[$key] += $retained_earnings_per_year_array[($key - 1)];
            }
            if(isset($general_reserves_per_year_array[($key - 1)])){
                $general_reserves_per_year_array[$key] += $general_reserves_per_year_array[($key - 1)];
            }
            if(isset($share_transfer_fund_per_year_array[($key - 1)])){
                $share_transfer_fund_per_year_array[$key] += $share_transfer_fund_per_year_array[($key - 1)];
            }
            if(isset($institutional_capital_fund_per_year_array[($key - 1)])){
                $institutional_capital_fund_per_year_array[$key] += $institutional_capital_fund_per_year_array[($key - 1)];
            }
            if(isset($educational_fund_per_year_array[($key - 1)])){
                $educational_fund_per_year_array[$key] += $educational_fund_per_year_array[($key - 1)];
            }
        endforeach;


        //Assets addition
        foreach($years_array as $year):
            if(isset($total_principal_money_market_investment_out_per_year_array[$year])){
                $other_financial_assets = $total_principal_money_market_investment_out_per_year_array[$year];
            }else{
                $other_financial_assets = 0;
            }
            $total_assets_per_year_array[$year] += $other_financial_assets;
        endforeach;

        $other_financial_assets = 0;

        foreach($years_array as $year):
            if(isset($total_principal_loans_out_per_year_array[$year])){
                $loan_to_members = $total_principal_loans_out_per_year_array[$year];
            }else{
                if(isset($loan_to_members)){

                }else{
                    $loan_to_members = 0;
                }
            }
            $total_assets_per_year_array[$year] += $loan_to_members;
        endforeach;

        foreach($years_array as $year):
            if(isset($total_interest_bearing_liability_per_year_array[$year])){
                $bank_loans = $total_interest_bearing_liability_per_year_array[$year];
            }else{
                if(isset($bank_loans)){

                }else{
                    $bank_loans = 0;
                }
            }
            $total_liabilities_per_year_array[$year] += $bank_loans;
        endforeach;



        



        // print_r($total_principal_loans_out_per_year_array);
        // echo "<hr/>";
        // //print_r($total_liabilities_per_year_array);
        // die;


        $loan_to_members = 0;
        

        foreach($years_array as $year):
            if(isset($total_asset_purchase_payments_per_year_array[$year])){
                $fixed_asset_value = $total_asset_purchase_payments_per_year_array[$year];
            }else{
                if(isset($fixed_asset_value)){

                }else{
                    $fixed_asset_value = 0;
                }
            }
            $total_assets_per_year_array[$year] += $fixed_asset_value;
        endforeach;
        $fixed_asset_value = 0;

        foreach($years_array as $year):
            if(isset($total_stock_purchases_per_year_array[$year])){
                $stock_purchase_value = $total_stock_purchases_per_year_array[$year];
            }else{
                if(isset($stock_purchase_value)){

                }else{
                    $stock_purchase_value = 0;
                }
            }
            $total_assets_per_year_array[$year] += $stock_purchase_value;
        endforeach;

        $stock_purchase_value = 0;

        foreach($years_array as $year):
            
            if(isset($account_balances_per_year_array[$year])){
                $cash_at_bank = $account_balances_per_year_array[$year];
            }else{
                if(isset($cash_at_bank)){

                }else{
                    $cash_at_bank = 0;
                }
            }
            $total_assets_per_year_array[$year] += $cash_at_bank;
        endforeach;

        $cash_at_bank = 0;

        //liabilities
        foreach($years_array as $year):
            if(isset($refundable_contributions_per_year_array[$year])){
                $refundable_member_deposits = $refundable_contributions_per_year_array[$year];
            }else{
                if(isset($refundable_member_deposits)){

                }else{
                    $refundable_member_deposits = 0;
                }
            }
            $total_liabilities_per_year_array[$year] += $refundable_member_deposits;
        endforeach;
        $refundable_member_deposits = 0;

        foreach($years_array as $year):
            if(isset($total_loan_overpayments_per_year_array[$year])){
                $loan_overpayment = $total_loan_overpayments_per_year_array[$year];
            }else{
                if(isset($loan_overpayment)){

                }else{
                    $loan_overpayment = 0;
                }
            }
            $total_liabilities_per_year_array[$year] += $loan_overpayment;
        endforeach;
        $loan_overpayment = 0;

        //equity
        foreach($non_refundable_contribution_options as $contribution_id => $name):
            foreach($years_array as $year):
                if(isset($non_refundable_contributions_per_year_array[$contribution_id][$year])){
                    $non_refundable_member_deposits = $non_refundable_contributions_per_year_array[$contribution_id][$year];
                }else{
                    if(isset($non_refundable_member_deposits)){

                    }else{
                        $non_refundable_member_deposits = 0;
                    }
                }
                $total_owners_equity_per_year_array[$year] += $non_refundable_member_deposits;
            endforeach;
        endforeach;
        $non_refundable_member_deposits = 0;

        foreach($years_array as $year):
            if(isset($share_transfer_fund_per_year_array[$year])){
                $share_transfer_fund = $share_transfer_fund_per_year_array[$year];
            }else{
                if(isset($share_transfer_fund)){

                }else{
                    $share_transfer_fund = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $share_transfer_fund;
        endforeach;
        $share_transfer_fund = 0;

        foreach($years_array as $year):
            if(isset($general_reserves_per_year_array[$year])){
                $general_reserves = $general_reserves_per_year_array[$year];
            }else{
                if(isset($general_reserves)){

                }else{
                    $general_reserves = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $general_reserves;
        endforeach;
        $general_reserves = 0;

        foreach($years_array as $year):
            if(isset($institutional_capital_fund_per_year_array[$year])){
                $institutional_capital = $institutional_capital_fund_per_year_array[$year];
            }else{
                if(isset($institutional_capital_fund_per_year_array[$year])){

                }else{
                    $institutional_capital = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $institutional_capital;
        endforeach;
        $institutional_capital = 0;

        foreach($years_array as $year):
            if(isset($educational_fund_per_year_array[$year])){
                $educational_fund = $educational_fund_per_year_array[$year];
            }else{
                if(isset($educational_fund_per_year_array[$year])){

                }else{
                    $educational_fund = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $educational_fund;
        endforeach;
        $educational_fund = 0;

        foreach($years_array as $year):
            if(isset($retained_earnings_per_year_array[$year])){
                $retained_earnings = $retained_earnings_per_year_array[$year];
            }else{
                if(isset($retained_earnings)){

                }else{
                    $retained_earnings = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $retained_earnings;
        endforeach;
        $retained_earnings = 0;

        // 
        foreach($years_array as $year):

            $balancing_difference_per_year_array[$year] = $total_assets_per_year_array[$year] - ($total_owners_equity_per_year_array[$year] + $total_liabilities_per_year_array[$year]);

        endforeach;

        foreach($years_array as $year):
            $total_assets_per_year_array[$year] -= $balancing_difference_per_year_array[$year];
        endforeach;

        $html = '
            <div class="report invoice-content-2 bordered document-border">
                <div class="row invoice-head">
                    <div class="col-md-7 col-xs-6">
                        <div class="invoice-logo">
                            <img src="';
                                $html.=is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                                $html.='" alt="" class="group-logo image-responsive" /> 
                        </div>
                    </div>
                    <div class="col-md-5 col-xs-6 text-right">
                        <div class="company-address">
                            <span class="bold uppercase">'.$this->group->name.'</span><br/>';
                            $html.=nl2br($this->group->address);
                            $html.='<br/>
                            <span class="bold">'.translate('Telephone').': </span> '.$this->group->phone.'
                            <br/>
                            <span class="bold">'.translate('Email Address').': </span> '.$this->group->email.'
                            <br/>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-center margin-top-10 margin-bottom-10">
                        '.ucfirst($this->group->name).'<br/>
                        '.translate('Statement of financial position as at').' '.$from_year.' '.translate('to').' '.$to_year.'
                    </div>
                </div>
                <hr/>
                <div class="row invoice-body">
                    <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement">
                        <thead>
                            <tr>
                                <th width="30%"></th>';

                                $width = round(70/count($years_array));
                                foreach($years_array as $year):
                                    $html .='
                                    <th class="text-right" width="'.$width.'%">'.$year.'</th>';
                                endforeach;
                            $html .='
                            </tr>
                        </thead>';
                        $html.= '
                            <tbody>
                            <tr>
                                <td><strong>'.translate('Assets').'</strong></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>'.translate('Receivables').'</small></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>'.translate('Other financial assets').'</small></td>';
                                foreach($years_array as $year):
                                    if(isset($total_principal_money_market_investment_out_per_year_array[$year])){
                                        $other_financial_assets = $total_principal_money_market_investment_out_per_year_array[$year];
                                    }else{
                                        $other_financial_assets = 0;
                                    }

                                    if(isset($total_stock_purchases_per_year_array[$year])){
                                        $other_financial_assets += $total_stock_purchases_per_year_array[$year];
                                    }else{
                                        if(isset($other_financial_assets)){

                                        }else{
                                            $other_financial_assets = 0;
                                        }
                                    }
                                    //$total_assets_per_year_array[$year] += $other_financial_assets;
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency($other_financial_assets).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small><a href="'.site_url('group/reports/loans_summary').'">'.translate('Loans to members').'</a></small></td>';
                                foreach($years_array as $year):
                                    if(isset($total_principal_loans_out_per_year_array[$year])){
                                        $loan_to_members = $total_principal_loans_out_per_year_array[$year] - $balancing_difference_per_year_array[$year];
                                    }else{
                                        $loan_to_members = 0;
                                    }

                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency($loan_to_members).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>'.translate('Property, plant and equipment').'</small></td>';
                                foreach($years_array as $year):
                                    if(isset($total_asset_purchase_payments_per_year_array[$year])){
                                        $fixed_asset_value = $total_asset_purchase_payments_per_year_array[$year];
                                    }else{
                                        if(isset($fixed_asset_value)){

                                        }else{
                                            $fixed_asset_value = 0;
                                        }
                                    }       
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency($fixed_asset_value).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>'.translate('Cash/ Cash Equivalent').'</small></td>';
                                foreach($years_array as $year): 
                                    if(isset($account_balances_per_year_array[$year])){
                                        $cash_at_bank = $account_balances_per_year_array[$year];
                                    }else{
                                        if(isset($cash_at_bank)){

                                        }else{
                                            $cash_at_bank = 0;
                                        }
                                    }
                                    $html .= ' 
                                    <td class="text-right"><small>'.(($cash_at_bank >= 0)?number_to_currency($cash_at_bank):"(".number_to_currency(abs($cash_at_bank)).")").'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr>
                                <td><strong>'.translate('Total Assets').'</strong></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency($total_assets_per_year_array[$year]).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>';
                            $html.= '
                                <tbody>
                                <tr>
                                    <td><strong>'.translate('Liabilities').'</strong></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                
                                <tr class="listing">
                                    <td><small>'.translate('Members Deposits').'</small></td>';
                                    foreach($years_array as $year):
                                        if(isset($refundable_contributions_per_year_array[$year])){
                                            $refundable_member_deposits = $refundable_contributions_per_year_array[$year];
                                        }else{
                                            if(isset($refundable_member_deposits)){

                                            }else{
                                                $refundable_member_deposits = 0;
                                            }
                                        }
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($refundable_member_deposits).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>'.translate('Members Interest payable').'</small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>'.translate('Trades and other payables').'</small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>'.translate('Sundry creditors').'</small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>'.translate('Dividends payable').'</small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>'.translate('Interest bearing liability').'</small></td>';
                                    foreach($years_array as $year):
                                        if(isset($total_interest_bearing_liability_per_year_array[$year])){
                                            $bank_loans = $total_interest_bearing_liability_per_year_array[$year];
                                        }else{
                                            $bank_loans = 0;
                                        }
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($bank_loans).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>'.translate('Member loan overpayments').' </small></td>';
                                    foreach($years_array as $year):
                                        if(isset($total_loan_overpayments_per_year_array[$year])){
                                            $loan_overpayment = $total_loan_overpayments_per_year_array[$year];
                                        }else{
                                            if(isset($loan_overpayment)){

                                            }else{
                                                $loan_overpayment = 0;
                                            }
                                        }
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($loan_overpayment).'</small></td>';
                                    endforeach;
                                $html .='                                
                                </tr>
                                <tr>
                                    <td><strong>'.translate('Total Liabilities').'</strong></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($total_liabilities_per_year_array[$year]).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                            ';
                            $html.= '
                                <tbody>
                                <tr>
                                    <td><strong>'.translate('Owners Equity').'</strong></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small><strong>'.translate('Members share capital').'</strong></small></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small></small></td>';
                                    endforeach;
                                $html .='
                                </tr>';
                                foreach($non_refundable_contribution_options as $contribution_id => $name):
                                    $html.='
                                    <tr class="listing">
                                        <td><small>&nbsp;&nbsp;&nbsp;'.$name.'</small></td>';
                                        foreach($years_array as $year):
                                            if(isset($non_refundable_contributions_per_year_array[$contribution_id][$year])){
                                                $non_refundable_member_deposits = $non_refundable_contributions_per_year_array[$contribution_id][$year];
                                            }else{
                                                if(isset($non_refundable_member_deposits)){

                                                }else{
                                                    $non_refundable_member_deposits = 0;
                                                }
                                            }
                                            $html .= ' 
                                            <td class="text-right"><small>'.number_to_currency($non_refundable_member_deposits).'</small></td>';
                                        endforeach;
                                    $html .='
                                    </tr>';
                                endforeach;
                                $html .='
                                <tr class="listing">
                                    <td><small>'.translate('Retained Earnings').'</small></td>';
                                    foreach($years_array as $year):
                                        if(isset($retained_earnings_per_year_array[$year])){
                                            $retained_earnings = $retained_earnings_per_year_array[$year];
                                        }else{
                                            if(isset($retained_earnings)){

                                            }else{
                                                $retained_earnings = 0;
                                            }
                                        }
                                        $html .= ' 
                                        <td class="text-right"><small>'.($retained_earnings < 0?'('.number_to_currency(abs($retained_earnings)).')':number_to_currency($retained_earnings)).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr>
                                    <td><strong>'.translate('Total Owners Equity').'</strong></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.($total_owners_equity_per_year_array[$year] < 0?'('.number_to_currency(abs($total_owners_equity_per_year_array[$year])).')':number_to_currency($total_owners_equity_per_year_array[$year])).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr>
                                    <td></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr>
                                    <td><strong>'.translate('Total Owners Equity & Liabilities').'</strong></td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($total_owners_equity_per_year_array[$year] + $total_liabilities_per_year_array[$year]).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                            ';
                        $html .= '
                        </tbody>
                        ';
                    $html .='
                    </table>';
                    //if(preg_match('/41\.210\.141\.116/',$_SERVER['REMOTE_ADDR'])||preg_match('/127\.0\.0\.1/',$_SERVER['REMOTE_ADDR'])){
                    if($this->input->get('debug')){
                        // $balancing_difference_per_year_array = array_filter($balancing_difference_per_year_array);
                        // print_r($balancing_difference_per_year_array);
                        $html .= '
                        <hr/>
                        <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                            <tbody>
                                <tr>
                                    <td>Difference</td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($balancing_difference_per_year_array[$year]).'</small></td>';
                                    endforeach;
                                $html .= '
                                </tr>
                            </tbody>
                        </table>
                        ';
                    }
                $html.='
        ';
        
        echo $html;
    }

    function get_investment_statement(){
        $from = strtotime($this->input->get_post('from'));
        $to = strtotime($this->input->get_post('to'));

        $years_array = array();

        if($from && $to){
            $from_year = date('Y',$from);
            $to_year = date('Y',$to);
            for($i = $from_year; $i <= $to_year; $i++):
                $years_array[] = $i;
            endfor;
        }else{
            $from_year = date('Y',strtotime('-1 year'));
            $to_year = date('Y');
            $years_array = array(
                $from_year,
                $to_year,
            );
        }

        $non_refundable_contributions = $this->contributions_m->get_group_non_equitable_non_refundable_contributions();

        $refundable_contribution_options = $this->contributions_m->get_group_refundable_contribution_options();

        $non_refundable_contribution_options = $this->contributions_m->get_group_equitable_non_refundable_contribution_options();

        $other_financial_assets_per_year_array = array();
        $total_assets_per_year_array = array();
        $total_liabilities_per_year_array = array();

        foreach($years_array as $year):
            $total_assets_per_year_array[$year] = 0;
            $other_financial_assets_per_year_array[$year] = 0;
            $total_liabilities_per_year_array[$year] = 0;
        endforeach;

        $total_principal_money_market_investment_out_per_year_array = $this->money_market_investments_m->get_group_total_principal_money_market_investment_out_per_year_array($this->group->id);

        $total_asset_purchase_payments_per_year_array = $this->withdrawals_m->get_group_total_asset_purchase_payments_per_year_array($this->group->id);

        $total_stock_purchases_per_year_array = $this->withdrawals_m->get_group_total_stock_purchases_per_year_array($this->group->id);

        $total_principal_loans_out_per_year_array = $this->loans_m->get_group_total_principal_loans_out_per_year_array($this->group->id);

        $total_interest_bearing_liability_per_year_array = $this->bank_loans_m->get_group_total_interest_bearing_liability_per_year_array($this->group->id);

        $account_balances_per_year_array = $this->transaction_statements_m->get_group_account_balances_per_year_array($this->group->id);

        $refundable_contributions_per_year_array = $this->reports_m->get_group_total_contributions_per_year_array($this->group->id,0,0,$refundable_contribution_options);

        $administrative_expense_categories = $this->expense_categories_m->get_group_administrative_expense_category_options();

        $other_expense_categories = $this->expense_categories_m->get_group_other_expense_category_options();

        $non_refundable_contributions_per_year_array = $this->reports_m->get_group_total_contributions_paid_cumulatively_per_contribution_per_year_array($this->group->id);

        $total_loan_interest_paid_per_year_array = $this->reports_m->get_group_total_loan_interest_paid_per_year_array($this->group->id);

        $total_money_market_interest_per_year_array = $this->money_market_investments_m->get_group_total_money_market_interest_per_year_array($this->group->id);

        $total_bank_loans_interest_paid_per_year_array = $this->withdrawals_m->get_group_total_bank_loans_interest_paid_per_year_array();

        $total_contributions_paid_per_contribution_per_year_array = $this->reports_m->get_group_total_contributions_paid_per_contribution_per_year_array();

        $expense_category_totals_per_year_array = $this->withdrawals_m->get_group_expense_category_totals_per_year_array();

        $total_loan_processing_income_per_year_array = $this->deposits_m->get_group_total_loan_processing_income_per_year_array();

        $total_income_per_year_array = $this->deposits_m->get_group_total_income_per_year_array();

        $total_miscellaneous_income_per_year_array = $this->deposits_m->get_group_total_miscellaneous_income_per_year_array();

        $total_fines_per_year_array = $this->reports_m->get_group_total_fines_per_year_array($this->group->id);

        $total_loan_overpayments_per_year_array = $this->reports_m->get_group_total_loan_overpayments_per_year_array($this->group->id);
        
        $total_year_interest_array = array();
        $net_year_interest_income_array = array();
        $net_year_income_array = array();
        $total_year_income_array = array();
        $year_surplus_before_tax_array = array();
        $year_surplus_after_tax_array = array();
        $net_surplus_for_the_year_array = array();
        $total_non_refundable_contributions_paid_per_year_array = array();
        $other_operating_income_per_year_array = array();
        $loan_processing_income_per_year_array = array();
        $group_income_per_year_array = array();
        $group_expenses_per_year_array = array();
        $transfer_to_statutory_reserve_per_year_array = array();
        $retained_earnings_per_year_array = array();
        $total_owners_equity_per_year_array = array();

        $alternative_years_array = array();

        $current_year = date('Y');

        for($i = 1970;$i <= $current_year; $i++):
            $alternative_years_array[] = $i;
        endfor;

        foreach($alternative_years_array as $year):
            $total_year_interest_array[$year] = 0;
            $net_year_interest_income_array[$year] = 0;
            $net_year_income_array[$year] = 0;
            $total_year_income_array[$year] = 0;
            $year_surplus_before_tax_array[$year] = 0;
            $year_surplus_after_tax_array[$year] = 0;
            $net_surplus_for_the_year_array[$year] = 0;
            $total_non_refundable_contributions_paid_per_year_array[$year] = 0;
            $other_operating_income_per_year_array[$year] = 0;
            $loan_processing_income_per_year_array[$year] = 0;
            $group_income_per_year_array[$year] = 0;
            $group_expenses_per_year_array[$year] = 0;
            $transfer_to_statutory_reserve_per_year_array[$year] = 0;
            $retained_earnings_per_year_array[$year] = 0;
            $general_reserves_per_year_array[$year] = 0;
            $share_transfer_fund_per_year_array[$year] = 0;
            $institutional_capital_fund_per_year_array[$year] =  0;
            $educational_fund_per_year_array[$year] =  0;
            $total_owners_equity_per_year_array[$year] =  0;
            $balancing_difference_per_year_array[$year] = 0;
        endforeach;

        foreach($non_refundable_contributions as $contribution):
            foreach($alternative_years_array as $year):
                $total_non_refundable_contributions_paid_per_year_array[$year] += isset($total_contributions_paid_per_contribution_per_year_array[$contribution->id][$year])?$total_contributions_paid_per_contribution_per_year_array[$contribution->id][$year]:0;
            endforeach;
        endforeach;

        foreach($administrative_expense_categories as $administrative_expense_category_id =>  $administrative_expense_category_name):
            foreach($alternative_years_array as $year):
                $group_expenses_per_year_array[$year] += (isset($expense_category_totals_per_year_array[$administrative_expense_category_id][$year])?$expense_category_totals_per_year_array[$administrative_expense_category_id][$year]:0);
            endforeach;
        endforeach;

        foreach($other_expense_categories as $other_expense_category_id =>  $other_expense_category_name):
            foreach($alternative_years_array as $year):
                $group_expenses_per_year_array[$year] += (isset($expense_category_totals_per_year_array[$other_expense_category_id][$year])?$expense_category_totals_per_year_array[$other_expense_category_id][$year]:0);
            endforeach;
        endforeach;

        foreach($alternative_years_array as $year):

            $total_year_interest_array[$year] += 
            (isset($total_loan_interest_paid_per_year_array[$year])?$total_loan_interest_paid_per_year_array[$year]:0);

            $total_year_interest_array[$year] += (isset($total_money_market_interest_per_year_array[$year])?$total_money_market_interest_per_year_array[$year]:0);

            $net_year_interest_income_array[$year] += $total_year_interest_array[$year];

            $net_year_interest_income_array[$year] -= (isset($total_bank_loans_interest_paid_per_year_array[$year])?($total_bank_loans_interest_paid_per_year_array[$year]):0);

            $other_operating_income_per_year_array[$year] += $total_non_refundable_contributions_paid_per_year_array[$year];

            $other_operating_income_per_year_array[$year] += (isset($total_loan_processing_income_per_year_array[$year])?$total_loan_processing_income_per_year_array[$year]:0);

            $other_operating_income_per_year_array[$year] += (isset($total_income_per_year_array[$year])?$total_income_per_year_array[$year]:0);

            $other_operating_income_per_year_array[$year] += (isset($total_miscellaneous_income_per_year_array[$year])?$total_miscellaneous_income_per_year_array[$year]:0);

            $other_operating_income_per_year_array[$year] += (isset($total_fines_per_year_array[$year])?$total_fines_per_year_array[$year]:0);

            $year_surplus_before_tax_array[$year] += $net_year_interest_income_array[$year] + $other_operating_income_per_year_array[$year] - $group_expenses_per_year_array[$year];

            $year_surplus_after_tax_array[$year] = $year_surplus_before_tax_array[$year];

            $general_reserves_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.00));

            $share_transfer_fund_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.00));

            $institutional_capital_fund_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.00));

            $educational_fund_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.00));

            $transfer_to_statutory_reserve_per_year_array[$year] = (($year_surplus_after_tax_array[$year] * 0.00));

            $retained_earnings_per_year_array[$year] = (($year_surplus_before_tax_array[$year] - $transfer_to_statutory_reserve_per_year_array[$year]));

        endforeach;

        foreach($retained_earnings_per_year_array as $key => $value):

            if(isset($retained_earnings_per_year_array[($key - 1)])){
                $retained_earnings_per_year_array[$key] += $retained_earnings_per_year_array[($key - 1)];
            }
            if(isset($general_reserves_per_year_array[($key - 1)])){
                $general_reserves_per_year_array[$key] += $general_reserves_per_year_array[($key - 1)];
            }
            if(isset($share_transfer_fund_per_year_array[($key - 1)])){
                $share_transfer_fund_per_year_array[$key] += $share_transfer_fund_per_year_array[($key - 1)];
            }
            if(isset($institutional_capital_fund_per_year_array[($key - 1)])){
                $institutional_capital_fund_per_year_array[$key] += $institutional_capital_fund_per_year_array[($key - 1)];
            }
            if(isset($educational_fund_per_year_array[($key - 1)])){
                $educational_fund_per_year_array[$key] += $educational_fund_per_year_array[($key - 1)];
            }
        endforeach;


        //Assets addition
        foreach($years_array as $year):
            if(isset($total_principal_money_market_investment_out_per_year_array[$year])){
                $other_financial_assets = $total_principal_money_market_investment_out_per_year_array[$year];
            }else{
                $other_financial_assets = 0;
            }
            $total_assets_per_year_array[$year] += $other_financial_assets;
        endforeach;

        $other_financial_assets = 0;
        $stock_investments = 0;


        foreach($years_array as $year):
            if(isset($total_interest_bearing_liability_per_year_array[$year])){
                $bank_loans = $total_interest_bearing_liability_per_year_array[$year];
            }else{
                if(isset($bank_loans)){

                }else{
                    $bank_loans = 0;
                }
            }
            $total_liabilities_per_year_array[$year] += $bank_loans;
        endforeach;



        



        // print_r($total_principal_loans_out_per_year_array);
        // echo "<hr/>";
        // //print_r($total_liabilities_per_year_array);
        // die;


        $loan_to_members = 0;
        

        foreach($years_array as $year):
            if(isset($total_asset_purchase_payments_per_year_array[$year])){
                $fixed_asset_value = $total_asset_purchase_payments_per_year_array[$year];
            }else{
                if(isset($fixed_asset_value)){

                }else{
                    $fixed_asset_value = 0;
                }
            }
            $total_assets_per_year_array[$year] += $fixed_asset_value;
        endforeach;
        $fixed_asset_value = 0;

        foreach($years_array as $year):
            if(isset($total_stock_purchases_per_year_array[$year])){
                $stock_purchase_value = $total_stock_purchases_per_year_array[$year];
            }else{
                if(isset($stock_purchase_value)){

                }else{
                    $stock_purchase_value = 0;
                }
            }
            $total_assets_per_year_array[$year] += $stock_purchase_value;
        endforeach;

        $stock_purchase_value = 0;

        foreach($years_array as $year):
            
            if(isset($account_balances_per_year_array[$year])){
                $cash_at_bank = $account_balances_per_year_array[$year];
            }else{
                if(isset($cash_at_bank)){

                }else{
                    $cash_at_bank = 0;
                }
            }
            $total_assets_per_year_array[$year] += $cash_at_bank;
        endforeach;

        $cash_at_bank = 0;

        //liabilities
        foreach($years_array as $year):
            if(isset($refundable_contributions_per_year_array[$year])){
                $refundable_member_deposits = $refundable_contributions_per_year_array[$year];
            }else{
                if(isset($refundable_member_deposits)){

                }else{
                    $refundable_member_deposits = 0;
                }
            }
            $total_liabilities_per_year_array[$year] += $refundable_member_deposits;
        endforeach;
        $refundable_member_deposits = 0;

        foreach($years_array as $year):
            if(isset($total_loan_overpayments_per_year_array[$year])){
                $loan_overpayment = $total_loan_overpayments_per_year_array[$year];
            }else{
                if(isset($loan_overpayment)){

                }else{
                    $loan_overpayment = 0;
                }
            }
            $total_liabilities_per_year_array[$year] += $loan_overpayment;
        endforeach;
        $loan_overpayment = 0;

        //equity
        foreach($non_refundable_contribution_options as $contribution_id => $name):
            foreach($years_array as $year):
                if(isset($non_refundable_contributions_per_year_array[$contribution_id][$year])){
                    $non_refundable_member_deposits = $non_refundable_contributions_per_year_array[$contribution_id][$year];
                }else{
                    if(isset($non_refundable_member_deposits)){

                    }else{
                        $non_refundable_member_deposits = 0;
                    }
                }
                $total_owners_equity_per_year_array[$year] += $non_refundable_member_deposits;
            endforeach;
        endforeach;
        $non_refundable_member_deposits = 0;

        foreach($years_array as $year):
            if(isset($share_transfer_fund_per_year_array[$year])){
                $share_transfer_fund = $share_transfer_fund_per_year_array[$year];
            }else{
                if(isset($share_transfer_fund)){

                }else{
                    $share_transfer_fund = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $share_transfer_fund;
        endforeach;
        $share_transfer_fund = 0;

        foreach($years_array as $year):
            if(isset($general_reserves_per_year_array[$year])){
                $general_reserves = $general_reserves_per_year_array[$year];
            }else{
                if(isset($general_reserves)){

                }else{
                    $general_reserves = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $general_reserves;
        endforeach;
        $general_reserves = 0;

        foreach($years_array as $year):
            if(isset($institutional_capital_fund_per_year_array[$year])){
                $institutional_capital = $institutional_capital_fund_per_year_array[$year];
            }else{
                if(isset($institutional_capital_fund_per_year_array[$year])){

                }else{
                    $institutional_capital = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $institutional_capital;
        endforeach;
        $institutional_capital = 0;

        foreach($years_array as $year):
            if(isset($educational_fund_per_year_array[$year])){
                $educational_fund = $educational_fund_per_year_array[$year];
            }else{
                if(isset($educational_fund_per_year_array[$year])){

                }else{
                    $educational_fund = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $educational_fund;
        endforeach;
        $educational_fund = 0;

        foreach($years_array as $year):
            if(isset($retained_earnings_per_year_array[$year])){
                $retained_earnings = $retained_earnings_per_year_array[$year];
            }else{
                if(isset($retained_earnings)){

                }else{
                    $retained_earnings = 0;
                }
            }
            $total_owners_equity_per_year_array[$year] += $retained_earnings;
        endforeach;
        $retained_earnings = 0;

        // 
        // foreach($years_array as $year):

        //     $balancing_difference_per_year_array[$year] = $total_assets_per_year_array[$year] - ($total_owners_equity_per_year_array[$year] + $total_liabilities_per_year_array[$year]);

        // endforeach;

        // foreach($years_array as $year):
        //     $total_assets_per_year_array[$year] -= $balancing_difference_per_year_array[$year];
        // endforeach;

        $html = '
            <div class="report invoice-content-2 bordered document-border">
                <div class="row invoice-head">
                    <div class="col-md-7 col-xs-6">
                        <div class="invoice-logo">
                            <img src="';
                                $html.=is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                                $html.='" alt="" class="group-logo image-responsive" /> 
                        </div>
                    </div>
                    <div class="col-md-5 col-xs-6 text-right">
                        <div class="company-address">
                            <span class="bold uppercase">'.$this->group->name.'</span><br/>';
                            $html.=nl2br($this->group->address);
                            $html.='<br/>
                            <span class="bold">'.translate('Telephone').': </span> '.$this->group->phone.'
                            <br/>
                            <span class="bold">'.translate('Email Address').': </span> '.$this->group->email.'
                            <br/>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-center margin-top-10 margin-bottom-10">
                        '.ucfirst($this->group->name).'<br/>
                        '.translate('Investment Summary as at').' '.$from_year.' '.translate('to').' '.$to_year.'
                    </div>
                </div>
                <hr/>
                <div class="row invoice-body">
                    <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement">
                        <thead>
                            <tr>
                                <th width="30%"></th>';

                                $width = round(70/count($years_array));
                                foreach($years_array as $year):
                                    $html .='
                                    <th class="text-right" width="'.$width.'%">'.$year.'</th>';
                                endforeach;
                            $html .='
                            </tr>
                        </thead>';
                        $html.= '
                            <tbody>
                            <tr>
                                <td><strong>'.translate('Assets').'</strong></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>'.translate('Money Market Investment').'</small></td>';
                                foreach($years_array as $year):
                                    if(isset($total_principal_money_market_investment_out_per_year_array[$year])){
                                        $other_financial_assets = $total_principal_money_market_investment_out_per_year_array[$year];
                                    }else{
                                        $other_financial_assets = 0;
                                    }
                                    $total_assets_per_year_array[$year] += $other_financial_assets;
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency($other_financial_assets).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>'.translate('Stock Investment').'</small></td>';
                                foreach($years_array as $year):
                                    if(isset($total_stock_purchases_per_year_array[$year])){
                                        $stock_investments += $total_stock_purchases_per_year_array[$year];
                                    }else{
                                        if(isset($stock_investments)){

                                        }else{
                                            $stock_investments = 0;
                                        }
                                    }
                                    $total_assets_per_year_array[$year]+= $stock_investments;
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency($stock_investments).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>'.translate('Assets').'('.translate('Property, plant and equipment').')</small></td>';
                                foreach($years_array as $year):
                                    if(isset($total_asset_purchase_payments_per_year_array[$year])){
                                        $fixed_asset_value = $total_asset_purchase_payments_per_year_array[$year];
                                    }else{
                                        if(isset($fixed_asset_value)){

                                        }else{
                                            $fixed_asset_value = 0;
                                        }
                                    }  
                                    $total_assets_per_year_array[$year]+= $fixed_asset_value;     
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency($fixed_asset_value).'</small></td>';
                                endforeach;
                            $html .='
                            </tr
                            <tr>
                                <td><strong>'.translate('Total Assets').'</strong></td>';
                                foreach($years_array as $year):
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency($total_assets_per_year_array[$year]).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>';
                            $html.= '
                            ';
                        $html .= '
                        </tbody>
                        ';
                    $html .='
                    </table>';
                    //if(preg_match('/41\.210\.141\.116/',$_SERVER['REMOTE_ADDR'])||preg_match('/127\.0\.0\.1/',$_SERVER['REMOTE_ADDR'])){
                    if($this->input->get('debug')){
                        // $balancing_difference_per_year_array = array_filter($balancing_difference_per_year_array);
                        // print_r($balancing_difference_per_year_array);
                        $html .= '
                        <hr/>
                        <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                            <tbody>
                                <tr>
                                    <td>Difference</td>';
                                    foreach($years_array as $year):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($balancing_difference_per_year_array[$year]).'</small></td>';
                                    endforeach;
                                $html .= '
                                </tr>
                            </tbody>
                        </table>
                        ';
                    }
                $html.='
        ';
        
        echo $html;
    }

    function get_monthly_balance_sheet(){

        $from_month = strtotime($this->input->get_post('from'));
        $to_month = strtotime($this->input->get_post('to'));

        $display_months_array = array();
        if($from_month && $to_month){
            $display_months_array = generate_months_array($from_month,$to_month);
        }else{
            $from_month = strtotime('-3 months');
            $to_month = strtotime('today');
            $display_months_array = generate_months_array($from_month,$to_month);
        }

        $current_month = date('M Y');
        $start_month = 'Jan 1971';

        $months_array = generate_months_array(strtotime($start_month),strtotime($current_month));

        $non_refundable_contributions = $this->contributions_m->get_group_non_equitable_non_refundable_contributions();

        $refundable_contribution_options = $this->contributions_m->get_group_refundable_contribution_options();

        $non_refundable_contribution_options = $this->contributions_m->get_group_equitable_non_refundable_contribution_options();

        $other_financial_assets_per_month_array = array();
        $total_assets_per_month_array = array();
        $total_liabilities_per_month_array = array();


        $total_principal_money_market_investment_out_per_month_array = $this->money_market_investments_m->get_group_total_principal_money_market_investment_out_per_month_array($this->group->id);

        $total_asset_purchase_payments_per_month_array = $this->withdrawals_m->get_group_total_asset_purchase_payments_per_month_array($this->group->id);

        $total_stock_purchases_per_month_array = $this->stocks_m->get_group_total_stocks_retained_per_month_array($this->group->id);

        $total_principal_loans_out_per_month_array = $this->loans_m->get_group_total_principal_loans_out_per_month_array($this->group->id);

        $total_interest_bearing_liability_per_month_array = $this->bank_loans_m->get_group_total_interest_bearing_liability_per_year_array($this->group->id);

        $account_balances_per_month_array = $this->transaction_statements_m->get_group_account_balances_per_month_array($this->group->id);

        $refundable_contributions_per_month_array = $this->reports_m->get_group_total_contributions_per_month_array($this->group->id,0,0,$refundable_contribution_options,$months_array);

        $administrative_expense_categories = $this->expense_categories_m->get_group_administrative_expense_category_options();

        $other_expense_categories = $this->expense_categories_m->get_group_other_expense_category_options();

        $non_refundable_contributions_per_month_array =  $this->reports_m->get_group_total_contributions_per_month_array($this->group->id,0,0,$non_refundable_contribution_options,$months_array);

        $total_loan_interest_paid_per_month_array = $this->reports_m->get_group_total_loan_interest_paid_per_month_array($this->group->id);

        $total_money_market_interest_per_month_array = $this->money_market_investments_m->get_group_total_money_market_interest_per_month_array($this->group->id);

        $total_stocks_sale_income_per_month_array = $this->stocks_m->get_group_total_stocks_sale_income_per_month_array($this->group->id);

        $total_bank_loans_interest_paid_per_month_array = $this->withdrawals_m->get_group_total_bank_loans_interest_paid_per_month_array();

        $total_stocks_sale_losses_per_month_array = $this->stocks_m->get_group_total_stocks_sale_losses_per_month_array($this->group->id);

        $total_contributions_paid_per_contribution_per_month_array = $this->reports_m->get_group_total_contributions_paid_per_contribution_per_month_array();

        $expense_category_totals_per_month_array = $this->withdrawals_m->get_group_expense_category_totals_per_month_array();

        $total_loan_processing_income_per_month_array = $this->deposits_m->get_group_total_loan_processing_income_per_month_array();

        $total_income_per_month_array = $this->deposits_m->get_group_total_income_per_month_array();

        $total_miscellaneous_income_per_month_array = $this->deposits_m->get_group_total_miscellaneous_income_per_month_array();

        $total_fines_per_month_array = $this->reports_m->get_group_total_fines_per_month_array($this->group->id);

        $total_loan_overpayments_per_month_array = $this->reports_m->get_group_total_loan_overpayments_per_month_array($this->group->id);
        
        $total_month_interest_array = array();
        $net_month_interest_income_array = array();
        $net_month_income_array = array();
        $total_month_income_array = array();
        $month_surplus_before_tax_array = array();
        $month_surplus_after_tax_array = array();
        $net_surplus_for_the_month_array = array();
        $total_non_refundable_contributions_paid_per_month_array = array();
        $other_operating_income_per_month_array = array();
        $loan_processing_income_per_month_array = array();
        $group_income_per_month_array = array();
        $group_expenses_per_month_array = array();
        $transfer_to_statutory_reserve_per_month_array = array();
        $retained_earnings_per_month_array = array();
        $total_owners_equity_per_month_array = array();


        foreach($months_array as $month):
            $total_month_interest_array[$month] = 0;
            $net_month_interest_income_array[$month] = 0;
            $net_month_income_array[$month] = 0;
            $total_month_income_array[$month] = 0;
            $month_surplus_before_tax_array[$month] = 0;
            $month_surplus_after_tax_array[$month] = 0;
            $net_surplus_for_the_month_array[$month] = 0;
            $total_non_refundable_contributions_paid_per_month_array[$month] = 0;
            $other_operating_income_per_month_array[$month] = 0;
            $loan_processing_income_per_month_array[$month] = 0;
            $group_income_per_month_array[$month] = 0;
            $group_expenses_per_month_array[$month] = 0;
            $transfer_to_statutory_reserve_per_month_array[$month] = 0;
            $retained_earnings_per_month_array[$month] = 0;
            $general_reserves_per_month_array[$month] = 0;
            $share_transfer_fund_per_month_array[$month] = 0;
            $institutional_capital_fund_per_month_array[$month] =  0;
            $educational_fund_per_month_array[$month] =  0;
            $total_owners_equity_per_month_array[$month] =  0;
            $balancing_difference_per_month_array[$month] = 0;
            $total_assets_per_month_array[$month] = 0;
            $other_financial_assets_per_month_array[$month] = 0;
            $total_liabilities_per_month_array[$month] = 0;
        endforeach;

        foreach($non_refundable_contributions as $contribution):
            foreach($months_array as $month):
                $total_non_refundable_contributions_paid_per_month_array[$month] += isset($total_contributions_paid_per_contribution_per_month_array[$contribution->id][$month])?$total_contributions_paid_per_contribution_per_month_array[$contribution->id][$month]:0;
            endforeach;
        endforeach;

        foreach($administrative_expense_categories as $administrative_expense_category_id =>  $administrative_expense_category_name):
            foreach($months_array as $month):
                $group_expenses_per_month_array[$month] += (isset($expense_category_totals_per_month_array[$administrative_expense_category_id][$month])?$expense_category_totals_per_month_array[$administrative_expense_category_id][$month]:0);
            endforeach;
        endforeach;

        foreach($other_expense_categories as $other_expense_category_id =>  $other_expense_category_name):
            foreach($months_array as $month):
                $group_expenses_per_month_array[$month] += (isset($expense_category_totals_per_month_array[$other_expense_category_id][$month])?$expense_category_totals_per_month_array[$other_expense_category_id][$month]:0);
            endforeach;
        endforeach;


        foreach($months_array as $month):

            $total_month_interest_array[$month] += (isset($total_loan_interest_paid_per_month_array[$month])?$total_loan_interest_paid_per_month_array[$month]:0);

            $total_month_interest_array[$month] += (isset($total_money_market_interest_per_month_array[$month])?$total_money_market_interest_per_month_array[$month]:0);

            $total_month_interest_array[$month] += (isset($total_stocks_sale_income_per_month_array[$month])?$total_stocks_sale_income_per_month_array[$month]:0);

            $net_month_interest_income_array[$month] += $total_month_interest_array[$month];

            $net_month_interest_income_array[$month] -= (isset($total_bank_loans_interest_paid_per_month_array[$month])?($total_bank_loans_interest_paid_per_month_array[$month]):0);

            $net_month_interest_income_array[$month] -= (isset($total_stocks_sale_losses_per_month_array[$month])?($total_stocks_sale_losses_per_month_array[$month]):0);

            $other_operating_income_per_month_array[$month] += $total_non_refundable_contributions_paid_per_month_array[$month];

            $other_operating_income_per_month_array[$month] += (isset($total_loan_processing_income_per_month_array[$month])?$total_loan_processing_income_per_month_array[$month]:0);

            $other_operating_income_per_month_array[$month] += (isset($total_income_per_month_array[$month])?$total_income_per_month_array[$month]:0);

            $other_operating_income_per_month_array[$month] += (isset($total_miscellaneous_income_per_month_array[$month])?$total_miscellaneous_income_per_month_array[$month]:0);

            $other_operating_income_per_month_array[$month] += (isset($total_fines_per_month_array[$month])?$total_fines_per_month_array[$month]:0);

            $month_surplus_before_tax_array[$month] += $net_month_interest_income_array[$month] + $other_operating_income_per_month_array[$month] - $group_expenses_per_month_array[$month];

            $month_surplus_after_tax_array[$month] = $month_surplus_before_tax_array[$month];

            $general_reserves_per_month_array[$month] = (($month_surplus_after_tax_array[$month] * 0.00));

            $share_transfer_fund_per_month_array[$month] = (($month_surplus_after_tax_array[$month] * 0.00));

            $institutional_capital_fund_per_month_array[$month] = (($month_surplus_after_tax_array[$month] * 0.00));

            $educational_fund_per_month_array[$month] = (($month_surplus_after_tax_array[$month] * 0.00));

            $transfer_to_statutory_reserve_per_month_array[$month] = (($month_surplus_after_tax_array[$month] * 0.00));

            $retained_earnings_per_month_array[$month] = (($month_surplus_before_tax_array[$month] - $transfer_to_statutory_reserve_per_month_array[$month]));
        endforeach;
        
        foreach($months_array as $month):

            $previous_month = date('M Y',strtotime('-1 month',strtotime($month)));

            if(isset($retained_earnings_per_month_array[($previous_month)])){
                $retained_earnings_per_month_array[$month] += $retained_earnings_per_month_array[($previous_month)];
            }
            if(isset($general_reserves_per_month_array[($previous_month)])){
                $general_reserves_per_month_array[$month] += $general_reserves_per_month_array[($previous_month)];
            }
            if(isset($share_transfer_fund_per_month_array[($previous_month)])){
                $share_transfer_fund_per_month_array[$month] += $share_transfer_fund_per_month_array[($previous_month)];
            }
            if(isset($institutional_capital_fund_per_month_array[($previous_month)])){
                $institutional_capital_fund_per_month_array[$month] += $institutional_capital_fund_per_month_array[($previous_month)];
            }
            if(isset($educational_fund_per_month_array[($previous_month)])){
                $educational_fund_per_month_array[$month] += $educational_fund_per_month_array[($previous_month)];
            }

        endforeach;

        //Assets addition
        foreach($months_array as $month):
            if(isset($total_principal_money_market_investment_out_per_month_array[$month])){
                $other_financial_assets = $total_principal_money_market_investment_out_per_month_array[$month];
            }else{
                $other_financial_assets = 0;
            }
            $total_assets_per_month_array[$month] += $other_financial_assets;
        endforeach;
        $other_financial_assets = 0;

        foreach($months_array as $month):
            if(isset($total_principal_loans_out_per_month_array[$month])){
                $loan_to_members = $total_principal_loans_out_per_month_array[$month];
            }else{
                if(isset($loan_to_members)){

                }else{
                    $loan_to_members = 0;
                }
            }
            $total_assets_per_month_array[$month] += $loan_to_members;
        endforeach;

        foreach($months_array as $month):
            if(isset($total_interest_bearing_liability_per_month_array[$month])){
                $bank_loans = $total_interest_bearing_liability_per_month_array[$month];
            }else{
                if(isset($bank_loans)){

                }else{
                    $bank_loans = 0;
                }
            }
            $total_liabilities_per_month_array[$month] += $bank_loans;
        endforeach;
        $loan_to_members = 0;
        
        foreach($months_array as $month):
            if(isset($total_asset_purchase_payments_per_month_array[$month])){
                $fixed_asset_value = $total_asset_purchase_payments_per_month_array[$month];
            }else{
                if(isset($fixed_asset_value)){

                }else{
                    $fixed_asset_value = 0;
                }
            }
            $total_assets_per_month_array[$month] += $fixed_asset_value;
        endforeach;
        $fixed_asset_value = 0;

        foreach($months_array as $month):
            if(isset($total_stock_purchases_per_month_array[$month])){
                $stock_purchase_value = $total_stock_purchases_per_month_array[$month];
            }else{
                if(isset($stock_purchase_value)){

                }else{
                    $stock_purchase_value = 0;
                }
            }
            $total_assets_per_month_array[$month] += $stock_purchase_value;
        endforeach;

        $stock_purchase_value = 0;

        foreach($months_array as $month):
            
            if(isset($account_balances_per_month_array[$month])){
                $cash_at_bank = $account_balances_per_month_array[$month];
            }else{
                if(isset($cash_at_bank)){

                }else{
                    $cash_at_bank = 0;
                }
            }
            $total_assets_per_month_array[$month] += $cash_at_bank;
        endforeach;

        $cash_at_bank = 0;

        //liabilities
        foreach($months_array as $month):
            if(isset($refundable_contributions_per_month_array[$month])){
                $refundable_member_deposits = $refundable_contributions_per_month_array[$month];
            }else{
                if(isset($refundable_member_deposits)){

                }else{
                    $refundable_member_deposits = 0;
                }
            }
            $total_liabilities_per_month_array[$month] += $refundable_member_deposits;
        endforeach;
        $refundable_member_deposits = 0;

        foreach($months_array as $month):
            if(isset($total_loan_overpayments_per_month_array[$month])){
                $loan_overpayment = $total_loan_overpayments_per_month_array[$month];
            }else{
                if(isset($loan_overpayment)){

                }else{
                    $loan_overpayment = 0;
                }
            }
            $total_liabilities_per_month_array[$month] += $loan_overpayment;
        endforeach;
        $loan_overpayment = 0;

        foreach($non_refundable_contribution_options as $contribution_id => $name):
            foreach($months_array as $month):
                if(isset($non_refundable_contributions_per_month_array[$contribution_id][$month])){
                    $non_refundable_member_deposits = $non_refundable_contributions_per_month_array[$contribution_id][$month];
                }else{
                    if(isset($non_refundable_member_deposits)){

                    }else{
                        $non_refundable_member_deposits = 0;
                    }
                }
                $total_owners_equity_per_month_array[$month] += $non_refundable_member_deposits;
            endforeach;
        endforeach;
        $non_refundable_member_deposits = 0;

        foreach($months_array as $month):
            if(isset($share_transfer_fund_per_month_array[$month])){
                $share_transfer_fund = $share_transfer_fund_per_month_array[$month];
            }else{
                if(isset($share_transfer_fund)){

                }else{
                    $share_transfer_fund = 0;
                }
            }
            $total_owners_equity_per_month_array[$month] += $share_transfer_fund;
        endforeach;
        $share_transfer_fund = 0;

        foreach($months_array as $month):
            if(isset($general_reserves_per_month_array[$month])){
                $general_reserves = $general_reserves_per_month_array[$month];
            }else{
                if(isset($general_reserves)){

                }else{
                    $general_reserves = 0;
                }
            }
            $total_owners_equity_per_month_array[$month] += $general_reserves;
        endforeach;
        $general_reserves = 0;

        foreach($months_array as $month):
            if(isset($institutional_capital_fund_per_month_array[$month])){
                $institutional_capital = $institutional_capital_fund_per_month_array[$month];
            }else{
                if(isset($institutional_capital_fund_per_month_array[$month])){

                }else{
                    $institutional_capital = 0;
                }
            }
            $total_owners_equity_per_month_array[$month] += $institutional_capital;
        endforeach;
        $institutional_capital = 0;

        foreach($months_array as $month):
            if(isset($educational_fund_per_month_array[$month])){
                $educational_fund = $educational_fund_per_month_array[$month];
            }else{
                if(isset($educational_fund_per_month_array[$month])){

                }else{
                    $educational_fund = 0;
                }
            }
            $total_owners_equity_per_month_array[$month] += $educational_fund;
        endforeach;
        $educational_fund = 0;

        foreach($months_array as $month):
            if(isset($retained_earnings_per_month_array[$month])){
                $retained_earnings = $retained_earnings_per_month_array[$month];
            }else{
                if(isset($retained_earnings)){

                }else{
                    $retained_earnings = 0;
                }
            }
            $total_owners_equity_per_month_array[$month] += $retained_earnings;
        endforeach;
        $retained_earnings = 0;

        foreach($months_array as $month):
            $total_assets_per_month_array[$month] -= $balancing_difference_per_month_array[$month];
        endforeach;

        $html = '
            <div class="report invoice-content-2 bordered document-border">
                <div class="row invoice-head">
                    <div class="col-md-7 col-xs-6">
                        <div class="invoice-logo">
                            <img src="';
                                $html.=is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                                $html.='" alt="" class="group-logo image-responsive" /> 
                        </div>
                    </div>
                    <div class="col-md-5 col-xs-6 text-right">
                        <div class="company-address">
                            <span class="bold uppercase">'.$this->group->name.'</span><br/>';
                            $html.=nl2br($this->group->address);
                            $html.='<br/>
                            <span class="bold">Telephone: </span> '.$this->group->phone.'
                            <br/>
                            <span class="bold">E-mail Address: </span> '.$this->group->email.'
                            <br/>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-center margin-top-10 margin-bottom-10">
                        '.ucfirst($this->group->name).'<br/>
                        Statement of financial position as at '.date("M Y",$from_month).' to '.date("M Y",$to_month).'
                    </div>
                </div>
                <hr/>
                <div class="row invoice-body">
                    <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement" id="m_table_2">
                        <thead>
                            <tr>
                                <th width="30%"></th>';

                                $width = round(70/count($display_months_array));
                                foreach($display_months_array as $month):
                                    $html .='
                                    <th class="text-right" width="'.$width.'%">'.$month.'</th>';
                                endforeach;
                            $html .='
                            </tr>
                        </thead>';
                        $html.= '
                            <tbody>
                            <tr>
                                <td><strong>Assets</strong></td>';
                                foreach($display_months_array as $month):
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Receivables</small></td>';
                                foreach($display_months_array as $month):
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Other financial assets</small></td>';
                                foreach($display_months_array as $month):
                                    if(isset($total_principal_money_market_investment_out_per_month_array[$month])){
                                        $other_financial_assets = $total_principal_money_market_investment_out_per_month_array[$month];
                                    }else{
                                        $other_financial_assets = 0;
                                    }

                                    if(isset($total_stock_purchases_per_month_array[$month])){
                                        $other_financial_assets += $total_stock_purchases_per_month_array[$month];
                                    }else{
                                        if(isset($other_financial_assets)){

                                        }else{
                                            $other_financial_assets = 0;
                                        }
                                    }
                                    //$total_assets_per_year_array[$year] += $other_financial_assets;
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency($other_financial_assets).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small><a href="'.site_url('group/reports/loans_summary').'">Loans to members</a></small></td>';
                                foreach($display_months_array as $month):
                                    if(isset($total_principal_loans_out_per_month_array[$month])){
                                        $loan_to_members = $total_principal_loans_out_per_month_array[$month] - $balancing_difference_per_month_array[$month];
                                    }else{
                                        $loan_to_members = 0;
                                    }
                                    $html .= ' 
                                    <td class="text-right"><small>'.($loan_to_members<0?'('.number_to_currency(abs($loan_to_members)).')':number_to_currency($loan_to_members)).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Property, plant and equipment</small></td>';
                                foreach($display_months_array as $month):
                                    if(isset($total_asset_purchase_payments_per_month_array[$month])){
                                        $fixed_asset_value = $total_asset_purchase_payments_per_month_array[$month];
                                    }else{
                                        if(isset($fixed_asset_value)){

                                        }else{
                                            $fixed_asset_value = 0;
                                        }
                                    }       
                                    $html .= ' 
                                    <td class="text-right"><small>'.number_to_currency($fixed_asset_value).'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr class="listing">
                                <td><small>Cash/ Cash Equivalent</small></td>';
                                foreach($display_months_array as $month): 
                                    if(isset($account_balances_per_month_array[$month])){
                                        $cash_at_bank = $account_balances_per_month_array[$month];
                                    }else{
                                        if(isset($cash_at_bank)){

                                        }else{
                                            $cash_at_bank = 0;
                                        }
                                    }
                                    $html .= ' 
                                    <td class="text-right"><small>'.(($cash_at_bank >= 0)?number_to_currency($cash_at_bank):"(".number_to_currency(abs($cash_at_bank)).")").'</small></td>';
                                endforeach;
                            $html .='
                            </tr>
                            <tr>
                                <td><strong>Total Assets</strong></td>';
                                foreach($display_months_array as $month):
                                    $html .= ' 
                                    <td class=" bold theme-font text-right">'.number_to_currency($total_assets_per_month_array[$month]).'</td>';
                                endforeach;
                            $html .='
                            </tr>';
                            $html.= '
                                <tbody>
                                <tr>
                                    <td><strong>Liabilities</strong></td>';
                                    foreach($display_months_array as $month):
                                        $html .= ' 
                                        <td class="text-right"></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                
                                <tr class="listing">
                                    <td><small>Members\' deposits</small></td>';
                                    foreach($display_months_array as $month):
                                        if(isset($refundable_contributions_per_month_array[$month])){
                                            $refundable_member_deposits = $refundable_contributions_per_month_array[$month];
                                        }else{
                                            if(isset($refundable_member_deposits)){

                                            }else{
                                                $refundable_member_deposits = 0;
                                            }
                                        }
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($refundable_member_deposits).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>Members\' Interest payable</small></td>';
                                    foreach($display_months_array as $month):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>Trades and other payables</small></td>';
                                    foreach($display_months_array as $month):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>Sundry creditors</small></td>';
                                    foreach($display_months_array as $month):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>Dividends payable</small></td>';
                                    foreach($display_months_array as $month):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency(0).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>Interest bearing liability</small></td>';
                                    foreach($display_months_array as $month):
                                        if(isset($total_interest_bearing_liability_per_month_array[$month])){
                                            $bank_loans = $total_interest_bearing_liability_per_month_array[$month];
                                        }else{
                                            $bank_loans = 0;
                                        }
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($bank_loans).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small>Member loan overpayments </small></td>';
                                    foreach($display_months_array as $month):
                                        if(isset($total_loan_overpayments_per_month_array[$month])){
                                            $loan_overpayment = $total_loan_overpayments_per_month_array[$month];
                                        }else{
                                            if(isset($loan_overpayment)){

                                            }else{
                                                $loan_overpayment = 0;
                                            }
                                        }
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($loan_overpayment).'</small></td>';
                                    endforeach;
                                $html .='                                
                                </tr>
                                <tr>
                                    <td><strong>Total Liabilities</strong></td>';
                                    foreach($display_months_array as $month):
                                        $html .= ' 
                                        <td class=" bold theme-font text-right">'.number_to_currency($total_liabilities_per_month_array[$month]).'</td>';
                                    endforeach;
                                $html .='
                                </tr>
                            ';
                            $html.= '
                                <tbody>
                                <tr>
                                    <td><strong>Owners\' Equity</strong></td>';
                                    foreach($display_months_array as $month):
                                        $html .= ' 
                                        <td class="text-right"></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr class="listing">
                                    <td><small><strong>Members\' share capital</strong></small></td>';
                                    foreach($display_months_array as $month):
                                        $html .= ' 
                                        <td class="text-right"><small></small></td>';
                                    endforeach;
                                $html .='
                                </tr>';
                                foreach($non_refundable_contribution_options as $contribution_id => $name):
                                    $html.='
                                    <tr class="listing">
                                        <td><small>&nbsp;&nbsp;&nbsp;'.$name.'</small></td>';
                                        foreach($display_months_array as $month):
                                            if(isset($non_refundable_contributions_per_month_array[$contribution_id][$month])){
                                                $non_refundable_member_deposits = $non_refundable_contributions_per_month_array[$contribution_id][$month];
                                            }else{
                                                if(isset($non_refundable_member_deposits)){

                                                }else{
                                                    $non_refundable_member_deposits = 0;
                                                }
                                            }
                                            $html .= ' 
                                            <td class="text-right"><small>'.number_to_currency($non_refundable_member_deposits).'</small></td>';
                                        endforeach;
                                    $html .='
                                    </tr>';
                                endforeach;
                                $html .='
                                <tr class="listing">
                                    <td><small>Retained Earnings</small></td>';
                                    foreach($display_months_array as $month):
                                        if(isset($retained_earnings_per_month_array[$month])){
                                            $retained_earnings = $retained_earnings_per_month_array[$month];
                                        }else{
                                            if(isset($retained_earnings)){

                                            }else{
                                                $retained_earnings = 0;
                                            }
                                        }
                                        $html .= ' 
                                        <td class="text-right"><small>'.($retained_earnings < 0?'('.number_to_currency(abs($retained_earnings)).')':number_to_currency($retained_earnings)).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr>
                                    <td><strong>Total Owners\' Equity</strong></td>';
                                    foreach($display_months_array as $month):
                                        $html .= ' 
                                        <td class="text-right"><small>'.($total_owners_equity_per_month_array[$month] < 0?'('.number_to_currency(abs($total_owners_equity_per_month_array[$month])).')':number_to_currency($total_owners_equity_per_month_array[$month])).'</small></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr>
                                    <td></td>';
                                    foreach($display_months_array as $month):
                                        $html .= ' 
                                        <td class="text-right"></td>';
                                    endforeach;
                                $html .='
                                </tr>
                                <tr>
                                    <td><strong>Total Owners\' Equity & Liabilities</strong></td>';
                                    foreach($display_months_array as $month):
                                        $html .= ' 
                                        <td class=" bold theme-font text-right">'.number_to_currency($total_owners_equity_per_month_array[$month] + $total_liabilities_per_month_array[$month]).'</td>';
                                    endforeach;
                                $html .='
                                </tr>
                            ';
                        $html .= '
                        </tbody>
                        ';
                    $html .='
                    </table>';
                    //if(preg_match('/41\.210\.141\.116/',$_SERVER['REMOTE_ADDR'])||preg_match('/127\.0\.0\.1/',$_SERVER['REMOTE_ADDR'])){
                    if($this->input->get('debug')){
                        // $balancing_difference_per_year_array = array_filter($balancing_difference_per_year_array);
                        // print_r($balancing_difference_per_year_array);
                        $html .= '
                        <hr/>
                        <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                            <tbody>
                                <tr>
                                    <td>Difference</td>';
                                    foreach($display_months_array as $month):
                                        $html .= ' 
                                        <td class="text-right"><small>'.number_to_currency($balancing_difference_per_month_array[$month]).'</small></td>';
                                    endforeach;
                                $html .= '
                                </tr>
                            </tbody>
                        </table>
                        ';
                    }
                $html.='
            </div>
        ';
        
        echo $html;
    }

    function get_eazzyclub_loans_summary(){
       
        $member_ids = $this->input->get_post('member_ids')?:0;
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-10 year');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $this->group_member_options = $this->members_m->get_group_members();
         
        $filter_parameters = array(
            'member_id' => $member_ids?:'',
            'is_fully_paid' => $this->input->get('is_fully_paid')?:'',
            'from' => $from,
            'to' => $to,
        );
        $loans = $this->loans_m->get_group_loans($filter_parameters,$this->group->id);
        $loan_amounts_paid_per_loan_array = $this->reports_m->get_group_loan_amounts_paid_per_loan_array($this->group->id,$from,$to);
        $loan_amounts_payable_per_loan_array = $this->reports_m->get_group_loan_amounts_payable_per_loan_array($this->group->id,$from,$to);
        $principal_amounts_paid_per_loan_array = $this->reports_m->get_group_principal_amounts_paid_per_loan_array($this->group->id,$from,$to);
        $interest_amounts_paid_per_loan_array = $this->reports_m->get_group_interest_amounts_paid_per_loan_array($this->group->id,$from,$to);
        $loan_balances_per_loan_array = $this->reports_m->get_group_loan_balances_per_loan_array($this->group->id,$from,$to);
        if($loans){
            $html = '
            <table class="table table-condensed table-striped table-hover table-header-fixed table-searchable">
                <thead>
                    <tr>
                        <th width="8px">#</th>
                        <th>Member Name</th>
                        <th>Disbursement Date</th>
                        <th class="text-right">Amount Borrowed</th>
                        <th class="text-right">Amount Payable</th>
                        <th class="text-right">Amount Paid</th>
                        <th class="text-right">Principal Paid</th>
                        <th class="text-right">Interest Paid</th>
                        <th class="text-right">Balance</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
            ';
            $i = 1;
            $total_amount_borrowed = 0;
            $total_amount_payable = 0;
            $total_amount_paid = 0;
            $total_principal_paid = 0;
            $total_interest_paid = 0;
            foreach($loans as $loan):
                $total_amount_borrowed += $loan->loan_amount;
                $total_amount_payable += (isset($loan_amounts_payable_per_loan_array[$loan->id])?($loan_amounts_payable_per_loan_array[$loan->id]):(0));
                $total_amount_paid += (isset($loan_amounts_paid_per_loan_array[$loan->id])?($loan_amounts_paid_per_loan_array[$loan->id]):(0));
                $total_principal_paid += (isset($principal_amounts_paid_per_loan_array[$loan->id])?($principal_amounts_paid_per_loan_array[$loan->id]):(0));
                $total_interest_paid += (isset($interest_amounts_paid_per_loan_array[$loan->id])?($interest_amounts_paid_per_loan_array[$loan->id]):(0));
                $html .= '
                <tr>
                    <td>'.($i).'.</td>
                    <td><a href="'.site_url('group/loans/loan_statement/'.$loan->id).'">'.$this->group_member_options[$loan->member_id].'</a></td>
                    <td>'.timestamp_to_date($loan->disbursement_date).'</td>
                    <td class="text-right">'.number_to_currency($loan->loan_amount).'</td>
                    <td class="text-right">'.(isset($loan_amounts_payable_per_loan_array[$loan->id])?number_to_currency($loan_amounts_payable_per_loan_array[$loan->id]):number_to_currency(0)).'</td>
                    <td class="text-right">'.(isset($loan_amounts_paid_per_loan_array[$loan->id])?number_to_currency($loan_amounts_paid_per_loan_array[$loan->id]):number_to_currency(0)).'</td>
                    <td class="text-right">'.(isset($principal_amounts_paid_per_loan_array[$loan->id])?number_to_currency($principal_amounts_paid_per_loan_array[$loan->id]):number_to_currency(0)).'</td>
                    <td class="text-right">'.(isset($interest_amounts_paid_per_loan_array[$loan->id])?number_to_currency($interest_amounts_paid_per_loan_array[$loan->id]):number_to_currency(0)).'</td>
                    <td class="text-right">'.(isset($loan_balances_per_loan_array[$loan->id])?number_to_currency($loan_balances_per_loan_array[$loan->id]):number_to_currency(0)).'</td>
                    <td>';

                    $html .= '
                    <a href="'.site_url('group/loans/view_installments/'.$loan->id).'" class="btn btn-xs default">
                        <i class="icon-eye"></i> Amortization &nbsp;&nbsp; 
                    </a>
                    <a href="'.site_url('group/loans/loan_statement/'.$loan->id).'" class="btn btn-xs btn-primary">
                        <i class="icon-book"></i> Statement &nbsp;&nbsp; 
                    </a>';

                $html .= '
                    </td>
                </tr>
                ';
                $i++;
            endforeach;
            $html .= '
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th>Totals</th>
                        <th></th>
                        <th class="text-right">'.number_to_currency($total_amount_borrowed).'</th>
                        <th class="text-right">'.number_to_currency($total_amount_payable).'</th>
                        <th class="text-right">'.number_to_currency($total_amount_paid).'</th>
                        <th class="text-right">'.number_to_currency($total_principal_paid).'</th>
                        <th class="text-right">'.number_to_currency($total_interest_paid).'</th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            ';
        }else{
            $html = '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert"">
                    <strong>Information!</strong> No loans summary to display
                </div>
            ';
        }
        echo $html;
    }


    function get_member_deposit_distribution(){
        $response = array();
        $months_ago = 6;
        $from = strtotime("first day of -".$months_ago." months", time());
        $contributions = $this->contributions_m->get_group_contributions_display_reports();
        $contribution_list = '';
        foreach ($contributions as $contribution) {
            if($contribution_list){
                $contribution_list.=','.$contribution->id;
            }else{
                $contribution_list=$contribution->id;
            }
        }
        $monthly_payments = $this->statements_m->get_group_member_total_paid_by_contribution_array_monthly($this->member->id,$this->group->id,$contribution_list,$from);
        $monthly_loan_repayments = $this->statements_m->get_group_member_total_paid_loan_payment_array_monthly($this->member->id,$this->group->id,$from);
        $years = array();
        foreach ($monthly_payments as $key=>$monthly_payment1) {
            foreach ($monthly_payment1 as $id => $amount) {
                $years[$key][$id] = $amount;
            }
        }
        $share_payments = array();
        $savings_payments= array();
        $other_payments = array();
        $months = array();
        $loan_payments = array();
        $years_array = array();
        for($i=0;$i<$months_ago;$i++){
            $share_payments[$i] = 0;
            $savings_payments[$i]= 0;
            $other_payments[$i] = 0;
            $from = strtotime('last day of +1 month',$from);
            if(array_key_exists(date('Ym',$from),$years)){
                $month = date('Ym',$from);
                $contribution_data = $years[$month];
                foreach ($contributions as $contribution) {
                    if($contribution->category==1){
                        if(array_key_exists($contribution->id, $contribution_data)){
                            $share_payments[$i]+= $contribution_data[$contribution->id];
                        }else{
                            $share_payments[$i]+= 0;
                        }
                    }else if($contribution->category==2){
                        if(array_key_exists($contribution->id, $contribution_data)){
                            $savings_payments[$i]+= $contribution_data[$contribution->id];
                        }else{
                            $savings_payments[$i]+=0;
                        }
                    }else if($contribution->category == 5){
                        if(array_key_exists($contribution->id, $contribution_data)){
                            $other_payments[$i]+= $contribution_data[$contribution->id];
                        }else{
                            $other_payments[$i]+= 0;
                        }
                    }
                }
            }else{
                // $share_payments[$i] = 0;
                // $savings_payments[$i] = 0;
                // $other_payments[$i] = 0;
            }

            

            if(array_key_exists(date('Ym',$from),$monthly_loan_repayments)){
                $loan_payments[$i]= $monthly_loan_repayments[date('Ym',$from)];
            }else{
                $loan_payments[$i]= 0;
            }
            
            $months[$i] = date('M \'y',$from);
        }
        $response = array(
            'share_payments' => $share_payments,
            'savings_payments' => $savings_payments,
            'other_payments' => $other_payments,
            'loan_payment' => $loan_payments,
            'months' => $months,
        );
        echo json_encode($response);
    }

    function get_member_deposit_summary(){
        $response = array();
        $contributions = $this->contributions_m->get_group_contributions_display_reports();
        $contribution_list = '';
        foreach ($contributions as $contribution) {
            if($contribution_list){
                $contribution_list.=','.$contribution->id;
            }else{
                $contribution_list=$contribution->id;
            }
        }
        $group_member_total_contribution_paid = $this->statements_m->get_group_member_total_paid_by_contribution_array($this->member->id,$this->group->id,$contribution_list);
        $share_contributions = '0';
        $savings_contributions = '0';
        $other_contributions ='0';
        $total_payments = 0;
        if($group_member_total_contribution_paid){
            foreach ($contributions as $contribution) {
                if($contribution->category == 1){
                    $share_contributions+=isset($group_member_total_contribution_paid[$contribution->id])?$group_member_total_contribution_paid[$contribution->id]:0;
                }else if($contribution->category == 2){
                    $savings_contributions+=isset($group_member_total_contribution_paid[$contribution->id])?$group_member_total_contribution_paid[$contribution->id]:0;
                }else if($contribution->category == 5){
                    $other_contributions+=isset($group_member_total_contribution_paid[$contribution->id])?$group_member_total_contribution_paid[$contribution->id]:0;
                }
            }
        }
        $response = array(
            'categories' => array(
                'Share Account',
                'Savings Account',
                'Projects'
            ),
            'amounts' => array(
                $share_contributions,
                $savings_contributions,
                $other_contributions
            )
        );
        echo json_encode($response);
    }

    function get_member_monthly_loan_repayments(){
        $response = array();
        $months_ago = 12;
        $from = strtotime("first day of -".$months_ago." months", time());
        $monthly_payments = $this->deposits_m->member_monthly_loan_deposits($this->group->id,$this->member->id,$from);
        $arr = array();
        foreach ($monthly_payments as $monthly_payment) {
            $arr[$monthly_payment->year] = $monthly_payment->total_amount;
        }
        $months = array();
        $amounts = array();
        for($i=0;$i<=$months_ago;$i++){
            if(array_key_exists(date('Ym', $from),$arr)){
                $months[] = date('M \'y',$from);
                $amounts[] = $arr[date('Ym', $from)];
            }else{
                $months[] = date('M \'y',$from);
                $amounts[] = 0;
            }
            $from = strtotime('last day of +1 month',$from);
        }
        $response = array(
            'months' => $months,
            'amounts' => $amounts,
        );
        echo json_encode($response);
    }

    function get_per_member_total_loan_amount(){
         echo '
            <table class="table m-table m-table--head-separator-primary">
                <thead class="thead-inverse">
                    <tr>
                        <th>
                            #
                        </th>
                        <th>
                            '.translate('Expense Category').'
                        </th>
                        <th class="text-right">
                            '.translate('Paid').' ('.$this->group_currency.')
                        </th>
                    </tr>
                </thead>
                <tbody>';
                    
                        $count = 1; 
                        foreach($this->group_member_options as $member_id => $member_name): 
                        echo '
                        <tr>
                            <td>'.($count++).'</td>
                            <td>'.$member_name.'</td>
                            <td  class="text-right">'.number_to_currency(rand(1000,10000)).'</td>
                        </tr>';
                    endforeach;
                    echo '
                </tbody>
            </table>
            <hr/>';
           ;
           $expenses_available = 'true';
        echo '<input class="expen_available" type="hidden" value="'.$expenses_available.'"/>';
    }

    function get_members_loan_amount_summary(){
        if(array_key_exists($this->member->id, $this->member_role_holder_options) || $this->member->is_admin){
            $count = 1; 
            $categories =  array();
            $amounts = array();
            $i=0;
            $other_amounts = 0 ;
            $posts = $this->loans_m->get_member_loans_distribution($this->group->id);
            $other_members = 0;
            foreach ($posts as $member_id=>$amount) {
                if($i>5){
                    $other_members+=$amount;
                }else{
                    $categories[] = isset($this->group_member_options[$member_id])?$this->group_member_options[$member_id]:'Member_'.$i;
                    $amounts[] = $amount;
                }
                $i++;
            }
            if($other_members){
                $categories[] = 'Other Members';
                $amounts[] = $other_members;
            }
            $response = array(
                'categories' => $categories,
                'amount' => $amounts,
            );
            echo json_encode($response);
        }else{
           $response = array(
                'status' => 0,
                'refer' => site_url('authentication'),
                'message' => 'You dont have rights to access this panel.',
            );
            echo json_encode($response);die; 
        }
    }

    function get_monthly_loan_deposits_vs_withdrawals(){
        if(array_key_exists($this->member->id, $this->member_role_holder_options) || $this->member->is_admin){
            $response = array();
            $months_ago = 12;
            $from = strtotime("first day of -".$months_ago." months", time());
            $transaction_type_list = '17,18,19,20,49,50,51,52';
            $posts = $this->transaction_statements_m->get_group_transaction_statement($from,'',$this->group->id,0,'ASC',0,0,$transaction_type_list);
            $deposits = array();
            $withdrawals = array();
            $arr = array();
            foreach ($posts as $post) {
                $arr[] = array(
                    'date' => $post->transaction_date,
                    'type' => $post->transaction_type,
                    'amount' => $post->amount,
                );
            }
            foreach ($arr as $data) {
                if(in_array($data['type'], $this->transactions->deposit_transaction_types)){
                    if(isset($deposits[date('Ym',$data['date'])])){
                        $deposits[date('Ym',$data['date'])]+=$data['amount'];
                    }else{
                        $deposits[date('Ym',$data['date'])] = $data['amount'];
                    }
                }elseif(in_array($data['type'],$this->transactions->withdrawal_transaction_types)){
                    if(isset($withdrawals[date('Ym',$data['date'])])){
                        $withdrawals[date('Ym',$data['date'])]+=$data['amount'];
                    }else{
                        $withdrawals[date('Ym',$data['date'])] = $data['amount'];
                    }
                }
            }
            for($i=0;$i<$months_ago;$i++){
                $disbursement[$i] = 0;
                $repayment[$i]= 0;
                $from = strtotime('last day of +1 month',$from);
                if(array_key_exists(date('Ym',$from),$deposits)){
                    $repayment[$i]= $deposits[date('Ym',$from)];
                }
                if(array_key_exists(date('Ym',$from),$withdrawals)){
                    $disbursement[$i]= $withdrawals[date('Ym',$from)];
                }
                $months[$i] = date('M \'y',$from);
            }

            $response = array(
                'disbursement' => $disbursement,
                'repayment' =>$repayment,
                'months' => $months,
            );
            echo json_encode($response);
        }else{
            $response = array(
                'status' => 0,
                'refer' => site_url('authentication'),
                'message' => 'You dont have rights to access this panel.',
            );
            echo json_encode($response);die;
        }
    }

    function get_group_loan_types_summary(){
        if(array_key_exists($this->member->id, $this->member_role_holder_options) || $this->member->is_admin){
            $response = array();  
            $posts = $this->loans_m->get_group_loan_types_distribution($this->group->id);
            $loan_type_options = $this->loan_types_m->get_options();
            $other_types = array();
            $amounts = array();
            $categories = array();
            $i = 0;
            foreach ($posts as $loan_type_id=>$amount) {
                if($i>5){
                    $other_types+=$amount;
                }else{
                    $categories[] = isset($loan_type_options[$loan_type_id])?$loan_type_options[$loan_type_id]:'Loan_type_'.$i;
                    $amounts[] = $amount;
                }
                $i++;
            }
            if($other_types){
                $categories[] = 'Other Types';
                $amounts[] = $other_types;
            }
            $response = array(
                'categories' => $categories,
                'amounts' => $amounts,
            );
            echo json_encode($response);
        }else{
            $response = array(
                'status' => 0,
                'refer' => site_url('authentication'),
                'message' => 'You dont have rights to access this panel.',
            );
            echo json_encode($response);die;
        }
    }


    function get_average_loan_application_amounts(){
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-10 year');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $average = $this->loan_applications_m->get_average_loan_applications_amount($this->group_id,$from,$to);
        print_r($average);
    }

    public function get_deposits_summary(){
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-1 year',time());
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $member_ids = ($this->input->get('member_ids'));
        if($member_ids){
            $member_options = array_flip(array_filter($member_ids))?:$this->active_group_member_options;
        }else{
            $member_options = $this->active_group_member_options;
        }
        $contribution_ids = ($this->input->get('contribution_ids'));
        //$refundable_contribution_options = $this->contributions_m->get_group_refundable_contribution_options();
        $open_contribution_options = $this->contributions_m->get_group_contribution_display_options($this->group->id,TRUE);
        if($contribution_ids){
            $contribution_options = array_flip(array_filter($contribution_ids))?:$open_contribution_options;
        }else{
            $contribution_options = $open_contribution_options;
        }
        $refundable_contributions_per_year_array = $this->reports_m->get_group_member_contributions_per_year_array($this->group->id,$from,$to,$contribution_options,$member_options);
        $query_string = $_GET;
        $query_string['generate_excel'] = 1;
        $generated_query_string = http_build_query($query_string);

        

        

        $html = '';
        if($refundable_contributions_per_year_array){
            $years_array = array();
            $months_array = array();
            $grand_total_balance = array();
            $year_months_array = array();
            $years_array = generate_years_from_dates($from,$to);
            $years_months_array = generate_years_months_from_dates($from,$to);
            $year_months_array = generate_years_months_from_dates($from,$to);
            foreach ($refundable_contributions_per_year_array as $key => $years) {           
                foreach ($years as $year_key => $months) {                   
                    $years_array[] = $year_key;
                    foreach ($months as $month_value => $amount) {
                        //$year_months_array[$year_key][$month_value] = $month_value;
                        $grand_total_balance[$year_key][$month_value] = 0;
                    } 
                }               
            } 
            $years_array = array_unique($years_array);
            $months_array = $months_array;
            $html='
            <div class="invoice-content-2 bordered document-border">
                <div class="row invoice-head">
                    <div class="col-md-7 col-xs-6">
                        <div class="invoice-logo">
                            <img src="';
                                $html.=is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
                                $html.= '" alt="" class="group-logo image-responsive" /> 
                        </div>
                    </div>
                    <div class="col-md-5 col-xs-6 text-right">
                        <div class="company-address">
                            <span class="bold uppercase">'.$this->group->name.'</span><br/>';
                            $html.= nl2br($this->group->address);
                            $html.= '<br/>
                            <span class="bold">'.translate('Telephone').': </span> '.$this->group->phone.'
                            <br/>
                            <span class="bold">'.translate('Email Address').': </span> '.$this->group->email.'
                            <br/>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-center margin-top-10 margin-bottom-10">
                        '.ucfirst($this->group->name).'<br/>
                        Member Deposits Summary from '.timestamp_to_report_time($from).' to '.timestamp_to_report_time($to).'
                    </div>
                </div>
                <hr/>
                <div class=" invoice-body">
                    <div class="col-xs-12 table-responsive ">
                        <table class="table table-hover table-condensed table-checkable dataTable no-footer table-statement">
                            <thead>
                                <tr>
                                <th width="20%"></th>';
                                    $width = round(80/count($years_array));
                                    foreach($years_array as $year):
                                        $html .='
                                        <th colspan="12" class="text-right" width="'.$width.'%">'.$year.'</th>';
                                    endforeach;
                                $html .='
                                </tr>
                            </thead>
                            <tbody>';
                            $count = 1;                    
                            $html.='
                            <tr>
                                <td width="20%"><strong>Member</strong></td>';
                                foreach($years_array as $year):
                                    if(array_key_exists($year, $year_months_array)){
                                        $width = round(80/count($year_months_array[$year]));
                                        foreach ($year_months_array[$year] as $key => $month):
                                            $html .='
                                            <td class="text-left" width="'.$width.'%"><strong>'.short_month_number_to_name($month).'</strong></td>';
                                        endforeach;
                                    }
                                endforeach;
                            $html .='</tr>';
                            foreach ($this->active_group_member_options as $member_id => $member):
                                if(array_key_exists($member_id, $member_options)){
                                    $member_explode = explode(" ", $member);
                                    //print_r($member_explode[0]); die();                       
                                    $html.='<tr>
                                        <td width="20%">'.$member_explode[0].'</td>';
                                    if(array_key_exists($member_id, $refundable_contributions_per_year_array)){
                                        foreach($years_array as $year):
                                            if(array_key_exists($year,$refundable_contributions_per_year_array[$member_id])){
                                                if(array_key_exists($year, $year_months_array)){
                                                    $width = round(80/count($year_months_array[$year]));
                                                    foreach ($year_months_array[$year] as $key => $month):
                                                        if(array_key_exists($month,$refundable_contributions_per_year_array[$member_id][$year])){
                                                            $grand_total_balance[$year][$month] += currency(number_to_currency($refundable_contributions_per_year_array[$member_id][$year][$month]));
                                                            $html .='
                                                            <td class="text-left" width="'.$width.'%"><small>'.number_to_currency($refundable_contributions_per_year_array[$member_id][$year][$month]).'</small></td>';
                                                        }
                                                    endforeach;
                                                }
                                            }
                                        endforeach;
                                    }
                                    $html .='</tr>';   
                                }
                            endforeach;
                            $html.='
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="1">Total</th>';
                                    foreach ($years_array as $key => $year):
                                        foreach ($year_months_array[$year] as $key => $month):
                                            $html.='<th><strong>'.number_to_currency($grand_total_balance[$year][$month]).'</strong></th>';
                                        endforeach;
                                    endforeach;
                                $html.='                            
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                ';
        }else{
            $html = '
                <div class="alert alert-info">
                    <strong>Information!</strong> No member deposit summary to display
                </div>
            ';
        }
        echo $html;
    }

    function generate_pdf_reports(){
        $html = $this->input->post('printHtml');
        if($html){
            print_r($this->pdf_library->generate_landscape_report($html));
        }
    }
}
