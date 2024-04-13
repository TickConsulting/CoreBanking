<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends Member_Controller{
    public $access_levels = array(1=>'Group Administrator',0=>'Member');
    protected $validation_rules = array(
        array(
            'field' =>  'first_name',
            'label' =>  'First Name',
            'rules' =>  'trim|required',
        ),array(
            'field' =>  'last_name',
            'label' =>  'Last Name',
            'rules' =>  'trim|required',
        ),array(
            'field' =>  'middle_name',
            'label' =>  'Middle Name',
            'rules' =>  'trim',
        ),array(
            'field' =>  'email',
            'label' =>  'Email address',
            'rules' =>  'trim|valid_email|callback_email_is_unique',
        ),
        array(
            'field' =>  'phone',
            'label' =>  'Phone Number',
            'rules' =>  'trim|valid_phone|required|callback_phone_is_unique',
        ),
        array(
            'field' =>  'id_number',
            'label' =>  'ID Number',
            'rules' =>  'trim|callback_id_number_is_unique',
        ),array(
            'field' =>  'membership_number',
            'label' =>  'Membership Number',
            'rules' =>  'trim|callback_membership_number_is_unique',
        ),
    );

    protected $loan_request_validation =  array(
        array(
            'field'=>'loan_request_comment',
            'label'=>'Loan Request comment',
            'rules'=>'trim'
        ),
        array(
            'field'=>'account_id',
            'label'=>'Disbursment Account',
            'rules'=>'trim|required'
        ),
    );

    protected $loan_request_validations =  array(
        array(
            'field'=>'loan_request_comment',
            'label'=>'Loan Request comment',
            'rules'=>'trim'
        ),
    );

    protected $transfer_options = array(
            1 => 'To contribution share',
            2 => 'To fines ',
            3 => 'To another loan',
            4 => 'To another member',
        );
     protected $loan_interest_rate_per = array(
            1   =>  'Per Day',
            2   =>  'Per Week',
            3   =>  'Per Month',
            4   =>  'Per Annum',
            5   =>  'For the whole loan repayment period'
        );
     protected $interest_types = array(
            1   =>  'Fixed Balance',
            2   =>  'Reducing Balance',
            3   =>  'Custom Interest Type'
        );

	function __construct(){
        parent::__construct();
        $this->load->model('members_m');
        $this->load->model('reports/reports_m');
        $this->load->model('group_roles/group_roles_m');
        $this->load->model('notifications/notifications_m');
        $this->load->model('deposits/deposits_m');
        $this->load->model('withdrawals/withdrawals_m');
        $this->load->model('contributions/contributions_m');
        $this->load->model('fine_categories/fine_categories_m');
        $this->load->model('statements/statements_m');
        $this->load->model('loans/loans_m');
        $this->load->model('loan_repayments/loan_repayments_m');
        $this->data['loan_interest_rate_per'] = $this->loan_interest_rate_per;
        $this->load->model('statements/statements_m');
        $this->load->library('files_uploader');
        $this->load->library('image_lib');
        $this->load->model('reports/reports_m');
        $this->load->model('sms/sms_m');
        $this->load->model('loan_types_m');
        $this->data['interest_types'] = $this->interest_types;

    }

    public function index(){
        $data = array();
        $this->template->title('Members')->build('shared/index',$data);
    }

    function contributions_summary(){
        $data = array();
        $this->template->title('Members')->build('shared/index',$data);
    }

    public function directory(){
        if($this->group->disable_member_directory){
            redirect('member');
        }
        $total_rows = $this->members_m->count_active_group_members();
        $pagination = create_pagination('member/members/directory/pages', $total_rows,25,5,TRUE);
        $data['group_role_options'] = $this->group_roles_m->get_group_role_options();
        $data['group_member_contribution_totals'] = $this->deposits_m->get_group_member_total_contributions_array();
        $data['group_member_fine_totals'] = $this->deposits_m->get_group_member_total_fines_array();
        $data['group_member_contribution_balance_totals'] = $this->statements_m->get_group_member_total_contribution_balances_array();
        $data['group_member_fine_balance_totals'] = $this->statements_m->get_group_member_total_fine_balances_array();
        $data['group_member_cumulative_contribution_balance_totals'] = $this->statements_m->get_group_member_total_cumulative_contribution_balances_array();
        $data['group_member_contribution_refund_totals'] = $this->withdrawals_m->get_group_member_total_contribution_refunds_array();
        $data['member_total_contribution_transfers_from_loans_array'] = $this->statements_m->get_group_member_total_contribution_transfers_from_loans_array();
        $data['member_total_contribution_transfers_from_loans_to_fine_array'] = $this->statements_m->get_group_member_total_contribution_transfers_from_loans_to_fine_array();
        $data['member_total_contribution_transfers_to_loan_array'] = $this->statements_m->get_group_member_total_contribution_transfers_to_loan_array();
        $data['pagination'] = $pagination;
        $data['posts'] = $this->members_m->limit($pagination['limit'])->get_active_group_members();
        $this->template->title('Member Directory')->build('member/directory',$data);
    }

    public function edit(){
        
        $id = $this->member->id;
        $post = $this->members_m->get_group_member($id);
        $post OR redirect('member/members');
        if($this->member->id == $post->id){
            //redirect('member/members/edit_my_profile');
        }
        $data['post'] = $post;
        $data['group_role_options'] = $this->group_roles_m->get_group_role_options();
        $data['next_of_kin_entries'] = $this->members_m->get_group_member_next_of_kin_entries($this->group->id,$post->id);
        $this->form_validation->set_rules($this->validation_rules);
        if($this->group->disable_member_edit_profile){
            redirect('member/members/view');
        }
        if($this->form_validation->run()){
            $groups_directory = './uploads/groups';
            if(!is_dir($groups_directory)){
                mkdir($groups_directory,0777,TRUE);
            }
            $avatar['file_name'] = '';
            if($_FILES['avatar']['name']){
                 $avatar = $this->files_uploader->upload('avatar',$groups_directory);
                 if($avatar){
                    if(is_file(FCPATH.$groups_directory.'/'.$post->avatar)){
                        if(unlink(FCPATH.$groups_directory.'/'.$post->avatar)){
                            $this->session->set_flashdata('info','Member profile picture successfully replaced');
                        }
                    }

                    if(is_file(FCPATH.'uploads/groups/'.$avatar['file_name'])){
                      $config['image_library'] = 'gd2';
                      $config['source_image'] = FCPATH.'uploads/groups/'.$avatar['file_name'];
                      $config['create_thumb'] = FALSE;
                      $config['maintain_ratio'] = TRUE;
                      $config['width'] = 100;
                      $config['height'] = 100;
                      $this->image_lib->clear();
                      $this->image_lib->initialize($config);
                      if($this->image_lib->resize()){

                      }else{
                        //$this->session->set_flashdata('warning','Member profile could not be resized');
                        //echo "Resize Failed.<br/>";
                      }
                    }

                 }
            }
            $user_input = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'middle_name' => $this->input->post('middle_name'),
                'email' => $this->input->post('email'),
                'phone' => valid_phone($this->input->post('phone')),
                'id_number' => $this->input->post('id_number'),
                'avatar' => $avatar['file_name']?:$post->avatar,
                'modified_on' => time(),
                'modified_by' => $this->user->id
            );
            $member_input = array(
                'membership_number'=> $this->input->post('membership_number'),
                'modified_on' => time(),
                'modified_by' => $this->user->id
            );
            if($user_update_result = $this->ion_auth->update($post->user_id,$user_input)){
                //do nothing for now
            }else{
                $this->session->set_flashdata('error','Could not update user profile');
            }
            if($member_update_result = $this->members_m->update($post->id,$member_input)){
                //do nothing for now
            }else{
                $this->session->set_flashdata('error','Could not update member profile');
            }
            if($member_update_result&&$user_update_result){
                $subject = 'Member profile update';
                $message = $this->user->first_name.' '.$this->user->last_name.' updated your membership profile.';
                $call_to_action = 'View profile';
                $call_to_action_link = "/group/members/view/".$post->id;
                $this->notifications->create($subject,$message,$this->user,$this->member->id,$post->user_id,$post->id,$this->group->id,$call_to_action,$call_to_action_link,1);
                $this->session->set_flashdata('success','Member profile updated successfully');
            }
            redirect('member/members/view');
        }else{
            //do nothing for now
        }
        $this->template->title('Edit My Profile',$post->first_name.' '.$post->last_name)->build('member/form',$data);
    }

    public function view($id = 0){
        if($id){

        }else{
            $id=$this->member->id;
        }
        if($this->group->enable_member_information_privacy){ 
            if($id == $this->member->id){

            }else{
                redirect("member/members/view/".$this->member->id);
            }
        }
        $id OR redirect('member/members');
        $post = $this->members_m->get_group_member($id);
        $post OR redirect('member/members');
         $ongoing_loan_amounts_payable = array();
        $ongoing_loan_amounts_paid = array();
        $base_where = array('member_id'=>$post->id,'is_fully_paid'=>0);
        $ongoing_member_loans = $this->loans_m->get_many_by($base_where);
        foreach ($ongoing_member_loans as $ongoing_member_loan){
            $ongoing_loan_amounts_payable[$ongoing_member_loan->id]
            = $this->loans_m->get_summation_for_invoice($ongoing_member_loan->id)->total_amount_payable;
            $ongoing_loan_amounts_paid[$ongoing_member_loan->id]
            = $this->loan_repayments_m->get_loan_total_payments($ongoing_member_loan->id);
        }

        $fully_paid_loan_amounts_payable = array();
        $fully_paid_loan_amounts_paid = array();
        $base_where = array('member_id'=>$post->id,'is_fully_paid'=>1);
        $fully_paid_member_loans = $this->loans_m->get_many_by($base_where);
        foreach ($fully_paid_member_loans as $fully_paid_member_loan){
            $fully_paid_loan_amounts_payable[$fully_paid_member_loan->id]
            = $this->loans_m->get_summation_for_invoice($fully_paid_member_loan->id)->total_amount_payable;
            $fully_paid_loan_amounts_paid[$fully_paid_member_loan->id]
            = $this->loan_repayments_m->get_loan_total_payments($fully_paid_member_loan->id);
        }
        $data['post'] = $post;
        $data['group_role_options'] = $this->group_roles_m->get_group_role_options();
        $data['contribution_options'] = $this->contributions_m->get_group_refundable_contribution_options();
        $data['fine_category_options'] = $this->fine_categories_m->get_group_options();
        /**
        $data['total_member_contributions'] = $this->deposits_m->get_group_member_total_contributions($post->id);
        $data['total_member_contribution_refunds'] = $this->withdrawals_m->get_group_member_total_contribution_refunds($post->id);
        $data['total_member_contribution_fines'] = $this->deposits_m->get_group_member_total_contribution_fines($post->id);
        $data['total_member_fines'] = $this->deposits_m->get_group_member_total_fines($post->id);
        $data['member_contribution_totals'] = $this->deposits_m->get_group_member_contribution_totals($post->id);
        $data['member_contribution_fine_totals'] = $this->deposits_m->get_group_member_contribution_fine_totals($post->id);
        $data['member_fine_totals'] = $this->deposits_m->get_group_member_fine_totals($post->id);
        $data['member_contribution_transfers_to'] = $this->statements_m->get_group_member_contribution_transfers_to_per_contribution_array($post->id);
        $data['member_contribution_transfers_from_loan_to_contribution'] = $this->statements_m->get_group_member_contribution_transfers_from_loan_to_contribution_per_contribution_array($post->id);

        $data['member_contribution_transfers_from'] = $this->statements_m->get_group_member_contribution_transfers_from_per_contribution_array($post->id);
        $data['member_contribution_transfers_from_contribution_to_fine_category'] = $this->statements_m->get_group_member_contribution_transfers_from_contribution_to_fine_category_per_contribution_array($post->id);
        $data['member_contribution_transfers_from_contribution_to_fine_category_fine_category_as_key'] = $this->statements_m->get_group_member_contribution_transfers_from_contribution_to_fine_category_fine_category_as_key_array($post->id);
        $data['member_contribution_transfers_from_contribution_to_fine_category_contribution_id_as_key'] = $this->statements_m->get_group_member_contribution_transfers_from_contribution_to_fine_category_contribution_id_as_key_array($post->id);
        $data['total_member_contribution_transfers_from_contribution_to_fine_category'] = $this->statements_m->get_group_member_total_contribution_transfers_from_contribution_to_fine_category($post->id);
        $data['total_member_contribution_transfers_from_loan_to_contribution'] = $this->statements_m->get_group_member_total_contribution_transfers_from_loan_to_contribution($post->id);
        $data['total_member_contribution_transfers_from_loan_to_fine'] = $this->statements_m->get_group_member_total_contribution_transfers_from_loan_to_fine($post->id);
        $data['total_member_contribution_transfers_to_loan'] = $this->statements_m->get_group_member_total_contribution_transfers_to_loan($post->id);
        $data['member_contribution_transfers_to_loan'] = $this->statements_m->get_group_member_contribution_transfers_to_loan_per_contribution_array($post->id);
        **/
        
        $data['total_member_contributions'] = $this->reports_m->get_group_member_total_contributions($post->id,$data['contribution_options']);
        $data['total_member_contributions_per_contribution_array'] = $this->reports_m->get_group_member_total_contributions_paid_per_contribution_array($id);
        $data['total_member_fines'] = $this->reports_m->get_group_member_total_fine_payments($this->group->id,0,0,$post->id);
        $data['total_fines_paid_per_member_array'] = $this->reports_m->get_group_total_fines_paid_per_member_array($id);
        $data['total_contribution_fines_paid_per_member_array'] = $this->reports_m->get_group_total_contribution_fines_paid_per_member_array($id);

        $data['member_contribution_refunds'] = $this->withdrawals_m->get_group_member_total_contribution_refunds_per_contribution_array($this->group->id,$id);
        $data['ongoing_member_loans'] = $ongoing_member_loans;
        $data['ongoing_loan_amounts_payable'] = $ongoing_loan_amounts_payable;
        $data['ongoing_loan_amounts_paid'] = $ongoing_loan_amounts_paid;
        $data['fully_paid_member_loans'] = $fully_paid_member_loans;
        $data['fully_paid_loan_amounts_payable'] = $fully_paid_loan_amounts_payable;
        $data['next_group_member_id'] = $this->_get_next_group_member($post->id);
        $data['next_of_kin_entries'] = $this->members_m->get_group_member_next_of_kin_entries($this->group->id,$post->id);
        $data['fully_paid_loan_amounts_paid'] = $fully_paid_loan_amounts_paid;

        $this->template->title($post->first_name.' '.$post->last_name)->build('shared/view',$data);
    }

    private function _get_next_group_member($current_member_id = 0) {
        $next_member_id = 0;
        $found_next_member_id = FALSE;
        $count = 1;
        foreach ($this->group_member_options as $member_id => $member_name) {
            if($count==1){
                $next_member_id = $member_id;
            }
            if ($found_next_member_id) {
                # code...
                return $member_id;
            }
            # code...
            if($current_member_id==$member_id){
                $found_next_member_id = TRUE;
            }
            $count++;
        }
        return $next_member_id;
    }

    public function email_is_unique(){
        $email = $this->input->post('email');
        if($email==''){
            return TRUE;
        }else{
            if($user = $this->ion_auth->identity_check($email)){
                if($this->input->post('user_id')==$user->id){
                    return TRUE;
                }else{
                    $this->form_validation->set_message('email_is_unique', 'The email address is already registered to another member.');
                    return FALSE;
                }
            }else{
                return TRUE;
            }
        }
    }

    public function phone_is_unique(){
        $phone = valid_phone($this->input->post('phone'));
        if($user = $this->ion_auth->identity_check($phone)){
            if($this->input->post('user_id')==$user->id){
                return TRUE;
            }else{
                $this->form_validation->set_message('phone_is_unique', 'The phone number is already registered to another member.');
                return FALSE;
            }
        }else{
            return TRUE;
        }
    }

    public function id_number_is_unique(){
        $id_number = $this->input->post('id_number');
        if($id_number==''){
            return TRUE;
        }else{
            if($user = $this->ion_auth->id_number_check($id_number)){
                if($this->input->post('user_id')==$user->id){
                    return TRUE;
                }else{
                    $this->form_validation->set_message('id_number_is_unique', 'The id number is already registered to another member.');
                    return FALSE;
                }
            }else{
                return TRUE;
            }
        }
    }

    public function membership_number_is_unique(){
        $membership_number = $this->input->post('membership_number');
        if($membership_number==''){
            return TRUE;
        }else{
            //$member = $this->members_m->get_member_by_membership_number($membership_number);
            //print_r($member);
            //die;
            if($member = $this->members_m->get_member_by_membership_number($membership_number)){
                if($this->input->post('user_id')==$member->user_id){
                    return TRUE;
                }else{
                    $this->form_validation->set_message('membership_number_is_unique', 'The membership number is already registered to another member.');
                    return FALSE;
                }
            }else{
                return TRUE;
            }
        }
    }


    function view_loan_requests($loan_application_id=0){       
        $post =  new stdClass();
        $currency = $this->group_currency;
        $this->form_validation->set_rules($this->loan_request_validations);
        if($this->form_validation->run()){ 
            $loan_request_comment = $this->input->post('loan_request_comment');
            if($loan_application_id){
                $get_loan_application_options = $this->loan_applications_m->get($loan_application_id);               
                if($get_loan_application_options){
                    $group_id = $get_loan_application_options->group_id;
                    $member_id = $this->member->id;
                    $user_id = $this->user->id;
                    $loan_amount = $get_loan_application_options->loan_amount; 
                    $loan_type_id = $get_loan_application_options->loan_type_id;
                    $get_loan_type_options = $this->loan_types_m->get($loan_type_id,$group_id);               
                    $get_group_member_loans_requests = $this->loans_m->get_loan_applications_member_requests($loan_type_id,$loan_application_id,$group_id,$member_id);     
                    foreach ($get_group_member_loans_requests as $key => $loan_request_details) {
                            $loan_request_application_id= $loan_request_details->id;
                            $loan_applicant_id = $loan_request_details->loan_request_applicant_user_id;
                            $guaranteed_amounts = $loan_request_details->amount;
                            $loan_applicant_member_id = $loan_request_details->loan_request_applicant_member_id;
                        }
                    $get_loan_applicant_details = $this->users_m->get($loan_applicant_id); 
                    if(!empty($get_group_member_loans_requests)){
                          if(isset($_POST['approve'])){
                             $input = array(
                                'is_approved'=>1,
                                'approved_on'=>time(),
                                'approve_comment'=>$loan_request_comment,
                                'loan_request_progress_status'=>3,
                                'active'=>1,
                                'modified_on'=>time(),
                                'modified_by'=>$this->user->id,
                            );
                            $action = 'approve';
                            $update_loan_application_status = array(
                                'active'=>1,
                                'modified_on'=>time(),
                                'modified_by'=>$this->user->id,
                                'status'=>3,
                                'modified_on'=>time(),
                                'modified_by'=>$this->user->id,
                            );
                            $update_loan_application = 1;
                            //$update_loan_application = $this->loan_applications_m->update($loan_application_id,$update_loan_application_status);
                           }else if(isset($_POST['decline'])){
                            $input = array(
                                'is_declined'=>1,
                                'declined_on'=>time(),
                                'decline_comment'=>$loan_request_comment,
                                'loan_request_progress_status'=>2,
                                'active'=>0,
                                'modified_on'=>time(),
                                'modified_by'=>$this->user->id,
                            );
                            $action = 'decline';
                            $update_loan_application_status = array(
                                'active'=>0,
                                'modified_on'=>time(),
                                'modified_by'=>$this->user->id,
                                'status'=>4,
                            );
                            $update_loan_application = $this->loan_applications_m->update($loan_application_id,$update_loan_application_status);                       
                            }
                            //$update_loan_request_application = $this->loans_m->update_loan_request_application($loan_request_application_id,$input);
                            $update_loan_request_application = 1;
                            if($update_loan_request_application){
                                $loan_request_status = $this->loans_m->get_group_loan_gurantorship_progress_status_request_array($group_id ,$loan_type_id,$loan_application_id); 
                                $guarantor_array =   array(
                                    'guarantor_user_id'=>$user_id,
                                    'guarantor_member_id' => $member_id,
                                    'first_name'=>$this->user->first_name,
                                    'last_name'=>$this->user->last_name,
                                    'guarantor_amount' => $guaranteed_amounts,
                                    'guarantor_phone_no'=>$this->user->phone,
                                );
                                $loan_applicant_array =  array(
                                    'loan_applicant_user_id'=>$get_loan_applicant_details->id,
                                    'loan_applicant_member_id' => $loan_applicant_member_id,
                                    'first_name'=>$get_loan_applicant_details->first_name,
                                    'last_name'=>$get_loan_applicant_details->last_name,
                                    'phone_no' =>$get_loan_applicant_details->phone,
                                );
                                $loan_details_array =   array(
                                    'loan_type_id'=>$loan_type_id,
                                    'loan_application_id' => $loan_application_id,
                                    'loan_amount' => $loan_amount,
                                    'loan_request_status'=>$loan_request_status,
                                    'group_id'=>$group_id,
                                    'currency'=>$currency,
                                    'loan_name'=>$get_loan_type_options->name,
                                    'action'=>$action,
                                );
                               $notify_loan_applicant_of_loan_status =  $this->messaging->notify_applicant_of_loan_request_status($guarantor_array,$loan_applicant_array,$loan_details_array);
                                    if($notify_loan_applicant_of_loan_status){
                                        redirect('member/loans/loan_guarantor_listing'); 
                                    }else{
                                        $this->session->set_flashdata('info',' Loan decline  failed:  Could not create  notification');
                                        redirect('member/loans/loan_guarantor_listing');                        
                                    }
                            }else{
                                $this->session->set_flashdata('error','Loan decline  failed:  could not decline loan request'); 
                            }
                    }else{
                        redirect('member/members/view_loan_requests_listing');   
                    }
                }else{
                    $this->session->set_flashdata('error','Failed: could not get group details');
                }                        
             }else{
                $this->session->set_flashdata('error','Loan decline  failed: loan decline failed loan applications details is missing');      
             }
        }else{
            foreach ($this->loan_request_validation as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $this->data['post'] = $post;
        $this->data['loan_application_id'] = $loan_application_id;        
        $this->template->title('List Loan Requests  ')->build('member/loan_request_listing',$this->data);
    }

    function view_eazzy_club_loan_requests($loan_application_id=0){
        $post =  new stdClass();
        $currency = $this->group_currency;
        $loan_request_status = array();
        $this->form_validation->set_rules($this->loan_request_validations);
        if($this->form_validation->run()){ 
            $loan_request_comment = $this->input->post('loan_request_comment');
            if($loan_application_id){
                $get_loan_application_options = $this->loan_applications_m->get($loan_application_id);               
                if($get_loan_application_options){
                    $group_id = $get_loan_application_options->group_id;
                    $member_id = $this->member->id;
                    $user_id = $this->user->id;
                    $loan_amount = $get_loan_application_options->loan_amount;
                    $member_supervisor_id = $get_loan_application_options->member_supervisor_id; 
                    $loan_type_id = $get_loan_application_options->loan_type_id;
                    $get_loan_type_options = $this->loan_types_m->get($loan_type_id,$group_id);               
                    $get_group_member_loans_requests = $this->loans_m->get_loan_applications_member_requests($loan_type_id,$loan_application_id,$group_id,$member_id);     
                    foreach($get_group_member_loans_requests as $key => $loan_request_details){
                        $loan_request_application_id= $loan_request_details->id;
                        $loan_applicant_id = $loan_request_details->loan_request_applicant_user_id;
                        $guaranteed_amounts = $loan_request_details->amount;
                        $loan_applicant_member_id = $loan_request_details->loan_request_applicant_member_id;
                    }
                    $member_details = $this->members_m->get_group_members_array($group_id);
                    //print_r($get_loan_application_options);
                    //print_r($member_details[$member_supervisor_id]); die();
                    $get_loan_applicant_details = $this->users_m->get($loan_applicant_id); 
                    if(!empty($get_group_member_loans_requests)){
                        if(isset($_POST['approve'])){
                            $input = array(
                                'is_approved'=>1,
                                'approved_on'=>time(),
                                'approve_comment'=>$loan_request_comment,
                                'loan_request_progress_status'=>3,
                                'active'=>1,
                                'modified_on'=>time(),
                                'modified_by'=>$this->user->id,
                            );
                            $action = 'approve';
                            $update_loan_application_status = array(
                                'active'=>1,
                                'modified_on'=>time(),
                                'modified_by'=>$this->user->id,
                                'status'=>3,
                                'modified_on'=>time(),
                                'modified_by'=>$this->user->id,
                            );
                            //$update_loan_application = 1;
                            $update_loan_application = $this->loan_applications_m->update($loan_application_id,$update_loan_application_status);
                        }else if(isset($_POST['decline'])){
                            $input = array(
                                'is_declined'=>1,
                                'declined_on'=>time(),
                                'decline_comment'=>$loan_request_comment,
                                'loan_request_progress_status'=>2,
                                'active'=>0,
                                'modified_on'=>time(),
                                'modified_by'=>$this->user->id,
                            );
                            $action = 'decline';
                            $update_loan_application_status = array(
                                'active'=>0,
                                'modified_on'=>time(),
                                'modified_by'=>$this->user->id,
                                'status'=>4,
                            );
                            //$update_loan_application = 1;
                            $update_loan_application = $this->loan_applications_m->update($loan_application_id,$update_loan_application_status);

                        }
                       // $update_loan_request_application = 1 ;
                        $update_loan_request_application = $this->loans_m->update_loan_request_application($loan_request_application_id,$input);
                        if($update_loan_request_application){
                            $loan_request_status = $this->loans_m->get_group_loan_gurantorship_progress_status_request_array($group_id ,$loan_type_id,$loan_application_id);                            
                            $guarantor_array =   array(
                                'guarantor_user_id'=>$user_id,
                                'guarantor_member_id' => $member_id,
                                'first_name'=>$this->user->first_name,
                                'last_name'=>$this->user->last_name,
                                'guarantor_amount' => $guaranteed_amounts,
                                'guarantor_phone_no'=>$this->user->phone,
                            );
                            $loan_applicant_array =  array(
                                'loan_applicant_user_id'=>$get_loan_applicant_details->id,
                                'loan_applicant_member_id' => $loan_applicant_member_id,
                                'first_name'=>$get_loan_applicant_details->first_name,
                                'last_name'=>$get_loan_applicant_details->last_name,
                                'phone_no' =>$get_loan_applicant_details->phone,
                            );
                            $loan_details_array =   array(
                                'loan_type_id'=>$loan_type_id,
                                'loan_application_id' => $loan_application_id,
                                'loan_amount' => $loan_amount,
                                'group_id'=>$group_id,
                                'currency'=>$currency,
                                'loan_name'=>$get_loan_type_options->name,
                                'action'=>$action,
                                'loan_request_status'=>$loan_request_status
                            );
                            $supervisor_details_array =  array(
                                'supervisor_user_id'=>$member_details[$member_supervisor_id]->user_id,
                                'supervisor_member_id' => $member_details[$member_supervisor_id]->id,
                                'supervisor_first_name'=>$member_details[$member_supervisor_id]->first_name,
                                'supervisor_last_name'=>$member_details[$member_supervisor_id]->last_name,
                                'supervisor_phone_no'=>$member_details[$member_supervisor_id]->phone
                            );
                            $notify_loan_applicant_of_loan_status=  $this->messaging->notify_loan_applicant_of_loan_request_status($guarantor_array,$loan_applicant_array,$loan_details_array,$supervisor_details_array);
                            if($notify_loan_applicant_of_loan_status){
                                $this->session->set_flashdata('info',' You have accepted to guarantee a loan ('.$get_loan_type_options->name.') of '.$currency.' '.number_to_currency($loan_amount).' and  you have  guaranteed '.$currency.' '.number_to_currency($guaranteed_amounts));
                               redirect('member/loans/loan_guarantor_listing'); 
                            }else{
                                $this->session->set_flashdata('info',' Loan  failed:  Could not create loan  notification');
                                redirect('member/loans/loan_guarantor_listing');                        
                            }
                        }else{
                            $this->session->set_flashdata('error','Loan decline  failed:  could not decline loan request'); 
                        }
                    }else{
                        redirect('member/members/view_loan_requests_listing');   
                    }
                }else{
                    $this->session->set_flashdata('error','Failed: could not get group details');
                }                        
             }else{
                $this->session->set_flashdata('error','Loan decline  failed: loan decline failed loan applications details is missing');      
             }
        }else{
            foreach ($this->loan_request_validation as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $this->data['post'] = $post;
        $this->data['loan_application_id'] = $loan_application_id;        
        $this->template->title('List Loan Requests  ')->build('member/loan_request_listing',$this->data); 
    }


    function view_loan_requests_listing(){
       $this->data['post'] = array();
       $this->template->title('Loan listing Requests')->build('member/loan_listing_status',$this->data); 
    }

    function view_loan_requests_status($loan_application_id){
        $post = new stdClass();
        $this->data['post'] = $post;        
        $this->data['loan_application_id'] = $loan_application_id;
        $this->template->title('Loan Listing  Requests')->build('member/loan_listing_status_action',$this->data);
        
    }

    function member_roles(){
        $result = $this->members_m->get_active_group_role_holder_options();
        $this->group->owner;
        print_r(  $this->group->owner); die();
    }


    function pending_loan_approval_request($loan_signatory_id=0){
       $loan_signatory_id; 
       $post =  new stdClass();
       $currency = $this->group_currency;
       $group_id = $this->group->id;
       $get_loan_signatories_details = $this->loans_m->get_loan_signatories($loan_signatory_id,$group_id);
       $loan_applications_id = isset($get_loan_signatories_details->loan_application_id)?$get_loan_signatories_details->loan_application_id:'';
       $get_loan_application_details = $this->loan_applications_m->get($loan_applications_id);
       $account_id = $this->input->post('account_id');
       $this->form_validation->set_rules($this->loan_request_validation);
       if($this->form_validation->run()){
        $loan_signatory_comment = $this->input->post('loan_request_comment');
        if(isset($_POST['approve'])){
            $action = 'approve';
        }elseif (isset($_POST['decline'])) {
            $action = 'decline';
        }
        $input =  array(
            'account_id'=>$account_id
        );
        $update_loan_account = $this->loan_applications_m->update($loan_applications_id,$input);
        $loan_approve = $this->loan_signatory_action($loan_signatory_id,$action);             
       }else{
            foreach ($this->loan_request_validation as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
       } 
       $this->data['post'] = $post;
       $this->data['loan_signatory_id'] = $loan_signatory_id;
       $this->data['account_id'] = isset($get_loan_application_details->account_id)?$get_loan_application_details->account_id:'';
       $this->data['active_accounts'] = $this->accounts_m->get_active_group_account_options();
       $this->template->title('Loan Signatory Approval Requests')->build('member/signatory_listing',$this->data); 
    }

    function loan_signatory_action($loan_signatory_id=0,$action=''){
        $group_id = $this->group->id;
        $currency = $this->group_currency;
        $signatory_member_id = $this->member->id;
        $signatory_user_id = $this->user->id;
        $loan_signatory_comment = $this->input->post('loan_request_comment');
        $get_loan_signatory_details = $this->loans_m->get_loan_signatories($loan_signatory_id,$group_id);
        $loan_application_id = $get_loan_signatory_details->loan_application_id;
        $loan_applicant_id = $get_loan_signatory_details->loan_request_applicant_user_id;
        $loan_applicant_member_id = $get_loan_signatory_details->loan_request_member_id; 
        $get_loan_applicant_user_details = $this->users_m->get($loan_applicant_id);
        $loan_amount = $get_loan_signatory_details->loan_amount;
        $loan_type_id = $get_loan_signatory_details->loan_type_id;
        $get_loan_type_details = $this->loan_types_m->get($loan_type_id);
        $get_all_signatories_loan_request = $this->loans_m->get_all_signatory_requests($loan_type_id,$loan_application_id,$group_id);
        $signatory_user_details = $this->users_m->get($signatory_user_id);        
        if(!empty($get_all_signatories_loan_request)){
            foreach ($get_all_signatories_loan_request as $key => $signatory_details) {
               $active[] = $signatory_details->active;               
            }
            if(in_array(1, $active)){             
                if($action =='approve'){
                    $data = array(
                        'is_approved'=>1,
                        'approve_comment'=>$loan_signatory_comment,
                        'loan_signatory_progress_status'=>3,
                        'modified_on'=>time(),
                        'modified_by'=>$this->user->id
                    ); 
                    $input = array(
                        'status'=>7,
                        'modified_by'=>$this->user->id,
                        'modified_on'=>time()
                    );
                }elseif ($action == 'decline') {
                    $data = array(
                        'is_declined'=>1,
                        'decline_comment'=>$loan_signatory_comment,
                        'loan_signatory_progress_status'=>2,
                        'active'=>0,
                        'modified_on'=>time(),
                        'modified_by'=>$this->user->id
                    );
                    $input = array(
                        'status'=>8,
                        'modified_by'=>$this->user->id,
                        'modified_on'=>time()
                    );
                }
                if($this->loan_applications_m->update($loan_application_id,$input)){
                    if($this->loans_m->update_loan_signatories($loan_signatory_id,$data)){
                        $notification =  $this->messaging->notify_loan_applicant_about_signatory_action($loan_signatory_id,$group_id,$loan_applicant_id,$loan_type_id,$signatory_member_id,$currency,$loan_applicant_member_id,$loan_application_id,$signatory_user_id,$action,$loan_amount);
                        if($notification){
                            $get_loan_application_details = $this->loan_applications_m->get($loan_application_id); ;
                            $this->session->set_flashdata('success','You have agreed to approve '.$get_loan_applicant_user_details->first_name.' '.$get_loan_applicant_user_details->last_name.' loan( '.$get_loan_type_details->name.') of  '.$currency.' '.number_to_currency($loan_amount).' as a signatory of the group');
                          redirect('member/loans/signatory_approvals_listing');
                        }else{
                            $this->session->set_flashdata('error','could not could not create loan notifications');
                        }
                    }else{
                       $this->session->set_flashdata('error','could not update loan signatory details'); 
                   }
                }else{
                    $this->session->set_flashdata('error','Loan application could not be edited');
                }
            }else{
                $this->session->set_flashdata('error','loan has been declined');
            }            
        }else{
        } 

    }
    

    function loan_listing($id,$generate_pdf=FALSE){
        $id or redirect('group/loans/listing');
        //echo $id; die();
        $loan = $this->loans_m->get_loan_and_member($id);
        if(!$loan){
            $this->session->set_flashdata('info','Sorry the loan does not exist');
            redirect('group/loans/listing');
        }

        $total_installment_payable = $this->loan_invoices_m->get_total_installment_loan_payable($id);
        $total_fines = $this->loan_invoices_m->get_total_loan_fines_payable($id);
        $total_transfers_out = $this->loan_invoices_m->get_total_loan_transfers_out($id);
        $total_paid = $this->loan_repayments_m->get_loan_total_payments($id);
        $loan_balance =$this->loans_m->get_loan_balance($id);
        $posts = $this->loans_m->get_loan_statement($id);

        $this->data['loan'] = $loan;
        $this->data['posts'] = $posts;
        $this->data['total_installment_payable'] = $total_installment_payable;
        $this->data['total_fines'] = $total_fines;
        $this->data['total_transfers_out'] = $total_transfers_out;
        $this->data['total_paid'] = $total_paid;
        $this->data['lump_sum_remaining'] = $this->loan_invoices_m->get_loan_lump_sum_as_date($id);
        $this->accounts = $this->accounts_m->get_group_account_options(FALSE);
        $this->data['accounts'] = $this->accounts;
        $this->data['deposit_options']=$this->transactions->deposit_method_options;
        $this->data['group'] = $this->group;
        $this->data['group_currency'] = $this->group_currency;
        $this->data['application_settings'] = $this->application_settings;
        $this->data['transfer_options'] = $this->transfer_options;
        if(is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){
            $this->data['group_logo'] = site_url('uploads/groups/'.$this->group->avatar);
        }else{
            $this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
        }
        if($generate_pdf==TRUE){
            $json_file = (json_encode($this->data));
            $response = $this->curl_post_data->curl_post_json_pdf($json_file,'https://pdfs.chamasoft.com/loan_statement',$loan->first_name.' Loan Statenent - '.$this->group->name);  
        }else{
            $this->template->title($loan->first_name.' '.$loan->last_name.' Loan Statement')->build('shared/loan_statement',$this->data); 
        }   
    }

    /*function ajax_get_signatory_loan_requests($signatory_loan_request_id=0){
        $signatory_loan_request_id ; //OR redirect();
        $group_id = $this->group->id;
        $get_loan_sinatory_details = $this->loans_m->get_loan_signatories($signatory_loan_request_id,$group_id);
        if(!empty($get_loan_sinatory_details)){
            $member_id = $this->member->id;
            $user_id = $this->user->id;            
            $member_id = $this->member->id;
            $user_id = $this->user->id;               
            $loan_type_id = $get_loan_sinatory_details->loan_type_id;
            $loan_application_id = $get_loan_sinatory_details->loan_application_id;
            $get_loans_applicant_requests = $this->loan_applications_m->get($loan_application_id);
            if(!empty($get_loans_applicant_requests)){ 
                $currency = $this->group_currency;
                $loan_amount = $get_loans_applicant_requests->loan_amount;
                $loan_applicant_id = $get_loans_applicant_requests->created_by;
                $get_loan_applicant_user_details = $this->users_m->get($loan_applicant_id);
                echo $get_loan_applicant_user_details->first_name. " has requested a loan(".$get_loans_applicant_requests->name.") of amount ".$currency. " ".$loan_amount."  <br>" ;
            }else{
            echo'<div class="alert alert-danger">
                   <button class="close" data-dismiss="alert"></button>
                   <strong>Error!</strong> Could not find loan application details
                </div>';
            }
        }else{
        echo'<div class="alert alert-danger">
               <button class="close" data-dismiss="alert"></button>
               <strong>Error!</strong> Could not find loan signatory details
            </div>';
        }
    }*/


    function ajax_get_signatory_loan_requests(){
        if($signatory_loan_request_id = $this->input->post('signatory_loan_request_id')){
            $validation_rules = array(
                array(
                    'field' => 'signatory_loan_request_id',
                    'label' => 'Signatory id',
                    'rules' => 'trim|required|numeric',
                ),
            );
            $this->form_validation->set_rules($validation_rules);
            $response = array();
            $html = '';
            if($this->form_validation->run()){
                $group_id = $this->group->id;
                $signatory_loan_request_id = $this->input->post('signatory_loan_request_id');
                $currency = $this->group_currency;
                $signatory_details = $this->loans_m->get_loan_signatories($signatory_loan_request_id,$group_id);
                if($signatory_details){
                    $loan_application = $this->loan_applications_m->get($signatory_details->loan_application_id);
                    if($loan_application){
                        $user_details = $this->users_m->get($signatory_details->loan_request_applicant_user_id);
                        $html.= $user_details->first_name. ' has requested a loan('.$loan_application->name.') of amount '.$this->group_currency. ' '.$loan_application->loan_amount ;
                        $response = array(
                            'status' => 200,
                            'message' => 'ok',
                            'html' => $html,
                        ); 
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not find loan application details.',
                            'html' => '',
                        );   
                    }                 
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find signatory details.',
                        'html' => '',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Validation',
                    'html' => validation_errors(),
                );
            }
            echo json_encode($response);
        }

    }

    function ajax_get_loan_requests($loan_application_id=0){
        $response = array();
        $html ='';
        if($loan_application_id){
            $get_loan_application_options = $this->loan_applications_m->get($loan_application_id);
            if(!empty($get_loan_application_options)){
                $group_id = $get_loan_application_options->group_id;                
                $member_id = $this->member->id;
                $user_id = $this->user->id;               
                $loan_type_id = $get_loan_application_options->loan_type_id;
                $get_group_member_loans_requests = $this->loans_m->get_loan_applications_member_requests($loan_type_id,$loan_application_id,$group_id,$member_id);
                if(count($get_group_member_loans_requests) > 0){
                    foreach ($get_group_member_loans_requests as $key => $loan_request_details) {
                         $loan_request_application_id= $loan_request_details->id;
                         $loan_applicant_id = $loan_request_details->loan_request_applicant_user_id;
                         $guaranteed_amounts = $loan_request_details->amount;
                         $loan_applicant_member_id = $loan_request_details->loan_request_applicant_member_id;
                     }
                     if($loan_applicant_id){
                          $currency = $this->group_currency;
                          $get_loan_applicant_user_details = $this->users_m->get($loan_applicant_id);
                          $get_guarantor_user_details = $this->users_m->get($user_id);
                          $html.=''.$get_loan_applicant_user_details->first_name. " has requested you  be his guarantor  of loan(".$get_loan_application_options->name.") of amount ".$currency. " ".$guaranteed_amounts."  <br>" ;                          
                          $response = array(
                                'status' => 200,
                                'message' => 'ok',
                                'html' => $html,
                            );                                                   

                     }else{ 
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not get loan apllicant details',
                            'html' => '',
                        );
                     }                     
                   
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => ' Could not get member loans requests',
                        'html' => '',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find loans details',
                    'html' => '',
                );            
            }             
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find loan request details',
                'html' => '',
            );
        }
        echo json_encode($response);
         
    }

    function decline_loan_requests(){
     $loan_application_id = $this->input->post('loan_application_id');
     $loan_request_comment = $this->input->post('loan_request_comment');
     //print_r($loan_application_id); die();
     if($loan_application_id){
        $get_loan_application_options = $this->loan_applications_m->get($loan_application_id);
        $group_id = $get_loan_application_options->group_id;
        //print_r($loan_application_id); die();
        $member_id = $this->member->id;
        $user_id = $this->user->id;
        //print_r($user_id); die();
        $loan_type_id = $get_loan_application_options->loan_type_id;
        $get_group_member_loans_to_decline = $this->loans_m->get_loan_applications_member_requests($loan_type_id,$loan_application_id,$group_id,$member_id);      
        foreach ($get_group_member_loans_to_decline as $key => $loan_request_details) {
                 $loan_request_application_id= $loan_request_details->id;
                 $loan_applicant_id = $loan_request_details->loan_request_applicant_user_id;
                 $guaranteed_amounts = $loan_request_details->amount;
                 $loan_applicant_member_id = $loan_request_details->loan_request_applicant_member_id;
             }
        if($get_group_member_loans_to_decline){
            $input = array(
                'is_declined'=>1,
                'declined_on'=>time(),
                'decline_comment'=>$loan_request_comment,
                'loan_request_progress_status'=>2,
                'modified_on'=>time(),
                'modified_by'=>$this->user->id,
            );
           $update_loan_request_application = $this->loans_m->update_loan_request_application($loan_request_application_id,$input);
            if($update_loan_request_application){

               $notify_loan_applicant_of_loan_decline =  $this->messaging->notify_loan_applicant_of_loan_rquest_decline($group_id,$loan_applicant_id,$loan_type_id,$member_id,$guaranteed_amounts,$currency,$loan_applicant_member_id,$loan_application_id);

               if($notify_loan_applicant_of_loan_decline){
                  echo 'success  notification created ';
               }else{
                echo 'could not create loan decline notification';
               }
            }else{
                echo 'could not decline loan request';
            }
        }else{
            echo 'could not get loan details to decline';
        }        
     }else{
        $this->session->set_flashdata('error','Loan decline  failed: loan decline failed loan applications details is missing');      
     }

    }

    function approval_requests(){
        $this->data['guarantor_requests'] = $this->loans_m->get_loan_application_guarantorship_requests_by_member_id($this->member->id ,$this->group->id);
        $this->data['supervisor_recommendations'] = $this->loans_m->get_member_supervisor_recommendations($this->group->id,$this->member->id);

        $this->template->title('My Pending Approvals')->build('shared/pending_approvals',$this->data);
    }

    function change_password(){
        $data = array();
        $validation_rules =  array(
            array(
                    'field' =>  'password',
                    'label' =>  'Password',
                    'rules' =>  'trim|required|min_length[8]|max_length[20]',
                ),
            array(
                    'field' =>  'conf_password',
                    'label' =>  'Confirm Password',
                    'rules' =>  'trim|required|matches[password]',
                )
        );
        $this->form_validation->set_rules($validation_rules);
        if($this->form_validation->run())
        {
            $input = array(
                'password' => $this->input->post('password')
            );
            $update = $this->ion_auth->update($this->user->id, $input);
            if($update)
            {
                $this->session->set_flashdata('success',"Password Change Successful");
            }
            else{
                $this->session->set_flashdata('error',"Password Change Failed");
            }
            redirect("group/members/view/".$this->member->id);
        }
        $this->template->title($this->user->first_name.' '.$this->user->last_name,"Change Password")->build('shared/change_password',$data);
    }

    

    

}
