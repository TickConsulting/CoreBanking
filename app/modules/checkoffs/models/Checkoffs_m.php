<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');

class Checkoffs_m extends MY_Model{

	protected $_table = 'checkoffs';

	function __construct(){
		parent::__construct();
		$this->install();
		$this->load->model('contributions/contributions_m');
		$this->load->model('fine_categories/fine_categories_m');
	}

	function install(){

		$this->db->query("
			create table if not exists checkoffs(
				id int not null auto_increment primary key,
				`checkoff_date` blob,
				`account_id` blob,
				`amount` blob,
				`active` blob,
				`group_id` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_by` blob,
				`modified_on` blob
			)"
		);
	}

	
	function get_all(){	
		$this->select_all_secure('checkoffs');
		return $this->db->get('checkoffs')->result();
	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('checkoffs',$input);
	}

	function update($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'checkoffs',$input);
	}

	function get_group_checkoff_amounts_array_by_checkoff_id($id = 0){
		$arr = array();
		$this->db->select(
			array(
				$this->dx('contribution_id').' as contribution_id ',
				$this->dx('member_id').' as member_id ',
				$this->dx('amount').' as amount ',
			)
		);
		$this->db->where($this->dx('checkoff_id').' = "'.$id.'"',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$deposits = $this->db->get('deposits')->result();
		foreach($deposits as $deposit):
			$arr[$deposit->contribution_id][$deposit->member_id] = $deposit->amount;
		endforeach;
		return $arr;
	}

	function get_group_deposits_by_checkoff_id($checkoff_id = 0,$group_id = 0){
		$this->select_all_secure('deposits');
		$this->db->where($this->dx('checkoff_id').' = "'.$checkoff_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->order_by($this->dx('deposit_date'),'DESC',FALSE);
		return $this->db->get('deposits')->result();
	}

	function get_group_checkoffs(){
		$this->select_all_secure('checkoffs');
		$this->db->where($this->dx('active').' = 1',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->order_by($this->dx('checkoff_date'),'DESC',FALSE);
		return $this->db->get('checkoffs')->result();
	}

	function count_group_checkoffs(){
		$this->db->where($this->dx('active').' = 1',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		return $this->db->count_all_results('checkoffs');
	}

	function get_group_checkoff($id = 0){
		$this->select_all_secure('checkoffs');
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where('id',$id);
		$this->db->limit(1);
		return $this->db->get('checkoffs')->row();
	}

	function get_group_checkoff_monthly_summary($group_id=0,$from=0,$to=0){
		$arr = array();
		$this->db->select(
			array(
				'SUM('.$this->dx('amount').') as total_amount',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('checkoff_date')." ),'%Y%m') as year ",
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($from){
			$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('checkoff_date')."),'%Y%m') >= '" . date('Ym',$from) . "'", NULL, FALSE);
		}
		if($to){
			$this->db->where($this->dx('checkoff_date').' <="'.$to.'"',NULL,FALSE);
		}
		$this->db->group_by(
            array(
                'year',
            )
        );
		$checkoffs = $this->db->get('checkoffs')->result();
		if($checkoffs){
			foreach ($checkoffs as $checkoff) {
				$arr[$checkoff->year] = $checkoff->total_amount;
			}
		}

		return $arr;
	}

}