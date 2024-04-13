<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller{
    
    protected $equity_transaction_type_options = array(
        1=>'C',//Deposit
        2=>'D',//Withdrawal
    );

    protected $group_status_options = array(
        1 => "On trial",
        2 => "Paying",
        3 => "Suspended",
    );

    protected $equity_transaction_type_options_keys = array();
    protected $data = array();
    protected $forwarder_type_options = array(
        1 => "All Posts Forwarder",
        2 => "Bank Account Number Posts Forwarder",
    );

    function __construct(){
        parent::__construct();
        $this->load->model('transaction_alerts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('bank_accounts/bank_accounts_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->equity_transaction_type_options_keys = array_flip($this->equity_transaction_type_options);
    }

    function index(){
        $bank = $this->banks_m->get_default_bank();
        $paying_group_ids = $this->billing_m->get_paying_group_id_array();
        $group_id_options_account_number_as_key_array = $this->groups_m->get_group_options_account_number_as_key_group_id_as_value($paying_group_ids);
        $bank_accounts = $this->bank_accounts_m->get_verified_partner_bank_accounts_with_transaction_alerts($bank->id);
        $account_numbers = array();
        $account = new stdClass;
        foreach($bank_accounts as $bank_account):
            $account->account_number = $bank_account->account_number;
            $account_numbers[] = $account;
        endforeach;

        //$account_numbers = array_unique($account_numbers);
        //print_r($account_numbers);die;
        $this->data['account_number_count'] = count($account_numbers);
        $this->data['total_transactions_amount'] = $this->transaction_alerts_m->get_total_transactions_amount($bank->id,$account_numbers);
        $this->data['total_deposit_transactions_amount'] = $this->transaction_alerts_m->get_total_deposit_transactions_amount($bank->id,$account_numbers);
        $this->data['total_withdrawal_transactions_amount'] = $this->transaction_alerts_m->get_total_withdrawal_transactions_amount($bank->id,$account_numbers);
        $this->data['total_deposit_transactions_amounts_by_group_bank_account_number_array'] = $this->transaction_alerts_m->get_total_deposit_transactions_amounts_by_group_bank_account_number_array($bank->id,$account_numbers);
        $this->data['total_withdrawal_transactions_amounts_by_group_bank_account_number_array'] = $this->transaction_alerts_m->get_total_withdrawal_transactions_amounts_by_group_bank_account_number_array($bank->id,$account_numbers);
        $this->data['deposit_percentage'] = round($this->data['total_deposit_transactions_amount']/$this->data['total_transactions_amount'] * 100);
        $this->data['withdrawal_percentage'] = round($this->data['total_withdrawal_transactions_amount']/$this->data['total_transactions_amount'] * 100);
        $this->data['total_deposits_by_month_array'] = $this->transaction_alerts_m->get_total_deposits_by_month_array($bank->id,$account_numbers);
        $this->data['total_withdrawals_by_month_array'] = $this->transaction_alerts_m->get_total_withdrawals_by_month_array($bank->id,$account_numbers);
        $this->template->title('Equity Bank Transaction Alerts Dashboard')->build('admin/index',$this->data);
    }

    function daily_kpis(){
        $bank = $this->banks_m->get_default_bank();
        $this->data['groups_signed_up_today_by_bank_branch'] = $this->groups_m->get_groups_signed_up_today_by_bank_branch();
        $this->data['groups_signed_up_today_count'] = $this->groups_m->count_groups_signed_up_today();
        $this->data['users_signed_up_today_count'] = $this->users_m->count_users_signed_up_today();
        $this->data['bank_branch_options'] = $this->bank_branches_m->get_bank_branch_options_by_bank_id($bank->id);
        $this->data['groups_signed_up_today_count_by_bank_branch_array'] = $this->groups_m->get_groups_signed_up_today_count_by_bank_branch_array($this->data['bank_branch_options']);
        $this->data['bank_accounts_by_bank_branch_count'] = $this->bank_accounts_m->get_bank_accounts_by_bank_branch_count($bank->id);
        $this->data['total_deposit_transactions_amount_for_today_by_bank_branch_id_array'] = $this->transaction_alerts_m->get_total_deposit_transactions_amount_for_today_by_bank_branch_id_array();
        $this->data['total_withdrawal_transactions_amount_for_today_by_bank_branch_id_array'] = $this->transaction_alerts_m->get_total_withdrawal_transactions_amount_for_today_by_bank_branch_id_array();
        $this->template->title('Daily KPIs')->build('admin/daily_kpis',$this->data);
    }


    function delete_accounts_transaction(){
        //echo $this->transaction_alerts_m->delete_alert_for_specific_account();
    }

    function get_equity_bank_transaction_alerts_from_chamasoft(){
        $data = array();
        $url = 'https://chamasoft.com/bank_accounts/json_ebldata';
        $ch = curl_init();  
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,true );
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);    
        $output = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($output);
        $i=0;
        echo count($result).' records</br>';
        foreach($result as $row):
            if($this->transaction_alerts_m->check_if_equity_bank_transaction_is_duplicate($row->tranid)==0){
                $i++;
                $input = array(
                    'tranCurrency'=>$row->tranCurrency,
                    'tranDate'=>$row->tranDate,
                    'tranid' =>$row->tranid,
                    'tranAmount' =>$row->tranAmount,
                    'trandrcr' =>$row->trandrcr,
                    'accid' =>$row->accid,
                    'refNo' =>$row->refNo,
                    'tranType' =>$row->tranType,
                    'created_on' =>$row->created_on,
                    'tranParticular' =>$row->tranParticular,
                    'tranRemarks' =>$row->tranRemarks
                );
                if($equity_bank_transaction_alert_id = $this->transaction_alerts_m->insert_equity_bank_transaction_alert($input)){
                    $bank_id = $this->banks_m->get_bank_id_by_slug('equity-bank');
                    $description = "<strong>Transaction ID:</strong>".$row->tranid."<br/>
                                    <strong>Transaction Transaction Type:</strong>".$row->tranType."<br/>
                                    <strong>Transaction Reference Number:</strong>".$row->refNo."<br/>
                                    <strong>Transaction Debit or Credit:</strong>".$row->trandrcr."<br/>
                                    <strong>Transaction Remarks:</strong>".$row->tranRemarks."<br/>
                                    <strong>Transaction Particular:</strong>".$row->tranParticular;
                    $input = array(
                        'equity_bank_transaction_alert_id'=>$equity_bank_transaction_alert_id,
                        'created_on'=>$row->created_on,
                        'transaction_id'=>$row->tranid,
                        'type'=>$this->equity_transaction_type_options_keys[$row->trandrcr],
                        'account_number'=>$row->accid,
                        'amount'=>valid_currency($row->tranAmount),
                        'transaction_date'=>strtotime($row->tranDate),
                        'bank_id'=>$bank_id,
                        'active'=>1,
                        'description'=>$description,
                        'particulars'=>$row->tranParticular,
                    );
                    if($this->transaction_alerts_m->insert($input)){
                        //echo json_encode(array('status'=>'success','input'=>'post','message'=>'Request OK','ACK'=>'OK','responseCode'=>$responseCode));
                        //die; 
                    }else{
                        echo json_encode(array('status'=>'success','input'=>'post','message'=>'Could not insert transaction alert','ACK'=>'NO','responseCode'=>2)); 
                        //die;
                    }
                }else{
                    echo json_encode(array('status'=>'success','input'=>'post','message'=>'Could not insert transaction alert','ACK'=>'NO','responseCode'=>2)); 
                    //die;
                }
            }else{
                continue;
            }
        endforeach;
        echo $i;
        echo 'Done';
    }

    function equity_bank_transaction_alerts_listing(){
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-3 years');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $name = $this->input->get('name');
        $bank_account_no = $this->input->get('bank_account_no');
        $amount = $this->input->get('amount');
        $total_rows = $this->transaction_alerts_m->count_equity_bank_transaction_alerts($name,$bank_account_no,$from,$to ,$amount);

        $pagination = create_pagination('admin/transaction_alerts/equity_bank_transaction_alerts_listing/pages', $total_rows,50,5,TRUE);
        $posts = $this->transaction_alerts_m->limit($pagination['limit'])->get_equity_bank_transaction_alerts($bank_account_no,$from,$to ,$amount);
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $this->data['posts'] = $posts;
        $this->data['pagination'] = $pagination;
        $this->data['group_bank_options'] = $this->banks_m->get_admin_bank_options();
        $this->data['group_status_options'] = $this->group_status_options;
        $this->template->title('Equity Bank Transaction Alerts')->build('admin/equity_bank_transaction_alerts_listing',$this->data);
    }


    function safaricom_transaction_alerts_listing(){
        $total_rows = $this->transaction_alerts_m->count_safaricom_transaction_alerts();
        $pagination = create_pagination('admin/transaction_alerts/safaricom_transaction_alerts_listing/pages', $total_rows,50,5,TRUE);
        $posts = $this->transaction_alerts_m->limit($pagination['limit'])->get_safaricom_transaction_alerts();
        $this->data['posts'] = $posts;
        $this->data['pagination'] = $pagination;
        $this->template->title('Safaricom Transaction Alerts')->build('admin/safaricom_transaction_alerts_listing',$this->data);
    }

    function t24_transaction_alerts_listing(){
        $total_rows = $this->transaction_alerts_m->count_t24_transaction_alerts();
        $pagination = create_pagination('admin/transaction_alerts/t24_transaction_alerts_listing/pages', $total_rows,50,5,TRUE);
        $posts = $this->transaction_alerts_m->limit($pagination['limit'])->get_t24_transaction_alerts();
        $this->data['posts'] = $posts;
        $this->data['pagination'] = $pagination;
        $this->template->title('T24 Transaction Alerts')->build('admin/t24_transaction_alerts_listing',$this->data);
    }
    

    function jambo_pay_transaction_alerts_listing(){
        $total_rows = $this->transaction_alerts_m->count_jambo_pay_transaction_alerts();
        $pagination = create_pagination('admin/transaction_alerts/jambo_pay_transaction_alerts_listing/pages', $total_rows,50,5,TRUE);
        $posts = $this->transaction_alerts_m->limit($pagination['limit'])->get_jambo_pay_transaction_alerts();
        $this->data['posts'] = $posts;
        $this->data['pagination'] = $pagination;
        $this->template->title('Jambo Pay Transaction Alerts')->build('admin/jambo_pay_transaction_alerts_listing',$this->data);
    }

    function listing(){
        $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-3 years');
        $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
        $name = $this->input->get('name');
        $bank_account_no = $this->input->get('bank_account_no');
        $amount = $this->input->get('amount');
        $transaction_id  = $this->input->get('transaction_id');

        $total_rows = $this->transaction_alerts_m->count_transaction_alerts($name,$bank_account_no,$from,$to ,$amount ,$transaction_id);

        $pagination = create_pagination('admin/transaction_alerts/listing/pages', $total_rows,250,5,TRUE);
        $this->data['bank_options'] = $this->banks_m->get_admin_bank_options();
        $this->data['mobile_money_providers'] = $this->mobile_money_providers_m->get_admin_mobile_money_provider_options();
        $posts = $this->transaction_alerts_m->limit($pagination['limit'])->get_transaction_alerts($name,$bank_account_no,$from,$to ,$amount,$transaction_id);
        $this->data['posts'] = $posts;
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $this->data['group_options'] = $this->groups_m->get_group_options_account_number_as_key();
        $this->data['pagination'] = $pagination;
        $this->template->title('Transaction Alerts')->build('admin/listing',$this->data);
        
    }

    function forwards($transaction_alert_id = 0){
        $transaction_alert_id OR redirect('admin/transaction_alerts/listing');
        $posts = $this->transaction_alerts_m->get_transaction_alert_forwards_by_transaction_alert_id($transaction_alert_id);
        $this->data['posts'] = $posts;
        $this->template->title('Transaction Alert Forwards')->build('admin/forwards',$this->data);
    }

    function forwards_listing(){
        $posts = $this->transaction_alerts_m->get_transaction_alert_forwards();
        $this->data['posts'] = $posts;
        $this->template->title('Transaction Alert Forwards')->build('admin/forwards',$this->data);
    }

    function create_transaction_alert_forwarder(){
        $post = new stdClass();   
        $validation_rules = array(
            array(
                'field' => 'name',
                'label' => 'Forwarder Name',
                'rules' => 'trim|required',
            ),array(
                'field' => 'bank_id',
                'label' => 'Bank',
                'rules' => 'trim|numeric',
            ),array(
                'field' => 'url',
                'label' => 'Forwarder URL',
                'rules' => 'trim|required',
            ),array(
                'field' => 'account_number',
                'label' => 'Bank Account Number',
                'rules' => 'trim',
            ),array(
                'field' => 'account_name',
                'label' => 'Bank Account Name',
                'rules' => 'trim',
            ),array(
                'field' => 'type',
                'label' => 'Forwarder Type',
                'rules' => 'trim|numeric',
            )
        ); 
        if($this->input->post('type')==2){
            $validation_rules[] = array(
                'field' => 'bank_id',
                'label' => 'Bank',
                'rules' => 'trim|numeric|required',
            );
            $validation_rules[] = array(
                'field' => 'account_number',
                'label' => 'Bank Account Number',
                'rules' => 'trim|required',
            );
            $validation_rules[] = array(
                'field' => 'account_name',
                'label' => 'Bank Account Number',
                'rules' => 'trim|required',
            );
        }  
        $this->form_validation->set_rules($validation_rules);
        if($this->form_validation->run()){
            $input = array(
                'name' => $this->input->post('name'),
                'bank_id' => $this->input->post('bank_id'),
                'url' => $this->input->post('url'),
                'type' => $this->input->post('type'),
                'account_number' => $this->input->post('account_number'),
                'account_name' => $this->input->post('account_name'),
                'created_on' => time(),
                'created_by' => $this->user->id,
            );
            if($this->transaction_alerts_m->insert_transaction_alert_forwarder($input)){
                $this->session->set_flashdata('success','Transaction forwarder added successfully.');
                redirect('admin/transaction_alerts/transaction_alert_forwarders_listing');
            }else{
                $this->session->set_flashdata('error','Could not insert transaction forwarder');
            }
        }else{
            foreach ($validation_rules as $key => $field){
                $post->$field['field'] = set_value($field['field']);
            }
        } 
        $this->data['forwarder_type_options'] = $this->forwarder_type_options;
        $this->data['bank_options'] = $this->banks_m->get_admin_bank_options();
        $this->data['post'] = $post;
        $this->template->title('Create Transaction Alert Forwarder')->build('admin/form',$this->data);
    }

    function edit_transaction_alert_forwarder($id = 0){
        $id OR redirect('admin/transaction_alerts/transaction_alert_forwarders_listing');
        $post = $this->transaction_alerts_m->get_transaction_alert_forwarder($id);
        $post OR redirect('admin/transaction_alerts/transaction_alert_forwarders_listing');  
        $validation_rules = array(
            array(
                'field' => 'name',
                'label' => 'Forwarder Name',
                'rules' => 'trim|required',
            ),array(
                'field' => 'bank_id',
                'label' => 'Bank',
                'rules' => 'trim|numeric',
            ),array(
                'field' => 'url',
                'label' => 'Forwarder URL',
                'rules' => 'trim|required',
            ),array(
                'field' => 'account_number',
                'label' => 'Bank Account Number',
                'rules' => 'trim',
            ),array(
                'field' => 'account_name',
                'label' => 'Bank Account Name',
                'rules' => 'trim',
            ),array(
                'field' => 'type',
                'label' => 'Forwarder Type',
                'rules' => 'trim|numeric',
            )
        ); 
        if($this->input->post('type')==2){
            $validation_rules[] = array(
                'field' => 'bank_id',
                'label' => 'Bank',
                'rules' => 'trim|numeric|required',
            );
            $validation_rules[] = array(
                'field' => 'account_number',
                'label' => 'Bank Account Number',
                'rules' => 'trim|required',
            );
            $validation_rules[] = array(
                'field' => 'account_name',
                'label' => 'Bank Account Number',
                'rules' => 'trim|required',
            );
        }   
        $this->form_validation->set_rules($validation_rules);
        if($this->form_validation->run()){
            $input = array(
                'name' => $this->input->post('name'),
                'bank_id' => $this->input->post('bank_id'),
                'url' => $this->input->post('url'),
                'account_number' => $this->input->post('account_number'),
                'type' => $this->input->post('type'),
                'account_name' => $this->input->post('account_name'),
                'modified_on' => time(),
                'modified_by' => $this->user->id,
            );
            if($this->transaction_alerts_m->update_transaction_alert_forwarder($id,$input)){
                $this->session->set_flashdata('success','Transaction forwarder changes saved successfully.');
                redirect('admin/transaction_alerts/transaction_alert_forwarders_listing');
            }else{
                $this->session->set_flashdata('error','Could not update transaction forwarder');
            }
        }else{
            foreach ($validation_rules as $key => $field){
                $post->$field['field'] = set_value($field['field'])?set_value($field['field']):$post->$field['field'];
            }
        }
        $this->data['forwarder_type_options'] = $this->forwarder_type_options; 
        $this->data['bank_options'] = $this->banks_m->get_admin_bank_options();
        $this->data['post'] = $post;
        $this->template->title('Edit Transaction Alert Forwarder')->build('admin/form',$this->data);
    }

    function delete_transaction_alert_forwarder($id = 0){
        $id OR redirect('admin/transaction_alerts/transaction_alert_forwarders_listing');
        $post = $this->transaction_alerts_m->get_transaction_alert_forwarder($id);
        $post OR redirect('admin/transaction_alerts/transaction_alert_forwarders_listing');
        if($this->transaction_alerts_m->delete_transaction_alert_forwarder($id)){
            $this->session->set_flashdata('success','Transaction alert forwarder deleted successfully');
        }else{
            $this->session->set_flashdata('error','Transaction alert forwarder could not be deleted');
        }
        redirect('admin/transaction_alerts/transaction_alert_forwarders_listing');
    }

    function transaction_alert_forwarders_listing(){
        $this->data['forwarder_type_options'] = $this->forwarder_type_options;
        $this->data['bank_options'] = $this->banks_m->get_admin_bank_options();
        $this->data['posts'] = $this->transaction_alerts_m->get_transaction_alert_forwarders();
        $this->template->title('List Transaction Alert Forwarders')->build('admin/transaction_alert_forwarders_listing',$this->data);
    }

    function fix_transaction_alerts_currency(){
        $equity_bank_transaction_alerts = $this->transaction_alerts_m->get_equity_bank_transaction_alerts();
        $result = TRUE;
        foreach($equity_bank_transaction_alerts as $equity_bank_transaction_alert):
            $input = array(
                'currency' => $equity_bank_transaction_alert->tranCurrency,
                'modified_on' => time(),
                'modified_by' => $this->user->id,
            );
            if($this->transaction_alerts_m->update_transaction_alert_by_equity_bank_transaction_alert_id($equity_bank_transaction_alert->id,$input)){

            }else{
                $result = FALSE;
            }
        endforeach;

        if($result){
            echo "All went well";
        }else{
            echo "Something went wrong";
        }
    }

    function find_missing_eazzychama_transaction_alerts($from = 0,$to = 0){
        if($from){
            $from = strtotime($from);
        }
        if($to){
            $to = strtotime($to);
        }
        $chamasoft_transaction_alerts = $this->transaction_alerts_m->get_transaction_alerts($from,$to);
        $eazzychama_transaction_alerts = $this->transaction_alerts_m->get_eazzychama_transaction_alerts($from,$to);
        $chamasoft_transaction_alert_ids_array = array();
        $eazzychama_transaction_alert_ids_array = array();
        foreach($chamasoft_transaction_alerts as $chamasoft_transaction_alert):
            $chamasoft_transaction_alert_ids_array[] = $chamasoft_transaction_alert->transaction_id;
        endforeach;
        foreach($eazzychama_transaction_alerts as $eazzychama_transaction_alert):
            $eazzychama_transaction_alert_ids_array[] = $eazzychama_transaction_alert->transaction_id;
        endforeach;

        $missing_eazzychama_transaction_alerts_ids_array = array();
        foreach($chamasoft_transaction_alert_ids_array as $key => $value):
            if(in_array($value,$eazzychama_transaction_alert_ids_array)){

            }else{
                $missing_eazzychama_transaction_alerts_ids_array[] = $value;
            }
        endforeach;
        $array_string = "(";
        foreach($missing_eazzychama_transaction_alerts_ids_array as $key => $value):
            $array_string .=" ".$key." => '".$value."' , <br/>";
        endforeach;
        $array_string .=")";
        echo $array_string;
        die;
    }
        

    function send_transaction_alerts_to_eazzychama(){
        set_time_limit(0);
            $transaction_alert_ids_array = array
 ( 0 => 'NB19JXSUVN' , 
1 => 'NAU1IA0RB5' , 
2 => 'NAU2I9M1DE' , 
3 => 'NAU1I9HJDP' , 
4 => 'NAT5HJCXX1' , 
5 => 'NAO3DRRZGP' , 
6 => 'NAL2BOEI5K' , 
7 => 'NAJ7AMMUFX' , 
8 => '201901185616356-2' , 
9 => '201901185616354-1' , 
10 => '20190118S546218-2' , 
11 => '20190118S414638-2' , 
12 => '20190118S401720-2' , 
13 => '201901185615697-1' , 
14 => '201901185615699-2' , 
15 => '20190118S384888-2' , 
16 => '2019011854136362-2' , 
17 => '2019011854136353-1' , 
18 => '201901185615158-1' , 
19 => '20190118S372785-2' , 
20 => '20190118S543966-2' , 
21 => '2019011854130361-1' , 
22 => '2019011854130369-2' , 
23 => '20190118S353724-2' , 
24 => '20190118S543272-2' , 
25 => '20190118S543157-2' , 
26 => '20190118S345804-2' , 
27 => '201901185614203-2' , 
28 => '2019011854126010-2' , 
29 => '2019011854126014-2' , 
30 => '2019011854125710-1' , 
31 => '2019011854124285-1' , 
32 => '2019011854124293-2' , 
33 => '20190118S331222-2' , 
34 => '2019011854120311-1' , 
35 => '2019011854120319-2' , 
36 => '20190118S320721-2' , 
37 => '20190118S318593-2' , 
38 => '20190118S316594-2' , 
39 => '20190118S311533-2' , 
40 => 'NAB44AC3R8' , 
41 => 'NA841XZA32' , 
42 => 'NA7417DVFW' , 
43 => 'NA30XLV1F4' , 
);

            foreach($transaction_alert_ids_array as $transaction_id):
                if($transaction_alert = $this->transaction_alerts_m->get_equity_bank_transaction_alert_by_transaction_id($transaction_id)){
                    $json_file = '{
                        "tranCurrency": "'.$transaction_alert->tranCurrency.'",
                        "tranDate": "'.$transaction_alert->tranDate.'",
                        "tranid": "'.$transaction_alert->tranid.'",
                        "tranAmount": "'.$transaction_alert->tranAmount.'",
                        "trandrcr": "'.$transaction_alert->trandrcr.'",
                        "refNo": "'.$transaction_alert->refNo.'",
                        "tranType": "'.$transaction_alert->tranType.'",
                        "tranParticular": "'.$transaction_alert->tranParticular.'", 
                        "tranRemarks": "'.$transaction_alert->tranRemarks.'",
                        "accid": "'.$transaction_alert->accid.'",
                        "username" : "chamasoft",
                        "password" : "NuFN=FbktVBfJb9Tt4ew8scAT#RRHD=j##Eug95nndmt4g+Aky93DR9RY_6C+"
                    }';
                    $url = 'https://eazzychama.co.ke/endpoint';
                    if($response = $this->curl->post_json($json_file,$url)){
                        echo $response."<br/>";
                    }else{
                        echo "did not receive response from endpoint. <br/>";
                    }
                }else{
                    echo "Cooud not find alert.<br/>";
                }
            endforeach;
        }

        function fix_date(){
            $id = '110304';
            $transaction = $this->transaction_alerts_m->get($id);
            if($transaction){
                $update = array(
                    'transaction_date' => '1541518020',
                    'created_on' => '1541518020',
                    'modified_on' => '1541518020',
                );
                $this->transaction_alerts_m->update($transaction->id,$update);
                //$this->transaction_alerts_m->update_online_banking_transaction($transaction->online_banking_transaction_alert_id,$update);
            }

            echo 'done';
            
        }

        function check_duplicate_transactions_alerts($id = 0){
            $id OR redirect('admin');
            $transaction_alert_details = $this->transaction_alerts_m->get($id);
            $equity_bank_transaction_alert_ids = array();
            $transaction_alert_delete_ids = array();
            if($transaction_alert_details){
                $transaction_id = $transaction_alert_details->transaction_id;
                $account_number = $transaction_alert_details->account_number;
                $amount =  currency($transaction_alert_details->amount);
                $equity_bank_transaction_alert_id = $transaction_alert_details->equity_bank_transaction_alert_id;
                $similar_details = $this->transaction_alerts_m->get_similar_transaction_alert($transaction_alert_details->bank_id , $account_number ,$transaction_alert_details->transaction_id);
                foreach ($similar_details as $key => $detail):
                    if($id == $detail->id){
                        //do nothing
                        $undeleted_alert_id = $detail->id;
                    }else{
                        $transaction_alert_delete_ids[] = $detail->id;
                        $equity_bank_transaction_alert_ids[] = $detail->equity_bank_transaction_alert_id;
                    }
                endforeach;
                echo 'id left :' .$undeleted_alert_id .'<br>';
                echo 'transaction id: ' .$transaction_alert_details->transaction_id .'<br>';
                print_r($account_number);
                print_r($transaction_alert_delete_ids);
                print_r($equity_bank_transaction_alert_ids);
                print_r($similar_details);

            }else{  
                echo 'There is no transaction alert details';
            }
        }

        function get_unreconciled_duplicate_transaction_alerts(){

            @ini_set('memory_limit','1500M');
            $from = strtotime('-7 days');
            $to = time();
            $transaction_alerts = $this->transaction_alerts_m->get_unreconciled_duplicate_transaction_alerts($from,$to);
            $arr = array();
            $transaction_id_array = array();
            foreach($transaction_alerts as $transaction_alert):
                if(isset($arr[$transaction_alert->transaction_id])){
                    $transaction_id_array[] = $transaction_alert->id;
                }else{
                    $arr[$transaction_alert->transaction_id] = 1;
                }
                //echo timestamp_to_date($transaction_alert->transaction_date)."|".$transaction_alert->transaction_id."|".$transaction_alert->account_number."|".number_to_currency($transaction_alert->amount)."<br/>";
            endforeach;
            //echo count($transaction_alerts);
            //die;
            foreach($transaction_id_array as $transaction_id):
                $input = array(
                    'active' => 0,
                    'modified_by' => 1,
                    'modified_on' => time(),
                );
                if($this->transaction_alerts_m->update($transaction_id,$input)){
                    echo "Success. <br/>";
                }
            endforeach;

        }


        function get_reconciled_duplicate_transaction_alerts(){

            @ini_set('memory_limit','1500M');
            $from = strtotime('-7 days');
            $to = time();
            $transaction_alerts = $this->transaction_alerts_m->get_reconciled_duplicate_transaction_alerts($from,$to);
            $arr = array();
            $transaction_id_array = array();
            foreach($transaction_alerts as $transaction_alert):
                if(isset($arr[$transaction_alert->transaction_id])){
                    $transaction_id_array[] = $transaction_alert->id;
                }else{
                    $arr[$transaction_alert->transaction_id] = 1;
                }
                //echo timestamp_to_date($transaction_alert->transaction_date)."|".$transaction_alert->transaction_id."|".$transaction_alert->account_number."|".number_to_currency($transaction_alert->amount)."<br/>";
            endforeach;
            echo count($transaction_id_array);
            die;
            foreach($transaction_id_array as $transaction_id):
                $input = array(
                    'active' => 0,
                    'modified_by' => 1,
                    'modified_on' => time(),
                );
                if($this->transaction_alerts_m->update($transaction_id,$input)){
                    echo "Success. <br/>";
                }
            endforeach;

        }

        function e_wallet_transaction_alerts(){
            $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-3 years');
            $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
            $name = $this->input->get('name');
            $bank_account_no = $this->input->get('bank_account_no');
            $amount = $this->input->get('amount');
            $transaction_id  = $this->input->get('transaction_id');

            $total_rows = $this->transaction_alerts_m->count_e_wallet_transaction_alerts($name,$bank_account_no,$from,$to ,$amount ,$transaction_id);

            $pagination = create_pagination('admin/transaction_alerts/e_wallet_transaction_alerts/pages', $total_rows,250,5,TRUE);
            $this->data['bank_options'] = $this->banks_m->get_admin_bank_options();
            $this->data['mobile_money_providers'] = $this->mobile_money_providers_m->get_admin_mobile_money_provider_options();
            $posts = $this->transaction_alerts_m->limit($pagination['limit'])->get_e_wallet_transaction_alerts($name,$bank_account_no,$from,$to ,$amount,$transaction_id);
            $this->data['posts'] = $posts;
            $this->data['from'] = $from;
            $this->data['to'] = $to;
            $this->data['group_options'] = $this->groups_m->get_group_options_account_number_as_key();
            $this->data['pagination'] = $pagination;
            $this->template->title('E-Wallet Transaction Alerts')->build('admin/e_wallet_transaction_alerts',$this->data);
        }

        function delete($transaction_alert_id=0){
            $transaction_alert_id OR redirect('admin/transaction_alerts/listing');
            $post = $this->transaction_alerts_m->get($transaction_alert_id);
            if($post->online_banking_transaction_alert_id){
                $this->db->where('id',$post->online_banking_transaction_alert_id);
                $this->db->delete('online_banking_transaction_alerts');
                $this->db->where('id',$transaction_alert_id);
                $this->db->delete('transaction_alerts');
            }
            redirect('admin/transaction_alerts/listing');
        }

}