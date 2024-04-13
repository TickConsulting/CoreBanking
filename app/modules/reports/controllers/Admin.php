<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller{

	protected $data = array();

	function __construct(){
        parent::__construct();
        $this->load->model('reports_m');
        $this->load->model('transaction_alerts/transaction_alerts_m');
        $this->load->model('referrers/referrers_m');
        $this->load->model('deposits/deposits_m');
		$this->load->model('bank_accounts/bank_accounts_m');
    }

    function index(){
    	$this->data['average_number_of_members_per_group'] = $this->reports_m->average_number_of_members_per_group();
    	$this->data['average_contribution_amounts_per_group_per_member_per_month'] = $this->reports_m->average_contribution_amounts_per_group_per_member_per_month();
    	$this->data['average_contribution_amounts_per_group_per_month'] = $this->data['average_contribution_amounts_per_group_per_member_per_month'] * $this->data['average_number_of_members_per_group'];
		$this->data['average_bank_balance_per_group_bank_account'] = $this->reports_m->average_bank_balance_per_group_bank_account();
    	print_r($this->data);
    	die;
        $this->template->title('Reports')->build('admin/index',$this->data);
    }

    function e_learning_score_card(){ 
        $this->pair_group_with_bank_branches();
        $this->input->get('generate_excel');
        $group_bank_branch_pairings = $this->reports_m->get_group_bank_branch_pairings_array();
        $group_ids = array();
        foreach($group_bank_branch_pairings as $group_id => $bank_branch_id):
            $group_ids[] = $group_id;
        endforeach;
        $total_rows = $this->groups_m->count_all('','','','','','','',$group_ids);
        $pagination = create_pagination('admin/groups/listing/pages',$total_rows,50,5);
        if($this->input->get('generate_excel')){
            $this->data['posts'] = $this->groups_m->get_all('','','','','','','',$group_ids);
        }else{
            $this->data['posts'] = $this->groups_m->limit($pagination['limit'])->get_all('','','','','','','',$group_ids);
            $this->data['pagination'] = $pagination;
        }
        $this->data['referrer_options'] = $this->referrers_m->get_admin_referrer_options();
        $this->data['bank_branch_options'] = $this->bank_branches_m->get_bank_branch_options_by_bank_id(1);
        $this->data['group_bank_branch_pairing_arrays'] = $this->reports_m->get_group_bank_branch_pairing_arrays();
        $this->data['group_member_logged_in_counts_array'] = $this->members_m->get_group_member_logged_in_counts_array();
        $this->data['group_member_logged_in_percentages_array'] = $this->members_m->get_group_member_logged_in_percentages_array();
        $this->data['group_transaction_alert_counts_array'] = $this->transaction_alerts_m->get_group_transaction_alert_counts_array();
        $this->data['group_member_deposit_reconciled_counts_array'] = $this->deposits_m->get_group_member_deposit_reconciled_counts_array();
        if($this->input->get('generate_excel')){
            //echo json_encode($this->data);
            //die;
            $response = $this->curl_post_data->curl_post_json_excel((json_encode($this->data)),'https://excel.chamasoft.com/groups/e_learning_score_card',$this->application_settings->application_name.' Score Card ');
            print_r($response);die;
        }
        $this->template->title('E-learning Score Card')->build('admin/e_learning_score_card',$this->data);
    }

    function pair_group_with_bank_branches(){
        $groups = $this->groups_m->get_all();
        $group_bank_branch_pairings_array = $this->reports_m->get_group_bank_branch_pairings_array_group_id_and_bank_branch_id_as_key();
        $result = TRUE;
        $successfully_inserted_pairings = 0;  
        foreach ($groups as $group) {
            # code...
            $bank_accounts = $this->bank_accounts_m->get_group_bank_accounts($group->id);
            if($bank_accounts){
                foreach($bank_accounts as $bank_account):
                    if(isset($group_bank_branch_pairings_array[$group->id][$bank_account->bank_branch_id])){

                    }else{
                        if($bank_account->partner){
                            $this->reports_m->delete_group_bank_branch_pairing($group->id,$bank_account->bank_id,$bank_account->bank_branch_id);
                            $input = array(
                                'group_id' => $group->id,
                                'bank_id' => $bank_account->bank_id,
                                'bank_branch_id' => $bank_account->bank_branch_id,
                                'created_on' => time(),
                            );
                            if($this->reports_m->insert_group_bank_branch_pairing($input)){
                                $successfully_inserted_pairings++;
                            }else{
                                $result =  FALSE;
                            }
                        }
                    }
                endforeach;
            }
        }
        //echo $successfully_inserted_pairings." Inserted ";
    }

}