<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{
    public $access_levels = array(1=>'Group Administrator',0=>'Member');
    
    protected $validation_rules = array(
        array(
            'field' => 'name',
            'label' => 'Group Name',
            'rules' => 'trim|required',
        ),array(
            'field' => 'size',
            'label' => 'Group Size',
            'rules' => 'trim|required|numeric|callback__check_if_greater_than_group_members',
        ),array(
            'field' => 'country_id',
            'label' => 'Country',
            'rules' => 'trim|required|numeric',
        ),array(
            'field' => 'currency_id',
            'label' => 'Currency',
            'rules' => 'trim|required|numeric',
        ),array(
            'field' => 'phone',
            'label' => 'Group Phone Number',
            'rules' => 'trim|callback__check_if_valid_phone',
        ),array(
            'field' => 'email',
            'label' => 'Group Email Address',
            'rules' => 'trim|valid_email',
        ),array(
            'field' => 'address',
            'label' => 'Group Address',
            'rules' => 'trim',
        )
    );

	function __construct(){
        parent::__construct();
        $this->load->model('members/members_m');
        $this->load->model('themes/themes_m');
        $this->load->library('files_uploader');
    }

    public function edit_profile(){
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $groups_directory = './uploads/groups';
            if(!is_dir($groups_directory)){
                mkdir($groups_directory,0777,TRUE);
            }
            $logo['file_name'] = ''; 
            if($_FILES['logo']['name']){
                 $logo = $this->files_uploader->upload('logo',$groups_directory);
                 if($logo){
                    if(is_file(FCPATH.$groups_directory.'/'.$this->group->avatar)){
                        if(unlink(FCPATH.$groups_directory.'/'.$this->group->avatar)){
                            $this->session->set_flashdata('info','Group Logo successfully replaced');
                        }
                    }

                 }
            }
            $input = array(
                'name' => $this->input->post('name'),
                'size' => $this->input->post('size'),
                'country_id' => $this->input->post('country_id'),
                'currency_id' => $this->input->post('currency_id'),
                'phone' => valid_phone($this->input->post('phone')),
                'email' => $this->input->post('email'),
                'address' => $this->input->post('address'),
                'avatar' => $logo['file_name']?:$this->group->avatar,
                'modified_by' => $this->user->id,
                'modified_on' => time(),
            );
            $result = $this->groups_m->update($this->group->id,$input);
            if($result){
                $this->setup_tasks_tracker->unset_completion_status('add-group-members',$this->group->id,$this->user->id);
                $this->setup_tasks_tracker->set_completion_status('add-group-members',$this->group->id,$this->user->id);
                $this->session->set_flashdata('success','Changes saved successfully');
            }else{
                $this->session->set_flashdata('error','Could not update group profile');
            }
            redirect('group/groups/edit_profile','refresh');
        }else{
        }
        $data['country_options'] = $this->countries_m->get_country_options();
        $data['currency_options'] = $this->currency_options;
        $data['post'] = $this->group;
        $this->template->title('Edit Group Profile')->build('group/form',$data);
    }

    public function edit_settings(){
        $validation_rules = array(
            array(
                'field' => 'billing_cycle',
                'label' => 'Group Billing Cycle',
                'rules' => 'trim|required',
            ),array(
                'field' => 'owner',
                'label' => 'Group Owner',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'member_listing_order_by',
                'label' => 'Group Members Listing Order',
                'rules' => 'trim',
            ),
            array(
                'field' => 'order_members_by',
                'label' => 'Group Members Listing Order By',
                'rules' => 'trim',
            ),
            array(
                'field' => 'enable_member_information_privacy',
                'label' => 'Enforce Member Information Privacy',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'enable_send_monthly_email_statements',
                'label' => 'Enable Send Monthly Email Statements',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'disable_arrears',
                'label' => 'Disable Arrears',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'disable_ignore_contribution_transfers',
                'label' => 'Disable Ignore Contribution Transfers',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'enable_bulk_transaction_alerts_reconciliation',
                'label' => 'Enable Bulk Transaction Alerts Reconciliation',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'disable_member_directory',
                'label' => 'Disable Member Directory',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'disable_member_edit_profile',
                'label' => 'Disable Member Edit Profile',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'enable_absolute_loan_recalculation',
                'label' => 'Enable Reducing Balance Loan Recalculation',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'enable_merge_transaction_alerts',
                'label' => 'Enable Merging of Transaction Alerts',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'statement_send_date',
                'label' => 'Statement sending date',
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'group_offer_loans',
                'label' => 'Group offer loans',
                'rules' => 'trim|numeric',
            ),

            
        );
        $post = new stdClass();
        $this->form_validation->set_rules($validation_rules);
        if($this->form_validation->run()){
            $input = array(
                'billing_cycle' => $this->input->post('billing_cycle'),
                'member_listing_order_by' => $this->input->post('member_listing_order_by'),
                'order_members_by' => $this->input->post('order_members_by'),
                'enable_send_monthly_email_statements' => $this->input->post('enable_send_monthly_email_statements')?1:0,
                'enable_bulk_transaction_alerts_reconciliation' => $this->input->post('enable_bulk_transaction_alerts_reconciliation')?1:0,
                'enable_member_information_privacy' => $this->input->post('enable_member_information_privacy')?1:0,
                'disable_arrears' => $this->input->post('disable_arrears')?1:0,
                'disable_ignore_contribution_transfers' => $this->input->post('disable_ignore_contribution_transfers')?1:0,
                'disable_member_directory' => $this->input->post('disable_member_directory')?1:0,
                'disable_member_edit_profile' => $this->input->post('disable_member_edit_profile')?1:0,
                'enable_absolute_loan_recalculation' => $this->input->post('enable_absolute_loan_recalculation')?1:0,
                'enable_merge_transaction_alerts' => $this->input->post('enable_merge_transaction_alerts')?1:0,
                'group_offer_loans' => $this->input->post('group_offer_loans')?1:0,
                'owner' => $this->input->post('owner'),
                'modified_by' => $this->user->id,
                'modified_on' => time(),
            );
            if($this->input->post('enable_send_monthly_email_statements')){
                $statement_send_date = $this->input->post('statement_send_date');
                $next_monthly_contribution_statement_send_date  = $this->get_next_monthly_contribution_statement_send_date($statement_send_date);
                $input += array(
                    'statement_send_date' => $statement_send_date,
                    'next_monthly_contribution_statement_send_date' => $next_monthly_contribution_statement_send_date,
                );
            }
            $result = $this->groups_m->update($this->group->id,$input);
            if($result){
                $this->session->set_flashdata('success','Changes saved successfully');
            }else{
                $this->session->set_flashdata('error','Could not update group settings');
            }
            redirect('group/groups/edit_settings','refresh');
        }else{

        }
        $this->data['statement_sending_date_options'] = $this->investment_groups->statement_sending_date_options;
        $this->data['group_user_options'] = $this->users_m->get_group_user_options($this->group->id);
        $this->data['post'] = $this->group;
        $this->data['billing_cycles'] = $this->billing_settings->billing_cycle;
        $this->data['order_by_options'] = $this->order_by_options;
        $this->data['member_listing_order_by_options'] = $this->member_listing_order_by_options;
        $this->template->title('Group Settings')->build('group/settings_form',$this->data);
    }

    function get_next_monthly_contribution_statement_send_date($statement_send_date = 0){
        $today = time();
        if($this->group->next_monthly_contribution_statement_send_date){
            if($statement_send_date){
                if(date('m',$this->group->next_monthly_contribution_statement_send_date) <= date('m',$today)
                    && mktime(0, 0, 0, date('m',$today), $statement_send_date, date('Y',$today)) > $today
                ){
                    $next_monthly_contribution_statement_send_date = mktime(0, 0, 0, date('m',$today), $statement_send_date, date('Y',$today));
                }else{
                    $next_monthly_contribution_statement_send_date = mktime(0, 0, 0, date('m',$today)+1,$statement_send_date, date('Y',$today));
                }
            }else{
                if(date('m',$this->group->next_monthly_contribution_statement_send_date) <= date('m',$today)){
                    $next_monthly_contribution_statement_send_date = mktime(0, 0, 0, date('m',$today), 28, date('Y',$today));
                }else{
                    $next_monthly_contribution_statement_send_date = mktime(0, 0, 0, date('m',$today)+1, 28, date('Y',$today));
                }
            }
        }else{
            if($statement_send_date){
                $next_monthly_contribution_statement_send_date = mktime(0, 0, 0, date('m',$today), $statement_send_date, date('Y',$today));
            }else{
                $next_monthly_contribution_statement_send_date = mktime(0, 0, 0, date('m',$today), 28, date('Y',$today));
            }
        }
        return $next_monthly_contribution_statement_send_date;
    }

    function _check_if_greater_than_group_members(){
        $size = $this->input->post('size');
        $group_members = $this->members_m->count_group_members();
        if($size<$group_members){
            $this->form_validation->set_message('_check_if_greater_than_group_members', 'The Group Size cannot be less than '.$group_members.'.');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    function _check_if_valid_phone(){
        $phone = $this->input->post('phone');
        if($phone){
            if(valid_phone($phone)){
                return TRUE;
            }else{
                $this->form_validation->set_message('_check_if_valid_phone', 'The Group Phone number format you entered is invalid');
                return FALSE;
            }
        }else{
            return TRUE;
        }
    }

    function index(){
        $this->template->title($this->group->name)->build('group/index');
    }

    public function logout(){
        //log the user out
        $this->ion_auth->logout();
        unset($_SESSION);
        $this->session->set_flashdata('success', 'You have Successfully Logged Out');
        redirect('authentication/login','refresh');
    }

    function ajax_set_theme(){
        $theme_id = $this->input->post('id');
        $theme = $this->themes_m->get($theme_id);
        if($theme){
            $input = array(
                'theme'=>$theme->slug,
                'modified_by'=>$this->user->id,
                'modified_on'=>time()
            );
            if($this->groups_m->update($this->group->id,$input)){
                echo 'success';
            }else{
                echo 'could not update group theme';
            }
        }else{
            echo 'no theme found';
        }
    }

    function ajax_remove_theme(){
        $input = array(
            'theme'=>'',
            'modified_by'=>$this->user->id,
            'modified_on'=>time()
        );
        if($this->groups_m->update($this->group->id,$input)){
            echo 'success';
        }else{
            echo 'could not update group theme';
        }
    }

}