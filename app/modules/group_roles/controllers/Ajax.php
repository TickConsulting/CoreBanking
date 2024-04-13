<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{

    protected $validation_rules = array(
        array(
            'field' => 'name',
            'label' => 'Group Role Name',
            'rules' => 'xss_clean|trim|required|callback__is_unique_group_name',
        ),array(
            'field' => 'description',
            'label' => 'Group Role Description',
            'rules' => 'xss_clean|trim',
        ),
    );

    function __construct(){
        parent::__construct();
        $this->load->model('group_roles_m');
        $this->load->model('members/members_m');
    }

    function _is_unique_group_name(){
        $name = $this->input->post('name');
        if($name){
            if($this->group_roles_m->is_name_unique($name,$this->group->id)){
                return TRUE;
            }else{
                $this->form_validation->set_message('_is_unique_group_name','Group role exists. Use a different unique name');
                return FALSE;
            }
        }
    }

    function create(){
    	$response = array();
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
                $response = array(
                    'status' => 1,
                    'message' => 'Group role successfully created',
                    'id' => $id,
                    'name' => $this->input->post('name'),
                    'refer'=>site_url('group/group_roles'),
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Group role could not be created',
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
        echo json_encode($response);die;
    }

    function edit(){
        $response = array();
        if($this->group_roles_m->get($this->input->post('id'))){       
            $this->form_validation->set_rules($this->validation_rules);
            if($this->input->post('name')){
                $id = $this->input->post('id');
                $input = array(
                    'name'=>$this->input->post('name'),
                    'description'=>$this->input->post('description'),
                    'group_id'=>$this->group->id,
                    'is_editable'=>1,
                    'active'=>1,
                    'modified_on'=>time(),
                    'modified_by'=>$this->user->id
                );
                $result= $this->group_roles_m->update($id , $input);
                if($result){
                    $response = array(
                        'status' => 1,
                        'message' => 'Group role successfully updated',
                        'id' => $id,
                        'name' => $this->input->post('name'),
                        'refer'=>site_url('group/group_roles'),
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Group role could not be updated',
                    );
                }

            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Group role name required',
                );   
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'The group role doesn not exist' ,
                'validation_errors' => '',
            );
        }
        echo json_encode($response);
    }
}