<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller
{

	protected $validation_rules=array(
        array(
                'field' =>  'name',
                'label' =>  'Sacco Name',
                'rules' =>  'trim|required',
            ),
        array(
                'field' =>  'slug',
                'label' =>  'Sacco Slug',
                'rules' =>  'trim|required|callback_sacco_is_unique',
            ),
        array(
                'field' =>  'partner',
                'label' =>  'Is a Chamasoft Partner',
                'rules' =>  'trim|numeric',
            ),
        array(
                'field' =>  'logo',
                'label' =>  'Sacco Logo',
                'rules' =>  '',
            ),
        array(
                'field' =>  'primary_color',
                'label' =>  'Primary Color',
                'rules' =>  '',
            ),
        array(
                'field' =>  'secondary_color',
                'label' =>  'Secondary Color',
                'rules' =>  '',
            ),
        array(
                'field' =>  'tertiary_color',
                'label' =>  'Tertiary Color',
                'rules' =>  '',
            ),
        array(
                'field' =>  'text_color',
                'label' =>  'Text Color',
                'rules' =>  '',
            ),
        array(
            'field' =>  'country_id',
            'label' =>  'Country',
            'rules' =>  'trim|required|numeric',
        ),
    );

	function __construct(){
        parent::__construct();
        $this->load->library('files_uploader');
        $this->load->model('saccos_m');
        $this->load->model('sacco_branches/sacco_branches_m');
        $this->country_options = $this->countries_m->get_country_options();
    }

    public function create(){
    	$data = array();
        $post = new stdClass();      
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            if(!empty($_FILES['logo']['name'])){
                if($upload_data = $this->files_uploader->upload('logo')){
                    $logo = $upload_data['file_name'];
                    $this->session->set_flashdata('info','Sacco logo uploaded successfully');
                }else{
                    $logo = '';
                }
            }else{
                $logo = '';
            }
            $data = array(
                'name'=>$this->input->post('name'),
                'slug'=>$this->input->post('slug'),
                'partner'=>$this->input->post('partner')?1:0,
                'primary_color'=>$this->input->post('primary_color'),
                'secondary_color'=>$this->input->post('secondary_color'),
                'tertiary_color'=>$this->input->post('tertiary_color'),
                'text_color'=>$this->input->post('text_color'),
                'country_id'=>$this->input->post('country_id'),
                'active'=>1,
                'logo'=>$logo,
                'created_on'=>time(),
                'created_by'=>$this->ion_auth->get_user()->id
            );
            $id = $this->saccos_m->insert($data);
            if($id){
                $this->session->set_flashdata('success','Sacco created successfully');
            }else{
                $this->session->set_flashdata('error','Sacco could not be created');
            }
            if($this->input->post('new_item')){
                redirect('admin/saccos/create','refresh');
            }else{
                redirect('admin/saccos/listing');
            }
        }else{
        	foreach ($this->validation_rules as $key => $field){
                $post->$field['field'] = set_value($field['field']);
            }
        }
        $data['id'] = '';
        $data['post'] = $post;
        $data['country_options'] = $this->country_options;
        $this->template->title('Create Sacco')->build('admin/form',$data);
    }

    public function listing(){
        $data = array();
        $total_rows = $this->saccos_m->count_all();
        $pagination = create_pagination('admin/saccos/listing/pages', $total_rows,50,5,TRUE);
        $data['posts'] = $this->saccos_m->limit($pagination['limit'])->get_all();
        $data['pagination'] = $pagination;
        $data['country_options'] = $this->country_options;
        $this->template->title('List Saccos')->build('admin/listing',$data);
    }

    public function edit($id = 0){
        //print_r($_COOKIE); die();
        $id OR redirect('admin/saccos/listing');
        $data = array();
        $post = new stdClass();  
        $post = $this->saccos_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry the sacco does not exist');
            redirect('admin/saccos/listing');
        } 
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            if(!empty($_FILES['logo']['name'])){
                if($upload_data = $this->files_uploader->upload('logo')){
                    $logo = $upload_data['file_name'];
                    //delete old logo
                    if(is_file(FCPATH.'uploads/files/'.$post->logo)){
                        if(unlink(FCPATH.'uploads/files/'.$post->logo)){
                            $this->session->set_flashdata('info','Sacco logo successfully replaced');
                        }
                    }
                }else{
                    $logo = '';
                }
            }else{
                $logo = '';
            }
            $data = array(
                'name'=>$this->input->post('name'),
                'slug'=>$this->input->post('slug'),
                'partner'=>$this->input->post('partner')?1:0,
                'primary_color'=>$this->input->post('primary_color'),
                'secondary_color'=>$this->input->post('secondary_color'),
                'tertiary_color'=>$this->input->post('tertiary_color'),
                'text_color'=>$this->input->post('text_color'),
                'country_id'=>$this->input->post('country_id'),
                'active'=>1,
                'logo'=>$logo,
                'modified_on'=>time(),
                'modified_by'=>$this->ion_auth->get_user()->id
            );
            $result = $this->saccos_m->update($id,$data);
            if($result){
                $sacco_branches = $this->sacco_branches_m->get_sacco_branch_options_by_sacco_id();
                if($sacco_branches){
                    $sacco_branch_updated = 0;
                    $failed_to_update = 0;
                    foreach ($sacco_branches as $key => $sacco_branch):
                        $sacco_input = array(
                            'country_id'=>$this->input->post('country_id'),
                            'modified_on'=>time(),
                            'modified_by'=>$this->ion_auth->get_user()->id
                        );
                        if($update = $this->sacco_branches_m->update($key,$sacco_input)){
                            $sacco_branch_updated++;
                        }else{
                            $failed_to_update++;
                        }
                    endforeach;
                    if($sacco_branch_updated){
                        $this->session->set_flashdata('success','Sacco Changes Saved Successfully ,'.$sacco_branch_updated .' Banck branches updated successfully');
                    }else{
                        $this->session->set_flashdata('success','Sacco Changes Saved Successfully ,'.$failed_to_update .' Sacco branches failed updated successfully');  
                    }
                    $this->session->set_flashdata('success','Sacco Changes Saved Successfully');
                }
            }else{
                $this->session->set_flashdata('error','Changes could not be saved');
            }
            if($this->input->post('new_item')){
                redirect('admin/saccos/create','refresh');
            }else{
                redirect('admin/saccos/edit/'.$id);
            }
        }else{

        }
        $data['id'] = $id;
        $data['post'] = $post;
        $data['country_options'] = $this->country_options;
        $this->template->title('Edit Sacco')->build('admin/form',$data);
    }

    public function change_sacco_country_id(){
        $saccos = $this->saccos_m->get_all();
        $saccos_updated =0;
        $sacco_no_updated = 0;
        $sacco_branch_updated = 0;
        $failed_to_update = 0;
        foreach ($saccos as $sacco_data => $sacco):
            $data = array(
                'country_id'=>1,
                'active'=>1,
                'modified_on'=>time(),
                'modified_by'=>$this->ion_auth->get_user()->id
            );
            $saccos_updated++;
            $result = $this->saccos_m->update($sacco->id,$data);
            if($result){
                $sacco_branches = $this->sacco_branches_m->get_sacco_branch_options_by_sacco_id();
                if($sacco_branches){
                    
                    foreach($sacco_branches as $key => $sacco_branch):
                        $sacco_input = array(
                            'country_id'=>1,
                            'modified_on'=>time(),
                            'modified_by'=>$this->ion_auth->get_user()->id
                        );
                        if($update = $this->sacco_branches_m->update($key,$sacco_input)){
                            $sacco_branch_updated++;
                        }else{
                            $failed_to_update++;
                        }
                    endforeach;
                   /* if($sacco_branch_updated){
                        $this->session->set_flashdata('success','Sacco Changes Saved Successfully ,'.$sacco_branch_updated .' Banck branches updated successfully');
                    }else{
                        $this->session->set_flashdata('success','Sacco Changes Saved Successfully ,'.$failed_to_update .' Sacco branches failed updated successfully');  
                    }
                    $this->session->set_flashdata('success','Sacco Changes Saved Successfully');*/
                }
            }else{
                $sacco_no_updated++;
                //$this->session->set_flashdata('error','Changes could not be saved');
            }
        endforeach;

        echo $sacco_branch_updated .' Sacco branches updated <br>';
        echo $failed_to_update .' Sacco branches failed to updat <br>';
        echo $sacco_no_updated .' Sacco  failed updated <br>';
        echo $saccos_updated .' Saccos  successfully updated <br>';

    }

    public function sacco_is_unique(){
        if($sacco = $this->saccos_m->get_by_slug($this->input->post('slug'))){
            if($this->input->post('id')==$sacco->id){
                return TRUE;
            }else{
                $this->form_validation->set_message('sacco_is_unique', 'The sacco already exists.');
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
        }
        redirect('admin/saccos/listing');
    }

    function hide($id = 0,$redirect= TRUE){
        $id OR redirect('admin/saccos/listing');
        $post = $this->admin_menus_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the Sacco does not exist');
            redirect('admin/saccos/listing');
        }
        $result = $this->saccos_m->update($post->id,array('active'=>0,'modified_on'=>time(),'modified_by'=>$this->ion_auth->get_user()->id));

        if($result){
            $this->session->set_flashdata('success','Saccos was successfully hidden');
        }else{
            $this->session->set_flashdata('error','Unable to hide '.$post->name.' Sacco');
        }

        if($redirect){
            redirect('admin/saccos/listing');
        }
        return TRUE;
    }

    function activate($id = 0,$redirect= TRUE){
        $id OR redirect('admin/saccos/listing');
        $post = $this->admin_menus_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the Sacco does not exist');
            redirect('admin/saccos/listing');
        }

        $result = $this->saccos_m->update($post->id,array('active'=>1,'modified_on'=>time(),'modified_by'=>$this->ion_auth->get_user()->id));

        if($result){
            $this->session->set_flashdata('success','Saccos were successfully activated');
        }else{
            $this->session->set_flashdata('error','Unable to hide '.$post->name.' Sacco');
        }

        if($redirect){
            redirect('admin/saccos/listing');
        }
        return TRUE;
    }

}