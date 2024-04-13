<?php 
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Member extends Member_Controller{
    protected $data = array();

    protected $validation_rules = array(
            array(
                    'field' => 'loan_amount',
                    'label' => 'Loan Amount Applying',
                    'rules' =>  'required|currency|trim'
                ),
            array(
                    'field' => 'repayment_period',
                    'label' => 'Loan Repayment Period',
                    'rules' =>  'required|numeric|trim'
                ),
            array(
                    'field' => 'agree_to_rules',
                    'label' => 'Group Rules and Regulations',
                    'rules' =>  'numeric|trim'
                ),           
            
        );

	public function __construct(){
        parent::__construct();
        $this->load->model('loan_types_m');
        $this->load->library('loan');

        $this->data['interest_types'] = $this->loan->interest_types;
        $this->data['loan_interest_rate_per'] = $this->loan->loan_interest_rate_per;
        $this->data['loan_processing_fee_types'] = $this->loan->loan_processing_fee_types;
        $this->data['late_loan_payment_fine_types'] = $this->loan->late_loan_payment_fine_types;
        $this->data['late_payments_fine_frequency'] = $this->loan->late_payments_fine_frequency;
        $this->data['fixed_amount_fine_frequency_on'] = $this->loan->fixed_amount_fine_frequency_on;
        $this->data['percentage_fine_on'] = $this->loan->percentage_fine_on;
        $this->data['one_off_fine_types'] = $this->loan->one_off_fine_types;
        $this->data['one_off_percentage_rate_on'] = $this->loan->one_off_percentage_rate_on;
        $this->data['loan_processing_fee_percentage_charged_on'] = $this->loan->loan_processing_fee_percentage_charged_on;
    }

    public function listing(){
        $total_rows = $this->loan_types_m->count_all();
        $pagination = create_pagination('member/loan_types/listing/pages', $total_rows,50,5,TRUE);
        $this->data['posts'] = $this->loan_types_m->limit($pagination['limit'])->get_all();
        $this->data['pagination'] = $pagination;
        $this->template->set_layout('member_default.html')->title('List Loan Types')->build('member/listing',$this->data);
    }

    function apply($id=0){
        $id OR redirect('member/loan_types/listing');
        $loan_type = $this->loan_types_m->get($id);
        $post = new StdClass();

        if(!$loan_type){
            $this->session->set_flashdata('error','Loan type not available');
            redirect('member/loan_types/listing');
        }
        $loan_status = $this->loan_applications_m->count_pending_loan_applications($id);
        if($loan_status){
            $this->session->set_flashdata('error','Sorry you already have an active loan application');
            redirect('member/loan_types/listing');
        }
        $this->validation_rules[] = array(
                    'field' => 'loan_amount',
                    'label' => 'Loan Amount Applying',
                    'rules' =>  'required|currency|trim|greater_than_equal_to['.$loan_type->minimum_loan_amount.']|less_than_equal_to['.$loan_type->maximum_loan_amount.']'
                );
        $this->validation_rules[] = array(
                    'field' => 'repayment_period',
                    'label' => 'Loan Repayment Period',
                    'rules' =>  'required|numeric|trim|greater_than_equal_to['.$loan_type->minimum_repayment_period.']|less_than_equal_to['.$loan_type->maximum_repayment_period.']'
                );
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $data = array(
                    'member_id' => $this->member->id,
                    'group_id' => $this->group->id,
                    'loan_type_id' => $loan_type->id,
                    'loan_amount' => $this->input->post('loan_amount'),
                    'repayment_period' => $this->input->post('repayment_period'),
                    'active' => 1,
                    'status' => 1,
                    'created_on' => time(),
                    'created_by' => $this->user->id,
                    'agree_to_rules' => $this->input->post('agree_to_rules')?:0,
                );
            if($this->loan->create_loan_application($data,$this->user,$this->group_currency)){
                $this->session->set_userdata('success','Loan application sent, awaiting approval');
            }else{
                $this->session->set_userdata('error','Error creating loan application');
            }
            redirect('member/loans/loan_applications');
        }
        foreach ($this->validation_rules as $key => $field) {
            $post->$field['field'] = set_value($field['field']);
        }

        $this->data['loan_type'] = $loan_type;
        $this->data['post'] = $post;
        $this->template->set_layout('member_default.html')->title('Apply for '.$loan_type->name)->build('member/form',$this->data);
    }

    function edit_application($id=0){
        $id OR redirect('member/loans/loan_applications');
        $post = $this->loan_applications_m->get_member_loan_application($id);
        if(!$post){
            $this->session->set_userdata('error','Sorry the loan application is not available');
            redirect('member/loans/loan_applications');
        }
        if($post->status==2){
            $this->session->set_userdata('error','Sorry the loan cannot be edited as it is being reviewed');
            redirect('member/loans/loan_applications');
        }else if($post->status==3){
            $this->session->set_userdata('error','Sorry the loan cannot be edited as it is already been disbursed');
            redirect('member/loans/loan_applications');
        }else if($post->status==4){
            $this->session->set_userdata('error','Sorry the loan cannot be edited as it is already declined');
            redirect('member/loans/loan_applications');
        }

        $loan_type = $this->loan_types_m->get($post->loan_type_id);
        if(!$loan_type){
            $this->session->set_flashdata('error','Loan type not available');
            redirect('member/loans/loan_applications');
        }

        $loan_status = $this->loan_applications_m->count_pending_loan_applications($id);
        if($loan_status){
            $this->session->set_flashdata('error','Sorry you already have an active loan application');
            redirect('member/loan_types/listing');
        }
        $this->validation_rules[] = array(
                    'field' => 'loan_amount',
                    'label' => 'Loan Amount Applying',
                    'rules' =>  'required|currency|trim|greater_than_equal_to['.$loan_type->minimum_loan_amount.']|less_than_equal_to['.$loan_type->maximum_loan_amount.']'
                );
        $this->validation_rules[] = array(
                    'field' => 'repayment_period',
                    'label' => 'Loan Repayment Period',
                    'rules' =>  'required|numeric|trim|greater_than_equal_to['.$loan_type->minimum_repayment_period.']|less_than_equal_to['.$loan_type->maximum_repayment_period.']'
                );
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $data = array(
                    'member_id' => $this->member->id,
                    'group_id' => $this->group->id,
                    'loan_type_id' => $loan_type->id,
                    'loan_amount' => $this->input->post('loan_amount'),
                    'repayment_period' => $this->input->post('repayment_period'),
                    'modified_on' => time(),
                    'modified_by' => $this->user->id,
                    'agree_to_rules' => $this->input->post('agree_to_rules')?:0,
                );
            if($this->loan_applications_m->update($id,$data)){
                $this->session->set_userdata('success','Loan application successfully updated');
            }else{
                $this->session->set_userdata('error','Error creating updating loan application');
            }
            redirect('member/loans/loan_applications');
        }
        foreach (array_keys($this->validation_rules) as $field){
             if (isset($_POST[$field]))
            {
                $post->$field = $this->form_validation->$field;
            }
        }

        $this->data['loan_type'] = $loan_type;
        $this->data['post'] = $post;
        $this->template->set_layout('member_default.html')->title('Edit  '.$loan_type->name)->build('member/form',$this->data);


    }
    function ajax_view($loan_type_id=0){
        //echo $loan_type_id; die();
        $post = new StdClass();     
        $post = $this->loan_types_m->get($loan_type_id);
        $interest_types = $this->loan->interest_types;
        $loan_interest_rate_per= $this->loan->loan_interest_rate_per;
        if(!$post){
            $this->session->set_flashdata('info','group contribution details do not exist');
            return FALSE;
        }else{  
            echo '<strong> Loan of  minimum '.$this->group_currency.' '.number_to_currency($post->minimum_loan_amount).' and a maximum of '.$this->group_currency.' '.number_to_currency($post->maximum_loan_amount).' amount to be repaid in a period of ';
            if($post->loan_repayment_period_type == 1){
                echo $post->fixed_repayment_period.' Months';
            }else if ($post->loan_repayment_period_type == 2) {
            echo $post->minimum_repayment_period.' - '.$post->maximum_repayment_period .' Months';
            }  
            echo ' and a maximum of '.$post->maximum_guarantors.' and minimum of '.$post->minimum_guarantors.' guarantors required and the interest rate of '.$post->interest_rate.'% per '.$loan_interest_rate_per[$post->loan_interest_rate_per].' on '.$interest_types[$post->interest_type].' </strong>';           
            
        }

    }

}