<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Activity_log{

	protected $ci;

	public function __construct(){
		$this->ci= & get_instance();
		$this->ci->load->model('activity_log/activity_log_m');
	}

	public function log_action($input = array()){
		return $this->ci->activity_log_m->insert($input);
	}
	
}