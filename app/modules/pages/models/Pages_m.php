<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Pages_m extends MY_Model {

	protected $_table = 'pages';

	function __construct(){
		$this->load->dbforge();
	}
	
}