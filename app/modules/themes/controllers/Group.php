<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Group extends Group_Controller{

	function __construct(){
        parent::__construct();
        $this->load->model('themes_m');
    }

    function index(){
    	$data = array();
    	$data['posts'] = $this->themes_m->get_all();
        $this->template->title('Themes')->build('group/index',$data);
    }
}