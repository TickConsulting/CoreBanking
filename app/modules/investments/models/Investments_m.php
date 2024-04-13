<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Investments_m extends MY_Model {

	protected $_table = 'investments';

	function __construct(){
		$this->load->dbforge();
	}

}