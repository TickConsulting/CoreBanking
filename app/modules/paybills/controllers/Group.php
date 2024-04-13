<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller
{
    protected $data= array();

    protected $validation_rules = array(
            array(
                'field' =>  'paybill_name',
                'label' =>  'Paybill Name',
                'rules' =>  'required',
            ),
            array(
                'field' =>  'paybill_number',
                'label' =>  'Paybill Number',
                'rules' =>  'required|trim',
            ),
            array(
                'field' =>  'paybill_account_number',
                'label' =>  'Account Number',
                'rules' =>  'required|trim',
            ),
        );

	function __construct()
    {
        parent::__construct();
        $this->load->model('paybills/paybills_m');
    }
    
	public function ajax_create(){
	    $this->form_validation->set_rules($this->validation_rules);
	    if($this->form_validation->run()){
	    	$input = array(
                'name'  =>  $this->input->post('paybill_name'),
                'paybill_number'  =>  $this->input->post('paybill_number'),
                'account_number'  =>  $this->input->post('paybill_account_number'),
                'group_id'  =>  $this->group->id,
                'active'    =>  1,
                'is_deleted'    =>  0,
                'created_by'    =>  $this->user->id,
                'created_on'    =>  time(),
            );
	        $id = $this->paybills_m->insert($input);
	        if($id){
	            if($paybill = $this->paybills_m->get($id)){
	                echo json_encode($paybill);
	            }else{
	                echo 'Could not add find any paybills';
	            }
	        }else{
	            echo 'Could not add paybill';
	        }
	    }else{
	        echo validation_errors();
	    }
	}
}
?>