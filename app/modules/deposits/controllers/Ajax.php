<?php
defined('BASEPATH') or exit('No direct script access allowed');



require_once './assets/vendor/autoload.php';
//require_once "./assets/PHPExcel.php";

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class Ajax extends Ajax_Controller
{

    protected $deposit_type_options = array(
        1 => "Contribution payment",
        2 => "Contribution fine payment",
        3 => "Fine payment",
        4 => "Incoming Bank Transfer",
        5 => "External deposit",
        6 => "Group Expense payment",
        7 => "Loan repayment",
        8 => "Financial Institution Loan",
        9 => "Other user defined deposit",
    );

    protected $transfer_to_options = array(
        1 => "Contribution share",
        2 => "Fine payment",
        3 => "Loan share",
        4 => "Another member",
    );

    protected $member_transfer_to_options = array(
        1 => "Contribution share",
        2 => "Fine payment",
        3 => "Loan share",
    );
    protected $data = array();

    function __construct()
    {
        parent::__construct();
        set_time_limit(0);
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);
        $this->load->model('deposits_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->model('contributions/contributions_m');
        $this->load->model('fine_categories/fine_categories_m');
        $this->load->model('depositors/depositors_m');
        $this->load->model('income_categories/income_categories_m');
        $this->load->model('stocks/stocks_m');
        $this->load->model('assets/assets_m');
        $this->load->model('money_market_investments/money_market_investments_m');
        $this->load->library('transactions');
        $this->load->library('loan');
        $this->load->library('contribution_invoices');
        $this->sms_template_default = $this->contribution_invoices->sms_template_default;
    }

    function get_deposit($id = 0, $generate_pdf = FALSE)
    {
        $post = $this->deposits_m->get_group_deposit($id);
        if (empty($post)) {
            echo 'Sorry, the entry does not exists.';
        } else {
            if ($post->active) {
                $deposit_transaction_names = $this->transactions->deposit_transaction_names;
                $contribution_options = $this->contributions_m->get_group_contribution_options();
                $fine_category_options = $this->fine_categories_m->get_group_options(FALSE);
                $deposit_method_options = $this->transactions->deposit_method_options;
                $depositor_options = $this->depositors_m->get_group_depositor_options();
                $income_category_options = $this->income_categories_m->get_group_income_category_options();
                $stock_options = $this->stocks_m->get_group_stock_options();
                $money_market_investment_options = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id, $this->group_currency);
                $accounts = $this->accounts_m->get_group_account_options(FALSE);
                $group_member_options = $this->group_member_options;
                $loan_type_options_options = $this->loan_types_m->get_options();
                if ($post->loan_id) {
                    $loan = $this->loans_m->get($post->loan_id);
                }
                $account = '';
                $description = '';
                $depositor = '';
                if ($post->type == 13 || $post->type == 14 || $post->type == 15 || $post->type == 16) {
                    $depositor = $depositor_options[$post->depositor_id];
                } else if ($post->type == 17 || $post->type == 18 || $post->type == 19 || $post->type == 20) {
                    $depositor = $this->group_member_options[$post->member_id];
                } else if ($post->type == 25 || $post->type == 26 || $post->type == 27 || $post->type == 28) {
                } else if ($post->type == 29 || $post->type == 30 || $post->type == 31 || $post->type == 32) {
                    $depositor = $money_market_investment_options[$post->money_market_investment_id];
                } else if ($post->type == 21 || $post->type == 22 || $post->type == 23 || $post->type == 24) {
                } else if ($post->type == 25 || $post->type == 26 || $post->type == 27 || $post->type == 28) {
                } else if ($post->type == 33 || $post->type == 34 || $post->type == 35 || $post->type == 36) {
                } else if ($post->type == 37 || $post->type == 38 || $post->type == 39 || $post->type == 40) {
                } else {
                    $depositor = $this->group_member_options[$post->member_id];
                }
                if ($post->account_id) {
                    $account = $accounts[$post->account_id];
                }
                if ($post->type == 1 || $post->type == 2 || $post->type == 3 || $post->type == 7) {
                    $description = $deposit_transaction_names[$post->type] . ' for "' . $contribution_options[$post->contribution_id] . '" contribution via ' . $deposit_method_options[$post->deposit_method];
                } else if ($post->type == 4 || $post->type == 5 || $post->type == 6 || $post->type == 8) {
                    if ($post->contribution_id) {
                        $for = $contribution_options[$post->contribution_id] . ' contribution late payment';
                    } else if ($post->fine_category_id) {
                        $for = $fine_category_options[$post->fine_category_id];
                    } else {
                        $for = '';
                    }
                    $description = $deposit_transaction_names[$post->type] . ' for "' . $for . '" via ' . $deposit_method_options[$post->deposit_method];
                } else if ($post->type == 9 || $post->type == 10 || $post->type == 11 || $post->type == 12) {
                    $description = $deposit_transaction_names[$post->type];
                } else if ($post->type == 13 || $post->type == 14 || $post->type == 15 || $post->type == 16) {
                    $description = $deposit_transaction_names[$post->type] . ' from ' . $income_category_options[$post->income_category_id];
                } else if ($post->type == 17 || $post->type == 18 || $post->type == 19 || $post->type == 20) {
                    if ($loan->loan_type_id) {
                        $description = "'" . $loan_type_options_options[$loan->loan_type_id] . "' " . $deposit_transaction_names[$post->type] . ' via ' . $deposit_method_options[$post->deposit_method] . ' to ' . $accounts[$post->account_id];
                    } else {
                        $description = $deposit_transaction_names[$post->type] . ' via ' . $deposit_method_options[$post->deposit_method] . ' to ' . $accounts[$post->account_id];
                    }
                    $account = '';
                } else if ($post->type == 25 || $post->type == 26 || $post->type == 27 || $post->type == 28) {
                    $description = $deposit_transaction_names[$post->type] . ' of ' . $post->number_of_shares_sold . ' "' . $stock_options[$post->stock_id] . '" shares';
                } else if ($post->type == 29 || $post->type == 30 || $post->type == 31 || $post->type == 32) {
                    $description = $deposit_transaction_names[$post->type];
                } else if ($post->type == 21 || $post->type == 22 || $post->type == 23 || $post->type == 24) {
                    $description = $deposit_transaction_names[$post->type];
                } else if ($post->type == 37 || $post->type == 38 || $post->type == 39 || $post->type == 40) {
                    $description = $deposit_transaction_names[$post->type];
                } else if ($post->type == 41 || $post->type == 42 || $post->type == 43 || $post->type == 44) {
                    if ($loan->loan_type_id) {
                        $description = "'" . $loan_type_options_options[$loan->loan_type_id] . "' " . $deposit_transaction_names[$post->type];
                    } else {
                        $description = $deposit_transaction_names[$post->type];
                    }
                }

                if ($post->description) {
                    $description .= ' : ' . $post->description;
                }
                $received_by = $this->ion_auth->get_user($post->created_by);
                $response = array(
                    'id' => $post->id,
                    'deposit_transaction_name' => $deposit_transaction_names[$post->type],
                    'deposit_date' => timestamp_to_date($post->deposit_date),
                    'deposit_method' => $this->transactions->deposit_method_options[$post->deposit_method],
                    'recorded_on' => timestamp_to_date_and_time($post->created_on),
                    'description' => $description,
                    'amount' => $this->group_currency . '. ' . number_to_currency($post->amount),
                    'depositor' => $depositor,
                    'account' => $account,
                    'type' => $post->type,
                    'is_reconciled' => $post->transaction_alert_id ? 1 : 0,
                    'received_by' => $received_by->first_name . ' ' . $received_by->last_name,
                    'transaction_alert_id' => $post->transaction_alert_id,
                    'loan_id' => $post->loan_id,
                );
                echo json_encode($response);
            } else {
                echo 'Sorry, the entry does not exists.';
            }
        }
    }

    function get_deposits_listing()
    {
        // if(array_key_exists($this->member->id, $this->member_role_holder_options)){
        $transaction_alert_id = $this->input->get('transaction_alert');
        $data = array();
        $from = strtotime(xss_clean_input($this->input->get('from'))) ?: '';
        $to = strtotime(xss_clean_input($this->input->get('to'))) ?: '';
        $member_only = $this->input->get_post('formember') ?: 0;
        if (preg_match('/group/', $this->uri->uri_string())) {
            $controller_starter = 'group';
        } else {
            $controller_starter = 'member';
        }
        $controller = $member_only ? $controller_starter . '/deposits/your_deposits/pages' : 'group/deposits/listing/pages';
        $filter_parameters = array(
            'transaction_alert_id' => $transaction_alert_id,
            'member_id' => ($member_only ? array($this->member->id) : ($this->input->get('member_id') ?: '')),
            'type' => $this->input->get('deposit_for') ?: '',
            'contributions' => $this->input->get('contributions') ?: '',
            'fine_categories' => $this->input->get('fine_categories') ?: '',
            'income_categories' => $this->input->get('income_categories') ?: '',
            'stocks' => $this->input->get('stocks') ?: '',
            'money_market_investments' => $this->input->get('money_market_investments') ?: '',
            'assets' => $this->input->get('assets') ?: '',
            'accounts' => $this->input->get('accounts') ?: '',
            'from' => $from,
            'to' => $to,
        );

        $deposit_transaction_names = $this->transactions->deposit_transaction_names;
        $deposit_type_options = $this->transactions->deposit_type_options;
        $contribution_options = $this->contributions_m->get_group_contribution_options();
        $fine_category_options = $this->fine_categories_m->get_group_options(FALSE);
        $deposit_method_options = $this->transactions->deposit_method_options;
        $deposit_for_options = $this->transactions->deposit_for_options;
        $depositor_options = $this->depositors_m->get_group_depositor_options();
        $income_category_options = $this->income_categories_m->get_group_income_category_options();
        $stock_options = $this->stocks_m->get_group_stock_options();
        $money_market_investment_options = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id, $this->group_currency);
        $accounts = $this->accounts_m->get_group_account_options(FALSE);
        $total_rows = $this->deposits_m->count_group_deposits($this->group->id, $filter_parameters);
        $pagination = create_pagination($controller, $total_rows, 50, 5, TRUE);
        $posts = $this->deposits_m->limit($pagination['limit'])->get_group_deposits($this->group->id, $filter_parameters);
        if (!empty($posts)) {
            if ($this->group->id == 33) {
                foreach ($posts as $post) {
                    if ($post->transaction_alert_id) {
                        $input = array(
                            'reconciled' => 1,
                            'modified_on' => time()
                        );
                        $this->transaction_alerts_m->update($post->transaction_alert_id, $input);
                    }
                }
            }
            echo form_open('group/deposits/action', ' id="form"  class="form-horizontal"');
            if ($pagination) {
                echo '
                    <div class="search-pagination">';
                if (!empty($pagination['links'])) :
                    echo '
                            <div class="paging">Showing from <span class="greyishBtn">' . $pagination['from'] . '</span> to <span class="greyishBtn">' . $pagination['to'] . '</span> of <span class="greyishBtn">' . $pagination['total'] . '</span> Deposits
                            </div>
                            <div class ="pagination">' . $pagination['links'] . '</div>';
                endif;
                echo '
                    </div>';
            }
            echo '  

                        <table class="table m-table m-table--head-separator-primary">
                            <thead>
                                <tr>
                                    <th width=\'2%\' nowrap>
                                        <label class="m-checkbox">
                                            <input type="checkbox" name="check" value="all" class="check_all">
                                            <span></span>
                                        </label>
                                    </th>
                                    <th nowrap>
                                       #
                                    </th>
                                    <th nowrap>
                                       ' . translate('Type') . '
                                    </th>
                                    <th nowrap>
                                       ' . translate('Deposited By') . '
                                    </th>
                                    
                                    <th class=\'text-right\' nowrap>
                                       ' . translate('Amount') . ' (' . $this->group_currency . ')
                                    </th>
                                    <th  class="text-right" nowrap>
                                       ' . translate('Deposited On') . '
                                    </th>
                                    <th nowrap>
                                       &nbsp;
                                    </th>
                                    ';
            if ($member_only) {
            } else {
                echo '
                                            <th>
                                                
                                            </th>
                                        ';
            }
            echo '
                                </tr>
                            </thead>
                            <tbody>';
            $i = $this->uri->segment(5, 0);
            $i++;
            foreach ($posts as $post) :
                echo '
                                        <tr>
                                            <td scope="row">
                                                <label class="m-checkbox">
                                                    <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="' . $post->id . '" />
                                                    <span></span>
                                                </label>
                                            </td>
                                            <td scope="row">' .
                    $i++ . '
                                            </td>
                                            <td nowrap>';
                if($post->type && isset($deposit_transaction_names[$post->type])){
                    echo $deposit_transaction_names[$post->type];
                }
                if ($post->transaction_alert_id) {
                    echo '
                                                        <span class="m-badge m-badge--info m-badge--wide"><small>Reconciled</small></span>
                                                    ';
                }
                echo '</td><td>';
                if ($post->type == 13 || $post->type == 14 || $post->type == 15 || $post->type == 16) {
                    echo isset($depositor_options[$post->depositor_id]) ? $depositor_options[$post->depositor_id] : '';
                } else if ($post->type == 17 || $post->type == 18 || $post->type == 19 || $post->type == 20) {
                    echo $this->group_member_options[$post->member_id];
                } else if ($post->type == 25 || $post->type == 26 || $post->type == 27 || $post->type == 28) {
                    echo ' - ';
                } else if ($post->type == 29 || $post->type == 30 || $post->type == 31 || $post->type == 32) {
                    echo $money_market_investment_options[$post->money_market_investment_id];
                } else if ($post->type == 21 || $post->type == 22 || $post->type == 23 || $post->type == 24) {
                    echo ' - ';
                } else if ($post->type == 25 || $post->type == 26 || $post->type == 27 || $post->type == 28) {
                    echo ' - ';
                } else if ($post->type == 33 || $post->type == 34 || $post->type == 35 || $post->type == 36) {
                    echo ' - ';
                } else if ($post->type == 37 || $post->type == 38 || $post->type == 39 || $post->type == 40) {
                    echo ' - ';
                } else if ($post->type == 45 || $post->type == 46 || $post->type == 47 || $post->type == 48) {
                    echo ' - ';
                } else if ($post->type == 49 || $post->type == 50 || $post->type == 51 || $post->type == 52) {
                    echo ' - ';
                } else {
                    echo $this->group_member_options[$post->member_id];
                }
                echo '
                                                </td><td class="text-right">' .
                    number_to_currency($post->amount) .
                    '</td><td class="text-right">' .
                    timestamp_to_date($post->deposit_date) .
                    '</td>';
                if ($member_only) {
                } else {
                    echo '
                                                <td nowrap>
                                                    <a href="javascript:;" class="btn btn-sm btn-primary m-btn m-btn--icon view_deposit action_button" id="' . $post->id . '" data-toggle="modal" data-target="#deposit_receipt">
                                                        <span>
                                                            <i class="la la-eye"></i>
                                                            <span>
                                                                ' . translate('More') . ' &nbsp;&nbsp; 
                                                            </span>
                                                        </span>
                                                    </a>
                                                    <a href="' . site_url('group/deposits/void/' . $post->id) . '" class="btn btn-sm confirmation_link btn-danger m-btn m-btn--icon action_button" data-message="Are you sure you want to void deposit?">
                                                        <span>
                                                            <i class="la la-trash"></i>
                                                            <span>
                                                                ' . translate('Void') . ' &nbsp;&nbsp;
                                                            </span>
                                                        </span>
                                                    </a>';
                    echo '
                                                    </td>';
                }
                echo '
                                        </tr>';
            endforeach;

            echo '
                            </tbody>
                        </table>

                        <div class="row col-md-12">';
            if ($pagination) {
                echo '
                            <div class="search-pagination">';
                if (!empty($pagination['links'])) :
                    echo '
                                    <div class ="pagination">' . $pagination['links'] . '</div>';
                endif;
                echo '
                            </div>';
            }
            echo '</div>';
            if ($member_only) {
            } else {
                if ($posts) :
                    echo '<button class="btn btn-sm btn-info" name=\'btnAction\' value=\'bulk_pdf_receipts\' data-placement="top"> <i class=\'fa fa-copy\'></i> ' . translate('Generate Bulk Receipts') . '</button>';
                    echo '&nbsp; &nbsp;';
                    echo '<button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_void\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-trash-o\'></i> ' . translate('Bulk Void') . '</button>';
                endif;
            }
            echo form_close();
        } else {
            echo '
                    <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                        <strong>' . translate('Sorry') . '!</strong> ' . translate('There are no deposit records to display') . '.
                    </div>
                ';
        }
        // }else{
        //     echo '
        //     <div class="container-fluid">
        //         <div class="row">
        //             <div class="col-md-12">
        //                 <div class="m-alert m-alert--outline alert alert-info fade show" role="alert"">
        //                     <strong>'.translate('Information').'!</strong> '.translate('You dont have rights to access this panel').'
        //                 </div>
        //             </div>
        //         </div>
        //     </div>';
        // }
    }

    function get_contribution_transfers_listing()
    {
        //if (array_key_exists($this->member->id, $this->member_role_holder_options)) {
            $from = strtotime(xss_clean_input($this->input->get('from'))) ?: '';
            $to = strtotime(xss_clean_input($this->input->get('to'))) ?: '';
            $filter_parameters = array(
                'from' => $from,
                'to' => $to,
                'member_id' => $this->input->get('member_id'),
                'transfer_to' => $this->input->get('transfer_to'),
                'member_transfer_to' => $this->input->get('member_transfer_to'),
                'contribution_from_id' => $this->input->get('contribution_from_id'),
                'member_to_id' => $this->input->get('member_to_id'),
            );
            $total_rows = $this->deposits_m->count_group_contribution_transfers($filter_parameters);
            $pagination = create_pagination('group/deposits/contribution_transfers/pages', $total_rows, 50, 5, TRUE);
            $transfer_to_options = $this->transfer_to_options;
            $contribution_options = $this->contributions_m->get_group_contribution_options();
            $fine_category_options = $this->fine_categories_m->get_group_options(FALSE);
            $posts = $this->deposits_m->get_group_contribution_transfers($filter_parameters);
            if (!empty($posts)) {
                echo form_open('group/deposits/action', ' id="form"  class="form-horizontal"');
                if (!empty($pagination['links'])) :
                    echo '
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn">' . $pagination['from'] . '</span> to <span class="greyishBtn">' . $pagination['to'] . '</span> of <span class="greyishBtn">' . $pagination['total'] . '</span> Contribution Transfers</p>';
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links'];
                    echo '</div></div>';
                endif;
                echo ' 
                    <table class="table m-table m-table--head-separator-primary">
                        <thead>
                            <tr>
                                <th width=\'2%\'>
                                    <label class="m-checkbox">
                                        <input type="checkbox" name="check" value="all" class="check_all">
                                        <span></span>
                                    </label>
                                </th>
                                <th width=\'2%\'>
                                    #
                                </th>
                                <th>
                                    Transfer Details
                                </th>
                                <th>
                                    Amount (' . $this->group_currency . ')
                                </th>  
                                <th>
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>';
                $i = $this->uri->segment(5, 0);
                $i++;
                foreach ($posts as $post) :
                    echo '
                                <tr>
                                    <td>
                                        <label class="m-checkbox">
                                            <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="' . $post->id . '" />
                                            <span></span>
                                        </label>
                                    </td>
                                    <td>' . ($i++) . '</td>
                                    <td>
                                        <strong> Transfer Date : </strong>' . timestamp_to_date($post->transfer_date) . '<br/>
                                        <strong> Member </strong> ' . $this->group_member_options[$post->member_id] . '<br/>
                                        <strong> Description </strong><hr/>';
                    if ($post->contribution_from_id == 'loan') {
                        echo 'Loan payment transfer to ';
                    } else {
                        echo 'Contribution transfer to ';
                    }
                    echo $transfer_to_options[$post->transfer_to];
                    echo ' : ';
                    if ($post->transfer_to == 1) {
                        if ($post->contribution_from_id == 'loan') {
                            echo 'from loan -' . $this->loans_m->get_loan_details($post->loan_from_id);
                        } else {
                            echo "'" . $contribution_options[$post->contribution_from_id] . "' contribution transfer to " . $contribution_options[$post->contribution_to_id];
                        }
                    } else if ($post->transfer_to == 2) {
                        if ($post->contribution_to_id) {
                            if ($post->contribution_from_id == 'loan') {
                                echo 'from loan -' . $this->loans_m->get_loan_details($post->loan_from_id);
                            } else {
                                echo "'" . $contribution_options[$post->contribution_from_id] . "' contribution transfer to " . $contribution_options[$post->contribution_to_id] . ' fine ';
                            }
                        } else if ($post->fine_category_to_id) {
                            if ($post->contribution_from_id == 'loan') {
                                echo 'from loan -' . $this->loans_m->get_loan_details($post->loan_from_id);
                            } else {
                                echo "'" . $contribution_options[$post->contribution_from_id] . "' contribution transfer to " . $fine_category_options[$post->fine_category_to_id];
                            }
                        }
                        if ($post->fine_category_to_id) {
                            echo '- for ' . $fine_category_options[$post->fine_category_to_id];
                        } else {
                            echo ' - for ' . $contribution_options[$post->contribution_to_id];
                        }
                    } else if ($post->transfer_to == 3) {
                        echo ' To loan ' . $this->loans_m->get_loan_details($post->loan_to_id);
                        if ($post->loan_from_id) {
                            echo " from loan " . $this->loans_m->get_loan_details($post->loan_from_id);;
                        } else if ($post->contribution_from_id) {
                            echo " from contribution - " . $contribution_options[$post->contribution_from_id];
                        }
                    } else if ($post->transfer_to == 4) {
                        if ($post->contribution_from_id == "loan") {
                            if ($post->member_transfer_to == 1) {
                                echo $this->group_member_options[$post->member_id] . '\'s loan ' . $this->loans_m->get_loan_details($post->loan_from_id) . ' to ' . $this->group_member_options[$post->member_to_id] . '\'s  ' . $contribution_options[$post->contribution_to_id];
                            } else if ($post->member_transfer_to == 2) {
                                if ($post->fine_category_to_id) {
                                    echo $this->group_member_options[$post->member_id] . '\'s loan ' . $this->loans_m->get_loan_details($post->loan_from_id) . ' to ' . $this->group_member_options[$post->member_to_id] . '\'s  ' . $fine_category_options[$post->fine_category_to_id] . ' fine ';
                                } else {
                                    echo $this->group_member_options[$post->member_id] . '\'s  loan ' .
                                        $this->loans_m->get_loan_details($post->loan_from_id) . ' to ' .
                                        $this->group_member_options[$post->member_to_id] . '\'s ' . $contribution_options[$post->contribution_to_id] . ' fine.';
                                }
                            } else if ($post->member_transfer_to == 3) {
                                echo $this->group_member_options[$post->member_id] . '\'s loan ' . $this->loans_m->get_loan_details($post->loan_from_id) . ' to ' . $this->group_member_options[$post->member_to_id] . '\'s loan ' . $this->loans_m->get_loan_details($post->loan_to_id);
                            }
                        } else {
                            if ($post->member_transfer_to == 1) {
                                echo $this->group_member_options[$post->member_id] . '\'s ' . $contribution_options[$post->contribution_from_id] . ' to ' . $this->group_member_options[$post->member_to_id] . '\'s  ' . $contribution_options[$post->contribution_to_id];
                            } else if ($post->member_transfer_to == 2) {
                                if ($post->fine_category_to_id) {
                                    echo $this->group_member_options[$post->member_id] . '\'s ' . $contribution_options[$post->contribution_from_id] . ' to ' . $this->group_member_options[$post->member_to_id] . '\'s  ' . $fine_category_options[$post->fine_category_to_id] . ' fine ';
                                } else {
                                    echo $this->group_member_options[$post->member_id] . '\'s ' .
                                        $contribution_options[$post->contribution_from_id] . ' to ';
                                    if ($post->contribution_to_id) {
                                        echo $this->group_member_options[$post->member_to_id] . '\'s ' . $contribution_options[$post->contribution_to_id] . ' fine.';
                                    } else if ($post->fine_category_to_id) {
                                        echo $this->group_member_options[$post->member_to_id] . '\'s ' . $fine_category_options[$post->fine_category_to_id] . ' fine.';
                                    }
                                }
                            } else if ($post->member_transfer_to == 3) {
                                echo $this->group_member_options[$post->member_id] . '\'s ' . $contribution_options[$post->contribution_from_id] . ' to ' . $this->group_member_options[$post->member_to_id] . '\'s loan ' . $this->loans_m->get_loan_details($post->loan_to_id);
                            }
                        }
                    }
                    if ($post->description) {
                        echo ' : ' . $post->description . '';
                    }
                    echo '
                                    </td>
                                    <td>
                                        ' . number_to_currency($post->amount) . '
                                    </td>  
                                    <td>
                                        <a href="' . site_url('group/deposits/void_contribution_transfer/' . $post->id) . '" class="btn btn-sm confirm_link btn-danger m-btn m-btn--icon action_button" data-message="Are you sure you want to void transfer?">
                                        <span>
                                        <i class="la la-trash"></i>
                                            <span>
                                                '.translate('Void') . ' &nbsp;&nbsp;
                                            </span>
                                        </span> 
                                        </a>
                                    </td>
                                </tr>';
                endforeach;
                
                echo '
                        </tbody>
                    </table>
                   
                    <div class="row col-md-12">';
                if (!empty($pagination['links'])) :
                    echo $pagination['links'];
                endif;
                echo ' 
                    </div>
                    <div class="clearfix"></div>';
                if ($posts) :
                    echo ' <button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_void_contribution_transfers\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-trash-o\'></i> Bulk Void</button>';
                endif;
                echo form_close();
            } else {
                echo '
                    <div class="m-alert m-alert--outline alert alert-success alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                        <strong>' . translate('Information') . '!</strong>
                        ' . translate('No contribution transfers to display') . '.                            
                    </div>';
            }
        // } else {
        //     echo '
        //     <div class="container-fluid">
        //         <div class="row">
        //             <div class="col-md-12">
        //                 <div class="m-alert m-alert--outline alert alert-info fade show" role="alert"">
        //                     <strong>' . translate('Information') . '!</strong> ' . trnslate('You dont have rights to access this panel') . '
        //                 </div>
        //             </div>
        //         </div>
        //     </div>';
        // }
    }

    function record_loan_repayments()
    {
        $data = array();
        $posts = $_POST;
        $response = array();
        $errors = array();
        $error_messages = array();
        $loans_options = array();
        $successes = array();
        $entries_are_valid = TRUE;
        // if(isset($_GET) && !$this->input->post('submit') && $this->input->get('member_id')){
        //     $loans_options = $this->loans_m->get_active_member_loans_option($this->input->get('member_id'));
        // }
        if ($posts) {
            if (empty($posts)) {
                $response = array(
                    'status' => 0,
                    'message' => 'You have not submitted any repayments to process',
                );
            } else {
                if (isset($posts['deposit_dates'])) {
                    $count = count($posts['deposit_dates']);
                    for ($i = 0; $i <= $count; $i++) :
                        if (isset($posts['deposit_dates'][$i]) && isset($posts['members'][$i]) && isset($posts['loans'][$i]) && isset($posts['accounts'][$i]) && isset($posts['amounts'][$i]) && isset($posts['deposit_methods'][$i])) :
                            //Deposit dates
                            /*if($posts['deposit_dates'][$i]==''){
                                $successes['deposit_dates'][$i] = 0;
                                $errors['deposit_dates'][$i] = 1;
                                $error_messages['deposit_dates'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['deposit_dates'][$i] = 1;
                                $errors['deposit_dates'][$i] = 0;
                            }*/

                            if (valid_date($posts['deposit_dates'][$i])) {
                                $successes['deposit_dates'][$i] = 1;
                                $errors['deposit_dates'][$i] = 0;
                            } else {
                                $successes['deposit_dates'][$i] = 0;
                                $errors['deposit_dates'][$i] = 1;
                                $error_messages['deposit_dates'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            }
                            //Members
                            if ($posts['members'][$i] == '') {
                                $loans_options[] = array('' => '--Select member first--');
                                $successes['members'][$i] = 0;
                                $errors['members'][$i] = 1;
                                $error_messages['members'][$i] = '--Please select a member--';
                                $entries_are_valid = FALSE;
                            } else {
                                if (is_numeric($posts['members'][$i])) {
                                    $successes['members'][$i] = 1;
                                    $errors['members'][$i] = 0;
                                    if ($loans_option = $this->loans_m->get_active_member_loans_option($posts['members'][$i])) {
                                        $loans_options[] = array('' => '--Select a loan--') + $loans_option;
                                    } else {
                                        $loans_options[] = array('' => 'Member has no active loans');
                                    }
                                } else {
                                    $loans_options[] = array('' => '--Select member first--');
                                    $successes['members'][$i] = 0;
                                    $errors['members'][$i] = 1;
                                    $error_messages['members'][$i] = 'Please enter a valid member value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Loans
                            if ($posts['loans'][$i] == '') {
                                $successes['loans'][$i] = 0;
                                $errors['loans'][$i] = 1;
                                $error_messages['loans'][$i] = 'Please select loan ';
                                $entries_are_valid = FALSE;
                            } else {
                                if (is_numeric($posts['loans'][$i])) {
                                    $successes['loans'][$i] = 1;
                                    $errors['loans'][$i] = 0;
                                } else {
                                    $successes['loans'][$i] = 0;
                                    $errors['loans'][$i] = 1;
                                    $error_messages['loans'][$i] = 'Please select a valid loan value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Accounts
                            if ($posts['accounts'][$i] == '') {
                                $successes['accounts'][$i] = 0;
                                $errors['accounts'][$i] = 1;
                                $error_messages['accounts'][$i] = 'Please select an account';
                                $entries_are_valid = FALSE;
                            } else {
                                $successes['accounts'][$i] = 1;
                                $errors['accounts'][$i] = 0;
                            }
                            //Deposit Method
                            if ($posts['deposit_methods'][$i] == '') {
                                $successes['deposit_methods'][$i] = 0;
                                $errors['deposit_methods'][$i] = 1;
                                $error_messages['deposit_methods'][$i] = 'Please select a deposit method';
                                $entries_are_valid = FALSE;
                            } else {
                                if (is_numeric($posts['deposit_methods'][$i])) {
                                    $successes['deposit_methods'][$i] = 1;
                                    $errors['deposit_methods'][$i] = 0;
                                } else {
                                    $successes['deposit_methods'][$i] = 0;
                                    $errors['deposit_methods'][$i] = 1;
                                    $error_messages['deposit_methods'][$i] = 'Please enter a valid deposit method';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //amounts
                            if ($posts['amounts'][$i] == '' || $posts['amounts'][$i] < 1 || $posts['amounts'][$i] < 1) {
                                $successes['amounts'][$i] = 0;
                                $errors['amounts'][$i] = 1;
                                $error_messages['amounts'][$i] = 'Please enter a fine amount';
                                $entries_are_valid = FALSE;
                            } else {
                                if (valid_currency($posts['amounts'][$i])) {
                                    $successes['amounts'][$i] = 1;
                                    $errors['amounts'][$i] = 0;
                                } else {
                                    $successes['amounts'][$i] = 0;
                                    $errors['amounts'][$i] = 1;
                                    $error_messages['amounts'][$i] = 'Please enter a valid fine amount';
                                    $entries_are_valid = FALSE;
                                }
                            }
                        endif;
                    endfor;
                } else {
                    $entries_are_valid = FALSE;
                }

                if ($entries_are_valid) {
                    $successful_contribution_payment_entry_count = 0;
                    $unsuccessful_contribution_payment_entry_count = 0;
                    if (isset($posts['deposit_dates'])) {
                        $count = count($posts['deposit_dates']);
                        for ($i = 0; $i <= $count; $i++) :
                            if (isset($posts['deposit_dates'][$i]) && isset($posts['members'][$i]) && isset($posts['loans'][$i]) && isset($posts['accounts'][$i]) && isset($posts['amounts'][$i]) && isset($posts['deposit_methods'][$i])) :
                                $amount = valid_currency($posts['amounts'][$i]);
                                $deposit_date = strtotime($posts['deposit_dates'][$i]);
                                $send_sms_notification = isset($posts['send_sms_notification'][$i]) ? $posts['send_sms_notification'][$i] : 0;
                                $send_email_notification = isset($posts['send_email_notification'][$i]) ? $posts['send_email_notification'][$i] : 0;
                                $member = $this->members_m->get_group_member($posts['members'][$i], '');
                                $created_by = $this->members_m->get_group_member_by_user_id('', $this->user->id);
                                $description = isset($posts['deposit_descriptions'][$i]) ? xss_clean_input($posts['deposit_descriptions'][$i]) : '';
                                if ($this->loan->record_loan_repayment('', $deposit_date, $member, $posts['loans'][$i], $posts['accounts'][$i], $posts['deposit_methods'][$i], $description, $amount, $send_sms_notification, $send_email_notification, $created_by)) {
                                    $successful_contribution_payment_entry_count++;
                                } else {
                                    $unsuccessful_contribution_payment_entry_count++;
                                }
                            endif;
                        endfor;
                    }
                    if ($successful_contribution_payment_entry_count) {
                        if ($successful_contribution_payment_entry_count == 1) {
                            $response = array(
                                'status' => 1,
                                'message' => $successful_contribution_payment_entry_count . ' loan repayment successfully recorded. ',
                                'refer' => site_url('bank/loans/listing'),
                            );
                        } else {
                            $response = array(
                                'status' => 1,
                                'message' => $successful_contribution_payment_entry_count . ' loan repayments successfully recorded. ',
                                'refer' => site_url('bank/loans/listing'),
                            );
                        }
                    }
                    if ($unsuccessful_contribution_payment_entry_count) {
                        if ($unsuccessful_contribution_payment_entry_count == 1) {
                            $response = array(
                                'status' => 0,
                                'message' => $unsuccessful_contribution_payment_entry_count . ' loan repayment was not successfully recorded. ',
                            );
                        } else {
                            $response = array(
                                'status' => 0,
                                'message' => $unsuccessful_contribution_payment_entry_count . ' loan repayments were not successfully recorded. ',
                            );
                        }
                    }
                } else {
                    $response = array(
                        'status' => 0,
                        'message' => 'There are some errors on the form. Please review and try again.',
                    );
                }
            }
        } else {
            $response = array(
                'status' => 0,
                'message' => 'You have not submitted any repayments to process',
            );
        }
        echo json_encode($response);
    }

    function record_debtor_loan_repayments()
    {
        $data = array();
        $posts = $_POST;
        $response = array();
        $errors = array();
        $error_messages = array();
        $loans_options = array();
        $successes = array();
        $entries_are_valid = TRUE;
        // if(isset($_GET) && !$this->input->post('submit') && $this->input->get('member_id')){
        //     $loans_options = $this->loans_m->get_active_member_loans_option($this->input->get('member_id'));
        // }
        if ($posts) {
            if (empty($posts)) {
                $response = array(
                    'status' => 0,
                    'message' => 'You have not submitted any repayments to process',
                );
            } else {
                if (isset($posts['deposit_dates'])) {
                    $count = count($posts['deposit_dates']);
                    for ($i = 0; $i <= $count; $i++) :
                        if (isset($posts['deposit_dates'][$i]) && isset($posts['debtors'][$i]) && isset($posts['loans'][$i]) && isset($posts['accounts'][$i]) && isset($posts['amounts'][$i]) && isset($posts['deposit_methods'][$i])) :
                            //Deposit dates
                            if ($posts['deposit_dates'][$i] == '') {
                                $successes['deposit_dates'][$i] = 0;
                                $errors['deposit_dates'][$i] = 1;
                                $error_messages['deposit_dates'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            } else {
                                $successes['deposit_dates'][$i] = 1;
                                $errors['deposit_dates'][$i] = 0;
                            }
                            //Debtors
                            if ($posts['debtors'][$i] == '') {
                                $loans_options[] = array('' => '--Select debtor first--');
                                $successes['debtors'][$i] = 0;
                                $errors['debtors'][$i] = 1;
                                $error_messages['debtors'][$i] = '--Please select a debtor--';
                                $entries_are_valid = FALSE;
                            } else {
                                if (is_numeric($posts['debtors'][$i])) {
                                    $successes['debtors'][$i] = 1;
                                    $errors['debtors'][$i] = 0;
                                    if ($loans_option = $this->debtors_m->get_active_debtor_loans_option($posts['debtors'][$i])) {
                                        $loans_options[] = array('' => '--Select a loan--') + $loans_option;
                                    } else {
                                        $loans_options[] = array('' => 'Debtor has no active loans');
                                    }
                                } else {
                                    $loans_options[] = array('' => '--Select debtor first--');
                                    $successes['debtors'][$i] = 0;
                                    $errors['debtors'][$i] = 1;
                                    $error_messages['debtors'][$i] = 'Please enter a valid debtor value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Loans
                            if ($posts['loans'][$i] == '') {
                                $successes['loans'][$i] = 0;
                                $errors['loans'][$i] = 1;
                                $error_messages['loans'][$i] = 'Please select loan ';
                                $entries_are_valid = FALSE;
                            } else {
                                if (is_numeric($posts['loans'][$i])) {
                                    $successes['loans'][$i] = 1;
                                    $errors['loans'][$i] = 0;
                                } else {
                                    $successes['loans'][$i] = 0;
                                    $errors['loans'][$i] = 1;
                                    $error_messages['loans'][$i] = 'Please select a valid loan value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Accounts
                            if ($posts['accounts'][$i] == '') {
                                $successes['accounts'][$i] = 0;
                                $errors['accounts'][$i] = 1;
                                $error_messages['accounts'][$i] = 'Please select an account';
                                $entries_are_valid = FALSE;
                            } else {
                                $successes['accounts'][$i] = 1;
                                $errors['accounts'][$i] = 0;
                            }
                            //Deposit Method
                            if ($posts['deposit_methods'][$i] == '') {
                                $successes['deposit_methods'][$i] = 0;
                                $errors['deposit_methods'][$i] = 1;
                                $error_messages['deposit_methods'][$i] = 'Please select a deposit method';
                                $entries_are_valid = FALSE;
                            } else {
                                if (is_numeric($posts['deposit_methods'][$i])) {
                                    $successes['deposit_methods'][$i] = 1;
                                    $errors['deposit_methods'][$i] = 0;
                                } else {
                                    $successes['deposit_methods'][$i] = 0;
                                    $errors['deposit_methods'][$i] = 1;
                                    $error_messages['deposit_methods'][$i] = 'Please enter a valid deposit method';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //amounts
                            if ($posts['amounts'][$i] == '' || $posts['amounts'][$i] < 1) {
                                $successes['amounts'][$i] = 0;
                                $errors['amounts'][$i] = 1;
                                $error_messages['amounts'][$i] = 'Please enter a fine amount';
                                $entries_are_valid = FALSE;
                            } else {
                                if (valid_currency($posts['amounts'][$i])) {
                                    $successes['amounts'][$i] = 1;
                                    $errors['amounts'][$i] = 0;
                                } else {
                                    $successes['amounts'][$i] = 0;
                                    $errors['amounts'][$i] = 1;
                                    $error_messages['amounts'][$i] = 'Please enter a valid fine amount';
                                    $entries_are_valid = FALSE;
                                }
                            }
                        endif;
                    endfor;
                } else {
                    $entries_are_valid = FALSE;
                }

                if ($entries_are_valid) {
                    $this->session->set_flashdata('error', '');
                    $successful_contribution_payment_entry_count = 0;
                    $unsuccessful_contribution_payment_entry_count = 0;
                    if (isset($posts['deposit_dates'])) {
                        $count = count($posts['deposit_dates']);
                        for ($i = 0; $i <= $count; $i++) :
                            if (isset($posts['deposit_dates'][$i]) && isset($posts['debtors'][$i]) && isset($posts['loans'][$i]) && isset($posts['accounts'][$i]) && isset($posts['amounts'][$i]) && isset($posts['deposit_methods'][$i])) :
                                $amount = valid_currency($posts['amounts'][$i]);
                                $deposit_date = strtotime($posts['deposit_dates'][$i]);
                                $send_sms_notification = isset($posts['send_sms_notification'][$i]) ? $posts['send_sms_notification'][$i] : 0;
                                $send_email_notification = isset($posts['send_email_notification'][$i]) ? $posts['send_email_notification'][$i] : 0;
                                $debtor = $this->debtors_m->get($posts['debtors'][$i], $this->group->id);


                                $created_by = $this->members_m->get_group_member_by_user_id($this->group->id, $this->user->id);

                                $description = isset($posts['deposit_descriptions'][$i]) ? xss_clean_input($posts['deposit_descriptions'][$i]) : '';

                                if ($this->loan->record_debtor_loan_repayment(
                                    $this->group->id,
                                    $deposit_date,
                                    $debtor,
                                    $posts['loans'][$i],
                                    $posts['accounts'][$i],
                                    $posts['deposit_methods'][$i],
                                    $description,
                                    $amount,
                                    $send_sms_notification,
                                    $send_email_notification,
                                    $created_by
                                )) {
                                    $successful_contribution_payment_entry_count++;
                                } else {
                                    $unsuccessful_contribution_payment_entry_count++;
                                }
                            endif;
                        endfor;
                    }


                    if ($successful_contribution_payment_entry_count) {
                        if ($successful_contribution_payment_entry_count == 1) {
                            $response = array(
                                'status' => 1,
                                'message' => $successful_contribution_payment_entry_count . ' loan repayment successfully recorded. ',
                                'refer' => site_url('group/deposits/listing'),
                            );
                        } else {
                            $response = array(
                                'status' => 1,
                                'message' => $successful_contribution_payment_entry_count . ' loan repayments successfully recorded. ',
                                'refer' => site_url('group/deposits/listing'),
                            );
                        }
                    }
                    if ($unsuccessful_contribution_payment_entry_count) {
                        if ($unsuccessful_contribution_payment_entry_count == 1) {
                            $response = array(
                                'status' => 0,
                                'message' => $unsuccessful_contribution_payment_entry_count . ' loan repayment was not successfully recorded. ',
                            );
                        } else {
                            $response = array(
                                'status' => 0,
                                'message' => $unsuccessful_contribution_payment_entry_count . ' loan repayments were not successfully recorded. ',
                            );
                        }
                    }
                } else {
                    $response = array(
                        'status' => 0,
                        'message' => 'There are some errors on the form. Please review and try again.',
                    );
                }
            }
        } else {
            $response = array(
                'status' => 0,
                'message' => 'You have not submitted any repayments to process',
            );
        }
        echo json_encode($response);
    }

    function record_contribution_payments()
    {
        $data = array();
        $posts = $_POST;
        $errors = array();
        $response = array();
        $error_messages = array();
        $successes = array();
        $entries_are_valid = TRUE;
        if ($posts) {
            if (empty($posts)) {
                $response = array(
                    'status' => 0,
                    'message' => 'You have not submitted any payments to process',
                );
            } else {
                if (isset($posts['deposit_dates'])) {
                    $count = count($posts['deposit_dates']);
                    for ($i = 0; $i <= $count; $i++) :
                        if (isset($posts['deposit_dates'][$i]) && isset($posts['members'][$i]) && isset($posts['contributions'][$i]) && isset($posts['accounts'][$i]) && isset($posts['amounts'][$i]) && isset($posts['deposit_methods'][$i])) :
                            //Deposit dates
                            if (valid_date($posts['deposit_dates'][$i])) {
                                $successes['deposit_dates'][$i] = 1;
                                $errors['deposit_dates'][$i] = 0;
                            } else {
                                $successes['deposit_dates'][$i] = 0;
                                $errors['deposit_dates'][$i] = 1;
                                $error_messages['deposit_dates'][$i] = 'Please enter a valid date';
                                $entries_are_valid = FALSE;
                            }

                            //Members
                            if ($posts['members'][$i] == '') {
                                $successes['members'][$i] = 0;
                                $errors['members'][$i] = 1;
                                $error_messages['members'][$i] = 'Please select a member';
                                $entries_are_valid = FALSE;
                            } else {
                                if (is_numeric($posts['members'][$i])) {
                                    $successes['members'][$i] = 1;
                                    $errors['members'][$i] = 0;
                                } else {
                                    $successes['members'][$i] = 0;
                                    $errors['members'][$i] = 1;
                                    $error_messages['members'][$i] = 'Please enter a valid member value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Contributions
                            if ($posts['contributions'][$i] == '') {
                                $successes['contributions'][$i] = 0;
                                $errors['contributions'][$i] = 1;
                                $error_messages['contributions'][$i] = 'Please select a contribution';
                                $entries_are_valid = FALSE;
                            } else {
                                if (is_numeric($posts['contributions'][$i])) {
                                    $successes['contributions'][$i] = 1;
                                    $errors['contributions'][$i] = 0;
                                } else {
                                    $successes['contributions'][$i] = 0;
                                    $errors['contributions'][$i] = 1;
                                    $error_messages['contributions'][$i] = 'Please select a valid contribution value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Accounts
                            if ($posts['accounts'][$i] == '') {
                                $successes['accounts'][$i] = 0;
                                $errors['accounts'][$i] = 1;
                                $error_messages['accounts'][$i] = 'Please select an account';
                                $entries_are_valid = FALSE;
                            } else {
                                $successes['accounts'][$i] = 1;
                                $errors['accounts'][$i] = 0;
                            }
                            //Deposit Method
                            if ($posts['deposit_methods'][$i] == '') {
                                $successes['deposit_methods'][$i] = 0;
                                $errors['deposit_methods'][$i] = 1;
                                $error_messages['deposit_methods'][$i] = 'Please select a deposit method';
                                $entries_are_valid = FALSE;
                            } else {
                                if (is_numeric($posts['deposit_methods'][$i])) {
                                    $successes['deposit_methods'][$i] = 1;
                                    $errors['deposit_methods'][$i] = 0;
                                } else {
                                    $successes['deposit_methods'][$i] = 0;
                                    $errors['deposit_methods'][$i] = 1;
                                    $error_messages['deposit_methods'][$i] = 'Please enter a valid deposit method';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //amounts
                            if ($posts['amounts'][$i] == '' || $posts['amounts'][$i] < 1) {
                                $successes['amounts'][$i] = 0;
                                $errors['amounts'][$i] = 1;
                                $error_messages['amounts'][$i] = 'Please enter a valid contribution amount';
                                $entries_are_valid = FALSE;
                            } else {
                                if (valid_currency($posts['amounts'][$i])) {
                                    $successes['amounts'][$i] = 1;
                                    $errors['amounts'][$i] = 0;
                                } else {
                                    $successes['amounts'][$i] = 0;
                                    $errors['amounts'][$i] = 1;
                                    $error_messages['amounts'][$i] = 'Please enter a valid fine amount';
                                    $entries_are_valid = FALSE;
                                }
                            }
                        endif;
                    endfor;
                } else {
                    $entries_are_valid = FALSE;
                }
                /*print_r($posts);
                echo "<br>";
                print_r($entries_are_valid); die();*/
                if ($entries_are_valid) {
                    $successful_contribution_payment_entry_count = 0;
                    $unsuccessful_contribution_payment_entry_count = 0;
                    $contribution_payments = array();
                    if (isset($posts['deposit_dates'])) {
                        $count = count($posts['deposit_dates']);
                        for ($i = 0; $i <= $count; $i++) :
                            if (isset($posts['deposit_dates'][$i]) && isset($posts['members'][$i]) && isset($posts['contributions'][$i]) && isset($posts['accounts'][$i]) && isset($posts['amounts'][$i]) && isset($posts['deposit_methods'][$i])) :

                                $contribution_payment = new stdClass();

                                $amount = currency($posts['amounts'][$i]);
                                $deposit_date = strtotime($posts['deposit_dates'][$i]);
                                $send_sms_notification = isset($posts['send_sms_notification'][$i]) ? $posts['send_sms_notification'][$i] : 0;
                                $send_email_notification = isset($posts['send_email_notification'][$i]) ? $posts['send_email_notification'][$i] : 0;
                                $description = isset($posts['deposit_descriptions'][$i]) ? $posts['deposit_descriptions'][$i] : '';

                                $contribution_payment->deposit_date = $deposit_date;
                                $contribution_payment->member_id = $posts['members'][$i];
                                $contribution_payment->contribution_id = $posts['contributions'][$i];
                                $contribution_payment->account_id = $posts['accounts'][$i];
                                $contribution_payment->amount = $amount;
                                $contribution_payment->deposit_method = $posts['deposit_methods'][$i];
                                $contribution_payment->send_sms_notification = $send_sms_notification;
                                $contribution_payment->send_email_notification = $send_email_notification;
                                $contribution_payment->description = xss_clean_input($description);

                                $contribution_payments[] = $contribution_payment;
                            endif;
                        endfor;

                        if ($this->transactions->record_group_contribution_payments($this->group->id, $contribution_payments)) {
                            $response = array(
                                'status' => 1,
                                'message' => 'Contributions recorded successfully.',
                                'refer' => site_url('group/deposits/listing'),
                            );
                        } else {
                            $response = array(
                                'status' => 1,
                                'message' => 'Something went wrong while recording the contribution payments.',
                                'refer' => site_url('group/deposits/listing'),
                            );
                        }
                    }
                    if ($successful_contribution_payment_entry_count) {
                        if ($successful_contribution_payment_entry_count == 1) {
                            $response = array(
                                'status' => 1,
                                'message' => $successful_contribution_payment_entry_count . ' contribution payment successfully recorded.',
                                'refer' => site_url('group/deposits/listing'),
                            );
                        } else {
                            $response = array(
                                'status' => 1,
                                'message' => $successful_contribution_payment_entry_count . ' contribution payments successfully recorded.',
                                'refer' => site_url('group/deposits/listing'),
                            );
                        }
                    }
                    if ($unsuccessful_contribution_payment_entry_count) {
                        if ($unsuccessful_contribution_payment_entry_count == 1) {
                            $response = array(
                                'status' => 0,
                                'message' => $unsuccessful_contribution_payment_entry_count . ' contribution payment was not successfully recorded.',
                            );
                        } else {
                            $response = array(
                                'status' => 0,
                                'message' => $unsuccessful_contribution_payment_entry_count . ' contribution payments were not successfully recorded. ',
                            );
                        }
                    }
                } else {
                    $response = array(
                        'status' => 0,
                        'message' => 'There are some errors on the form. Please review and try again.',
                        'validation_errors' => $error_messages,
                    );
                }
            }
        } else {
            $response = array(
                'status' => 0,
                'message' => 'You have not submitted any payments to process',
            );
        }
        echo json_encode($response);
    }

    function record_fine_payments()
    {
        $data = array();
        $posts = $_POST;
        $errors = array();
        $response = array();
        $successes = array();
        $entries_are_valid = TRUE;
        if ($posts) {
            if (empty($posts)) {
                $response = array(
                    'status' => 0,
                    'message' => 'You have not submitted any payments to process',
                );
            } else {
                if (isset($posts['deposit_dates'])) {
                    $count = count($posts['deposit_dates']);
                    for ($i = 0; $i <= $count; $i++) :
                        if (isset($posts['deposit_dates'][$i]) && isset($posts['members'][$i]) && isset($posts['fine_categories'][$i]) && isset($posts['accounts'][$i]) && isset($posts['amounts'][$i]) && isset($posts['deposit_methods'][$i])) :
                            //Deposit dates
                            if (valid_date($posts['deposit_dates'][$i])) {
                                $successes['deposit_dates'][$i] = 1;
                                $errors['deposit_dates'][$i] = 0;
                            } else {
                                $successes['deposit_dates'][$i] = 0;
                                $errors['deposit_dates'][$i] = 1;
                                $error_messages['deposit_dates'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            }
                            //Members
                            if ($posts['members'][$i] == '') {
                                $successes['members'][$i] = 0;
                                $errors['members'][$i] = 1;
                                $entries_are_valid = FALSE;
                            } else {
                                if (is_numeric($posts['members'][$i])) {
                                    $successes['members'][$i] = 1;
                                    $errors['members'][$i] = 0;
                                } else {
                                    $successes['members'][$i] = 0;
                                    $errors['members'][$i] = 1;
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Fine categories
                            if ($posts['fine_categories'][$i] == '') {
                                $successes['fine_categories'][$i] = 0;
                                $errors['fine_categories'][$i] = 1;
                                $entries_are_valid = FALSE;
                            } else {
                                $successes['fine_categories'][$i] = 1;
                                $errors['fine_categories'][$i] = 0;
                            }
                            //Accounts
                            if ($posts['accounts'][$i] == '') {
                                $successes['accounts'][$i] = 0;
                                $errors['accounts'][$i] = 1;
                                $entries_are_valid = FALSE;
                            } else {
                                $successes['accounts'][$i] = 1;
                                $errors['accounts'][$i] = 0;
                            }
                            //Deposit Method
                            if ($posts['deposit_methods'][$i] == '') {
                                $successes['deposit_methods'][$i] = 0;
                                $errors['deposit_methods'][$i] = 1;
                                $entries_are_valid = FALSE;
                            } else {
                                if (is_numeric($posts['deposit_methods'][$i])) {
                                    $successes['deposit_methods'][$i] = 1;
                                    $errors['deposit_methods'][$i] = 0;
                                } else {
                                    $successes['deposit_methods'][$i] = 0;
                                    $errors['deposit_methods'][$i] = 1;
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //amounts
                            if ($posts['amounts'][$i] == '' || $posts['amounts'][$i] < 1) {
                                $successes['amounts'][$i] = 0;
                                $errors['amounts'][$i] = 1;
                                $entries_are_valid = FALSE;
                            } else {
                                if (valid_currency($posts['amounts'][$i])) {
                                    $successes['amounts'][$i] = 1;
                                    $errors['amounts'][$i] = 0;
                                } else {
                                    $successes['amounts'][$i] = 0;
                                    $errors['amounts'][$i] = 1;
                                    $entries_are_valid = FALSE;
                                }
                            }
                        endif;
                    endfor;
                } else {
                    $entries_are_valid = FALSE;
                }
                if ($entries_are_valid) {
                    $successful_fine_payment_entry_count = 0;
                    $unsuccessful_fine_payment_entry_count = 0;
                    $count = count($posts['deposit_dates']);
                    for ($i = 0; $i <= $count; $i++) :
                        if (isset($posts['deposit_dates'][$i]) && isset($posts['members'][$i]) && isset($posts['fine_categories'][$i]) && isset($posts['accounts'][$i]) && isset($posts['amounts'][$i]) && isset($posts['deposit_methods'][$i])) :
                            $amount = valid_currency($posts['amounts'][$i]);
                            $deposit_date = strtotime($posts['deposit_dates'][$i]);
                            $send_sms_notification = isset($posts['send_sms_notification'][$i]) ? $posts['send_sms_notification'][$i] : 0;
                            $send_email_notification = isset($posts['send_email_notification'][$i]) ? $posts['send_email_notification'][$i] : 0;
                            $description = isset($posts['deposit_descriptions'][$i]) ? xss_clean_input($posts['deposit_descriptions'][$i]) : '';
                            if ($this->transactions->record_fine_payment($this->group->id, $deposit_date, $posts['members'][$i], $posts['fine_categories'][$i], $posts['accounts'][$i], $posts['deposit_methods'][$i], $description, $amount, $send_sms_notification, $send_email_notification)) {
                                $successful_fine_payment_entry_count++;
                            } else {
                                $unsuccessful_fine_payment_entry_count++;
                            }
                        endif;
                    endfor;
                    if ($successful_fine_payment_entry_count) {
                        if ($successful_fine_payment_entry_count == 1) {
                            $response = array(
                                'status' => 1,
                                'message' => $successful_fine_payment_entry_count . ' fine payment successfully recorded.',
                                'refer' => site_url('group/deposits/listing'),
                            );
                        } else {
                            $response = array(
                                'status' => 1,
                                'message' => $successful_fine_payment_entry_count . ' fine payments successfully recorded.',
                                'refer' => site_url('group/deposits/listing'),
                            );
                        }
                    }
                    if ($unsuccessful_fine_payment_entry_count) {
                        if ($unsuccessful_fine_payment_entry_count == 1) {
                            $response = array(
                                'status' => 0,
                                'message' => $unsuccessful_fine_payment_entry_count . ' fine payment was not successfully recorded.',
                            );
                        } else {
                            $response = array(
                                'status' => 0,
                                'message' => $unsuccessful_fine_payment_entry_count . ' fine payments were not successfully recorded.',
                            );
                        }
                    }
                } else {
                    $response = array(
                        'status' => 0,
                        'message' => 'There are some errors on the form. Please review and try again.',
                    );
                }
            }
        } else {
            $response = array(
                'status' => 0,
                'message' => 'You have not submitted any payments to process',
            );
        }
        echo json_encode($response);
    }

    function record_income()
    {
        $data = array();
        $posts = $_POST;
        $errors = array();
        $response = array();
        $successes = array();
        $entries_are_valid = TRUE;
        if ($posts) {
            if (empty($posts)) {
                $response = array(
                    'status' => 0,
                    'message' => 'You have not submitted any payments to process',
                );
            } else {
                if (isset($posts['deposit_dates'])) {
                    $count = count($posts['deposit_dates']);
                    for ($i = 0; $i <= $count; $i++) :
                        if (isset($posts['deposit_dates'][$i]) && isset($posts['depositors'][$i]) && isset($posts['income_categories'][$i]) && isset($posts['accounts'][$i]) && isset($posts['amounts'][$i]) && isset($posts['deposit_methods'][$i])) :
                            //Deposit dates
                            if (valid_date($posts['deposit_dates'][$i])) {
                                $successes['deposit_dates'][$i] = 1;
                                $errors['deposit_dates'][$i] = 0;
                            } else {
                                $successes['deposit_dates'][$i] = 0;
                                $errors['deposit_dates'][$i] = 1;
                                $error_messages['deposit_dates'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            }
                            //Members
                            if ($posts['depositors'][$i] == '') {
                                $successes['depositors'][$i] = 0;
                                $errors['depositors'][$i] = 1;
                                $entries_are_valid = FALSE;
                            } else {
                                if (is_numeric($posts['depositors'][$i])) {
                                    $successes['depositors'][$i] = 1;
                                    $errors['depositors'][$i] = 0;
                                } else {
                                    $successes['depositors'][$i] = 0;
                                    $errors['depositors'][$i] = 1;
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Fine categories
                            if ($posts['income_categories'][$i] == '') {
                                $successes['income_categories'][$i] = 0;
                                $errors['income_categories'][$i] = 1;
                                $entries_are_valid = FALSE;
                            } else {
                                $successes['income_categories'][$i] = 1;
                                $errors['income_categories'][$i] = 0;
                            }
                            //Accounts
                            if ($posts['accounts'][$i] == '') {
                                $successes['accounts'][$i] = 0;
                                $errors['accounts'][$i] = 1;
                                $entries_are_valid = FALSE;
                            } else {
                                $successes['accounts'][$i] = 1;
                                $errors['accounts'][$i] = 0;
                            }
                            //Deposit Method
                            if ($posts['deposit_methods'][$i] == '') {
                                $successes['deposit_methods'][$i] = 0;
                                $errors['deposit_methods'][$i] = 1;
                                $entries_are_valid = FALSE;
                            } else {
                                if (is_numeric($posts['deposit_methods'][$i])) {
                                    $successes['deposit_methods'][$i] = 1;
                                    $errors['deposit_methods'][$i] = 0;
                                } else {
                                    $successes['deposit_methods'][$i] = 0;
                                    $errors['deposit_methods'][$i] = 1;
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //amounts
                            if ($posts['amounts'][$i] == '' || $posts['amounts'][$i] < 1) {
                                $successes['amounts'][$i] = 0;
                                $errors['amounts'][$i] = 1;
                                $entries_are_valid = FALSE;
                            } else {
                                if (valid_currency($posts['amounts'][$i])) {
                                    $successes['amounts'][$i] = 1;
                                    $errors['amounts'][$i] = 0;
                                } else {
                                    $successes['amounts'][$i] = 0;
                                    $errors['amounts'][$i] = 1;
                                    $entries_are_valid = FALSE;
                                }
                            }
                        endif;
                    endfor;
                } else {
                    $entries_are_valid = FALSE;
                }
                if ($entries_are_valid) {
                    $successful_income_entry_count = 0;
                    $unsuccessful_income_entry_count = 0;
                    $count = count($posts['deposit_dates']);
                    for ($i = 0; $i <= $count; $i++) :
                        if (isset($posts['deposit_dates'][$i]) && isset($posts['depositors'][$i]) && isset($posts['income_categories'][$i]) && isset($posts['accounts'][$i]) && isset($posts['amounts'][$i]) && isset($posts['deposit_methods'][$i])) :
                            $amount = valid_currency($posts['amounts'][$i]);
                            $deposit_date = strtotime($posts['deposit_dates'][$i]);
                            $description = isset($posts['deposit_descriptions'][$i]) ? xss_clean_input($posts['deposit_descriptions'][$i]) : '';
                            if ($this->transactions->record_income_deposit($this->group->id, $deposit_date, $posts['depositors'][$i], $posts['income_categories'][$i], $posts['accounts'][$i], $posts['deposit_methods'][$i], $description, $amount)) {
                                $successful_income_entry_count++;
                            } else {
                                $unsuccessful_income_entry_count++;
                            }
                        endif;
                    endfor;

                    if ($successful_income_entry_count) {
                        if ($successful_income_entry_count == 1) {
                            $response = array(
                                'status' => 1,
                                'message' => $successful_income_entry_count . ' income payment successfully recorded.',
                                'refer' => site_url('group/deposits/listing'),
                            );
                        } else {
                            $response = array(
                                'status' => 1,
                                'message' => $successful_income_entry_count . ' income payments successfully recorded.',
                                'refer' => site_url('group/deposits/listing'),
                            );
                        }
                    }
                    if ($unsuccessful_income_entry_count) {
                        if ($unsuccessful_income_entry_count == 1) {
                            $response = array(
                                'status' => 0,
                                'message' => $unsuccessful_income_entry_count . ' income was not recorded.',
                            );
                        } else {
                            $response = array(
                                'status' => 0,
                                'message' => $unsuccessful_income_entry_count . ' income were not recorded.',
                            );
                        }
                    }
                } else {
                    $response = array(
                        'status' => 0,
                        'message' => 'There are some errors on the form. Please review and try again.',
                    );
                }
            }
        } else {
            $response = array(
                'status' => 0,
                'message' => 'You have not submitted any payments to process',
            );
        }
        echo json_encode($response);
    }

    function record_miscellaneous_payments()
    {
        $data = array();
        $posts = $_POST;
        $response = array();
        $errors = array();
        $error_messages = array();
        $successes = array();
        if ($posts) {
            $entries_are_valid = TRUE;
            if (empty($posts)) {
                $response = array(
                    'status' => 0,
                    'message' => 'You have not submitted any payments to process',
                );
            } else {
                if (isset($posts['deposit_dates'])) {
                    $count = count($posts['deposit_dates']);
                    for ($i = 0; $i <= $count; $i++) :
                        if (isset($posts['deposit_dates'][$i]) && isset($posts['members'][$i]) && isset($posts['miscellaneous_deposit_descriptions'][$i]) && isset($posts['accounts'][$i]) && isset($posts['amounts'][$i]) && isset($posts['deposit_methods'][$i])) :
                            //Deposit dates
                            if (valid_date($posts['deposit_dates'][$i])) {
                                $successes['deposit_dates'][$i] = 1;
                                $errors['deposit_dates'][$i] = 0;
                            } else {
                                $successes['deposit_dates'][$i] = 0;
                                $errors['deposit_dates'][$i] = 1;
                                $error_messages['deposit_dates'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            }
                            //Members
                            if ($posts['members'][$i] == '') {
                                $successes['members'][$i] = 0;
                                $errors['members'][$i] = 1;
                                $error_messages['members'][$i] = 'Please select a member';
                                $entries_are_valid = FALSE;
                            } else {
                                if (is_numeric($posts['members'][$i])) {
                                    $successes['members'][$i] = 1;
                                    $errors['members'][$i] = 0;
                                } else {
                                    $successes['members'][$i] = 0;
                                    $errors['members'][$i] = 1;
                                    $error_messages['members'][$i] = 'Please enter a valid member value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Fine categories
                            if ($posts['miscellaneous_deposit_descriptions'][$i] == '') {
                                $successes['miscellaneous_deposit_descriptions'][$i] = 0;
                                $errors['miscellaneous_deposit_descriptions'][$i] = 1;
                                $error_messages['miscellaneous_deposit_descriptions'][$i] = 'Please enter a description';
                                $entries_are_valid = FALSE;
                            } else {
                                $successes['miscellaneous_deposit_descriptions'][$i] = 1;
                                $errors['miscellaneous_deposit_descriptions'][$i] = 0;
                            }
                            //Accounts
                            if ($posts['accounts'][$i] == '') {
                                $successes['accounts'][$i] = 0;
                                $errors['accounts'][$i] = 1;
                                $error_messages['accounts'][$i] = 'Please select an account';
                                $entries_are_valid = FALSE;
                            } else {
                                $successes['accounts'][$i] = 1;
                                $errors['accounts'][$i] = 0;
                            }
                            //Deposit Method
                            if ($posts['deposit_methods'][$i] == '') {
                                $successes['deposit_methods'][$i] = 0;
                                $errors['deposit_methods'][$i] = 1;
                                $error_messages['deposit_methods'][$i] = 'Please select a deposit method';
                                $entries_are_valid = FALSE;
                            } else {
                                if (is_numeric($posts['deposit_methods'][$i])) {
                                    $successes['deposit_methods'][$i] = 1;
                                    $errors['deposit_methods'][$i] = 0;
                                } else {
                                    $successes['deposit_methods'][$i] = 0;
                                    $errors['deposit_methods'][$i] = 1;
                                    $error_messages['deposit_methods'][$i] = 'Please enter a valid deposit method';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //amounts
                            if ($posts['amounts'][$i] == '' || $posts['amounts'][$i] < 1) {
                                $successes['amounts'][$i] = 0;
                                $errors['amounts'][$i] = 1;
                                $error_messages['amounts'][$i] = 'Please enter a fine amount';
                                $entries_are_valid = FALSE;
                            } else {
                                if (valid_currency($posts['amounts'][$i])) {
                                    $successes['amounts'][$i] = 1;
                                    $errors['amounts'][$i] = 0;
                                } else {
                                    $successes['amounts'][$i] = 0;
                                    $errors['amounts'][$i] = 1;
                                    $error_messages['amounts'][$i] = 'Please enter a valid fine amount';
                                    $entries_are_valid = FALSE;
                                }
                            }
                        endif;
                    endfor;
                } else {
                    $entries_are_valid = FALSE;
                }
                if ($entries_are_valid) {
                    $successful_miscellaneous_payment_entry_count = 0;
                    $unsuccessful_miscellaneous_payment_entry_count = 0;
                    $count = count($posts['deposit_dates']);
                    for ($i = 0; $i <= $count; $i++) :
                        if (isset($posts['deposit_dates'][$i]) && isset($posts['members'][$i]) && isset($posts['miscellaneous_deposit_descriptions'][$i]) && isset($posts['accounts'][$i]) && isset($posts['amounts'][$i]) && isset($posts['deposit_methods'][$i])) :
                            $amount = valid_currency($posts['amounts'][$i]);
                            $deposit_date = strtotime($posts['deposit_dates'][$i]);
                            $send_sms_notification = isset($posts['send_sms_notification'][$i]) ? $posts['send_sms_notification'][$i] : 0;
                            $send_email_notification = isset($posts['send_email_notification'][$i]) ? $posts['send_email_notification'][$i] : 0;
                            $description = isset($posts['miscellaneous_deposit_descriptions'][$i]) ? xss_clean_input($posts['miscellaneous_deposit_descriptions'][$i]) : '';
                            if ($this->transactions->record_miscellaneous_payment($this->group->id, $deposit_date, $posts['members'][$i], $posts['accounts'][$i], $posts['deposit_methods'][$i], $description, $amount, $send_sms_notification, $send_email_notification)) {
                                $successful_miscellaneous_payment_entry_count++;
                            } else {
                                $unsuccessful_miscellaneous_payment_entry_count++;
                            }
                        endif;
                    endfor;
                    if ($successful_miscellaneous_payment_entry_count) {
                        if ($successful_miscellaneous_payment_entry_count == 1) {
                            $response = array(
                                'status' => 1,
                                'message' => $successful_miscellaneous_payment_entry_count . ' miscellaneous payment successfully recorded.',
                                'refer' => site_url('group/deposits/listing'),
                            );
                        } else {
                            $response = array(
                                'status' => 1,
                                'message' => $successful_miscellaneous_payment_entry_count . ' miscellaneous payments successfully recorded.',
                                'refer' => site_url('group/deposits/listing'),
                            );
                        }
                    }
                    if ($unsuccessful_miscellaneous_payment_entry_count) {
                        if ($unsuccessful_miscellaneous_payment_entry_count == 1) {
                            $response = array(
                                'status' => 0,
                                'message' => $unsuccessful_miscellaneous_payment_entry_count . ' miscellaneous payment was not recorded.',
                            );
                        } else {
                            $response = array(
                                'status' => 0,
                                'message' => $unsuccessful_miscellaneous_payment_entry_count . ' miscellaneous payments were not recorded.',
                            );
                        }
                    }
                } else {
                    $response = array(
                        'status' => 0,
                        'message' => 'There are some errors on the form. Please review and try again.',
                    );
                }
            }
        } else {
            $response = array(
                'status' => 0,
                'message' => 'You have not submitted any payments to process',
            );
        }
        echo json_encode($response);
    }

    function _is_contribution_from_id_does_not_match_contribution_to_id()
    {
        if ($this->input->post('transfer_to') == 1) {
            if ($this->input->post('contribution_from_id') == $this->input->post('contribution_to_id')) {
                $this->form_validation->set_message('_is_contribution_from_id_does_not_match_contribution_to_id', 'The contribution from must be different from contribution to.');
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return TRUE;
        }
    }

    function _loan_transfer_to_is_not_same_as_loan_from()
    {
        if ($this->input->post('contribution_from_id') == 'loan') {
            $loan_from_id = $this->input->post('loan_from_id');
            $loan_to_id = $this->input->post('loan_to_id');
            if ($loan_from_id == $loan_to_id) {
                $this->form_validation->set_message('_loan_transfer_to_is_not_same_as_loan_from', 'Loan from can not be the same as Loan to');
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return TRUE;
        }
    }

    function _valid_member_ids()
    {
        $member_ids = $this->input->post('member_ids');
        if ($member_ids) {
            return TRUE;
        } else {
            $this->form_validation->set_message('_valid_member_ids', 'Select atleast 1 member');
            return FALSE;
        }
    }

    function _member_to_id_is_not_same_as_member_id()
    {
        if ($this->input->post('transfer_to') == 4) {
            $member_to_id = $this->input->post('member_to_id');
            $member_id = $this->input->post('member_id');
            if ($member_id == $member_to_id) {
                $this->form_validation->set_message('_member_to_id_is_not_same_as_member_id', 'Select another member to transfer to, you cannot transfer to the same member');
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return TRUE;
        }
    }

    function _valid_date()
    {
        $date = $this->input->post('transfer_date');
        if (valid_date($date)) {
            return TRUE;
        } else {
            $this->form_validation->set_message('_valid_date', 'Kindly use a valid date');
            return FALSE;
        }
    }

    function _valid_currency()
    {
        $amount = $this->input->post('amount');
        if (currency($amount)) {
            return TRUE;
        } else {
            $this->form_validation->set_message('_valid_currency', 'Kindly enter valid amount');
            return FALSE;
        }
    }

    function record_contribution_transfers()
    {
        $data = array();
        $posts = $_POST;
        $errors = array();
        $response = array();
        $successes = array();
        $validation_rules = array(
            array(
                'field' =>  'transfer_date',
                'label' =>  'Transfer Date',
                'rules' =>  'trim|required|callback__valid_date',
            ), array(
                'field' =>  'transfer_for',
                'label' =>  'Transfer For',
                'rules' =>  'trim|required',
            ), array(
                'field' =>  'contribution_from_id',
                'label' =>  'Contribution from',
                'rules' =>  'trim|required|callback__is_contribution_from_id_does_not_match_contribution_to_id',
            ), array(
                'field' =>  'transfer_to',
                'label' =>  'Transfer to',
                'rules' =>  'trim|numeric|required',
            ), array(
                'field' =>  'contribution_to_id',
                'label' =>  'Contribution to',
                'rules' =>  'trim|numeric',
            ), array(
                'field' =>  'fine_category_to_id',
                'label' =>  'Fine Category to',
                'rules' =>  'trim',
            ), array(
                'field' =>  'amount',
                'label' =>  'Amount',
                'rules' =>  'trim|required|currency',
            ), array(
                'field' =>  'description',
                'label' =>  'Description',
                'rules' =>  'trim',
            ),
            array(
                'field' =>  'loan_from_id',
                'label' =>  'Loan from',
                'rules' =>  'trim',
            ),
            array(
                'field' =>  'loan_to_id',
                'label' =>  'Loan to',
                'rules' =>  'trim',
            ),
            array(
                'field' =>  'member_to_id',
                'label' =>  'Recipient Member',
                'rules' =>  'trim',
            ),
            array(
                'field' =>  'member_transfer_to',
                'label' =>  'Member transfer to',
                'rules' =>  'trim',
            ),
            array(
                'field' =>  'member_contribution_to_id',
                'label' =>  'Contribution to',
                'rules' =>  'trim',
            ),
            array(
                'field' =>  'member_fine_category_to_id',
                'label' =>  'Fine category to',
                'rules' =>  'trim',
            ),
            array(
                'field' =>  'member_loan_to_id',
                'label' =>  'Member loan to',
                'rules' =>  'trim',
            ),
        );
        if ($this->input->post('transfer_for') == 1) {
            $validation_rules[] = array(
                'field' => 'member_ids',
                'label' => 'Loan from',
                'rules' => 'callback__valid_member_ids',
            );
        }
        if ($this->input->post('contribution_from_id') == 'loan') {
            $validation_rules[] = array(
                'field' => 'loan_from_id',
                'label' => 'Loan from',
                'rules' => 'trim|numeric|required|callback__loan_transfer_from_has_payment',
            );
        }
        if ($this->input->post('transfer_to') == 1) {
            $validation_rules[] = array(
                'field' => 'contribution_to_id',
                'label' => 'Contribution to',
                'rules' => 'trim|numeric|required',
            );
        } else if ($this->input->post('transfer_to') == 2) {
            $validation_rules[] = array(
                'field' => 'fine_category_to_id',
                'label' => 'Fine Category to',
                'rules' => 'trim|required',
            );
        } else if ($this->input->post('transfer_to') == 3) {
            $validation_rules[] = array(
                'field' => 'loan_to_id',
                'label' => 'Loan Share to',
                'rules' => 'trim|required|numeric|callback__loan_transfer_to_is_not_same_as_loan_from',
            );
        } else if ($this->input->post('transfer_to') == 4) {

            $validation_rules[] = array(
                'field' => 'member_to_id',
                'label' => 'Member to Receive Transfer',
                'rules' => 'trim|required|numeric|callback__member_to_id_is_not_same_as_member_id',
            );
            $validation_rules[] = array(
                'field' => 'member_transfer_to',
                'label' => 'Select Transfer To',
                'rules' => 'trim|required|numeric',
            );
        }
        if ($this->input->post('member_transfer_to') == 1) {
            $validation_rules[] = array(
                'field' => 'member_contribution_to_id',
                'label' => 'Member Contribution To',
                'rules' => 'trim|required|numeric',
            );
        } else if ($this->input->post('member_transfer_to') == 2) {
            $validation_rules[] = array(
                'field' => 'member_fine_category_to_id',
                'label' => 'Member Fine Category To',
                'rules' => 'trim|required',
            );
        } else if ($this->input->post('member_transfer_to') == 3) {
            $validation_rules[] = array(
                'field' => 'member_loan_to_id',
                'label' => 'Member Loan To',
                'rules' => 'trim|required|numeric',
            );
        }
        $this->form_validation->set_rules($validation_rules);
        if ($this->form_validation->run()) {
            $transfer_date = $this->input->post('transfer_date');
            $contribution_from_id = $this->input->post('contribution_from_id');
            $transfer_to = $this->input->post('transfer_to');
            $contribution_to_id = $this->input->post('contribution_to_id');
            $fine_category_to_id = $this->input->post('fine_category_to_id');
            //$member_id = $this->input->post('member_id');
            $amount = $this->input->post('amount');
            $description = $this->input->post('description');
            $loan_from_id = $this->input->post('loan_from_id');
            $loan_to_id = $this->input->post('loan_to_id');
            $member_to_id = $this->input->post('member_to_id');
            $member_transfer_to = $this->input->post('member_transfer_to');
            $member_contribution_to_id = $this->input->post('member_contribution_to_id');
            $member_fine_category_to_id = $this->input->post('member_fine_category_to_id');
            $member_loan_to_id = $this->input->post('member_loan_to_id');
            $transfer_for = $this->input->post('transfer_for');
            if ($transfer_for == 1) {
                $members = $this->input->post('member_ids');
                foreach ($members as $member_id) {
                    $member_ids[$member_id] = 'Name';
                }
            } else {
                $member_ids = $this->active_group_member_options;
            }
            $member_ids_array = array();
            foreach ($member_ids as $member_id => $member_name) {
               
                if ($this->transactions->record_contribution_transfer(
                    $this->group->id,
                    $transfer_date,
                    $contribution_from_id,
                    $transfer_to,
                    $contribution_to_id,
                    $fine_category_to_id,
                    $member_id,
                    $amount,
                    $description,
                    $loan_from_id,
                    $loan_to_id,
                    $member_to_id,
                    $member_transfer_to,
                    $member_contribution_to_id,
                    $member_fine_category_to_id,
                    $member_loan_to_id
                )) {
                    $group_ids = array(
                        $this->group->id
                    );
                    $member_to_id = $member_to_id ?: 0;
                    $member_ids_array = array(
                        $member_id,
                        $member_to_id
                    );
                } else {
                    $response = array(
                        'status' => 0,
                        'message' => 'Contribution transfer not recorded successfully',
                    );
                }
            }

            if ($this->transactions->update_group_member_contribution_statement_balances($group_ids, $member_ids_array)) {
            } else {
                $response = array(
                    'status' => 0,
                    'message' => 'Could not reconcile contrbution statement',
                );
            }
            $response = array(
                'status' => 1,
                'message' => 'Contribution transfer recorded successfully',
                'refer' => site_url('group/deposits/contribution_transfers'),
            );
        } else {
            $post = array();
            $form_errors = $this->form_validation->error_array();
            foreach ($form_errors as $key => $value) {
                $post[$key] = $value;
            }
            $response = array(
                'status' => 0,
                'message' => 'There are some errors on the form. Please review and try again.',
                'validation_errors' => $post,
            );
        }
        echo json_encode($response);
    }

    function upload_contribution_payments()
    {
        set_time_limit(0);
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);
        $response = array();
        $validation_rules = array(
            array(
                'field' =>  'account_id',
                'label' =>  'Account',
                'rules' =>  'trim|required',
            ),
        );
        $this->form_validation->set_rules($validation_rules);
        if ($this->form_validation->run()) {
            $directory = './uploads/files/csvs';
            if (!is_dir($directory)) {
                mkdir($directory, 0777, TRUE);
            }
            $config['upload_path'] = FCPATH . 'uploads/files/csvs/';
            $config['allowed_types'] = 'xls|xlsx|csv';
            $config['max_size'] = '1024';
            $account_id = $this->input->post('account_id');
            $send_sms_notification = $this->input->post('send_sms_notification');
            $send_email_notification = $this->input->post('send_email_notification');
            $deposit_method = 1;
            $description = $this->input->post('description');
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('contribution_imports')) {
                $successful_invitations_count = 0;
                $unsuccessful_invitations_count = 0;
                $upload_data = $this->upload->data();
                $file_path = $upload_data['full_path'];

                $spreadsheet = new Spreadsheet();
                //$excel_sheet = new PHPExcel();
                if (file_exists($file_path)) {
                    $file_type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($file_path);
                    $excel_reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($file_type);
                    $excel_book = $excel_reader->load($file_path);
                    $sheet = $excel_book->getSheet(0);
                    //print_r($sheet); die();
                    $contribution_options = $this->contributions_m->get_active_group_contribution_options();
                    $allowed_column_headers = array_merge(array('', 'Member Name', 'Membership Number', 'Member Email', 'Deposit Date'), $contribution_options);
                    $contribution_ids = array();
                    $count = count($allowed_column_headers);
                    for ($column = 0; $column <= $count; $column++) {
                        $value = $sheet->getCellByColumnAndRow($column, 2)->getValue();
                        $value = str_replace('Amount (' . $this->group_currency . ')', '', $value);
                        if (in_array(trim($value), $allowed_column_headers)) {
                            $column_validation = true;
                            if (in_array(trim($value), $contribution_options)) {
                                $contribution_ids[$column] = array_search(trim($value), $contribution_options);
                            }
                        } else {
                            $column_validation = false;
                            break;
                        }
                    }
                    $member_id = '';
                    $member_name = '';
                    $deposit_date = '';
                    $contribution_payment_amounts = '';
                    $column_filtering = TRUE;
                    $column_validation_error = '';
                    if ($column_validation) {
                        $highestRow = $sheet->getHighestRow();
                        $member_payments = array();
                        $contribution_payment_amounts = array();
                        for ($row = 3; $row <= $highestRow; $row++) {
                            $member_name = '';
                            $member_id = 0;
                            for ($column = 2; $column <= $count; $column++) {
                                if ($column == 2) {
                                    $member_name = $sheet->getCellByColumnAndRow($column, $row)->getValue();
                                    $member_id = array_search($member_name, $this->active_group_member_options);
                                }
                                if ($column == 3) {
                                    $member_number = $sheet->getCellByColumnAndRow($column, $row)->getValue();
                                    if ($member_number) {
                                        if (array_key_exists($member_number, array_flip($this->active_group_membership_number_options))) {
                                        } else {
                                            $column_filtering = FALSE;
                                            $column_validation_error = $member_number . ' &nbsp; Membership number is invalid';
                                            break;
                                        }
                                    }
                                }
                                if ($column == 4) {
                                    $member_email = $sheet->getCellByColumnAndRow($column, $row)->getValue();
                                    if ($member_email) {
                                        if (valid_email($member_email)) {
                                        } else {
                                            $column_filtering = FALSE;
                                            $column_validation_error = $member_email . ' &nbsp; is not a valid email address';
                                            break;
                                        }
                                    }
                                }
                                if ($column == 5) {
                                    if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($sheet->getCellByColumnAndRow($column, $row))) {
                                        $deposit_date = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($sheet->getCellByColumnAndRow($column, $row)->getValue());
                                    } else {
                                        $deposit_date = strtotime($sheet->getCellByColumnAndRow($column, $row)->getValue());
                                    }
                                }
                                foreach ($contribution_ids as $column_key => $contribution_id) {
                                    $contribution_payment_amounts[$contribution_id] = $sheet->getCellByColumnAndRow($column_key, $row)->getValue();
                                }
                            }
                            $member_payments[] = array(
                                'member_id' => $member_id,
                                'member_name' => $member_name,
                                'deposit_date' => $deposit_date,
                                'payment_amounts' => $contribution_payment_amounts,
                                'total_member_payments' => array_sum($contribution_payment_amounts),
                            );
                        }
                        //print_r($member_payments); die();
                        if ($column_filtering) {
                            if (empty($member_payments)) {
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Error in the import list file',
                                );
                            } else {
                                $successes = 0;
                                $fails = 0;
                                $duplicates = 0;
                                $ignores = 0;
                                $errors = 0;
                                $phones = array();
                                $emails = array();
                                $row = 2;
                                $contribution_payments = array();
                                foreach ($member_payments as $member_payment) {
                                    $member_payment = (object)$member_payment;
                                    $member_id = $member_payment->member_id;
                                    $deposit_date = $member_payment->deposit_date;
                                    // $deposit_date = strtotime($member_payment->deposit_date);
                                    if ($member_id) {
                                        foreach ($member_payment->payment_amounts as $contribution_id => $amount) {
                                            if (currency($amount) > 0) {

                                                if (number_to_currency($amount) && $contribution_id) {
                                                    $successes++;
                                                    $contribution_payment = new stdClass();
                                                    $contribution_payment->deposit_date = $member_payment->deposit_date;
                                                    $contribution_payment->member_id = $member_payment->member_id;
                                                    $contribution_payment->contribution_id = $contribution_id;
                                                    $contribution_payment->account_id = $account_id;
                                                    $contribution_payment->amount = $amount;
                                                    $contribution_payment->deposit_method = $deposit_method;
                                                    $contribution_payment->send_sms_notification = $send_sms_notification;
                                                    $contribution_payment->send_email_notification = $send_email_notification;
                                                    $contribution_payment->description = $description;

                                                    $contribution_payments[] = $contribution_payment;

                                                    //add more like account_id,#deposit_date etc
                                                    // if($this->transactions->record_contribution_payment($this->group->id,
                                                    //     $deposit_date,
                                                    //     $member_id,
                                                    //     $contribution_id,
                                                    //     $account_id,
                                                    //     $deposit_method,
                                                    //     $description,
                                                    //     $amount,
                                                    //     $send_sms_notification,
                                                    //     $send_email_notification)){
                                                    //     ++$successes;
                                                    // }else{
                                                    //     ++$fails;
                                                    // }

                                                }
                                            }
                                        }
                                    } else {
                                        ++$errors;
                                    }
                                }
                                // print_r($contribution_payments); die;
                                if ($this->transactions->record_group_contribution_payments($this->group->id, $contribution_payments)) {
                                    $response = array(
                                        'status' => 1,
                                        'message' => $successes . ' Contributions recorded successfully',
                                        'refer' => site_url('group/deposits/listing')
                                    );
                                } else {
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Something went wrong while recording the contribution payments',
                                    );
                                }
                            }
                        } else {
                            $response = array(
                                'status' => 0,
                                'message' => 'Error:&nbsp;' . $column_validation_error,
                            );
                        }
                    } else {
                        $response = array(
                            'status' => 0,
                            'message' => 'Contibution Payment list file does not have the correct format',
                        );
                    }
                } else {
                    $response = array(
                        'status' => 0,
                        'message' => 'Contibution Payment list file was not found',
                    );
                }
            } else {
                $response = array(
                    'status' => 0,
                    'message' => 'File upload error: ' . $this->upload->display_errors(),
                );
            }
        } else {
            $response = array(
                'status' => 0,
                'message' => 'There are some errors on the form. Please review and try again.',
                'validation_errors' => $this->form_validation->error_array(),
            );
        }
        echo json_encode($response);
    }
}
