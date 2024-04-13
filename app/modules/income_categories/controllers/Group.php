<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Group extends Group_Controller{

     protected $data=array();

     protected $validation_rules=array(
            array(
                    'field' =>  'name',
                    'label' =>  'Income Category Name',
                    'rules' =>  'xss_clean|trim|required',
                ),
            array(
                    'field' =>   'slug',
                    'label' =>   'Income Category Slug',
                    'rules' =>   'xss_clean|trim|required|callback__is_unique_income_category_name',
                ),
            array(
                    'field' =>  'description',
                    'label' =>  'Income Category Description',
                    'rules' =>  'xss_clean|trim',
                ),
        );

    public function __construct(){
        parent::__construct();
        $this->load->model('income_categories_m');
    }

    // function index(){
    //     $data = array();
    //     $this->template->title('Income Categories')->build('group/index',$data);
    // }

    public function index(){
        $total_rows = $this->income_categories_m->count_all_group_income_categories();
        $pagination = create_pagination('group/income_categories/listing/pages', $total_rows,50,5,TRUE);
        $this->data['pagination'] = $pagination;
        $this->data['posts'] = $this->income_categories_m->limit($pagination['limit'])->get_all_group_income_categories();
        $this->template->title(translate('List Income Categories'))->build('group/listing',$this->data);
    }

    public function create(){
        $post = new stdClass();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $id = $this->income_categories_m->insert(
                array(
                    'slug'  =>  $this->input->post('slug'),
                    'name'  =>  $this->input->post('name'),
                    'description'  =>  $this->input->post('description'),
                    'group_id'  =>  $this->group->id,
                    'active'    =>  1,
                    'created_by'    =>  $this->user->id,
                    'created_on'    =>  time(),
                )
            );
            if($id){
                $this->session->set_flashdata('success',$this->input->post('name').' as an income category successfully created');
                if($this->input->post('new_item')){
                    redirect('group/income_categories/create');
                }else{
                    redirect('group/income_categories/listing');
                }
            }else{
                $this->session->set_flashdata('error','Unable to create a new income category');
                redirect('group/income_categories/create');
            }
        }else{
            foreach($this->validation_rules as $key => $field){
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $this->data['post'] = $post;
        $this->data['id'] = '';
        $this->template->title('Create Income Category')->build('group/form',$this->data);
    }

    public function ajax_create(){
        $response = array();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $income_category = array(
                'slug'  =>  $this->input->post('slug'),
                'name'  =>  $this->input->post('name'),
                'description'  =>  $this->input->post('description'),
                'group_id'  =>  $this->group->id,
                'active'    =>  1,
                'created_by'    =>  $this->user->id,
                'created_on'    =>  time(),
            );
            $id = $this->income_categories_m->insert($income_category);
            if($id){
                $income_category['id'] = $id;
                $response = array(
                    'status' => 1,
                    'income_category' => $income_category,
                    'message' => 'successfully added income category',
                    'validation_errors' => '',
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not add income category',
                    'validation_errors' => '',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'There are some errors on the form. Please review and try again.',
                'validation_errors' => $this->form_validation->error_array(),
            );
        }
        echo json_encode($response);
    }

    public function edit($id=0){
        $id OR redirect('group/income_categories/listing');
        $post = new stdClass();
        $post = $this->income_categories_m->get($id);
        if(!$post){
            $this->session->set_flashdata('info','Sorry the income category does not exist');
            redirect('group/income_categories/listing');
        }else{
            $this->form_validation->set_rules($this->validation_rules);
            if($this->form_validation->run()){
                $result = $this->income_categories_m->update($post->id,array(
                    'name'  =>  $this->input->post('name'),
                    'slug'  =>  $this->input->post('slug'),
                    'description'  =>  $this->input->post('description'),
                    'group_id'  =>  $this->group->id,
                    'modified_by'=> $this->user->id,
                    'modified_on'=> time(),
                ));
                if($result){
                    $this->session->set_flashdata('success',$this->input->post('name').' successfully updated');
                    if($this->input->post('new_item')){
                        redirect('group/income_categories/create');
                    }else{
                        redirect('group/income_categories/listing');
                    }
                }else{
                    $this->session->set_flashdata('error','Unable to update income category');
                    redirect('group/income_categories/listing');
                }
            }else{
                foreach(array_keys($this->validation_rules) as $field){
                    if (isset($_POST[$field])){
                        $post->$field = $this->form_validation->$field;
                    }
                }
            }
            $this->data['post'] = $post;
            $this->data['id'] = $id;
            $this->template->title('Edit Income Category')->build('group/form',$this->data);
        }
    }

    public function action(){   
        switch ($this->input->post('btnAction'))
        { 
            case 'bulk_hide':
                $this->hide();
                break;
            case 'bulk_unhide':
                $this->hide();
                break;
            default:
                redirect('group/income_categories/listing');
                break;
        }
    }

    function hide($id=0,$redirect=TRUE){
        $id OR redirect('group/income_categories/listing');

        $post = $this->income_categories_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the Income category does not exist');
            redirect('group/income_categories/listing');
            return FALSE; 
        }

        if($post->is_hidden)
        {
            $this->session->set_flashdata('Error','Sorry, the Income category is already hidden');
            redirect('group/income_categories/listing');
            return FALSE; 
        }

        $res = $this->income_categories_m->update($post->id,array('is_hidden'=>1,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Income category successfully hidden');
            if($redirect)
            {
                redirect('group/income_categories/listing');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to hide the Income category');
            if($redirect)
            {
                redirect('group/income_categories/listing');
            }
        }
    }

    function unhide($id=0,$redirect=TRUE){
        $id OR redirect('group/income_categories/listing');
        $post = $this->income_categories_m->get($id);
        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the Income category does not exist');
            redirect('group/income_categories/listing');
            return FALSE; 
        }

        if(!$post->is_hidden)
        {
            $this->session->set_flashdata('Error','Sorry, the Income category is not hidden');
            redirect('group/income_categories/listing');
            return FALSE; 
        }

        $res = $this->income_categories_m->update($post->id,array('is_hidden'=>NULL,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Income category successfully unhidden');
            if($redirect)
            {
                redirect('group/income_categories/listing');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to unhide the Income category');
            if($redirect)
            {
                redirect('group/income_categories/listing');
            }
        }
    }

    function _is_unique_income_category_name(){
        $id = $this->input->post('id');
        $group_id = $this->group->id;
        $slug = $this->input->post('slug');
        if(!$id){
            if($slug){
                if($this->income_categories_m->get_by_slug($slug,$id,$group_id))
                {
                    $this->form_validation->set_message('_is_unique_income_category_name','Another Income Category by the name <strong>`'.$this->input->post('name').'`</strong> already exists');
                    return FALSE;
                }
                else
                {
                    return TRUE;
                }            
            }
        }
        
    }

}

