<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Dashboard extends Bank_Controller{

  protected $data = array();

	function __construct(){
		parent::__construct();
        $this->load->model('groups/groups_m');
        $this->load->model('bank_accounts/bank_accounts_m');
        $this->load->model('bank_branches/bank_branches_m');
        $this->load->model('banks/banks_m');
        $this->load->model('loans/loans_m');
        $this->load->model('transaction_alerts/transaction_alerts_m');
	}

	function index(){
    //print_r($this->current_country->currency_code);
    //print_r($this->group_currency); die();
    //$groups_count = $this->groups_m->count_current_bank_staff_groups($this->user->id);
    $filter_parameters = array(
      'member_id' => $this->input->get('member_id'),
  );
 
    $this->data['total_members'] = $this->members_m->count_group_members($this->group->id, $filter_parameters);
    $this->data['groups_count'] = $this->loans_m->count_all_group_loans();
    $this->data['total_deposits'] =$this->deposits_m->get_group_loan_repayments_total_amount();
    $this->data['total_withdrawals'] =$this->loans_m->get_total_loaned_amount(); //$this->withdrawals_m->get_disbursed_withdrawal_requests_total_amount();
    $this->template->set_layout('dashboard.html')->title('DashBoard')->build('bank/index',$this->data);
  }

  function index_old(){
    $this->data['verified_partner_bank_accounts_count'] = $this->bank_accounts_m->count_verified_partner_bank_accounts();

    $this->data['transaction_alert_bank_accounts_count'] = $this->bank_accounts_m->count_verified_partner_bank_accounts_with_transaction_alerts();
    
    $paying_group_ids = $this->billing_m->get_paying_group_id_array();

    $this->data['group_options'] = $this->groups_m->get_group_options_account_number_as_key($paying_group_ids);
       $this->data['group_name_options'] = $this->groups_m->get_options();
       $group_id_options_account_number_as_key_array = $this->groups_m->get_group_options_account_number_as_key_group_id_as_value($paying_group_ids);


       //$this->data['partner_bank_accounts'] = $this->bank_accounts_m->get_partner_bank_accounts($this->bank->id,$paying_group_ids);
      
        $bank_accounts = $this->bank_accounts_m->get_verified_partner_bank_accounts_with_transaction_alerts();


        $account_numbers = array();


        foreach($bank_accounts as $bank_account):
              if(in_array($bank_account->account_number,$account_numbers)){

              }else{
                  $account_numbers[] = $bank_account->account_number;
              }
        endforeach;

      $this->data['account_number_count'] = count($account_numbers);


       $this->data['total_transactions_amount'] = $this->transaction_alerts_m->get_total_transactions_amount($this->bank->id,$account_numbers);

       $this->data['total_deposit_transactions_amount'] = $this->transaction_alerts_m->get_total_deposit_transactions_amount($this->bank->id,$account_numbers);
       //print_r($this->data['partner_bank_accounts']);
       $this->data['total_withdrawal_transactions_amount'] = $this->transaction_alerts_m->get_total_withdrawal_transactions_amount($this->bank->id,$account_numbers);
       $this->data['total_deposit_transactions_amounts_by_group_bank_account_number_array'] = $this->transaction_alerts_m->get_total_deposit_transactions_amounts_by_group_bank_account_number_array($this->bank->id,$account_numbers,$group_id_options_account_number_as_key_array);
       $this->data['total_withdrawal_transactions_amounts_by_group_bank_account_number_array'] = $this->transaction_alerts_m->get_total_withdrawal_transactions_amounts_by_group_bank_account_number_array($this->bank->id,$account_numbers,$group_id_options_account_number_as_key_array);
       //$this->data['total_withdrawal_transactions_amount'] = $this->transaction_alerts_m->get_total_withdrawal_transactions_amount($this->bank->id);      
      $arr = array();
      $group_ids = array();
      
       foreach($this->data['total_withdrawal_transactions_amounts_by_group_bank_account_number_array'] as $bank_account_number => $amount):
          if(in_array($bank_account_number,$arr)==FALSE):
            $arr[] = $bank_account_number;
            if(isset($group_id_options_account_number_as_key_array[$bank_account_number])){
              $group_ids[$group_id_options_account_number_as_key_array[$bank_account_number]] = isset($group_id_options_account_number_as_key_array[$bank_account_number])?$group_id_options_account_number_as_key_array[$bank_account_number]:0;
            }
          endif;
       endforeach;

       foreach($this->data['total_deposit_transactions_amounts_by_group_bank_account_number_array'] as $bank_account_number => $amount):
          if(in_array($bank_account_number,$arr)==FALSE):
            $arr[] = $bank_account_number;
            if(isset($group_id_options_account_number_as_key_array[$bank_account_number])){
              $group_ids[$group_id_options_account_number_as_key_array[$bank_account_number]] = $group_id_options_account_number_as_key_array[$bank_account_number];
            }
          endif;
       endforeach;

       //echo count($group_ids);
       //die;
       
       $this->data['group_ids'] = $group_ids;
       $this->data['bank_accounts_count'] = count($group_ids);
       $this->data['bank_accounts_array'] = $arr;
       $this->data['bank_accounts_by_bank_branch_count'] = $this->bank_accounts_m->get_bank_accounts_by_bank_branch_count($this->bank->id,$account_numbers);
       $this->data['bank_branch_options'] = $this->bank_branches_m->get_bank_branch_options_by_bank_id($this->bank->id);
       $this->data['deposit_percentage'] = round($this->data['total_deposit_transactions_amount']/$this->data['total_transactions_amount'] * 100);
       $this->data['withdrawal_percentage'] = round($this->data['total_withdrawal_transactions_amount']/$this->data['total_transactions_amount'] * 100);
       $this->data['total_deposits_by_month_array'] = $this->transaction_alerts_m->get_total_deposits_by_month_array($this->bank->id,$account_numbers);
       $this->data['total_withdrawals_by_month_array'] = $this->transaction_alerts_m->get_total_withdrawals_by_month_array($this->bank->id,$account_numbers);
       $this->template->set_layout('bank_dashboard.html')->title('Bank Dashboard',$this->bank->name)->build('bank/index',$this->data);
	}

  function checkin(){
        $slug = $this->input->get('slug');
        if($slug){
            $bank = $this->banks_m->get_by_slug($slug);
            if($bank){
              $this->session->set_userdata('bank_id',$bank->id);
              redirect('bank');
            }else{
              $this->session->set_flashdata('warning','Could not find bank');
              redirect('checkin');
            }
        }else{
            $this->session->set_flashdata('warning','Could not find bank');
            redirect('checkin');
        }
  }
  
}

?>