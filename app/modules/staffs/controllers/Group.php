<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{
   
	function __construct(){
        parent::__construct();
        $this->load->model('staffs_m');
    }
}