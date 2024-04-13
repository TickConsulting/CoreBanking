<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Themes_m extends MY_Model {

	protected $_table = 'themes';

	function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->install();
	}

	public function install(){
		$this->db->query("
		create table if not exists themes(
			id int not null auto_increment primary key,
			`name` blob,
			`logo` blob,
			`slug` blob,
			`primary_background_color` blob,
			`secondary_background_color` blob,
			`tertiary_background_color` blob,
			`primary_text_color` blob,
			`secondary_text_color` blob,
			`tertiary_text_color` blob,
			`primary_border_color` blob,
			`secondary_border_color` blob,
			`tertiary_border_color` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	}

	public function insert($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('themes',$input);
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'themes',$input);
    }

	public function count_all(){
		return $this->count_all_results('themes');
	}

	public function get_where($params = array(),$result = TRUE){
		$this->select_all_secure('themes');
		foreach($params as $k => $v){
			if($k == 'id'){
				$this->db->where('id',$id);
			}else{
				$this->db->where($this->dx($k)." = '".$v."'",NULL,FALSE);
			}
		}
		if($result){
			return $this->db->get('themes')->result();
		}else{
			return $this->db->get('themes')->row();
		}
	}
	
	public function get_all(){	
		$this->select_all_secure('themes');
		$this->db->order_by($this->dx('name'), 'ASC', FALSE);
		return $this->db->get('themes')->result();
	}

	public function get($id = 0){	
		$this->select_all_secure('themes');
		$this->db->where('id',$id);
		return $this->db->get('themes')->row();
	}


	public function get_by_slug($slug = ''){	
		$this->select_all_secure('themes');
		$this->db->where($this->dx('slug').' = "'.$slug.'"',NULL,FALSE);
		return $this->db->get('themes')->row();
	}

	public function get_default_theme(){
		$this->select_all_secure('themes');
		$this->db->where($this->dx('default_theme').' = 1',NULL,FALSE);
		return $this->db->get('themes')->row();
	}

}