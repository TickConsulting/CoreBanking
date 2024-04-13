<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller{
    protected $data=array();

    protected $validation_rules = array(
        array(
            'field' => 'name',
            'label' => 'Coupon Name',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'type',
            'label' => 'Coupon type',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'percentage_value',
            'label' => 'Percentage Coupon Value',
            'rules' => 'trim'
        ),
        array(
            'field' => 'fixed_amount',
            'label' => 'Fixed Amount',
            'rules' => 'trim|currency'
        ),
        array(
            'field' => 'coupon_waiver_type',
            'label' => 'Coupon Waiver Type',
            'rules' => 'trim|numeric'
        ),
        array(
            'field' => 'partial_waiver_period',
            'label' => 'Partial Waiver Period',
            'rules' => 'trim|numeric'
        ),
        array(
            'field' => 'partial_waiver_start_date',
            'label' => 'Partial Waiver Start Date',
            'rules' => 'trim|date'
        ),
        array(
            'field' => 'date_active_from',
            'label' => 'Distibution Date Active From',
            'rules' => 'trim|date|required'
        ),
        array(
            'field' => 'expiry_date',
            'label' => 'Distibution Expiry Date',
            'rules' => 'trim|date|required'
        ),

        array(
            'field' => 'distribution_limit',
            'label' => 'Distibution Usage limit',
            'rules' => 'trim|numeric|required'
        ),
        array(
            'field' => 'limited_users',
            'label' => 'Distibution User Limit',
            'rules' => 'trim|numeric'
        ),
        array(
            'field' => 'free_months',
            'label' => 'Months free subscription',
            'rules' => 'trim|numeric'
        ),
    );

    protected $coupon_types = array(
        1 => 'Fixed amount off subscription',
        2 => 'Percentage off subscription',
        3 => 'Waive unpaid subscription(s)',
        4 => 'Free months of subscription(s)',
    );

    protected $coupon_waiver_types = array(
        1 => 'Fully waiver',
        2 => 'Partial Waiver',
    );

	function __construct(){
        parent::__construct();
        $this->load->model('coupons_m');
        $this->data['coupon_types'] = $this->coupon_types;
        $this->data['coupon_waiver_types'] = $this->coupon_waiver_types;
    }

    function _additional_rules(){
        $type = $this->input->post('type');
        if($type == 1){
            $this->validation_rules[] = array(
                'field' => 'fixed_amount',
                'label' => 'Fixed Amount',
                'rules' => 'trim|currency|required'
            );
        }else if($type == 2){
            $this->validation_rules[] = array(
                'field' => 'percentage_value',
                'label' => 'Percentage coupon',
                'rules' => 'trim|currency|required'
            );
        }else if($type == 3){
            $this->validation_rules[] = array(
                'field' => 'coupon_waiver_type',
                'label' => 'Coupon Waiver Type',
                'rules' => 'trim|numeric|required'
            );

            $coupon_waiver_type = $this->input->post('coupon_waiver_type');
            if($coupon_waiver_type == 2){
                $this->validation_rules[] = array(
                    'field' => 'partial_waiver_period',
                    'label' => 'Partial Waiver Period',
                    'rules' => 'trim|numeric|required'
                );
                $this->validation_rules[] = array(
                    'field' => 'partial_waiver_start_date',
                    'label' => 'Partial Waiver Period Start Date',
                    'rules' => 'trim|date|required'
                );
            }
        }elseif ($type == 4) {
            $this->validation_rules[] = array(
                'field' => 'free_months',
                'label' => 'Months free subscription',
                'rules' => 'trim|numeric|required'
            );
        }

        $distribution_limit = $this->input->post('distribution_limit');
        if($distribution_limit==2){
            $this->validation_rules[] = array(
                'field' => 'limited_users',
                'label' => 'Users for limited distribution',
                'rules' => 'trim|numeric|required'
            );
        }

    }

    function create(){
        $post = new StdClass();
        $this->_additional_rules();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $input = array(
                'type' => $this->input->post('type'),
                'name' => $this->input->post('name'),
                'coupon' => strtoupper(random_string('alnum', 8)),
                'percentage_value' => $this->input->post('percentage_value'),
                'fixed_amount' => $this->input->post('fixed_amount'),
                'coupon_waiver_type' => $this->input->post('coupon_waiver_type'),
                'partial_waiver_period' => $this->input->post('partial_waiver_period'),
                'partial_waiver_start_date' => $this->input->post('partial_waiver_start_date'),
                'expiry_date' => $this->input->post('expiry_date'),
                'date_active_from' => $this->input->post('date_active_from'),
                'distribution_limit' => $this->input->post('distribution_limit'),
                'limited_users' => $this->input->post('limited_users'),
                'free_months' => $this->input->post('free_months'),
                'active' => 1,
                'created_by' => $this->user->id,
                'created_on' => time(),
            );
            if($this->coupons_m->insert($input)){
                $this->session->set_flashdata('success','Coupon successfully created');
                if($this->input->post('new_item')){
                    redirect('admin/coupons/create');
                    return FALSE;
                }
            }else{
                $this->session->set_flashdata('error','Could not create the requested coupon. Try again later');
            }
            redirect('admin/coupons/listing');
        }else{
            foreach ($this->validation_rules as $key => $field){
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $this->data['id'] = '';
        $this->data['post'] = $post;
        $this->template->title('Create Reward Coupon')->build('admin/form',$this->data);
    }

    function edit($id = 0){
        $id OR redirect('admin/coupons/listing');
        $post = $this->coupons_m->get($id);
        if(empty($post)){
            $this->session->set_flashdata('error','We could not find the coupon you trying to edit');
            redirect('admin/coupons/listing');
        }
        $this->_additional_rules();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $update = array(
                'type' => $this->input->post('type'),
                'name' => $this->input->post('name'),
                'percentage_value' => $this->input->post('percentage_value'),
                'fixed_amount' => $this->input->post('fixed_amount'),
                'coupon_waiver_type' => $this->input->post('coupon_waiver_type'),
                'partial_waiver_period' => $this->input->post('partial_waiver_period'),
                'partial_waiver_start_date' => $this->input->post('partial_waiver_start_date'),
                'expiry_date' => $this->input->post('expiry_date'),
                'date_active_from' => $this->input->post('date_active_from'),
                'distribution_limit' => $this->input->post('distribution_limit'),
                'limited_users' => $this->input->post('limited_users'),
                'free_months' => $this->input->post('free_months'),
                'modified_by' => $this->user->id,
                'modified_on' => time(),
            );
            if($this->coupons_m->update($post->id,$update)){
                $this->session->set_flashdata('success','Coupon successfully updated');
                if($this->input->post('new_item')){
                    redirect('admin/coupons/create');
                    return FALSE;
                }
            }else{
                $this->session->set_flashdata('error','Could not update the requested coupon. Try again later');
            }
            redirect('admin/coupons/listing');
        }else{
            foreach (array_keys($this->validation_rules) as $field){
                if(isset($_POST[$field])){
                    $post->$field = $this->form_validation->$field;
                }
            }
        }
        $this->data['id'] = $id;
        $this->data['post'] = $post;
        $this->template->title('Create Reward Coupon')->build('admin/form',$this->data);
    }

    function listing(){
        $total_rows = $this->coupons_m->count_all();
        $pagination = create_pagination('admin/coupons/listing/pages', $total_rows,50,5,TRUE);
        $posts = $this->coupons_m->limit($pagination['limit'])->get_all();
        $data['pagination'] = $pagination;
        $this->data['posts'] = $posts;
        $this->template->title('Coupons')->build('admin/listing',$this->data);
    }

    function action(){
        $ids = $this->input->post('action_to');
        $btnAction = $this->input->post('btnAction');
        if($btnAction == 'bulk_delete'){
            foreach ($ids as $id) {
                $this->delete($id,FALSE);
            }
        }
        redirect('admin/coupons/listing');
    }

    function delete($id=0,$redirect=TRUE){
        if($id){
            if($post = $this->coupons_m->get($id)){
                if($this->coupons_m->delete($post->id)){
                    $this->session->set_flashdata('success','Coupon successfully deleted');
                    if($redirect){
                        redirect('admin/coupons/listing');
                    }  
                    return TRUE;
                }else{
                    $this->session->set_flashdata('error','Could not delete the selected coupon');
                    if($redirect){
                        redirect('admin/coupons/listing');
                    }            
                    return FALSE;
                }
            }else{
                $this->session->set_flashdata('error','Coupon selected does not exist');
                if($redirect){
                    redirect('admin/coupons/listing');
                }            
                return FALSE;
            }
        }else{
            $this->session->set_flashdata('error','Coupon selected does not exist');
            if($redirect){
                redirect('admin/coupons/listing');
            }            
            return FALSE;
        }
    }

}