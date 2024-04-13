<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller
{

	protected $validation_rules=array(
        array(
                'field' =>  'name',
                'label' =>  'Referrer Name',
                'rules' =>  'trim|required|callback_name_is_unique',
            ),array(
                'field' =>  'referrer_information_required',
                'label' =>  'Referrer Information Required',
                'rules' =>  'trim|numeric',
            ),array(
                'field' =>  'referrer_information_label',
                'label' =>  'Required information label',
                'rules' =>  'trim',
            ),
    );

	function __construct(){
        parent::__construct();
        $this->load->model('referrers_m');
    }

    public function create(){
    	$data = array();
        $post = new stdClass();  
        if($this->input->post('referrer_information_required')==1){
            $this->validation_rules[] = array(
                'field' =>  'referrer_information_label',
                'label' =>  'Required information label',
                'rules' =>  'trim|required',
            );
        }    
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $data = array(
                'name'=>$this->input->post('name'),
                'referrer_information_required'=>$this->input->post('referrer_information_required')?1:0,
                'referrer_information_label'=>$this->input->post('referrer_information_label'),
                'active'=>1,
                'created_on'=>time(),
                'created_by'=>$this->ion_auth->get_user()->id
            );
            $id = $this->referrers_m->insert($data);
            if($id){
                $this->session->set_flashdata('success','Referrer created successfully');
            }else{
                $this->session->set_flashdata('error','Referrer could not be created');
            }
            if($this->input->post('new_item')){
                redirect('admin/referrers/create','refresh');
            }else{
                redirect('admin/referrers/listing');
            }
        }else{
        	foreach ($this->validation_rules as $key => $field){
                $post->$field['field'] = set_value($field['field']);
            }
        }
        $data['id'] = '';
        $data['post'] = $post;
        $this->template->title('Create Referrer')->build('admin/form',$data);
    }

    public function listing(){
        $data = array();
        $total_rows = $this->referrers_m->count_all();
        $pagination = create_pagination('admin/referrers/listing/pages', $total_rows,50,5,TRUE);
        $data['posts'] = $this->referrers_m->limit($pagination['limit'])->get_all();
        $data['pagination'] = $pagination;
        $this->template->title('List Referrers')->build('admin/listing',$data);
    }

    public function edit($id = 0){
        $id OR redirect('admin/referrers/listing');
        $data = array();
        $post = new stdClass();  
        $post = $this->referrers_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry the referrer does not exist');
            redirect('admin/referrers/listing');
        } 
        if($this->input->post('referrer_information_required')==1){
            $this->validation_rules[] = array(
                'field' =>  'referrer_information_label',
                'label' =>  'Required information label',
                'rules' =>  'trim|required',
            );
        }
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $data = array(
                'name'=>$this->input->post('name'),
                'referrer_information_required'=>$this->input->post('referrer_information_required')?1:0,
                'referrer_information_label'=>$this->input->post('referrer_information_label'),
                'modified_on'=>time(),
                'modified_by'=>$this->ion_auth->get_user()->id
            );
            $result = $this->referrers_m->update($id,$data);
            if($result){
                $this->session->set_flashdata('success','Referrer Changes Saved Successfully');
            }else{
                $this->session->set_flashdata('error','Changes could not be saved');
            }
            if($this->input->post('new_item')){
                redirect('admin/referrers/create','refresh');
            }else{
                redirect('admin/referrers/edit/'.$id);
            }
        }else{

        }
        $data['id'] = $id;
        $data['post'] = $post;
        $this->template->title('Edit Referrer')->build('admin/form',$data);
    }

    public function name_is_unique(){
        if($referrer = $this->referrers_m->get_where(array('name'=>$this->input->post('name')),FALSE)){
            if($this->input->post('id')==$referrer->id){
                return TRUE;
            }else{
                $this->form_validation->set_message('name_is_unique', 'The referrer name already exists.');
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
        redirect('admin/referrers/listing');
    }

    function hide($id = 0,$redirect= TRUE){
        $id OR redirect('admin/referrers/listing');
        $post = $this->admin_menus_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the Referrer does not exist');
            redirect('admin/referrers/listing');
        }
        $result = $this->referrers_m->update($post->id,array('active'=>0,'modified_on'=>time(),'modified_by'=>$this->ion_auth->get_user()->id));

        if($result){
            $this->session->set_flashdata('success','Referrers were successfully hidden');
        }else{
            $this->session->set_flashdata('error','Unable to hide '.$post->name.' Referrer');
        }

        if($redirect){
            redirect('admin/referrers/listing');
        }
        return TRUE;
    }

    function activate($id = 0,$redirect= TRUE){
        $id OR redirect('admin/referrers/listing');
        $post = $this->admin_menus_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the Referrer does not exist');
            redirect('admin/referrers/listing');
        }

        $result = $this->referrers_m->update($post->id,array('active'=>1,'modified_on'=>time(),'modified_by'=>$this->ion_auth->get_user()->id));

        if($result){
            $this->session->set_flashdata('success','Referrers were successfully activated');
        }else{
            $this->session->set_flashdata('error','Unable to hide '.$post->name.' Referrer');
        }

        if($redirect){
            redirect('admin/referrers/listing');
        }
        return TRUE;
    }

}