<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{

    protected $data = array();

	function __construct(){
        parent::__construct();
        $this->load->model('menus/menus_m');
    }
    function index(){

    }
    
    function listing($menu_key = ''){
    	//consider unnacceptable slugs
    	//empty slug redir to dashboard\
        $menu = $this->menus_m->get_link_by_language_key($menu_key);
        if($menu){
            // $menus = $this->menus_m->get_all();
            // foreach ($menus as $value) {
            //     print_r($value->icon);
            //    if(preg_match('/fa fa/', $value->icon)){
            //     $this->menus_m->update($value->id,array('icon' => str_replace('fa fa','la la',$value->icon)));
            //    }
            // }
            // die;
            $sub_menus = $this->menus_m->get_children_links($menu->id);
            $sub_menus_with_children = array();
            foreach ($sub_menus as $key => $value) {
                if($this->menus_m->has_children($value->id)){
                    $sub_menus_with_children[$value->id] = $value->id;
                }
            }
            $this->notification_counts = array(
                'UNRECONCILED_DEPOSITS_COUNT' => isset($this->unreconciled_deposits_count)?$this->unreconciled_deposits_count:'',
                'UNRECONCILED_WITHDRAWALS_COUNT' => isset($this->unreconciled_withdrawals_count)?$this->unreconciled_withdrawals_count:'',
                'WITHDRAWAL_TASKS_COUNT' => isset($this->withdrawal_tasks_count)?$this->withdrawal_tasks_count:'',
                'ACTIVE_LOAN_APPLICATIONS' => isset($this->active_loan_applications)?$this->active_loan_applications:'',
                'PENDING_WITHDRAWAL_APPROVAL_REQUESTS_COUNT' => isset($this->pending_withdrawal_approval_requests_count)?$this->pending_withdrawal_approval_requests_count:'',
            );
            $this->data['notification_counts'] = $this->notification_counts;
            // print_r($this->data['notification_counts']); die;
            $this->data['sub_menus'] = $sub_menus;
            $this->data['menu_key'] = $menu_key;
            $this->data['sub_menus_with_children'] = $sub_menus_with_children;
            $this->template->set_layout('menus.html')->title($menu->name)->build('group/listing',$this->data);
        }else{
            print_r('Menu not found');
        }
    	
    }


}