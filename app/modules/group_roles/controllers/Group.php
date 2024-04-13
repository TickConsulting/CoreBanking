<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{

    protected $validation_rules = array(
        array(
            'field' => 'name',
            'label' => 'Group Role Name',
            'rules' => 'trim|required',
        ),array(
            'field' => 'description',
            'label' => 'Group Role Description',
            'rules' => 'trim',
        ),
    );

    function __construct(){
        parent::__construct();
        $this->load->model('group_roles_m');
        $this->load->model('members/members_m');
    }

    // function index(){
    //     $data = array();
    //     $this->template->title('Group Roles')->build('group/index',$data);
    // }

    function create(){
    	$data = array();
        $post = new stdClass();      
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $input = array(
                'name'=>$this->input->post('name'),
                'description'=>$this->input->post('description'),
                'group_id'=>$this->group->id,
                'is_editable'=>1,
                'active'=>1,
                'created_on'=>time(),
                'created_by'=>$this->user->id
            );
            $id = $this->group_roles_m->insert($input);
            if($id){
                $this->session->set_flashdata('success','Group role created successfully');
            }else{
                $this->session->set_flashdata('error','Group role could not be created');
            }
            if($this->input->post('new_item')){
                redirect('group/group_roles/create','refresh');
            }else{
                redirect('group/group_roles/listing');
            }
        }else{
        	foreach ($this->validation_rules as $key => $field){
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $data['id'] = '';
        $data['post'] = $post;
        $this->template->title('Create Group Role')->build('group/form',$data);
    }

    function ajax_create(){
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $input = array(
                'name'=>$this->input->post('name'),
                'description'=>$this->input->post('description'),
                'group_id'=>$this->group->id,
                'is_editable'=>1,
                'active'=>1,
                'created_on'=>time(),
                'created_by'=>$this->user->id
            );
            $id = $this->group_roles_m->insert($input);
            if($group_role = $this->group_roles_m->get_group_role($id)){
                echo json_encode($group_role);
            }else{
                echo "Could not find group role";
            }
        }else{
            echo validation_errors();
        }
    }

    public function index(){
        $data = array();    
        $total_rows = $this->group_roles_m->count_all_group_roles();
        $pagination = create_pagination('group/group_roles/listing/pages', $total_rows,50,5,TRUE);
        $data['pagination'] = $pagination;
        $data['posts'] = $this->group_roles_m->limit($pagination['limit'])->get_all_group_roles();
        $data['member_group_role_ids'] = $this->members_m->get_member_group_role_ids();
        $this->template->title(translate('List Group Roles'))->build('group/listing',$data);
    }

    function edit($id = 0){
        $id OR redirect('group/group_roles/listing');
        $data = array();
        $post = new stdClass();  
        $post = $this->group_roles_m->get_group_role($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry the group role does not exist');
            redirect('group/group_roles/listing');
        } 
        if(!$post->is_editable){
            $this->session->set_flashdata('error','Sorry the group role is not editable');
            redirect('group/group_roles/listing');
        }

        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $input = array(
                'name'=>$this->input->post('name'),
                'description'=>$this->input->post('description'),
                'group_id'=>$this->group->id,
                'is_editable'=>1,
                'modified_on'=>time(),
                'modified_by'=>$this->user->id
            );
            $result = $this->group_roles_m->update($id,$input);
            if($result){
                $this->session->set_flashdata('success','Group roles changes saved successfully');
            }else{
                $this->session->set_flashdata('error','Group roles changes could not be saved');
            }
            if($this->input->post('new_item')){
                redirect('group/group_roles/create','refresh');
            }else{
                redirect('group/group_roles/listing');
            }
        }else{

        }
        $data['id'] = $id;
        $data['post'] = $post;
        $this->template->title('Edit Group Role')->build('group/form',$data);
    }

    function hide($id = 0){
        $id OR redirect('group/group_roles/listing');
        $data = array();
        $post = new stdClass();  
        $post = $this->group_roles_m->get_group_role($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry the group role does not exist');
            redirect('group/group_roles/listing');
        }  
        if(!$post->is_editable){
            $this->session->set_flashdata('error','Sorry the group role is not editable');
            redirect('group/group_roles/listing');
        }

        if($post->active){
            $input = array(
                'active'=>0,
                'modified_by'=>$this->user->id,
                'modified_on'=>time()
            );
            if($result = $this->group_roles_m->update($id,$input)){
                $this->session->set_flashdata('success','Group role hidden successfully');
            }else{
                $this->session->set_flashdata('error','Sorry the group role could not be hidden');
            }
        }else{
            $this->session->set_flashdata('info','Sorry the group role is already hidden');
        }
        redirect('group/group_roles/listing');
    }

    function unhide($id = 0){
        $id OR redirect('group/group_roles/listing');
        $data = array();
        $post = new stdClass();  
        $post = $this->group_roles_m->get_group_role($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry the group role does not exist');
            redirect('group/group_roles/listing');
        } 
        if(!$post->is_editable){
            $this->session->set_flashdata('error','Sorry the group role is not editable');
            redirect('group/group_roles/listing');
        }

        if(!$post->active){
            $input = array(
                'active'=>1,
                'modified_by'=>$this->user->id,
                'modified_on'=>time()
            );
            if($result = $this->group_roles_m->update($id,$input)){
                $this->session->set_flashdata('success','Group role unhidden successfully');
            }else{
                $this->session->set_flashdata('error','Sorry the group role could not be unhidden');
            }
        }else{
            $this->session->set_flashdata('info','Sorry the group role is already unhidden');
        }
        redirect('group/group_roles/listing');
    }
}