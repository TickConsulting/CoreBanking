<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Contribution_refunds_m extends MY_Model{

	protected $_table = 'contribution_refunds';

	function __construct(){
		parent::__construct();
		//$this->install();
	}

	function install(){
		$this->db->query("
			create table if not exists contribution_refunds(
				id int not null auto_increment primary key,
				`member_id` blob,
				`account_type` blob,
				`account_id` blob,
				`refund_method` blob,
				`refund_date` blob,
				`amount` blob,
				`group_id` blob,
				`contribution_id` blob,
				`description` blob,
				`active` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_by` blob,
				`modified_on` blob
			)"
		);

	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('contribution_refunds',$input);
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'contribution_refunds',$input);
    }

    function update_where($where = "",$input = array()){
        return $this->update_secure_where($where,'contribution_refunds',$input);
    }

    function get($id=0){
    	$this->select_all_secure('contribution_refunds');
    	$this->db->where('id',$id);
    	return $this->db->get('contribution_refunds')->row();
    }


    function get_group_contribution_refund($id=0){
    	$this->select_all_secure('contribution_refunds');
    	$this->db->where('id',$id);
    	$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
    	$this->db->where($this->dx('active').'="1"',NULL,FALSE);
    	return $this->db->get('contribution_refunds')->row();
    }

    function get_all()
    {
    	$this->select_all_secure('contribution_refunds');
    	$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
    	$this->db->where($this->dx('active').'="1"',NULL,FALSE);
    	$this->db->order_by($this->dx('refund_date'),'DESC',FALSE);
    	return $this->db->get('contribution_refunds')->result();
    }

    function count_active_contribution_refunds(){
    	$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
    	$this->db->where($this->dx('active').'="1"',NULL,FALSE);
    	return $this->db->count_all_results('contribution_refunds')?:0;
    }

    function update_group_back_dating_contribution_refunds_cut_off_date($group_id = 0,$input = array()){
        $where = " ".$this->dx('is_a_back_dating_record')." = '1' AND ".$this->dx('group_id')." = '".$group_id."' AND ".$this->dx('active')." = '1' ;";
        if($this->update_where($where,$input)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

}