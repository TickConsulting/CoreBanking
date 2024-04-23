<?php  defined('BASEPATH') OR exit('No direct script access allowed');
class Mobile extends Mobile_Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->model('fines_m');
        $this->load->model('fine_categories/fine_categories_m');
        $this->load->model('invoices/invoices_m');
        $this->load->library('transactions');
        $this->invoice_type_options = array(
            1=>"Contribution invoice",
            2=>"Contribution fine invoice",
            3=>"Fine invoice",
            4=>"Miscellaneous invoice",
            //5=>"Back dated contribution invoice",
            //6=>"Back dated contrbution fine invoice",
            //7=>"Back dated fine invoice",
            //8=>"Back dated general invoice",
        );

        $this->fine_invoice_type_options = array(
            2=>"Contribution fine invoice",
            3=>"Fine invoice",
            //5=>"Back dated contribution invoice",
            //6=>"Back dated contrbution fine invoice",
            //7=>"Back dated fine invoice",
            //8=>"Back dated general invoice",
        );
    }

    public function _remap($method, $params = array()){
       if(method_exists($this, $method)){
           return call_user_func_array(array($this, $method), $params);
       }
       $this->output->set_status_header('404');
       header('Content-Type: application/json');
       $file = file_get_contents('php://input')?(array)json_decode(file_get_contents('php://input')):array();
       $request = $_REQUEST+$file;
       echo encrypt_json_encode(
        array(
            'response' => array(
                    'status'    =>  404,
                    'message'       =>  '404 Method Not Found for URI: '.$this->uri->uri_string(),
                )

        )

        );
    }

    protected $validation_rules = array(
        array(
                'field' => 'fine_date',
                'label' => 'Fine Date',
                'rules' => 'required|xss_clean|trim|date',
            ),
        array(
                'field' => 'members',
                'label' => 'Members',
                'rules' => 'xss_clean|trim|callback__valid_members_to_fine',
            ),
        array(
                'field' => 'fine_category_id',
                'label' => 'Fine Category',
                'rules' => 'required|xss_clean|trim|callback__valid_fine_category',
            ),
        array(
                'field' => 'amount',
                'label' => 'Fine Amount',
                'rules' => 'required|xss_clean|trim|currency',
            ),
    );

    function _valid_members_to_fine(){
        $member_ids = $this->input->post('member_ids');
        $member_names = $this->input->post('member_names');
        $group_id = $this->input->post('group_id');
        $member_type_id = $this->input->post('member_type_id');
        if($member_type_id){
            if($member_type_id == 1){
                $members = $this->input->post('members');
                if(count($members)>0){
                    $is_valid = TRUE;
                    foreach ($members as $member_id) {
                        if(!$this->members_m->get_member_where_member_id($member_id,$group_id)){
                           $is_valid = FALSE;
                        }
                    }
                    if($is_valid){
                        //return TRUE;
                    }else{
                        $this->form_validation->set_message('_valid_members_to_fine',"Could not find some member records in this group");
                        return FALSE;
                    }
                }else{
                   $this->form_validation->set_message('_valid_members_to_fine','Kindly add atleast one member to fine');
                    return FALSE; 
                }
            }
        }else{
            if(empty($member_ids)){
                $this->form_validation->set_message('_valid_members_to_fine','Kindly add atleast one member to fine');
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
                    $this->form_validation->set_message('_valid_members_to_fine',"The following members ".$member_list.' do not exist in this group');
                    return FALSE;
                }
            }
        }
    }

    function _valid_fine_category(){
        $fine_category_id = $this->input->post('fine_category_id');
        $group_id = $this->input->post('group_id');
        if(preg_match('/fine_category-/', $fine_category_id)){
            $fine_category_id = str_replace('fine_category-','',$fine_category_id);
            if($this->fine_categories_m->get_group_fine_category($fine_category_id,$group_id)){
                return TRUE;
            }else{
                $this->form_validation->set_message('_valid_fine_category','Fine Category selected is not valid');
                return FALSE;
            }
        }elseif (preg_match('/contribution-/', $fine_category_id)) {
            $contribution_id = str_replace('contribution-','',$fine_category_id);
            if($this->contributions_m->get_group_contribution($contribution_id,$group_id)){
                return TRUE;
            }else{
                $this->form_validation->set_message('_valid_fine_category','Fine Category selected is not valid');
                return FALSE;
            }
        }else{
            if($this->fine_categories_m->get_group_fine_category($fine_category_id,$group_id)){
                return TRUE;
            }else{
                $this->form_validation->set_message('_valid_fine_category','Fine Category selected is not valid');
                return FALSE;
            }
        }
        
        
    }

    function fine_members(){
        $member_ids = array();
        $member_names = array();
        $i = 0;
        foreach ($this->request as $result_key => $result_value) {
            if($result_key=="members"){
                if(is_array($result_value)){
                    foreach ($result_value as $result_value_key => $result_value_value) {
                        $_POST['member_ids'][] = $result_value_value->member_id;
                        $_POST['member_names'][] = $result_value_value->member_name;
                    }
                }
            }elseif(preg_match('/phone/', $result_key)){
                $_POST[$result_key] = valid_phone($result_value);
            }else{
                $_POST[$result_key] = $result_value;
            }
            ++$i;
        }
        $user_id = $this->input->post('user_id')?:0;
        if($user_id){
            $this->user = $this->ion_auth->get_user($user_id);
            if($this->user){
                $this->ion_auth->update_last_login($this->user->id);
                $group_id = $this->input->post('group_id')?:0;
                $this->group = $this->groups_m->get($group_id);
                if($this->group){
                    if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                        if($this->member->group_role_id || $this->member->is_admin){
                            $this->form_validation->set_rules($this->validation_rules);
                            if($this->form_validation->run()){
                                $member_ids = $this->input->post('member_ids');
                                $successful_fine_entry_count = 0;
                                $unsuccessful_fine_entry_count = 0;
                                $amount = valid_currency($this->input->post('amount'));
                                $fine_date = $this->input->post('fine_date');
                                $fine_category_id = str_replace('fine_category-','',$this->input->post('fine_category_id'));
                                $description = $this->input->post('description');
                                foreach($member_ids as $member_id){
                                    $member = $this->members_m->get_group_member($member_id,$this->group->id);
                                    if($member){
                                        $res = $this->transactions->create_fine_invoice(
                                                3,
                                                $this->group->id,
                                                $fine_date,
                                                $member,
                                                $fine_category_id,
                                                $amount,
                                                '1',
                                                '1',
                                                $description
                                            );
                                        if($res){
                                            ++$successful_fine_entry_count;
                                        }else{
                                            ++$unsuccessful_fine_entry_count;
                                        }
                                    }else{
                                        $no_member_found_count++;
                                    }
                                }
                                $response = array(
                                        'status' => '1',
                                        'time'  => time(),
                                        'message' => $successful_fine_entry_count.' members fined',
                                    );
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
                            'message' => 'Could not find Applicant Details
',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 5,
                        'time' => time(),
                        'message' => 'Group details not found',
                    );
                }
            }else{
                $response = array(
                    'status' => 4,
                    'time' => time(),
                    'message' => 'User details not found',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'time' => time(),
                'message' => 'essential values missing',
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }


    function new_fine_members(){
        $response = array();
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                if(is_object($value)){
                    $_POST[$key] = (array)$value;
                }elseif(is_array($value)){
                    $_POST[$key] = (array)$value;
                }else{
                    $_POST[$key] = $value;
                }
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->group_role_id || $this->member->is_admin){
                        $this->form_validation->set_rules($this->validation_rules);
                        if($this->form_validation->run()){
                            $member_type_id = $this->input->post("member_type_id");
                            $fine_date = $this->input->post("fine_date");
                            $fine_category_id = $this->input->post("fine_category_id");
                            $description = $this->input->post("description");
                            $amount = currency($this->input->post("amount"));
                            $successful_fine_entry_count = 0;
                            $unsuccessful_fine_entry_count = 0;
                            if($member_type_id == 2){
                                $members = $this->members_m->get_active_group_member_options(); 
                                foreach ($members as $member_id => $member_name) {
                                    $member = $this->members_m->get_group_member($member_id,$this->group->id);
                                    $res = $this->transactions->create_fine_invoice(
                                            3,
                                            $this->group->id,
                                            $fine_date,
                                            $member,
                                            $fine_category_id,
                                            $amount,
                                            0,
                                            0,
                                            $description
                                        );
                                    if($res){
                                        ++$successful_fine_entry_count;
                                    }else{
                                        ++$unsuccessful_fine_entry_count;
                                    }
                                }

                                if($successful_fine_entry_count){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Successfully fined '.$successful_fine_entry_count.' members',
                                    );
                                }else if($successful_fine_entry_count){
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Could not complete fining '.$unsuccessful_fine_entry_count.' members',
                                    );
                                }
                            }elseif($member_type_id==1){
                                $member_ids = $this->input->post('members');
                                foreach ($member_ids as $member_id) {
                                    $member = $this->members_m->get_group_member($member_id,$this->group->id);
                                    $res = $this->transactions->create_fine_invoice(
                                            3,
                                            $this->group->id,
                                            $fine_date,
                                            $member,
                                            $fine_category_id,
                                            $amount,
                                            0,
                                            0,
                                            $description
                                        );
                                    if($res){
                                        ++$successful_fine_entry_count;
                                    }else{
                                        ++$unsuccessful_fine_entry_count;
                                    }
                                }
                                if($successful_fine_entry_count){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Successfully fined '.$successful_fine_entry_count.' members',
                                    );
                                }else if($successful_fine_entry_count){
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Could not complete fining '.$unsuccessful_fine_entry_count.' members',
                                    );
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Could not find the category of members to fine',
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
                        'message' => 'Could not find Applicant Details
',
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
        if($response['status']==0){
            update_request_id($this->request->request_id);
        }
        echo encrypt_json_encode(array('response'=>$response));
    }
    
    function get_group_fine_options(){
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
                    $group_contribution_fines = $this->contributions_m->get_active_group_contribution_fine_options($this->group->id);
                    $group_fine_category_options = $this->fine_categories_m->get_group_fine_categories($this->group->id);
                    $contribution_fine_options = array();
                    $fine_category_options = array();
                    foreach ($group_contribution_fines as $id => $name) {
                        $balance = $this->statements_m->get_member_fine_balance($this->group->id,$this->member->id,$id);
                        $contribution_fine_options[] = array(
                            'id' => 'contribution-'.$id,
                            'name' => $name,
                            'balance' => $balance,
                            'amount' => 0,
                        );
                    }
                    foreach ($group_fine_category_options as $group_fine_category_option) {
                        $balance = $this->statements_m->get_member_fine_balance($this->group->id,$this->member->id,'',$group_fine_category_option->id);
                        $fine_category_options[] = array(
                            'id' => 'fine_category-'.$group_fine_category_option->id,
                            'name' => $group_fine_category_option->name,
                            'balance' => $balance,
                            'amount' => $group_fine_category_option->amount,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'Fine types',
                        'contribution_fine_options' => $contribution_fine_options,
                        'fine_category_options' => $fine_category_options,
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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

    function get_group_fines_list(){
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

                    $lower_limit = $this->input->post('lower_limit')?:0;
                    $upper_limit = $this->input->post('upper_limit')?:20;
                    $total_rows = $this->fines_m->count_group_fines($this->group->id);
                    $records_per_page = $upper_limit - $lower_limit;
                    $pagination = create_custom_pagination('group',$total_rows,$records_per_page,$lower_limit,TRUE);
                    $posts = $this->invoices_m->limit($pagination['limit'])->get_group_fine_invoices($filter_params,$this->group->id); 
                    $fines = array();
                    $fine_category_options = $this->fine_categories_m->get_group_options(0,$this->group->id);
                    $contribution_options = $this->contributions_m->get_group_contribution_options($this->group->id);
                    $group_member_options = $this->members_m->get_group_member_options($this->group->id);
                    foreach ($posts as $key => $post) {
                        if($post->type==1){
                            $description=$this->invoice_type_options[$post->type].' for '.$contribution_options[$post->contribution_id].' contribution';
                        }else if($post->type==2){
                            $description= $this->invoice_type_options[$post->type].' for '.$contribution_options[$post->contribution_id].' contribution';
                        }else if($post->type==3){
                            $description= $this->invoice_type_options[$post->type].' for '.(isset($fine_category_options[$post->fine_category_id])?$fine_category_options[$post->fine_category_id]:'');
                        }else if($post->type==4){
                            $description= $this->invoice_type_options[$post->type].' for '.$post->description;
                        }else{
                            $description = '';
                        }
                        $fines[] = array(
                            'id' => $post->id,
                            'fine_date' => timestamp_to_mobile_shorttime($post->invoice_date),
                            'amount_payable' => $post->amount_payable,
                            'amount_paid' => $post->amount_paid,
                            'description' => $group_member_options[$post->member_id].' - '.$description,
                            'member' => $group_member_options[$post->member_id],
                        );
                    }

                    
                    $response = array(
                        'status' => 1,
                        'time' => time(),
                        'message' => 'deposit list',
                        'fines' => $fines,
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
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