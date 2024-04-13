<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Recipients_m extends MY_Model {

	protected $_table = 'recipients';

	function __construct(){
		$this->load->dbforge();
		//$this->install();
	}

	function install(){
		$this->db->query("
			create table if not exists recipients(
				id int not null auto_increment primary key,
				`name` BLOB,
				`phone_number` BLOB,
				`bank_id` BLOB,
				`description` BLOB,
				`account_number` BLOB,
				`paybill_number` BLOB,
				`account_name` BLOB,
				`group_id` BLOB,
				`type` BLOB,
				`active` BLOB,
				`is_hidden` BLOB,
				`created_by` BLOB,
				`created_on` BLOB,
				`modified_on` BLOB,
				`modified_by` BLOB
			)"
		);
	}

	/* types 
		1 mobile number
		2 paybill
		3 bank account */

	function insert($input,$skip_validation=FALSE){
		return $this->insert_secure_data('recipients',$input);
	}

	function get_all(){
		$this->select_all_secure('recipients');
		$this->db->order_by($this->dx('name'), 'DESC',FALSE);
		return $this->db->get('recipients')->result();
	}

	function get_group_mobile_money_account_recipient_options($group_id = 0){
		$this->select_all_secure('recipients');
		$this->db->where($this->dx('type').' = 1',NULL,FALSE);
		
		$recipients = $this->db->get('recipients')->result();
		$arr = array();
    	$member_options = $this->members_m->get_active_group_member_recipient_options();
		foreach($recipients as $recipient){
			$arr['Mobile Money Recipients']['mobile-'.$recipient->id] = $recipient->name.' ('.$recipient->phone_number.')';
		}
		$arr['Members'] = $member_options;
		return $arr;
	}

	function get_group_bank_account_recipient_options($group_id = 0){
		$this->select_all_secure('recipients');
		$this->db->where($this->dx('type').' = 3',NULL,FALSE);
		
		$recipients = $this->db->get('recipients')->result();
		$arr = array();
		foreach($recipients as $recipient){
			$arr['Bank Recipients']['bank-'.$recipient->id] = $recipient->name.' ('.$recipient->account_name.' - '.$recipient->account_number.')';
		}
		return $arr;
	}


	function get_group_paybill_account_recipient_options($group_id = 0){
		$this->select_all_secure('recipients');
		$this->db->where($this->dx('type').' = 2',NULL,FALSE);
		
		$recipients = $this->db->get('recipients')->result();
		$arr = array();
		foreach($recipients as $recipient){
			$arr['Paybill Recipients']['paybill-'.$recipient->id] = $recipient->name.' ('.$recipient->account_name.' - '.$recipient->account_number.')';
		}
		return $arr;
	}

	function get_group_recipients(){
		$this->select_all_secure('recipients');
		$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		$this->db->order_by($this->dx('name'), 'DESC',FALSE);
		return $this->db->get('recipients')->result();
	}

	function count_group_recipients(){
		$this->db->where($this->dx('group_id').'='.$this->group->id,NULL,FALSE);
		return $this->db->count_all_results('recipients')?:0;
	}
	
	function get($id = 0){
		$this->select_all_secure('recipients');
		$this->db->where('id',$id);
		return $this->db->get('recipients')->row();
	}

	function get_group_recipient($id = 0,$group_id = 0){
		$this->select_all_secure('recipients');
		$this->db->where('id',$id);
		
		$this->db->limit(1);
		return $this->db->get('recipients')->row();
	}

	function get_group_recipient_options($group_id = 0,$show_other_details = FALSE){
		$arr = array();
		$this->select_all_secure('recipients');
		
		$recipients = $this->db->get('recipients')->result();
		$options = array(
			'Mobile Money Recipients' => array(),
			'Bank Recipients' => array(),
			'Paybill Recipients' => array(),
		);
    	$member_options = $this->members_m->get_active_group_member_recipient_options($group_id);
		foreach($recipients as $recipient){
			if($recipient->type == 1){ //mobile number
				if($show_other_details){
					$options['Mobile Money Recipients']['mobile-'.$recipient->id] = $recipient->name.' ('.$recipient->phone_number.')';
				}else{
					$options['Mobile Money Recipients']['mobile-'.$recipient->id] = $recipient->name;

				}
			}elseif($recipient->type == 2){ //paybill
				if($show_other_details){
					$options['Paybill Recipients']['paybill-'.$recipient->id] = $recipient->name.' ('.$recipient->paybill_number.' - '.$recipient->account_number.')';
				}else{
					$options['Paybill Recipients']['paybill-'.$recipient->id] = $recipient->name;
				}
			}elseif($recipient->type == 3){ //bank account
				if($show_other_details){
					$options['Bank Recipients']['bank-'.$recipient->id] = $recipient->name.' ('.$recipient->account_name.' - '.$recipient->account_number.')';
				}else{
					$options['Bank Recipients']['bank-'.$recipient->id] = $recipient->name;
				}
			}
		}
		foreach ($options as $key => $value) {
			if(empty($value)){
				//skip
			}else{
				$arr[$key] = $value;
			}
		}
		if(empty($member_options)){

		}else{
			$arr['Members'] = $member_options;
		}
		return $arr;
	}

	function count_all($params = array()){
		return $this->db->count_all_results('recipients');
	}

	function update($id, $input,$skip_validation = false){
		return $this->update_secure_data($id,'recipients',$input);
	}

	function get_group_back_dating_recipient(){
		$this->select_all_secure('recipients');
		$this->db->where($this->dx('group_id')." = '".$this->group->id."' ",NULL,FALSE);
		$this->db->where($this->dx('is_system')." = '1' ",NULL,FALSE);
		$this->db->where($this->dx('active')." = '0' ",NULL,FALSE);
		$this->db->limit(1);
		$recipient = $this->db->get('recipients')->row();
		if($recipient){
			return $recipient;
		}else{
			$input = array(
				'is_system' => 1,
				'active' => 0,
				'group_id' => $this->group->id,
				'name' => 'Back-dating Recipient',
				'created_on' => time(),
				'created_by' => $this->user->id
			);
			if($id = $this->insert($input)){
				if($recipient = $this->get_group_recipient($id)){
					return $recipient;
				}else{
					return FALSE;
				}
			}else{
				return FALSE;
			}
		}
	}

	function is_phone_number_unique($name='',$group_id=0){
		$this->db->select('id');
		$this->db->where($this->dx('phone_number').' ="'.$phone_number.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." = '".$this->group->id."' ",NULL,FALSE);
		}
		return $this->db->get('recipients')->row();
	}

	// function get_group_recipient_recipient_options($group_id=0){
	// 	$arr = array();
	// 	$this->db->select(
	// 		array(
	// 			'id',
	// 			$this->dx('name')." as name ",
	// 			$this->dx('phone_number')." as phone_number ",
	// 		)
	// 	);
	// 	$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
	// 	if($group_id){
	// 		$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
	// 	}else{
	// 		$this->db->where($this->dx('group_id')." = '".$this->group->id."' ",NULL,FALSE);
	// 	}
	// 	$recipients = $this->db->get('recipients')->result();
	// 	foreach($recipients as $recipient):
	// 		/* types 
	// 			1 mobile number
	// 			2 paybill
	// 			3 bank account */
	// 		if($recipient->type == 1){
	// 			$arr['mobile-'.$recipient->id] = $recipient->name." - ".$recipient->phone_number;
	// 		}else if($recipient->type == 2){
	// 			$arr['paybill-'.$recipient->id] = $recipient->name." - ".$recipient->phone_number;
	// 		}else if($recipient->type == 3){
	// 			$arr['bank-'.$recipient->id] = $recipient->name." - ".$recipient->phone_number;
	// 		}
	// 	endforeach;
	// 	return $arr;
	// }


	function get_recipient_by_account_number($account_number=0,$type=0,$user_id=0){
		$this->select_all_secure('recipients');
		$this->db->where($this->dx('account_number').' = "'.$account_number.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' = "'.$type.'"',NULL,FALSE);
		$this->db->where($this->dx('group_id').' = "0"',NULL,FALSE);
		$this->db->where($this->dx('created_by').' = "'.$user_id.'"',NULL,FALSE);
		return $this->db->get('recipients')->row();
	}

}
