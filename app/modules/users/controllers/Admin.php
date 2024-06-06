<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller
{

    protected $data=array();

    protected $group_rules = array(
            array(
                    'field' =>  'name',
                    'label' =>  'Group Name',
                    'rules' =>  'required',
                ),
            array(
                    'field' =>  'description',
                    'label' =>  'Group Description',
                    'rules' =>  'required',
                )

        );

    protected $rules = array(
            array(
                    'field' =>  'first_name',
                    'label' =>  'First Name',
                    'rules' =>  'required',
                ),
            array(
                    'field' =>  'middle_name',
                    'label' =>  'Middle Name',
                    'rules' =>  '',
                ),
            array(
                    'field' =>  'last_name',
                    'label' =>  'last Name',
                    'rules' =>  'trim|required',
                ),
             array(
                    'field' =>  'password',
                    'label' =>  'Password',
                    'rules' =>  'trim',
                ),
            array(
                    'field' =>  'conf_password',
                    'label' =>  'Confirm Password',
                    'rules' =>  'trim|matches[password]',
                ),

            array(
                    'field' =>  'group_id[]',
                    'label' =>  'User Groups',
                    'rules' =>  'trim|required|callback_check_partner_banks',
                ),
            array(
                    'field' =>  'phone',
                    'label' =>  'Phone Number',
                    'rules' =>  'trim|required',
                ),
            array(
                    'field' =>  'email',
                    'label' =>  'Email Address',
                    'rules' =>  'trim|valid_email',
                ),
            array(
                    'field' =>  'ussd_pin',
                    'label' =>  'USSD PIN',
                    'rules' =>  'trim|required|numeric|min_length[4]|max_length[4]',
                ),
        );


	function __construct()
    {
        parent::__construct();
        $this->load->model('users_m');
        $this->load->model('groups/groups_m');
        $this->load->model('members/members_m');
        $this->load->model('banks/banks_m');
        $this->load->helper('pagination');
    }

    function index()
    {
        
    }

    function listing($id=''){
        $posts = $this->users_m->get_all_users();
        $json_file = json_encode($posts);
        $file="./logs/eazzykikundi_users_log.json";
        file_put_contents($file,$json_file);
        $group_id = '';
        $group_id = '';
        $name = '';
        $identity = '';
        if($this->input->get('filter')){
            $group_id = $this->input->post_get('user_group');
            $name = $this->input->post_get('name');
            $identity = trim($this->input->post_get('identity'));
        }
        $group_options = $this->input->get_post('group_option');
        $user_ids = array();
        if($group_options){
            $group_ids = array();
            if(in_array(1, $group_options)){
                $group_ids = $this->billing_m->get_paying_group_id_array();
            }
            if(in_array(2, $group_options)){
                $group_ids = array_merge($group_ids,$this->groups_m->get_group_ids_on_trial());
            }
            if(in_array(3, $group_options)){
                $group_ids = array_merge($group_ids,$this->groups_m->get_locked_group_ids());
            }
            if(in_array(4, $group_options)){
                $group_ids = array_merge($group_ids,$this->groups_m->get_group_ids_with_expired_trial());
            }
            
            if($group_ids){
                $user_ids = $this->members_m->get_user_ids_by_group_ids($group_ids);
            }
        }
        if(is_numeric($id)){
            $group_id = $id;
            $_GET['group_id'] = $id;
        }
        $this->data['group_options'] = array(
            1 => 'Paying Groups',
            2 => 'Groups On Trial',
            3 => 'Locked Groups',
            4 => 'Groups Trial Expired ',
        );
        $total_rows = $this ->users_m->count_all_active_users($group_id,$identity,$name,$user_ids);
        $pagination = create_pagination('admin/users/listing/pages', $total_rows,100,5,TRUE);
        $this->data['groups'] = $this->users_m->get_group_options();
        if($this->input->get_post('generate_excel') == 1){
            $posts = $this->users_m->get_all_users($group_id,$identity,$name,$user_ids);
            $this->data['user_group_pair'] = $this->groups_m->get_groups_for_user_pairing($posts);
            $this->data['posts'] = $posts;
            $this->data['settings'] = $this->application_settings;
            $json_file = json_encode($this->data);
            //print_r($json_file);die;
            $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/users/listing',$this->application_settings->application_name.' List of Users');
            print_r($response);die;
        }else{
            $this->data['posts'] = $this->users_m->limit($pagination['limit'])->get_all_users($group_id,$identity,$name,$user_ids);
            $this->data['pagination'] = $pagination;
        }
        
        $this->template->title('All Users List')->build('admin/listing',$this->data);
    }

    function list_demo_requests(){
        $total_rows = $this ->users_m->count_all_demo_users();
         $pagination = create_pagination('admin/users/list_demo_requests/pages', $total_rows,100,5,TRUE);
        $this->data['posts'] = $this->users_m->limit($pagination['limit'])->get_all_demo_users();
        $this->template->title('All Demo Users List')->build('admin/demo_listing',$this->data);
    }

    function export_users(){
        $group_id = '';
        $name = '';
        $identity = '';
        if($this->input->get('filter')){
            $group_id = $this->input->post_get('user_group');
            $name = $this->input->post_get('name');
            $identity = trim($this->input->post_get('identity'));
        }
        $group_options = $this->input->get_post('group_option');
        $user_ids = array();
        if($group_options){
            $group_ids = array();
            if($group_ids){
                $user_ids = $this->members_m->get_user_ids_by_group_ids($group_ids);
            }
        }
        $posts = $this->users_m->get_all_users($group_id,$identity,$name,$user_ids);
        $data['users'] = $posts;
        $file = "./logs/reset_groups/allusers_".time().".txt";
        file_put_contents($file,json_encode($data));
        $response = $this->curl_post_data->curl_post_json_excel($file,'https://excel.chamasoft.com/users/listing',$this->application_settings->application_name.' List of Users');
        print_r($response);die;// $this->curl->download_file($file);
        $this->session->set_flashdata('success','Exported'.count($posts).'users');
        redirect('admin/users/listing');
    }

    function import_users(){
        //if(preg_match('/\.local/', $_SERVER['HTTP_HOST'])){
            set_time_limit(0);
            ini_set('upload_max_filesize',"300M");
            ini_set('post_max_size',"300M");            
            if($_FILES){                
                set_time_limit(0);
                ini_set('memory_limit','2048M');
                ini_set('max_execution_time', 1200);

                if(isset($_FILES['group_file'])){                   
                    $file = $_FILES['group_file'];
                    if($file['tmp_name']){
                        $file_path = $file['tmp_name'];
                        if($file_path){
                            $contents = file_get_contents($file_path);
                            if($contents){
                                $data = json_decode($contents);
                                $users = isset($data->users)?$data->users:array();
                                if($users){
                                    $count = 0;
                                    foreach ($users as $user) {
                                        $phone = str_replace('+','', $user->phone);
                                        $password = $user->password;
                                        $email = $user->email;
                                        $additional_data = array();
                                        $identity = $user->phone?:$user->email;
                                        $old_id = $user->id;
                                        if($exist_user = $this->ion_auth->get_user_by_phone($phone)){
                                            $id = $exist_user->id;
                                        }else{
                                            if($exist_user = $this->ion_auth->get_user_by_email($phone)){
                                                $id = $exist_user->id;
                                            }else{
                                                unset($user->id);
                                                unset($user->phone);
                                                unset($user->password);
                                                unset($user->email);
                                                $additional_data = (array)$user;
                                                $id = $this->ion_auth->register($phone,$password,$email,$additional_data,array(2),TRUE);
                                                if($id){

                                                }else{
                                                    echo $phone;
                                                    echo $this->ion_auth->errors();
                                                }
                                            }
                                        }                                
                                        $update = array(
                                            'password' => $password,
                                        );
                                        $this->users_m->update_user($id,$update);
                                    }
                                }else{

                                }
                            }else{

                            }
                        }else{

                        }                        
                    }else{
                        print_r($file);die;
                    }
                }
            }
        // }else{
        //     echo 'Only for localhost';
        // }
        $this->template->title('Import Users')->build('admin/import_users',$this->data);
    }

    function create()
    {
        $post = new StdClass();
        $sel_groups = array();
        $pass_rules =  array(
            array(
                    'field' =>  'password',
                    'label' =>  'Password',
                    'rules' =>  'trim|required|min_length[8]|max_length[20]',
                ),
            array(
                    'field' =>  'conf_password',
                    'label' =>  'Confirm Password',
                    'rules' =>  'trim|required|matches[password]',
                ));
        $this->rules = array_merge($this->rules,$pass_rules);

        $this->form_validation->set_rules($this->rules);

        if($this->form_validation->run())
        {
           $additional_data = array(
                    'username'          =>      $this->input->post('first_name'),
                    'active'            =>      1, 
                    'ussd_pin'          =>      rand(1000,9999),
                    'first_name'        =>      $this->input->post('first_name'), 
                    'middle_name'       =>      $this->input->post('middle_name'), 
                    'last_name'         =>      $this->input->post('last_name'),
                    'ussd_pin'          =>      $this->input->post('ussd_pin'),
                    'created_on'        =>      time(),
                    'created_by'        =>      $this->ion_auth->get_user()->id,
                    );
           $phone = $this->input->post('phone');
           $email = $this->input->post('email');
           $password = $this->input->post('password');
           $groups = $this->input->post('group_id');

           //print_r($groups);die;

           $id = $this->ion_auth->register($phone,$password,$email, $additional_data,$groups);
           if($id)
           {
                $result = TRUE;
                if(in_array(4,$this->input->post('group_id'))){
                    $partner_bank_options = $this->input->post('partner_bank_options');
                    foreach($partner_bank_options as $bank_id):
                        $input = array(
                            'user_id'=>$id,
                            'bank_id'=>$bank_id,
                            'created_on'=>time(),
                            'created_by'=>$this->user->id,
                        );
                        if($this->banks_m->insert_user_bank_pairing($input)){

                        }else{
                            $result = FALSE;
                        }
                    endforeach;
                }
                if($result){
                    //$this->session->set_flashdata('info',"Successfully saved bank and user parings");
                }else{
                    $this->session->set_flashdata('warning',"Could not save bank and user parings");
                }
                $this->session->set_flashdata('success',$this->ion_auth->messages());
                if($this->input->post('new_item'))
                {
                    redirect('admin/users/create','refresh');
                }
                else
                {
                    redirect('admin/users/edit/'.$id,'refresh');
                }
           }
           else
           {
               $this->session->set_flashdata('error',$this->ion_auth->errors()); 
                redirect('admin/users/create','refresh');
           }
        }
        else
        {
            foreach ($this->rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }

        $this->data['groups'] = $this->users_m->get_group_options();
        $this->data['selected_partner_bank_options'] = array();
        $this->data['investment_groups'] = $this->groups_m->get_options();
        $this->data['partner_bank_options'] = $this->banks_m->get_partner_bank_options();
        $this->data['post'] = $post;
        $this->data['sel_groups'] = $sel_groups;
        $this->template->title('Create New User')->build('admin/form',$this->data);
    }

    function edit($id = 0){
        $id OR redirect('admin/users/listing');
        $post = new stdClass();
        $user = $this->ion_auth->get_user($id);
        if(!$user){
            $this->session->set_flashdata('error','Sorry the user does not exist');
            redirect('admin/users/listing');
        }
        $sel_groups =  $this->users_m->get_user_groups_option($user->id);
        $selected_partner_bank_options = $this->banks_m->get_user_bank_pairings_array($user->id);
        $this->form_validation->set_rules($this->rules);
        if($this->form_validation->run()){
            $groups = $this->input->post('group_id');
            $input = array(
                'first_name'    => $this->input->post('first_name'),
                'last_name'     => $this->input->post('last_name'),
                'middle_name'   => $this->input->post('middle_name'),
                'phone'         => valid_phone($this->input->post('phone')),
                'email'         => $this->input->post('email'),
                'first_time_login_status'         => 0,
                'ussd_pin'      => $this->input->post('ussd_pin'),
                'modified_on'   => time(),
                'modified_by'   => $this->ion_auth->get_user()->id,
            );
            if($this->input->post('password')){
                $input = array_merge($input,array('password_check'=>$this->ion_auth->hash_password($this->input->post('password'),'','',1)));
                $input = array_merge($input,array('password'=>$this->input->post('password')));
            }
            $update = $this->ion_auth->update($user->id, $input);
            if($update){
                $this->ion_auth->remove_from_group($sel_groups, $user->id);
                $this->ion_auth->add_to_group($this->input->post('group_id'), $user->id);
                $this->session->set_flashdata('success',$this->ion_auth->messages());
                $result = TRUE;
                $investment_group = $this->input->post('investment_group');
                if($investment_group){
                    $members = $this->members_m->get_member_by_user_id($user->id);
                    if(count($members)  == 1){
                        foreach ($members as $member) {
                            $this->members_m->update($member->id,array('group_id'=>$investment_group[0]));
                        } 
                    }else{
                        if(count($members) == count($investment_group)){
                            foreach ($members as $key => $member) {
                                $this->members_m->update($member->id,array('group_id'=>$investment_group[$key]));
                            }
                        }else{
                            if($this->ion_auth->is_group_account_manager($id)){
                                $user_group_id = $this->ion_auth->get_group_by_name('group-account-manager');
                                $data = array(
                                    'group_id' => array($user_group_id),
                                    'user_id' => $user->id,
                                    'created_by' => $user->id,
                                    'active' => 1,
                                    'created_on' => time(),
                                );
                                $this->group_account_managers_m->insert($data);
                            }
                        }
                    }
                }
                if($this->ion_auth->is_bank_admin($id)){
                    $this->banks_m->delete_user_bank_pairings($id);
                    $partner_bank_options = $this->input->post('partner_bank_options');
                    foreach($partner_bank_options as $bank_id):
                        $input = array(
                            'user_id'=>$id,
                            'bank_id'=>$bank_id,
                            'created_on'=>time(),
                            'created_by'=>$this->user->id,
                        );
                        if($this->banks_m->insert_user_bank_pairing($input)){

                        }else{
                            $result = FALSE;
                        }
                    endforeach;
                }
                if($result){
                    //$this->session->set_flashdata('info',"Successfully saved bank and user parings");
                }else{
                    $this->session->set_flashdata('warning',"Could not save bank and user parings");
                }
                if($this->input->post('new_item')){
                    redirect('admin/users/create');
                }else{
                    redirect('admin/users/listing');
                }
            }else{
                $this->session->set_flashdata('error',$this->ion_auth->errors());
                redirect('admin/users/listing');
            }
            die;
        }else{
            foreach ($this->rules as $key => $field) {
                $field_name = $field['field'];
                $post->$field_name = set_value($field['field']);
            }
        }

        if($this->input->post()){
            $this->data['post'] = $post;
            $this->data['sel_groups'] = array();
        }else{
            $this->data['post'] = $user;
            $this->data['sel_groups'] = $sel_groups;
        }
        //print_r($post);die;
        $this->data['selected_partner_bank_options'] = $selected_partner_bank_options;
        $this->data['partner_bank_options'] = $this->banks_m->get_partner_bank_options();
        $this->data['groups'] = $this->users_m->get_group_options();
        $this->data['investment_groups'] = $this->groups_m->get_options();
        $this->template->title('Edit '.ucwords($user->first_name))->build('admin/form',$this->data);
    }

    function disable($id=0, $redirect=TRUE)
    {
        $id OR redirect('admin/users/listing');
        $post = new StdClass();

        $user = $this->ion_auth->get_user($id);

        if(!$user)
        {
            $this->session->set_flhdata('error','Sorry the user does not exist');
            if($redirect)
            {
                redirect('admin/users/listing');
            }
            else
            {
                return TRUE;
            }
        }

        if($user->active !=1)
        {
            $this->session->set_flashdata('error','Sorry the user is already disable');
            redirect('admin/users/listing');
        }

        $update = $this->ion_auth->update($user->id, array(
                'active'    =>  NULL,
                'modified_by'   =>  $this->ion_auth->get_user()->id,
                'modified_on'   =>  time(),
            ));
        if($update)
        {
            $this->session->set_flashdata('success','User successfully disable');
        }

        else
        {
            $this->sesssion->set_flashdata('error','Unable to disable user');
        }

        if($redirect)
        {
            redirect('admin/users/listing');
        }
        else
        {
            return TRUE;
        }


    }

     function activate($id=0, $redirect=TRUE)
    {
        $id OR redirect('admin/users/listing');
        $post = new StdClass();

        $user = $this->ion_auth->get_user($id);

        if(!$user)
        {
            $this->session->set_flhdata('error','Sorry the user does not exist');
            if($redirect)
            {
                redirect('admin/users/listing');
            }
            else
            {
                return TRUE;
            }
        }

        if($user->active ==1)
        {
            $this->session->set_flashdata('error','Sorry the user is already active');
            if($redirect)
            {
                redirect('admin/users/listing');
            }
            else{
                return TRUE;
            } 
        }

        $update = $this->ion_auth->update($user->id, array(
                'active'    =>  1,
                'modified_by'   =>  $this->ion_auth->get_user()->id,
                'modified_on'   =>  time(),
            ));
        if($update)
        {
            $this->session->set_flashdata('success','User successfully activated');
        }

        else
        {
            $this->sesssion->set_flashdata('error','Unable to activate user');
        }

        if($redirect)
        {
            redirect('admin/users/listing');
        }
        else
        {
            return TRUE;
        }


    }

     function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_disable'){
            for($i=0;$i<count($action_to);$i++){
                $this->disable($action_to[$i],FALSE);
            }
        }else if($action == 'bulk_activate'){
            for($i=0;$i<count($action_to);$i++){
                $this->activate($action_to[$i],FALSE);
            }
        }
        redirect('admin/users/listing');
    }





    /**
   
   from here and below is all functions for the user groups

    ***/

    function create_user_group(){
        $post = new StdClass();
        $this->form_validation->set_rules($this->group_rules);

        if($this->form_validation->run())
        {
            $input = array(
                    'name'          =>  $this->input->post('name'),
                    'description'   =>  $this->input->post('description'),
                    'created_on'    => time(),
                    'created_by'    => $this->ion_auth->get_user()->id,
                );

            //create group
             $id = $this->users_m->insert_group($input);
            if($id)
            {
                $this->session->set_flashdata('success','New group successfully created');

                if($this->input->post('new_item'))
                {
                    redirect('admin/users/create_user_group','redirect');
                }
                else
                {
                    redirect('admin/users/edit_group/'.$id,'refresh');
                }
            }
            else
            {
                $this->session->set_flashdata('error','Unable to create a new group');
                redirect('admin/users/create_user_group','redirect');   
            }
        }
        else
        {
            foreach ($this->group_rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }

        $this->data['post'] = $post;
        $this->template->title('Create New User Group')->build('admin/form_user_group',$this->data);
    }

    function edit_group($id=0){
        $id OR redirect('admin/users/user_groups_listing');

        $group = $this->users_m->get_group($id);

        $post = new StdClass();

        if(!$group)
        {
            $this->session->set_flashdata('error','Sorry the group does not exist');
            redirect('admin/users/user_groups_listing');
        }

        $this->form_validation->set_rules($this->group_rules);
        if($this->form_validation->run())
        {
            $input = array(
                    'name'          =>  $this->input->post('name'),
                    'description'   =>  $this->input->post('description'),
                    'modified_by'   =>  $this->ion_auth->get_user()->id,
                    'modified_on'   =>  time(),
                );
            $update = $this->users_m->update_group($id,$input);
            if($update)
            {
                $this->session->set_flashdata('success','successfully updated '.ucwords($this->input->post('name')).' group');

                if($this->input->post('new_item'))
                {
                    redirect('admin/users/create_user_group','redirect');
                }
                else
                {
                    redirect('admin/users/user_groups_listing/','refresh');
                }
            }
            else
            {
                $this->session->set_flashdata('error','Unable to update '.ucwords($this->input->post('name')).' group');
                redirect('admin/users/create_user_group','redirect');   
            }
        }
        else
        {
            foreach ($this->group_rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value= set_value($field['field']);
            }
        }

        if($this->input->post('submit'))
        {
            $this->data['post'] = $post;
        }
        else
        {
            $this->data['post'] = $group;
        }

        $this->template->title('Edit '.ucwords($group->name).' User Group')->build('admin/form_user_group',$this->data);
    }

    function user_groups_listing(){
        $total_rows = $this ->users_m->count_all_active_groups();
        $pagination = create_pagination('admin/users/user_groups_listing', $total_rows,NULL,4, TRUE);

        $this->data['posts'] = $this->users_m->limit($pagination['limit'])->get_all_groups();
        $this->data['pagination'] = $pagination;
        $this->template->title('List User Groups')->build('admin/user_groups_listing',$this->data);
    }


    function delete_group($id=0,$redirect=TRUE){
        $id OR redirect('admin/users/user_groups_listing');

        $post = $this->users_m->get_group($id);

        $res = $this->users_m->delete_group($id);
        if($res)
        {
            $this->session->set_flashdata('success',$post->name.' group successfully deleted');
        }
        else
        {
            $this->session->set_flashdata('error','Unable to delete'.$post->name.' group');
        }

        if($redirect)
        {
            redirect('admin/users/user_groups_listing','refresh');
        }
        else
        {
            return TRUE;
        }
    }

    function user_group_action(){
        $action_to = $this->input->post('action_to');

        if($action_to && $this->input->post('btnAction')=='bulk_delete')
        {
            for($i=0;$i<count($action_to);$i++)
            {
                $this->delete_group($action_to[$i],FALSE);
            }
        }

        redirect('admin/users/user_groups_listing','refresh');
    }

     function check_partner_banks(){
        if(in_array(4,$this->input->post('group_id'))){
            $partner_bank_options = $this->input->post('partner_bank_options');
            if(!empty($partner_bank_options)){
                return TRUE;
            }else{
                $this->form_validation->set_message('check_partner_banks', 'Please select a bank the user belongs to.');
                return FALSE;
            }
        }else{
            return TRUE;
        }
    }

    function ajax_search_options(){
        $this->users_m->get_search_options();
    }


    function search()
    {
        $this->template->title('User Search')->build('admin/search',$this->data);
    }

    function ajax_view($id = 0){
        $post = new stdClass();
        $post = $this->ion_auth->get_user($id);
        if($post){
            $groups = $this->groups_m->current_user_groups($id);
            echo '
            <div>
                <strong> Quick Actions: </strong>
                <a href="'.site_url('admin/users/edit/'.$post->id).'" class="btn btn-xs default">
                    <i class="fa fa-edit"></i> Edit &nbsp;&nbsp;
                </a>
            </div>
            <hr/>
            ';
            echo '
                <div class="mt-element-list">
                    <div class="mt-list-head list-todo red">
                        <div class="list-head-title-container">
                            <div class="list-head-count">
                                <div class="list-head-count-item">
                                    <i class="fa fa-users"></i> '.$post->first_name.' '.$post->last_name.' </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-list-container list-todo">
                        <div class="list-todo-line"></div>
                        <ul>
                            <li class="mt-list-item">
                                <div class="list-todo-icon bg-white">
                                    <i class="fa fa-info"></i>
                                </div>
                                <div class="list-todo-item dark">
                                    <a class="list-toggle-container" data-toggle="collapse" href="#task-1" aria-expanded="false">
                                        <div class="list-toggle done uppercase">
                                            <div class="list-toggle-title bold">Group Information</div>
                                            <div class="badge badge-default pull-right bold"></div>
                                        </div>
                                    </a>
                                    <div class="task-list panel-collapse collapse in" id="task-1">
                                        <ul>
                                            <li class="task-list-item done">
                                                <div class="task-icon">
                                                    <a href="javascript:;">
                                                        <i class="fa fa-users"></i>
                                                    </a>
                                                </div>
                                                <div class="task-content">
                                                    <h4 class="uppercase bold">
                                                        <a href="javascript:;">'.$post->first_name.' '.$post->last_name.'</a>
                                                    </h4>
                                                    <ul class="">
                                                        ';
                                                        echo '
                                                        <li><strong>First Name: </strong>'.$post->first_name.'</li>
                                                        <li><strong>Last Name: </strong>'.$post->last_name.'</li>
                                                        <li><strong>E-mail: </strong>'.$post->email.'</li>
                                                        <li><strong>Phone: </strong>'.$post->phone.'</li>   
                                                        <li><strong>Registered On: </strong>'.timestamp_to_date_and_time($post->created_on).'</li>
                                                        <li><strong>Last Seen: </strong>'.timestamp_to_date_and_time($post->last_login).'</li>     
                                                    </ul>
                                                </div>
                                            </li>
                                        </ul>
                                        <div class="task-footer bg-grey">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <a class="task-trash" href="'.site_url("admin/users/edit/".$post->id).'">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </div>  
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="mt-list-item">
                                <div class="list-todo-icon bg-white">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="list-todo-item dark">
                                    <a class="list-toggle-container" data-toggle="collapse" href="#task-2" aria-expanded="false">
                                        <div class="list-toggle done uppercase">
                                            <div class="list-toggle-title bold">Groups</div>
                                            <div class="badge badge-default pull-right bold"></div>
                                        </div>
                                    </a>
                                    <div class="task-list panel-collapse collapse" id="task-2">
                                        <ul>
                                            <li class="task-list-item done">
                                                <div class="task-icon">
                                                    <a href="javascript:;">
                                                        <i class="fa fa-list-alt"></i>
                                                    </a>
                                                </div>
                                                <div class="task-content">
                                                    <h4 class="uppercase bold">
                                                        <a href="javascript:;">Group List</a>
                                                    </h4>
                                                    <p>';
                                                        if($groups):
                                                            echo '
                                                            <table class="table table-striped table-bordered table-advance table-condensed table-hover ">
                                                                <thead>
                                                                    <tr>
                                                                        <th width="8px">
                                                                            #
                                                                        </th>
                                                                        <th>
                                                                           Group Name
                                                                        </th>
                                                                        <th>
                                                                            Actions
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>';
                                                                    
                                                                    $i=1;
                                                                    foreach($groups as $group):
                                                                    echo '
                                                                        <tr>
                                                                            <td>'.$i.'</td>
                                                                            <td>'.$group->name.'</td>
                                                                            <td class="actions">';
                                                                            echo '
                                                                                <a href="'.site_url('admin/groups/search/'.$group->id).'" class="btn btn-xs btn-blue">
                                                                                    <i class="icon-eye"></i> View Group Profile &nbsp;&nbsp; 
                                                                                </a> ';
                                                                            echo'  
                                                                            </td>
                                                                        </tr>';
                                                                    $i++; endforeach; 
                                                                    echo '
                                                                </tbody>
                                                            </table>';
                                                        else:
                                                            echo'
                                                            <div class="alert alert-info">
                                                                <h4 class="block">Information! No records to display</h4>
                                                                <p>
                                                                    Sorry, no groups registered under this user profile.
                                                                </p>
                                                            </div>';
                                                        endif;
                                                    echo '
                                                    </p>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            ';
        }else{
            echo '<hr/>
                <div class="alert alert-info">
                    <strong>Info!</strong> Could not find user profile.
                </div>
            ';
        }
    }

    function verify_admin_otp($phone = 0){
        $phone OR redirect('verify_otp');
        $user = $this->ion_auth->get_user_by_identity($phone);
        if($user){
            $input = array(
                'is_validated'=>1,
                'modified_on'=>time(),
                'modified_by'=>$user->id,
            );
            if($this->users_m->update($user->id ,$input)){
                $response = array(
                    'status'=>1,
                    'refer'=>site_url('checkin'),
                    'message' =>'OTP confirmed  successfully',
                );
                echo json_encode($response); 
            }else{
                echo 'could not get user';
            }
        }
    }

    function get_verify_code($phone=0){
        $phone OR redirect('admin/users/listing');
        $user = $this->ion_auth->get_user_by_phone($phone);
        print_r($user);
    }

    function delete_user($phone='',$user_id=0){
        ($phone?:$user_id) OR redirect('admin/users/listing');
        $res  = $this->ion_auth->delete_user($phone,$user_id);
        if($res){
            $this->session->set_flashdata('info','Returned TRUE');
        }else{
            $this->session->set_flashdata('info','Returned FALSE');
        }

        redirect('admin/users/listing');
    }
}