<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mobile_m extends MY_Model{

 	public function __construct(){
        parent::__construct();
        $this ->load->dbforge();
    }

}