<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/*** @package  	chamasoft-Version-3.0
 * @subpackage  Categories
 * @category  	Module
 */
class Group extends Group_Controller{
/* * The id of post
* @access protected
* @var int
* data container
*/
     protected $data=array();

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
            array(
                    'field' =>   'is_an_administrative_expense_category',
                    'label' =>  'Is an administrative expense category',
                    'rules' =>   'xss_clean|trim',
                ),
        );

    function _is_unique_category_name(){
        $id = $this->input->post('id');
        $group_id = $this->group->id;
        $slug = $this->input->post('slug');
        if($slug)
        {
            if($this->expense_categories_m->get_by_slug($slug,$id,$group_id))
            {
                $this->form_validation->set_message('_is_unique_category_name','Another Expense Category by the name <strong>`'.$this->input->post('name').'`</strong> already exists');
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

    /*
     * Show all created posts
     * @access public
     * @return void
     */
    // function index(){
    //     $data = array();
    //     $this->template->title('Expense Categories')->build('group/index',$data);
    // }


    public function index()
    {
        $total_rows = $this->expense_categories_m->count_group_expense_categories();
        $pagination = create_pagination('group/expense_categories/listing/pages', $total_rows,50,5,TRUE);
        $this->data['pagination'] = $pagination;
        $this->data['posts'] = $this->expense_categories_m->limit($pagination['limit'])->get_group_expense_categories();
        $this->template->title(translate('List Group Expense Categories'))->build('group/listing',$this->data);
    }

    public function create()
    {
        $post = new stdClass();
        $this->form_validation->set_rules($this->validation_rules);

        if($this->form_validation->run())
        {
            $id = $this->expense_categories_m->insert(array(
                    'name'  =>  $this->input->post('name'),
                    'slug'  =>  $this->input->post('slug'),
                    'is_an_administrative_expense_category'  =>  $this->input->post('is_an_administrative_expense_category')?1:0,
                    'description'  =>  $this->input->post('description'),
                    'group_id'  =>  $this->group->id,
                    'active'    =>  1,
                    'created_by'    =>  $this->user->id,
                    'created_on'    =>  time(),
                ));

            if($id)
            {
                $this->session->set_flashdata('success',$this->input->post('name').' as an expense category successfully created');
                if($this->input->post('new_item'))
                {
                    redirect('group/expense_categories/create');
                }
                else
                {
                    redirect('group/expense_categories');
                }

            }
            else
            {
                $this->session->set_flashdata('error','Unable to create a new Expense Category');
                redirect('group/expense_categories/create');
            }
        }
        else
        {
            foreach ($this->validation_rules as $key => $field) 
            {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }

        $this->data['post'] = $post;
        $this->data['id'] = '';

        $this->template->title('Create Expense Category')->build('group/form',$this->data);
    }

    public function ajax_create(){
        $response =array();        
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $name= $this->input->post('name');
            $expense_category = array(
                'name'  =>  $this->input->post('name'),
                'slug'  =>  $this->input->post('slug'),
                'description'  =>  $this->input->post('description'),
                'is_an_administrative_expense_category'  =>  $this->input->post('is_an_administrative_expense_category')?1:0,
                'group_id'  =>  $this->group->id,
                'active'    =>  1,
                'created_by'    =>  $this->user->id,
                'created_on'    =>  time(),
            );
            $id = $this->expense_categories_m->insert($expense_category);
            if($id){
                $expense_category['id'] = $id;
                $response = array(
                    'status' => 1,
                    'id'=>$id,
                    'name'=>$name,
                    'expense_category'=>$expense_category,
                    'message' => 'Expense category created successfully.',
                    'validation_errors' =>'',
                    'refer'=>site_url('group/expense_categories')
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not add expense category.',
                    'validation_errors' =>'',
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

    function ajax_edit(){
        $response =array();
        $id = $this->input->post('id');
        if($id){
            $post = $this->expense_categories_m->get($id);
            if($post){
                $this->form_validation->set_rules($this->validation_rules);
                if($this->form_validation->run()){
                    $name= $this->input->post('name');
                    $update = $this->expense_categories_m->update($post->id,array(
                        'name'      =>  $this->input->post('name'),
                        'slug'      =>  $this->input->post('slug'),
                        'description'=> $this->input->post('description'),
                        'is_an_administrative_expense_category'  =>  $this->input->post('is_an_administrative_expense_category')?1:0,
                        'modified_by'=> $this->user->id,
                        'modified_on'=> time(),
                    ));
                    if($update){
                        $response = array(
                            'status' => 1,
                            'message' => $this->input->post('name').' successfully updated',
                            'refer'=>site_url('group/expense_categories')
                        ); 
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Unable to update Expense Category',
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
                        'message' => 'There are some errors on the form. Please review and try again.',
                        'validation_errors' => $post,
                    );
                }
            }else{
               $response = array(
                    'status' => 0,
                    'message' => 'Expense category details missing.',
                ); 
           }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Expense category id is missing in JSON Payload.',
            );
        }
        echo json_encode($response);
    }



    public function edit($id=0)
    {
        $id OR redirect('group/expense_categories');

        $post = new StdClass();

        $post = $this->expense_categories_m->get($id);

        if(!$post)
        {
            $this->session->set_flashdata('info','Sorry the expense category does not exist');
            redirect('group/expense_categories');
        }
        else
        {
            $this->form_validation->set_rules($this->validation_rules);
            if($this->form_validation->run())
            {
                $update = $this->expense_categories_m->update($post->id,array(
                        'name'      =>  $this->input->post('name'),
                        'slug'      =>  $this->input->post('slug'),
                        'description'=> $this->input->post('description'),
                        'is_an_administrative_expense_category'  =>  $this->input->post('is_an_administrative_expense_category')?1:0,
                        'modified_by'=> $this->user->id,
                        'modified_on'=> time(),
                    ));

                if($update)
                {
                    $this->session->set_flashdata('success',$this->input->post('name').' successfully updated');
                    if($this->input->post('new_item'))
                    {
                        redirect('group/expense_categories/create');
                    }
                    else
                    {
                        redirect('group/expense_categories');
                    }
                }
                else
                {
                    $this->session->set_flashdata('error','Unable to update Expense Category');
                    redirect('group/expense_categories');
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
            $this->template->title('Edit Group Expense Category')->build('group/form',$this->data);
        }
    }


    public function action()
    {   
        switch ($this->input->post('btnAction'))
        { 
            case 'publish':
                $this->publish();
                break;
            case 'bulk_delete':
                $this->delete();
                break;
            default:
                redirect('admin/email_templates');
                break;
        }
    }


    public function delete($id = 0)
    {
        // Delete one
        $ids = ($id) ? array($id) : $this->input->post('action_to');
        // Go through the array of slugs to delete
        if (!empty($ids))
        {
            $post_titles = array();
            foreach ($ids as $id)
            {
                // Get the current page so we can grab the id too
                if ($post = $this->email_templates_m->get($id))
                {
                    $this->email_templates_m->delete($id);
                    // Wipe cache for this model, the content has changed
                    $post_titles[] = $post->title;
                }
            }
        }
        // Some pages have been deleted

        if (!empty($post_titles))
        {
            // Only deleting one page
            if (count($post_titles) == 1)
            {
                $this->session->set_flashdata('success', sprintf(' Email Templates Deleted', $post_titles[0]));
            }
            // Deleting multiple pages
            else
            {
                $this->session->set_flashdata('success', sprintf(' Email Templates Deleted', implode('", "', $post_titles)));
            }
        }
        // For some reason, none of them were deleted
        else
        {
            $this->session->set_flashdata('info', 'Items: Email Templates Deleted');
        }
        redirect('admin/email_templates');
    }

    function hide($id=0,$redirect=TRUE)
    {
        $id OR redirect('group/expense_categories');

        $post = $this->expense_categories_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the Expense Category does not exist');
            redirect('group/expense_categories');
            return FALSE; 
        }

        if($post->is_hidden)
        {
            $this->session->set_flashdata('Error','Sorry, the Expense Category is already hidden');
            redirect('group/expense_categories');
            return FALSE; 
        }

        $res = $this->expense_categories_m->update($post->id,array('is_hidden'=>1,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Expense category successfully hidden');
            if($redirect)
            {
                redirect('group/expense_categories');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to hide the Expense Category');
            if($redirect)
            {
                redirect('group/expense_categories');
            }
        }
    }

     function unhide($id=0,$redirect=TRUE)
    {
        $id OR redirect('group/expense_categories');

        $post = $this->expense_categories_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the Expense Category does not exist');
            redirect('group/expense_categories');
            return FALSE; 
        }

        if(!$post->is_hidden)
        {
            $this->session->set_flashdata('Error','Sorry, the Expense Category is not hidden');
            redirect('group/expense_categories');
            return FALSE; 
        }

        $res = $this->expense_categories_m->update($post->id,array('is_hidden'=>NULL,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Expense category successfully unhidden');
            if($redirect)
            {
                redirect('group/expense_categories');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to unhide the Expense Category');
            if($redirect)
            {
                redirect('group/expense_categories');
            }
        }
    }

    function safe_delete(){
        $this->expense_categories_m->safe_delete(2339);
    }

}

