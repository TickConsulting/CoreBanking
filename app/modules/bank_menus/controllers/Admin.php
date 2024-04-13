<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller
{
    protected $enable_menu_options = array(
        'disable_member_directory' => 'Groups Disable Member Directory',
        'online_banking_enabled' => 'Online Banking',
        'is_sacco' => 'Enable only for saccos',
        'subscription_status_5' => 'Group Subscription Expired', 
        'group_offer_loans' => 'Group Offer Loans',
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
                    'field' =>  'description',
                    'label' =>  'Menu Description',
                    'rules' =>  'trim',
                ),
             // array(
             //        'field' =>  'language_key',
             //        'label' =>  'Language Key',
             //        'rules' =>  'trim',
             //    ),
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
        $this->load->model('bank_menus_m');
        $this->data['enable_menu_options'] = $this->enable_menu_options;
    }

    function index()
    {
        $this->template->title('User Sidebar Menus')->build('admin/index');
    }

    function delete($id = 0,$redirect= TRUE)
    {
        $id OR redirect('admin/bank_menus/listing');

        $post = $this->bank_menus_m->get($id);

        if(!$post)
        {
            $this->session->set_flashdata('error','Sorry, the Admin Menu does not exist');
            redirect('admin/bank_menus/listing');
        }

        $id = $this->bank_menus_m->delete($post->id);

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
            redirect('admin/bank_menus/listing');
        }
        return TRUE;
    }

    function hide($id=0,$redirect=TRUE)
    {
        $id OR redirect('admin/bank_menus/listing');

        $post = $this->bank_menus_m->get($id);

        if(!$post)
        {
            $this->session->set_flashdata('error','Sorry, the Admin Menu does not exist');
            redirect('admin/bank_menus/listing');
        }
        if($post->active=='')
        {
            $this->session->set_flashdata('error','Sorry, the Admin Menu is already hidden');
            redirect('admin/bank_menus/listing');
        }

        $id = $this->bank_menus_m->update($post->id,array
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
            redirect('admin/bank_menus/listing');
        }
        return TRUE;
    }

    function activate($id=0,$redirect=TRUE)
    {
        $id OR redirect('admin/bank_menus/listing');

        $post = $this->bank_menus_m->get($id);

        if(!$post)
        {
            $this->session->set_flashdata('error','Sorry, the Admin Menu does not exist');
            redirect('admin/bank_menus/listing');
        }
        if($post->active)
        {
            $this->session->set_flashdata('error','Sorry, the Admin Menu is already activated');
            redirect('admin/bank_menus/listing');
        }

        $id = $this->bank_menus_m->update($post->id,array
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
            redirect('admin/bank_menus/listing');
        }
        return TRUE;
    }

    function listing()
    {
        $this->data['posts'] = $this->bank_menus_m->get_parent_links();
        $this->data['side_bar_menu_options'] = $this->bank_menus_m->get_options();
        $this->template->title('User Sidebar Menus')->build('admin/listing',$this->data);
    }

    function create()
    {
        $post = new StdClass();
        $this->form_validation->set_rules($this->rules);
        
        if($this->form_validation->run())
        {
             $data = array(
                'name'=>$this->input->post('name'),
                'description'=>$this->input->post('description'),
                // 'slug'=>str_replace(' ','-',strtolower(trim($this->input->post('name')))),
                'url'=>$this->input->post('url'),
                'icon'=>$this->input->post('icon'),
                'color'=>$this->input->post('color'),
                'size'=>$this->input->post('size'),
                'created_by'=>$this->ion_auth->get_user()->id,
                'created_on'=>time(),
                'active'=>1,
                'parent_id'=>$this->input->post('parent_id')?$this->input->post('parent_id'):0,
            );

            $id = $this->bank_menus_m->insert($data);
            if($id)
            {
                $this->session->set_flashdata('success','User Sidebar Menu Item Created Successfully.');
                if($this->input->post('new_item'))
                {
                    redirect('admin/bank_menus/create','refresh');
                }
                else
                {
                    redirect('admin/bank_menus/edit/'.$id);
                }
                
            }
            else{
                $this->session->set_flashdata('error','Menu Item could not be Created.');
                redirect('admin/bank_menus/create');
            }
        }
        else
        {
            foreach ($this->rules as $key => $field) {
                $field_value = $field['field'];
                 $post->$field_value = set_value($field['field']);
            }
        }

        $this->data['menus'] = $this->bank_menus_m->get_options();
        $this->data['post'] = $post;
        $this->template->title('Create User Sidebar Menus Form')->build('admin/form',$this->data);
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

        redirect('admin/bank_menus/listing');
    }

    function sort()
    {
        $this->data['posts'] = $this->bank_menus_m->get_parent_links();
        $this->template->title('Sort User Menus')->build('admin/sort', $this->data);
    }

    function edit($id=0)
    {
        $id OR redirect('admin/bank_menus/listing');

        $post = new StdClass();

        $post = $this->bank_menus_m->get($id);
        if(!$post)
        {
            $this->session->set_flashdata('error','Sorry, the User Sidebar Menu does not exist');
            redirect('admin/bank_menus/listing');
        }

        $this->form_validation->set_rules($this->rules);

        if($this->form_validation->run())
        {
             $data = array(
                'name'=>$this->input->post('name'),
                'description'=>$this->input->post('description'),
                // 'slug'=>str_replace(' ','-',strtolower(trim($this->input->post('name')))),
                'url'=>$this->input->post('url'),
                'icon'=>$this->input->post('icon'),
                'color'=>$this->input->post('color'),
                'size'=>$this->input->post('size'),
                'created_by'=>$this->ion_auth->get_user()->id,
                'created_on'=>time(),
                'active'=>1,
                'parent_id'=>$this->input->post('parent_id')?$this->input->post('parent_id'):0,
            );
            $update = $this->bank_menus_m->update($id,$data);
            if($update)
            {
                $this->session->set_flashdata('success',$this->input->post('name').' successfully updated');
                if($this->input->post('new_item'))
                {
                    redirect('admin/bank_menus/create','refresh');
                }
                else
                {
                    redirect('admin/bank_menus/listing','refresh');
                }
            }
            else
            {
                $this->session->set_flashdata('error','Unable to update');
                redirect('admin/bank_menus/listing','refresh');
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

        

        $this->data['menus'] = $this->bank_menus_m->get_options();
        $this->data['post'] = $post;

        $this->template->title('Edit User Menu')->build('admin/form',$this->data);
    }


    function ajax_sort_update()
    {
        $data = json_decode($this->input->post('json'));
        for($i=0;$i<count($data);$i++){
            $this->bank_menus_m->update($data[$i]->id,array(
                'position'=>$i,
                'modified_on' => time(),
                'modified_by' => $this->ion_auth->get_user()->id,
            ));
            $this->_children($data[$i],0,$i);
        }
    }

    private function _children($pt,$parent_id,$position){
        echo "Dashboard I:".$pt->id."P:".$parent_id."||";
        $this->bank_menus_m->update($pt->id,array(
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