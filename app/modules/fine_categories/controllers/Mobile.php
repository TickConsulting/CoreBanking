<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Mobile extends Mobile_Controller{
    protected $validation_rules=array(
            array(
                    'field' =>   'name',
                    'label' =>   'Category Name',
                    'rules' =>   'xss_clean|trim|required|callback__is_unique_category_name',
                ),
            array(
                    'field' =>   'slug',
                    'label' =>   'Category Name Slug',
                    'rules' =>   'xss_clean|trim|required',
                ),
            array(
                    'field' =>  'amount',
                    'label' =>  'Category Fines amount',
                    'rules' =>  'xss_clean|trim|currency|required',
                ),
    );

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

    public function __construct(){
        parent::__construct();
        $this->load->model('fine_categories_m');
    }

    function _is_unique_category_name(){
        $id = $this->input->post('id');
        $group_id = $this->input->post('group_id');
        $slug = $this->input->post('slug');
        if($slug){
            if($this->fine_categories_m->get_by_slug($slug,$id,$group_id)){
                $this->form_validation->set_message('_is_unique_category_name','Another Fine Category by the name `'.$this->input->post('name').'` already exists');
                return FALSE;
            }
            else
            {
                return TRUE;
            }
            
        }
    }

    function create(){
        $group_role_ids = array();
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
                    $this->form_validation->set_rules($this->validation_rules);
                    if($this->form_validation->run()){
                        $name = $this->input->post('name');
                        $description = $this->input->post('description');
                        $slug = $this->input->post('slug');
                        $amount = currency($this->input->post('amount'));
                        $input = array(
                                'name'  =>  $name,
                                'slug'  =>  $slug,
                                'amount'  =>  $amount,
                                'group_id'  =>  $this->group->id,
                                'active'    =>  1,
                                'created_by'    =>  $this->user->id,
                                'created_on'    =>  time(),
                        );
                        if($id = $this->fine_categories_m->insert($input)){
                            $response = array(
                                'status' => 1,
                                'time' => time(),
                                'success' => $this->input->post('name').' as a fine category was successfully created',
                                'fine_category' =>array(
                                    'id' => $id,
                                    'name' => $name,
                                ),
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'time' => time(),
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
        $group_role_ids = array();
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
                    $this->form_validation->set_rules($this->validation_rules);
                    if($this->form_validation->run()){
                        $name = $this->input->post('name');
                        $description = $this->input->post('description');
                        $slug = $this->input->post('slug');
                        $amount = currency($this->input->post('amount'));
                        $id = $this->input->post('id');
                        if($fine_category = $this->fine_categories_m->get_group_fine_category($id,$this->group->id)){
                            $update = array(
                                'name'      =>  $this->input->post('name'),
                                'slug'      =>  $this->input->post('slug'),
                                'amount'    =>  currency($this->input->post('amount')),
                                'modified_by'=> $this->user->id,
                                'modified_on'=> time(),
                            );
                            if($this->fine_categories_m->update($fine_category->id,$update)){
                                $response = array(
                                    'status' => 1,
                                    'time' => time(),
                                    'success' => 'Fine category successfully updated',
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'time' => time(),
                                    'message' => 'Error updating fine category',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not find fine category to edit',
                                'time' => time(),
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

    function delete(){
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
                    if($fine_category = $this->fine_categories_m->get_group_fine_category($id,$this->group->id)){
                        if($this->transaction_statements_m->check_if_fine_category_has_transactions($fine_category->id,$this->group->id)){
                            $response = array(
                                    'status' => 0,
                                    'time' => time(),
                                    'message' => 'Fine category has existing transactions and can not be deleted',
                                );
                        }else{
                            if($this->fine_categories_m->safe_delete($fine_category->id,$this->group->id)){
                                $response = array(
                                    'status' => 1,
                                    'message' => "Fine category successfully deleted"
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Unable to safely delete fine category',
                                );
                            }
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not find fine category to delete',
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
                    if($post = $this->fine_categories_m->get_group_fine_category($id,$this->group->id)){
                        if($post->is_hidden){
                            $response = array(
                                'status' => 0,
                                'message' => 'Sorry, the Fine Category is already hidden',
                            );
                        }else{
                            $update = array(
                                'is_hidden'=>1,
                                'modified_by'=>$this->user->id,
                                'modified_on'=>time()
                            );
                            if($this->fine_categories_m->update($post->id,$update)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Success',
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Unable to hide the Fine Category',
                                );
                            }
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not find fine category to hide',
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
                    if($post = $this->fine_categories_m->get_group_fine_category($id,$this->group->id)){
                        if(!$post->is_hidden){
                            $response = array(
                                'status' => 0,
                                'message' => 'Sorry, the Fine Category is already active',
                            );
                        }else{
                            $update = array(
                                'is_hidden'=>NULL,
                                'modified_by'=>$this->user->id,
                                'modified_on'=>time()
                            );
                            if($this->fine_categories_m->update($post->id,$update)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Success',
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Unable to activate the Fine Category',
                                );
                            }
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not find fine category to activate',
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

    function get_group_fine_category_options(){
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
                    $fields = array(
                          'id',
                          'name',
                    );
                    $fine_categories = $this->fine_categories_m->get_group_fine_categories($this->group->id);
                    $fines = array();
                    foreach ($fine_categories as $fine_category) {
                        $fines[] = array(
                            'id' => $fine_category->id,
                            'name' => $fine_category->name,
                            'amount' => 0,
                            'balance' => 0,
                        );
                    }
                    $fine_categories = array_merge($fines);
                    $response = array(
                        'status' => 1,
                        'message' => 'Fine Categories',
                        'fine_category_options' => $fine_categories,
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


    function group_fine_categories_list(){
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
                    $total_rows = $this->fine_categories_m->count_all_group_expense_categories($this->group->id);
                    $pagination = create_custom_pagination('mobile',$total_rows,$records_per_page,$lower_limit,TRUE);
                    $categories = array();
                    $posts = $this->fine_categories_m->limit($pagination['limit'])->get_all($this->group->id);
                    foreach ($posts as $post) {
                        $categories[] = array(
                            'id' => $post->id,
                            'name' => $post->name,
                            'is_hidden' => $post->is_hidden?1:0,
                            'active' => $post->active?1:0,
                            'amount' => $post->amount?:0,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'success',
                        'fine_categories' => $categories,
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


}
?>