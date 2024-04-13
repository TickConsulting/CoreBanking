<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Mobile extends Mobile_Controller{
    protected $validation_rules=array(
           array(
            'field' => 'name',
            'label' => 'Group Role Name',
            'rules' => 'xss_clean|trim|required|callback__is_unique_name',
        ),array(
            'field' => 'description',
            'label' => 'Group Role Description',
            'rules' => 'xss_clean|trim',
        ),
    );

    function __construct(){
        parent::__construct();
        $this->load->model('group_roles_m');
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
            )
        );
    }



    function get_group_unassigned_roles(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id')?:0;
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id')?:0;
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $group_roles = $this->group_roles_m->get_group_role_options($this->group->id);
                    $roles = array();
                    foreach ($group_roles as $role_id => $role_name) {
                        if($this->members_m->check_if_group_role_id_is_assigned($role_id,$this->group->id)){
                            $roles+=array(
                                $role_name => 0,
                            ); 
                        }else{
                            $roles+=array(
                                $role_name => 1,
                            );  
                        }
                    }
                    
                    $response = array(
                        'status' => 1,
                        'message' => 'success',
                        'time' => time(),
                        'roles_status' => $roles,
                        'member_has_role' => $this->member->group_role_id?1:0,
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

    function get_group_unassigned_role_ids(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id')?:0;
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id')?:0;
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $group_roles = $this->group_roles_m->get_group_role_options($this->group->id);
                    $roles = array();
                    foreach ($group_roles as $role_id => $role_name) {
                        if($this->members_m->check_if_group_role_id_is_assigned($role_id,$this->group->id)){
                            $roles+=array(
                                $role_id => 0,
                            ); 
                        }else{
                            $roles+=array(
                                $role_id => 1,
                            );  
                        }
                    }
                    
                    $response = array(
                        'status' => 1,
                        'message' => 'success',
                        'time' => time(),
                        'roles_status' => $roles,
                        'member_has_role' => $this->member->group_role_id?1:0,
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


    function _is_unique_name(){
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $group_id = $this->input->post('group_id');
        if($name){
            if($this->group_roles_m->get_by_name($name,$id,$group_id))
            {
                $this->form_validation->set_message('_is_unique_name','Another Group Role by the name `'.$this->input->post('name').'` already exists');
                return FALSE;
            }
            else
            {
                return TRUE;
            }
            
        }
        
    }

    function group_roles_list(){
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
                    $lower_limit = $this->input->post('lower_limit')?:0;
                    $upper_limit = $this->input->post('upper_limit')?:100;
                    $records_per_page = $upper_limit - $lower_limit;
                    if($lower_limit>$upper_limit){
                        $records_per_page = 100;
                    }
                    $total_rows = $this->group_roles_m->count_all_group_roles($this->group->id);
                    $pagination = create_custom_pagination('mobile',$total_rows,$records_per_page,$lower_limit,TRUE);
                    $categories = array();
                    $posts = $this->group_roles_m->limit($pagination['limit'])->get_all_group_roles($this->group->id);
                    $member_group_role_ids = $this->members_m->get_member_group_role_ids($this->group->id);
                    foreach ($posts as $post) {
                        $categories[] = array(
                            'id' => $post->id,
                            'description' => $post->description,
                            'name' => $post->name,
                            'active' => $post->active?1:0,
                            'is_editable' => $post->is_editable?1:0,
                            'in_use' => in_array($post->id,$member_group_role_ids)?1:0,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'success',
                        'group_roles' => $categories,
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

    function create(){
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
                    $this->form_validation->set_rules($this->validation_rules);
                    if($this->form_validation->run()){
                        $input = array(
                                'name'=>$this->input->post('name'),
                                'description'=>$this->input->post('description'),
                                'group_id'=>$this->group->id,
                                'is_editable'=>1,
                                'active'=>1,
                                'created_on'=>time(),
                                'created_by'=>$this->user->id
                            );
                        $id = $this->group_roles_m->insert($input);
                        if($id){
                            $response = array(
                                'status' => 1,
                                'success' => $this->input->post('name').' as a group role was successfully created',
                                'id' =>$id,
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Unable to create a new Fine Category',
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

    function edit(){
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
                    $id = $this->input->post('id');
                    if($post = $this->group_roles_m->get_group_role($id,$this->group->id)){
                        if($post->is_editable){
                            $this->form_validation->set_rules($this->validation_rules);
                            if($this->form_validation->run()){
                                $update = array(
                                        'name'=>$this->input->post('name'),
                                        'description'=>$this->input->post('description'),
                                        'modified_on'=>time(),
                                        'modified_by'=>$this->user->id
                                    );
                                if($this->group_roles_m->update($post->id,$update)){
                                    $response = array(
                                        'status' => 1,
                                        'success' => $this->input->post('name').' as a group role was successfully edited',
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Unable to create a new Fine Category',
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
                                'message' => 'You are not allowed to edit this group role'
                            ); 
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not find group role to edit'
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
                    $id = $this->input->post('id');
                    if($post = $this->group_roles_m->get_group_role($id,$this->group->id)){
                        if($post->is_editable){
                            if(!$post->active){
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Group role already hidden',
                                );
                            }else{
                                if($this->group_roles_m->update($post->id,array(
                                        'active' => 0,
                                        'modified_by' => $this->user->id,
                                        'modified_on' => time(),
                                    ))){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Group role successfully hidden'
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Unable to update group role',
                                    );
                                }
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'You are not allowed to edit this group role'
                            ); 
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not find group role to edit'
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
                    $id = $this->input->post('id');
                    if($post = $this->group_roles_m->get_group_role($id,$this->group->id)){
                        if($post->is_editable){
                            if($post->active){
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Group role already active',
                                );
                            }else{
                                if($this->group_roles_m->update($post->id,array(
                                        'active' => 1,
                                        'modified_by' => $this->user->id,
                                        'modified_on' => time(),
                                    ))){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Group role successfully activated'
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Unable to update group role',
                                    );
                                }
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'You are not allowed to edit this group role'
                            ); 
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not find group role to edit'
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
}
?>