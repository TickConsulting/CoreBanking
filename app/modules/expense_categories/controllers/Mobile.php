<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Mobile extends Mobile_Controller{
    protected $validation_rules=array(
        array(
                'field' =>   'name',
                'label' =>  'Category Name',
                'rules' =>   'xss_clean|trim|required',
            ),
        array(
                'field' =>   'slug',
                'label' =>   'Category Name',
                'rules' =>   'xss_clean|trim|callback__is_unique_category_name',
            ),
        array(
                'field' =>   'description',
                'label' =>  'Category Description',
                'rules' =>   'xss_clean|trim',
            ),
    );

    function _is_unique_category_name(){
        $id = $this->input->post('id');
        $group_id = $this->input->post('group_id');
        $slug = $this->input->post('slug');
        if($slug)
        {
            if($this->expense_categories_m->get_by_slug($slug,$id,$group_id))
            {
                $this->form_validation->set_message('_is_unique_category_name','Another Expense Category by the name '.$this->input->post('name').'`already exists');
                return FALSE;
            }
            else
            {
                return TRUE;
            }
            
        }
        
    }
   

    public function __construct()
    {
        parent::__construct();
        $this->load->model('expense_categories_m');
    }

    function get_group_expense_category_options(){
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
                    $expense_categories = $this->expense_categories_m->get_group_expense_category_options($this->group->id);
                    $expense_category_options = array();
                    foreach ($expense_categories as $id => $name) {
                        $expense_category_options[] = array(
                            'id' => $id,
                            'name' => $name
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'Expense Categories',
                        'expense_category_options' => $expense_category_options,
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

    function get_group_expense_categories(){
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
                    $total_rows = $this->expense_categories_m->count_group_expense_categories($this->group->id);
                    $pagination = create_custom_pagination('mobile',$total_rows,$records_per_page,$lower_limit,TRUE);
                    $categories = array();
                    $posts = $this->expense_categories_m->limit($pagination['limit'])->get_group_expense_categories($this->group->id);

                    foreach ($posts as $post) {
                        $categories[] = array(
                            'id' => $post->id,
                            'name' => $post->name,
                            'description' => $post->description,
                            'is_hidden' => $post->is_hidden?1:0,
                            'active' => $post->active?1:0,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'success',
                        'expense_categories' => $categories,
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
        $_POST['slug'] = generate_slug($this->input->post('name'));
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $this->form_validation->set_rules($this->validation_rules);
                    if($this->form_validation->run()){
                        $id = $this->expense_categories_m->insert(array(
                                'name'  =>  $this->input->post('name'),
                                'slug'  =>  $this->input->post('slug'),
                                'description'  =>  $this->input->post('description'),
                                'group_id'  =>  $this->group->id,
                                'active'    =>  1,
                                'created_by'    =>  $this->user->id,
                                'created_on'    =>  time(),
                            ));
                        if($id){
                            $response = array(
                                'status' => 1,
                                'message' => $this->input->post('name').' created',
                                'id' =>$id,
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Unable to create a new expense category',
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
                    $id = $this->input->post('id');
                    if($id){
                        $expense_category = $this->expense_categories_m->get_group_expense_category($id,$this->group->id);
                        if($expense_category){
                            $this->form_validation->set_rules($this->validation_rules);
                            if($this->form_validation->run()){
                                $update = $this->expense_categories_m->update($expense_category->id,array(
                                        'name'      =>  $this->input->post('name'),
                                        'slug'      =>  $this->input->post('slug'),
                                        'description'=> $this->input->post('description'),
                                        'modified_by'=> $this->user->id,
                                        'modified_on'=> time(),
                                    ));
                                if($update){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Expense category successfully updated',
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Error updating expense category',
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
                                'time' => time(),
                                'message' => 'Expense category not found',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'time' => time(),
                            'message' => 'Expense category parameter is missing',
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
        $_POST['slug'] = generate_slug($this->input->post('name'));
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $id = $this->input->post('id');
                    if($id){
                        $expense_category = $this->expense_categories_m->get_group_expense_category($id,$this->group->id);
                        if($expense_category){
                            if($this->transaction_statements_m->check_if_expense_category_has_transactions($expense_category->id,$this->group->id)){
                                $response = array(
                                        'status' => 0,
                                        'message' => 'Expense category has existing transactions and can not be deleted',
                                    );
                            }else{
                                if($this->expense_categories_m->safe_delete($expense_category->id,$this->group->id)){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Expense category successfully deleted',
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Unable to safely delete expense category',
                                    );
                                }
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Expense category is missing',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Expense category parameter is missing',
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
        $_POST['slug'] = generate_slug($this->input->post('name'));
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $id = $this->input->post('id');
                    if($id){
                        $expense_category = $this->expense_categories_m->get_group_expense_category($id,$this->group->id);
                        if($expense_category){
                            if($expense_category->is_hidden){
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Expense category already hidden',
                                );
                            }else{
                                if($this->expense_categories_m->update($expense_category->id,array(
                                        'is_hidden' => 1,
                                        'modified_by' => $this->user->id,
                                        'modified_on' => time(),
                                    ))){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Expense category successfully hidden'
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Unable to update expense category',
                                    );
                                }
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Expense category is missing',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Expense category parameter is missing',
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
        $_POST['slug'] = generate_slug($this->input->post('name'));
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $id = $this->input->post('id');
                    if($id){
                        $expense_category = $this->expense_categories_m->get_group_expense_category($id,$this->group->id);
                        if($expense_category){
                            if(!$expense_category->is_hidden){
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Expense category already visible',
                                );
                            }else{
                                if($this->expense_categories_m->update($expense_category->id,array(
                                        'is_hidden' => '',
                                        'modified_by' => $this->user->id,
                                        'modified_on' => time(),
                                    ))){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Expense category successfully unhidden'
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Unable to update expense category',
                                    );
                                }
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Expense category is missing',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Expense category parameter is missing',
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


    function manage(){
        $file = file_get_contents('php://input');
        $response = array();
        $request = array();
        header('Content-Type: application/json');
        $time = time();
        if($file){
            $result = json_decode($file);
            $request = $result;
            if($result){
                $action_type = isset($result->action_type)?$result->action_type:0;
                $user_id = isset($result->current_user_id)?$result->current_user_id:0;
                $group_id = isset($result->group_id)?$result->group_id:0;
                if($action_type&&$user_id&&$group_id){
                    $user = $this->ion_auth->get_user($user_id);
                    if($user){
                        $group = $this->groups_m->get($group_id);
                        if($group){
                            $name = isset($result->name)?trim($result->name):'';
                            $result = (array)$result+array('slug'=>generate_slug($name));
                            $this->group_id = $group->id;

                            foreach ($result as $result_key => $result_value) {
                                $_POST[$result_key] = $result_value;
                            }

                            if($action_type==1){//delete
                                $id = $this->input->post('expense_category_id');
                                if($id){
                                    $expense_category = $this->expense_categories_m->get($id);
                                    if($expense_category){
                                        if($this->transaction_statements_m->check_if_expense_category_has_transactions($expense_category->id,$group->id)){
                                            $response = array(
                                                    'status' => 0,
                                                    'time' => time(),
                                                    'message' => 'Expense category has existing transactions and can not be deleted',
                                                );
                                        }else{
                                            if($this->expense_categories_m->safe_delete($expense_category->id,$group->id)){
                                                $response = array(
                                                    'status' => 1,
                                                    'time' => time(),
                                                    'message' => '',
                                                    'success' => "Expense category successfully deleted"
                                                );
                                            }else{
                                                $response = array(
                                                    'status' => 0,
                                                    'time' => time(),
                                                    'message' => 'Unable to safely delete expense category',
                                                );
                                            }
                                        }
                                    }else{
                                        $response = array(
                                            'status' => 0,
                                            'time' => time(),
                                            'message' => 'Expense category is missing',
                                        );
                                    }
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'time' => time(),
                                        'message' => 'Expense category parameter is missing',
                                    );
                                }
                            }else if($action_type==2){//edit
                                $id = $this->input->post('expense_category_id');
                                if($id){
                                    $expense_category = $this->expense_categories_m->get($id);
                                    if($expense_category){
                                        $this->form_validation->set_rules($this->validation_rules);
                                        if($this->form_validation->run()){
                                            $update = $this->expense_categories_m->update($expense_category->id,array(
                                                    'name'      =>  $this->input->post('name'),
                                                    'slug'      =>  $this->input->post('slug'),
                                                    'description'=> $this->input->post('description'),
                                                    'modified_by'=> $user->id,
                                                    'modified_on'=> time(),
                                                ));
                                            if($update){
                                                $response = array(
                                                    'status' => 1,
                                                    'time' => time(),
                                                    'success' => 'Expense category successfully updated',
                                                );
                                            }else{
                                                $response = array(
                                                    'status' => 0,
                                                    'time' => time(),
                                                    'message' => 'Error updating expense category',
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
                                            'time' => time(),
                                            'message' => 'Expense category not found',
                                        );
                                    }
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'time' => time(),
                                        'message' => 'Expense category parameter is missing',
                                    );
                                }
                            }else if($action_type==3){//Create
                                $this->form_validation->set_rules($this->validation_rules);
                                if($this->form_validation->run()){
                                    $id = $this->expense_categories_m->insert(array(
                                            'name'  =>  $this->input->post('name'),
                                            'slug'  =>  $this->input->post('slug'),
                                            'description'  =>  $this->input->post('description'),
                                            'group_id'  =>  $group->id,
                                            'active'    =>  1,
                                            'created_by'    =>  $user->id,
                                            'created_on'    =>  time(),
                                        ));
                                    if($id){
                                        $response = array(
                                            'status' => 1,
                                            'time' => time(),
                                            'success' => $this->input->post('name').' created',
                                            'id' =>$id,
                                        );
                                    }else{
                                        $response = array(
                                            'status' => 0,
                                            'time' => time(),
                                            'message' => 'Unable to create a new expense category',
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
                            }else if($action_type==4){//open action

                            }else if($action_type==5){//Close action

                            }else if($action_type==6){//hide action
                                $id = $this->input->post('expense_category_id');
                                if($id){
                                    $expense_category = $this->expense_categories_m->get($id);
                                    if($expense_category){
                                        if($expense_category->is_hidden){
                                            $response = array(
                                                'status' => 0,
                                                'time' => time(),
                                                'message' => 'Expense category already hidden',
                                            );
                                        }else{
                                            if($this->expense_categories_m->update($expense_category->id,array(
                                                    'is_hidden' => 1,
                                                    'modified_by' => $user->id,
                                                    'modified_on' => time(),
                                                ))){
                                                $response = array(
                                                    'status' => 1,
                                                    'time' => time(),
                                                    'message' => '',
                                                    'success' => 'Expense category successfully hidden'
                                                );
                                            }else{
                                                $response = array(
                                                    'status' => 0,
                                                    'time' => time(),
                                                    'message' => 'Unable to update expense category',
                                                );
                                            }
                                        }
                                    }else{
                                        $response = array(
                                            'status' => 0,
                                            'time' => time(),
                                            'message' => 'Expense category is missing',
                                        );
                                    }
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'time' => time(),
                                        'message' => 'Expense category parameter is missing',
                                    );
                                }
                            }else if($action_type==7){//Unhide action
                                $id = $this->input->post('expense_category_id');
                                if($id){
                                    $expense_category = $this->expense_categories_m->get($id);
                                    if($expense_category){
                                        if(!$expense_category->is_hidden){
                                            $response = array(
                                                'status' => 0,
                                                'time' => time(),
                                                'message' => 'Expense category already visible',
                                            );
                                        }else{
                                            if($this->expense_categories_m->update($expense_category->id,array(
                                                    'is_hidden' => '',
                                                    'modified_by' => $user->id,
                                                    'modified_on' => time(),
                                                ))){
                                                $response = array(
                                                    'status' => 1,
                                                    'time' => time(),
                                                    'message' => '',
                                                    'success' => 'Expense category successfully unhidden'
                                                );
                                            }else{
                                                $response = array(
                                                    'status' => 0,
                                                    'time' => time(),
                                                    'message' => 'Unable to update expense category',
                                                );
                                            }
                                        }
                                    }else{
                                        $response = array(
                                            'status' => 0,
                                            'time' => time(),
                                            'message' => 'Expense category is missing',
                                        );
                                    }
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'time' => time(),
                                        'message' => 'Expense category parameter is missing',
                                    );
                                }
                            }else{
                                $response = array(
                                        'status' => 0,
                                        'time' => time(),
                                        'message' => 'Action type supplied is invalid',
                                    );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'time' => time(),
                                'message' => 'Group details unavailable',
                            );
                        }
                    }else{
                        $response = array(
                                'status' => 0,
                                'time' => time(),
                                'message' => 'User details unavailable',
                            );
                    }
                }else{
                    $response = array(
                            'status' => 0,
                            'time' => time(),
                            'message' => 'Essential parameters missing',
                        );
                }
            }else{
                $response = array(
                        'status' => 0,
                        'time' => time(),
                        'message' => 'File sent has the wrong format',
                    );
            }
        }else{
            $response = array(
                    'status' => 0,
                    'time' => time(),
                    'message' => 'No file sent',
                );
        }

        echo encrypt_json_encode(array('response'=>$response,'request'=>$request));
    }
}
?>