<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{

    protected $data = array();
	
	function __construct(){
        parent::__construct();
        $this->load->model('permissions_m');
        $this->load->model('group_roles/group_roles_m');
        $this->load->model('menus/menus_m');
    }


    function index(){
        $group_roles = $this->group_roles_m->get_all_active_group_roles();
        $this->data['group_roles'] = $group_roles;
        //$this->data['parent_menus'] = $this->menus_m->get_active_parent_links();
        $this->data['parent_menus'] = $this->menus_m->get_parent_links();
        if($this->input->post('submit')){
            if($this->permissions_m->delete_group_permissions()){
                $permissions = $_POST;
                foreach ($permissions as $menu_id => $roles){
                    if(is_array($roles)):
                    foreach ($roles as $role_id) {
                        $input_data = array(
                            'group_id'  =>  $this->group->id,
                            'menu_id'   =>  $menu_id,
                            'role_id'   =>  $role_id,
                            'created_by'=>  $this->user->id,
                            'created_on'=>  time(),
                            'active'    =>  1,
                        );
                        $this->permissions_m->insert($input_data);
                        unset($input_data);
                    }
                    endif;
                }
                $this->session->set_flashdata('success','Successfully added group permissions');
                redirect('group/permissions','redirect');
            }else{
                $this->session->set_flashdata('error','There was a problem adding group permissions');
            }
        }
        $this->data['posts'] = $this->permissions_m->get_group_permissions_array();
        $this->template->set_layout('default_full_width.html')->title('Group Member Permissions')->build('group/index',$this->data);
    }

}