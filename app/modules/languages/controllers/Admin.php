<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin extends Admin_Controller{
        protected $validation_rules = array(
        	array(
                    'field' => 'name',
                    'label' => 'Name',
                    'rules' => 'trim|required|callback_name_is_unique',
                ),array(
                    'field' => 'country_id',
                    'label' => 'Country',
                    'rules' => 'trim|required',
                ),array(
                    'field' => 'short_code',
                    'label' => 'Short Code',
                    'rules' => 'trim|required|callback_short_code_is_unique',
                )
        );	

	function __construct(){
		parent::__construct();
		$this->load->model('languages_m');
		$this->load->model('countries/countries_m');
		$this->load->library('session');
		
	}

	function index(){

	}

	function short_code_is_unique(){
		$id=$this->input->post('id');
                if($language = $this->languages_m->get_language_by_short_code($this->input->post('short_code'))){
        	       if(isset($language->short_code)){
        		        if($language->id==$id){
        			     return true;
                                }
        		       $this->form_validation->set_message('short_code_is_unique', 'The language code already exists.');
                                return FALSE;
        	        }else{
        		      return TRUE;
        	        }
                }else{
                        return TRUE;
                }
        }

        function name_is_unique(){
                $id=$this->input->post('id');
                if($language = $this->languages_m->get_language_by_name($this->input->post('name'))){
        	       if(isset($language->name)){
        		        if($language->id==$id){
        			     return true;
        		        }
        		        $this->form_validation->set_message('name_is_unique', 'The language already exists.');
                                return FALSE;
        	        }else{
        		         return TRUE;
        	        }
                }else{
        		return TRUE;
        	}
        }

	function create(){
		$input=array();
		$post= new stdClass();
		$this->data= new stdClass();
		$this->form_validation->set_rules($this->validation_rules);
        	if($this->form_validation->run()){
        		$input = array(
        			'name' => $this->input->post('name'),
        			'country_id' => $this->input->post('country_id'),
        			'short_code' => strtolower($this->input->post('short_code')),
        			'active' => 1,
        			'created_on' =>  time(),
        			'created_by' => $this->user->id,
        		);

        	       $this->languages_m->insert($input);
        	       redirect('admin/languages/listing');
        	}else{
        		foreach ($this->validation_rules as $key => $field){
                                $field_value = $field['field'];
	                       $post->$field_value = set_value($field['field']);
	                }
        	}
        	$this->data->country_options=$this->countries_m->get_admin_country_options();
        	$this->data->post = $post;
        	$this->data->input = $input;
        	$this->data->id = '';
		$this->template->title('Create Language')->build('form',$this->data);
	}

	function edit($id){
		$input=array();
		$post= new stdClass();
		$this->data= new stdClass();
		if(!$post=$this->languages_m->get($id)){
			$this->session->set_flashdata('error','Language ID record not found');
			redirect('admin/languages/listing');
		}
		$this->form_validation->set_rules($this->validation_rules);
        	if($this->form_validation->run()){
        		$input = array(
        			'name' => $this->input->post('name'),
        			'country_id' => $this->input->post('country_id'),
        			'short_code' => strtolower($this->input->post('short_code')),
        			'active' => 1,
        			'modified_on' =>  time(),
        			'modified_by' => $this->user->id,
        		);
        	       $this->languages_m->update($id,$input);
        	       redirect('admin/languages/listing');
        	}else{
                        foreach (array_keys($this->validation_rules) as $field){
                                if(isset($_POST[$field])){
                                    $post->$field = $this->form_validation->$field;
                                }
                        }
	        }
        	$this->data->country_options=$this->countries_m->get_admin_country_options();
        	$this->data->post = $post;
        	$this->data->input = $input;
        	$this->data->id = $id;
		$this->template->title('Update Language')->build('form',$this->data);
	}

	function listing(){
		$this->data=new stdClass();
		$total_rows=count($this->languages_m->get_all());
		$pagination = create_pagination('admin/languages/listing',$total_rows,50,5);
        	$this->data->posts = $this->languages_m->limit($pagination['limit'])->get_all();
        	$this->data->pagination=$pagination;
        	$this->template->title('List Language')->build('listing',$this->data);	
	}	
}