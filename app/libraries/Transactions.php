<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Transactions{

    protected $ci;
    public  $group;

    public $payment_for_options = array(
        1 => 'Contribution Payment',
        2 => 'Fine Payment',
        3 => 'Loan Repayment',
        4 => 'Miscellaneous Payment',

    );

    public $deposit_method_options = array(
        1 => "MPesa",
        2 => "Cash",
        3 => "Cheque",
        4 => "Fund Transfer",
        5 => "Standing Order",
        6 => "TigoPesa", 
        7 => "Airtel Money", 
        8 => "Halopesa"
    );

    public $deposit_for_options = array(
        1 => "Contribution payment",
        2 => "Fine payment",
        3 => "Miscellaneous payment",
        4 => "Income",
        5 => "Loan repayment",
        6 => "Bank loan disbursement",
        7 => "Funds transfer",
        8 => "Stock sale",
        9 => "Asset sale",
        10 => "Money market cash in",
        11 => 'Loan processing income',
        12 => 'External loan repayment',
    );

    public $withdrawal_for_options = array(
        1 => "Expense",
        2 => "Asset Purchase Payment",
        3 => "Loan Disbursement",
        4 => "Stock Purchase",
        5 => "Money Market Investment",
        6 => "Money Market Investment Top Up",
        7 => "Contribution Refund",
        8 => "Bank Loan Repayment",
        9 => "Funds Transfer",
        10 => "External Lending",
        11 => "Dividends",
    );

    public $withdrawal_method_options = array(
        1 => 'Cash',
        2 => 'Mpesa',
        3 => 'Cheque',
        4 => 'Account to Account Transfer',
        5 => "TigoPesa", 
        6 => "Airtel Money", 
        7 => "Halopesa"
    );

    public $deposit_transaction_names = array(
        1 => 'Contribution payment',
        2 => 'Contribution payment',
        3 => 'Contribution payment',
        7 => 'Contribution payment',
        4 => 'Fine payment',
        5 => 'Fine payment',
        6 => 'Fine payment',
        8 => 'Fine payment',
        9 => 'Miscellaneous payment',
        10 => 'Miscellaneous payment',
        11 => 'Miscellaneous payment',
        12 => 'Miscellaneous payment',
        13 => 'Income ',
        14 => 'Income ',
        15 => 'Income ',
        16 => 'Income ',
        17 => 'Loan Repayment ',
        18 => 'Loan Repayment ',
        19 => 'Loan Repayment ',
        20 => 'Loan Repayment ',
        21 => 'Bank Loan Disbursement ',
        22 => 'Bank Loan Disbursement ',
        23 => 'Bank Loan Disbursement ',
        24 => 'Bank Loan Disbursement ',
        25 => 'Stock Sale ',
        26 => 'Stock Sale ',
        27 => 'Stock Sale ',
        28 => 'Stock Sale ',
        29 => 'Money Market Investment Cash in ',
        30 => 'Money Market Investment Cash in ',
        31 => 'Money Market Investment Cash in ',
        32 => 'Money Market Investment Cash in ',
        33 => 'Asset Sale ',
        34 => 'Asset Sale ',
        35 => 'Asset Sale ',
        36 => 'Asset Sale ',
        37 => 'Funds Transfer ',
        38 => 'Funds Transfer ',
        39 => 'Funds Transfer ',
        40 => 'Funds Transfer ',
        41 => 'Loan Processing Income',
        42 => 'Loan Processing Income',
        43 => 'Loan Processing Income',
        44 => 'Loan Processing Income',
        45 => 'External Lending Processing Income',
        46 => 'External Lending Processing Income',
        47 => 'External Lending Processing Income',
        48 => 'External Lending Processing Income',
        49 => 'External Lending Loan Repayment ',
        50 => 'External Lending Loan Repayment ',
        51 => 'External Lending Loan Repayment ',
        52 => 'External Lending Loan Repayment ',
        
    );

    public $deposit_type_options = array(
        "1,2,3,7" => "Contribution Payments",
        "4,5,6,8" => "Fine Payments",
        "9,10,11,12" => "Miscellaneous Payments",
        "13,14,15,16" => "Income Receipts",
        "17,18,19,20" => "Loan Repayments",
        "21,22,23,24" => "Bank Loan Disbursements",
        "25,26,27,28" => "Stocks Sales",
        "29,30,31,32" => "Money Market Investment Cash ins",
        "33,34,35,36" => "Asset Sales",
        "37,38,39,40" => "Funds Transfers",
        "41,42,43,44" => "Loan Processing Income",
        '45,46,47,48' => 'External Lending Processing Income',
        '49,50,51,52' => 'External Lending Loan Repayment ',
    );

    public $deposit_types_status_options = array(
        1 => "1,2,3,7",
        2 => "4,5,6,8" ,
        3 => "9,10,11,12",
        4 => "13,14,15,16",
        5 => "17,18,19,20",
        6 =>"21,22,23,24", 
        7 =>"25,26,27,28",
        8 =>"29,30,31,32",
        9 =>"33,34,35,36",
        10 =>"37,38,39,40", 
        11 =>"41,42,43,44",
        12 =>'45,46,47,48',
        13 =>'49,50,51,52',       
    );

    public $withdrawal_types_status_options = array(
        1 => "1,2,3,4",
        2 => "5,6,7,8",
        3 => "9,10,11,12",
        4 => "13,14,15,16",
        5 => "17,18,19,20",
        6 => "21,22,23,24",
        7 => "25,26,27,28",
        8 => "29,30,31,32",
        9 => "33,34,35,36",       
        10 => "37,38,39,40",       
    );

    public $withdrawal_transaction_names = array(
        1 => 'Expense',
        2 => 'Expense',
        3 => 'Expense',
        4 => 'Expense',
        5 => 'Asset Purchase Payment',
        6 => 'Asset Purchase Payment',
        7 => 'Asset Purchase Payment',
        8 => 'Asset Purchase Payment',
        9 => 'Loan Disbursement',
        10 => 'Loan Disbursement',
        11 => 'Loan Disbursement',
        12 => 'Loan Disbursement',
        13 => 'Stock Purchase',
        14 => 'Stock Purchase',
        15 => 'Stock Purchase',
        16 => 'Stock Purchase',
        17 => 'Money Market Investment',
        18 => 'Money Market Investment',
        19 => 'Money Market Investment',
        20 => 'Money Market Investment',
        21 => 'Contribution Refund',
        22 => 'Contribution Refund',
        23 => 'Contribution Refund',
        24 => 'Contribution Refund',
        25 => 'Bank Loan Repayment',
        26 => 'Bank Loan Repayment',
        27 => 'Bank Loan Repayment',
        28 => 'Bank Loan Repayment',
        29 => 'Funds Transfer',
        30 => 'Funds Transfer',
        31 => 'Funds Transfer',
        32 => 'Funds Transfer',
        33 => 'External Loan Disbursement',
        34 => 'External Loan Disbursement',
        35 => 'External Loan Disbursement',
        36 => 'External Loan Disbursement',
        37 => 'Dividend',
        38 => 'Dividend',
        39 => 'Dividend',
        40 => 'Dividend',
    );

    public $withdrawal_type_options = array(
        "1,2,3,4" => "Expenses",
        "5,6,7,8" => "Asset Purchase Payments",
        "9,10,11,12" => "Loan Disbursements",
        "13,14,15,16" => "Stock Purchases",
        "17,18,19,20" => "Money Market Investments",
        "21,22,23,24" => "Contribution Refunds",
        "25,26,27,28" => "Bank Loan Repayments",
        "29,30,31,32" => "Funds Transfers",
        "33,34,35,36" => "External Loan Disbursement",
        "37,38,39,40" => "Dividend",
    );

    public $statement_transaction_names = array(
        1 => 'Contribution invoice',
        2 => 'Contribution fine invoice',
        3 => 'Fine invoice',
        4 => 'Miscellaneous invoice',
        9 => 'Contribution payment',
        10 => 'Contribution payment',
        11 => 'Contribution payment',
        15 => 'Contribution payment',
        12 => 'Fine payment',
        13 => 'Fine payment',
        14 => 'Fine payment',
        16 => 'Fine payment',
        17 => 'Miscellaneous payment',
        18 => 'Miscellaneous payment',
        19 => 'Miscellaneous payment',
        20 => 'Miscellaneous payment',
        21 => 'Contribution refund',
        22 => 'Contribution refund',
        23 => 'Contribution refund',
        24 => 'Contribution refund',
        25 => 'Contribution transfer from ',
        26 => 'Contribution transfer to',
        27 => 'Contribution transfer to contribution',
        28 => 'Contribution transfer to fine',
        29 => 'Loan payment transfer to contribution',
        30 => 'Contribution transfer to loan',
        31 => 'Loan payment transfer to fine',
    );

    public $transaction_names = array(
        1 => 'Contribution payment',
        2 => 'Contribution payment',
        3 => 'Contribution payment',
        7 => 'Contribution payment',
        4 => 'Fine payment',
        5 => 'Fine payment',
        6 => 'Fine payment',
        8 => 'Fine payment',
        9 => 'Miscellaneous payment',
        10 => 'Miscellaneous payment',
        11 => 'Miscellaneous payment',
        12 => 'Miscellaneous payment',
        13 => 'Income ',
        14 => 'Income ',
        15 => 'Income ',
        16 => 'Income ',
        17 => 'Loan Repayment ',
        18 => 'Loan Repayment ',
        19 => 'Loan Repayment ',
        20 => 'Loan Repayment ',
        21 => 'Bank Loan Disbursement ',
        22 => 'Bank Loan Disbursement ',
        23 => 'Bank Loan Disbursement ',
        24 => 'Bank Loan Disbursement ',
        25 => 'Stock Sale ',
        26 => 'Stock Sale ',
        27 => 'Stock Sale ',
        28 => 'Stock Sale ',
        29 => 'Money Market Investment Cash in ',
        30 => 'Money Market Investment Cash in ',
        31 => 'Money Market Investment Cash in ',
        32 => 'Money Market Investment Cash in ',
        33 => 'Asset Sale ',
        34 => 'Asset Sale ',
        35 => 'Asset Sale ',
        36 => 'Asset Sale ',
        37 => 'Incoming Bank Funds Transfer ',
        38 => 'Incoming Bank Funds Transfer ',
        39 => 'Incoming Bank Funds Transfer ',
        40 => 'Incoming Bank Funds Transfer ',
        41 => 'Expense',
        42 => 'Expense',
        43 => 'Expense',
        44 => 'Expense',
        45 => 'Asset Purchase Payment',
        46 => 'Asset Purchase Payment',
        47 => 'Asset Purchase Payment',
        48 => 'Asset Purchase Payment',
        49 => 'Loan Disbursement',
        50 => 'Loan Disbursement',
        51 => 'Loan Disbursement',
        52 => 'Loan Disbursement',
        53 => 'Stock Purchase',
        54 => 'Stock Purchase',
        55 => 'Stock Purchase',
        56 => 'Stock Purchase',
        57 => 'Money Market Investment',
        58 => 'Money Market Investment',
        59 => 'Money Market Investment',
        60 => 'Money Market Investment',
        61 => 'Contribution Refund',
        62 => 'Contribution Refund',
        63 => 'Contribution Refund',
        64 => 'Contribution Refund',
        65 => 'Bank Loan Repayment',
        66 => 'Bank Loan Repayment',
        67 => 'Bank Loan Repayment',
        68 => 'Bank Loan Repayment',
        69 => 'Funds Transfer',
        70 => 'Funds Transfer',
        71 => 'Funds Transfer',
        72 => 'Funds Transfer',
        73 => 'Loan Processing Income',
        74 => 'Loan Processing Income',
        75 => 'Loan Processing Income',
        76 => 'Loan Processing Income',
        77 => "External Lending",
        78 => "External Lending",
        79 => "External Lending",
        80 => "External Lending",
        81 => "External Lending Processing Income",
        82 => "External Lending Processing Income",
        83 => "External Lending Processing Income",
        84 => "External Lending Processing Income",
        85 => 'External Lending Loan Repayment',
        86 => 'External Lending Loan Repayment',
        87 => 'External Lending Loan Repayment',
        88 => 'External Lending Loan Repayment',
        89 => 'Dividend',
        90 => 'Dividend',
        91 => 'Dividend',
        92 => 'Dividend',
    );

    //Deposits
    public $bank_deposit_transaction_types = array(
        1=>1,
        4=>4,
        9=>9,
        13=>13,
        17=>17,
        21=>21,
        25=>25,
        29=>29,
        33=>33,
        37=>37,
        41=>73,
        45 => 81,
        49 => 85,
    );

    public $sacco_deposit_transaction_types = array(
        2=>2,
        5=>5,
        10=>10,
        14=>14,
        18=>18,
        22=>22,
        26=>26,
        30=>30,
        34=>34,
        38=>38,
        42=>74,
        46 => 82,
        50 => 86,
    );

    public $mobile_deposit_transaction_types = array(
        3=>3,
        6=>6,
        11=>11,
        15=>15,
        19=>19,
        23=>23,
        27=>27,
        31=>31,
        35=>35,
        39=>39,
        43=>75,
        47 => 83,
        51 => 87,
    );

    public $petty_deposit_transaction_types = array(
        7=>7,
        8=>8,
        12=>12,
        16=>16,
        20=>20,
        24=>24,
        28=>28,
        32=>32,
        36=>36,
        40=>40,
        44=>76,
        48 => 84,
        52 => 88,
    );

    public $deposit_transaction_types;

    //Withdrawals
    public $bank_withdrawal_transaction_types = array(
        1=>41,
        5=>45,
        9=>49,
        13=>53,
        17=>57,
        21=>61,
        25=>65,
        29=>69,
        33 => 77,
        37 => 89,
    );

    public $sacco_withdrawal_transaction_types = array(
        2=>42,
        6=>46,
        10=>50,
        14=>54,
        18=>58,
        22=>62,
        26=>66,
        30=>70,
        34 => 78,
        38 => 90,

    );

    public $mobile_withdrawal_transaction_types = array(
        3=>43,
        7=>47,
        11=>51,
        15=>55,
        19=>59,
        23=>63,
        27=>67,
        31=>71,
        35 => 79,
        39 => 91,

    );

    public $petty_withdrawal_transaction_types = array(
        4=>44,
        8=>48,
        12=>52,
        16=>56,
        20=>60,
        24=>64,
        28=>68,
        32=>72,
        36 => 80,
        40 => 92,

    );

    public $withdrawal_transaction_types;

    public $contribution_payment_transaction_types = array(
        1 ,
        2 ,
        3 ,
        7 ,
    );

    public $fine_payment_transaction_types = array(
        4 ,
        5 ,
        6 ,
        8 ,
    );

    public $miscellaneous_payment_transaction_types = array(
        9 ,
        10 ,
        11 ,
        12 ,
    );

    public $income_deposit_transaction_types = array(
        13 ,
        14 ,
        15 ,
        16 ,
    );

    public $loan_repayment_transaction_types = array(
        17 ,
        18 ,
        19 ,
        20 ,
    );

    public $bank_loan_disbursement_deposit_transaction_types = array(
        21 ,
        22 ,
        23 ,
        24 ,
    );

    public $stock_sale_deposit_transaction_types = array(
        25 ,
        26 ,
        27 ,
        28 ,
    );

    public $money_market_investment_cash_in_deposit_transaction_types = array(
        29 ,
        30 ,
        31 ,
        32 ,
    );

    public $asset_sale_deposit_transaction_types = array(
        33 ,
        34 ,
        35 ,
        36 ,
    );

    public $incoming_account_transfer_withdrawal_transaction_types = array(
        37,38,39,40
    );

    public $loan_processing_income_deposit_transaction_types = array(
        41,
        42,
        43,
        44
    );

    public $external_lending_processing_income_deposit_transaction_types = array(
        45,
        46,
        47,
        48
    );

    public $external_lending_loan_repayment_deposit_transaction_types = array(
       49,
       50,
       51,
       52,
    );


    /*****Withdrawals***/

    public $expense_withdrawal_transaction_types = array(
        1,2,3,4
    );

    public $statement_expense_withdrawal_transaction_types = array(
        41,42,43,44
    );

    public $stock_purchase_withdrawal_transaction_types = array(
        13,14,15,16
    );

    public $statement_stock_purchase_withdrawal_transaction_types = array(
        53,54,55,56
    );

    public $money_market_investment_withdrawal_transaction_types = array(
        17,18,19,20
    );

    public $statement_money_market_investment_withdrawal_transaction_types = array(
        57,58,59,60
    );

    public $asset_purchase_withdrawal_transaction_types = array(
        5,6,7,8
    );

    public $statement_asset_purchase_withdrawal_transaction_types = array(
        45,46,47,48
    );

    public $contribution_refund_withdrawal_transaction_types = array(
        21,22,23,24
    );

    public $statement_contribution_refund_withdrawal_transaction_types = array(
        61,62,63,64
    );

    public $bank_loan_repayment_withdrawal_transaction_types = array(
        25,26,27,28
    );

    public $statement_bank_loan_repayment_withdrawal_transaction_types = array(
        45,46,47,48
    );

    public $loan_disbursement_withdrawal_transaction_types = array(
        9,10,11,12
    );

    public $statement_loan_disbursement_withdrawal_transaction_types = array(
        49,50,51,52
    );

    public $outgoing_account_transfer_withdrawal_transaction_types = array(
        29,30,31,32
    );

    public $statement_outgoing_account_transfer_withdrawal_transaction_types = array(
        69,70,71,72
    );

     public $statement_loan_processing_income_deposit_transaction_types = array(
        73,74,75,76
    );

    public $external_lending_withdrawal_transaction_types = array(
        33,34,35,36
    );

    public $statement_external_lending_withdrawal_transaction_types = array(
        77,78,79,80
    );

    public $statement_external_lending_processing_income_transaction_types = array(
        81,82,83,84
    );

    public $statement_external_lending_loan_repayment_transaction_types = array(
        85,86,87,88
    );

    public $statement_dividend_transaction_types = array(
        89,90,91,92
    );

    public $dividend_withdrawal_transaction_types = array(
        37,38,39,40
    );

    public $currency_code_options = array();

    public $application_settings;

    public $withdrawal_request_transaction_names = array(
        1 => 'Loan Disbursement',
        2 => 'Expense Payment',
        3 => 'Dividend Payout',
        4 => 'Welfare',
        5 => 'Shares Refund',
        6 => 'Account Transfer'
    );

    public $recipient_options = array(
        1 => 'Mobile Wallet',
        // 2 => 'Paybill',
        3 => 'Bank Account',
    );

    public $disbursement_channel_options = array(
        1 => 'Bank Disbursement',
        2 => 'Cash Payment',
    );

    public $payable_transaction_types_array = array(1,21,22,23,24,25,27,30);

    public $fine_payable_transaction_types_array = array(2,3);

    public $payable_transaction_types_minus_contribution_transfers_array = array(1,21,22,23,24,27,30);

    public $paid_transaction_types_array = array(9,10,11,15,26);

    public $fine_paid_transaction_types_array = array(12,13,14,16,28);

    public $paids_transaction_types_minus_contribution_transfers_array = array(9,10,11,15);

    public $paid_deductable_transaction_types_array = array(21,22,23,24,25,27,30);

    public function __construct(){
        $this->ci= & get_instance();
        set_time_limit(0);
        ini_set('memory_limit','2048M');
        ini_set('max_execution_time', 1200);
        $this->ci->load->model('members/members_m');
        $this->ci->load->model('reports/reports_m');
        $this->ci->load->model('invoices/invoices_m');
        $this->ci->load->model('groups/groups_m');
        $this->ci->load->model('countries/countries_m');
        $this->ci->load->model('settings/settings_m');
        $this->ci->load->model('deposits/deposits_m');
        $this->ci->load->model('withdrawals/withdrawals_m');
        $this->ci->load->model('statements/statements_m');
        $this->ci->load->model('fine_categories/fine_categories_m');
        $this->ci->load->model('fines/fines_m');
        $this->ci->load->model('depositors/depositors_m');
        $this->ci->load->model('money_market_investments/money_market_investments_m');
        $this->ci->load->model('stocks/stocks_m');
        $this->ci->load->model('accounts/accounts_m');
        $this->ci->load->model('transaction_statements/transaction_statements_m');
        $this->ci->load->model('sacco_accounts/sacco_accounts_m');
        $this->ci->load->model('bank_accounts/bank_accounts_m');
        $this->ci->load->model('mobile_money_accounts/mobile_money_accounts_m');
        $this->ci->load->model('petty_cash_accounts/petty_cash_accounts_m');
        $this->ci->load->model('bank_loans/bank_loans_m');
        $this->ci->load->model('contribution_refunds/contribution_refunds_m');
        $this->ci->load->model('transaction_alerts/transaction_alerts_m');
        $this->ci->load->model('expense_categories/expense_categories_m');
        $this->ci->load->model('recipients/recipients_m');
        $this->ci->load->model('banks/banks_m');
        $this->ci->load->library('loan');
        $this->ci->load->library('curl');
        $this->ci->load->library('process_transactions');
        $this->deposit_transaction_types = $this->bank_deposit_transaction_types + $this->sacco_deposit_transaction_types + $this->mobile_deposit_transaction_types + $this->petty_deposit_transaction_types;
        $this->withdrawal_transaction_types = $this->bank_withdrawal_transaction_types + $this->sacco_withdrawal_transaction_types + $this->mobile_withdrawal_transaction_types + $this->petty_withdrawal_transaction_types;
        $this->currency_code_options = $this->ci->countries_m->get_currency_code_options();
        $this->application_settings = $this->ci->settings_m->get_settings()?:'';
        if(preg_match('/(eazzyclub\.co\.ug)/',$_SERVER['HTTP_HOST'])){
            $this->deposit_method_options = array(
                1 => "Airtel Money",
                2 => "Cash",
                3 => "Cheque",
                4 => "Fund Transfer",
                5 => "MTN Money",
            );
        }
    }

    // public function reconcile_member_statement($transaction_type = 0,$group_id = 0,$member_id = 0,$contribution_id = 0,$date = 0){
    //     if($transaction_type&&$group_id&&$member_id&&$contribution_id){
    //         $result = TRUE;
    //         //$transaction_type==1||$transaction_type==21||$transaction_type==22||$transaction_type==23||$transaction_type==24
    //         if(in_array($transaction_type,$this->payable_transaction_types_array)){
    //             $group_ids = array($group_id);
    //             $member_ids = array($member_id);
    //             return $this->update_group_member_contribution_statement_balances($group_ids,$member_ids);
    //         }else if(in_array($transaction_type,$this->paid_transaction_types_array)){
    //             $contribution_fine_invoices = $this->ci->invoices_m->get_member_contribution_fine_invoices_to_revise($group_id,$member_id,$contribution_id);
    //             if($this->_revise_contribution_fine_invoices($contribution_fine_invoices)){
    //                 $group_ids[] = $group_id; 
    //                 $member_ids[] = $member_id; 
    //                 return $this->update_group_member_contribution_statement_balances($group_ids,$member_ids);
    //             }else{
    //                 return FALSE;
    //             }
                
    //         }   
    //     }else{
    //         $this->ci->session->set_flashdata('error','Parameters missing for the function reconcile_member_statement');
    //         return FALSE;
    //     } 
    // }

    public function reconcile_member_statement($transaction_type = 0,$group_id = 0,$member_id = 0,$contribution_id = 0,$date = 0){
        if($transaction_type&&$group_id&&$member_id&&$contribution_id){
            $result = TRUE;
            //$transaction_type==1||$transaction_type==21||$transaction_type==22||$transaction_type==23||$transaction_type==24
            if(in_array($transaction_type,$this->payable_transaction_types_array)){
                $group_ids = array($group_id);
                $member_ids = array($member_id);
                return $this->update_group_member_contribution_statement_balances($group_ids,$member_ids,$date);
            }else if(in_array($transaction_type,$this->paid_transaction_types_array)){
                $contribution_fine_invoices = $this->ci->invoices_m->get_member_contribution_fine_invoices_to_revise($group_id,$member_id,$contribution_id);
                if($this->_revise_contribution_fine_invoices($contribution_fine_invoices)){
                    $group_ids[] = $group_id; 
                    $member_ids[] = $member_id; 
                    return $this->update_group_member_contribution_statement_balances($group_ids,$member_ids,$date);
                }else{
                    return FALSE;
                }
                
            }   
        }else{
            $this->ci->session->set_flashdata('error','Parameters missing for the function reconcile_member_statement');
            return FALSE;
        } 
    }

    function revise_group_contribution_fine_invoices($group_ids = array(),$member_ids = array()){
        $contribution_fine_invoices = $this->ci->invoices_m->get_group_member_contribution_fine_invoices_to_revise($group_ids,$member_ids);
    }

    // function update_group_member_contribution_statement_balances($group_ids = array(),$member_ids = array()){
    //     $statement_entries = $this->ci->statements_m->get_group_member_contribution_statements($group_ids,$member_ids);
    //     $member_contribution_balances_array = array();
    //     $member_contribution_balances_minus_contribution_transfers_array = array();
    //     $member_contribution_paid_array = array();
    //     $member_cumulative_balances_array = array();
    //     $member_cumulative_balances_minus_contribution_transfers_array = array();
    //     $member_cumulative_paid_array = array();
    //     if($statement_entries){
    //         $statement_entries_array = array();
    //         $contribution_ids = array();
    //         $statement_ids = array();
    //         foreach($statement_entries as $statement_entry):
    //             $member_contribution_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] = 0;
    //             $member_cumulative_balances_array[$statement_entry->group_id][$statement_entry->member_id] = 0;

    //             $member_contribution_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] = 0;
    //             $member_cumulative_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id] = 0;

    //             $member_contribution_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] = 0;
    //             $member_cumulative_paid_array[$statement_entry->group_id][$statement_entry->member_id] = 0;
    //             if(in_array($statement_entry->contribution_id,$contribution_ids)){

    //             }else{
    //                 $contribution_ids[] = $statement_entry->contribution_id;
    //             }
    //             $statement_ids[] = $statement_entry->id;
    //         endforeach;
    //         $contribution_objects_array = $this->ci->contributions_m->get_group_contribution_objects_array($group_ids,$contribution_ids);
    //         foreach($statement_entries as $statement_entry):
    //             $cumulative_balance = 0;
    //             $contribution_balance = 0;
    //             $cumulative_minus_contribution_transfers_balance = 0;
    //             $contribution_minus_contribution_transfers_balance = 0;
    //             $cumulative_paid = 0;
    //             $contribution_paid = 0;
    //             $contribution = $contribution_objects_array[$statement_entry->contribution_id];
    //             $contribution_from = isset($contribution_objects_array[$statement_entry->contribution_from_id])?$contribution_objects_array[$statement_entry->contribution_from_id]:"";
    //             if(in_array($statement_entry->transaction_type,$this->payable_transaction_types_array)){
    //                 if(valid_currency($statement_entry->amount)){

    //                     if($contribution->display_contribution_arrears_cumulatively){
    //                         if($contribution_from){
    //                             if($statement_entry->contribution_to_id){
    //                                 $member_contribution_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_to_id] += currency($statement_entry->amount);
    //                                 $member_cumulative_balances_array[$statement_entry->group_id][$statement_entry->member_id] += currency($statement_entry->amount);
    //                             }
    //                         }
    //                     }else{                            
    //                         $member_contribution_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] += currency($statement_entry->amount);
    //                         $member_cumulative_balances_array[$statement_entry->group_id][$statement_entry->member_id] += currency($statement_entry->amount);
                            
    //                         if(in_array($statement_entry->transaction_type,$this->payable_transaction_types_minus_contribution_transfers_array)){
    //                             $member_contribution_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] += currency($statement_entry->amount);
    //                             $member_cumulative_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id] += currency($statement_entry->amount);
    //                         }
    //                     }
                    
    //                     $contribution_balance = $member_contribution_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id];
    //                     $cumulative_balance = $member_cumulative_balances_array[$statement_entry->group_id][$statement_entry->member_id]; 

    //                     $contribution_minus_contribution_transfers_balance = $member_contribution_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id];
    //                     $cumulative_minus_contribution_transfers_balance = $member_cumulative_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id]; 

    //                     if(in_array($statement_entry->transaction_type,$this->paid_deductable_transaction_types_array)){

    //                         $member_contribution_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] -= currency($statement_entry->amount);
    //                         $member_cumulative_paid_array[$statement_entry->group_id][$statement_entry->member_id] -= currency($statement_entry->amount);
    //                     }

    //                     $contribution_paid = $member_contribution_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id];
    //                     $cumulative_paid = $member_cumulative_paid_array[$statement_entry->group_id][$statement_entry->member_id]; 


    //                 }
    //             }
    //             if(in_array($statement_entry->transaction_type,$this->paid_transaction_types_array)){
    //                 if(valid_currency($statement_entry->amount)){
    //                     if($contribution->display_contribution_arrears_cumulatively){

    //                     }else{
    //                         $member_contribution_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] -= currency($statement_entry->amount);
    //                         $member_cumulative_balances_array[$statement_entry->group_id][$statement_entry->member_id] -= currency($statement_entry->amount);

    //                         if(in_array($statement_entry->transaction_type,$this->payable_transaction_types_minus_contribution_transfers_array)){
    //                             $member_contribution_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] -= currency($statement_entry->amount);
    //                             $member_cumulative_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id] -= currency($statement_entry->amount);
    //                         }
    //                     }

    //                     $member_contribution_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] += currency($statement_entry->amount);
    //                     $member_cumulative_paid_array[$statement_entry->group_id][$statement_entry->member_id] += currency($statement_entry->amount);

    //                     $contribution_balance = $member_contribution_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id];
    //                     $cumulative_balance = $member_cumulative_balances_array[$statement_entry->group_id][$statement_entry->member_id];  

    //                     $contribution_minus_contribution_transfers_balance = $member_contribution_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id];
    //                     $cumulative_minus_contribution_transfers_balance = $member_cumulative_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id]; 



    //                     $contribution_paid = $member_contribution_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id];
    //                     $cumulative_paid = $member_cumulative_paid_array[$statement_entry->group_id][$statement_entry->member_id]; 
    //                     //echo $cumulative_paid."<br/>";
    //                 }
    //             }
    //             //echo $statement_entry->amount." | ".$cumulative_paid."<br/>";
    //             $statement_entries_array[] = array(
    //                 'transaction_type' => $statement_entry->transaction_type,
    //                 'transaction_date' => $statement_entry->transaction_date,
    //                 'contribution_id' => $statement_entry->contribution_id,
    //                 'refund_id' => $statement_entry->refund_id,
    //                 'fine_id' => $statement_entry->fine_id,
    //                 'user_id' => $statement_entry->user_id,
    //                 'member_id' => $statement_entry->member_id,
    //                 'group_id' => $statement_entry->group_id,
    //                 'invoice_id' => $statement_entry->invoice_id,
    //                 'amount' => $statement_entry->amount,
    //                 'contribution_balance' => $contribution_balance,
    //                 'balance' => $cumulative_balance,
    //                 'contribution_paid' => $contribution_paid,
    //                 'cumulative_paid' => $cumulative_paid,
    //                 'contribution_minus_contribution_transfers_balance' => $contribution_minus_contribution_transfers_balance,
    //                 'cumulative_minus_contribution_transfers_balance' => $cumulative_minus_contribution_transfers_balance,
    //                 'active' => $statement_entry->active,
    //                 'created_by' => $statement_entry->created_by,
    //                 'created_on' => $statement_entry->created_on,
    //                 'modified_on' => $statement_entry->modified_on,
    //                 'modified_by' => $statement_entry->modified_by,
    //                 'account_id' => $statement_entry->account_id,
    //                 'checkoff_id' => $statement_entry->checkoff_id,
    //                 'description' => $statement_entry->description,
    //                 'deposit_id' => $statement_entry->deposit_id,
    //                 'transfer_to' => $statement_entry->transfer_to,
    //                 'contribution_transfer_id' => $statement_entry->contribution_transfer_id,
    //                 'contribution_from_id' => $statement_entry->contribution_from_id,
    //                 'contribution_to_id' => $statement_entry->contribution_to_id,
    //                 'fine_category_to_id' => $statement_entry->fine_category_to_id,
    //                 'contribution_invoice_due_date' => $statement_entry->contribution_invoice_due_date,
    //                 'fine_invoice_due_date' => $statement_entry->fine_invoice_due_date,
    //                 'loan_transfer_invoice_id' => $statement_entry->loan_transfer_invoice_id,
    //                 'loan_from_id' => $statement_entry->loan_from_id,
    //                 'loan_to_id' => $statement_entry->loan_to_id,
    //                 'is_a_back_dating_record' => $statement_entry->is_a_back_dating_record,
    //                 'old_statement_id'=> $statement_entry->id,
    //                 'member_to_id' => $statement_entry->member_to_id,
    //                 'member_from_id' => $statement_entry->member_from_id,
                    
    //             );
    //             //echo timestamp_to_date($statement_entry->transaction_date)."|".number_to_currency($cumulative_balance)."<br/>";
    //         endforeach;   
                 
    //         if(empty($statement_entries_array)){
    //             return FALSE;
    //         }else{
    //             //if($this->ci->statements_m->void_group_member_contribution_statements($group_ids,$member_ids)){
    //             //if($statement_ids_array = $this->ci->statements_m->get_group_member_contribution_statement_ids_array($group_ids,$member_ids)){
    //                 if($statement_insert_result = $this->ci->statements_m->insert_statements_batch($statement_entries_array)){
    //                     if($statement_ids){
    //                         if($this->ci->statements_m->void_contribution_statements_by_ids_array($statement_ids)){
    //                             //get statements with olds ids 
    //                             //get loan repayments with olds ids and create afresh loan repayments with new ids
    //                             $old_member_statements_array = array();
    //                             $new_statements = $this->ci->statements_m->get_group_member_new_contribution_statements_array($statement_ids);
    //                             if($new_statements){

    //                                 foreach ($new_statements as $key => $new_statement_entry):
    //                                     $old_member_statements_array[$new_statement_entry->old_statement_id] = $new_statement_entry->id;
    //                                 endforeach;

    //                                 if(empty($old_member_statements_array)){
    //                                     return FALSE;
    //                                 }else{
    //                                     $loan_repayments  = $this->ci->loan_repayments_m->get_group_member_loan_repayments_array($statement_ids); 
    //                                     if($loan_repayments){
    //                                         $loan_repayment_ids = array();
    //                                         $loan_repayment_input = array();
    //                                         foreach ($loan_repayments as $key => $loan_repayment_entry):
    //                                             $loan_repayment_ids[] = $loan_repayment_entry->id;
    //                                             $loan_repayment_input[] = array(
    //                                                 'loan_id'   =>  $loan_repayment_entry->loan_id,
    //                                                 'group_id'  =>  $loan_repayment_entry->group_id,
    //                                                 'member_id' =>  $loan_repayment_entry->member_id,
    //                                                 'receipt_date'=>$loan_repayment_entry->receipt_date,
    //                                                 'amount'    =>  $loan_repayment_entry->amount,
    //                                                 'status'    =>1,
    //                                                 'active'    =>1,
    //                                                 'created_on'=>  time(),
    //                                                 'transfer_from' => $loan_repayment_entry->transfer_from,
    //                                                 'incoming_loan_transfer_invoice_id' => '',
    //                                                 'incoming_contribution_transfer_id' => $old_member_statements_array[$loan_repayment_entry->incoming_contribution_transfer_id],
    //                                                 'old_incoming_contribution_transfer_id'=> $loan_repayment_entry->incoming_contribution_transfer_id,
    //                                                 'old_loan_repayment_id'=>$loan_repayment_entry->id,
    //                                             );
    //                                         endforeach;
    //                                         if(empty($loan_repayment_ids)){
    //                                             return FALSE;
    //                                         }else{
    //                                             if($this->ci->loan_repayments_m->void_group_member_loan_repayments($loan_repayment_ids)){
    //                                                 if($loan_repayment_insert_result = $this->ci->loan_repayments_m->insert_loan_repayments_batch($loan_repayment_input)){
    //                                                     //get loan statements and update payment id 
    //                                                     if(empty($loan_repayment_ids)){

    //                                                     }else{
    //                                                        $old_loan_payment_statements = $this->ci->loans_m->get_old_loan_repayment_statements_array($loan_repayment_ids);
    //                                                        if($old_loan_payment_statements){
    //                                                            $old_member_repayment_array =  array();
    //                                                             $new_loan_repayments = $this->ci->loan_repayments_m->get_new_loan_repayments_array($loan_repayment_ids);
    //                                                             if($new_loan_repayments){
    //                                                                 foreach ($new_loan_repayments as $key => $loan_repayment):
    //                                                                     $old_member_repayment_array[$loan_repayment->old_loan_repayment_id] = $loan_repayment->id;
    //                                                                 endforeach;
    //                                                                 if($old_member_repayment_array){
    //                                                                     $loan_repayment_input = array();
    //                                                                     $loan_statement_ids = array();
    //                                                                     foreach ($old_loan_payment_statements as $key => $loan_statement_entry):
    //                                                                         $loan_statement_ids[] = $loan_statement_entry->id;
    //                                                                         $loan_repayment_input[] = array(
    //                                                                             'member_id' =>  $loan_statement_entry->member_id,
    //                                                                             'group_id'  =>  $loan_statement_entry->group_id,
    //                                                                             'transaction_date' => $loan_statement_entry->transaction_date,
    //                                                                             'transaction_type'  =>  4,
    //                                                                             'transfer_from' => $loan_statement_entry->transfer_from,
    //                                                                             'loan_id'   =>  $loan_statement_entry->loan_id,
    //                                                                             'loan_payment_id'   => $old_member_repayment_array[$loan_statement_entry->loan_payment_id],
    //                                                                             'amount'        =>  $loan_statement_entry->amount,
    //                                                                             'balance'       =>  0,
    //                                                                             'active'        =>  1,
    //                                                                             'status'        =>  1,
    //                                                                             'created_on'    =>  time(),
    //                                                                             'old_loan_statement_id'=>$loan_statement_entry->id,
    //                                                                         );
    //                                                                     endforeach;
    //                                                                     if(empty($loan_statement_ids)){
    //                                                                         return FALSE;
    //                                                                     }else{
    //                                                                         if($this->ci->loans_m->void_group_member_loan_statements($loan_statement_ids)){
    //                                                                             if($loan_statements_insert_result = $this->ci->loans_m->insert_loan_statements_batch($loan_repayment_input)){
    //                                                                               /*  print_r($old_loan_payment_statements);
    //                                                                                 print_r($loan_statement_ids);
    //                                                                                 print_r($loan_repayment_input); die();*/
    //                                                                                 return TRUE;
    //                                                                             }else{
    //                                                                                 return FALSE;
    //                                                                             }
    //                                                                         }else{
    //                                                                             return FALSE;
    //                                                                         }
    //                                                                     }
    //                                                                 }else{
    //                                                                     return FALSE;
    //                                                                 }
    //                                                             }else{
    //                                                                 return FALSE;   
    //                                                             }
    //                                                        }else{
    //                                                            return FALSE;
    //                                                        } 
    //                                                     }
    //                                                 }else{
    //                                                     return FALSE;
    //                                                 }
    //                                             }else{
    //                                                 return FALSE;
    //                                             }
    //                                         }
    //                                     }else{
    //                                         return TRUE;
    //                                     }
    //                                 }
    //                             }else{
    //                                 return FALSE;
    //                             }
    //                         }else{
    //                             return FALSE;
    //                         }
    //                     }else{
    //                         return TRUE;
    //                     }                        
    //                     //return TRUE;
    //                 }else{
    //                     return FALSE;
    //                 }
    //             // }else{
    //             //     return FALSE;
    //             // }
    //         }
    //     }else{
    //         return TRUE;
    //     }
    // }

    function update_group_member_contribution_statement_balances($group_ids = array(),$member_ids = array(),$date = 0){
        //$date = 0;
        $open_contribution_ids = $this->ci->contributions_m->get_group_open_contribution_ids($group_ids);
        $latest_statement_entries = $this->ci->statements_m->get_group_member_latest_statement_entries($group_ids,$member_ids,$date);
        
        $latest_contribution_statement_entries = $this->ci->statements_m->get_group_member_contribution_latest_statement_entries($group_ids,$member_ids,$date);
        $statement_entries = $this->ci->statements_m->get_group_member_contribution_statements($group_ids,$member_ids,$date);
        $member_contribution_balances_array = array();
        $member_contribution_balances_minus_contribution_transfers_array = array();
        $member_contribution_paid_array = array();
        $member_cumulative_balances_array = array();
        $member_cumulative_balances_minus_contribution_transfers_array = array();
        $member_cumulative_paid_array = array();
        if($statement_entries){
            $statement_entries_array = array();
            $contribution_ids = array();
            $statement_ids = array();

            foreach($statement_entries as $statement_entry):

                $member_cumulative_balances_array[$statement_entry->group_id][$statement_entry->member_id] = 
                isset($latest_statement_entries[$statement_entry->group_id][$statement_entry->member_id])?$latest_statement_entries[$statement_entry->group_id][$statement_entry->member_id]->balance:0;

                $member_cumulative_paid_array[$statement_entry->group_id][$statement_entry->member_id] = 
                isset($latest_statement_entries[$statement_entry->group_id][$statement_entry->member_id])?$latest_statement_entries[$statement_entry->group_id][$statement_entry->member_id]->cumulative_paid:0;

                $member_cumulative_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id] = 
                isset($latest_statement_entries[$statement_entry->group_id][$statement_entry->member_id])?$latest_statement_entries[$statement_entry->group_id][$statement_entry->member_id]->cumulative_minus_contribution_transfers_balance:0;


                $member_contribution_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] = 
                isset($latest_contribution_statement_entries[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id])?$latest_contribution_statement_entries[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id]->contribution_balance:0;

                $member_contribution_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] = 
                isset($latest_contribution_statement_entries[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id])?$latest_contribution_statement_entries[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id]->contribution_minus_contribution_transfers_balance:0;

                $member_contribution_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] =  
                isset($latest_contribution_statement_entries[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id])?$latest_contribution_statement_entries[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id]->contribution_paid:0;

                if(in_array($statement_entry->contribution_id,$contribution_ids)){

                }else{
                    $contribution_ids[] = $statement_entry->contribution_id;
                }
                $statement_ids[] = $statement_entry->id;

            endforeach;

            $contribution_objects_array = $this->ci->contributions_m->get_group_contribution_objects_array($group_ids,$contribution_ids);
            //print_r($contribution_objects_array);die;

            foreach($statement_entries as $statement_entry):
                
                $cumulative_balance = 0;
                $contribution_balance = 0;
                $cumulative_minus_contribution_transfers_balance = 0;
                $contribution_minus_contribution_transfers_balance = 0;
                $cumulative_paid = 0;
                $contribution_paid = 0;
                $contribution = $contribution_objects_array[$statement_entry->contribution_id];
                if(in_array($statement_entry->contribution_id, $open_contribution_ids)){
                    $contribution_from = isset($contribution_objects_array[$statement_entry->contribution_from_id])?$contribution_objects_array[$statement_entry->contribution_from_id]:"";
                    if(in_array($statement_entry->transaction_type,$this->payable_transaction_types_array)){
                        if(valid_currency($statement_entry->amount)){

                            if($contribution->display_contribution_arrears_cumulatively){
                                if($contribution_from){
                                    if($statement_entry->contribution_to_id){
                                        $member_contribution_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_to_id] += currency($statement_entry->amount);
                                        $member_cumulative_balances_array[$statement_entry->group_id][$statement_entry->member_id] += currency($statement_entry->amount);
                                    }
                                }
                            }else{                            
                                $member_contribution_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] += currency($statement_entry->amount);
                                $member_cumulative_balances_array[$statement_entry->group_id][$statement_entry->member_id] += currency($statement_entry->amount);
                                
                                if(in_array($statement_entry->transaction_type,$this->payable_transaction_types_minus_contribution_transfers_array)){
                                    $member_contribution_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] += currency($statement_entry->amount);
                                    $member_cumulative_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id] += currency($statement_entry->amount);
                                }
                            }
                        
                            $contribution_balance = $member_contribution_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id];
                            $cumulative_balance = $member_cumulative_balances_array[$statement_entry->group_id][$statement_entry->member_id]; 

                            $contribution_minus_contribution_transfers_balance = $member_contribution_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id];
                            $cumulative_minus_contribution_transfers_balance = $member_cumulative_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id]; 

                            if(in_array($statement_entry->transaction_type,$this->paid_deductable_transaction_types_array)){

                                $member_contribution_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] -= currency($statement_entry->amount);
                                $member_cumulative_paid_array[$statement_entry->group_id][$statement_entry->member_id] -= currency($statement_entry->amount);
                            }

                            $contribution_paid = $member_contribution_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id];
                            $cumulative_paid = $member_cumulative_paid_array[$statement_entry->group_id][$statement_entry->member_id]; 


                        }
                    }
                    if(in_array($statement_entry->transaction_type,$this->paid_transaction_types_array)){
                        if(valid_currency($statement_entry->amount)){
                            if($contribution->display_contribution_arrears_cumulatively){

                            }else{
                                $member_contribution_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] -= currency($statement_entry->amount);
                                $member_cumulative_balances_array[$statement_entry->group_id][$statement_entry->member_id] -= currency($statement_entry->amount);

                                if(in_array($statement_entry->transaction_type,$this->payable_transaction_types_minus_contribution_transfers_array)){
                                    $member_contribution_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] -= currency($statement_entry->amount);
                                    $member_cumulative_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id] -= currency($statement_entry->amount);
                                }
                            }

                            $member_contribution_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] += currency($statement_entry->amount);
                            $member_cumulative_paid_array[$statement_entry->group_id][$statement_entry->member_id] += currency($statement_entry->amount);

                            $contribution_balance = $member_contribution_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id];
                            $cumulative_balance = $member_cumulative_balances_array[$statement_entry->group_id][$statement_entry->member_id];  

                            $contribution_minus_contribution_transfers_balance = $member_contribution_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id];
                            $cumulative_minus_contribution_transfers_balance = $member_cumulative_balances_minus_contribution_transfers_array[$statement_entry->group_id][$statement_entry->member_id]; 



                            $contribution_paid = $member_contribution_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id];
                            $cumulative_paid = $member_cumulative_paid_array[$statement_entry->group_id][$statement_entry->member_id]; 
                            //echo $cumulative_paid."<br/>";
                        }
                    }
                }
                
                $statement_entries_array[] = array(
                    'statement_type' => 1,
                    'transaction_type' => $statement_entry->transaction_type,
                    'transaction_date' => $statement_entry->transaction_date,
                    'contribution_id' => $statement_entry->contribution_id,
                    'refund_id' => $statement_entry->refund_id,
                    'fine_id' => $statement_entry->fine_id,
                    'user_id' => $statement_entry->user_id,
                    'member_id' => $statement_entry->member_id,
                    'group_id' => $statement_entry->group_id,
                    'invoice_id' => $statement_entry->invoice_id,
                    'amount' => currency($statement_entry->amount),
                    'contribution_balance' => $contribution_balance,
                    'balance' => $cumulative_balance,
                    'contribution_paid' => $contribution_paid,
                    'cumulative_paid' => $cumulative_paid,
                    'contribution_minus_contribution_transfers_balance' => $contribution_minus_contribution_transfers_balance,
                    'cumulative_minus_contribution_transfers_balance' => $cumulative_minus_contribution_transfers_balance,
                    'active' => $statement_entry->active,
                    'created_by' => $statement_entry->created_by,
                    'created_on' => $statement_entry->created_on,
                    'modified_on' => $statement_entry->modified_on,
                    'modified_by' => $statement_entry->modified_by,
                    'account_id' => $statement_entry->account_id,
                    'description' => $statement_entry->description,
                    'deposit_id' => $statement_entry->deposit_id,
                    'transfer_to' => $statement_entry->transfer_to,
                    'contribution_transfer_id' => $statement_entry->contribution_transfer_id,
                    'contribution_from_id' => $statement_entry->contribution_from_id,
                    'contribution_to_id' => $statement_entry->contribution_to_id,
                    'fine_category_to_id' => $statement_entry->fine_category_to_id,
                    'contribution_invoice_due_date' => $statement_entry->contribution_invoice_due_date,
                    'fine_invoice_due_date' => $statement_entry->fine_invoice_due_date,
                    'loan_transfer_invoice_id' => $statement_entry->loan_transfer_invoice_id,
                    'loan_from_id' => $statement_entry->loan_from_id,
                    'loan_to_id' => $statement_entry->loan_to_id,
                    'is_a_back_dating_record' => $statement_entry->is_a_back_dating_record,
                    'old_statement_id'=> $statement_entry->id,
                    'member_to_id' => $statement_entry->member_to_id,
                    'member_from_id' => $statement_entry->member_from_id,
                );
            endforeach;    

            if(empty($statement_entries_array)){//this is where the error is ..Kindly review
                return FALSE;
            }else{
                //if($this->ci->statements_m->void_group_member_contribution_statements($group_ids,$member_ids)){
                //if($statement_ids_array = $this->ci->statements_m->get_group_member_contribution_statement_ids_array($group_ids,$member_ids)){
                    if($statement_insert_result = $this->ci->statements_m->insert_statements_batch($statement_entries_array)){
                        if($statement_ids){
                            if($this->ci->statements_m->void_contribution_statements_by_ids_array($statement_ids,$group_ids)){
                                //get statements with olds ids 
                                //get loan repayments with olds ids and create afresh loan repayments with new ids
                                $old_member_statements_array = array();
                                $new_statements = $this->ci->statements_m->get_group_member_new_contribution_statements_array($group_ids,$statement_ids);
                                if($new_statements){

                                    foreach ($new_statements as $key => $new_statement_entry):
                                        $old_member_statements_array[$new_statement_entry->old_statement_id] = $new_statement_entry->id;
                                    endforeach;

                                    if(empty($old_member_statements_array)){
                                        return FALSE;
                                    }else{
                                        $loan_repayments  = $this->ci->loan_repayments_m->get_group_member_loan_repayments_array($statement_ids); 
                                        if($loan_repayments){
                                            $loan_repayment_ids = array();
                                            $loan_repayment_input = array();
                                            foreach ($loan_repayments as $key => $loan_repayment_entry):
                                                $loan_repayment_ids[] = $loan_repayment_entry->id;
                                                $loan_repayment_input[] = array(
                                                    'loan_id'   =>  $loan_repayment_entry->loan_id,
                                                    'group_id'  =>  $loan_repayment_entry->group_id,
                                                    'member_id' =>  $loan_repayment_entry->member_id,
                                                    'receipt_date'=>$loan_repayment_entry->receipt_date,
                                                    'amount'    =>  $loan_repayment_entry->amount,
                                                    'status'    =>1,
                                                    'active'    =>1,
                                                    'created_on'=>  time(),
                                                    'transfer_from' => $loan_repayment_entry->transfer_from,
                                                    'incoming_loan_transfer_invoice_id' => '',
                                                    'incoming_contribution_transfer_id' => $old_member_statements_array[$loan_repayment_entry->incoming_contribution_transfer_id],
                                                    'old_incoming_contribution_transfer_id'=> $loan_repayment_entry->incoming_contribution_transfer_id,
                                                    'old_loan_repayment_id'=>$loan_repayment_entry->id,
                                                );
                                            endforeach;
                                            if(empty($loan_repayment_ids)){
                                                return FALSE;
                                            }else{
                                                if($this->ci->loan_repayments_m->void_group_member_loan_repayments($loan_repayment_ids)){
                                                    if($loan_repayment_insert_result = $this->ci->loan_repayments_m->insert_loan_repayments_batch($loan_repayment_input)){
                                                        //get loan statements and update payment id 
                                                        if(empty($loan_repayment_ids)){

                                                        }else{
                                                           $old_loan_payment_statements = $this->ci->loans_m->get_old_loan_repayment_statements_array($loan_repayment_ids);
                                                           if($old_loan_payment_statements){
                                                               $old_member_repayment_array =  array();
                                                                $new_loan_repayments = $this->ci->loan_repayments_m->get_new_loan_repayments_array($loan_repayment_ids);
                                                                if($new_loan_repayments){
                                                                    foreach ($new_loan_repayments as $key => $loan_repayment):
                                                                        $old_member_repayment_array[$loan_repayment->old_loan_repayment_id] = $loan_repayment->id;
                                                                    endforeach;
                                                                    if($old_member_repayment_array){
                                                                        $loan_repayment_input = array();
                                                                        $loan_statement_ids = array();
                                                                        foreach ($old_loan_payment_statements as $key => $loan_statement_entry):
                                                                            $loan_statement_ids[] = $loan_statement_entry->id;
                                                                            $loan_repayment_input[] = array(
                                                                                'member_id' =>  $loan_statement_entry->member_id,
                                                                                'group_id'  =>  $loan_statement_entry->group_id,
                                                                                'transaction_date' => $loan_statement_entry->transaction_date,
                                                                                'transaction_type'  =>  4,
                                                                                'transfer_from' => $loan_statement_entry->transfer_from,
                                                                                'loan_id'   =>  $loan_statement_entry->loan_id,
                                                                                'loan_payment_id'   => $old_member_repayment_array[$loan_statement_entry->loan_payment_id],
                                                                                'amount'        =>  $loan_statement_entry->amount,
                                                                                'balance'       =>  0,
                                                                                'active'        =>  1,
                                                                                'status'        =>  1,
                                                                                'created_on'    =>  time(),
                                                                                'old_loan_statement_id'=>$loan_statement_entry->id,
                                                                            );
                                                                        endforeach;
                                                                        if(empty($loan_statement_ids)){
                                                                            return FALSE;
                                                                        }else{
                                                                            if($this->ci->loans_m->void_group_member_loan_statements($loan_statement_ids)){
                                                                                if($loan_statements_insert_result = $this->ci->loans_m->insert_loan_statements_batch($loan_repayment_input)){
                                                                                    return TRUE;
                                                                                }else{
                                                                                    return FALSE;
                                                                                }
                                                                            }else{
                                                                                return FALSE;
                                                                            }
                                                                        }
                                                                    }else{
                                                                        return FALSE;
                                                                    }
                                                                }else{
                                                                    return FALSE;   
                                                                }
                                                           }else{
                                                               return FALSE;
                                                           } 
                                                        }
                                                    }else{
                                                        return FALSE;
                                                    }
                                                }else{
                                                    return FALSE;
                                                }
                                            }
                                        }else{
                                            return TRUE;
                                        }
                                    }
                                }else{
                                    return FALSE;
                                }
                            }else{
                                return FALSE;
                            }
                        }else{
                            return TRUE;
                        }                        
                        //return TRUE;
                    }else{
                        return FALSE;
                    }
                // }else{
                //     return FALSE;
                // }
            }
        }else{
            //die('nothing');
            return TRUE;
        }
    }

    // private function _revise_contribution_fine_invoices($contribution_fine_invoices = array()){
    //     $result = TRUE;
    //     foreach ($contribution_fine_invoices as $contribution_fine_invoice) {
    //         # code...
    //         $balance = $this->ci->statements_m->get_member_contribution_balance($contribution_fine_invoice->group_id,$contribution_fine_invoice->member_id,$contribution_fine_invoice->contribution_id,$contribution_fine_invoice->invoice_date);
    //         if($balance<=0){
    //             if($this->void_fine_invoice($contribution_fine_invoice->id,0,$contribution_fine_invoice->contribution_id)){

    //             }else{
    //                 $result = FALSE;
    //             }
    //         }
    //     }
    //     return $result;
    // }
    private function _revise_contribution_fine_invoices($contribution_fine_invoices = array()){
        $result = TRUE;
        foreach ($contribution_fine_invoices as $contribution_fine_invoice) {
            # code...
            $balance = $this->ci->statements_m->get_group_member_contribution_balance($contribution_fine_invoice->group_id,$contribution_fine_invoice->member_id,$contribution_fine_invoice->contribution_id,$contribution_fine_invoice->invoice_date);
            if($balance<=0){
                if($this->void_fine_invoice($contribution_fine_invoice->id,0,$contribution_fine_invoice->contribution_id)){

                }else{
                    $result = FALSE;
                }
            }
        }
        return $result;
    }

    private function _update_invoice_amount_payables($invoices = array(),$total_amount_paid = 0){
        $result = TRUE;
        foreach($invoices as $invoice){
            if($total_amount_paid>0){
                if($total_amount_paid>$invoice->amount_payable){
                    $amount_paid = $invoice->amount_payable;
                    $total_amount_paid-=$invoice->amount_payable;
                }else{
                    $amount_paid = $total_amount_paid;
                    $total_amount_paid-=$invoice->amount_payable;
                }
            }else{
                $amount_paid = 0;
            }
            $input = array(
                'amount_paid'=>$amount_paid,
                'modified_on'=>time(),
            );
            if($invoice->amount_paid==$amount_paid){
                //do nothing for now
            }else{
                if($result = $this->ci->invoices_m->update($invoice->id,$input)){

                }else{
                    $this->ci->session->set_flashdata('error','Could not update invoice amount paid');
                    $result = FALSE;
                }
            }
        }
        return $result;
    }

    public function update_member_contribution_statement_balances($group_id = 0,$member_id = 0,$contribution_id = 0,$date = 0){
        $result = TRUE;
        $statement_entries = $this->ci->statements_m->get_member_contribution_statement_entries_to_reconcile($group_id,$member_id,$contribution_id,$date);
        $balance = $this->ci->statements_m->get_member_contribution_balance($group_id,$member_id,$contribution_id,$date);
        foreach ($statement_entries as $statement_entry) {
            # code...
            if($statement_entry->transaction_type==1||$transaction_type==21||$transaction_type==22||$transaction_type==23||$transaction_type==24){
                $balance += $statement_entry->amount;
            }else if($statement_entry->transaction_type==9||$statement_entry->transaction_type==10||$statement_entry->transaction_type==11||$statement_entry->transaction_type==15){
                $balance -= $statement_entry->amount;
            }
            $input = array(
                'contribution_balance'=>$balance,
                'balance'=>$balance,
                'modified_on'=>time()
            );
            if($result = $this->ci->statements_m->update($statement_entry->id,$input)){
                //do nothing for now
            }else{
                $result = FALSE;
            }
        }
        $this->update_group_contribution_fines_balances($group_id,$member_id);
        return $result;
    }

    public function reconcile_member_fine_statement($transaction_type = 0,$group_id = 0,$member_id = 0,$contribution_id = 0,$fine_category_id = 0,$date = 0){
        if($transaction_type&&$group_id&&$member_id&&($contribution_id||$fine_category_id)){
            $group_ids = array($group_id);
            $member_ids = array($member_id);
            return $this->update_group_member_fine_statement_balances($group_ids,$member_ids);
        }else{
            //die("am in");
            return FALSE;
        }
    }

    // public function update_group_member_fine_statement_balances($group_ids = array(),$member_ids = array()){
    //     $statement_entries = $this->ci->statements_m->get_group_member_fine_statements($group_ids,$member_ids);

    //     $member_contribution_fine_balances_array = array();
    //     $member_fine_balances_array = array();
    //     $member_cumulative_fine_balances_array = array();

    //     $member_contribution_fine_paid_array = array();
    //     $member_fine_paid_array = array();
    //     $member_cumulative_fine_paid_array = array();
    //     if($statement_entries){
    //         $statement_entries_array = array();
    //         $contribution_ids = array();
    //         $fine_category_ids = array();
    //         foreach($statement_entries as $statement_entry):
                
    //             $member_cumulative_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id] = 0;
                
    //             $member_cumulative_fine_paid_array[$statement_entry->group_id][$statement_entry->member_id] = 0;

    //             if($statement_entry->contribution_id){
    //                 $member_contribution_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] = 0;

    //                 $member_contribution_fine_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] = 0;

    //                 if(in_array($statement_entry->contribution_id,$contribution_ids)){

    //                 }else{
    //                     $contribution_ids[] = $statement_entry->contribution_id;
    //                 } 
    //             }

    //             if($statement_entry->fine_category_id){

    //                 $member_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->fine_category_id] = 0;

    //                 $member_fine_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->fine_category_id] = 0;

    //                 if(in_array($statement_entry->fine_category_id,$fine_category_ids)){

    //                 }else{
    //                     $fine_category_ids[] = $statement_entry->fine_category_id;
    //                 } 
    //             }
                
    //         endforeach;

    //         $contribution_objects_array = $this->ci->contributions_m->get_group_contribution_objects_array($group_ids,$contribution_ids);

    //         $statement_ids = array();

    //         foreach($statement_entries as $statement_entry):

    //             $cumulative_balance = 0;
    //             $contribution_fine_balance = 0;
    //             $fine_balance = 0;

    //             $cumulative_paid = 0;
    //             $contribution_fine_paid = 0;
    //             $fine_paid = 0;

    //             if(in_array($statement_entry->transaction_type,$this->fine_payable_transaction_types_array)){
    //                 if(valid_currency($statement_entry->amount)){

    //                     $member_cumulative_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id] += currency($statement_entry->amount);

    //                     if($statement_entry->contribution_id){
    //                         $member_contribution_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] += currency($statement_entry->amount);
    //                     }

    //                     if($statement_entry->fine_category_id){
    //                         $member_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->fine_category_id] += currency($statement_entry->amount);
    //                     }

    //                     $cumulative_balance = $member_cumulative_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id];


    //                 }
    //             }

    //             if(in_array($statement_entry->transaction_type,$this->fine_paid_transaction_types_array)){
                    
    //                 $member_cumulative_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id] -= currency($statement_entry->amount);

    //                 $member_cumulative_fine_paid_array[$statement_entry->group_id][$statement_entry->member_id] += currency($statement_entry->amount);

    //                 if($statement_entry->contribution_id){
    //                     $member_contribution_fine_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] -= currency($statement_entry->amount);
    //                 }

    //                 if($statement_entry->fine_category_id){
    //                     $member_fine_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->fine_category_id] -= currency($statement_entry->amount);
    //                 }
                    
    //                 $cumulative_paid = $member_cumulative_fine_paid_array[$statement_entry->group_id][$statement_entry->member_id];
    //                 $cumulative_balance = $member_cumulative_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id];

    //             }
                
    //             if($statement_entry->contribution_id){
    //                 $contribution_fine_balance = $member_contribution_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id];
    //             }

    //             if($statement_entry->fine_category_id){
    //                 $fine_balance = $member_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->fine_category_id];
    //             }

    //             if($statement_entry->contribution_id){
    //                 $contribution_fine_paid = $member_contribution_fine_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id];
    //             }

    //             if($statement_entry->fine_category_id){
    //                 $fine_paid = $member_fine_paid_array
    //                 [$statement_entry->group_id]
    //                 [$statement_entry->member_id]
    //                 [$statement_entry->fine_category_id];
    //             }

    //             $statement_entries_array[] = array(
    //                 'transaction_type' => $statement_entry->transaction_type,
    //                 'transaction_date' => $statement_entry->transaction_date,
    //                 'contribution_id' => $statement_entry->contribution_id,
    //                 'refund_id' => $statement_entry->refund_id,
    //                 'fine_id' => $statement_entry->fine_id,
    //                 'user_id' => $statement_entry->user_id,
    //                 'member_id' => $statement_entry->member_id,
    //                 'group_id' => $statement_entry->group_id,
    //                 'invoice_id' => $statement_entry->invoice_id,
    //                 'amount' => $statement_entry->amount,
    //                 'contribution_fine_balance' => $contribution_fine_balance,
    //                 'fine_balance' => $fine_balance,
    //                 'contribution_balance' => $contribution_fine_balance,
    //                 'balance' => $cumulative_balance,
    //                 'contribution_fine_paid' => $contribution_fine_paid,
    //                 'fine_paid' => $fine_paid,
    //                 'contribution_paid' => $contribution_fine_paid,
    //                 'cumulative_paid' => $cumulative_paid,
    //                 'contribution_minus_contribution_transfers_balance' => $statement_entry->contribution_minus_contribution_transfers_balance,
    //                 'cumulative_minus_contribution_transfers_balance' => $statement_entry->cumulative_minus_contribution_transfers_balance,
    //                 'active' => $statement_entry->active,
    //                 'created_by' => $statement_entry->created_by,
    //                 'created_on' => $statement_entry->created_on,
    //                 'modified_on' => $statement_entry->modified_on,
    //                 'modified_by' => $statement_entry->modified_by,
    //                 'account_id' => $statement_entry->account_id,
    //                 'description' => $statement_entry->description,
    //                 'deposit_id' => $statement_entry->deposit_id,
    //                 'transfer_to' => $statement_entry->transfer_to,
    //                 'contribution_transfer_id' => $statement_entry->contribution_transfer_id,
    //                 'contribution_from_id' => $statement_entry->contribution_from_id,
    //                 'contribution_to_id' => $statement_entry->contribution_to_id,
    //                 'fine_category_to_id' => $statement_entry->fine_category_to_id,
    //                 'contribution_invoice_due_date' => $statement_entry->contribution_invoice_due_date,
    //                 'fine_invoice_due_date' => $statement_entry->fine_invoice_due_date,
    //                 'loan_transfer_invoice_id' => $statement_entry->loan_transfer_invoice_id,
    //                 'loan_from_id' => $statement_entry->loan_from_id,
    //                 'loan_to_id' => $statement_entry->loan_to_id,
    //                 'is_a_back_dating_record' => $statement_entry->is_a_back_dating_record,
    //             );

    //             $statement_ids[] = $statement_entry->id;

    //         endforeach;

    //         if(empty($statement_entries_array)){
    //             return FALSE;
    //         }else{
    //             if($statement_insert_result = $this->ci->statements_m->insert_statements_batch($statement_entries_array)){
    //                 if($statement_ids){
    //                     if($this->ci->statements_m->void_fine_statements_by_ids_array($statement_ids)){
    //                         return TRUE;
    //                     }else{
    //                         return FALSE;
    //                     }
    //                 }else{
    //                     return TRUE;
    //                 }
    //             }else{
    //                 return FALSE;
    //             }
    //         }

    //     }else{
    //         return TRUE;
    //     }
    // }

    public function update_group_member_fine_statement_balances($group_ids = array(),$member_ids = array(),$date = 0){

        $latest_fine_statement_entries = $this->ci->statements_m->get_group_member_fine_latest_statement_entries($group_ids,$member_ids,$date);

        $latest_contribution_fine_statement_entries = $this->ci->statements_m->get_group_member_contribution_fine_latest_statement_entries($group_ids,$member_ids,$date);

        $latest_fine_category_statement_entries = $this->ci->statements_m->get_group_member_fine_category_latest_statement_entries($group_ids,$member_ids,$date);
        
        $statement_entries = $this->ci->statements_m->get_group_member_fine_statements($group_ids,$member_ids,$date);
        $member_contribution_fine_balances_array = array();
        $member_fine_balances_array = array();
        $member_cumulative_fine_balances_array = array();
        $member_contribution_fine_paid_array = array();
        $member_fine_paid_array = array();
        $member_cumulative_fine_paid_array = array();
        if($statement_entries){
            $statement_entries_array = array();
            $contribution_ids = array();
            $fine_category_ids = array();
            foreach($statement_entries as $statement_entry):
                
                $member_cumulative_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id] = isset($latest_fine_statement_entries[$statement_entry->group_id][$statement_entry->member_id])?$latest_fine_statement_entries[$statement_entry->group_id][$statement_entry->member_id]->balance:0;
                
                $member_cumulative_fine_paid_array[$statement_entry->group_id][$statement_entry->member_id] = isset($latest_fine_statement_entries[$statement_entry->group_id][$statement_entry->member_id])?$latest_fine_statement_entries[$statement_entry->group_id][$statement_entry->member_id]->cumulative_paid:0;

                if($statement_entry->contribution_id){
                    $member_contribution_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] = isset($latest_contribution_fine_statement_entries[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id])?$latest_contribution_fine_statement_entries[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id]->contribution_fine_balance:0;

                    $member_contribution_fine_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] = isset($latest_contribution_fine_statement_entries[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id])?$latest_contribution_fine_statement_entries[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id]->contribution_fine_paid:0;

                    if(in_array($statement_entry->contribution_id,$contribution_ids)){

                    }else{
                        $contribution_ids[] = $statement_entry->contribution_id;
                    } 
                }

                if($statement_entry->fine_category_id){

                    $member_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->fine_category_id] = isset($latest_fine_category_statement_entries[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->fine_category_id])?$latest_fine_category_statement_entries[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->fine_category_id]->fine_balance:0;

                    $member_fine_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->fine_category_id] = isset($latest_fine_category_statement_entries[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->fine_category_id])?$latest_fine_category_statement_entries[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->fine_category_id]->fine_paid:0;;

                    if(in_array($statement_entry->fine_category_id,$fine_category_ids)){

                    }else{
                        $fine_category_ids[] = $statement_entry->fine_category_id;
                    } 
                }
                
            endforeach;

            $contribution_objects_array = $this->ci->contributions_m->get_group_contribution_objects_array($group_ids,$contribution_ids);

            $statement_ids = array();
            $statement_group_ids = array();

            foreach($statement_entries as $statement_entry):

                $cumulative_balance = 0;
                $contribution_fine_balance = 0;
                $fine_balance = 0;

                $cumulative_paid = 0;
                $contribution_fine_paid = 0;
                $fine_paid = 0;

                if(in_array($statement_entry->transaction_type,$this->fine_payable_transaction_types_array)){
                    if(valid_currency($statement_entry->amount)){

                        $member_cumulative_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id] += currency($statement_entry->amount);

                        if($statement_entry->contribution_id){
                            $member_contribution_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] += currency($statement_entry->amount);
                        }

                        if($statement_entry->fine_category_id){
                            $member_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->fine_category_id] += currency($statement_entry->amount);
                        }

                        $cumulative_balance = $member_cumulative_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id];


                    }
                }

                if(in_array($statement_entry->transaction_type,$this->fine_paid_transaction_types_array)){
                    
                    $member_cumulative_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id] -= currency($statement_entry->amount);

                    $member_cumulative_fine_paid_array[$statement_entry->group_id][$statement_entry->member_id] += currency($statement_entry->amount);

                    if($statement_entry->contribution_id){
                        $member_contribution_fine_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id] -= currency($statement_entry->amount);
                    }

                    if($statement_entry->fine_category_id){
                        $member_fine_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->fine_category_id] -= currency($statement_entry->amount);
                    }

                }

                $cumulative_paid = $member_cumulative_fine_paid_array[$statement_entry->group_id][$statement_entry->member_id];
                $cumulative_balance = $member_cumulative_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id];
                
                if($statement_entry->contribution_id){
                    $contribution_fine_balance = $member_contribution_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id];
                }

                if($statement_entry->fine_category_id){
                    $fine_balance = $member_fine_balances_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->fine_category_id];
                }

                if($statement_entry->contribution_id){
                    $contribution_fine_paid = $member_contribution_fine_paid_array[$statement_entry->group_id][$statement_entry->member_id][$statement_entry->contribution_id];
                }

                if($statement_entry->fine_category_id){
                    $fine_paid = $member_fine_paid_array
                    [$statement_entry->group_id]
                    [$statement_entry->member_id]
                    [$statement_entry->fine_category_id];
                }

                $statement_entries_array[] = array(
                    'statement_type' => 2,
                    'transaction_type' => $statement_entry->transaction_type,
                    'transaction_date' => $statement_entry->transaction_date,
                    'contribution_id' => $statement_entry->contribution_id,
                    'fine_category_id' => $statement_entry->fine_category_id,
                    'refund_id' => $statement_entry->refund_id,
                    'fine_id' => $statement_entry->fine_id,
                    'user_id' => $statement_entry->user_id,
                    'member_id' => $statement_entry->member_id,
                    'group_id' => $statement_entry->group_id,
                    'invoice_id' => $statement_entry->invoice_id,
                    'amount' => $statement_entry->amount,
                    'contribution_fine_balance' => $contribution_fine_balance,
                    'fine_balance' => $fine_balance,
                    'contribution_balance' => $contribution_fine_balance,
                    'balance' => $cumulative_balance,
                    'contribution_fine_paid' => $contribution_fine_paid,
                    'fine_paid' => $fine_paid,
                    'contribution_paid' => $contribution_fine_paid,
                    'cumulative_paid' => $cumulative_paid,
                    'contribution_minus_contribution_transfers_balance' => $statement_entry->contribution_minus_contribution_transfers_balance,
                    'cumulative_minus_contribution_transfers_balance' => $statement_entry->cumulative_minus_contribution_transfers_balance,
                    'active' => $statement_entry->active,
                    'created_by' => $statement_entry->created_by,
                    'created_on' => $statement_entry->created_on,
                    'modified_on' => $statement_entry->modified_on,
                    'modified_by' => $statement_entry->modified_by,
                    'account_id' => $statement_entry->account_id,
                    'description' => $statement_entry->description,
                    'deposit_id' => $statement_entry->deposit_id,
                    'transfer_to' => $statement_entry->transfer_to,
                    'contribution_transfer_id' => $statement_entry->contribution_transfer_id,
                    'contribution_from_id' => $statement_entry->contribution_from_id,
                    'contribution_to_id' => $statement_entry->contribution_to_id,
                    'fine_category_to_id' => $statement_entry->fine_category_to_id,
                    'contribution_invoice_due_date' => $statement_entry->contribution_invoice_due_date,
                    'fine_invoice_due_date' => $statement_entry->fine_invoice_due_date,
                    'loan_transfer_invoice_id' => $statement_entry->loan_transfer_invoice_id,
                    'loan_from_id' => $statement_entry->loan_from_id,
                    'loan_to_id' => $statement_entry->loan_to_id,
                    'is_a_back_dating_record' => $statement_entry->is_a_back_dating_record,
                );

                $statement_ids[] = $statement_entry->id;
                $statement_group_ids[] = $statement_entry->group_id;

            endforeach;

            if(empty($statement_entries_array)){
                return FALSE;
            }else{
                if($statement_insert_result = $this->ci->statements_m->insert_statements_batch($statement_entries_array)){
                    if($statement_ids){
                        if($this->ci->statements_m->void_fine_statements_by_ids_array($statement_ids,$statement_group_ids)){
                            return TRUE;
                        }else{
                            return FALSE;
                        }
                    }else{
                        return TRUE;
                    }
                }else{
                    return FALSE;
                }
            }

        }else{
            return TRUE;
        }
    }

    public function reconcile_member_miscellaneous_statement($transaction_type = 0,$group_id = 0,$member_id = 0,$date = 0){
        if($transaction_type&&$group_id&&$member_id){
            $result = TRUE;
            if($transaction_type==4){
                $balance = $this->ci->statements_m->get_member_miscellaneous_balance($group_id,$member_id,$date);
                $total_amount_paid = $this->ci->deposits_m->get_total_miscellaneous_amount_paid_after_date($group_id,$member_id,$date);
                if($balance<0){
                    $total_amount_paid+=abs($balance);
                }else if($balance>$total_amount_paid){
                    $balance = 0;
                }
                $invoices = $this->ci->invoices_m->get_member_miscellaneous_invoices_to_reconcile($group_id,$member_id,$date);
                if($this->_update_invoice_amount_payables($invoices,$total_amount_paid)){
                    return $this->update_member_miscellaneous_statement_balances($group_id,$member_id,$date);
                }else{
                    return FALSE;
                }
            }else if($transaction_type==17||$transaction_type==18||$transaction_type==19||$transaction_type==20){
                $total_amount_paid = $this->ci->deposits_m->get_total_miscellaneous_amount_paid($group_id,$member_id);
                $invoices = $this->ci->invoices_m->get_member_miscellaneous_invoices_to_reconcile($group_id,$member_id);
                if($this->_update_invoice_amount_payables($invoices,$total_amount_paid)){
                    return $this->update_member_miscellaneous_statement_balances($group_id,$member_id);
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function update_member_fine_statement_balances($group_id = 0,$member_id = 0,$contribution_id = 0,$fine_category_id = 0,$date = 0){
        $result = TRUE;
        if($contribution_id){
            $statement_entries = $this->ci->statements_m->get_member_contribution_fine_statement_entries_to_reconcile($group_id,$member_id,$contribution_id,$date);
        }else if($fine_category_id){
            $statement_entries = $this->ci->statements_m->get_member_fine_statement_entries_to_reconcile($group_id,$member_id,$fine_category_id,$date);
        }else{
            $statement_entries = array();
        }
        $balance = $this->ci->statements_m->get_member_fine_balance($group_id,$member_id,$contribution_id,$fine_category_id,$date);
        foreach ($statement_entries as $statement_entry) {
            # code...
            if($statement_entry->transaction_type==2||$statement_entry->transaction_type==3){
                $balance += $statement_entry->amount;
                
            }else if($statement_entry->transaction_type==12||$statement_entry->transaction_type==13||$statement_entry->transaction_type==14||$statement_entry->transaction_type==16){
                $balance -= $statement_entry->amount;
            }
            $input = array(
                'contribution_balance'=>$balance,
                'balance'=>$balance,
                'modified_on'=>time()
            );
            if($result = $this->ci->statements_m->update($statement_entry->id,$input)){
            }else{
                $result = FALSE;
            }
        }
        $this->update_group_contribution_fines_balances($group_id,$member_id);
        return $result;
    }

    public function update_member_miscellaneous_statement_balances($group_id = 0,$member_id = array(),$date = 0){
        $result = TRUE;
        if(is_array($member_id)){
            foreach($member_id as $key => $value){
                $statement_entries = $this->ci->statements_m->get_member_miscellaneous_statement_entries_to_reconcile($group_id,$value,$date);
                $balance = $this->ci->statements_m->get_member_miscellaneous_balance($group_id,$value,$date);
                foreach ($statement_entries as $statement_entry) {
                    # code...
                    if($statement_entry->transaction_type==4){
                        $balance += $statement_entry->amount;
                        
                    }else if($statement_entry->transaction_type==17||$statement_entry->transaction_type==18||$statement_entry->transaction_type==19||$statement_entry->transaction_type==20){
                        $balance -= $statement_entry->amount;
                    }
                    $input = array(
                        'contribution_balance'=>$balance,
                        'balance'=>$balance,
                        'modified_on'=>time()
                    );
                    if($result = $this->ci->statements_m->update($statement_entry->id,$input)){
                    }else{
                        $result = FALSE;
                    }
                }
                $this->update_group_contribution_fines_balances($group_id,$value);
            }
            return $result;
        }else{
            $statement_entries = $this->ci->statements_m->get_member_miscellaneous_statement_entries_to_reconcile($group_id,$member_id,$date);
            $balance = $this->ci->statements_m->get_member_miscellaneous_balance($group_id,$member_id,$date);
            foreach ($statement_entries as $statement_entry) {
                # code...
                if($statement_entry->transaction_type==4){
                    $balance += $statement_entry->amount;
                    
                }else if($statement_entry->transaction_type==17||$statement_entry->transaction_type==18||$statement_entry->transaction_type==19||$statement_entry->transaction_type==20){
                    $balance -= $statement_entry->amount;
                }
                $input = array(
                    'contribution_balance'=>$balance,
                    'balance'=>$balance,
                    'modified_on'=>time()
                );
                if($result = $this->ci->statements_m->update($statement_entry->id,$input)){
                }else{
                    $result = FALSE;
                }
            }
            $this->update_group_contribution_fines_balances($group_id,$member_id);
            return $result;
        }
    }

    public function void_bulk_contribution_invoice($group_id = 0, $ids = array()){
        if($ids){
            $member_statements_reconciled = 0;
            $member_statements_failed_reconciled = 0;
            if($this->ci->invoices_m->void_group_invoices($group_id,$ids)){
                $statement_entries_array = $this->ci->statements_m->get_group_statement_by_invoice_ids_array($group_id,$ids);
                $statement_ids = array();
                $member_ids =  array();
                $group_ids = array();
                if($statement_entries_array){
                    foreach ($statement_entries_array as $key => $statement):
                        $member_statements_reconciled++;
                        $member_ids[] = $statement->member_id;
                        $group_ids[] = $statement->group_id;
                        $statement_ids[] = $statement->id;
                    endforeach;
                    if($statement_ids && $member_ids && $group_ids){
                        if($this->ci->statements_m->void_group_contribution_statements_by_ids_array($group_ids,$statement_ids)){
                            return $this->update_group_member_contribution_statement_balances($group_ids,$member_ids);
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Some parameters are missing(var)');
                        return FALSE;   
                    }
                }
            }       
        }else{
            $this->ci->session->set_flashdata('error','invoice id array is empty(var)');
            return FALSE;
        }

    }

    public function void_bulk_fine_invoice($group_id = 0, $ids = array()){
        if($ids){
            if($this->ci->invoices_m->void_group_invoices($group_id,$ids)){
                $statement_entries_array = $this->ci->statements_m->get_group_statement_by_invoice_ids_array($group_id,$ids);
                $statement_ids = array();
                $member_ids =  array();
                $group_ids = array();
                if($statement_entries_array){
                    foreach ($statement_entries_array as $key => $statement):
                        $member_statements_reconciled++;
                        $member_ids[] = $statement->member_id;
                        $group_ids[] = $statement->group_id;
                        $statement_ids[] = $statement->id;
                    endforeach;
                    if($statement_ids && $member_ids && $group_ids){
                        if($this->ci->statements_m->void_group_fine_statements_by_ids_array($group_ids,$statement_ids)){
                            return $this->update_group_member_fine_statement_balances($group_ids,$member_ids);
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Some parameters are missing(var)');
                        return FALSE;   
                    }
                }
            }
        }else{
            $this->ci->session->set_flashdata('error','invoice id array is empty(var)');
            return FALSE;
        }
    }

    public function void_bulk_miscellaneous_invoice($group_id = 0, $ids = array()){
        if($ids){
            // if($this->ci->invoices_m->void_group_invoices($group_id,$ids)){
            //     $statement_entries_array = $this->ci->statements_m->get_group_statement_by_invoice_ids_array($group_id,$ids);
            //     $statement_ids = array();
            //     $member_ids =  array();
            //     $group_ids = array();
            //     $member_statements_reconciled = 0;
            //     if($statement_entries_array){
            //         foreach ($statement_entries_array as $key => $statement):
            //             $member_statements_reconciled++;
            //             $member_ids[] = $statement->member_id;
            //             $group_ids[] = $statement->group_id;
            //             $statement_ids[] = $statement->id;
            //         endforeach;
            //         if($statement_ids && $member_ids && $group_ids){
            //             if($this->ci->statements_m->void_group_miscellaneous_statements_by_ids_array($group_ids,$statement_ids)){
            //                 return $this->update_member_miscellaneous_statement_balances($group_ids[0],$member_ids);
            //             }
            //         }else{
            //             $this->ci->session->set_flashdata('error','Some parameters are missing(var)');
            //             return FALSE;   
            //         }
            //     }
            // }
            $response = TRUE;
            foreach($ids as $key => $value){
                $response = $this->void_miscellaneous_invoice($value);
            }
            return $response;
        }else{
            $this->ci->session->set_flashdata('error','invoice id array is empty(var)');
            return FALSE;
        }
    }

    public function void_contribution_invoice($id = 0){
        $input = array(
            'active'=>0,
            'modified_on'=>time(),
        );
        if($result = $this->ci->invoices_m->update($id,$input)){
            $statement_entry = $this->ci->statements_m->get_statement_entry_by_invoice_id($id);
            if($statement_entry){
                $input = array(
                    'active'=>0,
                    'modified_on'=>time(),
                );
                if($result = $this->ci->statements_m->update($statement_entry->id,$input)){
                    if($this->reconcile_member_statement($statement_entry->transaction_type,$statement_entry->group_id,$statement_entry->member_id,$statement_entry->contribution_id,$statement_entry->transaction_date)){
                        return TRUE;
                    }else{
                        $this->ci->session->set_flashdata('error','Something went wrong during statement reconciliation');
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Contribution invoice statement entry could not be found');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Contribution invoice could not be updated');
            return FALSE;
        }
    }

    public function void_fine_invoice($id = 0,$fine_id = 0,$contribution_id = 0,$group_id=0){
        $fine = $this->ci->fines_m->get_group_fine($fine_id,$group_id);
        if($fine){
            $input = array(
                'active'=>0,
                'modified_on'=>time(),
            );
            if($result = $this->ci->fines_m->update($fine_id,$input)){
                $input = array(
                    'active'=>0,
                    'modified_on'=>time(),
                );
                if($result = $this->ci->invoices_m->update($id,$input)){
                    $statement_entry = $this->ci->statements_m->get_statement_entry_by_invoice_id($id);
                    if($statement_entry){
                        $input = array(
                            'active'=>0,
                            'modified_on'=>time(),
                        );
                        if($result = $this->ci->statements_m->update($statement_entry->id,$input)){
                            if($this->reconcile_member_fine_statement($statement_entry->transaction_type,$statement_entry->group_id,$statement_entry->member_id,$statement_entry->contribution_id,$statement_entry->fine_category_id,$statement_entry->transaction_date)){
                                return TRUE;
                            }else{
                                $this->ci->session->set_flashdata('error','Something went wrong during statement reconciliation');
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Fine invoice statement entry could not be found');
                        return FALSE;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Fine invoice could not be updated');
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else if($contribution_id){
            $input = array(
                'active'=>0,
                'modified_on'=>time(),
            );
            if($result = $this->ci->invoices_m->update($id,$input)){
                $statement_entry = $this->ci->statements_m->get_statement_entry_by_invoice_id($id);
                if($statement_entry){
                    $input = array(
                        'active'=>0,
                        'modified_on'=>time(),
                    );
                    if($result = $this->ci->statements_m->update($statement_entry->id,$input)){
                        if($this->reconcile_member_fine_statement($statement_entry->transaction_type,$statement_entry->group_id,$statement_entry->member_id,$statement_entry->contribution_id,$statement_entry->fine_category_id,$statement_entry->transaction_date)){
                            return TRUE;
                        }else{
                            $this->ci->session->set_flashdata('error','Something went wrong during statement reconciliation');
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Fine invoice statement entry could not be found');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Fine invoice could not be updated');
                return FALSE;
            }

        }else{
            return FALSE;
        }
    }

    function void_group_contribution_fine_invoices($group_id = 0,$invoice_ids = array(),$statements_ids = array()){
        if($this->ci->invoices_m->void_group_contribution_fine_invoices($group_id,$invoice_ids)){
            if($this->ci->statements_m->void_group_contribution_fine_statements($group_id,$statements_ids)){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function void_miscellaneous_invoice($id = 0){
        $input = array(
            'active'=>0,
            'modified_on'=>time(),
        );
        if($result = $this->ci->invoices_m->update($id,$input)){
            $statement_entry = $this->ci->statements_m->get_statement_entry_by_invoice_id($id);
            if($statement_entry){
                $input = array(
                    'active'=>0,
                    'modified_on'=>time(),
                );
                if($result = $this->ci->statements_m->update($statement_entry->id,$input)){
                    if($this->reconcile_member_miscellaneous_statement($statement_entry->transaction_type,$statement_entry->group_id,$statement_entry->member_id,$statement_entry->transaction_date)){
                        return TRUE;
                    }else{
                        $this->ci->session->set_flashdata('error','Something went wrong during statement reconciliation');
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Miscellaneous invoice statement entry could not be found');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Miscellaneous invoice could not be updated');
            return FALSE;
        }
    }

    // public function create_invoice($type = 0,$group_id = 0,$member = array(),$contribution = array(),$invoice_date = 0,$due_date = 0,$amount_payable = 0,$description = '',$sms_template = '',$send_sms_notification = FALSE,$send_email_notification = FALSE,$is_a_back_dating_record = FALSE){
    //     $group = $this->ci->groups_m->get($group_id);
    //     if($group&&$type&&$group_id&&$member&&$contribution&&$invoice_date&&$due_date&&$amount_payable&&$sms_template){
    //         $input = array(
    //             'type'=>1,
    //             'invoice_number'=>'',
    //             'user_id'=>$member->user_id,
    //             'member_id'=>$member->id,
    //             'contribution_id'=>$contribution->id,
    //             'group_id'=>$group_id,
    //             'invoice_date'=>$invoice_date,
    //             'due_date'=>$due_date,
    //             'amount_payable'=>$amount_payable,
    //             'description'=>$description,
    //             'active'=>1,
    //             'created_on'=>time(),
    //             'is_a_back_dating_record' => $is_a_back_dating_record?1:0
    //         );
    //         if($invoice_id = $this->ci->invoices_m->insert($input)){
    //             $input = array(
    //                 'transaction_type'=>1,
    //                 'transaction_date'=>$invoice_date,
    //                 'contribution_invoice_due_date'=>$due_date,
    //                 'contribution_id'=>$contribution->id,
    //                 'user_id'=>$member->user_id,
    //                 'member_id'=>$member->id,
    //                 'group_id'=>$group_id,
    //                 'invoice_id'=>$invoice_id,
    //                 'amount'=>$amount_payable,
    //                 'active'=>1,
    //                 'created_on'=>time(),
    //                 'is_a_back_dating_record' => $is_a_back_dating_record?1:0
    //             );
    //             if($statement_entry_id = $this->ci->statements_m->insert($input)){
    //                 return TRUE;
    //             }else{
    //                 //could not insert statement entry
    //                 return FALSE;
    //             }
    //         }else{
    //             //could not insert invoice entry
    //             return FALSE;
    //         } 
    //     }else{
    //         $this->ci->session->set_flashdata('error','Parameters missing for the function create_invoice');
    //         return FALSE;
    //     }
    // }

    public function create_invoice($type = 0,$group_id = 0,$member = array(),$contribution = array(),$invoice_date = 0,$due_date = 0,$amount_payable = 0,$description = '',$sms_template = '',$send_sms_notification = FALSE,$send_email_notification = FALSE,$is_a_back_dating_record = FALSE){
        $group = $this->ci->groups_m->get($group_id);
        if($group&&$type&&$group_id&&$member&&$contribution&&$invoice_date&&$due_date&&$amount_payable){
        // if($group&&$type&&$group_id&&$member&&$contribution&&$invoice_date&&$due_date&&$amount_payable&&$sms_template){
            $input = array(
                'type'=>1,
                'invoice_number'=>'',
                'user_id'=>$member->user_id,
                'member_id'=>$member->id,
                'contribution_id'=>$contribution->id,
                'group_id'=>$group_id,
                'invoice_date'=>$invoice_date,
                'due_date'=>$due_date,
                'amount_payable'=>$amount_payable,
                'description'=>$description,
                'active'=>1,
                'created_on'=>time(),
                'is_a_back_dating_record' => $is_a_back_dating_record?1:0
            );
            if($invoice_id = $this->ci->invoices_m->insert($input)){
                $input = array(
                    'transaction_type'=>1,
                    'transaction_date'=>$invoice_date,
                    'contribution_invoice_due_date'=>$due_date,
                    'contribution_id'=>$contribution->id,
                    'user_id'=>$member->user_id,
                    'member_id'=>$member->id,
                    'group_id'=>$group_id,
                    'invoice_id'=>$invoice_id,
                    'amount'=>$amount_payable,
                    'active'=>1,
                    'created_on'=>time(),
                    'is_a_back_dating_record' => $is_a_back_dating_record?1:0
                );
                if($statement_entry_id = $this->ci->statements_m->insert($input)){
                    return TRUE;
                }else{
                    //could not insert statement entry
                    return FALSE;
                }
            }else{
                //could not insert invoice entry
                return FALSE;
            } 
        }else{
            $this->ci->session->set_flashdata('error','Parameters missing for the function create_invoice');
            return FALSE;
        }
    }

    public function create_contribution_fine_invoice($type = 0,$group_id = 0,$member = array(),$contribution = array(),$invoice_date = 0,$due_date = 0,$amount_payable = 0,$description = '',$sms_template = '',$send_sms_notification = FALSE,$send_email_notification = FALSE,$parent_id = 0){
        $group = $this->ci->groups_m->get($group_id);
        if($group&&$type&&$group_id&&$member&&$contribution&&$invoice_date&&$due_date&&$amount_payable){
            $input = array(
                'type'=>2,
                'invoice_number'=>'',
                'user_id'=>$member->user_id,
                'member_id'=>$member->id,
                'contribution_id'=>$contribution->id,
                'group_id'=>$group_id,
                'invoice_date'=>$invoice_date,
                'due_date'=>$due_date,
                'amount_payable'=>$amount_payable,
                'description'=>$description,
                'parent_id'=>$parent_id,
                'active'=>1,
                'created_on'=>time(),
            );
            if($invoice_id = $this->ci->invoices_m->insert($input)){
                $input = array(
                    'transaction_type'=>2,
                    'transaction_date'=>$invoice_date,
                    'fine_invoice_due_date'=>$due_date,
                    'contribution_id'=>$contribution->id,
                    'user_id'=>$member->user_id,
                    'member_id'=>$member->id,
                    'group_id'=>$group_id,
                    'invoice_id'=>$invoice_id,
                    'amount'=>$amount_payable,
                    'active'=>1,
                    'created_on'=>time(),
                );
                if($statement_entry_id = $this->ci->statements_m->insert($input)){  
                    return TRUE;
                }else{
                    //could not insert statement entry
                    return FALSE;
                }
            }else{
                //could not insert invoice entry
                return FALSE;
            } 
        }else{
            $this->ci->session->set_flashdata('error','Parameters missing for the function create_contribution_fine_invoice');
            return FALSE;
        }
    }

    public function create_fine_invoice($type = 0,$group_id = 0,$fine_date = 0,$member = array(),$fine_category_id = 0,$amount = 0,$send_sms_notification = FALSE,$send_email_notification = FALSE,$description = "",$is_a_back_dating_record = FALSE){
        $fine_category = $this->ci->fine_categories_m->get_group_fine_category($fine_category_id,$group_id);
        $group = $this->ci->groups_m->get($group_id);
        if($group&&$type&&$group_id&&$member&&$fine_category_id&&$amount&&$fine_category&&$fine_date){
            $input = array(
                'member_id'=>$member->id,
                'group_id'=>$group_id,
                'fine_category_id'=>$fine_category_id,
                'fine_date'=>$fine_date,
                'amount'=>$amount,
                'active'=>1,
                'created_on'=>time(),
                "is_a_back_dating_record" => $is_a_back_dating_record?1:0,
            );
            if($fine_id = $this->ci->fines_m->insert($input)){
                //$description = '';
                $input = array(
                    'type'=>3,
                    'invoice_number'=>'',
                    'user_id'=>$member->user_id,
                    'member_id'=>$member->id,
                    'fine_category_id'=>$fine_category_id,
                    'fine_id'=>$fine_id,
                    'group_id'=>$group_id,
                    'invoice_date'=>$fine_date,
                    'due_date'=>$fine_date,
                    'amount_payable'=>$amount,
                    'description'=>$description,
                    'active'=>1,
                    'created_on'=>time(),
                    "is_a_back_dating_record" => $is_a_back_dating_record?1:0,
                );
                if($invoice_id = $this->ci->invoices_m->insert($input)){
                    $input = array(
                        'transaction_type'=>3,
                        'transaction_date'=>$fine_date,
                        'fine_invoice_due_date'=>$fine_date,
                        'fine_id'=>$fine_id,
                        'fine_category_id'=>$fine_category_id,
                        'user_id'=>$member->user_id,
                        'member_id'=>$member->id,
                        'group_id'=>$group_id,
                        'invoice_id'=>$invoice_id,
                        'description'=>$description,
                        'amount'=>$amount,
                        'active'=>1,
                        'created_on'=>time(),
                        "is_a_back_dating_record" => $is_a_back_dating_record?1:0,
                    );

                    if($statement_entry_id = $this->ci->statements_m->insert($input)){
                        if($this->ci->transactions->reconcile_member_fine_statement(3,$group_id,$member->id,0,$fine_category_id,$fine_date)){
                            $total_outstanding_balance = $this->ci->statements_m->get_member_fine_balance($group_id,$member->id);
                            $group_currency = $this->currency_code_options[$group->currency_id];
                            $sms_data = array(
                                'FIRST_NAME' => $member->first_name,
                                'GROUP_CURRENCY' => $group_currency,
                                'AMOUNT' => number_to_currency($amount),
                                'FINE_CATEGORY' => $fine_category->name,
                                'FINE_DATE' => timestamp_to_date($fine_date,TRUE),
                                'FINE_BALANCE' => number_to_currency($total_outstanding_balance),
                                'GROUP_NAME' => $group->name,
                            );
                            $email_data = array(
                                'DATE' => date('d',$fine_date),
                                'MONTH' => date('M',$fine_date),
                                'FIRST_NAME' => $member->first_name,
                                'LAST_NAME' => $member->last_name,
                                'GROUP_CURRENCY' => $group_currency,
                                'AMOUNT' => number_to_currency($amount),
                                'FINE_BALANCE' => number_to_currency($total_outstanding_balance),
                                'FINE_DATE' => timestamp_to_date($fine_date),
                                'FINE_CATEGORY' => $fine_category->name,
                                'LINK' => $this->application_settings->protocol.$group->slug.'.'.$this->application_settings->url.'/member/members/view/'.$member->id,
                                'GROUP_NAME' => $group->name,
                                'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
                                'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
                                'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
                            );
                            if($this->ci->messaging->send_fine_invoice_notification_to_member($group,$member,$send_sms_notification,$send_email_notification,$sms_data,$email_data,$amount)){
                                if($this->ci->notifications->create(
                                    'You have been fined for '.$fine_category->name,
                                    'You have been fined '.$group_currency.' '.number_to_currency($amount).' for '.$fine_category->name.' on '.timestamp_to_date($fine_date),
                                    $this->ci->ion_auth->get_user($member->user_id),
                                    $member->id,
                                    $member->user_id,
                                    $member->id,
                                    $group_id,
                                    'View Fine Invoice',
                                    'group/invoices/view/'.$invoice_id,
                                    4,
                                    $invoice_id
                                )){
                                    return TRUE;
                                }else{
                                    return FALSE;
                                }
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function create_miscellaneous_invoice($type = 0,$group_id = 0,$member = array(),$invoice_date = 0,$due_date = 0,$amount_payable = 0,$description = '',$sms_template = '',$send_sms_notification = FALSE,$send_email_notification = FALSE){
        $group = $this->ci->groups_m->get($group_id);
        if($type&&$group_id&&$member&&$invoice_date&&$due_date&&$amount_payable&&$description){
            $input = array(
                'type'=>4,
                'invoice_number'=>'',
                'user_id'=>$member->user_id,
                'member_id'=>$member->id,
                'contribution_id'=>0,
                'group_id'=>$group_id,
                'invoice_date'=>$invoice_date,
                'due_date'=>$due_date,
                'amount_payable'=>$amount_payable,
                'description'=>$description,
                'active'=>1,
                'created_on'=>time(),
            );
            if($invoice_id = $this->ci->invoices_m->insert($input)){
                $input = array(
                    'transaction_type'=>4,
                    'transaction_date'=>$invoice_date,
                    'contribution_id'=>0,
                    'user_id'=>$member->user_id,
                    'member_id'=>$member->id,
                    'group_id'=>$group_id,
                    'invoice_id'=>$invoice_id,
                    'description'=>$description,
                    'amount'=>$amount_payable,
                    'active'=>1,
                    'created_on'=>time(),
                );
                if($statement_entry_id = $this->ci->statements_m->insert($input)){    
                    if($this->ci->transactions->reconcile_member_miscellaneous_statement(4,$group_id,$member->id,$invoice_date)){
                        $miscellaneous_balance = $this->ci->statements_m->get_member_miscellaneous_balance($group_id,$member->id);
                        $group_currency = $this->currency_code_options[$group->currency_id];
                        $sms_data = array(
                            'FIRST_NAME' => $member->first_name,
                            'GROUP_CURRENCY' => $group_currency,
                            'AMOUNT' => number_to_currency($amount_payable),
                            'DESCRIPTION' => $description,
                            'MISCELLANEOUS_BALANCE' => number_to_currency($miscellaneous_balance),
                            'INVOICE_DATE' => $invoice_date,
                            'APPLICATION_NAME'=>$this->application_settings->application_name,
                            'GROUP_NAME' => $group->name,
                        );
                        $email_data = array(
                            'DATE' => date('d',$invoice_date),
                            'MONTH' => date('M',$invoice_date),
                            'FIRST_NAME' => $member->first_name,
                            'LAST_NAME' => $member->last_name,
                            'GROUP_NAME' => $group->name,
                            'GROUP_CURRENCY' => $group_currency,
                            'AMOUNT' => number_to_currency($amount_payable),
                            'DESCRIPTION' => $description,
                            'APPLICATION_NAME'=>$this->application_settings->application_name,
                            'INVOICE_DATE' => timestamp_to_date($invoice_date),
                            'DUE_DATE' => timestamp_to_date($due_date),
                            'MISCELLANEOUS_BALANCE' => number_to_currency($miscellaneous_balance),
                            'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
                            'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
                            'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'',
                            'LINK' => $this->application_settings->protocol.$group->slug.'.'.$this->application_settings->url.'/member/members/view/'.$member->id, 
                        );
                        if($this->ci->messaging->send_miscellaneous_invoice_notification_to_member($group,$member,$send_sms_notification,$send_email_notification,$sms_data,$email_data,$amount_payable)){
                            if($this->ci->notifications->create(
                                'You have been invoiced for '.$description,
                                'You have been invoiced '.$group_currency.' '.number_to_currency($amount_payable).' payable on '.timestamp_to_date($invoice_date).' for '.$description,
                                $this->ci->ion_auth->get_user($member->user_id),
                                $member->id,
                                $member->user_id,
                                $member->id,
                                $group_id,
                                'View Miscellaneous Invoice',
                                'group/invoices/view/'.$invoice_id,
                                7,
                                $invoice_id
                            )){
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    //could not insert statement entry
                    return FALSE;
                }
            }else{
                //could not insert invoice entry
                return FALSE;
            } 
        }else{
            $this->ci->session->set_flashdata('error','Parameters missing for the function create_invoice');
            return FALSE;
        }
    }
   
    // public function record_contribution_payment($group_id = 0,$deposit_date = 0,$member_id = 0,$contribution_id = 0,$account_id = 0,$deposit_method = 0,$description = '',$amount = 0,$send_sms_notification = FALSE,$send_email_notification = FALSE,$transaction_alert_id = 0,$is_a_back_dating_record = FALSE,$checkoff_id = 0){
    //     if(preg_match('/bank-/', $account_id)){
    //         $type = 1;
    //     }else if(preg_match('/sacco-/', $account_id)){
    //         $type = 2;
    //     }else if(preg_match('/mobile-/', $account_id)){
    //         $type = 3;
    //     }else if(preg_match('/petty-/', $account_id)){
    //         $type = 7;
    //     }else{
    //         $type = 0;
    //     }
        
    //     if(preg_match('/bank-/', $account_id)){
    //         $transaction_type = 9;
    //     }else if(preg_match('/sacco-/', $account_id)){
    //         $transaction_type = 10;
    //     }else if(preg_match('/mobile-/', $account_id)){
    //         $transaction_type = 11;
    //     }else if(preg_match('/petty-/', $account_id)){
    //         $transaction_type = 15;
    //     }else{
    //         $transaction_type = 0;
    //     }
    //     $member = $this->ci->members_m->get_group_member($member_id,$group_id);
    //     $group = $this->ci->groups_m->get($group_id);
    //     $contribution = $this->ci->contributions_m->get_group_contribution($contribution_id,$group_id);
    //     if($group&&$contribution&&$member&&$type&&$transaction_type&&$group_id&&$deposit_date&&$member_id&&$contribution_id&&$account_id&&$deposit_method&&$amount){
    //         $input = array(
    //             'type'=>$type,
    //             'group_id'=>$group_id,
    //             'deposit_date'=>$deposit_date,
    //             'member_id'=>$member_id,
    //             'contribution_id'=>$contribution_id,
    //             'account_id'=>$account_id,
    //             'deposit_method'=>$deposit_method,
    //             'amount'=>$amount,
    //             'description'=>$description,
    //             'checkoff_id'=>$checkoff_id,
    //             'transaction_alert_id'=>$transaction_alert_id,
    //             'active'=>1,
    //             'created_on'=>time(),
    //             'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
    //         );

    //         print_r($input);die;
    //         if($deposit_id = $this->ci->deposits_m->insert($input)){
    //             $input = array(
    //                 'transaction_type'=>$transaction_type,
    //                 'group_id'=>$group_id,
    //                 'transaction_date'=>$deposit_date,
    //                 'member_id'=>$member_id,
    //                 'deposit_id'=>$deposit_id,
    //                 'user_id'=>$member->user_id,
    //                 'account_id'=>$account_id,
    //                 'contribution_id'=>$contribution_id,
    //                 'description'=>$deposit_method.' - '.$description,
    //                 'amount'=>$amount,
    //                 'checkoff_id'=>$checkoff_id,
    //                 'active'=>1,
    //                 'created_on'=>time(),
    //                 'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
    //             );
    //             if($statement_entry_id = $this->ci->statements_m->insert($input)){
    //                 if($this->reconcile_member_statement($transaction_type,$group_id,$member_id,$contribution_id,$deposit_date)){
    //                     if(preg_match('/bank-/', $account_id)){
    //                         $deposit_transaction_type = 1;
    //                     }else if(preg_match('/sacco-/', $account_id)){
    //                         $deposit_transaction_type = 2;
    //                     }else if(preg_match('/mobile-/', $account_id)){
    //                         $deposit_transaction_type = 3;
    //                     }else if(preg_match('/petty-/', $account_id)){
    //                         $deposit_transaction_type = 7;
    //                     }else{
    //                         $deposit_transaction_type = 0;
    //                     }

    //                     if($this->deposit($group_id,$deposit_id,$deposit_transaction_type,$deposit_date,$account_id,$amount,$member_id,$contribution_id,'','',$description,'','','','','','','','','','',$transaction_alert_id,$is_a_back_dating_record,'','','',$checkoff_id)){    
    //                         $sms_data = array(
    //                             'FIRST_NAME' => $member->first_name,
    //                             'GROUP_CURRENCY' => $this->currency_code_options[$group->currency_id],
    //                             'AMOUNT' => number_to_currency($amount),
    //                             'CONTRIBUTION_NAME' => $contribution->name,
    //                             'APPLICATION_NAME'=>$this->application_settings->application_name,
    //                             'DEPOSIT_DATE' => timestamp_to_date($deposit_date,TRUE),
    //                             'GROUP_NAME' => $group->name,
    //                         );
    //                         $email_data = array(
    //                             'DATE' => date('d',$deposit_date),
    //                             'MONTH' => date('M',$deposit_date),
    //                             'FIRST_NAME' => $member->first_name,
    //                             'GROUP_NAME' => $group->name,
    //                             'LAST_NAME' => $member->last_name,
    //                             'GROUP_CURRENCY' => $this->currency_code_options[$group->currency_id],
    //                             'AMOUNT' => number_to_currency($amount),
    //                             'APPLICATION_NAME'=>$this->application_settings->application_name,
    //                             'CONTRIBUTION_NAME' => $contribution->name,
    //                             'DEPOSIT_DATE' => timestamp_to_date($deposit_date),
    //                             'LINK' => $this->application_settings->protocol.$group->slug.'.'.$this->application_settings->url.'/group/members/view/'.$member->id,
    //                             'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
    //                             'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
    //                             'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
    //                         );   
    //                         $currency = $this->currency_code_options[$group->currency_id];
    //                         $contribution = $this->ci->contributions_m->get_group_contribution($contribution_id,$group->id);
    //                         $payment_method = $this->deposit_method_options[$deposit_method];
    //                         if($this->ci->messaging->send_contribution_payment_notification_to_member($group,$member,$amount,$contribution,$currency,$payment_method)){
    //                             return TRUE;
    //                         }else{
    //                             return FALSE;
    //                         }
    //                     }else{
    //                         return FALSE;
    //                     }
    //                 }else{                        
    //                     return FALSE;
    //                 }
    //             }else{
    //                 return FALSE;
    //             }
    //         }else{
    //             return FALSE;
    //         }
    //     }else{
    //         return FALSE;
    //     }
    // }

    public function record_contribution_payment($group_id = 0,$deposit_date = 0,$member_id = 0,$contribution_id = 0,$account_id = 0,$deposit_method = 0,$description = '',$amount = 0,$send_sms_notification = FALSE,$send_email_notification = FALSE,$transaction_alert_id = 0,$is_a_back_dating_record = FALSE,$transaction_alert=array()){
        if(preg_match('/bank-/', $account_id)){
            $type = 1;
        }else if(preg_match('/sacco-/', $account_id)){
            $type = 2;
        }else if(preg_match('/mobile-/', $account_id)){
            $type = 3;
        }else if(preg_match('/petty-/', $account_id)){
            $type = 7;
        }else{
            $type = 0;
        }        
        if(preg_match('/bank-/', $account_id)){
            $transaction_type = 9;
        }else if(preg_match('/sacco-/', $account_id)){
            $transaction_type = 10;
        }else if(preg_match('/mobile-/', $account_id)){
            $transaction_type = 11;
        }else if(preg_match('/petty-/', $account_id)){
            $transaction_type = 15;
        }else{
            $transaction_type = 0;
        }

        if($deposit_date > time()){
            $deposit_date = $transaction_alert->created_on;
        }


        $member = $this->ci->members_m->get_group_member($member_id,$group_id);
        $group = $this->ci->groups_m->get($group_id);
        $contribution = $this->ci->contributions_m->get_group_contribution($contribution_id,$group_id);
        if($group&&$contribution&&$member&&$type&&$transaction_type&&$group_id&&$deposit_date&&$member_id&&$contribution_id&&$account_id&&$deposit_method&&$amount){
            $input = array(
                'type'=>$type,
                'group_id'=>$group_id,
                'deposit_date'=>$deposit_date,
                'member_id'=>$member_id,
                'contribution_id'=>$contribution_id,
                'account_id'=>$account_id,
                'deposit_method'=>$deposit_method,
                'amount'=>$amount,
                'description'=>$description,
                'transaction_alert_id'=>$transaction_alert_id,
                'active'=>1,
                'created_on'=>time(),
                'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
            );
            if($deposit_id = $this->ci->deposits_m->insert($input)){
                $input = array(
                    'transaction_type'=>$transaction_type,
                    'group_id'=>$group_id,
                    'transaction_date'=>$deposit_date,
                    'member_id'=>$member_id,
                    'deposit_id'=>$deposit_id,
                    'user_id'=>$member->user_id,
                    'account_id'=>$account_id,
                    'contribution_id'=>$contribution_id,
                    'description'=>$deposit_method.' - '.$description,
                    'amount'=>$amount,
                    'active'=>1,
                    'created_on'=>time(),
                    'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                );
                if($statement_entry_id = $this->ci->statements_m->insert($input)){
                    if($this->update_group_member_contribution_statement_balances(array($group_id),array($member_id),$deposit_date)){
                        if(preg_match('/bank-/', $account_id)){
                            $deposit_transaction_type = 1;
                        }else if(preg_match('/sacco-/', $account_id)){
                            $deposit_transaction_type = 2;
                        }else if(preg_match('/mobile-/', $account_id)){
                            $deposit_transaction_type = 3;
                        }else if(preg_match('/petty-/', $account_id)){
                            $deposit_transaction_type = 7;
                        }else{
                            $deposit_transaction_type = 0;
                        }

                        if($this->deposit($group_id,$deposit_id,$deposit_transaction_type,$deposit_date,$account_id,$amount,$member_id,$contribution_id,'','',$description,'','','','','','','','','','',$transaction_alert_id,$is_a_back_dating_record)){   
                            $currency = $this->currency_code_options[$group->currency_id];
                            $payment_method = $this->deposit_method_options[$deposit_method];
                            if($this->ci->messaging->send_contribution_payment_notification_to_member($group,$member,$send_sms_notification,$send_email_notification,$amount,$contribution,$currency,$payment_method,$deposit_date)){
                                return TRUE;
                            }else{
                                $this->ci->session->set_flashdata('error','messaging has some errors');
                                return FALSE;
                            }
                        }else{
                            $this->ci->session->set_flashdata('error','Could not make deposit summary');
                            return FALSE;
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Could not reconcile member statement');
                        return FALSE;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Could not make a statement entry');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Could not create a deposit');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Some values are missing');
            return FALSE;
        }
    }

    // public function record_group_contribution_payments($group_id = 0,$contribution_payments = array()){
    //     if($group_id&&$contribution_payments){
    //         $group_ids = array($group_id);
    //         $member_ids = array();
    //         $contribution_ids = array();
    //         foreach($contribution_payments as $contribution_payment):
    //             if(in_array($contribution_payment->member_id,$member_ids)){

    //             }else{
    //                 $member_ids[] = $contribution_payment->member_id;
    //             }
    //             if(in_array($contribution_payment->contribution_id,$contribution_ids)){

    //             }else{
    //                 $contribution_ids[] = $contribution_payment->contribution_id;
    //             }
    //         endforeach;
    //         $member_objects_array = $this->ci->members_m->get_group_member_objects_array($group_ids,$member_ids);
    //         $group = $this->ci->groups_m->get($group_id);
    //         $contribution_objects_array = $this->ci->contributions_m->get_group_contribution_objects_array($group_ids,$contribution_ids);
    //         $result = TRUE;
    //         $deposits = array();
    //         foreach($contribution_payments as $contribution_payment):
    //             if(isset($contribution_payment->deposit_date)&&isset($contribution_payment->member_id)&&isset($contribution_payment->contribution_id)&&isset($contribution_payment->account_id)&&isset($contribution_payment->amount)&&isset($contribution_payment->deposit_method)){
    //                $checkoff_id = isset($contribution_payment->checkoff_id)?$contribution_payment->checkoff_id:'';
    //                 if(preg_match('/bank-/', $contribution_payment->account_id)){
    //                     $type = 1;
    //                 }else if(preg_match('/sacco-/', $contribution_payment->account_id)){
    //                     $type = 2;
    //                 }else if(preg_match('/mobile-/', $contribution_payment->account_id)){
    //                     $type = 3;
    //                 }else if(preg_match('/petty-/', $contribution_payment->account_id)){
    //                     $type = 7;
    //                 }else{
    //                     $type = 0;
    //                 }

    //                 if(preg_match('/bank-/', $contribution_payment->account_id)){
    //                     $transaction_type = 9;
    //                 }else if(preg_match('/sacco-/', $contribution_payment->account_id)){
    //                     $transaction_type = 10;
    //                 }else if(preg_match('/mobile-/', $contribution_payment->account_id)){
    //                     $transaction_type = 11;
    //                 }else if(preg_match('/petty-/', $contribution_payment->account_id)){
    //                     $transaction_type = 15;
    //                 }else{
    //                     $transaction_type = 0;
    //                 }


    //                 $contribution = $contribution_objects_array[$contribution_payment->contribution_id];
    //                 $member = $member_objects_array[$contribution_payment->member_id];

    //                 if($group&&$contribution&&$member&&$type&&$transaction_type&&$group_id&&$contribution_payment->deposit_date&&$contribution_payment->member_id&&$contribution_payment->contribution_id&&$contribution_payment->account_id&&$contribution_payment->deposit_method&&$contribution_payment->amount){
    //                     $transaction_alert_id = isset($contribution_payment->transaction_alert_id)?$contribution_payment->transaction_alert_id:0;
    //                     $is_a_back_dating_record = isset($contribution_payment->is_a_back_dating_record)?1:0;

    //                     $input = array(
    //                         'type'=>$type,
    //                         'group_id'=>$group_id,
    //                         'deposit_date'=>$contribution_payment->deposit_date,
    //                         'member_id'=>$contribution_payment->member_id,
    //                         'contribution_id'=>$contribution_payment->contribution_id,
    //                         'account_id'=>$contribution_payment->account_id,
    //                         'deposit_method'=>$contribution_payment->deposit_method,
    //                         'amount'=>$contribution_payment->amount,
    //                         'description'=>$contribution_payment->description,
    //                         'checkoff_id'=>$checkoff_id,
    //                         'transaction_alert_id'=>$transaction_alert_id,
    //                         'active'=>1,
    //                         'created_on'=>time(),
    //                         'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
    //                     );

    //                     if($deposit_id = $this->ci->deposits_m->insert($input)){
    //                         $input = array(
    //                             'transaction_type'=>$transaction_type,
    //                             'group_id'=>$group_id,
    //                             'transaction_date'=>$contribution_payment->deposit_date,
    //                             'member_id'=>$contribution_payment->member_id,
    //                             'deposit_id'=>$deposit_id,
    //                             'checkoff_id'=>$checkoff_id,
    //                             'user_id'=>$member->user_id,
    //                             'account_id'=>$contribution_payment->account_id,
    //                             'contribution_id'=>$contribution_payment->contribution_id,
    //                             'description'=>$contribution_payment->deposit_method.' - '.$contribution_payment->description,
    //                             'amount'=>$contribution_payment->amount,
    //                             'active'=>1,
    //                             'created_on'=>time(),
    //                             'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
    //                         );


    //                         if($statement_entry_id = $this->ci->statements_m->insert($input)){

    //                             if(preg_match('/bank-/', $contribution_payment->account_id)){
    //                                 $deposit_transaction_type = 1;
    //                             }else if(preg_match('/sacco-/', $contribution_payment->account_id)){
    //                                 $deposit_transaction_type = 2;
    //                             }else if(preg_match('/mobile-/', $contribution_payment->account_id)){
    //                                 $deposit_transaction_type = 3;
    //                             }else if(preg_match('/petty-/', $contribution_payment->account_id)){
    //                                 $deposit_transaction_type = 7;
    //                             }else{
    //                                 $deposit_transaction_type = 0;
    //                             }

    //                             $deposit = new stdClass();

    //                             $deposit->group_id = $group_id;
    //                             $deposit->deposit_id = $deposit_id;
    //                             $deposit->checkoff_id = $checkoff_id;
    //                             $deposit->transaction_type = $deposit_transaction_type;
    //                             $deposit->transaction_date = $contribution_payment->deposit_date;
    //                             $deposit->account_id = $contribution_payment->account_id;
    //                             $deposit->amount = $contribution_payment->amount;
    //                             $deposit->member_id = $contribution_payment->member_id;
    //                             $deposit->contribution_id = $contribution_payment->contribution_id;
    //                             $deposit->fine_category_id = 0;
    //                             $deposit->income_category_id = 0;
    //                             $deposit->description = $contribution_payment->description ;
    //                             $deposit->depositor_id = 0;
    //                             $deposit->stock_sale_id = 0;
    //                             $deposit->money_market_investment_id = 0;
    //                             $deposit->asset_id = 0;
    //                             $deposit->bank_loan_id = 0;
    //                             $deposit->account_transfer_id = 0;
    //                             $deposit->from_account_id = 0;
    //                             $deposit->to_account_id = 0;
    //                             $deposit->loan_id = 0;
    //                             $deposit->loan_repayment_id = 0;
    //                             $deposit->transaction_alert_id = 0;
    //                             $deposit->is_a_back_dating_record = 0;
    //                             $deposit->debtor_loan_id = 0;
    //                             $deposit->debtor_id = 0;
    //                             $deposit->debtor_loan_repayment_id = 0;
    //                             $deposit->checkoff_id = $checkoff_id;
    //                             $deposits[] = $deposit;
    //                         }else{
    //                             $result = FALSE;
    //                             break;
    //                         }
    //                     }else{
    //                         $result = FALSE;
    //                         break;
    //                     }
    //                 }else{
    //                     $result = FALSE;
    //                     break;
    //                 }
    //             }else{
    //                 $result = FALSE;
    //                 break;
    //             }
    //         endforeach;
    //         if($result){
    //             if($this->bulk_deposit($group_id,$deposits)){
    //                 if($this->update_group_member_contribution_statement_balances($group_ids,$member_ids)){
    //                     if($this->send_group_contribution_payments_notifications($group_id,$contribution_payments)){
    //                         return TRUE;
    //                     }else{
    //                         return FALSE;
    //                     }
    //                 }else{
    //                     return FALSE;
    //                 }
    //             }else{
    //                 return FALSE;
    //             }
    //         }else{
    //             return FALSE;
    //         }
    //     }else{
    //         return FALSE;
    //     }
    // }

    public function record_group_contribution_payments($group_id = 0,$contribution_payments = array()){
        if($group_id&&$contribution_payments){
            $group_ids = array($group_id);
            $member_ids = array();
            $contribution_ids = array();
            foreach($contribution_payments as $contribution_payment):
                if(in_array($contribution_payment->member_id,$member_ids)){

                }else{
                    $member_ids[] = $contribution_payment->member_id;
                }
                if(in_array($contribution_payment->contribution_id,$contribution_ids)){

                }else{
                    $contribution_ids[] = $contribution_payment->contribution_id;
                }
            endforeach;
            $member_objects_array = $this->ci->members_m->get_group_member_objects_array($group_ids,$member_ids);
            $group = $this->ci->groups_m->get($group_id);
            $contribution_objects_array = $this->ci->contributions_m->get_group_contribution_objects_array($group_ids,$contribution_ids);
            $result = TRUE;
            $deposits = array();
            foreach($contribution_payments as $contribution_payment):
                if(isset($contribution_payment->deposit_date)&&isset($contribution_payment->member_id)&&isset($contribution_payment->contribution_id)&&isset($contribution_payment->account_id)&&isset($contribution_payment->amount)&&isset($contribution_payment->deposit_method)){

                    if(preg_match('/bank-/', $contribution_payment->account_id)){
                        $type = 1;
                    }else if(preg_match('/sacco-/', $contribution_payment->account_id)){
                        $type = 2;
                    }else if(preg_match('/mobile-/', $contribution_payment->account_id)){
                        $type = 3;
                    }else if(preg_match('/petty-/', $contribution_payment->account_id)){
                        $type = 7;
                    }else{
                        $type = 0;
                    }

                    if(preg_match('/bank-/', $contribution_payment->account_id)){
                        $transaction_type = 9;
                    }else if(preg_match('/sacco-/', $contribution_payment->account_id)){
                        $transaction_type = 10;
                    }else if(preg_match('/mobile-/', $contribution_payment->account_id)){
                        $transaction_type = 11;
                    }else if(preg_match('/petty-/', $contribution_payment->account_id)){
                        $transaction_type = 15;
                    }else{
                        $transaction_type = 0;
                    }


                    $contribution = $contribution_objects_array[$contribution_payment->contribution_id];
                    $member = $member_objects_array[$contribution_payment->member_id];
                    if($group&&$contribution&&$member&&$type&&$transaction_type&&$group_id&&$contribution_payment->deposit_date&&$contribution_payment->member_id&&$contribution_payment->contribution_id&&$contribution_payment->account_id&&$contribution_payment->deposit_method&&$contribution_payment->amount){

                        $created_by = isset($contribution_payment->created_by)?$contribution_payment->created_by:0;
                        $transaction_alert_id = isset($contribution_payment->transaction_alert_id)?$contribution_payment->transaction_alert_id:0;
                        $is_a_back_dating_record = isset($contribution_payment->is_a_back_dating_record)?1:0;
                        $checkoff_id = isset($contribution_payment->checkoff_id)?$contribution_payment->checkoff_id:0;

                        $input = array(
                            'type'=>$type,
                            'group_id'=>$group_id,
                            'deposit_date'=>$contribution_payment->deposit_date,
                            'member_id'=>$contribution_payment->member_id,
                            'contribution_id'=>$contribution_payment->contribution_id,
                            'account_id'=>$contribution_payment->account_id,
                            'deposit_method'=>$contribution_payment->deposit_method,
                            'amount'=>$contribution_payment->amount,
                            'description'=>$contribution_payment->description,
                            'transaction_alert_id'=>$transaction_alert_id,
                            'active'=>1,
                            'created_on'=>time(),
                            'created_by' => $created_by,
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                            'checkoff_id' => $checkoff_id,
                        );

                        if($deposit_id = $this->ci->deposits_m->insert($input)){
                            $input = array(
                                'transaction_type'=>$transaction_type,
                                'group_id'=>$group_id,
                                'transaction_date'=>$contribution_payment->deposit_date,
                                'member_id'=>$contribution_payment->member_id,
                                'deposit_id'=>$deposit_id,
                                'user_id'=>$member->user_id,
                                'account_id'=>$contribution_payment->account_id,
                                'contribution_id'=>$contribution_payment->contribution_id,
                                'description'=>$contribution_payment->deposit_method.' - '.$contribution_payment->description,
                                'amount'=>$contribution_payment->amount,
                                'active'=>1,
                                'created_on'=>time(),
                                'created_by' => $created_by,
                                'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                                'checkoff_id' => $checkoff_id,
                            );


                            if($statement_entry_id = $this->ci->statements_m->insert($input)){

                                if(preg_match('/bank-/', $contribution_payment->account_id)){
                                    $deposit_transaction_type = 1;
                                }else if(preg_match('/sacco-/', $contribution_payment->account_id)){
                                    $deposit_transaction_type = 2;
                                }else if(preg_match('/mobile-/', $contribution_payment->account_id)){
                                    $deposit_transaction_type = 3;
                                }else if(preg_match('/petty-/', $contribution_payment->account_id)){
                                    $deposit_transaction_type = 7;
                                }else{
                                    $deposit_transaction_type = 0;
                                }

                                $deposit = new stdClass();

                                $deposit->group_id = $group_id;
                                $deposit->deposit_id = $deposit_id;
                                $deposit->transaction_type = $deposit_transaction_type;
                                $deposit->transaction_date = $contribution_payment->deposit_date;
                                $deposit->account_id = $contribution_payment->account_id;
                                $deposit->amount = $contribution_payment->amount;
                                $deposit->member_id = $contribution_payment->member_id;
                                $deposit->contribution_id = $contribution_payment->contribution_id;
                                $deposit->fine_category_id = 0;
                                $deposit->income_category_id = 0;
                                $deposit->description = $contribution_payment->description ;
                                $deposit->depositor_id = 0;
                                $deposit->stock_sale_id = 0;
                                $deposit->money_market_investment_id = 0;
                                $deposit->asset_id = 0;
                                $deposit->bank_loan_id = 0;
                                $deposit->account_transfer_id = 0;
                                $deposit->from_account_id = 0;
                                $deposit->to_account_id = 0;
                                $deposit->loan_id = 0;
                                $deposit->loan_repayment_id = 0;
                                $deposit->transaction_alert_id = 0;
                                $deposit->is_a_back_dating_record = 0;
                                $deposit->debtor_loan_id = 0;
                                $deposit->debtor_id = 0;
                                $deposit->debtor_loan_repayment_id = 0;
                                $deposit->created_by = $created_by;
                                $deposits[] = $deposit;

                            }else{
                                $result = FALSE;
                                break;
                            }
                        }else{
                            $result = FALSE;
                            break;
                        }
                    }else{
                        $result = FALSE;
                        break;
                    }
                }else{
                    $result = FALSE;
                    break;
                }
            endforeach;
            if($result){
                if($this->bulk_deposit($group_id,$deposits)){
                    //$date = time();
                    if($this->update_group_member_contribution_statement_balances($group_ids,$member_ids)){
                        if($this->send_group_contribution_payments_notifications($group_id,$contribution_payments)){
                            return TRUE;
                        }else{
                            $this->ci->session->set_flashdata('error','Could not send contribution payments notifications');
                            return FALSE;
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Could not update group member contribution statement balances');
                        return FALSE;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Could not make a bulk deposit');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Result was false');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Missing parameters');
            return FALSE;
        }
    }

    function send_group_contribution_payments_notifications($group_id = 0,$contribution_payments = array()){
        $notification_values_are_valid = FALSE;
        $group = $this->ci->groups_m->get($group_id);
        $members = $this->ci->members_m->get_group_members_array($group_id);
        $contribution_options = $this->ci->contributions_m->get_group_contribution_options($group_id);
        foreach ($contribution_payments as $key => $contribution_payment) {
            $contribution_payment = (object)$contribution_payment;
            $member = $members[$contribution_payment->member_id];
            $contribution = $this->ci->contributions_m->get_group_contribution($contribution_payment->contribution_id,$group->id);
            $currency = $this->currency_code_options[$group->currency_id];
            $payment_method = $this->deposit_method_options[$contribution_payment->deposit_method];
            $send_sms_notification = $contribution_payment->send_sms_notification;
            $send_email_notification = $contribution_payment->send_email_notification;
            if($this->ci->messaging->send_contribution_payment_notification_to_member($group,$member,$send_sms_notification,$send_email_notification,$contribution_payment->amount,$contribution,$currency,$payment_method,$contribution_payment->deposit_date)){
                return TRUE;
            }else{
                return FALSE;
            }
        }
        if($notification_values_are_valid){
            if($this->ci->notifications->create_bulk($notifications)){
                return TRUE;
            }else{
               return FALSE;
            }
        }else{
            return FALSE;
        }
        
    }
    public function record_fine_payment($group_id = 0,$deposit_date = 0,$member_id = 0,$fine_category = 0,$account_id = 0,$deposit_method = 0,$description = '',$amount = 0,$send_sms_notification = FALSE,$send_email_notification = FALSE,$transaction_alert_id = 0,$is_a_back_dating_record = FALSE){
        if(preg_match('/bank-/', $account_id)){
            $type = 4;
        }else if(preg_match('/sacco-/', $account_id)){
            $type = 5;
        }else if(preg_match('/mobile-/', $account_id)){
            $type = 6;
        }else if(preg_match('/petty-/', $account_id)){
            $type = 8;
        }else{
            $type = 0;
        }
        
        if(preg_match('/bank-/', $account_id)){
            $transaction_type = 12;
        }else if(preg_match('/sacco-/', $account_id)){
            $transaction_type = 13;
        }else if(preg_match('/mobile-/', $account_id)){
            $transaction_type = 14;
        }else if(preg_match('/petty-/', $account_id)){
            $transaction_type = 16;
        }else{
            $transaction_type = 0;
        }

        if(preg_match('/contribution-/', $fine_category)){
            $contribution_id = str_replace('contribution-','',$fine_category);
            $fine_category_id = 0;
        }else if(preg_match('/fine_category-/', $fine_category)){
            $fine_category_id = str_replace('fine_category-','',$fine_category);
            $contribution_id = 0;
        }else{
            $fine_category_id = 0;
            $contribution_id = 0;
        }


        $member = $this->ci->members_m->get_group_member($member_id,$group_id);
        $group = $this->ci->groups_m->get($group_id);
        if($group&&$member&&$type&&$transaction_type&&$group_id&&$deposit_date&&$member_id&&($contribution_id||$fine_category_id)&&$account_id&&$deposit_method&&$amount){
            $input = array(
                'type'=>$type,
                'group_id'=>$group_id,
                'deposit_date'=>$deposit_date,
                'member_id'=>$member_id,
                'contribution_id'=>$contribution_id,
                'fine_category_id'=>$fine_category_id,
                'account_id'=>$account_id,
                'deposit_method'=>$deposit_method,
                'amount'=>$amount,
                'description'=>$description,
                'transaction_alert_id'=>$transaction_alert_id,
                'active'=>1,
                'created_on'=>time(),
                'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
            );
            if($deposit_id = $this->ci->deposits_m->insert($input)){
                $input = array(
                    'transaction_type'=>$transaction_type,
                    'group_id'=>$group_id,
                    'transaction_date'=>$deposit_date,
                    'deposit_id'=>$deposit_id,
                    'member_id'=>$member_id,
                    'user_id'=>$member->user_id,
                    'account_id'=>$account_id,
                    'fine_category_id'=>$fine_category_id,
                    'contribution_id'=>$contribution_id,
                    'description'=>$deposit_method.' - '.$description,
                    'amount'=>$amount,
                    'active'=>1,
                    'created_on'=>time(),
                    'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                );
                if($statement_entry_id = $this->ci->statements_m->insert($input)){
                    if($this->reconcile_member_fine_statement($transaction_type,$group_id,$member_id,$contribution_id,$fine_category_id,$deposit_date)){
                        
                        if(preg_match('/bank-/', $account_id)){
                            $deposit_transaction_type = 4;
                        }else if(preg_match('/sacco-/', $account_id)){
                            $deposit_transaction_type = 5;
                        }else if(preg_match('/mobile-/', $account_id)){
                            $deposit_transaction_type = 6;
                        }else if(preg_match('/petty-/', $account_id)){
                            $deposit_transaction_type = 8;
                        }else{
                            $deposit_transaction_type = 0;
                        }
                        if($this->deposit($group_id,$deposit_id,$deposit_transaction_type,$deposit_date,$account_id,$amount,$member_id,$contribution_id,$fine_category_id,'',$description,'','','','','','','','','','',$transaction_alert_id,$is_a_back_dating_record)){    
                            $fine_balance = $this->ci->statements_m->get_member_fine_balance($group_id,$member->id,$contribution_id);
                            $total_outstanding_balance = $this->ci->statements_m->get_member_fine_balance($group_id,$member->id);
                            $sms_data = array(
                                'FIRST_NAME' => $member->first_name,
                                'GROUP_CURRENCY' => $this->currency_code_options[$group->currency_id],
                                'AMOUNT' => number_to_currency($amount),
                                'FINE_BALANCE' => $fine_balance,
                                'TOTAL_OUTSTANDING_BALANCE' => $total_outstanding_balance,
                                'APPLICATION_NAME'=>$this->application_settings->application_name,
                                'DEPOSIT_DATE' => timestamp_to_date($deposit_date,TRUE),
                                'GROUP_NAME' => $group->name,
                            );
                            
                            $email_data = array(
                                'APPLICATION_NAME'  =>  $this->application_settings->application_name,
                                'LOGO' => $this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'',
                                'GROUP_NAME' => $group->name,
                                'FIRST_NAME' => $member->first_name,
                                'LAST_NAME' => $member->last_name,
                                'GROUP_CURRENCY' => $this->currency_code_options[$group->currency_id],
                                'AMOUNT' => number_to_currency($amount),
                                'DEPOSIT_DATE' => timestamp_to_date($deposit_date),
                                'FINE_BALANCE' => number_to_currency($fine_balance),
                                'DATE' => date('d',$deposit_date),
                                'MONTH' => date('M',$deposit_date),
                                'TOTAL_OUTSTANDING_BALANCE'=>number_to_currency($total_outstanding_balance),
                                'LINK' => site_url(),
                                'YEAR' => date('Y',time()),                                
                                // 'LINK' => $this->application_settings->protocol.$group->slug.'.'.$this->application_settings->url.'/group/members/view/'.$member->id,
                                'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
                                'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
                                'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
                            );
                            if($this->ci->messaging->send_fine_payment_notification_to_member($group,$member,$send_sms_notification,$send_email_notification,$sms_data,$email_data,$amount)){
                                if($this->ci->notifications->create(
                                    'Your contribution payment has been recorded.',
                                    'Your contribution payment of '.$this->currency_code_options[$group->currency_id].' '.number_to_currency($amount).' made on '.timestamp_to_date($deposit_date).' has been recorded.',
                                    $this->ci->ion_auth->get_user($member->user_id),
                                    $member->id,
                                    $member->user_id,
                                    $member->id,
                                    $group_id,
                                    'View Statement',
                                    'group/statements/view/'.$member->id,
                                    5,
                                    0,
                                    $deposit_id
                                )){
                                    return TRUE;
                                }else{
                                    return FALSE;
                                }
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Error occured adding deposit');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Error occured adding deposit');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Some fields are missing');
            return FALSE;
        }
    }

    public function record_miscellaneous_payment($group_id = 0,$deposit_date = 0,$member_id = 0,$account_id = 0,$deposit_method = 0,$description = '',$amount = 0,$send_sms_notification = FALSE,$send_email_notification = FALSE,$transaction_alert_id = 0){
        if(preg_match('/bank-/', $account_id)){
            $type = 9;
        }else if(preg_match('/sacco-/', $account_id)){
            $type = 10;
        }else if(preg_match('/mobile-/', $account_id)){
            $type = 11;
        }else if(preg_match('/petty-/', $account_id)){
            $type = 12;
        }else{
            $type = 0;
        }
        
        if(preg_match('/bank-/', $account_id)){
            $transaction_type = 17;
        }else if(preg_match('/sacco-/', $account_id)){
            $transaction_type = 18;
        }else if(preg_match('/mobile-/', $account_id)){
            $transaction_type = 19;
        }else if(preg_match('/petty-/', $account_id)){
            $transaction_type = 20;
        }else{
            $transaction_type = 0;
        }
        $group = $this->ci->groups_m->get($group_id);
        $member = $this->ci->members_m->get_group_member($member_id,$group_id);
        if($group&&$member&&$type&&$transaction_type&&$group_id&&$deposit_date&&$member_id&&$description&&$account_id&&$deposit_method&&$amount){
            $input = array(
                'type'=>$type,
                'group_id'=>$group_id,
                'deposit_date'=>$deposit_date,
                'member_id'=>$member_id,
                'contribution_id'=>0,
                'fine_category_id'=>0,
                'account_id'=>$account_id,
                'deposit_method'=>$deposit_method,
                'amount'=>$amount,
                'description'=>$description,
                'transaction_alert_id'=>$transaction_alert_id,
                'active'=>1,
                'created_on'=>time(),
            );
            if($deposit_id = $this->ci->deposits_m->insert($input)){
                $input = array(
                    'transaction_type'=>$transaction_type,
                    'group_id'=>$group_id,
                    'transaction_date'=>$deposit_date,
                    'deposit_id'=>$deposit_id,
                    'member_id'=>$member_id,
                    'user_id'=>$member->user_id,
                    'account_id'=>$account_id,
                    'fine_category_id'=>0,
                    'contribution_id'=>0,
                    'description'=>$description,
                    'amount'=>$amount,
                    'active'=>1,
                    'created_on'=>time(),
                );
                if($statement_entry_id = $this->ci->statements_m->insert($input)){
                    if($this->reconcile_member_miscellaneous_statement($transaction_type,$group_id,$member_id,$deposit_date)){
                        
                        if(preg_match('/bank-/', $account_id)){
                            $deposit_transaction_type = 9;
                        }else if(preg_match('/sacco-/', $account_id)){
                            $deposit_transaction_type = 10;
                        }else if(preg_match('/mobile-/', $account_id)){
                            $deposit_transaction_type = 11;
                        }else if(preg_match('/petty-/', $account_id)){
                            $deposit_transaction_type = 12;
                        }else{
                            $deposit_transaction_type = 0;
                        }

                        if($this->deposit($group_id,$deposit_id,$deposit_transaction_type,$deposit_date,$account_id,$amount,$member_id,'','','',$description,'','','','','','','','','','',$transaction_alert_id)){      
                            $miscellaneous_balance = $this->ci->statements_m->get_member_miscellaneous_balance($group_id,$member_id);
                            $sms_data = array(
                                'FIRST_NAME' => $member->first_name,
                                'GROUP_CURRENCY' => $this->currency_code_options[$group->currency_id],
                                'AMOUNT' => number_to_currency($amount),
                                'DESCRIPTION' => $description,
                                'MISCELLANEUOS_BALANCE' => number_to_currency($miscellaneous_balance),
                                'DEPOSIT_DATE' => timestamp_to_date($deposit_date),
                                'GROUP_NAME' => $group->name,
                            );
                            $email_data = array(
                                'DATE' => date('d',$deposit_date),
                                'MONTH' => date('M',$deposit_date),
                                'FIRST_NAME' => $member->first_name,
                                'LAST_NAME' => $member->last_name,
                                'GROUP_CURRENCY' => $this->currency_code_options[$group->currency_id],
                                'GROUP_NAME' => $group->name,
                                'AMOUNT' => number_to_currency($amount),
                                'DESCRIPTION' => $description,
                                'MISCELLANEOUS_BALANCE' => number_to_currency($miscellaneous_balance),
                                'LINK' => $this->application_settings->protocol.$group->slug.'.'.$this->application_settings->url.'/member/members/view/'.$member->id,
                                'DEPOSIT_DATE' => timestamp_to_date($deposit_date),
                                'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
                                'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
                                'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
                            );
                            if($this->ci->messaging->send_miscellaneous_payment_notification_to_member($group,$member,$send_sms_notification,$send_email_notification,$sms_data,$email_data,$amount)){
                                if($this->ci->notifications->create(
                                    'Your miscellaneous payment for '.$description.' has been recorded.',
                                    'Your miscellaneous payment of '.$this->currency_code_options[$group->currency_id].' '.number_to_currency($amount).' made on '.timestamp_to_date($deposit_date).' for '.$description.' has been recorded.',
                                    $this->ci->ion_auth->get_user($member->user_id),
                                    $member->id,
                                    $member->user_id,
                                    $member->id,
                                    $group_id,
                                    'View Statement',
                                    'group/statements/view/'.$member->id,
                                    5,
                                    0,
                                    $deposit_id
                                )){
                                    return TRUE;
                                }else{
                                    return FALSE;
                                }
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function record_income_deposit($group_id = 0,$deposit_date = 0,$depositor_id = 0,$income_category_id = 0,$account_id = 0,$deposit_method = 0,$description = '',$amount = 0,$transaction_alert_id = 0,$is_a_back_dating_record = FALSE){
        if(preg_match('/bank-/', $account_id)){
            $type = 13;
        }else if(preg_match('/sacco-/', $account_id)){
            $type = 14;
        }else if(preg_match('/mobile-/', $account_id)){
            $type = 15;
        }else if(preg_match('/petty-/', $account_id)){
            $type = 16;
        }else{
            $type = 0;
        }
        $depositor = $this->ci->depositors_m->get_group_depositor($depositor_id,$group_id);
        if($depositor&&$type&&$group_id&&$deposit_date&&$depositor_id&&$account_id&&$deposit_method&&$amount&&$income_category_id){
            $input = array(
                'type'=>$type,
                'group_id'=>$group_id,
                'deposit_date'=>$deposit_date,
                'depositor_id'=>$depositor->id,
                'income_category_id'=>$income_category_id,
                'member_id'=>0,
                'contribution_id'=>0,
                'fine_category_id'=>0,
                'account_id'=>$account_id,
                'deposit_method'=>$deposit_method,
                'amount'=>$amount,
                'description'=>$description,
                'transaction_alert_id'=>$transaction_alert_id,
                'active'=>1,
                'created_on'=>time(),
                'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
            );
            if($deposit_id = $this->ci->deposits_m->insert($input)){
                if(preg_match('/bank-/', $account_id)){
                    $deposit_transaction_type = 13;
                }else if(preg_match('/sacco-/', $account_id)){
                    $deposit_transaction_type = 14;
                }else if(preg_match('/mobile-/', $account_id)){
                    $deposit_transaction_type = 15;
                }else if(preg_match('/petty-/', $account_id)){
                    $deposit_transaction_type = 16;
                }else{
                    $deposit_transaction_type = 0;
                }
                if($this->deposit($group_id,$deposit_id,$deposit_transaction_type,$deposit_date,$account_id,$amount,'','','',$income_category_id,$description,$depositor_id,'','','','','','','','','',$transaction_alert_id,$is_a_back_dating_record)){      
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Could not insert');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Missing details');
            return FALSE;
        }
    }

    public function record_loan_processing_income_deposit($group_id=0,$loan_id=0,$deposit_date=0,$member_id=0,$account_id=0,$deposit_method=0,$description='',$amount=0,$transaction_alert_id=0,$is_admin_loan_type = FALSE){
        if($group_id&&$loan_id&&$deposit_date&&$member_id&&$account_id){
            if(preg_match('/bank-/', $account_id)){
                $type = 41;
            }else if(preg_match('/sacco-/', $account_id)){
                $type = 42;
            }else if(preg_match('/mobile-/', $account_id)){
                $type = 43;
            }else if(preg_match('/petty-/', $account_id)){
                $type = 44;
            }else{
                $type = 0;
            }
            $transaction_type=$type;

            $member = $this->ci->members_m->get_group_member($member_id,$group_id);

            $input = array(
                'type'=>$type,
                'group_id'=>$group_id,
                'deposit_date'=>$deposit_date,
                'member_id'=>$member_id,
                'loan_id'=>$loan_id,
                'is_admin'=>$is_admin_loan_type?1:0,
                'fine_category_id'=>0,
                'account_id'=>$account_id,
                'deposit_method'=>$deposit_method,
                'amount'=>$amount,
                'description'=>$description,
                'transaction_alert_id'=>$transaction_alert_id,
                'active'=>1,
                'created_on'=>time(),
            );
            if($deposit_id = $this->ci->deposits_m->insert($input)){
                if($this->deposit($group_id,$deposit_id,$transaction_type,$deposit_date,$account_id,$amount,$member_id,'','','',$description,'','','','','','','','',$loan_id,'',$transaction_alert_id)){
                    return TRUE;
                }else{
                    return FALSE;
                }

            }else{
                $this->ci->session->set_flashdata('error','There was an error recording deposit');
                return FALSE;
            }

        }else{
            return FALSE;
        }
    }

    public function record_external_lending_processing_income_deposit($group_id=0,$debtor_loan_id=0,$deposit_date=0,$debtor_id=0,$account_id=0,$deposit_method=0,$description='',$amount=0,$transaction_alert_id=0){
        if($group_id&&$debtor_loan_id&&$deposit_date&&$debtor_id&&$account_id){
            if(preg_match('/bank-/', $account_id)){
                $type = 45;
            }else if(preg_match('/sacco-/', $account_id)){
                $type = 46;
            }else if(preg_match('/mobile-/', $account_id)){
                $type = 47;
            }else if(preg_match('/petty-/', $account_id)){
                $type = 48;
            }else{
                $type = 0;
            }
            $transaction_type=$type;

            //$member = $this->ci->members_m->get_group_member($member_id,$group_id);

            $input = array(
                'type'=>$type,
                'group_id'=>$group_id,
                'deposit_date'=>$deposit_date,
                'debtor_id'=>$debtor_id,
                'debtor_loan_id'=>$debtor_loan_id,
                'fine_category_id'=>0,
                'account_id'=>$account_id,
                'deposit_method'=>$deposit_method,
                'amount'=>$amount,
                'description'=>$description,
                'transaction_alert_id'=>$transaction_alert_id,
                'active'=>1,
                'created_on'=>time(),
            );
            if($deposit_id = $this->ci->deposits_m->insert($input)){
                if($this->deposit($group_id,$deposit_id,$transaction_type,$deposit_date,$account_id,$amount,'','','','',$description,'','','','','','','','','','',$transaction_alert_id,'',$debtor_loan_id,$debtor_id)){
                    return TRUE;
                }else{
                    return FALSE;
                }

            }else{
                $this->ci->session->set_flashdata('error','There was an error recording deposit');
                return FALSE;
            }

        }else{
            return FALSE;
        }
    }

    public function void_loan_processing_income($id=0,$deposit = array(),$loan_id=0,$void_loan=FALSE,$modified_by=array(),$group_id=0,$unreconcile_transaction_alerts = TRUE){
        if($id && $deposit && $deposit->active){
            $input = array(
                'active'=>0,
                'modified_on'=>time(),
                );
            if($void_loan==FALSE || $deposit->transaction_alert_id){
                if($result = $this->ci->deposits_m->update($id,$input)){
                    if($this->void_deposit($deposit->group_id,$deposit->id,$deposit->type,$deposit->deposit_date,$deposit->account_id,$deposit->amount)){
                        if($unreconcile_transaction_alerts){
                            if($deposit->transaction_alert_id){
                                $input = array(
                                    'reconciled'=>0,
                                    'modified_on'=>time()
                                );
                                if($this->ci->transaction_alerts_m->update($deposit->transaction_alert_id,$input)){
                                    return TRUE;
                                }else{
                                    return FALSE;
                                }
                            }else{
                                return TRUE;
                            }
                        }else{
                            return TRUE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return($this->ci->loan->void_loan($deposit->loan_id,$modified_by,$unreconcile_transaction_alerts));
            }
        }else if($loan_id){
            $deposit = $this->ci->deposits_m->get_deposit_for_loan_processing_by_loan_id($loan_id,$group_id);
            if($deposit && $deposit->active){
                return($this->void_loan_processing_income($deposit->id,$deposit,'','','',$group_id,$unreconcile_transaction_alerts));
            }else{
                return TRUE;
            }
        }else{
            return FALSE;
        }
    }

    function void_external_lending_loan_processing_income($id=0,$deposit = array(),$loan_id=0,$void_loan=FALSE,$modified_by=array(),$group_id=0){
        if($id && $deposit && $deposit->active){
            $input = array(
                'active'=>0,
                'modified_on'=>time(),
                );
            if($void_loan==FALSE){
                if($result = $this->ci->deposits_m->update($id,$input)){
                 if($this->void_deposit($deposit->group_id,$deposit->id,$deposit->type,$deposit->deposit_date,$deposit->account_id,$deposit->amount)){
                        if($deposit->transaction_alert_id){
                            $input = array(
                                'reconciled'=>0,
                                'modified_on'=>time()
                            );
                            if($this->ci->transaction_alerts_m->update($deposit->transaction_alert_id,$input)){
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return TRUE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return($this->ci->loan->void_loan($deposit->loan_id,$modified_by));
            }
        }else if($loan_id){
            $deposit = $this->ci->deposits_m->get_deposit_for_external_lending_loan_processing_by_debtor_loan_id($loan_id,$group_id);
            if($deposit && $deposit->active){
                return($this->void_external_lending_loan_processing_income($deposit->id,$deposit,'','','',$group_id));
            }else{
                return TRUE;
            }
        }else{
            return FALSE;
        }
    }

    public function record_contribution_refund($group_id = 0,$refund_date = 0,$member_id = 0,$account_id = 0,$contribution_id=0,$refund_method = 0,$description = '',$amount = 0,$created_by=0,$transaction_alert_id = 0,$is_a_back_dating_record = FALSE){
        if(preg_match('/bank-/', $account_id)){
            $type = 21;
        }else if(preg_match('/sacco-/', $account_id)){
            $type = 22;
        }else if(preg_match('/mobile-/', $account_id)){
            $type = 23;
        }else if(preg_match('/petty-/', $account_id)){
            $type = 24;
        }else{
            $type = 0;
        }
        if($group_id&&$member_id&&$amount&&$contribution_id&&$type&&$refund_method&&$account_id){
            $member = $this->ci->members_m->get($member_id);
            $id =$this->ci->contribution_refunds_m->insert(array(
                'member_id' => $member_id,
                'account_id' => $account_id,
                'refund_method' => $refund_method,
                'refund_date' => $refund_date,
                'amount' => $amount,
                'contribution_id' => $contribution_id,
                'description' => $description,
                'created_by' => $created_by,
                'group_id' => $group_id,
                'created_on' => time(),
                'active' => 1,
                'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
            ));
            if($id){
                $statement_id = $this->ci->statements_m->insert(array(
                    'transaction_type'  =>  $type,
                    'transaction_date'  =>  $refund_date,
                    'contribution_id'   =>  $contribution_id,
                    'refund_id'         =>  $id,
                    'user_id'           =>  $member->user_id,
                    'member_id'         =>  $member->id,
                    'group_id'          =>  $group_id,
                    'amount'            =>  $amount,
                    'active'            =>  1,
                    'created_on'        =>  time(),
                    'created_by'        =>  $created_by,
                    'description'       =>  $description,
                    'account_id'        =>  $account_id,
                    'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                ));
                if($statement_id){
                    if($this->reconcile_member_statement($type,$group_id,$member_id,$contribution_id,$refund_date)){
                        $input = array(
                            'type'=>$type,
                            'group_id' => $group_id,
                            'withdrawal_date' => $refund_date,
                            'withdrawal_method' => $refund_method,
                            'contribution_refund_id' => $id,
                            'member_id'  =>  $member->id,
                            'contribution_id'   =>  $contribution_id,
                            'account_id' => $account_id,
                            'amount' => $amount,
                            'transaction_alert_id' => $transaction_alert_id,
                            'description' => $description,
                            'active' => 1,
                            'created_on' => time(),
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                        );
                        if($withdrawal_id = $this->ci->withdrawals_m->insert($input)){
                            $transaction_type = $type;
                            if($this->withdrawal(
                                $group_id,
                                $withdrawal_id,
                                $transaction_type,
                                $refund_date,
                                $account_id,
                                $amount,
                                $description,
                                '',
                                '',
                                $id,
                                '',
                                '',
                                '',
                                '',
                                '',
                                $transaction_alert_id,$contribution_id,$member->id,'','','',$is_a_back_dating_record)){
                                $contribution = $this->ci->contributions_m->get_group_contribution($contribution_id,$group_id);
                                if($this->ci->notifications->create(
                                    'Ccontribution refund for '.$contribution->name,
                                    'You have been refunded '.number_to_currency($amount).'  for '.$contribution->name.'. The refund was made on '.timestamp_to_date($refund_date).' : '.$description.'.',
                                    $this->ci->ion_auth->get_user($member->user_id),
                                    $member->id,
                                    $member->user_id,
                                    $member->id,
                                    $group_id,
                                    'View Statement',
                                    'group/statements/view/'.$member->id,
                                    8,0,0,0,0,'',1,0,0,0,0,0,$withdrawal_id)){
                                    //return TRUE;
                                }else{
                                    //return FALSE;
                                }
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','unable to create the refund');
                        return FALSE;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Unable to create statement');
                    return FALSE;
                }
            }
        }else{
            $this->ci->session->set_flashdata('error','Some parameters are missing');
            return FALSE;
        }
    }

    public function void_contribution_refund($id = 0){
        if($id){
            $statement_entry = $this->ci->statements_m->get_statement_entry_by_refund_id($id);
            if($statement_entry){
                $void_id = $this->ci->contribution_refunds_m->update($id,array('active'=>0,'modified_on'=>time()));
                if($void_id){
                    $statement_id  = $this->ci->statements_m->update($statement_entry->id,array('active'=>0,'modified_on'=>time()));
                    if($statement_id){
                        if($this->reconcile_member_statement($statement_entry->transaction_type,$statement_entry->group_id,$statement_entry->member_id,$statement_entry->contribution_id,$statement_entry->transaction_date)){
                            $withdrawal = $this->ci->withdrawals_m->get_withdrawal_by_contribution_refund_id($id,$statement_entry->group_id);
                            if($withdrawal){
                                $input = array(
                                    'active' => 0,
                                    'modified_on'=> time(),
                                );
                                if($this->ci->withdrawals_m->update($withdrawal->id,$input)){
                                    if($this->void_withdrawal($withdrawal->group_id,$withdrawal->id,$withdrawal->type,$withdrawal->withdrawal_date,$withdrawal->account_id,$withdrawal->amount)){
                                        return TRUE;
                                    }else{
                                        $this->ci->session->set_flashdata('warning','Sorry, could not void withdrawal.');
                                        return FALSE;
                                    }
                                }else{
                                    $this->ci->session->set_flashdata('warning','Sorry, could not update withdrawal.');
                                    return FALSE;
                                }
                            }else{
                                $this->ci->session->set_flashdata('warning','Sorry, could not find withdrawal.');
                                return FALSE;
                            }
                        }else{
                            $this->ci->session->set_flashdata('warning','Sorry, unable to reconcile statement.');
                            return FALSE;
                        }
                    }else{
                        $this->ci->session->set_flashdata('warning','Sorry, unable to void the refund.');
                        return FALSE;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Sorry, unable to void the refund.');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Sorry, unable perform the refund.');
                return FALSE;
            }
            
        }else{
            $this->ci->session->set_flashdata('error','Some parameters are missing');
            return FALSE;
        }
    }

    public function record_dividend_withdrawal($group_id = 0,$withdrawal_date = 0,$member_id = 0,$withdrawal_method = 0,$account_id = 0,$description = '',$amount = 0,$transaction_alert_id = 0,$is_a_back_dating_record = FALSE){
        if(preg_match('/bank-/', $account_id)){
            $type = 37;
        }else if(preg_match('/sacco-/', $account_id)){
            $type = 38;
        }else if(preg_match('/mobile-/', $account_id)){
            $type = 39;
        }else if(preg_match('/petty-/', $account_id)){
            $type = 40;
        }else{
            $type = 0;
        }
        if($type&&$group_id&&$withdrawal_date&&$member_id&&$withdrawal_method&&$account_id&&$amount){
            $input = array(
                'type'=>$type,
                'group_id' => $group_id,
                'withdrawal_date' => $withdrawal_date,
                'member_id' => $member_id,
                'withdrawal_method' => $withdrawal_method,
                'account_id' => $account_id,
                'amount' => $amount,
                'description' => $description,
                'transaction_alert_id' => $transaction_alert_id,
                'active' => 1,
                'created_on' => time(),
                'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
            );
            if($withdrawal_id = $this->ci->withdrawals_m->insert($input)){
                $transaction_type = $type;
                if($this->withdrawal($group_id,$withdrawal_id,$transaction_type,$withdrawal_date,$account_id,$amount,$description,0,0,0,0,0,0,0,0,$transaction_alert_id,0,$member_id,0,0,0,$is_a_back_dating_record,0,0,0)){
                    return TRUE;
                }else{
                    $this->ci->session->set_flashdata('error','Failed to reconcile withdrawal');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Withdrawal recording failed');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Some files/parameters were not submited');
            return FALSE;
        }
    }

    public function void_dividend_withdrawal($id = 0,$withdrawal = array()){
        $input = array(
            'active' => 0,
            'modified_on' => time(),
        );
        if($this->ci->withdrawals_m->update($id,$input)){
            if($this->void_withdrawal($withdrawal->group_id,$withdrawal->id,$withdrawal->type,$withdrawal->withdrawal_date,$withdrawal->account_id,$withdrawal->amount)){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    // public function void_contribution_payment($id = 0,$deposit = array()){
    //     $input = array(
    //         'active'=>0,
    //         'modified_on'=>time(),
    //     );
    //     if($result = $this->ci->deposits_m->update($id,$input)){
    //         if($statement_entry = $this->ci->statements_m->get_statement_entry_by_deposit_id($id)){
                
    //             $input = array(
    //                 'active'=>0,
    //                 'modified_on'=>time(),
    //             );
    //             if($result = $this->ci->statements_m->update($statement_entry->id,$input)){
    //                 if($this->reconcile_member_statement($statement_entry->transaction_type,$statement_entry->group_id,$statement_entry->member_id,$statement_entry->contribution_id,$statement_entry->transaction_date)){
    //                     if($this->void_deposit($deposit->group_id,$deposit->id,$deposit->type,$deposit->deposit_date,$deposit->account_id,$deposit->amount)){
    //                         return TRUE;
    //                     }else{
    //                         $this->ci->session->set_flashdata('warning','Could not void deposit.');
    //                         return FALSE;
    //                     }
    //                 }else{
    //                     $this->ci->session->set_flashdata('warning','Could not reconcile member statement deposit.');
    //                     return FALSE;
    //                 }
    //             }else{
    //                 $this->ci->session->set_flashdata('warning','Could not update statement.');
    //                 return FALSE;
    //             }
    //         }else{
    //             $this->ci->session->set_flashdata('warning','Could not get statement entry by deposit id statement.');
    //             return FALSE;
    //         }
    //     }else{
    //         $this->ci->session->set_flashdata('warning','Could not update statement.');
    //         return FALSE;
    //     }
    // }

    public function void_contribution_payment($id = 0,$deposit = array()){
        $input = array(
            'active'=>0,
            'modified_on'=>time(),
        );
        if($result = $this->ci->deposits_m->update($id,$input)){
            if($statement_entry = $this->ci->statements_m->get_statement_entry_by_deposit_id($id,$deposit->group_id)){               
                $input = array(
                    'active'=>0,
                    'modified_on'=>time(),
                );
                if($result = $this->ci->statements_m->update_statement($statement_entry->id,$input,$statement_entry->group_id)){
                    if($this->reconcile_member_statement($statement_entry->transaction_type,$statement_entry->group_id,$statement_entry->member_id,$statement_entry->contribution_id,$statement_entry->transaction_date)){;
                        if($this->void_deposit($deposit->group_id,$deposit->id,$deposit->type,$deposit->deposit_date,$deposit->account_id,$deposit->amount)){
                            return TRUE;
                        }else{
                            $this->ci->session->set_flashdata('warning','Could not void deposit.');
                            return FALSE;
                        }
                    }else{
                        $this->ci->session->set_flashdata('warning','Could not reconcile member statement deposit.');
                        return FALSE;
                    }
                }else{
                    $this->ci->session->set_flashdata('warning','Could not update statement.');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('warning','Could not get statement entry by deposit id statement.');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('warning','Could not update statement.');
            return FALSE;
        }
    }

    public function void_fine_payment($id = 0,$deposit = array()){
        $input = array(
            'active'=>0,
            'modified_on'=>time(),
        );        
        if($result = $this->ci->deposits_m->update($id,$input)){
            if($statement_entry = $this->ci->statements_m->get_statement_entry_by_deposit_id($id)){
                $input = array(
                    'active'=>0,
                    'modified_on'=>time(),
                );
                if($result = $this->ci->statements_m->update($statement_entry->id,$input)){
                    if($this->reconcile_member_fine_statement($statement_entry->transaction_type,$statement_entry->group_id,$statement_entry->member_id,$statement_entry->contribution_id,$statement_entry->fine_category_id,$statement_entry->transaction_date)){
                        if($this->void_deposit($deposit->group_id,$deposit->id,$deposit->type,$deposit->deposit_date,$deposit->account_id,$deposit->amount)){
                            return TRUE;
                        }else{
                            $this->ci->session->set_flashdata('warning','Could not void deposit.');
                            return FALSE;
                        }
                    }else{   
                            $this->ci->session->set_flashdata('warning','Could not reconcile member fine statement.');

                        return FALSE;
                    }
                }else{
                            $this->ci->session->set_flashdata('warning','Could not update statement.');

                    return FALSE;
                }
            }else{
                            $this->ci->session->set_flashdata('warning','Could not statement by deposit id.');

                return FALSE;
            }
        }else{
                            $this->ci->session->set_flashdata('warning','Could not update  deposit.');

            return FALSE;
        }
    }

    public function void_miscellaneous_payment($id = 0,$deposit = array()){
        $input = array(
            'active'=>0,
            'modified_on'=>time(),
        );        
        if($result = $this->ci->deposits_m->update($id,$input)){
            if($statement_entry = $this->ci->statements_m->get_statement_entry_by_deposit_id($id)){
                $input = array(
                    'active'=>0,
                    'modified_on'=>time(),
                );
                if($result = $this->ci->statements_m->update($statement_entry->id,$input)){
                    if($this->reconcile_member_miscellaneous_statement($statement_entry->transaction_type,$statement_entry->group_id,$statement_entry->member_id,$statement_entry->transaction_date)){
                        if($this->void_deposit($deposit->group_id,$deposit->id,$deposit->type,$deposit->deposit_date,$deposit->account_id,$deposit->amount)){
                            return TRUE;
                        }else{
                            return FALSE;
                        }
                    }else{                        
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function void_income_deposit($id = 0,$deposit = array()){
        $input = array(
            'active'=>0,
            'modified_on'=>time(),
        );        
        if($result = $this->ci->deposits_m->update($id,$input)){
            if($this->void_deposit($deposit->group_id,$deposit->id,$deposit->type,$deposit->deposit_date,$deposit->account_id,$deposit->amount)){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    function void_loan_repayment_deposit($id=0,$deposit = array(),$loan_repayment_id=0,$group_id = 0,$unreconcile_transaction_alerts = TRUE,$update_loan_invoices = 2){
        
        if($id && $deposit && $deposit->active){
        //die("am in");

            $input = array(
                'active'=>0,
                'modified_on'=>time(),
            ); 
            
            if($result = $this->ci->deposits_m->update($id,$input)){
                
                if($this->ci->loan->void_loan_repayment($deposit->loan_repayment_id,'','','',$unreconcile_transaction_alerts,$update_loan_invoices)){
                     
                    if($this->void_deposit($deposit->group_id,$deposit->id,$deposit->type,$deposit->deposit_date,$deposit->account_id,$deposit->amount)){
                       
                        if($unreconcile_transaction_alerts){
                            if($deposit->transaction_alert_id){
                                $input = array(
                                    'reconciled' => 0,
                                    'modified_on' => time()
                                );
                                if($this->ci->transaction_alerts_m->update($deposit->transaction_alert_id,$input)){
                                    return TRUE;
                                }else{
                                    return FALSE;
                                }
                            }else{
                                return TRUE;
                            }
                        }else{
                            return TRUE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else if($loan_repayment_id){
            
            $deposit = $this->ci->deposits_m->get_deposit_by_loan_repayment_id($loan_repayment_id,$group_id);
         
            if($deposit && $deposit->active){
                return($this->void_loan_repayment_deposit($deposit->id,$deposit,'',$group_id,$unreconcile_transaction_alerts));
            }else{
                return TRUE;
            }
        }else{
            return FALSE;
        }
    }


    function void_external_loan_repayment_deposit($id=0,$deposit = array(),$loan_repayment_id=0){
        if($id && $deposit && $deposit->active){
            $input = array(
                'active'=>0,
                'modified_on'=>time(),
                ); 
            if($result = $this->ci->deposits_m->update($id,$input)){
                if($this->ci->loan->void_external_lending_loan_repayment($deposit->debtor_loan_repayment_id)){
                    if($this->void_deposit($deposit->group_id,$deposit->id,$deposit->type,$deposit->deposit_date,$deposit->account_id,$deposit->amount)){
                        if($deposit->transaction_alert_id){
                            $input = array(
                                'reconciled'=>0,
                                'modified_on'=>time()
                            );
                            if($this->ci->transaction_alerts_m->update($deposit->transaction_alert_id,$input)){
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return TRUE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else if($loan_repayment_id){
            $deposit = $this->ci->deposits_m->get_deposit_by_external_loan_repayment_id($loan_repayment_id);
            if($deposit && $deposit->active){
                return($this->void_external_loan_repayment_deposit($deposit->id,$deposit));
            }else{
                return TRUE;
            }
        }else{
            return FALSE;
        }
    }

    function void_loan($loan_id=0,$group_id=0){
        if($loan_id){
            $withdrawal = $this->ci->withdrawals_m->get_withdrawal_by_loan_id($loan_id,$group_id);
            if($withdrawal){
                $input = array(
                    'active' => 0,
                    'modified_on'=> time(),
                );
                if($this->ci->withdrawals_m->update($withdrawal->id,$input)){
                    if($this->void_withdrawal($withdrawal->group_id,$withdrawal->id,$withdrawal->type,$withdrawal->withdrawal_date,$withdrawal->account_id,$withdrawal->amount)){

                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return TRUE;
            }
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function void_external_loan($debtor_loan_id=0,$group_id=0){
        if($debtor_loan_id){
            $withdrawal = $this->ci->withdrawals_m->get_group_withdrawal_by_debtor_loan_id($debtor_loan_id,$group_id);
            if($withdrawal){
                 $input = array(
                                'active' => 0,
                                'modified_on'=> time(),
                            );
                if($this->ci->withdrawals_m->update($withdrawal->id,$input)){
                    if($this->void_withdrawal($withdrawal->group_id,$withdrawal->id,$withdrawal->type,$withdrawal->withdrawal_date,$withdrawal->account_id,$withdrawal->amount)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return TRUE;
            }
            return TRUE;
        }else{
            return FALSE;
        }
    }

    public function create_money_market_investment($group_id= 0,$investment_institution_name = 0,$investment_date = 0,$investment_amount = 0,$withdrawal_account_id = 0,$description = '',$transaction_alert_id = 0,$is_a_back_dating_record = FALSE){
        if($group_id&&$investment_institution_name&&$investment_date&&$investment_amount&&$withdrawal_account_id){
            $input = array(
                'investment_institution_name' => $investment_institution_name,
                'investment_date' => $investment_date,
                'investment_amount' => $investment_amount,
                'group_id' => $group_id,
                'description' => $description,
                'withdrawal_account_id' => $withdrawal_account_id,
                'created_on' => time(),
                'active' => 1,
                'is_closed' => 0,
                'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
            );
            if($money_market_investment_id = $this->ci->money_market_investments_m->insert($input)){
                if($this->_create_money_market_investment_withdrawal($group_id,$money_market_investment_id,$withdrawal_account_id,$investment_date,$investment_amount,$description,$transaction_alert_id,$is_a_back_dating_record)){
                    return $money_market_investment_id;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    private function _create_money_market_investment_withdrawal($group_id = 0,$money_market_investment_id = 0,$account_id = 0,$withdrawal_date = 0,$amount = 0,$description = '',$transaction_alert_id = 0,$is_a_back_dating_record = FALSE){
        if(preg_match('/bank-/', $account_id)){
            $type = 17;
        }else if(preg_match('/sacco-/', $account_id)){
            $type = 18;
        }else if(preg_match('/mobile-/', $account_id)){
            $type = 19;
        }else if(preg_match('/petty-/', $account_id)){
            $type = 20;
        }else{
            $type = 0;
        }
        $transaction_type = $type;
        $input = array(
            'type'=>$type,
            'group_id' => $group_id,
            'withdrawal_date' => $withdrawal_date,
            'money_market_investment_id' => $money_market_investment_id,
            'withdrawal_method' => 1,
            'account_id' => $account_id,
            'amount' => $amount,
            'description' => $description,
            'transaction_alert_id' => $transaction_alert_id,
            'active' => 1,
            'created_on' => time(),
            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
        );
        if($withdrawal_id = $this->ci->withdrawals_m->insert($input)){
            if($this->withdrawal($group_id,$withdrawal_id,$transaction_type,$withdrawal_date,$account_id,$amount,$description,$money_market_investment_id,'','','','','','','',$transaction_alert_id,0,0,0,0,0,$is_a_back_dating_record)){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function void_money_market_investment($id = 0,$money_market_investment = array()){
        $input = array(
            'active' => 0,
            'modified_on' => time(),
        );
        if($result = $this->ci->money_market_investments_m->update($id,$input)){
            if($money_market_investment){
                if($money_market_investment->is_closed){
                    $deposit = $this->ci->deposits_m->get_money_market_investment_cash_in_deposit_by_money_market_investment_id($money_market_investment->id,$money_market_investment->group_id);
                    if($deposit){
                        if($this->void_group_deposit($deposit->id,$deposit,TRUE,$deposit->group_id)){
                            if($this->_void_money_market_investment_withdrawals($money_market_investment->id,$money_market_investment->group_id)){
                                return TRUE;
                            }else{
                                $this->ci->session->set_flashdata('warning',"Could not void money market withdrawals");
                                return FALSE;
                            }
                        }else{
                            $this->ci->session->set_flashdata('warning',"Could not void money market cash in");
                            return FALSE;
                        }
                    }else{
                        $this->ci->session->set_flashdata('warning',"Could not find deposit");
                        return FALSE;
                    }
                }else{
                    if($this->_void_money_market_investment_withdrawals($money_market_investment->id,$money_market_investment->group_id)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    private function _void_money_market_investment_withdrawals($money_market_investment_id = 0,$group_id = 0){
        $withdrawals = $this->ci->withdrawals_m->get_money_market_investment_withdrawals_by_money_market_investment_id($money_market_investment_id,$group_id);
        if($withdrawals){
            foreach($withdrawals as $withdrawal){
                $input = array(
                    'active' => 0,
                    'modified_on' => time(),
                );
                $void_result = TRUE;
                if($result = $this->ci->withdrawals_m->update($withdrawal->id,$input)){
                    if($this->void_withdrawal($withdrawal->group_id,$withdrawal->id,$withdrawal->type,$withdrawal->withdrawal_date,$withdrawal->account_id,$withdrawal->amount)){
                        
                    }else{
                        $void_result = FALSE;
                    }
                }else{
                    $void_result = FALSE;
                }
            }
            return $void_result;
        }else{
            return FALSE;
        }           
    }

    public function void_money_market_investment_withdrawal($id = 0,$withdrawal = array()){
        if($id&&$withdrawal){
            $money_market_investment = $this->ci->money_market_investments_m->get_group_money_market_investment($withdrawal->money_market_investment_id,$withdrawal->group_id);
            if($money_market_investment){
                if($withdrawal->amount==$money_market_investment->investment_amount){
                    return $this->void_money_market_investment($money_market_investment->id,$money_market_investment);
                }else{
                    $input = array(
                        'active' => 0,
                        'modified_on' => time(),
                    );
                    $void_result = TRUE;
                    if($result = $this->ci->withdrawals_m->update($withdrawal->id,$input)){
                        if($this->void_withdrawal($withdrawal->group_id,$withdrawal->id,$withdrawal->type,$withdrawal->withdrawal_date,$withdrawal->account_id,$withdrawal->amount)){
                            $input = array(
                                'modified_on' => time(),
                                'investment_amount' => $money_market_investment->investment_amount-$withdrawal->amount,
                            );
                            if($result = $this->ci->money_market_investments_m->update($money_market_investment->id,$input)){
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function top_up_money_market_investment($group_id = 0,$id = 0,$top_up_date = 0,$top_up_amount = 0,$top_up_withdrawal_account_id = 0,$top_up_description = '',$transaction_alert_id = 0){
        $money_market_investment = $this->ci->money_market_investments_m->get_group_money_market_investment($id,$group_id);
        if($money_market_investment&&$group_id&&$id&&$top_up_date&&$top_up_amount&&$top_up_withdrawal_account_id){
            $investment_amount = $money_market_investment->investment_amount + $top_up_amount;
            $input = array(
                'investment_amount' => $investment_amount,
                'modified_on' => time(),
            );
            if($result = $this->ci->money_market_investments_m->update($id,$input)){
                //add withdrawal add code
                if($this->_create_money_market_investment_withdrawal($group_id,$id,$top_up_withdrawal_account_id,$top_up_date,$top_up_amount,$top_up_description,$transaction_alert_id)){
                    return $result;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function record_stock_purchase($group_id = 0,$purchase_date = 0,$name = '',$number_of_shares = '',$account_id = 0,$price_per_share = 0,$transaction_alert_id = 0,$is_a_back_dating_record = FALSE){
        if(preg_match('/bank-/', $account_id)){
            $type = 13;
        }else if(preg_match('/sacco-/', $account_id)){
            $type = 14;
        }else if(preg_match('/mobile-/', $account_id)){
            $type = 15;
        }else if(preg_match('/petty-/', $account_id)){
            $type = 16;
        }else{
            $type = 0;
        }

        if($type&&$group_id&&$purchase_date&&$name&&$number_of_shares&&$account_id&&$price_per_share){
            $input = array(
                'group_id'=>$group_id,
                'purchase_date'=>$purchase_date,
                'name'=>$name,
                'number_of_shares'=>$number_of_shares,
                'withdrawal_account_id'=>$account_id,
                'purchase_price'=>$price_per_share,
                'current_price'=>$price_per_share,
                'active'=>1,
                'created_on'=>time(),
                'is_a_back_dating_record'=>$is_a_back_dating_record?1:0,
            );
            if($stock_id = $this->ci->stocks_m->insert($input)){
                $amount = $number_of_shares * $price_per_share;
                $input = array(
                    'type'=>$type,
                    'group_id' => $group_id,
                    'withdrawal_date' => $purchase_date,
                    'stock_id' => $stock_id,
                    'withdrawal_method' => 1,
                    'account_id' => $account_id,
                    'amount' => $amount,
                    'transaction_alert_id' => $transaction_alert_id,
                    'description' => '',
                    'active' => 1,
                    'created_on' => time(),
                    'is_a_back_dating_record'=>$is_a_back_dating_record?1:0,
                );
                if($withdrawal_id = $this->ci->withdrawals_m->insert($input)){
                    $transaction_type = $type;
                    if($this->withdrawal($group_id,$withdrawal_id,$transaction_type,$purchase_date,$account_id,$amount,'','','','',$stock_id,'','','','',$transaction_alert_id,0,0,0,0,0,$is_a_back_dating_record)){
                        return $stock_id;
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function void_stock_purchase($id = 0,$group_id = 0){
        $input = array(
            'active' => 0,
            'modified_on' => time(),
        );
        if($result = $this->ci->stocks_m->update($id,$input)){
            $withdrawal = $this->ci->withdrawals_m->get_withdrawal_by_stock_id($id,$group_id);
            if($withdrawal){
                $input = array(
                    'active' => 0,
                    'modified_on' => time(),
                );
                if($this->ci->withdrawals_m->update($withdrawal->id,$input)){
                    if($this->void_withdrawal($withdrawal->group_id,$withdrawal->id,$withdrawal->type,$withdrawal->withdrawal_date,$withdrawal->account_id,$withdrawal->amount)){
                        $deposits = $this->ci->deposits_m->get_group_stock_sale_deposits_by_stock_id($withdrawal->stock_id,$withdrawal->group_id);
                        $result = TRUE;
                        if($deposits){
                            foreach ($deposits as $deposit) {
                                # code..
                                if($this->void_group_deposit($deposit->id,$deposit,TRUE,$deposit->group_id)){
                                    //do nothing for now
                                }else{
                                    $result = FALSE;
                                }
                            }
                        }
                        return $result;
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function record_stock_sale($group_id = 0,$stock_id = 0,$sale_date = 0,$account_id = 0,$number_of_shares_sold = 0,$sale_price_per_share = 0,$number_of_previously_sold_shares = 0,$transaction_alert_id = 0,$is_a_back_dating_record = FALSE){
        if($group_id&&$stock_id&&$sale_date&&$account_id&&$number_of_shares_sold&&$sale_price_per_share){
            $input = array(
                'group_id' => $group_id,
                'stock_id' => $stock_id,
                'account_id' => $account_id,
                'sale_date' => $sale_date,
                'number_of_shares_sold' => $number_of_shares_sold,
                'sale_price_per_share' => $sale_price_per_share,
                'active' => 1,
                'created_on' => time(),
                'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
            );
            if($stock_sale_id = $this->ci->stocks_m->insert_stock_sale($input)){
                $total_number_of_shares_sold = $number_of_previously_sold_shares + $number_of_shares_sold;
                $input = array(
                    'number_of_shares_sold' => $total_number_of_shares_sold,
                    'modified_on' => time(),
                );
                if($result = $this->ci->stocks_m->update($stock_id,$input)){
                    if(preg_match('/bank-/', $account_id)){
                        $type = 25;
                    }else if(preg_match('/sacco-/', $account_id)){
                        $type = 26;
                    }else if(preg_match('/mobile-/', $account_id)){
                        $type = 27;
                    }else if(preg_match('/petty-/', $account_id)){
                        $type = 28;
                    }else{
                        $type = 0;
                    }
                    if($stock_sale_id&&$type){
                        $amount = $number_of_shares_sold * $sale_price_per_share;
                        $input = array(
                            'stock_sale_id' => $stock_sale_id,
                            'stock_id' => $stock_id,
                            'sale_price_per_share' => $sale_price_per_share,
                            'number_of_shares_sold' => $number_of_shares_sold,
                            'type' => $type,
                            'group_id'=>$group_id,
                            'deposit_date'=>$sale_date,
                            'depositor_id'=>0,
                            'income_category_id'=>0,
                            'member_id'=>0,
                            'contribution_id'=>0,
                            'fine_category_id'=>0,
                            'account_id'=>$account_id,
                            'transaction_alert_id'=>$transaction_alert_id,
                            'deposit_method'=>1,
                            'amount'=>$amount,
                            'description'=>'',
                            'active'=>1,
                            'created_on'=>time(),
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                        );
                        if($deposit_id = $this->ci->deposits_m->insert($input)){
                            $deposit_transaction_type = $type;
                            if($this->deposit($group_id,$deposit_id,$deposit_transaction_type,$sale_date,$account_id,$amount,'','','','','','',$stock_sale_id,'','','','','','','','',$transaction_alert_id,$is_a_back_dating_record)){      
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function void_stock_sale_deposit($id = 0,$deposit = array()){
        $input = array(
            'active'=>0,
            'modified_on'=>time(),
        );        
        if($result = $this->ci->deposits_m->update($id,$input)){
            if($this->void_deposit($deposit->group_id,$deposit->id,$deposit->type,$deposit->deposit_date,$deposit->account_id,$deposit->amount)){
                if($this->void_stock_sale($deposit->group_id,$deposit->stock_sale_id,$deposit->stock_id,$deposit->number_of_shares_sold)){
                    return TRUE;
                }else{
                    $this->ci->session->set_flashdata('warning','Could not void stock sale');
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function void_stock_sale($group_id = 0,$id = 0,$stock_id = 0,$number_of_shares_sold = 0){
        if($group_id&&$id&&$stock_id&&$number_of_shares_sold){
            $input = array(
                'active'=>0,
                'modified_on'=>time(),
            );
            if($result = $this->ci->stocks_m->update_stock_sale($id,$input)){
                $stock = $this->ci->stocks_m->get_group_stock($stock_id,$group_id);
                if($stock){
                    $number_of_shares_sold = $stock->number_of_shares_sold - $number_of_shares_sold;
                    $input = array(
                        'number_of_shares_sold'=>$number_of_shares_sold,
                        'modified_on'=>time()
                    );
                    if($result = $this->ci->stocks_m->update($stock_id,$input)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{
                    $this->ci->session->set_flashdata('info','Stock voided');
                    return TRUE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    
    public function record_expense_withdrawal($group_id = 0,$expense_date = 0,$expense_category_id = 0,$withdrawal_method = 0,$account_id = 0,$description = '',$amount = 0,$transaction_alert_id = 0,$is_a_back_dating_record = FALSE){
        if(preg_match('/bank-/', $account_id)){
            $type = 1;
        }else if(preg_match('/sacco-/', $account_id)){
            $type = 2;
        }else if(preg_match('/mobile-/', $account_id)){
            $type = 3;
        }else if(preg_match('/petty-/', $account_id)){
            $type = 4;
        }else{
            $type = 0;
        }
        if($type&&$group_id&&$expense_date&&$expense_category_id&&$withdrawal_method&&$account_id&&$amount){
            $input = array(
                'type'=>$type,
                'group_id' => $group_id,
                'withdrawal_date' => $expense_date,
                'expense_category_id' => $expense_category_id,
                'withdrawal_method' => $withdrawal_method,
                'account_id' => $account_id,
                'amount' => $amount,
                'description' => $description,
                'transaction_alert_id' => $transaction_alert_id,
                'active' => 1,
                'created_on' => time(),
                'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
            );
            if($withdrawal_id = $this->ci->withdrawals_m->insert($input)){
                $transaction_type = $type;
                if($this->withdrawal($group_id,$withdrawal_id,$transaction_type,$expense_date,$account_id,$amount,$description,'','','','','','','','',$transaction_alert_id,'','','','',$expense_category_id,$is_a_back_dating_record)){
                    return TRUE;
                }else{
                    $this->ci->session->set_flashdata('error','Failed to reconcile withdrawal');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Withdrawal recording failed');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Some files/parameters were not submited');
            return FALSE;
        }
    }

    public function void_expense_withdrawal($id = 0,$withdrawal = array()){
        $input = array(
            'active'=>0,
            'modified_on'=>time(),
        );
        if($result = $this->ci->withdrawals_m->update($id,$input)){
            if($this->void_withdrawal($withdrawal->group_id,$withdrawal->id,$withdrawal->type,$withdrawal->withdrawal_date,$withdrawal->account_id,$withdrawal->amount)){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function void_bank_loan_repayment_withdrawal($id = 0,$withdrawal = array()){
        $input = array(
            'active'=>0,
            'modified_on'=>time(),
        );
        if($result = $this->ci->withdrawals_m->update($id,$input)){
            if($this->void_withdrawal($withdrawal->group_id,$withdrawal->id,$withdrawal->type,$withdrawal->withdrawal_date,$withdrawal->account_id,$withdrawal->amount)){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function record_asset_purchase_payment($group_id = 0,$payment_date = 0,$asset_id = 0,$account_id = 0,$payment_method = 0,$description = '',$amount = 0,$transaction_alert_id = 0,$is_a_back_dating_record = FALSE){
        if(preg_match('/bank-/', $account_id)){
            $type = 5;
        }else if(preg_match('/sacco-/', $account_id)){
            $type = 6;
        }else if(preg_match('/mobile-/', $account_id)){
            $type = 7;
        }else if(preg_match('/petty-/', $account_id)){
            $type = 8;
        }else{
            $type = 0;
        }
        if($type&&$group_id&&$payment_date&&$asset_id&&$account_id&&$payment_method&&$amount){
            $input = array(
                'type'=>$type,
                'group_id'=>$group_id,
                'withdrawal_date'=>$payment_date,
                'asset_id'=>$asset_id,
                'account_id'=>$account_id,
                'withdrawal_method'=>$payment_method,
                'amount'=>$amount,
                'description'=>$description,
                'transaction_alert_id'=>$transaction_alert_id,
                'active'=>1,
                'created_on'=>time(),
                'is_a_back_dating_record'=>$is_a_back_dating_record?1:0,
            );
            if($withdrawal_id = $this->ci->withdrawals_m->insert($input)){
                $transaction_type = $type;
                if($this->withdrawal($group_id,$withdrawal_id,$transaction_type,$payment_date,$account_id,$amount,$description,'',$asset_id,'','','','','','',$transaction_alert_id,0,0,0,0,0,$is_a_back_dating_record)){
                    return TRUE;
                }else{
                    $this->ci->session->set_flashdata('error','Failed to reconcile account withdrawal');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Failed to create account withdrawal');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Your request is missing some values. Try again');
            return FALSE;
        }
    }

    public function void_asset_purchase_payment($id = 0,$withdrawal = array()){
        $input = array(
            'active'=>0,
            'modified_on'=>time(),
        );
        if($result = $this->ci->withdrawals_m->update($id,$input)){
            if($this->void_withdrawal($withdrawal->group_id,$withdrawal->id,$withdrawal->type,$withdrawal->withdrawal_date,$withdrawal->account_id,$withdrawal->amount)){
                return TRUE;
            }else{
                return FALSE;
            }     
        }else{
            return FALSE;
        }
    }

    public function record_money_market_investment_cash_in_deposit($group_id = 0,$money_market_investment_id = 0,$cash_in_date = 0,$cash_in_deposit_account_id = 0,$cash_in_amount = 0,$transaction_alert_id = 0,$is_a_back_dating_record = FALSE){
        if(preg_match('/bank-/', $cash_in_deposit_account_id)){
            $type = 29;
        }else if(preg_match('/sacco-/', $cash_in_deposit_account_id)){
            $type = 30;
        }else if(preg_match('/mobile-/', $cash_in_deposit_account_id)){
            $type = 31;
        }else if(preg_match('/petty-/', $cash_in_deposit_account_id)){
            $type = 32;
        }else{
            $type = 0;
        }
        if($type&&$group_id&&$money_market_investment_id&&$cash_in_date&&$cash_in_deposit_account_id&&$cash_in_amount){
            $input = array(
                'type'=>$type,
                'group_id'=>$group_id,
                'deposit_date'=>$cash_in_date,
                'money_market_investment_id'=>$money_market_investment_id,
                'transaction_alert_id'=>$transaction_alert_id,
                'account_id'=>$cash_in_deposit_account_id,
                'deposit_method'=>1,
                'amount'=>$cash_in_amount,
                'description'=>'',
                'active'=>1,
                'created_on'=>time(),
                'is_a_back_dating_record'=>$is_a_back_dating_record?1:0,
            );
            $transaction_type = $type;
            if($deposit_id = $this->ci->deposits_m->insert($input)){
                if($this->deposit($group_id,$deposit_id,$type,$cash_in_date,$cash_in_deposit_account_id,$cash_in_amount,'','','','','','','',$money_market_investment_id,'','','','','','','',$transaction_alert_id,$is_a_back_dating_record)){
                    if($this->record_money_market_investment_cash_in($money_market_investment_id,$cash_in_date,$cash_in_deposit_account_id,$cash_in_amount,$is_a_back_dating_record)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function record_money_market_investment_cash_in($money_market_investment_id = 0,$cash_in_date = 0,$cash_in_deposit_account_id = 0,$cash_in_amount = 0,$is_a_back_dating_record = FALSE){
        if($money_market_investment_id&&$cash_in_date&&$cash_in_deposit_account_id&&$cash_in_amount){
            $group_id = $this->ci->group?$this->ci->group->id:"";
            $money_market_investment = $this->ci->money_market_investments_m->get_group_money_market_investment($money_market_investment_id);
            
            $cashins_deposits = $this->ci->deposits_m->get_group_money_market_investment_cash_in_deposits_options($group_id,$money_market_investment_id);
            $total_cash_in_amount = 0;
            foreach ($cashins_deposits as $key => $cashins_deposit) {
                $total_cash_in_amount += currency($cashins_deposit->amount);
            }
            if($total_cash_in_amount > $money_market_investment->investment_amount){
                $is_closed = 1;
            }else{
                $is_closed = 0;
            }
            $input = array(
                'is_closed'=>$is_closed,
                'total_cash_in_amount'=>$total_cash_in_amount,
                'deposit_account_id'=>$cash_in_deposit_account_id,
                'cash_in_amount'=>$cash_in_amount,
                'cash_in_date'=>$cash_in_date,
                'modified_on'=>time(),
            );
            if($result = $this->ci->money_market_investments_m->update($money_market_investment_id,$input)){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function void_money_market_investment_cash_in_deposit($id = 0,$deposit = array()){
        $input = array(
            'active'=>0,
            'modified_on'=>time(),
        );        
        if($result = $this->ci->deposits_m->update($id,$input)){
            if($this->void_deposit($deposit->group_id,$deposit->id,$deposit->type,$deposit->deposit_date,$deposit->account_id,$deposit->amount)){
                if($this->_void_money_market_investment_cash_in($deposit->money_market_investment_id)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    private function _void_money_market_investment_cash_in($id = 0){
        $input = array(
            'is_closed'=>0,
            'deposit_account_id'=>0,
            'cash_in_amount'=>0,
            'cash_in_date'=>0,
            'modified_on'=>time(),
        );
        if($result = $this->ci->money_market_investments_m->update($id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    public function record_asset_sale_deposit($group_id = 0,$asset_id = 0,$sale_date = 0,$account_id = 0,$amount = 0,$transaction_alert_id = 0,$is_a_back_dating_record = FALSE){
        if(preg_match('/bank-/', $account_id)){
            $type = 33;
        }else if(preg_match('/sacco-/', $account_id)){
            $type = 34;
        }else if(preg_match('/mobile-/', $account_id)){
            $type = 35;
        }else if(preg_match('/petty-/', $account_id)){
            $type = 36;
        }else{
            $type = 0;
        }
        if($type&&$group_id&&$asset_id&&$sale_date&&$account_id&&$amount){
            $input = array(
                'type'=>$type,
                'group_id'=>$group_id,
                'deposit_date'=>$sale_date,
                'asset_id'=>$asset_id,
                'account_id'=>$account_id,
                'transaction_alert_id'=>$transaction_alert_id,
                'deposit_method'=>1,
                'amount'=>$amount,
                'description'=>'',
                'active'=>1,
                'created_on'=>time(),
                'is_a_back_dating_record'=>$is_a_back_dating_record?1:0,
            );
            $transaction_type = $type;
            if($deposit_id = $this->ci->deposits_m->insert($input)){
                $input = array(
                    'transaction_type'=>$transaction_type,
                    'group_id'=>$group_id,
                    'deposit_id'=>$deposit_id,
                    'transaction_date'=>$sale_date,
                    'asset_id'=>$asset_id,
                    'account_id'=>$account_id,
                    'description'=>'',
                    'amount'=>$amount,
                    'active'=>1,
                    'created_on'=>time(),
                    'is_a_back_dating_record'=>$is_a_back_dating_record?1:0,
                );
                if($this->deposit($group_id,$deposit_id,$type,$sale_date,$account_id,$amount,'','','','','','','','',$asset_id,'','','','','','',$transaction_alert_id,$is_a_back_dating_record)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function void_asset_sale_deposit($id = 0,$deposit = array()){
        $input = array(
            'active'=>0,
            'modified_on'=>time(),
        );        
        if($result = $this->ci->deposits_m->update($id,$input)){
            if($this->void_deposit($deposit->group_id,$deposit->id,$deposit->type,$deposit->deposit_date,$deposit->account_id,$deposit->amount)){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function create_bank_loan($group_id = 0,$description = '',$amount_loaned = 0,$total_loan_amount_payable = 0,$loan_balance = 0,$loan_start_date = 0,$loan_end_date = 0,$account_id = 0,$is_fully_paid = 0,$transaction_alert_id = 0,$is_a_back_dating_record = FALSE){
        if($group_id&&$description&&$amount_loaned&&$total_loan_amount_payable&&$loan_balance&&$loan_start_date&&$loan_end_date&&$account_id){
            $input = array(
                'description' => $description,
                'amount_loaned' => $amount_loaned,
                'total_loan_amount_payable' => $total_loan_amount_payable,
                'loan_balance' => $loan_balance,
                'balance' => $loan_balance,
                'loan_start_date' => $loan_start_date,
                'loan_end_date' => $loan_end_date,
                'account_id' => $account_id,
                'group_id' => $group_id,
                'is_fully_paid' => $is_fully_paid,
                'created_on' => time(),
                'active' => 1,
                'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
            );
            if($bank_loan_id = $this->ci->bank_loans_m->insert($input)){
                if(preg_match('/bank-/', $account_id)){
                    $type = 21;
                }else if(preg_match('/sacco-/', $account_id)){
                    $type = 22;
                }else if(preg_match('/mobile-/', $account_id)){
                    $type = 23;
                }else if(preg_match('/petty-/', $account_id)){
                    $type = 24;
                }else{
                    $type = 0;
                }
                if($type){
                    $input = array(
                        'type'=>$type,
                        'group_id'=>$group_id,
                        'deposit_date'=>$loan_start_date,
                        'bank_loan_id'=>$bank_loan_id,
                        'account_id'=>$account_id,
                        'deposit_method'=>1,
                        'amount'=>$amount_loaned,
                        'description'=> $description,
                        'transaction_alert_id'=> $transaction_alert_id,
                        'active'=>1,
                        'created_on'=>time(),
                        'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                    );
                    if($deposit_id = $this->ci->deposits_m->insert($input)){
                        if($this->deposit($group_id,$deposit_id,$type,$loan_start_date,$account_id,$amount_loaned,'','','','','','','','','',$bank_loan_id,'','','','','',$transaction_alert_id,$is_a_back_dating_record)){
                            return TRUE;
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function void_bank_loan($id = 0,$group_id = 0){
        if($id&&$group_id){
            $input = array(
                'active'=>0,
                'modified_on'=>time(),
            );
            if($result = $this->ci->bank_loans_m->update($id,$input)){
                //void repayments
                $repayments = $this->ci->bank_loans_m->get_all_bank_loan_repayments($id);
                if($repayments){
                    foreach ($repayments as $payment) {
                        $withdrawal = $this->ci->withdrawals_m->get_group_withdrawal_by_bank_loan_repayment_id($payment->id,$payment->group_id);
                        $this->void_group_withdrawal($withdrawal->id,$withdrawal,TRUE,$withdrawal->group_id);
                    }
                }
                $deposit = $this->ci->deposits_m->get_bank_loan_disbursement_deposit_by_bank_loan_id($id,$group_id);
                if($deposit){
                    $input = array(
                        'active'=>0,
                        'modified_on'=>time(),
                    ); 
                    if($result = $this->ci->deposits_m->update($deposit->id,$input)){
                        if($this->void_deposit($deposit->group_id,$deposit->id,$deposit->type,$deposit->deposit_date,$deposit->account_id,$deposit->amount)){
                            return TRUE;
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function record_account_transfer($group_id,$transfer_date,$from_account_id,$to_account_id,$amount,$description,$transaction_alert_id = 0,$is_a_back_dating_record = FALSE){
        
        if(preg_match('/bank-/', $to_account_id)){
            $deposit_type = 37;
        }else if(preg_match('/sacco-/', $to_account_id)){
            $deposit_type = 38;
        }else if(preg_match('/mobile-/', $to_account_id)){
            $deposit_type = 39;
        }else if(preg_match('/petty-/', $to_account_id)){
            $deposit_type = 40;
        }else{
            $deposit_type = 0;
        }

        if(preg_match('/bank-/', $from_account_id)){
            $withdrawal_type = 29;
        }else if(preg_match('/sacco-/', $from_account_id)){
            $withdrawal_type = 30;
        }else if(preg_match('/mobile-/', $from_account_id)){
            $withdrawal_type = 31;
        }else if(preg_match('/petty-/', $from_account_id)){
            $withdrawal_type = 32;
        }else{
            $withdrawal_type = 0;
        }

        if($group_id&&$transfer_date&&$from_account_id&&$to_account_id&&$amount&&$deposit_type&&$withdrawal_type){
            $input = array(
                'transfer_date'=>$transfer_date,
                'to_account_id'=>$to_account_id,
                'from_account_id'=>$from_account_id,
                'group_id'=>$group_id,
                'amount'=>$amount,
                'description'=>$description,
                'active'=>1,
                'created_on'=>time(),
                'is_a_back_dating_record'=>$is_a_back_dating_record?1:0,
            );
            if($account_transfer_id = $this->ci->accounts_m->insert_account_transfer($input)){
                $input = array(
                    'type'=>$withdrawal_type,
                    'account_transfer_id'=>$account_transfer_id,
                    'to_account_id'=>$to_account_id,
                    'from_account_id'=>$from_account_id,
                    'group_id'=>$group_id,
                    'withdrawal_date'=>$transfer_date,
                    'account_id'=>$from_account_id,
                    'withdrawal_method'=>1,
                    'amount'=>$amount,
                    'description'=>$description,
                    'transaction_alert_id'=>$transaction_alert_id,
                    'active'=>1,
                    'created_on'=>time(),
                    'is_a_back_dating_record'=>$is_a_back_dating_record?1:0,
                );  
                if($withdrawal_id = $this->ci->withdrawals_m->insert($input)){
                    $transaction_type = $withdrawal_type;
                    if($this->withdrawal($group_id,$withdrawal_id,$transaction_type,$transfer_date,$from_account_id,$amount,$description,'','','','','',$account_transfer_id,$from_account_id,$to_account_id,$transaction_alert_id,0,0,0,0,0,$is_a_back_dating_record)){
                        $input = array(
                            'type'=>$deposit_type,
                            'group_id'=>$group_id,
                            'deposit_date'=>$transfer_date,
                            'account_transfer_id'=>$account_transfer_id,
                            'to_account_id'=>$to_account_id,
                            'from_account_id'=>$from_account_id,
                            'account_id'=>$to_account_id,
                            'deposit_method'=>1,
                            'amount'=>$amount,
                            'transaction_alert_id'=>$transaction_alert_id,
                            'description'=> $description,
                            'active'=>1,
                            'created_on'=>time(),
                            'is_a_back_dating_record'=>$is_a_back_dating_record?1:0,
                        );
                        if($deposit_id = $this->ci->deposits_m->insert($input)){
                            if($this->deposit($group_id,$deposit_id,$deposit_type,$transfer_date,$to_account_id,$amount,'','','','','','','','','','',$account_transfer_id,$from_account_id,$to_account_id,'','',$transaction_alert_id,$is_a_back_dating_record)){
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            } 
        }else{
            return FALSE;
        }
    }

    public function void_account_transfer($account_transfer_id = 0,$group_id = 0){
        if($account_transfer_id){
            $withdrawal = $this->ci->withdrawals_m->get_withdrawal_by_account_transfer_id($account_transfer_id,$group_id);
            $deposit = $this->ci->deposits_m->get_deposit_by_account_transfer_id($account_transfer_id,$group_id);
            $input = array(
                'active'=>0,
                'modified_on'=>time()
            );
            if($this->ci->accounts_m->update_account_transfer($account_transfer_id,$input)){
                if($withdrawal&&$deposit){
                    $result = TRUE;
                    $input = array(
                        'active'=>0,
                        'modified_on'=>time(),
                    );
                    if($result = $this->ci->withdrawals_m->update($withdrawal->id,$input)){
                        if($this->void_withdrawal($withdrawal->group_id,$withdrawal->id,$withdrawal->type,$withdrawal->withdrawal_date,$withdrawal->account_id,$withdrawal->amount)){
                            
                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }

                    $input = array(
                        'active'=>0,
                        'modified_on'=>time(),
                    ); 
                    if($result = $this->ci->deposits_m->update($deposit->id,$input)){
                        if($this->void_deposit($deposit->group_id,$deposit->id,$deposit->type,$deposit->deposit_date,$deposit->account_id,$deposit->amount)){
                            
                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                    return $result;
                }else{
                    return TRUE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function deposit($group_id = 0,$deposit_id = 0,$transaction_type = 0,$transaction_date = 0,$account_id = 0,$amount = 0,$member_id = 0,$contribution_id = 0,$fine_category_id = 0,$income_category_id = 0,$description = '',$depositor_id = 0,$stock_sale_id = 0,$money_market_investment_id = 0,$asset_id = 0,$bank_loan_id = 0,$account_transfer_id = 0,$from_account_id = 0,$to_account_id = 0,$loan_id=0,$loan_repayment_id=0,$transaction_alert_id = 0,$is_a_back_dating_record = FALSE,$debtor_loan_id=0,$debtor_id=0,$debtor_loan_repayment_id=0,$checkoff_id = 0){
        if($group_id&&$transaction_type&&$deposit_id&&$transaction_date&&$account_id){
            if(in_array($transaction_type,array_flip($this->deposit_transaction_types))){
                if(in_array($transaction_type,$this->contribution_payment_transaction_types)){
                    if($member_id&&$contribution_id){
                        $input = array(
                            'transaction_type' => $this->deposit_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'deposit_id' => $deposit_id,
                            'group_id' => $group_id,
                            'member_id' => $member_id,
                            'contribution_id' => $contribution_id,
                            'description' => $description,
                            'amount' => $amount,
                            'checkoff_id' => $checkoff_id,
                            'active' => 1,
                            'transaction_alert_id' => $transaction_alert_id,
                            'created_on' => time(),
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_increase_account_balance($group_id,$account_id,$transaction_type,$amount)){
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        }
                    }else{
                        return FALSE;  
                    }
                }else if(in_array($transaction_type,$this->fine_payment_transaction_types)){
                    if($member_id&&($contribution_id||$fine_category_id)){
                        $input = array(
                            'transaction_type' => $this->deposit_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'deposit_id' => $deposit_id,
                            'group_id' => $group_id,
                            'member_id' => $member_id,
                            'contribution_id' => $contribution_id,
                            'fine_category_id' => $fine_category_id,
                            'description' => $description,
                            'amount' => $amount,
                            'transaction_alert_id' => $transaction_alert_id,
                            'active' => 1,
                            'created_on' => time(),
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_increase_account_balance($group_id,$account_id,$transaction_type,$amount)){
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        }
                    }else{
                        return FALSE;
                    }
                }else if(in_array($transaction_type,$this->miscellaneous_payment_transaction_types)){
                    if($member_id){
                        $input = array(
                            'transaction_type' => $this->deposit_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'deposit_id' => $deposit_id,
                            'group_id' => $group_id,
                            'member_id' => $member_id,
                            'contribution_id' => $contribution_id,
                            'fine_category_id' => $fine_category_id,
                            'description' => $description,
                            'transaction_alert_id' => $transaction_alert_id,
                            'amount' => $amount,
                            'active' => 1,
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                            'created_on' => time(),
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_increase_account_balance($group_id,$account_id,$transaction_type,$amount)){
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        }
                    }else{
                        return FALSE;
                    }
                }else if(in_array($transaction_type,$this->income_deposit_transaction_types)){
                    if($depositor_id&&$income_category_id){
                        $input = array(
                            'transaction_type' => $this->deposit_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'deposit_id' => $deposit_id,
                            'group_id' => $group_id,
                            'member_id' => $member_id,
                            'contribution_id' => $contribution_id,
                            'fine_category_id' => $fine_category_id,
                            'income_category_id' => $income_category_id,
                            'depositor_id' => $depositor_id,
                            'transaction_alert_id' => $transaction_alert_id,
                            'description' => $description,
                            'amount' => $amount,
                            'active' => 1,
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                            'created_on' => time(),
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_increase_account_balance($group_id,$account_id,$transaction_type,$amount)){
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        }
                    }else{
                        return FALSE;
                    }
                }else if(in_array($transaction_type,$this->stock_sale_deposit_transaction_types)){
                    if($stock_sale_id){
                        $input = array(
                            'transaction_type' => $this->deposit_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'deposit_id' => $deposit_id,
                            'group_id' => $group_id,
                            'member_id' => $member_id,
                            'contribution_id' => $contribution_id,
                            'fine_category_id' => $fine_category_id,
                            'depositor_id' => $depositor_id,
                            'stock_sale_id' => $stock_sale_id,
                            'transaction_alert_id' => $transaction_alert_id,
                            'description' => $description,
                            'amount' => $amount,
                            'active' => 1,
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                            'created_on' => time(),
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_increase_account_balance($group_id,$account_id,$transaction_type,$amount)){
                                return TRUE;
                                
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        }
                    }else{
                        return FALSE;
                    }
                }else if(in_array($transaction_type,$this->money_market_investment_cash_in_deposit_transaction_types)){
                    if($money_market_investment_id){
                        $input = array(
                            'transaction_type' => $this->deposit_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'deposit_id' => $deposit_id,
                            'group_id' => $group_id,
                            'member_id' => $member_id,
                            'contribution_id' => $contribution_id,
                            'fine_category_id' => $fine_category_id,
                            'depositor_id' => $depositor_id,
                            'stock_sale_id' => $stock_sale_id,
                            'money_market_investment_id' => $money_market_investment_id,
                            'description' => $description,
                            'transaction_alert_id' => $transaction_alert_id,
                            'amount' => $amount,
                            'active' => 1,
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                            'created_on' => time(),
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_increase_account_balance($group_id,$account_id,$transaction_type,$amount)){
                                return TRUE;
                                
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        }
                    }else{
                        return FALSE;
                    }
                }else if(in_array($transaction_type,$this->asset_sale_deposit_transaction_types)){
                    if($asset_id){
                        $input = array(
                            'transaction_type' => $this->deposit_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'deposit_id' => $deposit_id,
                            'group_id' => $group_id,
                            'member_id' => $member_id,
                            'contribution_id' => $contribution_id,
                            'fine_category_id' => $fine_category_id,
                            'depositor_id' => $depositor_id,
                            'stock_sale_id' => $stock_sale_id,
                            'money_market_investment_id' => $money_market_investment_id,
                            'asset_id' => $asset_id,
                            'transaction_alert_id' => $transaction_alert_id,
                            'description' => $description,
                            'amount' => $amount,
                            'active' => 1,
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                            'created_on' => time(),
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_increase_account_balance($group_id,$account_id,$transaction_type,$amount)){
                                return TRUE;
                                
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        }
                    }else{
                        return FALSE;
                    }
                }else if(in_array($transaction_type,$this->bank_loan_disbursement_deposit_transaction_types)){
                    if($bank_loan_id){
                        $input = array(
                            'transaction_type' => $this->deposit_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'deposit_id' => $deposit_id,
                            'group_id' => $group_id,
                            'member_id' => $member_id,
                            'contribution_id' => $contribution_id,
                            'fine_category_id' => $fine_category_id,
                            'depositor_id' => $depositor_id,
                            'stock_sale_id' => $stock_sale_id,
                            'money_market_investment_id' => $money_market_investment_id,
                            'asset_id' => $asset_id,
                            'bank_loan_id' => $bank_loan_id,
                            'transaction_alert_id' => $transaction_alert_id,
                            'description' => $description,
                            'amount' => $amount,
                            'active' => 1,
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                            'created_on' => time(),
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_increase_account_balance($group_id,$account_id,$transaction_type,$amount)){
                                return TRUE;
                                
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        }
                    }else{
                        return FALSE;
                    }
                }else if(in_array($transaction_type,$this->loan_processing_income_deposit_transaction_types)){
                    if($loan_id){
                        $input = array(
                            'transaction_type' => $this->deposit_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'deposit_id' => $deposit_id,
                            'group_id' => $group_id,
                            'member_id' => $member_id,
                            'contribution_id' => $contribution_id,
                            'fine_category_id' => $fine_category_id,
                            'depositor_id' => $depositor_id,
                            'stock_sale_id' => $stock_sale_id,
                            'money_market_investment_id' => $money_market_investment_id,
                            'loan_id' => $loan_id,
                            'asset_id' => $asset_id,
                            'bank_loan_id' => $bank_loan_id,
                            'transaction_alert_id' => $transaction_alert_id,
                            'description' => $description,
                            'amount' => $amount,
                            'active' => 1,
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                            'created_on' => time(),
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_increase_account_balance($group_id,$account_id,$transaction_type,$amount)){
                                return TRUE;
                                
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        }
                    }else{
                        return FALSE;
                    }
                }else if(in_array($transaction_type,$this->loan_repayment_transaction_types)){
                    if($loan_id){

                        $input = array(
                            'transaction_type' => $this->deposit_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'deposit_id' => $deposit_id,
                            'group_id' => $group_id,
                            'member_id' => $member_id,
                            'contribution_id' => $contribution_id,
                            'fine_category_id' => $fine_category_id,
                            'depositor_id' => $depositor_id,
                            'stock_sale_id' => $stock_sale_id,
                            'money_market_investment_id' => $money_market_investment_id,
                            'asset_id' => $asset_id,
                            'bank_loan_id' => $bank_loan_id,
                            'description' => $description,
                            'amount' => $amount,
                            'loan_id'=>$loan_id,
                            'transaction_alert_id' => $transaction_alert_id,
                            'loan_repayment_id'=>$loan_repayment_id,
                            'active' => 1,
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                            'created_on' => time(),
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_increase_account_balance($group_id,$account_id,$transaction_type,$amount)){
                                return TRUE;
                                
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        }
                    }else{
                        return FALSE;
                    }
                }else if(in_array($transaction_type,$this->incoming_account_transfer_withdrawal_transaction_types)){
                    if($account_transfer_id&&$from_account_id&&$to_account_id){
                        $input = array(
                            'transaction_type' => $this->deposit_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'deposit_id' => $deposit_id,
                            'group_id' => $group_id,
                            'member_id' => $member_id,
                            'contribution_id' => $contribution_id,
                            'fine_category_id' => $fine_category_id,
                            'depositor_id' => $depositor_id,
                            'stock_sale_id' => $stock_sale_id,
                            'money_market_investment_id' => $money_market_investment_id,
                            'asset_id' => $asset_id,
                            'bank_loan_id' => $bank_loan_id,
                            'account_transfer_id' => $account_transfer_id,
                            'from_account_id' => $from_account_id,
                            'to_account_id' => $to_account_id,
                            'transaction_alert_id' => $transaction_alert_id,
                            'description' => $description,
                            'amount' => $amount,
                            'active' => 1,
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                            'created_on' => time(),
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_increase_account_balance($group_id,$account_id,$transaction_type,$amount)){
                                return TRUE;
                                
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        }
                    }else{
                        return FALSE;
                    }
                }else if(in_array($transaction_type,$this->external_lending_processing_income_deposit_transaction_types)){
                    if($debtor_loan_id && $debtor_id){
                        $input = array(
                            'transaction_type' => $this->deposit_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'deposit_id' => $deposit_id,
                            'group_id' => $group_id,
                            'member_id' => $member_id,
                            'debtor_id' => $debtor_id,
                            'contribution_id' => $contribution_id,
                            'fine_category_id' => $fine_category_id,
                            'depositor_id' => $depositor_id,
                            'stock_sale_id' => $stock_sale_id,
                            'money_market_investment_id' => $money_market_investment_id,
                            'loan_id' => $loan_id,
                            'debtor_loan_id' => $debtor_loan_id,
                            'asset_id' => $asset_id,
                            'bank_loan_id' => $bank_loan_id,
                            'transaction_alert_id' => $transaction_alert_id,
                            'description' => $description,
                            'amount' => $amount,
                            'active' => 1,
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                            'created_on' => time(),
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_increase_account_balance($group_id,$account_id,$transaction_type,$amount)){
                                return TRUE;
                                
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        }
                    }else{
                        return FALSE;
                    }
                }else if(in_array($transaction_type,$this->external_lending_loan_repayment_deposit_transaction_types)){
                    if($debtor_id&&$debtor_loan_id&&$debtor_loan_repayment_id){
                        $input = array(
                            'transaction_type' => $this->deposit_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'deposit_id' => $deposit_id,
                            'group_id' => $group_id,
                            'member_id' => $member_id,
                            'contribution_id' => $contribution_id,
                            'fine_category_id' => $fine_category_id,
                            'depositor_id' => $depositor_id,
                            'stock_sale_id' => $stock_sale_id,
                            'money_market_investment_id' => $money_market_investment_id,
                            'asset_id' => $asset_id,
                            'bank_loan_id' => $bank_loan_id,
                            'description' => $description,
                            'amount' => $amount,
                            'loan_id'=>$loan_id,
                            'transaction_alert_id' => $transaction_alert_id,
                            'loan_repayment_id'=>$loan_repayment_id,
                            'active' => 1,
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                            'debtor_id' => $debtor_id,
                            'debtor_loan_id' => $debtor_loan_id,
                            'debtor_loan_repayment_id' => $debtor_loan_repayment_id,
                            'created_on' => time(),
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_increase_account_balance($group_id,$account_id,$transaction_type,$amount)){
                                return TRUE;
                                
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }


    // public function bulk_deposit($group_id = 0,$deposits = array()){
    //     if($group_id&&$deposits){
    //         $values_are_valid = TRUE;
    //         $account_balance_increase_amounts_array = array();

    //         foreach($deposits as $deposit):
    //             if($deposit->group_id&&$deposit->transaction_type&&$deposit->deposit_id&&$deposit->transaction_date&&$deposit->account_id){
    //                 if(array_key_exists($deposit->account_id,$account_balance_increase_amounts_array)){

    //                 }else{
    //                     $account_balance_increase_amounts_array[$deposit->account_id] = 0;
    //                 }
    //             }
    //         endforeach;

    //         foreach($deposits as $deposit):
    //             if($deposit->group_id&&$deposit->transaction_type&&$deposit->deposit_id&&$deposit->transaction_date&&$deposit->account_id){
    //                  if(in_array($deposit->transaction_type,array_flip($this->deposit_transaction_types))){
    //                     if(in_array($deposit->transaction_type,$this->contribution_payment_transaction_types)){
    //                         if($deposit->member_id&&$deposit->contribution_id){
    //                             $input[] = array(
    //                                 'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
    //                                 'transaction_date' => $deposit->transaction_date,
    //                                 'account_id' => $deposit->account_id,
    //                                 'deposit_id' => $deposit->deposit_id,
    //                                 'group_id' => $deposit->group_id,
    //                                 'member_id' => $deposit->member_id,
    //                                 'contribution_id' => $deposit->contribution_id,
    //                                 'description' => $deposit->description,
    //                                 'amount' => currency($deposit->amount),
    //                                 'active' => 1,
    //                                 'transaction_alert_id' => $deposit->transaction_alert_id,
    //                                 'created_on' => time(),
    //                                 'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
    //                                 'checkoff_id' => 0,
    //                             );
    //                         }else{
    //                             $values_are_valid = FALSE;
    //                             break; 
    //                         }
    //                     }else if(in_array($deposit->transaction_type,$this->fine_payment_transaction_types)){
    //                         if($deposit->member_id&&($deposit->contribution_id||$deposit->fine_category_id)){
    //                             $input[] = array(
    //                                 'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
    //                                 'transaction_date' => $deposit->transaction_date,
    //                                 'account_id' => $deposit->account_id,
    //                                 'deposit_id' => $deposit->deposit_id,
    //                                 'group_id' => $deposit->group_id,
    //                                 'member_id' => $deposit->member_id,
    //                                 'contribution_id' => $deposit->contribution_id,
    //                                 'fine_category_id' => $deposit->fine_category_id,
    //                                 'description' => $deposit->description,
    //                                 'amount' => currency($deposit->amount),
    //                                 'transaction_alert_id' => $deposit->transaction_alert_id,
    //                                 'active' => 1,
    //                                 'created_on' => time(),
    //                                 'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
    //                             );
    //                         }else{
    //                             $values_are_valid = FALSE;
    //                             break;
    //                         }
    //                     }else if(in_array($deposit->transaction_type,$this->miscellaneous_payment_transaction_types)){
    //                         if($deposit->member_id){
    //                             $input[] = array(
    //                                 'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
    //                                 'transaction_date' => $deposit->transaction_date,
    //                                 'account_id' => $deposit->account_id,
    //                                 'deposit_id' => $deposit->deposit_id,
    //                                 'group_id' => $deposit->group_id,
    //                                 'member_id' => $deposit->member_id,
    //                                 'contribution_id' => $deposit->contribution_id,
    //                                 'fine_category_id' => $deposit->fine_category_id,
    //                                 'description' => $deposit->description,
    //                                 'transaction_alert_id' => $deposit->transaction_alert_id,
    //                                 'amount' => currency($deposit->amount),
    //                                 'active' => 1,
    //                                 'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
    //                                 'created_on' => time(),
    //                             );
    //                         }else{
    //                             $values_are_valid = FALSE;
    //                             break;
    //                         }
    //                     }else if(in_array($deposit->transaction_type,$this->income_deposit_transaction_types)){
    //                         if($deposit->depositor_id&&$deposit->income_category_id){
    //                             $input[] = array(
    //                                 'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
    //                                 'transaction_date' => $deposit->transaction_date,
    //                                 'account_id' => $deposit->account_id,
    //                                 'deposit_id' => $deposit->deposit_id,
    //                                 'group_id' => $deposit->group_id,
    //                                 'member_id' => $deposit->member_id,
    //                                 'contribution_id' => $deposit->contribution_id,
    //                                 'fine_category_id' => $deposit->fine_category_id,
    //                                 'income_category_id' => $deposit->income_category_id,
    //                                 'depositor_id' => $deposit->depositor_id,
    //                                 'transaction_alert_id' => $deposit->transaction_alert_id,
    //                                 'description' => $deposit->description,
    //                                 'amount' => currency($deposit->amount),
    //                                 'active' => 1,
    //                                 'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
    //                                 'created_on' => time(),
    //                             );
    //                         }else{
    //                             $values_are_valid = FALSE;
    //                             break;
    //                         }
    //                     }else if(in_array($deposit->transaction_type,$this->stock_sale_deposit_transaction_types)){
    //                         if($deposit->stock_sale_id){
    //                             $input[] = array(
    //                                 'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
    //                                 'transaction_date' => $deposit->transaction_date,
    //                                 'account_id' => $deposit->account_id,
    //                                 'deposit_id' => $deposit->deposit_id,
    //                                 'group_id' => $deposit->group_id,
    //                                 'member_id' => $deposit->member_id,
    //                                 'contribution_id' => $deposit->contribution_id,
    //                                 'fine_category_id' => $deposit->fine_category_id,
    //                                 'depositor_id' => $deposit->depositor_id,
    //                                 'stock_sale_id' => $deposit->stock_sale_id,
    //                                 'transaction_alert_id' => $deposit->transaction_alert_id,
    //                                 'description' => $deposit->description,
    //                                 'amount' => currency($deposit->amount),
    //                                 'active' => 1,
    //                                 'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
    //                                 'created_on' => time(),
    //                             );
    //                         }else{
    //                             $values_are_valid = FALSE;
    //                             break;
    //                         }
    //                     }else if(in_array($deposit->transaction_type,$this->money_market_investment_cash_in_deposit_transaction_types)){
    //                         if($deposit->money_market_investment_id){
    //                             $input[] = array(
    //                                 'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
    //                                 'transaction_date' => $deposit->transaction_date,
    //                                 'account_id' => $deposit->account_id,
    //                                 'deposit_id' => $deposit->deposit_id,
    //                                 'group_id' => $deposit->group_id,
    //                                 'member_id' => $deposit->member_id,
    //                                 'contribution_id' => $deposit->contribution_id,
    //                                 'fine_category_id' => $deposit->fine_category_id,
    //                                 'depositor_id' => $deposit->depositor_id,
    //                                 'stock_sale_id' => $deposit->stock_sale_id,
    //                                 'money_market_investment_id' => $deposit->money_market_investment_id,
    //                                 'description' => $deposit->description,
    //                                 'transaction_alert_id' => $deposit->transaction_alert_id,
    //                                 'amount' => currency($deposit->amount),
    //                                 'active' => 1,
    //                                 'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
    //                                 'created_on' => time(),
    //                             );
    //                         }else{
    //                             $values_are_valid = FALSE;
    //                             break;
    //                         }
    //                     }else if(in_array($deposit->transaction_type,$this->asset_sale_deposit_transaction_types)){
    //                         if($deposit->asset_id){
    //                             $input[] = array(
    //                                 'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
    //                                 'transaction_date' => $deposit->transaction_date,
    //                                 'account_id' => $deposit->account_id,
    //                                 'deposit_id' => $deposit->deposit_id,
    //                                 'group_id' => $deposit->group_id,
    //                                 'member_id' => $deposit->member_id,
    //                                 'contribution_id' => $deposit->contribution_id,
    //                                 'fine_category_id' => $deposit->fine_category_id,
    //                                 'depositor_id' => $deposit->depositor_id,
    //                                 'stock_sale_id' => $deposit->stock_sale_id,
    //                                 'money_market_investment_id' => $deposit->money_market_investment_id,
    //                                 'asset_id' => $deposit->asset_id,
    //                                 'transaction_alert_id' => $deposit->transaction_alert_id,
    //                                 'description' => $deposit->description,
    //                                 'amount' => currency($deposit->amount),
    //                                 'active' => 1,
    //                                 'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
    //                                 'created_on' => time(),
    //                             );
    //                         }else{
    //                             $values_are_valid = FALSE;
    //                             break;
    //                         }
    //                     }else if(in_array($deposit->transaction_type,$this->bank_loan_disbursement_deposit_transaction_types)){
    //                         if($deposit->bank_loan_id){
    //                             $input = array(
    //                                 'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
    //                                 'transaction_date' => $deposit->transaction_date,
    //                                 'account_id' => $deposit->account_id,
    //                                 'deposit_id' => $deposit->deposit_id,
    //                                 'group_id' => $deposit->group_id,
    //                                 'member_id' => $deposit->member_id,
    //                                 'contribution_id' => $deposit->contribution_id,
    //                                 'fine_category_id' => $deposit->fine_category_id,
    //                                 'depositor_id' => $deposit->depositor_id,
    //                                 'stock_sale_id' => $deposit->stock_sale_id,
    //                                 'money_market_investment_id' => $deposit->money_market_investment_id,
    //                                 'asset_id' => $deposit->asset_id,
    //                                 'bank_loan_id' => $deposit->bank_loan_id,
    //                                 'transaction_alert_id' => $deposit->transaction_alert_id,
    //                                 'description' => $deposit->description,
    //                                 'amount' => currency($deposit->amount),
    //                                 'active' => 1,
    //                                 'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
    //                                 'created_on' => time(),
    //                             );
    //                         }else{
    //                             $values_are_valid = FALSE;
    //                             break;
    //                         }
    //                     }else if(in_array($deposit->transaction_type,$this->loan_processing_income_deposit_transaction_types)){
    //                         if($deposit->loan_id){
    //                             $input[] = array(
    //                                 'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
    //                                 'transaction_date' => $deposit->transaction_date,
    //                                 'account_id' => $deposit->account_id,
    //                                 'deposit_id' => $deposit->deposit_id,
    //                                 'group_id' => $deposit->group_id,
    //                                 'member_id' => $deposit->member_id,
    //                                 'contribution_id' => $deposit->contribution_id,
    //                                 'fine_category_id' => $deposit->fine_category_id,
    //                                 'depositor_id' => $deposit->depositor_id,
    //                                 'stock_sale_id' => $deposit->stock_sale_id,
    //                                 'money_market_investment_id' => $deposit->money_market_investment_id,
    //                                 'loan_id' => $deposit->loan_id,
    //                                 'asset_id' => $deposit->asset_id,
    //                                 'bank_loan_id' => $deposit->bank_loan_id,
    //                                 'transaction_alert_id' => $deposit->transaction_alert_id,
    //                                 'description' => $deposit->description,
    //                                 'amount' => currency($deposit->amount),
    //                                 'active' => 1,
    //                                 'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
    //                                 'created_on' => time(),
    //                             );
    //                         }else{
    //                             $values_are_valid = FALSE;
    //                             break;
    //                         }
    //                     }else if(in_array($deposit->transaction_type,$this->loan_repayment_transaction_types)){
    //                         if($deposit->loan_id){
    //                             $input[] = array(
    //                                 'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
    //                                 'transaction_date' => $deposit->transaction_date,
    //                                 'account_id' => $deposit->account_id,
    //                                 'deposit_id' => $deposit->deposit_id,
    //                                 'group_id' => $deposit->group_id,
    //                                 'member_id' => $deposit->member_id,
    //                                 'contribution_id' => $deposit->contribution_id,
    //                                 'fine_category_id' => $deposit->fine_category_id,
    //                                 'depositor_id' => $deposit->depositor_id,
    //                                 'stock_sale_id' => $deposit->stock_sale_id,
    //                                 'money_market_investment_id' => $deposit->money_market_investment_id,
    //                                 'asset_id' => $deposit->asset_id,
    //                                 'bank_loan_id' => $deposit->bank_loan_id,
    //                                 'description' => $deposit->description,
    //                                 'amount' => currency($deposit->amount),
    //                                 'loan_id'=>$deposit->loan_id,
    //                                 'transaction_alert_id' => $deposit->transaction_alert_id,
    //                                 'loan_repayment_id'=>$deposit->loan_repayment_id,
    //                                 'active' => 1,
    //                                 'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
    //                                 'created_on' => time(),
    //                             );
    //                         }else{
    //                             $values_are_valid = FALSE;
    //                             break;
    //                         }
    //                     }else if(in_array($deposit->transaction_type,$this->incoming_account_transfer_withdrawal_transaction_types)){
    //                         if($deposit->account_transfer_id&&$deposit->from_account_id&&$deposit->to_account_id){
    //                             $input[] = array(
    //                                 'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
    //                                 'transaction_date' => $deposit->transaction_date,
    //                                 'account_id' => $deposit->account_id,
    //                                 'deposit_id' => $deposit->deposit_id,
    //                                 'group_id' => $deposit->group_id,
    //                                 'member_id' => $deposit->member_id,
    //                                 'contribution_id' => $deposit->contribution_id,
    //                                 'fine_category_id' => $deposit->fine_category_id,
    //                                 'depositor_id' => $deposit->depositor_id,
    //                                 'stock_sale_id' => $deposit->stock_sale_id,
    //                                 'money_market_investment_id' => $deposit->money_market_investment_id,
    //                                 'asset_id' => $deposit->asset_id,
    //                                 'bank_loan_id' => $deposit->bank_loan_id,
    //                                 'account_transfer_id' => $deposit->account_transfer_id,
    //                                 'from_account_id' => $deposit->from_account_id,
    //                                 'to_account_id' => $deposit->to_account_id,
    //                                 'transaction_alert_id' => $deposit->transaction_alert_id,
    //                                 'description' => $deposit->description,
    //                                 'amount' => currency($deposit->amount),
    //                                 'active' => 1,
    //                                 'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
    //                                 'created_on' => time(),
    //                             );
                                
    //                         }else{
    //                             $values_are_valid = FALSE;
    //                             break;
    //                         }
    //                     }else if(in_array($deposit->transaction_type,$this->external_lending_processing_income_deposit_transaction_types)){
    //                         if($deposit->debtor_loan_id && $deposit->debtor_id){
    //                             $input[] = array(
    //                                 'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
    //                                 'transaction_date' => $deposit->transaction_date,
    //                                 'account_id' => $deposit->account_id,
    //                                 'deposit_id' => $deposit->deposit_id,
    //                                 'group_id' => $deposit->group_id,
    //                                 'member_id' => $deposit->member_id,
    //                                 'debtor_id' => $deposit->debtor_id,
    //                                 'contribution_id' => $deposit->contribution_id,
    //                                 'fine_category_id' => $deposit->fine_category_id,
    //                                 'depositor_id' => $deposit->depositor_id,
    //                                 'stock_sale_id' => $deposit->stock_sale_id,
    //                                 'money_market_investment_id' => $deposit->money_market_investment_id,
    //                                 'loan_id' => $deposit->loan_id,
    //                                 'debtor_loan_id' => $deposit->debtor_loan_id,
    //                                 'asset_id' => $deposit->asset_id,
    //                                 'bank_loan_id' => $deposit->bank_loan_id,
    //                                 'transaction_alert_id' => $deposit->transaction_alert_id,
    //                                 'description' => $deposit->description,
    //                                 'amount' => currency($deposit->amount),
    //                                 'active' => 1,
    //                                 'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
    //                                 'created_on' => time(),
    //                             );
    //                         }else{
    //                             $values_are_valid = FALSE;
    //                             break;
    //                         }
    //                     }else if(in_array($deposit->transaction_type,$this->external_lending_loan_repayment_deposit_transaction_types)){
    //                         if($deposit->debtor_id&&$deposit->debtor_loan_id&&$deposit->debtor_loan_repayment_id){
    //                             $input[] = array(
    //                                 'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
    //                                 'transaction_date' => $deposit->transaction_date,
    //                                 'account_id' => $deposit->account_id,
    //                                 'deposit_id' => $deposit->deposit_id,
    //                                 'group_id' => $deposit->group_id,
    //                                 'member_id' => $deposit->member_id,
    //                                 'contribution_id' => $deposit->contribution_id,
    //                                 'fine_category_id' => $deposit->fine_category_id,
    //                                 'depositor_id' => $deposit->depositor_id,
    //                                 'stock_sale_id' => $deposit->stock_sale_id,
    //                                 'money_market_investment_id' => $deposit->money_market_investment_id,
    //                                 'asset_id' => $deposit->asset_id,
    //                                 'bank_loan_id' => $deposit->bank_loan_id,
    //                                 'description' => $deposit->description,
    //                                 'amount' => currency($deposit->amount),
    //                                 'loan_id' => $deposit->loan_id,
    //                                 'transaction_alert_id' => $deposit->transaction_alert_id,
    //                                 'loan_repayment_id' => $deposit->loan_repayment_id,
    //                                 'active' => 1,
    //                                 'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
    //                                 'debtor_id' => $deposit->debtor_id,
    //                                 'debtor_loan_id' => $deposit->debtor_loan_id,
    //                                 'debtor_loan_repayment_id' => $deposit->debtor_loan_repayment_id,
    //                                 'created_on' => time(),
    //                             );
    //                         }else{
    //                             $values_are_valid = FALSE;
    //                             break;
    //                         }
    //                     }else{
    //                         $values_are_valid = FALSE;
    //                         break;
    //                     }
    //                     $account_balance_increase_amounts_array[$deposit->account_id] += currency($deposit->amount);
    //                 }else{
    //                     $values_are_valid = FALSE;
    //                     break;
    //                 }
    //             }else{
    //                 $values_are_valid = FALSE;
    //                 break;
    //             }
    //         endforeach;

    //         if($values_are_valid){
    //             if($this->ci->transaction_statements_m->insert_batch($input)){
    //                 $result = TRUE;
    //                 foreach($account_balance_increase_amounts_array as $account_id => $amount):
    //                     if($this->_increase_bulk_account_balance($group_id,$account_id,$amount)){

    //                     }else{
    //                         $result = FALSE;
    //                     }
    //                 endforeach;
    //                 if($result){
    //                     return TRUE;
    //                 }else{
    //                     return FALSE;
    //                 }
    //             }else{
    //                 return FALSE;
    //             }
    //         }else{
    //             return FALSE;
    //         }
    //     }else{
    //         return FALSE;
    //     }
    // }
     public function bulk_deposit($group_id = 0,$deposits = array()){
        if($group_id&&$deposits){
            $values_are_valid = TRUE;
            $account_balance_increase_amounts_array = array();

            foreach($deposits as $deposit):
                if($deposit->group_id&&$deposit->transaction_type&&$deposit->deposit_id&&$deposit->transaction_date&&$deposit->account_id){
                    if(array_key_exists($deposit->account_id,$account_balance_increase_amounts_array)){

                    }else{
                        $account_balance_increase_amounts_array[$deposit->account_id] = 0;
                    }
                }
            endforeach;

            foreach($deposits as $deposit):
                if($deposit->group_id&&$deposit->transaction_type&&$deposit->deposit_id&&$deposit->transaction_date&&$deposit->account_id){
                     if(in_array($deposit->transaction_type,array_flip($this->deposit_transaction_types))){
                        if(in_array($deposit->transaction_type,$this->contribution_payment_transaction_types)){
                            if($deposit->member_id&&$deposit->contribution_id){
                                $input[] = array(
                                    'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
                                    'transaction_date' => $deposit->transaction_date,
                                    'account_id' => $deposit->account_id,
                                    'deposit_id' => $deposit->deposit_id,
                                    'group_id' => $deposit->group_id,
                                    'member_id' => $deposit->member_id,
                                    'contribution_id' => $deposit->contribution_id,
                                    'description' => $deposit->description,
                                    'amount' => currency($deposit->amount),
                                    'active' => 1,
                                    'transaction_alert_id' => $deposit->transaction_alert_id,
                                    'created_on' => time(),
                                    'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
                                );
                            }else{
                                $values_are_valid = FALSE;
                                break; 
                            }
                        }else if(in_array($deposit->transaction_type,$this->fine_payment_transaction_types)){
                            if($deposit->member_id&&($deposit->contribution_id||$deposit->fine_category_id)){
                                $input[] = array(
                                    'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
                                    'transaction_date' => $deposit->transaction_date,
                                    'account_id' => $deposit->account_id,
                                    'deposit_id' => $deposit->deposit_id,
                                    'group_id' => $deposit->group_id,
                                    'member_id' => $deposit->member_id,
                                    'contribution_id' => $deposit->contribution_id,
                                    'fine_category_id' => $deposit->fine_category_id,
                                    'description' => $deposit->description,
                                    'amount' => currency($deposit->amount),
                                    'transaction_alert_id' => $deposit->transaction_alert_id,
                                    'active' => 1,
                                    'created_on' => time(),
                                    'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
                                );
                            }else{
                                $values_are_valid = FALSE;
                                break;
                            }
                        }else if(in_array($deposit->transaction_type,$this->miscellaneous_payment_transaction_types)){
                            if($deposit->member_id){
                                $input[] = array(
                                    'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
                                    'transaction_date' => $deposit->transaction_date,
                                    'account_id' => $deposit->account_id,
                                    'deposit_id' => $deposit->deposit_id,
                                    'group_id' => $deposit->group_id,
                                    'member_id' => $deposit->member_id,
                                    'contribution_id' => $deposit->contribution_id,
                                    'fine_category_id' => $deposit->fine_category_id,
                                    'description' => $deposit->description,
                                    'transaction_alert_id' => $deposit->transaction_alert_id,
                                    'amount' => currency($deposit->amount),
                                    'active' => 1,
                                    'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
                                    'created_on' => time(),
                                );
                            }else{
                                $values_are_valid = FALSE;
                                break;
                            }
                        }else if(in_array($deposit->transaction_type,$this->income_deposit_transaction_types)){
                            if($deposit->depositor_id&&$deposit->income_category_id){
                                $input[] = array(
                                    'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
                                    'transaction_date' => $deposit->transaction_date,
                                    'account_id' => $deposit->account_id,
                                    'deposit_id' => $deposit->deposit_id,
                                    'group_id' => $deposit->group_id,
                                    'member_id' => $deposit->member_id?$deposit->member_id:'',
                                    'contribution_id' => $deposit->contribution_id?$deposit->contribution_id:'',
                                    'fine_category_id' => $deposit->fine_category_id?$deposit->fine_category_id:'',
                                    'income_category_id' => $deposit->income_category_id,
                                    'depositor_id' => $deposit->depositor_id,
                                    'transaction_alert_id' => $deposit->transaction_alert_id?$deposit->transaction_alert_id:'',
                                    'description' => $deposit->description,
                                    'amount' => currency($deposit->amount),
                                    'active' => 1,
                                    'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
                                    'created_on' => time(),
                                );
                            }else{
                                $values_are_valid = FALSE;
                                break;
                            }
                        }else if(in_array($deposit->transaction_type,$this->stock_sale_deposit_transaction_types)){
                            if($deposit->stock_sale_id){
                                $input[] = array(
                                    'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
                                    'transaction_date' => $deposit->transaction_date,
                                    'account_id' => $deposit->account_id,
                                    'deposit_id' => $deposit->deposit_id,
                                    'group_id' => $deposit->group_id,
                                    'member_id' => $deposit->member_id,
                                    'contribution_id' => $deposit->contribution_id,
                                    'fine_category_id' => $deposit->fine_category_id,
                                    'depositor_id' => $deposit->depositor_id,
                                    'stock_sale_id' => $deposit->stock_sale_id,
                                    'transaction_alert_id' => $deposit->transaction_alert_id,
                                    'description' => $deposit->description,
                                    'amount' => currency($deposit->amount),
                                    'active' => 1,
                                    'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
                                    'created_on' => time(),
                                );
                            }else{
                                $values_are_valid = FALSE;
                                break;
                            }
                        }else if(in_array($deposit->transaction_type,$this->money_market_investment_cash_in_deposit_transaction_types)){
                            if($deposit->money_market_investment_id){
                                $input[] = array(
                                    'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
                                    'transaction_date' => $deposit->transaction_date,
                                    'account_id' => $deposit->account_id,
                                    'deposit_id' => $deposit->deposit_id,
                                    'group_id' => $deposit->group_id,
                                    'member_id' => $deposit->member_id,
                                    'contribution_id' => $deposit->contribution_id,
                                    'fine_category_id' => $deposit->fine_category_id,
                                    'depositor_id' => $deposit->depositor_id,
                                    'stock_sale_id' => $deposit->stock_sale_id,
                                    'money_market_investment_id' => $deposit->money_market_investment_id,
                                    'description' => $deposit->description,
                                    'transaction_alert_id' => $deposit->transaction_alert_id,
                                    'amount' => currency($deposit->amount),
                                    'active' => 1,
                                    'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
                                    'created_on' => time(),
                                );
                            }else{
                                $values_are_valid = FALSE;
                                break;
                            }
                        }else if(in_array($deposit->transaction_type,$this->asset_sale_deposit_transaction_types)){
                            if($deposit->asset_id){
                                $input[] = array(
                                    'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
                                    'transaction_date' => $deposit->transaction_date,
                                    'account_id' => $deposit->account_id,
                                    'deposit_id' => $deposit->deposit_id,
                                    'group_id' => $deposit->group_id,
                                    'member_id' => $deposit->member_id,
                                    'contribution_id' => $deposit->contribution_id,
                                    'fine_category_id' => $deposit->fine_category_id,
                                    'depositor_id' => $deposit->depositor_id,
                                    'stock_sale_id' => $deposit->stock_sale_id,
                                    'money_market_investment_id' => $deposit->money_market_investment_id,
                                    'asset_id' => $deposit->asset_id,
                                    'transaction_alert_id' => $deposit->transaction_alert_id,
                                    'description' => $deposit->description,
                                    'amount' => currency($deposit->amount),
                                    'active' => 1,
                                    'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
                                    'created_on' => time(),
                                );
                            }else{
                                $values_are_valid = FALSE;
                                break;
                            }
                        }else if(in_array($deposit->transaction_type,$this->bank_loan_disbursement_deposit_transaction_types)){
                            if($deposit->bank_loan_id){
                                $input = array(
                                    'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
                                    'transaction_date' => $deposit->transaction_date,
                                    'account_id' => $deposit->account_id,
                                    'deposit_id' => $deposit->deposit_id,
                                    'group_id' => $deposit->group_id,
                                    'member_id' => $deposit->member_id,
                                    'contribution_id' => $deposit->contribution_id,
                                    'fine_category_id' => $deposit->fine_category_id,
                                    'depositor_id' => $deposit->depositor_id,
                                    'stock_sale_id' => $deposit->stock_sale_id,
                                    'money_market_investment_id' => $deposit->money_market_investment_id,
                                    'asset_id' => $deposit->asset_id,
                                    'bank_loan_id' => $deposit->bank_loan_id,
                                    'transaction_alert_id' => $deposit->transaction_alert_id,
                                    'description' => $deposit->description,
                                    'amount' => currency($deposit->amount),
                                    'active' => 1,
                                    'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
                                    'created_on' => time(),
                                );
                            }else{
                                $values_are_valid = FALSE;
                                break;
                            }
                        }else if(in_array($deposit->transaction_type,$this->loan_processing_income_deposit_transaction_types)){
                            if($deposit->loan_id){
                                $input[] = array(
                                    'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
                                    'transaction_date' => $deposit->transaction_date,
                                    'account_id' => $deposit->account_id,
                                    'deposit_id' => $deposit->deposit_id,
                                    'group_id' => $deposit->group_id,
                                    'member_id' => $deposit->member_id,
                                    'contribution_id' => $deposit->contribution_id,
                                    'fine_category_id' => $deposit->fine_category_id,
                                    'depositor_id' => $deposit->depositor_id,
                                    'stock_sale_id' => $deposit->stock_sale_id,
                                    'money_market_investment_id' => $deposit->money_market_investment_id,
                                    'loan_id' => $deposit->loan_id,
                                    'asset_id' => $deposit->asset_id,
                                    'bank_loan_id' => $deposit->bank_loan_id,
                                    'transaction_alert_id' => $deposit->transaction_alert_id,
                                    'description' => $deposit->description,
                                    'amount' => currency($deposit->amount),
                                    'active' => 1,
                                    'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
                                    'created_on' => time(),
                                );
                            }else{
                                $values_are_valid = FALSE;
                                break;
                            }
                        }else if(in_array($deposit->transaction_type,$this->loan_repayment_transaction_types)){
                            if($deposit->loan_id){
                                $input[] = array(
                                    'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
                                    'transaction_date' => $deposit->transaction_date,
                                    'account_id' => $deposit->account_id,
                                    'deposit_id' => $deposit->deposit_id,
                                    'group_id' => $deposit->group_id,
                                    'member_id' => $deposit->member_id,
                                    'contribution_id' => $deposit->contribution_id,
                                    'fine_category_id' => $deposit->fine_category_id,
                                    'depositor_id' => $deposit->depositor_id,
                                    'stock_sale_id' => $deposit->stock_sale_id,
                                    'money_market_investment_id' => $deposit->money_market_investment_id,
                                    'asset_id' => $deposit->asset_id,
                                    'bank_loan_id' => $deposit->bank_loan_id,
                                    'description' => $deposit->description,
                                    'amount' => currency($deposit->amount),
                                    'loan_id'=>$deposit->loan_id,
                                    'transaction_alert_id' => $deposit->transaction_alert_id,
                                    'loan_repayment_id'=>$deposit->loan_repayment_id,
                                    'active' => 1,
                                    'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
                                    'created_on' => time(),
                                );
                            }else{
                                $values_are_valid = FALSE;
                                break;
                            }
                        }else if(in_array($deposit->transaction_type,$this->incoming_account_transfer_withdrawal_transaction_types)){
                            if($deposit->account_transfer_id&&$deposit->from_account_id&&$deposit->to_account_id){
                                $input[] = array(
                                    'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
                                    'transaction_date' => $deposit->transaction_date,
                                    'account_id' => $deposit->account_id,
                                    'deposit_id' => $deposit->deposit_id,
                                    'group_id' => $deposit->group_id,
                                    'member_id' => $deposit->member_id,
                                    'contribution_id' => $deposit->contribution_id,
                                    'fine_category_id' => $deposit->fine_category_id,
                                    'depositor_id' => $deposit->depositor_id,
                                    'stock_sale_id' => $deposit->stock_sale_id,
                                    'money_market_investment_id' => $deposit->money_market_investment_id,
                                    'asset_id' => $deposit->asset_id,
                                    'bank_loan_id' => $deposit->bank_loan_id,
                                    'account_transfer_id' => $deposit->account_transfer_id,
                                    'from_account_id' => $deposit->from_account_id,
                                    'to_account_id' => $deposit->to_account_id,
                                    'transaction_alert_id' => $deposit->transaction_alert_id,
                                    'description' => $deposit->description,
                                    'amount' => currency($deposit->amount),
                                    'active' => 1,
                                    'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
                                    'created_on' => time(),
                                );
                                
                            }else{
                                $values_are_valid = FALSE;
                                break;
                            }
                        }else if(in_array($deposit->transaction_type,$this->external_lending_processing_income_deposit_transaction_types)){
                            if($deposit->debtor_loan_id && $deposit->debtor_id){
                                $input[] = array(
                                    'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
                                    'transaction_date' => $deposit->transaction_date,
                                    'account_id' => $deposit->account_id,
                                    'deposit_id' => $deposit->deposit_id,
                                    'group_id' => $deposit->group_id,
                                    'member_id' => $deposit->member_id,
                                    'debtor_id' => $deposit->debtor_id,
                                    'contribution_id' => $deposit->contribution_id,
                                    'fine_category_id' => $deposit->fine_category_id,
                                    'depositor_id' => $deposit->depositor_id,
                                    'stock_sale_id' => $deposit->stock_sale_id,
                                    'money_market_investment_id' => $deposit->money_market_investment_id,
                                    'loan_id' => $deposit->loan_id,
                                    'debtor_loan_id' => $deposit->debtor_loan_id,
                                    'asset_id' => $deposit->asset_id,
                                    'bank_loan_id' => $deposit->bank_loan_id,
                                    'transaction_alert_id' => $deposit->transaction_alert_id,
                                    'description' => $deposit->description,
                                    'amount' => currency($deposit->amount),
                                    'active' => 1,
                                    'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
                                    'created_on' => time(),
                                );
                            }else{
                                $values_are_valid = FALSE;
                                break;
                            }
                        }else if(in_array($deposit->transaction_type,$this->external_lending_loan_repayment_deposit_transaction_types)){
                            if($deposit->debtor_id&&$deposit->debtor_loan_id&&$deposit->debtor_loan_repayment_id){
                                $input[] = array(
                                    'transaction_type' => $this->deposit_transaction_types[$deposit->transaction_type],
                                    'transaction_date' => $deposit->transaction_date,
                                    'account_id' => $deposit->account_id,
                                    'deposit_id' => $deposit->deposit_id,
                                    'group_id' => $deposit->group_id,
                                    'member_id' => $deposit->member_id,
                                    'contribution_id' => $deposit->contribution_id,
                                    'fine_category_id' => $deposit->fine_category_id,
                                    'depositor_id' => $deposit->depositor_id,
                                    'stock_sale_id' => $deposit->stock_sale_id,
                                    'money_market_investment_id' => $deposit->money_market_investment_id,
                                    'asset_id' => $deposit->asset_id,
                                    'bank_loan_id' => $deposit->bank_loan_id,
                                    'description' => $deposit->description,
                                    'amount' => currency($deposit->amount),
                                    'loan_id' => $deposit->loan_id,
                                    'transaction_alert_id' => $deposit->transaction_alert_id,
                                    'loan_repayment_id' => $deposit->loan_repayment_id,
                                    'active' => 1,
                                    'is_a_back_dating_record' => $deposit->is_a_back_dating_record?1:0,
                                    'debtor_id' => $deposit->debtor_id,
                                    'debtor_loan_id' => $deposit->debtor_loan_id,
                                    'debtor_loan_repayment_id' => $deposit->debtor_loan_repayment_id,
                                    'created_on' => time(),
                                );
                            }else{
                                $values_are_valid = FALSE;
                                break;
                            }
                        }else{
                            $values_are_valid = FALSE;
                            break;
                        }
                        $account_balance_increase_amounts_array[$deposit->account_id] += currency($deposit->amount);
                    }else{
                        $values_are_valid = FALSE;
                        break;
                    }
                }else{
                    $values_are_valid = FALSE;
                    break;
                }
            endforeach;

            if($values_are_valid){
                if($this->ci->transaction_statements_m->insert_batch($input)){
                    $result = TRUE;
                    foreach($account_balance_increase_amounts_array as $account_id => $amount):
                        if($this->_increase_bulk_account_balance($group_id,$account_id,$amount)){

                        }else{
                            $result = FALSE;
                        }
                    endforeach;
                    if($result){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }


    public function void_deposit($group_id = 0,$deposit_id = 0,$transaction_type = 0,$transaction_date = 0,$account_id = 0,$amount = 0){
        if($group_id&&$deposit_id&&$transaction_type&&$transaction_date&&$account_id&&$amount){
            $transaction_statement_entry = $this->ci->transaction_statements_m->get_transaction_statement_entry_by_deposit_id($deposit_id,$group_id);
            if($transaction_statement_entry){
                $input = array(
                    'active' => 0,
                    'modified_on' => time()
                );
                if($result = $this->ci->transaction_statements_m->update($transaction_statement_entry->id,$input)){
                    if($this->_decrease_account_balance($group_id,$account_id,$transaction_type,$amount)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{            
            return FALSE;
        }
    }

    public function withdrawal(
        $group_id = 0,
        $withdrawal_id = 0,
        $transaction_type = 0,
        $transaction_date = 0,
        $account_id = 0,
        $amount = 0,
        $description = 0,
        $money_market_investment_id = 0,
        $asset_id = 0,
        $contribution_refund_id = 0,
        $stock_id = 0,
        $loan_id = 0,
        $account_transfer_id = 0,
        $from_account_id = 0,
        $to_account_id = 0,
        $transaction_alert_id = 0,
        $contribution_id = 0,
        $member_id = 0,$bank_loan_id = 0,$bank_loan_repayment_id = 0,$expense_category_id = 0,$is_a_back_dating_record = FALSE,
        $debtor_id = 0,
        $debtor_loan_id = 0,
        $is_bank_loan_interest = 0
        ){
        if($group_id&&$transaction_type&&$withdrawal_id&&$transaction_date&&$account_id){
            if(in_array($transaction_type,array_flip($this->withdrawal_transaction_types))){
                if(in_array($transaction_type,$this->expense_withdrawal_transaction_types)){
                    if($expense_category_id){
                        $input = array(
                            'transaction_type' => $this->withdrawal_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'group_id' => $group_id,
                            'withdrawal_id' => $withdrawal_id,
                            'contribution_id' => $contribution_id,
                            'member_id' => $member_id,
                            'description' => $description,
                            'transaction_alert_id' => $transaction_alert_id,
                            'expense_category_id' => $expense_category_id,
                            'amount' => $amount,
                            'active' => 1,
                            'created_on' => time(),
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_decrease_account_balance($group_id,$account_id,$this->withdrawal_transaction_types[$transaction_type],$amount)){
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        }
                    }else{
                        return FALSE;
                    }
                }else if(in_array($transaction_type,$this->money_market_investment_withdrawal_transaction_types)){
                    if($money_market_investment_id){
                        $input = array(
                            'transaction_type' => $this->withdrawal_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'group_id' => $group_id,
                            'withdrawal_id' => $withdrawal_id,
                            'money_market_investment_id' => $money_market_investment_id,
                            'transaction_alert_id' => $transaction_alert_id,
                            'description' => $description,
                            'amount' => $amount,
                            'active' => 1,
                            'created_on' => time(),
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_decrease_account_balance($group_id,$account_id,$this->withdrawal_transaction_types[$transaction_type],$amount)){
                                return TRUE;
                                
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        } 
                    }else{
                        return FALSE;  
                    }
                }else if(in_array($transaction_type,$this->asset_purchase_withdrawal_transaction_types)){
                    if($asset_id){
                        $input = array(
                            'transaction_type' => $this->withdrawal_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'group_id' => $group_id,
                            'withdrawal_id' => $withdrawal_id,
                            'transaction_alert_id' => $transaction_alert_id,
                            'money_market_investment_id' => $money_market_investment_id,
                            'asset_id' => $asset_id,
                            'description' => $description,
                            'amount' => $amount,
                            'active' => 1,
                            'created_on' => time(),
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_decrease_account_balance($group_id,$account_id,$this->withdrawal_transaction_types[$transaction_type],$amount)){
                                return TRUE;
                                
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        } 
                    }else{
                        return FALSE;  
                    }
                }else if(in_array($transaction_type,$this->contribution_refund_withdrawal_transaction_types)){
                    if($contribution_refund_id&&$member_id&&$contribution_id){
                        $input = array(
                            'transaction_type' => $this->withdrawal_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'group_id' => $group_id,
                            'withdrawal_id' => $withdrawal_id,
                            'money_market_investment_id' => $money_market_investment_id,
                            'asset_id' => $asset_id,
                            'contribution_refund_id' => $contribution_refund_id,
                            'contribution_id' => $contribution_id,
                            'member_id' => $member_id,
                            'description' => $description,
                            'transaction_alert_id' => $transaction_alert_id,
                            'amount' => $amount,
                            'active' => 1,
                            'created_on' => time(),
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_decrease_account_balance($group_id,$account_id,$this->withdrawal_transaction_types[$transaction_type],$amount)){
                                return TRUE;
                                
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        } 
                    }else{
                        return FALSE;  
                    }
                }else if(in_array($transaction_type,$this->stock_purchase_withdrawal_transaction_types)){
                    if($stock_id){
                        $input = array(
                            'transaction_type' => $this->withdrawal_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'group_id' => $group_id,
                            'withdrawal_id' => $withdrawal_id,
                            'money_market_investment_id' => $money_market_investment_id,
                            'asset_id' => $asset_id,
                            'contribution_refund_id' => $contribution_refund_id,
                            'stock_id' => $stock_id,
                            'description' => $description,
                            'transaction_alert_id' => $transaction_alert_id,
                            'amount' => $amount,
                            'active' => 1,
                            'created_on' => time(),
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_decrease_account_balance($group_id,$account_id,$this->withdrawal_transaction_types[$transaction_type],$amount)){
                                return TRUE;
                                
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        } 
                    }else{
                        return FALSE;  
                    }
                }else if(in_array($transaction_type,$this->loan_disbursement_withdrawal_transaction_types)){
                    if($loan_id&&$member_id){
                        $input = array(
                            'transaction_type' => $this->withdrawal_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'group_id' => $group_id,
                            'withdrawal_id' => $withdrawal_id,
                            'money_market_investment_id' => $money_market_investment_id,
                            'asset_id' => $asset_id,
                            'contribution_refund_id' => $contribution_refund_id,
                            'stock_id' => $stock_id,
                            'loan_id' => $loan_id,
                            'member_id' => $member_id,
                            'transaction_alert_id' => $transaction_alert_id,
                            'description' => $description,
                            'amount' => $amount,
                            'active' => 1,
                            'created_on' => time(),
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_decrease_account_balance($group_id,$account_id,$this->withdrawal_transaction_types[$transaction_type],$amount)){
                                return TRUE;
                                
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        } 
                    }else{
                        return FALSE;  
                    }
                }else if(in_array($transaction_type,$this->outgoing_account_transfer_withdrawal_transaction_types)){
                    if($account_transfer_id&&$from_account_id&&$to_account_id){
                        $input = array(
                            'transaction_type' => $this->withdrawal_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'group_id' => $group_id,
                            'withdrawal_id' => $withdrawal_id,
                            'money_market_investment_id' => $money_market_investment_id,
                            'asset_id' => $asset_id,
                            'contribution_refund_id' => $contribution_refund_id,
                            'stock_id' => $stock_id,
                            'loan_id' => $loan_id,
                            'account_transfer_id' => $account_transfer_id,
                            'transaction_alert_id' => $transaction_alert_id,
                            'from_account_id' => $from_account_id,
                            'to_account_id' => $to_account_id,
                            'description' => $description,
                            'amount' => $amount,
                            'active' => 1,
                            'created_on' => time(),
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_decrease_account_balance($group_id,$from_account_id,$this->withdrawal_transaction_types[$transaction_type],$amount)){
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        } 
                    }else{
                        return FALSE;  
                    }
                }else if(in_array($transaction_type,$this->bank_loan_repayment_withdrawal_transaction_types)){
                    if($bank_loan_id&&$bank_loan_repayment_id){
                        $input = array(
                            'transaction_type' => $this->withdrawal_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'group_id' => $group_id,
                            'withdrawal_id' => $withdrawal_id,
                            'money_market_investment_id' => $money_market_investment_id,
                            'asset_id' => $asset_id,
                            'contribution_refund_id' => $contribution_refund_id,
                            'stock_id' => $stock_id,
                            'loan_id' => $loan_id,
                            'account_transfer_id' => $account_transfer_id,
                            'transaction_alert_id' => $transaction_alert_id,
                            'from_account_id' => $from_account_id,
                            'to_account_id' => $to_account_id,
                            'bank_loan_id' => $bank_loan_id,
                            'bank_loan_repayment_id' => $bank_loan_repayment_id,
                            'description' => $description,
                            'amount' => $amount,
                            'active' => 1,
                            'created_on' => time(),
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                            'is_bank_loan_interest' => $is_bank_loan_interest?1:0,
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_decrease_account_balance($group_id,$account_id,$this->withdrawal_transaction_types[$transaction_type],$amount)){
                                return TRUE;
                                
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        } 
                    }else{
                        return FALSE;  
                    }
                }else if(in_array($transaction_type,$this->external_lending_withdrawal_transaction_types)){
                    if($debtor_id&&$debtor_loan_id){
                        $input = array(
                            'transaction_type' => $this->withdrawal_transaction_types[$transaction_type],
                            'transaction_date' => $transaction_date,
                            'account_id' => $account_id,
                            'group_id' => $group_id,
                            'withdrawal_id' => $withdrawal_id,
                            'money_market_investment_id' => $money_market_investment_id,
                            'asset_id' => $asset_id,
                            'contribution_refund_id' => $contribution_refund_id,
                            'stock_id' => $stock_id,
                            'loan_id' => $loan_id,
                            'member_id' => $member_id,
                            'debtor_id' => $debtor_id,
                            'debtor_loan_id' => $debtor_loan_id,
                            'transaction_alert_id' => $transaction_alert_id,
                            'description' => $description,
                            'amount' => $amount,
                            'active' => 1,
                            'created_on' => time(),
                            'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                        );
                        if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                            if($this->_decrease_account_balance($group_id,$account_id,$this->withdrawal_transaction_types[$transaction_type],$amount)){
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;  
                        } 
                    }else{
                        return FALSE;  
                    }
                }else if(in_array($transaction_type,$this->dividend_withdrawal_transaction_types)){
                    $input = array(
                        'transaction_type' => $this->withdrawal_transaction_types[$transaction_type],
                        'transaction_date' => $transaction_date,
                        'account_id' => $account_id,
                        'group_id' => $group_id,
                        'withdrawal_id' => $withdrawal_id,
                        'money_market_investment_id' => $money_market_investment_id,
                        'asset_id' => $asset_id,
                        'contribution_refund_id' => $contribution_refund_id,
                        'stock_id' => $stock_id,
                        'loan_id' => $loan_id,
                        'member_id' => $member_id,
                        'debtor_id' => $debtor_id,
                        'debtor_loan_id' => $debtor_loan_id,
                        'transaction_alert_id' => $transaction_alert_id,
                        'description' => $description,
                        'amount' => $amount,
                        'active' => 1,
                        'created_on' => time(),
                        'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                    );
                    if($transaction_statement_entry_id = $this->ci->transaction_statements_m->insert($input)){
                        if($this->_decrease_account_balance($group_id,$account_id,$this->withdrawal_transaction_types[$transaction_type],$amount)){
                            return TRUE;
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;  
                    } 
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }


    public function void_withdrawal($group_id = 0,$withdrawal_id = 0,$transaction_type = 0,$transaction_date = 0,$account_id = 0,$amount = 0){
        if($group_id&&$withdrawal_id&&$transaction_type&&$transaction_date&&$account_id&&is_numeric($amount)){
            $transaction_statement_entry = $this->ci->transaction_statements_m->get_transaction_statement_entry_by_withdrawal_id($withdrawal_id,$group_id);
            if($transaction_statement_entry){
                $input = array(
                    'active' => 0,
                    'modified_on' => time()
                );
                if($result = $this->ci->transaction_statements_m->update($transaction_statement_entry->id,$input)){
                    if($this->_increase_account_balance($group_id,$account_id,$transaction_statement_entry->transaction_type,$amount)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{

                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{         
            return FALSE;
        }
    }

    private function _increase_account_balance($group_id = 0,$account_id = 0,$transaction_type = 0,$amount = 0){
        if($account_id&&$transaction_type){
            if(in_array($transaction_type,array_flip($this->bank_deposit_transaction_types))||in_array($transaction_type,$this->bank_withdrawal_transaction_types)){
                $account_id = str_replace('bank-','',$account_id);
                $current_balance = $this->ci->bank_accounts_m->get_group_bank_account_current_balance($account_id,$group_id);
                $current_balance += $amount;
                $input = array(
                    'current_balance' => $current_balance,
                    'modified_on' => time(),
                );
                if($result = $this->ci->bank_accounts_m->update($account_id,$input)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else if(in_array($transaction_type,array_flip($this->sacco_deposit_transaction_types))||in_array($transaction_type,$this->sacco_withdrawal_transaction_types)){
                $account_id = str_replace('sacco-','',$account_id);
                $current_balance = $this->ci->sacco_accounts_m->get_group_sacco_account_current_balance($account_id,$group_id);
                $current_balance += $amount;
                $input = array(
                    'current_balance' => $current_balance,
                    'modified_on' => time(),
                );
                if($result = $this->ci->sacco_accounts_m->update($account_id,$input)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else if(in_array($transaction_type,array_flip($this->mobile_deposit_transaction_types))||in_array($transaction_type,$this->mobile_withdrawal_transaction_types)){
                $account_id = str_replace('mobile-','',$account_id);
                $current_balance = $this->ci->mobile_money_accounts_m->get_group_mobile_money_account_current_balance($account_id,$group_id);
                $current_balance += $amount;
                $input = array(
                    'current_balance' => $current_balance,
                    'modified_on' => time(),
                );
                if($result = $this->ci->mobile_money_accounts_m->update($account_id,$input)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else if(in_array($transaction_type,array_flip($this->petty_deposit_transaction_types))||in_array($transaction_type,$this->petty_withdrawal_transaction_types)){
                $account_id = str_replace('petty-','',$account_id);
                $current_balance = $this->ci->petty_cash_accounts_m->get_group_petty_cash_account_current_balance($account_id,$group_id);
                $current_balance += $amount;
                $input = array(
                    'current_balance' => $current_balance,
                    'modified_on' => time(),
                );
                if($result = $this->ci->petty_cash_accounts_m->update($account_id,$input)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    private function _increase_bulk_account_balance($group_id = 0,$account_id = 0,$amount = 0){
        if($account_id&&$group_id&&$amount){
            if(preg_match('/bank-/', $account_id)){
                $account_id = str_replace('bank-','',$account_id);
                $current_balance = $this->ci->bank_accounts_m->get_group_bank_account_current_balance($account_id,$group_id);
                $current_balance += $amount;
                $input = array(
                    'current_balance' => $current_balance,
                    'modified_on' => time(),
                );
                if($result = $this->ci->bank_accounts_m->update($account_id,$input)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else if(preg_match('/sacco-/', $account_id)){
                $account_id = str_replace('sacco-','',$account_id);
                $current_balance = $this->ci->sacco_accounts_m->get_group_sacco_account_current_balance($account_id,$group_id);
                $current_balance += $amount;
                $input = array(
                    'current_balance' => $current_balance,
                    'modified_on' => time(),
                );
                if($result = $this->ci->sacco_accounts_m->update($account_id,$input)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else if(preg_match('/mobile-/', $account_id)){
                $account_id = str_replace('mobile-','',$account_id);
                $current_balance = $this->ci->mobile_money_accounts_m->get_group_mobile_money_account_current_balance($account_id,$group_id);
                $current_balance += $amount;
                $input = array(
                    'current_balance' => $current_balance,
                    'modified_on' => time(),
                );
                if($result = $this->ci->mobile_money_accounts_m->update($account_id,$input)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else if(preg_match('/petty-/', $account_id)){
                $account_id = str_replace('petty-','',$account_id);
                $current_balance = $this->ci->petty_cash_accounts_m->get_group_petty_cash_account_current_balance($account_id,$group_id);
                $current_balance += $amount;
                $input = array(
                    'current_balance' => $current_balance,
                    'modified_on' => time(),
                );
                if($result = $this->ci->petty_cash_accounts_m->update($account_id,$input)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    private function _decrease_account_balance($group_id = 0,$account_id = 0,$transaction_type = 0,$amount = 0){
        if($account_id&&$transaction_type){
            if(in_array($transaction_type,array_flip($this->bank_deposit_transaction_types))||in_array($transaction_type,$this->bank_withdrawal_transaction_types)){
                $account_id = str_replace('bank-','',$account_id);
                $current_balance = $this->ci->bank_accounts_m->get_group_bank_account_current_balance($account_id,$group_id);
                $current_balance -= $amount;
                $input = array(
                    'current_balance' => $current_balance,
                    'modified_on' => time(),
                );
                if($result = $this->ci->bank_accounts_m->update($account_id,$input)){
                    //die("Am in");
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else if(in_array($transaction_type,array_flip($this->sacco_deposit_transaction_types))||in_array($transaction_type,$this->sacco_withdrawal_transaction_types)){
                $account_id = str_replace('sacco-','',$account_id);
                $current_balance = $this->ci->sacco_accounts_m->get_group_sacco_account_current_balance($account_id,$group_id);
                $current_balance -= $amount;
                $input = array(
                    'current_balance' => $current_balance,
                    'modified_on' => time(),
                );
                if($result = $this->ci->sacco_accounts_m->update($account_id,$input)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else if(in_array($transaction_type,array_flip($this->mobile_deposit_transaction_types))||in_array($transaction_type,$this->mobile_withdrawal_transaction_types)){
                $account_id = str_replace('mobile-','',$account_id);
                $current_balance = $this->ci->mobile_money_accounts_m->get_group_mobile_money_account_current_balance($account_id,$group_id);
                $current_balance -= $amount;
                $input = array(
                    'current_balance' => $current_balance,
                    'modified_on' => time(),
                );
                if($result = $this->ci->mobile_money_accounts_m->update($account_id,$input)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else if(in_array($transaction_type,array_flip($this->petty_deposit_transaction_types))||in_array($transaction_type,$this->petty_withdrawal_transaction_types)){
                $account_id = str_replace('petty-','',$account_id);
                $current_balance = $this->ci->petty_cash_accounts_m->get_group_petty_cash_account_current_balance($account_id,$group_id);
                $current_balance -= $amount;
                $input = array(
                    'current_balance' => $current_balance,
                    'modified_on' => time(),
                );
                if($result = $this->ci->petty_cash_accounts_m->update($account_id,$input)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    private function _update_account_balances($group_id = 0,$account_id = 0,$transaction_date = 0){
        if($account_id&&$group_id&&$transaction_date){
            $account_transaction_statement_entries = $this->ci->transaction_statements_m->get_transaction_statement_entries_to_reconcile($account_id,$group_id,$transaction_date);
            $result = TRUE;
            $balance = $this->ci->transaction_statements_m->get_account_balance($account_id,$group_id,$transaction_date);
            foreach($account_transaction_statement_entries as $account_transaction_statement_entry){
                if(in_array($account_transaction_statement_entry->transaction_type,$this->deposit_transaction_types)){
                    $balance+=$account_transaction_statement_entry->amount;
                }else if(in_array($account_transaction_statement_entry->transaction_type,$this->withdrawal_transaction_types)){
                    $balance-=$account_transaction_statement_entry->amount;
                }else{
                    $result = FALSE;
                }
                $input = array(
                    'balance'=>$balance,
                    'modified_on'=>time()
                );
                if($this->ci->transaction_statements_m->update($account_transaction_statement_entry->id,$input)){
                    //do nothing for now
                }else{
                    $result = FALSE;
                }
            }

            return $result;
        }else{
            return FALSE;
        }
    }

    public function mark_transaction_alert_as_reconciled($id = 0,$group_id = 0){
        if($id&&$group_id){   
            $input = array(
                'reconciled'=>1,
                'group_id'=>$group_id,
                'modified_on'=>time()
            );
            if($this->ci->transaction_alerts_m->update($id,$input)){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    // public function void_group_deposit($id = 0,$post = array(),$get_deposit_siblings = TRUE,$group_id = 0,$user=array()){
    //     if($id&&$post&&$group_id){
    //         $result = TRUE;
    //         /***
    //             if($post->account_id){
    //                 $account_id = $post->account_id;
    //                 if(preg_match('/bank-/', $account_id)){
    //                     $account_id = str_replace('bank-','', $account_id);
    //                     $account = $this->ci->bank_accounts_m->get_group_bank_account($account_id,$group_id);
    //                     if($account){
    //                         if($account->is_default){
    //                             $this->ci->session->set_flashdata('error','Transaction was paid via E-Wallet and can not be voided.');
    //                             return FALSE;
    //                         }
    //                     }
    //                 }
    //             }
    //         ***/
    //         if($post->type==1||$post->type==2||$post->type==3||$post->type==7){
    //             if($this->void_contribution_payment($id,$post)){
    //                 $this->ci->session->set_flashdata('success','Contribution payment successfully voided.');
    //             }else{
    //                 $this->ci->session->set_flashdata('error','Contribution payment could not be voided.');
    //                 $result = FALSE;
    //             }
    //         }else if($post->type==4||$post->type==5||$post->type==6||$post->type==8){
    //             if($this->void_fine_payment($id,$post)){
    //                 $this->ci->session->set_flashdata('success','Fine payment successfully voided.');
    //             }else{
    //                 $this->ci->session->set_flashdata('error','Fine payment could not be voided.');
    //                 $result = FALSE;
    //             }
    //         }else if($post->type==9||$post->type==10||$post->type==11||$post->type==12){
    //             if($this->void_miscellaneous_payment($id,$post)){
    //                 $this->ci->session->set_flashdata('success','Miscellaneous payment successfully voided.');
    //             }else{
    //                 $this->ci->session->set_flashdata('error','Miscellaneous payment could not be voided.');
    //                 $result = FALSE;

    //             }
    //         }else if($post->type==13||$post->type==14||$post->type==15||$post->type==16){
    //             if($this->void_income_deposit($id,$post)){
    //                 $this->ci->session->set_flashdata('success','Income deposit successfully voided.');
    //             }else{
    //                 $this->ci->session->set_flashdata('error','Income deposit could not be voided.');
    //                 $result = FALSE;
    //             }
    //         }else if($post->type==17||$post->type==18||$post->type==19||$post->type==20){
    //             if($this->void_loan_repayment_deposit($id,$post,'','','',1)){
    //                 $this->ci->session->set_flashdata('success','Loan repayment successfully voided.');
    //             }else{
    //                 $this->ci->session->set_flashdata('error','Loan repayment was not voided.');
    //                 $result = FALSE;
    //             }
    //         }else if($post->type==21||$post->type==22||$post->type==23||$post->type==24){
    //             if($this->void_bank_loan($post->bank_loan_id,$group_id)){
    //                 $this->ci->session->set_flashdata('success','Bank loan deposit successfully voided.');
    //             }else{
    //                 $this->ci->session->set_flashdata('error','Bank loan deposit could not be voided.');
    //                 $result = FALSE;
    //             }
    //         }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){

    //             if($this->void_stock_sale_deposit($id,$post)){
    //                 $this->ci->session->set_flashdata('success','Stock sale successfully voided.');
    //             }else{
    //                 $this->ci->session->set_flashdata('error','Stock sale could not be voided.');
    //                 $result = FALSE;
    //             }
    //         }else if($post->type==29||$post->type==30||$post->type==31||$post->type==32){
    //             if($this->void_money_market_investment_cash_in_deposit($id,$post)){
    //                 $this->ci->session->set_flashdata('success','Money market investment cash in successfully voided.');
    //             }else{
    //                 $this->ci->session->set_flashdata('error','Money market investment cash in could not be voided.');
    //                 $result = FALSE;
    //             }
    //         }else if($post->type==33||$post->type==34||$post->type==35||$post->type==36){
    //             if($this->void_asset_sale_deposit($id,$post)){
    //                 $this->ci->session->set_flashdata('success','Asset sale successfully voided.');
    //             }else{
    //                 $this->ci->session->set_flashdata('error','Asset sale could not be voided.');
    //                 $result = FALSE;
    //             }
    //         }else if($post->type==37||$post->type==38||$post->type==39||$post->type==40){
    //             if($this->void_account_transfer($post->account_transfer_id,$post->group_id)){
    //                 $this->ci->session->set_flashdata('success','Account transfer successfully voided.');
    //             }else{
    //                 $this->ci->session->set_flashdata('error','Account transfer could not be voided.');
    //                 $result = FALSE;
    //             }
    //         }
    //         else if($post->type==41||$post->type==42||$post->type==43||$post->type==44){
    //             if($this->void_loan_processing_income($post->id,$post,'',TRUE,$user)){
    //                 $this->ci->session->set_flashdata('success','Loan processing income successfully voided.');
    //             }else{
    //                 $this->ci->session->set_flashdata('error','Loan processing income could not be voided.');
    //                 $result = FALSE;
    //             }
    //         }else if($post->type==49||$post->type==50||$post->type==51||$post->type==52){
    //             if($this->void_external_loan_repayment_deposit($id,$post)){
    //                 $this->ci->session->set_flashdata('success','External Loan repayment successfully voided.');
    //             }else{
    //                 $this->ci->session->set_flashdata('error','External Loan repayment was not voided.');
    //                 $result = FALSE;
    //             }
    //         }
    //         if($post->transaction_alert_id){
    //             $input = array(
    //                 'reconciled'=>0,
    //                 'modified_on'=>time()
    //             );
    //             if($this->ci->transaction_alerts_m->update($post->transaction_alert_id,$input)){
    //                 if($get_deposit_siblings){
    //                     $sibling_deposits = $this->ci->deposits_m->get_group_deposit_siblings_by_transaction_alert_id($post->transaction_alert_id,$post->group_id);
    //                 }
    //                 if(isset($sibling_deposits)){
    //                     foreach ($sibling_deposits as $deposit) {
    //                         # code...
    //                         if($this->void_group_deposit($deposit->id,$deposit,FALSE,$deposit->group_id)){

    //                         }else{
    //                             $result = FALSE;
    //                         }
    //                     }
    //                 }
    //             }else{
    //                 $result = FALSE;
    //             }
    //         }
    //         return $result;
    //     }else{
    //         return FALSE;
    //     }
    // }


    public function void_group_deposit($id = 0,$post = array(),$get_deposit_siblings = TRUE,$group_id = 0,$user=array()){
        if($id&&$post){
            $result = TRUE;
            /***
                if($post->account_id){
                    $account_id = $post->account_id;
                    if(preg_match('/bank-/', $account_id)){
                        $account_id = str_replace('bank-','', $account_id);
                        $account = $this->ci->bank_accounts_m->get_group_bank_account($account_id,$group_id);
                        if($account){
                            if($account->is_default){
                                $this->ci->session->set_flashdata('error','Transaction was paid via E-Wallet and can not be voided.');
                                return FALSE;
                            }
                        }
                    }
                }
            ***/
            if($post->type==1||$post->type==2||$post->type==3||$post->type==7){
                if($this->void_contribution_payment($id,$post)){
                    $this->ci->session->set_flashdata('success','Contribution payment successfully voided.');
                }else{
                    //$this->ci->session->set_flashdata('error','Contribution payment could not be voided.');
                    $result = FALSE;
                }
            }else if($post->type==4||$post->type==5||$post->type==6||$post->type==8){
                if($this->void_fine_payment($id,$post)){
                    $this->ci->session->set_flashdata('success','Fine payment successfully voided.');
                }else{
                    $this->ci->session->set_flashdata('error','Fine payment could not be voided.');
                    $result = FALSE;
                }
            }else if($post->type==9||$post->type==10||$post->type==11||$post->type==12){
                if($this->void_miscellaneous_payment($id,$post)){
                    $this->ci->session->set_flashdata('success','Miscellaneous payment successfully voided.');
                }else{
                    $this->ci->session->set_flashdata('error','Miscellaneous payment could not be voided.');
                    $result = FALSE;
                }
            }else if($post->type==13||$post->type==14||$post->type==15||$post->type==16){
                if($this->void_income_deposit($id,$post)){
                    $this->ci->session->set_flashdata('success','Income deposit successfully voided.');
                }else{
                    $this->ci->session->set_flashdata('error','Income deposit could not be voided.');
                    $result = FALSE;
                }
            }else if($post->type==17||$post->type==18||$post->type==19||$post->type==20){
                if($this->void_loan_repayment_deposit($id,$post,'','','',1)){
                    $this->ci->session->set_flashdata('success','Loan repayment successfully voided.');
                }else{
                    $this->ci->session->set_flashdata('error','Loan repayment was not voided.');
                    $result = FALSE;
                }
            }else if($post->type==21||$post->type==22||$post->type==23||$post->type==24){
                if($this->void_bank_loan($post->bank_loan_id,$group_id)){
                    $this->ci->session->set_flashdata('success','Bank loan deposit successfully voided.');
                }else{
                    $this->ci->session->set_flashdata('error','Bank loan deposit could not be voided.');
                    $result = FALSE;
                }
            }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){

                if($this->void_stock_sale_deposit($id,$post)){
                    $this->ci->session->set_flashdata('success','Stock sale successfully voided.');
                }else{
                    $this->ci->session->set_flashdata('error','Stock sale could not be voided.');
                    $result = FALSE;
                }
            }else if($post->type==29||$post->type==30||$post->type==31||$post->type==32){
                if($this->void_money_market_investment_cash_in_deposit($id,$post)){
                    $this->ci->session->set_flashdata('success','Money market investment cash in successfully voided.');
                }else{
                    $this->ci->session->set_flashdata('error','Money market investment cash in could not be voided.');
                    $result = FALSE;
                }
            }else if($post->type==33||$post->type==34||$post->type==35||$post->type==36){
                if($this->void_asset_sale_deposit($id,$post)){
                    $this->ci->session->set_flashdata('success','Asset sale successfully voided.');
                }else{
                    $this->ci->session->set_flashdata('error','Asset sale could not be voided.');
                    $result = FALSE;
                }
            }else if($post->type==37||$post->type==38||$post->type==39||$post->type==40){
                if($this->void_account_transfer($post->account_transfer_id,$post->group_id)){
                    $this->ci->session->set_flashdata('success','Account transfer successfully voided.');
                }else{
                    $this->ci->session->set_flashdata('error','Account transfer could not be voided.');
                    $result = FALSE;
                }
            }
            else if($post->type==41||$post->type==42||$post->type==43||$post->type==44){
                if($this->void_loan_processing_income($post->id,$post,'',TRUE,$user)){
                    $this->ci->session->set_flashdata('success','Loan processing income successfully voided.');
                }else{
                    $this->ci->session->set_flashdata('error','Loan processing income could not be voided.');
                    $result = FALSE;
                }
            }else if($post->type==49||$post->type==50||$post->type==51||$post->type==52){
                if($this->void_external_loan_repayment_deposit($id,$post)){
                    $this->ci->session->set_flashdata('success','External Loan repayment successfully voided.');
                }else{
                    $this->ci->session->set_flashdata('error','External Loan repayment was not voided.');
                    $result = FALSE;
                }
            }
            if($post->transaction_alert_id){
                $input = array(
                    'reconciled'=>0,
                    'auto_reconcile'=>0,
                    'modified_on'=>time()
                );
                if($this->ci->transaction_alerts_m->update($post->transaction_alert_id,$input)){                    
                    if($get_deposit_siblings){
                        $sibling_deposits = $this->ci->deposits_m->get_group_deposit_siblings_by_transaction_alert_id($post->transaction_alert_id,$post->group_id);
                    }
                    if(isset($sibling_deposits)){
                        foreach ($sibling_deposits as $deposit) {
                            # code...
                            if($this->void_group_deposit($deposit->id,$deposit,FALSE,$deposit->group_id)){

                            }else{
                                $result = FALSE;
                            }
                        }
                    }
                }else{
                    $result = FALSE;
                }
            }
            return $result;
        }else{
            return FALSE;
        }
    }

    public function void_group_withdrawal($id = 0,$post = array(),$get_withdrawal_siblings = TRUE,$group_id = 0){
        if($id&&$post){
            $result = TRUE;
          
            // if($post->account_id){
            //     $account_id = $post->account_id;
            //     if(preg_match('/bank-/', $account_id)){
            //         $account_id = str_replace('bank-','', $account_id);
            //         $account = $this->ci->bank_accounts_m->get_group_bank_account($account_id,$group_id);
            //         if($account){
            //             if($account->is_default){
            //                 $this->ci->session->set_flashdata('error','Transaction was paid via E-Wallet and can not be voided.');
            //                 return FALSE;
            //             }
            //         }
            //     }
            // }
            if($post->type==1||$post->type==2||$post->type==3||$post->type==4){
                if($this->void_expense_withdrawal($id,$post)){
                    $this->ci->session->set_flashdata('success','Expense successfully voided.');
                }else{
                    $this->ci->session->set_flashdata('error','Expense could not be voided.');
                    $result = FALSE;
                }
            }else if($post->type==5||$post->type==6||$post->type==7||$post->type==8){
                if($this->void_asset_purchase_payment($id,$post)){
                    $this->ci->session->set_flashdata('success','Asset purchase payment successfully voided.');
                }else{
                    $this->ci->session->set_flashdata('error','Asset purchase payment could not be voided.');
                    $result = FALSE;
                }
            }else if($post->type==9||$post->type==10||$post->type==11||$post->type==12){
                
                if($this->ci->loan->void_loan($post->loan_id,$this->ci->ion_auth->get_user($post->created_by?:1),TRUE)){
                    $this->ci->session->set_flashdata('success','Loan successfully voided.');
                }else{
                    $this->ci->session->set_flashdata('error','Loan could not be voided.');
                    $result = FALSE;
                }
            }else if($post->type==17||$post->type==18||$post->type==19||$post->type==20){
                if($this->void_money_market_investment_withdrawal($id,$post)){
                    $this->ci->session->set_flashdata('success','Money market investment deposit successfully voided.');
                }else{
                    $this->ci->session->set_flashdata('error','Money market investment deposit could not be voided.');
                    $result = FALSE;
                }
            }else if($post->type==21||$post->type==22||$post->type==23||$post->type==24){
                if($this->void_contribution_refund($post->contribution_refund_id)){
                    $this->ci->session->set_flashdata('success','Contribution refund withdrawal voided successfully');
                }else{
                    $this->ci->session->set_flashdata('error','Contribution refund withdrawal not voided successfully');
                    $result = FALSE;
                }
            }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){
                if($this->ci->loan->void_bank_loan_repayment($post->bank_loan_repayment_id,$post->created_by)){
                    $this->ci->session->set_flashdata('success','Bank loan voided successfully');
                }else{
                    $this->ci->session->set_flashdata('error','Bank loan voided not voided successfully');
                    $result = FALSE;
                }
            }else if($post->type==13||$post->type==14||$post->type==15||$post->type==16){
                if($this->void_stock_purchase($post->stock_id,$group_id)){
                    $this->ci->session->set_flashdata('success','Stock purchase withdrawal voided successfully');
                }else{
                    $this->ci->session->set_flashdata('error','Stock purchase withdrawal could not be voided successfully');
                    $result = FALSE;
                }
            }else if($post->type==29||$post->type==30||$post->type==31||$post->type==32){
                if($this->void_account_transfer($post->account_transfer_id,$post->group_id)){
                    $this->ci->session->set_flashdata('success','Account transfer voided successfully');
                }else{
                    $this->ci->session->set_flashdata('error','Account transfer could not be voided successfully');
                    $result = FALSE;
                }
            }else if($post->type==33||$post->type==34||$post->type==35||$post->type==36){
                if($this->ci->loan->void_external_loan($post->debtor_loan_id,$group_id)){
                    $this->ci->session->set_flashdata('success','Loan successfully voided.');
                }else{
                    $this->ci->session->set_flashdata('error','Loan could not be voided.');
                    $result = FALSE;
                }
            }else if($post->type==37||$post->type==38||$post->type==39||$post->type==40){
                if($this->void_dividend_withdrawal($id,$post)){
                    $this->ci->session->set_flashdata('success','Dividend successfully voided.');
                }else{
                    $this->ci->session->set_flashdata('error','Dividend could not be voided.');
                    $result = FALSE;
                }
            }
            if($post->transaction_alert_id){
                $input = array(
                    'reconciled'=>0,
                    'modified_on'=>time()
                );
                if($this->ci->transaction_alerts_m->update($post->transaction_alert_id,$input)){
                    if($get_withdrawal_siblings){
                        $sibling_withdrawals = $this->ci->withdrawals_m->get_group_withdrawal_siblings_by_transaction_alert_id($post->transaction_alert_id,$post->group_id);
                    }
                    if(isset($sibling_withdrawals)){
                        foreach ($sibling_withdrawals as $withdrawal) {
                            # code...
                            if($this->void_group_withdrawal($withdrawal->id,$withdrawal,FALSE,$withdrawal->group_id)){

                            }else{
                                $result = FALSE;
                            }
                        }
                    }
                }else{
                    $result = FALSE;
                }
            } 
            return $result; 
        }else{
            return FALSE;
        }
    }

    public function record_bank_loan_repayment_withdrawal($group_id = 0,$bank_loan_id = 0,$payment_date = 0,$account_id = 0,$payment_method = 0,$amount = 0,$description = '',$transaction_alert_id = 0,$bank_loan_repayment_id = 0,$is_a_back_dating_record = FALSE,$is_bank_loan_interest = 0){
        if(preg_match('/bank-/', $account_id)){
            $type = 25;
        }else if(preg_match('/sacco-/', $account_id)){
            $type = 26;
        }else if(preg_match('/mobile-/', $account_id)){
            $type = 27;
        }else if(preg_match('/petty-/', $account_id)){
            $type = 28;
        }else{
            $type = 0;
        }
        if($type&&$group_id&&$bank_loan_id&&$payment_date&&$account_id&&$payment_method&&$amount&&$bank_loan_repayment_id){
            $input = array(
                'type'=>$type,
                'group_id' => $group_id,
                'withdrawal_date' => $payment_date,
                'bank_loan_id' => $bank_loan_id,
                'bank_loan_repayment_id' => $bank_loan_repayment_id,
                'withdrawal_method' => $payment_method,
                'account_id' => $account_id,
                'amount' => $amount,
                'description' => $description,
                'transaction_alert_id' => $transaction_alert_id,
                'active' => 1,
                'created_on' => time(),
                'is_a_back_dating_record' => $is_a_back_dating_record?1:0,
                'is_bank_loan_interest' => $is_bank_loan_interest?1:0,
            );
            if($withdrawal_id = $this->ci->withdrawals_m->insert($input)){
                $transaction_type = $type;
                if($this->withdrawal($group_id,$withdrawal_id,$transaction_type,$payment_date,$account_id,$amount,$description,'','','','','','','','',$transaction_alert_id,'','',$bank_loan_id,$bank_loan_repayment_id,0,$is_a_back_dating_record)){
                    return TRUE;
                }else{
                    $this->ci->session->set_flashdata('warning','Could not insert withdrawal in transaction statement');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('warning','Could not insert withdrawal');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('warning','Parameter missing');
            return FALSE;
        }
    }

    public function match_loan_disbursement_to_transaction_alert($group_id = 0,$loan_id = 0,$transaction_alert_id = 0){
        if($group_id&&$loan_id&&$transaction_alert_id){
            $withdrawal = $this->ci->withdrawals_m->get_group_withdrawal_by_loan_id($loan_id,$group_id);
            if($withdrawal){
                $input = array(
                    'transaction_alert_id' => $transaction_alert_id,
                    'modified_on' => time(),
                );
                if($this->ci->withdrawals_m->update($withdrawal->id,$input)){
                    $transaction_statement_entry = $this->ci->transaction_statements_m->get_transaction_statement_entry_by_withdrawal_id($withdrawal->id,$group_id);
                    if($transaction_statement_entry){
                        $input = array(
                            'transaction_alert_id' => $transaction_alert_id,
                            'modified_on' => time(),
                        );
                        if($this->ci->transaction_statements_m->update($transaction_statement_entry->id,$input)){
                            return TRUE;
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function record_contribution_transfer($group_id = 0,$transfer_date = 0,$contribution_from_id = 0,$transfer_to = 0,$contribution_to_id = 0,$fine_category_to_id = 0,$member_id = 0,$amount = 0,$description = '',$loan_from_id=0,$loan_to_id=0,$member_to_id = 0,$member_transfer_to = 0,$member_contribution_to_id = 0,$member_fine_category_to_id = 0,$member_loan_to_id = 0){
        $member = $this->ci->members_m->get_group_member($member_id,$group_id);
        if($transfer_to==1){
            if($contribution_from_id=='loan'){
               
                if($loan_from_id&&$group_id&&$member_id&&$transfer_to&&$amount&&$transfer_date&&$contribution_to_id){
                        $input = array(
                            'transfer_date'=>strtotime($transfer_date),
                            'contribution_from_id'=>$contribution_from_id,
                            'loan_from_id'=>$loan_from_id,
                            'transfer_to'=>$transfer_to,
                            'contribution_to_id'=>$contribution_to_id,
                            'group_id'=>$group_id,
                            'member_id'=>$member_id,
                            'amount'=>$amount,
                            'description'=>$description,
                            'created_on'=>time(),
                            'active'=>1,
                        );
                        if($contribution_transfer_id = $this->ci->deposits_m->insert_contribution_transfer($input)){
                            $loan_transfer_invoice_id = $this->ci->loan->create_loan_transfer_invoice($member_id,$loan_from_id,$transfer_to,$amount,strtotime($transfer_date),$group_id,$contribution_transfer_id,2);
                            if($loan_transfer_invoice_id){
                                $input = array(
                                    'transaction_date' => strtotime($transfer_date),
                                    'transaction_type'=>29,
                                    'group_id'=>$group_id,
                                    'transfer_to'=>$transfer_to,
                                    'contribution_transfer_id'=>$contribution_transfer_id,
                                    'loan_transfer_invoice_id' => $loan_transfer_invoice_id,
                                    'contribution_from_id'=>$contribution_from_id,
                                    'loan_from_id' => $loan_from_id,
                                    'contribution_to_id'=>$contribution_to_id,
                                    'user_id'=>$member->user_id,
                                    'member_id'=>$member_id,
                                    'amount'=>$amount,
                                    'description'=>$description,        
                                    'created_on'=>time(),
                                    'active'=>1,
                                );
                                if($transfer_from_statement_entry_id = $this->ci->statements_m->insert($input)){
                                    $input = array(
                                        'transaction_date' => strtotime($transfer_date),
                                        'transaction_type'=>26,
                                        'transfer_to'=>$transfer_to,
                                        'contribution_transfer_id'=>$contribution_transfer_id,
                                        'contribution_id'=>$contribution_to_id,
                                        'contribution_from_id'=>$contribution_from_id,
                                        'loan_from_id' => $loan_from_id,
                                        'contribution_to_id'=>$contribution_to_id,
                                        'group_id'=>$group_id,
                                        'user_id'=>$member->user_id,
                                        'member_id'=>$member_id,
                                        'amount'=>$amount,
                                        'description'=>$description,        
                                        'created_on'=>time(),
                                        'active'=>1,
                                    );
                                    if($transfer_to_statement_entry_id = $this->ci->statements_m->insert($input)){
                                        $group_ids = array($group_id);

                                        $members_ids = array(
                                            $member_id
                                        );
                                        $this->ci->loan->update_loan_invoices($loan_from_id);
                                        return $contribution_transfer_id;
                                    }else{
                                        return FALSE;
                                    }
                                }else{
                                    return FALSE;
                                }
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                }else{
                    $this->ci->session->set_flashdata('error','Missing parameters');
                    return FALSE;
                }
            }else{
                if($group_id&&$transfer_date&&$contribution_to_id&&$contribution_from_id&&$transfer_to&&$member_id&&$amount){
                    $input = array(
                        'transfer_date'=>strtotime($transfer_date),
                        'contribution_from_id'=>$contribution_from_id,
                        'transfer_to'=>$transfer_to,
                        'contribution_to_id'=>$contribution_to_id,
                        'group_id'=>$group_id,
                        'member_id'=>$member_id,
                        'amount'=>$amount,
                        'description'=>$description,
                        'created_on'=>time(),
                        'active'=>1,
                    );
                    if($contribution_transfer_id = $this->ci->deposits_m->insert_contribution_transfer($input)){
                        $input = array(
                            'transaction_date' => strtotime($transfer_date),
                            'transaction_type'=>25,
                            'group_id'=>$group_id,
                            'transfer_to'=>$transfer_to,
                            'contribution_transfer_id'=>$contribution_transfer_id,
                            'contribution_id'=>$contribution_from_id,
                            'contribution_from_id'=>$contribution_from_id,
                            'contribution_to_id'=>$contribution_to_id,
                            'user_id'=>$member->user_id,
                            'member_id'=>$member_id,
                            'amount'=>$amount,
                            'description'=>$description,        
                            'created_on'=>time(),
                            'active'=>1,
                        );
                        if($transfer_from_statement_entry_id = $this->ci->statements_m->insert($input)){
                            $input = array(
                                'transaction_date' => strtotime($transfer_date),
                                'transaction_type'=>26,
                                'transfer_to'=>$transfer_to,
                                'contribution_transfer_id'=>$contribution_transfer_id,
                                'contribution_id'=>$contribution_to_id,
                                'contribution_from_id'=>$contribution_from_id,
                                'contribution_to_id'=>$contribution_to_id,
                                'group_id'=>$group_id,
                                'user_id'=>$member->user_id,
                                'member_id'=>$member_id,
                                'amount'=>$amount,
                                'description'=>$description,        
                                'created_on'=>time(),
                                'active'=>1,
                            );
                            if($transfer_to_statement_entry_id = $this->ci->statements_m->insert($input)){
                                return $contribution_transfer_id;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }
        }else if($transfer_to==2){
            if($contribution_from_id=='loan'){
                if(preg_match('/contribution-/', $fine_category_to_id)){
                    $contribution_to_id = str_replace('contribution-','',$fine_category_to_id);
                    $fine_category_to_id = 0;
                }else if(preg_match('/fine_category-/', $fine_category_to_id)){
                    $fine_category_to_id = str_replace('fine_category-','',$fine_category_to_id);
                    $contribution_to_id = 0;
                }else{
                    $fine_category_to_id = 0;
                    $contribution_to_id = 0;
                }
                if($group_id&&$transfer_date&&$contribution_from_id&&$transfer_to&&$member_id&&$amount&&$loan_from_id){
                    $input = array(
                        'transfer_date'=>strtotime($transfer_date),
                        'contribution_from_id'=>$contribution_from_id,
                        'loan_from_id'=>$loan_from_id,
                        'fine_category_to_id'=>$fine_category_to_id,
                        'transfer_to'=>$transfer_to,
                        'contribution_to_id'=>$contribution_to_id,
                        'group_id'=>$group_id,
                        'member_id'=>$member_id,
                        'amount'=>$amount,
                        'description'=>$description,
                        'created_on'=>time(),
                        'active'=>1,
                    );
                    if($contribution_transfer_id = $this->ci->deposits_m->insert_contribution_transfer($input)){
                        if($loan_transfer_invoice_id =$this->ci->loan->create_loan_transfer_invoice($member_id,$loan_from_id,$transfer_to,$amount,strtotime($transfer_date),$group_id,$contribution_transfer_id,2)){
                            $input = array(
                                'transaction_date' => strtotime($transfer_date),
                                'transaction_type'=>31,
                                'group_id'=>$group_id,
                                'transfer_to'=>$transfer_to,
                                'contribution_transfer_id'=>$contribution_transfer_id,
                                'contribution_id'=>'',
                                'loan_transfer_invoice_id'=>$loan_transfer_invoice_id,
                                'contribution_from_id'=>$contribution_from_id,
                                'contribution_to_id'=>$contribution_to_id,
                                'fine_category_to_id'=>$fine_category_to_id,
                                'user_id'=>$member->user_id,
                                'member_id'=>$member_id,
                                'amount'=>$amount,
                                'description'=>$description,        
                                'created_on'=>time(),
                                'active'=>1,
                            );
                            if($transfer_from_statement_entry_id = $this->ci->statements_m->insert($input)){
                                $input = array(
                                    'transaction_date' => strtotime($transfer_date),
                                    'transaction_type'=>28,
                                    'transfer_to'=>$transfer_to,
                                    'contribution_transfer_id'=>$contribution_transfer_id,
                                    'contribution_id'=>$contribution_to_id,
                                    'contribution_from_id'=>$contribution_from_id,
                                    'contribution_to_id'=>$contribution_to_id,
                                    'fine_category_to_id'=>$fine_category_to_id,
                                    'fine_category_id'=>$fine_category_to_id,
                                    'group_id'=>$group_id,
                                    'user_id'=>$member->user_id,
                                    'member_id'=>$member_id,
                                    'amount'=>$amount,
                                    'description'=>$description,        
                                    'created_on'=>time(),
                                    'active'=>1,
                                );
                                if($transfer_to_statement_entry_id = $this->ci->statements_m->insert($input)){
                                    $this->ci->loan->update_loan_invoices($loan_from_id);
                                    return $contribution_transfer_id;
                                }else{
                                    return FALSE;
                                }
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                if(preg_match('/contribution-/', $fine_category_to_id)){
                    $contribution_to_id = str_replace('contribution-','',$fine_category_to_id);
                    $fine_category_to_id = 0;
                }else if(preg_match('/fine_category-/', $fine_category_to_id)){
                    $fine_category_to_id = str_replace('fine_category-','',$fine_category_to_id);
                    $contribution_to_id = 0;
                }else{
                    $fine_category_to_id = 0;
                    $contribution_to_id = 0;
                }

                if($group_id&&$transfer_date&&$contribution_from_id&&$transfer_to&&$member_id&&$amount){
                    $input = array(
                        'transfer_date'=>strtotime($transfer_date),
                        'contribution_from_id'=>$contribution_from_id,
                        'fine_category_to_id'=>$fine_category_to_id,
                        'transfer_to'=>$transfer_to,
                        'contribution_to_id'=>$contribution_to_id,
                        'group_id'=>$group_id,
                        'member_id'=>$member_id,
                        'amount'=>$amount,
                        'description'=>$description,
                        'created_on'=>time(),
                        'active'=>1,
                    );
                    if($contribution_transfer_id = $this->ci->deposits_m->insert_contribution_transfer($input)){
                        $input = array(
                            'transaction_date' => strtotime($transfer_date),
                            'transaction_type'=>27,
                            'group_id'=>$group_id,
                            'transfer_to'=>$transfer_to,
                            'contribution_transfer_id'=>$contribution_transfer_id,
                            'contribution_id'=>$contribution_from_id,
                            'contribution_from_id'=>$contribution_from_id,
                            'contribution_to_id'=>$contribution_to_id,
                            'fine_category_to_id'=>$fine_category_to_id,
                            'user_id'=>$member->user_id,
                            'member_id'=>$member_id,
                            'amount'=>$amount,
                            'description'=>$description,        
                            'created_on'=>time(),
                            'active'=>1,
                        );
                        if($transfer_from_statement_entry_id = $this->ci->statements_m->insert($input)){
                            $input = array(
                                'transaction_date' => strtotime($transfer_date),
                                'transaction_type'=>28,
                                'transfer_to'=>$transfer_to,
                                'contribution_transfer_id'=>$contribution_transfer_id,
                                'contribution_id'=>$contribution_to_id,
                                'contribution_from_id'=>$contribution_from_id,
                                'contribution_to_id'=>$contribution_to_id,
                                'fine_category_to_id'=>$fine_category_to_id,
                                'fine_category_id'=>$fine_category_to_id,
                                'group_id'=>$group_id,
                                'user_id'=>$member->user_id,
                                'member_id'=>$member_id,
                                'amount'=>$amount,
                                'description'=>$description,        
                                'created_on'=>time(),
                                'active'=>1,
                            );
                            if($transfer_to_statement_entry_id = $this->ci->statements_m->insert($input)){
                                return $contribution_transfer_id;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            } 
        }else if($transfer_to==3){
            if($contribution_from_id=='loan'){
                if($group_id&&$transfer_date&&$loan_from_id&&$transfer_to&&$member_id&&$amount&&$loan_to_id){
                    $input=array(
                            'transfer_date' => strtotime($transfer_date),
                            'amount' => $amount,
                            'transfer_to' => $transfer_to,
                            'loan_to_id' => $loan_to_id,
                            'contribution_from_id' => $contribution_from_id,
                            'loan_from_id' => $loan_from_id,
                            'group_id' => $group_id,
                            'created_on' => time(),
                            'member_id' => $member_id,
                            'description' => $description,
                            'active' => 1,
                        );
                        print_r($input);
                        die();
                    if($contribution_transfer_id = $this->ci->deposits_m->insert_contribution_transfer($input)){
                        $loan_transfer_invoice_id = $this->ci->loan->create_loan_transfer_invoice($member_id,$loan_from_id,$transfer_to,$amount,strtotime($transfer_date),$group_id,$contribution_transfer_id,2);
                        if($loan_transfer_invoice_id){
                            if($loan_transfer_to_invoice_id = $this->ci->loan->create_incoming_transfer_payment($loan_to_id,$amount,$member_id,strtotime($transfer_date),$group_id,$loan_transfer_invoice_id,$contribution_from_id)){
                                $this->ci->loan->update_loan_invoices($loan_from_id);
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Missing parameters');
                    return FALSE;
                }
            }else{
                if($group_id&&$amount&&$loan_to_id&&$member_id&&$transfer_date&&$contribution_from_id&&$transfer_to){
                    $input=array(
                            'transfer_date' => strtotime($transfer_date),
                            'amount' => $amount,
                            'transfer_to' => $transfer_to,
                            'loan_to_id' => $loan_to_id,
                            'contribution_from_id' => $contribution_from_id,
                            'group_id' => $group_id,
                            'created_on' => time(),
                            'member_id' => $member_id,
                            'description' => $description,
                            'active' => 1,
                        );
                    if($contribution_transfer_id = $this->ci->deposits_m->insert_contribution_transfer($input)){
                        $input = array(
                            'transaction_date' => strtotime($transfer_date),
                            'transaction_type'=>30,
                            'group_id'=>$group_id,
                            'transfer_to'=>$transfer_to,
                            'contribution_transfer_id'=>$contribution_transfer_id,
                            'contribution_id'=>$contribution_from_id,
                            'contribution_from_id'=>$contribution_from_id,
                            'loan_to_id' => $loan_to_id,
                            'user_id'=>$member->user_id,
                            'member_id'=>$member_id,
                            'amount'=>$amount,
                            'description'=>$description,        
                            'created_on'=>time(),
                            'active'=>1,
                        );
                        if($transfer_from_statement_entry_id = $this->ci->statements_m->insert($input)){
                            if($this->ci->loan->create_incoming_transfer_payment($loan_to_id,$amount,$member_id,strtotime($transfer_date),$group_id,'',$contribution_from_id,$transfer_from_statement_entry_id)){
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }
                }
            }
        }else if($transfer_to==4){
            $member_to = $this->ci->members_m->get_group_member($member_to_id,$group_id);
            if($member_transfer_to==1){
                //die;
                if($group_id&&$transfer_date&&$contribution_from_id&&$transfer_to&&$member_id&&$amount&&$member_to_id&&$member_transfer_to&&$member_contribution_to_id){
                    if($contribution_from_id=='loan'){
                        if($loan_from_id&&$group_id&&$member_id&&$member_to_id&&$transfer_to&&$amount&&$transfer_date&&$member_contribution_to_id&&$member_transfer_to&&$member_to_id){
                            $input = array(
                                'transfer_date'=>strtotime($transfer_date),
                                'contribution_from_id'=>$contribution_from_id,
                                'loan_from_id'=>$loan_from_id,
                                'transfer_to'=>$transfer_to,
                                'contribution_to_id'=>$member_contribution_to_id,
                                'group_id'=>$group_id,
                                'member_id'=>$member_id,
                                'amount'=>$amount,
                                'description'=>$description,
                                'member_to_id'=>$member_to_id,
                                'member_transfer_to'=>$member_transfer_to,
                                'description'=>$description,
                                'created_on'=>time(),
                                'active'=>1,
                            );
                            if($contribution_transfer_id = $this->ci->deposits_m->insert_contribution_transfer($input)){
                                $loan_transfer_invoice_id = $this->ci->loan->create_loan_transfer_invoice($member_id,$loan_from_id,$transfer_to,$amount,strtotime($transfer_date),$group_id,$contribution_transfer_id,2);
                                if($loan_transfer_invoice_id){
                                    $input = array(
                                        'transaction_date' => strtotime($transfer_date),
                                        'transaction_type'=>29,
                                        'group_id'=>$group_id,
                                        'transfer_to'=>$transfer_to,
                                        'contribution_transfer_id'=>$contribution_transfer_id,
                                        'loan_transfer_invoice_id' => $loan_transfer_invoice_id,
                                        'contribution_from_id'=>$contribution_from_id,
                                        'loan_from_id' => $loan_from_id,
                                        'contribution_to_id'=>$member_contribution_to_id,
                                        'user_id'=>$member->user_id,
                                        'member_id'=>$member_id,
                                        'member_to_id' => $member_to_id,
                                        'amount'=>$amount,
                                        'description'=>$description,        
                                        'created_on'=>time(),
                                        'active'=>1,
                                    );
                                    if($transfer_from_statement_entry_id = $this->ci->statements_m->insert($input)){
                                        $input = array(
                                            'transaction_date' => strtotime($transfer_date),
                                            'transaction_type'=>26,
                                            'transfer_to'=>$transfer_to,
                                            'contribution_transfer_id'=>$contribution_transfer_id,
                                            'contribution_id'=>$member_contribution_to_id,
                                            'contribution_from_id'=>$contribution_from_id,
                                            'loan_from_id' => $loan_from_id,
                                            'contribution_to_id'=>$member_contribution_to_id,
                                            'group_id'=>$group_id,
                                            'user_id'=>$member_to->user_id,
                                            'member_id'=>$member_to_id,
                                            'member_from_id' => $member_id,
                                            'amount'=>$amount,
                                            'description'=>$description,        
                                            'created_on'=>time(),
                                            'active'=>1,
                                        );
                                        if($transfer_to_statement_entry_id = $this->ci->statements_m->insert($input)){
                                            $this->ci->loan->update_loan_invoices($loan_from_id);
                                            return $contribution_transfer_id;
                                        }else{
                                            return FALSE;
                                        }
                                    }else{
                                        return FALSE;
                                    }
                                }else{
                                    return FALSE;
                                }
                            }else{
                                return FALSE;
                            }
                        }else{
                            $this->ci->session->set_flashdata('error','Missing parameters');
                            return FALSE;
                        }
                    }else{
                        $input = array(
                            'transfer_date'=>strtotime($transfer_date),
                            'contribution_from_id'=>$contribution_from_id,
                            'transfer_to'=>$transfer_to,
                            'contribution_to_id'=>$member_contribution_to_id,
                            'group_id'=>$group_id,
                            'member_id'=>$member_id,
                            'amount'=>$amount,
                            'description'=>$description,
                            'member_transfer_to' => $member_transfer_to,
                            'member_to_id' => $member_to_id,
                            'created_on'=>time(),
                            'active'=>1,
                        );
                        if($contribution_transfer_id = $this->ci->deposits_m->insert_contribution_transfer($input)){
                            $input = array(
                                'transaction_date' => strtotime($transfer_date),
                                'transaction_type'=>25,
                                'group_id'=>$group_id,
                                'transfer_to'=>$member_transfer_to,
                                'contribution_transfer_id'=>$contribution_transfer_id,
                                'contribution_id'=>$contribution_from_id,
                                'contribution_from_id'=>$contribution_from_id,
                                'contribution_to_id'=>$member_contribution_to_id,
                                'user_id'=>$member->user_id,
                                'member_id'=>$member_id,
                                'member_to_id'=>$member_to_id,
                                'amount'=>$amount,
                                'description'=>$description,        
                                'created_on'=>time(),
                                'active'=>1,
                            );
                            if($transfer_from_statement_entry_id = $this->ci->statements_m->insert($input)){
                                $input = array(
                                    'transaction_date' => strtotime($transfer_date),
                                    'transaction_type'=>26,
                                    'transfer_to'=>$member_transfer_to,
                                    'contribution_transfer_id'=>$contribution_transfer_id,
                                    'contribution_id'=>$member_contribution_to_id,
                                    'contribution_from_id'=>$contribution_from_id,
                                    'contribution_to_id'=>$member_contribution_to_id,
                                    'group_id'=>$group_id,
                                    'user_id'=>$member_to->user_id,
                                    'member_id'=>$member_to_id,
                                    'member_from_id'=>$member_id,
                                    'amount'=>$amount,
                                    'description'=>$description,        
                                    'created_on'=>time(),
                                    'active'=>1,
                                );
                                if($transfer_to_statement_entry_id = $this->ci->statements_m->insert($input)){
                                    return $contribution_transfer_id;
                                }else{
                                    $this->ci->session->set_flashdata('warning',"Could not create contribution transfer, transfer to could not be created");
                                    return FALSE;
                                }
                            }else{
                                $this->ci->session->set_flashdata('warning',"Could not create contribution transfer, transfer from could not be created");
                                return FALSE;
                            }
                        }else{
                            $this->ci->session->set_flashdata('warning',"Could not create contribution transfer");
                            return FALSE;
                        }
                    }
                }else{
                    $this->ci->session->set_flashdata('warning',"Could not create contribution transfer, parameter missing");
                    return FALSE;
                }
            }else if($member_transfer_to==2){
                if(preg_match('/contribution-/', $member_fine_category_to_id)){
                    $member_contribution_to_id = str_replace('contribution-','',$member_fine_category_to_id);
                    $member_fine_category_to_id = 0;
                }else if(preg_match('/fine_category-/', $member_fine_category_to_id)){
                    $member_fine_category_to_id = str_replace('fine_category-','',$member_fine_category_to_id);
                    $member_contribution_to_id = 0;
                }else{
                    $member_fine_category_to_id = 0;
                    $member_contribution_to_id = 0;
                }
                if($contribution_from_id=='loan'){
                    if($group_id&&$transfer_date&&$contribution_from_id&&$member_fine_category_to_id&&$transfer_to&&$member_transfer_to&&$member_id&&$member_to_id&&$amount&&$loan_from_id){
                        $input = array(
                            'transfer_date'=>strtotime($transfer_date),
                            'contribution_from_id'=>$contribution_from_id,
                            'loan_from_id'=>$loan_from_id,
                            'fine_category_to_id'=>$member_fine_category_to_id,
                            'transfer_to'=>$transfer_to,
                            'contribution_to_id'=>$contribution_to_id,
                            'group_id'=>$group_id,
                            'member_id'=>$member_id,
                            'member_to_id'=>$member_to_id,
                            'member_transfer_to'=>$member_transfer_to,
                            'amount'=>$amount,
                            'description'=>$description,
                            'created_on'=>time(),
                            'active'=>1,
                        );
                        if($contribution_transfer_id = $this->ci->deposits_m->insert_contribution_transfer($input)){
                            if($loan_transfer_invoice_id =$this->ci->loan->create_loan_transfer_invoice($member_id,$loan_from_id,$transfer_to,$amount,strtotime($transfer_date),$group_id,$contribution_transfer_id,2)){
                                $input = array(
                                    'transaction_date' => strtotime($transfer_date),
                                    'transaction_type'=>31,
                                    'group_id'=>$group_id,
                                    'transfer_to'=>$transfer_to,
                                    'contribution_transfer_id'=>$contribution_transfer_id,
                                    'contribution_id'=>'',
                                    'loan_transfer_invoice_id'=>$loan_transfer_invoice_id,
                                    'contribution_from_id'=>$contribution_from_id,
                                    'contribution_to_id'=>$member_contribution_to_id,
                                    'fine_category_to_id'=>$member_fine_category_to_id,
                                    'user_id'=>$member->user_id,
                                    'member_id'=>$member_id,
                                    'member_to_id' => $member_to_id,
                                    'amount'=>$amount,
                                    'description'=>$description,        
                                    'created_on'=>time(),
                                    'active'=>1,
                                );
                                if($transfer_from_statement_entry_id = $this->ci->statements_m->insert($input)){
                                    $input = array(
                                        'transaction_date' => strtotime($transfer_date),
                                        'transaction_type'=>28,
                                        'transfer_to'=>$transfer_to,
                                        'contribution_transfer_id'=>$contribution_transfer_id,
                                        'contribution_id'=>$member_contribution_to_id,
                                        'contribution_from_id'=>$contribution_from_id,
                                        'contribution_to_id'=>$member_contribution_to_id,
                                        'fine_category_to_id'=>$member_fine_category_to_id,
                                        'fine_category_id'=>$member_fine_category_to_id,
                                        'group_id'=>$group_id,
                                        'user_id'=>$member_to->user_id,
                                        'member_id'=>$member_to_id,
                                        'member_from_id'=>$member_id,
                                        'amount'=>$amount,
                                        'description'=>$description,        
                                        'created_on'=>time(),
                                        'active'=>1,
                                    );
                                    if($transfer_to_statement_entry_id = $this->ci->statements_m->insert($input)){
                                        $this->ci->loan->update_loan_invoices($loan_from_id);
                                        return $contribution_transfer_id;
                                    }else{
                                        return FALSE;
                                    }
                                }else{
                                    return FALSE;
                                }
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    if($group_id&&$transfer_date&&$contribution_from_id&&$transfer_to&&$member_id&&$amount&&($member_fine_category_to_id||$member_contribution_to_id)){
                        $input = array(
                            'transfer_date'=>strtotime($transfer_date),
                            'contribution_from_id'=>$contribution_from_id,
                            'fine_category_to_id'=>$member_fine_category_to_id,
                            'transfer_to'=>$transfer_to,
                            'contribution_to_id'=>$member_contribution_to_id,
                            'group_id'=>$group_id,
                            'member_id'=>$member_id,
                            'amount'=>$amount,
                            'description'=>$description,
                            'member_transfer_to' => $member_transfer_to,
                            'member_to_id' => $member_to_id,
                            'created_on'=>time(),
                            'active'=>1,
                        );
                        if($contribution_transfer_id = $this->ci->deposits_m->insert_contribution_transfer($input)){
                            $input = array(
                                'transaction_date' => strtotime($transfer_date),
                                'transaction_type'=>27,
                                'group_id'=>$group_id,
                                'transfer_to'=>$transfer_to,
                                'contribution_transfer_id'=>$contribution_transfer_id,
                                'contribution_id'=>$contribution_from_id,
                                'contribution_from_id'=>$contribution_from_id,
                                'contribution_to_id'=>$member_contribution_to_id,
                                'fine_category_to_id'=>$member_fine_category_to_id,
                                'user_id'=>$member->user_id,
                                'member_id'=>$member_id,
                                'member_to_id'=>$member_to_id,
                                'amount'=>$amount,
                                'description'=>$description,        
                                'created_on'=>time(),
                                'active'=>1,
                            );
                            if($transfer_from_statement_entry_id = $this->ci->statements_m->insert($input)){
                                $input = array(
                                    'transaction_date' => strtotime($transfer_date),
                                    'transaction_type'=>28,
                                    'transfer_to'=>$transfer_to,
                                    'contribution_transfer_id'=>$contribution_transfer_id,
                                    'contribution_id'=>$member_contribution_to_id,
                                    'contribution_from_id'=>$contribution_from_id,
                                    'contribution_to_id'=>$member_contribution_to_id,
                                    'fine_category_to_id'=>$member_fine_category_to_id,
                                    'fine_category_id'=>$member_fine_category_to_id,
                                    'group_id'=>$group_id,
                                    'user_id'=>$member_to->user_id,
                                    'member_id'=>$member_to_id,
                                    'member_from_id'=>$member_id,
                                    'amount'=>$amount,
                                    'description'=>$description,        
                                    'created_on'=>time(),
                                    'active'=>1,
                                );
                                if($transfer_to_statement_entry_id = $this->ci->statements_m->insert($input)){
                                    return $contribution_transfer_id;
                                }else{
                                    return FALSE;
                                }
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }
            }else if($member_transfer_to==3){
                if($contribution_from_id=='loan'){
                    if($group_id&&$transfer_date&&$member_transfer_to&&$member_to_id&&$loan_from_id&&$transfer_to&&$member_id&&$amount&&$member_loan_to_id){
                        $input=array(
                                'transfer_date' => strtotime($transfer_date),
                                'amount' => $amount,
                                'transfer_to' => $transfer_to,
                                'loan_to_id' => $member_loan_to_id,
                                'contribution_from_id' => $contribution_from_id,
                                'loan_from_id' => $loan_from_id,
                                'group_id' => $group_id,
                                'created_on' => time(),
                                'member_id' => $member_id,
                                'member_to_id' => $member_to_id,
                                'member_transfer_to' => $member_transfer_to,
                                'member_id' => $member_id,
                                'description' => $description,
                                'active' => 1,
                            );
                        if($contribution_transfer_id = $this->ci->deposits_m->insert_contribution_transfer($input)){
                            $loan_transfer_invoice_id = $this->ci->loan->create_loan_transfer_invoice($member_id,$loan_from_id,$transfer_to,$amount,strtotime($transfer_date),$group_id,$contribution_transfer_id,2);
                            if($loan_transfer_invoice_id){
                                if($loan_transfer_to_invoice_id = $this->ci->loan->create_incoming_transfer_payment($member_loan_to_id,$amount,$member_to_id,strtotime($transfer_date),$group_id,$loan_transfer_invoice_id,$contribution_from_id)){
                                    $this->ci->loan->update_loan_invoices($loan_from_id);
                                    return TRUE;
                                }else{
                                    return FALSE;
                                }
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Missing parameters');
                        return FALSE;
                    }
                }else{
                    if($group_id&&$amount&&$member_loan_to_id&&$member_id&&$transfer_date&&$contribution_from_id&&$transfer_to&&$member_transfer_to&&$member_to_id){
                        $input=array(
                            'transfer_date' => strtotime($transfer_date),
                            'amount' => $amount,
                            'transfer_to' => $transfer_to,
                            'loan_to_id' => $member_loan_to_id,
                            'contribution_from_id' => $contribution_from_id,
                            'group_id' => $group_id,
                            'created_on' => time(),
                            'member_id' => $member_id,
                            'member_transfer_to' => $member_transfer_to,
                            'member_to_id' => $member_to_id,
                            'description' => $description,
                            'active' => 1,
                        );
                        if($contribution_transfer_id = $this->ci->deposits_m->insert_contribution_transfer($input)){
                            $input = array(
                                'transaction_date' => strtotime($transfer_date),
                                'transaction_type'=>30,
                                'group_id'=>$group_id,
                                'transfer_to'=>$transfer_to,
                                'contribution_transfer_id'=>$contribution_transfer_id,
                                'contribution_id'=>$contribution_from_id,
                                'contribution_from_id'=>$contribution_from_id,
                                'loan_to_id' => $member_loan_to_id,
                                'user_id'=>$member->user_id,
                                'member_id'=>$member_id,
                                'member_to_id' => $member_to_id,
                                'amount'=>$amount,
                                'description'=>$description,        
                                'created_on'=>time(),
                                'active'=>1,
                            );
                            if($transfer_from_statement_entry_id = $this->ci->statements_m->insert($input)){
                                if($this->ci->loan->create_incoming_transfer_payment($member_loan_to_id,$amount,$member_to_id,strtotime($transfer_date),$group_id,'',$contribution_from_id,$transfer_from_statement_entry_id)){
                                    return TRUE;
                                }else{
                                    return FALSE;
                                }
                            }else{
                                return FALSE;
                            }
                        }
                    }
                }
            }
        }else{

            return FALSE;
        }
    }
    public function void_contribution_transfer($id = 0,$group_id = 0){
        $input = array(
            'active' => 0,
            'modified_on' => time()
        );
        $post = $this->ci->deposits_m->get_group_contribution_transfer($id,$group_id);
        /*$contribution_transfer_statement_entries = $this->ci->statements_m->get_contribution_transfer_statement_entries($id,$group_id);
        foreach($contribution_transfer_statement_entries as $statement_entry){
            $statement_entries[] =$statement_entry->id; 
        }
        $statement_entry='';
        if($statement_entries){
            foreach ($statement_entries as $contribution_entries_id) {
                if($statement_entry){
                    $statement_entry=$statement_entry.','.$contribution_entries_id;
                }else{
                    $statement_entry=$contribution_entries_id;
                }
            }
        }
        print_r($statement_entry);
        $result = $this->ci->loan_repayments_m->get_loan_repayments($post->loan_to_id,'',$statement_entry);
        print_r($post); 
        print_r($result);
        foreach ($result as $res) {
            $loan_statement[] =$this->ci->loans_m->get_statement_by_loan_payment_id($res->id);
        }
        print_r($loan_statement);
        print_r($statement_entries); die();*/
        if($post){
            if($post->contribution_from_id=='loan'){
                if($post->transfer_to==3){
                    $loan_from_id_void = $this->ci->loan->void_transfered_from_loan($post->loan_from_id,$post->id);
                    if($loan_from_id_void){
                        $loan_to_id_void = $this->ci->loan->void_transfered_to_loan($post->loan_to_id,$loan_from_id_void);
                        $this->ci->deposits_m->update_contribution_transfer($id,$input);
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{
                    if($this->ci->loan->void_transfered_from_loan($post->loan_from_id,$post->id)){
                        if($this->ci->deposits_m->update_contribution_transfer($id,$input)){
                            if($contribution_transfer_statement_entries = $this->ci->statements_m->get_contribution_transfer_statement_entries($id,$group_id)){
                                $result = TRUE;
                                foreach($contribution_transfer_statement_entries as $statement_entry){
                                    $input = array(
                                        'active' => 0,
                                        'modified_on' => time()
                                    );
                                    if($this->ci->statements_m->update($statement_entry->id,$input)){

                                    }else{
                                        $result = FALSE;
                                    }
                                }
                                return $result;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }
            }else{
                if($this->ci->deposits_m->update_contribution_transfer($id,$input)){
                    if($contribution_transfer_statement_entries = $this->ci->statements_m->get_contribution_transfer_statement_entries($id,$group_id)){
                        $result = TRUE;
                        $statement_entries=array();
                        foreach($contribution_transfer_statement_entries as $statement_entry){
                            $input = array(
                                'active' => 0,
                                'modified_on' => time()
                            );
                            if($this->ci->statements_m->update($statement_entry->id,$input)){
                                $statement_entries[] =$statement_entry->id; 
                            }else{
                                $result = FALSE;
                            }
                        }
                        if($post->transfer_to==3){
                            $loan_to_id_void = $this->ci->loan->void_transfered_to_loan($post->loan_to_id,'',$statement_entries);
                        }
                        return $result;
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }
        }else{
            return FALSE;
        }
    }

    public function send_withdrawal_transaction_alert_notification($bank_account_number = 0,$transaction_date = 0,$transaction_type = '',$amount = 0,$transaction_description='',$transaction_currency = '',$transaction_alert_id = 0){
        if($bank_account_number&&$transaction_date&&$transaction_type&&$amount){
            $group = $this->ci->groups_m->get_group_by_bank_account_number($bank_account_number);
            if($group){
                if($group->notify_members_on_withdrawals){
                    $bank_account = $this->ci->bank_accounts_m->get_group_bank_account_by_account_number($bank_account_number,$group->id);
                    if($bank_account){
                        if($user = $this->ci->ion_auth->get_user($group->owner)){
                            $members = $this->ci->members_m->get_group_members($group->id);
                            $result = TRUE;
                            if($members){
                                foreach($members as $member):
                                    if(trim($transaction_type)=="deposit"){
                                        $transaction_type_conjunction = 'to';
                                        $call_to_action_link = "member";
                                        $category = 13;
                                        $transaction_type_action = 'credited';
                                    }else if(trim($transaction_type)=="withdrawal"){
                                        $transaction_type_conjunction = 'from';
                                        $call_to_action_link = "member";
                                        $category = 14;
                                        $transaction_type_action = 'debited';
                                    }
                                    $notification_subject = "Withdrawal notification for ".$group->name;
                                    $notification_message = " A ".$transaction_type." of ".$transaction_currency." ".number_to_currency($amount)." has been made ".$transaction_type_conjunction." group account ".$bank_account_number.".";
                                    $call_to_action = "A ".$transaction_type." has been made ".$transaction_type_conjunction." your ".$group->name." account ";

                                    if($this->ci->notifications->create($notification_subject,$notification_message,$user,$member->id,$member->user_id,$member->id,$group->id,$call_to_action,$call_to_action_link,$category,0,0,$transaction_alert_id)){
                                        if(valid_email($member->email)){

                                            $email_data = array(
                                                'FIRST_NAME'=>$member->first_name,
                                                'LAST_NAME'=>$member->last_name,
                                                'AMOUNT'=>number_to_currency($amount),
                                                'TRANSACTION_DATE'=>timestamp_to_date($transaction_date),
                                                'TRANSACTION_TYPE'=>$transaction_type,
                                                'TRANSACTION_TYPE_ACTION'=>$transaction_type_action,
                                                'BANK_ACCOUNT_NUMBER'=>$bank_account_number,
                                                'BANK_ACCOUNT_NAME'=>$bank_account->account_name,
                                                'GROUP_NAME'=>$group->name,
                                                'GROUP_CURRENCY'=>$transaction_currency,
                                                'TRANSACTION_TYPE_CONJUNCTION'=>$transaction_type_conjunction,
                                                'TRANSACTION_DESCRIPTION'=>$transaction_description,
                                                'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
                                                'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
                                                'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'',
                                                'DATE' => date('d'),
                                                'MONTH' => date('M'),
                                            );

                                            $message = $this->ci->emails_m->build_email_message('withdrawal-transaction-notification',$email_data);

                                            $input = array(
                                                'email_to'=>$member->email,
                                                //'email_to'=>'edwin.njoroge@digitalvision.co.ke',
                                                //'cc' => $cc,
                                                'subject'=>$notification_subject,
                                                'email_from'=>'',
                                                'group_id'=>$group->id,
                                                'member_id'=>$member->id,
                                                'user_id'=>$member->user_id,
                                                'message'=>$message,
                                                'created_on'=>time(),
                                                'created_by'=>$member->user_id
                                            );
                                            if($this->ci->emails_m->insert_email_queue($input)){

                                            }else{
                                                $result = FALSE;
                                            }
                                        }else if(valid_phone($member->phone)){
                                            $sms_data = array(
                                                'FIRST_NAME'=>$member->first_name,
                                                'AMOUNT'=>number_to_currency($amount),
                                                'TRANSACTION_DATE'=>timestamp_to_date($transaction_date),
                                                'TRANSACTION_TYPE'=>$transaction_type,
                                                'TRANSACTION_TYPE_CONJUNCTION'=>$transaction_type_conjunction,
                                                'BANK_ACCOUNT_NUMBER'=>$bank_account_number,
                                                'GROUP_CURRENCY'=>$transaction_currency,
                                                'GROUP_NAME'=>$group->name,
                                            );
                                            $message = $this->ci->sms_m->build_sms_message('withdrawal-transaction-notification',$sms_data);
                                            $input = array(
                                                'sms_to' => $member->phone,
                                                'message' => $message,
                                                'group_id' => $group->id,
                                                'member_id' => $member->id,
                                                'user_id' => $member->user_id,
                                                'system_sms' => 0,
                                                'created_by' => $member->user_id,
                                                'created_on' => time(),
                                            );
                                            if($this->ci->sms_m->insert_sms_queue($input)){

                                            }else{
                                                $result = FALSE;
                                            }
                                        }else{
                                            $result = FALSE;
                                        }
                                    }else{
                                        $result = FALSE;
                                    }
                                endforeach;
                                if($result){
                                    return TRUE;
                                }else{
                                    return FALSE;
                                }
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return TRUE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function send_transaction_alert_notification($bank_account_number = 0,$transaction_date = 0,$transaction_type = '',$amount = 0,$transaction_description='',$transaction_currency = '',$transaction_alert_id = 0){
        if($bank_account_number&&$transaction_date&&$transaction_type&&$amount){
            $group = $this->ci->groups_m->get_group_by_bank_account_number($bank_account_number);
            if($group){
                $bank_account = $this->ci->bank_accounts_m->get_group_bank_account_by_account_number($bank_account_number,$group->id);
                if($bank_account){
                    $notified_member_id = 0;
                    if($user = $this->ci->ion_auth->get_user($group->owner)){
                        if($member = $this->ci->members_m->get_group_member_by_user_id($group->id,$user->id)){
                            $notified_member_id = $member->id;
                            if(trim($transaction_type)=="deposit"){
                                $transaction_type_conjunction = 'to';
                                $call_to_action_link = "#";
                                $category = 13;
                            }else if(trim($transaction_type)=="withdrawal"){
                                $transaction_type_conjunction = 'from';
                                $call_to_action_link = "#";
                                $category = 14;
                            }
                            $notification_subject = "Transaction notification for ".$group->name;
                            $notification_message = " A ".$transaction_type." of ".$transaction_currency." ".number_to_currency($amount)." has been made ".$transaction_type_conjunction." group account ".$bank_account_number.". Click here to reconcile it.";
                            $call_to_action = "Reconcile ".$transaction_type;
                            if($this->ci->notifications->create($notification_subject,$notification_message,$user,$member->id,$user->id,$member->id,$group->id,$call_to_action,$call_to_action_link,$category,0,0,$transaction_alert_id)){
                                if(valid_phone($user->phone)){
                                    $sms_data = array(
                                        'FIRST_NAME'=>$user->first_name,
                                        'AMOUNT'=>number_to_currency($amount),
                                        'TRANSACTION_DATE'=>timestamp_to_date($transaction_date),
                                        'TRANSACTION_TYPE'=>$transaction_type,
                                        'TRANSACTION_TYPE_CONJUNCTION'=>$transaction_type_conjunction,
                                        'BANK_ACCOUNT_NUMBER'=>$bank_account_number,
                                        'GROUP_CURRENCY'=>$transaction_currency,
                                        'APPLICATION_NAME' => $this->application_settings->application_name,
                                        'GROUP_NAME'=>'['.$group->name.']',
                                    );
                                    $message = $this->ci->sms_m->build_sms_message('transaction-alert-notification',$sms_data,'',$member->language_id);
                                    $input = array(
                                        'sms_to' => $user->phone,
                                        'message' => $message,
                                        'group_id' => $group->id,
                                        'member_id' => $member->id,
                                        'user_id' => $user->id,
                                        'system_sms' => 1,
                                        'created_by' => $user->id,
                                        'created_on' => time(),
                                    );
                                    $this->ci->sms_m->insert_sms_queue($input);
                                }
                                if(trim($transaction_type)=="deposit"){
                                    $transaction_type_conjunction = 'to';
                                    $transaction_type_action = 'credited';
                                }else if(trim($transaction_type)=="withdrawal"){
                                    $transaction_type_conjunction = 'from';
                                    $transaction_type_action = 'debited';
                                }
                                $transaction_type = ucfirst($transaction_type);
                                if(valid_email($user->email)){
                                    $email_data = array(
                                        'FIRST_NAME'=>$user->first_name,
                                        'LAST_NAME'=>$user->last_name,
                                        'APPLICATION_NAME' => $this->application_settings->application_name,
                                        'GROUP_NAME'=>$group->name,
                                        'BANK_ACCOUNT_NAME'=>$bank_account->account_name,
                                        'BANK_ACCOUNT_NUMBER'=>$bank_account_number,
                                        'TRANSACTION_TYPE_ACTION'=>$transaction_type_action,
                                        'GROUP_CURRENCY'=>$transaction_currency,
                                        'AMOUNT'=>number_to_currency($amount),
                                        'TRANSACTION_DESCRIPTION'=>$transaction_description,
                                        'TRANSACTION_TYPE'=>$transaction_type,
                                        'LINK' => site_url(),
                                        'TRANSACTION_DATE'=>timestamp_to_date($transaction_date),
                                        'TRANSACTION_TYPE_CONJUNCTION'=>$transaction_type_conjunction,
                                        'RECONCILE_URL'=>$reconcile_url,
                                        'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
                                        'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
                                        'LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'',
                                        'DATE' => date('d'),
                                        'MONTH' => date('M'),
                                        'YEAR' => date('Y'),
                                    );

                                    $message = $this->ci->emails_m->build_email_message('transaction-alert-notification',$email_data);
                                    
                                    $cc = '';
                                    if($bank_account->enable_email_transaction_alerts_to_members){
                                        $member_ids = $this->ci->bank_accounts_m->get_bank_account_email_transaction_alert_member_pairings_array($bank_account->id,$group->id);
                                        if(isset($member_ids)){
                                            $cc = $this->ci->members_m->get_group_member_email_address_list_by_member_id_array($member_ids,$group->id);
                                        }
                                    }
                                    $input = array(
                                        'email_to'=>$user->email,
                                        //'email_to'=>'edwin.njoroge@digitalvision.co.ke',
                                        'cc' => $cc,
                                        'subject'=>$notification_subject,
                                        'email_from'=>'',
                                        'group_id'=>$group->id,
                                        'member_id'=>$member->id,
                                        'user_id'=>$user->id,
                                        'message'=>$message,
                                        'created_on'=>time(),
                                        'created_by'=>$user->id
                                    );
                                    if($this->ci->emails_m->insert_email_queue($input)){
                                        if($bank_account->enable_sms_transaction_alerts_to_members){

                                            $result = TRUE;
                                            $member_ids = $this->ci->bank_accounts_m->get_bank_account_sms_transaction_alert_member_pairings_array($bank_account->id,$group->id);     
                                            foreach($member_ids as $member_id):
                                                if($member_id == $notified_member_id){
                                                    continue;
                                                }
                                                $member = $this->ci->members_m->get_group_member($member_id,$group->id);
                                                if($member){
                                                    if($member->phone){
                                                         $sms_data = array(
                                                            'FIRST_NAME'=>$member->first_name,
                                                            'AMOUNT'=>number_to_currency($amount),
                                                            'TRANSACTION_DATE'=>timestamp_to_date($transaction_date),
                                                            'TRANSACTION_TYPE'=>$transaction_type,
                                                            'TRANSACTION_TYPE_CONJUNCTION'=>$transaction_type_conjunction,
                                                            'BANK_ACCOUNT_NUMBER'=>$bank_account_number,
                                                            'GROUP_CURRENCY'=>$transaction_currency,
                                                            'GROUP_NAME'=>'['.$group->name.']',
                                                            'APPLICATION_NAME' => $this->application_settings->application_name,
                                                        );
                                                        $message = $this->ci->sms_m->build_sms_message('transaction-alert-notification',$sms_data,'',$member->language_id);
                                                        $input = array(
                                                            'sms_to' => $member->phone,
                                                            'message' => $message,
                                                            'group_id' => $group->id,
                                                            'member_id' => $member->id,
                                                            'user_id' => $user->id,
                                                            'system_sms' => 0,
                                                            'created_by' => $user->id,
                                                            'created_on' => time(),
                                                        );
                                                        if($this->ci->sms_m->insert_sms_queue($input)){

                                                        }else{
                                                            $result = FALSE;
                                                        }
                                                    }
                                                }else{
                                                    $result = FALSE;
                                                }
                                            endforeach;
                                            return $result;
                                        }else{
                                            return TRUE;
                                        }
                                    }else{
                                        return FALSE;
                                    }
                                }
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function void_asset($id = 0,$asset = array()){
        if($id&&$asset){
            $input = array(
                'active'=>0,
                'modified_on'=>time()
            );
            if($this->ci->assets_m->update($id,$input)){
                //void asset purchase payments
                $asset_purchase_payments = $this->ci->withdrawals_m->get_group_asset_purchase_withdrawals_by_asset_id($id,$asset->group_id);
                $result = TRUE;
                foreach($asset_purchase_payments as $asset_purchase_payment):
                    if($this->void_group_withdrawal($asset_purchase_payment->id,$asset_purchase_payment,TRUE,$asset_purchase_payment->group_id)){

                    }else{
                        $result = FALSE;
                    }
                endforeach;
                //void asset sale entries
                $asset_sale_deposits = $this->ci->deposits_m->get_group_asset_sale_deposits_by_asset_id($id,$asset->group_id);
                foreach($asset_sale_deposits as $asset_sale_deposit):

                    if($this->void_group_deposit($asset_sale_deposit->id,$asset_sale_deposit,TRUE,$asset_sale_deposit->group_id)){

                    }else{
                        $result = FALSE;
                    }
                endforeach;
                return $result;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function void_all_group_member_transactions($group_id = 0,$member_id = 0){
        if($group_id&&$member_id){
            $member = $this->ci->members_m->get_group_member($member_id,$group_id);
            if($member){
                $result = TRUE;
                //void deposits
                $deposits = $this->ci->deposits_m->get_group_member_deposits($member_id,$group_id);
                foreach($deposits as $deposit):
                    if($this->void_group_deposit($deposit->id,$deposit,TRUE,$deposit->group_id)){

                    }else{
                        $result = FALSE;
                    }
                endforeach;
                //void withdrawals
                $withdrawals = $this->ci->withdrawals_m->get_group_member_withdrawals($member_id,$group_id);
                foreach($withdrawals as $withdrawal):
                    if($this->void_group_withdrawal($withdrawal->id,$withdrawal,TRUE,$withdrawal->group_id)){

                    }else{
                        $result = FALSE;
                    }
                endforeach;
                //void invoices
                $invoices = $this->ci->invoices_m->get_group_member_invoices_to_void($member_id,$group_id);
                foreach($invoices as $invoice):
                    if($invoice->type==1){
                        if($this->void_contribution_invoice($invoice->id)){
                        
                        }else{
                            $result = FALSE;
                        }
                    }else if($invoice->type==2){
                        if($this->void_fine_invoice($invoice->id,'',$invoice->contribution_id)){
                        
                        }else{
                            $result = FALSE;
                        }
                    }else if($invoice->type==3){
                        if($this->void_fine_invoice($invoice->id,$invoice->fine_id)){

                        }else{
                            $result = FALSE;
                        }
                    }else if($invoice->type==4){
                        if($this->void_miscellaneous_invoice($invoice->id)){

                        }else{
                            $result = FALSE;
                        }
                    }
                endforeach;
                //void all contribution transfers
                $contribution_transfers = $this->ci->deposits_m->get_group_member_contribution_transfers($member_id,$group_id);
                foreach($contribution_transfers as $contribution_transfer):
                    if($this->void_contribution_transfer($contribution_transfer->id,$contribution_transfer->group_id)){

                    }else{
                        $result = FALSE;
                    }
                endforeach;
                return $result;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function forward_transaction_alert($bank_id = 0,$account_number = 0,$data = array()){
        if($bank_id&&$account_number&&$data){
            if($transaction_alert_forwarders = $this->ci->transaction_alerts_m->get_transaction_alert_forwarders_by_bank_id_and_account_number($bank_id,$account_number)){
                $result = TRUE;
                foreach($transaction_alert_forwarders as $transaction_alert_forwarder):



                    if($response = $this->ci->curl->post_json($data,$transaction_alert_forwarder->url)){
                        
                    }else{
                        $result = FALSE;
                    }



                endforeach;
                return $result;
            }else{
                return TRUE;
            }
        }else{
            return FALSE;
        }
    }

    public function forward_transaction_alert_to_urls($data = array()){
        if($data){
            if($transaction_alert_forwarders = $this->ci->transaction_alerts_m->get_all_posts_transaction_alert_forwarders()){
                $result = TRUE;
                foreach($transaction_alert_forwarders as $transaction_alert_forwarder):
                    if($response = $this->ci->curl->post_json($data,$transaction_alert_forwarder->url)){
                        
                    }else{
                        $result = FALSE;
                    }
                endforeach;
                return $result;
            }else{
                return TRUE;
            }
        }else{
            return FALSE;
        }
    }

    public function queue_transaction_alert_forwards($transaction_alert_id = 0,$data = array()){
        if($transaction_alert_id&&$data){
            if($json_data = json_decode($data)){
                if($transaction_alert_forwarders = $this->ci->transaction_alerts_m->get_all_posts_transaction_alert_forwarders()){
                    foreach($transaction_alert_forwarders as $transaction_alert_forwarder):
                        $input[] = array(
                            'transaction_alert_id'=>$transaction_alert_id, 
                            'url'=>$transaction_alert_forwarder->url, 
                            'tranCurrency'=>$json_data->tranCurrency,
                            'tranDate'=>$json_data->tranDate,
                            'tranid'=>$json_data->tranid,
                            'tranAmount'=>$json_data->tranAmount,
                            'trandrcr'=>$json_data->trandrcr,
                            'accid'=>$json_data->accid,
                            'refNo'=>$json_data->refNo,
                            'tranType'=>$json_data->tranType,
                            'tranParticular'=>$json_data->tranParticular,
                            'tranRemarks'=>$json_data->tranRemarks,
                            'ip_address'=>$_SERVER['REMOTE_ADDR']?:'',
                            'created_on'=>time(),
                            'active'=>1,
                            'is_forwarded'=>0,
                            'response'=>"",
                        );
                    endforeach;
                    if($this->ci->transaction_alerts_m->insert_batch_transaction_alert_forwards($input)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{
                    return TRUE;
                }   
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }











    /*************************TRANSACTIONAL APIS********************/

    /***api transactional withdrawal types****/
    protected $api_withdrawal_types = array(
            1 => 'Loan Disbursement',
        );

    function create_api_transaction_withdrawal($amount=0,$account_id=0,$transaction_type=0,$member_id=0,$group_id=0){
        if($amount && $account_id&&array_key_exists($transaction_type, $this->api_withdrawal_types)&&$member_id&&$group_id){
            if($amount>1){
                $account_balance = $this->ci->accounts_m->get_group_account_balance($account_id);
                if($account_balance>$amount){
                    $account_number = $this->ci->accounts_m->get_group_account_number($account_id);
                    $group_member = $this->ci->members_m->get_group_member($member_id,$group_id);
                    if($group_member){
                        $data = array(
                            'customer' => array(
                                'phone' => $group_member->phone,
                                'user_id' => $group_member->user_id,
                            ),
                            "transaction" => array(
                                'amount' => $amount,
                                'description' => $this->api_withdrawal_types[$transaction_type],
                            ),
                            "result" => array(
                                "callback_url" => site_url('transaction_alerts/api_transaction_withdrawal_callback'),
                            ),
                            "credentials"=> array(
                                "username" => "0763747066",
                                "password" => "31784253"
                            ),
                            "group" => array(
                                "group_id"=> $group_member->group_id,
                                "group_name" => $group_member->group_name,
                            ),
                        );
                        $result = $this->ci->curl->post_json(json_encode($data),'http://api.chamasoft.com/safaricom/b2c_withdrawal_request');
                        if($result){
                            $result = json_decode($result);
                            if($result->response->status==0){
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }else{
                            return FALSE;
                        }
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function update_back_dating_records_cut_off_date($group_id = 0,$cut_off_date = 0){
        if($group_id&&$cut_off_date){
            $result = TRUE;
            //invoices
            if($this->_update_back_dating_invoices_cut_off_date($group_id,$cut_off_date)){

            }else{
                $result = FALSE;
            }
            //statements
            if($this->_update_back_dating_statement_entries_cut_off_date($group_id,$cut_off_date)){

            }else{
                $result = FALSE;
            }
            //account transfers
            if($this->_update_back_dating_account_transfers_cut_off_date($group_id,$cut_off_date)){

            }else{
                $result = FALSE;
            }

            //bank_loan_repayments
            if($this->_update_back_dating_bank_loan_repayments_cut_off_date($group_id,$cut_off_date)){

            }else{
                $result = FALSE;
            }

            //bank_loans
            if($this->_update_back_dating_bank_loans_cut_off_date($group_id,$cut_off_date)){

            }else{
                $result = FALSE;
            }

            //contribution_refunds
            if($this->_update_back_dating_contribution_refunds_cut_off_date($group_id,$cut_off_date)){

            }else{
                $result = FALSE;
            }

            //deposits
            if($this->_update_back_dating_deposits_cut_off_date($group_id,$cut_off_date)){

            }else{
                $result = FALSE;
            }

            //fines
            if($this->_update_back_dating_fines_cut_off_date($group_id,$cut_off_date)){

            }else{
                $result = FALSE;
            }
            
            //loan_invoices
            if($this->_update_back_dating_loan_invoices_cut_off_date($group_id,$cut_off_date)){

            }else{
                $result = FALSE;
            }
            
            //loan_repayments
            if($this->_update_back_dating_loan_repayments_cut_off_date($group_id,$cut_off_date)){

            }else{
                $result = FALSE;
            }

            //loan_statements
            if($this->_update_back_dating_loan_statements_cut_off_date($group_id,$cut_off_date)){

            }else{
                $result = FALSE;
            }
            
            //loans
            if($this->_update_back_dating_loans_cut_off_date($group_id,$cut_off_date)){

            }else{
                $result = FALSE;
            }
            
            //money_market_investments
            if($this->_update_back_dating_money_market_investments_cut_off_date($group_id,$cut_off_date)){

            }else{
                $result = FALSE;
            }
            
            //stock_sales
            if($this->_update_back_dating_stock_sales_cut_off_date($group_id,$cut_off_date)){

            }else{
                $result = FALSE;
            }
            
            //stocks
            if($this->_update_back_dating_stocks_cut_off_date($group_id,$cut_off_date)){

            }else{
                $result = FALSE;
            }
            
            //transaction_statements
            if($this->_update_back_dating_transaction_statements_cut_off_date($group_id,$cut_off_date)){

            }else{
                $result = FALSE;
            }
            
            //withdrawals
            if($this->_update_back_dating_withdrawals_cut_off_date($group_id,$cut_off_date)){

            }else{
                $result = FALSE;
            }

            if($result){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;   
        }
    }

    private function _update_back_dating_invoices_cut_off_date($group_id = 0,$cut_off_date = 0){
        $input = array(
            'invoice_date' => $cut_off_date,
            'due_date' => $cut_off_date,
            'modified_on' => time(), 
        );
        if($this->ci->invoices_m->update_group_back_dating_invoices_cut_off_date($group_id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    private function _update_back_dating_statement_entries_cut_off_date($group_id = 0,$cut_off_date = 0){
        $input = array(
            'transaction_date' => $cut_off_date,
            'contribution_invoice_due_date' => $cut_off_date,
            'modified_on' => time(), 
        );
        if($this->ci->statements_m->update_group_back_dating_statement_entries_cut_off_date($group_id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    private function _update_back_dating_account_transfers_cut_off_date($group_id = 0,$cut_off_date = 0){
        $input = array(
            'transfer_date' => $cut_off_date,
            'modified_on' => time(), 
        );
        if($this->ci->withdrawals_m->update_group_back_dating_account_transfers_cut_off_date($group_id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    private function _update_back_dating_bank_loan_repayments_cut_off_date($group_id = 0,$cut_off_date = 0){
        $input = array(
            'receipt_date' => $cut_off_date,
            'modified_on' => time(), 
        );
        if($this->ci->withdrawals_m->update_group_back_dating_bank_loan_repayments_cut_off_date($group_id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    private function _update_back_dating_bank_loans_cut_off_date($group_id = 0,$cut_off_date = 0){
        $input = array(
            'loan_start_date' => $cut_off_date,
            'loan_end_date' => $cut_off_date,
            'modified_on' => time(), 
        );
        if($this->ci->bank_loans_m->update_group_back_dating_bank_loans_cut_off_date($group_id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    private function _update_back_dating_contribution_refunds_cut_off_date($group_id = 0,$cut_off_date = 0){
        $input = array(
            'refund_date' => $cut_off_date,
            'modified_on' => time(), 
        );
        if($this->ci->contribution_refunds_m->update_group_back_dating_contribution_refunds_cut_off_date($group_id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    private function _update_back_dating_deposits_cut_off_date($group_id = 0,$cut_off_date = 0){
        $input = array(
            'deposit_date' => $cut_off_date,
            'modified_on' => time(), 
        );
        if($this->ci->deposits_m->update_group_back_dating_deposits_cut_off_date($group_id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    private function _update_back_dating_fines_cut_off_date($group_id = 0,$cut_off_date = 0){
        $input = array(
            'fine_date' => $cut_off_date,
            'modified_on' => time(), 
        );
        if($this->ci->fines_m->update_group_back_dating_fines_cut_off_date($group_id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    private function _update_back_dating_loan_invoices_cut_off_date($group_id = 0,$cut_off_date = 0){
        $input = array(
            'invoice_date' => $cut_off_date,
            'due_date' => $cut_off_date,
            'modified_on' => time(), 
        );
        if($this->ci->loan_invoices_m->update_group_back_dating_loan_invoices_cut_off_date($group_id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    private function _update_back_dating_loan_repayments_cut_off_date($group_id = 0,$cut_off_date = 0){
        $input = array(
            'receipt_date' => $cut_off_date,
            'modified_on' => time(), 
        );
        if($this->ci->loan_repayments_m->update_group_back_dating_loan_repayments_cut_off_date($group_id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    private function _update_back_dating_loan_statements_cut_off_date($group_id = 0,$cut_off_date = 0){
        $input = array(
            'transaction_date' => $cut_off_date,
            'modified_on' => time(), 
        );
        if($this->ci->loans_m->update_group_back_dating_loan_statements_cut_off_date($group_id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    private function _update_back_dating_loans_cut_off_date($group_id = 0,$cut_off_date = 0){
        $input = array(
            'disbursement_date' => $cut_off_date,
            'loan_end_date' => $cut_off_date,
            'modified_on' => time(), 
        );
        if($this->ci->loans_m->update_group_back_dating_loans_cut_off_date($group_id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    private function _update_back_dating_money_market_investments_cut_off_date($group_id = 0,$cut_off_date = 0){
        $input = array(
            'investment_date' => $cut_off_date,
            'cash_in_date' => $cut_off_date,
            'modified_on' => time(), 
        );
        if($this->ci->money_market_investments_m->update_group_back_dating_money_market_investments_cut_off_date($group_id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    private function _update_back_dating_stock_sales_cut_off_date($group_id = 0,$cut_off_date = 0){
        $input = array(
            'sale_date' => $cut_off_date,
            'modified_on' => time(), 
        );
        if($this->ci->deposits_m->update_group_back_dating_stock_sales_cut_off_date($group_id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    private function _update_back_dating_stocks_cut_off_date($group_id = 0,$cut_off_date = 0){
        $input = array(
            'purchase_date' => $cut_off_date,
            'modified_on' => time(), 
        );
        if($this->ci->stocks_m->update_group_back_dating_stocks_cut_off_date($group_id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    private function _update_back_dating_transaction_statements_cut_off_date($group_id = 0,$cut_off_date = 0){
        $input = array(
            'transaction_date' => $cut_off_date,
            'modified_on' => time(), 
        );
        if($this->ci->transaction_statements_m->update_group_back_dating_transaction_statements_cut_off_date($group_id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    private function _update_back_dating_withdrawals_cut_off_date($group_id = 0,$cut_off_date = 0){
        $input = array(
            'withdrawal_date' => $cut_off_date,
            'modified_on' => time(), 
        );
        if($this->ci->withdrawals_m->update_group_back_dating_withdrawals_cut_off_date($group_id,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function get_group_member_savings($group_id=0,$member_id = 0){        
        if($member_id){
            $total_group_member_contributions = $this->ci->deposits_m->get_group_member_total_contributions($member_id,$group_id);
            //$total_group_member_contribution_arrears = $this->ci->statements_m->get_member_contribution_balance($group_id,$member_id);
            //$total_group_member_total_fines = $this->ci->deposits_m->get_group_member_total_fines($member_id,$group_id);
            //$total_group_member_fine_arrears = $this->ci->statements_m->get_member_fine_balance($group_id,$member_id);
            $ongoing_loan_amounts_payable = array();
            $ongoing_loan_amounts_paid = array();
            $base_where = array('member_id'=>$member_id,'is_fully_paid'=>0);
            $ongoing_member_loans = $this->ci->loans_m->get_many_by($base_where,$group_id);
            $total_loan_lump_sum_balance = 0;
            foreach ($ongoing_member_loans as $ongoing_member_loan){
                $ongoing_loan_amounts_payable[$ongoing_member_loan->id]
                = $this->ci->loans_m->get_summation_for_invoice($ongoing_member_loan->id)->total_amount_payable;
                $ongoing_loan_amounts_paid[$ongoing_member_loan->id]
                = $this->ci->loan_repayments_m->get_loan_total_payments($ongoing_member_loan->id);
                $total_loan_lump_sum_balance+=$this->ci->loan_invoices_m->get_loan_lump_sum_as_date($ongoing_member_loan->id,time());
            }
            $ongoing_payable_loan_amount = array_sum($ongoing_loan_amounts_payable);
            $ongoing_loans_paid_amount = array_sum($ongoing_loan_amounts_paid);
            $total_loan_balances = $ongoing_payable_loan_amount - $ongoing_loans_paid_amount;
            $total_member_contributions =  $total_group_member_contributions;
           // $total_member_fines  = $total_group_member_total_fines - ($total_group_member_fine_arrears);
            $total_member_savings = $total_member_contributions  - $total_loan_balances;
            return $total_member_savings;
        }else{
            $this->ci->session->set_flashdata('error',"Member id is missing");
            return FALSE;
        }      
        
    }

    function make_group_arrears_payment($group = array(),$user=array(),$member=array(),$account=array(),$amount=0,$initiate_request=1){
        if($group&&$user&&$member&&$amount&&$account){
            $total_amount = valid_currency($amount);
            $deposit_types = array();
            $contribution_ids = array();
            $fine_category_ids = array();
            $loan_ids = array();
            $balance_amount = $total_amount;
            $total_amount_to_pay = 0;
            $index = 0;
            $amounts= array();
            $descriptions = array();
            $balances = array();
            if($balance_amount>0){
                $group_contribution_fines = $this->ci->contributions_m->get_active_group_contribution_fine_options($group->id);
                if($group_contribution_fines){
                    foreach ($group_contribution_fines as $id => $name) {
                        if($balance_amount>0){
                            $balance = $this->ci->statements_m->get_member_fine_balance($group->id,$member->id,$id);
                            if($balance>0){
                                $deposit_types[$index] = 2;
                                $fine_category_ids[$index] = 'contribution-'.$id;
                                if($balance>=$balance_amount){
                                    $amount = $balance_amount;
                                }else{
                                    $amount = $balance;
                                }
                                $amounts[$index] = $amount; 
                                $total_amount_to_pay+=$amount;
                                $balance_amount -= $amount;
                                $index++;
                            }
                        }
                    }
                }
            }
            if($balance_amount>0){
                $contributions = $this->ci->contributions_m->get_active_group_contribution_options($group->id);
                if($contributions){
                    foreach($contributions as $id=>$name) {
                        if($balance_amount>0){
                            $balance = $this->ci->statements_m->get_member_contribution_balance($group->id,$member->id,$id)?:0;
                            if($balance>0){
                                $deposit_types[$index] = 1;
                                $contribution_ids[$index] = $id;
                                if($balance>=$balance_amount){
                                    $amount = $balance_amount;
                                }else{
                                    $amount = $balance;
                                }
                                $balance_amount -= $amount;
                                $amounts[$index] = $amount; 
                                $total_amount_to_pay+=$amount;
                                $index++;
                            }
                        }
                    }
                }
            }
            if($balance_amount>0){
                $group_fine_category_options = $this->ci->fine_categories_m->get_group_active_fine_categories($group->id);
                if($group_fine_category_options){
                    foreach ($group_fine_category_options as $group_fine_category_option) {
                        if($balance_amount>0){
                            $balance = $this->ci->statements_m->get_member_fine_balance($group->id,$member->id,'',$group_fine_category_option->id);
                            if($balance>0){
                                $balances[$index] = $balance;
                                $deposit_types[$index] = 2;
                                $fine_category_ids[$index] = 'fine_category-'.$group_fine_category_option->id;
                                if($balance>=$balance_amount){
                                    $amount = $balance_amount;
                                }else{
                                    $amount = $balance;
                                }
                                $balance_amount -= $amount;
                                $amounts[$index] = $amount; 
                                $total_amount_to_pay+=$amount;
                                $index++;
                            }
                        }
                    }
                }
            }
            if($balance_amount>0){
                if(isset($contributions)){
                    foreach ($contributions as $id => $name) {
                        if($balance_amount>0){
                            $deposit_types[$index] = 1;
                            $contribution_ids[$index] = $id;
                            $amount = $balance_amount;
                            $balance_amount -= $amount;
                            $amounts[$index] = $amount; 
                            $total_amount_to_pay+=$amount;
                            $index++;
                        }
                    }
                }
            }
            if($balance_amount>0){
                if(isset($group_fine_category_options)){
                    foreach ($group_fine_category_options as $group_fine_category_option) {
                        if($balance_amount>0){
                            $deposit_types[$index] = 2;
                            $fine_category_ids[$index] = 'fine_category-'.$id;
                            $amount = $balance_amount;
                            $balance_amount -= $amount;
                            $amounts[$index] = $amount; 
                            $total_amount_to_pay+=$amount;
                            $index++;
                        }
                    }
                }
            }
            if($total_amount_to_pay == $total_amount){
                $transactions = new StdClass();
                $transactions->total_amount = currency($total_amount);
                $transactions->amounts = $amounts;
                $transactions->contribution_ids = $contribution_ids;
                $transactions->fine_category_ids = $fine_category_ids;
                $transactions->loan_ids = $loan_ids;
                $transactions->descriptions = $descriptions;
                $transactions->deposit_type = $deposit_types;
                if($result = $this->make_online_group_payment($user,$group,$member,$account,$transactions,$initiate_request)){
                    return $result;
                }else{
                    $this->ci->session->set_flashdata('error',"Server error: ".$this->session->flashdata('message'));
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','We could not reconcile for individual item payment. Kindly use make payment option to pay for individual items');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Ensure all parameters are passed');
            return FALSE;
        }
    }

    function make_online_group_payment($user=array(),$group=array(),$member = array(),$account=array(),$transaction=array(),$initiate_request =1,$phone=0){
        if($user && $account && $group && $transaction && $member){
            $phone = valid_phone($phone)?:$user->phone;
            $account_number = $account->account_number;
            $account_id = 'bank-'.$account->id;
            $account_password = $account->account_password;
            $input = array(
                'contribution_ids' => serialize($transaction->contribution_ids),
                'fine_category_ids' => serialize($transaction->fine_category_ids),
                'loan_ids' => serialize($transaction->loan_ids),
                'descriptions' => serialize($transaction->descriptions),
                'amounts' => serialize($transaction->amounts),
                'payment_for' => serialize($transaction->deposit_type),
                'reference_number' => '',
                "account_id" => $account_id,
                "group_id" => $group->id,
                "member_id" => $member->id,
                "user_id" => $user->id,
                "amount" => $transaction->total_amount,
                "phone" => $phone,
                "status" => 1,
                "active" => 1,
                "created_on" => time(),
                "created_by" => $user->id,
            );
            if($id = $this->ci->deposits_m->insert_online_payment_request($input)){
                $reference_number = time()+$id;
                if($initiate_request){
                    $post_data = json_encode(array(
                        "request_id" => time(),
                        "data" => array(
                            'transaction' => array(
                                "amount" => $transaction->total_amount,
                                "account_number" => $account_number,
                                "reference_number" => $reference_number,
                                "security_pass" => openssl_key_encrypt($account_password),
                                "channel"  =>  1
                            ),
                            "user" => array(
                                'phone_number' => $phone,
                            ),
                            "callback_url" => site_url('/transaction_alerts/online_banking_payment'),
                        ),
                    ));
                    if(preg_match('/(demo\.websacco\.com)/',$_SERVER['HTTP_HOST']) || preg_match('/(uat\.websacco\.com)/',$_SERVER['HTTP_HOST'])){
                        $url = "https://api-test.chamasoft.com:443/api/transactions/make_payment";
                    }else{
                        $url = "https://api.chamasoft.com:443/api/transactions/make_payment";
                    }
                    if($response = $this->ci->curl->post_json_payment($post_data,$url)){
                        if($res = json_decode($response)){
                            if(isset($res->data)){
                                $update = array(
                                    "reference_number" => $reference_number,
                                    "response_code" => $res->data->response_code,
                                    "response_description" => $res->data->response_description,
                                    "status" => ($res->code == '200')?1:2,
                                );
                                if($this->ci->deposits_m->update_payment_request($id,$update)){
                                    return $res;
                                }else{
                                    $this->ci->session->set_flashdata('error','Error occured updating payment');
                                    return FALSE;  
                                }
                            }else{
                                return $res->description;
                            }
                        }else{
                            $this->ci->session->set_flashdata('error','Error occured while processing request. Try again later');
                            return FALSE;  
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Could not make payment. '.$this->ci->session->flashdata('error'));
                        return FALSE;
                    }
                }else{
                    $update = array(
                        "reference_number" => $reference_number,
                        "response_code" => 0,
                        "response_description" => 'successful',
                        "status" => 1,
                    );
                    if($this->ci->deposits_m->update_payment_request($id,$update)){
                        return $reference_number;
                    }else{
                        $this->ci->session->set_flashdata('error','Error occured updating payment');
                        return FALSE;  
                    }
                }
            }else{
                $this->ci->session->set_flashdata('error','Error occured. Could not proceed with the payment request. Try again later');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Essential parameters are missing');
            return FALSE;
        }
        
        if($response){
            if($response = json_decode($response)){
                $result = $response->response;
                if(isset($result->id)){
                    $update = array(
                        'contribution_ids' => serialize($contribution_ids),
                        'fine_category_ids' => serialize($fine_category_ids),
                        'loan_ids' => serialize($loan_ids),
                        'descriptions' => serialize($descriptions),
                        'amounts' => serialize($amounts),
                        'status' => ($result->status == 1)?1:2,
                        'status_description' => $result->message,
                        'payment_for' => serialize($deposit_type),
                    );
                    $this->safaricom_m->update($result->id,$update);
                }
                $response = array(
                    'status' => $result->status,
                    'message' => $result->message,
                    'time' => time(),
                    'request_id' => isset($result->request_id)?$result->request_id:0,
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Invalid response while initiating payment',
                    'time' => time(),
                );  
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not initiate payment. Try again later',
                'time' => time(),
            );
        }
    }

    function calculate_convenience_charge($user=array(),$group=array(),$member = array(),$account=array(),$amount=0,$type=1){
        if($user && $account && $group && $amount && $member && $type){
            $account_number = $account->account_number;
            $account_id = 'bank-'.$account->id;
            $account_password = $account->account_password;
            $post_data = json_encode(array(
                "request_id" => time(),
                "data" => array(
                    'transaction' => array(
                        "amount" => $amount,
                        "account_number" => $account_number,
                        "security_pass" => openssl_key_encrypt($account_password),
                    )
                ),
            ));
            if($type==1){
                $url = "https://api.chamasoft.com:443/api/transactions/calculate_deposit_charge";
            }elseif($type==2){
                $url = "https://api.chamasoft.com/api/transactions/calculate_disbursement_charge";
            }            
            if($response = $this->ci->curl->post_json_payment($post_data,$url)){
                if($res = json_decode($response)){
                    if(isset($res->data)){
                        return $res->data->charge_amount;
                    }else{
                        return $res->description;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Error occured while processing request. Try again later');
                    return FALSE;  
                }
            }else{
                $this->ci->session->set_flashdata('error','Could not make payment. '.$this->ci->session->flashdata('error'));
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Essential parameters are missing');
            return FALSE;
        }
        if($response){
            if($response = json_decode($response)){
                $result = $response->response;
                if(isset($result->id)){
                    $update = array(
                        'contribution_ids' => serialize($contribution_ids),
                        'fine_category_ids' => serialize($fine_category_ids),
                        'loan_ids' => serialize($loan_ids),
                        'descriptions' => serialize($descriptions),
                        'amounts' => serialize($amounts),
                        'status' => ($result->status == 1)?1:2,
                        'status_description' => $result->message,
                        'payment_for' => serialize($deposit_type),
                    );
                    $this->safaricom_m->update($result->id,$update);
                }
                $response = array(
                    'status' => $result->status,
                    'message' => $result->message,
                    'time' => time(),
                    'request_id' => isset($result->request_id)?$result->request_id:0,
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Invalid response while initiating payment',
                    'time' => time(),
                );  
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not initiate payment. Try again later',
                'time' => time(),
            );
        }
    }

    function reconcile_payment_request($request_id=0,$transaction_alert_id=0,$transaction_id=0,$transaction_date=0,$deposit_method=1){
        if($request_id&&$transaction_alert_id&&$transaction_date){
            if($request = $this->ci->deposits_m->get_online_payment($request_id)){
                $amounts = unserialize($request->amounts);
                $descriptions = unserialize($request->descriptions);
                $loan_ids = unserialize($request->loan_ids);
                $fine_category_ids = unserialize($request->fine_category_ids);
                $contribution_ids = unserialize($request->contribution_ids);
                $group_id = $request->group_id;
                $deposit_date = $transaction_date;
                $member_id = $request->member_id;
                $success = 0;
                $fail = 0;
                $account_id = $request->account_id;
                $payment_for = unserialize($request->payment_for);
                if($payment_for){
                    foreach ($payment_for as $key =>$payment) {
                        $description = (isset($descriptions[$key])?$descriptions[$key]:'').' Payment transaction receipt number '.$transaction_id;
                        $amount = currency($amounts[$key]);
                        if($payment == 1){//pay contribution
                            $contribution_id = $contribution_ids[$key];
                            if($this->record_contribution_payment($group_id,$deposit_date,$member_id,$contribution_id,$account_id,$deposit_method,$description,$amount,1,1,$transaction_alert_id)){
                                ++$success;
                            }else{
                                ++$fail;
                            }
                        }else if($payment == 2){//pay fine
                            $fine_category = $fine_category_ids[$key];
                            if($this->record_fine_payment($group_id,$deposit_date,$member_id,$fine_category,$account_id,$deposit_method,$description,$amount,1,1,$transaction_alert_id)){
                                ++$success;
                            }else{
                                ++$fail;
                            }
                        }else if ($payment == 3) {//pay loan
                            $loan_id = $loan_ids[$key];
                            $member = $this->ci->members_m->get_group_member($member_id,$group_id);
                            $created_by = $this->ci->ion_auth->get_user($request->user_id);
                            if($this->ci->loan->record_loan_repayment($group_id,$deposit_date,$member,$loan_id,$account_id,$deposit_method,$description,$amount,1,1,$created_by,$member_id,$transaction_alert_id)){
                                ++$success;
                            }else{
                                ++$fail;
                            }
                        }else if ($payment == 4) {//pay misceleneous
                            if($this->record_miscellaneous_payment($group_id,$deposit_date,$member_id,$account_id,$deposit_method,$description,$amount,1,1,$transaction_alert_id)){
                                ++$success;
                            }else{
                                ++$fail;
                            }
                        }
                    }
                    if($this->mark_transaction_alert_as_reconciled($transaction_alert_id,$request->group_id)){
                        if($this->ci->deposits_m->update_payment_request($request->id,array(
                            'is_reconcilled' => 1,
                            'modified_on' => time(),
                            'transaction_alert_id' => $transaction_alert_id,
                            'modified_on' => time(),
                        ))){
                            return TRUE;
                        }else{
                            $this->ci->session->set_flashdata('error','Deposit not reconciled');
                            return FALSE;
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Didnt mark transaction reconciled');
                        return TRUE;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','No payment for');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Missing request');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Missing params');
            return FALSE;
        }
    }

    function reconcile_loan_application($application_id=0,$transaction_alert_id=0){
        if($application_id&&$transaction_alert_id){
            $application = $this->ci->loan_applications_m->get($application_id);
            if($application){
                if($this->ci->loan->create_loan_from_loan_application($application,$transaction_alert_id)){
                    if($this->mark_transaction_alert_as_reconciled($transaction_alert_id,$application->group_id)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Missing application');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Missing params');
            return FALSE;
        }
    }

    function reconcile_withdrawal_request_bank_charges($request_id=0,$transaction_alert_id=0,$transaction_id=0,$transaction_date=0,$group_id=0,$account_id=0){
        if($request_id&&$transaction_alert_id&&$transaction_date&&$group_id&&$account_id){
            if($request = $this->ci->withdrawals_m->get_group_withdrawal_request($request_id,$group_id)){
                $amount = currency($request->disbursement_charges);
                $description = $request->disbursement_result_description.' withdrawal charges : '.$transaction_id;
                $name = 'Bank Charges';
                $slug = 'bank-charges';
                $description.=' '.$request->description;
                $account_id = 'bank-'.$account_id;
                if($expense_category = $this->ci->expense_categories_m->get_by_slug($slug,'',$group_id)){
                    $expense_category_id = $expense_category->id;
                }else{
                    $input = array(
                        'name'  =>  $name,
                        'slug'  =>  $slug,
                        'is_an_administrative_expense_category'  =>  1,
                        'description'  =>  'Track bank charges',
                        'group_id'  =>  $group_id,
                        'active'    =>  1,
                        'created_on'    =>  time(),
                    );
                    $expense_category_id = $this->ci->expense_categories_m->insert($input);
                }
                if($this->record_expense_withdrawal($group_id,$transaction_date,$expense_category_id,1,$account_id,$description,$amount,$transaction_alert_id)){
                    if($this->mark_transaction_alert_as_reconciled($transaction_alert_id,$group_id)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Missing request');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Missing params');
            return FALSE;
        }
    }

    function reconcile_loan_application_bank_charges($application_id=0,$transaction_alert_id=0,$transaction_id=0,$transaction_date=0,$group_id=0,$account_id=0){
        if($application_id&&$transaction_alert_id&&$transaction_date&&$group_id&&$account_id){
            $application = $this->ci->loan_applications_m->get($application_id);
            if($application){
                $amount = currency($application->disbursement_charges);
                $description = $application->disbursement_result_description.' withdrawal charges : '.$transaction_id;
                $name = 'Bank Charges';
                $slug = 'bank-charges';
                $description.= isset($application->description)?' '.$application->description:'';
                $account_id = 'bank-'.$account_id;
                if($expense_category = $this->ci->expense_categories_m->get_by_slug($slug,'',$group_id)){
                    $expense_category_id = $expense_category->id;
                }else{
                    $input = array(
                        'name'  =>  $name,
                        'slug'  =>  $slug,
                        'is_an_administrative_expense_category'  =>  1,
                        'description'  =>  'Track bank charges',
                        'group_id'  =>  $group_id,
                        'active'    =>  1,
                        'created_on'    =>  time(),
                    );
                    $expense_category_id = $this->ci->expense_categories_m->insert($input);
                }
                if($this->record_expense_withdrawal($group_id,$transaction_date,$expense_category_id,1,$account_id,$description,$amount,$transaction_alert_id)){
                    if($this->mark_transaction_alert_as_reconciled($transaction_alert_id,$group_id)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Missing application');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Missing params');
            return FALSE;
        }
    }


    function record_funds_transfer_request($user=array(),$group=array(),$requesting_member=array(),$amount = 0,$request_data = array(),$group_currency="KES"){
        if($user&&$group&&$requesting_member&&currency($amount)&&$request_data){
            if($group_default_bank_account = $this->ci->bank_accounts_m->get_group_default_bank_account($group->id)){
                $balance = ($group_default_bank_account->current_balance+($group_default_bank_account->initial_balance?:0));
                if($balance>=$amount){
                    $valid_entry = FALSE;
                    $recipient = isset($request_data->recipient)?$request_data->recipient:'';
                    $withdrawal_for = isset($request_data->withdrawal_for)?$request_data->withdrawal_for:'';
                    $expense_category_id = isset($request_data->expense_category_id)?$request_data->expense_category_id:'';
                    $description = isset($request_data->description)?$request_data->description:'';
                    $phone = isset($request_data->phone)?valid_phone($request_data->phone):'';
                    $member_id = isset($request_data->member_id)?$request_data->member_id:0;
                    $paybill_number = isset($request_data->paybill_number)?$request_data->paybill_number:'';
                    $paybill_account_number = isset($request_data->paybill_account_number)?$request_data->paybill_account_number:'';
                    $bank_id = isset($request_data->bank_id)?$request_data->bank_id:'';
                    $account_number = isset($request_data->account_number)?$request_data->account_number:'';
                    $contribution_id = isset($request_data->contribution_id)?$request_data->contribution_id:'';
                    $transfer_from = isset($request_data->transfer_from)?$request_data->transfer_from:'';
                    $transfer_to = isset($request_data->transfer_to)?$request_data->transfer_to:'';
                    if($recipient == 1){
                        if($phone){

                        }else{
                            $this->ci->session->set_flashdata('error','Recipient phone number is invalid');
                            return FALSE;
                        }
                    }elseif ($recipient == 2) {
                        if($paybill_number && $paybill_account_number){

                        }else{
                            $this->ci->session->set_flashdata('error','Recipient paybill and paybill account number are invalid');
                            return FALSE;
                        }
                    }elseif($recipient == 3){
                        if($bank_id && $account_number){

                        }else{
                            $this->ci->session->set_flashdata('error','Recipient Bank and account number are invalid');
                            return FALSE;
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Recipient selected in invalid');
                        return FALSE;
                    }
                    if(array_key_exists($withdrawal_for, $this->withdrawal_request_transaction_names)){
                        if($withdrawal_for==1){//expense
                            if($this->ci->expense_categories_m->expense_category_exists($expense_category_id,$group->id)){
                                $valid_entry = TRUE;
                            }else{
                                $this->ci->session->set_flashdata('error','Selected expense category is invalid. Kindly select a different expense category');
                                return FALSE;
                            }
                        }else if ($withdrawal_for==2) {
                            if($member = $this->ci->members_m->get_group_member($member_id,$group->id)){
                                if($contribution = $this->ci->contributions_m->get_group_contribution($contribution_id,$group->id)){
                                    $valid_entry = TRUE;
                                }else{
                                    $this->ci->session->set_flashdata('error','Contribution to refund from not found. Kindly select again');
                                    return FALSE;
                                }
                            }else{
                                $this->ci->session->set_flashdata('error','Selected member not found. Kindly try again');
                                return FALSE;
                            }
                        }elseif ($withdrawal_for==3) {
                            if($member = $this->ci->members_m->get_group_member($member_id,$group->id)){
                                $valid_entry = TRUE;
                            }else{
                                $this->ci->session->set_flashdata('error','Selected member not found. Kindly try again');
                                return FALSE;
                            }
                        }elseif ($withdrawal_for==5) {
                            if($this->ci->accounts_m->check_if_group_account_exists($transfer_from,$group->id)){
                                if($this->ci->accounts_m->check_if_group_account_exists($transfer_to,$group->id)){
                                    $valid_entry = TRUE;
                                }else{
                                    $this->ci->session->set_flashdata('error','Could not find account to transfer to. Kindly Select another account');
                                    return FALSE;
                                }
                            }else{
                                $this->ci->session->set_flashdata('error','Could not find account to transfer from. Kindly Select another account');
                                return FALSE;
                            }
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Withdrawal for reason type is not accepted');
                        return FALSE;
                    }
                    if($valid_entry){
                        $input = array(
                            'user_id' => $user->id,
                            'group_id' => $group->id,
                            'member_id' => $requesting_member->id,
                            'amount' => $amount,
                            'withdrawal_for' => $withdrawal_for,
                            'expense_category_id' => $expense_category_id,
                            'description' => $description,
                            'request_date' => time(),
                            'recipient' => $recipient,
                            'recipient_member_id' => $member_id,
                            'contribution_id' => $contribution_id,
                            'recipient_phone_number' => $phone,
                            'recipient_paybill_number' => $paybill_number,
                            'recipient_paybill_account_number' => $paybill_account_number,
                            'recipient_bank_id'=> $bank_id,
                            'recipient_account_number' => $account_number,
                            'transfer_from_account_id' => $transfer_from,
                            'transfer_to_account_id' => $transfer_to,
                            'created_on' => time(),
                            'status' => 0,
                            'active' => 1,
                            'created_by' => $user->id,
                        );
                        if($input){
                            if($withdrawal_request_id = $this->ci->withdrawals_m->insert_withdrawal_request($input)){
                                if($this->_notify_signatories_of_withdrawal_request($withdrawal_request_id,$group,$user,$requesting_member->id,$amount,$group_currency)){
                                    return $withdrawal_request_id;
                                }else{
                                    $this->ci->session->set_flashdata('error','Request failed: Could not send notification to group signatories');
                                    return FALSE;
                                }
                            }else{
                                $this->ci->session->set_flashdata('error','Could not make withdrawal request. Try again after some time');
                                return FALSE;
                            }
                        }else{
                            $this->ci->session->set_flashdata('error','Could not make withdrawal request. Try again after some time');
                            return FALSE;
                        }
                    }else{
                        $this->ci->session->set_flashdata('error','Invalid entries. Kindly check your request');
                        return FALSE;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Insufficient bank balance '.$group_currency." ".number_to_currency($balance));
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Could not find group bank account profile');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Could not complete the process. Withdrawals request details are missing');
            return FALSE;
        }
    }

    function notify_signatories_of_withdrawal_request($withdrawal_request_id = 0,$group = array(),$user = array(),$member = array(),$withdrawal_request = array(),$group_currency='',$signatories=array()){
        if($withdrawal_request_id&&$group&&$member&&$user&&$withdrawal_request&&$signatories){
            $email_alerts = array();
            $sms_alerts = array();
            $notification_alerts = array();
            $amount = currency($withdrawal_request->amount);
            foreach($signatories as $signatory):
                //create message alert
                //create email alert
                //create notification alert
                //create withdrawal request
                $withdrawal_for_message = $this->withdrawal_request_transaction_names[$withdrawal_request->withdrawal_for];
                $message = 'Kindly respond to a '.$withdrawal_for_message.' withdrawal request of '.$group_currency.' '.number_to_currency($amount).' made by '.$member->first_name.' '.$member->last_name.' and expires in 12 hours';
                $withdrawal_approval_request = array(
                    'withdrawal_request_id' => $withdrawal_request_id,
                    'member_id' => $signatory->id,
                    'description' => $message,
                    'group_id' => $group->id,
                    'status' => ($signatory->id == $member->id)?1:0,
                    'is_approved' => ($signatory->id == $member->id)?1:0,
                    'approved_on' => ($signatory->id == $member->id)?time():'',
                    'is_declined' => 0,
                    'active' => 1,
                    'created_on' => time(),
                    'created_by' => $member->user_id,
                );
                $withdrawal_approval_request_id = $this->ci->withdrawals_m->insert_withdrawal_approval_request($withdrawal_approval_request);
                if($signatory->id == $member->id){
                    continue;
                }
                $this->ci->messaging->send_withdrawal_approval_request_sms($group,$signatory,$user,$group_currency,$amount,$withdrawal_for_message);                    
                $notifications[] = array(
                    'subject' => 'Withdrawal Approval Request',
                    'message' => $message,
                    'from_user' => $user,
                    'from_member_id' => $member->id,
                    'to_user_id' => $signatory->user_id,
                    'to_member_id' => $signatory->id,
                    'group_id' => $group->id,
                    'call_to_action' => "Approve or Decline withdrawal request",
                    'call_to_action_link' => 'group/withdrawals/withdrawal_requests',
                    'category' => 17,
                    'withdrawal_request_id' => $withdrawal_request_id,
                );
            endforeach;
            $this->ci->notifications->create_bulk($notifications);
            return TRUE;
        }else{
            $this->ci->session->set_flashdata('error','Some fields are missing and thus can not create a wuthdrawal approval request');
            return FALSE;
        }
    }

    function process_batch_withdrawal_requests($withdrawal_request = array(),$group_currency= "KES",$requesting_member=array(),$group=array(),$user=array()){
        // Kindly consult before patching this function.. this is real money, again kindly. Thank you
        $values_are_valid = TRUE;
        if(empty($withdrawal_request)||!$group_currency||empty($requesting_member)){
            $this->ci->session->set_flashdata('warning','Empty requests submitted');
            return FALSE;
        }else{
            $input = array(
                'withdrawal_for' => $withdrawal_request->withdrawal_for,
                'member_id' => $withdrawal_request->member_id,
                'loan_type_id' => isset($withdrawal_request->loan_type_id)?$withdrawal_request->loan_type_id:'',
                'expense_category_id' => isset($withdrawal_request->expense_category_id)?$withdrawal_request->expense_category_id:'',
                'description' => isset($withdrawal_request->description)?$withdrawal_request->description:'',
                'contribution_id' => isset($withdrawal_request->contribution_id)?$withdrawal_request->contribution_id:'',
                'amount' => isset($withdrawal_request->amount)?$withdrawal_request->amount:'',
                'recipient_id' => isset($withdrawal_request->recipient)?$withdrawal_request->recipient:'',
                'bank_account_id' => isset($withdrawal_request->bank_account_id)?$withdrawal_request->bank_account_id:'',
                'account_to_id' => isset($withdrawal_request->account_to_id)?$withdrawal_request->account_to_id:'',
                'request_date' => time(),
                'reference_number' => time(),
                'transfer_to' => isset($withdrawal_request->transfer_to)?$withdrawal_request->transfer_to:'',
                'user_id' => $requesting_member->user_id,
                'expiry_time' => strtotime('+12 hours',time()),
                'created_on' => time(),
                'created_by' => $requesting_member->user_id,
                'status' => 1,
                'is_approved' => 1,
                'is_declined' => 0,
                'decline_reason' => '',
                'is_disbursed' => 0,
                'active' => 1,
            );
            if($withdrawal_request_id = $this->ci->withdrawals_m->insert_withdrawal_request($input)){
                 
                    return TRUE;
            }
            else{
                $this->ci->session->set_flashdata('error','Could not create the withdraw request. Try again');
                return FALSE;
            }
        }
    }

    function calculate_withdrawal_charges($account=array(),$amount=0){
        if($account&&$amount){
            $account_number = $account->account_number;
            $account_password = $account->account_password;
            $post_data = json_encode(array(
                "request_id" => time(),
                "data" => array(
                    'transaction' => array(
                        "amount" => $amount,
                        "account_number" => $account_number,
                        "security_pass" => openssl_key_encrypt($account_password),
                    )
                ),
            ));
            if(preg_match('/\.local/', $_SERVER['HTTP_HOST']) || preg_match('/uat\.chamasoft\.com/', $_SERVER['HTTP_HOST']) || preg_match('/chamasoftbeta/', $_SERVER['HTTP_HOST'])){
                $url = "https://api-test.chamasoft.com:443/api/transactions/calculate_disbursement_charge";
            }else{
               $url = "https://api.chamasoft.com:443/api/transactions/calculate_disbursement_charge"; 
            }
            if($response = $this->ci->curl->post_json_payment($post_data,$url)){
                if($res = json_decode($response)){
                    if(isset($res->data)){
                        return $res->data->charge_amount;
                    }else{
                        return $res->description;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Error occured while processing request. Try again later');
                    return FALSE;  
                }
            }else{
                $this->ci->session->set_flashdata('error','Could not make payment. '.$this->ci->session->flashdata('error'));
                return FALSE;
            }

        }
    }

    function process_batch_withdrawal_requests_websacco($withdrawal_request = array(),$group_currency= "KES",$requesting_member=array(),$group=array(),$user=array()){
        $values_are_valid = TRUE;
        if(empty($withdrawal_request)||!$group_currency||empty($requesting_member)||empty($group)||empty($user)){
            $this->ci->session->set_flashdata('error','Empty requests submitted');
            return FALSE;
        }else{
            // check the requesting person.
            $requesting_user = $this->ci->ion_auth->get_user($requesting_member->user_id);
            if($user->id == $requesting_user->id ||  ENVIRONMENT == 'development'){
                // get the default group account.
                if($group_default_bank_account = $this->ci->bank_accounts_m->get_group_default_bank_account($group->id)){

                    // calculate the convenience charge.
                    $balance = currency(($group_default_bank_account->current_balance+($group_default_bank_account->initial_balance?:0)));
                    $withdrawal_charges = currency($this->calculate_withdrawal_charges($group_default_bank_account,$withdrawal_request->amount));
                    
                    // check if the group has funds for withdrawal.
                    if(($balance-$withdrawal_charges)>=$withdrawal_request->amount){

                        // if group has funds for charge, record the withdrawal request.
                        $input = array(
                            'withdrawal_for' => $withdrawal_request->withdrawal_for,
                            'member_id' => $withdrawal_request->member_id,
                            'loan_type_id' => isset($withdrawal_request->loan_type_id)?$withdrawal_request->loan_type_id:'',
                            'expense_category_id' => isset($withdrawal_request->expense_category_id)?$withdrawal_request->expense_category_id:'',
                            'description' => isset($withdrawal_request->description)?$withdrawal_request->description:'',
                            'contribution_id' => isset($withdrawal_request->contribution_id)?$withdrawal_request->contribution_id:'',
                            'amount' => isset($withdrawal_request->amount)?$withdrawal_request->amount:'',
                            'recipient_id' => isset($withdrawal_request->recipient)?$withdrawal_request->recipient:'',
                            'bank_account_id' => isset($withdrawal_request->bank_account_id)?$withdrawal_request->bank_account_id:'',
                            'account_to_id' => isset($withdrawal_request->account_to_id)?$withdrawal_request->account_to_id:'',
                            'request_date' => time(),
                            'transfer_to' => isset($withdrawal_request->transfer_to)?$withdrawal_request->transfer_to:'',
                            'group_id' => $group->id,
                            'user_id' => $requesting_member->user_id,
                            'expiry_time' => strtotime('+12 hours',time()),
                            'created_on' => time(),
                            'created_by' => $requesting_member->user_id,
                            'status' => 0,
                            'is_approved' => 1,
                            'is_declined' => 0,
                            'decline_reason' => '',
                            'is_disbursed' => 0,
                            'active' => 1,
                        );

                        // signatories. get all the group officials.

                        $signatories = $this->ci->members_m->get_active_group_role_holder_member_details($group->id);

                        if($withdrawal_request_id = $this->ci->withdrawals_m->insert_withdrawal_request($input)){
                            if($this->notify_signatories_of_withdrawal_request($withdrawal_request_id,$group,$user,$requesting_member,$withdrawal_request,$group_currency,$signatories)){
                                return TRUE;
                            }else{
                                return FALSE;
                            }
                        }else{
                            $this->ci->session->set_flashdata('error','Could not create the withdraw request. Try again');
                            return FALSE;
                        }
                        // notify the signatories.

                    }else{
                        $this->ci->session->set_flashdata('error','Group account has insufficient balance: '.$group_currency." ".number_to_currency($balance).'. Kindly note the withdrawal charges are KES '.number_to_currency($withdrawal_charges).' Ensure group balance is at least '.$group_currency.' '.number_to_currency($balance+$withdrawal_charges).' to initiate this transaction');
                        return FALSE;   
                    }

                }else{
                    $this->ci->session->set_flashdata('error','Could not find group bank profile');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Requesting user profile does not match member user profile. Logout and login again');
                return FALSE;
            }
        }
    }

    function mark_withdrawal_request_expired($request = array()){
        if($request){
            $input = array(
                'is_approved'=>0,
                'is_declined'=>1,
                'status' => 1,
                'declined_by'=>$request->created_by,
                'decline_reason'=>'Signatories took too long to respond past the expiry time',
                'modified_by'=>$request->created_by,
                'modified_on'=>time()
            );
            $signatories = $this->ci->bank_accounts_m->get_group_active_account_signatories($request->bank_account_id,$request->group_id);
            if($this->ci->withdrawals_m->update_withdrawal_request($request->id,$input)){
                $group = $this->ci->groups_m->get($request->group_id);
                $group_currency = $this->ci->groups_m->get_this_group_currency($request->group_id); 
                $this->ci->messaging->send_withdrawal_request_decline_sms_notification($group,$group_currency,$request,$this->withdrawal_request_transaction_names,FALSE,$signatories,TRUE);
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    function decline_withdrawal_request($user_id =0,$group=array(),$withdrawal_request_id=0,$member_id=0,$decline_reason='',$priority_decline=0,$group_currency=''){
        if($group&&$withdrawal_request_id&&$member_id&&$user_id){
            if($decline_reason){
                $post = $this->ci->withdrawals_m->get_group_withdrawal_request($withdrawal_request_id,$group->id);
                if($post){
                    if($withdrawal_approval_request=$this->ci->withdrawals_m->get_group_member_withdrawal_approval_request_by_member_id($post->id,$member_id)){
                        if($withdrawal_approval_request->status && $priority_decline == FALSE){
                            $this->ci->session->set_flashdata('error','You have already responded to this withdrawal request');
                            return FALSE;
                        }else{
                            $input = array(
                                'modified_on' => time(),
                                'modified_by' => $user_id,
                                'status' => 1,
                                'is_declined' => 1,
                                'is_approved' => 0,
                                'approved_on' => time(),
                                'decline_reason' => $decline_reason,
                                'declined_on' => time(),
                            );
                            if($this->ci->withdrawals_m->update_withdrawal_approval_request($withdrawal_approval_request->id,$input)){
                                if($this->toggle_withdrawal_request_status($withdrawal_request_id,$group,$group_currency,$priority_decline)){
                                    return TRUE;
                                }else{
                                   $this->ci->session->set_flashdata('error','Could not toggle withdrawal request status');
                                    return FALSE; 
                                }
                            }else{
                                $this->ci->session->set_flashdata('error','Could not update withdrawal approval request');
                                return FALSE;
                            }
                        }
                    }else{
                        if($post->created_by == $user_id){ //user created request but is not a signatory
                            $input = array(
                                'status' => 1,
                                'is_declined' => 1,
                                'declined_on' => time(),
                                'modified_on' => time(),
                                'decline_reason' => 'Request cancelled by the member who requested',
                            );
                            if($this->ci->withdrawals_m->update_withdrawal_request($withdrawal_request_id,$input)){
                                return TRUE;
                            }else{
                                $this->ci->session->set_flashdata('error','Could cancel withdrawal request');
                                return FALSE;
                            }
                        }else{
                            $this->ci->session->set_flashdata('error','Could not find withdrawal approval request');
                            return FALSE;
                        }
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Could not find group withdrawal request');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Give reasons why you are declining the withdrawal request');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Missing Values');
            return FALSE;
        }
    }

    function approve_withdrawal_request($user_id =0,$group=array(),$withdrawal_request_id=0,$member_id=0,$group_currency=''){
        if($group&&$withdrawal_request_id&&$member_id&&$user_id){
            $post = $this->ci->withdrawals_m->get_group_withdrawal_request($withdrawal_request_id,$group->id);
            if($post){
                if($withdrawal_approval_request=$this->ci->withdrawals_m->get_group_member_withdrawal_approval_request_by_member_id($post->id,$member_id)){
                    if($withdrawal_approval_request->status){
                        $this->ci->session->set_flashdata('error','You have already responded to this withdrawal request');
                        return FALSE;
                    }else{
                        $input = array(
                            'modified_on' => time(),
                            'modified_by' => $user_id,
                            'status' => 1,
                            'is_approved' => 1,
                            'approved_on' => time(),
                        );
                        if($this->ci->withdrawals_m->update_withdrawal_approval_request($withdrawal_approval_request->id,$input)){
                            if($this->toggle_withdrawal_request_status($withdrawal_approval_request->withdrawal_request_id,$group,$group_currency)){
                                return TRUE;
                            }else{
                                $this->ci->session->set_flashdata('error',$this->ci->session->flashdata('error'));
                                return FALSE;
                            }
                        }else{
                            $this->ci->session->set_flashdata('error','Could not update withdrawal approval request');
                            return FALSE;
                        }
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Could not find withdrawal approval request');
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Could not find group withdrawal request');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Missing Values');
            return FALSE;
        }
    }

    function toggle_withdrawal_request_status($withdrawal_request_id = 0,$group=array(),$group_currency='',$priority_cancel=FALSE){
        // Kindly consult before patching this function.. this is real money, again kindly. Thank you
        if($withdrawal_request_id&&$group&&$group_currency){
            $withdrawal_request = $this->ci->withdrawals_m->get_group_withdrawal_request($withdrawal_request_id);
            if($withdrawal_request){
                $withdrawal_approval_requests = $this->ci->withdrawals_m->get_group_withdrawal_request_approval_requests($withdrawal_request->id);
                if($withdrawal_approval_requests){
                    if(count((array_keys(array_combine(array_keys(json_decode(json_encode($withdrawal_approval_requests), true)), array_column(json_decode(json_encode($withdrawal_approval_requests), true), 'is_approved')),1))) == count($withdrawal_approval_requests)){ //approved by all signatories
                        $input = array(
                            'is_approved'=>1,
                            'status' => 1,
                            'is_declined'=>0,
                            'modified_by'=>$this->ci->user->id,
                            'modified_on'=>time()
                        );
                        if($this->ci->withdrawals_m->update_withdrawal_request($withdrawal_request_id,$input)){
                            return TRUE;
                        }else{
                            return FALSE;
                        }
                    }elseif(count((array_keys(array_combine(array_keys(json_decode(json_encode($withdrawal_approval_requests), true)), array_column(json_decode(json_encode($withdrawal_approval_requests), true), 'is_declined')),1)))){
                        $key = array_search(1, array_column(json_decode(json_encode($withdrawal_approval_requests), true), 'is_declined'));
                        $input = array(
                            'is_approved'=>0,
                            'is_declined'=>1,
                            'status' => 1,
                            'declined_by'=>$withdrawal_approval_requests[$key]->modified_by,
                            'decline_reason'=>$withdrawal_approval_requests[$key]->decline_reason,
                            'modified_by'=>$this->ci->user->id,
                            'modified_on'=>time()
                        );
                        $signatories = $this->ci->bank_accounts_m->get_group_active_account_signatories($withdrawal_request->bank_account_id,$group->id);
                        if($this->ci->withdrawals_m->update_withdrawal_request($withdrawal_request_id,$input)){
                            $this->ci->messaging->send_withdrawal_request_decline_sms_notification($group,$group_currency,$withdrawal_request,$this->withdrawal_request_transaction_names,$priority_cancel,$signatories);
                            return TRUE;
                        }else{
                            return FALSE;
                        }
                    }else{
                        $input = array(
                            'is_approved'=>0,
                            'is_declined'=>0,
                            'modified_by'=>$this->ci->user->id,
                            'modified_on'=>time()
                        );
                        if($this->ci->withdrawals_m->update_withdrawal_request($withdrawal_request_id,$input)){
                            return TRUE;
                        }else{
                            return FALSE;
                        }
                    }
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    function process_bulk_withdrawal_disbursement_requests($limit=20){
        $requests = $this->ci->withdrawals_m->get_undisbursed_approved_withdrawal_requests($limit);
        
       //print_r($requests); die;
        $i = 0;
        foreach ($requests as $key => $request) {
            ++$i;
            $this->process_withdrawal_request_disbursement_riskTick($request);
        }
        return $i;
    }

    function process_withdrawal_request_disbursement_riskTick($request = array()){
         
        if($request){
                // file_put_contents("logs/ongoing_withdrawal_disbursement.dat",' Time: '.time().' Request Data '.json_encode($request)."\n",FILE_APPEND);
                $reference_number = $this->get_withdrawal_request_reference_number();
                $channel = 1;
                $full_name = 'Name Not Provided';
                $phone = '';
                $bank_code = '';
                $bank_name = '';
                $bank_account_number = '';
                $destination = '';
                $bank_account_name ='';
                $valid_recipient = TRUE;

                if(preg_match('/bank-/', $request->recipient_id)){
                    $recipient_id = str_replace('bank-', '', $request->recipient_id);
                    $recipient = $this->ci->recipients_m->get($recipient_id);
                    if($recipient){
                    }else{
                        $valid_recipient = FALSE;
                    } 
                }else if(preg_match('/mobile-/', $request->recipient_id)){
                     
                    $recipient_id = str_replace('mobile-', '', $request->recipient_id);
                    $recipient = $this->ci->recipients_m->get($recipient_id);
                    if($recipient){
                    }else{
                        $valid_recipient = FALSE;
                    }
                }else if(preg_match('/member-/', $request->recipient_id)){
                    
                    $member_id = str_replace('member-', '', $request->recipient_id);
                    $member = $this->ci->members_m->get_group_member($member_id,$request->group_id);
                    if($member){
                        $recipient = new StdClass;
                        $recipient->name = $member->first_name.' '.$member->last_name;
                        $recipient->phone_number = $member->phone;
                        $recipient->account_name = '';
                        $recipient->account_number = '';
                    }else{
                        $valid_recipient = FALSE;
                    }
                }else{                    
                    $recipient = $this->ci->recipients_m->get($request->recipient_id); // get the recipient.
                    if($recipient){
                    }else{
                        $valid_recipient = FALSE;
                    }                    
                }

                if($valid_recipient){
                    $full_name = $recipient->name;
                    // channel.
                    if($request->transfer_to == 1){ // mobile
                        $phone = $recipient->phone_number;
                        $channel = 1;
                    }else if($request->transfer_to == 3){ // bank.
                        $channel = 4;
                        $bank = $this->ci->banks_m->get($recipient->bank_id);
                        $bank_name = $bank->name;
                        $bank_account_number = $recipient->account_number;
                        $bank_account_name = $recipient->account_name;
                        $bank_code = $bank->bank_code;
                    }else{ // via paybill
                        $channel = 2;
                    }
                    // $account_password = $account->account_password;
                    
                    $remarks= 'Withdrawal for '.$this->withdrawal_request_transaction_names[$request->withdrawal_for];
                    $request_callback_url=site_url('transaction_alerts/reconcile_online_banking_withdrawal');
                     
                    // $jsondata = json_encode(array(
                    //     'request_id' => time(),
                    //     'data' => array(
                    //         'transaction' => array(
                    //             'reference_number' => $reference_number,
                    //             'amount' => $request->amount,
                    //             'remarks' => 'Withdrawal for '.$this->withdrawal_request_transaction_names[$request->withdrawal_for],
                    //             'channel' => $channel,
                    //             'account_number' => $account->account_number,
                    //             'security_pass' => openssl_key_encrypt($account_password),
                    //             'source' => '',
                    //             'destination' => $destination,
                    //         ),
                    //         'recipient' => array(
                    //             'full_name' => $full_name,
                    //             'phone_number' => $phone,
                    //             'bank_code' => $bank_code,
                    //             'bank_name' => $bank_name,
                    //             'bank_account_number' => $bank_account_number,
                    //             'bank_account_name' => $bank_account_name,
                    //         ),
                    //         'request_callback_url' => site_url('transaction_alerts/reconcile_online_banking_withdrawal'),
                    //     ),
                    // ));
                
                    $response = $this->ci->process_transactions->disburse_funds($request->amount,$phone,$account=array(),$reference_number,$full_name,$remarks,$channel=1,$request_callback_url,$disburse_charge=0,$currency='KES');           
                    if($response){
                        if($response_data = json_decode($response)){
                            
                            if($response_data->code == 200){
                                $update = array(
                                    'reference_number' => $reference_number,
                                    'disbursement_status' => 1,
                                    'modified_on' => time(),
                                );
                                $this->ci->withdrawals_m->update_withdrawal_request($request->id,$update);
                            }else{
                                if($response_data->code == 'API070'){

                                }else{
                                    $update = array(
                                        'reference_number' => $reference_number,
                                        'disbursement_status' => 2,
                                        'status' => 2,
                                        'is_disbursement_declined' => 1,
                                        'disbursement_failed_error_message' =>$response_data->code.' : '.$response_data->description,
                                        'modified_on' => time(),
                                    );
                                    $this->ci->withdrawals_m->update_withdrawal_request($request->id,$update);
                                }
                            }
                        }else{
                            
                        }
                        $this->_send_developer_alert_email($response);
                    }else{
                        echo 'Failed response';
                    }
                }else{
                    echo "Invalid recipient";
                }   
            
        }else{

        }
    }
    function reconcile_account_disbursement($request = array(),$channel = 1){
		if($request){
			if($request->request_reconcilled !=1){
				if($channel == 1){
					$update = array(
						'result_code' => $request->callback_result_code,
						'result_description' => $request->callback_result_description,
						'transaction_id' => $request->transaction_id,
						'status' => ($request->callback_result_code =='0')?4:3,
						'transaction_date' => $request->transaction_completed_time?:time(),
					);
					$payment_request = $this->ci->transactions_m->get_payment_transaction_by_originator_conversation_id($request->originator_conversation_id);
					if($payment_request){
						$this->ci->transactions_m->update_payment($payment_request->id,$update);
					}
					if($request->callback_result_code=='0'){
						$transaction_id = $request->transaction_receipt;
						if($this->ci->withdrawals_m->is_unique_withdrawal($transaction_id)){
							$amount = currency($request->transaction_amount);
							$account_id = $request->account_id;
							$withdrawal = array(
								'withdrawal_date' => $request->transaction_completed_time,
								'account_id' => $account_id,
								'withdrawal_method' => 1,
								'description' => $request->receiver_party_public_name.' Receipt number: '.$request->transaction_receipt,
								'amount' => $amount,
								'transaction_id' => $transaction_id,
								'safaricom_b2c_id' => $request->id,
								'active' => 1,
								'created_by' => 1,
								'created_on' => time(),
							);
							if($withdrawal_id = $this->ci->withdrawals_m->insert($withdrawal)){
								$balance = $this->calculate_account_balance($account_id,3,$amount);
								$transaction_data = array(
									'transaction_type' => 3,
									'transaction_particulars' => $request->receiver_party_public_name.' Receipt number: '.$request->transaction_receipt,
									'transaction_id' => $request->transaction_receipt,
									'account_id' => $account_id,
									'amount' => $amount,
									'transaction_date' => $request->transaction_completed_time,
									'transaction_channel' => 3,
									'withdrawal_id' => $withdrawal_id,
									'balance' => $balance,
									'active' => 1,
									'created_on' => time(),
								);
								if($transaction_statement_id = $this->ci->transactions_m->insert($transaction_data)){
									if($this->update_account_balances($account_id,$balance)){
										$update = array(
											'request_reconcilled' => 1,
											'modified_on' => time(),
										);
										$this->ci->safaricom_m->update_b2c_request($request->id,$update);
										if($request->disburse_charge){
											if($this->_reconcile_withdrawal_charges($request)){
												if($account_number=$this->ci->accounts_m->get_account_number($request->account_id)){
													clear_transaction($account_number);
												}
												return TRUE;
											}else{
												return FALSE;
											}
										}else{
											if($account_number=$this->ci->accounts_m->get_account_number($request->account_id)){
												clear_transaction($account_number);
											}
											return TRUE;
										}
									}else{
										$this->ci->session->set_flashdata('error','Could not update B2C request reconcilled');
										return FALSE;
									}
								}else{
									$this->ci->session->set_flashdata('error','Could not create transaction statement');
									return FALSE;
								}
							}else{
								$this->ci->session->set_flashdata('error','Could not insert withdrawal');
								return FALSE;
							}
						}else{
							$this->ci->session->set_flashdata('error','Withdrawal not unique');
							return FALSE;
						}
					}else{
						$update = array(
							'request_reconcilled' => 1,
							'modified_on' => time(),
						);
						$this->ci->safaricom_m->update_b2c_request($request->id,$update);
						if($account_number=$this->ci->accounts_m->get_account_number($request->account_id)){
							clear_transaction($account_number);
						}
						return TRUE;
					}
				}elseif($channel==2){
					$update = array(
						'transaction_id' => $request->transaction_id,
						'transaction_date' => $request->transaction_completed_time?:time(),
						'amount' => $request->amount,
					);
					$payment_request = $this->ci->transactions_m->get_payment_transaction_by_transaction_id($request->transaction_id);
					if($payment_request){
						$this->ci->transactions_m->update_transaction_payment($payment_request->id,$update);
					}
					if($request->result_code=='0'){
						$transaction_id = $request->transaction_id;
						if($this->ci->withdrawals_m->is_unique_withdrawal($transaction_id)){
							$amount = currency($request->amount);
							$request->account_id = $request->account_id?:1;
							$account_id = $request->account_id;
							$withdrawal = array(
								'withdrawal_date' => $request->transaction_completed_time,
								'account_id' => $account_id,
								'withdrawal_method' => 1,
								'description' => $request->receiver_party_public_name.' Receipt number: '.$transaction_id,
								'amount' => $amount,
								'transaction_id' => $transaction_id,
								'safaricom_b2c_id' => $request->id,
								'active' => 1,
								'created_by' => 1,
								'created_on' => time(),
							);
							$withdrawal_id = $this->ci->withdrawals_m->insert($withdrawal);
							if($withdrawal_id){
								$balance = $this->calculate_account_balance($account_id,3,$amount);
								$transaction_data = array(
									'transaction_type' => 3,
									'transaction_particulars' => $request->receiver_party_public_name.' Receipt number: '.$transaction_id,
									'transaction_id' => $transaction_id,
									'account_id' => $account_id,
									'amount' => $amount,
									'transaction_date' => $request->transaction_completed_time,
									'transaction_channel' => 3,
									'withdrawal_id' => $withdrawal_id,
									'balance' => $balance,
									'active' => 1,
									'created_on' => time(),
								);
								$transaction_statement_id = $this->ci->transactions_m->insert($transaction_data);
								if($transaction_statement_id){
									if($this->update_account_balances($account_id,$balance)){
										$update = array(
											'request_reconcilled' => 1,
											'account_id' => $account_id,
											'modified_on' => time(),
										);
										$this->ci->safaricom_m->update_b2b_transactions($request->id,$update);
										if($request->disburse_charge){
											if($this->_reconcile_withdrawal_charges($request)){
												if($account_number=$this->ci->accounts_m->get_account_number($request->account_id)){
													clear_transaction($account_number);
												}
												return TRUE;
											}else{
												return FALSE;
											}
										}else{
											if($account_number=$this->ci->accounts_m->get_account_number($request->account_id)){
												clear_transaction($account_number);
											}
											return TRUE;
										}
									}else{
										$this->ci->session->set_flashdata('error','Could not update B2C request reconcilled');
										return FALSE;
									}
								}else{
									$this->ci->session->set_flashdata('error','Could not create transaction statement');
									return FALSE;
								}
							}else{
								$this->ci->session->set_flashdata('error','Could not insert withdrawal');
								return FALSE;
							}
						}else{
							$this->ci->session->set_flashdata('error','Withdrawal not unique');
							return FALSE;
						}
					}else{
						$update = array(
							'request_reconcilled' => 1,
							'modified_on' => time(),
						);
						$this->ci->safaricom_m->update_b2b_transactions($request->id,$update);
						if($account_number=$this->ci->accounts_m->get_account_number($request->account_id)){
							clear_transaction($account_number);
						}
						return TRUE;
					}
					$account_number=$this->ci->accounts_m->get_account_number($request->account_id);
					clear_transaction($account_number);
				}elseif ($channel == 3) {
					$update = array(
						'result_code' => $request->callback_result_code,
						'result_description' => $request->callback_result_description,
						'transaction_id' => $request->transaction_id,
						'status' => ($request->callback_result_code =='0')?4:3,
						'transaction_date' => $request->transaction_completed_time?:time(),
					);
					$payment_request = $this->ci->transactions_m->get_payment_transaction_by_originator_conversation_id($request->originator_conversation_id);
					if($payment_request){
						$this->ci->transactions_m->update_payment($payment_request->id,$update);
					}
					if($request->callback_result_code=='0'){
						$transaction_id = $request->transaction_id;
						if($this->ci->withdrawals_m->is_unique_withdrawal($transaction_id)){
							$amount = currency($request->transaction_amount);
							$account_id = $request->account_id;
							$withdrawal = array(
								'withdrawal_date' => $request->transaction_completed_time,
								'account_id' => $account_id,
								'withdrawal_method' => 1,
								'description' => "MTN Disbursement",
								'amount' => $amount,
								'transaction_id' => $transaction_id,
								'mtn_disbursement_id' => $request->id,
								'active' => 1,
								'created_by' => 1,
								'created_on' => time(),
							);
							if($withdrawal_id = $this->ci->withdrawals_m->insert($withdrawal)){
								$balance = $this->calculate_account_balance($account_id,3,$amount);
								$transaction_data = array(
									'transaction_type' => 3,
									'transaction_particulars' => 'MTN Withdrawal Receipt number: '.$request->transaction_id,
									'transaction_id' => $request->transaction_id,
									'account_id' => $account_id,
									'amount' => $amount,
									'transaction_date' => $request->transaction_completed_time,
									'transaction_channel' => 3,
									'withdrawal_id' => $withdrawal_id,
									'balance' => $balance,
									'active' => 1,
									'created_on' => time(),
								);
								if($transaction_statement_id = $this->ci->transactions_m->insert($transaction_data)){
									if($this->update_account_balances($account_id,$balance)){
										$update = array(
											'request_reconcilled' => 1,
											'modified_on' => time(),
										);
										$this->ci->mtn_m->update_disbursement_request($request->id,$update);
										if($request->disburse_charge){
											if($this->_reconcile_withdrawal_charges($request,$channel)){
												if($account_number=$this->ci->accounts_m->get_account_number($request->account_id)){
													clear_transaction($account_number);
												}
												return TRUE;
											}else{
												return FALSE;
											}
										}else{
											if($account_number=$this->ci->accounts_m->get_account_number($request->account_id)){
												clear_transaction($account_number);
											}
											return TRUE;
										}
									}else{
										$this->ci->session->set_flashdata('error','Could not update B2C request reconcilled');
										return FALSE;
									}
								}else{
									$this->ci->session->set_flashdata('error','Could not create transaction statement');
									return FALSE;
								}
							}else{
								$this->ci->session->set_flashdata('error','Could not insert withdrawal');
								return FALSE;
							}
						}else{
							$this->ci->session->set_flashdata('error','Withdrawal not unique');
							return FALSE;
						}
					}else{
						$update = array(
							'request_reconcilled' => 1,
							'modified_on' => time(),
						);
						$this->ci->mtn_m->update_disbursement_request($request->id,$update);
						if($account_number=$this->ci->accounts_m->get_account_number($request->account_id)){
							clear_transaction($account_number);
						}
						return TRUE;
					}
				}elseif($channel == 4){
					$update = array(
						'result_code' => $request->result_code,
						'result_description' => $request->result_description,
						'transaction_id' => $request->transaction_id,
						'status' => ($request->result_code =='0')?4:3,
						'transaction_date' => $request->transaction_completed_time?:time(),
					);
					$payment_request = $this->ci->transactions_m->get_payment_transaction_by_destination_reference_number($request->destination_reference_number);
					if($payment_request){
						$this->ci->transactions_m->update_payment($payment_request->id,$update);
					}
					if($request->result_code=='0'){
						$transaction_id = $request->transaction_id;
						if($this->ci->withdrawals_m->is_unique_withdrawal($transaction_id)){
							$amount = currency($request->amount);
							$account_id = $request->account_id;
							$withdrawal = array(
								'withdrawal_date' => $request->transaction_completed_time,
								'account_id' => $account_id,
								'withdrawal_method' => 1,
								'description' => $request->destination_naration.' Receipt number: '.$request->transaction_id,
								'amount' => $amount,
								'transaction_id' => $transaction_id,
								'coop_bank_funds_transfer_id' => $request->id,
								'active' => 1,
								'created_by' => 1,
								'created_on' => time(),
							);
							if($withdrawal_id = $this->ci->withdrawals_m->insert($withdrawal)){
								$balance = $this->calculate_account_balance($account_id,3,$amount);
								$transaction_data = array(
									'transaction_type' => 3,
									'transaction_particulars' => $request->destination_naration.' Receipt number: '.$request->transaction_id,
									'transaction_id' => $request->transaction_id,
									'account_id' => $account_id,
									'amount' => $amount,
									'transaction_date' => $request->transaction_completed_time,
									'transaction_channel' => 3,
									'withdrawal_id' => $withdrawal_id,
									'balance' => $balance,
									'active' => 1,
									'created_on' => time(),
								);
								if($transaction_statement_id = $this->ci->transactions_m->insert($transaction_data)){
									if($this->update_account_balances($account_id,$balance)){
										$update = array(
											'request_reconcilled' => 1,
											'modified_on' => time(),
										);
										$this->ci->coop_bank_m->update_disbursement($request->id,$update);
										if($request->disburse_charge){
											if($this->_reconcile_withdrawal_charges($request,4)){
												if($account_number=$this->ci->accounts_m->get_account_number($request->account_id)){
													clear_transaction($account_number);
												}
												return TRUE;
											}else{
												return FALSE;
											}
										}else{
											if($account_number=$this->ci->accounts_m->get_account_number($request->account_id)){
												clear_transaction($account_number);
											}
											return TRUE;
										}
									}else{
										$this->ci->session->set_flashdata('error','Could not update B2C request reconcilled');
										return FALSE;
									}
								}else{
									$this->ci->session->set_flashdata('error','Could not create transaction statement');
									return FALSE;
								}
							}else{
								$this->ci->session->set_flashdata('error','Could not insert withdrawal');
								return FALSE;
							}
						}else{
							$this->ci->session->set_flashdata('error','Withdrawal not unique');
							return FALSE;
						}
					}else{
						$update = array(
							'request_reconcilled' => 1,
							'modified_on' => time(),
						);
						$this->ci->coop_bank_m->update_disbursement($request->id,$update);
						if($account_number=$this->ci->accounts_m->get_account_number($request->account_id)){
							clear_transaction($account_number);
						}
						return TRUE;
					}
				}
			}else{
				$this->ci->session->set_flashdata('error','Request already reconcilled');
				return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Request not sent');
			return FALSE;
		}
	}

    function process_withdrawal_request_disbursement($request = array()){
        if($request){
            $valid_recipient = TRUE;
            $error_message = '';
            $reference_number = $this->get_withdrawal_request_reference_number()."";
            $bank_account = $this->ci->bank_accounts_m->get_group_bank_account($request->bank_account_id,$request->group_id);
            $bank = $this->ci->banks_m->get($bank_account->bank_id);
            $group = $this->ci->groups_m->get($request->group_id);
            $source_currency = $this->ci->countries_m->get_currency_code($bank_account->account_currency_id)?:$this->ci->countries_m->get_currency_code($bank->country_id);
            $source_country_code = $this->ci->countries_m->get_country_code($bank_account->account_currency_id)?:$this->ci->countries_m->get_country_code($bank->country_id);            
            if($bank_account){
                if(preg_match('/bank-/', $request->recipient_id)){
                    $recipient_id = str_replace('bank-', '', $request->recipient_id);
                    $recipient = $this->ci->recipients_m->get($recipient_id);
                    if($recipient){
                    }else{
                        $valid_recipient = FALSE;
                    } 
                }else if(preg_match('/mobile-/', $request->recipient_id)){
                    $recipient_id = str_replace('mobile-', '', $request->recipient_id);
                    $recipient = $this->ci->recipients_m->get($recipient_id);
                    if($recipient){
                    }else{
                        $valid_recipient = FALSE;
                    }
                }else if(preg_match('/member-/', $request->recipient_id)){
                    $member_id = str_replace('member-', '', $request->recipient_id);
                    $member = $this->ci->members_m->get_group_member($member_id,$request->group_id);
                    if($member){
                        $recipient = new StdClass;
                        $recipient->name = $member->first_name.' '.$member->last_name;
                        $recipient->phone_number = $member->phone;
                        $recipient->account_name = '';
                        $recipient->account_number = '';
                    }else{
                        $valid_recipient = FALSE;
                    }
                }else{                    
                    $recipient = $this->ci->recipients_m->get($request->recipient_id); // get the recipient.
                    if($recipient){
                    }else{
                        $valid_recipient = FALSE;
                    }                    
                }
                $narration = $this->withdrawal_request_transaction_names[$request->withdrawal_for];
                if($valid_recipient){
                    if($request->transfer_to == 1){//mobile
                        if($this->ci->curl->equityBankRequests->mobile_money_funds_transfer($reference_number,isset($member)?$member->phone:$recipient->phone_number,intval($request->amount),$bank_account->account_number,$source_country_code,$request)){
                            $update = array(
                                'reference_number' => $reference_number,
                                'status' => 3,
                                'is_disbursed' => 1,
                                'disbursed_on' => time(),
                                'modified_on' => time(),
                            );
                            $this->record_withdrawal_disbursement($request,$bank_account,$source_currency,$reference_number,$narration,$recipient);
                        }else{
                            $update = array(
                                'reference_number' => $reference_number,
                                'status' => 2,
                                'disbursement_failed_error_message' => $this->ci->session->flashdata('error'),
                                'is_disbursement_declined' => 1,
                                'modified_on' => time(),
                                'declined_on' => time(),
                            );
                        }
                    }else if($request->transfer_to == 3){//bank
                        //$source_currency="TZS";
                        if($this->ci->curl->equityBankRequests->funds_transfer($reference_number,$narration,$bank_account->account_number,intval($request->amount),$source_currency,$recipient->account_number,$recipient->account_currency,$request)){
                            $this->record_withdrawal_disbursement($request,$bank_account,$source_currency,$reference_number,$narration,$recipient);
                            $update = array(
                                'reference_number' => $reference_number,
                                'status' => 3,
                                'is_disbursed' => 1,
                                'disbursed_on' => time(),
                                'modified_on' => time(),
                            );
                        }else{
                            $update = array(
                                'reference_number' => $reference_number,
                                'status' => 2,
                                'is_disbursement_declined' => 1,
                                'disbursement_failed_error_message' => $this->ci->session->flashdata('error'),
                                'modified_on' => time(),
                                'declined_on' => time(),
                            );
                        }
                    }else{
                        $update = array(
                            'reference_number' => $reference_number,
                            'status' => 2,
                            'disbursement_failed_error_message' => 'Undefined transfer - to Channel',
                            'modified_on' => time(),
                            'is_disbursement_declined' => 1,
                            'declined_on' => time(),
                        );
                    }
                }else{
                    $this->_send_developer_alert_email("Request number ".$request->id." failed due to invalid recipient");
                }
            }else{
                $update = array(
                    'reference_number' => $reference_number,
                    'status' => 2,
                    'disbursement_failed_error_message' => 'There is no source account for the disbusement',
                    'modified_on' => time(),
                    'is_disbursement_declined' => 1,
                    'declined_on' => time(),
                );
            }
            $this->ci->withdrawals_m->update_withdrawal_request($request->id,$update);
        }else{

        }
    }

    function record_withdrawal_disbursement($request=array(),$bank_account=array(),$currency='',$reference_number='',$narration='',$recipient=''){
        if($request&&$bank_account&&$reference_number&&$recipient){
            if($this->ci->transaction_alerts_m->check_if_equity_bank_transaction_is_duplicate($reference_number)){
                return FALSE;
            }
            $transaction_date = time();
            $input = array(
                'tranCurrency'=>$currency,
                'tranDate'=> $transaction_date,
                'tranid'=>$reference_number,
                'tranAmount'=>intval($request->amount),
                'trandrcr'=>"D",
                'accid'=>$bank_account->account_number,
                'refNo'=>$reference_number,
                'tranType'=>"Funds Transfer",
                'tranParticular'=>"Funds Transfer from ".$bank_account->account_name." to ".$recipient->name,
                'tranRemarks'=>$narration,
                'ip_address'=>$_SERVER['REMOTE_ADDR']?:'',
                'created_on'=>$transaction_date,
            );
            if($equity_bank_transaction_alert_id = $this->ci->transaction_alerts_m->insert_equity_bank_transaction_alert($input)){
                $description = "<strong>Transaction ID:</strong>".$reference_number."<br/>
                                <strong>Transaction Transaction Type:</strong>Funds Transfer<br/>
                                <strong>Transaction Reference Number:</strong>".$reference_number."<br/>
                                <strong>Transaction Debit or Credit:</strong>D<br/>
                                <strong>Transaction Remarks:</strong>".$narration."<br/>
                                <strong>Transaction Particular:</strong>Funds Transfer from ".$bank_account->account_name." to ".$recipient->name;
                $input = array(
                    'equity_bank_transaction_alert_id'=>$equity_bank_transaction_alert_id,
                    'created_on'=>$transaction_date,
                    'transaction_id'=>$reference_number,
                    'type'=>2,
                    'account_number'=>$bank_account->account_number,
                    'amount'=>valid_currency($request->amount),
                    'transaction_date'=>$transaction_date,
                    'is_merged'=> 0,
                    'reconciled'=> 0,
                    'bank_id'=>$bank_account->bank_id,
                    'active'=>1,
                    'particulars'=>"Funds Transfer from ".$bank_account->account_name." to ".$recipient->name,
                    'ip_address'=>$_SERVER['REMOTE_ADDR']?:'',
                    'description'=>$description,
                    'group_members_notified'=>0,
                    'currency'=>$currency,
                );
                if($transaction_alert_id = $this->ci->transaction_alerts_m->insert($input)){
                    $this->reconcile_withdrawal_request($request,$transaction_alert_id,$reference_number,$transaction_date,$bank_account,$recipient);
                    $this->ci->messaging->notify_all_members_withdrawal_success($request,$currency);
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    function reconcile_withdrawal_request($request=array(),$transaction_alert_id=0,$transaction_id=0,$transaction_date=0,$bank_account=array(),$recipient=array()){
        //     1 => 'Loan Disbursement',
        //     2 => 'Expense Payment',
        //     3 => 'Dividend Payout',
        //     4 => 'Welfare',
        //     5 => 'Shares Refund',
        //     6 => 'Account Transfer',
        if($request&&$transaction_alert_id&&$transaction_date&&$bank_account&&$recipient){
            $group_id = $request->group_id;
            $amount = currency($request->amount);
            $description = "Funds Transfer to ".$recipient->name.'. '.$request->description.' : '.$transaction_id;
            $account_id = 'bank-'.$bank_account->id;
            if($request->withdrawal_for == 1){
                //loan disbursement logic
            }else if($request->withdrawal_for == 2){
                $expense_category_id = $request->expense_category_id;
                if($this->record_expense_withdrawal($group_id,$transaction_date,$expense_category_id,1,$account_id,$description,$amount,$transaction_alert_id)){
                    if($this->mark_transaction_alert_as_reconciled($transaction_alert_id,$group_id)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else if($request->withdrawal_for == 3){
                $member_id = $request->member_id;
                if($this->record_dividend_withdrawal($group_id,$transaction_date,$member_id,1,$account_id,$description,$amount,$transaction_alert_id)){
                    if($this->mark_transaction_alert_as_reconciled($transaction_alert_id,$group_id)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else if($request->withdrawal_for == 4){
                $member_id = $request->member_id;
                $member = $this->ci->members_m->get_group_member($member_id,$group_id);
                $name = 'Welfare';
                $slug = generate_slug($name);
                $description.='. Welfare to '.($member->first_name.' '.$member->last_name);
                $expense_category = $this->ci->expense_categories_m->get_by_slug($slug,'',$group_id);
                if($expense_category){
                    $expense_category_id = $expense_category->id;
                }else{
                    $input = array(
                        'name'  =>  $name,
                        'slug'  =>  $slug,
                        'is_an_administrative_expense_category'  =>  1,
                        'description'  =>  'Track member welfare',
                        'group_id'  =>  $group_id,
                        'active'    =>  1,
                        'created_on'    =>  time(),
                    );
                    $expense_category_id = $this->ci->expense_categories_m->insert($input);
                }
                if($this->record_expense_withdrawal($group_id,$transaction_date,$expense_category_id,1,$account_id,$description,$amount,$transaction_alert_id)){
                    if($this->mark_transaction_alert_as_reconciled($transaction_alert_id,$group_id)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else if($request->withdrawal_for == 5){
                $member_id = $request->member_id;
                $contribution_id = $request->contribution_id;
                if($this->record_contribution_refund($group_id,$transaction_date,$member_id,$account_id,
                    $contribution_id,1,'',$amount,1,$transaction_alert_id)){
                    if($this->mark_transaction_alert_as_reconciled($transaction_alert_id,$group_id)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{
                    return FALSE;
                }
            }else if($request->withdrawal_for == 6){
                if($this->record_account_transfer($group_id,$transaction_date,$account_id,$request->account_to_id,$amount,$description,$transaction_alert_id)) {
                    if($this->mark_transaction_alert_as_reconciled($transaction_alert_id,$group_id)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else {
                    return FALSE;
                }
            }else{
                $this->ci->session->set_flashdata('error','Could not reconcile unknown withdrawal for');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','Missing params');
            return FALSE;
        }
    }

    function process_loan_application_disbursement($application = array()){
        $this->ci->session->set_flashdata('error','');
        if($application){
            $account = $this->ci->wallets_m->get_wallet_account($application->group_id);
            if($account){
                $reference_number = $this->get_loan_disbursement_reference_number();
                $channel = '';
                $full_name = 'Name Not Provided';
                $phone = '';
                $bank_code = '';
                $bank_name = '';
                $bank_account_number = '';
                $destination = '';
                $member_id = str_replace('member-', '', $application->member_id);
                $member = $this->ci->members_m->get_group_member($application->member_id,$application->group_id);
                if($member){
                    $full_name = $member->first_name.' '.$member->last_name;
                    $channel = 1;
                    $phone = $member->phone;
                }else{
                    $this->ci->session->set_flashdata('error','Member not found');
                    return FALSE;
                }
               
                $account_password = $account->account_password;
                $jsondata = json_encode(array(
                    'request_id' => time(),
                    'data' => array(
                        'transaction' => array(
                            'reference_number' => $reference_number,
                            'amount' => currency($application->loan_amount),
                            'remarks' => 'Loan disbursement for '.$member->first_name.' '.$member->last_name,
                            'channel' => $channel,
                            'account_number' => $account->account_number,
                            'security_pass' => openssl_key_encrypt($account_password),
                            'source' => '',
                            'destination' => $destination,
                        ),
                        'recipient' => array(
                            'full_name' => $full_name,
                            'phone_number' => $phone,
                            'bank_code' => $bank_code,
                            'bank_name' => $bank_name,
                            'bank_account_number' => $bank_account_number,
                        ),
                        'request_callback_url' => site_url('transaction_alerts/reconcile_online_banking_loan_application'),
                    ),
                ));
                $url = "https://api.chamasoft.com:443/api/transactions/disburse_funds";
                if($response = $this->ci->curl->post_json_payment($jsondata,$url)){
                    if($response_data = json_decode($response)){
                        if($response_data->code == 200){
                            $update = array(
                                'reference_number' => $reference_number,
                                'status' => 3,
                                'modified_on' => time(),
                            );
                            $this->ci->loan_applications_m->update($application->id,$update);
                            $this->_send_developer_alert_email($response_data->description);
                            return TRUE;
                        }else{
                            $update = array(
                                'reference_number' => $reference_number,
                                'status' => 2,
                                'disbursement_fail_reason' =>$response_data->description,
                                'disbursement_failed_error_message' =>$response_data->code.' : '.$response_data->description,
                                'modified_on' => time(),
                            );
                            $this->ci->loan_applications_m->update($application->id,$update);
                            $this->_send_developer_alert_email($response);
                            return FALSE;
                        }
                    }else{
                        $this->_send_developer_alert_email($response);
                        $this->ci->session->set_flashdata('error','Failed response');
                        return FALSE;
                    }
                }else{
                    $this->ci->session->set_flashdata('error','Failed response');
                    return FALSE;
                }
            }else{//declined. No default bank account to disburse from
                $this->ci->session->set_flashdata('error','Wallet not found');
                return FALSE;
            }
        }else{
            $this->ci->session->set_flashdata('error','loan application not found');
            return FALSE;
        }
    }

    function process_disbursed_withdrawals(){
        $disbursements = $this->ci->withdrawals_m->get_unreconcilled_disbursed_withdrawals();
        if($disbursements){
            foreach ($disbursements as $key => $disbursement) {
                $group_currency = $this->ci->groups_m->get_this_group_currency($disbursement->group_id); 
                $bank_account = $this->ci->bank_accounts_m->get_group_default_bank_account($disbursement->group_id);
                $withdrawal_request = array(
                    'currency' => $group_currency,
                    'transaction_date' => $disbursement->disbursed_on,
                    'transaction_id' => $disbursement->disbursement_receipt_number,
                    'amount' => $disbursement->amount,
                    'transaction_type' => 'D',
                    'account_number' => $bank_account->account_number,
                    'bank_id' => $bank_account->bank_id,
                    'reference_number' => $disbursement->disbursement_receipt_number,
                    'transaction_particular' => $disbursement->disbursement_description,
                );
                $url = site_url('transaction_alerts/nic_bank');
                if($json_file = $this->ci->curl->post_json(json_encode($withdrawal_request),$url)){
                    if($response = json_decode($json_file)){
                        if($response->status==1){
                            $transaction_alert_id = $response->transaction_alert_id;
                            $update = array(
                                'transaction_alert_id' => $transaction_alert_id,
                                'modified_on' => time(),
                            );
                            if($this->ci->withdrawals_m->update_withdrawal_request($disbursement->id,$update)){
                                if($this->auto_reconcile_withdrawal_request($disbursement->id,$bank_account)){
                                    if($this->notify_bank_withdrawal_disbursement($disbursement->id,$bank_account)){
                                        echo 'Everything Okay';
                                    }else{
                                        echo $this->ci->session->set_flashdata('error');
                                    }
                                }else{
                                    echo $this->ci->session->set_flashdata('error');
                                }
                                echo 'created_transaction alert';
                            }else{
                                echo 'FAILED';
                            }
                        }else{
                            echo $response->message;
                        }
                    }else{
                        print_r($json_file);
                        echo 'Invalid json file';
                    }
                }else{
                    echo 'No response from transaction alert';
                }
            }
        }else{
            echo 'No disbursements';
        }
    }


    function get_withdrawal_request_reference_number(){
        $reference_number = time()+round(time()/10000);
        if($this->ci->withdrawals_m->withdrawal_request_reference_number_already_used($reference_number)){
            $this->get_withdrawal_request_reference_number();
        }else{
            $this->ci->withdrawals_m->insert_withdrawal_request_reference_number(array('reference_number'=>$reference_number,'created_on'=>time()));
            return $reference_number;
        }
    }

    function get_loan_disbursement_reference_number(){
        $reference_number = time()+round(time()/10000);
        if($this->ci->loan_applications_m->get_application_by_reference_number($reference_number)){
            $this->get_loan_disbursement_reference_number();
        }else{
            return $reference_number;
        }
    }

    function _send_developer_alert_email($file=''){
        if($file){
            $file = serialize($file);
             $headers = 'From: B2C Safaricom Files From '.$_SERVER['SERVER_NAME'].' <notifications@websacco.com>' . "\r\n" .
                    'Reply-To: info@tickconsulting.co.ke' . "\r\n".
                    'X-Mailer: PHP/' . phpversion();
            @mail('ongidigeofrey@gmail.com','Disbursement Response',$file,$headers);
            @mail('geofrey.ongidi@digitalvision.co.ke','Disbursement Response',$file,$headers);
            @mail('geofrey.ongidi@digitalvision.co.ke','Disbursement Response',$file,$headers);
        }
    }

    function update_group_contribution_fines_balances($group_id=0,$member_id=0){
        // $group_id = $group_id?:'2323';
        // if($member_id){
        //     $group_member_options = array($member_id=>'name');
        // }else{
        //     $group_member_options = $this->ci->members_m->get_all_member_options_only($group_id);
        // }
        
        // if($group_member_options){
        //     $this->group = $this->ci->groups_m->get($group_id);
        //     $total_contributions_paid_per_member_array = $this->ci->reports_m->get_group_total_contributions_paid_per_member_array($group_id,$group_member_options);
        //     $total_contribution_balances_per_member_array = $this->ci->reports_m->get_group_total_contribution_balances_per_member_array($group_id,$this->group->disable_ignore_contribution_transfers,$group_member_options);

        //     $group_total_fines_paid_per_member_array = $this->ci->reports_m->get_group_total_fines_paid_per_member_array('',$group_id,$group_member_options);
        //     $group_total_fines_balances_per_member_array = $this->ci->reports_m->get_group_total_fines_balances_per_member_array($group_id,$group_member_options);

        //     $member_ids = array();
        //     $contribution_total_payables = array();
        //     $contribution_total_paids = array();
        //     $contribution_total_balances = array();
        //     $fines_total_paids = array();
        //     $fines_total_balances = array();
        //     $fines_total_payables = array();
        //     $group_ids= array();
        //     $created_ons = array();
        //     foreach ($group_member_options as $id => $name) {
        //         $member_ids[] = $id;
        //         $contribution_total_paids[] = $total_contributions_paid_per_member_array[$id];
        //         $contribution_total_balances[] = $total_contribution_balances_per_member_array[$id];
        //         $contribution_total_payables[] = $total_contributions_paid_per_member_array[$id] + $total_contribution_balances_per_member_array[$id];
        //         $fines_total_paids[] = $group_total_fines_paid_per_member_array[$id];
        //         $fines_total_balances[] = $group_total_fines_balances_per_member_array[$id];
        //         $fines_total_payables[] = $group_total_fines_paid_per_member_array[$id] + $group_total_fines_balances_per_member_array[$id];

        //         // $total_group_member_contributions = $this->ci->deposits_m->get_group_member_total_contributions($id,$group_id);
        //         // $total_member_contribution_refunds = $this->ci->withdrawals_m->get_group_member_total_contribution_refunds($id,$group_id);
        //         // $total_member_contribution_transfers_to_loan = $this->ci->statements_m->get_group_member_total_contribution_transfers_to_loan($id,$group_id);
        //         // $total_member_contribution_transfers_from_loan_to_contribution = $this->ci->statements_m->get_group_member_total_contribution_transfers_from_loan_to_contribution($id,$group_id);
        //         // $total_group_member_contribution_arrears = $this->ci->statements_m->get_member_contribution_balance($group_id,$id);
        //         // $total_group_member_fine_arrears = $this->ci->statements_m->get_member_fine_balance($group_id,$id);
        //         // $total_member_contribution_transfers_from_contribution_to_fine_category = $this->ci->statements_m->get_group_member_total_contribution_transfers_from_contribution_to_fine_category($id,$group_id);
        //         // $total_group_member_total_fines = $this->ci->deposits_m->get_group_member_total_fines($id,$group_id);
        //         // $contribution_total_paids[] =  ($total_group_member_contributions-$total_member_contribution_transfers_from_contribution_to_fine_category-$total_member_contribution_refunds-$total_member_contribution_transfers_to_loan+$total_member_contribution_transfers_from_loan_to_contribution)?:0;
        //         // $contribution_total_balances[] = $this->ci->statements_m->get_member_contribution_balance($group_id,$id);
        //         // $contribution_total_payables[] = 0;
        //         // $fines_total_paids[] = ($total_group_member_total_fines+$total_member_contribution_transfers_from_contribution_to_fine_category)?:0;
        //         // $fines_total_balances[] = $this->ci->statements_m->get_member_fine_balance($group_id,$id);
        //         // $fines_total_payables[] = 0;

        //         $group_ids[] = $this->group->id;
        //         $created_ons[] = time();
        //     }
        //     if($member_ids){
        //         $input = array(
        //             'member_id' => $member_ids,
        //             'group_id' => $group_ids,
        //             'contributions_amount_payable' => $contribution_total_payables,
        //             'contributions_amount_paid' => $contribution_total_paids,
        //             'contributions_balance' => $contribution_total_balances,
        //             'fines_amount_payable' => $fines_total_payables,
        //             'fines_amount_paid' => $fines_total_paids,
        //             'fines_balance' => $fines_total_balances,
        //             'created_on' => $created_ons,
        //         );
        //         // print_r('<pre>');
        //         // print_r($input);die;
        //         // print_r('</pre>');
        //         if($this->ci->statements_m->delete_existing_contibutions_fines_balances($group_id,$member_id)){
        //             $this->ci->statements_m->insert_batch_member_contributions_fines_balances($input);
        //         }
        //     }
        // }
    }

    // function send_invoice_notifications($queued_contribution_invoices = array(),$group_ids = array(),$member_ids = array(),$contribution_ids = array(),$member_objects_array = array(),$contribution_objects_array = array(),$contribution_settings_objects_array = array()){

    //     if(empty($queued_contribution_invoices)||empty($group_ids)||empty($member_ids)||empty($contribution_ids)||empty($member_objects_array)||empty($contribution_objects_array)||empty($contribution_settings_objects_array))
    //     {
    //         return FALSE;
    //     }else{
    //         $cumulative_balance_array = $this->ci->statements_m->get_cumulative_balances_array($group_ids,$member_ids);
    //         $contribution_balance_array = $this->ci->statements_m->get_contribution_balances_array($group_ids,$member_ids,$contribution_ids);
    //         $group_objects_array = $this->ci->groups_m->get_group_objects_array($group_ids);
            
    //         $result = TRUE;
    //         foreach($queued_contribution_invoices as $queued_contribution_invoice):

    //             $group = $group_objects_array[$queued_contribution_invoice->group_id];
    //             $member = $member_objects_array[$queued_contribution_invoice->member_id];
    //             $contribution = $contribution_objects_array[$queued_contribution_invoice->contribution_id];
    //             $contribution_settings = $contribution_settings_objects_array[$queued_contribution_invoice->contribution_id];

    //             $contribution_balance = $contribution_balance_array[$queued_contribution_invoice->group_id][$queued_contribution_invoice->member_id][$queued_contribution_invoice->contribution_id];
    //             $cumulative_balance = $cumulative_balance_array[$queued_contribution_invoice->group_id][$queued_contribution_invoice->member_id];

    //             $group_currency = $this->currency_code_options[$group->currency_id];
    //             if($group){
    //                 if($member){
    //                     if($contribution){
    //                         if($contribution_settings){
    //                             $sms_data = array(
    //                                 'FIRST_NAME' => $member->first_name,
    //                                 'GROUP_CURRENCY' => $group_currency,
    //                                 'INVOICED_AMOUNT' => number_to_currency($queued_contribution_invoice->amount_payable),
    //                                 'DUE_DATE' => $queued_contribution_invoice->due_date,
    //                                 'INVOICE_DATE' => $queued_contribution_invoice->invoice_date,
    //                                 'CONTRIBUTION_NAME' => $contribution->name,
    //                                 'CONTRIBUTION_BALANCE' => number_to_currency($cumulative_balance),
    //                                 'TOTAL_OUTSTANDING_BALANCE'=>number_to_currency($cumulative_balance),
    //                                 'APPLICATION_NAME'=>$this->application_settings->application_name,
    //                             );
    //                             $email_data = array(
    //                                 'DATE' => date('d',$queued_contribution_invoice->invoice_date),
    //                                 'MONTH' => date('M',$queued_contribution_invoice->invoice_date),
    //                                 'DUE_DATE' => timestamp_to_date($queued_contribution_invoice->due_date),
    //                                 'HOW_TO_PAY' => '',
    //                                 'PROFILE_URL' => $this->application_settings->protocol.$group->slug.'.'.$this->application_settings->url.'/group/members/view/'.$member->id,
    //                                 'FIRST_NAME' => $member->first_name,
    //                                 'LAST_NAME' => $member->last_name,
    //                                 'GROUP_CURRENCY' => $this->currency_code_options[$group->currency_id],
    //                                 'GROUP_NAME' => $group->name,
    //                                 'AMOUNT_PAYABLE' => number_to_currency($queued_contribution_invoice->amount_payable), 
    //                                 'CONTRIBUTION_NAME' =>  $contribution->name,
    //                                 'CONTRIBUTION_BALANCE' => number_to_currency($cumulative_balance),
    //                                 'TOTAL_OUTSTANDING_BALANCE'=> number_to_currency($cumulative_balance),
    //                                 'APPLICATION_NAME'=>$this->application_settings->application_name,
    //                                 'LINK' => $this->application_settings->protocol.$group->slug.'.'.$this->application_settings->url.'/member/members/view/'.$member->id,
    //                                 'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
    //                                 'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
    //                                 'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
    //                             );
    //                             $send_sms_notification = isset($contribution_settings->sms_notifications_enabled)?1:0;
    //                             $send_email_notification = isset($contribution_settings->email_notifications_enabled)?1:0;
    //                             if($send_sms_notification){
    //                                 echo "Group ID: ".$group->id." | Contribution ID ".$contribution->id." | Member ID ".$member->id." SMS Reminder Enabled.<br/>";
    //                             }
    //                             if($send_email_notification){
    //                                 echo "Group ID: ".$group->id." | Contribution ID ".$contribution->id." | Member ID ".$member->id." Email Reminder Enabled.<br/>";
    //                             }
    //                             if($this->ci->messaging->send_contribution_invoice_notification_to_member($group->id,$member,$send_sms_notification,$send_email_notification,$contribution_settings->sms_template,$sms_data,$email_data,$queued_contribution_invoice->amount_payable)){
    //                                 if($this->ci->notifications->create(
    //                                     'A contribution invoice has been sent to you.',
    //                                     'You have been invoiced '.$group_currency.' '.number_to_currency($queued_contribution_invoice->amount_payable).' payable on '.timestamp_to_date($queued_contribution_invoice->invoice_date).' for your "'.$contribution->name.'" contribution - '.$group->name.'.',
    //                                     $this->ci->ion_auth->get_user($member->user_id),
    //                                     $member->id,
    //                                     $member->user_id,
    //                                     $member->id,
    //                                     $group->id,
    //                                     'View Invoice',
    //                                     'group/invoices/listing/',
    //                                     2,
    //                                     0
    //                                 )){
                                        
    //                                 }else{
    //                                     $result = FALSE;
    //                                 }
    //                             }else{
    //                                 $result = FALSE;
    //                             }
    //                         }else{
    //                             $result = FALSE;
    //                         }
    //                     }else{
    //                         $result = FALSE;
    //                     }
    //                 }else{
    //                     $result = FALSE;
    //                 }
    //             }else{
    //                 $result = FALSE;
    //             }
    //         endforeach;
    //         if($result){
    //             return TRUE;
    //         }else{
    //             return FALSE;
    //         }
    //     }
    // }
    // function send_fine_invoice_notifications($queued_contribution_fine_invoices = array(),$group_ids = array(),$member_ids = array(),$contribution_ids = array(),$member_objects_array = array(),$contribution_objects_array = array()){

    //     if(empty($queued_contribution_fine_invoices)||empty($group_ids)||empty($member_ids)||empty($contribution_ids)||empty($member_objects_array)||empty($contribution_objects_array))
    //     {
    //         return FALSE;
    //     }else{

    //         $cumulative_fine_balance_array = $this->ci->statements_m->get_cumulative_fine_balances_array($group_ids,$member_ids);

    //         $contribution_fine_balance_array = $this->ci->statements_m->get_contribution_fine_balances_array($group_ids,$member_ids,$contribution_ids);

    //         $group_objects_array = $this->ci->groups_m->get_group_objects_array($group_ids);

    //         $result = TRUE;
    //         foreach($queued_contribution_fine_invoices as $queued_contribution_fine_invoice):

    //             $group = $group_objects_array[$queued_contribution_fine_invoice->group_id];
    //             $member = $member_objects_array[$queued_contribution_fine_invoice->member_id];
    //             $contribution = $contribution_objects_array[$queued_contribution_fine_invoice->contribution_id];
    //             //$contribution_settings = $contribution_settings_objects_array[$queued_contribution_invoice->contribution_id];
    //             $contribution_fine_balance = $contribution_fine_balance_array[$queued_contribution_fine_invoice->group_id][$queued_contribution_fine_invoice->member_id][$queued_contribution_fine_invoice->contribution_id];
    //             $cumulative_fine_balance = $cumulative_fine_balance_array[$queued_contribution_fine_invoice->group_id][$queued_contribution_fine_invoice->member_id];
    //             $group_currency = $this->currency_code_options[$group->currency_id];
    //             $amount_payable = $queued_contribution_fine_invoice->amount_payable;
    //             $sms_data = array(
    //                 'FIRST_NAME' => $member->first_name,
    //                 'GROUP_CURRENCY' => $group_currency,
    //                 'AMOUNT' => number_to_currency($amount_payable),
    //                 'CONTRIBUTION_NAME' => $contribution->name,
    //                 'CONTRIBUTION_FINE_BALANCE' => number_to_currency($contribution_fine_balance_array[$queued_contribution_fine_invoice->group_id][$queued_contribution_fine_invoice->member_id][$queued_contribution_fine_invoice->contribution_id]),
    //                 'TOTAL_OUTSTANDING_BALANCE' => number_to_currency($cumulative_fine_balance_array[$queued_contribution_fine_invoice->group_id][$queued_contribution_fine_invoice->member_id]),
    //                 'APPLICATION_NAME'=>$this->application_settings->application_name,
    //                 'GROUP_NAME' => $group->name,
    //             );
    //             $email_data = array(
    //                 'DATE' => date('d',$queued_contribution_fine_invoice->fine_date),
    //                 'MONTH' => date('M',$queued_contribution_fine_invoice->fine_date),
    //                 'FIRST_NAME' => $member->first_name,
    //                 'LAST_NAME' => $member->last_name,
    //                 'GROUP_NAME' => $group->name,
    //                 'GROUP_CURRENCY' => $group_currency,
    //                 'AMOUNT' => number_to_currency($amount_payable),
    //                 'CONTRIBUTION_NAME' => $contribution->name,
    //                 'CONTRIBUTION_FINE_BALANCE' => number_to_currency($contribution_fine_balance_array[$queued_contribution_fine_invoice->group_id][$queued_contribution_fine_invoice->member_id][$queued_contribution_fine_invoice->contribution_id]),
    //                 'APPLICATION_NAME'=>$this->application_settings->application_name,
    //                 'TOTAL_OUTSTANDING_BALANCE' => number_to_currency($cumulative_fine_balance_array[$queued_contribution_fine_invoice->group_id][$queued_contribution_fine_invoice->member_id]),
    //                 'LINK' => $this->application_settings->protocol.$group->slug.'.'.$this->application_settings->url.'/member/members/view/'.$member->id,
    //                 'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
    //                 'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
    //                 'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
    //             );
    //             $send_sms_notification = isset($queued_contribution_fine_invoice->fine_sms_notifications_enabled)?1:0;
    //             $send_email_notification = isset($queued_contribution_fine_invoice->fine_email_notifications_enabled)?1:0;
    //             if($this->ci->messaging->send_contribution_fine_invoice_notification_to_member($group,$member,$send_sms_notification,$send_email_notification,$sms_data,$email_data,$amount_payable)){
    //                 if($this->ci->notifications->create(
    //                     'You have been fined for contribution late payment.',
    //                     'You have been fined '.$group_currency.' '.number_to_currency($amount_payable).' payable on '.timestamp_to_date($queued_contribution_fine_invoice->fine_date).' for late payment of "'.$contribution->name.'" contribution - '.$group->name.'.',
    //                     $this->ci->ion_auth->get_user($member->user_id),
    //                     $member->id,
    //                     $member->user_id,
    //                     $member->id,
    //                     $group->id,
    //                     'View Fine Invoice',
    //                     'group/invoices/listing/',
    //                     3,
    //                     0
    //                 )){
    //                     //return TRUE;
    //                 }else{
    //                     $result = FALSE;
    //                 }
    //             }else{
    //                 $result = FALSE;
    //             }
    //         endforeach;
    //         if($result){
    //             return TRUE;
    //         }else{
    //             return FALSE;
    //         }
    //     }
    // }


    function send_invoice_notifications($queued_contribution_invoices = array(),$group_ids = array(),$member_ids = array(),$contribution_ids = array(),$member_objects_array = array(),$contribution_objects_array = array(),$contribution_settings_objects_array = array()){

        $sms_template = '[GROUP_NAME]: Hi [FIRST_NAME], you have been invoiced [GROUP_CURRENCY] [INVOICED_AMOUNT], for your [CONTRIBUTION_NAME], new balance is [GROUP_CURRENCY] [CONTRIBUTION_BALANCE].';
        if(empty($queued_contribution_invoices)||empty($group_ids)||empty($member_ids)||empty($contribution_ids)||empty($member_objects_array)||empty($contribution_objects_array)||empty($contribution_settings_objects_array))
        {
            return FALSE;
        }else{
            $cumulative_balance_array = $this->ci->statements_m->get_cumulative_balances_array($group_ids,$member_ids);
            $contribution_balance_array = $this->ci->statements_m->get_contribution_balances_array($group_ids,$member_ids,$contribution_ids);
            $group_objects_array = $this->ci->groups_m->get_group_objects_array($group_ids);
            
            $result = TRUE;
            foreach($queued_contribution_invoices as $queued_contribution_invoice):

                $group = $group_objects_array[$queued_contribution_invoice->group_id];
                $member = $member_objects_array[$queued_contribution_invoice->member_id];
                $contribution = $contribution_objects_array[$queued_contribution_invoice->contribution_id];
                $contribution_settings = $contribution_settings_objects_array[$queued_contribution_invoice->contribution_id];

                $contribution_balance = $contribution_balance_array[$queued_contribution_invoice->group_id][$queued_contribution_invoice->member_id][$queued_contribution_invoice->contribution_id];
                $cumulative_balance = $cumulative_balance_array[$queued_contribution_invoice->group_id][$queued_contribution_invoice->member_id];
                $group_currency = $this->currency_code_options[$group->currency_id];
                if($group){
                    if($member){
                        if($contribution){
                            if($contribution_settings){
                                if($contribution_balance <= 0 || $cumulative_balance <= 0){

                                }else{
                                    $sms_data = array(
                                        'GROUP_NAME' => $group->name,
                                        'FIRST_NAME' => $member->first_name,
                                        'GROUP_CURRENCY' => $group_currency,
                                        'INVOICED_AMOUNT' => number_to_currency($queued_contribution_invoice->amount_payable),
                                        'DUE_DATE' => $queued_contribution_invoice->due_date,
                                        'INVOICE_DATE' => $queued_contribution_invoice->invoice_date,
                                        'CONTRIBUTION_NAME' => $contribution->name,
                                        'CONTRIBUTION_BALANCE' => number_to_currency($contribution_balance),
                                        'TOTAL_OUTSTANDING_BALANCE'=>number_to_currency($cumulative_balance),
                                        'APPLICATION_NAME'=>$this->application_settings->application_name,
                                    );
                                    $email_data = array(
                                        'APPLICATION_NAME'=>$this->application_settings->application_name,
                                        'GROUP_NAME' => $group->name,
                                        'FIRST_NAME' => $member->first_name,
                                        'LAST_NAME' => $member->last_name,
                                        'GROUP_CURRENCY' => $this->currency_code_options[$group->currency_id],
                                        'AMOUNT_PAYABLE' => number_to_currency($queued_contribution_invoice->amount_payable), 
                                        'CONTRIBUTION_NAME' =>  $contribution->name,
                                        'DUE_DATE' => timestamp_to_date($queued_contribution_invoice->due_date),
                                        'DATE' => date('d',$queued_contribution_invoice->invoice_date),
                                        'MONTH' => date('M',$queued_contribution_invoice->invoice_date),
                                        'YEAR' => date('Y',time()),
                                        'ARREARS' => number_to_currency($contribution_balance),
                                        'HOW_TO_PAY' => '',
                                        'TOTAL_ARREARS' => number_to_currency($cumulative_balance),
                                        'LINK' => site_url(),
                                        'LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
                                        // 'PROFILE_URL' => $this->application_settings->protocol.$this->application_settings->url.'/group/members/view/'.$member->id,
                                        // 'TOTAL_OUTSTANDING_BALANCE'=> number_to_currency($cumulative_balance),
                                        // 'LINK' => $this->application_settings->protocol.$this->application_settings->url.'/member/members/view/'.$member->id,
                                        // 'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
                                        // 'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
                                        
                                    );
                                    $send_sms_notification = isset($contribution_settings->sms_notifications_enabled)?1:0;
                                    $send_email_notification = isset($contribution_settings->email_notifications_enabled)?1:0;
                                    if($send_sms_notification){

                                        //echo "Group ID: ".$group->id." | Contribution ID ".$contribution->id." | Member ID ".$member->id." SMS Reminder Enabled.<br/>";
                                    }
                                    if($send_email_notification){

                                       // echo "Group ID: ".$group->id." | Contribution ID ".$contribution->id." | Member ID ".$member->id." Email Reminder Enabled.<br/>";
                                    }
                                    if($this->ci->messaging->send_contribution_invoice_notification_to_member($group->id,$member,$send_sms_notification,$send_email_notification,$sms_template,$sms_data,$email_data,$queued_contribution_invoice->amount_payable,$group)){
                                        // if($this->ci->member_notifications->create(
                                        //     'A contribution invoice has been sent to you.',
                                        //     'You have been invoiced '.$group_currency.' '.number_to_currency($queued_contribution_invoice->amount_payable).' payable on '.timestamp_to_date($queued_contribution_invoice->invoice_date).' for your "'.$contribution->name.'" contribution - '.$group->name.'.',
                                        //     $this->ci->ion_auth->get_user($member->user_id),
                                        //     $member->id,
                                        //     $member->user_id,
                                        //     $member->id,
                                        //     $group->id,
                                        //     'View Invoice',
                                        //     'group/invoices/listing/',
                                        //     2,
                                        //     0
                                        // )){
                                            
                                        // }else{
                                        //     $result = FALSE;
                                        // }
                                    }else{
                                        $result = FALSE;
                                    }

                                }

                            }else{
                                $result = FALSE;
                            }
                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                }else{
                    $result = FALSE;
                }
            endforeach;
            if($result){
                return TRUE;
            }else{
                return FALSE;
            }
        }
    }
    function send_fine_invoice_notifications($queued_contribution_fine_invoices = array(),$group_ids = array(),$member_ids = array(),$contribution_ids = array(),$member_objects_array = array(),$contribution_objects_array = array()){

        if(empty($queued_contribution_fine_invoices)||empty($group_ids)||empty($member_ids)||empty($contribution_ids)||empty($member_objects_array)||empty($contribution_objects_array))
        {
            return FALSE;
        }else{

            $cumulative_fine_balance_array = $this->ci->statements_m->get_cumulative_fine_balances_array($group_ids,$member_ids);

            $contribution_fine_balance_array = $this->ci->statements_m->get_contribution_fine_balances_array($group_ids,$member_ids,$contribution_ids);

            $group_objects_array = $this->ci->groups_m->get_group_objects_array($group_ids);

            $result = TRUE;
            foreach($queued_contribution_fine_invoices as $queued_contribution_fine_invoice):
                $group = $group_objects_array[$queued_contribution_fine_invoice->group_id];
                $member = $member_objects_array[$queued_contribution_fine_invoice->member_id];
                $contribution = $contribution_objects_array[$queued_contribution_fine_invoice->contribution_id];
                //$contribution_settings = $contribution_settings_objects_array[$queued_contribution_invoice->contribution_id];
                $contribution_fine_balance = $contribution_fine_balance_array[$queued_contribution_fine_invoice->group_id][$queued_contribution_fine_invoice->member_id][$queued_contribution_fine_invoice->contribution_id];
                $cumulative_fine_balance = $cumulative_fine_balance_array[$queued_contribution_fine_invoice->group_id][$queued_contribution_fine_invoice->member_id];
                $group_currency = $this->currency_code_options[$group->currency_id];
                $amount_payable = isset($queued_contribution_fine_invoice->amount_payable)?$queued_contribution_fine_invoice->amount_payable:(isset($queued_contribution_fine_invoice->amount)?$queued_contribution_fine_invoice->amount:0);
                $sms_data = array(
                    'FIRST_NAME' => $member->first_name,
                    'GROUP_CURRENCY' => $group_currency,
                    'AMOUNT' => number_to_currency($amount_payable),
                    'CONTRIBUTION_NAME' => $contribution->name,
                    'CONTRIBUTION_FINE_BALANCE' => number_to_currency($contribution_fine_balance_array[$queued_contribution_fine_invoice->group_id][$queued_contribution_fine_invoice->member_id][$queued_contribution_fine_invoice->contribution_id]),
                    'TOTAL_OUTSTANDING_BALANCE' => number_to_currency($cumulative_fine_balance_array[$queued_contribution_fine_invoice->group_id][$queued_contribution_fine_invoice->member_id]),
                    'APPLICATION_NAME'=>$this->application_settings->application_name,
                    'GROUP_NAME' => $group->name,
                );
                $email_data = array(
                    'DATE' => date('d',$queued_contribution_fine_invoice->fine_date),
                    'MONTH' => date('M',$queued_contribution_fine_invoice->fine_date),
                    'FIRST_NAME' => $member->first_name,
                    'LAST_NAME' => $member->last_name,
                    'GROUP_NAME' => $group->name,
                    'GROUP_CURRENCY' => $group_currency,
                    'AMOUNT' => number_to_currency($amount_payable),
                    'CONTRIBUTION_NAME' => $contribution->name,
                    'CONTRIBUTION_FINE_BALANCE' => number_to_currency($contribution_fine_balance_array[$queued_contribution_fine_invoice->group_id][$queued_contribution_fine_invoice->member_id][$queued_contribution_fine_invoice->contribution_id]),
                    'APPLICATION_NAME'=>$this->application_settings->application_name,
                    'TOTAL_OUTSTANDING_BALANCE' => number_to_currency($cumulative_fine_balance_array[$queued_contribution_fine_invoice->group_id][$queued_contribution_fine_invoice->member_id]),
                    'LINK' => $this->application_settings->protocol.$this->application_settings->url.'/member/members/view/'.$member->id,
                    'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
                    'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
                    'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
                );
                $send_sms_notification = isset($queued_contribution_fine_invoice->fine_sms_notifications_enabled)?1:0;
                $send_email_notification = isset($queued_contribution_fine_invoice->fine_email_notifications_enabled)?1:0;
                if($this->ci->messaging->send_contribution_fine_invoice_notification_to_member($group,$member,$send_sms_notification,$send_email_notification,$sms_data,$email_data,$amount_payable)){
                    // if($this->ci->member_notifications->create(
                    //     'You have been fined for contribution late payment.',
                    //     'You have been fined '.$group_currency.' '.number_to_currency($amount_payable).' payable on '.timestamp_to_date($queued_contribution_fine_invoice->fine_date).' for late payment of "'.$contribution->name.'" contribution - '.$group->name.'.',
                    //     $this->ci->ion_auth->get_user($member->user_id),
                    //     $member->id,
                    //     $member->user_id,
                    //     $member->id,
                    //     $group->id,
                    //     'View Fine Invoice',
                    //     'group/invoices/listing/',
                    //     3,
                    //     0
                    // )){
                    //     //return TRUE;
                    // }else{
                    //     $result = FALSE;
                    // }
                    return TRUE;
                }else{
                    $result = FALSE;
                }
            endforeach;
            if($result){
                return TRUE;
            }else{
                return FALSE;
            }
        }
    }

    public function fix_voided_transactions($id=0,$group_id = 0,$transaction_type = 0,$account_id = 0,$amount = 0){
        if($id&&$group_id&&$transaction_type&&$account_id&&$amount){
            $input = array(
                'active' => 0,
                'modified_on' => time()
            );
            if($result = $this->ci->transaction_statements_m->update($id,$input)){
                if(in_array($transaction_type, $this->deposit_transaction_types)){
                    if($this->_decrease_account_balance($group_id,$account_id,$transaction_type,$amount)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }elseif(in_array($transaction_type,$this->withdrawal_transaction_types)){
                    if($this->_increase_account_balance($group_id,$account_id,$transaction_type,$amount)){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }
            }else{
                return FALSE;
            }
        }
    }

    function fix_orphan_transaction_statements($transaction_statements = array()){

        $transaction_statements = $this->ci->transaction_statements_m->get_orphan_transaction_statements();
        foreach($transaction_statements as $transaction_statement):
            $input = array(
                'active' => 0,
                'modified_by' => 1,
                'modified_on' => time()
            );
            if($this->ci->transaction_statements_m->update($transaction_statement->id,$input)){
                if($this->_decrease_account_balance($transaction_statement->group_id,$transaction_statement->account_id,$transaction_statement->transaction_type,$transaction_statement->amount)){
                    echo "Transaction statement fixed. </br>";
                }else{
                    echo "Transaction statement could not fixed. </br>";
                }
            }else{
                echo "Could update transaction statement. <br/>";
            }
        endforeach;
        echo count($transaction_statements);
    }

}
