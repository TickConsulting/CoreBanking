<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Mobile_money_providers_m extends MY_Model {

	protected $_table = 'mobile_money_providers';

	function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->install();
	}

	public function install()
	{
		$this->db->query("
		create table if not exists mobile_money_providers(
			id int not null auto_increment primary key,
			`name` blob,
			`slug` blob,
			`partner` blob,
			`logo` blob,
			`primary_color` blob,
			`secondary_color` blob,
			`tertiary_color` blob,
			`text_color` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	}

	public function insert($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('mobile_money_providers',$input);
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'mobile_money_providers',$input);
    }

	public function count_all(){
		return $this->count_all_results('mobile_money_providers');
	}
	
	public function get_all(){	
		$this->select_all_secure('mobile_money_providers');
		$this->db->order_by($this->dx('name'), 'ASC', FALSE);
		return $this->db->get('mobile_money_providers')->result();
	}

	public function get($id = 0){	
		$this->select_all_secure('mobile_money_providers');
		$this->db->where('id',$id);
		return $this->db->get('mobile_money_providers')->row();
	}

	public function get_by_slug($slug = ''){	
		$this->select_all_secure('mobile_money_providers');
		$this->db->where($this->dx('slug').' = "'.$slug.'"',NULL,FALSE);
		return $this->db->get('mobile_money_providers')->row();
	}

	public function get_id_by_slug($slug = ''){	
		$this->db->select('id as id');
		$this->db->where($this->dx('slug').' = "'.$slug.'"',NULL,FALSE);
		$res = $this->db->get('mobile_money_providers')->row();
		if($res){
			return $res->id;
		}
		return NULL;
	}
	

	public function get_admin_mobile_money_provider_options(){
		$arr = array();
		$this->select_all_secure('mobile_money_providers');
		$mobile_money_providers = $this->db->get('mobile_money_providers')->result();
		foreach($mobile_money_providers as $mobile_money_provider){
			if($mobile_money_provider->active){
				$status = '';
			}else{
				$status = "- ( Hidden )";
			}
			$arr[$mobile_money_provider->id] = $mobile_money_provider->name.' '.$status;
		}
		return $arr;
	}

	public function get_group_mobile_money_provider_options($country_id=0){
		$arr = array();
		$this->select_all_secure('mobile_money_providers');
		if($country_id){
			$this->db->where($this->dx('country_id').' = "'.$country_id.'"',NULL,FALSE);
		}
		$mobile_money_providers = $this->db->get('mobile_money_providers')->result();
		foreach($mobile_money_providers as $mobile_money_provider){
			$arr[$mobile_money_provider->id] = $mobile_money_provider->name;
		}
		return $arr;
	}
}