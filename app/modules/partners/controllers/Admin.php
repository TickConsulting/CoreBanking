<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller{

	protected $data = array();

	protected $validation_rules = array(
        array(
            'field' => 'name',
            'label' => 'Partner\'s Official Name',
            'rules' => 'trim|required|xss_clean',
        ), 
        array(
            'field' => 'slug',
            'label' => 'Slug',
            'rules' => 'trim|required|callback__is_unique_partner_name',
        ), 
        array(
            'field' => 'user_ids',
            'label' => 'Partner Users',
            'rules' => 'callback__check_partner_users',
        ),
    );

    protected $commission_type_options = array(
        1 => "Percentage Amount Commission per Group Payment",
        2 => "Fixed Amount Commission per Group Payment",
    );

	function __construct(){
        parent::__construct();
        $this->load->model('partners_m');
    }

    function index(){

    }

    function listing(){
        $total_rows = $this->partners_m->count_all();
        $pagination = create_pagination('admin/partners/listing/pages', $total_rows,50,5,TRUE);
        $this->data['posts'] = $this->partners_m->limit($pagination['limit'])->get_all();
        $this->data['pagination'] = $pagination;
    	$this->template->title('Partners Listing')->build('admin/listing',$this->data);
    }

    function create(){
    	$post = new stdClass();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $input = array(
                "name" => $this->input->post('name'),
                "slug" => $this->input->post('slug'),
                "active" => 1,
                "created_on" => time(),
                "created_by" => $this->user->id,
            );
            if($id = $this->partners_m->insert($input)){
                $this->partners_m->delete_user_partner_pairings($id);
                $user_ids = $this->input->post('user_ids');
                $result = TRUE;
                foreach($user_ids as $user_id):
                    $input = array(
                        'partner_id' => $id,
                        'user_id' => $user_id,
                        "created_on" => time(),
                        "created_by" => $this->user->id,
                    );
                    if($this->partners_m->insert_user_partner_pairing($input)){

                    }else{
                        $result = FALSE;
                    }
                endforeach;
                if($result){
                    $this->session->set_flashdata('success',"Partner created successfully");
                }else{
                    $this->session->set_flashdata('warning',"something went wrong while creating the user partner pairings");
                }
                redirect("admin/partners/listing");
            }else{
                $this->session->set_flashdata('error',"Could not insert partner");
            }
        }else{
        	foreach ($this->validation_rules as $key => $field){
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $this->data['post'] = $post;
        $this->data['id'] = '';
        $this->data['selected_user_ids'] = $this->input->post('user_ids');
        $this->data['selected_user_options'] = $this->users_m->get_user_selected_user_options($this->data['selected_user_ids']);
    	$this->template->title('Create Partner')->build('admin/form',$this->data);
    }

    function edit($id = 0){
        $id OR redirect('admin/partners/listing');
        $post = $this->partners_m->get($id);
        $post OR redirect('admin/partners/listing');
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $input = array(
                "name" => $this->input->post('name'),
                "slug" => $this->input->post('slug'),
                "modified_on" => time(),
                "modified_by" => $this->user->id,
            );
            if($this->partners_m->update($id,$input)){
                $this->partners_m->delete_user_partner_pairings($id);
                $user_ids = $this->input->post('user_ids');
                $result = TRUE;
                foreach($user_ids as $user_id):
                    $input = array(
                        'partner_id' => $id,
                        'user_id' => $user_id,
                        "created_on" => time(),
                        "created_by" => $this->user->id,
                    );
                    if($this->partners_m->insert_user_partner_pairing($input)){

                    }else{
                        $result = FALSE;
                    }
                endforeach;
                if($result){
                    $this->session->set_flashdata('success',"Partner changes saved successfully");
                }else{
                    $this->session->set_flashdata('warning',"Something went wrong while creating the user partner pairings");
                }
                redirect("admin/partners/listing");
            }else{
                $this->session->set_flashdata('error',"Could not update partner");
            }
        }else{
            foreach ($this->validation_rules as $key => $field){
                if(isset($post->$field['field'])){
                    $post->$field['field'] = $post->$field['field']?:set_value($field['field']);
                }
            }
        }
        $this->data['post'] = $post;
        $this->data['id'] = $id;
        $this->data['selected_user_ids'] = $this->input->post('user_ids')?:$this->partners_m->get_user_partner_pairings_array($id);
        $this->data['selected_user_options'] = $this->users_m->get_user_selected_user_options($this->data['selected_user_ids']);
        $this->template->title('Edit Partner')->build('admin/form',$this->data);
    }

    function view($id = 0){
        $id OR redirect('admin/partners/listing');
        $post = $this->partners_m->get($id);
        $post OR redirect('admin/partners/listing');
        $this->data['post'] = $post;
        $user_partner_pairings_array = $this->partners_m->get_user_partner_pairings_array($post->id);
        $this->data['users'] = $this->users_m->get_partner_users($user_partner_pairings_array);
        $this->data['user_count'] = count($this->data['users']);
        $this->data['groups'] = $this->groups_m->get_groups_by_partner_id($post->id);
        $this->data['group_count'] = $this->groups_m->count_groups_by_partner_id($post->id);
        $this->data['partner_commission_matrices'] = $this->partners_m->get_partner_commission_matrices($id);
        $this->data['partner_commission_type'] = $this->partners_m->get_partner_commission_type($id);
        $this->template->title('View Partner',$post->name)->build('admin/view',$this->data);
    }

    function commission_matrix($id = 0){
        $id OR redirect('admin/partners/listing');
        $post = $this->partners_m->get($id);
        $post OR redirect('admin/partners/listing');

        $validation_rules = array(
            array(
                'field' => 'commission_type',
                'label' => 'Commission Type',
                'rules' => 'trim|required|numeric|xss_clean',
            ), 
        );  

        $this->form_validation->set_rules($validation_rules);

        $entries_are_valid = TRUE;

        $commission_type = $this->input->post('commission_type');
        $percentage_minimum_group_numbers = $this->input->post('percentage_minimum_group_numbers');
        $percentage_maximum_group_numbers = $this->input->post('percentage_maximum_group_numbers');
        $minimum_group_numbers = $this->input->post('minimum_group_numbers');
        $maximum_group_numbers = $this->input->post('maximum_group_numbers');
        $percentages = $this->input->post('percentages');
        $fixed_amounts = $this->input->post('fixed_amounts');

        $count = count($maximum_group_numbers)?:count($percentage_maximum_group_numbers); 

        for($i = 0; $i < $count; $i++):
            if($commission_type == 1){
                if($percentage_minimum_group_numbers[$i]){

                }else{
                    $entries_are_valid = FALSE;
                }
                if($percentage_maximum_group_numbers[$i]){

                }else{
                    $entries_are_valid = FALSE;
                }
                if($percentage_minimum_group_numbers[$i]<$percentage_maximum_group_numbers[$i]){

                }else{
                    $entries_are_valid = FALSE;
                }
                if($percentages[$i]){
                    if($percentages[$i]>0||$percentages<=100){

                    }else{
                        $entries_are_valid = FALSE;
                    }
                }else{
                    $entries_are_valid = FALSE;
                }
            }else if($commission_type == 2){
                if($minimum_group_numbers[$i]){

                }else{

                    $entries_are_valid = FALSE;
                }
                if($maximum_group_numbers[$i]){

                }else{

                    $entries_are_valid = FALSE;
                }
                if($minimum_group_numbers[$i]<$maximum_group_numbers[$i]){

                }else{
                    $entries_are_valid = FALSE;
                }
                if($fixed_amounts[$i]){
                    if(currency($fixed_amounts[$i])>0){

                    }else{
                        $entries_are_valid = FALSE;
                    }
                }else{
                    $entries_are_valid = FALSE;
                }

            }else{
                $entries_are_valid = FALSE;
            }
        endfor;

        if($this->form_validation->run()&&$entries_are_valid){
            $result = TRUE;
            $this->partners_m->delete_partner_commission_matrices($id);
            $this->partners_m->delete_partner_commission_type($id);
            $input = array(
                'commission_type' => $commission_type,
                'partner_id' => $id,
                'active' => 1,
                'created_by' => $this->user->id,
                'created_on' => time(),
            );
            if($this->partners_m->insert_partner_commission_type($input)){

            }else{
                $result = FALSE;
            }
            if($commission_type == 1){
                $i = 0;
                foreach ($percentages as $percentage) {
                    # code...
                    $input = array(
                        'commission_type' => $commission_type,
                        'partner_id' => $id,
                        'minimum_group_number' => $percentage_minimum_group_numbers[$i],
                        'maximum_group_number' => $percentage_maximum_group_numbers[$i],
                        'percentage' => $percentage,
                        'active' => 1,
                        'created_by' => $this->user->id,
                        'created_on' => time(),
                    );
                    if($this->partners_m->insert_partner_commission_matrix($input)){

                    }else{
                        $result = FALSE;
                    }
                    $i++;
                }
            }else if($commission_type == 2){
                $i = 0;
                foreach ($fixed_amounts as $fixed_amount) {
                    # code...
                    $input = array(
                        'commission_type' => $commission_type,
                        'partner_id' => $id,
                        'minimum_group_number' => $minimum_group_numbers[$i],
                        'maximum_group_number' => $maximum_group_numbers[$i],
                        'fixed_amount' => currency($fixed_amount),
                        'active' => 1,
                        'created_by' => $this->user->id,
                        'created_on' => time(),
                    ); 
                    if($this->partners_m->insert_partner_commission_matrix($input)){

                    }else{
                        $result = FALSE;
                    }
                    $i++;
                }
            }else{
                $result = FALSE;
            }
            if($result){
                $this->session->set_flashdata('success',"Commission matrix saved successfully.");
            }else{
                $this->session->set_flashdata('error',"Something went wrong when saving the commission matrix.");
            }
            redirect('admin/partners/listing');
        }else{
            if($entries_are_valid){

            }else{
                $this->session->set_flashdata('error','Kindly entries made, there is some data missing');
            }
        }
        $this->data['posts'] = $_POST;
        $this->data['partner_commission_matrices'] = $this->partners_m->get_partner_commission_matrices($id);
        $this->data['partner_commission_type'] = $this->partners_m->get_partner_commission_type($id);
        $this->data['post'] = $post;
        $this->data['commission_type_options'] = $this->commission_type_options;
        $this->template->title('Commission Matrix',$post->name)->build('admin/commission_matrix',$this->data);
    }

    function _is_unique_partner_name(){
        $slug = $this->input->post('slug');
        $id = $this->input->post('id');
        if(empty($slug)){
            $this->form_validation->set_message('_is_unique_partner_name','Partner\'s official name is required');
            return FALSE;
        }else{
            $partner = $this->partners_m->get_by_slug($slug,$id);
            if($partner){
                $this->form_validation->set_message('_is_unique_partner_name','Partner\'s official name already exists');
                return FALSE;
            }else{
                return TRUE;
            }
        }
    }

    function _check_partner_users(){
        $user_ids = $this->input->post('user_ids');
        $count = count($user_ids);
        if($count>0){
            return TRUE;
        }else{
            $this->form_validation->set_message('_check_partner_users', 'At least one user should be selected');
            return FALSE;
        }
    }

}