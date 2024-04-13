<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller{

	protected $data=array();

    protected $validation_rules = array(
        array(
            'field' => 'invoice_date',
            'label' => 'Invoice Date',
            'rules' => 'trim|required',
        ),array(
            'field' => 'group_id',
            'label' => 'Group',
            'rules' => 'trim|required|numeric',
        ),
    );

	function __construct(){
        parent::__construct();
        $this->load->model('invoices_m');
        $this->load->model('members/members_m');
        $this->load->library('contribution_invoices');
    }

    function listing(){
        $group_id = $this->input->get('group_id');
        $from = $this->input->get('from')?strtotime($this->input->get('from')):0;
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $total_rows = $this->invoices_m->count_invoices($group_id,$from,$to);
        $pagination = create_pagination('admin/invoices/listing/pages', $total_rows,50,5,TRUE);
    	$this->data['posts'] = $this->invoices_m->limit($pagination['limit'])->get_invoices($group_id,$from,$to);
    	$this->data['pagination'] = $pagination;
        $this->data['group_options'] = $this->groups_m->get_options();
        $this->data['member_options'] = $this->members_m->get_options();
        $this->template->title('Invoices Listing')->build('admin/listing',$this->data);
    }

    function queued_contribution_invoices(){
        $total_rows = $this->invoices_m->count_queued_contribution_invoices();
        $pagination = create_pagination('admin/invoices/queued_contribution_invoices/pages', $total_rows,50,5,TRUE);
        $this->data['posts'] = $this->invoices_m->limit($pagination['limit'])->get_queued_contribution_invoices(0);
        $this->data['pagination'] = $pagination;
        $this->data['group_options'] = $this->groups_m->get_options();
        $this->data['member_options'] = $this->members_m->get_options();
        $this->template->title('Queued Contribution Invoices')->build('admin/queued_contribution_invoices',$this->data);
    }


    function queued_contribution_fine_invoices(){
        $total_rows = $this->invoices_m->count_queued_contribution_fine_invoices();
        $pagination = create_pagination('admin/invoices/queued_contribution_fine_invoices/pages', $total_rows,5000,5,TRUE);
        $this->data['posts'] = $this->invoices_m->limit($pagination['limit'])->get_queued_contribution_fine_invoices(0);
        $this->data['pagination'] = $pagination;
        $this->data['group_options'] = $this->groups_m->get_options();
        $this->data['member_options'] = $this->members_m->get_options();
        $this->template->title('Queued Contribution Fine Invoices')->build('admin/queued_contribution_fine_invoices',$this->data);
    }

    function fix_contribution_fine_invoice_date(){

        ini_set('memory_limit','500M');
        set_time_limit(0);
        $invoices = $this->invoices_m->get_all_contribution_fine_invoices();
        $created_on_count = 0;
        $no_created_on_count = 0;
        $date_match_count = 0;
        $date_mismatch_count = 0;
        $success_count = 0;
        $failure_count = 0;
        foreach($invoices as $invoice):
            if($invoice->created_on){
                if(date('dmY',$invoice->created_on)==date('dmY',$invoice->invoice_date)){
                    $date_match_count++;
                }else{
                    $input = array(
                        'invoice_date' => $invoice->created_on,
                        'modified_on' => time(),
                    );
                    if($this->invoices_m->update($invoice->id,$input)){
                        $input = array(
                            'transaction_date' => $invoice->created_on,
                            'fine_invoice_due_date' => $invoice->created_on,
                            'modified_on' => time(),
                        );
                        if($this->statements_m->update_by_invoice_id($invoice->id,$input)){
                            $success_count++;
                        }else{
                            $failure_count++;
                        }  
                    }else{
                        $failure_count++;
                    }
                    $date_mismatch_count++;
                    //echo timestamp_to_date($invoice->created_on)."|".timestamp_to_date($invoice->invoice_date)."<br/>";
                }
                $created_on_count++;
            }else{
                $no_created_on_count++;
            }
        endforeach;
        echo "Created On: ". $created_on_count."<br/>";
        echo "No Created On: ". $no_created_on_count."<br/>";
        echo "Date Match: ". $date_match_count."<br/>";
        echo "Date MisMatch: ". $date_mismatch_count."<br/>";
        echo "Success Count: ". $success_count."<br/>";
        echo "Failure Count: ". $failure_count."<br/>";
    }

    function delete_group_queued_contribution_fine_invoices($group_id = 0){
        $this->invoices_m->delete_group_queued_contribution_fine_invoices($group_id);
    }

    function clean_fine_invoices_queue($group_id = 0){
        if($group_id){
            $group_ids[] = $group_id;
        }else{
            $groups = $this->groups_m->get_groups_trial_expired();
            $group_ids = array();
            foreach($groups as $group):
                $group_ids[] = $group->id;
            endforeach;
            $paying_group_ids = $this->billing_m->get_paying_group_id_array();
            $groups_billing_payable_amounts = $this->billing_m->get_groups_billing_payable_amounts_array($paying_group_ids);
            $groups_billing_paid_amounts = $this->billing_m->get_groups_billing_paid_amounts_array($paying_group_ids);
            $groups_billing_last_payment_dates_array = $this->billing_m->get_groups_billing_last_payment_dates_array($paying_group_ids);
            $groups_billing_first_payment_dates_array = $this->billing_m->get_groups_billing_first_payment_dates_array($paying_group_ids);
            $paying_groups_arrears_array = array();
            foreach($paying_group_ids as $group_id):
                $payable = isset($groups_billing_payable_amounts[$group_id])?$groups_billing_payable_amounts[$group_id]:0;
                $paid = isset($groups_billing_paid_amounts[$group_id])?$groups_billing_paid_amounts[$group_id]:0;
                $paying_groups_arrears_array[$group_id] = $payable - $paid;
                if($paying_groups_arrears_array[$group_id]<=0):
                    $key = array_search($group_id,$paying_group_ids);
                    unset($paying_group_ids[$key]);
                endif;
            endforeach;
            $groups = $this->groups_m->get_paying_groups($paying_group_ids);
            foreach($groups as $group):
                $group_ids[] = $group->id;
            endforeach;
        }
        $this->contributions_m->disable_group_contributions_invoicing($group_ids);
        $this->invoices_m->delete_group_queued_contribution_fine_invoices($group_ids);;
    }

    function display_contribution_queue_groups_count(){
        $queued_items = $this->invoices_m->get_group_contribution_fine_queue_counts();
        foreach($queued_items as $queued_item):
            echo $queued_item->group_name.":".$queued_item->count." : ".$queued_item->group_id."<br/>";
        endforeach;
    }
}
