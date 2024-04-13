<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{

    protected $data = array();

	function __construct(){
        parent::__construct();
    }

    function index(){
    	$this->data['settings_menus'] = $this->settings_menus_m->get_parent_links();
    	$this->template->set_layout('default_full_width.html')->title(translate('Group Settings'),$this->group->name)->build('group/index',$this->data);
    }

}