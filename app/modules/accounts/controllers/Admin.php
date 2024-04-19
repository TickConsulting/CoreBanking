<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller{

    protected $data = array();

    protected $validation_rules = array(
        array(
            'field' => 'notification_url',
            'label' => 'Notification URL',
            'rules' => 'trim|callback__valid_url'
        ),
        array(
            'field' => 'notification_url_username',
            'label' => 'Notification URL Username',
            'rules' => 'trim'
        ),
        array(
            'field' => 'notification_url_password',
            'label' => 'Notification URL Password',
            'rules' => 'trim'
        ),
        array(
            'field' => 'tariff_id',
            'label' => 'Tariff Id',
            'rules' => 'trim|required'
        ),
    );

    protected $account_transfer_rules = array(
        array(
            'field' => 'type',
            'label' => 'Transfer Type',
            'rules' => 'trim|required|numeric'
        ),
        array(
            'field' => 'account_from',
            'label' => 'Account From',
            'rules' => 'trim|numeric'
        ),
         array(
            'field' => 'account_to',
            'label' => 'Account To',
            'rules' => 'trim|required|numeric'
        ),
          array(
            'field' => 'amount',
            'label' => 'Amount',
            'rules' => 'trim|currency'
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'trim|required'
        ),
    );

	function __construct(){
        parent::__construct();
        $this->load->model('accounts_m');
        $this->load->model('countries/countries_m');
        $this->load->model('tariffs/tariffs_m');
        $this->load->model('transactions/transactions_m');
        $this->load->library('transactions');
        $this->load->library('pdfgenerator');
    }

    function _transfer_rules(){
        $type = $this->input->post('type');

        if($type == 1){
            $this->account_transfer_rules[] = array(
                'field' => 'account_from',
                'label' => 'Account From',
                'rules' => 'trim|required|numeric'
            );
            $this->account_transfer_rules[] = array(
                'field' => 'amount',
                'label' => 'Amount',
                'rules' => 'trim|required|currency'
            );
        }else if($type ==2){
            $this->account_transfer_rules[] = array(
                'field' => 'receipt_number',
                'label' => 'Receipt Number',
                'rules' => 'trim|required'
            );
        }
    }

    function _valid_url(){
        $notification_url = $this->input->post('notification_url');
        if($notification_url){
            if(filter_var($notification_url, FILTER_VALIDATE_URL)){
                return TRUE;
            }else{
                $this->form_validation->set_message('_valid_url','Enter a valid URL');
                return FALSE;
            }
        }else{
            return  TRUE;
        }
        
    }

    function index(){
    }

    function create(){
    }

    function listing(){
        $account_name = $this->input->get_post('account_name');
        $filter_params = array(
            'account_name' => filter_var($account_name,FILTER_SANITIZE_STRING),
            'account_number' => filter_var($account_name,FILTER_SANITIZE_STRING)
        );
        $currency_options = $this->countries_m->get_currency_options();
        $total_rows = $this->accounts_m->count_all($filter_params);
        $pagination = create_pagination('admin/accounts/listing/pages', $total_rows,100,5,TRUE);
        $posts = $this->accounts_m->limit($pagination['limit'])->get_all($filter_params);
        $this->data['account_signatories'] = $this->accounts_m->get_account_signatories_array($posts);
        //$this->data['signatories_names'] = $this->accounts_m->get_signatory_options();
        $this->data['posts'] = $posts;
        $this->data['pagination'] = $pagination;
        $this->data['currency_options'] = $currency_options;
        $this->template->title('Accounts')->build('admin/listing',$this->data);
    }

    function action(){
    }

    function edit($id = 0){
        $id OR redirect("admin/accounts/listing");
        $post = $this->accounts_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Account not found');
            redirect("admin/accounts/listing");
        }
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            if($id == 1){
                $balance = 3000;
            }
            else if($id==2){
                $balance = 0;
            }
            else if($id == 39){
                $balance = 37942;
            }elseif($id = 4226){
                $balance = 35775;
            }else{
                $balance = $post->balance;
            }
            $update = array(
                'notification_url' => $this->input->post('notification_url'),
                'notification_url_username' => $this->input->post('notification_url_username'),
                'notification_url_password' => $this->input->post('notification_url_password'),
                'currency_id' => $this->input->post('currency_id'),
                'account_name' => $this->input->post('account_name'),
                'tariff_id' => $this->input->post('tariff_id'),
                'modified_on' => time(),
                'balance' => $balance,
                'modified_by' => $this->user->id,
            );
            // print_r($update);die;
            if($this->accounts_m->update($id,$update)){
                $this->session->set_flashdata('success','Account URLs successfully updated');
            }else{
                $this->session->set_flashdata('error','Error occured why updating URL');
            }
            redirect("admin/accounts/listing");
        }else{
            foreach ($this->validation_rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value= $post->$field_value?:set_value($field_value);
            }
        }
        $this->data['post'] = $post;
        $this->data['id'] = $post->id;
        $this->data['currencies'] = $this->countries_m->get_currency_options();
        $this->data['tariff_options'] = $this->tariffs_m->get_options();
        $this->template->title('Edit Account URLs, Username and Passsword')->build('admin/form',$this->data);
    }

    function deactivate($id = 0,$redirect = TRUE){
    }

    function activate($id = 0,$redirect = TRUE){
    }

    function delete($id = 0,$redirect = TRUE){
        $id OR redirect('admin/accounts/listing');
        $post = $this->accounts_m->get($id);
        $post OR redirect('admin/accounts/listing');

        $statement_counts = $this->transactions_m->count_statement_entries($id);
        if($statement_counts){
            $this->session->set_flashdata('error','Could not delete account, account already with some transactions');
        }else{
            if($this->accounts_m->delete($post->id)){
                $this->session->set_flashdata('success','Account deleted successfully');
            }else{
                $this->session->set_flashdata('error','Error occured why deleting account');
            }
        }
        redirect('admin/accounts/listing');
    }

    function reversal($transaction_id=0,$type=0){
        $this->transactions->record_payment_transaction_reversal('','','',$transaction_id,$type);
    }

    function statement($id=0,$generate_pdf=FALSE){
        $id OR redirect('admin/accounts/listing');
        $account = $this->accounts_m->get($id);
        $account OR redirect('admin/accounts/listing');
        $from = strtotime($this->input->get_post('from'))?:strtotime("-3 months",time());
        $to =  strtotime($this->input->get_post('to'))?:strtotime("tomorrow");
        $statement = $this->transactions_m->get_account_statement($id,$from,$to);
        $this->data['from'] = $from;
        $this->data['to'] = time();
        $this->data['statements'] = $statement;
        $this->data['account'] = $account;
        $this->data['signatory'] = $this->accounts_m->get_account_signatory($account->id);
        $this->data['currency'] = $this->countries_m->get_currency($account->currency_id);
        $this->data['payment_transaction_types'] = $this->transactions_m->payment_transaction_types;
        $this->data['deposit_transaction_types'] = $this->transactions_m->deposit_transaction_types;
        $this->data['balance_brought_forward'] = $this->transactions_m->get_total_group_balances($id,$from);
        $this->data['id'] = $id;
        if($generate_pdf){
            $this->data['pdf_true'] = TRUE;
            $html = $this->load->view('admin/statement',$this->data,TRUE);
            $this->pdfgenerator->generate_portrait_report($html,$account->account_name);
        }else{
            $this->data['pdf_true'] = FALSE;
            $this->template->title('Account Statement')->build('admin/statement',$this->data);
        }
    }

    function record_transfer(){
        $post = new StdClass();
        $this->_transfer_rules();
        $this->form_validation->set_rules($this->account_transfer_rules);
        if($this->form_validation->run()){
            $receipt_number = $this->input->post('receipt_number');
            $account_from = $this->input->post('account_from');
            $account_to = $this->input->post('account_to');
            $amount = valid_currency($this->input->post('amount'));
            $description = $this->input->post('description');
            $type = $this->input->post('type');
            $account_to = $this->accounts_m->get($account_to);
            if($type == 1){
                $account_from = $this->accounts_m->get($account_from);
            }else{
                $transaction = $this->safaricom_m->get_c2b_payment_by_transaaction_id($receipt_number);
                if($transaction){
                    $account_from = $this->accounts_m->get_account_by_account_number($transaction->account);
                    if(!$account_from){
                        $request = $this->safaricom_m->get_stk_payment_by_transaaction_id($receipt_number);
                        if($request){
                            $account_from = $this->accounts_m->get($request->account_id);
                        }
                    }
                    $amount = $transaction->amount;
                }
            }
            if($account_from&&$account_to&&$amount){
                if($account_from->balance >= $amount){
                    if($this->transactions->record_account_transfer($account_from,$account_to,$amount,$description,$this->user,$type,$receipt_number)){
                        $this->session->set_flashdata('success','Account transfer recorded successfully');
                    }else{

                    }
                }else{
                    $this->session->set_flashdata('error','insufficient account balances');
                }
            }else{
                $this->session->set_flashdata('error','Could not find accounts entered');
            }
            redirect('admin/accounts/transfers');
        }else{
            foreach ($this->validation_rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $this->data['post'] = $post;
        $this->data['accounts'] = $this->accounts_m->get_options();
        $this->template->title('Record Account Transfer')->build('admin/record_transfer',$this->data);
    }

    function transfers(){
        $total_rows = $this->accounts_m->count_all_transfers();
        $pagination = create_pagination('admin/accounts/transfers/pages', $total_rows,100,5,TRUE);
        $posts = $this->accounts_m->limit($pagination['limit'])->get_all_transfers();
        $this->data['posts'] = $posts;
        $this->data['pagination'] = $pagination;
        $this->template->title('Accounts')->build('admin/transfers',$this->data);
    }

    function void($id=0){
        $id OR redirect('admin/accounts/transfers');
        if($this->transactions->void_account_transfer($id,$this->user)){
            $this->session->set_flashdata('success','Account transfer successfully voided');
        }else{

        }
        redirect('admin/accounts/transfers');
    }

    function get_account_summary_graph(){
        $add_one = TRUE;
        $format = 'M Y';
        $from = strtotime(" 1st ".date('M Y',strtotime("-24 months",time())));
        $to = time();
        $gl_account = intval($this->accounts_m->get_account_number_balances());
        $transaction_summarys = $this->transactions_m->get_transaction_statements($from,$to);
        $deposits = array();
        $withdrawals = array();
        $months = array();
        foreach ($transaction_summarys as $transaction_summary) {
            $date = date('Ym',$transaction_summary->transaction_date);
            if(in_array($transaction_summary->transaction_type, $this->transactions_m->deposit_transaction_types)){
                if(array_key_exists($date, $deposits)){
                    $deposits[$date]=$deposits[$date]+$transaction_summary->amount;
                }else{
                    $deposits[$date]=$transaction_summary->amount;
                }
            }else if(in_array($transaction_summary->transaction_type, $this->transactions_m->withdrawal_transaction_types)){
                if(array_key_exists($date, $withdrawals)){
                    $withdrawals[$date]=$withdrawals[$date]+$transaction_summary->amount;
                }else{
                    $withdrawals[$date]=$transaction_summary->amount;
                }
            }
            if(array_key_exists($date, $months)){

            }else{
                $months[$date] = date('M Y',strtotime($date.'01'));
            }
            
        }
        $today = strtotime(($add_one?'1 ':'').date($format,time()));
        $days = (strtotime(date('d-M-Y',time())) - $from)/(24*60*60)+1;
        $new_months = array();
        $bank_values= array();
        $cash_values= array();
        $months = array_reverse($months,TRUE);
        $deposits = array_reverse($deposits,TRUE);
        $withdrawals = array_reverse($withdrawals,TRUE);
        foreach ($months as $month => $name) {
           $new_months[] = $name;
           $bank_values[] = isset($deposits[$month])?$deposits[$month]:0;
           $cash_values[] = isset($withdrawals[$month])?$withdrawals[$month]:0;
        }

        $result = new StdClass();
        $result->months = $new_months;
        $result->bank_values = $bank_values;
        $result->cash_values = $cash_values;
        

        echo json_encode($result);
        
    }

    function get_per_month_transactions(){
        $from = strtotime(" 1st ".date('M Y',strtotime("-24 months",time())));
        $to = time();
        $transactions = $this->transactions_m->count_number_of_transactions_per_month($from,$to);

        print_r($transactions);
    }

    function update_account_balances($id = 0){
        $account = $this->accounts_m->get($id);
        $to = strtotime("+1 day");
        $from = strtotime("-2years");
        $statements = $this->transactions_m->get_account_statement($id);
        $balance = 0;
        $update = array();
        foreach($statements as $key=>$statement){
            $balance = $this->transactions->calculate_account_balance($id,$statement->transaction_type,$statement->amount,$statement->transaction_date,$balance,TRUE);
            $update[$statement->id] =array('balance'=>$balance);
            // $this->transactions_m->update($statement->id,$update);
            // $this->update_account_balances($id,$balance);
            // echo "Key:-> ".$key." amount ".number_to_currency($statement->amount)."  Balance: ".number_to_currency($balance)." <br/>";
        }

        foreach($update as $key=>$entry){
            $this->transactions_m->update($key,$entry);
        }
        $this->transactions->update_account_balances($id,$balance);
        $this->session->set_flashdata('success',"Account balances updated successfully");
        // redirect("admin/accounts/listing");
        redirect("admin/accounts/statement/".$id);
    }

}
