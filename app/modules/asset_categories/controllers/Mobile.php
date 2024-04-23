<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mobile extends Mobile_Controller{

    function __construct(){
        parent::__construct();
        $this->load->model('asset_categories/asset_categories_m');
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

        ));
    }

    protected $validation_rules=array(
        array(
                'field' =>   'name',
                'label' =>   'Category Name',
                'rules' =>   'xss_clean|trim|required',
            ),
        array(
                'field' =>   'slug',
                'label' =>   'Category Name Slug',
                'rules' =>   'xss_clean|trim|required|callback__is_unique_category_name',
            ),
        array(
                'field' =>   'description',
                'label' =>   'Category Description',
                'rules' =>   'xss_clean|trim',
            ),
    );

    function _is_unique_category_name(){
        $id = $this->input->post('id');
        $group_id = $this->input->post('group_id');
        $slug = $this->input->post('slug');
        if($slug){
            if($ans =$this->asset_categories_m->get_by_slug($slug,$id,$group_id)){
                $this->form_validation->set_message('_is_unique_category_name','Another Asset Category by the name '.$this->input->post('name').' already exists');
                return FALSE;
            }else{
                return TRUE;
            }
        }  
    }


    function get_group_asset_categories(){
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
                    $total_rows = $this->asset_categories_m->count_group_asset_categories($this->group->id);
                    $pagination = create_custom_pagination('mobile',$total_rows,$records_per_page,$lower_limit,TRUE);
                    $categories = array();
                    $group_asset_categories = $this->asset_categories_m->limit($pagination['limit'])->get_group_asset_categories($this->group->id);
                    foreach ($group_asset_categories as $group_asset_category) {
                        $categories[] = array(
                            'id' => $group_asset_category->id,
                            'name' => $group_asset_category->name,
                            'description' => $group_asset_category->description,
                            'is_hidden' => $group_asset_category->is_hidden?1:0,
                            'active' => $group_asset_category->active?1:0,
                        );
                    }
                    
                    $response = array(
                        'status' => 1,
                        'message' => 'success',
                        'asset_categories' => $categories,
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

    function create(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $_POST['slug'] = generate_slug($this->input->post('name'));
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->group_role_id || $this->member->is_admin){
                        $this->form_validation->set_rules($this->validation_rules);
                        if($this->form_validation->run()){
                            $id = $this->asset_categories_m->insert(array(
                                'name'  =>  $this->input->post('name'),
                                'description' => $this->input->post('description'),
                                'slug'  =>  $this->input->post('slug'),
                                'group_id'  =>  $this->group->id,
                                'active'    =>  1,
                                'created_by'    =>  $this->user->id,
                                'created_on'    =>  time(),
                                )
                            );
                            if($id){
                                $response = array(
                                    'status' => 1,
                                    'message' => $this->input->post('name').' as a asset category was successfully',
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Unable to create a new asset category',
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
                            'message' => 'Only group officials are allowed to perform this operation',
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
        $_POST['slug'] = generate_slug($this->input->post('name'));
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->group_role_id || $this->member->is_admin){
                        $this->form_validation->set_rules($this->validation_rules);
                        if($this->form_validation->run()){
                            $id = $this->input->post('id');
                            if($post = $this->asset_categories_m->get($id,$this->group->id)){
                                $update = $this->asset_categories_m->update($post->id,array(
                                    'name'      =>  $this->input->post('name'),
                                    'slug'      =>  $this->input->post('slug'),
                                    'description' => $this->input->post('description'),
                                    'modified_by'=> $this->user->id,
                                    'modified_on'=> time(),
                                ));
                                if($update){
                                    $response = array(
                                        'status' => 1,
                                        'message' => $this->input->post('name').' successfully updated',
                                    );
                                }else{
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Unable to update Fine Category',
                                    );
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Could not find group asset category to edit',
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
                            'message' => 'Only group officials are allowed to perform this operation',
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
        $_POST['slug'] = generate_slug($this->input->post('name'));
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->group_role_id || $this->member->is_admin){
                        $id = $this->input->post('id');
                        if($post = $this->asset_categories_m->get($id,$this->group->id)){
                            if($post->is_hidden){
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Asset category is already headen',
                                );
                            }else{
                                if($this->asset_categories_m->update($post->id,array(
                                        'is_hidden' => 1,
                                        'modified_on' => time(),
                                        'modified_by' => $this->user->id,
                                    ))){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Asset category successfully hidden',
                                    );
                                }else{
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Unable to hide asset category',
                                    );
                                }
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not find group asset category to edit',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Only group officials are allowed to perform this operation',
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
        $_POST['slug'] = generate_slug($this->input->post('name'));
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->group_role_id || $this->member->is_admin){
                        $id = $this->input->post('id');
                        if($post = $this->asset_categories_m->get($id,$this->group->id)){
                            if(!$post->is_hidden){
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Asset category is visible and cannot be hidden',
                                );
                            }else{
                                if($this->asset_categories_m->update($post->id,array(
                                        'is_hidden' => '',
                                        'modified_on' => time(),
                                        'modified_by' => $this->user->id,
                                    ))){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Asset category successfully unhidden',
                                    );
                                }else{
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Unable to unhide asset category',
                                    );
                                }
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not find group asset category to edit',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Only group officials are allowed to perform this operation',
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
        echo encrypt_json_encode(array('response'=>$response));
    }
}