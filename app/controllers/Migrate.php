<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Migrate extends Public_Controller{

	protected $registered_users_emails = array();
	protected $registered_users_phones = array();

	function __construct(){
		parent::__construct();
        $this->load->model('migrate_m');
    }

    function partitiondb(){
    	$this->migrate_m->partitiondb();
    }

    function translate(){
    	$str = $this->input->post('str');
    	echo translate($str);
    }

    function handle_localhost_requests(){
        
    }
}