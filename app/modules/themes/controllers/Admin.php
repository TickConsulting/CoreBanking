<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller{

	protected $validation_rules=array(
        array(
                'field' =>  'name',
                'label' =>  'Theme Name',
                'rules' =>  'trim|required|callback_name_is_unique',
            ),
        array(
                'field' =>  'slug',
                'label' =>  'Theme Slug',
                'rules' =>  'trim|required',
            ),
        array(
                'field' =>  'logo',
                'label' =>  'Theme Logo',
                'rules' =>  '',
            ),
        array(
                'field' =>  'primary_background_color',
                'label' =>  'Primary Background Color',
                'rules' =>  '',
            ),
        array(
                'field' =>  'secondary_background_color',
                'label' =>  'Secondary Background Color',
                'rules' =>  '',
            ),
        array(
                'field' =>  'tertiary_background_color',
                'label' =>  'Tertiary Background Color',
                'rules' =>  '',
            ),
        array(
                'field' =>  'quaternary_background_color',
                'label' =>  'Quaternary Background Color',
                'rules' =>  '',
            ),
        array(
                'field' =>  'primary_text_color',
                'label' =>  'Primary Text Color',
                'rules' =>  '',
            ),
        array(
                'field' =>  'secondary_text_color',
                'label' =>  'Secondary Text Color',
                'rules' =>  '',
            ),
        array(
                'field' =>  'tertiary_text_color',
                'label' =>  'Tertiary Text Color',
                'rules' =>  '',
            ),
        array(
                'field' =>  'primary_border_color',
                'label' =>  'Primary Border Color',
                'rules' =>  '',
            ),
        array(
                'field' =>  'secondary_border_color',
                'label' =>  'Secondary Border Color',
                'rules' =>  '',
            ),
        array(
                'field' =>  'tertiary_border_color',
                'label' =>  'Tertiary Border Color',
                'rules' =>  '',
            )
    );

	function __construct(){
        parent::__construct();
        $this->load->model('themes_m');
        $this->load->library('files_uploader');
    }

    public function create(){
        $data = array();
        $post = new stdClass();      
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            if(!empty($_FILES['logo']['name'])){
                if($upload_data = $this->files_uploader->upload('logo')){
                    $logo = $upload_data['file_name'];
                    $this->session->set_flashdata('info','Theme logo uploaded successfully');
                }else{
                    $logo = '';
                }
            }else{
                $logo = '';
            }
            $data = array(
                'name'=>$this->input->post('name'),
                'logo'=>$logo,
                'slug'=>$this->input->post('slug'),
                'primary_background_color'=>$this->input->post('primary_background_color'),
                'secondary_background_color'=>$this->input->post('secondary_background_color'),
                'tertiary_background_color'=>$this->input->post('tertiary_background_color'),
                'quaternary_background_color'=>$this->input->post('quaternary_background_color'),
                'primary_text_color'=>$this->input->post('primary_text_color'),
                'secondary_text_color'=>$this->input->post('secondary_text_color'),
                'tertiary_text_color'=>$this->input->post('tertiary_text_color'),
                'primary_border_color'=>$this->input->post('primary_border_color'),
                'secondary_border_color'=>$this->input->post('secondary_border_color'),
                'tertiary_border_color'=>$this->input->post('tertiary_border_color'),
                'active'=>1,
                'created_on'=>time(),
                'created_by'=>$this->ion_auth->get_user()->id
            );
            $id = $this->themes_m->insert($data);
            if($id){
                $this->session->set_flashdata('success','Theme created successfully');
            }else{
                $this->session->set_flashdata('error','Theme could not be created');
            }
            redirect('admin/themes/listing');
        }else{
            foreach ($this->validation_rules as $key => $field){
                $field_name = $field['field'];
                $post->$field_name = set_value($field_name);
            }
        }
        $data['id'] = '';
        $data['post'] = $post;
        $this->template->title('Create Theme')->build('admin/form',$data);
    }

    public function listing(){
        $data = array();
        $data['posts'] = $this->themes_m->get_all();
        $this->template->title('Theme Listing')->build('admin/listing',$data);
    }


    public function edit($id = 0){
        $id OR redirect('admin/themes/listing');
        $data = array();
        $post = new stdClass();  
        $post = $this->themes_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry the Theme does not exist');
            redirect('admin/themes/listing');
        } 
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            if(!empty($_FILES['logo']['name'])){
                if($upload_data = $this->files_uploader->upload('logo')){
                    $logo = $upload_data['file_name'];
                    //delete old logo
                    if(is_file(FCPATH.'uploads/files/'.$post->logo)&&$logo!==$post->logo){
                        if(unlink(FCPATH.'uploads/files/'.$post->logo)){
                            $this->session->set_flashdata('info','Theme logo successfully replaced');
                        }
                    }
                }else{
                    $this->session->set_flashdata('warning','Could not upload photo');
                    $logo = '';
                }
            }else{
                $logo = '';
            }
            $data = array(
                'name'=>$this->input->post('name'),
                'slug'=>$this->input->post('slug'),
                'primary_background_color'=>$this->input->post('primary_background_color'),
                'secondary_background_color'=>$this->input->post('secondary_background_color'),
                'quaternary_background_color'=>$this->input->post('quaternary_background_color'),
                'tertiary_background_color'=>$this->input->post('tertiary_background_color'),
                'primary_text_color'=>$this->input->post('primary_text_color'),
                'secondary_text_color'=>$this->input->post('secondary_text_color'),
                'tertiary_text_color'=>$this->input->post('tertiary_text_color'),
                'primary_border_color'=>$this->input->post('primary_border_color'),
                'secondary_border_color'=>$this->input->post('secondary_border_color'),
                'tertiary_border_color'=>$this->input->post('tertiary_border_color'),
                'logo'=>$logo?$logo:$post->logo,
                'modified_on'=>time(),
                'modified_by'=>$this->ion_auth->get_user()->id
            );
            $result = $this->themes_m->update($id,$data);
            if($result){
                $this->session->set_flashdata('success','Theme Changes Saved Successfully');
            }else{
                $this->session->set_flashdata('error','Changes could not be saved');
            }
            if($this->input->post('new_item')){
                redirect('admin/themes/create','refresh');
            }else{
                redirect('admin/themes/listing');
            }
        }else{

        }
        $data['id'] = $id;
        $data['post'] = $post;
        $this->template->title('Edit Theme')->build('admin/form',$data);
    }

    public function name_is_unique(){
        if($theme = $this->themes_m->get_by_slug($this->input->post('slug'))){
            if($this->input->post('id')==$theme->id){
                return TRUE;
            }else{
                $this->form_validation->set_message('name_is_unique', 'The theme already exists.');
                return FALSE;
            }
        }else{
            return TRUE;
        }
    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_hide'){
            for($i=0;$i<count($action_to);$i++){
                $this->hide($action_to[$i],FALSE);
            }
        }else if($action == 'bulk_activate'){
            for($i=0;$i<count($action_to);$i++){
                $this->activate($action_to[$i],FALSE);
            }
        }else if($action == 'bulk_delete'){
            for($i=0;$i<count($action_to);$i++){
                $this->delete($action_to[$i],FALSE);
            }
        }
        redirect('admin/themes/listing');
    }

    function hide($id = 0,$redirect= TRUE){
        $id OR redirect('admin/themes/listing');
        $post = $this->admin_menus_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the Theme does not exist');
            redirect('admin/themes/listing');
        }
        $result = $this->themes_m->update($post->id,array('active'=>0,'modified_on'=>time(),'modified_by'=>$this->ion_auth->get_user()->id));

        if($result){
            $this->session->set_flashdata('success','Themes was successfully hidden');
        }else{
            $this->session->set_flashdata('error','Unable to hide '.$post->name.' Theme');
        }

        if($redirect){
            redirect('admin/themes/listing');
        }
        return TRUE;
    }

    function activate($id = 0,$redirect= TRUE){
        $id OR redirect('admin/themes/listing');
        $post = $this->admin_menus_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the Theme does not exist');
            redirect('admin/themes/listing');
        }

        $result = $this->themes_m->update($post->id,array('active'=>1,'modified_on'=>time(),'modified_by'=>$this->ion_auth->get_user()->id));

        if($result){
            $this->session->set_flashdata('success','Themes were successfully activated');
        }else{
            $this->session->set_flashdata('error','Unable to hide '.$post->name.' Theme');
        }

        if($redirect){
            redirect('admin/themes/listing');
        }
        return TRUE;
    }


    function delete($id = 0,$redirect= TRUE){
        $id OR redirect('admin/themes/listing');
        $post = $this->admin_menus_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the Theme does not exist');
            redirect('admin/themes/listing');
        }

        $result = $this->themes_m->delete($post->id);

        if($result){
            $this->session->set_flashdata('success','Theme was successfully deleted');
        }else{
            $this->session->set_flashdata('error','Unable to delete '.$post->name.' Theme');
        }

        if($redirect){
            redirect('admin/themes/listing');
        }
        return TRUE;
    }


    function set_as_default($id = 0){
        $id OR redirect('admin/themes/listing');
        $theme = $this->themes_m->get_default_theme();
        if($theme){
            $input = array(
                'default_theme' => 0,
                'modified_by' => $this->user->id,
                'modified_on' => time(),
            );
            if($result = $this->themes_m->update($theme->id,$input)){
                $this->session->set_flashdata('info',$theme->name.' successfully removed as default theme.');
            }else{
                $this->session->set_flashdata('error',$theme->name.' could not be removed as default theme.');
            }
        }else{
            $this->session->set_flashdata('error','Could not find default theme.');
        }
        $input = array(
            'default_theme' => 1,
            'modified_by' => $this->user->id,
            'modified_on' => time(),
        );

        $theme = $this->themes_m->get($id);
        if($theme){
            if($result = $this->themes_m->update($theme->id,$input)){
                $this->session->set_flashdata('success',$theme->name.' successfully set as default theme.');
            }else{
                $this->session->set_flashdata('error',$theme->name.' could not be set as default theme.');
            }
        }else{
            $this->session->set_flashdata('error','Could not find theme to set as default.');
        }
        redirect('admin/themes/listing');
    }
}