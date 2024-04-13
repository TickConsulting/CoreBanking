<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller
{

    protected $enable_menu_options = array(
        'disable_member_directory' => 'Groups Disable Member Directory',
        'online_banking_enabled' => 'Online Banking',
        'is_sacco' => 'Enable only for saccos',
        'allow_members_request_loan' => 'Group Allows Member Request Loan'
    );

    protected $rules=array(
            array(
                    'field' =>  'name',
                    'label' =>  'Menu Name',
                    'rules' =>  'trim|required',
                ),
             array(
                    'field' =>  'url',
                    'label' =>  'Menu URL',
                    'rules' =>  'trim|required',
                ),
            array(
                    'field' =>  'help_url',
                    'label' =>  'Menu URL',
                    'rules' =>  'trim',
                ),             
              array(
                    'field' =>  'icon',
                    'label' =>  'Menu Icon',
                    'rules' =>  'trim|required',
                ),
               array(
                    'field' =>  'parent_id',
                    'label' =>  'Parent Menu',
                    'rules' =>  'trim',
                ),
               array(
                    'field' =>  'enable_menu_for',
                    'label' =>  'Enable Menu For',
                    'rules' =>  'trim',
                ),
                array(
                    'field' =>  'enabled_or_disabled',
                    'label' =>  'Feature enabled or disabled',
                    'rules' =>  'trim|numeric',
                ),
                array(
                    'field' =>  'enabled_disabled_feature',
                    'label' =>  'Feature enabled or disabled',
                    'rules' =>  'trim',
                ), 
        );
    protected $data=array();

	function __construct()
    {
        parent::__construct();
        $this->load->model('member_menus_m');
    }

    function index()
    {
        $this->template->title('User Sidebar Member_menus')->build('admin/index');
    }

    function delete($id = 0,$redirect= TRUE)
    {
        $id OR redirect('admin/member_menus/listing');

        $post = $this->member_menus_m->get($id);

        if(!$post)
        {
            $this->session->set_flashdata('error','Sorry, the Admin Menu does not exist');
            redirect('admin/member_menus/listing');
        }

        $id = $this->member_menus_m->delete($post->id);

        if($id)
        {
            $this->session->set_flashdata('success',$post->name.' was successfully deleted');
        }
        else
        {
            $this->session->set_flashdata('error','Unable to delete '.$post->name.' Admin menu');
        }
        if($redirect)
        {
            redirect('admin/member_menus/listing');
        }
        return TRUE;
    }

    function hide($id=0,$redirect=TRUE){
        $id OR redirect('admin/member_menus/listing');

        $post = $this->member_menus_m->get($id);

        if(!$post)
        {
            $this->session->set_flashdata('error','Sorry, the Admin Menu does not exist');
            redirect('admin/member_menus/listing');
        }
        if($post->active=='')
        {
            $this->session->set_flashdata('error','Sorry, the Admin Menu is already hidden');
            redirect('admin/member_menus/listing');
        }

        $id = $this->member_menus_m->update($post->id,array
            (
                'active'=>NULL,
                'modified_on' => time(),
                'modified_by' => $this->ion_auth->get_user()->id,
            ));

        if($id)
        {
            $this->session->set_flashdata('success',$post->name.' was successfully hidden');
        }
        else
        {
            $this->session->set_flashdata('error','Unable to hide '.$post->name.' Admin menu');
        }
        if($redirect)
        {
            redirect('admin/member_menus/listing');
        }
        return TRUE;
    }

    function activate($id=0,$redirect=TRUE)
    {
        $id OR redirect('admin/member_menus/listing');

        $post = $this->member_menus_m->get($id);

        if(!$post)
        {
            $this->session->set_flashdata('error','Sorry, the Admin Menu does not exist');
            redirect('admin/member_menus/listing');
        }
        if($post->active)
        {
            $this->session->set_flashdata('error','Sorry, the Admin Menu is already activated');
            redirect('admin/member_menus/listing');
        }

        $id = $this->member_menus_m->update($post->id,array
            (
                'active'=>1,
                'modified_on' => time(),
                'modified_by' => $this->ion_auth->get_user()->id,
            ));

        if($id)
        {
            $this->session->set_flashdata('success',$post->name.' was successfully activated');
        }
        else
        {
            $this->session->set_flashdata('error','Unable to hide '.$post->name.' Admin menu');
        }
        if($redirect)
        {
            redirect('admin/member_menus/listing');
        }
        return TRUE;
    }

    function listing()
    {
        $this->data['posts'] = $this->member_menus_m->get_parent_links();
        $this->data['side_bar_menu_options'] = $this->member_menus_m->get_options();
        $this->template->title('User Sidebar Member_menus')->build('admin/listing',$this->data);
    }

    function create(){
        $post = new StdClass();
        $this->form_validation->set_rules($this->rules);
        
        if($this->form_validation->run())
        {
             $data = array(
                'parent_id'=>$this->input->post('parent_id')?$this->input->post('parent_id'):0,
                'name'=>$this->input->post('name'),
                'url'=>$this->input->post('url'),
                'help_url'=>$this->input->post('help_url'),
                'icon'=>$this->input->post('icon'),
                'color'=>$this->input->post('color'),
                'contextual_help_content'=>$this->input->post('contextual_help_content'),
                'enable_menu_for'=>$this->input->post('enable_menu_for'),
                'enabled_or_disabled'=>$this->input->post('enabled_or_disabled'),
                'enabled_disabled_feature'=>$this->input->post('enabled_disabled_feature'),
                'size'=>$this->input->post('size'),
                'created_by'=>$this->ion_auth->get_user()->id,
                'created_on'=>time(),
                'active'=>1,
            );
            $id = $this->member_menus_m->insert($data);
            if($id)
            {
                $this->session->set_flashdata('success','User Sidebar Menu Item Created Successfully.');
                if($this->input->post('new_item'))
                {
                    redirect('admin/member_menus/create','refresh');
                }
                else
                {
                    redirect('admin/member_menus/edit/'.$id);
                }
                
            }
            else{
                $this->session->set_flashdata('error','Menu Item could not be Created.');
                redirect('admin/member_menus/create');
            }
        }
        else
        {
            foreach ($this->rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $this->data['enable_menu_options'] = $this->enable_menu_options;
        $this->data['member_menus'] = $this->member_menus_m->get_options();
        $this->data['post'] = $post;
        $this->template->title('Create User Sidebar Member_menus Form')->build('admin/form',$this->data);
    }

    function action()
    {
        $action_to = $this->input->post('action_to');

        $action = $this->input->post('btnAction');

        if($action == 'bulk_delete')
        {
            for($i=0;$i<count($action_to);$i++)
            {
                $this->delete($action_to[$i],FALSE);
            }
        }

        redirect('admin/member_menus/listing');
    }

    function sort()
    {
        $this->data['posts'] = $this->member_menus_m->get_parent_links();
        $this->template->title('Sort User Member_menus')->build('admin/sort', $this->data);
    }

    function edit($id=0)
    {
        $id OR redirect('admin/member_menus/listing');

        $post = new StdClass();

        $post = $this->member_menus_m->get($id);
        if(!$post)
        {
            $this->session->set_flashdata('error','Sorry, the User Sidebar Menu does not exist');
            redirect('admin/member_menus/listing');
        }

        $this->form_validation->set_rules($this->rules);

        if($this->form_validation->run())
        {
            $data = array(
                'parent_id'=>$this->input->post('parent_id')?$this->input->post('parent_id'):0,
                'name'=>$this->input->post('name'),
                'url'=>$this->input->post('url'),
                'help_url'=>$this->input->post('help_url'),
                'contextual_help_content'=>$this->input->post('contextual_help_content'),
                'enable_menu_for'=>$this->input->post('enable_menu_for'),
                'enabled_or_disabled'=>$this->input->post('enabled_or_disabled'),
                'enabled_disabled_feature'=>$this->input->post('enabled_disabled_feature'),
                'icon'=>$this->input->post('icon'),
                'color'=>$this->input->post('color'),
                'size'=>$this->input->post('size'),
                'modified_by'=>$this->ion_auth->get_user()->id,
                'modified_on'=>time(),
            );
            $update = $this->member_menus_m->update($id,$data);
            if($update)
            {
                $this->session->set_flashdata('success',$this->input->post('name').' successfully updated');
                if($this->input->post('new_item'))
                {
                    redirect('admin/member_menus/create','refresh');
                }
                else
                {
                    redirect('admin/member_menus/listing','refresh');
                }
            }
            else
            {
                $this->session->set_flashdata('error','Unable to update');
                redirect('admin/member_menus/listing','refresh');
            }
        }
        else
        {
            foreach (array_keys($this->rules) as $field){
                 if (isset($_POST[$field])){
                    $post->$field = $this->form_validation->$field;
                }
            }
        }
        $this->data['member_menus'] = $this->member_menus_m->get_options();
        $this->data['post'] = $post;
        $this->data['enable_menu_options'] = $this->enable_menu_options;
        $this->template->title('Edit User Menu')->build('admin/form',$this->data);
    }


    function ajax_sort_update()
    {
        $data = json_decode($this->input->post('json'));
        for($i=0;$i<count($data);$i++){
            $this->member_menus_m->update($data[$i]->id,array(
                'position'=>$i,
                'modified_on' => time(),
                'modified_by' => $this->ion_auth->get_user()->id,
            ));
            $this->_children($data[$i],0,$i);
        }
    }

    private function _children($pt,$parent_id,$position){
        echo "Dashboard I:".$pt->id."P:".$parent_id."||";
        $this->member_menus_m->update($pt->id,array(
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