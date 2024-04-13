<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Group extends Group_Controller{

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

	function __construct(){
        parent::__construct();
        $this->load->model('accounts/accounts_m');
        $this->load->model('deposits/deposits_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('contributions/contributions_m');
        $this->load->model('checkoffs_m');
    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_void_checkoff'){
            if(empty($action_to)){
                $this->session->set_flashdata('error','Select atleast one checkoff deposit to void');
                redirect('group/checkoffs/listing');
            }else{
                for($i=0;$i<count($action_to);$i++){
                    $this->void($action_to[$i],FALSE);
                }
            }
            redirect($this->agent->referrer());
        }
    }

    function index(){
        $this->data = array();
        $this->data['total_checkoff'] = $this->contributions_m->get_total_checkoff_set_amount($this->group->id);
        $this->data['set_members'] = $this->contributions_m->count_members_set_checkoff($this->group->id);
        $this->template->title('Checkoffs')->build('group/index',$this->data);
    }


    function set($generate_pdf=FALSE,$generate_excel=FALSE){
        $data = array();
        $contribution_options = $this->contributions_m->get_group_checkoff_contribution_options();
        if(!$contribution_options){
            $this->session->set_flashdata('warning','You have not setup any savings contributions');
            if($this->agent->referrer() && ($this->agent->referrer() != current_url())){
                redirect($this->agent->referrer());
            }else{
                redirect('group/checkoffs/listing');
            }
        }
        $data['member_checkoff_contribution_amount_pairings'] = $this->contributions_m->get_group_member_checkoff_contribution_amount_pairings_array();
        $data['contribution_options'] = $contribution_options;
        $data['group'] = $this->group;
        $data['application_settings'] = $this->application_settings;
        $data['active_group_member_options'] = $this->active_group_member_options;
        $data['group_currency'] = $this->group_currency;
        $data['membership_numbers'] = $this->membership_numbers;
        if($this->input->get('generate_pdf') == 1){
            if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
                $data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
            }else{
                $data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
            }
            $response = $this->curl_post_data->curl_post_json_pdf((json_encode($data)),'https://pdfs.chamasoft.com/deposits/set_checkoff',$this->group->name.' Checkoff');
            print_r($response);die;
        }
        if($this->input->get('generate_excel') == 1){
            if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
                $data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
            }else{
                $data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
            }
            $response = $this->curl_post_data->curl_post_json_excel((json_encode($data)),'https://excel.chamasoft.com/deposits/set_checkoff',$this->group->name.' Checkoff');
            print_r($response);die;
        }
        $this->template->title('Set Check Off')->build('group/set',$data);
    }

    function submit(){
        $contribution_options = $this->contributions_m->get_group_checkoff_contribution_options();
        if(!$contribution_options){
            $this->session->set_flashdata('warning','You have not setup any savings contributions');
            if($this->agent->referrer() && ($this->agent->referrer() != current_url())){
                redirect($this->agent->referrer());
            }else{
                redirect('group/checkoffs/listing');
            }
        }
        set_time_limit(0);
        ini_set("memory_limit", "-1");
        $data = array();
        $validation_rules = array(
            array(
                'field' => 'checkoff_date',
                'label' => 'Checkoff Date',
                'rules' => 'required|trim',
            ),
            array(
                'field' => 'account_id',
                'label' => 'Account',
                'rules' => 'required|trim',
            ),
        );
        $this->form_validation->set_rules($validation_rules);
        if($this->form_validation->run()){
            $checkoff_date = strtotime($this->input->post('checkoff_date'));
            $account_id = $this->input->post('account_id');
            $input = array(
                'checkoff_date' => $checkoff_date,
                'group_id' => $this->group->id,
                'account_id' => $account_id,
                'active' => 1,
                'amount' => 0,
                'created_on' => time(),
                'created_by' => $this->user->id,
            );
            if($checkoff_id = $this->checkoffs_m->insert($input)){
                $checkoff_amounts = $this->input->post('checkoff_amounts');
                $result = TRUE;
                $total_amount = 0;
                foreach($checkoff_amounts as $contribution_id => $members):
                    foreach($members as $member_id => $amount):
                        if($amount):
                            $amount = valid_currency($amount);
                            if($this->transactions->record_contribution_payment($this->group->id,$checkoff_date,$member_id,$contribution_id,$account_id,1,'',$amount,FALSE,FALSE,0,$checkoff_id)){
                                $total_amount+=$amount;
                            }else{
                                $result = FALSE;
                            }   
                        endif;
                    endforeach;
                endforeach;
                if($result){
                    $this->session->set_flashdata('success','Check off submitted successfully.');
                    $input = array(
                        'amount' => $total_amount,
                        'modified_by' => $this->user->id,
                        'modified_on' => time()
                    );
                    if($this->checkoffs_m->update($checkoff_id,$input)){

                    }else{
                        $this->session->set_flashdata('warning','Something went wrong when updating the checkoff entries.');
                    }
                }else{
                    $this->session->set_flashdata('warning','Something went wrong when submitting the checkoff sheet.');
                }
            }else{
                $this->session->set_flashdata('error','Could not save checkoff ');
            }
            redirect('group/checkoffs/listing');
        }
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',TRUE);
        $data['member_checkoff_contribution_amount_pairings'] = $this->contributions_m->get_group_member_checkoff_contribution_amount_pairings_array();
        $data['contribution_options'] = $contribution_options;
        $data['membership_numbers'] = $this->membership_numbers;
        $this->template->title('Submit Check Off')->build('group/submit',$data);
    }

    function view($id = 0){
        $data = array();
        $id OR redirect('group/checkoffs/listing');
        $post = $this->checkoffs_m->get_group_checkoff($id);
        $post OR redirect('group/checkoffs/listing');
        $data['post'] = $post;
        $data['contribution_options'] = $this->contributions_m->get_group_contribution_options();
        $data['checkoff_amounts'] = $this->checkoffs_m->get_group_checkoff_amounts_array_by_checkoff_id($post->id);
        $member_ids = array();
        foreach($data['checkoff_amounts'] as $contribution_id => $members):
            foreach($members as $member_id => $amount):
                if(in_array($member_id,$member_ids)){

                }else{
                    $member_ids[] = $member_id;
                }
            endforeach;
        endforeach; 
        $data['member_ids'] = $member_ids;
        $data['accounts'] = $this->accounts_m->get_group_account_options(FALSE);
        $data['group'] = $this->group;
        $data['application_settings'] = $this->application_settings;
        $data['group_member_options'] = $this->group_member_options;
        $data['group_currency'] = $this->group_currency;
        $data['membership_numbers'] = $this->membership_numbers;
        if($this->input->get('generate_pdf') == 1){
            if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
                $data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
            }else{
                $data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
            }
            $response = $this->curl_post_data->curl_post_json_pdf((json_encode($data)),'https://pdfs.chamasoft.com/deposits/view_checkoff',$this->group->name.' View Checkoff');
            print_r($response);die;
        }
        if($this->input->get('generate_excel') == 1){
            if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
                $data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
            }else{
                $data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
            }
            $response = $this->curl_post_data->curl_post_json_excel((json_encode($data)),'https://excel.chamasoft.com/deposits/view_checkoff',$this->group->name.' View Checkoff');
            print_r($response);die;
        }
        $this->template->set_layout('default_full_width.html')->title('View Check Off')->build('group/view',$data);
    }

    function listing(){
        $data = array();
        $total_rows = $this->checkoffs_m->count_group_checkoffs();
        $pagination = create_pagination('group/checkoffs/listing/pages',$total_rows,20,5,TRUE);
        $data['pagination'] = $pagination;
        $data['posts'] = $this->checkoffs_m->limit($pagination['limit'])->get_group_checkoffs();
        // $data['posts'] = $this->checkoffs_m->get_group_checkoffs();
        $this->template->title('Check Offs')->build('group/listing',$data);
    }

    function void($id = 0,$redirect = TRUE){
        set_time_limit(0);
        ini_set("memory_limit", "-1");
        $id OR redirect('group/checkoffs/listing');
        $post = $this->checkoffs_m->get_group_checkoff($id);
        $post OR redirect('group/checkoffs/listing');
        $input = array(
            'active' => 0,
            'modified_on' => time(),
            'modified_by' => $this->user->id
        );
        if($this->checkoffs_m->update($post->id,$input)){
            $checkoff_deposits = $this->deposits_m->get_group_deposits_by_checkoff_id($post->id);
            $result = TRUE;
            foreach($checkoff_deposits as $deposit):
                if($this->transactions->void_group_deposit($deposit->id,$deposit,FALSE,$deposit->group_id,$this->user)){

                }else{
                    $result = FALSE;
                }
            endforeach;
            if($result){
                $this->session->set_flashdata('success','Check off deposits voided successfully');
            }else{
                $this->session->set_flashdata('warning','Something went wrong while voiding checkoff deposits');
            }
        }else{
            $this->session->set_flashdata('error','Could not void checkoff');
        }
        if($redirect){
            if($this->agent->referrer()){
                redirect($this->agent->referrer());
            }else{
                redirect('group/checkoffs/listing');
            }
        }
    }
}