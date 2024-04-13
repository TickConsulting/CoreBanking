<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Mobile extends Mobile_Controller{

    function __construct(){
        parent::__construct();
        $this->load->model('contributions/contributions_m');
        $this->load->library('contribution_invoices');
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
                ),
            'request'   =>  $request,

        )

        );
    }

    function get_group_contribution_options(){
        $usernames = array();
        $phones = array();
        $group_role_ids = array();
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
                    $contributions = $this->contributions_m->get_active_group_contribution_options($this->group->id);
                    $contribution_id_list = '';
                    $contribution_options = array();
                    foreach ($contributions as $id => $name) {
                        $contribution_options[] = array(
                            'id' => $id,
                            'name' => $name,
                        );

                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'Contribution List',
                        'time' => time(),
                        'contributions' => $contribution_options,
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
    
    function get_member_contribution_options(){
        $usernames = array();
        $phones = array();
        $group_role_ids = array();
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
                    $contributions = $this->contributions_m->get_active_group_contribution_options($this->group->id);
                    $contribution_id_list = '';
                    $contribution_options = array();
                    foreach ($contributions as $id => $name) {
                        $balance = $this->statements_m->get_member_contribution_balance($this->group->id,$this->member->id,$id)?:0;
                        $contribution_options[] = array(
                            'id' => $id,
                            'name' => $name,
                            'balance' => $balance,
                        );

                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'Contribution List',
                        'time' => time(),
                        'contributions' => $contribution_options,
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

    function get_group_contributions(){
        $usernames = array();
        $phones = array();
        $group_role_ids = array();
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id')?:'';
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $contributions = $this->contributions_m->get_group_contributions($this->group->id);
                    $group_contributions = array();
                    $regular_contribution_settings_array = $this->contributions_m->get_group_regular_contribution_settings_array($this->group->id);
                    $contribution_type_options = $this->contribution_invoices->contribution_type_options;
                    $contribution_frequency_options = $this->contribution_invoices->contribution_frequency_options;
                    $days_of_the_month = $this->contribution_invoices->days_of_the_month;
                    $month_days = $this->contribution_invoices->month_days;
                    $week_days = $this->contribution_invoices->week_days;
                    $every_two_week_days = $this->contribution_invoices->every_two_week_days;
                    $week_numbers = $this->contribution_invoices->week_numbers;
                    $starting_months = $this->contribution_invoices->starting_months;

                    if($contributions){ 
                        foreach ($contributions as $post) {
                            $frequency ='';
                            $invoice_date = '';
                            $contribution_date = '';
                            if($post->regular_invoicing_active){
                                $type = (isset($contribution_type_options[$post->type])?$contribution_type_options[$post->type]:'');
                                if($post->type == 1){
                                    $regular_contribution_setting = isset($regular_contribution_settings_array[$post->id])?$regular_contribution_settings_array[$post->id]:'';
                                    if($regular_contribution_setting){
                                        if($post->type == 1){
                                            if($regular_contribution_setting->contribution_frequency==1){
                                                //Once a month
                                                $frequency.=$contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$days_of_the_month[$regular_contribution_setting->month_day_monthly].' '.$month_days[$regular_contribution_setting->week_day_monthly?$regular_contribution_setting->week_day_monthly:0];
                                            }else if($regular_contribution_setting->contribution_frequency==6){
                                                //Weekly
                                                $frequency.=$contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$week_days[$regular_contribution_setting->week_day_weekly];
                                            }else if($regular_contribution_setting->contribution_frequency==7){
                                                //Fortnight or every two weeks
                                                $frequency.=$contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$every_two_week_days[$regular_contribution_setting->week_day_fortnight].' '.$week_numbers[$regular_contribution_setting->week_number_fortnight];
                                            }else if($regular_contribution_setting->contribution_frequency==2||$regular_contribution_setting->contribution_frequency==3||$regular_contribution_setting->contribution_frequency==4||$regular_contribution_setting->contribution_frequency==5){
                                                //Multiple months
                                                $frequency.= $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$days_of_the_month[$regular_contribution_setting->month_day_multiple].' '.$month_days[$regular_contribution_setting->week_day_multiple?$regular_contribution_setting->week_day_multiple:0].', '.$starting_months[$regular_contribution_setting->start_month_multiple];
                                            }else if($regular_contribution_setting->contribution_frequency==8){
                                                $frequency.= $contribution_frequency_options[$regular_contribution_setting->contribution_frequency];
                                            }
                                            $invoice_date =timestamp_to_mobile_time($regular_contribution_setting->invoice_date);
                                            $contribution_date = timestamp_to_mobile_time($regular_contribution_setting->contribution_date);
                                        }elseif ($post->type ==2) {
                                            $one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($post->id,$this->group->id);
                                            $invoice_date =timestamp_to_mobile_time($one_time_contribution_setting->invoice_date);
                                            $contribution_date = timestamp_to_mobile_time($one_time_contribution_setting->contribution_date);
                                        }else{

                                        }
                                        
                                    }
                                }elseif($post->type == 2){
                                    if($post->one_time_invoicing_active){
                                        $one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($post->id,$this->group->id);
                                        if($one_time_contribution_setting){ 
                                            $invoice_date = timestamp_to_mobile_time($one_time_contribution_setting->invoice_date);
                                            $contribution_date = timestamp_to_mobile_time($one_time_contribution_setting->contribution_date);
                                            $frequency = 'One Time Contribution';
                                        }else{
                                            
                                        }

                                    }
                                }
                            }else{
                                $type = 'Invoicing Disabled';
                            }
                            
                            $group_contributions[] = array(
                                'id' => $post->id,
                                'name' => $post->name,
                                'amount' => $post->amount,
                                'type' => $type,
                                'contribution_type' => $post->type,
                                'frequency' => $frequency,
                                'invoice_date' => $invoice_date,
                                'contribution_date' => $contribution_date, 
                                'one_time_contribution_setting' => isset($one_time_contribution_setting)?$one_time_contribution_setting->id:0,
                                'is_hidden' => $post->is_hidden?1:0,
                                'active' => $post->active?1:0,
                            );
                        }
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'Group contributions',
                        'contributions' => $group_contributions,
                    );
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

    function get_group_contribution(){
        $usernames = array();
        $phones = array();
        $group_role_ids = array();
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id')?:'';
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $id = $this->input->post('id');
                    if($id){
                        if($post = $this->contributions_m->get_group_contribution($id,$this->group->id)){
                            if($post->type==1){
                                $regular_contribution_setting = $this->contributions_m->get_group_regular_contribution_setting($id,$this->group->id);
                                $post = (object) array_merge((array) $regular_contribution_setting, (array) $post);
                            }else if($post->type==2){
                                $one_time_contribution_setting = $this->contributions_m->get_group_one_time_contribution_setting($id,$this->group->id);
                                $post = (object) array_merge((array) $one_time_contribution_setting, (array) $post);
                            }elseif ($post->type == 3) {
                                $post = (object) array_merge(array('contribution_id'=>$post->id), (array) $post);
                            }else{

                            }
                            if (isset($post->enable_contribution_member_list) &&$post->enable_contribution_member_list) {
                                $selected_group_members = $this->contributions_m->get_contribution_member_pairings_array($id,$this->group->id);
                            }else{
                                $selected_group_members = $this->members_m->get_active_group_members_member_ids($this->group->id);
                            }
                            $contribution_fine_settings = $this->contributions_m->get_contribution_fine_settings($id,$this->group->id);
                            $response = array(
                                'status' => 1,
                                'message' => 'success',
                                'contribution_settings' => $post,
                                'contribution_fine_settings' => $contribution_fine_settings,
                                'selected_group_members' => $selected_group_members,
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Group contribution not available',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Bad request: Missing contribution id',
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

    function delete(){
        $usernames = array();
        $phones = array();
        $group_role_ids = array();
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id')?:'';
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $id = $this->input->post('id');
                    if($id){
                        if($post = $this->contributions_m->get_group_contribution($id,$this->group->id)){
                            $password = $this->input->post('password');
                            $identity = valid_phone($this->user->phone)?:$this->user->email;
                            if($this->ion_auth->login($identity,$password)){
                                if($this->transaction_statements_m->check_if_contribution_has_transactions(
                                    $post->id,
                                    $post->group_id)||$this->statements_m->check_if_contribution_has_transactions(
                                        $post->id,
                                        $post->group_id)){
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'The contribution has transactions associated to it, void all transactions associated to this account before deleting it'
                                    );
                                }else{
                                    if($this->contributions_m->safe_delete($post->id,$post->group_id)){
                                        $response = array(
                                            'status' => 1,
                                            'message' => 'Contribution deleted successfully'
                                        );
                                    }else{
                                        $response = array(
                                            'status' => 0,
                                            'message' => 'Contribution could not be deleted'
                                        );
                                    }
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'You entered the wrong password.'
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Group contribution not available',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Bad request: Missing contribution id',
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

    function hide(){
        $usernames = array();
        $phones = array();
        $group_role_ids = array();
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id')?:'';
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $id = $this->input->post('id');
                    if($id){
                        if($post = $this->contributions_m->get_group_contribution($id,$this->group->id)){
                            if($post->is_hidden){
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Sorry, the Contribution is already hidden',
                                );
                            }else{
                                $input = array(
                                    'is_hidden'=>1,
                                    'modified_by'=>$this->user->id,
                                    'modified_on'=>time(),
                                );
                                if($result = $this->contributions_m->update($post->id,$input)){
                                    $response = array(
                                        'status' => 1,
                                        'message' => $post->name.' was successfully hidden',
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Unable to hide '.$post->name,
                                    );
                                }
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Group contribution not available',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Bad request: Missing contribution id',
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
    
    function unhide(){
        $usernames = array();
        $phones = array();
        $group_role_ids = array();
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id')?:'';
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $id = $this->input->post('id');
                    if($id){
                        if($post = $this->contributions_m->get_group_contribution($id,$this->group->id)){
                            if(!$post->is_hidden){
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Sorry, the Contribution is not hidden',
                                );
                            }else{
                                $input = array(
                                    'is_hidden'=> 0,
                                    'modified_by'=>$this->user->id,
                                    'modified_on'=>time(),
                                );
                                if($result = $this->contributions_m->update($post->id,$input)){
                                    $response = array(
                                        'status' => 1,
                                        'message' => $post->name.' was successfully activated',
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Unable to activate '.$post->name,
                                    );
                                }
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Group contribution not available',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Bad request: Missing contribution id',
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

}?>