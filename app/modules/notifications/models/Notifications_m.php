<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications_m extends MY_Model {

protected $_table = 'notifications';

	function __construct(){
		$this->load->dbforge();
		//$this->install();
	}

	public function install(){
		$this->db->query("
		create table if not exists notifications(
			id int not null auto_increment primary key,
		 	`from_member_id` blob,
		  	`from_user_id` blob,
		  	`to_user_id` blob,
		  	`to_member_id` blob,
		  	`subject` blob,
		  	`message` blob,
		  	`group_id` blob,
		  	`is_read` blob,
		  	`active` blob,
		  	`created_by` blob,
		  	`created_on` blob,
		  	`modified_by` blob,
			modified_on blob
		)");
	}

	public function insert($input = array(),$key=FALSE){
		return $this->insert_secure_data('notifications', $input);
	}

	public function insert_batch($input = array(),$key=FALSE){
		return $this->insert_chunked_batch_secure_data('notifications', $input);
	}

	function get($id=0,$group_id=0){
		$this->select_all_secure('notifications');
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		return $this->db->get('notifications')->row();
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'notifications',$input);
    }

	public function get_unread_member_notifications_array(){
		return array();
		$arr = array();
		$this->db->select(
			array(
				'id',
				$this->dx('to_member_id').' as member_id ',
				$this->dx('call_to_action_link').' as url',
			)
		);
		$this->db->where($this->dx('group_id').' = '.$this->group->id,NULL,FALSE);
		$this->db->where($this->dx('to_member_id').' = '.$this->member->id,NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('is_read').' = "0" ',NULL,FALSE);
		$notifications = $this->db->get('notifications')->result();
		foreach($notifications as $notification){
			$arr[trim($notification->url)][$notification->member_id] = $notification->id;
		}
		return $arr;
	}


	public function get_unread_member_notifications($limit = 0){		
		return array(); 
		$this->select_all_secure('notifications');
		$this->db->where($this->dx('group_id').' = '.$this->group->id,NULL,FALSE);
		$this->db->where($this->dx('to_member_id').' = '.$this->member->id,NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('is_read').' = "0" ',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		if($limit){
			$this->db->limit($limit);
		}
		return $this->db->get('notifications')->result();
	}

	public function get_member_notifications($filter_parameters = array()){
		$this->select_all_secure('notifications');
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('created_on').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('created_on').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['is_read']) && isset($filter_parameters['is_read'])){
			if($filter_parameters['is_read']==0){
				$this->db->where($this->dx('is_read').' = "0" ',NULL,FALSE);
			}else if($filter_parameters['is_read']==1){
				$this->db->where($this->dx('is_read').' = "1" ',NULL,FALSE);
			}
		}
		$this->db->where($this->dx('group_id').' = '.$this->group->id,NULL,FALSE);
		$this->db->where($this->dx('to_member_id').' = '.$this->member->id,NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('notifications')->result();
	}

	public function get_member_notification(){
		$this->select_all_secure('notifications');
		$this->db->where($this->dx('group_id').' = '.$this->group->id,NULL,FALSE);
		$this->db->where($this->dx('to_member_id').' = '.$this->member->id,NULL,FALSE);
		return $this->db->get('notifications')->row();
	}

	public function count_unread_member_notifications($group_id=0,$member_id=0){
		return;
		if($group_id){
			$this->db->where($this->dx('group_id').' = '.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = '.$this->group->id,NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('to_member_id').' = '.$member_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('to_member_id').' = '.$this->member->id,NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('is_read').' = "0" ',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->count_all_results('notifications');
	}

	public function count_group_membership_notifications($group_id=0,$member_id=0){
		if($group_id){
			$this->db->where($this->dx('group_id').' = '.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = '.$this->group->id,NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('to_member_id').' = '.$member_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('to_member_id').' = '.$this->member->id,NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('is_read').' = "0" ',NULL,FALSE);
		$this->db->where($this->dx('subject').' = "Group Membership Request" ',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->count_all_results('notifications');
	}

	public function count_member_notifications($filter_parameters = array(),$group_id=0,$member_id=0){
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('created_on').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('created_on').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['is_read']) && isset($filter_parameters['is_read'])){
			if($filter_parameters['is_read']==0){
				$this->db->where($this->dx('is_read').' = "0" ',NULL,FALSE);
			}else if($filter_parameters['is_read']==1){
				$this->db->where($this->dx('is_read').' = "1" ',NULL,FALSE);
			}
		}
		if($group_id){
			$this->db->where($this->dx('group_id').' = '.$group_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = '.$this->group->id,NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('to_member_id').' = '.$member_id,NULL,FALSE);
		}else{
			$this->db->where($this->dx('to_member_id').' = '.$this->member->id,NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->count_all_results('notifications');
	}

	public function mark_all_group_notifications_as_read($group_id = 0){
		$this->db->select(array('id'));
		$this->db->where($this->dx('group_id').' = '.$group_id,NULL,FALSE);
		$notifications = $this->db->get('notifications')->result();
		foreach($notifications as $notification){
			$input = array(
				'is_read'=>1,
				'modified_on'=>time(),
			); 
			if($this->update($notification->id,$input)){
				return TRUE;
			}else{
				return FALSE;
			}
		}

	}

	function mark_all_member_notifications_as_read($member_id=0,$group_id=0){
		return $this -> db -> query("update notifications set 
                is_read=".$this->exa('1').",
                modified_on = ".$this->exa(time())." 
                where ".$this->dx("to_member_id")." ='".$member_id."' and ".$this->dx("group_id")." = '".$group_id."'");  
	}

	function mark_all_member_notifications_as_unread($member_id=0,$group_id=0){
		return $this -> db -> query("update notifications set 
                is_read=".$this->exa('0').",
                modified_on = ".$this->exa(time())." 
                where ".$this->dx("to_member_id")." ='".$member_id."' and ".$this->dx("group_id")." = '".$group_id."'");  
	}

	function delete_old_notifications(){
		$this->db->where($this->dx('created_on')." < '".strtotime('-1 month')."' ",NULL,FALSE);
		return $this->db->delete('notifications');
	}

}