<?php  defined('BASEPATH') OR exit('No direct script access allowed');
class Mobile extends Mobile_Controller{

    protected $sms_template;
    
    public function __construct(){
        parent::__construct();
        $this->load->model('invoices_m');
        $this->load->library('contribution_invoices');
        $this->sms_template = $this->contribution_invoices->sms_template_default;
    }

    protected $invoice_type_options = array(
            1=>"Contribution invoice",
            2=>"Contribution fine invoice",
            3=>"Fine invoice",
            4=>"Miscellaneous invoice",
            //5=>"Back dated contribution invoice",
            //6=>"Back dated contrbution fine invoice",
            //7=>"Back dated fine invoice",
            //8=>"Back dated general invoice",
        );

    protected $send_to_options = array(
        ' ' => '--Select members to invoice--',
        '1' => 'All Members',
        '2' => 'Individual Members',
    );

    protected $validation_rules = array(
        array(
            'field' => 'type',
            'label' => 'Invoice Type',
            'rules' => 'xss_clean|trim|required|numeric|callback__valid_invoice_type',
        ),array(
            'field' => 'send_to',
            'label' => 'Send to',
            'rules' => 'xss_clean|trim|required|numeric|callback__valid_send_to_option',
        ),array(
            'field' => 'member_id',
            'label' => 'Member',
            'rules' => '',
        ),array(
            'field' => 'amount_payable',
            'label' => 'Amount Payable',
            'rules' => 'xss_clean|trim|required|currency',
        ),array(
            'field' => 'invoice_date',
            'label' => 'Invoice Date',
            'rules' => 'xss_clean|trim|required|date',
        ),array(
            'field' => 'due_date',
            'label' => 'Due Date',
            'rules' => 'xss_clean|trim|required|date',
        ),array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'xss_clean|trim',
        ),array(
            'field' => 'send_sms_notification',
            'label' => 'Send SMS Notification',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'send_email_notification',
            'label' => 'Send Email Notification',
            'rules' => 'xss_clean|trim|numeric',
        ),array(
            'field' => 'sms_template',
            'label' => 'SMS Template',
            'rules' => 'xss_clean|trim',
        ),array(
            'field' => 'contribution_id',
            'label' => 'Contribution',
            'rules' => 'xss_clean|trim',
        ),array(
            'field' => 'fine_category_id',
            'label' => 'Fine Category',
            'rules' => 'xss_clean|trim',
        )
    );

    function _additional_validation_rules(){
        if($this->input->post('type')==1||$this->input->post('type')==2||$this->input->post('type')==5||$this->input->post('type')==6){ 
            $this->validation_rules[] = array(
                'field' => 'contribution_id',
                'label' => 'Contribution',
                'rules' => 'xss_clean|trim|required|numeric|callback__contribution_exists',
            );
        }
        if($this->input->post('type')==1){
            if($this->input->post('send_sms_notification')){
                $this->validation_rules[] = array(
                    'field' => 'sms_template',
                    'label' => 'SMS Template',
                    'rules' => 'xss_clean|trim',
                );
            }
        }else if($this->input->post('type')==4){
            $this->validation_rules[] = array(
                'field' => 'description',
                'label' => 'Description',
                'rules' => 'xss_clean|trim|required',
            );
        }
        if($this->input->post('send_to')==2){
            $this->validation_rules[] = array(
                'field' => 'member_id',
                'label' => 'Member',
                'rules' => 'callback__member_id_is_not_empty',
            );
        }
        if($this->input->post('type')==3){
            $this->validation_rules[] = array(
                'field' => 'fine_category_id',
                'label' => 'Fine Category',
                'rules' => 'xss_clean|trim|required|callback__valid_fine_category',
            );
        }
    }

    function _valid_fine_category(){
        $fine_category_id = $this->input->post('fine_category_id');
        $group_id = $this->input->post('group_id');
        if($this->fine_categories_m->get_group_fine_category($fine_category_id,$group_id)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_valid_fine_category','Fine Category selected is not valid');
            return FALSE;
        }
    }

    function _valid_invoice_type(){
        $type = $this->input->post('type');
        if(array_key_exists($type, $this->invoice_type_options)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_valid_invoice_type','Invoice type should be valid');
            return FALSE;
        }
    }

    function _valid_send_to_option(){
        $send_to = $this->input->post('send_to');
        if(array_key_exists($send_to, $this->send_to_options)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_valid_send_to_option','Send to option must be valid');
            return FALSE;
        }
    }

    function _member_id_is_not_empty(){
        $member_ids = $this->input->post('member_ids');
        $member_names = $this->input->post('member_names');
        $group_id = $this->input->post('group_id');
        if(empty($member_ids)){
            $this->form_validation->set_message('_member_id_is_not_empty','Kindly add atleast one member to invoice');
            return FALSE;
        }
        $is_valid = TRUE;
        $invalid_members[] = array();
        foreach ($member_ids as $key=>$id) {
            if(!$this->members_m->get_member_where_member_id($id,$group_id)){
               $invalid_members[] = $member_names[$key]; 
               $is_valid = FALSE;
            }
        }

        if($is_valid){
            return TRUE;
        }else{
            $member_list = '';
            foreach ($invalid_members as $key => $name) {
               if($member_list){
                    $member_list.=', '.$name;
               }else{
                    $member_list=$name;
               }
            }
            if($member_list){
                $this->form_validation->set_message('_member_id_is_not_empty',"The following members ".$member_list.' do not exist in this group');
                return FALSE;
            }
        }
    }

    function _contribution_exists(){
        $group_id = $this->input->post('group_id');
        $contribution_id = $this->input->post('contribution_id');
        if($this->contributions_m->contribution_exists_in_group($contribution_id,$group_id)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_contribution_exists','Contribution selected does not exist in this group');
            return FALSE;
        }
    }

    function create(){
        $member_ids = array();
        $member_names = array();
        $i = 0;
        foreach ($this->request as $key => $value) {
            if($key=="members"){
                if(is_array($value)){
                    foreach ($value as $result_value_key => $result_value_value) {
                        $member_ids[] = $result_value_value->member_id;
                        $member_names[] = $result_value_value->member_name;
                    }
                }
            }else{
                $_POST[$key] = $value;
            }
            ++$i;
        }
        $_POST['member_ids'] = $member_ids;
        $_POST['member_names'] = $member_names;
        $user_id = $this->input->post('user_id')?:'';
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->group_role_id || $this->member->is_admin){
                        $this->_additional_validation_rules();
                        $this->form_validation->set_rules($this->validation_rules);
                        if($this->form_validation->run()){
                            if($this->input->post('send_to')==1){
                                $member_ids = array_flip($this->members_m->get_active_group_member_options($this->group->id));
                            }else{
                                $member_ids = $this->input->post('member_ids');
                            }
                            if($member_ids){
                                $successfully_created = 0;
                                $unsuccessfully_created = 0;
                                foreach ($member_ids as $member_id) {
                                    $member = $this->members_m->get_group_member($member_id,$this->group->id);
                                    $contribution = $this->contributions_m->get_group_contribution($this->input->post('contribution_id'),$this->group->id);
                                    if($this->input->post('type')==1){
                                       if($this->transactions->create_invoice(1,
                                            $this->group->id,
                                            $member,
                                            $contribution,
                                            $this->input->post('invoice_date'),
                                            $this->input->post('due_date'),
                                            $this->input->post('amount_payable'),
                                            $this->input->post('description'),
                                            $this->sms_template,
                                            $this->input->post('send_sms_notification'),
                                            $this->input->post('send_email_notification')
                                            )){
                                            ++$successfully_created;
                                        }else{
                                            ++$unsuccessfully_created;
                                        } 
                                    }else if($this->input->post('type')==2){
                                        if($this->transactions->create_contribution_fine_invoice(2,
                                            $this->group->id,
                                            $member,
                                            $contribution,
                                            $this->input->post('invoice_date'),
                                            $this->input->post('due_date'),
                                            $this->input->post('amount_payable'),
                                            $this->input->post('description'),
                                            '',
                                            $this->input->post('send_sms_notification'),
                                            $this->input->post('send_email_notification')
                                            )){
                                            ++$successfully_created;
                                        }else{
                                            ++$unsuccessfully_created;
                                        } 
                                    }else if($this->input->post('type')==3){
                                        if($this->transactions->create_fine_invoice(3,
                                            $this->group->id,
                                            $this->input->post('invoice_date'),
                                            $member,
                                            $this->input->post('fine_category_id'),
                                            $this->input->post('amount_payable'),
                                            $this->input->post('send_sms_notification'),
                                            $this->input->post('send_email_notification'),
                                            $this->input->post('description')
                                            )
                                        ){
                                            ++$successfully_created;
                                        }else{
                                            ++$unsuccessfully_created;
                                        }                  
                                    }else if($this->input->post('type')==4){
                                        if($this->transactions->create_miscellaneous_invoice(
                                            4,
                                            $this->group->id,
                                            $member,
                                            $this->input->post('invoice_date'),
                                            $this->input->post('due_date'),
                                            $this->input->post('amount_payable'),
                                            $this->input->post('description'),
                                            $this->sms_template,
                                            $this->input->post('send_sms_notification'),
                                            $this->input->post('send_email_notification')
                                            )){
                                           ++$successfully_created;
                                        }else{
                                            ++$unsuccessfully_created;
                                        } 
                                    }
                                }

                                if($successfully_created){
                                    $response = array(
                                            'status' => 1,
                                            'message' => "Invoice successfully added",
                                        );
                                }else{
                                    $response = array(
                                            'status' => 0,
                                            'message' => "Unable to add invoice",
                                        );
                                }
                            }else{
                                $response = array(
                                        'status' => 0,
                                        'message' => 'Member list error',
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
                                    'time' => time(),
                                    'message' => 'Form validation failed',
                                    'validation_errors' => $post,
                                );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function void(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->group_role_id || $this->member->is_admin){
                        $id = $this->input->post('id');
                        $post = $this->invoices_m->get_group_invoice($id,'',$this->group->id);
                        if($post){
                            $res = FALSE;
                            if($post->type==1){
                                if($this->transactions->void_contribution_invoice($id,$this->group->id)){
                                    $res = TRUE;
                                }else{
                                    $res = FALSE;
                                }
                            }else if($post->type==2){
                                if($this->transactions->void_fine_invoice($id,'',$post->contribution_id,$this->group->id)){
                                    $res=TRUE;
                                }else{
                                    $res = FALSE;
                                }
                            }else if($post->type==3){
                                if($this->transactions->void_fine_invoice($id,$post->fine_id,'',$this->group->id)){
                                    $res = TRUE;
                                }else{
                                    $res = FALSE;
                                }
                            }else if($post->type==4){
                                if($this->transactions->void_miscellaneous_invoice($id,$this->group->id)){
                                    $res = TRUE;
                                }else{
                                    $res = FALSE;
                                }
                            }
                            if($res){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'success',
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Error occured while voiding',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Invoice not available',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'You are not allowed to perform this request. For group admins only',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function get_group_invoices(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $invoices = array();
                    $filter_params = array();
                    if($this->member->is_admin || $this->member->group_role_id){

                    }else{
                        if($this->group->enable_member_information_privacy){
                            $filter_params = array(
                                'member_ids' => array(
                                    $this->member->id
                                ),
                            );
                        }else{

                        }
                    }
                    $total_rows = $this->invoices_m->count_group_invoices($filter_params,$this->group->id);   
                    $lower_limit = $this->input->post('lower_limit')?:0;
                    $upper_limit = $this->input->post('upper_limit')?:100;
                    $records_per_page = $upper_limit - $lower_limit;
                    if($lower_limit>$upper_limit){
                        $records_per_page = 100;
                    }
                    $contribution_options = $this->contributions_m->get_group_contribution_options($this->group->id);
                    $fine_category_options = $this->fine_categories_m->get_group_options(FALSE,$this->group->id);
                    $pagination = create_custom_pagination('mobile',$total_rows,$records_per_page,$lower_limit,TRUE);
                    $posts = $this->invoices_m->limit($pagination['limit'])->get_group_invoices($filter_params,$this->group->id);
                    $group_member_options = $this->members_m->get_group_member_options($this->group->id);
                    
                    foreach ($posts as $post) {
                        $type ='';
                        if($post->type==1){
                            $type = $this->invoice_type_options[$post->type].' for '.$contribution_options[$post->contribution_id].' contribution';
                        }else if($post->type==2){
                            $type = $this->invoice_type_options[$post->type].' for '.$contribution_options[$post->contribution_id].' contribution';
                        }else if($post->type==3){
                            $type = $this->invoice_type_options[$post->type].' for '.(isset($fine_category_options[$post->fine_category_id])?$fine_category_options[$post->fine_category_id]:'');
                        }else if($post->type==4){
                            $type = $this->invoice_type_options[$post->type].' for '.$post->description;
                        }
                        $invoices[] = array(
                            'id' => $post->id,
                            'invoice_date' => timestamp_to_mobile_shorttime($post->invoice_date),
                            'due_date' => timestamp_to_mobile_shorttime($post->due_date),
                            'member' => $group_member_options[$post->member_id],
                            'type' => $type,
                            'amount_payable' => $post->amount_payable,
                            'amount_paid' => $post->amount_paid,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'success',
                        'invoices' => $invoices,
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function get_group_invoice(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $invoice_id = $this->input->post('invoice_id');
                    if($post = $this->invoices_m->get_group_invoice($invoice_id,0,$this->group->id)){
                        $description = $post->description;
                        if($post->member_id == $this->member->id){
                            $member = $this->member;
                        }else{
                            $member = $this->members_m->get_group_member($post->member_id,$this->group->id);
                        }
                        if($post->type==1 || $post->type==2){
                            $contribution_options = $this->contributions_m->get_group_contribution_options($this->group->id);
                            $type = $this->invoice_type_options[$post->type];
                            $type_description = $type.' for '.$contribution_options[$post->contribution_id].' contribution';
                        }else if($post->type==3){
                            $fine_category_options = $this->fine_categories_m->get_group_options(FALSE,$this->group->id);
                            $type = $this->invoice_type_options[$post->type];
                            $type_description = $type.' for '.(isset($fine_category_options[$post->fine_category_id])?$fine_category_options[$post->fine_category_id]:'');
                        }else if($post->type==4){
                            $type = $this->invoice_type_options[$post->type];
                            $description = '';
                            $type_description=$type.' '.$post->description;
                        }
                        $currency = $this->countries_m->get_group_currency_name($this->group->id);
                        $type_description.= " ---- ".ucwords(number_to_words($post->amount_payable)).' '.$currency.'s only';

                        $response = array(
                            'status' => 1,
                            'message' => 'successful',
                            'data' => array(
                                'member' => $member->first_name.' '.$member->last_name,
                                'due_date' => timestamp_to_mobile_shorttime($post->due_date),
                                'type' => $type,
                                'description' => $description,
                                'type_description' => $type_description,
                                'amount' => $post->amount_payable,
                            ),
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not find group invoice details',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }


}?>