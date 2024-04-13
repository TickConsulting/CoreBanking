<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Group extends Group_Controller{

    protected $data=array();

    protected $validation_rules=array(
        array(
                'field' =>   'name',
                'label' =>   'Category Name',
                'rules' =>   'trim|required',
            ),
        array(
                'field' =>   'slug',
                'label' =>   'Category Name Slug',
                'rules' =>   'trim|callback__is_unique_category_name',
            ),
        array(
                'field' =>  'amount',
                'label' =>  'Category Fines amount',
                'rules' =>  'trim|currency',
            ),
    );

    function _is_unique_category_name(){
        $id = $this->input->post('id');
        $group_id = $this->group->id;
        $name = $this->input->post('name');
        $slug = preg_replace('/\s*/', '', $name);
        if($slug){
            if($this->fine_categories_m->get_by_slug($slug,$id,$group_id))
            {
                $this->form_validation->set_message('_is_unique_category_name','Another Fine Category by the name <strong>`'.$this->input->post('name').'`</strong> already exists');
                return FALSE;
            }
            else
            {
                return TRUE;
            }
            
        }        
     }
   

    public function __construct(){
        parent::__construct();
        $this->load->model('fine_categories_m');
    }

    /*
     * Show all created posts
     * @access public
     * @return void
     */
    // function index(){
    //     $data = array();
    //     $this->template->title('Fine Categories')->build('group/index',$data);
    // }


    public function index(){
        $total_rows = $this->fine_categories_m->count_all_group_expense_categories();
        $pagination = create_pagination('group/expense_categories/listing/pages', $total_rows,50,5,TRUE);
        $this->data['pagination'] = $pagination;
        $this->data['posts'] = $this->fine_categories_m->limit($pagination['limit'])->get_all();
        $this->template->title(translate('List Group Fines Categories'))->build('group/listing',$this->data);
    }

    

    public function create(){
        $post = new stdClass();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $id = $this->fine_categories_m->insert(array(
                'name'  =>  $this->input->post('name'),
                'slug'  =>  $this->input->post('slug'),
                'group_id'  =>  $this->group->id,
                'active'    =>  1,
                'created_by'    =>  $this->user->id,
                'created_on'    =>  time(),
                )
            );

            if($id){
                $this->session->set_flashdata('success',$this->input->post('name').' as a fine category was successfully created');
                if($this->input->post('new_item')){
                    redirect('group/fine_categories/create');
                }else{
                    redirect('group/fine_categories/listing');
                }
            }else{
                $this->session->set_flashdata('error','Unable to create a new Fine Category');
                redirect('group/fine_categories/create');
            }
        }else{
            foreach ($this->validation_rules as $key => $field) {
                $file_value = $field['field'];
                $post->$file_value= set_value($field['field']);
            }
        }
        $this->data['post'] = $post;
        $this->data['id'] = '';
        $this->template->title('Create Fine Category')->build('group/form',$this->data);
    }
    
    public function ajax_create(){
        $response = array();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $id = $this->fine_categories_m->insert(array(
                'name'  =>  $this->input->post('name'),
                'slug'  =>  $this->input->post('slug'),
                'amount'  =>  $this->input->post('amount'),
                'group_id'  =>  $this->group->id,
                'active'    =>  1,
                'created_by'    =>  $this->user->id,
                'created_on'    =>  time(),
                )
            );
            if($id){
                if($fine_category = $this->fine_categories_m->get_group_fine_category($id)){
                    $fine_category->currency = $this->group_currency;
                    $fine_category->amount = number_to_currency($fine_category->amount);
                    $response = array(
                        'status' => 1,
                        'message' => 'Fine Category successfully created',
                        'data'=>$fine_category,
                        'refer'=>site_url('group/fine_category/listing')
                    );                    
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not add find any fine categories',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not add fine category',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => validation_errors(),
                'validation_errors'=> validation_errors()
            );
        }
        echo json_encode($response);
    }



    public function edit($id=0){
        $id OR redirect('group/fine_categories/listing');

        $post = new StdClass();

        $post = $this->fine_categories_m->get($id);

        if(!$post)
        {
            $this->session->set_flashdata('info','Sorry the fine category does not exist');
            redirect('group/fine_categories/listing');
        }
        else
        {
            $this->form_validation->set_rules($this->validation_rules);
            if($this->form_validation->run())
            {
                $update = $this->fine_categories_m->update($post->id,array(
                        'name'      =>  $this->input->post('name'),
                        'slug'      =>  $this->input->post('slug'),
                        'modified_by'=> $this->user->id,
                        'modified_on'=> time(),
                    ));

                if($update)
                {
                    $this->session->set_flashdata('success',$this->input->post('name').' successfully updated');
                    if($this->input->post('new_item'))
                    {
                        redirect('group/fine_categories/create');
                    }
                    else
                    {
                        redirect('group/fine_categories/listing');
                    }
                }
                else
                {
                    $this->session->set_flashdata('error','Unable to update Fine Category');
                    redirect('group/fine_categories/listing');
                }
            }
            else
            {
                 foreach (array_keys($this->validation_rules) as $field)
                {
                    if (isset($_POST[$field]))
                    {
                        $post->$field = $this->form_validation->$field;
                    }
                }
            }
            $this->data['post'] = $post;
            $this->data['id'] = $id;
            $this->template->title('Edit Group Fine Category')->build('group/form',$this->data);
        }
    }


    public function action(){   
        switch ($this->input->post('btnAction'))
        { 
            case 'publish':
                $this->publish();
                break;
            case 'bulk_delete':
                $this->delete();
                break;
            default:
                redirect('admin/fine_categories/listing');
                break;
        }
    }


    public function delete($id = 0){
        
    }

    function hide($id=0,$redirect=TRUE)
    {
        $id OR redirect('group/fine_categories/listing');

        $post = $this->fine_categories_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the Fine Category does not exist');
            redirect('group/fine_categories/listing');
            return FALSE; 
        }

        if($post->is_hidden)
        {
            $this->session->set_flashdata('Error','Sorry, the Fine Category is already hidden');
            redirect('group/fine_categories/listing');
            return FALSE; 
        }

        $res = $this->fine_categories_m->update($post->id,array('is_hidden'=>1,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Fine category successfully hidden');
            if($redirect)
            {
                redirect('group/fine_categories/listing');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to hide the Fine Category');
            if($redirect)
            {
                redirect('group/fine_categories/listing');
            }
        }
    }

     function unhide($id=0,$redirect=TRUE)
    {
        $id OR redirect('group/fine_categories/listing');

        $post = $this->fine_categories_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the Fine Category does not exist');
            redirect('group/fine_categories/listing');
            return FALSE; 
        }

        if(!$post->is_hidden)
        {
            $this->session->set_flashdata('Error','Sorry, the Fine Category is not hidden');
            redirect('group/fine_categories/listing');
            return FALSE; 
        }

        $res = $this->fine_categories_m->update($post->id,array('is_hidden'=>NULL,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Fine category successfully unhidden');
            if($redirect)
            {
                redirect('group/fine_categories/listing');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to unhide the Fine Category');
            if($redirect)
            {
                redirect('group/fine_categories/listing');
            }
        }
    }

    function ajax_get_fine_category($id = 0){
        $id = $this->input->post('id');
        if($fine_category = $this->fine_categories_m->get($id)){
            echo json_encode($fine_category);
        }else{
            echo "error";
        }
    }

    function display_fine_categories(){
        $posts = $this->fine_categories_m->get_all();
        print_r($posts);
    }

    function listing(){
        redirect('group/fine_categories');
    }
}

