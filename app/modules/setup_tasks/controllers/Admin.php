<?php if(!defined('BASEPATH'))  exit('You are not allowed to view this script');
Class Admin extends Admin_Controller{
    
    protected $rules = array(
        array(
                'field' => 'name',
                'label' => 'Title',
                'rules' =>  'trim|required'
            ),
        array(
                'field' => 'slug',
                'label' => 'Title',
                'rules' =>  'trim|required|callback__is_unique'
            ),
        array(
                'field' => 'description',
                'label' => 'Description',
                'rules' => 'trim|required'
            ),
        array(
                'field' => 'call_to_action_name',
                'label' => 'Call To Action Name',
                'rules' =>  'trim|required'
            ),
        array(
                'field' => 'call_to_action_link',
                'label' => 'Call To Action Link',
                'rules' =>  'trim|required'
            ),
        array(
                'field' => 'icon',
                'label' => 'Icon ',
                'rules' => 'trim|required'
            ),
        array(
                'field' => 'parent_id',
                'label' => 'Parent Setup Task ',
                'rules' => 'trim|numeric'
            ),
    );

    function __construct(){
        parent::__construct();
        $this->load->model('setup_tasks_m');
    }

    function index(){
        
    }

    function _is_unique(){
        $slug = $this->input->post('slug');
        $id = $this->input->post('id');
        $setup_task = $this->setup_tasks_m->get_by_slug($slug);
        if($setup_task){
            if($id==$setup_task->id){
                return TRUE;
            }else{    
                $this->form_validation->set_message('_is_unique','Setup task already exists');
                return FALSE;
            }
        }else{
            return TRUE;
        }
    }

    function create(){
        $post = new StdClass();
        $this->form_validation->set_rules($this->rules);
        if($this->form_validation->run()){
            $data = array(
                'name' => $this->input->post('name'),
                'slug' => $this->input->post('slug'),
                'parent_id' => $this->input->post('parent_id')?:0,
                'description' => $this->input->post('description'),
                'call_to_action_name' => $this->input->post('call_to_action_name'),
                'call_to_action_link' => $this->input->post('call_to_action_link'),
                'icon' => $this->input->post('icon'),
                'active' => 1,
                'created_by' => $this->user->id,
                'created_on' => time(),
            );
            $id = $this->setup_tasks_m->insert($data);
            if($id){
                $this->session->set_flashdata('success','Setup tasks added successfully');
                if($this->input->post('new_item')){
                    redirect('admin/setup_tasks/create');
                }else{
                    redirect('admin/setup_tasks/edit/'.$id);
                }
            }else{
                $this->session->set_flashdata('error','There was an error adding the setup task');
            }
            redirect('admin/setup_tasks/listing');
        }else{
            foreach ($this->rules as $key => $field){
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $data['id'] = '';
        $data['post'] = $post;
        $data['setup_task_options'] = $this->setup_tasks_m->get_options();
        $this->template->title('Create Setup Task')->build('admin/form',$data);
    }

    function edit($id=0){
        $id OR redirect('admin/setup_tasks/listing');
        $post = new stdClass();
        $post = $this->setup_tasks_m->get($id);
        if(empty($post)){
            $this->session->set_flashdata('error','Sorry the setu task does not exist');
            redirect('admin/setup_tasks/listing','refresh');
        }
        $this->form_validation->set_rules($this->rules);
        if($this->form_validation->run()){
            $input = array(
                'name' => $this->input->post('name'),
                'slug' => $this->input->post('slug'),
                'description' => $this->input->post('description'),
                'call_to_action_name' => $this->input->post('call_to_action_name'),
                'call_to_action_link' => $this->input->post('call_to_action_link'),
                'icon' => $this->input->post('icon'),
                'active' => 1,
                'modified_by' => $this->user->id,
                'modified_on' => time(),
            );
            $result = $this->setup_tasks_m->update($id,$input);
            if($result){
                $this->session->set_flashdata('success','Changes successfully saved');
                if($this->input->post('new_item')){
                    redirect('admin/setup_tasks/create');
                }else{
                    redirect('admin/setup_tasks/listing');
                }
            }else{
                $this->session->set_flashdata('error','There was an error saving changes ');
            }
            redirect('admin/setup_tasks/listing');
        }else{
            foreach (array_keys($this->rules) as $field){
                if(isset($_POST[$field])){
                    $post->$field = $this->form_validation->$field;
                }
            }
        }
        $data['id'] = $id;
        $data['post'] = $post;
        $data['setup_task_options'] = $this->setup_tasks_m->get_options();
        $this->template->title('Edit Setup Task')->build('admin/form',$data);
    }


    function listing(){
        $data = array();
        $posts = $this->setup_tasks_m->get_all();
        $data['posts'] = $posts;
        $this->template->title('List Setup Tasks')->build('admin/listing',$data);
    }

    function activate($id=0){
        $id OR redirect('admin/setup_tasks/listing');
        $post=$this->setup_tasks_m->get($id);
        if(empty($post)){
            $this->session->set_flashdata('error','Sorry the setup task does not exist');
            redirect('admin/setup_tasks/listing','refresh');
        }

        if($post->active){
            $this->session->set_flashdata('error','Sorry the setup task is already active');
            redirect('admin/setup_tasks/listing','refresh');
        }

        $result = $this->setup_tasks_m->update($post->id,array(
            'active' => 1,
            'modified_by' => $this->user->id,
            'modified_on' => time(),
        ));

        if($result){
            $this->session->set_flashdata('success','Setup task activated');
        }else{
            $this->session->set_flashdata('error','Unable to activate setup task ');
        }
        redirect('admin/setup_tasks/listing','refresh');
    }


    function hide($id=0){
        $id OR redirect('admin/setup_tasks/listing');
        $post = $this->setup_tasks_m->get($id);
        if(empty($post)){
            $this->session->set_flashdata('error','Sorry the setup task does not exist');
            redirect('admin/setup_tasks/listing','refresh');
        }

        if($post->active != 1){
            $this->session->set_flashdata('error','Sorry the setup task is already hidden');
            redirect('admin/setup_tasks/listing','refresh');
        }

        $result = $this->setup_tasks_m->update($id,array(
            'active' => 0,
            'modified_by' => $this->user->id,
            'modified_on' => time(),
        ));

        if($result){
            $this->session->set_flashdata('success','Setup task hidden successfully');
        }else{
            $this->session->set_flashdata('error','Unable to hide setup task ');
        }
        redirect('admin/setup_tasks/listing','refresh');
    }


    function delete($id = 0,$redirect = TRUE){
        $id OR redirect('admin/setup_tasks/listing');
        $post=$this->setup_tasks_m->get($id);
        if(empty($post)){
            $this->session->set_flashdata('error','Sorry the list is not available');
            if($redirect){
                redirect('admin/setup_tasks/listing','refresh');
            }
            return FALSE;
        }

        $delete = $this->setup_tasks_m->safe_delete($id);
        if($delete){
            $result = $this->setup_tasks_m->delete_setup_task_trackers_by_slug($post->slug);
            if($result){
                $this->session->set_flashdata('info','Group Setup Task Trackers Deleted');
            }
            $this->session->set_flashdata('success','Setup task successfully deleted');
        }else{
            $this->session->set_flashdata('error','Setup task could not be deleted ');
        }
        if($redirect){
            redirect('admin/setup_tasks/listing','refresh');
        }else{
            return TRUE;
        }
    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_delete'){
            for($i=0;$i<count($action_to);$i++)
            {
                $this->delete($action_to[$i],FALSE);
            }
        }
        redirect('admin/setup_tasks/listing');
    }

    function sort(){
        $data['posts'] = $this->setup_tasks_m->get_parent_setup_tasks();
        $this->template->title('Sort Group Setup Tasks')->build('admin/sort',$data);
    }

    function ajax_sort_update()
    {
        $data = json_decode($this->input->post('json'));
        for($i=0;$i<count($data);$i++){
            $this->setup_tasks_m->update($data[$i]->id,array(
                'position'=>$i,
                'modified_on' => time(),
                'modified_by' => $this->ion_auth->get_user()->id,
            ));
            $this->_children($data[$i],0,$i);
        }
    }

    private function _children($pt,$parent_id,$position){
        echo "Dashboard I:".$pt->id."P:".$parent_id."||";
        $this->setup_tasks_m->update($pt->id,array(
            'position'=>$position,
            'parent_id'=>$parent_id,
            'modified_on' => time(),
            'modified_by' => $this->ion_auth->get_user()->id,
        ));
        $k=0;
        if(isset($pt->children)){
            foreach($pt->children as $child){
                $k++;
                $this->_children($child,$pt->id,$k);
            }
        }

    }

}
?>