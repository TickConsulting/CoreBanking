<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Depositors_m extends MY_Model {

	protected $_table = 'depositors';

	function __construct(){
		$this->load->dbforge();
		//$this->install();
	}

	function install(){
		$this->db->query("
		create table if not exists depositors(
			id int not null auto_increment primary key,
			`name` BLOB,
			`email` BLOB,
			`phone` BLOB,
			`description` BLOB,
			`group_id` BLOB,
			`active` BLOB,
			`is_hidden` BLOB,
			created_by BLOB,
			created_on BLOB,
			modified_on BLOB,
			modified_by BLOB
		)");
	}

	function insert($input,$skip_validation=FALSE){
		return $this->insert_secure_data('depositors',$input);
	}

	function get_all(){
		$this->select_all_secure('depositors');
		$this->db->order_by($this->dx('name'), 'DESC',FALSE);
		return $this->db->get('depositors')->result();
	}

	function get_all_group_depositors($group_id=array()){
		$this->select_all_secure('depositors');
		if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		$this->db->order_by($this->dx('name'), 'DESC',FALSE);
		return $this->db->get('depositors')->result();
	}

	function count_all_group_depositors(){
		$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		return $this->db->count_all_results('depositors')?:0;
	}
	
	function get($id){
		$this->select_all_secure('depositors');
		$this->db->where('id',$id);
		return $this->db->get('depositors')->row();
	}

	function get_group_depositor($id = 0,$group_id = 0){
		$this->select_all_secure('depositors');
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		return $this->db->get('depositors')->row();
	}

	function get_group_depositor_options($group_id = 0){
		$arr = array();
		$this->db->select(array(
			'id',
			$this->dx('name').' as name',
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').'='.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		}
		$depositors = $this->db->get('depositors')->result();
		foreach($depositors as $depositor){
			$arr[$depositor->id] = $depositor->name;
		}
		return $arr;
	}

	function count_all($params = array()){
		return $this->db->count_all_results('depositors');
	}

	function update($id, $input,$skip_validation = false){
		return $this->update_secure_data($id,'depositors',$input);
	}

	function get_group_back_dating_depositor(){
		$this->select_all_secure('depositors');
		$this->db->where($this->dx('group_id')." = '".$this->group->id."' ",NULL,FALSE);
		$this->db->where($this->dx('is_system')." = '1' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '0' ",NULL,FALSE);
		$this->db->limit(1);
		$depositor = $this->db->get('depositors')->row();
		if($depositor){
			return $depositor;
		}else{
			$input = array(
				'is_system' => 1,
				'active' => 0,
				'group_id' => $this->group->id,
				'name' => 'Back-dating Depositor',
				'created_on' => time(),
				'created_by' => $this->user->id
			);
			if($id = $this->insert($input)){
				if($depositor = $this->get_group_depositor($id)){
					return $depositor;
				}else{
					return FALSE;
				}
			}else{
				return FALSE;
			}
		}
	}

	function is_name_unique($name='',$group_id=0){
		$this->db->select('id');
		$this->db->where($this->dx('name').' ="'.$name.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." = '".$this->group->id."' ",NULL,FALSE);
		}
		return $this->db->get('depositors')->row();
	}

}
