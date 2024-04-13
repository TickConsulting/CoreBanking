<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller
{
    protected $data = array();

	protected $validation_rules = array(
		array(
            'field' => 'parent_id',
            'label' => 'Parent Menu',
            'rules' => 'trim',
        ),array(
            'field' => 'name',
            'label' => 'Menu Name',
            'rules' => 'trim|required',
        ),array(
            'field' => 'url',
            'label' => 'Menu URL',
            'rules' => 'trim',
        ),array(
            'field' => 'icon',
            'label' => 'Menu Icon',
            'rules' => 'trim|required',
        )
    );

	function __construct()
    {
        parent::__construct();
        $this->load->model('quick_action_menus_m');
    }

    function create(){
        $post = new stdClass();      
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $input = array(
                'parent_id'=>$this->input->post('parent_id')?$this->input->post('parent_id'):0,
                'name'=>$this->input->post('name'),
                'url'=>$this->input->post('url'),
                'icon'=>$this->input->post('icon'),
                'created_by'=>$this->ion_auth->get_user()->id,
                'created_on'=>time(),
                'active'=>1,
            );
            $id = $this->quick_action_menus_m->insert($input);
            if($id){
                $this->session->set_flashdata('success','User Quick Action Menu Item Created Successfully.');
                if($this->input->post('new_item'))
                {
                    redirect('admin/quick_action_menus/create');

                }
                redirect('admin/quick_action_menus/edit/'.$id);
            }else{
                $this->session->set_flashdata('error','User Quick Action Menu Item could not be Created.');
                redirect('admin/quick_action_menus/create');
            }
        }else{
            foreach ($this->validation_rules as $key => $field)
            {
                $post->$field['field'] = set_value($field['field']);
            }
        }
        $this->data['side_bar_menu_options'] = $this->quick_action_menus_m->get_options();
        $this->data['post'] = $post;
        $this->template->title('Create User Quick Action Menu Item')->build('admin/form', $this->data);
    }

    function edit($id = 0){
        $id OR redirect('admin/quick_action_menus');
        $this->form_validation->set_rules($this->validation_rules);  
        $post = $this->quick_action_menus_m->get($id);
        if(empty($post))
        {
            $this->session->set_flashdata('error','Sorry, the User Quick Actions Menu does not exist');
            redirect('admin/quick_action_menus/listing','refresh');
        }
        if($this->form_validation->run()){
            $input = array(
                'parent_id'=>$this->input->post('parent_id')?$this->input->post('parent_id'):0,
                'name'=>$this->input->post('name'),
                'url'=>$this->input->post('url'),
                'icon'=>$this->input->post('icon'),
                'modified_by'=>$this->ion_auth->get_user()->id,
                'modified_on'=>time(),
            );
            $result = $this->quick_action_menus_m->update($id,$input);
            if($id){
                $this->session->set_flashdata('success','User Quick Action Menu Item Changes Saved Successfully.');
                if($this->input->post('new_item'))
                {
                    redirect('admin/quick_action_menus/create');

                }
            }else{
                $this->session->set_flashdata('error','User Quick Action Menu Item Changes could not be Saved.');
            }
            redirect('admin/quick_action_menus/listing');
        }else{
            foreach ($this->validation_rules as $key => $field)
            {
                $field = $field['field'];
                if (isset($_POST[$field]))
                {
                    $post->$field = $_POST[$field];
                }
            }
        }
        $this->data['side_bar_menu_options'] = $this->quick_action_menus_m->get_options($this->input->post('position')?$this->input->post('position'):$post->position);
        $this->data['post'] = $post;
        $this->template->title('Edit User Quick Action Menu Item')->build('admin/form', $this->data);
    }

    function index(){
        redirect('admin/quick_action_menus/listing','refresh');
        $this->template->title('Quick Action Menus Dashboard')->build('admin/index', $this->data);
    }

    function listing(){
        $this->data['posts'] = $this->quick_action_menus_m->get_parent_links();
        $this->data['side_bar_menu_options'] = $this->quick_action_menus_m->get_options();
        $this->template->title('User Quick Action Menus')->build('admin/listing', $this->data);
    }

    public function hide($id = 0){
        $id OR redirect('admin/quick_action_menus/listing');

        $post = $this->quick_action_menus_m->get($id);
        if(empty($post))
        {
            $this->session->set_flashdata('error','Sorry, the User Quick Actions Menu does not exist');
            redirect('admin/quick_action_menus/listing','refresh');
        }

        if($post->active == 0)
        {
            $this->session->set_flashdata('error','Sorry, the User Quick Action Menu is already hidden');
            redirect('admin/quick_action_menus/listing','refresh');
        }

        $result = $this->quick_action_menus_m->update($id,array(
                    'active'        =>  0,
                    'modified_by'   =>  $this->ion_auth->get_user()->id,
                    'modified_on'   =>  time(),
                ));
        if($result){
            $this->session->set_flashdata('success','User Quick Action Menu Hidden');
        }else{
            $this->session->set_flashdata('error','User Quick Action Menu could not be Hidden');
        }
        redirect('admin/quick_action_menus/listing');
    }

    public function activate($id = 0){
        $id OR redirect('admin/quick_action_menus/listing');

        $post = $this->quick_action_menus_m->get($id);
        if(empty($post))
        {
            $this->session->set_flashdata('error','Sorry, the User Quick Actions Menu does not exist');
            redirect('admin/quick_action_menus/listing','refresh');
        }

        if($post->active == 1)
        {
            $this->session->set_flashdata('error','Sorry, the User Quick Action Menu is already active');
            redirect('admin/quick_action_menus/listing','refresh');
        }


        $result = $this->quick_action_menus_m->update($id,array(
                    'active'     =>  1,
                    'modified_by'=> $this->ion_auth->get_user()->id,
                    'modified_on'=> time()));
        if($result){
            $this->session->set_flashdata('success','User Quick Action Menu Activated');
        }else{
            $this->session->set_flashdata('error','User Quick Action Menu could not be Activated');
        }
        redirect('admin/quick_action_menus/listing');
    }

    public function delete($id = 0){
         // Delete one
        $ids = ($id) ? array($id) : $this->input->post('action_to');
        // Go through the array of slugs to delete
        if (!empty($ids))
        {
            $post_titles = array();
            foreach ($ids as $id)
            {
                // Get the current page so we can grab the id too
                if ($post = $this->quick_action_menus_m->get($id))
                {
                    $this->quick_action_menus_m->delete($id);
                    // Wipe cache for this model, the content has changed
                    $post_titles[] = $post->name;
                }
            }
        }
        // Some pages have been deleted

        if (!empty($post_titles))
        {
            // Only deleting one page
            if (count($post_titles) == 1)
            {
                $this->session->set_flashdata('success', $post_titles[0].' User Quick Action Menus Deleted ');
            }
            // Deleting multiple pages
            else
            {
                $this->session->set_flashdata('success', implode('", "', $post_titles).' User Quick Action Menus Deleted ');
            }
        }
        // For some reason, none of them were deleted
        else
        {
            $this->session->set_flashdata('error', 'Items: Quick Action Menus  Deleted');
        }
        redirect('admin/quick_action_menus/listing');
    }

    public function action()
    {   
        switch ($this->input->post('btnAction'))
        { 
            case 'bulk_delete':
                $this->delete();
                break;
            default:
                redirect('admin/quick_action_menus/listing');
                break;
        }
    }

    public function sort(){
        $data = array();
        $data['posts'] = $this->quick_action_menus_m->get_parent_links();
        $this->template->title('Sort User Quick Action Menus')->build('admin/sort', $data);
    }

    public function ajax_sort_update(){
        $data = json_decode($this->input->post('json'));
        for($i=0;$i<count($data);$i++){
            $this->quick_action_menus_m->update($data[$i]->id,array(
                'position'=>$i,
                'modified_on' => time(),
                'modified_by' => $this->ion_auth->get_user()->id,
            ));
            $this->_children($data[$i],0,$i);
        }
    }

    private function _children($pt,$parent_id,$position){
        echo "Quick Action I:".$pt->id."P:".$parent_id."||";
        $this->quick_action_menus_m->update($pt->id,array(
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