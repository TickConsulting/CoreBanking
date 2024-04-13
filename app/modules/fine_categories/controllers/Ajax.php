<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{

	protected $data=array();

    protected $validation_rules=array(
        array(
                'field' =>   'name',
                'label' =>   'Category Name',
                'rules' =>   'xss_clean|trim|required',
            ),
        array(
                'field' =>   'slug',
                'label' =>   'Category Name Slug',
                'rules' =>   'xss_clean|trim|callback__is_unique_category_name',
            ),
        array(
                'field' =>  'amount',
                'label' =>  'Category Fines amount',
                'rules' =>  'xss_clean|trim|currency',
            ),
    );

    function _is_unique_category_name(){
        $id = $this->input->post('id');
        $group_id = $this->group->id;
        $name = $this->input->post('name');
        $slug = preg_replace('/\s*/', '', $name);
        if($slug){
            if($this->fine_categories_m->get_by_slug($slug,$id,$group_id))
            {
                $this->form_validation->set_message('_is_unique_category_name','Another Fine Category by the name <strong>`'.$this->input->post('name').'`</strong> already exists');
                return FALSE;
            }
            else
            {
                return TRUE;
            }
            
        }        
    }
   
    public function __construct(){
        parent::__construct();
        $this->load->model('fine_categories_m');
    }

    public function create(){
        $response = array();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $fine_category = array(
                'name'  =>  $this->input->post('name'),
                'slug'  =>  $this->input->post('slug'),
                'amount'  =>  currency($this->input->post('amount')),
                'group_id'  =>  $this->group->id,
                'active'    =>  1,
                'created_by'    =>  $this->user->id,
                'created_on'    =>  time(),
            );
            $id = $this->fine_categories_m->insert($fine_category);
            if($id){
                $fine_category['currency'] = $this->group_currency;
                $fine_category['id'] = $id;
                $fine_category['amount'] = number_to_currency($this->input->post('amount'));
                $response = array(
                    'status' => 1,
                    'message' => 'Fine Category successfully created',
                    'fine_category'=> $fine_category,
                    'refer'=>site_url('group/fine_categories')
                );                    
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not add fine category',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'There are some errors on the form. Please review and try again.',
                'validation_errors' => $this->form_validation->error_array(),
            );
        }
        echo json_encode($response);
    }

    public function edit(){
    	$id = $this->input->post('id');
    	if($id){
    		$post = $this->fine_categories_m->get($id);
    		if($post){
    			$this->form_validation->set_rules($this->validation_rules);
	            if($this->form_validation->run()){
	            	$update = $this->fine_categories_m->update($post->id,array(
                        'name'=>  $this->input->post('name'),
                        'slug'=>  $this->input->post('slug'),
                        'modified_by'=> $this->user->id,
                        'modified_on'=> time(),
                    ));
                    if($update){
	                    $response = array(
			                'status' => 1,
			                'message' => $this->input->post('name').' successfully updated',
			                'refer'=>site_url('group/fine_categories/listing')
			            );
	                }else{
	                	$response = array(
			                'status' => 0,
			                'message' => 'Unable to update Fine Category',
			            );
	                }

	            }else{
	            	$response = array(
		                'status' => 0,
		                'message' => validation_errors(),
	                	'validation_errors'=> validation_errors()
		            );
	            }
    		}else{
    			$response = array(
	                'status' => 0,
	                'message' => 'Sorry the fine category does not exist',
	            );
    		}
    	}else{
    		$response = array(
                'status' => 0,
                'message' => 'Could not find fine catgory id',
            );
    	}
    	echo json_encode($response);

    }


}
?>