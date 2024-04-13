<?php 
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Group extends Group_Controller{
	
    public function __construct(){
        parent::__construct(); 
    }
    
    function index(){
        $this->template->title('Group Operations')->build('group/index');
    }
}