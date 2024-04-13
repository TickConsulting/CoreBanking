<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{

    protected $data =  array();

    function __construct(){
        parent::__construct();
        $this->load->model('transaction_alerts_m');
        $this->load->model('contributions/contributions_m');
        $this->load->model('fine_categories/fine_categories_m');
        $this->load->model('expense_categories/expense_categories_m');
        $this->load->model('income_categories/income_categories_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('stocks/stocks_m');
        $this->load->model('assets/assets_m');
        $this->load->model('money_market_investments/money_market_investments_m');
        $this->load->model('bank_loans/bank_loans_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->library('transactions');
        $this->load->library('loan');
        $this->load->library('contribution_invoices');
        $this->data['loan_amount_type'] = $this->loan->loan_amount_type;
        $this->sms_template_default = $this->contribution_invoices->sms_template_default;
    }

    
    public function index(){
        $data = array();
        $this->template->title('Group Transaction Alerts')->build('group/index',$data);
    }

    function update_transaction_alerts_from_mobile_provider(){
        echo $this->transaction_alerts_m->update_transaction_alerts_from_mobile_provider();
    }

    function create_dummy_verified_bank_account(){
        // print_r($this->bank_accounts_m->get_group_bank_accounts($this->group->id)); die;
        $id = $this->bank_accounts_m->insert(array(
            'group_id'          =>  $this->group->id,
            'account_number'    =>  '666',
            'account_name'      => 'The kimutai',
            'initial_balance'   =>  currency('900000'),
            'bank_branch_id'    =>  1862,
            'is_verified'           =>  1,
            'bank_id'           =>  57,
            'enable_email_transaction_alerts_to_members'           =>  0,
            'created_by'        =>  $this->user->id,
            'account_password'  =>  '',
            'created_on'        =>  time(),
            'active'            =>  1,
        ));
        if($id){
            print_r('Successful');
        }
        die;
    }

    function create_dummy_transaction_alert($account_number = 0,$type = 1){
        $input = array(
            'equity_bank_transaction_alert_id'=> 1,
            'created_on'=>time(),
            'transaction_id'=>'SUDO RM -RF',
            'type'=> $type,
            'account_number'=> $account_number,
            'amount'=>valid_currency(5000),
            'transaction_date'=>strtotime('24-05-2019'),
            'is_merged'=> 0,
            'reconciled'=> 0,
            'bank_id'=>57,
            'active'=>1,
            'particulars'=>'SUDO RM -RF',
            'ip_address'=>$_SERVER['REMOTE_ADDR']?:'',
            'description'=>'SUDO RM -RF Payment by The Kimutai Peter',
            'group_members_notified'=>0,
            'currency'=>'KES',
        );
        $transaction_alert_id = $this->transaction_alerts_m->insert($input);
        if($transaction_alert_id){
            print_r('Successful');
        }
    }

    function reconcile_deposits(){
        $this->session->unset_userdata('timestamp');
        $this->data['asset_category_options'] = $this->asset_categories_m->get_group_asset_category_options();
        $this->data['banks'] = $this->banks_m->get_group_bank_options();
        $this->data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $this->data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $this->data['placeholders'] = isset($placeholders[0])?$placeholders[0]:array();
        $this->data['invoice_days'] = $this->contribution_invoices->invoice_days;
        $this->data['month_days'] = $this->contribution_invoices->month_days;
        $this->data['week_days'] = $this->contribution_invoices->week_days;
        $this->data['days_of_the_month'] = $this->contribution_invoices->days_of_the_month;
        $this->data['every_two_week_days'] = $this->contribution_invoices->every_two_week_days;
        $this->data['months'] = $this->contribution_invoices->months;
        $this->data['starting_months'] = $this->contribution_invoices->starting_months;
        $this->data['week_numbers'] = $this->contribution_invoices->week_numbers;
        $this->data['contribution_category_options'] = $this->contribution_invoices->contribution_category_options;
        $this->data['contribution_frequency_options'] = $this->contribution_invoices->contribution_frequency_options;
        $this->data['contribution_type_options'] = $this->contribution_invoices->contribution_type_options;
        $this->data['contribution_days_option']=$this->contribution_invoices->contribution_days_option;        
        $this->data['starting_days'] = $this->contribution_invoices->starting_days;
        $this->data['sms_template_default'] = $this->sms_template_default;
        $this->data['fine_types'] = $this->contribution_invoices->fine_types;
        $this->data['fine_chargeable_on_options'] = $this->contribution_invoices->fine_chargeable_on_options;
        $this->data['fine_frequency_options'] = $this->contribution_invoices->fine_frequency_options;
        $this->data['fine_mode_options'] = $this->contribution_invoices->fine_mode_options;
        $this->data['fine_limit_options'] = $this->contribution_invoices->fine_limit_options;
        $this->data['percentage_fine_on_options'] = $this->contribution_invoices->percentage_fine_on_options;
        $this->data['stock_options'] = $this->stocks_m->get_group_stock_options();
        $this->data['asset_options'] = $this->assets_m->get_group_asset_options();
        $this->data['money_market_investment_options'] = $this->money_market_investments_m->get_group_open_money_market_investment_options($this->group->id,$this->group_currency);
        $this->data['income_category_options'] = $this->income_categories_m->get_group_income_category_options();
        $this->data['depositor_options'] = $this->depositors_m->get_group_depositor_options();
        $this->data['deposit_for_options'] = $this->transactions->deposit_for_options;
        $this->data['fine_category_options'] = $this->fine_categories_m->get_group_fine_category_options();
        $this->data['contribution_options'] = $this->contributions_m->get_active_group_contribution_options();
        $this->data['bank_account_options'] = $this->bank_accounts_m->get_group_verified_partner_bank_account_options();
        $this->data['mobile_money_account_options'] = $this->mobile_money_accounts_m->get_group_verified_partner_mobile_account_options();
        $this->data['from_account_options'] = $this->accounts_m->get_active_group_account_options(FALSE);
        $this->data['account_options'] = $this->accounts_m->get_active_group_account_options(FALSE);
        $this->data['interest_types'] = $this->loan->interest_types;
        $this->data['loan_interest_rate_per'] = $this->loan->loan_interest_rate_per;
        $this->data['loan_grace_periods'] = $this->loan->loan_grace_periods;
        $this->data['loan_days'] = $this->loan->loan_days;
        $this->data['sms_template_default'] = $this->loan->sms_template_default;
        $this->data['late_loan_payment_fine_types'] = $this->loan->late_loan_payment_fine_types;
        $this->data['late_payments_fine_frequency'] = $this->loan->late_payments_fine_frequency;
        $this->data['fixed_amount_fine_frequency_on'] = $this->loan->fixed_amount_fine_frequency_on;
        $this->data['percentage_fine_on'] = $this->loan->percentage_fine_on;
        $this->data['one_off_fine_types'] = $this->loan->one_off_fine_types;
        $this->data['one_off_percentage_rate_on'] = $this->loan->one_off_percentage_rate_on;
        $this->data['loan_processing_fee_types'] = $this->loan->loan_processing_fee_types;
        $this->data['loan_processing_fee_percentage_charged_on'] = $this->loan->loan_processing_fee_percentage_charged_on;
        $this->data['group_role_options'] = $this->group_roles_m->get_group_role_options();
        $this->template->title(translate('Reconcile Deposits'))->build('group/reconcile_deposits',$this->data);
    }

    function admin_reconcile_deposits(){
        $this->session->unset_userdata('timestamp');
        $this->data['asset_category_options'] = $this->asset_categories_m->get_group_asset_category_options();
        $this->data['banks'] = $this->banks_m->get_group_bank_options();
        $this->data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $this->data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $this->data['placeholders'] = isset($placeholders[0])?$placeholders[0]:array();
        $this->data['invoice_days'] = $this->contribution_invoices->invoice_days;
        $this->data['month_days'] = $this->contribution_invoices->month_days;
        $this->data['week_days'] = $this->contribution_invoices->week_days;
        $this->data['days_of_the_month'] = $this->contribution_invoices->days_of_the_month;
        $this->data['every_two_week_days'] = $this->contribution_invoices->every_two_week_days;
        $this->data['months'] = $this->contribution_invoices->months;
        $this->data['starting_months'] = $this->contribution_invoices->starting_months;
        $this->data['week_numbers'] = $this->contribution_invoices->week_numbers;
        $this->data['contribution_frequency_options'] = $this->contribution_invoices->contribution_frequency_options;
        $this->data['contribution_type_options'] = $this->contribution_invoices->contribution_type_options;
        $this->data['sms_template_default'] = $this->sms_template_default;
        $this->data['fine_types'] = $this->contribution_invoices->fine_types;
        $this->data['fine_chargeable_on_options'] = $this->contribution_invoices->fine_chargeable_on_options;
        $this->data['fine_frequency_options'] = $this->contribution_invoices->fine_frequency_options;
        $this->data['fine_mode_options'] = $this->contribution_invoices->fine_mode_options;
        $this->data['fine_limit_options'] = $this->contribution_invoices->fine_limit_options;
        $this->data['percentage_fine_on_options'] = $this->contribution_invoices->percentage_fine_on_options;
        $this->data['stock_options'] = $this->stocks_m->get_group_stock_options();
        $this->data['asset_options'] = $this->assets_m->get_group_asset_options();
        $this->data['money_market_investment_options'] = $this->money_market_investments_m->get_group_open_money_market_investment_options($this->group->id,$this->group_currency);
        $this->data['income_category_options'] = $this->income_categories_m->get_group_income_category_options();
        $this->data['depositor_options'] = $this->depositors_m->get_group_depositor_options();
        $this->data['deposit_for_options'] = $this->transactions->deposit_for_options;
        $this->data['fine_category_options'] = $this->fine_categories_m->get_group_fine_category_options();
        $this->data['contribution_options'] = $this->contributions_m->get_active_group_contribution_options();
        $this->data['bank_account_options'] = $this->bank_accounts_m->get_group_verified_partner_bank_account_options();
        $this->data['mobile_money_account_options'] = $this->mobile_money_accounts_m->get_group_verified_partner_mobile_account_options();
        $this->data['from_account_options'] = $this->accounts_m->get_active_group_account_options();
        $this->data['account_options'] = $this->accounts_m->get_active_group_account_options();
        $this->data['interest_types'] = $this->loan->interest_types;
        $this->data['loan_interest_rate_per'] = $this->loan->loan_interest_rate_per;
        $this->data['loan_grace_periods'] = $this->loan->loan_grace_periods;
        $this->data['loan_days'] = $this->loan->loan_days;
        $this->data['sms_template_default'] = $this->loan->sms_template_default;
        $this->data['late_loan_payment_fine_types'] = $this->loan->late_loan_payment_fine_types;
        $this->data['late_payments_fine_frequency'] = $this->loan->late_payments_fine_frequency;
        $this->data['fixed_amount_fine_frequency_on'] = $this->loan->fixed_amount_fine_frequency_on;
        $this->data['percentage_fine_on'] = $this->loan->percentage_fine_on;
        $this->data['one_off_fine_types'] = $this->loan->one_off_fine_types;
        $this->data['one_off_percentage_rate_on'] = $this->loan->one_off_percentage_rate_on;
        $this->data['loan_processing_fee_types'] = $this->loan->loan_processing_fee_types;
        $this->data['loan_processing_fee_percentage_charged_on'] = $this->loan->loan_processing_fee_percentage_charged_on;
        //$this->data['unreconciled_deposits'] = $this->transaction_alerts_m->get_group_unreconciled_deposits($this->group_partner_bank_account_number_list,$this->group_partner_mobile_money_account_number_list);
        $this->template->set_layout('default_full_width.html')->title('Reconcile Deposits')->build('group/admin_reconcile_deposits',$this->data);
    }

    function ajax_get_unreconciled_deposits_listing(){
        $unreconciled_deposits = $this->transaction_alerts_m->get_group_unreconciled_deposits($this->group_partner_bank_account_number_list,$this->group_partner_mobile_money_account_number_list);
        //print_r($unreconciled_deposits);die;
        $bank_account_options = $this->bank_accounts_m->get_group_verified_partner_bank_account_options();
        $mobile_money_account_options = $this->mobile_money_accounts_m->get_group_verified_partner_mobile_account_options();
        $merged_ids = $this->transaction_alerts_m->get_transaction_alerts_merge_parent_ids($this->group_partner_bank_account_number_list,$this->group_partner_mobile_money_account_number_list);
        if(empty($unreconciled_deposits)){
            echo'
                <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    </button>
                    <strong>'.translate('Information!').' </strong>'.translate('No unreconciled deposits to display.').'
                </div>
            ';
        }else{
            echo form_open('group/transaction_alerts/action', ' id="form"  class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed form-horizontal deposit_listing_form"');
            echo '
                <div class="table-responsive">
                    <table class="table table-condensed table-striped table-hover table-header-fixed table-searchable">
                        <thead>
                            <tr>';
                            if($this->group->enable_merge_transaction_alerts && count($unreconciled_deposits) >= 2){
                                echo '<th width=\'2%\'>
                                        <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                            <input type="checkbox" name="check" value="all" class="check_all">
                                            <span></span>
                                        </label>
                                    </th>
                                ';
                            }
                            echo '
                                <th width=\'2%\'>
                                    #
                                </th>
                                <th width="20%">
                                    Date
                                </th>
                                <th width="25%">
                                    Account
                                </th>
                                <th width="25%">
                                    Details
                                </th>
                                <th class="text-right">
                                    Amount ('.$this->group_currency.')
                                </th>
                                <th>
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>';
                            echo "
                            <tr id='unreconciled_deposits_loading_row' style='display:none;'>
                                <td colspan='6'></td>
                            </tr>";
                            $count=1; foreach($unreconciled_deposits as $unreconciled_deposit):
                                $account_details = isset($bank_account_options[$unreconciled_deposit->account_number])?$bank_account_options[$unreconciled_deposit->account_number]:(isset($mobile_money_account_options[$unreconciled_deposit->account_number])?$mobile_money_account_options[$unreconciled_deposit->account_number]:'');
                                echo '
                                <tr id="unreconciled_deposit_row_'.$unreconciled_deposit->id.'"';
                                    if($unreconciled_deposit->reconciled){
                                        echo ' class="success" ';
                                    }
                                    echo '>';
                                    if($this->group->enable_merge_transaction_alerts && count($unreconciled_deposits) >= 2){
                                        echo ' 
                                            <td>
                                                <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                                    <input name="action_to[]"" type="checkbox" class="checkboxes" value="'.$unreconciled_deposit->id.'"/>
                                                    <span></span>
                                                </label>
                                            </td>
                                        ';
                                    }

                                    echo '
                                    <td><input name=\'action_to_reconcile\' type="checkbox" class="action_to_reconcile checkboxes" value="'.$unreconciled_deposit->id.'" /></td>
                                    <td>
                                        '.timestamp_to_date($unreconciled_deposit->transaction_date).'<br/>
                                        <small><strong>Delivered On: </strong>'.timestamp_to_date_and_time($unreconciled_deposit->created_on).'</small>
                                    </td>
                                    <td>'. $account_details.'</td>
                                    <td>
                                        '.$unreconciled_deposit->particulars.' <small><a class=\'toggle_transaction_alert_details text-primary\' style="cursor: pointer;">More..</a></small>
                                        <div class="transaction_alert_details" style="display:none;"">
                                            '.$unreconciled_deposit->description.'
                                        </div>
                                    </td>
                                    <td class="text-right">'.number_to_currency($unreconciled_deposit->amount).'</td>
                                    <td class="reconcile_action">';
                                        if($unreconciled_deposit->reconciled){
                                            echo '<span class="label label-sm label-success"> Reconciled </span>';
                                        }else{
                                            echo '
                                                <a class="btn btn-sm btn-outline-accent m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air reconcile_deposit_pop_up reconcile_deposit" data-toggle="modal" data-target="#reconcile_deposit_pop_up" id="'.$unreconciled_deposit->id.'" data-backdrop="static" data-keyboard="false">
                                                    <span>
                                                        <i class="la la-pencil"></i>
                                                        <span>Reconcile</span>
                                                    </span>
                                                </a>
                                           ';
                                        }
                                        if($this->group->enable_bulk_transaction_alerts_reconciliation){
                                            echo '
                                                <a href="'.site_url('group/deposits/upload_payments/'.$unreconciled_deposit->id).'"class="btn btn-sm btn-outline-accent m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air mt-2" id="'.$unreconciled_deposit->id.'">
                                                    <span>
                                                        <i class="la la-cloud-upload"></i>
                                                        <span>Reconcile via Upload</span>
                                                    </span>
                                                </a>
                                            ';
                                        }
                                        
                                        if(isset($merged_ids[$unreconciled_deposit->id])){
                                            echo '
                                                <a href="'.site_url('group/transaction_alerts/unmerge_transaction/'.$unreconciled_deposit->id).'"class="btn btn-sm btn-outline-accent m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air mt-2" id="'.$unreconciled_deposit->id.'">
                                                    <span>
                                                        <i class="la la-cloud-upload"></i>
                                                        <span>Unmerge Alert</span>
                                                    </span>
                                                </a>
                                            ';
                                        }
                                    echo '
                                    </td>
                                </tr>';
                            endforeach;
                            echo '
                        </tbody>
                    </table>
                </div>
                <div class="clearfix"></div>';
                if($this->group->enable_merge_transaction_alerts && count($unreconciled_deposits) >= 2){
                    echo '
                        <button class="btn btn-sm btn-accent m-btn m-btn--custom m-btn--icon confirmation_bulk_action" name="btnAction" value="merge_transaction_alerts">
                                <i class="la la-chain"></i>
                                Merge Transaction ALerts
                        </button>
                   ';
                }
                echo '
                <div class="clearfix"></div>';
                if($unreconciled_deposits):
                    echo'
                        <button class="btn btn-sm btn-info reconcile_confirmation" type="button" id="reconcile_confirmation" name=\'btnAction\' value=\'bulk_void\' data-toggle="confirmation" data-content="Are you sure you want to mark as reconciled ? This action is irreversible " data-placement="top"> <i class=\'la la-trash-o\'></i> Bulk mark as Reconciled</button>';
                endif;                

            echo form_close();
        } 
    }

    function ajax_get_admin_unreconciled_deposits_listing(){
        $unreconciled_deposits = $this->transaction_alerts_m->get_group_unreconciled_deposits($this->group_partner_bank_account_number_list,$this->group_partner_mobile_money_account_number_list);
        $bank_account_options = $this->bank_accounts_m->get_group_verified_partner_bank_account_options();
        $mobile_money_account_options = $this->mobile_money_accounts_m->get_group_verified_partner_mobile_account_options();
        if(!empty($unreconciled_deposits)){
            echo form_open('group/transaction_alerts/action', ' id="form"  class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed form-horizontal"');

                echo '
                <div class=\'table-responsive\'>
                    <table class="table table-condensed table-striped table-hover table-header-fixed table-searchable">
                        <thead>
                            <tr>';
                            if($this->group->enable_merge_transaction_alerts && count($unreconciled_deposits) >= 2){
                                echo '<th width=\'2%\'>
                                        <input type="checkbox" name="check" value="all" class="check_all">
                                    </th>';
                            }
                            echo '
                                <th width=\'2%\'>
                                    #
                                </th>
                                <th width="20%">
                                    Date
                                </th>
                                <th width="25%">
                                    Account
                                </th>
                                <th width="25%">
                                    Details
                                </th>
                                <th class="text-right">
                                    Amount ('.$this->group_currency.')
                                </th>
                                <th>
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>';
                            echo "
                            <tr id='unreconciled_deposits_loading_row'>
                                <td colspan='6'></td>
                            </tr>";
                            $count=1; foreach($unreconciled_deposits as $unreconciled_deposit):
                                $account_details = isset($bank_account_options[$unreconciled_deposit->account_number])?$bank_account_options[$unreconciled_deposit->account_number]:(isset($mobile_money_account_options[$unreconciled_deposit->account_number])?$mobile_money_account_options[$unreconciled_deposit->account_number]:'');
                                echo '
                                <tr id="unreconciled_deposit_row_'.$unreconciled_deposit->id.'"';
                                    if($unreconciled_deposit->reconciled){
                                        echo ' class="success" ';
                                    }
                                    echo '>';
                                    if($this->group->enable_merge_transaction_alerts && count($unreconciled_deposits) >= 2){
                                        echo '<td><input name=\'action_to[]\' type=\'checkbox\' class=\'checkboxes\' value="'.$unreconciled_deposit->id.'"/></td>';
                                    }

                                    echo '
                                    <td class=\'unreconciled_deposit_count\'>'.$count++.'</td>
                                    <td>
                                        '.timestamp_to_date($unreconciled_deposit->transaction_date).'<br/>
                                        <small><strong>Delivered On:</strong>'.timestamp_to_date_and_time($unreconciled_deposit->created_on).'</small><br/>
                                        <small><strong>Modified On:</strong>'.timestamp_to_date_and_time($unreconciled_deposit->modified_on).'</small>
                                    </td>
                                    <td>'. $account_details.'</td>
                                    <td>
                                        '.$unreconciled_deposit->particulars.' <small><a class=\'toggle_transaction_alert_details\'>More..</a></small>
                                        <div class="transaction_alert_details">
                                            '.$unreconciled_deposit->description.'
                                        </div>
                                    </td>
                                    <td class="text-right">'.number_to_currency($unreconciled_deposit->amount).'</td>
                                    <td class=\'reconcile_action\'>';
                                        if($unreconciled_deposit->reconciled){
                                            echo '<span class="label label-sm label-success"> Reconciled </span>';
                                        }else{
                                            echo '
                                            <a data-toggle="modal" data-content="#reconcile_deposit" data-title="Reconcile Deposit" data-id="'.$unreconciled_deposit->id.'" id="'.$unreconciled_deposit->id.'"  href="#" class="btn btn-xs full_width_inline reconcile_deposit  blue">
                                                <i class="fa fa-pencil"></i> Reconcile &nbsp;&nbsp; 
                                            </a>';
                                        }
                                        if($this->group->enable_bulk_transaction_alerts_reconciliation){
                                            echo '
                                            <a href="'.site_url('group/deposits/upload_payments/'.$unreconciled_deposit->id).'" data-toggle="" data-content="" data-title="Bulk Reconcile Deposit" data-id="'.$unreconciled_deposit->id.'" id="'.$unreconciled_deposit->id.'"  href="#" class="btn btn-xs   blue">
                                                <i class="fa fa-pencil"></i> Reconcile via Upload &nbsp;&nbsp; 
                                            </a>';
                                        }
                                    echo '
                                    </td>
                                </tr>';
                            endforeach;
                            echo '
                        </tbody>
                    </table>
                </div>
                <div class="clearfix"></div>';
                if($this->group->enable_merge_transaction_alerts && count($unreconciled_deposits) >= 2){
                    echo '
                    <button class=\'btn btn-sm btn-success confirmation_bulk_action\' name=\'btnAction\' value=\'merge_transaction_alerts\' data-toggle=\'confirmation\' data-placement=\'top\'> <i class=\'fa fa-code-fork\'></i>Merge Transaction ALerts</button>';
                            echo '&nbsp; &nbsp;';
                }
            echo form_close();
        }else{
            echo'
            <div class="alert alert-info">
                <h4 class="block">'.($this->lang->line('no_records_to_display')?:"Information! No records to display").'</h4>
                <p>
                    No unreconciled deposits to display.
                </p>
            </div>';
        } 
    }

    function reconcile_withdrawals(){
        $this->session->unset_userdata('timestamp');
        $this->data['banks'] = $this->banks_m->get_group_bank_options();
        $this->data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $this->data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $this->data['to_account_options'] = $this->accounts_m->get_active_group_account_options(FALSE);
        $this->data['bank_loan_options'] = $this->bank_loans_m->get_group_bank_loan_options();
        $this->data['contribution_options'] = $this->contributions_m->get_active_group_contribution_options();
        $this->data['asset_options'] = $this->assets_m->get_group_asset_options();
        $this->data['asset_category_options'] = $this->asset_categories_m->get_group_asset_category_options();
        $this->data['money_market_investment_options'] = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
        $this->data['expense_category_options'] = $this->expense_categories_m->get_group_expense_category_options();
        $this->data['bank_account_options'] = $this->bank_accounts_m->get_group_verified_partner_bank_account_options();
        $this->data['withdrawal_for_options'] = $this->transactions->withdrawal_for_options;
        $this->data['account_options'] = $this->accounts_m->get_active_group_account_options(FALSE);
        $this->data['interest_types'] = $this->loan->interest_types;
        $this->data['loan_interest_rate_per'] = $this->loan->loan_interest_rate_per;
        $this->data['loan_grace_periods'] = $this->loan->loan_grace_periods;
        $this->data['loan_days'] = $this->loan->loan_days;
        $this->data['sms_template_default'] = $this->loan->sms_template_default;
        $this->data['late_loan_payment_fine_types'] = $this->loan->late_loan_payment_fine_types;
        $this->data['late_payments_fine_frequency'] = $this->loan->late_payments_fine_frequency;
        $this->data['fixed_amount_fine_frequency_on'] = $this->loan->fixed_amount_fine_frequency_on;
        $this->data['percentage_fine_on'] = $this->loan->percentage_fine_on;
        $this->data['one_off_fine_types'] = $this->loan->one_off_fine_types;
        $this->data['one_off_percentage_rate_on'] = $this->loan->one_off_percentage_rate_on;
        $this->data['loan_processing_fee_types'] = $this->loan->loan_processing_fee_types;
        $this->data['loan_processing_fee_percentage_charged_on'] = $this->loan->loan_processing_fee_percentage_charged_on;
        preg_match_all("/\[[^\]]*\]/", $this->data['sms_template_default'],$placeholders);
        $this->data['placeholders'] = isset($placeholders[0])?$placeholders[0]:array();
        $this->data['placeholders'] = isset($placeholders[0])?$placeholders[0]:array();
        $this->data['invoice_days'] = $this->contribution_invoices->invoice_days;
        $this->data['month_days'] = $this->contribution_invoices->month_days;
        $this->data['week_days'] = $this->contribution_invoices->week_days;
        $this->data['days_of_the_month'] = $this->contribution_invoices->days_of_the_month;
        $this->data['every_two_week_days'] = $this->contribution_invoices->every_two_week_days;
        $this->data['months'] = $this->contribution_invoices->months;
        $this->data['starting_months'] = $this->contribution_invoices->starting_months;
        $this->data['week_numbers'] = $this->contribution_invoices->week_numbers;
        $this->data['contribution_frequency_options'] = $this->contribution_invoices->contribution_frequency_options;
        $this->data['contribution_type_options'] = $this->contribution_invoices->contribution_type_options;
        $this->data['sms_template_default'] = $this->sms_template_default;
        $this->data['fine_types'] = $this->contribution_invoices->fine_types;
        $this->data['fine_chargeable_on_options'] = $this->contribution_invoices->fine_chargeable_on_options;
        $this->data['fine_frequency_options'] = $this->contribution_invoices->fine_frequency_options;
        $this->data['fine_mode_options'] = $this->contribution_invoices->fine_mode_options;
        $this->data['fine_limit_options'] = $this->contribution_invoices->fine_limit_options;
        $this->data['percentage_fine_on_options'] = $this->contribution_invoices->percentage_fine_on_options;
        $this->data['group_role_options'] = $this->group_roles_m->get_group_role_options();
        //$this->data['unreconciled_withdrawals'] = $this->transaction_alerts_m->get_group_unreconciled_withdrawals($this->group_partner_bank_account_number_list);
        $this->template->title('Reconcile Withdrawals')->build('group/reconcile_withdrawals',$this->data);
    }


    function ajax_get_unreconciled_withdrawals_listing(){
        $unreconciled_withdrawals = $this->transaction_alerts_m->get_group_unreconciled_withdrawals($this->group_partner_bank_account_number_list,$this->group_partner_mobile_money_account_number_list);
        $bank_account_options = $this->bank_accounts_m->get_group_verified_partner_bank_account_options();
        $mobile_money_account_options = $this->mobile_money_accounts_m->get_group_verified_partner_mobile_account_options();
        if(empty($unreconciled_withdrawals)){
            echo'
                <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    </button>
                    <strong>'.translate('Information!').' </strong>'.translate('No unreconciled withdrawals to display.').'
                </div>
            ';
        }else{
            echo form_open('group/transaction_alerts/action', ' id="form"  class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed form-horizontal"');
            echo '
                <div class="table-responsive">
                    <table class="table table-condensed table-striped table-hover table-header-fixed table-searchable">
                        <thead>
                            <tr>';
                            if($this->group->enable_merge_transaction_alerts && count($unreconciled_withdrawals) >= 2){
                                echo '
                                    <th width=\'2%\'>
                                        <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                            <input type="checkbox" name="check" value="all" class="check_all">
                                            <span></span>
                                        </label>
                                    </th>
                                ';
                            }
                            echo '
                                <th width=\'2%\'>
                                    #
                                </th>
                                <th width="20%">
                                    Date
                                </th>
                                <th width="25%">
                                    Account
                                </th>
                                <th width="25%">
                                    Details
                                </th>
                                <th class="text-right">
                                    Amount ('.$this->group_currency.')
                                </th>
                                <th>
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>';
                            echo "
                            <tr id='unreconciled_withdrawals_loading_row' style='display:none;'>
                                <td colspan='6'></td>
                            </tr>";
                            $count=1; foreach($unreconciled_withdrawals as $unreconciled_withdrawal):
                                $account_details = isset($bank_account_options[$unreconciled_withdrawal->account_number])?$bank_account_options[$unreconciled_withdrawal->account_number]:(isset($mobile_money_account_options[$unreconciled_withdrawal->account_number])?$mobile_money_account_options[$unreconciled_withdrawal->account_number]:'');
                                echo '
                                <tr id="unreconciled_withdrawal_row_'.$unreconciled_withdrawal->id.'"';
                                    if($unreconciled_withdrawal->reconciled){
                                        echo ' class="success" ';
                                    }
                                    echo '>';
                                    if($this->group->enable_merge_transaction_alerts && count($unreconciled_withdrawals) >= 2){
                                        echo ' 
                                            <td>
                                                <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                                    <input name="action_to[]"" type="checkbox" class="checkboxes" value="'.$unreconciled_withdrawal->id.'"/>
                                                    <span></span>
                                                </label>
                                            </td>
                                        ';
                                    }


                                    echo '
                                    <td><input name=\'action_to_reconcile\' type="checkbox" class="action_to_reconcile checkboxes" value="'.$unreconciled_withdrawal->id.'" /></td>
                                    <td>
                                        '.timestamp_to_date($unreconciled_withdrawal->transaction_date).'<br/>
                                        <small><strong>Delivered On: </strong>'.timestamp_to_date_and_time($unreconciled_withdrawal->created_on).'</small>
                                    </td>
                                    <td>'. $account_details.'</td>
                                    <td>
                                        '.$unreconciled_withdrawal->particulars.' <small><a class=\'toggle_transaction_alert_details text-primary\' style="cursor: pointer;">More..</a></small>
                                        <div class="transaction_alert_details" style="display:none;">
                                            '.$unreconciled_withdrawal->description.'
                                        </div>
                                    </td>
                                    <td class="text-right">'.number_to_currency($unreconciled_withdrawal->amount).'</td>
                                    <td class="reconcile_action">';
                                        if($unreconciled_withdrawal->reconciled){
                                            echo '<span class="label label-sm label-success"> Reconciled </span>';
                                        }else{
                                            echo '
                                                <a class="btn btn-sm btn-outline-accent m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air reconcile_withdrawal_pop_up reconcile_withdrawal" data-toggle="modal" data-target="#reconcile_withdrawal_pop_up" id="'.$unreconciled_withdrawal->id.'" data-backdrop="static" data-keyboard="false">
                                                    <span>
                                                        <i class="la la-pencil"></i>
                                                        <span>Reconcile</span>
                                                    </span>
                                                </a>
                                           ';
                                        }
                                    echo '
                                    </td>
                                </tr>';
                            endforeach;
                            echo '
                        </tbody>
                    </table>
                </div>
                <div class="clearfix"></div>';
                if($this->group->enable_merge_transaction_alerts && count($unreconciled_withdrawals) >= 2){
                    echo '
                        <button class="btn btn-sm btn-accent m-btn m-btn--custom m-btn--icon confirmation_bulk_action" name="btnAction" value="merge_transaction_alerts">
                                <i class="la la-chain"></i>
                                Merge Transaction ALerts
                        </button>
                   ';
                }
                echo '
                <div class="clearfix"></div>';
                if($unreconciled_withdrawals):
                    echo'
                        <button class="btn btn-sm btn-info reconcile_confirmation" type="button" id="reconcile_confirmation" name=\'btnAction\' value=\'bulk_void\' data-toggle="confirmation" data-content="Are you sure you want to mark as reconciled ? This action is irreversible " data-placement="top"> <i class=\'la la-trash-o\'></i> Bulk mark as Reconciled</button>';
                endif;   
            echo form_close();
        }
    }

    function reconciled_deposits($type = ''){
        $transaction_alert_id = $this->input->get('transaction_alert');
        $filter_parameters = array(
            'transaction_alert_id' => $transaction_alert_id
        );
        $this->data['bank_account_options'] = $this->bank_accounts_m->get_group_verified_partner_bank_account_options();
        $this->data['reconciled_deposits'] = $this->transaction_alerts_m->get_group_reconciled_deposits($this->group_partner_bank_account_number_list,$filter_parameters);
        // $ids = array(616159,643055,626591,619101,617828,616971,615172);
        // foreach ($this->data['reconciled_deposits'] as $key => $deposit) {
        //     $posts = $this->deposits_m->get_group_deposits($this->group->id,array('transaction_alert_id' => $deposit->id));
        //     if(count($posts)>1){
        //         //if($posts[0]->amount == $posts[1]->amount){
        //             if(!in_array($deposit->id, $ids)){
        //                 print_r($posts);die;
        //             }
        //         //}
        //     }
        // }
        $this->template->title(translate('Reconciled Deposits'))->build('group/reconciled_deposits',$this->data);
    }

    function unreconciled_deposits($type = ''){
        $transaction_alert_id = $this->input->get('transaction_alert');
        $filter_parameters = array(
            'transaction_alert_id' => $transaction_alert_id
        );
        $this->data['bank_account_options'] = $this->bank_accounts_m->get_group_verified_partner_bank_account_options();
        $this->data['unreconciled_deposits'] = $this->transaction_alerts_m->get_group_unreconciled_deposits($this->group_partner_bank_account_number_list,$this->group_partner_mobile_money_account_number_list);
        $this->template->set_layout('default_full_width.html')->title('Unreconciled Deposits')->build('group/unreconciled_deposits',$this->data);
    }


    function unreconciled_withdrawals($type = ''){
        $transaction_alert_id = $this->input->get('transaction_alert');
        $filter_parameters = array(
            'transaction_alert_id' => $transaction_alert_id
        );
        $this->data['bank_account_options'] = $this->bank_accounts_m->get_group_verified_partner_bank_account_options();
        $this->data['unreconciled_withdrawals'] = $this->transaction_alerts_m->get_group_unreconciled_withdrawals($this->group_partner_bank_account_number_list,$this->group_partner_mobile_money_account_number_list);
        $this->template->set_layout('default_full_width.html')->title('Unreconciled Withdrawals')->build('group/unreconciled_withdrawals',$this->data);
    }


    function reconciled_withdrawals($type = ''){
        $transaction_alert_id = $this->input->get('transaction_alert');
        $filter_parameters = array(
            'transaction_alert_id' => $transaction_alert_id
        );
        $this->data['bank_account_options'] = $this->bank_accounts_m->get_group_verified_partner_bank_account_options();
        $this->data['reconciled_withdrawals'] = $this->transaction_alerts_m->get_group_reconciled_withdrawals($this->group_partner_bank_account_number_list,$filter_parameters);
        //print_r($this->data);die;
        $this->template->title(translate('Reconciled Withdrawals'))->build('group/reconciled_withdrawals',$this->data);
    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_deactivate'){
            for($i=0;$i<count($action_to);$i++){
                $this->deactivate($action_to[$i],FALSE);
            }
        }elseif($action == 'merge_transaction_alerts'){
            if(is_array($action_to) && count($action_to) >= 2){
                if($this->merge_transaction_alerts($action_to)){
                    $this->session->set_flashdata('success','Transaction Alerts Successfully Merged');
                }else{
                    $this->session->set_flashdata('error','Could not merge transaction alerts');
                }
            }else{
                $this->session->set_flashdata('error','Please select atleats 2 transaction alerts to merge');
            }
        }

        if($this->agent->referrer()){
            redirect($this->agent->referrer());
        }else{
            redirect('group/transaction_alerts/unreconciled_deposits');
        }
    }

    function unmerge_transaction($id=0){
        $alerts = $this->transaction_alerts_m->get_merged_transaction_alerts_by_merge_id($id);
        if($alerts){
            foreach($alerts as $alert){
                $update = array(
                    'merged_transaction_alert_id' => 0,
                    'is_merged' => 0,
                );
                $this->transaction_alerts_m->update($alert->id,$update);
            }
            $update = array(
                'active' => 0,
                'modified_on' => time(),
            );
            if($this->transaction_alerts_m->update($id,$update)){
                $this->session->set_flashdata('success','Transaction alert successfully unmerged');
            }else{
                $this->session->set_flashdata('error','Could not unmerge transaction alert');
            }
        }else{
            $this->session->set_flashdata('error','Could not unmerge transaction alert');
        }
        redirect('group/transaction_alerts/unreconciled_deposits');
    }

    function merge_transaction_alerts($ids = array()){
        $i = 0;
        $total_amount = 0;
        $transaction_alert = $this->transaction_alerts_m->get($ids[0]);
        $new_transaction_alert = (array)$transaction_alert;
        unset($new_transaction_alert['id']);
        $account_number = $transaction_alert->account_number;

        $new_transaction_alert_id =  $this->transaction_alerts_m->insert($new_transaction_alert);
        foreach ($ids as $key => $id) {
            $transaction_alert = $this->transaction_alerts_m->get($id);
            if($transaction_alert->reconciled || $transaction_alert->is_merged || ($transaction_alert->account_number != $account_number)){
                //skip
            }else{
                if($this->transaction_alerts_m->update($id,array('merged_transaction_alert_id' => $new_transaction_alert_id,'is_merged' => 1,'modified_on' => time(),'modified_by' => $this->user->id))){
                    $i++;
                    $total_amount += $transaction_alert->amount;
                }
            }
        }

        if($i){ 
            $input = array(
                'amount' => $total_amount,
                'modified_on' => time(),
                'modified_by' => $this->user->id,
            );
        }else{//no transactions alerts were merged
            $input = array(
                'active' => 0,
                'modified_on' => time(),
                'modified_by' => $this->user->id,
            );
        }

        if($this->transaction_alerts_m->update($new_transaction_alert_id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
        
    }

    function deactivate($id = 0){
        $input = array(
            'active' => 0,
            'modified_by' => $this->user->id,
            'modified_on' => time(),
        );
        if($result = $this->transaction_alerts_m->update($id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function ajax_get(){
        $this->output->enable_profiler(FALSE);
        $id = $this->input->post('id');
        if($transaction_alert = $this->transaction_alerts_m->get($id)){
            $account_ids_account_number_as_keys_options = $this->accounts_m->get_group_account_ids_account_number_as_keys_options();
            $transaction_alert->formatted_transaction_date = timestamp_to_date($transaction_alert->transaction_date);
            $transaction_alert->formatted_transaction_date_value = timestamp_to_date($transaction_alert->transaction_date,TRUE);
            $transaction_alert->formatted_amount = number_to_currency($transaction_alert->amount);
            $transaction_alert->account_id = isset($account_ids_account_number_as_keys_options[$transaction_alert->account_number])?$account_ids_account_number_as_keys_options[$transaction_alert->account_number]:'';
            echo json_encode($transaction_alert);
        }else{
            echo 'error';
        }
    }

    // function ajax_reconcile_deposit(){
    //     $error_message = '<strong>Error!</strong> There were errors found on the form <ul>';
    //     $transaction_alert_id = $this->input->post('transaction_alert_id');
    //     $enable_notifications = $this->input->post('enable_notifications');
    //     $transaction_alert = $this->transaction_alerts_m->get($transaction_alert_id);
    //     $deposit_fors = $this->input->post('deposit_fors');
    //     $contributions = $this->input->post('contributions');
    //     $fine_categories = $this->input->post('fine_categories');
    //     $members = $this->input->post('members');
    //     $debtors = $this->input->post('debtors');
    //     $income_categories = $this->input->post('income_categories');
    //     $depositors = $this->input->post('depositors');
    //     $amounts = $this->input->post('amounts');
    //     $amount_payables = $this->input->post('amount_payables');
    //     $descriptions = $this->input->post('descriptions');
    //     $from_account_ids = $this->input->post('from_account_ids');
    //     $number_of_shares_solds = $this->input->post('number_of_shares_solds');
    //     $price_per_shares = $this->input->post('price_per_shares');
    //     $stock_ids = $this->input->post('stock_ids');
    //     $asset_ids = $this->input->post('asset_ids');
    //     $money_market_investment_ids = $this->input->post('money_market_investment_ids');
    //     $loans = $this->input->post('loans');
    //     $external_loans = $this->input->post('external_loans');
    //     $account_id = $this->bank_accounts_m->get_group_bank_account_id_by_account_number($transaction_alert->account_number);
    //     if($account_id){
    //       $account_id = 'bank-'.$account_id;  
    //     }else{
    //         $account_id = $this->mobile_money_accounts_m->get_group_mobile_money_account_id_by_account_number($transaction_alert->account_number);
    //         if($account_id){
    //            $account_id = 'mobile-'.$account_id; 
    //         }else{
                
    //         }
    //     }
        
    //     $amount_reconciled = 0;
    //     if(isset($amounts)):
    //         foreach($amounts as $amount){
    //             $amount_reconciled+=valid_currency($amount);
    //         }
    //     endif;
    //     if(isset($deposit_fors)&&$transaction_alert&&$account_id){
    //         if($transaction_alert->reconciled==1){
    //             echo "Transaction already reconciled";
    //             die;
    //         }else{
    //             $entries_are_valid = TRUE;
    //             $count = count($deposit_fors)-1;
    //             for($i=0;$i<=$count;$i++):
    //                 if(isset($deposit_fors[$i])){
    //                     if($deposit_fors[$i]==''){
    //                         $entries_are_valid = FALSE;
    //                     }else{
    //                         //do nothing for now
    //                     }
    //                 }


    //                 //Members
    //                 if(isset($members[$i])){
    //                     if($members[$i]==''){
    //                         $entries_are_valid = FALSE;
    //                     }else{
    //                         if(is_numeric($members[$i])){
    //                             //do nothing for now
    //                         }else{
    //                             $entries_are_valid = FALSE;
    //                         }
    //                     }
    //                 }
    //                 //Members
    //                 if(isset($debtors[$i])){
    //                     if($debtors[$i]==''){
    //                         $entries_are_valid = FALSE;
    //                     }else{
    //                         if(is_numeric($debtors[$i])){
    //                             //do nothing for now
    //                         }else{
    //                             $entries_are_valid = FALSE;
    //                         }
    //                     }
    //                 }

    //                 //Members
    //                 if(isset($loans[$i])){
    //                     if($loans[$i]==''){
    //                         $entries_are_valid = FALSE;
    //                     }else{
    //                         if(is_numeric($loans[$i])){
    //                             //do nothing for now
    //                         }else{
    //                             $entries_are_valid = FALSE;
    //                         }
    //                     }
    //                 }
    //                 //Members
    //                 if(isset($external_loans[$i])){
    //                     if($external_loans[$i]==''){
    //                         $entries_are_valid = FALSE;
    //                     }else{
    //                         if(is_numeric($external_loans[$i])){
    //                             //do nothing for now
    //                         }else{
    //                             $entries_are_valid = FALSE;
    //                         }
    //                     }
    //                 }

    //                 //Depositors
    //                 if(isset($depositors[$i])){
    //                     if($depositors[$i]==''){
    //                         $entries_are_valid = FALSE;
    //                     }else{
    //                         if(is_numeric($depositors[$i])){
    //                             //do nothing for now
    //                         }else{
    //                             $entries_are_valid = FALSE;
    //                         }
    //                     }
    //                 }

    //                 //Contributions
    //                 if(isset($income_categories[$i])){
    //                     if($income_categories[$i]==''){
    //                         $entries_are_valid = FALSE;
    //                     }else{
    //                         if(is_numeric($income_categories[$i])){
    //                             //do nothing for now
    //                         }else{
    //                             $entries_are_valid = FALSE;
    //                         }
    //                     }
    //                 }

    //                 //Contributions
    //                 if(isset($contributions[$i])){
    //                     if($contributions[$i]==''){
    //                         $entries_are_valid = FALSE;
    //                     }else{
    //                         if(is_numeric($contributions[$i])){
    //                             //do nothing for now
    //                         }else{
    //                             $entries_are_valid = FALSE;
    //                         }
    //                     }
    //                 }

    //                 //from account ids
    //                 if(isset($from_account_ids[$i])){
    //                     if($from_account_ids[$i]==''){
    //                         $entries_are_valid = FALSE;
    //                     }else{
    //                         //do nothing for now
    //                     }
    //                 }


    //                 if(isset($deposit_fors[$i])){
    //                     if($deposit_fors[$i]==3||$deposit_fors[$i]==6){
    //                         if($descriptions[$i]==''){
    //                             $entries_are_valid = FALSE;
    //                         }else{
    //                             //do nothing for now
    //                         }
    //                     }
    //                 }

    //                 //Fine category
    //                 if(isset($fine_categories[$i])){
    //                     if($fine_categories[$i]==''){
    //                         $entries_are_valid = FALSE;
    //                     }else{
    //                         //do nothing for now
    //                     }
    //                 }
    //                 //amounts
    //                 if(isset($amounts[$i])){
    //                     if($amounts[$i]==''){
    //                         $entries_are_valid = FALSE;
    //                     }else{
    //                         if(valid_currency($amounts[$i])){
    //                             //do nothing for now
    //                         }else{
    //                             $entries_are_valid = FALSE; 
    //                         }
    //                     }
    //                 }
    //                 //amounts  payables
    //                 if(isset($amount_payables[$i])){
    //                     if($amount_payables[$i]==''){
    //                         $entries_are_valid = FALSE;
    //                     }else{
    //                         if(valid_currency($amount_payables[$i])){
    //                             //do nothing for now
    //                         }else{
    //                             $entries_are_valid = FALSE; 
    //                         }
    //                     }
    //                 }
    //                 //price per shares
    //                 if(isset($price_per_shares[$i])){
    //                     if($price_per_shares[$i]==''){
    //                         $entries_are_valid = FALSE;
    //                     }else{
    //                         if(valid_currency($price_per_shares[$i])){
    //                             //do nothing for now
    //                         }else{
    //                             $entries_are_valid = FALSE; 
    //                         }
    //                     }
    //                 }
    //                 //number of shares sold
    //                 if(isset($number_of_shares_solds[$i])){
    //                     if($number_of_shares_solds[$i]==''){
    //                         $entries_are_valid = FALSE;
    //                     }else{
    //                         if(is_numeric($number_of_shares_solds[$i])){
    //                             //do nothing for now
    //                         }else{
    //                             $entries_are_valid = FALSE; 
    //                         }
    //                     }
    //                 }
    //                 //stock id
    //                 if(isset($stock_ids[$i])){
    //                     if($stock_ids[$i]==''){
    //                         $entries_are_valid = FALSE;
    //                     }else{
    //                         if(is_numeric($stock_ids[$i])){
    //                             //do nothing for now
    //                         }else{
    //                             $entries_are_valid = FALSE; 
    //                         }
    //                     }
    //                 }

    //                 //asset id
    //                 if(isset($asset_ids[$i])){
    //                     if($asset_ids[$i]==''){
    //                         $entries_are_valid = FALSE;
    //                     }else{
    //                         if(is_numeric($asset_ids[$i])){
    //                             //do nothing for now
    //                         }else{
    //                             $entries_are_valid = FALSE; 
    //                         }
    //                     }
    //                 }
    //                 //money market investment id
    //                 if(isset($money_market_investment_ids[$i])){
    //                     if($money_market_investment_ids[$i]==''){
    //                         $entries_are_valid = FALSE;
    //                     }else{
    //                         if(is_numeric($money_market_investment_ids[$i])){
    //                             //do nothing for now
    //                         }else{
    //                             $entries_are_valid = FALSE; 
    //                         }
    //                     }
    //                 }
    //             endfor;
    //         }
    //     }else{
    //        $entries_are_valid = FALSE; 
    //     }

    //     if($amount_reconciled==$transaction_alert->amount){

    //     }else{
    //         $error_message .= "<li>The amount reconciled has to be equal to the amount deposited</li>";
    //         $entries_are_valid = FALSE; 
    //     }
    //     if($entries_are_valid){
    //         //make entries
    //         $result = TRUE;
    //         $count = count($deposit_fors)-1;
    //         for($i=0;$i<=$count;$i++):
    //             if($deposit_fors[$i]==1){
    //                 //contribution payment
    //                 if($this->transactions->record_contribution_payment($this->group->id,$transaction_alert->transaction_date,$members[$i],$contributions[$i],$account_id,2,$descriptions[$i],valid_currency($amounts[$i]),$enable_notifications,$enable_notifications,$transaction_alert->id)){
    //                     //update transaction alerts
    //                     if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

    //                     }else{
    //                         $result = FALSE;
    //                     }
    //                 }else{
    //                     $result = FALSE;
    //                 }
    //             }else if($deposit_fors[$i]==2){
    //                 //fine payment
    //                 if($this->transactions->record_fine_payment($this->group->id,$transaction_alert->transaction_date,$members[$i],$fine_categories[$i],$account_id,2,$descriptions[$i],valid_currency($amounts[$i]),$enable_notifications,$enable_notifications,$transaction_alert->id)){
    //                     //update transaction alerts
    //                     if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

    //                     }else{
    //                         $result = FALSE;
    //                     }
    //                 }else{
    //                     $result = FALSE;
    //                 }
    //             }else if($deposit_fors[$i]==3){
    //                 //miscellaneous payment
    //                 if($this->transactions->record_miscellaneous_payment($this->group->id,$transaction_alert->transaction_date,$members[$i],$account_id,2,$descriptions[$i],valid_currency($amounts[$i]),$enable_notifications,$enable_notifications,$transaction_alert->id)){
    //                     //update transaction alerts
    //                     if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

    //                     }else{
    //                         $result = FALSE;
    //                     }
    //                 }else{
    //                     $result = FALSE;
    //                 }
    //             }else if($deposit_fors[$i]==4){
    //                 //income deposit
    //                 if($this->transactions->record_income_deposit($this->group->id,$transaction_alert->transaction_date,$depositors[$i],$income_categories[$i],$account_id,2,$descriptions[$i],valid_currency($amounts[$i]),$transaction_alert->id)){
    //                     //update transaction alerts
    //                     if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

    //                     }else{
    //                         $result = FALSE;
    //                     }
    //                 }else{
    //                     $result = FALSE;
    //                 }
    //             }else if($deposit_fors[$i]==5){
    //                 //loan repayment
    //                 $member = $this->members_m->get_group_member($members[$i]);
    //                 if($member){
    //                     if($this->loan->record_loan_repayment($this->group->id,$transaction_alert->transaction_date,$member,$loans[$i],$account_id,2,$descriptions[$i],valid_currency($amounts[$i]),$enable_notifications,$enable_notifications,$member,$member->user_id,$transaction_alert->id)){
    //                         //update transaction alerts
    //                         if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

    //                         }else{
    //                             $result = FALSE;
    //                         }
    //                     }else{
    //                         $result = FALSE;
    //                     }
    //                 }else{
    //                     $result = FALSE;
    //                 }
    //             }else if($deposit_fors[$i]==6){
    //                 //bank loan disbursement disbursement deposit
    //                 if($this->transactions->create_bank_loan($this->group->id,$descriptions[$i],valid_currency($amounts[$i]),$amount_payables[$i],$amount_payables[$i],$transaction_alert->transaction_date,$transaction_alert->transaction_date,$account_id,0,$transaction_alert->id)){
    //                     //update transaction alerts
    //                     if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

    //                     }else{
    //                         $result = FALSE;
    //                     }
    //                 }else{
    //                     $result = FALSE;
    //                 }
    //             }else if($deposit_fors[$i]==7){
    //                 //incoming bank transfer
    //                 if($this->transactions->record_account_transfer($this->group->id,$transaction_alert->transaction_date,$from_account_ids[$i],$account_id,valid_currency($amounts[$i]),$descriptions[$i],$transaction_alert->id)){
    //                     //update transaction alerts
    //                     //find identical withdrawal and mark it as reconciled just incase both accounts belong to the same group
    //                     if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){
    //                         if($transaction_alert = $this->transaction_alerts_m->get_group_matching_withdrawal_transaction_alert($this->group->id,$transaction_alert->transaction_date,$from_account_ids[$i],$account_id,valid_currency($amounts[$i]))){
    //                             if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

    //                             }else{
    //                                 return FALSE;
    //                             }
    //                         }else{

    //                         }
    //                     }else{
    //                         $result = FALSE;
    //                     }
    //                 }else{
    //                     $result = FALSE;
    //                 }
    //             }else if($deposit_fors[$i]==8){
    //                 //stock sales
    //                 $stock = $this->stocks_m->get_group_stock($stock_ids[$i]);
    //                 if($stock){
    //                     if($this->transactions->record_stock_sale($this->group->id,
    //                         $stock_ids[$i],$transaction_alert->transaction_date,$account_id,$number_of_shares_solds[$i],
    //                         valid_currency($price_per_shares[$i]),$stock->number_of_shares_sold,$transaction_alert->id)){
    //                         //update transaction alerts
    //                         if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

    //                         }else{
    //                             $result = FALSE;
    //                         }
    //                     }else{
    //                         $result = FALSE;
    //                     }
    //                 }else{
    //                     $result = FALSE;
    //                 }   
    //             }else if($deposit_fors[$i]==9){
    //                 //asset sale
    //                 if($this->transactions->record_asset_sale_deposit($this->group->id,$asset_ids[$i],$transaction_alert->transaction_date,$account_id,valid_currency($amounts[$i]),$transaction_alert->id)){
    //                     //update transaction alerts
    //                     if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

    //                     }else{
    //                         $result = FALSE;
    //                     }
    //                 }else{
    //                     $result = FALSE;
    //                 }
    //             }else if($deposit_fors[$i]==10){
    //                 //money market investment cash in
    //                 if($this->transactions->record_money_market_investment_cash_in_deposit($this->group->id,$money_market_investment_ids[$i],$transaction_alert->transaction_date,$account_id,valid_currency($amounts[$i]),$transaction_alert->id)){
    //                     //update transaction alerts
    //                     if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

    //                     }else{
    //                         $result = FALSE;
    //                     }
    //                 }else{
    //                     $result = FALSE;
    //                 }
    //             }else if($deposit_fors[$i]==11){
    //                 //money market investment cash in
    //                 if($this->loan->calculate_and_record_loan_processing_fee($loans[$i],FALSE,FALSE,$transaction_alert->id,$transaction_alert->amount)){
    //                     //update transaction alerts
    //                     if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

    //                     }else{
    //                         $result = FALSE;
    //                     }
    //                 }else{
    //                     $result = FALSE;
    //                 }
    //             }else if($deposit_fors[$i]==12){
    //                 //money market investment cash in
    //                 $debtor = $this->debtors_m->get($debtors[$i],$this->group->id);;
    //                 if($debtor){
    //                     if($this->loan->record_debtor_loan_repayment(
    //                                     $this->group->id,
    //                                     $transaction_alert->transaction_date,
    //                                     $debtor,
    //                                     $external_loans[$i],
    //                                     $account_id,
    //                                     1,
    //                                     '',
    //                                     currency($amounts[$i]),
    //                                     $enable_notifications,
    //                                     $enable_notifications,
    //                                     $this->user,
    //                                     $transaction_alert->id
    //                                 )){
    //                         //update transaction alerts
    //                         if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

    //                         }else{
    //                             $result = FALSE;
    //                         }
    //                     }else{
    //                         $result = FALSE;
    //                     }
    //                 }else{
    //                     $result = FALSE;
    //                 }
    //             }
    //         endfor;
    //         if($result){
    //             echo 'success';
    //         }else{
    //             echo 'Something went wrong';
    //         }
    //     }else{
    //         echo $error_message .= "<li>All fields on the form are required. Kindly review your entries and try again</li></ul>";
    //     }
    // }
    
    function bulk_mark_as_reconciled(){
        $alerts_reconciled = 0;
        $failed_to_reconcile = 0;
        $error_message = '';
        $success_message = '';
        $response = array();
        $transaction_alert_ids = $this->input->post('transaction_alert_ids');
        if(empty($transaction_alert_ids)){
            $response = array(
                'status' => 0,
                'error_message' => 'Please choose transaction alerts to reconcile',
                'html' => validation_errors(),
            );
        }else{
            $count = count($transaction_alert_ids);          
            for ($i=0; $i < $count; $i++){
                if($this->transaction_alerts_m->get($transaction_alert_ids[$i])){
                    $input = array(
                        'system_bulk_reconciled'=>1,
                        'reconciled' => 1,
                        'marked_as_reconciled' => 1,
                        'modified_by'=>$this->user->id,
                        'modified_on' => time()
                    );
                    if($result = $this->transaction_alerts_m->update($transaction_alert_ids[$i],$input)){
                        $success_message = 'Transactions reconciled successfully';
                        $alerts_reconciled++;
                    }else{
                        $error_message = 'Could not reconcile alert';
                        $failed_to_reconcile++;
                    }
                }else{
                    $error_message = 'Alert does not exist';
                    $failed_to_reconcile++;
                }
            } 
            if($alerts_reconciled){
                $response = array(
                    'status' => 1,
                    'message' => $alerts_reconciled.' Transactions reconciled successfully ',
                    'error_message'=>$error_message,
                    'success_message' => $success_message,
                );
            }

            if($failed_to_reconcile){
                $response = array(
                    'status' => 1,
                    'message' => $failed_to_reconcile.' failed to reconciled ',
                    'error_message'=>$error_message,
                    'success_message' => $success_message,
                );
            }      
        }
        echo json_encode($response);
    }

    function ajax_reconcile_deposit(){
        $error_message = '<strong>Error!</strong> There were errors found on the form <ul>';
        $transaction_alert_id = $this->input->post('transaction_alert_id');
        $enable_notifications = $this->input->post('enable_notifications');
        $transaction_alert = $this->transaction_alerts_m->get($transaction_alert_id);
        $deposit_fors = $this->input->post('deposit_fors');
        $contributions = $this->input->post('contributions');
        $fine_categories = $this->input->post('fine_categories');
        $members = $this->input->post('members');
        $debtors = $this->input->post('debtors');
        $income_categories = $this->input->post('income_categories');
        $depositors = $this->input->post('depositors');
        $amounts = $this->input->post('amounts');
        $amount_payables = $this->input->post('amount_payables');
        $descriptions = $this->input->post('descriptions');
        $from_account_ids = $this->input->post('from_account_ids');
        $number_of_shares_solds = $this->input->post('number_of_shares_solds');
        $price_per_shares = $this->input->post('price_per_shares');
        $stock_ids = $this->input->post('stock_ids');
        $asset_ids = $this->input->post('asset_ids');
        $money_market_investment_ids = $this->input->post('money_market_investment_ids');
        $loans = $this->input->post('loans');
        $external_loans = $this->input->post('external_loans');
        $account_id = $this->bank_accounts_m->get_group_bank_account_id_by_account_number($transaction_alert->account_number);
        if($account_id){
          $account_id = 'bank-'.$account_id;  
        }else{
            $account_id = $this->mobile_money_accounts_m->get_group_mobile_money_account_id_by_account_number($transaction_alert->account_number);
            if($account_id){
               $account_id = 'mobile-'.$account_id; 
            }else{
                $account_id = $this->bank_accounts_m->get_group_bank_account_id_by_account_number('0'.$transaction_alert->account_number);
                if($account_id){
                    $account_id = 'bank-'.$account_id;
                }
            }
        }
        
        
        $amount_reconciled = 0;
        if(isset($amounts)):
            foreach($amounts as $amount){
                $amount_reconciled+=valid_currency($amount);
            }
        endif;
        if(isset($deposit_fors)&&$transaction_alert&&$account_id){
            if($transaction_alert->reconciled==1){
                echo "Transaction already reconciled";
                die;
            }else{
                $entries_are_valid = TRUE;
                $count = count($deposit_fors)-1;
                for($i=0;$i<=$count;$i++):
                    if(isset($deposit_fors[$i])){
                        if($deposit_fors[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            //do nothing for now
                        }
                    }


                    //Members
                    if(isset($members[$i])){
                        if($members[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($members[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE;
                            }
                        }
                    }
                    //Members
                    if(isset($debtors[$i])){
                        if($debtors[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($debtors[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE;
                            }
                        }
                    }

                    //Members
                    if(isset($loans[$i])){
                        if($loans[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($loans[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE;
                            }
                        }
                    }
                    //Members
                    if(isset($external_loans[$i])){
                        if($external_loans[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($external_loans[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE;
                            }
                        }
                    }

                    //Depositors
                    if(isset($depositors[$i])){
                        if($depositors[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($depositors[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE;
                            }
                        }
                    }

                    //Contributions
                    if(isset($income_categories[$i])){
                        if($income_categories[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($income_categories[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE;
                            }
                        }
                    }

                    //Contributions
                    if(isset($contributions[$i])){
                        if($contributions[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($contributions[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE;
                            }
                        }
                    }

                    //from account ids
                    if(isset($from_account_ids[$i])){
                        if($from_account_ids[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            //do nothing for now
                        }
                    }


                    if(isset($deposit_fors[$i])){
                        if($deposit_fors[$i]==3||$deposit_fors[$i]==6){
                            if($descriptions[$i]==''){
                                $entries_are_valid = FALSE;
                            }else{
                                //do nothing for now
                            }
                        }
                    }

                    //Fine category
                    if(isset($fine_categories[$i])){
                        if($fine_categories[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            //do nothing for now
                        }
                    }
                    //amounts
                    if(isset($amounts[$i])){
                        if($amounts[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(valid_currency($amounts[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE; 
                            }
                        }
                    }
                    //amounts  payables
                    if(isset($amount_payables[$i])){
                        if($amount_payables[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(valid_currency($amount_payables[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE; 
                            }
                        }
                    }
                    //price per shares
                    if(isset($price_per_shares[$i])){
                        if($price_per_shares[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(valid_currency($price_per_shares[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE; 
                            }
                        }
                    }
                    //number of shares sold
                    if(isset($number_of_shares_solds[$i])){
                        if($number_of_shares_solds[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($number_of_shares_solds[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE; 
                            }
                        }
                    }
                    //stock id
                    if(isset($stock_ids[$i])){
                        if($stock_ids[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($stock_ids[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE; 
                            }
                        }
                    }

                    //asset id
                    if(isset($asset_ids[$i])){
                        if($asset_ids[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($asset_ids[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE; 
                            }
                        }
                    }
                    //money market investment id
                    if(isset($money_market_investment_ids[$i])){
                        if($money_market_investment_ids[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($money_market_investment_ids[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE; 
                            }
                        }
                    }
                endfor;
            }
        }else{
           $entries_are_valid = FALSE; 
        }

        if($amount_reconciled==$transaction_alert->amount){

        }else{
            $error_message .= "<li>The amount reconciled has to be equal to the amount deposited</li>";
            $entries_are_valid = FALSE; 
        }
        $error = '';
        if($entries_are_valid){
            //make entries
            $result = TRUE;
            $count = count($deposit_fors)-1;
            for($i=0;$i<=$count;$i++):
                if($deposit_fors[$i]==1){
                    //contribution payment
                    if($this->transactions->record_contribution_payment($this->group->id,$transaction_alert->transaction_date,$members[$i],$contributions[$i],$account_id,2,$descriptions[$i],valid_currency($amounts[$i]),$enable_notifications,$enable_notifications,$transaction_alert->id,'',$transaction_alert)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                        }else{
                            $error = 'Could not mark transation alert';
                            $result = FALSE;
                        }
                    }else{
                        $error = $this->session->flashdata('error');
                        $result = FALSE;
                    }
                }else if($deposit_fors[$i]==2){
                    //fine payment
                    if($this->transactions->record_fine_payment($this->group->id,$transaction_alert->transaction_date,$members[$i],$fine_categories[$i],$account_id,2,$descriptions[$i],valid_currency($amounts[$i]),$enable_notifications,$enable_notifications,$transaction_alert->id,'',$transaction_alert)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else if($deposit_fors[$i]==3){
                    //miscellaneous payment
                    if($this->transactions->record_miscellaneous_payment($this->group->id,$transaction_alert->transaction_date,$members[$i],$account_id,2,$descriptions[$i],valid_currency($amounts[$i]),$enable_notifications,$enable_notifications,$transaction_alert->id,'',$transaction_alert)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else if($deposit_fors[$i]==4){
                    //income deposit
                    if($this->transactions->record_income_deposit($this->group->id,$transaction_alert->transaction_date,$depositors[$i],$income_categories[$i],$account_id,2,$descriptions[$i],valid_currency($amounts[$i]),$transaction_alert->id)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else if($deposit_fors[$i]==5){
                    //loan repayment
                    $member = $this->members_m->get_group_member($members[$i]);
                    if($member){
                        if($this->loan->record_loan_repayment($this->group->id,$transaction_alert->transaction_date,$member,$loans[$i],$account_id,2,$descriptions[$i]??'',valid_currency($amounts[$i]),$enable_notifications,$enable_notifications,$member,$member->user_id,$transaction_alert->id)){
                            //update transaction alerts
                            if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                            }else{
                                $result = FALSE;
                            }
                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else if($deposit_fors[$i]==6){
                    //bank loan disbursement disbursement deposit
                    if($this->transactions->create_bank_loan($this->group->id,$descriptions[$i],valid_currency($amounts[$i]),$amount_payables[$i],$amount_payables[$i],$transaction_alert->transaction_date,$transaction_alert->transaction_date,$account_id,0,$transaction_alert->id)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else if($deposit_fors[$i]==7){
                    //incoming bank transfer
                    if($this->transactions->record_account_transfer($this->group->id,$transaction_alert->transaction_date,$from_account_ids[$i],$account_id,valid_currency($amounts[$i]),$descriptions[$i],$transaction_alert->id)){
                        //update transaction alerts
                        //find identical withdrawal and mark it as reconciled just incase both accounts belong to the same group
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){
                            if($transaction_alert = $this->transaction_alerts_m->get_group_matching_withdrawal_transaction_alert($this->group->id,$transaction_alert->transaction_date,$from_account_ids[$i],$account_id,valid_currency($amounts[$i]))){
                                if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                                }else{
                                    return FALSE;
                                }
                            }else{

                            }
                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else if($deposit_fors[$i]==8){
                    //stock sales
                    $stock = $this->stocks_m->get_group_stock($stock_ids[$i]);
                    if($stock){
                        if($this->transactions->record_stock_sale($this->group->id,
                            $stock_ids[$i],$transaction_alert->transaction_date,$account_id,$number_of_shares_solds[$i],
                            valid_currency($price_per_shares[$i]),$stock->number_of_shares_sold,$transaction_alert->id)){
                            //update transaction alerts
                            if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                            }else{
                                $result = FALSE;
                            }
                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }   
                }else if($deposit_fors[$i]==9){
                    //asset sale
                    if($this->transactions->record_asset_sale_deposit($this->group->id,$asset_ids[$i],$transaction_alert->transaction_date,$account_id,valid_currency($amounts[$i]),$transaction_alert->id)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else if($deposit_fors[$i]==10){
                    //money market investment cash in
                    
                    if($this->transactions->record_money_market_investment_cash_in_deposit($this->group->id,$money_market_investment_ids[$i],$transaction_alert->transaction_date,$account_id,valid_currency($amounts[$i]),$transaction_alert->id)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else if($deposit_fors[$i]==11){
                    //money market investment cash in
                    if($this->loan->calculate_and_record_loan_processing_fee($loans[$i],FALSE,FALSE,$transaction_alert->id,currency($amounts[$i]))){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else if($deposit_fors[$i]==12){
                    //money market investment cash in
                    $debtor = $this->debtors_m->get($debtors[$i],$this->group->id);;
                    if($debtor){
                        if($this->loan->record_debtor_loan_repayment(
                                        $this->group->id,
                                        $transaction_alert->transaction_date,
                                        $debtor,
                                        $external_loans[$i],
                                        $account_id,
                                        1,
                                        '',
                                        currency($amounts[$i]),
                                        $enable_notifications,
                                        $enable_notifications,
                                        $this->user,
                                        $transaction_alert->id
                                    )){
                            //update transaction alerts
                            if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                            }else{
                                $result = FALSE;
                            }
                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }
            endfor;
            if($result){
                echo 'success';
            }else{
                echo 'Something went wrong with error: '.$error;
            }
        }else{
            echo $error_message .= "<li>All fields on the form are required. Kindly review your entries and try again</li></ul>";
        }
    }


    function ajax_reconcile_withdrawal(){
        $error_message = '<strong>Error!</strong> There were errors found on the form <ul>';
        $transaction_alert_id = $this->input->post('transaction_alert_id');
        $transaction_alert = $this->transaction_alerts_m->get($transaction_alert_id);
        $withdrawal_fors = $this->input->post('withdrawal_fors');
        $descriptions = $this->input->post('descriptions');
        $expense_categories = $this->input->post('expense_categories');
        $asset_ids = $this->input->post('asset_ids');
        $amounts = $this->input->post('amounts');
        $stock_names = $this->input->post('stock_names');
        $price_per_shares = $this->input->post('price_per_shares');
        $number_of_shares = $this->input->post('number_of_shares');
        $money_market_investment_names = $this->input->post('money_market_investment_names');
        $money_market_investment_ids = $this->input->post('money_market_investment_ids');
        $members = $this->input->post('members');
        $contributions = $this->input->post('contributions');
        $to_account_ids = $this->input->post('to_account_ids');
        $bank_loan_ids = $this->input->post('bank_loan_ids');
        $loans = $this->input->post('loans');

        $account_id = $this->bank_accounts_m->get_group_verified_bank_account_id_by_account_number($transaction_alert->account_number);
        
        if($account_id){
            $account_id = 'bank-'.$account_id;  
        }else{
            $account_id = $this->mobile_money_accounts_m->get_group_mobile_money_account_id_by_account_number($transaction_alert->account_number);
            if($account_id){
               $account_id = 'mobile-'.$account_id; 
            }else{
                
            }
        }
        $amount_reconciled = 0;
        $loan_match_error = FALSE;
        if(isset($amounts)):
            foreach($amounts as $amount){
                $amount_reconciled+=valid_currency($amount);
            }
        endif;
        if(isset($withdrawal_fors)&&$transaction_alert&&$account_id){
            if($transaction_alert->reconciled==1){
                echo "Transaction already reconciled";
                die;
            }else{
                $loan_match_amount = 0;
                $loan_recorded_amount = 0;
                $loan_selections_are_valid = TRUE;
                $loan_ids_array = array();
                $entries_are_valid = TRUE;
                $count = count($withdrawal_fors)-1;

                for($i=0;$i<=$count;$i++):
                    if(isset($withdrawal_fors[$i])){
                        if($withdrawal_fors[$i]==3){
                            if($members[$i]&&$loans[$i]){
                                if($loan = $this->loans_m->get_group_loan($loans[$i])){
                                    $loan_recorded_amount += $loan->loan_amount;
                                    if(in_array($loan->id,$loan_ids_array)){
                                        $loan_selections_are_valid = FALSE;
                                    }else{
                                        $loan_ids_array[] = $loan->id;
                                    }
                                }
                                if($amounts[$i]){
                                    $loan_match_amount += valid_currency($amounts[$i]);
                                }
                            }
                        }
                    }
                endfor;

                if($loan_recorded_amount==$loan_match_amount){

                }else{
                    $loan_match_error = TRUE;
                    $entries_are_valid = FALSE;
                }

                if($loan_selections_are_valid){

                }else{
                    $entries_are_valid = FALSE;
                }

                for($i=0;$i<=$count;$i++):

                    if(isset($withdrawal_fors[$i])){
                        if($withdrawal_fors[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            //do nothing for now
                        }
                    }


                    //loans
                    if(isset($loans[$i])){
                        if($loans[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($loans[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE;
                            }
                        }
                    }
                    if(isset($withdrawal_fors[$i])){
                        if($withdrawal_fors[$i]==3){
                            if($members[$i]&&$loans[$i]){
                                $loan = $this->loans_m->get_group_loan($loans[$i]);
                                if($loan){
                                    /*
                                    if(($loan->member_id==$members[$i])&&('bank-'.$account_id==$loan->account_id)&&($amounts[$i]==$loan->loan_amount)){
                                        //do nothing for now
                                    }else{
                                        $loan_match_error = TRUE;
                                        $entries_are_valid = FALSE;
                                    }
                                    */
                                }else{
                                    $entries_are_valid = FALSE;
                                }
                            }else{
                                $entries_are_valid = FALSE;
                            }
                        }
                    }

                    //amounts
                    if(isset($amounts[$i])){
                        if($amounts[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(valid_currency($amounts[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE; 
                            }
                        }
                    }


                    //expense category id
                    if(isset($expense_categories[$i])){
                        if($expense_categories[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($expense_categories[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE; 
                            }
                        }
                    }

                    //to account ids
                    if(isset($to_account_ids[$i])){
                        if($to_account_ids[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            //do nothing for now
                        }
                    }
                    //asset id
                    if(isset($asset_ids[$i])){
                        if($asset_ids[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($asset_ids[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE; 
                            }
                        }
                    }
                    //asset id
                    if(isset($bank_loan_ids[$i])){
                        if($bank_loan_ids[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($bank_loan_ids[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE; 
                            }
                        }
                    }
                    //asset id
                    if(isset($money_market_investment_ids[$i])){
                        if($money_market_investment_ids[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($money_market_investment_ids[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE; 
                            }
                        }
                    }
                    //stock names
                    if(isset($stock_names[$i])){
                        if($stock_names[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                                //do nothing for now
                            
                        }
                    }
                    //number of shares
                    if(isset($number_of_shares[$i])){
                        if($number_of_shares[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($number_of_shares[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE; 
                            }
                        }
                    }
                    //price of shares
                    if(isset($price_per_shares[$i])){
                        if($price_per_shares[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($price_per_shares[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE; 
                            }
                        }
                    }

                    //stock names
                    if(isset($money_market_investment_names[$i])){
                        if($money_market_investment_names[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                                //do nothing for now
                            
                        }
                    }
                    //Members
                    if(isset($members[$i])){
                        if($members[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($members[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE;
                            }
                        }
                    }

                    //Contributions
                    if(isset($contributions[$i])){
                        if($contributions[$i]==''){
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($contributions[$i])){
                                //do nothing for now
                            }else{
                                $entries_are_valid = FALSE;
                            }
                        }
                    }


                endfor;

                for($i=0;$i<=$count;$i++):
                    if(isset($withdrawal_fors[$i])){
                        if($withdrawal_fors[$i]==9){
                            if("bank-".$account_id == $to_account_ids[$i]){
                                $error_message .= "<li>On funds transfer select a recipient account different to the account withdrawn from.</li>";
                                $entries_are_valid = FALSE;
                            }
                        }
                    }
                endfor;
            }
        }else{
           $entries_are_valid = FALSE; 
        }
        if($loan_selections_are_valid){

        }else{
            $error_message .= "<li>You cannot match the same loan twice. Remove one and try again</li>";
            $entries_are_valid = FALSE; 
        }

        if($amount_reconciled==$transaction_alert->amount){

        }else{
            $error_message .= "<li>The amount reconciled has to be equal to the amount withdrawn</li>";
            $entries_are_valid = FALSE; 
        }
        if($loan_match_error){
            $error_message .= "<li>Cannot match loan, the loan has to match the member, the bank account and amount withdrawn</li>";
        }
        if($entries_are_valid){
            //make entries
            $result = TRUE;
            $count = count($withdrawal_fors)-1;
            //$account_id = 'bank-'.$account_id;
            for($i=0;$i<=$count;$i++):
                if($withdrawal_fors[$i]==1){
                    //expense withdrawal
                    if($this->transactions->record_expense_withdrawal($this->group->id,$transaction_alert->transaction_date,$expense_categories[$i],2,$account_id,$descriptions[$i],valid_currency($amounts[$i]),$transaction_alert->id)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else if($withdrawal_fors[$i]==2){
                    //asset purchase payment
                    if($this->transactions->record_asset_purchase_payment($this->group->id,$transaction_alert->transaction_date,$asset_ids[$i],$account_id,2,$descriptions[$i],valid_currency($amounts[$i]),$transaction_alert->id)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else if($withdrawal_fors[$i]==3){
                    //loan disbursement match
                    if($this->transactions->match_loan_disbursement_to_transaction_alert($this->group->id,$loans[$i],$transaction_alert->id)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else if($withdrawal_fors[$i]==4){
                    //stock purchase
                    if($this->transactions->record_stock_purchase($this->group->id,$transaction_alert->transaction_date,$stock_names[$i],$number_of_shares[$i],$account_id,$price_per_shares[$i],$transaction_alert->id)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else if($withdrawal_fors[$i]==5){
                    //money market investment
                    //echo $account_id;
                    //die;
                    if($this->transactions->create_money_market_investment($this->group->id,$money_market_investment_names[$i],$transaction_alert->transaction_date,valid_currency($amounts[$i]),$account_id,$descriptions[$i],$transaction_alert->id)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else if($withdrawal_fors[$i]==6){
                    //money market investment
                    if($this->transactions->top_up_money_market_investment($this->group->id,$money_market_investment_ids[$i],$transaction_alert->transaction_date,valid_currency($amounts[$i]),$account_id,$descriptions[$i],$transaction_alert->id)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else if($withdrawal_fors[$i]==7){
                    //contribution refund

                    if($this->transactions->record_contribution_refund($this->group->id,
                        $transaction_alert->transaction_date,
                        $members[$i],$account_id,
                        $contributions[$i],1,
                        '',
                        valid_currency($amounts[$i]),1,$transaction_alert->id)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else if($withdrawal_fors[$i]==8){
                    //bank loan repayment
                    $description = $descriptions[$i]?:'Bank Loan Repayment';
                    if($this->loan->bank_loan_repayment($bank_loan_ids[$i],valid_currency($amounts[$i]),$transaction_alert->transaction_date,$this->group->id,$account_id,1,$description,$this->user->id,$transaction_alert->id)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else if($withdrawal_fors[$i]==9){
                    //money transfer
                    // echo $account_id;
                    // echo "<br/>";
                    // echo $to_account_ids[$i];
                    // die;
                    if($this->transactions->record_account_transfer($this->group->id,$transaction_alert->transaction_date,$account_id,$to_account_ids[$i],valid_currency($amounts[$i]),$descriptions[$i],$transaction_alert->id)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){
                            if($transaction_alert = $this->transaction_alerts_m->get_group_matching_deposit_transaction_alert($this->group->id,$transaction_alert->transaction_date,$account_id,$to_account_ids[$i],valid_currency($amounts[$i]))){
                                if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                                }else{
                                    return FALSE;
                                }
                            }else{

                            }
                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else if($withdrawal_fors[$i]==11){
                    //dividend withdrawal
                    if($this->transactions->record_dividend_withdrawal($this->group->id,$transaction_alert->transaction_date,$members[$i],2,$account_id,$descriptions[$i],valid_currency($amounts[$i]),$transaction_alert->id)){
                        //update transaction alerts
                        if($this->transactions->mark_transaction_alert_as_reconciled($transaction_alert->id,$this->group->id)){

                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }
            endfor;
            if($result){
                echo 'success';
            }else{
                // echo 'Something went wrong';
                print_r($this->session->error); die;
            }
        }else{
            echo $error_message .= "<li>All fields on the form are required. Kindly review your entries and try again</li></ul>";
        }
    }


    // function void_deposit($id = 0){
    //     $id OR redirect('group/transaction_alerts/listing');
    //     $post = $this->transaction_alerts_m->get($id);
    //     $post OR redirect('group/transaction_alerts/listing');
    //     $post->reconciled!==1 OR redirect('group/transaction_alerts/listing'); 
    //     $deposit = $this->deposits_m->get_group_deposit_by_transaction_alert_id($post->id);
    //     if($deposit){
    //         if($this->transactions->void_group_deposit($deposit->id,$deposit,TRUE,$this->group->id)){
    //             $this->session->set_flashdata('success','Reconciliation successfully voided'); 
    //         }else{
    //             $this->session->set_flashdata('warning','Something went wrong'); 
    //         }
    //     }else{
    //         $this->session->set_flashdata('warning','Could not find deposit'); 
    //     }
        
    //     if($this->agent->referrer()){
    //         redirect($this->agent->referrer());
    //     }else{
    //         redirect('group/deposits/listing');
    //     }
    // }

    function void_deposit($id = 0){
        $id OR redirect('group/transaction_alerts/listing');
        $post = $this->transaction_alerts_m->get($id);
        $post OR redirect('group/transaction_alerts/listing');
        $post->reconciled !== 1 OR redirect('group/transaction_alerts/listing'); 
        $deposit = $this->deposits_m->get_group_deposit_by_transaction_alert_id($post->id);
        if($post->marked_as_reconciled){
            $input = array(
                'reconciled' => 0,
                'marked_as_reconciled' => 0,
                'modified_on' => time(),
                'modified_by' => $this->user->id,
            );
            if($this->transaction_alerts_m->update($post->id,$input)){
                $this->session->set_flashdata('success',"Transaction alert reconciliation voided successfully.");
            }else{
                $this->session->set_flashdata('error',"Could not void transaction alert reconciliation.");
            }
        }else{
            if($deposit){
                if($this->transactions->void_group_deposit($deposit->id,$deposit,TRUE,$this->group->id)){
                    $this->session->set_flashdata('success','Reconciliation successfully voided'); 
                }else{
                    $this->session->set_flashdata('warning','Something went wrong'); 
                }
            }else{
                $input = array(
                    'reconciled' => 0,
                    'marked_as_reconciled' => 0,
                    'modified_on' => time(),
                    'modified_by' => $this->user->id,
                );
                if($this->transaction_alerts_m->update($post->id,$input)){
                    $this->session->set_flashdata('success',"Transaction alert reconciliation voided successfully.");
                }else{
                    $this->session->set_flashdata('error',"Could not void transaction alert reconciliation.");
                }
                //$this->session->set_flashdata('warning','Could not find deposit'); 
            }
        }   
        if($this->agent->referrer()){
            redirect($this->agent->referrer());
        }else{
            redirect('group/deposits/listing');
        }
    }

    function void_withdrawal($id = 0){
        $id OR redirect('group/transaction_alerts/listing');
        $post = $this->transaction_alerts_m->get($id);
        $post OR redirect('group/transaction_alerts/listing');
        $post->reconciled!==1 OR redirect('group/transaction_alerts/listing'); 
        if($post->marked_as_reconciled){
            $input = array(
                'is_merged' => 0,
                'reconciled' => 0,
                'marked_as_reconciled' => 0,
                'modified_on' => time(),
                'modified_by' => $this->user->id,
            );
            if($this->transaction_alerts_m->update($post->id,$input)){
                $this->session->set_flashdata('success',"Transaction alert reconciliation voided successfully.");
            }else{
                $this->session->set_flashdata('error',"Could not void transaction alert reconciliation.");
            }
        }else{
            $withdrawal = $this->withdrawals_m->get_group_withdrawal_by_transaction_alert_id($post->id);
            if($withdrawal){
                if($this->transactions->void_group_withdrawal($withdrawal->id,$withdrawal,TRUE,$this->group->id)){
                    $this->session->set_flashdata('success','Reconciliation successfully voided'); 
                }else{
                    $this->session->set_flashdata('warning','Something went wrong'); 
                }
            }else{
                $this->session->set_flashdata('warning','Could not find withdrawal'); 
            }
        }
        if($this->agent->referrer()){
            redirect($this->agent->referrer());
        }else{
            redirect('group/withdrawals/listing');
        }
    }

    function check_new_unreconciled_deposits($timestamp = 0){
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        if($this->session->has_userdata('timestamp')){
            $timestamp = $this->session->userdata('timestamp');
        }else{
            $timestamp = time();
            $data = array(
                'timestamp' => $timestamp,
            );
            $this->session->set_userdata($data);
        }
        $unreconciled_deposits = $this->transaction_alerts_m->get_group_unreconciled_deposits($this->group_partner_bank_account_number_list,$this->group_partner_mobile_money_account_number_list,$timestamp);
        if($unreconciled_deposits){
            $old_timestamp = $timestamp;
            $timestamp = time();
            $data = array(
                'timestamp' => $timestamp,
            );
            $this->session->set_userdata($data);
            $bank_account_options = $this->bank_accounts_m->get_group_verified_partner_bank_account_options();
            $mobile_money_account_options = $this->mobile_money_accounts_m->get_group_verified_partner_mobile_account_options();
            $data = array(
                'timestamp' => $old_timestamp,
                'unreconciled_deposits' => $unreconciled_deposits,
                'bank_account_options' => $bank_account_options,
                'mobile_money_account_options' => $mobile_money_account_options,
            );
            echo "data: ".json_encode($data)."\n\n";
            flush();
        }
    }

    function check_new_unreconciled_withdrawals($timestamp = 0){
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        if($this->session->has_userdata('timestamp')){
            $timestamp = $this->session->userdata('timestamp');
        }else{
            $timestamp = time();
            $data = array(
                'timestamp' => $timestamp,
            );
            $this->session->set_userdata($data);
        }
        $unreconciled_withdrawals = $this->transaction_alerts_m->get_group_unreconciled_withdrawals($this->group_partner_bank_account_number_list,$this->group_partner_mobile_money_account_number_list,$timestamp);
        if($unreconciled_withdrawals){
            $old_timestamp = $timestamp;
            $timestamp = time();
            $data = array(
                'timestamp' => $timestamp,
            );
            $this->session->set_userdata($data);
            $bank_account_options = $this->bank_accounts_m->get_group_verified_partner_bank_account_options();
            $mobile_money_account_options = $this->mobile_money_accounts_m->get_group_verified_partner_mobile_account_options();
            $data = array(
                'timestamp' => $old_timestamp,
                'unreconciled_withdrawals' => $unreconciled_withdrawals,
                'bank_account_options' => $bank_account_options,
                'mobile_money_account_options' => $mobile_money_account_options,
            );
            echo "data: ".json_encode($data)."\n\n";
            flush();
        }
    }

    function ajax_get_new_unreconciled_deposits($timestamp = 0){
        $unreconciled_deposits = $this->transaction_alerts_m->get_group_unreconciled_deposits($this->group_partner_bank_account_number_list,$this->group_partner_mobile_money_account_number_list,$timestamp);
        if($unreconciled_deposits){
            $bank_account_options = $this->bank_accounts_m->get_group_verified_partner_bank_account_options();
            $mobile_money_account_options = $this->mobile_money_accounts_m->get_group_verified_partner_mobile_account_options();
            $count=1; 
            foreach($unreconciled_deposits as $unreconciled_deposit):
                $account_details = isset($bank_account_options[$unreconciled_deposit->account_number])?$bank_account_options[$unreconciled_deposit->account_number]:(isset($mobile_money_account_options[$unreconciled_deposit->account_number])?$mobile_money_account_options[$unreconciled_deposit->account_number]:'');
                echo '
                    <tr class="new_unreconciled_deposit" id="unreconciled_deposit_row_'.$unreconciled_deposit->id.'"';
                    if($this->group->enable_merge_transaction_alerts && count($unreconciled_deposits) >= 2){
                        echo '
                            <th width=\'2%\'>
                                <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                    <input type="checkbox" name="check" value="all" class="check_all">
                                    <span></span>
                                </label>
                            </th>
                        ';
                    }
                    echo '>';
                    if($unreconciled_deposit->reconciled){
                        echo ' class="success" ';
                    }
                    if($this->group->enable_merge_transaction_alerts && count($unreconciled_deposits) >= 2){
                        echo ' 
                            <td>
                                <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                    <input name="action_to[]"" type="checkbox" class="checkboxes" value="'.$unreconciled_deposit->id.'"/>
                                    <span></span>
                                </label>
                            </td>
                        ';
                    }
                    echo'
                    <td class="unreconciled_deposit_count">'.$count++.'</td>
                    <td>
                        '.timestamp_to_date($unreconciled_deposit->transaction_date).'<span class="m-badge m-badge--primary m-badge--wide"> New </span>
                            <br/>
                            <small><strong>Delivered On:</strong>'.timestamp_to_date_and_time($unreconciled_deposit->created_on).'</small>
                    </td>
                    <td>'. $account_details.'</td>
                    <td>
                        '.$unreconciled_deposit->particulars.' <small><a class=\'toggle_transaction_alert_details\'>More..</a></small>
                        <div class="transaction_alert_details" style="display: none;">
                            '.$unreconciled_deposit->description.'
                        </div>
                    </td>
                    <td class="text-right">'.number_to_currency($unreconciled_deposit->amount).'</td>
                    <td class="reconcile_action">';
                        if($unreconciled_deposit->reconciled){
                            echo '<span class="m-badge m-badge--primary m-badge--wide"> Reconciled </span>';
                        }else{
                            echo '
                                <a class="btn btn-sm btn-outline-accent m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air reconcile_deposit_pop_up reconcile_deposit" data-toggle="modal" data-target="#reconcile_deposit_pop_up" id="'.$unreconciled_deposit->id.'">
                                    <span>
                                        <i class="la la-pencil"></i>
                                        <span>Reconcile</span>
                                    </span>
                                </a>
                           ';
                            if($this->group->enable_bulk_transaction_alerts_reconciliation){
                                echo '
                                    <a href="'.site_url('group/deposits/upload_payments/'.$unreconciled_deposit->id).'"class="btn btn-sm btn-outline-accent m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air mt-2" id="'.$unreconciled_deposit->id.'">
                                        <span>
                                            <i class="la la-cloud-upload"></i>
                                            <span>Reconcile via Upload</span>
                                        </span>
                                    </a>
                                ';
                            }
                        }
                    echo '
                    </td>
                </tr>';
            endforeach;
        }
    }

    function ajax_get_new_unreconciled_withdrawals($timestamp = 0){
        $unreconciled_withdrawals = $this->transaction_alerts_m->get_group_unreconciled_withdrawals($this->group_partner_bank_account_number_list,$this->group_partner_mobile_money_account_number_list,$timestamp);
        if($unreconciled_withdrawals){
            $bank_account_options = $this->bank_accounts_m->get_group_verified_partner_bank_account_options();
            $mobile_money_account_options = $this->mobile_money_accounts_m->get_group_verified_partner_mobile_account_options();
            $count=1; 
            foreach($unreconciled_withdrawals as $unreconciled_withdrawal):
                $account_details = isset($bank_account_options[$unreconciled_withdrawal->account_number])?$bank_account_options[$unreconciled_withdrawal->account_number]:(isset($mobile_money_account_options[$unreconciled_withdrawal->account_number])?$mobile_money_account_options[$unreconciled_withdrawal->account_number]:'');
                echo '
                <tr class="new_unreconciled_withdrawal" id="unreconciled_withdrawal_row_'.$unreconciled_withdrawal->id.'"';
                    if($unreconciled_withdrawal->reconciled){
                        echo ' class="success" ';
                    }
                    if($this->group->enable_merge_transaction_alerts && count($unreconciled_withdrawals) >= 2){
                        echo '
                            <th width=\'2%\'>
                                <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                    <input type="checkbox" name="check" value="all" class="check_all">
                                    <span></span>
                                </label>
                            </th>
                        ';
                    }
                    echo '
                    >';
                    if($this->group->enable_merge_transaction_alerts && count($unreconciled_withdrawals) >= 2){
                        echo '
                            <td>
                                <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                    <input name="action_to[]"" type="checkbox" class="checkboxes" value="'.$unreconciled_withdrawal->id.'"/>
                                    <span></span>
                                </label>
                            </td>';
                    };
                    echo'
                    <td>
                        '.timestamp_to_date($unreconciled_withdrawal->transaction_date).' <span class="m-badge m-badge--primary m-badge--wide"> New </span><br/>
                        <small><strong>Delivered On: </strong>'.timestamp_to_date_and_time($unreconciled_withdrawal->created_on).'</small>
                    </td>
                    <td>'. $account_details.'</td>
                    <td>
                        '.$unreconciled_withdrawal->particulars.' <small><a class=\'toggle_transaction_alert_details\'>More..</a></small>
                        <div class="transaction_alert_details" style="display: none;">
                            '.$unreconciled_withdrawal->description.'
                        </div>
                    </td>
                    <td class="text-right">'.number_to_currency($unreconciled_withdrawal->amount).'</td>
                    <td class=\'reconcile_action\'>';
                        if($unreconciled_withdrawal->reconciled){
                            echo '<span class="m-badge m-badge--primary m-badge--wide"> Reconciled </span>';
                        }else{
                            echo '
                                <a class="btn btn-sm btn-outline-accent m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air reconcile_withdrawal_pop_up reconcile_wthdrawal" data-toggle="modal" data-target="#reconcile_withdrawal_pop_up" id="'.$unreconciled_withdrawal->id.'">
                                    <span>
                                        <i class="la la-pencil"></i>
                                        <span>Reconcile</span>
                                    </span>
                                </a>
                            ';
                        }
                    echo '
                    </td>
                </tr>';
            endforeach;
        }
    }

    function reconcile_lace_deposit_transaction_alerts(){
        $transaction_alerts = $this->transaction_alerts_m->get_group_lace_deposit_transaction_alerts($this->group_partner_bank_account_number_list,$this->group_partner_mobile_money_account_number_list);
        //print_r($transaction_alerts)."<br/>";
        //$deposits = $this->deposits_m->get_group_loan_processing_income_deposits();
        //print_r($deposits);
        foreach($transaction_alerts as $transaction_alert):
            $input = array(
                'reconciled' => 1,
                'modified_on' => time()
            );
            //$this->transaction_alerts_m->update($transaction_alert->id,$input);
        endforeach;
    }

    function reconcile_transactions(){
        $transaction_alerts = $this->transaction_alerts_m->get_group_unreconciled_deposit_transaction_alerts();
        foreach($transaction_alerts as $transaction_alert){
             $input = array(
                'reconciled' => 1,
                'modified_on' => time()
            );
            $this->transaction_alerts_m->update($transaction_alert->id,$input);
        }
        echo count($transaction_alerts);
    }

    function get_group_all_transaction_alerts_deposits($account_number = 0){
        $transaction_alerts = $this->transaction_alerts_m->get_group_transaction_alerts_deposits_by_account_number($account_number);
        print_r($transaction_alerts);
    }
}