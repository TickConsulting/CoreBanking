<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{

    protected $data = array();
    protected $validation_rules = array(
            array(
                    'field' => 'description',
                    'label' => 'Bank loan description',
                    'rules' => 'required|trim',
                ),
            array(
                    'field' => 'amount_loaned',
                    'label' => 'Bank loan amount received',
                    'rules' => 'required|trim|currency',
                ),
            array(
                    'field' => 'total_loan_amount_payable',
                    'label' => 'Bank loan total amount payable',
                    'rules' => 'required|trim|currency',
                ),
            array(
                    'field' => 'loan_balance',
                    'label' => 'Bank loan balance',
                    'rules' => 'required|trim|currency',
                ),
            array(
                    'field' => 'loan_start_date',
                    'label' => 'Bank loan start date',
                    'rules' => 'required|trim',
                ),
            array(
                    'field' => 'loan_end_date',
                    'label' => 'Bank loan end date',
                    'rules' => 'required|trim|callback__end_date_is_great_than_start_date',
                ),
            array(
                    'field' => 'account_id',
                    'label' => 'Group account loan deposited to',
                    'rules' => 'required|trim',
                ),
        );

    protected $repayment_status_options = array(
        1 => "Fully Paid",
        0 => "In Progress",
    );


    protected $validation_rules_rapayment = array(
             array(
                    'field' => 'repayment_date',
                    'label' => 'Bank loan repayment date',
                    'rules' => '',
                ),
            array(
                    'field' => 'member_id',
                    'label' => 'Member Name',
                    'rules' => '',
                ),
            array(
                    'field' => 'account_id',
                    'label' => 'Account',
                    'rules' => '',
                ),
            array(
                    'field' => 'amounts',
                    'label' => 'Amount repaid',
                    'rules' => '',
                ),
        );

    function __construct()
    {
        parent::__construct();
        $this->load->model('accounts/accounts_m');
        $this->load->model('bank_loans_m');
        $this->load->model('members/members_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->library('transactions');
        $this->load->library('loan');
        $this->accounts = $this->accounts_m->get_active_group_account_options(FALSE,TRUE);
        $this->data['accounts'] = $this->accounts;
        $this->data['members'] = $this->members_m->get_group_member_options();
        $this->data['banks'] = $this->banks_m->get_group_bank_options();
        $this->data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $this->data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $this->data['withdrawal_method_options'] = $this->transactions->withdrawal_method_options;
    }

    function _end_date_is_great_than_start_date(){
        $loan_end_date = $this->input->post('loan_end_date');
        $loan_start_date = $this->input->post('loan_start_date');
        if(strtotime($loan_end_date)<strtotime($loan_start_date)){
            $this->form_validation->set_message('_end_date_is_great_than_start_date','Loan end date can not be before loan start date');
            return FALSE;
        }
        else{
            return TRUE;
        }
    }

    
    function index(){
        $this->template->title('Group Bank Loans')->build('group/index');
    }


    function create(){
        $post = new stdClass();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            if($this->input->post('loan_balance')<=0 ){
                $is_fully_paid = 1;
            }else{
                $is_fully_paid='';
            }
            $description = $this->input->post('description');
            $amount_loaned = $this->input->post('amount_loaned');
            $total_loan_amount_payable = $this->input->post('total_loan_amount_payable');
            $loan_balance = $this->input->post('loan_balance');
            $loan_start_date = strtotime($this->input->post('loan_start_date'));
            $loan_end_date = strtotime($this->input->post('loan_end_date'));
            $account_id = $this->input->post('account_id');
            if($this->transactions->create_bank_loan($this->group->id,$description,$amount_loaned,$total_loan_amount_payable,$loan_balance,$loan_start_date,$loan_end_date,$account_id,$is_fully_paid)){
                $this->session->set_flashdata('success','Bank loan successfully created');
                if($this->input->post('new_item')){
                    redirect('group/bank_loans/create');
                }else{
                    redirect('group/bank_loans/listing');
                }
            }else{
                $this->session->set_flashdata('error','Error creating Bank loan');
                redirect('group/bank_loans/create');
            }

        }

        foreach ($this->validation_rules as $key => $field) {
            $field_name = $field['field'];
            $post->$field_name = set_value($field['field']);
        }

        $this->data['id'] = '';
        $this->data['post'] = $post;
        $this->template->title(translate('Create Bank Loan'))->build('group/form',$this->data);
    }

    function edit($id=0){
        $id OR redirect('group/bank_loans/listing');
        $post = $this->bank_loans_m->get($id);
        if(empty($post)){
            $this->session->set_flashdata('error','Sorry, the bank loan does not exist');
            redirect('group/bank_loans/listing');
            return FALSE;
        }
        $this->form_validation->set_rules(array(
                array(
                        'field' => 'description',
                        'label' => 'Bank Loan Description',
                        'rules' => 'trim|required',
                    ),
                array(
                        'field' => 'loan_balance',
                        'label' => 'Bank Loan Balance',
                        'rules' => 'trim|required|currency',
                    ),
            ));
        if($this->form_validation->run()){
            if($this->input->post('loan_balance')<=0 ){
                $is_fully_paid = 1;
            }else{
                $is_fully_paid='';
            }
            $update = $this->bank_loans_m->update($post->id,array(
                'description' => $this->input->post('description'),
                'loan_balance' => $this->input->post('loan_balance'),
                'balance' => $this->input->post('loan_balance'),
                'is_fully_paid' => $is_fully_paid,
                'modified_by' => $this->user->id,
                'modified_on' => time(),
            ));
            if($update){
                $this->session->set_flashdata('success', $this->input->post('description').'  successfully edited');
                if($this->input->post('new_item')){
                    redirect('group/bank_loans/create');
                }else{
                    redirect('group/bank_loans/listing');
                }
            }else{
                $this->session->set_flashdata('error','There was an error updating bank loan. Try again');
                redirect('group/bank_loans/edit/'.$id);
            }
        }
        else
        {
            // Go through all the known fields and get the post values
            foreach (array_keys($this->validation_rules) as $field)
            {
                 if (isset($_POST[$field]))
                {
                    $post->$field = $this->form_validation->$field;
                }
            }
        }

        $this->data['post'] = $post;
        $this->data['id'] = $post->id;
        $this->data['edit'] = TRUE;

        $this->template->title('Edit Bank Loan')->build('group/form',$this->data);
    }

    function listing(){
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
        $data['repayment_status_options'] = $this->repayment_status_options;
        if($this->input->get('generate_excel')==1){
            $filter_parameters = array(
                'accounts' => $this->input->get('accounts')?:'',
                'is_fully_paid' => $this->input->get('is_fully_paid')?:'',
                'from' => $from,
                'to' => $to,
            );
            $posts = $this->bank_loans_m->get_group_bank_loans($filter_parameters);
            $data['posts'] = $posts;
            $data['filter_parameters'] = $filter_parameters;
            $data['group_currency'] = $this->group_currency;
            $data['group'] = $this->group;
            $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
            $json_file = json_encode($data);
            //print_r($json_file);die;
            print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/bank_loans_summary/listing',$this->group->name.' Bank Loans List'));
            die;
        }
        $data['from'] = $from;
        $data['to'] = $to;
        $this->template->title(translate('Bank Loan List'))->build('group/listing',$data);
    }

    function void($id = 0,$redirect = TRUE){
        $id OR redirect('group/bank_loans/listing');
        $post = $this->bank_loans_m->get($id);
        $post OR redirect('group/bank_loans/listing');
        $deposit = $this->deposits_m->get_bank_loan_disbursement_deposit_by_bank_loan_id($id);
        $deposit OR redirect('group/bank_loans/listing');
        if($this->transactions->void_group_deposit($deposit->id,$deposit,TRUE,$this->group->id)){
            $this->session->set_flashdata('success','Bank loan voided successfully');
        }else{
            $this->session->set_flashdata('error','Bank loan could not be voided successfully');
        }
        if($redirect){
            if($this->agent->referrer()){
                redirect($this->agent->referrer());
            }else{
                redirect('group/bank_loans/listing');
            }
        }
    }

    function statement($id=0)
    {
        $id OR redirect('group/bank_loans/listing');
        $loan = $this->bank_loans_m->get($id);
        if(empty($loan)){
            $this->session->set_flashdata('error','Sorry, the bank loan is not available');
            redirect('group/bank_loans/listing');
            return FALSE;
        }
        $posts = $this->bank_loans_m->get_all_bank_loan_repayments($id);

        $this->data['posts'] = $posts;
        $this->data['loan'] = $loan;
        $this->accounts = $this->accounts_m->get_group_account_options(FALSE);
        $this->data['accounts'] = $this->accounts;
        $this->template->title('Bank loan statement')->build('shared/statement',$this->data);
    }

    function record_repayment($id){
        $id OR redirect('group/bank_loans/listing');
        $bank_loan = $this->bank_loans_m->get($id);

        if(!$bank_loan){
            $this->session->set_flashdata('error','Sorry, the bank loan is not available');
            redirect('group/bank_loans/listing');
            return FALSE;
        }
       
        $posts = $_POST;
        $errors = array();
        $error_messages = array();
        $successes = array();
        if($this->input->post('submit')){
            $entries_are_valid = TRUE;
            if(!empty($posts)){
                if(isset($posts['repayment_date'])){
                    $count = count($posts['repayment_date']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['repayment_date'][$i])&&isset($posts['account_id'][$i])&&isset($posts['repayment_descriptions'][$i])&&isset($posts['amounts'][$i])&&isset($posts['repayment_method'])):    
                            //Deposit dates
                            if($posts['repayment_date'][$i]==''){
                                $successes['repayment_date'][$i] = 0;
                                $errors['repayment_date'][$i] = 1;
                                $error_messages['repayment_date'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['repayment_date'][$i] = 1;
                                $errors['repayment_date'][$i] = 0;
                            }
                            //Members
                            if($posts['repayment_descriptions'][$i]==''){
                                $successes['repayment_descriptions'][$i] = 0;
                                $errors['repayment_descriptions'][$i] = 1;
                                $error_messages['repayment_descriptions'][$i] = 'Please add repayment description';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['repayment_descriptions'][$i] = 1;
                                $errors['repayment_descriptions'][$i] = 0;
                            }
                             //Accounts
                            if($posts['account_id'][$i]==''){
                                $successes['account_id'][$i] = 0;
                                $errors['account_id'][$i] = 1;
                                $error_messages['account_id'][$i] = 'Please select an account';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['account_id'][$i] = 1;
                                $errors['account_id'][$i] = 0;
                            }
                            //amounts
                            if($posts['amounts'][$i]==''){
                                $successes['amounts'][$i] = 0;
                                $errors['amounts'][$i] = 1;
                                $error_messages['amounts'][$i] = 'Please enter a repayment amount';
                                $entries_are_valid = FALSE;
                            }else{
                                if(valid_currency($posts['amounts'][$i])){
                                    $successes['amounts'][$i] = 1;
                                    $errors['amounts'][$i] = 0;
                                }else{
                                    $successes['amounts'][$i] = 0;
                                    $errors['amounts'][$i] = 1;
                                    $error_messages['amounts'][$i] = 'Please enter a valid repayment amount';
                                    $entries_are_valid = FALSE; 
                                }
                            }

                            //withdrawal methods
                            if($posts['repayment_method'][$i]==''){
                                $successes['repayment_method'][$i] = 0;
                                $errors['repayment_method'][$i] = 1;
                                $error_messages['repayment_method'][$i] = 'Please enter a repayment method';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['repayment_method'][$i] = 1;
                                $errors['repayment_method'][$i] = 0;
                            }
                        endif;
                    endfor;
                }
            }

            if($entries_are_valid){
                $this->session->set_flashdata('error','');
                $successful_expense_entry_count = 0;
                $unsuccessful_expense_entry_count = 0;
                if(isset($posts['repayment_date'])){
                    $count = count($posts['repayment_date']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['repayment_date'][$i])&&isset($posts['account_id'][$i])&&isset($posts['repayment_descriptions'][$i])&&isset($posts['amounts'][$i])&&isset($posts['repayment_method'])): 

                            $is_bank_loan_interest = isset($posts['is_bank_loan_interest'][$i])?1:0;
                            $amount = valid_currency($posts['amounts'][$i]);
                            $repayment_date = strtotime($posts['repayment_date'][$i]); 
                            //
                            $result = $this->loan->bank_loan_repayment(
                                $bank_loan->id,
                                $amount,
                                $repayment_date,
                                $this->group->id,
                                $posts['account_id'][$i],
                                $posts['repayment_method'][$i],
                                $posts['repayment_descriptions'][$i],
                                $this->user->id,
                                0,
                                FALSE,
                                $is_bank_loan_interest
                            );
                            if($result){
                                ++$successful_expense_entry_count;
                            }else{
                                 ++$unsuccessful_expense_entry_count;
                            }
                        endif;
                    endfor;
                }
                if($successful_expense_entry_count){
                    if($successful_expense_entry_count==1){
                        $this->session->set_flashdata('success',$successful_expense_entry_count.' bank loan repayment successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('success',$successful_expense_entry_count.' bank loan repayment successfully recorded. ');
                    }
                }

                if($unsuccessful_expense_entry_count){
                    if($unsuccessful_expense_entry_count==1){
                        $this->session->set_flashdata('error',$unsuccessful_expense_entry_count.' bank loan repayment was not successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('error',$unsuccessful_expense_entry_count.' bank loan repayment were not successfully recorded. ');
                    }
                }
                redirect('group/bank_loans/statement/'.$id);
            }else{
                $this->session->set_flashdata('error','There are some errors on the form. Please review and try again.');
            }
            
        }
        $this->data['errors'] = $errors;
        $this->data['error_messages'] = $error_messages;
        $this->data['successes'] = $successes;
        $this->data['posts'] = $posts;
        $this->data['bank_loan'] = $bank_loan;
        $this->data['posts'] = $posts;
        $this->data['id'] = $id;
        $this->template->title('Record Bank loan repayment - '.$bank_loan->description)->set_layout('default_full_width.html')->build('group/record_repayment',$this->data);
    }


    function repayment_listing(){

        $total_rows = $this->bank_loans_m->count_repayments();
        $pagination = create_pagination('group/bank_loans/repayment_listing/pages',$total_rows);
        $posts = $this->bank_loans_m->limit($pagination['limit'])->get_all_bank_loan_payments();

        $this->data['posts'] = $posts;
        $this->accounts = $this->accounts_m->get_group_account_options(FALSE);
        $this->data['accounts'] = $this->accounts;
        $this->data['pagination'] = $pagination;

        $this->template->title('Bank loan repayments list')->build('group/repayment_listing',$this->data);
    }

    function view_repayment($id=0){
        $id OR redirect('group/bank_loans/repayment_listing');

        $post = $this->bank_loans_m->get_bank_loan_repayment($id);
        if(!$post){
            $this->session->set_flashdata('error','The repayment does not exist');
            redirect('group/bank_loans/repayment_listing');
            return FALSE;
        }

        $this->data['post'] = $post;
        $this->accounts = $this->accounts_m->get_group_account_options(FALSE);
        $this->data['accounts'] = $this->accounts;
        $this->template->title('View Repayment')->build('group/view_repayment',$this->data);
    }

    function void_repayment($id=0,$redirect=TRUE){
        if($id){
            $post = $this->bank_loans_m->get_bank_loan_repayment($id);
            if($post){
                $void_id = $this->loan->void_bank_loan_repayment($id,$this->user->id);
                if($void_id){
                    $this->session->set_flashdata('Success','Bank loan repayment successfully voided');
                    if($redirect){
                        redirect($this->agent->referrer(),'refresh');
                    }
                    return TRUE;
                }else{
                    $this->session->set_flashdata('error','Bank loan repayment not voided');
                    if($redirect){
                        redirect($this->agent->referrer(),'refresh');
                    }
                    return FALSE;
                }

            }else{
                $this->session->set_flashdata('error','Bank loan not found');
                if($redirect){
                    redirect($this->agent->referrer(),'refresh');
                }
                return FALSE;
            }

        }else{
            $this->session->set_flashdata('error','Bank loan id required');
            if($redirect){
                redirect($this->agent->referrer(),'refresh');
            }
            return FALSE;
        }
    }

    function action_payment(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_void'){
            for($i=0;$i<count($action_to);$i++){
                $this->void_repayment($action_to[$i],FALSE);
            }
        }
        if($this->agent->referrer()){
            redirect($this->agent->referrer());
        }else{
            redirect('group/bank_loans/repayment_listing');
        }
    }

}