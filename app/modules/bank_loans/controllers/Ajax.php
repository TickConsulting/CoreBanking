<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
	
	protected $data = array();
    protected $validation_rules = array(
        array(
                'field' => 'description',
                'label' => 'Bank loan description',
                'rules' => 'xss_clean|required|trim',
            ),
        array(
                'field' => 'amount_loaned',
                'label' => 'Bank loan amount received',
                'rules' => 'xss_clean|required|trim|currency',
            ),
        array(
                'field' => 'total_loan_amount_payable',
                'label' => 'Bank loan total amount payable',
                'rules' => 'xss_clean|required|trim|currency',
            ),
        array(
                'field' => 'loan_balance',
                'label' => 'Bank loan balance',
                'rules' => 'xss_clean|required|trim|currency',
            ),
        array(
                'field' => 'loan_start_date',
                'label' => 'Bank loan start date',
                'rules' => 'xss_clean|required|trim',
            ),
        array(
                'field' => 'loan_end_date',
                'label' => 'Bank loan end date',
                'rules' => 'xss_clean|required|trim|callback__end_date_is_great_than_start_date',
            ),
        array(
                'field' => 'account_id',
                'label' => 'Group account loan deposited to',
                'rules' => 'xss_clean|required|trim',
            ),
    );
    
    function __construct(){
        parent::__construct();
        $this->load->model('bank_loans_m');
        $this->load->model('accounts/accounts_m');
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

    function create(){
        $response = array();
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
                $response = array(
                    'status' => 1,
                    'message' => 'Bank loan successfully created',
                    'refer' => site_url('group/bank_loans/listing'),
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Error creating Bank loan',
                );
            }

        }else{
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
        echo json_encode($response);die;
    }

    function edit(){
        $response = array();
        $id = $this->input->post('id');
        if($post = $this->bank_loans_m->get($id)){       
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
                    $response = array(
                        'status' => 1,
                        'message' => $this->input->post('description').'  successfully edited' ,
                        'refer'=>site_url('group/bank_loans/listing')
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'There was an error updating bank loan. Try again' ,
                        'refer'=>site_url('group/bank_loans/listing')
                    );
                }
            }else{
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
        }else{
            $response = array(
                'status' => 0,
                'message' => 'The bank loan not exist' ,
                'validation_errors' => '',
            );
        }
        echo json_encode($response);
    }

    function record_repayment(){
        $data = array();
        $posts = $_POST;
        $errors = array();
        $response = array();
        $error_messages = array();
        $successes = array();
        $entries_are_valid = TRUE; 
        $id = $this->input->post('id');
        if($id){
            $bank_loan = $this->bank_loans_m->get($id);
            if($bank_loan){
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
                        if($successful_expense_entry_count){
                            $response = array(
                                'status' => 1,
                                'message' =>'success',$successful_expense_entry_count.' bank loan repayment successfully recorded.',
                                'refer'=>site_url('group/bank_loans/statement/'.$id),
                            );
                        }
                    }

                    if($unsuccessful_expense_entry_count){
                        $response = array(
                            'status' => 1,
                            'message' => 'error',$unsuccessful_expense_entry_count.' bank loan repayment was not successfully recorded. ',
                            'refer'=>site_url('group/bank_loans/statement/'.$id),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'error','There are some errors on the form. Please review and try again.',
                    );                  
                }

            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Sorry, the bank loan is not available',
                );  
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'bank loan id variable required',
            );
        }
        echo json_encode($response);
        


    }

    function get_bank_loans_listing(){
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $filter_parameters = array(
            'accounts' => $this->input->get('accounts')?:'',
            'is_fully_paid' => $this->input->get('is_fully_paid')?:'',
            'from' => $from,
            'to' => $to,
        );
        $posts = $this->bank_loans_m->get_group_bank_loans($filter_parameters);
        $account_options = $this->accounts_m->get_group_account_options(FALSE);
        if(!empty($posts)){
            echo form_open('admin/saccos/action', ' id="form"  class="form-horizontal"'); 
                if ( ! empty($pagination['links'])):
                    echo '
                    <div class="row col-md-12">
                        <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Bank loans </p>';
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links']; 
                    echo '</div></div>';
                endif; 
                echo ' 
                <table class="table m-table m-table--head-separator-primary">
                    <thead>
                        <tr>
                            <th>
                                #
                            </th>
                            <th>
                                '.translate('Details').'
                            </th>
                            <th class="text-right">
                                '.translate('Loan Amount').'
                            </th>
                            <th class="text-right">
                               '.translate('Payable').'
                            </th>
                            <th class="text-right">
                                '.translate('Balance').' ('.$this->group_currency.')
                            </th>
                            <th>
                                '.translate('Actions').'
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                        $i = $this->uri->segment(5, 0); 
                            foreach($posts as $post):
                            echo '
                            <tr>
                                <td>'.($i+1).'</td>
                                <td> 
                                    <strong> '.translate('Description').' : </strong> '.$post->description.' <br/>
                                    <strong> '.translate('Loan Duration').' : </strong> '.timestamp_to_date($post->loan_start_date).' to '.timestamp_to_date($post->loan_end_date).'<br/>
                                    <strong> '.translate('Loan Disbursement Receipient Account').' : </strong> '.$account_options[$post->account_id].' <br/>
                                    <strong> '.translate('Loan Repayment Status').' : </strong>';
                                    if($post->active){
                                        if($post->is_fully_paid){
                                            echo "<span class='label label-success'>".translate('Paid')."</span>";
                                        }else{
                                            echo "<span class='label label-primary'>".translate('In progress')."</span>";
                                        }
                                    }else{
                                        echo "<span class='label label-danger'>".translate('Voided')."</span>";
                                    }
                                echo '
                                </td>
                                <td class="text-right">
                                    '.number_to_currency($post->amount_loaned).'
                                </td>
                                <td class="text-right">
                                    '.number_to_currency($post->total_loan_amount_payable).'
                                </td>
                                <td class="text-right">
                                    '.number_to_currency($post->loan_balance).'
                                </td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <a href="'.site_url('group/bank_loans/edit/'.$post->id).'" class="btn btn-sm btn-primary m-btn  m-btn m-btn--icon generate_pdf_link">
                                            <span>
                                                <i class="fa fa-edit"></i>
                                                <span>
                                                    '.translate('Edit').'                                                               
                                                </span>
                                            </span>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split more_actions_toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="sr-only">More actions..</span>
                                        </button>
                                        <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(120px, 31px, 0px);">
                                           
                                            <a class="dropdown-item" href="'.site_url('group/bank_loans/statement/'.$post->id).'">
                                                '.translate('Statement').'
                                            </a>
                                            <a class="dropdown-item view_transaction_alert_link" href="'.site_url('group/bank_loans/record_repayment/'.$post->id).'">
                                                '.translate('Record Payment').' 
                                            </a>

                                            <a class="dropdown-item confirmation_link" href="'.site_url('group/bank_loans/void/'.$post->id).'">
                                                '.translate('Void').'
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>';
                            $i++;
                            endforeach;
                        echo '
                    </tbody>
                </table>
                <div class="clearfix"></div>
                <div class="row col-md-12">';
            if( ! empty($pagination['links'])): 
                echo $pagination['links']; 
            endif; 
            echo '
            </div>
            <div class="clearfix"></div>';
            echo form_close();
        }else{

            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'.translate('Sorry').'!</strong> '.translate('No Bank loans to display').'.
                </div>';
        }
    }

}