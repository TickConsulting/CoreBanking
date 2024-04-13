<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
Class Staffs_m extends MY_Model{

	protected $_table = 'staffs';

	function __construct(){
		parent::__construct();

		//$this->install();
	}

	function install(){
	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE)
	{
		return $this->insert_secure_data('staffs',$input);
	}

	function update($id,$input=array(),$SKIP_VALIDATION=FALSE)
	{
		return $this->update_secure_data($id,'staffs',$input);
	}

	function get($id=0){
		$this->select_all_secure('staffs');
		$this->db->where('id',$id);
		return $this->db->get('staffs')->row();
	}


	function get_options(){
		$this->select_all_secure('staffs');
		$this->db->select(array(
				$this->dx('users.first_name').' as first_name',
				$this->dx('users.last_name').' as last_name',
			));
		$this->db->where($this->dx('staffs.is_deleted').' IS NULL ',NULL,FALSE);
		$this->db->join('users','users.id ='.$this->dx('staffs.user_id'),'INNER');
        $this->db->order_by($this->dx('first_name'), 'ASC', FALSE);
		$staffs = $this->db->get('staffs')->result();
		$arr = array();
		if($staffs){
			foreach ($staffs as $key => $staff) 
			{
				$arr[$staff->id] = $staff->first_name.' '.$staff->last_name;
			}
		}
		return $arr;
	}

}