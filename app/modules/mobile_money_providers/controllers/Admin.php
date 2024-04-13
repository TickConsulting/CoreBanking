<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller
{

	protected $validation_rules=array(
        array(
                'field' =>  'name',
                'label' =>  'Mobile Money Provider Name',
                'rules' =>  'xss_clean|trim|required',
            ),
        array(
                'field' =>  'slug',
                'label' =>  'Mobile_money_provider Slug',
                'rules' =>  'xss_clean|trim|required|callback_mobile_money_provider_is_unique',
            ),
        array(
                'field' =>  'partner',
                'label' =>  'Is a Chamasoft Partner',
                'rules' =>  'xss_clean|trim|numeric',
            ),
        array(
                'field' =>  'logo',
                'label' =>  'Mobile_money_provider Logo',
                'rules' =>  'xss_clean|',
            ),
        array(
                'field' =>  'primary_color',
                'label' =>  'Primary Color',
                'rules' =>  'xss_clean|',
            ),
        array(
                'field' =>  'secondary_color',
                'label' =>  'Secondary Color',
                'rules' =>  'xss_clean|',
            ),
        array(
                'field' =>  'tertiary_color',
                'label' =>  'Tertiary Color',
                'rules' =>  'xss_clean|',
            ),
        array(
                'field' =>  'text_color',
                'label' =>  'Text Color',
                'rules' =>  'xss_clean|',
            ),
    );

	function __construct(){
        parent::__construct();
        $this->load->library('files_uploader');
        $this->load->model('mobile_money_providers_m');
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
                    $this->session->set_flashdata('info','Mobile Money Provider logo uploaded successfully');
                }else{
                    $logo = '';
                }
            }else{
                $logo = '';
            }
            $data = array(
                'name'=>$this->input->post('name'),
                'slug'=>$this->input->post('slug'),
                'country_id'=>$this->input->post('country_id'),
                'partner'=>$this->input->post('partner')?1:0,
                'primary_color'=>$this->input->post('primary_color'),
                'secondary_color'=>$this->input->post('secondary_color'),
                'tertiary_color'=>$this->input->post('tertiary_color'),
                'text_color'=>$this->input->post('text_color'),
                'active'=>1,
                'logo'=>$logo,
                'created_on'=>time(),
                'created_by'=>$this->ion_auth->get_user()->id
            );
            $id = $this->mobile_money_providers_m->insert($data);
            if($id){
                $this->session->set_flashdata('success','Mobile Money Provider created successfully');
            }else{
                $this->session->set_flashdata('error','Mobile Money Provider could not be created');
            }
            if($this->input->post('new_item')){
                redirect('admin/mobile_money_providers/create','refresh');
            }else{
                redirect('admin/mobile_money_providers/listing');
            }
        }else{
        	foreach ($this->validation_rules as $key => $field){
                $post->$field['field'] = set_value($field['field']);
            }
        }
        $data['id'] = '';
        $data['post'] = $post;
        $data['country_options'] = $this->country_options;
        $this->template->title('Create Mobile Money Provider')->build('admin/form',$data);
    }

    public function listing(){
        $data = array();
        $total_rows = $this->mobile_money_providers_m->count_all();
        $pagination = create_pagination('admin/mobile_money_providers/listing/pages', $total_rows,50,5,TRUE);
        $data['posts'] = $this->mobile_money_providers_m->limit($pagination['limit'])->get_all();
        $data['pagination'] = $pagination;
        $data['country_options'] = $this->country_options;
        $this->template->title('List Mobile Money Providers')->build('admin/listing',$data);
    }

    public function edit($id = 0){
        $id OR redirect('admin/mobile_money_providers/listing');
        $data = array();
        $post = new stdClass();  
        $post = $this->mobile_money_providers_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry the mobile_money_provider does not exist');
            redirect('admin/mobile_money_providers/listing');
        } 
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            if(!empty($_FILES['logo']['name'])){
                if($upload_data = $this->files_uploader->upload('logo')){
                    $logo = $upload_data['file_name'];
                    //delete old logo
                    if(is_file(FCPATH.'uploads/files/'.$post->logo)){
                        if(unlink(FCPATH.'uploads/files/'.$post->logo)){
                            $this->session->set_flashdata('info','Mobile Money Provider logo successfully replaced');
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

                'country_id'=>$this->input->post('country_id'),
                'partner'=>$this->input->post('partner')?1:0,
                'primary_color'=>$this->input->post('primary_color'),
                'secondary_color'=>$this->input->post('secondary_color'),
                'tertiary_color'=>$this->input->post('tertiary_color'),
                'text_color'=>$this->input->post('text_color'),
                'active'=>1,
                'logo'=>$logo,
                'modified_on'=>time(),
                'modified_by'=>$this->ion_auth->get_user()->id
            );
            $result = $this->mobile_money_providers_m->update($id,$data);
            if($result){
                $this->session->set_flashdata('success','Mobile Money Provider Changes Saved Successfully');
            }else{
                $this->session->set_flashdata('error','Changes could not be saved');
            }
            if($this->input->post('new_item')){
                redirect('admin/mobile_money_providers/create','refresh');
            }else{
                redirect('admin/mobile_money_providers/listing');
            }
        }else{

        }
        $data['id'] = $id;
        $data['country_options'] = $this->country_options;
        $data['post'] = $post;
        $this->template->title('Edit Mobile Money Provider')->build('admin/form',$data);
    }

    public function mobile_money_provider_is_unique(){
        if($mobile_money_provider = $this->mobile_money_providers_m->get_by_slug($this->input->post('slug'))){
            if($this->input->post('id')==$mobile_money_provider->id){
                return TRUE;
            }else{
                $this->form_validation->set_message('mobile_money_provider_is_unique', 'The mobile_money_provider already exists.');
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
        redirect('admin/mobile_money_providers/listing');
    }

    function hide($id = 0,$redirect= TRUE){
        $id OR redirect('admin/mobile_money_providers/listing');
        $post = $this->admin_menus_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the Mobile_money_provider does not exist');
            redirect('admin/mobile_money_providers/listing');
        }
        $result = $this->mobile_money_providers_m->update($post->id,array('active'=>0,'modified_on'=>time(),'modified_by'=>$this->ion_auth->get_user()->id));

        if($result){
            $this->session->set_flashdata('success','Mobile_money_providers was successfully hidden');
        }else{
            $this->session->set_flashdata('error','Unable to hide '.$post->name.' Mobile_money_provider');
        }

        if($redirect){
            redirect('admin/mobile_money_providers/listing');
        }
        return TRUE;
    }

    function activate($id = 0,$redirect= TRUE){
        $id OR redirect('admin/mobile_money_providers/listing');
        $post = $this->admin_menus_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the Mobile_money_provider does not exist');
            redirect('admin/mobile_money_providers/listing');
        }

        $result = $this->mobile_money_providers_m->update($post->id,array('active'=>1,'modified_on'=>time(),'modified_by'=>$this->ion_auth->get_user()->id));

        if($result){
            $this->session->set_flashdata('success','Mobile_money_providers were successfully activated');
        }else{
            $this->session->set_flashdata('error','Unable to hide '.$post->name.' Mobile_money_provider');
        }

        if($redirect){
            redirect('admin/mobile_money_providers/listing');
        }
        return TRUE;
    }

    function get_all_mobile_money_providers_to_json(){
        $posts = $this->mobile_money_providers_m->get_all();
        $data = array();
        foreach ($posts as $key => $post) {
            $data[] = array(
                'id'=>$post->id,
                'name'=>$post->name,
                'logo'=>$post->logo,
                'slug'=>$post->slug,
                'primary_color'=>$post->primary_color,
                'secondary_color'=>$post->secondary_color,
                'tertiary_color'=>$post->tertiary_color,
                'text_color'=>$post->text_color,
                
            );
            
        }

        file_put_contents("logs/mobile_money_providers.json",json_encode($data)."\n",FILE_APPEND);
    }

}