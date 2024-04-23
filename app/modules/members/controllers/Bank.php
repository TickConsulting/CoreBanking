<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bank extends Bank_Controller{

    public $error_feedback = array();
    public $access_levels = array(1=>'Group Administrator',0=>'Member');
    
    public $organization_roles = array(
        '1' => 'Payroll Accountant',
        '2' => 'Sacco Officer ',
        '3' => 'Credit committee',
        '4' => 'Sacco Manager'
    );

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
            'field' =>  'phone',
            'label' =>  'Phone Number',
            'rules' =>  'trim|required|callback_phone_is_unique',
        ),array(
            'field' =>  'email',
            'label' =>  'Email address',
            'rules' =>  'trim|valid_email|callback_email_is_unique',
        ),array(
            'field' =>  'id_number',
            'label' =>  'ID Number',
            'rules' =>  'trim|callback_id_number_is_unique',
        ),array(
            'field' =>  'membership_number',
            'label' =>  'Membership Number',
            'rules' =>  'trim|callback_membership_number_is_unique',
        ),array(
            'field' =>  'group_role_id',
            'label' =>  'Group Role',
            'rules' =>  'trim|numeric|callback_group_role_assignment_is_unique',
        ),
        array(
            'field' =>  'organization_role_id',
            'label' =>  'Organization Role',
            'rules' =>  'trim|numeric|callback_organization_role_assignment_is_unique',
        ),array(
            'field' =>  'is_admin',
            'label' =>  'Access Level',
            'rules' =>  'trim|numeric',
        ),array(
            'field' =>  'date_of_birth',
            'label' =>  'Date of Birth',
            'rules' =>  'trim',
        ),
    );

	function __construct(){
        parent::__construct();
        $this->load->model('members_m');
        $this->load->model('languages/languages_m');
        $this->load->model('group_roles/group_roles_m');
        $this->load->model('notifications/notifications_m');
        $this->load->model('deposits/deposits_m');
        $this->load->model('organization_roles/organization_roles_m');
        $this->load->model('withdrawals/withdrawals_m');
        $this->load->model('contributions/contributions_m');
        $this->load->model('fine_categories/fine_categories_m');
        $this->load->model('statements/statements_m');
        $this->load->model('loans/loans_m');
        $this->load->model('loan_repayments/loan_repayments_m');
        $this->load->model('statements/statements_m');
        $this->load->model('reports/reports_m');
        $this->load->model('users/users_m');
        $this->load->library('files_uploader');
        $this->load->library('setup_tasks_tracker');
        $this->load->library('transactions');
        $this->load->library('group_members');
        $this->load->library('image_lib');
        $this->load->library('excel_library');
        $this->load->helper('form');
    }

    public function index(){
        $this->template->title(translate('Members'))->build('bank/directory');
    }

    public function invite_members(){
        $data = array();
        // check if the group has a join in code.
        if(!$this->group->join_code){
            // generate the join code
            $join_code = strtolower(random_string('alnum',6)).'-'.$this->group->id;
            $input = array(
                'join_code' => $join_code,
                'modified_on' => time(),
                'modified_by' => $this->user->id,
            );
            if($result = $this->groups_m->update($this->group->id,$input)){
                $data['join_code'] = $join_code;
            }else{
                redirect('bank/members');
            }
        }else{
            $data['join_code'] = $this->group->join_code;
        }
        $this->template->title(translate('Invite Members'))->build('bank/invite_members',$data);
    }

    public function add_users(){
        // if(!$this->group->enable_add_members_manually){
        //     redirect('bank/members/invite_members');
        // }
        $data = array();
        $posts = $_POST;
        $errors = array();
        $error_messages = array();
        $successes = array();
        $phones = array();
        $emails = array();
        $group_roles = array();
        $entries_are_valid = TRUE;
        if($this->input->post('submit')){
            if(!empty($posts)){
                if(isset($posts['first_names'])){
                    $count = count($posts['first_names']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['first_names'][$i])&&isset($posts['last_names'][$i])&&isset($posts['phones'][$i])&&isset($posts['emails'][$i])):
                            //first names
                                if($posts['first_names'][$i]==''){
                                    $successes['first_names'][$i] = 0;
                                    $errors['first_names'][$i] = 1;
                                    $error_messages['first_names'][$i] = 'Please enter a first name';
                                    $entries_are_valid = FALSE;
                                }else{
                                    $successes['first_names'][$i] = 1;
                                    $errors['first_names'][$i] = 0;
                                }
                            //last names
                                if($posts['last_names'][$i]==''){
                                    $successes['last_names'][$i] = 0;
                                    $errors['last_names'][$i] = 1;
                                    $error_messages['last_names'][$i] = 'Please enter a last name';
                                    $entries_are_valid = FALSE;
                                }else{
                                    $successes['last_names'][$i] = 1;
                                    $errors['last_names'][$i] = 0;
                                }
                            //phones
                                if($posts['phones'][$i]==''){
                                    $successes['phones'][$i] = 0;
                                    $errors['phones'][$i] = 1;
                                    $error_messages['phones'][$i] = 'Please enter a phone number';
                                    $entries_are_valid = FALSE;
                                }else{
                                    if(valid_phone($posts['phones'][$i])){
                                        if(in_array($posts['phones'][$i],$phones)){
                                            $successes['phones'][$i] = 0;
                                            $errors['phones'][$i] = 1;
                                            $error_messages['phones'][$i] = 'Please enter another phone number, you cannot have duplicated phone numbers';
                                            $entries_are_valid = FALSE;
                                        }else{
                                            $successes['phones'][$i] = 1;
                                            $errors['phones'][$i] = 0;
                                            $phones[] = $posts['phones'][$i];
                                        }
                                    }else{
                                        $successes['phones'][$i] = 0;
                                        $errors['phones'][$i] = 1;
                                        $error_messages['phones'][$i] = 'Please enter a valid phone number';
                                        $entries_are_valid = FALSE;
                                    }
                                }
                                //emails
                                if($posts['emails'][$i]==''){
                                    $successes['emails'][$i] = 1;
                                    $errors['emails'][$i] = 0;
                                }else{
                                    if(valid_email($posts['emails'][$i])){
                                        if(in_array($posts['emails'][$i],$emails)){
                                            $successes['emails'][$i] = 0;
                                            $errors['emails'][$i] = 1;
                                            $error_messages['emails'][$i] = 'Please enter another email address, you cannot have duplicated email addresses';
                                            $entries_are_valid = FALSE;
                                        }else{
                                            $successes['emails'][$i] = 1;
                                            $errors['emails'][$i] = 0;
                                            $emails[] = $posts['emails'][$i];
                                        }
                                    }else{
                                        $successes['emails'][$i] = 0;
                                        $errors['emails'][$i] = 1;
                                        $error_messages['emails'][$i] = 'Please enter a valid email addresses';
                                        $entries_are_valid = FALSE;
                                    }
                                }
                                //Group roles
                                if($posts['group_roles'][$i]==''){
                                    $successes['group_roles'][$i] = 1;
                                    $errors['group_roles'][$i] = 0;
                                }else{
                                    if(in_array($posts['group_roles'][$i],$group_roles)){
                                        $successes['group_roles'][$i] = 0;
                                        $errors['group_roles'][$i] = 1;
                                        $error_messages['group_roles'][$i] = 'You have already assigned this role to another member. Two members cannot share the same role.';
                                        $entries_are_valid = FALSE;
                                    }else{
                                        $successes['group_roles'][$i] = 1;
                                        $errors['group_roles'][$i] = 0;
                                        $group_roles[] = $posts['group_roles'][$i];
                                    }
                                }
                        endif;
                    endfor;
                }
            }

            if($entries_are_valid){
                if(isset($posts['first_names'])){
                    $count = count($posts['first_names']);
                    $successful_invitations_count = 0;
                    $unsuccessful_invitations_count = 0;
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['first_names'][$i])&&isset($posts['last_names'][$i])&&isset($posts['phones'][$i])&&isset($posts['emails'][$i])):
                            $send_invitation_sms = isset($posts['send_invitation_sms'][$i])?$posts['send_invitation_sms'][$i]:0;
                            $send_invitation_email = isset($posts['send_invitation_email'][$i])?$posts['send_invitation_email'][$i]:0;
                            $first_name = strip_tags($posts['first_names'][$i]);
                            $last_name = strip_tags($posts['last_names'][$i]);
                            if($this->group_members->add_member_to_group($this->group,$first_name,$last_name,$posts['phones'][$i],$posts['emails'][$i],$send_invitation_sms,$send_invitation_email,$this->user,$this->member->id,$posts['group_roles'][$i])){
                                $successful_invitations_count++;
                            }else{
                                $unsuccessful_invitations_count++;
                            }
                        endif;
                    endfor;
                    if($successful_invitations_count){
                        if($successful_invitations_count==1){
                            $this->session->set_flashdata('success',$successful_invitations_count.' member successfully added to your group.');
                        }else{
                            $this->session->set_flashdata('success',$successful_invitations_count.' members successfully added to your group.');
                        }
                    }
                    if($unsuccessful_invitations_count){
                        if($unsuccessful_invitations_count==1){
                            $this->session->set_flashdata('warning',$unsuccessful_invitations_count.' member was not added to your group.');
                        }else{
                            $this->session->set_flashdata('warning',$unsuccessful_invitations_count.' members were not added to your group.');
                        }
                    }
                    $this->group_members->set_active_group_size($this->group->id,TRUE);
                    $this->setup_tasks_tracker->set_completion_status('add-group-members',$this->group->id,$this->user->id);
                    redirect('bank/members/listing');
                }
            }else{
                $this->session->set_flashdata('error','There are some errors on the form. Please review and try again.');
            }
        }
        $data['posts'] = $posts;
        $data['group_role_options'] = $this->group_roles_m->get_group_role_options()+array(''=>'Member','0'=>'--Add new role--');
        $data['errors'] = $errors;
        $data['error_messages'] = $error_messages;
        $data['successes'] = $successes;
        $this->template->title(translate('Add Users'))->build('bank/add_members',$data);
    }
    function ajax_active_group_member_options_using_name(){     
        $result = $this->members_m->get_ajax_active_group_member_options_using_name();
        return $result;
    }
    public function ajax_add_members(){
        $group_role_options = $this->group_roles_m->get_group_role_options();
        $data = array();
        $posts = $_POST;
        $errors = array();
        $error_messages = array();
        $successes = array();
        $phones = array();
        $emails = array();
        $group_roles = array();
        $entries_are_valid = TRUE;
        if($this->input->post('submit')){
            if(!empty($posts)){
                if(isset($posts['first_names'])){
                    $count = count($posts['first_names']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['first_names'][$i])&&isset($posts['last_names'][$i])&&isset($posts['phones'][$i])&&isset($posts['emails'][$i])):
                            //first names
                                if($posts['first_names'][$i]==''){
                                    $successes['first_names'][$i] = 0;
                                    $errors['first_names'][$i] = 1;
                                    $error_messages['first_names'][$i] = 'Please enter a first name';
                                    $entries_are_valid = FALSE;
                                }else{
                                    $successes['first_names'][$i] = 1;
                                    $errors['first_names'][$i] = 0;
                                }
                            //last names
                                if($posts['last_names'][$i]==''){
                                    $successes['last_names'][$i] = 0;
                                    $errors['last_names'][$i] = 1;
                                    $error_messages['last_names'][$i] = 'Please enter a last name';
                                    $entries_are_valid = FALSE;
                                }else{
                                    $successes['last_names'][$i] = 1;
                                    $errors['last_names'][$i] = 0;
                                }
                            //phones
                                if($posts['phones'][$i]==''){
                                    $successes['phones'][$i] = 0;
                                    $errors['phones'][$i] = 1;
                                    $error_messages['phones'][$i] = 'Please enter a phone number';
                                    $entries_are_valid = FALSE;
                                }else{
                                    if(valid_phone($posts['phones'][$i])){
                                        if(in_array($posts['phones'][$i],$phones)){
                                            $successes['phones'][$i] = 0;
                                            $errors['phones'][$i] = 1;
                                            $error_messages['phones'][$i] = 'Please enter another phone number, you cannot have duplicated phone numbers';
                                            $entries_are_valid = FALSE;
                                        }else{
                                            $successes['phones'][$i] = 1;
                                            $errors['phones'][$i] = 0;
                                            $phones[] = $posts['phones'][$i];
                                        }
                                    }else{
                                        $successes['phones'][$i] = 0;
                                        $errors['phones'][$i] = 1;
                                        $error_messages['phones'][$i] = 'Please enter a valid phone number';
                                        $entries_are_valid = FALSE;
                                    }
                                }
                               //emails
                                if($posts['emails'][$i]==''){
                                    $successes['emails'][$i] = 1;
                                    $errors['emails'][$i] = 0;
                                }else{
                                    if(valid_email($posts['emails'][$i])){
                                        if(in_array($posts['emails'][$i],$emails)){
                                            $successes['emails'][$i] = 0;
                                            $errors['emails'][$i] = 1;
                                            $error_messages['emails'][$i] = 'Please enter another email address, you cannot have duplicated email addresses';
                                            $entries_are_valid = FALSE;
                                        }else{
                                            $successes['emails'][$i] = 1;
                                            $errors['emails'][$i] = 0;
                                            $emails[] = $posts['emails'][$i];
                                        }
                                    }else{
                                        $successes['emails'][$i] = 0;
                                        $errors['emails'][$i] = 1;
                                        $error_messages['emails'][$i] = 'Please enter a valid email addresses';
                                        $entries_are_valid = FALSE;
                                    }
                                }
                                //Group roles
                                if($posts['group_roles'][$i]==''){
                                    $successes['group_roles'][$i] = 1;
                                    $errors['group_roles'][$i] = 0;
                                }else{
                                    if(in_array($posts['group_roles'][$i],$group_roles)){
                                        $successes['group_roles'][$i] = 0;
                                        $errors['group_roles'][$i] = 1;
                                        $error_messages['group_roles'][$i] = 'You have already assigned this role to another member. Two members cannot share the same role.';
                                        $entries_are_valid = FALSE;
                                    }else{
                                        $successes['group_roles'][$i] = 1;
                                        $errors['group_roles'][$i] = 0;
                                        $group_roles[] = $posts['group_roles'][$i];
                                    }
                                }
                        endif;
                    endfor;
                }
            }
            if($entries_are_valid){
                $member_id_array = array();
                if(isset($posts['first_names'])){
                    $count = count($posts['first_names']);
                    $successful_invitations_count = 0;
                    $unsuccessful_invitations_count = 0;
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['first_names'][$i])&&isset($posts['last_names'][$i])&&isset($posts['phones'][$i])&&isset($posts['emails'][$i])):
                            $send_invitation_sms = isset($posts['send_invitation_sms'][$i])?$posts['send_invitation_sms'][$i]:0;
                            $send_invitation_email = isset($posts['send_invitation_email'][$i])?$posts['send_invitation_email'][$i]:0;
                            $first_name = strip_tags($posts['first_names'][$i]);
                            $last_name = strip_tags($posts['last_names'][$i]);
                            if($member_id = $this->group_members->add_member_to_group($this->group,$first_name,$last_name,$posts['phones'][$i],$posts['emails'][$i],$send_invitation_sms,$send_invitation_email,$this->user,$this->member->id,$posts['group_roles'][$i])){
                                $member_id_array[] = $member_id;
                                $successful_invitations_count++;
                            }else{
                                $unsuccessful_invitations_count++;
                            }
                        endif;
                    endfor;
                    if($successful_invitations_count){
                        if($successful_invitations_count==1){
                            //$this->session->set_flashdata('success',$successful_invitations_count.' member successfully added to your group.');
                        }else{
                            //$this->session->set_flashdata('success',$successful_invitations_count.' members successfully added to your group.');
                        }
                    }
                    if($unsuccessful_invitations_count){
                        if($unsuccessful_invitations_count==1){
                            //$this->session->set_flashdata('warning',$unsuccessful_invitations_count.' member was not added to your group.');
                        }else{
                            //$this->session->set_flashdata('warning',$unsuccessful_invitations_count.' members were not added to your group.');
                        }
                    }
                    $this->group_members->set_active_group_size($this->group->id,TRUE);
                    $this->setup_tasks_tracker->set_completion_status('add-group-members',$this->group->id,$this->user->id);
                    if($members = $this->members_m->get_group_members_by_member_id_array($this->group->id,$member_id_array)){
                        $group_members = array();
                        foreach ($members as $member) {
                            # code...
                            if(isset($group_role_options[$member->group_role_id])){
                                $member->group_role_name = $group_role_options[$member->group_role_id];
                            }else{
                                $member->group_role_name = "Member";
                            }
                            $group_members[] = $member;
                        }
                        echo json_encode($group_members);
                        $this->session->set_userdata('success_feedback',"");
                        $this->session->set_flashdata('success',"");
                        $this->session->set_flashdata('info',"");

                    }else{
                        echo 'Could not fetch added members';
                    }
                }
            }else{
                echo 'There are some errors on the form. Please review and try again.';
            }
        }else{
            //print_r($_POST);
            // echo 'Form not submitted.';
        }
    }

    public function ajax_add_member(){
        $validation_rules = array(
            array(
                'field' =>  'first_name',
                'label' =>  'First Name',
                'rules' =>  'trim|required',
            ),array(
                'field' =>  'last_name',
                'label' =>  'Last Name',
                'rules' =>  'trim|required',

            ),array(
                'field' =>  'group_role_id',
                'label' =>  'Group Role',
                'rules' =>  'trim|required|numeric',
            ),array(
                'field' =>  'middle_name',
                'label' =>  'Middle Name',
                'rules' =>  'trim',
            ),array(
                'field' =>  'phone',
                'label' =>  'Phone Number',
                'rules' =>  'trim|required|valid_phone',
            ),array(
                'field' =>  'email',
                'label' =>  'Email address',
                'rules' =>  'trim|valid_email',
            )
        );
        $response = array();
        $this->form_validation->set_rules($validation_rules);
        if($this->form_validation->run()){
            $first_name = $this->input->post('first_name');
            $middle_name =$this->input->post('middle_name')?:'';
            $last_name = $this->input->post('last_name');
            $phone = $this->input->post('phone');
            $email = $this->input->post('email');
            $group_role_id = $this->input->post('group_role_id');
            $send_invitation_sms = $this->input->post('send_sms_notification');
            $send_invitation_email = $this->input->post('send_email_notification');
            if($member_id = $this->group_members->add_member_to_group($this->group,$first_name,$last_name,$phone,$email,$send_invitation_sms,$send_invitation_email,$this->user,$this->member->id,$group_role_id,$middle_name,"","",FALSE,$id_number)){
                if($member = $this->members_m->get_group_member($member_id)){
                    $response = array(
                        'status' => 1,
                        'member' => $member,
                        'message' => 'User successfully added',
                        'refer'=>site_url('bank/members/listing')
                    );
                }else{
                    echo "Could not find any member";
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find any member',
                        'validation_errors' => '',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not add member to the group',
                    'validation_errors' => '',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'There are some errors on the form. Please review and try again.',
                'validation_errors' => $this->form_validation->error_array(),
            );
        }
        echo json_encode($response);
    }

    public function import_members(){
        if(!$this->group->enable_import_members_manually){
            // $this->session->set_flashdata('success','You Are Not Allowed To Import Members. Kindly Contact Support.');
            redirect('bank/members/invite_members');
        }
        $data = array();
        if($this->input->post('import')){
            $directory = './uploads/files/csvs';
            if(!is_dir($directory)){
                mkdir($directory,0777,TRUE);
            }
            $config['upload_path'] = FCPATH . 'uploads/files/csvs/';
            $config['allowed_types'] = 'xls|xlsx|csv';
            $config['max_size'] = '1024';
            $this->load->library('upload',$config);
            if($this->upload->do_upload('member_list_file')){
                $successful_invitations_count = 0;
                $unsuccessful_invitations_count = 0;
                $upload_data = $this->upload->data();
                $file_path = $upload_data['full_path'];
                $this->load->library('Excel');
                $excel_sheet = new PHPExcel();
                if(file_exists($file_path)){
                    $file_type = PHPExcel_IOFactory::identify($file_path);
                    $excel_reader = PHPExcel_IOFactory::createReader($file_type);
                    $excel_book = $excel_reader->load($file_path);
                    $sheet = $excel_book->getSheet(0);
                    $allowed_column_headers = array('First Name','Last Name','Phone','Email','ID Number','Membership Number','Date of Birth(DD-MM-YYYY)','Location','Next of Kin Full Name','Next of Kin ID Number','Next of Kin Phone','Next of Kin Relationship');
                    $count = count($allowed_column_headers)-1;
                    for($column = 0; $column <= $count; $column++){
                        $value = $sheet->getCellByColumnAndRow($column, 1)->getValue();
                        if(in_array(trim($value), $allowed_column_headers)){
                            $column_validation = true;
                        }else{
                            $column_validation = false;
                            break;
                        }
                    }

                    if($column_validation){
                        $highestRow = $sheet->getHighestRow();
                        $members = array();
                        for($row = 2; $row <= $highestRow; $row++){
                            $first_name = '';
                            $last_name = '';
                            $phone = '';
                            $email = '';
                            for($column = 0; $column <= $count; $column++){
                                if($column == 0){
                                    $first_name = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }else if($column == 1){
                                    $last_name = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }else if($column == 2){
                                    $phone = valid_phone($sheet->getCellByColumnAndRow($column,$row)->getValue());
                                }else if($column == 3){
                                    $email = filter_var($sheet->getCellByColumnAndRow($column,$row)->getValue(), FILTER_SANITIZE_EMAIL);
                                }else if($column == 4){
                                    $id_number = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }else if($column == 5){
                                    $membership_number = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }else if($column == 6){
                                    $date_of_birth = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }else if($column == 7){
                                    $physical_address = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }else if($column == 8){
                                    $next_of_kin_full_name = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }else if($column == 9){
                                    $next_of_kin_id_number = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }else if($column == 10){
                                    $next_of_kin_phone = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }else if($column == 11){
                                    $next_of_kin_relationship = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }
                            }
                            $members[] = array(
                                'first_name' => $first_name,
                                'last_name' => $last_name,
                                'phone' => $phone,
                                'email' => $email,
                                'id_number' => $id_number,
                                'membership_number' => $membership_number,
                                'date_of_birth' => $date_of_birth,
                                'physical_address' => $physical_address,
                                'next_of_kin_full_name' => $next_of_kin_full_name,
                                'next_of_kin_id_number' => $next_of_kin_id_number,
                                'next_of_kin_phone' => $next_of_kin_phone,
                                'next_of_kin_relationship' => $next_of_kin_relationship,
                            );
                        }

                        if(empty($members)){
                            $this->session->set_flashdata('info','The Member list file does not have any branches to import');
                        }else{
                            $members = (object)$members;
                            $successes = 0;
                            $duplicates = 0;
                            $ignores = 0;
                            $errors = 0;
                            $phones = array();
                            $emails = array();
                            $row = 2;
                            foreach($members as $member){
                                $member = (object)$member;
                                if($member->first_name||$member->last_name||$member->phone||$member->email){
                                    if($member->first_name&&$member->last_name&&valid_phone($member->phone)){
                                        $email = valid_email($member->email)?$member->email:'';
                                        $first_name = strip_tags($member->first_name);
                                        $last_name = strip_tags($member->last_name);
                                        $membership_number = strip_tags($member->membership_number);
                                        $date_of_birth = strip_tags($member->date_of_birth);
                                        $physical_address = strip_tags($member->physical_address);
                                        $id_number = strip_tags($member->id_number);
                                        $next_of_kin_full_name = strip_tags($member->next_of_kin_full_name);
                                        $next_of_kin_id_number = strip_tags($member->next_of_kin_id_number);
                                        $next_of_kin_phone = strip_tags($member->next_of_kin_phone);
                                        $next_of_kin_relationship = strip_tags($member->next_of_kin_relationship);
                                        if($this->group_members->add_member_to_group(
                                            $this->group,
                                            $first_name,
                                            $last_name,
                                            $member->phone,
                                            $member->email,
                                            FALSE,
                                            FALSE,
                                            $this->user,
                                            $this->member->id,
                                            '',
                                            '',
                                            '',
                                            '',
                                            TRUE,
                                            $id_number,
                                            $membership_number,
                                            $date_of_birth,
                                            $physical_address,
                                            $next_of_kin_full_name,
                                            $next_of_kin_id_number,
                                            $next_of_kin_phone,
                                            $next_of_kin_relationship
                                        )){
                                            $successful_invitations_count++;
                                        }else{
                                            $unsuccessful_invitations_count++;
                                        }
                                    }else{
                                        $error_message = ' Row #'.$row;
                                        if($member->first_name==''){
                                            $error_message.=' First name missing';
                                        }
                                        if($member->last_name==''){
                                            if( $error_message == ' Row #'.$row){
                                                $error_message.=' Last name missing';
                                            }else{
                                                $error_message.=' ,last name missing';
                                            }
                                        }
                                        if(valid_phone($member->phone)==FALSE){
                                            if( $error_message == ' Row #'.$row){
                                                $error_message.=' Phone invalid or missing';
                                            }else{
                                                $error_message.=' and phone invalid or missing';
                                            }
                                        }
                                        $this->error_feedback[] = $error_message;
                                        $errors++;
                                    }
                                    $row++;
                                }
                            }
                            if($successful_invitations_count){
                                if($successful_invitations_count==1){
                                    $this->session->set_flashdata('success',$successful_invitations_count.' member successfully added to your group.');
                                }else{
                                    $this->session->set_flashdata('success',$successful_invitations_count.' members successfully added to your group.');
                                }
                            }
                            if($unsuccessful_invitations_count){
                                if($unsuccessful_invitations_count==1){
                                    $this->session->set_flashdata('info',$unsuccessful_invitations_count.' Applicant Details
 were updated.');
                                }else{
                                    $this->session->set_flashdata('info',$unsuccessful_invitations_count.' members details were updated.');
                                }
                            }
                            if($errors){
                                if($errors==1){
                                    $this->session->set_flashdata('error',$errors.' error encountered while importing, some details were missing.');
                                }else{
                                    $this->session->set_flashdata('error',$errors.' errors encountered while importing, some details were missing.');
                                }
                            }
                            if($this->error_feedback){
                                $this->session->set_userdata('error_feedback',$this->error_feedback);
                            }
                            $this->group_members->set_active_group_size($this->group->id);
                            $this->setup_tasks_tracker->set_completion_status('add-group-members',$this->group->id,$this->user->id);
                            redirect('bank/members/listing');
                        }
                    }else{
                        $this->session->set_flashdata('error','Member list file does not have the correct format');
                    }
                }else{
                    $this->session->set_flashdata('error','Member list file was not found');
                }
            }else{
                $this->session->set_flashdata('error','Member list file type is not allowed');
            }
        }
        $this->template->title(translate('Import Members'))->build('bank/import_members',$data);
    }

    public function update_members(){

        $data = array();
        if($this->input->post('import')){
            $directory = './uploads/files/csvs';
            if(!is_dir($directory)){
                mkdir($directory,0777,TRUE);
            }
            $config['upload_path'] = FCPATH . 'uploads/files/csvs/';
            $config['allowed_types'] = 'xls|xlsx|csv';
            $config['max_size'] = '1024';
            $this->load->library('upload',$config);
            if($this->upload->do_upload('member_list_file')){
                $successful_invitations_count = 0;
                $unsuccessful_invitations_count = 0;
                $upload_data = $this->upload->data();
                $file_path = $upload_data['full_path'];
                $this->load->library('Excel');
                $excel_sheet = new PHPExcel();
                if(file_exists($file_path)){
                    $file_type = PHPExcel_IOFactory::identify($file_path);
                    $excel_reader = PHPExcel_IOFactory::createReader($file_type);
                    $excel_book = $excel_reader->load($file_path);
                    $sheet = $excel_book->getSheet(0);
                    $allowed_column_headers = array('First Name','Last Name','Phone','Email','ID Number','Membership Number','Date of Birth(DD-MM-YYYY)','Location','Next of Kin Full Name','Next of Kin ID Number','Next of Kin Phone','Next of Kin Relationship');
                    $count = count($allowed_column_headers);
                    for($column = 1; $column <= $count; $column++){
                        $value = $sheet->getCellByColumnAndRow($column, 2)->getValue();
                        if(in_array(trim($value), $allowed_column_headers)){
                            $column_validation = true;
                        }else{
                            $column_validation = false;
                            break;
                        }
                    }
                    if($column_validation){
                        $highestRow = $sheet->getHighestRow();
                        $members = array();
                        for($row = 3; $row <= $highestRow; $row++){
                            $first_name = '';
                            $last_name = '';
                            $phone = '';
                            $email = '';
                            for($column = 1; $column <= $count; $column++){
                                if($column == 1){
                                    $first_name = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }else if($column == 2){
                                    $last_name = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }else if($column == 3){
                                    $phone = valid_phone($sheet->getCellByColumnAndRow($column,$row)->getValue());
                                }else if($column == 4){
                                    $email = filter_var($sheet->getCellByColumnAndRow($column,$row)->getValue(), FILTER_SANITIZE_EMAIL);
                                }else if($column == 5){
                                    $id_number = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }else if($column == 6){
                                    $membership_number = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }else if($column == 7){
                                    $date_of_birth = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }else if($column == 8){
                                    $physical_address = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }else if($column == 9){
                                    $next_of_kin_full_name = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }else if($column == 10){
                                    $next_of_kin_id_number = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }else if($column == 11){
                                    $next_of_kin_phone = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }else if($column == 12){
                                    $next_of_kin_relationship = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                                }
                            }
                            $members[] = array(
                                'first_name' => $first_name,
                                'last_name' => $last_name,
                                'phone' => $phone,
                                'email' => $email,
                                'id_number' => $id_number,
                                'membership_number' => $membership_number,
                                'date_of_birth' => $date_of_birth,
                                'physical_address' => $physical_address,
                                'next_of_kin_full_name' => $next_of_kin_full_name,
                                'next_of_kin_id_number' => $next_of_kin_id_number,
                                'next_of_kin_phone' => $next_of_kin_phone,
                                'next_of_kin_relationship' => $next_of_kin_relationship,
                            );
                        }

                        if(empty($members)){
                            $this->session->set_flashdata('info','The Member list file does not have any branches to import');
                        }else{
                            $members = (object)$members;
                            $successes = 0;
                            $duplicates = 0;
                            $ignores = 0;
                            $errors = 0;
                            $phones = array();
                            $emails = array();
                            $row = 2;
                            foreach($members as $member){
                                $member = (object)$member;
                                if($member->first_name||$member->last_name||$member->phone||$member->email){
                                    if($member->first_name&&$member->last_name&&valid_phone($member->phone)){
                                        $email = valid_email($member->email)?$member->email:'';
                                        $first_name = strip_tags($member->first_name);
                                        $last_name = strip_tags($member->last_name);
                                        $membership_number = strip_tags($member->membership_number);
                                        $date_of_birth = strip_tags($member->date_of_birth);
                                        $physical_address = strip_tags($member->physical_address);
                                        $id_number = strip_tags($member->id_number);
                                        $next_of_kin_full_name = strip_tags($member->next_of_kin_full_name);
                                        $next_of_kin_id_number = strip_tags($member->next_of_kin_id_number);
                                        $next_of_kin_phone = strip_tags($member->next_of_kin_phone);
                                        $next_of_kin_relationship = strip_tags($member->next_of_kin_relationship);
                                        if($this->group_members->add_member_to_group(
                                            $this->group,
                                            $first_name,
                                            $last_name,
                                            $member->phone,
                                            $member->email,
                                            FALSE,
                                            FALSE,
                                            $this->user,
                                            $this->member->id,
                                            '',
                                            '',
                                            '',
                                            '',
                                            TRUE,
                                            $id_number,
                                            $membership_number,
                                            $date_of_birth,
                                            $physical_address,
                                            $next_of_kin_full_name,
                                            $next_of_kin_id_number,
                                            $next_of_kin_phone,
                                            $next_of_kin_relationship
                                        )){
                                            $successful_invitations_count++;
                                        }else{
                                            $unsuccessful_invitations_count++;
                                        }
                                    }else{
                                        $error_message = ' Row #'.$row;
                                        if($member->first_name==''){
                                            $error_message.=' First name missing';
                                        }
                                        if($member->last_name==''){
                                            if( $error_message == ' Row #'.$row){
                                                $error_message.=' Last name missing';
                                            }else{
                                                $error_message.=' ,last name missing';
                                            }
                                        }
                                        if(valid_phone($member->phone)==FALSE){
                                            if( $error_message == ' Row #'.$row){
                                                $error_message.=' Phone invalid or missing';
                                            }else{
                                                $error_message.=' and phone invalid or missing';
                                            }
                                        }
                                        $this->error_feedback[] = $error_message;
                                        $errors++;
                                    }
                                    $row++;
                                }
                            }
                            if($successful_invitations_count){
                                if($successful_invitations_count==1){
                                    $this->session->set_flashdata('success',$successful_invitations_count.' member successfully added to your group.');
                                }else{
                                    $this->session->set_flashdata('success',$successful_invitations_count.' members successfully added to your group.');
                                }
                            }
                            if($unsuccessful_invitations_count){
                                if($unsuccessful_invitations_count==1){
                                    $this->session->set_flashdata('info',$unsuccessful_invitations_count.' Applicant Details
 were updated.');
                                }else{
                                    $this->session->set_flashdata('info',$unsuccessful_invitations_count.' members details were updated.');
                                }
                            }
                            if($errors){
                                if($errors==1){
                                    $this->session->set_flashdata('error',$errors.' error encountered while importing, some details were missing.');
                                }else{
                                    $this->session->set_flashdata('error',$errors.' errors encountered while importing, some details were missing.');
                                }
                            }
                            if($this->error_feedback){
                                $this->session->set_userdata('error_feedback',$this->error_feedback);
                            }
                            $this->group_members->set_active_group_size($this->group->id);
                            $this->setup_tasks_tracker->set_completion_status('add-group-members',$this->group->id,$this->user->id);
                            redirect('bank/members/listing');
                        }
                    }else{
                        $this->session->set_flashdata('error','Member list file does not have the correct format');
                    }
                }else{
                    $this->session->set_flashdata('error','Member list file was not found');
                }
            }else{
                $this->session->set_flashdata('error','Member list file type is not allowed');
            }
        }
        $this->template->title('Update Members')->build('bank/update_members',$data);
    }

    function export_update_member_template(){
        $data = array();
        $members = $this->members_m->get_group_members_with_next_of_kin_details();
        $data['members'] = $members;
        $data['group'] = $this->group;
        $json_file = json_encode($data);
        print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/members/export_update_member_template',$this->group->name.' member update template'));
            die;
    }

    public function listing(){
        $data = array();
        if($this->input->get_post('generate_excel')==1){
            $filter_parameters = array(
                'member_id' => $this->input->get('member_id'),
            );
            $data['group_role_options'] = $this->group_roles_m->get_group_role_options();
            $data['group_member_options'] = $this->members_m->get_group_member_with_membership_number_options();
            $total_rows = $this->members_m->count_group_members($this->group->id,$filter_parameters);
            $pagination = create_pagination('bank/members/listing/pages', $total_rows,50,5,TRUE);
            $data['posts'] = $this->members_m->get_group_members($this->group->id,$filter_parameters);
            $data['group'] = $this->group;
            $data['group_currency'] = $this->group_currency;
            $json_file = json_encode($data); 
            $this->excel_library->generate_member_list($json_file);
            print_r($json_file); die();           
            print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/members/listing',$this->group->name.' List of Members'));
            die;
        }
        $data['group_member_options'] = $this->members_m->get_group_member_with_membership_number_options();
        $this->template->title(translate('List Users'))->build('bank/listing',$data);
    }

    public function membership_requests(){
        $data = array();   
        $data['group_member_options'] = $this->members_m->get_group_member_with_membership_number_options();
        $this->template->title(translate('Membership Requests'))->build('bank/membership_requests',$data);
    }

    public function admit_member($member_id=0){
        // get the Applicant Details
.
        $membership_request = $this->members_m->get_group_membership_request($member_id);
        // add member to the group.
        $result = $this->group_members->add_member_to_group(
            $this->group,
            $membership_request->first_name,
            $membership_request->last_name,
            $membership_request->phone,
            $membership_request->email,
            FALSE,
            FALSE,
            $this->user,
            '',
            '',
            '',
            '',
            '',
            TRUE,
            $membership_request->id_number,
            '',
            '',
            $membership_request->location,
            $membership_request->next_of_kin_full_name,
            $membership_request->next_of_kin_id_number,
            $membership_request->next_of_kin_phone,
            $membership_request->next_of_kin_relationship
        );
        // yield a session message.
        if($result){
            $input = array(
                'is_deleted' => 1
            );
            if($this->members_m->update_group_membership_request($member_id,$input)){
                $this->session->set_flashdata('success',translate('Member successfully admitted to the group.'));
            }            
        } else {
            $this->session->set_flashdata('error',translate('Member could not be admitted to the group, Kindly try again.'));
        }
        // redirect to self registered members listing.
        redirect('bank/members');
    }
    function search($group_id=''){
        if($group_id){
            $group = $this->groups_m->get($group_id);
            if(!$group){
                redirect('admin/groups/search');
            }
        }
        $this->data['group_id'] = $group_id;
        $this->template->title('Search for a user')->build('bank/search',$this->data);
    }
    public function reject_member($member_id=0){
        // update the message status to rejected.
        $input = array(
            'is_deleted' => 1
        );

        $result = $this->members_m->update_group_membership_request($member_id,$input);
        // yield a session message.
        if($result){
            $this->session->set_flashdata('success',translate('Member successfully rejected.'));
        } else {
            $this->session->set_flashdata('error',translate('Member could not be rejected, Kindly try again.'));
        }
        // redirect to self registered members listing.
        redirect('bank/members/self_registered_members');
    }

    public function directory(){
        $data = array();
        $total_rows = $this->members_m->count_active_group_members();
        $pagination = create_pagination('bank/members/directory/pages', $total_rows,25,5,TRUE);
        $data['group_role_options'] = $this->group_roles_m->get_group_role_options();
        $group_member_total_cumulative_contribution_paid_per_member_array = $this->statements_m->get_group_member_total_cumulative_contribution_paid_per_member_array($this->group->id);
        $group_member_total_cumulative_contribution_arrears_per_member_array = $this->statements_m->get_group_member_total_cumulative_contribution_arrears_per_member_array($this->group->id);

        if($group_member_total_cumulative_contribution_paid_per_member_array&&$group_member_total_cumulative_contribution_arrears_per_member_array){
            $data['total_contributions_paid_per_member_array'] = $group_member_total_cumulative_contribution_paid_per_member_array;
            $data['total_contribution_balances_per_member_array'] = $group_member_total_cumulative_contribution_arrears_per_member_array;
        }else{
            $data['total_contributions_paid_per_member_array'] = $this->reports_m->get_group_total_contributions_paid_per_member_array();
            $data['total_contribution_balances_per_member_array'] = $this->reports_m->get_group_total_contribution_balances_per_member_array();
        }
        $group_member_total_cumulative_fine_paid_per_member_array = $this->statements_m->get_group_member_total_cumulative_fine_paid_per_member_array($this->group->id);
        $group_member_total_cumulative_fine_arrears_per_member_array = $this->statements_m->get_group_member_total_cumulative_fine_arrears_per_member_array($this->group->id);
        if($group_member_total_cumulative_fine_paid_per_member_array && $group_member_total_cumulative_fine_arrears_per_member_array){
            $data['group_total_fines_paid_per_member_array'] = $group_member_total_cumulative_fine_paid_per_member_array;
            $data['group_total_fines_balances_per_member_array'] = $group_member_total_cumulative_fine_arrears_per_member_array;
        }else{
            $data['group_total_fines_paid_per_member_array'] = $this->reports_m->get_group_total_fines_paid_per_member_array();
            $data['group_total_fines_balances_per_member_array'] = $this->reports_m->get_group_total_fines_balances_per_member_array();
        }       
        $data['pagination'] = $pagination;
        $data['posts'] = $this->members_m->limit($pagination['limit'])->get_active_group_members();
        $this->template->title('Member Directory')->build('bank/directory',$data);
    }

    public function edit($id = 0){
//         if(!$this->group->enable_edit_member_profile){
//             $this->session->set_flashdata('error','You Are Not Allowed To Edit Member Profile. Kindly Contact Support.');
//             redirect('bank/members');
//         }   
        $id OR redirect('bank/members');
        $post = $this->members_m->get_group_member($id);
        $post OR redirect('bank/members');
        $posts = $_POST;
        $errors = array();
        $error_messages = array();
        $successes = array();
        $entries_are_valid = TRUE;
        $allocations_total = 0;
        if(!empty($posts)){
            if(isset($posts['full_names'])){
                $count = count($posts['full_names']);
                for($i=0;$i<=$count;$i++):
                    if(isset($posts['full_names'][$i])&&isset($posts['id_numbers'][$i])&&isset($posts['next_of_kin_phones'][$i])&&isset($posts['next_of_kin_emails'][$i])&&isset($posts['relationships'][$i])&&isset($posts['allocations'][$i])):

                        //Full names
                        if($posts['full_names'][$i]==''){
                            $successes['full_names'][$i] = 0;
                            $errors['full_names'][$i] = 1;
                            $error_messages['full_names'][$i] = 'Please enter a full name';
                            $entries_are_valid = FALSE;
                        }else{
                            $successes['full_names'][$i] = 1;
                            $errors['full_names'][$i] = 0;
                        }

                        //ID Numbers
                        if($posts['id_numbers'][$i]==''){
                            $successes['id_numbers'][$i] = 0;
                            $errors['id_numbers'][$i] = 1;
                            $error_messages['id_numbers'][$i] = 'Please enter an id number';
                            $entries_are_valid = FALSE;
                        }else{
                            $successes['id_numbers'][$i] = 1;
                            $errors['id_numbers'][$i] = 0;
                        }

                        //Next of Kin Phones
                        if($posts['next_of_kin_phones'][$i]==''){
                            $successes['next_of_kin_phones'][$i] = 0;
                            $errors['next_of_kin_phones'][$i] = 1;
                            $error_messages['next_of_kin_phones'][$i] = 'Please enter a phone';
                            $entries_are_valid = FALSE;
                        }else{
                            if(valid_phone($posts['next_of_kin_phones'][$i])){
                                $successes['next_of_kin_phones'][$i] = 1;
                                $errors['next_of_kin_phones'][$i] = 0;
                            }else{
                                $successes['next_of_kin_phones'][$i] = 0;
                                $errors['next_of_kin_phones'][$i] = 1;
                                $error_messages['next_of_kin_phones'][$i] = 'Please enter a valid phone';
                                $entries_are_valid = FALSE;
                            }
                        }
                        //Next of Kin Emails
                        if($posts['next_of_kin_emails'][$i]==''){
                            //do nothing for now
                            $successes['next_of_kin_emails'][$i] = 1;
                            $errors['next_of_kin_emails'][$i] = 0;
                        }else{
                            if(valid_email($posts['next_of_kin_emails'][$i])){
                                $successes['next_of_kin_emails'][$i] = 1;
                                $errors['next_of_kin_emails'][$i] = 0;
                            }else{
                                $successes['next_of_kin_emails'][$i] = 0;
                                $errors['next_of_kin_emails'][$i] = 1;
                                $error_messages['next_of_kin_emails'][$i] = 'Please enter a valid email';
                                $entries_are_valid = FALSE;
                            }
                        }

                        //Relationships
                        if($posts['relationships'][$i]==''){
                            $successes['relationships'][$i] = 0;
                            $errors['relationships'][$i] = 1;
                            $error_messages['relationships'][$i] = 'Please enter a relationship';
                            $entries_are_valid = FALSE;
                        }else{
                            $successes['relationships'][$i] = 1;
                            $errors['relationships'][$i] = 0;
                        }

                        //Allocations
                        if($posts['allocations'][$i]==''){
                            $successes['allocations'][$i] = 0;
                            $errors['allocations'][$i] = 1;
                            $error_messages['allocations'][$i] = 'Please add an allocation';
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($posts['allocations'][$i])){
                                $successes['allocations'][$i] = 1;
                                $errors['allocations'][$i] = 0;
                                $allocations_total += $posts['allocations'][$i];
                            }else{
                                $successes['allocations'][$i] = 0;
                                $errors['allocations'][$i] = 1;
                                $error_messages['allocations'][$i] = 'Please select a valid allocation value';
                                $entries_are_valid = FALSE;
                            }
                        }

                    endif;
                endfor;
                if($allocations_total!==100):
                    $entries_are_valid = FALSE;
                    $this->session->set_flashdata('warning','Allocation to Next of Kin needs to add up to 100%');
                endif;
            }
        }
        if($entries_are_valid==FALSE):
            $this->session->set_flashdata('error','You have errors in your Next of Kin entries kindly review and save again');
        endif;
        $data['post'] = $post;
        $data['group_role_options'] = $this->group_roles_m->get_group_role_options();
        //$data['organization_role_options'] = $this->organization_roles_m->get_group_organization_role_options();
        $data['organization_role_options'] = $this->organization_roles;
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()&&$entries_are_valid){
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
                        $this->session->set_flashdata('warning','Member profile could not be resized');
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
            if($post->user_id == $this->user->id){
                $is_admin = 1;
            }else{
                $is_admin = $this->input->post('is_admin')?1:0;
            }
            $member_input = array(
                'membership_number'=> $this->input->post('membership_number'),
                'group_role_id'=> $this->input->post('group_role_id'),
                'organization_role_id' =>$this->input->post('organization_role_id'),
                'date_of_birth'=> strtotime($this->input->post('date_of_birth')),
                'postal_address'=> $this->input->post('postal_address'),
                'physical_address'=> $this->input->post('physical_address'),
                'is_admin'=> $is_admin,
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
            if($entries_are_valid){
                $this->members_m->delete_next_of_kin($this->group->id,$id);
                $this->session->set_flashdata('error','');
                if(!empty($posts)){
                    $successful_next_of_kin_entry = 0;
                    $unsuccessful_next_of_kin_entry = 0;
                    if(isset($posts['full_names'])){
                        $count = count($posts['full_names']);
                        for($i=0;$i<=$count;$i++):
                            if(isset($posts['full_names'][$i])&&isset($posts['id_numbers'][$i])&&isset($posts['next_of_kin_phones'][$i])&&isset($posts['next_of_kin_emails'][$i])&&isset($posts['relationships'][$i])&&isset($posts['allocations'][$i])):
                                $input = array(
                                    'full_name'=>$posts['full_names'][$i],
                                    'id_number'=>$posts['id_numbers'][$i],
                                    'phone'=>$posts['next_of_kin_phones'][$i],
                                    'email'=>$posts['next_of_kin_emails'][$i],
                                    'relationship'=>$posts['relationships'][$i],
                                    'allocation'=>$posts['allocations'][$i],
                                    'member_id'=>$id,
                                    'group_id'=>$this->group->id,
                                    'created_by'=>$this->user->id,
                                    'created_on'=>time(),
                                );
                                if($next_of_kin_id = $this->members_m->insert_next_of_kin($input)){
                                    $successful_next_of_kin_entry++;
                                }else{
                                    //do nothing for now
                                    $unsuccessful_next_of_kin_entry++;
                                }
                            endif;
                        endfor;
                    }
                }
                if($successful_next_of_kin_entry==1){
                    $this->session->set_flashdata('info',$successful_next_of_kin_entry.' next of kin entry saved');
                }else if($successful_next_of_kin_entry>1){
                    $this->session->set_flashdata('info',$successful_next_of_kin_entry.' next of kin entries saved');
                }
                if($unsuccessful_next_of_kin_entry==1){
                    $this->session->set_flashdata('warning',$unsuccessful_next_of_kin_entry.' next of kin entry not saved');
                }else if($unsuccessful_next_of_kin_entry>1){
                    $this->session->set_flashdata('warning',$unsuccessful_next_of_kin_entry.' next of kin entries not saved');
                }
            }
            if($member_update_result&&$user_update_result){
                $subject = 'Member profile update';
                $message = $this->user->first_name.' '.$this->user->last_name.' updated your membership profile.';
                $call_to_action = 'View profile';
                $call_to_action_link = "/bank/members/view/".$post->id;
                $this->notifications->create($subject,$message,$this->user,$this->member->id,$post->user_id,$post->id,$this->group->id,$call_to_action,$call_to_action_link,1);
                $this->session->set_flashdata('success','Member profile updated successfully');
            }
            redirect('bank/members/listing');
        }else{
            //do nothing for now
        }
        $data['errors'] = $errors;
        $data['error_messages'] = $error_messages;
        $data['successes'] = $successes;
        $data['posts'] = $posts;
        $data['next_of_kin_entries'] = $this->members_m->get_group_member_next_of_kin_entries($this->group->id,$post->id);
        $this->template->title(translate('Edit User'),$post->first_name.' '.$post->last_name)->build('bank/form',$data);
    }

    public function ajax_edit(){
        $id = $this->input->post('id');
        $group_role_options = $this->group_roles_m->get_group_role_options();
        if($id){
            if($post = $this->members_m->get_group_member($id)){
                $this->form_validation->set_rules($this->validation_rules);
                if($this->form_validation->run()){
                    $user_input = array(
                        'first_name' => $this->input->post('first_name'),
                        'last_name' => $this->input->post('last_name'),
                        'email' => $this->input->post('email'),
                        'phone' => valid_phone($this->input->post('phone')),
                        'modified_on' => time(),
                        'modified_by' => $this->user->id
                    );
                    $member_input = array(
                        'group_role_id'=> $this->input->post('group_role_id'),
                        'modified_on' => time(),
                        'modified_by' => $this->user->id
                    );
                    if($user_update_result = $this->ion_auth->update($post->user_id,$user_input)){
                        //do nothing for now
                    }
                    if($member_update_result = $this->members_m->update($post->id,$member_input)){
                        //do nothing for now
                    }
                    if($member_update_result&&$user_update_result){
                        $subject = 'Member profile update';
                        $message = $this->user->first_name.' '.$this->user->last_name.' updated your membership profile.';
                        $call_to_action = 'View profile';
                        $call_to_action_link = "/bank/members/view/".$post->id;
                        $this->notifications->create($subject,$message,$this->user,$this->member->id,$post->user_id,$post->id,$this->group->id,$call_to_action,$call_to_action_link,1);
                        if($member = $this->members_m->get_group_member($post->id)){
                            $group_role_name = isset($group_role_options[$member->group_role_id])?$group_role_options[$member->group_role_id]:"Member";
                            $member->group_role_name = $group_role_name;
                            echo json_encode($member);
                        }else{
                            echo "Could not find the member";
                        }
                    }else{
                        echo "Could not edit the member profile";
                    }
                }else{
                    echo validation_errors();
                }
            }else{
                echo "Could not find the group member";
            }
        }else{
            echo "Member id not supplied";
        }
    }

    public function view($id = 0){
        $id OR redirect('bank/members');
        $post = $this->members_m->get_group_member($id);
        $post OR redirect('bank/members');
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
        //print_r($data['contribution_options']);
        //die;
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
            $data['member_contribution_refunds'] = $this->withdrawals_m->get_group_member_total_contribution_refunds_per_contribution_array($this->group->id,$id);
        **/
        $data['total_member_contributions'] = $this->reports_m->get_group_member_total_contributions($post->id,$data['contribution_options']);
        $data['total_member_contributions_per_contribution_array'] = $this->reports_m->get_group_member_total_contributions_paid_per_contribution_array($id);
        $data['total_member_fines'] = $this->reports_m->get_group_member_total_fine_payments($this->group->id,0,0,$post->id);
        $data['total_fines_paid_per_member_array'] = $this->reports_m->get_group_total_fines_paid_per_member_array($id);
        $data['total_contribution_fines_paid_per_member_array'] = $this->reports_m->get_group_total_contribution_fines_paid_per_member_array($id);

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
        foreach ($this->active_group_member_options as $member_id => $member_name) {
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

    public function send_invitation($id = 0,$redirect = TRUE){
        $id OR redirect('bank/members/listing');
        $post = $this->members_m->get_group_member($id);
        $post OR redirect('bank/members/listing');
        $user = $this->ion_auth->get_user($post->user_id);
        $user OR redirect('bank/members/listing');
        if($this->messaging->send_single_member_first_time_login_invitation_message($this->group,1,$this->user,$post->user_id)){
            $this->session->set_flashdata('success','Invitation sent successfully');
        }else{
            $this->session->set_flashdata('error','Invitation not sent');
        }
        if($redirect){
            if($this->agent->referrer()){
                redirect($this->agent->referrer());
            }else{
                redirect('bank/members/listing');
            }
        }
    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_send_invitation'){
            for($i=0;$i<count($action_to);$i++){
                $this->send_invitation($action_to[$i],FALSE);
            }
        }
        if($this->agent->referrer()){
            redirect($this->agent->referrer());
        }else{
            redirect('bank/members/listing');
        }
    }

    public function ajax_valid_phone(){
    	$phone = $this->input->phone('phone');
    	if(valid_phone($phone)){
    		echo 'valid';
    	}else{
    		echo 'invalid';
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

    public function group_role_assignment_is_unique(){
        $group_role_id = $this->input->post('group_role_id');
        if($group_role_id==''){
            return TRUE;
        }else{
            if($member = $this->members_m->get_member_by_group_role_id($group_role_id)){
                if($this->input->post('user_id')==$member->user_id){
                    return TRUE;
                }else{
                    $this->form_validation->set_message('group_role_assignment_is_unique', 'The group role is already assigned to another member.');
                    return FALSE;
                }
            }else{
                return TRUE;
            }
        }
    }

    public function organization_role_assignment_is_unique(){
        $organization_role_id = $this->input->post('organization_role_id');
        if($organization_role_id==''){
            return TRUE;
        }else{
            if($member = $this->members_m->get_member_by_organization_role_id($organization_role_id)){
                if($this->input->post('user_id')==$member->user_id){
                    return TRUE;
                }else{
                     return TRUE;
                    /*$this->form_validation->set_message('organization_role_assignment_is_unique', 'The organization role is already assigned to another member.');
                    return FALSE;*/
                }
            }else{
                return TRUE;
            }
        }
    }

    public function suspend($id = 0){
        $id OR redirect('bank/members/listing');
        $post = $this->members_m->get_group_member($id);
        $post OR redirect('bank/members/listing');
        if($this->user->id==$post->user_id||$post->user_id==$this->group->owner){
            $this->session->set_flashdata('warning','You cannot suspend this member.');
        }else{
            $input = array(
                'active' => 0,
                'modified_on' => time(),
                'modified_by' => $this->user->id
            );
            if($this->members_m->update($id,$input)){
                $this->session->set_flashdata('success',$post->first_name.' '.$post->last_name.' suspended successfully. ');
            }else{
                $this->session->set_flashdata('error',$post->first_name.' '.$post->last_name.' could not be suspended. ');
            }
        }
        redirect('bank/members/listing');
    }

    public function activate($id = 0){
        $id OR redirect('bank/members/listing');
        $post = $this->members_m->get_group_member($id);
        $post OR redirect('bank/members/listing');
        $input = array(
            'active' => 1,
            'modified_on' => time(),
            'modified_by' => $this->user->id
        );
        if($this->members_m->update($id,$input)){
            $this->session->set_flashdata('success',$post->first_name.' '.$post->last_name.' activated successfully. ');
        }else{
            $this->session->set_flashdata('error',$post->first_name.' '.$post->last_name.' could not be activated. ');
        }
        redirect('bank/members/listing');
    }

    public function delete($id = 0){
        set_time_limit(0);
        ini_set('memory_limit','1536M');
        $id OR redirect('bank/members/listing');
        $post = $this->members_m->get_group_member($id);
        $post OR redirect('bank/members/listing');
        $data['post'] = $post;
        $data['group_role_options'] = $this->group_roles_m->get_group_role_options();
        $data['contribution_options'] = $this->contributions_m->get_group_refundable_contribution_options();
        $data['total_member_contributions'] = $this->reports_m->get_group_member_total_contributions($post->id,$data['contribution_options']);
        
        if($this->user->id==$this->group->owner||$this->ion_auth->is_admin()){
            if($this->user->id==$post->user_id||$post->user_id==$this->group->owner){
                $this->session->set_flashdata('warning','You cannot delete this member from the group.');
            }else{
                $password = $this->input->get('confirmation_string');
                $identity = valid_phone($this->user->phone)?:$this->user->email;
                if($this->ion_auth->login($identity,$password)){
                    /**
                     * 1. Check if member has any transactions - cannot delete
                     * 2. Check if member is a signatory - delete
                     * 
                     */
                    if($this->transactions->get_group_member_savings($this->group->id,$id)) {
                        $this->session->set_flashdata('error',$post->first_name.' '.$post->last_name.' cannot be deleted has existing transactions. ');
                    }else{
                        if($post->group_role_id) {
                            $this->session->set_flashdata('error',$post->first_name.' '.$post->last_name.' cannot be deleted. Member has existing role. ');
                        }else if($this->bank_accounts_m->check_if_member_is_signatory($post->id)){
                            $this->session->set_flashdata('error',$post->first_name.' '.$post->last_name.' cannot be deleted. Member is a bank signatory.');
                        }else {
                            $input = array(
                                'active' => 0,
                                'is_deleted' => 1,
                                'modified_on' => time(),
                                'modified_by' => $this->user->id
                            );
                            if($this->members_m->update($id,$input)){
                                $this->session->set_flashdata('success',$post->first_name.' '.$post->last_name.' deleted successfully. ');
                                $this->group_members->set_active_group_size($this->group->id);
                            }else{
                                $this->session->set_flashdata('error',$post->first_name.' '.$post->last_name.' could not be deleted. ');
                            }
                        }
                    }

                    // if($this->transactions->void_all_group_member_transactions($this->group->id,$id)){
                    //     $input = array(
                    //         'active' => 0,
                    //         'is_deleted' => 1,
                    //         'modified_on' => time(),
                    //         'modified_by' => $this->user->id
                    //     );
                    //     if($this->members_m->update($id,$input)){
                    //         $this->session->set_flashdata('success',$post->first_name.' '.$post->last_name.' deleted successfully. ');
                    //         $this->group_members->set_active_group_size($this->group->id);
                    //     }else{
                    //         $this->session->set_flashdata('error',$post->first_name.' '.$post->last_name.' could not be deleted. ');
                    //     }
                    // }else{
                    //     $this->session->set_flashdata('error','Something went wrong while voiding all member records');
                    // }
                }else{
                    $this->session->set_flashdata('warning','You entered the wrong password.');
                }
            }
        }else{
            $this->session->set_flashdata('warning','You do not have sufficient permissions to delete a member.');
        }
        redirect('bank/members/listing');
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
            redirect("bank/members/view/".$this->member->id);
        }
        $this->template->title($this->user->first_name.' '.$this->user->last_name,"Change Password")->build('shared/change_password',$data);
    }

    function suspension_requests(){
        $data = array();
        $this->template->title('Suspension Requests')->build('shared/suspension_requests',$data);
    }
  

    function change_member_user_id($member_id = 0,$user_id=0){
        $input = array(
            'user_id' => $user_id,
            'modified_by' => 1,
            'modified_on' => time()
        );
        $this->members_m->update($member_id,$input);
    }
}
