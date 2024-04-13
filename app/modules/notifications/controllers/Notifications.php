<?php  defined('BASEPATH') OR exit('No direct script access allowed');
class Notifications extends Public_Controller{
    
    function __construct(){
        parent::__construct();
        $this->load->model('notifications_m');
    }

    function delete_old_notifications(){
    	$this->notifications_m->delete_old_notifications();
    }
}
