<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bank extends Bank_Controller{

    protected $data = array();

	function __construct(){
        parent::__construct();
        $this->load->model('settings_menus/settings_menus_m');
    }

    function index(){
    	$this->data['settings_menus'] = $this->settings_menus_m->get_parent_links();
    	$this->template->set_layout('default_full_width.html')->title(translate('Settings'))->build('group/index',$this->data);
    }

}