<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loans extends Public_Controller{

	function __construct(){
        parent::__construct();
        $this->load->library('loan');
        $this->load->model('loan_invoices/loan_invoices_m');
        $this->load->model('deposits/deposits_m');
    }

    function queue_loan_invoices($date=0){
    	$this->loan->queue_today_loan_invoices($date);
    }

    function process_loan_invoices_queue($limit=0){
    	$this->loan->process_loan_invoices_queue($limit=0);
    }

    function queue_fine_loan_late_payment($date=0){
        $this->loan->queue_fine_loan_late_payment($date);
    }

    function process_loan_late_payment_invoices_queue($limit=0){
        $this->loan->process_loan_late_payment_invoices_queue($limit);
    }

    function queue_outstanding_loan_balance_fines($date=0){
        $this->loan->queue_outstanding_loan_balance_fines($date);
    }

    function process_outstanding_loan_balance_invoices_queue($limit=0){
        $this->loan->process_outstanding_loan_balance_invoices_queue($limit);
    }

    function loan_fixer($id=0){
        $this->loan->loan_fixer($id);
    }

    function pay_invoices($loan_id=0){
        $this->loan->pay_invoices($loan_id);
    }

    function pay_edited_invoices($loan_id=0){
        $this->loan->pay_edited_invoices($loan_id);
    }

    function update_loan_invoices($loan_id=0){
        $this->loan->update_loan_invoices($loan_id);
    }

    function update_debtor_loan_invoices($loan_id=0){
        $this->loan->update_loan_invoices($loan_id,1);
    }

    function loan_update_status($id=0){
        $this->loan->update_loan_status($id);
    }

    function test_server_time(){
        print_r($this->loan_invoices_m->test_server_time());

        echo timestamp_to_message_time(time());die;
    }

    function recalculate_reducing_balance($loan_id=0,$amount_paid=0,$repayment_date=''){
        $this->loan->recalculate_reducing_balance($loan_id,$amount_paid,$repayment_date);
    }

    function get_lump_sum($loan_id=0,$date=0){
        $this->loan_invoices_m->get_loan_lump_sum_as_date($loan_id,$date);
    }

    function recalculate_loan_balance_invoice_for_reducing($loan_id=0){
        print_r($this->loan->recalculate_loan_balance_invoice_for_reducing($loan_id));
    }

    function correct_voided_loan_invoices(){
        $loans = $this->loans_m->get_voided_loans();
        foreach($loans as $loan):
            if($this->loan_invoices_m->update_voided_loan_invoices($loan->id)){
                echo "Updated loan invoices.<br/>";
            }else{
                echo "Could not update loan invoices.<br/>";
            }
        endforeach;
    }
    

    function get_all_active_group_loans($group_id = 0){
        $from = strtotime("02-04-2017");
        $to = strtotime("14-02-2020");
        $total_rows = $this->loans_m->count_active_group_loans($group_id,$from,$to,FALSE);
        $step_size = 5;
        $pagination = create_pagination('loans/get_all_active_group_loans/'.$group_id.'/',$total_rows,$step_size,4,TRUE);
        $loans = $this->loans_m->limit($pagination['limit'])->get_active_group_loans($group_id,$from,$to,FALSE);
        if($loans){
            foreach ($loans as $loan) {
                 if($res = $this->loan->update_loan_invoices($loan->id)){
                    echo $loan->id;
                    echo 'done'.'<br/>';
                }else{
                    echo 'Failed'.$loan->id.'<br/>';
                }
            }
        }
        if($pagination){
            $total = $pagination['total'];
            $current_page = $pagination['current_page'];
            $next_page = ($current_page+$step_size);
            if($next_page<$total){
                $url = site_url('loans/get_all_active_group_loans/'.$group_id.'/'.($next_page));
                echo '
                    <script>
                        window.location = "'.$url.'";
                    </script>

                ';
                redirect($url);
            }
        }
        print_r($pagination);
        echo count($loans);
    }


    function test_interest_paid_logic($group_id = 0,$loan_id = 0){
        $this->reports_m->test_interest_paid_logic($group_id,$loan_id);
    }

    function get_loan_invoices(){
        $loan_invoices = $this->loan_invoices_m->get_group_loan_invoices(3912);
        echo count($loan_invoices);
        foreach($loan_invoices as $loan_invoice):
            if($loan_invoice->principle_amount_payable < 0){
                echo "Am in";
            }
        endforeach;
    }


    function get_loans_with_payments(){
        $transfers = $this->deposits_m->get_group_contribution_transfers('',3912);
        foreach ($transfers as $transfer) {
            $loans = $this->loans_m->get_voided_loan($transfer->loan_to_id,$transfer->group_id);
            if($loans){
                $loan = $this->loans_m->get_member_active_loan($loans->member_id,$loans->loan_amount,$loans->group_id,$loans->disbursement_date)?:array();
                if(count($loan) == 1){
                    $new_loan = $loan[0];
                    if($new_loan){
                        $contribution_transfer_statement_entries = $this->statements_m->get_all_contribution_transfer_statement_entries($transfer->id,$transfer->group_id);
                        if(isset($contribution_transfer_statement_entries[0])){
                            $statement = $contribution_transfer_statement_entries[0];
                            $contribution_transfer = $this->deposits_m->get_group_contribution_transfer($statement->contribution_transfer_id,$statement->group_id);
                            $input = array(
                                    'loan_id'   =>  $new_loan->id,
                                    'group_id'  =>  $new_loan->group_id,
                                    'member_id' =>  $new_loan->member_id,
                                    'receipt_date'=>$transfer->transfer_date,
                                    'amount'    =>  $transfer->amount,
                                    'status'    =>  1,
                                    'active'    =>  1,
                                    'created_on'=>  time(),
                                    'transfer_from' => $transfer->contribution_from_id,
                                    'incoming_loan_transfer_invoice_id' => '',
                                    'incoming_contribution_transfer_id' => $statement->id,
                                );
                            $repayment_id =$this->loan_repayments_m->insert($input);
                            if($repayment_id){
                                $statement_entry_id =$this->loans_m->insert_loan_statement(array(
                                    'member_id' =>  $new_loan->member_id,
                                    'group_id'  =>  $new_loan->group_id,
                                    'transaction_date' =>   $transfer->transfer_date,
                                    'transaction_type'  =>  4,
                                    'transfer_from' => $transfer->contribution_from_id,
                                    'loan_id'   =>  $new_loan->id,
                                    'loan_payment_id'   =>  $repayment_id,
                                    'amount'        =>  $transfer->amount,
                                    'balance'       =>  0,
                                    'active'        =>  1,
                                    'status'        =>  1,
                                    'created_on'    =>  time(),
                                ));
                                if($statement_entry_id){
                                    $this->deposits_m->update_contribution_transfer($contribution_transfer->id,array(
                                        'loan_to_id' => $new_loan->id,
                                    ));

                                    $this->statements_m->update($statement->id,array(
                                        'loan_to_id' => $new_loan->id,
                                    ));
                                }
                                $this->update_loan_invoices($new_loan->id);
                            }
                        }
                    }
                }
            }
        }
    }

    function get_groups_with_loans(){
       $groups  =  $this->loans_m->get_groups_with_loans();
       $update = array(
            'group_offer_loans' => 1,
       );
       $success = 0;
       foreach ($groups as $group) {
            if($this->groups_m->update($group->group_id,$update)){
                ++$success;
            }
       }

       echo  $success.' groups updated out of '.count($groups);
    }

    function test_group_member($id=0){
        if(!$this->ion_auth->is_admin($id) && !$this->ion_auth->is_bank_admin($id) && !$this->ion_auth->is_group_member($id) && !$this->ion_auth->is_group_account_manager($id)){
            die('in');
        }else{
            die('out');
        }
    }
}