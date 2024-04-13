<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Referrers_m extends MY_Model {

	protected $_table = 'referrers';

	function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->install();
	}

	public function install(){
		$this->db->query("
		create table if not exists referrers(
			id int not null auto_increment primary key,
			`name` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	}

	public function insert($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('referrers',$input);
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'referrers',$input);
    }

	public function count_all(){
		return $this->count_all_results('referrers');
	}

	public function get_where($params = array(),$result = TRUE){
		$this->select_all_secure('referrers');
		foreach($params as $k => $v){
			if($k == 'id'){
				$this->db->where('id',$id);
			}else{
				$this->db->where($this->dx($k)." = '".$v."'",NULL,FALSE);
			}
		}
		if($result){
			return $this->db->get('referrers')->result();
		}else{
			return $this->db->get('referrers')->row();
		}
	}
	
	public function get_all(){	
		$this->select_all_secure('referrers');
		$this->db->order_by($this->dx('name'), 'ASC', FALSE);
		return $this->db->get('referrers')->result();
	}

	public function get_all_active(){	
		$this->select_all_secure('referrers');
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		$this->db->order_by($this->dx('name'), 'ASC', FALSE);
		return $this->db->get('referrers')->result();
	}

	public function get($id = 0){	
		$this->select_all_secure('referrers');
		$this->db->where('id',$id);
		return $this->db->get('referrers')->row();
	}

	public function get_admin_referrer_options(){
		$arr = array();
		$this->select_all_secure('referrers');
		$referrers = $this->db->get('referrers')->result();
		foreach($referrers as $referrer){
			if($referrer->active){
				$status = '';
			}else{
				$status = "- ( Hidden )";
			}
			$arr[$referrer->id] = $referrer->name.' '.$status;
		}
		return $arr;
	}

	public function get_options(){
		$arr = array();
		$this->select_all_secure('referrers');
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		$referrers = $this->db->get('referrers')->result();
		foreach($referrers as $referrer){
			$arr[$referrer->id] = $referrer->name;
		}
		return $arr;
	}

}