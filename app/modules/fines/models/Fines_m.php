<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Fines_m extends MY_Model {

	protected $_table = 'fines';

	function __construct(){
		$this->load->dbforge();
		//$this->install();
	}


	function install(){
		$this->db->query("
		create table if not exists fines(
			id int not null auto_increment primary key,
			`group_id` blob,
			`member_id` blob,
			`fine_category_id` blob,
			`fine_date` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	}

	function insert($input,$skip_validation=FALSE){
		return $this->insert_secure_data('fines',$input);
	}

	function update($id, $input,$skip_validation = false){
		return $this->update_secure_data($id,'fines',$input);
	}

	function update_where($where = "",$input = array()){
        return $this->update_secure_where($where,'fines',$input);
    }

	function get_all(){
		$this->select_all_secure('fines');
		$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		return $this->db->get('fines')->result();
	}
	
	function get($id = 0){
		$this->select_all_secure('fines');
		$this->db->where('id',$id);
		return $this->db->get('fines')->row();
	}

	function get_group_fine($id = 0,$group_id=0){
		$this->select_all_secure('fines');
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('fines.group_id').'= "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('fines.group_id').'= "'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->get('fines')->row();
	}

	function get_group_fines($group_id=0){
		$this->select_all_secure('fines');
		$this->db->select(array('invoices.id as invoice_id'));
		if($group_id){
			$this->db->where($this->dx('fines.group_id').'= "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('fines.group_id').'= "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('fines.active').'= "1"',NULL,FALSE);
		$this->db->join('invoices',$this->dx('invoices.fine_id').' = fines.id ');
		$this->db->order_by($this->dx('fine_date'),'DESC',FALSE);
		return $this->db->get('fines')->result();
	}

	function get_group_and_member_fines(){
		$this->select_all_secure('fines');
		$this->db->select(array('invoices.id as invoice_id'));
		$this->db->where($this->dx('fines.group_id').'= "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('fines.member_id').'= "'.$this->member->id.'"',NULL,FALSE);
		$this->db->where($this->dx('fines.active').'= "1"',NULL,FALSE);
		$this->db->join('invoices',$this->dx('invoices.fine_id').' = fines.id ');
		$this->db->order_by($this->dx('fine_date'),'DESC',FALSE);
		return $this->db->get('fines')->result();
	}

	function count_group_fines($group_id = 0){
		if($group_id){
			$this->db->where($this->dx('fines.group_id').'= "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('fines.group_id').'= "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('fines.active').'= "1"',NULL,FALSE);
		$this->db->join('invoices',$this->dx('invoices.fine_id').' = fines.id ');
		return $this->db->count_all_results('fines');
	}

	function count_group_and_member_fines(){
		$this->db->where($this->dx('fines.group_id').'= "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('fines.member_id').'= "'.$this->member->id.'"',NULL,FALSE);
		$this->db->where($this->dx('fines.active').'= "1"',NULL,FALSE);
		$this->db->join('invoices',$this->dx('invoices.fine_id').' = fines.id ');
		return $this->db->count_all_results('fines');
	}

	function update_group_back_dating_fines_cut_off_date($group_id = 0,$input = array()){
        $where = " ".$this->dx('is_a_back_dating_record')." = '1' AND ".$this->dx('group_id')." = '".$group_id."' AND ".$this->dx('active')." = '1' ;";
        if($this->update_where($where,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

}
