<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_log_m extends MY_Model {

	protected $_table = 'activity_log';

	public function __construct(){
		parent::__construct();
		$this->load->dbforge();
		$this->install();
	}

	public function install()
	{
		$this->db->query("
		create table if not exists activity_log(
			id int not null auto_increment primary key,
			`user_id` blob,
			`group_id` blob,
			`member_id` blob,
			`url` blob,
			action blob,
			description blob,
			`ip_address` blob,
			`request_method` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	}

	function insert($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('activity_log',$input);
	}

	public function count_all($to='',$from='',$group_ids=array()){
		$group_id_list = '';
		if($group_ids){
			if(is_array($group_ids)){
				foreach ($group_ids as $group_id) {
					if($group_id_list){
						$group_id_list.=','.$group_id;
					}else{
						$group_id_list = $group_id;
					}
				}
			}else{
				$group_id_list = $group_ids;
			}

			$group_id_list = str_replace('=','',$group_id_list);
		}
		if($to && $from){
			$this->db->where($this->dx('created_on').' >="'.$from.'"',NULL,FALSE);
			$this->db->where($this->dx('created_on').' <="'.$to.'"',NULL,FALSE);
		}
		if($group_id_list){
			$this->db->where($this->dx('group_id').' IN('.$group_id_list.')',NULL,FALSE);
		}
		return $this->count_all_results('activity_log');
	}

	public function get_all($to='',$from='',$group_ids=array()){	
		$group_id_list = '';
		if($group_ids){
			if(is_array($group_ids)){
				foreach ($group_ids as $group_id) {
					if($group_id_list){
						$group_id_list.=','.$group_id;
					}else{
						$group_id_list = $group_id;
					}
				}
			}else{
				$group_id_list = $group_ids;
			}

			$group_id_list = str_replace('=','',$group_id_list);
		}

		$this->select_all_secure('activity_log');
		if($to && $from){
			$this->db->where($this->dx('created_on').' >="'.$from.'"',NULL,FALSE);
			$this->db->where($this->dx('created_on').' <="'.$to.'"',NULL,FALSE);
		}
		if($group_id_list){
			$this->db->where($this->dx('group_id').' IN('.$group_id_list.')',NULL,FALSE);
		}
		$this->db->order_by($this->dx('created_on'), 'DESC', FALSE);
		return $this->db->get('activity_log')->result();
	}

	public function get($id = 0){	
		$this->select_all_secure('activity_log');
		$this->db->where('id',$id);
		return $this->db->get('activity_log')->row();
	}

	function get_all_for_pricing(){
		$not_in = '408,2374,358,1,552,579'; 
		$this->select_all_secure('activity_log');
		$this->db->where('id>',19999);
		$this->db->where($this->dx('user_id').' NOT IN('.$not_in.')',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'), 'DESC', FALSE);
		return $this->db->get('activity_log')->result();
	}

}