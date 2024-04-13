<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Paybills_m extends MY_Model{

	protected $_table = 'paybills';

	function __construct(){
		parent::__construct();
		//$this->install();
	}

	function install(){
		$this->db->query("
			create table if not exists paybills(
				id int not null auto_increment primary key,
				`name` blob,
				`account_number` blob,
				`paybill_number` blob,
				`description` blob,
				`group_id` blob,
				`active` blob,
				`is_hidden` blob,
				`is_deleted` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_on` blob,
				`modified_by` blob
			)"
		);
	}

	function get($id=0){
		$this->select_all_secure('paybills');
		$this->db->where('id',$id);
		return $this->db->get('paybills')->row();
	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('paybills',$input);
	}

	function get_group_paybill_options($group_id=0){
    	$this->select_all_secure('paybills');
    	$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_deleted').' = "0"',NULL,FALSE);
		$paybill_options = $this->db->get('paybills')->result();
		$arr = array();
		foreach ($paybill_options as $paybill) {
			$arr['paybill-'.$paybill->id] = $paybill->name.' '.$paybill->paybill_number.' (Account Number: '.$paybill->account_number.')';
		}
    	return $arr;
    }
}
?>