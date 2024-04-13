<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Group extends Group_Controller{

     protected $data=array();

     protected $validation_rules=array(
            array(
                    'field' =>  'name',
                    'label' =>  'Recipient Name',
                    'rules' =>  'trim|required',
                ),
            array(
                    'field' =>   'phone_number',
                    'label' =>   'Recipient Phone Number',
                    'rules' =>   'trim|required|valid_phone',
                ),
            array(
                    'field' =>  'description',
                    'label' =>  'Recipient Description',
                    'rules' =>  'trim',
                ),
        );

    public function __construct(){
        parent::__construct();
        $this->load->model('recipients_m');
    }

    function index(){
        $data = array();
        $this->template->title('Recipients')->build('group/index',$data);
    }

    public function listing(){
        $total_rows = $this->recipients_m->count_group_recipients();
        $pagination = create_pagination('group/recipients/listing/pages', $total_rows,50,5,TRUE);
        $this->data['pagination'] = $pagination;
        $this->data['posts'] = $this->recipients_m->limit($pagination['limit'])->get_group_recipients();
        $this->template->title('List Recipients')->build('group/listing',$this->data);
    }

    public function create(){
        $post = new stdClass();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $id = $this->recipients_m->insert(
                array(
                    'name'  =>  $this->input->post('name'),
                    'phone_number'  =>  $this->input->post('phone_number'),
                    'description'  =>  $this->input->post('description'),
                    'group_id'  =>  $this->group->id,
                    'active'    =>  1,
                    'created_by'    =>  $this->user->id,
                    'created_on'    =>  time(),
                )
            );
            if($id){
                $this->session->set_flashdata('success',$this->input->post('name').' as a recipient successfully created');
                if($this->input->post('new_item')){
                    redirect('group/recipients/create');
                }else{
                    redirect('group/recipients/listing');
                }
            }else{
                $this->session->set_flashdata('error','Unable to create a new Recipient');
                redirect('group/recipients/create');
            }
        }else{
            foreach($this->validation_rules as $key => $field){
                $post->$field['field'] = set_value($field['field']);
            }
        }
        $this->data['post'] = $post;
        $this->data['id'] = '';
        $this->template->title('Create Recipient')->build('group/form',$this->data);
    }

    public function ajax_create(){
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $id = $this->recipients_m->insert(array(
                'name'  =>  $this->input->post('name'),
                'phone_number'  =>  $this->input->post('phone_number'),
                'description'  =>  $this->input->post('description'),
                'group_id'  =>  $this->group->id,
                'modified_by'=> $this->user->id,
                'modified_on'=> time(),
                )
            );
            if($id){
                if($recipient = $this->recipients_m->get_group_recipient($id)){
                    echo json_encode($recipient);
                }else{
                    echo 'Could not add find any recipient';
                }
            }else{
                echo 'Could not add recipient';
            }
        }else{
            echo validation_errors();
        }
    }

    public function edit($id=0){
        $id OR redirect('group/recipients/listing');
        $post = new stdClass();
        $post = $this->recipients_m->get($id);
        if(!$post){
            $this->session->set_flashdata('info','Sorry the expense category does not exist');
            redirect('group/recipients/listing');
        }else{
            $this->form_validation->set_rules($this->validation_rules);
            if($this->form_validation->run()){
                $result = $this->recipients_m->update($post->id,array(
                    'name'  =>  $this->input->post('name'),
                    'email'  =>  $this->input->post('email'),
                    'phone'  =>  $this->input->post('phone'),
                    'description'  =>  $this->input->post('description'),
                    'group_id'  =>  $this->group->id,
                    'modified_by'=> $this->user->id,
                    'modified_on'=> time(),
                ));
                if($result){
                    $this->session->set_flashdata('success',$this->input->post('name').' successfully updated');
                    if($this->input->post('new_item')){
                        redirect('group/recipients/create');
                    }else{
                        redirect('group/recipients/listing');
                    }
                }else{
                    $this->session->set_flashdata('error','Unable to update Recipient');
                    redirect('group/recipients/listing');
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
            $this->template->title('Edit Group Recipient')->build('group/form',$this->data);
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
                redirect('group/recipients/listing');
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
        redirect('admin/email_templates/listing');
    }

    function hide($id=0,$redirect=TRUE){
        $id OR redirect('group/recipients/listing');

        $post = $this->recipients_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the Recipient does not exist');
            redirect('group/recipients/listing');
            return FALSE; 
        }

        if($post->is_hidden)
        {
            $this->session->set_flashdata('Error','Sorry, the Recipient is already hidden');
            redirect('group/recipients/listing');
            return FALSE; 
        }

        $res = $this->recipients_m->update($post->id,array('is_hidden'=>1,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Expense category successfully hidden');
            if($redirect)
            {
                redirect('group/recipients/listing');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to hide the Recipient');
            if($redirect)
            {
                redirect('group/recipients/listing');
            }
        }
    }

    function unhide($id=0,$redirect=TRUE){
        $id OR redirect('group/recipients/listing');
        $post = $this->recipients_m->get($id);
        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the Recipient does not exist');
            redirect('group/recipients/listing');
            return FALSE; 
        }

        if(!$post->is_hidden)
        {
            $this->session->set_flashdata('Error','Sorry, the Recipient is not hidden');
            redirect('group/recipients/listing');
            return FALSE; 
        }

        $res = $this->recipients_m->update($post->id,array('is_hidden'=>NULL,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res)
        {
            $this->session->set_flashdata('success','Recipient successfully unhidden');
            if($redirect)
            {
                redirect('group/recipients/listing');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to unhide the Recipient');
            if($redirect)
            {
                redirect('group/recipients/listing');
            }
        }
    }

}

