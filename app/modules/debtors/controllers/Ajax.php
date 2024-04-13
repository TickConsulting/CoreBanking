<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
	
	protected $data = array();

    protected $loan_validation_rules=array(
        array(
            'field' =>  'debtor_id',
            'label' =>  'Debtor',
            'rules' =>  'xss_clean|trim|required|numeric'
        ),
        array(
            'field' =>  'disbursement_date',
            'label' =>  'Disbursement Date',
            'rules' =>  'xss_clean|trim|required|date'
        ),
        array(
            'field' =>  'loan_amount',
            'label' =>  'Loan Amount',
            'rules' =>  'xss_clean|trim|required|currency'
        ),
        array(
            'field' =>  'repayment_period',
            'label' =>  'Loan Repayment Period',
            'rules' =>  'xss_clean|trim|required|numeric'
        ),
        array(
            'field' =>  'interest_rate',
            'label' =>  'Loan Interest Rate',
            'rules' =>  'xss_clean|trim|numeric'
        ),
        array(
            'field' =>  'interest_type',
            'label' =>  'Loan Interest Type',
            'rules' =>  'xss_clean|trim|numeric'
        ),
        array(
            'field' =>  'account_id',
            'label' =>  'Loan Disbursing Account',
            'rules' =>  'xss_clean|trim|required'
        ),
        array(
            'field' =>  'grace_period',
            'label' =>  'Loan Grace Period',
            'rules' =>  'xss_clean|trim|required'
        ),
        array(
            'field' =>  'enable_loan_fines',
            'label' =>  'Enable Late Loan Payment Fines',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'loan_fine_type',
            'label' =>  'Loan Fine Type',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'enable_outstanding_loan_balance_fines',
            'label' =>  'Enable Fines for Outstanding Balances',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'enable_loan_processing_fee',
            'label' =>  'Enable Loan Processing',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'enable_loan_guarantors',
            'label' =>  "Enable Loan Guarantors",
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'enable_loan_fine_deferment',
            'label' =>  'Enable Loan Deferment',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'sms_notifications_enabled',
            'label' =>  'Enable SMS Notifications',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'email_notifications_enabled',
            'label' =>  'Enable Email Notifications',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'fixed_fine_amount',
            'label' =>  'Fixed Fine Amount',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'fixed_amount_fine_frequency',
            'label' =>  'Fixed Amount Fine Frequecy',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'fixed_amount_fine_frequency_on',
            'label' =>  'Fixed Amount Fine Frequecy On',
            'rules' =>  'xss_clean|trim'  
        ),
        array(
            'field' =>  'percentage_fine_rate',
            'label' =>  'Percentage Fine Rate',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'percentage_fine_frequency',
            'label' =>  'Percentage Fine Frequecy',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'percentage_fine_on',
            'label' =>  'Percentage Fine On',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'one_off_fine_type',
            'label' =>  'One Off Fine Type',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'one_off_fixed_amount',
            'label' =>  'One Off Fine Amount',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'one_off_percentage_rate',
            'label' =>  'One Off Percentage Rate',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'one_off_percentage_rate_on',
            'label' =>  'One Off Percentage Rate On',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_fine_type',
            'label' =>  'Outstanding Loan Balance Fine Type',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_fine_fixed_amount',
            'label' =>  'Outstanding Loan Balance Fine Fixed Amount',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_fixed_fine_frequency',
            'label' =>  'Outstanding Loan Balance Fine Fixed Amount Frequecy',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_percentage_fine_on',
            'label' =>  'Outstanding Loan Balance Percentage Fine On',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_percentage_fine_frequency',
            'label' =>  'Outstanding Loan Balance Percentage Fine Frequency',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_percentage_fine_rate',
            'label' =>  'Outstanding Loan Balance Percentage Fine Rate',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_fine_one_off_amount',
            'label' =>  'Outstanding Loan Balance Fine One Off Amount',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'loan_processing_fee_type',
            'label' =>  'Loan Processing Fee Type',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'loan_processing_fee_fixed_amount',
            'label' =>  'Loan Processing Fee Fixed Amount',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'loan_processing_fee_percentage_rate',
            'label' =>  'Loan Processing Fee Fixed Percentage Rate',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'loan_processing_fee_percentage_charged_on',
            'label' =>  'Loan Processing Fee Fixed Percentage Charged On',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' => 'loan_interest_rate_per',
            'label' => 'Loan Interest Rate Per',
            'rules' => 'xss_clean|required|numeric|trim',
        ),
        array(
            'field' =>  'custom_interest_procedure',
            'label' =>  'Custom Procedure',
            'rules' => 'xss_clean',
        ),
        array(
            'field' => 'enable_reducing_balance_installment_recalculation',
            'label' => 'Enable Reducing Balance Recalulation on Early Installment Repayment',
            'rules' => 'xss_clean|trim|numeric',
        ),
    );

    protected  $validation_rules = array(
        array(
            'field' =>  'name',
            'label' =>  'Debtor Name',
            'rules' =>  'trim|required|min_length[8]',
        ),array(
            'field' =>  'phone',
            'label' =>  'Phone Number',
            'rules' =>  'trim|required|valid_phone',
        ),array(
            'field' =>  'email',
            'label' =>  'Email address',
            'rules' =>  'trim|valid_email',
        ),array(
            'field' =>  'description',
            'label' =>  'Debtor Description',
            'rules' =>  'trim',
        )
    );
    
    function __construct(){
        parent::__construct();
        $this->load->model('debtors_m');

    }

    public function list_debtors(){
        $filter_parameters = array('id'=>$this->input->get('debtor_id'));
        $total_rows = $this->debtors_m->count_group_debtors($this->group->id,$filter_parameters);
        $pagination = create_pagination('group/deposits/listing/pages',$total_rows,50,5,TRUE);
        $posts = $this->debtors_m->limit($pagination['limit'])->get_all($this->group->id,$filter_parameters);
        if(!empty($posts)){
        echo form_open('group/debtors/action', ' id="form"  class="form-horizontal"');
        if(!empty($pagination['links'])):
            echo '
            <div class="row col-md-12">
                <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Debtor Loans</p>';
                echo '<div class ="top-bar-pagination">';
                echo $pagination['links']; 
                echo '</div></div>';
                endif; 
            echo '  
            <table class="table table-condensed table-striped table-hover table-header-fixed table-searchable">
                <thead>
                    <tr>
                        <th width=\'2%\'>
                             <input type="checkbox" name="check" value="all" class="check_all">
                        </th>
                        <th>
                            Details
                        </th>
                        <th>
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>';
                    $i = $this->uri->segment(5, 0); $i++; foreach($posts as $post):
                    echo '
                        <tr>
                            <td><input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" /></td>
                            <td>
                                #'.($i++).' : <strong>Debtor Name: </strong> '.$post->name.'<br/>
                            ';
                            echo '
                            <br/>';
                                    echo '<strong>Phone Number: </strong>'.$post->phone.'<br/>';
                                    echo '<strong>Email Address: </strong>'.$post->email.'<br/>';

                                    echo "<strong>Description</strong><hr/>";
                                    echo $post->description.'<br/>';
                            echo '
                            </td> 
                            <td>';

                                echo '<a href="'.site_url('group/debtors/edit/'.$post->id).'" class="btn btn-xs default">
                                    <i class="fa fa-pencil"></i> Edit &nbsp;&nbsp; 
                                </a>';
                                if($post->active){
                                    echo '<a href="'.site_url('group/debtors/suspend/'.$post->id).'" class="btn btn-xs red">
                                        <i class="fa fa-trash-o"></i> Suspend &nbsp;&nbsp; 
                                    </a>';
                                }else{
                                    echo '<a href="'.site_url('group/debtors/activate/'.$post->id).'" class="btn btn-xs blue">
                                        <i class="fa fa-eye"></i> Activate &nbsp;&nbsp; 
                                    </a>';
                                }
                            echo '
                            </td>
                        </tr>';
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
            if($posts):
                echo '<button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_suspend\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-trash-o\'></i> Bulk Suspend</button>
                &nbsp;
                &nbsp;
                &nbsp;
            ';
            echo '<button class="btn btn-sm btn-primary confirmation_bulk_action" name=\'btnAction\' value=\'bulk_activate\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-eye\'></i> Bulk Activate</button>';
            endif;
            echo form_close();
        }else{
            echo '
            <div class="alert alert-info">
                <h4 class="block">Information! No records to display</h4>
                <p>
                    No debtors to display.
                </p>
            </div>';
        } 
    }

    public function create(){
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $name = $this->input->post('name');
            $phone = $this->input->post('phone');
            $email = $this->input->post('email');
            $description = $this->input->post('description');
            if($debtor_id = $this->debtors_m->insert(array(
                    'group_id' => $this->group->id,
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'active' => 1,
                    'description' => $description,
                    'created_on' => time(),
                    'created_by' => $this->user->id,
                ))){
                if($debtor = $this->debtors_m->get($debtor_id)){
                    $response = array(
                        'status' => 1,
                        'message' => 'Debtor successfully created: ',
                        'debtor'=>$debtor
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find any debtor',
                    );
                }
            }else{
                 $response = array(
                    'status' => 0,
                    'message' => 'Could not create debtor',
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
        echo json_encode($response);
    }

    public function create_loan(){
        $this->form_validation->set_rules($this->loan_validation_rules);
        if($this->form_validation->run()){
            $debtor_id =  $this->input->post('debtor_id');
            $group_id  =  $this->group->id;

            $enable_loan_fines = $this->input->post('enable_loan_fines');
            $enable_outstanding_loan_balance_fines = $this->input->post('enable_outstanding_loan_balance_fines');
            $enable_loan_processing_fee = $this->input->post('enable_loan_processing_fee');
            $custom_interest_procedure = $this->input->post('custom_interest_procedure');

            $loan_details = array(
                'disbursement_date' => $this->input->post('disbursement_date'),
                'loan_amount'   =>  $this->input->post('loan_amount'),
                'account_id'    =>  $this->input->post('account_id'),
                'repayment_period'  =>  $this->input->post('repayment_period'),
                'interest_rate' =>  $this->input->post('interest_rate'),
                'loan_interest_rate_per' =>  $this->input->post('loan_interest_rate_per'),
                'interest_type' =>  $this->input->post('interest_type'),
                'custom_interest_procedure'=>$custom_interest_procedure,
                'grace_period'  =>  $this->input->post('grace_period'),
                'sms_notifications_enabled' =>  $this->input->post('sms_notifications_enabled'),
                'sms_template'  =>  $this->input->post('sms_template'),
                'email_notifications_enabled' =>  $this->input->post('email_notifications_enabled'),
                'enable_loan_fines' =>  $enable_loan_fines,
                'enable_outstanding_loan_balance_fines'=>$enable_outstanding_loan_balance_fines,
                'enable_loan_processing_fee' => $enable_loan_processing_fee,
                'enable_loan_fine_deferment' => $this->input->post('enable_loan_fine_deferment'),
                'enable_loan_guarantors' => $this->input->post('enable_loan_guarantors'),
                'enable_reducing_balance_installment_recalculation' => $this->input->post('enable_reducing_balance_installment_recalculation')?1:0,
                'active'    =>  1,
                'created_by'    =>  $this->user->id,
                'created_on'    =>  time(),
            );

            if($enable_loan_fines)
            {
                $loan_fine_type    =  $this->input->post('loan_fine_type');
                $loan_details = $loan_details+array('loan_fine_type'=>$loan_fine_type);
                if($loan_fine_type==1)
                {
                    $loan_details = $loan_details + array(
                        'fixed_fine_amount' =>  $this->input->post('fixed_fine_amount'),
                        'fixed_amount_fine_frequency'   =>  $this->input->post('fixed_amount_fine_frequency'),
                        'fixed_amount_fine_frequency_on' => $this->input->post('fixed_amount_fine_frequency_on'),
                    );
                }else if($loan_fine_type == 2){
                    $loan_details = $loan_details + array(
                            'percentage_fine_rate'  =>  $this->input->post('percentage_fine_rate'),
                            'percentage_fine_frequency' =>  $this->input->post('percentage_fine_frequency'),
                            'percentage_fine_on'    =>  $this->input->post('percentage_fine_on'),
                        );
                }else if($loan_fine_type==3){
                    $one_off_fine_type     =  $this->input->post('one_off_fine_type');
                    $loan_details = $loan_details+array('one_off_fine_type'=>$one_off_fine_type);
                    if($one_off_fine_type==1){
                        $loan_details = $loan_details + array('one_off_fixed_amount'  =>  $this->input->post('one_off_fixed_amount'));
                    }else if($one_off_fine_type==2){
                        $loan_details = $loan_details + array(
                                'one_off_percentage_rate'   =>  $this->input->post('one_off_percentage_rate'),
                                'one_off_percentage_rate_on'    =>  $this->input->post('one_off_percentage_rate_on'),
                            );
                    }
                }
            }
            if($enable_outstanding_loan_balance_fines)
            {
                $outstanding_loan_balance_fine_type = $this->input->post('outstanding_loan_balance_fine_type');

                $loan_details = $loan_details+array('outstanding_loan_balance_fine_type'=>$outstanding_loan_balance_fine_type);
                if($outstanding_loan_balance_fine_type==1){
                    $loan_details = $loan_details + array(
                        'outstanding_loan_balance_fine_type'    =>  $this->input->post('outstanding_loan_balance_fine_type'),
                        'outstanding_loan_balance_fine_fixed_amount'   =>$this->input->post('outstanding_loan_balance_fine_fixed_amount'),
                        'outstanding_loan_balance_fixed_fine_frequency'=>$this->input->post('outstanding_loan_balance_fixed_fine_frequency'),
                    );
                }else if($outstanding_loan_balance_fine_type==2){
                    $loan_details = $loan_details + array(
                            'outstanding_loan_balance_percentage_fine_rate'=>$this->input->post('outstanding_loan_balance_percentage_fine_rate'),
                            'outstanding_loan_balance_percentage_fine_frequency'=>$this->input->post('outstanding_loan_balance_percentage_fine_frequency'),
                            'outstanding_loan_balance_percentage_fine_on'=>$this->input->post('outstanding_loan_balance_percentage_fine_on'),
                        );
                }else if($outstanding_loan_balance_fine_type==3){
                    $loan_details = $loan_details + array(
                            'outstanding_loan_balance_fine_one_off_amount'=>$this->input->post('outstanding_loan_balance_fine_one_off_amount'), 
                        );
                }
            }
            if($enable_loan_processing_fee)
            {
                $loan_processing_fee_type  =  $this->input->post('loan_processing_fee_type');

                $loan_details = $loan_details + array('loan_processing_fee_type'=>$loan_processing_fee_type);
                if($loan_processing_fee_type==1){
                    $loan_details = $loan_details + array('loan_processing_fee_fixed_amount'  =>  $this->input->post('loan_processing_fee_fixed_amount'));

                }else if($loan_processing_fee_type==2){
                    $loan_details = $loan_details + array(
                        'loan_processing_fee_percentage_rate'=>$this->input->post('loan_processing_fee_percentage_rate'),
                        'loan_processing_fee_percentage_charged_on' =>  $this->input->post('loan_processing_fee_percentage_charged_on'),);
                }
            }
            $guarantor_id = $this->input->post('guarantor_id');
            $guaranteed_amount = $this->input->post('guaranteed_amount');
            $guarantor_comment = $this->input->post('guarantor_comment');
            $guarantors=array(
                        'guarantor_id' => $guarantor_id,
                        'guaranteed_amount' => $guaranteed_amount,
                        'guarantor_comment' => $guarantor_comment
                    );
            $custom_loan_values = array();
            if($this->input->post('custom_interest_procedure')==1){
                $custom_loan_values = array(
                    'date_from' =>  $this->input->post('interest_rate_date_from'),
                    'date_to' =>  $this->input->post('interest_rate_date_to'),
                    'rate' =>  $this->input->post('custom_interest_rate'),
                );
            }else if($this->input->post('custom_interest_procedure')==2){
                $custom_loan_values = array(
                        'payment_date' =>  $this->input->post('custom_payment_date'),
                        'amount_payable' =>  $this->input->post('custom_amount_payable'),
                    );
            }

            if($id = $this->loan->create_debtor_loan($debtor_id,$group_id,$loan_details,$custom_loan_values,$custom_interest_procedure,$guarantors)){
                //send notification, email and message
                if($this->input->post('enable_loan_guarantors')){
                    if($guarantor_id){
                        for ($i=0; $i < count($guarantor_id); $i++) { 
                            if(isset($guarantor_id[$i])){
                                $to_user = $this->members_m->get_user_by_member_id($guarantor_id[$i]);
                                $this->notifications->create(
                                    'Guarantor',
                                    'Dear '.$this->group_member_options[$guarantor_id[$i]].', you have been choosen to guarantee '.$this->group_debtor_options[$debtor_id].' '.$this->group_currency.' '.number_to_currency($guaranteed_amount[$i]).' - '.$this->group->name,
                                    $this->user,
                                    $this->member->id,
                                    $to_user->id,
                                    $guarantor_id[$i],
                                    $this->group->id,
                                    'View Loan',
                                    'group/debtor_loan_listing',
                                    15);
                            }
                        }
                    }
                }
                $response = array(
                    'status' => 1,
                    'message' => 'Debtor loan successfuly created.',
                    'refer' => site_url('group/debtors/debtor_loan_listing'),
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Error occured creating debtor loan',
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
        echo json_encode($response);
    }

    public function edit_loan($id=0){
        $post = new StdClass();
        $response = array();
        $loan_id = $this->input->post('id')?:$id;
        if($loan_id){
            $post = $this->debtors_m->get_loan_and_debtor($loan_id);
            if($post){
                if($post->active){
                    $this->form_validation->set_rules($this->loan_validation_rules);
                    if($this->form_validation->run()){
                        $debtor_id =  $this->input->post('debtor_id');
                        $group_id  =  $this->group->id;

                        $enable_loan_fines = $this->input->post('enable_loan_fines');
                        $enable_outstanding_loan_balance_fines = $this->input->post('enable_outstanding_loan_balance_fines');
                        $enable_loan_processing_fee = $this->input->post('enable_loan_processing_fee');
                        $custom_interest_procedure = $this->input->post('custom_interest_procedure');

                        $loan_details = array(
                            'disbursement_date' => $this->input->post('disbursement_date'),
                            'loan_amount'   =>  $this->input->post('loan_amount'),
                            'account_id'    =>  $this->input->post('account_id'),
                            'repayment_period'  =>  $this->input->post('repayment_period'),
                            'interest_rate' =>  $this->input->post('interest_rate'),
                            'loan_interest_rate_per' =>  $this->input->post('loan_interest_rate_per'),
                            'interest_type' =>  $this->input->post('interest_type'),
                            'custom_interest_procedure'=>$custom_interest_procedure,
                            'grace_period'  =>  $this->input->post('grace_period'),
                            'sms_notifications_enabled' =>  $this->input->post('sms_notifications_enabled'),
                            'sms_template'  =>  $this->input->post('sms_template'),
                            'email_notifications_enabled' =>  $this->input->post('email_notifications_enabled'),
                            'enable_loan_fines' =>  $enable_loan_fines,
                            'enable_outstanding_loan_balance_fines'=>$enable_outstanding_loan_balance_fines,
                            'enable_loan_processing_fee' => $enable_loan_processing_fee,
                            'enable_loan_fine_deferment' => $this->input->post('enable_loan_fine_deferment'),
                            'enable_loan_guarantors' => $this->input->post('enable_loan_guarantors'),
                            'enable_reducing_balance_installment_recalculation' => $this->input->post('enable_reducing_balance_installment_recalculation')?1:0,
                            'active'    =>  1,
                            'modified_by'    =>  $this->user->id,
                            'modified_on'    =>  time(),
                            'is_edited' => 1,
                        );

                        if($enable_loan_fines){
                            $loan_fine_type    =  $this->input->post('loan_fine_type');
                            $loan_details = $loan_details+array('loan_fine_type'=>$loan_fine_type);
                            if($loan_fine_type==1)
                            {
                                $loan_details = $loan_details + array(
                                        'fixed_fine_amount' =>  $this->input->post('fixed_fine_amount'),
                                        'fixed_amount_fine_frequency'   =>  $this->input->post('fixed_amount_fine_frequency'),
                                        'fixed_amount_fine_frequency_on' => $this->input->post('fixed_amount_fine_frequency_on'),
                                    );
                            }else if($loan_fine_type == 2){
                                $loan_details = $loan_details + array(
                                        'percentage_fine_rate'  =>  $this->input->post('percentage_fine_rate'),
                                        'percentage_fine_frequency' =>  $this->input->post('percentage_fine_frequency'),
                                        'percentage_fine_on'    =>  $this->input->post('percentage_fine_on'),
                                    );
                            }else if($loan_fine_type==3){
                                $one_off_fine_type     =  $this->input->post('one_off_fine_type');
                                $loan_details = $loan_details+array('one_off_fine_type'=>$one_off_fine_type);
                                if($one_off_fine_type==1){
                                    $loan_details = $loan_details + array('one_off_fixed_amount'  =>  $this->input->post('one_off_fixed_amount'));
                                }else if($one_off_fine_type==2){
                                    $loan_details = $loan_details + array(
                                            'one_off_percentage_rate'   =>  $this->input->post('one_off_percentage_rate'),
                                            'one_off_percentage_rate_on'    =>  $this->input->post('one_off_percentage_rate_on'),
                                        );
                                }
                            }
                        }
                        if($enable_outstanding_loan_balance_fines){
                            $outstanding_loan_balance_fine_type = $this->input->post('outstanding_loan_balance_fine_type');

                            $loan_details = $loan_details+array('outstanding_loan_balance_fine_type'=>$outstanding_loan_balance_fine_type);
                            if($outstanding_loan_balance_fine_type==1){
                                $loan_details = $loan_details + array(
                                    'outstanding_loan_balance_fine_type'    =>  $this->input->post('outstanding_loan_balance_fine_type'),
                                    'outstanding_loan_balance_fine_fixed_amount'   =>$this->input->post('outstanding_loan_balance_fine_fixed_amount'),
                                    'outstanding_loan_balance_fixed_fine_frequency'=>$this->input->post('outstanding_loan_balance_fixed_fine_frequency'),
                                );
                            }else if($outstanding_loan_balance_fine_type==2){
                                $loan_details = $loan_details + array(
                                        'outstanding_loan_balance_percentage_fine_rate'=>$this->input->post('outstanding_loan_balance_percentage_fine_rate'),
                                        'outstanding_loan_balance_percentage_fine_frequency'=>$this->input->post('outstanding_loan_balance_percentage_fine_frequency'),
                                        'outstanding_loan_balance_percentage_fine_on'=>$this->input->post('outstanding_loan_balance_percentage_fine_on'),
                                    );
                            }else if($outstanding_loan_balance_fine_type==3){
                                $loan_details = $loan_details + array(
                                        'outstanding_loan_balance_fine_one_off_amount'=>$this->input->post('outstanding_loan_balance_fine_one_off_amount'), 
                                    );
                            }
                        }
                        if($enable_loan_processing_fee){
                            $loan_processing_fee_type  =  $this->input->post('loan_processing_fee_type');

                            $loan_details = $loan_details + array('loan_processing_fee_type'=>$loan_processing_fee_type);
                            if($loan_processing_fee_type==1){
                                $loan_details = $loan_details + array('loan_processing_fee_fixed_amount'  =>  $this->input->post('loan_processing_fee_fixed_amount'));

                            }else if($loan_processing_fee_type==2){
                                $loan_details = $loan_details + array(
                                    'loan_processing_fee_percentage_rate'=>$this->input->post('loan_processing_fee_percentage_rate'),
                                    'loan_processing_fee_percentage_charged_on' =>  $this->input->post('loan_processing_fee_percentage_charged_on'),);
                            }
                        }
                        $guarantor_id = $this->input->post('guarantor_id');
                        $guaranteed_amount = $this->input->post('guaranteed_amount');
                        $guarantor_comment = $this->input->post('guarantor_comment');
                        $guarantors=array(
                                    'guarantor_id' => $guarantor_id,
                                    'guaranteed_amount' => $guaranteed_amount,
                                    'guarantor_comment' => $guarantor_comment
                                );
                        $custom_loan_values = array();
                        if($this->input->post('custom_interest_procedure')==1){
                            $custom_loan_values = array(
                                'date_from' =>  $this->input->post('interest_rate_date_from'),
                                'date_to' =>  $this->input->post('interest_rate_date_to'),
                                'rate' =>  $this->input->post('custom_interest_rate'),
                            );
                        }else if($this->input->post('custom_interest_procedure')==2){
                            $custom_loan_values = array(
                                    'payment_date' =>  $this->input->post('custom_payment_date'),
                                    'amount_payable' =>  $this->input->post('custom_amount_payable'),
                                );
                        }

                        if($this->loan->edit_debtor_loan(
                                $post->id,
                                $debtor_id,
                                $group_id,
                                $loan_details,
                                $guarantors,
                                $custom_interest_procedure,
                                $custom_loan_values
                            )){
                            $response = array(
                                'status' => 1,
                                'message' => 'Success updated debtor loan.',
                                'refer' => site_url('group/debtors/debtor_loan_listing'),
                            ); 
                        }else{
                            $response = array(
                                'status' =>0,
                                'message' => 'Error occured while editing debtor loan',
                               
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
                        'status' => 100,
                        'message' => 'Loan does not exist thus cannot edit',
                        'refer' => site_url('group/debtors/debtor_loan_listing'),
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'The loan does not exist',
                    'validation_errors' => '',
                );  
            }

        }else{
            $response = array(
                'status' => 0,
                'message' => 'Loan id not provided.',
                'validation_errors' => '',
            );
        }
        echo json_encode($response);
    }

    function ajax_get_debtor_loans_listing(){
        $filter_parameters = array(
            'debtor_id' => $this->input->get('debtor_id'),
            'to' => strtotime($this->input->get('to')),
            'from' => strtotime($this->input->get('from')),
        );
        $total_rows = $this->debtors_m->count_group_debtor_loans($this->group->id,$filter_parameters);
        $pagination = create_pagination('group/deposits/listing/pages',$total_rows,50,5,TRUE);
        $posts = $this->debtors_m->limit($pagination['limit'])->get_all_loans($this->group->id,$filter_parameters);
        $accounts = $this->accounts_m->get_active_group_account_options(FALSE);
        $guarantors = $this->debtors_m->get_loan_guarantors_array();
        if(!empty($posts)){
        echo form_open('group/debtors/action', ' id="form"  class="form-horizontal"');
        if(!empty($pagination['links'])):
            echo '
            <div class="row col-md-12">
                <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Debtor Loans</p>';
                echo '<div class ="top-bar-pagination">';
                echo $pagination['links']; 
                echo '</div></div>';
                endif; 
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
                        <th width=\'2%\' nowrap>
                            #
                        </th>
                        <th nowrap>
                            Debtor Name
                        </th>
                        <th nowrap>
                            Amount ('.$this->group_currency.')
                        </th>
                        <th nowrap>
                            Disbursed on
                        </th>  
                        <th nowrap> 
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>';
                    $i = $this->uri->segment(5, 0); $i++; foreach($posts as $post):
                    echo '
                        <tr>
                            <td scope="row">
                                <label class="m-checkbox">
                                    <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" />
                                    <span></span>
                                </label>
                            </td>
                            <td>
                                '.($i++).'
                            </td>
                            <td >
                              '.$this->group_debtor_options[$post->debtor_id].'
                            </td>
                            <td  >
                                '.number_to_currency($post->loan_amount).'
                            </td> 
                            <td>
                                '.timestamp_to_date($post->disbursement_date).'
                            </td> 
                            <td nowrap class="text-left align-middle">
                                <a href="'.site_url('group/loans/view_installments/'.$post->id).'" class="btn btn-sm btn-primary m-btn m-btn--icon  action_button" id="'.$post->id.'" data-target="#deposit_receipt">
                                    <span>
                                        <i class="fa fa-list-alt"></i>
                                        <span>
                                            Installments &nbsp;&nbsp; 
                                        </span>
                                    </span>
                                </a>&nbsp;&nbsp;
                                <a href="javascript:;" class="btn btn-sm btn-primary m-btn m-btn--icon view_loan_statement action_button" id="'.$post->id.'" data-toggle="modal" data-target="#deposit_receipt" data-keyboard="false" data-backdrop="static">
                                    <span>
                                        <i class="la la-eye"></i>
                                        <span>
                                            More &nbsp;&nbsp; 
                                        </span>
                                    </span>
                                </a>
                                &nbsp;&nbsp;&nbsp;
                                <br/>';
                                if($post->is_fully_paid){
                                }else{
                                    echo
                                    '<a href="'.site_url('group/deposits/record_debtor_loan_repayments/?loan_id='.$post->id.'&debtor_id='.$post->debtor_id).'" class="btn btn-sm btn-primary m-btn m-btn--icon action_button mt-1" id="'.$post->id.'" data-target="#deposit_receipt">
                                        <span>
                                            <i class="mdi mdi-cash-multiple"></i>
                                            <span>
                                                Record Repayment &nbsp;&nbsp; 
                                            </span>
                                        </span>
                                    </a>&nbsp;&nbsp;';
                                }
                            echo '
                            </td>
                        </tr>';
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
            if($posts):
                echo '<button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_void\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-trash-o\'></i> Bulk Void</button>';
            endif;
            echo form_close();
        }else{
            echo '
            <div class="alert alert-info">
                <h4 class="block">'.translate('Information').'! '.translate('No records to display').'</h4>
                <p>
                    '.translate('No debtor loans to display').'.
                </p>
            </div>';
        } 
    }

    function get_loan_statement($id = 0){
        $posts = $this->debtors_m->get_loan_statement($id);
        if($id){
            $loan = $this->debtors_m->get_loan_and_debtor($id);
            if(!$loan){
                $this->session->set_flashdata('info','Sorry the loan does not exist');
                redirect('group/debtors/debtor_loan_listing');
            }
            $total_installment_payable = $this->debtors_m->get_total_installment_loan_payable($id);
            $total_fines = $this->debtors_m->get_total_loan_fines_payable($id);
            $total_paid = $this->debtors_m->get_loan_total_payments($id);
            $loan_balance =$this->debtors_m->get_loan_balance($id);
            $posts = $this->debtors_m->get_loan_statement($id);
            $this->data['loan'] = $loan;
            $this->data['posts'] = $posts;
            $this->data['total_installment_payable'] = $total_installment_payable;
            $this->data['total_fines'] = $total_fines;
            $this->data['total_paid'] = $total_paid;

            $lump_sum_remaining = $this->loan_invoices_m->get_loan_lump_sum_as_date($id);
            $accounts = $this->accounts_m->get_group_account_options(FALSE);
            $deposit_options =$this->transactions->deposit_method_options;
            $this->data['group'] = $this->group;
            $this->data['group_currency'] = $this->group_currency;
            $this->data['application_settings'] = $this->application_settings;
            $transfer_options = $this->loan->transfer_options;
            $loan_type_options = $this->loan_types_m->get_options();
            $response = array(
                'status' => 1,
                'message' => 'Loan Details',
                'data' => array(
                    'loan' => $loan,
                    'total_installment_payable'=>$total_installment_payable,
                    'total_fines'=>$total_fines,
                    'total_paid'=>$total_paid,
                    'loan_balance'=>$loan_balance,
                    'posts'=>$posts,
                    'lump_sum_remaining'=>$lump_sum_remaining,
                    'loan_type_options' => $loan_type_options,
                    'transfer_options' => $transfer_options,
                    'deposit_options' => $deposit_options,
                    'accounts' => $accounts,
                    'interest_types' => $this->loan->interest_types,
                    'loan_interest_rate_per' => $this->loan->loan_interest_rate_per,
                    'late_payments_fine_frequency' => $this->loan->late_payments_fine_frequency,
                    'percentage_fine_on' => $this->loan->percentage_fine_on,
                    'loan_processing_fee_percentage_rate' => $this->loan->loan_processing_fee_percentage_charged_on,
                ),
            );

        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not get the selected loan details',
            );
        }
        echo json_encode($response);
    }





}