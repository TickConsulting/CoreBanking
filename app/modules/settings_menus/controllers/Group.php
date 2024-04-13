<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{

	function __construct(){
        parent::__construct();
        $this->load->model('settings_menus_m');
    }

	function ajax_search_options(){
        $this->settings_menus_m->get_search_options();
    }

}