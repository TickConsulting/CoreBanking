<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Group extends Group_Controller{
    protected $data = array();
    protected $validation_rules = array(
            array(
                'field' => 'member_id',
                'label' => 'Member name',
                'rules' => 'required|trim|numeric',
            ),
            array(
                'field' => 'refund_date',
                'label' => 'Refund date',
                'rules' => 'required|trim',
            ),
            array(
                'field' => 'contribution_id',
                'label' => 'Contribution to refund from',
                'rules' => 'required|trim|numeric',
            ),
             array(
                'field' => 'account_id',
                'label' => 'Account id',
                'rules' => 'required|trim',
            ),
             array(
                'field' => 'refund_method',
                'label' => 'Refund method',
                'rules' => 'required|trim',
            ),
            array(
                'field' => 'amount',
                'label' => 'Refund Amount',
                'rules' => 'required|trim|currency',
            ),
            array(
                'field' => 'description',
                'label' => 'Description',
                'rules' => 'trim',
            ),
        );

	function __construct(){
        parent::__construct();
        $this->load->library('transactions');
        $this->load->model('accounts/accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->model('contributions/contributions_m');
        $this->load->model('members/members_m');
        $this->load->library('notifications');
        $this->load->library('contribution_invoices');
        $this->load->model('contribution_refunds_m');
        $this->data['accounts'] = $this->accounts_m->get_group_account_options();
        $this->data['active_accounts'] = $this->accounts_m->get_active_group_account_options('','','','',TRUE);
        $this->data['withdrawal_methods'] = $this->transactions->withdrawal_method_options;
        $this->data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $this->data['active_contribution_options'] = $this->contributions_m->get_active_group_contribution_options();
        $this->sms_template_default = $this->contribution_invoices->sms_template_default;
    }

    function index(){
        $this->template->title('Group Contribution Refunds')->build('group/index');
    }

    function create(){
    	$data = array();
    	$post = new stdClass();

        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $id = $this->transactions->record_contribution_refund(
                $this->group->id,
                strtotime($this->input->post('refund_date')),
                $this->input->post('member_id'),
                $this->input->post('account_id'),
                $this->input->post('contribution_id'),
                $this->input->post('refund_method'),
                $this->input->post('description'),
                $this->input->post('amount'),
                $this->user->id);

            if($id){
                $this->session->set_flashdata('success','Refund successfully created');

                $from_member = $this->members_m->get_group_member_by_user_id('',$this->user->id);
                $to_member = $this->members_m->get_group_member($this->input->post('member_id'));
                $contribution_name = $this->data['active_contribution_options'][$this->input->post('contribution_id')];
                $this->notifications->create('Contribution Refund',
                    'Dear '.$to_member->first_name.' you have been refunded '.$this->group_currency.' '.number_to_currency($this->input->post('amount')).' for your '.$contribution_name,
                    $this->user,
                    $from_member->id,
                    $to_member->user_id,
                    $to_member->id,
                    $this->group->id,
                    'View Refund',
                    'group/contribution_refunds/view/'.$id,8);
                if($this->input->post('new_item')){
                    redirect('group/contribution_refunds/create');
                }else{
                    redirect('group/contribution_refunds/view/'.$id);
                }
            }else{
                $this->session->set_flashdata('error','Refund was not successfully created');

                redirect('group/contribution_refunds/create');
            }
        }
        foreach ($this->validation_rules as $key => $field) 
        {
            $field_value = $field['field'];
            $post->$field_value= set_value($field['field']);
        }
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
        $this->data['post'] = $post;
        $this->data['contribution_category_options'] = $this->contribution_invoices->contribution_category_options;
        $this->data['banks'] = $this->banks_m->get_group_bank_options();
        $this->data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $this->data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $this->data['group_role_options'] = $this->group_roles_m->get_group_role_options();
        $this->template->title(translate('Create contribution refund'))->build('group/form',$this->data);
    }

    function ajax_create(){
        $response = array();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $id = $this->transactions->record_contribution_refund(
                $this->group->id,
                strtotime($this->input->post('refund_date')),
                $this->input->post('member_id'),
                $this->input->post('account_id'),
                $this->input->post('contribution_id'),
                $this->input->post('refund_method'),
                $this->input->post('description'),
                $this->input->post('amount'),
                $this->user->id);

            if($id){
                
                $from_member = $this->members_m->get_group_member_by_user_id('',$this->user->id);
                $to_member = $this->members_m->get_group_member($this->input->post('member_id'));
                $contribution_name = $this->data['active_contribution_options'][$this->input->post('contribution_id')];
                $this->notifications->create('Contribution Refund',
                    'Dear '.$to_member->first_name.' you have been refunded '.$this->group_currency.' '.number_to_currency($this->input->post('amount')).' for your '.$contribution_name,
                    $this->user,
                    $from_member->id,
                    $to_member->user_id,
                    $to_member->id,
                    $this->group->id,
                    'View Refund',
                    'group/contribution_refunds/view/'.$id,8);
                $response = array(
                    'status' => 1,
                    'message' => 'Refund successfully created.',
                    'refer'=>site_url('group/contribution_refunds/view/'.$id)
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Refund was not successfully created',
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

    function listing(){
        $total_rows = $this->contribution_refunds_m->count_active_contribution_refunds();
        $pagination = create_pagination('group/contribution_refunds/listing/pages', $total_rows);
        $this->data['posts'] = $this->contribution_refunds_m->limit($pagination['limit'])->get_all();
        $this->data['pagination'] = $pagination;
        $this->template->title('List Contributions')->build('group/listing',$this->data);
    }

    function view($id=0){
        $id OR redirect('group/contribution_refunds/listing');
        $post = $this->contribution_refunds_m->get($id);
        $post OR redirect('group/contribution_refunds/listing');
        $member = $this->members_m-> get_group_member($post->member_id);

        $this->data['post'] = $post;
        $this->data['member'] = $member;
        $this->data['user'] = $this->ion_auth->get_user($post->created_by);

        $this->template->title($member->first_name.' '.$member->last_name.' Contribution Refund')->build('group/view',$this->data);
    }


    function void($id=0,$redirect=TRUE){
        $id OR redirect('group/contribution_refunds/listing');
        $post = $this->contribution_refunds_m->get_group_contribution_refund($id);
        $post OR redirect('group/contribution_refunds/listing');

        $update = $this->transactions->void_contribution_refund($id);

        if($update){
            $this->session->set_flashdata('success','Successfully voided');
            if($redirect){
                redirect('group/contribution_refunds/listing');
             }
             return TRUE;
        }else{

            $this->session->set_flashdata('error','Contribution refund not successfully voided');
            if($redirect){
                redirect('group/contribution_refunds/listing');
             }
             return FALSE;
        }

    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_void'){
            for($i=0;$i<count($action_to);$i++){
                $this->void($action_to[$i],FALSE);
            }
        }
        redirect('group/contribution_refunds/listing');
    }

}