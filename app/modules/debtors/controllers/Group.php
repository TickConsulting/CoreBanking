<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller
{
    protected $data = array();

	protected $loan_validation_rules=array(
        array(
            'field' =>  'debtor_id',
            'label' =>  'Debtor',
            'rules' =>  'trim|required|numeric'
        ),
        array(
            'field' =>  'disbursement_date',
            'label' =>  'Disbursement Date',
            'rules' =>  'trim|required|date'
        ),
        array(
            'field' =>  'loan_amount',
            'label' =>  'Loan Amount',
            'rules' =>  'trim|required|currency'
        ),
        array(
            'field' =>  'repayment_period',
            'label' =>  'Loan Repayment Period',
            'rules' =>  'trim|required|numeric'
        ),
        array(
            'field' =>  'interest_rate',
            'label' =>  'Loan Interest Rate',
            'rules' =>  'trim|numeric'
        ),
        array(
            'field' =>  'interest_type',
            'label' =>  'Loan Interest Type',
            'rules' =>  'trim|numeric'
        ),
        array(
            'field' =>  'account_id',
            'label' =>  'Loan Disbursing Account',
            'rules' =>  'trim|required'
        ),
        array(
            'field' =>  'grace_period',
            'label' =>  'Loan Grace Period',
            'rules' =>  'trim|required'
        ),
        array(
            'field' =>  'enable_loan_fines',
            'label' =>  'Enable Late Loan Payment Fines',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'loan_fine_type',
            'label' =>  'Loan Fine Type',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'enable_outstanding_loan_balance_fines',
            'label' =>  'Enable Fines for Outstanding Balances',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'enable_loan_processing_fee',
            'label' =>  'Enable Loan Processing',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'enable_loan_guarantors',
            'label' =>  "Enable Loan Guarantors",
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'enable_loan_fine_deferment',
            'label' =>  'Enable Loan Deferment',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'sms_notifications_enabled',
            'label' =>  'Enable SMS Notifications',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'email_notifications_enabled',
            'label' =>  'Enable Email Notifications',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'fixed_fine_amount',
            'label' =>  'Fixed Fine Amount',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'fixed_amount_fine_frequency',
            'label' =>  'Fixed Amount Fine Frequecy',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'fixed_amount_fine_frequency_on',
            'label' =>  'Fixed Amount Fine Frequecy On',
            'rules' =>  'trim'  
        ),
        array(
            'field' =>  'percentage_fine_rate',
            'label' =>  'Percentage Fine Rate',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'percentage_fine_frequency',
            'label' =>  'Percentage Fine Frequecy',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'percentage_fine_on',
            'label' =>  'Percentage Fine On',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'one_off_fine_type',
            'label' =>  'One Off Fine Type',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'one_off_fixed_amount',
            'label' =>  'One Off Fine Amount',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'one_off_percentage_rate',
            'label' =>  'One Off Percentage Rate',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'one_off_percentage_rate_on',
            'label' =>  'One Off Percentage Rate On',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_fine_type',
            'label' =>  'Outstanding Loan Balance Fine Type',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_fine_fixed_amount',
            'label' =>  'Outstanding Loan Balance Fine Fixed Amount',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_fixed_fine_frequency',
            'label' =>  'Outstanding Loan Balance Fine Fixed Amount Frequecy',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_percentage_fine_on',
            'label' =>  'Outstanding Loan Balance Percentage Fine On',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_percentage_fine_frequency',
            'label' =>  'Outstanding Loan Balance Percentage Fine Frequency',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_percentage_fine_rate',
            'label' =>  'Outstanding Loan Balance Percentage Fine Rate',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'outstanding_loan_balance_fine_one_off_amount',
            'label' =>  'Outstanding Loan Balance Fine One Off Amount',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'loan_processing_fee_type',
            'label' =>  'Loan Processing Fee Type',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'loan_processing_fee_fixed_amount',
            'label' =>  'Loan Processing Fee Fixed Amount',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'loan_processing_fee_percentage_rate',
            'label' =>  'Loan Processing Fee Fixed Percentage Rate',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'loan_processing_fee_percentage_charged_on',
            'label' =>  'Loan Processing Fee Fixed Percentage Charged On',
            'rules' =>  'trim'
        ),
        array(
            'field' => 'loan_interest_rate_per',
            'label' => 'Loan Interest Rate Per',
            'rules' => 'required|numeric|trim',
        ),
        array(
            'field' =>  'custom_interest_procedure',
            'label' =>  'Custom Procedure',
            'rules' => '',
        ),
        array(
            'field' => 'enable_reducing_balance_installment_recalculation',
            'label' => 'Enable Reducing Balance Recalulation on Early Installment Repayment',
            'rules' => 'trim|numeric',
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
        $this->load->library('loan');
        $this->load->library('notifications');

        $this->data['interest_types'] = $this->loan->interest_types;
        $this->data['late_loan_payment_fine_types'] = $this->loan->late_loan_payment_fine_types;
        $this->data['late_payments_fine_frequency'] = $this->loan->late_payments_fine_frequency;
        $this->data['percentage_fine_on'] = $this->loan->percentage_fine_on;
        $this->data['one_off_fine_types'] = $this->loan->one_off_fine_types;
        $this->data['one_off_percentage_rate_on'] = $this->loan->one_off_percentage_rate_on;
        $this->data['loan_processing_fee_types'] =  $this->loan->loan_processing_fee_types;
        $this->data['sms_template_default'] = $this->loan->sms_template_default;
        $this->data['loan_grace_periods'] = $this->loan->loan_grace_periods;
        $this->data['loan_processing_fee_percentage_charged_on'] = $this->loan->loan_processing_fee_percentage_charged_on;
        $this->data['loan_days'] = $this->loan->loan_days;
        $this->data['loan_interest_rate_per'] = $this->loan->loan_interest_rate_per;
        $this->data['fixed_amount_fine_frequency_on'] = $this->loan->fixed_amount_fine_frequency_on;
        $this->accounts = $this->accounts_m->get_group_account_options();
        $this->active_accounts = $this->accounts_m->get_active_group_account_options();
        $this->data['accounts'] = $this->accounts;
        $this->data['active_accounts'] = $this->active_accounts;

        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
    }

    function index(){

    }

    public function create(){
        $post = new StdClass();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $data = array(
                'name' => $this->input->post('name'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email'),
                'description' => $this->input->post('description'),
                'created_by' => $this->user->id,
                'created_on' => time(),
                'group_id' => $this->group->id,
                'active' => 1,
            );
            if($this->debtors_m->insert($data)){
                $this->session->set_flashdata('success','Successfuly added a new debtor');
            }else{
                $this->session->set_flashdata('error','Error occured adding new debtor');
            }
            redirect('group/debtors/listing');
        }
        foreach ($this->validation_rules as $key => $field) {
            $post->$field['field'] = set_value($field['field']);
        }
        $this->data['post'] = $post;
        $this->template->title('Add New Debtor')->build('group/add_debtor',$this->data);
    }

    public function listing(){
        if($this->input->get('generate_excel') == 1){
            $filter_parameters = array('id'=>$this->input->get('debtor_id'));
            $this->data['posts'] = $this->debtors_m->get_all($this->group->id,$filter_parameters);
            $this->data['group_currency'] = $this->group_currency;
            $this->data['group'] = $this->group;
            $this->data['debtor_options'] = $this->group_debtor_options;
            $this->data['filter_parameters'] = $filter_parameters;

            $json_file = json_encode($this->data);
            //print_r($json_file);die;
            print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/debtors/listing',$this->group->name.' List of Debtors'));
            die;
        }
        $this->template->title('List Debtors')->build('group/listing',$this->data);
    }

    public function edit($id = 0){
        $id OR redirect('group/debtors/listing');

        $post = $this->debtors_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, debtor not available');
            redirect('group/debtors/listing');
        }
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $data = array(
                'name' => $this->input->post('name'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email'),
                'description' => $this->input->post('description'),
                'modified_by' => $this->user->id,
                'modified_on' => time(),
            );
            if($this->debtors_m->update($id,$data)){
                $this->session->set_flashdata('success','Successfuly updated debtor');
            }else{
                $this->session->set_flashdata('error','Error occured updating debtor');
            }
            redirect('group/debtors/listing');
        }else{
            foreach(array_keys($this->validation_rules) as $field){
                if (isset($_POST[$field])){
                    $post->$field = $this->form_validation->$field;
                }
            }
        }
        $this->data['post'] = $post;
        $this->template->title('Edit Debtor')->build('group/add_debtor',$this->data);
    }

    public function ajax_add_new_debtor(){
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
                    $this->session->unset_userdata('success_feedback');
                    echo json_encode($debtor);
                }else{
                    echo "Could not find any debtor";
                }
            }else{
                echo "Could not add debtor to the group";
            }
        }else{
            echo validation_errors();
        }
    }


    function loan_validation_function(){
        if($this->input->post('sms_notifications_enabled')){
            $this->loan_validation_rules[] = array(
                    'field' =>  'sms_template',
                    'label' =>  'Loan Installment SMS Template',
                    'rules' =>  'required|trim'
                );
        }

        if($this->input->post('enable_loan_fines')){
            $this->loan_validation_rules[] = array(
                    'field' =>  'loan_fine_type',
                    'label' =>  'Late Loan Fine Type',
                    'rules' =>  'required|trim'
                );

            if($this->input->post('loan_fine_type')==1){
                $this->loan_validation_rules[] = array(
                        'field' =>  'fixed_fine_amount',
                        'label' =>  'Fixed Fine Amount',
                        'rules' =>  'required|trim|currency'
                    );
                $this->loan_validation_rules[] = array(
                        'field' =>  'fixed_amount_fine_frequency',
                        'label' =>  'Fixed Amount Fine Frequency',
                        'rules' =>  'required|trim'
                    );
                $this->loan_validation_rules[] = array(
                        'field' =>  'fixed_amount_fine_frequency_on',
                        'label' =>  'Fixed Amount Fine Frequency On',
                        'rules' =>  'required|trim'
                    );
            }
            if($this->input->post('loan_fine_type')==2){
                $this->loan_validation_rules[] = array(
                    'field' =>  'percentage_fine_rate',
                    'label' =>  'Percentage(%) Fine Rate',
                    'rules' =>  'required|trim|numeric'
                );

                $this->loan_validation_rules[] = array(
                    'field' =>  'percentage_fine_frequency',
                    'label' =>  'Percentage Fine Frequency',
                    'rules' =>  'required|trim'
                );

                $this->loan_validation_rules[] = array(
                    'field' =>  'percentage_fine_on',
                    'label' =>  'Percentage Fine Frequency on',
                    'rules' =>  'required|trim'
                );
            }

            if($this->input->post('loan_fine_type')==3){
                $this->loan_validation_rules[] = array(
                    'field' =>  'one_off_fine_type',
                    'label' =>  'One off Fine Type',
                    'rules' =>  'required|trim'
                );

                if($this->input->post('one_off_fine_type')==1)
                {
                    $this->loan_validation_rules[] = array(
                        'field' =>  'one_off_fixed_amount',
                        'label' =>  'One off Fine Fixed Amount',
                        'rules' =>  'required|trim|currency'
                    );
                }
                if($this->input->post('one_off_fine_type')==2)
                {
                    $this->loan_validation_rules[] = array(
                        'field' =>  'one_off_percentage_rate',
                        'label' =>  'One off Percentage (%) Rate',
                        'rules' =>  'required|trim|numeric'
                    );

                    $this->loan_validation_rules[] = array(
                        'field' =>  'one_off_percentage_rate_on',
                        'label' =>  'One off Percentage Rate On',
                        'rules' =>  'required|trim'
                    );
                }
            }
        }

        if($this->input->post('enable_outstanding_loan_balance_fines')){
            $this->loan_validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fine_type',
                    'label' =>  'Outstanding Loan FIne Types',
                    'rules' =>  'required|trim'
                );
            if($this->input->post('outstanding_loan_balance_fine_type')==1){
                $this->loan_validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fine_fixed_amount',
                    'label' =>  'Outstanding Loan Balance FIne Fixed Amount',
                    'rules' =>  'required|trim|currency'
                );
                $this->loan_validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fixed_fine_frequency',
                    'label' =>  'Outstanding Loan Balance FIne Fixed Frequency Rate',
                    'rules' =>  'required|trim'
                );
            }
            if($this->input->post('outstanding_loan_balance_fine_type')==2){
                $this->loan_validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_percentage_fine_rate',
                    'label' =>  'Outstanding Loan Balance Percentage Rate',
                    'rules' =>  'required|trim|numeric'
                );
                $this->loan_validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_percentage_fine_frequency',
                    'label' =>  'Outstanding Loan Balance Percentage Fine Frequency',
                    'rules' =>  'required|trim'
                );
                $this->loan_validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_percentage_fine_on',
                    'label' =>  'Outstanding Loan Balance Percentage Rate on',
                    'rules' =>  'required|trim'
                );
            }
            if($this->input->post('outstanding_loan_balance_fine_type')==3){
                $this->loan_validation_rules[] = array(
                    'field' =>  'outstanding_loan_balance_fine_one_off_amount',
                    'label' =>  'Outstanding Loan Balance One Off Amount',
                    'rules' =>  'required|trim|currency'
                );
            }
        }
        if($this->input->post('enable_loan_processing_fee'))
        {
            $this->loan_validation_rules[] = array(
                    'field' =>  'loan_processing_fee_type',
                    'label' =>  'Loan Processing Fee Type',
                    'rules' =>  'required|trim'
                );
            if($this->input->post('loan_processing_fee_type')==1){
                $this->loan_validation_rules[] = array(
                    'field' =>  'loan_processing_fee_fixed_amount',
                    'label' =>  'Loan Processing Fee Fixed Amount',
                    'rules' =>  'required|trim|currency'
                );
            }else if($this->input->post('loan_processing_fee_type')==2){
                 $this->loan_validation_rules[] = array(
                    'field' =>  'loan_processing_fee_percentage_rate',
                    'label' =>  'Loan Processing Percentage Value',
                    'rules' =>  'required|trim|numeric'
                ); 
                 $this->loan_validation_rules[] = array(
                    'field' =>  'loan_processing_fee_percentage_charged_on',
                    'label' =>  'Loan Processing Fee Charged On',
                    'rules' =>  'required|trim'
                );
            }
        }

        if($this->input->post('enable_loan_guarantors')){
            $this->loan_validation_rules[] = array(
                    'field' =>  'guarantor_id[]',
                    'label' =>  'Guarontor Name',
                    'rules' =>  'callback__verify_guarantor_name'
                );
        }
        if($this->input->post('interest_type')==3){
            $custom_interest_procedure = $this->input->post('custom_interest_procedure');
            if($this->input->post('custom_interest_procedure')==1){
                $this->loan_validation_rules[] = array(
                    'field' =>  'interest_rate_date_from',
                    'label' =>  'Interest Rate Date From',
                    'rules' =>  'callback__interest_rate_breakdown'
                );
            }
            else if($this->input->post('custom_interest_procedure')==2){
                $this->loan_validation_rules[] = array(
                    'field' =>  'custom_payment_date',
                    'label' =>  'Loan installment payment date',
                    'rules' =>  'callback__installment_breakdown'
                );
            }
        }else{
            $custom_interest_procedure='';
            $this->loan_validation_rules[] = array(
                    'field' =>  'interest_type',
                    'label' =>  'Interest Type ',
                    'rules' =>  'required|trim|numeric'
                );
             $this->loan_validation_rules[] = array(
                    'field' =>  'interest_rate',
                    'label' =>  'Interest Rate ',
                    'rules' =>  'required|trim|numeric'
                );
        }
        return $this->loan_validation_rules;
    }

    function _verify_guarantor_name(){
        $guarantors = $this->input->post('guarantor_id');
        $debtor_id = $this->input->post('debtor_id');
        $guaranteed_amounts = $this->input->post('guaranteed_amount');
        if(count($guarantors)>=1){
            for($i=0;$i<count($guarantors);$i++){
                if(empty($guarantors[$i])){
                  $this->form_validation->set_message('_verify_guarantor_name','The Guarantor Name field is required');
                    return FALSE;   
                }
                if($guarantors[$i]==$debtor_id){
                    $this->form_validation->set_message('_verify_guarantor_name','Guarantor number '.++$i.' should not be the same as the debtor taking the Loan');
                    return FALSE; 
                }
                if(!currency($guaranteed_amounts[$i]) && !empty($guaranteed_amounts[$i])){
                    $this->form_validation->set_message('_verify_guarantor_name','The Guaranteed amount row '.++$i.' must be a valid currency');
                    return FALSE;  
                }
                else{
                    return TRUE;
                }
            }
        }else{
            $this->form_validation->set_message('_verify_guarantor_name','Add atleast one guarantor');
            return FALSE;
        }
    }


    function create_loan(){
        $post = new StdClass();
        $this->loan_validation_rules = $this->loan_validation_function();
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
                
                $this->session->set_flashdata('success',"Debtor loan successfuly created");
                redirect('group/debtors/debtor_loan_listing');
            }else{
                $this->session->set_flashdata('error',"Error occured creating debtor loan");
                redirect('group/debtors/debtor_loan_listing');
            }  
        }

        foreach ($this->loan_validation_rules as $key => $field) {
            $field_value = $field['field'];
            $post->$field_value = set_value($field['field']);
        }
        preg_match_all("/\[[^\]]*\]/", $this->loan->sms_template_default,$placeholders);
        $this->data['placeholders'] = isset($placeholders[0])?$placeholders[0]:array();
        $this->data['post'] = $post;
        $this->data['banks'] = $this->banks_m->get_group_bank_options();
        $this->data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $this->data['group_members'] = $this->members_m->get_group_member_options($this->group->id);
        $this->data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();  
        $this->template->title(translate('Create Debtor Loan'))->build('group/form',$this->data);
    }

    function edit_loan($id=0){
        $id or redirect('group/debtors/debtor_loan_listing');
        $post = $this->debtors_m->get_loan_and_debtor($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry the loan does not exist');
            redirect('group/debtors/debtor_loan_listing');
        }
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
                $this->session->set_flashdata('success','Success updated debtor loan');
            }else{
                $this->session->set_flashdata('error','Error occured while editing debtor loan');
            }
            redirect('group/debtors/debtor_loan_listing');
        }else{
           foreach(array_keys($this->loan_validation_rules) as $field){
                if (isset($_POST[$field])){
                    $post->$field = $this->form_validation->$field;
                }
            } 
        }
        preg_match_all("/\[[^\]]*\]/", $this->loan->sms_template_default,$placeholders);
        $this->data['placeholders'] = isset($placeholders[0])?$placeholders[0]:array();
        $this->data['post'] = $post;
        $this->data['banks'] = $this->banks_m->get_group_bank_options();
        $this->data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $this->data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();  
        $guarantors = $this->debtors_m->get_loan_guarantors($post->id);

        $guarantors_details = array();
        foreach ($guarantors as $key=>$guarantors_detail) {
            $guarantors_details['guarantor_id'][$key] = $guarantors_detail->guarantor_id;
            $guarantors_details['guaranteed_amount'][$key] = $guarantors_detail->guaranteed_amount;
            $guarantors_details['guarantor_comment'][$key] = $guarantors_detail->guarantor_comment;
        }
        $this->data['group_members'] = $this->members_m->get_group_member_options($this->group->id);
        $this->data['guarantors_details'] = $guarantors_details;
        $this->template->title('Edit Debtor Loan')->build('group/form',$this->data);
    }

    function get_loan_guarantors_array(){
        print_r($this->debtors_m->get_loan_guarantors_array());die;
    }
    

    function debtor_loan_listing(){
        $from  = $this->input->post('from');
        $to  = $this->input->post('get');
        if($this->input->get('generate_excel')==1){
            $filter_parameters = array(
                'debtor_id' => $this->input->get('debtor_id'),
                'to' => strtotime($this->input->get('to')),
                'from' => strtotime($this->input->get('from')),
            );
            $this->data['posts'] = $this->debtors_m->get_all_loans($this->group->id,$filter_parameters);
            $this->data['$accounts'] = $this->accounts_m->get_active_group_account_options(FALSE);
            $this->data['$guarantors'] = $this->debtors_m->get_loan_guarantors_array();
            $this->data['group_currency'] = $this->group_currency;
            $this->data['group'] = $this->group;
            $this->data['debtor_options'] = $this->group_debtor_options;
            $this->data['filter_parameters'] = $filter_parameters;
            $this->data['members'] = $this->group_member_options;
            $json_file = json_encode($this->data);
            //print_r($json_file);
            print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/debtors/debtor_loan_listing',$this->group->name.' List of Debtor Loans'));
            die;
        }

        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $this->template->title(translate('Debtor Loans Listing'))->build('group/debtor_loan_listing',$this->data);
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
        //print_r($posts);die;
        $accounts = $this->accounts_m->get_active_group_account_options(FALSE);
        $guarantors = $this->debtors_m->get_loan_guarantors_array();
        /*print_r('<pre>');
        print_r($posts);
        print_r('</pre>');*/
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
                        <th class=\'text-right\'>
                            Amount ('.$this->group_currency.')
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
                                #'.($i++).' : <strong>'.$this->group_debtor_options[$post->debtor_id].'</strong><br/>
                                <strong>Disbursement Date : </strong>'.timestamp_to_date($post->disbursement_date).',
                                <small><strong>Recorded On : </strong>'.timestamp_to_date_and_time($post->created_on).'</small><br/>
                            ';
                            echo '
                            <br/>';
                                    echo "<strong>Description</strong><hr/>";
                                    echo '<strong>Interest Rate: </strong>'.$post->interest_rate.'% '.$this->loan->interest_types[$post->interest_type].' '.$this->loan->loan_interest_rate_per[$post->loan_interest_rate_per].'.<br/>';
                                    if($post->enable_loan_fines){
                                        echo '<strong>Late Payment Fines: </strong>'.$this->loan->late_loan_payment_fine_types[$post->loan_fine_type].'';
                                        if($post->loan_fine_type==1){
                                            echo ' of '.$this->group_currency.' '.number_format($post->fixed_fine_amount).' '.$this->loan->late_payments_fine_frequency[$post->fixed_amount_fine_frequency].' on '.$this->loan->fixed_amount_fine_frequency_on[$post->fixed_amount_fine_frequency_on];
                                        }else if($post->loan_fine_type == 2){
                                            echo ' of '.$post->percentage_fine_rate.'% '.$this->loan->late_payments_fine_frequency[$post->percentage_fine_frequency].' on '.$this->loan->percentage_fine_on[$post->percentage_fine_on];
                                        }else if($post->loan_fine_type==3){
                                            echo ' of '.$this->loan->one_off_fine_types[$post->one_off_fine_type];
                                            if($post->one_off_fine_type==1){
                                                echo ' of '.$this->group_currency.' '.number_to_currency($post->one_off_fixed_amount);
                                            }else if($post->one_off_fine_type==2){
                                                echo ' of '.$post->one_off_percentage_rate.'% on '.$this->loan->one_off_percentage_rate_on[$post->one_off_percentage_rate_on];
                                            }
                                        }

                                        echo '<br/>';
                                    }
                                    if($post->enable_outstanding_loan_balance_fines){
                                        echo '<strong>Outstanding Loan Balance Fines: </strong>'.$this->loan->late_loan_payment_fine_types[$post->outstanding_loan_balance_fine_type];
                                        if($post->outstanding_loan_balance_fine_type==1){
                                            echo ' of '.$this->group_currency.' '.number_to_currency($post->outstanding_loan_balance_fine_fixed_amount).' per ';
                                            echo $this->loan->late_payments_fine_frequency[$post->outstanding_loan_balance_fixed_fine_frequency];
                                        }else if($post->outstanding_loan_balance_fine_type==2){
                                            echo ' of '.$post->outstanding_loan_balance_percentage_fine_rate.'% '.$this->loan->late_payments_fine_frequency[$post->outstanding_loan_balance_percentage_fine_frequency].' on '.$this->loan->percentage_fine_on[$post->outstanding_loan_balance_percentage_fine_on];
                                        }else if($outstanding_loan_balance_fine_type==3){
                                            echo ' of '.$this->group_currency.' '.number_to_currency($post->outstanding_loan_balance_fine_one_off_amount);
                                        }
                                        echo '<br/>';
                                    }
                                    if($post->enable_loan_processing_fee){
                                        echo '<strong>Loan Processing Charges: </strong>'.$this->loan->loan_processing_fee_types[$post->loan_processing_fee_type];
                                        if($post->loan_processing_fee_type==1){
                                            echo " of ".$this->group_currency.' '.number_to_currency($post->loan_processing_fee_fixed_amount);
                                        }else if($post->loan_processing_fee_type==2){
                                            echo ' of '.$post->loan_processing_fee_percentage_rate.'% on '.$this->loan->loan_processing_fee_percentage_charged_on[$post->loan_processing_fee_percentage_charged_on];
                                        }


                                        echo '<br/>';
                                    }

                                    if($post->enable_loan_guarantors){
                                        
                                        $loan_guarantors = isset($guarantors[$post->id])?$guarantors[$post->id]:array();
                                        if($loan_guarantors){
                                            echo '
                                            <br/>';
                                            echo "<strong>Guarantors</strong><hr/>";
                                            echo '<table class="table table-condensed table-striped table-hover">
                                                <tbody>
                                                    <tr>
                                                        <td><strong>Member Name</strong></td>
                                                        <td><strong>Amount</strong></td>
                                                        <td><strong>Comment</strong></td>
                                                    </tr>
                                                </tbody>
                                                <tbody>';
                                                foreach ($loan_guarantors as $loan_guarantor) {
                                                    $loan_guarantor = (object)$loan_guarantor;
                                                    echo '<tr>
                                                            <td>'.$this->group_member_options[$loan_guarantor->member_id].'</td>
                                                            <td>'.number_to_currency($loan_guarantor->amount).'</td>
                                                            <td>'.$loan_guarantor->comment.'</td>
                                                        </tr>';
                                                }
                                                    
                                            echo '</tbody>
                                            </table>'
                                            ;
                                        }
                                        
                                    }

                                    if($post->account_id){
                                        echo '<br/>Withdrawn from '.$accounts[$post->account_id];
                                    }

                                    if($post->is_fully_paid){
                                        echo '<br/> <span class="label label-xs label-success">Fully Paid</span>';
                                    }else{
                                        echo '<br/><strong>Loan Status:</strong> <span class="label label-xs label-primary">In Progress</span>';
                                    }
                            echo '
                            </td>
                            <td  class=\'text-right\'>
                                '.number_to_currency($post->loan_amount).'
                            </td>  
                            <td>
                                <a href="'.site_url('group/debtors/edit_loan/'.$post->id).'" class="btn btn-xs default">
                                    <i class="fa fa-pencil"></i> Edit &nbsp;&nbsp; 
                                </a>
                                <a href="'.site_url('group/debtors/view/'.$post->id).'" class="btn btn-xs default">
                                    <i class="fa fa-eye"></i> View &nbsp;&nbsp; 
                                </a>
                                <a href="'.site_url('group/debtors/statement/'.$post->id).'" class="btn btn-xs btn-success">
                                    <i class="fa fa-book"></i> statement &nbsp;&nbsp; 
                                </a>
                                <a href="'.site_url('group/deposits/record_debtor_loan_repayments/?loan_id='.$post->id.'&debtor_id='.$post->debtor_id).'" class="btn btn-xs blue">
                                    <i class="fa fa-money"></i> Record Repayment &nbsp;&nbsp; 
                                </a>
                                <a href="'.site_url('group/debtors/void_loan/'.$post->id).'" class="btn confirmation_link btn-xs red">
                                    <i class="fa fa-trash-o"></i> Void &nbsp;&nbsp; 
                                </a>';
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
                <h4 class="block">Information! No records to display</h4>
                <p>
                    No debtor loans to display.
                </p>
            </div>';
        } 
    }

    function ajax_get_active_debtor_loans(){
        $debtor_id = $this->input->post('debtor_id');
        $attribute = $this->input->post('attribute');
        if(preg_match('/debtors/',$attribute)){
            $value =explode('debtors[', $attribute);
            $value = explode(']', $value[1]);

            $att = $value[0];
        }
        else{
            $att = $attribute;
        }
        if($debtor_id){
            $loans = $this->debtors_m->get_active_debtor_loans_option($debtor_id);
            if($loans){
                echo '<i class="" ></i>'.form_dropdown('loans['.$att.']',array(''=>'Select a loan')+$loans+array('0'=>"Add Loan"),'',' class="form-control  loan new_loan"');
            }else{
                echo  '<i class="" ></i>'.form_dropdown('loans['.$att.']',array(''=>'Debtor has no active loans')+array('0'=>"Add Loan"),'',' class="form-control  loan new_loan"');
            }
        }else{
            echo '<i class="" ></i>'.form_dropdown('loans['.$att.']',array(''=>'Select Debtor first'),'',' class="form-control  loan new_loan"');
        }
    }

    function statement($id=0){
        $id or redirect('group/debtors/debtor_loan_listing');
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
        $this->data['lump_sum_remaining'] = $this->debtors_m->get_loan_lump_sum_as_date($id);
        $this->accounts = $this->accounts_m->get_group_account_options(FALSE);
        $this->data['accounts'] = $this->accounts;
        $this->data['deposit_options']=$this->transactions->deposit_method_options;
        $this->data['group'] = $this->group;
        $this->data['group_currency'] = $this->group_currency;
        $this->data['application_settings'] = $this->application_settings;
        if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
            $this->data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
        }else{
            $this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
        }
        $generate_pdf = FALSE;
        if($generate_pdf==TRUE){
            //$response = $this->curl_post_data->curl_post_json_pdf((json_encode($this->data)),'https://pdfs.chamasoft.com/loan_statement',$loan->first_name.' Loan Statenent - '.$this->group->name);
            print_r($response);die;
        }else{
            $this->template->title($loan->name.' Loan Statement')->build('group/statement',$this->data); 
        }
    }

    function loan_invoices_sent_list(){
        $posts= $this->loan_invoices_m->get_all_group_sent_loan_invoices();

        $this->data['posts'] = $posts;
        $this->template->title('Sent Member Loan Invoices')->build('group/sent_invoices',$this->data);
    }

    function view($id=0,$generate_pdf=FALSE){
        $id or redirect('group/debtors/debtor_loan_listing');
        $posts = $this->debtors_m->get_loan_installments($id);
        if(!$posts){
            $this->session->set_flashdata('error','The loan has no installments to display');
            redirect('group/debtors/debtor_loan_listing');
            die;
        }
        $loan = $this->debtors_m->get_loan_and_debtor($id);
        $this->data['loan'] = $loan;
        $this->data['posts'] = $posts;
        $this->data['members'] = $this->members_m->get_group_member_options();
        $this->data['loan_guarantors'] = $this->debtors_m->get_loan_guarantors($id);
        $this->data['accounts'] = $this->accounts;
        $this->data['group'] = $this->group;
        $this->data['group_currency'] = $this->group_currency;
        $this->data['application_settings'] = $this->application_settings;
        if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
            $this->data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
        }else{
            $this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
        }
        if($generate_pdf==TRUE){
            $response = $this->curl_post_data->curl_post_json_pdf((json_encode($this->data)),'https://pdfs.chamasoft.com/loan_installment',$loan->first_name.' Loan Installment Breakdown - '.$this->group->name);
            print_r($response);die;
        }else{
            $this->template->title($loan->name.' Loan Installments')->build('group/view',$this->data);
        }
    }

    function disable_invoice_penalties($id=0){
        $id OR redirect($this->agent->referrer());
        $diasabled = $this->debtors_m->update_loan_invoices($id,array('disable_fines'=>1,'modified_by'=>$this->user->id,'modified_on'=>time()));
        if($diasabled){
            $this->session->set_flashdata('success','Successfully disabled');
        }else{
            $this->session->set_flashdata('error','Unable to disable loan invoice Penalties');
        }
        redirect($this->agent->referrer());
    }

    function enable_invoice_penalties($id=0){
        $id OR redirect($this->agent->referrer());
        $diasabled = $this->debtors_m->update_loan_invoices($id,array('disable_fines'=>NULL,'modified_by'=>$this->user->id,'modified_on'=>time()));
        if($diasabled){
            $this->session->set_flashdata('success','Successfully disabled');
        }else{
            $this->session->set_flashdata('error','Unable to disable loan invoice Penalties');
        }
        redirect($this->agent->referrer());
    }

    function void_loan($id=0,$redirect=TRUE){
        if($id){
            $withdrawal = $this->withdrawals_m->get_group_withdrawal_by_debtor_loan_id($id,$this->group->id);
            if($withdrawal){
                    if($this->transactions->void_group_withdrawal($withdrawal->id,$withdrawal,TRUE,$this->group->id)){
                        if($redirect){
                            redirect('group/debtors/debtor_loan_listing');
                        }
                    return TRUE;
                }else{
                    if($redirect){
                        redirect('group/debtors/debtor_loan_listing');
                    }
                    return FALSE;
                }
            }else{
                if($this->loan->void_external_loan($id)){
                    if($redirect){
                            redirect('group/debtors/debtor_loan_listing');
                        }
                    return TRUE;
                }else{
                    if($redirect){
                        redirect('group/debtors/debtor_loan_listing');
                    }
                    return FALSE;
                }
            }
        }else{
            $this->session->set_flashdata('error','Kindly pass all parameters');
            if($redirect){
                redirect('group/debtors/debtor_loan_listing');
            }
            return FALSE;
        }
    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_void'){
            for($i=0;$i<count($action_to);$i++){
                $this->void_loan($action_to[$i],FALSE);
            }
            redirect($this->agent->referrer());
        }else if($action == 'bulk_suspend'){
            for($i=0;$i<count($action_to);$i++){
                $this->suspend($action_to[$i],FALSE);
            }
            redirect($this->agent->referrer());
        }else if($action == 'bulk_activate'){
            for($i=0;$i<count($action_to);$i++){
                $this->activate($action_to[$i],FALSE);
            }
            redirect($this->agent->referrer());
        }else{
            redirect('group/loans/listing');
        }
    }

    function suspend($id=0,$redirect=TRUE){
        if($id){
            if($post = $this->debtors_m->get($id)){
                if(!$post->active){
                    $this->session->set_flashdata('error','Debtor already suspended');
                    if($redirect){
                        redirect($this->agent->referrer());
                    }
                    return FALSE;
                }else{
                    if($this->debtors_m->update($id,array('modified_on'=>time(),'modified_by'=>$this->user->id,'active'=>NULL))){
                        $this->session->set_flashdata('success','Successfully suspended');
                        if($redirect){
                            redirect($this->agent->referrer());
                        }
                        return TRUE;
                    }else{
                        $this->session->set_flashdata('error','Error while suspending, try again');
                        if($redirect){
                            redirect($this->agent->referrer());
                        }
                        return FALSE;
                    }
                }
            }else{
                $this->session->set_flashdata('error','Could not find debtor');
                if($redirect){
                    redirect($this->agent->referrer());
                }
                return FALSE;
            }
        }else{
            $this->session->set_flashdata('error','parameters missing');
            if($redirect){
                redirect($this->agent->referrer());
            }
            return FALSE;
        }
    }

    function activate($id=0,$redirect = TRUE){
        if($id){
            if($post = $this->debtors_m->get($id)){
                if($post->active){
                    $this->session->set_flashdata('error','Debtor already active');
                    if($redirect){
                        redirect($this->agent->referrer());
                    }
                    return FALSE;
                }else{
                    if($this->debtors_m->update($id,array('modified_on'=>time(),'modified_by'=>$this->user->id,'active'=>1))){
                        $this->session->set_flashdata('success','Successfully activated');
                        if($redirect){
                            redirect($this->agent->referrer());
                        }
                        return TRUE;
                    }else{
                        $this->session->set_flashdata('error','Error while activating, try again');
                        if($redirect){
                            redirect($this->agent->referrer());
                        }
                        return FALSE;
                    }
                }
            }else{
                $this->session->set_flashdata('error','Could not find debtor');
                if($redirect){
                    redirect($this->agent->referrer());
                }
                return FALSE;
            }
        }else{
            $this->session->set_flashdata('error','parameters missing');
            if($redirect){
                redirect($this->agent->referrer());
            }
            return FALSE;
        }
    }
}