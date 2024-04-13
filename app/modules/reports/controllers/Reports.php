<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('pmailer');
        $this->load->model('reports_m');
        $this->load->model('groups/groups_m');
        $this->load->model('emails/emails_m');
        $this->load->model('referrers/referrers_m');
        $this->load->model('users/users_m');
        $this->load->model('banks/banks_m');
        $this->load->model('bank_accounts/bank_accounts_m');
        $this->load->model('bank_branches/bank_branches_m');
        $this->load->model('transaction_alerts/transaction_alerts_m');
    }

    public function generate_daily_kpi_email(){
    	$bank = $this->banks_m->get_by_slug('equity-bank');
    	$groups_signed_up_today_by_bank_branch = $this->groups_m->get_groups_signed_up_today_by_bank_branch();
        $groups_signed_up_today_count = $this->groups_m->count_groups_signed_up_today();
        $users_signed_up_today_count = $this->users_m->count_users_signed_up_today();
        $bank_branch_options = $this->bank_branches_m->get_bank_branch_options_by_bank_id($bank->id);
        $groups_signed_up_today_count_by_bank_branch_array = $this->groups_m->get_groups_signed_up_today_count_by_bank_branch_array($bank_branch_options);
        $bank_accounts_by_bank_branch_count = $this->bank_accounts_m->get_bank_accounts_by_bank_branch_count($bank->id);
        $total_deposit_transactions_amount_for_today_by_bank_branch_id_array = $this->transaction_alerts_m->get_total_deposit_transactions_amount_for_today_by_bank_branch_id_array();
        $total_withdrawal_transactions_amount_for_today_by_bank_branch_id_array = $this->transaction_alerts_m->get_total_withdrawal_transactions_amount_for_today_by_bank_branch_id_array();
        
		$message='
			<div class="row">
				<div class="col-md-12 col-sm-12">
				    <div class="portlet light bordered">
				        <div class="portlet-title">
				            <div class="caption font-green">
				                <span class="caption-subject bold uppercase"></span>
				            </div>
				        </div>
				        <div class="portlet-body">
				            <div class="row">
				                <div class="col-md-12 col-sm-12">
				                	<h4>Daily KPIs</h4>
				                    <table border="1" cellpadding="3" class="table table-bordered table-condensed table-striped table-hover table-searchable">
				                        <thead>
				                            <tr>
				                                <th width="8px">#</th>
				                                <th>Daily KPI Name</th>
				                                <th>Daily KPI</th>
				                            </tr>
				                        </thead>
				                        <tbody>
				                            <tr>
				                                <td>1.</td>
				                                <td>Number of Groups Registered</td>
				                                <td>'.$groups_signed_up_today_count.'</td>
				                            </tr>
				                            <tr>
				                                <td>2.</td>
				                                <td>Number of Customers Registered</td>
				                                <td>'.$users_signed_up_today_count.'</td>
				                            </tr>
				                        </tbody>
				                    </table>
				                    <h4>By Branches</h4>
				                    <table  border="1" cellpadding="3" class="table table-bordered table-condensed table-striped table-hover table-searchable">
				                        <thead>
				                            <tr>
				                                <th width="8px">#</th>
				                                <th>Branch</th>
				                                <th>Number of Groups Registered</th>
				                                <th>Number of Customers Registered</th>
				                            </tr>
				                        </thead>
				                        <tbody>';
				                            $count = 1; foreach($groups_signed_up_today_by_bank_branch as $branch):
				                                $message.='<tr>
				                                    <td>'.$count.'</td>
				                                    <td>'.$bank_branch_options[$branch->bank_branch_id].'</td>
				                                    <td>'.$groups_signed_up_today_count_by_bank_branch_array[$branch->bank_branch_id].'</td>
				                                    <td>'.$branch->member_count.'</td>
				                                </tr>';
				                            $count++; endforeach;
				                        $message.='</tbody>
				                    </table>
				                </div>
				            </div>
				            <h4>Branch Analysis</h4>
				            <div class="row">
				                <div class="col-md-12 col-sm-12">
				                    <div id="" class="table-responsive">
				                        <table border="1" cellpadding="3" class="table table-bordered table-condensed table-striped table-hover table-searchable">
				                            <thead>
				                                <tr>
				                                    <th width="8px">#</th>
				                                    <th>Branch</th>
				                                    <th class="">Transactions</th>
				                                    <th class="text-right">Deposits</th>
				                                    <th class="text-right">Withdrawals</th>
				                                </tr>
				                            </thead>
				                            <tbody>';
				                                $i = 1; $group_count = 0; $grand_total_deposits = 0; $grand_total_withdrawals = 0; foreach($bank_accounts_by_bank_branch_count as $bank_branch):
				                                    $message.='<tr>
				                                        <td>'.$i++.'</td>
				                                        <td>'.$bank_branch_options[$bank_branch->bank_branch_id].'</td>
				                                        <td></td>
				                                        <td class="text-right">';
				                                         
				                                            $total_deposits = isset($total_deposit_transactions_amount_for_today_by_bank_branch_id_array[$bank_branch->bank_branch_id])?$total_deposit_transactions_amount_for_today_by_bank_branch_id_array[$bank_branch->bank_branch_id]:0;
				                                            $grand_total_deposits+=$total_deposits;
				                                            $message.=number_to_currency($total_deposits); 
				                                       
				                                        $message.='</td>
				                                        <td class="text-right">';
				                
				                                            $total_withdrawals = isset($total_withdrawal_transactions_amount_for_today_by_bank_branch_id_array[$bank_branch->bank_branch_id])?$total_withdrawal_transactions_amount_for_today_by_bank_branch_id_array[$bank_branch->bank_branch_id]:0;
				                                            $message.= number_to_currency($total_withdrawals);

				                                            $grand_total_withdrawals+=$total_withdrawals; 
				                                        
				                                        $message.='</td>
				                                    </tr>';
				                                $group_count += $bank_branch->group_count; endforeach; 
				                                $message.='<tr>
				                                    <th>#</th>
				                                    <th>Total</th>
				                                    <th><strong><?php //echo $group_count; ?></strong></th>
				                                    <th><strong>'.number_to_currency($grand_total_deposits).'</strong></th>
				                                    <th><strong>'.number_to_currency($grand_total_withdrawals).'</th>
				                                </tr>
				                            </tbody>
				                        </table>
				                    </div>
				                </div>
				            </div>
				        </div>
				    </div>
				</div>
				</div>';
		//echo $message;
		$input = array(
			"email_to"=>"edwin.njoroge@digitalvision.co.ke",
			"subject"=>"Daily KPIs",
			"message"=>$message,
			"created_on"=>time()
		);
		$this->emails_m->insert_email_queue($input);
		$input = array(
			"email_to"=>"philip.galego@equitybank.co.ke",
			"subject"=>"Daily KPIs",
			"message"=>$message,
			"created_on"=>time()
		);
		$this->emails_m->insert_email_queue($input);
    }

	function queue_paying_groups_text_file_email(){
		$paying_group_ids = $this->billing_m->get_paying_group_id_array();
		$groups_billing_payable_amounts = $this->billing_m->get_groups_billing_payable_amounts_array($paying_group_ids);
		$groups_billing_paid_amounts = $this->billing_m->get_groups_billing_paid_amounts_array($paying_group_ids);
		$groups_billing_last_payment_dates_array = $this->billing_m->get_groups_billing_last_payment_dates_array($paying_group_ids);
		$groups_billing_first_payment_dates_array = $this->billing_m->get_groups_billing_first_payment_dates_array($paying_group_ids);
		$paying_groups_arrears_array = array();
        $bank_branch_options = $this->bank_branches_m->get_bank_branch_options_by_bank_id(1);
        $group_bank_branch_pairing_arrays = $this->reports_m->get_group_bank_branch_pairing_arrays();
		$paying_groups_arrears_array = $paying_groups_arrears_array;
		$groups_billing_last_payment_dates_array = $groups_billing_last_payment_dates_array;
		$groups_billing_first_payment_dates_array = $groups_billing_first_payment_dates_array;
		$posts = $this->groups_m->get_paying_groups($paying_group_ids);
		$user_options = $this->users_m->get_options(TRUE);
		$billing_cycle_options = $this->billing_settings->billing_cycle;
		$group_bank_account_options = $this->bank_accounts_m->get_group_bank_account_group_id_as_key_options(FALSE);
        $referrer_options = $this->referrers_m->get_admin_referrer_options();
        $file = fopen("uploads/files/Equity_Chama_Paying_Groups_".date('Y').date('m').date('d').".txt", "w") or die("Unable to open file!");
		$row = "Sign_Up_Date\tFirst_Payment_Date\tLast_Payment_Date\tGroup_Name\tAdmin_Name_Contacts\tBankAccount\tReferrer\tReferrer_Information\tBranch\tSOL\n";
        fwrite($file, $row);
        foreach ($posts as $group) {
			# code...
			$sol = "";
			$account_number = "";
			if(array_key_exists($group->id,$group_bank_account_options)){
                $bank_accounts = $group_bank_account_options[$group->id];
                $count = 1;
                foreach($bank_accounts as $bank_account):
                    $account_number = $bank_account;
                	$sol = substr($account_number,0,3);
                    break;
                endforeach;
            }else{
                $account_number = "No bank account entered.";
                $sol = "000";
            }

            $count = 1; 
            $bank_branch = "";
            if(isset($group_bank_branch_pairing_arrays[$group->id])):
                foreach($group_bank_branch_pairing_arrays[$group->id] as $bank_branch_id):
                    if($count==1){
                        $bank_branch = $bank_branch_options[$bank_branch_id];
                    }else{
                        $bank_branch .= ",".$bank_branch_options[$bank_branch_id];
                    }
                    $count++;
                endforeach;
            endif;
			$referrer = isset($referrer_options[$group->referrer_id])?$referrer_options[$group->referrer_id]:"No Referrer";
			$referrer_information = $group->referrer_information?:"No Referrer Information";
			$row = date('d-F-Y',$group->created_on)."\t".date('d-F-Y',$groups_billing_first_payment_dates_array[$group->id])."\t".date('d-F-Y',$groups_billing_last_payment_dates_array[$group->id])."\t".$group->name."\t".str_replace("&nbsp;"," ",strip_tags($user_options[$group->owner]))."\t".$account_number."\t".$referrer."\t".$referrer_information."\t".$bank_branch."\t".$sol."\n";
			fwrite($file, $row);
		}

		fclose($file);
	
		$input = array(
			'email_to' => 'peter.kimutai@digitalvision.co.ke',
			// 'cc' => '',
			'subject' => $this->application_settings->application_name.' Paying Groups Report',
			'message' => $this->application_settings->application_name.' Paying Groups Report',
			'created_on' => time(),
			'created_by' => 1,
			'attachments' => serialize(array("uploads/files/Equity_Chama_Paying_Groups_".date('Y').date('m').date('d').".txt"))
		);
		$this->emails_m->insert_email_queue($input);
		
	}

	function test_interest_paid_logic($group_id = 0,$loan_id = 0){
		$this->reports_m->test_interest_paid_logic($group_id,$loan_id);
	}

}